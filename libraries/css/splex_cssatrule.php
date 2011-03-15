<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS AtRule Class
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
class Splex_CSSAtRule extends Splex_CSSRuleSet 
{  
  /**
   * @var string The type of at rule.
   **/
  var $type;
  
  /**
   * Constructor.
   *   
   * @param string $type The type of at rule.     
   * @return void
   */
  public function __construct($type) 
  {
    parent::__construct();
    $this->type = $type;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Returns a css at rule string.
   *   
   * @return string
   */
  public function __toString() 
  {
    $result = "@{$this->type} {";
    $result .= parent::__toString();
    $result .= '}';
    return $result;
  }
}