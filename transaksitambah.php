<?php
// SKRIP UNTUK MENAMBAH BARANG
require_once "connection.php";
require_once "util.php";


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
    $page = count($data);

    $barang = [];
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

  <form action="transaksitambah.php" method="post">
    <?php
    foreach ($data as $key => $value) {
    ?>
        <div>
        <label> Barang: </label>
        <select name="barang_id[<?= $key ?>]">
    <?php
        foreach ($barang as $row) {
    ?>
            <option value="<?= $row['id'] ?>" <?= $row['id'] == $value['barang_id'] ? "selected='selected'" : "" ?>><?= $row['nama'] ?></option>
    <?php
    ?>
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
    <label>Jumlah: </label><input name="jumlah[<?= $page ?>]"/></br>
    <button name="submit" value="tambah">Tambah</button><br>
    <button name="submit" value="submit">Submit</button>
  </form>
<?php
} else {
    // bagian ini akan dijalankan ketika user menekan tombol submit

    mysqli_begin_transaction($connection);
    try {

        $total = array_sum($_POST["jumlah"]);

        // masukkan transaksi
        $stmt = mysqli_prepare(
            $connection,
            "INSERT INTO transaksi (total) VALUES (?)"
        );
        mysqli_stmt_bind_param($stmt, "i", $total);
        mysqli_stmt_execute($stmt);

        $transaksi_id = mysqli_insert_id($connection);

        foreach ($data as $value) {
            // masukkan detail transaksi
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

            // jangan lupa kurangi stok dari barang
            $stmt = mysqli_prepare($connection, "UPDATE barang SET stok = stok - ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $value['jumlah'], $value['barang_id']);
            mysqli_stmt_execute($stmt);
        }

        mysqli_commit($connection);
        redirect("index.php");
    } catch (exception $e) {
        print_r("gagal menambahkan transaksi " . mysqli_error($connection));
        mysqli_rollback($connection);
        exit();
    }
}
?>
