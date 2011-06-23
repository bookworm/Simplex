<?php
  
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
    
// ------------------------------------------------------------------------

/**
 * Helpers used in AbSTRUCT.
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
 * Render Module For AbSTRUCT. Interfaces with the Simplex renderModule function.
 *  
 * @return string
 **/   
if(!function_exists('ab_renderModule'))
{
  function ab_renderModule($name, $chrome, $moduleProps = array()) 
  {   
    $moduleProps['name'] = $name;     
    $module = $moduleProps;
    echo loadModule($module, $chrome);
  }   
}