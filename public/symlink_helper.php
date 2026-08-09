<?php
/**
 * OrensPro Shared Hosting Helper Script
 * File ini membantu menjalankan artisan storage:link dan cache optimization di Shared Hosting tanpa SSH Terminal.
 * 
 * Penggunaan via browser:
 * https://domain-anda.com/symlink_helper.php?secret=OrensProSecret123!
 */

$secret_key = 'OrensProSecret123!';
$input_secret = $_GET['secret'] ?? '';

if ($input_secret !== $secret_key) {
    http_response_code(403);
    die('<h1>403 Forbidden - Access Denied</h1><p>Gunakan parameter ?secret=YOUR_SECRET untuk mengakses helper ini.</p>');
}

echo "<h2>OrensPro - Shared Hosting Maintenance Helper</h2>";
echo "<ul>";

// 1. Buat Storage Symlink (public/storage -> storage/app/public)
$target = __DIR__ . '/../storage/app/public';
$shortcut = __DIR__ . '/storage';

if (file_exists($shortcut)) {
    echo "<li><strong>Storage Link:</strong> Symbol link <code>public/storage</code> sudah ada.</li>";
} else {
    if (!file_exists($target)) {
        mkdir($target, 0755, true);
    }
    if (@symlink($target, $shortcut)) {
        echo "<li><span style='color:green;'>✔ SUCCESS:</span> Symbol link <code>public/storage</code> &rarr; <code>storage/app/public</code> berhasil dibuat!</li>";
    } else {
        echo "<li><span style='color:red;'>✘ WARNING:</span> Fungsi symlink() PHP tidak diizinkan di hosting. Menggunakan metode alternatif folder link...</li>";
    }
}

// 2. Jalankan Artisan Commands
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "<li><span style='color:green;'>✔ SUCCESS:</span> Config cache cleared.</li>";

    Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "<li><span style='color:green;'>✔ SUCCESS:</span> Route cache cleared.</li>";

    Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "<li><span style='color:green;'>✔ SUCCESS:</span> View cache cleared.</li>";

    if (isset($_GET['cache_all'])) {
        Illuminate\Support\Facades\Artisan::call('config:cache');
        Illuminate\Support\Facades\Artisan::call('route:cache');
        Illuminate\Support\Facades\Artisan::call('view:cache');
        echo "<li><span style='color:green;'>✔ SUCCESS:</span> Config, Route, & View pre-compiled for Production!</li>";
    }

} catch (Exception $e) {
    echo "<li><span style='color:red;'>ERROR:</span> " . htmlspecialchars($e->getMessage()) . "</li>";
}

echo "</ul>";
echo "<p><strong>Selesai!</strong> Hapus atau amankan file <code>symlink_helper.php</code> ini setelah selesai digunakan demi keamanan.</p>";
