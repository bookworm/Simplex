<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * A container for layout objects.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_Container extends AB_Markup
{  
  /**
   * @var string Name of the container. Required.
   **/ 
  var $name;   
  
  /**
   * Constructor Function
   * 
   * @param string $name Name of the container. Required. 
   * @param object $grid Grid object. Optional.
   * @return void
   **/
  public function __construct($name, $grid = null)
  {       
    // Extend this class with methods and properties from the; 'AB_Layouts', 'AB_Grid_Layouts' classes.
    parent::extendClass(array('AB_Layouts', 'AB_Grid_Layouts'));       
    
    $this->name = $name;  
    $this->grid = $grid;
  } 
    
// ------------------------------------------------------------------------     
  
  /**
   * Renders the container and returns the rendered string.
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
<?php foreach($this->layouts as $layout): ?>
<?php echo $layout->render(); ?>
<?php endforeach; ?>
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
    $result = "<div id='$this->name' class='container'";          
    $result .= '>';
    return $result;
  }          
}   