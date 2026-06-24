<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

/* =========================
   UPDATE STATUS
========================= */

if(isset($_GET['masak'])){

    $id = $_GET['masak'];

    mysqli_query($koneksi,"
    UPDATE transaksi
    SET status_pesanan='dimasak'
    WHERE id_transaksi='$id'
    ");

    echo "
    <script>
    alert('Pesanan sedang dimasak');
    window.location='index.php';
    </script>
    ";
    exit;
}

if(isset($_GET['selesai'])){

    $id = $_GET['selesai'];

    $ambil = mysqli_query($koneksi,"
    SELECT *
    FROM transaksi
    WHERE id_transaksi='$id'
    ");

    $pesanan = mysqli_fetch_assoc($ambil);

    if($pesanan){

        $simpan = mysqli_query($koneksi,"
        INSERT INTO riwayat_pesanan(
            id_transaksi,
            nomor_pesanan,
            id_user,
            total_harga,
            metode_pembayaran,
            payment_status,
            status_pesanan
        ) VALUES (
            '".$pesanan['id_transaksi']."',
            '".$pesanan['nomor_pesanan']."',
            '".$pesanan['id_user']."',
            '".$pesanan['total_harga']."',
            '".$pesanan['metode_pembayaran']."',
            '".$pesanan['payment_status']."',
            'selesai'
        )
        ");

        if($simpan){

            mysqli_query($koneksi,"
            DELETE FROM transaksi
            WHERE id_transaksi='$id'
            ");

        }

    }

    echo "
    <script>
    alert('Pesanan berhasil diselesaikan');
    window.location='index.php';
    </script>
    ";
    exit;
}

/* =========================
   DATA PESANAN
========================= */

$query = mysqli_query($koneksi,"
SELECT transaksi.*, users.nama_lengkap
FROM transaksi
JOIN users ON transaksi.id_user = users.id_user
WHERE transaksi.status_pesanan IN ('diproses','dimasak')
ORDER BY transaksi.id_transaksi DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Dapur</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/kasir.css">

<style>

.btn-masak{
    background:#f59e0b;
    color:white;
    border:none;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

.btn-masak:hover{
    color:white;
}

.btn-selesai{
    background:#22c55e;
    color:white;
    border:none;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

.btn-selesai:hover{
    color:white;
}

</style>

</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <div class="logo">
            👨‍🍳 Dapur Panel
        </div>

        <ul>

            <li>
                <a href="index.php" class="active">
                    <i class="fa-solid fa-fire-burner"></i>
                    Pesanan Dapur
                </a>
            </li>

            <li>
                <a href="../logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            </li>

        </ul>

    </div>

    <!-- CONTENT -->

    <div class="content">

        <div class="topbar">

            <div>

                <h2>
                    Dashboard Dapur
                </h2>

                <p>
                    Kelola pesanan yang telah dikonfirmasi kasir
                </p>

            </div>

        </div>

        <!-- TABLE -->

        <div class="table-box">

            <div class="table-header">

                <h4>
                    Daftar Pesanan Masuk
                </h4>

            </div>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>No Pesanan</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php
                    $no=1;

                    while($data=mysqli_fetch_assoc($query)){
                    ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td>
                            <strong>
                                <?= $data['nomor_pesanan']; ?>
                            </strong>
                        </td>

                        <td>
                            <?= $data['nama_lengkap']; ?>
                        </td>

                        <td>
                            Rp <?= number_format($data['total_harga']); ?>
                        </td>

                        <td>

                            <?php if($data['payment_status']=='paid'){ ?>

                                <span class="badge bg-success">
                                    Paid
                                </span>

                            <?php }else{ ?>

                                <span class="badge bg-danger">
                                    Unpaid
                                </span>

                            <?php } ?>

                        </td>

                        <td>

                            <?php if($data['status_pesanan']=='diproses'){ ?>

                                <span class="badge bg-warning text-dark">
                                    Menunggu Dimasak
                                </span>

                            <?php }else{ ?>

                                <span class="badge bg-primary">
                                    Sedang Dimasak
                                </span>

                            <?php } ?>

                        </td>

                        <td>
                            <?= date('d M Y H:i', strtotime($data['waktu_pesanan'])); ?>
                        </td>

                        <td>

                            <div class="action-group">

                                <a href="detail.php?id=<?= $data['id_transaksi']; ?>"
                                class="btn-detail">
                                    Detail
                                </a>

                                <?php if($data['status_pesanan']=='diproses'){ ?>

                                <a href="?masak=<?= $data['id_transaksi']; ?>"
                                class="btn-masak"
                                onclick="return confirm('Mulai memasak pesanan ini?')">
                                    Mulai Masak
                                </a>

                                <?php } ?>

                                <?php if($data['status_pesanan']=='dimasak'){ ?>

                                <a href="?selesai=<?= $data['id_transaksi']; ?>"
                                class="btn-selesai"
                                onclick="return confirm('Pesanan sudah selesai dimasak?')">
                                    Selesai
                                </a>

                                <?php } ?>

                            </div>

                        </td>

                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>