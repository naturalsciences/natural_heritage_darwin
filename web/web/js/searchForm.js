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


(function($){
  $.pager = function(el, options){
    // To avoid scope issues, use 'base' instead of 'this'
    // to reference this class from internal events and functions.
    var base = this;
    
    // Access to jQuery and DOM versions of element
    base.$el = $(el);
    base.el = el;
    
    // Add a reverse reference to the DOM object
    if(base.$el.data("pager")) {
      return;
    }
    base.top_form = $(el).closest('form');
    base.$el.data("pager", base);
    
    base.submit_form = function submit_search_form(url)
    {
      $.ajax({
        type: "POST",
        url: url,
        data: base.top_form.serialize(),
        success: function(html) {
          base.top_form.find(base.options['result_content']).html(html);
          base.top_form.find(base.options['result_container']).slideDown();
        }
      });
      base.top_form.find(base.options['result_content']).html('<img src="/images/loader.gif" />');
    };
    
    base.change_nbr_per_page = function change_nbr_per_page(event)
    {
      event.preventDefault();
      base.submit_form(base.top_form.attr('action'));
    }

    base.change_page = function change_page(event)
    {
      event.preventDefault();
      base.submit_form($(this).attr("href"));

      base.top_form.attr('action', $(this).attr("href"))
    };

    base.change_sort = function change_sort(event)
    {
      event.preventDefault();
      base.submit_form($(this).attr("href"));
    };

    base.init = function(){
      base.options = $.extend({},$.pager.defaultOptions, options);
      base.top_form.find(base.options['fld_rec_per_page']).bind('change', base.change_nbr_per_page);
      base.top_form.find(base.options['pager_links']).bind('click', base.change_page);
      base.top_form.find(base.options['sort_links']).bind('click', base.change_sort);
    };
    base.init();
  };
  
  
  $.pager.defaultOptions = {
    fld_rec_per_page: '.rec_per_page',
    result_container: '.search_results',
    result_content: '.search_results_content',
    pager_links: 'a.sort',
    sort_links: '.pager a'
  };
  
  $.fn.pager = function(options){
    return this.each(function(){
      (new $.pager(this, options));
    });
  };

})(jQuery);

(function($) {
  $.results = function ( el , options ) {
    // To avoid scope issues, use 'base' instead of 'this'
    // to reference this class from internal events and functions.
    var base = this;

    // Access to jQuery and DOM versions of element
    base.$el = $(el);
    base.el = el;

    // Add a reverse reference to the DOM object
    if(base.$el.data("results")) {
      return;
    }
    base.top_form = $(el).closest('form');
    base.$el.data("results", base);

    base.init = function(){
      base.options = $.extend({},$.results.defaultOptions, options);
      base.top_form.off('click', base.options['clear_item_links']).on('click', base.options['clear_item_links'], base.clear_item);
    };

    base.submit_form = function submit_search_form(url)
    {
      $.ajax({
        type: "POST",
        url: url,
        data: base.top_form.serialize(),
        success: function(html) {
          base.top_form.find(base.options['result_content']).html(html);
          base.top_form.find(base.options['result_container']).slideDown();
        }
      });
      base.top_form.find(base.options['result_content']).html('<img src="/images/loader.gif" />');
    };

    base.clear_item = function clear_item(event)
    {
      event.preventDefault();
      if (confirm(base.options['confirmation_message'])) {
        $.ajax({
          url: $(this).attr('href'),
          success: function (html) {
            if (html == "ok") {
              base.submit_form(base.top_form.attr('action'));
            }
            else {
              base.top_form.find(base.options['error_message_container']).html(html);
            }
          }
        });
      }
    };

    base.init();

  }

  $.results.defaultOptions = {
    result_container: '.search_results',
    result_content: '.search_results_content',
    error_message_container: 'div#error_message',
    clear_item_links: 'a.clear_item',
    confirmation_message: 'Are you sure ?'
  };

  $.fn.results = function( options ) {
    return this.each( function() {
      ( new $.results( this , options ) );
    });
  };

})(jQuery);