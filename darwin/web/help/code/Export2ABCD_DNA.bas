Attribute VB_Name = "Export2ABCD"
Option Explicit

Dim owner, specimenID, additionalID, code, accessionNumber, datasetName, isPhysical, acquisitionType, acquiredFrom, acquisitionDay, acquisitionMonth, acquisitionYear
Dim ocean, continent, sea, country, state_territory, province, region, archipelago, district, county, department, island, city, municipality
Dim populatedPlace, naturalSite, exactSite, samplingCode, elevationInMeters, depthInMeters, latitude, longitude, samplingMethod, fixation, ecology
Dim siteProperty_1, sitePropertyValue_1, siteProperty_2, sitePropertyValue_2, siteProperty_3, sitePropertyValue_3, siteProperty_4, sitePropertyValue_4, siteProperty_5, sitePropertyValue_5
Dim siteProperty_6, sitePropertyValue_6, siteProperty_7, sitePropertyValue_7, siteProperty_8, sitePropertyValue_8, siteProperty_9, sitePropertyValue_9, siteProperty_10, sitePropertyValue_10
Dim expedition_project, collectedBy, collectionStartDay, collectionStartMonth, collectionStartYear, collectionStartTimeH, collectionStartTimeM, collectionEndDay, collectionEndMonth, collectionEndYear, collectionEndTimeH, collectionEndTimeM, localityNotes
Dim classification, phylum, classis, ordo, superfamilia, familia, subfamilia, genus, subgenus, species, subspecies, author_year, variety_form, informalName
Dim identificationMethod, identificationHistory, taxonFullName, identifiedBy, identificationDay, identificationMonth, identificationYear, referenceString, publicationString, identificationNotes
Dim hostClassis, hostOrdo, hostFamilia, hostGenus, hostSpecies, hostAuthor_year, hostRemark
Dim kindOfUnit, statusType, totalNumber, sex, maleCount, femaleCount, sexUnknownCount, lifeStage, socialStatus, urlPicture, externalLink
Dim specimenProperty_1, specimenPropertyValue_1, specimenProperty_2, specimenPropertyValue_2, specimenProperty_3, specimenPropertyValue_3, specimenProperty_4, specimenPropertyValue_4, specimenProperty_5, specimenPropertyValue_5
Dim specimenProperty_6, specimenPropertyValue_6, specimenProperty_7, specimenPropertyValue_7, specimenProperty_8, specimenPropertyValue_8, specimenProperty_9, specimenPropertyValue_9, specimenProperty_10, specimenPropertyValue_10
Dim specimenProperty_11, specimenPropertyValue_11, specimenProperty_12, specimenPropertyValue_12, specimenProperty_13, specimenPropertyValue_13, specimenProperty_14, specimenPropertyValue_14, specimenProperty_15, specimenPropertyValue_15
Dim specimenProperty_16, specimenPropertyValue_16, specimenProperty_17, specimenPropertyValue_17, specimenProperty_18, specimenPropertyValue_18, specimenProperty_19, specimenPropertyValue_19, specimenProperty_20, specimenPropertyValue_20
Dim associatedUnitInstitution, associatedUnitCollection, associatedUnitID, associationType
Dim institutionStorage, buildingStorage, floorStorage, roomStorage, laneStorage, columnStorage, shelfStorage, barcode, conservation
Dim container, containerType, containerStorage, subcontainer, subcontainerType, subcontainerStorage
Dim notes
'Dim boxStorage, tubeStorage,

Dim SpecHeadings() As Variant
Dim SpecHeadings_compared() As Variant

Dim sampleDatasetName, sampleID, associatedSpecimenInstitution, associatedSpecimenDataset, associatedspecimenID
Dim sampleAcquiredFrom
Dim partOfOrganism, sampleTissueType, samplePreparationType, samplePreservation
Dim sampleInstitutionStorage, sampleBuildingStorage, sampleFloorStorage, sampleRoomStorage, sampleColumnStorage, sampleBoxStorage, sampleTubeStorage, sample2Dbarcode
Dim sampleNotes

Dim SampleHeadings() As Variant
Dim SampleHeadings_compared() As Variant

Dim dnaDatasetName, dnaID, dnaAdditionalID, associatedSampleInstitution, associatedSampleDataset, associatedSampleID
Dim dnaConcentration, dnaAbsorbance260280, dnaSize
Dim extractionTissue, extractionMethod, digestionTime, digestionVolume, elutionBuffer, elutionVolume
Dim extractedBy, extractionDay, extractionMonth, extractionYear
Dim genBank, dnaInstitutionStorage, dnaBuildingStorage, dnaFloorStorage, dnaRoomStorage, dnaFridgeOrDrawerStorage, dnaBoxStorage, dnaPositionStorage, dna2Dbarcode, dnaPreservation
Dim dnaNotes

Dim DNAHeadings() As Variant
Dim DNAHeadings_compared() As Variant

Dim strate As Integer

'************************************************************************************************************************************
'| Purpose: Create XML file containing all data of the excel file (following ABCD schema), including specimen if owner is checked.
'************************************************************************************************************************************
Public Sub CreateXML()

On Error GoTo Err_CreateXML

ProcessCarriageReturns

'Call function for mapping columns heading row
DefineHeadings

If CheckHeaders(check:=False) Then

    Dim rowCounter As Long
    
    'Desactivate Application alerts
    With Application
        .DisplayAlerts = False
        .ScreenUpdating = False
        .Cursor = xlWait
    End With
    
    'Let user posted about the running event...
    Application.StatusBar = "Processing... Please do not disturb..."
    DoEvents
    
    'Make copy of sheets for rework and initiate the XML file
    If CopySheetsForRework Then
    
        Dim dom As MSXML2.DOMDocument60
        Dim root As MSXML2.IXMLDOMElement
        Dim firstnode As MSXML2.IXMLDOMNode
        Dim node As MSXML2.IXMLDOMNode
        Dim attr As MSXML2.IXMLDOMAttribute
        Dim subnode As MSXML2.IXMLDOMElement
        Dim strPath
        'Dim rowNb As Integer, rowNbSample As Integer, rowNbDNA As Integer, specrecords As Integer
        Dim rowNb As Long, rowNbSample As Long, rowNbDNA As Long, specrecords As Long
        specrecords = 0
        rowNbSample = 0
        rowNbDNA = 0

        Set dom = New MSXML2.DOMDocument60
        dom.async = False
        dom.validateOnParse = False
        dom.resolveExternals = False
        dom.preserveWhiteSpace = True
    
        ' Create a processing instruction targeted for xml.
        Set node = dom.createProcessingInstruction("xml", "version=""1.0"" encoding=""UTF-8""")
        dom.appendChild node
        Set node = Nothing
    
        ' Create a comment for the document.
        Set node = dom.createComment("Schema ABCDDNA - Template général d'encodage pour les données moléculaires, version 01/2014")
        dom.appendChild node
        Set node = Nothing
    
        ' Create the root element.
        Set root = dom.createNode(NODE_ELEMENT, "DataSets", "http://www.tdwg.org/schemas/abcd/2.06")
        ' Add the root element to the DOM instance.
        dom.appendChild root
        Set attr = dom.createNode(NODE_ATTRIBUTE, "xmlns:storage", "")
        attr.Value = "http://darwin.naturalsciences.be/xsd/"
        root.setAttributeNode attr
        Set attr = dom.createNode(NODE_ATTRIBUTE, "xs:schemaLocation", "http://www.w3.org/2001/XMLSchema-instance")
        attr.Value = "http://www.tdwg.org/schemas/abcd/2.06 http://darwin.naturalsciences.be/xsd/ABCD_2.06_EFGDNA.XSD http://darwin.naturalsciences.be/xsd/ http://darwin.naturalsciences.be/xsd/storage.xsd"
        root.setAttributeNode attr
    
        'Create a DataSet container
        root.appendChild dom.createTextNode(vbCrLf)
        Set firstnode = dom.createNode(NODE_ELEMENT, "DataSet", "http://www.tdwg.org/schemas/abcd/2.06")
        root.appendChild firstnode
        root.appendChild dom.createTextNode(vbCrLf)
        firstnode.appendChild dom.createTextNode(vbCrLf & Space$(2))
    
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
    
        'Create ABCD container and insert data in xml file
        Dim FoundCellSample As Range, FoundCellDNA As Range, specimenIDOfRow As Range, SampleIdOfRow As Range
        Dim LastR As Long
        Dim FindOwner As Range, ownership As String
        
        LastR = Application.Sheets("cSPECIMEN").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
        
        For rowCounter = 2 To LastR

            Set FindOwner = Application.Sheets("cSPECIMEN").Rows(1).Find(what:="owner", lookAt:=xlWhole)
            If Not FindOwner Is Nothing Then
                ownership = Application.Sheets("cSPECIMEN").Cells(rowCounter, FindOwner.Column).Value
                
                If ownership <> "" Then
                
                    specrecords = specrecords + 1
                    
                    Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
                    node.appendChild subnode
                    node.appendChild dom.createTextNode(vbCrLf + Space$(4))
                    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
                    XMLSpecID dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLUnitRef dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLIdentification dom:=dom, subnode:=subnode, rowCounter:=rowCounter, strate:=1
                    XMLRecordBasis dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLKindOfUnit dom:=dom, subnode:=subnode, rowCounter:=rowCounter, strate:=1
                    XMLSpecimenUnit dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLStage dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLPicture dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLAssociation dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLGather dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLMeasurements dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLSex dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLNotes dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLRecordURI dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLExtensionStorage dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            
                End If
            End If
            
            If FeuilleExiste("SAMPLE") Then
            
                '---------------------
                'Level Sample
                '---------------------
                Set specimenIDOfRow = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(specimenID, lookAt:=xlWhole).Column)
        
                Do While Not Application.Sheets("cSAMPLE").Cells.Find(specimenIDOfRow.Value, lookAt:=xlWhole) Is Nothing

                    Set FoundCellSample = Application.Sheets("cSAMPLE").Cells(Application.Sheets("cSAMPLE").Cells.Find(specimenIDOfRow.Value, lookAt:=xlWhole).Row, Application.Sheets("cSAMPLE").Rows(1).Find(sampleID, lookAt:=xlWhole).Column)
                    
                    Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
                    node.appendChild subnode
                    node.appendChild dom.createTextNode(vbCrLf + Space$(4))
                    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
                    
                    XMLSampleID dom:=dom, subnode:=subnode, rowCounter:=FoundCellSample.Row
                    XMLIdentification dom:=dom, subnode:=subnode, rowCounter:=rowCounter, strate:=2
                    XMLKindOfUnit dom:=dom, subnode:=subnode, rowCounter:=FoundCellSample.Row, strate:=2
                    XMLSampleUnit dom:=dom, subnode:=subnode, rowCounter:=FoundCellSample.Row, rowCounterSpec:=rowCounter, sample:=True
                    XMLStage dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLSampleAssociation dom:=dom, subnode:=subnode, rowCounter:=FoundCellSample.Row
                    XMLGather dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLSex dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLSampleNotes dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLExtensionSampleStorage dom:=dom, subnode:=subnode, rowCounter:=FoundCellSample.Row
                    
                    rowNbSample = rowNbSample + 1
                    
                    If FeuilleExiste("DNA") Then
        
                        Set SampleIdOfRow = Application.Sheets("cSAMPLE").Cells(FoundCellSample.Row, Application.Sheets("cSAMPLE").Rows(1).Find(sampleID, lookAt:=xlWhole).Column)

                        '---------------------
                        'Level DNA
                        '---------------------
                        Do While Not Application.Sheets("cDNA").Cells.Find(SampleIdOfRow.Value, lookAt:=xlWhole) Is Nothing
                            
                            Set FoundCellDNA = Application.Sheets("cDNA").Cells(Application.Sheets("cDNA").Cells.Find(SampleIdOfRow.Value, lookAt:=xlWhole).Row, Application.Sheets("cDNA").Rows(1).Find(dnaID, lookAt:=xlWhole).Column)
                                
                            Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
                            node.appendChild subnode
                            node.appendChild dom.createTextNode(vbCrLf + Space$(4))
                            subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
                            
                            XMLDNAID dom:=dom, subnode:=subnode, rowCounter:=FoundCellDNA.Row
                            XMLIdentification dom:=dom, subnode:=subnode, rowCounter:=rowCounter, strate:=2
                            XMLKindOfUnit dom:=dom, subnode:=subnode, rowCounter:=FoundCellDNA.Row, strate:=3
                            XMLSampleUnit dom:=dom, subnode:=subnode, rowCounter:=FoundCellSample.Row, rowCounterSpec:=rowCounter, sample:=False
                            XMLStage dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                            XMLDNAAssociation dom:=dom, subnode:=subnode, rowCounter:=FoundCellDNA.Row
                            XMLGather dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                            XMLDNAMeasurements dom:=dom, subnode:=subnode, rowCounter:=FoundCellDNA.Row
                            XMLSex dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                            XMLDNANotes dom:=dom, subnode:=subnode, rowCounter:=FoundCellDNA.Row
                            XMLExtensionDNA dom:=dom, subnode:=subnode, rowCounter:=FoundCellDNA.Row
            
                            FoundCellDNA.EntireRow.Delete (xlShiftUp)
                                                 
                            rowNbDNA = rowNbDNA + 1
                     
                        Loop
                            
                        FoundCellSample.EntireRow.Delete (xlShiftUp)
                    
                    End If
                    
                Loop
            
            End If
        
        rowNb = rowCounter - 1

        Next rowCounter
    
        If FeuilleExiste("SAMPLE") Then
        
            Dim LastRSample As Long
    
            LastRSample = Application.Sheets("cSAMPLE").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
            
            If LastRSample > 1 Then
            
                For rowCounter = 2 To LastRSample
            
                    Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
                    node.appendChild subnode
                    node.appendChild dom.createTextNode(vbCrLf + Space$(4))
                    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
                    XMLSampleID dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLKindOfUnit dom:=dom, subnode:=subnode, rowCounter:=rowCounter, strate:=2
                    XMLSampleUnit dom:=dom, subnode:=subnode, rowCounter:=rowCounter, rowCounterSpec:=0, sample:=True
                    XMLSampleAssociation dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLSampleNotes dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLExtensionSampleStorage dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    
                    rowNbSample = rowNbSample + 1
                    
                Next rowCounter
            
            End If
        
        End If
        
        If FeuilleExiste("DNA") Then
        
            Dim LastRDNA As Long
    
            LastRDNA = Application.Sheets("cDNA").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
        
            If LastRDNA > 1 Then
            
                For rowCounter = 2 To LastRDNA
        
                    Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
                    node.appendChild subnode
                    node.appendChild dom.createTextNode(vbCrLf + Space$(4))
                    subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
                    XMLDNAID dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLKindOfUnit dom:=dom, subnode:=subnode, rowCounter:=rowCounter, strate:=3
                    XMLDNAAssociation dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLDNAMeasurements dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLDNANotes dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    XMLExtensionDNA dom:=dom, subnode:=subnode, rowCounter:=rowCounter
                    
                    rowNbDNA = rowNbDNA + 1
        
                Next rowCounter
            
            End If
        
        End If

    Else:
        GoTo Exit_CreateXML
    End If

Else:
    GoTo Exit_CreateXML
End If

'Call function for deletion of sheets after encoding in XML file and saving
DeleteSheetsAfterRework

'Reactivate Application alerts
With Application
    .DisplayAlerts = True
    .ScreenUpdating = True
    .Cursor = xlDefault
End With

' Save the XML document to a file
If rowNb > 0 Or rowNbSample > 0 Or rowNbDNA > 0 Then
DefineFileName:         strPath = Application.GetSaveAsFilename(InitialFileName:="export.xml", FileFilter:="XML Files (*.xml), *.xml", Title:="Select where to save your file")
    If strPath <> False Then
        ' Save the file at the location provided with the name provided
        dom.Save strPath
        If FeuilleExiste("SAMPLE") And FeuilleExiste("DNA") Then
            MsgBox "Your output file was successfully created based on the SPECIMEN-sheet, the SAMPLE-sheet and the DNA-sheet, with :" _
            & vbCrLf & rowNb & " specimens, among which " & specrecords & " will be imported in DaRWIN" _
            & vbCrLf & rowNbSample & " samples" _
            & vbCrLf & rowNbDNA & " DNA extracts."
        Else:
            MsgBox "Your output file was successfully created based on the SPECIMEN-sheet, with " & rowNb & " records."
        End If
        Application.StatusBar = "Done"
        DoEvents
    End If
Else:
    GoTo Exit_CreateXML
End If

Exit_CreateXML:
    DeleteSheetsAfterRework
    With Application
        .DisplayAlerts = True
        .ScreenUpdating = True
        .Cursor = xlDefault
    End With

    Application.StatusBar = False
    DoEvents
    
    Application.Sheets("SPECIMEN").Activate
    
    Exit Sub

Err_CreateXML:
    
    If FindOwner Is Nothing Then
        Resume Next
    ElseIf Err.Number = -2147024891 Then
        MsgBox prompt:="You don't have the rights to save the file " & strPath & " on the location selected." & vbCrLf & _
                        "Please provide an other location.", Title:="No Sufficient rights", Buttons:=vbExclamation
        Resume DefineFileName
    Else
        MsgBox prompt:="Error " & Err.Number & vbNewLine & Err.Description & vbCrLf & "In CreateXML."
    End If
    GoTo Exit_CreateXML

End Sub


'**********************************************************************************
'| Create ABCD xml file
'**********************************************************************************

