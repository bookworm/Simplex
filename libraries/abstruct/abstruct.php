<?php 

// Loader File For Abstruct.     

// Load Includes     
include_once 'helpers' . DS . 'helpers_files.php';   
include_once 'helpers' . DS . 'helpers_array.php';  
include_once 'helpers' . DS . 'helpers_joomla.php';

// Globals and Super Objs  
global $mainframe;
$templatename = $mainframe->getTemplate();    
$templateOBJ  = JFactory::getDocument();
define('ABSTRUCTPATH', JPATH_SITE . DS . 'templates' .  DS . $templatename . DS . 'abstruct');  

require_once_dir(ABSTRUCTPATH . DS . 'core'); 
require_once_dir(ABSTRUCTPATH);