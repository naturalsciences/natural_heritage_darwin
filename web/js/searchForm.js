(function($){
  $.choose_form = function(el, options){
    // To avoid scope issues, use 'base' instead of 'this'
    // to reference this class from internal events and functions.
    var base = this;
    
    // Access to jQuery and DOM versions of element
    base.$el = $(el);
    base.el = el;
    
    // Add a reverse reference to the DOM object
    base.$el.data("choose_form", base);
    
    base.search_form_submit = function search_form_submit(event)
    {
      event.preventDefault();
      base.$el.find(".tree").slideUp().html("");
        $.ajax({
          type: "POST",
          url: $(this).attr('action'),
          data: $(this).serialize(),
          success: function(html){
            base.$el.find(base.options['content_elem']).html(html);
            base.$el.find(base.options['result_container']).slideDown();
          }
        });
      base.$el.find(base.options['content_elem']).html('<img src="/images/loader.gif" />');
      return false;
    }
    
    
    
    base.init = function(){
      base.options = $.extend({},$.choose_form.defaultOptions, options);
      base.$el.find(base.options['form_elem']).bind('submit.sform',base.search_form_submit);
    };
    
    base.init();
  };
  


    
  $.choose_form.defaultOptions = {
    content_elem: '.search_results_content',
    form_elem: '.search_form',
    result_container: '.search_results'
  };
  
  $.fn.choose_form = function(options){
    return this.each(function(){
      (new $.choose_form(this, options));
    });
  };

})(jQuery);



