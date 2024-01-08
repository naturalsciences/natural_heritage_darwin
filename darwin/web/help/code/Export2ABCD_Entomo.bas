Attribute VB_Name = "Export2ABCD"
Option Explicit

Dim specimenID, code, datasetName, accessionNumber
Dim phylum, classis, ordo, familia, genus, species, author_year, subspecies, taxonFullName, informalName, identifiedBy, identificationNotes, samplingCode
Dim continent, country, region_district, state_province, municipality, exactSite, latitude, longitude, ecology, samplingMethod, collectedBy
Dim collectionStartDay, collectionStartMonth, collectionStartYear, collectionEndDay, collectionEndMonth, collectionEndYear, localityNotes
Dim kindOfUnit, statusType, totalNumber, adultCount, larvaCount, pupaCount, maleCount, femaleCount, sexUnknownCount, socialStatus
Dim conservation, institutionStorage, buildingStorage, floorStorage, roomStorage, laneStorage, columnStorage, shelfStorage
Dim container, containerType, containerStorage, subcontainer, subcontainerType, subcontainerStorage, notes
'Dim boxStorage, tubeStorage

Dim SpecHeadings() As Variant
Dim SpecHeadings_compared() As Variant

'************************************************
'************************************************
'Reset CommandBar
'Public Sub ResetDeleteRename()
'    Application.CommandBars("Ply").Enabled = True
'    Application.CommandBars("Ply").Reset
'End Sub
'************************************************
'************************************************

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
        'Dim rowNb As Integer, specrecords As Integer
        Dim rowNb As Long, specrecords As Long
        specrecords = 0

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
        Set node = dom.createComment("Schema ABCD - Template général d'encodage pour les données zoologiques, version 01/2014)")
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
        
        LastR = Application.Sheets("cSPECIMEN").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
        
        For rowCounter = 2 To LastR
                      
            Application.StatusBar = "Processing... Please do not disturb... Exported rows: " & rowCounter - 1
            DoEvents
          
            Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
            node.appendChild subnode
            node.appendChild dom.createTextNode(vbCrLf + Space$(4))
            subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
            XMLSpecID dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLIdentification dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLKindOfUnit dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLSpecimenUnit dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLGather dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLMeasurements dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLNotes dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLExtensionStorage dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            
            rowNb = rowCounter - 1

        Next rowCounter
    
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
If rowNb > 0 Then
DefineFileName:         strPath = Application.GetSaveAsFilename(InitialFileName:="export.xml", FileFilter:="XML Files (*.xml), *.xml", Title:="Select where to save your file")
    If strPath <> False Then
        ' Save the file at the location provided with the name provided
        dom.Save strPath
        MsgBox "Your output file was successfully created based on the SPECIMEN-sheet, with " & rowNb & " records."
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
    
    If Err.Number = -2147024891 Then
        MsgBox prompt:="You don't have the rights to save the file " & strPath & " on the location selected." & vbCrLf & _
                        "Please provide an other location.", Title:="No Sufficient rights", Buttons:=vbExclamation
        Resume DefineFileName
    Else:
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
'DataSets/DataSet/Units/Unit/Identifications
' Code totaly refactored by Paul-André Duchesne (Royal belgian Institute for natural Sciences) on the 2015-12-22
Private Sub XMLIdentification(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

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
    Dim taxonomy(7)
        taxonomy(1) = "Phylum"
        taxonomy(2) = "Class"
        taxonomy(3) = "Order"
        taxonomy(4) = "Family"
        taxonomy(5) = "Genus"
        taxonomy(6) = "Species"
        taxonomy(7) = "Subspecies"
    
    Dim rank(7) As String
        rank(1) = "phylum"
        rank(2) = "classis"
        rank(3) = "ordo"
        rank(4) = "familia"
        rank(5) = "genus"
        rank(6) = "species"
        rank(7) = "subspecies"
    
    ' >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    ' Added/Modified the 2015/12/22
    '-----------------------------------------------
    ' New variable definition
    '-----------------------------------------------
    Dim rankABCDName(5) As String
        rankABCDName(1) = "phylum"
        rankABCDName(2) = "classis"
        rankABCDName(3) = "ordo"
        rankABCDName(4) = "familia"
        rankABCDName(5) = "genusgroup"
        
    Dim higherTaxaUpTo As Long
    '-----------------------------------------------
    ' Classification cleaning and definition
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Modification of code here because field doesn't exist
    ' Fix it to zoology
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    strClassification = "zoology"
    'Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(classification, lookAt:=xlWhole, MatchCase:=True).Column).Value = _
    'CleanUp(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(classification, lookAt:=xlWhole, MatchCase:=True).Column).Value)
    'strClassification = LCase(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(classification, lookAt:=xlWhole, MatchCase:=True).Column).Value)
    'If strClassification = "plantae" Then
    '    strClassification = "botanical"
    'End If
    '-----------------------------------------------
    ' Taxonomy content cleaning
    '-----------------------------------------------
    For Count = 1 To 7
        Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value = _
        CleanUp(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value)
    Next Count
    '-----------------------------------------------
    ' Higher Taxa up to count
    '-----------------------------------------------
    For Count = 1 To 7
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
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Commenting of code here because field doesn't exist
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    'Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(variety_form, lookAt:=xlWhole).Column).Value = _
    'CleanUp(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(variety_form, lookAt:=xlWhole).Column).Value)
    'strNameAddendum = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(variety_form, lookAt:=xlWhole).Column).Value
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
        If higherTaxaUpTo > 5 Then
            booComposed = True
            For Count = 5 To 7
                ' If genus is not present, it's not possible to auto-compose
                If Count = 7 And ( _
                    IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Or _
                    IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Or _
                    Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value = "" _
                    ) Then
                    booComposed = False
                    Exit For
                ' Stop the moment a field is encountered empty
                ElseIf Count <> 8 And ( _
                    IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Or _
                    IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Or _
                    Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value = "" _
                    ) Then
                    Exit For
                ' If not empty, compose the taxon full name
                ElseIf Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) And _
                        Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) And _
                        Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value <> "" Then
                    ' Split in an array the content of field
                    tempTaxonName = Split(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value)
                    'Define Parenthesis pattern
                    regExp.Pattern = "[\(\)]+"
                    ' Depending the level encountered, fill the taxon full name differently
                    Select Case Count
                        Case Is = 5
                            strTaxonFullName = strTaxonFullName & " " & tempTaxonName(0)
                        'Case Is = 8
                        '    If strClassification = "botanical" Then
                        '        If ( _
                        '            InStr(LCase(tempTaxonName(0)), "subgen.") > 0 Or _
                        '            InStr(LCase(tempTaxonName(0)), "subg.") > 0 _
                        '            ) And _
                        '            UBound(tempTaxonName) > 0 Then
                        '            strTaxonFullName = strTaxonFullName & " subgen. " & tempTaxonName(1)
                        '        ElseIf regExp.Test(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value) Then
                        '            strTaxonFullName = strTaxonFullName & " subgen. " & regExp.Replace(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value, "")
                        '        Else
                        '            strTaxonFullName = strTaxonFullName & " subgen. " & tempTaxonName(0)
                        '        End If
                        '    ElseIf Left$(tempTaxonName(0), 1) <> "(" Then
                        '        strTaxonFullName = strTaxonFullName & " (" & tempTaxonName(0) & ")"
                        '    Else
                        '        strTaxonFullName = strTaxonFullName & " " & tempTaxonName(0)
                        '    End If
                        Case Is = 6
                            strTaxonFullName = strTaxonFullName & " " & Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value
                        Case Is = 7
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
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Commenting of code here because field doesn't exist
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    'strIdRef = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(referenceString, lookAt:=xlWhole).Column).Value
    '-----------------------------------------------
    ' Determination date composants
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Commenting of code here because fields don't exist
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    '' Day
    'strDetermDD = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationDay, lookAt:=xlWhole).Column).Value
    'If strDetermDD <> "" And IsNumeric(strDetermDD) Then
    '    If strDetermDD > 31 Or strDetermDD = 0 Then
    '        strDetermDD = ""
    '    ElseIf strDetermDD < 10 Then
    '        strDetermDD = "0" & strDetermDD
    '    End If
    'Else:
    '    strDetermDD = ""
    'End If
    '' Month
    'strDetermMM = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationMonth, lookAt:=xlWhole).Column).Value
    'If strDetermMM <> "" And IsNumeric(strDetermMM) Then
    '    If strDetermMM > 12 Or strDetermMM = 0 Then
    '        strDetermMM = ""
    '    ElseIf strDetermMM < 10 Then
    '        strDetermMM = "0" & strDetermMM
    '    End If
    'Else:
    '    strDetermMM = ""
    'End If
    '' Year
    'strDetermYY = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationYear, lookAt:=xlWhole).Column).Value
    'If strDetermYY <> "" And IsNumeric(strDetermYY) Then
    '    If strDetermYY > 999 Then
    '        strDetermYY = strDetermYY
    '    Else:
    '        strDetermYY = ""
    '    End If
    'Else:
    '    strDetermYY = ""
    'End If
    '' Date composition
    'If strDetermYY <> "" And strDetermMM <> "" And strDetermDD <> "" Then
    '    strIdDate = strDetermYY & "-" & strDetermMM & "-" & strDetermDD
    'ElseIf strDetermYY <> "" And strDetermMM <> "" And strDetermDD = "" Then
    '    strIdDate = strDetermYY & "-" & strDetermMM
    'ElseIf strDetermYY <> "" And strDetermMM = "" And strDetermDD = "" Then
    '    strIdDate = strDetermYY
    'Else:
    '    strIdDate = ""
    'End If
    '-----------------------------------------------
    ' Identification method
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Commenting of code here because field doesn't exist
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    'strIdentificationMethod = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationMethod, lookAt:=xlWhole).Column).Value
    '-----------------------------------------------
    ' Taxon comments
    '-----------------------------------------------
    strTaxoComment = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationNotes, lookAt:=xlWhole).Column).Value
    '-----------------------------------------------
    ' Identification History - sort of comment
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Commenting of code here because field doesn't exist
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    'strIdentificationHistory = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationHistory, lookAt:=xlWhole).Column).Value
    ''strOldGenus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(oldGenus, lookAt:=xlWhole).Column).Value
    ''strOldSubgenus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(oldSubgenus, lookAt:=xlWhole).Column).Value
    ''If strOldGenus <> "" And strOldSubgenus <> "" Then
    ''    strIdentificationHistory = "Old genus: " & strOldGenus & "; old subgenus: " & strOldSubgenus & vbCrLf & strIdentificationHistory
    ''ElseIf strOldGenus <> "" And strOldSubgenus = "" Then
    ''    strIdentificationHistory = "Old genus: " & strOldGenus & vbCrLf & strIdentificationHistory
    ''ElseIf strOldSubgenus <> "" And strOldGenus = "" Then
    ''    strIdentificationHistory = "Old sub genus: " & strOldSubgenus & vbCrLf & strIdentificationHistory
    ''End If
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
            For Count = 1 To 5
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
                For Count = 5 To 1 Step -1
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
                If higherTaxaUpTo > 5 Then
                    If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(6), lookAt:=xlWhole).Column).Value) And _
                        Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(6), lookAt:=xlWhole).Column).Value) And _
                        Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(6), lookAt:=xlWhole).Column).Value <> "" Then
                        strSpecies = Trim$(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(9), lookAt:=xlWhole).Column).Value)
                        If strAuthorYear <> "" Then
                            regExp.Pattern = strAuthorYear
                            If regExp.Test(strSpecies) Then
                                strSpecies = Trim$(regExp.Replace(strSpecies, ""))
                            End If
                        End If
                    End If
                    If Not IsEmpty(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(7), lookAt:=xlWhole).Column).Value) And _
                        Not IsNull(Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(7), lookAt:=xlWhole).Column).Value) And _
                        Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(rank(7), lookAt:=xlWhole).Column).Value <> "" Then
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
                       
            If (Not IsEmpty(strIdRef) And Not IsNull(strIdRef) And strIdRef <> "") Then
            
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
        
        If Not IsEmpty(strIdentificationHistory) And Not IsNull(strIdentificationHistory) And strIdentificationHistory <> "" Then
        
            Set xmlIdentificationHistory = dom.createNode(NODE_ELEMENT, "IdentificationHistory", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlIdentificationHistory.Text = strIdentificationHistory
            xmlIdentifications.appendChild xmlIdentificationHistory
            xmlIdentifications.appendChild dom.createTextNode(vbCrLf + Space$(8))
        
        End If
    
    End If

End Sub

'DataSets/DataSet/Units/Unit/KindOfUnit
Private Sub XMLKindOfUnit(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLKindOfUnit
    
    Dim xmlKindUnit As MSXML2.IXMLDOMElement
    Dim strKindOfUnit_class As String
    Dim strKindOfUnit_part As String
    Dim strKindOfUnit As String

    strKindOfUnit = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(kindOfUnit, lookAt:=xlWhole).Column).Value
            
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
        
    Dim strAccessionNumber As String
    
    strAccessionNumber = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(accessionNumber, lookAt:=xlWhole).Column).Value
    
    Dim strConservation As String
    Dim strTypeStatus As String
    
    strTypeStatus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(statusType, lookAt:=xlWhole).Column).Value
    strConservation = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(conservation, lookAt:=xlWhole).Column).Value
    
    Dim strTestFill As String
    strTestFill = strAccessionNumber & strTypeStatus & strConservation
    
    If Not IsEmpty(strTestFill) And Not IsNull(strTestFill) And strTestFill <> "" Then
    
        Set xmlSpecUnit = dom.createNode(NODE_ELEMENT, "SpecimenUnit", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlSpecUnit
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
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
    
        If Not IsEmpty(strConservation) And Not IsNull(strConservation) And strConservation <> "" Then
    
            Set xmlSpecimenUnitPreparations = dom.createNode(NODE_ELEMENT, "Preparations", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlSpecUnit.appendChild xmlSpecimenUnitPreparations
            xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlSpecimenUnitPreparations.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
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

'DataSets/DataSet/Units/Unit/Gathering
Private Sub XMLGather(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLGather
    
    Dim xmlGathering As MSXML2.IXMLDOMElement
    Dim xmlGatheringCode As MSXML2.IXMLDOMElement
    Dim xmlGatheringDateTime As MSXML2.IXMLDOMElement
    Dim xmlGatheringDateTimeBegin As MSXML2.IXMLDOMElement
    Dim xmlGatheringDateTimeEnd As MSXML2.IXMLDOMElement
    Dim xmlGatheringAgents As MSXML2.IXMLDOMElement
    Dim xmlGatheringAgent As MSXML2.IXMLDOMElement
    Dim xmlGatheringAgentPerson As MSXML2.IXMLDOMElement
    Dim xmlGatheringAgentFullName As MSXML2.IXMLDOMElement
    Dim xmlGatheringMethod As MSXML2.IXMLDOMElement
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
    Dim xmlGatheringBiotope As MSXML2.IXMLDOMElement
    Dim xmlGatheringBiotopeText As MSXML2.IXMLDOMElement
    Dim xmlGatheringNotes As MSXML2.IXMLDOMElement
    
    Dim strLocalityCode As String
    Dim strDateStart As String, strDateStartD As String, strDateStartM As String, strDateStartY As String
    Dim strDateEnd As String, strDateEndD As String, strDateEndM As String, strDateEndY As String
    Dim DateStart As String, DateStartString As String, DateEnd As String, DateEndString As String
    Dim DateStartText As String, DateEndText As String, DateText As String
    Dim strGatheringAgent As String
    Dim strMethod As String
    Dim strLocalityText As String
    Dim strAreaName As String
    Dim strAreaClass As String
    Dim strLatitude As String, strLongitude As String
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
    
    strGatheringAgent = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(collectedBy, lookAt:=xlWhole).Column).Value
    
    strMethod = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(samplingMethod, lookAt:=xlWhole).Column)
    
    strLocalityText = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Cells.Find(exactSite, lookAt:=xlWhole).Column)
    
    Dim Area(5)
        Area(1) = continent
        Area(2) = country
        Area(3) = state_province
        Area(4) = region_district
        Area(5) = municipality
    
    Dim AreaName(5)
        AreaName(1) = "Continent"
        AreaName(2) = "Country"
        AreaName(3) = "State or province"
        AreaName(4) = "Region or district"
        AreaName(5) = "Municipality"
        
    Dim i As Integer
    Dim celval As String, celval2 As String
    Dim rep As String, sep As String
    rep = ""
    sep = " "
    
    For i = 1 To 5
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
    
    strBiotope = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(ecology, lookAt:=xlWhole).Column).Value
    
    strGatheringNotes = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(localityNotes, lookAt:=xlWhole).Column).Value
    
    Dim strTestFill As String, strTestFill2 As String
    strTestFill = strLocalityCode & strDateStart & strDateEnd & strGatheringAgent & strMethod & strLocalityText & rep & strBiotope & strGatheringNotes
    
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
            Or Not IsEmpty(strDateEnd) And Not IsNull(strDateEnd) And strDateEnd <> "" Then
        
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
                            
            If Not IsEmpty(strDateEnd) And Not IsNull(strDateEnd) And strDateEnd <> "" Then
        
                Set xmlGatheringDateTimeEnd = dom.createNode(NODE_ELEMENT, "ISODateTimeEnd", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlGatheringDateTimeEnd.Text = strDateEnd
                xmlGatheringDateTime.appendChild xmlGatheringDateTimeEnd
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
        
            For i = 1 To 5
        
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
    
    Dim strTotalCount As String
    Dim strMaleCount As String
    Dim strFemaleCount As String
    Dim strSexUnknownCount As String
    Dim strAdultCount As String
    Dim strLarvaCount As String
    Dim strPupaCount As String
    Dim strSocialStatus As String
    Dim strTestFill As String
    
    strSocialStatus = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(socialStatus, lookAt:=xlWhole).Column).Value
    strTotalCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(totalNumber, lookAt:=xlWhole).Column).Value
    strMaleCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(maleCount, lookAt:=xlWhole).Column).Value
    strFemaleCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(femaleCount, lookAt:=xlWhole).Column).Value
    strSexUnknownCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(sexUnknownCount, lookAt:=xlWhole).Column).Value
    strAdultCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(adultCount, lookAt:=xlWhole).Column).Value
    strLarvaCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(larvaCount, lookAt:=xlWhole).Column).Value
    strPupaCount = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(pupaCount, lookAt:=xlWhole).Column).Value
     
    strTestFill = strTotalCount & strMaleCount & strFemaleCount & strSexUnknownCount & strAdultCount & strLarvaCount & strPupaCount & strSocialStatus
    
    If Not IsEmpty(strTestFill) And Not IsNull(strTestFill) And strTestFill <> "" Then
    
        Set xmlMeasurementsOrFacts = dom.createNode(NODE_ELEMENT, "MeasurementsOrFacts", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlMeasurementsOrFacts
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
                
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

        If Not IsEmpty(strSexUnknownCount) And Not IsNull(strSexUnknownCount) And strSexUnknownCount <> "" Then

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
            xmlMeasurementOrFactLowerValue.Text = strSexUnknownCount
            xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
            xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        End If
                    
        If Not IsEmpty(strAdultCount) And Not IsNull(strAdultCount) And strAdultCount <> "" Then

            Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
            xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))

            Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
            xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))

            Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementOrFactParameter.Text = "N adults"
            xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
            xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementOrFactLowerValue.Text = strAdultCount
            xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
            xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        End If
    
        If Not IsEmpty(strLarvaCount) And Not IsNull(strLarvaCount) And strLarvaCount <> "" Then

            Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
            xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))

            Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
            xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))

            Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementOrFactParameter.Text = "N larva"
            xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
            xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementOrFactLowerValue.Text = strLarvaCount
            xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
            xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        End If

         If Not IsEmpty(strPupaCount) And Not IsNull(strPupaCount) And strPupaCount <> "" Then

            Set xmlMeasurementOrFact = dom.createNode(NODE_ELEMENT, "MeasurementOrFact", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementsOrFacts.appendChild xmlMeasurementOrFact
            xmlMeasurementsOrFacts.appendChild dom.createTextNode(vbCrLf + Space$(8))
            xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))

            Set xmlMeasurementOrFactAtomised = dom.createNode(NODE_ELEMENT, "MeasurementOrFactAtomised", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementOrFact.appendChild xmlMeasurementOrFactAtomised
            xmlMeasurementOrFact.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))

            Set xmlMeasurementOrFactParameter = dom.createNode(NODE_ELEMENT, "Parameter", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementOrFactParameter.Text = "N pupa"
            xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactParameter
            xmlMeasurementOrFactAtomised.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlMeasurementOrFactLowerValue = dom.createNode(NODE_ELEMENT, "LowerValue", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlMeasurementOrFactLowerValue.Text = strPupaCount
            xmlMeasurementOrFactAtomised.appendChild xmlMeasurementOrFactLowerValue
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
    Dim xmlContainer As MSXML2.IXMLDOMElement
    Dim xmlContainerName As MSXML2.IXMLDOMElement
    Dim xmlContainerType As MSXML2.IXMLDOMElement
    Dim xmlContainerStorage As MSXML2.IXMLDOMElement
    Dim xmlSubcontainerName As MSXML2.IXMLDOMElement
    Dim xmlSubcontainerType As MSXML2.IXMLDOMElement
    Dim xmlSubcontainerStorage As MSXML2.IXMLDOMElement
'    Dim xmlBox As MSXML2.IXMLDOMElement
'    Dim xmlTube As MSXML2.IXMLDOMElement
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
'    Dim strBox As String
'    Dim strTube As String
    Dim strContainer As String
    Dim strContainerType As String
    Dim strContainerStorage As String
    Dim strSubcontainer As String
    Dim strSubcontainerType As String
    Dim strSubcontainerStorage As String

    
    strInstitution = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(institutionStorage, lookAt:=xlWhole).Column)
    strBuilding = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(buildingStorage, lookAt:=xlWhole).Column)
    strFloor = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(floorStorage, lookAt:=xlWhole).Column)
    strRoom = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(roomStorage, lookAt:=xlWhole).Column)
    strRow = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(laneStorage, lookAt:=xlWhole).Column)
    strColumn = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(columnStorage, lookAt:=xlWhole).Column)
    strShelf = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(shelfStorage, lookAt:=xlWhole).Column)
