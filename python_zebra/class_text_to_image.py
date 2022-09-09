from PIL import ImageFont, ImageDraw, Image
import numpy as np
from datetime import date

class Class_text_img():

    def __init__(self):
        self.fontColor              = (0,0,0)
        self.thickness              = 1
        self.lineType               = 1
        self.WIDTH=512
        self.HEIGHT=612
        self.BOTTOM_PAD=40
        self.FONTPATH ="/var/thermic_printer_zebra/ttf/SourceSerifPro-It.ttf"
        self.FONTPATH_ARIAL ="/var/thermic_printer_zebra/ttf/arial.ttf" 
        self.FONTPATH_ARIALBOLD ="/var/thermic_printer_zebra//ttf/calibrib.ttf"
        self.interligne=5        
        
    def set_font_color(self, font_color):
        self.fontColor=font_color7
        
    def set_thickness(self, thickness):
        self.thickness=thickness
        
    def set_linewidth(self, linetype):
        self.lineType=linetype
        
    def set_width(self, width):
        self.WIDTH=width
        
    def set_height(self, height):
        self.HEIGHT=height
        
    def set_bottom_pad(self, pad):
        self.BOTTOM_PAD=pad
        
    def write_left(self, draw , position, text, font, fill=None, stroke_width=0):
        if fill is None:
            fill=self.fontColor
        draw.text(position,  text=text,  font=font, fill=fill, stroke_width=stroke_width)
        line_size=draw.textsize(text, font = font)
        return line_size
        
    def write_right(self, draw , width, offset_h, offset_v,  text, font, fill=None, stroke_width=0):
        if fill is None:
            fill=self.fontColor    
        line_size=draw.textsize(text, font = font)
        line_size=(width-(line_size[0]+offset_h), offset_v)
        draw.text(line_size,  text=text,  font=font, fill=fill, stroke_width=stroke_width)
        return line_size
        
    def write_line(self, draw, position, text, font, fill=None):
        if fill is None:
            fill=self.fontColor    
        line_size_l=draw.textsize(text, font = font)
        draw.line((position[0], position[1], position[0]+line_size_l[0], position[1]), fill=fill)
        return line_size_l
        
    def build_label(self, sci_name=None, author=None, typesp=None, det=None, det_year=None, code1=None, code2=None, nbr=None, locality=None, country=None, latlong=None, collector=None, collecting_date=None, collection=None, prop1= None, prop2=None, prop3=None, prop4=None, prop5=None):
        if not sci_name is None:
            if len(sci_name.strip())==0:
                sci_name=None
        if not author is None:
            if len(author.strip())==0:
                author=None        
        if not typesp is None:
            if len(typesp.strip())==0:
                typesp=None
        if not det is None:
            if len(det.strip())==0:
                det=None        
        if not det_year is None:
            if len(det_year.strip())==0:
                det_year=None 
        if not code1 is None:
            if len(code1.strip())==0:
                code1=None
        if not code2 is None:
            if len(code2.strip())==0:
                code2=None
        if not country is None:
            if len(country.strip())==0:
                country=None
        if not latlong is None:
            if len(latlong.strip())==0:
                latlong=None
        if not collector is None:
            if len(collector.strip())==0:
                collector=None
        if not collecting_date is None:
            if len(collecting_date.strip())==0:
                collecting_date=None                 
        if not collection is None:
            if len(collection.strip())==0:
                collection=None
        if not prop1 is None:
            if len(prop1.strip())==0:
                prop1=None
        if not prop2 is None:
            if len(prop2.strip())==0:
                prop2=None   
        if not prop3 is None:
            if len(prop3.strip())==0:
                prop3=None
        if not prop4 is None:
            if len(prop4.strip())==0:
                prop4=None   
        if not prop5 is None:
            if len(prop5.strip())==0:
                prop5=None                  
                
        GARAMOND_ITALIC = ImageFont.truetype(self.FONTPATH, 24)
        ARIAL_M = ImageFont.truetype(self.FONTPATH_ARIAL, 18)    
        ARIAL_S = ImageFont.truetype(self.FONTPATH_ARIAL, 16)
        ARIAL_BN = ImageFont.truetype(self.FONTPATH_ARIALBOLD, 34)        
        img = np.zeros((self.HEIGHT,self.WIDTH,3), np.uint8)
        img.fill(255)
        v=30        
        img_pil = Image.fromarray(img)
        draw = ImageDraw.Draw(img_pil)
        
        previous_line_length=0
        if not sci_name is None:
            line_size = self.write_left(draw, (40, v), sci_name, GARAMOND_ITALIC, self.fontColor)
            ##print(line_size)
            v=v+line_size[1]+self.interligne
            ##print(v)
        if not author is None:
            line_size = self.write_left(draw, (50, v), author, ARIAL_M)
            v=v+line_size[1]+self.interligne
        if not typesp is None:
            line_size = self.write_left(draw, (80, v), typesp, ARIAL_M)
            v=v+line_size[1]+2
            line_size = self.write_line(draw, (80, v), typesp, ARIAL_M)
            
        if not det is None:
            det1 = None
            det2 = None
            if len(det) > 36 :
                p = det[:36].rfind(' ')
                det1 = det[:p]                   
                det2 = det[p:]             
                line_size = self.write_left(draw, (40, v), "Det: " + det1, ARIAL_M)
                vline=v+line_size[1]+2
                line_size = self.write_line(draw, (40, vline), "Det:", ARIAL_M)
                v=v+line_size[1]+2
                line_size = self.write_left(draw, (80, v), det2, ARIAL_M)
                v=v+line_size[1]+2
            else:
                line_size = self.write_left(draw, (40, v), "Det: " + det, ARIAL_M)
                vline=v+line_size[1]-2
                line_size = self.write_line(draw, (40, vline), "Det:", ARIAL_M)
                v=v+line_size[1]+2
            if not det_year is None:
                '''
                #print(line_size)
                #print(self.WIDTH)
                #print(draw.textsize("Det: " + det, font = ARIAL_S))
                #print(draw.textsize("Det: " + det, font = ARIAL_S)[0])
                #print(self.WIDTH - (draw.textsize("Det: " + det, font = ARIAL_S)[0]) - 35)
                if line_size[0]> (self.WIDTH - (draw.textsize("Det: " + det, font = ARIAL_S)[0]) - 35):
                    v=v+line_size[1]+2
                '''
           
                line_size_right = self.write_right(draw, self.WIDTH, 35, v-3, det_year, ARIAL_S)
                v=v+line_size[1]+2
                
                previous_line_length
        if not nbr is None:
            line_size = self.write_left(draw, (40, v), "Nbr: "+ nbr, ARIAL_M)
            v=v+line_size[1]+2
            line_size = self.write_line(draw, (40, v), "Nbr:", ARIAL_M)
        offset_loc=40+draw.textsize("Loc2", font = ARIAL_M)[0]            
        if not locality is None:
            loc = None
            loc2 = None
            loc3 = None
            loc4 = None
            if len(locality) > 36 :
                    p = locality[:36].rfind(' ')
                    loc = locality[:p]                   
                    loc2 = locality[p:]
                    if len(loc2) > 36 :
                        p2 = loc2[:36].rfind(' ')
                        loc2 = loc2[:p2]
                        loc3 = loc2[p2:]
                        if len(loc3) > 36 :
                            p3 = loc3[:36].rfind(' ')
                            loc3 = loc3[:p3]
                            loc4 = loc3[p3:]
            else:
                loc=locality
            if loc is not None:
                #print("DEBUG_LOC")
                #print(loc)
                line_size = self.write_left(draw, (40, v), "Loc: "+ loc, ARIAL_M)
                v=v+line_size[1] + self.interligne
                line_size = self.write_line(draw, (40, v-7), "Loc:", ARIAL_M)
            #offset_loc=40+draw.textsize("Loc2", font = ARIAL_M)[0]
            if loc2 is not None:
                line_size = self.write_left(draw, (offset_loc, v), loc2, ARIAL_M)
                v=v+line_size[1] + self.interligne
            if loc3 is not None:
                line_size = self.write_left(draw, (offset_loc, v), loc3, ARIAL_M)
                v=v+line_size[1] + self.interligne
            if loc4 is not None:
                line_size = self.write_left(draw, (offset_loc, v), loc4, ARIAL_M)
                v=v+line_size[1] + self.interligne
        if not country is None:
            #print("DEBUG_COUNTRY")
            #print(country)
            line_size = self.write_left(draw, (offset_loc, v), country, ARIAL_S)
            v=v+line_size[1] + self.interligne
        if not latlong is None:
            line_size = self.write_left(draw, (offset_loc, v), latlong, ARIAL_M)
            v=v+line_size[1] + self.interligne
        if not collector is None:
            line_size = self.write_left(draw, (40, v), "Rec: "+ collector, ARIAL_M)
            v=v+line_size[1]+2
            line_size = self.write_line(draw, (40, v-3), "Rec:", ARIAL_M)
            v=v+line_size[1]+2
        if not collecting_date is None:
            line_size_right = self.write_right(draw, self.WIDTH, 35,  v - line_size[1] - self.interligne, collecting_date, ARIAL_S)
        if not collection is None:
            line_size = self.write_left(draw, (40, v), collection, ARIAL_S)
            v=v+line_size[1]+2            
        if not code1 is None:
            line_size = self.write_left(draw, (40, v), code1, ARIAL_BN)
            v=v+line_size[1]+self.interligne
        if not code2 is None:
            line_size = self.write_left(draw, (40, v), code2, ARIAL_BN)
            v=v+line_size[1]+self.interligne
        if not prop1 is None:
            line_size = self.write_left(draw, (40, v), prop1, ARIAL_S)
            v=v+line_size[1]+2
        if not prop2 is None:
            line_size = self.write_left(draw, (40, v), prop2, ARIAL_S)
            v=v+line_size[1]+2
        if not prop3 is None:
            line_size = self.write_left(draw, (40, v), prop3, ARIAL_S)
            v=v+line_size[1]+2
        if not prop4 is None:
            line_size = self.write_left(draw, (40, v), prop4, ARIAL_S)
            v=v+line_size[1]+2 
        if not prop5 is None:
            line_size = self.write_left(draw, (40, v), prop5, ARIAL_S)
            v=v+line_size[1]+2             
        today = date.today()
        printing_date = today.strftime("%d/%m/%Y")
        #line_size = self.write_right(draw, self.WIDTH, 20,  v , printing_date, ARIAL_S)
        #v=v+line_size[1]
        size_date=draw.textsize(printing_date, font = ARIAL_S)
        line_size = self.write_left(draw, (self.WIDTH-(size_date[0]+35), v), printing_date, ARIAL_S)
        
        v=v+line_size[1]
        img = np.array(img_pil)
        img = img[ 0:v+self.BOTTOM_PAD, 0:self.WIDTH]
        #img_pil.save("/var/thermic_printer_zebra/debug.png")
        return img