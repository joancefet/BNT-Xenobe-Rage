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

include "config/config.php";

//require_once($_SERVER['DOCUMENT_ROOT']."/classes/check.facebook.login.php");

	$user = new user();
	$facebook = new facebook(array(
	  'appId'  => FB_ID,
	  'secret' => FB_SECRET,
	));
// New database driven language entries
load_languages($db, $lang, array('new', 'login', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars, $db_logging);

$title = "User Registration";
include "header.php";
?>


<div class="tablecell content both-border">
	<div class="pad">
<?
bigtitle ();
?>

					<div id="fb-root"></div>
       				 <div class="half float_l">
    <form action="new2.php" method="post">
    <div class="login-container">
    	<div class="login-option">
        	<label for="handle">
            	Username
                <span class="hint">Your Account Username</span>
            </label>
        	<input type="handle" id="handle" name="handle" size="20" maxlength="255">
        </div>
        <div class="clear"></div>
		<div class="login-option">
        	<label for="email">
            	Email Address
                <span class="hint"></span>
            </label>
        	<input type="handle" id="email" name="email" size="20" maxlength="255">
        </div>
        <div class="clear"></div>
		<div class="login-option">
        	<label for="email2">
            	Email Address
                <span class="hint">Confirm Your Address</span>
            </label>
        	<input type="handle" id="email2" name="email2" size="20" maxlength="255">
        </div>
        <div class="clear"></div>
        <div class="login-option">
            <label for="pass">
            	Password
            	<span class="hint">Remember to include 0-9,a-z,A-Z?</span>
            </label>
            <input type="password" id="pass" name="pass" size="20" maxlength="255">
            </div>
        <div class="clear"></div>
        <div class="login-option">
            <label for="pass2">
            	Password
            	<span class="hint">Repeat the Password?</span>
            </label>
            <input type="password" id="pass2" name="pass2" size="20" maxlength="255">
            </div>
        <div class="clear"></div>
    	<div class="login-option">
        	<label for="character">
            	Character Name
                <span class="hint">Your Characters Name</span>
            </label>
        	<input type="handle" id="character" name="character" size="20" maxlength="255">
        </div>
        <div class="clear"></div>
    	<div class="login-option">
        	<label for="ship">
            	Ship Name
                <span class="hint">Your Ships Name</span>
            </label>
        	<input type="handle" id="ship" name="ship" size="20" maxlength="255">
        </div>
        <div class="clear"></div>

        <br/><br/>
    	<div class="login-option">
        	<label for="ship">
            	Recovery Question 1
                <span class="hint">A personal question only youd be able to answer!</span>
            </label>
        	<input type="handle" id="security_question_1" name="security_question_1" size="20" maxlength="190">
        </div>
        <div class="clear"></div>
    	<div class="login-option">
        	<label for="ship">
            	Answer
                <span class="hint">Answer to your first question!</span>
            </label>
        	<input type="handle" id="security_answer_1" name="security_answer_1" size="20" maxlength="20">
        </div>
        <div class="clear"></div>
        <div class="login-option">
        	<label for="ship">
            	Recovery Question 2
                <span class="hint">A personal question only youd be able to answer!</span>
            </label>
        	<input type="handle" id="security_question_2" name="security_question_2" size="20" maxlength="190">
        </div>
        <div class="clear"></div>
    	<div class="login-option">
        	<label for="ship">
            	Answer
                <span class="hint">Answer to your second question!</span>
            </label>
        	<input type="handle" id="security_answer_2" name="security_answer_2" size="20" maxlength="20">
        </div>
        <div class="clear"></div>
        <div class="login-option">
        	<label for="ship">
            	Recovery Question 3
                <span class="hint">A personal question only youd be able to answer!</span>
            </label>
        	<input type="handle" id="security_question_3" name="security_question_3" size="20" maxlength="190">
        </div>
        <div class="clear"></div>
    	<div class="login-option">
        	<label for="ship">
            	Answer
                <span class="hint">Answer to your third question!</span>
            </label>
        	<input type="handle" id="security_answer_3" name="security_answer_3" size="20" maxlength="20">
        </div>
 		<br/><br/>
        <div class="clear"></div>        
        <div class="login-option">
			<input class="login-button" type="submit" value="Create">
        </div>
        <div class="clear"></div>
        
    </div>
    <div class="cookie-warning">By creating your account you agree to let us set cookies.</div>
    </form>
						
					</div>
					
					<div class="cleaner"></div>	

</div></div>
<?
include "footer.php";
?>
