<?php
/******************************************************************************
 * library of functions
 ******************************************************************************/

/******************************************************************************
 * extracting all profiles data from DB
 ******************************************************************************/
function getProfileRows() {
  global $pdo;
  $stmt = $pdo->query("SELECT profile_id, first_name, last_name, email, headline
    ,summary FROM profile");
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/******************************************************************************
 * extracting specific profile data from DB
 ******************************************************************************/
function getProfileRow($profile_id) {
  global $pdo;
  $stmt = $pdo->prepare("SELECT user_id, profile_id, first_name, last_name, email
                              ,headline, summary
                        FROM profile
                        where profile_id = :profile_id");
  $stmt->execute(array(":profile_id" => $profile_id));
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

/******************************************************************************
 * extracting positions data for specific profile from DB
 ******************************************************************************/
function getPositions($profile_id) {
  global $pdo;
  $stmt = $pdo->prepare("SELECT rank, year, description
                          FROM position
                          where profile_id = :profile_id
                          ORDER BY rank");
  $stmt->execute(array(':profile_id' => $profile_id));
  return $stmt->fetchall(PDO::FETCH_ASSOC);
}

/******************************************************************************
 * extracting education data for specific profile from DB
 ******************************************************************************/
function getEducation($profile_id) {
  global $pdo;
  $stmt = $pdo->prepare("SELECT rank, year, name
                          FROM education e
                          JOIN institution i ON i.institution_id = e.institution_id
                          WHERE profile_id = :profile_id
                          ORDER BY rank");
  $stmt->execute(array(':profile_id' => $profile_id));
  return $stmt->fetchall(PDO::FETCH_ASSOC);
}

/******************************************************************************
 * deleting positions data for specific profile from DB
 ******************************************************************************/
function clearPositions($profile_id) {
  global $pdo;
  $sql = "DELETE FROM position WHERE profile_id = :profile_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':profile_id' => $profile_id));
}

/******************************************************************************
 * deleting education data for specific profile from DB
 ******************************************************************************/
function clearEducation($profile_id) {
  global $pdo;
  $sql = "DELETE FROM education WHERE profile_id = :profile_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':profile_id' => $profile_id));
}

/******************************************************************************
 * inserting positions data for specific profile to DB
 ******************************************************************************/
function setPositions($profile_id) {
  global $pdo;
  for($i=1; $i<=9; $i++) {
    if (! isset($_SESSION['year'.$i])) continue;
    $sql = "INSERT INTO position (profile_id, rank, year, description)
            VALUES (:profile_id, :rank, :year, :description)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            ':profile_id' => $profile_id,
            ':rank' => $i,
            ':year' => htmlentities($_SESSION['year'.$i]),
            ':description' => htmlentities($_SESSION['desc'.$i]))
    );
  }
}

/******************************************************************************
 * inserting education data for specific profile to DB
 ******************************************************************************/
function setEducation($profile_id) {
  global $pdo;
  for($i=1; $i<=9; $i++) {
    if (! isset($_SESSION['edu_year'.$i])) continue;
    $institution_id = getInstitutionId($_SESSION['edu_school'.$i]);
    $sql = "INSERT INTO Education (profile_id, rank, year, institution_id)
            VALUES (:profile_id, :rank, :year, :institution_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            ':profile_id' => $profile_id,
            ':rank' => $i,
            ':year' => htmlentities($_SESSION['edu_year'.$i]),
            ':institution_id' => $institution_id)
    );
  }
}

/******************************************************************************
 * extracting schools list from DB
 ******************************************************************************/
function getInstitutionId($edu_school) {
  global $pdo;
  $institution_id = false;
  $stmt = $pdo->prepare("SELECT institution_id
                          FROM Institution
                          WHERE name = :name");
  $stmt->execute(array(':name' => $edu_school));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ( $row != false ) $institution_id = $row['institution_id'];
  if ($institution_id == false) {
    $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES(:name)');
    $stmt->execute(array(':name' => htmlentities($edu_school)));
    $institution_id = $pdo->lastInsertId();
  }
  return $institution_id;
}

/******************************************************************************
 * checking positions form for correctness
 ******************************************************************************/
function positionsFilled() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];
    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return false;
    }
  }
  return true;
}

/******************************************************************************
 * checking education form for correctness
 ******************************************************************************/
function eduPositionsFilled() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;
    $year = $_POST['edu_year'.$i];
    $school = $_POST['edu_school'.$i];
    if ( strlen($year) == 0 || strlen($school) == 0 ) {
      return false;
    }
  }
  return true;
}

/******************************************************************************
 * checking if year is numeric
 ******************************************************************************/
