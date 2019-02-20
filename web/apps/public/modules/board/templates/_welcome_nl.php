
<h1>Welkom in onze KBIN-collectie databank</h1>

<p>
Het Koninklijke Belgisch Instituut voor Natuurwetenschappen biedt onderdak aan een waardevolle collectie van zoologisch, antropologisch, paleontologisch, mineralogisch en geologisch materiaal en data. De beroemde Iguanodons van Bernissart, ambassadeurs van het Belgische Wetenschappelijke Instituut in Brussel, maken deel uit van een natuurhistorische collectie die momenteel geraamd  wordt op meer dan 37 miljoen exemplaren.
</p>
<br />
<p>
De wortels van de huidige collectie gaan ver terug in de geschiedenis. De collecie is voortgekomen uit de Natuurhistorische verzameling van Karel van Lotharingen, gouverneur van Nederland (1712-1780) en maakte deel uit van het didactisch materiaal van de Centrale School van de Stad Brussel. Na de onafhankelijkheid van België, schonk de stad Brussel de collectie aan de Belgische regering en werd het een onderdeel van het autonome "Koninklijke Natuurhistorisch Museum" in 1846, beter bekend sinds 1948 als het Koninklijke Belgisch Instituut voor Natuurwetenschappen. Dankzij veldwerk door onderzoekers en medewerkers, zowel in België als in het buitenland, schenkingen en aankopen groeien de collecties steeds verder aan.
</p>
<br />
<p>
De DaRWIN website (darwin.naturalsciences.be) licht een belangrijke tip van de sluier op wat betreft de omvang en diversiteit van de bezittingen. Vandaag bevat onze Darwin databank al informatie over meer dan 2.637.600 specimens (540.000 records) opgeslagen in de depots van het Instituut. Dit aantal stijgt nog dagelijks dankzij de aanhoudende inspanningen van het patrimonium directie, de curatoren en hun adjuncten die verantwoordelijk zijn voor het behoud van de opgeslagen monsters en archiefinformatie. Onze online databank bevat informatie over de collecties van de gewervelde dieren, ongewervelden, de insecten, paleontologie, geologie en mineralogie.
</p>
<br />
<p>
Informatie over de antropologische collectie wordt bijgehouden op een parallel systeem: MARS (Multimedia Archaeological Research System). Deze gegevens kunnen worden geraadpleegd op volgend webadres: <a href="http://www.natuurwetenschappen.be/mars" target="_pop">www.natuurwetenschappen.be/mars</a>. De Belgische Geologische dienst en de afdeling Beheer van het Mariene Ecosystemen verstrekken hun gegevens via andere systemen. Meer informatie over deze departementen kan u vinden op hun respectievelijke webpagina’s: <a href="https://www.naturalsciences.be/nl/science/do/25/scientific-research/research-programmes/94" target="_pop">www.naturalsciences.be/nl/science/do/25/scientific-research/research-programmes/94</a> en <a href="http://odnature.naturalsciences.be/home/" target="_pop">odnature.naturalsciences.be/home/</a>
</p>
<br />
<p>
De hoeksteen van de DARWIN database is het specimen en de informatie over de herkomst en de status ervan. Hoewel de status van de specimens de huidige regelgeving van de  <a href="http://iczn.org/" target="_pop">International Code inzake zoölogische nomenclatuur</a> volgt, worden ook andere status specificaties niet opgenomen door de ICZN (bijv. topotype) gebruikt als aanvullende informatie over de monster (s) in kwestie.
</p>
<br />
<p>Wij wensen u een prettig bezoek doorheen onze virtuele collecties!</p>
<br />

<h2>Ontdek enkele willekeurige exemplaren :</h2>
<p>
  <ul class="rand_spec">
    <?php foreach($specimens as $spec):?>
      <li><?php echo link_to($spec->getAggregatedName(),'search/view?id='.$spec->getId());?></li>
    <?php endforeach;?>
  </ul>
</p>

<p>
  <a href="<?php echo __('http://www.natuurwetenschappen.be');?>">
            <?php echo image_tag('public/rbins_logo.png', 'class=logo_center_align');?><br />
          </a>
</p>
