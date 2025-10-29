<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Report;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on the authenticated user's role.
     */
    public function index()
    {
        $user = Auth::user();
        // $role = $user->role->name;


        if (Auth::user()->roles[0]->name == "admin") {
            $totalReports = Report::count();
            $totalUsers = User::count();
            $underInspectionReports = Report::where('report_status', 'under_inspection')->count();
            $resolvedReports = Report::where('report_status', 'resolved')->count();

            // get last 5 reports for "Recent Reports"
            $recentReports = Report::latest()->take(5)->get();

            return view('admin.dashboard', compact(
                'totalReports',
                'totalUsers',
                'underInspectionReports',
                'resolvedReports',
                'recentReports'
            ));

        } elseif (Auth::user()->roles[0]->name == "vet") {
            $vet = Auth::user();

            $allReports = Report::where(function ($query) use ($vet) {
                $query->where('report_status', '!=', 'resolved') // show all reports except resolved ones (unfiltered)
                    ->orWhere(function ($query) use ($vet) {
                        $query->where('report_status', 'resolved') // for resolved ones
                            ->whereHas('vetAssessments', function ($subQuery) use ($vet) {
                                $subQuery->where('assessor_id', $vet->id); // only their own assessments
                            });
                    });
            })
                ->latest()
                ->get();


            $unassessedReports = Report::where('report_status', 'submitted')->latest()->get();
            $underReviewReports = Report::where('report_status', 'under_inspection')->whereHas('vetAssessments', function ($query) use ($vet) {
                $query->where('assessor_id', $vet->id);
            })->latest()->get();
            $resolved = Report::where('report_status', 'resolved')
                ->whereHas('vetAssessments', function ($query) use ($vet) {
                    $query->where('assessor_id', $vet->id);
                })
                ->latest()
                ->get();

            $highRiskReports = Report::where('risk_level', 'high')
                ->where('report_status', '!=', 'resolved')
                ->latest()
                ->get();

            return view('vet.dashboard', compact('unassessedReports', 'underReviewReports', 'highRiskReports', 'allReports', 'resolved'));

        } elseif (Auth::user()->roles[0]->name == "farmer") {
            $reports = $user->reports()->latest()->get();
            $unassessedReports = $user->reports()->where('report_status', 'submitted')->latest()->get();
            $underReviewReports = $user->reports()->where('report_status', 'under_inspection')->latest()->get();
            $resolved = $user->reports()->where('report_status', 'resolved')->latest()->get();
            return view('farmer.dashboard', compact('reports', 'resolved', 'unassessedReports', 'underReviewReports'));
        }


        Auth::logout();
        return redirect('/login')->withErrors(['error' => 'No role assigned.']);

    }
}
