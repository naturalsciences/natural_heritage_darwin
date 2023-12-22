import socket

LOCAL_PATH="zebra_debug.txt"


f = open(LOCAL_PATH, 'rb')
all_v=b""
byte = f.read(1)
all_v=all_v+byte
while byte != b"":
    # Do stuff with byte.
    byte = f.read(1)
    print(byte)
    all_v=all_v+byte
print(all_v)


host = "172.16.1.200"
port = 9100                   # The same port as used by the server
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect((host, port))
s.sendall(all_v)
#data = s.recv(1024)
s.close()
print('Received')