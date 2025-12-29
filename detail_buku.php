<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$id = $_GET['id'];
$query = $conn->query("SELECT * FROM buku WHERE id_buku = '$id'");
$buku = $query->fetch_assoc();

if (!$buku) {
    echo "<script>alert('Buku tidak ditemukan!');window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Buku - <?= $buku['judul'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-book-half me-2"></i>Perpustakaan
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
  <div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-4 bg-light d-flex align-items-center justify-content-center p-3">
                        <img src="cover/<?= $buku['cover'] ?>" class="img-fluid shadow-sm rounded" style="max-height: 500px; object-fit: contain;" alt="<?= $buku['judul'] ?>">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body p-5">
                            <h2 class="fw-bold text-primary mb-3"><?= $buku['judul'] ?></h2>
                            <hr>
                            <div class="mb-3">
                                <h5 class="fw-bold text-muted">Penulis</h5>
                                <p class="lead"><?= $buku['penulis'] ?></p>
                            </div>
                            <div class="mb-3">
                                <h5 class="fw-bold text-muted">Penerbit</h5>
                                <p class="lead"><?= $buku['penerbit'] ?></p>
                            </div>
                            <div class="mb-4">
                                <h5 class="fw-bold text-muted">Tahun Terbit</h5>
                                <p class="lead"><?= $buku['tahun_terbit'] ?></p>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="buku.php" class="btn btn-secondary px-4">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <?php if($_SESSION['role'] != 'kapus'): ?>
                                <a href="transaksi_peminjaman.php?id_buku=<?= $buku['id_buku'] ?>" class="btn btn-success px-4">
                                    <i class="bi bi-bookmark-plus me-2"></i>Pinjam Buku
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
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
