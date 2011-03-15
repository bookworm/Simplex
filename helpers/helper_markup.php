<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

// ------------------------------------------------------------------------

/**
 * Simplex Markup Helpers for working with markup. Adds JS, CSS, create links etc.
 *
 * @package     simplex
 * @subpackage  helpers
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */

// ------------------------------------------------------------------------

/**
 * Loads a Layout . Basically a quick include function
 *
 * Usage:
 * {{{
 *    echo loadLayout('header');
 * }}}
 *
 * @param string $layoutName  The name of layout to load
 * @return void
 **/     
if(!function_exists('loadLayout'))    
{
  function loadLayout($layoutname)  
  {   
    $splex = getSplexInstance();  
    echo $splex->loader->loadFile($layoutname, 'layout'); 
  } 
}
  
// ------------------------------------------------------------------------

/**
 * Unordered List
 *
 * @note Generates an HTML unordered list from an single or multi-dimensional array.
 *
 * Usage:
 * {{{
 *    echo ul($listarray); 
 * }}}
 *
 * @param array
 * @param mixed
 * @return string  
 * @see _list()
 */
if (!function_exists('ul'))
{
  function ul($list, $attributes = '')
  {
    return _list('ul', $list, $attributes);
  }
}

// ------------------------------------------------------------------------

/**
 * Ordered List
 *
 * @note Generates an HTML ordered list from an single or multi-dimensional array.
 *
 * Usage:
 * {{{
 *    echo ol($listarray); 
 * }}}
 *
 * @param array
 * @param mixed
 * @return string 
 * @see _list()
 */
if (!function_exists('ol'))
{
  function ol($list, $attributes = '')
  {
    return _list('ol', $list, $attributes);
  }
}

// ------------------------------------------------------------------------

/**
 * Generates an HTML ordered list from an single or multi-dimensional array.
 *
 * @access  private
 * @param string
 * @param mixed
 * @param mixed
 * @param intiger
 * @return string  
 * @see ul()
 * @see ol()    
 */
if (!function_exists('_list'))
{
  function _list($type = 'ul', $list, $attributes = '', $depth = 0)
  {
    // If an array wasn't submitted there's nothing to do...
    if (!is_array($list)) {
      return $list;
    }

    // Set the indentation based on the depth
    $out = str_repeat(" ", $depth);

    // Were any attributes submitted?  If so generate a string
    if (is_array($attributes))
    {
      $atts = '';
      foreach ($attributes as $key => $val) {
        $atts .= ' ' . $key . '="' . $val . '"';
      }
      $attributes = $atts;
    }

    // Write the opening list tag
    $out .= "<".$type.$attributes.">\n";

    // Cycle through the list elements.  If an array is
    // encountered we will recursively call _list()

    static $_last_list_item = '';
    foreach ($list as $key => $val)
    {
      $_last_list_item = $key;

      $out .= str_repeat(" ", $depth + 2);
      $out .= "<li>";

      if (!is_array($val)) {
        $out .= $val;
      }
      else {
        $out .= $_last_list_item."\n";
        $out .= _list($type, $val, '', $depth + 4);
        $out .= str_repeat(" ", $depth + 2);
      }

      $out .= "</li>\n";
    }

    // Set the indentation for the closing tag
    $out .= str_repeat(" ", $depth);

    // Write the closing list tag
    $out .= "</".$type.">\n";
    return $out;
  }
}

// ------------------------------------------------------------------------

/**
 * Generates HTML BR tags based on number supplied
 *
 * Usage:
 * {{{
 *    echo br(5);
 * }}}
 *
 * @param integer
 * @return string   
 */
if (!function_exists('br'))
{
  function br($num = 1)
  {
    return str_repeat("<br />", $num);
  }
}

// ------------------------------------------------------------------------

/**
 * Generates a page document type declaration
 *
 * Usage:
 * {{{
 *    echo doctype('xhtml-trans');
 * }}}
 *
 * @note Valid options are xhtml-11, xhtml-strict, xhtml-trans, xhtml-frame,
 * html4-strict, html4-trans, and html4-frame.  Values are saved in the
 * doctypes config file.
 *
 * @param string  type  The doctype to be generated
 * @return  string   
 */
if (!function_exists('doctype'))
{
  function doctype($type = 'xhtml1-strict')
  {
    global $_doctypes; 
    
    $splex = getSplexInstance();

    if (!is_array($_doctypes))
    {   
      $filepath = $splex->loader->getFilePath('config_doctypes.php');
      if (!require_once($filepath)) {
        return false;
      }
    }
    if (isset($_doctypes[$type])) {
      return $_doctypes[$type];
    }
    else {
      return false;
    }
  }
}

// ------------------------------------------------------------------------

/**
 * Generates meta tags.
 *
 * Usage:
 * {{{
 *    echo meta($name = 'Content-Type', $content = 'text/html', $type =  'http-equiv');
 * }}}
 * $str .= '<meta '.$type.'="'.$name.'" content="'.$content.'" />'.$newline;
 * @param string $name    The value of the content type attribute.  
 * @param string $content The value of the conten attribute.
 * @param string $type    The type of mete content
 * @param string $newline Something to insert after the meta tag. Default is "\n"    
 * @return string  
 */
if (!function_exists('meta'))
{
  function meta($name = '', $content = '', $type = 'name', $newline = "\n")
  {
    // Since we allow the data to be passes as a string, a simple array
    // or a multidimensional one, we need to do a little prepping.
    if (!is_array($name)) {
      $name = array(array('name' => $name, 'content' => $content, 'type' => $type, 'newline' => $newline));
    }
    else
    {
      // Turn single array into multidimensional
      if (isset($name['name'])) {
        $name = array($name);
      }
    }

    $str = '';
    foreach ($name as $meta)
    {
      $type     = (!isset($meta['type']) OR $meta['type'] == 'name') ? 'name' : 'http-equiv';
      $name     = (!isset($meta['name']))   ? ''  : $meta['name'];
      $content  = (!isset($meta['content']))  ? ''  : $meta['content'];
      $newline  = (!isset($meta['newline']))  ? "\n"  : $meta['newline'];
      $str .= '<meta '.$type.'="'.$name.'" content="'.$content.'" />'.$newline;
    }
    return $str;
  }
}

// ------------------------------------------------------------------------

/**
 * Generates non-breaking space entities based on number supplied
 *
 * Usage:
 * {{{
 *    echo nbs(5);
 * }}}
 *
 * @param integer
 * @return string  
 */
if (!function_exists('nbs'))
{
  function nbs($num = 1)
  {
    return str_repeat("&nbsp;", $num);
  }
}   

// ------------------------------------------------------------------------ 

/**
 * Returns a Powered By Link to Simplex.
 *
 * Usage:
 * {{{
 *    echo poweredBy();
 * }}}
 *
 * @param bool $about Include a short one sentence description of Simplex
 * @return void
 **/
if(!function_exists('poweredBy')) 
{  
  function poweredBy($about = true)
  {
    ob_start();
    ?>
<p class="powered_by">Powered By <a href="http://www.simplex.designbreakdown.com">Simplex</a> 
  <?php if($about == true): ?>, a Joomla! template framework<?php endif;?>
</p>    
    <?php 
    echo ob_get_clean();
  }
}