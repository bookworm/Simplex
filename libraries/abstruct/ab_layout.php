<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Layout class. Can hold columns or modules.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_Layout extends AB_Markup
{  
  /**
   * @var string Name of the layout. Required.
   **/ 
  var $name;  
  
  /**
   * Constructor Function
   * 
   * @param string $name Name of the layout. Required. 
   * @param array $props Layout properties.
   * @return void
   **/
  public function __construct($name, $props = array())
  { 
    // Extend this class with methods and properties from the; 'AB_Modules', 'AB_Columns', 'AB_Grid_Layouts'
    // classes.
    parent::extendClass(array('AB_Modules', 'AB_Columns', 'AB_Grid_Layouts'));       
    
    $this->name = $name;    
    
    if(!empty($props))
    {    
      if(isset($props['alpha'])) $this->alpha    = $props['alpha'];    
      if(isset($props['omega'])) $this->omega    = $props['omega'];
    }
  }   
  
// ------------------------------------------------------------------------   

  /**
   * Renders the layout and returns the rendered string.
   *   
   * @return string
   **/ 
  public function render()
  {    
    ob_start(); 
    ?>     
<?php echo $this->genOpenTag(); ?>
<?php echo $this->domWrap(); ?>  
<?php echo $this->getBefore(); ?>   
<?php echo !empty($this->columns); ?>
<?php if(empty($this->columns) != false): ?>   
<?php foreach($this->columns as $column): ?> 
<?php echo $column->render(); ?>
<?php endforeach; ?>   
<?php else: ?>    
<?php foreach($this->modules as $module): ?>
<?php echo $module->render(); ?>
<?php endforeach; ?>     
<?php endif; ?>
<?php echo $this->getAfter(); ?>    
<?php echo $this->domClose(); ?>     
</div> 
    <?php
    return ob_get_clean();
  }     
  
// ------------------------------------------------------------------------   

  /**
   * Generates the opening div tag,
   *   
   * @return string
   **/
  public function genOpenTag()
  {       
    $result = "<div id='$this->name'  class='layout grid12' ";    
    $result .= '>';
    return $result;
  }
}