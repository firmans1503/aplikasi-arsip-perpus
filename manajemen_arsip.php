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
    // Get file path to delete file
    $q = $conn->query("SELECT file_path FROM arsip WHERE id_arsip=$id");
    $f = $q->fetch_assoc();
    if ($f['file_path'] && file_exists($f['file_path'])) {
        unlink($f['file_path']);
    }
    
    $conn->query("DELETE FROM arsip WHERE id_arsip=$id");
    header("Location: manajemen_arsip.php");
}

// Handle Add/Edit (Admin Only)
if (isset($_POST['simpan']) && $role == 'admin') {
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $tanggal = $_POST['tanggal'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];
    
    // File Upload Handling
    $file_path = "";
    if (!empty($_FILES['file_arsip']['name'])) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["file_arsip"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["file_arsip"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        }
    }

    if ($_POST['id_arsip']) {
        // Edit
        $id = $_POST['id_arsip'];
        if ($file_path) {
            $sql = "UPDATE arsip SET judul='$judul', kategori='$kategori', tanggal_arsip='$tanggal', lokasi_fisik='$lokasi', deskripsi='$deskripsi', file_path='$file_path' WHERE id_arsip=$id";
        } else {
            $sql = "UPDATE arsip SET judul='$judul', kategori='$kategori', tanggal_arsip='$tanggal', lokasi_fisik='$lokasi', deskripsi='$deskripsi' WHERE id_arsip=$id";
        }
    } else {
        // Add
        $sql = "INSERT INTO arsip (judul, kategori, tanggal_arsip, lokasi_fisik, deskripsi, file_path) VALUES ('$judul', '$kategori', '$tanggal', '$lokasi', '$deskripsi', '$file_path')";
    }
    
    if ($conn->query($sql)) {
        header("Location: manajemen_arsip.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch Data
$arsip = $conn->query("SELECT * FROM arsip ORDER BY id_arsip DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Arsip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Sistem Pengarsipan</a>
            <div>
                <span class="text-white me-3">Login sebagai: <?= ucfirst($role) ?></span>
                <a href="index.php" class="btn btn-outline-light btn-sm">Kembali ke Beranda</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Kelola Arsip Perusahaan</h2>
        
        <?php if($role == 'admin'): ?>
        <!-- Form (Admin Only) -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Form Arsip</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_arsip" id="id_arsip">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Judul Arsip</label>
                            <input type="text" name="judul" id="judul" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Kategori</label>
                            <select name="kategori" id="kategori" class="form-control" required>
                                <option value="Laporan">Laporan</option>
                                <option value="Surat">Surat</option>
                                <option value="Dokumentasi">Dokumentasi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Arsip</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Lokasi Fisik</label>
                            <input type="text" name="lokasi" id="lokasi" class="form-control" placeholder="Contoh: Lemari A, Rak 2">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Upload File (PDF/Doc/Img)</label>
                            <input type="file" name="file_arsip" class="form-control">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah file saat edit.</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <button type="button" onclick="resetForm()" class="btn btn-secondary">Batal</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>File</th>
                            <?php if($role == 'admin'): ?>
                            <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row = $arsip->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold"><?= $row['judul'] ?></div>
                                <small class="text-muted"><?= $row['deskripsi'] ?></small>
                            </td>
                            <td><span class="badge bg-info"><?= $row['kategori'] ?></span></td>
                            <td><?= $row['tanggal_arsip'] ?></td>
                            <td><?= $row['lokasi_fisik'] ?></td>
                            <td>
                                <?php if($row['file_path']): ?>
                                    <a href="<?= $row['file_path'] ?>" class="btn btn-success btn-sm" download>
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <?php if($role == 'admin'): ?>
                            <td>
                                <button onclick='edit(<?= json_encode($row) ?>)' class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></button>
                                <a href="?delete=<?= $row['id_arsip'] ?>" onclick="return confirm('Hapus arsip ini?')" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function edit(data) {
            document.getElementById('id_arsip').value = data.id_arsip;
            document.getElementById('judul').value = data.judul;
            document.getElementById('kategori').value = data.kategori;
            document.getElementById('tanggal').value = data.tanggal_arsip;
            document.getElementById('lokasi').value = data.lokasi_fisik;
            document.getElementById('deskripsi').value = data.deskripsi;
            window.scrollTo(0,0);
        }

        function resetForm() {
            document.getElementById('id_arsip').value = '';
            document.querySelector('form').reset();
        }
    </script>
</body>
</html>
