<?php slot('title', __('Edit Preferences widgets'));  ?>
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<div class="page edition">
 <h1><?php echo __('My Preferences');?></h1>
  <?php echo form_tag('user/preferences');?>
    <table class="user_table">
      <thead>
        <tr>
          <th colspan="2"><?php echo __("Search in Specimens");?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>
          <?php echo $form['search_cols_specimen']->renderLabel();?>
            <?php echo help_ico($form['search_cols_specimen']->renderHelp(),$sf_user);?>
          </th>
          <td><div class="search_cols_specimen" ><table><?php echo $form['search_cols_specimen'];?></table></div></td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th colspan="2"><?php echo __("Board Widgets");?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>
            <?php echo $form['board_spec_rec_pp']->renderLabel();?>
            <?php echo help_ico($form['board_spec_rec_pp']->renderHelp(),$sf_user);?>
          </th>
          <td><?php echo $form['board_spec_rec_pp'];?></td>
        </tr>
        <tr>
          <th>
            <?php echo $form['board_search_rec_pp']->renderLabel();?>
            <?php echo help_ico($form['board_search_rec_pp']->renderHelp(),$sf_user);?>
          </th>
          <td><?php echo $form['board_search_rec_pp'];?></td>
        </tr>
        <tr>
          <th>
            <?php echo $form['default_search_rec_pp']->renderLabel();?>
            <?php echo help_ico($form['default_search_rec_pp']->renderHelp(),$sf_user);?>
          </th>
          <td><?php echo $form['default_search_rec_pp'];?></td>
        </tr>

      </tbody>
      <thead>
        <tr>
          <th colspan="2"><?php echo __("Forms");?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>
            <?php echo $form['help_message_activated']->renderLabel();?>
            <?php echo help_ico($form['help_message_activated']->renderHelp(),$sf_user);?>
          </th>
          <td><?php echo $form['help_message_activated'];?></td>
        </tr>
        <tr>
      </tbody>
       <thead>
        <tr>
          <th colspan="2"><?php echo __("Search option");?></th>
        </tr>
      </thead>
      <!--<tbody>
        <tr>
          <th>
            <?php echo $form['exclude_prefix_in_searches']->renderLabel();?>
            <?php echo help_ico($form['exclude_prefix_in_searches']->renderHelp(),$sf_user);?>
          </th>
          <td><?php echo $form['exclude_prefix_in_searches'];?></td>
        </tr>
        <tr>
      </tbody>-->
      <tfoot>
        <tr>
          <td colspan='2'>
            <input type="submit" />
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
