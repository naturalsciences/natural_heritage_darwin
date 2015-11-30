Attribute VB_Name = "ExportTaxonomy2XML"
Option Explicit

Dim domain, kingdom, super_phylum, phylum, sub_phylum, super_class, class, sub_class, infra_class, super_order, order, sub_order, infra_order, section
Dim sub_section, super_family, family, sub_family, super_tribe, tribe, sub_tribe, infra_tribe, genus, sub_genus, species, sub_species, variety, sub_variety
Dim form, sub_form, abberans

Dim TaxoHeadings() As Variant
Dim TaxoHeadings_compared() As Variant

Dim strate As Integer

Dim level_name(31) As String
Dim strPattern As String
Dim strExcludePattern As String
Dim strExcludePatternBis As String
Dim strReplace As String
Dim strReplacementMessage As String
Dim booAlreadyInited As Boolean


Private Type KingdomLevelNamePattern
    kingdom As String
    level_name_pattern(31) As String
End Type
Dim kingdom_level_name_pattern(4) As KingdomLevelNamePattern

Private Type UpperLevels
    upper_level() As Integer
End Type
Dim possibleUpperLevels() As UpperLevels

'************************************************************************************************************************************
'| Purpose: Create XML file containing all data of the excel file (following ABCD schema), including specimen if owner is checked.
'************************************************************************************************************************************
Public Sub CreateXML()

On Error GoTo Err_CreateXML

Dim intAutoCorrectAnswer As Integer
'Dim intActivateCheck As Integer
Dim booAutoCorrectAnswer As Boolean
Dim booContinue As Boolean: booContinue = True

ProcessCarriageReturns

'Call function for mapping columns heading row
DefineHeadings


'intActivateCheck = MsgBox("Would you like to activate the check of eventual mistakes encountered in the template ?", _
                            vbYesNo, _
                            "Template check ?" _
                         )
'If intActivateCheck = vbYes Then
    intAutoCorrectAnswer = MsgBox("Would you like the program tries to auto-correct the eventual mistakes encountered in the template ?", _
                                    vbYesNo, _
                                    "Try auto-correction ?" _
                                 )
'End If
If CheckHeaders(check:=False) Then
    'If intActivateCheck Then
        booContinue = CheckTaxa(displayCheckMessages:=False, tryAutoCorrect:=intAutoCorrectAnswer)
    'End If
    If booContinue Then
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
            Dim rowNb As Long, taxo_tree_nb As Long
            taxo_tree_nb = 0
    
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
            Set node = dom.createComment("Import Taxonomy dans DaRWIN")
            dom.appendChild node
            Set node = Nothing
    
            ' Create the root element.
            Set root = dom.createNode(NODE_ELEMENT, "TaxonomyImport", "http://www.tdwg.org/schemas/abcd/2.06")
            ' Add the root element to the DOM instance.
            dom.appendChild root
            Set attr = dom.createNode(NODE_ATTRIBUTE, "xmlns:taxonomy", "")
            attr.Value = "http://darwin.naturalsciences.be/xsd/"
            root.setAttributeNode attr
            Set attr = dom.createNode(NODE_ATTRIBUTE, "xs:schemaLocation", "http://www.w3.org/2001/XMLSchema-instance")
            attr.Value = "http://www.tdwg.org/schemas/abcd/2.06 http://darwin.naturalsciences.be/xsd/taxonomy.xsd"
            root.setAttributeNode attr
            root.appendChild dom.createTextNode(vbCrLf & Space$(2))
            
            'Create a Metadata container
            Set node = dom.createNode(NODE_ELEMENT, "Metadata", "http://www.tdwg.org/schemas/abcd/2.06")
            root.appendChild node
            XMLMetadata dom:=dom, node:=node
            root.appendChild dom.createTextNode(vbCrLf & Space(2))
            
            'Create container and insert data in xml file
            Dim LastR As Long
    
            LastR = Application.Sheets("cTAXONOMY").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
    
            For rowCounter = 2 To LastR
    
                Application.StatusBar = "Processing... Please do not disturb... Exported rows: " & rowCounter - 1
                DoEvents
    
                XMLTaxoTree dom:=dom, subnode:=root, rowCounter:=rowCounter
    
                rowNb = rowCounter - 1
    
            Next rowCounter
    
        Else:
            GoTo Exit_CreateXML
        End If
    Else
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

'' Save the XML document to a file
If rowNb > 0 Then
DefineFileName:         strPath = Application.GetSaveAsFilename(InitialFileName:="taxonomy.xml", FileFilter:="XML Files (*.xml), *.xml", Title:="Select where to save your file")
    If strPath <> False Then
        ' Save the file at the location provided with the name provided
        dom.Save strPath
        MsgBox "Your output file was successfully created based on the TAXONOMY-sheet, with " & rowNb & " taxonomical trees."
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

    Exit Sub

Err_CreateXML:

    If Err.Number = -2147024891 Then
        MsgBox prompt:="You don't have the rights to save the file " & strPath & " on the location selected." & vbCrLf & _
                        "Please provide an other location.", Title:="No Sufficient rights", Buttons:=vbExclamation
        Resume DefineFileName
    Else
        MsgBox prompt:="Error " & Err.Number & vbNewLine & Err.Description & vbCrLf & "In CreateXML."
    End If
    Resume Next
    GoTo Exit_CreateXML

End Sub

'**********************************************************************************
'| Create xml file
'**********************************************************************************

Private Sub XMLTaxoTree(ByRef dom As MSXML2.DOMDocument60, ByRef subnode As MSXML2.IXMLDOMElement, ByRef rowCounter As Long)

    On Error Resume Next
    
    Dim xmlTaxonomicalTree As MSXML2.IXMLDOMElement
    Dim xmlTaxonomicalUnit As MSXML2.IXMLDOMElement
    Dim xmlLevelName As MSXML2.IXMLDOMElement
    Dim xmlLevelRef As MSXML2.IXMLDOMElement
    Dim xmlTaxonFullName As MSXML2.IXMLDOMElement
    Dim xmlTaxonName As MSXML2.IXMLDOMElement
    Dim xmlTaxonAuthorYear As MSXML2.IXMLDOMElement
    
    Dim strLevelName As String, strLevelRef As String, strTaxonFullName As String, strTaxonName As String, strTaxonAuthorYear As String
    
    Dim rep As String, rep2 As String, sep As String
    Dim celval As String, celcol As Integer
    rep = ""
    rep2 = ""
    sep = " "
    
    Dim Count As Integer
    
    If Not booAlreadyInited Then
        initGlobal
    End If
    
    For Count = 1 To 31
        celval = Application.Sheets("cTAXONOMY").Cells(rowCounter, Application.Sheets("cTAXONOMY").Rows(1).Find(level_name(Count), lookAt:=xlWhole, MatchCase:=True).Column).Value
        If Not IsEmpty(celval) And Not IsNull(celval) And celval <> "" Then
            rep = rep & celval & sep
        End If
        celval = ""
    Next Count
        
    'pas identification history car comme il s'agit d'une balise directement sous Identifications, il ne faut pas créer l'arbre Identification -> Result -> etc.
    
    If Not IsEmpty(rep) And Not IsNull(rep) And rep <> "" Then

        Set xmlTaxonomicalTree = dom.createNode(NODE_ELEMENT, "TaxonomicalTree", "http://www.tdwg.org/schemas/abcd/2.06")
        subnode.appendChild xmlTaxonomicalTree
        subnode.appendChild dom.createTextNode(vbCrLf)
        xmlTaxonomicalTree.appendChild dom.createTextNode(vbCrLf + Space$(4))
                    
        celval = ""
        For Count = 1 To 31
        
            celval = Application.Sheets("cTAXONOMY").Cells(rowCounter, Application.Sheets("cTAXONOMY").Rows(1).Find(level_name(Count), lookAt:=xlWhole).Column).Value

            If Not IsEmpty(celval) And Not IsNull(celval) And celval <> "" Then
         
                Set xmlTaxonomicalUnit = dom.createNode(NODE_ELEMENT, "TaxonomicalUnit", "http://www.tdwg.org/schemas/abcd/2.06")
                xmlTaxonomicalTree.appendChild xmlTaxonomicalUnit
                xmlTaxonomicalTree.appendChild dom.createTextNode(vbCrLf + Space$(4))
                xmlTaxonomicalUnit.appendChild dom.createTextNode(vbCrLf + Space$(6))
    
                strLevelName = level_name(Count)

                Set xmlLevelName = dom.createNode(NODE_ELEMENT, "LevelName", "http://www.tdwg.org/schemas/abcd/2.06")
                If Count = 14 Or Count = 15 Then
                    xmlLevelName.Text = strLevelName & "_zoology"
                Else:
                    xmlLevelName.Text = strLevelName
                End If
                xmlTaxonomicalUnit.appendChild xmlLevelName
                xmlTaxonomicalUnit.appendChild dom.createTextNode(vbCrLf + Space$(6))

                Set xmlTaxonFullName = dom.createNode(NODE_ELEMENT, "TaxonFullName", "http://www.tdwg.org/schemas/abcd/2.06")
                strTaxonFullName = celval
                xmlTaxonFullName.Text = strTaxonFullName
                xmlTaxonomicalUnit.appendChild xmlTaxonFullName
                xmlTaxonomicalUnit.appendChild dom.createTextNode(vbCrLf + Space$(4))
                
            End If
            
            celval = ""
        
        Next Count
                    
    End If
    
End Sub

'**********************************************************************************
'| Purpose: internal functions
'**********************************************************************************

'Create a comparison tool for used headings and supported headings
'-----------------------------------------------------------------------------------------------------------
Public Function CheckHeaders(ByRef check As Boolean) As Boolean

    On Error Resume Next
    
    Dim searchTerm, findTerm As Boolean, i As Integer, j As Integer, k As Integer
    Dim LastCTaxo As Integer
    Dim taxo_header_missing As String, taxo_sheet_missing As String
    Dim MsgRecord As String, MsgIntro As String, Msg As String
    
    DefineHeadings
    
    Erase TaxoHeadings_compared

    'RECORDS
    If FeuilleExiste("TAXONOMY") Then
        
        taxo_sheet_missing = 0
        LastCTaxo = Application.Sheets("TAXONOMY").Cells(1, Columns.Count).End(xlToLeft).Column
        j = 1
        For i = 1 To LastCTaxo
            'Check if a value exists in the Array
            searchTerm = Trim(Application.Sheets("TAXONOMY").Cells(1, i).Value)
            For k = LBound(TaxoHeadings) To UBound(TaxoHeadings)
                If TaxoHeadings(k) = searchTerm Then
                    findTerm = True
                    Exit For
                Else:
                    findTerm = False
                End If
            Next k
                                
            If findTerm = False Then
                ReDim Preserve TaxoHeadings_compared(1 To j)
                TaxoHeadings_compared(j) = Trim(Application.Sheets("TAXONOMY").Cells(1, i).Value)
                j = j + 1
            End If
        Next i
    
        taxo_header_missing = ""
        If UBound(TaxoHeadings_compared) > 0 Then
            For i = LBound(TaxoHeadings_compared) To UBound(TaxoHeadings_compared)
                taxo_header_missing = taxo_header_missing & TaxoHeadings_compared(i) & vbCrLf
            Next i
        End If

    Else:
        taxo_sheet_missing = 1
    
    End If
                    
    'Construction du message d'erreur
    MsgRecord = ""
    If taxo_sheet_missing = 1 Then
        'Bloque si pas de feuille TAXONOMY
        CheckHeaders = False
        MsgBox "There must be a sheet named 'TAXONOMY'. Please, rename the sheet that contains taxonomy and run the program again."
        Exit Function
    Else:
        If taxo_header_missing <> "" Then
            MsgRecord = "Some headings were not recognized in the TAXONOMY-sheet. They will not be exported in the xml abcd-formatted file.  There are listed by worksheet below. " & vbCrLf & "TAXONOMY-sheet: " & taxo_header_missing
        End If
    End If
        
    'Decision sur l'arrêt de l'export et affichage message explicatif
    If check = True Then
        If MsgRecord <> "" Then
            MsgBox "Some headings were not recognized. They will not be exported in the xml abcd-formatted file." & vbCrLf & vbCrLf & _
            "Unrecognized headings are listed by worksheet below: " _
            & vbCrLf & taxo_header_missing
            
            CheckHeaders = False
            Exit Function
        Else:
            CheckHeaders = True
            MsgBox ("All headers were recognized!")
        End If
    ElseIf check = False Then
        If MsgRecord <> "" Then
            Msg = "Some headings were not recognized. They will not be exported in the xml abcd-formatted file.  Click OK if you wish continue the export anyway, or Cancel to stop the program." & vbCrLf & _
            "Unrecognized headings are listed by worksheet below: " _
            & vbCrLf & taxo_header_missing
            
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

