<div>
  <form class="edition qtiped_form" method="post" id="collection_maintenance" action="<?php echo url_for('loanitem/maintenances?ids='.$sf_request->getParameter('ids'));?>">
      <h1><?php echo __('Add Maintenance :');?></h1>
    <?php include_stylesheets_for_form($form) ?>
    <?php include_javascripts_for_form($form) ?>
    
    <?php echo $form->renderGlobalErrors() ?>

    <table>
    <tbody>
      <tr>
            <th><?php echo $form['category']->renderLabel();?></th>
            <td>
              <?php echo $form['category']->renderError() ?>
              <?php echo $form['category'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['action_observation']->renderLabel();?></th>
            <td>
              <?php echo $form['action_observation']->renderError() ?>
              <?php echo $form['action_observation'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['modification_date_time']->renderLabel();?></th>
            <td>
              <?php echo $form['modification_date_time']->renderError() ?>
              <?php echo $form['modification_date_time'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['people_ref']->renderLabel();?></th>
            <td>
              <?php echo $form['people_ref']->renderError() ?>
              <?php echo $form['people_ref'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['description']->renderLabel();?></th>
            <td>
              <?php echo $form['description']->renderError() ?>
              <?php echo $form['description'];?>
            </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <input id="submit" type="submit" value="<?php echo __('Add Maintenance');?>" />
        </td>
      </tr>
    </tfoot>
   </table>
  <script  type="text/javascript">
  $(document).ready(function () {
    $('form.qtiped_form').modal_screen();


 $('.result_choose').live('click',function () 
     {
       el = $(this).closest('tr');
       if(!$("div.search_box':hidden").length)
       {
         $("#collection_maintenance_people_ref").val(getIdInClasses(el));
         $("#collection_maintenance_people_ref_name").val(el.find('.item_name').text()).show();
         $('#collection_maintenance_people_ref .reference_clear').show();
         $('div.search_box').slideUp();
       } else {
         $("#collection_maintenance_people_ref").val(getIdInClasses(el));
         $("#collection_maintenance_people_ref_name").val(el.find('.item_name').text()).show();
         $('#collection_maintenance_people_ref .contact_reference_clear').show();
         $('div.contact_search_box').slideUp();
       }
     });

    $('#collection_maintenance_people_ref_name').click( function() 
    {
      $('.search_results_content').empty() ;
    });


  });
  </script>
</form>

  <div class="search_box">
    <?php include_partial('people/searchForm', array('form' => new PeopleFormFilter(),'is_choose'=>true)) ?>
  </div>
