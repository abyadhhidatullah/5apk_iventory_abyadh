<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function redirectKe(string $page = 'dashboard'): never { header('Location: index.php?page=' . urlencode($page)); exit; }
function flash(string $key, ?string $value = null): ?string {
    if ($value !== null) { $_SESSION['_flash'][$key] = $value; return null; }
    $message = $_SESSION['_flash'][$key] ?? null; unset($_SESSION['_flash'][$key]); return $message;
}
function fieldOptions(mysqli $db, string $column): array {
    $map = [
        'idkategori' => ['kategori','idkategori','namakategori'],
        'idmerek' => ['merek','idmerek','namamerek'],
        'idpenyuplai' => ['penyuplai','idpenyuplai','namapenyuplai'],
        'idbarang' => ['barang','idbarang','namabarang'],
        'iduser' => ['user','iduser','namauser'],
    ];
    if (!isset($map[$column])) return [];
    [$table,$id,$label] = $map[$column];
    $result = $db->query("SELECT `$id`,`$label` FROM `$table` ORDER BY `$label`");
    $items=[]; while($row=$result->fetch_assoc()) $items[]=$row; return $items;
}
