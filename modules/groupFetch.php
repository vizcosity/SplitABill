<?php
// Grab the bills for a given group.
// Grab the users within a given group.
session_start();
date_default_timezone_set("Europe/London");
error_reporting(E_ALL);
ini_set("display_errors", 1);

include("database.php");
$db = new Database();


// Grabs the jointoken for a specified group of id passed into the function.
function getJoinToken($groupID){

  global $db;

  $dbQuery = $db->prepare("SELECT joinToken FROM groups WHERE id=:groupID");
  $dbQuery->bindValue(":groupID", $groupID, SQLITE3_INTEGER);

  $dbQuery = $dbQuery->execute()->fetchArray();

  return $dbQuery['joinToken'];

}



// Grabs the users from the group, formats for HTML render and outputs as string.
function getUsersInGroup($groupID){

  $dbQuery = fetchUsersInGroup($groupID);

  $output = "";

  while($row = $dbQuery->fetchArray()){
    // For each row entry, concatente the string.s
    $output = $output.stringifyUserItem($row);
  }

  // Output should be generated by now.
  return $output;

}


// Grabs the users from the group, formats for HTML render and outputs as string.
function getBillsInGroup($userID, $groupID){

  $dbQuery = fetchBillsInGroup($groupID);

  $output = "";

  while ($row = $dbQuery->fetchArray()){
    $output = $output.stringifyBillItem($userID, $row);
  }

  return $output;

}


function stringifyBillItem($userID, $item){

  // Need to get the specific cost for the user,
  // need to get the amount of users for a specific bill.

  $cost = getCostForUser($userID, $item['id']);

  return '<a href="bill.php?id='.$item['id'].'" class="collection-item"><span class="badge">£'.$cost.'</span>'.$item['name'].'</a>';

}

function getCostForUser($userID, $billID){
  global $db;

  $query = $db->prepare("SELECT selfCost FROM usersInBill WHERE billID=:billID AND userID=:userID");
  $query->bindValue(":billID", $billID, SQLITE3_INTEGER);
  $query->bindValue(":userID", $userID, SQLITE3_INTEGER);

  $query = $query->execute()->fetchArray();

  return $query['selfCost'];
}

// Takes in a userItem and outputs a html formatted element for the group page.
function stringifyUserItem($userItem){

  return

  '<li class="collection-item avatar">
    <i class="material-icons circle blue accent-2">face</i>
    <span class="title">'.$userItem['name'].'</span>
    <p>('.$userItem['username'].')</p>
    <p>Joined Since: '.date("d F Y", $userItem['created_at']).'</p>
    <a href="user.php?id='.$userItem['id'].'" class="secondary-content"><i class="material-icons">info_outline</i></a>
  </li>';

}

// Grab the users given a specific Group.
function fetchUsersInGroup($groupID){

  global $db;

  $statement = $db->prepare("SELECT * FROM users WHERE id IN (SELECT userID FROM usersInGroup WHERE groupID=:groupID)");
  $statement->bindValue(":groupID", $groupID, SQLITE3_INTEGER);

  $statement = $statement->execute();

  return $statement;

}

// Grab the bills given a specific group.
function fetchBillsInGroup($groupID){

  global $db;

  $statement = $db->prepare("SELECT * FROM bills WHERE group_id=:group_id");
  $statement->bindValue(":group_id", $groupID, SQLITE3_INTEGER);

  $statement = $statement->execute();

  return $statement;

}

?>
