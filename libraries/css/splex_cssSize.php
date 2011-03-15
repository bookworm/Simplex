<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS Size. 
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
 */
class Splex_CSSSize extends Splex_CSSValue 
{ 
  /**
   * @var int The size.
   **/
  var $size;     
  
  /**
   * @var string The unit this size is in. e.g em
   **/
  var $unit = '';
  
  /**
   * Constructor, 
   *
   * @param int $size    The size.  
   * @param string $unit The unit this size is in. e.g em           
   * @return void
   **/
  public function __construct($size, $unit = null)
  {
    $this->size = floatval($size);
    $this->unit = $unit;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Sets the size.
   *
   * @param int $size The size. 
   * @return void
   **/
  public function setSize($size) 
  {
    $this->size = floatval($size);
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Whether or not the size is relative e.g em or %.
   *
   * @return bool
   **/
  public function isRelative() 
  {
    if($this->unit === '%' || $this->unit === 'em' || $this->unit === 'ex')
      return true;       
      
    if($this->unit === null && $this->size != 0)
      return true;
      
    return false;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Spits out a css string for this.
   *
   * @return string
   **/
  public function __toString() 
  {
    return $this->size.($this->unit === null ? '' : $this->unit);
  }
}