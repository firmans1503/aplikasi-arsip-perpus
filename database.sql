

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  PRIMARY KEY (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;








CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `penulis` varchar(255) NOT NULL,
  `penerbit` varchar(255) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_buku`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO buku VALUES("1","Pemrograman Web Modern","Eko Kurniawan","Informatika","2024","book1.jpg");
INSERT INTO buku VALUES("2","Algoritma dan Struktur Data","Rinaldi Munir","Informatika","2023","book2.jpg");
INSERT INTO buku VALUES("3","Database Design","Indra Yatini","Andi Offset","2022","book3.jpg");
INSERT INTO buku VALUES("4","Jaringan Komputer","Iwan Sofana","Informatika","2023","book4.jpg");
INSERT INTO buku VALUES("5","Kecerdasan Buatan","Suyanto","Informatika","2024","book5.jpg");



CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT,
  `id_buku` int(11) DEFAULT NULL,
  `id_anggota` int(11) DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  PRIMARY KEY (`id_peminjaman`),
  KEY `id_buku` (`id_buku`),
  KEY `id_anggota` (`id_anggota`),
  CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`),
  CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kapus','petugas') NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users (username, password, role) VALUES 
('admin', '123456', 'admin'),
('kapus', '123456', 'kapus'),
('petugas', '123456', 'petugas');



-- Updates for System Upgrade
ALTER TABLE `buku` 
ADD COLUMN `kategori` varchar(100) DEFAULT NULL,
ADD COLUMN `isbn` varchar(20) DEFAULT NULL,
ADD COLUMN `stok` int(11) DEFAULT 0;

ALTER TABLE `anggota`
ADD COLUMN `nim` varchar(20) DEFAULT NULL,
ADD COLUMN `alamat` text DEFAULT NULL,
ADD COLUMN `kontak` varchar(20) DEFAULT NULL,
ADD COLUMN `email` varchar(100) DEFAULT NULL;

ALTER TABLE `peminjaman`
ADD COLUMN `status` enum('dipinjam','kembali') DEFAULT 'dipinjam';

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT,
  `id_peminjaman` int(11) NOT NULL,
  `tanggal_dikembalikan` date NOT NULL,
  `denda` int(11) DEFAULT 0,
  PRIMARY KEY (`id_pengembalian`),
  KEY `id_peminjaman` (`id_peminjaman`),
  CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
