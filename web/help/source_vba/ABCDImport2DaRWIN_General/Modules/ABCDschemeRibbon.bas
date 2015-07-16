'Ribbon ABCDscheme

Public Sub RunExportXML(control As IRibbonControl)
    CreateXML
End Sub

Public Sub RunShowLatLong(control As IRibbonControl)
    frmLatLong.Show
End Sub

Public Sub RunCheckLatLong(control As IRibbonControl)
    CheckLatLong
End Sub

Public Sub RunCheckHeaders(control As IRibbonControl)
    If CheckHeaders(check:=True) = True Then
    Else:
        Exit Sub
    End If
End Sub

Public Sub RunCheckDuplicatedID(control As IRibbonControl)
    CheckDuplicatedID
End Sub