'    strBox = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(boxStorage, lookAt:=xlWhole).Column)
'    strTube = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(tubeStorage, lookAt:=xlWhole).Column)
    strContainer = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(container, lookAt:=xlWhole).Column)
    strContainerType = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(containerType, lookAt:=xlWhole).Column)
    strContainerStorage = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(containerStorage, lookAt:=xlWhole).Column)
    strSubcontainer = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(subcontainer, lookAt:=xlWhole).Column)
    strSubcontainerType = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(subcontainerType, lookAt:=xlWhole).Column)
    strSubcontainerStorage = Application.Sheets("cSPECIMEN").Cells(rowCounter, Sheets("cSPECIMEN").Rows(1).Find(subcontainerStorage, lookAt:=xlWhole).Column)

    
    Dim strAdditionalID As String
    Dim strCode As String

    strAdditionalID = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(code, lookAt:=xlWhole).Column).Value

    If strInstitution <> "" Or strBuilding <> "" Or strFloor <> "" Or strRoom <> "" Or strRow <> "" Or strColumn <> "" Or strShelf <> "" _
        Or strContainer <> "" Or strContainerType <> "" Or strContainerStorage <> "" Or strSubcontainer <> "" Or strSubcontainerType <> "" _
        Or strSubcontainerStorage <> "" Or strAdditionalID <> "" Then
    'Or strBox <> "" Or strTube <> ""
    
        Set xmlUnitExtension = dom.createNode(NODE_ELEMENT, "UnitExtension", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlUnitExtension
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
        
        Set xmlStorage = dom.createNode(NODE_ELEMENT, "storage:Storage", "http://darwin.naturalsciences.be/xsd/")
        xmlUnitExtension.appendChild xmlStorage
        xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
    
        If strInstitution <> "" Or strBuilding <> "" Or strFloor <> "" Or strRoom <> "" Or strRow <> "" Or strColumn <> "" Or strShelf <> "" Then
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
    
        If strAdditionalID <> "" Then

            Set xmlCodes = dom.createNode(NODE_ELEMENT, "storage:Codes", "http://darwin.naturalsciences.be/xsd/")
            xmlStorage.appendChild xmlCodes
            xmlStorage.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlCodes.appendChild dom.createTextNode(vbCrLf + Space$(12))
    
            Set xmlCode = dom.createNode(NODE_ELEMENT, "storage:Code", "http://darwin.naturalsciences.be/xsd/")
            xmlCodes.appendChild xmlCode
            xmlCodes.appendChild dom.createTextNode(vbCrLf + Space$(10))
            xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))

            Set xmlCodeType = dom.createNode(NODE_ELEMENT, "storage:Type", "http://darwin.naturalsciences.be/xsd/")
            xmlCodeType.Text = "Code"
            xmlCode.appendChild xmlCodeType
            xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
            Set xmlCodeValue = dom.createNode(NODE_ELEMENT, "storage:Value", "http://darwin.naturalsciences.be/xsd/")
            xmlCodeValue.Text = strAdditionalID
            xmlCode.appendChild xmlCodeValue
            xmlCode.appendChild dom.createTextNode(vbCrLf + Space$(12))
            
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

