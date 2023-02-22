<?php
session_start();

// cek apakah ada session login atau belum
if(!isset($_SESSION["login"])){
    header("Location: login.php");
    exit;
}

require 'functions.php';

$id = $_GET["id"];

if( hapus($id) > 0){
    header("Location: index.php");
}

?>