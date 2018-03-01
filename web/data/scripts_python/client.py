import socket, select, sys



class Client(object):
    def __init__(self, p_message, p_ip='0.0.0.0', p_port=8091):
        self.m_message = p_message
        self.m_ip = p_ip
        self.m_port = p_port
    

    def call_service(self):
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.connect((self.m_ip, self.m_port))
        s.send(self.m_message)
        #socket_list = [sys.stdin, s]
        #s.shutdown()
        s.close()
        
'''
        while 1:
            read_sockets, write_sockets, error_sockets = select.select(socket_list, [], [])


            for sock in read_sockets:
                # incoming message from remote server
                if sock == s:
                    data = sock.recv(4096)
                    if not data:
                        print('\nDisconnected from server')
                        sys.exit()
                    else:
                        sys.stdout.write("\n")
                        message = data.decode()
                        sys.stdout.write(message)
                        sys.stdout.write('<Me> ')
                        sys.stdout.flush()

                else:
                    msg = sys.stdin.readline()
                    s.send(bytes(msg))
                    sys.stdout.write('<Me> ')
'''