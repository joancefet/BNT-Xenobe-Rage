<?php
#############################################################################################
# Xenobe Rage - A Refreshed take on Blacknova traders, improving on the impressive hard work#
# done by the Blacknova development team													#
# Copyright (C) 2012-2013 David Dawson and the Xenobe Rage Development Team					#
# Blacknova Traders - A web-based massively multiplayer space combat and trading game		#
# Copyright (C) 2001-2012 Ron Harwood and the BNT development team							#
#																							#
#  This program is free software: you can redistribute it and/or modify						#
#  it under the terms of the GNU Affero General Public License as							#
#  published by the Free Software Foundation, either version 3 of the						#
#  License, or (at your option) any later version.											#
#																							#
#  This program is distributed in the hope that it will be useful,							#
#  but WITHOUT ANY WARRANTY; without even the implied warranty of							#
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the							#
#  GNU Affero General Public License for more details.										#
#																							#
#  You should have received a copy of the GNU Affero General Public License					#
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.					#
#############################################################################################

#############################################################################################	
# Log Managment																				#
#																							#
# @copyright 	2013 - David Dawson															#
# @Contact		web.developer@live.co.uk													#
# @license    	http://www.gnu.org/licenses/agpl.txt										#
#############################################################################################
 
#############################################################################################	
# Notes:																					#
#																							#
# 																							#
#																							#
# 																							#
#############################################################################################
require_once($_SERVER['DOCUMENT_ROOT']."/config/config.php");
class manage_log { 
	private $_db;
	public function __construct()
	{
		if (!session_id())
		{
			session_start();
		}
		$this->connect = db::init();
	}
	#############################################################################################
	# GET SHIP NAME																				#
	#																							#
	#############################################################################################
	private function find_ship_name($search_criteria){
		global $db_prefix;
		$result = $this->connect->query("SELECT ship_name FROM ".$db_prefix."ships WHERE ship_id='".$search_criteria."'");
		$extract = $result->fetch();
		return $extract['ship_name'];
	}
	#############################################################################################
	# GET SHIP OWNER																			#
	#																							#
	#############################################################################################
	private function find_character_name($search_criteria){
		global $db_prefix;
		$result = $this->connect->query("SELECT character_name FROM ".$db_prefix."ships WHERE ship_id='".$search_criteria."'");
		$extract = $result->fetch();
		return $extract['character_name'];
	}
	##
	## Gives the valid output for the log id
	##
	private function player_log_data($event_id,$a,$b,$c,$ip_array){
		 switch ($event_id)
		 {
			  case 99999;
				  return $a;
			  break;
			  case 1;
			  return "You Have Logged In With I.P. ".$ip_array[0];
			  break;
			  case 50001;
			  return "Account Resynced With I.P. ".$ip_array[0];
			  break;
			  case 2;
			  return "You Have Logged Out";
			  break;
			  case 3;
			  return "There was a bad login attempt from ".$ip_array[0];
			  break;
			  case 4; # Attack failed
			  return $this->find_character_name($a) . " Attempted to attack your ship, but your crew managed to out maneuver the attack!";
			  break;
			  case 5;
			  return $this->find_character_name($a) . " Attempted to attack your ship, but their sensors couldnt get a lock on your ship!";
			  break;
			  case 6;
			  return $this->find_character_name($a) . " Attacked your ship, but luckily your crew where on the bridge at the time, and they managed to engage the EWD!";
			  break;
			  case 7;
			  return $this->find_character_name($a) . " Attacked your ship, and your EWD failed to activate!";
			  break;
			  case 8;
			  return $this->find_character_name($a) . " Scanned your ship, your crew are getting nervous that an attack is inbound....";
			  break;
			  case 9;
			  return $this->find_character_name($a) . " Attempted to scan your ship, but their sensors where no match for your superior cloak!";
			  break;
			  case 10;
			  ## $b = escape pod
			  if($b=="Y")
			  {
				return $this->find_character_name($a) . " attacked your ship. Their weapons, technologies, they where just too powerful for us. We lost our ship, we barely made it out of their in the escape pod!";
			  }
			  else
			  {
				return $this->find_character_name($a) . " attacked your ship. We lost our ship... your ice cold corpse can been seen burning up in a nearby planets atmosphere!";  
			  }
			  break;
			  case 11;
			  if(($a=="") or is_null($a))
			  {
				  $a = "Unnamed";
			  }
			  return "Your planet <strong>".$a."</strong> in sector ".$b." was infected with a zombie virus. Your citizens managed to fight off the zombies, but you lost ".$c."% colonists!";
			  break;
			  case 12;
			  if(($a=="") or is_null($a))
			  {
				  $a = "Unnamed";
			  }
			  return "Your planet <strong>".$a."</strong> in sector ".$b." was hit by a shockwave caused by a nearby coronal mass ejection. All your power supplies where drained!";
			  break;
			  case 13;
			  return "Your fighter fleet in sector <strong>".$a."</strong> is not receiving enough fuel from nearby planets, you lost ".$b."% of your fighters!";
			  break;
			  case 14;
			  if(($a=="") or is_null($a))
			  {
				  $a = "Unnamed";
			  }
			  return "Your planet <strong>".$a."</strong> in sector ".$b." is suffering a shortage of food supplies, you lost ".$c." colonists to cannibalism!";
			  break;
			  case 15;
			  return "Your ship was towed from ".$a." to sector <strong>".$b."</strong> for violating a federation treaty. Your ship is too big for them to handle!";
			  break;
			  case 16;
			  return "Your ship was towed from ".$a." to sector <strong>".$b."</strong>, as your empire is so vast they consider your not a part from the Federation!";
			  break;
			  case 17;
			  return "Your ship managed to get through some sector defences in sector ".$a.", time to have some fun!";
			  break;
			  case 18;
			  return "Your ship was destroyed while attempting to enter sector ".$a.", you didnt have an escape pod... your ice cold corpse can been seen burning up in a nearby planets atmosphere!!";
			  break;
			  case 19;
			  return "Your ship was destroyed while attempting to enter sector ".$a.", they where just too powerful for us. We lost our ship, we barely made it out of their in the escape pod!";
			  break;
			  case 20;
			  return $this->find_character_name($b) . " attacked your fighters in sector ".$a.". None of our pilots survived, our hearts go out to the families of those pilots!";
			  break;
			  case 21;
			  return $this->find_character_name($b) . " attacked your minefield in sector ".$a.". Despite them being armed with advanced warheads, ".$b." easily took down our minefield!";
			  break;
			  case 22;
			  return $this->find_character_name($b) . " attacked your fighters in sector ".$a.". We managed to repel the attack while sustaining minimal casualties! Your remaining <strong>".$c."</strong> fighters have regrouped!";
			  break;
			  case 23;
			  return $this->find_character_name($b) . " attacked your minefield in sector ".$a.". The minefield managed to withstand the attack, the remaining ".$c." mines have been redeployed!";
			  break;
			  case 24;
			  return "While in combat, the high energies from the weapons exchanged cause a singularity to open, unfortunatly your engine's where not powerful enough to avoid being sucked into the black hole. You lost your ship!";
			  break;
			  case 25;
			  return "While in combat, the high energies from the weapons exchanged cause a singularity to open, fortunatly your pilot was trained well, they hit the EWD device before the gravitational pull of the black hole sucked you in!";
			  break;
			  case 26;
			  return "The Federation does not aprove of you attacking ".$this->find_character_name($a).", and as such has placed a bounty on you, ".$b." Credits.!";
			  break;
			  case 27;
			  return $this->find_character_name($a)." attacked your ship, lucky your ship was prepared and you survived! You lost ".$b." fighters, and ".$c." armour plates!";
			  break;
			  case 28;
			  ## $b = escape pod
			  if($b=="Y")
			  {
				return $this->find_character_name($a) . "'s ship was just too strong for us. We lost our ship, we barely made it out of their in the escape pod!";
			  }
			  else
			  {
				return $this->find_character_name($a) . "'s ship was just too strong for us. Your ice cold corpse can been seen burning up in a nearby planets atmosphere!";  
			  }
			  break;
			  case 29;
			  if(($a=="") or is_null($a))
			  {
				  $a = "Unnamed";
			  }
			  return "Your planet <strong>".$a."</strong> in sector ".$b." was raided by pirates!. Your security forces managed to arrive on the scene in time, but you lost ".$c." credits!";
			  break;
			  case 30;
			  return "Account created successfully.";
			  break;
			  case 31;
			  return "Your planet ".$a." in sector ".$b." is currently under sustained bombardment by a starship with the name: ".$this->find_ship_name($c);
			  break;
			  case 32;
			  return $this->find_ship_name($c). " managed to land troops on your planet ".$a." in sector ".$b.". Your citizens fought valiantly, however they where far outnumbered, rarther then let the enemy gain a foothold, they implemented 'order 94', using a lazer drill on the planets core to destroy the planet.";
			  break;
			  case 33;
				  $temp_a = rand(1, 10);
				  if($temp_a>5)
				  {$z = $this->find_ship_name($c);}
				  else
				  {$z = $this->find_character_name($c);}
				  return "Your planet ".$a." in sector ".$b." has been defeated in battle. The last transmission from the planet contained the following: '@$£~$%&#...Attack...%&^$%#...".$z."...##&*^...Help'";
			  break;
			  case 34;
				  return "Your planet ".$a." in sector ".$b." was attacked by  ".$this->find_character_name($c).". Early reports indicate the enemy ship (".$this->find_ship_name($c).") was destroyed in low orbit, parts of the ship have even been spotted intact on the ground, ripe for salvaging.";
			  break;
			  case 35;
				  return "Your ship was destroyed while attacking ".$this->find_character_name($a)."'s ship, luckily you had an escape pod, and managed to evade detection to fight another day.";
			  break;
			  case 36;
				  return "Your ship was destroyed while attacking ".$this->find_character_name($a)."'s ship, and you didnt have an escape pod.";
			  break;
			  case 37;
				  return "Congratulations, you have destroyed the enemy ship, ".$this->find_ship_name($a)." captained by ".$this->find_character_name($a).", only loosing ".$b." armour, and ".$c." fighters.";
			  break;
			  case 38;
				  $temp_a = rand(1, 10);
				  if($temp_a>7)
				  {return "Your planet ".$a." in sector ".$b." has detected strange readings in low orbit. ";}
				  else if($temp_a>3)
				  {return "Your planet ".$a." in sector ".$b." detected strange readings emanating from a ship bearing the name ".$this->find_ship_name($c)." in low orbit.";} 
				  else
				  {return "Your spies managed to thwart an attempt to scan your planet ".$a.", in sector ".$b.", by ".$this->find_character_name($c).".";} 
			  break;
			  case 39;
				  $temp_a = rand(1, 10);
				  if($temp_a>7)
				  {return "Your planet ".$a." in sector ".$b." has detected strange readings in low orbit. ";}
				  else if($temp_a>3)
				  {return "Your planet ".$a." in sector ".$b." has been scanned by a ship bearing the name ".$this->find_ship_name($c).".";} 
				  else
				  {return "Your planet ".$a." in sector ".$b." has been scanned by ".$this->find_character_name($c).".";} 
			  break;
			  case 40;
				  return "You have left the team, ".$a;
			  break;
			  case 41;
				  return $a." has left the team.";
			  break;
			  case 42;
				  return $a." have left the team, and relinquished control to ".$b.".";
			  break;
			  case 43;
				  return "Your team coordinator has decided to leave ".$a.". You have been appointed to take its place.";
			  break;
			  case 44;
				  return "You have joined the team, ".$a;
			  break;
			  case 45;
				  return $b." has accepted to join ".$a.". This brings you one step further to galactic domination.";
			  break;
			  case 46;
				  return "You have been kick from the team, ".$a;
			  break;
			  case 47;
				  return "You have created a new team, ".$a;
			  break;
			  case 48;
				  return "You have been invited to be part of, ".$a;
			  break;
			  case 49;
				  return $b." has rejected an invitation to join your team, ".$a.".";
			  break;
			  case 50;
				  return "You have renamed your team to, ".$a;
			  break;
			  case 51;
				  return "Your team coordinator renamed the team to, ".$a;
			  break;
			  case 52;
				  return "You have hit a minefield in sector <b>".$a."</b>.";
			  break;
			  case 53;
				  return "Your ship was destroyed when it hit a minefield in sector <b>".$a."</b>.";
			  break;
			  case 54;
				  return "You had to pay a toll (".$a." credits) to enter sector <b>".$b."</b>.";
			  break;
			  case 55;
				  $temp_a = rand(1, 10);
				  if($temp_a>5)
				  {$z = "all your crew got was the name of the ship, <b>".$this->find_ship_name($a)."</b>";}
				  else
				  {$z = "your crew recognised the ship instantly, its captained by <b>".$this->find_character_name($a)."</b>";}
					  return "Your ship has detected abnormal readings, when your officers checked the sensors, they spot a ship cloaking, ".$z;
			  break;
			  case 56;
				  $temp_a = rand(1, 10);
				  if($temp_a>5)
				  {$z = "all your crew got was the name of the ship, <b>".$this->find_ship_name($a)."</b>";}
				  else
				  {$z = "your crew recognised the ship instantly, its captained by <b>".$this->find_character_name($a)."</b>";}
				return "A bright flash of white light has appeared all over your ship.... you've been scanned, when your officers checked the sensors, they spot a ship cloaking, ".$z;
			  break;
		 }
	}
/*
			  return "<font color='#6190a5'>Low Priority</font>";
			  return "<font color='#E9AB17'>Medium Priority</font>";
			  return "<font color='#FF0000'>High Priority</font>";
			  return "<font color='#FF0000'><b>Extreme Priority</b></font>";
*/


