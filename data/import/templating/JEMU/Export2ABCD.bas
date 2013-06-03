Attribute VB_Name = "Export2ABCD"
Option Explicit

' TO DO:
'-----------
'AJOUTER: comments

Dim Sample_ID, Field_ID, Museum_voucher_ID, Collection_Code, Institution_storing
Dim Sample_DonorPerson, Sample_DonorInstitution, Donor_Email
Dim Classification, Phylum, Class, Order, Family, Subfamily, Genus, Species, Subspecies, Identifier, Identifier_Institution
Dim Collector, Collection_Date, Continent, country, Province, Region, Municipality, Exact_Site, Latitude, Longitude, Elevation, Depth, Unit
Dim Comment1_Spec, Comment2_Spec, Comment3_Spec, Comment4_Spec, Comment5_Spec
Dim Sample_ID_Sample, Sample_2Dbarcode, Sample_type, Sample_description, Sample_protocol, Sample_preservation, Institution, Storage_rack, Storage_position
Dim Sample_ID_DNA, DNA_2Dbarcode, DNA_Sample_ID, DNA_quality, DNA_size, DNA_comment, DNA_rack_ID, DNA_position, Extraction_date
Dim Protocol, Tissue, Digestion_time, Digestion_volume, Elution, Elution_volume, Operator, Institute, Remarks, DNA_concentration

Public Sub CreateXML()

'**********************************************************************************
'| Purpose: Create XML file containing all data of the excel file (following ABCD schema)
'**********************************************************************************

'On Error GoTo Err_CreateXML

Define_Headings

CheckNumericAndDate

With Application
    .DisplayAlerts = False
    .ScreenUpdating = False
    .Cursor = xlWait
End With

If Check_Duplicates_SpecimenID = True Then
'    GoTo Exit_CreateXML
End If

If Check_Duplicates_SampleID = True Then
'    GoTo Exit_CreateXML
End If

If Check_Duplicates_DNAID = True Then
'    GoTo Exit_CreateXML
End If

AutoCompleteClassif

'**********************************************************************************
'Make copy of sheets for rework and initiate the XML file
'**********************************************************************************
If CopySheetsForRework Then

    Dim dom As MSXML2.DOMDocument60
    Dim root As MSXML2.IXMLDOMElement
    Dim firstnode As MSXML2.IXMLDOMNode
    Dim node As MSXML2.IXMLDOMNode
    Dim attr As MSXML2.IXMLDOMAttribute
    Dim subnode As MSXML2.IXMLDOMElement
    Dim strPath As String

    Set dom = New MSXML2.DOMDocument60
    dom.async = False
    dom.validateOnParse = False
    dom.resolveExternals = False
    dom.preserveWhiteSpace = True

    ' Create a processing instruction targeted for xml.
    Set node = dom.createProcessingInstruction("xml", "version=""1.0"" encoding=""UTF-8""")
    dom.appendChild node
    Set node = Nothing

    ' Create the root element.
    Set root = dom.createElement("DataSets")
    ' Add the root element to the DOM instance.
    dom.appendChild root
    Set attr = dom.createNode(NODE_ATTRIBUTE, "xmlns", "http://www.w3.org/2001/XMLSchema-instance")
    attr.Value = "http://www.tdwg.org/schemas/abcd/2.06"
    root.setAttributeNode attr
'    Set attr = dom.createNode(NODE_ATTRIBUTE, "xmlns:xs", "http://www.w3.org/2001/XMLSchema-instance")
'    attr.Value = "http://www.w3.org/2001/XMLSchema"
'    root.setAttributeNode attr
'    Set attr = dom.createNode(NODE_ATTRIBUTE, "xmlns:dna", "http://www.w3.org/2001/XMLSchema-instance")
'    attr.Value = "http://www.dnabank-network.org/schemas/ABCDDNA"
'    root.setAttributeNode attr

    Set attr = dom.createNode(NODE_ATTRIBUTE, "xs:schemaLocation", "http://www.w3.org/2001/XMLSchema-instance")
    attr.Value = "http://www.tdwg.org/schemas/abcd/2.06 http://rs.tdwg.org/abcd/2.06/ABCD_2.06.xsd"
    root.setAttributeNode attr

    'Create a DataSet container
    root.appendChild dom.createTextNode(vbCrLf)
    Set firstnode = dom.createNode(NODE_ELEMENT, "DataSet", "http://www.tdwg.org/schemas/abcd/2.06")
    root.appendChild firstnode
    root.appendChild dom.createTextNode(vbCrLf)
    firstnode.appendChild dom.createTextNode(vbCrLf & Space$(2))

    'Create a DatasetGUID container
    Set node = dom.createNode(NODE_ELEMENT, "DatasetGUID", "http://www.tdwg.org/schemas/abcd/2.06")
    node.Text = "JEMU Project"
    firstnode.appendChild node
    firstnode.appendChild dom.createTextNode(vbCrLf & Space$(2))
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))

    'Create a Technical Contacts container
    Set node = dom.createNode(NODE_ELEMENT, "TechnicalContacts", "http://www.tdwg.org/schemas/abcd/2.06")
    firstnode.appendChild node
    firstnode.appendChild dom.createTextNode(vbCrLf & Space$(2))
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))
    XMLTechnicalContacts dom:=dom, node:=node

    'Create a Content Contacts container
    Set node = dom.createNode(NODE_ELEMENT, "ContentContacts", "http://www.tdwg.org/schemas/abcd/2.06")
    firstnode.appendChild node
    firstnode.appendChild dom.createTextNode(vbCrLf & Space$(2))
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))
    XMLContentContacts dom:=dom, node:=node
    
    'Create a Metadata container
    Set node = dom.createNode(NODE_ELEMENT, "Metadata", "http://www.tdwg.org/schemas/abcd/2.06")
    firstnode.appendChild node
    firstnode.appendChild dom.createTextNode(vbCrLf & Space$(2))
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))
    XMLMetadata dom:=dom, node:=node
    
    'Create units container
    Set node = dom.createNode(NODE_ELEMENT, "Units", "http://www.tdwg.org/schemas/abcd/2.06")
    firstnode.appendChild node
    firstnode.appendChild dom.createTextNode(vbCrLf + Space$(2))
    node.appendChild dom.createTextNode(vbCrLf + Space$(4))

'**********************************************************************************
'Insert information for each specimen/sample/DNA
'**********************************************************************************
Dim RangeSamples As Range
Dim rowCounter As Integer
Dim LastR As Integer
LastR = Application.Sheets("cSPECIMEN").Cells(Rows.Count, "A").End(xlUp).Row

Dim FoundCellSample As Range, FoundCellDNA As Range, SpecimenIdOfRow As Range, SpecimenSourceIdOfRow As Range, SpecimenInstitutionIdOfRow As Range, SampleIdOfRow As Range, SampleInstitutionIdOfRow As Range
Dim FoundSpecInSample As Range, FoundSampleInDNA As Range
Dim vSpecimenSourceIdOfRow As String
Dim vSpecimenInstitutionIdOfRow As String
Dim vSampleInstitutionIdOfRow As String

For rowCounter = 2 To LastR
    'Create a unit subnode for each record (specimen, sample or DNA)
    Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
    node.appendChild subnode
    node.appendChild dom.createTextNode(vbCrLf + Space$(4))
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))

'--------------------------------------------------------------------------------
'FIRST STEP: ADD INFORMATION PRESENT IN EACH LEVEL...
'--------------------------------------------------------------------------------
    
    '---------------------
    'Level Specimen
    '---------------------
    XMLSpecimenID dom:=dom, subnode:=subnode, rowCounter:=rowCounter
    XMLIdentification dom:=dom, subnode:=subnode, rowCounter:=rowCounter
    XMLKindOfUnit dom:=dom, subnode:=subnode, rowConcerned:=rowCounter, Level:=1
    XMLGather dom:=dom, subnode:=subnode, rowCounter:=rowCounter
    XMLNotesSpec dom:=dom, subnode:=subnode, rowCounter:=rowCounter
    
        Set SpecimenIdOfRow = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Museum_voucher_ID, lookAt:=xlWhole).Column)
        Set SpecimenSourceIdOfRow = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collection_Code, lookAt:=xlWhole).Column)
        If SpecimenSourceIdOfRow <> "" Then
            vSpecimenSourceIdOfRow = SpecimenSourceIdOfRow.Value
        Else
            vSpecimenSourceIdOfRow = "Not defined"
        End If
        Set SpecimenInstitutionIdOfRow = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Institution_storing, lookAt:=xlWhole).Column)
        If SpecimenInstitutionIdOfRow <> "" Then
            vSpecimenInstitutionIdOfRow = SpecimenInstitutionIdOfRow.Value
        Else
            vSpecimenInstitutionIdOfRow = "Not defined"
        End If
        
        '---------------------
        'Level Sample
        '---------------------
        Do While Not Application.Sheets("cSAMPLE").Cells.Find(SpecimenIdOfRow.Value, lookAt:=xlWhole) Is Nothing
            Set FoundCellSample = Application.Sheets("cSAMPLE").Cells(Application.Sheets("cSAMPLE").Cells.Find(SpecimenIdOfRow.Value, lookAt:=xlWhole).Row, Application.Sheets("cSAMPLE").Rows(1).Find(Sample_ID, lookAt:=xlWhole).Column)
            
            Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
            node.appendChild subnode
            node.appendChild dom.createTextNode(vbCrLf + Space$(4))
            subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
            XMLSampleID dom:=dom, subnode:=subnode, rowConcerned:=FoundCellSample.Row
            XMLKindOfUnit dom:=dom, subnode:=subnode, rowConcerned:=FoundCellSample.Row, Level:=2
            XMLSampleUnit dom:=dom, subnode:=subnode, rowConcerned:=FoundCellSample.Row
            XMLSampleAssociation dom:=dom, subnode:=subnode, rowConcerned:=FoundCellSample.Row, associatedUnit:=SpecimenIdOfRow.Value, associatedSource:=vSpecimenSourceIdOfRow, associatedInstitution:=vSpecimenInstitutionIdOfRow
            XMLNotesSample dom:=dom, subnode:=subnode, rowConcerned:=FoundCellSample.Row
                    
            Set SampleIdOfRow = Application.Sheets("cSAMPLE").Cells(FoundCellSample.Row, Application.Sheets("cSAMPLE").Rows(1).Find(Sample_ID, lookAt:=xlWhole).Column)
            Set SampleInstitutionIdOfRow = Application.Sheets("cSAMPLE").Cells(FoundCellSample.Row, Application.Sheets("cSAMPLE").Rows(1).Find(Institution, lookAt:=xlWhole).Column)
            If SampleInstitutionIdOfRow <> "" Then
                vSampleInstitutionIdOfRow = SampleInstitutionIdOfRow.Value
            Else
                vSampleInstitutionIdOfRow = "Not defined"
            End If
    
            '---------------------
            'Level DNA
            '---------------------
            Do While Not Application.Sheets("cDNA").Cells.Find(SampleIdOfRow.Value, lookAt:=xlWhole) Is Nothing
                Set FoundCellDNA = Application.Sheets("cDNA").Cells(Application.Sheets("cDNA").Cells.Find(SampleIdOfRow.Value, lookAt:=xlWhole).Row, Application.Sheets("cDNA").Rows(1).Find(DNA_Sample_ID, lookAt:=xlWhole).Column)
                    
                Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
                node.appendChild subnode
                node.appendChild dom.createTextNode(vbCrLf + Space$(4))
                subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
                XMLDNAID dom:=dom, subnode:=subnode, rowConcerned:=FoundCellDNA.Row
                XMLKindOfUnit dom:=dom, subnode:=subnode, rowConcerned:=FoundCellDNA.Row, Level:=3
                XMLDNAAssociation dom:=dom, subnode:=subnode, rowConcerned:=FoundCellDNA.Row, associatedUnit:=SpecimenIdOfRow.Value, associatedUnit2:=SampleIdOfRow.Value, associatedSource2:="Not defined", associatedInstitution2:=vSampleInstitutionIdOfRow
                XMLSequenceDNA dom:=dom, subnode:=subnode, rowConcerned:=FoundCellDNA.Row
                XMLNotesDNA dom:=dom, subnode:=subnode, rowConcerned:=FoundCellDNA.Row
                XMLExtensionDNA dom:=dom, subnode:=subnode, rowConcerned:=FoundCellDNA.Row
                FoundCellDNA.EntireRow.Delete (xlShiftUp)
                                            
            Loop
                
            FoundCellSample.EntireRow.Delete (xlShiftUp)
                
        Loop
Next rowCounter

'----------------------------------------------------------------------------------------------------------------------
'SECOND STEP: ADD INFORMATION PRESENT IN SAMPLE (AND POSSIBLY IN DNA)...
'----------------------------------------------------------------------------------------------------------------------

Dim RemainingSample, RemainingDNA, remainingCounter As Integer
Dim LastRemainingSample
Dim SampleInstitutionIdOfRemainingSample
Dim vSampleInstitutionIdOfRemainingSample As String

LastRemainingSample = Application.Sheets("cSAMPLE").Cells(Rows.Count, "A").End(xlUp).Row

For remainingCounter = 2 To LastRemainingSample
Set RemainingSample = Application.Sheets("cSAMPLE").Cells(2, Sheets("cSAMPLE").Rows(1).Find(Sample_ID, lookAt:=xlWhole).Column)

Set SampleInstitutionIdOfRemainingSample = Application.Sheets("cSAMPLE").Cells(RemainingSample.Row, Application.Sheets("cSAMPLE").Rows(1).Find(Institution, lookAt:=xlWhole).Column)
    If Not SampleInstitutionIdOfRemainingSample Is Nothing Then
        vSampleInstitutionIdOfRemainingSample = SampleInstitutionIdOfRemainingSample.Value
    Else:
        vSampleInstitutionIdOfRemainingSample = "Not defined"
    End If

If Not RemainingSample Is Nothing Then
    Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
    node.appendChild subnode
    node.appendChild dom.createTextNode(vbCrLf + Space$(4))
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    XMLSampleID dom:=dom, subnode:=subnode, rowConcerned:=RemainingSample.Row
    XMLKindOfUnit dom:=dom, subnode:=subnode, rowConcerned:=RemainingSample.Row, Level:=1
    XMLSampleUnit dom:=dom, subnode:=subnode, rowConcerned:=RemainingSample.Row
    XMLSampleAssociation dom:=dom, subnode:=subnode, rowConcerned:=RemainingSample.Row, associatedUnit:="Not defined", associatedSource:="Not defined", associatedInstitution:="Not defined"
    XMLNotesSample dom:=dom, subnode:=subnode, rowConcerned:=RemainingSample.Row


    Do While Not Application.Sheets("cDNA").Cells.Find(RemainingSample.Value, lookAt:=xlWhole) Is Nothing
        Set RemainingDNA = Application.Sheets("cDNA").Cells(Application.Sheets("cDNA").Cells.Find(RemainingSample.Value, lookAt:=xlWhole).Row, Application.Sheets("cDNA").Rows(1).Find(DNA_Sample_ID, lookAt:=xlWhole).Column)
            Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
            node.appendChild subnode
            node.appendChild dom.createTextNode(vbCrLf + Space$(4))
            subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
            XMLDNAID dom:=dom, subnode:=subnode, rowConcerned:=RemainingDNA.Row
            XMLDNAAssociation dom:=dom, subnode:=subnode, rowConcerned:=RemainingDNA.Row, associatedUnit:="Not defined", associatedUnit2:=RemainingSample.Value, associatedSource2:="Not defined", associatedInstitution2:=vSampleInstitutionIdOfRemainingSample
            XMLSequenceDNA dom:=dom, subnode:=subnode, rowConcerned:=RemainingDNA.Row
            XMLNotesDNA dom:=dom, subnode:=subnode, rowConcerned:=RemainingDNA.Row
            XMLExtensionDNA dom:=dom, subnode:=subnode, rowConcerned:=RemainingDNA.Row
            RemainingDNA.EntireRow.Delete (xlShiftUp)
        Loop
    RemainingSample.EntireRow.Delete (xlShiftUp)
End If

Next remainingCounter

'----------------------------------------------------------------------------------------------------------------------
'THIRD STEP: ADD INFORMATION PRESENT IN DNA ONLY...
'----------------------------------------------------------------------------------------------------------------------

Dim LastRemainingDNA, lastremainingCounter As Integer
Dim RowLastRemainingDNA
RowLastRemainingDNA = Application.Sheets("cDNA").Cells(Rows.Count, "A").End(xlUp).Row
    
For lastremainingCounter = 2 To RowLastRemainingDNA
    Set LastRemainingDNA = Application.Sheets("cDNA").Cells(2, Sheets("cDNA").Rows(1).Find(DNA_Sample_ID, lookAt:=xlWhole).Column)
    
    If Not LastRemainingDNA Is Nothing Then
        Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
        node.appendChild subnode
        node.appendChild dom.createTextNode(vbCrLf + Space$(4))
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        XMLDNAID dom:=dom, subnode:=subnode, rowConcerned:=LastRemainingDNA.Row
        XMLDNAAssociation dom:=dom, subnode:=subnode, rowConcerned:=LastRemainingDNA.Row, associatedUnit:="Not defined", associatedSource2:="Not defined", associatedInstitution2:="Not defined", associatedUnit2:="Not defined"
        XMLSequenceDNA dom:=dom, subnode:=subnode, rowConcerned:=LastRemainingDNA.Row
        XMLNotesDNA dom:=dom, subnode:=subnode, rowConcerned:=LastRemainingDNA.Row
        XMLExtensionDNA dom:=dom, subnode:=subnode, rowConcerned:=LastRemainingDNA.Row
        LastRemainingDNA.EntireRow.Delete (xlShiftUp)
    End If
Next lastremainingCounter

Else:
    DeleteSheetsAfterRework
    GoTo Exit_CreateXML
    
End If

'**********************************************************************************
'Delete sheets after encoding in XML file and saving
'**********************************************************************************

DeleteSheetsAfterRework

With Application
    .DisplayAlerts = True
    .ScreenUpdating = True
    .Cursor = xlDefault
End With

' Save the XML document to a file
DefineFileName:         strPath = Application.GetSaveAsFilename(InitialFileName:="test.xml", FileFilter:="XML Files (*.xml), *.xml", Title:="Select where to save your file")
            If strPath <> "False" Then
                ' Save the file at the location provided with the name provided
                dom.Save strPath
            End If

Exit_CreateXML:
    With Application
        .DisplayAlerts = True
        .ScreenUpdating = True
        .Cursor = xlDefault
    End With

    Exit Sub

Err_CreateXML:

    DeleteSheetsAfterRework

    If Err.Number = -2147024891 Then
        MsgBox prompt:="You don't have the rights to save the file " & strPath & " on the location selected." & vbCrLf & _
                        "Please provide an other location.", Title:="No Sufficient rights", Buttons:=vbExclamation
        Resume DefineFileName
    Else
        MsgBox prompt:="Error " & Err.Number & vbNewLine & Err.Description & vbCrLf & "In CreateXML."
    End If
    Resume Exit_CreateXML
            
End Sub

Public Sub Define_Headings()

'*************************************************************************
'Define Name of headings in each to map columns
'*************************************************************************
Museum_voucher_ID = "Museum_voucher_ID"
Field_ID = "Field_ID"
Institution_storing = "Institution_storing"
Collection_Code = "Collection_Code"
Classification = "Classification"
Phylum = "Phylum"
Class = "Class"
Order = "Order"
Family = "Family"
Subfamily = "Subfamily"
Genus = "Genus"
Species = "Species"
Subspecies = "Subspecies"
Identifier = "Identifier"
Identifier_Institution = "Identifier_Institution"
Collector = "Collector"
Collection_Date = "Collection_Date"
Continent = "Continent"
country = "Country"
Province = "Province"
Region = "Region"
Municipality = "Municipality"
Exact_Site = "Exact_Site"
Latitude = "Latitude"
Longitude = "Longitude"
Elevation = "Elevation"
Depth = "Depth"
Unit = "Unit"
Comment1_Spec = "Comment1"
Comment2_Spec = "Comment2"
Comment3_Spec = "Comment3"
Comment4_Spec = "Comment4"
Comment5_Spec = "Comment5"

Sample_ID = "Sample_ID"
Sample_ID_Sample = "Sample_ID"
Sample_2Dbarcode = "Sample_2Dbarcode"
Sample_type = "Sample_type"
Sample_description = "Sample_description"
Sample_protocol = "Sample_protocol"
Sample_preservation = "Sample_preservation"
Institution = "Institution"
Storage_rack = "Storage_rack"
Storage_position = "Storage_position"
Sample_DonorPerson = "Sample_DonorPerson"
Sample_DonorInstitution = "Sample_DonorInstitution"
Donor_Email = "Donor_Email"

Sample_ID_DNA = "Sample_ID"
DNA_Sample_ID = "DNA_Sample_ID"
DNA_2Dbarcode = "DNA_2Dbarcode"
DNA_concentration = "DNA_concentration"
DNA_quality = "DNA_quality"
DNA_size = "DNA_size"
DNA_comment = "DNA_comment"
DNA_rack_ID = "DNA_rack_ID"
DNA_position = "DNA_position"
Extraction_date = "Extraction_date"
Protocol = "Protocol"
Tissue = "Tissue"
Digestion_time = "Digestion_time"
Digestion_volume = "Digestion_volume"
Elution = "Elution"
Elution_volume = "Elution_volume"
Operator = "Operator"
Institute = "Institute"
Remarks = "Remarks"

End Sub

Private Function Check_Duplicates_SpecimenID() As Boolean

