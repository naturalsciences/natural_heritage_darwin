with gtu_continents as (
  select distinct
         gtu_ref,
         trim(unnest(string_to_array(lower(tag_value), ';'))) as gtu_continent
  from tag_groups 
  where sub_group_name_indexed like 'cont%' 
  order by gtu_continent
)
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
         when gtu_continent in ('asia', 'asie', 'asie centrale', 'east asia') then
           'Asia'
         when gtu_continent in ('australia', 'oceania') then
           'Oceania'
         when gtu_continent in ('europe', 'europa, l'' europe', 'europe centrale', 'europe orientale', 'north sea belgium/holland') then
	   'Europe'
         else
           gtu_continent
       end as continent
from gtu_continents
order by continent