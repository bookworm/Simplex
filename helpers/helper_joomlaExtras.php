<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );   

// ------------------------------------------------------------------------

/**
 * Some Extra Joomla! Helpers. You might or might not find this useful.
 *
 * @package     simplex
 * @subpackage  helpers
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */  

// ------------------------------------------------------------------------          

/**
 * Loads Params Directly From Ini file. 
 *
 * Usage: 
 * {{{
 *    $params = loadParams();    
 * }}}      
 *   
 * @param string $paramsPath The path to the param's file. Defaults to params.ini
 * @return void
 **/ 
if (!function_exists('loadParams'))  
{
  function loadParams($paramsPath = null)
  {   
    if($paramsPath == null)  $paramsPath = $this->directory_path . '/params.ini';  

    if (is_writable($paramsPath)) {
      $params_array = parse_ini_file($paramsPath);
      return $params_array;      
    } 

    else { 
      die("Params.ini needs to be writable for template to function");
    }     
  }  
}

// ------------------------------------------------------------------------          

/**
 * Writes a Param
 *
 * Usage: 
 * {{{
 *    $writeParam('bob', 'isyouruncle');    
 * }}}      
 * 
 * @param string $paramName   Name of the parameter to write  
 * @param mixed  $paramValue  The value to write to the param
 * @param string $paramsPath  The path to the param's file. Defaults to params.ini     
 * @return void
 **/ 
if (!function_exists('writeParam'))     
{
  function writeParam($paramName, $paramValue, $paramsPath = null) 
  {   
    jimport( 'joomla.filesystem.file' );
      
    $params_array = loadParams();    
    if($paramsPath == null)
      $paramsPath =  getcwd() . '/../../params.ini';  
    $params_array[$paramName] = $paramValue;  

    if(is_writable($paramsPath)) 
    { 
      $path      = $paramsPath;   
      $assoc_arr = $params_array;
      $content   = "";                     

      // Loop over the params and store for writing
      foreach ($assoc_arr as $key=>$elem) 
      {
        if(is_array($elem)) {
          for($i=0;$i<count($elem);$i++) {
            $content .= $key."[] = ".$elem[$i]."\n";
          }   
        }
        else if($elem=="") $content .= $key."=\n";
        else $content .= $key."=".$elem."\n";  
      }     

      JFile::write($path, $content); 
    } 
  }
}