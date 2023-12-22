#!/usr/bin/python3
import sys
sys.path.append('/var/thermic_printer_zebra/')
import psycopg2
import psycopg2.extras
from threading import Lock
from class_zebra import Class_zpl
from class_text_to_image import Class_text_img
import socket
import traceback



HOST = "172.16.1.200"
PORT = 9100                   # The same port as used by the server

def xstr(s):
    if s is None:
        return ''
    return str(s)
    
class DarwinClientZebra(object):
    def __init__(self ):
        #self.m_logfile = p_logfile
        self.m_mutex = Lock()
        
    def set_pg_connection(self, p_connection_string):
        try:
            self.m_connection_string= p_connection_string
            self.m_conn = psycopg2.connect(self.m_connection_string)
            self.m_conn.set_client_encoding('UTF8')
        except Exception as e:
            #printException(self.m_logfile)
            print(e)
            
    def define_and_print_label(self, p_specimen_id):
        try:            
            self.m_mutex.acquire()
            print("in label")
            print(p_specimen_id)
            record=self.get_specimen_content_pg(p_specimen_id)
            print(record)
            record = {k : xstr(v) for k,v in record.items()}
            print(record)
            #######collection, family, order
            collection = record['collection']
            family = str(record['family'])
            order = str(record['order'])
            
            
            if (record['specimen_count_min'] != "") & (record['specimen_count_min'] != "0") & ((record['specimen_count_males_min'] == "") | (record['specimen_count_males_min'] == "0")) & ((
                    record['specimen_count_females_min'] == "") | (record['specimen_count_females_min'] == "0")) & ((record['specimen_count_juveniles_min'] == "") | (record['specimen_count_juveniles_min'] == "0")):
                if record['specimen_count_max'] == record['specimen_count_min']:
                    nbrsexundef = str(record['specimen_count_min']) + " undefined" #+ namesex
                else:
                    nbrsexundef = str(record['specimen_count_min']) + '-' + str(record['specimen_count_max']) + " undefined" #+ namesex
            else:
                nbrsexundef = ""
                
            if (record['specimen_count_males_min'] != "") & (record['specimen_count_males_min'] != "0"):
                if record['specimen_count_males_max'] == record['specimen_count_males_min']:
                    nbrsexmale = str(record['specimen_count_males_min']) + u'♂'#.decode('string_escape')
                else:
                    nbrsexmale = str(record['specimen_count_males_min']) + '-' + str(
                        record['specimen_count_males_max']) + u'♂'#.decode('string_escape')
            else:
                nbrsexmale = ""
                
            if (record['specimen_count_females_min'] != "") & (record['specimen_count_females_min'] != "0"):
                if record['specimen_count_females_max'] == record['specimen_count_females_min']:
                    nbrsexfem = str(record['specimen_count_females_min']) + u'♀'#.decode('string_escape')
                else:
                    nbrsexfem = str(record['specimen_count_females_min']) + '-' + str(
                        record['specimen_count_females_max']) + u'♀'#.decode('string_escape')
            else:
                nbrsexfem = ""
            if (record['specimen_count_juveniles_min'] != "") & (record['specimen_count_juveniles_min'] != "0"):
                if record['specimen_count_juveniles_max'] == record['specimen_count_juveniles_min']:
                    nbrsexjuv = str(record['specimen_count_juveniles_min']) + ' juv.'
                else:
                    nbrsexjuv = str(record['specimen_count_juveniles_min']) + '-' + str(
                        record['specimen_count_juveniles_max']) + ' juv.'
            else:
                nbrsexjuv = ""
                
            bl1 = ""
            bl2 = ""
            bl3 = ""
            if ((record['specimen_count_min'] == "") | (record['specimen_count_min'] == "0")) & ((record['specimen_count_males_min'] == "") | (record['specimen_count_males_min'] == "0")) & ((record['specimen_count_females_min'] == "") | (record['specimen_count_females_min'] == "0")) & ((record['specimen_count_juveniles_min'] == "") | (record['specimen_count_juveniles_min'] == "0")):
                nbrsex = "1 " + " undefined" #+ namesex
            else:
                if nbrsexundef != "" :
                    bl1 = "    "
                if nbrsexmale != "":
                    bl2 = "    "
                if nbrsexfem != "":
                    bl3 = "    "
                nbrsex = nbrsexundef + bl1 + nbrsexmale + bl2 + nbrsexfem + bl3 + nbrsexjuv

            #######Type
            """
            if (str(record['type']) == None) | (str(record['type']) == 'specimen') :
                if record['status'] != None:
                    typesp = str(record['status'])
                else:
                    typesp = ""
            else:
                typesp = str(record['type'])
            """   
            typesp = ""
            if record['status'] != None:
                typesp = str(record['type'])
            #######Scientific name
            sciName = str(record['name'])
            status=""
            if record['status'] != None:
                status=str(record['status'])
            #if record['status'] != None:
            #    sciName= sciName+ " "+str(record['status'])
            author =  str(record['author'])

            #######Identifier
            if record['identifier'].strip() == "":
                det = u"/"
            else:
                det = str(record['identifier'].strip())
            datedet = record['date_determ']
            datedetmask = record['date_determ_mask']
            if datedetmask == "32":
                datedet = "(" + datedet[6:] + ")"
            if datedetmask == "48":
                datedet = "(" + datedet[3:-5]+ "/" + datedet[6:] + ")"
            if datedetmask == "56":
                datedet = "(" + datedet + ")"

            #######recolter
            if record['collector'].strip() == "":
                if record['expedition'].strip() == "":
                    recol = u"/"
                else:
                    #recol = str(record['expedition'].strip())
                    recol = record['expedition'].strip()
            else:
                #recol = str(record['collector'].strip())
                recol = record['collector'].strip()

            datestartRecol = ""
            dateendRecol = ""
            datestartRecol1 = str(record['collecting_start_date'])
            dateendRecol1 = str(record['collecting_end_date'])
            startdatemask = record['collecting_start_date_mask']
            enddatemask = record['collecting_end_date_mask']
            if startdatemask == "0" :
                dateRecol = ''
            else:
                if startdatemask == "32":
                    datestartRecol = datestartRecol1[6:]
                elif startdatemask == "48":
                    datestartRecol = datestartRecol1[3:-5] + "/" + datestartRecol1[6:]
                else:
                    datestartRecol = datestartRecol1
                    
            if enddatemask == "0":
                dateendRecol = ""
            else:
                if enddatemask == "32":
                    dateendRecol = dateendRecol1[6:]
                elif enddatemask == "48":
                    dateendRecol = dateendRecol1[3:-5] + "/" + dateendRecol1[6:]
                else:
                    dateendRecol = dateendRecol1
            if dateendRecol != "":
                dateRecol = '(' + datestartRecol + "-" + dateendRecol + ')'
            elif datestartRecol != "":
                dateRecol = '(' + datestartRecol + ')'
            else:
                dateRecol = ''

            #######code
            code = str(record['code'])
            #print(code)
            if code.upper().startswith("BE_"):
                code=code[3:]
            if (code[0:10] == 'RMCA_Vert_'):
                code1 = code[0:10].replace("_", " ")
                code2 = code[10:]
            elif (code[0:10] == 'RMCA_Vert.'):
                code1 = code[0:10].replace(".", " ")
                code2 = code[10:]
            elif (code[0:9] == 'RMCA_Mam_'):
                code1 = code[0:9].replace("_", " ")
                code2 = code[9:]
            else :
                code1 = ''
                code2 = code
            ig = str(record['ig_num'])

            #######other
            instit = u"RMCA Tervuren(KMMA/MRAC)"
            dateLabel = str(record['current_date'])

            #######Locality
            '''
            loc2 = ""
            loc3 = ""
            loc4 = ""
            if record['municipality'] != "":
                Munisite = record['municipality'] + ", "+ record['site']
            else:
                Munisite = record['site']
            if Munisite != "":
                if len(Munisite) > 36 :
                    p = Munisite[:36].rfind(' ')
                    loc = str(Munisite)[:p]
                    loc2orig = str(Munisite)[p:]
                    loc2 = loc2orig
                    if len(loc2orig) > 36 :
                        p2 = loc2orig[:36].rfind(' ')
                        loc2 = str(loc2orig)[:p2]
                        loc3orig = str(loc2orig)[p2:]
                        loc3 = loc3orig
                        if len(loc3orig) > 36 :
                            p3 = loc3orig[:36].rfind(' ')
                            loc3 = str(loc3orig)[:p3]
                            loc4 = str(loc3orig)[p3:]
                else:
                    loc = str(Munisite)
            else:
                loc = u"/"
            '''
            if record['municipality'] != "":
                Munisite = record['municipality'] + ", "+ record['site']
            else:
                Munisite = record['site']
            loc=Munisite
            country = "("+str(record['country'])+")"
            Coordsource = record['coordinates_source']
            longsec = record['longitude_dms_seconds']
            latsec = record['latitude_dms_seconds']
            latitude = str(record['latitude'])
            if (len(latitude)>7):
                latitude = latitude[:7]
            longitude = str(record['longitude'])
            if (len(longitude)>6):
                longitude = longitude[:6]
            latitudeDMS = str(record['latitudedms'])
            longitudeDMS = str(record['longitudedms'])
            latlong = u"Lat : /  Long: /"
            if Coordsource == "DD":
                if latitude != "":
                    latlong= "Lat: " + latitude + " Long: " + longitude
            elif latitudeDMS != "" :
                if (len(latsec) > 6) | (len(longsec) > 6):
                    if latitude != "":
                        latlong= "Lat: " + latitude + " Long: " + longitude
                else :
                    latlong = latitudeDMS + " - " + longitudeDMS

            #######properties
            prop1 = ""
            prop2 = ""
            prop3 = ""
            prop4 = ""
            prop5 = ""
            if record['dna'].strip() != "" :
                prop1 =  str(record['dna']) + ", "
            if record['collector_field_number'].strip() != "" :
                prop2 = "coll field nbr:" + str(record['collector_field_number'])
            if record['parasite'].strip() != "":
                prop3 = "parasit.:" + str(record['parasite'])+ ", "
            if record['identification_extra_info'].strip() != "":
                prop3 = prop3 + "ident.:" + str(record['identification_extra_info'])
            if record['fixation_extra_info'].strip() != "":
                prop4 = "Fix.:" + str(record['fixation_extra_info']) + ", "
            if record['dissection'].strip() != "":
                prop4 = prop4 + "Dissect.:" + str(record['dissection'])
            if record['eod'].strip() != "":
                prop5 = str(record['eod'])

            generator=Class_text_img()
            generator.set_width(420)
            generator.set_height(632)
            #print(loc)
            #print(country)
            ##print(loc2)
            ##print(loc3)
            ##print(loc4)
            #print(country)
            #print(code1)
            #print(code2)
           
            img=generator.build_label(sci_name=sciName, status=status, author=author, typesp=typesp, det=det, det_year=datedet, code1=code2, nbr=nbrsex, locality=loc, country=country, latlong=latlong, collector=recol, collecting_date=dateRecol, collection=code1, prop1=prop1, prop2=prop2, prop3= prop3, prop4=prop4, prop5=prop5)
            
            zpl=Class_zpl()
            zpl.set_compress(False)
            zpl.set_blacklight_percentage(50)
            str_label=zpl.process(img)
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            s.connect((HOST, PORT))
            s.sendall(str.encode(str_label))
            #data = s.recv(1024)
            s.close()
                        
        except Exception as e:
            #print("exception")
            #print(e)
            msg = traceback.format_exc()
            print(msg)
        finally:
            if self.m_mutex.locked(): 
                self.m_mutex.release()
            
    def get_specimen_content_pg(self, p_specimen_id):
        try:
            query = """SELECT 
            COALESCE(c.code_prefix,'')||COALESCE(c.code_prefix_separator,'')||COALESCE(c.code,'')||COALESCE(c.code_suffix_separator,'')||COALESCE(c.code_suffix,'') as code,
            s.ig_num,
            s.collection_ref as collection,
            (fct_rmca_taxonomy_split_name_author(t.name, level_ref))[1] as name,
            i.determination_status as status,
            (fct_rmca_taxonomy_split_name_author(t.name, level_ref))[2] as author,
            fct_rmca_sort_taxon_get_parent_level_text(s.taxon_ref,28) as order,
            fct_rmca_sort_taxon_get_parent_level_text(s.taxon_ref,34) as family,
            s.type, 
            s.sex,
            s.specimen_count_min,
            s.specimen_count_max,
            s.specimen_count_males_min,
            s.specimen_count_males_max,
            s.specimen_count_females_min,
            s.specimen_count_females_max,
            s.specimen_count_juveniles_min,
            s.specimen_count_juveniles_max,
            coalesce((select * FROM rmca_get_locality_tag_from_specimen(s.id, '%%', 'Exact_site') LIMIT 1  ) ,'') as site,
            coalesce((select * FROM rmca_get_locality_tag_from_specimen(s.id, '%%', 'Municipality') LIMIT 1  ) ,'') as municipality,
            coalesce((select * FROM rmca_get_locality_tag_from_specimen(s.id, '%%', 'Country') LIMIT 1 ) ,'') as country,
            g.latitude, g.longitude, g.coordinates_source,g.latitude_dms_seconds,g.longitude_dms_seconds,
            COALESCE(g.latitude_dms_degree || '°' || COALESCE(g.latitude_dms_minutes || '''','') || COALESCE(g.latitude_dms_seconds || '"','') || CASE WHEN g.latitude_dms_direction = -1 THEN ' S' WHEN g.latitude_dms_direction = 1 THEN ' N' END,'') as latitudedms, 
            COALESCE(g.longitude_dms_degree || '°' || COALESCE(g.longitude_dms_minutes || '''','') || COALESCE(g.longitude_dms_seconds || '"','') || CASE WHEN g.longitude_dms_direction = -1 THEN ' O' WHEN g.longitude_dms_direction = 1 THEN ' E' END,'') as longitudedms,
            s.container_storage,
            COALESCE(to_char(s.gtu_from_date, 'DD/MM/YYYY'),'') as collecting_start_date,
            COALESCE(to_char(s.gtu_to_date, 'DD/MM/YYYY'),'') as collecting_end_date, 
            s.gtu_from_date_mask as collecting_start_date_mask,
            s.gtu_to_date_mask as collecting_end_date_mask, 
            (SELECT string_agg(case
                when given_name  is not NULL then

                TRIM(family_name||', '||given_name||COALESCE(' ('||NULLIF(title,'')||')',''))
                ELSE
                formated_name
                end
                , '; ') as identifier
                FROM (SELECT unnest( spec_ident_ids::int[] ) as idpeople 
                       FROM specimens WHERE id = %s ) i
                LEFT JOIN 
                   people p
                   ON p.id = i.idpeople) as identifier,
            CASE WHEN date_part('year', i.notion_date) != 1 THEN
              COALESCE(to_char(i.notion_date, 'DD/MM/YYYY'),'')
            END as date_determ, 
            i.notion_date_mask as date_determ_mask,
            (SELECT string_agg(case
when given_name  is not NULL then

TRIM(family_name||', '||given_name||COALESCE(' ('||NULLIF(title,'')||')',''))
ELSE
formated_name
end, '; ') as collector
                FROM (SELECT unnest( spec_coll_ids::int[] ) as idpeople FROM specimens WHERE id = %s ) i
                LEFT JOIN 
                  people p
                  ON p.id = i.idpeople) as collector,
            s.expedition_name as expedition,
            'DNA: '||p1.lower_value as dna,
            'Fieldnr. '||p2.lower_value as collector_field_number,
            'Par. '||p3.lower_value as parasite,
             p4.lower_value as identification_extra_info,
            'Fix. '||p5.lower_value as fixation_extra_info,
            'Diss. '||p6.lower_value as dissection,
            'EOD: '||p7.lower_value as eod,
            to_char(CURRENT_DATE, 'DD/MM/YYYY') as current_date

            FROM specimens s
            LEFT JOIN 
               codes c
               ON c.referenced_relation='specimens' and c.code_category='main' and s.id=c.record_id
            LEFT JOIN
               taxonomy t
               ON t.id = s.taxon_ref
            LEFT JOIN
               gtu g
               ON s.gtu_ref = g.id
            LEFT JOIN 
               properties p1
               ON p1.record_id = s.id AND p1.referenced_relation='specimens' AND Lower(p1.property_type) = 'dna'
            LEFT JOIN 
               properties p2
               ON p2.record_id = s.id AND p2.referenced_relation='specimens' AND Lower(p2.property_type) = 'collector_field_number'
            LEFT JOIN 
               properties p3
               ON p3.record_id = s.id AND p3.referenced_relation='specimens' AND Lower(p3.property_type) = 'parasite'
            LEFT JOIN 
               properties p4
               ON p4.record_id = s.id AND p4.referenced_relation='specimens' AND Lower(p4.property_type) = 'identification_extra_info'
            LEFT JOIN 
               properties p5
               ON p5.record_id = s.id AND p5.referenced_relation='specimens' AND Lower(p5.property_type) = 'fixation extra info'
            LEFT JOIN 
               properties p6
               ON p6.record_id = s.id AND p6.referenced_relation='specimens' AND Lower(p6.property_type) = 'dissection'
            LEFT JOIN 
               properties p7
               ON p7.record_id = s.id AND p7.referenced_relation='specimens' AND Lower(p7.property_type) = 'eod'
            LEFT JOIN 
               identifications i
               ON s.id=i.record_id AND i.referenced_relation='specimens' AND i.notion_concerned='taxonomy'
            LEFT JOIN flat_dict j
             ON i.determination_status=j.id::varchar
            WHERE s.id =%s;"""
            cur = self.m_conn.cursor(cursor_factory=psycopg2.extras.DictCursor)
            cur.execute(query,  (p_specimen_id, p_specimen_id, p_specimen_id)    )
            for item in cur:
                return item
        except Exception as e:
            ##printException(self.m_logfile)
            print(e) 