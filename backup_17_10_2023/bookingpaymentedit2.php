<?php
if (!$truecall)
  exit;

echo "<script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>";
echo "<link href='css/select2.min.css' rel='stylesheet' />" ;
echo"<script src='js/select2.min.js'></script>";

if ($_SESSION['ULI'] && $request_id)
  {

  if (isset($_POST["CLEAR"]))
    {
    $update = new BookingDBUpdate($request_id);
    $update->SkipLastUpdatedBy();
    $update->BookingDBUpdateAddField("bkg_cccvc"      ,"");
    $update->BookingDBUpdateAddField("bkg_ccexpmonth" ,"");
    $update->BookingDBUpdateAddField("bkg_ccexpyear"  ,"");
    $update->BookingDBUpdateAddField("bkg_ccname"     ,"");
    $update->BookingDBUpdateAddField("bkg_ccnumber"   ,"");
    $update->BookingDBUpdateAddField("bkg_cctype"     ,0);
    $update->BookingDBUpdatePerform();

//    PerformDBAction("UPDATE bookingdb SET bkg_ccname='', bkg_ccnumber='',bkg_cctype=0, bkg_ccexpyear='',bkg_ccexpmonth='', bkg_cccvc='' WHERE id=$request_id");   //AA0391
    }

  if (isset($_POST["SEND"]))
    {
    $finalpricebase       = GF_PrepareFieldForDatabase(1,$_POST["finalpricebase"]);        //AA0515
    $bcomcommissionfield  = GF_PrepareFieldForDatabase(1,$_POST["bcomcommissionfield"]);
    $dateto               = GF_PrepareFieldForDatabase(4,$_POST["dateto"]);
    $moneytolch           = GF_PrepareFieldForDatabase(3,$_POST["moneytolch"]);
    $payment2nd           = GF_PrepareFieldForDatabase(3,$_POST["payment2nd"]);
    $paymentaccepted      = GF_PrepareFieldForDatabase(3,$_POST["paymentaccepted"]);
    $paymentnotes         = GF_PrepareFieldForDatabase(0,$_POST["paymentnotes"]);
    $propid               = GF_PrepareFieldForDatabase(1,$_POST["propid"]);
    $prepaycheck          = GF_PrepareFieldForDatabase(3,$_POST["prepaycheck"]);
    $skipcommission       = GF_PrepareFieldForDatabase(3,$_POST["skipcommission"]);
    $skipcleaningcalc     = GF_PrepareFieldForDatabase(3,$_POST["skipcleaningcalc"]);
    $skipmanagementfee    = GF_PrepareFieldForDatabase(3,$_POST["skipmanagementfee"]);
    $skiptouristtax       = GF_PrepareFieldForDatabase(3,$_POST["skiptouristtax"]);

    $payments_class = new GF_BookingPayments($request_id,"");
    $payments_class->SetPaymentLineFromPost($_POST);

    $paid        = $payments_class->GetPaymentsPaid();
    $payments    = $payments_class->PaymentsToString();

    $update = new BookingDBUpdate($request_id);
    $update->SkipLastUpdatedBy();
    if ($_SESSION['ULI']==gl_user_tarun || $_SESSION['ULI']==gl_user_eric || $_SESSION['ULI']==gl_user_govind)
      $update->BookingDBUpdateAddField("bkg_bcomcommissionfield"    ,$bcomcommissionfield);
    $update->BookingDBUpdateAddField("bkg_finalpricebase"         ,$finalpricebase);        //AA0515
    $update->BookingDBUpdateAddField("bkg_moneytolch"             ,$moneytolch);
    $update->BookingDBUpdateAddField("bkg_notesaccounts"          ,$_POST["notesaccounts"]);
    $update->BookingDBUpdateAddField("bkg_paid"                   ,$paid);
    $update->BookingDBUpdateAddField("bkg_payment2nd"             ,$payment2nd);
    $update->BookingDBUpdateAddField("bkg_paymentaccepted"        ,$paymentaccepted);
    $update->BookingDBUpdateAddField("bkg_paymentnotes"           ,$paymentnotes);
    $update->BookingDBUpdateAddField("bkg_payments"               ,$payments);
    $update->BookingDBUpdateAddField("bkg_prepaycheck"            ,$prepaycheck);
    $update->BookingDBUpdateAddField("bkg_skipcleaningcalc"       ,$skipcleaningcalc);
    $update->BookingDBUpdateAddField("bkg_skipcommission"         ,$skipcommission);
    $update->BookingDBUpdateAddField("bkg_skipmanagementfee"      ,$skipmanagementfee);
    $update->BookingDBUpdateAddField("bkg_skiptouristtax"         ,$skiptouristtax);
    $update->BookingDBUpdatePerform();

    if ($dateto>=GF_TodayCET())
      {
      require_once("paymentscalculate.php");
      PaymentsCalculate($request_id,"One Booking");
      }

    LogAction("Update Booking PayEdit",$request_id,$propid,0,0);
//    echo "<script>window.location = \"bookingpayedit.php?ID=$request_id\";</script>";
    }

  $record = PerformDBAction("SELECT * FROM bookingdb WHERE id=$request_id",1);
  if (!$record)
    echo "Booking does not exist";
  else
    {
    $permit = PermitToSeeBooking($record);
    if ($permit)
      {
      echo Draw($record,$request_id);
      echo BookingMenuNew($record,"Payments");
      }
    else
      echo "No rights to see page";
    }
  }

