<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------  

/**
 * Column Handling Class. 
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/  
class AB_Column extends AB_Markup
{ 
  /**
   * @var string Name of the column. Required.
   **/    
  var $name;      
  
  /**
   * @var bool Whether or not to equalize this column's width.
   **/
  var $equalize;    
  
  /**
   * @var string Width of column. 
   *  If there is a grid applied to the class then the width is assumed to be the number of "column spans". 
   *  In which case a class will be generated and applied to the class of the column.
   *  By default its assumed to be PX and will be outputted as to style attribute of the column e.g width: 70px;      
   **/ 
  var $width; 
  
  /**
   * @var string Left margin of column only applicable if no grid assigned to the column.
   **/
  var $margin;
  
  /**
   * @var object The parent layout holding this column.
   **/          
  var $layout; 
  
  /**
   * @var bool Whether or not the column has been rendered yet.
   **/  
  var $rendered;     
   
  /**
   * @var string Holds a string of the rendered column.
   **/  
  var $renderedString;   
    
  /**
   * Constructor Function.
   *  
   * @param string $name Give the column a name. Required.
   * @param array $props Column properties.
   *  $props['alpha'], 
   *  $props['omega']
   *  $props['equalize'], 
   *  $props['width'],
   *  $props['margin']
   * @param object $grid Grid object. Optional.
   * @param object $layout Does this have a parent layout?
   * @return void
   **/
  public function __construct($name, $props = array(), $grid = null, $layout = null)
  { 
    // Extend this class with methods and properties from the; 'AB_Modules', 'AB_Columns' classes.
    parent::extendClass(array('AB_Modules', 'AB_Grid_Layouts',));      
        
    if(!empty($props))
    { 
      if(isset($props['alpha']))    $this->alpha    = $props['alpha'];    
      if(isset($props['omega']))    $this->omega    = $props['omega'];
      if(isset($props['equalize'])) $this->equalize = $props['equalize'];    
      if(isset($props['width']))    $this->width    = $props['width'];     
      if(isset($props['margin']))   $this->margin   = $props['margin'];    
    }        
    
    $this->name   = $name;   
    $this->grid   = $grid;
    $this->layout = $layout;
  }   
   
// ------------------------------------------------------------------------  
  
  /**
   * Renders the column and returns the rendered string.
   *   
   * @param bool $reRender Whether or not to re-render this column.
   * @return string
   **/
  public function render($reRender = false)
  {         
    if($this->rendered == true AND $reRender == false) return $this->renderedString; 
    ob_start();
    ?>  
<?php echo $this->genOpenTag(); ?>
<?php echo $this->domWrap(); ?>  
<?php echo $this->getBefore(); ?>
<?php foreach($this->modules as $module): ?>  
<?php echo $module->render(); ?>
<?php endforeach; ?>
<?php echo $this->getAfter(); ?>    
<?php echo $this->domClose(); ?>     
</div>       
    <?php    
    $this->rendered == true;
    $this->renderedString = ob_get_clean();   
    return $this->renderedString;
  }  
  
// ------------------------------------------------------------------------  
  
  /**
   * Generates the opening div tag,
   *   
   * @return string
   **/
  public function genOpenTag()
  {       
    $result = "<div id='$this->name' ";    
    if($this->grid == null) 
    {
      $styles  = "style='";      
      $styles .= "width: $this->width; ";      
      
      if($this->alpha == null) $styles .= "margin-left: $this->width;";   
      
      $styles .= "'";           
      $result .= $styles;
    }       
    else {      
      $class = $this->grid->genColumnClass($this);
      $result .= "class='$class column'";
    }     
    
    $result .= '>';
    return $result;
  } 
  
// ------------------------------------------------------------------------  
  
  /**
   * Sets the parent layout object.
   *  
   * @param object $layout The layout object.
   * @return void
   **/
  public function setParent($layout)
  {
    $this->parent = $layout;
  }     
}