'Make copy of each sheets at the end of the sheets for structure rework instead of working
'         directly in the working sheet (with the risk of losing some vital infos ;) )
'-------------------------------------------------------------------------------------------------------------------------------------------------
Private Function CopySheetsForRework() As Boolean

On Error GoTo Err_CopySheetsForRework

Dim LastR As Long, LastC As Long
Dim R As Long
Dim sheetCount As Integer
Dim c As Range
Dim CellString As String

Dim taxonomy_sheet As Boolean

DefineHeadings

If FeuilleExiste("TAXONOMY") Then
    
    If FeuilleExiste("cTAXONOMY") Then
        Sheets("cTAXONOMY").Delete
    End If
    
    sheetCount = Sheets.Count
    
    'Copy each sheet without VBA code behind
    Sheets.Add After:=Sheets(Sheets.Count)
    Sheets(sheetCount + 1).Name = "cTAXONOMY"
    Sheets("TAXONOMY").Select
    Cells.Copy
    Sheets("cTAXONOMY").Select
    Cells.Select
    ActiveSheet.Paste
      
    LastC = Application.Sheets("cTAXONOMY").Cells(1, Columns.Count).End(xlToLeft).Column
    LastR = Application.Sheets("cTAXONOMY").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row

    'Delete empty lines
    For R = LastR To 1 Step -1
        For Each c In Application.Sheets("cTAXONOMY").Range(Cells(R, 1), Cells(R, LastC))
            CellString = CellString & Trim(c.Value)
        Next c
        If CellString = "" Then
            Application.Sheets("cTAXONOMY").Rows(R).Delete
        End If
        CellString = ""
    Next R
    
    'Trim each cell
    DoTrim

    taxonomy_sheet = True

Else:
    taxonomy_sheet = False

End If

If taxonomy_sheet = True Then
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
    MsgBox prompt:="There is no sheet named TAXONOMY in your file. Please, the worksheet that contains your data should be renamed TAXONOMY.", _
            Buttons:=vbCritical, _
            Title:="No TAXONOMY-sheet in your file"
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
    For Each cl In Application.Worksheets("cTAXONOMY").UsedRange
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

domain = "domain"
kingdom = "kingdom"
super_phylum = "super_phylum"
phylum = "phylum"
sub_phylum = "sub_phylum"
super_class = "super_class"
class = "class"
sub_class = "sub_class"
infra_class = "infra_class"
super_order = "super_order"
order = "order"
sub_order = "sub_order"
infra_order = "infra_order"
section = "section_zoology"
sub_section = "sub_section_zoology"
super_family = "super_family"
family = "family"
sub_family = "sub_family"
super_tribe = "super_tribe"
tribe = "tribe"
sub_tribe = "sub_tribe"
infra_tribe = "infra_tribe"
genus = "genus"
sub_genus = "sub_genus"
species = "species"
sub_species = "sub_species"
variety = "variety"
sub_variety = "sub_variety"
form = "form"
sub_form = "sub_form"
abberans = "abberans"

TaxoHeadings = Array("domain", "kingdom", "super_phylum", "phylum", "sub_phylum", "super_class", "class", "sub_class", "infra_class", "super_order", "order", _
"sub_order", "infra_order", "section", "sub_section", "super_family", "family", "sub_family", "super_tribe", "tribe", "sub_tribe", "infra_tribe", "genus", "sub_genus", _
"species", "sub_species", "variety", "sub_variety", "form", "sub_form", "abberans")

End Sub

' Delete the copied sheets when all is done
'-------------------------------------------------------------
Private Sub DeleteSheetsAfterRework()

On Error GoTo Err_DeleteSheetsAfterRework

If FeuilleExiste("cTAXONOMY") Then
    Sheets("cTAXONOMY").Delete
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

