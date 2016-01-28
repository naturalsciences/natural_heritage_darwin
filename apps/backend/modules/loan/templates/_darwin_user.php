<?php if($form['user_ref']->getValue()!=""):?>
    <tr id="user_<?php echo $row_num; ?>">
      <td><?php if($form['user_ref']->getValue()) : ?>
      <?php echo image_tag(Doctrine::getTable('Users')->find($form['user_ref']->getValue())
                               ->getCorrespondingImage()) ; ?>
          <?php endif ; ?>
      </td>
      <td><?php
        if($sf_user->isA(Users::ADMIN) || ($sf_user->getId()==$form['user_ref']->getValue())) {
          echo link_to(
            $form[ 'user_ref' ]->renderLabel(),
            'user/edit',
            array (
              'query_string' => 'id=' . $form[ 'user_ref' ]->getValue()
            )
          );
        }
        else {
          echo $form['user_ref']->renderLabel();
        }
        ?>
      </td>
      <td><?php echo $form['has_encoding_right']->render() ; ?></td>
      <td class="widget_row_delete">
        <?php echo image_tag('remove.png', 'alt=Delete class=clear_code id=clear_user_'.$row_num); ?>
        <?php echo $form->renderHiddenFields();?>
    <script type="text/javascript">
      $(document).ready(function () {
        $("#clear_user_<?php echo $row_num;?>").click( function()
        {
           parent_el = $(this).closest('tr');
           parentTableId = $(parent_el).closest('table').attr('id')
           $(parent_el).find('input[id$=\"_user_ref\"]').val('');
           $(parent_el).hide();
//           $.fn.catalogue_people.reorder( $(parent_el).closest('table') );
           visibles = $('table#'+parentTableId+' .user_data:visible').size();
        });
        $('table .hidden_record').each(function() {
          $(this).closest('tr').hide() ;
        });
      });
    </script>
    </td>
  </tr>
<?php endif ; ?>  
