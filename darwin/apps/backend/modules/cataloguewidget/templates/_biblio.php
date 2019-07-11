<table class="catalogue_table">
  <tbody>
  <?php foreach($Biblios as $bib):?>
  <tr>
    <td>
      <a class="link_catalogue" title="<?php echo __('Bibliography');?>" href="<?php echo url_for('catalogue/biblio?id='.$bib->getBibliographyRef()) ; ?>"><?php echo $bib->Bibliography->getTitle(); ?></a>
    </td>
    <td class="widget_row_delete">
      <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=catalogue_bibliography&id='.$bib['id']);?>" title="<?php echo __('Delete Bibliography') ?>"><?php echo image_tag('remove.png'); ?>
      </a>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
<br />
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Bibliography');?>" class="link_catalogue" href="<?php echo url_for('catalogue/biblio?table='.$table.'&rid='.$eid);?>"><?php echo __('Add');?></a>