'*************************************************************************
'Check if there are duplicates in the column Sample_ID of the "SPECIMEN" sheet
'*************************************************************************
    On Error GoTo Err_Dupl_Spec
    'Define variable.
    Dim EvalRangeSpec As Range, errcount As Integer, Target As Range
    Dim LastRSpec As Integer, ColSpecID As Integer, R As Integer
    LastRSpec = Application.Sheets("SPECIMEN").Cells(Rows.Count, Application.Sheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole).Column).End(xlUp).Row
    ColSpecID = Application.Sheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole).Column
    errcount = 0

    For R = 4 To LastRSpec
        Set EvalRangeSpec = Application.Sheets("SPECIMEN").Columns(ColSpecID)
        Set Target = Application.Sheets("SPECIMEN").Cells(R, ColSpecID)
        If WorksheetFunction.CountIf(EvalRangeSpec, Target.Value) > 1 Then
            Target.Interior.Color = RGB(248, 66, 83)
            errcount = errcount + 1
        Else
        End If
    Next R
   
    If errcount > 0 Then
        MsgBox "Some Specimen id's (Museum_voucher_ID) have duplicates in the worksheet" & "SPECIMEN" & ". Please check these records (in red)."
        Check_Duplicates_SpecimenID = True
    Else: Check_Duplicates_SpecimenID = False
    End If

Exit_Dupl_Spec:
    
    Exit Function

Err_Dupl_Spec:
    
    MsgBox prompt:="An error occured in function Check_Duplicates_SpecimenID." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in function Check_Duplicates_SpecimenID"
    Resume Exit_Dupl_Spec

End Function

Private Function Check_Duplicates_SampleID() As Boolean

'*************************************************************************
'Check if there are duplicates in the column Sample_ID of the "SPECIMEN" sheet
'*************************************************************************
On Error GoTo Err_Dupl_Sample

    'Define variable.
    Dim EvalRangeSample As Range, errcount As Integer, Target As Range
    Dim LastRSample As Integer, ColSampleID As Integer, R As Integer
    LastRSample = Application.Sheets("SAMPLE").Cells(Rows.Count, Application.Sheets("SAMPLE").Rows("1:3").Find("Sample_ID", lookAt:=xlWhole).Column).End(xlUp).Row
    ColSampleID = Application.Sheets("SAMPLE").Rows("1:3").Find("Sample_ID", lookAt:=xlWhole).Column
    errcount = 0
  
    For R = 2 To LastRSample
        Set Target = Application.Sheets("SAMPLE").Cells(R, ColSampleID)
        Set EvalRangeSample = Application.Sheets("SAMPLE").Columns(ColSampleID)
        If WorksheetFunction.CountIf(EvalRangeSample, Target.Value) > 1 Then
            Target.Interior.Color = RGB(248, 66, 83)
            errcount = errcount + 1
        Else
        End If
    Next R
   
    If errcount > 0 Then
        MsgBox "Some sample_ID's have duplicates in the worksheet" & "SAMPLE" & ". Please check these records (in red)."
        Check_Duplicates_SampleID = True
    Else: Check_Duplicates_SampleID = False
    End If

Exit_Dupl_Sample:
    
    Exit Function

Err_Dupl_Sample:
    
    MsgBox prompt:="An error occured in function Check_Duplicates_SampleID." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in function Check_Duplicates_SampleID"
    Resume Exit_Dupl_Sample

End Function

Private Function Check_Duplicates_DNAID() As Boolean
 
'*************************************************************************
'Check if there are duplicates in the column Sample_ID of the "SPECIMEN" sheet
'*************************************************************************
On Error GoTo Err_Dupl_DNA

    'Define variable.
    Dim EvalRangeDNA As Range, errcount As Integer, Target As Range
    Dim LastRDNA As Integer, ColDNAID As Integer, R As Integer
    LastRDNA = Application.Sheets("DNA").Cells(Rows.Count, Application.Sheets("DNA").Rows("1:3").Find(DNA_Sample_ID, lookAt:=xlWhole).Column).End(xlUp).Row
    ColDNAID = Application.Sheets("DNA").Rows("1:3").Find(DNA_Sample_ID, lookAt:=xlWhole).Column
    errcount = 0
   
    For R = 2 To LastRDNA
        Set EvalRangeDNA = Application.Sheets("DNA").Columns(ColDNAID)
        Set Target = Application.Sheets("DNA").Cells(R, ColDNAID)
        If WorksheetFunction.CountIf(EvalRangeDNA, Target.Value) > 1 Then
            Target.Interior.Color = RGB(248, 66, 83)
            errcount = errcount + 1
        Else
        End If
    Next R
   
    If errcount > 0 Then
        MsgBox "Some DNA ID's have duplicates in the worksheet" & "DNA" & ". Please check these records (in red)."
        Check_Duplicates_DNAID = True
    Else: Check_Duplicates_DNAID = False
    End If

Exit_Dupl_DNA:
    
    Exit Function

Err_Dupl_DNA:
    
    MsgBox prompt:="An error occured in function Check_Duplicates_DNAID." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in function Check_Duplicates_DNAID"
    Resume Exit_Dupl_DNA

End Function

Private Sub AutoCompleteClassif()

    Dim LastR As Integer, R As Integer
    Dim Classif, ID
    Dim ClassifValue As String
    ClassifValue = "Zoological"
    LastR = Application.Sheets("SPECIMEN").Cells(Rows.Count, Application.Sheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole).Column).End(xlUp).Row
    Set Classif = Application.Worksheets("SPECIMEN").Rows("1:3").Find(Classification, lookAt:=xlWhole)
    Set ID = Application.Worksheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole)

    For R = 2 To LastR
        If Application.Sheets("SPECIMEN").Cells(R, Classif.Column) = "" And Application.Sheets("SPECIMEN").Cells(R, ID.Column) <> "" Then
        Application.Sheets("SPECIMEN").Cells(R, Classif.Column).Value = ClassifValue
        Application.Sheets("SPECIMEN").Cells(R, Classif.Column).Interior.Color = RGB(254, 222, 104)
        End If
    Next R

End Sub

Private Function CopySheetsForRework() As Boolean

'********************************************************************************************
'| Make copy of specimen sheet at the end of the sheets for structure rework instead of working
'|          directly in the working sheet (with the risk of losing some vital infos ;) )
'********************************************************************************************

On Error GoTo Err_CopySheetsForRework

Dim sheetcount As Integer
Dim R As Long, col As Long

If FeuilleExiste("cSPECIMEN") Then
Sheets("cSPECIMEN").Delete
End If
If FeuilleExiste("cSAMPLE") Then
Sheets("cSAMPLE").Delete
End If
If FeuilleExiste("cDNA") Then
Sheets("cDNA").Delete
End If

sheetcount = Sheets.Count

'----------------------------------------------------------------------
'Copy each sheet without VBA code behind
'----------------------------------------------------------------------
Sheets.Add After:=Sheets(Sheets.Count)
Sheets(sheetcount + 1).Name = "cSPECIMEN"
Sheets("SPECIMEN").Select
Cells.Copy
Sheets("cSPECIMEN").Activate
Sheets("cSPECIMEN").Select
Cells.Select
ActiveSheet.Paste

Sheets.Add After:=Sheets(Sheets.Count)
Sheets(sheetcount + 2).Name = "cSAMPLE"
Sheets("SAMPLE").Select
Cells.Copy
Sheets("cSAMPLE").Select
Cells.Select
ActiveSheet.Paste

Sheets.Add After:=Sheets(Sheets.Count)
Sheets(sheetcount + 3).Name = "cDNA"
Sheets("DNA").Select
Cells.Copy
Sheets("cDNA").Select
Cells.Select
ActiveSheet.Paste

'----------------------------------------------------------------------
'Delete uneeded lines and column
'----------------------------------------------------------------------
Sheets("cSPECIMEN").Select
Rows("1").Select
Selection.Delete Shift:=xlShiftUp
Sheets("cSPECIMEN").Select
Rows("2").Select
Selection.Delete Shift:=xlShiftUp
Application.Sheets("cSAMPLE").Select
Rows("1").Select
Selection.Delete Shift:=xlShiftUp
Application.Sheets("cSAMPLE").Select
Rows("2").Select
Selection.Delete Shift:=xlShiftUp
Application.Sheets("cDNA").Select
Rows("1").Select
Selection.Delete Shift:=xlShiftUp
Application.Sheets("cDNA").Select
Rows("2").Select
Selection.Delete Shift:=xlShiftUp

'-------------------------------------------------------------------------
'Delete empty rows and leading and trailing spaces
'-------------------------------------------------------------------------
Dim LastRSpec As Integer, LastCSpec As Integer, LastRSample As Integer, LastCSample As Integer, LastRDNA As Integer, LastCDNA As Integer
LastRSpec = Application.Sheets("cSPECIMEN").Cells(Rows.Count, Application.Sheets("cSPECIMEN").Rows(1).Find("Museum_voucher_ID", lookAt:=xlWhole).Column).End(xlUp).Row
LastCSpec = Application.Sheets("cSPECIMEN").Cells(1, Columns.Count).End(xlToLeft).Column
LastRSample = Application.Sheets("cSAMPLE").Cells(Rows.Count, Application.Sheets("cSAMPLE").Rows(1).Find("Sample_ID", lookAt:=xlWhole).Column).End(xlUp).Row
LastCSample = Application.Sheets("cSAMPLE").Cells(1, Columns.Count).End(xlToLeft).Column
LastRDNA = Application.Sheets("cDNA").Cells(Rows.Count, Application.Sheets("cDNA").Rows(1).Find("DNA_Sample_ID", lookAt:=xlWhole).Column).End(xlUp).Row
LastCDNA = Application.Sheets("cDNA").Cells(1, Columns.Count).End(xlToLeft).Column

'SPECIMEN sheet
For R = LastRSpec To 1 Step -1
    If Application.Sheets("cSPECIMEN").Cells(R, 1) = "" Then
        Application.Sheets("cSPECIMEN").Rows(R).Delete
    End If
Next R
For R = LastRSpec To 1 Step -1
    For col = LastCSpec To 1 Step -1
        Application.Sheets("cSPECIMEN").Cells(R, col).Value = Trim(Application.Sheets("cSPECIMEN").Cells(R, col).Value)
    Next col
Next R

'SAMPLE sheet
For R = LastRSample To 1 Step -1
    If Application.Sheets("cSAMPLE").Cells(R, 1) = "" Then
        Application.Sheets("cSAMPLE").Rows(R).Delete
    End If
Next R
For R = LastRSample To 1 Step -1
    For col = LastCSample To 1 Step -1
        Application.Sheets("cSAMPLE").Cells(R, col).Value = Trim(Application.Sheets("cSAMPLE").Cells(R, col).Value)
    Next col
Next R

'DNA sheet
For R = LastRDNA To 1 Step -1
    If Application.Sheets("cDNA").Cells(R, 1) = "" Then
        Application.Sheets("cDNA").Rows(R).Delete
    End If
Next R
For R = LastRDNA To 1 Step -1
    For col = LastCDNA To 1 Step -1
        Application.Sheets("cDNA").Cells(R, col).Value = Trim(Application.Sheets("cDNA").Cells(R, col).Value)
    Next col
Next R

CopySheetsForRework = True

Exit_CopySheetsForRework:
    
    Exit Function

Err_CopySheetsForRework:
    
    CopySheetsForRework = False
    MsgBox prompt:="An error occured in function CopySheetsForRework." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in function CopySheetsForRework"
    Resume Exit_CopySheetsForRework

End Function

Private Sub CheckNumericAndDate()

'***************************************************************************************************************
'| Check if values in Elevation, Depth, DNA_concentration, DNA_quality, DNA_size, Digestion_volume and Elution_volume
'|          are numeric (if not empty)
'***************************************************************************************************************

Dim R As Integer
Dim LastRSpec As Integer, LastRSample As Integer, LastRDNA As Integer
Dim Latit, Longit

LastRSpec = Application.Sheets("SPECIMEN").Cells(Rows.Count, Application.Sheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole).Column).End(xlUp).Row
LastRSample = Application.Sheets("SAMPLE").Cells(Rows.Count, Application.Sheets("SAMPLE").Rows("1:3").Find(Sample_ID_Sample, lookAt:=xlWhole).Column).End(xlUp).Row
LastRDNA = Application.Sheets("DNA").Cells(Rows.Count, Application.Sheets("DNA").Rows("1:3").Find(DNA_Sample_ID, lookAt:=xlWhole).Column).End(xlUp).Row

'-------------------------------------------------------------------------
'Empty fill of each cells
'-------------------------------------------------------------------------
'SPECIMEN sheet
For R = LastRSpec To 4 Step -1
    Application.Sheets("SPECIMEN").Rows(R).Cells.Interior.ColorIndex = xlNone
Next R
'SAMPLE sheet
For R = LastRSample To 4 Step -1
    Application.Sheets("SAMPLE").Rows(R).Cells.Interior.ColorIndex = xlNone
Next R
'DNA sheet
For R = LastRDNA To 4 Step -1
    Application.Sheets("DNA").Rows(R).Cells.Interior.ColorIndex = xlNone
Next R


'-------------------------------------------------------------------------
'Check in SPECIMEN-sheet and DNA-sheet
'-------------------------------------------------------------------------
For R = 4 To LastRSpec

    If Not IsEmpty(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Elevation, lookAt:=xlWhole).Column)) _
        And Not IsNull(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Elevation, lookAt:=xlWhole).Column)) _
        And Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Elevation, lookAt:=xlWhole).Column) <> "" Then
            If Not IsNumeric(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Elevation, lookAt:=xlWhole).Column).Value) Then
                Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Elevation, lookAt:=xlWhole).Column).Interior.Color = RGB(254, 222, 104)
            End If
    End If
    
    If Not IsEmpty(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Depth, lookAt:=xlWhole).Column)) _
    And Not IsNull(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Depth, lookAt:=xlWhole).Column)) _
    And Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Depth, lookAt:=xlWhole).Column) <> "" Then
        If Not IsNumeric(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Depth, lookAt:=xlWhole).Column).Value) Then
            Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Depth, lookAt:=xlWhole).Column).Interior.Color = RGB(254, 222, 104)
        End If
    End If
    
    If Not IsEmpty(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Collection_Date, lookAt:=xlWhole).Column)) _
    And Not IsNull(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Collection_Date, lookAt:=xlWhole).Column)) _
    And Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Collection_Date, lookAt:=xlWhole).Column) <> "" Then
        If Not IsDate(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Collection_Date, lookAt:=xlWhole).Column).Value) Then
            Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Collection_Date, lookAt:=xlWhole).Column).Interior.Color = RGB(254, 222, 104)
        End If
    End If

    If Not IsEmpty(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Latitude, lookAt:=xlWhole).Column)) _
    And Not IsNull(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Latitude, lookAt:=xlWhole).Column)) _
    And Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Latitude, lookAt:=xlWhole).Column) <> "" Then
        If IsNumeric(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Latitude, lookAt:=xlWhole).Column)) = True _
        And Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Latitude, lookAt:=xlWhole).Column) <= 90 _
        And Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Latitude, lookAt:=xlWhole).Column) >= -90 Then
        Else:
            Latit = ConvertDMSToDecimal(dmsLatLong:=Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Latitude, lookAt:=xlWhole).Column), LatOrLong:=True, rowCounter:=R, check:=True)
            If IsNull(Latit) And Latit = "" Then
                Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Latitude, lookAt:=xlWhole).Column).Interior.Color = RGB(248, 66, 83)
            End If
        End If
    End If

    If Not IsEmpty(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Longitude, lookAt:=xlWhole).Column)) _
    And Not IsNull(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Longitude, lookAt:=xlWhole).Column)) _
    And Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Longitude, lookAt:=xlWhole).Column) <> "" Then
        If IsNumeric(Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Longitude, lookAt:=xlWhole).Column)) = True _
        And Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Longitude, lookAt:=xlWhole).Column) <= 180 _
        And Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Longitude, lookAt:=xlWhole).Column) >= -180 Then
        Else:
            Longit = ConvertDMSToDecimal(dmsLatLong:=Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Longitude, lookAt:=xlWhole).Column), LatOrLong:=False, rowCounter:=R, check:=True)
            If IsNull(Longit) And Longit = "" Then
                Application.Sheets("SPECIMEN").Cells(R, Application.Sheets("SPECIMEN").Rows("1:3").Find(Longitude, lookAt:=xlWhole).Column).Interior.Color = RGB(248, 66, 83)
            End If
        End If
    End If

Next R

For R = 4 To LastRDNA

    If Not IsEmpty(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_concentration, lookAt:=xlWhole).Column)) _
    And Not IsNull(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_concentration, lookAt:=xlWhole).Column)) _
    And Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_concentration, lookAt:=xlWhole).Column) <> "" Then
        If Not IsNumeric(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_concentration, lookAt:=xlWhole).Column).Value) Then
            Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_concentration, lookAt:=xlWhole).Column).Interior.Color = RGB(254, 222, 104)
        End If
    End If
    
    If Not IsEmpty(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_quality, lookAt:=xlWhole).Column)) _
    And Not IsNull(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_quality, lookAt:=xlWhole).Column)) _
    And Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_quality, lookAt:=xlWhole).Column) <> "" Then
        If Not IsNumeric(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_quality, lookAt:=xlWhole).Column).Value) Then
            Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_quality, lookAt:=xlWhole).Column).Interior.Color = RGB(254, 222, 104)
        End If
    End If
    
    If Not IsEmpty(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_size, lookAt:=xlWhole).Column)) _
    And Not IsNull(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_size, lookAt:=xlWhole).Column)) _
    And Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_size, lookAt:=xlWhole).Column) <> "" Then
        If Not IsNumeric(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_size, lookAt:=xlWhole).Column).Value) Then
            Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_size, lookAt:=xlWhole).Column).Interior.Color = RGB(248, 66, 83)
        ElseIf Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_size, lookAt:=xlWhole).Column).Value <> Int(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_size, lookAt:=xlWhole).Column).Value) Then
            Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(DNA_size, lookAt:=xlWhole).Column).Interior.Color = RGB(248, 66, 83)
        End If
    End If
    
    If Not IsEmpty(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(Extraction_date, lookAt:=xlWhole).Column)) _
    And Not IsNull(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(Extraction_date, lookAt:=xlWhole).Column)) _
    And Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(Extraction_date, lookAt:=xlWhole).Column) <> "" Then
        If Not IsDate(Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(Extraction_date, lookAt:=xlWhole).Column).Value) Then
            Application.Sheets("DNA").Cells(R, Application.Sheets("DNA").Rows("1:3").Find(Extraction_date, lookAt:=xlWhole).Column).Interior.Color = RGB(248, 66, 83)
        End If
    End If
Next R

End Sub

Private Function Simple_SampleID() As Boolean

'********************************************************************************************
'| Delete all "/", "_", "-", " " and uppercase SAMPLE ID
'********************************************************************************************

On Error GoTo Err_Simple_SampleID

Dim LastRSpec As Integer, LastRSample As Integer, LastRDNA As Integer, R As Integer
Dim SampleID As String

LastRSpec = Application.Sheets("cSPECIMEN").Cells(Rows.Count, "A").End(xlUp).Row
For R = 2 To LastRSpec
    SampleID = UCase(Replace(Replace(Replace(Replace(Application.Cells(R, "A").Value, "/", ""), "_", ""), "-", ""), " ", ""))
    Application.Sheets("cSPECIMEN").Cells(R, "A").Value = SampleID
Next R

LastRSample = Application.Sheets("cSAMPLE").Cells(Rows.Count, "A").End(xlUp).Row
For R = 2 To LastRSample
    SampleID = UCase(Replace(Replace(Replace(Replace(Application.Cells(R, "A").Value, "/", ""), "_", ""), "-", ""), " ", ""))
    Application.Sheets("cSAMPLE").Cells(R, "A").Value = SampleID
Next R

LastRDNA = Application.Sheets("cDNA").Cells(Rows.Count, "A").End(xlUp).Row
For R = 2 To LastRDNA
    SampleID = UCase(Replace(Replace(Replace(Replace(Application.Cells(R, "A").Value, "/", ""), "_", ""), "-", ""), " ", ""))
    Application.Sheets("cDNA").Cells(R, "A").Value = SampleID
Next R

Simple_SampleID = True

Exit_Simple_SampleID:
    
    Exit Function

Err_Simple_SampleID:
    
    Simple_SampleID = False
    MsgBox prompt:="An error occured in function Simple_SampleID." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in function Simple_SampleID"
    Resume Exit_Simple_SampleID

End Function

Private Sub DeleteSampleRowAfterXML()

'********************************************************************************************
'| Delete lines from the "SAMPLE"-sheet encoded in XML file
'********************************************************************************************

Dim FoundCell As Range, SampleIdOfRow
Dim LastR As Integer
Dim rowCounter As Integer

LastR = Application.Sheets("cSPECIMEN").Cells(Rows.Count, Application.Sheets("cSPECIMEN").Rows(1).Find(Sample_ID, lookAt:=xlWhole).Column).End(xlUp).Row

For rowCounter = 2 To LastR
    Set SampleIdOfRow = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Sample_ID_DNA, lookAt:=xlWhole).Column)
    Set FoundCell = Application.Sheets("cSAMPLE").Columns(Application.Sheets("cSAMPLE").Rows(1).Find(Sample_ID_Sample, lookAt:=xlWhole).Column).Find(SampleIdOfRow, lookAt:=xlWhole)
    If Not FoundCell Is Nothing Then
        FoundCell.EntireRow.Delete (xlShiftUp)
    End If
Next rowCounter

End Sub

Private Sub DeleteDNARowAfterXML()

'********************************************************************************************
'| Delete lines from the "DNA"-sheet encoded in XML file
'********************************************************************************************

Dim FoundCell As Range, SampleIdOfRow
Dim LastR As Integer
Dim rowCounter As Integer

LastR = Application.Sheets("cSPECIMEN").Cells(Rows.Count, Application.Sheets("cSPECIMEN").Rows(1).Find(Sample_ID, lookAt:=xlWhole).Column).End(xlUp).Row

For rowCounter = 2 To LastR
    Set SampleIdOfRow = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Sample_ID, lookAt:=xlWhole).Column)
    Set FoundCell = Application.Sheets("cDNA").Columns(Application.Sheets("cDNA").Rows(1).Find(Sample_ID_DNA, lookAt:=xlWhole).Column).Find(SampleIdOfRow, lookAt:=xlWhole)
    If Not FoundCell Is Nothing Then
        FoundCell.EntireRow.Delete (xlShiftUp)
    End If
