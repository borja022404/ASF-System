<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Create a manual backup and download it immediately.
     */
    public function manualBackup()
    {
        $filename = 'backup_' . Carbon::now()->format('Y_m_d_His') . '.zip';

        // Call artisan backup command
        \Artisan::call('backup:database', [
            '--filename' => $filename
        ]);

        $filePath = storage_path('app/backups/' . $filename);

        // Wait for file to be created
        $tries = 0;
        while (!file_exists($filePath) && $tries < 10) {
            usleep(500000); // wait 0.5 seconds
            $tries++;
        }

        if (file_exists($filePath)) {
            // Return JSON with the download URL
            return response()->json([
                'success' => true,
                'download_url' => route('admin.backup.download', $filename)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Backup failed to create. Please try again.'
        ]);
    }



    /**
     * List all backup zip files in storage/app/backups/
     */
    public function listBackups()
    {
        // Make sure the backups folder exists
        if (!Storage::disk('local')->exists('backups')) {
            Storage::disk('local')->makeDirectory('backups');
        }

        // Get all files in backups folder
        $allFiles = Storage::disk('local')->files('backups');

        // Filter only .zip files
        $files = array_filter($allFiles, function ($file) {
            return strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'zip';
        });

        // Map file info
        $files = array_map(function ($file) {
            return [
                'name' => basename($file),
                'path' => $file,
                'size' => \Storage::disk('local')->size($file),
                'last_modified' => date('Y-m-d H:i:s', \Storage::disk('local')->lastModified($file)),
            ];
        }, $files);

        // Sort newest first
        usort($files, function ($a, $b) {
            return strtotime($b['last_modified']) - strtotime($a['last_modified']);
        });

        return view('admin.backups.index', compact('files'));
    }

    /**
     * Download a specific backup zip file
     */
    public function downloadBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return back()->with('error', 'File not found!');
    }
}
