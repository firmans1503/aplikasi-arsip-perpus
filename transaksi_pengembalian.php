<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$role = $_SESSION['role'];

// Filter Logic
$filter_tgl = isset($_GET['tgl_kembali']) ? $_GET['tgl_kembali'] : '';

// Fetch Active Loans
$sql = "SELECT p.*, a.nama, b.judul, b.id_buku FROM peminjaman p 
        JOIN anggota a ON p.id_anggota = a.id_anggota 
        JOIN buku b ON p.id_buku = b.id_buku 
        WHERE p.status = 'dipinjam'";

if ($filter_tgl) {
    $sql .= " AND p.tanggal_kembali = '$filter_tgl'";
}

$sql .= " ORDER BY p.tanggal_kembali ASC";
$loans = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Peminjaman - Perpustakaan</title>
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
    <h2 class="text-primary fw-bold mb-4">Daftar Peminjaman Aktif</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="col-form-label fw-bold">Filter Tanggal Kembali:</label>
                </div>
                <div class="col-auto">
                    <input type="date" name="tgl_kembali" class="form-control" value="<?= $filter_tgl ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <?php if($filter_tgl): ?>
                        <a href="transaksi_pengembalian.php" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </div>
                <div class="col-auto ms-auto">
                     <input type="text" id="searchReturn" class="form-control" placeholder="Cari nama/judul...">
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="returnTable">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali (Rencana)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = $loans->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['judul'] ?></td>
                            <td><?= $row['tanggal_pinjam'] ?></td>
                            <td><?= $row['tanggal_kembali'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('searchReturn').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#returnTable tbody tr');
        
        rows.forEach(function(row) {
            let member = row.cells[1].textContent.toLowerCase();
            let book = row.cells[2].textContent.toLowerCase();
            if(member.includes(filter) || book.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
  </script>
</body>
</html>
