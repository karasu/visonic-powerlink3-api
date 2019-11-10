// **********************************************************************
//  BEGINNING OF THE PART TO SPECIALISE
//  DEBUT DE PARTIE A SPECIALISER
// **********************************************************************

// PLEASE READ THE DOC ON GIT BEFORE USING THIS CODE!
// VEUILLEZ LIRE LA DOC SUR GIT avant d'utiliser ce code !


// Technicals informations
// Informations techniques
// -----------------------

// Jeedom host IP - USE ONLEY IP, NO HOST NAME
// IP de Jeedom - METTRE UNE IP OBLIGATOIREMENT, PAS DE HOST NAME
$IP_JEEDOM = 'X.Y.Z.J';

// Alarm host or IP
// Hostname ou Ip de l'alarme
$IP_ALARME = 'X.Y.Z.A';

// Alarme user code: the code you use to arm or disam alarm
// WARNING: This information is stored in clear in your jeedom. Moreover, the alarm use http protocol (no https). This means that this information is transited in clean in the network
// Code utilisateur de l'alarme : le code que vous utilisez pour l'armer ou la désarmer
// ATTENTION : Cette information est stockée en clair dans votre jeedom. De plus, l'alarme utilise un protocole http (pas de https). Cela signifie que cette information sera véhiculée en clair sur le réseau.
$CODE_ALARME = '0000';


// Virtual's informations: names of the vituals informations to set
// Informations sur le virtuel : nom des informations vituelles à renseigner
// -------------------------------------------------------------------------

// Number of errors generated by the secnario
// Nombre d'erreurs générées par l'appel du scénario
$VIRT_ERRORS_NUMBER="#[Alarme][Visonic][nombreErreurs]#";

// Erreur during the scenario execution? 
// Erreur durant l'execution du scenario ?
$VIRT_ERRORS_EXECUTION_SCENARIO="#[Alarme][Visonic][erreurSurDernierAppel]#";

// Battery level
// Niveau de la batterie
$VIRT_BATTERY_LEVEL="#[Alarme][Visonic][batterie]#";

// GSM Level
// Niveau de reception du mobile
$VIRT_GSM_LEVEL="#[Alarme][Visonic][signalGSM]#";

// Alarm is connected or not?
// Alarme connectée ou non ? 
$VIRT_ALARM_CONNECTED="#[Alarme][Visonic][connectee]#";

// AC trouble or not?
// Problème d'alimentation électrique ?
$VIRT_AC_TROUBLE="#[Alarme][Visonic][defautAllimentationSecteur]#";

// Alarm is low battery?
// Battterie faible sur l'alarme ?
$VIRT_LOW_BATTERY="#[Alarme][Visonic][batterieFaible]#";

// Jaming on the alarm?
// Brouillage de l'alarme
$VIRT_JAMING_TROUBLE="#[Alarme][Visonic][brouillage]#";

// Communication problem
// Problème de communication
$VIRT_COMMUNICATION_FAILURE="#[Alarme][Visonic][problemeCommunication]#";

// Array of values for partition armed state
// Tableau de valeures pour l'état armé de la partition
$VIRT_PART_ARMEE__TAB = array("#[Alarme][Visonic][part1Armee]#", "#[Alarme][Visonic][part2Armee]#", "#[Alarme][Visonic][part3Armee]#");

// Array of values for partition alert state
// Tableau de valeures pour l'état alerte de la partition
$VIRT_PART_ALERTE__TAB = array("#[Alarme][Visonic][part1Alerte]#", "#[Alarme][Visonic][part2Alerte]#", "#[Alarme][Visonic][part3Alerte]#");

// Array of values for partition trouble state
// Tableau de valeures pour l'état défaut de la partition
$VIRT_PART_TROUBLE__TAB = array("#[Alarme][Visonic][part1Trouble]#", "#[Alarme][Visonic][part2Trouble]#", "#[Alarme][Visonic][part3Trouble]#");

// Array of values for partition ready state
// Tableau de valeures pour l'état prête de la partition
$VIRT_PART_PRETE__TAB = array("#[Alarme][Visonic][part1Prete]#", "#[Alarme][Visonic][part2Prete]#", "#[Alarme][Visonic][part3Prete]#");

// Array of values for partition fire state
// Tableau de valeures pour l'état alerte incendie de la partition
$VIRT_PART_INCENDIE__TAB = array("#[Alarme][Visonic][part1Incendie]#", "#[Alarme][Visonic][part2Incendie]#", "#[Alarme][Visonic][part3Incendie]#");

// Alarm port - Normaly should not be modified
// Port de l'alarme - Ne devrait pas être nodifé
$PORT_ALARME = '8181';

