<?php if($files->count() && $atLeastOneFileVisible): ?>
<style>
.truncate {
  width: 250px;
  /* need automatic multi-line height */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  border:1px solid #999999;
  float: left;
  word-wrap: break-word;
}
.truncate.open {
  width: 500px;
  white-space: normal;
  text-overflow: inherit;
}

.helpicon {
  position: relative;
  float:right;
}
</style>

  <table class="catalogue_table_view">
    
    <tbody id="file_body">
      <?php $row_num = 0;?>
      <?php foreach($files as $file):?>
        <?php $row_num+=1;?>
		<tr><td>
		<fieldset>
		<legend>Document nr. <?php print($row_num); ?></legend>
		<div>
		<table>
		<thead>
		  <tr>
			<th><?php echo __('Name'); ?></th>
			<th><?php echo __('Description'); ?></th>
			<th><?php echo __('Created At') ; ?></th>
		  </tr>
		</thead>
        <tr class="row_num_<?php echo $row_num;?>">
          <td><?php echo $file->getTitle(); ?></td>
          <td><?php echo $file->getDescription(); ?></td>
          <td><?php $date = new DateTime($file->getCreationDate());
                    echo $date->format('d/m/Y'); ?></td>
        </tr>
		<thead>
		  <tr>
			<th><?php echo __('Download'); ?></th>
			<th colspan="2"><?php echo __('MIME'); ?></th>
		  </tr>
		</thead>
        <tr class="row_num_<?php echo $row_num;?>">
          <td>
            <?php $alt=($file->getDescription()!='')?$file->getTitle().' / '.$file->getDescription():$file->getTitle();?>
            
              <?php echo link_to($file->getFileName()." ".image_tag('criteria.png'),'multimedia/downloadFile?id='.$file->getId(), array('alt'=>$alt, 'title'=>$alt)) ; ?>
           
          </td>
          <td colspan="2"><?php echo $file->getMimeType(); ?></td>
        </tr>
		<?php if(stripos($file->getMimeType(), "image")===0):?>
		   <tr>
			<td colspan="3">
				<img src="<?php print(url_for("multimedia/preview")."/id/".$file->getId()."/width/200/height/200");?>" ></img>
			</td>
		    </tr>
		<?php endif;?>
		<?php if(strlen(trim($file->getFieldObservations()))>0):?>
		<thead>
		<tr>
			<th>Field observations</th>
		</tr>
		</thead>
		<tr>
			<td>
			<div class="truncate" id="truncate_<?php print($row_num);?>">
				 <button class="helpicon" id="helpicon_<?php print($row_num);?>">
					?
				 </button>
					<?php print($file->getFieldObservations()); ?>
				</div>
			</td>
		</tr>
		<?php endif;?>
		<?php if(strlen(trim($file->getTechnicalParameters()))>0):?>
		<thead>
		<tr>
			<th>Technical parameters</th>
		</tr>
		</thead>
		<tr>
			<td>
			<div class="truncate" id="truncate_tech_<?php print($row_num);?>">
				 <button class="helpicon" id="helpicon_tech<?php print($row_num);?>">
					?
				 </button>
					<?php print($file->getTechnicalParameters()); ?>
				</div>
			</td>
		</tr>
		<?php endif;?>
        <script type="text/javascript">
          $("tr.row_num_<?php echo $row_num;?>").hover(function(){
                                                                  parent_el = $(this).closest('tbody');
                                                                  parent_tr = $(parent_el).children('tr.row_num_<?php echo $row_num;?>');
                                                                  $(parent_tr).css('background-color', '#E9EDBE');
                                                                },
                                                      function(){
                                                                  parent_el = $(this).closest('tbody');
                                                                  parent_tr = $(parent_el).children('tr.row_num_<?php echo $row_num;?>');
                                                                  $(parent_tr).css('background-color', '#F6F6F6');
                                                                });
																

			  
			
																		
		
        </script>
		</table>
		</div>
		</fieldset>
		</td></tr>
      <?php endforeach;?>
    </tbody>
  </table>
   <script type="text/javascript">
   
   
			 $('.helpicon').on('click', function () {
				    console.log("click");
			  //get the current text value of the ? or X
			  var text = $(this).text();
					//when we click the ? or X, toggle an open class
				$(this).closest('.truncate').toggleClass('open');
				//toggle the X with an ?
				$(this).text(text == "X" ? "?":"X");
				});
   </script>
<?php endif;?>