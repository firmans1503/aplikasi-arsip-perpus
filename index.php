<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$role = $_SESSION['role'];

// Ambil statistik
$buku = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc();
$anggota = $conn->query("SELECT COUNT(*) as total FROM anggota")->fetch_assoc();
$peminjaman = $conn->query("SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam'")->fetch_assoc();

// Ambil 8 buku terbaru
$daftar_buku = $conn->query("SELECT * FROM buku ORDER BY id_buku DESC LIMIT 8");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perpustakaan SMKN 1 Palembang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-book-half me-2"></i>Sistem Perpustakaan
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="buku.php">Buku</a></li>
          <li class="nav-item"><a class="nav-link" href="anggota.php">Anggota</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Transaksi</a>
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

  <!-- Welcome Notification -->
  <?php if(isset($_SESSION['welcome_shown']) && $_SESSION['welcome_shown'] === false): ?>
  <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1050">
    <div id="welcomeToast" class="toast toast-welcome align-items-center border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          Selamat datang, <?= $_SESSION['user'] ?>!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
  <?php $_SESSION['welcome_shown'] = true; endif; ?>

  <!-- Hero Section -->
  <section class="hero-section text-center">
    <div class="container hero-content">
      <h1 class="mb-3">Sistem Informasi Perpustakaan</h1>
      <p class="mb-4">Kelola buku, anggota, dan transaksi peminjaman dengan mudah.</p>
      
      <!-- Search Bar -->
      <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="input-group mb-3 shadow-lg">
                <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Cari buku..." aria-label="Cari buku">
                <button class="btn btn-warning fw-bold px-4" type="button" id="searchBtn">Cari</button>
            </div>
            <div id="searchResults" class="list-group text-start position-absolute w-50 start-50 translate-middle-x" style="z-index: 1000; display: none;">
                <!-- Results will appear here -->
            </div>
        </div>
      </div>

      <div class="mt-4">
        <a href="buku.php" class="btn btn-outline-light me-2"><i class="bi bi-book me-2"></i>Daftar Buku</a>
        <a href="transaksi_peminjaman.php" class="btn btn-outline-light"><i class="bi bi-arrow-left-right me-2"></i>Transaksi</a>
      </div>
    </div>
  </section>

  <script>
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', function() {
        const query = this.value;
        if (query.length > 2) {
            fetch(`search_handler.php?q=${query}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        searchResults.style.display = 'block';
                        data.forEach(item => {
                            const a = document.createElement('a');
                            a.href = `detail_buku.php?id=${item.id}`;
                            a.className = 'list-group-item list-group-item-action';
                            a.innerHTML = `
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">${item.judul}</h5>
                                    <small class="badge bg-primary">Buku</small>
                                </div>
                                <p class="mb-1 text-muted small">${item.info}</p>
                            `;
                            searchResults.appendChild(a);
                        });
                    } else {
                        searchResults.style.display = 'none';
                    }
                });
        } else {
            searchResults.style.display = 'none';
        }
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
  </script>

  <!-- Statistik -->
  <section class="stats-section">
    <div class="container">
      <div class="row text-center g-4 justify-content-center">
        <div class="col-md-4">
          <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-journal-album"></i></div>
            <div class="stat-number"><?= $buku['total'] ?></div>
            <div class="stat-label">Koleksi Buku</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-number"><?= $anggota['total'] ?></div>
            <div class="stat-label">Anggota Aktif</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
            <div class="stat-number"><?= $peminjaman['total'] ?></div>
            <div class="stat-label">Sedang Dipinjam</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features-section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">Kenapa Memilih Kami?</h2>
        <p class="text-muted">Fasilitas terbaik untuk mendukung kegiatan belajarmu</p>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon-wrapper">
              <i class="bi bi-collection"></i>
            </div>
            <h4>Koleksi Lengkap</h4>
            <p class="text-muted">Tersedia berbagai jenis buku pelajaran, novel, hingga jurnal ilmiah terbaru.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon-wrapper">
              <i class="bi bi-wifi"></i>
            </div>
            <h4>Akses Digital</h4>
            <p class="text-muted">Cari dan pesan buku secara online kapan saja dan di mana saja.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon-wrapper">
              <i class="bi bi-cup-hot"></i>
            </div>
            <h4>Ruang Nyaman</h4>
            <p class="text-muted">Suasana membaca yang tenang, sejuk, dan nyaman untuk belajar.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Daftar Buku -->
  <section id="koleksi" class="collection-section">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold text-primary mb-0">Koleksi Terbaru</h2>
        <a href="buku.php" class="btn btn-outline-primary rounded-pill px-4">Lihat Semua <i class="bi bi-arrow-right ms-2"></i></a>
      </div>
      <div class="row">
        <?php while($b = $daftar_buku->fetch_assoc()): ?>
        <div class="col-md-3 mb-4 book-item">
          <div class="card h-100">
            <img src="cover/<?= $b['cover'] ?>" class="card-img-top" alt="<?= $b['judul'] ?>">
            <div class="card-body">
              <h5 class="card-title text-truncate"><?= $b['judul'] ?></h5>
              <p class="card-text text-muted mb-2"><?= $b['penulis'] ?></p>
              <a href="detail_buku.php?id=<?= $b['id_buku'] ?>" class="btn btn-primary btn-sm w-100 mt-2">Detail</a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold mb-3"><i class="bi bi-book-half me-2"></i>Perpustakaan</h5>
          <p class="small text-white-50">Perpustakaan SMKN 1 Palembang berkomitmen untuk mencerdaskan kehidupan bangsa melalui literasi.</p>
        </div>
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold mb-3">Tautan Cepat</h5>
          <ul class="list-unstyled">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="buku.php">Koleksi Buku</a></li>
            <li><a href="anggota.php">Keanggotaan</a></li>
            <li><a href="tentang.php">Tentang Kami</a></li>
          </ul>
        </div>
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold mb-3">Hubungi Kami</h5>
          <ul class="list-unstyled small text-white-50">
            <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Jl. Moch. Sochib, Palembang</li>
            <li class="mb-2"><i class="bi bi-envelope me-2"></i>info@smkn1palembang.sch.id</li>
            <li class="mb-2"><i class="bi bi-telephone me-2"></i>(0711) 123456</li>
          </ul>
        </div>
      </div>
      <hr class="border-white-50">
      <div class="text-center small text-white-50">
        &copy; 2025 Perpustakaan SMKN 1 Palembang. All rights reserved.
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Auto hide welcome toast
    const welcomeToast = document.getElementById('welcomeToast');
    if (welcomeToast) {
        setTimeout(() => {
            const toast = new bootstrap.Toast(welcomeToast);
            toast.hide();
        }, 3000); // Hide after 3 seconds
    }
  </script>
</body>
</html>
