<?php
include '../koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($koneksi, "
    SELECT dt.*, p.nama_produk
    FROM detail_transaksi dt
    JOIN produk p ON dt.id_produk = p.id_produk
    WHERE dt.id_transaksi = '$id'
");
?>

<h2>Detail Pesanan</h2>

<?php while($row = mysqli_fetch_assoc($query)) { ?>
    <p>
        <?= $row['nama_produk'] ?> |
        Qty: <?= $row['qty'] ?> |
        Rp <?= number_format($row['subtotal']) ?>
    </p>
<?php } ?>