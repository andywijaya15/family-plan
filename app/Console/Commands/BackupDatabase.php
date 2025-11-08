<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup PostgreSQL database';

    public function handle()
    {
        $url = parse_url(env('DB_URL'));

        $host = $url['host'];
        $port = $url['port'] ?? 5432;
        $user = $url['user'];
        $pass = $url['pass'];
        $dbname = ltrim($url['path'], '/');

        $timestamp = now()->format('Ymd_His');
        $backupFile = storage_path("app/backups/{$dbname}_{$timestamp}.sql");

        // Pastikan folder ada
        Storage::makeDirectory('backups');

        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $command = "PGPASSWORD=\"{$pass}\" pg_dump -h {$host} -p {$port} -U {$user} -d {$dbname} -F p -n public > {$backupFile}";

        $this->info("Running backup...");
        $result = null;
        $output = null;

        exec($command, $output, $result);

        if ($result === 0) {
            $this->info("✅ Backup berhasil disimpan di: {$backupFile}");
        } else {
            $this->error("❌ Gagal melakukan backup. Coba cek koneksi atau pg_dump di server.");
        }
    }
}
