<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GeolocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlDir = database_path('seeders/sql');
        $altDir = 'C:\\Users\\Gonza\\Documents\\CLIENTES\\Geolocalización';

        $baseDir = File::isDirectory($sqlDir) ? $sqlDir : $altDir;

        $files = [
            'countries' => 'PaisesLatan.sql',
            'states' => 'ProvinciasEstadosLatan.sql',
            'locations' => 'LocalidadesLatan.sql',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('locations')->truncate();
        DB::table('states')->truncate();
        DB::table('countries')->truncate();

        foreach ($files as $table => $file) {
            $filePath = $baseDir . DIRECTORY_SEPARATOR . $file;
            if (!File::exists($filePath)) {
                $filePath = $altDir . DIRECTORY_SEPARATOR . $file;
            }

            if (!File::exists($filePath)) {
                $this->command->error("File not found: {$file}");
                continue;
            }

            $content = File::get($filePath);
            // Remove multi-line comments /* ... */
            $content = preg_replace('/\/\*.*?\*\//s', '', $content);
            // Replace invalid timestamp '1900-10-11 21:00:00' which is outside MySQL timestamp range (1970-2038)
            $content = str_replace("'1900-10-11 21:00:00'", "'2024-01-01 00:00:00'", $content);
            
            // Clean up comments and execute line by line or unprepared
            $lines = explode("\n", $content);
            $statements = [];
            $currentStatement = '';

            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
                    continue;
                }
                $currentStatement .= $line . "\n";
                if (str_ends_with($trimmed, ';')) {
                    $statements[] = $currentStatement;
                    $currentStatement = '';
                }
            }

            foreach ($statements as $statement) {
                try {
                    DB::unprepared($statement);
                } catch (\Throwable $e) {
                    $this->command->warn("Error executing in {$table}: " . $e->getMessage());
                }
            }

            $count = DB::table($table)->count();
            $this->command->info("Seeded table '{$table}' with {$count} records.");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
