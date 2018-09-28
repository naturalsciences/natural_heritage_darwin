<div class="panel">
  <?php echo form_tag('savesearch/saveSearch'.($form->getObject()->isNew() ? '' : '?id='.$form->getObject()->getId()), array('class'=>'search_form','id'=>'save_search'));?>
  <?php echo $form->renderHiddenFields(); ?>
  <?php echo $form->renderGlobalErrors(); ?>  
  <table class="form_table">
    <tbody>
    <tr>
      <td><?php echo $form['name']->renderLabel() ; ?></td>
      <td></td>
    </tr>
    <tr>
      <td align="left">
        <?php echo $form['name'] ; ?>
      </td>
      <td>
        <?php echo $form['favorite'] ; ?>
        <?php echo image_tag('favorite_on.png', array('id'=> 'favorite_on', 'alt' => __('Set a bookmark'))) ; ?>
        <?php echo image_tag('favorite_off.png', array('id'=> 'favorite_off', 'alt' => __('Set a bookmark'))) ; ?>
      </td>
    </tr>
    <tr>
      <td><label><?php echo __('Last modification'); ?></label></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="2">
        <input class="medium_size" disabled="disabled" type="text" value="<?php echo __($form->getObject()->isNew()?'Not Saved Yet':$form->getObject()->getModificationDateTime());?>">
      </td>
    </tr>
    <tr>
      <td><label><?php echo __('Type of search');?></label></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="2">
        <input class="medium_size" disabled="disabled" type="text" value="<?php echo __($form->getObject()->getSubject());?>">
      </td>
    </tr>
    <?php if($is_spec_search):?>
    <tr>
      <td colspan="2"><label><?php echo __("Number of items ") ; ?></label></td>
    </tr>
    <tr>
      <td colspan="2">
        <input class="medium_size" disabled="disabled" type="text" value="<?php echo $form->getObject()->getNumberOfIds();?>">
      </td>
    </tr>
    <?php endif;?>
    </tbody>
  </table>
  <br />
  <h2 style="display:none"><?php echo __("Visibility of fields in results :") ; ?></h2>
    <?php echo $form['visible_fields_in_result']->renderError(); ?>
  <table class="fields" style="display:none">
    <thead>
      <tr>
        <th><?php echo __('Fields') ; ?></th>
        <th><?php echo __('Visible ?') ; ?></th>
      </tr>
    </thead>
    <?php echo $form['visible_fields_in_result'] ; ?>
  </table>
  <div class="aligned_fields"> 
    <?php echo $form['is_only_id']->render(array('class'=>'hidden')) ; ?>
    <?php echo $form['subject']->render() ; ?>
    <input type="submit" name="save" id="save" value="<?php echo __('Save'); ?>" class="search_submit">
  </div>      
  </form>

<script  type="text/javascript">
spec_list_saved = null;
$(document).ready(function () {
  if($('#my_saved_searches_favorite').is(':checked'))
  {
    $('#favorite_on').attr('class','show') ;
    $('#favorite_off').attr('class','hidden') ;      
  }
  else
  {
    $('#favorite_on').attr('class','hidden') ;  
    $('#favorite_off').attr('class','show') ;      
  }

  $('#favorite_on').click(function(){
    $('#favorite_off').attr('class','show') ; 
    $(this).attr('class','hidden') ;
    $('#my_saved_searches_favorite').removeAttr('checked');
  });

  $('#favorite_off').click(function(){
    $('#favorite_on').attr('class','show') ; 
    $(this).attr('class','hidden') ;
    $('#my_saved_searches_favorite').attr('checked','checked');
  });

   $('form#save_search').submit(function () {
      $.ajax({
          type: "POST",
          url: $(this).attr('action'),
          data: $(this).serialize(),
          success: function(html){
            re = new RegExp("^ok");  
            if(re.test(html))
            {
              id_arr = html.split(',');
              spec_list_saved = id_arr[1];
              $('#save_search').attr('value','<?php echo __('Search Saved');?>') ;
              $('body').trigger('close_modal');
              return;
            }
            $('form#save_search').parent().before(html).remove();
          }
      });
      return false;
    });
});
</script>

</div>

