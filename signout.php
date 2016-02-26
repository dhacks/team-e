<?php
// signout.php
require_once 'init.php';

$_SESSION = array();

session_destroy();

$signin_url = "signin.php";
header("Location: {$signin_url}");
