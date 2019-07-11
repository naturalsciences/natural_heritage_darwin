with specimen_groups as 
(
  select sgr_batch_nr as batch_id, 
         array_to_string(array_agg(distinct sgr_code), E'\n') as spec_subset_codes,
         array_to_string(array_agg(distinct 'Comment for ' || sgr_code || ': ' || sgr_comment), E'\n') as spec_subset_comments,
         array_to_string(array_agg(distinct 'Sex for ' || sgr_code || ': ' || sex_name), E'\n') as spec_subset_sexes,
         array_to_string(array_agg(distinct 'Stage for ' || sgr_code || ': ' || stg_name), E'\n') as spec_subset_stages,
         array_to_string(array_agg(distinct 'Type for ' || sgr_code || ': ' || tyl_name), E'\n') as spec_subset_types,
         array_to_string(array_agg(distinct 'Conservation mean for ' || sgr_code || ': ' || sto_storage), E'\n') as spec_subset_cons_means,
         array_to_string(array_agg(distinct 'Storage location for ' || sgr_code || ': ' || rom_code || '/' || coalesce(sgr_row,'*') || '/' || coalesce(sgr_shelf,'*') || '/' || coalesce(sgr_container,'*') || '/' || coalesce(sgr_subcontainer,'*')), E'\n') as spec_subset_storage_locations,
         array_to_string(array_agg(distinct 'Count for ' || sgr_code || ': ' || sgr_number_in_group), E'\n') as spec_subset_count,
         array_to_string(array_agg(distinct 'Part item for ' || sgr_code || ': ' || pit_item), E'\n') as spec_subset_pit_item          
  from darwin1.tbl_specimen_groups
  inner join darwin1.tbl_sexes on sgr_sex_nr = sex_id_ctn
  inner join darwin1.tbl_stages on sgr_stage_nr = stg_id_ctn
  inner join darwin1.tbl_types on sgr_type_nr = tyl_id_ctn
  inner join darwin1.tbl_storage on sgr_storage_nr = sto_id_ctn
  inner join darwin1.tbl_rooms on sgr_room_nr = rom_id_ctn
  group by sgr_batch_nr
  --having count(distinct sgr_code) > 1
)
select col.col_short_name as "Collection - Code", 
       col.col_group_name as "Collection - Name",
       case 
         when scl.scl_name = 'Undefined' then
           null
         else
           scl.scl_name 
       end::text as "Collection - Sub collection name",
       cle_code as "Sampling event code",
       cle_complete_name as "Sampling event name",
       expe.exp_name as "Expedition",
       case
         when cle_date_from_year is not null then
           case
             when cle_date_from_month is null then
               cle_date_from_year::text
             else
               case
                 when cle_date_from_day is null then
                   lpad(cle_date_from_month::text, 2, '0') || '/' || cle_date_from_year
                 else
                   lpad(cle_date_from_day::text, 2, '0') || '/' || lpad(cle_date_from_month::text, 2, '0') || '/' || cle_date_from_year
               end  
           end
         else
           null
       end::text as "Sampling date from",
       case
         when cle_date_to_year is not null then
           case
             when cle_date_to_month is null then
               cle_date_to_year::text
             else
               case
                 when cle_date_to_day is null then
                   lpad(cle_date_to_month::text, 2, '0') || '/' || cle_date_to_year
                 else
                   lpad(cle_date_to_day::text, 2, '0') || '/' || lpad(cle_date_to_month::text, 2, '0') || '/' || cle_date_to_year
               end  
           end
         else
           null
       end as "Sampling date to",
       cev.cle_ig as "I.G.",
       case
         when cle_ig_date_year is not null then
           case
             when cle_ig_date_month is null then
               cle_ig_date_year::text
             else
               case
                 when cle_ig_date_day is null then
                   lpad(cle_ig_date_month::text, 2, '0') || '/' || cle_ig_date_year
                 else
                   lpad(cle_ig_date_day::text, 2, '0') || '/' || lpad(cle_ig_date_month::text, 2, '0') || '/' || cle_ig_date_year
               end  
           end
         else
           null
       end as "I.G. date",
       case
         when cle_acquisition_date_year is not null then
           case
             when cle_acquisition_date_month is null then
               cle_acquisition_date_year::text
             else
               case
                 when cle_acquisition_date_day is null then
                   lpad(cle_acquisition_date_month::text, 2, '0') || '/' || cle_acquisition_date_year
                 else
                   lpad(cle_acquisition_date_day::text, 2, '0') || '/' || lpad(cle_acquisition_date_month::text, 2, '0') || '/' || cle_acquisition_date_year
               end  
           end
         else
           null
       end as "Acquisition date",
       cou_name as "Sampling Country",
       sta_code as "Sampling location code",
       sta_location_full_name as "Sampling location name",
       case
         when sta_date_from_year is not null then
           case
             when sta_date_from_month is null then
               sta_date_from_year::text
             else
               case
                 when sta_date_from_day is null then
                   lpad(sta_date_from_month::text, 2, '0') || '/' || sta_date_from_year
                 else
                   lpad(sta_date_from_day::text, 2, '0') || '/' || lpad(sta_date_from_month::text, 2, '0') || '/' || sta_date_from_year
               end  
           end
         else
           null
       end::text as "Sampling location date from",
       case
         when sta_date_to_year is not null then
           case
             when sta_date_to_month is null then
               sta_date_to_year::text
             else
               case
                 when sta_date_to_day is null then
                   lpad(sta_date_to_month::text, 2, '0') || '/' || sta_date_to_year
                 else
                   lpad(sta_date_to_day::text, 2, '0') || '/' || lpad(sta_date_to_month::text, 2, '0') || '/' || sta_date_to_year
               end  
           end
         else
           null
       end as "Sampling location date to",
       sta_latitude_from_decimal_1 as "Sampling location Latitude (from)",
       sta_longitude_from_decimal_1 as "Sampling location Longitude (from)",
       sta_latitude_to_decimal_1 as "Sampling location Latitude (to)",
       sta_longitude_to_decimal_1 as "Sampling location Longitude (to)",
       elevation as "Sampling location altitude (m)",
       taxphyl.tax_full_name as "Taxon phylum",
       taxclass.tax_full_name as "Taxon class",
       taxorder.tax_full_name as "Taxon order",
       taxfam.tax_full_name as "Taxon family",
       tax.tax_full_name as "Taxon name",
       lev_meaning as "Taxon level",
       cev.cle_comment as "Sampling event comment",
       sta_comment as "Sampling location comment",
       bat_comment as "Specimen comment",
       bat_specimen_number_in_batch as "Specimen count",
       bat.bat_unique_rbins_code as "Specimen old unique RBINS code",
       bat.bat_code "Specimen old code",
       spec_subset_codes as "Specimen subset codes",
       spec_subset_comments as "Specimen subset comments",
       spec_subset_sexes as "Specimen subset sexes",
       spec_subset_stages as "Specimen subset stages",
       spec_subset_types as "Specimen subset types",
       spec_subset_cons_means as "Specimen subset conservation means",
       spec_subset_storage_locations as "Specimen subset storage locations",
       spec_subset_count as "Specimen subset count",
       spec_subset_pit_item as "Specimen part concerned"
