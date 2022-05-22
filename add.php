<?php
session_start();

require_once "pdo.php";
require_once "bootstrap.php";
require_once "resumeFunctions.php";

/*****************************************************************************
 * only authorized user are permitted to operate on this page
 ****************************************************************************/
killNotLoggedInUser();

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
/*****************************************************************************
 * if form variables are set - making bunch of checks and refreshing page with
 * showing flash-style message if something is filled not prorely
 ****************************************************************************/
if (havePostFormVariables()) {
    /************************************************************************
     * all form variables aren't empty
     ************************************************************************/
    if (!allFormVariablesFilled()) {
        $_SESSION['result_message'] = 'All fields are required';
        header("Location: add.php");
        return;
    /*************************************************************************
     * simple check for proper email
     ************************************************************************/
    } elseif (str_contains($_POST['email'],'@') === false) {
        $_SESSION['result_message'] = "Email must have an at-sign (@)";
        header("Location: add.php");
        return;
    /*************************************************************************
     * position and education variables are filled, years fields are numeric
     ************************************************************************/
    } else {
        if (! positionsFilled()) {
            $_SESSION['result_message'] = "All fields are required";
            header("Location: add.php");
            return;
        }
        if ( !yearsNumeric() ) {
            $_SESSION['result_message'] = "Position year must be numeric";
            header("Location: add.php");
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
    /**************************************************************************
     * if all looks well - putting form to session variables and redirecting to
     * index.php to make the DB insertion/edition
     *************************************************************************/
    setSessionVariablesFromPOST();
    header("Location: index.php");
    return;
}
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
    <title>Ignat Nushtaev Resume DB 7db7f9b1</title>
</head>
<body>
<div class="container">
    <h1>Adding Profile for
        <?php
        if (isset($_SESSION['name']) ) {echo htmlentities($_SESSION['name']);}
        ?>
    </h1>
    <?php
    if (isset($_SESSION['result_message']) && $_SESSION['result_message'] !== 'Record added')
    { echo '<p style="color: red">';
        echo ($_SESSION['result_message']);
        echo '</p>';
        unset($_SESSION['result_message']);
    }
    ?>
    <form method="post">
        <p>First Name:
            <input type="text" name="first_name" size="60"></p>
        <p>Last Name:
            <input type="text" name="last_name" size="60"></p>
        <p>Email:
            <input type="text" name="email" size="30"></p>
        <p>Headline:
            <br>
            <input type="text" name="headline" size="80"></p>
        <p>Summary:
            <br>
            <textarea type="text" name="summary" rows="8" cols="80"></textarea></p>
        <p>
            Education:
            <input type="submit" id="addEdu" value="+">
        </p>
        <div id="edu_fields"></div>
        <p></p>
        <p>
            Position:
            <input type="submit" id="addPos" value="+">
        </p>
        <div id="position_fields"></div>
        <p></p>
        <p>
            <input type="submit" value="Add"/>
            <input type="submit" name="cancel" value="Cancel">
        </p>
    </form>
    <!--************************************************************************
    * js for dynamically population input form with extra education and/or
    * position lines
    *************************************************************************-->
    <script>
        countPos = 0;
        countEdu = 0;
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
            <input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"><br>\
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
            });

            $('#addEdu').click(function(event){
                event.preventDefault();
                if ( countEdu >= 9 ) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
                countEdu++;
                window.console && console.log("Adding education "+countEdu);

                $('#edu_fields').append(
                    '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
                );
                /**************************************************************
                 * autocompletion of school based on values from DB
                 *************************************************************/
                $('.school').autocomplete({
                    source: "school.php"
                });
            });
        });
    </script>
</div>
</body>
</html>
