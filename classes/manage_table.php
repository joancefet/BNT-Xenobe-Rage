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
# Table Managment																			#
#																							#
# @copyright 	2013 - David Dawson															#
# @Contact		web.developer@live.co.uk													#
# @license    	http://www.gnu.org/licenses/agpl.txt										#
#############################################################################################
 
#############################################################################################	
# Notes:																					#
#																							#
# 				ADD IN LOGGING FOR WHEN FUNCTIONS FAIL										#
#																							#
# 																							#
#############################################################################################
require_once($_SERVER['DOCUMENT_ROOT']."/config/config.php");
class manage_table { 
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
	#############################################################################################
	# GET ACCOUNT ID																			#
	#																							#
	#############################################################################################
	public function find_account_id($username){
		global $db_prefix;		
		$result = $this->connect->query("SELECT user_id FROM ".$db_prefix."account WHERE username='".$username."'");
		$account_id = $result->fetch();
		return $account_id['user_id'];
	}
	#############################################################################################
	# GET ACCOUNT EMPIRE STATS																	#
	#																							#
	#############################################################################################
	public function get_account_stats($user_id){
		global $db_prefix;		
		$result = $this->connect->query("SELECT empire_parts,empire_research,empire_food,empire_energy,empire_ores FROM ".$db_prefix."account WHERE user_id='".$user_id."'");
		$account_stats = $result->fetch();
		return $account_stats;
	}
	#############################################################################################
	# SET ACCOUNT LIVE SHIP																		#
	#																							#
	#############################################################################################
	public function set_active_ship($ship_id,$account_id){
		global $db_prefix;
		$set_active_ship = $this->connect->prepare("UPDATE ".$db_prefix."account SET active_ship=? WHERE user_id='".$account_id."'");
		$set_active_ship->bindParam(1, $ship_id, PDO::PARAM_INT);
		if($set_active_ship->execute())
		{	
			return true;									
		}
		else
		{
			return false;
		}
	}
	#############################################################################################
	# GIVE PLAYER POINTS																		#
	#																							#
	#############################################################################################
	public function add_points_to_player($ship_id,$points){
		global $db_prefix;
		echo "test";
		$v = 0;
		$result = $this->connect->query("SELECT points FROM ".$db_prefix."ships WHERE ship_id='".$ship_id."'");
		$current_count = $result->fetch();
		$v = $current_count['points'];
		$v = $v + $points;
		$update_stat = $this->connect->prepare("UPDATE ".$db_prefix."ships SET points = ? WHERE ship_id='".$ship_id."'");	
		$update_stat->bindParam(1, $v, PDO::PARAM_INT);
		if($update_stat->execute())
		{
			return true;
			# LOG THIS #											
		}
		else
		{
			/*Log failure to update points*/
			$manage_log = new manage_log();
			$manage_log->security_log($ship_id,34,$update_stat->errorInfo());
			return false;
			# LOG FAIL #
		}
	}
	#############################################################################################
	# REMOVE PLAYER POINTS																		#
	#																							#
	#############################################################################################
	public function remove_points_from_player($ship_id,$points){
		global $db_prefix;
		$v = 0;
		$result = $this->connect->query("SELECT points FROM ".$db_prefix."ships WHERE ship_id='".$ship_id."'");
		$current_count = $result->fetch();
		$v = $current_count['points'];
		$v = $v - $points;
		if($v<0){$v=0;}
		$update_stat = $this->connect->prepare("UPDATE ".$db_prefix."ships SET points = ? WHERE ship_id='".$ship_id."'");	
		$update_stat->bindParam(1, $v, PDO::PARAM_INT);
		if($update_stat->execute())
		{
			return true;
			# LOG THIS #											
		}
		else
		{
			/*Log failure to update points*/
			$manage_log = new manage_log();
			$manage_log->security_log($ship_id,34,$update_stat->errorInfo());
			return false;
			# LOG FAIL #
		}
	}
	#############################################################################################
	# CREATE USER SPACESHIP																		#
	#																							#
	#############################################################################################
	public function create_user_space_ship($account_id,$ship_name,$user_handle,$user_email,$stamp,$ip){
		global $db_prefix;
		global $start_armor;
		global $start_credits;
		global $start_energy;
		global $start_fighters;
		global $start_turns;
		global $start_editors;
		global $start_genesis;
		global $start_beacon;
		global $start_emerwarp;
		global $escape;
		global $scoop;
		global $start_minedeflectors;
		global $start_lssd;
		
		$create_ship = $this->connect->prepare("INSERT INTO ".$db_prefix."ships SET 
		ship_id = ?,
		ship_name = ?, 
		ship_destroyed = ?, 
		character_name = ?, 
		password = ?, 
		email = ?, 
		armor_pts = ?, 
		credits = ?,
		ship_energy = ?,
		ship_fighters = ?,
		turns = ?,
		on_planet = ?,
		dev_warpedit = ?,
		dev_genesis = ?,
		dev_beacon = ?,
		dev_emerwarp = ?,
		dev_escapepod = ?,
		dev_fuelscoop = ?,
		dev_minedeflector = ?,
		last_login = ?,
		ip_address = ?,
		trade_colonists = ?,
		trade_fighters = ?,
		trade_torps = ?,
		trade_energy = ?,
		lang = ?,
		dev_lssd = ?
		");
		$default_ship_destroyed = "N";
		$default_on_planet = "N";
		$default_trade_colonists = "Y";
		$default_trade_fighters = "N";
		$default_trade_torps = "N";
		$default_trade_energy = "Y";
		$lang = "GB";
		$password = "REDUNDANT";
		
		$create_ship->bindParam(1, $account_id, PDO::PARAM_INT);
		$create_ship->bindParam(2, $ship_name, PDO::PARAM_STR);
		$create_ship->bindParam(3, $default_ship_destroyed, PDO::PARAM_STR);
		$create_ship->bindParam(4, $user_handle, PDO::PARAM_STR);
		$create_ship->bindParam(5, $password, PDO::PARAM_STR);
		$create_ship->bindParam(6, $user_email, PDO::PARAM_STR);
		$create_ship->bindParam(7, $start_armor, PDO::PARAM_INT);
		$create_ship->bindParam(8, $start_credits, PDO::PARAM_INT);
		$create_ship->bindParam(9, $start_energy, PDO::PARAM_INT);
		$create_ship->bindParam(10, $start_fighters, PDO::PARAM_INT);
		$create_ship->bindParam(11, $start_turns, PDO::PARAM_INT);
		$create_ship->bindParam(12, $default_on_planet, PDO::PARAM_STR);
		$create_ship->bindParam(13, $start_editors, PDO::PARAM_INT);
		$create_ship->bindParam(14, $start_genesis, PDO::PARAM_INT);
		$create_ship->bindParam(15, $start_beacon, PDO::PARAM_INT);
		$create_ship->bindParam(16, $start_emerwarp, PDO::PARAM_INT);
		$create_ship->bindParam(17, $escape, PDO::PARAM_STR);
		$create_ship->bindParam(18, $scoop, PDO::PARAM_STR);
		$create_ship->bindParam(19, $start_minedeflectors, PDO::PARAM_INT);
		$create_ship->bindParam(20, $stamp, PDO::PARAM_STR);
		$create_ship->bindParam(21, $ip, PDO::PARAM_STR);
		$create_ship->bindParam(22, $default_trade_colonists, PDO::PARAM_STR);
		$create_ship->bindParam(23, $default_trade_fighters, PDO::PARAM_STR);
		$create_ship->bindParam(24, $default_trade_torps, PDO::PARAM_STR);
		$create_ship->bindParam(25, $default_trade_energy, PDO::PARAM_STR);
		$create_ship->bindParam(26, $lang, PDO::PARAM_STR);
		$create_ship->bindParam(27, $start_lssd, PDO::PARAM_STR);
		if($create_ship->execute())
		{
			/*Ship built!*/
			return true;										
		}
		else
		{
			/*Ship not built*/
			//$create_ship->errorInfo();
			return false;
		}
	}
	#############################################################################################
	# LOCK ANY TABLE																			#
	#																							#
	#############################################################################################
	public function lock_table($table_write_names,$table_read_names){
		global $db_prefix;
		$manage_log = new manage_log();
		$table_sql_input = "";
		foreach ($table_write_names as &$table) {
			$table_sql_input .= $db_prefix.$table." WRITE,";
		}
		foreach ($table_read_names as &$table) {
			$table_sql_input .= $db_prefix.$table." READ,";
		}
		$table_sql_input = substr($table_sql_input, 0, -1); /*FIX: Remove the last added comma*/
		$lock_table = $this->connect->query("LOCK TABLES ".$table_sql_input.";");
		if($lock_table->execute())
		{
			return true;
		}
		else
		{
			/*Only need to log a failure!*/
			$manage_log->security_log(0,14,$lock_table->errorInfo());
			return false;
		}

	}
	#############################################################################################
	# UNLOCK TABLES																				#
	#																							#
	#############################################################################################
	public function unlock_table(){
		$manage_log = new manage_log();
		$unlock_table = $this->connect->query("UNLOCK TABLES;");
		if($unlock_table->execute())
		{
			return true;
		}
		else
		{
			/*Only need to log a failure*/
			$manage_log->security_log(0,15,$unlock_table->errorInfo());
			return false;
		}
	}
	#############################################################################################
	# PROCESS QUERY																				#
	#																							#
	#																							#
	# WARNING ANYTHING USING THIS FUNCTION NEEDS TO BE CONVERTED TO A STRICTER CLASS			#
	#############################################################################################
	public function process_query($query){
		/*
		$query - the query to be run
		*/
		$result = $this->connect->query($query);
		$result->execute();
		return $result->fetchAll(PDO::FETCH_ASSOC);
		
	}
	#############################################################################################
	# CHECK SECTOR IS REAL																		#
	#																							#
	#############################################################################################
	public function real_sector($sector){
		global $db_prefix;
		$sector_is_real = $this->connect->query("SELECT * FROM ".$db_prefix."universe WHERE sector_id=".$sector.";");
		$sector_is_real->execute();
		return $sector_is_real->rowCount();
	}
	#############################################################################################
	# CHECK PLANET IS REAL																		#
	#																							#
	#############################################################################################
	public function real_planet($planet_id){
		global $db_prefix;
		$planet_is_real = $this->connect->query("SELECT * FROM ".$db_prefix."planets WHERE planet_id=".$planet_id.";");
		$planet_is_real->execute();
		return $planet_is_real->rowCount();
	}
	#############################################################################################
	# COUNT SECTOR LINKS																		#
	#																							#
	#############################################################################################
	public function count_sector_links($sector){
		global $db_prefix;
		$count_links = $this->connect->query("SELECT * FROM ".$db_prefix."links WHERE link_start=".$sector.";");
		$count_links->execute();
		return $count_links->rowCount();
	}
	#############################################################################################
	# CHECK FOR XENOBE																			#
	#																							#
	#############################################################################################
	public function is_that_xenobe($ship_id){
		global $db_prefix;
		$result = $this->connect->query("SELECT email FROM ".$db_prefix."ships WHERE ship_id=".$ship_id.";");
		$data = $result->fetch();
		
		
		if ( preg_match("/(\@xenobe)$/", $data['email']) !== 0 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	#############################################################################################
	# CREATE IGB ACCOUNT																		#
	#																							#
	#############################################################################################
	public function create_new_igb($owner_id){
		global $db_prefix;
		$manage_log = new manage_log();
		$create_igb = $this->connect->prepare("INSERT INTO ".$db_prefix."ibank_accounts SET ship_id = ? , balance = ? , loan = ?");
		$igb_default_balance = 0;
		$igb_default_loan = 0;
		$create_igb->bindParam(1, $owner_id, PDO::PARAM_INT);
		$create_igb->bindParam(2, $igb_default_balance, PDO::PARAM_INT);
		$create_igb->bindParam(3, $igb_default_loan, PDO::PARAM_INT);
		
		if($create_igb->execute())
		{
			return true;
			# LOG THIS #											
		}
		else
		{
			return false;
			/*Log failure to create bounty*/
			$manage_log = new manage_log();
			$manage_log->security_log(0,31,$create_igb->errorInfo());
		}

	}
	#############################################################################################
	# CREATE NEW ZONE																			#
	#																							#
	#############################################################################################
	public function create_new_zone($owner_id,$character_name){
		global $db_prefix;
		
		$character_name = $character_name."'s Territory";
		$manage_log = new manage_log();
		$create_zone = $this->connect->prepare("INSERT INTO ".$db_prefix."zones SET zone_name = ? , owner = ? , corp_zone = ? , allow_beacon = ? , allow_attack = ? , allow_planetattack = ? , allow_warpedit = ? , allow_planet = ? , allow_trade = ? , allow_defenses = ? , max_hull = ? ");
		$zone_default_corp = 'N';
		$zone_default_beacon = 'Y';
		$zone_default_attack = 'Y';
		$zone_default_planet_attack = 'Y';
		$zone_default_warp_edit = 'Y';
		$zone_default_planet_create = 'Y';
		$zone_default_trading = 'Y';
		$zone_default_defenses = 'Y';
		$zone_default_max_hull = 0;
		$create_zone->bindParam(1, $character_name, PDO::PARAM_STR);
		$create_zone->bindParam(2, $owner_id, PDO::PARAM_INT);
		$create_zone->bindParam(3, $zone_default_corp, PDO::PARAM_STR);
		$create_zone->bindParam(4, $zone_default_beacon, PDO::PARAM_STR);
		$create_zone->bindParam(5, $zone_default_attack, PDO::PARAM_STR);
		$create_zone->bindParam(6, $zone_default_planet_attack, PDO::PARAM_STR);
		$create_zone->bindParam(7, $zone_default_warp_edit, PDO::PARAM_STR);
		$create_zone->bindParam(8, $zone_default_planet_create, PDO::PARAM_STR);
		$create_zone->bindParam(9, $zone_default_trading, PDO::PARAM_STR);
		$create_zone->bindParam(10, $zone_default_defenses, PDO::PARAM_STR);
		$create_zone->bindParam(11, $zone_default_max_hull, PDO::PARAM_INT);
		if($create_zone->execute())
		{
			return true;
			# LOG THIS #											
		}
		else
		{
			return false;
			/*Log failure to create bounty*/
			$manage_log = new manage_log();
			$manage_log->security_log(0,30,$create_zone->errorInfo());
		}

	}
	#############################################################################################
	# GET ZONE INFORMATION																		#
	#																							#
	#############################################################################################
	public function zone_information($sector,$option){
		global $db_prefix;
		$result = $this->connect->query("SELECT ".$option.",".$db_prefix."universe.zone_id FROM ".$db_prefix."zones,".$db_prefix."universe WHERE sector_id=".$sector." AND ".$db_prefix."zones.zone_id=".$db_prefix."universe.zone_id");
		return $result->fetch();
	}
	#############################################################################################
	# GET ZONE OWNER INFORMATION																#
	#																							#
	#############################################################################################
	public function zone_owner_information($zone_id){
		global $db_prefix;
		$result = $this->connect->query("SELECT * FROM ".$db_prefix."zones WHERE zone_id=".$zone_id."");
		return $result->fetch();
	}
	#############################################################################################
	# LIST SECTOR LINKS WITHIN SECTOR															#
	#																							#
	#############################################################################################
	public function list_sector_links($sector){
		global $db_prefix;
		$result = $this->connect->query("SELECT * FROM ".$db_prefix."links WHERE link_start=".$sector." ORDER BY link_dest ASC;");
		$result = $result->fetchAll(PDO::FETCH_ASSOC);
		$actual_links = array();
		if($result)
		{
			foreach ($result as $row)
			{
				array_push($actual_links, $row['link_dest']); 
			}
		}
		return $actual_links;
	}
	#############################################################################################
	# CHECK FOR LINK CONFLICTS																	#
	#																							#
	#############################################################################################
	public function check_link_conflicts($current_sector,$target_sector){
		global $db_prefix;
		$checking_conflicts = $this->connect->query("SELECT * FROM ".$db_prefix."links WHERE link_start=".$current_sector." AND link_dest=".$target_sector."");
		$checking_conflicts->execute();
		return $checking_conflicts->rowCount();
	}
	#############################################################################################
	# CREATE NEW WARP LINK																		#
	#																							#
	#############################################################################################
	public function insert_new_warp_link($originating_sector,$destination_sector){
		global $db_prefix;
		$create_link = $this->connect->prepare("INSERT INTO ".$db_prefix."links SET link_start=?, link_dest=?");
		$create_link->bindParam(1, $originating_sector, PDO::PARAM_INT);
		$create_link->bindParam(2, $destination_sector, PDO::PARAM_INT);
		
		if($create_link->execute())
		{	
			return true;									
		}
		else
		{
			return false;
		}
	}
	#############################################################################################
	# DELETE WARP LINK																				#
	#																							#
	#############################################################################################
	public function delete_warp_link($originating_sector,$destination_sector){
		global $db_prefix;
		$delete_link = $this->connect->prepare("DELETE FROM ".$db_prefix."links WHERE link_start=? AND link_dest=?");
		$delete_link->bindParam(1, $originating_sector, PDO::PARAM_INT);
		$delete_link->bindParam(2, $destination_sector, PDO::PARAM_INT);
		if($delete_link->execute())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	#############################################################################################
	# GET TEAM ID																		#
	#																							#
	#############################################################################################
	public function team_id($user_id){
		global $db_prefix;		
		$result = $this->connect->query("SELECT team FROM ".$db_prefix."ships WHERE ship_id=".$user_id."");
		$team = $result->fetch();
		return $team['team'];
	}
	#############################################################################################
	# UPDATE SHIP IN COMBAT / COMBAT ENDED																			#
	#																							#
	#############################################################################################
	public function ship_in_sector_defence_combat($sector_id,$ship_id){
		
		global $db_prefix;
		$manage_log = new manage_log();
		if($sector_id > 0)
		{
			$challange = $this->connect->prepare("UPDATE ".$db_prefix."ships SET cleared_defences= ? WHERE ship_id='".$ship_id."'");
			$challange->bindParam(1, $sector_id, PDO::PARAM_INT);
			if($challange->execute())
			{
				/*Ship has engaged the sector defences in sector X*/
				$manage_log->security_log($ship_id,16,$sector_id);
				return true;										
			}
			else
			{
				$manage_log->security_log(0,18,$challange->errorInfo());
				return false;
			}
		}
		else
		{
			$o = "Y";
			$challange = $this->connect->prepare("UPDATE ".$db_prefix."ships SET cleared_defences= ? WHERE ship_id='".$ship_id."'");	
			$challange->bindParam(1, $o, PDO::PARAM_STR);	
			if($challange->execute())
			{
				/*Ship has passed through sector defences*/
				$manage_log->security_log($ship_id,17);
				return true;											
			}
			else
			{
				$manage_log->security_log(0,19,$challange->errorInfo());
				return false;
			}
		}
	}
	#############################################################################################
	# GET SECTOR DEFENCES INFORMATION															#
	#																							#
	#############################################################################################
	public function sector_defence_quantities($sector,$defence,$user_id,$mode){
		global $db_prefix;
		if($mode=="own")
		{
			## User is checking their own sector defence report! ##
			$result = $this->connect->query("SELECT * FROM ".$db_prefix."sector_defence WHERE sector_id=".$sector." and defence_type ='".$defence."' and ship_id='".$user_id."' ORDER BY quantity DESC");
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			$total = 0;
			if($result)
			{
				foreach ($result as $row)
				{
					$total = $total + $row['quantity'];
				}
			}
		}
		else
		{
			## Investigation a sector ##
			$user_team = $this->team_id($user_id);
			$result = $this->connect->query("SELECT * FROM ".$db_prefix."sector_defence WHERE sector_id=".$sector." and defence_type ='".$defence."' and ship_id!='".$user_id."' ORDER BY quantity DESC");
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			$total = 0;
			if($result)
			{
				foreach ($result as $row)
				{
					if($user_team==0)
					{
						/*Your not a member of a team!*/
						$total = $total + $row['quantity'];
					}
					else if($user_team==$this->team_id($row['ship_id']))
					{
						/* Your part of the same team! Your not into attacking friends are you? */
					}
					else
					{
						/*Ohhh we can attack them defences!*/
						$total = $total + $row['quantity'];
					}
				}
			}
		}
		return $total;
	}
	#############################################################################################
	# GET ALL PLAYER INFORMATION																#
	#																							#
	#############################################################################################
	public function playerinfo($id,$select){
		global $db_prefix;
		if($select=="")
		{
			$result = $this->connect->query("SELECT * FROM ".$db_prefix."ships WHERE ship_id='".$id."'");
		}
		else
		{
			$result = $this->connect->query("SELECT ".$select." FROM ".$db_prefix."ships WHERE ship_id='".$id."'");
		}

		return $result->fetch();
	}
	#############################################################################################
	# GET ALL PLANET INFORMATION																#
	#																							#
	#############################################################################################
	public function planetinfo($id,$select){
		global $db_prefix;
		if($select=="")
		{
			$result = $this->connect->query("SELECT * FROM ".$db_prefix."planets WHERE planet_id='".$id."'");
		}
		else
		{
			$result = $this->connect->query("SELECT ".$select." FROM ".$db_prefix."planets WHERE planet_id'".$id."'");
		}

		return $result->fetch();
	}
	#############################################################################################
	# GET ALL UNIVERSE INFORMATION																#
	#																							#
	#############################################################################################
	public function universeinfo($id,$select){
		global $db_prefix;
		if($select=="")
		{
			$result = $this->connect->query("SELECT * FROM ".$db_prefix."universe WHERE sector_id='".$id."'");
		}
		else
		{
			$result = $this->connect->query("SELECT ".$select." FROM ".$db_prefix."universe WHERE sector_id='".$id."'");
		}

		return $result->fetch();
	}
	#############################################################################################
	# FIND PLAYER																				#
	#																							#
	#############################################################################################
	public function find_player($find,$table,$where){
		global $db_prefix;
		if($where=="")
		{
			$result = $this->connect->query("SELECT * FROM ".$db_prefix.$table." WHERE ".$where."");
		}
		else
		{
			$result = $this->connect->query("SELECT ".$find." FROM ".$db_prefix.$table." WHERE ".$where."");
		}

		return $result->fetchAll(PDO::FETCH_ASSOC);
	}
	#############################################################################################
	# CHECK PLAYER ALIVE																		#
	#																							#
	#############################################################################################
	public function check_player_alive($id){
		global $db_prefix;
		$result = $this->connect->query("SELECT ship_destroyed FROM ".$db_prefix."ships WHERE ship_id='".$id."'");
		$is_alive = $result->fetch();
		return $is_alive['ship_destroyed'];
	}
	#############################################################################################
	# UPDATE USERS SHIP KILLS																	#
	#																							#
	#############################################################################################
	public function update_player_ship_kills($ship_id){
		global $db_prefix, $global_points_ship_kills;
		$v = 0;
		$result = $this->connect->query("SELECT ship_kills FROM ".$db_prefix."ships WHERE ship_id='".$ship_id."'");
		$current_count = $result->fetch();
		$v = $current_count['ship_kills'];
		$v = $v + 1;
		$update_stat = $this->connect->prepare("UPDATE ".$db_prefix."ships SET ship_kills = ? WHERE ship_id='".$ship_id."'");	
		$update_stat->bindParam(1, $v, PDO::PARAM_INT);
		if($update_stat->execute())
		{
			/*POINTS - GIVE THE PLAYER A POINT FOR THE KILL*/
			$this->add_points_to_player($ship_id,$global_points_ship_kills);
			return true;
			# LOG THIS #											
		}
		else
		{
			/*Log failure to update kill count for ship*/
			$manage_log = new manage_log();
			$manage_log->security_log($ship_id,32,$update_stat->errorInfo());
			return false;
			# LOG FAIL #
		}
	}
	#############################################################################################
	# UPDATE USERS SHIP DEATHS																	#
	#																							#
	#############################################################################################
	public function update_player_ship_deaths($ship_id){
		global $db_prefix;
		$v = 0;
		$result = $this->connect->query("SELECT ship_deaths FROM ".$db_prefix."ships WHERE ship_id='".$ship_id."'");
		$current_count = $result->fetch();
		$v = $current_count['ship_deaths'];
		$v = $v + 1;
		$update_stat = $this->connect->prepare("UPDATE ".$db_prefix."ships SET ship_deaths = ? WHERE ship_id='".$ship_id."'");	
		$update_stat->bindParam(1, $v, PDO::PARAM_INT);
		if($update_stat->execute())
		{
			return true;
			# LOG THIS #											
		}
		else
		{
			/*Log failure to update death count for ship*/
			$manage_log = new manage_log();
			$manage_log->security_log($ship_id,33,$update_stat->errorInfo());
			return false;
			# LOG FAIL #
		}
	}
	#############################################################################################
	# GET USER ID																				#
	#																							#
	#############################################################################################
	public function find_player_userid($search_criteria){
		global $db_prefix;
		$result = $this->connect->query("SELECT ship_id FROM ".$db_prefix."ships WHERE email='".$search_criteria."'");
		return $result->fetch();
	}
	#############################################################################################
	# GET FEDERATION BOUNTY																		#
	#																							#
	#############################################################################################
	public function get_bounty($target_id,$placed_by){
		global $db_prefix;

			$result = $this->connect->query("SELECT SUM(amount) AS btytotal FROM ".$db_prefix."bounty WHERE bounty_on = ".$target_id." AND placed_by = ".$placed_by."");
		return $result->fetch();
	}
	#############################################################################################
	# DELETE BOUNTY																				#
	#																							#
	#############################################################################################
	public function delete_bounty($bounty_id){
		global $db_prefix;
		$manage_log = new manage_log();
		$result = $this->connect->query("DELETE FROM ".$db_prefix."bounty WHERE bounty_id = '".$bounty_id."'");
		if($result->execute())
		{
			return true;
		}
		else
		{
			/*Log failure to delete bounty*/
			$manage_log = new manage_log();
			$manage_log->security_log(0,20,$result->errorInfo());
			return false;
		}
	}
	#############################################################################################
	# CREATE FEDERATION BOUNTY																	#
	#																							#
	#############################################################################################
	public function create_bounty($target_id,$placed_by,$amount){
		global $db_prefix;
		$manage_log = new manage_log();
		$create_bounty = $this->connect->prepare("INSERT INTO ".$db_prefix."bounty SET bounty_on = ? , placed_by = ? , amount = ? ");
		$create_bounty->bindParam(1, $target_id, PDO::PARAM_INT);
		$create_bounty->bindParam(2, $placed_by, PDO::PARAM_INT);
		$create_bounty->bindParam(3, $amount, PDO::PARAM_INT);
		
		if($create_bounty->execute())
		{
			# LOG THIS #											
		}
		else
		{
			/*Log failure to create bounty*/
			$manage_log = new manage_log();
			$manage_log->security_log(0,21,$create_bounty->errorInfo());
		}

	}
	#############################################################################################
	# UPDATE SHIP CREDITS																		#
	#																							#
	#############################################################################################
	public function update_ship_credits($ship_id,$type,$credits){
		global $db_prefix;
		$manage_log = new manage_log();
		if($type=="1")
		{$type = "+";}
		else if($type=="2")
		{$type = "-";}
		else
		{$type = "-";}//All else fails, always subtract just in case its some wierd exploit.
		$update_creds = $this->connect->prepare("UPDATE ".$db_prefix."ships SET credits = credits ".$type." ? WHERE ship_id='".$ship_id."'");	
		$update_creds->bindParam(1, $credits, PDO::PARAM_INT);
		if($update_creds->execute())
		{
			return true;
			# LOG THIS #											
		}
		else
		{
			/*Log failure to create bounty*/
			$manage_log = new manage_log();
			$manage_log->security_log(0,22,$update_creds->errorInfo());
			return false;
			# LOG FAIL #
		}

	}
	#############################################################################################
	# FETCH USER CREDITS																		#
	#																							#
	#############################################################################################
	public function check_credits($ship_id){
		global $db_prefix;
		$result = $this->connect->query("SELECT credits FROM ".$db_prefix."ships WHERE ship_id='".$ship_id."'");
		return $result->fetch();
	}
	#############################################################################################
	# COLLECT BOUNTY																			#
	#																							#
	#############################################################################################
	public function collect_bounty($attacker, $bounty_on)
	{
		$tables_to_lock_array = array('ships','bounty','player_logs','security_logs');
		$this->lock_table($tables_to_lock_array);
		global $db_prefix;
		$manage_log = new manage_log();
		$result = $this->connect->query("SELECT * FROM ".$db_prefix."bounty WHERE bounty_on = ".$bounty_on."");
		$bounty_found = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($bounty_found as $bounty)
			{
				/*Found a bounty, now do somthing with it*/
				echo "<br/>A bounty of <b>" .$bounty['amount'] . "</b> has been placed on <b>" . $this->find_character_name($bounty['bounty_on'])."</b><br/>";
				$current_credits = $this->check_credits($attacker);
				$target_data = $this->playerinfo($bounty_on,"");
				$attacker_data = $this->playerinfo($attacker,"");
				$bounty['amount'] = (int)$bounty['amount'];
				$s = $attacker_data['ship_id'];
				
				$r = $bounty['amount'];
				if($this->update_ship_credits($s,"1",$r))
				{
					/*now log it*/
					#playerlog ($db, $attacker, LOG_BOUNTY_CLAIMED, "$bountydetails[amount]|$bountydetails[character_name]|$placed");
					#playerlog ($db, $bountydetails['placed_by'], LOG_BOUNTY_PAID, "$bountydetails[amount]|$bountydetails[character_name]");
				}
				else
				{
					/*Allready logged the failure by the function.*/
				}
				
				if($this->delete_bounty($bounty['bounty_id']))
				{
					/*Tell the player the bounty has been removed*/
				}
				else
				{
					/*Allready logged the failure by the function.*/
				}
			}
			$this->unlock_table();
	}
	#############################################################################################
	# RESET PLAYER SHIP																			#
	#																							#
	#############################################################################################
	public function reset_ship($ship_id){
		global $db_prefix;
		$manage_log = new manage_log();
		$reset_player_ship = $this->connect->prepare("UPDATE ".$db_prefix."ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=0,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=0,ship_colonists=0,ship_fighters=0,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',dev_lssd='N' WHERE ship_id='".$ship_id."'");		
		if($reset_player_ship->execute())
		{
			$manage_log->security_log($ship_id,13);
			# LOG THIS #											
		}
		else
		{
			$manage_log->security_log($ship_id,12,$reset_player_ship->errorInfo());
			# LOG FAIL #
		}

	}
	#############################################################################################
	# MOVE SHIP																					#
	#																							#
	#############################################################################################
	public function move_ship($ship_id,$sector,$turns){
		global $db_prefix;
		$manage_log = new manage_log();
		if($turns>0)
		{
			$update_move = $this->connect->prepare("UPDATE ".$db_prefix."ships SET sector = ?, turns_used=turns_used+1, turns=turns-1 WHERE ship_id='".$ship_id."'");	
			$update_move->bindParam(1, $sector, PDO::PARAM_INT);	
			if($update_move->execute())
			{
				if($this->is_that_xenobe($ship_id))
				{
					/*
					Dont want xenobe movements spamming, just need to track real player movements
					*/
				}
				else
				{
					$turns = $turns-1; /*Fix for log*/
					$manage_log->security_log($ship_id,10,$sector,$turns);
				}
				return true;
				# LOG THIS #											
			}
			else
			{
				$manage_log->security_log($ship_id,9,$update_move->errorInfo());
				return false;
				# LOG FAIL #
			}
		}
		else
		{
			$manage_log->security_log($ship_id,11,$sector,$turns);
			return false;
			# LOG FAIL #
		}

	}
	#############################################################################################
	# SECTOR DEFENCE MANAGMENT																	#
	#																							#
	#############################################################################################
	public function manage_sector_defences($sector,$sector_fighters,$sector_mines,$attacker_id){
		global $db_prefix;
		$manage_log = new manage_log();
		/*
		First check if the defences are utterly destroyed. 
		*/
		## Checking if fighters destroyed ##
		if(($sector_fighters==0) or ($sector_fighters<1))
		{
			/*we dont want to be wiping out team mates fighters*/
			$user_team = $this->team_id($attacker_id);
			$result = $this->connect->query("SELECT * FROM ".$db_prefix."sector_defence WHERE sector_id=".$sector." and defence_type ='F' and ship_id!='".$attacker_id."' ORDER BY quantity DESC");
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			$total = 0;
			if($result)
			{
				foreach ($result as $row)
				{
					if($user_team==0)
					{
						/*Your not a member of a team, so you have no team mates to worry about.... delete away!!*/
						$result = $this->connect->query("DELETE FROM ".$db_prefix."sector_defence WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='F'");
						if($result->execute())
						{
							## log sector fighters killed ##
							##New Log ##
							$manage_log->player_log($row['ship_id'],20,$sector,$attacker_id,'','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
						}
						else
						{
							## log failed to kill sector fighters ##
						}
					}
					else if($user_team==$this->team_id($row['ship_id']))
					{
						/* Fighters are part of your own team... cease firing ... CEASE FIRING!?!?! */
					}
					else
					{
						/*Ohhh we can attack them defences!*/
						$result = $this->connect->query("DELETE FROM ".$db_prefix."sector_defence WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='F'");
						if($result->execute())
						{
							## log sector fighters killed ##
							##New Log ##
							$manage_log->player_log($row['ship_id'],20,$sector,$attacker_id,'','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
						}
						else
						{
							## log failed to kill sector fighters ##
						}
					}
				}
			}
		}
		## checking if minefield is destroyed ##
		if($sector_mines==0)
		{
			/*we dont want to be wiping out team mates mine fields*/
			$user_team = $this->team_id($attacker_id);
			$result = $this->connect->query("SELECT * FROM ".$db_prefix."sector_defence WHERE sector_id=".$sector." and defence_type ='M' and ship_id!='".$attacker_id."' ORDER BY quantity DESC");
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			$total = 0;
			if($result)
			{
				foreach ($result as $row)
				{
					if($user_team==0)
					{
						/*Your not a member of a team, so you have no team mates to worry about.... delete away!!*/
						$result = $this->connect->query("DELETE FROM ".$db_prefix."sector_defence WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='M'");
						if($result->execute())
						{
							## log sector mines killed ##
							##New Log ##
							$manage_log->player_log($row['ship_id'],21,$sector,$attacker_id,'','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
						}
						else
						{
							## log failed to kill sector mines ##
						}
					}
					else if($user_team==$this->team_id($row['ship_id']))
					{
						/* mines are part of your own team... cease firing ... CEASE FIRE!?!?! */
					}
					else
					{
						/*Ohhh we can attack them defences!*/
						$result = $this->connect->query("DELETE FROM ".$db_prefix."sector_defence WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='M'");
						if($result->execute())
						{
							## log sector mines killed ##
							##New Log ##
							$manage_log->player_log($row['ship_id'],21,$sector,$attacker_id,'','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
						}
						else
						{
							## log failed to kill sector mines ##
						}
					}
				}
			}
		}
		/*defences not destroyed... update the sector information to reflect the damage that was inflicted in the attack*/
		if($sector_fighters>0)
		{
			/*we dont want to be wiping out team mates fighters*/
			$user_team = $this->team_id($attacker_id);
			$result = $this->connect->query("SELECT * FROM ".$db_prefix."sector_defence WHERE sector_id=".$sector." and defence_type ='F' and ship_id!='".$attacker_id."' ORDER BY quantity DESC");
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			if($result)
			{
				foreach ($result as $row)
				{
					if($user_team==0)
					{
						
						/*Your not a member of a team, so you have no team mates to worry about.... delete away!!*/
						if(($sector_fighters==0) or ($sector_fighters<1))
						{
							$result = $this->connect->query("DELETE FROM ".$db_prefix."sector_defence WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='F'");
							if($result->execute())
							{
								## log sector fighters killed ##
								##New Log ##
								$manage_log->player_log($row['ship_id'],20,$sector,$attacker_id,'','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
							}
							else
							{
								## log failed to kill sector fighters ##
							}
						}
						else
						{
							/*lets remove some of those fighters*/
							$result = $this->connect->prepare("UPDATE ".$db_prefix."sector_defence SET quantity = ? WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='F'");	
							$result->bindParam(1, $sector_fighters, PDO::PARAM_INT);	
							if($result->execute())
							{
								## log sector defence changes ##	
								##New Log ##
								$manage_log->player_log($row['ship_id'],22,$sector,$attacker_id,$sector_fighters,'',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');									
							}
							else
							{
								## log sector defence change failed##
							}
						}
						if($sector_fighters<0)
						{
							/*little fix for negative values, add them back on*/
							$sector_fighters = 0 - $sector_fighters;
						}
					}
					else if($user_team==$this->team_id($row['ship_id']))
					{
						/* Fighters are part of your own team... cease firing ... CEASE FIRING!?!?! */
					}
					else
					{
						/*Ohhh we can attack them defences!*/
						/*Your not a member of a team, so you have no team mates to worry about.... delete away!!*/
						if(($sector_fighters==0) or ($sector_fighters<1))
						{
							$result = $this->connect->query("DELETE FROM ".$db_prefix."sector_defence WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='F'");
							if($result->execute())
							{
								## log sector fighters killed ##
								##New Log ##
								$manage_log->player_log($row['ship_id'],20,$sector,$attacker_id,'','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
							}
							else
							{
								## log failed to kill sector fighters ##
							}
						}
						else
						{
							/*lets remove some of those fighters*/
							$result = $this->connect->prepare("UPDATE ".$db_prefix."sector_defence SET quantity = ? WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='F'");	
							$result->bindParam(1, $sector_fighters, PDO::PARAM_INT);	
							if($result->execute())
							{
								## log sector defence changes ##
								##New Log ##
								$manage_log->player_log($row['ship_id'],22,$sector,$attacker_id,$sector_fighters,'',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');											
							}
							else
							{
								## log sector defence change failed##
							}
						}
						if($sector_fighters<0)
						{
							/*little fix for negative values, add them back on*/
							$sector_fighters = 0 - $sector_fighters;
						}
					}
				}
			}
		}
		if($sector_mines>0)
		{
			/*we dont want to be wiping out team mates fighters*/
			$user_team = $this->team_id($attacker_id);
			$result = $this->connect->query("SELECT * FROM ".$db_prefix."sector_defence WHERE sector_id=".$sector." and defence_type ='M' and ship_id!='".$attacker_id."' ORDER BY quantity DESC");
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			if($result)
			{
				foreach ($result as $row)
				{
					if($user_team==0)
					{
						/*Your not a member of a team, so you have no team mates to worry about.... delete away!!*/
						if(($sector_mines==0) or ($sector_mines<1))
						{
							$result = $this->connect->query("DELETE FROM ".$db_prefix."sector_defence WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='M'");
							if($result->execute())
							{
								## log sector mines killed ##
								##New Log ##
								$manage_log->player_log($row['ship_id'],21,$sector,$attacker_id,'','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
							}
							else
							{
								## log failed to kill sector mines ##
							}
						}
						else
						{
							/*lets remove some of those mines*/
							$result = $this->connect->prepare("UPDATE ".$db_prefix."sector_defence SET quantity = ? WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='M'");	
							$result->bindParam(1, $sector_mines, PDO::PARAM_INT);	
							if($result->execute())
							{
								## log sector defence changes ##	
								##New Log ##
								$manage_log->player_log($row['ship_id'],23,$sector,$attacker_id,$sector_mines,'',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');											
							}
							else
							{
								## log sector defence change failed##
							}
						}
						if($sector_mines<0)
						{
							/*little fix for negative values, add them back on*/
							$sector_mines = 0 - $sector_mines;
						}
					}
					else if($user_team==$this->team_id($row['ship_id']))
					{
						/* mines are part of your own team... cease firing ... CEASE FIRING!?!?! */
					}
					else
					{
						/*Ohhh we can attack them defences!*/
						/*Your not a member of a team, so you have no team mates to worry about.... delete away!!*/
						if(($sector_mines==0) or ($sector_mines<1))
						{
							$result = $this->connect->query("DELETE FROM ".$db_prefix."sector_defence WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='M'");
							if($result->execute())
							{
								## log sector mines killed ##
								##New Log ##
								$manage_log->player_log($row['ship_id'],21,$sector,$attacker_id,'','',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');
							}
							else
							{
								## log failed to kill sector mines ##
							}
						}
						else
						{
							/*lets remove some of those mines*/
							$result = $this->connect->prepare("UPDATE ".$db_prefix."sector_defence SET quantity = ? WHERE sector_id = '".$sector."' AND defence_id = '".$row['defence_id']."' AND defence_type='M'");	
							$result->bindParam(1, $sector_mines, PDO::PARAM_INT);	
							if($result->execute())
							{
								## log sector defence changes ##	
								##New Log ##
								$manage_log->player_log($row['ship_id'],23,$sector,$attacker_id,$sector_mines,'',"<font color='#FF0000'>High Priority</font>",'<b><font color="#FF0000">Warning</font></b>');											
							}
							else
							{
								## log sector defence change failed##
							}
						}
						if($sector_mines<0)
						{
							/*little fix for negative values, add them back on*/
							$sector_mines = 0 - $sector_mines;
						}
					}
				}
			}
		}
	}
	#############################################################################################
	# KILL PLAYER SHIP																			#
	#																							#
	#############################################################################################
	public function kill_ship($ship_id){
		global $db_prefix;
		$manage_log = new manage_log();
		if($ship_id>0)
		{
			/*DISABLED UNTIL BETTER METHOD TO HANDLE DESTROYED SHIPS AT ACCOUNT LEVEL*/
			//$kill_player_ship = $this->connect->prepare("UPDATE ".$db_prefix."ships SET ship_destroyed='Y' WHERE ship_id='".$ship_id."'");
			$kill_player_ship = $this->connect->prepare("UPDATE ".$db_prefix."ships SET ship_destroyed='N' WHERE ship_id='".$ship_id."'");		
			if($kill_player_ship->execute())
			{
				# Security Log #	
				$manage_log->security_log(0,6);											
			}
			else
			{
				# Security Log #
				$manage_log->security_log($ship_id,4,$kill_player_ship->errorInfo());
				
			}
		}
		else
		{
			# LOG FAIL NO TARGET #
			$manage_log->security_log(0,5);
		}
	}
	#############################################################################################
	# DISABLE XENOBE																			#
	#																							#
	#############################################################################################
	public function disable_xenobe($ship_email){
		/*
		
		######## NEED REWRITING - TRACK XENOBES BY ID NOT EMAIL!!?!?!?!
		
		*/
		global $db_prefix;
		$manage_log = new manage_log();
		$disable_xenobe_ship = $this->connect->prepare("UPDATE ".$db_prefix."xenobe SET active='N' WHERE xenobe_id='".$ship_email."'");		
		if($disable_xenobe_ship->execute())
		{
			# LOG THIS #											
		}
		else
		{
			# LOG FAIL #
		}

	}
	
	#############################################################################################
	# UPDATE THE PLAYERS INFORMATION															#
	#																							#
	#############################################################################################
	public function updatePlayer($id,$table,$query){
		global $db_prefix;
		$manage_log = new manage_log();
		
		foreach ($query as $key => $field) {
			/*Build list of fields to process*/
			$fields_to_update .= $key." = ?, ";
		}
		$fields_to_update = substr($fields_to_update, 0, -2); /*FIX: Remove the last added comma*/

		$update_player = $this->connect->prepare("UPDATE ".$db_prefix.$table." SET ".$fields_to_update." WHERE ship_id='".$id."'");
		$n = 1;
		foreach ($query as $key => &$field) {
			if(is_numeric($field))
			{
				# NUMBER #
				$update_player->bindParam($n, $field, PDO::PARAM_INT);
			}
			else
			{
				# STRING #
				$update_player->bindParam($n, $field, PDO::PARAM_STR);
			}
			$n++;
		}
		
		if($update_player->execute())
		{
			# LOG THIS #
			if($this->is_that_xenobe($id))
			{
				/*Dont want xenobes spamming the logs*/
			}
			else
			{
				$manage_log->security_log($id,7,$table,$fields_to_update);
			}
			return true;											
		}
		else
		{
			# LOG FAIL #
			$manage_log->security_log($id,8,$update_player->errorInfo());
			return false;
		}
			
	}
	#############################################################################################
	# UPDATE RESOURCE																			#
	#																							#
	#############################################################################################
	public function update_resources($player_id,$facility,$new_stat){
		global $db_prefix;
		$update_resources = $this->connect->prepare("UPDATE ".$db_prefix."account SET ".$facility."=".$facility."+".$new_stat." WHERE user_id=?");
		$update_resources->bindParam(1, $player_id, PDO::PARAM_INT);
		if($update_resources->execute())
		{	
			return true;									
		}
		else
		{
			return false;
		}
	}
	#############################################################################################
	# GENERATE RESOURCE																			#
	#																							#
	#############################################################################################
	public function generate_resource($player_id,$facility){
		global $db_prefix,$facility_shipyard_parts,$facility_research_points,$facility_hydroponics_food,$facility_solarplant_energy,$facility_mining_ore;
		$generating_stuff = $this->connect->query("SELECT * FROM ".$db_prefix."planets WHERE owner=".$player_id." AND ".$facility."='Y'");
		$generating_stuff->execute();
		$count = $generating_stuff->rowCount();
			if($facility == "facility_shipyard")
			{
				$count = ($count * $facility_shipyard_parts)+$facility_shipyard_parts;
				$empire_resource = "empire_parts";
			}
			else if($facility == "facility_research")
			{
				$count = ($count * $facility_research_points)+$facility_research_points;
				$empire_resource = "empire_research";
			}
			else if($facility == "facility_hydroponics")
			{
				$count = ($count * $facility_hydroponics_food)+$facility_hydroponics_food;
				$empire_resource = "empire_food";
			}
			else if($facility == "facility_solarplant")
			{
				$count = ($count * $facility_solarplant_energy)+$facility_solarplant_energy;
				$empire_resource = "empire_energy";
			}
			else if($facility == "facility_bank")
			{
				$count = ($count * $facility_mining_ore)+$facility_mining_ore;
				$empire_resource = "empire_ores";
			}
			
			if($this->update_resources($player_id,$empire_resource,$count))
			{	
				return true;									
			}
			else
			{
				return false;
			}
		
	}
	#############################################################################################
	# SCHEDULE EMPIRE																			#
	#																							#
	#############################################################################################
	public function schedule_empire(){
		global $db_prefix;
		$result = $this->connect->query("SELECT user_id FROM ".$db_prefix."account");
		$result = $result->fetchAll(PDO::FETCH_ASSOC);
		$total = 0;
		if($result)
		{
			foreach ($result as $row)
			{
				if($this->generate_resource($row['user_id'],"facility_shipyard"))
				{}
				if($this->generate_resource($row['user_id'],"facility_research"))
				{}
				if($this->generate_resource($row['user_id'],"facility_hydroponics"))
				{}
				if($this->generate_resource($row['user_id'],"facility_solarplant"))
				{}
				if($this->generate_resource($row['user_id'],"facility_bank"))
				{}
				
			}
		}
	}
	
}
?>