function yearsNumeric() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    $year = $_POST['year'.$i];
    if ( ! is_numeric($year) ) {
      return false;
    }
  }
  return true;
}

/******************************************************************************
 * checking if education year is numeric (could be one parametrized function)
 ******************************************************************************/
function eduYearsNumeric() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    $year = $_POST['edu_year'.$i];
    if ( ! is_numeric($year) ) {
      return false;
    }
  }
  return true;
}

/******************************************************************************
 * updating profile data for given profile
 ******************************************************************************/
function updateProfile() {
  global $pdo;
  $sql = "UPDATE profile SET
            first_name = :first_name,
            last_name = :last_name,
            email = :email,
            headline = :headline,
            summary = :summary
          where profile_id = :profile_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
          ':last_name' => htmlentities($_SESSION['last_name']),
          ':first_name' => htmlentities($_SESSION['first_name']),
          ':email' => htmlentities($_SESSION['email']),
          ':headline' => htmlentities($_SESSION['headline']),
          ':profile_id' => $_SESSION['profile_id'],
          ':summary' => htmlentities($_SESSION['summary']))
  );
}

/******************************************************************************
 * inserting profile data for new profile
 ******************************************************************************/
function setProfile() {
  global $pdo;
  $sql = "INSERT INTO profile (user_id, last_name, first_name, email, headline,summary)
          VALUES (:user_id, :last_name, :first_name, :email, :headline, :summary)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
          ':user_id' => $_SESSION['logged_user_id'],
          ':last_name' => htmlentities($_SESSION['last_name']),
          ':first_name' => htmlentities($_SESSION['first_name']),
          ':email' => htmlentities($_SESSION['email']),
          ':headline' => htmlentities($_SESSION['headline']),
          ':summary' => htmlentities($_SESSION['summary']))
  );
}

/******************************************************************************
 * resetting stored in session form variables
 ******************************************************************************/
function unsetFormVariables() {
  unset($_SESSION['last_name']);
  unset($_SESSION['first_name']);
  unset($_SESSION['email']);
  unset($_SESSION['headline']);
  unset($_SESSION['summary']);
  unset($_SESSION['profile_id']);
  for($i=1; $i<=9; $i++) {
    unset($_SESSION['year'.$i]);
    unset($_SESSION['desc'.$i]);
  }
  for($i=1; $i<=9; $i++) {
    unset($_SESSION['edu_year'.$i]);
    unset($_SESSION['edu_school'.$i]);
  }
}

/******************************************************************************
 * checking if the form session variables are set
 ******************************************************************************/
function haveFormVariables() {
  if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])
      && isset($_SESSION['email']) && isset($_SESSION['headline'])
      && isset($_SESSION['summary']))
    return true;
  return false;
}

/******************************************************************************
 * checking if the form variables were posted
 ******************************************************************************/
function havePostFormVariables() {
  if (isset($_POST['first_name']) && isset($_POST['last_name'])
      && isset($_POST['email']) && isset($_POST['headline'])
      && isset($_POST['summary']))
    return true;
  return false;
}

/******************************************************************************
 * checking if all the form fields were filled with values
 ******************************************************************************/
function allFormVariablesFilled() {
  if ((strlen($_POST['first_name']) < 1) || (strlen($_POST['last_name']) < 1)
      || (strlen($_POST['email']) < 1) || (strlen($_POST['headline']) < 1)
      || (strlen($_POST['summary']) < 1))
    return false;
  return true;
}

/******************************************************************************
 * preventing unauthorized users from visiting some pages
 ******************************************************************************/
function killNotLoggedInUser() {
  if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1  ) {
    die('ACCESS DENIED');
  }
}

/******************************************************************************
 * saving post request to session variables
 ******************************************************************************/
function setSessionVariablesFromPOST() {
  $_SESSION['first_name'] = $_POST['first_name'];
  $_SESSION['last_name'] = $_POST['last_name'];
  $_SESSION['email'] = $_POST['email'];
  $_SESSION['headline'] = $_POST['headline'];
  $_SESSION['summary'] = $_POST['summary'];
  $_SESSION['result_message'] = 'Record added';
  for($i=1; $i<=9; $i++) {
    if (! isset($_POST['year'.$i])) continue;
    $_SESSION['year'.$i] = $_POST['year'.$i];
    $_SESSION['desc'.$i] = $_POST['desc'.$i];
  }
  for($i=1; $i<=9; $i++) {
    if (! isset($_POST['edu_year'.$i])) continue;
    $_SESSION['edu_year'.$i] = $_POST['edu_year'.$i];
    $_SESSION['edu_school'.$i] = $_POST['edu_school'.$i];
  }
}
?>
