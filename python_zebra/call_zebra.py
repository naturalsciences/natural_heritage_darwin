#!/usr/bin/python3
import sys
sys.path.append('/var/thermic_printer_zebra/')
import argparse
from threading import Lock
import sys
from label_darwin import DarwinClientZebra
import time

PG_CONNEX = "host='' dbname='' user='' password=''"

def RepresentsInt(s):
    try: 
        int(s)
        return True
    except ValueError:
        return False
        
if __name__ =='__main__':
    parser = argparse.ArgumentParser(description='Darwin Avery Dennison print spooler')
    parser.add_argument('--specimen_id', '-s', help='darwin2 specimen id')
    parser.add_argument('--user_ip', '-i', help='darwin2 user ip')
    args= parser.parse_args()
    specimenid = args.specimen_id
    
    if '_' in specimenid:
        print("serie")
        m_mutex = Lock()
        #label_tool = DarwinClientParser(LOGFILE)
        #label_tool.set_pg_connection(PG_CONNEX)
        for p_id in specimenid.split("_") :
            if RepresentsInt(p_id) is True:
                try:
                    print("launched");
                    m_mutex.acquire()
                    time.sleep(2)
                    #fileprn = io.StringIO()
                    label_tool = DarwinClientZebra()
                    label_tool.set_pg_connection(PG_CONNEX)
                    label_text = label_tool.define_and_print_label(p_id)
                    #fileprn.close()
                except Exception as e:
                    #TODO more efficent exception capture
                    print("Issue")
                    print("Unexpected error:", sys.exc_info()[0])
                finally:
                    if m_mutex.locked(): 
                        m_mutex.release()
    elif RepresentsInt(specimenid) is True:
        print("GO")
        label_tool = DarwinClientZebra()
        label_tool.set_pg_connection(PG_CONNEX)
        label_text = label_tool.define_and_print_label(specimenid)
    