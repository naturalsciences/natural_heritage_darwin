import argparse
from client import Client
from print_thermic_client_class import DarwinClientParser


PG_CONNEX = "host='172.16.11.113' dbname='darwin2' user='darwin2' password='darwin123'"
LOGFILE = 'D:\Thermic_printer\log\main_printer_darwin.log' 
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
        label_text = label_tool.define_and_print_label(args.specimen_id)
        #print('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!')
        #print(label_text)
        DarwinPrinterClient=Client(label_text, IP_SERVICE, PORT)
        DarwinPrinterClient.call_service()