<?php

namespace App\Http\Controllers;

use App\Models\DatabaseSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BackupController extends Controller
{
    public function index()
    {
        $backups = DatabaseSnapshot::all();

        return view('backup.index')->with('backups', $backups);
    }

    public function deleteBackup(DatabaseSnapshot $backup)
    {
        $filePath = storage_path($backup->files_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $backup->delete();

        return redirect()
            ->route('backup.index')
            ->with('message', 'Backup deleted successfully')
            ->with('subtitle', 'The backup file has been permanently removed');
    }

    public function startBackup()
    {
        $databaseName = env('DB_DATABASE');
        $backupName = 'backup_' . date('Y_m_d_H_i_s');

        $sql = '';
        $tables = DB::select("SHOW TABLES FROM $databaseName");
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            // Skip the database_snapshots table
            $skipTables = ['database_snapshots', 'failed_jobs', 'password_reset_tokens', 'personal_access_tokens', 'migrations', 'password_resets', 'jobs', 'sessions', 'cache', 'cache_locks'];
            if (in_array($tableName, $skipTables)) {
                continue;
            }

            $createTable = DB::select("SHOW CREATE TABLE $tableName")[0]->{'Create Table'};
            $sql .= "$createTable;\n\n";

            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $rowArray = (array)$row;
                    $columns = implode('`, `', array_keys($rowArray));
                    
                    $values = [];
                    foreach ($rowArray as $value) {
                        if (is_null($value)) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    
                    $valueString = implode(', ', $values);
                    $sql .= "INSERT INTO `$tableName` (`$columns`) VALUES ($valueString);\n";
                }
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

    public function fromBackup(DatabaseSnapshot $backup)
    {
        $backupPath = storage_path($backup->files_path);

        if (!file_exists($backupPath)) {
            return response()->json(['status' => 'error', 'message' => 'Backup file not found.'], 404);
        }

        // Create a temporary directory to extract files
        $tempDir = storage_path('app/temp_restore_' . time());
        if (!mkdir($tempDir, 0755, true)) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create temporary directory.'], 500);
        }

        try {
            // Extract the zip file
            $zip = new \ZipArchive();
            if ($zip->open($backupPath) !== true) {
                throw new \Exception("Could not open the backup zip file.");
            }

            $zip->extractTo($tempDir);
            $zip->close();

            // Check if the SQL file exists
            $sqlFile = $tempDir . '/backup.sql';
            if (!file_exists($sqlFile)) {
                throw new \Exception("SQL backup file not found in the archive.");
            }

            // Get the list of existing tables to drop them before import
            // but keep the database_snapshots table
            $tables = DB::select('SHOW TABLES');

            // Start transaction for database operations
            try {
                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=0');

                // Drop existing tables except database_snapshots
                foreach ($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    if (!in_array($tableName, ['database_snapshots', 'failed_jobs', 'migrations', 'password_reset_tokens', 'personal_access_tokens', 'password_resets', 'jobs', 'sessions', 'cache', 'cache_locks'])) {
                        DB::unprepared("DROP TABLE IF EXISTS `$tableName`");
                    }
                }

                // Import SQL
                $sqlCommands = file_get_contents($sqlFile);
                DB::unprepared($sqlCommands);
            } catch (\Exception $e) {
                throw new \Exception("Database restore failed: " . $e->getMessage());
            } finally {
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }

            // Restore files
            $publicDir = $tempDir . '/public';
            if (is_dir($publicDir)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($publicDir),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $file) {
                    if ($file->isDir()) {
                        continue;
                    }

                    $relativePath = substr($file->getRealPath(), strlen($publicDir) + 1);
                    $targetPath = storage_path('app/public/' . $relativePath);

                    // Create directory if it doesn't exist
                    $targetDir = dirname($targetPath);
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }

                    copy($file->getRealPath(), $targetPath);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } finally {
            // Clean up temporary directory
            if (is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
        }
    }

    public function uploadBackup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'backup_file' => 'required|file|mimes:zip',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        // Save the uploaded file
        $backupFile = $request->file('backup_file');
        $backupName = 'backup_' . date('Y_m_d_H_i_s');
        $storagePath = "app/{$backupName}.zip";
        $backupFile->storeAs('', $backupName . '.zip');

        // Create a temporary directory to extract files
        $tempDir = storage_path('app/temp_restore_' . time());
        if (!mkdir($tempDir, 0755, true)) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create temporary directory.'], 500);
        }

        try {
            // Extract the zip file
            $zip = new \ZipArchive();
            if ($zip->open(storage_path($storagePath)) !== true) {
                throw new \Exception("Could not open the backup zip file.");
            }

            $zip->extractTo($tempDir);
            $zip->close();

            // Check if the SQL file exists
            $sqlFile = $tempDir . '/backup.sql';
            if (!file_exists($sqlFile)) {
                throw new \Exception("SQL backup file not found in the archive.");
            }

            // Get SQL content for storage
            $sqlContent = file_get_contents($sqlFile);

            // Get the list of existing tables to drop them before import
            $tables = DB::select('SHOW TABLES');

            try {
                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=0');

                // Drop existing tables except protected ones
                foreach ($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    if (!in_array($tableName, ['database_snapshots', 'migrations', 'password_reset_tokens', 'personal_access_tokens', 'password_resets', 'jobs', 'sessions', 'cache', 'cache_locks'])) {
                        DB::unprepared("DROP TABLE IF EXISTS `$tableName`");
                    }
                }

                // Import SQL
                DB::unprepared($sqlContent);
            } catch (\Exception $e) {
                throw new \Exception("Database restore failed: " . $e->getMessage());
            } finally {
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }

            // Restore files
            $publicDir = $tempDir . '/public';
            if (is_dir($publicDir)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($publicDir),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $file) {
                    if ($file->isDir()) {
                        continue;
                    }

                    $relativePath = substr($file->getRealPath(), strlen($publicDir) + 1);
                    $targetPath = storage_path('app/public/' . $relativePath);

                    // Create directory if it doesn't exist
                    $targetDir = dirname($targetPath);
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }

                    copy($file->getRealPath(), $targetPath);
                }
            }

            // Create a database record for the backup
            DatabaseSnapshot::create([
                'sql' => $sqlContent,
                'files_path' => $storagePath,
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } finally {
            // Clean up temporary directory
            if (is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Not implemented.']);
    }

    // Helper method to recursively delete a directory
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
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
