<?php

use App\Models\DatabaseSnapshot;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// app(Schedule::class)->call(function () {
//     Log::info('TIME: ' . date('Y-m-d H:i:s') . ' - Creating backup...');

//     $databaseName = env('DB_DATABASE');
//     $backupName = 'backup_' . date('Y_m_d_H_i_s');

//     $sql = '';
//     $tables = DB::select("SHOW TABLES FROM $databaseName");
//     foreach ($tables as $table) {
//         $tableName = array_values((array)$table)[0];

//         // Skip the database_snapshots table
//         $skipTables = ['database_snapshots', 'password_reset_tokens', 'personal_access_tokens', 'migrations', 'password_resets', 'jobs', 'sessions', 'cache', 'cache_locks'];
//         if (in_array($tableName, $skipTables)) {
//             continue;
//         }

//         $createTable = DB::select("SHOW CREATE TABLE $tableName")[0]->{'Create Table'};
//         $sql .= "$createTable;\n\n";

//         $rows = DB::table($tableName)->get();
//         foreach ($rows as $row) {
//             $rowArray = (array)$row;
//             $columns = implode('`, `', array_keys($rowArray));
//             $values = implode("', '", array_map('addslashes', array_values($rowArray)));
//             $sql .= "INSERT INTO `$tableName` (`$columns`) VALUES ('$values');\n";
//         }
//         $sql .= "\n";
//     }

//     $zip = new \ZipArchive();
//     $zipFileName = storage_path("app/{$backupName}.zip");

//     if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
//         $files = new \RecursiveIteratorIterator(
//             new \RecursiveDirectoryIterator(storage_path('app/public')),
//             \RecursiveIteratorIterator::LEAVES_ONLY
//         );

//         foreach ($files as $name => $file) {
//             if (fnmatch('backup_*.zip', $file->getFilename())) {
//                 continue;
//             }

//             if (!$file->isDir()) {
//                 $filePath = $file->getRealPath();
//                 $relativePath = 'public/' . substr($filePath, strlen(storage_path('app/public')) + 1);
//                 $zip->addFile($filePath, $relativePath);
//             }
//         }

//         $zip->addFromString('backup.sql', $sql);

//         $zip->close();
//     } else {
//         return response()->json(['status' => 'zipf', 'message' => 'Could not create ZIP file.']);
//     }

//     DatabaseSnapshot::query()->create([
//         'sql' => $sql,
//         'files_path' => "app/{$backupName}.zip",
//     ]);
// })->everyMinute();
