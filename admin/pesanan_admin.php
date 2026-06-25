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

/* DATA RIWAYAT PESANAN */

$query = mysqli_query($koneksi,"
SELECT
    riwayat_pesanan.*,
    users.nama_lengkap
FROM riwayat_pesanan
LEFT JOIN users
ON riwayat_pesanan.id_user = users.id_user
ORDER BY riwayat_pesanan.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesanan Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/pesanan_admin.css" rel="stylesheet">
</head>

<body>

<div class="wrapper">

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">🍔 Dim's Admin</div>
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="produk.php">Produk</a></li>
        <li><a href="pesanan_admin.php" class="active">Pesanan</a></li>
        <li><a href="users.php">Staff</a></li>
        <li><a href="users_customer.php">Customer</a></li>
        <li><a href="laporan.php">Laporan</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<!-- CONTENT -->
<div class="content">

<div class="header">
    <div>
        <h2>Data Pesanan</h2>
        <p>Kelola semua pesanan pelanggan</p>
    </div>
</div>

<!-- TABLE -->
<div class="table-box">
<table class="table table-hover align-middle">

<thead>
<tr>
<th>No</th>
<th>Nomor Pesanan</th>
<th>User ID</th>
<th>Total</th>
<th>Pembayaran</th>
<th>Status Pesanan</th>
<th>Payment</th>
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
    <?= $data['nomor_pesanan']; ?>
</td>

<td>
    <?= $data['nama_lengkap']; ?>
</td>

<td>
    Rp <?= number_format($data['total_harga']); ?>
</td>

<td>
    <?= strtoupper($data['metode_pembayaran']); ?>
</td>

<td>

<?php if($data['status_pesanan']=='pending'){ ?>

<span class="badge bg-warning">
    Pending
</span>

<?php }elseif($data['status_pesanan']=='diproses'){ ?>

<span class="badge bg-primary">
    Diproses
</span>

<?php }elseif($data['status_pesanan']=='dimasak'){ ?>

<span class="badge bg-info">
    Dimasak
</span>

<?php }else{ ?>

<span class="badge bg-success">
    Selesai
</span>

<?php } ?>

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
    <?= date('d-m-Y H:i',strtotime($data['created_at'])) ?>
</td>

<td>

<button
class="btn btn-info btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalDetail"

onclick='detailPesanan(
<?= json_encode($data["nomor_pesanan"]); ?>,
<?= json_encode($data["nama_lengkap"]); ?>,
<?= json_encode(number_format($data["total_harga"])); ?>,
<?= json_encode(strtoupper($data["metode_pembayaran"])); ?>,
<?= json_encode($data["status_pesanan"]); ?>,
<?= json_encode($data["payment_status"]); ?>
)'>

Detail

</button>

</td>

</tr>

<?php } ?>

</tbody>
</table>
</div>

</div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div class="modal fade" id="modalDetail" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content detail-modal">

            <div class="modal-header detail-header">

                <div>
                    <h4 class="mb-1">
                        📋 Detail Pesanan
                    </h4>

                    <small>
                        Informasi lengkap transaksi pelanggan
                    </small>
                </div>

                <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="detail-card">

                            <h6>
                                Informasi Pesanan
                            </h6>

                            <div class="detail-item">
                                <span>No Pesanan</span>
                                <strong id="d_nomor"></strong>
                            </div>

                            <div class="detail-item">
                                <span>Customer</span>
                                <strong id="d_user"></strong>
                            </div>

                            <div class="detail-item">
                                <span>Total</span>
                                <strong class="harga">
                                    Rp <span id="d_total"></span>
                                </strong>
                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="detail-card">

                            <h6>
                                Status Transaksi
                            </h6>

                            <div class="detail-item">
                                <span>Metode</span>
                                <strong id="d_metode"></strong>
                            </div>

                            <div class="detail-item">
                                <span>Status Pesanan</span>
                                <strong id="d_status"></strong>
                            </div>

                            <div class="detail-item">
                                <span>Status Pembayaran</span>
                                <strong id="d_payment"></strong>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal">

                    Tutup

                </button>

            </div>

        </div>

    </div>

</div>
<!-- ================= MODAL UPDATE ================= -->
<div class="modal fade" id="modalUpdate">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<form method="POST" action="update_status.php">

<div class="modal-header">
<h5>Update Status Pesanan</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="id" id="u_id">

<select name="status_pesanan" id="u_status" class="form-control">

<option value="pending">Pending</option>
<option value="diproses">Diproses</option>
<option value="dimasak">Dimasak</option>
<option value="selesai">Selesai</option>

</select>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
<button class="btn btn-primary">Update</button>
</div>

</form>

</div>
</div>
</div>

<script>

function detailPesanan(
nomor,
user,
total,
metode,
status,
payment
){

document.getElementById('d_nomor').innerText = nomor;
document.getElementById('d_user').innerText = user;
document.getElementById('d_total').innerText = total;
document.getElementById('d_metode').innerText = metode;
document.getElementById('d_status').innerText = status;
document.getElementById('d_payment').innerText = payment;

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>