Next rowCounter

End Sub


Private Function ConvertDMSToDecimal(dmsLatLong As Range, LatOrLong As Boolean, rowCounter As Integer, check As Boolean) As Variant

'*******************************************************************************************************
'| Purpose: Convert D(egree)M(inute)S(econd) Latitude or Longitude into its corresponding decimal value
'| Parameters: dmsLatLong: string representing the dms latitude or longitude
'|                        LatOrLong: boolean - true for a latitude, false for a longitude
'| Returns: A decimal latitude or longitude
'*******************************************************************************************************

On Error GoTo Err_ConvertDMSToDecimal

Dim degreepart As Integer
Dim minutepart As Double
Dim strminutepart As String
Dim secondpart As Double
Dim strsecondpart As String
Dim restpart As Double
Dim SampleIdOfRow
Dim correspondingC As Range, correspondingH As Long, correspondingV As Long
Dim LatLongitude
Dim dmsLatLongV As String

dmsLatLongV = dmsLatLong.Value
dmsLatLongV = Replace(dmsLatLongV, "chr(34)", "''")

If check = True Then
    Set SampleIdOfRow = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole).Column)
    LatLongitude = Application.Sheets("SPECIMEN").Cells(2, dmsLatLong.Column).Value
ElseIf check = False Then
    Set SampleIdOfRow = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Museum_voucher_ID, lookAt:=xlWhole).Column)
    LatLongitude = Application.Sheets("cSPECIMEN").Cells(1, dmsLatLong.Column).Value
End If

correspondingV = Application.Sheets("SPECIMEN").Rows("1:3").Find(LatLongitude, lookAt:=xlWhole).Column
correspondingH = Application.Sheets("SPECIMEN").Columns(Application.Sheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole).Column).Find(SampleIdOfRow, lookAt:=xlWhole).Row
Set correspondingC = Application.Sheets("SPECIMEN").Cells(correspondingH, correspondingV)

'No fill
If correspondingC.Interior.Color = RGB(248, 66, 83) Then
correspondingC.Interior.ColorIndex = xlNone
End If

' If no degree found, not necessary to go further, exit function
If InStr(1, dmsLatLongV, "°") = 0 Then
    ConvertDMSToDecimal = Null
    correspondingC.Interior.Color = RGB(248, 66, 83)
    GoTo Exit_ConvertDMSToDecimal
Else
    ' Take the degree part and convert integer
    degreepart = CInt(Left$(dmsLatLongV, InStr(1, dmsLatLongV, "°") - 1))
    ' Continue with the minute part
    If InStr(InStr(1, dmsLatLongV, "°"), dmsLatLongV, "'") <> 0 Then
        If InStr(InStr(InStr(1, dmsLatLongV, "°"), dmsLatLongV, "'"), dmsLatLongV, Chr$(34)) = 0 Then
            If LatOrLong Then
                If InStr(1, UCase(dmsLatLongV), "S") = 0 And InStr(1, UCase(dmsLatLongV), "N") = 0 Then
                    ConvertDMSToDecimal = Null
                    correspondingC.Interior.Color = RGB(248, 66, 83)
                    GoTo Exit_ConvertDMSToDecimal
                Else
                    strminutepart = Mid$(dmsLatLongV, InStr(1, dmsLatLongV, "°") + 1, InStr(1, dmsLatLongV, "'") - InStr(1, dmsLatLongV, "°") - 1)
                    If IsNull(strminutepart) Or IsEmpty(strminutepart) Or strminutepart = "" Then
                        minutepart = 0
                    Else
                        minutepart = CDbl(strminutepart)
                    End If
                    If minutepart >= 60 Then
                        restpart = degreepart + Round(minutepart / 60) + ((minutepart / 60) - Round(minutepart / 60))
                    Else
                        restpart = degreepart + (minutepart / 60)
                    End If
                    If InStr(1, UCase(dmsLatLongV), "N") = 0 Then
                        restpart = -restpart
                    End If
                    If restpart < -90 Or restpart > 90 Then
                        ConvertDMSToDecimal = Null
                    Else
                        ConvertDMSToDecimal = restpart
                    End If
                End If
            Else
                If InStr(1, UCase(dmsLatLongV), "W") = 0 And InStr(1, UCase(dmsLatLongV), "E") = 0 Then
                    ConvertDMSToDecimal = Null
                    correspondingC.Interior.Color = RGB(248, 66, 83)
                    GoTo Exit_ConvertDMSToDecimal
                Else
                    strminutepart = Mid$(dmsLatLongV, InStr(1, dmsLatLongV, "°") + 1, InStr(1, dmsLatLongV, "'") - InStr(1, dmsLatLongV, "°") - 1)
                    If IsNull(strminutepart) Or IsEmpty(strminutepart) Or strminutepart = "" Then
                        minutepart = 0
                    Else
                        minutepart = CDbl(strminutepart)
                    End If
                    If minutepart >= 60 Then
                        restpart = degreepart + Round(minutepart / 60) + ((minutepart / 60) - Round(minutepart / 60))
                    Else
                        restpart = degreepart + (minutepart / 60)
                    End If
                    If InStr(1, UCase(dmsLatLongV), "E") = 0 Then
                        restpart = -restpart
                    End If
                    If restpart < -180 Or restpart > 180 Then
                        ConvertDMSToDecimal = Null
                    Else
                        ConvertDMSToDecimal = restpart
                    End If
                End If
            End If
        Else
            If LatOrLong Then
                If InStr(1, UCase(dmsLatLongV), "S") = 0 And InStr(1, UCase(dmsLatLongV), "N") = 0 Then
                    ConvertDMSToDecimal = Null
                    correspondingC.Interior.Color = RGB(248, 66, 83)
                    GoTo Exit_ConvertDMSToDecimal
                Else
                    strminutepart = Mid$(dmsLatLongV, InStr(1, dmsLatLongV, "°") + 1, InStr(1, dmsLatLongV, "'") - InStr(1, dmsLatLongV, "°") - 1)
                    If IsNull(strminutepart) Or IsEmpty(strminutepart) Or strminutepart = "" Then
                        minutepart = 0
                    Else
                        minutepart = CDbl(strminutepart)
                    End If
                    strsecondpart = Mid$(dmsLatLongV, InStr(1, dmsLatLongV, "'") + 1, InStr(1, dmsLatLongV, Chr$(34)) - InStr(1, dmsLatLongV, "'") - 1)
                    If IsNull(strsecondpart) Or IsEmpty(strsecondpart) Or strsecondpart = "" Then
                        secondpart = 0
                    Else
                        secondpart = CDbl(strsecondpart)
                    End If
                    If secondpart >= 60 Then
                        minutepart = minutepart + Round(secondpart / 60) + ((secondpart / 60) - Round(secondpart / 60))
                    Else
                        minutepart = minutepart + (secondpart / 60)
                    End If
                    If minutepart >= 60 Then
                        restpart = degreepart + Round(minutepart / 60) + ((minutepart / 60) - Round(minutepart / 60))
                    Else
                        restpart = degreepart + (minutepart / 60)
                    End If
                    If InStr(1, UCase(dmsLatLongV), "N") = 0 Then
                        restpart = -restpart
                    End If
                    If restpart < -90 Or restpart > 90 Then
                        ConvertDMSToDecimal = Null
                    Else
                        ConvertDMSToDecimal = restpart
                    End If
                End If
            Else
                If InStr(1, UCase(dmsLatLongV), "W") = 0 And InStr(1, UCase(dmsLatLongV), "E") = 0 Then
                    ConvertDMSToDecimal = Null
                    correspondingC.Interior.Color = RGB(248, 66, 83)
                    GoTo Exit_ConvertDMSToDecimal
                Else
                    strminutepart = Mid$(dmsLatLongV, InStr(1, dmsLatLongV, "°") + 1, InStr(1, dmsLatLongV, "'") - InStr(1, dmsLatLongV, "°") - 1)
                    If IsNull(strminutepart) Or IsEmpty(strminutepart) Or strminutepart = "" Then
                        minutepart = 0
                    Else
                        minutepart = CDbl(strminutepart)
                    End If
                    strsecondpart = Mid$(dmsLatLongV, InStr(1, dmsLatLongV, "'") + 1, InStr(1, dmsLatLongV, Chr$(34)) - InStr(1, dmsLatLongV, "'") - 1)
                    If IsNull(strsecondpart) Or IsEmpty(strsecondpart) Or strsecondpart = "" Then
                        secondpart = 0
                    Else
                        secondpart = CDbl(strsecondpart)
                    End If
                    If secondpart >= 60 Then
                        minutepart = minutepart + Round(secondpart / 60) + ((secondpart / 60) - Round(secondpart / 60))
                    Else
                        minutepart = minutepart + (secondpart / 60)
                    End If
                    If minutepart >= 60 Then
                        restpart = degreepart + Round(minutepart / 60) + ((minutepart / 60) - Round(minutepart / 60))
                    Else
                        restpart = degreepart + (minutepart / 60)
                    End If
                    If InStr(1, UCase(dmsLatLongV), "E") = 0 Then
                        restpart = -restpart
                    End If
                    If restpart < -180 Or restpart > 180 Then
                        ConvertDMSToDecimal = Null
                    Else
                        ConvertDMSToDecimal = restpart
                    End If
                End If
            End If
        End If
    Else
        'If no minutes, test if there's a second part
        If InStr(InStr(1, dmsLatLongV, "°"), dmsLatLongV, Chr$(34)) <> 0 Then
            strsecondpart = Mid$(dmsLatLongV, InStr(1, dmsLatLongV, "°") + 1, InStr(1, dmsLatLongV, Chr$(34)) - InStr(1, dmsLatLongV, "°") - 1)
            If IsNull(strsecondpart) Or IsEmpty(strsecondpart) Or strsecondpart = "" Then
                secondpart = 0
            Else
                secondpart = CDbl(strsecondpart)
            End If
            restpart = degreepart + (secondpart / 3600)
            If LatOrLong Then
                If InStr(1, UCase(dmsLatLongV), "S") = 0 And InStr(1, UCase(dmsLatLongV), "N") = 0 Then
                    ConvertDMSToDecimal = Null
                    correspondingC.Interior.Color = RGB(248, 66, 83)
                    GoTo Exit_ConvertDMSToDecimal
                Else
                    If InStr(1, UCase(dmsLatLongV), "N") = 0 Then
                        restpart = -restpart
                    End If
                    If restpart < -90 Or restpart > 90 Then
                        ConvertDMSToDecimal = Null
                    Else
                        ConvertDMSToDecimal = restpart
                    End If
                End If
            Else
                If InStr(1, UCase(dmsLatLongV), "E") = 0 And InStr(1, UCase(dmsLatLongV), "W") = 0 Then
                    ConvertDMSToDecimal = Null
                    correspondingC.Interior.Color = RGB(248, 66, 83)
                    GoTo Exit_ConvertDMSToDecimal
                Else
                    If InStr(1, UCase(dmsLatLongV), "E") = 0 Then
                        restpart = -restpart
                    End If
                    If restpart < -180 Or restpart > 180 Then
                        ConvertDMSToDecimal = Null
                    Else
                        ConvertDMSToDecimal = restpart
                    End If
                End If
            End If
        Else
            restpart = degreepart
            If LatOrLong Then
                If InStr(1, UCase(dmsLatLongV), "S") = 0 And InStr(1, UCase(dmsLatLongV), "N") = 0 Then
                    ConvertDMSToDecimal = Null
                    correspondingC.Interior.Color = RGB(248, 66, 83)
                    GoTo Exit_ConvertDMSToDecimal
                Else
                    If InStr(1, UCase(dmsLatLongV), "N") = 0 Then
                        restpart = -restpart
                    End If
                    If restpart < -90 Or restpart > 90 Then
                        ConvertDMSToDecimal = Null
                    Else
                        ConvertDMSToDecimal = restpart
                    End If
                End If
            Else
                If InStr(1, UCase(dmsLatLongV), "E") = 0 And InStr(1, UCase(dmsLatLongV), "W") = 0 Then
                    ConvertDMSToDecimal = Null
                    correspondingC.Interior.Color = RGB(248, 66, 83)
                    GoTo Exit_ConvertDMSToDecimal
                Else
                    If InStr(1, UCase(dmsLatLongV), "E") = 0 Then
                        restpart = -restpart
                    End If
                    If restpart < -180 Or restpart > 180 Then
                        ConvertDMSToDecimal = Null
                    Else
                        ConvertDMSToDecimal = restpart
                    End If
                End If
            End If
        End If
    End If
End If

Exit_ConvertDMSToDecimal:
    
    Exit Function
    
Err_ConvertDMSToDecimal:
    
    ConvertDMSToDecimal = Null
    If Err.Number <> 13 Then
        MsgBox prompt:="An error occured in function ConvertDMSToDecimal." & vbCrLf & _
                       "Error Number: " & Err.Number & "." & vbCrLf & _
                       "Error description: " & Err.Description & ".", _
                Buttons:=vbCritical, _
                Title:="Error in function ConvertDMSToDecimal"
    End If
    Resume Exit_ConvertDMSToDecimal
    
End Function

Private Sub DeleteSheetsAfterRework()

'******************************************************************************
'| Purpose: Delete the unecessary sheets when all's done
'******************************************************************************

On Error GoTo Err_DeleteSheetsAfterRework

If FeuilleExiste("cSPECIMEN") Then
Sheets("cSPECIMEN").Delete
End If
If FeuilleExiste("cSAMPLE") Then
Sheets("cSAMPLE").Delete
End If
If FeuilleExiste("cDNA") Then
Sheets("cDNA").Delete
End If

Exit_DeleteSheetsAfterRework:
    
    Exit Sub

Err_DeleteSheetsAfterRework:
    
    MsgBox prompt:="An error occured in sub DeleteSheetsAfterRework." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub DeleteSheetsAfterRework"
    Resume Exit_DeleteSheetsAfterRework

End Sub

Private Sub XMLMetadata(ByRef dom As MSXML2.DOMDocument60, ByRef node As MSXML2.IXMLDOMNode)
Dim xmlMetadataDescription As MSXML2.IXMLDOMElement
Dim xmlMetadataRepresentation As MSXML2.IXMLDOMElement
Dim xmlMetadataTitle As MSXML2.IXMLDOMElement
Dim xmlMetadataRevisionData As MSXML2.IXMLDOMElement
Dim xmlMetadataDateModified As MSXML2.IXMLDOMElement
Dim attrMetadataRepresentation As MSXML2.IXMLDOMAttribute

Set xmlMetadataDescription = dom.createNode(NODE_ELEMENT, "Description", "http://www.tdwg.org/schemas/abcd/2.06")
node.appendChild xmlMetadataDescription
node.appendChild dom.createTextNode(vbCrLf & Space$(4))
xmlMetadataDescription.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlMetadataRepresentation = dom.createNode(NODE_ELEMENT, "Representation", "http://www.tdwg.org/schemas/abcd/2.06")
xmlMetadataDescription.appendChild xmlMetadataRepresentation
xmlMetadataDescription.appendChild dom.createTextNode(vbCrLf & Space$(6))
xmlMetadataRepresentation.appendChild dom.createTextNode(vbCrLf & Space$(8))
Set xmlMetadataTitle = dom.createNode(NODE_ELEMENT, "Title", "http://www.tdwg.org/schemas/abcd/2.06")
xmlMetadataTitle.Text = "Data encoded by: " & Application.UserName
xmlMetadataRepresentation.appendChild xmlMetadataTitle
Set attrMetadataRepresentation = dom.createNode(NODE_ATTRIBUTE, "language", "http://www.tdwg.org/schemas/abcd/2.06")
attrMetadataRepresentation.Value = "EN"
xmlMetadataRepresentation.setAttributeNode attrMetadataRepresentation

Set xmlMetadataRevisionData = dom.createNode(NODE_ELEMENT, "RevisionData", "http://www.tdwg.org/schemas/abcd/2.06")
node.appendChild xmlMetadataRevisionData
node.appendChild dom.createTextNode(vbCrLf & Space$(4))
xmlMetadataRevisionData.appendChild dom.createTextNode(vbCrLf & Space$(6))
Set xmlMetadataDateModified = dom.createNode(NODE_ELEMENT, "DateModified", "http://www.tdwg.org/schemas/abcd/2.06")
xmlMetadataDateModified.Text = Format$(Now, "yyyy-mm-dd\THh:Nn:Ss")
xmlMetadataRevisionData.appendChild xmlMetadataDateModified
xmlMetadataRevisionData.appendChild dom.createTextNode(vbCrLf & Space$(6))

End Sub

Private Sub XMLTechnicalContacts(ByRef dom As MSXML2.DOMDocument60, ByRef node As MSXML2.IXMLDOMNode)
Dim xmlTechnicalContact As MSXML2.IXMLDOMElement
Dim xmlTechnicalContactName As MSXML2.IXMLDOMElement
Dim xmlTechnicalContactEmail As MSXML2.IXMLDOMElement
Dim xmlTechnicalContactPhone As MSXML2.IXMLDOMElement

Set xmlTechnicalContact = dom.createNode(NODE_ELEMENT, "TechnicalContact", "http://www.tdwg.org/schemas/abcd/2.06")
node.appendChild xmlTechnicalContact
node.appendChild dom.createTextNode(vbCrLf & Space$(4))
xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlTechnicalContactName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
xmlTechnicalContactName.Text = "Coordinator: Thierry Backeljau (RBINS)"
xmlTechnicalContact.appendChild xmlTechnicalContactName
xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlTechnicalContactEmail = dom.createNode(NODE_ELEMENT, "Email", "http://www.tdwg.org/schemas/abcd/2.06")
xmlTechnicalContactEmail.Text = "Thierry.Backeljau@naturalsciences.be"
xmlTechnicalContact.appendChild xmlTechnicalContactEmail
xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlTechnicalContactPhone = dom.createNode(NODE_ELEMENT, "Phone", "http://www.tdwg.org/schemas/abcd/2.06")
xmlTechnicalContactPhone.Text = "+32 2 627 43 39"
xmlTechnicalContact.appendChild xmlTechnicalContactPhone
xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlTechnicalContact = dom.createNode(NODE_ELEMENT, "TechnicalContact", "http://www.tdwg.org/schemas/abcd/2.06")
node.appendChild xmlTechnicalContact
node.appendChild dom.createTextNode(vbCrLf & Space$(4))
xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlTechnicalContactName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
xmlTechnicalContactName.Text = "Coordinator: Marc De Meyer (RMCA)"
xmlTechnicalContact.appendChild xmlTechnicalContactName
xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlTechnicalContactEmail = dom.createNode(NODE_ELEMENT, "Email", "http://www.tdwg.org/schemas/abcd/2.06")
xmlTechnicalContactEmail.Text = "marc.de.meyer@africamuseum.be"
xmlTechnicalContact.appendChild xmlTechnicalContactEmail
xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlTechnicalContactPhone = dom.createNode(NODE_ELEMENT, "Phone", "http://www.tdwg.org/schemas/abcd/2.06")
xmlTechnicalContactPhone.Text = "+32 2 769 53 60"
xmlTechnicalContact.appendChild xmlTechnicalContactPhone
xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

End Sub

Private Sub XMLContentContacts(ByRef dom As MSXML2.DOMDocument60, ByRef node As MSXML2.IXMLDOMNode)
Dim xmlContentContact As MSXML2.IXMLDOMElement
Dim xmlContentContactName As MSXML2.IXMLDOMElement
Dim xmlContentContactEmail As MSXML2.IXMLDOMElement
Dim xmlContentContactPhone As MSXML2.IXMLDOMElement

