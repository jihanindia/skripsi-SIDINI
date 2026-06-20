<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with(['patient', 'result', 'user'])
            ->forCurrentUser()
            ->orderByDesc('assessment_date')
            ->orderByDesc('created_at')
            ->get();

        $showPuskesmasColumn = auth()->user()->isDinas();

        $metadata = [];
        if (file_exists(storage_path('app/knn_metadata.json'))) {
            $metadata = json_decode(file_get_contents(storage_path('app/knn_metadata.json')), true) ?? [];
        }

        return view('patients.index', compact('assessments', 'showPuskesmasColumn', 'metadata'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medical_record_number' => 'required|unique:patients',
            'name' => 'required',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|integer',
            'blood_type' => 'nullable',
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);

        Patient::create($validated);

        return redirect()->route('patients.index')->with('success', 'Pasien berhasil ditambahkan!');
    }
}
