<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("database.php");
$db = new Database();

$name = $_POST['name'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirm_password'];
$email = $_POST['email'];
$noRedirect = isset($_POST['noRedirect']);

// Validate the input.
if ($password != $confirmPassword) invalidationHandler("Passwords do not match.");
if (strlen($name) > 50) invalidationHandler("Name exceeds 50 characters. Please enter a shorter name.");
if (strlen($username) > 50) invalidationHandler("Username exceeds 50 characters. Please enter a shorter name");

if (!isset($_POST['name']) ||
  !isset($_POST['username']) ||
  !isset($_POST['password']) ||
  !isset($_POST['confirm_password']) ||
  !isset($_POST['email']))
  invalidationHandler("Please fill in all the fields.");


// Prepare the SQL statement to add the user.
$statement = $db->prepare("INSERT INTO users VALUES(NULL, ':username', NULL, ':name', ':email', ':password', ':salt', ':created_at');");

// Get the salt value based on the current date time.
$salt = sha1(time());

// Bind the values for the statement.
$statement->bindValue(':username', $username, SQLITE3_TEXT);
$statement->bindValue(':name', $name, SQLITE3_TEXT);
$statement->bindValue(':email', $email, SQLITE3_TEXT);
$statement->bindValue(':password', $password, SQLITE3_TEXT);
$statement->bindValue(':salt', $salt, SQLITE3_INTEGER);
$statement->bindValue(':created_at', $username, SQLITE3_TEXT);



// This function handles invalid requests. Takes in a message which is either sent back
// as a JSON response, or appended to the URL as a GET parameter in case JS is turned off.
function invalidationHandler($message){

    global $noRedirect;

    if ($noRedirect){
      echo '{
        "success": false,
        "reason": '.$message.'
      }';
    } else {
      header("Location: ../pages/register.php?message=".$message);
    }
}

?>