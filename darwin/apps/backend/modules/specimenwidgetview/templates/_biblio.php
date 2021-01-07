<ul class="bib_view">
  <?php foreach($Biblios as $bib):?>
    <li>
	<div style=" border-width: 2px;border-style: solid;border-color: #5baabd;">
     
	  <?php $full_bib=$bib->Bibliography;?>
	  <table>
	  <tr><td><b/>Year : </b></td><td><?php echo $full_bib->getYear(); ?></a></td></tr>
	  <tr><td><b/>Title : </b></td><td><a target="_blank" href="<?php echo url_for('bibliography/view?id='.$bib->getBibliographyRef()) ; ?>"><?php echo $full_bib->getTitle(); ?></a></td></tr>
	  <?php if(strlen(trim($full_bib->getUri()))):?>
		 <tr><td><b>URI : </b></td><td>
		  <?php if(strtolower($full_bib->getUriProtocol())=="doi") : ?>
				   <a href="https://dx.doi.org/<?php  print($full_bib->getUri()); ?>" target="_blank"><?php  print($full_bib->getUri()); ?></a>
				  <?php elseif(strtolower($full_bib->getUriProtocol())=="url"):?>
				   <a href="<?php  print($full_bib->getUri()); ?>" target="_blank"><?php  print($full_bib->getUri()); ?></a>
				 <?php else:?>
				 <?php>  <?php  print($full_bib->getUri()); ?></a>			 
		  <?php endif;?>
	   <?php endif;?>
	   </td>
	   </tr>
	 </table>
	</div>
	<br/>
	</li>
  <?php endforeach;?>
</li>