'DataSets/DataSet/TechnicalContacts
Private Sub XMLTechnicalContacts(ByRef dom As MSXML2.DOMDocument60, ByRef node As MSXML2.IXMLDOMNode)

    Dim xmlTechnicalContact As MSXML2.IXMLDOMElement
    Dim xmlTechnicalContactName As MSXML2.IXMLDOMElement
    Dim xmlTechnicalContactEmail As MSXML2.IXMLDOMElement
    Dim xmlTechnicalContactPhone As MSXML2.IXMLDOMElement
    
    Dim strTechnicalContact, strTechnicalContactEmail
    
    strTechnicalContact = "DaRWIN-team"
    strTechnicalContactEmail = "darwin-ict@naturalsciences.be"
    
    Set xmlTechnicalContact = dom.createNode(NODE_ELEMENT, "TechnicalContact", "http://www.tdwg.org/schemas/abcd/2.06")
    node.appendChild xmlTechnicalContact
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))
    xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))
    
    Set xmlTechnicalContactName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlTechnicalContactName.Text = strTechnicalContact
    xmlTechnicalContact.appendChild xmlTechnicalContactName
    xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))
    
    Set xmlTechnicalContactEmail = dom.createNode(NODE_ELEMENT, "Email", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlTechnicalContactEmail.Text = strTechnicalContactEmail
    xmlTechnicalContact.appendChild xmlTechnicalContactEmail
    xmlTechnicalContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

End Sub

'DataSets/DataSet/ContentContacts
Private Sub XMLContentContacts(ByRef dom As MSXML2.DOMDocument60, ByRef node As MSXML2.IXMLDOMNode)

    Dim xmlContentContact As MSXML2.IXMLDOMElement
    Dim xmlContentContactName As MSXML2.IXMLDOMElement
    Dim xmlContentContactEmail As MSXML2.IXMLDOMElement
    
    Dim strContentContact, strContentContactEmail
    
'    If FeuilleExiste("DNA") Then
'        strContentContact = "DNA extension for ABCD schema contact: DNA Bank Network"
'        strContentContactEmail = "contact(at)dnabank-network.org "
'    Else:
'        strContentContact = "ABCD contact: Walter G. Berendsohn"
'        strContentContactEmail = "w.berendsohn(at)bgbm.org"
'    End If
    
    strContentContact = "DaRWIN-team"
    strContentContactEmail = "darwin-ict@naturalsciences.be"
    
    Set xmlContentContact = dom.createNode(NODE_ELEMENT, "ContentContact", "http://www.tdwg.org/schemas/abcd/2.06")
    node.appendChild xmlContentContact
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))
    xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))
    
    Set xmlContentContactName = dom.createNode(NODE_ELEMENT, "Name", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlContentContactName.Text = strContentContact
    xmlContentContact.appendChild xmlContentContactName
    xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))
    
    Set xmlContentContactEmail = dom.createNode(NODE_ELEMENT, "Email", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlContentContactEmail.Text = strContentContactEmail
    xmlContentContact.appendChild xmlContentContactEmail
    xmlContentContact.appendChild dom.createTextNode(vbCrLf & Space$(6))

End Sub

'DataSets/DataSet/Metadata
Private Sub XMLMetadata(ByRef dom As MSXML2.DOMDocument60, ByRef node As MSXML2.IXMLDOMNode)

    Dim xmlMetadataDescription As MSXML2.IXMLDOMElement
    Dim xmlMetadataRepresentation As MSXML2.IXMLDOMElement
    Dim xmlMetadataTitle As MSXML2.IXMLDOMElement
    Dim xmlMetadataRevisionData As MSXML2.IXMLDOMElement
    Dim xmlMetadataDateModified As MSXML2.IXMLDOMElement
    Dim attrMetadataRepresentation As MSXML2.IXMLDOMAttribute
    Dim xmlMetadataDateCreated As MSXML2.IXMLDOMElement
    Dim xmlVersion As MSXML2.IXMLDOMElement
    Dim xmlVersionMajor As MSXML2.IXMLDOMElement
    Dim xmlVersionMinor As MSXML2.IXMLDOMElement
    
    Set xmlMetadataDescription = dom.createNode(NODE_ELEMENT, "Description", "http://www.tdwg.org/schemas/abcd/2.06")
    node.appendChild xmlMetadataDescription
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))
    xmlMetadataDescription.appendChild dom.createTextNode(vbCrLf & Space$(6))
    Set xmlMetadataRepresentation = dom.createNode(NODE_ELEMENT, "Representation", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlMetadataDescription.appendChild xmlMetadataRepresentation
    xmlMetadataDescription.appendChild dom.createTextNode(vbCrLf & Space$(6))
    xmlMetadataRepresentation.appendChild dom.createTextNode(vbCrLf & Space$(8))
    Set xmlMetadataTitle = dom.createNode(NODE_ELEMENT, "Title", "http://www.tdwg.org/schemas/abcd/2.06")
    If FeuilleExiste("DNA") Then
        xmlMetadataTitle.Text = "Pre-formated ABCDDNA xml file for import into DaRWIN"
    Else:
        xmlMetadataTitle.Text = "Pre-formated ABCD xml file for import into DaRWIN"
    End If
    xmlMetadataRepresentation.appendChild xmlMetadataTitle
    Set attrMetadataRepresentation = dom.createNode(NODE_ATTRIBUTE, "language", "http://www.tdwg.org/schemas/abcd/2.06")
    attrMetadataRepresentation.Value = "EN"
    xmlMetadataRepresentation.setAttributeNode attrMetadataRepresentation
    
    Set xmlVersion = dom.createNode(NODE_ELEMENT, "Version", "http://www.tdwg.org/schemas/abcd/2.06")
    node.appendChild xmlVersion
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))
    xmlVersion.appendChild dom.createTextNode(vbCrLf & Space$(6))
    Set xmlVersionMajor = dom.createNode(NODE_ELEMENT, "Major", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlVersionMajor.Text = "1"
    xmlVersion.appendChild xmlVersionMajor
    xmlVersion.appendChild dom.createTextNode(vbCrLf & Space$(4))
    xmlVersionMajor.appendChild dom.createTextNode(vbCrLf & Space$(6))
    Set xmlVersionMinor = dom.createNode(NODE_ELEMENT, "Minor", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlVersionMinor.Text = "4"
    xmlVersion.appendChild xmlVersionMinor
    xmlVersion.appendChild dom.createTextNode(vbCrLf & Space$(4))
    xmlVersionMinor.appendChild dom.createTextNode(vbCrLf & Space$(6))
    
    Set xmlMetadataRevisionData = dom.createNode(NODE_ELEMENT, "RevisionData", "http://www.tdwg.org/schemas/abcd/2.06")
    node.appendChild xmlMetadataRevisionData
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))
    xmlMetadataRevisionData.appendChild dom.createTextNode(vbCrLf & Space$(6))
    Set xmlMetadataDateModified = dom.createNode(NODE_ELEMENT, "DateModified", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlMetadataDateModified.Text = Format$(Now, "yyyy-mm-dd\THh:Nn:Ss")
    xmlMetadataRevisionData.appendChild xmlMetadataDateModified
    xmlMetadataRevisionData.appendChild dom.createTextNode(vbCrLf & Space$(6))

End Sub

'DataSets/DataSet/Units/Unit/SourceInstitutionID, SourceID and UnitID
Private Sub XMLSpecID(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
    'On Error GoTo Err_XMLSpecID
    
    Dim xmlSpecimenUnitID As MSXML2.IXMLDOMElement
    Dim xmlSourceID As MSXML2.IXMLDOMElement
    Dim xmlSourceInstitutionID As MSXML2.IXMLDOMElement

    Dim strSourceInstitutionID As String, strSourceID As String, strUnitID As String
    
    strSourceInstitutionID = "See Collection attributed in DaRWIN"
    strSourceID = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(datasetName, lookAt:=xlWhole).Column).Value
    strUnitID = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(specimenID, lookAt:=xlWhole).Column).Value
    
    Set xmlSourceInstitutionID = dom.createNode(NODE_ELEMENT, "SourceInstitutionID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSourceInstitutionID.Text = strSourceInstitutionID
    subnode.appendChild xmlSourceInstitutionID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    
    If Not IsEmpty(strSourceID) And Not IsNull(strSourceID) And strSourceID <> "" Then
        Set xmlSourceID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSourceID.Text = strSourceID
        subnode.appendChild xmlSourceID
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    Else:
        Set xmlSourceID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSourceID.Text = "Not defined"
        subnode.appendChild xmlSourceID
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    End If
    
    If Not IsEmpty(strUnitID) And Not IsNull(strUnitID) And strUnitID <> "" Then
        Set xmlSpecimenUnitID = dom.createNode(NODE_ELEMENT, "UnitID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSpecimenUnitID.Text = strUnitID
        subnode.appendChild xmlSpecimenUnitID
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    End If
    
'Exit_XMLSpecID:
'
'        Exit Sub
'
'Err_XMLSpecID:
'
'        MsgBox prompt:="An error occured in sub XMLSpecID." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLSpecID"
'        Resume Exit_XMLSpecID

End Sub

'DataSets/DataSet/Units/Unit/UnitReferences
Private Sub XMLUnitRef(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
    'On Error GoTo Err_XMLUnitRef
    
    Dim xmlUnitReferences As MSXML2.IXMLDOMElement
    Dim xmlUnitReference As MSXML2.IXMLDOMElement
    Dim xmlRefTitle As MSXML2.IXMLDOMElement
    
    Dim strUnitRef As String
    
    strUnitRef = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(publicationString, lookAt:=xlWhole).Column).Value
    
    If Not IsEmpty(strUnitRef) And Not IsNull(strUnitRef) And strUnitRef <> "" Then
    
        Set xmlUnitReferences = dom.createNode(NODE_ELEMENT, "UnitReferences", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlUnitReferences
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlUnitReferences.appendChild dom.createTextNode(vbCrLf + Space$(8))
    
        Set xmlUnitReference = dom.createNode(NODE_ELEMENT, "UnitReference", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlUnitReferences.appendChild xmlUnitReference
        xmlUnitReferences.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlUnitReference.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
        Set xmlRefTitle = dom.createNode(NODE_ELEMENT, "TitleCitation", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlRefTitle.Text = strUnitRef
        xmlUnitReference.appendChild xmlRefTitle
        xmlUnitReference.appendChild dom.createTextNode(vbCrLf + Space$(8))
    
    End If
    
'Exit_XMLUnitRef:
'
'        Exit Sub
'
'Err_XMLUnitRef:
'
'        MsgBox prompt:="An error occured in sub XMLUnitRef." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLUnitRef"
'        Resume Exit_XMLUnitRef

End Sub

'DataSets/DataSet/Units/Unit/Identifications
' Code totaly refactored by Paul-André Duchesne (Royal belgian Institute for natural Sciences) on the 2015-12-22
Private Sub XMLIdentification(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long, ByRef strate As Integer)

    On Error Resume Next
'    On Error GoTo Err_XMLIdentification
    
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
    Dim xmlIdentificationZooSubgenus As MSXML2.IXMLDOMElement
    Dim xmlIdentificationSpeciesEpithet As MSXML2.IXMLDOMElement
    Dim xmlIdentificationSubspeciesEpithet As MSXML2.IXMLDOMElement
    Dim xmlIdentificationNameAt As MSXML2.IXMLDOMElement
    Dim xmlIdentificationZoo As MSXML2.IXMLDOMElement
    Dim xmlIdentificationZooGenus As MSXML2.IXMLDOMElement
    Dim xmlIdentificationZooSpecies As MSXML2.IXMLDOMElement
    Dim xmlIdentificationZooSubspecies As MSXML2.IXMLDOMElement
    Dim xmlIdentificationZooAuthor As MSXML2.IXMLDOMElement
    Dim xmlIdentificationBota As MSXML2.IXMLDOMElement
    Dim xmlIdentificationBotaGenus As MSXML2.IXMLDOMElement
    Dim xmlIdentificationBotaSpecies As MSXML2.IXMLDOMElement
    Dim xmlIdentificationBotaSubSpecies As MSXML2.IXMLDOMElement
    Dim xmlIdentificationBotaAuthor As MSXML2.IXMLDOMElement
    Dim xmlIdentificationInformalNameString As MSXML2.IXMLDOMElement
    Dim xmlNameAddendum As MSXML2.IXMLDOMElement
    Dim xmlIdDate As MSXML2.IXMLDOMElement
    Dim xmlIdDateText As MSXML2.IXMLDOMElement
    Dim xmlIdISODate As MSXML2.IXMLDOMElement
    Dim xmlIdentificationIdentifiers As MSXML2.IXMLDOMElement
    Dim xmlIdentificationIdentifier As MSXML2.IXMLDOMElement
    Dim xmlIdentifierPerson As MSXML2.IXMLDOMElement
    Dim xmlIdentifierPersonName As MSXML2.IXMLDOMElement
    Dim xmlIdentificationMethod As MSXML2.IXMLDOMElement
    Dim xmlReferences As MSXML2.IXMLDOMElement
    Dim xmlReference As MSXML2.IXMLDOMElement
    Dim xmlIdTitleCitation As MSXML2.IXMLDOMElement
    Dim xmlTaxoComment As MSXML2.IXMLDOMElement
    Dim xmlIdentificationHistory As MSXML2.IXMLDOMElement
    
    ' >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    ' Added/Modified the 2015/12/22
    '-----------------------------------------------
    Dim strTaxonFullName As String, strTaxonName As String, strTaxonRank As String, strGenus As String, strSubgenus As String, strSpecies As String, strSubspecies As String, strAuthorYear As String
    Dim strInformalName As String, strNameAddendum As String, strIdentifier As String, strIdRef As String, strClassification As String
    Dim strDetermDD As String, strDetermMM As String, strDetermYY As String, strIdDate As String, strIdentificationMethod As String, strTaxoComment As String
    Dim strOldGenus As String, strOldSubgenus As String, strIdentificationHistory As String
    
    Dim rep As String, rep2 As String, sep As String
    Dim celval As String, celcol As Integer
    '-----------------------------------------------
    ' Fifth new variables
    '-----------------------------------------------
    Dim tempTaxonName() As String
    Dim booComposed As Boolean
    Dim iCounter As Long
    Dim subGenDelimPos As Long
    
    Dim regExp As New regExp

    With regExp
        .Global = True
        .MultiLine = True
        .IgnoreCase = False
    End With
    
    '-----------------------------------------------
    '<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    rep = ""
    rep2 = ""
    sep = " "
    
    Dim Count As Integer
    Dim taxonomy(10)
        taxonomy(1) = "Phylum"
        taxonomy(2) = "Class"
        taxonomy(3) = "Order"
        taxonomy(4) = "Superfamily"
        taxonomy(5) = "Family"
        taxonomy(6) = "Subfamily"
        taxonomy(7) = "Genus"
        taxonomy(8) = "Subgenus"
        taxonomy(9) = "Species"
        taxonomy(10) = "Subspecies"
    
    Dim rank(10) As String
        rank(1) = "phylum"
        rank(2) = "classis"
        rank(3) = "ordo"
        rank(4) = "superfamilia"
        rank(5) = "familia"
        rank(6) = "subfamilia"
        rank(7) = "genus"
        rank(8) = "subgenus"
        rank(9) = "species"
        rank(10) = "subspecies"
    
    ' >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    ' Added/Modified the 2015/12/22
    '-----------------------------------------------
    ' New variable definition
    '-----------------------------------------------
    Dim rankABCDName(8) As String
        rankABCDName(1) = "phylum"
        rankABCDName(2) = "classis"
        rankABCDName(3) = "ordo"
        rankABCDName(4) = "superfamilia"
        rankABCDName(5) = "familia"
        rankABCDName(6) = "subfamilia"
        rankABCDName(7) = "genusgroup"
        rankABCDName(8) = "unranked"
        
    Dim higherTaxaUpTo As Long
    '-----------------------------------------------
    ' Classification cleaning and definition
    '-----------------------------------------------
    Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(classification, lookAt:=xlWhole, MatchCase:=True).Column).Value = _
    CleanUp(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(classification, lookAt:=xlWhole, MatchCase:=True).Column).Value)
    strClassification = LCase(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(classification, lookAt:=xlWhole, MatchCase:=True).Column).Value)
    If strClassification = "plantae" Then
        strClassification = "botanical"
    End If
    '-----------------------------------------------
    ' Taxonomy content cleaning
    '-----------------------------------------------
    For Count = 1 To 10
        Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value = _
        CleanUp(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value)
    Next Count
    '-----------------------------------------------
    ' Higher Taxa up to count
    '-----------------------------------------------
    For Count = 1 To 10
        celval = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value
        If Not IsEmpty(celval) And Not IsNull(celval) And celval <> "" Then
            higherTaxaUpTo = Count
        End If
        celval = ""
    Next Count
    '-----------------------------------------------
    ' AuthorTeam content cleaning
    '-----------------------------------------------
    Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(author_year, lookAt:=xlWhole, MatchCase:=True).Column).Value = _
    CleanUp(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(author_year, lookAt:=xlWhole, MatchCase:=True).Column).Value)
    strAuthorYear = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(author_year, lookAt:=xlWhole, MatchCase:=True).Column).Value
    '-----------------------------------------------
    ' NameAdendum content cleaning
    '-----------------------------------------------
    Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(variety_form, lookAt:=xlWhole).Column).Value = _
    CleanUp(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(variety_form, lookAt:=xlWhole).Column).Value)
    strNameAddendum = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(variety_form, lookAt:=xlWhole).Column).Value
    '-----------------------------------------------
    ' Informal name content cleaning
    '-----------------------------------------------
    Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(informalName, lookAt:=xlWhole, MatchCase:=True).Column).Value = _
    CleanUp(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(informalName, lookAt:=xlWhole, MatchCase:=True).Column).Value)
    strInformalName = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(informalName, lookAt:=xlWhole, MatchCase:=True).Column).Value
    '-----------------------------------------------
    ' TaxonFullName content cleaning and composition
    '-----------------------------------------------
    Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(taxonFullName, lookAt:=xlWhole).Column).Value = _
    CleanUp(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(taxonFullName, lookAt:=xlWhole).Column).Value)
    strTaxonFullName = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(taxonFullName, lookAt:=xlWhole).Column).Value
    ' If taxon full name not filled and the informal name not filled either, we can try composing the full name
    If (IsEmpty(strTaxonFullName) Or IsNull(strTaxonFullName) Or strTaxonFullName = "") And _
       (IsEmpty(strInformalName) Or IsNull(strInformalName) Or strInformalName = "") Then
        ' If user filled in the fields above genus level...
        If higherTaxaUpTo > 7 Then
            booComposed = True
            For Count = 7 To 10
                ' If genus is not present, it's not possible to auto-compose
                If Count = 7 And ( _
                    IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Or _
                    IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Or _
                    Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value = "" _
                    ) Then
                    booComposed = False
                    Exit For
                ' Stop the moment a field is encountered empty (for level above subgenus)
                ElseIf Count <> 8 And ( _
                    IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Or _
                    IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Or _
                    Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value = "" _
                    ) Then
                    Exit For
                ' If not subgenus level and not empty, compose the taxon full name
                ElseIf Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) And _
                        Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) And _
                        Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value <> "" Then
                    ' Split in an array the content of field
                    tempTaxonName = Split(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value)
                    'Define Parenthesis pattern
                    regExp.Pattern = "[\(\)]+"
                    ' Depending the level encountered, fill the taxon full name differently
                    Select Case Count
                        Case Is = 7
                            strTaxonFullName = strTaxonFullName & " " & tempTaxonName(0)
                        Case Is = 8
                            If strClassification = "botanical" Then
                                If ( _
                                    InStr(LCase(tempTaxonName(0)), "subgen.") > 0 Or _
                                    InStr(LCase(tempTaxonName(0)), "subg.") > 0 _
                                    ) And _
                                    UBound(tempTaxonName) > 0 Then
                                    strTaxonFullName = strTaxonFullName & " subgen. " & tempTaxonName(1)
                                ElseIf regExp.Test(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Then
                                    strTaxonFullName = strTaxonFullName & " subgen. " & regExp.Replace(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value, "")
                                Else
                                    strTaxonFullName = strTaxonFullName & " subgen. " & tempTaxonName(0)
                                End If
                            ElseIf Left$(tempTaxonName(0), 1) <> "(" Then
                                strTaxonFullName = strTaxonFullName & " (" & tempTaxonName(0) & ")"
                            Else
                                strTaxonFullName = strTaxonFullName & " " & tempTaxonName(0)
                            End If
                        Case Is = 9
                            strTaxonFullName = strTaxonFullName & " " & Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value
                        Case Is = 10
                            If LCase(tempTaxonName(0)) <> "ssp." And _
                               LCase(tempTaxonName(0)) <> "subsp." And _
                               LCase(tempTaxonName(0)) <> "f." And _
                               LCase(tempTaxonName(0)) <> "form." And _
                               LCase(tempTaxonName(0)) <> "subf." And _
                               LCase(tempTaxonName(0)) <> "var." And _
                               LCase(tempTaxonName(0)) <> "subvar." Then
                                strTaxonFullName = strTaxonFullName & " subsp. " & Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value
                            Else
                                strTaxonFullName = strTaxonFullName & " " & Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value
                            End If
                    End Select
                End If
            Next Count
            ' If name composition occured, fill in the rest
            If booComposed Then
                If strNameAddendum <> "" And Not IsEmpty(strNameAddendum) And Not IsNull(strNameAddendum) Then
                    strTaxonFullName = strTaxonFullName & " var. " & strNameAddendum
                End If
                If Not IsEmpty(strAuthorYear) And Not IsNull(strAuthorYear) And strAuthorYear <> "" And InStr(strTaxonFullName, strAuthorYear) = 0 Then
                    strTaxonFullName = strTaxonFullName & " " & strAuthorYear
                End If
                strTaxonFullName = Trim$(strTaxonFullName)
            End If
        '... if not, take the higher level filled
        Else
            strTaxonFullName = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(higherTaxaUpTo), lookAt:=xlWhole, MatchCase:=True).Column).Value
            tempTaxonName = Split(strTaxonFullName)
            ' If Taxon full name is composed of only one element (no author and year) and the field author
            ' and year is filled, add it to the full name
            If UBound(tempTaxonName) = 0 And _
                Not IsEmpty(strAuthorYear) And _
                Not IsNull(strAuthorYear) And _
                strAuthorYear <> "" Then
                strTaxonFullName = strTaxonFullName & " " & strAuthorYear
            End If
        End If
    End If
    '-----------------------------------------------
    ' Move up other completed fields
    '-----------------------------------------------
    ' Identifiers
    '-----------------------------------------------
    strIdentifier = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identifiedBy, lookAt:=xlWhole).Column).Value
    '-----------------------------------------------
    ' Bib reference
    '-----------------------------------------------
    strIdRef = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(referenceString, lookAt:=xlWhole).Column).Value
    '-----------------------------------------------
    ' Determination date composants
    '-----------------------------------------------
    ' Day
    strDetermDD = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationDay, lookAt:=xlWhole).Column).Value
    If strDetermDD <> "" And IsNumeric(strDetermDD) Then
        If strDetermDD > 31 Or strDetermDD = 0 Then
            strDetermDD = ""
        ElseIf strDetermDD < 10 Then
            strDetermDD = "0" & strDetermDD
        End If
    Else:
        strDetermDD = ""
    End If
    ' Month
    strDetermMM = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationMonth, lookAt:=xlWhole).Column).Value
    If strDetermMM <> "" And IsNumeric(strDetermMM) Then
        If strDetermMM > 12 Or strDetermMM = 0 Then
            strDetermMM = ""
        ElseIf strDetermMM < 10 Then
            strDetermMM = "0" & strDetermMM
        End If
    Else:
        strDetermMM = ""
    End If
    ' Year
    strDetermYY = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationYear, lookAt:=xlWhole).Column).Value
    If strDetermYY <> "" And IsNumeric(strDetermYY) Then
        If strDetermYY > 999 Then
            strDetermYY = strDetermYY
        Else:
            strDetermYY = ""
        End If
    Else:
        strDetermYY = ""
    End If
    ' Date composition
    If strDetermYY <> "" And strDetermMM <> "" And strDetermDD <> "" Then
        strIdDate = strDetermYY & "-" & strDetermMM & "-" & strDetermDD
    ElseIf strDetermYY <> "" And strDetermMM <> "" And strDetermDD = "" Then
        strIdDate = strDetermYY & "-" & strDetermMM
    ElseIf strDetermYY <> "" And strDetermMM = "" And strDetermDD = "" Then
        strIdDate = strDetermYY
    Else:
        strIdDate = ""
    End If
    '-----------------------------------------------
    ' Identification method
    '-----------------------------------------------
    strIdentificationMethod = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationMethod, lookAt:=xlWhole).Column).Value
    '-----------------------------------------------
    ' Taxon comments
    '-----------------------------------------------
    strTaxoComment = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationNotes, lookAt:=xlWhole).Column).Value
    '-----------------------------------------------
    ' Identification History - sort of comment
    '-----------------------------------------------
    strIdentificationHistory = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationHistory, lookAt:=xlWhole).Column).Value
    'strOldGenus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(oldGenus, lookAt:=xlWhole).Column).Value
    'strOldSubgenus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(oldSubgenus, lookAt:=xlWhole).Column).Value
    'If strOldGenus <> "" And strOldSubgenus <> "" Then
    '    strIdentificationHistory = "Old genus: " & strOldGenus & "; old subgenus: " & strOldSubgenus & vbCrLf & strIdentificationHistory
    'ElseIf strOldGenus <> "" And strOldSubgenus = "" Then
    '    strIdentificationHistory = "Old genus: " & strOldGenus & vbCrLf & strIdentificationHistory
    'ElseIf strOldSubgenus <> "" And strOldGenus = "" Then
    '    strIdentificationHistory = "Old sub genus: " & strOldSubgenus & vbCrLf & strIdentificationHistory
    'End If
    '-----------------------------------------------
    ' Redefinition of strTestFill
    '-----------------------------------------------
    Dim strTestFill As String
    strTestFill = strTaxonFullName & strInformalName

    'pas identification history car comme il s'agit d'une balise directement sous Identifications, il ne faut pas créer l'arbre Identification -> Result -> etc.
    
    If (Not IsEmpty(strTestFill) And Not IsNull(strTestFill) And strTestFill <> "") _
        Or _
       (Not IsEmpty(strIdentificationHistory) And Not IsNull(strIdentificationHistory) And strIdentificationHistory <> "") Then
    '-----------------------------------------------
    ' <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        Set xmlIdentifications = dom.createNode(NODE_ELEMENT, "Identifications", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlIdentifications
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlIdentifications.appendChild dom.createTextNode(vbCrLf + Space$(8))
    
        If Not IsEmpty(strTestFill) And Not IsNull(strTestFill) And strTestFill <> "" Then

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
            
            '>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            ' Added on the 2015-12-22
            '---------------------------------------------
            ' Redefinition of Higher Taxa composition
            '---------------------------------------------
            
            ' Definition of Higher Taxa
            
            Set xmlIdentificationHiTaxa = dom.createNode(NODE_ELEMENT, "HigherTaxa", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlIdentificationTaxonId.appendChild xmlIdentificationHiTaxa
            xmlIdentificationTaxonId.appendChild dom.createTextNode(vbCrLf + Space$(14))
            xmlIdentificationHiTaxa.appendChild dom.createTextNode(vbCrLf + Space$(16))
            
           'Higher taxonomic rank data
            For Count = 1 To 8
                If Count < higherTaxaUpTo Then
                
                    celval = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole).Column).Value
                    tempTaxonName = Split(celval)
                    strTaxonName = ""
                    If strClassification = "botanical" And _
                        Count = 8 And _
                        ( _
                            InStr(LCase(tempTaxonName(0)), "subgen.") > 0 Or _
                            InStr(LCase(tempTaxonName(0)), "subg.") > 0 _
                        ) Then
                        If UBound(tempTaxonName) > 0 Then
                            For iCounter = 1 To UBound(tempTaxonName)
                                strTaxonName = strTaxonName & " " & tempTaxonName(iCounter)
                            Next iCounter
                        End If
                    ElseIf Count = 8 And _
                            Left$(tempTaxonName(0), 1) = "(" Then
                        'Clean up from parenthesis
                        regExp.Pattern = "[\(\)]+"
                        If regExp.Test(celval) Then
                            strTaxonName = regExp.Replace(celval, "")
                        End If
                    Else
                        strTaxonName = celval
                    End If
                    
                    strTaxonName = Trim$(strTaxonName)
                    
                    If Not IsEmpty(strTaxonName) And Not IsNull(strTaxonName) And strTaxonName <> "" Then
                 
                        Set xmlIdentificationHiTaxon = dom.createNode(NODE_ELEMENT, "HigherTaxon", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlIdentificationHiTaxa.appendChild xmlIdentificationHiTaxon
                        xmlIdentificationHiTaxa.appendChild dom.createTextNode(vbCrLf + Space$(16))
                        xmlIdentificationHiTaxon.appendChild dom.createTextNode(vbCrLf + Space$(18))
            
                        Set xmlIdentificationHiTaxonName = dom.createNode(NODE_ELEMENT, "HigherTaxonName", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlIdentificationHiTaxonName.Text = strTaxonName
                        xmlIdentificationHiTaxon.appendChild xmlIdentificationHiTaxonName
                        xmlIdentificationHiTaxon.appendChild dom.createTextNode(vbCrLf + Space$(18))
                    
                        strTaxonRank = rankABCDName(Count)
            
                        Set xmlIdentificationHiTaxonRank = dom.createNode(NODE_ELEMENT, "HigherTaxonRank", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlIdentificationHiTaxonRank.Text = strTaxonRank
                        xmlIdentificationHiTaxon.appendChild xmlIdentificationHiTaxonRank
                        xmlIdentificationHiTaxon.appendChild dom.createTextNode(vbCrLf + Space$(18))
                        
                    End If
                    
                    celval = ""
                End If
            Next Count
            
            '---------------------------------------------
            ' Redefinition of either ScientificFullName or
            ' InformalNameString definition
            '---------------------------------------------
            
            If Not IsEmpty(strInformalName) And Not IsNull(strInformalName) And strInformalName <> "" Then
                
                Set xmlIdentificationInformalNameString = dom.createNode(NODE_ELEMENT, "InformalNameString", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationInformalNameString.Text = strInformalName
                xmlIdentificationTaxonId.appendChild xmlIdentificationInformalNameString
                xmlIdentificationTaxonId.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
            ElseIf Not IsEmpty(strTaxonFullName) And Not IsNull(strTaxonFullName) And strTaxonFullName <> "" Then
                
                Set xmlIdentificationScName = dom.createNode(NODE_ELEMENT, "ScientificName", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationTaxonId.appendChild xmlIdentificationScName
                xmlIdentificationTaxonId.appendChild dom.createTextNode(vbCrLf + Space$(14))
                xmlIdentificationScName.appendChild dom.createTextNode(vbCrLf + Space$(16))
                
                Set xmlIdentificationFullScNameString = dom.createNode(NODE_ELEMENT, "FullScientificNameString", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationFullScNameString.Text = strTaxonFullName
                xmlIdentificationScName.appendChild xmlIdentificationFullScNameString
                xmlIdentificationScName.appendChild dom.createTextNode(vbCrLf + Space$(16))
                                    
                ' Definition of NameAtomised entries
                Set xmlIdentificationNameAt = dom.createNode(NODE_ELEMENT, "NameAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationScName.appendChild xmlIdentificationNameAt
                xmlIdentificationScName.appendChild dom.createTextNode(vbCrLf + Space$(16))
                xmlIdentificationNameAt.appendChild dom.createTextNode(vbCrLf + Space$(18))
                
                ' Composition of the GenusOrMonomial and Subgenus keywords / NameAtomised entries
                ' This composition is valid for either Plantae or Animalia entries
                strGenus = ""
                strSubgenus = ""
                For Count = 8 To 1 Step -1
                    If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole).Column).Value) And _
                        Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole).Column).Value) And _
                        Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole).Column).Value <> "" Then
                        tempTaxonName = Split(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole).Column).Value)
                        If Count = 8 Then
                            If strClassification <> "botanical" Then
                                If ( _
                                    LCase(tempTaxonName(0)) = "subgen." Or _
                                    LCase(tempTaxonName(0)) = "subg." _
                                   ) And UBound(tempTaxonName) > 0 Then
                                    strSubgenus = tempTaxonName(1)
                                ElseIf regExp.Test(tempTaxonName(0)) Then
                                    strSubgenus = regExp.Replace(tempTaxonName(0), "")
                                Else
                                    strSubgenus = tempTaxonName(0)
                                End If
                            End If
                        Else
                            strGenus = tempTaxonName(0)
                            Exit For
                        End If
                    End If
                Next Count
                If higherTaxaUpTo > 8 Then
                    If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(9), lookAt:=xlWhole).Column).Value) And _
                        Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(9), lookAt:=xlWhole).Column).Value) And _
                        Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(9), lookAt:=xlWhole).Column).Value <> "" Then
                        strSpecies = Trim$(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(9), lookAt:=xlWhole).Column).Value)
                        If strAuthorYear <> "" Then
                            regExp.Pattern = strAuthorYear
                            If regExp.Test(strSpecies) Then
                                strSpecies = Trim$(regExp.Replace(strSpecies, ""))
                            End If
                        End If
                    End If
                    If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(10), lookAt:=xlWhole).Column).Value) And _
                        Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(10), lookAt:=xlWhole).Column).Value) And _
                        Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(10), lookAt:=xlWhole).Column).Value <> "" Then
                        strSubspecies = Trim$(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(10), lookAt:=xlWhole).Column).Value)
                        If strAuthorYear <> "" Then
                            regExp.Pattern = strAuthorYear
                            If regExp.Test(strSubspecies) Then
                                strSubspecies = Trim$(regExp.Replace(strSubspecies, ""))
                            End If
                        End If
                    End If
                End If
                'Depending the classification used, define differently the keywords
                If strClassification = "botanical" Then
                    Set xmlIdentificationBota = dom.createNode(NODE_ELEMENT, "Botanical", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlIdentificationNameAt.appendChild xmlIdentificationBota
                    xmlIdentificationNameAt.appendChild dom.createTextNode(vbCrLf + Space$(18))
                    xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
                    'GenusOrMonomial keyword
                    Set xmlIdentificationBotaGenus = dom.createNode(NODE_ELEMENT, "GenusOrMonomial", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlIdentificationBotaGenus.Text = strGenus
                    xmlIdentificationBota.appendChild xmlIdentificationBotaGenus
                    xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
                    'FirstEpithet keyword
                    If Not IsEmpty(strSpecies) And _
                        Not IsNull(strSpecies) And _
                        strSpecies <> "" Then
                        Set xmlIdentificationBotaSpecies = dom.createNode(NODE_ELEMENT, "FirstEpithet", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlIdentificationBotaSpecies.Text = strSpecies
                        xmlIdentificationBota.appendChild xmlIdentificationBotaSpecies
                        xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
                    End If
                    'InfraspecificEpithet keyword
                    If Not IsEmpty(strSubspecies) And _
                        Not IsNull(strSubspecies) And _
                        strSubspecies <> "" Then
                        Set xmlIdentificationBotaSubSpecies = dom.createNode(NODE_ELEMENT, "InfraspecificEpithet", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlIdentificationBotaSubSpecies.Text = strSubspecies
                        xmlIdentificationBota.appendChild xmlIdentificationBotaSubSpecies
                        xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
                    End If
                    If Not IsEmpty(strAuthorYear) And Not IsNull(strAuthorYear) And strAuthorYear <> "" Then
                        If InStr(strAuthorYear, "(") > 0 Then
                            Set xmlIdentificationBotaAuthor = dom.createNode(NODE_ELEMENT, "AuthorTeamParenthesis", "http://www.tdwg.org/schemas/abcd/2.06")
                            xmlIdentificationBotaAuthor.Text = strAuthorYear
                            xmlIdentificationBota.appendChild xmlIdentificationBotaAuthor
                            xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
                        Else
                            Set xmlIdentificationBotaAuthor = dom.createNode(NODE_ELEMENT, "AuthorTeam", "http://www.tdwg.org/schemas/abcd/2.06")
                            xmlIdentificationBotaAuthor.Text = strAuthorYear
                            xmlIdentificationBota.appendChild xmlIdentificationBotaAuthor
                            xmlIdentificationBota.appendChild dom.createTextNode(vbCrLf + Space$(20))
                        End If
                    End If
                Else
                    Set xmlIdentificationZoo = dom.createNode(NODE_ELEMENT, "Zoological", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlIdentificationNameAt.appendChild xmlIdentificationZoo
                    xmlIdentificationNameAt.appendChild dom.createTextNode(vbCrLf + Space$(18))
                    xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                    'GenusOrMonomial keyword
                    Set xmlIdentificationZooGenus = dom.createNode(NODE_ELEMENT, "GenusOrMonomial", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlIdentificationZooGenus.Text = strGenus
                    xmlIdentificationZoo.appendChild xmlIdentificationZooGenus
                    xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                    'Subgenus keyword
                    If Not IsEmpty(strSubgenus) And _
                        Not IsNull(strSubgenus) And _
                        strSubgenus <> "" Then
                        Set xmlIdentificationZooSubgenus = dom.createNode(NODE_ELEMENT, "Subgenus", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlIdentificationZooSubgenus.Text = strSubgenus
                        xmlIdentificationZoo.appendChild xmlIdentificationZooSubgenus
                        xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                    End If
                    'SpeciesEpithet keyword
                    If Not IsEmpty(strSpecies) And _
                        Not IsNull(strSpecies) And _
                        strSpecies <> "" Then
                        Set xmlIdentificationZooSpecies = dom.createNode(NODE_ELEMENT, "SpeciesEpithet", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlIdentificationZooSpecies.Text = strSpecies
                        xmlIdentificationZoo.appendChild xmlIdentificationZooSpecies
                        xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                    End If
                    'SubspeciesEpithet keyword
                    If Not IsEmpty(strSubspecies) And _
                        Not IsNull(strSubspecies) And _
                        strSubspecies <> "" Then
                        Set xmlIdentificationZooSubspecies = dom.createNode(NODE_ELEMENT, "SubspeciesEpithet", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlIdentificationZooSubspecies.Text = strSubspecies
                        xmlIdentificationZoo.appendChild xmlIdentificationZooSubspecies
                        xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                    End If
                    If Not IsEmpty(strAuthorYear) And Not IsNull(strAuthorYear) And strAuthorYear <> "" Then
                        If InStr(strAuthorYear, "(") > 0 Then
                            Set xmlIdentificationZooAuthor = dom.createNode(NODE_ELEMENT, "AuthorTeamParenthesisAndYear", "http://www.tdwg.org/schemas/abcd/2.06")
                            xmlIdentificationZooAuthor.Text = strAuthorYear
                            xmlIdentificationZoo.appendChild xmlIdentificationZooAuthor
                            xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                        Else
                            Set xmlIdentificationZooAuthor = dom.createNode(NODE_ELEMENT, "AuthorTeamOriginalAndYear", "http://www.tdwg.org/schemas/abcd/2.06")
                            xmlIdentificationZooAuthor.Text = strAuthorYear
                            xmlIdentificationZoo.appendChild xmlIdentificationZooAuthor
                            xmlIdentificationZoo.appendChild dom.createTextNode(vbCrLf + Space$(20))
                        End If
                    End If

                End If
                                
                If Not IsEmpty(strNameAddendum) And Not IsNull(strNameAddendum) And strNameAddendum <> "" Then
                
                    Set xmlNameAddendum = dom.createNode(NODE_ELEMENT, "NameAddendum", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlNameAddendum.Text = "Variety : " & strNameAddendum
                    xmlIdentificationScName.appendChild xmlNameAddendum
                    xmlIdentificationScName.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                End If
                        
            End If
            '----------------------------------------
            '<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
            If Not IsEmpty(strIdentifier) And Not IsNull(strIdentifier) And strIdentifier <> "" Then
            
                Set xmlIdentificationIdentifiers = dom.createNode(NODE_ELEMENT, "Identifiers", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdIdentification.appendChild xmlIdentificationIdentifiers
                xmlIdIdentification.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlIdentificationIdentifiers.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
                Set xmlIdentificationIdentifier = dom.createNode(NODE_ELEMENT, "Identifier", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationIdentifiers.appendChild xmlIdentificationIdentifier
                xmlIdentificationIdentifiers.appendChild dom.createTextNode(vbCrLf + Space$(12))
                xmlIdentificationIdentifier.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                Set xmlIdentifierPerson = dom.createNode(NODE_ELEMENT, "PersonName", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentificationIdentifier.appendChild xmlIdentifierPerson
                xmlIdentificationIdentifier.appendChild dom.createTextNode(vbCrLf + Space$(14))
                xmlIdentifierPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
        
                Set xmlIdentifierPersonName = dom.createNode(NODE_ELEMENT, "FullName", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdentifierPersonName.Text = strIdentifier
                xmlIdentifierPerson.appendChild xmlIdentifierPersonName
                xmlIdentifierPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
               
            End If
                       
            If (Not IsEmpty(strIdRef) And Not IsNull(strIdRef) And strIdRef <> "") And strate = 1 Then
            
                Set xmlReferences = dom.createNode(NODE_ELEMENT, "References", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdIdentification.appendChild xmlReferences
                xmlIdIdentification.appendChild dom.createTextNode(vbCrLf + Space$(14))
                xmlReferences.appendChild dom.createTextNode(vbCrLf + Space$(16))
            
                Set xmlReference = dom.createNode(NODE_ELEMENT, "Reference", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlReferences.appendChild xmlReference
                xmlReferences.appendChild dom.createTextNode(vbCrLf + Space$(16))
                xmlReference.appendChild dom.createTextNode(vbCrLf + Space$(18))
            
                Set xmlIdTitleCitation = dom.createNode(NODE_ELEMENT, "TitleCitation", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdTitleCitation.Text = strIdRef
                xmlReference.appendChild xmlIdTitleCitation
                xmlReference.appendChild dom.createTextNode(vbCrLf + Space$(18))
                        
            End If
                            
            If Not IsEmpty(strIdDate) And Not IsNull(strIdDate) And strIdDate <> "" Then
                    
                Set xmlIdDate = dom.createNode(NODE_ELEMENT, "Date", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdIdentification.appendChild xmlIdDate
                xmlIdIdentification.appendChild dom.createTextNode(vbCrLf + Space$(14))
                xmlIdDate.appendChild dom.createTextNode(vbCrLf + Space$(16))
                
                Set xmlIdISODate = dom.createNode(NODE_ELEMENT, "ISODateTimeBegin", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlIdISODate.Text = strIdDate
                xmlIdDate.appendChild xmlIdISODate
                xmlIdDate.appendChild dom.createTextNode(vbCrLf + Space$(16))
                
            End If
            
            If strate = 1 Then
                If Not IsEmpty(strIdentificationMethod) And Not IsNull(strIdentificationMethod) And strIdentificationMethod <> "" Then
            
                    Set xmlIdentificationMethod = dom.createNode(NODE_ELEMENT, "Method", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlIdentificationMethod.Text = strIdentificationMethod
                    xmlIdIdentification.appendChild xmlIdentificationMethod
                    xmlIdIdentification.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                End If
                        
                If Not IsEmpty(strTaxoComment) And Not IsNull(strTaxoComment) And strTaxoComment <> "" Then
                    
                    Set xmlTaxoComment = dom.createNode(NODE_ELEMENT, "Notes", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlTaxoComment.Text = strTaxoComment
                    xmlIdIdentification.appendChild xmlTaxoComment
                    xmlIdIdentification.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                End If
            End If

        End If
        
        If Not IsEmpty(strIdentificationHistory) And Not IsNull(strIdentificationHistory) And strIdentificationHistory <> "" And strate = 1 Then
        
            Set xmlIdentificationHistory = dom.createNode(NODE_ELEMENT, "IdentificationHistory", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlIdentificationHistory.Text = strIdentificationHistory
            xmlIdentifications.appendChild xmlIdentificationHistory
            xmlIdentifications.appendChild dom.createTextNode(vbCrLf + Space$(8))
        
        End If
    
    End If

End Sub

'DataSets/DataSet/Units/Unit/RecordBasis
Private Sub XMLRecordBasis(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLRecordBasis
    
    Dim xmlSpecCategory As MSXML2.IXMLDOMElement
    
    Dim strSpecCategory As String
    
    strSpecCategory = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(isPhysical, lookAt:=xlWhole).Column).Value
    
    If strSpecCategory = "Yes" Or strSpecCategory = "yes" Or strSpecCategory = "Y" Or strSpecCategory = "y" Or strSpecCategory = "Physical" Or strSpecCategory = "physical" Then
        strSpecCategory = "PreservedSpecimen"
    ElseIf strSpecCategory = "No" Or strSpecCategory = "no" Or strSpecCategory = "N" Or strSpecCategory = "n" Or strSpecCategory = "Observation" Or strSpecCategory = "observation" Then
        strSpecCategory = "HumanObservation"
    Else:
        strSpecCategory = ""
    End If
    
    If Not IsEmpty(strSpecCategory) And Not IsNull(strSpecCategory) And strSpecCategory <> "" Then
        Set xmlSpecCategory = dom.createNode(NODE_ELEMENT, "RecordBasis", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSpecCategory.Text = strSpecCategory
        subnode.appendChild xmlSpecCategory
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    End If
    
'Exit_XMLRecordBasis:
'
'        Exit Sub
'
'Err_XMLRecordBasis:
'
'        MsgBox prompt:="An error occured in sub XMLRecordBasis." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLRecordBasis"
'        Resume Exit_XMLRecordBasis
'
End Sub

'DataSets/DataSet/Units/Unit/KindOfUnit
Private Sub XMLKindOfUnit(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long, ByRef strate As Integer)

    On Error Resume Next
'    On Error GoTo Err_XMLKindOfUnit
    
    Dim xmlKindUnit As MSXML2.IXMLDOMElement
    Dim strKindOfUnit_class As String
    Dim strKindOfUnit_part As String
    Dim strKindOfUnit As String

    If strate = 1 Then

        strKindOfUnit = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(kindOfUnit, lookAt:=xlWhole).Column).Value
        
    ElseIf strate = 2 Then

        strKindOfUnit_part = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(partOfOrganism, lookAt:=xlWhole).Column).Value
        strKindOfUnit_class = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(sampleTissueType, lookAt:=xlWhole).Column).Value
    
        If strKindOfUnit_part <> "" And strKindOfUnit_class <> "" Then
            strKindOfUnit = strKindOfUnit_part & ", " & strKindOfUnit_class
        ElseIf strKindOfUnit_part <> "" And strKindOfUnit_class = "" Then
            strKindOfUnit = strKindOfUnit_part
        ElseIf strKindOfUnit_part = "" And strKindOfUnit_class <> "" Then
            strKindOfUnit = strKindOfUnit_class
        ElseIf strKindOfUnit_part = "" And strKindOfUnit_class = "" Then
            strKindOfUnit = "Tissue"
        End If

    ElseIf strate = 3 Then
    
            strKindOfUnit = "DNA extract"
    
    End If
    
    If Not IsEmpty(strKindOfUnit) And Not IsNull(strKindOfUnit) And strKindOfUnit <> "" Then
        Set xmlKindUnit = dom.createNode(NODE_ELEMENT, "KindOfUnit", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlKindUnit.Text = strKindOfUnit
        subnode.appendChild xmlKindUnit
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    End If
    
'Exit_XMLKindOfUnit:
'
'        Exit Sub
'
'Err_XMLKindOfUnit:
'
'        MsgBox prompt:="An error occured in sub XMLKindOfUnit." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLKindOfUnit"
'        Resume Exit_XMLKindOfUnit

End Sub

'DataSets/DataSet/Units/Unit/SpecimenUnit/Acquisition, Accession, Preparations, NomenclaturalTypeDesignations
Private Sub XMLSpecimenUnit(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLSpecimenUnit
    
    Dim xmlSpecUnit As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquisition As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquisitionDate As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquiredFrom As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquiredPerson As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquiredPersonName As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquisitionText As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquisitionType As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAccession As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAccessionDate As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAccessionCatalogue As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAccessionNumber As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitPreparations As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitPreparation As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitPreparationType As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitPreparationMaterials As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitNomenclatureTypes As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitNomenclatureType As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitNomenclatureTypeStatus As MSXML2.IXMLDOMElement
    
    Dim strAcquisitionDate As String
    Dim strAcquisDD As String, strAcquisMM As String, strAcquisYY As String
    Dim strAcquisitionType As String
    Dim strAcquiredFrom As String
    
    strAcquisDD = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(acquisitionDay, lookAt:=xlWhole).Column).Value
    If strAcquisDD <> "" And IsNumeric(strAcquisDD) Then
        If strAcquisDD > 31 Or strAcquisDD = 0 Then
            strAcquisDD = ""
        ElseIf strAcquisDD < 10 Then
            strAcquisDD = "0" & strAcquisDD
        End If
    Else:
        strAcquisDD = ""
    End If

    strAcquisMM = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(acquisitionMonth, lookAt:=xlWhole).Column).Value
    If strAcquisMM <> "" And IsNumeric(strAcquisMM) Then
        If strAcquisMM > 12 Or strAcquisMM = 0 Then
            strAcquisMM = ""
        ElseIf strAcquisMM < 10 Then
            strAcquisMM = "0" & strAcquisMM
        End If
    Else:
        strAcquisMM = ""
    End If

    strAcquisYY = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(acquisitionYear, lookAt:=xlWhole).Column).Value
    If strAcquisYY <> "" And IsNumeric(strAcquisYY) Then
        If strAcquisYY > 999 Then
            strAcquisYY = strAcquisYY
        Else:
            strAcquisYY = ""
        End If
    Else:
        strAcquisYY = ""
    End If

    If strAcquisYY <> "" And strAcquisMM <> "" And strAcquisDD <> "" Then
        strAcquisitionDate = strAcquisYY & "-" & strAcquisMM & "-" & strAcquisDD
    ElseIf strAcquisYY <> "" And strAcquisMM <> "" And strAcquisDD = "" Then
        strAcquisitionDate = strAcquisYY & "-" & strAcquisMM
    ElseIf strAcquisYY <> "" And strAcquisMM = "" And strAcquisDD = "" Then
        strAcquisitionDate = strAcquisYY
    Else:
        strAcquisitionDate = ""
    End If
    
    strAcquisitionType = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(acquisitionType, lookAt:=xlWhole).Column).Value
    strAcquiredFrom = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(acquiredFrom, lookAt:=xlWhole).Column).Value
    
    Dim strAccessionNumber As String
    
    strAccessionNumber = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(accessionNumber, lookAt:=xlWhole).Column).Value
    
    Dim strFixation As String
    Dim strConservation As String
    Dim strTypeStatus As String
    
    strTypeStatus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(statusType, lookAt:=xlWhole).Column).Value
    strConservation = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(conservation, lookAt:=xlWhole).Column).Value
    strFixation = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(fixation, lookAt:=xlWhole).Column).Value
    
    Dim strTestFill As String
    strTestFill = strAcquisitionDate & strAcquisitionType & strAcquiredFrom & strAccessionNumber & strTypeStatus & strFixation & strConservation
    
    If Not IsEmpty(strTestFill) And Not IsNull(strTestFill) And strTestFill <> "" Then
    
        Set xmlSpecUnit = dom.createNode(NODE_ELEMENT, "SpecimenUnit", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlSpecUnit
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
        If Not IsEmpty(strAcquisitionDate) And Not IsNull(strAcquisitionDate) And strAcquisitionDate <> "" _
            Or Not IsEmpty(strAcquisitionType) And Not IsNull(strAcquisitionType) And strAcquisitionType <> "" _
            Or Not IsEmpty(strAcquiredFrom) And Not IsNull(strAcquiredFrom) And strAcquiredFrom <> "" Then
    
            Set xmlSpecimenUnitAcquisition = dom.createNode(NODE_ELEMENT, "Acquisition", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitAcquisition
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitAcquisition.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            If Not IsEmpty(strAcquisitionDate) And Not IsNull(strAcquisitionDate) And strAcquisitionDate <> "" Then
                Set xmlSpecimenUnitAcquisitionDate = dom.createNode(NODE_ELEMENT, "AcquisitionDate", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitAcquisitionDate.Text = strAcquisitionDate
                xmlSpecimenUnitAcquisition.appendChild xmlSpecimenUnitAcquisitionDate
                xmlSpecimenUnitAcquisition.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
        
            If Not IsEmpty(strAcquisitionType) And Not IsNull(strAcquisitionType) And strAcquisitionType <> "" Then
                Set xmlSpecimenUnitAcquisitionType = dom.createNode(NODE_ELEMENT, "AcquisitionType", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitAcquisitionType.Text = strAcquisitionType
                xmlSpecimenUnitAcquisition.appendChild xmlSpecimenUnitAcquisitionType
                xmlSpecimenUnitAcquisition.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
    
            If Not IsEmpty(strAcquiredFrom) And Not IsNull(strAcquiredFrom) And strAcquiredFrom <> "" Then
                Set xmlSpecimenUnitAcquiredFrom = dom.createNode(NODE_ELEMENT, "AcquiredFrom", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitAcquisition.appendChild xmlSpecimenUnitAcquiredFrom
                xmlSpecimenUnitAcquisition.appendChild dom.createTextNode(vbCrLf + Space$(12))
                xmlSpecimenUnitAcquiredFrom.appendChild dom.createTextNode(vbCrLf + Space$(14))
                Set xmlSpecimenUnitAcquiredPerson = dom.createNode(NODE_ELEMENT, "Person", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitAcquiredFrom.appendChild xmlSpecimenUnitAcquiredPerson
                xmlSpecimenUnitAcquiredFrom.appendChild dom.createTextNode(vbCrLf + Space$(14))
                xmlSpecimenUnitAcquiredPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
                Set xmlSpecimenUnitAcquiredPersonName = dom.createNode(NODE_ELEMENT, "FullName", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitAcquiredPersonName.Text = strAcquiredFrom
                xmlSpecimenUnitAcquiredPerson.appendChild xmlSpecimenUnitAcquiredPersonName
                xmlSpecimenUnitAcquiredPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
            End If
    
        End If
    
        If Not IsEmpty(strAccessionNumber) And Not IsNull(strAccessionNumber) And strAccessionNumber <> "" Then
            
            Set xmlSpecimenUnitAccession = dom.createNode(NODE_ELEMENT, "Accessions", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitAccession
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitAccession.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
            Set xmlSpecimenUnitAccessionCatalogue = dom.createNode(NODE_ELEMENT, "AccessionCatalogue", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitAccessionCatalogue.Text = "IG Number"
            xmlSpecimenUnitAccession.appendChild xmlSpecimenUnitAccessionCatalogue
            xmlSpecimenUnitAccession.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlSpecimenUnitAccessionNumber = dom.createNode(NODE_ELEMENT, "AccessionNumber", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitAccessionNumber.Text = strAccessionNumber
            xmlSpecimenUnitAccession.appendChild xmlSpecimenUnitAccessionNumber
            xmlSpecimenUnitAccession.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
        End If
    
        If Not IsEmpty(strConservation) And Not IsNull(strConservation) And strConservation <> "" _
        Or Not IsEmpty(strFixation) And Not IsNull(strFixation) And strFixation <> "" Then
    
            Set xmlSpecimenUnitPreparations = dom.createNode(NODE_ELEMENT, "Preparations", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitPreparations
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            If Not IsEmpty(strFixation) And Not IsNull(strFixation) And strFixation <> "" Then
    
                Set xmlSpecimenUnitPreparation = dom.createNode(NODE_ELEMENT, "Preparation", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparations.appendChild xmlSpecimenUnitPreparation
                xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                Set xmlSpecimenUnitPreparationType = dom.createNode(NODE_ELEMENT, "PreparationType", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationType.Text = "Fixation"
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationType
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
                Set xmlSpecimenUnitPreparationMaterials = dom.createNode(NODE_ELEMENT, "PreparationMaterials", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationMaterials.Text = strFixation
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationMaterials
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
            End If
    
            If Not IsEmpty(strConservation) And Not IsNull(strConservation) And strConservation <> "" Then
    
                Set xmlSpecimenUnitPreparation = dom.createNode(NODE_ELEMENT, "Preparation", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparations.appendChild xmlSpecimenUnitPreparation
                xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                Set xmlSpecimenUnitPreparationType = dom.createNode(NODE_ELEMENT, "PreparationType", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationType.Text = "Conservation"
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationType
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                Set xmlSpecimenUnitPreparationMaterials = dom.createNode(NODE_ELEMENT, "PreparationMaterials", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationMaterials.Text = strConservation
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationMaterials
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
            End If
                    
        End If
    
        If Not IsEmpty(strTypeStatus) And Not IsNull(strTypeStatus) And strTypeStatus <> "" Then
        
            Set xmlSpecimenUnitNomenclatureTypes = dom.createNode(NODE_ELEMENT, "NomenclaturalTypeDesignations", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitNomenclatureTypes
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitNomenclatureTypes.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlSpecimenUnitNomenclatureType = dom.createNode(NODE_ELEMENT, "NomenclaturalTypeDesignation", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitNomenclatureTypes.appendChild xmlSpecimenUnitNomenclatureType
            xmlSpecimenUnitNomenclatureTypes.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlSpecimenUnitNomenclatureType.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            Set xmlSpecimenUnitNomenclatureTypeStatus = dom.createNode(NODE_ELEMENT, "TypeStatus", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitNomenclatureTypeStatus.Text = strTypeStatus
            xmlSpecimenUnitNomenclatureType.appendChild xmlSpecimenUnitNomenclatureTypeStatus
            xmlSpecimenUnitNomenclatureType.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    
        End If
    
    End If
    
'Exit_XMLSpecimenUnit:
'
'        Exit Sub
'
'Err_XMLSpecimenUnit:
'
'        MsgBox prompt:="An error occured in sub XMLSpecimenUnit." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLSpecimenUnit"
'        Resume Exit_XMLSpecimenUnit

End Sub

'DataSets/DataSet/Units/Unit/ZoologicalUnit/
Private Sub XMLStage(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)
    
    On Error Resume Next
    
    Dim xmlZoologicalUnit As MSXML2.IXMLDOMElement
    Dim xmlPhasesOrStages As MSXML2.IXMLDOMElement
    Dim xmlPhaseOrStage As MSXML2.IXMLDOMElement

    Dim strStage As String
    
    strStage = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(lifeStage, lookAt:=xlWhole).Column).Value

    If Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(classification, lookAt:=xlWhole).Column).Value = "Zoological" _
        Or Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(classification, lookAt:=xlWhole).Column).Value = "zoological" _
        Or Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(classification, lookAt:=xlWhole).Column).Value = "" Then

        If Not IsEmpty(strStage) And Not IsNull(strStage) And strStage <> "" Then
            
            Set xmlZoologicalUnit = dom.createNode(NODE_ELEMENT, "ZoologicalUnit", "http://www.tdwg.org/schemas/abcd/2.06")
            subnode.appendChild xmlZoologicalUnit
            subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlZoologicalUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
            Set xmlPhasesOrStages = dom.createNode(NODE_ELEMENT, "PhasesOrStages", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlZoologicalUnit.appendChild xmlPhasesOrStages
            xmlZoologicalUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlPhasesOrStages.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
            Set xmlPhaseOrStage = dom.createNode(NODE_ELEMENT, "PhaseOrStage", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlPhaseOrStage.Text = strStage
            xmlPhasesOrStages.appendChild xmlPhaseOrStage
            xmlPhasesOrStages.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
        End If
    
    End If

End Sub

'DataSets/DataSet/Units/Unit/MultiMediaObjects/
Private Sub XMLPicture(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

'    On Error GoTo Err_XMLPicture
    On Error Resume Next
    
    Dim xmlMultimediaObjects As MSXML2.IXMLDOMElement
    Dim xmlMultimediaObject As MSXML2.IXMLDOMElement
    Dim xmlMultimediaURI As MSXML2.IXMLDOMElement
    
    Dim strPicture As String
    
    strPicture = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(urlPicture, lookAt:=xlWhole).Column).Value
    
    If Not IsEmpty(strPicture) And Not IsNull(strPicture) And strPicture <> "" Then
        Set xmlMultimediaObjects = dom.createNode(NODE_ELEMENT, "MultiMediaObjects", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlMultimediaObjects
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlMultimediaObjects.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
         Set xmlMultimediaObject = dom.createNode(NODE_ELEMENT, "MultiMediaObject", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlMultimediaObjects.appendChild xmlMultimediaObject
        xmlMultimediaObjects.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlMultimediaObject.appendChild dom.createTextNode(vbCrLf + Space$(10))
       
        Set xmlMultimediaURI = dom.createNode(NODE_ELEMENT, "FileURI", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlMultimediaURI.Text = strPicture
        xmlMultimediaObject.appendChild xmlMultimediaURI
        xmlMultimediaObject.appendChild dom.createTextNode(vbCrLf + Space$(10))
    End If
    
'Exit_XMLPicture:
'
'        Exit Sub
'
'Err_XMLPicture:
'
'        MsgBox prompt:="An error occured in sub XMLPicture." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLPicture"
'        Resume Exit_XMLPicture
        
End Sub

'DataSets/DataSet/Units/Unit/Associations/
Private Sub XMLAssociation(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLAssociation
    
    Dim xmlAssociations As MSXML2.IXMLDOMElement
    Dim xmlUnitAssociation As MSXML2.IXMLDOMElement
    Dim xmlAssociatedUnitID As MSXML2.IXMLDOMElement
    Dim xmlAssociatedInstitutionID As MSXML2.IXMLDOMElement
    Dim xmlAssociatedSourceID As MSXML2.IXMLDOMElement
    Dim xmlAssociationType As MSXML2.IXMLDOMElement
    
    Dim strAssocInstitution As String
    Dim strAssocColl As String
    Dim strAssocUnit As String
    Dim strAssocType As String
    
    strAssocInstitution = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(associatedUnitInstitution, lookAt:=xlWhole).Column)
    strAssocColl = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(associatedUnitCollection, lookAt:=xlWhole).Column)
    strAssocUnit = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(associatedUnitID, lookAt:=xlWhole).Column).Value
    strAssocType = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(associationType, lookAt:=xlWhole).Column).Value
    
    If Not IsEmpty(strAssocUnit) And Not IsNull(strAssocUnit) And strAssocUnit <> "" Then
    
        Set xmlAssociations = dom.createNode(NODE_ELEMENT, "Associations", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlAssociations
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(8))
        
        Set xmlUnitAssociation = dom.createNode(NODE_ELEMENT, "UnitAssociation", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlAssociations.appendChild xmlUnitAssociation
        xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
        Set xmlAssociatedInstitutionID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceInstitutionCode", "http://www.tdwg.org/schemas/abcd/2.06")
        If Not IsEmpty(strAssocInstitution) And Not IsNull(strAssocInstitution) And strAssocInstitution <> "" Then
            xmlAssociatedInstitutionID.Text = strAssocInstitution
        Else:
            xmlAssociatedInstitutionID.Text = "Not defined"
        End If
        xmlUnitAssociation.appendChild xmlAssociatedInstitutionID
        xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
        Set xmlAssociatedSourceID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceName", "http://www.tdwg.org/schemas/abcd/2.06")
        If Not IsEmpty(strAssocColl) And Not IsNull(strAssocColl) And strAssocColl <> "" Then
            xmlAssociatedSourceID.Text = strAssocColl
        Else:
            xmlAssociatedSourceID.Text = "Not defined"
        End If
        xmlUnitAssociation.appendChild xmlAssociatedSourceID
        xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
        Set xmlAssociatedUnitID = dom.createNode(NODE_ELEMENT, "AssociatedUnitID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlAssociatedUnitID.Text = strAssocUnit
        xmlUnitAssociation.appendChild xmlAssociatedUnitID
        xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
        Set xmlAssociationType = dom.createNode(NODE_ELEMENT, "AssociationType", "http://www.tdwg.org/schemas/abcd/2.06")
        If Not IsEmpty(strAssocType) And Not IsNull(strAssocType) And strAssocType <> "" Then
            xmlAssociationType.Text = strAssocType
        Else:
            xmlAssociationType.Text = "Not defined"
        End If
        xmlUnitAssociation.appendChild xmlAssociationType
        xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
    End If
    
'Exit_XMLAssociation:
'
'        Exit Sub
'
'Err_XMLAssociation:
'
'        MsgBox prompt:="An error occured in sub XMLAssociation." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLAssociation"
'        Resume Exit_XMLAssociation

End Sub

'DataSets/DataSet/Units/Unit/Gathering
Private Sub XMLGather(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLGather
    
    Dim xmlGathering As MSXML2.IXMLDOMElement
    Dim xmlGatheringCode As MSXML2.IXMLDOMElement
    Dim xmlGatheringDateTime As MSXML2.IXMLDOMElement
    Dim xmlGatheringDateTimeBegin As MSXML2.IXMLDOMElement
    Dim xmlGatheringTimeBegin As MSXML2.IXMLDOMElement
    Dim xmlGatheringDateTimeEnd As MSXML2.IXMLDOMElement
    Dim xmlGatheringTimeEnd As MSXML2.IXMLDOMElement
    Dim xmlGatheringAgents As MSXML2.IXMLDOMElement
    Dim xmlGatheringAgent As MSXML2.IXMLDOMElement
    Dim xmlGatheringAgentPerson As MSXML2.IXMLDOMElement
    Dim xmlGatheringAgentFullName As MSXML2.IXMLDOMElement
    Dim xmlGatheringMethod As MSXML2.IXMLDOMElement
    Dim xmlGatheringProject As MSXML2.IXMLDOMElement
    Dim xmlGatheringProjectTitle As MSXML2.IXMLDOMElement
    Dim xmlGatheringLocality As MSXML2.IXMLDOMElement
    Dim xmlGatheringLocalityText As MSXML2.IXMLDOMElement
    Dim xmlGatheringNamedAreas As MSXML2.IXMLDOMElement
    Dim xmlGatheringNamedArea As MSXML2.IXMLDOMElement
    Dim xmlGatheringAreaName As MSXML2.IXMLDOMElement
    Dim xmlGatheringAreaClass As MSXML2.IXMLDOMElement
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
    Dim xmlSiteMeasurementsOrFacts As MSXML2.IXMLDOMElement
    Dim xmlSiteMeasurementOrFact As MSXML2.IXMLDOMElement
    Dim xmlSiteMeasurementOrFactAtomised As MSXML2.IXMLDOMElement
    Dim xmlSiteMeasurementOrFactParameter As MSXML2.IXMLDOMElement
    Dim xmlSiteMeasurementOrFactValue As MSXML2.IXMLDOMElement
    Dim xmlGatheringBiotope As MSXML2.IXMLDOMElement
    Dim xmlGatheringBiotopeText As MSXML2.IXMLDOMElement
    Dim xmlGatheringNotes As MSXML2.IXMLDOMElement
    
    Dim strLocalityCode As String
    Dim strDateStart As String, strDateStartD As String, strDateStartM As String, strDateStartY As String, strDateStartTH As String, strDateStartTM As String, strDateStartT As String
    Dim strDateEnd As String, strDateEndD As String, strDateEndM As String, strDateEndY As String, strDateEndTH As String, strDateEndTM As String, strDateEndT As String
    Dim DateStart As String, DateStartString As String, DateEnd As String, DateEndString As String
    Dim TimeStart As String, TimeStartString As String, TimeEnd As String, TimeEndString As String
    Dim DateStartText As String, DateEndText As String, DateText As String
    Dim strGatheringAgent As String
    Dim strProject As String
    Dim strMethod As String
    Dim strLocalityText As String
    Dim strAreaName As String
    Dim strAreaClass As String
    Dim strLatitude As String, strLongitude As String
    Dim strElevation As String
    Dim strDepth As String
    Dim strProperty As String, strPropertyValue As String
    Dim strBiotope As String
    Dim strGatheringNotes As String
    
    strLocalityCode = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(samplingCode, lookAt:=xlWhole).Column).Value
    
    strDateStartD = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionStartDay, lookAt:=xlWhole).Column).Value
    If strDateStartD <> "" And IsNumeric(strDateStartD) Then
        If strDateStartD > 31 Or strDateStartD = 0 Then
            strDateStartD = ""
        ElseIf strDateStartD < 10 Then
            strDateStartD = "0" & strDateStartD
        End If
    Else:
        strDateStartD = ""
    End If
    
    strDateStartM = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionStartMonth, lookAt:=xlWhole).Column).Value
    If strDateStartM <> "" And IsNumeric(strDateStartM) Then
        If strDateStartM > 12 Or strDateStartM = 0 Then
            strDateStartM = ""
        ElseIf strDateStartM < 10 Then
            strDateStartM = "0" & strDateStartM
        End If
    Else:
        strDateStartM = ""
    End If
    
    strDateStartY = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionStartYear, lookAt:=xlWhole).Column).Value
    If strDateStartY <> "" And IsNumeric(strDateStartY) Then
        If strDateStartY > 999 Then
            strDateStartY = strDateStartY
        Else:
            strDateStartY = ""
        End If
    Else:
        strDateStartY = ""
    End If
    
    If strDateStartY <> "" And strDateStartM <> "" And strDateStartD <> "" Then
        strDateStart = strDateStartY & "-" & strDateStartM & "-" & strDateStartD
    ElseIf strDateStartY <> "" And strDateStartM <> "" And strDateStartD = "" Then
        strDateStart = strDateStartY & "-" & strDateStartM
    ElseIf strDateStartY <> "" And strDateStartM = "" And strDateStartD = "" Then
        strDateStart = strDateStartY
    Else:
        strDateStart = ""
    End If

    strDateStartTH = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionStartTimeH, lookAt:=xlWhole).Column)
    If strDateStartTH <> "" And IsNumeric(strDateStartTH) Then
        If strDateStartTH > 24 Then
            strDateStartTH = ""
        ElseIf strDateStartTH < 10 Then
            If Len(strDateStartTH) = 2 Then
                strDateStartTH = strDateStartTH
            Else:
                strDateStartTH = "0" & strDateStartTH
            End If
        End If
'        If strDateStartTH > 24 Or strDateStartTH = 0 Then
'            strDateStartTH = ""
'        ElseIf strDateStartTH < 10 Then
'            strDateStartTH = "0" & strDateStartTH
'        End If
    Else:
        strDateStartTH = ""
    End If
    
    strDateStartTM = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionStartTimeM, lookAt:=xlWhole).Column)
    If strDateStartTM <> "" And IsNumeric(strDateStartTM) Then
        If strDateStartTM > 59 Then
            strDateStartTM = ""
        ElseIf strDateStartTM < 10 Then
            If Len(strDateStartTM) = 2 Then
                strDateStartTM = strDateStartTM
            Else:
                strDateStartTM = "0" & strDateStartTM
            End If
        End If
'        If strDateStartTM > 59 Or strDateStartTM = 0 Then
'            strDateStartTM = ""
'        ElseIf strDateStartTM < 10 Then
'            strDateStartTM = "0" & strDateStartTM
'        End If
    Else:
        If strDateStartTH <> "" Then
            strDateStartTM = "00"
        Else:
            strDateStartTM = ""
        End If
    End If
    
    If strDateStartTH <> "" And strDateStartTM <> "" Then
        strDateStartT = strDateStartTH & ":" & strDateStartTM & ":00"
    Else:
        strDateStartT = ""
    End If
            
    strDateEndD = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionEndDay, lookAt:=xlWhole).Column).Value
    If strDateEndD <> "" And IsNumeric(strDateEndD) Then
        If strDateEndD > 31 Or strDateEndD = 0 Then
            strDateEndD = ""
        ElseIf strDateEndD < 10 Then
            strDateEndD = "0" & strDateEndD
        End If
    Else:
        strDateEndD = ""
    End If
    
    strDateEndM = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionEndMonth, lookAt:=xlWhole).Column).Value
    If strDateEndM <> "" And IsNumeric(strDateEndM) Then
        If strDateEndM > 12 Or strDateEndM = 0 Then
            strDateEndM = ""
        ElseIf strDateEndM < 10 Then
            strDateEndM = "0" & strDateEndM
        End If
    Else:
        strDateEndM = ""
    End If
    
    strDateEndY = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionEndYear, lookAt:=xlWhole).Column).Value
    If strDateEndY <> "" And IsNumeric(strDateEndY) Then
        If strDateEndY > 999 Then
            strDateEndY = strDateEndY
        Else:
            strDateEndY = ""
        End If
    Else:
        strDateEndY = ""
    End If
    
    If strDateEndY <> "" And strDateEndM <> "" And strDateEndD <> "" Then
        strDateEnd = strDateEndY & "-" & strDateEndM & "-" & strDateEndD
    ElseIf strDateEndY <> "" And strDateEndM <> "" And strDateEndD = "" Then
        strDateEnd = strDateEndY & "-" & strDateEndM
    ElseIf strDateEndY <> "" And strDateEndM = "" And strDateEndD = "" Then
        strDateEnd = strDateEndY
    Else:
        strDateEnd = ""
    End If

    strDateEndTH = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionEndTimeH, lookAt:=xlWhole).Column)
    If strDateEndTH <> "" And IsNumeric(strDateEndTH) Then
        If strDateEndTH > 24 Then
            strDateEndTH = ""
        ElseIf strDateEndTH < 10 Then
            If Len(strDateEndTH) = 2 Then
                strDateEndTH = strDateEndTH
            Else:
                strDateEndTH = "0" & strDateEndTH
            End If
        End If
'        If strDateEndTH > 24 Or strDateEndTH = 0 Then
'            strDateEndTH = ""
'        ElseIf strDateEndTH < 10 Then
'            strDateEndTH = "0" & strDateEndTH
'        End If
    Else:
        strDateEndTH = ""
    End If
    
    strDateEndTM = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectionEndTimeM, lookAt:=xlWhole).Column)
    If strDateEndTM <> "" And IsNumeric(strDateEndTM) Then
        If strDateEndTM > 59 Then
            strDateEndTM = ""
        ElseIf strDateEndTM < 10 Then
            If Len(strDateEndTM) = 2 Then
                strDateEndTM = strDateEndTM
            Else:
                strDateEndTM = "0" & strDateEndTM
            End If
        End If
'        If strDateEndTM > 59 Or strDateEndTM = 0 Then
'            strDateEndTM = ""
'        ElseIf strDateEndTM < 10 Then
'            strDateEndTM = "0" & strDateEndTM
'        End If
    Else:
        If strDateEndTH <> "" Then
            strDateEndTM = "00"
        Else:
            strDateEndTM = ""
        End If
    End If
    
    If strDateEndTH <> "" And strDateEndTM <> "" Then
        strDateEndT = strDateEndTH & ":" & strDateEndTM & ":00"
    Else:
        strDateEndT = ""
    End If
    
    strGatheringAgent = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectedBy, lookAt:=xlWhole).Column).Value
    
    strProject = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(expedition_project, lookAt:=xlWhole).Column)
    
    strMethod = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(samplingMethod, lookAt:=xlWhole).Column)
    
    strLocalityText = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(exactSite, lookAt:=xlWhole).Column)
    
    Dim Area(16)
        Area(1) = ocean
        Area(2) = continent
        Area(3) = sea
        Area(4) = country
        Area(5) = state_territory
        Area(6) = province
        Area(7) = region
        Area(8) = archipelago
        Area(9) = district
        Area(10) = county
        Area(11) = department
        Area(12) = island
        Area(13) = city
        Area(14) = municipality
        Area(15) = populatedPlace
        Area(16) = naturalSite

    Dim AreaName(16)
        AreaName(1) = "Ocean"
        AreaName(2) = "Continent"
        AreaName(3) = "Sea"
        AreaName(4) = "Country"
        AreaName(5) = "State or territory"
        AreaName(6) = "Province"
        AreaName(7) = "Region"
        AreaName(8) = "Archipelago"
        AreaName(9) = "District"
        AreaName(10) = "County"
        AreaName(11) = "Department"
        AreaName(12) = "Island"
        AreaName(13) = "City"
        AreaName(14) = "Municipality"
        AreaName(15) = "Populated place"
        AreaName(16) = "Natural site"

    Dim i As Integer
    Dim celval As String, celval2 As String
    Dim rep As String, sep As String
    rep = ""
    sep = " "
    
    For i = 1 To 16
        celval = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(Area(i), lookAt:=xlWhole).Column).Value
        If Not IsEmpty(celval) And Not IsNull(celval) And celval <> "" Then
            rep = rep & celval & sep
        End If
        celval = ""
    Next i
    
    Dim Latit, Longit, LatitudeDMS As Double, LongitudeDMS As Double
    Latit = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(latitude, lookAt:=xlWhole).Column)
    Longit = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(longitude, lookAt:=xlWhole).Column)
    LatitudeDMS = 1000
    LongitudeDMS = 1000
    If (Not IsEmpty(Latit) And Not IsNull(Latit) And Latit <> "") _
        Or (Not IsEmpty(Longit) And Not IsNull(Longit) And Longit <> "") Then
            If IsNumeric(Latit) = True And -90 <= Latit And Latit <= 90 Then
                LatitudeDMS = Round(Latit, 5)
            Else:
                Latit = ConvertDMSToDecimal(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(latitude, lookAt:=xlWhole).Column), True, rowCounter, False)
                If Not IsNull(Latit) And Latit <> "" Then
                    LatitudeDMS = Round(Latit, 5)
                Else:
                    LatitudeDMS = ""
                End If
            End If
            If IsNumeric(Longit) = True And -180 <= Longit And Longit <= 180 Then
                LongitudeDMS = Round(Longit, 5)
            Else:
                Longit = ConvertDMSToDecimal(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(longitude, lookAt:=xlWhole).Column), False, rowCounter, False)
                If Not IsNull(Longit) And Longit <> "" Then
                    LongitudeDMS = Round(Longit, 5)
                Else:
                    LongitudeDMS = ""
                End If
            End If
    End If
    
    strElevation = Trim(Replace(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(elevationInMeters, lookAt:=xlWhole).Column).Value, "m", ""))
    strDepth = Trim(Replace(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(depthInMeters, lookAt:=xlWhole).Column).Value, "m", ""))
    
    Dim SiteProperty(10)
        SiteProperty(1) = siteProperty_1
        SiteProperty(2) = siteProperty_2
        SiteProperty(3) = siteProperty_3
        SiteProperty(4) = siteProperty_4
        SiteProperty(5) = siteProperty_5
        SiteProperty(6) = siteProperty_6
        SiteProperty(7) = siteProperty_7
        SiteProperty(8) = siteProperty_8
        SiteProperty(9) = siteProperty_9
        SiteProperty(10) = siteProperty_10

    Dim SitePropertyValue(10)
        SitePropertyValue(1) = sitePropertyValue_1
        SitePropertyValue(2) = sitePropertyValue_2
        SitePropertyValue(3) = sitePropertyValue_3
        SitePropertyValue(4) = sitePropertyValue_4
        SitePropertyValue(5) = sitePropertyValue_5
        SitePropertyValue(6) = sitePropertyValue_6
        SitePropertyValue(7) = sitePropertyValue_7
        SitePropertyValue(8) = sitePropertyValue_8
        SitePropertyValue(9) = sitePropertyValue_9
        SitePropertyValue(10) = sitePropertyValue_10
    
    Dim j As Integer
    Dim rep2 As String
    rep2 = ""
    
    'Si le paramètre de la propriété n'est pas rempli, la valeur ne sera pas présente
    For i = 1 To 10
        celval = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(SiteProperty(i), lookAt:=xlWhole).Column)
        celval2 = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(SitePropertyValue(i), lookAt:=xlWhole).Column)
        If (Not IsEmpty(celval) And Not IsNull(celval) And celval <> "") _
            And (Not IsEmpty(celval2) And Not IsNull(celval2) And celval2 <> "") Then
            rep2 = rep2 & celval & sep
        End If
        celval = ""
        celval2 = ""
    Next i
    
    strBiotope = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(ecology, lookAt:=xlWhole).Column).Value
    
    strGatheringNotes = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(localityNotes, lookAt:=xlWhole).Column).Value
    
    Dim strTestFill As String, strTestFill2 As String
    strTestFill = strLocalityCode & strDateStart & strDateStartT & strDateEnd & strDateEndT & DateText & strGatheringAgent & strProject & strMethod & strLocalityText & rep _
    & strLatitude & strLongitude & strElevation & strDepth & rep2 & strBiotope & strGatheringNotes
    strTestFill2 = strLocalityCode & strDateStart & strDateStartT & strDateEnd & strDateEndT & strGatheringAgent & strLocalityText & rep & strLatitude & strLongitude & strElevation & strDepth
    
    If Not IsEmpty(strTestFill) And Not IsNull(strTestFill) And strTestFill <> "" _
        Or (LatitudeDMS <> 1000 And LongitudeDMS <> 1000) Then

        Set xmlGathering = dom.createNode(NODE_ELEMENT, "Gathering", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlGathering
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
        
        If Not IsEmpty(strLocalityCode) And Not IsNull(strLocalityCode) And strLocalityCode <> "" Then
        
            Set xmlGatheringCode = dom.createNode(NODE_ELEMENT, "Code", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringCode.Text = strLocalityCode
            xmlGathering.appendChild xmlGatheringCode
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
        End If
      
        If Not IsEmpty(strDateStart) And Not IsNull(strDateStart) And strDateStart <> "" _
            Or Not IsEmpty(strDateEnd) And Not IsNull(strDateEnd) And strDateEnd <> "" _
            Or Not IsEmpty(strDateStartT) And Not IsNull(strDateStartT) And strDateStartT <> "" _
            Or Not IsEmpty(strDateEndT) And Not IsNull(strDateEndT) And strDateEndT <> "" Then
        
            Set xmlGatheringDateTime = dom.createNode(NODE_ELEMENT, "DateTime", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlGatheringDateTime
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlGatheringDateTime.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            If Not IsEmpty(strDateStart) And Not IsNull(strDateStart) And strDateStart <> "" Then
        
                Set xmlGatheringDateTimeBegin = dom.createNode(NODE_ELEMENT, "ISODateTimeBegin", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlGatheringDateTimeBegin.Text = strDateStart
                xmlGatheringDateTime.appendChild xmlGatheringDateTimeBegin
                xmlGatheringDateTime.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            End If
            
            If Not IsEmpty(strDateStartT) And Not IsNull(strDateStartT) And strDateStartT <> "" Then
        
                Set xmlGatheringTimeBegin = dom.createNode(NODE_ELEMENT, "TimeOfDayBegin", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlGatheringTimeBegin.Text = strDateStartT
                xmlGatheringDateTime.appendChild xmlGatheringTimeBegin
                xmlGatheringDateTime.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            End If
                
            If Not IsEmpty(strDateEnd) And Not IsNull(strDateEnd) And strDateEnd <> "" Then
        
                Set xmlGatheringDateTimeEnd = dom.createNode(NODE_ELEMENT, "ISODateTimeEnd", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlGatheringDateTimeEnd.Text = strDateEnd
                xmlGatheringDateTime.appendChild xmlGatheringDateTimeEnd
                xmlGatheringDateTime.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            End If
                
            If Not IsEmpty(strDateEndT) And Not IsNull(strDateEndT) And strDateEndT <> "" Then
        
                Set xmlGatheringTimeEnd = dom.createNode(NODE_ELEMENT, "TimeOfDayEnd", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlGatheringTimeEnd.Text = strDateEndT
                xmlGatheringDateTime.appendChild xmlGatheringTimeEnd
                xmlGatheringDateTime.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            End If
                
        End If
    
        If Not IsEmpty(strGatheringAgent) And Not IsNull(strGatheringAgent) And strGatheringAgent <> "" Then
            
            Set xmlGatheringAgents = dom.createNode(NODE_ELEMENT, "Agents", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlGatheringAgents
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlGatheringAgents.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlGatheringAgent = dom.createNode(NODE_ELEMENT, "GatheringAgent", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringAgents.appendChild xmlGatheringAgent
            xmlGatheringAgents.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlGatheringAgent.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            Set xmlGatheringAgentPerson = dom.createNode(NODE_ELEMENT, "Person", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringAgent.appendChild xmlGatheringAgentPerson
            xmlGatheringAgent.appendChild dom.createTextNode(vbCrLf + Space$(14))
            xmlGatheringAgentPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
            
            Set xmlGatheringAgentFullName = dom.createNode(NODE_ELEMENT, "FullName", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringAgentFullName.Text = strGatheringAgent
            xmlGatheringAgentPerson.appendChild xmlGatheringAgentFullName
            xmlGatheringAgentPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
        
        End If
          
        If Not IsEmpty(strProject) And Not IsNull(strProject) And strProject <> "" Then
        
            Set xmlGatheringProject = dom.createNode(NODE_ELEMENT, "Project", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlGatheringProject
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlGatheringProject.appendChild dom.createTextNode(vbCrLf + Space$(10))
            
            Set xmlGatheringProjectTitle = dom.createNode(NODE_ELEMENT, "ProjectTitle", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringProjectTitle.Text = strProject
            xmlGatheringProject.appendChild xmlGatheringProjectTitle
            xmlGatheringProject.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
        End If
        
        If Not IsEmpty(strMethod) And Not IsNull(strMethod) And strMethod <> "" Then
            
            Set xmlGatheringMethod = dom.createNode(NODE_ELEMENT, "Method", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringMethod.Text = strMethod
            xmlGathering.appendChild xmlGatheringMethod
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
            
        End If
        
        If Not IsEmpty(strLocalityText) And Not IsNull(strLocalityText) And strLocalityText <> "" Then
        
            Set xmlGatheringLocality = dom.createNode(NODE_ELEMENT, "LocalityText", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringLocality.Text = strLocalityText
            xmlGathering.appendChild xmlGatheringLocality
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
            
        End If
               
        If (Not IsEmpty(rep) And Not IsNull(rep) And rep <> "") Then
            
            Set xmlGatheringNamedAreas = dom.createNode(NODE_ELEMENT, "NamedAreas", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlGatheringNamedAreas
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlGatheringNamedAreas.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
            For i = 1 To 16
        
                strAreaName = ""
                strAreaClass = ""
                strAreaName = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(what:=Area(i), lookAt:=xlWhole).Column)
                strAreaClass = AreaName(i)
                
                If (Not IsEmpty(strAreaName) And Not IsNull(strAreaName) And strAreaName <> "") Then
                    
                    Set xmlGatheringNamedArea = dom.createNode(NODE_ELEMENT, "NamedArea", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlGatheringNamedAreas.appendChild xmlGatheringNamedArea
                    xmlGatheringNamedAreas.appendChild dom.createTextNode(vbCrLf + Space$(12))
                    xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    
                    Set xmlGatheringAreaClass = dom.createNode(NODE_ELEMENT, "AreaClass", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlGatheringAreaClass.Text = strAreaClass
                    xmlGatheringNamedArea.appendChild xmlGatheringAreaClass
                    xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    
                    Set xmlGatheringAreaName = dom.createNode(NODE_ELEMENT, "AreaName", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlGatheringAreaName.Text = strAreaName
                    xmlGatheringNamedArea.appendChild xmlGatheringAreaName
                    xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                End If
            
            Next i
        
        End If
        
        If LongitudeDMS <> 1000 And LatitudeDMS <> 1000 Then
            
            Set xmlGatheringSiteCoordSets = dom.createNode(NODE_ELEMENT, "SiteCoordinateSets", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlGatheringSiteCoordSets
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlGatheringSiteCoordSets.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlGatheringSiteCoord = dom.createNode(NODE_ELEMENT, "SiteCoordinates", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringSiteCoordSets.appendChild xmlGatheringSiteCoord
            xmlGatheringSiteCoordSets.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlGatheringSiteCoord.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            Set xmlGatheringCoordLatLong = dom.createNode(NODE_ELEMENT, "CoordinatesLatLong", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringSiteCoord.appendChild xmlGatheringCoordLatLong
            xmlGatheringSiteCoord.appendChild dom.createTextNode(vbCrLf + Space$(14))
            xmlGatheringCoordLatLong.appendChild dom.createTextNode(vbCrLf + Space$(16))
            
            Set xmlGatheringCoordLongDec = dom.createNode(NODE_ELEMENT, "LongitudeDecimal", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringCoordLongDec.Text = Replace(CStr(LongitudeDMS), ",", ".")
            xmlGatheringCoordLatLong.appendChild xmlGatheringCoordLongDec
            xmlGatheringCoordLatLong.appendChild dom.createTextNode(vbCrLf + Space$(16))
            xmlGatheringCoordLongDec.appendChild dom.createTextNode(vbCrLf + Space$(18))

            Set xmlGatheringCoordLatDec = dom.createNode(NODE_ELEMENT, "LatitudeDecimal", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringCoordLatDec.Text = Replace(CStr(LatitudeDMS), ",", ".")
            xmlGatheringCoordLatLong.appendChild xmlGatheringCoordLatDec
            xmlGatheringCoordLatLong.appendChild dom.createTextNode(vbCrLf + Space$(16))
            xmlGatheringCoordLatDec.appendChild dom.createTextNode(vbCrLf + Space$(18))
            
        End If
        
        If Not IsEmpty(strElevation) And Not IsNull(strElevation) And strElevation <> "" Then
        
            Set xmlGatheringElevation = dom.createNode(NODE_ELEMENT, "Altitude", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlGatheringElevation
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlGatheringElevation.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
            Set xmlGatheringElevationMeasure = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringElevation.appendChild xmlGatheringElevationMeasure
            xmlGatheringElevation.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlGatheringElevationMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlGatheringElevationValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringElevationValue.Text = strElevation
            xmlGatheringElevationMeasure.appendChild xmlGatheringElevationValue
            xmlGatheringElevationMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlGatheringElevationUnit = dom.createNode(NODE_ELEMENT, "UnitOfMeasurement", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringElevationUnit.Text = "m"
            xmlGatheringElevationMeasure.appendChild xmlGatheringElevationUnit
            xmlGatheringElevationMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
        End If
            
        If Not IsEmpty(strDepth) And Not IsNull(strDepth) And strDepth <> "" Then
        
            Set xmlGatheringDepth = dom.createNode(NODE_ELEMENT, "Depth", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlGatheringDepth
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlGatheringDepth.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
            Set xmlGatheringDepthMeasure = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringDepth.appendChild xmlGatheringDepthMeasure
            xmlGatheringDepth.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlGatheringDepthMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlGatheringDepthValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringDepthValue.Text = strDepth
            xmlGatheringDepthMeasure.appendChild xmlGatheringDepthValue
            xmlGatheringDepthMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlGatheringDepthUnit = dom.createNode(NODE_ELEMENT, "UnitOfMeasurement", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringDepthUnit.Text = "m"
            xmlGatheringDepthMeasure.appendChild xmlGatheringDepthUnit
            xmlGatheringDepthMeasure.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
        End If
        
        If Not IsEmpty(rep2) And Not IsNull(rep2) And rep2 <> "" Then
    
            Set xmlSiteMeasurementsOrFacts = dom.createNode(NODE_ELEMENT, "SiteMeasurementsOrFacts", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlSiteMeasurementsOrFacts
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlSiteMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(10))
            
            For i = 1 To 10
                
                strProperty = ""
                strPropertyValue = ""
                strProperty = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(what:=SiteProperty(i), lookAt:=xlWhole).Column).Value
                strPropertyValue = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(what:=SitePropertyValue(i), lookAt:=xlWhole).Column).Value
                
                'Paramètre et valeur doivent être remplis
                If strProperty <> "" And strPropertyValue <> "" Then
                
                    Set xmlSiteMeasurementOrFact = dom.createNode(NODE_ELEMENT, "SiteMeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSiteMeasurementsOrFacts.appendChild xmlSiteMeasurementOrFact
                    xmlSiteMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(10))
                    xmlSiteMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                    Set xmlSiteMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSiteMeasurementOrFact.appendChild xmlSiteMeasurementOrFactAtomised
                    xmlSiteMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(12))
                    xmlSiteMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                    Set xmlSiteMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSiteMeasurementOrFactParameter.Text = strProperty
                    xmlSiteMeasurementOrFactAtomised.appendChild xmlSiteMeasurementOrFactParameter
                    xmlSiteMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    
                    Set xmlSiteMeasurementOrFactValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlSiteMeasurementOrFactValue.Text = strPropertyValue
                    xmlSiteMeasurementOrFactAtomised.appendChild xmlSiteMeasurementOrFactValue
                    xmlSiteMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                End If
            
            Next i
    
        End If
        
        If Not IsEmpty(strBiotope) And Not IsNull(strBiotope) And strBiotope <> "" Then
            
            Set xmlGatheringBiotope = dom.createNode(NODE_ELEMENT, "Biotope", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGathering.appendChild xmlGatheringBiotope
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlGatheringBiotope.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlGatheringBiotopeText = dom.createNode(NODE_ELEMENT, "Text", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringBiotopeText.Text = strBiotope
            xmlGatheringBiotope.appendChild xmlGatheringBiotopeText
            xmlGatheringBiotope.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        End If
        
        If Not IsEmpty(strGatheringNotes) And Not IsNull(strGatheringNotes) And strGatheringNotes <> "" Then
            Set xmlGatheringNotes = dom.createNode(NODE_ELEMENT, "Notes", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringNotes.Text = strGatheringNotes
            xmlGathering.appendChild xmlGatheringNotes
            xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(10))
        End If
    
    End If

'Exit_XMLGather:
'
'        Exit Sub
'
'Err_XMLGather:
'
'        MsgBox prompt:="An error occured in sub XMLGather." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLGather"
'        Resume Exit_XMLGather

End Sub

'DataSets/DataSet/Units/Unit/MeasurementsOrFacts
Private Sub XMLMeasurements(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)
    
    On Error Resume Next
'    On Error GoTo Err_XMLMeasurements
    
    Dim xmlMeasurementsOrFacts As MSXML2.IXMLDOMElement
    Dim xmlMeasurementOrFact As MSXML2.IXMLDOMElement
    Dim xmlMeasurementOrFactAtomised As MSXML2.IXMLDOMElement
    Dim xmlMeasurementOrFactParameter As MSXML2.IXMLDOMElement
    Dim xmlMeasurementOrFactLowerValue As MSXML2.IXMLDOMElement
    
    Dim strSocialStatus As String
    strSocialStatus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(socialStatus, lookAt:=xlWhole).Column).Value
    Dim strTotalCount As String, strMaleCount As String, strFemaleCount As String, strSexUNCount As String
    strTotalCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(totalNumber, lookAt:=xlWhole).Column).Value
    strMaleCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(maleCount, lookAt:=xlWhole).Column).Value
    strFemaleCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(femaleCount, lookAt:=xlWhole).Column).Value
    strSexUNCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(sexUnknownCount, lookAt:=xlWhole).Column).Value
    Dim strTestCount As String
    strTestCount = strSocialStatus & strTotalCount & strMaleCount & strFemaleCount & strSexUNCount
    
    Dim strProperty As String, strPropertyValue As String
    Dim specimenProperty(20)
        specimenProperty(1) = specimenProperty_1
        specimenProperty(2) = specimenProperty_2
        specimenProperty(3) = specimenProperty_3
        specimenProperty(4) = specimenProperty_4
        specimenProperty(5) = specimenProperty_5
        specimenProperty(6) = specimenProperty_6
        specimenProperty(7) = specimenProperty_7
        specimenProperty(8) = specimenProperty_8
        specimenProperty(9) = specimenProperty_9
        specimenProperty(10) = specimenProperty_10
        specimenProperty(11) = specimenProperty_11
        specimenProperty(12) = specimenProperty_12
        specimenProperty(13) = specimenProperty_13
        specimenProperty(14) = specimenProperty_14
        specimenProperty(15) = specimenProperty_15
        specimenProperty(16) = specimenProperty_16
        specimenProperty(17) = specimenProperty_17
        specimenProperty(18) = specimenProperty_18
        specimenProperty(19) = specimenProperty_19
        specimenProperty(20) = specimenProperty_20
    
    
    Dim specimenPropertyValue(20)
        specimenPropertyValue(1) = specimenPropertyValue_1
        specimenPropertyValue(2) = specimenPropertyValue_2
        specimenPropertyValue(3) = specimenPropertyValue_3
        specimenPropertyValue(4) = specimenPropertyValue_4
        specimenPropertyValue(5) = specimenPropertyValue_5
        specimenPropertyValue(6) = specimenPropertyValue_6
        specimenPropertyValue(7) = specimenPropertyValue_7
        specimenPropertyValue(8) = specimenPropertyValue_8
        specimenPropertyValue(9) = specimenPropertyValue_9
        specimenPropertyValue(10) = specimenPropertyValue_10
        specimenPropertyValue(11) = specimenPropertyValue_11
        specimenPropertyValue(12) = specimenPropertyValue_12
        specimenPropertyValue(13) = specimenPropertyValue_13
        specimenPropertyValue(14) = specimenPropertyValue_14
        specimenPropertyValue(15) = specimenPropertyValue_15
        specimenPropertyValue(16) = specimenPropertyValue_16
        specimenPropertyValue(17) = specimenPropertyValue_17
        specimenPropertyValue(18) = specimenPropertyValue_18
        specimenPropertyValue(19) = specimenPropertyValue_19
        specimenPropertyValue(20) = specimenPropertyValue_20
        
    Dim i As Integer, j As Integer
    Dim celval As String, celval2 As String
    Dim rep As String
    rep = ""
    
    'Si le paramètre de la propriété n'est pas rempli, la valeur ne sera pas présente
    For i = 1 To 20
        celval = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(specimenProperty(i), lookAt:=xlWhole).Column)
        celval2 = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(specimenPropertyValue(i), lookAt:=xlWhole).Column)
        If (Not IsEmpty(celval) And Not IsNull(celval) And celval <> "") _
            And (Not IsEmpty(celval2) And Not IsNull(celval2) And celval2 <> "") Then
            rep = rep & celval
        End If
        celval = ""
        celval2 = ""
    Next i
    
    Dim strHostClassis As String, strHostOrdo As String, strHostFamilia As String, strHostGenus As String, strHostSpecies As String, strHostAuthor As String
    Dim strHostName As String, strHostRemark As String
    strHostClassis = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(hostClassis, lookAt:=xlWhole).Column).Value
    strHostOrdo = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(hostOrdo, lookAt:=xlWhole).Column).Value
    strHostFamilia = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(hostFamilia, lookAt:=xlWhole).Column).Value
    strHostGenus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(hostGenus, lookAt:=xlWhole).Column).Value
    strHostSpecies = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(hostSpecies, lookAt:=xlWhole).Column).Value
    strHostAuthor = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(hostAuthor_year, lookAt:=xlWhole).Column).Value
    strHostName = Trim(strHostGenus & " " & strHostSpecies & " " & strHostAuthor)
    strHostRemark = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(hostRemark, lookAt:=xlWhole).Column).Value
    Dim strTestHost As String
    strTestHost = strHostClassis & strHostOrdo & strHostFamilia & strHostGenus & strHostSpecies & strHostAuthor & strHostRemark
    
    If Not IsEmpty(strTestCount) And Not IsNull(strTestCount) And strTestCount <> "" _
        Or Not IsEmpty(rep) And Not IsNull(rep) And rep <> "" _
        Or Not IsEmpty(strTestHost) And Not IsNull(strTestHost) And strTestHost <> "" Then
    
            Set xmlMeasurementsOrFacts = dom.createNode(NODE_ELEMENT, "MeasurementsOrFacts", "http://www.tdwg.org/schemas/abcd/2.06")
            subnode.appendChild xmlMeasurementsOrFacts
            subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
            xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
    
            If Not IsEmpty(strTestCount) And Not IsNull(strTestCount) And strTestCount <> "" Then
            
                If Not IsEmpty(strSocialStatus) And Not IsNull(strSocialStatus) And strSocialStatus <> "" Then
        
                    Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                    xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))

                    Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))

                    Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactParameter.Text = "Social status"
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
                    Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactLowerValue.Text = strSocialStatus
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                End If
                
                If Not IsEmpty(strTotalCount) And Not IsNull(strTotalCount) And strTotalCount <> "" Then
    
                    Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                    xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))

                    Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))

                    Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactParameter.Text = "N total"
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
                    Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactLowerValue.Text = strTotalCount
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                End If

                If Not IsEmpty(strMaleCount) And Not IsNull(strMaleCount) And strMaleCount <> "" Then
    
                    Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                    xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))

                    Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))

                    Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactParameter.Text = "N males"
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
                    Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactLowerValue.Text = strMaleCount
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                End If

                If Not IsEmpty(strFemaleCount) And Not IsNull(strFemaleCount) And strFemaleCount <> "" Then
    
                    Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                    xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))

                    Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))

                    Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactParameter.Text = "N females"
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
                    Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactLowerValue.Text = strFemaleCount
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                End If

                If Not IsEmpty(strSexUNCount) And Not IsNull(strSexUNCount) And strSexUNCount <> "" Then
    
                    Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                    xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))

                    Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))

                    Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactParameter.Text = "N sex unknown"
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
                    Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactLowerValue.Text = strSexUNCount
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                End If
        
            End If
    
            If Not IsEmpty(rep) And Not IsNull(rep) And rep <> "" Then
    
                For i = 1 To 20
        
                    strProperty = ""
                    strPropertyValue = ""
                    strProperty = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(what:=specimenProperty(i), lookAt:=xlWhole).Column).Value
                    strPropertyValue = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(what:=specimenPropertyValue(i), lookAt:=xlWhole).Column).Value
        
                    If strProperty <> "" And strPropertyValue <> "" Then
    
                        Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                        xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                        Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
                        Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactParameter.Text = strProperty
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
                        Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactLowerValue.Text = strPropertyValue
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                    End If
        
                Next i
            
            End If
            
            If Not IsEmpty(strTestHost) And Not IsNull(strTestHost) And strTestHost <> "" Then
            
                    If Not IsEmpty(strHostClassis) And Not IsNull(strHostClassis) And strHostClassis <> "" Then
        
                        Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                        xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                        Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                        Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactParameter.Text = "Host - Class"
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                        Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactLowerValue.Text = strHostClassis
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                    
                    End If
                    
                    If Not IsEmpty(strHostOrdo) And Not IsNull(strHostOrdo) And strHostOrdo <> "" Then
        
                        Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                        xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                        Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                        Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactParameter.Text = "Host - Order"
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                        Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactLowerValue.Text = strHostOrdo
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                    
                    End If
    
                    If Not IsEmpty(strHostFamilia) And Not IsNull(strHostFamilia) And strHostFamilia <> "" Then
        
                        Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                        xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                        Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                        Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactParameter.Text = "Host - Family"
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                        Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactLowerValue.Text = strHostFamilia
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                    
                    End If
                    
                    If Not IsEmpty(strHostName) And Not IsNull(strHostName) And strHostName <> "" Then
                        
                        Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                        xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                        Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                        Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactParameter.Text = "Host - Taxon name"
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                        Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactLowerValue.Text = Trim(strHostName)
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                    
                    End If
    
                    If Not IsEmpty(strHostRemark) And Not IsNull(strHostRemark) And strHostRemark <> "" Then
    
                        Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                        xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                        Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                        xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                        Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactParameter.Text = "Host - Remark"
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                        Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                        xmlMeasurementOrFactLowerValue.Text = strHostRemark
                        xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                        xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                    
                    End If
            
            End If
    
        End If
    
