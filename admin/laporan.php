<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

$where = "";

$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';

if($tgl_awal != '' && $tgl_akhir != ''){

    $where = "WHERE DATE(t.waktu_pesanan)
              BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$total_produk = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT COUNT(*) as total FROM produk")
);

$total_customer = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT COUNT(*) as total FROM users WHERE role='customer'")
);

$total_pesanan = mysqli_fetch_assoc(
    mysqli_query($koneksi,"SELECT COUNT(*) as total FROM transaksi")
);

$total_pendapatan = mysqli_fetch_assoc(
    mysqli_query($koneksi,"
    SELECT SUM(total_harga) as total
    FROM transaksi
    ")
);

$query = mysqli_query($koneksi,"
SELECT
t.*,
u.nama_lengkap
FROM transaksi t
LEFT JOIN users u
ON t.id_user = u.id_user
$where
ORDER BY t.id_transaksi DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Laporan Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/laporan.css" rel="stylesheet">

</head>

<body>

<div class="wrapper">

<div class="sidebar">

    <div class="logo">🍔 Dim's Admin</div>

    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="produk.php">Produk</a></li>
        <li><a href="pesanan_admin.php">Pesanan</a></li>
        <li><a href="users.php" >Staff</a></li>
        <li><a href="users_customer.php">Customer</a></li>
        <li><a href="laporan.php" class="active">Laporan</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>

</div>

<div class="content">

<div class="header">

    <div>
        <h2>Laporan Admin</h2>
        <p>Monitoring transaksi & pendapatan</p>
    </div>

    <form method="GET" class="filter-box">

        <input type="date"
        name="tgl_awal"
        value="<?= $tgl_awal ?>">

        <input type="date"
        name="tgl_akhir"
        value="<?= $tgl_akhir ?>">

        <button type="submit">
            Filter
        </button>

    </form>

</div>

<div class="export-box">

    <a href="export_excel.php"
    class="btn-export excel">
        Export Excel
    </a>

    <a href="export_pdf.php"
    target="_blank"
    class="btn-export pdf">
        Export PDF
    </a>

</div>

<div class="cards">

    <div class="card-box">
        <h5>Total Produk</h5>
        <h2><?= $total_produk['total']; ?></h2>
    </div>

    <div class="card-box">
        <h5>Total Customer</h5>
        <h2><?= $total_customer['total']; ?></h2>
    </div>

    <div class="card-box">
        <h5>Total Pesanan</h5>
        <h2><?= $total_pesanan['total']; ?></h2>
    </div>

    <div class="card-box">
        <h5>Total Pendapatan</h5>
        <h2>
            Rp
            <?= number_format($total_pendapatan['total'],0,',','.'); ?>
        </h2>
    </div>

</div>

<div class="table-box">

<table class="table align-middle">

<thead>

<tr>
<th>No</th>
<th>ID Transaksi</th>
<th>Customer</th>
<th>Total</th>
<th>Status</th>
<th>Tanggal</th>
</tr>

</thead>

<tbody>

<?php
$no = 1;

while($data = mysqli_fetch_assoc($query)){
?>

<tr>

<td><?= $no++; ?></td>

<td>#<?= $data['id_transaksi']; ?></td>

<td><?= $data['nama_lengkap']; ?></td>

<td>
Rp <?= number_format($data['total_harga'],0,',','.'); ?>
</td>

<td>
<span class="status <?= $data['status_pesanan']; ?>">
<?= ucfirst($data['status_pesanan']); ?>
</span>
</td>

<td>
<?= date('d M Y H:i', strtotime($data['waktu_pesanan'])); ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>
</div>

</body>
</html>
```
