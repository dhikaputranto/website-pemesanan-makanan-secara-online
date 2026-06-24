
<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

$id_user   = $_SESSION['id_user'];
$id_produk = $_GET['id'];

/* AMBIL DATA PRODUK */

$produk = mysqli_query($koneksi, "
SELECT * FROM produk
WHERE id_produk='$id_produk'
");

$data_produk = mysqli_fetch_assoc($produk);

$harga = $data_produk['harga'];

/* CEK PRODUK DI KERANJANG */

$cek = mysqli_query($koneksi, "
SELECT * FROM keranjang
WHERE id_user='$id_user'
AND id_produk='$id_produk'
AND status='belum'
");

/* JIKA SUDAH ADA */

if(mysqli_num_rows($cek) > 0){

    $data = mysqli_fetch_assoc($cek);

    $qty_baru = $data['qty'] + 1;

    $subtotal = $qty_baru * $harga;

    mysqli_query($koneksi, "
    UPDATE keranjang SET
    qty='$qty_baru',
    subtotal='$subtotal'
    WHERE id_keranjang='".$data['id_keranjang']."'
    ");

}else{

    mysqli_query($koneksi, "
    INSERT INTO keranjang(
        id_user,
        id_produk,
        qty,
        subtotal,
        status
    ) VALUES (
        '$id_user',
        '$id_produk',
        '1',
        '$harga',
        'belum'
    )
    ");

}

/* REDIRECT */

header("Location: index.php?success=1");
exit;
?>

