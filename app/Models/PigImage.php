<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PigImage extends Model
{
    use HasFactory;

    /**
     * Ang mga attributes na pwedeng i-mass assign.
     *
     * @var array
     */
    protected $fillable = [
        'report_id',
        'image_path',
    ];

    /**
     * Kunin ang report na kinabibilangan ng image.
     * Isang PigImage ay belongsTo (kasama sa) isang Report.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}