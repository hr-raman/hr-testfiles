<?php
if (!$truecall)
  exit;

$arrivaldate     = GF_DateAddDays(GF_TodayCET(),-1);
$counter         = 0;
$requiredcompany = gl_company_puglia;
$excelheader = $excelmainbody = [];

if(isset($_POST["send"]) || isset($_POST["export"])){
  $arrivaldate = GF_PrepareFieldForDatabase(4,$_POST["arrivaldate"]);
  $arrivaldate = GF_DateValid($arrivaldate)?$arrivaldate:GF_DateAddDays(GF_TodayCET(),-1);
}

$stick_contents  = "";
$stick_contents .= str_replace("name=arrivaldate","name=arrivaldate placeholder=yyyymmdd",GF_MakeInputField("arrivaldate",$arrivaldate,10,8,4));
$stick_contents .= MakeButtonType("send","Submit","submit");
$stick_contents .= MakeButtonType("export","Export","submit");

$excelheader  = ["Booking ID","Property Name","Arrival","Departure","Adults","Childs","Guest Booking Country"];

$str  = "";
$str .= "<br>";
$str .= "<div id='contentWrapper' class='table-responsive'>";
$str .= "<table id='tableWrapper' class='table table-striped' style='width: 100%;'>";
$str .= "<tr><th colspan='13' style='text-align:center;'>Daily declaration for Puglia</th></tr>";
$str .= "<tr>";
$str .= "<th></th>";
foreach($excelheader as $headeritem){
  $str .= "<th>$headeritem</th>";
}
$str .= "</tr>";

$db = new DBRead("bookingdb","bkg_status IN (1,2) AND bkg_datefrom=$arrivaldate AND bkg_company=$requiredcompany","","id,bkg_adults,bkg_children,bkg_countrynr,bkg_datefrom,bkg_dateto,bkg_propname");
while($data = $db->DBGetNext()){
    $id              = GF_PrepareFieldFromDatabase(1,$data["id"]);
    $adults          = GF_PrepareFieldFromDatabase(1,$data["bkg_adults"]);
    $children        = GF_PrepareFieldFromDatabase(1,$data["bkg_children"]);
    $countrynr       = GF_PrepareFieldFromDatabase(0,$data["bkg_countrynr"]);
    $datefrom        = GF_PrepareFieldFromDatabase(4,$data["bkg_datefrom"]);
    $dateto          = GF_PrepareFieldFromDatabase(4,$data["bkg_dateto"]);
    $propname        = GF_PrepareFieldFromDatabase(0,$data["bkg_propname"]);

    $bookinglink     = "<a href='https://www.happy.rentals/datacenter/bookingedit.php?ID=$id' target='_blank'>$id</a>";
    $countryname     = GetCountryName($countrynr);

    $excelbody       = [$id,$propname,$datefrom,$dateto,$adults,$children,$countryname];
    $excelmainbody[] = $excelbody;

    $str .= "<tr>";
    $str .= "<td>" . ++$counter . "</td>";
    $str .= "<td>$bookinglink</td>";
    $str .= "<td>$propname</td>";
    $str .= "<td>$datefrom</td>";
    $str .= "<td>$dateto</td>";
    $str .= "<td>$adults</td>";
    $str .= "<td>$children</td>";
    $str .= "<td>$countryname</td>";
    $str .= "</tr>";
}
$str .= "</table>";

if(isset($_POST["export"])){
  ob_end_clean();
  $filename = "Puglia".$arrivaldate.rand(100,999).".xlsx";
  GF_ExportXLSXData($filename,$excelmainbody,$excelheader);
	exit;
}

echo MakeStickyBar($stick_contents);
echo $str;
?>