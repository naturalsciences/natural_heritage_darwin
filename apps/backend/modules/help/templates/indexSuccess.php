<?php slot('title',__("Darwin Help"));?>
<div class="page">
  <h1><?php echo __('Darwin Guide :');?></h1>

  <p><a href="/help/DaRWIN_FR_2014.pdf">Manuel d'utilisation</a></p><br />
      
  <h1><?php echo __('ABCD Templates* :');?></h1>
  <p>*Only available in english</p><br />
  
  <p><a href="/help/How to import a XML file in Darwin.pdf">How to import a XML file in Darwin?</a></p><br />

  <h1><?php echo __('Loans Manual :');?></h1>
  <?php if (isset($help_language) && $help_language === 'nl'): ?>
    <p><a href="/help/Gebruikershandleiding DaRWIN Loans NL.pdf">Gebruikershandleiding DaRWIN Loans NL.pdf</a></p><br />
  <?php else: ?>
    <p><a href="/help/Manual DaRWIN Loans EN.pdf">Manual DaRWIN Loans EN.pdf</a></p><br />
  <?php endif; ?>
  <!-- Add table with templates and user manuals -->
  <table class="results ">
    <thead>
      <tr>
        <th>Section</th>
        <th>Templates</th>
        <th>User manuals</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>General</td>
        <td>
          <a href="/help/ABCDImport2DaRWIN_General.xlsm">ABCDImport2DaRWIN_General.xlsm</a><br />
          <a href="/help/ABCDImport2DaRWIN_General_taxonFullName.xlsm">ABCDImport2DaRWIN_General_taxonFullName.xlsm</a>
        </td>
        <td><a href="/help/ABCDImport2DaRWIN_General_usermanual_taxonFullName.pdf">ABCDImport2DaRWIN_General_usermanual.pdf</a></td>   
      </tr>
      <tr>
        <td>DNA / JEMU</td>
        <td>
          <a href="/help/ABCDImport2DaRWIN_DNA.xlsm">ABCDImport2DaRWIN_DNA.xlsm</a><br />
          <a href="/help/ABCDImport2DaRWIN_DNA_taxonFullName.xlsm">ABCDImport2DaRWIN_DNA_taxonFullName.xlsm</a>
        </td>
        <td><a href="/help/ABCDImport2DaRWIN_DNA_usermanual.pdf">ABCDImport2DaRWIN_DNA_usermanual.pdf</a></td>   
      </tr>
      <tr>
        <td>Invertebrates</td>
        <td>
          <a href="/help/ABCDImport2DaRWIN_InvEntomo.xlsm">ABCDImport2DaRWIN_InvEntomo.xlsm</a><br />
          <a href="/help/ABCDImport2DaRWIN_InvEntomo_taxonFullName.xlsm">ABCDImport2DaRWIN_InvEntomo_taxonFullName.xlsm</a>
        </td>
        <td><a href="/help/ABCDImport2DaRWIN_InvEntomo_usermanual.pdf">ABCDImport2DaRWIN_InvEntomo_usermanual.pdf</a></td>   
      </tr>
      <tr>
        <td>Entomology</td>
        <td>
          <a href="/help/ABCDImport2DaRWIN_Entomo.xlsm">ABCDImport2DaRWIN_Entomo.xlsm</a><br />
          <a href="/help/ABCDImport2DaRWIN_Entomo_taxonFullName.xlsm">ABCDImport2DaRWIN_Entomo_taxonFullName.xlsm</a>
        </td>
        <td><a href="/help/ABCDImport2DaRWIN_Entomo_usermanual.pdf">ABCDImport2DaRWIN_Entomo_usermanual.pdf</a></td>   
      </tr>
      <tr>
        <td>Vertebrates</td>
        <td>
          <a href="/help/ABCDImport2DaRWIN_Vert.xlsm">ABCDImport2DaRWIN_Vert.xlsm</a><br />
          <a href="/help/ABCDImport2DaRWIN_Vert_taxonFullName.xlsm">ABCDImport2DaRWIN_Vert_taxonFullName.xlsm</a>
        </td>
        <td><a href="/help/ABCDImport2DaRWIN_Vert_usermanual.pdf">ABCDImport2DaRWIN_Vert_usermanual.pdf</a></td>   
      </tr>
      <tr>
        <td>Paleontology</td>
        <td>
          <a href="/help/ABCDImport2Darwin_Paleontology.xlsm">ABCDImport2DaRWIN_Paleontology.xlsm</a><br />
          <a href="/help/ABCDImport2Darwin_Paleontology_taxonFullName.xlsm">ABCDImport2DaRWIN_Paleontology_taxonFullName.xlsm</a>
        </td>
        <td><a href="/help/ABCDImport2DaRWIN_Paleontology_usermanual.pdf">ABCDImport2DaRWIN_Paleontology_usermanual.pdf</a></td>   
      </tr>
      <tr>
        <td>Geology</td>
        <td>
          <a href="/help/ABCDImport2DaRWIN_Litho.xlsm">ABCDImport2DaRWIN_Litho.xlsm</a><br />
          <a href="/help/ABCDImport2DaRWIN_Minerals.xlsm">ABCDImport2DaRWIN_Mineralo.xlsm</a><br />
          <a href="/help/Template_Litho_localities.xlsx">Template_Litho_localities.xlsx</a>
        </td>
        <td><a href="/help/ABCDImport2DaRWIN_Geol_usermanual.pdf">ABCDImport2DaRWIN_Geol_usermanual.pdf</a></td>   
      </tr>
    </tbody>
  </table>
 
  <br />
 
  <h1><?php echo __('Additional tools for import :');?></h1>
 
  <table class="results ">
    <thead>
      <tr>
        <th>Taxonomy import</th>
        <th>Taxon full name checker</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <p>Template: </p><a href="/help/TaxonomyImport.xlsm">TaxonomyImport.xlsm</a><br />
          <p>User manual: </p><a href="/help/TaxonomyImport2DaRWIN_usermanual.pdf">TaxonomyImport2DaRWIN_usermanual.pdf</a>
        </td>
        <td><a href="/help/Check_taxonFullName.xlsm">Check_taxonFullName.xlsm</a></td>   
      </tr>
    </tbody>
  </table>

  <br />

</div>