'Exit_XMLMeasurements:
'
'        Exit Sub
'
'Err_XMLMeasurements:
'
'        MsgBox prompt:="An error occured in sub XMLMeasurements." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLMeasurements"
'        Resume Exit_XMLMeasurements

End Sub

'DataSets/DataSet/Units/Unit/Sex
Private Sub XMLSex(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLSex
    
    Dim xmlSexe As MSXML2.IXMLDOMElement
    
    Dim strSex As String
    strSex = UCase(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(sex, lookAt:=xlWhole).Column).Value)
    
    If Not IsEmpty(strSex) And Not IsNull(strSex) And strSex <> "" _
        And (strSex = "M" Or strSex = "F" Or strSex = "U" Or strSex = "N" Or strSex = "X" Or strSex = "m" Or strSex = "f" Or strSex = "u" Or strSex = "n" Or strSex = "x") Then
        Set xmlSexe = dom.createNode(NODE_ELEMENT, "Sex", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSexe.Text = strSex
        subnode.appendChild xmlSexe
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    End If
    
'Exit_XMLSex:
'
'        Exit Sub
'
'Err_XMLSex:
'
'        MsgBox prompt:="An error occured in sub XMLSex." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLSex"
'        Resume Exit_XMLSex

End Sub

'DataSets/DataSet/Units/Unit/Notes
Private Sub XMLNotes(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLSpecNotes

    Dim xmlUnitNotes As MSXML2.IXMLDOMElement
    
    Dim strComment As String
    
    strComment = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(notes, lookAt:=xlWhole).Column)
    
    If Not IsEmpty(strComment) And Not IsNull(strComment) And strComment <> "" Then
        Set xmlUnitNotes = dom.createNode(NODE_ELEMENT, "Notes", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlUnitNotes.Text = strComment
        subnode.appendChild xmlUnitNotes
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    End If

'Exit_XMLSpecNotes:
'
'        Exit Sub
'
'Err_XMLSpecNotes:
'
'        MsgBox prompt:="An error occured in sub XMLSpecNotes." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLSpecNotes"
'        Resume Exit_XMLSpecNotes

End Sub

'DataSets/DataSet/RECORDS/Unit/RecordURI
Private Sub XMLRecordURI(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next

    Dim xmlUnitRecordURI As MSXML2.IXMLDOMElement
    
    Dim strExternalLink As String
    
    strExternalLink = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(externalLink, lookAt:=xlWhole).Column)
    
    If Not IsEmpty(strExternalLink) And Not IsNull(strExternalLink) And strExternalLink <> "" Then
        Set xmlUnitRecordURI = dom.createNode(NODE_ELEMENT, "RecordURI", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlUnitRecordURI.Text = strExternalLink
        subnode.appendChild xmlUnitRecordURI
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    End If

End Sub

'DataSets/DataSet/Units/Unit/UnitExtension => storage.xsd
Private Sub XMLExtensionStorage(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLExtensionStorage
    
    Dim attrStorage As MSXML2.IXMLDOMAttribute
    Dim xmlUnitExtension As MSXML2.IXMLDOMElement
    Dim xmlStorage As MSXML2.IXMLDOMElement
    Dim xmlLocalisation As MSXML2.IXMLDOMElement
    Dim xmlInstitution As MSXML2.IXMLDOMElement
    Dim xmlBuilding As MSXML2.IXMLDOMElement
    Dim xmlFloor As MSXML2.IXMLDOMElement
    Dim xmlRoom As MSXML2.IXMLDOMElement
    Dim xmlColumn As MSXML2.IXMLDOMElement
    Dim xmlRow As MSXML2.IXMLDOMElement
    Dim xmlShelf As MSXML2.IXMLDOMElement
'    Dim xmlBox As MSXML2.IXMLDOMElement
'    Dim xmlTube As MSXML2.IXMLDOMElement
    Dim xmlContainer As MSXML2.IXMLDOMElement
    Dim xmlContainerName As MSXML2.IXMLDOMElement
    Dim xmlContainerType As MSXML2.IXMLDOMElement
    Dim xmlContainerStorage As MSXML2.IXMLDOMElement
    Dim xmlSubcontainerName As MSXML2.IXMLDOMElement
    Dim xmlSubcontainerType As MSXML2.IXMLDOMElement
    Dim xmlSubcontainerStorage As MSXML2.IXMLDOMElement
    Dim xmlBarcode As MSXML2.IXMLDOMElement
    Dim xmlCodes As MSXML2.IXMLDOMElement
    Dim xmlCode As MSXML2.IXMLDOMElement
    Dim xmlCodeType As MSXML2.IXMLDOMElement
    Dim xmlCodeValue As MSXML2.IXMLDOMElement
    
    Dim strInstitution As String
    Dim strBuilding As String
    Dim strFloor As String
    Dim strRoom As String
    Dim strRow As String
    Dim strColumn As String
    Dim strShelf As String
    Dim strContainer As String
    Dim strContainerType As String
    Dim strContainerStorage As String
    Dim strSubcontainer As String
    Dim strSubcontainerType As String
    Dim strSubcontainerStorage As String
'    Dim strBox As String
'    Dim strTube As String
    Dim strBarcode As String
    
    strInstitution = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(institutionStorage, lookAt:=xlWhole).Column)
    strBuilding = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(buildingStorage, lookAt:=xlWhole).Column)
    strFloor = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(floorStorage, lookAt:=xlWhole).Column)
    strRoom = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(roomStorage, lookAt:=xlWhole).Column)
    strRow = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(laneStorage, lookAt:=xlWhole).Column)
    strColumn = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(columnStorage, lookAt:=xlWhole).Column)
    strShelf = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(shelfStorage, lookAt:=xlWhole).Column)
    
    strContainer = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(container, lookAt:=xlWhole).Column)
    strContainerType = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(containerType, lookAt:=xlWhole).Column)
    strContainerStorage = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(containerStorage, lookAt:=xlWhole).Column)
    strSubcontainer = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(subcontainer, lookAt:=xlWhole).Column)
    strSubcontainerType = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(subcontainerType, lookAt:=xlWhole).Column)
    strSubcontainerStorage = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(subcontainerStorage, lookAt:=xlWhole).Column)

