<?php
include 'koneksi.php';

$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'all'; // all, buku, arsip

$results = [];

if ($keyword) {
    // Search Buku
    if ($type == 'all' || $type == 'buku') {
        $sql_buku = "SELECT id_buku as id, judul, penulis as info, 'Buku' as type FROM buku WHERE judul LIKE '%$keyword%' OR penulis LIKE '%$keyword%'";
        $q_buku = $conn->query($sql_buku);
        while ($row = $q_buku->fetch_assoc()) {
            $results[] = $row;
        }
    }

    // Search Arsip
    // Search Arsip - Removed
}

header('Content-Type: application/json');
echo json_encode($results);
?>