'**********************************************************************************
'| Purpose: internal functions
'**********************************************************************************

'Create a comparison tool for used headings and supported headings
'-----------------------------------------------------------------------------------------------------------
Public Function CheckHeaders(ByRef check As Boolean) As Boolean

    On Error Resume Next
    
    Dim searchTerm, findTerm As Boolean, i As Integer, j As Integer, k As Integer
    Dim LastCSpec As Integer
    Dim missing_spec As String
    Dim ID_record As Long
    Dim FindSpec As Long
    Dim records_missing As Integer
    Dim MsgRecord As String, Msg As String
    
    DefineHeadings
    
    Erase SpecHeadings_compared

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
    
    'Decision sur l'arrêt de l'export et affichage alternatif si juste RECORDS ou si SAMPLE/DNA aussi présentes
    If check = True Then
        If MsgRecord <> "" Then
            MsgBox "Some headings were not recognized. They will not be exported in the xml ABCD-formatted file." & vbCrLf & vbCrLf & _
            "Unrecognized headings are listed below: " _
            & vbCrLf & missing_spec
            
            CheckHeaders = False
            Exit Function
        Else:
            CheckHeaders = True
            MsgBox ("All headers were recognized!")
        End If
    ElseIf check = False Then
        If MsgRecord <> "" Then
            Msg = "Some headings were not recognized. They will not be exported in the xml ABCD-formatted file.  Click OK if you wish continue the export anyway, or Cancel to stop the program." & vbCrLf & _
            "Unrecognized headings are listed below: " _
            & vbCrLf & missing_spec
            
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

