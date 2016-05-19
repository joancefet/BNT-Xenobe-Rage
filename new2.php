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
// File: new2.php

require_once($_SERVER['DOCUMENT_ROOT']."/config/config.php");

$title = "User Registration";
include "header.php";

$status = false;
$errors = array();
?>

<div class="tablecell content both-border">
	<div class="pad">
<?
bigtitle ();

/*If account creation is closed, do not show the page!*/
if ($account_creation_closed)
{
    die ("Server Is Currently Closed To New Players");
}
##
##
## New Registration Process
##
##


if ($request)
{

}
else
{
	/*BUILD A NORMAL ACCOUNT WITHOUT FACEBOOK*/
	echo "Building Default Account";
	
	$usr_created_handle = NULL;
	$usr_created_shipname = NULL;
	$usr_created_character = NULL;
	$usr_created_email = NULL;
	$usr_created_email2 = NULL;
	$usr_selected_language = NULL;
	$usr_created_password = NULL;
	$usr_created_password2 = NULL;
	$security_answer_1 = NULL;
	$security_answer_2 = NULL;
	$security_answer_3 = NULL;
	$security_question_1 = NULL;
	$security_question_2 = NULL;
	$security_question_3 = NULL;
	
	if (array_key_exists('handle', $_POST))
	{
		$usr_created_handle = $_POST['handle'];
	}
	if (array_key_exists('pass', $_POST))
	{
		$usr_created_password = $_POST['pass'];
	}
	if (array_key_exists('pass2', $_POST))
	{
		$usr_created_password2 = $_POST['pass2'];
	}
	if (array_key_exists('character', $_POST))
	{
		$usr_created_character = $_POST['character'];
	}
	if (array_key_exists('email', $_POST))
	{
		$usr_created_email = $_POST['email'];
	}
	if (array_key_exists('email2', $_POST))
	{
		$usr_created_email2 = $_POST['email2'];
	}
	if (array_key_exists('ship', $_POST))
	{
		$usr_created_shipname = $_POST['ship'];
	}
	
	if (array_key_exists('security_answer_1', $_POST))
	{
		$security_answer_1 = $_POST['security_answer_1'];
	}
	if (array_key_exists('security_answer_2', $_POST))
	{
		$security_answer_2 = $_POST['security_answer_2'];
	}
	if (array_key_exists('security_answer_3', $_POST))
	{
		$security_answer_3 = $_POST['security_answer_3'];
	}
	
	if (array_key_exists('security_question_1', $_POST))
	{
		$security_question_1 = $_POST['security_question_1'];
	}
	if (array_key_exists('security_question_2', $_POST))
	{
		$security_question_2 = $_POST['security_question_2'];
	}
	if (array_key_exists('security_question_3', $_POST))
	{
		$security_question_3 = $_POST['security_question_3'];
	}	
	if (array_key_exists('lang', $_POST))
	{
		$usr_selected_language = $_POST['lang'];
	}
	else
	{
		$usr_selected_language = $default_lang;
	}

	$usr_created_password = trim($usr_created_password);
	$usr_created_password = stripslashes($usr_created_password);
	$usr_created_password = htmlspecialchars($usr_created_password);
	
	$usr_created_handle = htmlspecialchars ($usr_created_handle);
	$usr_created_handle = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $usr_created_handle);
	$usr_created_email = htmlspecialchars ($usr_created_email);
	$usr_created_email2 = htmlspecialchars ($usr_created_email2);
	//$usr_created_email = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $usr_created_email);
	$usr_created_shipname = htmlspecialchars ($usr_created_shipname);
	$usr_created_shipname = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $usr_created_shipname);
	$usr_created_character = htmlspecialchars ($usr_created_character);
	$usr_created_character = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $usr_created_character);	
	
	$security_question_1 = htmlspecialchars ($security_question_1);
	$security_answer_1 = htmlspecialchars ($security_answer_1);
	$security_question_2 = htmlspecialchars ($security_question_2);
	$security_answer_2 = htmlspecialchars ($security_answer_2);
	$security_question_3 = htmlspecialchars ($security_question_3);
	$security_answer_3 = htmlspecialchars ($security_answer_3);
	
	$security_question_1 = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $security_question_1);
	$security_answer_1 = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $security_answer_1);
	$security_question_2 = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $security_question_2);
	$security_answer_2 = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $security_answer_2);
	$security_question_3 = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $security_question_3);
	$security_answer_3 = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $security_answer_3);
	
	if (!get_magic_quotes_gpc())
	{
		$usr_created_handle = addslashes ($usr_created_handle);
		$usr_created_email = addslashes ($usr_created_email);
		$usr_created_email2 = addslashes ($usr_created_email2);
		$usr_created_shipname = addslashes ($usr_created_shipname);
		$usr_created_character = addslashes ($usr_created_character);
	}
	####
	## BUILD ACCOUNT
	####
	
	####
	## Step One, check for duplicates and conflicts
	####
	##	NOTES:
	##	Add in username support at DB level
	##
	###
	
	$result = $db->Execute ("SELECT email, character_name, ship_name FROM {$db->prefix}ships WHERE email='$usr_created_email' OR character_name='$usr_created_character' OR ship_name='$usr_created_shipname'");
	db_op_result ($db, $result, __LINE__, __FILE__, $db_logging);
	$flag = 0;
	####
	## Checking for blank entries
	####	
	if ($usr_created_handle == '' || $usr_created_shipname == '' || $usr_created_character == '' || $usr_created_email == '' || $usr_created_password == '')
	{
		array_push($errors, 'Please ensure the form is complete before submitting');
		$flag = 1;
	}
	####
	## Checking for duplicates
	####
	while (!$result->EOF)
	{
		$row = $result->fields;
		if (strtolower ($row['email']) == strtolower ($usr_created_email))
		{
			array_push($errors, $usr_created_email.' - Email allready in use');
			$flag = 1;
		}
		if (strtolower ($row['character_name']) == strtolower($usr_created_character))
		{
			array_push($errors, $usr_created_character.' - Character Name Allready In Use');
			$flag = 1;
		}
		if (strtolower ($row['ship_name']) == strtolower ($usr_created_shipname))
		{
			array_push($errors, $usr_created_shipname.' - Ship Name Allready In Use');
			$flag = 1;
		}
		$result->MoveNext();
	}
	####
	## Checking email
	####
	if($usr_created_email == $usr_created_email2)
	{
		
	}
	else
	{
			array_push($errors, $usr_created_email.' does not match '.$usr_created_email2);
			$flag = 1;
	}
	####
	## Checking password
	####
	
	if(!empty($usr_created_password) && ($usr_created_password == $usr_created_password2))
	{
		if (strlen($usr_created_password) <= '8') {
		   array_push($errors, 'Your password must be at least 8 characters long');
		   $flag = 1;
		}
		elseif(!preg_match("#[0-9]+#",$usr_created_password)) {
			array_push($errors, 'Your password must contain numbers');
			$flag = 1;
		}
		elseif(!preg_match("#[a-z]+#",$usr_created_password)) {
			array_push($errors, 'Your password must contain 1 lower case character');
			$flag = 1;
		}
	}
	elseif(!empty($usr_created_password)) {
		array_push($errors, 'Your passwords do not match, please try again');
		$flag = 1;
	}
	elseif ((strlen($security_answer_1) <= '4') or (strlen($security_answer_2) <= '4') or (strlen($security_answer_3) <= '4') or (strlen($security_question_1) <= '4') or (strlen($security_question_2) <= '4') or (strlen($security_question_3) <= '4'))
	{
		array_push($errors, 'Please ensure you have entered valid security questions and answers. Hint: Ensure your questions answer is at least 5 characters long, and no blank entries allowed, all questions must be set.');
		$flag = 1;
	}
	
	
	
	
	

	
	
	
	
	####
	## Step Two, build account
	####
	
	if ($flag == 0)
	{
		$shared_function = new shared();
		$time_date_full = $shared_function->manage_time("full");
		$db = db::init();
		$fbId = 0;
		$user = new user();	
		
		$security_answer_1 = md5($security_answer_1);
		$security_answer_2 = md5($security_answer_2);
		$security_answer_3 = md5($security_answer_3);
		
		$sth = $db->prepare("SELECT * FROM ".$db_prefix."account WHERE username = ?");
		$sth->execute(array($usr_created_handle));
		if (!$sth->fetch())
		{

			$location = "earth";
			$account_id = rand(1,999).rand(1,999).rand(1,999);
			$sql = "INSERT INTO ".$db_prefix."account (facebook_id, username, password, name, email, location, gender, ip, registration_date, handle, active_ship, user_id, personal_question_1,personal_question_2,personal_question_3,personal_question_1_answer,personal_question_2_answer,personal_question_3_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$data = array(
				$fbId,
				$usr_created_handle,
				md5($usr_created_password),
				$usr_created_character,
				$usr_created_email,
				$location,
				'null gender',
				$_SERVER['REMOTE_ADDR'],
				$time_date_full,
				$usr_created_character,
				0,
				$account_id,
				$security_question_1,
				$security_question_2,
				$security_question_3,
				$security_question_1,
				$security_question_2,
				$security_question_3
				);
			$sth = $db->prepare($sql);
		}
		else
		{
			#echo "username in use";	
		}
			
		$sth = $db->prepare($sql);
				if ($sth->execute($data))
				{
					$sql_manager = new manage_table();
					/*
					Now Create Ship (need to code in a way to allow multiple ships - future feature....)
					*/
					##
					
					##get user id from the newly created account
					$user_account_id = $sql_manager->find_account_id($usr_created_handle);
					####
					##create ship from new id
					####
					
					$create_ship = $sql_manager->create_user_space_ship($user_account_id,$usr_created_shipname,$usr_created_character,$usr_created_email,$time_date_full,$_SERVER['REMOTE_ADDR']);
					
					if($create_ship)
					{
						//log_move ($db, $user_account_id, 0); // A new player is placed into sector 0. Make sure his movement log shows it, so they see it on the galaxy map.
						####
						## Set up players Zones
						####
						####
						## Set up players IGB account
						####
						$create_new_zone = $sql_manager->create_new_zone($user_account_id,$usr_created_character);
						if ($create_new_zone)
						{
							$create_new_igb = $sql_manager->create_new_igb($user_account_id);
							if ($create_new_igb)
							{
								$log_manager = new manage_log();
								$log_manager->player_log($user_account_id,30,'','','','',"<font color='#6190a5'>Low Priority</font>","<b>Account Created</b>");
								?>
								<h1>Success</h1>
								<p>
									Well.... what are you waiting for ....!!
									<a href="index.php">Start Playing XR</a>
								</p>
								<?php 
							}
							else
							{
								#Unable to create a IGB for the user
								array_push($errors, 'Error Connecting to DB');
								die();
								
							}  
						}
						else
						{
							#Unable to create a new zone for the user
							array_push($errors, 'Error Connecting to DB');
							die();
							
						}        
					}
					else
					{
						#Unable to save the account
						array_push($errors, 'Error Connecting to DB');
						die();
						
					}
				}
				else
				{
					#Cant connect to the DB
					array_push($errors, 'Error Connecting to DB');
				}			
		}
		else
		{
			$l_new_err = str_replace ("[here]", "<a href='new.php'>" . $l_here . "</a>",$l_new_err);
			echo $l_new_err;
		}
	
