import argparse
from client import Client
from label_darwin import DarwinClientZebra
import io
from threading import Lock
import socket, select, sys

PG_CONNEX = "host='' dbname='' user='' password=''"
LOGFILE = 'D:\Thermic_printer\log\main_printer_darwin.log' 
#IP_SERVICE =  'fp2.museum.africamuseum.be'
#IP_SERVICE =  '172.16.1.12'
#IP_SERVICE =  'pcverteb716.museum.africamuseum.be'
PORT = 8091 

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
    IP_SERVICE = args.user_ip
    
    if RepresentsInt(specimenid) is True:
        print("GO")
        
        print(IP_SERVICE)
        print(PORT)
        label_tool = DarwinClientZebra()
        label_tool.set_pg_connection(PG_CONNEX)
        #fileprn = io.StringIO()
        label_text = label_tool.define_and_print_label(specimenid)
        #print(label_text)
        #DarwinPrinterClient=Client(label_text, IP_SERVICE, PORT)
        #DarwinPrinterClient.call_service()
        #fileprn.close()
    elif '_' in specimenid:
        m_mutex = Lock()
        label_tool = DarwinClientZebra(LOGFILE)
        label_tool.set_pg_connection(PG_CONNEX)
        for p_id in specimenid.split("_") :
            if RepresentsInt(p_id) is True:
                try:
                    print("launched");
                    m_mutex.acquire()
                    #fileprn = io.StringIO()
                    label_text=""
                    label_text = label_tool.define_and_print_label(p_id)
                    #DarwinPrinterClient=Client(label_text, IP_SERVICE, PORT)
                    #DarwinPrinterClient.call_service()
                    print("done");
                    #fileprn.close()
                except Exception, e:
                    #TODO more efficent exception capture
                    print("Issue")
                    print("Unexpected error:", sys.exc_info()[0])
                finally:
                    if m_mutex.locked(): 
                        m_mutex.release()