// **********************************************************************
//  END OF THE PART TO SPECIALISE
//  FIN DE PARTIE A SPECIALISER
// **********************************************************************

cmd::byString($VIRT_ERRORS_EXECUTION_SCENARIO)->event(false);

// Registration du client
// ----------------------
log::add('visonic', 'debug', 'registerClient - lancement...');

$curl = curl_init();
//$curl=prepareJsonRpcCall("PmaxService/registerClient", $curl);


curl_setopt_array($curl, array(
  CURLOPT_PORT => $PORT_ALARME,
  CURLOPT_URL => "http://".$IP_ALARME.":".$PORT_ALARME."/remote/json-rpc",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n\t\"params\": [\"".$IP_JEEDOM."\", ".$CODE_ALARME.", \"user\"],\n\t\"jsonrpc\": \"2.0\",\n\t\"method\": \"PmaxService/registerClient\", \n\t\"id\":1\n}",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err || strpos($response, 'error') !== false) {
  cmd::byString($VIRT_ERRORS_NUMBER)->event(cmd::byString($VIRT_ERRORS_NUMBER)->execCmd()+1);
  cmd::byString($VIRT_ERRORS_EXECUTION_SCENARIO)->event(true);
  //log::add('visonic', 'error', 'registerClient - entrée : '.$curl);
  log::add('visonic', 'error', 'registerClient - réponse : '.$response);
  if ($err != '') {
  	log::add('visonic', 'error', 'registerClient - erreur : '.$err);
  }
  return;
}
else {
  cmd::byString($VIRT_ERRORS_NUMBER)->event(0);
}

log::add('visonic', 'debug', 'registerClient - réponse :'.$response);


// Recup niveau batterie
// ---------------------

log::add('visonic', 'debug', 'getBatteryLevel - lancement...');

$curl = curl_init();
//$curl=prepareJsonRpcCall("PmaxService/getBatteryLevel", curl_init());

curl_setopt_array($curl, array(
  CURLOPT_PORT => $PORT_ALARME,
  CURLOPT_URL => "http://".$IP_ALARME.":".$PORT_ALARME."/remote/json-rpc",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n\t\"params\": null,\n\t\"jsonrpc\": \"2.0\",\n\t\"method\": \"PmaxService/getBatteryLevel\", \n\t\"id\":1\n}",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err || strpos($response, 'error') !== false) {
  cmd::byString($VIRT_ERRORS_NUMBER)->event(cmd::byString($VIRT_ERRORS_NUMBER)->execCmd()+1);
  cmd::byString($VIRT_ERRORS_EXECUTION_SCENARIO)->event(true);
  log::add('visonic', 'error', 'getBatteryLevel - réponse : '.$response);
  if ($err != '') {
    log::add('visonic', 'error', 'getBatteryLevel - erreur : '.$err);
  }
}
else {
  cmd::byString($VIRT_ERRORS_NUMBER)->event(0);
  
  log::add('visonic', 'debug', 'getBatteryLevel - réponse :'.$response);

  $res=json_decode($response);

  // Set value
  cmd::byString($VIRT_BATTERY_LEVEL)->event($res->{'result'});
}



// Recup niveau GSM
// ----------------

log::add('visonic', 'debug', 'getGsmLevel - lancement...');

$curl = curl_init();
//$curl=prepareJsonRpcCall("PmaxService/getGsmLevel", $curl);

curl_setopt_array($curl, array(
  CURLOPT_PORT => $PORT_ALARME,
  CURLOPT_URL => "http://".$IP_ALARME.":".$PORT_ALARME."/remote/json-rpc",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n\t\"params\": null,\n\t\"jsonrpc\": \"2.0\",\n\t\"method\": \"PmaxService/getGsmLevel\", \n\t\"id\":1\n}",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err || strpos($response, 'error') !== false) {
  //log::add('visonic', 'error', 'getGsmLevel - entrée : '.$curl);
  cmd::byString($VIRT_ERRORS_NUMBER)->event(cmd::byString($VIRT_ERRORS_NUMBER)->execCmd()+1);
  cmd::byString($VIRT_ERRORS_EXECUTION_SCENARIO)->event(true);
  log::add('visonic', 'error', 'getGsmLevel - réponse : '.$response);
  if ($err != '') {
    log::add('visonic', 'error', 'getGsmLevel - erreur : '.$err);
  }
}
else {
  cmd::byString($VIRT_ERRORS_NUMBER)->event(0);

  log::add('visonic', 'debug', 'getGsmLevel - réponse :'.$response);

  $res=json_decode($response);

  // Set value
  cmd::byString($VIRT_GSM_LEVEL)->event($res->{'result'});
}




// Recup alarme connectée
// ----------------------

log::add('visonic', 'debug', 'isPanelConnected - lancement...');

$curl = curl_init();
//$curl=prepareJsonRpcCall("PmaxService/isPanelConnected", $curl);

curl_setopt_array($curl, array(
  CURLOPT_PORT => $PORT_ALARME,
  CURLOPT_URL => "http://".$IP_ALARME.":".$PORT_ALARME."/remote/json-rpc",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n\t\"params\": null,\n\t\"jsonrpc\": \"2.0\",\n\t\"method\": \"PmaxService/isPanelConnected\", \n\t\"id\":1\n}",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err || strpos($response, 'error') !== false) {
  //log::add('visonic', 'error', 'isPanelConnected - entrée : '.$curl);
  cmd::byString($VIRT_ERRORS_NUMBER)->event(cmd::byString($VIRT_ERRORS_NUMBER)->execCmd()+1);
  cmd::byString($VIRT_ERRORS_EXECUTION_SCENARIO)->event(true);
  log::add('visonic', 'error', 'isPanelConnected - réponse : '.$response);
  if ($err != '') {
    log::add('visonic', 'error', 'isPanelConnected - erreur : '.$err);
  }
}
else {
  cmd::byString($VIRT_ERRORS_NUMBER)->event(0);

  log::add('visonic', 'debug', 'isPanelConnected - réponse :'.$response);

  $res=json_decode($response);

  // Set value
  cmd::byString($VIRT_ALARM_CONNECTED)->event($res->{'result'});
}




// Recup panel statuses
// --------------------

log::add('visonic', 'debug', 'getPanelStatuses - lancement...');

$curl = curl_init();
//$curl=prepareJsonRpcCall("PmaxService/getPanelStatuses", $curl);

curl_setopt_array($curl, array(
  CURLOPT_PORT => $PORT_ALARME,
  CURLOPT_URL => "http://".$IP_ALARME.":".$PORT_ALARME."/remote/json-rpc",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n\t\"params\": null,\n\t\"jsonrpc\": \"2.0\",\n\t\"method\": \"PmaxService/getPanelStatuses\", \n\t\"id\":1\n}",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err || strpos($response, 'error') !== false) {
  //log::add('visonic', 'error', 'getPanelStatuses - entrée : '.$curl);
  cmd::byString($VIRT_ERRORS_NUMBER)->event(cmd::byString($VIRT_ERRORS_NUMBER)->execCmd()+1);
  cmd::byString($VIRT_ERRORS_EXECUTION_SCENARIO)->event(true);
  log::add('visonic', 'error', 'getPanelStatuses - réponse : '.$response);
  if ($err != '') {
    log::add('visonic', 'error', 'getPanelStatuses - erreur : '.$err);
  }
}
else {
  cmd::byString($VIRT_ERRORS_NUMBER)->event(0);

  log::add('visonic', 'debug', 'getPanelStatuses - réponse :'.$response);

  $res=json_decode($response);

  // Set values
  
  // Alimentation secteur - ok
  cmd::byString($VIRT_AC_TROUBLE)->event($res->{'result'}->{'statuses'}->{'acTrouble'});
  // Batterie faible - ok
  cmd::byString($VIRT_LOW_BATTERY)->event($res->{'result'}->{'statuses'}->{'lowBattery'});
  // Brouillage - ok
  cmd::byString($VIRT_JAMING_TROUBLE)->event($res->{'result'}->{'statuses'}->{'jammingTrouble'});
  // Problème communication
  cmd::byString($VIRT_COMMUNICATION_FAILURE)->event($res->{'result'}->{'statuses'}->{'communicationFailure'});
  
  // Etat de chaque partition
  for ($i=0 ; $i < count($res->{'result'}->{'partitions'}) && $i < 3 ; $i++) {

	  $val = $res->{'result'}->{'partitions'}[$i];
	  $indicePart=$val->{'partition'}-1;
	  
	  if ($val->{'state'} == 'DISARM')
		cmd::byString($VIRT_PART_ARMEE__TAB[$indicePart])->event(false);
	  else
		cmd::byString($VIRT_PART_ARMEE__TAB[$indicePart])->event(true);

	  // Partition - alerte
	  cmd::byString($VIRT_PART_ALERTE__TAB[$indicePart])->event($val->{'statuses'}->{'alertBit'});
	  
	  // Partition - trouble
	  cmd::byString($VIRT_PART_TROUBLE__TAB[$indicePart])->event($val->{'statuses'}->{'troubleBit'});
	  
	  // Partition - prete
	  cmd::byString($VIRT_PART_PRETE__TAB[$indicePart])->event($val->{'statuses'}->{'readyBit'});
	  
	  // Partition incendie
	  cmd::byString($VIRT_PART_INCENDIE__TAB[$indicePart])->event($val->{'statuses'}->{'fireBit'});
  }
    
}
