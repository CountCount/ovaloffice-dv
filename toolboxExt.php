<?php
print 'Die erweiterte Toolbox wird grundlegend ueberarbeitet.';
exit;


include_once 'system.php';
$db = new Database();

$k = htmlspecialchars(strip_tags($_GET['uk']));
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
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<style type="text/css">
* { background: transparent; margin: 0; padding: 0;}
body { position: relative; margin: 0; padding: 0; font-family: "Century Gothic", "Arial", "Trebuchet MS", Verdana, sans-serif; font-size: 10px; width: 420px; height: 420px; background: transparent; overflow: hidden; }
div { padding: 3px; }
.alert { color: #c00; font-weight: bold; fon-variant: small-caps; }
.ok { color: #060; }
img { vertical-align: text-bottom; }
.col { width: 100px; float: left; }
.hideme { display: none; }
h4 { margin-top: 6px; }
table.radar { font-size: 10px; }
.radar input[type=text], #zc { width: 20px; height: 16px; padding: 2px; font-size: 10px; border: 1px solid #ccc; background: #fff; }
td.con { margin: 2px; border: 1px solid #666; text-align: center; padding: 3px; vertical-align: middle; width: 85px; height: 65px;}
#disclaimer { position:absolute; bottom: 3px; left: 3px; width: 414px; }
.hrn { border-radius: 20px 20px 0 0; -moz-border-radius: 20px 20px 0 0; -webkit-border-radius: 20px 20px 0 0;}
.hro { border-radius: 0 20px 20px 0; -moz-border-radius: 0 20px 20px 0; -webkit-border-radius: 0 20px 20px 0;}
.hrs { border-radius: 0 0 20px 20px; -moz-border-radius: 0 0 20px 20px; -webkit-border-radius: 0 0 20px 20px;}
.hrw { border-radius: 20px 0 0 20px; -moz-border-radius: 20px 0 0 20px; -webkit-border-radius: 20px 0 0 20px;}
#hdb-button, #oo-button, .et-tab { width: 104px; text-align: center; font-size: 12px; padding: 3px 8px; border: 1px solid #b37c4a; background: #5c2b20; font-variant: small-caps; font-weight: bold; color: #f0d79e; border-radius: 12px; -moz-border-radius: 12px; -webkit-border-radius: 12px; cursor: pointer; }
#links { position: absolute; right: 3px; top: 3px; width: 111px; }
.radar button { border: 1px solid #333; text-align: center; padding: 3px; vertical-align: middle; width: 85px; height: 65px; background: rgba(0,0,0,.2); border-radius: 20px; -moz-border-radius: 20px; -webkit-border-radius: 20px; margin: 2px; font-size: 11px; cursor: pointer; }
.radar button:hover { background-color: rgba(0,0,238,.2); }
select { font-family: "Century Gothic", "Arial", "Trebuchet MS", Verdana, sans-serif; font-size: 10px; border: 1px solid #ccc; }
#et-tabs { position: relative; }
.et-tab { cursor: pointer; float: left; margin: 3px; text-decoration: none; }
.et-content { clear: left; }
ul#ui-autocomplete { max-height: 200px; background: #fff; cursor: pointer;}
#current-items { min-height: 16px; padding: 8px; background-image: url("http://www.dieverdammten.de/gfx/design/inv_ground.gif"); width: 250px; margin-bottom: 6px; }
.count { font-weight: bold; color: #009; }
.plus a { color: #090; }
.minus a { color: #c00; }
.item { font-size: 14px; float:left; margin-right: 3px; }
.item a {text-decoration: none; background: rgba(0,0,0,.2); border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; line-height: 12px; height: 12px; margin: 1px; }
.item img { border: 1px dotted transparent; }
.item.broken img { border: 1px dotted #f00; }
</style>
<!--[if gte IE 5]>
<style type="text/css"> 
body {
  background-color:#000001;
  filter:Chroma(color=#000001);
}
</style>
<![endif]-->

</head>
<body>
<?php

// error?
$error = $xml->error;
$error_code = (string) $error['code'];
if ( $error_code == 'horde_attacking' ) {
	print '<span class="alert">Zombies attackieren die Stadt.</span>';
	exit;
}
elseif ( $error_code == 'not_in_game' ) {
	print '<span class="alert">Bist Du schon Bürger einer neuen Stadt?</span>';
	exit;
}
elseif ( $error_code == 'user_not_found' ) {
	print '<span class="alert">DV meldet, dass Dein User-XML nicht gefunden wurde.</span>';
	exit;
}
elseif ( $error_code != '' ) {
	print '<span class="alert">Ein unbekannter Fehler ist aufgetreten. Bist Du vielleicht tot?</span>';
	#mail('ovaloffice.dv@googlemail.com', 'OOTX XML '.date('His',time()), '<html><body>'.var_export($error_code, true).'<hr/><pre>'.var_export($xml, true).'</pre></body></html>');
	exit;
}

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
foreach ( (array) $estimations AS $e ) {
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

// user
$data['user'] = array(
		'id' => (int) $owner['id'],
		'name' => (string) $owner['name'],
		'avatar' => (string) $owner['avatar'],
		'x' => (int) $owner['x'],
		'y' => (int) $owner['y'],
		'rx' => (int) $owner['x'] - $data['town']['x'],
		'ry' => (int) $data['town']['y'] - $owner['y'],
		'out' => (int) $owner['out'],
		'ban' => (int) $owner['ban'],
		'hero' => (int) $owner['hero'],
		'job' => (string) $owner['job'],
		'dead' => (int) $owner['dead'],
	);
	
// map, radar
$dvoo = array();
foreach ( $map->children() AS $zdata ) {
	$zx = (int) $zdata['x']; // x
	$zy = (int) $zdata['y']; // y
	$zz = (int) $zdata['z']; // zombies
	$c_zz = (isset($zdata['z']) ? (int) $zdata['z'] : null); // zombies
	$zv = (int) $zdata['nvt']; // visited (bool)
	$zt = (int) $zdata['tag']; // tag
	$zd = (int) $zdata['danger']; // danger
	$data['map'][$zy][$zx] = array('x' => $zx, 'y' => $zy, 'z' => $zz, 'cz' => $c_zz, 'nvt' => $zv, 'tag' => $zt, 'danger' => $zd);
	
	if ( $building = $zdata->building ) {
		$zb = array('name' => (string) $building['name'], 'type' => (int) $building['type'], 'dig' => (int) $building['dig']);
	}
	else {
		$zb = array();
	}
	$data['map'][$zy][$zx]['building'] = $zb;
	// get previously stored values
	
	$ud = array();
	#$prev = $db->query(' SELECT * FROM dvoo_zones WHERE tid = '.$data['town']['id'].' AND day = '.$data['current_day'].' AND x = '.$zx.' AND y = '.$zy.' ');
	if ( $prev ) {
		$dvoo['map'][$zy][$zx] = array(
			'x' => $prev[0]['x'], 
			'y' => $prev[0]['y'], 
			'z' => $prev[0]['z'], 
			'nvt' => $prev[0]['nvt'], 
			'tag' => $prev[0]['tag'],
			'danger' => $prev[0]['danger'],
			'dried' => $prev[0]['dried'],
			'radar_r' => $prev[0]['radar_r'],
			'radar_z' => $prev[0]['radar_z'],
		);
	}	
	
	$ux = $data['user']['x'];
	$uy = $data['user']['y'];
	
	$nx = $ux;
	$ny = $uy - 1;
	
	$ox = $ux + 1;
	$oy = $uy;
	
	$sx = $ux;
	$sy = $uy + 1;
	
	$wx = $ux - 1;
	$wy = $uy;
	
	foreach ( array('n','w','o','s') AS $h ) {
		$vx = $h . 'x';
		$vy = $h . 'y';
		#$prev = $db->query(' SELECT * FROM dvoo_zones_zones WHERE tid = '.$data['town']['id'].' AND x = '.$$vx.' AND y = '.$$vy.' ORDER BY day DESC LIMIT 1 ');
		if ( $prev ) {
			$dvoo['map'][$$vy][$$vx] = array(
				'x' => $prev[0]['x'], 
				'y' => $prev[0]['y'], 
				'z' => $prev[0]['z'], 
				'nvt' => $prev[0]['nvt'], 
				'tag' => $prev[0]['tag'],
				'danger' => $prev[0]['danger'],
				'dried' => $prev[0]['dried'],
				'radar_r' => $prev[0]['radar_r'],
				'radar_z' => $prev[0]['radar_z'],
			);
		}
	}
	
}

// items for autocomplete
$aitems = array();
$q = ' SELECT iname AS name FROM dvoo_items ORDER BY iname ASC ';
$r = $db->query($q);
foreach ( $r AS $o ) {
	$aitems[] = $o['name'];
}

// zone items
#$q = ' SELECT info FROM dvoo_zones WHERE tid = '.$data['town']['id'].' AND day = '.$data['current_day'].' AND x = '.$ux.' AND y = '.$uy.' ';
$r = $db->query($q);
$info = unserialize($r[0][0]);
if ( isset($info['items']) ) {
	$items = $info['items'];
}
else {
	$items = array();
}

?>
<div id="links">
<form id="#oo_form" method="POST" action="http://dv.sindevel.com/oo/" target="_blank"><input type="hidden" name="key" value="<?php print $k; ?>" /><input type="hidden" name="ref" value="ootx" /><input id="oo-button" type="submit" value="Oval Office" /></form><br/>
<form id="#hdb_form" method="POST" action="http://dv-hdb.cwsurf.de" target="_new"><input type="hidden" name="u" value="<?php print $data['user']['id']; ?>" /><input type="hidden" name="n" value="<?php print $data['user']['name']; ?>" /><input type="hidden" name="t" value="<?php print $data['town']['id']; ?>" /><input type="hidden" name="a" value="<?php print $data['user']['avatar']; ?>" /><input id="hdb-button" type="submit" value="Hau den Bürger!" /></form>	

<form id="#map_form" method="POST" action="http://dv.sindevel.com/oo/upd.php" target="_blank"><input type="hidden" name="key" value="<?php print $k; ?>" /><input class="hideme" id="oo-button" type="submit" value="Kartenupdate" /></form><br/>

</div>
<p id="disclaimer" class="alert">Achtung: Die erweiterte Toolbox befindet sich noch im Aufbau und ist noch nicht funktionsfähig. Es ist auch noch nicht abschließend klar, ob die Funktionen überhaupt wie geplant funktionieren werden. Vorschläge & Kritik gern im Oval Office.</p>
<div id="et-tabs"><a id="et-radar-link" class="et-tab" href="#et-radar">Heldenradar</a><a id="et-items-link" class="et-tab" href="#et-items">Gegenstände</a></div>
<div id="et-radar" class="et-content">
<form id="hero-radar">
<table border="0" class="radar">
	<tr>
		<td></td>
		<td class="con hrn">
			<strong>Norden</strong><br/>
			<input type="checkbox" id="nr" name="n_regenerated" value="1" <?php print (isset($dvoo['map'][$ny][$nx]) && !is_null($dvoo['map'][$ny][$nx]['dried']) ? ($dvoo['map'][$ny][$nx]['dried'] == 0 ? 'checked="checked"' : '') : (isset($dvoo['map'][$ny][$nx]) && !is_null($dvoo['map'][$ny][$nx]['radar_r']) ? ($dvoo['map'][$ny][$nx]['radar_r'] == 1 ? 'checked="checked"' : '') : '')); ?> /> regeneriert<br/>
			<input type="text" id="nz" name="n_zombies" value="<?php print (isset($data['map'][$ny][$nx]) && !is_null($data['map'][$ny][$nx]['cz']) ? $data['map'][$ny][$nx]['cz'] : (isset($dvoo['map'][$ny][$nx]) && !is_null($dvoo['map'][$ny][$nx]['z']) ? $dvoo['map'][$ny][$nx]['z'] : '')); ?>" /> Zombies
		</td>
		<td></td>
	</tr>
	<tr>
		<td class="con hrw">
			<strong>Westen</strong><br/>
			<input type="checkbox" id="wr" name="w_regenerated" value="1" <?php print (isset($dvoo['map'][$wy][$wx]) && !is_null($dvoo['map'][$wy][$wx]['dried']) ? ($dvoo['map'][$wy][$wx]['dried'] == 0 ? 'checked="checked"' : '') : (isset($dvoo['map'][$wy][$wx]) && !is_null($dvoo['map'][$wy][$wx]['radar_r']) ? ($dvoo['map'][$wy][$wx]['radar_r'] == 1 ? 'checked="checked"' : '') : '')); ?> /> regeneriert<br/>
			<input type="text" id="wz" name="w_zombies" value="<?php print (isset($data['map'][$wy][$wx]) && !is_null($data['map'][$wy][$wx]['cz']) ? $data['map'][$wy][$wx]['cz'] : (isset($dvoo['map'][$wy][$wx]) && !is_null($dvoo['map'][$wy][$wx]['z']) ? $dvoo['map'][$wy][$wx]['z'] : '')); ?>" /> Zombies
		</td>
		<td class="con">
			<strong>Dein Standort</strong><br/>
			X <input type="text" id="cx" name="current_x" value="<?php print (isset($data['user']['rx']) ? $data['user']['rx'] : 0); ?>" /><br/>
			Y <input type="text" id="cy" name="current_y" value="<?php print (isset($data['user']['ry']) ? $data['user']['ry'] : 0); ?>" />
		</td>
		<td class="con hro">
			<strong>Osten</strong><br/>
			<input type="checkbox" id="or" name="o_regenerated" value="1" <?php print (isset($dvoo['map'][$oy][$ox]) && !is_null($dvoo['map'][$oy][$ox]['dried']) ? ($dvoo['map'][$oy][$ox]['dried'] == 0 ? 'checked="checked"' : '') : (isset($dvoo['map'][$oy][$ox]) && !is_null($dvoo['map'][$oy][$ox]['radar_r']) ? ($dvoo['map'][$oy][$ox]['radar_r'] == 1 ? 'checked="checked"' : '') : '')); ?> /> regeneriert<br/>
			<input type="text" id="oz" name="o_zombies" value="<?php print (isset($data['map'][$oy][$ox]) && !is_null($data['map'][$oy][$ox]['cz']) ? $data['map'][$oy][$ox]['cz'] : (isset($dvoo['map'][$oy][$ox]) && !is_null($dvoo['map'][$oy][$ox]['z']) ? $dvoo['map'][$oy][$ox]['z'] : '')); ?>" /> Zombies
		</td>
	</tr>
	<tr>
		<td><button onclick="updateRadar();return false;">Radar-Info übertragen</button></td>
		<td class="con hrs">
			<strong>Süden</strong><br/>
			<input type="checkbox" id="sr" name="s_regenerated" value="1" <?php print (isset($dvoo['map'][$sy][$sx]) && !is_null($dvoo['map'][$sy][$sx]['dried']) ? ($dvoo['map'][$sy][$sx]['dried'] == 0 ? 'checked="checked"' : '') : (isset($dvoo['map'][$sy][$sx]) && !is_null($dvoo['map'][$sy][$sx]['radar_r']) ? ($dvoo['map'][$sy][$sx]['radar_r'] == 1 ? 'checked="checked"' : '') : '')); ?> /> regeneriert<br/>
			<input type="text" id="sz" name="s_zombies" value="<?php print (isset($data['map'][$sy][$sx]) && !is_null($data['map'][$sy][$sx]['cz']) ? $data['map'][$sy][$sx]['cz'] : (isset($dvoo['map'][$sy][$sx]) && !is_null($dvoo['map'][$sy][$sx]['z']) ? $dvoo['map'][$sy][$sx]['z'] : '')); ?>" /> Zombies
		</td>
		<td><button onclick="updateMap();return false;">Karte aktualisieren</button></td>
	</tr>
</table>
<h4>Statistische Erfassung für Aufklärer</h4>
<p>Wird mit Aktualisierung des Heldenradars übertragen.</p>
<select id="ze" name="zone_exploration_level">
<option value="0">Kaum erforschte Zone</option>
<option value="1">Die Zone wurde teilweise erforscht</option>
<option value="2">Die Zone wurde erforscht</option>
<option value="3">Die Zone wurde voll und ganz erforscht und kartographiert</option></select><br/>
<input type="text" id="zc" name="zombie_count" value="<?php print (isset($data['map'][$cy][$cx]) && !is_null($data['map'][$cy][$cx]['cz']) ? $data['map'][$cy][$cx]['cz'] : (isset($dvoo['map'][$cy][$cx]) && !is_null($dvoo['map'][$cy][$cx]['z']) ? $dvoo['map'][$cy][$cx]['z'] : '0')); ?>" /> Zombies<br/>
<input type="checkbox" id="gc" name="got_caught" value="1" /> Tarnung aufgeflogen?
</form>
<p><strong>Information:</strong> Die Übertragung der Daten aus dem Heldenradar wurde überarbeitet und sollte nun zuverlässiger funktionieren. Fehler bitte im OO melden.</p>
</div>
<div id="et-items" class="et-content hideme">
	<form id="add-item" onsubmit="addItem();return false;">
	Informationen zur Zone [<?php print (isset($data['user']['rx']) ? $data['user']['rx'] : 0); ?>|<?php print (isset($data['user']['ry']) ? $data['user']['ry'] : 0); ?>]
	<div id="current-zombies"><input type="text" maxlength="2" name="z" value="<?php print (isset($data['map'][$cy][$cx]) && !is_null($data['map'][$cy][$cx]['cz']) ? $data['map'][$cy][$cx]['cz'] : (isset($dvoo['map'][$cy][$cx]) && !is_null($dvoo['map'][$cy][$cx]['z']) ? $dvoo['map'][$cy][$cx]['z'] : '0')); ?>" /> Zombies</div>
	<div id="current-items">
	<?php
	$out = '';
		
		foreach ( $items AS $item ) {
			$bis = $db->query(' SELECT i.iid AS id, i.iimg AS img, i.iname AS name, i.icat AS cat FROM dvoo_items i WHERE i.iid = '.$item['id'].' ');
			$bh = $bis[0];
			$out .= '<div class="item '.($item['broken'] ? ' broken' : '').'"><span class="minus"><a href="javascript:changeItem(-1, '.$item['id'].');">x</a></span>&nbsp;<img src="'.t('GAME_ICON_SERVER').$bh['img'].'.gif" title="'.$bh['name'].'" />&nbsp;<span class="minus"><a href="javascript:changeItem('.($item['count'] - 1).', '.$item['id'].');">-</a></span><span class="count">'.$item['count'].'</span><span class="plus"><a href="javascript:changeItem('.($item['count'] + 1).', '.$item['id'].');">+</a></span></div>';
		}
		
		print $out.'<br style="clear:left;" />';
	?>
	</div>
	
		<input type="hidden" name="formid" value="et-add-item" />
		<input type="hidden" name="t" value="<?php print $data['town']['id']; ?>" />
		<input type="hidden" name="u" value="<?php print $data['user']['id']; ?>" />
		<input type="hidden" name="d" value="<?php print $data['current_day']; ?>" />
		<input type="hidden" name="x" value="<?php print $ux; ?>" />
		<input type="hidden" name="y" value="<?php print $uy; ?>" />
		<input name="item-name" id="item-name" type="text" /><input type="submit" value="<?php print t('ADD'); ?>">
	</form>
</div>
<script type="text/javascript">
<!--//

function ajaxRequest(theURL, sendString, callbackFunction) {
	var thisRequestObject;

	thisRequestObject = initiateRequest();
	thisRequestObject.onreadystatechange = processRequest;

	function initiateRequest() {
		if (window.XMLHttpRequest)
			return new XMLHttpRequest();
		elseif (window.ActiveXObject)
			return new ActiveXObject("Microsoft.XMLHTTP");
	}

	function processRequest() {
		if (thisRequestObject.readyState == 4) {
			if (thisRequestObject.status == 200) {
				if (callbackFunction)
					callbackFunction(thisRequestObject, sendString);
			}
			else
				alert("Fehler: (" + thisRequestObject.status + ") " + thisRequestObject.statusText);
		}
	}

	this.sendGetData = function() {
		if (theURL) {
			thisRequestObject.open("GET", theURL, true);
			thisRequestObject.send(sendString);
		}
	}

	this.sendPostData = function() {
		if (theURL) {
			thisRequestObject.open("POST", theURL, true);
			thisRequestObject.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			thisRequestObject.send(sendString);
		}
	}
}

function updateMap() {
	var sendData = new ajaxRequest('http://dv.sindevel.com/oo/upd.php?key=<?php print $k; ?>', 'request=true', showReceived);
	sendData.sendPostData();
}

function updateRadar() {
	if ( document.getElementById('nr').checked ) { var nr = 1; } else { var nr = 0; }
	if ( document.getElementById('wr').checked ) { var wr = 1; } else { var wr = 0; }
	if ( document.getElementById('sr').checked ) { var sr = 1; } else { var sr = 0; }
	if ( document.getElementById('or').checked ) { var or = 1; } else { var or = 0; }
	
	if ( document.getElementById('nz').value == '' ) { var nz = -1; } else { var nz = document.getElementById('nz').value; }
	if ( document.getElementById('wz').value == '' ) { var wz = -1; } else { var wz = document.getElementById('wz').value; }
	if ( document.getElementById('sz').value == '' ) { var sz = -1; } else { var sz = document.getElementById('sz').value; }
	if ( document.getElementById('oz').value == '' ) { var oz = -1; } else { var oz = document.getElementById('oz').value; }
	
	if ( document.getElementById('gc').checked ) { var gc = 1; } else { var gc = 0; }
	var ze = document.getElementById('ze').value;
	if ( document.getElementById('zc').value == '' ) { var zc = 0; } else { var zc = document.getElementById('zc').value; }
	
	var sendData = new ajaxRequest('http://dv.sindevel.com/oo/rdr.php?key=<?php print $k; ?>&nr='+nr+'&nz='+nz+'&wr='+wr+'&wz='+wz+'&or='+or+'&oz='+oz+'&sr='+sr+'&sz='+sz+'&cx='+document.getElementById('cx').value+'&cy='+document.getElementById('cy').value+'&gc='+gc+'&ze='+ze+'&zc='+zc, 'request=true', showReceived);
	sendData.sendPostData();
}

function showReceived(returnData) {
	//if ( alert(returnData.responseText) ) { alert(':)'); }
	alert(returnData.responseText);
	document.write('Loading...');
	location.reload(true);
}

var et_cur = "#et-radar";
$("#et-tabs a").click(function (e) { 
	e.preventDefault();
	var newEt = $(this).attr("href");
	$(et_cur).fadeOut();
	$(newEt).fadeIn("slow");
	et_cur = newEt;
});

$(function() {
		var availableItems = [ <?php print '"'.implode('","', $aitems).'"'; ?>	];
		$( "#item-name" ).autocomplete({
			source: availableItems,
			minLength: 3
		});
	});

function addItem() {  
	$("#ai-button").hide();
	$("#current-items").html("<div class=\'loading\'></div>");
	var ai = $.post(  
		"toolbox.ajax.php",  
		$("#add-item").serialize(),  
		function(data){  
			//$("#").val("");
			$("#current-items").html(data);
			$("#ai-button").fadeIn(500);
		}  
	);
}
function changeItem(a,i) {  
	var ci = $.post(  
		"toolbox.ajax.php",  
		$("#add-item").serialize()+"&formid=et-change-item&a="+a+"&i="+i,  
		function(data){  
			//$("#").val("");
			$("#current-items").html(data);
		}  
	);
}

//-->
</script>
</body>
</html>

<?
// -- Piwik Tracking API init -- 
require_once "PiwikTracker.php";
PiwikTracker::$URL = 'http://sindevel.com/piwik/';

$piwikTracker = new PiwikTracker( $idSite = 2 );
// You can manually set the visitor details (resolution, time, plugins, etc.) 
// See all other ->set* functions available in the PiwikTracker.php file
$piwikTracker->setURL('http://dv.sindevel.com/oo/toolboxExt.php');
$piwikTracker->setCustomVariable(1, 'scode', $k);
$piwikTracker->setCustomVariable(2, 'ip', $_SERVER['REMOTE_ADDR']);
$piwikTracker->setCustomVariable(3, 'query', $_SERVER['QUERY_STRING']);
// Sends Tracker request via http
$piwikTracker->doTrackPageView('OO ToolboxExtended');