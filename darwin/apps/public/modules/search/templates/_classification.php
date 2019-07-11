            <?php if(isset($common_name['taxonomy'][$spec->getTaxonRef()])) : ?>
            <?php $first=true ; ?>
            <?php foreach($common_name['taxonomy'][$spec->getTaxonRef()]['community'] as $community => $name) : ?>
              <?php if($first) : ?>
              <tr>
                <td rowspan="<?php echo count($common_name['taxonomy'][$spec->getTaxonRef()]['community']); ?>" class="top_aligned"><?php echo __('Taxonomy') ; ?></td>
                <?php $first=false ; ?>
              <?php endif ; ?>
              <td><?php echo $community ?></td>
              <td><?php echo $name ; ?></td>
              </tr>
              <?php endforeach ; ?>
            <?php endif ; ?>

            <?php if(isset($common_name['chronostratigraphy'][$spec->getTaxonRef()])) : ?>
            <?php $first=true ; ?>
            <?php foreach($common_name['chronostratigraphy'][$spec->getChronoRef()]['community'] as $community => $name) : ?>
            <?php if($first) : ?>
              <tr>
                <td rowspan="<?php echo count($common_name['chronostratigraphy'][$spec->getChronoRef()]['community']); ?>" class="top_aligned"><?php echo __('Chronostratigraphy') ; ?></td>
                <?php $first=false ; ?>
              <?php endif ; ?>
              <td><?php echo $community ?></td>
              <td><?php echo $name; ?></td>
              </tr>
              <?php endforeach ; ?>
            <?php endif ; ?>

            <?php if(isset($common_name['lithology'][$spec->getTaxonRef()])) : ?>
            <?php $first=true ; ?>
            <?php foreach($common_name['lithology'][$spec->getLithologyRef()]['community'] as $community => $name) : ?>
            <?php if($first) : ?>
              <tr>
                <td rowspan="<?php echo count($common_name['lithology'][$spec->getLithologyRef()]['community']); ?>" class="top_aligned"><?php echo __('Lithology') ; ?></td>
                <?php $first=false ; ?>
              <?php endif ; ?>
              <td><?php echo $community ?></td>
              <td><?php echo $name ; ?></td>
              </tr>
              <?php endforeach ; ?>
            <?php endif ; ?>

            <?php if(isset($common_name['lithostratigraphy'][$spec->getTaxonRef()])) : ?>
            <?php $first=true ; ?>
            <?php foreach($common_name['lithostratigraphy'][$spec->getLithoRef()]['community'] as $community => $name) : ?>
            <?php if($first) : ?>
              <tr>
                <td rowspan="<?php echo count($common_name['lithostratigraphy'][$spec->getLithoRef()]['community']); ?>" class="top_aligned"><?php echo __('Lithostratigraphy') ; ?></td>
                <?php $first=false ; ?>
              <?php endif ; ?>
              <td><?php echo $community ?></td>
              <td><?php echo $name ; ?></td>
              </tr>
              <?php endforeach ; ?>
            <?php endif ; ?>

            <?php if(isset($common_name['mineralogy'][$spec->getTaxonRef()])) : ?>
            <?php $first=true ; ?>
            <?php foreach($common_name['mineralogy'][$spec->getMineralRef()]['community'] as $community => $name) : ?>
            <?php if($first) : ?>
              <tr>
                <td rowspan="<?php echo count($common_name['mineralogy'][$spec->getMineralRef()]['community']); ?>" class="top_aligned"><?php echo __('Mineralogy') ; ?></td>
                <?php $first=false ; ?>
              <?php endif ; ?>
              <td><?php echo $community ?></td>
              <td><?php echo $name ; ?></td>
              </tr>
              <?php endforeach ; ?>
            <?php endif ; ?>
