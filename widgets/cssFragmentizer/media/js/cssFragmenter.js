var selectMode = false;    
var $$ = jQuery;  

parseCSS = function(css) {
    var rules = [];
    var blocks = css.split('}');
    blocks.pop();
    var len = blocks.length;
    for (var i = 0; i < len; i++)
    {
        var pair = blocks[i].split('{'); 
        var selector = $$.trim(pair[0]);  
        var obj = { selector: selector, rules: parseCSSBlock(pair[1])};
        rules[i] = obj;
    }
    return rules;
} 

parseCSSBlock = function(css) { 
    var rule = {};
    var declarations = css.split(';');
    declarations.pop();
    var len = declarations.length;
    for (var i = 0; i < len; i++)
    {
        var loc = declarations[i].indexOf(':');
        var property = $$.trim(declarations[i].substring(0, loc));
        var value = $$.trim(declarations[i].substring(loc + 1));

        if (property != "" && value != "")
            rule[property] = value;
    }
    return rule;
}

function bind_element(option)
{
  element_selector = option.attr('selector'); 
  $$(element_selector).hover(function() {
    if (selectMode == true)
    {
      $$(this).stop().glow("yellow", 1000);
    }    
  });
  
  $$(element_selector).click(function() {
    if (selectMode == true) {  
      var selector = option.attr('selector'); 
      var cssFragmentString = editor.getCode() + '\n' + selector + ' {' + '}'; 
      $$('#element-selector').val(option.val()).selectmenu('destroy').selectmenu();
      $$('#param-css-selector').val(selector);   
      var search = editor.getCode();
      if(search.search(selector) == -1) {
        editor.setCode(cssFragmentString);
      }
    }
  });  
}   

$$(document).ready(function() {
  $$('#select-it').click(function() {
    if(selectMode == false) {
      selectMode = true;
    } else {
      selectMode = false;
    }
    if($$(this).hasClass('active')) {
      $$(this).removeClass('active');
    }
    else {
      $$(this).addClass('active');
    }   
  });    
  
  $$('select#element-selector option:not(#element-selector-blank)').each(function(){
    bind_element($$(this));
  }); 
  
  $$('#element-selector').change(function() {    
    var selector = $$('#element-selector option:selected').attr('selector');  
    $$('#param-css-selector').val(selector);
    $$(selector).effect("highlight", {}, 3000);  
    
    var cssFragmentString = editor.getCode() + '\n' + selector + ' {' + '}';
    var search = editor.getCode(); 
    if(search.search(selector) == -1) {
      editor.setCode(cssFragmentString);
    }
  }); 
  
  $$('#save-css').click(function() { 
    $(this).addClass('saving');   
    $$(this).children('.text').text('Saving CSS');
    $$.post("?tmpl=save&model=cssfragment", { cssFragment: editor.getCode }, function(data) {       
      $$('#save-css').children('.text').text('Saved CSS');      
      setTimeout( "$$('#save-css').children('.text').text('Save CSS');", 1000 );
      $$('#save-css').removeClass('saving');
    });             
    return false;
  });
      
}); 