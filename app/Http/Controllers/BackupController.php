<?php

namespace App\Http\Controllers;

use App\Models\DatabaseSnapshot;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function index()
    {
        $backups = DatabaseSnapshot::all();

        return view('backup.index')->with('backups', $backups);
    }

    public function startBackup()
    {
        $databaseName = env('DB_DATABASE');
        $backupName = 'backup_' . date('Y_m_d_H_i_s');

        $sql = '';
        $tables = DB::select("SHOW TABLES FROM $databaseName");
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            $createTable = DB::select("SHOW CREATE TABLE $tableName")[0]->{'Create Table'};
            $sql .= "$createTable;\n\n";

            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array)$row;
                $columns = implode('`, `', array_keys($rowArray));
                $values = implode("', '", array_map('addslashes', array_values($rowArray)));
                $sql .= "INSERT INTO `$tableName` (`$columns`) VALUES ('$values');\n";
            }
            $sql .= "\n";
        }

        $zip = new \ZipArchive();
        $zipFileName = storage_path("app/{$backupName}.zip");

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(storage_path('app/public')),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (fnmatch('backup_*.zip', $file->getFilename())) {
                    continue;
                }

                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = 'public/' . substr($filePath, strlen(storage_path('app/public')) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->addFromString('backup.sql', $sql);

            $zip->close();
        } else {
            return response()->json(['status' => 'zipf', 'message' => 'Could not create ZIP file.']);
        }

        DatabaseSnapshot::query()->create([
            'sql' => $sql,
            'files_path' => "app/{$backupName}.zip",
        ]);

        return response()->json(['status' => 'success']);
    }

    public function downloadBackup(DatabaseSnapshot $backup)
    {
        $filePath = storage_path($backup->files_path);

        if (!file_exists($filePath)) {
            return response()->json(['status' => 'error', 'message' => 'File not found.'], 404);
        }

        return response()->download($filePath);
    }
}
