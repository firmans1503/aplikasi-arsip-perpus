<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$role = $_SESSION['role'];

// Handle Delete (Admin Only)
if (isset($_GET['delete']) && $role == 'admin') {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM anggota WHERE id_anggota=$id");
    header("Location: anggota.php");
}

// Handle Add/Edit (Admin Only)
if (isset($_POST['simpan']) && $role == 'admin') {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $alamat = $_POST['alamat'];
    $kontak = $_POST['kontak'];
    $email = $_POST['email'];

    if ($_POST['id_anggota']) {
        // Edit
        $id = $_POST['id_anggota'];
        $sql = "UPDATE anggota SET nama='$nama', nim='$nim', alamat='$alamat', kontak='$kontak', email='$email' WHERE id_anggota=$id";
    } else {
        // Add
        $sql = "INSERT INTO anggota (nama, nim, alamat, kontak, email) VALUES ('$nama', '$nim', '$alamat', '$kontak', '$email')";
    }
    
    if ($conn->query($sql)) {
        header("Location: anggota.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Anggota - Perpustakaan SMKN 1 Palembang</title>
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
          <li class="nav-item"><a class="nav-link active" href="anggota.php">Anggota</a></li>
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

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Manajemen Anggota</h2>
        <?php if($role == 'admin'): ?>
        <button class="btn btn-success" onclick="showForm()"><i class="bi bi-plus-circle me-2"></i>Tambah Anggota</button>
        <?php endif; ?>
    </div>

    <?php if($role == 'admin'): ?>
    <!-- Form -->
    <div class="card mb-4" id="formCard" style="display: none;">
        <div class="card-header bg-success text-white">Form Anggota</div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="id_anggota" id="id_anggota">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>NIM / NIP</label>
                        <input type="text" name="nim" id="nim" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Kontak (HP)</label>
                        <input type="text" name="kontak" id="kontak" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control"></textarea>
                    </div>
                </div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <button type="button" onclick="hideForm()" class="btn btn-secondary">Batal</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <?php
        $q = mysqli_query($conn, "SELECT * FROM anggota ORDER BY id_anggota DESC");
        while($d = mysqli_fetch_array($q)):
        ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($d['nama']) ?>&background=random" class="rounded-circle" width="80" alt="Member">
                    </div>
                    <h5 class="card-title"><?= $d['nama'] ?></h5>
                    <p class="text-muted mb-1"><?= $d['nim'] ?></p>
                    <p class="card-text small text-muted mb-3"><?= $d['email'] ?> | <?= $d['kontak'] ?></p>
                    
                    <?php if($role == 'admin'): ?>
                    <div class="d-flex justify-content-center gap-2">
                        <button onclick='edit(<?= json_encode($d) ?>)' class="btn btn-warning btn-sm">Edit</button>
                        <a href="?delete=<?= $d['id_anggota'] ?>" onclick="return confirm('Hapus anggota ini?')" class="btn btn-danger btn-sm">Hapus</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
  </div>

  <footer class="bg-primary text-white text-center py-3 mt-auto">
    &copy; 2025 Perpustakaan SMKN 1 Palembang
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function showForm() {
        document.getElementById('formCard').style.display = 'block';
        document.getElementById('id_anggota').value = '';
        document.querySelector('form').reset();
        window.scrollTo(0,0);
    }

    function hideForm() {
        document.getElementById('formCard').style.display = 'none';
    }

    function edit(data) {
        showForm();
        document.getElementById('id_anggota').value = data.id_anggota;
        document.getElementById('nama').value = data.nama;
        document.getElementById('nim').value = data.nim;
        document.getElementById('kontak').value = data.kontak;
        document.getElementById('email').value = data.email;
        document.getElementById('alamat').value = data.alamat;
    }
  </script>
</body>
</html>
