          <?php if($form->hasError()):?>
          <tr>
              <td>
                <?php echo $form->renderError();?>
              </td>
          </tr>
          <?php endif;?>
          <tr>
            <td><?php echo $form['people_ref']->renderError();?></td>          
          </tr>
          <tr class="spec_ident_collectors_data" id="<?php echo $id_field.'_'.$row_num; ?>">
            <td><?php echo $form['people_ref']->render();?></td>
          </tr>
