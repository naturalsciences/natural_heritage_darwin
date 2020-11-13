#script to check the association between EOD files (electric discharge organs) and Darwin number
# example Excel contains:
#   a column with the Darwin number eg: RMCA 2018-32
#   b a column with a field number or eod number (integer) eg : 3455
#   c=> the files eod_3455_A.csv and 3433_B.wav are to be linked to RMCA 2018-32
# output Excel contains 3 sheets : matched files, "orphans" (file in the folder without association) and not found (files do not  exist)

#-i input csv file containing the EODs and the Darwin number
#-f folder containing the EODs files
#-o the name of the output Excel file
#-dw the position of the column with the Darwin number
#-c the position of the column with the EOD number
#-d the delimiter in the source CSV (optional, by default \t)

#example of invocation
#C:\Users\ftheeten\Downloads>python parse_eod_multimedia.py -i "C:\Users\ftheeten\Downloads\mukweze_eod\Collection 2018-032 Expédition EOD 2018.txt -f #C:\Users\ftheeten\Downloads\mukweze_eod\wetransfer-57f865\Mukweze original EODs part 2A" -o "mukweze_eod_to_darwin" -dw 1 -c 35


import argparse, csv, re, xlsxwriter
from os import walk



def parse_file(inputfile, inputfolder, outputfile, position_column_darwin, position_column_eod, delimiter):
    
    dict_association={}
    eod_files = []
    all_matched=[]
    for (dirpath, dirnames, filenames) in walk(inputfolder):
        eod_files.extend(filenames)
        break
    print(eod_files)
    with open(inputfile, newline='') as csvfile:
        reader = csv.reader(csvfile, delimiter=delimiter)
        for row in reader:
            print(row[position_column_darwin-1])
            print(row[position_column_eod-1])
            darwin_number=row[position_column_darwin-1]
            eod_number=row[position_column_eod-1]            
            pattern=re.compile('.*[^0-9]'+eod_number+"[^0-9]?.*\..+" )
            print(pattern)
            matched = list(filter(pattern.match, eod_files)) # Read Note
            print(matched)
            dict_association[darwin_number]={'eod_radical':eod_number, 'files':matched}
            all_matched=all_matched+matched
    print(dict_association)
    orphans=list(set(eod_files) - set(all_matched))
    print(orphans)
    not_found = { key:value for (key,value) in dict_association.items() if len(value['files']) == 0}
    found = { key:value for (key,value) in dict_association.items() if len(value['files']) > 0}
    print(not_found)
    workbook= xlsxwriter.Workbook(outputfile+'.xlsx')
    bold = workbook.add_format({'bold': True})
    
    matches_sheet=workbook.add_worksheet("matches")    
    matches_sheet.write_row(0,0, ["unitid", "file", "full_path"], bold)   
    i_row=1    
    for key, val in found.items():
        dw=key
        for file in val['files']:
            matches_sheet.write_row(i_row, 0, [str(key), str(file), inputfolder+'\\'+str(file)])
            i_row+=1 

    orphans_sheet=workbook.add_worksheet("orphans")  
    orphans_sheet.write_row(0,0, ["file", "full_path"], bold) 
    i_row=1        
    for  file in orphans:
        orphans_sheet.write_row(i_row, 0, [str(file), inputfolder+'\\'+str(file)])
        i_row+=1                
    
    
    notfound_sheet=workbook.add_worksheet("not_found")  
    notfound_sheet.write_row(0,0, ["darwin_number, eod_radical"], bold) 
    i_row=1        
    for  key, val in not_found.items():
        notfound_sheet.write_row(i_row, 0, [key, val['eod_radical']])
        i_row+=1                
    
    workbook.close()
    
    
    
if __name__ == "__main__":
   
   parser = argparse.ArgumentParser()
   parser.add_argument("-i",  help="inputfile", required=True)
   parser.add_argument("-f",  help="inputfolder", required=True)
   parser.add_argument("-o",   help="output file", required=True)
   parser.add_argument("-c", type=int, help="position column with eod pattern" , required=True)
   parser.add_argument("-dw", type=int, help="position column with darwin number" , required=True)
   parser.add_argument("-d",  help="field delimiter", default='\t')
   args = vars(parser.parse_args())
   print(args)
   parse_file(args['i'], args['f'], args['o'], args['dw'], args['c'], args['d'])