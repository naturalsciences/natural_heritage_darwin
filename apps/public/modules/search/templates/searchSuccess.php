<?php slot('title', __('Search results'));  ?>         

<div class="encoding">
  <?php include_stylesheets_for_form($form) ?>
  <?php include_javascripts_for_form($form) ?>
    
  <div class="page" id="search_div">
    <h1 id="title"><?php echo __('Search Results');?></h1>    
    <?php if(isset($search) && $search->count() != 0):?> 
      <?php include_partial('result_cols', array('columns' => $columns, 'field_to_show' => $field_to_show));?>       
    <?php endif ; ?>
    <?php echo form_tag('search/search', array('class'=>'publicsearch_form','id'=>'specimen_filter'));?>
      <ul id="intro" class="hidden">
        <?php 
          // Render all the form fields as hidden input if possible. if the value is an array or and object render them as usual
          foreach($form as $row)
          { 
          $w = new sfWidgetFormInputHidden();
          $attributes = $form->getWidget($row->getName())->getAttributes();
          if(is_string($row->getValue()) || is_null($row->getValue()))
            echo '<li>'.$w->render( $form->getWidgetSchema()->generateName($row->getName()),$row->getValue(),$attributes).'</li>';
          else
            echo '<li>'.$row.'</li>';
        }?>
      </ul>
      <div class="search_results">
        <div class="search_results_content">
          <?php include_partial('searchSuccess',
                                array('search' => $search,
                                      'form' => $form, 
                                      'field_to_show' => $field_to_show,
                                      'pagerLayout' => $pagerLayout,
                                      'common_names' => $common_names,
                                      'columns' => $columns,
                                      //ftheeten 2018 10 29
                                      'codes'=> $codes,
                                     )
                               ); ?>
        </div>
      </div>
      <script  type="text/javascript">
        $(document).ready(function () {

          $("#criteria_butt").click(function(){
            $('#specimen_filter').attr('action','<?php echo url_for('search/search?criteria=1');?>').submit();
          });
        });
      </script>
      <div class="check_right" id="save_button"> 
        <input type="button" id="criteria_butt" value="<?php echo __('Back to criteria'); ?>">    
      </div> 
    </form>
  </div>
</div>  

