<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::withCount('assessments')->get();
        return view('patients.index', compact('patients'));
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
