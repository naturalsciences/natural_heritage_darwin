          <tr>
              <td colspan="3">
                <?php echo $form->renderError();?>
              </td>
          </tr>
          <tr class="spec_ident_biblio_data" id="biblio_<?php echo $row_num; ?>">
            <td></td>
            <td>
			
			
			
			<?php $current_label_protocol=$form['bibliography_uri_protocol']->renderLabel(); $raw_current_label_protocol= strip_tags($form['bibliography_uri_protocol']->renderLabel()); $current_uri=$form['bibliography_uri']->renderLabel(); $raw_current_uri_protocol= strip_tags($form['bibliography_uri']->renderLabel());?>
			<div style=" border-width: 2px;border-style: solid;border-color: #C1CF56; cursor: pointer;" >
			 <b>Year : </b><?php echo $form['bibliography_year']->renderLabel();?><br/>
			 <b>Title : </b><?php echo $form['bibliography_ref']->renderLabel();?><br/>
			 <?php if(strtolower($raw_current_label_protocol)!=="none"): ;?>
				 <b>URL : </b>
				 <?php if(strtolower( $raw_current_label_protocol)=="doi"):?>
					<a href="https://dx.doi.org/<?php  print($raw_current_uri_protocol); ?>" target="_blank">https://dx.doi.org/<?php  print($raw_current_uri_protocol); ?></a>
				 <?php elseif(strip_tags($raw_current_label_protocol)=="url"):?>
					 <a href="<?php  print($raw_current_uri_protocol); ?>" target="_blank"><?php  print($raw_current_uri_protocol); ?></a>
				 <?php else:?>
					 <?php print($current_uri);?>
				 </div>
				<?php endif;?>
			<?php endif;?>
			</td>
            <td class="widget_row_delete">
              <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_biblio_'.$row_num); ?>
              <?php echo $form->renderHiddenFields();?>
    <script type="text/javascript">
      $(document).ready(function () {
        $("#clear_biblio_<?php echo $row_num;?>").click( function()
        {
           parent_el = $(this).closest('tr');
           parentTableId = $(parent_el).closest('table').attr('id')
           $(parent_el).find('input[id$=\"_bibliography_ref\"]').val('');
           $(parent_el).hide();
           $.fn.catalogue_people.reorder( $(parent_el).closest('table') );
           visibles = $('table#'+parentTableId+' .spec_ident_biblio_data:visible').size();
           if(!visibles)
           {
            $(this).closest('table#'+parentTableId).find('thead').hide();
           }
        });
        $('table .hidden_record').each(function() {
          $(this).closest('tr').hide() ;
        });
      });
    </script>
            </td>
          </tr>