Dim LastR As Long, LastC As Long
'Dim SpecRange As Range
Dim R As Long
Dim sheetcount As Integer
Dim c As Range
Dim CellString As String

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
    DoTrim
    
    recordsheet = True

Else:
    recordsheet = False

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
Sub DoTrim()
    Dim cl As Variant
    
    Application.ScreenUpdating = False

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
    
    Application.ScreenUpdating = True
    Application.StatusBar = "Done"
End Sub

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

specimenID = "specimenID"
code = "code"
datasetName = "datasetName"
accessionNumber = "accessionNumber"
phylum = "phylum"
classis = "classis"
ordo = "ordo"
familia = "familia"
genus = "genus"
species = "species"
author_year = "author_year"
subspecies = "subspecies"
taxonFullName = "taxonFullName"
informalName = "informalName"
identifiedBy = "identifiedBy"
identificationNotes = "identificationNotes"
samplingCode = "samplingCode"
continent = "continent"
country = "country"
region_district = "region_district"
state_province = "state_province"
municipality = "municipality"
exactSite = "exactSite"
latitude = "latitude"
longitude = "longitude"
ecology = "ecology"
samplingMethod = "samplingMethod"
collectedBy = "collectedBy"
collectionStartDay = "collectionStartDay"
collectionStartMonth = "collectionStartMonth"
collectionStartYear = "collectionStartYear"
collectionEndDay = "collectionEndDay"
collectionEndMonth = "collectionEndMonth"
collectionEndYear = "collectionEndYear"
localityNotes = "localityNotes"
kindOfUnit = "kindOfUnit"
statusType = "statusType"
totalNumber = "totalNumber"
adultCount = "adultCount"
larvaCount = "larvaCount"
pupaCount = "pupaCount"
maleCount = "maleCount"
femaleCount = "femaleCount"
sexUnknownCount = "sexUnknownCount"
socialStatus = "socialStatus"
conservation = "conservation"
institutionStorage = "institutionStorage"
buildingStorage = "buildingStorage"
floorStorage = "floorStorage"
roomStorage = "roomStorage"
laneStorage = "laneStorage"
columnStorage = "columnStorage"
shelfStorage = "shelfStorage"
container = "container"
containerType = "containerType"
containerStorage = "containerStorage"
subcontainer = "subcontainer"
subcontainerType = "subcontainerType"
subcontainerStorage = "subcontainerStorage"
'boxStorage = "boxStorage"
'tubeStorage = "tubeStorage"
notes = "notes"

