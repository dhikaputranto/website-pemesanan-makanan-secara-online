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

/* AMBIL ADMIN, KASIR, DAPUR */
$query = mysqli_query($koneksi, "
SELECT * FROM users
WHERE role IN ('admin','kasir','dapur')
ORDER BY id_user DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Staff</title>

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
        <li><a href="users.php" class="active">Staff</a></li>
        <li><a href="users_customer.php">Customer</a></li>
        <li><a href="laporan.php">Laporan</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<!-- CONTENT -->
<div class="content">

<div class="header">
    <div>
        <h2>Data Staff</h2>
        <p>Admin, Kasir, dan Dapur</p>
    </div>
</div>

<div class="table-box">

<table class="table table-hover align-middle">

<thead>
<tr>
<th>No</th>
<th>Nama</th>
<th>Email</th>
<th>Role</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php $no=1; while($data = mysqli_fetch_assoc($query)){ ?>

<tr>
<td><?= $no++; ?></td>
<td><?= $data['nama_lengkap'] ?? '-'; ?></td>
<td><?= $data['email']; ?></td>

<td>
<?php if($data['role']=='admin'){ ?>
<span class="badge bg-primary">Admin</span>
<?php } elseif($data['role']=='kasir'){ ?>
<span class="badge bg-warning text-dark">Kasir</span>
<?php } else { ?>
<span class="badge bg-secondary">Dapur</span>
<?php } ?>
</td>

<td>

<button class="btn btn-info btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalDetail"
onclick="detailUser(
'<?= $data['id_user']; ?>',
'<?= $data['nama'] ?? '-'; ?>',
'<?= $data['email']; ?>',
'<?= $data['role']; ?>'
)">
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

<!-- MODAL DETAIL -->
<div class="modal fade" id="modalDetail">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5>Detail Staff</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<p><b>ID:</b> <span id="u_id"></span></p>
<p><b>Nama:</b> <span id="u_nama"></span></p>
<p><b>Email:</b> <span id="u_email"></span></p>
<p><b>Role:</b> <span id="u_role"></span></p>
</div>

</div>
</div>
</div>

<script>
function detailUser(id,nama,email,role){
document.getElementById('u_id').innerText=id;
document.getElementById('u_nama').innerText=nama;
document.getElementById('u_email').innerText=email;
document.getElementById('u_role').innerText=role;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>