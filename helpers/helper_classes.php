<?php   

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );     

// ------------------------------------------------------------------------

/**
 * Class Helpers. 
 *
 * @note These Functions help you work with Joomla! page and component classes.
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
 * BodyClass Function
 *  
 * Usage:
 * {{{
 *    echo bodyClass(); Usually place this as body class e,g 
 *    <body class="<?php echo bodyClass(); ?>">    
 * }}} 
 *
 * @param mixed $bodyClass a extra body or body classes
 * @return string  
 * @prerequisite Splex_Browser::  
 * @prerequisitefile splex_browser.php
 **/ 
if(!function_exists('bodyClass')) 
{
  function bodyClass($bodyClass = null) 
  {  
    $mainframe  = JFactory::getApplication();
    $splex      = getSplexInstance();
    $params     = $mainframe->getParams();   
    $pageclass  = $params->get('pageclass_sfx'); 
    $pageclass .= ' ' .JRequest::getVar('option');   
    
    // Push Classes From Template Config Into the Body Class Array
    if(isset($splex->tconfig->bodyClasses) AND is_array($splex->tconfig->bodyClasses)) 
    {   
      foreach($splex->tconfig->bodyClasses as $val) {
        $pageclass .= ' ' . $val;
      }
    }     

    // Push Passed Classes Into the Body Class Array 
    if ($bodyClass) 
    {                
      if(!is_array($bodyClass)) $bodyClasses = array($bodyClass);   
      
      foreach($bodyClasses2 as $bodyClass) {
        $pageclass .= ' ' . $bodyClass;
      }   
    }
         
    // Add the users browser to the body class array
    $browser    = strtolower($splex->browser->getBrowser());   
    $pageclass .=  ' ' . $browser;  
    
    return $pageclass;  
  }  
}