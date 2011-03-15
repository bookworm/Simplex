<?php   

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Basically a parent class for the models.   
 *
 * @note Its definitely not a true abstract class but you already new that from how I declared it. right?
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
class Splex_Model_Abstract
{      
  /**
   * @var bool Has this model been successful? 
   **/     
  var $success;  
    
  /**
   * @var array Holds all the error messages.
   **/
  var $errors = array();       
  
  /**
   * @var string The success message.
   **/
  var $successMessage;     
  
  /**
   * @var array Holds the response object.
   **/
  var $response;
  
  /**
   * Empty Constructor. Empty like my soul.
   * 
   * @return void
   **/
  public function __construct() { }   
   
// ------------------------------------------------------------------------
  
  /**
   * Renders the response (error messages, success messages etc) (by default as json) from the model.
   *       
   * @param bool $asJSON Whether or not to return the response as json. Default is true.
   * @return mixed
   **/
  public function response($asJSON = true)
  {         
    $this->response['success'] = $this->success;      
    
    if($this->success == true) 
      $this->response['message'] = $this->successMessage; 
    else
    {
      foreach($this->errors as $error) {
        $this->response['message'] = $this->response['message'] . ' ' . $error;
      }
    }    
      
    if($asJSON == true)
      return json_encode($this->response);  
    else
      return $this->response;
  }      
}