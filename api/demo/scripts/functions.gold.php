<?php
/*
	* Functions Object
	* MobSci Implementation
*/

class DBFunctions
{
	// Constructor sets the DB construct
	function __construct() {
		include_once("db/connect.inc.php");
	}

	// Add new user to database (with username & password)
    public function addUser($username, $password) {
        $result = mysql_query("INSERT INTO accounts(Username, Password, Type, Visibility, CreatedDateTime) VALUES('".$username."', MD5('".$password."'), 1, 1, now())");
        if ($result) {
            $uid = mysql_insert_id();
            $result = mysql_query("SELECT AccountID, Username, EmailAddress, Password, Image, Description, Location, Quote, Type, Visibility, CreatedDateTime, RatingScore FROM accounts WHERE AccountID = '".$uid."'");
            return mysql_fetch_array($result);
        }
        else
        	return false;
    }


    // Get user info by email and password
    public function login($username, $password) {
        $result = mysql_query("SELECT AccountID, Username, EmailAddress, Password, Image, Description, Location, Quote, Type, Visibility, CreatedDateTime, RatingScore FROM accounts WHERE Username='".$username."' AND Password=MD5('".$password."')");
        $num_of_rows = mysql_num_rows($result);

        // If greater than zero, return the user, otherwise return false
        if ($num_of_rows > 0)
            return mysql_fetch_array($result);
        else
            return false;
    }


    // Get user info by id and password
    public function loginById($id, $password) {
    	$result = mysql_query("SELECT AccountID, Username, EmailAddress, Password, Image, Description, Location, Quote, Type, Visibility, CreatedDateTime, RatingScore FROM accounts WHERE AccountID=".$id." AND Password=MD5('".$password."')");
        $num_of_rows = mysql_num_rows($result);

        // If greater than zero, return the user, otherwise return false
        if ($num_of_rows > 0)
            return mysql_fetch_array($result);
        else
            return false;
    }


    // Gets all the tags by AccountID of user
	public function getTagsById($id) {
		$result = mysql_query("Select t.*, a.Username From tags t, accounts A Where t.OwnerID = '".$id."' and t.OwnerID = a.AccountID Order By t.CreatedDateTime Desc");
    	// check for result, if greater than 0, send all tags, otherwise send back false
    	$num_of_rows = mysql_num_rows($result);
    	if ($num_of_rows > 0)
    		return $result;
    	else
	    	return false;
    }


	// Return the Username for the given user id
	public function getNameFromId($id) {
		$result = mysql_query("Select Username From accounts a Where a.AccountID = '$id'");
    	// check for result
    	if ($result)
    		return mysql_fetch_array($result);	// return query result
    	else
    		return false;	// user has no tags
	}

	// Checks to make sure the username is not taken
    public function checkUsername($uName) {
        $result = mysql_query("SELECT Username from accounts WHERE Username = '$uName'");
        $num_of_rows = mysql_num_rows($result);
        if ($num_of_rows > 0)
            return true;	// uname taken
        return false;	// uname not taken
    }


    // Add tag to database. Will return true if the add was successful
    public function addTag($oId, $vis, $name, $desc, $imgUrl, $loc, $lat, $lon, $cat) {
    	$result = mysql_query("Select AddTag($oId, \"$name\", $vis, \"$desc\", \"$imgUrl\", \"$loc\", $lat, $lon, \"$cat\")");
    	return $result;
    }

    // Remove a tag from database
    public function removeTag($tId) {
        $result = mysql_query("Delete from tags Where TagId = '$tId'");
        return $result;
    }

    // Add a friend to a given id
    public function addFriend($userId, $friendName) {
		$result = mysql_query("Select AddFriend($userId,'$friendName')");
		$returnCode = mysql_result($result, 0);
		return $returnCode;
    }


    // Returns adventure details
    public function getAdventureById($id) {
		$res = mysql_query("SELECT * FROM adventures WHERE ID=".$id);
		$num_of_rows = mysql_num_rows($res);

		if ($num_of_rows > 0)
			return mysql_fetch_array($result);
		else
			return false;
    }

}

?>