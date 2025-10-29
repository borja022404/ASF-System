<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\PigImage;
use App\Models\User;
use App\Models\Notification;
use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show the form for submitting a new report.
     */
    public function create()
    {
        // Group symptoms by risk_level
        $symptoms = [
            'low' => Symptom::where('risk_level', 'low')->get(),
            'medium' => Symptom::where('risk_level', 'medium')->get(),
            'high' => Symptom::where('risk_level', 'high')->get(),
        ];

        return view('farmer.reports.create', compact('symptoms'));
    }

    /**
     * Store a newly created report.
     */
    public function store(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|array|min:1',
            'symptoms.*' => 'exists:symptoms,id',
            'symptoms_description' => 'required|string',
            'symptom_onset_date' => 'required|date',
            'location_name' => 'nullable|string',
            'barangay' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'affected_pig_count' => 'nullable|integer|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $reportId = 'ASF-' . Str::upper(Str::random(8));
            $symptomIds = $request->input('symptoms', []);
            $selectedSymptoms = Symptom::whereIn('id', $symptomIds)->get();

            // Compute risk level
            $riskLevel = 'low';
            $pigHealthStatus = 'unassessed';
            if ($selectedSymptoms->contains('risk_level', 'high')) {
                $riskLevel = 'high';
                $pigHealthStatus = 'dead';
            } elseif ($selectedSymptoms->contains('risk_level', 'medium')) {
                $riskLevel = 'medium';
            }

            // Create report
            $report = Report::create([
                'user_id' => Auth::id(),
                'report_id' => $reportId,
                'symptoms_description' => $request->symptoms_description,
                'symptom_onset_date' => $request->symptom_onset_date,
                'location_name' => $request->location_name,
                'barangay' => $request->barangay,
                'city' => $request->city,
                'province' => $request->province,
                'report_status' => 'submitted',
                'risk_level' => $riskLevel,
                'pig_health_status' => $pigHealthStatus,
                'affected_pig_count' => $request->input('affected_pig_count', 0),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // Attach symptoms to pivot table
            $report->symptoms()->attach($symptomIds);
            // Upload pig images if any
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('public/pig_images');
                    PigImage::create([
                        'report_id' => $report->id,
                        'image_path' => str_replace('public/', '', $path),
                    ]);
                }
            }



            // Notify Vets
            $vets = User::whereHas('roles', fn($q) => $q->where('name', 'vet'))->get();
            foreach ($vets as $vet) {
                Notification::create([
                    'type' => 'NewReportSubmitted',
                    'sender_id' => Auth::id(),
                    'receiver_id' => $vet->id,
                    'notifiable_type' => Report::class,
                    'notifiable_id' => $report->id,
                    'data' => json_encode([
                        'message' => 'A new report has been submitted by ' . $report->user->name,
                        'report_id' => $report->report_id,
                        'risk_level' => $riskLevel,
                    ]),
                    'url' => route('vet.reports.show', $report->id),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('farmer.reports.show', $report->id)
                ->with('success', 'Report submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing report: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'There was an error submitting your report. Please try again.');
        }
    }
    /**
     * Show all farmer reports.
     */
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())
            ->with(['symptoms', 'images'])
            ->latest()
            ->get();

        return view('farmer.reports.index', compact('reports'));
    }

    /**
     * Show a specific report.
     */
    public function show(Report $report)
    {
        // Debug information - remove after fixing
        \Log::info('Report show method called', [
            'report_id' => $report->id,
            'report_user_id' => $report->user_id,
            'auth_user_id' => Auth::id(),
            'auth_user' => Auth::user() ? Auth::user()->toArray() : null
        ]);

        // Temporary disable for testing - REMOVE AFTER DEBUGGING
        // if ($report->user_id !== Auth::id()) {
        //     \Log::warning('Access denied to report', [
        //         'report_id' => $report->id,
        //         'report_user_id' => $report->user_id,
        //         'auth_user_id' => Auth::id()
        //     ]);
        //     abort(403);
        // }

        $report->load(['images', 'symptoms', 'notes.user']);

        // Always return a Collection for grouping
        $symptomsByRisk = $report->symptoms->isNotEmpty()
            ? $report->symptoms->groupBy('risk_level')
            : collect();

        return view('farmer.reports.show', compact('report', 'symptomsByRisk'));
    }

    /**
     * Edit report form.
     */
    public function edit(Report $report)
    {
        if ($report->user_id !== Auth::id())
            abort(403);

        $report->load('symptoms');
        $allSymptoms = Symptom::all()->groupBy('risk_level');

        return view('farmer.reports.edit', compact('report', 'allSymptoms'));
    }

    /**
     * Update an existing report.
     */
    public function update(Request $request, Report $report)
    {
        if ($report->user_id !== Auth::id())
            abort(403);


        $request->validate([
            'symptoms' => 'required|array',
            'symptoms.*' => 'exists:symptoms,id',
            'symptoms_description' => 'required|string',
            'symptom_onset_date' => 'required|date',
            'location_name' => 'nullable|string',
            'barangay' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'affected_pig_count' => 'integer|min:0',
        ]);

        $symptomIds = $request->input('symptoms', []);
        $selectedSymptoms = Symptom::whereIn('id', $symptomIds)->get();

        $riskLevel = 'low';
        $pigHealthStatus = 'unassessed';
        if ($selectedSymptoms->contains('risk_level', 'high')) {
            $riskLevel = 'high';
            $pigHealthStatus = 'dead';
        } elseif ($selectedSymptoms->contains('risk_level', 'medium')) {
            $riskLevel = 'medium';
        }

        $report->update([
            'symptoms_description' => $request->symptoms_description,
            'symptom_onset_date' => $request->symptom_onset_date,
            'location_name' => $request->location_name,
            'barangay' => $request->barangay,
            'city' => $request->city,
            'province' => $request->province,
            'risk_level' => $riskLevel,
            'pig_health_status' => $pigHealthStatus,
            'affected_pig_count' => $request->input('affected_pig_count', $report->affected_pig_count),
        ]);

        // Update pivot table
        $report->symptoms()->sync($symptomIds);

        return redirect()
            ->route('farmer.reports.show', $report->id)
            ->with('success', 'Report updated successfully!');
    }


    public function resolved()
    {
        $resolved = Report::where('report_status', 'resolved')->latest()->get();
        return view('farmer.reports.resolved', compact('resolved'));
    }


    public function submitted()
    {
        $user = Auth::user();
        $submitted = $user->reports()->where('report_status', 'submitted')->latest()->get();
        return view('farmer.reports.submitted', compact('submitted'));
    }



    public function inspection()
    {
        $inspection = Report::where('report_status', 'under_inspection')->latest()->get();
        return view('farmer.reports.inspection', compact('inspection'));
    }

}
