with 
gtu_continents as (
  select distinct
         gtu_ref,
         trim(unnest(string_to_array(lower(tag_value), ';'))) as gtu_continent
  from tag_groups 
  where sub_group_name_indexed like 'cont%' 
), 
gtu_infos as 
(
select distinct 
       gtu_ref,
       case
         when gtu_continent in ('africa', 'afrika', 'afrique', 'central africa', 'democratic republic of congo') then
           'Africa'
         when gtu_continent in ('amerika', 'america', 'amerique', 'amérique du nord', 'amérique', 'noord amerika', 'north america', 'united states') then
           'North America'
         when gtu_continent in ('amérique du sud', 'chili', 'south america', 'south-america', 'zuid-amerika', 'zuid- amerika') then
           'South America'
         when gtu_continent in ('antarctica', 'antarctique') then
           'Antarctica'
         when gtu_continent in ('asia', 'asie', 'asie centrale', 'east asia', 'est asia') then
           'Asia'
         when gtu_continent in ('australia', 'oceania') then
           'Oceania'
         when gtu_continent in ('europe', 'europa, l'' europe', 'europe centrale', 'europe orientale', 'north sea belgium/holland') then
	   'Europe'
         else
           gtu_continent
       end as continent
from gtu_continents
),
specimens_data as 
(
select case 
         when (collection_ref = 3 or collection_path like '/3/%') then
           'PALEO'
         when (collection_ref = 231 or collection_path like '/231/%') then
           'MIN'
         else
           'BIO'
       end as "Branch", 
       case
         when strpos(taxon_path, '/91/') != 0 and 
	     (collection_ref = 1 or collection_path like '/1/%') then
           'VZ'
        when (strpos(taxon_path, '/37/') != 0 or 
              strpos(taxon_path, '/61/') != 0 or 
              strpos(taxon_path, '/353/') != 0 or 
              strpos(taxon_path, '/184176/') != 0 or 
              strpos(taxon_path, '/177325/') != 0 or
              strpos(taxon_path, '/64/') != 0 or
              strpos(taxon_path, '/65/') != 0 or 
              strpos(taxon_path, '/177320/') != 0 or
              strpos(taxon_path, '/276/') != 0 or
	      strpos(taxon_path, '/109920/') != 0 or
	      strpos(taxon_path, '/30/') != 0 or
	      strpos(taxon_path, '/72/') != 0 or
	      strpos(taxon_path, '/75/') != 0 or 
	      strpos(taxon_path, '/206101/') != 0 or
	      strpos(taxon_path, '/44/') != 0 or
	      strpos(taxon_path, '/33/') != 0 or 
	      strpos(taxon_path, '/177319/') != 0 or
	      strpos(taxon_path, '/28/') != 0 or
	      strpos(taxon_path, '/79/') != 0 or
	      strpos(taxon_path, '/19/') != 0 or
	      strpos(taxon_path, '/81/') != 0 or
	      strpos(taxon_path, '/83/') != 0 or
	      strpos(taxon_path, '/90/') != 0  or
              (strpos(taxon_path, '/2/') != 0 and 
	       strpos(taxon_path, '/61/') = 0 and 
	       strpos(taxon_path, '/353/') = 0 and 
	       strpos(taxon_path, '/184176/') = 0 and 
	       strpos(taxon_path, '/177325/') = 0 and
	       strpos(taxon_path, '/64/') = 0 and
	       strpos(taxon_path, '/65/') = 0 and 
	       strpos(taxon_path, '/177320/') = 0 and
	       strpos(taxon_path, '/276/') = 0
	      )
	     ) and 
	     (collection_ref = 2 or collection_path like '/2/%') 
          then
           'IZ'
         when (
                strpos(taxon_path, '/4/') != 0 or
                strpos(taxon_path, '/12/') != 0 or 
                strpos(taxon_path, '/154425/') != 0 or 
                strpos(taxon_path, '/10/') != 0 or
                strpos(taxon_path, '/406/') != 0 or
                strpos(taxon_path, '/408/') != 0 or
                strpos(taxon_path, '/50/') != 0 or
                strpos(taxon_path, '/423/') != 0 or
                strpos(taxon_path, '/413/') != 0 or
                strpos(taxon_path, '/11/') != 0 or
	        strpos(taxon_path, '/47/') != 0 or 
	        strpos(taxon_path, '/106418/') != 0 or
                (
                  strpos(taxon_path, '/2/') != 0 and 
	          strpos(taxon_path, '/4/') = 0 and 
	          strpos(taxon_path, '/12/') = 0 and 
	          strpos(taxon_path, '/10/') = 0 and 
	          strpos(taxon_path, '/406/') = 0 and
	          strpos(taxon_path, '/408/') = 0 and
	          strpos(taxon_path, '/50/') = 0 and 
	          strpos(taxon_path, '/423/') = 0 and
	          strpos(taxon_path, '/413/') = 0 and 
	          strpos(taxon_path, '/11/') = 0 and
	          strpos(taxon_path, '/47/') = 0
                )
              ) and 
	     (collection_ref = 4 or collection_path like '/4/%') 
          then
           'Entomology'
         when strpos(chrono_path, '/11/') != 0 and
             (collection_ref = 3 or collection_path like '/3/%') then
           'Cenozoic'           
         when strpos(chrono_path, '/26/') != 0 and
             (collection_ref = 3 or collection_path like '/3/%') then
           'Mesozoic'           
         when strpos(chrono_path, '/5/') != 0 and
             (collection_ref = 3 or collection_path like '/3/%') then
           'Paleozoic'
         when (collection_ref = 200 or collection_path like '/200/%') then
           'Meteorites'
         when (collection_ref = 276) then
           'Gems'
         else
           'Other'
       end as "Department",
       case
         when strpos(taxon_path, '/110/') != 0 and 
	     (collection_ref = 1 or collection_path like '/1/%')  then
           'Amphibians'
         when strpos(taxon_path, '/111/') != 0 and 
	     (collection_ref = 1 or collection_path like '/1/%')  then
           'Birds'
         when (strpos(taxon_path, '/97/') != 0 or strpos(taxon_path, '/98/') != 0) and 
	     (collection_ref = 1 or collection_path like '/1/%')  then
           'Fishes'
         when strpos(taxon_path, '/113/') != 0 and 
	     (collection_ref = 1 or collection_path like '/1/%')  then
           'Mammals'
         when strpos(taxon_path, '/114/') != 0 and 
	     (collection_ref = 1 or collection_path like '/1/%')  then
           'Reptiles'
         when strpos(taxon_path, '/91/') != 0 and 
	     (collection_ref = 1 or collection_path like '/1/%')  then
           'Vertebrates'
         when strpos(taxon_path, '/37/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Annelida'
         when strpos(taxon_path, '/61/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Arthropoda - Branchiopoda'
         when (strpos(taxon_path, '/353/') != 0 or strpos(taxon_path, '/184176/') != 0 or strpos(taxon_path, '/177325/') != 0) and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Arthropoda - Decapoda'
         when strpos(taxon_path, '/64/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Arthropoda - Maxillopoda'
         when (strpos(taxon_path, '/65/') != 0 or strpos(taxon_path, '/177320/') != 0) and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Arthropoda - Ostracoda'
         when strpos(taxon_path, '/276/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Arthropoda - Peracarida'
	 when strpos(taxon_path, '/2/') != 0 and 
	     strpos(taxon_path, '/61/') = 0 and 
	     strpos(taxon_path, '/353/') = 0 and 
	     strpos(taxon_path, '/184176/') = 0 and 
	     strpos(taxon_path, '/177325/') = 0 and
	     strpos(taxon_path, '/64/') = 0 and
	     strpos(taxon_path, '/65/') = 0 and 
	     strpos(taxon_path, '/177320/') = 0 and
	     strpos(taxon_path, '/276/') = 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
	   'Arthropoda - Others'
         when (strpos(taxon_path, '/109920/') != 0 or strpos(taxon_path, '/30/') != 0) and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Bryozoa'
         when strpos(taxon_path, '/72/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Cnidaria'
         when (strpos(taxon_path, '/75/') != 0 or strpos(taxon_path, '/206101/') != 0) and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Echinodermata'
         when strpos(taxon_path, '/44/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Hemichordata'
         when (strpos(taxon_path, '/33/') != 0 or strpos(taxon_path, '/177319/') != 0 ) and 
	     (collection_ref = 2 or collection_path like '/2/%')then
           'Mollusca'
         when strpos(taxon_path, '/28/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Nematoda'
         when strpos(taxon_path, '/79/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Nemertea'
         when strpos(taxon_path, '/19/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Platyhelminthes'
         when strpos(taxon_path, '/81/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Porifera'
         when strpos(taxon_path, '/83/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Protozoans'
         when strpos(taxon_path, '/90/') != 0 and 
	     (collection_ref = 2 or collection_path like '/2/%') then
           'Tunicates'
         when strpos(taxon_path, '/4/') != 0  and 
	     (collection_ref = 4 or collection_path like '/4/%') then
	   'Insecta - Coleoptera'
         when (strpos(taxon_path, '/12/') != 0 or strpos(taxon_path, '/154425/') != 0) and 
	     (collection_ref = 4 or collection_path like '/4/%')then
           'Insecta - Diptera'
         when strpos(taxon_path, '/10/') != 0  and 
	     (collection_ref = 4 or collection_path like '/4/%') then
	   'Insecta - Hemiptera'
         when strpos(taxon_path, '/406/') != 0  and 
	     (collection_ref = 4 or collection_path like '/4/%') then
	   'Insecta - Hymenoptera'
         when strpos(taxon_path, '/408/') != 0  and 
	     (collection_ref = 4 or collection_path like '/4/%') then
	   'Insecta - Lepidoptera'
         when strpos(taxon_path, '/50/') != 0  and 
	     (collection_ref = 4 or collection_path like '/4/%') then
	   'Insecta - Myriapoda'
         when strpos(taxon_path, '/423/') != 0  and 
	     (collection_ref = 4 or collection_path like '/4/%') then
	   'Insecta - Odonata'
         when strpos(taxon_path, '/413/') != 0  and 
	     (collection_ref = 4 or collection_path like '/4/%') then
	   'Insecta - Orthoptera'
         when strpos(taxon_path, '/11/') != 0  and 
	     (collection_ref = 4 or collection_path like '/4/%') then
	   'Insecta - Trichoptera'
         when (strpos(taxon_path, '/47/') != 0 or strpos(taxon_path, '/106418/') != 0) and 
	     (collection_ref = 4 or collection_path like '/4/%')then
           'Arthropoda - Chelicerata'
	 when strpos(taxon_path, '/2/') != 0 and 
	      strpos(taxon_path, '/4/') = 0 and 
	      strpos(taxon_path, '/12/') = 0 and 
	      strpos(taxon_path, '/10/') = 0 and 
	      strpos(taxon_path, '/406/') = 0 and
	      strpos(taxon_path, '/408/') = 0 and
	      strpos(taxon_path, '/50/') = 0 and 
	      strpos(taxon_path, '/423/') = 0 and
	      strpos(taxon_path, '/413/') = 0 and 
	      strpos(taxon_path, '/11/') = 0 and 
	      strpos(taxon_path, '/47/') = 0 and
	      (collection_ref = 4 or collection_path like '/4/%') then
	   'Arthropoda - Others'
	 when (strpos(lithology_path, '/29/') != 0 or lithology_ref = 29) and
	   (collection_ref = 200 or collection_path like '/200/%') then
	   'Achondrites'
	 when (strpos(lithology_path, '/28/') != 0 or lithology_ref = 28) and
	   (collection_ref = 200 or collection_path like '/200/%') then
	   'Chondrites'
	 when (strpos(lithology_path, '/169/') != 0 or lithology_ref = 169) and
	   (collection_ref = 200 or collection_path like '/200/%') then
	   'Irons'
	 when (strpos(lithology_path, '/168/') != 0 or lithology_ref = 168) and
	   (collection_ref = 200 or collection_path like '/200/%') then
	   'Stony-Irons'
         else
	   'Others'
       end as "Group",
       case 
         when gtu_infos.gtu_ref is null then
           '-'
         else
           gtu_infos.continent
       end as continent,
       taxon_ref,
       taxon_level_ref,
       lithology_ref,
       type,
       sum(specimen_count_max) as specimen_count_maximum
from specimens left join gtu_infos on specimens.gtu_ref = gtu_infos.gtu_ref
where case
        when strpos(taxon_path, '/91/') != 0 and 
	     (collection_ref = 1 or collection_path like '/1/%') then
          'VZ'
        when (strpos(taxon_path, '/37/') != 0 or 
              strpos(taxon_path, '/61/') != 0 or 
              strpos(taxon_path, '/353/') != 0 or 
              strpos(taxon_path, '/184176/') != 0 or 
              strpos(taxon_path, '/177325/') != 0 or
              strpos(taxon_path, '/64/') != 0 or
              strpos(taxon_path, '/65/') != 0 or 
              strpos(taxon_path, '/177320/') != 0 or
              strpos(taxon_path, '/276/') != 0 or
	      strpos(taxon_path, '/109920/') != 0 or
	      strpos(taxon_path, '/30/') != 0 or
	      strpos(taxon_path, '/72/') != 0 or
	      strpos(taxon_path, '/75/') != 0 or 
	      strpos(taxon_path, '/206101/') != 0 or
	      strpos(taxon_path, '/44/') != 0 or
	      strpos(taxon_path, '/33/') != 0 or 
	      strpos(taxon_path, '/177319/') != 0 or
	      strpos(taxon_path, '/28/') != 0 or
	      strpos(taxon_path, '/79/') != 0 or
	      strpos(taxon_path, '/19/') != 0 or
	      strpos(taxon_path, '/81/') != 0 or
	      strpos(taxon_path, '/83/') != 0 or
	      strpos(taxon_path, '/90/') != 0 or
              (strpos(taxon_path, '/2/') != 0 and 
	       strpos(taxon_path, '/61/') = 0 and 
	       strpos(taxon_path, '/353/') = 0 and 
	       strpos(taxon_path, '/184176/') = 0 and 
	       strpos(taxon_path, '/177325/') = 0 and
	       strpos(taxon_path, '/64/') = 0 and
	       strpos(taxon_path, '/65/') = 0 and 
	       strpos(taxon_path, '/177320/') = 0 and
	       strpos(taxon_path, '/276/') = 0
	      ) 
	     ) and 
	     (collection_ref = 2 or collection_path like '/2/%') 
          then
          'IZ'
        when (
               strpos(taxon_path, '/4/') != 0 or
               strpos(taxon_path, '/12/') != 0 or 
               strpos(taxon_path, '/154425/') != 0 or 
               strpos(taxon_path, '/10/') != 0 or
               strpos(taxon_path, '/406/') != 0 or
               strpos(taxon_path, '/408/') != 0 or
               strpos(taxon_path, '/50/') != 0 or
               strpos(taxon_path, '/423/') != 0 or
               strpos(taxon_path, '/413/') != 0 or
               strpos(taxon_path, '/11/') != 0 or
	       strpos(taxon_path, '/47/') != 0 or 
	       strpos(taxon_path, '/106418/') != 0 or
               (
                 strpos(taxon_path, '/2/') != 0 and 
	         strpos(taxon_path, '/4/') = 0 and 
	         strpos(taxon_path, '/12/') = 0 and 
	         strpos(taxon_path, '/10/') = 0 and 
	         strpos(taxon_path, '/406/') = 0 and
	         strpos(taxon_path, '/408/') = 0 and
	         strpos(taxon_path, '/50/') = 0 and 
	         strpos(taxon_path, '/423/') = 0 and
	         strpos(taxon_path, '/413/') = 0 and 
	         strpos(taxon_path, '/11/') = 0 and
	         strpos(taxon_path, '/47/') = 0
               )
             ) and 
	     (collection_ref = 4 or collection_path like '/4/%') then
	  'Entomology'
        when strpos(chrono_path, '/11/') != 0 and
             (collection_ref = 3 or collection_path like '/3/%') then
          'Cenozoic'           
        when strpos(chrono_path, '/26/') != 0 and
             (collection_ref = 3 or collection_path like '/3/%') then
          'Mesozoic'           
        when strpos(chrono_path, '/5/') != 0 and
             (collection_ref = 3 or collection_path like '/3/%') then
          'Paleozoic'           
        when (collection_ref = 200 or collection_path like '/200/%') then
          'Meteorites'
        when (collection_ref = 276) then
          'Gems'
        else
          'Other'
      end in ('VZ', 'IZ', 'Entomology', 'Cenozoic', 'Mesozoic', 'Paleozoic', 'n/a', 'Meteorites', 'Gems', 'Other')
group by "Branch", 
       "Department",
       "Group",
       case 
         when gtu_infos.gtu_ref is null then
           '-'
         else
           gtu_infos.continent
       end,
       taxon_ref,
       taxon_level_ref,
       lithology_ref,
       type
)
select "Branch", 
       "Department", 
       "Group", 
       continent, 
       case 
         when "Branch" = 'MIN' then
           (
             select count(distinct lithology_ref) 
             from specimens_data as sub_specimen_data
             where sub_specimen_data.type != 'specimen'
               and sub_specimen_data."Branch" = main_specimen_data."Branch"
               and sub_specimen_data."Department" = main_specimen_data."Department"
               and sub_specimen_data."Group" = main_specimen_data."Group"
               and sub_specimen_data.continent = main_specimen_data.continent
           )
         else
           (
             select count(distinct taxon_ref) 
             from specimens_data as sub_specimen_data
             where sub_specimen_data.taxon_level_ref >= 48 
               and sub_specimen_data.type != 'specimen'
               and sub_specimen_data."Branch" = main_specimen_data."Branch"
               and sub_specimen_data."Department" = main_specimen_data."Department"
               and sub_specimen_data."Group" = main_specimen_data."Group"
               and sub_specimen_data.continent = main_specimen_data.continent
           ) 
       end as "Type Taxa",
       coalesce((select sum(specimen_count_maximum) 
        from specimens_data as sub_specimen_data
        where sub_specimen_data.type != 'specimen'
          and sub_specimen_data."Branch" = main_specimen_data."Branch"
          and sub_specimen_data."Department" = main_specimen_data."Department"
          and sub_specimen_data."Group" = main_specimen_data."Group"
          and sub_specimen_data.continent = main_specimen_data.continent
       ),0) as "Type",
       case 
         when "Branch" = 'MIN' then
           count(distinct lithology_ref)
         else
           count(distinct taxon_ref) 
       end as "Specimen Taxa"
from specimens_data as main_specimen_data
group by "Branch", "Department", "Group", continent
order by "Branch", "Department", "Group", continent
