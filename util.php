<?php

function is_method_get()
{
  return $_SERVER["REQUEST_METHOD"] === "GET";
}

function redirect($url)
{
  header("Location: " . $url);
  exit();
}
