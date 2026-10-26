<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function wajibLogin(): void {
    if (empty($_SESSION['iduser'])) {
        header('Location: index.php');
        exit;
    }
}
