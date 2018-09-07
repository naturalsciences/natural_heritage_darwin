<table class="catalogue_table_view">
  <tbody>
  <?php foreach($Biblios as $bib):?>
  <tr>
    <td>
      <a class="link_catalogue" title="<?php echo __('Bibliography');?>" href="<?php echo url_for('bibliography/view?id='.$bib->getBibliographyRef()) ; ?>"><?php echo $bib->Bibliography->getTypeFormatted(); ?></a>
    </td>
    <td>
      <?php echo $bib->Bibliography->getTitle(); ?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