'Initialize global variables
'---------------------------
Private Sub initGlobal()
    strPattern = "[\$\[\]\(\)\@\^\-\*\_\/\\\+\n\#\&\!\,\;\:\.]+"
    strExcludePattern = "indet\.|indét\.|undet\.|\?"
    strExcludePatternBis = "^\w" & strPattern
    
    'Define The Kingdoms
    kingdom_level_name_pattern(1).kingdom = "Animalia"
    kingdom_level_name_pattern(2).kingdom = "Plantae"
    kingdom_level_name_pattern(3).kingdom = "Ichnofossile"
    kingdom_level_name_pattern(4).kingdom = "Others"
    'Define the patterns per level for each Kingdom
    'First Animalia
    kingdom_level_name_pattern(1).level_name_pattern(16) = "oidea$"
    kingdom_level_name_pattern(1).level_name_pattern(17) = "idae$"
    kingdom_level_name_pattern(1).level_name_pattern(18) = "inae$"
    kingdom_level_name_pattern(1).level_name_pattern(20) = "ini$"
    kingdom_level_name_pattern(1).level_name_pattern(21) = "ina$"
    'Second Plantae
    kingdom_level_name_pattern(2).level_name_pattern(10) = "anae$"
    kingdom_level_name_pattern(2).level_name_pattern(11) = "ales$"
    kingdom_level_name_pattern(2).level_name_pattern(12) = "ineae$"
    kingdom_level_name_pattern(2).level_name_pattern(13) = "aria$"
    kingdom_level_name_pattern(2).level_name_pattern(16) = "acea$"
    kingdom_level_name_pattern(2).level_name_pattern(17) = "aceae$"
    kingdom_level_name_pattern(2).level_name_pattern(18) = "oideae$"
    kingdom_level_name_pattern(2).level_name_pattern(20) = "eae$"
    kingdom_level_name_pattern(2).level_name_pattern(21) = "inae$"
    'Third Ichnofossile
    kingdom_level_name_pattern(3).level_name_pattern(16) = "oidea$"
    kingdom_level_name_pattern(3).level_name_pattern(17) = "idae$"
    kingdom_level_name_pattern(3).level_name_pattern(18) = "inae$"
    kingdom_level_name_pattern(3).level_name_pattern(20) = "ini$"
    kingdom_level_name_pattern(3).level_name_pattern(21) = "ina$"
    'Fourth Others
    kingdom_level_name_pattern(4).level_name_pattern(16) = "(oidea|acea)$"
    kingdom_level_name_pattern(4).level_name_pattern(17) = "(idae|aceae)$"
    kingdom_level_name_pattern(4).level_name_pattern(18) = "(inae|oideae)$"
    kingdom_level_name_pattern(4).level_name_pattern(20) = "(ini|eae)$"
    kingdom_level_name_pattern(4).level_name_pattern(21) = "(ina|inae)$"
    
    strReplace = ""
    level_name(1) = "domain"
    level_name(2) = "kingdom"
    level_name(3) = "super_phylum"
    level_name(4) = "phylum"
    level_name(5) = "sub_phylum"
    level_name(6) = "super_class"
    level_name(7) = "class"
    level_name(8) = "sub_class"
    level_name(9) = "infra_class"
    level_name(10) = "super_order"
    level_name(11) = "order"
    level_name(12) = "sub_order"
    level_name(13) = "infra_order"
    level_name(14) = "section"
    level_name(15) = "sub_section"
    level_name(16) = "super_family"
    level_name(17) = "family"
    level_name(18) = "sub_family"
    level_name(19) = "super_tribe"
    level_name(20) = "tribe"
    level_name(21) = "sub_tribe"
    level_name(22) = "infra_tribe"
    level_name(23) = "genus"
    level_name(24) = "sub_genus"
    level_name(25) = "species"
    level_name(26) = "sub_species"
    level_name(27) = "variety"
    level_name(28) = "sub_variety"
    level_name(29) = "form"
    level_name(30) = "sub_form"
    level_name(31) = "abberans"
    
    'Redim the PULs
    ReDim possibleUpperLevels(31)
    ReDim possibleUpperLevels(1).upper_level(1)
    ReDim possibleUpperLevels(2).upper_level(1)
    ReDim possibleUpperLevels(3).upper_level(1)
    ReDim possibleUpperLevels(4).upper_level(2)
    ReDim possibleUpperLevels(5).upper_level(1)
    ReDim possibleUpperLevels(6).upper_level(3)
    ReDim possibleUpperLevels(7).upper_level(4)
    ReDim possibleUpperLevels(8).upper_level(1)
    ReDim possibleUpperLevels(9).upper_level(1)
    ReDim possibleUpperLevels(10).upper_level(6)
    ReDim possibleUpperLevels(11).upper_level(7)
    ReDim possibleUpperLevels(12).upper_level(1)
    ReDim possibleUpperLevels(13).upper_level(1)
    ReDim possibleUpperLevels(14).upper_level(8)
    ReDim possibleUpperLevels(15).upper_level(1)
    ReDim possibleUpperLevels(16).upper_level(10)
    ReDim possibleUpperLevels(17).upper_level(11)
    ReDim possibleUpperLevels(18).upper_level(1)
    ReDim possibleUpperLevels(19).upper_level(13)
    ReDim possibleUpperLevels(20).upper_level(14)
    ReDim possibleUpperLevels(21).upper_level(1)
    ReDim possibleUpperLevels(22).upper_level(1)
    ReDim possibleUpperLevels(23).upper_level(16)
    ReDim possibleUpperLevels(24).upper_level(1)
    ReDim possibleUpperLevels(25).upper_level(16)
    ReDim possibleUpperLevels(26).upper_level(1)
    ReDim possibleUpperLevels(27).upper_level(3)
    ReDim possibleUpperLevels(28).upper_level(1)
    ReDim possibleUpperLevels(29).upper_level(4)
    ReDim possibleUpperLevels(30).upper_level(1)
    ReDim possibleUpperLevels(31).upper_level(6)
    
    'The possible upper levels (PULs) array used to test the missing/wrong parenties
    possibleUpperLevels(2).upper_level(0) = 1
    possibleUpperLevels(3).upper_level(0) = 2
    possibleUpperLevels(4).upper_level(0) = 2
    possibleUpperLevels(4).upper_level(1) = 3
    possibleUpperLevels(5).upper_level(0) = 4
    possibleUpperLevels(6).upper_level(0) = 2
    possibleUpperLevels(6).upper_level(1) = 4
    possibleUpperLevels(6).upper_level(2) = 5
    possibleUpperLevels(7).upper_level(0) = 2
    possibleUpperLevels(7).upper_level(1) = 4
    possibleUpperLevels(7).upper_level(2) = 5
    possibleUpperLevels(7).upper_level(3) = 6
    possibleUpperLevels(8).upper_level(0) = 7
    possibleUpperLevels(9).upper_level(0) = 8
    possibleUpperLevels(10).upper_level(0) = 2
    possibleUpperLevels(10).upper_level(1) = 4
    possibleUpperLevels(10).upper_level(2) = 5
    possibleUpperLevels(10).upper_level(3) = 7
    possibleUpperLevels(10).upper_level(4) = 8
    possibleUpperLevels(10).upper_level(5) = 9
    possibleUpperLevels(11).upper_level(0) = 2
    possibleUpperLevels(11).upper_level(1) = 4
    possibleUpperLevels(11).upper_level(2) = 5
    possibleUpperLevels(11).upper_level(3) = 7
    possibleUpperLevels(11).upper_level(4) = 8
    possibleUpperLevels(11).upper_level(5) = 9
    possibleUpperLevels(11).upper_level(6) = 10
    possibleUpperLevels(12).upper_level(0) = 11
    possibleUpperLevels(13).upper_level(0) = 12
    possibleUpperLevels(14).upper_level(0) = 2
    possibleUpperLevels(14).upper_level(1) = 4
    possibleUpperLevels(14).upper_level(2) = 5
    possibleUpperLevels(14).upper_level(3) = 7
    possibleUpperLevels(14).upper_level(4) = 8
    possibleUpperLevels(14).upper_level(5) = 9
    possibleUpperLevels(14).upper_level(6) = 11
    possibleUpperLevels(14).upper_level(7) = 12
    possibleUpperLevels(15).upper_level(0) = 14
    possibleUpperLevels(16).upper_level(0) = 2
    possibleUpperLevels(16).upper_level(1) = 4
    possibleUpperLevels(16).upper_level(2) = 5
    possibleUpperLevels(16).upper_level(3) = 7
    possibleUpperLevels(16).upper_level(4) = 8
    possibleUpperLevels(16).upper_level(5) = 9
    possibleUpperLevels(16).upper_level(6) = 11
    possibleUpperLevels(16).upper_level(7) = 12
    possibleUpperLevels(16).upper_level(8) = 14
    possibleUpperLevels(16).upper_level(9) = 15
    possibleUpperLevels(17).upper_level(0) = 2
    possibleUpperLevels(17).upper_level(1) = 4
    possibleUpperLevels(17).upper_level(2) = 5
    possibleUpperLevels(17).upper_level(3) = 7
    possibleUpperLevels(17).upper_level(4) = 8
    possibleUpperLevels(17).upper_level(5) = 9
    possibleUpperLevels(17).upper_level(6) = 11
    possibleUpperLevels(17).upper_level(7) = 12
    possibleUpperLevels(17).upper_level(8) = 14
    possibleUpperLevels(17).upper_level(9) = 15
    possibleUpperLevels(17).upper_level(10) = 16
    possibleUpperLevels(18).upper_level(0) = 17
    possibleUpperLevels(19).upper_level(0) = 2
    possibleUpperLevels(19).upper_level(1) = 4
    possibleUpperLevels(19).upper_level(2) = 5
    possibleUpperLevels(19).upper_level(3) = 7
    possibleUpperLevels(19).upper_level(4) = 8
    possibleUpperLevels(19).upper_level(5) = 9
    possibleUpperLevels(19).upper_level(6) = 11
    possibleUpperLevels(19).upper_level(7) = 12
    possibleUpperLevels(19).upper_level(8) = 14
    possibleUpperLevels(19).upper_level(9) = 15
    possibleUpperLevels(19).upper_level(10) = 16
    possibleUpperLevels(19).upper_level(11) = 17
    possibleUpperLevels(19).upper_level(12) = 18
    possibleUpperLevels(20).upper_level(0) = 2
    possibleUpperLevels(20).upper_level(1) = 4
    possibleUpperLevels(20).upper_level(2) = 5
    possibleUpperLevels(20).upper_level(3) = 7
    possibleUpperLevels(20).upper_level(4) = 8
    possibleUpperLevels(20).upper_level(5) = 9
    possibleUpperLevels(20).upper_level(6) = 11
    possibleUpperLevels(20).upper_level(7) = 12
    possibleUpperLevels(20).upper_level(8) = 14
    possibleUpperLevels(20).upper_level(9) = 15
    possibleUpperLevels(20).upper_level(10) = 16
    possibleUpperLevels(20).upper_level(11) = 17
    possibleUpperLevels(20).upper_level(12) = 18
    possibleUpperLevels(20).upper_level(13) = 36
    possibleUpperLevels(21).upper_level(0) = 20
    possibleUpperLevels(22).upper_level(0) = 21
    possibleUpperLevels(23).upper_level(0) = 2
    possibleUpperLevels(23).upper_level(1) = 4
    possibleUpperLevels(23).upper_level(2) = 5
    possibleUpperLevels(23).upper_level(3) = 7
    possibleUpperLevels(23).upper_level(4) = 8
    possibleUpperLevels(23).upper_level(5) = 9
    possibleUpperLevels(23).upper_level(6) = 11
    possibleUpperLevels(23).upper_level(7) = 12
    possibleUpperLevels(23).upper_level(8) = 14
    possibleUpperLevels(23).upper_level(9) = 15
    possibleUpperLevels(23).upper_level(10) = 16
    possibleUpperLevels(23).upper_level(11) = 17
    possibleUpperLevels(23).upper_level(12) = 18
    possibleUpperLevels(23).upper_level(13) = 20
    possibleUpperLevels(23).upper_level(14) = 21
    possibleUpperLevels(23).upper_level(15) = 22
    possibleUpperLevels(24).upper_level(0) = 23
    possibleUpperLevels(25).upper_level(0) = 4
    possibleUpperLevels(25).upper_level(1) = 5
    possibleUpperLevels(25).upper_level(2) = 7
    possibleUpperLevels(25).upper_level(3) = 8
    possibleUpperLevels(25).upper_level(4) = 9
    possibleUpperLevels(25).upper_level(5) = 11
    possibleUpperLevels(25).upper_level(6) = 12
    possibleUpperLevels(25).upper_level(7) = 14
    possibleUpperLevels(25).upper_level(8) = 15
    possibleUpperLevels(25).upper_level(9) = 16
    possibleUpperLevels(25).upper_level(10) = 17
    possibleUpperLevels(25).upper_level(11) = 18
    possibleUpperLevels(25).upper_level(12) = 20
    possibleUpperLevels(25).upper_level(13) = 21
    possibleUpperLevels(25).upper_level(14) = 23
    possibleUpperLevels(25).upper_level(15) = 24
    possibleUpperLevels(26).upper_level(0) = 25
    possibleUpperLevels(27).upper_level(0) = 23
    possibleUpperLevels(27).upper_level(1) = 25
    possibleUpperLevels(27).upper_level(2) = 26
    possibleUpperLevels(28).upper_level(0) = 27
    possibleUpperLevels(29).upper_level(0) = 25
    possibleUpperLevels(29).upper_level(1) = 26
    possibleUpperLevels(29).upper_level(2) = 27
    possibleUpperLevels(29).upper_level(3) = 28
    possibleUpperLevels(30).upper_level(0) = 29
    possibleUpperLevels(31).upper_level(0) = 25
    possibleUpperLevels(31).upper_level(1) = 26
    possibleUpperLevels(31).upper_level(2) = 27
    possibleUpperLevels(31).upper_level(3) = 28
    possibleUpperLevels(31).upper_level(4) = 29
    possibleUpperLevels(31).upper_level(5) = 30
    
    booAlreadyInited = True
End Sub

'Shortcut procedure to call the error writing with orange color
'--------------------------------------------------------------
Private Sub WriteInfo(ByRef rowCounter As Long, _
                      ByRef columnCounter As Long, _
                      ByRef iRow As Long, _
                      ByRef iColumn As Integer, _
                      Optional ByRef replacementMessage As String, _
                      Optional ByRef color As Integer = 6)
    WriteError rowCounter:=rowCounter, _
                columnCounter:=columnCounter, _
                iRow:=iRow, _
                iColumn:=iColumn, _
                errorLevel:="info", _
                replacementMessage:=replacementMessage, _
                color:=color
End Sub

'Shortcut procedure to call the error writing with orange color
'--------------------------------------------------------------
Private Sub WriteWarning(ByRef rowCounter As Long, _
                            ByRef columnCounter As Long, _
                            ByRef iRow As Long, _
                            ByRef iColumn As Integer, _
                            Optional ByRef replacementMessage As String, _
                            Optional ByRef color As Integer = 46)
    WriteError rowCounter:=rowCounter, _
                columnCounter:=columnCounter, _
                iRow:=iRow, _
                iColumn:=iColumn, _
                errorLevel:="warning", _
                replacementMessage:=replacementMessage, _
                color:=color
End Sub

'Write the wrong value in the right corresponding colum and highlight with colour (red, orange, yellow) the error on data sheet
'------------------------------------------------------------------------------------------------------------------------------
Private Sub WriteError(ByRef rowCounter As Long, _
                        ByRef columnCounter As Long, _
                        ByRef iRow As Long, _
                        ByRef iColumn As Integer, _
                        Optional ByRef errorLevel As String = "error", _
                        Optional ByRef replacementMessage As String = "", _
                        Optional ByRef color As Integer = -1)

    Dim rng As Range
    Dim strCellRef As String
    Dim colour As Integer
    Dim strMessage As String
    
    If color <> -1 Then
        colour = color
    Else
        colour = 3
    End If
    
    If ActiveSheet.Name <> "TAXONOMY" Then
        Application.Sheets("TAXONOMY").Activate
    End If
        
    Cells(rowCounter, columnCounter).Select
    With Selection
        .Interior.ColorIndex = colour
    End With

    If replacementMessage = "" Then
        strMessage = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
    Else
        strMessage = replacementMessage
    End If
    
    Application.Sheets("CheckTaxa").Cells(iRow, 1).Value _
    = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Address
    Application.Sheets("CheckTaxa").Cells(iRow, iColumn).Value _
    = strMessage
    strCellRef _
    = WorksheetFunction.Substitute(Application.Sheets("CheckTaxa").Cells(iRow, 1).Value, "$", "")
    Application.Sheets("CheckTaxa").Activate
    Set rng = ActiveSheet.Range(Cells(iRow, 1), Cells(iRow, 1))
    With rng
        .Parent.Hyperlinks.Add Anchor:=rng, _
                                Address:="", _
                                SubAddress:="TAXONOMY!" & strCellRef, _
                                TextToDisplay:="TAXONOMY!" & strCellRef
    End With
    Application.Sheets("CheckTaxa").Cells(iRow, 6).Value _
    = errorLevel
    Application.Sheets("TAXONOMY").Activate
    If Cells(rowCounter, columnCounter).Value <> "" Then
        Set rng = ActiveSheet.Range(Cells(rowCounter, columnCounter), Cells(rowCounter, columnCounter))
        With rng
            .Parent.Hyperlinks.Add Anchor:=rng, _
                                    Address:="", _
                                    SubAddress:="CheckTaxa!A" & iRow
        End With
    End If
    
End Sub

' Applies autocorrection on the given cell
'-------------------------------------------------------
Private Function AutoCorrect(ByRef rowCounter As Long, _
                             ByRef columnCounter As Long, _
                             ByRef iRow As Long, _
                             Optional ByRef typeOfAction As String = "wrong name termination") As Boolean
