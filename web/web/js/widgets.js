(function($){
  $.widgets_screen = function(el, options){
    // To avoid scope issues, use 'base' instead of 'this'
    // to reference this class from internal events and functions.
    var base = this;

    // Access to jQuery and DOM versions of element
    base.$el = $(el);
    base.el = el;

    // Add a reverse reference to the DOM object
    base.$el.data("widgets_screen", base);

    base.init = function(){
      base.options = $.extend({},$.widgets_screen.defaultOptions, options);

      $('.board_col').sortable({
        helper: 'clone',
        connectWith: ['.board_col'],
        handle: '.widget_top_bar span',
        update: base.changeOrder,
        create: base.onInitialize
      });

      $('.widget_top_bar').live('dblclick', base.toggleWidget);
      $('.widget_refresh').live('click', base.refreshWidget);
      $('.widget_close').live('click', base.closeWidget);
      $('.widget_collection_button a').click(base.showWidgetCollection);
      $('.widget_collection_container a').click(base.addWidget);
      $('.widget_top_button').live('click', base.showWidgetContent);
      $('.widget_bottom_button').live('click',base.hideWidgetContent);
    };

    base.toggleWidget = function()
    {
      $(this).find('.widget_bottom_button:visible, .widget_top_button:visible').click();
    };

    base.showWidgetContent = function()
    {
      elem = $(this).closest('.widget');
      base.changeStatus(elem.attr('id'), 'open');
      elem.find('.widget_content').slideDown();
      elem.find('.widget_bottom_button').show();
      elem.find('.widget_top_button').hide();
    };

    base.hideWidgetContent = function()
    {
      elem = $(this).closest('.widget');
      base.changeStatus(elem.attr('id'), 'close');
      elem.find('.widget_content').slideUp();
      elem.find('.widget_bottom_button').hide();
      elem.find('.widget_top_button').show();
    };

    var last_notified = {};

    base.onInitialize = function (event, ui)
    {
      $('.widget script').remove();
    }

    base.changeOrder = function ()
    {
      var notification = {};
      $('.board_col').each(function(index,elem)
      {
        notification['col'+(index+1)] = String($(elem).sortable('toArray'));
      });

      if(! objectsAreSame(notification,last_notified))
        $.post(base.options['change_order_url'], notification );
      last_notified = notification;
    };

    base.refreshWidget = function(event, element){
      if(element == null)
        element = $(this);
      widget = element.closest('.widget');
      hideForRefresh(widget.find('.widget_content'));
      /** Replaced by an async query to avoid bug
      widget.find('.widget_content').load(base.options['reload_url'] + '/widget/' + widget.attr('id') );
      **/
      $.ajax({
        url: base.options['reload_url'] + '/widget/' + widget.attr('id'),
        async: false, //
        success: function(data) {
          widget.find('.widget_content').html(data);
        }
      });

      return false;
    };

    base.closeWidget = function(event){
        event.preventDefault();
        widget = $(this).closest('.widget');

        base.changeStatus(widget.attr('id'), 'hidden');
        $('.widget_collection_container .no_more').addClass('hidden');
        $('#boardprev_'+widget.attr('id')).removeClass('hidden');
        widget.remove();
        if($('.board_col li').length == 0)
          $('.no_more_wigets').removeClass('hidden');
        return false;
    };

    base.showWidgetCollection = function(event){
      event.preventDefault();
      if($('.widget_collection_container').is(":hidden"))
        {
            $('.widget_collection_container').slideDown();
            $('.widget_collection_button img').attr('src',base.options['collection_img_up']);
        }
        else
        {
            $('.widget_collection_container').slideUp();
            $('.widget_collection_button img').attr('src',base.options['collection_img_down']);
        }
    };

    base.flash = function (widget) {
      var $flash_el = $('#'+widget);
      var top_pos = $flash_el.position().top;
      scroll(0,top_pos - $('.widget_collection_button').position().top - 40);

      $flash_el.addClass('flash');
      setTimeout(function () {
        $flash_el.removeClass('flash');
        }, 1000);
    }

    base.addWidget = function(event){
      event.preventDefault();
      var widget_id = $(this).attr('alt');
      var to_open_id = widget_id;

      if($(this).parent('div').hasClass('hidden')){
        base.flash(to_open_id);
        return;
      }

      hideForRefresh('.widget_collection_container');
      var insertionDone = false;
      // Change Widget Status
      var add_url = $(this).attr('href');
      var positions = [];
      $('.board_col').each(function (i,el)
      {
        first_non_mandatory = $(el).find('li.widget a.widget_close:first');
        if(!first_non_mandatory.length)
          positions.push($(el).find('li.widget').length);
        else
        {
           widget_id = first_non_mandatory.closest('.widget').attr('id');
           positions.push( $(el).find('>li').index($('#'+widget_id)) );
        }
      });

      add_url += '/place/' + positions.join(',');
      $.get(add_url, function(html)
      {
        col = $(html).attr('col-ref');
        col -= 1;

        if( $('.board_col:eq(' + col + ') > li.widget a.widget_close').length == 0)
          $('.board_col:eq(' + col + ')').append(html);
        else
          $('.board_col:eq(' + col + ') > li:eq('+ positions[col] +')').before(html);
        showAfterRefresh('.widget_collection_container');
        attachHelpQtip($('#'+to_open_id));
        //base.flash(to_open_id);
      });

      $(this).parent().addClass('hidden');

      if($('.widget_collection_container .widget_preview:visible').length == 0)
          $('.widget_collection_container .no_more').removeClass('hidden');
      $('.no_more_wigets').addClass('hidden');
    };

    base.changeStatus = function (id,status)
    {
      $.post(base.options['change_status_url'] + '/widget/' + id + '/status/' + status );
    };

    base.init();
  };




  $.widgets_screen.defaultOptions = {
    change_order_url: '',
    change_status_url: '',
    reload_url: '',
    position_url: '',
    collection_img_up: '/images/widgets_expand_up_button.png',
    collection_img_down: '/images/widget_expand_button.png'
  };

  $.fn.widgets_screen = function(options){
    return this.each(function(){
      (new $.widgets_screen(this, options));
    });
  };

})(jQuery);