	private function security_log_data($event_id,$a,$b,$c,$ip_array){
		 switch ($event_id)
		 {
			  case 1;
			  return "User doesnt have a valid user ID, Hack Attempt? Event ID: ".$a;
			  break;
			  case 2;
			  return "Invalid event id passed to class, Hack Attempt? User Attempted: ".$a;
			  break;
			  case 3;
			  return "An unknown error while connecting to the database lead to the following error: <code>".$a."</code>";
			  break;
			  case 4;
			  return "An error occoured while updating a player to set them to destroyed with following error: <code>".$a."</code>";
			  break;
			  case 5;
			  return "The kill ship function was called, however no target was given. Hack attempt?";
			  break;
			  case 6;
			  return "Their ship was set to destroy Successfully!";
			  break;
			  case 7;
			  return "The players stats have been updated! Table: ".$a.", Data: <code>".$b."</code>";
			  break;
			  case 8;
			  return "Failed to update the player data. Error Output: <code>".$a."</code>";
			  break;
			  case 9;
			  return "An error occoured when trying to move the player. Error Output: <code>".$a."</code>";
			  break;
			  case 10;
			  return "Player has moved to sector ".$a.". They have ".$b." turns left!";
			  break;
			  case 11;
			  return "Player was unable to move to sector ".$a.". They have ".$b." turns left!";
			  break;
			  case 12;
			  return "An error occoured when trying to reset the player. Error Output: <code>".$a."</code>";
			  break;
			  case 13;
			  return "The players ship was reset!";
			  break;
			  case 14;
			  return "An error occoured when trying to lock the tables. Error Output: <code>".$b."</code>";
			  break;
			  case 15;
			  return "An error occoured when trying to unlock the tables. Error Output: <code>".$b."</code>";
			  break;
			  case 16;
			  return "Player has engaged sector defences in sector: ".$a."!";
			  break;
			  case 17;
			  return "Player has cleared the sector defences!";
			  break;
			  case 18;
			  return "An error occoured when trying to update a player when engaging sector defences. Error Output: <code>".$a."</code>";
			  break;
			  case 19;
			  return "An error occoured when trying to update a player when clearing sector defences. Error Output: <code>".$a."</code>";
			  break;
			  case 20;
			  return "An error occoured when trying to delete a players bounty. Error Output: <code>".$a."</code>";
			  break;
			  case 21;
			  return "An error occoured when trying to create a players bounty. Error Output: <code>".$a."</code>";
			  break;
			  case 22;
			  return "An error occoured when trying to update a players ship credits. Error Output: <code>".$a."</code>";
			  break;
			  case 23;
			  return $a." was unable to attack ".$b." as they had moved to another sector!";
			  break;
			  case 24;
			  return $a." did not have enough turns to execute an attack on ".$b."! (Turns Available ".$c.")";
			  break;
			  case 25;
			  return $a." attempted to attack a team mate ".$b."!";
			  break;
			  case 26;
			  return $a." attempted to attack ".$b.", however the ZONE does not allow attacking!";
			  break;
			  case 27;
			  return $a." attempted to initiate a Multiple Attack on ".$b."! (User refreshing screen? or attempting to break the code?)";
			  break;
			  case 28;
			  return $a." and ".$b." are currently locked in combat";
			  break;
			  case 29;
			  return $a." and ".$b." managed to open a singularity while engaged in combat";
			  break;
			  case 30;
			  return "an ERROR Occoured when atempted to set up zones for new player";
			  break;
			  case 31;
			  return "an ERROR Occoured when atempted to set up IGB for new player";
			  break;
			  case 32;
			  return "an ERROR Occoured when trying to add up player kills";
			  break;
			  case 33;
			  return "an ERROR Occoured when trying to add up player deaths";
			  break;
			  case 34;
			  return "an ERROR Occoured when trying to modify player points";
			  break;
		 }
	}

