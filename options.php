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
// File: options.php

include "config/config.php";

// New database driven language entries
load_languages($db, $lang, array('options', 'common', 'global_includes', 'global_funcs', 'footer'), $langvars, $db_logging);
updatecookie ();

$body_class = 'options';
$title = $l_opt_title;
include "header.php";

if (checklogin () )
{
    die ();
}
   $resx = $db->Execute("SELECT personal_question_1,personal_question_2,personal_question_3 FROM {$db->prefix}account WHERE user_id='$user_ship_id'");
    db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);
    $accountinfo = $resx->fields;

$recovery_question_1 = NULL;
if(!empty($accountinfo['personal_question_1']))
{$recovery_question_1 = $accountinfo['personal_question_1'];
}
$recovery_question_2 = NULL;
if(!empty($accountinfo['personal_question_2']))
{$recovery_question_2 = $accountinfo['personal_question_2'];
}
$recovery_question_3 = NULL;
if(!empty($accountinfo['personal_question_3']))
{$recovery_question_3 = $accountinfo['personal_question_3'];
}

?>
<div class="tablecell content both-border">
	<div class="pad">
<?
bigtitle ();

$res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id='$user_ship_id'");
$playerinfo = $res->fields;
echo "<h3>Change Your Password</h3>";
	?>
    <div class="general-table-container">
    <?	
echo "<form action=option2.php method=post>";
echo "<table><body>";
echo "<tr><td></td><td></td></tr>";
echo "<tr>";
echo "<td>Current Password</td>";
echo "<td><input type=password name=current_password size=20 maxlength=20 value=\"\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>New Password:</td>";
echo "<td><input type=password name=new_password_1 size=20 maxlength=20 value=\"\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>New Password (Again):</td>";
echo "<td><input type=password name=new_password_2 size=20 maxlength=20 value=\"\"></td>";
echo "</tr>";
echo "</tbody></table></div>\n";
echo "<br/><input type=submit value=Submit><br/>";
echo "<h3>Set Account Recovery Questions:</h3>";
echo "<p>You may set your own account security questions and answers, please ensure you make a copy of these somwhere, or they are memorable enough for you to remember. When recovering your account you will be presented with 1 of these questions at random, so please ensure they are all filled in.</p><br/>";
	?>
    <div class="general-table-container">
    <?	
echo "<form action=option2.php method=post>";
echo "<table><body>";
echo "<tr><td></td><td></td></tr>";
echo "<tr>";
echo "<td>Current Password</td>";
echo "<td><input type=password name=current_password_rec size=20 maxlength=20 value=\"\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Security Question 1:</td>";
echo "<td><input type=text name=security_question_1 size=20 maxlength=190 value=\"".$recovery_question_1."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Security Answer 1:</td>";
echo "<td><input type=text name=security_answer_1 size=16 maxlength=190 value=\"\"></td>";
echo "</tr>";

echo "<tr>";
echo "<td>Security Question 2:</td>";
echo "<td><input type=text name=security_question_2 size=20 maxlength=190 value=\"".$recovery_question_2."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Security Answer 2:</td>";
echo "<td><input type=text name=security_answer_2 size=16 maxlength=190 value=\"\"></td>";
echo "</tr>";

echo "<tr>";
echo "<td>Security Question 3:</td>";
echo "<td><input type=text name=security_question_3 size=20 maxlength=190 value=\"".$recovery_question_3."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Security Answer 3:</td>";
echo "<td><input type=text name=security_answer_3 size=16 maxlength=190 value=\"\"></td>";
echo "</tr>";
echo "</tbody></table></div>\n";
echo "<br/><input type=submit value=Submit><br/>";



echo "<h3>Select Your Language</h3>";
	?>
    <div class="general-table-container">
    <?
echo "<table><body>";
echo "<tr><td></td><td></td></tr>";
echo "<tr>";
echo "<td>Choose One:</td><td><select name=newlang>";

foreach ($avail_lang as $curlang)
{
    if ($curlang['file'] == $playerinfo['lang'])
    {
        $selected = "selected";
    }
    else
    {
        $selected = "";
    }
    echo "<option value=" . $curlang['file'] . " " . $selected . ">" . $curlang['name'] . "</option>";
}

echo "</select></td>";
echo "</tr>";
echo "</tbody></table></div>\n";
echo "<br/><input type=submit value=Submit><br/>";
echo "</form><br>";

TEXT_GOTOMAIN ();
?>
</div></div>
<?
include "footer.php";
?>
