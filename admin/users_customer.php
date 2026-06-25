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

/* AMBIL CUSTOMER */
$query = mysqli_query($koneksi, "
SELECT * FROM users
WHERE role='customer'
ORDER BY id_user DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Customer</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/users.css" rel="stylesheet">
</head>

<body>

<div class="wrapper">

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">🍔 Dim's Admin</div>
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="produk.php">Produk</a></li>
        <li><a href="pesanan_admin.php">Pesanan</a></li>
        <li><a href="users.php">Staff</a></li>
        <li><a href="users_customer.php"class="active">Customer</a></li>
        <li><a href="laporan.php">Laporan</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<!-- CONTENT -->
<div class="content">

<div class="header">
    <div>
        <h2>Data Customer</h2>
        <p>Daftar pelanggan Dims Outlet</p>
    </div>
</div>

<div class="table-box">

<table class="table table-hover align-middle">

<thead>
<tr>
<th>No</th>
<th>Nama</th>
<th>Email</th>
<th>Tanggal Daftar</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php $no=1; while($data = mysqli_fetch_assoc($query)){ ?>

<tr>
<td><?= $no++; ?></td>

<td><?= $data['nama_lengkap']; ?></td>
<td><?= $data['email']; ?></td>
<td><?= date('d-m-Y H:i', strtotime($data['created_at'])) ?></td>

<td>

<!-- DETAIL -->
<button class="btn btn-info btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalDetail"
onclick="detailUser(
'<?= $data['id_user']; ?>',
'<?= $data['nama_lengkap']; ?>',
'<?= $data['email']; ?>',
'<?= $data['created_at']; ?>'
)">
Detail
</button>

<!-- HAPUS -->
<button class="btn btn-danger btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalHapus"
onclick="hapusUser(<?= $data['id_user']; ?>)">
Hapus
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
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5>Detail Customer</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<p><b>ID:</b> <span id="u_id"></span></p>
<p><b>Nama:</b> <span id="u_nama"></span></p>
<p><b>Email:</b> <span id="u_email"></span></p>
<p><b>Tanggal Daftar:</b> <span id="u_created"></span></p>
</div>

</div>
</div>
</div>

<!-- ================= MODAL HAPUS ================= -->
<div class="modal fade" id="modalHapus">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5>Hapus Customer</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
Apakah kamu yakin ingin menghapus customer ini?
</div>

<div class="modal-footer">

<form method="GET" action="hapus_user.php">
<input type="hidden" name="id" id="hapus_id">

<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
<button class="btn btn-danger">Hapus</button>

</form>

</div>

</div>
</div>
</div>

<!-- ================= SCRIPT ================= -->
<script>
function detailUser(id,nama,email,created){
document.getElementById('u_id').innerText = id;
document.getElementById('u_nama').innerText = nama;
document.getElementById('u_email').innerText = email;
document.getElementById('u_created').innerText = created;
}

function hapusUser(id){
document.getElementById('hapus_id').value = id;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>