On Error GoTo Err_AutoCorrect

    Dim regExp As New regExp
    Dim strCellVal As String
    Dim strCellValueTemp() As String
    Dim strCellValueForRegexp As String
    Dim iCounter As Integer
    Dim booPatternMatch As Boolean: booPatternMatch = False
    Dim lngMatchingLevel As Long
    Dim strKingdom As String
    Dim strLevelNamePattern() As String
    'Define Regexp behavior
    With regExp
        .Global = True
        .MultiLine = True
        .IgnoreCase = False
    End With
    'Store the current cell value
    strCellVal = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
    strCellValueForRegexp = strCellVal
    If columnCounter <= 23 Then
        strCellValueTemp = Split(strCellVal)
        strCellValueForRegexp = strCellValueTemp(0)
    End If
    '... and store the value of the Kingdom column
    strKingdom = Application.Sheets("TAXONOMY").Cells(rowCounter, 2).Value
    'Browse the useable pattern for regexp
    For iCounter = LBound(kingdom_level_name_pattern) To UBound(kingdom_level_name_pattern) - 1
        'The moment a kingdom is matched, exit the loop... the counter will serve us to focus on the right entries
        If kingdom_level_name_pattern(iCounter).kingdom = strKingdom Then
            Exit For
        End If
    Next iCounter
    'Store the array of patterns for the corresponding kingdom
    strLevelNamePattern = kingdom_level_name_pattern(iCounter).level_name_pattern
    
    Select Case typeOfAction
        'If the sort of problem is a wrong name termination for the level encountered
        Case "wrong name termination":
            'First try to find an other level that could end with what's ending the name in the current level
            For iCounter = LBound(strLevelNamePattern) + 1 To UBound(strLevelNamePattern)
                If strLevelNamePattern(iCounter) <> "" And iCounter <> columnCounter Then
                    regExp.Pattern = strLevelNamePattern(iCounter)
                    'If more than one match, it's impossible to choose which one to copy to
                    'Abord than and mark this has error
                    If booPatternMatch Then
                        If regExp.Test(strCellValueForRegexp) Then
                            Err.Raise vbObjectError + 601, _
                                        "ExportTaxonomy2XML::AutoCorrect()", _
                                        "Errors occured during the taxa auto correction process !" & _
                                        "There's more than one possible target for the move of the name entered !"
                        End If
                    Else
                        booPatternMatch = regExp.Test(strCellValueForRegexp)
                        If booPatternMatch Then
                            lngMatchingLevel = CLng(iCounter)
                        End If
                    End If
                End If
            Next iCounter
            'If only one match, move the content to the correct level cell
            If booPatternMatch Then
                '... But test first the cell is not filled already
                If Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, lngMatchingLevel).Value) = "" Then
                    Application.Sheets("TAXONOMY").Cells(rowCounter, lngMatchingLevel).Value = strCellVal
                    Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = ""
                    WriteInfo rowCounter:=rowCounter, _
                                columnCounter:=lngMatchingLevel, _
                                iRow:=iRow, _
                                iColumn:=3, _
                                replacementMessage:="Value (" & strCellVal & ") " & _
                                                    "has been moved from level " & level_name(columnCounter) & " " & _
                                                    "to level " & level_name(lngMatchingLevel) & "."
                'Otherwise, abort and generate an error
                Else
                    strReplacementMessage = "Impossible to move the entry (" & _
                                            strCellVal & _
                                            ") from level " & _
                                            level_name(columnCounter) & _
                                            " to level " & level_name(lngMatchingLevel) & "." & vbCrLf & _
                                            "The targeted level is already filled in."
                    Err.Raise vbObjectError + 602, _
                                        "ExportTaxonomy2XML::AutoCorrect()", _
                                        "Errors occured during the taxa auto correction process !" & _
                                        "The targeted level is already filled - move of the name is not possible !"
                End If
            Else
                strReplacementMessage = "Value (" & _
                                        strCellVal & _
                                        ") follow no known pattern for the level " & _
                                        level_name(columnCounter) & "."
                Err.Raise vbObjectError + 602, _
                            "ExportTaxonomy2XML::AutoCorrect()", _
                            "Errors occured during the taxa auto correction process !" & _
                            "The name encountered follow no known pattern !"
            End If
        Case "":
            
        Case "":
            
    End Select
    AutoCorrect = True

Exit_AutoCorrect:
    Exit Function
Err_AutoCorrect:
'    MsgBox prompt:="An error occured in function AutoCorrect." & vbCrLf & _
 '                  "Error Number: " & Err.Number & "." & vbCrLf & _
  '                 "Error description: " & Err.Description & ".", _
   '         Buttons:=vbCritical, _
    '        Title:="Error in sub AutoCorrect"
    AutoCorrect = False
    GoTo Exit_AutoCorrect

End Function

