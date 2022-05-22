<?php
session_start();

require_once "pdo.php";
require_once "bootstrap.php";
require_once "resumeFunctions.php";
// unsetFormVariables();
/******************************************************************************
 * if we've got SESSION variables associated with form fields - that means we've
 * been redirected from add.php or edit.php and should put the form to database
 * ***************************************************************************/
if (haveFormVariables()) {
    if (isset($_SESSION['profile_id'])) {
        updateProfile();
        clearPositions($_SESSION['profile_id']);
        setPositions($_SESSION['profile_id']);
        clearEducation($_SESSION['profile_id']);
        setEducation($_SESSION['profile_id']);
    } else {
        setProfile();
        $_SESSION['profile_id'] = $pdo->lastInsertId();
        setPositions($_SESSION['profile_id']);
        setEducation($_SESSION['profile_id']);
    }
    unsetFormVariables();
}
/******************************************************************************
 * ust getting the preview info from DB
 * ***************************************************************************/
$profileRows = getProfileRows();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
          crossorigin="anonymous">

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
          integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
          crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.js"
            integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
            crossorigin="anonymous"></script>

    <title>Ignat Nushtaev - Resume Registry 2e8c6be6</title>
</head>
<body>
<div class="container">
    <h1>Resume Registry</h1>
    <?php
    /************************************************************************
    * some elements on this form are available for logged in users only
    *************************************************************************/
    if (isset($_SESSION['result_message']))
    { echo '<p style="color: green">';
        echo ($_SESSION['result_message']);
        echo '</p>';
        unset($_SESSION['result_message']);
    }

    if ( isset($_SESSION['name']) ) {
        echo '<p><a href="logout.php">Logout</a></p>';}
    else {echo '<p><a href="login.php">Please log in</a></p>';}
    ?>

    <table border="1">
        <tr>
            <td>Name</td>
            <td>Headline</td>
            <?php
            if ( isset($_SESSION['name']) ) {
                echo '<td>Action</td>';}
            ?>
        </tr>
        <?php
        foreach ( $profileRows as $row ) {
            echo "<tr><td>";
            echo('<a href="view.php?profile_id='.$row['profile_id'].'">'
                .$row['first_name'].' '.$row['last_name'].'</a>');
            echo("</td><td>");
            echo($row['headline']);
            echo("</td><td>");
            if ( isset($_SESSION['name']) ) {
                echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
                echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
            }
            echo("</td></tr>\n");
        }
        ?>
    </table>
    <?php
    if ( isset($_SESSION['name']) ) {
        echo '<p><a href="add.php">Add New Entry</a></p>';}
    ?>

</div>
</body>