SpecHeadings = Array("specimenID", "code", "datasetName", "accessionNumber", "phylum", "classis", "ordo", "familia", "genus", "species", "author_year", "subspecies", "taxonFullName", "informalName", "identifiedBy", "identificationNotes", _
"samplingCode", "continent", "country", "region_district", "state_province", "municipality", "exactSite", "latitude", "longitude", "ecology", "samplingMethod", "collectedBy", "collectionStartDay", "collectionStartMonth", _
"collectionStartYear", "collectionEndDay", "collectionEndMonth", "collectionEndYear", "localityNotes", "kindOfUnit", "statusType", "totalNumber", "adultCount", "larvaCount", "pupaCount", "maleCount", _
"femaleCount", "sexUnknownCount", "socialStatus", "conservation", "institutionStorage", "buildingStorage", "floorStorage", "roomStorage", "laneStorage", "columnStorage", "shelfStorage", _
"container", "containerType", "containerStorage", "subcontainer", "subcontainerType", "subcontainerStorage", "notes")

'"boxStorage", "tubeStorage",
End Sub

' Delete the copied sheets when all is done
'-------------------------------------------------------------
Private Sub DeleteSheetsAfterRework()

On Error GoTo Err_DeleteSheetsAfterRework

If FeuilleExiste("cSPECIMEN") Then
    Sheets("cSPECIMEN").Delete
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
    
    If FeuilleExiste("SPECIMEN") Then

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
    
    Else:
        MsgBox "The SPECIMEN-sheet is missing."
        Exit Sub
    
    End If

    'If duplicates, display a warning message
    If errspec > 0 Then
        MsgBox "Some IDs have duplicates in the specimenID column. Check the specimenID column in the SPECIMEN-sheet. Duplicates are highlighted in red." & _
        vbCrLf & errspec
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

