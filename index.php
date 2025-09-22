<?php
include 'koneksi.php';
$result = mysqli_query($koneksi, "SELECT * FROM artikel ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Artikel Kebudayaan Sulawesi Utara</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Kebudayaan Sulawesi Utara</h1>
  <p style="text-align:center;"><a href="tambah.php">+ Tambah Artikel</a></p>

  <div class="container">
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
      <div class="card">
        <img src="gambar/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>">
        <h2><?php echo htmlspecialchars($row['nama']); ?></h2>
        <p><b>Kategori:</b> <?php echo htmlspecialchars($row['kategori']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
        <p>
          <a href="detail.php?id=<?php echo $row['id']; ?>">Lihat</a> |
          <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
          <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin mau dihapus?');">Hapus</a>
        </p>
      </div>
    <?php } ?>
  </div>
</body>
</html>
