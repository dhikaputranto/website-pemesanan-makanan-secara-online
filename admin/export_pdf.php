
<?php
session_start();
include '../koneksi.php';

$query = mysqli_query($koneksi,"
SELECT
t.*,
u.nama_lengkap
FROM transaksi t
LEFT JOIN users u
ON t.id_user = u.id_user
ORDER BY t.id_transaksi DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>Export PDF</title>

<style>

body{
    font-family:Arial;
    padding:20px;
}

h2{
    text-align:center;
    margin-bottom:25px;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    border:1px solid #000;
    padding:10px;
    font-size:13px;
}

table th{
    background:#eee;
}

</style>

</head>

<body onload="window.print()">

<h2>Laporan Penjualan</h2>

<table>

<tr>
    <th>No</th>
    <th>ID Transaksi</th>
    <th>Customer</th>
    <th>Total</th>
    <th>Status</th>
    <th>Tanggal</th>
</tr>

<?php
$no = 1;

while($data = mysqli_fetch_assoc($query)){
?>

<tr>

<td><?= $no++; ?></td>

<td>#<?= $data['id_transaksi']; ?></td>

<td><?= $data['nama_lengkap']; ?></td>

<td>Rp <?= number_format($data['total_harga']); ?></td>

<td><?= $data['status_pesanan']; ?></td>

<td><?= $data['waktu_pesanan']; ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>
```