	public function security_log($user_id,$event_id,$a,$b,$c,$tracking){
		global $db_prefix;
		$shared_function = new shared();
		if($tracking=="notrack")
		{
			$user_ip_address = "";
			$user_agent = "";
			$user_host = "";
		}
		else
		{
			$ip_array = $shared_function->sortIP();
			$user_ip_address = $ip_array[0];
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$user_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		}

		$event_content = $this->security_log_data($event_id,$a,$b,$c,$ip_array);

		$timestamp = $shared_function->manage_time("full");
		$create_log = $this->connect->prepare("INSERT INTO ".$db_prefix."security_logs SET ship_id = ? , type = ? , time = ?, data = ?, user_agent = ?, user_host = ?, user_ip = ?");
		$create_log->bindParam(1, $user_id, PDO::PARAM_INT);
		$create_log->bindParam(2, $event_id, PDO::PARAM_INT);
		$create_log->bindParam(3, $timestamp, PDO::PARAM_STR);
		$create_log->bindParam(4, $event_content, PDO::PARAM_STR);
		$create_log->bindParam(5, $user_agent, PDO::PARAM_STR);
		$create_log->bindParam(6, $user_host, PDO::PARAM_STR);
		$create_log->bindParam(7, $user_ip_address, PDO::PARAM_STR);
		if($create_log->execute())
		{
			# Do nothing, log was created!!! #											
		}
		else
		{
			#infinite loop fail ha! to add in a log for when the log which tracks logging fails? maybe overkill#
		}
	}
	
