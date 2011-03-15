<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

// ------------------------------------------------------------------------

/**
 * Simplex Form Helpers. Functions for working with forms 
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
 * Form Declaration.
 *
 * @note Creates the opening portion of the form.
 *
 * @param string $action      The URI segments of the form destination
 * @param array  $attributes  A key/value pair of attributes
 * @param array  $hidden      A key/value pair hidden data
 */       
if(!function_exists('formOpen'))
{
  function formOpen($action = '', $attributes = '', $hidden = array())
  {
    $splex = getSplexInstance();

    if ($attributes == '') {
      $attributes = 'method="post"';
    }

    $action  = $splex->joomla->site_uri . 'index.php/' . $action;
    $form    = '<form action="'.$action.'"';
    $form   .= _attributesToString($attributes, true);
    $form   .= '>';

    if (is_array($hidden) AND count($hidden) > 0) {
      $form .= formHidden($hidden);
    }
    return $form;
  }   
}

// ------------------------------------------------------------------------

/**
 * Form Declaration - Multipart type
 *
 * Creates the opening portion of the form, but with "multipart/form-data".
 *
 * @param string $action      The URI segments of the form destination
 * @param array  $attributes  A key/value pair of attributes
 * @param array  $hidden      A key/value pair hidden data
 * @return string    
 */
if (!function_exists('formOpenMultipart'))
{
  function formOpenMultipart($action, $attributes = array(), $hidden = array())
  {
    $attributes['enctype'] = 'multipart/form-data';
    return formOpen($action, $attributes, $hidden);
  }
}

// ------------------------------------------------------------------------

/**
 * Hidden Input Field
 *
 * Generates hidden fields.  You can pass a simple key/value string or an associative
 * array with multiple values.
 *
 * @param mixed  $name       Name Attribute of the form
 * @param string $value      Value Attribute of the form
 * @param bool   $recursing  
 * @return string    
 */
if (!function_exists('formHidden'))
{
  function formHidden($name, $value = '', $recursing = false)
  {
    static $form;

    if ($recursing === false) {
      $form = "\n";
    }

    if (is_array($name))
    {
      foreach ($name as $key => $val) {
        formHidden($key, $val, true);
      }
      return $form;
    }

    if (!is_array($value)) {
      $form .= '<input type="hidden" name="'.$name.'" value="'.formPrep($value, $name).'" />'."\n";
    }
    else
    {
      foreach ($value as $k => $v) {
        $k = (is_int($k)) ? '' : $k; 
        formHidden($name.'['.$k.']', $v, true);
      }
    }
    return $form;
  }
}

// ------------------------------------------------------------------------

/**
 * Text Input Field
 *
 * @param mixed  $data   Name of Field.
 * @param string $value  Value Attribute.
 * @param string $extra  Extra Attributes in the form of a html string.
 * @return string 
 */
if (!function_exists('formInput'))
{
  function formInput($data = '', $value = '', $extra = '')
  {
    $defaults = array('type' => 'text', 'name' => ((!is_array($data)) ? $data : ''), 'value' => $value);
    return "<input "._parseFormAttributes($data, $defaults).$extra." />";
  }
}

// ------------------------------------------------------------------------

/**
 * Password Field
 *
 * @note Identical to the input function but adds the "password" type
 *
 * @param mixed  $data   Name of Field.
 * @param string $value  Value Attribute.
 * @param string $extra  Extra Attributes in the form of a html string.
 * @return string      
 */
if (!function_exists('formPassword'))
{
  function formPassword($data = '', $value = '', $extra = '')
  {
    if (!is_array($data)) {
      $data = array('name' => $data);
    }
    $data['type'] = 'password';
    return formInput($data, $value, $extra);
  }
}

// ------------------------------------------------------------------------

/**
 * Upload Field
 *
 * @note Identical to the input function but adds the "file" type
 *
 * @param mixed  $data   Name of Field.
 * @param string $value  Value Attribute.
 * @param string $extra  Extra Attributes in the form of a html string.
 * @return string   
 */