Public Sub CompleteTaxonomy()

On Error Resume Next

Dim LastR As Long
Dim rowCounter As Long

Dim strOriginPhylum As String, strOriginClass As String, strOriginOrder As String, strOriginFamily As String
Dim LinkedPhylum As String, LinkedClass As String, LinkedOrder As String
Dim RowLinkedTaxo As Integer
Dim FindPhylumSpec As Long, FindPhylumTaxo As Long, FindClassSpec As Long, FindClassTaxo As Long
Dim FindOrderSpec As Long, FindOrderTaxo As Long, FindFamilySpec As Long, FindFamilyTaxo As Long
Dim LastCSpec As Long

DefineHeadings

If FeuilleExiste("SPECIMEN") Then

    If FeuilleExiste("TAXONOMY") Then
    
        'Create columns phylum, class and order if don't exist
        FindPhylumSpec = Application.Sheets("SPECIMEN").Rows(2).Find(phylum, lookAt:=xlWhole).Column
        FindClassSpec = Application.Sheets("SPECIMEN").Rows(2).Find(classis, lookAt:=xlWhole).Column
        FindOrderSpec = Application.Sheets("SPECIMEN").Rows(2).Find(ordo, lookAt:=xlWhole).Column
        FindFamilySpec = Application.Sheets("SPECIMEN").Rows(2).Find(familia, lookAt:=xlWhole).Column
        
        FindPhylumTaxo = Application.Sheets("TAXONOMY").Rows(1).Find(phylum, lookAt:=xlWhole).Column
        FindClassTaxo = Application.Sheets("TAXONOMY").Rows(1).Find(classis, lookAt:=xlWhole).Column
        FindOrderTaxo = Application.Sheets("TAXONOMY").Rows(1).Find(ordo, lookAt:=xlWhole).Column
        FindFamilyTaxo = Application.Sheets("TAXONOMY").Rows(1).Find(familia, lookAt:=xlWhole).Column
        
        LastCSpec = Application.Sheets("SPECIMEN").Cells(2, Columns.Count).End(xlToLeft).Column

        If FindFamilySpec = 0 Then
            MsgBox "No column 'familia' recognized in the SPECIMEN-sheet."
            GoTo Exit_CompleteTaxonomy
        End If
        
        If FindPhylumSpec = 0 Then
            Application.Sheets("SPECIMEN").Columns(LastCSpec + 1).Insert
            Application.Sheets("SPECIMEN").Cells(2, (LastCSpec + 1)).Value = "phylum"
        End If
        
        If FindClassSpec = 0 Then
            Application.Sheets("SPECIMEN").Columns(LastCSpec + 2).Insert
            Application.Sheets("SPECIMEN").Cells(2, (LastCSpec + 2)).Value = "classis"
        End If
        
        If FindOrderSpec = 0 Then
            Application.Sheets("SPECIMEN").Columns(LastCSpec + 3).Insert
            Application.Sheets("SPECIMEN").Cells(2, (LastCSpec + 3)).Value = "ordo"
        End If
                
        If FindPhylumTaxo = 0 Or FindClassTaxo = 0 Or FindOrderTaxo = 0 Or FindFamilyTaxo = 0 Then
            MsgBox "To use this tool, the columns 'phylum', 'classis', 'ordo' and 'familia' have to be present in the first row of the TAXONOMY-sheet. It seems that at least one is not recognized."
        End If
        
        LastR = Application.Sheets("SPECIMEN").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
        If LastR < 3 Then LastR = 3
        
        For rowCounter = LastR To 3 Step -1
            strOriginPhylum = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find(phylum, lookAt:=xlWhole).Column)
            strOriginClass = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find(classis, lookAt:=xlWhole).Column)
            strOriginOrder = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find(ordo, lookAt:=xlWhole).Column)
            strOriginFamily = Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find(familia, lookAt:=xlWhole).Column)
        
            If strOriginPhylum = "" And strOriginClass = "" And strOriginOrder = "" Then
                If strOriginFamily <> "" Then
        
                    If Not (Application.Sheets("TAXONOMY").Cells.Find(what:=strOriginFamily, lookAt:=xlWhole)) Is Nothing Then
                
                        RowLinkedTaxo = Application.Sheets("TAXONOMY").Columns(4).Find(what:=strOriginFamily, lookAt:=xlWhole).Row
                        LinkedPhylum = Application.Sheets("TAXONOMY").Cells(RowLinkedTaxo, 1).Value
                        LinkedClass = Application.Sheets("TAXONOMY").Cells(RowLinkedTaxo, 2).Value
                        LinkedOrder = Application.Sheets("TAXONOMY").Cells(RowLinkedTaxo, 3).Value
                        
                        Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find(phylum, lookAt:=xlWhole).Column).Value = LinkedPhylum
                        Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find(classis, lookAt:=xlWhole).Column).Value = LinkedClass
                        Application.Sheets("SPECIMEN").Cells(rowCounter, Application.Sheets("SPECIMEN").Cells.Find(ordo, lookAt:=xlWhole).Column).Value = LinkedOrder
            
                    End If
                End If
            End If
        Next rowCounter
    
    Else:
        MsgBox "This file does not contain a sheet named 'TAXONOMY'."
        GoTo Exit_CompleteTaxonomy
    End If
    
Else:
    MsgBox "This file does not contain a sheet named 'SPECIMEN'."
    GoTo Exit_CompleteTaxonomy
End If

Exit_CompleteTaxonomy:
    
    Exit Sub

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

