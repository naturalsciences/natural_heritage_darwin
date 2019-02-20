<?php

/*
 * This file is part of the DaRWIN package.
 * (c) Paul-André Duchesne <paul-andre.duchesne@naturalsciences.be>
 * (c) Brice Maron <brice.maron@naturalsciences.be>
 * (c) Yann Chambert <yann.chambert@naturalsciences.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * widgetFormSelectDoubleListFilterable a choice widget for a model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     DaRWIN Team <darwin-ict@naturalsciences.be>
 */
class widgetFormSelectDoubleListFilterable extends sfWidgetForm
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addRequiredOption('choices');

    $this->addOption('class', 'double_list');
    $this->addOption('filter_class', 'double_list_filter_input');
    /* Flag for add option input box activation */
    $this->addOption('add_active', false);
    /* Class used for add option input stylin' */
    $this->addOption('add_class', 'double_list_add_input');
    /* Add action url */
    $this->addOption('add_url', '');
    $this->addOption('class_select', 'double_list_select');
    $this->addOption('label_unassociated', 'Unassociated');
    $this->addOption('label_associated', 'Associated');
    $this->addOption('unassociate', '<img src="/images/previous.png" alt="unassociate" />');
    $this->addOption('associate', '<img src="/images/next.png" alt="associate" />');

    $this->addOption('template', <<<EOF
<ul id=%error_id% class="error_list" style="display:none;">
  <li></li>
</ul>
<div class="%class%">
  <div style="float: left">
    <div class="double_list_filter_label">%label_unassociated%</div>
    <div class="double_list_filter">%filter%%filter_reset%</div>
    %unassociated%
  </div>
  <div style="float: left; margin-top: 3em">
    %unassociate%
    <br />
    %associate%
  </div>
  <div style="float: left">
    <div class="double_list_filter_label">%label_associated%</div>
    %associated%
  </div>
  <div class="double_list_filter_add">
    <div>%add_option%</div>
  </div>
  <br style="clear: both" />
  <script type="text/javascript">

    $(document).ready(function () {
      sfDoubleList.init(document.getElementById('%id%'), '%class_select%');

      // Generate one array for the list corresponding to what's in unassociated (displayed or not)
      var unassociated_array_%id% = array_from_options($('#unassociated_%id%'));

      // Bind to on change of unassociated select the update of unassociated_array_%id% array "selected" option
      $('#unassociated_%id%').bind('change',function()
        {
          var i;
          $(this).find('option').each(function()
            {
              for (i in unassociated_array_%id%)
              {
                if($(this).val() == unassociated_array_%id%[i]["value"])
                {
                  unassociated_array_%id%[i]["selected"] = $(this).attr('selected');
                }
              }
            });
          /*console.log(unassociated_array_%id%[0]);
          console.log(unassociated_array_%id%[1]);*/
        });

      // Bind the filter function to keyUp event of filter input
      $('#filter_%id%').bind('keyup', function(){jQuery('#unassociated_%id%').html(html_options_from_array(filter_array(unassociated_array_%id% , 
                                                                                                                        jQuery('#filter_%id%').val())
                                                                                                          )
                                                                                  );
                                                });

      // Bind the remove filter function to click event of filter clear image
      $('#filter_%id%_clear').bind('click', 
                                   function(){jQuery('#filter_%id%').val('');
                                              jQuery('#unassociated_%id%').html(html_options_from_array(filter_array(unassociated_array_%id%, 
                                                                                                                     jQuery('#filter_%id%').val()
                                                                                                                    )
                                                                                                       )
                                                                               );
                                             });

      // Bind the move from unassociated to associate link click
      // and do some work:
      // - Remove options selected from stored array
      // - Trigger the move action from sfDoubleList object
      $('#associate_%id%').bind('click',function(){
        jQuery("#unassociated_%id% option:selected'").each(function(){
          unassociated_array_%id% = remove_from_array(unassociated_array_%id%, 
                                                      jQuery(this).val(),
                                                      true
                                                     );
        });
        sfDoubleList.move('unassociated_%id%', '%id%');
        // Set titles of options
        set_options_title($('#unassociated_%id%'));
        set_options_title($('#%id%'));
        return false;
      });
      
      // Bind the move from associated to unassociate link click
      // and do some work:
      // - Add options selected to stored array
      // - Let sfDoubleList object do the move
      // - Resort the Array
      // - Produce a new option list with filter
      $('#unassociate_%id%').bind('click',function(){
        jQuery('#%id% option:selected').each(function(){
          unassociated_array_%id% = add_to_array(unassociated_array_%id%, 
                                                 list_options_from(jQuery(this))
                                                );
        });
        unassociated_array_%id% = sort_array(unassociated_array_%id%, false);
        sfDoubleList.move('%id%', 'unassociated_%id%');
        jQuery('#unassociated_%id%').html(html_options_from_array(filter_array(unassociated_array_%id% , 
                                                                               jQuery('#filter_%id%').val())
                                                                              )
                                                                 );
        // Set titles of options
        set_options_title($('#unassociated_%id%'));
        set_options_title($('#%id%'));
        return false;
      });
      
      // Click on add option link try to add an option in the corresponding table providing values to this widget
      $('a#add_%id%_link').click(function(){
        // Hide and empty errors list first
        $('#%error_id%').hide();
        $('#%error_id% li').text('');
        var input_value = $('#add_%id%').val().trim();
        // Trigger the insertion only if there is a value
        if(input_value.length)
        {
          // Create an array that will be used as data to be posted
          var addItem = {"value": input_value};
          $('#add_%id%_loader').show();
          $.ajax({
            type: "POST",
            url: $(this).attr('href'),
            data: $.param(addItem),
            success: function(html) 
            {
              // Test what is returned is well the id of the new record inserted
              if(!isNaN(html))
              {
                  // Create the new value to add as an array with 3 elements: value, text and selected
                  var list_of_options = new Array();
                  list_of_options["value"] = html;
                  list_of_options["text"] = $('#add_%id%').val();
                  list_of_options["selected"] = true;
                  // Recreate the array stored by adding the new array of 3 elements to it and by sorting it
                  unassociated_array_%id% = sort_array(add_to_array(unassociated_array_%id%, 
                                                                    list_of_options
                                                                   ),
                                                       false
                                                      );
                  // Then refilter it based on what's entered in filter and generate an html list of option
                  // Replace html of unassociated list by the generated one
                  $('#unassociated_%id%').html(html_options_from_array(filter_array(unassociated_array_%id% , $('#filter_%id%').val())));
                  // Than remove value from add input
                  $('#add_%id%').val('');
              }
              else
              {
                // If an error is encountered, display it
                // Usually, it is a duplicate error
                $('#%error_id%').find('li').text(html);
                $('#%error_id%').show();
              }
              $('#add_%id%_loader').hide();
            },
            error: function(xhr)
            {
              addError('Error!  Status = ' + xhr.status);
              $('#add_%id%_loader').hide();
            }
          });
        }
        return false;
      });
      
      // Set titles of options
      set_options_title($('#unassociated_%id%'));
      set_options_title($('#%id%'));
    });

  </script>
</div>
EOF
    );

  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if (is_null($value))
    {
      $value = array();
    }
    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    $associated = array();
    $unassociated = array();

    if (!is_array($value))
      $value = array($value);

    foreach ($choices as $key => $option)
    {
      if (in_array(strval($key), $value))
      {
        $associated[$key] = $option;
      }
      else
      {
        $unassociated[$key] = $option;
      }
    }

    $size = isset($attributes['size']) ? $attributes['size'] : (isset($this->attributes['size']) ? $this->attributes['size'] : 10);
    $id = $this->generateId($name);
    $error_id = 'error_'.$id;
    // The two select boxes
    $associatedWidget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $associated), array('size' => $size, 'class' => $this->getOption('class_select').'-selected'));
    $unassociatedWidget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $unassociated), array('size' => $size, 'class' => $this->getOption('class_select')));
    // The input box used to filter content of unassociated select box
    $filterWidget = new sfWidgetFormInputText(array(), array('class'=>$this->getOption('filter_class')));
    // Options to be passed to the renderTag of reset filter image
    $filterResetOptions = array('src' => '/images/remove.png',
                                'id' => 'filter_'.$id."_clear",
                                'class' => 'filter_clear',
                                'alt' => __('Reset filter'),
                                'title' => __('Reset filter')
                               );
    $addOptionHTML = '';
    // If add of a value in distant table is possible and activated
    if ($this->getOption('add_active'))
    {
      // Define the add value input box
      $addOptionWidget = new sfWidgetFormInputText(array(), array('id' => 'add_'.$id, 'class'=>$this->getOption('add_class')));
      $addOptionWidget->setLabel(__('Add new value:'));
      // Options to be passed to the renderTag of add option image
      $addImageOptions = array('src' => '/images/add_green.png',
                               'id' => 'add_'.$id.'_image',
                               'class' => 'add_option',
                               'alt' => __('Add value'),
                               'title' => __('Add value')
                              );
      // Options to be passed to the timer displayed while tryin' to insert a value
      $addTimerImageOptions = array('src' => '/images/loader.gif',
                                    'id' => 'add_'.$id.'_loader',
                                    'style' => 'display:none;'
                                   );
      $addLinkOptions = array('id'=> 'add_'.$id.'_link',
                              'class' => 'add_option',
                              'href' => url_for($this->getOption('add_url'))
                              );
      $addOptionHTML = $this->renderContentTag('label',$addOptionWidget->getLabel(), array('for'=>'add_'.$id)).$addOptionWidget->render('add_'.$name).$this->renderContentTag('a',$this->renderTag('img', $addImageOptions), $addLinkOptions).$this->renderTag('img', $addTimerImageOptions);
    }

    return strtr($this->getOption('template'), array(
      '%class%'              => $this->getOption('class'),
      '%class_select%'       => $this->getOption('class_select'),
      '%id%'                 => $id,
      '%label_associated%'   => $this->getOption('label_associated'),
      '%label_unassociated%' => $this->getOption('label_unassociated'),
      '%associate%'          => sprintf('<a href="#" id="associate_'.$id.'">%s</a>', $this->getOption('associate')),
      '%unassociate%'        => sprintf('<a href="#" id="unassociate_'.$id.'">%s</a>', $this->getOption('unassociate')),
      '%associated%'         => $associatedWidget->render($name),
      '%unassociated%'       => $unassociatedWidget->render('unassociated_'.$name),
      '%filter%'             => $filterWidget->render('filter_'.$name),
      '%filter_reset%'       => $this->renderTag('img', $filterResetOptions),
      '%add_option%'         => $addOptionHTML,
      '%error_id%'           => $error_id
    ));
  }
}
