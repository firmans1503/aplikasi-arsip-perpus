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
    $q = $conn->query("SELECT cover FROM buku WHERE id_buku=$id");
    $f = $q->fetch_assoc();
    if ($f['cover'] && file_exists("cover/".$f['cover'])) {
        unlink("cover/".$f['cover']);
    }
    
    $conn->query("DELETE FROM buku WHERE id_buku=$id");
    header("Location: buku.php");
}

// Handle Add/Edit (Admin Only)
if (isset($_POST['simpan']) && $role == 'admin') {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun'];
    $isbn = $_POST['isbn'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    
    $cover = "";
    if (!empty($_FILES['cover']['name'])) {
        $target_dir = "cover/";
        $cover = time() . "_" . basename($_FILES["cover"]["name"]);
        move_uploaded_file($_FILES["cover"]["tmp_name"], $target_dir . $cover);
    }

    if ($_POST['id_buku']) {
        // Edit
        $id = $_POST['id_buku'];
        if ($cover) {
            $sql = "UPDATE buku SET judul='$judul', penulis='$penulis', penerbit='$penerbit', tahun_terbit='$tahun', isbn='$isbn', kategori='$kategori', stok='$stok', cover='$cover' WHERE id_buku=$id";
        } else {
            $sql = "UPDATE buku SET judul='$judul', penulis='$penulis', penerbit='$penerbit', tahun_terbit='$tahun', isbn='$isbn', kategori='$kategori', stok='$stok' WHERE id_buku=$id";
        }
    } else {
        // Add
        $sql = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, isbn, kategori, stok, cover) VALUES ('$judul', '$penulis', '$penerbit', '$tahun', '$isbn', '$kategori', '$stok', '$cover')";
    }
    
    if ($conn->query($sql)) {
        header("Location: buku.php");
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
  <title>Daftar Buku - Perpustakaan SMKN 1 Palembang</title>
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
          <li class="nav-item"><a class="nav-link active" href="buku.php">Buku</a></li>
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

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Manajemen Buku</h2>
        <?php if($role == 'admin'): ?>
        <button class="btn btn-success" onclick="showForm()"><i class="bi bi-plus-circle me-2"></i>Tambah Buku</button>
        <?php endif; ?>
    </div>

    <?php if($role == 'admin'): ?>
    <!-- Form -->
    <div class="card mb-4" id="formCard" style="display: none;">
        <div class="card-header bg-success text-white">Form Buku</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_buku" id="id_buku">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Judul Buku</label>
                        <input type="text" name="judul" id="judul" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Penulis</label>
                        <input type="text" name="penulis" id="penulis" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Penerbit</label>
                        <input type="text" name="penerbit" id="penerbit" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Tahun Terbit</label>
                        <input type="number" name="tahun" id="tahun" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>ISBN</label>
                        <input type="text" name="isbn" id="isbn" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Kategori</label>
                        <input type="text" name="kategori" id="kategori" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Stok</label>
                        <input type="number" name="stok" id="stok" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Cover Buku</label>
                        <input type="file" name="cover" class="form-control">
                    </div>
                </div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <button type="button" onclick="hideForm()" class="btn btn-secondary">Batal</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="input-group shadow-sm">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari buku berdasarkan judul, penulis, atau kategori..." aria-label="Cari buku">
                <button class="btn btn-primary" type="button" id="searchBtn">Cari</button>
            </div>
        </div>
    </div>

    <div class="row">
      <?php
      $q = mysqli_query($conn, "SELECT * FROM buku ORDER BY id_buku DESC");
      while($d = mysqli_fetch_array($q)):
      ?>
      <div class="col-md-3 mb-4 book-item">
        <div class="card h-100 shadow-sm border-0">
          <img src="cover/<?= $d['cover'] ?>" class="card-img-top" alt="<?= $d['judul'] ?>" style="height: 300px; object-fit: cover;">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title text-truncate" title="<?= $d['judul'] ?>"><?= $d['judul'] ?></h5>
            <p class="card-text text-muted mb-1"><small><i class="bi bi-person me-1"></i><?= $d['penulis'] ?></small></p>
            <p class="card-text text-muted mb-1"><small><i class="bi bi-tag me-1"></i><?= $d['kategori'] ?></small></p>
            <div class="d-flex justify-content-between mb-3">
                <span class="badge bg-info text-dark">Stok: <?= $d['stok'] ?></span>
                <span class="badge bg-secondary"><?= $d['tahun_terbit'] ?></span>
            </div>
            
            <div class="mt-auto">
                <div class="d-flex gap-2 mb-2">
                    <a href="detail_buku.php?id=<?= $d['id_buku'] ?>" class="btn btn-primary btn-sm w-50">Detail</a>
                    <?php if($role != 'kapus'): ?>
                    <a href="transaksi_peminjaman.php?id_buku=<?= $d['id_buku'] ?>" class="btn btn-success btn-sm w-50">Pinjam</a>
                    <?php endif; ?>
                </div>
                <?php if($role == 'admin'): ?>
                <div class="d-flex gap-2">
                    <button onclick='edit(<?= json_encode($d) ?>)' class="btn btn-warning btn-sm w-50">Edit</button>
                    <a href="?delete=<?= $d['id_buku'] ?>" onclick="return confirm('Hapus buku ini?')" class="btn btn-danger btn-sm w-50">Hapus</a>
                </div>
                <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function showForm() {
        document.getElementById('formCard').style.display = 'block';
        document.getElementById('id_buku').value = '';
        document.querySelector('form').reset();
        window.scrollTo(0,0);
    }

    function hideForm() {
        document.getElementById('formCard').style.display = 'none';
    }

    function edit(data) {
        showForm();
        document.getElementById('id_buku').value = data.id_buku;
        document.getElementById('judul').value = data.judul;
        document.getElementById('penulis').value = data.penulis;
        document.getElementById('penerbit').value = data.penerbit;
        document.getElementById('tahun').value = data.tahun_terbit;
        document.getElementById('isbn').value = data.isbn;
        document.getElementById('kategori').value = data.kategori;
        document.getElementById('stok').value = data.stok;
    }

    // Simple Search Filter
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.book-item');
        
        items.forEach(function(item) {
            let text = item.textContent.toLowerCase();
            if(text.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
  </script>
</body>
</html>