'Check if given Taxonomic informations are correct (for what can be tested)
'---------------------------------------------------------------------------
Public Function CheckTaxa(ByRef displayCheckMessages As Boolean, ByRef tryAutoCorrect As Integer) As Boolean

    On Error GoTo Err_CheckTaxa
    
    Dim LastR As Long, LastC As Long, rowCounter As Long, columnCounter As Long, sheetCount As Long
    
    LastC = Application.Sheets("TAXONOMY").Cells(1, Columns.Count).End(xlToLeft).Column
    LastR = Application.Sheets("TAXONOMY").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
    If LastR < 2 Then LastR = 2
    
    Dim i As Long
    Dim Count As Integer
    
    Dim regExp As New regExp
    Dim booErrorFound As Boolean
    
    Dim strKingdom As String
    Dim strKingdomSplit() As String
    Dim strLevelNamePattern() As String
    Dim strCellValue As String
    Dim strCellValueTemp() As String
    Dim strCellValueForRegexp As String
        
    Dim iCounter As Long
    Dim jCounter As Long
    Dim previousLevel As Long: previousLevel = 0
    Dim strPUL As String
    
    Dim keywordPosition As Long
    Dim keywordSubPosition As Long
    Dim parenthesisStartPos As Long
    Dim parenthesisStopPos As Long
    Dim inParenthesis As String
    Dim strParentSplited() As String
    Dim strSubLevelsSplited() As String
    
    Dim rngCell As Range
    
    If Not booAlreadyInited Then
        initGlobal
    End If
    
    With regExp
        .Global = True
        .MultiLine = True
        .IgnoreCase = False
    End With
    
    If FeuilleExiste("TAXONOMY") Then
    
        'Delete CheckTaxa sheet if exists
        If FeuilleExiste("CheckTaxa") Then
            Application.DisplayAlerts = False
            Application.Sheets("CheckTaxa").Delete
            Application.DisplayAlerts = True
        End If
        
        'Then recreate it and set the "headers" for that sheet displaying errors encountered
        sheetCount = Sheets.Count
        Sheets.Add After:=Sheets(Sheets.Count)
        'Set sheet name
        Sheets(sheetCount + 1).Name = "CheckTaxa"
        'Set sheet columns headers
        Application.Sheets("CheckTaxa").Cells(1, 1).Value = "Cell concerned"
        Application.Sheets("CheckTaxa").Cells(1, 2).Value = "Wrong name"
        Application.Sheets("CheckTaxa").Cells(1, 3).Value = "Wrong hierarchy"
        Application.Sheets("CheckTaxa").Cells(1, 4).Value = "Wrong end for the corresponding level"
        Application.Sheets("CheckTaxa").Cells(1, 5).Value = "Seemingly erroneous name composition for given level"
        Application.Sheets("CheckTaxa").Cells(1, 6).Value = "Error Level"
        'Freeze first row
        ActiveSheet.Rows(2).Select
        ActiveWindow.FreezePanes = True
        'Colourize headers
        ActiveSheet.Range("A1:F1").Select
        With Selection
            .Interior.ColorIndex = 46
            .Font.ColorIndex = 2
        End With
                
        'Go back on the main sheet, and set the background color on none
        Application.Sheets("TAXONOMY").Activate
        Set rngCell = ActiveSheet.Range(Cells(2, 1), Cells(LastR, LastC))
        With rngCell
            .Hyperlinks.Delete
            .Select
            With Selection.Interior
                .ColorIndex = xlColorIndexNone
            End With
        End With
        
        i = 2
        
        Application.ScreenUpdating = False
        Application.Cursor = xlWait
        
        ' Very first loop through all cells to capitalize first letter of text in all cells
        For rowCounter = 2 To LastR
            'Display parsing counter in status bar
            Application.StatusBar = "Initial parsing - Processing... Please do not disturb... Checked rows: " & rowCounter - 1
            For columnCounter = 1 To LastC
                Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value)
                If Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value <> "" Then
                    ' Bring the first letter in upper case for everything's above species
                    If columnCounter < 25 Then
                        strSubLevelsSplited = Split(Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value)
                        If (InStr(LCase$(strSubLevelsSplited(0)), "subgen.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "subg.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "sect.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "subsect.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "ser.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "sp.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "ssp.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "subsp.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "var.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "subvar.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "f.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "subf.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "ab.") + _
                            InStr(LCase$(strSubLevelsSplited(0)), "abb.")) = 0 Then
                            Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = UCase(Left$(Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value, 1)) & _
                                                                                                    Mid$(Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value, 2)
                        End If
                    End If
                    regExp.Pattern = "\(.+\)"
                    strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                    If regExp.Test(strCellValue) Then
                        parenthesisStartPos = InStr(strCellValue, "(")
                        parenthesisStopPos = InStr(parenthesisStartPos, strCellValue, ")")
                        inParenthesis = Trim$(Mid$(strCellValue, parenthesisStartPos + 1, parenthesisStopPos - parenthesisStartPos - 1))
                        inParenthesis = UCase(Left$(inParenthesis, 1)) & Mid$(inParenthesis, 2)
                        Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Left$(strCellValue, parenthesisStartPos) & _
                                                                                                inParenthesis & _
                                                                                                Right$(strCellValue, Len(strCellValue) - parenthesisStopPos + 1)
                        strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                    End If
                End If
            Next columnCounter
            If (rowCounter Mod 1000) = 0 Then
                ActiveWorkbook.Save
            End If
        Next rowCounter
        
        ' Loop through all Cells to analyse content and pinpoint the wrong ones (with eventual corrections applied)
        For rowCounter = 2 To LastR
            'Display parsing counter in status bar
            Application.StatusBar = "First parsing - Processing... Please do not disturb... Checked rows: " & rowCounter - 1
            'Get the kingdom concerned and get the corresponding pattern list for regexp test
            If Application.Sheets("TAXONOMY").Cells(rowCounter, 2).Value <> "" Then
                strKingdomSplit = Split(Application.Sheets("TAXONOMY").Cells(rowCounter, 2).Value)
                strKingdom = strKingdomSplit(0)
            Else
                strKingdom = "Others"
            End If
            For iCounter = LBound(kingdom_level_name_pattern) + 1 To UBound(kingdom_level_name_pattern)
                If kingdom_level_name_pattern(iCounter).kingdom = strKingdom Then
                    Exit For
                End If
            Next iCounter
            If iCounter > UBound(kingdom_level_name_pattern) Then
                iCounter = UBound(kingdom_level_name_pattern)
            End If
            strLevelNamePattern = kingdom_level_name_pattern(iCounter).level_name_pattern
            ' For each column of row...
            For columnCounter = 1 To LastC
                strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                'rngCell = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter)
                strCellValueForRegexp = LCase(strCellValue)
                If strCellValue <> "" Then
                    ' First, test if an excluded keyword is present
                    regExp.Pattern = strExcludePattern
                    If regExp.Test(strCellValueForRegexp) Then
                        'If it's the case, write the error
                        WriteError rowCounter, columnCounter, i, 2, "error", "Indetermination keyword cannot be used for units in catalogues !"
                        'Used to not cumulate writing of errors
                        booErrorFound = True
                    End If
                    ' Second, test pattern of name begining with something followed by special char
                    regExp.Pattern = strExcludePatternBis
                    If regExp.Test(strCellValueForRegexp) Then
                        'If it's the case, write the error
                        WriteError rowCounter, columnCounter, i, 2, "error", "The first element in the name cannot be abbreviated !"
                        'Used to not cumulate writing of errors
                        booErrorFound = True
                    End If
                    ' Third, test if the content is not only made of special characters
                    regExp.Pattern = strPattern
                    If regExp.Test(strCellValueForRegexp) Then
                        If Trim$(regExp.Replace(strCellValueForRegexp, strReplace)) _
                            = "" Then
                            'If it's the case, write the error
                            WriteError rowCounter, columnCounter, i, 2
                            'Used to not cumulate writing of errors
                            booErrorFound = True
                        End If
                    End If
                    ' Third, test made only on a few levels
                    If Not booErrorFound And _
                       strLevelNamePattern(columnCounter) <> "" Then
                        'Recompose the value to be tested for regexp if level above Genus (included)
                        If columnCounter <= 23 Then
                            strCellValueTemp = Split(strCellValue)
                            strCellValueForRegexp = strCellValueTemp(0)
                        End If
                        ' Try to match the termination of names pattern given the level encountered
                        regExp.Pattern = strLevelNamePattern(columnCounter)
                        If Not regExp.Test(strCellValueForRegexp) Then
                            'If no match and autocorrection is activated, try to make the correction
                            If tryAutoCorrect = vbYes Then
                                strReplacementMessage = ""
                                If Not AutoCorrect(rowCounter, columnCounter, i) Then
                                    'If correction failed, write an error
                                    WriteError rowCounter, columnCounter, i, 4, replacementMessage:=strReplacementMessage
                                End If
                            Else
                                'If autocorrection not activated, write an error
                                WriteError rowCounter, columnCounter, i, 4
                            End If
                            booErrorFound = True
                        End If
                    End If
                    ' Fourth, If no previous error, check the existence of some keywords and check their adequation
                    ' with the current level
                    If Not booErrorFound Then
                        ' Test abberans keyword
                        keywordPosition = InStr(LCase$(strCellValue), "abb.") + _
                                            InStr(LCase$(strCellValue), "ab.")
                        If keywordPosition > 0 And _
                            columnCounter < 31 Then
                            If tryAutoCorrect = vbYes And _
                               Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, 31).Value) = "" Then
                               Application.Sheets("TAXONOMY").Cells(rowCounter, 31).Value = strCellValue
                               Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Trim$(Left$(strCellValue, keywordPosition - 1))
                               strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                            Else
                                'If autocorrection not activated or cell already filled, write an error
                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:="Not corrected: Wrong keyword for the current level (" & _
                                            level_name(columnCounter) & _
                                            ")." & _
                                            vbCrLf & _
                                            "Maybe targeted " & _
                                            level_name(31) & _
                                            " is already filled..."
                                booErrorFound = True
                            End If
                        ' If none of the abberans keyword were found, we were on a abberans level cell and for a Plantae kingdom
                        ElseIf keywordPosition = 0 And _
                            columnCounter = 31 And _
                            strKingdom = "Plantae" Then
                            ' The keyword must be present for abberans under Plantae Kingdom
                            WriteError rowCounter, columnCounter, i, 5, replacementMessage:="In the Plantae kingdom, the infra-species keyword (abb. or ab.)" & _
                                        vbCrLf & _
                                        "must be present for the current level (" & _
                                        level_name(columnCounter) & ")"
                            booErrorFound = True
                        End If
                        If Not booErrorFound Then
                            ' Test subforma keyword
                            keywordPosition = InStr(LCase$(strCellValue), "subf.")
                            If keywordPosition > 0 And _
                                columnCounter < 30 Then
                                If tryAutoCorrect = vbYes And _
                                   Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, 30).Value) = "" Then
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, 30).Value = strCellValue
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Trim$(Left$(strCellValue, keywordPosition - 1))
                                   strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                Else
                                    'If autocorrection not activated or cell already filled, write an error
                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:="Not corrected: Wrong keyword for the current level (" & _
                                                level_name(columnCounter) & _
                                                ")." & _
                                                vbCrLf & _
                                                "Maybe targeted " & _
                                                level_name(30) & _
                                                " is already filled..."
                                    booErrorFound = True
                                End If
                            ' If none of the sub-forma keyword were found, we were on a sub-forma level cell and for a Plantae kingdom
                            ElseIf keywordPosition = 0 And _
                                columnCounter = 30 And _
                                strKingdom = "Plantae" Then
                                ' The keyword must be present for subforma under Plantae Kingdom
                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:="In the Plantae kingdom, the infra-species keyword (subf.)" & _
                                            vbCrLf & _
                                            "must be present for the current level (" & _
                                            level_name(columnCounter) & ")"
                                booErrorFound = True
                            End If
                        End If
                        If Not booErrorFound Then
                            ' Test forma keyword
                            keywordPosition = InStr(LCase$(strCellValue), "f.")
                            keywordSubPosition = InStr(LCase$(strCellValue), "subf.")
                            If keywordPosition > 0 And _
                                columnCounter < 29 And _
                                ( _
                                    keywordSubPosition = 0 Or _
                                    ( _
                                        keywordSubPosition > 0 And _
                                        keywordPosition - 3 <> keywordSubPosition _
                                    ) _
                                ) _
                                Then
                                If tryAutoCorrect = vbYes And _
                                   Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, 29).Value) = "" Then
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, 29).Value = strCellValue
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Trim$(Left$(strCellValue, keywordPosition - 1))
                                   strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                Else
                                    'If autocorrection not activated or cell already filled, write an error
                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:="Not corrected: Wrong keyword for the current level (" & _
                                                level_name(columnCounter) & _
                                                ")." & _
                                                vbCrLf & _
                                                "Maybe targeted " & _
                                                level_name(29) & _
                                                " is already filled..."
                                    booErrorFound = True
                                End If
                            ' If none of the forma keyword were found, we were on a forma level cell and for a Plantae kingdom
                            ElseIf keywordPosition = 0 And _
                                keywordSubPosition = 0 And _
                                columnCounter = 29 And _
                                strKingdom = "Plantae" Then
                                ' The keyword must be present for forma under Plantae Kingdom
                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:="In the Plantae kingdom, the infra-species keyword (f.)" & _
                                            vbCrLf & _
                                            "must be present for the current level (" & _
                                            level_name(columnCounter) & ")"
                                booErrorFound = True
                            End If
                        End If
                        If Not booErrorFound Then
                            ' Test sub-variety
                            keywordPosition = InStr(LCase$(strCellValue), "subvar.")
                            If keywordPosition > 0 And _
                                columnCounter < 28 Then
                                If tryAutoCorrect = vbYes And _
                                   Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, 28).Value) = "" Then
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, 28).Value = strCellValue
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Trim$(Left$(strCellValue, keywordPosition - 1))
                                   strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                Else
                                    'If autocorrection not activated or cell already filled, write an error
                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:="Not corrected: Wrong keyword for the current level (" & _
                                                level_name(columnCounter) & _
                                                ")." & _
                                                vbCrLf & _
                                                "Maybe targeted " & _
                                                level_name(28) & _
                                                " is already filled..."
                                    booErrorFound = True
                                End If
                            ' If none of the sub-variety keyword were found, we were on a sub-variety level cell and for a Plantae kingdom
                            ElseIf keywordPosition = 0 And _
                                columnCounter = 28 And _
                                strKingdom = "Plantae" Then
                                ' The keyword must be present for sub variety under Plantae Kingdom
                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:="In the Plantae kingdom, the infra-species keyword (subvar.)" & _
                                            vbCrLf & _
                                            "must be present for the current level (" & _
                                            level_name(columnCounter) & ")"
                                booErrorFound = True
                            End If
                        End If
                        If Not booErrorFound Then
                            ' Test variety keyword
                            keywordPosition = InStr(LCase$(strCellValue), "var.")
                            keywordSubPosition = InStr(LCase$(strCellValue), "subvar.")
                            If keywordPosition > 0 And _
                                columnCounter < 27 And _
                                ( _
                                    keywordSubPosition = 0 Or _
                                    ( _
                                        keywordSubPosition > 0 And _
                                        keywordPosition - 3 <> keywordSubPosition _
                                    ) _
                                ) _
                                Then
                                If tryAutoCorrect = vbYes And _
                                   Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, 27).Value) = "" Then
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, 27).Value = strCellValue
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Trim$(Left$(strCellValue, keywordPosition - 1))
                                   strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                Else
                                    'If autocorrection not activated or cell already filled, write an error
                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:="Not corrected: Wrong keyword for the current level (" & _
                                                level_name(columnCounter) & _
                                                ")." & _
                                                vbCrLf & _
                                                "Maybe targeted " & _
                                                level_name(27) & _
                                                " is already filled..."
                                    booErrorFound = True
                                End If
                            ' If none of the variety keyword were found, we were on a variety level cell and for a Plantae kingdom
                            ElseIf keywordPosition = 0 And _
                                keywordSubPosition = 0 And _
                                columnCounter = 27 And _
                                Application.Sheets("TAXONOMY").Cells(rowCounter, 2).Value = "Plantae" Then
                                ' The keyword must be present for variety under Plantae Kingdom
                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:="In the Plantae kingdom, the infra-species keyword (var.)" & _
                                            vbCrLf & _
                                            "must be present for the current level (" & _
                                            level_name(columnCounter) & ")"
                                booErrorFound = True
                            End If
                        End If
                        If Not booErrorFound Then
                            ' Test sub-species
                            keywordPosition = InStr(LCase$(strCellValue), "subsp.") + _
                                                InStr(LCase$(strCellValue), "ssp.")
                            If keywordPosition > 0 And _
                                columnCounter < 26 Then
                                If tryAutoCorrect = vbYes And _
                                   Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, 26).Value) = "" Then
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, 26).Value = strCellValue
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Trim$(Left$(strCellValue, keywordPosition - 1))
                                   strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                Else
                                    'If autocorrection not activated or cell already filled, write an error
                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:="Not corrected: Wrong keyword for the current level (" & _
                                                level_name(columnCounter) & _
                                                ")." & _
                                                vbCrLf & _
                                                "Maybe targeted " & _
                                                level_name(26) & _
                                                " is already filled..."
                                    booErrorFound = True
                                End If
                            ' If none of the sub-species keyword were found, we were on a sub-species level cell and for a Plantae kingdom
                            ElseIf keywordPosition = 0 And _
                                columnCounter = 26 And _
                                strKingdom = "Plantae" Then
                                ' The keyword must be present for sub-species under Plantae Kingdom
                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:="In the Plantae kingdom, the infra-species keyword (subsp. or ssp.)" & _
                                            vbCrLf & _
                                            "must be present for the current level (" & _
                                            level_name(columnCounter)
                                booErrorFound = True
                            End If
                        End If
                        If Not booErrorFound Then
                            ' Test species keyword
                            keywordPosition = InStr(LCase$(strCellValue), "sp.")
                            keywordSubPosition = InStr(LCase$(strCellValue), "subsp.") + _
                                                    InStr(LCase$(strCellValue), "ssp.")
                            If keywordPosition > 0 And _
                                columnCounter < 25 And _
                                ( _
                                    keywordSubPosition = 0 Or _
                                    ( _
                                        keywordSubPosition > 0 And _
                                        keywordPosition - 3 <> keywordSubPosition And _
                                        keywordPosition - 1 <> keywordSubPosition _
                                    ) _
                                ) _
                                Then
                                If tryAutoCorrect = vbYes And _
                                   Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, 25).Value) = "" Then
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, 25).Value = strCellValue
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Trim$(Left$(strCellValue, keywordPosition - 1))
                                   strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                Else
                                    'If autocorrection not activated or cell already filled, write an error
                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:="Not corrected: Wrong keyword for the current level (" & _
                                                level_name(columnCounter) & _
                                                ")." & _
                                                vbCrLf & _
                                                "Maybe targeted " & _
                                                level_name(25) & _
                                                " is already filled..."
                                    booErrorFound = True
                                End If
                            End If
                        End If
                        If Not booErrorFound Then
                            ' Test subgenus keyword on an other level than keyword
                            keywordPosition = InStr(LCase$(strCellValue), "subgen.") + _
                                                InStr(LCase$(strCellValue), "subg.") + _
                                                InStr(LCase$(strCellValue), "sect.") + _
                                                InStr(LCase$(strCellValue), "subsect.") + _
                                                InStr(LCase$(strCellValue), "ser.")
                            If keywordPosition > 0 _
                               And _
                               columnCounter < 24 Then
                                If tryAutoCorrect = vbYes And _
                                   Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, 24).Value) = "" Then
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, 24).Value = strCellValue
                                   Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = Trim$(Left$(strCellValue, keywordPosition - 1))
                                   strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                Else
                                    'If autocorrection not activated or cell already filled, write an error
                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:="Not corrected: Wrong keyword for the current level (" & _
                                                level_name(columnCounter) & _
                                                ")." & _
                                                vbCrLf & _
                                                "Maybe targeted " & _
                                                level_name(24) & _
                                                " is already filled..."
                                    booErrorFound = True
                                End If
                            ' If none of the subgenus keyword were found, we were on a sub-genus level cell and for a Plantae kingdom
                            ElseIf keywordPosition = 0 And _
                                    columnCounter = 24 And _
                                    strKingdom = "Plantae" Then
                                    strSubLevelsSplited = Split(strCellValue)
                                    keywordPosition = InStr(LCase(strSubLevelsSplited(0)), "(")
                                    If UBound(strSubLevelsSplited) > 0 Then
                                        keywordPosition = keywordPosition + InStr(LCase(strSubLevelsSplited(1)), "(")
                                    End If
                                    If keywordPosition = 0 Then
                                        ' The keyword must be present for sub genuses under Plantae Kingdom
                                        WriteError rowCounter, columnCounter, i, 5, replacementMessage:="In the Plantae kingdom, the infra-genus keyword (subg., sect.,...)" & _
                                                    vbCrLf & _
                                                    "must be present for the current level (" & _
                                                    level_name(columnCounter) & ")"
                                        booErrorFound = True
                                    End If
                            End If
                        End If
                        If Not booErrorFound And columnCounter <= 23 Then
                            strSubLevelsSplited = Split(strCellValue)
                            If UBound(strSubLevelsSplited) > 0 Then
                                If ( _
                                    Left$(strSubLevelsSplited(1), 1) = "(" And _
                                    Right$(strSubLevelsSplited(1), 1) = ")" _
                                   ) Then
                                    If tryAutoCorrect = vbYes And _
                                        Application.Sheets("TAXONOMY").Cells(rowCounter, 24).Value = "" Then
                                        Application.Sheets("TAXONOMY").Cells(rowCounter, 24).Value = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                        Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = strSubLevelsSplited(0)
                                        strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                        WriteInfo rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                " was containing a subgenus name." & _
                                                vbCrLf & _
                                                "It has been moved to the right level and the current level adapted." & _
                                                vbCrLf & _
                                                "Please check it."
                                        booErrorFound = True
                                        Exit For
                                    Else
                                        WriteError rowCounter, columnCounter, i, 5, replacementMessage:="It's not possible to move the sub-genus entered in genus." & _
                                                vbCrLf & _
                                                "Either the sub-genus is already filled or have you the autocorrection not activated." & _
                                                vbCrLf & _
                                                "Please correct this."
                                        booErrorFound = True
                                        Exit For
                                    End If
                                End If
                            End If
                        End If
                    End If
                    ' Fifth, check, for levels bellow Genus (sub-genus, species,...) that name composition is
                    ' well made of the name coming from possible upper level - try to correct if possible and mark
                    ' as an error otherwise
                    If Not booErrorFound And columnCounter >= 24 Then
                        For iCounter = UBound(possibleUpperLevels(columnCounter).upper_level) - 1 To 0 Step -1
                            ' Cath the first possible upper level filled
                            If (Application.Sheets("TAXONOMY").Cells(rowCounter, possibleUpperLevels(columnCounter).upper_level(iCounter)).Value) <> "" Then
                                ' Split the content of the parent
                                strParentSplited = Split(Application.Sheets("TAXONOMY").Cells(rowCounter, possibleUpperLevels(columnCounter).upper_level(iCounter)).Value)
                                ' Split the content of the cell self
                                strSubLevelsSplited = Split(strCellValue)
                                ' As we start from the sub level sub genus we test well that the first part of the current cell name
                                ' is well the same as the first part of the parent name
                                If Not strSubLevelsSplited(0) = strParentSplited(0) And _
                                    possibleUpperLevels(columnCounter).upper_level(iCounter) >= 23 Then
                                    ' If not and we've activated the auto correction, bring that first part from the parent into the name of the current level cell
                                    If tryAutoCorrect = vbYes Then
                                        Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = _
                                            strParentSplited(0) & _
                                            " " & _
                                            LCase(Left$(strCellValue, 1)) & _
                                            Mid$(strCellValue, 2)
                                        strCellValue = Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value
                                        strSubLevelsSplited = Split(strCellValue)
                                        '... and write a warning telling we made a modification
                                        WriteInfo rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                " has been autocompleted for the first name part." & _
                                                vbCrLf & _
                                                "Please check it."
                                        booErrorFound = True
                                    Else
                                        WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                " is not composed of a valid/corresponding first name." & _
                                                vbCrLf & _
                                                "Please correct this."
                                        booErrorFound = True
                                        Exit For
                                    End If
                                End If
                                ' For levels above sub-genus
                                Select Case columnCounter
                                    'Sub-genus level: we take car here of the existence of a subgenus keyword at least
                                    Case Is = 24
                                        ' Test well the species level is comprised of at least two part (basis of a binomial)
                                        If UBound(strSubLevelsSplited) < 1 Then
                                            WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                    " must have at least a binomial structure." & _
                                                    vbCrLf & _
                                                    "Please correct this."
                                            booErrorFound = True
                                            Exit For
                                        ElseIf (InStr(LCase(strSubLevelsSplited(1)), "(") > 0 And _
                                                strKingdom = "Plantae") Then
                                            If Right$(LCase(strSubLevelsSplited(1)), 1) <> ")" Then
                                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                        " is not composed of at least a valid sub genus naming part." & _
                                                        vbCrLf & _
                                                        "Please correct this."
                                                booErrorFound = True
                                                Exit For
                                            Else
                                                If tryAutoCorrect = vbYes Then
                                                    strCellValue = _
                                                    strSubLevelsSplited(0) & " subg. " & _
                                                    Mid$(strSubLevelsSplited(1), 2, Len(strSubLevelsSplited(1)) - 2)
                                                    If UBound(strSubLevelsSplited) > 1 Then
                                                        For jCounter = 2 To UBound(strSubLevelsSplited)
                                                            strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                        Next jCounter
                                                    End If
                                                    Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = strCellValue
                                                    strSubLevelsSplited = Split(strCellValue)
                                                    '... and write a warning telling we made a modification
                                                    WriteWarning rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                            " has been adapted for the subgenus name part." & _
                                                            vbCrLf & _
                                                            "Please check it."
                                                    booErrorFound = True
                                                    Exit For
                                                Else
                                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                            " is not composed of at least a valid sub genus naming part." & _
                                                            vbCrLf & _
                                                            "Please correct this."
                                                    booErrorFound = True
                                                    Exit For
                                                End If
                                            End If
                                        ' In the case of Plantae the structure of a sub-genus must be at least of 3 name elements
                                        ElseIf strKingdom = "Plantae" And UBound(strSubLevelsSplited) < 2 Then
                                            WriteError rowCounter, columnCounter, i, 5, replacementMessage:="For Plantae, " & _
                                                    level_name(columnCounter) & _
                                                    " must have at least a trinomial structure." & _
                                                    vbCrLf & _
                                                    "Please correct this."
                                            booErrorFound = True
                                            Exit For
                                        ' In the case the subgenus keyword is not present...
                                        ElseIf (InStr(LCase(strSubLevelsSplited(1)), "subgen.") > 0 Or _
                                                InStr(LCase(strSubLevelsSplited(1)), "subg.") > 0 Or _
                                                InStr(LCase(strSubLevelsSplited(1)), "sect.") > 0 Or _
                                                InStr(LCase(strSubLevelsSplited(1)), "subsect.") > 0 Or _
                                                InStr(LCase(strSubLevelsSplited(1)), "ser.") > 0 _
                                               ) And strKingdom <> "Plantae" Then
                                            If UBound(strSubLevelsSplited) > 1 Then
                                                If tryAutoCorrect = vbYes Then
                                                    strCellValue = _
                                                        strSubLevelsSplited(0) & _
                                                        " (" & strSubLevelsSplited(2) & ")"
                                                    If UBound(strSubLevelsSplited) > 2 Then
                                                        For jCounter = 3 To UBound(strSubLevelsSplited)
                                                            strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                        Next jCounter
                                                    End If
                                                    Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = strCellValue
                                                    strSubLevelsSplited = Split(strCellValue)
                                                    '... and write a warning telling we made a modification
                                                    WriteInfo rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                            " has been autocompleted for the first name part." & _
                                                            vbCrLf & _
                                                            "Please check it."
                                                Else
                                                    WriteInfo rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                            " has not been modified." & _
                                                            vbCrLf & _
                                                            "The use of a subgenus keyword outside Plantae is allowed but not often used..." & _
                                                            vbCrLf & _
                                                            "Please check it."
                                                End If
                                                booErrorFound = True
                                                Exit For
                                            Else
                                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                        " is not composed of at least a valid sub genus naming part." & _
                                                        vbCrLf & _
                                                        "Please correct this."
                                                booErrorFound = True
                                                Exit For
                                            End If
                                        ElseIf Not _
                                           (InStr(LCase(strSubLevelsSplited(1)), "subgen.") > 0 Or _
                                            InStr(LCase(strSubLevelsSplited(1)), "subg.") > 0 Or _
                                            InStr(LCase(strSubLevelsSplited(1)), "sect.") > 0 Or _
                                            InStr(LCase(strSubLevelsSplited(1)), "subsect.") > 0 Or _
                                            InStr(LCase(strSubLevelsSplited(1)), "ser.") > 0 Or _
                                            InStr(LCase(strSubLevelsSplited(1)), "(") > 0 _
                                           ) Then
                                            '... and autocorrection is activated, bring the needed element
                                            If tryAutoCorrect = vbYes Then
                                                strCellValue = _
                                                    strSubLevelsSplited(0) & _
                                                    IIf(strKingdom = "Plantae", _
                                                        " subg. " & strSubLevelsSplited(1), _
                                                        " (" & strSubLevelsSplited(1) & ")")
                                                If UBound(strSubLevelsSplited) > 1 Then
                                                    For jCounter = 2 To UBound(strSubLevelsSplited)
                                                        strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                    Next jCounter
                                                End If
                                                Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = strCellValue
                                                strSubLevelsSplited = Split(strCellValue)
                                                '... and write a warning telling we made a modification
                                                WriteWarning rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                        " has been autocompleted for the subgenus name part." & _
                                                        vbCrLf & _
                                                        "Please check it."
                                                booErrorFound = True
                                                Exit For
                                            Else
                                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                        " is not composed of at least a valid sub genus naming part." & _
                                                        vbCrLf & _
                                                        "Please correct this."
                                                booErrorFound = True
                                                Exit For
                                            End If
                                        End If
                                    ' Species level
                                    Case Is = 25
                                        ' Test well the species level is comprised of at least two part (basis of a binomial)
                                        If UBound(strSubLevelsSplited) < 1 Then
                                            WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                    " must have at least a binomial structure." & _
                                                    vbCrLf & _
                                                    "Please correct this."
                                            booErrorFound = True
                                            Exit For
                                        End If
                                        ' Test well, if the kingdom is Plantae and if the parent is a sub-genus, that a
                                        ' sub-genus keyword is present in the name, otherwise falls in error
                                        Select Case possibleUpperLevels(columnCounter).upper_level(iCounter)
                                            Case Is = 24
                                                Select Case strKingdom
                                                    Case Is = "Plantae"
                                                        If InStr(LCase(strSubLevelsSplited(1)), "subgen.") + _
                                                            InStr(LCase(strSubLevelsSplited(1)), "subg.") + _
                                                            InStr(LCase(strSubLevelsSplited(1)), "sect.") + _
                                                            InStr(LCase(strSubLevelsSplited(1)), "subsect.") + _
                                                            InStr(LCase(strSubLevelsSplited(1)), "ser.") = 0 Then
                                                            If tryAutoCorrect = vbYes Then
                                                                If Left$(strSubLevelsSplited(1), 1) = "(" Then
                                                                    If Right$(strSubLevelsSplited(1), 1) <> ")" Then
                                                                        WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                            " is not composed of the binomial species own part." & _
                                                                            vbCrLf & _
                                                                            "Please correct this."
                                                                        booErrorFound = True
                                                                        Exit For
                                                                    ElseIf Mid$(strSubLevelsSplited(1), 2, Len(strSubLevelsSplited(1)) - 2) = strParentSplited(2) Then
                                                                        strCellValue = _
                                                                        strParentSplited(0) & " " & _
                                                                        LCase(strParentSplited(1)) & " " & _
                                                                        strParentSplited(2)
                                                                        If UBound(strSubLevelsSplited) > 1 Then
                                                                            For jCounter = 2 To UBound(strSubLevelsSplited)
                                                                                strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                            Next jCounter
                                                                        End If
                                                                    Else
                                                                        strCellValue = _
                                                                        strParentSplited(0) & " " & _
                                                                        LCase(strParentSplited(1)) & " " & _
                                                                        Mid$(strSubLevelsSplited(1), 2, Len(strSubLevelsSplited(1)) - 2)
                                                                        If UBound(strSubLevelsSplited) > 1 Then
                                                                            For jCounter = 2 To UBound(strSubLevelsSplited)
                                                                                strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                            Next jCounter
                                                                        End If
                                                                    End If
                                                                Else
                                                                    If UBound(strParentSplited) > 1 Then
                                                                        strCellValue = _
                                                                        strParentSplited(0) & " " & _
                                                                        LCase(strParentSplited(1)) & " " & _
                                                                        strParentSplited(2)
                                                                        For jCounter = 1 To UBound(strSubLevelsSplited)
                                                                            strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                        Next jCounter
                                                                    Else
                                                                        WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(24) & _
                                                                            " is apparently in error." & _
                                                                            vbCrLf & _
                                                                            "Please adapt the " & _
                                                                            level_name(columnCounter) & _
                                                                            " when the " & _
                                                                            level_name(24) & _
                                                                            " will be corrected."
                                                                        booErrorFound = True
                                                                        Exit For
                                                                    End If
                                                                End If
                                                                Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = strCellValue
                                                                strSubLevelsSplited = Split(strCellValue)
                                                                '... and write a warning telling we made a modification
                                                                WriteWarning rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                        " has been autocompleted including the sub-genus name part." & _
                                                                        vbCrLf & _
                                                                        "Please check it."
                                                                Exit For
                                                            Else
                                                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                        " is not composed of at least a valid sub genus naming part." & _
                                                                        vbCrLf & _
                                                                        "Please correct this."
                                                                booErrorFound = True
                                                                Exit For
                                                            End If
                                                        ElseIf UBound(strSubLevelsSplited) > 1 Then
                                                            If strSubLevelsSplited(2) <> strParentSplited(2) Then
                                                                If tryAutoCorrect = vbYes And UBound(strSubLevelsSplited) > 2 Then
                                                                    strCellValue = _
                                                                    strParentSplited(0) & " " & _
                                                                    LCase(strParentSplited(1)) & " " & _
                                                                    strParentSplited(2)
                                                                    For jCounter = 2 To UBound(strSubLevelsSplited)
                                                                        strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                    Next jCounter
                                                                    Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = strCellValue
                                                                    strSubLevelsSplited = Split(strCellValue)
                                                                    '... and write a warning telling we made a modification
                                                                    WriteWarning rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                            " has been autocompleted including the sub-genus name part." & _
                                                                            vbCrLf & _
                                                                            "Please check it."
                                                                    booErrorFound = True
                                                                    Exit For
                                                                Else
                                                                    '... and write a warning telling sub-genus part is different
                                                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                            " has got a sub-genus name part given different from the one given at sub-genus level." & _
                                                                            vbCrLf & _
                                                                            "Please check it."
                                                                    booErrorFound = True
                                                                    Exit For
                                                                End If
                                                            Else
                                                                Exit For
                                                            End If
                                                        Else
                                                            WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                    " is not composed of at least a valid sub genus naming followed by its own name part." & _
                                                                    vbCrLf & _
                                                                    "Please correct this."
                                                            booErrorFound = True
                                                            Exit For
                                                        End If
                                                    Case Else
                                                        If Left$(strSubLevelsSplited(1), 1) = "(" Then
                                                            If Right$(strSubLevelsSplited(1), 1) <> ")" Then
                                                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                    " is not composed of the binomial species own part." & _
                                                                    vbCrLf & _
                                                                    "Please correct this."
                                                                booErrorFound = True
                                                                Exit For
                                                            ElseIf Mid$(strSubLevelsSplited(1), 2, Len(strSubLevelsSplited(1)) - 2) <> Mid$(strParentSplited(1), 2, Len(strParentSplited(1)) - 2) Then
                                                                strCellValue = _
                                                                strParentSplited(0) & " " & _
                                                                strParentSplited(1)
                                                                If UBound(strSubLevelsSplited) > 1 Then
                                                                    For jCounter = 2 To UBound(strSubLevelsSplited)
                                                                        strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                    Next jCounter
                                                                End If
                                                                booErrorFound = True
                                                            End If
                                                        ElseIf InStr(LCase(strSubLevelsSplited(1)), "subgen.") + _
                                                            InStr(LCase(strSubLevelsSplited(1)), "subg.") + _
                                                            InStr(LCase(strSubLevelsSplited(1)), "sect.") + _
                                                            InStr(LCase(strSubLevelsSplited(1)), "subsect.") + _
                                                            InStr(LCase(strSubLevelsSplited(1)), "ser.") > 0 Then
                                                            If UBound(strSubLevelsSplited) > 2 Then
                                                                If InStr(LCase(strParentSplited(1)), "subgen.") + _
                                                                    InStr(LCase(strParentSplited(1)), "subg.") + _
                                                                    InStr(LCase(strParentSplited(1)), "sect.") + _
                                                                    InStr(LCase(strParentSplited(1)), "subsect.") + _
                                                                    InStr(LCase(strParentSplited(1)), "ser.") > 0 Then
                                                                    If UBound(strParentSplited) = 1 Then
                                                                        strCellValue = _
                                                                        strParentSplited(0) & " " & _
                                                                        LCase(strParentSplited(1))
                                                                        For jCounter = 3 To UBound(strSubLevelsSplited)
                                                                            strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                        Next jCounter
                                                                        Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = strCellValue
                                                                        strSubLevelsSplited = Split(strCellValue)
                                                                        WriteInfo rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                            " has been left unchanged but be warned of the occurence of a problem at sub-genus level." & _
                                                                            vbCrLf & _
                                                                            "Please check it."
                                                                        booErrorFound = True
                                                                        Exit For
                                                                    Else
                                                                        strCellValue = _
                                                                        strParentSplited(0) & " " & _
                                                                        LCase(strParentSplited(1)) & " " & _
                                                                        strParentSplited(2)
                                                                        For jCounter = 3 To UBound(strSubLevelsSplited)
                                                                            strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                        Next jCounter
                                                                        booErrorFound = True
                                                                    End If
                                                                Else
                                                                    strCellValue = _
                                                                    strParentSplited(0) & " " & _
                                                                    strParentSplited(1)
                                                                    For jCounter = 3 To UBound(strSubLevelsSplited)
                                                                        strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                    Next jCounter
                                                                    booErrorFound = True
                                                                End If
                                                            Else
                                                                WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                    " is not composed of the binomial species own part." & _
                                                                    vbCrLf & _
                                                                    "Please correct this."
                                                                booErrorFound = True
                                                                Exit For
                                                            End If
                                                        ElseIf tryAutoCorrect = vbYes Then
                                                            If InStr(LCase(strParentSplited(1)), "subgen.") + _
                                                                InStr(LCase(strParentSplited(1)), "subg.") + _
                                                                InStr(LCase(strParentSplited(1)), "sect.") + _
                                                                InStr(LCase(strParentSplited(1)), "subsect.") + _
                                                                InStr(LCase(strParentSplited(1)), "ser.") > 0 Then
                                                                If UBound(strParentSplited) = 1 Then
                                                                    WriteInfo rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                        " has been left unchanged but be warned of the occurence of a problem at sub-genus level." & _
                                                                        vbCrLf & _
                                                                        "Please check it."
                                                                    booErrorFound = True
                                                                    Exit For
                                                                Else
                                                                    strCellValue = _
                                                                    strParentSplited(0) & " " & _
                                                                    LCase(strParentSplited(1)) & " " & _
                                                                    strParentSplited(2)
                                                                    For jCounter = 1 To UBound(strSubLevelsSplited)
                                                                        strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                    Next jCounter
                                                                    booErrorFound = True
                                                                End If
                                                            Else
                                                                strCellValue = _
                                                                strParentSplited(0) & " " & _
                                                                strParentSplited(1)
                                                                For jCounter = 1 To UBound(strSubLevelsSplited)
                                                                    strCellValue = strCellValue & " " & strSubLevelsSplited(jCounter)
                                                                Next jCounter
                                                                booErrorFound = True
                                                            End If
                                                        End If
                                                        If tryAutoCorrect = vbYes And booErrorFound Then
                                                            Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value = strCellValue
                                                            strSubLevelsSplited = Split(strCellValue)
                                                            '... and write a warning telling we made a modification
                                                            WriteWarning rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                    " has been autocompleted including the sub-genus name part." & _
                                                                    vbCrLf & _
                                                                    "Please check it."
                                                        ElseIf booErrorFound Then
                                                            WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                    " is not composed of at least a valid sub genus naming followed by its own name part." & _
                                                                    vbCrLf & _
                                                                    "Please correct this."
                                                        End If
                                                        Exit For
                                                End Select
                                            Case Else
                                                ' If a subgenus keyword is present on the species but parent is not sub-genus...
                                                If (InStr(LCase(strSubLevelsSplited(1)), "subgen.") > 0 Or _
                                                     InStr(LCase(strSubLevelsSplited(1)), "subg.") > 0 Or _
                                                     InStr(LCase(strSubLevelsSplited(1)), "sect.") > 0 Or _
                                                     InStr(LCase(strSubLevelsSplited(1)), "subsect.") > 0 Or _
                                                     InStr(LCase(strSubLevelsSplited(1)), "ser.") > 0 _
                                                    ) Then
                                                    WriteError rowCounter, 24, i, 5, replacementMessage:="For botany, a " & _
                                                            level_name(columnCounter) & _
                                                            " attached to a genus cannot mention the keyword qualifying the intermediate level (i.e.: subg., sect.,...)." & _
                                                            vbCrLf & _
                                                            "The intermediate level entry is missing, please correct this or remove the subgenus keyword from your species."
                                                    i = i + 1
                                                    WriteError rowCounter, columnCounter, i, 5, replacementMessage:="For botany, a " & _
                                                            level_name(columnCounter) & _
                                                            " attached to a genus cannot mention the keyword qualifying the intermediate level (i.e.: subg., sect.,...)." & _
                                                            vbCrLf & _
                                                            "The intermediate level entry is missing, please correct this or remove the subgenus keyword from your species."
                                                    booErrorFound = True
                                                    Exit For
                                                ElseIf Left$(strSubLevelsSplited(1), 1) = "(" Then
                                                    If UBound(strSubLevelsSplited) < 2 Then
                                                        WriteError rowCounter, columnCounter, i, 5, replacementMessage:="The second element of species name cannot be composed of parenthesis - " & _
                                                                vbCrLf & _
                                                                "Parenthesis are left for specifying sub-genus or to qualify an author in the recomposition of the species name." & _
                                                                vbCrLf & _
                                                                "Please correct this."
                                                    Else
                                                        WriteError rowCounter, 24, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                " attached to a genus cannot mention the keyword qualifying the sub-genus level (i.e.: subg., sect.,...)." & _
                                                                vbCrLf & _
                                                                "The intermediate level entry is missing, please correct this or remove the subgenus qualification from your species."
                                                        i = i + 1
                                                        WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                                " attached to a genus cannot mention the keyword qualifying the sub-genus level (i.e.: subg., sect.,...)." & _
                                                                vbCrLf & _
                                                                "The intermediate level entry is missing, please correct this or remove the subgenus qualification from your species."
                                                    End If
                                                    booErrorFound = True
                                                    Exit For
                                                End If
                                        End Select
                                    ' Infra-species levels
                                    Case Is >= 26
                                        ' Test well the infra-species level is comprised of at least three parts (basis of a trinomial)
                                        If UBound(strSubLevelsSplited) < 2 Then
                                            WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                    " must have at least a trinomial structure." & _
                                                    vbCrLf & _
                                                    "Please correct this."
                                            booErrorFound = True
                                        End If
                                        ' If the second naming element is not the same as what's on the parent... fail !
                                        ' We should get this more subtile by also testing if sub-genus level is filled and therefore
                                        ' by testing more deeper...
                                        If strSubLevelsSplited(1) <> strParentSplited(1) Then
                                            WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                                    " has to comprise at least the binomial structure of the taxon this entry is attached to." & _
                                                    vbCrLf & _
                                                    "Please correct this."
                                            booErrorFound = True
                                        End If
                                        Exit For
                                End Select
                            End If
                        Next iCounter
                    End If
                    If Not booErrorFound And columnCounter >= 25 And strCellValue <> "" Then
                        strSubLevelsSplited = Split(strCellValue)
                        If (InStr(LCase(strSubLevelsSplited(1)), "subgen.") > 0 Or _
                             InStr(LCase(strSubLevelsSplited(1)), "subg.") > 0 Or _
                             InStr(LCase(strSubLevelsSplited(1)), "sect.") > 0 Or _
                             InStr(LCase(strSubLevelsSplited(1)), "subsect.") > 0 Or _
                             InStr(LCase(strSubLevelsSplited(1)), "ser.") > 0 _
                            ) Then
                            If UBound(strSubLevelsSplited) > 2 Then
                                If InStr(LCase(strSubLevelsSplited(3)), "subgen.") > 0 Or _
                                   InStr(LCase(strSubLevelsSplited(3)), "subg.") > 0 Or _
                                   InStr(LCase(strSubLevelsSplited(3)), "sect.") > 0 Or _
                                   InStr(LCase(strSubLevelsSplited(3)), "subsect.") > 0 Or _
                                   InStr(LCase(strSubLevelsSplited(3)), "ser.") > 0 Or _
                                   (Left$(strSubLevelsSplited(3), 1) = "(" And _
                                    Right$(strSubLevelsSplited(3), 1) = ")" _
                                   ) Then
                                    booErrorFound = True
                                End If
                            Else
                                booErrorFound = True
                            End If
                        ElseIf Left$(strSubLevelsSplited(1), 1) = "(" And _
                                Right(strSubLevelsSplited(1), 1) = ")" Then
                            If UBound(strSubLevelsSplited) > 1 Then
                                If InStr(LCase(strSubLevelsSplited(2)), "subgen.") > 0 Or _
                                   InStr(LCase(strSubLevelsSplited(2)), "subg.") > 0 Or _
                                   InStr(LCase(strSubLevelsSplited(2)), "sect.") > 0 Or _
                                   InStr(LCase(strSubLevelsSplited(2)), "subsect.") > 0 Or _
                                   InStr(LCase(strSubLevelsSplited(2)), "ser.") > 0 Or _
                                   (Left$(strSubLevelsSplited(2), 1) = "(" And _
                                    Right$(strSubLevelsSplited(2), 1) = ")" _
                                   ) Then
                                    booErrorFound = True
                                End If
                            Else
                                booErrorFound = True
                            End If
                        End If
                        If booErrorFound Then
                            WriteError rowCounter, columnCounter, i, 5, replacementMessage:=level_name(columnCounter) & _
                                        " depending on a qualified subgenus should have a valid binomial name." & _
                                        vbCrLf & _
                                        "Please correct this."
                        End If
                    End If
                End If
                If booErrorFound Then
                    i = i + 1
                End If
                booErrorFound = False
            Next columnCounter
            If (rowCounter Mod 1000) = 0 Then
                ActiveWorkbook.Save
            End If
        Next rowCounter
        ' Loop through all Cells to analyse content and pinpoint the wrong ones (with eventual corrections applied)
        ' Second parsing to catch the mising parenties
        For rowCounter = 2 To LastR
            'Display parsing counter in status bar
            Application.StatusBar = "Second parsing - Processing... Please do not disturb... Checked rows: " & rowCounter - 1
            ' For each column of row...
            For columnCounter = 1 To LastC
                If Trim$(Application.Sheets("TAXONOMY").Cells(rowCounter, columnCounter).Value) <> "" Then
                    If UBound(possibleUpperLevels(columnCounter).upper_level) > 0 And previousLevel <> 0 Then
                        'Test made to check the entries requiring missing/wrong parents
                        For iCounter = UBound(possibleUpperLevels(columnCounter).upper_level) - 1 To 0 Step -1
                            booErrorFound = True
                            strPUL = strPUL & ", " & level_name(possibleUpperLevels(columnCounter).upper_level(iCounter))
                            If possibleUpperLevels(columnCounter).upper_level(iCounter) = previousLevel Then
                                booErrorFound = False
                                Exit For
                            End If
                        Next iCounter
                        If booErrorFound Then
                            strPUL = Mid$(strPUL, 3)
                            strReplacementMessage = "No valid possible upper level was found..." & _
                                    vbCrLf & _
                                    "Please correct this by providing one of these parent levels: " & _
                                    strPUL & _
                                    " for the current one (" & level_name(columnCounter) & ")"
                        End If
                    End If
                    strPUL = ""
                    previousLevel = columnCounter
                End If
                If booErrorFound Then
                    WriteError rowCounter, columnCounter, i, 3, replacementMessage:=strReplacementMessage
                    strReplacementMessage = ""
                    i = i + 1
                End If
                booErrorFound = False
            Next columnCounter
            previousLevel = 0
            If (rowCounter Mod 1000) = 0 Then
                ActiveWorkbook.Save
            End If
        Next rowCounter
        'Autofit columns
        Application.Sheets("CheckTaxa").Activate
        ActiveSheet.Columns("A:F").AutoFit
        LastR = Application.Sheets("CheckTaxa").Cells.Find("*", SearchOrder:=xlByRows, SearchDirection:=xlPrevious).Row
        If LastR < 2 Then LastR = 2
        Application.Sheets("TAXONOMY").Activate
        ActiveSheet.Columns("A:AE").AutoFit
                
        Application.ScreenUpdating = True
        ' If no errors encountered delete the sheet
        ' Otherwise generate an error
        If FeuilleExiste("CheckTaxa") Then
            For rowCounter = 2 To LastR
                If Application.Sheets("CheckTaxa").Cells(rowCounter, 6).Value = "error" Then
                    Err.Raise vbObjectError + 600, _
                              "ExportTaxonomy2XML::CheckTaxa()", _
                              "Errors occured during the taxa check process ! " & _
                              "Please check the sheet CheckTaxa to get the list of problems."
                End If
            Next rowCounter
            Application.DisplayAlerts = False
            Application.Sheets("CheckTaxa").Delete
            Application.DisplayAlerts = True
            If displayCheckMessages Then
                MsgBox "Every taxa name composition seem correct after simple check."
            End If
        Else
            If displayCheckMessages Then
                MsgBox "Every taxa name composition seem correct after simple check."
            End If
        End If
        CheckTaxa = True
    Else
        MsgBox "There must be a sheet named 'TAXONOMY'. " & _
                "Please, rename the sheet that contains information about your TAXONOMYs and run the program again."
        CheckTaxa = False
    End If
    
