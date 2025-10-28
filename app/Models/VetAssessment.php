<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VetAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'assessor_id',
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

}
