<?php
session_start();
require_once __DIR__.'/includes/fungsi.php';
if (isset($_GET['logout'])) { $_SESSION=[]; session_destroy(); header('Location: index.php'); exit; }
if (empty($_SESSION['iduser'])) { require __DIR__.'/auth/login.php'; exit; }
require_once __DIR__.'/config/koneksi.php';
require_once __DIR__.'/includes/definisi.php';
require_once __DIR__.'/proses/proses_crud.php';
$page=$_GET['page']??'dashboard';
$allowed=['dashboard',...array_keys($defs)]; if(!in_array($page,$allowed,true))$page='dashboard';
$title=$page==='dashboard'?'Dashboard':$defs[$page]['title'];
require __DIR__.'/pages/component/header.php';
require __DIR__.'/pages/component/navbar.php';
?><main class="main-area"><header class="topbar"><div class="breadcrumb">Iventori / <strong><?=e($title)?></strong></div><div class="top-meta"><span class="online-dot"></span> Sistem Aktif <span><?=date('d M Y')?></span></div></header><section class="content-area">
<?php if($message=flash('success')):?><div class="alert success"><?=e($message)?></div><?php endif?>
<?php if($message=flash('error')):?><div class="alert error"><?=e($message)?></div><?php endif?>
<?php if($page==='dashboard') require __DIR__.'/pages/dashboard.php'; else require __DIR__.'/pages/tampilan/'.$page.'.php'; ?>
<footer class="footer">© <?=date('Y')?> IVENTORI · Sistem Manajemen Inventaris</footer></section></main></div><script src="assets/js/app.js"></script></body></html>
