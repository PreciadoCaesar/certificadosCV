<?php
session_start();
$_SESSION['nombre']="";
$_SESSION['pass']="";
$_SESSION['rol']="";
header("Location:login.php");
?>