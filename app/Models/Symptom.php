<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    protected $fillable = ['name', 'description', 'risk_level'];

    /**
     * A symptom can belong to many reports.
     */
    // app/Models/Symptom.php
    public function reports()
    {
        return $this->belongsToMany(Report::class, 'report_symptom', 'symptom_id', 'report_id');
    }

}
