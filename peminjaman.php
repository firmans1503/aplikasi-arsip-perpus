<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Peminjaman - Perpustakaan SMKN 1 Palembang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Navbar -->
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
          <li class="nav-item"><a class="nav-link active" href="peminjaman.php">Peminjaman</a></li>
          <li class="nav-item"><a class="nav-link" href="anggota.php">Anggota</a></li>
          <li class="nav-item"><a class="nav-link" href="tentang.php">Tentang</a></li>
          <li class="nav-item ms-2">
              <span class="text-white small me-2">Halo, <?= $_SESSION['user'] ?> (<?= ucfirst($_SESSION['role']) ?>)</span>
              <a href="logout.php" class="btn btn-danger btn-sm rounded-pill">Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Data Peminjaman</h2>
        <a href="index.php" class="btn btn-outline-secondary">
            &larr; Kembali ke Beranda
        </a>
    </div>
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-striped">
            <thead class="table-primary">
              <tr>
                <th>ID</th>
                <th>Nama Anggota</th>
                <th>Judul Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- Dummy Data -->
              <tr>
                <td>1</td>
                <td>Ahmad Rizki</td>
                <td>Pemrograman Web Modern</td>
                <td>2024-01-10</td>
                <td>2024-01-17</td>
                <td><span class="badge bg-success">Dikembalikan</span></td>
              </tr>
              <tr>
                <td>2</td>
                <td>Siti Aminah</td>
                <td>Algoritma dan Struktur Data</td>
                <td>2024-01-12</td>
                <td>2024-01-19</td>
                <td><span class="badge bg-warning text-dark">Dipinjam</span></td>
              </tr>
              <tr>
                <td>3</td>
                <td>Budi Santoso</td>
                <td>Database Design</td>
                <td>2024-01-15</td>
                <td>2024-01-22</td>
                <td><span class="badge bg-danger">Terlambat</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <footer class="bg-primary text-white text-center py-3 mt-auto">
    &copy; 2025 Perpustakaan SMKN 1 Palembang
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
