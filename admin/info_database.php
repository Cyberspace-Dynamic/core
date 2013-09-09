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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class MySQL_Info extends page_generic{
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'pm', 'core', 'config', 'db2');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_config_man');
		$handler = array();
		parent::__construct(false, $handler);
		$this->process();
	}

	// ---------------------------------------------------------
	// Display form
	// ---------------------------------------------------------
	public function display(){
		//Set some default-values
		$table_count = 0;
		$table_size = 0;
		$index_size = 0;

		$arrTables = $this->db2->listTables();

		foreach ($arrTables as $strTablename){
			$arrTableInfos = $this->db2->fieldInformation($strTablename);
			
			$this->tpl->assign_block_vars('table_row', array(
				'TABLE_NAME'	=> $strTablename,
				'ROWS'			=> $arrTableInfos['rows'],
				'COLLATION'		=> $arrTableInfos['collation'],
				'ENGINE'		=> $arrTableInfos['engine'],
				'TABLE_SIZE'	=> $this->convert_db_size($arrTableInfos['data_length']),
				'INDEX_SIZE'	=> $this->convert_db_size($arrTableInfos['index_length']))
			);

			$index_size += $arrTableInfos['index_length'];
			$table_size += $arrTableInfos['data_length'];
			$table_count++;
		}

		$this->tpl->assign_vars(array(
			'NUM_TABLES'		=> sprintf($this->user->lang('num_tables'), $table_count),
			'TOTAL_TABLE_SIZE'	=> $this->convert_db_size($table_size),
			'TOTAL_INDEX_SIZE'	=> $this->convert_db_size($index_size),
			'TOTAL_SIZE'		=> $this->convert_db_size($table_size + $index_size),

			'DB_ENGINE'			=> $this->dbtype,
			'DB_NAME'			=> $this->dbname,
			'DB_PREFIX'			=> $this->table_prefix,
			'DB_VERSION'		=> 'Client ('.$this->db2->client_version.')<br/>Server ('.$this->db2->server_version.')',
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('title_mysqlinfo'),
			'template_file'		=> 'admin/info_database.html',
			'display'			=> true)
		);
	}

	// ---------------------------------------------------------
	// Process Helper
	// ---------------------------------------------------------
	private function convert_db_size($bytes){
		if ( $bytes <= 1024 ){
			return $bytes.' B';
		} elseif ( $bytes <= 1048576 ) {
			return (round($bytes/1024, 2)).' KB';
		} else {
			return (round($bytes/1048576, 2)).' MB';
		}
	}
}
registry::register('MySQL_Info');
?>