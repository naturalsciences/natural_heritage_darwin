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
select 'BIO' as "Branch", 
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
              (strpos(taxon_path, '/2/') != 0 and 
	       strpos(taxon_path, '/61/') = 0 and 
	       strpos(taxon_path, '/353/') = 0 and 
	       strpos(taxon_path, '/184176/') = 0 and 
	       strpos(taxon_path, '/177325/') = 0 and
	       strpos(taxon_path, '/64/') = 0 and
	       strpos(taxon_path, '/65/') = 0 and 
	       strpos(taxon_path, '/177320/') = 0 and
	       strpos(taxon_path, '/276/') = 0
	      ) or
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
	      strpos(taxon_path, '/90/') != 0 
	     ) and 
	     (collection_ref = 2 or collection_path like '/2/%') 
          then
          'IZ'
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
              (strpos(taxon_path, '/2/') != 0 and 
	       strpos(taxon_path, '/61/') = 0 and 
	       strpos(taxon_path, '/353/') = 0 and 
	       strpos(taxon_path, '/184176/') = 0 and 
	       strpos(taxon_path, '/177325/') = 0 and
	       strpos(taxon_path, '/64/') = 0 and
	       strpos(taxon_path, '/65/') = 0 and 
	       strpos(taxon_path, '/177320/') = 0 and
	       strpos(taxon_path, '/276/') = 0
	      ) or
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
	      strpos(taxon_path, '/90/') != 0
	     ) and 
	     (collection_ref = 2 or collection_path like '/2/%') 
          then
          'IZ'
        else
          'Other'
      end in ('VZ', 'IZ')
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
       type
)
select "Branch", 
       "Department", 
       "Group", 
       continent, 
       (select count(distinct taxon_ref) 
        from specimens_data as sub_specimen_data
        where sub_specimen_data.taxon_level_ref >= 48 
          and sub_specimen_data.type != 'specimen'
          and sub_specimen_data."Group" = main_specimen_data."Group"
          and sub_specimen_data.continent = main_specimen_data.continent
       ) as "Type Taxa",
       coalesce((select sum(specimen_count_maximum) 
        from specimens_data as sub_specimen_data
        where sub_specimen_data.type != 'specimen'
          and sub_specimen_data."Group" = main_specimen_data."Group"
          and sub_specimen_data.continent = main_specimen_data.continent
       ),0) as "Type",
       count(distinct taxon_ref) as "Specimen Taxa"
from specimens_data as main_specimen_data
group by "Branch", "Department", "Group", continent
order by "Branch", "Department", "Group", continent
