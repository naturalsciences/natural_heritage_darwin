<table  class="catalogue_table_view">
  
  <thead style="<?php echo ($Codes->count()?'':'display: none;');?>">
    <tr>
      <th>
        <?php echo __('Category'); ?>
      </th>
      <th>
        <?php echo __('Code') ; ?>
      </th>
      <th>
        <?php echo __('Barcode') ; ?>
      </th>
    </tr>
  </thead>
  <?php $i=0;?>
  <?php foreach($Codes as $code):?>
  <tr>
    <td><?php echo $code->getCodeCategory();?></td>
    <td>
      <?php echo($code->getCodePrefix().$code->getCodePrefixSeparator().$code->getCode().$code->getCodeSuffixSeparator().$code->getCodeSuffix());?>
    </td>
    <td><div style='width:20mm;height:20mm;' id="bcTarget_<?php echo($i);?>" class="bcTarget"></div><input id="val_for_bcTarget_<?php echo($i);?>" type="hidden" value="<?php echo($code->getCodePrefix().$code->getCodePrefixSeparator().$code->getCode().$code->getCodeSuffixSeparator().$code->getCodeSuffix());?>"></input></td>
    <?php $i++; ?>
  </tr>
  <?php endforeach ; ?>
   <tr>
    <td><?php echo __("Original and stable id");?></td>
    <td><?php echo   $stable?$stable->getOriginalId():"";?></td>
  </tr>
  <tr>
    <td><?php echo __("UUID");?></td>
    <td><?php echo $stable? $stable->getUuid():"";?></td>
  </tr>
  <?php $import_ref=$spec->getImportRef(); if($import_ref!==null):?>
  <tr>
		<td><b>Nr. import</td>
		<td><?php print($import_ref); ?></td>
	</tr>
 <?php endif;?>
  </tbody>
 </table>
 <?php if($specCode->getValidLabel()!==null||strlen($specCode->getLabelCreatedOn())>0||strlen($specCode->getLabelCreatedBy())>0):?>
 <br/>

  <br/>
 <table class="catalogue_table_view">
 <thead>
    <tr>
        <th>
        <?php echo __("Valid label");?>
        </th>
         <th>
            <?php echo __("Label created on");?>
        </th>
         <th>
            <?php echo __("Label created by");?>
        </th>
    </tr>      
  </thead>
  <tbody>
  <tr>
     <td>
        <?php if($specCode->getValidLabel()===TRUE):?>
            <?php echo __("Yes");?>
        <?php elseif($specCode->getValidLabel()===FALSE):?>
            <?php echo __("No");?>
        <?php endif;?>
      </td>
     <td>
        <?php echo $specCode->getLabelCreatedOn();?>
      </td>
     <td>
        <?php echo $specCode->getLabelCreatedBy();?>
      </td>
  </tr>
 </tbody>
 </table>
  <?php endif;?>
  <script  type="text/javascript">
//ftheeten 2017 08 10
$(document).ready(function () {

        function setBarcodes()
        {
            $( ".bcTarget" ).each(function() {
          
                var idtmp=$( this ).attr('id');
                
                var idx=idtmp.replace(/bcTarget_/g,'');
              
                var idVal='val_for_bcTarget_'+idx;
                
                var val=$("#"+idVal).val();
                
                $("#"+idtmp).barcode(val, "datamatrix",{moduleSize:2,showHRI:false});
            });
        }
        
        setBarcodes();
});
</script>
   

