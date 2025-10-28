<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'user_id',
        'report_id',
        'pig_health_status',
        'symptoms_description',
        'num_mortality',
        'symptom_onset_date',
        'mortality_date',
        'affected_pig_count',
        'report_status',
        'risk_level',
        'location_name',
        'barangay',
        'city',
        'province',
        'latitude',
        'longitude',
        'is_read_by_staff',
    ];

    /**
     * Relationships
     */

    // Each report belongs to one user (the farmer)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A report can have many uploaded pig images
    public function images()
    {
        return $this->hasMany(PigImage::class);
    }

    // A report can have many notes (from staff or vets)
    public function notes()
    {
        return $this->hasMany(CaseNote::class);
    }

    // A report can have many symptoms (many-to-many)
    // app/Models/Report.php
    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class, 'report_symptom', 'report_id', 'symptom_id')->withTimestamps();
    }


    public function vetAssessments()
    {
        return $this->hasMany(VetAssessment::class);
    }




}
