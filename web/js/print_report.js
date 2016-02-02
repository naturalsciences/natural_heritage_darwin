/**
 * Created by Paul-André Duchesne on 15/09/2015.
 */
(function($) {
    $.print_report = function ( el , options ) {
        // To avoid scope issues, use 'print_base' instead of 'this'
        // to reference this class from internal events and functions.
        var print_base = this;

        // Access to jQuery and DOM versions of element
        print_base.$el = $(el);
        print_base.el = el;

        // Add a reverse reference to the DOM object
        if(print_base.$el.data("print_report")) {
            return;
        }
        print_base.top_form = $(el).closest('form');
        print_base.$el.data("print_report", print_base);

        print_base.init = function(){
            print_base.options = $.extend({},$.print_report.defaultOptions, options);
            print_base.top_form.off('click', print_base.options['print_button']).on('click', print_base.options['print_button'], print_base.print_item);
        };

        print_base.print_item = function print_item(event) {
            event.preventDefault();
            var last_position = $(window).scrollTop();
            scroll(0,0) ;
            $(this).qtip({
                id: 'modal',

                content: {
                    text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
                    title: { button: true, text: print_base.options['q_tip_text'] },
                    ajax: {
                        url: $(this).attr('href'),
                        type: 'POST',
                        data: {
                                "with_js" : 1
                              }
                    }
                },

                position: {
                    my: 'top center',
                    at: 'top center',
                    adjust:{
                        y: 250 // option set in case of the qtip become too big
                    },
                    target: $(document.body)
                },

                show: {
                    ready: true,
                    delay: 0,
                    event: event.type,
                    solo: true,
                    modal: {
                        on: true,
                        blur: false
                    }
                },

                hide: {
                    event: 'close_modal',
                    target: $('body')
                },

                events: {
                    hide: function(event, api) {
                        $('#submit_btn').die('click') ;
                        scroll(0,last_position) ;
                        api.destroy();
                    }
                },

                style: 'ui-tooltip-light ui-tooltip-rounded qtiped_report_form'
            }, event );
            return false;
        };

        print_base.init();

    }

    $.print_report.defaultOptions = {
        print_button: 'a.print_item',
        q_tip_text: 'Please fill in the criterias to print your report'
    };

    $.fn.print_report = function( options ) {
        return this.each( function() {
            ( new $.print_report( this , options ) );
        });
    };

})(jQuery);