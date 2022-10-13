<?php
// SKRIP UNTUK MENAMPILKAN DATA
require_once "connection.php";

echo "<a href='/'>Kembali</a><br/>";
if (mysqli_connect_errno()) {
  echo "failed to connect to mysql:" . mysqli_connect_error();
  exit();
}

$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

$query = "
SELECT barang.nama, detail_transaksi.jumlah 
  FROM detail_transaksi 
  INNER JOIN barang ON barang.id = barang_id 
  WHERE detail_transaksi.transaksi_id = ?
  ORDER BY detail_transaksi.id
";
if ($stmt = mysqli_prepare($connection, $query)) {
  mysqli_stmt_bind_param($stmt, "i", $id);

  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
?>
  <table>
    <thead>
      <tr>
        <td>Barang</td>
        <td>Harga</td>
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
            <?php echo $row['jumlah']; ?>
          </th>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
<?php
  mysqli_free_result($result);
}
