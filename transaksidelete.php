<?php
require_once "connection.php";
require_once "util.php";
require_once "transaksiutil.php";

$id = filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);

if (mysqli_begin_transaction($connection)) {
  delete_detail_transaksi($connection, $id);
  $stmt = mysqli_prepare($connection, "DELETE FROM transaksi WHERE id=?");
  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);

    mysqli_stmt_execute($stmt);
  }

  if (mysqli_errno($connection)) {
    echo mysqli_error($connection);
  }

  redirect("index.php");
}
