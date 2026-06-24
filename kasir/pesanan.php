<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role'] != 'kasir'){
    header("Location: ../login.php");
    exit;
}

/* UPDATE STATUS */

/* UPDATE STATUS */

if(isset($_GET['konfirmasi'])){

    $id = $_GET['konfirmasi'];

    mysqli_query($koneksi,"
    UPDATE transaksi
    SET status_pesanan='diproses'
    WHERE id_transaksi='$id'
    ");

    echo "
    <script>
    alert('Pesanan berhasil dikonfirmasi');
    window.location='pesanan.php';
    </script>
    ";
    exit;
}

/* DATA PESANAN */

$query = mysqli_query($koneksi,"
SELECT transaksi.*, users.nama_lengkap
FROM transaksi
JOIN users ON transaksi.id_user = users.id_user
ORDER BY transaksi.id_transaksi DESC
");

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pesanan Kasir</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/kasir.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

</head>

<body>

<div class="wrapper">

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        💳 Kasir Panel
    </div>

    <ul>

        <li>
            <a href="index.php">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="pesanan.php" class="active">
                <i class="fa-solid fa-bag-shopping"></i>
                Pesanan
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
                Data Pesanan
            </h2>

            <p>
                Kelola semua pesanan customer
            </p>

        </div>

    </div>

    <!-- TABLE -->

    <div class="table-box">

        <div class="table-header">

            <h4>
                Daftar Pesanan Customer
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
                        <th>Metode</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php
                $no=1;
                while($data = mysqli_fetch_assoc($query)){
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

                            <?php if($data['metode_pembayaran'] == 'qris'){ ?>

                                <span class="badge bg-primary">
                                    QRIS
                                </span>

                            <?php } else { ?>

                                <span class="badge bg-success">
                                    Tunai
                                </span>

                            <?php } ?>

                        </td>

                        <td>

                            <?php if($data['payment_status'] == 'paid'){ ?>

                            <span class="badge bg-success">
                                Paid
                            </span>

                            <?php } else { ?>

                            <span class="badge bg-danger">
                                Unpaid
                            </span>

                            <?php } ?>

                        </td>

                        <td>

                            <?php if($data['status_pesanan'] == 'pending'){ ?>

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                            <?php } elseif($data['status_pesanan'] == 'diproses'){ ?>

                                <span class="badge bg-info">
                                    Diproses
                                </span>

                            <?php } elseif($data['status_pesanan'] == 'dimasak'){ ?>

                                <span class="badge bg-primary">
                                    Dimasak
                                </span>

                            <?php } else { ?>

                                <span class="badge bg-success">
                                    Selesai
                                </span>

                            <?php } ?>

                        </td>

                        <td>
                            <?= date('d M Y H:i', strtotime($data['waktu_pesanan'])); ?>
                        </td>

                        <td>

                            <div class="action-group">

                                <a href="detail_pesanan.php?id=<?= $data['id_transaksi']; ?>"
                                class="btn-detail">
                                    Detail
                                </a>

                                <?php
                                    if(
                                        $data['payment_status'] == 'paid'
                                        &&
                                        $data['status_pesanan'] == 'pending'
                                    ){
                                    ?>

                                    <a href="?konfirmasi=<?= $data['id_transaksi']; ?>"
                                    class="btn-konfirmasi"
                                    onclick="return confirm('Konfirmasi pesanan ini?')">
                                        Konfirmasi
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