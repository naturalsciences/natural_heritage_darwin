import subprocess
import io
import sys
import linecache
import psycopg2
import psycopg2.extras
import os
import hashlib
import time
from threading import Lock
#import paramiko


reload(sys)  # Reload does the trick!
sys.setdefaultencoding('UTF8')

def printException():
    exc_type, exc_obj, tb = sys.exc_info()
    f = tb.tb_frame
    lineno = tb.tb_lineno
    filename = f.f_code.co_filename
    linecache.checkcache(filename)
    line = linecache.getline(filename, lineno, f.f_globals)
    #print 'EXCEPTION IN ({}, LINE {} "{}"): {}'.format(filename, lineno, line.strip(), exc_obj)


def xstr(s):
    if s is None:
        return ''
    return str(s)

class DarwinClientParser(object):
    def __init__(self, p_logfile='D:\Thermic_printer\log\main_printer_darwin.log' ):
        self.m_logfile = p_logfile
        self.m_mutex = Lock()
        
    def set_pg_connection(self, p_connection_string):
        try:
            self.m_connection_string= p_connection_string
            self.m_conn = psycopg2.connect(self.m_connection_string)
            self.m_conn.set_client_encoding('UTF8')
        except Exception, e:
            printException(self.m_logfile)


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
            COALESCE(g.latitude_dms_degree || '\\u00B0' || COALESCE(g.latitude_dms_minutes || '''','') || COALESCE(g.latitude_dms_seconds || '"','') || CASE WHEN g.latitude_dms_direction = -1 THEN ' S' WHEN g.latitude_dms_direction = 1 THEN ' N' END,'') as latitudedms, 
            COALESCE(g.longitude_dms_degree || '\\u00B0' || COALESCE(g.longitude_dms_minutes || '''','') || COALESCE(g.longitude_dms_seconds || '"','') || CASE WHEN g.longitude_dms_direction = -1 THEN ' O' WHEN g.longitude_dms_direction = 1 THEN ' E' END,'') as longitudedms,
            s.container_storage,
            COALESCE(to_char(s.gtu_from_date, 'DD/MM/YYYY'),'') as collecting_start_date,
            COALESCE(to_char(s.gtu_to_date, 'DD/MM/YYYY'),'') as collecting_end_date, 
            s.gtu_from_date_mask as collecting_start_date_mask,
            s.gtu_to_date_mask as collecting_end_date_mask, 
            (SELECT string_agg(p.formated_name, '; ') as identifier
                FROM (SELECT unnest( spec_ident_ids::int[] ) as idpeople 
                       FROM specimens WHERE id = %s ) i
                LEFT JOIN 
                   people p
                   ON p.id = i.idpeople) as identifier,
            CASE WHEN date_part('year', i.notion_date) != 1 THEN
              COALESCE(to_char(i.notion_date, 'DD/MM/YYYY'),'')
            END as date_determ, 
            i.notion_date_mask as date_determ_mask,
            (SELECT string_agg(p.formated_name, '; ') as collector
                FROM (SELECT unnest( spec_coll_ids::int[] ) as idpeople FROM specimens WHERE id = %s ) i
                LEFT JOIN 
                  people p
                  ON p.id = i.idpeople) as collector,
            p1.lower_value as dna,
            p2.lower_value as collector_field_number,
            p3.lower_value as parasite,
            p4.lower_value as identification_extra_info,
            p5.lower_value as fixation_extra_info,
            p6.lower_value as dissection,
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
               identifications i
               ON s.id=i.record_id AND i.referenced_relation='specimens' AND i.notion_concerned='taxonomy'

            WHERE s.id =%s;"""
            cur = self.m_conn.cursor(cursor_factory=psycopg2.extras.DictCursor)
            cur.execute(query,  (p_specimen_id, p_specimen_id, p_specimen_id)    )
            for item in cur:
                return item
        except Exception, e:
            printException(self.m_logfile)



    def define_and_print_label(self, p_specimen_id, fileprn):
        try:            
            self.m_mutex.acquire()
           
            record=self.get_specimen_content_pg(p_specimen_id)
            record = {k : xstr(v) for k,v in record.items()}
            
            ############# data manipulation before creating the file ##########################

            #######collection, family, order
            collection = record['collection']
            family = unicode(record['family'])
            order = unicode(record['order'])

            #######Nbr of undefined, male, female, juvenile
           # if record['sex'] == "undefined" | record['sex'] ==  :
          #      namesex = " undefined"
           # elif record['sex'] == "male":
          #      namesex = u'\\u2642\n'.decode('string_escape')
          #  elif record['sex'] == "female":
          #      namesex = u'\\u2640\n'.decode('string_escape')
            if (record['specimen_count_min'] != "") & (record['specimen_count_min'] != "0") & ((record['specimen_count_males_min'] == "") | (record['specimen_count_males_min'] == "0")) & ((
                    record['specimen_count_females_min'] == "") | (record['specimen_count_females_min'] == "0")) & ((record['specimen_count_juveniles_min'] == "") | (record['specimen_count_juveniles_min'] == "0")):
                if record['specimen_count_max'] == record['specimen_count_min']:
                    nbrsexundef = str(record['specimen_count_min']) + " undefined"
                else:
                    nbrsexundef = str(record['specimen_count_min']) + '-' + str(record['specimen_count_max']) + " undefined"
            else:
                nbrsexundef = ""

            if (record['specimen_count_males_min'] != "") & (record['specimen_count_males_min'] != "0"):
                if record['specimen_count_males_max'] == record['specimen_count_males_min']:
                    nbrsexmale = str(record['specimen_count_males_min']) + u'\\u2642\n'.decode('string_escape')
                else:
                    nbrsexmale = str(record['specimen_count_males_min']) + '-' + str(
                        record['specimen_count_males_max']) + u'\\u2642\n'.decode('string_escape')
            else:
                nbrsexmale = ""
            if (record['specimen_count_females_min'] != "") & (record['specimen_count_females_min'] != "0"):
                if record['specimen_count_females_max'] == record['specimen_count_females_min']:
                    nbrsexfem = str(record['specimen_count_females_min']) + u'\\u2640\n'.decode('string_escape')
                else:
                    nbrsexfem = str(record['specimen_count_females_min']) + '-' + str(
                        record['specimen_count_females_max']) + u'\\u2640\n'.decode('string_escape')
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
                nbrsex = "1 undefined"
            else:
                if nbrsexundef != "" :
                    bl1 = "    "
                if nbrsexmale != "":
                    bl2 = "    "
                if nbrsexfem != "":
                    bl3 = "    "
                nbrsex = nbrsexundef + bl1 + nbrsexmale + bl2 + nbrsexfem + bl3 + nbrsexjuv

            #######Type
            if (unicode(record['type']) == None) | (unicode(record['type']) == 'specimen') :
                if record['status'] != None:
                    typesp = unicode(record['status'])
                else:
                    typesp = ""
            else:
                typesp = unicode(record['type'])

            #######Scientific name
            sciName = unicode(record['name'])
            author =  unicode(record['author'])

            #######Identifier
            if record['identifier'].strip() == "":
                det = u"/"
            else:
                det = unicode(record['identifier'].strip())
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
                recol = u"/"
            else:
                recol = unicode(record['collector'].strip())
                recol = record['collector'].strip()

            datestartRecol = ""
            dateendRecol = ""
            datestartRecol1 = unicode(record['collecting_start_date'])
            dateendRecol1 = unicode(record['collecting_end_date'])
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
            code = unicode(record['code'])
            if (code[0:10] == 'RMCA_Vert_'):
				code1 = code[0:10]
				code2 = code[10:]
            elif (code[0:9] == 'RMCA_Mam_'):
                code1 = code[0:9]
                code2 = code[9:]
            else :
				code1 = ''
				code2 = code
            ig = unicode(record['ig_num'])

            #######other
            instit = u"RMCA Tervuren(KMMA/MRAC)"
            dateLabel = unicode(record['current_date'])

            #######Locality
            loc2 = ""
            if record['municipality'] != "":
                Munisite = record['municipality'] + ", "+ record['site']
            else:
                Munisite = record['site']
            if Munisite != "":
                padd = 0
                padd2 = 0
                if len(Munisite) > 38 :
                    p = Munisite.find(',')
                    padd = 1
                    if p == -1:
                        p = Munisite.find(' ')
                        padd = 1
                    end = p+1
                    start = p+padd
                    loc = unicode(Munisite)[:end]
                    loc2 = unicode(Munisite)[start:]
                    if len(loc2) > 38 :
						p2 = loc2.find(',')
						padd2 = 1
						if p2 == -1:
							p2 = loc2.find(' ')
							padd2 = 1
						end = p+p2+1
						start = p+p2+padd2
						loc = unicode(Munisite)[:end]
						loc2 = unicode(Munisite)[start:]
                else:
                    loc = unicode(Munisite)
            else:
                loc = u"/"
            country = unicode(record['country'])
            Coordsource = record['coordinates_source']
            longsec = record['longitude_dms_seconds']
            latsec = record['latitude_dms_seconds']
            latitude = unicode(record['latitude'])
            longitude = unicode(record['longitude'])
            latitudeDMS = unicode(record['latitudedms'])
            longitudeDMS = unicode(record['longitudedms'])
            #print("coord"+Coordsource)
            if Coordsource == "DD":
                if latitude != "":
                    latlong= "Lat: " + latitude + " Long: " + longitude
            elif latitudeDMS != "" :
                #print(" len sec="+str(len(latsec)) + " latitude="+latitude)
                if (len(latsec) > 6) | (len(longsec) > 6):
                    #print(" sec="+latsec + " latitude="+latitude)
                    if latitude != "":
                        latlong= "Lat: " + latitude + " Long: " + longitude
                else :
                    latlong = "Lat: " + latitudeDMS + " Long: " + longitudeDMS
            else:
                latlong = u"Lat : /      Long: /"

            #######properties
            prop1 = ""
            prop2 = ""
            prop3 = ""
            prop4 = ""
            if record['dna'].strip() != "" :
                prop1 =  unicode(record['dna']) + ", "
            if record['collector_field_number'].strip() != "" :
                prop2 = "coll field nbr:" + unicode(record['collector_field_number'])
            if record['parasite'].strip() != "":
                prop3 = "parasit.:" + unicode(record['parasite'])+ ", "
            if record['identification_extra_info'].strip() != "":
                prop3 = prop3 + "ident.:" + unicode(record['identification_extra_info'])
            if record['fixation_extra_info'].strip() != "":
                prop4 = "Fix.:" + unicode(record['fixation_extra_info']) + ", "
            if record['dissection'].strip() != "":
                prop4 = prop4 + "Dissect.:" + unicode(record['dissection'])

            #######remove lines if empty
            Lines_to_remove1 = 0 #type missing
            Lines_to_remove2 = 0 #properties missing
            Lines_to_remove2a = 0
            Lines_to_remove2b = 0
            Lines_to_remove2c = 0
            Lines_to_remove2d = 0
            Lines_to_remove3 = 0 #author missing
            Lines_to_remove4 = 0 #loc line 2 missing
            Lines_to_remove5 = 0 #add line if collecting date + recolt too long
            if loc2 == "" :
                Lines_to_remove4 = 3
            if author == "" :
                Lines_to_remove3 = 3
            if typesp == "" :
                Lines_to_remove1 = 3
				
            if prop1 == "" :
                Lines_to_remove2a = 2
            if prop2 == "":
                Lines_to_remove2b = 2
            if prop3 == "":
                Lines_to_remove2c = 2
            if prop4 == "":
                Lines_to_remove2d = 2
            Lines_to_remove2 = Lines_to_remove2a + Lines_to_remove2b + Lines_to_remove2c + Lines_to_remove2d
			
            col = 49 - len(dateRecol)
            if (len(recol) < (col - 7)) | (col == 49):
                Lines_to_remove5 = 3

            Lines_to_remove_coll6 = 0
            if collection == 6:
                Lines_to_remove_coll6 = 3
            ##print "lines=" + str(Lines_to_remove) + "lines2=" + str(Lines_to_remove_coll6)
            ####### start label creation #########################################################################
            fileprn.write(u"#!A1")
            # start
            fileprn.write(u"#N13")
            # Nationality of character set
            label_height = 48  - Lines_to_remove_coll6 - Lines_to_remove1 - Lines_to_remove2  - Lines_to_remove3 - Lines_to_remove4 - Lines_to_remove5
            ##print "height=" + str(label_height)
            fileprn.write(u"#IMNB50/"+str(label_height)+"/")
            # Material information : width=50, height = 35
            fileprn.write(u"#ERN1//")
            # Start of label format

            # line 44
            line = 44 - Lines_to_remove_coll6 - Lines_to_remove1 - Lines_to_remove2 - Lines_to_remove3 - Lines_to_remove4 - Lines_to_remove5
            fileprn.write(u"#T0#J"+str(line)+"#YN100/BI/40X33///{}                      #G".format(sciName))
            # T=Horizontal #print position
            # J=Vertical #print position
            # YT,YN=Text field font scale (T in mm, N in points)
            # BI = Bold italic
            # U = unicode
            # 40X33 = 40 = height of text, 33=width

            # line 41
            line = 41 - Lines_to_remove_coll6 - Lines_to_remove1 - Lines_to_remove2 - Lines_to_remove4 - Lines_to_remove5
            fileprn.write(u"#T3#J"+str(line)+"#YN902/U/29X25///{}                       #G".format(author))
            #fileprn.write(u"#T24#J25.6#YR0/0/0.3/23/3                   #G")

            # line 38
            line = 38 - Lines_to_remove_coll6  - Lines_to_remove2 - Lines_to_remove4 - Lines_to_remove5
            fileprn.write(u"#T20#J"+str(line)+"#YN202/UB/40X33///{}                     #G".format(typesp))

            # line 35
            line = 35 - Lines_to_remove_coll6  - Lines_to_remove2 - Lines_to_remove4 - Lines_to_remove5
            fileprn.write(u"#T0#J"+str(line)+"#YN902//29X25///Det:                    #G")
            fileprn.write(u"#T0#J"+str(line - 0.3)+"#YL0//0.2/4                         #G")
            # YL0//0.2/6 : line of type 0, 6mm long and 0.2 tick
            if det == u"/":
                fileprn.write(u"#T5#J" + str(line) + "#YN902/UB/29X25///{}              #G".format(det))
            else :
                fileprn.write(u"#T5#J" + str(line) + "#YN902/U/29X25///{}               #G".format(det))
            col = 48 - len(datedet) #37
            if len(det) < (col - 7) :
                fileprn.write(u"#T"+str(col)+"#J"+str(line)+"#YT101///{}                #G".format(datedet))
            else:
                fileprn.write(u"#T" + str(col) + "#J" + str(line - 2) + "#YT101///{}    #G".format(datedet))

            # line 21
            #if family != 'None':
            #    fileprn.write(u"#T0#J21#YT113////Fam :                                 #G")
            #    fileprn.write(u"#T0#J20.7#YL0//0.2/6                                   #G")
            #    fileprn.write(u"#T5#J21#YN902/UB/27X24///{}                            #G".format(family))
            #elif order != 'None':
            #    fileprn.write(u"#T0#J21#YT113////Ord  :                                #G")
            #    fileprn.write(u"#T0#J20.7#YL0//0.2/6                                   #G")
            #    fileprn.write(u"#T5#J21#YN902/UB/27X24///{}                            #G".format(order))

            # line 32
            line = 32 - Lines_to_remove2 - Lines_to_remove4 - Lines_to_remove5
            if collection != 6:
                fileprn.write(u"#T0#J"+str(line)+"#YN902//29X25///Nbr:                #G")
                fileprn.write(u"#T0#J"+str(line - 0.3)+"#YL0//0.2/4                     #G")
                fileprn.write(u"#T5#J"+str(line)+"#YN902/U/29X25///{}                   #G".format(nbrsex))

            # line 29
            line = 29 - Lines_to_remove2 - Lines_to_remove4 - Lines_to_remove5
            fileprn.write(u"#T0#J"+str(line)+"#YN902//29X25///Loc:                    #G")
            fileprn.write(u"#T0#J"+str(line - 0.3)+"#YL0//0.2/4                         #G")
            if loc == u"/":
                fileprn.write(u"#T5#J"+str(line)+"#YN902/UB/29X25///{}                  #G".format(loc))
            else :
                fileprn.write(u"#T5#J"+str(line)+"#YN902/U/29X25///{}                   #G".format(loc))

            # line 26
            line = 26 - Lines_to_remove2 - Lines_to_remove5
            fileprn.write(u"#T5#J"+str(line)+"#YN902/U/29X25///{}                       #G".format(loc2))

            # line 23
            line = 23 - Lines_to_remove2 - Lines_to_remove5
            fileprn.write(u"#T5#J"+str(line)+"#YN902/U/29X25///{}                       #G".format(country))

            # line 20
            line = 20 - Lines_to_remove2 - Lines_to_remove5
            fileprn.write(u"#T5#J"+str(line)+"#YN902/U/29X25///{}                       #G".format(latlong))

            # line 17
            line = 17 - Lines_to_remove2 - Lines_to_remove5
            fileprn.write(u"#T0#J"+str(line)+"#YN902//29X25///Rec:                    #G")
            fileprn.write(u"#T0#J"+str(line - 0.3)+"#YL0//0.2/4                         #G")
            if loc == u"/":
                fileprn.write(u"#T5#J"+str(line)+"#YN902/UB/29X25/// {}                 #G".format(recol))
            else :
                fileprn.write(u"#T5#J"+str(line)+"#YN902/U/29X25/// {}                  #G".format(recol))

            # line 14
            line = 14 - Lines_to_remove2
            col2 = 49 - len(dateRecol)
            fileprn.write(u"#T" + str(col2) + "#J" + str(line) + "#YT101///{}           #G".format(dateRecol))

            # line 10.5
            line = 10.5 - Lines_to_remove2
            #fileprn.write(u"#T0#J" + str(line)+"#YN902//29X25///Code:                  #G")
            #fileprn.write(u"#T0#J" + str(line - 0.3) + "#YL0//0.2/6                    #G")
            fileprn.write(u"#T0#J" + str(line+0.5)+"#YT101////{}                   		#G".format(code1))
            if code1 == '':
                fileprn.write(u"#T0#J" + str(line)+"#YN902/UB/44X40///{}                    #G".format(code2))
            else:
			    fileprn.write(u"#T9#J" + str(line)+"#YN902/UB/44X40///{}                    #G".format(code2))

            # line 8.5
            line = 8.5 - Lines_to_remove2b - Lines_to_remove2c - Lines_to_remove2d
            #fileprn.write(u"#T0#J3.5#YT113////IG    :                                  #G")
            #fileprn.write(u"#T0#J3.2#YL0//0.2/6                                        #G")
            #fileprn.write(u"#T5#J3.5#YN902/UB/27X24///{}                               #G".format(ig))
            fileprn.write(u"#T0#J"+str(line)+"#YT101//// {}                             #G".format(prop1))

            # line 6.5
            line = 6.5 - Lines_to_remove2c - Lines_to_remove2d
            #fileprn.write(u"#T0#J3.5#YT113////IG    :                                  #G")
            #fileprn.write(u"#T0#J3.2#YL0//0.2/6                                        #G")
            #fileprn.write(u"#T5#J3.5#YN902/UB/27X24///{}                               #G".format(ig))
            fileprn.write(u"#T0#J"+str(line)+"#YT101//// {}                             #G".format(prop2))

            # line 4.5
            line = 4.5 - Lines_to_remove2d
            #fileprn.write(u"#T0#J3.5#YT113////IG    :                                  #G")
            #fileprn.write(u"#T0#J3.2#YL0//0.2/6                                        #G")
            #fileprn.write(u"#T5#J3.5#YN902/UB/27X24///{}                               #G".format(ig))
            fileprn.write(u"#T0#J"+str(line)+"#YT101//// {}                             #G".format(prop3))

            # line 2.5
            line = 2.5
            #fileprn.write(u"#T0#J1#YT101//// {}                                        #G".format(instit))
            fileprn.write(u"#T0#J"+str(line)+"#YT101//// {}                             #G".format(prop4))

            # line 0.5
            line = 0.5
            # fileprn.write(u"#T0#J1#YT101//// {}                                        #G".format(instit))
            fileprn.write(u"#T37#J" + str(line) + "#YT101//// {}                         #G".format(dateLabel))

            # line 3 (circle to attach to specimen)
            #fileprn.write(u"#T46.8#J3.0#YN902/2B/51///o                                #G")
            #fileprn.write(u"#T46.9#J3.1#YN902/2B/61///o                                #G")
            #fileprn.write(u"#T47.0#J3.2#YN902/2B/61///o                                #G")

            fileprn.write(u"#Q1/")

            tmplabel= fileprn.getvalue()
            #print(tmplabel)
            #fileprn.close()
            return tmplabel
        except Exception, e:
            printException()
        finally:
            if self.m_mutex.locked(): 
                self.m_mutex.release()


