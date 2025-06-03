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
					$returned[]="<tr><td>".$tmp."</td><td>".ExtLinks::getLinkTypes()[$link->getType()]."</td><td><a target='_blank' href='".$link->getUrl()."'>".truncate_text($link->getUrl(), 50)."...</a></td><td><div title=".$link->getComment()."/>".truncate_text($link->getComment(), 50)."</div></td><td>".$link->getAccessRights()."</td><tr>";
					
				}
			}
			
			return $returned;
		}
  ?>
  <?php foreach($links as $link):?>
  <?php
	switch ($link->getType()) {
	/*
	'ext' => 'Other External',
    //'vc' => 'Virtual Collection',
	'html_3d_snippet_general' => '3D (Frame general)',
	'html_3d_link' => '3D (Link)',
	'html_3d_snippet' => '3D (Sketchfab)',
	'dna' => 'DNA',
	'dna_genbank' => 'DNA (Genbank)',
	'dna_elixir' => 'DNA (Elixir)',
	'dna_labbook' => 'DNA Labbook',
	'dna_molecdata' => 'DNA(Molecular data)',
	'dna_molecdata_assocsp'=>'DNA(MolecData AssocSp)',
    'dna_permit' => 'DNA(Permit)',
	'iiif' => 'Image (IIIF)',
	'image' => 'Image (non IIIF)',
	'pdf' => 'PDF',
	'publication'=> 'Publication',
	'ltp' => 'LTP',
	'nagoya'=> 'Nagoya',
	'sound' => 'Sound',
	'video' => 'Video',
	'other' => 'Others'
	*/
    case "ext":
        $map_array["others"][$i]=$link;
		$logo_array[$i]=image_tag('extlink',array('title' =>'External URL'));
        break;
	case "2d_stackoptica":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('2d_stackoptica',array('title' =>'2d_stackoptica'));
        break;
	case "2d_sphaeroptica":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('2d_sphaeroptica',array('title' =>'2d_sphaeroptica'));
        break;
	case "2d_spectraloptica":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('2d_spectraloptica',array('title' =>'2d_spectraloptica'));
        break;
	case "2d_polaroptica":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('2d_polaroptica',array('title' =>'2d_polaroptica'));
        break;
	case "2d_inside":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('2d_inside',array('title' =>'2d_inside'));
        break;
	case "3d_inside":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('3d_inside',array('title' =>'3d_inside'));
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
	case "dna_molecdata":
         $map_array["dna"][$i]=$link;
		 $logo_array[$i]=image_tag('dna_icon',array('title' =>'DNA Molecular data link'));
        break;
	case "dna_molecdata_assocsp":
         $map_array["dna"][$i]=$link;
		 $logo_array[$i]=image_tag('dna_icon',array('title' =>'DNA Molecular data associated specimen link'));
        break;
	case "dna_permit":
         $map_array["dna"][$i]=$link;
		 $logo_array[$i]=image_tag('dna_icon',array('title' =>'DNA permit'));
        break;
	case "iiif":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('image_icon',array('title' =>'Image IIIF link'));
        break;
	case "iiif_info":
        $map_array["multimedia"][$i]=$link;
		$logo_array[$i]=image_tag('image_icon',array('title' =>'iiif_info'));
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
	case "internal_database":
        $map_array["others"][$i]=$link;
		$logo_array[$i]=image_tag('rbins-icon',array('title' =>'Link to RBINS database system'));
        break;
	case "windows_file_system":
        $map_array["others"][$i]=$link;
		$logo_array[$i]=image_tag('rbins-icon',array('title' =>'Link to file system or NAS'));
        break;
	case "linux_file_system":
        $map_array["others"][$i]=$link;
		$logo_array[$i]=image_tag('rbins-icon',array('title' =>'Link to file system or NAS'));
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
		  <th><?php echo __('Access rights');?></th>
		  
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