'    strBox = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(boxStorage, lookAt:=xlWhole).Column)
'    strTube = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(tubeStorage, lookAt:=xlWhole).Column)
    strBarcode = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(barcode, lookAt:=xlWhole).Column)
    
    Dim strAdditionalID As String
    Dim strCode As String

    strAdditionalID = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(additionalID, lookAt:=xlWhole).Column).Value
    strCode = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(code, lookAt:=xlWhole).Column).Value

    If strInstitution <> "" Or strBuilding <> "" Or strFloor <> "" Or strRoom <> "" Or strRow <> "" Or strColumn <> "" Or strShelf <> "" _
        Or strContainer <> "" Or strContainerType <> "" Or strContainerStorage <> "" Or strSubcontainer <> "" Or strSubcontainerType <> "" Or strSubcontainerStorage <> "" _
        Or strBarcode <> "" Or strAdditionalID <> "" Or strCode <> "" Then
        'Or strBox <> "" Or strTube <> ""
        Set xmlUnitExtension = dom.createNode(NODE_ELEMENT, "UnitExtension", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlUnitExtension
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
        
        Set xmlStorage = dom.createNode(NODE_ELEMENT, "storage:Storage", "http://darwin.naturalsciences.be/xsd/")
        xmlUnitExtension.appendChild xmlStorage
        xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
        If strInstitution <> "" Or strBuilding <> "" Or strFloor <> "" Or strRoom <> "" Or strRow <> "" Or strColumn <> "" Or strShelf <> "" Or strBarcode <> "" Then
        'Or strBox <> "" Or strTube <> ""
        
            Set xmlLocalisation = dom.createNode(NODE_ELEMENT, "storage:Localisation", "http://darwin.naturalsciences.be/xsd/")
            xmlStorage.appendChild xmlLocalisation
            xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
            If strInstitution <> "" Then
                Set xmlInstitution = dom.createNode(NODE_ELEMENT, "storage:Institution", "http://darwin.naturalsciences.be/xsd/")
                xmlInstitution.Text = strInstitution
                xmlLocalisation.appendChild xmlInstitution
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strBuilding <> "" Then
                Set xmlBuilding = dom.createNode(NODE_ELEMENT, "storage:Building", "http://darwin.naturalsciences.be/xsd/")
                xmlBuilding.Text = strBuilding
                xmlLocalisation.appendChild xmlBuilding
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strFloor <> "" Then
                Set xmlFloor = dom.createNode(NODE_ELEMENT, "storage:Floor", "http://darwin.naturalsciences.be/xsd/")
                xmlFloor.Text = strFloor
                xmlLocalisation.appendChild xmlFloor
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strRoom <> "" Then
                Set xmlRoom = dom.createNode(NODE_ELEMENT, "storage:Room", "http://darwin.naturalsciences.be/xsd/")
                xmlRoom.Text = strRoom
                xmlLocalisation.appendChild xmlRoom
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strRow <> "" Then
                Set xmlRow = dom.createNode(NODE_ELEMENT, "storage:Row", "http://darwin.naturalsciences.be/xsd/")
                xmlRow.Text = strRow
                xmlLocalisation.appendChild xmlRow
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strColumn <> "" Then
                Set xmlColumn = dom.createNode(NODE_ELEMENT, "storage:Column", "http://darwin.naturalsciences.be/xsd/")
                xmlColumn.Text = strColumn
                xmlLocalisation.appendChild xmlColumn
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strShelf <> "" Then
                Set xmlShelf = dom.createNode(NODE_ELEMENT, "storage:Shelf", "http://darwin.naturalsciences.be/xsd/")
                xmlShelf.Text = strShelf
                xmlLocalisation.appendChild xmlShelf
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
'            If strBox <> "" Then
'                Set xmlBox = dom.createNode(NODE_ELEMENT, "storage:Box", "http://darwin.naturalsciences.be/xsd/")
'                xmlBox.Text = strBox
'                xmlLocalisation.appendChild xmlBox
'                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
'            End If
'            If strTube <> "" Then
'                Set xmlTube = dom.createNode(NODE_ELEMENT, "storage:Tube", "http://darwin.naturalsciences.be/xsd/")
'                xmlTube.Text = strTube
'                xmlLocalisation.appendChild xmlTube
'                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
'            End If
            If strBarcode <> "" Then
                Set xmlBarcode = dom.createNode(NODE_ELEMENT, "storage:Barcode", "http://darwin.naturalsciences.be/xsd/")
                xmlBarcode.Text = strBarcode
                xmlLocalisation.appendChild xmlBarcode
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
        
        End If
    
        If strContainer <> "" Or strContainerType <> "" Or strContainerStorage <> "" Or strSubcontainer <> "" Or strSubcontainerType <> "" Or strSubcontainerStorage <> "" Then
        
            Set xmlContainer = dom.createNode(NODE_ELEMENT, "storage:Container", "http://darwin.naturalsciences.be/xsd/")
            xmlStorage.appendChild xmlContainer
            xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlContainer.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            If strContainer <> "" Then
                Set xmlContainerName = dom.createNode(NODE_ELEMENT, "storage:ContainerName", "http://darwin.naturalsciences.be/xsd/")
                xmlContainerName.Text = strContainer
                xmlContainer.appendChild xmlContainerName
                xmlContainer.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strContainerType <> "" Then
                Set xmlContainerType = dom.createNode(NODE_ELEMENT, "storage:ContainerType", "http://darwin.naturalsciences.be/xsd/")
                xmlContainerType.Text = strContainerType
                xmlContainer.appendChild xmlContainerType
                xmlContainer.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strContainerStorage <> "" Then
                Set xmlContainerStorage = dom.createNode(NODE_ELEMENT, "storage:ContainerStorage", "http://darwin.naturalsciences.be/xsd/")
                xmlContainerStorage.Text = strContainerStorage
                xmlContainer.appendChild xmlContainerStorage
                xmlContainer.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strSubcontainer <> "" Then
                Set xmlSubcontainerName = dom.createNode(NODE_ELEMENT, "storage:SubcontainerName", "http://darwin.naturalsciences.be/xsd/")
                xmlSubcontainerName.Text = strSubcontainer
                xmlContainer.appendChild xmlSubcontainerName
                xmlContainer.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strSubcontainerType <> "" Then
                Set xmlSubcontainerType = dom.createNode(NODE_ELEMENT, "storage:SubcontainerType", "http://darwin.naturalsciences.be/xsd/")
                xmlSubcontainerType.Text = strSubcontainerType
                xmlContainer.appendChild xmlSubcontainerType
                xmlContainer.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            If strSubcontainerStorage <> "" Then
                Set xmlSubcontainerStorage = dom.createNode(NODE_ELEMENT, "storage:SubcontainerStorage", "http://darwin.naturalsciences.be/xsd/")
                xmlSubcontainerStorage.Text = strSubcontainerStorage
                xmlContainer.appendChild xmlSubcontainerStorage
                xmlContainer.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
        
        End If
        
        If strCode <> "" Or strAdditionalID <> "" Then

            Set xmlCodes = dom.createNode(NODE_ELEMENT, "storage:Codes", "http://darwin.naturalsciences.be/xsd/")
            xmlStorage.appendChild xmlCodes
            xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlCodes.appendChild dom.createTextNode(vbCrLf + Space$(12))

            If Not IsEmpty(strCode) And Not IsNull(strCode) And strCode <> "" Then
    
                Set xmlCode = dom.createNode(NODE_ELEMENT, "storage:Code", "http://darwin.naturalsciences.be/xsd/")
                xmlCodes.appendChild xmlCode
                xmlCodes.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))

                Set xmlCodeType = dom.createNode(NODE_ELEMENT, "storage:Type", "http://darwin.naturalsciences.be/xsd/")
                xmlCodeType.Text = "Code"
                xmlCode.appendChild xmlCodeType
                xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                Set xmlCodeValue = dom.createNode(NODE_ELEMENT, "storage:Value", "http://darwin.naturalsciences.be/xsd/")
                xmlCodeValue.Text = strCode
                xmlCode.appendChild xmlCodeValue
                xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            End If
            
            If Not IsEmpty(strAdditionalID) And Not IsNull(strAdditionalID) And strAdditionalID <> "" Then
    
                Set xmlCode = dom.createNode(NODE_ELEMENT, "storage:Code", "http://darwin.naturalsciences.be/xsd/")
                xmlCodes.appendChild xmlCode
                xmlCodes.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))

                Set xmlCodeType = dom.createNode(NODE_ELEMENT, "storage:Type", "http://darwin.naturalsciences.be/xsd/")
                xmlCodeType.Text = "Additional ID"
                xmlCode.appendChild xmlCodeType
                xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                Set xmlCodeValue = dom.createNode(NODE_ELEMENT, "storage:Value", "http://darwin.naturalsciences.be/xsd/")
                xmlCodeValue.Text = strAdditionalID
                xmlCode.appendChild xmlCodeValue
                xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            End If

        End If
    
    End If
        
            
