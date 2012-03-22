<?php include_javascripts_for_form($form) ?>
<div id="keyword_screen">
  <?php echo form_tag('catalogue/keyword?table='.$sf_request->getParameter('table').'&id='.$sf_request->getParameter('id'), array('class'=>'edition qtiped_form') );?>
  
  <p id="unit_original_name">
    <label id="original_name_label"><?php echo __('Unit Name :');?></label>
    <input id="original_name" type="text" class="large_size" value="<?php echo isset($ref_object['name']) ?$ref_object['name'] :$ref_object['title']; ?>" />
  </p>
  <table id="keyword_table">
    <thead>
      <tr>
        <th class="top_aligned">
          <?php echo $form->renderGlobalErrors() ?>
          <label><?php echo __('Keyword');?><label>
        </th>
        <th class="top_aligned"><label><?php echo __('Value');?><label></th>
        <th></th>
      </tr>
    </thead>
    <tbody id="keyword_table">
      <?php foreach($form['ClassificationKeywords'] as $form_value):?>
        <?php include_partial('nameValue', array('form' => $form_value));?>
      <?php endforeach;?>
      <?php foreach($form['newKeywords'] as $form_value):?>
        <?php include_partial('nameValue', array('form' => $form_value));?>
      <?php endforeach; ?>
    </tbody>
  </table>

<h3><?php echo __('Possible Keywords');?></h3>
<ul class="name_tags">
  <?php foreach(ClassificationKeywords::getTags($sf_request->getParameter('table'),$sf_request->getParameter('kingdom')) as $key => $name):?>
    <?php $keyword = ClassificationKeywords::getTagsTitleAndColor($key) ; ?>
    <li <?php echo ('class ='.$keyword['class'].' id='.$key) ?>><?php echo link_to( __($name),'catalogue/addKeyword?key='.$key,array('title' => __($keyword['title']))); ?></li>
  <?php endforeach;?>
</ul>

  <table class="bottom_actions">
    <tfoot>
      <tr>
        <td>
          <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>

<script  type="text/javascript">
  $(document).ready(function () {
    $('.name_tags li a').click(function(event)
    {
      id = 'li#'+$(this).parent().attr('id') ;

      var tag_value = returnText($('#original_name'));    
      hideForRefresh('#keyword_screen');
      event.preventDefault();clearPropertyValue

      $.ajax(
      {
        type: "GET",
        url: $(this).attr('href')+ "/num/" + (0+$('#keyword_table tr').length),
        success: function(html)
        {
          $('#keyword_table').append(html);
          if(tag_value != '')
          {
            $('#keyword_table tr:last td input[type="text"]').val(tag_value);
            tag_value='';
          }
          showAfterRefresh('#keyword_screen');
          $(id).hide() ;
        }
      });
    });
    
    $('.clear_prop').live('click', function(event)
    {
      $('li#'+$(this).attr('id')).show() ;  
      parent_el = $(this).closest('tr');
      $(parent_el).find('input').val('');
      $(parent_el).hide();      
    }); 
    
    $('form.qtiped_form').modal_screen();
  });
</script>
</div>
