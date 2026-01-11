<?php
session_start();

$_SESSION = [];

session_destroy();

/* Redirigir al inicio */
header("Location: index.php");
exit;