'Exit_XMLExtensionStorage:
'
'        Exit Sub
'
'Err_XMLExtensionStorage:
'
'        MsgBox prompt:="An error occured in sub XMLExtensionStorage." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLExtensionStorage"
'        Resume Exit_XMLExtensionStorage
        
End Sub

'****SAMPLE****
'DataSets/DataSet/Units/Unit/SourceInstitutionID, SourceID and UnitID
Private Sub XMLSampleID(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLSampleID
    
    Dim xmlSampleUnitID As MSXML2.IXMLDOMElement
    Dim xmlSourceID As MSXML2.IXMLDOMElement
    Dim xmlSourceInstitutionID As MSXML2.IXMLDOMElement
    
    Dim strSampleUnitID As String
    Dim strSourceID As String
    Dim strSourceInstitutionID As String
    
    strSampleUnitID = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(sampleID, lookAt:=xlWhole).Column).Value
    strSourceID = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(sampleDatasetName, lookAt:=xlWhole).Column).Value
    strSourceInstitutionID = "See Collection attributed in DaRWIN"
    
    Set xmlSourceInstitutionID = dom.createNode(NODE_ELEMENT, "SourceInstitutionID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSourceInstitutionID.Text = strSourceInstitutionID
    subnode.appendChild xmlSourceInstitutionID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    
    If strSourceID <> "" Then
        Set xmlSourceID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSourceID.Text = strSourceID
        subnode.appendChild xmlSourceID
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    Else:
        Set xmlSourceInstitutionID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSourceInstitutionID.Text = "Not defined"
        subnode.appendChild xmlSourceInstitutionID
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    End If

    If strSampleUnitID <> "" Then
        Set xmlSampleUnitID = dom.createNode(NODE_ELEMENT, "UnitID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSampleUnitID.Text = strSampleUnitID
        subnode.appendChild xmlSampleUnitID
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    End If

'Exit_XMLSampleID:
'
'        Exit Sub
'
'Err_XMLSampleID:
'
'        MsgBox prompt:="An error occured in sub XMLSampleID." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLSampleID"
'        Resume Exit_XMLSampleID

End Sub

'DataSets/DataSet/Units/Unit/SpecimenUnit/Acquisition, Preparations
Private Sub XMLSampleUnit(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long, ByRef rowCounterSpec As Long, ByRef sample As Boolean)

    On Error Resume Next
'    On Error GoTo Err_XMLSpecimenUnit
    
    Dim xmlSpecUnit As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquisition As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquiredFrom As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquiredPerson As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquiredPersonName As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquiredOrg As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquiredOrgName As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAcquiredOrgRep As MSXML2.IXMLDOMElement
    Dim attrSpecimenUnitAcquiredOrgRep As MSXML2.IXMLDOMAttribute
    Dim xmlSpecimenUnitAcquiredOrgRepText As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitPreparations As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitPreparation As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitPreparationType As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitPreparationMaterials As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitNomenclatureTypes As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitNomenclatureType As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitNomenclatureTypeStatus As MSXML2.IXMLDOMElement

    Dim strPreparation As String
    Dim strPreservation As String
    Dim strFixation As String
    Dim strTypeStatusSpec As String
    Dim strSampleAcquiredFrom As String
    
    If rowCounterSpec = 0 Then
        strTypeStatusSpec = ""
    Else:
        strTypeStatusSpec = Application.Sheets("cSPECIMEN").Cells(rowCounterSpec, Application.Sheets("cSPECIMEN").Rows(1).Find(statusType, lookAt:=xlWhole).Column).Value
    End If
    strPreparation = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(samplePreparationType, lookAt:=xlWhole).Column).Value
    strPreservation = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(samplePreservation, lookAt:=xlWhole).Column).Value
    strFixation = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(fixation, lookAt:=xlWhole).Column).Value
