import argparse
from client import Client
from print_thermic_client_class import DarwinClientParser
import io
from threading import Lock
import socket, select, sys


PG_CONNEX = "host='172.16.11.132' dbname='darwin2' user='darwin2' password='darwin123'"
LOGFILE = 'C:\data\thermic_printer\log\main_printer_darwin.log' 
LOGFILE = 'D:\Thermic_printer\log\main_printer_darwin.log' 

#IP_SERVICE =  '172.16.1.7'
IP_SERVICE =  '172.16.11.105'
PORT = 8091

def RepresentsInt(s):
    try: 
        int(s)
        return True
    except ValueError:
        return False
'''
print("Test")
value = raw_input("id darwin ?")
print(value)

DarwinPrinterClient=Client(value, '172.16.1.7', 8091)
DarwinPrinterClient.call_service()
'''

if __name__ =='__main__':
    parser = argparse.ArgumentParser(description='Darwin Avery Dennison print spooler')
    parser.add_argument('--specimen_id', '-s', help='darwin2 specimen id')
    args= parser.parse_args()
    #print(args.specimen_id)
    if RepresentsInt(args.specimen_id) is True:
        label_tool = DarwinClientParser(LOGFILE)
        label_tool.set_pg_connection(PG_CONNEX)
        fileprn = io.StringIO()
        label_text = label_tool.define_and_print_label(args.specimen_id, fileprn)
        #print(label_text)
        DarwinPrinterClient=Client(label_text, IP_SERVICE, PORT)
        DarwinPrinterClient.call_service()
        fileprn.close()
    elif '_' in args.specimen_id:
        m_mutex = Lock()
        label_tool = DarwinClientParser(LOGFILE)
        label_tool.set_pg_connection(PG_CONNEX)
        for p_id in args.specimen_id.split("_") :
            if RepresentsInt(p_id) is True:
                try:
                    m_mutex.acquire()
                    fileprn = io.StringIO()
                    label_text=""
                    label_text = label_tool.define_and_print_label(p_id, fileprn)
                    DarwinPrinterClient=Client(label_text, IP_SERVICE, PORT)
                    DarwinPrinterClient.call_service()
                    fileprn.close()
                except Exception, e:
                    #TODO more efficent exception capture
                    print("Issue")
                finally:
                    if m_mutex.locked(): 
                        m_mutex.release()