Set xmlContentContact = dom.createNode(NODE_ELEMENT, "ContentContact", "http://www.tdwg.org/schemas/abcd/2.06")
node.appendChild xmlContentContact
node.appendChild dom.createTextNode(vbCrLf & Space$(4))
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactName.Text = "Scientist: Kurt Jordaens, Ph.D. (RMCA)"
xmlContentContact.appendChild xmlContentContactName
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactEmail = dom.createNode(NODE_ELEMENT, "Email", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactEmail.Text = "kurt.jordaens@africamuseum.be"
xmlContentContact.appendChild xmlContentContactEmail
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactPhone = dom.createNode(NODE_ELEMENT, "Phone", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactPhone.Text = "+32 2 769 53 77"
xmlContentContact.appendChild xmlContentContactPhone
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContact = dom.createNode(NODE_ELEMENT, "ContentContact", "http://www.tdwg.org/schemas/abcd/2.06")
node.appendChild xmlContentContact
node.appendChild dom.createTextNode(vbCrLf & Space$(4))
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactName.Text = "Scientist: Zoltán T. Nagy, Ph.D. (RBINS)"
xmlContentContact.appendChild xmlContentContactName
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactEmail = dom.createNode(NODE_ELEMENT, "Email", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactEmail.Text = "zoltan-tamas.nagy@naturalsciences.be"
xmlContentContact.appendChild xmlContentContactEmail
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactPhone = dom.createNode(NODE_ELEMENT, "Phone", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactPhone.Text = "+32 2 627 44 24"
xmlContentContact.appendChild xmlContentContactPhone
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContact = dom.createNode(NODE_ELEMENT, "ContentContact", "http://www.tdwg.org/schemas/abcd/2.06")
node.appendChild xmlContentContact
node.appendChild dom.createTextNode(vbCrLf & Space$(4))
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactName.Text = "Scientist: Floris C. Breman, MSc. (RMCA)"
xmlContentContact.appendChild xmlContentContactName
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactEmail = dom.createNode(NODE_ELEMENT, "Email", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactEmail.Text = "floris.breman@africamuseum.be"
xmlContentContact.appendChild xmlContentContactEmail
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactPhone = dom.createNode(NODE_ELEMENT, "Phone", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactPhone.Text = "+32 2 769 56 30"
xmlContentContact.appendChild xmlContentContactPhone
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContact = dom.createNode(NODE_ELEMENT, "ContentContact", "http://www.tdwg.org/schemas/abcd/2.06")
node.appendChild xmlContentContact
node.appendChild dom.createTextNode(vbCrLf & Space$(4))
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactName.Text = "Scientist: Gontran Sonet, MSc. (RBINS)"
xmlContentContact.appendChild xmlContentContactName
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactEmail = dom.createNode(NODE_ELEMENT, "Email", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactEmail.Text = "gontran.sonet@naturalsciences.be"
xmlContentContact.appendChild xmlContentContactEmail
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

Set xmlContentContactPhone = dom.createNode(NODE_ELEMENT, "Phone", "http://www.tdwg.org/schemas/abcd/2.06")
xmlContentContactPhone.Text = "+32 2 627 44 24"
xmlContentContact.appendChild xmlContentContactPhone
xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

End Sub

'Private Sub XMLMetadata(ByRef dom As MSXML2.DOMDocument60, ByRef node As MSXML2.IXMLDOMNode)
'
''**********************************************************************************
''Create Metadata node in XML file
''**********************************************************************************
'
'Dim xmlMetadataDescription As MSXML2.IXMLDOMElement
'Dim xmlMetadataRepresentation As MSXML2.IXMLDOMElement
'Dim xmlMetadataTitle As MSXML2.IXMLDOMElement
'Dim xmlMetadataDetails As MSXML2.IXMLDOMElement
'Dim xmlMetadataURI As MSXML2.IXMLDOMElement
'Dim xmlMetadataRevisionData As MSXML2.IXMLDOMElement
'Dim xmlMetadataDateModified As MSXML2.IXMLDOMElement
'Dim xmlMetadataOwners As MSXML2.IXMLDOMElement
'Dim xmlMetadataOwner As MSXML2.IXMLDOMElement
'Dim xmlMetadataOrganisation As MSXML2.IXMLDOMElement
'Dim xmlMetadataOrganisationName As MSXML2.IXMLDOMElement
'Dim xmlMetadataOrgRepresentation As MSXML2.IXMLDOMElement
'Dim attrMetadataOrgRepresentation As MSXML2.IXMLDOMAttribute
'Dim xmlMetadataOrgRepresentationText As MSXML2.IXMLDOMElement
'Dim xmlMetadataOrgRepresentationAbr As MSXML2.IXMLDOMElement
'Dim xmlMetadataOwnerAddresses As MSXML2.IXMLDOMElement
'Dim xmlMetadataOwnerAddress As MSXML2.IXMLDOMElement
'Dim xmlMetadataOwnerEmailAddresses As MSXML2.IXMLDOMElement
'Dim xmlMetadataOwnerEmailAddress As MSXML2.IXMLDOMElement
'Dim xmlMetadataOwnerURI As MSXML2.IXMLDOMElement
'Dim xmlMetadataOwnerURL As MSXML2.IXMLDOMElement
'Dim xmlMetadataOwnerLogoURI As MSXML2.IXMLDOMElement
'Dim xmlMetadataIPRStatements As MSXML2.IXMLDOMElement
'Dim xmlMetadataCopyrights As MSXML2.IXMLDOMElement
'Dim xmlMetadataCopyright As MSXML2.IXMLDOMElement
'Dim attrMetadataCopyright As MSXML2.IXMLDOMAttribute
'Dim xmlMetadataCopyrightText As MSXML2.IXMLDOMElement
'
'
'Set xmlMetadataDescription = dom.createElement("Description")
'node.appendChild xmlMetadataDescription
'node.appendChild dom.createTextNode(vbCrLf & Space$(4))
'xmlMetadataDescription.appendChild dom.createTextNode(vbCrLf & Space$(6))
'
'Set xmlMetadataRepresentation = dom.createElement("Representation")
'xmlMetadataDescription.appendChild xmlMetadataRepresentation
'xmlMetadataDescription.appendChild dom.createTextNode(vbCrLf & Space$(6))
'xmlMetadataRepresentation.appendChild dom.createTextNode(vbCrLf & Space$(8))
'Set xmlMetadataTitle = dom.createElement("Title")
'xmlMetadataTitle.Text = "RBINS collections"
'xmlMetadataRepresentation.appendChild xmlMetadataTitle
'xmlMetadataRepresentation.appendChild dom.createTextNode(vbCrLf & Space$(8))
'Set xmlMetadataDetails = dom.createElement("Details")
'xmlMetadataDetails.Text = "The Royal Belgian Institute of Natural Sciences houses a precious collection of zoological, anthropological, paleontological, mineralogical and geological " _
'& "materials and data. The renowned Iguanodons from Bernissart, ambassadors of the Belgian science institute in Brussels, represent a natural history collection currently estimated" _
'& "to hold over 37 million specimens. The roots of the present day collection reach far back in history. It evolved from the Natural History collection of Karel of Lotharingen, governor" _
'& "of The Netherlands (1712-1780) and was part of didactic materials owned by the Central School of the City of Brussels. After the independence of Belgium, the City of Brussels donated" _
'& "the collection to the Belgian Government and became part of the autonomous Royal Natural History Museum in 1846, known as the Royal Belgian Institute of Natural Sciences since 1948." _
'& "Fieldwork by researchers and collaborators, in Belgium and abroad, donations and purchases have been expanding the assets ever since. Data presented here are coming from darwin" _
'& "database, the collection management tool of the RBINS. Today, the darwin database manages information on about 350.000 speciments stored in the institute's depositories. This" _
'& "number rises on a daily basis thanks to the continued efforts of curators and their adjuncts that are responsible for maintaining the stored specimens and information. Our online" _
'& "database provides information about the collections of the Vertebrates, Invertebrates, Entomology and Paleobotany. The application will soon be expanded with paleontozoological data." _
'& "The Department of Geology and the Department of Marine Ecosystems provide information on different systems. More information on these departments can be found on" _
'& "www.sciencesnaturelles.be/institute/structure/geology/gsb_website And www.mumm.ac.be The corner stone of the darwin database is the specimen and the information about its origin" _
'& "and its status. Although the status of the specimens follow the current regulations of the International Code on Zoological Nomenclature other status specifications not treated by" _
'& "the ICZN regulations (eg. topotype) have been maintained as supplementary information about the specimen(s) in question. Enjoy your virtual visit through our collections!"
'xmlMetadataRepresentation.appendChild xmlMetadataDetails
'xmlMetadataRepresentation.appendChild dom.createTextNode(vbCrLf & Space$(8))
'Set xmlMetadataURI = dom.createElement("URI")
'xmlMetadataURI.Text = "http://darwin.naturalsciences.be"
'xmlMetadataRepresentation.appendChild xmlMetadataURI
'xmlMetadataRepresentation.appendChild dom.createTextNode(vbCrLf & Space$(8))
'
'Set xmlMetadataRevisionData = dom.createElement("RevisionData")
'node.appendChild xmlMetadataRevisionData
'node.appendChild dom.createTextNode(vbCrLf & Space$(4))
'xmlMetadataRevisionData.appendChild dom.createTextNode(vbCrLf & Space$(6))
'Set xmlMetadataDateModified = dom.createElement("DateModified")
'xmlMetadataDateModified.Text = Format$(Now, "yyyy-mm-dd\THh:Nn:Ss")
'xmlMetadataRevisionData.appendChild xmlMetadataDateModified
'xmlMetadataRevisionData.appendChild dom.createTextNode(vbCrLf & Space$(6))
'
'Set xmlMetadataOwners = dom.createElement("Owners")
'node.appendChild xmlMetadataOwners
'node.appendChild dom.createTextNode(vbCrLf & Space$(4))
'xmlMetadataOwners.appendChild dom.createTextNode(vbCrLf & Space$(6))
'Set xmlMetadataOwner = dom.createElement("Owner")
'xmlMetadataOwners.appendChild xmlMetadataOwner
'xmlMetadataOwners.appendChild dom.createTextNode(vbCrLf & Space$(6))
'xmlMetadataOwner.appendChild dom.createTextNode(vbCrLf & Space$(8))
'Set xmlMetadataOrganisation = dom.createElement("Organisation")
'xmlMetadataOwner.appendChild xmlMetadataOrganisation
'xmlMetadataOwner.appendChild dom.createTextNode(vbCrLf & Space$(8))
'xmlMetadataOrganisation.appendChild dom.createTextNode(vbCrLf & Space$(10))
'Set xmlMetadataOrganisationName = dom.createElement("Name")
'xmlMetadataOrganisation.appendChild xmlMetadataOrganisationName
'xmlMetadataOrganisation.appendChild dom.createTextNode(vbCrLf & Space$(10))
'xmlMetadataOrganisationName.appendChild dom.createTextNode(vbCrLf & Space$(12))
'Set xmlMetadataOrgRepresentation = dom.createElement("Representation")
'xmlMetadataOrganisationName.appendChild xmlMetadataOrgRepresentation
'xmlMetadataOrganisationName.appendChild dom.createTextNode(vbCrLf & Space$(12))
'xmlMetadataOrgRepresentation.appendChild dom.createTextNode(vbCrLf & Space$(14))
'Set attrMetadataOrgRepresentation = dom.createNode(NODE_ATTRIBUTE, "xsi:language", "http://www.tdwg.org/schemas/abcd/2.06")
'attrMetadataOrgRepresentation.Value = "EN"
'xmlMetadataOrgRepresentation.setAttributeNode attrMetadataOrgRepresentation
'Set xmlMetadataOrgRepresentationText = dom.createElement("Text")
'xmlMetadataOrgRepresentationText.Text = "Royal Belgian Institute of Natural Sciences"
'xmlMetadataOrgRepresentation.appendChild xmlMetadataOrgRepresentationText
'xmlMetadataOrgRepresentation.appendChild dom.createTextNode(vbCrLf & Space$(14))
'Set xmlMetadataOrgRepresentationAbr = dom.createElement("Abreviation")
'xmlMetadataOrgRepresentationAbr.Text = "RBINS"
'xmlMetadataOrgRepresentation.appendChild xmlMetadataOrgRepresentationAbr
'xmlMetadataOrgRepresentation.appendChild dom.createTextNode(vbCrLf & Space$(14))
'
'Set xmlMetadataOwnerAddresses = dom.createElement("Addresses")
'xmlMetadataOwner.appendChild xmlMetadataOwnerAddresses
'xmlMetadataOwner.appendChild dom.createTextNode(vbCrLf & Space$(8))
'xmlMetadataOwnerAddresses.appendChild dom.createTextNode(vbCrLf & Space$(10))
'Set xmlMetadataOwnerAddress = dom.createElement("Address")
'xmlMetadataOwnerAddress.Text = "Rue Vautier Straat, 29 - 1000 Bruxelles/Brussels - Belgique/Belgie"
'xmlMetadataOwnerAddresses.appendChild xmlMetadataOwnerAddress
'xmlMetadataOwnerAddresses.appendChild dom.createTextNode(vbCrLf & Space$(10))
'
'Set xmlMetadataOwnerEmailAddresses = dom.createElement("EmailAddresses")
'xmlMetadataOwner.appendChild xmlMetadataOwnerEmailAddresses
'xmlMetadataOwner.appendChild dom.createTextNode(vbCrLf & Space$(8))
'xmlMetadataOwnerEmailAddresses.appendChild dom.createTextNode(vbCrLf & Space$(10))
'Set xmlMetadataOwnerEmailAddress = dom.createElement("EmailAddress")
'xmlMetadataOwnerEmailAddress.Text = "collections@naturalsciences.be"
'xmlMetadataOwnerEmailAddresses.appendChild xmlMetadataOwnerEmailAddress
'xmlMetadataOwnerEmailAddresses.appendChild dom.createTextNode(vbCrLf & Space$(10))
'
'Set xmlMetadataOwnerURI = dom.createElement("URI")
'xmlMetadataOwner.appendChild xmlMetadataOwnerURI
'xmlMetadataOwner.appendChild dom.createTextNode(vbCrLf & Space$(8))
'xmlMetadataOwnerURI.appendChild dom.createTextNode(vbCrLf & Space$(10))
'Set xmlMetadataOwnerURL = dom.createElement("URL")
'xmlMetadataOwnerURL.Text = "http://darwin.naturalsciences.be"
'xmlMetadataOwnerURI.appendChild xmlMetadataOwnerURL
'xmlMetadataOwnerURI.appendChild dom.createTextNode(vbCrLf & Space$(10))
'
'Set xmlMetadataOwnerLogoURI = dom.createElement("URL")
'xmlMetadataOwnerLogoURI.Text = "http://www.naturalsciences.be/layout_images/logo"
'xmlMetadataOwner.appendChild xmlMetadataOwnerLogoURI
'xmlMetadataOwner.appendChild dom.createTextNode(vbCrLf & Space$(8))
'
'Set xmlMetadataIPRStatements = dom.createElement("IPRStatements")
'node.appendChild xmlMetadataIPRStatements
'node.appendChild dom.createTextNode(vbCrLf & Space$(4))
'xmlMetadataIPRStatements.appendChild dom.createTextNode(vbCrLf & Space$(6))
'Set xmlMetadataCopyrights = dom.createElement("Copyrights")
'xmlMetadataIPRStatements.appendChild xmlMetadataCopyrights
'xmlMetadataIPRStatements.appendChild dom.createTextNode(vbCrLf & Space$(6))
'xmlMetadataCopyrights.appendChild dom.createTextNode(vbCrLf & Space$(8))
'Set xmlMetadataCopyright = dom.createElement("Copyright")
'xmlMetadataCopyrights.appendChild xmlMetadataCopyright
'xmlMetadataCopyrights.appendChild dom.createTextNode(vbCrLf & Space$(8))
'xmlMetadataCopyright.appendChild dom.createTextNode(vbCrLf & Space$(10))
'Set xmlMetadataCopyrightText = dom.createElement("Text")
'xmlMetadataCopyrightText.Text = "All data given access here are the sole property of the Royal Belgian Institute for Natural Sciences (RBINS) and are protected by the laws of copyright. " _
'& "The reuse of data, for any purpose whatsoever, is subject to prior authorization given by the Royal Belgian Institute for Natural Sciences (RBINS). For more informations, comments " _
'& "or details on the above lines, please contact the Royal Belgian Institute for Natural Sciences (RBINS)."
'xmlMetadataCopyright.appendChild xmlMetadataCopyrightText
'xmlMetadataCopyright.appendChild dom.createTextNode(vbCrLf & Space$(10))
'Set attrMetadataCopyright = dom.createNode(NODE_ATTRIBUTE, "abcd:language", "http://www.tdwg.org/schemas/abcd/2.06")
'attrMetadataCopyright.Value = "EN"
'xmlMetadataCopyright.setAttributeNode attrMetadataCopyright
'
'Exit Sub
'
'End Sub

Private Sub XMLSpecimenID(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Integer)

'**********************************************************************************
'Create node with identifier for samples in XML file
'**********************************************************************************

On Error GoTo Err_XMLSpecimenID

Dim xmlSpecimenUnitID As MSXML2.IXMLDOMElement
Dim xmlSourceID As MSXML2.IXMLDOMElement
Dim xmlSourceInstitutionID As MSXML2.IXMLDOMElement

Dim strSpecimenUnitID As String
Dim strSourceID As String
Dim strSourceInstitutionID As String
Dim SpecimenUnitID As String, SpecimenUnitID_alternative As String

SpecimenUnitID = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Museum_voucher_ID, lookAt:=xlWhole).Column)
SpecimenUnitID_alternative = Trim(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Institution_storing, lookAt:=xlWhole).Column)) & ":" & _
Trim(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collection_Code, lookAt:=xlWhole).Column)) & ":" & _
Trim(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Field_ID, lookAt:=xlWhole).Column))

If Not IsEmpty(SpecimenUnitID) And Not IsNull(SpecimenUnitID) And SpecimenUnitID <> "" Then
    strSpecimenUnitID = SpecimenUnitID
ElseIf SpecimenUnitID_alternative <> "::" Then
    strSpecimenUnitID = SpecimenUnitID_alternative
Else:
    strSpecimenUnitID = "Undefined"
End If

strSourceID = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collection_Code, lookAt:=xlWhole).Column).Value
strSourceInstitutionID = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Institution_storing, lookAt:=xlWhole).Column).Value