if (!function_exists('formUpload'))
{
  function formUpload($data = '', $value = '', $extra = '')
  {
    if (!is_array($data)) {
      $data = array('name' => $data);
    }

    $data['type'] = 'file';
    return formInput($data, $value, $extra);
  }
}

// ------------------------------------------------------------------------

/**
 * Textarea field
 *
 * @param mixed  $data   Name of Field.
 * @param string $value  Value Attribute.
 * @param string $extra  Extra Attributes in the form of a html string.
 * @return string    
 */
if (!function_exists('formTextarea'))
{
  function formTextarea($data = '', $value = '', $extra = '')
  {
    $defaults = array('name' => ((!is_array($data)) ? $data : ''), 'cols' => '90', 'rows' => '12');

    if (!is_array($data) OR!isset($data['value'])) {
      $val = $value;
    }
    else {
      $val = $data['value']; 
      unset($data['value']); // textareas don't use the value attribute
    }
    
    $name = (is_array($data)) ? $data['name'] : $data;
    return "<textarea "._parseFormAttributes($data, $defaults).$extra.">".formPrep($val, $name)."</textarea>";
  }
}

// ------------------------------------------------------------------------

/**
 * Multi-select menu
 *
 * @param string $name      Name Attribute
 * @param array  $options   Array of Options to pass to formDropdown function
 * @param mixed  $selected  The Default Selected Item
 * @param string $extra     Extra Attributes in the form of a html string.
 * @return string    
 */
if (!function_exists('formMultiselect'))
{
  function formMultiselec($name = '', $options = array(), $selected = array(), $extra = '')
  {
    if (!strpos($extra, 'multiple')) {
      $extra .= ' multiple="multiple"';
    }
    
    return formDropdown($name, $options, $selected, $extra);
  }
}

// --------------------------------------------------------------------

/**
 * Drop-down Menu
 *
 * @param string $name      Name Attribute
 * @param array  $options   Array of Options
 * @param mixed  $selected  The Default Selected Item
 * @param string $extra     Extra Attributes in the form of a html string 
 * @return string    
 */
if (!function_exists('formDropdown'))
{
  function formDropdown($name = '', $options = array(), $selected = array(), $extra = '')
  {
    if (!is_array($selected)) {
      $selected = array($selected);
    }

    if ($extra != '') $extra = ' '.$extra;

    $multiple = (count($selected) > 1 && strpos($extra, 'multiple') === false) ? ' multiple="multiple"' : '';
    $form     = '<select name="'.$name.'"'.$extra.$multiple.">\n";

    foreach ($options as $key => $val)
    {
      $key = (string) $key;

      if (is_array($val))
      {
        $form .= '<optgroup label="'.$key.'">'."\n";

        foreach ($val as $optgroup_key => $optgroup_val) {
          $sel   = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';
          $form .= '<option value="'.$optgroup_key.'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
        }

        $form .= '</optgroup>'."\n";
      }
      else {
        $sel   = (in_array($key, $selected)) ? ' selected="selected"' : '';
        $form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
      }
    }
    $form .= '</select>';
    return $form;
  }
}

// ------------------------------------------------------------------------

/**
 * Checkbox Field
 *
 * @param string $data      Name Attribute
 * @param array  $value     Value Attribute
 * @param mixed  $checked   The Default Selected Item
 * @param string $extra     Extra Attributes in the form of a html string
 * @return string   
 */
if (!function_exists('formCheckbox'))
{
  function formCheckbox($data = '', $value = '', $checked = false, $extra = '')
  {
    $defaults = array('type' => 'checkbox', 'name' => ((!is_array($data)) ? $data : ''), 'value' => $value);

    if (is_array($data) AND array_key_exists('checked', $data))
    {
      $checked = $data['checked'];

      if ($checked == false) {
        unset($data['checked']);
      }
      else {
        $data['checked'] = 'checked';
      }
    }

    if ($checked == true) {
      $defaults['checked'] = 'checked';
    }
    else {
      unset($defaults['checked']);
    }
    return "<input "._parseFormAttributes($data, $defaults).$extra." />";
  }
}

