import socket
import select
import sys
from threading import Thread
from avery_dennison import AveryDennisonPrinterMap


AD_DRIVER='D:/Thermic_printer/prfile32'
TCP_IP = '0.0.0.0'
TCP_PORT = 8091
BUFFER_SIZE = 4096  # Normally 1024

class ServerThread(Thread):

    def __init__(self,ip,port, p_conn):
        Thread.__init__(self)
        self.ip = ip
        self.port = port
        self.conn = p_conn
        self.is_running = True
        #print "[+] New thread started for "+ip+":"+str(port)


    def run(self):
        while self.is_running is True:
            data = self.conn.recv(BUFFER_SIZE * 2)
            data = data.decode('utf-8')
            if not data: 
                #print("BREAK")
                break
            #print(data)
            darwin_printer_parser = AveryDennisonPrinterMap(AD_DRIVER)
            darwin_printer_parser.print_label(data)
        #print("SHOULD STOP")             


#MAIN#            
try:
    print("RUNNING")
    threads = []


    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    server_socket.bind((TCP_IP , TCP_PORT))
    server_socket.listen(10)


    read_sockets, write_sockets, error_sockets = select.select([server_socket], [], [])


    while True:
        #print "Waiting for incoming connections..."
        for sock in read_sockets:
            #print("CREATE THREAD")
            (conn, (ip,port)) = server_socket.accept()
            newthread = ServerThread(ip,port, conn)
            newthread.start()
            threads.append(newthread)

    for t in threads:
        t.join()
        #print('JOIN')
except:
    print "Unexpected error:", sys.exc_info()[0]
