<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Type');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($types as $type => $items):?>
    <tr>
      <td class="data_grouping">
        <?php echo __(ucfirst($type)); ?>
      </td>
      <td>
        <table class="widget_sub_table" alt="<?php echo $type;?>">
          <thead>
            <tr>
              <th></th>
              <th><?php echo __('People');?></th>
              <th><?php echo __('Sub-Type');?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($items as $person):?>
            <tr class="peo_id_<?php echo $person->getId();?>" id="id_<?php echo $person->getId();?>">
              <td class="handle"></td>
              <td>
                <a class="link_catalogue" title="<?php echo __('View People');?>" href="<?php echo url_for('people/view?id='.$person->getPeopleRef()); ?>">		  
                  <?php echo $person->People->getFormatedName();?>	  
                  <?php echo image_tag('info.png');?>
                </a>		  
              </td>            
              <td class="catalogue_people_sub_type">
                <?php echo $person->getPeopleSubType();?>
              </td>
            </tr>
          <?php endforeach;?>
          </tbody>
        </table>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
