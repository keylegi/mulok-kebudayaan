<?php
include 'koneksi.php';
if (!isset($_GET['id'])) { header('Location: index.php'); exit; }
$id = (int)$_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT gambar FROM artikel WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);

if ($row) {
    if (!empty($row['gambar']) && file_exists('gambar/'.$row['gambar'])) {
        @unlink('gambar/'.$row['gambar']);
    }
    $stmt2 = mysqli_prepare($koneksi, "DELETE FROM artikel WHERE id = ?");
    mysqli_stmt_bind_param($stmt2, "i", $id);
    mysqli_stmt_execute($stmt2);
}

header('Location: index.php');
exit;
?>
