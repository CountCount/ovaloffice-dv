<?php
$v = explode(".","3.15");
include_once 'system.php';
$db = new Database();

$k = htmlspecialchars(strip_tags($_GET['uk']));
$utd = true;
$sv = explode(".",$_GET['v']);
if ( count($sv) != 2 || (int) ($sv[0].$sv[1]) < (int) ($v[0].$v[1]) ) {
	$utd = false;
}
#$status = simplexml_load_file('http://www.dieverdammten.de/xml/status');

// secure site key (dv.sindevel.com)
$siteKey = 'ad5f690eec8ab50665fd5343c263abaa';
// ingame Link
$xml = @simplexml_load_file('http://www.dieverdammten.de/xml?k=' .$k . ';sk=' . $siteKey);

if ( !$xml ) {
//todo: error
print 'error';
return 'error';
}

?>
<html><head><title>OO Toolbox</title>
<style type="text/css">
* { background: transparent; margin: 0; padding: 0;}
body { margin: 0; padding: 0; font-family: "Century Gothic", "Arial", "Trebuchet MS", Verdana, sans-serif; font-size: 12px; width: 420px; height: 100px; background: transparent; overflow: hidden; color: #F0D88D; text-shadow: 1px 1px 0 #704018; }
div { padding: 3px; }
.alert { color: #c00; font-weight: bold; font-variant: small-caps; text-shadow: 1px 1px 0 #F0D88D, 0px 0px 2px #ff0; }
.ok { color: #060; font-weight: bold; }
img { vertical-align: text-bottom; }
.col { width: 80px; float: left; }
.col1 { width: 130px; }
.hideme { display: none; }
.update { position: absolute; width: 400px; background: rgba(204,0,0,.5); color: #fff; padding: 1px 3px; font-size: 10px; top: 83px; }
#hdb-button { display: none!important; font-size: 12px; padding: 3px 8px; border: 1px solid #b37c4a; background: #5c2b20; font-variant: small-caps; font-weight: bold; color: #f0d79e; border-radius: 12px; -moz-border-radius: 12px; -webkit-border-radius: 12px; cursor: pointer; }
.postamt { width: 95px; padding-top: 24px; background: transparent url('css/img/fl_mail.png') center top no-repeat; text-align: center; font-size: 1em; }
.postamt.alert { background-image: url('css/img/fl_mail_alert.gif'); }
.update-oo { color: #ff0; font-weight: bold; }
</style>
<!--[if gte IE 5]>
<style type="text/css"> 
body {
  background-color:#000001;
  filter:Chroma(color=#000001);
}
</style>
<![endif]-->
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
</head>
<body>
<?php

// get main objects p=1
$headers = $xml->headers;
$game = $xml->headers->game;
$city = $xml->data->city;
$map = $xml->data->map;
$citizens = $xml->data->citizens;
$cadavers = $xml->data->cadavers;
$expeditions = $xml->data->expeditions;
$bank = $xml->data->bank;
$estimations = $xml->data->estimations->e;
$upgrades = $xml->data->upgrades;
$news = $xml->data->city->news;
$defense = $xml->data->city->defense;
$buildings = $xml->data->city->building;
$owner = $xml->headers->owner->citizen;
$myzone = $xml->headers->owner->myZone;

if ( $utd === false ) { print '<div class="update"><strong>Achtung:</strong> Version '.implode(".",$v).' der Toolbox ist verfügbar! (Du hast v'.implode(".",$sv).'.) &gt;&gt;<a class="update-oo" href="http://dieverdammten.net/ovaloffice/js/ext/oo_toolbox.user.js">Download</a></div>'; }

$error = $xml->error;
$error_code = (string) $error['code'];
if ( $error_code != '' ) {

	// mail
	$q = ' SELECT COUNT(*) FROM dvoo_fl_mailbox WHERE receiver = '.((int) $owner['id']).' AND `read` IS NULL ';
	$r = $db->query($q);
	$newmail = $r[0][0];
	$q = ' SELECT COUNT(*) FROM dvoo_fl_invite WHERE b = '.((int) $owner['id']).' ';
	$r = $db->query($q);
	$newinv = $r[0][0];
	
	print '<div class="col postamt'.($newmail + $newinv > 0 ? ' alert' : '').'">';
	print '<span class="'.($newmail > 0 ? 'alert' : '').'">Nachrichten: '.$newmail.'</span>';
	if ( $newinv > 0 ) {
		print '<br/><span class="'.($newinv > 0 ? 'alert' : '').'">Einladungen: '.$newinv.'</span>';
	}
	print '</div>';

	if ( $error_code == 'horde_attacking' ) {
		print '<span class="alert">Zombies attackieren die Stadt.</span>';
	}
	elseif ( $error_code == 'not_in_game' ) {
		print '<span class="alert">Bist Du schon Bürger einer neuen Stadt?</span>';
		/*$user = $db->query(' SELECT id, name, avatar FROM dvoo_citizens WHERE scode = "'.$k.'" ');
		if ( $user ) {
			$uid = $user[0][0];
			$name = $user[0][1];
			$avatar = $user[0][2];
			//print '<br/><form id="#hdb_form" method="POST" action="http://dv-hdb.cwsurf.de" target="_new"><input type="hidden" name="u" value="'.$uid.'" /><input type="hidden" name="n" value="'.$name.'" /><input type="hidden" name="t" value="0" /><input type="hidden" name="a" value="'.$avatar.'" /><input id="hdb-button" type="submit" value="Hau den Bürger!" /></form>';
		}*/
	}
	elseif ( $error_code == 'user_not_found' ) {
		print '<span class="alert">DV meldet, dass Dein User-XML nicht gefunden wurde.</span>';
	}
	elseif ( $error_code != '' ) {
		print '<span class="alert">Ein unbekannter Fehler ist aufgetreten. Bist Du vielleicht tot?</span>';
	}

	exit;
}

// current data array
$data = array();

// system
$data['system']['icon_url'] = (string) $headers['iconurl'];
$data['system']['avatar_url'] = (string) $headers['avatarurl'];

// current day
$data['current_day'] = (int) $game['days'];

// map size
$data['map']['height'] = (int) $map['hei'];
$data['map']['width'] = (int) $map['wid'];

// town data
$data['town']['id'] = (int) $game['id'];
$data['town']['name'] = (string) $city['city'];
$data['town']['x'] = (int) $city['x'];
$data['town']['y'] = (int) $city['y'];
$data['town']['door'] = (int) $city['door'];
$data['town']['water'] = (int) $city['water'];
$data['town']['chaos'] = (int) $city['chaos'];
$data['town']['devast'] = (int) $city['devast'];
$data['defense']['total'] = (int) $defense['total'];

// estimations
if ( isset($estimations) && !is_null($estimations) ) {
	foreach ( $estimations AS $e ) {
		$eday = (int) $e['day'];
		$emin = (int) $e['min'];
		$emax = (int) $e['max'];
		$ebest = (int) $e['maxed'];
		$data['estimations'][$eday] = array(
			'min' => $emin,
			'max' => $emax,
			'best' => $ebest,
		);
	}
}


$door = '<div class="door"><img title="Stadttor" src="http://data.dieverdammten.de/gfx/icons/small_door_closed.gif"> ';
if ( $data['town']['door'] == 1 ) {
	$door .= '<span class="alert">Offen! <img src="http://www.dieverdammten.de/gfx/forum/smiley/h_warning.gif" title="Das Tor muss zu!" /></span>';
} 
elseif ( $data['town']['door'] == 0 ) {
	$door .= '<span class="ok">Geschlossen!</span>';
}
$door .= '</div>';

$defense = '<div class="defense"><img title="Stadtverteidigung" src="http://data.dieverdammten.de/gfx/icons/item_shield_mt.gif"> <span class="'.(isset($data['estimations'][$data['current_day']]['min']) ? ($data['estimations'][$data['current_day']]['max'] > $data['defense']['total'] ? 'alert' : 'ok') : '').'">'.$data['defense']['total'].'</span></div>';
$attack = '<div class="attack"><img title="Angriffsabschätzung heute" src="http://www.dieverdammten.de/gfx/forum/smiley/h_death.gif"> '.(isset($data['estimations'][$data['current_day']]['min']) && $data['estimations'][$data['current_day']]['min'] > 0 ? $data['estimations'][$data['current_day']]['min'].' - '.$data['estimations'][$data['current_day']]['max'] : '???').($data['estimations'][$data['current_day']]['best'] == 0 ? ' <img src="http://www.dieverdammten.de/gfx/forum/smiley/h_warning.gif" title="Noch nicht die bestmögliche Abschätzung" />' : '').'</div>';

$tomorrow = $data['current_day'] + 1;

if ( isset($data['estimations'][$tomorrow]['min']) && $data['estimations'][$tomorrow]['min'] > 0 ) {
	$attack .= '<div class="attack"><img title="Angriffsabschätzung morgen" src="http://www.dieverdammten.de/gfx/forum/smiley/h_death.gif"> '.$data['estimations'][$tomorrow]['min'].' - '.$data['estimations'][$tomorrow]['max'].($data['estimations'][$tomorrow]['best'] == 0 ? ' <img src="http://www.dieverdammten.de/gfx/forum/smiley/h_warning.gif" title="Noch nicht die bestmögliche Abschätzung" />' : '').'</div>';
}

$ct = 0; // total
$co = 0; // out
$cd = 0; // door
$ch = 0; // hero
$cb = 0; // ban

foreach ( $citizens->children() AS $ca ) {
	$ct++;
	if ( (int) $ca['out'] == 1 ) { $co++; }
	if ( (int) $ca['out'] == 1 && $data['town']['x'] == (int) $ca['x'] && $data['town']['y'] == (int) $ca['y'] ) { $cd++; }
	if ( (int) $ca['ban'] == 1 ) { $cb++; }
	if ( (int) $ca['hero'] == 1 ) { $ch++; }
}
$citizen = '<div class="citizen"><img title="Bürger insgesamt" src="http://www.dieverdammten.de/gfx/forum/smiley/h_human.gif"> '.$ct.'</div><div><img title="Bürger draußen" src="http://www.dieverdammten.de/gfx/forum/smiley/h_camp.gif"> '.$co.'</div><div><img title="Bürger am Stadttor" src="http://data.dieverdammten.de/gfx/icons/small_door_closed.gif"> '.$cd.'</div>';

$water = '<div class="water"><img title="Wasser im Brunnen" src="http://www.dieverdammten.de/gfx/forum/smiley/h_well.gif" /> '.$data['town']['water'].'</div>';

$bdef = 0;
$bwat = 0;
foreach ( $bank->children() AS $bia ) {
	$bi_name = (string) $bia['name'];
	$bi_count = (int) $bia['count'];
	$bi_id = (int) $bia['id'];
	$bi_cat = (string) $bia['cat'];
	$bi_img = (string) $bia['img'];
	$bi_broken = (int) $bia['broken'];

	if ( $bi_cat == 'Armor' ) { $bdef += $bi_count; }
	if ( $bi_id == 1 ) { $bwat = $bi_count; }

}
$water .= '<div class="water"><img title="Wasser in der Bank" src="http://www.dieverdammten.de/gfx/forum/smiley/h_water.gif" /> '.$bwat.'</div>';
$water .= '<div class="water"><img title="Verteidigungsgegenstände" src="http://www.dieverdammten.de/gfx/forum/smiley/h_guard.gif" /> '.$bdef.'</div>';

// mail
$q = ' SELECT COUNT(*) FROM dvoo_fl_mailbox WHERE receiver = '.((int) $owner['id']).' AND `read` IS NULL ';
$r = $db->query($q);
$newmail = $r[0][0];
$q = ' SELECT COUNT(*) FROM dvoo_fl_invite WHERE b = '.((int) $owner['id']).' ';
$r = $db->query($q);
$newinv = $r[0][0];
?>
<div class="col col1">
<?php
print $door;
print $defense;
print $attack;
?>
</div><div class="col">
<?php
print $citizen;
?>
</div><div class="col">
<?php
print $water;
?>
</div><div class="col postamt <?php print ($newmail + $newinv > 0 ? ' alert' : ''); ?>">
<?php
print '<span class="'.($newmail > 0 ? 'alert' : '').'">Nachrichten: '.$newmail.'</span>';
if ( $newinv > 0 ) {
	print '<br/><span class="'.($newinv > 0 ? 'alert' : '').'">Einladungen: '.$newinv.'</span>';
}
?>
</div>
<hr style="clear:both;" class="hideme" />
</body>
</html>

<?
// -- Piwik Tracking API init -- 
require_once "PiwikTracker.php";
PiwikTracker::$URL = 'http://sindevel.com/piwik/';

$piwikTracker = new PiwikTracker( $idSite = 2 );
// You can manually set the visitor details (resolution, time, plugins, etc.) 
// See all other ->set* functions available in the PiwikTracker.php file
$piwikTracker->setURL('http://dv.sindevel.com/oo/toolbox.php');
$piwikTracker->setCustomVariable(1, 'scode', $k);
$piwikTracker->setCustomVariable(2, 'ip', $_SERVER['REMOTE_ADDR']);
$piwikTracker->setCustomVariable(3, 'query', $_SERVER['QUERY_STRING']);
// Sends Tracker request via http
$piwikTracker->doTrackPageView('OO Toolbox');