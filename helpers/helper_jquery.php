<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );  

// ------------------------------------------------------------------------

/**
 * Simplex jQuery Helpers.
 *
 * @package     simplex
 * @subpackage  helpers
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */

// ------------------------------------------------------------------------

/**
 * Wraps JS in a jQuery document.ready()
 *
 * @param mixed $content The JS content to wrap it in.   
 * @return objbuffer   
 **/   
if(!function_exists('domReady'))
{
  function domReady($content)
  {   
    ob_start();
    ?>
jQuery.noConflict();

jQuery(document).ready(function($) {
  <?php echo $content;?>
});      
    <?php
    return ob_get_clean();  
  }  
}  

// ------------------------------------------------------------------------
         
/**
 * Takes some plugin options and returns a jQuery formatted settings object
 *
 * @param array $options An Array of Config options.   
 * @return objbuffer   
 **/      
if(!function_exists('jqOptions'))
{
  function jqOptions($options)
  {  
    ob_start();
    ?>
<?php $l = count($options);
foreach($options as $i => $option): ?> 
  <?php echo "$option:"; echo "' "; echo $option['config']; echo" '"; if (!$i == $l - 1) echo',';  ?>  
<?php endforeach;?>   
    <?php
    return ob_get_clean();     
  }  
}