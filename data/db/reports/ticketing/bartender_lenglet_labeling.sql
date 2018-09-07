select *
from
"public"."labeling"  
where (collection = 1 or collection_path like '/1/%')
  and case when coalesce('?InviteCollection','') = '' then true else collection in (select id from collections where name_indexed in (select fullToIndex(regexp_split_to_table('?InviteCollection', ';')))) end
  and case when coalesce('?InviteCodeFrom', '') = '' and coalesce('?InviteCodeTo', '') = '' then true
           else
            case when coalesce('?InviteCodeFrom', '') != '' and coalesce('?InviteCodeTo', '') = '' then
                   code_array && (string_to_array(coalesce(translate('?InviteCodeFrom', E',#', ';;'),''),';'))::varchar[]
                 when coalesce('?InviteCodeFrom', '') = '' and coalesce('?InviteCodeTo', '') != '' then
                   code_array && (string_to_array(coalesce(translate('?InviteCodeTo', E',#', ';;'),''),';'))::varchar[]
                 when convert_to_integer(coalesce('?InviteCodeFrom', '')) != 0 and convert_to_integer(coalesce('?InviteCodeTo', '')) != 0 then
                   code_num between convert_to_integer(coalesce('?InviteCodeFrom','')) and convert_to_integer(coalesce('?InviteCodeTo',''))
                 else
                   false
            end
      end
  and case when coalesce('?InvitePays', '') = '' then true 
           else countries_array && (select array_agg(countriesList)::varchar[] from (select fullToIndex(regexp_split_to_table(coalesce(translate('?InvitePays', E',/\\#.', ';;;; '),''),';')) as countriesList) as subqry)  
           end
  and case when coalesce('?InviteProvince', '') = '' then true 
           else provinces_array && (select array_agg(provincesList)::varchar[] from (select fullToIndex(regexp_split_to_table(coalesce(translate('?InviteProvince', E',/\\#.', ';;;; '),''),';')) as provincesList) as subqry)  
           end
  and case when coalesce('?InviteLocalisation', '') = '' then true 
           else location_array && (select array_agg(locationsList)::varchar[] from (select fullToIndex(regexp_split_to_table(coalesce(translate('?InviteLocalisation', E',/\\#.', ';;;; '),''),';')) as locationsList) as subqry)  
           end
  and case when coalesce(replace('?InviteIGFrom','-',''), '') = '' and coalesce(replace('?InviteIGTo','-',''), '') = '' then true
           else
            case when coalesce(replace('?InviteIGFrom','-',''), '') != '' and coalesce(replace('?InviteIGTo','-',''), '') = '' then
                   labeling.ig_num_indexed in (select fullToIndex(regexp_split_to_table(translate('?InviteIGFrom', E',/\\#.', ';;;; '), ';')))
                 when coalesce(replace('?InviteIGFrom','-',''), '') = '' and coalesce(replace('?InviteIGTo','-',''), '') != '' then
                   labeling.ig_num_indexed in (select fullToIndex(regexp_split_to_table(translate('?InviteIGTo', E',/\\#.', ';;;; '), ';')))
                 when convert_to_integer(coalesce(replace('?InviteIGFrom','-',''), '')) != 0 and convert_to_integer(coalesce(replace('?InviteIGTo','-',''), '')) != 0 then
                   labeling.ig_numeric between convert_to_integer('?InviteIGFrom') and convert_to_integer('?InviteIGTo')
                 else
                   false
            end
      end
  and case when coalesce('?InviteItem', '') = '' then true
           else labeling.part && (select array_agg(itemList)::varchar[] from (select fullToIndex(regexp_split_to_table(coalesce(translate('?InviteItem', E',/\\#.', ';;;; '),''),';')) as itemList) as subqry)
           end
  and case when coalesce('?InviteType', '') = '' then true
           else labeling.type && (select array_agg(typeList)::varchar[] from (select fullToIndex(regexp_split_to_table(coalesce(translate('?InviteType', E',/\\#.', ';;;; '),''),';')) as typeList) as subqry)
           end
  and case when coalesce('?InviteSex', '') = '' then true
           else labeling.sex Like '?InviteSex' || '%'
           end
  and case when coalesce('?InviteStage', '') = '' then true
           else labeling.stage Like '?InviteStage' || '%'
           end
  and case when coalesce('?InviteHigherTaxa', '') = '' then true
      else labeling.taxon_path like (select path || id || '%' from taxonomy where name = '?InviteHigherTaxa')
      end
  and case when trim(translate(coalesce('?InviteTaxa', ''), E'.,;&|\\/#()', '          ')) = '' then true
      else labeling.taxon_name_indexed like '%' || fullToIndex('?InviteTaxa') || '%'
      end
  limit convert_to_integer('?InviteLimit')
;
