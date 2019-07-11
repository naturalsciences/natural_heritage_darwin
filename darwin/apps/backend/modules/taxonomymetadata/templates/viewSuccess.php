<?php include_stylesheets_for_form($searchForm) ?>
<?php include_javascripts_for_form($searchForm) ?>

<div class="page">
    <h1><?php echo __('View Taxonomic metadata');?></h1>
     <div class="table_view">
            <table class="classifications_edit">
                <tbody>
                    <tr>
                        <th><?php echo $form['taxonomy_name']->renderLabel() ?></th>
                        <td>
                          <?php echo $taxonomy->getTaxonomyName(ESC_RAW); ?>
                          <input type="hidden" class="col_check_metadata_ref" value="<?php echo $taxonomy->getId(); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $form['definition']->renderLabel() ?></th>
                        <td>
                          <?php echo $taxonomy->getDefinition(); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $form['is_reference_taxonomy']->renderLabel() ?></th>
                        <td>
                          <?php echo ($taxonomy->getIsReferenceTaxonomy())? 'true' : 'false'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $form['source']->renderLabel() ?></th>
                        <td>
                          <?php echo $taxonomy->getSource(); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $form['creation_date']->renderLabel() ?></th>
                        <td>
                          <?php echo $taxonomy->getCreationDateMasked(ESC_RAW); ?>
                        </td>
                    </tr>
                     <tr>
                        <th><?php echo $form['url_website']->renderLabel() ?></th>
                        <td>
                          <?php echo $taxonomy->getUrlWebsite(); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $form['url_webservice']->renderLabel() ?></th>
                        <td>
                          <?php echo $taxonomy->getUrlWebservice(); ?>
                        </td>
                    </tr>
                    <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
                    <tr><td colspan="2"><?php echo image_tag('edit.png');?> <?php echo link_to(__('Edit this item'),'taxonomymetadata/edit?id='.$taxonomy->getId());?></td></tr>
                    <?php endif;?>
                 </tbody>
            </table>
            
     </div> 
      <h1><?php echo __('Choose a Taxon');?></h1>
<script language="javascript">
$(document).ready(function () {
    $('.result_choose').live('click', result_choose);
});

</script>
 <div class="table_view">
    <?php include_partial('catalogue/chooseItem', array('searchForm' => $searchForm,'is_choose' => true)) ?>
</div>
</div>