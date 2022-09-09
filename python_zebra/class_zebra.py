#!/usr/bin/python3
import base64
import io
import numpy as np

class Class_zpl():
    
    
    def __init__(self):
        #print("construct")    
        self.blacklimit=int(20* 768/100)
        self.compress=False
        self.total=0
        self.width_byte=0
        self.mapCode =  dict()
        self.init_map_code()
        self.min_height=200
        self.current_height=200
    
    def init_map_code(self):   
        self.mapCode[1] = "G"
        self.mapCode[2] = "H"
        self.mapCode[3] = "I"
        self.mapCode[4] = "J"
        self.mapCode[5] = "K"
        self.mapCode[6] = "L"
        self.mapCode[7] = "M"
        self.mapCode[8] = "N"
        self.mapCode[9] = "O"
        self.mapCode[10] = "P"
        self.mapCode[11] = "Q"
        self.mapCode[12] = "R"
        self.mapCode[13] = "S"
        self.mapCode[14] = "T"
        self.mapCode[15] = "U"
        self.mapCode[16] = "V"
        self.mapCode[17] = "W"
        self.mapCode[18] = "X"
        self.mapCode[19] = "Y"
        self.mapCode[20] = "g"
        self.mapCode[40] = "h"
        self.mapCode[60] = "i"
        self.mapCode[80] = "j"
        self.mapCode[100] = "k"
        self.mapCode[120] = "l"
        self.mapCode[140] = "m"
        self.mapCode[160] = "n"
        self.mapCode[180] = "o"
        self.mapCode[200] = "p"
        self.mapCode[220] = "q"
        self.mapCode[240] = "r"
        self.mapCode[260] = "s"
        self.mapCode[280] = "t"
        self.mapCode[300] = "u"
        self.mapCode[320] = "v"
        self.mapCode[340] = "w"
        self.mapCode[360] = "x"
        self.mapCode[380] = "y"
        self.mapCode[400] = "z"
         
    def set_blacklight_percentage(self, pc):
        self.blacklimit=int(pc * 768/100)
        
    def set_compress(self, val):
        self.compress=val
        
    def set_min_height(self, min_height):
        self.min_height=min_height
        
    def four_byte_binary(self, binary_str):    
        decimal=int(binary_str, 2)
        if decimal>15:
            returned=hex(decimal).upper()
            returned=returned[2:]
        else:
            #returned=hex(decimal).upper()+"0"
            returned=hex(decimal).upper()
            #if binary_str!="00000000":
            #    print("cut="+returned)
            returned=returned[2:]
            returned="0"+returned
            #if binary_str!="00000000":
            #    print("low10="+returned)
        #
        #if binary_str!="00000000":
        #    print(binary_str+"\t"+str(decimal)+"\t"+returned+"\t")
        return returned
        
        
    def four_byte_binary(self, binary_str):    
        decimal=int(binary_str, 2)
        if decimal>15:
            returned=hex(decimal).upper()
            returned=returned[2:]
        else:
            #returned=hex(decimal).upper()+"0"
            returned=hex(decimal).upper()
            #if binary_str!="00000000":
            #    print("cut="+returned)
            returned=returned[2:]
            returned="0"+returned
            #if binary_str!="00000000":
            #    print("low10="+returned)        #
        #if binary_str!="00000000":
            #print(binary_str+"\t"+str(decimal)+"\t"+returned+"\t")
        return returned
    
    def createBody(self, img):
        height, width, colmap = img.shape
        self.current_height=max(height, self.min_height)
        rgb = 0
        index=0
        aux_binary_char=['0', '0', '0', '0', '0', '0', '0', '0']
        sb=[]
        if(width%8>0):
            self.width_byte=int((width/8)+1)
        else:
            self.width_byte=width/8
        self.total=self.width_byte*height
        i=0
        for h in range(0, height):
            for w in range(0, width):
                color = img[h,w]                
                blue=color[0]
                green=color[1]
                red=color[2]
                blue=blue  & 0xFF
                green=green  & 0xFF
                red=red  & 0xFF
                auxchar='1'
                total_color=red+green+blue
                if(total_color> self.blacklimit):                   
                    auxchar='0'
                aux_binary_char[index]=auxchar
                index=index+1
                if(index==8 or w==(width-1)):
                    #if "".join(aux_binary_char) !="00000000":
                    #    print(i)
                    sb.append(self.four_byte_binary("".join(aux_binary_char)))
                    i=i+1
                    aux_binary_char=['0', '0', '0', '0', '0', '0', '0', '0']
                    index=0           
            sb.append("\n")
        #print(sb)
        #print(self.blacklimit)
        return ''.join(sb)
        
    def encode_hex_ascii(self, code):
        max_linea=self.width_byte*2
        sb_code=[]
        sb_linea=[]
        previous_line=1
        counter=1
        aux = code[0]
        first_char=False
        for i in range(1, len(code)):
            if(first_char):
                aux=code[i]
                first_char=False
                continue
            if(code[i]=="\n"):
                if(counter>= max_linea and aux=='0'):
                    sb_linea.append(",")
                elif(counter>= max_linea and aux=='F'):
                    sb_linea.append("!")  
                elif(counter>20):
                    multi20=int((counter/20))*20
                    resto20=counter%20
                    sb_linea.append(self.mapCode[multi20])  
                    if(resto20!=0):
                        sb_linea.append(self.mapCode[resto20] +aux)  
                    else:
                        sb_linea.append(aux)
                else:
                    sb_linea.append(self.mapCode[counter] +aux)
                
                counter=1
                first_char=True
                if(''.join(sb_linea)==previous_line):
                    sb_code.append(":")
                else:
                    sb_code.append(''.join(sb_linea))
                previous_line=''.join(sb_linea)
                sb_linea=[]
                continue
            if aux==code[i]:
                counter=counter+1
            else:
                if counter>20:
                    multi20=int((counter/20))*20
                    resto20=counter%20
                    sb_linea.append(self.mapCode[multi20]) 
                    if resto20!=0:
                        sb_linea.append(self.mapCode[resto20] + aux)
                    else:
                        sb_linea.append(aux)
                else:
                    
                    sb_linea.append(self.mapCode[counter] + aux)
                counter=1
                aux=code[i]
        return ''.join(sb_code)
        
    def head_doc(self):
        return "^XA "  + "^LL"+ str(self.current_height) + "^PR1,1,1 ^FO0,0^GFA,"+ str(int(self.total)) + ","+ str(int(self.total)) + "," + str(int(self.width_byte)) +", "
        
    def foot_doc(self):
        return  "^FS"+ "^XZ"

    def process(self, img):
        self.init_map_code()
        cuerpo=self.createBody(img)       
        if self.compress:
            cuerpo=self.encode_hex_ascii(cuerpo)            
        return self.head_doc() + cuerpo + self.foot_doc()
        