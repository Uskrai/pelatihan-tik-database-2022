<?php
// SKRIP UNTUK MENAMPILKAN DATA
require_once "connection.php";

if (mysqli_connect_errno()) {
  echo "failed to connect to mysql:" . mysqli_connect_error();
  exit();
}

if ($result = mysqli_query($connection, "SELECT * FROM barang")) {
  echo "Jumlah barang: " . mysqli_num_rows($result);

?>
  <table>
    <thead>
      <tr>
        <td>Nama</td>
        <td>Harga</td>
        <td>Stok</td>
        <td>Aksi</td>
      </tr>
    </thead>
    <tbody>
      <?php
      while ($row = mysqli_fetch_array($result)) {
      ?>
        <tr>
          <th>
            <?php echo $row['nama']; ?>
          </th>
          <th>
            <?php echo $row['harga']; ?>
          </th>
          <th>
            <?php echo $row['stok']; ?>
          </th>
          <th>
            <a href="barangdelete.php?id=<?= $row['id'] ?>">Hapus</a>
            <a href="barangupdate.php?id=<?= $row['id'] ?>">Ubah</a>
          </th>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
  <a href="barangtambah.php">Tambah Barang</a>
<?php
  mysqli_free_result($result);
}

$query = "
  SELECT transaksi.id, transaksi.total FROM transaksi
";
if ($result = mysqli_query($connection, $query)) {
  echo "<br/>";
  echo "Jumlah Transaksi: " . mysqli_num_rows($result);
}
?>
<table>
  <thead>
    <tr>
      <td>Id</td>
      <td>Jumlah</td>
      <td>Aksi</td>
    </tr>
  </thead>
  <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
    ?>
      <tr>
        <th>
          <?php echo $row['id']; ?>
        </th>
        <th>
          <?php echo $row['total']; ?>
        </th>
        <th>
          <a href="transaksidetail.php?id=<?= $row['id'] ?>">Detail</a>
          <a href="transaksidelete.php?id=<?= $row['id'] ?>">Hapus</a>
          <a href="transaksiupdate.php?id=<?= $row['id'] ?>">Ubah</a>
        </th>
      </tr>
    <?php
    }
    ?>
  </tbody>
</table>
<a href="transaksitambah.php">Tambah Transaksi</a>
<?php

?>
