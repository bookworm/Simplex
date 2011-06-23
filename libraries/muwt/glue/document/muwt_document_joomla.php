<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );  

// ------------------------------------------------------------------------

/**
 * Joomla! Implementation Of Muwt Head, For Adding Stuff To Document <head>.
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
class Muwt_Document_Joomla implements Muwt_Document
{         
  /**
   * Adds a Script To The Joomla! Document <head>  
   *       
   * @param string $filename The name of the file to add   
   * @return void
   **/
  public function addScript($filename) 
  {
    $splex = getSplexInstance();
    $splex->loader->load_js($filename);      
  }  
 
// ------------------------------------------------------------------------
     
  /**
   * Adds a Script Declaration To The Joomla! Document <head>  
   *
   * @param string $content  The Script Content   
   * @param string $type     MIME type of script 
   * @return void
   **/
  public function addScriptDeclaration($content, $type = 'text/javascript') 
  {
    $doc = JFactory::getDocument();   
    $doc->addScriptDeclaration($content, $type);   
  }
  
// ------------------------------------------------------------------------ 
  
  /**
   * Adds a StyleSheet To The Joomla! Document <head>    
   *       
   * @param string $filename The name of the file to add   
   * @return void
   **/
  public function addStyleSheet($filename) 
  {
    $splex = getSplexInstance();
    $splex->loader->load_css($filename);  
  }                                                                          
  
// ------------------------------------------------------------------------          
  
  /**
   * Adds a Style Declaration To The Joomla! Document <head>  
   *       
   * @param string $content  The Script Content   
   * @param string $type     MIME type of script 
   * @return void
   **/
  public function addStyleDeclaration($content, $type = 'text/css') 
  {
    $doc = JFactory::getDocument();   
    $doc->addStyleDeclaration($content, $type);   
  }
  
// ------------------------------------------------------------------------
  
  /**
   * Returns a nested array containing the scripts and style sheets. i.e
   * $headData['scripts'], $headData['styles]     
   *      
   * @return array 
   **/
  public function getHeadData() 
  {
    $doc      = JFactory::getDocument();  
    $headData = $doc->getHeadData();      
    return $headData;
  }  
}