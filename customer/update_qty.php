<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$id_keranjang = $_GET['id'];
$aksi = $_GET['aksi'];

/* AMBIL DATA KERANJANG + PRODUK */

$query = mysqli_query($koneksi,"
SELECT keranjang.*, produk.harga
FROM keranjang
JOIN produk ON keranjang.id_produk = produk.id_produk
WHERE keranjang.id_keranjang='$id_keranjang'
AND keranjang.id_user='$id_user'
");

$data = mysqli_fetch_assoc($query);

$qty = $data['qty'];
$harga = $data['harga'];

/* TOMBOL TAMBAH */

if($aksi == 'tambah'){

    $qty++;

}

/* TOMBOL KURANG */

if($aksi == 'kurang'){

    if($qty > 1){

        $qty--;

    }

}

/* HITUNG SUBTOTAL BARU */

$subtotal = $harga * $qty;

/* UPDATE DATABASE */

mysqli_query($koneksi,"
UPDATE keranjang SET
qty='$qty',
subtotal='$subtotal'
WHERE id_keranjang='$id_keranjang'
");

/* KEMBALI */

header("Location: keranjang.php");
?>