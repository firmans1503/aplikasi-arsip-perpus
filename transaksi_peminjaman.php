<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$role = $_SESSION['role'];

// Handle Borrowing
if (isset($_POST['pinjam']) && $role != 'kapus') {
    $id_anggota = $_POST['id_anggota'];
    $id_buku = $_POST['id_buku'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    // Check Stock
    $cek_stok = $conn->query("SELECT stok FROM buku WHERE id_buku=$id_buku")->fetch_assoc();
    if ($cek_stok['stok'] > 0) {
        // Insert Peminjaman
        $sql = "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, status) VALUES ('$id_anggota', '$id_buku', '$tanggal_pinjam', '$tanggal_kembali', 'dipinjam')";
        if ($conn->query($sql)) {
            // Decrease Stock
            $conn->query("UPDATE buku SET stok = stok - 1 WHERE id_buku=$id_buku");
            $success = "Peminjaman berhasil dicatat!";
        } else {
            $error = "Gagal mencatat peminjaman: " . $conn->error;
        }
    } else {
        $error = "Stok buku habis!";
    }
}

// Fetch Data for Dropdowns
$anggota = $conn->query("SELECT * FROM anggota ORDER BY nama ASC");
$buku = $conn->query("SELECT * FROM buku WHERE stok > 0 ORDER BY judul ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaksi Peminjaman - Perpustakaan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-archive me-2"></i>Sistem Arsip & Perpus</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="buku.php">Buku</a></li>
          <li class="nav-item"><a class="nav-link" href="anggota.php">Anggota</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">Transaksi</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="transaksi_peminjaman.php">Peminjaman</a></li>
                <li><a class="dropdown-item" href="transaksi_pengembalian.php">Pengembalian</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
          <li class="nav-item ms-2">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['user'] ?>&background=random" alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                    <span><?= $_SESSION['user'] ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text text-muted small"><?= ucfirst($role) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <h2 class="text-primary fw-bold mb-4">Transaksi Peminjaman</h2>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if($role != 'kapus'): ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Anggota</label>
                        <select name="id_anggota" class="form-select" required>
                            <option value="">-- Pilih Anggota --</option>
                            <?php while($a = $anggota->fetch_assoc()): ?>
                                <option value="<?= $a['id_anggota'] ?>"><?= $a['nama'] ?> (<?= $a['nim'] ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Judul Buku</label>
                        <select name="id_buku" class="form-select" required>
                            <option value="">-- Pilih Buku --</option>
                            <?php 
                            $selected_book = isset($_GET['id_buku']) ? $_GET['id_buku'] : '';
                            while($b = $buku->fetch_assoc()): 
                                $selected = ($b['id_buku'] == $selected_book) ? 'selected' : '';
                            ?>
                                <option value="<?= $b['id_buku'] ?>" <?= $selected ?>><?= $b['judul'] ?> (Stok: <?= $b['stok'] ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Kembali (Rencana)</label>
                        <input type="date" name="tanggal_kembali" class="form-control" required>
                    </div>
                </div>
                <button type="submit" name="pinjam" class="btn btn-primary w-100">Proses Peminjaman</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Riwayat Peminjaman Terakhir</h4>
            <input type="text" id="searchHistory" class="form-control w-25" placeholder="Cari riwayat...">
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="historyTable">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $history = $conn->query("SELECT p.*, a.nama, b.judul FROM peminjaman p 
                                            JOIN anggota a ON p.id_anggota = a.id_anggota 
                                            JOIN buku b ON p.id_buku = b.id_buku 
                                            ORDER BY p.id_peminjaman DESC LIMIT 20");
                    $no = 1;
                    while($h = $history->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $h['nama'] ?></td>
                        <td><?= $h['judul'] ?></td>
                        <td><?= $h['tanggal_pinjam'] ?></td>
                        <td><?= $h['tanggal_kembali'] ?></td>
                        <td>
                            <?php if($h['status'] == 'dipinjam'): ?>
                                <span class="badge bg-warning text-dark">Dipinjam</span>
                            <?php else: ?>
                                <span class="badge bg-success">Kembali</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('searchHistory').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#historyTable tbody tr');
        
        rows.forEach(function(row) {
            let text = row.textContent.toLowerCase();
            if(text.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
  </script>
</body>
</html>
