<?php
// SKRIP UNTUK MENAMBAH BARANG
require_once "connection.php";
require_once "util.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// jika get maka user belum memasukkan data
// yang bisa server proses sehingga kita akan 
// menampilkan form terlebih dahulu
if (is_method_get()) {
  $id = filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);
  $stmt = mysqli_prepare($connection, "SELECT id,nama,harga,stok FROM barang WHERE id=?");
  mysqli_stmt_bind_param($stmt, "i", $id);

  mysqli_stmt_execute($stmt);

  mysqli_stmt_bind_result($stmt, $id, $nama, $harga, $stok);
  $result = mysqli_stmt_get_result($stmt);

  $barang = mysqli_fetch_assoc($result);
?>
  <!-- 
    action merujuk ke tempat yang user akan tuju saat menekan tombol submit
    method post agar saat submit is_method_get akan mengembalikan nilai false sehingga akan menjalankan bagian dari else
  --!>
  <form action="barangupdate.php" method="post">
    <input hidden value="<?= $barang["id"] ?>" name="id"/>
    <label>Nama:  </label><input name="nama" value="<?= $barang["nama"] ?>"/></br>
    <label>Harga: </label><input name="harga" value="<?= $barang["harga"] ?>"/></br>
    <label>Stok:  </label><input name="stok" value="<?= $barang["stok"] ?>"/><br>
    <button>Submit</button>
  </form>
<?php
} else {
  // ini akan dijalankan ketika user menekan tombol submit
  $id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
  $nama = htmlspecialchars($_POST["nama"]);
  $harga = filter_var($_POST["harga"], FILTER_SANITIZE_NUMBER_INT);
  $stok = filter_var($_POST["stok"], FILTER_SANITIZE_NUMBER_INT);

  $stmt = mysqli_prepare($connection, "UPDATE barang SET nama=?, harga=?, stok=? WHERE id=?");
  mysqli_stmt_bind_param($stmt, "siii", $nama, $harga, $stok, $id);
  mysqli_stmt_execute($stmt);

  redirect("index.php");
}
?>