Exit_CheckTaxa:
    
    Application.StatusBar = ""
    Application.Cursor = xlDefault
    Exit Function
    
Err_CheckTaxa:

    MsgBox prompt:="An error occured in function CheckTaxa." & vbCrLf & _
                   "Error Number: " & Err.Number & "." & vbCrLf & _
                   "Error description: " & Err.Description & ".", _
            Buttons:=vbCritical, _
            Title:="Error in sub CheckTaxa"
    CheckTaxa = False
    GoTo Exit_CheckTaxa
    
End Function

Public Sub CheckTaxonomyWithAutoCorrection()

CheckTaxa True, vbYes

End Sub

Public Sub CheckTaxonomyWithoutAutoCorrection()

CheckTaxa True, vbNo

End Sub

'DataSets/DataSet/Metadata
Private Sub XMLMetadata(ByRef dom As MSXML2.DOMDocument60, ByRef node As MSXML2.IXMLDOMNode)

    Dim xmlVersion As MSXML2.IXMLDOMElement
    Dim xmlVersionMajor As MSXML2.IXMLDOMElement
    Dim xmlVersionMinor As MSXML2.IXMLDOMElement
    
    node.appendChild dom.createTextNode(vbCrLf & Space$(4))
    Set xmlVersion = dom.createNode(NODE_ELEMENT, "Version", "http://www.tdwg.org/schemas/abcd/2.06")
    node.appendChild xmlVersion
    xmlVersion.appendChild dom.createTextNode(vbCrLf & Space$(6))
    Set xmlVersionMajor = dom.createNode(NODE_ELEMENT, "Major", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlVersionMajor.Text = "1"
    xmlVersion.appendChild xmlVersionMajor
    xmlVersion.appendChild dom.createTextNode(vbCrLf & Space$(6))
    Set xmlVersionMinor = dom.createNode(NODE_ELEMENT, "Minor", "http://www.tdwg.org/schemas/abcd/2.06")
    xmlVersionMinor.Text = "2"
    xmlVersion.appendChild xmlVersionMinor
    xmlVersion.appendChild dom.createTextNode(vbCrLf & Space$(4))
    node.appendChild dom.createTextNode(vbCrLf & Space$(2))
    
End Sub

