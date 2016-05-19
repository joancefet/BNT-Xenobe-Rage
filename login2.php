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
// File: login2.php

include "config/config.php";
$manage_log = new manage_log();
// Test to see if server is closed to logins
$playerfound = false;

if (isset($_POST['handle']))
{
$username = $_POST['handle'];
}
elseif($_GET['handle'])
{
	$username = $_GET['handle'];
}
$pass = $_POST['pass'];
if ($username != NULL)
{
    $res = $db->Execute("SELECT * FROM {$db->prefix}account WHERE username='$username'");
    db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
    if ($res)
    {
        $playerfound = $res->RecordCount();
    }
    $account_information = $res->fields;
    $lang = $account_information['lang'];
}


if (!isset($_GET['lang']))
{
    $_GET['lang'] = null;
    $lang = $default_lang;
    $link = '';
}
else
{
    $lang = $_GET['lang'];
    $link = "?lang=" . $lang;
}

// New database driven language entries
load_languages($db, $lang, array('login2', 'login', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars, $db_logging);

// first placement of cookie - don't use updatecookie.
##
## TAKING OUT PASSWORD OUT OF THE COOKIE!!! REPLACING WITH I.P. ADDRESS temporarily. THIS IS A SECURITY RISK AND WILL BE REPLACED WITH SESSIONS
##
$shared_function = new shared();

$user_ship_id = $account_information['user_id'];
if ($server_closed)
{
    $title = $l_login_sclosed;
    include "header.php";
?>
<div class="tablecell content both-border">
	<div class="pad">
<?
    echo "<div style='text-align:center; color:#ff0; font-size:20px;'><br>The Server is currently closed!</div><br>\n";
    TEXT_GOTOLOGIN();
?>
</div></div>
<?
    include "footer.php";
    die();
}

$title = $l_login_title2;

// Check Banned
$banned = 0;

if (isset($account_information))
{
    $res = $db->Execute("SELECT * FROM {$db->prefix}ip_bans WHERE '$ip' LIKE ban_mask OR '$account_information[ip]' LIKE ban_mask");
    db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
    if ($res->RecordCount() != 0)
    {
        setcookie("userpass", "", 0, $gamepath, $gamedomain);
        setcookie("userpass", "", 0); // Delete from default path as well.
		setcookie("userID", "", 0, $gamepath, $gamedomain);
		setcookie("userID", "", 0);
        $banned = 1;
    }
}

include "header.php";
?>
<div class="tablecell content both-border">
	<div class="pad">
<?
bigtitle ();

if ($playerfound)
{
    if ($account_information['password'] == md5($pass))
    {

		$ip_array = $shared_function->sortIP();
		$user_ip_address = $ip_array[0];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$user_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		$cookie_session_id = md5($user_agent);
		
		$data = array('username'=>$username, 'password'=>$cookie_session_id,'user_id'=>$account_information['user_id'],'user_ip'=>$user_ip_address,'user_host'=>$user_host,'user_agent'=>$user_agent);
		$data=serialize($data);
		setcookie("userID", $data, time() + (3600*24), $gamepath, $gamedomain);

		
		$res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id='$account_information[user_id]'");
		db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
		$player_ship_info = $res->fields;
		TEXT_GOTOMAIN();
        header("Location: main.php"); 
		
        if ($player_ship_info['ship_destroyed'] == "N")
        {
			
			##New Log ##
			$manage_log->player_log($player_ship_info['ship_id'],1,'','','','',"<font color='#6190a5'>Low Priority</font>","<b>Logged In</b>");
            // player's ship has not been destroyed
            $stamp = date("Y-m-d H-i-s");
            $update = $db->Execute("UPDATE {$db->prefix}ships SET last_login='$stamp',ip_address='$ip' WHERE ship_id=$player_ship_info[ship_id]");
            db_op_result ($db, $update, __LINE__, __FILE__, $db_logging);
            $_SESSION['logged_in'] = true;
            TEXT_GOTOMAIN();
            header("Location: main.php"); // This redirect avoids any rendering for the user of login2. Its a direct transition, visually
        }
        else
        {
			## NEED NEW CHECK FOR SHIP DESTROYED!
            // player's ship has been destroyed
            if ($player_ship_info['dev_escapepod'] == "Y")
            {
                $resx = $db->Execute("UPDATE {$db->prefix}ships SET hull=0,engines=0,power=0,computer=0,sensors=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_ore=0,ship_organics=0,ship_energy=1000,ship_colonists=0,ship_goods=0,ship_fighters=100,ship_damage=0,on_planet='N',dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,ship_destroyed='N',dev_lssd='N' WHERE ship_id=$player_ship_info[ship_id]");
                db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);
                $l_login_died = str_replace("[here]", "<a href='main.php'>" . $l_here . "</a>", $l_login_died);
                echo $l_login_died;
            }
            else
            {
                echo "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.<br><br>";

                // Check if $newbie_nice is set, if so, verify ship limits
                if ($newbie_nice == "YES")
                {
                    $newbie_info = $db->Execute("SELECT hull,engines,power,computer,sensors,armor,shields,beams,torp_launchers,cloak FROM {$db->prefix}ships WHERE ship_id='$player_ship_info[ship_id]' AND hull<='$newbie_hull' AND engines<='$newbie_engines' AND power<='$newbie_power' AND computer<='$newbie_computer' AND sensors<='$newbie_sensors' AND armor<='$newbie_armor' AND shields<='$newbie_shields' AND beams<='$newbie_beams' AND torp_launchers<='$newbie_torp_launchers' AND cloak<='$newbie_cloak'");
                    db_op_result ($db, $newbie_info, __LINE__, __FILE__, $db_logging);
                    $num_rows = $newbie_info->RecordCount();

                    if ($num_rows)
                    {
                        echo "<br><br>" . $l_login_newbie . "<br><br>";
                        $resx = $db->Execute("UPDATE {$db->prefix}ships SET hull=0,engines=0,power=0,computer=0,sensors=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_ore=0,ship_organics=0,ship_energy=1000,ship_colonists=0,ship_goods=0,ship_fighters=100,ship_damage=0,credits=1000,on_planet='N',dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,ship_destroyed='N',dev_lssd='N' WHERE ship_id=$player_ship_info[ship_id]");
                        db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);

                        $l_login_newlife = str_replace("[here]", "<a href='main.php'>" . $l_here . "</a>", $l_login_newlife);
                        echo $l_login_newlife;
                    }
                    else
                    {
                        echo "<br><br>" . $l_login_looser . "<br><br>" . $l_login_looser2;
                    }

                } // End if $newbie_nice
                else
                {
                    echo "<br><br>" . $l_login_looser . "<br><br>" . $l_login_looser2;
                }
            }
        }
    }
	elseif($_POST['change_pass']=="true")
	{
		$validate_fail = 0;
		$password_errors = array();
		if (array_key_exists('new_password_1', $_POST))
		{
			$new_password_1 = $_POST['new_password_1'];
		}
		if (array_key_exists('new_password_2', $_POST))
		{
			$new_password_2 = $_POST['new_password_2'];
		}
		$new_password_1= trim($new_password_1);
		$new_password_1 = stripslashes($new_password_1);
		$new_password_1 = htmlspecialchars($new_password_1);
		
		$resx = $db->Execute("SELECT user_id,personal_question_1_answer,personal_question_2_answer,personal_question_3_answer FROM {$db->prefix}account WHERE username='$username'");
		db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);
		$accountinfo = $resx->fields;
		if($accountinfo['personal_question_1_answer']!=md5($_POST['security_question_1']))
		{
			$validate_fail = 1;	
		}
		elseif($accountinfo['personal_question_2_answer']!=md5($_POST['security_question_2']))
		{
			$validate_fail = 1;	
		}
		elseif($accountinfo['personal_question_3_answer']!=md5($_POST['security_question_3']))
		{
			$validate_fail = 1;	
		}
		elseif(!preg_match("#[0-9]+#",$new_password_1)) 
		{
			array_push($password_errors, 'Your password must contain numbers');
			$validate_fail = 1;	
		}
		elseif(!preg_match("#[a-z]+#",$new_password_1)) 
		{
			array_push($password_errors, 'Your password must contain 1 lower case character');
			$validate_fail = 1;	
		}
		elseif($new_password_1 != $new_password_2)
		{
			array_push($password_errors, 'Your passwords do not match');
			$validate_fail = 1;	
		}

		if($validate_fail == 0)
		{
			$new_password_1 = md5($new_password_1);
			$res = $db->Execute("UPDATE {$db->prefix}account SET password='$new_password_1' WHERE username='$username'");
			db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
			if ($res)
			{
				 echo "Password saved. <a href='index.php'>Click Here</a> to login";
				/*Save New Password*/
			}
			else
			{
				echo "An error occoured when trying to save the new password, try again later<br><br>";
			}		
		}
		else
		{
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
	}
	elseif($_POST['attempt']=="true")
	{
		####
		##
		## CHECK AND VERIFY
		##
		####
		$validate_fail = 0;
		$resx = $db->Execute("SELECT user_id,personal_question_1_answer,personal_question_2_answer,personal_question_3_answer FROM {$db->prefix}account WHERE username='$username'");
		db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);
		$accountinfo = $resx->fields;
		if($accountinfo['personal_question_1_answer']!=md5($_POST['security_question_1']))
		{
			$validate_fail = 1;	
		}
		elseif($accountinfo['personal_question_2_answer']!=md5($_POST['security_question_2']))
		{
			$validate_fail = 1;	
		}
		elseif($accountinfo['personal_question_3_answer']!=md5($_POST['security_question_3']))
		{
			$validate_fail = 1;	
		}
		
		if($validate_fail == 0)
		{
			?>
			<h3>Change your password</h3>
			<div class="general-table-container">
			<?
			
			echo "<form action=login2.php method=post>";
			echo "<table><body>";
			echo "<tr><td></td><td></td></tr>";
			echo "<tr>";
			echo "<td>New Password:</td>";
			echo "<td><input type=password name=new_password_1 size=20 maxlength=20 value=\"\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>New Password (Again):</td>";
			echo "<td><input type=password name=new_password_2 size=20 maxlength=20 value=\"\"></td>";
			echo "</tr>";
			echo "</tbody></table></div>\n";
			echo "<input type='hidden' name='change_pass' value='true'>";
			/*Lets reverify on the password change too, in case of any workarounds*/
			echo "<input type='hidden' name='handle' value='".$username."'>";
			echo "<input type='hidden' name='security_question_1' value='".$_POST['security_question_1']."'>";
			echo "<input type='hidden' name='security_question_2' value='".$_POST['security_question_2']."'>";
			echo "<input type='hidden' name='security_question_3' value='".$_POST['security_question_3']."'>";
			echo "<br/><input type=submit value=Submit><br/></form>";
			
			
			
		}
		else
		{
			echo "You failed to pass the validation, this has been logged. <a href='index.php'>Click Here</a> to go back to the main page.";
			
			## LOG IT ##
			adminlog($db, (1000 + LOG_BADLOGIN), "{$ip}|{$username}|{$pass}");
			##New Log ##
			$manage_log->player_log($accountinfo['user_id'],3,'','','','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
		}
		
		
	}
    elseif($_GET['recover']=="true")
	{
		####
		##
		## ACCOUNT RECOVERY
		##
		####
		$resx = $db->Execute("SELECT user_id,personal_question_1,personal_question_2,personal_question_3 FROM {$db->prefix}account WHERE username='$username'");
		db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);
		$accountinfo = $resx->fields;
		$recovery_question_1 = $accountinfo['personal_question_1'];
		$recovery_question_2 = $accountinfo['personal_question_2'];
		$recovery_question_3 = $accountinfo['personal_question_3'];
		

	?>
    <h3>Recovering Account!</h3>
    <div class="general-table-container">
    <?	
		echo "<form action=login2.php?recover=true method=post>";
		echo "<table><body>";
		echo "<tr><td>Your Question</td><td>Please enter your answer</td></tr>";

		echo "<tr>";
		echo "<td>".$recovery_question_1."</td>";
		echo "<td><input type=text name=security_question_1 size=20 maxlength=20 value=\"\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td>".$recovery_question_2."</td>";
		echo "<td><input type=text name=security_question_2 size=20 maxlength=20 value=\"\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td>".$recovery_question_3."</td>";
		echo "<td><input type=text name=security_question_3 size=20 maxlength=20 value=\"\"></td>";
		echo "</tr>";
		echo "</tbody></table></div>\n";
		echo "<input type='hidden' name='handle' value='".$username."'>";
		echo "<input type='hidden' name='attempt' value='true'>";
		echo "<br/><input type=submit value=Submit><br/>";
		echo "</form><br>";
		?>
        <p>Please ensure you fill in all the answers!</p>
		</div>
		<?
	}
	else
    {
		
		/*Password is incorrect, recover account maybe?*/
		
        // password is incorrect
		echo "Forgotten your password?<br/><br/>";
		echo "The password you entered is incorrect.<br/><br/>";
		echo "If you have forgotten your password, <a href='login2.php?recover=true&handle=".$username."'>Click Here</a> to recover your account.<br/><br/>";
		echo "Otherwise,  <a href='index.php'>Click Here</a> to try again. Attempt logged with IP address of ".$ip ."...";
		
		## LOG IT ##
        adminlog($db, (1000 + LOG_BADLOGIN), "{$ip}|{$username}|{$pass}");
		##New Log ##
		$manage_log->player_log($player_ship_info['ship_id'],3,'','','','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
    }
}
else
{
    $l_login_noone = str_replace("[here]", "<a href='new_fb.php" . $link . "'>" . $l_here . "</a>", $l_login_noone);
    echo "<strong>" . $l_login_noone . "</strong><br>";
}
?>
</div></div>
<?
include "footer.php";
?>
