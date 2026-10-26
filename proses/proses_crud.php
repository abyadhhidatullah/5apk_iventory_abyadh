<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['crud_action'])) {
    return;
}

$entity = (string)($_POST['entity'] ?? '');
if (!isset($defs[$entity])) {
    flash('error', 'Bagian data tidak valid.');
    redirectKe();
}

$definition = $defs[$entity];
$table = $definition['table'];
$primaryKey = $definition['pk'];
$action = (string)$_POST['crud_action'];

try {
    if ($action === 'delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            throw new RuntimeException('ID data tidak valid.');
        }
        $koneksi->begin_transaction();
        if ($entity === 'barang') {
            $check = $koneksi->prepare('SELECT COUNT(*) AS total FROM barangmasuk WHERE idbarang = ?');
            $check->bind_param('i', $id);
            $check->execute();
            if ((int)$check->get_result()->fetch_assoc()['total'] > 0) {
                $check->close();
                throw new RuntimeException('Barang memiliki riwayat barang masuk sehingga tidak dapat dihapus.');
            }
            $check->close();
        }
        if ($entity === 'barangmasuk') {
            $find = $koneksi->prepare('SELECT idbarang, jumlah FROM barangmasuk WHERE idbarangmasuk = ? FOR UPDATE');
            $find->bind_param('i', $id);
            $find->execute();
            $old = $find->get_result()->fetch_assoc();
            $find->close();
            if ($old) {
                $stock = $koneksi->prepare('UPDATE barang SET stok = GREATEST(0, stok - ?) WHERE idbarang = ?');
                $stock->bind_param('ii', $old['jumlah'], $old['idbarang']);
                $stock->execute();
                $stock->close();
            }
        }
        $stmt = $koneksi->prepare("DELETE FROM `$table` WHERE `$primaryKey` = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        $koneksi->commit();
        flash('success', 'Data berhasil dihapus.');
    } elseif (in_array($action, ['create', 'update'], true)) {
        $columns = [];
        $values = [];
        $types = '';
        $params = [];
        foreach ($definition['fields'] as $column => $meta) {
            $value = trim((string)($_POST[$column] ?? ''));
            if ($column === 'password' && $value === '' && $action === 'update') {
                continue;
            }
            if (!empty($meta[2]) && $value === '') {
                throw new RuntimeException($meta[0] . ' wajib diisi.');
            }
            if ($column === 'password' && $value !== '') {
                if (strlen($value) < 8) {
                    throw new RuntimeException('Password minimal 8 karakter.');
                }
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            if ($meta[1] === 'number' && (!is_numeric($value) || (int)$value < 0)) {
                throw new RuntimeException($meta[0] . ' harus berupa angka nol atau lebih.');
            }
            $columns[] = $column;
            $values[] = $value;
            $types .= 's';
            $params[] = $value;
        }
        if ($entity === 'user' && $action === 'create' && trim((string)($_POST['password'] ?? '')) === '') {
            throw new RuntimeException('Password wajib diisi untuk pengguna baru.');
        }
        if ($entity === 'barangmasuk' && (int)($_POST['jumlah'] ?? 0) < 1) {
            throw new RuntimeException('Jumlah barang masuk minimal 1.');
        }

        $koneksi->begin_transaction();
        if ($action === 'create') {
            $quotedColumns = '`' . implode('`,`', $columns) . '`';
            $placeholders = implode(',', array_fill(0, count($columns), '?'));
            $sql = "INSERT INTO `$table` ($quotedColumns) VALUES ($placeholders)";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();
            if ($entity === 'barangmasuk') {
                $itemId = (int)($_POST['idbarang'] ?? 0);
                $quantity = (int)($_POST['jumlah'] ?? 0);
                $stock = $koneksi->prepare('UPDATE barang SET stok = stok + ? WHERE idbarang = ?');
                $stock->bind_param('ii', $quantity, $itemId);
                $stock->execute();
                if ($stock->affected_rows < 1) {
                    $stock->close();
                    throw new RuntimeException('Barang tidak ditemukan; stok tidak diperbarui.');
                }
                $stock->close();
            }
        } else {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            if (!$id || $id < 1) {
                throw new RuntimeException('ID data tidak valid.');
            }
            if ($entity === 'barangmasuk') {
                $oldQuery = $koneksi->prepare('SELECT idbarang, jumlah FROM barangmasuk WHERE idbarangmasuk = ? FOR UPDATE');
                $oldQuery->bind_param('i', $id);
                $oldQuery->execute();
                $old = $oldQuery->get_result()->fetch_assoc();
                $oldQuery->close();
                if (!$old) {
                    throw new RuntimeException('Transaksi barang masuk tidak ditemukan.');
                }
                $oldStock = $koneksi->prepare('UPDATE barang SET stok = GREATEST(0, stok - ?) WHERE idbarang = ?');
                $oldStock->bind_param('ii', $old['jumlah'], $old['idbarang']);
                $oldStock->execute();
                $oldStock->close();
            }
            $set = implode(',', array_map(static fn($column) => "`$column` = ?", $columns));
            $sql = "UPDATE `$table` SET $set WHERE `$primaryKey` = ?";
            $types .= 'i';
            $params[] = $id;
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();
            if ($entity === 'barangmasuk') {
                $itemId = (int)($_POST['idbarang'] ?? 0);
                $quantity = (int)($_POST['jumlah'] ?? 0);
                $newStock = $koneksi->prepare('UPDATE barang SET stok = stok + ? WHERE idbarang = ?');
                $newStock->bind_param('ii', $quantity, $itemId);
                $newStock->execute();
                $newStock->close();
            }
        }
        $koneksi->commit();
        flash('success', $action === 'create' ? 'Data berhasil ditambahkan.' : 'Data berhasil diperbarui.');
    } else {
        throw new RuntimeException('Aksi tidak dikenali.');
    }
} catch (Throwable $exception) {
    try { $koneksi->rollback(); } catch (Throwable $ignored) {}
    error_log('IVENTORI CRUD error: ' . $exception->getMessage());
    flash('error', $exception instanceof mysqli_sql_exception
        ? 'Data gagal disimpan. Periksa data wajib, duplikasi, dan relasi tabel.'
        : $exception->getMessage());
}
redirectKe($entity);