// ------------------------------------------------------------------------

/**
 * Radio Button
 *
 * @param string $data      Name Attribute
 * @param array  $value     Value Attribute
 * @param mixed  $checked   The Default Selected Item
 * @param string $extra     Extra Attributes in the form of a html string
 * @return string  
 */
if (!function_exists('formRadio'))
{
  function formRadio($data = '', $value = '', $checked = false, $extra = '')
  {
    if (!is_array($data)) { 
      $data = array('name' => $data);
    }
    $data['type'] = 'radio';
    return formCheckbox($data, $value, $checked, $extra);
  }
}

// ------------------------------------------------------------------------

/**
 * Submit Button
 *
 * @param string $data      Name Attribute
 * @param array  $value     Value Attribute
 * @param string $extra     Extra Attributes in the form of a html string
 * @return string  
 */
if (!function_exists('formSubmit'))
{ 
  function formSubmit($data = '', $value = '', $extra = '')
  {
    $defaults = array('type' => 'submit', 'name' => ((!is_array($data)) ? $data : ''), 'value' => $value);
    return "<input "._parseFormAttributes($data, $defaults).$extra." />";
  }
}

// ------------------------------------------------------------------------

/**
 * Reset Button
 *
 * @param mixed  $data      Name Attribute
 * @param array  $value     Value Attribute
 * @param mixed  $checked   The Default Selected Item
 * @param string $extra     Extra Attributes in the form of a html string
 * @return string   
 */
if (!function_exists('formReset'))
{
  function formReset($data = '', $value = '', $extra = '')
  {
    $defaults = array('type' => 'reset', 'name' => ((!is_array($data)) ? $data : ''), 'value' => $value);
    return "<input "._parseFormAttributes($data, $defaults).$extra." />";
  }
}

// ------------------------------------------------------------------------

/**
 * Form Button
 *
 * @param mixed  $data     Name Attribute
 * @param array  $content  Content
 * @param string $extra    Extra Attributes in the form of a html string
 * @return string   
 */
if (!function_exists('formButton'))
{
  function formRutton($data = '', $content = '', $extra = '')
  {
    $defaults = array('name' => ((!is_array($data)) ? $data : ''), 'type' => 'button');

    if ( is_array($data) AND isset($data['content'])) {
      $content = $data['content'];
      unset($data['content']); // content is not an attribute
    }
    return "<button "._parseFormAttributes($data, $defaults).$extra.">".$content."</button>";
  }
}

// ------------------------------------------------------------------------

/**
 * Form Label Tag
 *
 * @param string  $label_text  The text to appear onscreen
 * @param string  $id          The id the label applies to
 * @param string  $attributes  Additional attributes
 * @return string 
 */
if (!function_exists('formLabel'))
{
  function formLabel($label_text = '', $id = '', $attributes = array())
  {

    $label = '<label';

    if ($id != '') {
       $label .= " for=\"$id\"";
    }

    if (is_array($attributes) AND count($attributes) > 0)
    {
      foreach ($attributes as $key => $val) {
        $label .= ' '.$key.'="'.$val.'"';
      }
    }

    $label .= ">$label_text</label>";
    return $label;
  }
}

// ------------------------------------------------------------------------
/**
 * Fieldset Tag.
 *
 * Used to produce <fieldset><legend>text</legend>.  To close fieldset
 * use form_fieldset_close()
 *
 * @param string $legend_text  The legend text
 * @param string $attributes   Additional attributes
 * @return string   
 */
