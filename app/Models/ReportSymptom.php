<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSymptom extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'report_symptom';

    // Allow mass assignment of these fields
    protected $fillable = [
        'report_id',
        'symptom_id',
        'created_at',
        'updated_at',
    ];

    /**
     * A ReportSymptom belongs to a Report.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * A ReportSymptom belongs to a Symptom.
     */
    public function symptom()
    {
        return $this->belongsTo(Symptom::class);
    }
}
