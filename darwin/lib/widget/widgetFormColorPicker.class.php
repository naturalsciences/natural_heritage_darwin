<?php
/**
 * widgetFormColorPicker: generates a Jquery Colorpicker widget.
 *
 * @package    symfony
 * @subpackage form
 * @author     DaRWIN2 team <darwin-ict@naturalsciences.be>
 */

class widgetFormColorPicker extends sfWidgetFormInput
{
  /**
   * Gets the Stylesheets paths associated with the widget.
   *
   * @return array An array of Stylesheets paths
   */
  public function getStylesheets()
  {
    return array('/css/farbtastic.css' => 'screen');
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavascripts()
  {
    return array('/js/farbtastic.js');
  }

 public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $html = parent::render($name, $value, $attributes, $errors);
    $html .= "&nbsp;<span class='round_color'>&nbsp;</span>&nbsp;" ;
    $html .= image_tag('ColorPickerUIBtnWheel.png',array('class' => 'color_pckr')) ;
    $html .= "
    <script type=\"text/javascript\">
      $(document).ready(function () { 
        $('.round_color').css('backgroundColor','".$value."');      
        $('.color_pckr').qtip({
          show: { delay: 0, event: 'click'},
          hide: { event: 'click' },
          style: {  
            name: 'light',
            title: { padding: '3px'},
            width: { min: '215px', max: '215px'}
          },         
          content: {
            title: { button: true, text: '&nbsp;' },
            text: '<div id=\"colorpicker\"></div>',
          },
          events: {
            show: function () {
              $('#colorpicker').farbtastic(function(color){
                $('#" . $this->generateId($name) . "').val(color);
                $('.round_color').css('backgroundColor', color);    
              });
            }
          } 
       });       
       $('#" . $this->generateId($name) . "').keyup(function(){
         $.farbtastic('#colorpicker').setColor($(this).val());
         $('.round_color').css('backgroundColor', color);        
       });
       $('#" . $this->generateId($name) . "').attr('value', '" . (($value) ? $value : '#ededee') . "');
     });
   </script>
    ";

    return $html;
  }
}
