<?php          

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Stuff shared between anything handling columns.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_Columns 
{   
  /**
   * @var array The columns within the column/layout etc.   
   **/ 
  var $columns = array();     
  
  /**
   * Empty Constructor
   *  
   * @return void
   **/
  public function __construct() { }     
  
// ------------------------------------------------------------------------
  
  /**
   * Adds a column to the column
   *  
   * @param object $column The column object.
   * @return void
   **/
  public function addColumn($column)
  {
    $this->columns[$column->name] = $column;
  }  
  
// ------------------------------------------------------------------------
  
  /**
   * Moves a column position. 
   *
   * @note Essentially swaps the positions of two columns.
   *  
   * @param string $name Name of the column to move. 
   * @param string $destname Name of the destination column.
   * @return void
   **/
  public function moveColumn($name, $destname)
  {  
    $splex = getSplexInstance();
    $source = $splex->struct->ab->columns[$name];
    $dest   = $splex->struct->ab->columns[$destname];
    $this->columns[$name] = $dest; 
    $this->columns[$destname] = $source;
    unset($source);
    unset($dest);
  }
    
// ------------------------------------------------------------------------
  
  /**
   * Re-order the columns.    
   *
   * @note Its assumed that whatever is passed in here directly relates to current columns array.
   *  I.E: The length of $orders array should match the number of columns and the columns should all already exist
   *  in the columns array.
   *   
   * @param array $orders List of each column in the order they should be.
   * @return void
   */  
  public function orderColumns($orders)
  {   
    // Theoritcally this implementation should work. Hehe.
    $count = 0; 
    $moved = array();    
    
    foreach($this->columns as $k => $column)
    {       
      $move = $orders[$count];   
      if($column->name != $move && !in_array($move, $moved) && !in_array($k, $moved)) 
      {    
        $this->moveColumn($move, $k);     
        $moved[] = $move;
        $moved[] = $k;
      } 
      $count++;
    }
  }  
}