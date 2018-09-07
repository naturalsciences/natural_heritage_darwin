<?php slot('widget_title',__('My Saved Searches'));  ?>

  <table class="saved_searches_board">
  <?php foreach($searches as $search):?>
    <tr class="r_id_<?php echo $search->getId();?>">
        <td class="fav_col"><?php if($search->getFavorite()):?>
            <?php echo image_tag('favorite_on.png', 'alt=Favorite class="fav_img favorite_on"');?>
            <?php echo image_tag('favorite_off.png', 'alt=Favorite class="fav_img favorite_off hidden"');?>
        <?php else:?>
            <?php echo image_tag('favorite_on.png', 'alt=Favorite class="fav_img favorite_on hidden"');?>
            <?php echo image_tag('favorite_off.png', 'alt=Favorite class="fav_img favorite_off"');?>
        <?php endif;?>
        </td>
        <td>
          <?php echo link_to($search->getName(),'specimensearch/search?search_id='.$search->getId(),array('title'=>__('Go to your search')) ); ?>
        </td>
        <td><?php echo link_to(image_tag('criteria.png'),'specimensearch/index?search_id='.$search->getId());?></td>
        <td class="row_delete">
         <?php echo link_to(image_tag('remove.png'), 'savesearch/deleteSavedSearch?table=my_saved_searches&id='.$search->getId(), array('class'=>'del_butt'));?>
        </td>
    </tr>
    <?php endforeach;?>
    </table>

<script type="text/javascript">
$(document).ready(function () {

  $('.saved_searches_board .fav_img').click(function(){
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

  $('.saved_searches_board .del_butt').click(function(event)
  {

    event.preventDefault();  
    var answer = confirm('<?php echo addslashes(__('Are you sure ?'));?>');
    if( answer )
    {
      $.get($(this).attr('href'),function (html){
        $('#savedSearch').find('.widget_refresh').click();
      });
    }
  });
});
</script>
<div class="actions">
    <div class="action_button"><?php echo link_to(__("Manage"),'savesearch/index');?></div>
    <div style="clear:right"></div>
</div>
