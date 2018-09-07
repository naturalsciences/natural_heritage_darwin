
<h1>Bienvenue dans notre base de données des collections de l’ Institut</h1>

<p>
L'Institut Royal des Sciences naturelles abrite une collection précieuse de matériel et documents zoologiques, 
anthropologiques, paléontologiques, minéralogiques et géologiques. Les fameux iguanodons de Bernissart, 
ambassadeurs de l'Institut scientifique belge à Bruxelles, font partie d'une collection d'histoire naturelle,
qui est actuellement estimée à plus de 37 millions de spécimens.
</p>
<br />
<p>
L'origine de la collection actuelle remonte loin dans l'histoire.
La collection est issue de la collection d'histoire naturelle de Charles de Lorraine, gouverneur des Pays-Bas (1712-1780),
qui faisait partie du matériel pédagogique de l'École centrale de la Ville de Bruxelles.
Après l'indépendance de la Belgique, la collection fut donnée par la ville au gouvernement belge et est devenue une partie autonome du «Musée royal d'Histoire naturelle" en 1846,
plus connue depuis 1948 comme l'Institut royal des Sciences naturelles.
Grâce aux chercheurs et à leur personnel, tant belges qu’étrangers, les dons et les achats des collections n’ont cessé de s’accroître jusqu'à nos jours.
</p>
<br />
<p>
L'application DaRWIN (darwin.naturalsciences.be) nous montre une facette importante des collections en terme de taille et de diversité.
Aujourd'hui, notre base de données contient des informations sur plus de 2.637.600 specimens (540.000 enregistrements) stockés dans les conservatoires de l'Institut.
Ce nombre augmente chaque jour grâce aux efforts de la direction du patrimoine, des conservateurs et de leurs adjoints, qui sont responsables de la conservation des échantillons et des informations archivées.
Notre base de données en ligne contient des informations sur les collections de vertébrés, invertébrés, insectes, paléontologie, géoogie et minéralogie.
</p>
<br />
<p>
Les données de la collection anthropologique sont conservées dans un système parallèle: MARS (Multimedia Archaeological Research System)qui peut être accédée via le lien suivant: <a href="http://www.sciencesnaturelles.be/mars">www.sciencesnaturelles.be/mars</a>.
Le service géologique de Belgique et le Département de la Gestion des écosystèmes marins fournissent leurs données via d'autres systèmes.
Plus d'information sur ces départements peu être obtenue en consultant leurs pages Web: <a href="https://www.naturalsciences.be/fr/science/do/25/scientific-research/research-programmes/94" target="_pop">www.naturalsciences.be/fr/science/do/25/scientific-research/research-programmes/94</a> et <a href="http://odnature.naturalsciences.be/home/" target="_pop">odnature.naturalsciences.be/home/</a>
</p>
<br />
<p>
La pierre angulaire de la base de données DARWIN est le spécimen et les informations relatives à son origine et son statut.
Bien que les données sur le statut des spécimens suivent la réglementation en vigueur du <a href="http://iczn.org/" target="_pop">Code international de nomenclature zoologique (CINZ) </a>,
vous y trouverez tout autre statut non reconnu par le CINZ (par exemple topotype) fournis en tant que complément d'information sur le ou les échantillon (s) en question.
</p>
<br />
<p>Nous vous souhaitons une agréable visite virtuelle parmi nos collections!</p>
<br />

<h2>Découvrez quelques spécimens au hasard :</h2>
<p>
  <ul class="rand_spec">
    <?php foreach($specimens as $spec):?>
      <li><?php echo link_to($spec->getAggregatedName(),'search/view?id='.$spec->getId());?></li>
    <?php endforeach;?>
  </ul>
</p>


<p>
  <a href="<?php echo __('http://www.sciencesnaturelles.be');?>">
            <?php echo image_tag('public/rbins_logo.png', 'class=logo_center_align');?><br />
          </a>
</p>
