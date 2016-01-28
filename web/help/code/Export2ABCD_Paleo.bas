Attribute VB_Name = "Export2ABCD"
Option Explicit

Dim specimenID, datasetName, accessionNumber, classification
Dim phylum, classis, ordo, superfamilia, familia, subfamilia, genus, subgenus, species, author_year, subspecies, variety_form, taxonFullName, informalName
Dim epoch, age, age_local, samplingCode, country, locality, notes

Dim SpecHeadings() As Variant
Dim SpecHeadings_compared() As Variant

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
    
    'Make copy of sheets for rework and initiate the XML file
    If CopySheetsForRework Then
    
        Dim dom As MSXML2.DOMDocument60
        Dim root As MSXML2.IXMLDOMElement
        Dim firstnode As MSXML2.IXMLDOMNode
        Dim node As MSXML2.IXMLDOMNode
        Dim attr As MSXML2.IXMLDOMAttribute
        Dim subnode As MSXML2.IXMLDOMElement
        Dim strPath As String
        Dim rowNb As Integer, specrecords As Integer
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
        Set node = dom.createComment("Schema ABCDEFG - Template général d'encodage pour les données géologiques, version 01/2014)")
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
                            
            Set subnode = dom.createNode(NODE_ELEMENT, "Unit", "http://www.tdwg.org/schemas/abcd/2.06")
            node.appendChild subnode
            node.appendChild dom.createTextNode(vbCrLf + Space$(4))
            subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
            XMLSpecID dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLIdentification dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLSpecimenUnit dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLGather dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLNotes dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            XMLExtensionEFG dom:=dom, subnode:=subnode, rowCounter:=rowCounter
            
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
    If strPath <> "False" Then
        ' Save the file at the location provided with the name provided
        dom.Save strPath
        MsgBox "Your output file was successfully created based on the SPECIMEN-sheet, with " & rowNb & " records."
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

    Exit Sub

Err_CreateXML:
    
    If Err.Number = -2147024891 Then
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
    xmlMetadataTitle.Text = "Pre-formated ABCD xml file for import into DaRWIN"
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
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Modification of code here because field doesn't exist
    ' Fix it to zoology
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    'strClassification = "zoology"
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
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Commenting of code here because field doesn't exist
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    'strIdentifier = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identifiedBy, lookAt:=xlWhole).Column).Value
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
    ' Commenting of code here because field doesn't exist
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    ' Day
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
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Commenting of code here because field doesn't exist
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    'strTaxoComment = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationNotes, lookAt:=xlWhole).Column).Value
    '-----------------------------------------------
    ' Identification History - sort of comment
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    ' Commenting of code here because field doesn't exist
    ' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    '-----------------------------------------------
    'strIdentificationHistory = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(identificationHistory, lookAt:=xlWhole).Column).Value
    'strIdentificationHistory = ""
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