'    If sample = False Then
'        If Not IsEmpty(strPreservation) And Not IsNull(strPreservation) And strPreservation <> "" Then
'            strPreservation = "Extraction tissue preservation: " & strPreservation
'        End If
'    End If
    If sample = True Then
        strSampleAcquiredFrom = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(sampleAcquiredFrom, lookAt:=xlWhole).Column).Value
    Else:
        strSampleAcquiredFrom = ""
    End If
    
    If (Not IsEmpty(strPreparation) And Not IsNull(strPreparation) And strPreparation <> "") _
        Or (Not IsEmpty(strPreservation) And Not IsNull(strPreservation) And strPreservation <> "") _
        Or (Not IsEmpty(strSampleAcquiredFrom) And Not IsNull(strSampleAcquiredFrom) And strSampleAcquiredFrom <> "") Then
        
        Set xmlSpecUnit = dom.createNode(NODE_ELEMENT, "SpecimenUnit", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlSpecUnit
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
        If Not IsEmpty(strSampleAcquiredFrom) And Not IsNull(strSampleAcquiredFrom) And strSampleAcquiredFrom <> "" Then
    
            Set xmlSpecimenUnitAcquisition = dom.createNode(NODE_ELEMENT, "Acquisition", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitAcquisition
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitAcquisition.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlSpecimenUnitAcquiredFrom = dom.createNode(NODE_ELEMENT, "AcquiredFrom", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitAcquisition.appendChild xmlSpecimenUnitAcquiredFrom
            xmlSpecimenUnitAcquisition.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlSpecimenUnitAcquiredFrom.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            Set xmlSpecimenUnitAcquiredPerson = dom.createNode(NODE_ELEMENT, "Person", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitAcquiredFrom.appendChild xmlSpecimenUnitAcquiredPerson
            xmlSpecimenUnitAcquiredFrom.appendChild dom.createTextNode(vbCrLf + Space$(14))
            xmlSpecimenUnitAcquiredPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
            
            Set xmlSpecimenUnitAcquiredPersonName = dom.createNode(NODE_ELEMENT, "FullName", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitAcquiredPersonName.Text = strSampleAcquiredFrom
            xmlSpecimenUnitAcquiredPerson.appendChild xmlSpecimenUnitAcquiredPersonName
            xmlSpecimenUnitAcquiredPerson.appendChild dom.createTextNode(vbCrLf + Space$(16))
    
        End If

        If Not IsEmpty(strPreparation) And Not IsNull(strPreparation) And strPreparation <> "" _
        Or Not IsEmpty(strPreservation) And Not IsNull(strPreservation) And strPreservation <> "" _
        Or Not IsEmpty(strFixation) And Not IsNull(strFixation) And strFixation <> "" Then
    
            Set xmlSpecimenUnitPreparations = dom.createNode(NODE_ELEMENT, "Preparations", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitPreparations
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            If Not IsEmpty(strFixation) And Not IsNull(strFixation) And strFixation <> "" Then
    
                Set xmlSpecimenUnitPreparation = dom.createNode(NODE_ELEMENT, "Preparation", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparations.appendChild xmlSpecimenUnitPreparation
                xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                Set xmlSpecimenUnitPreparationType = dom.createNode(NODE_ELEMENT, "PreparationType", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationType.Text = "Specimen fixation"
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationType
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
                Set xmlSpecimenUnitPreparationMaterials = dom.createNode(NODE_ELEMENT, "PreparationMaterials", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationMaterials.Text = strFixation
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationMaterials
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
            End If
    
            If Not IsEmpty(strPreparation) And Not IsNull(strPreparation) And strPreparation <> "" Then
    
                Set xmlSpecimenUnitPreparation = dom.createNode(NODE_ELEMENT, "Preparation", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparations.appendChild xmlSpecimenUnitPreparation
                xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                                
                Set xmlSpecimenUnitPreparationType = dom.createNode(NODE_ELEMENT, "PreparationType", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationType.Text = "Tissue preparation"
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationType
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
    
                Set xmlSpecimenUnitPreparationMaterials = dom.createNode(NODE_ELEMENT, "PreparationMaterials", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationMaterials.Text = strPreparation
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationMaterials
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
            End If

            If Not IsEmpty(strPreservation) And Not IsNull(strPreservation) And strPreservation <> "" Then
    
                Set xmlSpecimenUnitPreparation = dom.createNode(NODE_ELEMENT, "Preparation", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparations.appendChild xmlSpecimenUnitPreparation
                xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                Set xmlSpecimenUnitPreparationType = dom.createNode(NODE_ELEMENT, "PreparationType", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationType.Text = "Tissue preservation"
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationType
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                                
                Set xmlSpecimenUnitPreparationMaterials = dom.createNode(NODE_ELEMENT, "PreparationMaterials", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlSpecimenUnitPreparationMaterials.Text = strPreservation
                xmlSpecimenUnitPreparation.appendChild xmlSpecimenUnitPreparationMaterials
                xmlSpecimenUnitPreparation.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
            End If
                    
        End If
    
        If Not IsEmpty(strTypeStatusSpec) And Not IsNull(strTypeStatusSpec) And strTypeStatusSpec <> "" Then
        
            Set xmlSpecimenUnitNomenclatureTypes = dom.createNode(NODE_ELEMENT, "NomenclaturalTypeDesignations", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitNomenclatureTypes
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitNomenclatureTypes.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlSpecimenUnitNomenclatureType = dom.createNode(NODE_ELEMENT, "NomenclaturalTypeDesignation", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitNomenclatureTypes.appendChild xmlSpecimenUnitNomenclatureType
            xmlSpecimenUnitNomenclatureTypes.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlSpecimenUnitNomenclatureType.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            Set xmlSpecimenUnitNomenclatureTypeStatus = dom.createNode(NODE_ELEMENT, "TypeStatus", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecimenUnitNomenclatureTypeStatus.Text = strTypeStatusSpec
            xmlSpecimenUnitNomenclatureType.appendChild xmlSpecimenUnitNomenclatureTypeStatus
            xmlSpecimenUnitNomenclatureType.appendChild dom.createTextNode(vbCrLf + Space$(14))
                    
        End If

    End If
    
'Exit_XMLSpecimenUnit:
'
'        Exit Sub
'
'Err_XMLSpecimenUnit:
'
'        MsgBox prompt:="An error occured in sub XMLSpecimenUnit." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLSpecimenUnit"
'        Resume Exit_XMLSpecimenUnit

End Sub

'DataSets/Dataset/Units/Unit/Associations
Private Sub XMLSampleAssociation(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLSampleAssociation
    
    Dim xmlAssociations As MSXML2.IXMLDOMElement
    Dim xmlUnitAssociation As MSXML2.IXMLDOMElement
    Dim xmlAssociatedUnitID As MSXML2.IXMLDOMElement
    Dim xmlAssociatedInstitutionID As MSXML2.IXMLDOMElement
    Dim xmlAssociatedSourceID As MSXML2.IXMLDOMElement
    Dim xmlAssociationType As MSXML2.IXMLDOMElement
    
    Dim strAssociatedSpecimenInstitution As String
    Dim strAssociatedSpecimenDataset As String
    Dim strAssociatedspecimenID As String
    
    strAssociatedSpecimenInstitution = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(associatedSpecimenInstitution, lookAt:=xlWhole).Column).Value
    strAssociatedSpecimenDataset = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(associatedSpecimenDataset, lookAt:=xlWhole).Column).Value
    strAssociatedspecimenID = Application.Sheets("cSAMPLE").Cells(rowCounter, Application.Sheets("cSAMPLE").Rows(1).Find(associatedspecimenID, lookAt:=xlWhole).Column).Value

    If Not IsEmpty(strAssociatedspecimenID) And Not IsNull(strAssociatedspecimenID) And strAssociatedspecimenID <> "" Then
   
            Set xmlAssociations = dom.createNode(NODE_ELEMENT, "Associations", "http://www.tdwg.org/schemas/abcd/2.06")
            subnode.appendChild xmlAssociations
            subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(10))
            
            Set xmlUnitAssociation = dom.createNode(NODE_ELEMENT, "UnitAssociation", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlAssociations.appendChild xmlUnitAssociation
            xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            If Not IsEmpty(strAssociatedSpecimenInstitution) And Not IsNull(strAssociatedSpecimenInstitution) And strAssociatedSpecimenInstitution <> "" Then
                Set xmlAssociatedInstitutionID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceInstitutionCode", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedInstitutionID.Text = strAssociatedSpecimenInstitution
                xmlUnitAssociation.appendChild xmlAssociatedInstitutionID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            Else:
                Set xmlAssociatedInstitutionID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceInstitutionCode", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedInstitutionID.Text = "Not defined"
                xmlUnitAssociation.appendChild xmlAssociatedInstitutionID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
    
            If Not IsEmpty(strAssociatedSpecimenDataset) And Not IsNull(strAssociatedSpecimenDataset) And strAssociatedSpecimenDataset <> "" Then
                Set xmlAssociatedSourceID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceName", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedSourceID.Text = strAssociatedSpecimenDataset
                xmlUnitAssociation.appendChild xmlAssociatedSourceID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            Else:
                Set xmlAssociatedSourceID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceName", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedSourceID.Text = "Not defined"
                xmlUnitAssociation.appendChild xmlAssociatedSourceID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            
            If Not IsEmpty(strAssociatedspecimenID) And Not IsNull(strAssociatedspecimenID) And strAssociatedspecimenID <> "" Then
                Set xmlAssociatedUnitID = dom.createNode(NODE_ELEMENT, "AssociatedUnitID", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedUnitID.Text = strAssociatedspecimenID
                xmlUnitAssociation.appendChild xmlAssociatedUnitID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
    
            Set xmlAssociationType = dom.createNode(NODE_ELEMENT, "AssociationType", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlAssociationType.Text = "Specimen-Sample"
            xmlUnitAssociation.appendChild xmlAssociationType
            xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
    End If
    
'Exit_XMLSampleAssociation:
'
'        Exit Sub
'
'Err_XMLSampleAssociation:
'
'        MsgBox prompt:="An error occured in sub XMLSampleAssociation." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLSampleAssociation"
'        Resume Exit_XMLSampleAssociation
    
End Sub

'DataSets/DataSet/Units/Unit/Notes
Private Sub XMLSampleNotes(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLSpecNotes

    Dim xmlUnitNotes As MSXML2.IXMLDOMElement
    
    Dim strComment As String
    
    strComment = Application.Sheets("cSAMPLE").Cells(rowCounter, Sheets("cSAMPLE").Rows(1).Find(sampleNotes, lookAt:=xlWhole).Column)
    
    If Not IsEmpty(strComment) And Not IsNull(strComment) And strComment <> "" Then
        Set xmlUnitNotes = dom.createNode(NODE_ELEMENT, "Notes", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlUnitNotes.Text = strComment
        subnode.appendChild xmlUnitNotes
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    End If

'Exit_XMLSpecNotes:
'
'        Exit Sub
'
'Err_XMLSpecNotes:
'
'        MsgBox prompt:="An error occured in sub XMLSpecNotes." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLSpecNotes"
'        Resume Exit_XMLSpecNotes

End Sub

'DataSets/DataSet/Units/Unit/UnitExtension => storage.xsd
Public Sub XMLExtensionSampleStorage(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLSampleStorage
    
    Dim attr As MSXML2.IXMLDOMAttribute
    Dim xmlUnitExtension As MSXML2.IXMLDOMElement
    Dim xmlStorage As MSXML2.IXMLDOMElement
    Dim xmlLocalisation As MSXML2.IXMLDOMElement
    Dim xmlInstitution As MSXML2.IXMLDOMElement
    Dim xmlBuilding As MSXML2.IXMLDOMElement
    Dim xmlFloor As MSXML2.IXMLDOMElement
    Dim xmlRoom As MSXML2.IXMLDOMElement
    Dim xmlColumn As MSXML2.IXMLDOMElement
    Dim xmlBox As MSXML2.IXMLDOMElement
    Dim xmlTube As MSXML2.IXMLDOMElement
    Dim xmlBarcode As MSXML2.IXMLDOMElement
    
    Dim strInstitution As String
    Dim strBuilding As String
    Dim strFloor As String
    Dim strRoom As String
    Dim strColumn As String
    Dim strBox As String
    Dim strTube As String
    Dim str2DBarcode As String
    
    strInstitution = Application.Sheets("cSAMPLE").Cells(rowCounter, Sheets("cSAMPLE").Rows(1).Find(sampleInstitutionStorage, lookAt:=xlWhole).Column)
    strBuilding = Application.Sheets("cSAMPLE").Cells(rowCounter, Sheets("cSAMPLE").Rows(1).Find(sampleBuildingStorage, lookAt:=xlWhole).Column)
    strFloor = Application.Sheets("cSAMPLE").Cells(rowCounter, Sheets("cSAMPLE").Rows(1).Find(sampleFloorStorage, lookAt:=xlWhole).Column)
    strRoom = Application.Sheets("cSAMPLE").Cells(rowCounter, Sheets("cSAMPLE").Rows(1).Find(sampleRoomStorage, lookAt:=xlWhole).Column)
    strColumn = Application.Sheets("cSAMPLE").Cells(rowCounter, Sheets("cSAMPLE").Rows(1).Find(sampleColumnStorage, lookAt:=xlWhole).Column)
    strBox = Application.Sheets("cSAMPLE").Cells(rowCounter, Sheets("cSAMPLE").Rows(1).Find(sampleBoxStorage, lookAt:=xlWhole).Column)
    strTube = Application.Sheets("cSAMPLE").Cells(rowCounter, Sheets("cSAMPLE").Rows(1).Find(sampleTubeStorage, lookAt:=xlWhole).Column)
    str2DBarcode = Application.Sheets("cSAMPLE").Cells(rowCounter, Sheets("cSAMPLE").Rows(1).Find(sample2Dbarcode, lookAt:=xlWhole).Column)
    
    If strInstitution <> "" Or strBuilding <> "" Or strFloor <> "" Or strRoom <> "" Or strColumn <> "" Or strBox <> "" Or str2DBarcode <> "" Then
        
        Set xmlUnitExtension = dom.createNode(NODE_ELEMENT, "UnitExtension", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlUnitExtension
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
        Set xmlStorage = dom.createNode(NODE_ELEMENT, "storage:Storage", "http://darwin.naturalsciences.be/xsd/")
        xmlUnitExtension.appendChild xmlStorage
        xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
        Set xmlLocalisation = dom.createNode(NODE_ELEMENT, "storage:Localisation", "http://darwin.naturalsciences.be/xsd/")
        xmlStorage.appendChild xmlLocalisation
        xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
        xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        If strInstitution <> "" Then
            Set xmlInstitution = dom.createNode(NODE_ELEMENT, "storage:Institution", "http://darwin.naturalsciences.be/xsd/")
            xmlInstitution.Text = strInstitution
            xmlLocalisation.appendChild xmlInstitution
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        End If
        If strBuilding <> "" Then
            Set xmlBuilding = dom.createNode(NODE_ELEMENT, "storage:Building", "http://darwin.naturalsciences.be/xsd/")
            xmlBuilding.Text = strBuilding
            xmlLocalisation.appendChild xmlBuilding
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        End If
        If strFloor <> "" Then
            Set xmlFloor = dom.createNode(NODE_ELEMENT, "storage:Floor", "http://darwin.naturalsciences.be/xsd/")
            xmlFloor.Text = strFloor
            xmlLocalisation.appendChild xmlFloor
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        End If
        If strRoom <> "" Then
            Set xmlRoom = dom.createNode(NODE_ELEMENT, "storage:Room", "http://darwin.naturalsciences.be/xsd/")
            xmlRoom.Text = strRoom
            xmlLocalisation.appendChild xmlRoom
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        End If
        If strColumn <> "" Then
            Set xmlColumn = dom.createNode(NODE_ELEMENT, "storage:Column", "http://darwin.naturalsciences.be/xsd/")
            xmlColumn.Text = strColumn
            xmlLocalisation.appendChild xmlColumn
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        End If
        If strBox <> "" Then
            Set xmlBox = dom.createNode(NODE_ELEMENT, "storage:Box", "http://darwin.naturalsciences.be/xsd/")
            xmlBox.Text = strBox
            xmlLocalisation.appendChild xmlBox
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        End If
        If strTube <> "" Then
            Set xmlTube = dom.createNode(NODE_ELEMENT, "storage:Tube", "http://darwin.naturalsciences.be/xsd/")
            xmlTube.Text = strTube
            xmlLocalisation.appendChild xmlTube
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        End If
        If str2DBarcode <> "" Then
            Set xmlBarcode = dom.createNode(NODE_ELEMENT, "storage:Barcode", "http://darwin.naturalsciences.be/xsd/")
            xmlBarcode.Text = str2DBarcode
            xmlLocalisation.appendChild xmlBarcode
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
        End If
    
    End If
    
'Exit_XMLSampleStorage:
'
'        Exit Sub
'
'Err_XMLSampleStorage:
'
'        MsgBox prompt:="An error occured in sub XMLSampleStorage." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLSampleStorage"
'        Resume Exit_XMLSampleStorage
    
End Sub

'****DNA****
'DataSets/DataSet/Units/Unit/SourceInstitutionID, SourceID and UnitID
Private Sub XMLDNAID(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)
    
    On Error Resume Next
'    On Error GoTo Err_XMLDNAID
    
    Dim xmlUnitID As MSXML2.IXMLDOMElement
    Dim xmlSourceID As MSXML2.IXMLDOMElement
    Dim xmlSourceInstitutionID As MSXML2.IXMLDOMElement
    
    Dim strDNAUnitID As String
    Dim strSourceID As String
    Dim strSourceInstitutionID As String
    
    strDNAUnitID = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaID, lookAt:=xlWhole).Column).Value
    strSourceID = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaDatasetName, lookAt:=xlWhole).Column).Value
    strSourceInstitutionID = "See Collection attributed in DaRWIN"
    
    Set xmlSourceInstitutionID = dom.createNode(NODE_ELEMENT, "SourceInstitutionID", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlSourceInstitutionID.Text = strSourceInstitutionID
    subnode.appendChild xmlSourceInstitutionID
    subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    
    If strSourceID <> "" Then
        Set xmlSourceID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSourceID.Text = strSourceID
        subnode.appendChild xmlSourceID
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    Else:
        Set xmlSourceInstitutionID = dom.createNode(NODE_ELEMENT, "SourceID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlSourceInstitutionID.Text = "Not defined"
        subnode.appendChild xmlSourceInstitutionID
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    End If

    If strDNAUnitID <> "" Then
        Set xmlUnitID = dom.createNode(NODE_ELEMENT, "UnitID", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlUnitID.Text = strDNAUnitID
        subnode.appendChild xmlUnitID
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
    End If

'Exit_XMLDNAID:
'
'        Exit Sub
'
'Err_XMLDNAID:
'
'        MsgBox prompt:="An error occured in sub XMLDNAID." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLDNAID"
'        Resume Exit_XMLDNAID

End Sub

'DataSets/Dataset/Units/Unit/Associations
Private Sub XMLDNAAssociation(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLDNAAssociation
    
    Dim xmlAssociations As MSXML2.IXMLDOMElement
    Dim xmlUnitAssociation As MSXML2.IXMLDOMElement
    Dim xmlAssociatedUnitID As MSXML2.IXMLDOMElement
    Dim xmlAssociatedInstitutionID As MSXML2.IXMLDOMElement
    Dim xmlAssociatedSourceID As MSXML2.IXMLDOMElement
    Dim xmlAssociationType As MSXML2.IXMLDOMElement
    
    Dim strAssociatedSampleInstitution As String
    Dim strAssociatedSampleDataset As String
    Dim strAssociatedSampleID As String
    
    strAssociatedSampleInstitution = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(associatedSampleInstitution, lookAt:=xlWhole).Column).Value
    strAssociatedSampleDataset = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(associatedSampleDataset, lookAt:=xlWhole).Column).Value
    strAssociatedSampleID = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(associatedSampleID, lookAt:=xlWhole).Column).Value

    If Not IsEmpty(strAssociatedSampleID) And Not IsNull(strAssociatedSampleID) And strAssociatedSampleID <> "" Then
   
            Set xmlAssociations = dom.createNode(NODE_ELEMENT, "Associations", "http://www.tdwg.org/schemas/abcd/2.06")
            subnode.appendChild xmlAssociations
            subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(10))
            
            Set xmlUnitAssociation = dom.createNode(NODE_ELEMENT, "UnitAssociation", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlAssociations.appendChild xmlUnitAssociation
            xmlAssociations.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            If Not IsEmpty(strAssociatedSampleInstitution) And Not IsNull(strAssociatedSampleInstitution) And strAssociatedSampleInstitution <> "" Then
                Set xmlAssociatedInstitutionID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceInstitutionCode", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedInstitutionID.Text = strAssociatedSampleInstitution
                xmlUnitAssociation.appendChild xmlAssociatedInstitutionID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            Else:
                Set xmlAssociatedInstitutionID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceInstitutionCode", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedInstitutionID.Text = "Not defined"
                xmlUnitAssociation.appendChild xmlAssociatedInstitutionID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
    
            If Not IsEmpty(strAssociatedSampleDataset) And Not IsNull(strAssociatedSampleDataset) And strAssociatedSampleDataset <> "" Then
                Set xmlAssociatedSourceID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceName", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedSourceID.Text = strAssociatedSampleDataset
                xmlUnitAssociation.appendChild xmlAssociatedSourceID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            Else:
                Set xmlAssociatedSourceID = dom.createNode(NODE_ELEMENT, "AssociatedUnitSourceName", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedSourceID.Text = "Not defined"
                xmlUnitAssociation.appendChild xmlAssociatedSourceID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            
            If Not IsEmpty(strAssociatedSampleID) And Not IsNull(strAssociatedSampleID) And strAssociatedSampleID <> "" Then
                Set xmlAssociatedUnitID = dom.createNode(NODE_ELEMENT, "AssociatedUnitID", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlAssociatedUnitID.Text = strAssociatedSampleID
                xmlUnitAssociation.appendChild xmlAssociatedUnitID
                xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
    
            Set xmlAssociationType = dom.createNode(NODE_ELEMENT, "AssociationType", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlAssociationType.Text = "Sample-DNA extract"
            xmlUnitAssociation.appendChild xmlAssociationType
            xmlUnitAssociation.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
    End If
    
'Exit_XMLDNAAssociation:
'
'        Exit Sub
'
'Err_XMLDNAAssociation:
'
'        MsgBox prompt:="An error occured in sub XMLDNAAssociation." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLDNAAssociation"
'        Resume Exit_XMLDNAAssociation
    
End Sub

'DataSets/DataSet/Units/Unit/MeasurementsOrFacts
Private Sub XMLDNAMeasurements(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLMeasurements
    
    Dim xmlMeasurementsOrFacts As MSXML2.IXMLDOMElement
    Dim xmlMeasurementOrFact As MSXML2.IXMLDOMElement
    Dim xmlMeasurementOrFactAtomised As MSXML2.IXMLDOMElement
    Dim xmlMeasurementOrFactParameter As MSXML2.IXMLDOMElement
    Dim xmlMeasurementOrFactLowerValue As MSXML2.IXMLDOMElement
    Dim xmlMeasurementOrFactUnit As MSXML2.IXMLDOMElement
    
    Dim strDigestionTime As String
    Dim strDigestionVolume As String
    Dim strElution As String
    Dim strElutionVolume As String
    Dim strLength As String
    
    strDigestionTime = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(digestionTime, lookAt:=xlWhole).Column).Value
    strDigestionVolume = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(digestionVolume, lookAt:=xlWhole).Column).Value
    strElution = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(elutionBuffer, lookAt:=xlWhole).Column).Value
    strElutionVolume = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(elutionVolume, lookAt:=xlWhole).Column).Value
    If Not IsEmpty(Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaSize, lookAt:=xlWhole).Column)) _
        And Not IsNull(Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaSize, lookAt:=xlWhole).Column)) _
        And Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaSize, lookAt:=xlWhole).Column).Value <> "" _
        And IsNumeric(Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaSize, lookAt:=xlWhole).Column)) Then
            If Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaSize, lookAt:=xlWhole).Column).Value - Int(Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaSize, lookAt:=xlWhole).Column).Value) = 0 Then
                strLength = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaSize, lookAt:=xlWhole).Column).Value
            End If
    Else: strLength = ""
    End If
    
    If Not IsEmpty(strDigestionTime) And Not IsNull(strDigestionTime) And strDigestionTime <> "" _
        Or Not IsEmpty(strDigestionVolume) And Not IsNull(strDigestionVolume) And strDigestionVolume <> "" _
        Or Not IsEmpty(strElution) And Not IsNull(strElution) And strElution <> "" _
        Or Not IsEmpty(strElutionVolume) And Not IsNull(strElutionVolume) And strElutionVolume <> "" Then
        
            Set xmlMeasurementsOrFacts = dom.createNode(NODE_ELEMENT, "MeasurementsOrFacts", "http://www.tdwg.org/schemas/abcd/2.06")
            subnode.appendChild xmlMeasurementsOrFacts
            subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
            xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
            
            If Not IsEmpty(strDigestionTime) And Not IsNull(strDigestionTime) And strDigestionTime <> "" Then
    
                Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactParameter.Text = "Digestion Time"
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
                Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactLowerValue.Text = strDigestionTime
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            End If
    
            If Not IsEmpty(strDigestionVolume) And Not IsNull(strDigestionVolume) And strDigestionVolume <> "" Then
    
                Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactParameter.Text = "Digestion Volume"
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
                Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactLowerValue.Text = strDigestionVolume
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
                Set xmlMeasurementOrFactUnit = dom.createNode(NODE_ELEMENT, "UnitOfMeasurement", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactUnit.Text = "µl"
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactUnit
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
           
            End If
    
            If Not IsEmpty(strElution) And Not IsNull(strElution) And strElution <> "" Then
    
                Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactParameter.Text = "Elution buffer"
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
                Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactLowerValue.Text = strElution
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            End If
    
            If Not IsEmpty(strElutionVolume) And Not IsNull(strElutionVolume) And strElutionVolume <> "" Then
    
                Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
                Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
                Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactParameter.Text = "Elution Volume"
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
                Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactLowerValue.Text = strElutionVolume
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
                Set xmlMeasurementOrFactUnit = dom.createNode(NODE_ELEMENT, "UnitOfMeasurement", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlMeasurementOrFactUnit.Text = "µl"
                xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactUnit
                xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            End If
            
            If Not IsEmpty(strLength) And Not IsNull(strLength) And strLength <> "" And IsNumeric(strLength) Then
               
                    Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
                    xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
                    Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
                    xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
                    Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactParameter.Text = "DNA size"
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
                    Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactLowerValue.Text = strLength
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                    Set xmlMeasurementOrFactUnit = dom.createNode(NODE_ELEMENT, "UnitOfMeasurement", "http://www.tdwg.org/schemas/abcd/2.06")
                    xmlMeasurementOrFactUnit.Text = "bp"
                    xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactUnit
                    xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            End If
    
        End If
    
'Exit_XMLMeasurements:
'
'        Exit Sub
'
'Err_XMLMeasurements:
'
'        MsgBox prompt:="An error occured in sub XMLMeasurements." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLMeasurements"
'        Resume Exit_XMLMeasurements

End Sub

'DataSets/DataSet/Units/Unit/Notes
Private Sub XMLDNANotes(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLDNANotes
    
    Dim XMLNotes As MSXML2.IXMLDOMElement
    
    Dim strDNANotes As String
    
    strDNANotes = Application.Sheets("cDNA").Cells(rowCounter, Sheets("cDNA").Rows(1).Find(dnaNotes, lookAt:=xlWhole).Column).Value
    
    If Not IsEmpty(strDNANotes) And Not IsNull(strDNANotes) And strDNANotes <> "" Then
        Set XMLNotes = dom.createNode(NODE_ELEMENT, "Notes", "http://www.tdwg.org/schemas/abcd/2.06")
        XMLNotes.Text = strDNANotes
        subnode.appendChild XMLNotes
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
    End If
    
'Exit_XMLDNANotes:
'
'        Exit Sub
'
'Err_XMLDNANotes:
'
'        MsgBox prompt:="An error occured in sub XMLDNANotes." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLDNANotes"
'        Resume Exit_XMLDNANotes

End Sub

'DataSets/DataSet/Units/Unit/UnitExtension 1)DNA 2)Storage
Private Sub XMLExtensionDNA(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLExtensionDNA
    
    Dim attr As MSXML2.IXMLDOMAttribute
    Dim xmlUnitExtension As MSXML2.IXMLDOMElement
    Dim xmlDNASample As MSXML2.IXMLDOMElement
    Dim xmlDNATissue As MSXML2.IXMLDOMElement
    Dim xmlDNAPreservation As MSXML2.IXMLDOMElement
    Dim xmlExtractionStaff As MSXML2.IXMLDOMElement
    Dim xmlDNAExtractionDate As MSXML2.IXMLDOMElement
    Dim xmlDNAExtractionMethod As MSXML2.IXMLDOMElement
    Dim xmlDNAROfAbsorbance As MSXML2.IXMLDOMElement
    Dim xmlDNAConcentration As MSXML2.IXMLDOMElement
    Dim attrDNAConcentration As MSXML2.IXMLDOMAttribute
    Dim xmlDNAAmplifications As MSXML2.IXMLDOMElement
    Dim xmlDNAAmplification As MSXML2.IXMLDOMElement
    Dim xmlDNASequencings As MSXML2.IXMLDOMElement
    Dim xmlDNASequencing As MSXML2.IXMLDOMElement
    Dim xmlDNAGenBankNumber As MSXML2.IXMLDOMElement
    
    Dim attrStorage As MSXML2.IXMLDOMAttribute
    Dim xmlStorage As MSXML2.IXMLDOMElement
    Dim xmlLocalisation As MSXML2.IXMLDOMElement
    Dim xmlInstitution As MSXML2.IXMLDOMElement
    Dim xmlBuilding As MSXML2.IXMLDOMElement
    Dim xmlFloor As MSXML2.IXMLDOMElement
    Dim xmlRoom As MSXML2.IXMLDOMElement
    Dim xmlColumn As MSXML2.IXMLDOMElement
    Dim xmlBox As MSXML2.IXMLDOMElement
    Dim xmlPosition As MSXML2.IXMLDOMElement
    Dim xmlBarcode As MSXML2.IXMLDOMElement
    Dim xmlCodes As MSXML2.IXMLDOMElement
    Dim xmlCode As MSXML2.IXMLDOMElement
    Dim xmlCodeType As MSXML2.IXMLDOMElement
    Dim xmlCodeValue As MSXML2.IXMLDOMElement

    Dim strTissue As String, strPreservation As String, strPreservationLimited As String, strExtractionStaff As String
    Dim strExtractionDate As String, strExtractionDD As String, strExtractionMM As String, strExtractionYY As String
    Dim strConcentration As String, strAbs As String, strExtractionMethod As String, strGenBank As String
    Dim strTestFill As String, strTestFill2 As String
    
    strTissue = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(extractionTissue, lookAt:=xlWhole).Column).Value
    strPreservation = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaPreservation, lookAt:=xlWhole).Column).Value
    If strPreservation = "Dry" Or strPreservation = "dry" Or strPreservation = "Frozen" Or strPreservation = "frozen" Then
        strPreservationLimited = strPreservation
    Else:
        strPreservationLimited = ""
    End If
    strExtractionStaff = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(extractedBy, lookAt:=xlWhole).Column).Value
       
    strExtractionDD = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(extractionDay, lookAt:=xlWhole).Column).Value
    If strExtractionDD <> "" And IsNumeric(strExtractionDD) Then
        If strExtractionDD > 31 Or strExtractionDD = 0 Then
            strExtractionDD = ""
        ElseIf strExtractionDD < 10 Then
            strExtractionDD = "0" & strExtractionDD
        End If
    Else:
        strExtractionDD = ""
    End If
    
    strExtractionMM = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(extractionMonth, lookAt:=xlWhole).Column).Value
    If strExtractionMM <> "" And IsNumeric(strExtractionMM) Then
        If strExtractionMM > 12 Or strExtractionMM = 0 Then
            strExtractionMM = ""
        ElseIf strExtractionMM < 10 Then
            strExtractionMM = "0" & strExtractionMM
        End If
    Else:
        strExtractionMM = ""
    End If
    
    strExtractionYY = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(extractionYear, lookAt:=xlWhole).Column).Value
    If strExtractionYY <> "" And IsNumeric(strExtractionYY) Then
        If strExtractionYY > 999 Then
            strExtractionYY = strExtractionYY
        Else:
            strExtractionYY = ""
        End If
    Else:
        strExtractionYY = ""
    End If
    
    If strExtractionYY <> "" And strExtractionMM <> "" And strExtractionDD <> "" Then
        strExtractionDate = strExtractionYY & "-" & strExtractionMM & "-" & strExtractionDD
    Else:
        strExtractionDate = ""
    End If
    
    strAbs = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaAbsorbance260280, lookAt:=xlWhole).Column).Value
    strConcentration = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaConcentration, lookAt:=xlWhole).Column).Value
    strExtractionMethod = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(extractionMethod, lookAt:=xlWhole).Column).Value
    strGenBank = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(genBank, lookAt:=xlWhole).Column).Value
        
    strTestFill = strTissue & strPreservationLimited & strExtractionStaff & strExtractionDate & strAbs & strConcentration & strExtractionMethod & strGenBank
    
    Dim strInstitution As String
    Dim strBuilding As String
    Dim strFloor As String
    Dim strRoom As String
    Dim strColumn As String
    Dim strBox As String
    Dim strPosition As String
    Dim str2DBarcode As String
    Dim strAdditionalID As String
    
    strInstitution = Application.Sheets("cDNA").Cells(rowCounter, Sheets("cDNA").Rows(1).Find(dnaInstitutionStorage, lookAt:=xlWhole).Column)
    strBuilding = Application.Sheets("cDNA").Cells(rowCounter, Sheets("cDNA").Rows(1).Find(dnaBuildingStorage, lookAt:=xlWhole).Column)
    strFloor = Application.Sheets("cDNA").Cells(rowCounter, Sheets("cDNA").Rows(1).Find(dnaFloorStorage, lookAt:=xlWhole).Column)
    strRoom = Application.Sheets("cDNA").Cells(rowCounter, Sheets("cDNA").Rows(1).Find(dnaRoomStorage, lookAt:=xlWhole).Column)
    strColumn = Application.Sheets("cDNA").Cells(rowCounter, Sheets("cDNA").Rows(1).Find(dnaFridgeOrDrawerStorage, lookAt:=xlWhole).Column)
    strBox = Application.Sheets("cDNA").Cells(rowCounter, Sheets("cDNA").Rows(1).Find(dnaBoxStorage, lookAt:=xlWhole).Column)
    strPosition = Application.Sheets("cDNA").Cells(rowCounter, Sheets("cDNA").Rows(1).Find(dnaPositionStorage, lookAt:=xlWhole).Column)
    str2DBarcode = Application.Sheets("cDNA").Cells(rowCounter, Sheets("cDNA").Rows(1).Find(dna2Dbarcode, lookAt:=xlWhole).Column)
    strAdditionalID = Application.Sheets("cDNA").Cells(rowCounter, Application.Sheets("cDNA").Rows(1).Find(dnaAdditionalID, lookAt:=xlWhole).Column).Value

    strTestFill2 = strInstitution & strBuilding & strFloor & strRoom & strColumn & strBox & strPosition & str2DBarcode
    
    If Not IsEmpty(strTestFill) And Not IsNull(strTestFill) And strTestFill <> "" _
        And Not IsEmpty(strTestFill2) And Not IsNull(strTestFill2) And strTestFill2 <> "" _
        And Not IsEmpty(strAdditionalID) And Not IsNull(strAdditionalID) And strAdditionalID <> "" Then
        
        Set xmlUnitExtension = dom.createNode(NODE_ELEMENT, "UnitExtension", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlUnitExtension
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
    
        If Not IsEmpty(strTestFill2) And Not IsNull(strTestFill2) And strTestFill2 <> "" Then
        
            Set xmlStorage = dom.createNode(NODE_ELEMENT, "storage:Storage", "http://darwin.naturalsciences.be/xsd/")
            xmlUnitExtension.appendChild xmlStorage
            xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
            
            Set xmlLocalisation = dom.createNode(NODE_ELEMENT, "storage:Localisation", "http://darwin.naturalsciences.be/xsd/")
            xmlStorage.appendChild xmlLocalisation
            xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            If strInstitution <> "" Then
                Set xmlInstitution = dom.createNode(NODE_ELEMENT, "storage:Institution", "http://darwin.naturalsciences.be/xsd/")
                xmlInstitution.Text = strInstitution
                xmlLocalisation.appendChild xmlInstitution
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            
            If strBuilding <> "" Then
                Set xmlBuilding = dom.createNode(NODE_ELEMENT, "storage:Building", "http://darwin.naturalsciences.be/xsd/")
                xmlBuilding.Text = strBuilding
                xmlLocalisation.appendChild xmlBuilding
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            
            If strFloor <> "" Then
                Set xmlFloor = dom.createNode(NODE_ELEMENT, "storage:Floor", "http://darwin.naturalsciences.be/xsd/")
                xmlFloor.Text = strFloor
                xmlLocalisation.appendChild xmlFloor
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            
            If strRoom <> "" Then
                Set xmlRoom = dom.createNode(NODE_ELEMENT, "storage:Room", "http://darwin.naturalsciences.be/xsd/")
                xmlRoom.Text = strRoom
                xmlLocalisation.appendChild xmlRoom
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            
            If strColumn <> "" Then
                Dim strFridge As String
                strFridge = strColumn & " (Fridge Or Drawer)"
                Set xmlColumn = dom.createNode(NODE_ELEMENT, "storage:Column", "http://darwin.naturalsciences.be/xsd/")
                xmlColumn.Text = strFridge
                xmlLocalisation.appendChild xmlColumn
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            
            If strBox <> "" Then
                Set xmlBox = dom.createNode(NODE_ELEMENT, "storage:Box", "http://darwin.naturalsciences.be/xsd/")
                xmlBox.Text = strBox
                xmlLocalisation.appendChild xmlBox
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            
            If strPosition <> "" Then
                Set xmlPosition = dom.createNode(NODE_ELEMENT, "storage:Position", "http://darwin.naturalsciences.be/xsd/")
                xmlPosition.Text = strPosition
                xmlLocalisation.appendChild xmlPosition
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
            
            If str2DBarcode <> "" Then
                Set xmlBarcode = dom.createNode(NODE_ELEMENT, "storage:Barcode", "http://darwin.naturalsciences.be/xsd/")
                xmlBarcode.Text = str2DBarcode
                xmlLocalisation.appendChild xmlBarcode
                xmlLocalisation.appendChild dom.createTextNode(vbCrLf + Space$(12))
            End If
        
        End If

        If Not IsEmpty(strAdditionalID) And Not IsNull(strAdditionalID) And strAdditionalID <> "" Then
            
            Set xmlCodes = dom.createNode(NODE_ELEMENT, "storage:Codes", "http://darwin.naturalsciences.be/xsd/")
            xmlStorage.appendChild xmlCodes
            xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlCodes.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlCode = dom.createNode(NODE_ELEMENT, "storage:Code", "http://darwin.naturalsciences.be/xsd/")
            xmlCodes.appendChild xmlCode
            xmlCodes.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))

            Set xmlCodeType = dom.createNode(NODE_ELEMENT, "storage:Type", "http://darwin.naturalsciences.be/xsd/")
            xmlCodeType.Text = "Additional ID"
            xmlCode.appendChild xmlCodeType
            xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlCodeValue = dom.createNode(NODE_ELEMENT, "storage:Value", "http://darwin.naturalsciences.be/xsd/")
            xmlCodeValue.Text = strAdditionalID
            xmlCode.appendChild xmlCodeValue
            xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
        End If
        
        If Not IsEmpty(strTestFill) And Not IsNull(strTestFill) And strTestFill <> "" Then
        
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
            
            If Not IsEmpty(strPreservation) And Not IsNull(strPreservation) And strPreservation <> "" Then
            
                Set xmlDNAPreservation = dom.createNode(NODE_ELEMENT, "dna:Preservation", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlDNAPreservation.Text = strPreservation
                xmlDNASample.appendChild xmlDNAPreservation
                xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
                
            End If
            
            If Not IsEmpty(strExtractionStaff) And Not IsNull(strExtractionStaff) And strExtractionStaff <> "" Then
            
                Set xmlExtractionStaff = dom.createNode(NODE_ELEMENT, "dna:ExtractionStaff", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlExtractionStaff.Text = strExtractionStaff
                xmlDNASample.appendChild xmlExtractionStaff
                xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
                
            End If
            
            If strExtractionDate <> "" And IsDate(strExtractionDate) Then
            
                Set xmlDNAExtractionDate = dom.createNode(NODE_ELEMENT, "dna:ExtractionDate", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlDNAExtractionDate.Text = strExtractionDate
                xmlDNASample.appendChild xmlDNAExtractionDate
                xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
                
            End If
                    
            If Not IsEmpty(strExtractionMethod) And Not IsNull(strExtractionMethod) And strExtractionMethod <> "" Then
            
                Set xmlDNAExtractionMethod = dom.createNode(NODE_ELEMENT, "dna:ExtractionMethod", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlDNAExtractionMethod.Text = strExtractionMethod
                xmlDNASample.appendChild xmlDNAExtractionMethod
                xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
                
            End If
            
            If Not IsEmpty(strAbs) And Not IsNull(strAbs) And strAbs <> "" Then
            
                Set xmlDNAROfAbsorbance = dom.createNode(NODE_ELEMENT, "dna:RatioOfAbsorbance260_280", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlDNAROfAbsorbance.Text = strAbs
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
            
            If Not IsEmpty(strGenBank) And Not IsNull(strGenBank) And strGenBank <> "" Then
            
                Set xmlDNAAmplifications = dom.createNode(NODE_ELEMENT, "dna:Amplifications", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlDNASample.appendChild xmlDNAAmplifications
                xmlDNASample.appendChild dom.createTextNode(vbCrLf + Space$(10))
                xmlDNAAmplifications.appendChild dom.createTextNode(vbCrLf + Space$(12))
                
                Set xmlDNAAmplification = dom.createNode(NODE_ELEMENT, "dna:Amplification", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlDNAAmplifications.appendChild xmlDNAAmplification
                xmlDNAAmplifications.appendChild dom.createTextNode(vbCrLf + Space$(12))
                xmlDNAAmplification.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
                Set xmlDNASequencings = dom.createNode(NODE_ELEMENT, "dna:Sequencings", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlDNAAmplification.appendChild xmlDNASequencings
                xmlDNAAmplification.appendChild dom.createTextNode(vbCrLf + Space$(14))
                xmlDNASequencings.appendChild dom.createTextNode(vbCrLf + Space$(16))
                
                Set xmlDNASequencing = dom.createNode(NODE_ELEMENT, "dna:Sequencing", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlDNASequencings.appendChild xmlDNASequencing
                xmlDNASequencings.appendChild dom.createTextNode(vbCrLf + Space$(16))
                xmlDNASequencing.appendChild dom.createTextNode(vbCrLf + Space$(18))
                
                Set xmlDNAGenBankNumber = dom.createNode(NODE_ELEMENT, "dna:GenBankNumber", "http://www.dnabank-network.org/schemas/ABCDDNA")
                xmlDNAGenBankNumber.Text = strGenBank
                xmlDNASequencing.appendChild xmlDNAGenBankNumber
                xmlDNASequencing.appendChild dom.createTextNode(vbCrLf + Space$(18))
            
            End If
        
        End If
            
    End If
    
'Exit_XMLExtensionDNA:
'
'        Exit Sub
'
'Err_XMLExtensionDNA:
'
'        MsgBox prompt:="An error occured in sub XMLExtensionDNA." & vbCrLf & _
'                       "Error Number: " & Err.Number & "." & vbCrLf & _
'                       "Error description: " & Err.Description & ".", _
'                Buttons:=vbCritical, _
'                Title:="Error in sub XMLExtensionDNA"
'        Resume Exit_XMLExtensionDNA

End Sub

'**********************************************************************************
'| Purpose: internal functions
'**********************************************************************************

'Create a comparison tool for used headings and supported headings
'-----------------------------------------------------------------------------------------------------------
Public Function CheckHeaders(ByRef check As Boolean) As Boolean

    On Error Resume Next
    
    Dim searchTerm, findTerm As Boolean, i As Integer, j As Integer, k As Integer
    Dim LastCSpec As Integer, LastCSample As Integer, LastCDNA As Integer
    Dim missing_spec As String, missing_sample As String, missing_dna As String
    Dim ID_record As Long, ID_sample As Long, ID_DNA As Long, ID As Long
    Dim FindSpec As Long, FindSample As Long, FindDNA As Long
    Dim records_missing As Integer, sample_missing As Integer, dna_missing As Integer
    Dim MsgRecord As String, MsgSample As String, MsgDNA As String, MsgIntro As String, Msg As String
    
    DefineHeadings
    
    Erase SpecHeadings_compared
    Erase SampleHeadings_compared
    Erase DNAHeadings_compared

    'RECORDS
    If FeuilleExiste("SPECIMEN") Then
        
        records_missing = 0
        LastCSpec = Application.Sheets("SPECIMEN").Cells(2, Columns.Count).End(xlToLeft).Column
        j = 1
        For i = 1 To LastCSpec
            'Check if a value exists in the Array
            searchTerm = Trim(Application.Sheets("SPECIMEN").Cells(2, i).Value)
            For k = LBound(SpecHeadings) To UBound(SpecHeadings)
                If SpecHeadings(k) = searchTerm Then
                    findTerm = True
                    Exit For
                Else:
                    findTerm = False
                End If
            Next k
                                
            If findTerm = False Then
                ReDim Preserve SpecHeadings_compared(1 To j)
                SpecHeadings_compared(j) = Trim(Application.Sheets("SPECIMEN").Cells(2, i).Value)
                j = j + 1
            End If
        Next i
    
        FindSpec = Application.Sheets("SPECIMEN").Rows(2).Find(what:="specimenID", lookAt:=xlWhole).Column
        If FindSpec = 0 Then
            ID_record = 0
        Else:
            ID_record = 1
        End If
    
        missing_spec = ""
        If UBound(SpecHeadings_compared) > 0 Then
            For i = LBound(SpecHeadings_compared) To UBound(SpecHeadings_compared)
                missing_spec = missing_spec & SpecHeadings_compared(i) & vbCrLf
            Next i
        End If

    Else:
        records_missing = 1
    
    End If
            
    'SAMPLE
    If FeuilleExiste("SAMPLE") Then
        
        sample_missing = 0
        LastCSample = Application.Sheets("SAMPLE").Cells(2, Columns.Count).End(xlToLeft).Column
        j = 1
        For i = 1 To LastCSample
            'Check if a value exists in the Array
            searchTerm = Trim(Application.Sheets("SAMPLE").Cells(2, i).Value)
            For k = LBound(SampleHeadings) To UBound(SampleHeadings)
                If SampleHeadings(k) = searchTerm Then
                    findTerm = True
                    Exit For
                Else:
                    findTerm = False
                End If
            Next k
                                
            If findTerm = False Then
                ReDim Preserve SampleHeadings_compared(1 To j)
                SampleHeadings_compared(j) = Trim(Application.Sheets("SAMPLE").Cells(2, i).Value)
                j = j + 1
            End If
        Next i
    
        FindSample = Application.Sheets("SAMPLE").Rows(2).Find(what:="sampleID", lookAt:=xlWhole).Column
        If FindSample = 0 Then
            ID_sample = 0
        Else:
            ID_sample = 1
        End If
    
        missing_sample = ""
        If UBound(SampleHeadings_compared) > 0 Then
            For i = LBound(SampleHeadings_compared) To UBound(SampleHeadings_compared)
                missing_sample = missing_sample & SampleHeadings_compared(i) & vbCrLf
            Next i
        End If

    Else:
        sample_missing = 1
    
    End If
        
    'DNA
    If FeuilleExiste("DNA") Then
        
        dna_missing = 0
        LastCDNA = Application.Sheets("DNA").Cells(2, Columns.Count).End(xlToLeft).Column
        j = 1
        For i = 1 To LastCDNA
            'Check if a value exists in the Array
            searchTerm = Trim(Application.Sheets("DNA").Cells(2, i).Value)
            For k = LBound(DNAHeadings) To UBound(DNAHeadings)
                If DNAHeadings(k) = searchTerm Then
                    findTerm = True
                    Exit For
                Else:
                    findTerm = False
                End If
            Next k
                                
            If findTerm = False Then
                ReDim Preserve DNAHeadings_compared(1 To j)
                DNAHeadings_compared(j) = Trim(Application.Sheets("DNA").Cells(2, i).Value)
                j = j + 1
            End If
        Next i
        
        FindDNA = Application.Sheets("DNA").Rows(2).Find(what:="dnaID", lookAt:=xlWhole).Column
        If FindDNA = 0 Then
            ID_DNA = 0
        Else:
            ID_DNA = 1
        End If
    
        missing_dna = ""
        If UBound(DNAHeadings_compared) > 0 Then
            For i = LBound(DNAHeadings_compared) To UBound(DNAHeadings_compared)
                missing_dna = missing_dna & DNAHeadings_compared(i) & vbCrLf
            Next i
        End If
    
    Else:
        dna_missing = 1
    
    End If
        
    'Construction du message d'erreur
    MsgRecord = ""
    If records_missing = 1 Then
        'Bloque si pas de feuille RECORDS
        CheckHeaders = False
        MsgBox "There must be a sheet named 'SPECIMEN'. Please, rename the sheet that contains information about your specimens and run the program again."
        Exit Function
    Else:
        If ID_record = 0 Then
            'Bloque si pas d'ID + rappel de vérifier toutes les feuilles
            CheckHeaders = False
            MsgBox "No column named 'specimenID' was found in the SPECIMEN-sheet. Please, rename the column corresponding to the IDs or add a blank column with 'specimenID' as header. Then run the program again."
            Exit Function
        Else:
            If missing_spec <> "" Then
                MsgRecord = "Some headings were not recognized in the SPECIMEN-sheet. They will not be exported in the xml abcd-formatted file.  There are listed by worksheet below. " & vbCrLf & "SPECIMEN-sheet: " & missing_spec
            End If
        End If
    End If
    
    MsgSample = ""
    If sample_missing = 1 Then
        'Bloque si pas de feuille SAMPLE
            CheckHeaders = False
            MsgBox "There must be a sheet named 'SAMPLE'. Please, rename the sheet that contains information about your samples and run the program again."
            Exit Function
    Else:
        If ID_sample = 0 Then
            CheckHeaders = False
            MsgBox "No column named 'sampleID' was found in the SAMPLE-sheet. Please, rename the column corresponding to the IDs or add a blank column with 'sampleID' as header. The DNA-sheet must also contain a column for IDs named 'dnaID', check if it is present. Then run the program again."
            Exit Function
        Else:
            If missing_sample <> "" Then
                MsgSample = "Some headings were not recognized in the SAMPLE-sheet. They will not be exported in the xml abcd-formatted file.  There are listed by worksheet below. " & vbCrLf & "SAMPLE-sheet: " & missing_sample
            End If
        End If
    End If
    
    MsgDNA = ""
    If dna_missing = 1 Then
        'Bloque si pas de feuille RECORDS
        CheckHeaders = False
        MsgBox "There must be a sheet named 'DNA'. Please, rename the sheet that contains information about your DNA-extracts and run the program again."
        Exit Function
    Else:
        If ID_DNA = 0 Then
            CheckHeaders = False
            MsgBox "No column named 'dnaID' was found in the DNA-sheet. Please, rename the column corresponding to the IDs or add a blank column with 'dnaID' as header. Then run the program again."
            Exit Function
        Else:
            If missing_dna <> "" Then
                MsgDNA = "Some headings were not recognized in the DNA-sheet. They will not be exported in the xml abcd-formatted file.  There are listed by worksheet below. " & vbCrLf & "DNA-sheet: " & missing_dna
            End If
        End If
    End If
    
    'Decision sur l'arrêt de l'export et affichage alternatif si juste RECORDS ou si SAMPLE/DNA aussi présentes
    If check = True Then
        If MsgRecord <> "" Or MsgSample <> "" Or MsgDNA <> "" Then
            MsgBox "Some headings were not recognized. They will not be exported in the xml abcd-formatted file." & vbCrLf & vbCrLf & _
            "Unrecognized headings are listed by worksheet below: " _
            & vbCrLf & "=> SPECIMEN-sheet: " & vbCrLf & missing_spec _
            & vbCrLf & "=> SAMPLE-sheet: " & vbCrLf & missing_sample _
            & vbCrLf & "=> DNA-sheet: " & vbCrLf & missing_dna
            
            CheckHeaders = False
            Exit Function
        Else:
            CheckHeaders = True
            MsgBox ("All headers were recognized!")
        End If
    ElseIf check = False Then
        If MsgRecord <> "" Or MsgSample <> "" Or MsgDNA <> "" Then
            Msg = "Some headings were not recognized. They will not be exported in the xml abcd-formatted file.  Click OK if you wish continue the export anyway, or Cancel to stop the program." & vbCrLf & _
            "Unrecognized headings are listed by worksheet below: " _
            & vbCrLf & "=> SPECIMEN-sheet: " & vbCrLf & missing_spec _
            & vbCrLf & "=> SAMPLE-sheet: " & vbCrLf & missing_sample _
            & vbCrLf & "=> DNA-sheet: " & vbCrLf & missing_dna
            
            If MsgBox(prompt:=Msg, Buttons:=vbOKCancel) = vbCancel Then
                CheckHeaders = False
                Exit Function
            Else:
                CheckHeaders = True
            End If
        Else:
            CheckHeaders = True
        End If
    End If
End Function

'Check if a worksheet already exists
'---------------------------------------------------
Public Function FeuilleExiste(strFeuille) As Boolean
    On Error Resume Next
    FeuilleExiste = Not (Application.Sheets(strFeuille) Is Nothing)
End Function

'Create hash code for record without ID
'--------------------------------------------------------
Public Function BASE64SHA1(ByVal sTextToHash As String) As String

    Dim asc As Object
    Dim enc As Object
    Dim TextToHash() As Byte
    Dim SharedSecretKey() As Byte
    Dim bytes() As Byte
    Const cutoff As Integer = 5

    Set asc = CreateObject("System.Text.UTF8Encoding")
    Set enc = CreateObject("System.Security.Cryptography.HMACSHA1")

    TextToHash = asc.GetBytes_4(sTextToHash)
    SharedSecretKey = asc.GetBytes_4(sTextToHash)
    enc.Key = SharedSecretKey

    bytes = enc.ComputeHash_2((TextToHash))
    BASE64SHA1 = EncodeBase64(bytes)
    BASE64SHA1 = Left(BASE64SHA1, cutoff)

    Set asc = Nothing
    Set enc = Nothing

End Function

Private Function EncodeBase64(ByRef arrData() As Byte) As String

    Dim objXML As Object
    Dim objNode As Object

    Set objXML = CreateObject("MSXML2.DOMDocument")
    Set objNode = objXML.createElement("b64")

    objNode.DataType = "bin.base64"
    objNode.nodeTypedValue = arrData
    EncodeBase64 = objNode.Text

    Set objNode = Nothing
    Set objXML = Nothing

End Function

'Make copy of each sheets at the end of the sheets for structure rework instead of working
'         directly in the working sheet (with the risk of losing some vital infos ;) )
'-------------------------------------------------------------------------------------------------------------------------------------------------
Private Function CopySheetsForRework() As Boolean

On Error GoTo Err_CopySheetsForRework

Dim LastR As Long, LastC As Long, LastRSample As Long, LastCSample As Long, LastRDNA As Long, LastCDNA As Long
'Dim SpecRange As Range, SampleRange As Range, DNARange As Range
Dim R As Long
Dim sheetcount As Integer
Dim c As Range
Dim CellString As String
Dim cl As Variant
Dim recordsheet As Boolean

DefineHeadings

If FeuilleExiste("SPECIMEN") Then
    
    If FeuilleExiste("cSPECIMEN") Then
        Sheets("cSPECIMEN").Delete
    End If
    
    sheetcount = Sheets.Count
    
    'Copy each sheet without VBA code behind
    Sheets.Add After:=Sheets(Sheets.Count)
    Sheets(sheetcount + 1).Name = "cSPECIMEN"
    Sheets("SPECIMEN").Select
    Cells.Copy
    Sheets("cSPECIMEN").Cells(1, 1).PasteSpecial xlPasteValues
    
    'Delete uneeded headings
    Application.Sheets("cSPECIMEN").Select
    Rows("1").Select
    Selection.Delete Shift:=xlShiftUp
    
    LastC = Application.Sheets("cSPECIMEN").Cells(1, Columns.Count).End(xlToLeft).Column
    LastR = Application.Sheets("cSPECIMEN").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
'    Set SpecRange = Application.Sheets("cSPECIMEN").Range(Cells(1, 1), Cells(LastR, LastC))

    'Delete empty lines
    For R = LastR To 2 Step -1
        For Each c In Application.Sheets("cSPECIMEN").Range(Cells(R, 1), Cells(R, LastC))
            CellString = CellString & Trim(c.Value)
        Next c
        'Add BASH CODE as unique ID
        If CellString = "" Then
            Application.Sheets("cSPECIMEN").Rows(R).Delete
        ElseIf (Application.Sheets("cSPECIMEN").Cells(R, Sheets("cSPECIMEN").Rows(1).Find(specimenID, lookAt:=xlWhole).Column).Value = "") Then
                Application.Sheets("cSPECIMEN").Cells(R, Sheets("cSPECIMEN").Rows(1).Find(specimenID, lookAt:=xlWhole).Column).Value = "hash-" & BASE64SHA1(CellString)
        End If
        CellString = ""
    Next R
    
    'Trim each cell
        'Loop through cells removing excess spaces
        For Each cl In Application.Worksheets("cSPECIMEN").UsedRange
            If Len(cl) > Len(WorksheetFunction.Trim(cl)) Then
                If InStr(1, WorksheetFunction.Trim(cl), "=") Then
                    cl.Value = Trim$(Mid$(WorksheetFunction.Trim(cl), 2))
                Else
                    cl.Value = WorksheetFunction.Trim(cl)
                End If
            End If
        Next cl
    
    recordsheet = True

Else:
    recordsheet = False

End If

If FeuilleExiste("SAMPLE") Then
    
    If FeuilleExiste("cSAMPLE") Then
        Sheets("cSAMPLE").Delete
    End If
    
    sheetcount = Sheets.Count
    
    'Copy each sheet without VBA code behind
    Sheets.Add After:=Sheets(Sheets.Count)
    Sheets(sheetcount + 1).Name = "cSAMPLE"
    Sheets("SAMPLE").Select
    Cells.Copy
    With Sheets("cSAMPLE").Cells(1, 1)
         .PasteSpecial xlPasteFormats
         .PasteSpecial xlPasteValues
    End With
    
    'Delete uneeded headings
    Application.Sheets("cSAMPLE").Select
    Rows("1").Select
    Selection.Delete Shift:=xlShiftUp

    LastCSample = Application.Sheets("cSAMPLE").Cells(1, Columns.Count).End(xlToLeft).Column
    LastRSample = Application.Sheets("cSAMPLE").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
'    Set SampleRange = Application.Sheets("cSAMPLE").Range(Cells(1, 1), Cells(LastRSample, LastCSample))

    'Delete empty lines
    For R = LastRSample To 2 Step -1
        For Each c In Application.Sheets("cSAMPLE").Range(Cells(R, 1), Cells(R, LastCSample))
            CellString = CellString & Trim(c.Value)
            'Add BASH CODE as unique ID
        Next c
        If CellString = "" Then
            Application.Sheets("cSAMPLE").Rows(R).Delete
        ElseIf (Application.Sheets("cSAMPLE").Cells(R, Sheets("cSAMPLE").Rows(1).Find(sampleID, lookAt:=xlWhole).Column).Value = "") Then
                Application.Sheets("cSAMPLE").Cells(R, Sheets("cSAMPLE").Rows(1).Find(sampleID, lookAt:=xlWhole).Column).Value = "hash-" & BASE64SHA1(CellString)
        End If
        CellString = ""
    Next R
    
    'Trim each cell
        'Loop through cells removing excess spaces
        For Each cl In Application.Worksheets("cSAMPLE").UsedRange
            If Len(cl) > Len(WorksheetFunction.Trim(cl)) Then
                If InStr(1, WorksheetFunction.Trim(cl), "=") Then
                    cl.Value = Trim$(Mid$(WorksheetFunction.Trim(cl), 2))
                Else
                    cl.Value = WorksheetFunction.Trim(cl)
                End If
            End If
        Next cl

End If

If FeuilleExiste("DNA") Then
    
    If FeuilleExiste("cDNA") Then
        Sheets("cDNA").Delete
    End If
    
    sheetcount = Sheets.Count
    
    'Copy each sheet without VBA code behind
    Sheets.Add After:=Sheets(Sheets.Count)
    Sheets(sheetcount + 1).Name = "cDNA"
    Sheets("DNA").Select
    Cells.Copy
    With Sheets("cDNA").Cells(1, 1)
         .PasteSpecial xlPasteFormats
         .PasteSpecial xlPasteValues
    End With
    
    'Delete uneeded headings
    Application.Sheets("cDNA").Select
    Rows("1").Select
    Selection.Delete Shift:=xlShiftUp

    LastCDNA = Application.Sheets("cDNA").Cells(1, Columns.Count).End(xlToLeft).Column
    LastRDNA = Application.Sheets("cDNA").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
'    Set DNARange = Application.Sheets("cDNA").Range(Cells(1, 1), Cells(LastRDNA, LastCDNA))

    'Delete empty lines
    For R = LastRDNA To 2 Step -1
        For Each c In Application.Sheets("cDNA").Range(Cells(R, 1), Cells(R, LastCDNA))
            CellString = CellString & Trim(c.Value)
            'Add BASH CODE as unique ID
        Next c
        If CellString = "" Then
            Application.Sheets("cDNA").Rows(R).Delete
        ElseIf (Application.Sheets("cDNA").Cells(R, Sheets("cDNA").Rows(1).Find(dnaID, lookAt:=xlWhole).Column).Value = "") Then
                Application.Sheets("cDNA").Cells(R, Sheets("cDNA").Rows(1).Find(dnaID, lookAt:=xlWhole).Column).Value = "hash-" & BASE64SHA1(CellString)
        End If
        CellString = ""
    Next R
    
    'Trim each cell
        'Loop through cells removing excess spaces
        For Each cl In Application.Worksheets("cDNA").UsedRange
            If Len(cl) > Len(WorksheetFunction.Trim(cl)) Then
                If InStr(1, WorksheetFunction.Trim(cl), "=") Then
                    cl.Value = Trim$(Mid$(WorksheetFunction.Trim(cl), 2))
                Else
                    cl.Value = WorksheetFunction.Trim(cl)
                End If
            End If
        Next cl

End If

If recordsheet = True Then
    CopySheetsForRework = True
Else:
    GoTo Err2_CopySheetsForRework:
End If

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

Err2_CopySheetsForRework:
    
    CopySheetsForRework = False
    MsgBox prompt:="There is no sheet named SPECIMEN in your file. Please, the worksheet that contains your data should be renamed SPECIMEN.", _
            Buttons:=vbCritical, _
            Title:="No SPECIMEN-sheet in your file"
    Resume Exit_CopySheetsForRework

End Function

'| Check time format
'-------------------------------
Function IsTime(Heure$) As Boolean
Dim HeureValide
IsTime = True
On Error GoTo MauvaiseHeure
HeureValide = TimeValue(Heure)
Exit Function
MauvaiseHeure:
IsTime = False
End Function

'| Purpose: Convert D(egree)M(inute)S(econd) Latitude or Longitude into its corresponding decimal value
'| Parameters: dmsLatLong: string representing the dms latitude or longitude
'|                        LatOrLong: boolean - true for a latitude, false for a longitude
'| Returns: A decimal latitude or longitude
'----------------------------------------------------------------------------------------------------------------------------------------------------
Public Function ConvertDMSToDecimal(dmsLatLong As Range, LatOrLong As Boolean, rowCounter As Long, check As Boolean) As Variant

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
    
    If LatOrLong = True Then
        LatLongitude = "Latitude"
    Else:
        LatLongitude = "Longitude"
    End If
    
    If check = False Then
        correspondingV = Application.Sheets("cSPECIMEN").Rows("1:3").Find(LatLongitude, lookAt:=xlWhole).Column
        correspondingH = dmsLatLong.Row
        Set correspondingC = Application.Sheets("cSPECIMEN").Cells(correspondingH, correspondingV)
    ElseIf check = True Then
        correspondingV = Application.Sheets("SPECIMEN").Rows("1:3").Find(LatLongitude, lookAt:=xlWhole).Column
        correspondingH = dmsLatLong.Row
        Set correspondingC = Application.Sheets("SPECIMEN").Cells(correspondingH, correspondingV)
    End If
    
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

'**********************************************************************************
'| Subs for internal purpose
'**********************************************************************************
'Trim all cells
'--------------------
'Sub DoTrim()
''    Dim aCell As Range
''    Dim wsh As Worksheet
'    Dim cl As Variant
'
'    Application.ScreenUpdating = False
'
'     'Loop through cells removing excess spaces
'    For Each cl In Application.Worksheets("cSPECIMEN").UsedRange
'        If Len(cl) > Len(WorksheetFunction.Trim(cl)) Then
'            cl.Value = WorksheetFunction.Trim(cl)
'        End If
'    Next cl
'
''            For Each aCell In Application.Worksheets("cSPECIMEN").UsedRange
''                If Not aCell.Value = "" And aCell.HasFormula = False Then
''                    With aCell
''                        .Value = Replace(.Value, Chr(160), "")
''                        .Value = Application.WorksheetFunction.Clean(.Value)
''                        .Value = Application.Trim(.Value)
''                    End With
''                End If
''            Next aCell
'
'    Application.ScreenUpdating = True
'    Application.StatusBar = "Done"
'End Sub

'Avoid carriage returns
'--------------------------------
Private Sub ProcessCarriageReturns()
    Dim MyRange As Range
    Application.ScreenUpdating = False
    Application.Calculation = xlCalculationManual
 
    For Each MyRange In ActiveSheet.UsedRange
        If 0 < InStr(MyRange, Chr(10)) Then
            MyRange = Replace(MyRange, Chr(10), "&#10;")
        End If
        If 0 < InStr(MyRange, Chr(13)) Then
            MyRange = Replace(MyRange, Chr(13), "&#13;")
       End If
    Next
 
    Application.ScreenUpdating = True
    Application.Calculation = xlCalculationAutomatic
End Sub


'Define Name of headings in each to map columns
'----------------------------------------------------------------------
Public Sub DefineHeadings()

'RECORDS
owner = "owner"
specimenID = "specimenID"
additionalID = "additionalID"
code = "code"
accessionNumber = "accessionNumber"
datasetName = "datasetName"
isPhysical = "isPhysical"
acquisitionType = "acquisitionType"
acquiredFrom = "acquiredFrom"
acquisitionDay = "acquisitionDay"
acquisitionMonth = "acquisitionMonth"
acquisitionYear = "acquisitionYear"

ocean = "ocean"
continent = "continent"
sea = "sea"
country = "country"
state_territory = "state_territory"
province = "province"
region = "region"
archipelago = "archipelago"
district = "district"
county = "county"
department = "department"
island = "island"
city = "city"
municipality = "municipality"
populatedPlace = "populatedPlace"
naturalSite = "naturalSite"
exactSite = "exactSite"

samplingCode = "samplingCode"
elevationInMeters = "elevationInMeters"
depthInMeters = "depthInMeters"
latitude = "latitude"
longitude = "longitude"
samplingMethod = "samplingMethod"
fixation = "fixation"
ecology = "ecology"
siteProperty_1 = "siteProperty_1"
sitePropertyValue_1 = "sitePropertyValue_1"
siteProperty_2 = "siteProperty_2"
sitePropertyValue_2 = "sitePropertyValue_2"
siteProperty_3 = "siteProperty_3"
sitePropertyValue_3 = "sitePropertyValue_3"
siteProperty_4 = "siteProperty_4"
sitePropertyValue_4 = "sitePropertyValue_4"
siteProperty_5 = "siteProperty_5"
sitePropertyValue_5 = "sitePropertyValue_5"
siteProperty_6 = "siteProperty_6"
sitePropertyValue_6 = "sitePropertyValue_6"
siteProperty_7 = "siteProperty_7"
sitePropertyValue_7 = "sitePropertyValue_7"
siteProperty_8 = "siteProperty_8"
sitePropertyValue_8 = "sitePropertyValue_8"
siteProperty_9 = "siteProperty_9"
sitePropertyValue_9 = "sitePropertyValue_9"
siteProperty_10 = "siteProperty_10"
sitePropertyValue_10 = "sitePropertyValue_10"
expedition_project = "expedition_project"
collectedBy = "collectedBy"
collectionStartDay = "collectionStartDay"
collectionStartMonth = "collectionStartMonth"
collectionStartYear = "collectionStartYear"
collectionStartTimeH = "collectionStartTimeH"
collectionStartTimeM = "collectionStartTimeM"
collectionEndDay = "collectionEndDay"
collectionEndMonth = "collectionEndMonth"
collectionEndYear = "collectionEndYear"
collectionEndTimeH = "collectionEndTimeH"
collectionEndTimeM = "collectionEndTimeM"
localityNotes = "localityNotes"
classification = "classification"
phylum = "phylum"
classis = "classis"
ordo = "ordo"
superfamilia = "superfamilia"
familia = "familia"
subfamilia = "subfamilia"
genus = "genus"
subgenus = "subgenus"
species = "species"
subspecies = "subspecies"
author_year = "author_year"
variety_form = "variety_form"
informalName = "informalName"
identificationMethod = "identificationMethod"
identificationHistory = "identificationHistory"
taxonFullName = "taxonFullName"
identifiedBy = "identifiedBy"
identificationDay = "identificationDay"
identificationMonth = "identificationMonth"
identificationYear = "identificationYear"
referenceString = "referenceString"
publicationString = "publicationString"
identificationNotes = "identificationNotes"
hostClassis = "hostClassis"
hostOrdo = "hostOrdo"
hostFamilia = "hostFamilia"
hostGenus = "hostGenus"
hostSpecies = "hostSpecies"
hostAuthor_year = "hostAuthor_year"
hostRemark = "hostRemark"
kindOfUnit = "kindOfUnit"
statusType = "statusType"
totalNumber = "totalNumber"
sex = "sex"
maleCount = "maleCount"
femaleCount = "femaleCount"
sexUnknownCount = "sexUnknownCount"
lifeStage = "lifeStage"
socialStatus = "socialStatus"
urlPicture = "urlPicture"
externalLink = "externalLink"
specimenProperty_1 = "specimenProperty_1"
specimenPropertyValue_1 = "specimenPropertyValue_1"
specimenProperty_2 = "specimenProperty_2"
specimenPropertyValue_2 = "specimenPropertyValue_2"
specimenProperty_3 = "specimenProperty_3"
specimenPropertyValue_3 = "specimenPropertyValue_3"
specimenProperty_4 = "specimenProperty_4"
specimenPropertyValue_4 = "specimenPropertyValue_4"
specimenProperty_5 = "specimenProperty_5"
specimenPropertyValue_5 = "specimenPropertyValue_5"
specimenProperty_6 = "specimenProperty_6"
specimenPropertyValue_6 = "specimenPropertyValue_6"
specimenProperty_7 = "specimenProperty_7"
specimenPropertyValue_7 = "specimenPropertyValue_7"
specimenProperty_8 = "specimenProperty_8"
specimenPropertyValue_8 = "specimenPropertyValue_8"
specimenProperty_9 = "specimenProperty_9"
specimenPropertyValue_9 = "specimenPropertyValue_9"
specimenProperty_10 = "specimenProperty_10"
specimenPropertyValue_10 = "specimenPropertyValue_10"
specimenProperty_11 = "specimenProperty_11"
specimenPropertyValue_11 = "specimenPropertyValue_11"
specimenProperty_12 = "specimenProperty_12"
specimenPropertyValue_12 = "specimenPropertyValue_12"
specimenProperty_13 = "specimenProperty_13"
specimenPropertyValue_13 = "specimenPropertyValue_13"
specimenProperty_14 = "specimenProperty_14"
specimenPropertyValue_14 = "specimenPropertyValue_14"
specimenProperty_15 = "specimenProperty_15"
specimenPropertyValue_15 = "specimenPropertyValue_15"
specimenProperty_16 = "specimenProperty_16"
specimenPropertyValue_16 = "specimenPropertyValue_16"
specimenProperty_17 = "specimenProperty_17"
specimenPropertyValue_17 = "specimenPropertyValue_17"
specimenProperty_18 = "specimenProperty_18"
specimenPropertyValue_18 = "specimenPropertyValue_18"
specimenProperty_19 = "specimenProperty_19"
specimenPropertyValue_19 = "specimenPropertyValue_19"
specimenProperty_20 = "specimenProperty_20"
specimenPropertyValue_20 = "specimenPropertyValue_20"
associatedUnitInstitution = "associatedUnitInstitution"
associatedUnitCollection = "associatedUnitCollection"
associatedUnitID = "associatedUnitID"
associationType = "associationType"
institutionStorage = "institutionStorage"
buildingStorage = "buildingStorage"
floorStorage = "floorStorage"
roomStorage = "roomStorage"
laneStorage = "laneStorage"
columnStorage = "columnStorage"
shelfStorage = "shelfStorage"
'boxStorage = "boxStorage"
'tubeStorage = "tubeStorage"
container = "container"
containerType = "containerType"
containerStorage = "containerStorage"
subcontainer = "subcontainer"
subcontainerType = "subcontainerType"
subcontainerStorage = "subcontainerStorage"
barcode = "barcode"
conservation = "conservation"
notes = "notes"

SpecHeadings = Array("owner", "specimenID", "additionalID", "code", "accessionNumber", "datasetName", "isPhysical", "acquisitionType", "acquiredFrom", _
"acquisitionDay", "acquisitionMonth", "acquisitionYear", "ocean", "continent", "sea", "country", "state_territory", "province", "region", "archipelago", _
"district", "county", "department", "island", "city", "municipality", "populatedPlace", "naturalSite", "exactSite", "samplingCode", "elevationInMeters", _
"depthInMeters", "latitude", "longitude", "samplingMethod", "fixation", "ecology", "siteProperty_1", "sitePropertyValue_1", "siteProperty_2", "sitePropertyValue_2", _
"siteProperty_3", "sitePropertyValue_3", "siteProperty_4", "sitePropertyValue_4", "siteProperty_5", "sitePropertyValue_5", "siteProperty_6", "sitePropertyValue_6", _
"siteProperty_7", "sitePropertyValue_7", "siteProperty_8", "sitePropertyValue_8", "siteProperty_9", "sitePropertyValue_9", _
"siteProperty_10", "sitePropertyValue_10", "expedition_project", "collectedBy", _
"collectionStartDay", "collectionStartMonth", "collectionStartYear", "collectionStartTimeH", "collectionStartTimeM", _
"collectionEndDay", "collectionEndMonth", "collectionEndYear", "collectionEndTimeH", "collectionEndTimeM", "localityNotes", _
"classification", "phylum", "classis", "ordo", "superfamilia", "familia", "subfamilia", "genus", "subgenus", "species", "subspecies", _
"author_year", "variety_form", "informalName", "identificationMethod", "identificationHistory", "taxonFullName", "identifiedBy", _
"identificationDay", "identificationMonth", "identificationYear", "referenceString", "publicationString", "identificationNotes", _
"hostClassis", "hostOrdo", "hostFamilia", "hostGenus", "hostSpecies", "hostAuthor_year", "hostRemark", _
"kindOfUnit", "statusType", "totalNumber", "sex", "maleCount", "femaleCount", "sexUnknownCount", "lifeStage", "socialStatus", "urlPicture", "externalLink", _
"specimenProperty_1", "specimenPropertyValue_1", "specimenProperty_2", "specimenPropertyValue_2", "specimenProperty_3", "specimenPropertyValue_3", _
"specimenProperty_4", "specimenPropertyValue_4", "specimenProperty_5", "specimenPropertyValue_5", "specimenProperty_6", "specimenPropertyValue_6", _
"specimenProperty_7", "specimenPropertyValue_7", "specimenProperty_8", "specimenPropertyValue_8", "specimenProperty_9", "specimenPropertyValue_9", _
"specimenProperty_10", "specimenPropertyValue_10", "specimenProperty_11", "specimenPropertyValue_11", "specimenProperty_12", _
"specimenPropertyValue_12", "specimenProperty_13", "specimenPropertyValue_13", "specimenProperty_14", "specimenPropertyValue_14", "specimenProperty_15", _
"specimenPropertyValue_15", "specimenProperty_16", "specimenPropertyValue_16", "specimenProperty_17", "specimenPropertyValue_17", "specimenProperty_18", _
"specimenPropertyValue_18", "specimenProperty_19", "specimenPropertyValue_19", "specimenProperty_20", "specimenPropertyValue_20", _
"associatedUnitInstitution", "associatedUnitCollection", "associatedUnitID", "associationType", "institutionStorage", "buildingStorage", "floorStorage", "roomStorage", _
"laneStorage", "columnStorage", "shelfStorage", "container", "containerType", "containerStorage", "subcontainer", "subcontainerType", "subcontainerStorage", _
"barcode", "conservation", "notes")

'SAMPLE
sampleDatasetName = "sampleDatasetName"
sampleID = "sampleID"
associatedSpecimenInstitution = "associatedSpecimenInstitution"
associatedSpecimenDataset = "associatedSpecimenDataset"
associatedspecimenID = "associatedSpecimenID"
sampleAcquiredFrom = "sampleAcquiredFrom"
partOfOrganism = "partOfOrganism"
sampleTissueType = "sampleTissueType"
samplePreparationType = "samplePreparationType"
samplePreservation = "samplePreservation"
sampleInstitutionStorage = "sampleInstitutionStorage"
sampleBuildingStorage = "sampleBuildingStorage"
sampleFloorStorage = "sampleFloorStorage"
sampleRoomStorage = "sampleRoomStorage"
sampleColumnStorage = "sampleColumnStorage"
sampleBoxStorage = "sampleBoxStorage"
sampleTubeStorage = "sampleTubeStorage"
sample2Dbarcode = "sample2Dbarcode"
sampleNotes = "sampleNotes"

SampleHeadings = Array("sampleDatasetName", "sampleID", "associatedSpecimenInstitution", "associatedSpecimenDataset", "associatedSpecimenID", _
"sampleAcquiredFrom", "partOfOrganism", "sampleTissueType", "samplePreparationType", "samplePreservation", _
"sampleInstitutionStorage", "sampleBuildingStorage", "sampleFloorStorage", "sampleRoomStorage", "sampleColumnStorage", "sampleBoxStorage", _
"sampleTubeStorage", "sample2Dbarcode", "sampleNotes")

'DNA
dnaDatasetName = "dnaDatasetName"
dnaID = "dnaID"
dnaAdditionalID = "dnaAdditionalID"
associatedSampleInstitution = "associatedSampleInstitution"
associatedSampleDataset = "associatedSampleDataset"
associatedSampleID = "associatedSampleID"
dnaConcentration = "dnaConcentration"
dnaAbsorbance260280 = "dnaAbsorbance260280"
dnaSize = "dnaSize"
extractionTissue = "extractionTissue"
extractionMethod = "extractionMethod"
digestionTime = "digestionTime"
digestionVolume = "digestionVolume"
elutionBuffer = "elutionBuffer"
elutionVolume = "elutionVolume"
extractedBy = "extractedBy"
extractionDay = "extractionDay"
extractionMonth = "extractionMonth"
extractionYear = "extractionYear"
genBank = "genBank"
dnaInstitutionStorage = "dnaInstitutionStorage"
dnaBuildingStorage = "dnaBuildingStorage"
dnaFloorStorage = "dnaFloorStorage"
dnaRoomStorage = "dnaRoomStorage"
dnaFridgeOrDrawerStorage = "dnaFridgeOrDrawerStorage"
dnaBoxStorage = "dnaBoxStorage"
dnaPositionStorage = "dnaPositionStorage"
dna2Dbarcode = "dna2Dbarcode"
dnaPreservation = "dnaStorageMedium"
dnaNotes = "dnaNotes"

DNAHeadings = Array("dnaDatasetName", "dnaID", "dnaAdditionalID", "associatedSampleInstitution", "associatedSampleDataset", "associatedSampleID", "dnaConcentration", _
"dnaAbsorbance260280", "dnaSize", "extractionTissue", "extractionMethod", "digestionTime", "digestionVolume", "elutionBuffer", "elutionVolume", "extractedBy", _
"extractionDay", "extractionMonth", "extractionYear", "genBank", "dnaInstitutionStorage", "dnaBuildingStorage", "dnaFloorStorage", "dnaRoomStorage", "dnaFridgeOrDrawerStorage", _
"dnaBoxStorage", "dnaPositionStorage", "dna2Dbarcode", "dnaStorageMedium", "dnaNotes")

End Sub

' Delete the copied sheets when all is done
'-------------------------------------------------------------
Private Sub DeleteSheetsAfterRework()

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

'Check if given Latitude or Longitude are correct
Public Sub CheckLatLong()

    On Error Resume Next
    
    Dim LastR As Integer, rowCounter As Long
    LastR = Application.Sheets("SPECIMEN").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
    If LastR < 3 Then LastR = 3
    Dim R As Long, ColLatit As Integer, ColLongit As Integer
    Dim Latit, Longit
    Dim ErrLatit, AddLatit, ErrLongit, AddLongit, sheetcount As Integer
    Dim i As Long
    
    If FeuilleExiste("SPECIMEN") Then
    
        'Créer la feuille reprenant les erreurs
        If FeuilleExiste("CheckLatLong") Then
            Application.DisplayAlerts = False
            Application.Sheets("CheckLatLong").Delete
            Application.DisplayAlerts = True
        End If
        
        sheetcount = Sheets.Count
        Sheets.Add After:=Sheets(Sheets.Count)
        Sheets(sheetcount + 1).Name = "CheckLatLong"
        Application.Sheets("CheckLatLong").Cells(1, 1).Value = "Address latitude cell"
        Application.Sheets("CheckLatLong").Cells(1, 2).Value = "Erroneous latitude value"
        Application.Sheets("CheckLatLong").Cells(1, 3).Value = "Address longitude cell"
        Application.Sheets("CheckLatLong").Cells(1, 4).Value = "Erroneous longitude value"
    
        'Supprimer le highlight des cellules erronées avant de relancer le check
        ColLatit = Application.Sheets("SPECIMEN").Cells.Find("latitude", lookAt:=xlWhole).Column
        ColLongit = Application.Sheets("SPECIMEN").Cells.Find("longitude", lookAt:=xlWhole).Column
        
        'Exit Sub si pas de colonne lat/long
        If ColLatit = 0 Or ColLongit = 0 Then
            Application.DisplayAlerts = False
            Application.Sheets("CheckLatLong").Delete
            Application.DisplayAlerts = True
            Application.Sheets("SPECIMEN").Activate
            MsgBox "No latitude or longitude to check."
            GoTo Exit_Check_Lat_Long
        End If
        
        For R = LastR To 3 Step -1
            Application.Sheets("SPECIMEN").Cells(R, ColLatit).Interior.ColorIndex = xlNone
            Application.Sheets("SPECIMEN").Cells(R, ColLongit).Interior.ColorIndex = xlNone
        Next R
    
        i = 1
        
        For rowCounter = 3 To LastR
            
            Latit = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find("latitude", lookAt:=xlWhole).Column)
            Longit = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find("longitude", lookAt:=xlWhole).Column)
        
            If (Not IsEmpty(Latit) And Not IsNull(Latit) And Latit <> "") _
                Or (Not IsEmpty(Longit) And Not IsNull(Longit) And Longit <> "") Then
                
                If IsNumeric(Latit) = True And -90 <= Latit And Latit <= 90 Then
                Else:
                    Latit = ConvertDMSToDecimal(Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find("latitude", lookAt:=xlWhole).Column), True, rowCounter, True)
                End If
                
                If IsNumeric(Longit) = True And -180 <= Longit And Longit <= 180 Then
                Else:
                    Longit = ConvertDMSToDecimal(Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find("longitude", lookAt:=xlWhole).Column), False, rowCounter, True)
                End If
    
                If IsNull(Longit) Or IsNull(Latit) Then
                    
                    i = i + 1
                    
                    If IsNull(Longit) Then
                        ErrLongit = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find("longitude", lookAt:=xlWhole).Column).Value
                        AddLongit = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find("longitude", lookAt:=xlWhole).Column).Address
                        Application.Sheets("CheckLatLong").Cells(i, 3).Value = AddLongit
                        Application.Sheets("CheckLatLong").Cells(i, 4).Value = ErrLongit
                    End If
                    
                    If IsNull(Latit) Then
                        ErrLatit = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find("latitude", lookAt:=xlWhole).Column).Value
                        AddLatit = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find("latitude", lookAt:=xlWhole).Column).Address
                        Application.Sheets("CheckLatLong").Cells(i, 1).Value = AddLatit
                        Application.Sheets("CheckLatLong").Cells(i, 2).Value = ErrLatit
                    End If
                
                End If
                
            End If
        
        Next rowCounter
        
        If FeuilleExiste("CheckLatLong") Then
            If Application.Sheets("CheckLatLong").Cells(2, 1).Value = "" Then
                Application.DisplayAlerts = False
                Application.Sheets("CheckLatLong").Delete
                Application.DisplayAlerts = True
                MsgBox "Every latitude/longitude seems correct."
            End If
        Else:
                MsgBox "Every latitude/longitude seems correct."
        End If
    
    Else:
            MsgBox "There must be a sheet named 'SPECIMEN'. Please, rename the sheet that contains information about your specimens and run the program again."
    End If
    
Exit_Check_Lat_Long:
    
    Exit Sub
    
End Sub

'Check if there are duplicates in the column IDs of the SPECIMEN-sheet, the SAMPLE-sheet and the DNA-sheet
'-----------------------------------------------------------------------------------------------------------------------------------------------------
Public Sub CheckDuplicatedID()

    On Error GoTo Err_Dupl_Spec
    
    DefineHeadings
    
    If FeuilleExiste("SPECIMEN") And FeuilleExiste("SAMPLE") And FeuilleExiste("DNA") Then

        '**SPECIMEN**
        '-------------------
        
        'Define variable
        Dim EvalRangeSpec As Range, errspec As Integer, Target As Range
        Dim LastRSpec As Integer, ColSpecID As Integer, R As Integer
        LastRSpec = Application.Sheets("SPECIMEN").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
        If LastRSpec < 3 Then LastRSpec = 3

        'Map column with ID and exit function if no such column
        ColSpecID = Application.Sheets("SPECIMEN").Rows("1:3").Find(specimenID, lookAt:=xlWhole).Column
        For R = LastRSpec To 3 Step -1
            Application.Sheets("SPECIMEN").Cells(R, ColSpecID).Interior.ColorIndex = xlNone
        Next R
    
        'Check duplicates
        errspec = 0
        For R = 2 To LastRSpec
            Set EvalRangeSpec = Application.Sheets("SPECIMEN").Columns(ColSpecID)
            Set Target = Application.Sheets("SPECIMEN").Cells(R, ColSpecID)
            If WorksheetFunction.CountIf(EvalRangeSpec, Target.Value) > 1 Then
                Target.Interior.Color = RGB(248, 66, 83)
                errspec = errspec + 1
            End If
        Next R
    
        '**SAMPLE**
        '----------------
        
        'Define variable
        Dim EvalRangeSample As Range, errsample As Integer, TargetSample As Range
        Dim LastRSample As Integer, ColSampleID As Integer
        LastRSample = Application.Sheets("SAMPLE").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
        If LastRSample < 3 Then LastRSample = 3

        'Map column with ID and exit function if no such column
        ColSampleID = Application.Sheets("SAMPLE").Rows("1:3").Find(sampleID, lookAt:=xlWhole).Column
        For R = LastRSample To 3 Step -1
            Application.Sheets("SAMPLE").Cells(R, ColSampleID).Interior.ColorIndex = xlNone
        Next R

        'Check duplicates
        errsample = 0
        For R = 2 To LastRSample
            Set EvalRangeSample = Application.Sheets("SAMPLE").Columns(ColSampleID)
            Set TargetSample = Application.Sheets("SAMPLE").Cells(R, ColSampleID)
            If WorksheetFunction.CountIf(EvalRangeSample, TargetSample.Value) > 1 Then
                TargetSample.Interior.Color = RGB(248, 66, 83)
                errsample = errsample + 1
            End If
        Next R
        
        '**DNA**
        '------------

        'Define variable
        Dim EvalRangeDNA As Range, errdna As Integer, TargetDNA As Range
        Dim LastRDNA As Integer, ColDNAID As Integer
        LastRDNA = Application.Sheets("DNA").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
        If LastRDNA < 3 Then LastRDNA = 3

        'Map column with ID and exit function if no such column
        ColDNAID = Application.Sheets("DNA").Rows("1:3").Find(dnaID, lookAt:=xlWhole).Column
        For R = LastRDNA To 3 Step -1
            Application.Sheets("DNA").Cells(R, ColDNAID).Interior.ColorIndex = xlNone
        Next R

        'Check duplicates
        errdna = 0
        For R = 2 To LastRDNA
            Set EvalRangeDNA = Application.Sheets("DNA").Columns(ColDNAID)
            Set TargetDNA = Application.Sheets("DNA").Cells(R, ColDNAID)
            If WorksheetFunction.CountIf(EvalRangeDNA, TargetDNA.Value) > 1 Then
                TargetDNA.Interior.Color = RGB(248, 66, 83)
                errdna = errdna + 1
            End If
        Next R
    
    Else:
        MsgBox "There is at least one worksheet missing. Check if the 3 required sheets are present and named as follow: 'SPECIMEN', 'SAMPLE' and 'DNA'"
        Exit Sub
    
    End If

    'If duplicates, display a warning message
    If errspec > 0 Or errsample > 0 Or errdna > 0 Then
        MsgBox "Some IDs have duplicates in at least one of your sheets. Remember that the ID is used to make the link between the SAMPLE-sheet and the SPECIMEN-sheet and between the DNA-sheet and the SAMPLE-sheet." & _
        "Duplicates could lead to a wrong link between specimen and sample or sample and dna." & vbCrLf & _
        "Check the specimenID column in the SPECIMEN-sheet, the sampleID column in the SAMPLE-sheet and the dnaID column in the DNA-sheet. Duplicates are highlighted in red." & _
        vbCrLf & "=> SPECIMEN-sheet: " & errspec & _
        vbCrLf & "=> SAMPLE-sheet: " & vbCrLf & errsample & _
        vbCrLf & "=> DNA-sheet: " & vbCrLf & errdna
    Else: MsgBox "No duplicates were detected."
    End If

Exit_Dupl_Spec:
    
    Exit Sub

Err_Dupl_Spec:
    
    MsgBox prompt:="An error occured in function Check_Duplicates_SpecimenID." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in function Check_Duplicates_SpecimenID"
    Resume Exit_Dupl_Spec

End Sub

'Check if association between sheets established thanks to ID's are recognized
'--------------------------------------------------------------------------------------------------------------
Public Sub CheckAssociation()
    
    DefineHeadings
    
    Dim R As Integer, missing_spec As Integer, missing_sample As Integer
    Dim EvalRangeSpec As Range, EvalRangeSample As Range, LookValue
    Dim LastRSpec As Integer, LastRSample As Integer, LastRDNA As Integer
    Dim AssocSpecIDCol As Integer, SpecIDCol As Integer, AssocSampleIDCol As Integer, SampleIDCol As Integer
    Dim AssocSpecID As String, AssocSampleID As String
    
    LastRSpec = Application.Sheets("SPECIMEN").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
    LastRSample = Application.Sheets("SAMPLE").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
    If LastRSample < 3 Then LastRSample = 3
    LastRDNA = Application.Sheets("SAMPLE").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
    If LastRDNA < 3 Then LastRDNA = 3
    
    AssocSpecIDCol = Application.Sheets("SAMPLE").Rows("1:3").Find("associatedSpecimenID", lookAt:=xlWhole).Column
    SpecIDCol = Application.Sheets("SPECIMEN").Rows("1:3").Find(specimenID, lookAt:=xlWhole).Column
    missing_spec = 0
    'Empty fill
    For R = LastRSample To 3 Step -1
        Application.Sheets("SAMPLE").Cells(R, AssocSpecIDCol).Interior.ColorIndex = xlNone
    Next R

    AssocSampleIDCol = Application.Sheets("DNA").Rows("1:3").Find("associatedSampleID", lookAt:=xlWhole).Column
    SampleIDCol = Application.Sheets("SAMPLE").Rows("1:3").Find(sampleID, lookAt:=xlWhole).Column
    missing_sample = 0
    'Empty fill
    For R = LastRDNA To 5 Step -1
        Application.Sheets("DNA").Cells(R, AssocSampleIDCol).Interior.ColorIndex = xlNone
    Next R
    
    'See if Assoc ID referenced in SAMPLE-sheet are also found in SPECIMEN-sheet
    For R = 3 To LastRSample
        AssocSpecID = Trim(Application.Sheets("SAMPLE").Cells(R, AssocSpecIDCol).Value)
        If AssocSpecID <> "" Then
            Set LookValue = Application.Sheets("SPECIMEN").Columns(SpecIDCol).Find(AssocSpecID, lookAt:=xlWhole)
            If LookValue Is Nothing Then
                Application.Sheets("SAMPLE").Cells(R, AssocSpecIDCol).Interior.Color = RGB(248, 66, 83)
                missing_spec = missing_spec + 1
            Else:
                If Application.Sheets("SAMPLE").Cells(R, AssocSpecIDCol).Interior.Color = RGB(248, 66, 83) Then
                    Application.Sheets("SAMPLE").Cells(R, AssocSpecIDCol).Interior.ColorIndex = xlNone
                End If
            End If
        End If
    Next R
    
    'See if Assoc ID referenced in DNA-sheet are also found in SAMPLE-sheet
    For R = 3 To LastRDNA
        AssocSampleID = Trim(Application.Sheets("DNA").Cells(R, AssocSampleIDCol).Value)
        If AssocSampleID <> "" Then
            Set LookValue = Application.Sheets("SAMPLE").Columns(SampleIDCol).Find(AssocSampleID, lookAt:=xlWhole)
            If LookValue Is Nothing Then
                Application.Sheets("DNA").Cells(R, AssocSampleIDCol).Interior.Color = RGB(248, 66, 83)
                missing_sample = missing_sample + 1
            Else:
                If Application.Sheets("DNA").Cells(R, AssocSampleIDCol).Interior.Color = RGB(248, 66, 83) Then
                    Application.Sheets("DNA").Cells(R, AssocSampleIDCol).Interior.ColorIndex = xlNone
                End If
            End If
        End If
    Next R
    
    If missing_spec > 0 Or missing_sample > 0 Then
        MsgBox "Some associatedIDs were not recognized. Remember that the associated ID is used to make the link between the SAMPLE-sheet and the SPECIMEN-sheet and between the DNA-sheet and the SAMPLE-sheet." & vbCrLf & _
        "Check the associatedSpecimenID column in the SAMPLE-sheet and the associatedSampleID column in the DNA-sheet. Unrecognized associated IDs are highlighted in red." & _
        vbCrLf & "=> SAMPLE-sheet: " & vbCrLf & missing_spec & _
        vbCrLf & "=> DNA-sheet: " & vbCrLf & missing_sample
    Else: MsgBox "All associations were recognized."
    End If

End Sub


'---------------------------------------------------------------
' Function responsible of cleansing
'---------------------------------------------------------------

Private Function CleanUp(ByVal strCellValue As String) As String

Dim regExp As New regExp

    With regExp
        .Global = True
        .MultiLine = True
        .IgnoreCase = False
    End With
    
    'Clean up multiple spaces into one
    regExp.Pattern = "\s\s+"
    If regExp.Test(strCellValue) Then
        strCellValue = regExp.Replace(strCellValue, " ")
    End If
    'Clean up start with space into empty
    regExp.Pattern = "^\s"
    If regExp.Test(strCellValue) Then
        strCellValue = regExp.Replace(strCellValue, "")
    End If
    'Clean up end with space into empty
    regExp.Pattern = "\s$"
    If regExp.Test(strCellValue) Then
        strCellValue = regExp.Replace(strCellValue, "")
    End If
    'Clean up parenthesis followed by space into parenthesis only
    regExp.Pattern = "\(\s"
    If regExp.Test(strCellValue) Then
        strCellValue = regExp.Replace(strCellValue, "(")
    End If
    'Clean up parenthesis preceeded by space into parenthesis only
    regExp.Pattern = "\s\)"
    If regExp.Test(strCellValue) Then
        strCellValue = regExp.Replace(strCellValue, ")")
    End If
    'Clean up comma preceeded by space into comma only
    regExp.Pattern = "\s\,"
    If regExp.Test(strCellValue) Then
        strCellValue = regExp.Replace(strCellValue, ",")
    End If
    'Clean up point preceeded by space into point only
    regExp.Pattern = "\s\."
    If regExp.Test(strCellValue) Then
        strCellValue = regExp.Replace(strCellValue, ".")
    End If

    CleanUp = strCellValue

End Function