If strSourceInstitutionID <> "" Then
    Set xmlSourceInstitutionID = dom.createNode(NODE_ELEMENT, "SourceInstitutionID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSourceInstitutionID.Text = strSourceInstitutionID
    subnode.appendChild xmlSourceInstitutionID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
Else
    Set xmlSourceInstitutionID = dom.createNode(NODE_ELEMENT, "SourceInstitutionID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSourceInstitutionID.Text = "Not defined"
    subnode.appendChild xmlSourceInstitutionID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
End If
If strSourceID <> "" Then
    Set xmlSourceID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSourceID.Text = strSourceID
    subnode.appendChild xmlSourceID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
Else
    Set xmlSourceID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSourceID.Text = "Not defined"
    subnode.appendChild xmlSourceID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
End If
If strSpecimenUnitID <> "" Then
    Set xmlSpecimenUnitID = dom.createNode(NODE_ELEMENT, "UnitID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSpecimenUnitID.Text = strSpecimenUnitID
    subnode.appendChild xmlSpecimenUnitID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
Else
    Set xmlSpecimenUnitID = dom.createNode(NODE_ELEMENT, "UnitID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSpecimenUnitID.Text = "Not defined"
    subnode.appendChild xmlSpecimenUnitID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
End If

Exit_XMLSpecimenID:
    
    Exit Sub
    
Err_XMLSpecimenID:
    
    MsgBox prompt:="An error occured in sub XMLSpecimenID." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLSpecimenID"
    Resume Exit_XMLSpecimenID

End Sub

Private Sub XMLSampleID(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Integer)

'**********************************************************************************
'Create node with identifier for samples in XML file
'**********************************************************************************

On Error GoTo Err_XMLSampleID

Dim xmlSampleUnitID As MSXML2.IXMLDOMElement
Dim xmlSourceID As MSXML2.IXMLDOMElement
Dim xmlSourceInstitutionID As MSXML2.IXMLDOMElement

Dim strSampleUnitID As String
Dim strSourceID As String
Dim strSourceInstitutionID As String

strSampleUnitID = Application.Sheets("cSAMPLE").Cells(rowConcerned, Application.Sheets("cSAMPLE").Rows(1).Find(Sample_ID, lookAt:=xlWhole).Column).Value
strSourceID = "Not defined"
strSourceInstitutionID = Application.Sheets("cSAMPLE").Cells(rowConcerned, Application.Sheets("cSAMPLE").Rows(1).Find(Institution, lookAt:=xlWhole).Column).Value

If strSourceInstitutionID <> "" Then
    Set xmlSourceInstitutionID = dom.createNode(NODE_ELEMENT, "SourceInstitutionID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSourceInstitutionID.Text = strSourceInstitutionID
    subnode.appendChild xmlSourceInstitutionID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
End If
If strSourceID <> "" Then
    Set xmlSourceID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSourceID.Text = strSourceID
    subnode.appendChild xmlSourceID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
End If
If strSampleUnitID <> "" Then
    Set xmlSampleUnitID = dom.createNode(NODE_ELEMENT, "UnitID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSampleUnitID.Text = strSampleUnitID
    subnode.appendChild xmlSampleUnitID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
End If

Exit_XMLSampleID:
    
    Exit Sub
    
Err_XMLSampleID:
    
    MsgBox prompt:="An error occured in sub XMLSampleID." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLSampleID"
    Resume Exit_XMLSampleID

End Sub

Private Sub XMLSampleAssociation(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Integer, ByRef associatedUnit As String, ByRef associatedInstitution As String, ByRef associatedSource As String)

'**********************************************************************************
'Create node with identifier for samples in XML file
'**********************************************************************************

On Error GoTo Err_XMLSampleAssociation

Dim xmlAssociations As MSXML2.IXMLDOMElement
Dim xmlUnitAssociation As MSXML2.IXMLDOMElement
Dim xmlAssociatedUnitID As MSXML2.IXMLDOMElement
Dim xmlAssociatedInstitutionID As MSXML2.IXMLDOMElement
Dim xmlAssociatedSourceID As MSXML2.IXMLDOMElement
Dim xmlAssociationType As MSXML2.IXMLDOMElement

Set xmlAssociations = dom.createNode(NODE_ELEMENT, "Associations", "http://www.tdwg.org/schemas/abcd/2.06")
subnode.appendChild xmlAssociations
subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(10))
Set xmlUnitAssociation = dom.createNode(NODE_ELEMENT, "UnitAssociation", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociations.appendChild xmlUnitAssociation
xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(10))
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
Set xmlAssociatedInstitutionID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceInstitutionCode", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociatedInstitutionID.Text = associatedInstitution
xmlUnitAssociation.appendChild xmlAssociatedInstitutionID
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
Set xmlAssociatedSourceID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceName", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociatedSourceID.Text = associatedSource
xmlUnitAssociation.appendChild xmlAssociatedSourceID
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
Set xmlAssociatedUnitID = dom.createNode(NODE_ELEMENT, "AssociatedUnitID", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociatedUnitID.Text = associatedUnit
xmlUnitAssociation.appendChild xmlAssociatedUnitID
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
Set xmlAssociationType = dom.createNode(NODE_ELEMENT, "AssociationType", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociationType.Text = "Sample-specimen"
xmlUnitAssociation.appendChild xmlAssociationType
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))

Exit_XMLSampleAssociation:
    
    Exit Sub
    
Err_XMLSampleAssociation:
    
    MsgBox prompt:="An error occured in sub XMLSampleAssociation." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLSampleAssociation"
    Resume Exit_XMLSampleAssociation

End Sub

Private Sub XMLSampleUnit(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Integer)

'**********************************************************************************
'Create node about sample in XML file
'**********************************************************************************

On Error GoTo Err_XMLSampleUnit

Dim xmlSpecUnit As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitAcquisition As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitAcquiredFrom As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitOrganisation As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitPerson As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitEmailAddresses As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitOrganisationName As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitPersonFullName As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitEmailAddress As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitPreparations As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitPreparation As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitPreparationType As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitPreparationMaterials As MSXML2.IXMLDOMElement
Dim xmlSpecimenUnitOrganisationRepr As MSXML2.IXMLDOMElement
Dim attrSpecimenUnitOrganisationRepr As MSXML2.IXMLDOMAttribute
Dim xmlSpecimenUnitOrganisationReprText As MSXML2.IXMLDOMElement

Dim strName As String
Dim strFullName As String
Dim strEmailAddress As String
Dim strPreparationAgent As String
Dim strPreparationType As String
Dim strPreparationMaterials As String
Dim TestIfEmpty As String

strName = Application.Sheets("cSAMPLE").Cells(rowConcerned, Application.Sheets("cSAMPLE").Rows(1).Find(Sample_DonorInstitution, lookAt:=xlWhole).Column).Value
strFullName = Application.Sheets("cSAMPLE").Cells(rowConcerned, Application.Sheets("cSAMPLE").Rows(1).Find(Sample_DonorPerson, lookAt:=xlWhole).Column).Value
strEmailAddress = Application.Sheets("cSAMPLE").Cells(rowConcerned, Application.Sheets("cSAMPLE").Rows(1).Find(Donor_Email, lookAt:=xlWhole).Column).Value
strPreparationType = Application.Sheets("cSAMPLE").Cells(rowConcerned, Application.Sheets("cSAMPLE").Rows(1).Find(Sample_protocol, lookAt:=xlWhole).Column).Value
strPreparationMaterials = Application.Sheets("cSAMPLE").Cells(rowConcerned, Application.Sheets("cSAMPLE").Rows(1).Find(Sample_preservation, lookAt:=xlWhole).Column).Value
TestIfEmpty = strName & strFullName & strEmailAddress & strPreparationType & strPreparationMaterials

If TestIfEmpty <> "" Then
Set xmlSpecUnit = dom.createNode(NODE_ELEMENT, "SpecimenUnit", "http://www.tdwg.org/schemas/abcd/2.06")
subnode.appendChild xmlSpecUnit
subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))

    'Create tree SpecimenUnit-Acquisition-AcquiredFrom if cells are not empty
    If strName <> "" Then
        If strFullName <> "" Then
            If strEmailAddress <> "" Then
    
                Set xmlSpecimenUnitAcquisition = dom.createNode(NODE_ELEMENT, "Acquisition", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecUnit.appendChild xmlSpecimenUnitAcquisition
                xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlSpecimenUnitAcquisition.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                Set xmlSpecimenUnitAcquiredFrom = dom.createNode(NODE_ELEMENT, "AcquiredFrom", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitAcquisition.appendChild xmlSpecimenUnitAcquiredFrom
                xmlSpecimenUnitAcquisition.appendChild dom.createTextNode(vbCrLf + Space$(12))
                xmlSpecimenUnitAcquiredFrom.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
                If strName <> "" Then
    
                    Set xmlSpecimenUnitOrganisation = dom.createNode(NODE_ELEMENT, "Organisation", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSpecimenUnitAcquiredFrom.appendChild xmlSpecimenUnitOrganisation
                    xmlSpecimenUnitAcquiredFrom.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    xmlSpecimenUnitOrganisation.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
                    Set xmlSpecimenUnitOrganisationName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSpecimenUnitOrganisation.appendChild xmlSpecimenUnitOrganisationName
                    xmlSpecimenUnitOrganisation.appendChild dom.createTextNode(vbCrLf + Space$(16))
                    xmlSpecimenUnitOrganisationName.appendChild dom.createTextNode(vbCrLf + Space$(18))
    
                    Set xmlSpecimenUnitOrganisationRepr = dom.createNode(NODE_ELEMENT, "Representation", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSpecimenUnitOrganisationName.appendChild xmlSpecimenUnitOrganisationRepr
                    xmlSpecimenUnitOrganisationName.appendChild dom.createTextNode(vbCrLf + Space$(18))
                    xmlSpecimenUnitOrganisationRepr.appendChild dom.createTextNode(vbCrLf + Space$(20))
    
                    Set xmlSpecimenUnitOrganisationReprText = dom.createNode(NODE_ELEMENT, "Text", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSpecimenUnitOrganisationReprText.Text = strName
                    xmlSpecimenUnitOrganisationRepr.appendChild xmlSpecimenUnitOrganisationReprText
                    xmlSpecimenUnitOrganisationRepr.appendChild dom.createTextNode(vbCrLf + Space$(18))
    
                    Set attrSpecimenUnitOrganisationRepr = dom.createNode(NODE_ATTRIBUTE, "language", "http://www.tdwg.org/schemas/abcd/2.06")
                    attrSpecimenUnitOrganisationRepr.Value = "EN"
                    xmlSpecimenUnitOrganisationRepr.setAttributeNode attrSpecimenUnitOrganisationRepr
    
                End If
    
                If strFullName <> "" Then
    
                    Set xmlSpecimenUnitPerson = dom.createNode(NODE_ELEMENT, "Person", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSpecimenUnitAcquiredFrom.appendChild xmlSpecimenUnitPerson
                    xmlSpecimenUnitAcquiredFrom.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    xmlSpecimenUnitPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
                    Set xmlSpecimenUnitPersonFullName = dom.createNode(NODE_ELEMENT, "FullName", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSpecimenUnitPersonFullName.Text = strFullName
                    xmlSpecimenUnitPerson.appendChild xmlSpecimenUnitPersonFullName
                    xmlSpecimenUnitPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
                End If
    
                If strEmailAddress <> "" Then
    
                    Set xmlSpecimenUnitEmailAddresses = dom.createNode(NODE_ELEMENT, "EmailAddresses", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSpecimenUnitAcquiredFrom.appendChild xmlSpecimenUnitEmailAddresses
                    xmlSpecimenUnitAcquiredFrom.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    xmlSpecimenUnitEmailAddresses.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
                    Set xmlSpecimenUnitEmailAddress = dom.createNode(NODE_ELEMENT, "EmailAddress", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSpecimenUnitEmailAddress.Text = strEmailAddress
                    xmlSpecimenUnitEmailAddresses.appendChild xmlSpecimenUnitEmailAddress
                    xmlSpecimenUnitEmailAddresses.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
                End If
    
            End If
        End If
    End If
    
    'Create tree SpecimenUnit-Preparation if cells are not empty
    If strPreparationType <> "" And strPreparationMaterials <> "" Then
    
            Set xmlSpecimenUnitPreparations = dom.createNode(NODE_ELEMENT, "Preparations", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitPreparations
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlSpecimenUnitPreparation = dom.createNode(NODE_ELEMENT, "Preparation", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitPreparations.appendChild xmlSpecimenUnitPreparation
            xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
            Set xmlSpecimenUnitPreparationType = dom.createNode(NODE_ELEMENT, "PreparationType", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitPreparationType.Text = strPreparationType
            xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationType
            xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
            Set xmlSpecimenUnitPreparationMaterials = dom.createNode(NODE_ELEMENT, "PreparationMaterials", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitPreparationMaterials.Text = strPreparationMaterials
            xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationMaterials
            xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
    End If
    
    If strPreparationType <> "" And strPreparationMaterials = "" Then
    
            Set xmlSpecimenUnitPreparations = dom.createNode(NODE_ELEMENT, "Preparations", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitPreparations
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlSpecimenUnitPreparation = dom.createNode(NODE_ELEMENT, "Preparation", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitPreparations.appendChild xmlSpecimenUnitPreparation
            xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
            Set xmlSpecimenUnitPreparationType = dom.createNode(NODE_ELEMENT, "PreparationType", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitPreparationType.Text = strPreparationType
            xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationType
            xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
    End If
    
    If strPreparationType = "" And strPreparationMaterials <> "" Then
    
            Set xmlSpecimenUnitPreparations = dom.createNode(NODE_ELEMENT, "Preparations", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitPreparations
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlSpecimenUnitPreparation = dom.createNode(NODE_ELEMENT, "Preparation", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitPreparations.appendChild xmlSpecimenUnitPreparation
            xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
            Set xmlSpecimenUnitPreparationMaterials = dom.createNode(NODE_ELEMENT, "PreparationMaterials", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitPreparationMaterials.Text = strPreparationMaterials
            xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationMaterials
            xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
    End If
    
End If

Exit_XMLSampleUnit:

    Exit Sub

Err_XMLSampleUnit:

    MsgBox prompt:="An error occured in sub XMLSampleUnit." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLSampleUnit"
    Resume Exit_XMLSampleUnit

End Sub


Private Sub XMLIdentification(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Integer)

'**********************************************************************************
'Create node with identifications information in XML file
'**********************************************************************************

On Error GoTo Err_XMLIdentification

Dim xmlIdentifications As MSXML2.IXMLDOMElement
Dim xmlIdIdentification As MSXML2.IXMLDOMElement
Dim xmlIdentificationResult As MSXML2.IXMLDOMElement
Dim xmlIdentificationTaxonId As MSXML2.IXMLDOMElement
Dim xmlIdentificationScName As MSXML2.IXMLDOMElement
Dim xmlIdentificationFullScNameString As MSXML2.IXMLDOMElement
Dim xmlIdentificationHiTaxa As MSXML2.IXMLDOMElement
Dim xmlIdentificationHiTaxon As MSXML2.IXMLDOMElement
Dim xmlIdentificationHiTaxonName As MSXML2.IXMLDOMElement
Dim xmlIdentificationHiTaxonRank As MSXML2.IXMLDOMElement
Dim xmlIdentificationNameAtom As MSXML2.IXMLDOMElement
Dim xmlIdentificationNameAtomZoo As MSXML2.IXMLDOMElement
Dim xmlIdentificationGenusOrMonomial As MSXML2.IXMLDOMElement
Dim xmlIdentificationSpeciesEpithet As MSXML2.IXMLDOMElement
Dim xmlIdentificationSubSpeciesEpithet As MSXML2.IXMLDOMElement
Dim xmlIdentificationNameAt As MSXML2.IXMLDOMElement
Dim xmlIdentificationZoo As MSXML2.IXMLDOMElement
Dim xmlIdentificationZooGenus As MSXML2.IXMLDOMElement
Dim xmlIdentificationZooSpecies As MSXML2.IXMLDOMElement
Dim xmlIdentificationZooSubspecies As MSXML2.IXMLDOMElement
Dim xmlIdentificationBota As MSXML2.IXMLDOMElement
Dim xmlIdentificationBotaGenus As MSXML2.IXMLDOMElement
Dim xmlIdentificationBotaSpecies As MSXML2.IXMLDOMElement
Dim xmlIdentificationBotaSubSpecies As MSXML2.IXMLDOMElement
Dim xmlIdentificationIdentifiers As MSXML2.IXMLDOMElement
Dim xmlIdentificationIdentifier As MSXML2.IXMLDOMElement
Dim xmlIdentifierPerson As MSXML2.IXMLDOMElement
Dim xmlIdentifierPersonName As MSXML2.IXMLDOMElement
Dim xmlIdentifierOrg As MSXML2.IXMLDOMElement
Dim xmlIdentifierOrgName As MSXML2.IXMLDOMElement
Dim xmlIdentifierOrgRepr As MSXML2.IXMLDOMElement
Dim xmlIdentifierOrgText As MSXML2.IXMLDOMElement
Dim attrIdentifierOrgText As MSXML2.IXMLDOMAttribute

Dim strScName As String
Dim strScNameH As String
Dim strTaxonRank As String
Dim strTaxonName As String
Dim strGenus As String
Dim strSpecies As String
Dim strSubspecies As String
Dim strIdentifierPerson As String
Dim strIdentifierOrg As String

Dim rep As String, rep2 As String, sep As String
Dim cel
rep = ""
rep2 = ""
sep = " "

Dim Count As Integer
Dim taxonomy(8)
    taxonomy(1) = Phylum
    taxonomy(2) = Class
    taxonomy(3) = Order
    taxonomy(4) = Family
    taxonomy(5) = Subfamily
    taxonomy(6) = Genus
    taxonomy(7) = Species
    taxonomy(8) = Subspecies

Dim rank(5) As String
    rank(1) = "phylum"
    rank(2) = "classis"
    rank(3) = "ordo"
    rank(4) = "familia"
    rank(5) = "subfamilia"
Dim rankConcerned As Long

For Count = 6 To 8
    Set cel = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(taxonomy(Count), lookAt:=xlWhole).Column)
    If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
        rep = rep & cel.Value & sep
    End If
Next Count

strScName = RTrim(rep)

For Count = 1 To 5
    Set cel = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(taxonomy(Count), lookAt:=xlWhole).Column)
    If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
        rep2 = rep2 & cel.Value & sep
    End If
Next Count

strScNameH = RTrim(rep2)

If Not IsEmpty(strScName) And Not IsNull(strScName) And strScName <> "" _
    Or Not IsEmpty(strScNameH) And Not IsNull(strScNameH) And strScNameH <> "" Then

    Set xmlIdentifications = dom.createNode(NODE_ELEMENT, "Identifications", "http://www.tdwg.org/schemas/abcd/2.06")
    subnode.appendChild xmlIdentifications
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    xmlIdentifications.appendChild dom.createTextNode(vbCrLf + Space$(8))

    Set xmlIdIdentification = dom.createNode(NODE_ELEMENT, "Identification", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlIdentifications.appendChild xmlIdIdentification
    xmlIdentifications.appendChild dom.createTextNode(vbCrLf + Space$(8))
    xmlIdIdentification.appendChild dom.createTextNode(vbCrLf + Space$(10))

    Set xmlIdentificationResult = dom.createNode(NODE_ELEMENT, "Result", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlIdIdentification.appendChild xmlIdentificationResult
    xmlIdIdentification.appendChild dom.createTextNode(vbCrLf + Space$(10))
    xmlIdentificationResult.appendChild dom.createTextNode(vbCrLf + Space$(12))

    Set xmlIdentificationTaxonId = dom.createNode(NODE_ELEMENT, "TaxonIdentified", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlIdentificationResult.appendChild xmlIdentificationTaxonId
    xmlIdentificationResult.appendChild dom.createTextNode(vbCrLf + Space$(12))
    xmlIdentificationTaxonId.appendChild dom.createTextNode(vbCrLf + Space$(14))

    If Not IsEmpty(strScNameH) And Not IsNull(strScNameH) And strScNameH <> "" Then
        Set xmlIdentificationHiTaxa = dom.createNode(NODE_ELEMENT, "HigherTaxa", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlIdentificationTaxonId.appendChild xmlIdentificationHiTaxa
        xmlIdentificationTaxonId.appendChild dom.createTextNode(vbCrLf + Space$(14))
        xmlIdentificationHiTaxa.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
    'Taxonomic data
        For Count = 1 To 5
            Set cel = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(taxonomy(Count), lookAt:=xlWhole).Column)
            
            If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
         
                Set xmlIdentificationHiTaxon = dom.createNode(NODE_ELEMENT, "HigherTaxon", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationHiTaxa.appendChild xmlIdentificationHiTaxon
                xmlIdentificationHiTaxa.appendChild dom.createTextNode(vbCrLf + Space$(16))
                xmlIdentificationHiTaxon.appendChild dom.createTextNode(vbCrLf + Space$(18))
    
                Set xmlIdentificationHiTaxonName = dom.createNode(NODE_ELEMENT, "HigherTaxonName", "http://www.tdwg.org/schemas/abcd/2.06")
                strTaxonName = cel.Value
                xmlIdentificationHiTaxonName.Text = strTaxonName
                xmlIdentificationHiTaxon.appendChild xmlIdentificationHiTaxonName
                xmlIdentificationHiTaxon.appendChild dom.createTextNode(vbCrLf + Space$(18))
    
                Select Case cel.Column
                    Case Application.Sheets("cSPECIMEN").Rows(1).Find(Phylum, lookAt:=xlWhole).Column
                        rankConcerned = 1
                    Case Application.Sheets("cSPECIMEN").Rows(1).Find(Class, lookAt:=xlWhole).Column
                        rankConcerned = 2
                    Case Application.Sheets("cSPECIMEN").Rows(1).Find(Order, lookAt:=xlWhole).Column
                        rankConcerned = 3
                    Case Application.Sheets("cSPECIMEN").Rows(1).Find(Family, lookAt:=xlWhole).Column
                        rankConcerned = 4
                    Case Application.Sheets("cSPECIMEN").Rows(1).Find(Subfamily, lookAt:=xlWhole).Column
                        rankConcerned = 5
                End Select
    
                strTaxonRank = rank(rankConcerned)
    
                Set xmlIdentificationHiTaxonRank = dom.createNode(NODE_ELEMENT, "HigherTaxonRank", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationHiTaxonRank.Text = strTaxonRank
                xmlIdentificationHiTaxon.appendChild xmlIdentificationHiTaxonRank
                xmlIdentificationHiTaxon.appendChild dom.createTextNode(vbCrLf + Space$(18))
            
            End If
        
        Next Count
    
    End If

    If Not IsEmpty(strScName) And Not IsNull(strScName) And strScName <> "" Then
        
        Set xmlIdentificationScName = dom.createNode(NODE_ELEMENT, "ScientificName", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlIdentificationTaxonId.appendChild xmlIdentificationScName
        xmlIdentificationTaxonId.appendChild dom.createTextNode(vbCrLf + Space$(14))
        xmlIdentificationScName.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
        Set xmlIdentificationFullScNameString = dom.createNode(NODE_ELEMENT, "FullScientificNameString", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlIdentificationFullScNameString.Text = strScName
        xmlIdentificationScName.appendChild xmlIdentificationFullScNameString
        xmlIdentificationScName.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
    'Condition: s'il y a des infos dans les cellules genre, espèce, sous-espèce
        Set xmlIdentificationNameAt = dom.createNode(NODE_ELEMENT, "NameAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlIdentificationScName.appendChild xmlIdentificationNameAt
        xmlIdentificationScName.appendChild dom.createTextNode(vbCrLf + Space$(16))
        xmlIdentificationNameAt.appendChild dom.createTextNode(vbCrLf + Space$(18))
        
        If Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Classification, lookAt:=xlWhole).Column).Value = "Zoological" Then
            Set xmlIdentificationZoo = dom.createNode(NODE_ELEMENT, "Zoological", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlIdentificationNameAt.appendChild xmlIdentificationZoo
            xmlIdentificationNameAt.appendChild dom.createTextNode(vbCrLf + Space$(18))
            xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
            'Genus and species data
                For Count = 6 To 8
                    Set cel = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(taxonomy(Count), lookAt:=xlWhole).Column)
                    
                    If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
                            
                            Select Case cel.Column
                            Case Application.Sheets("cSPECIMEN").Rows(1).Find(Genus, lookAt:=xlWhole).Column
                                Set xmlIdentificationZooGenus = dom.createNode(NODE_ELEMENT, "GenusOrMonomial", "http://www.tdwg.org/schemas/abcd/2.06")
                                strGenus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Genus, lookAt:=xlWhole).Column).Value
                                xmlIdentificationZooGenus.Text = strGenus
                                xmlIdentificationZoo.appendChild xmlIdentificationZooGenus
                                xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                            Case Application.Sheets("cSPECIMEN").Rows(1).Find(Species, lookAt:=xlWhole).Column
                                Set xmlIdentificationZooSpecies = dom.createNode(NODE_ELEMENT, "SpeciesEpithet", "http://www.tdwg.org/schemas/abcd/2.06")
                                strSpecies = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Species, lookAt:=xlWhole).Column).Value
                                xmlIdentificationZooSpecies.Text = strSpecies
                                xmlIdentificationZoo.appendChild xmlIdentificationZooSpecies
                                xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                            Case Application.Sheets("cSPECIMEN").Rows(1).Find(Subspecies, lookAt:=xlWhole).Column
                                Set xmlIdentificationZooSubspecies = dom.createNode(NODE_ELEMENT, "SubspeciesEpithet", "http://www.tdwg.org/schemas/abcd/2.06")
                                strSubspecies = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Subspecies, lookAt:=xlWhole).Column).Value
                                xmlIdentificationZooSubspecies.Text = strSubspecies
                                xmlIdentificationZoo.appendChild xmlIdentificationZooSubspecies
                                xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                            End Select
    
                    End If
                
                Next Count
    
        ElseIf Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Classification, lookAt:=xlWhole).Column).Value = "Botanical" Then
            Set xmlIdentificationBota = dom.createNode(NODE_ELEMENT, "Botanical", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlIdentificationNameAt.appendChild xmlIdentificationBota
            xmlIdentificationNameAt.appendChild dom.createTextNode(vbCrLf + Space$(18))
            xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
            'Genus and species data
                For Count = 6 To 8
                    Set cel = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(taxonomy(Count), lookAt:=xlWhole).Column)
                    If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
                            
                            Select Case cel.Column
                            Case Application.Sheets("cSPECIMEN").Rows(1).Find(Genus, lookAt:=xlWhole).Column
                                Set xmlIdentificationBotaGenus = dom.createNode(NODE_ELEMENT, "GenusOrMonomial", "http://www.tdwg.org/schemas/abcd/2.06")
                                strGenus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Genus, lookAt:=xlWhole).Column).Value
                                xmlIdentificationBotaGenus.Text = strGenus
                                xmlIdentificationBota.appendChild xmlIdentificationBotaGenus
                                xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
                            Case Application.Sheets("cSPECIMEN").Rows(1).Find(Species, lookAt:=xlWhole).Column
                                Set xmlIdentificationBotaSpecies = dom.createNode(NODE_ELEMENT, "FirstEpithet", "http://www.tdwg.org/schemas/abcd/2.06")
                                strSpecies = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Species, lookAt:=xlWhole).Column).Value
                                xmlIdentificationBotaSpecies.Text = strSpecies
                                xmlIdentificationBota.appendChild xmlIdentificationBotaSpecies
                                xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
                            Case Application.Sheets("cSPECIMEN").Rows(1).Find(Subspecies, lookAt:=xlWhole).Column
                                Set xmlIdentificationBotaSubSpecies = dom.createNode(NODE_ELEMENT, "InfraspecificEpithet", "http://www.tdwg.org/schemas/abcd/2.06")
                                strSpecies = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Subspecies, lookAt:=xlWhole).Column).Value
                                xmlIdentificationBotaSpecies.Text = strSpecies
                                xmlIdentificationBota.appendChild xmlIdentificationBotaSpecies
                                xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
                            End Select
    
                    End If
                Next Count
       
        End If
    End If

'Je laisse les identifiers dans le if strName a une valeur car ça n'aurait pas de sens de rapporter un identifier s'il n'y a pas d'info d'identification

    If (Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier, lookAt:=xlWhole).Column).Value) _
        And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier, lookAt:=xlWhole).Column).Value) _
        And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier, lookAt:=xlWhole).Column).Value <> "") _
        Or (Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier_Institution, lookAt:=xlWhole).Column).Value) _
        And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier_Institution, lookAt:=xlWhole).Column).Value) _
        And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier_Institution, lookAt:=xlWhole).Column).Value <> "") Then

            Set xmlIdentificationIdentifiers = dom.createNode(NODE_ELEMENT, "Identifiers", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlIdIdentification.appendChild xmlIdentificationIdentifiers
            xmlIdIdentification.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlIdentificationIdentifiers.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
            Set xmlIdentificationIdentifier = dom.createNode(NODE_ELEMENT, "Identifier", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlIdentificationIdentifiers.appendChild xmlIdentificationIdentifier
            xmlIdentificationIdentifiers.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlIdentificationIdentifier.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier, lookAt:=xlWhole).Column).Value) _
            And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier, lookAt:=xlWhole).Column).Value) _
            And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier, lookAt:=xlWhole).Column).Value <> "" Then
            
                Set xmlIdentifierPerson = dom.createNode(NODE_ELEMENT, "PersonName", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationIdentifier.appendChild xmlIdentifierPerson
                xmlIdentificationIdentifier.appendChild dom.createTextNode(vbCrLf + Space$(14))
                xmlIdentifierPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
                Set xmlIdentifierPersonName = dom.createNode(NODE_ELEMENT, "FullName", "http://www.tdwg.org/schemas/abcd/2.06")
                strIdentifierPerson = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier, lookAt:=xlWhole).Column).Value
                xmlIdentifierPersonName.Text = strIdentifierPerson
                xmlIdentifierPerson.appendChild xmlIdentifierPersonName
                xmlIdentifierPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
            End If
            
            If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier_Institution, lookAt:=xlWhole).Column).Value) _
            And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier_Institution, lookAt:=xlWhole).Column).Value) _
            And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier_Institution, lookAt:=xlWhole).Column).Value <> "" Then
            
                Set xmlIdentifierOrg = dom.createNode(NODE_ELEMENT, "Organisation", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationIdentifier.appendChild xmlIdentifierOrg
                xmlIdentificationIdentifier.appendChild dom.createTextNode(vbCrLf + Space$(14))
                xmlIdentifierOrg.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
                Set xmlIdentifierOrgName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentifierOrg.appendChild xmlIdentifierOrgName
                xmlIdentifierOrg.appendChild dom.createTextNode(vbCrLf + Space$(16))
                xmlIdentifierOrgName.appendChild dom.createTextNode(vbCrLf + Space$(18))

                Set xmlIdentifierOrgRepr = dom.createNode(NODE_ELEMENT, "Representation", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentifierOrgName.appendChild xmlIdentifierOrgRepr
                xmlIdentifierOrgName.appendChild dom.createTextNode(vbCrLf + Space$(18))
                xmlIdentifierOrgRepr.appendChild dom.createTextNode(vbCrLf + Space$(20))

                Set xmlIdentifierOrgText = dom.createNode(NODE_ELEMENT, "Text", "http://www.tdwg.org/schemas/abcd/2.06")
                strIdentifierOrg = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Identifier_Institution, lookAt:=xlWhole).Column).Value
                xmlIdentifierOrgText.Text = strIdentifierOrg
                xmlIdentifierOrgRepr.appendChild xmlIdentifierOrgText
                xmlIdentifierOrgRepr.appendChild dom.createTextNode(vbCrLf + Space$(20))
                
                Set attrIdentifierOrgText = dom.createNode(NODE_ATTRIBUTE, "language", "http://www.tdwg.org/schemas/abcd/2.06")
                attrIdentifierOrgText.Value = "EN"
                xmlIdentifierOrgRepr.setAttributeNode attrIdentifierOrgText

            End If

    End If


