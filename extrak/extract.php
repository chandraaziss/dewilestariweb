<?php
$zip = new ZipArchive;
$path = __DIR__ . '/laravel/vendor';
$namaZip = __DIR__ . '/laravel/vendor.zip';

if (!is_dir($path)) {
    mkdir($path, 0755, true);
}

if ($zip->open($namaZip) === TRUE) {
    $zip->extractTo($path);
    $zip->close();
    echo "Vendor berhasil di-extract ✅<br>";
    unlink($namaZip);
    echo "vendor.zip dihapus.";
} else {
    echo "Gagal membuka vendor.zip";
}