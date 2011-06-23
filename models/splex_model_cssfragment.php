<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Saves a css fragment.
 * 
 * @package     simplex
 * @subpackage  save
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Splex_Model_CSSfragment extends Splex_Model_Abstract
{   
  
  /**
   * Empty Constructor. Empty like my soul.
   * 
   * @return void
   **/
  function __construct()
  {  
    $this->successMessage = "Saved Param value successfully.";
  }  
  
// ------------------------------------------------------------------------
  
  /**
   * Saves the css fragment.
   *            
   * @return void
   **/
  public function save()
  {
    $splex = getSplexInstance();  
        
    $splex->jpog->paramObjs['css']->value = JRequest::getString('cssFragment');
    $splex->jpog->paramObjs['css']->save();    
    
    $splex->jpog->dump();      
    $this->success = true;
  }
}