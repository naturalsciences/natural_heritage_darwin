<?php include_javascripts_for_form($form) ?>
<div id="multimedia_screen">
  <?php $request_params = ($sf_request->hasParameter('rid'))?'?rid='.$sf_request->getParameter('rid'):'?table='.$sf_request->getParameter('table').'&id='.$sf_request->getParameter('id').'&file_id='.$sf_request->getParameter('file_id') ; echo form_tag('multimedia/add'.$request_params , array('class'=>'edition qtiped_form', 'enctype'=>'multipart/form-data'));?>
    <?php echo $form->renderHiddenFields();?>
    <table>
      <tbody>
        <tr>
          <td colspan="2">
            <?php echo $form->renderGlobalErrors() ?>
          </td>
        </tr>
        <tr>
          <th class="top_aligned"><?php echo $form['title']->renderLabel();?></th>
          <td>
            <?php echo $form['title']->renderError(); ?>
            <?php echo $form['title'];?>
          </td>
        </tr>
        <tr>
          <th class="top_aligned"><?php echo $form['description']->renderLabel();?></th>
          <td>
            <?php echo $form['description']->renderError(); ?>
            <?php echo $form['description'];?>
          </td>
        </tr>
        <tr>
          <th class="top_aligned"><?php echo $form['visible']->renderLabel();?></th>
          <td>
            <?php echo $form['visible']->renderError(); ?>
            <?php echo $form['visible'];?>
          </td>
        </tr>
        <tr>
          <th class="top_aligned"><?php echo $form['publishable']->renderLabel();?></th>
          <td>
            <?php echo $form['publishable']->renderError(); ?>
            <?php echo $form['publishable'];?>
          </td>
        </tr>
      </tbody>
    </table>
    <table class="bottom_actions">
      <tfoot>
        <tr>
          <td>
            <ul class="error_list" id="file_error_message" style="display:none">
              <li></li>
            </ul>
          </td>
          <td>
            <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
            <?php if(! $form->getObject()->isNew()):?>
              <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=multimedia&id='.$form->getObject()->getId());?>" title="<?php echo __('Are you sure ?') ?>"><?php echo __('Delete');?></a>
            <?php endif;?>
            <input id="submit" type="submit" value="<?php echo __('Save');?>" />
          </td>
        </tr>
      </tfoot>
    </table>
  </form>

  <script type="text/javascript">
    $(document).ready(function () {

      $('form.qtiped_form').modal_screen();

    });

    $('input#multimedia_visible').change(function () {
      if (!($(this).attr('checked'))) {
        $('input#multimedia_publishable').attr('checked', false);
      }
    });

    $('input#multimedia_publishable').change(function () {
      if ($(this).attr('checked')) {
        $('input#multimedia_visible').attr('checked', true);
      }
    });

  </script>

</div>
