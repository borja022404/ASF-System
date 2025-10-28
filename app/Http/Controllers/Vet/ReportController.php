<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VetAssessment;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{

    public function index(Request $request)
    {
        $query = Report::with(['user', 'symptoms']);

        // Filter by Report ID
        if ($request->filled('report_id')) {
            $query->where('report_id', 'like', '%' . $request->report_id . '%');
        }

        // Filter by Health Status
        if ($request->filled('pig_health_status')) {
            $query->where('pig_health_status', $request->pig_health_status);
        }

        // Filter by Risk Level
        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        // Use pagination with query persistence
        $reports = $query->latest()->paginate(10)->appends($request->query());

        return view('vet.reports.index', compact('reports'));
    }



    /**
     * Ipakita ang detalye ng isang partikular na report.
     * Kasama rito ang notes at images.
     */
    public function show(Report $report)
    {
        // Markahan ang report bilang 'nabasa' ng staff
        if (!$report->is_read_by_staff) {
            $report->is_read_by_staff = true;
            $report->save();
        }

        // Gamitin ang with() para makuha ang related models (eager loading)
        $report->load('user', 'images', 'notes.user');

        return view('vet.reports.show', compact('report'));
    }

    public function UnderReview(Request $request)
    {
        $query = Report::with('user')->where('report_status', 'under_inspection');

        if ($request->filled('report_id')) {
            $query->where('report_id', 'like', '%' . $request->report_id . '%');
        }

        $reports = $query->latest()->get();

        return view('vet.reports.under_review', compact('reports'));
    }
    public function UnassessedReview(Request $request)
    {
        $query = Report::with('user')->where('report_status', 'submitted');

        if ($request->filled('report_id')) {
            $query->where('report_id', 'like', '%' . $request->report_id . '%');
        }

        $reports = $query->latest()->get();

        return view('vet.reports.pending_review', compact('reports'));
    }


    /**
     * I-update ang status ng isang report.
     */


    public function reportupdate(Request $request, Report $report)
    {
        $request->validate([
            'report_status' => 'required|in:under_inspection,resolved,closed',
        ]);

        $report->report_status = $request->report_status;
        $report->save();

        // Log assessor activity in vet_assessments table
        VetAssessment::updateOrCreate(
            [
                'report_id' => $report->id,
                'assessor_id' => Auth::id(),
            ],
            [] // No extra fields for now
        );

        // Create notification
        Notification::create([
            'type' => 'report_status_updated',
            'sender_id' => Auth::id(),
            'receiver_id' => $report->user_id,
            'notifiable_type' => Report::class,
            'notifiable_id' => $report->id,
            'data' => "Your ASF Report ({$report->report_id}) status has been updated to: {$report->report_status}",
            'url' => route('farmer.reports.show', $report->id),
            'read_at' => null,
        ]);

        return back()->with('success', 'Report status updated successfully!');
    }

    public function healthupdate(Request $request, Report $report)
    {
        $request->validate([
            'risk_level' => 'required|in:low,medium,high',
            'pig_health_status' => 'required|in:unassessed,infected,dead,isolate',
        ]);

        $report->risk_level = $request->risk_level;
        $report->pig_health_status = $request->pig_health_status;
        $report->save();

        // Log assessor activity in vet_assessments table
        VetAssessment::updateOrCreate(
            [
                'report_id' => $report->id,
                'assessor_id' => Auth::id(),
            ],
            [] // No extra fields for now
        );

        // Notify the report owner (farmer)
        Notification::create([
            'type' => 'report_status_updated',
            'sender_id' => Auth::id(),
            'receiver_id' => $report->user_id,
            'notifiable_type' => Report::class,
            'notifiable_id' => $report->id,
            'data' => "Hello {$report->user->name}, the health status for your ASF Report (#{$report->report_id}) has been updated to '{$report->pig_health_status}' with a risk level of '{$report->risk_level}'.",
            'url' => route('farmer.reports.show', $report->id),
            'read_at' => null,
        ]);

        // Notify Admins
        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            Notification::create([
                'type' => 'report_updated_by_Specialist',
                'sender_id' => Auth::id(),
                'receiver_id' => $admin->id,
                'notifiable_type' => Report::class,
                'notifiable_id' => $report->id,
                'data' => json_encode([
                    'message' => "Report (#{$report->report_id}) has been assessed by specialist {$report->user->name}.",
                    'report_id' => $report->report_id,
                    'risk_level' => $report->risk_level,
                    'pig_health_status' => $report->pig_health_status,
                ]),
                'url' => route('admin.reports.show', $report->id),
                'read_at' => null,
            ]);
        }

        DB::commit();
        return back()->with('success', 'Report health status updated successfully!');
    }



    public function highRisk()
    {
        $highRiskReports = Report::where('risk_level', 'high')
            ->latest()
            ->get();

        return view('vet.reports.high_risk', compact('highRiskReports'));
    }


    public function resolved()
    {
        $resolved = Report::where('report_status', 'resolved')->latest()->get();
        return view('Vet.reports.resolved', compact('resolved'));
    }

}