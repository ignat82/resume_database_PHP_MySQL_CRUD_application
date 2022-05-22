<?php
session_start();

require_once "pdo.php";
require_once "bootstrap.php";
require_once "resumeFunctions.php";

/*****************************************************************************
 * only authorized user are permitted to operate on this page
 ****************************************************************************/
killNotLoggedInUser();

/*****************************************************************************
 * saving id of profile we're going to edit in session variable and
 * redirecting to same page
 ****************************************************************************/
if ( isset($_GET['profile_id']) ) {
    $_SESSION['profile_id'] = $_GET['profile_id'];
    header('Location: edit.php');
    return;
}

/*****************************************************************************
 * checking if the user is going to edit someone else profile
 ****************************************************************************/
$row = getProfileRow($_SESSION['profile_id']);
if ($_SESSION['logged_user_id'] != $row['user_id'] ) {
    die('Not alowed to edit this record');
}

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

/*****************************************************************************
 * preforming bunch of checks
 ****************************************************************************/
if (havePostFormVariables()) {
    if (!allFormVariablesFilled()) {
        $_SESSION['result_message'] = 'All fields are required';
        header("Location: edit.php");
        return;
    } elseif (str_contains($_POST['email'],'@') === false) {
        $_SESSION['result_message'] = "Email must have an at-sign (@)";
        header("Location: edit.php");
        return;
    } else {
        if (! positionsFilled()) {
            $_SESSION['result_message'] = "All fields are required";
            header("Location: edit.php");
            return;
        }
        if ( !yearsNumeric() ) {
            $_SESSION['result_message'] = "Position year must be numeric";
            header("Location: edit.php");
            return;
        }
        if (!eduPositionsFilled()) {
            $_SESSION['result_message'] = "All fields are required";
            header("Location: add.php");
            return;
        }
        if ( !eduYearsNumeric() ) {
            $_SESSION['result_message'] = "Education year must be numeric";
            header("Location: add.php");
            return;
        }
    }
    /*****************************************************************************
     * if everything seem ok - putting form to session variables and redirecting
     * to index.php where DB operation will be preformed
     ****************************************************************************/
    setSessionVariablesFromPOST();
    header("Location: index.php");
    return;
}

/*****************************************************************************
 * getting data from DB to prepopulate the edit form
 ****************************************************************************/
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
    <?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
    <h1>Editing Profile for
        <?php
        if (isset($_SESSION['name']) ) {echo htmlentities($_SESSION['name']);}
        ?>
    </h1>
    <?php
    if (isset($_SESSION['result_message'])) {
        echo '<p style="color: red">';
        echo ($_SESSION['result_message']);
        echo '</p>';
        unset($_SESSION['result_message']);
    }
    ?>
    <form method="post">
        <p>First Name:
            <input type="text" name="first_name" size="60" value="<?= $row['first_name'] ?>">
        </p>
        <p>Last Name:
            <input type="text" name="last_name" size="60" value="<?= $row['last_name'] ?>">
        </p>
        <p>Email:
            <input type="text" name="email" size="30" value="<?= $row['email'] ?>">
        </p>
        <p>Headline:
            <br>
            <input type="text" name="headline" size="80" value="<?= $row['headline'] ?>">
        </p>
        <p>Summary:
            <br>
            <textarea type="text" name="summary" rows="8" cols="80"><?php
                echo htmlentities($row['summary'])
                ?></textarea>
        </p>
        <p>
            Education:
            <input type="submit" id="addEdu" value="+">
        </p>
        <div id="education_fields">
            <?php
            /*****************************************************************
             * constructing the necessary number of "education" forms
             ****************************************************************/
            $i = 0;
            $countEdu = 0;
            foreach ( $eduRows as $row ) {
                $i++;
                if ($row != null) {
                    $countEdu++;
                    echo('<div id="edu'.$i.'">'.
                        '<p>Year: <input type="text" name="edu_year'.$i.'" value="'.$row['year'].'">'.
                        '<input type="button" value="-" onclick="$(\'#edu'.$i.'\').remove();return false;"></p>'.
                        '<p>School: <input type="text" size="80" name="edu_school'.$i.
                        '" class="school" value="'.$row['name'].'">'.'</p>'.
                        '</div>');
                }
            }
            ?>
            <script>
                /**************************************************************
                 * autocompletion of school based on values from DB
                 *************************************************************/
                $('.school').autocomplete({
                    source: "school.php"
                });
            </script>
        </div>
        <p>
            Position:
            <input type="submit" id="addPos" value="+">
        </p>
        <div id="position_fields">
            <?php
            /*****************************************************************
             * constructing the necessary number of "position" forms
             ****************************************************************/
            $i = 0;
            $countPos = 0;
            foreach ( $posRows as $row ) {
                $i++;
                if ($row != null) {
                    $countPos++;
                    echo('<div id="position'.$i.'">'.
                        '<p>Year: <input type="text" name="year'.$i.'" value="'.$row['year'].'">'.
                        '<input type="button" value="-" onclick="$(\'#position'.$i.'\').remove();return false;"></p>'.
                        '<textarea name="desc'.$i.'" rows="8" cols="80">'.$row['description'].'</textarea>'.
                        '</div>');
                }
            }
            ?>
        </div>
        <p><p/>
        <p>
            <input type="submit" value="Save"/>
            <input type="submit" name="cancel" value="Cancel">
        </p>
    </form>
    <script>
        /*****************************************************************
         * js to add of delete "position" forms
         ****************************************************************/
        countPos = <?= $countPos ?>;
        $(document).ready(function(){
            window.console && console.log('Document ready called');
            $('#addPos').click(function(event){
                event.preventDefault();
                if ( countPos >= 9 ) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                window.console && console.log("Adding position "+countPos);
                $('#position_fields').append(
                    '<div id="position'+countPos+'"> \
              <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
              <input type="button" value="-" \
              onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
              <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
              </div>');
            });
        });
    </script>
    <script>
        /*****************************************************************
         * js to add of delete "education" forms
         ****************************************************************/
        countEdu = <?= $countEdu ?>;
        $(document).ready(function(){
            window.console && console.log('Document ready called');
            $('#addEdu').click(function(event){
                // http://api.jquery.com/event.preventdefault/
                event.preventDefault();
                if ( countEdu >= 9 ) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countEdu++;
                window.console && console.log("Adding position "+countEdu);
                $('#education_fields').append(
                    '<div id="edu'+countEdu+'"> \
              <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
              <input type="button" value="-" \
              onclick="$(\'#edu'+countEdu+'\').remove();return false;"></p> \
              <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" \
              class="school" value=""><\p>\
              </div>');
                $('.school').autocomplete({
                    source: "school.php"
                });
            });

        });
    </script>
</div>
</body>