'DataSets/DataSet/Units/Unit/SpecimenUnit/Acquisition, Accession, Preparations, NomenclaturalTypeDesignations
Private Sub XMLSpecimenUnit(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
'    On Error GoTo Err_XMLSpecimenUnit
    
    Dim xmlSpecUnit As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAccession As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAccessionCatalogue As MSXML2.IXMLDOMElement
    Dim xmlSpecimenUnitAccessionNumber As MSXML2.IXMLDOMElement
        
    Dim strAccessionNumber As String
    
    strAccessionNumber = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(accessionNumber, lookAt:=xlWhole).Column).Value
    
    If Not IsEmpty(strAccessionNumber) And Not IsNull(strAccessionNumber) And strAccessionNumber <> "" Then
    
        Set xmlSpecUnit = dom.createNode(NODE_ELEMENT, "SpecimenUnit", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlSpecUnit
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlSpecUnit.appendChild dom.createTextNode(vbCrLf + Space$(10))

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
    Dim xmlGatheringNamedAreas As MSXML2.IXMLDOMElement
    Dim xmlGatheringNamedArea As MSXML2.IXMLDOMElement
    Dim xmlGatheringAreaName As MSXML2.IXMLDOMElement
    Dim xmlGatheringAreaClass As MSXML2.IXMLDOMElement
    
    Dim strLocalityCode As String

    Dim strLocality As String, strCountry As String
    
    strLocalityCode = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(samplingCode, lookAt:=xlWhole).Column).Value
    strLocality = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(locality, lookAt:=xlWhole).Column).Value
    strCountry = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(country, lookAt:=xlWhole).Column).Value

    If Not IsEmpty(strLocality) And Not IsNull(strLocality) And strLocality <> "" _
        Or Not IsEmpty(strCountry) And Not IsNull(strCountry) And strCountry <> "" Then
    
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
                            
        Set xmlGatheringNamedAreas = dom.createNode(NODE_ELEMENT, "NamedAreas", "http://www.tdwg.org/schemas/abcd/2.06")
        xmlGathering.appendChild xmlGatheringNamedAreas
        xmlGathering.appendChild dom.createTextNode(vbCrLf + Space$(10))
        xmlGatheringNamedAreas.appendChild dom.createTextNode(vbCrLf + Space$(12))
                            
        If (Not IsEmpty(strCountry) And Not IsNull(strCountry) And strCountry <> "") Then
            
            Set xmlGatheringNamedArea = dom.createNode(NODE_ELEMENT, "NamedArea", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringNamedAreas.appendChild xmlGatheringNamedArea
            xmlGatheringNamedAreas.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            Set xmlGatheringAreaClass = dom.createNode(NODE_ELEMENT, "AreaClass", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringAreaClass.Text = "Country"
            xmlGatheringNamedArea.appendChild xmlGatheringAreaClass
            xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            Set xmlGatheringAreaName = dom.createNode(NODE_ELEMENT, "AreaName", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringAreaName.Text = strCountry
            xmlGatheringNamedArea.appendChild xmlGatheringAreaName
            xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(14))
        
        End If
            
        If (Not IsEmpty(strLocality) And Not IsNull(strLocality) And strLocality <> "") Then
            
            Set xmlGatheringNamedArea = dom.createNode(NODE_ELEMENT, "NamedArea", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringNamedAreas.appendChild xmlGatheringNamedArea
            xmlGatheringNamedAreas.appendChild dom.createTextNode(vbCrLf + Space$(12))
            xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            Set xmlGatheringAreaClass = dom.createNode(NODE_ELEMENT, "AreaClass", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringAreaClass.Text = "Municipality"
            xmlGatheringNamedArea.appendChild xmlGatheringAreaClass
            xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(14))
            
            Set xmlGatheringAreaName = dom.createNode(NODE_ELEMENT, "AreaName", "http://www.tdwg.org/schemas/abcd/2.06")
            xmlGatheringAreaName.Text = strLocality
            xmlGatheringNamedArea.appendChild xmlGatheringAreaName
            xmlGatheringNamedArea.appendChild dom.createTextNode(vbCrLf + Space$(14))
        
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

'DataSets/DataSet/Units/Unit/UnitExtension => efg.xsd
Private Sub XMLExtensionEFG(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

'**********************************************************************************
'Create node with information for DNA extract that concern the DNA extension in XML file
'**********************************************************************************
On Error Resume Next
'On Error GoTo Err_XMLExtensionEFG

Dim attr As MSXML2.IXMLDOMAttribute
Dim xmlUnitExtension As MSXML2.IXMLDOMElement
Dim xmlEarthScienceSpecimen As MSXML2.IXMLDOMElement
Dim xmlUnitStratigraphicDetermination As MSXML2.IXMLDOMElement
Dim xmlChronostratigraphicAttributions As MSXML2.IXMLDOMElement
Dim xmlChronostratigraphicAttribution As MSXML2.IXMLDOMElement
Dim xmlChronostratigraphicDivision As MSXML2.IXMLDOMElement
Dim xmlChronostratigraphicName As MSXML2.IXMLDOMElement

Dim strEpoch As String
Dim strAge As String
Dim strAgebis As String

strEpoch = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(epoch, lookAt:=xlWhole).Column).Value
strAge = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(age, lookAt:=xlWhole).Column).Value
strAgebis = Application.Sheets("cSPECIMEN").Cells(rowCounter, Application.Sheets("cSPECIMEN").Rows(1).Find(age_local, lookAt:=xlWhole).Column).Value

If (Not IsEmpty(strEpoch) And Not IsNull(strEpoch) And strEpoch <> "") _
    Or (Not IsEmpty(strAge) And Not IsNull(strAge) And strAge <> "") _
    Or (Not IsEmpty(strAgebis) And Not IsNull(strAgebis) And strAgebis <> "") Then

    Set xmlUnitExtension = dom.createNode(NODE_ELEMENT, "UnitExtension", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlUnitExtension
        subnode.appendChild dom.createTextNode(vbCrLf + Space$(6))
        xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
    
        Set xmlEarthScienceSpecimen = dom.createNode(NODE_ELEMENT, "efg:EarthScienceSpecimen", "http://www.synthesys.info/ABCDEFG/1.0")
        xmlUnitExtension.appendChild xmlEarthScienceSpecimen
        xmlUnitExtension.appendChild dom.createTextNode(vbCrLf + Space$(8))
        xmlEarthScienceSpecimen.appendChild dom.createTextNode(vbCrLf + Space$(10))
        
        Set xmlUnitStratigraphicDetermination = dom.createNode(NODE_ELEMENT, "efg:UnitStratigraphicDetermination", "http://www.synthesys.info/ABCDEFG/1.0")
        xmlEarthScienceSpecimen.appendChild xmlUnitStratigraphicDetermination
        xmlEarthScienceSpecimen.appendChild dom.createTextNode(vbCrLf + Space$(10))
        xmlUnitStratigraphicDetermination.appendChild dom.createTextNode(vbCrLf + Space$(12))
        
        Set xmlChronostratigraphicAttributions = dom.createNode(NODE_ELEMENT, "efg:ChronostratigraphicAttributions", "http://www.synthesys.info/ABCDEFG/1.0")
        xmlUnitStratigraphicDetermination.appendChild xmlChronostratigraphicAttributions
        xmlUnitStratigraphicDetermination.appendChild dom.createTextNode(vbCrLf + Space$(12))
        xmlChronostratigraphicAttributions.appendChild dom.createTextNode(vbCrLf + Space$(14))
                
        If Not IsEmpty(strEpoch) And Not IsNull(strEpoch) And strEpoch <> "" Then
            Set xmlChronostratigraphicAttribution = dom.createNode(NODE_ELEMENT, "efg:ChronostratigraphicAttribution", "http://www.synthesys.info/ABCDEFG/1.0")
            xmlChronostratigraphicAttributions.appendChild xmlChronostratigraphicAttribution
            xmlChronostratigraphicAttributions.appendChild dom.createTextNode(vbCrLf + Space$(14))
            xmlChronostratigraphicAttribution.appendChild dom.createTextNode(vbCrLf + Space$(16))
            Set xmlChronostratigraphicDivision = dom.createNode(NODE_ELEMENT, "efg:ChronoStratigraphicDivision", "http://www.synthesys.info/ABCDEFG/1.0")
            xmlChronostratigraphicDivision.Text = "Epoch/Serie"
            xmlChronostratigraphicAttribution.appendChild xmlChronostratigraphicDivision
            xmlChronostratigraphicAttribution.appendChild dom.createTextNode(vbCrLf + Space$(18))
            Set xmlChronostratigraphicName = dom.createNode(NODE_ELEMENT, "efg:ChronostratigraphicName", "http://www.synthesys.info/ABCDEFG/1.0")
            xmlChronostratigraphicName.Text = strEpoch
            xmlChronostratigraphicAttribution.appendChild xmlChronostratigraphicName
            xmlChronostratigraphicAttribution.appendChild dom.createTextNode(vbCrLf + Space$(18))
        End If

        If Not IsEmpty(strAge) And Not IsNull(strAge) And strAge <> "" Then
            Set xmlChronostratigraphicAttribution = dom.createNode(NODE_ELEMENT, "efg:ChronostratigraphicAttribution", "http://www.synthesys.info/ABCDEFG/1.0")
            xmlChronostratigraphicAttributions.appendChild xmlChronostratigraphicAttribution
            xmlChronostratigraphicAttributions.appendChild dom.createTextNode(vbCrLf + Space$(14))
            xmlChronostratigraphicAttribution.appendChild dom.createTextNode(vbCrLf + Space$(16))
            Set xmlChronostratigraphicDivision = dom.createNode(NODE_ELEMENT, "efg:ChronoStratigraphicDivision", "http://www.synthesys.info/ABCDEFG/1.0")
            xmlChronostratigraphicDivision.Text = "Age/Stage"
            xmlChronostratigraphicAttribution.appendChild xmlChronostratigraphicDivision
            xmlChronostratigraphicAttribution.appendChild dom.createTextNode(vbCrLf + Space$(18))
            Set xmlChronostratigraphicName = dom.createNode(NODE_ELEMENT, "efg:ChronostratigraphicName", "http://www.synthesys.info/ABCDEFG/1.0")
            xmlChronostratigraphicName.Text = strAge
            xmlChronostratigraphicAttribution.appendChild xmlChronostratigraphicName
            xmlChronostratigraphicAttribution.appendChild dom.createTextNode(vbCrLf + Space$(18))
        End If

        If Not IsEmpty(strAgebis) And Not IsNull(strAgebis) And strAgebis <> "" Then
            Set xmlChronostratigraphicAttribution = dom.createNode(NODE_ELEMENT, "efg:ChronostratigraphicAttribution", "http://www.synthesys.info/ABCDEFG/1.0")
            xmlChronostratigraphicAttributions.appendChild xmlChronostratigraphicAttribution
            xmlChronostratigraphicAttributions.appendChild dom.createTextNode(vbCrLf + Space$(14))
            xmlChronostratigraphicAttribution.appendChild dom.createTextNode(vbCrLf + Space$(16))
            Set xmlChronostratigraphicDivision = dom.createNode(NODE_ELEMENT, "efg:ChronoStratigraphicDivision", "http://www.synthesys.info/ABCDEFG/1.0")
            xmlChronostratigraphicDivision.Text = "Age/Stage local"
            xmlChronostratigraphicAttribution.appendChild xmlChronostratigraphicDivision
            xmlChronostratigraphicAttribution.appendChild dom.createTextNode(vbCrLf + Space$(18))
            Set xmlChronostratigraphicName = dom.createNode(NODE_ELEMENT, "efg:ChronostratigraphicName", "http://www.synthesys.info/ABCDEFG/1.0")
            xmlChronostratigraphicName.Text = strAgebis
            xmlChronostratigraphicAttribution.appendChild xmlChronostratigraphicName
            xmlChronostratigraphicAttribution.appendChild dom.createTextNode(vbCrLf + Space$(18))
        End If

End If

'Exit_XMLExtensionEFG:
'
'    Exit Sub
'
'Err_XMLExtensionEFG:
'
'    MsgBox prompt:="An error occured in sub XMLExtensionEFG." & vbCrLf & _
'                   "Error Number: " & Err.Number & "." & vbCrLf & _
'                   "Error description: " & Err.Description & ".", _
'            Buttons:=vbCritical, _
'            Title:="Error in sub XMLExtensionEFG"
'    Resume Exit_XMLExtensionEFG

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
        LastCSpec = Application.Sheets("SPECIMEN").Cells(1, Columns.Count).End(xlToLeft).Column
        j = 1
        For i = 1 To LastCSpec
            'Check if a value exists in the Array
            searchTerm = Trim(Application.Sheets("SPECIMEN").Cells(1, i).Value)
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
                SpecHeadings_compared(j) = Trim(Application.Sheets("SPECIMEN").Cells(1, i).Value)
                j = j + 1
            End If
        Next i
    
        FindSpec = Application.Sheets("SPECIMEN").Rows(1).Find(specimenID, lookAt:=xlWhole).Column
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
            MsgBox "No column named 'ID' was found in the SPECIMEN-sheet. Please, rename the column corresponding to the IDs or add a blank column with 'ID' as header. Then run the program again."
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
    Application.Sheets("cSPECIMEN").Cells(1, 1).PasteSpecial xlPasteValues
    Application.Sheets("cSPECIMEN").Select
    
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
'---------------------------------
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

specimenID = "ID"
datasetName = "Collection"
accessionNumber = "IG_Number"
classification = "Classification"
phylum = "phylum"
classis = "classis"
ordo = "ordo"
superfamilia = "superfamilia"
familia = "familia"
subfamilia = "subfamilia"
genus = "genus"
subgenus = "subgenus"
species = "species"
author_year = "author_year"
subspecies = "subspecies"
variety_form = "variety_form"
taxonFullName = "taxonFullName"
informalName = "informalName"
epoch = "Epoch"
age = "Age"
age_local = "Age_bis"
samplingCode = "samplingCode"
country = "Country"
locality = "Locality"
notes = "Comment"

SpecHeadings = Array("ID", "Collection", "IG_Number", "Classification", "phylum", "classis", "ordo", _
                        "superfamilia", "familia", "subfamilia", "genus", "subgenus", "species", "author_year", _
                        "subspecies", "variety_form", "taxonFullName", "informalName", _
                        "Epoch", "Age", "Age_bis", "samplingCode", "Country", "Locality", "Comment")

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
        If LastRSpec < 2 Then LastRSpec = 2

        'Map column with ID and exit function if no such column
        ColSpecID = Application.Sheets("SPECIMEN").Rows("1:3").Find(specimenID, lookAt:=xlWhole).Column
        For R = LastRSpec To 2 Step -1
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

Public Sub InsertData()

Dim CurrentWbkName As String
CurrentWbkName = ThisWorkbook.Name

Dim rowCounter As Integer
Dim LastR As Integer, LastC As Integer

Application.ScreenUpdating = False
Application.DisplayAlerts = False

If FeuilleExiste("ExportABCD") Then
    Sheets("ExportABCD").Delete
End If

Dim LocFilePath As String
Dim fd As FileDialog
'Crée une boite de dialogue de sélection de fichiers :
Set fd = Application.FileDialog(msoFileDialogFilePicker)
fd.AllowMultiSelect = False
fd.Filters.Add "Excel", "*.xls; *.xlsx, 1"

If fd.Show = -1 Then
        LocFilePath = fd.SelectedItems(1)
        MsgBox "The path is: " & LocFilePath

Else
    GoTo Exit_LocalityInsert

End If

'    If fd.SelectedItems.Count = 1 Then
'        LocFilePath = fd.SelectedItems(1)
'    Else
'    End If
Set fd = Nothing

Dim WbkData As Workbook, WbkDataName As String
Set WbkData = Workbooks.Open(Filename:=LocFilePath)
WbkDataName = WbkData.Name

If FeuilleExiste("ExportABCD") Then

    Application.Workbooks(WbkDataName).Sheets("ExportABCD").Copy After:=Application.Workbooks(CurrentWbkName).Sheets("SPECIMEN")
    Application.Workbooks(WbkDataName).Close SaveChanges:=False
    Application.Workbooks(CurrentWbkName).Activate
    
    LastR = Application.Workbooks(CurrentWbkName).Sheets("ExportABCD").Cells(Rows.Count, "A").End(xlUp).Row
    LastC = Application.Workbooks(CurrentWbkName).Sheets("ExportABCD").Cells(1, Columns.Count).End(xlToLeft).Column
    
    Application.Workbooks(CurrentWbkName).Sheets("ExportABCD").Range(Cells(2, 1), Cells(LastR, LastC)).Select
    Selection.Copy Application.Workbooks(CurrentWbkName).Sheets("SPECIMEN").Cells(2, 1)
    Application.Workbooks(CurrentWbkName).Sheets("ExportABCD").Delete

Else:
    MsgBox "The ExportABCD sheet doesn't exist in the workbook you have selected."
    GoTo Exit_LocalityInsert

End If

Exit_LocalityInsert:

Application.Workbooks(CurrentWbkName).Sheets("SPECIMEN").Activate

Application.ScreenUpdating = True
Application.DisplayAlerts = True

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

