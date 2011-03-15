<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS Color Value Class
 *
 * @note Based on the css parser by Raphael Schweikert https://github.com/sabberworm/PHP-CSS-Parser
 *
 * @package     simplex
 * @subpackage  libraries
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.  
 * @author      2010 Raphael Schweikert http://www.sabberworm.com/
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.   
 * @todo Better support for colors?
 */
class Splex_CSSColor extends Splex_CSSValue 
{  
  /**
   * @var string The color.
   **/
  var $color;
  
  /** 
   * The constructor.
   *
   * @param string $color The color. 
   * @return void
   **/
  public function __construct($color) 
  {
    $this->color = $color;
  }      
  
// ------------------------------------------------------------------------
    
  /** 
   * Get the color description by imploding the color.
   *
   * @return string
   **/
  public function getColorDescription() 
  {
    return implode('', array_keys($this->color));
  }
  
// ------------------------------------------------------------------------   
  
  /** 
   * Turns this into css string.
   *
   * @return string
   **/
  public function __toString() 
  {
    return $this->getColorDescription().'('.implode(', ', $this->color).')';
  }
}