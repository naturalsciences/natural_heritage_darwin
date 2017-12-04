/*
 * labelgrid
 *
 * Copyright (c) 2010 labelgrid.net (http://labelgrid.net/GPL-LICENSE.txt)
 * Licensed under the GPL (GPL-LICENSE.txt) licenses.
 *
 * http://www.labelgrid.net
 */

      function loadFilelist(){
    		try
    		{
			var i=1;
			for (i=1;i<11;i++)
			{
			    l1=jQuery.jStore.get('labelfile'+i);
			    if (l1==null || l1=='')
			    {
             	         $('#menu'+i).html('label'+i+' - empty');
			    }
			    else
			    {			
             	         $('#menu'+i).html(l1);
			    }
			}
    		}
	    	catch(err)
    		{
        		alert('Error in loading filelist.');
    		}
      }

      function updateCalibrate(){
    		try
    		{
			    storageready=1;	
                      gLeft=jQuery.jStore.get('labelgrid-calibrate-margin-left');
                      gTop=jQuery.jStore.get('labelgrid-calibrate-margin-top');
			    if (gLeft==null)
			    {
				gLeft=0;
			    }	
			    if (gTop==null)
			    {
				gTop=0;
			    }	

			    $('#calibrate').html('<a href=# id="calibratelink">CalibrateX</a> ('+gLeft+','+gTop+')');
    		}
	    	catch(err)
    		{
        		alert('Error in loading calibration values.');
    		}
	}


	$(function() {
	$("#dialog").dialog("destroy");
		var name = $("#formlabelname"),
			allFields = $([]).add(name),
			tips = $(".validateTips");

		function updateTips(t) {
			tips
				.text(t)
				.addClass('ui-state-highlight');
			setTimeout(function() {
				tips.removeClass('ui-state-highlight', 1500);
			}, 500);
		}


		function checkLength(o,n,min,max) {

			if ( o.val().length > max || o.val().length < min ) {
				o.addClass('ui-state-error');
				updateTips("Length of Name must be between "+min+" and "+max+".");
				return false;
			} else {
				return true;
			}

		}


		$("#print-dialog-form").dialog({
			autoOpen: false,
			height: 200,
			width: 325,
			modal: true,
			buttons: {
				'OK': function() {
					var bValid = true;

					if (bValid) {
						gNumberOfPages=parseFloat($('#formnumpages').val());
						$(this).dialog('close');
						PrintLabel();
					}
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
			}
		});

		$("#calibrate-dialog-form").dialog({
			autoOpen: false,
			height: 180,
			width: 325,
			modal: true,
			buttons: {
				'OK': function() {
					var bValid = true;

					if (bValid) {
						calibrate();
						$(this).dialog('close');
					}
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
			}
		});

		$("#create-dialog-form").dialog({
			autoOpen: false,
			height: 180,
			width: 325,
			modal: true,
			buttons: {
				'OK': function() {
					var bValid = true;

					if (bValid) {
						NewTemplate();
						$(this).dialog('close');
					}
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
			}
		});


		$("#save-dialog-form").dialog({
			autoOpen: false,
			height: 305,
			width: 345,
			modal: true,
			buttons: {
				'OK': function() {
					var bValid = true;
					allFields.removeClass('ui-state-error');
					bValid = bValid && checkLength(name,"formlabelname",3,10);

					if (bValid) {
						SaveLabel($('#labelsavedselection').val(),$('#formlabelname').val());
						$(this).dialog('close');
				            alert("Label saved successfully to browser's local storage.");
					}
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
				allFields.val('').removeClass('ui-state-error');
			}
		});

		$("#open-dialog-form").dialog({
			autoOpen: false,
			height: 180,
			width: 325,
			modal: true,
			buttons: {
				'OK': function() {
					var bValid = true;

					if (bValid) {
						InternalLoadLabel(internallabelid)
						$(this).dialog('close');
					}
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
			}
		});

		

		$("#template-dialog-form").dialog({
			autoOpen: false,
			height: 490,
			width: 455,
			modal: true,
			buttons: {
				'OK': function() {
					var bValid = true;
					//allFields.removeClass('ui-state-error');
					//bValid = bValid && checkLength(name,"formlabelname",3,16);


					var pw=parseFloat($('#formpagewidth').val());
					var ph=parseFloat($('#formpageheight').val());
					var flm=parseFloat($('#formleftmargin').val());
					var ftm=parseFloat($('#formtopmargin').val());
					var fnr=parseFloat($('#formnumrows').val());
					var fnc=parseFloat($('#formnumcols').val());
					var flw=parseFloat($('#formlabelwidth').val());
					var flh=parseFloat($('#formlabelheight').val());
					var fhs=parseFloat($('#formhorispace').val());
					var fvs=parseFloat($('#formvertispace').val());

					if (pw>=0.5 && pw<=9)
					{
					}
					else
					{
						alert("Page Width must be between 0.5 to 9.0 inches.");
						bValid=false;
					}
					if (bValid)
					{
						if (ph>=0.5 && ph<=12)
						{
						}
						else
						{
						alert("Page Height must be between 0.5 to 12.0 inches.");
						bValid=false;
						}
					}
					if (bValid)
					{
						if (flm>=0 && flm<=9)
						{
						}
						else
						{
						alert("Form Left Margin must be between 0 to 9 inches.");
						bValid=false;
						}
					}
					if (bValid)
					{
						if (ftm>=0 && ftm<=12.0)
						{
						}
						else
						{
						alert("Form Top Margin must be between 0 to 12.0 inches.");
						bValid=false;
						}
					}
					if (bValid)
					{
						if (fnr>=1 && fnr<=20)
						{
						}
						else
						{
						alert("Number of Rows must be between 1 to 20.");
						bValid=false;
						}
					}
					if (bValid)
					{
						if (fnc>=1 && fnc<=10)
						{
						}
						else
						{
						alert("Number of Columns must be between 1 to 20.");
						bValid=false;
						}
					}
					if (bValid)
					{
						if (flw>=0.5 && flw<=9.0)
						{
						}
						else
						{
						alert("Label Width must be between 0.5 to 9.0.");
						bValid=false;
						}
					}
					if (bValid)
					{
						if (flh>=0.5 && flh<=12.0)
						{
						}
						else
						{
						alert("Label Height must be between 0.5 to 12.0.");
						bValid=false;
						}
					}
					if (bValid)
					{
						if (fhs>=0 && fhs<=9.0)
						{
						}
						else
						{
						alert("Horizontal Space must be between 0 to 9.0.");
						bValid=false;
						}
					}
					if (bValid)
					{
						if (fvs>=0 && fvs<=12.0)
						{
						}
						else
						{
						alert("Vertical Space must be between 0 to 12.0.");
						bValid=false;
						}
					}

					if (bValid) {

			    			$('.paper').remove();
						$('.paperpreview').remove();
						$('.label').remove();
						$('#papersketch').remove();
						$('.labelcontainer').remove();
						gTemplate[1]=pw;
						gTemplate[2]=ph;
						gTemplate[3]=flm;
						gTemplate[4]=ftm;
						gTemplate[7]=fnr;
						gTemplate[8]=fnc;
						gTemplate[5]=flw;
						gTemplate[6]=flh;
						gTemplate[9]=fhs;
						gTemplate[10]=fvs;
						CreateTemplateView(gTemplate[1],
									 gTemplate[2],
									 gTemplate[3],
									 gTemplate[4],
									 gTemplate[5],
									 gTemplate[6],
									 gTemplate[7],
									 gTemplate[8],
									 gTemplate[9],
									 gTemplate[10]);

						$(this).dialog('close');
					}
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
				//allFields.val('').removeClass('ui-state-error');
			}
		});
	
		$("#dialog-form").dialog({
			autoOpen: false,
			height: 370,
			width: 430,
			modal: true,
			buttons: {
				'OK': function() {
					$(gSelectedObjectID).html(arr['textEditor'].get_content());
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
			}
		});

	});

	function SaveLabel(labelid,labelname) 
	{
			    try{
  
				    jQuery.jStore.set('xlabel'+labelid+'-template', JSON.stringify(gTemplates.Templates[gLabelIndex]));
				    var textobj=new Array();
				    var objcounter=0;
				    $('.label-text').each(function(index){

					 var attrcounter=0;
					 textobj[objcounter]=new Array();
					 textobj[objcounter][attrcounter]=$(this).attr('id');
					 attrcounter++;
					 textobj[objcounter][attrcounter]=$(this).html();
					 attrcounter++;
					 textobj[objcounter][attrcounter]=$(this).css('left');
					 attrcounter++;
					 textobj[objcounter][attrcounter]=$(this).css('top');
					 attrcounter++;
					 objcounter++;
				    });
				    jQuery.jStore.set('xlabel'+labelid+'-objs', JSON.stringify(textobj));
				    $('#menu'+labelid).html('label'+labelid+' - ' + labelname + ' - <a id="xlabel'+labelid+'" class="load" href=#>open</a>');
				    jQuery.jStore.set('labelfile'+labelid, 'label'+labelid+' - ' + labelname + ' - <a id="xlabel'+labelid+'" class="load" href=#>open</a>');

			    }
	    		    catch(err)
    			    {
					alert("Error in saving label.");
			    }
	}

	$(function() {
		$('.clickieA4').live('click', function() {
			browserselected='IE';
			var pr1 = window.open( "print.html", "Calibrate");

		 return false;
		});
	});

	$(function() {
		$('.clickieLetter').live('click', function() {
			browserselected='IE';
			var pr1 = window.open( "printLetter.html", "Calibrate");
		 return false;
		});
	});

	$(function() {
		$('.clickdone').live('click', function() {
			    NewTemplate();
			    return false;
		});
	});


	$(function() {
		$('.clickcancel').live('click', function() {
			    NewTemplate();
			    return false;
		});
	});

	$(function() {
		$('.clicksave').live('click', function() {
			    try{
				    jQuery.jStore.set('labelgrid-calibrate-margin-top', $('#formverti').val());  
				    jQuery.jStore.set('labelgrid-calibrate-margin-left',$('#formhori').val());  

				    updateCalibrate();
				    alert("Calibration values saved to browser's local storage.");
			    }
	    		    catch(err)
    			    {
					gLeft=$('#formhori').val();
					gTop=$('#formverti').val();
					$('#calibrate').html('<a href=# id="calibratelink">CalibrateX</a> ('+gLeft+','+gTop+')');
        				alert("Error in saving Calibration values to browser's local storage. However the values have been saved to the memory and will be taken into account during printing.");
		    	    }
			    return false;
		});
	});


	function InitTemplate() {
		gTemplate[0]=gTemplates.Templates[gLabelIndex][0];
		gTemplate[1]=gTemplates.Templates[gLabelIndex][1];
		gTemplate[2]=gTemplates.Templates[gLabelIndex][2];
		gTemplate[3]=gTemplates.Templates[gLabelIndex][3];
		gTemplate[4]=gTemplates.Templates[gLabelIndex][4];
		gTemplate[5]=gTemplates.Templates[gLabelIndex][5];
		gTemplate[6]=gTemplates.Templates[gLabelIndex][6];
		gTemplate[7]=gTemplates.Templates[gLabelIndex][7];
		gTemplate[8]=gTemplates.Templates[gLabelIndex][8];
		gTemplate[9]=gTemplates.Templates[gLabelIndex][9];
		gTemplate[10]=gTemplates.Templates[gLabelIndex][10];
	}

	function NewTemplate()
	{
			alert("test");
			calibratenow=0;
			labelactive=0;
			templatemode=1;
			GridApp();
			EnableTemplateSelect();
	    		$('.paper').remove();
			$('.paperpreview').remove();
			$('.label').remove();
			$('#papersketch').remove();
			$('.labelcontainer').remove();

			LoadTemplates();
			CreateTemplateView(8.268,11.693,0.283,0.598,2.5,1.5,7,3,0.1,0.0);
			gLabelIndex=3;
	}

	$(function() {
		$("#template").click(function() {
			if (labelactive==1)
			{
				$('#create-dialog-form').addClass('dialogclass');
     				$('#create-dialog-form').dialog('open');
			}
			else
			{	
				NewTemplate();
			}

			return false;
		});		
	});

	$(function() {
		$("#create").click(function() {
			if (calibratenow==1)
			{
				alert("This option is not available during calibration.");
			}
			else if (templatemode==1)
			{
				alert("This option is not available during template creation.");
			}
			else
			{
				CreateTextObject();
			}
			return false;
		});		
	});



	$(function() {
		$("#delete").click(function() {
			if (calibratenow==1)
			{
				alert("This option is not available during calibration.");
			}
			else if (templatemode==1)
			{
				alert("This option is not available during template creation.");
			}
			else
			{
				DeleteSelectedObject();
			}
			return false;
		});		
	});

	$(function() {
		$("#save").click(function() {
			if (calibratenow==1)
			{
				alert("This option is not available during calibration.");
			}
			else if (templatemode==1)
			{
				alert("This option is not available during template creation.");
			}
			else
			{
						$('#save-dialog-form').addClass('dialogclass');
                      			$('#save-dialog-form').dialog('open');
			}
			return false;
		});		
	});

	$(function() {
		$("#custom").click(function() {
						$('#template-dialog-form').addClass('dialogclass');
						$('#formpagewidth').val(gTemplate[1]);
						$('#formpageheight').val(gTemplate[2]);
						$('#formleftmargin').val(gTemplate[3]);
						$('#formtopmargin').val(gTemplate[4]);
						$('#formnumrows').val(gTemplate[7]);
						$('#formnumcols').val(gTemplate[8]);
						$('#formlabelwidth').val(gTemplate[5]);
						$('#formlabelheight').val(gTemplate[6]);
						$('#formhorispace').val(gTemplate[9]);
						$('#formvertispace').val(gTemplate[10]);
                      			$('#template-dialog-form').dialog('open');
			return false;
		});		
	});

	function InternalLoadLabel(labelid)
	{
				    var lbt=jQuery.jStore.get(labelid+'-template');  
				    var lblobj=jQuery.jStore.get(labelid+'-objs');
				    gSelectedObjectID ='';
  	    			    try{
						gTemplate=eval('('+lbt+')');
						lblobj=eval('('+lblobj+')');
			          }
   			          catch (err)
				    {
						gTemplate=lbt;
				    }	
			  	    $('.paper').remove();
			  	    $('.paperpreview').remove();
			  	    $('.label').remove();
				    labelactive=0;
				    CreateLabel(gTemplate[5],gTemplate[6]);

				    var i=0;
				    	
				    for (i=0;i<lblobj.length;i++)
				    {
					$('.label').append('<div class="label-text ui-widget-content" id="' + lblobj[i][0] +'">' + lblobj[i][1] + '</div>');
					objStr='#'+lblobj[i][0];
					$(objStr).css('left',lblobj[i][2]);
					$(objStr).css('top',lblobj[i][3]);
					$(objStr).css('border','0px solid black').css('border-style','dotted');
					$(objStr).draggable({ containment: '.label' });
					$(objStr).hover(
					  function () {
						if (gSelectedObjectID=='#'+$(this).attr('id'))
						{
							$(this).css('cursor','move');
						}
						else
						{
							$('#'+$(this).attr('id')).css('border','1px solid black');
							$('#'+$(this).attr('id')).css('border-style','dotted');
							$(this).css('cursor','move');
						}
					  }, 
		  			  function () {
							if (gSelectedObjectID=='#'+$(this).attr('id'))
							{
								$(this).css('cursor','pointer');
							}
							else
							{
								$('#'+$(this).attr('id')).css('border','0px solid black');
								$(this).css('cursor','pointer');
							}
					  }
				      );

                      		$(objStr).click(function() {
                      			//alert('text click');
                      			if (gSelectedObjectID=='')
                      			{
                      					gSelectedObjectID='#'+$(this).attr('id');
                      					$(gSelectedObjectID).css('border','2px solid black');
                      					$(gSelectedObjectID).css('border-style','dotted');
                      			}
                      			else
                      			{
                      				if (gSelectedObjectID=='#'+$(this).attr('id'))
                      				{
                      				}
                      				else
                      				{
                      					$(gSelectedObjectID).css('border','0px solid black');
                      					gSelectedObjectID='#'+$(this).attr('id');
                      					$(gSelectedObjectID).css('border','2px solid black');
                      					$(gSelectedObjectID).css('border-style','dotted');
                      				}
                      			}
                      			gTextClick=1;
                      		});
                      
                      		$(objStr).dblclick(function() {
                      			gSelectedObjectID='#'+$(this).attr('id');
                      
                      			$('#dialog-form').addClass('dialogclass');
                      			$('#dialog-form').dialog('open');
                      			$('#textEditorFrm').html('<form><textarea name="form[info1]" cols="52" rows="2"\
                      							  id="textEditor" method="post" action="#">'
                      							 +$(gSelectedObjectID).html()
                      							 +'</textarea></form>');
                      			$('#textEditor').rte({
                                          css: ['default.css'],
                                          width:405,
                                          height:200,
                                          controls_rte: rte_toolbar,
                                          controls_html: html_toolbar
                      			},arr);
                      			return false;
                      		});		

				    }	
	}
 
	$(function() {
		$(".load").live('click', function() {

			if (labelactive==1)
			{
				internallabelid=$(this).attr('id');
				$('#open-dialog-form').addClass('dialogclass');
     				$('#open-dialog-form').dialog('open');
			}
			else
			{

				    var labelid=$(this).attr('id');
				//alert('labelid '+labelid);
				    var lbt=jQuery.jStore.get(labelid+'-template');  
				    var lblobj=jQuery.jStore.get(labelid+'-objs');
				    gSelectedObjectID ='';
				    //Chrome/Safari ab, Mozilla/IE bb
  	    			    try{
						//alert('a-'+eval('('+lbt+')')[0]);
						gTemplate=eval('('+lbt+')');
						//alert('a-'+eval('('+lblobj+')')[0]);
						lblobj=eval('('+lblobj+')');
			          }
   			          catch (err)
				    {
						gTemplate=lbt;
				    }	
			  	    $('.paper').remove();
			  	    $('.paperpreview').remove();
			  	    $('.label').remove();
				    labelactive=0;
				    CreateLabel(gTemplate[5],gTemplate[6]);

				    var i=0;
				    	
				    for (i=0;i<lblobj.length;i++)
				    {
					$('.label').append('<div class="label-text ui-widget-content" id="' + lblobj[i][0] +'">' + lblobj[i][1] + '</div>');
					objStr='#'+lblobj[i][0];
					$(objStr).css('left',lblobj[i][2]);
					$(objStr).css('top',lblobj[i][3]);
					$(objStr).css('border','0px solid black').css('border-style','dotted');
					$(objStr).draggable({ containment: '.label' });
					$(objStr).hover(
					  function () {
						if (gSelectedObjectID=='#'+$(this).attr('id'))
						{
							$(this).css('cursor','move');
						}
						else
						{
							$('#'+$(this).attr('id')).css('border','1px solid black');
							$('#'+$(this).attr('id')).css('border-style','dotted');
							$(this).css('cursor','move');
						}
					  }, 
		  			  function () {
							if (gSelectedObjectID=='#'+$(this).attr('id'))
							{
								$(this).css('cursor','pointer');
							}
							else
							{
								$('#'+$(this).attr('id')).css('border','0px solid black');
								$(this).css('cursor','pointer');
							}
					  }
				      );

                      		$(objStr).click(function() {
                      			if (gSelectedObjectID=='')
                      			{
                      					gSelectedObjectID='#'+$(this).attr('id');
                      					$(gSelectedObjectID).css('border','2px solid black');
                      					$(gSelectedObjectID).css('border-style','dotted');
                      			}
                      			else
                      			{
                      				if (gSelectedObjectID=='#'+$(this).attr('id'))
                      				{
                      				}
                      				else
                      				{
                      					$(gSelectedObjectID).css('border','0px solid black');
                      					gSelectedObjectID='#'+$(this).attr('id');
                      					$(gSelectedObjectID).css('border','2px solid black');
                      					$(gSelectedObjectID).css('border-style','dotted');
                      				}
                      			}
                      			gTextClick=1;
                      		});
                      
                      		$(objStr).dblclick(function() {
                      			gSelectedObjectID='#'+$(this).attr('id');
                      
                      			$('#dialog-form').addClass('dialogclass');
                      			$('#dialog-form').dialog('open');
                      			$('#textEditorFrm').html('<form><textarea name="form[info1]" cols="52" rows="2"\
                      							  id="textEditor" method="post" action="#">'
                      							 +$(gSelectedObjectID).html()
                      							 +'</textarea></form>');
                      			$('#textEditor').rte({
                                          css: ['default.css'],
                                          width:405,
                                          height:200,
                                          controls_rte: rte_toolbar,
                                          controls_html: html_toolbar
                      			},arr);
                      			return false;
                      		});		

				    }	
			}

			return false;
		});		
	});

	function calibrate()
	{
			labelactive=0;
			calibratenow=1;
			templatemode=0;
			DisableTemplateSelect();
			$('.paper').remove();
			$('.label').remove();			
			$('#papersketch').remove();
			$('.labelcontainer').remove();
			
			$('<div id="papersketch"></div>').appendTo('#container');
			//$('#papersketch').css('height','4in');
			$('#papersketch').css('background','white');
			$('#papersketch').css('height','auto');
			$('#papersketch').css('width','auto');


			var myhtml = '\
<b>Why do I need to do this?</b><br />\
<br /><ul style="list-style-type:none"><li>Different types of printers have different minimum margins for printing. In a local application, the system settings like margins can be easily setup and configured. For a web application, these settings proved to be difficult to configure. On top of that, different browsers handle margins differently. Some browsers provide a Page Setup page for setting the margins while some simply assume a minimum margin internally. labelgrid is designed to overcome all the problems associated with margins via a unique Calibration. The instructions below show how to setup and calibrate the browser with your printer. You will only need to do this once for the specific browser/printer pair. The calibration values will be saved into your browser local storage and automatically loaded the next time you access labelgrid.<br /><br /> Click on <a href=# class="clickcancel">Cancel</a> if you do not want to continue to calibrate.</li></ul>\
<b>Browser and Printer Calibration</b><br /><br />\
<ol>\
<li>Goto Internet Explorer->Printer->Page Setup or Mozilla->File->Page Setup or Opera->File->Print Options</li>\
<li>In the Margin settings, set the following margins to zero.<br /><br />Left:0, Right:0, Top:0, Bottom:0<br /><br />Sometimes Internet Explorer will not allow you to input a zero margin, if this is the case, use the minimum margin provided by the browser. For Mozilla, the margin settings is in the "Margins & Header/Footer" tab.<br /><br /></li>\
<li>If you are using Internet Explorer, set the Headers and Footers (if any) to be "Empty" and unchecked the "Enable Shrink-to-Fit" option. <br /><br />If you are using Mozilla, set the Headers and Footers (if any) to be "blank" and unchecked the "Shrink To Fit Page Width" option.<br /><br />If you are using Opera, unchecked the "Print headers and footers" option, unchecked the "Fit to paper width" option and set "Scale print to" option to "100%".</li>\
<li>Click on <a href=# class="clickieA4">Print A4</a> to calibrate using an A4 sized paper or click on <a href=# class="clickieLetter">Print Letter</a> to calibrate using a Letter sized paper.</li>\
<li>Fold the printed page in half both horizontally and vertically.</li>\
<li>Unfold the page and compare the printed lines on the page with the folded lines.</li>\
<li>Adjust the vertical/horizontal margins below.<br /><br />\
<form name=adjustie>\
<table style="border-collapse: collapse;border: 0px solid black; padding:1em">\
<tr><td>Horizontal Adjustments(-100 to 100)</td><td width=200>Vertical Adjustments(-100 to 100)</td></tr>\
<tr><td>&nbsp;<input id="formhori" type=text size=8 name=Hori value="0" STYLE="margin: 0px; padding: 0px;"> px </td><td>&nbsp;<input id="formverti" type=text size=12 name=Verti value="0" STYLE="margin: 0px; padding: 0px;" >  px </td></tr>\
</table>\
</form>\
</li>\
<li>Repeat step 4 - 7 until the folded lines match the printed lines.</li>\
<li><a href=# class="clicksave">Save</a> the calibrated values.</li>\
<li>Click <a href=# class="clickdone">Done</a> when you have completed the calibration.</li>\
</ol>\
';
			$('#papersketch').html(myhtml);

	}

	$(function() {
		$("#calibratelink").live('click',function() {
			//window.open("print.html");
			if (labelactive==1)
			{
				$('#calibrate-dialog-form').addClass('dialogclass');
     				$('#calibrate-dialog-form').dialog('open');
			}
			else
			{	
				calibrate();
			}
			return false;
		});		
	});


	$(function() {
		$("#createlabel").click(function() {
			//gTemplate=gTemplates.Templates[gLabelIndex];
			CreateLabel(gTemplate[5],gTemplate[6]);
			return false;
		});		
	});

	$(function() {
		$("#print").click(function() {
      //$('#print-dialog-form').addClass('dialogclass');
      //$('#print-dialog-form').dialog('open');
			gNumberOfPages=1;
			PrintLabel();
			return false;
		});		
	});



	$(function() {
		$("select#lbltem").change(
					function(){

			    			$('.paper').remove();
						$('.paperpreview').remove();
						$('.label').remove();
						$('#papersketch').remove();
						$('.labelcontainer').remove();

						CreateTemplateView(gTemplates.Templates[$(this).val()][1],
									 gTemplates.Templates[$(this).val()][2],
									 gTemplates.Templates[$(this).val()][3],
									 gTemplates.Templates[$(this).val()][4],
									 gTemplates.Templates[$(this).val()][5],
									 gTemplates.Templates[$(this).val()][6],
									 gTemplates.Templates[$(this).val()][7],
									 gTemplates.Templates[$(this).val()][8],
									 gTemplates.Templates[$(this).val()][9],
									 gTemplates.Templates[$(this).val()][10]);

						gLabelIndex=$(this).val();
						//gTemplate=gTemplates.Templates[gLabelIndex];	
						InitTemplate();
					});
	});
  
	function DeleteSelectedObject(){
		if (gSelectedObjectID=='')
		{
		}
		else
		{
			$(gSelectedObjectID).remove();
		}
	}

	function LoadTemplates() {

		gLabelIndex=2;

	};

	
      function ConvertPxToInt(pxVal) {
            var validch = "0123456789.";
            var retVal = 0;
            for (i = 0; i < pxVal.length; i++) {
                if (validch.indexOf(pxVal.charAt(i)) == -1) {
                    if (i > 0) {
                        retVal = parseInt(pxVal.substring(0, i));
                        return retVal;
                    }
                }
            }
            return retValue;
      }

	function ConvertInchToInt(pxVal) {
            var validch = "0123456789.";
            var retVal = 0;
            for (i = 0; i < pxVal.length; i++) {
                if (validch.indexOf(pxVal.charAt(i)) == -1) {
                    if (i > 0) {
                        retVal = parseFloat(pxVal.substring(0, i));
                        return retVal;
                    }
                }
            }
            return retValue;
      }


	function CreateTemplateView(paperWidth,paperHeight,marginLeft,marginTop,labelWidth,labelHeight,numRows,numColumns,horizontalSpace,verticalSpace)
	{	
		var sRatio=1.35;
		var screenwidth=8.268/sRatio,screenheight=11.693/sRatio;	
		var widthratio=screenwidth/paperWidth;
		var heightratio=screenheight/paperHeight;
		paperWidth=widthratio*paperWidth;
		paperHeight=heightratio*paperHeight;
		marginLeft=widthratio*marginLeft;
		marginTop=heightratio*marginTop;
		labelWidth=widthratio*labelWidth;
		labelHeight=heightratio*labelHeight;
		horizontalSpace=widthratio*horizontalSpace;
		verticalSpace=heightratio*verticalSpace;

		CreateTemplateInches(paperWidth,paperHeight,marginLeft,marginTop,labelWidth,labelHeight,numRows,numColumns,horizontalSpace,verticalSpace)

	}

	function CreateTemplateInches(paperWidth,paperHeight,marginLeft,marginTop,labelWidth,labelHeight,numRows,numColumns,horizontalSpace,verticalSpace)
	{

			var paper=$('<div class="paper"></div>');
			paper.css('width',paperWidth+'in');
			paper.css('height',paperHeight+'in');
			paper.appendTo('#container');

			var i=0,j=0;
			var posix=0,posiy=0;
			var numrows=numRows, numcolumns=numColumns;
			for (i=0;i<numrows;i++)
			{
				posix=0;
				posiy=i*(labelHeight+verticalSpace);

				for (j=0;j<numcolumns;j++)
				{
					posix=j*(labelWidth+horizontalSpace);
					var label=$('<div class="label"></div>');
					label.css('margin-left',marginLeft+'in')
					     .css('margin-top',marginTop+'in')
					     .css('width',labelWidth+'in')
					     .css('height',labelHeight+'in')
					     .css('left',posix+'in')
					     .css('top',posiy+'in');
					paper.append(label);
				}
			}

	};

	function CreateTextObject()
	{
		$('.label').append('<div class="label-text ui-widget-content" id="' + 'lbltextobj' + gObjectID +'">Text</div>');
		objStr="#lbltextobj"+gObjectID;
		$(objStr).css('border','1px solid black').css('border-style','dotted');
		$(objStr).draggable({ containment: '.label' });
		$(objStr).hover(
		  function () {
			if (gSelectedObjectID=='#'+$(this).attr('id'))
			{
				$(this).css('cursor','move');
			}
			else
			{
				$('#'+$(this).attr('id')).css('border','1px solid black');
				$('#'+$(this).attr('id')).css('border-style','dotted');
				$(this).css('cursor','move');
			}
		  }, 
		  function () {
			if (gSelectedObjectID=='#'+$(this).attr('id'))
			{
				$(this).css('cursor','pointer');
			}
			else
			{
				$('#'+$(this).attr('id')).css('border','0px solid black');
				$(this).css('cursor','pointer');
			}
		  }
		);

		$(objStr).click(function() {
			//alert('text click');
			if (gSelectedObjectID=='')
			{
					gSelectedObjectID='#'+$(this).attr('id');
					$(gSelectedObjectID).css('border','2px solid black');
					$(gSelectedObjectID).css('border-style','dotted');
			}
			else
			{
				if (gSelectedObjectID=='#'+$(this).attr('id'))
				{
				}
				else
				{
					$(gSelectedObjectID).css('border','0px solid black');
					gSelectedObjectID='#'+$(this).attr('id');
					$(gSelectedObjectID).css('border','2px solid black');
					$(gSelectedObjectID).css('border-style','dotted');
				}
			}
			gTextClick=1;
		});

		$(objStr).dblclick(function() {
			gSelectedObjectID='#'+$(this).attr('id');

			$('#dialog-form').addClass('dialogclass');
			$('#dialog-form').dialog('open');
			$('#textEditorFrm').html('<form><textarea name="form[info1]" cols="52" rows="2"\
							  id="textEditor" method="post" action="#">'
							 +$(gSelectedObjectID).html()
							 +'</textarea></form>');
			$('#textEditor').rte({
                    css: ['default.css'],
                    width:405,
                    height:200,
                    controls_rte: rte_toolbar,
                    controls_html: html_toolbar
			},arr);
			return false;
		});		
		
		gObjectsList[gObjectsListCounter]=gObjectID+'';
		gObjectsListCounter++;
		gObjectID++;

	}

	function FullApp()
	{
		$('#leftbar').hide();
		$('#mainapp').removeClass('span-15');
		$('#mainapp').addClass('span-22');
	}

	function GridApp()
	{
		$('#leftbar').show();
		$('#mainapp').removeClass('span-22');
		$('#mainapp').addClass('span-15');
	}

	function DisableTemplateSelect()
	{
		$('#lbltem').attr('disabled','disabled');
		$('#custom').attr('disabled','disabled');
	}

	function EnableTemplateSelect()
	{
		$('#lbltem').attr('disabled','');
		$('#custom').attr('disabled','');
	}

	function CreateLabel(labelWidth,labelHeight)
	{
		if (labelactive==1)
		{
			alert('An existing label already exist.');
			return false;
		}
		//$('#createlabel').toggle();
		if (calibratenow==1)
		{
			alert('This option is not available during calibration.');
			return false;
		}
		
		
		calibratenow=0;
		labelactive=1;
		templatemode=0;

		if (labelWidth>6)
		{
			FullApp();
		}
		DisableTemplateSelect();

		//Store all the template values
		var paper=$('.paper');
		var objStr;
		var labelleft=0,labeltop=0;

		$('.paper').remove();
		$('#papersketch').remove();
		$('.labelcontainer').remove();
		$('<div id="papersketch"></div>').appendTo('#container');
		$('#papersketch').css('height',labelHeight+2+'in');
		$('<center><div class="labelcontainer"></div></center>').appendTo('#papersketch');
		$('.labelcontainer').css('width',labelWidth+'in');
		$('.labelcontainer').css('height',labelHeight+2+'in');

		$('<center><div class="label"></div></center>').appendTo('.labelcontainer');


		$('.label').css('width',labelWidth+'in')
			     .css('height',labelHeight+'in')
			     .css('border','1px solid black')
			     .css('border-style','dotted');		
		labeltop=ConvertInchToInt($('#papersketch').css('height'));
		labeltop=(labeltop-labelHeight)/2;

		$('.label').css('top',labeltop+'in')


		$(".label").click(function() {
			if (gTextClick==1)
			{
				gTextClick=0;
			}
			else
			{
				$(gSelectedObjectID).css('border','0px solid black');
				gSelectedObjectID='';				
				//alert('clcik');
			}
		});		

	return false;

	}

	function CMtoInches(cmvalue)
	{
		return cmvalue/2.54;
	};

	function InchestoCM(inchesvalue)
	{
		return inchesvalue*2.54;
	};


/* INSERT AND MODIFY BY SON */

var parseXml;

if (typeof window.DOMParser != "undefined") {
    parseXml = function(xmlStr) {
        return ( new window.DOMParser() ).parseFromString(xmlStr, "text/xml");
    };
} else if (typeof window.ActiveXObject != "undefined" &&
       new window.ActiveXObject("Microsoft.XMLDOM")) {
    parseXml = function(xmlStr) {
        var xmlDoc = new window.ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async = "false";
        xmlDoc.loadXML(xmlStr);
        return xmlDoc;
    };
} else {
    throw new Error("No XML parser found");
}

function xml_to_string(xml_node)
    {
        if (xml_node.xml)
            return xml_node.xml;
        else if (XMLSerializer)
        {
            var xml_serializer = new XMLSerializer();
            return xml_serializer.serializeToString(xml_node);
        }
        else
        {
            alert("ERROR: Extremely old browser");
            return "";
        }
    }



function PrintLabel() {

	    // modified  by Son 24/04/2014
	
		 var paperWidth=gTemplate[1];
		 var paperHeight=gTemplate[2];
		 var marginLeft=gTemplate[3];
		 var marginTop=gTemplate[4];

		 var labelWidth=gTemplate[5];
		 var labelHeight=gTemplate[6];
		 var numRows=gTemplate[7];

		 var numColumns=gTemplate[8];
		 var horizontalSpace=gTemplate[9];
		 var verticalSpace=gTemplate[10];

    
		 var pr1 = window.open( "", "PrintLabel");
	       var myhtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><link rel="stylesheet" href="/css/resetgrid.css" type="text/css"><link rel="stylesheet" href="/css/labelgrid.css" type="text/css"></head><body></body></html>';
 
	   pr1.document.open();
	   pr1.document.write(myhtml);
		 pr1.document.close();
 
     var labelbody = $('body',pr1.document);
     var info_xml_doc=parseXml(document.getElementById("print_info").innerHTML);
     var labels_to_print = info_xml_doc.getElementsByTagName("label");
     var print_info= new Array();
     for (var k=0;k<labels_to_print.length;k++)
      { print_info[k]=new Array();
        print_info[k][0]=labels_to_print[k].getElementsByTagName("genus")[0].childNodes[0].nodeValue;
        print_info[k][1]=labels_to_print[k].getElementsByTagName("species")[0].childNodes[0].nodeValue;
    }
 
		 var i=0,j=0,ipages=1;
		 var posix=0,posiy=0;
		 var current_spec_index=0;;
		 var numrows=numRows, numcolumns=numColumns;
     var numspecs=labels_to_print.length*gNumberOfPages;
     var maxpagespecs=numrows*numcolumns;
     var numpages=Math.floor(numspecs/maxpagespecs)+1;
     
     //alert(numrows+" - "+numcolumns+" - "+numpages);
     
		 //for (ipages=0;ipages<gNumberOfPages;ipages++)
		 for (ipages=0;ipages<numpages;ipages++)
		 {
		 var labelwrapperwidth=0,labelwrapperheight=0;
		 labelwrapperwidth=(numcolumns-1)*(labelWidth+horizontalSpace) + labelWidth;
		 labelwrapperheight=(numrows-1)*(labelHeight+verticalSpace) + labelHeight;
 
	   var labelwrapper=$('<div class="labelwrapper" id="lw'+ipages+'"></div>',pr1.document);
		 labelwrapper.css('margin-left',marginLeft+'in')
		             .css('margin-top',marginTop+'in')
		             .css('width',labelwrapperwidth+'in')
		             .css('height',labelwrapperheight+'in');
		 labelbody.append(labelwrapper);


		 for (i=0;i<numrows;i++)
		 {
			posix=0;
			posiy=i*(labelHeight+verticalSpace);
			for (j=0;j<numcolumns;j++)
			{
			  current_spec_index=(ipages*maxpagespecs)+(i*numcolumns)+j;
			  if (current_spec_index<numspecs){
				  posix=j*(labelWidth+horizontalSpace);
			    var label=$('<div class="label"></div>',pr1.document);
				  label.css('width',labelWidth+'in')
				     .css('height',labelHeight+'in')
				     .css('left',posix+'in')
				     .css('top',posiy+'in');
				  var textobj=$('<div class="label-text ui-widget-content" style="padding:5px"></div>',pr1.document);
				  
				  // insert our specimen info
					var current_label_info=print_info[current_spec_index];
          var to_print="<b>"+current_label_info[0]+"</b><br>"+current_label_info[1];
		  
				  textobj.html(to_print);
          textobj.appendTo(label);
			    
			    
			    $('#lw'+ipages,pr1.document).append(label);
			    }
			}
		 }

		 labelbody.append('<p style="page-break-before: always;height:1px"></p>');

	       }

		pr1.print();


  	};