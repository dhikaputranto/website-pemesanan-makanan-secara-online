<?php
session_start();
include '../koneksi.php';

/* CEK LOGIN */
if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

/* AMBIL TRANSAKSI */
$query = mysqli_query($koneksi, "
SELECT * FROM transaksi
ORDER BY waktu_pesanan DESC
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

<?php $no=1; while($data = mysqli_fetch_assoc($query)){ ?>

<tr>
<td><?= $no++; ?></td>
<td><?= $data['nomor_pesanan']; ?></td>
<td><?= $data['id_user']; ?></td>
<td>Rp <?= number_format($data['total_harga']); ?></td>
<td><?= strtoupper($data['metode_pembayaran']); ?></td>

<td>
<?php if($data['status_pesanan']=='pending'){ ?>
<span class="badge bg-warning">Pending</span>
<?php } elseif($data['status_pesanan']=='diproses'){ ?>
<span class="badge bg-primary">Diproses</span>
<?php } elseif($data['status_pesanan']=='dimasak'){ ?>
<span class="badge bg-secondary">Dimasak</span>
<?php } else { ?>
<span class="badge bg-success">Selesai</span>
<?php } ?>
</td>

<td>
<?php if($data['payment_status']=='paid'){ ?>
<span class="badge bg-success">Paid</span>
<?php } elseif($data['payment_status']=='unpaid'){ ?>
<span class="badge bg-warning">Unpaid</span>
<?php } elseif($data['payment_status']=='expired'){ ?>
<span class="badge bg-danger">Expired</span>
<?php } else { ?>
<span class="badge bg-dark">Cancelled</span>
<?php } ?>
</td>

<td><?= date('d-m-Y H:i', strtotime($data['waktu_pesanan'])) ?></td>

<td>

<!-- DETAIL -->
<button class="btn btn-info btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalDetail"
onclick="detailPesanan(
'<?= $data['nomor_pesanan']; ?>',
'<?= $data['id_user']; ?>',
'<?= $data['total_harga']; ?>',
'<?= $data['metode_pembayaran']; ?>',
'<?= $data['status_pesanan']; ?>',
'<?= $data['payment_status']; ?>',
'<?= $data['catatan']; ?>'
)">
Detail
</button>

<!-- UPDATE STATUS -->
<button class="btn btn-primary btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalUpdate"
onclick="updatePesanan(<?= $data['id_transaksi']; ?>,'<?= $data['status_pesanan']; ?>')">
Update
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
<div class="modal fade" id="modalDetail">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5>Detail Pesanan</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<p><b>Nomor:</b> <span id="d_nomor"></span></p>
<p><b>User ID:</b> <span id="d_user"></span></p>
<p><b>Total:</b> Rp <span id="d_total"></span></p>
<p><b>Pembayaran:</b> <span id="d_metode"></span></p>
<p><b>Status:</b> <span id="d_status"></span></p>
<p><b>Payment:</b> <span id="d_payment"></span></p>
<p><b>Catatan:</b> <span id="d_catatan"></span></p>

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
function detailPesanan(nomor,user,total,metode,status,payment,catatan){
document.getElementById('d_nomor').innerText=nomor;
document.getElementById('d_user').innerText=user;
document.getElementById('d_total').innerText=total;
document.getElementById('d_metode').innerText=metode;
document.getElementById('d_status').innerText=status;
document.getElementById('d_payment').innerText=payment;
document.getElementById('d_catatan').innerText=catatan;
}

function updatePesanan(id,status){
document.getElementById('u_id').value=id;
document.getElementById('u_status').value=status;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>