End If

Exit_XMLIdentification:
    
    Exit Sub
    
Err_XMLIdentification:
    
    MsgBox prompt:="An error occured in sub XMLIdentification." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLIdentification"
    Resume Exit_XMLIdentification

End Sub

Private Sub XMLGather(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Integer)

'**********************************************************************************
'Create node with information on gathering in XML file
'**********************************************************************************

On Error GoTo Err_XMLGather

Dim xmlGathering As MSXML2.IXMLDOMElement
Dim xmlGatheringProject As MSXML2.IXMLDOMElement
Dim xmlGatheringProjectTitle As MSXML2.IXMLDOMElement
Dim xmlGatheringNamedAreas As MSXML2.IXMLDOMElement
Dim xmlGatheringNamedArea As MSXML2.IXMLDOMElement
Dim xmlGatheringAreaName As MSXML2.IXMLDOMElement
Dim xmlGatheringAreaClass As MSXML2.IXMLDOMElement
Dim xmlGatheringLocality As MSXML2.IXMLDOMElement
Dim xmlGatheringLocalityText As MSXML2.IXMLDOMElement
Dim xmlGatheringCountry As MSXML2.IXMLDOMElement
Dim xmlGatheringCountryName As MSXML2.IXMLDOMElement
Dim xmlGatheringAgents As MSXML2.IXMLDOMElement
Dim xmlGatheringAgent As MSXML2.IXMLDOMElement
Dim xmlGatheringAgentPerson As MSXML2.IXMLDOMElement
Dim xmlGatheringAgentFullName As MSXML2.IXMLDOMElement
Dim xmlGatheringDateTime As MSXML2.IXMLDOMElement
Dim xmlGatheringDateText As MSXML2.IXMLDOMElement
Dim xmlGatheringSiteCoordSets As MSXML2.IXMLDOMElement
Dim xmlGatheringSiteCoord As MSXML2.IXMLDOMElement
Dim xmlGatheringCoordLatLong As MSXML2.IXMLDOMElement
Dim xmlGatheringCoordLatDec As MSXML2.IXMLDOMElement
Dim xmlGatheringCoordLongDec As MSXML2.IXMLDOMElement
Dim xmlGatheringElevation As MSXML2.IXMLDOMElement
Dim xmlGatheringElevationMeasure As MSXML2.IXMLDOMElement
Dim xmlGatheringElevationValue As MSXML2.IXMLDOMElement
Dim xmlGatheringElevationUnit As MSXML2.IXMLDOMElement
Dim xmlGatheringDepth As MSXML2.IXMLDOMElement
Dim xmlGatheringDepthMeasure As MSXML2.IXMLDOMElement
Dim xmlGatheringDepthValue As MSXML2.IXMLDOMElement
Dim xmlGatheringDepthUnit As MSXML2.IXMLDOMElement

Dim strProjectTitle As String
Dim strFullName As String
Dim strRole As String
Dim strAreaName As String
Dim strAreaClass As String
Dim strDateText As String
Dim strGatheringLocality As String
Dim strElevation As String
Dim strDepth As String

Dim country

Set xmlGathering = dom.createNode(NODE_ELEMENT, "Gathering", "http://www.tdwg.org/schemas/abcd/2.06")
subnode.appendChild xmlGathering
subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))

Dim Count As Integer
Dim location(5)
    location(1) = Continent
    location(2) = country
    location(3) = Province
    location(4) = Region
    location(5) = Municipality
Dim cel

Dim rank(5) As String
    rank(1) = "Continent"
    rank(2) = "Country"
    rank(3) = "Province"
    rank(4) = "Region"
    rank(5) = "Municipality"
Dim rankConcerned As Long

Dim rep As String, sep As String
rep = ""
sep = " "

If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collection_Date, lookAt:=xlWhole).Column).Value) _
    And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collection_Date, lookAt:=xlWhole).Column).Value) _
    And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collection_Date, lookAt:=xlWhole).Column).Value <> "" Then
      
        Set xmlGatheringDateTime = dom.createNode(NODE_ELEMENT, "DateTime", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlGathering.appendChild xmlGatheringDateTime
        xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlGatheringDateTime.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
        Set xmlGatheringDateText = dom.createNode(NODE_ELEMENT, "DateText", "http://www.tdwg.org/schemas/abcd/2.06")
        strDateText = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collection_Date, lookAt:=xlWhole).Column).Value
        If IsDate(strDateText) Then
            xmlGatheringDateText.Text = CDate(Format(strDateText, "dd/mm/yyyy"))
        Else:
            xmlGatheringDateText.Text = strDateText
        End If
        xmlGatheringDateTime.appendChild xmlGatheringDateText
        xmlGatheringDateTime.appendChild dom.createTextNode(vbCrLf + Space$(10))

End If

If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collector, lookAt:=xlWhole).Column).Value) _
    And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collector, lookAt:=xlWhole).Column).Value) _
    And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collector, lookAt:=xlWhole).Column).Value <> "" Then

        Set xmlGatheringAgents = dom.createNode(NODE_ELEMENT, "Agents", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlGathering.appendChild xmlGatheringAgents
        xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlGatheringAgents.appendChild dom.createTextNode(vbCrLf + Space$(10))
       
        Set xmlGatheringAgent = dom.createNode(NODE_ELEMENT, "GatheringAgent", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlGatheringAgents.appendChild xmlGatheringAgent
        xmlGatheringAgents.appendChild dom.createTextNode(vbCrLf + Space$(10))
        xmlGatheringAgent.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        Set xmlGatheringAgentPerson = dom.createNode(NODE_ELEMENT, "Person", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlGatheringAgent.appendChild xmlGatheringAgentPerson
        xmlGatheringAgent.appendChild dom.createTextNode(vbCrLf + Space$(12))
        xmlGatheringAgentPerson.appendChild dom.createTextNode(vbCrLf + Space$(14))
        
        Set xmlGatheringAgentFullName = dom.createNode(NODE_ELEMENT, "FullName", "http://www.tdwg.org/schemas/abcd/2.06")
        strFullName = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Collector, lookAt:=xlWhole).Column).Value
        xmlGatheringAgentFullName.Text = strFullName
        xmlGatheringAgentPerson.appendChild xmlGatheringAgentFullName
        xmlGatheringAgentPerson.appendChild dom.createTextNode(vbCrLf + Space$(14))
        
End If

If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Exact_Site, lookAt:=xlWhole).Column).Value) _
    And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Exact_Site, lookAt:=xlWhole).Column).Value) _
    And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Exact_Site, lookAt:=xlWhole).Column).Value <> "" Then

        Set xmlGatheringLocality = dom.createNode(NODE_ELEMENT, "LocalityText", "http://www.tdwg.org/schemas/abcd/2.06")
        strGatheringLocality = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Exact_Site, lookAt:=xlWhole).Column).Value
        xmlGatheringLocality.Text = strGatheringLocality
        xmlGathering.appendChild xmlGatheringLocality
        xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
                        
End If

Set country = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(location(2), lookAt:=xlWhole).Column)

If country <> "" Then
    Set xmlGatheringCountry = dom.createNode(NODE_ELEMENT, "Country", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlGathering.appendChild xmlGatheringCountry
    xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
    xmlGatheringCountry.appendChild dom.createTextNode(vbCrLf + Space$(10))
                    
    Set xmlGatheringCountryName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlGatheringCountryName.Text = country.Value
    xmlGatheringCountry.appendChild xmlGatheringCountryName
    xmlGatheringCountry.appendChild dom.createTextNode(vbCrLf + Space$(10))
End If

For Count = 1 To 5
    Set cel = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(location(Count), lookAt:=xlWhole).Column)
    If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
        rep = rep & cel.Value & sep
    End If
Next Count

If Not IsEmpty(rep) And Not IsNull(rep) And rep <> "" Then

    Set xmlGatheringNamedAreas = dom.createNode(NODE_ELEMENT, "NamedAreas", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlGathering.appendChild xmlGatheringNamedAreas
    xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
    xmlGatheringNamedAreas.appendChild dom.createTextNode(vbCrLf + Space$(10))

    For Count = 1 To 5
        Set cel = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(location(Count), lookAt:=xlWhole).Column)
            If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
                Set xmlGatheringNamedArea = dom.createNode(NODE_ELEMENT, "NamedArea", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlGatheringNamedAreas.appendChild xmlGatheringNamedArea
                xmlGatheringNamedAreas.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                Select Case cel.Column
                Case Application.Sheets("cSPECIMEN").Rows(1).Find(Continent, lookAt:=xlWhole).Column
                    rankConcerned = 1
                Case Application.Sheets("cSPECIMEN").Rows(1).Find(country, lookAt:=xlWhole).Column
                    rankConcerned = 2
                Case Application.Sheets("cSPECIMEN").Rows(1).Find(Province, lookAt:=xlWhole).Column
                    rankConcerned = 3
                Case Application.Sheets("cSPECIMEN").Rows(1).Find(Region, lookAt:=xlWhole).Column
                    rankConcerned = 4
                Case Application.Sheets("cSPECIMEN").Rows(1).Find(Municipality, lookAt:=xlWhole).Column
                    rankConcerned = 5
                End Select
                
                strAreaClass = rank(rankConcerned)
                 
                Set xmlGatheringAreaClass = dom.createNode(NODE_ELEMENT, "AreaClass", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlGatheringAreaClass.Text = strAreaClass
                xmlGatheringNamedArea.appendChild xmlGatheringAreaClass
                xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(12))
               
                Set xmlGatheringAreaName = dom.createNode(NODE_ELEMENT, "AreaName", "http://www.tdwg.org/schemas/abcd/2.06")
                strAreaName = cel.Value
                xmlGatheringAreaName.Text = strAreaName
                xmlGatheringNamedArea.appendChild xmlGatheringAreaName
                xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(12))

                
        End If
        
    Next Count

End If

Dim Latit, Longit

If (Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Latitude, lookAt:=xlWhole).Column).Value) _
    And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Latitude, lookAt:=xlWhole).Column).Value) _
    And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Latitude, lookAt:=xlWhole).Column).Value <> "") _
    Or (Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Longitude, lookAt:=xlWhole).Column).Value) _
    And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Longitude, lookAt:=xlWhole).Column).Value) _
    And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Longitude, lookAt:=xlWhole).Column).Value <> "") Then
    
        If IsNumeric(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Latitude, lookAt:=xlWhole).Column)) = True _
        And -90 <= Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Latitude, lookAt:=xlWhole).Column) <= 90 Then
            Latit = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Latitude, lookAt:=xlWhole).Column)
        Else:
            Latit = ConvertDMSToDecimal(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Latitude, lookAt:=xlWhole).Column), True, rowCounter, False)
        End If
        
        If IsNumeric(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Longitude, lookAt:=xlWhole).Column)) = True _
        And -90 <= Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Longitude, lookAt:=xlWhole).Column) <= 90 Then
            Longit = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Longitude, lookAt:=xlWhole).Column)
        Else:
            Longit = ConvertDMSToDecimal(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Longitude, lookAt:=xlWhole).Column), False, rowCounter, False)
        End If

        If Not IsNull(Longit) And Longit <> "" Then
        If Not IsNull(Latit) And Latit <> "" Then
    
            Set xmlGatheringSiteCoordSets = dom.createNode(NODE_ELEMENT, "SiteCoordinateSets", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlGatheringSiteCoordSets
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlGatheringSiteCoordSets.appendChild dom.createTextNode(vbCrLf + Space$(10))
           
            Set xmlGatheringSiteCoord = dom.createNode(NODE_ELEMENT, "SiteCoordinates", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringSiteCoordSets.appendChild xmlGatheringSiteCoord
            xmlGatheringSiteCoordSets.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlGatheringSiteCoord.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlGatheringCoordLatLong = dom.createNode(NODE_ELEMENT, "CoordinatesLatLong", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringSiteCoord.appendChild xmlGatheringCoordLatLong
            xmlGatheringSiteCoord.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlGatheringCoordLatLong.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            If Not IsNull(Longit) And Longit <> "" Then
              
                    Set xmlGatheringCoordLongDec = dom.createNode(NODE_ELEMENT, "LongitudeDecimal", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlGatheringCoordLongDec.Text = Replace(CStr(Longit), ",", ".")
                    xmlGatheringCoordLatLong.appendChild xmlGatheringCoordLongDec
                    xmlGatheringCoordLatLong.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
            End If
           
            If Not IsNull(Latit) And Latit <> "" Then
    
                    Set xmlGatheringCoordLatDec = dom.createNode(NODE_ELEMENT, "LatitudeDecimal", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlGatheringCoordLatDec.Text = Replace(CStr(Latit), ",", ".")
                    xmlGatheringCoordLatLong.appendChild xmlGatheringCoordLatDec
                    xmlGatheringCoordLatLong.appendChild dom.createTextNode(vbCrLf + Space$(14))
                            
            End If
    
        End If
        End If
        
End If

If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Elevation, lookAt:=xlWhole).Column).Value) _
    And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Elevation, lookAt:=xlWhole).Column).Value) _
    And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Elevation, lookAt:=xlWhole).Column).Value <> "" Then

        Set xmlGatheringElevation = dom.createNode(NODE_ELEMENT, "Altitude", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlGathering.appendChild xmlGatheringElevation
        xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlGatheringElevation.appendChild dom.createTextNode(vbCrLf + Space$(10))

        Set xmlGatheringElevationMeasure = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlGatheringElevation.appendChild xmlGatheringElevationMeasure
        xmlGatheringElevation.appendChild dom.createTextNode(vbCrLf + Space$(10))
        xmlGatheringElevationMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        Set xmlGatheringElevationValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
        strElevation = Trim(Replace(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Elevation, lookAt:=xlWhole).Column).Value, "m", ""))
        xmlGatheringElevationValue.Text = strElevation
        xmlGatheringElevationMeasure.appendChild xmlGatheringElevationValue
        xmlGatheringElevationMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        Set xmlGatheringElevationUnit = dom.createNode(NODE_ELEMENT, "UnitOfMeasurement", "http://www.tdwg.org/schemas/abcd/2.06")
        If Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Unit, lookAt:=xlWhole).Column).Value <> "" Then
                xmlGatheringElevationUnit.Text = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Unit, lookAt:=xlWhole).Column).Value
        ElseIf IsNumeric(strElevation) Then
                xmlGatheringElevationUnit.Text = "m"
        Else:
                xmlGatheringElevationUnit.Text = "NA"
        End If
        xmlGatheringElevationMeasure.appendChild xmlGatheringElevationUnit
        xmlGatheringElevationMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))

End If

If (Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Depth, lookAt:=xlWhole).Column).Value) _
    And Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Depth, lookAt:=xlWhole).Column).Value) _
    And Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Depth, lookAt:=xlWhole).Column).Value <> "") Then

        Set xmlGatheringDepth = dom.createNode(NODE_ELEMENT, "Depth", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlGathering.appendChild xmlGatheringDepth
        xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlGatheringDepth.appendChild dom.createTextNode(vbCrLf + Space$(10))

        Set xmlGatheringDepthMeasure = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlGatheringDepth.appendChild xmlGatheringDepthMeasure
        xmlGatheringDepth.appendChild dom.createTextNode(vbCrLf + Space$(10))
        xmlGatheringDepthMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        Set xmlGatheringDepthValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
        strDepth = Trim(Replace(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Depth, lookAt:=xlWhole).Column).Value, "m", ""))
        xmlGatheringDepthValue.Text = strDepth
        xmlGatheringDepthMeasure.appendChild xmlGatheringDepthValue
        xmlGatheringDepthMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
        Set xmlGatheringDepthUnit = dom.createNode(NODE_ELEMENT, "UnitOfMeasurement", "http://www.tdwg.org/schemas/abcd/2.06")
        If Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Unit, lookAt:=xlWhole).Column).Value <> "" Then
                xmlGatheringDepthUnit.Text = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Unit, lookAt:=xlWhole).Column).Value
        Else:
                xmlGatheringDepthUnit.Text = "m"
        End If
        xmlGatheringDepthMeasure.appendChild xmlGatheringDepthUnit
        xmlGatheringDepthMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))

End If

Exit_XMLGather:
    
    Exit Sub
    
Err_XMLGather:
    
    MsgBox prompt:="An error occured in sub XMLGather." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLGather"
    Resume Exit_XMLGather

End Sub

Private Sub XMLNotesSpec(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Integer)

On Error GoTo Err_XMLSpecNotes

Dim xmlNotes As MSXML2.IXMLDOMElement

Dim strNotes As String
Dim Count As Integer
Dim CommentSpec(5)
    CommentSpec(1) = Comment1_Spec
    CommentSpec(2) = Comment2_Spec
    CommentSpec(3) = Comment3_Spec
    CommentSpec(4) = Comment4_Spec
    CommentSpec(5) = Comment5_Spec
Dim cel

Dim rep As String
rep = ""
  
For Count = 1 To 5
Set cel = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(CommentSpec(Count), lookAt:=xlWhole).Column)
    If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
        rep = rep & vbCrLf & cel.Value
    End If
Next Count
strNotes = Trim(rep)

