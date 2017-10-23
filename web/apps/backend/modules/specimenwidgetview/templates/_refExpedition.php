<table class="catalogue_table_view">
  <tr>
    <td>
      <?php if ($spec->getExpeditionName() != "") : ?>
        <?php echo link_to($spec->getExpeditionName(), 'expedition/view?id='.$spec->getExpeditionRef()) ?>
      <?php endif ; ?>
    </td>
  </tr>
</table>

