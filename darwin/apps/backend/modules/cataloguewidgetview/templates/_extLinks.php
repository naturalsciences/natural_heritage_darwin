<?php use_helper('Text');?>
	<style>
		.spacer{
			height: 10px;
		}
	</style>
  <?php $map_array=Array();$logo_array=Array();
	$map_array["dna"]=[];
	$map_array["links"]=[];
	$map_array["multimedia"]=[];
	$map_array["others"]=[];
	$i=0;
?>
  <?php function parse_links($array, $main_key, $logo_array)
		{
			$returned=[];

			if(array_key_exists($main_key, $array))
			{
				
				foreach($array[$main_key] as $sub_key=>$link)
				{
				
					if($link->getType()=="html_3d_snippet")
					{
						$tmp= '<a  class="link_catalogue_view" href="'.url_for("extlinks/sketchfabSnippet?id=".$link->getId()).'/model/undefined">'.$logo_array[$sub_key]."</a></td>";
					}
					elseif($link->getType()=="iiif")
					{
						$tmp= '<a  class="link_catalogue_view" href="'.url_for("extlinks/iiifViewer?id=".$link->getId()).'/model/undefined">'.$logo_array[$sub_key]."</a></td>";
					}
					else
					{
						$tmp='<a href="'.$link->getUrl().'" target="_blank" class="complete_widget">'.$logo_array[$sub_key].'</a>'; 
					}
					$returned[]="<tr><td>".$tmp."</td><td>".ExtLinks::getLinkTypes()[$link->getType()]."</td><td><a target='_blank' href='".$link->getUrl()."'>".truncate_text($link->getUrl(), 50)."...</a></td><td><div title=".$link->getComment()."/>".truncate_text($link->getComment(), 50)."</div></td><tr>";
					
				}
			}
			
			return $returned;
		}
  ?>
  <?php foreach($links as $link):?>
  <?php
	switch ($link->getType()) {
    case "ext":
        $map_array["others"][$i]=$link;
		$logo_array[$i]=image_tag('extlink',array('title' =>'External URL'));
        break;
    case "html_3d_snippet_general":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('3D_icon',array('title' =>'3D link'));
        break;
	case "html_3d_link":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('3D_icon',array('title' =>'3D link'));
        break;
	case "html_3d_snippet":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('3D_icon',array('title' =>'3D link'));
        break;
    case "dna":
        $map_array["dna"][$i]=$link;
		$logo_array[$i]=image_tag('dna_icon',array('title' =>'DNA icon'));
        break;
	case "dna_genbank":
         $map_array["dna"][$i]=$link;
		 $logo_array[$i]=image_tag('genbank',array('title' =>'GenBank link'));
        break;
	case "dna_elixir":
         $map_array["dna"][$i]=$link;
		 $logo_array[$i]=image_tag('elixir',array('title' =>'Elixir link'));
        break;
	case "dna_labbook":
         $map_array["dna"][$i]=$link;
		 $logo_array[$i]=image_tag('genbank',array('title' =>'DNA Labbook link'));
        break;
	case "iiif":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('image_icon',array('title' =>'Image IIIF link'));
        break;
	case "image":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('image_icon',array('title' =>'Image non-IIIF link'));
        break;
	case "pdf":
         $map_array["links"][$i]=$link;
		 $logo_array[$i]=image_tag('pdf_link',array('title' =>'IPFF link'));
        break;
	case "ltp":
        $map_array["links"][$i]=$link;
		$logo_array[$i]=image_tag('data_icon',array('title' =>'LTP link'));
        break;
	case "nagoya":
        $map_array["links"][$i]=$link;
		$logo_array[$i]=image_tag('nagoya_icon',array('title' =>'Nagoya icon'));
        break;
	case "sound":
       $map_array["multimedia"][$i]=$link;
	   $logo_array[$i]=image_tag('sound_icon',array('title' =>'Sound link'));
        break;
	case "video":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('movie_icon',array('title' =>'Video link'));
        break;
	case "other":
        $map_array["others"][$i]=$link;
		$logo_array[$i]=image_tag('extlink',array('title' =>'External URL'));
        break;
	default:
	}	
	$i++;
   ?>
  
  <?php endforeach;?>
  <?php if(count($map_array)>0): ?>
  
		<table><thead>
		<tr>
		  <th></th>
		  <th><?php echo __('Type');?></th>
		  <th><?php echo __('Url');?></th>
		  <th><?php echo __('Comment');?></th>
		  
		</tr>
	  </thead>
  <?php  if(count($map_array["links"])>0):?>
		
		<tr><td colspan="4"><b><i>Links</i></b></td></tr>
		 <tr class="spacer"><td class="spacer"></td></tr>
		<?php $items=parse_links($map_array, "links", $logo_array); ?>
		<?php print(implode('',$items));?>
		
  <?php endif; ?>
  <?php  if(count($map_array["dna"])>0):?>
		<tr class="spacer"><td class="spacer"></td></tr>
		<tr><td colspan="4"><b><i>DNA</b></i></td></tr>
		 <tr class="spacer"><td class="spacer"></td></tr>
		<?php  $items=parse_links($map_array, "dna", $logo_array); ?>
		<?php print(implode('',$items));?>
		
  <?php endif; ?>
  <?php  if(count($map_array["multimedia"])>0):?>
		<tr class="spacer"><td class="spacer"></td></tr>
		<tr><td colspan="4"><b><i>Multimedia</i></b></td></tr>
		  <tr class="spacer"><td class="spacer"></td></tr>
		<?php  $items=parse_links($map_array, "multimedia", $logo_array); ?>
		<?php print(implode('',$items));?>
		</table>
  <?php endif; ?>
  <?php  if(count($map_array["multimedia"])>0):?>
		<tr class="spacer"><td class="spacer"></td></tr>
		<tr><td colspan="4"><b><i>Multimedia</i></b></td></tr>
		  <tr class="spacer"><td class="spacer"></td></tr>
		<?php  $items=parse_links($map_array, "multimedia", $logo_array); ?>
		<?php print(implode('',$items));?>
		</table>
  <?php endif; ?>
  <?php  if(count($map_array["others"])>0):?>
	<tr class="spacer"><td class="spacer"></td></tr>
		<tr><td colspan="4"><b><i>Others</i></b></td></tr>
		 <tr class="spacer"><td class="spacer"></td></tr>
		<?php $items=parse_links($map_array, "others", $logo_array); ?>
		<?php print(implode('',$items));?>
		
  <?php endif; ?>
	</table>
  <?php endif; ?>

<script  type="text/javascript">
$(document).ready(function () {
    $('#testtp').qtip();

});
</script>



