<?php if(count($parts)==0):?>
  <h2><?php echo __('Currently, there are no parts');?></h2>
<?php else:?>
<table class="catalogue_table<?php if($view) echo '_view' ; ?>">
  <thead>
  <tr>
    <th></th>
    <th><?php echo __('Code');?></th>
    <th><?php echo __('Part');?></th>
    <th><?php echo __('Room');?></th>
    <th><?php echo __('Row');?></th>
    <th><?php echo __('Shelf');?></th>
    <th><?php echo __('Container');?></th>
    <th><?php echo __('Sub container');?></th>
    <th></th>
    <th></th>
    <th></th>   
    <?php if ($view) : ?><th></th><?php endif ;?>
  </tr>
  </thead>
  <tbody>
  <?php foreach($parts as $part):?>
  <tr class="rid_<?php echo $part->getId();?>">
 	  <?php if ($sf_user->isAtLeast(Users::ENCODER)): ?>
      <td style="padding-left: <?php echo 20*($part->getLevel()-1);?>px; ">
        <?php echo image_tag('info-green.png',"title=info class=extd_info_".$part->getId());?>
        <div class="extended_info" style="display:none;">          
        </div>
        <script  type="text/javascript">
          $(".extd_info_<?php echo $part->getId();?>").qtip({
            show: { solo: true, event:'mouseover' },
            hide: { event:'mouseout' },
            style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue',
            content: {
              text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
              title: { text: '<?php echo __("Linked Info") ; ?>' },
              ajax: {
                url: '<?php echo url_for("parts/extendedInfo");?>',
                type: 'GET',
                data: { id: '<?php echo $part->getId() ; ?>' }
              }
            }
          });        
        </script>
      </td>
    <?php else : ?>
      <td>&nbsp;</td>
    <?php endif ; ?>  
    <td class="code_name"><?php if(isset($codes[$part->getId()])):?>
      <ul><?php foreach($codes[$part->getId()] as $code):?>
      <li><?php echo $code->getCodeFormated();?></li>
      <?php endforeach;?></ul>
    <?php endif;?>
    </td>
    <td class="item_name"><?php echo $part->getSpecimenPart();?></td>
    <?php if($sf_user->isA(Users::REGISTERED_USER)) : ?>
      <td colspan="5">&nbsp;</td>
    <?php else : ?>
      <td><?php echo $part->getRoom();?></td>
      <td><?php echo $part->getRow();?></td>
      <td><?php echo $part->getShelf();?></td>
      <td><?php echo $part->getContainer();?></td>
      <td><?php echo $part->getSubContainer();?></td>
    <?php endif ; ?>  
    <?php if(!isset($is_choose) || $is_choose==false):?>
      <td>
        <?php echo link_to(image_tag('blue_eyel.png'),'parts/view?id='.$part->getId(), array('title'=>__('View this part')));?>
      </td>      
      <?php if (!$view): ?> 
      <td>
        <?php echo link_to(image_tag('edit.png'),'parts/edit?id='.$part->getId(), array('title'=>__('Edit this part')));?>
      </td>
      <td>
        <?php echo link_to(image_tag('duplicate.png',array('title'=>__('Duplicate this part'))), 'parts/edit?indid='.$individual->getId().'&duplicate_id='.$part->getId(),array('class' => 'duplicate_link')) ?>
      </td>      
      <td>
        <a class="row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=specimen_parts&id='.$part->getId());?>" title="<?php echo __('Delete this part') ?>"><?php echo image_tag('remove.png'); ?>
      </td>
      <?php else : ?>
        <td colspan="3">&nbsp;</td>
      <?php endif ; ?>      
    <?php else:?>
      <td colspan="2">
        <div class="result_choose"><?php echo __('Choose');?></div>
      </td>
    <?php endif;?>
  </tr>
  <?php endforeach;?>
  </tbody>
  <?php if((!isset($is_choose) || $is_choose==false) && (!$view)):?>
    <tfoot>
    <tr>
      <td colspan='10'>
        <div class="add_spec_individual">	        
          <a href="<?php echo url_for('parts/edit?indid='.$individual->getId());?>"><?php echo __('Add part');?></a>
        </div>
      </td>
    </tr>
    </tfoot>
    <?php endif;?>
</table>
<?php endif;?>