from darwin1.tbl_batches as bat 
inner join darwin1.tbl_collections as col on bat.bat_collection_id_nr = col.col_id_ctn
left join 
  (
    darwin1.tbl_collecting_events as cev 
    left join
    darwin1.tbl_expeditions as expe
    on cev.cle_expedition_nr = expe.exp_id_ctn
  ) on bat_coll_event_nr = cev.cle_id_ctn
left join darwin1.tbl_sub_collections as scl on bat.bat_sub_collection_nr = scl.scl_id_ctn
left join 
  (
    darwin1.tbl_stations
    left join
    darwin1.tbl_countries on sta_country_nr = cou_id_ctn
  ) on sta_id_ctn = bat_station_nr
inner join darwin1.tbl_levels on bat_taxon_level_nr = lev_id_ctn
inner join 
  (
    darwin1.tbl_taxa as tax
    inner join
    darwin1.tbl_taxa as taxfam on tax.tax_familly_nr = taxfam.tax_id_ctn
    inner join 
    darwin1.tbl_taxa as taxorder on tax.tax_order_nr = taxorder.tax_id_ctn
    inner join 
    darwin1.tbl_taxa as taxclass on tax.tax_class_nr = taxclass.tax_id_ctn
    inner join 
    darwin1.tbl_taxa as taxphyl on tax.tax_phylum_nr = taxphyl.tax_id_ctn
  ) on bat.bat_taxa_nr = tax.tax_id_ctn
inner join specimen_groups on bat_id_ctn = batch_id
--where col.col_id_ctn in (132/*, 133, 192, 212, 215, 254, 278, 279, 280, 282, 291*/)
where col.col_id_ctn = 291
order by col.col_group_name, scl.scl_name, taxphyl.tax_full_name, taxclass.tax_full_name, taxorder.tax_full_name, taxfam.tax_full_name, tax.tax_full_name
;