If Not IsEmpty(strNotes) And Not IsNull(strNotes) And strNotes <> "" Then
    Set xmlNotes = dom.createNode(NODE_ELEMENT, "Notes", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlNotes.Text = strNotes
    subnode.appendChild xmlNotes
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    
End If

Exit_XMLSpecNotes:
    
    Exit Sub
    
Err_XMLSpecNotes:
    
    MsgBox prompt:="An error occured in sub XMLSpecNotes." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLSpecNotes"
    Resume Exit_XMLSpecNotes

End Sub

Private Sub XMLKindOfUnit(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Integer, ByRef Level As Integer)

'**********************************************************************************
'Create node with information on the kind of unit in XML file
'**********************************************************************************

On Error GoTo Err_XMLKindOfUnit

Dim xmlKindUnit As MSXML2.IXMLDOMElement
Dim strKindOfUnit_class As String
Dim strKindOfUnit_part As String
Dim strKindOfUnit As String

If Level = 1 Then

        strKindOfUnit = "Specimen"
        
ElseIf Level = 2 Then

    strKindOfUnit_part = Application.Sheets("cSAMPLE").Cells(rowConcerned, Application.Sheets("cSAMPLE").Rows(1).Find(Sample_description, lookAt:=xlWhole).Column).Value
    strKindOfUnit_class = Application.Sheets("cSAMPLE").Cells(rowConcerned, Application.Sheets("cSAMPLE").Rows(1).Find(Sample_type, lookAt:=xlWhole).Column).Value
    
    If strKindOfUnit_part <> "" And strKindOfUnit_class <> "" Then
        strKindOfUnit = "Sample: " & strKindOfUnit_part & " ; " & strKindOfUnit_class
    ElseIf strKindOfUnit_part <> "" And strKindOfUnit_class = "" Then
        strKindOfUnit = "Sample: " & strKindOfUnit_part
    ElseIf strKindOfUnit_part = "" And strKindOfUnit_class <> "" Then
        strKindOfUnit = "Sample: " & strKindOfUnit_class
    End If

ElseIf Level = 3 Then

        strKindOfUnit = "DNA-extract"

End If

Set xmlKindUnit = dom.createNode(NODE_ELEMENT, "KindOfUnit", "http://www.tdwg.org/schemas/abcd/2.06")
xmlKindUnit.Text = strKindOfUnit
subnode.appendChild xmlKindUnit
subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))

Exit_XMLKindOfUnit:
    
    Exit Sub
    
Err_XMLKindOfUnit:
    
    MsgBox prompt:="An error occured in sub XMLKindOfUnit." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLKindOfUnit"
    Resume Exit_XMLKindOfUnit
End Sub

Private Sub XMLNotesSample(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Integer)

On Error GoTo Err_XMLSampleNotes

Dim xmlNotes As MSXML2.IXMLDOMElement

Dim strNotes As String

Dim Count As Integer
Dim NotesSample(4)
    NotesSample(1) = Sample_2Dbarcode
    NotesSample(2) = Institution
    NotesSample(3) = Storage_rack
    NotesSample(4) = Storage_position
Dim cel

Dim rank(4) As String
    rank(1) = "2D barcode:"
    rank(2) = "Institution:"
    rank(3) = "Storage rack:"
    rank(4) = "Storage position:"
Dim rankConcerned As Long

Dim rep As String
rep = ""

For Count = 1 To 4
Set cel = Application.Sheets("cSAMPLE").Cells(rowConcerned, Sheets("cSAMPLE").Rows(1).Find(NotesSample(Count), lookAt:=xlWhole).Column)

    If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
            
        Select Case cel.Column
            Case Application.Sheets("cSAMPLE").Rows(1).Find(Sample_2Dbarcode, lookAt:=xlWhole).Column
                    rankConcerned = 1
            Case Application.Sheets("cSAMPLE").Rows(1).Find(Institution, lookAt:=xlWhole).Column
                    rankConcerned = 2
            Case Application.Sheets("cSAMPLE").Rows(1).Find(Storage_rack, lookAt:=xlWhole).Column
                    rankConcerned = 3
            Case Application.Sheets("cSAMPLE").Rows(1).Find(Storage_position, lookAt:=xlWhole).Column
                    rankConcerned = 4
        End Select

        rep = rep & vbCrLf & rank(rankConcerned) & cel.Value
    End If
Next Count

strNotes = Trim(rep)

If Not IsEmpty(strNotes) And Not IsNull(strNotes) And strNotes <> "" Then
    Set xmlNotes = dom.createNode(NODE_ELEMENT, "Notes", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlNotes.Text = strNotes
    subnode.appendChild xmlNotes
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
End If

Exit_XMLSampleNotes:
    
    Exit Sub
    
Err_XMLSampleNotes:
    
    MsgBox prompt:="An error occured in sub XMLSampleNotes." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLSampleNotes"
    Resume Exit_XMLSampleNotes

End Sub


Private Sub XMLDNAID(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Long)

'**********************************************************************************
'Create node with identifier for DNA extract in XML file
'**********************************************************************************

On Error GoTo Err_XMLDNAID

Dim xmlDNAUnitID As MSXML2.IXMLDOMElement
Dim xmlDNASourceID As MSXML2.IXMLDOMElement
Dim xmlDNAInstitutionID As MSXML2.IXMLDOMElement

Dim strDNAUnitID As String
Dim strDNASourceID As String
Dim strDNAInstitutionID As String

strDNAUnitID = Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(DNA_Sample_ID, lookAt:=xlWhole).Column).Value
strDNASourceID = "Not defined"
strDNAInstitutionID = Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(Institute, lookAt:=xlWhole).Column).Value

If Not IsEmpty(strDNAInstitutionID) And Not IsNull(strDNAInstitutionID) And strDNAInstitutionID <> "" Then
    Set xmlDNAInstitutionID = dom.createNode(NODE_ELEMENT, "SourceInstitutionID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlDNAInstitutionID.Text = strDNAInstitutionID
    subnode.appendChild xmlDNAInstitutionID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
End If
If Not IsEmpty(strDNASourceID) And Not IsNull(strDNASourceID) And strDNASourceID <> "" Then
    Set xmlDNASourceID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlDNASourceID.Text = strDNASourceID
    subnode.appendChild xmlDNASourceID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
End If
If Not IsEmpty(strDNAUnitID) And Not IsNull(strDNAUnitID) And strDNAUnitID <> "" Then
    Set xmlDNAUnitID = dom.createNode(NODE_ELEMENT, "UnitID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlDNAUnitID.Text = strDNAUnitID
    subnode.appendChild xmlDNAUnitID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
End If

Exit_XMLDNAID:
    
    Exit Sub
    
Err_XMLDNAID:
    
    MsgBox prompt:="An error occured in sub XMLDNAID." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLDNAID"
    Resume Exit_XMLDNAID

End Sub

Private Sub XMLDNAAssociation(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Long, ByRef associatedUnit As String, ByRef associatedUnit2 As String, ByRef associatedInstitution2 As String, ByRef associatedSource2 As String)

'**********************************************************************************
'Create node with identifier for DNA extract in XML file
'**********************************************************************************

On Error GoTo Err_XMLDNAAssociation

Dim xmlAssociations As MSXML2.IXMLDOMElement
Dim xmlUnitAssociation As MSXML2.IXMLDOMElement
Dim xmlAssociatedUnitID As MSXML2.IXMLDOMElement
Dim xmlAssociatedInstitutionID As MSXML2.IXMLDOMElement
Dim xmlAssociatedSourceID As MSXML2.IXMLDOMElement
Dim xmlAssociationType As MSXML2.IXMLDOMElement
Dim xmlAssociationComment As MSXML2.IXMLDOMElement

Set xmlAssociations = dom.createNode(NODE_ELEMENT, "Associations", "http://www.tdwg.org/schemas/abcd/2.06")
subnode.appendChild xmlAssociations
subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(8))

Set xmlUnitAssociation = dom.createNode(NODE_ELEMENT, "UnitAssociation", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociations.appendChild xmlUnitAssociation
xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(8))
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))

Set xmlAssociatedInstitutionID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceInstitutionCode", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociatedInstitutionID.Text = associatedInstitution2
xmlUnitAssociation.appendChild xmlAssociatedInstitutionID
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))
Set xmlAssociatedSourceID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceName", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociatedSourceID.Text = associatedSource2
xmlUnitAssociation.appendChild xmlAssociatedSourceID
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))
Set xmlAssociatedUnitID = dom.createNode(NODE_ELEMENT, "AssociatedUnitID", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociatedUnitID.Text = associatedUnit2
xmlUnitAssociation.appendChild xmlAssociatedUnitID
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))
Set xmlAssociationType = dom.createNode(NODE_ELEMENT, "AssociationType", "http://www.tdwg.org/schemas/abcd/2.06")
xmlAssociationType.Text = "DNA-sample"
xmlUnitAssociation.appendChild xmlAssociationType
xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))

Exit_XMLDNAAssociation:
    
    Exit Sub
    
Err_XMLDNAAssociation:
    
    MsgBox prompt:="An error occured in sub XMLDNAAssociation." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLDNAAssociation"
    Resume Exit_XMLDNAAssociation

End Sub

Private Sub XMLSequenceDNA(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Long)

'**********************************************************************************
'Create node with information on DNA extract in XML file
'**********************************************************************************

On Error GoTo Err_XMLSequenceDNA

Dim xmlSequences As MSXML2.IXMLDOMElement
Dim xmlSequencesSeq As MSXML2.IXMLDOMElement
Dim xmlSequencesDB As MSXML2.IXMLDOMElement
Dim xmlSequencesIDinDB As MSXML2.IXMLDOMElement
Dim xmlSequencingAgent As MSXML2.IXMLDOMElement
Dim xmlSequencingAgentPerson As MSXML2.IXMLDOMElement
Dim xmlSequencingAgentPersonName As MSXML2.IXMLDOMElement
Dim xmlSequencingAgentOrg As MSXML2.IXMLDOMElement
Dim xmlSequencingAgentOrgName As MSXML2.IXMLDOMElement
Dim xmlSequencingAgentOrgRepr As MSXML2.IXMLDOMElement
Dim xmlSequencingAgentOrgReprText As MSXML2.IXMLDOMElement
Dim attrSequencingAgentOrgReprText As MSXML2.IXMLDOMAttribute
Dim xmlSequenceLength As MSXML2.IXMLDOMElement

Dim strSeqAgentName As String
Dim strSeqOrganisation As String
Dim strSeqLength

strSeqAgentName = Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(Operator, lookAt:=xlWhole).Column).Value
strSeqOrganisation = Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(Institute, lookAt:=xlWhole).Column).Value
strSeqLength = Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(DNA_size, lookAt:=xlWhole).Column).Value

If Not IsEmpty(strSeqAgentName) And Not IsNull(strSeqAgentName) And strSeqAgentName <> "" Then
    If Not IsEmpty(strSeqOrganisation) And Not IsNull(strSeqOrganisation) And strSeqOrganisation <> "" Then
        If Not IsEmpty(strSeqLength) And Not IsNull(strSeqLength) And strSeqLength <> "" Then

        Set xmlSequences = dom.createNode(NODE_ELEMENT, "Sequences", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlSequences
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlSequences.appendChild dom.createTextNode(vbCrLf + Space$(8))
        Set xmlSequencesSeq = dom.createNode(NODE_ELEMENT, "Sequence", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSequences.appendChild xmlSequencesSeq
        xmlSequences.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlSequencesSeq.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
        Set xmlSequencesDB = dom.createNode(NODE_ELEMENT, "Database", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSequencesDB.Text = "Internal - JEMU"
        xmlSequencesSeq.appendChild xmlSequencesDB
        xmlSequencesSeq.appendChild dom.createTextNode(vbCrLf + Space$(10))

        Set xmlSequencesIDinDB = dom.createNode(NODE_ELEMENT, "ID-in-Database", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSequencesIDinDB.Text = "NA"
        xmlSequencesSeq.appendChild xmlSequencesIDinDB
        xmlSequencesSeq.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
        If Not IsEmpty(strSeqAgentName) And Not IsNull(strSeqAgentName) And strSeqAgentName <> "" Then
            If Not IsEmpty(strSeqOrganisation) And Not IsNull(strSeqOrganisation) And strSeqOrganisation <> "" Then

                Set xmlSequencingAgent = dom.createNode(NODE_ELEMENT, "SequencingAgent", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSequencesSeq.appendChild xmlSequencingAgent
                xmlSequencesSeq.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlSequencingAgent.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                If Not IsEmpty(strSeqOrganisation) And Not IsNull(strSeqOrganisation) And strSeqOrganisation <> "" Then
                    Set xmlSequencingAgentOrg = dom.createNode(NODE_ELEMENT, "Organisation", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSequencingAgent.appendChild xmlSequencingAgentOrg
                    xmlSequencingAgent.appendChild dom.createTextNode(vbCrLf + Space$(12))
                    xmlSequencingAgentOrg.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    Set xmlSequencingAgentOrgName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSequencingAgentOrg.appendChild xmlSequencingAgentOrgName
                    xmlSequencingAgentOrg.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    xmlSequencingAgentOrgName.appendChild dom.createTextNode(vbCrLf + Space$(16))
                    Set xmlSequencingAgentOrgRepr = dom.createNode(NODE_ELEMENT, "Representation", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSequencingAgentOrgName.appendChild xmlSequencingAgentOrgRepr
                    xmlSequencingAgentOrgName.appendChild dom.createTextNode(vbCrLf + Space$(16))
                    xmlSequencingAgentOrgRepr.appendChild dom.createTextNode(vbCrLf + Space$(18))
                    Set xmlSequencingAgentOrgReprText = dom.createNode(NODE_ELEMENT, "Text", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSequencingAgentOrgReprText.Text = strSeqOrganisation
                    xmlSequencingAgentOrgRepr.appendChild xmlSequencingAgentOrgReprText
                    xmlSequencingAgentOrgRepr.appendChild dom.createTextNode(vbCrLf + Space$(18))
                    Set attrSequencingAgentOrgReprText = dom.createNode(NODE_ATTRIBUTE, "language", "http://www.tdwg.org/schemas/abcd/2.06")
                    attrSequencingAgentOrgReprText.Value = "EN"
                    xmlSequencingAgentOrgRepr.setAttributeNode attrSequencingAgentOrgReprText

                End If

                If Not IsEmpty(strSeqAgentName) And Not IsNull(strSeqAgentName) And strSeqAgentName <> "" Then
                    Set xmlSequencingAgentPerson = dom.createNode(NODE_ELEMENT, "Person", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSequencingAgent.appendChild xmlSequencingAgentPerson
                    xmlSequencingAgent.appendChild dom.createTextNode(vbCrLf + Space$(12))
                    xmlSequencingAgentPerson.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    Set xmlSequencingAgentPersonName = dom.createNode(NODE_ELEMENT, "FullName", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSequencingAgentPersonName.Text = strSeqAgentName
                    xmlSequencingAgentPerson.appendChild xmlSequencingAgentPersonName
                    xmlSequencingAgentPerson.appendChild dom.createTextNode(vbCrLf + Space$(14))
                End If
                    
            End If
        End If
        
        If Not IsEmpty(strSeqLength) And Not IsNull(strSeqLength) And strSeqLength <> "" Then
            If IsNumeric(strSeqLength) Then
                If strSeqLength = Int(strSeqLength) Then
            
                    Set xmlSequenceLength = dom.createNode(NODE_ELEMENT, "Length", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSequenceLength.Text = strSeqLength
                    xmlSequencesSeq.appendChild xmlSequenceLength
                    xmlSequencesSeq.appendChild dom.createTextNode(vbCrLf + Space$(14))
        
                End If
            End If
        End If
        
        End If
    End If
End If

Exit_XMLSequenceDNA:

    Exit Sub
    
Err_XMLSequenceDNA:
    
    MsgBox prompt:="An error occured in sub XMLSequenceDNA." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLSequenceDNA"
    Resume Exit_XMLSequenceDNA

End Sub

Private Sub XMLExtensionDNA(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Long)

'**********************************************************************************
'Create node with information for DNA extract that concern the DNA extension in XML file
'**********************************************************************************

On Error GoTo Err_XMLExtensionDNA

Dim attr As MSXML2.IXMLDOMAttribute
Dim xmlUnitExtension As MSXML2.IXMLDOMElement
Dim xmlDNASample As MSXML2.IXMLDOMElement
Dim xmlDNAExtractionDate As MSXML2.IXMLDOMElement
Dim xmlDNAConcentration As MSXML2.IXMLDOMElement
Dim attrDNAConcentration As MSXML2.IXMLDOMAttribute
Dim xmlDNAROfAbsorbance As MSXML2.IXMLDOMElement
Dim xmlDNAExtractionMethod As MSXML2.IXMLDOMElement
Dim xmlDNATissue As MSXML2.IXMLDOMElement

Dim strExtractionDate As String
Dim strConcentration As String
Dim strRatioOfAbs As String
Dim strExtractionMethod As String
Dim strTissue As String

Dim DigT, DigV, Elu, EluT

strExtractionDate = Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(Extraction_date, lookAt:=xlWhole).Column).Value
strConcentration = Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(DNA_concentration, lookAt:=xlWhole).Column).Value
strRatioOfAbs = Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(DNA_quality, lookAt:=xlWhole).Column).Value
strTissue = Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(Tissue, lookAt:=xlWhole).Column).Value

Set xmlUnitExtension = dom.createNode(NODE_ELEMENT, "UnitExtension", "http://www.tdwg.org/schemas/abcd/2.06")
subnode.appendChild xmlUnitExtension
subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))

    Set attr = dom.createNode(NODE_ATTRIBUTE, "dna:schemaLocation", "http://www.dnabank-network.org/schemas/ABCDDNA")
    attr.Value = "http://www.dnabank-network.org/schemas/ABCDDNA http://www.dnabank-network.org/schemas/ABCDDNA/DNA.xsd"
    xmlUnitExtension.setAttributeNode attr

Set xmlDNASample = dom.createNode(NODE_ELEMENT, "dna:DNASample", "http://www.dnabank-network.org/schemas/ABCDDNA")
xmlUnitExtension.appendChild xmlDNASample
xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))

If Not IsEmpty(strTissue) And Not IsNull(strTissue) And strTissue <> "" Then
    Set xmlDNATissue = dom.createNode(NODE_ELEMENT, "dna:Tissue", "http://www.dnabank-network.org/schemas/ABCDDNA")
    xmlDNATissue.Text = strTissue
    xmlDNASample.appendChild xmlDNATissue
    xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
End If

If Not IsEmpty(strExtractionDate) And Not IsNull(strExtractionDate) And strExtractionDate <> "" Then
    Set xmlDNAExtractionDate = dom.createNode(NODE_ELEMENT, "dna:ExtractionDate", "http://www.dnabank-network.org/schemas/ABCDDNA")
        If IsDate(strExtractionDate) Then
            strExtractionDate = Format$(strExtractionDate, "yyyy-mm-dd")
            xmlDNAExtractionDate.Text = strExtractionDate
        Else:
        End If
    xmlDNASample.appendChild xmlDNAExtractionDate
    xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
End If

Dim Count As Integer
Dim Extraction(4)
    Extraction(1) = Digestion_time
    Extraction(2) = Digestion_volume
    Extraction(3) = Elution
    Extraction(4) = Elution_volume
Dim cel

Dim rep As String
rep = ""

For Count = 1 To 4
Set cel = Application.Sheets("cDNA").Cells(rowConcerned, Sheets("cDNA").Rows(1).Find(Extraction(Count), lookAt:=xlWhole).Column)
    If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
        Select Case Count
        Case 1
            rep = "Digestion Time: " & cel.Value & vbCrLf
        Case 2
            rep = rep & "Digestion volume: " & cel.Value & " µl" & vbCrLf
        Case 3
            rep = rep & "Elution: " & cel.Value & vbCrLf
        Case 4
            rep = rep & "Elution volume: " & cel.Value & " µl"
        End Select
        If Not IsEmpty(rep) And Not IsNull(rep) And rep <> "" Then
            rep = rep
        End If
    End If
Next Count

If Not IsEmpty(Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(Protocol, lookAt:=xlWhole).Column)) _
And Not IsNull(Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(Protocol, lookAt:=xlWhole).Column)) _
And (Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(Protocol, lookAt:=xlWhole).Column)) <> "" Then
    strExtractionMethod = RTrim(Application.Sheets("cDNA").Cells(rowConcerned, Application.Sheets("cDNA").Rows(1).Find(Protocol, lookAt:=xlWhole).Column) & vbCrLf & rep)
Else: strExtractionMethod = rep
End If

If Not IsEmpty(strExtractionMethod) And Not IsNull(strExtractionMethod) And strExtractionMethod <> "" Then
    Set xmlDNAExtractionMethod = dom.createNode(NODE_ELEMENT, "dna:ExtractionMethod", "http://www.dnabank-network.org/schemas/ABCDDNA")
    xmlDNAExtractionMethod.Text = strExtractionMethod
    xmlDNASample.appendChild xmlDNAExtractionMethod
    xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
End If

If Not IsEmpty(strRatioOfAbs) And Not IsNull(strRatioOfAbs) And strRatioOfAbs <> "" Then
    Set xmlDNAROfAbsorbance = dom.createNode(NODE_ELEMENT, "dna:RatioOfAbsorbance260_280", "http://www.dnabank-network.org/schemas/ABCDDNA")
    xmlDNAROfAbsorbance.Text = strRatioOfAbs
    xmlDNASample.appendChild xmlDNAROfAbsorbance
    xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
End If

If Not IsEmpty(strConcentration) And Not IsNull(strConcentration) And strConcentration <> "" Then
    Set xmlDNAConcentration = dom.createNode(NODE_ELEMENT, "dna:Concentration", "http://www.dnabank-network.org/schemas/ABCDDNA")
    xmlDNAConcentration.Text = strConcentration
    xmlDNASample.appendChild xmlDNAConcentration
    xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
    Set attrDNAConcentration = dom.createNode(NODE_ATTRIBUTE, "Unit", "http://www.dnabank-network.org/schemas/ABCDDNA")
    attrDNAConcentration.Value = "ng/µl"
    xmlDNAConcentration.setAttributeNode attrDNAConcentration

End If

Exit_XMLExtensionDNA:

    Exit Sub
    
Err_XMLExtensionDNA:
    
    MsgBox prompt:="An error occured in sub XMLExtensionDNA." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLExtensionDNA"
    Resume Exit_XMLExtensionDNA

