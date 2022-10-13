<?php

function delete_detail_transaksi($connection, $transaksi_id)
{
  mysqli_begin_transaction($connection);

  try {
    // query ini akan mengembalikan stok barang ke 
    // semula sebelum transaksi terjadi dengan menambahkan
    // jumlah yang ada di transaksi dengan stok barang
    // sehingga seakan akan transaksi tersebut tidak pernah terjadi
    $query = "
      UPDATE barang
        INNER JOIN (
          SELECT barang_id, SUM(jumlah) as jumlah
            FROM detail_transaksi
            WHERE transaksi_id = ?
            GROUP BY barang_id
          ) AS detail_transaksi ON
              barang.id = detail_transaksi.barang_id
        SET
          barang.stok = barang.stok + detail_transaksi.jumlah
    ";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $transaksi_id);
    mysqli_stmt_execute($stmt);


    // query ini akan menghapus semua detail yang dimiliki transaksi
    $stmt = mysqli_prepare($connection, "DELETE FROM detail_transaksi WHERE transaksi_id=?");
    mysqli_stmt_bind_param($stmt, "i", $transaksi_id);
    mysqli_stmt_execute($stmt);

    mysqli_commit($connection);
    return true;
  } catch (exception $e) {
    mysqli_rollback($connection);
    return false;
  }
};
