<?php
include_once 'system.php';
$db = new Database();

// get key (ajax)
$u = (int) $_POST['u'];
$t = (int) $_POST['t'];
$l = (string) $_POST['l'];

$url = 'http://ruine.dvspot.de/script/api/ext_item_request.php';
$fields = array(
	'k' => '11a7fd3d3b79638ed92ca8d5c26b1308',
	'player' => $u,
	'city' => $t,
	'list' => $l,
);

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($fields));

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);

print ($result === true ? 'Einkaufsliste erfolgreich übermittelt' : 'Fehler: '.$result);
#mail('ovaloffice.dv@googlemail.com', 'Ruine', var_export($_POST,true) . ' - ' . http_build_query($fields) . ' - ' . var_export($result,true));

/*
Der Aufruf funktioniert mit folgenden Parametern (via GET oder POST):
k            Dein Identifikationskey, er lautet 11a7fd3d3b79638ed92ca8d5c26b1308
player   Spieler-ID
city        Stadt-ID
list         Gebäude-IDs, durch Kommata getrennt

Ein Aufruf könnte z.B. so aussehen:
http://ruine.dvspot.de/script/api/ext_item_request.php?k=11a7fd3d3b79638ed92ca8d5c26b1308&player=31883&city=6681&list=1010,1011
*/