<?php
header('Content-type: text/html; charset=UTF-8');
error_reporting(E_ALL);ini_set('display_errors',1);
$dsn = "mysql:host=115.28.78.55;dbname=book";
$dbh = new PDO($dsn, 'root', 'mysql');
$dbh->query('set names utf8;');
?>