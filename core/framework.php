<?php       

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Framework Initialization File.
 *
 * @note All The core stuff happens here so bad idea to make modifications anywhere in the core folder.
 * Monsters, bugs. headaches and swear words abound. Tread softly and carry a big debugger.  
 *    
 * @package     simplex
 * @subpackage  core
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */

// ------------------------------------------------------------------------   

// Check that were running PHP 5.2 or newer
version_compare(PHP_VERSION, '5.2', '<') and exit('Simplex requires PHP 5.2 or newer.');  

// Globals and Super Objs  
global $mainframe;
$templatename = $mainframe->getTemplate();    
$templateOBJ  = JFactory::getDocument();  

// File Sytem Path Definitions.
define('BASEPATH',      JPATH_SITE . DS . 'templates' .  DS . $templatename);           

// Check to see if Simplex is installed as a plugin.
$pluginpath = JPATH_SITE . DS . 'plugins' . DS . 'system' . DS . 'splex_plugin_config.php';
if(file_exists($pluginpath)) {
  define('FRAMEWORKPATH', JPATH_SITE . DS . 'plugins' . DS . 'system' . DS . 'simplex'); 
  define('SIMPLEX_LOADED_AS_PLUGIN', true);         
}
else {
  define('FRAMEWORKPATH', JPATH_SITE . DS . 'templates' .  DS . $templatename . DS . 'simplex');      
}        

// URl Paths. Used for media files, scripts, css etc.
define('TEMPLATEURLPATH',  $this->baseurl . '/templates/' . $templatename);  
    
// Require needed files and then load up Simplex.
require_once(    FRAMEWORKPATH . '/core/common.php'); 
require_once 'core_helpers.php';  
require_once(    FRAMEWORKPATH . '/core/base.php');      
$splexCorePath = FRAMEWORKPATH . '/core/simplex.php';    
$splex         = splex_loadClass('Simplex', $splexCorePath, true, $templateOBJ);