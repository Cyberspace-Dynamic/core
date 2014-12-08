<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "maintenanceuser_crontask" ) ) {
	class maintenanceuser_crontask extends crontask {

		public function __construct(){
			$this->defaults['description']	= 'Deleting Maintenance-user';
			$this->defaults['delay']		= false;
			$this->defaults['ajax']			= false;
		}

		public function run(){
			$muser = unserialize(stripslashes($this->encrypt->decrypt($this->config->get('maintenance_user'))));
			if ($muser['user_id']){
				$this->db->prepare("DELETE FROM __users WHERE user_id =?")->execute($muser['user_id']);
				
				$this->pdh->put('user_groups_users', 'delete_user_from_group', array($muser['user_id'], 2));
				
				$this->pdh->put('user', 'delete_special_user', array($muser['user_id']));
				$this->logs->add('action_maintenanceuser_deleted', array(), $muser['user_id'], $this->user->lang('maintenanceuser_user'));
			}
			$this->config->set('maintenance_user', '');
		}
	}
}
?>