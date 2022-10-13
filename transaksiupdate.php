<?php
// SKRIP UNTUK MENAMBAH BARANG
require_once "connection.php";
require_once "util.php";
require_once "transaksiutil.php";


$transaksi_id = is_method_get() ? $_GET['id'] : $_POST['id'];
$data = [];
foreach ($_POST["barang_id"] ?? [] as $key => $value) {
  $data[$key]["barang_id"] = $value;
}
foreach ($_POST["jumlah"] ?? [] as $key => $value) {
  $data[$key]["jumlah"] = $value;
}

// jika get maka user belum memasukkan data
// yang bisa server proses sehingga kita akan
// menampilkan form terlebih dahulu
if (is_method_get() || $_POST["submit"] === "tambah") {

  // ini akan dijalankan saat user menekan tombol 'ubah' di index.php
  if (is_method_get()) {
    // mengambil detail transaksi yang ingin user update
    $stmt = mysqli_prepare($connection, "SELECT * FROM detail_transaksi WHERE transaksi_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $transaksi_id);
    mysqli_stmt_execute($stmt);

    // memasukkan select diatas ke dalam variable data
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }
  }

  $page = count($data);
  $is_add_new = !is_method_get() && $page > 0;

  $barang = [];
  // mengambil data barang untuk menampilkan ke dalam select
  if ($result = mysqli_query($connection, "SELECT * FROM barang")) {
    while ($row = mysqli_fetch_assoc($result)) {
      $barang[] = $row;
    }
  }
?>
  <!-- 
    action merujuk ke tempat yang user akan tuju saat menekan tombol submit
    method post agar saat submit is_method_get akan mengembalikan nilai false sehingga akan menjalankan bagian dari else
  --!>

  <form action="transaksiupdate.php" method="post">
    <input hidden name="id" value="<?= $transaksi_id ?>"
    <?php
    // bagian ini akan menampilkan
    // akan menampilkan data yang user masukkan sebelumnya
    foreach ($data as $key => $value) {
    ?>
      <div>
      <label> Barang: </label>
      <select name="barang_id[<?= $key ?>]">
      <?php
      // bagian ini akan menampilkan barang barang yang bisa dipilih oleh user
      foreach ($barang as $row) {
      ?>
          <option value="<?= $row['id'] ?>" <?= $row['id'] == $value['barang_id'] ? "selected='selected'" : "" ?>><?= $row['nama'] ?></option>
      <?php
      }
      ?>
      </select>
      <br>
      <label> Jumlah: </label>
      <input name="jumlah[<?= $key ?>]" value="<?= $value['jumlah'] ?>" />
      <br>
      </div>
    <?php
    }
    // akan dijalankan saat user menekan tombol tambah
    if ($is_add_new) {
    ?>
      <label>Barang:  </label>
        <select name="barang_id[<?= $page ?>]">
          <?php
          foreach ($barang as $row) {
          ?>
            <option value="<?= $row["id"] ?>"><?= $row["nama"] ?></option>
          <?php
          }
          ?>
        </select>
        </br>
      <label>Harga: </label><input name="jumlah[<?= $page ?>]"/></br>
    <?php
    }
    ?>
    <button name="submit" value="tambah">Tambah</button><br>
    <button name="submit" value="submit">Submit</button>
  </form>
<?php
} else {
  // ini akan dijalankan ketika user menekan tombol submit

  $total = array_sum($_POST["jumlah"]);

  // check apakah ada transaksi atau tidak,
  // apabila tidak ada maka batalkan update transaksi
  if ($transaksi_id == null) {
    print_r("transaksi tidak ada");
    exit();
  }

  // 
  $stmt = mysqli_prepare(
    $connection,
    "UPDATE transaksi SET total = ? WHERE id = ?"
  );
  mysqli_stmt_bind_param($stmt, "ii", $total, $transaksi_id);
  mysqli_stmt_execute($stmt);

  if (!delete_detail_transaksi($connection, $transaksi_id))  {
    print_r("gagal update transaksi");
    exit();
  }

  foreach ($data as $value) {
    $stmt = mysqli_prepare(
      $connection,
      "INSERT INTO detail_transaksi (transaksi_id, barang_id, jumlah) VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param(
      $stmt,
      "iii",
      $transaksi_id,
      $value["barang_id"],
      $value["jumlah"]
    );
    mysqli_stmt_execute($stmt);
  }

  redirect("transaksidetail.php?id=" . $transaksi_id);
}
?>
