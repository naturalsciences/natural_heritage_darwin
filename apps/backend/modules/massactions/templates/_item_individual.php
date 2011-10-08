      <tr>
        <td>
          <?php echo image_tag('info.png',"title=info class=extd_info");?>
          <div class="extended_info" style="display:none;">
            <dl>
              <dt><?php echo __('Collection :');?></dt>
              <dd><?php echo $item->getCollectionName();?></dd>
              <dt><?php echo __('Taxonomy :');?></dt>
              <dd><?php echo $item->getTaxonName();?></dd>
              <dt><?php echo __('Sampling Location :');?></dt>
              <dd><?php echo $item->getGtu(ESC_RAW);?></dd>
              <dt><?php echo __('Type :');?></dt>
              <dd><?php echo $item->SpecimenIndividuals[0]->getTypeGroup();?></dd>
              <dt><?php echo __('Sex :');?></dt>
              <dd><?php echo $item->SpecimenIndividuals[0]->getSex();?></dd>
              <dt><?php echo __('State :');?></dt>
              <dd><?php echo $item->SpecimenIndividuals[0]->getState();?></dd>
              <dt><?php echo __('Social Status :');?></dt>
              <dd><?php echo $item->SpecimenIndividuals[0]->getSocialStatus();?></dd>
            </dl>
          </div>
        </td>
        <td>
          <?php echo truncate_text($item->getAggregatedName(),40);?>
        </td>
        <td>
          <input name="mass_action[item_list][]" type="hidden" value="<?php echo $item->SpecimenIndividuals[0]->getId();?>" class="item_row">
          <a class="row_delete" href="#" title="<?php echo __('Are you sure ?') ?>"><?php echo image_tag('remove.png'); ?></a>
        </td>
      </tr>