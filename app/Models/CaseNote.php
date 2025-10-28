<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseNote extends Model
{
    use HasFactory;

    /**
     * Ang mga attributes na pwedeng i-mass assign.
     *
     * @var array
     */
    protected $fillable = [
        'report_id',
        'user_id',
        'content',
        'note_type',
    ];

    /**
     * Kunin ang report na kinabibilangan ng note.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Kunin ang user na gumawa ng note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}