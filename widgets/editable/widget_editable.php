 <?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Makes Stuff Editable. Like fracking magic.
 * 
 * @package     simplex
 * @subpackage  muwt.widgets
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */ 
class Muwt_Widget_editable extends Muwt_Widget 
{ 
  /**
   * Constructor.
   * 
   * @param object $param The parameter object to construct the widget from.
   * @return void
   **/
  function __construct($param)
  {             
    $this->param = $param; 
    $this->paramName = $this->param->name;   
    
    $this->_addResources(); 
  }  
  
// ------------------------------------------------------------------------

  /**
   * Adds needed resources to head.
   *                                                               
   * @return void
   **/    
  public function _addResources()
  {  
    $splex = getSplexInstance();    

    if(checkHead('editable', 'scripts') == false)
      $splex->muwt->addScript('editable');
    if(checkHead('jeditable.min', 'scripts') == false)
      $splex->muwt->addScript('jeditable.min');
  }  
  
// ------------------------------------------------------------------------

  /**
   * Renders the widget.
   *   
   * @note Empty because its all JS for now. Its here because its required method for widgets.
   *
   * @return string
   **/
  public function render() { } 
}