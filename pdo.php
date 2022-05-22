<?php
/*****************************************************************************
 * MySQL DB connector
 ****************************************************************************/
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=autos', 'ignat', 'pass');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
