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
         <!--ftheeten 2016 06 14-->
         
        <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
         <td class="rurl_container">
            <select class="url_report">
            <option value=<?php echo("http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_excel%3Areport_excel.prpt/report?ID_USER=".sfContext::getInstance()->getUser()->getId()."&ID_Q=".$search->getId()."&userid=report&password=report&output-target=table%2Fexcel%3Bpage-mode%3Dflow&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=false")?>>
            Excel
            </option>
            <option value=<?php echo("http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_rtf%3Areport_inverts_rtf.prpt/report?ID_USER=".sfContext::getInstance()->getUser()->getId()."&ID_Q=".$search->getId()."&userid=report&password=report&output-target=table%2Frtf%3Bpage-mode%3Dflow&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=false")?>>
            RTF publications Invertebrates
            </option>
            </select>
         </td>
         <td>
            <input id="report_link" class="save_search report_link" value="Get report" type="button">
         </td>
       <?php endif;?>     
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
  
   $(".report_link").click(function(event){
    
    var url_report = $(this).closest('tr').children("td.rurl_container").find(".url_report").val();
    window.open(url_report, '_blank');
 });
  
});
</script>
<div class="actions">
    <div class="action_button"><?php echo link_to(__("Manage"),'savesearch/index');?></div>
    <div style="clear:right"></div>
</div>
