<?php
include '../koneksi.php';

$id = $_POST['id'];
$status = $_POST['status_pesanan'];

mysqli_query($koneksi, "
    UPDATE transaksi
    SET status_pesanan='$status'
    WHERE id_transaksi='$id'
");

header("Location: pesanan_admin.php");
?>