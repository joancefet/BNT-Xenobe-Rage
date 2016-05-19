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
// File: index.php

$index_page = true;
include "config/config.php";

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

// Check to see if the language database has been installed yet.
$result = $db->Execute("SELECT name, value FROM {$db->prefix}languages WHERE category=? AND language=?;", array('common', $lang));
if (!$result)
{
    // If not, redirect to create_universe.
    header("Location: create_universe.php");
    die ();
}

$title = $l->get('l_welcome_bnt');
$body_class = 'index';

include "header2.php";
?>
<div class="xenobe-container"><div class="homepage-container">
	<div class="header-image"></div>
    <div class="links">
        <?php 
		if($account_creation_closed==true)
		{}
		else
		{
			echo '<a href="new_fb.php">New Player</a> - ';
		}?>
		 <a href="mailto:<?php echo $admin_mail; ?>">Contact Us</a> - <a href="ranking.php<?php echo $link; ?>">Ranking</a> - <a href="faq.php<?php echo $link; ?>">How To Play</a> - <a href="settings.php<?php echo $link; ?>">Settings</a> - <a href="<?php echo $link_forums; ?>" target="_blank">Forums</a></div>
        <?php 
		if($account_creation_closed==true && $server_closed==false)
		{
			?>
			<div class="website-alert">This Server Is Currently Closed To New Players</div>
			<?php
		}
		else if($server_closed==true)
		{
			?>
			<div class="website-alert">The Server Is Currently Down For Maintenance! Please Try Again Later</div>
			<?php
		}
		else
		{
						?>
			<div class="website-alert">Server currently in test mode, random resets occouring daily/weekly!</div>
			<?php
		}?>
    <form action="login2.php" method="post">
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
            <label for="pass">
            	Password
            	<span class="hint">Forgotten Your Password?</span>
            </label>
            <input type="password" id="pass" name="pass" size="20" maxlength="255">
            </div>
        <div class="clear"></div>
        <div class="login-option">
			<input class="login-button" type="submit" value="Login">
        </div>
        <div class="clear"></div>
        
    </div>
    <div class="cookie-warning">By signing up or logging in you agree to let us set cookies.</div>
    </form>
    <br>
    
    
    <br>
    
    <div class="core-message"><h1>Server Updated To 0.19 Code.</h1><div class="core-content">  
Version: Xenoberage 0.19<br/><br/>
<b>FIXES:</b>
<ul>
<li>Scanning planets not showing in logs - FIXED</li>

<li>realspace movement into sector defences, getting stuck in loop. - FIXED</li>

<li>realspace page not showing footer or correct theme - FIXED</li>

<li>Realspacing into sector via direct, bypassed the tolls. -FIXED</li>

<li>Death message typo - FIXED</li>

<li>special characters not showing on planets names - FIXED</li>

<li>multiple planet colonists limits causing backend issues i.e. shrinking planets - FIXED</li>

<li>moved logs for teams into new logging system</li>

<li>tolls, and minefield logs fixed to new logging system</li>

<li>fixed attacking ships on planets causing db to hang</li>

<li>fixed change password</li>

<li>fixed scan logs showing in wrong logs</li>
</ul>
<b>CHANGES/NEW STUFF:</b>
<ul>
<li>You now get points for killing player ships. (More to come, suggestions??)</li>
<li>You now get points for creating planets (and equally loose points for destroying too :P )</li>

<li>Ship kills and deaths show on the rankings</li>

<li>Added in new account recovery system using preset questions on registration, instead of answering emails, which sometimes dont send....</li>

<li>New feature Facilitites. can be built once on a planet, if a planet is captured in combat the facilitites have a random chance of staying intact</li>
<ul>
<li>Facility Hydrophonics ONLINE (bonus organics)</li>
<li>Facility solar planet ONLINE (No more impact from coronal mass ejection, bonus energy)</li>
<li>Facility Bank ONLINE (bonus credits)</li>
<li>Facility Shipyard ONLINE (Bonus production for fighters and torps on a planet)</li>
<li>Facility Homeworld ONLINE (additional bonus to all the planets outputs)</li>
<li>Facility Medical ONLINE (No more zombie plague outbreaks, double planet size)</li>
<li>Facility Research ONLINE (bonus research points)</li>
</ul>
<li>Sofa wasnt working correctly, so ive tweaked and change how the sofa works. its an experiment, and its more harsher, reducing the grind, and blocking players old habbit of building insainly powerful planets and then destroying their ship for low stats to abuse the way the sofa worked.</li>

<li>Allowed more characters in team name/description, and enforced character text limit</li>

<li>when you die now (in combat), if you have an escape pod, you escape to sol system with your credits, if you dont, you get cloned at sol with no credits ... (maybe future change... have AI take over users empire??? )</li>

<li>Updated planet scans to inform the users of a rough estimate accuracy rate of any planet scans</li>
</ul>

<b>KNOWN ISSUES/BUGS:</b>
<ul>
<li>bounty in logs showing incorrect money number</li>
<li>hack ship not working.</li>
<li>Militery facilitites not working</li>
<li>creating universe doesnt create admin account correctly</li>
<li>still displays left column after logout</li>
<li>failed login page showing wrong themeset</li>
</ul>

   </div></div> 
   <div class="core-message"><h1>Server Updated To 0.18a Code.</h1><div class="core-content">
   Version: Xenoberage 0.18a<br/><br/>

Change Log:
<ul>
<li>Quick update to remove the facebook integration</li>
<li>Moved some planet attack logs to the new logging system</li>
</ul>
Known Issues:
<ul>
<li>Scanning planets not showing in logs</li>
<li>when you die you dont get resurrected. (replacing with new system anyway....)</li>
<li>bounty in logs showing incorrect money number</li>
<li>sofa doesnt seem to be working.... (unconfirmed)</li>
<li>hack ship not working.</li>
<li>various facilities abilities disabled</li>
<li>unable to "influence" senators</li>
<li>creating universe doesnt create admin account correctly</li>
<li>research points not working</li>
<li>ship kills not showing correctly.</li>
<li>attacking ships on planet uses old attack code (need to finish consolidating the code)</li>
<li>points not accumulating</li>
<li>empire stats not displayed to end user. (beta feature coming in 0.017)</li>
<li>still displays left column after logout</li>
<li>failed login page still shows user handle and ship name on password error. (Keep it as feature?)</li>
</ul>
   </div></div>

    </div><div class="footer">
         <div class="github"><a href="https://github.com/xgermz/xenoberage"><div class="logo-github"></div></a></div>
        <div class="copyright"><span class="bolder">Xenobe Rage</span> &copy;2012 - 2014 David Dawson. All rights reserved.<br /><span class="bolder">Blacknova Traders</span> &copy;2000-2012 Ron Harwood &amp; the BNT Dev team. All rights reserved.</div>
    </div>
</div>
