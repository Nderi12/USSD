<?php
if(@$_REQUEST["reset_phone"]==1){ session_start(); session_destroy(); }
// session_start(); session_destroy();
session_start();
error_reporting(0);
if(!isset($_SESSION["TransactionId"])){ $_SESSION["TransactionId"]=rand(55555,99999); }
if(isset($_REQUEST["MSISDN"]) && !empty($_REQUEST["MSISDN"]) ){ $_SESSION["MSISDN"]=$_REQUEST["MSISDN"]; }
?>

<html>
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USSD demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</head>
<body>
<?php
$demo_page_url="http://localhost/ussd/";
$api_url="http://localhost:8000/api/ussd-requests";

if(!isset($_REQUEST["ussd_string"])){ $_REQUEST["ussd_string"]=""; }

$post=["sessionId"=>$_SESSION["TransactionId"],"phoneNumber"=>$_SESSION["MSISDN"],"text"=>$_REQUEST["ussd_string"],"serviceCode"=>1234];
if(isset($_SESSION["MSISDN"])){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
  $R_String = curl_exec($ch);
  $raw_response = $R_String;
  $action=substr($R_String,0,3);
  $R_String = substr($R_String,3);
}
if(!isset($R_String) && isset($_SESSION["MSISDN"]) ){ echo $R_String; }
?>
  <div class="card row">
    <div class="col-md-12">

      <?php if(!isset($_SESSION["MSISDN"])){ ?>
        <form action="<?php echo $demo_page_url;?>" method="post">Please enter your phone number in international format<br><br><input class="form-control" type="text" autocomplete="off" value="" id="MSISDN" name="MSISDN" placeholder="e.g. 254722..." autofocus></form>
      <?php }else{ ?>

            <?php echo "Session ID: ".$_SESSION["TransactionId"]." | Phone Number: ".$_SESSION["MSISDN"]." | <a href='".$demo_page_url."/?reset_phone=1'>Change Phone</a><hr>"; ?>
            <?php echo str_replace("\n","<br>",$R_String)."<br><br>"; ?>
            <?php if($action!="END"){ ?>
            <form action="<?php echo $demo_page_url;?>" method="post"><input type="text" class="form-control" autocomplete="off" value="" id="ussd_string" name="ussd_string" autofocus></form>
            <?php }else{ $_SESSION["TransactionId"] = rand(55555,99999); } ?>

      <?php } ?>

      <br>
      <hr>
      <code><?php echo $raw_response; ?></code>

    </div>
  </div>
</body>
</html>