function Draw($record,$request_id) // Eric 20191123 warning
   {
  require_once("errormanfuncs.php");

  $adults              = GF_PrepareFieldFromDatabase(1,$record["bkg_adults"]);
  $agent               = GF_PrepareFieldFromDatabase(1,$record["bkg_agent"]);
  $agentref            = GF_PrepareFieldFromDatabase(0,$record["bkg_agentref"]);         //AA0130
  $arrivaltime         = GF_PrepareFieldFromDatabase(1,$record["bkg_arrivaltime"]);
  $bcomcommissionfield = GF_PrepareFieldFromDatabase(1,$record["bkg_bcomcommissionfield"]);
  $bookingdate         = GF_PrepareFieldFromDatabase(4,$record["bkg_bookingdate"]);
  $cccvc               = GF_PrepareFieldFromDatabase(0,$record["bkg_cccvc"]);
  $ccexpmonth          = GF_PrepareFieldFromDatabase(0,$record["bkg_ccexpmonth"]);
  $ccexpyear           = GF_PrepareFieldFromDatabase(0,$record["bkg_ccexpyear"]);
  $ccname              = GF_PrepareFieldFromDatabase(0,$record["bkg_ccname"]);
  $ccnumber            = GF_PrepareFieldFromDatabase(0,$record["bkg_ccnumber"]);
  $cctype              = GF_PrepareFieldFromDatabase(1,$record["bkg_cctype"]);
  $children            = GF_PrepareFieldFromDatabase(1,$record["bkg_children"]);
  $company             = GF_PrepareFieldFromDatabase(1,$record["bkg_company"]);
  $countrynr           = GF_PrepareFieldFromDatabase(1,$record["bkg_countrynr"]);
  $currency            = GF_PrepareFieldFromDatabase(1,$record["bkg_currency"]);
  $datefrom            = GF_PrepareFieldFromDatabase(4,$record["bkg_datefrom"]);
  $dateto              = GF_PrepareFieldFromDatabase(4,$record["bkg_dateto"]);
  $departuretime       = GF_PrepareFieldFromDatabase(1,$record["bkg_departuretime"]);
  $email               = GF_PrepareFieldFromDatabase(0,$record["bkg_email"]);
  $finalprice          = GF_PrepareFieldFromDatabase(1,$record["bkg_finalprice"]);            //AA0515
  $finalpricebase      = GF_PrepareFieldFromDatabase(1,$record["bkg_finalpricebase"]);        //AA0515
  $finalpricetax       = GF_PrepareFieldFromDatabase(1,$record["bkg_finalpricetax"]);
  $firstname           = GF_PrepareFieldFromDatabase(0,$record["bkg_firstname"]);
  $guestaddress1       = GF_PrepareFieldFromDatabase(0,$record["bkg_guestaddress1"]);       //AA0132
  $guestaddress2       = GF_PrepareFieldFromDatabase(0,$record["bkg_guestaddress2"]);       //AA0132
  $guestpostcode       = GF_PrepareFieldFromDatabase(0,$record["bkg_guestpostcode"]);       //AA0132
  $guesttown           = GF_PrepareFieldFromDatabase(0,$record["bkg_guesttown"]);           //AA0132
  $language            = GF_PrepareFieldFromDatabase(1,$record["bkg_language"]);
  $lastname            = GF_PrepareFieldFromDatabase(0,$record["bkg_lastname"]);
  $makepaymentmonthly  = GF_PrepareFieldFromDatabase(1,$record["bkg_makepaymentmonthly"]);
  $makercompany        = GF_PrepareFieldFromDatabase(3,$record["bkg_makercompany"]);
  $mobile              = GF_PrepareFieldFromDatabase(0,$record["bkg_mobile"]);
  $moneytolch          = GF_PrepareFieldFromDatabase(3,$record["bkg_moneytolch"]);
  $notes               = GF_PrepareFieldFromDatabase(0,$record["bkg_notes"]);
  $notesaccounts       = GF_PrepareFieldFromDatabase(0,$record["bkg_notesaccounts"]);
  $paid                = GF_PrepareFieldFromDatabase(1,$record["bkg_paid"]);
  $payment2nd          = GF_PrepareFieldFromDatabase(3,$record["bkg_payment2nd"]);
  $paymentaccepted     = GF_PrepareFieldFromDatabase(3,$record["bkg_paymentaccepted"]);
  $paymentnotes        = GF_PrepareFieldFromDatabase(0,$record["bkg_paymentnotes"]);
  $payments            = GF_PrepareFieldFromDatabase(0,$record["bkg_payments"]);           //AA0394
  $prepaycheck         = GF_PrepareFieldFromDatabase(3,$record["bkg_prepaycheck"]);
  $propid              = GF_PrepareFieldFromDatabase(1,$record["bkg_propid"]);
  $propname            = GF_PrepareFieldFromDatabase(0,$record["bkg_propname"]);
  $skipcommission      = GF_PrepareFieldFromDatabase(3,$record["bkg_skipcommission"]);
  $skipcleaningcalc    = GF_PrepareFieldFromDatabase(3,$record["bkg_skipcleaningcalc"]);
  $skipmanagementfee   = GF_PrepareFieldFromDatabase(3,$record["bkg_skipmanagementfee"]);
  $skiptouristtax      = GF_PrepareFieldFromDatabase(3,$record["bkg_skiptouristtax"]);
  $statusbooking       = GF_PrepareFieldFromDatabase(1,$record["bkg_status"]);
  $telephone           = GF_PrepareFieldFromDatabase(0,$record["bkg_telephone"]);

  $tobepaid = $finalprice - $paid + 0.00;
  if ($paymentaccepted)
    $tobepaid = 0;
  $tobepaid_out = number_format($tobepaid,2,'.','');

  $payments_class = new GF_BookingPayments($propid,$payments);

  $strout = "";
  $strout .= "<div class=canvasnew23>";
  $extramsg = "";
  if(isset($_SESSION['capture_msg']) && $_SESSION['capture_msg']!='') {
	$extramsg = 	$_SESSION['capture_msg'];
	unset($_SESSION['capture_msg']);
  }
  if($extramsg) {
	  if(strpos($extramsg,"200")!==false)
		$strout .= "<table class=errormessage cellspacing=0 width=100%><tr><td bgcolor=lightgreen>$extramsg</td></tr></table>";
	  else
		$strout .= ErrorMessageEditNoBootstrap(gl_errors_bookings,0,$record,$extramsg);
  }else
		$strout .= ErrorMessageEditNoBootstrap(gl_errors_bookings,0,$record);

  require_once("bookingcalculation.inc");
  $a = new GF_BookingCalculation(0,$record);
  $a->LoadFromDatabase();

  $b = new GF_BookingCalculation(0,$record);
  $b->Calculate();
  $b->Sort();

  $equal = $a->IsEqualTo($b);

  $strout .= "<input type=hidden name=propid       value=$propid>";   //AA0251
  $strout .= "<input type=hidden name=datefrom     value=$datefrom>";
  $strout .= "<input type=hidden name=dateto       value=$dateto>";
  $strout .= "<table class=dialogedit cellspacing=0 width=100%>";
    $strout .= "<tr>";
      $strout .= "<td class=dialogbold>";
        $strout .= "Booking: $request_id";
      $strout .= "</td>";
      if ($equal==0)
        {
        $strout .= "<td class=dialogredlarge colspan=3>";
          $strout .= "Calculations different from stored calculations.";
        $strout .= "</td>";
        }
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td width=150>Property name</td>";
      $strout .= "<td width=350>";
        if ($propid)
          $strout .= PropertyData($propid,"pro_name") . " ($propid)";
      $strout .= "</td>";
      $strout .= "<td width=150>Status</td>";
      $strout .= "<td>";
        $strout .= GF_ValueFromTable($_SESSION['bookingstatustable'],$statusbooking,0,1);
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td>Arrival/departure date</td>";
      $strout .= "<td>";
        $nrofdays = GF_CalculateDays($datefrom,$dateto);
        $strout .= "$datefrom - $dateto";

        $fieldname = "dayshort" . GF_DayofWeek($datefrom);
        $dayfrom = GF_Text($fieldname);
        $fieldname = "dayshort" . GF_DayofWeek($dateto);
        $dayto = GF_Text($fieldname);

        $strout .= " ($nrofdays days, $dayfrom-$dayto)";
      $strout .= "</td>";
      $strout .= "<td>Agent</td>";
      $strout .= "<td>";
        if ($agent)
          $strout .= UserData($agent,"usr_name") . " ($agent)";
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td>Arrival/departure time</td>";
      $strout .= "<td>";
        $strout .= GF_ValueFromTable($_SESSION['arrivaltimetable'],$arrivaltime,0,1);
        $strout .= " / ";
        $strout .= GF_ValueFromTable($_SESSION['departuretimetable'],$arrivaltime,0,1);
      $strout .= "</td>";
      $strout .= "<td>Agent reference</td>";
      $strout .= "<td>";
        $strout .= $agentref;
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td>Adults/Children</td>";
      $strout .= "<td>";
        $strout .= "$adults / $children";
      $strout .= "</td>";
      $strout .= "<td>Property Company</td>";
      $strout .= "<td>";
        if ($company)
          $strout .= UserData($company,"usr_name");
        else
          $strout .= "";
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td>Booking date</td>";
      $strout .= "<td>";
        $strout .= $bookingdate;
      $strout .= "</td>";
    $strout .= "</tr>";

    $fixedprice = PropertyIsFixedPrice($propid,GF_TodayCET());
    $strout .= "<tr>";
      $strout .= "<td>Fixed Price?</td>";
      $strout .= "<td>";
       if ($fixedprice)
         $strout .= "Yes";
       else
         $strout .= "No";
      $strout .= "</td>";
    $strout .= "</tr>";


  $strout .= "</table>";


  $strout .= "<br>";

  $strout .= "<table class=dialogedit  cellspacing=0 width=100%>";
    $strout .= "<tr>";
      $strout .= "<td class=dialogbold colspan=3>Client Data</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td width=150>First/Last Name</td>";
      $strout .= "<td width=350>$firstname $lastname</td>";
      $strout .= "<td width=150>Telephone</td>";
      $strout .= "<td>$telephone</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td width=150>Email Address</td>";
      $strout .= "<td width=400>$email</td>";
      $strout .= "<td>Mobile</td>";
      $strout .= "<td>$mobile</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td>Address Line 1</td>";
      $strout .= "<td>$guestaddress1</td>";
      $strout .= "<td>Language</td>";
      $strout .= "<td>";
        $strout .= GF_ValueFromTable($_SESSION['languagetable'],$language,0,1);
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td>Address Line 2$guestaddress2</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td>Postcode/Town</td>";
      $strout .= "<td>$guestpostcode  /  $guesttown</td>";
      $strout .= "<td>Country</td>";
      $strout .= "<td>";
        $strout .= GetCountryName($countrynr);
      $strout .= "</td>";
    $strout .= "</tr>";
  $strout .= "</table>";
if(PermissionGranted('accountteam') || $_SESSION['ULI']==gl_user_bipin) {
  $strout .= "<br>";
  $strout .= "<table class=dialogedit cellspacing=0 width=100%>";
	$strout .= "<tr>";
      $strout .= "<td width=120 class=dialogbold>Collect amount</td>";
      $strout .= "<td>";
        $strout .= GF_MakeInputField("collect_amount",0,8,8,1);

	  $strout .= "<input type=submit name=CAPTURE value=\"Collect amount\" onclick=\"return confirm('Are you sure ?');\" style='background:green; color:#fff;'>";
	  $thirtypercent = $finalprice*.30;
	  $strout .= " 30% of total is:".number_format($thirtypercent,2,'.','');
      $strout .= "</td>";
    $strout .= "</tr>";
  $strout .= "</table>";
}
	$strout .= "<br>";
  $property_currency = CurrencyForProperty($propid);
  $strout .= "<table class=dialogedit cellspacing=0 width=100%>";
    $strout .= "<tr>";
      $strout .= "<td class=dialogbold width=120>Financials</td>";
      $strout .= "<td class=dialogbold width=90>" . GF_ValueFromTable($_SESSION['currencytable'],$property_currency,0,2) . "</td>";
// Eric 20191123 warning      if ($othercurrencies)
//        $strout .= "<td class=dialogbold width=90>Received</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td width=120>Agreed price</td>";
      $strout .= "<td>";
        $strout .= $finalprice;
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td width=120>Base price</td>";
      $strout .= "<td>";
        $strout .= GF_MakeInputField("finalpricebase",$finalpricebase,10,10,1);
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td width=120>Extra Tourist Tax</td>";
      $strout .= "<td>";
        $strout .= $finalpricetax;
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td width=120>BCOM Commission</td>";
      $strout .= "<td>";
        $strout .= GF_MakeInputField("bcomcommissionfield",$bcomcommissionfield,10,10,1);
      $strout .= "</td>";
      $strout .= "</td>";
      $strout .= "<td colspan=12>To be paid: $tobepaid_out";
      $strout .= "</td>";
    $strout .= "</tr>";

    $payments_maxqty = $payments_class->GetPaymentMaxQty(); //AA0394
    $strout .= "<tr>";
        $strout .= "<td width=120></td>\n";
        $strout .= "<td width=80></td>\n";
        $strout .= "<td width=80>Amount</td>\n";
        $strout .= "<td width=60>Bank Account</td>\n";
        $strout .= "<td width=60>Date</td>\n";
        $strout .= "<td width=60>Agent paid</td>\n";
        $strout .= "<td>Notes</td>\n";
    $strout .= "</tr>";
    for ($i=0; $i<$payments_maxqty; $i++)
      {
      $method = $payments_class->GetPaymentMethod($i);
      $payment_currency = PaymentMethodCurrency($method);

      $strout .= "<input type=hidden name=paymentauthcode$i value=" . $payments_class->GetPaymentAuthCode($i) . ">";
      $strout .= "<input type=hidden name=paymentcaptcode$i value=" . $payments_class->GetPaymentCaptCode($i) . ">";
      $iplusone = $i+1;
      $strout .= "<tr>";
        $strout .= "<td>Payment $iplusone</td>\n";
        $strout .= "<td>";
        $amountleft = $payments_class->GetPaymentAmountLeft($i,1);
        if ($payment_currency==$property_currency)
          $strout .= "<input type=hidden name=paymentleft$i value=$amountleft>$amountleft";
        else
          $strout .= GF_MakeInputField("paymentleft$i",$payments_class->GetPaymentAmountLeft($i,1),10,10,1);
        $strout .= "</td>";


        $strout .= "<td>";
          $strout .= GF_MakeInputField("paymentright$i",$payments_class->GetPaymentAmountRight($i,1),10,10,1);
        $strout .= "</td>";

        $strout .= "<td>";
          if ($makercompany==gl_user_portoletizia)
            $strout .= FillBankAccountsSelectionBox("paymethod$i",$payments_class->GetPaymentMethod($i),3);           //AA0481
          else
            $strout .= FillBankAccountsSelectionBox("paymethod$i",$payments_class->GetPaymentMethod($i),1,"1009");           //AA0481

         $strout .= "</td>";

        $strout .= "<td>";
          $strout .= GF_MakeInputField("paydate$i",$payments_class->GetPaymentDate($i),8,8,1);
        $strout .= "</td>";

        $strout .= "<td>";
          $strout .= MakeCheckBox("paycost$i",$payments_class->GetPaymentBankCost($i));
        $strout .= "</td>";

        $strout .= "<td>";
         $strout .= GF_MakeInputField("paydesc$i",$payments_class->GetPaymentNotes($i),20,20,0);
        $strout .= "</td>";

      $strout .= "</tr>";

      ?>
     <script>

          $(document).ready(function(){
            $("select[name='paymethod"+<?php echo $i?>+"']").select2();
          });

    </script>


      <?php
      }

    $strout .= "<tr>";
      $strout .= "<td>Total paid</td>";
      $strout .= "<td colspan=8>";
        $paid_out = number_format($paid,2,'.','');
        $agentcommission = 0; //Eric 20191123 warning
        $overpaid = $paid - $finalprice - $agentcommission;
        $overpaid_out  = number_format($overpaid,2,'.','');
        $strout .= $paid_out;
      $strout .= "</td>";
    $strout .= "</tr>";
  $strout .= "</table>";

  $strout .= "<br>";

  $strout .= "<table class=dialogedit cellspacing=0 width=100%>";
    $strout .= "<tr>";
      $strout .= "<td width=200>";
        $strout .= MakeCheckBox("skiptouristtax",$skiptouristtax);
        $strout .= "Skip Tourist Tax";
      $strout .= "</td>";
      $strout .= "<td width=200>";
        $strout .= MakeCheckBox("skipcommission",$skipcommission);
        $strout .= "Skip commission";
      $strout .= "</td>";
      $strout .= "<td>";
        $strout .= MakeCheckBox("skipmanagementfee",$skipmanagementfee);
        $strout .= "Skip management fee";
      $strout .= "</td>";
      $strout .= "<td>";
        $strout .= MakeCheckBox("skipcleaningcalc",$skipcleaningcalc);
        $strout .= "Skip Cleaning Calculation";
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td>";
        $strout .= MakeCheckBox("moneytolch",$moneytolch);
        $strout .= "All money to company";
      $strout .= "</td>";
  $strout .= "</table>";

  $strout .= "<br>";
  $strout .= "<table class=dialogedit cellspacing=0 width=100%>";
    $strout .= "<tr>";
      $strout .= "<td width=200>2nd payment on arrival</td>";
      $strout .= "<td>";
        $strout .= MakeCheckBox("payment2nd",$payment2nd);
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td>Accept difference ($overpaid_out)</td>";
      $strout .= "<td>";
        $strout .= MakeCheckBox("paymentaccepted",$paymentaccepted);
      $strout .= "</td>";
    $strout .= "</tr>";

    if ($_SESSION['ULI']==gl_user_eric || PermissionGranted('accountteam'))
      {
      $strout .= "<tr>";
        $strout .= "<td>Prepay Check</td>";
        $strout .= "<td>";
          $strout .= MakeCheckBox("prepaycheck",$prepaycheck);
      $strout .= "</td>";
      }
    else
      {
      $strout .= "<input type=hidden name=prepaycheck value=$prepaycheck>";
      }

    $strout .= "<tr>";
      $strout .= "<td>Payment Notes</td>";
      $strout .= "<td>";
        $strout .= GF_MakeInputField("paymentnotes",$paymentnotes,80,80,0);
      $strout .= "</td>";
    $strout .= "</tr>";

    $strout .= "<tr>";
      $strout .= "<td width=200>Notes (For accounts)</td>";
      $strout .= "<td>";
        $strout .= "<textarea cols=80 rows=8 name=notesaccounts style=\"font-size:8pt;\">$notesaccounts</textarea>";
      $strout .= "</td>";
    $strout .= "</tr>";

  $strout .= "</table>";
  $strout .=  "<br>";

//  if ($agent==gl_user_bookingcom || $agent==gl_user_bookingcomch)
    if ($_SESSION['ULI']==gl_user_eric || $_SESSION['ULI']==gl_user_veena || $_SESSION['ULI']==gl_user_larisse || $_SESSION['ULI']==gl_user_bipin || $_SESSION['ULI']==gl_user_dipanshu  || PermissionGranted('accountteam'))
      {
      $strout .= "<table class=dialogedit cellspacing=0 width=100%>";
        $strout .= "<tr>";
          $strout .= "<td colspan=2><b>Credit card details</b></td>";
        $strout .= "</tr>";

        $strout .= "<tr>";
          $strout .= "<td width=150>Type</td>";
          $strout .= "<td>";
           // $strout .= "<b>" . $_SESSION['creditcardtypebookingcom'][$cctype][1] . "</b>";
          $strout .= "</td>";
        $strout .= "</tr>";

        $strout .= "<tr>";
          $strout .= "<td>Name</td>";
          $strout .= "<td>";
            $strout .= "<b>" . GF_Encrypt($ccname,0) . "</b>";
          $strout .= "</td>";
        $strout .= "</tr>";

        $strout .= "<tr>";
          $strout .= "<td>Number</td>";
          $strout .= "<td>";
           // $strout .= "<b>" . GF_Encrypt($ccnumber,0) . "</b>";
			if(GF_Encrypt($ccnumber,0))
				$strout .= "<b>************".substr(GF_Encrypt($ccnumber,0),-4)."</b>";
          $strout .= "</td>";
        $strout .= "</tr>";

        $strout .= "<tr>";
          $strout .= "<td>Expire Year / Month</td>";
          $strout .= "<td>";
           // $strout .= "<b>" . GF_Encrypt($ccexpyear,0) . "/" . GF_Encrypt($ccexpmonth,0) . "</b>";
		   if(GF_Encrypt($ccexpyear,0))
			$strout .= "<b>**** / **</b>";
          $strout .= "</td>";
        $strout .= "</tr>";

        $strout .= "<tr>";
          $strout .= "<td>Cvc code</td>";
          $strout .= "<td>";
            $strout .= "<b>" . GF_Encrypt($cccvc,0) . "</b>";
          $strout .= "</td>";
        $strout .= "</tr>";
      $strout .= "</table>";
      }

    if($makepaymentmonthly){
      $paymentdistribution = GF_PerMonthPaymentDistribution($record,0);

      $strout .= "<table class=dialogedit cellspacing=0 width=100%>";
      $strout .= "<tr>";
        $strout .= "<th colspan=6>Payment Distribution  - (Pay Today: " . $paymentdistribution['paytoday'] . ")</th>";
      $strout .= "</tr>";

      $strout .= "<tr>";
        $strout .= "<th></th>";
        $strout .= "<th>Date From</th>";
        $strout .= "<th>Date To</th>";
        $strout .= "<th>To be Paid (on period month)</th>";
        $strout .= "<th>Paid</th>";
        $strout .= "<th>Pay Now (on period month)</th>";
      $strout .= "</tr>";

      $counter = 0;
      foreach($paymentdistribution as $key=>$paymentdistributiontab){
        if(!is_numeric($key)){
          continue;
        }
        $strout .= "<tr>";
          $strout .= "<td>" . ++$counter . "</td>";
          $strout .= "<td>" . $paymentdistributiontab['datefrom'] . "</td>";
          $strout .= "<td>" . $paymentdistributiontab['dateto'] . "</td>";
          $strout .= "<td>" . $paymentdistributiontab['tobepaid'] . "</td>";
          $strout .= "<td>" . $paymentdistributiontab['paid'] . "</td>";
          $strout .= "<td>" . $paymentdistributiontab['paynow'] . "</td>";
        $strout .= "</tr>";
      }
    }
  $strout .= "</table>";
  $strout .= "<br>";
  $strout .= "</div>";
  $strout .= "<div class=menu3>";

  $today = GF_TodayCET();
  $disalloweditafterdeparture = 0;
  if ($dateto<$today)
    if ($statusbooking==1 || $statusbooking==2 || $statusbooking==3)
      {
      $help = PermissionGranted("booking_edit_afterdeparture");
      if (PermissionGranted("booking_edit_afterdeparture")==0)
        $disalloweditafterdeparture = 1;
      }

//  $disalloweditafterdeparture = 0;
  if (PermitToUpdateBooking($record) && $disalloweditafterdeparture==0)
    {
    if (PermissionGranted('accountteam') || $_SESSION['ULI']==gl_user_eric ||
        $_SESSION['ULI']==gl_user_bookerpl || $_SESSION['ULI']==gl_user_carla || $_SESSION['ULI']==gl_user_bipin)
      $strout .= "<input type=submit name=SEND value=\"Save Changes\">&nbsp;&nbsp;";
      if ($_SESSION['ULI']==gl_user_eric || PermissionGranted('accountteam'))
        $strout .= "<input type=submit name=CLEAR value=\"Clear Credit Card Details\">";
    }

  if ($propid && PropertyData($propid,"pro_iscategory")) //AA0396
    {
    $dbyear     = gl_dbyear;
    $dbyearnext = gl_dbyear + 1;
    $strout .= "<input type=button onclick=\"CalendarDrawNew($dbyear,$propid)\" value=\"Calendar $dbyear\">";
    $strout .= "<input type=button onclick=\"CalendarDrawNew($dbyearnext,$propid)\" value=\"Calendar $dbyearnext\">";
    }
  $strout .= "</div>";

  return $strout;
  }
