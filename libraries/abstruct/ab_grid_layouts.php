<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------   

/**
 * Stuff shared between anything that has a grid applied to it.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/  
class AB_Grid_Layouts
{   
  /**
   * @var object Holds the Grid class that is applied to the column.     
   **/
  var $grid = null;
  
  /**
   * @var bool Whether or not this column is the first column in its parent layout.      
   **/
  var $alpha = false;   
  
  /**
   * @var bool Whether or not this column is the last column in its parent layout.  
   **/
  var $omega = false;    
  
  /**
   * Empty Constructor
   *  
   * @return void
   **/
  public function __construct() { } 
  
// ------------------------------------------------------------------------   
  
  /**
   * Sets the grid.
   *  
   * @param object $grid The grid object.
   * @return void
   **/
  public function setGrid($grid)
  {
    $this->grid = $grid;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Sets this column as the first column (alpha) in the current layout.
   *  
   * @param bool $alpha Whether or not its alpha. Default: false
   * @return void
   **/
  public function setAplpha($alpha = false)
  {
    $this->alpha  = $alpha;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Sets this column as the last column (omega) in the current layout.
   *  
   * @param bool $omega Whether or not its omega. Default: false
   * @return void
   **/
  public function setOmega($omega = false)
  {
    $this->omega  = $omega;
  }  
}