$$(document).ready(function() {
  $$('.module .mod-title').editable('?tmpl=save&model=moduletitle', {
    indicator : 'Saving...',
    tooltip   : 'Click to edit...',
    event     : 'edit',   
    submitdata : function(value, settings) { 
      var rethash = {};
      var id = $$(this).parent().attr('id');  
      id = id.split("module-").join("");     
      rethash['moduleID'] = id;
      return rethash;
    }    
  });
});