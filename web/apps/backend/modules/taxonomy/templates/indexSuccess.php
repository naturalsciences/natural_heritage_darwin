<?php slot('title', __('Search Taxonomic unit'));  ?>        

<div class="page">
  <!--JMHerpers 2018 03 14-->
  <h1><?php echo __('Taxon Search');?></h1>
  <?php include_partial('catalogue/chooseItem', array('searchForm' => $searchForm, 'is_choose' => false)) ?>
  
  <script>
    //ftheeten 2018 04 10
    (function($){ //create closure so we can safely use $ as alias for jQuery

      $.urlParam = function(name){
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            if (results==null){
               return null;
            }
            else{
               return decodeURI(results[1]) || 0;
            }
        }  
      $(document).ready(function(){
            var ig_num=$.urlParam('ig_num');
            alert(ig_num);
            $.("#searchCatalogue_ig_number").text(ig_num);
       
      });

    })(jQuery);
</script>

</div>