if (!function_exists('formFieldset'))
{
  function formFieldset($legend_text = '', $attributes = array())
  {
    $fieldset  = "<fieldset";
    $fieldset .= _attributesToString($attributes, false);
    $fieldset .= ">\n";

    if ($legend_text != '') {
      $fieldset .= "<legend>$legend_text</legend>\n";
    }
    return $fieldset;
  }
}

// ------------------------------------------------------------------------

/**
 * Fieldset Close Tag
 *
 * @param string $extra Extra Attributes in the form of a html string
 * @return string  
 */
if (!function_exists('formFieldsetClose'))
{
  function formFieldsetClose($extra = '')
  {
    return "</fieldset>".$extra;
  }
}

// ------------------------------------------------------------------------

/**
 * Form Close Tag
 *
 * @param string $extra Extra Attributes in the form of a html string
 * @return string  
 */
if (!function_exists('formClose'))
{
  function formClose($extra = '')
  {
    return "</form>".$extra;
  }
}

// ------------------------------------------------------------------------

/**
 * Form Prep
 *
 * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
 * @todo replace with joomla APi stuff.
 *
 * @access  public
 * @param   string $str String to process and clean
 * @param   $field_name Name of field
 * @return  string 
 */
if (!function_exists('formPrep'))
{
  function formPrep($str = '', $field_name = '')
  {
    static $prepped_fields = array();
    
    // if the field name is an array we do this recursively
    if (is_array($str))
    {
      foreach ($str as $key => $val) {
        $str[$key] = formPrep($val);
      }
      return $str;
    }

    if($str === '') {
      return '';
    }

    // we've already prepped a field with this name
    // @todo need to figure out a way to namespace this so
    // that we know the *exact* field and not just one with
    // the same name
    if (isset($prepped_fields[$field_name])) {
      return $str;
    }
    
    $str = htmlspecialchars($str);

    // In case htmlspecialchars misses these.
    $str = str_replace(array("'", '"'), array("&#39;", "&quot;"), $str);

    if($field_name != ''){
      $prepped_fields[$field_name] = $str;
    }
    return $str;
  }
}

// ------------------------------------------------------------------------

/**
 * Parse the form attributes
 *
 * Helper function used by some of the form helpers
 *
 * @param array $attributes Attributes to parse
 * @param array $default    A Default Array.
 * @return string    
 */
if (!function_exists('_parseFormAttributes'))
{
  function _parseFormAttributes($attributes, $default)
  {
    if (is_array($attributes))
    {
      foreach ($default as $key => $val)
      {
        if (isset($attributes[$key])) {
          $default[$key] = $attributes[$key];
          unset($attributes[$key]);
        }
      }

      if (count($attributes) > 0) {
        $default = array_merge($default, $attributes);
      }
    }

    $att = '';
    
    foreach ($default as $key => $val)
    {
      if ($key == 'value') {
        $val = formPrep($val, $default['name']);
      }
      $att .= $key . '="' . $val . '" ';
    }
    return $att;
  }
}

// ------------------------------------------------------------------------

/**
 * Attributes To String
 *
 * Helper function used by some of the form helpers. 
 *
 * @param mixed
 * @param bool
 * @return string 
 */
if (!function_exists('_attributesToString'))
{
  function _attributesToString($attributes, $formtag = false)
  {
    if (is_string($attributes) AND strlen($attributes) > 0)
    {
      if ($formtag == true AND strpos($attributes, 'method=') === false) {
        $attributes .= ' method="post"';
      }
      return ' '.$attributes;
    }
  
    if (is_object($attributes) AND count($attributes) > 0) {
      $attributes = (array)$attributes;
    }

    if (is_array($attributes) AND count($attributes) > 0)
    {
      $atts = '';

      if (!isset($attributes['method']) AND $formtag === true) {
        $atts .= ' method="post"';
      }

      foreach ($attributes as $key => $val) {
        $atts .= ' '.$key.'="'.$val.'"';
      }
      return $atts;
    }
  }
}