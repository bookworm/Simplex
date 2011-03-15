$$(document).ready(function() {   
  var mode = null;
  
  $$('#modes-list li').hover(function() {
    $$('#mode-label').show().css('top',  '-20px');
    var label = $$(this).children().text();
    $$('#mode-label').children('.label').text(label);    
    var offset = $$(this).position();     
    $$('#mode-label').stop().animate({ top: offset.top + 40 }, 300);
  }, function() {
    $$('#mode-label').hide().css('top',  '-20px');
  });
  
  $$('.mode-select').click(function() {        
    $$('.mode-select').removeClass('active');
    $$(this).addClass('active');
    $$('#modes-list-wrap').animate({ left: '-100px'}, 1000);
    mode = $$(this).parent().attr('id');
    $$('.mode-wrap').hide();  
    $$('.mode-wrap.active').removeClass('active'); 
    $$('.mode-wrap' + '.' + mode).fadeIn().addClass('active');      
    
    $$('#going-back').show(); 
    
    if(mode == 'edit-mode')
    {
      $$('.module .mod-title').bind({
        click: function() {
          $$(this).trigger('edit');
        }
      });
    } 
    else {
      
    }  
    
    if(mode == 'layout-mode') 
    {  
      $$('.STRUCT .layout').sortable({ 
        items: 'div.column',
        stop: function(event, ui) {
          var order = $$(this).sortable('toArray');  
          var layoutID = $$(this).attr('id');
          $$.post("?tmpl=save&model=editLayout", { order: order, layoutID: layoutID }, function(data) {       
          });
        }
      });
    }    
    
    if(mode == 'style-mode')
    {
      $$('#element-selector').selectmenu(); 
    }   
    
    return false;
  });
  
  $$('.open-mode-list').click(function() {
    $$('#modes-list').animate({ left: '100px'}, 1000);    
    return false;    
  });  
  
  $$('#going-back').click(function() {  
    mode = null; 
    $$(this).hide();
    $$('.mode-wrap.active').fadeOut().removeClass('active');     
    $$('#modes-list-wrap').animate({ left: '0px'}, 1000);     
    return false;
  });
  
  $$('.syle-mode-select').click(function() { 
    var styleMode = $$(this).parent().attr('id');   
    $$('.a-style-mode').hide().removeClass('active');
    $$('#style-' + styleMode).fadeIn().addClass('active');  
    $$('.syle-mode-select').removeClass('active');
    $$(this).addClass('active');    
    $$('#style-mode-selects li').removeClass('active');
    $$(this).parent().addClass('active'); 
    return false;   
  });
  
});