//#424040f0;#253529
?>
<script>
$(document).on('select2:open', e => {
              const select2 = $(e.target).data('select2');

              if (!select2.options.get('multiple')) {
                  select2.dropdown.$search.get(0).focus();
              }
});
</script>
<style>
.select2-container .select2-selection--single {
    height: 19px;
    border: 1px solid grey ;
	background: white;
  vertical-align: top;
	font-size: 10px;
  bottom: 5px;


}

.select2-container .select2-selection--single .select2-selection__clear{
	margin-right:4px;
}
.select2-container .select2-selection--single .select2-selection__rendered {
    padding-left: 3px;
    padding-bottom: 5px;
    padding-top:0px;

}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 18px;
    position: absolute;
    top: 1px;
    right: 1px;
    width: 20px;
    color: #444;
}
span.select2-selection.select2-selection--single::before {
    position: absolute;

    right: 5px;
	  top:0px;
    transform: rotate(90deg);
}

.select2-search--dropdown .select2-search__field {
    padding: inherit;
    width: 97%;
    height: 23px;
    max-width: 100%;
}
.select2-results__option {
    padding: 7px 5px 7px;
    vertical-align: middle;
    font-size: 11px;

    user-select: none;
    -webkit-user-select: none;
    font-family: Verdana, Arial, Helvetica, sans-serif;
}
.select2-results__options {
    list-style: none;
    margin: 0;
    padding: 0;
    overflow-y: scroll;
    max-height: 160px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #444;
    line-height: 16px;
}

</style>

?>