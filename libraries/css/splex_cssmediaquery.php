<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS Media Query. 
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
class Splex_CSSMediaQuery extends Splex_CSSList 
{  
  /**
   * @var string The media query string.
   **/
  var $query = null;

  /**
   * Constructor Function that calls it's parent constructor. Sort like a teenager.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }
  
// ------------------------------------------------------------------------  
  
  /**
   * Awesome too string conversion functionality. 
   *
   * @return void
   */
  public function __toString() 
  {
    $result = "@media {$this->query} {";
    $result .= parent::__toString();
    $result .= '}';         
    
    return $result;
  }
}