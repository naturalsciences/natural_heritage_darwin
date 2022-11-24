<table class="catalogue_table_view">
  <tbody>
    <tr>
      <td colspan="2">
        <?php echo image_tag('info.png',array(
          'title'=>'info',
          'class'=>'coll_extd_info',
          'data-manid'=> $spec->getCollections()->getMainManagerRef(),
          'data-staffid'=> $spec->getCollections()->getStaffRef())
          );?>
        <?php if($sf_user->isAtLeast(Users::ADMIN) || ($sf_user->isAtLeast(Users::MANAGER) )) : ?>  
		  <?php 		  
		  $assoc_cols=explode("|",trim(Collections::getCollectionPathName($spec->getCollectionRef(), $sf_user),'|'));			
           array_walk($assoc_cols, function(&$k, $v){ $k=link_to($k,url_for('collection/statistics?id='.$v), array('target' => '_blank')); });         	   
		   $full_urls=implode("/",$assoc_cols);		  
		  ?>
          <?php print($full_urls);?>
        <?php else : ?>
         <?php echo trim(implode("/",Collections::getCollectionPathName($spec->getCollectionRef(), $sf_user)),"/");?></a>
        <?php endif ; ?>
      </td>
    </tr>
    
  </tbody>
</table>
<script type="text/javascript">
$(document).ready(function () {
  $(".coll_extd_info").qtip({
    show: { solo: true, event:'click' },
    hide: { event:false },
    style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue',
    content: {
      text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
      title: { button: true, text: ' ' },
      ajax: {
        url: '<?php echo url_for('collection/extdinfo');?>',
        type: 'GET',
        data: { id: $(".coll_extd_info").attr('data-manid'), staffid: $(".coll_extd_info").attr('data-staffid')}
      }
    }
  });
});
</script>
