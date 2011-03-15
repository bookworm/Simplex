<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Saves a module title.
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
class Splex_Model_Moduletitle extends Splex_Model_Abstract
{ 
  /**
   * Empty Constructor. Empty like my soul.
   * 
   * @return void
   **/
  function __construct()
  {  
    $this->successMessage = "Saved Module Title successfully.";
  }  

// ------------------------------------------------------------------------

  /**
   * Saves the module title.
   *            
   * @param mixed $value Value if just passing a value to save.
   * @return void
   **/
  public function save()
  {  
    jimport( 'joomla.database.table' ); 
    jimport( 'joomla.error.error' );
     
    if(!isAdmin()) 
    {
      $this->success = false;  
      $this->response['message'] = 'bad boy';
      return false; 
    }  
    
    $row = JTable::getInstance('module');  
    $db  = JFactory::getDBO();             
    
    $post = array();
    $post['id'] = JRequest::getString('moduleID');
    $post['title'] = JRequest::getString('value');   
    $post['params'] = array();
    
    if (!$row->bind($post)) {
      return JError::raiseWarning( 500, $row->getError() );
    }
    
    if (!$row->store()) {
      JError::raiseError(500, $row->getError() );
    } 
    
    $this->success = true;
  }    
  
  
// ------------------------------------------------------------------------

  /**
   * Custom Response Render Method.
   *   
   * @note Returns a simple string as a response object because jeditable doesn't expect json back.
   *         
   * @return string
   **/ 
  public function response()
  {  
    if($this->success == true)
      return JRequest::getString('value');
    else 
      return $this->successMessage;
  }
}  
