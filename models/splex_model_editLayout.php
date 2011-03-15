<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Modifies a layout object.
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
class Splex_Model_editLayout extends Splex_Model_Abstract
{   
  
  /**
   * Empty Constructor. Empty like my soul.
   * 
   * @return void
   **/
  function __construct()
  {  
    $this->successMessage = "Saved Layout Column Order value successfully.";
  }  
  
// ------------------------------------------------------------------------
  
  /**
   * Saves the column order.
   *            
   * @return void
   **/
  public function save()
  {
    $splex = getSplexInstance();    
    
    jimport('joomla.environment.request');   
    
    $post =  JRequest::get('post'); 
    
    $orders    = $post['order']; 
    $layoutID  = JRequest::getString('layoutID');    
    
    $l = $splex->struct->getLayout($layoutID);      
    $l->orderColumns($orders);
    $splex->struct->save();
    
  }
}