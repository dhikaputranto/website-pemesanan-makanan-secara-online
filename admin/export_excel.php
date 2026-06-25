
<?php
session_start();
include '../koneksi.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penjualan.xls");

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

<table border="1" cellpadding="10">

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
```
