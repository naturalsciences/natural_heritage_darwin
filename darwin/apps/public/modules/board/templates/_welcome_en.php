
<h1>Welcome to the RBINS-collection database</h1>

<br />
<p>
The Royal Belgian Institute of Natural Sciences houses a precious collection of zoological, anthropological, paleontological, mineralogical and geological materials and data. The renowned Iguanodons from Bernissart, ambassadors of the Belgian science institute in Brussels, represent a natural history collection currently estimated to hold over 37 million specimens. 
</p>
<br />
<p>
The roots of the present day collection reach far back in history. It evolved from the Natural History collection of Karel of Lotharingen, governor of The Netherlands (1712-1780) and was part of didactic materials owned by the Central School of the City of Brussels. After the independence of Belgium, the City of Brussels donated the collection to the Belgian Government and became part of the autonomous “Royal Natural History Museum” in 1846, known as the “Royal Belgian Institute of Natural Sciences” since 1948. Fieldwork by researchers and collaborators, in Belgium and abroad, donations and purchases have been 
expanding the assets ever since. 
</p>
<br />
<p>
The darwin website is the main gate to glimpse the extent and diversity of the collections. Today, the darwin database manages information on about 350.000 specimens stored in the institute’s depositories. This number rises on a daily basis thanks to the continued efforts of curators and their adjuncts that are responsible for maintaining the stored specimens and information. Our online database provides information about the collections of the Vertebrates, Invertebrates and Entomology. The application will soon be expanded with paleontological data. 
</p>
<br />
<p>
Information on the Anthropological collection is maintained on a parallel system: mars (Multimedia Archaeological Research System). This data can be consulted following: http://www.naturalsciences.be/mars. The Department of Geology and the Department of Marine Ecosystems provide information on different systems. More information on these departments can be found on <a href="http://www.sciencesnaturelles.be/institute/structure/geology/gsb_website" target="_pop">www.sciencesnaturelles.be/institute/structure/geology/gsb_website</a> And <a href="http://www.mumm.ac.be" target="_pop">www.mumm.ac.be</a>
</p>
<br />
<p>
The corner stone of the darwin database is the specimen and the information about its origin and its status. Although the status of the specimens follow the current regulations of the <a href="http://iczn.org/" target="_pop">International Code on Zoological Nomenclature</a> other status specifications not treated by the ICZN regulations (eg. topotype) have been maintained as supplementary information about the specimen(s) in question.      
</p>
<br />
<p>Enjoy your virtual visit through our collections!</p>
<br />

<h2>Discover some random specimens :</h2>
<p>
  <ul class="rand_spec">
    <?php foreach($specimens as $spec):?>
      <li><?php echo link_to($spec->getAggregatedName(),'search/view?id='.$spec->getId());?></li>
    <?php endforeach;?>
  </ul>
</p>

<p>
  <a href="<?php echo __('http://www.naturalsciences.be');?>">
            <?php echo image_tag('public/rbins_logo.png', 'class=logo_center_align');?><br />
          </a>
</p>