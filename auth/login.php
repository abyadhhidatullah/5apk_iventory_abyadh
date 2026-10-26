<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/fungsi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        try {
            $stmt = $koneksi->prepare('SELECT iduser, namauser, username, password FROM `user` WHERE username = ? LIMIT 1');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $account = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $stored = (string)($account['password'] ?? '');
            $valid = false;
            $legacyPlainText = false;
            if ($account && $stored !== '') {
                if (password_get_info($stored)['algo'] !== null) {
                    $valid = password_verify($password, $stored);
                } else {
                    // Support accounts created by the old schema that stored plain-text passwords.
                    $valid = hash_equals($stored, $password);
                    $legacyPlainText = $valid;
                }
            }

            if ($valid) {
                // The migration SQL widens password to VARCHAR(255). Only then upgrade legacy values.
                if ($legacyPlainText) {
                    $column = $koneksi->query("SELECT CHARACTER_MAXIMUM_LENGTH AS max_len FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'password'")->fetch_assoc();
                    if ((int)($column['max_len'] ?? 0) >= 60) {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $upgrade = $koneksi->prepare('UPDATE `user` SET `password` = ? WHERE `iduser` = ?');
                        $upgrade->bind_param('si', $newHash, $account['iduser']);
                        $upgrade->execute();
                        $upgrade->close();
                    }
                }
                session_regenerate_id(true);
                $_SESSION['iduser'] = (int)$account['iduser'];
                $_SESSION['namauser'] = $account['namauser'];
                $_SESSION['username'] = $account['username'];
                $_SESSION['login'] = true;
                redirectKe('dashboard');
            }
            $error = 'Username atau password tidak sesuai.';
        } catch (mysqli_sql_exception $exception) {
            error_log('IVENTORI login error: ' . $exception->getMessage());
            $error = 'Login belum dapat diproses. Periksa koneksi dan struktur database.';
        }
    }
}
?><!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#d63384"><title>Masuk | IVENTORI</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="login-page"><section class="login-art"><div class="brand"><span class="brand-logo">▦</span><span>IVENTORI</span></div><div><p class="eyebrow">INVENTORY MANAGEMENT SYSTEM</p><h1>Inventaris rapi, pekerjaan lebih mudah.</h1><p>Kelola data barang, stok masuk, kategori, merek, penyuplai, dan pengguna dari satu tempat.</p><div class="login-feature"><span>✓</span> Data terorganisir dan mudah dicari</div><div class="login-feature"><span>✓</span> Ringkasan stok dalam satu dashboard</div></div><small>© <?= date('Y') ?> IVENTORI · Inventory Management System</small></section>
<main class="login-main"><form class="login-card" method="post" action="" autocomplete="on"><span class="eyebrow">SELAMAT DATANG KEMBALI</span><h2>Masuk ke akun</h2><p class="muted">Gunakan akun yang sudah terdaftar.</p><?php if ($error !== ''): ?><div class="alert error" role="alert"><?= e($error) ?></div><?php endif; ?><label for="username">Username<input id="username" name="username" autocomplete="username" required autofocus value="<?= e($_POST['username'] ?? '') ?>"></label><label for="password">Password<div class="password-field"><input id="password" type="password" name="password" autocomplete="current-password" required><button type="button" id="togglePassword" aria-label="Tampilkan password" aria-pressed="false">Lihat</button></div></label><button class="btn primary full" type="submit">Masuk ke IVENTORI <span aria-hidden="true">→</span></button><p class="login-note">Akses hanya untuk pengguna yang terdaftar.</p></form></main><script src="assets/js/app.js"></script></body></html>
