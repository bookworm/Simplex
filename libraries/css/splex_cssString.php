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
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.  
 * @author      2010 Raphael Schweikert http://www.sabberworm.com/
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.   
 */
class Splex_CSSString extends Splex_CSSValue 
{  
  /**
   * @var string Holds the css string
   **/
  var $string;
  
  /** 
   * The Constructor.
   *
   * @param string $string The CSS string.
   * @return void
   **/
  public function __construct($string) 
  {
    $this->string = $string;
  }
  
// ------------------------------------------------------------------------   
  
  /** 
   * Turns this into a CSS string. 
   *
   * @note Writing that out loud sounds so funny. Okay, just giggle inducing level of funny.
   *
   * @return string
   **/
  public function __toString()
  {
    return '"'.addslashes($this->string).'"';
  }
}