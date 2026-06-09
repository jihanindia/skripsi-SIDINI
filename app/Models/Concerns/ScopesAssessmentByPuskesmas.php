<?php

namespace App\Models\Concerns;

trait ScopesAssessmentByPuskesmas
{
    public function scopeForCurrentUser($query)
    {
        $user = auth()->user();

        if ($user && $user->isPuskesmas() && $user->puskesmas) {
            $query->where($this->getTable() . '.puskesmas', $user->puskesmas);
        }

        return $query;
    }
}
