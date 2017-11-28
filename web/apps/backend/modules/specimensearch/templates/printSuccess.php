<?php slot('title', __('Print Specimens'));  ?>
<?php use_javascript('../barcode/jquery-barcode.js') ?>

<div class="page">
  <h1><?php echo __('Print Specimens');?></h1>
  

  
  <div class="templatecontainer">	

   <br/>
	<div name="div_scale" id="div_scale" style="height:10mm">
		
	</div>
    <div style="display: inline; margin-bottom: 0; padding:0">
    <form action=""  style="display: inline; margin: 0;">
     <label for="lbltem">Templates:</label>
     <select name="lbltem" id="lbltem" style='width:300px;'">
		<option value="-1">Pick up a label</option>
      
     </select>

     <a href=# id="print" title="Print Label"><button type="button" onclick="window.print();return false;">Print</button> </a>
	<div>Use the "print preview" feature of your browser to check the layout</div>
    </form>
    </div>
	<div id="print_info" name="print_info"></div>
	<div id="print_page_size" name="print_page_size"></div>
  <br/><br/><br/>
  <div> 

	<div id="tmpData" name="tmpData" class="hideLabelXML">
	<?php
	echo htmlspecialchars_decode("<search_result>");
	echo htmlspecialchars_decode("<specimens>");
	foreach($specimensearch as $specimen) 
		{
			//echo $specimen->getXMLRepresentation();
			$tmpSpecXML = new XMLRepresentationOfSpecimen($specimen);
			echo htmlspecialchars_decode($tmpSpecXML->getXMLRepresentation());
			//echo htmlspecialchars($tmpSpecXML->getXMLRepresentation());
			
		}
	echo htmlspecialchars_decode("</specimens>");
	echo htmlspecialchars_decode("</search_result>");
	?>

	</div>  



   
    <br/><br/><br/>
    
    <script type="text/javascript" src="/js/jquery.jstore-all-min.js"></script>  
    <script type="text/javascript" src="/js/jquery-ui-1.8.1.custom.js"></script>   
    <!--<script type="text/javascript" src="/js/mylabelgrid.js"></script>-->


    <script type="text/javascript">
	$.fn.hasOverflow = function() {
    var $this = $(this);
    var $children = $this.find('*');
    var len = $children.length;

    if (len) {
        var maxWidth = 0;
        var maxHeight = 0
        $children.map(function(){
            maxWidth = Math.max(maxWidth, $(this).outerWidth(true));
            maxHeight = Math.max(maxHeight, $(this).outerHeight(true));
        });

        return maxWidth > $this.width() || maxHeight > $this.height();
    }

    return false;
};

	$(document).ready(function() {
					getTemplateMeta();
					
					$( "#lbltem" ).change(function() 
					{
						var idxLabel=$("#lbltem").val();
						if(idxLabel!=-1)
						{
							getLabelDescId(idxLabel);
						}

					});
                  		

	});

	function getTemplateMeta()
	{
		var url_tmp="/backend.php/printtemplate.xml/desc";
		go_template_meta(url_tmp);
	}
	
	function go_template_meta(request_url)
	{
	
					
		var request=$.ajax({
			type: "GET",
			url: request_url,
			dataType: "xml",
			async: false
			});
		request.done(function(xml){
			
				$(xml).find('label_descriptions > label').each(
											function(index)
											{
												var id_tmp=$(this).find('id').text();
												var label_tmp=$(this).find('label').text();
												var option_tmp= "<option value=\""+id_tmp+"\">"+label_tmp+"</option>";
												$('#lbltem').append(option_tmp);
													
													
											}
											);
				
				
				
			});
		var xmlData=getDataFromDiv("#tmpData");
		//alert(xmlData);
	}
	
	function getDataFromDiv(p_divId)
	{
		return $("#tmpData").html();
	}
	
	function getLabelDescId(p_id)
	{
		var url="/backend.php/printtemplate.xml/"+p_id;
		//alert(url);
		$.ajax({
			type: "GET",
			url: url,
			dataType: "xml",
			success: function(xml) {

				var tmpXML = xml;
				//alert(xml);
           			//sessionStorage['darwin_current_label_desc'] = tmpXML;
				arrayLabelGeneralDesc=[];
				$(xml).find('label_descriptions > label > label_desc > rows').each(function(index)
				{
					//structure for metadata at whole label level
					var rows_label=$(this).text();
					var cols_label=$(this).siblings("columns").text();
					var xml_unit_separator=$(this).siblings("xml_unit_separator").text();
					var height_overflow=$(this).siblings("height_overflow").text();
					var arrayLabelGeneralDescCSS= [];
					var maxPageHeight= $(this).siblings("max_page_height").text();
					var autoEnlarge= $(this).siblings("auto_enlarge").text();
					$css_descriptionLabel=$(this).siblings("css_description_label");
					$css_descriptionLabel.find("attribute").each(
						function()
						{
							var attribute=$(this).text();
							var value=$(this).siblings("value").text();
							arrayLabelGeneralDescCSS.push({ attribute: attribute, value:value});
						}
					);
					//alert(maxPageHeight);
					arrayLabelGeneralDesc.push({xml_unit_separator: xml_unit_separator, rows: rows_label, columns:cols_label, nested_css:  arrayLabelGeneralDescCSS, heightOverflow: height_overflow, maxPageHeight: maxPageHeight, autoEnlarge: autoEnlarge });
					
					
				});				
				arrayXmlDesc = [];
				//metadata field level
				$(xml).find('label_descriptions > label > label_desc > fields > field > path').each(function(index)
				{
					//alert($(this).text());
					var path=$(this).text();
					var rows=$(this).siblings("rows").text();
					var columns=$(this).siblings("columns").text();
					
					var label=$(this).siblings("label_field").text();
                    //ftheeten 2017 08 10
                    var barcode=false;
                    var barcodeTmp=$(this).siblings("is_barcode").text();
                    if(barcodeTmp=="true")
                    {
                        barcode=true;
                    }
                    var module_size=$(this).siblings("module_size").text();
                    var barcode_format=$(this).siblings("barcode_format").text();
                    var barcode_height=$(this).siblings("barcode_height").text();
                    var barcode_width=$(this).siblings("barcode_width").text();
                    
					//alert(label);
					$css_description=$(this).siblings("css_description");
					var arrayXmlDescCSS = [];
					$css_description.find("attribute").each(
						function()
						{
							var attribute=$(this).text();
							var value=$(this).siblings("value").text();
							//alert(attribute);
							//alert(value);
							arrayXmlDescCSS.push({ attribute: attribute, value:value});
						}

					);
					var limit=$(this).siblings("limit").text();
					var prefix=$(this).siblings("prefix").text();
					var suffix=$(this).siblings("suffix").text();
					var glue=$(this).siblings("glue").text();
					var truncate_length=$(this).siblings("truncate_length").text();
					var truncate_sign=$(this).siblings("truncate_sign").text();
					var mapping_mode = $(this).siblings("mapping_mode").text();
					var arrayMatchAndReplace=[];
					$match_replace=$(this).siblings("mapping_values");
					$match_replace.find("old_value").each(
						function()
						{
							var old_value=$(this).text();
							var new_value=$(this).siblings("new_value").text();
							if(old_value.length>0||new_value.length>0)
							{
								if(old_value.length==0)
								{
									old_value="";
								}
								if(new_value.length==0)
								{
									new_value="";
								}
								arrayMatchAndReplace.push({old_value: old_value, new_value: new_value});
							}
						}
					);
					//structure for metadata at field level
					arrayXmlDesc.push({ path: path, rows:rows, columns:columns, label:label, nested_css: arrayXmlDescCSS, limit:limit, prefix:prefix, suffix: suffix, glue : glue, mapping_mode: mapping_mode, match_and_replace: arrayMatchAndReplace, truncate_length : truncate_length, truncate_sign : truncate_sign
                    //ftheeten 2017 08 10
                    ,barcode: barcode
                    ,module_size: module_size
                    ,barcode_format: barcode_format
                    ,barcode_width: barcode_width
                    ,barcode_height: barcode_height
                    });


				});
				//alert(xml);
				//var tmpXML = xml;
				
				//get XML data (records to be displayed) from hidde div
				var tmpXML = getDataFromDiv("#tmpData");
				var xmlDarwinData=$.parseXML(tmpXML);

				//place here for Ajax synchronisation 
				$("#recordsDarwin tr").remove();
				var tableHTMLAllLabels=$('<table></table>');
				$('#recordsDarwin').append(tableHTMLAllLabels);
				var rowHTMLAllLabels= $('<tr></tr>');
				var currentLabelLine=0;
				var rowsLabel=-1;
				if(arrayLabelGeneralDesc.length>0)
				{
					//retrieve metadata main label level
					var tmpLabelMetadata=arrayLabelGeneralDesc[0];
					var unit_separator=tmpLabelMetadata.xml_unit_separator;
					 rowsLabel=tmpLabelMetadata.rows;
					var columnsLabel=tmpLabelMetadata.columns;
					var cssMainLabel=tmpLabelMetadata.nested_css;
					var heightOverflow=tmpLabelMetadata.heightOverflow;
					var maxPageHeight=tmpLabelMetadata.maxPageHeight;
					var widthOverflow=tmpLabelMetadata.autoEnlarge;
					//alert(columnsLabel);
					
					var iLabel=0;
					var currentPageColumn=1;
					//alert(unit_separator);
					//alert(xmlDarwinData);
					//alert(tmpXML);
					var ratioPxCm=getRatioPixelMM(1, $('#div_scale').height() );
					var previousHeight=-1;
					var totalHeight=0;
					$(xmlDarwinData).find(unit_separator).each(function()
					{
						//alert("sep found");
						//var xml2=$(this);
						//	alert($(xml2).text());

						var currentRow=-1;
						var previousRow=1;								
						var currentColumn=-1;
						var tmpCellFullLabel=$("<td></td>");
						var tableHTMLOneLabel=$('<table></table>');
						var rowHTMLInsideLabel= $('<tr></tr>');
						var cellHTMLInsideLabel= $('<td></td>');
						//workaround to remove extra space between lines
						cellHTMLInsideLabel.css("line-height","1pt" );
						var divContainer=$("<div></div>");
						divContainer.css("display", "inline-table");
						cellHTMLInsideLabel.append(divContainer);
						rowHTMLInsideLabel.append(cellHTMLInsideLabel);
						tableHTMLOneLabel.append(rowHTMLInsideLabel);
						
						for (var i = 0; i < arrayXmlDesc.length; i++) 
						{
							var lineHeight="";
							var arrayValues=[];

							//var xml2=$(this);
							//alert(xml2.toString());
							//retrieve metadata at field level
							// use array[i] here
							var tmpField=arrayXmlDesc[i];
							
							var tmp_path=tmpField.path;
							var tmp_rows=tmpField.rows;
							//alert(tmp_rows);
							
							var tmp_columns=tmpField.columns;
							//alert(tmp_columns);
							var tmp_label=tmpField.label;
							
							var cssArrayTmp=tmpField.nested_css;
							var searchReplaceMode = tmpField.mapping_mode; 
							var searchReplaceArrayTmp=tmpField.match_and_replace;
							//alert(searchReplaceArrayTmp.length);
							var tmp_limit=tmpField.limit;
							var tmp_prefix=tmpField.prefix;
							var tmp_suffix=tmpField.suffix;
							var tmp_glue=tmpField.glue;
							
							var tmp_truncate_length=tmpField.truncate_length;
							var tmp_truncate_sign=tmpField.truncate_sign;
							//ftheteen 2017 08 10
                            var tmp_barcode=tmpField.barcode;
                            var tmp_module_size=tmpField.module_size;
                            var tmp_barcode_format=tmpField.barcode_format;
                            var tmp_barcode_height=tmpField.barcode_height;
                            var tmp_barcode_width=tmpField.barcode_width;
                            
							var tmpCell=$("<td></td>");
							var maxField=-1;
							
							
							/*if(typeof tmp_glue != 'undefined')
							{
								alert("no glue");
							}*/
							var maxLimit=-1;
							
							if(typeof tmp_limit != 'undefined')
							{
								if(tmp_limit.length>0)
								{
									maxLimit=tmp_limit;
									//alert(maxLimit);
								}
							}
							
							var maxLength=-1;
							
							if(typeof tmp_limit != 'undefined')
							{
								if(tmp_truncate_length.length>0)
								{
									maxLength=tmp_truncate_length;
									//alert(maxLimit);
								}
							}
							
							//var tmpCell=$("<td></td>");
							var tmpCell=$("<div></div>");
							tmpCell.css("display", "table-cell");
							//tmpCell.css("float", "left")
							var iPath=0;
							var arrayValues=[];
							$(this).find( tmp_path).each(function()
							{
								//alert("found");
								if(maxLimit==-1||iPath<maxLimit)
								{
									//get value and replace if match
									var fieldTmp=$(this).text();
									fieldTmp=match_and_replace_value(fieldTmp, searchReplaceArrayTmp, searchReplaceMode);
									if(fieldTmp!==null)
									{
										if (fieldTmp.trim().length>0)
										{
											
											if(isStringAndSet(tmp_prefix,"") )
											{
												fieldTmp=tmp_prefix.concat(fieldTmp);
											}
											if(isStringAndSet(tmp_suffix,"") )
											{
												fieldTmp=fieldTmp.concat(tmp_suffix);
											}
											arrayValues.push(fieldTmp);
										}
									}
								}
								iPath++;
							});
							var glueDef=isStringAndSet( tmp_glue ," ");
                            if(tmp_barcode===true)
                            {
                               
                                var tmpValue=arrayValues.join(glueDef);
                                if(tmp_barcode_format=="datamatrix")
                                {
                                    
                                    tmpCell.barcode(tmpValue,"datamatrix",{moduleSize: tmp_module_size,showHRI:false, output:"css"});
                                 }
                                 else
                                 {
                                   
                                    tmpCell.barcode("1234567890128", "ean13",{barWidth:0.8, barHeight:1});
                                 }
                              
                            }
                            else
                            {
                                var tmpValue=arrayValues.join(glueDef);
                                //alert(tmpValue);
                                
                                if(isStringAndSet(tmp_label,"") )
                                {
                                    //alert(tmpValue);
                                    if(tmpValue.length>0)
                                    {
                                        tmpValue=tmp_label.concat(tmpValue);
                                    }
                                }
                                
                                if(maxLength>0 && tmpValue.length>maxLength)
                                {
                                    tmpValue=tmpValue.substring(0, maxLength);
                                    tmpValue=tmpValue.concat(tmp_truncate_sign);
                                }
                                tmpCell.text(tmpValue);	
							}
							//alert(tmpValue);
								
							for (var jCSS = 0; jCSS < cssArrayTmp.length; jCSS++) 
							{
									
								var tmpAttribute=cssArrayTmp[jCSS].attribute;
								var tmpCssValue=cssArrayTmp[jCSS].value;
								tmpCell.css(tmpAttribute ,tmpCssValue );
								//tmpCell.css("border-style" ,"solid" );
								
								//workaround to remove extra space between lines
								/*if(tmpAttribute="line-height")
								{
									lineHeight=tmpCssValue;
									
									cellHTMLInsideLabel.css("line-height",lineHeight );
								}*/
							}
							
							
							
							currentRow=tmp_rows;
							
							if(currentRow>previousRow)
							{
								
								
								previousRow=currentRow;
								
								rowHTMLInsideLabel= $('<tr></tr>');
								cellHTMLInsideLabel= $('<td></td>');
								divContainer=$("<div></div>");
								divContainer.css("display", "inline-table");
								//workaround to remove extra space between lines
								cellHTMLInsideLabel.css("line-height","1pt" );
								//if(lineHeight.length>0)
								//{
								//	cellHTMLInsideLabel.css("line-height",lineHeight );
								//}
								cellHTMLInsideLabel.append(divContainer);
								rowHTMLInsideLabel.append(cellHTMLInsideLabel);
								tableHTMLOneLabel.append(rowHTMLInsideLabel);
							}
							
							divContainer.append(tmpCell);
							/*else
							{
								//alert("stay on the same row");
							}*/
						}
						
						
						if(columnsLabel<currentPageColumn)
						{
							//alert("passage à la ligne");
							tableHTMLAllLabels.append(rowHTMLAllLabels);
							rowHTMLAllLabels= $('<tr></tr>');
							currentPageColumn=1;
							currentLabelLine++;
							//page break
							if(currentLabelLine>=rowsLabel)
							{
								//alert("break");
								currentLabelLine=0;
								rowHTMLPageBreak= $('<tr></tr>');
								var cellPageBreak=$("<td></td>");
								cellPageBreak.css("height", "0px");
								var divPageBreak=$("<div></div>");
								rowHTMLPageBreak.css("page-break-after","always");
								cellPageBreak.append(divPageBreak);
								rowHTMLPageBreak.append(cellPageBreak);
								tableHTMLAllLabels.append(rowHTMLPageBreak);
								totalHeight=0;
							}
						}
						/*else
						{
							
							alert("smaller");
						}*/
						var cell2=$("<td></td>");
						var div2=$("<div></div>");
						
						div2.append(tableHTMLOneLabel.clone());
						
						for (var jCSS = 0; jCSS < cssMainLabel.length; jCSS++) 
						{
								
								var tmpAttribute=cssMainLabel[jCSS].attribute;
								var tmpValue=cssMainLabel[jCSS].value;
									//alert(tmpAttribute);
									//alert(tmpValue);
								div2.css(tmpAttribute ,tmpValue );
								//cell2.css("border-style" ,"solid" );
								
						}
						if(heightOverflow=="true"&&tableHTMLOneLabel.hasOverflow())
						{
							//alert("overflow found");
							div2.css("height", "100%");
						}
						//ftheeten 2014 11 17
						if(widthOverflow=="true"&&tableHTMLOneLabel.hasOverflow())
						{
							//alert("overflow found");
							div2.css("width", "100%");
						}
						
						//var previousHeight=parseFloat($('#recordsDarwin').height());
						cell2.append(div2);
						
						rowHTMLAllLabels.append(cell2);
						//alert("added Row");
						var currentHeight=parseFloat($('#recordsDarwin').height());
						//alert(currentHeight);
						var currentHeightCM=currentHeight/ratioPxCm;
						//alert(currentHeightCM);
						if(currentHeight != previousHeight)
						{
							//alert((currentHeight - previousHeight)/ratioPxCm);
							var diffHeight=parseFloat((currentHeight - previousHeight)/ratioPxCm)*10;
							totalHeight=totalHeight + diffHeight;
							//alert(totalHeight);
							if(totalHeight>maxPageHeight && maxPageHeight>=0)
							{
								currentLabelLine=0;
								rowHTMLPageBreak= $('<tr></tr>');
								var cellPageBreak=$("<td></td>");
								cellPageBreak.css("height", "0px");
								var divPageBreak=$("<div></div>");
								rowHTMLPageBreak.css("page-break-after","always");
								cellPageBreak.append(divPageBreak);
								rowHTMLPageBreak.append(cellPageBreak);
								tableHTMLAllLabels.append(rowHTMLPageBreak);
								totalHeight=0;
							}
						}
						iLabel++;
						currentPageColumn++;
						previousHeight=currentHeight;
						
						
					});
					
				}
				//for last page
				if(currentPageColumn>1)
				{
					tableHTMLAllLabels.append(rowHTMLAllLabels);
										
				}
				
				//$('#recordsDarwin').append(tableHTMLAllLabels);
				
				//alert(ratioPxCm);
				var heightCm=parseFloat($('#recordsDarwin').height())/ratioPxCm;
				//alert("height total:");
				//alert($('#recordsDarwin').height());
				//alert(heightCm);
				//alert($('#recordsDarwin').css("height"));
				//alert("add table");
								
			}
		});
		//alert("end reached");
	}
	
	function isInt(n)
	{
		return typeof n== "number" && isFinite(n) && n%1===0;
	}
	
	function isStringAndSet(value, defaultValue)
	{
		var returned=defaultValue;
		if(typeof value != 'undefined')
		{
			if(value.length)
			{
				return value;
				
			}
		}
		return returned;
	}
	
	function preg_quote( str ) 
	{
		// http://kevin.vanzonneveld.net
		// +   original by: booeyOH
		// +   improved by: Ates Goral (http://magnetiq.com)
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   bugfixed by: Onno Marsman
		// *     example 1: preg_quote("$40");
		// *     returns 1: '\$40'
		// *     example 2: preg_quote("*RRRING* Hello?");
		// *     returns 2: '\*RRRING\* Hello\?'
		// *     example 3: preg_quote("\\.+*?[^]$(){}=!<>|:");
		// *     returns 3: '\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:'

		return (str+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
	}
	
	function match_and_replace_value(p_value, p_array_match_and_replace, p_match_and_replace_mode)
	{
		
		var returned= p_value;
		//alert(p_array_match_and_replace.length);
		for(var i=0; i<p_array_match_and_replace.length; i++)
		{
			tmpElem=p_array_match_and_replace[i];
			
			if(
				p_match_and_replace_mode.indexOf("full") > -1 
				&& 
				p_match_and_replace_mode.indexOf("case insensitive") == -1 
				&& 
				returned==tmpElem.old_value )
			{
				//alert("cas 1");
				return tmpElem.new_value;
			}
			else if(
				p_match_and_replace_mode.indexOf("full") > -1 
				&& 
				p_match_and_replace_mode.indexOf("case insensitive") > -1 
			)
			{
				//alert("cas 2");
				var myRegExCI= new RegExp('^'+tmpElem.old_value+'$', 'ig');
				var res = returned.match(myRegExCI);
				if(res)
				{
					return tmpElem.new_value;
				}
			}
			else if(
				p_match_and_replace_mode.indexOf("substring") > -1 
				&& 
				p_match_and_replace_mode.indexOf("case insensitive") == -1 
			)
			{
				//alert("cas 3");
				var myRegExCS= new RegExp(tmpElem.old_value, 'g');
				var res = returned.match(myRegExCS);
				if(res)
				{
					return returned.replace(myRegExCS, tmpElem.new_value);
				}
			}
			else if(
				p_match_and_replace_mode.indexOf("substring") > -1 
				&& 
				p_match_and_replace_mode.indexOf("case insensitive") > -1 
			)
			{
				//alert("cas 4");
				//alert(tmpElem.old_value);
				var myRegExCI= new RegExp(   tmpElem.old_value, 'ig');
				var res = returned.match(myRegExCI);
				if(res)
				{
					//alert("matched");
					return returned.replace(myRegExCI, tmpElem.new_value);
				}
			}
			/*if(returned==tmpElem.old_value)
			{
				return tmpElem.new_value;
			}*/
		}
		return returned;
	}
	
	function getRatioPixelMM(valuemm, valuePix)
	{
		return parseFloat(valuePix)/parseFloat(valuemm);
	}
	


		
	
    </script>

   


 </div>
 </div>
 </div>
 	 <div id="recordsDarwin" name="recordsDarwin" class="print_label_rmca">
	 
 </div>

