<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tentang Kami - Perpustakaan SMKN 1 Palembang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">Perpustakaan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="buku.php">Buku</a></li>
          <li class="nav-item"><a class="nav-link" href="peminjaman.php">Peminjaman</a></li>
          <li class="nav-item"><a class="nav-link" href="anggota.php">Anggota</a></li>
          <li class="nav-item"><a class="nav-link active" href="tentang.php">Tentang</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="text-primary fw-bold mb-4">Tentang Perpustakaan</h2>
            <p class="lead text-muted">Perpustakaan SMKN 1 Palembang adalah pusat sumber belajar yang menyediakan berbagai koleksi buku, jurnal, dan referensi digital untuk mendukung kegiatan belajar mengajar.</p>
            <p>Kami berkomitmen untuk meningkatkan minat baca siswa dan menyediakan akses informasi yang mudah dan cepat. Dengan fasilitas yang nyaman dan koleksi yang terus diperbarui, kami berharap dapat menjadi tempat favorit bagi siswa untuk belajar dan berkarya.</p>
            <div class="mt-4">
                <a href="index.php" class="btn btn-primary me-2">Jelajahi Koleksi</a>
                <a href="index.php" class="btn btn-outline-secondary">Kembali ke Beranda</a>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <img src="https://img.freepik.com/free-vector/library-interior-empty-room-reading-with-books-wooden-shelves_107791-1555.jpg" class="img-fluid rounded shadow-lg" alt="Library Illustration">
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-4 text-center">
            <div class="p-4 bg-light rounded shadow-sm h-100">
                <h4 class="fw-bold">Visi</h4>
                <p>Menjadi pusat informasi dan literasi yang unggul dan berbasis teknologi.</p>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="p-4 bg-light rounded shadow-sm h-100">
                <h4 class="fw-bold">Misi</h4>
                <p>Menyediakan layanan prima, koleksi lengkap, dan fasilitas yang mendukung pembelajaran.</p>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="p-4 bg-light rounded shadow-sm h-100">
                <h4 class="fw-bold">Layanan</h4>
                <p>Peminjaman buku, ruang baca, akses internet, dan referensi digital.</p>
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
