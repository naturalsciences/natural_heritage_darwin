import subprocess
import io
import sys
import linecache
from threading import Lock

reload(sys)  # Reload does the trick!
sys.setdefaultencoding('UTF8')

def PrintException():
    try:
        exc_type, exc_obj, tb = sys.exc_info()
        f = tb.tb_frame
        lineno = tb.tb_lineno
        filename = f.f_code.co_filename
        linecache.checkcache(filename)
        line = linecache.getline(filename, lineno, f.f_globals)
        #print('EXCEPTION IN ({}, LINE {} "{}"): {}'.format(filename, lineno, line.strip(), exc_obj))
    except:
        Print("Exception dans l'exception !")
        



class AveryDennisonPrinterMap(object):
    def __init__(self, p_driver_file):
        self.m_driver_file = p_driver_file
        self.m_mutex = Lock()
        

    def print_label(self, p_tmp_label):
        try:            
            self.m_mutex.acquire()            
            p = subprocess.Popen('"'+self.m_driver_file + '" "/n:Default Settings" "/-"',  stdin=subprocess.PIPE,stdout=subprocess.PIPE)
            outs, errs =p.communicate(bytearray(p_tmp_label, 'latin1'))
            p.stdin.close()
        except Exception, e:
            print("Exception !")
            PrintException()
        finally:
            if self.m_mutex.locked(): 
                self.m_mutex.release()


