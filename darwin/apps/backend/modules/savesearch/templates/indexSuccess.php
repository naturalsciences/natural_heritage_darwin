<?php use_helper('Date');?>
<div class="page">

  <?php if($is_only_spec):?>
    <?php slot('title', __('My saved specimens'));?>
    <h1><?php echo __('My saved specimens');?></h1>
  <?php else:?>
    <?php slot('title', __('My saved searches'));?>
    <h1><?php echo __('My saved searches');?></h1>
  <?php endif;?>

  <table style="width:75%" class="saved_searches">
  <tbody>
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
          <div class="search_name">
            <?php echo link_to($search->getName(),'specimensearch/search?search_id='.$search->getId(),array('title'=>__('Go to your search')) ); ?>
            <?php if($is_only_spec):?>
              <span class="saved_count">(<?php echo format_number_choice('[0] No Items|[1] 1 Item |(1,+Inf] %1% Items', array('%1%' =>  $search->getNumberOfIds()), $search->getNumberOfIds());?>)</span>
            <?php endif;?>
          </div>
          <div class="date">
            <?php echo format_datetime($search->getModificationDateTime(),'f');?>
          </div>
        </td>
        <td><?php if(!$is_only_spec):?><?php echo link_to(image_tag('criteria.png'),'specimensearch/index?search_id='.$search->getId());?><?php endif;?></td>
        <td><a href="" class="edit_request"><?php echo image_tag('edit.png');?></a></td>
        <td>
         <?php echo link_to(image_tag('remove.png'), 'savesearch/deleteSavedSearch?table=my_saved_searches&id='.$search->getId(), array('class'=>'del_butt'));?>
        </td>
        <!--ftheeten 2016 06 14-->
         <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
         <td class="rurl_container">
            <select class="url_report">
            <option value=<?php echo("http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_excel%3Areport_excel_page.prpt/report?ID_USER=".sfContext::getInstance()->getUser()->getId()."&ID_Q=".$search->getId()."&userid=report&password=report&output-target=table%2Fexcel%3Bpage-mode%3Dflow&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=false")?>>
            Excel (specimens)
            </option>          
         
             <option value=<?php echo("http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_excel%3Areport_excel_taxonomy.prpt/report?ID_USER=".sfContext::getInstance()->getUser()->getId()."&ID_Q=".$search->getId()."&userid=report&password=report&output-target=table%2Fexcel%3Bpage-mode%3Dflow&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=false")?>>
            Excel (taxonomy)
            </option>
            <option value=<?php echo("http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_rtf%3Areport_inverts_rtf.prpt/report?ID_USER=".sfContext::getInstance()->getUser()->getId()."&ID_Q=".$search->getId()."&userid=report&password=report&output-target=table%2Frtf%3Bpage-mode%3Dflow&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=false")?>>
            RTF publications Invertebrates
            </option>
            </select>
            
            
         </td>
         <td class="report_pager" >
            Page : <input type="number" name="page_excel" id="page_excel" value="1" style="width:30px"/>
            Size : <input type="text" name="size_excel" id="size_excel" value="50000" style="width:40px"/>
         </td>   
         <td>
            <input id="report_link" class="save_search report_link" value="Get report" type="button">
         </td>
         <td>
            <a id="geojson_link" target="_blank" href="<?php print(url_for("savesearch/geojson"));?>?query_id=<?php print($search->getId());?>&user_id=<?php print(sfContext::getInstance()->getUser()->getId()); ?>">GIS Layer (.geojson)</a>
         </td>
         <?php endif;?>
    </tr>
<?php endforeach;?>
    </tbody>
    </table>
</div>

<script type="text/javascript">

$(document).ready(function () {

  $('.saved_searches .del_butt').click(function(event)
  {
    event.preventDefault();
    search_row = $(this).closest('tr');
    var answer = confirm('<?php echo __('Are you sure ?');?>');
    if( answer )
    {
      $.get($(this).attr('href'),function (html){
        search_row.remove();
      });
    }
  });

  $('.saved_searches .fav_img').click(function(){
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



  $(".edit_request").click(function(event){
    event.preventDefault();
    var last_position = $(window).scrollTop();
    scroll(0,0) ;

    $(this).qtip({
      id: 'modal',
      content: {
        text: '<img src="/images/loader.gif" alt="loading"> loading ...',
        title: { button: true, text: '<?php echo ($is_only_spec ? __('Edit your specimens') : __('Edit your search') ) ;?>' },
        ajax: {
          url: '<?php echo url_for('savesearch/saveSearch');?>/id/' + getIdInClasses($(this).closest('tr')),
          type: 'get'
        }
      },
      position: {
        my: 'top center',
        at: 'top center',
        adjust:{
          y: 250 // option set in case of the qtip become too big
        },
        target: $(document.body),
      },

      show: {
        ready: true,
        delay: 0,
        event: event.type,
        solo: true,
        modal: {
          on: true,
          blur: false
        },
      },
      hide: {
        event: 'close_modal',
        target: $('body')
      },
      events: {
        hide: function(event, api) {
          scroll(0,last_position);
          api.destroy();
          location.reload();
        }
      },
      style: 'ui-tooltip-light ui-tooltip-rounded'
    });
    return false;
 });
 
 //ftheeten 2016 10 2016
 $(".report_link").click(function(event){
    
    var url_report = $(this).closest('tr').children("td.rurl_container").find(".url_report").val();
    //ftheeten 2018 12 11
    var size=$(this).closest('tr').find("#size_excel").val();
    var page=$(this).closest('tr').find("#page_excel").val();
    
    if(url_report.includes("report_excel_page"))
    {        
        url_report=url_report+"&PAGE="+page+"&SIZE="+size;
    }
    
    
    window.open(url_report, '_blank');
 });
 
 $(".url_report").change(
    function()
    {
        
        var tmpVal=$(".url_report").val();
        if(tmpVal.includes("report_excel_page"))
        {
            $(".report_pager").css("display","inline");
        }
        else
        {
            $(".report_pager").css("display","none");
        }
    }
 );


 
});
</script>
