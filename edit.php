<?php
include 'koneksi.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }

$stmt = mysqli_prepare($koneksi, "SELECT * FROM artikel WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$article = mysqli_fetch_assoc($res);
if (!$article) { header('Location: index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori = trim($_POST['kategori']);
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $gambar_name = $article['gambar'];

    // jika upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
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
            if (move_uploaded_file($file['tmp_name'], 'gambar/'.$newname)) {
                // hapus file lama jika ada
                if (!empty($gambar_name) && file_exists('gambar/'.$gambar_name)) {
                    @unlink('gambar/'.$gambar_name);
                }
                $gambar_name = $newname;
            } else {
                $errors[] = "Gagal menyimpan file gambar.";
            }
        }
    }

    if (empty($errors)) {
        $stmt2 = mysqli_prepare($koneksi, "UPDATE artikel SET kategori=?, nama=?, deskripsi=?, gambar=? WHERE id=?");
        mysqli_stmt_bind_param($stmt2, "ssssi", $kategori, $nama, $deskripsi, $gambar_name, $id);
        if (mysqli_stmt_execute($stmt2)) {
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Database error: ".mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Edit Artikel</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Edit Artikel</h1>
  <?php if (!empty($errors)) {
    echo '<div class="error"><ul>';
    foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>';
    echo '</ul></div>';
  } ?>
  <form method="post" enctype="multipart/form-data">
    <label>Kategori</label><br>
    <input type="text" name="kategori" value="<?php echo htmlspecialchars($article['kategori']); ?>" required><br><br>

    <label>Nama</label><br>
    <input type="text" name="nama" value="<?php echo htmlspecialchars($article['nama']); ?>" required><br><br>

    <label>Deskripsi</label><br>
    <textarea name="deskripsi" rows="6" required><?php echo htmlspecialchars($article['deskripsi']); ?></textarea><br><br>

    <label>Gambar saat ini</label><br>
    <img src="gambar/<?php echo htmlspecialchars($article['gambar']); ?>" alt="" style="width:200px;"><br><br>

    <label>Ganti Gambar (opsional)</label><br>
    <input type="file" name="gambar" accept="image/*"><br><br>

    <button type="submit">Update</button> | <a href="index.php">Batal</a>
  </form>
</body>
</html>