End Sub

Private Sub XMLNotesDNA(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowConcerned As Integer)

On Error GoTo Err_XMLDNANotes

Dim xmlNotes As MSXML2.IXMLDOMElement

Dim strNotes As String

Dim Count As Integer
Dim DNANotes(6)
    DNANotes(1) = DNA_2Dbarcode
    DNANotes(2) = Institute
    DNANotes(3) = DNA_rack_ID
    DNANotes(4) = DNA_position
    DNANotes(5) = DNA_comment
    DNANotes(6) = Remarks
Dim cel

Dim rank(6) As String
    rank(1) = "2D barcode:"
    rank(2) = "Institution:"
    rank(3) = "Storage rack:"
    rank(4) = "Storage position:"
    rank(5) = "Comment on DNA:"
    rank(6) = "Comment on extraction:"
Dim rankConcerned As Long

Dim rep As String
rep = ""

For Count = 1 To 6
Set cel = Application.Sheets("cDNA").Cells(rowConcerned, Sheets("cDNA").Rows(1).Find(DNANotes(Count), lookAt:=xlWhole).Column)

    If Not IsEmpty(cel.Value) And Not IsNull(cel.Value) And cel.Value <> "" Then
            
        Select Case cel.Column
            Case Application.Sheets("cDNA").Rows(1).Find(DNA_2Dbarcode, lookAt:=xlWhole).Column
                    rankConcerned = 1
            Case Application.Sheets("cDNA").Rows(1).Find(Institute, lookAt:=xlWhole).Column
                    rankConcerned = 2
            Case Application.Sheets("cDNA").Rows(1).Find(DNA_rack_ID, lookAt:=xlWhole).Column
                    rankConcerned = 3
            Case Application.Sheets("cDNA").Rows(1).Find(DNA_position, lookAt:=xlWhole).Column
                    rankConcerned = 4
            Case Application.Sheets("cDNA").Rows(1).Find(DNA_comment, lookAt:=xlWhole).Column
                    rankConcerned = 5
            Case Application.Sheets("cDNA").Rows(1).Find(Remarks, lookAt:=xlWhole).Column
                    rankConcerned = 6

        End Select

        rep = rep & vbCrLf & rank(rankConcerned) & cel.Value
    End If
Next Count

strNotes = Trim(rep)

If Not IsEmpty(strNotes) And Not IsNull(strNotes) And strNotes <> "" Then
    Set xmlNotes = dom.createNode(NODE_ELEMENT, "Notes", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlNotes.Text = strNotes
    subnode.appendChild xmlNotes
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
End If

Exit_XMLDNANotes:
    
    Exit Sub
    
Err_XMLDNANotes:
    
    MsgBox prompt:="An error occured in sub XMLDNANotes." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub XMLDNANotes"
    Resume Exit_XMLDNANotes

End Sub


'*********************
'--- ABCDscheme TAB---
'*********************

Public Sub Define_Date()
    frmDate.Show
End Sub

Public Sub Define_LatLong()
    frmLatLong.Show
End Sub

Public Sub Check_Dupl()

On Error GoTo Err_Check_Dupl

Dim errcount, sheetcount As Integer, R As Integer

Define_Headings

'-------------------------------------------------------------------------
'Delete DUPLICATES-sheet if already created
'-------------------------------------------------------------------------
If FeuilleExiste("DUPLICATES") Then
    Application.DisplayAlerts = False
    Sheets("SPECIMEN").Activate
    Sheets("DUPLICATES").Delete
    Application.DisplayAlerts = True
End If

sheetcount = Sheets.Count
Sheets.Add After:=Sheets(Sheets.Count)
Sheets(sheetcount + 1).Name = "DUPLICATES"
Sheets("DUPLICATES").Cells(1, 1).Value = "SPECIMEN-sheet"
Sheets("DUPLICATES").Cells(1, 1).Font.FontStyle = "Bold"
Sheets("DUPLICATES").Cells(2, 1).Value = "SPECIMEN ID"
Sheets("DUPLICATES").Cells(2, 1).Font.FontStyle = "Italic"
Sheets("DUPLICATES").Cells(2, 2).Value = "Address"
Sheets("DUPLICATES").Cells(2, 2).Font.FontStyle = "Italic"
        
Sheets("DUPLICATES").Cells(1, 3).Value = "SAMPLE-sheet"
Sheets("DUPLICATES").Cells(1, 3).Font.FontStyle = "Bold"
Sheets("DUPLICATES").Cells(2, 3).Value = "SAMPLE ID"
Sheets("DUPLICATES").Cells(2, 3).Font.FontStyle = "Italic"
Sheets("DUPLICATES").Cells(2, 4).Value = "Address"
Sheets("DUPLICATES").Cells(2, 4).Font.FontStyle = "Italic"
        
Sheets("DUPLICATES").Cells(1, 5).Value = "DNA-sheet"
Sheets("DUPLICATES").Cells(1, 5).Font.FontStyle = "Bold"
Sheets("DUPLICATES").Cells(2, 5).Value = "DNA ID"
Sheets("DUPLICATES").Cells(2, 5).Font.FontStyle = "Italic"
Sheets("DUPLICATES").Cells(2, 6).Value = "Address"
Sheets("DUPLICATES").Cells(2, 6).Font.FontStyle = "Italic"
        
With Worksheets("DUPLICATES")
    Columns("A").ColumnWidth = 25
    Columns("B").ColumnWidth = 16
    Columns("C").ColumnWidth = 25
    Columns("D").ColumnWidth = 16
    Columns("E").ColumnWidth = 25
    Columns("F").ColumnWidth = 16
End With

'-------------------------------------------------------------------------
'Test the presence of duplicated ids in each sheet
'-------------------------------------------------------------------------
Check_DuplSpec
Check_DuplSample
Check_DuplDNA
If Sheets("DUPLICATES").Cells(3, 1) <> "" Or Sheets("DUPLICATES").Cells(3, 3) <> "" Or Sheets("DUPLICATES").Cells(3, 5) <> "" Then
    errcount = 1
Else:
    errcount = 0
End If

'-------------------------------------------------------------------------
'Display an error message
'-------------------------------------------------------------------------
If errcount > 0 Then
    'Sort on id values, to display duplicates next to each other
    Sheets("DUPLICATES").Range("A2:B100000").Select
    ActiveWorkbook.Worksheets("DUPLICATES").Sort.SortFields.Clear
    ActiveWorkbook.Worksheets("DUPLICATES").Sort.SortFields.Add Key:=Range("A2:A100000"), SortOn:=xlSortOnValues, Order:=xlAscending, DataOption:=xlSortNormal
    With ActiveWorkbook.Worksheets("DUPLICATES").Sort
        .SetRange Range("A2:B100000")
        .Header = xlYes
        .MatchCase = False
        .Orientation = xlTopToBottom
        .SortMethod = xlPinYin
        .Apply
    End With
    Sheets("DUPLICATES").Range("C2:D100000").Select
    ActiveWorkbook.Worksheets("DUPLICATES").Sort.SortFields.Clear
    ActiveWorkbook.Worksheets("DUPLICATES").Sort.SortFields.Add Key:=Range("C2:C100000"), SortOn:=xlSortOnValues, Order:=xlAscending, DataOption:=xlSortNormal
    With ActiveWorkbook.Worksheets("DUPLICATES").Sort
        .SetRange Range("C2:D100000")
        .Header = xlYes
        .MatchCase = False
        .Orientation = xlTopToBottom
        .SortMethod = xlPinYin
        .Apply
    End With
    Sheets("DUPLICATES").Range("E2:F100000").Select
    ActiveWorkbook.Worksheets("DUPLICATES").Sort.SortFields.Clear
    ActiveWorkbook.Worksheets("DUPLICATES").Sort.SortFields.Add Key:=Range("E2:E100000"), SortOn:=xlSortOnValues, Order:=xlAscending, DataOption:=xlSortNormal
    With ActiveWorkbook.Worksheets("DUPLICATES").Sort
        .SetRange Range("E2:F100000")
        .Header = xlYes
        .MatchCase = False
        .Orientation = xlTopToBottom
        .SortMethod = xlPinYin
        .Apply
    End With
    Sheets("DUPLICATES").Range("A1:A1").Select
    MsgBox "Some specimen, sample or DNA-extract ID's have duplicates. Please check these records (in red or in the DUPLICATES-sheet)."

ElseIf errcount = 0 Then
    Application.DisplayAlerts = False
    Sheets("DUPLICATES").Delete
    Application.DisplayAlerts = True
    MsgBox "No duplicates were found."
End If
    
Exit_Check_Dupl:
    
    Exit Sub

Err_Check_Dupl:
    
    MsgBox prompt:="An error occured in sub Check_Dupl." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Duplicates"
    Resume Exit_Check_Dupl

End Sub


Function FeuilleExiste(strFeuille) As Boolean
On Error Resume Next
FeuilleExiste = Not (Application.Sheets(strFeuille) Is Nothing)
End Function

Public Sub Check_DuplSpec()

Dim R As Integer, rspec As Integer, TargetSpec As Range, errcountspec As Integer
Dim EvalRangeSpec As Range
Dim LastRSpec As Integer, ColSpecID As Integer
LastRSpec = Application.Sheets("SPECIMEN").Cells(Rows.Count, Application.Sheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole).Column).End(xlUp).Row
ColSpecID = Application.Sheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole).Column
Dim AddressSpec

'-------------------------------------------------------------------------
'Empty fill of each cells
'-------------------------------------------------------------------------
For R = LastRSpec To 4 Step -1
    Application.Sheets("SPECIMEN").Rows(R).Cells.Interior.ColorIndex = xlNone
Next R

'-------------------------------------------------------------------------
'See if duplicates and copy them in DUPLICATES-sheet
'-------------------------------------------------------------------------
rspec = 3
For R = 4 To LastRSpec
    Set EvalRangeSpec = Application.Sheets("SPECIMEN").Columns(ColSpecID)
    Set TargetSpec = Application.Sheets("SPECIMEN").Cells(R, ColSpecID)
    If WorksheetFunction.CountIf(EvalRangeSpec, TargetSpec.Value) > 1 Then
        TargetSpec.Interior.Color = RGB(248, 66, 83)
        errcountspec = errcountspec + 1
        Sheets("DUPLICATES").Cells(rspec, 1).Value = TargetSpec.Value
        AddressSpec = "SPECIMEN!" & TargetSpec.Address
        Sheets("DUPLICATES").Cells(rspec, 2).Select
        ActiveSheet.Hyperlinks.Add Anchor:=Selection, Address:="", SubAddress:=AddressSpec, TextToDisplay:=AddressSpec
        rspec = rspec + 1
    End If
Next R

End Sub

Public Sub Check_DuplSample()

Dim R As Integer, rsample As Integer, TargetSample As Range, errcountsample As Integer
Dim EvalRangeSample As Range
Dim LastRSample As Integer, ColSampleID As Integer
LastRSample = Application.Sheets("SAMPLE").Cells(Rows.Count, Application.Sheets("SAMPLE").Rows("1:3").Find(Sample_ID, lookAt:=xlWhole).Column).End(xlUp).Row
ColSampleID = Application.Sheets("SAMPLE").Rows("1:3").Find(Sample_ID, lookAt:=xlWhole).Column
Dim AddressSample

'-------------------------------------------------------------------------
'Empty fill of each cells
'-------------------------------------------------------------------------
For R = LastRSample To 4 Step -1
    Application.Sheets("SAMPLE").Rows(R).Cells.Interior.ColorIndex = xlNone
Next R

'-------------------------------------------------------------------------
'See if duplicates and copy them in DUPLICATES-sheet
'-------------------------------------------------------------------------
rsample = 3
For R = 2 To LastRSample
    Set EvalRangeSample = Application.Sheets("SAMPLE").Columns(ColSampleID)
    Set TargetSample = Application.Sheets("SAMPLE").Cells(R, ColSampleID)
    If WorksheetFunction.CountIf(EvalRangeSample, TargetSample.Value) > 1 Then
        TargetSample.Interior.Color = RGB(248, 66, 83)
        errcountsample = errcountsample + 1
        Sheets("DUPLICATES").Cells(rsample, 3).Value = TargetSample.Value
        AddressSample = "SAMPLE!" & TargetSample.Address
        Sheets("DUPLICATES").Cells(rsample, 4).Select
        ActiveSheet.Hyperlinks.Add Anchor:=Selection, Address:="", SubAddress:=AddressSample, TextToDisplay:=AddressSample
        rsample = rsample + 1
    End If
Next R

End Sub

Public Sub Check_DuplDNA()

Dim R As Integer, TargetDNA As Range, rdna As Integer, errcountdna As Integer
Dim EvalRangeDNA As Range
Dim LastRDNA As Integer, ColDNAID As Integer
LastRDNA = Application.Sheets("DNA").Cells(Rows.Count, Application.Sheets("DNA").Rows("1:3").Find(DNA_Sample_ID, lookAt:=xlWhole).Column).End(xlUp).Row
ColDNAID = Application.Sheets("DNA").Rows("1:3").Find(DNA_Sample_ID, lookAt:=xlWhole).Column
Dim AddressDNA

'-------------------------------------------------------------------------
'Empty fill of each cells
'-------------------------------------------------------------------------
'DNA sheet
For R = LastRDNA To 4 Step -1
    Application.Sheets("DNA").Rows(R).Cells.Interior.ColorIndex = xlNone
Next R

'-------------------------------------------------------------------------
'See if duplicates and copy them in DUPLICATES-sheet
'-------------------------------------------------------------------------
rdna = 3
For R = 2 To LastRDNA
    Set EvalRangeDNA = Application.Sheets("DNA").Columns(ColDNAID)
    Set TargetDNA = Application.Sheets("DNA").Cells(R, ColDNAID)
    If WorksheetFunction.CountIf(EvalRangeDNA, TargetDNA.Value) > 1 Then
        TargetDNA.Interior.Color = RGB(248, 66, 83)
        errcountdna = errcountdna + 1
        Sheets("DUPLICATES").Cells(rdna, 5).Value = TargetDNA.Value
        AddressDNA = "DNA!" & TargetDNA.Address
        Sheets("DUPLICATES").Cells(rdna, 6).Select
        ActiveSheet.Hyperlinks.Add Anchor:=Selection, Address:="", SubAddress:=AddressDNA, TextToDisplay:=AddressDNA
        rdna = rdna + 1
    End If
Next R

End Sub


Public Sub Check_Values()

Define_Headings
CheckNumericAndDate

Dim sheetcount As Integer, R As Integer
Dim LastRSpec As Integer, LastCSpec As Integer, LastRDNA As Integer, LastCDNA As Integer, iter As Integer, col As Integer
LastRSpec = Application.Sheets("SPECIMEN").Cells(Rows.Count, Application.Sheets("SPECIMEN").Rows("1:3").Find(Museum_voucher_ID, lookAt:=xlWhole).Column).End(xlUp).Row
LastCSpec = Application.Sheets("SPECIMEN").Cells(2, Columns.Count).End(xlToLeft).Column
LastRDNA = Application.Sheets("DNA").Cells(Rows.Count, Application.Sheets("DNA").Rows("1:3").Find(DNA_Sample_ID, lookAt:=xlWhole).Column).End(xlUp).Row
LastCDNA = Application.Sheets("DNA").Cells(2, Columns.Count).End(xlToLeft).Column
Dim AddressSpec, AddressDNA

'-------------------------------------------------------------------------
'Delete CHECK_VALUES-sheet if already created
'-------------------------------------------------------------------------
If FeuilleExiste("CHECK_VALUES") Then
    Sheets("CHECK_VALUES").Activate
    Cells.Select
    Selection.ClearContents
Else:
    sheetcount = Sheets.Count
    Sheets.Add After:=Sheets(Sheets.Count)
    Sheets(sheetcount + 1).Name = "CHECK_VALUES"
End If

Sheets("CHECK_VALUES").Cells(1, 1).Value = "SPECIMEN-sheet"
Sheets("CHECK_VALUES").Cells(1, 1).Font.FontStyle = "Bold"
Sheets("CHECK_VALUES").Cells(2, 1).Value = "Field"
Sheets("CHECK_VALUES").Cells(2, 1).Font.FontStyle = "Italic"
Sheets("CHECK_VALUES").Cells(2, 2).Value = "Value"
Sheets("CHECK_VALUES").Cells(2, 2).Font.FontStyle = "Italic"
Sheets("CHECK_VALUES").Cells(2, 3).Value = "Address"
Sheets("CHECK_VALUES").Cells(2, 3).Font.FontStyle = "Italic"
Sheets("CHECK_VALUES").Cells(2, 4).Value = "Incorrect/Unexpected value"
Sheets("CHECK_VALUES").Cells(2, 4).Font.FontStyle = "Italic"
Sheets("CHECK_VALUES").Cells(1, 6).Value = "DNA-sheet"
Sheets("CHECK_VALUES").Cells(1, 6).Font.FontStyle = "Bold"
Sheets("CHECK_VALUES").Cells(2, 6).Value = "Field"
Sheets("CHECK_VALUES").Cells(2, 6).Font.FontStyle = "Italic"
Sheets("CHECK_VALUES").Cells(2, 7).Value = "Value"
Sheets("CHECK_VALUES").Cells(2, 7).Font.FontStyle = "Italic"
Sheets("CHECK_VALUES").Cells(2, 8).Value = "Address"
Sheets("CHECK_VALUES").Cells(2, 8).Font.FontStyle = "Italic"
Sheets("CHECK_VALUES").Cells(2, 9).Value = "Incorrect/Unexpected value"
Sheets("CHECK_VALUES").Cells(2, 9).Font.FontStyle = "Italic"
    
With Worksheets("CHECK_VALUES")
    Columns("A").ColumnWidth = 16
    Columns("B").ColumnWidth = 16
    Columns("C").ColumnWidth = 25
    Columns("D").ColumnWidth = 25
    Columns("F").ColumnWidth = 16
    Columns("G").ColumnWidth = 16
    Columns("H").ColumnWidth = 25
    Columns("I").ColumnWidth = 25
End With


iter = 3
For R = 4 To LastRSpec
    For col = 2 To LastCSpec
        If Sheets("SPECIMEN").Cells(R, col).Interior.Color = RGB(248, 66, 83) Then
            Sheets("CHECK_VALUES").Cells(iter, 1).Value = Sheets("SPECIMEN").Cells(2, col).Value
            Sheets("CHECK_VALUES").Cells(iter, 2).Value = Sheets("SPECIMEN").Cells(R, col).Value
            AddressSpec = "SPECIMEN!" & Sheets("SPECIMEN").Cells(R, col).Address
            Sheets("CHECK_VALUES").Cells(iter, 3).Select
            ActiveSheet.Hyperlinks.Add Anchor:=Selection, Address:="", SubAddress:=AddressSpec, TextToDisplay:=AddressSpec
            Sheets("CHECK_VALUES").Cells(iter, 4).Value = "Incorrect"
            iter = iter + 1
        ElseIf Sheets("SPECIMEN").Cells(R, col).Interior.Color = RGB(254, 222, 104) Then
            Sheets("CHECK_VALUES").Cells(iter, 1).Value = Sheets("SPECIMEN").Cells(2, col).Value
            Sheets("CHECK_VALUES").Cells(iter, 2).Value = Sheets("SPECIMEN").Cells(R, col).Value
            Sheets("CHECK_VALUES").Cells(iter, 3).Select
            AddressSpec = "SPECIMEN!" & Sheets("SPECIMEN").Cells(R, col).Address
            ActiveSheet.Hyperlinks.Add Anchor:=Selection, Address:="", SubAddress:=AddressSpec, TextToDisplay:=AddressSpec
            Sheets("CHECK_VALUES").Cells(iter, 4).Value = "Unexpected"
            iter = iter + 1
        End If
    Next col
Next R

iter = 3
For R = 4 To LastRDNA
    For col = 2 To LastCDNA
        If Sheets("DNA").Cells(R, col).Interior.Color = RGB(248, 66, 83) Then
            Sheets("CHECK_VALUES").Cells(iter, 6).Value = Sheets("DNA").Cells(2, col).Value
            Sheets("CHECK_VALUES").Cells(iter, 7).Value = Sheets("DNA").Cells(R, col).Value
            Sheets("CHECK_VALUES").Cells(iter, 8).Select
            AddressDNA = "DNA!" & Sheets("DNA").Cells(R, col).Address
            ActiveSheet.Hyperlinks.Add Anchor:=Selection, Address:="", SubAddress:=AddressDNA, TextToDisplay:=AddressDNA
            Sheets("CHECK_VALUES").Cells(iter, 9).Value = "Incorrect"
            iter = iter + 1
        ElseIf Sheets("DNA").Cells(R, col).Interior.Color = RGB(254, 222, 104) Then
            Sheets("CHECK_VALUES").Cells(iter, 6).Value = Sheets("DNA").Cells(2, col).Value
            Sheets("CHECK_VALUES").Cells(iter, 7).Value = Sheets("DNA").Cells(R, col).Value
            Sheets("CHECK_VALUES").Cells(iter, 8).Select
            AddressDNA = "DNA!" & Sheets("DNA").Cells(R, col).Address
            ActiveSheet.Hyperlinks.Add Anchor:=Selection, Address:="", SubAddress:=AddressDNA, TextToDisplay:=AddressDNA
            Sheets("CHECK_VALUES").Cells(iter, 9).Value = "Unexpected"
            iter = iter + 1
        End If
    Next col
Next R

If Application.CountA(Application.Sheets("CHECK_VALUES").Rows(3).EntireRow) = 0 Then
    MsgBox "All values are correct."
    Application.DisplayAlerts = False
    Application.Sheets("CHECK_VALUES").Delete
    Application.DisplayAlerts = True
End If

End Sub

Public Sub Add_Column()
    frmAddColumn.Show
End Sub


Sub AllowPasteValuesOnly()
    Selection.PasteSpecial Paste:=xlPasteFormulas, Operation:=xlNone, _
              SkipBlanks:=False, Transpose:=False
End Sub





