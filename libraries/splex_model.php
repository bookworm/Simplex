<?php   

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Load Simplex + Prequisites
$splex = getSplexInstance();
$splex->loader->load_include('splex_model_abstract.php');

// ------------------------------------------------------------------------

/**
 * Model Class. Handles ajax and save stuff. 
 *
 * @note The was a serious lack of originality when I named this class Model. I shamelessly stole the naming from Gantry.    
 * At the moment its just a simple wrapper around the models.
 * 
 * @package     simplex
 * @subpackage  libraries
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Splex_Model
{ 
  /**
   * Constructor Function.
   *
   * @note Empty for now like the soul of your next door neighbor.
   *
   * @return void
   */
  public function __construct() { }     
  
// ------------------------------------------------------------------------
   
  /**
   * Creates a new model object.
   *
   * @param string $name The name of the model
   * @param mixed  $arg An argument object to pass.
   * @return object The Model object
   */
  public function create($name, $arg = null)
  {     
    $splex = getSplexInstance();
        
    $prefix       = "splex" . '_' .'model';
    $filename     = $prefix . '_' . $name;
    $classname    = capitalizeWords($filename, '_');   
    
    if($arg == null)
      $model = $splex->loader->load_class($filename, $classname);
    else
      $model = $splex->loader->load_class($filename, $classname, 'php', true, $arg);     
      
    return $model;                    
  }
}