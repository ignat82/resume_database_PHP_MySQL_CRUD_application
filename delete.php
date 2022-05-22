<?php
session_start();

require_once "pdo.php";
require_once "bootstrap.php";
require_once "resumeFunctions.php";

/*****************************************************************************
 * only authorized user are permitted to operate on this page
 ****************************************************************************/
killNotLoggedInUser();

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    clearPositions($_POST['profile_id']);
    unsetFormVariables();
    $_SESSION['result_message'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

$row = getProfileRow($_GET['profile_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ignat Nushtaev - Resume Registry 59d415f6</title>
</head>
<body>
<div class="container">
    <h1>Deleting Profile</h1>
    <p>First Name: <?= htmlentities($row['first_name']) ?></p>
    <p>Last Name: <?= htmlentities($row['last_name']) ?></p>
    <form method="post">
        <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
        <input type="submit" value="Delete" name="delete">
        <a href="index.php">Cancel</a>
    </form>
</div>
</body>
