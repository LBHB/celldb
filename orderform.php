<?php
/*** CELLDB
orderform.php - generate printable order form. hacked out of excel spreadsheet

created 2008-06-03 - SVD
***/

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
include_once "./celldb.php";
?>

<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content="Microsoft Excel 11">
<link rel=File-List href="ISR%20Purchase%20Order_files/filelist.xml">
<link rel=Edit-Time-Data href="ISR%20Purchase%20Order_files/editdata.mso">
<link rel=OLE-Object-Data href="ISR%20Purchase%20Order_files/oledata.mso">
<!--[if gte mso 9]><xml>
 <o:DocumentProperties>
  <o:Author>palmaira</o:Author>
  <o:LastAuthor>Stephen V. David</o:LastAuthor>
  <o:LastPrinted>2003-06-09T20:42:56Z</o:LastPrinted>
  <o:Created>2002-09-09T15:05:46Z</o:Created>
  <o:LastSaved>2008-06-12T14:42:41Z</o:LastSaved>
  <o:Company>ISR</o:Company>
  <o:Version>11.5606</o:Version>
 </o:DocumentProperties>
 <o:CustomDocumentProperties>
  <o:_AdHocReviewCycleID dt:dt="float">2044023208</o:_AdHocReviewCycleID>
  <o:_EmailSubject dt:dt="string">ISR purchase order form</o:_EmailSubject>
  <o:_AuthorEmail dt:dt="string">hennlec@isr.umd.edu</o:_AuthorEmail>
  <o:_AuthorEmailDisplayName dt:dt="string">Laurent Henn-Lecordier</o:_AuthorEmailDisplayName>
  <o:_ReviewingToolsShownOnce dt:dt="string"></o:_ReviewingToolsShownOnce>
 </o:CustomDocumentProperties>
</xml><![endif]-->
<style>
<!--table
	{mso-displayed-decimal-separator:"\.";
	mso-displayed-thousand-separator:"\,";}
@page
	{margin:.56in .75in .48in .75in;
	mso-header-margin:.5in;
	mso-footer-margin:.5in;}
tr
	{mso-height-source:auto;}
col
	{mso-width-source:auto;}
br
	{mso-data-placement:same-cell;}
.style18
	{mso-number-format:"_\(\0022$\0022* \#\,\#\#0\.00_\)\;_\(\0022$\0022* \\\(\#\,\#\#0\.00\\\)\;_\(\0022$\0022* \0022-\0022??_\)\;_\(\@_\)";
	mso-style-name:Currency;
	mso-style-id:4;}
.style0
	{mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	white-space:nowrap;
	mso-rotate:0;
	mso-background-source:auto;
	mso-pattern:auto;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial;
	mso-generic-font-family:auto;
	mso-font-charset:0;
	border:none;
	mso-protection:locked visible;
	mso-style-name:Normal;
	mso-style-id:0;}
td
	{mso-style-parent:style0;
	padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial;
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:locked visible;
	white-space:nowrap;
	mso-rotate:0;}
.xl24
	{mso-style-parent:style0;
	text-align:center;}
