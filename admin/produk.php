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

/* AMBIL KATEGORI */
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");

/* ===================== SIMPAN PRODUK ===================== */
if(isset($_POST['simpan'])){

    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga       = (int) $_POST['harga'];
    $stok        = (int) $_POST['stok'];

    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];

    $folder = "../uploads/";

    if(!file_exists($folder)){
        mkdir($folder, 0777, true);
    }

    $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];

    if(in_array($ext, $allowed)){

        $newName = "produk_" . time() . "." . $ext;
        move_uploaded_file($tmp, $folder . $newName);

        mysqli_query($koneksi, "
            INSERT INTO produk 
            (id_kategori, nama_produk, deskripsi, harga, gambar, stok, status)
            VALUES 
            ('$id_kategori','$nama_produk','$deskripsi','$harga','$newName','$stok','tersedia')
        ");

        echo "<script>alert('Produk berhasil ditambahkan!');window.location='produk.php';</script>";

    } else {
        echo "<script>alert('Format gambar tidak valid!');</script>";
    }
}

/* ===================== UPDATE PRODUK ===================== */
if(isset($_POST['update'])){

    $id_produk   = $_POST['id_produk'];
    $id_kategori = $_POST['id_kategori'];
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga       = (int) $_POST['harga'];
    $stok        = (int) $_POST['stok'];

    if($_FILES['gambar']['name'] != ""){

        $gambar = $_FILES['gambar']['name'];
        $tmp    = $_FILES['gambar']['tmp_name'];

        $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if(in_array($ext, $allowed)){

            $newName = "produk_" . time() . "." . $ext;
            move_uploaded_file($tmp, "../uploads/" . $newName);

            mysqli_query($koneksi, "
                UPDATE produk SET
                id_kategori='$id_kategori',
                nama_produk='$nama_produk',
                deskripsi='$deskripsi',
                harga='$harga',
                stok='$stok',
                gambar='$newName'
                WHERE id_produk='$id_produk'
            ");

        }

    } else {

        mysqli_query($koneksi, "
            UPDATE produk SET
            id_kategori='$id_kategori',
            nama_produk='$nama_produk',
            deskripsi='$deskripsi',
            harga='$harga',
            stok='$stok'
            WHERE id_produk='$id_produk'
        ");
    }

    echo "<script>alert('Produk berhasil diupdate!');window.location='produk.php';</script>";
}

/* ===================== DATA PRODUK ===================== */
$query_produk = mysqli_query($koneksi, "
SELECT produk.*, kategori.nama_kategori 
FROM produk 
JOIN kategori ON produk.id_kategori = kategori.id_kategori
ORDER BY id_produk DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Produk</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/produk.css" rel="stylesheet">
</head>

<body>

<div class="wrapper">

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">🍔 Dim's Admin</div>
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="produk.php" class="active">Produk</a></li>
        <li><a href="pesanan_admin.php">Pesanan</a></li>
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
        <h2>Data Produk</h2>
        <p>Kelola menu makanan & minuman</p>
    </div>

    <button class="btn btn-warning text-white"
        data-bs-toggle="modal"
        data-bs-target="#modalTambah">
        + Tambah Produk
    </button>
</div>

<!-- TABLE -->
<div class="table-box">
<table class="table align-middle">

<thead>
<tr>
<th>No</th>
<th>Gambar</th>
<th>Nama</th>
<th>Deskripsi</th>
<th>Kategori</th>
<th>Harga</th>
<th>Stok</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php $no=1; while($data = mysqli_fetch_assoc($query_produk)){ ?>

<tr>
<td><?= $no++; ?></td>

<td>
<img src="../uploads/<?= $data['gambar']; ?>" width="60">
</td>

<td><?= $data['nama_produk']; ?></td>
<td><?= $data['deskripsi']; ?></td>
<td><?= $data['nama_kategori']; ?></td>
<td>Rp <?= number_format($data['harga']); ?></td>
<td><?= $data['stok']; ?></td>

<td>

<button class="btn btn-primary btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalEdit"
onclick="editProduk(
'<?= $data['id_produk']; ?>',
'<?= $data['id_kategori']; ?>',
'<?= $data['nama_produk']; ?>',
`<?= $data['deskripsi']; ?>`,
'<?= $data['harga']; ?>',
'<?= $data['stok']; ?>'
)">
Edit
</button>

<button class="btn btn-danger btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalHapus"
onclick="hapusProduk(<?= $data['id_produk']; ?>)">
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

<!-- ================= TAMBAH ================= -->
<div class="modal fade" id="modalTambah">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form method="POST" enctype="multipart/form-data">

<div class="modal-header">
<h5>Tambah Produk</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<select name="id_kategori" class="form-control mb-2">
<option>Pilih Kategori</option>
<?php while($k=mysqli_fetch_assoc($kategori)){ ?>
<option value="<?= $k['id_kategori']; ?>">
<?= $k['nama_kategori']; ?>
</option>
<?php } ?>
</select>

<input type="text" name="nama_produk" class="form-control mb-2" placeholder="Nama">

<textarea name="deskripsi" class="form-control mb-2"></textarea>

<input type="number" name="harga" class="form-control mb-2" placeholder="Harga">

<input type="number" name="stok" class="form-control mb-2" placeholder="Stok">

<input type="file" name="gambar" class="form-control">

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
<button name="simpan" class="btn btn-warning text-white">Simpan</button>
</div>

</form>

</div>
</div>
</div>

<!-- ================= EDIT ================= -->
<div class="modal fade" id="modalEdit">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form method="POST" enctype="multipart/form-data">

<div class="modal-header">
<h5>Edit Produk</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="id_produk" id="edit_id">

<select name="id_kategori" id="edit_kategori" class="form-control mb-2">
<?php
$kategori2 = mysqli_query($koneksi,"SELECT * FROM kategori");
while($k=mysqli_fetch_assoc($kategori2)){
?>
<option value="<?= $k['id_kategori']; ?>">
<?= $k['nama_kategori']; ?>
</option>
<?php } ?>
</select>

<input type="text" name="nama_produk" id="edit_nama" class="form-control mb-2">

<textarea name="deskripsi" id="edit_deskripsi" class="form-control mb-2"></textarea>

<input type="number" name="harga" id="edit_harga" class="form-control mb-2">

<input type="number" name="stok" id="edit_stok" class="form-control mb-2">

<input type="file" name="gambar" class="form-control">

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
<button name="update" class="btn btn-primary">Update</button>
</div>

</form>

</div>
</div>
</div>

<!-- ================= HAPUS ================= -->
<div class="modal fade" id="modalHapus">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5>Hapus Produk</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
Yakin ingin hapus produk ini?
</div>

<div class="modal-footer">
<form method="GET" action="hapus_produk.php">
<input type="hidden" name="id" id="hapus_id">
<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
<button class="btn btn-danger">Hapus</button>
</form>
</div>

</div>
</div>
</div>

<script>
function editProduk(id,kategori,nama,deskripsi,harga,stok){
document.getElementById('edit_id').value=id;
document.getElementById('edit_kategori').value=kategori;
document.getElementById('edit_nama').value=nama;
document.getElementById('edit_deskripsi').value=deskripsi;
document.getElementById('edit_harga').value=harga;
document.getElementById('edit_stok').value=stok;
}

function hapusProduk(id){
document.getElementById('hapus_id').value=id;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>