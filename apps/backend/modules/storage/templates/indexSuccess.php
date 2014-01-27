<?php slot('title',__("Storage View"));?>
<div class="page">
  <h1><?php echo __('Storage View :');?></h1>
    <ul class="conserv_list">
    <?php foreach($to_query as $type=>$name):?>
      <li>
        <strong><?php echo __($elements[$type]); ?></strong> :
        <?php if($type == $previousEl):?>
          <?php echo $name == ''? "<i>emtpy</i>": $name;?>
        <?php else:?>
          <?php echo link_to( $name == ''? "<i>emtpy</i>": $name ,$to_query2->getRawValue()[$type]);?>
        <?php endif;?>
      </li>
    <?php endforeach;?>
  </ul>

<br /><br />


  <?php if(isset($results)):?>
    <h1><?php echo __('Choose a '. $elements[$currentEl]);?> </h1>
    <?php include_partial('choice', array('link'=> true, 'results'=>$results));?>
  <?php else:?>
    <div>
      <div class="conserv_view_box">
        <h3><?php echo __('Taxonomy');?> :</h3>
        <?php include_partial('choice', array('link'=> false, 'results'=> $results_array['taxon_name']));?>
      </div>
      <div class="conserv_view_box">
        <h3><?php echo __('Container');?> :</h3>
        <?php include_partial('choice', array('link'=> false, 'results'=> $results_array['container']));?>
      </div>
      <div class="conserv_view_box">
        <h3><?php echo __('Lithology');?> :</h3>
        <?php include_partial('choice', array('link'=> false, 'results'=> $results_array['lithology_name']));?>
      </div>
    </div>
  <?php endif;?>
</div>

<script type="text/javascript">
  $(document).ready(function (){
    $('.link_to_search').click(function (event){
      event.preventDefault();
      search_data =jQuery.parseJSON( decodeBase64($(this).attr('data-search')) );
      postToUrl($(this).attr('href'), search_data);
    });
  });
</script>
