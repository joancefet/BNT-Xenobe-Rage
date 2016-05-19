<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: option2.php

include "config/config.php";
if (checklogin () )
{
    die ();
}

global $l_opt2_title;
$title = $l_opt2_title;

if ($new_password_1 == $new_password_2 && $password == md5($current_password) && $new_password_1 != "")
{
	
	$shared_function = new shared();
	$ip_array = $shared_function->sortIP();
	$user_ip_address = $ip_array[0];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$user_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$cookie_session_id = md5($user_agent);
	$data = array('username'=>$username, 'password'=>$cookie_session_id,'user_id'=>$user_ship_id,'user_ip'=>$user_ip_address,'user_host'=>$user_host,'user_agent'=>$user_agent);
	$data=serialize($data);
	setcookie("userID", $data, time() + (3600*24), $gamepath, $gamedomain);
	
}


if (!preg_match("/^[\w]+$/", $newlang))
{
    $newlang = $default_lang;
}
else
{
    $lang = $_POST['newlang'];
}

// New database driven language entries
load_languages($db, $lang, array('option2', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'), $langvars, $db_logging);

####
##
## Need to re-write all of this 
##
####


include "header.php";
?>
<div class="tablecell content both-border">
	<div class="pad">
<?
bigtitle ();
   $resx = $db->Execute("SELECT password FROM {$db->prefix}account WHERE user_id='$user_ship_id'");
    db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);
    $accountinfo = $resx->fields;


####
##
## Set Password
##
####
$password_errors = array();
if(!empty($current_password) && ($new_password_1 == $new_password_2))
{
	if ($accountinfo['password'] != md5($current_password))
	{
    	array_push($password_errors, 'Your password doesnt match your current password');
		$flag = 1;
	}
	
	if (strlen($new_password_1) <= '8')
	{
		array_push($password_errors, 'Your password must be at least 8 characters long');
		$flag = 1;
	}
	elseif(!preg_match("#[0-9]+#",$new_password_1)) 
	{
		array_push($password_errors, 'Your password must contain numbers');
		$flag = 1;
	}
	elseif(!preg_match("#[a-z]+#",$new_password_1)) 
	{
		array_push($password_errors, 'Your password must contain 1 lower case character');
		$flag = 1;
	}
}
elseif ($accountinfo['password'] != md5($current_password))
{
	array_push($password_errors, 'You entered your password wrong, please try again.');
	$flag = 1;
}
elseif(!empty($new_password_1))
{
	array_push($password_errors, 'Your passwords do not match, please try again');
	$flag = 1;
}
/*Password Is Ok?*/
if (!empty($current_password) && ($flag == 0))
{
	$new_password_1 = md5($new_password_1);
    $res = $db->Execute("UPDATE {$db->prefix}account SET password='$new_password_1' WHERE user_id=$user_ship_id");
    db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
    if ($res)
    {
       echo $l_opt2_passchanged . "<br><br>";
    }
    else
    {
        echo $l_opt2_passchangeerr . "<br><br>";
    }
}
 else if (!empty($current_password))
{ ?>
	<h3>Error Changing Password</h3>
<?php
	if (!empty($password_errors))
	{
		echo '<ul>';
		foreach ($password_errors as $e)
		{
			echo '<li>' . $e . '</li>';
		}
		echo '</ul>';
	}
}

####
##
## Set Recovery Questions
##
####
$flag_recovery = 0;
$recovery_errors = array();
if(!empty($current_password_rec) && !empty($security_answer_1) && !empty($security_answer_2) && !empty($security_answer_3) && !empty($security_question_1) && !empty($security_question_2) && !empty($security_question_3) )
{
	if ($accountinfo['password'] != md5($current_password_rec))
	{
    	array_push($recovery_errors, 'Your password doesnt match your current password');
		$flag_recovery = 1;
	}
	
	if ((strlen($security_answer_1) <= '4') or (strlen($security_answer_2) <= '4') or (strlen($security_answer_3) <= '4') or (strlen($security_question_1) <= '4') or (strlen($security_question_2) <= '4') or (strlen($security_question_3) <= '4'))
	{
		array_push($recovery_errors, 'Please ensure you have entered valid security questions and answers. Hint: Ensure your questions answer is at least 5 characters long, and no blank entries allowed, all questions must be set.');
		$flag_recovery = 1;
	}
}
elseif ($accountinfo['password'] != md5($current_password_rec))
{
	array_push($recovery_errors, 'You entered your password wrong, please try again.');
	$flag_recovery = 1;
}
elseif(empty($security_answer_1) or empty($security_answer_2) or empty($security_answer_3) or empty($security_question_1) or empty($security_question_2) or empty($security_question_3) or 
is_null($security_answer_1) or is_null($security_answer_2) or is_null($security_answer_3) or is_null($security_question_1) or is_null($security_question_2) or is_null($security_question_3))
{
	array_push($recovery_errors, 'One or more of your recovery questions or answer has invalid input, please check again.');
	$flag_recovery = 1;
}
/*Password Is Ok?*/
if (!empty($current_password_rec) && ($flag_recovery == 0))
{
	/*Lets encrypt all the information, just some basic protection, need better though*/
	$security_answer_1 = md5($security_answer_1);
	$security_answer_2 = md5($security_answer_2);
	$security_answer_3 = md5($security_answer_3);
	
	$security_question_1 = $security_question_1;
	$security_question_2 = $security_question_2;
	$security_question_3 = $security_question_3;
    $res = $db->Execute("UPDATE {$db->prefix}account SET 
	personal_question_1='$security_question_1',
	personal_question_2='$security_question_2',
	personal_question_3='$security_question_3',
	personal_question_1_answer='$security_answer_1',
	personal_question_2_answer='$security_answer_2',
	personal_question_3_answer='$security_answer_3'
	 WHERE user_id=$user_ship_id");
    db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
    if ($res)
    {
       echo "You have set your recovery questions and answers<br><br>";
    }
    else
    {
        echo "An error occoured when trying to set new questions and answers<br><br>";
    }
}
 else if (!empty($current_password_rec))
{ ?>
	<h3>Error Setting Recovery Questions and Answers:</h3>
<?php
	if (!empty($recovery_errors))
	{
		echo '<ul>';
		foreach ($recovery_errors as $e)
		{
			echo '<li>' . $e . '</li>';
		}
		echo '</ul>';
	}
}


####
##
## Set Language
##
####

$res = $db->Execute("UPDATE {$db->prefix}ships SET lang='$lang' WHERE ship_id='$user_ship_id'");
db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
foreach ($avail_lang as $curlang)
{
    if ($lang == $curlang['file'])
    {
        $l_opt2_chlang = str_replace("[lang]", "$curlang[name]", $l_opt2_chlang);
        echo $l_opt2_chlang . "<p>";
        break;
    }
}

echo "<br>";
TEXT_GOTOMAIN();
?>
</div></div>
<?
include "footer.php";
?>
