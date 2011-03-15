<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Gird System Class. Rather versatile as far as grid width and grid structure.
 *                   
 * @note The naming conventions for properties of the grid are those used in the CSS3 spec. 
 *  If its not intuitive blame them not me.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_Grid
{   
  /**
   * @var int The number of columns in this grid.
   **/
  var $count  = 12;      
  
  /**
   * @var int The total width of the grid.
   **/
  var $width = 978;      
  
  /**
   * @var int Gap/margin between columns.
   **/
  var $gap = 30;  
  
  /**
   * @var int Border-rule/Ruler space between columns. Can be used for borders etc.
   **/
  var $rule = 0;      
  
  /**
   * @var int The base width of columns.
   **/
  var $columnWidth = 54;       
  
  /**
   * @var string Optional name for the gird class. Used as an ID when generating the css.
   *  Might be used to maintain multiple grids in the same template.
   **/    
  var $name;
  
  /**
   * Constructor function.
   *  
   * @param array $props Grid properties
   *  $props['count'], 
   *  $props['width']
   *  $props['gap'], 
   *  $props['rule'],
   *  $props['columnWidth']
   * @return void
   **/
  public function __construct($props = array())
  {  
    if(!empty($props))
    {                      
      if(isset($props['count']))       $this->count       = $props['count']; 
      if(isset($props['width']))       $this->width       = $props['width'];
      if(isset($props['gap']))         $this->gap         = $props['gap']; 
      if(isset($props['rule']))        $this->rule        = $props['rule'];
      if(isset($props['columnWidth'])) $this->columnWidth = $props['columnWidth'];   
      if(isset($props['name']))        $this->name        = $props['name'];
    }
  }    
   
// ------------------------------------------------------------------------
  
  /**
   * Returns CSS3 style string for creating columns in text bodies.
   *  
   * @return string $result
   **/  
  public function getCSS3()
  { 
    $result = ".grid { column-count: $this->count; column-gap: $this->gap; 
      column-width: $this->columnWidth; column-rule: $this->rule; }";         
    if(!is_null($this->name)) {
      $result = '#' . $this->name . ' ' . $result;
    }
    return $result;
  }    
  
// ------------------------------------------------------------------------
  
  /**
   * Returns CSS2 Styles. All grid classes, container class and their styles.
   *  
   * @return string $gridclasses
   **/
  public function getCSS2()
  {   
    $gridclasses = ".grid1 { width: " . $this->columnWidth . "px; }";    
    for($i = 1; $i < $this->count; $i++)
    {               
      $gridwidth = $this->columnWidth * ($i + 1);
      $gridwidth = $gridwidth + ($this->gap * $i);
      $gridclasses .= "\n" . ".grid" . ($i + 1) . " { width: " . $gridwidth . "px; }";
    }  
    return $gridclasses;
  }   
  
// ------------------------------------------------------------------------
  
  /**
   * Generates the correct column-span class for a column.
   *  
   * @return string $class
   **/
  public function genColumnClass($columnOBJ)
  {         
    $gridclass = 'grid grid';
    $class = ''; 
    $widthCount = $this->count;    
    
    if($columnOBJ->alpha == true) $class .= ' alpha';  
    if($columnOBJ->omega == true) $class .= ' omega';     
    
    if(!is_null($columnOBJ->layout))
    { 
      $distributeRemainder = true;   
      $columnsCount = count($columnOBJ->layout->columns); 
      
      if(!is_null($columnOBJ->width)) {    
        $gridclass .= $columnOBJ->width;
      } 
      else 
      {
        foreach($columnOBJ->layout->columns as $column)
        {
          if($column->width) {
            $columnsCount--;   
            $widthcount = $widthcount - $column->width;
          } 
          if(!$column->width AND $column->rendered == true) {      
            $distributeRemainder = false;
          }    
        }      
        $remainder = $widthCount % $columnsCount;   
        if($distributeRemainder == true)     
          $gridclass .= $widthCount / $columnsCount + $remainder;  
        else
          $gridclass .= $widthCount / $columnsCount;  
      }
    }   
    else {
      $class .= ' lonely';
    }    
    
    $class .= $gridclass;
    return $class;
  } 
}