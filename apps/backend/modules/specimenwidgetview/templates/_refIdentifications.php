<table class="catalogue_table_view" id="identifications">
  <thead style="<?php echo ($identifications->count()?'':'display: none;');?>" class="spec_ident_head">
    <tr>
      <th><?php echo __('Date'); ?></th>
      <th><?php echo __('Category');?></th>
      <th><?php echo __('Subject'); ?></th>
      <th><?php echo __('Det. St.'); ?></th>
      <th><?php echo __("Identifiers") ; ?></th>
    </tr>
  </thead>
  <?php foreach($identifications as $identification):?>
  <tbody id="refIdent" class="spec_ident_data">
    <tr class="spec_ident_data">
      <td class="datesNum">
        <?php echo sfOutputEscaper::unescape($identification->getNotionDateMasked()); ?>
      </td>
      <td>
        <?php echo $identification->getNotionConcerned();?>
      </td>
      <td>
        <?php echo $identification->getValueDefined();?>
      </td>
      <td>
        <?php echo $identification->getDeterminationStatus();?>
      </td>
      <td>
        <ul class="tool">
        <?php foreach($people[$identification->getId()] as $identifier):?>
           <li><?php echo $identifier; ?></li>
        <?php endforeach ; ?>
        </ul>
      </td>
    </tr>
  </tbody>
  <?php endforeach;?>
</table>
