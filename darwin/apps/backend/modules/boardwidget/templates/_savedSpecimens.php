<?php slot('widget_title',__('My Saved Specimens'));  ?>

<table class="saved_spec_board">
  <?php foreach($specimens as $specimen):?>
    <tr class="r_id_<?php echo $specimen->getId();?>">
        <td class="fav_col"><?php if($specimen->getFavorite()):?>
            <?php echo image_tag('favorite_on.png', 'alt=Favorite class="fav_img favorite_on"');?>
            <?php echo image_tag('favorite_off.png', 'alt=Favorite class="fav_img favorite_off hidden"');?>
        <?php else:?>
            <?php echo image_tag('favorite_on.png', 'alt=Favorite class="fav_img favorite_on hidden"');?>
            <?php echo image_tag('favorite_off.png', 'alt=Favorite class="fav_img favorite_off"');?>
        <?php endif;?>
        </td>
        <td>
          <?php echo link_to($specimen->getName(),'specimensearch/search?search_id='.$specimen->getId(),array('title'=>__('Go to your search')) ); ?>
          <span class="saved_count">(<?php echo format_number_choice('[0] No Items|[1] 1 Item |(1,+Inf] %1% Items', array('%1%' =>  $specimen->getNumberOfIds()), $specimen->getNumberOfIds());?>)</span>
        </td>
        <td class="row_delete">
         <?php echo link_to(image_tag('remove.png'), 'savesearch/deleteSavedSearch?table=my_saved_searches&id='.$specimen->getId(), array('class'=>'del_butt'));?>
        </td>
    </tr>
<?php endforeach;?>
</table>

<script type="text/javascript">
$(document).ready(function () {

  $('.saved_spec_board .fav_img').click(function(){
    if($(this).hasClass('favorite_on'))
    {
      $(this).parent().find('.favorite_off').removeClass('hidden'); 
      $(this).addClass('hidden') ;
      fav_status = 0;
    }
    else
    {
      $(this).parent().find('.favorite_on').removeClass('hidden');
      $(this).addClass('hidden') ;
      fav_status = 1;
    }
    rid = getIdInClasses($(this).closest('tr'));
    $.get('<?php echo url_for('savesearch/favorite');?>/id/' + rid + '/status/' + fav_status,function (html){
    });
  });

  $('.saved_spec_board a.del_butt').click(function(event)
  {

    event.preventDefault();  
    var answer = confirm('<?php echo addslashes(__('Are you sure ?'));?>');
    if( answer )
    {
      $.get($(this).attr('href'),function (html){
        $('#savedSpecimens').find('.widget_refresh').click();
      });
    }
  });
});
</script>
<br />
<div class="actions">
    <div class="action_button"><?php echo link_to(__("Manage"),'savesearch/index?specimen=1');?></div>
    <div style="clear:right"></div>
</div>