	public function player_log($user_id,$event_id,$a,$b,$c,$tracking,$log_priority,$log_title){
		global $db_prefix;
		$shared_function = new shared();
		if($tracking=="notrack")
		{
			$user_ip_address = "";
			$user_agent = "";
			$user_host = "";
		}
		else
		{
			$ip_array = $shared_function->sortIP();
			$user_ip_address = $ip_array[0];
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$user_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		}

		$event_content = $this->player_log_data($event_id,$a,$b,$c,$ip_array);

		$timestamp = $shared_function->manage_time("full");
		$create_log = $this->connect->prepare("INSERT INTO ".$db_prefix."player_logs SET ship_id = ? , type = ? , time = ?, data = ?, user_agent = ?, user_host = ?, user_ip = ?, priority = ?, title = ?");
		$create_log->bindParam(1, $user_id, PDO::PARAM_INT);
		$create_log->bindParam(2, $event_id, PDO::PARAM_INT);
		$create_log->bindParam(3, $timestamp, PDO::PARAM_STR);
		$create_log->bindParam(4, $event_content, PDO::PARAM_STR);
		$create_log->bindParam(5, $user_agent, PDO::PARAM_STR);
		$create_log->bindParam(6, $user_host, PDO::PARAM_STR);
		$create_log->bindParam(7, $user_ip_address, PDO::PARAM_STR);
		$create_log->bindParam(8, $log_priority, PDO::PARAM_STR);
		$create_log->bindParam(9, $log_title, PDO::PARAM_STR);
		if($create_log->execute())
		{
			# Do nothing, log was created!!! #											
		}
		else
		{
			# Log failed to work..... log this in the admin logs.... hopefully it will work there?! #
			if($user_id>0)
			{
				/*username is valid... why else would the log fail?*/
				if($event_id>0)
				{
					/*NO other known reason this should be failing.*/
					$this->security_log($user_id,3,$create_log->errorInfo());
				}
				else
				{
					/*Invalid Event ID*/
					$this->security_log($user_id,2,$event_id);
				}
			}
			else
			{
				/*Invalid User ID*/
				$this->security_log(0,1,$event_id);
			}
		}
	}
	#####################################
	#									#
	# Count Log Feed					#
	#									#
	#####################################
	public function count_log_total($table_name,$user_id){
		$count_total_logs = $this->connect->prepare("SELECT * FROM ".$table_name." WHERE ship_id='".$user_id."'");
		$count_total_logs->execute();
		return $count_total_logs->rowCount();
	}
	#####################################
	#									#
	# Show Log Feed						#
	#									#
	#####################################
	public function show_log($table,$position,$default_logs_show_per_page,$user_id){
		$result = $this->connect->prepare("SELECT * FROM ".$table." WHERE ship_id='".$user_id."' ORDER BY time DESC LIMIT $position, $default_logs_show_per_page");
		$result->execute();
		$output = $result->fetchAll(PDO::FETCH_ASSOC);
		if($output)
		{
			foreach ($output as $row)
			{				
				$build_table .= '<tr><td>'.$row['title'].'</td><td>'.$row['priority'].'</td><td>'.$row['data'].'</td></tr>';
				$build_table .= '<tr><td colspan="3" id="player_log_page_time">'.$row['time'].'</td></tr>';
			}
		}
		return $build_table;
	}
}
?>