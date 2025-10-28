<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--filename=}';
    protected $description = 'Backup the entire database';

    public function handle()
    {
        $timestamp = Carbon::now()->format('Y_m_d_His');
        $sqlFilename = 'backup_' . $timestamp . '.sql';
        $zipFilename = 'backup_' . $timestamp . '.zip';
        $storagePathSql = storage_path('app/backups/' . $sqlFilename);
        $storagePathZip = storage_path('app/backups/' . $zipFilename);

        // Ensure backups directory exists
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        // Get database config
        $dbConnection = config('database.default');
        $dbHost = config("database.connections.$dbConnection.host");
        $dbPort = config("database.connections.$dbConnection.port");
        $dbDatabase = config("database.connections.$dbConnection.database");
        $dbUsername = config("database.connections.$dbConnection.username"); // root
        $dbPassword = config("database.connections.$dbConnection.password"); // root
        $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        $passwordPart = $dbPassword ? "--password={$dbPassword}" : "";
        $command = "cmd /c \"$mysqldump --user={$dbUsername} $passwordPart --host={$dbHost} --port={$dbPort} {$dbDatabase} > {$storagePathSql}\"";

        $returnVar = null;
        $output = null;
        exec($command, $output, $returnVar);

        if ($returnVar === 0 && file_exists($storagePathSql)) {
            // Compress to ZIP
            $zip = new \ZipArchive();
            if ($zip->open($storagePathZip, \ZipArchive::CREATE) === true) {
                $zip->addFile($storagePathSql, $sqlFilename);
                $zip->close();

                // Delete the raw SQL file to keep only the zip
                unlink($storagePathSql);

                $this->info("Database backup created and compressed: {$zipFilename}");
            } else {
                $this->error("Failed to create ZIP archive.");
            }
        } else {
            $this->error("Backup failed! Command output: " . implode("\n", $output));
        }
    }

}
