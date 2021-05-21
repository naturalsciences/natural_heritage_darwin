<?php use_helper('Text');?>
<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Url');?></th>
      <th><?php echo __('Comment');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($links as $link):?>
  <tr>
    <td>
	 <?php  if($link->getType()=="html_3d_snippet"):?>
                <a  class="link_catalogue_view" href="<?php echo url_for("extlinks/sketchfabSnippet?id=".$link->getId());?>/model/undefined"><?php echo image_tag('3D_icon',array('title' =>'3D link'));?></a></td>
	<?php  elseif($link->getType()=="iiif"):?>
				<a  class="link_catalogue_view" href="<?php echo url_for("extlinks/iiifViewer?id=".$link->getId());?>/model/undefined"><?php echo image_tag('image_icon',array('title' =>'Image IIIF link'));?></a></td>
    <?php elseif($link->getType() == 'dna') : ?>
       <a href="<?php echo $link->getUrl();?>" target="_blank" class='complete_widget'>
        <?php echo image_tag('dna_icon',array('title' =>'DNA icon'));?></a>
      </a>
	 <?php elseif($link->getType() == 'ltp') : ?>
       <a href="<?php echo $link->getUrl();?>" target="_blank" class='complete_widget'>
        <?php echo image_tag('data_icon',array('title' =>'Data icon'));?></a>
      </a>
	 <?php elseif($link->getType() == 'sound') : ?>
       <a href="<?php echo $link->getUrl();?>" target="_blank" class='complete_widget'>
        <?php echo image_tag('sound_icon',array('title' =>'Sound icon'));?></a>
      </a>
	  <?php elseif($link->getType() == 'nagoya') : ?>
       <a href="<?php echo $link->getUrl();?>" target="_blank" class='complete_widget'>
        <?php echo image_tag('nagoya_icon',array('title' =>'Nagoya icon'));?></a>
      </a>
	  <?php elseif($link->getType() == 'video') : ?>
       <a href="<?php echo $link->getUrl();?>" target="_blank" class='complete_widget'>
        <?php echo image_tag('movie_icon',array('title' =>'Video icon'));?></a>
      </a>
    <?php else : ?>
      <a href="<?php echo $link->getUrl();?>" target="_blank" class='complete_widget'>
        <?php echo image_tag('other_link',array('title' =>'External URL'));?></a>
      </a>
    <?php endif ; ?>
    </td>
    <td>
      <div title="<?php echo $link->getComment();?>"><?php echo truncate_text($link->getComment(), 50);?></div>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
<script  type="text/javascript">
$(document).ready(function () {
    $('#testtp').qtip();

});
</script>



