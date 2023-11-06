<?php
include(get_cfg_var('wamp_dir') . "/home/happyhol/domains/initfirstnew.php");
include("passwordcheck.php");
include("phpspreadsheet.php");

if(isset($_POST["export"])){
  ob_start();
}

include("dcstandardheader.php");
include("headerdatacenter.php");

// $request_id = GF_GetRequestInteger("ID");
echo CreateMenu("Top","pugliadailydec.php","");
echo  "<div class=\"container-fluid\">";
if (!PermissionGranted())
  echo "Not allowed to run script";
else
{
  $truecall = 1;
  echo "<FORM method=post action=\"pugliadailydec.php\">";
    include("pugliadailydec2.php");
  echo "</FORM>";
}
echo "</div>";
include("dcstandardfooter.php");

?>

