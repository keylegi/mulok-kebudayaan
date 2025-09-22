<?php
include 'koneksi.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori = trim($_POST['kategori']);
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);

    // validasi sederhana
    if ($kategori === '' || $nama === '' || $deskripsi === '') {
        $errors[] = "Semua field harus diisi.";
    }

    // upload gambar
    $newname = '';
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Pilih file gambar.";
    } else {
        $file = $_FILES['gambar'];
        $allowed = ['image/jpeg','image/png','image/gif'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Upload gagal.";
        } elseif (!in_array($file['type'], $allowed)) {
            $errors[] = "Tipe file harus JPG/PNG/GIF.";
        } elseif ($file['size'] > 2*1024*1024) {
            $errors[] = "Ukuran maksimum 2MB.";
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newname = uniqid('img_') . '.' . $ext;
            if (!move_uploaded_file($file['tmp_name'], 'gambar/'.$newname)) {
                $errors[] = "Gagal menyimpan file gambar.";
            }
        }
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO artikel (kategori, nama, deskripsi, gambar) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $kategori, $nama, $deskripsi, $newname);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Database error: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Tambah Artikel</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Tambah Artikel</h1>
  <?php if (!empty($errors)) {
    echo '<div class="error"><ul>';
    foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>';
    echo '</ul></div>';
  } ?>
  <form method="post" enctype="multipart/form-data">
    <label>Kategori</label><br>
    <input type="text" name="kategori" required><br><br>

    <label>Nama</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Deskripsi</label><br>
    <textarea name="deskripsi" rows="6" required></textarea><br><br>

    <label>Gambar (jpg/png/gif, max 2MB)</label><br>
    <input type="file" name="gambar" accept="image/*" required><br><br>

    <button type="submit">Simpan</button> | <a href="index.php">Batal</a>
  </form>
</body>
</html>
