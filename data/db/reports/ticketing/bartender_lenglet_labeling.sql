select *
from
"public"."labeling" as df 
where (collection = 1 or collection_path like '/1/%')
  and case when coalesce('?InviteCollection','') = '' then true else collection in (select id from collections where name_indexed in (select fullToIndex(regexp_split_to_table('?InviteCollection', ';')))) end
  and case when coalesce('?InviteCodeFrom', '') = '' and coalesce('?InviteCodeTo', '') = '' then true
           else coalesce('?InviteCodeFrom', '') != ''
            and (lenglet_code_array && (string_to_array(coalesce(translate('?InviteCodeFrom', E',/\\#', ';;;;'),''),';'))::varchar[]
                 or
                 case 
                   when convert_to_integer(coalesce('?InviteCodeFrom','')) != 0 and convert_to_integer(coalesce('?InviteCodeTo','')) != 0 then
                     convert_to_integer(lenglet_code) between convert_to_integer(coalesce('?InviteCodeFrom','')) and convert_to_integer(coalesce('?InviteCodeTo',''))
                   else
                     false
                 end 
                )
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
  and case when coalesce('?InviteIGFrom', '') = '' and coalesce('?InviteIGTo', '') = '' then true
      else df.ig_num != '-' and 
           (df.ig_num in (select trim(regexp_split_to_table(coalesce('?InviteIGFrom',''), ';'))) 
            or
            case 
              when convert_to_integer(coalesce('?InviteIGFrom','')) != 0 and convert_to_integer(coalesce('?InviteIGTo','')) != 0 then
                convert_to_integer(df.ig_num) between convert_to_integer(coalesce('?InviteIGFrom','')) and convert_to_integer(coalesce('?InviteIGTo',''))
              else
                false
            end 
           )
      end
  and case when coalesce('?InviteItem', '') = '' then true
           else df.part && (select array_agg(itemList)::varchar[] from (select fullToIndex(regexp_split_to_table(coalesce(translate('?InviteItem', E',/\\#.', ';;;; '),''),';')) as itemList) as subqry)
           end
  and case when coalesce('?InviteType', '') = '' then true
           else df.type && (select array_agg(typeList)::varchar[] from (select fullToIndex(regexp_split_to_table(coalesce(translate('?InviteType', E',/\\#.', ';;;; '),''),';')) as typeList) as subqry)
           end
  and case when coalesce('?InviteSex', '') = '' then true
           else df.sex && (select array_agg(sexList)::varchar[] from (select fullToIndex(regexp_split_to_table(coalesce(translate('?InviteSex', E',/\\#.', ';;;; '),''),';')) as sexList) as subqry)
           end
  and case when coalesce('?InviteStage', '') = '' then true
           else df.stage && (select array_agg(stageList)::varchar[] from (select fullToIndex(regexp_split_to_table(coalesce(translate('?InviteStage', E',/\\#.', ';;;; '),''),';')) as stageList) as subqry)
           end
  and case when coalesce('?InviteHigherTaxa', '') = '' then true
      else df.taxon_path like (select path || id || '%' from taxonomy where name = '?InviteHigherTaxa')
      end
  and case when trim(translate(coalesce('?InviteTaxa', ''), E'.,;&|\\/#()', '          ')) = '' then true
      else df.taxon_name_indexed @@ to_tsquery('simple', translate('?InviteTaxa', E'.,;&|\\/#() ', '  |&||||  &'))
      end
  limit convert_to_integer('?InviteLimit')
;