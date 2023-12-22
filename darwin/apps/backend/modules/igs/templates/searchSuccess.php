<?php if($form->isValid()):?>
  <?php
	if(isset($increment))
	{
		if($increment)
		{
			$s_url.="&increment=on";
		}
		
	}
	else
	{
		$increment=false;
	}
  ?>
  <?php if(isset($igss) && $igss->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage) && isset($is_choose)):?>
    <?php
      if($orderDir=='asc')
        $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
      else
        $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
    ?>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
    <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout)); ?>
    <div class="results_container">
      <table class="results">
        <thead>
          <tr>
            <th>
              <a class="sort sort_by_ig_num" href="<?php echo url_for($s_url.'&orderby=ig_num'.( ($orderBy=='ig_num' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('I.G.');?>
                <?php if($orderBy=='ig_num') echo $orderSign ?>
              </a>
            </th>
			 <?php if($increment): ?>
				<th><?php echo __("Copy");?></th>
				<th><?php echo __("Increment");?></th>
			 <?php endif;?>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=ig_type'.( ($orderBy=='ig_type' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Type');?>
                <?php if($orderBy=='ig_type') echo $orderSign ?>
              </a>
            </th>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=ig_date'.( ($orderBy=='ig_date' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('I.G. creation date');?>
                <?php if($orderBy=='ig_date') echo $orderSign ?>
              </a>
            </th>
            <th><?php echo __('Comment') ; ?></th>
            
            <!--ftheeten 2018 04 10-->
            <th><?php echo __('Total count'); ?></th>
            <!--ftheeten 2018 04 10-->
            <th><?php echo __('Count by collection'); ?></th>
			<th><?php echo __('Nagoya status'); ?></th> 
			<?php if(!$is_choose): ?>				
            <!--ftheeten 2016 10 28 -->
				 <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
					<th>Report</th>
					<th>&nbsp;</th>
				<?php endif;?>
				
				<!--ftheeten 2018 04 10-->
				<th colspan="4"><?php echo __("Links");?></th>
				
				<th><?php echo __("Detail");?></th>
			<?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($igss as $igs):?>
            <tr class="rid_<?php echo $igs->getId(); ?>">
              <td><?php echo $igs->getIgNum();?></td>
			  <?php if($increment): ?>
			    <td><input type="button" class="ig_copy" value="Copy" ig_val="<?php echo $igs->getIgNum();?>"></input></td>
				<td><input type="button" class="ig_increment" value="Increment" ig_val="<?php echo $igs->getIgNum();?>"></input></td>
			  <?php endif;?>
              <td><?php echo Igs::getIgTypeAllowedValue()[$igs->getIgType()];?></td>
              <td><?php echo $igs->getIgDateMasked(ESC_RAW);?></td>
              <td><?php echo (isset($comments[$igs->getId()])? $comments[$igs->getId()] : '&nbsp;')  ?></td>              
             
                <!--ftheeten 2018 04 10-->
                 <td>                 
                   <?php print($igs->getSpecCount())?>
                </td>
				<td>                 
                   <?php print($igs->getSpecCountByCollection())?>
                </td>
				<td>                 
                   <?php print($igs->getNagoyaStatus())?>
                </td>
				<?php if(!$is_choose): ?>	
				  <!--ftheeten 2016 10 28 -->
				  <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
				  <td class="rurl_container">
						<select class="url_report">
							<option value=<?php echo("http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3Areports_ig%3Arapport_ig_ichtyo.prpt/report?REPORT_KEY=".$igs->getId()."&userid=report&password=report&output-target=pageable%2Fpdf&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=false")?>>PDF</option>
						</select>
				  </td>
				   <td>
						<input id="report_link" class="save_search report_link" value="Get report" type="button">
					</td>
					
				   <?php endif;?>
				   <td><!--ftheeten 2018 04 10-->
					<?php echo form_tag('specimensearch/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('target'=>'_blank', 'class'=>'specimensearch_form','id'=>'specimen_filter'));?><input type="hidden" id="specimen_search_filters[ig_ref]" name="specimen_search_filters[ig_ref]" value="<?php echo($igs->getId());?>"/><input type="submit" value="<?php echo __("Get specimens")?>"></form>
				   </td>
				   <td><!--ftheeten 2018 04 10-->
						<?php echo link_to(__('Scientific names'),'taxonomy/index?ig_num='.$igs->getIgNum(), array("target"=>"_href"));?>
				   </td>
					<td><!--ftheeten 2018 04 10-->
						<?php echo link_to(__('Localities'),'gtu/index?ig_num='.$igs->getIgNum(), array("target"=>"_href"));?>
				   </td>
				   <td><!--ftheeten 2018 04 10-->
						<?php echo link_to(__('People'),'people/index?ig_num='.$igs->getIgNum(), array("target"=>"_href"));?>
				   </td>
				   <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
				   <td class="<?php echo (! $is_choose)?'edit':'choose';?>">
						<?php if(! $is_choose):?>
						  <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
							<?php echo link_to(image_tag('edit.png',array('title'=>'Edit IGS')),'igs/edit?id='.$igs->getId(), array("target"=> "_blank"));?>
							<?php echo link_to(image_tag('duplicate.png',array('title'=>'Duplicate IGS')),'igs/new?duplicate_id='.$igs->getId(), array("target"=> "_blank"));?>
						  <?php endif ;?>
						  <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'igs/view?id='.$igs->getId(), array("target"=> "_blank"));?>
						<?php else:?>
						  <div class="result_choose"><?php echo __('Choose');?></div>
						<?php endif;?>
				  </td>
				   <?php endif; ?>
				 <?php endif; ?>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <script type="text/javascript">
        //ftheeten 2016 10 28
        $(document).ready(function () {  
             //ftheeten 2016 10 2016
             $(".report_link").click(function(event){
                
                var url_report = $(this).closest('tr').children("td.rurl_container").find(".url_report").val();
                window.open(url_report, '_blank');
             });
        
        });
    </script>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php else:?>
    <?php echo __('No I.G. Matching');?>
  <?php endif;?>
<?php else:?>
  <div class="error">
    <?php if(!$form['to_date']->hasError()): ?>
      <?php echo $form->renderGlobalErrors();?>
    <?php endif; ?>
    <?php echo $form['ig_num']->renderError() ?>
    <?php echo $form['from_date']->renderError() ?>
    <?php if(!$form['from_date']->hasError()): ?>
      <?php echo $form['to_date']->renderError() ?>
    <?php endif; ?>
  </div>
<?php endif;?>
