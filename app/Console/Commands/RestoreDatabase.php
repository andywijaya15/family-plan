<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RestoreDatabase extends Command
{
    protected $signature = 'db:restore:latest';
    protected $description = 'Restore PostgreSQL database from the latest backup file';

    public function handle()
    {
        $backupFiles = glob(storage_path('app/backups/*.sql'));

        if (empty($backupFiles)) {
            $this->error("❌ Tidak ada file backup di folder backups.");
            return;
        }

        // Ambil file terbaru berdasarkan waktu modifikasi
        usort($backupFiles, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestFile = $backupFiles[0];

        $url = parse_url(env('DB_URL'));

        $host = $url['host'];
        $port = $url['port'] ?? 5432;
        $user = $url['user'];
        $pass = $url['pass'];
        $dbname = ltrim($url['path'], '/');

        $this->info("🔄 Performing clean restore from: " . basename($latestFile));

        // 1️⃣ Drop dan recreate schema public
        $dropCommand = "PGPASSWORD=" . escapeshellarg($pass) .
            " psql -h " . escapeshellarg($host) .
            " -p " . escapeshellarg($port) .
            " -U " . escapeshellarg($user) .
            " -d " . escapeshellarg($dbname) .
            " -c " . escapeshellarg("DROP SCHEMA public CASCADE; CREATE SCHEMA public;");

        exec($dropCommand, $dropOutput, $dropResult);

        if ($dropResult !== 0) {
            $this->error("❌ Gagal drop schema public.");
            $this->line(implode("\n", $dropOutput));
            return;
        }

        $this->info("✅ Schema public berhasil di-drop dan dibuat ulang.");

        // 2️⃣ Restore backup
        $restoreCommand = "PGPASSWORD=" . escapeshellarg($pass) .
            " psql -h " . escapeshellarg($host) .
            " -p " . escapeshellarg($port) .
            " -U " . escapeshellarg($user) .
            " -d " . escapeshellarg($dbname) .
            " -f " . escapeshellarg($latestFile);

        exec($restoreCommand, $restoreOutput, $restoreResult);

        if ($restoreResult === 0) {
            $this->info("✅ Restore database berhasil!");
        } else {
            $this->error("❌ Gagal melakukan restore. Cek koneksi, database, atau isi file backup.");
            $this->line(implode("\n", $restoreOutput));
        }
    }
}
