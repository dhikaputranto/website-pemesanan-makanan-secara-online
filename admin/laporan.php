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

    $where = "WHERE DATE(r.created_at)
              BETWEEN '$tgl_awal'
              AND '$tgl_akhir'";
}

/* TOTAL PRODUK */

$total_produk = mysqli_fetch_assoc(
    mysqli_query($koneksi,"
    SELECT COUNT(*) as total
    FROM produk
    ")
);

/* TOTAL CUSTOMER */

$total_customer = mysqli_fetch_assoc(
    mysqli_query($koneksi,"
    SELECT COUNT(*) as total
    FROM users
    WHERE role='customer'
    ")
);

/* TOTAL PESANAN */

$total_pesanan = mysqli_fetch_assoc(
    mysqli_query($koneksi,"
    SELECT COUNT(*) as total
    FROM riwayat_pesanan
    ")
);

/* TOTAL PENDAPATAN */

$total_pendapatan = mysqli_fetch_assoc(
    mysqli_query($koneksi,"
    SELECT SUM(total_harga) as total
    FROM riwayat_pesanan
    WHERE payment_status='paid'
    ")
);

/* DATA LAPORAN */

$query = mysqli_query($koneksi,"
SELECT
    r.*,
    u.nama_lengkap
FROM riwayat_pesanan r
LEFT JOIN users u
ON r.id_user = u.id_user
$where
ORDER BY r.id_riwayat DESC
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

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        🍔 Dim's Admin
    </div>

    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="produk.php">Produk</a></li>
        <li><a href="pesanan_admin.php">Pesanan</a></li>
        <li><a href="users.php">Staff</a></li>
        <li><a href="users_customer.php">Customer</a></li>
        <li><a href="laporan.php" class="active">Laporan</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>

</div>

<!-- CONTENT -->

<div class="content">

<div class="header">

    <div>
        <h2>Laporan Admin</h2>
        <p>Monitoring transaksi dan pendapatan</p>
    </div>

    <form method="GET" class="filter-box">

        <input
        type="date"
        name="tgl_awal"
        value="<?= $tgl_awal ?>">

        <input
        type="date"
        name="tgl_akhir"
        value="<?= $tgl_akhir ?>">

        <button type="submit">
            Filter
        </button>

    </form>

</div>

<!-- EXPORT -->

<div class="export-box">

    <a href="export_excel.php" class="btn-export excel">
        Export Excel
    </a>

    <a href="export_pdf.php" target="_blank" class="btn-export pdf">
        Export PDF
    </a>

</div>

<!-- CARD -->

<div class="cards">

    <div class="card-box">

        <h5>Total Produk</h5>

        <h2>
            <?= $total_produk['total']; ?>
        </h2>

    </div>

    <div class="card-box">

        <h5>Total Customer</h5>

        <h2>
            <?= $total_customer['total']; ?>
        </h2>

    </div>

    <div class="card-box">

        <h5>Total Pesanan</h5>

        <h2>
            <?= $total_pesanan['total']; ?>
        </h2>

    </div>

    <div class="card-box">

        <h5>Total Pendapatan</h5>

        <h2>
            Rp <?= number_format($total_pendapatan['total'] ?? 0,0,',','.'); ?>
        </h2>

    </div>

</div>

<!-- TABLE -->

<div class="table-box">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>No</th>
<th>No Pesanan</th>
<th>Customer</th>
<th>Total</th>
<th>Metode</th>
<th>Status Pesanan</th>
<th>Status Bayar</th>
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

<td>
    <?= $data['nomor_pesanan']; ?>
</td>

<td>
    <?= $data['nama_lengkap']; ?>
</td>

<td>
    Rp <?= number_format($data['total_harga'],0,',','.'); ?>
</td>

<td>
    <?= strtoupper($data['metode_pembayaran']); ?>
</td>

<td>

<?php if($data['status_pesanan']=='selesai'){ ?>

<span class="badge bg-success">
    Selesai
</span>

<?php }elseif($data['status_pesanan']=='dimasak'){ ?>

<span class="badge bg-info">
    Dimasak
</span>

<?php }elseif($data['status_pesanan']=='diproses'){ ?>

<span class="badge bg-primary">
    Diproses
</span>

<?php }else{ ?>

<span class="badge bg-warning text-dark">
    Pending
</span>

<?php } ?>

</td>

<td>

<?php if($data['payment_status']=='paid'){ ?>

<span class="badge bg-success">
    Lunas
</span>

<?php }else{ ?>

<span class="badge bg-danger">
    Belum Lunas
</span>

<?php } ?>

</td>

<td>
    <?= date('d M Y H:i', strtotime($data['created_at'])); ?>
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