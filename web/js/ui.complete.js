
  $.widget( "custom.catcomplete", $.ui.autocomplete, {
    _init: function() {
      var that = this;

      this.element.data('autocomplete', this.element.data('catcomplete'));
      this.options.label_element = $(this.element);
      this.options.value_element = $(this.element).parent().find('input:hidden');

      that.options.value_element.change(function(e) {
        //that.loadExistingValue();
      });

      that.options.label_element.hover(function(e) {
        $(this).attr('title',$(this).val());
      });

      this.options.select = function( event, ui ) {
        that.options.label_element.removeClass('value_disabled');
        that.options.value_element.val( ui.item.value ).trigger('change');
        return false;
      };

      this.options.search = function (event, ui) {
        if(ui.item) { // Click on elem
        } else {
          var fieldLevelId = '';
          if(that.options.data && that.options.data.field_level_id && $.isFunction(that.options.data.field_level_id)) {
            fieldLevelId = that.options.data.field_level_id();
          }
          $.ajax({
            url: that.options.source,
            data: {term : that.options.label_element.val(), 
                   field_level_id: fieldLevelId,
                  },
            dataType: 'json',
            success: function(data) {
              that.options.label_element.data('autocomplete')._response(data);
            }
          });
        }
        return false;
      }

      this.options.change = function( event, ui ) {
        // Clean some targeted fields
        if(that.options.data !== undefined) {
          if (that.options.data['field_to_clean_class'] !== '' && that.options.data['field_to_clean_class'] !== undefined) {
            if ($("." + that.options.data['field_to_clean_class']).length) {
              $("." + that.options.data['field_to_clean_class']).val('');
            }
          }
        }
        if(ui.item) {
          // Click on elem and loose focus
        }
        else {
          $.ajax({
            url: that.options.source,
            data: {term : that.options.label_element.val(), exact: 1 },
            dataType: 'json',
            success: function(data) {
              removeValue = function() {
                that.options.label_element.val('').removeClass('value_disabled');
                that.options.value_element.val('');
              };

              removeValue(that);
              if(data.length == 0) {
                removeValue();
              } else {
                that.options.value_element.val(data[0].value);
                that.options.label_element.val(data[0].label);
              }
            }
          });
        }
        return false;
      }

      this.options.focus = function( event, ui ) {
        that.options.label_element.val( ui.item.label );
        return false;
      };
      //this.loadExistingValue();

      var btn = $(this.element).parent().find('.btn');
      btn.on('click', function() {
        $(this).blur();
        // pass empty string as value to search for, displaying all results
        that.options.label_element.data('autocomplete')._search('');
        that.options.label_element.focus();
      });
    },
/*    loadExistingValue: function() {
      var that = this;
      val = that.options.value_element.val();
      if(val) {
        //FEtch
        $.ajax({
          url: that.options.source,
          data: {id : val},
          dataType: 'json',
          success: function(data) {
            that.options.label_element.val(data.label);
            if (typeof data.is_active !== 'undefined' && ! data.is_active) {
                that.options.label_element.addClass('value_disabled');
            }
          }
        });

      }
    },*/
    options: {
      value_element: undefined,
      label_element: undefined,
    },
  });
