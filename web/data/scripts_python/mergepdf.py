#!/usr/bin/env python
# -*- coding: UTF-8 -*-

from wsgiref.simple_server import make_server
from cgi import parse_qs, escape
from pyPdf import PdfFileWriter, PdfFileReader
from urllib2 import Request, urlopen
from StringIO import StringIO

def append_pdf(output,input):
    for pageNum in xrange(input.getNumPages()):
        currentPage = input.getPage(pageNum)
        #currentPage.mergePage(watermark.getPage(0))
        output.addPage(currentPage)
    
def application(environ, start_response):
    status = '200 OK'
    
    response_headers = [('Content-type', 'application/pdf')]
    d = parse_qs(environ['QUERY_STRING'])
    loan_id = d.get('loan', [''])[0]
       
    writer = PdfFileWriter()

    url_1 = "http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_loans%3Aloans_exped.prpt/report?LOAN_ID="+str(loan_id)+"&userid=report&password=report&output-target=pageable%2Fpdf&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=true"
    remoteFile1 = urlopen(Request(url_1)).read()
    memoryFile1 = StringIO(remoteFile1)
    pdfFile1 = PdfFileReader(memoryFile1)
    page = pdfFile1.getPage(0)
    page.rotateClockwise(270)
    writer.addPage(page)
    writer.addBlankPage()
    writer.addPage(page)
    writer.addBlankPage()
	
    url_2 = "http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_loans%3Aloans_cites-danger.prpt/report?LOAN_ID="+str(loan_id)+"&userid=report&password=report&output-target=pageable%2Fpdf&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=true"
    remoteFile2 = urlopen(Request(url_2)).read()
    memoryFile2 = StringIO(remoteFile2)
    pdfFile2 = PdfFileReader(memoryFile2)
    append_pdf(writer, pdfFile2)
    writer.addBlankPage()
    append_pdf(writer, pdfFile2)
    writer.addBlankPage()
	
    url_3 = "http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_loans%3Aloans_lettre_intro.prpt/report?LOAN_ID="+str(loan_id)+"&userid=report&password=report&output-target=pageable%2Fpdf&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=true"
    remoteFile3 = urlopen(Request(url_3)).read()
    memoryFile3 = StringIO(remoteFile3)
    pdfFile3 = PdfFileReader(memoryFile3)
    append_pdf(writer, pdfFile3)
    writer.addBlankPage()
    
    url_4 = "http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_loans%3Aloans_prod.prpt/report?LOAN_ID="+str(loan_id)+"&userid=report&password=report&output-target=pageable%2Fpdf&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=true"
    remoteFile4 = urlopen(Request(url_4)).read()
    memoryFile4 = StringIO(remoteFile4)
    pdfFile4 = PdfFileReader(memoryFile4)
    append_pdf(writer, pdfFile4)
    nbrpages = pdfFile4.getNumPages()
    if nbrpages % 2 != 0 :
        writer.addBlankPage()
    append_pdf(writer, pdfFile4)
    if nbrpages % 2 != 0 :
        writer.addBlankPage()
	
    url_5 = "http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_loans%3Aloans_prod_copy.prpt/report?LOAN_ID="+str(loan_id)+"&userid=report&password=report&output-target=pageable%2Fpdf&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=true"
    remoteFile5 = urlopen(Request(url_5)).read()
    memoryFile5 = StringIO(remoteFile5)
    pdfFile5 = PdfFileReader(memoryFile5)
    append_pdf(writer, pdfFile5)
	
    #url_6 = "http://172.16.11.138:8080/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_loans%3Aloans_cites-danger_return.prpt/report?LOAN_ID="+str(loan_id)+"&userid=report&password=report&output-target=pageable%2Fpdf&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=true"
    #remoteFile6 = urlopen(Request(url_6)).read()
    #memoryFile6 = StringIO(remoteFile6)
    #pdfFile6 = PdfFileReader(memoryFile6)
    #append_pdf(writer, pdfFile6)
    
    outputStream = StringIO()
    writer.write(outputStream)
    merged= outputStream.getvalue()
    outputStream.close()
    memoryFile1.close()
    memoryFile2.close()
    memoryFile3.close()
    memoryFile4.close()
    memoryFile5.close()
    #memoryFile6.close()
    
    start_response(status, response_headers)
    return [merged]