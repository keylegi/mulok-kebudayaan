<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

if (!isset($_GET['id'])) {
    die("ID artikel tidak ditemukan.");
}

$id = intval($_GET['id']); // biar aman

// ambil data artikel berdasarkan id
$stmt = mysqli_prepare($koneksi, "SELECT kategori, nama, deskripsi, gambar FROM artikel WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    die("Artikel tidak ditemukan.");
}

$artikel = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Artikel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($artikel['nama']); ?></h1>
    <p><b>Kategori:</b> <?php echo htmlspecialchars($artikel['kategori']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($artikel['deskripsi'])); ?></p>

    <?php if (!empty($artikel['gambar'])): ?>
        <img src="gambar/<?php echo htmlspecialchars($artikel['gambar']); ?>" 
             alt="<?php echo htmlspecialchars($artikel['nama']); ?>" 
             style="max-width:400px;">
    <?php endif; ?>

    <p><a href="index.php">← Kembali</a></p>
</body>
</html>
