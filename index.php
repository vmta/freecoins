<!DOCTYPE HTML>

<html>
<head>
    <title>Claim Free UMKoins</title>

    <link rel="icon" href="css/favicon.png" type="image/png" />
    <link rel="stylesheet" href="css/common.css" type="text/css" />
</head>

<body>


<?php

require "include/config.php";

$NET_TYPE = (isset($_POST['network']) && !empty($_POST['network'])) ? $_POST['network'] : "mainnet";
$SITE_URL = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'];

/* Autoload and register any classes not previously loaded */
spl_autoload_register(function ($class_name){
  $classFile = "include/" . $class_name . ".php";
  if( is_file($classFile) && ! class_exists($class_name) )
    include $classFile;
});

/* Create Block object */
$block = new Block($_SERVER_[$NET_TYPE], $_AUTH_[$NET_TYPE]);


$str = '';


/* Test if a wallet has an associated address */
if (empty($block->listreceivedbyaddress()[0]["address"])) {
    if (!empty($block->getnewaddress())) {
        print_r("<p>Successfully created address:<br>" . $block->listreceivedbyaddress()[0]["address"] . "</p>");
    }
}
/* Test if a wallet has positive balance */
if ($block->getbalances()["mine"]["trusted"] < $_REWARD_[$NET_TYPE]) {
    print_r("<p>Deposit UMKoins to address:<br>" . $block->listreceivedbyaddress()[0]["address"] . ".</p>");
    exit();
}


if ($_POST["claim"] == 1) {

  /* Check if given address is valid */
  if ($block->validateaddress($_POST["address"])["isvalid"]) {

    /* Address is valid, proceed with payment routine */

    /*
     * Collect information on payees addresses and time of transactions.
     *
     * Retrieve transactions from blockchain, set an array which would hold
     * addresses as keys and time as values. For each address, get only the
     * time of latest transaction to further check if it is older than the
     * TIME_OUT period.
     */
    $_ADDR_ARRAY = array();
    foreach ($block->listsinceblock()['transactions'] as $transaction) {

      /* If array had no key for an address, set time to 0 */
      if (!isset($_ADDR_ARRAY[$transaction['address']]))
        $_ADDR_ARRAY[$transaction['address']] = 0;

      /*
       * Check if a contained in array value for given key is smaller than
       * that of a transaction and set greater value to such key.
       */
      $_ADDR_ARRAY[$transaction['address']] = ($_ADDR_ARRAY[$transaction['address']] < $transaction['time']) ? $transaction['time'] : $_ADDR_ARRAY[$transaction['address']];
    }

    /*
     * Test if a user's address received a payment in the last TIME_OUT period
     * and let them wait or proceed further.
     */
    if ($_ADDR_ARRAY[$_POST['address']] > time() - $_TIME_OUT_[$NET_TYPE]) {

      /* User already received a payment in TIME_OUT period */
      $_TIME_OUT_REMAINDER = $_ADDR_ARRAY[$_POST['address']] + $_TIME_OUT_[$NET_TYPE] - time();
      $str = "<p>Time till next Free UMKoins claim</p>" .
             "<p class='counter' name='counter' id='counter'>&nbsp;</p>" .
             "<script type='text/javascript' src='js/counter.js'></script>" .
             "<script type='text/javascript'>" .
             "var TIME_OUT = " . $_TIME_OUT_REMAINDER . ";" .
             "var SITE_URL = 'http://" . $SITE_URL . "';" .
             "setInterval(counter, 1000);" .
             "</script>";

    } else {

      /* User is eligible to claim Free UMKoins */
      $tx = $block->sendtoaddress($_POST["address"], $_REWARD_[$NET_TYPE], "Free UMKoins", "Free Claim Player");

      if ($tx) {

        /* Successful transaction, construct the link for client to verify */
        $str = "<p>Check transaction <a target='_blank' href='http://www.umkoin.org/en/blockexplorer.php?net=" . $NET_TYPE . "&txid=" . $tx . "'>here</a>.</p>" .
               "<p class='counter' name='counter' id='counter'>&nbsp;</p>" .
               "<script type='text/javascript' src='js/counter.js'></script>" .
               "<script type='text/javascript'>" .
               "var TIME_OUT = " . $_TIME_OUT_[$NET_TYPE] . ";" .
               "var SITE_URL = 'http://" . $SITE_URL . "';" .
               "setInterval(counter, 1000);" .
               "</script>";

      } else {

        /* Transaction failed */
        $str = "<p>Failed to send " . $_REWARD_[$NET_TYPE] . " UMKoins to address " . $_POST["address"] . "</p>";

      }

    }

  } else {

    /* Address is invalid, inform the client */
    $str = "Address " . $_POST["address"] . " is invalid on " . $NET_TYPE . ".";

  }

} else {

    /* An initial call to the page from client, just display a simple claim request form */
    $str = "<p>Claim Free UMKoins</p>" .
      "<form action='' method='post' autocomplete='on'>" .
      "    <input type='hidden' name='claim' id='claim' value='1'>" .
      "    <p><input type='text' name='address' id='address' placeholder='1FeDNQk5FuNCxJK7un4NhW8hRjpSC99g5t' size='34'></p>" .
      "    <p><select name='network' id='network'>" .
      "        <option value='mainnet' selected>Mainnet</option>" .
      "        <option value='testnet'>Testnet</option>" .
      "    </select>" .
      "    <button type='submit'>Claim</button></p>" .
      "</form>";

}

/* FINALLY display the constructed HTML code */
print_r($str);

?>

</body>
</html>
