<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'DAoC';
  var $maxlevel = false;
  var $version  = '1.0';

  function do_it($install,$lang)
  {
    global $db;
    $classes = array(
      array('Unknown','Unknown',0,0),
      array('Animist','Plate',0,0),
      array('Armsman','Plate',0,0),
      array('Bainshee','Plate',0,0),
      array('Bard','Plate',0,0),
      array('Berserker','Plate',0,0),
      array('Blademaster','Plate',0,0),
      array('Bonedancer','Plate',0,0),
      array('Cabalist','Plate',0,0),
      array('Champion','Plate',0,0),
      array('Cleric','Plate',0,0),
      array('Druid','Plate',0,0),
      array('Eldritch','Plate',0,0),
      array('Enchanter','Plate',0,0),
      array('Friar','Plate',0,0),
      array('Healer','Plate',0,0),
      array('Heretic','Plate',0,0),
      array('Hero','Plate',0,0),
      array('Hunter','Plate',0,0),
      array('Infiltrator','Plate',0,0),
      array('Mentalist','Plate',0,0),
      array('Mercenary','Plate',0,0),
      array('Minstrel','Plate',0,0),
      array('Necromancer','Plate',0,0),
      array('Nightshade','Plate',0,0),
      array('Paladin','Plate',0,0),
      array('Ranger','Plate',0,0),
      array('Reaver','Plate',0,0),
      array('Runemaster','Plate',0,0),
      array('Savage','Plate',0,0),
      array('Scout','Plate',0,0),
      array('Shadowblade','Plate',0,0),
      array('Shaman','Plate',0,0),
      array('Skald','Plate',0,0),
      array('Sorcerer','Plate',0,0),
      array('Spiritmaster','Plate',0,0),
      array('Thane','Plate',0,0),
      array('Theurgist','Plate',0,0),
      array('Valewalker','Plate',0,0),
      array('Valkyrie','Plate',0,0),
      array('Vampiir','Plate',0,0),
      array('Warden','Plate',0,0),
      array('Warlock','Plate',0,0),
      array('Warrior','Plate',0,0),
      array('Wizard','Plate',0,0)
    );

    $races = array(
      'Unknown',
      'Valonian',
      'Briton',
      'Half Ogre',
      'Highlander',
      'Inconnu',
      'Saracen',
      'Celt',
      'Elf',
      'Firbolg',
      'Lurikeen',
      'Shar',
      'Sylvan',
      'Dwarf',
      'Frostalf',
      'Kobold',
      'Norse',
      'Troll',
      'Valkyn'
    );

    $factions = array(
      'Albion',
      'Hibernia',
      'Midgard'
    );
    
    //lets do some tweak on the templates dependent on the game
    $aq =  array();
    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );

    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
    if($install)
    {
    	array_push($aq, "UPDATE __config SET config_value = 32 WHERE config_name='default_style' ;");
    	array_push($aq, "UPDATE __users SET user_style = '32' ;");
    }

    //Itemstats
    array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_itemstats' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");


    // this is the fix stuff.. instert the new information
    // into the database. moved it to a new class, its easier to
    // handle
    $gmanager = new GameManagerPlus();
    $game_config = array(
      'classes'       => $classes,
      'races'         => $races,
      'factions'      => $factions,
      'class_colors'  => false,
      'max_level'     => $this->maxlevel,
      'add_sql'       => $aq,
      'version'       => $this->version,
    );
    
    $gmanager->ChangeGame($this->gamename, $game_config, $lang);
   }
}

?>
