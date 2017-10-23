<ul class="bib_view">
  <?php foreach($Biblios as $bib):?>
    <li>
      <a href="<?php echo url_for('bibliography/view?id='.$bib->getBibliographyRef()) ; ?>"><?php echo $bib->Bibliography->getTitle(); ?></a>
    </li>
  <?php endforeach;?>
</li>
