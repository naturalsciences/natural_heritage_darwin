import argparse
import pandas
import psycopg2
import sys
import chardet
import traceback
from pathlib import Path 

unit_col="unitid"
col_to_copy=["identifiedby", "collectedby","acquiredFrom"]

def load_dw_import(file, ip, db, user, password):
    rawdata = open(file, "rb").read()
    result = chardet.detect(rawdata)
    charenc = result['encoding']
    print(charenc)
    #rawdata.close()
    connect_str = "dbname='"+db+"' user='"+user+"' host='"+ip+"' password='"+password+"'"
    #print(connect_str)
    conn = psycopg2.connect(connect_str)
    cursor = conn.cursor()
    print(file)
    my_data = pandas.read_csv(file, sep='\t', encoding=charenc)
    my_data.columns=my_data.columns.str.lower()
    #print(my_data)
    sql = """INSERT INTO people_align_debug(filename,unitid, people_role, people_name ) VALUES(%s, %s, %s, %s);"""
    for ind in my_data.index:
        #print(row)
        for col in col_to_copy:
            #print(col)
            #print("===>")
            if unit_col in my_data:
                if col in my_data:
                    #print(my_data[col][ind])
                    if(not pandas.isnull(my_data[col][ind])):
                        cursor.execute(sql, (file, my_data[unit_col][ind],col ,my_data[col][ind] ))
                        conn.commit()
    cursor.close()
    conn.close()

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--file")
    parser.add_argument("--ip")
    parser.add_argument("--db")
    parser.add_argument("--user")
    parser.add_argument("--password")
    args = parser.parse_args()
    if ";" in args.file:
        files= args.file.split(";")
        for file in files:
            try:
                print(file)
                load_dw_import(file.strip(), args.ip, args.db, args.user, args.password)
            except:
                print("Oops!", sys.exc_info()[0], "occurred.")
                traceback.print_exc() 
    #else:
    #    load_dw_import(args.file, args.ip, args.db, args.user, args.password)
    
'''
CORRESPONDING SQL


--
with a as
(
SELECT * from people_align_debug a left join v_imports_filename_encoded b on replace(a.filename,'.txt', '')=b.filename_encoded),
b as(
select distinct unitid,specimens.id from a inner join specimens on a.collection_ref=specimens.collection_ref and fulltoindex(unitid,false)=fulltoindex(main_code_indexed, false))
UPDATE people_align_debug set specimen_fk=id from b where people_align_debug.unitid=b.unitid;

with c as (select * from people_align_debug),
d as (
select * from c inner join people on trim(fulltoindex(people_name, true)) = trim(fulltoindex(formated_name, true))
)
update people_align_debug SET people_fk=d.id from d where people_align_debug.people_name=d.people_name;

with f
as
(
select * from people_align_debug where people_fk is null and people_name is not null)
insert into people (family_name) select people_name from f;

with c as (select * from people_align_debug),
d as (
select * from c inner join people on trim(fulltoindex(people_name, true)) = trim(fulltoindex(family_name, true))
)
update people_align_debug SET people_fk=d.id from d where people_align_debug.people_name=d.people_name where people_fk is null;

--LINK TO SPEC
with link_spec as
with link_spec as
(
select people_align_debug.filename, unitid, specimens.id from people_align_debug inner  join v_imports_filename_encoded on people_align_debug.filename=filename_encoded||'.txt'

inner join codes on LOWER(unitid)=LOWER(TRIM(COALESCE(code_prefix,'')||COALESCE(code_prefix_separator,'')||COALESCE(code,'')||COALESCE(code_suffix_separator,'')||COALESCE(code_suffix,''))) and referenced_relation='specimens' and code_category='main'
inner join specimens on codes.record_id=specimens.id  and v_imports_filename_encoded.collection_ref=specimens.collection_ref )
UPDATE people_align_debug set specimen_fk =id from link_spec where  people_align_debug.unitid=link_spec.unitid
;
--DIAG ALL
with a as (select people_align_debug.*, v_imports_filename_encoded.id from people_align_debug inner join 
v_imports_filename_encoded on people_align_debug.filename=filename_encoded||'.txt'

 where specimen_fk is not null)

 select a.* , spec_coll_ids, spec_ident_ids, spec_don_sel_ids from a inner join specimens on a.specimen_fk=specimens.id;
--DIAG COLL

with a as (select people_align_debug.*, v_imports_filename_encoded.id from people_align_debug inner join 
v_imports_filename_encoded on people_align_debug.filename=filename_encoded||'.txt'

 where specimen_fk is not null)

 select a.* , spec_coll_ids, spec_ident_ids, spec_don_sel_ids from a inner join specimens on a.specimen_fk=specimens.id where people_role = 'collectedby' and not( people_fk  = any (spec_coll_ids));
 
 --OR
 
 with a as (select people_align_debug.*, v_imports_filename_encoded.id from people_align_debug inner join 
v_imports_filename_encoded on people_align_debug.filename=filename_encoded||'.txt'

 where specimen_fk is not null)

 select a.* , spec_coll_ids, spec_ident_ids, spec_don_sel_ids from a inner join specimens on a.specimen_fk=specimens.id where people_role = 'collectedby' and coalesce(array_length(spec_coll_ids,1),0)=0;
 
 --REPAIr COLLECTORS
 
 with a as (select people_align_debug.*, v_imports_filename_encoded.id from people_align_debug inner join 
v_imports_filename_encoded on people_align_debug.filename=filename_encoded||'.txt'

 where specimen_fk is not null),
 b 
 as

 (select distinct unitid, people_role, people_name, people_fk, specimen_fk,spec_coll_ids, spec_ident_ids, spec_don_sel_ids from a inner join specimens on a.specimen_fk=specimens.id where people_role = 'collectedby' and coalesce(array_length(spec_coll_ids,1),0)=0)


insert into catalogue_people(referenced_relation, record_id, people_type, order_by, people_ref
) SELECT 'specimens', b.specimen_fk , 'collector', 1, b.people_fk from b
 ;

'''