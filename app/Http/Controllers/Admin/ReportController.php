<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // for PDF
use PhpOffice\PhpSpreadsheet\Spreadsheet; // for Excel
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord; // for Word
use PhpOffice\PhpWord\IOFactory;
use App\Models\VetAssessment;
use Carbon\Carbon;


class ReportController extends Controller
{
    /**
     * Display a list of all reports.
     */
    public function index(Request $request)
    {
        // Base query with relationships (include assessor)
        $query = Report::with(['user', 'notes.user', 'vetAssessments.assessor']);

        // Filter by health status
        if ($request->filled('pig_health_status')) {
            $query->where('pig_health_status', $request->pig_health_status);
        }

        // Filter by risk level
        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where(function ($q) use ($request) {
                $q->where('barangay', 'like', '%' . $request->location . '%')
                    ->orWhere('city', 'like', '%' . $request->location . '%')
                    ->orWhere('province', 'like', '%' . $request->location . '%');
            });
        }

        // Paginated reports
        $reports = $query->latest()->paginate(10)->appends($request->query());

        // Risk counts (ignores pagination)
        $riskCounts = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
        ];

        $counts = Report::when($request->filled('pig_health_status'), function ($q) use ($request) {
            $q->where('pig_health_status', $request->pig_health_status);
        })
            ->when($request->filled('risk_level'), function ($q) use ($request) {
                $q->where('risk_level', $request->risk_level);
            })
            ->when($request->filled('location'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('barangay', 'like', '%' . $request->location . '%')
                        ->orWhere('city', 'like', '%' . $request->location . '%')
                        ->orWhere('province', 'like', '%' . $request->location . '%');
                });
            })
            ->select('risk_level')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level');

        foreach ($counts as $level => $total) {
            $riskCounts[$level] = $total;
        }

        return view('admin.reports.index', compact('reports', 'riskCounts'));
    }

    /**
     * Display the specified report with all details.
     */
    public function show(Report $report)
    {
        // Markahan ang report bilang 'nabasa' ng staff
        if (!$report->is_read_by_staff) {
            $report->is_read_by_staff = true;
            $report->save();
        }


        $report->load('user', 'images', 'notes.user');

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Update the specified report's status.
     */
    public function reportupdate(Request $request, Report $report)
    {
        $request->validate([
            'report_status' => 'required|in:under_inspection,resolved,closed',

        ]);

        $report->report_status = $request->report_status;
        $report->save();

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


    /**
     * Remove the specified report from storage.
     */
    public function destroy(Report $report)
    {
        $report->delete();
        return back()->with('success', 'Report deleted successfully!');
    }


    public function analysis()
    {
        // Reports by status
        $riskCounts = [
            'low' => Report::where('risk_level', 'low')->count(),
            'medium' => Report::where('risk_level', 'medium')->count(),
            'high' => Report::where('risk_level', 'high')->count(),
        ];

        // Calculate total and percentages for risk levels
        $totalRiskReports = $riskCounts['low'] + $riskCounts['medium'] + $riskCounts['high'];
        $riskPercentages = [
            'low' => $totalRiskReports > 0 ? round(($riskCounts['low'] / $totalRiskReports) * 100, 1) : 0,
            'medium' => $totalRiskReports > 0 ? round(($riskCounts['medium'] / $totalRiskReports) * 100, 1) : 0,
            'high' => $totalRiskReports > 0 ? round(($riskCounts['high'] / $totalRiskReports) * 100, 1) : 0,
        ];

        $submittedCount = Report::where('report_status', 'submitted')->count();
        $forInspectionCount = Report::where('report_status', 'under_inspection')->count();
        $resolvedCount = Report::where('report_status', 'resolved')->count();

        // Get last 6 months
        $months = [];
        $monthlyCounts = [];
        $monthlyRiskData = [];
        $monthlyStatusData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            // Total reports per month
            $monthlyCount = Report::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyCounts[] = $monthlyCount;

            // Risk level breakdown per month
            $monthlyRiskData[] = [
                'low' => Report::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('risk_level', 'low')
                    ->count(),
                'medium' => Report::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('risk_level', 'medium')
                    ->count(),
                'high' => Report::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('risk_level', 'high')
                    ->count(),
            ];

            // Status breakdown per month
            $monthlyStatusData[] = [
                'submitted' => Report::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('report_status', 'submitted')
                    ->count(),
                'inspection' => Report::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('report_status', 'under_inspection')
                    ->count(),
                'resolved' => Report::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('report_status', 'resolved')
                    ->count(),
            ];
        }

        return view('admin.reports.analysis', compact(
            'riskCounts',
            'riskPercentages',
            'submittedCount',
            'forInspectionCount',
            'resolvedCount',
            'months',
            'monthlyCounts',
            'monthlyRiskData',
            'monthlyStatusData'
        ));
    }

    public function reportsMap()
    {
        $reports = Report::select(
            'id',
            'report_id',
            'barangay',
            'city',
            'province',
            'latitude',
            'longitude',
            'risk_level',
            'report_status',
            'created_at'
        )
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();


        return view('admin.map', compact('reports'));
    }


    public function export(Request $request)
    {
        $type = $request->get('type', 'pdf'); // default pdf

        // ✅ Apply filters
        $query = Report::with(['user', 'notes.user', 'vetAssessments.assessor']);

        if ($request->pig_health_status) {
            $query->where('pig_health_status', $request->pig_health_status);
        }
        if ($request->risk_level) {
            $query->where('risk_level', $request->risk_level);
        }
        if ($request->location) {
            $query->where(function ($q) use ($request) {
                $q->where('barangay', 'like', "%{$request->location}%")
                    ->orWhere('city', 'like', "%{$request->location}%");
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        // ✅ PDF Export
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.exports.pdf', compact('reports'));
            return $pdf->download('reports.pdf');
        }

        
        // ✅ Excel Export
        if ($type === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Headers
            $sheet->setCellValue('A1', 'Report ID');
            $sheet->setCellValue('B1', 'User');
            $sheet->setCellValue('C1', 'Health Status');
            $sheet->setCellValue('D1', 'Risk Level');
            $sheet->setCellValue('E1', 'Location');
            $sheet->setCellValue('F1', 'Date');
            $sheet->setCellValue('G1', 'Specialist');
            $sheet->setCellValue('H1', 'Notes');

            $row = 2;
            foreach ($reports as $report) {
                $notesText = $report->notes->map(function ($note) {
                    return strtoupper($note->note_type) . " by " . ($note->user->name ?? 'Unknown') .
                        ": " . $note->content . " (" . $note->created_at->format('M d, Y h:i A') . ")";
                })->implode("\n");

                $sheet->setCellValue("A{$row}", $report->report_id);
                $sheet->setCellValue("B{$row}", $report->user->name ?? 'N/A');
                $sheet->setCellValue("C{$row}", ucfirst($report->pig_health_status));
                $sheet->setCellValue("D{$row}", ucfirst($report->risk_level));
                $sheet->setCellValue("E{$row}", "{$report->barangay}, {$report->city}");
                $sheet->setCellValue("F{$row}", $report->created_at->format('M d, Y'));

                // Handle vet assessments safely in PHP (not Blade)
                if ($report->vetAssessments->isNotEmpty()) {
                    // Collect all assessor names into a string
                    $assessors = $report->vetAssessments->pluck('assessor.name')->implode(', ');
                } else {
                    $assessors = 'N/A';
                }

                $sheet->setCellValue("G{$row}", $assessors);

                // Assuming $notesText is defined elsewhere
                $sheet->setCellValue("H{$row}", $notesText);


                // Wrap text for notes
                $sheet->getStyle("G{$row}")->getAlignment()->setWrapText(true);
                $row++;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = "reports.xlsx";
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
        }

        // ✅ Word Export
        if ($type === 'word') {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            foreach ($reports as $report) {
                $section->addTitle("Report: {$report->report_id}", 2);
                $section->addText("User: " . ($report->user->name ?? 'N/A'));
                $section->addText("Health Status: " . ucfirst($report->pig_health_status));
                $section->addText("Risk Level: " . ucfirst($report->risk_level));
                $section->addText("Location: {$report->barangay}, {$report->city}");
                $section->addText("Date: " . $report->created_at->format('M d, Y'));
                 if ($report->vetAssessments->isNotEmpty()) {
                    // Collect all assessor names into a string
                    $assessors = $report->vetAssessments->pluck('assessor.name')->implode(', ');
                } else {
                    $assessors = 'N/A';
                }
                $section->addText("Specilaist: " . $assessors);

                // Notes
                if ($report->notes->count()) {
                    $section->addText("Notes:", ['bold' => true]);
                    foreach ($report->notes as $note) {
                        $section->addText(
                            strtoupper($note->note_type) . " by " . ($note->user->name ?? 'Unknown') .
                            ": " . $note->content . " (" . $note->created_at->format('M d, Y h:i A') . ")"
                        );
                    }
                } else {
                    $section->addText("No notes.");
                }

                $section->addTextBreak(2);
            }

            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $fileName = 'reports.docx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);

            return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Invalid export type selected.');
    }
}