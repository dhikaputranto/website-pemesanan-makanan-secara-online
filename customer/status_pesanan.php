<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/* =========================
   DATA PESANAN TERAKHIR
========================= */

$query = mysqli_query($koneksi,"
SELECT *
FROM transaksi
WHERE id_user = '$id_user'
ORDER BY waktu_pesanan DESC
LIMIT 1
");

if(!$query){
    die(mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);

/* =========================
   DETAIL PESANAN
========================= */

$detail = [];

if($data){

    $query_detail = mysqli_query($koneksi,"
    SELECT detail_transaksi.*, produk.nama_produk, produk.gambar
    FROM detail_transaksi
    JOIN produk ON detail_transaksi.id_produk = produk.id_produk
    WHERE detail_transaksi.id_transaksi='".$data['id_transaksi']."'
    ");

    while($row = mysqli_fetch_assoc($query_detail)){
        $detail[] = $row;
    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Status Pesanan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/status_pesanan.css">

</head>
<body>

<div class="container-custom">

<?php if($data){ ?>

<div class="card-status">

    <div class="judul">
        <h2>🍔 Status Pesanan</h2>
        <p>Pantau perkembangan pesanan Anda secara realtime</p>
    </div>

    <div class="nomor">
        Nomor Pesanan :
        <span><?= $data['nomor_pesanan']; ?></span>
    </div>

    <!-- TRACKING -->

    <div class="tracking">

        <div class="step active">
            <div class="circle">1</div>
            <small>Pending</small>
        </div>

        <div class="line"></div>

        <div class="step <?= in_array($data['status_pesanan'],['diproses','dimasak','selesai']) ? 'active':'' ?>">
            <div class="circle">2</div>
            <small>Diproses</small>
        </div>

        <div class="line"></div>

        <div class="step <?= in_array($data['status_pesanan'],['dimasak','selesai']) ? 'active':'' ?>">
            <div class="circle">3</div>
            <small>Dimasak</small>
        </div>

        <div class="line"></div>

        <div class="step <?= $data['status_pesanan']=='selesai' ? 'active':'' ?>">
            <div class="circle">4</div>
            <small>Selesai</small>
        </div>

    </div>

    <div class="status-box">

        <div class="box">
            <h6>Status Pembayaran</h6>

            <?php if($data['payment_status']=='paid'){ ?>
                <h4 class="paid">✅ Lunas</h4>
            <?php } else { ?>
                <h4 class="unpaid">❌ Belum Lunas</h4>
            <?php } ?>

        </div>

        <div class="box">
            <h6>Status Pesanan</h6>

            <?php if($data['status_pesanan']=='pending'){ ?>
                <h4 class="pending">🕒 Menunggu Konfirmasi</h4>
            <?php } elseif($data['status_pesanan']=='diproses'){ ?>
                <h4 class="proses">📦 Sedang Diproses</h4>
            <?php } elseif($data['status_pesanan']=='dimasak'){ ?>
                <h4 class="proses">👨‍🍳 Sedang Dimasak</h4>
            <?php } else { ?>
                <h4 class="selesai">🎉 Pesanan Selesai</h4>
            <?php } ?>

        </div>

    </div>

    <div class="info-box">

        <div class="info-item">
            <strong>Waktu Pesanan</strong>
            <p><?= date('d M Y H:i', strtotime($data['waktu_pesanan'])); ?></p>
        </div>

        <div class="info-item">
            <strong>Metode Pembayaran</strong>
            <p><?= strtoupper($data['metode_pembayaran']); ?></p>
        </div>

    </div>

    <div class="total">
        <p>Total Pembayaran</p>
        <h3>Rp <?= number_format($data['total_harga']); ?></h3>
    </div>

    <?php if(!empty($data['catatan'])){ ?>

    <div class="catatan">
        <strong>📝 Catatan Pesanan</strong><br>
        <?= $data['catatan']; ?>
    </div>

    <?php } ?>

    <a href="status_pesanan.php" class="btn-kembali">
        🔄 Refresh Status
    </a>

    <a href="index.php" class="btn-kembali">
        🏠 Kembali ke Beranda
    </a>

</div>

<?php }else{ ?>

<div class="card-status empty">

    <h3>Belum Ada Pesanan</h3>

    <p>
        Silakan lakukan pemesanan terlebih dahulu.
    </p>

    <a href="index.php" class="btn-kembali">
        Pesan Sekarang
    </a>

</div>

<?php } ?>

</div>

</body>
</html>