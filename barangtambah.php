<?php
// SKRIP UNTUK MENAMBAH BARANG
require_once "connection.php";
require_once "util.php";

// jika get maka user belum memasukkan data
// yang bisa server proses sehingga kita akan 
// menampilkan form terlebih dahulu
if (is_method_get()) {
?>
  <!-- 
    action merujuk ke tempat yang user akan tuju saat menekan tombol submit
    method post agar saat submit is_method_get akan mengembalikan nilai false sehingga akan menjalankan bagian dari else
  --!>
  <form action="barangtambah.php" method="post">
    <label>Nama:  </label><input name="nama"/></br>
    <label>Harga: </label><input name="harga"/></br>
    <label>Stok:  </label><input name="stok"/><br>
    <button>Submit</button>
  </form>
<?php
} else {
  // ini akan dijalankan ketika user menekan tombol submit
  $nama = htmlspecialchars($_POST["nama"]);
  $harga = filter_var($_POST["harga"], FILTER_SANITIZE_NUMBER_INT);
  $stok = filter_var($_POST["stok"], FILTER_SANITIZE_NUMBER_INT);

  $stmt = mysqli_prepare($connection, "INSERT INTO barang (nama, harga, stok) VALUES (?, ?, ?)");
  mysqli_stmt_bind_param($stmt, "sii", $nama, $harga, $stok);
  mysqli_stmt_execute($stmt);

  redirect("index.php");
}
?>
