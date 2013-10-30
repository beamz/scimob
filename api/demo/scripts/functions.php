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
            $result = mysql_query("SELECT AccountID, Username, EmailAddress, Image, Description, Location, Quote, Type, Visibility, CreatedDateTime, RatingScore FROM accounts WHERE AccountID = '".$uid."'");
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



    // Returns details on user based on username
    public function getUser($username) {
        $result = mysql_query("SELECT * FROM accounts WHERE Username='".$username."'");
        $num_of_rows = mysql_num_rows($result);

        // If greater than zero, return the user, otherwise return false
        if ($num_of_rows > 0)
            return mysql_fetch_array($result);
        else
            return false;
    }


    // Gets all the tags associated with a user, along with the tag comments
	public function getTagsById($id) {
		$result = mysql_query("SELECT t.TagID, t.OwnerID, t.Name, t.Description, t.ImageUrl, t.Visibility, t.Location, t.Latitude, t.Longitude, t.Category, t.CreatedDateTime, t.RatingScore, a.Username FROM tags t, accounts a WHERE t.OwnerID=".$id." AND t.OwnerID=a.AccountID ORDER BY t.CreatedDateTime DESC");
    	$num_of_rows = mysql_num_rows($result);

    	if ($num_of_rows > 0) {
    		while ($rows = mysql_fetch_array($result)) {
	    		$comments = $this->getTagComments($rows['TagID']);		// Grab all the comments for this tag
	    		$data[] = array('TagID'=>$rows['TagID'], 'OwnerID'=>$rows['OwnerID'], 'Username'=>$rows['Username'], 'Name'=>$rows['Name'], 'Description'=>$rows['Description'], 'ImageUrl'=>$rows['ImageUrl'], 'Visibility'=>$rows['Visibility'], 'Location'=>$rows['Location'], 'Latitude'=>$rows['Latitude'], 'Longitude'=>$rows['Longitude'], 'Category'=>$rows['Category'], 'CreatedDateTime'=>$rows['CreatedDateTime'], 'RatingScore'=>$rows['RatingScore'], 'comment'=>$comments);
    		}
    	}
    	else
	    	$data = array('error'=>1, 'error_msg'=>'User doesn\'t have any tags');

	    return $data;
    }



	// Return the Username for the given user id
	public function getNameFromId($id) {
		$result = mysql_query("Select Username From accounts Where AccountID = '$id'");
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
    public function addTag($oId, $name, $desc, $imgUrl, $loc, $lat, $lon, $cat) {
    	$result = mysql_query("INSERT INTO tags(OwnerID, Name, Description, ImageUrl, Location, Latitude, Longitude, CreatedDateTime, Category) VALUES(".$oId.", '".$name."', '".$desc."', '".$imgUrl."', '".$loc."', ".$lat.", ".$lon.", now(), '".$cat."')");
    	$resID = mysql_insert_id();
    	if ($result)
    		return $resID;
    	else
    		return false;
    }



    // Remove a tag from database
    public function removeTag($tId) {
        $result = mysql_query("Delete from tags Where TagId = '$tId'");
        return $result;
    }



    // Removes a friend from a given uID
    public function removeFriend($uID, $fID) {
    	$result = mysql_query("DELETE FROM friendassociations WHERE uID=".$uID." AND fID=".$fID);

    	if ($result)
	    	$data = array('success'=>1);	// removed users' relationship
	    else
	    	$data = array("error"=>1, "error_msg"=>"Error removing friend!");	// Error removing relationship

    	return $data;
    }



    // Add a friend to a given id
    public function addFriend($userId, $friendName) {
    	$query = mysql_query("SELECT AccountID FROM accounts WHERE Username='".$friendName."' OR EmailAddress='".$friendName."' LIMIT 1");
    	$num_of_rows = mysql_num_rows($query);

    	if ($num_of_rows > 0) {
	    	$friendID = mysql_result($query, 0);	// Friend exists, obtain their AccountID

	    	// Find out relationship b/w id(s)
	    	$result = mysql_query("SELECT * FROM friendassociations WHERE uID=".$userId." AND fID=".$friendID);
	    	$numOfAssociations = mysql_num_rows($result);

	    	if ($numOfAssociations > 0) {
		    	$data = array('error'=>1, 'error_msg'=>"Users are already friends!");	// Users are already friends
	    	} else {
	    		mysql_query("INSERT INTO friendassociations(uID, fID) VALUES(".$userId.", ".$friendID.")");
		    	$data = array('success'=>1);	// All good, send back 'success'
	    	}
    	} else {
    	     $data = array("error"=>2, "error_msg"=>"Could not find given user");	// Error retrieving user
    	}

    	return $data;
    }


    // Returns all the friends of the specified user ID
    public function getFriends($uID) {
	    $result = mysql_query("SELECT * FROM friendassociations WHERE uID=".$uID);
	    $num_of_rows = mysql_num_rows($result);

	    if ($num_of_rows > 0) {
	    	$data['success'] = 1;
		    while ($r = mysql_fetch_array($result)) {
		    	$query = mysql_query("SELECT * FROM accounts WHERE AccountID=".$r['fID']);
		    	while($row = mysql_fetch_array($query)) {
				    $data['friend'][] = array('AccountID'=>$row['AccountID'], 'Username'=>$row['Username'], 'Name'=>$row['Name'], 'EmailAddress'=>$row['EmailAddress'], 'Image'=>$row['Image'], 'Description'=>$row['Description'], 'Location'=>$row['Location'], 'Quote'=>$row['Quote'], 'Type'=>$row['Type'], 'Visibility'=>$row['Visibility'], 'RatingScore'=>$row['RatingScore']);
		    	}
		    }
	    }
	    else
		    $data = array('error'=>1, 'error_msg'=>'User has no friends.');

	    return $data;
    }




    // Edit profile function
    public function editProfile($imgUrl, $desc, $location, $quote, $uId) {
	    $result = mysql_query("UPDATE accounts SET Image='".$imgUrl."', Description='".$desc."', Location='".$location."', Quote='".$quote."' WHERE AccountID=".$uId);
	    if ($result) {
		    $res = mysql_query("SELECT AccountID, Username, EmailAddress, Image, Description, Location, Quote, Type, Visibility, CreatedDateTime, RatingScore FROM accounts WHERE AccountID=".$uId);
		    while ($row = mysql_fetch_array($res)) {
			    $data = array('success'=>1, 'AccountID'=>$row['AccountID'], 'Username'=>$row['Username'], 'Name'=>$row['Name'], 'EmailAddress'=>$row['EmailAddress'], 'Image'=>$row['Image'], 'Description'=>$row['Description'], 'Location'=>$row['Location'], 'Quote'=>$row['Quote'], 'Type'=>$row['Type'], 'Visibility'=>$row['Visibility'], 'RatingScore'=>$row['RatingScore']);
		    }
	    }
	    else
		    $data = array('error'=>1, 'error_msg'=>'Error editing profile.');

		return $data;
    }



    // Returns adventure details by its id
    public function getAdventureById($id) {
		$result = mysql_query("SELECT * FROM adventures WHERE ID=".$id);
		$num_of_rows = mysql_num_rows($result);

		if ($num_of_rows > 0) {
			while ($row = mysql_fetch_array($result)) {
				$data = array('success'=>1,'ID'=>$row['ID'],'OwnerID'=>$row['OwnerID'], 'Name'=>$row['Name'], 'Description'=>$row['Description'], 'Location'=>$row['Location'], 'Visibility'=>$row['Visibility'], 'CreatedDateTime'=>$row['CreatedDateTime']);
			}
		}
		else
			$data = array('error'=>1, 'error_msg'=>"This adventure does not exist!");

		return $data;
    }



    // Returns adventure details of adventures the user owns
    public function getAdventureByOwnerId($id) {
		$result = mysql_query("SELECT * FROM adventures WHERE OwnerID=".$id);
		$num_of_rows = mysql_num_rows($result);

		if ($num_of_rows > 0) {
			$data = array('success'=>1);
			while ($row = mysql_fetch_array($result)) {
				$data['adventure'][] = array('ID'=>$row['ID'],'OwnerID'=>$row['OwnerID'], 'Name'=>$row['Name'], 'Description'=>$row['Description'], 'Location'=>$row['Location'], 'Visibility'=>$row['Visibility'], 'CreatedDateTime'=>$row['CreatedDateTime']);
			}
		}
		else
			$data = array('error'=>1, 'error_msg'=>"You are not part of any adventures!");

		return $data;
    }




    // Returns all the tags attached to an adventure ID
    public function getAllAdventureTags($id) {
		$res = mysql_query("SELECT * FROM adventuretags WHERE AdvID=".$id);
		$nums = mysql_num_rows($res);

		if ($nums > 0) {
			$data = array('success'=>1);
			while ($row = mysql_fetch_array($res)) {
				$tagid = $row['TagID'];
				// Get all tag info
				$res2 = mysql_query("SELECT * FROM tags WHERE TagID=".$tagid);

				while ($row2 = mysql_fetch_array($res2)) {
					$data['tag'][] = array('TagID'=>$row2['TagID'],'OwnerID'=>$row2['OwnerID'], 'Name'=>$row2['Name'], 'Description'=>$row2['Description'], 'ImageUrl'=>$row2['ImageUrl'], 'Visibility'=>$row2['Visibility'], 'Location'=>$row2['Location'], 'Latitude'=>$row2['Latitude'], 'Longitude'=>$row2['Longitude'], 'Category'=>$row2['Category'], 'CreatedDateTime'=>$row2['CreatedDateTime'], 'RatingScore'=>$row2['RatingScore']);
				}
			}
		}
		else
			$data = array('error'=>1, 'error_msg'=>'This adventure does not have any tags associated with it yet.');

		return $data;
    }




    // Returns all the adventures and tags associated with it
    public function getAllAdventuresUserPartOf($id) {
		$res = mysql_query("SELECT * FROM adventures WHERE ID IN (SELECT DISTINCT(AdvID) FROM adventuretags WHERE TagID IN (SELECT TagID FROM tags WHERE OwnerID=".$id."))");
		$nums = mysql_num_rows($res);

		if ($nums > 0) {
			$data = array('success'=>1);

			while ($row = mysql_fetch_array($res)) {
				$tags = $this->getTags($row['ID']);
				$data['adventure'][] = array('ID'=>$row['ID'],'OwnerID'=>$row['OwnerID'], 'Name'=>$row['Name'], 'Description'=>$row['Description'], 'Location'=>$row['Location'], 'Visibility'=>$row['Visibility'], 'CreatedDateTime'=>$row['CreatedDateTime'], 'Tag'=>$tags);
			}
		}
		else
			$data = array('error'=>1, 'error_msg'=>'User is not part of any adventures.');

	    return $data;
    }


    // Internal function returns all the tags associated with an adventure id
	public function getTags($advid) {
		$tags = array();
		$query = mysql_query("SELECT TagID FROM adventuretags WHERE AdvID=".$advid);

		while ($x = mysql_fetch_array($query)) {
			$tagid = $x['TagID'];

			$y = mysql_query("SELECT * FROM tags WHERE TagID=".$tagid);
			while ($r = mysql_fetch_array($y)) {
				$comments = $this->getTagComments($r['TagID']);		// Grab all the comments for this tag
				$tags[] = array('TagID'=>$r['TagID'],'OwnerID'=>$r['OwnerID'], 'Name'=>$r['Name'], 'Description'=>$r['Description'], 'ImageUrl'=>$r['ImageUrl'], 'Visibility'=>$r['Visibility'], 'Location'=>$r['Location'], 'Latitude'=>$r['Latitude'], 'Longitude'=>$r['Longitude'], 'Category'=>$r['Category'], 'CreatedDateTime'=>$r['CreatedDateTime'], 'RatingScore'=>$r['RatingScore'], 'comment'=>$comments);
			}
		}
		return $tags;
	}





	// External & Internal Function returns all the comments attached to a tag id
	public function getTagComments($tagId) {
		$result = mysql_query("SELECT * FROM tagcomments WHERE ParentTagID=".$tagId);
		$comments = array();

		while ($row = mysql_fetch_array($result)) {
			$comments[] = array('ID'=>$row['ID'], 'ParentTagID'=>$row['ParentTagID'], 'Username'=>$row['Username'], 'Title'=>$row['Title'], 'Text'=>$row['Text'], 'CreatedDateTime'=>$row['CreatedDateTime'], 'RatingScore'=>$row['RatingScore']);
		}
		return $comments;
	}




	// Function returns individual tag object with all of its attached comments
	public function getAllInfoOnTag($id) {
		$result = mysql_query("SELECT * FROM tags WHERE TagID=".$id);
		$data = array('error'=>1, 'error_msg'=>'No tag found.');

		while ($tag = mysql_fetch_array($result)) {
			$comments = $this->getTagComments($id);
			$data = array('success'=>1, 'TagID'=>$tag['TagID'],'OwnerID'=>$tag['OwnerID'], 'Name'=>$tag['Name'], 'Description'=>$tag['Description'], 'ImageUrl'=>$tag['ImageUrl'], 'Visibility'=>$tag['Visibility'], 'Location'=>$tag['Location'], 'Latitude'=>$tag['Latitude'], 'Longitude'=>$tag['Longitude'], 'Category'=>$tag['Category'], 'CreatedDateTime'=>$tag['CreatedDateTime'], 'RatingScore'=>$tag['RatingScore'], 'comment'=>$comments);
		}

		return $data;
	}



	// Function adds a comment to a tag - returns tag with all of its comments
	function addTagComment($tID, $uName, $title, $comment) {
		$result = mysql_query("INSERT INTO tagcomments(ParentTagID, Username, Title, Text, CreatedDateTime) VALUES(".$tID.", '".$uName."', '".$title."', '".$comment."', now())");

		if ($result)
			$data = $this->getAllInfoOnTag($tID);
		else
			$data = array('error'=>1, 'error_msg'=>'An error has occurred!');

		return $data;
	}



	// Function removes a tag comment - returns tag with all of its updated comments
	function deleteTagComment($cID) {
		$query = mysql_query("SELECT ParentTagID FROM tagcomments WHERE ID=".$cID);
		$parentTag = mysql_result($query, 0);

		$result = mysql_query("DELETE FROM tagcomments WHERE ID=".$cID);

		if ($result)
			$data = $this->getAllInfoOnTag($parentTag);
		else
			$data = array('error'=>1, 'error_msg'=>'An error has occurred!');

		return $data;
	}

}

?>