.xl25
	{mso-style-parent:style0;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl26
	{mso-style-parent:style0;
	text-align:center;
	border:.5pt solid windowtext;}
.xl27
	{mso-style-parent:style0;
	border:.5pt solid windowtext;}
.xl28
	{mso-style-parent:style0;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl29
	{mso-style-parent:style0;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;}
.xl30
	{mso-style-parent:style0;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;}
.xl31
	{mso-style-parent:style0;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;}
.xl32
	{mso-style-parent:style0;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;}
.xl33
	{mso-style-parent:style0;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;}
.xl34
	{mso-style-parent:style0;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl35
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl36
	{mso-style-parent:style0;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl37
	{mso-style-parent:style0;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl38
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;}
.xl39
	{mso-style-parent:style0;
	font-size:6.0pt;
	font-family:"Book Antiqua", serif;
	mso-font-charset:0;}
.xl40
	{mso-style-parent:style0;
	font-size:6.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.xl41
	{mso-style-parent:style0;
	mso-number-format:"\0022$\0022\#\,\#\#0\.00";
	border:.5pt solid windowtext;}
.xl42
	{mso-style-parent:style0;
	vertical-align:middle;
	border:.5pt solid windowtext;}
.xl43
	{mso-style-parent:style0;
	mso-number-format:"\0022$\0022\#\,\#\#0\.00";
	vertical-align:middle;
	border:.5pt solid windowtext;}
.xl44
	{mso-style-parent:style18;
	vertical-align:middle;
	border:.5pt solid windowtext;}
.xl45
	{mso-style-parent:style18;
	border:.5pt solid windowtext;}
.xl46
	{mso-style-parent:style18;
	mso-number-format:"_\(\0022$\0022* \#\,\#\#0\.00_\)\;_\(\0022$\0022* \\\(\#\,\#\#0\.00\\\)\;_\(\0022$\0022* \0022-\0022??_\)\;_\(\@_\)";
	vertical-align:middle;
	border:.5pt solid windowtext;}
.xl47
	{mso-style-parent:style18;
	mso-number-format:"_\(\0022$\0022* \#\,\#\#0\.00_\)\;_\(\0022$\0022* \\\(\#\,\#\#0\.00\\\)\;_\(\0022$\0022* \0022-\0022??_\)\;_\(\@_\)";
	border:.5pt solid windowtext;}
.xl48
	{mso-style-parent:style0;
	text-align:left;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl49
	{mso-style-parent:style0;
	font-size:9.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;}
.xl50
	{mso-style-parent:style0;
	font-size:8.0pt;
	font-family:"Arial Unicode MS";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	background:white;
	mso-pattern:auto none;}
.xl51
	{mso-style-parent:style0;
	text-align:left;}
.xl52
	{mso-style-parent:style0;
	font-size:8.0pt;
	font-family:"Arial Unicode MS";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:auto none;}
.xl53
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;}
.xl54
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;}
.xl55
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;}
.xl56
	{mso-style-parent:style0;
	mso-number-format:"Short Date";
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl57
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl58
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl59
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
-->
</style>
<!--[if gte mso 9]><xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Sheet1</x:Name>
    <x:WorksheetOptions>
     <x:Print>
      <x:ValidPrinterInfo/>
      <x:HorizontalResolution>600</x:HorizontalResolution>
      <x:VerticalResolution>600</x:VerticalResolution>
     </x:Print>
     <x:PageBreakZoom>60</x:PageBreakZoom>
     <x:Selected/>
     <x:Panes>
      <x:Pane>
       <x:Number>3</x:Number>
       <x:ActiveRow>20</x:ActiveRow>
       <x:ActiveCol>8</x:ActiveCol>
      </x:Pane>
     </x:Panes>
     <x:ProtectContents>False</x:ProtectContents>
     <x:ProtectObjects>False</x:ProtectObjects>
     <x:ProtectScenarios>False</x:ProtectScenarios>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
   <x:ExcelWorksheet>
    <x:Name>Sheet2</x:Name>
    <x:WorksheetOptions>
     <x:ProtectContents>False</x:ProtectContents>
     <x:ProtectObjects>False</x:ProtectObjects>
     <x:ProtectScenarios>False</x:ProtectScenarios>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
   <x:ExcelWorksheet>
    <x:Name>Sheet3</x:Name>
    <x:WorksheetOptions>
     <x:ProtectContents>False</x:ProtectContents>
     <x:ProtectObjects>False</x:ProtectObjects>
     <x:ProtectScenarios>False</x:ProtectScenarios>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
  <x:WindowHeight>12120</x:WindowHeight>
  <x:WindowWidth>13650</x:WindowWidth>
  <x:WindowTopX>-180</x:WindowTopX>
  <x:WindowTopY>-120</x:WindowTopY>
  <x:ProtectStructure>False</x:ProtectStructure>
  <x:ProtectWindows>False</x:ProtectWindows>
 </x:ExcelWorkbook>
 <x:ExcelName>
  <x:Name>Print_Area</x:Name>
  <x:SheetIndex>1</x:SheetIndex>
  <x:Formula>=Sheet1!$A$1:$G$52</x:Formula>
 </x:ExcelName>
</xml><![endif]-->
</head>

<body link=blue vlink=purple>

<?php
$sql="SELECT * FROM oOrder WHERE id=$id";
$odata=mysql_query($sql);
if ($drow=mysql_fetch_array($odata)) {
  $newrow=0;
  $companyid=$drow["companyid"];
  $dateordered=$drow["dateordered"];
  $daterequired=$drow["daterequired"];
  $frs=$drow["frs"];
  $shippingprice=$drow["shippingprice"];
  $addedby=$drow["addedby"];
  
  $sql="SELECT * FROM oCompany WHERE id=$companyid";
  $cdata=mysql_query($sql);
  $crow=mysql_fetch_array($cdata);
  
  $cname=$crow["name"];
  $caddress1=$crow["address1"];
  $caddress2=$crow["address2"];
  $ccity=$crow["city"];
  $cstate=$crow["state"];
  $czipcode=$crow["zipcode"];
  $curl=$crow["url"];
  $cphone=$crow["phone"];
  $cfax=$crow["fax"];
  $ccontactperson=$crow["contactperson"];
  $ccontactemail=$crow["contactemail"];
  $caccountnumber=$crow["accountnumber"];
  
  $sql="SELECT * FROM gUserPrefs WHERE userid=\"$addedby\"";
  $udata=mysql_query($sql);
  $urow=mysql_fetch_array($udata);
  
  $labname=$urow["realname"];
  $labemail=$urow["email"];

  $sql="SELECT oOrderItem.*,oItem.name,oItem.productnumber,oItem.units FROM oOrderItem,oItem WHERE oOrderItem.itemid=oItem.id AND oOrderItem.orderid=$id";
  $idata=mysql_query($sql);
  
} else {
  $newrow=1;
}

?>
<table x:str border=0 cellpadding=0 cellspacing=0 style='border-collapse:
 collapse;table-layout:fixed'>
 <col width=89 style='mso-width-source:userset;mso-width-alt:3254;width:67pt'>
 <col width=265 style='mso-width-source:userset;mso-width-alt:9691;width:199pt'>
 <col width=59 style='mso-width-source:userset;mso-width-alt:2157;width:44pt'>
 <col width=47 style='mso-width-source:userset;mso-width-alt:1718;width:35pt'>
 <col width=73 style='mso-width-source:userset;mso-width-alt:2669;width:55pt'>
 <col width=104 style='mso-width-source:userset;mso-width-alt:3803;width:78pt'>
 <tr height=21 style='height:15.75pt'>
  <td colspan=7 height=21 class=xl55 width=637 style='height:15.75pt;
  width:478pt'>Supply/Equipment Order</td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td colspan=7 height=21 class=xl55 style='height:15.75pt'>University of
  Maryland - Institute for Systems Research</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 colspan=7 style='height:12.75pt;mso-ignore:colspan'></td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 style='height:12.75pt' x:str="Vendor:  ">Vendor:<span
  style='mso-spacerun:yes'>  </span></td>
  <td class=xl25><?php echo($cname); ?></td>
  <td class=xl24>Date:</td>
  <td colspan=3 class=xl56><?php echo($dateordered); ?></td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 style='height:16.5pt' x:str="Address: ">Address:<span
  style='mso-spacerun:yes'> </span></td>
  <td class=xl28 style='border-top:none'><?php echo($caddress1); ?></td>
  <td class=xl24>PO# :</td>
  <td colspan=3 class=xl58>&nbsp;</td>
  <td></td>
 </tr>
 <tr height=23 style='mso-height-source:userset;height:17.25pt'>
  <td height=23 style='height:17.25pt'></td>
  <td class=xl28 style='border-top:none'><?php echo($caddress2); ?></td>
  <td colspan=5 class=xl54>Ship To</td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 style='height:16.5pt'></td>
  <td class=xl28 style='border-top:none'><?php $taddr="$ccity, $cstate $czip"; if (",  " <> $taddr) { echo($taddr); } ?></td>
  <td colspan=5 class=xl24>Carla Scarbor</td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.75pt'>
  <td height=21 style='height:15.75pt' x:str="Phone No:  ">Phone No:<span
  style='mso-spacerun:yes'>  </span></td>
  <td class=xl28 style='border-top:none'><?php echo($cphone);?></td>
  <td colspan=5 class=xl24>University of Maryland - ISR</td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 style='height:16.5pt' x:str="Fax No: ">Fax No:<span
  style='mso-spacerun:yes'> </span></td>
  <td class=xl28 style='border-top:none'><?php echo($cfax); ?></td>
  <td colspan=5 class=xl24>Room 2164<span style='mso-spacerun:yes'>   
  </span>Bldg. 0115</td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 style='height:16.5pt'>Acct. No:</td>
  <td class=xl48 style='border-top:none'><?php echo($caccountnumber); ?></td>
  <td colspan=5 class=xl24>College Park, MD<span style='mso-spacerun:yes'> 
  </span>20742</td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 style='height:16.5pt'>URL:</td>
  <td class=xl28 style='border-top:none'><?php echo($curl); ?></td>
  <td colspan=5 class=xl24><?php echo($labname); ?><span style='mso-spacerun:yes'> 
  </span>x5-6596</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 colspan=7 style='height:12.75pt;mso-ignore:colspan'></td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl39 colspan=2 style='height:12.75pt;mso-ignore:colspan'>NOTE:<span
  style='mso-spacerun:yes'>  </span>THE UNIVERSITY OF MARYLAND IS EXEMPT FROM
  THE FOLLOWING TAXES:</td>
  <td colspan=5 class=xl54>Please Send Invoices and Receipts to:</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl40 colspan=2 style='height:12.75pt;mso-ignore:colspan'>STATE
  OF MARYLAND SALES TAX CERTIFICATE NO. 30002583</td>
  <td colspan=5 class=xl24>Carla Scarbor</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl40 colspan=2 style='height:12.75pt;mso-ignore:colspan'>MANUFACTURERS
  FEDERAL EXISE TAX REGISTRATION NO. 52730123K</td>
  <td colspan=5 class=xl24>301-405-3800</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 colspan=2 style='height:12.75pt;mso-ignore:colspan'></td>
  <td colspan=5 class=xl24>UMD - Institute for Systems Research</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 colspan=2 style='height:12.75pt;mso-ignore:colspan'></td>
  <td colspan=5 class=xl24>Room 2164&nbsp;&nbsp;Bldg. 0115</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td colspan=2 height=17 class=xl54 style='height:12.75pt'
  x:str="The following numbers must appear on all related ">The following
  numbers must appear on all related<span style='mso-spacerun:yes'> </span></td>
  <td colspan=5 class=xl24>College Park, MD<span style='mso-spacerun:yes'> 
  </span>20742</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td colspan=2 height=17 class=xl54 style='height:12.75pt'>correspondence,
  shipping papers, and invoices:</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 colspan=2 style='height:12.75pt;mso-ignore:colspan'></td>
 </tr>
 <tr height=20 style='mso-height-source:userset;height:15.0pt'>
  <td height=20 colspan=2 style='height:15.0pt;mso-ignore:colspan'>FRS#:<span
  style='mso-spacerun:yes'>   </span><?php echo($frs);?><span
  style='mso-spacerun:yes'>  </span>REQ#:<span style='mso-spacerun:yes'> 
  </span>______________<span style='display:none'>_</span></td>
  <td colspan=2 class=xl24></td>
  <td colspan=2 class=xl51></td>
  <td class=xl24></td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 colspan=7 style='height:12.75pt;mso-ignore:colspan'></td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl26 style='height:12.75pt'>Part No.</td>
  <td class=xl26 style='border-left:none'>Item Description</td>
  <td class=xl26 style='border-left:none'>Qty</td>
  <td class=xl26 style='border-left:none'>Unit</td>
  <td class=xl26 style='border-left:none'>Unit Price</td>
  <td class=xl26 style='border-left:none'>Total</td>
 </tr>
  
  <?php 

$totalcost=0;
setlocale(LC_MONETARY, 'en_US');
while ($irow=mysql_fetch_array($idata)) {  ?>

 <tr height=20 style='mso-height-source:userset;height:15.0pt'>
  <td height=20 class=xl49 style='height:15.0pt;border-top:none'><?php echo($irow["productnumber"]);?></td>
  <td class=xl52 style='border-top:none;border-left:none'><?php echo($irow["name"]);?></td>
  <td class=xl44 align=center style='border-top:none;border-left:none'><?php echo($irow["quantity"]);?></td>
  <td class=xl42 align=center style='border-top:none;border-left:none'><?php echo($irow["units"]);?></td>
  <td class=xl46 align=right style='border-top:none;border-left:none'><?php echo(money_format('$%i',$irow["unitprice"]));?></td>
  <td class=xl43 align=right style='border-top:none;border-left:none' x:num="0"
  x:fmla="=C25*E25"><?php $thiscost=$irow["quantity"]*$irow["unitprice"];
                          $totalcost=$totalcost+$thiscost;
                          echo(money_format('$%i',$thiscost));?></td>
  <td></td>
  <td colspan=5 style='mso-ignore:colspan'></td>
 </tr>

                                          <?php } ?>

 <tr height=21 style='mso-height-source:userset;height:15.75pt'>
  <td height=21 class=xl26 style='height:15.75pt;border-top:none'>&nbsp;</td>
  <td class=xl27 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl27 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl27 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl47 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl41 align=right style='border-top:none;border-left:none' x:num="0"
  x:fmla="=C32*E32">$0.00</td>
  <td></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.75pt'>
  <td height=21 class=xl26 style='height:15.75pt;border-top:none'>&nbsp;</td>
  <td class=xl27 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl27 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl27 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl47 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl41 align=right style='border-top:none;border-left:none' x:num="0"
  x:fmla="=C33*E33">$0.00</td>
  <td></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.75pt'>
  <td height=21 colspan=2 style='height:15.75pt;mso-ignore:colspan'></td>
  <td colspan=3 class=xl53>Shipping &amp; Handling</td>
  <td class=xl41 align=right style='border-top:none'><?php echo(money_format('$%i',$shippingprice)); $totalcost=$totalcost+$shippingprice;?></td>
  <td></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.75pt'>
  <td height=21 colspan=2 style='height:15.75pt;mso-ignore:colspan'></td>
  <td colspan=3 class=xl24>Other</td>
  <td class=xl41 style='border-top:none'>&nbsp;</td>
  <td></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.75pt'>
  <td height=21 colspan=2 style='height:15.75pt;mso-ignore:colspan'></td>
  <td colspan=3 class=xl24>Total Cost</td>
  <td class=xl41 align=right style='border-top:none' x:num="0"
  x:fmla="=SUM(F25:F36)"><?php echo(money_format('$%i',$totalcost));?></td>
  <td></td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 colspan=7 style='height:12.75pt;mso-ignore:colspan'></td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl29 style='height:12.75pt'>Authorized</td>
  <td class=xl30>&nbsp;</td>
  <td class=xl30>&nbsp;</td>
  <td class=xl30>&nbsp;</td>
  <td class=xl31>&nbsp;</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl32 style='height:12.75pt'>Signature:</td>
  <td class=xl25>&nbsp;</td>
  <td>Date:</td>
  <td colspan=2 class=xl56 style='border-right:.5pt solid black'>&nbsp;</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl32 style='height:12.75pt'>&nbsp;</td>
  <td colspan=3 style='mso-ignore:colspan'></td>
  <td class=xl33>&nbsp;</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl34 style='height:12.75pt'>&nbsp;</td>
  <td class=xl35>MUST HAVE AUTHORIZED SIGNATURE</td>
  <td class=xl25>&nbsp;</td>
  <td class=xl25>&nbsp;</td>
  <td class=xl36>&nbsp;</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 colspan=7 style='height:12.75pt;mso-ignore:colspan'></td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl38 colspan=2 style='height:12.75pt;mso-ignore:colspan'>Payment
  Information:</td>
  <td class=xl31>&nbsp;</td>
  <td></td>
  <td class=xl29>&nbsp;</td>
  <td class=xl31>&nbsp;</td>
  <td></td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl32 style='height:12.75pt'>&nbsp;</td>
  <td></td>
  <td class=xl33>&nbsp;</td>
  <td></td>
  <td class=xl32 colspan=2 style='mso-ignore:colspan;border-right:.5pt solid black'>Order
  By:<span style='mso-spacerun:yes'>  </span>______________</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl32 style='height:12.75pt'><span
  style='mso-spacerun:yes'> </span></td>
  <td></td>
  <td class=xl33>&nbsp;</td>
  <td></td>
  <td class=xl32>&nbsp;</td>
  <td class=xl33>&nbsp;</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl32 style='height:12.75pt'>&nbsp;</td>
  <td></td>
  <td class=xl33>&nbsp;</td>
  <td></td>
  <td class=xl32 colspan=2 style='mso-ignore:colspan;border-right:.5pt solid black'>Date
  Ordered:<span style='mso-spacerun:yes'>  </span>____________</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl32 style='height:12.75pt'>&nbsp;</td>
  <td></td>
  <td class=xl33>&nbsp;</td>
  <td></td>
  <td class=xl34>&nbsp;</td>
  <td class=xl36>&nbsp;</td>
 </tr>
 <tr height=17 style='height:12.75pt'>
  <td height=17 class=xl34 style='height:12.75pt'>&nbsp;</td>
  <td class=xl25>&nbsp;</td>
  <td class=xl36>&nbsp;</td>
  <td colspan=3 style='mso-ignore:colspan'></td>
 </tr>
</table>

</body>

</html>