/*End of user account creation*/
}
/*-----------------------------------------*/
		

		
		// $username = $_POST['username']; // This needs to STAY before the db query
		
if ($flag == 0)
{}
 else
{ ?>
	<h1>Errors occured during registration.</h1>
	<p>Please try again.</p>
<?php
	if (!empty($errors))
	{
		echo '<ul>';
		foreach ($errors as $e)
		{
			echo '<li>' . $e . '</li>';
		}
		echo '</ul>';
	}
}
?>
    </div>
    <div class="footer">
         <div class="github"><a href="https://github.com/xgermz/xenoberage"><div class="logo-github"></div></a></div>
        <div class="copyright"><span class="bolder">Xenobe Rage</span> &copy;2012 - 2014 David Dawson. All rights reserved.<br /><span class="bolder">Blacknova Traders</span> &copy;2000-2012 Ron Harwood &amp; the BNT Dev team. All rights reserved.</div>
    </div>
</div>
            <?php
/*






# Get the user supplied post vars.
$username  = null;
$shipname  = null;
$character = null;
if (array_key_exists('character', $_POST))
{
    $character  = $_POST['character'];
}

if (array_key_exists('shipname', $_POST))
{
    $shipname   = $_POST['shipname'];
}

if (array_key_exists('username', $_POST))
{
    $username   = $_POST['username'];
}

if (array_key_exists('lang', $_POST))
{
    $lang   = $_POST['lang'];
}
else
{
    $lang = $default_lang;
}

$character = htmlspecialchars ($character);
$shipname = htmlspecialchars ($shipname);
$character = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $character);
$shipname = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $shipname);

// $username = $_POST['username']; // This needs to STAY before the db query

if (!get_magic_quotes_gpc())
{
    $username = addslashes ($username);
    $character = addslashes ($character);
    $shipname = addslashes ($shipname);
}

$result = $db->Execute ("SELECT email, character_name, ship_name FROM {$db->prefix}ships WHERE email='$username' OR character_name='$character' OR ship_name='$shipname'");
db_op_result ($db, $result, __LINE__, __FILE__, $db_logging);
$flag = 0;

if ($username == '' || $character == '' || $shipname == '' )
{
    echo $l_new_blank . '<br>';
    $flag = 1;
}

while (!$result->EOF)
{
    $row = $result->fields;
    if (strtolower ($row['email']) == strtolower ($username))
    {
        echo "$l_new_inuse  $l_new_4gotpw1 <a href=mail.php?mail=$username>$l_clickme</a> $l_new_4gotpw2<br>";
        $flag = 1;
    }
    if (strtolower ($row['character_name']) == strtolower($character))
    {
        $l_new_inusechar=str_replace("[character]", $character, $l_new_inusechar);
        echo $l_new_inusechar . '<br>';
        $flag = 1;
    }
    if (strtolower ($row['ship_name']) == strtolower ($shipname))
    {
        $l_new_inuseship = str_replace ("[shipname]", $shipname, $l_new_inuseship);
        echo $l_new_inuseship . '<br>';
        $flag = 1;
    }
    $result->MoveNext();
}

if ($flag == 0)
{
    // Insert code to add player to database
    $makepass = "";
    $syllables = "er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
    $syllable_array = explode (",", $syllables);
    for ($count=1; $count<=4; $count++)
    {
        if (mt_rand ()%10 == 1)
        {
            $makepass .= sprintf("%0.0f",(mt_rand ()%50)+1);
        }
        else
        {
            $makepass .= sprintf("%s", $syllable_array[mt_rand ()%62]);
        }
    }
    $stamp=date("Y-m-d H:i:s");
    $query = $db->Execute("SELECT MAX(turns_used + turns) AS mturns FROM {$db->prefix}ships");
    db_op_result ($db, $query, __LINE__, __FILE__, $db_logging);
    $res = $query->fields;

    $mturns = $res['mturns'];

    if ($mturns > $max_turns)
    {
        $mturns = $max_turns;
    }

    $result2 = $db->Execute("INSERT INTO {$db->prefix}ships (ship_name, ship_destroyed, character_name, password, email, armor_pts, credits, ship_energy, ship_fighters, turns, on_planet, dev_warpedit, dev_genesis, dev_beacon, dev_emerwarp, dev_escapepod, dev_fuelscoop, dev_minedeflector, last_login, ip_address, trade_colonists, trade_fighters, trade_torps, trade_energy, cleared_defences, lang, dev_lssd)
                             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array ($shipname, 'N', $character, $makepass, $username, $start_armor, $start_credits, $start_energy, $start_fighters, $mturns, 'N', $start_editors, $start_genesis, $start_beacon, $start_emerwarp, $escape, $scoop, $start_minedeflectors, $stamp, $ip, 'Y', 'N', 'N', 'Y', NULL, $lang, $start_lssd));
    db_op_result ($db, $result2, __LINE__, __FILE__, $db_logging);

    if (!$result2)
    {
        echo $db->ErrorMsg() . "<br>";
    }
    else
    {
        $result2 = $db->Execute("SELECT ship_id FROM {$db->prefix}ships WHERE email='$username'");
        db_op_result ($db, $result2, __LINE__, __FILE__, $db_logging);

        $shipid = $result2->fields;

        // To do: build a bit better "new player" message
        $l_new_message = str_replace("[pass]", $makepass, $l_new_message);
        $l_new_message = str_replace("[ip]", $ip, $l_new_message);

        # Some reason \r\n is broken, so replace them now.
        $l_new_message = str_replace('\r\n', "\r\n", $l_new_message);

        $link_to_game = "http://";
        $link_to_game .= ltrim($gamedomain,".");// Trim off the leading . if any
        //$link_to_game .= str_replace($_SERVER['DOCUMENT_ROOT'],"",dirname(__FILE__));
        $link_to_game .= $gamepath;
        mail("$username", "$l_new_topic", "$l_new_message\r\n\r\n$link_to_game","From: $admin_mail\r\nReply-To: $admin_mail\r\nX-Mailer: PHP/" . phpversion());

        log_move ($db, $shipid['ship_id'], 0); // A new player is placed into sector 0. Make sure his movement log shows it, so they see it on the galaxy map.
        $resx = $db->Execute("INSERT INTO {$db->prefix}zones VALUES(NULL,'$character\'s Territory', $shipid[ship_id], 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
        db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);

        $resx = $db->Execute("INSERT INTO {$db->prefix}ibank_accounts (ship_id,balance,loan) VALUES($shipid[ship_id],0,0)");
        db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);

        if ($display_password)
        {
            echo $l_new_pwis . " " . $makepass . "<br><br>";
        }

        $l_new_pwsent=str_replace("[username]", $_POST['username'], $l_new_pwsent);
        echo $l_new_pwsent . '<br><br>';
        echo "<a href=index.php" . $link . ">$l_clickme</A> $l_new_login";
    }
}
else
{
    $l_new_err = str_replace ("[here]", "<a href='new.php'>" . $l_here . "</a>",$l_new_err);
    echo $l_new_err;
}

*/
?>


</div></div>
<?
include "footer.php";
?>
