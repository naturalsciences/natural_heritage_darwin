<?php include_stylesheets_for_form($searchForm) ?>
<?php include_javascripts_for_form($searchForm) ?>
<div class="page">
  <h1><?php echo __('Choose a Loan');?></h1>
  <script language="javascript">
    $(document).ready(function () {
      $('.result_choose').live('click', result_choose);
    });
  </script>
  <div class="catalogue_filter">
    <?php echo form_tag('loan/search'.( ( isset($is_choose) && $is_choose ) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'catalogue_filter'));?>
    <div class="container">
      <table class="search" id="<?php echo ( ( isset($is_choose) && $is_choose ) ) ? 'search_and_choose' : 'search' ?>">
        <thead>
          <tr>
            <th><?php echo $searchForm['name']->renderLabel();?></th>
            <th><?php echo $searchForm['status']->renderLabel();?></th>
            <th><?php echo $searchForm['from_date']->renderLabel();?></th>
            <th><?php echo $searchForm['to_date']->renderLabel();?></th>
            <th><?php echo $searchForm['only_darwin']->renderLabel();?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $searchForm->renderHiddenFields();?><?php echo $searchForm['name'];?></td>
            <td><?php echo $searchForm['status'];?></td>
            <td><?php echo $searchForm['from_date'];?></td>
            <td><?php echo $searchForm['to_date'];?></td>
            <td><?php echo $searchForm['only_darwin'];?></td>
            <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search');?>" /></td>
          </tr>
        </tbody>
      </table>

      <div class="search_results">
        <div class="search_results_content">
        </div>
      </div>
      <?php if( (isset($user_allowed) && $user_allowed) || ($sf_user->getDbUserType() >= Users::ENCODER) ): ?>
        <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for($searchForm['table']->getValue().'/new') ?>"><?php echo __('New Loan');?></a></div>
      <?php endif ; ?>
    </div>
    </form>
    <script>
      $(document).ready(function () {
        $('.catalogue_filter').choose_form({});
        $(".new_link").click( function()
        {
          url = $(this).find('a').attr('href'),
            data= $('.search_form').serialize(),
            reg=new RegExp("(<?php echo $searchForm->getName() ; ?>)", "g");
          open(url+'?'+data.replace(reg,'<?php echo $searchForm['table']->getValue() ; ?>'));
          return false;
        });
      });
    </script>
  </div>
</div>
