<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\CaseNote;
use App\Models\Report;
use App\Models\Notification; // Add this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Add this line

class CaseNoteController extends Controller
{
    /**
     * Store a new note for a specific report.
     */
    public function store(Request $request, Report $report)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        // Create the new case note
        $note = CaseNote::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(), // The user (vet) who is logged in
            'content' => $validated['content'],
            'note_type' => 'farmer_comment'
        ]);

        // Create a notification for the report owner (the farmer)
        if ($report->user_id) {
            Notification::create([
                'type' => 'new_case_note',
                'sender_id' => Auth::id(),
                'receiver_id' => $report->user_id, // The farmer who submitted the report
                'notifiable_type' => Report::class,
                'notifiable_id' => $report->id,
                'data' => 'A new comment has been added: ' . $report->report_id,
                'url' => route('admin.reports.show', $report->id),
                'read_at' => null, // Unread by default
            ]);
        }

        return back()->with('success', 'Note added successfully!');
    }


    

}