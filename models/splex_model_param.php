<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Saves a param value. Very simple class.
 * 
 * @package     simplex
 * @subpackage  save
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Splex_Model_Param extends Splex_Model_Abstract
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
   * Saves the param.
   *            
   * @param mixed $params Name of the param to save an array of params with corresponding names and values.   
   *  e.g array("paramName" => $value);
   * @param mixed $value Value if just passing a value to save.
   * @return void
   **/
  public function save($params, $value = null)
  {
    $splex = getSplexInstance();      
    
    if(is_string($params)) {
      $splex->jpog->paramObjs[$params]->value = $value;           
      $splex->jpog->paramObjs[$params]->save();
    }
    else
    {
      foreach($params as $name => $value) {
         $splex->jpog->paramObjs[$name]->value = $value;   
         $splex->jpog->paramObjs[$name]->save();
      }
    }          
    
    $splex->jpog->dump();   
       
    $this->success = true;
  }
}