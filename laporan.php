<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$role = $_SESSION['role'];

// Filter Logic
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'semua';
$filter_nama = isset($_GET['nama_anggota']) ? $_GET['nama_anggota'] : '';

// Fetch Reports
$buku = $conn->query("SELECT * FROM buku ORDER BY judul ASC");
$anggota = $conn->query("SELECT * FROM anggota ORDER BY nama ASC"); // Keep original for Anggota Tab

// Transaction Query with Filter
$sql_transaksi = "SELECT p.*, a.nama, b.judul FROM peminjaman p 
                  JOIN anggota a ON p.id_anggota = a.id_anggota 
                  JOIN buku b ON p.id_buku = b.id_buku 
                  WHERE p.tanggal_pinjam BETWEEN '$tgl_awal' AND '$tgl_akhir'";

if ($filter_status != 'semua') {
    $sql_transaksi .= " AND p.status = '$filter_status'";
}

if (!empty($filter_nama)) {
    $sql_transaksi .= " AND a.nama LIKE '%$filter_nama%'";
}

$sql_transaksi .= " ORDER BY p.tanggal_pinjam DESC";
$transaksi = $conn->query($sql_transaksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan - Perpustakaan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css">
  <style>
      @media print {
          .no-print { display: none !important; }
          .card { border: none; shadow: none; }
      }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm no-print">
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
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Transaksi</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="transaksi_peminjaman.php">Peminjaman</a></li>
                <li><a class="dropdown-item" href="transaksi_pengembalian.php">Pengembalian</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link active" href="laporan.php">Laporan</a></li>
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
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2 class="text-primary fw-bold">Laporan Perpustakaan</h2>
        <button onclick="window.print()" class="btn btn-secondary"><i class="bi bi-printer me-2"></i>Cetak Laporan</button>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4 no-print" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi-pane" type="button" role="tab">Laporan Transaksi</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="buku-tab" data-bs-toggle="tab" data-bs-target="#buku-pane" type="button" role="tab">Laporan Buku</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="anggota-tab" data-bs-toggle="tab" data-bs-target="#anggota-pane" type="button" role="tab">Laporan Anggota</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        
        <!-- Transaksi -->
        <div class="tab-pane fade show active" id="transaksi-pane" role="tabpanel">
            <div class="card mb-4 no-print">
                <div class="card-body bg-light">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="semua" <?= $filter_status == 'semua' ? 'selected' : '' ?>>Semua</option>
                                <option value="dipinjam" <?= $filter_status == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                                <option value="kembali" <?= $filter_status == 'kembali' ? 'selected' : '' ?>>Kembali</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Anggota</label>
                            <input type="text" name="nama_anggota" class="form-control" placeholder="Nama..." value="<?= $filter_nama ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <h4 class="mb-3">Laporan Transaksi Peminjaman</h4>
            <p class="mb-3">Periode: <?= date('d-m-Y', strtotime($tgl_awal)) ?> s/d <?= date('d-m-Y', strtotime($tgl_akhir)) ?></p>
            
            <table class="table table-bordered table-striped">
                <thead>
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
                    $no=1; 
                    if($transaksi->num_rows > 0):
                        while($r = $transaksi->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $r['nama'] ?></td>
                        <td><?= $r['judul'] ?></td>
                        <td><?= $r['tanggal_pinjam'] ?></td>
                        <td><?= $r['tanggal_kembali'] ?></td>
                        <td><?= ucfirst($r['status']) ?></td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data transaksi pada periode ini.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Buku -->
        <div class="tab-pane fade" id="buku-pane" role="tabpanel">
            <h4 class="mb-3">Data Koleksi Buku</h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($r = $buku->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $r['judul'] ?></td>
                        <td><?= $r['penulis'] ?></td>
                        <td><?= $r['penerbit'] ?></td>
                        <td><?= $r['tahun_terbit'] ?></td>
                        <td><?= $r['stok'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Anggota -->
        <div class="tab-pane fade" id="anggota-pane" role="tabpanel">
            <h4 class="mb-3">Data Anggota Perpustakaan</h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM/NIP</th>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($r = $anggota->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $r['nim'] ?></td>
                        <td><?= $r['nama'] ?></td>
                        <td><?= $r['kontak'] ?></td>
                        <td><?= $r['email'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
