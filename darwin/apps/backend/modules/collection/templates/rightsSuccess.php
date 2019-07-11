<div>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<script type="text/javascript">
$(document).ready(function () {
    $('form.qtiped_form').modal_screen();

    $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
    $('.collapsed').click(function()
    {
        $(this).hide();
        $(this).siblings('.expanded').show();
        $(this).parent().siblings('ul').show();
    });
    
    $('.expanded').click(function()
    {
        $(this).hide();
        $(this).siblings('.collapsed').show();
        $(this).parent().siblings('ul').hide();
    });
    $('.treelist li input[type=checkbox]').click(function()
    {
	  class_val = $(this).closest('li').attr('class');
   	  val = $(this).attr('checked') ;
	  alt_val = $(this).closest('ul .'+class_val).find(':checkbox').attr('checked',val);
//	  $('tbody[alt="'+alt_val+'"] tr input[value="'+$(this).val()+'"]').attr("checked","checked");
    		
    });
});
</script>

  <?php if(count($form['collections']) ==0 ):?>
    <div class="warn_message"><?php echo __('This Collection does not have any sub collections that you can manage.');?></div>
  <?php else:?>
    <?php echo form_tag('collection/rights?user_ref='.$sf_params->get('user_ref').'&collection_ref='.$sf_params->get('collection_ref'), array('class'=>'edition qtiped_form', 'id' => 'collection_right_form') );?>
    <table class="widget_sub_table">
      <tr>
        <td>
          <?php echo $form->renderGlobalErrors();?>
          <div class="treelist">
            <?php $prev_level = 0;?>
            <?php foreach($form['collections'] as $id => $col):?>
              <?php $current_col = $form->getEmbeddedForm('collections')->getEmbeddedForm($id)->getCollection();?>
              <?php $current_sub_form = $form->getEmbeddedForm('collections')->getEmbeddedForm($id);?>
              <?php if($prev_level < $current_col->getLevel()):?>
                <ul>
              <?php else:?>
                </li>
                <?php if($prev_level > $current_col->getLevel()):?>
                  <?php echo str_repeat('</ul></li>',$prev_level-$current_col->getLevel());?>
                <?php endif;?>
            <?php endif;?>
              <li class="rid_<?php echo $current_col->getId();?>"><div class="">
              <?php echo image_tag ('individual_expand.png', array('alt' => '+', 'class'=> 'tree_cmd collapsed'));?>
              <?php echo image_tag ('individual_expand_up.png', array('alt' => '-', 'class'=> 'tree_cmd expanded'));?>
              <span><?php echo $current_col->getName();?>
                <?php if($current_col->getMainManagerRef() == $sf_params->get('user_ref')):?>
                  <?php echo $col['db_user_type']->render(array('class'=>'hidden'));?>
                  <?php echo $col['db_user_type']->render(array('disabled'=>'disabled'));?>
                <?php else:?>
                  <?php echo $col['db_user_type']->render();?>
                <?php endif;?>
              </span></div>
            <?php $prev_level =$current_col->getLevel();?>
          <?php endforeach;?>
          <?php echo str_repeat('</li></ul>',$current_col->getLevel());?>
          </div>
        </td>
      </tr>

      <tr>
        <td>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </table>
    </form>
  <?php endif;?>
</div>
