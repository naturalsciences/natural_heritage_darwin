UPDATE gtu SET 
latitude = NULL,
longitude = NULL,
latitude_dms_degree=null,
latitude_dms_minutes=null,
latitude_dms_seconds=null,
latitude_dms_direction=null,
longitude_dms_degree=null,
longitude_dms_minutes=null,
longitude_dms_seconds=null,
longitude_dms_direction=null,
latitude_utm=null,
longitude_utm=null,
utm_zone=null
WHERE id IN (SELECT gtu_ref FROM specimens WHERE taxon_ref IN (SELECT id FROM taxonomy WHERE sensitive_info_withheld =true));