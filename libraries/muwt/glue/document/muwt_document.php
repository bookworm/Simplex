<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );  

// ------------------------------------------------------------------------

/**
 * Interface For Adding Stuff To Document <head>.
 *
 * @note Most widgets will need to be able to add their css, js etc to the document head. This class
 * provides an interface for that functionality.
 *    
 * @package     muwt
 * @subpackage  muwt.glue
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
interface Muwt_Document 
{      
  /**
   * Add a Script To The Document <head>
   *  
   * @param string $path The path of the file to add
   **/
  public function addScript($path);  
  
  /**
   * Add a Script Declaration To The Document <head> 
   *
   * @param string $content The Script Content
   **/   
  public function addScriptDeclaration($content);  
  
  /**
   * Add a StyleSheet To The Document <head> 
   *  
   * @param string $path The path of the file to add
   **/
  public function addStyleSheet($path);  
  
  /**
   * Add a Style Declaration To The Document <head>
   *     
   * @param string $content The Script Content
   **/
  public function addStyleDeclaration($content);    
  
  /**
   * Should return a nested array containing the scripts and style sheets. E.g
   * $headData['scripts'], $headData['styles]     
   *   
   * @return array 
   **/
  public function getHeadData();  
}