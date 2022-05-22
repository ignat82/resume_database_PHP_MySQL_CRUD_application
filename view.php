<?php
session_start();
// connecting db
require_once "pdo.php";
require_once "bootstrap.php";
require_once "resumeFunctions.php";
// Demand a GET parameter

if ( isset($_GET['profile_id']) ) {
    $_SESSION['profile_id'] = $_GET['profile_id'];
    header('Location: view.php');
    return;
}

/******************************************************************************
 * getting profile, positions and education data from DB
 ******************************************************************************/
$row = getProfileRow($_SESSION['profile_id']);
$posRows = getPositions($_SESSION['profile_id']);
$eduRows = getEducation($_SESSION['profile_id']);
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

    <link rel="stylesheet"
          href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-3.2.1.js"
            integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
            crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
            integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
            crossorigin="anonymous"></script>
    <title>Ignat Nushtaev profiles DB 7db7f9b1</title>
</head>
<body>
<div class="container">
    <h1>Profile information</h1>
    <p>First Name: <?php echo htmlentities($row['first_name']) ?></p>
    <p>Last Name: <?php echo htmlentities($row['last_name']) ?></p>
    <p>Email: <?php echo htmlentities($row['email']) ?></p>
    <p>Headline:<br> <?php echo htmlentities($row['headline']) ?></p>
    <p>Summary:<br> <?php echo htmlentities($row['summary']) ?></p>
    <p></p>
    <p>Education</p>
    <ul>
        <?php
        foreach ( $eduRows as $row ) {
            if ($row != null) {
                echo('<li>'.$row['year'].': '.$row['name'].'</li>');
            }
        }
        ?>
    </ul>
    <p>Position</p>
    <ul>
        <?php
        foreach ( $posRows as $row ) {
            if ($row != null) {
                echo('<li>'.$row['year'].': '.$row['description'].'</li>');
            }
        }
        ?>
    </ul>
    <p>
        <a href="index.php">Done</a>
    </p>
</div>
</body>
</html>
