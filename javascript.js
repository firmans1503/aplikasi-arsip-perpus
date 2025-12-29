// Contoh data buku
const book = {
  title: "Belajar Coding untuk Pemula",
  author: "Jane Doe",
  year: 2025,
  isbn: "9876543210",
  description: "Buku ini membahas dasar-dasar pemrograman dengan contoh yang mudah dipahami."
};

// Menampilkan data di halaman
document.getElementById('title').textContent = book.title;
document.getElementById('author').textContent = book.author;
document.getElementById('year').textContent = book.year;
document.getElementById('isbn').textContent = book.isbn;
document.getElementById('description').textContent = book.description;
