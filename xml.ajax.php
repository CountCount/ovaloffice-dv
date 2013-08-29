<?php
include_once 'system.php';
$db = new Database();

// get version number
$v = (int) $_POST['v'];
// get process number
$p = (int) $_POST['p'];

if ( $p == 2 ) {
	$k = htmlspecialchars(strip_tags($_POST['k']));
	$n = '';
	$getxml = (int) $_POST['u'];
}
elseif ( $p == 1 ) {
	$k = htmlspecialchars(strip_tags($_POST['k']));
	$n = htmlspecialchars(strip_tags($_POST['n']));
}
else {
	// no data send -> start
	print '<script type="text/javascript">
			$("#CitizenIdentificationHeader").hide();
			$("#CitizenIdentificationContent").toggleClass("loading");
		</script>';
	print '<div class="status">Bitte identifizieren Sie sich.</div>';
	print '<script type="text/javascript">
		$("#link_auswaertigesamt").remove();
		$("#link_reisebuero").remove();
		$("#link_einwohnermeldeamt").remove();
		$("#link_finanzamt").remove();
		$("#link_statistikamt").remove();
		$("#link_wartebereich").remove();
		$("#mailbox-wrapper").remove();
	</script>';
	include 'start.php';
	exit;
}

if ( $p == 1 ) {
	// external ID
	$xml = simplexml_load_file('http://www.dieverdammten.de/xml?k=' .$k);
	$soul = null;
}
elseif ( $p == 2 ) {

	$todaysstart = mktime(0, 15, 0, date('m'), date('d'), date('Y'));
	$town_xml = 0;
	$soul = NULL;
	$dbu = 0;
	
	$q = ' SELECT r.town_id AS tid, r.citizen_id AS uid FROM dvoo_town_citizens r INNER JOIN dvoo_citizens c ON c.id = r.citizen_id WHERE c.scode = "'.$k.'" ORDER BY r.town_id DESC LIMIT 1 ';
	$r = $db->query($q);
	if ( count($r) > 0 && isset($r[0]['tid']) ) {
		$tid = $r[0]['tid'];
		$uid = $r[0]['uid'];
		$town_xml = 1;
	}
	
	if ( $town_xml == 1 && $getxml == 0 ) {
		// get cached data
		$q = ' SELECT tid, day FROM dvoo_xml WHERE tid = '.$tid.' AND uid = '.$uid.' AND stamp >= '.$todaysstart.' ORDER BY stamp DESC LIMIT 1 ';
		$r = $db->query($q);
		if ( count($r) > 0 && isset($r[0]['xml']) ) {
			$xml_string = file_get_contents('xml/history/'.$r[0]['tid'].'-'.$r[0]['day'].'.xml');
			#$xml_string = $r[0]['xml'];
		}
		else {
			$dbu = 1;
			// retrieve fresh data
			$siteKey = 'ad5f690eec8ab50665fd5343c263abaa';
			
			$xml_string = file_get_contents('http://www.dieverdammten.de/xml?k=' .$k . ';sk=' . $siteKey);
			
			$q = ' SELECT s.xml FROM dvoo_soul s INNER JOIN dvoo_citizens c ON c.id = s.uid WHERE c.scode = "'.$k.'" AND s.stamp >= '.(time() - 3600).' ORDER BY stamp DESC LIMIT 1 ';
			$r = $db->query($q);
			if ( count($r) > 0 && isset($r[0]['xml']) ) {
				$soul_string = $r[0]['xml'];
			}
			else {
				// ingame Link
				$soul_string = file_get_contents('http://www.dieverdammten.de/xml/ghost?k=' .$k . ';sk=' . $siteKey);
			}
			$soul = simplexml_load_string($soul_string);
		}
	}
	else {
		$dbu = 1;
		// retrieve fresh data
		$siteKey = 'ad5f690eec8ab50665fd5343c263abaa';
		
		$xml_string = file_get_contents('http://www.dieverdammten.de/xml?k=' .$k . ';sk=' . $siteKey);
		
		$q = ' SELECT s.xml FROM dvoo_soul s INNER JOIN dvoo_citizens c ON c.id = s.uid WHERE c.scode = "'.$k.'" AND s.stamp >= '.(time() - 3600).' ORDER BY stamp DESC LIMIT 1 ';
		$r = $db->query($q);
		if ( count($r) > 0 && isset($r[0]['xml']) ) {
			$soul_string = $r[0]['xml'];
		}
		else {
			// ingame Link
			$soul_string = file_get_contents('http://www.dieverdammten.de/xml/ghost?k=' .$k . ';sk=' . $siteKey);
		}
		$soul = simplexml_load_string($soul_string);
	}
	
	$xml = simplexml_load_string($xml_string);
}
if ( !$xml ) {
	print '<script type="text/javascript">
			$("#CitizenIdentificationHeader").hide();
			$("#CitizenIdentificationContent").toggleClass("loading");
		</script>';
	print '<div class="error">XML error</div>';
	print '<script type="text/javascript">
	$.each(["auswaertigesamt", "finanzamt", "einwohnermeldeamt", "link_auswaertigesamt", "link_finanzamt", "link_einwohnermeldeamt", "mailbox-wrapper"], function(index,value) {
		$("#" + value).remove();
		});
	</script>';
	exit;
}

$error = $xml->error;
$error_code = (string) $error['code'];
if ( $error_code == 'horde_attacking' ) {
	print '<script type="text/javascript">
			$("#CitizenIdentificationHeader").hide();
			$("#CitizenIdentificationContent").toggleClass("loading");
		</script>';
	print '<div class="error">Zombies attackieren die Stadt. Alle Türen und Fenster sind verriegelt. Durch ein Fenster erkennst Du die Pinnwand. Darauf sind die letzten verfügbaren Daten von gestern zu sehen.</div>';
	print '<script type="text/javascript">
	$.each(["office2", "office3", "office4", "office5", "office7", "link_office2", "link_office3", "link_office4", "link_office5", "link_office7"], function(index,value) {
		$("#" + value).remove();
		});
	</script>';
	exit;
}
elseif ( $error_code == 'not_in_game' ) {
	$user = $db->query(' SELECT id, name, avatar FROM dvoo_citizens WHERE scode = "'.$k.'" ');
	if ( $user ) {
		$uid = $user[0][0];
		$name = $user[0][1];
		$avatar = $user[0][2];
	}
	print '<script type="text/javascript">
			$("#CitizenIdentificationHeader").hide();
			$("#CitizenIdentificationContent").toggleClass("loading");
		</script>';
	print '<div class="error">Ein Fehler ist aufgetreten. Bist Du schon Bürger einer neuen Stadt?</div>';
	print '<script type="text/javascript">
	$.each(["office2", "office3", "office4", "office5", "office7", "link_office2", "link_office3", "link_office4", "link_office5", "link_office7"], function(index,value) {
		$("#" + value).remove();
		});
		var userid = '.$uid.';
		loadDeadContent('.$uid.');';
	print '</script>';
	exit;
}
elseif ( $error_code != '' ) {
	print '<script type="text/javascript">
			$("#CitizenIdentificationHeader").hide();
			$("#CitizenIdentificationContent").toggleClass("loading");
		</script>';
	print '<div class="error">Ein unbekannter Fehler ist aufgetreten. Bist Du vielleicht tot?</div>';
	print '<script type="text/javascript">
	$.each(["office2", "office3", "office4", "office5", "office7", "link_office2", "link_office3", "link_office4", "link_office5", "link_office7"], function(index,value) {
		$("#" + value).remove();
		});
	</script>';
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

if ( $p == 2 ) {
	$owner = $xml->headers->owner->citizen;
	$myzone = $xml->headers->owner->myZone;
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
$data['town']['hard'] = (int) $city['hard'];


$db->iquery('INSERT INTO dvoo_towns VALUES (
	'.$data['town']['id'].', 
	"'.$data['town']['name'].'", 
	'.$data['town']['hard'].', 
	'.$data['town']['x'].', 
	'.$data['town']['y'].', 
	'.$data['map']['height'].', 
	'.$data['map']['width'].', 
	'.$data['current_day'].', 
	40,
	'.$data['town']['water'].', 
	'.$data['town']['door'].', 
	'.$data['town']['chaos'].', 
	'.$data['town']['devast'].',
	0,
	'.time().'
	) ON DUPLICATE KEY UPDATE 
		hard = '.$data['town']['hard'].', 
		x = '.$data['town']['x'].', 
		y = '.$data['town']['y'].', 
		h = '.$data['map']['height'].', 
		w = '.$data['map']['width'].', 
		day = '.$data['current_day'].', 
		water = '.$data['town']['water'].', 
		door = '.$data['town']['door'].', 
		chaos = '.$data['town']['chaos'].', 
		devast = '.$data['town']['devast'].', 
		stamp = '.time()
);
if ( $data['town']['devast'] == 1 ) {
	$db->iquery('UPDATE dvoo_towns SET devast_on = '.$data['current_day'].' WHERE devast_on = 0 AND id = '.$data['town']['id'].' LIMIT 1');
}

// news
$data['news']['z'] = (int) $news['z'];
$data['news']['v'] = (int) $news['def'];

if ( $data['current_day'] > 1 && $dbu == 1 ) {
	$db->iquery(' INSERT INTO dvoo_stat_zombies VAlUES ('.$data['town']['id'].', '.($data['current_day'] - 1).', '.$data['news']['z'].', '.$data['news']['v'].') ON DUPLICATE KEY UPDATE z = '.$data['news']['z'].', v = '.$data['news']['v']);
}

// defense
$data['defense']['total'] = (int) $defense['total'];

// estimations
if ( !is_null($estimations) ) {
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

// UPDATE persistent data
// map basic data
/* ### map2 ### */
foreach ( $map->children() AS $zdata ) {
	// core xml data
	$zx = (int) $zdata['x']; // x
	$zy = (int) $zdata['y']; // y
	$zv = (int) $zdata['nvt']; // visited (bool)
	
	$zz = (isset($zdata['z']) ? (int) $zdata['z'] : 'NULL'); // zombies
	$zt = (isset($zdata['tag']) ? (int) $zdata['tag'] : 'NULL'); // tag
	$zd = (isset($zdata['danger']) ? (int) $zdata['danger'] : 'NULL'); // danger
	$ds = $zv == 0 ? t('WATCHTOWER') : t('VISION');

	$q = ' INSERT INTO dvoo_zones_zones VALUES ('.$data['town']['id'].', '.$data['current_day'].', '.$zx.', '.$zy.', '.$zv.', '.$zt.', '.$zd.', '.$zz.', '.time().', "'.$ds.'", '.time().') ON DUPLICATE KEY UPDATE nvt = '.$zv.', tag = '.$zt.', danger = '.$zd.', z = '.$zz.', stamp = '.time().' ';
	$db->iquery($q);
	
	// building data
	if ( $building = $zdata->building ) {
		$zb = array('name' => (string) $building['name'], 'type' => (int) $building['type'], 'dig' => (int) $building['dig'], 'content' => (string) $building['content']);
		$q = ' INSERT INTO dvoo_zones_buildings VALUES ('.$data['town']['id'].', '.$zx.', '.$zy.', "'.mysql_real_escape_string($zb['name']).'", '.$zb['type'].', '.$zb['dig'].', 0, "'.mysql_real_escape_string($zb['content']).'", NULL, '.time().' ) ON DUPLICATE KEY UPDATE name = "'.mysql_real_escape_string($zb['name']).'", type = '.$zb['type'].', dig = '.$zb['dig'].', stamp = '.time().' ';
		$db->iquery($q);
		
	}
}

// citizens
foreach ( $citizens->children() AS $ca ) {
	if ( $dbu == 1 ) {
		$cname = '';
		$res = $db->query('SELECT name FROM dvoo_citizens WHERE id = '.(int) $ca['id'].' LIMIT 1');
		if ( isset($res[0][0]) ) {
			$cname = $res[0][0];
		}
		$q = 'INSERT INTO dvoo_citizens VALUES ('.(int) $ca['id'].', "'.(string) $ca['name'].'", "", "", "'.(string) $ca['avatar'].'", "") ON DUPLICATE KEY UPDATE avatar = "'.(string) $ca['avatar'].'"'.($cname != '' && $cname != (string) $ca['name'] ? ', name = "'.(string) $ca['name'].'", oldnames = CONCAT(oldnames, ", ", "'.$cname.'")': '');
		$db->iquery($q);
		#mail('ovaloffice.dv@gmail.com', 'Names', $q);
		$caq = 'INSERT INTO dvoo_town_citizens VALUES (
			'.$data['town']['id'].', 
			'.(int) $ca['id'].', 
			'.(int) $ca['ban'].', 
			'.(int) $ca['hero'].', 
			"'.(string) $ca['job'].'", 
			'.(int) $ca['dead'].', 
			'.(int) $ca['out'].', 
			'.(!isset($ca['x']) ? $data['town']['x'] : (int) $ca['x']).', 
			'.(is_null($ca['y']) ? $data['town']['y'] : (int) $ca['y']).') 
			ON DUPLICATE KEY UPDATE 
				`ban` = '.(int) $ca['ban'].', 
				`hero` = '.(int) $ca['hero'].', 
				`job` = "'.(string) $ca['job'].'", 
				`dead` = '.(int) $ca['dead'].', 
				`out` = '.(int) $ca['out'].', 
				`x` = '.(!isset($ca['x']) ? $data['town']['x'] : (int) $ca['x']).', 
				`y` = '.(is_null($ca['y']) ? $data['town']['y'] : (int) $ca['y']).' 
			';
		$db->iquery($caq);
	}
	
	$data['citizens'][(int) $ca['id']] = array(
		'id' => (int) $ca['id'],
		'name' => (string) $ca['name'],
		'out' => (int) $ca['out'],
		'ban' => (int) $ca['ban'],
		'hero' => (int) $ca['hero'],
		'job' => (string) $ca['job'],
		'dead' => (int) $ca['dead'],
		'avatar' => (string) $ca['avatar'],
		'x' => (!isset($ca['x']) ? $data['town']['x'] : (int) $ca['x']),
		'y' => (is_null($ca['y']) ? $data['town']['y'] : (int) $ca['y']),
		'rx' => (is_null($ca['x']) ? 0 : (int) $ca['x'] - $data['town']['x']),
		'ry' => (is_null($ca['y']) ? 0 : (int) $ca['y'] - $data['town']['y']),
		'msg' => (string) $ca,
		'baseDef' => (int) $ca['baseDef'],
	);
	
	$data['map'][$data['citizens'][(int) $ca['id']]['y']][$data['citizens'][(int) $ca['id']]['x']]['citizens'][(int) $ca['id']] = array('name' => (string) $ca['name'], 'job' => (string) $ca['job']);

}
if ( $dbu == 1 ) {
	$db->iquery('UPDATE dvoo_towns SET citizens = '.count($data['citizens']).' WHERE id = '.$data['town']['id']);
}
// cadaver
$tcadavers = array();

foreach ( $cadavers->children() AS $ca ) {
	
	$tcadavers[(int) $ca['day']][(int) $ca['id']] = array(
		'id' => (int) $ca['id'],
		'name' => (string) $ca['name'],
		'day' => (int) $ca['day'],
		'dtype' => (int) $ca['dtype'],
		'msg' => (string) $ca->msg,
	);
	
	if ( isset($ca->cleanup) ) {
		$cleanup = $ca->cleanup;
		$tcadavers[(int) $ca['day']][(int) $ca['id']]['cleanup_type'] = (string) $cleanup['type'];
		$tcadavers[(int) $ca['day']][(int) $ca['id']]['cleanup_user'] = (string) $cleanup['user'];
	}
	if ( $dbu == 1 ) {
		$caq = 'UPDATE dvoo_town_citizens SET dead = 1 WHERE town_id = '.$data['town']['id'].' AND citizen_id = '.(int) $ca['id'].'; ';
		$db->iquery($caq);
	}
}
krsort($tcadavers);
$data['cadavers'] = array();
foreach ( $tcadavers AS $cad ) {
	$data['cadavers'] = array_merge($data['cadavers'], $cad);
}

if ( $p == 2 ) {
	// owner citizen
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
	); //core data
	#var_dump($data['user']);
	if ( $dbu == 1 ) {
		$db->iquery(' UPDATE dvoo_citizens SET scode = "'.$k.'" WHERE id = '.$data['user']['id']);
		$db->iquery(' UPDATE dvoo_town_citizens SET x = '.$data['user']['x'].' AND y = '.$data['user']['y'].' ');
	}
}

$db->iquery(' INSERT INTO dvoo_login_log VALUES (NULL, '.time().', "'.$_SERVER['REMOTE_ADDR'].'", "'.$_SERVER['HTTP_REFERER'].'", '.$p.', "'.$k.'", "'.((string) $owner['name']).'" ) ');

// my zone
if ( $p == 2 && $data['town']['hard'] == 0 && !($data['user']['rx'] == 0 && $data['user']['ry'] == 0) ) {
	$items = array();
	if ( isset($myzone->item) ) {
		foreach ( $myzone->item AS $item ) {
			$items[] = array(
				'id' => (int) $item['id'],
				'count' => (int) $item['count'],
				'broken' => (int) $item['broken'],
			);
		}
	}
	
	$info['items'] = $items;
	if ( $dbu == 1 ) {
		// update regen radar upon visit
		$q = ' INSERT INTO dvoo_zones_regen VALUES ('.$data['town']['id'].', '.$data['current_day'].', '.((int) $owner['x']).', '.((int) $owner['y']).', '.(1 - ((int) $myzone['dried'])).', '.time().', "'.((string) $owner['name']).'") ON DUPLICATE KEY UPDATE r = '.(1 - ((int) $myzone['dried'])).', `on` = '.time().', `by` = "'.((string) $owner['name']).'" ';
		$db->iquery($q);
		
		/* ### map2 ### */
		if ( count($items) == 0 ) {
			$items = '""';
		}
		else {
			$items = '"'.mysql_real_escape_string(serialize($items)).'"';
		}
		$q = ' INSERT INTO dvoo_zones_visit VALUES ('.$data['town']['id'].', '.$data['current_day'].', '.((int) $owner['x']).', '.((int) $owner['y']).', 1, '.((int) $myzone['dried']).', '.((int) $myzone['z']).', '.$items.', '.time().', "'.((string) $owner['name']).'") ON DUPLICATE KEY UPDATE dried = '.((int) $myzone['dried']).', z = '.((int) $myzone['z']).', items = '.$items.', `on` = '.time().', `by` = "'.((string) $owner['name']).'" ';
		$db->iquery($q);
	}
}

// expeditions
$e = 0;
	
foreach ( $expeditions->children() AS $exp ) {
	$data['expeditions'][$e] = array(
		'creator' => (int) $exp['authorId'],
		'length' => (int) $exp['length'],
		'name' => (string) $exp['name'],
		'route' => array(),
	
	);
	
	$i = 0;
	$ox = $data['town']['x'];
	$oy = $data['town']['y'];
	foreach ( $exp->children() AS $pa ) {
		$px = (int) $pa['x'];
		$py = (int) $pa['y'];
		
		if ( $i != 0 ) {
			if ( abs($px - $ox) > 1 ) {
				if ( $px > $ox ) {
					for ( $tx = ($ox + 1); $tx <= $px; $tx++ ) {
						$data['expeditions'][$e]['route'][$i] = array('x' => $tx, 'y' => $py);
						$i++;
					}
				}
				else {
					for ( $tx = ($ox - 1); $tx >= $px; $tx-- ) {
						$data['expeditions'][$e]['route'][$i] = array('x' => $tx, 'y' => $py);
						$i++;
					}
				}
			}
			elseif ( abs($py -$oy) > 1 ) {
				if ( $py > $oy ) {
					for ( $ty = ($oy + 1); $ty <= $py; $ty++ ) {
						$data['expeditions'][$e]['route'][$i] = array('x' => $px, 'y' => $ty);
						$i++;
					}
				}
				else {
					for ( $ty = ($oy - 1); $ty >= $py; $ty-- ) {
						$data['expeditions'][$e]['route'][$i] = array('x' => $px, 'y' => $ty);
						$i++;
					}
				}
			}
			else {
				$data['expeditions'][$e]['route'][$i] = array('x' => $px, 'y' => $py);
				$i++;
			}
		}
		else {
			$data['expeditions'][$e]['route'][$i] = array('x' => $px, 'y' => $py);
			$i++;
		}	
		$ox = $px;
		$oy = $py;
	}
	if ( $dbu == 1 ) {
		$db->iquery('INSERT IGNORE INTO dvoo_expeditions VALUES (NULL, '.$data['town']['id'].', '.$data['current_day'].', '.$data['expeditions'][$e]['creator'].', '.$data['expeditions'][$e]['length'].', "'.mysql_escape_string($data['expeditions'][$e]['name']).'", "'.mysql_escape_string(serialize($data['expeditions'][$e]['route'])).'")');
	}
	$e++;
}

// buildings
if ( $data['town']['hard'] == 0 && $dbu == 1 ) {
	foreach ( $buildings AS $b ) {
	
		$db->iquery(' INSERT INTO dvoo_buildings VALUES (
			'.((int) $b['id']).',
			1,
			"'.((string) $b['name']).'",
			'.((int) $b['temporary']).',
			"'.((string) $b['img']).'",
			'.(isset($b['parent']) ? (int) $b['parent'] : 0).',
			0,
			0,
			NULL,
			NULL,
			"'.mysql_real_escape_string((string) $b).'"
		) ON DUPLICATE KEY UPDATE active = 1, name = "'.((string) $b['name']).'", temporary = '.((int) $b['temporary']).',
			 img = "'.((string) $b['img']).'", parent = '.(isset($b['parent']) ? (int) $b['parent'] : 0).', `desc` = "'.mysql_real_escape_string((string) $b).'" ');
		
		$db->iquery(' INSERT IGNORE INTO dvoo_town_buildings VALUES (
			'.$data['town']['id'].',
			'.$data['current_day'].',
			'.((int) $b['id']).'
		) ');
	}
}
elseif ( $data['town']['hard'] == 1 ) {
	$db->iquery(' INSERT IGNORE INTO dvoo_town_buildings (SELECT tid, '.$data['current_day'].' AS day, bid FROM dvoo_town_buildings WHERE tid = '.$data['town']['id'].' AND day = '.($data['current_day'] - 1).') ');
}

//bank
if ( $dbu == 1 ) {
	$db->iquery(' DELETE FROM dvoo_bankitems WHERE tid = '.$data['town']['id'].' AND cday = '.$data['current_day']);
}

foreach ( $bank->children() AS $bia ) {
	$bi_name = (string) $bia['name'];
	$bi_count = (int) $bia['count'];
	$bi_id = (int) $bia['id'];
	$bi_cat = (string) $bia['cat'];
	$bi_img = (string) $bia['img'];
	$bi_broken = (int) $bia['broken'];
	
	if ( $dbu == 1 ) {
		$db->iquery('INSERT IGNORE INTO dvoo_items VALUES ('.$bi_id.', "'.$bi_name.'", "'.$bi_img.'", "'.$bi_cat.'")');
		
		$db->iquery('INSERT INTO dvoo_bankitems VALUES ('.$data['town']['id'].', '.$data['current_day'].', '.$bi_id.', '.$bi_count.', '.$bi_broken.') ON DUPLICATE KEY UPDATE icount = '.$bi_count.' ');
	}
	
	$data['bank'][$bi_cat][] = array(
		'id' => $bi_id,
		'name' => $bi_name,
		'count' => $bi_count,
		'category' => $bi_cat,
		'image' => $bi_img,
		'broken' => $bi_broken,
		
	);
}

// data array
$t = time();
$db->iquery(' DELETE FROM dvoo_rawdata WHERE id = '.$data['user']['id'].' AND time < '.($t - 86400).' ');
$db->iquery(' INSERT INTO dvoo_rawdata VALUES ('.$data['user']['id'].', '.$t.', "'.mysql_escape_string(serialize($data)).'") ');

$lastday = mktime(0,5,0,(int) date("n",time()),(int) date("j",time()),(int) date("Y",time()));
$db->iquery('DELETE g.*, c.*  FROM dvoo_groups g LEFT JOIN dvoo_group_citizens c ON c.gid = g.gid WHERE g.stamp < '.$lastday.' AND g.persistent = 0'); 

// return ajax request
print '<script type="text/javascript">
			//$("#CitizenIdentificationHeader").hide();
			$("#CitizenIdentificationContent").toggleClass("loading");
			$("#headtownday").html("'.$data['town']['name'].' :: '.t('DAY').' '.$data['current_day'].'");
			
			loadTabContent('.$data['user']['id'].');
			
			var userid = '.$data['user']['id'].';
		</script>';

if ( $p == 2 ) {
	$sp = 0;
	for ( $i = $data['current_day']; $i > 0; $i-- ) {
		$sp += $i;
	}
	$cp = floor(pow($data['current_day'], 1.5));

	$door = '<div class="door"><img title="Stadttor" src="http://data.dieverdammten.de/gfx/icons/small_door_closed.gif"> ';
	if ( $data['town']['door'] == 1 ) {
		$door .= '<span class="alert">Offen!</span>';
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

	print '';

	$qr = 150;
	print '<div id="ooid">
		<div class="underlay"></div>
		<div class="content">
			<div class="ooid_picture"><img src="'.$data['user']['avatar'].'" /></div>
			<div class="ooid_name">'.$data['user']['name'].'</div>
			<div class="ooid_issue_location">Ausgestellt von <span>'.$data['town']['name'].'</span></div>
			<div class="ooid_issue_time">am <span>'.utf8_encode(strftime("%e. %B %Y", (int) (time() + 86400 - (86400 * $data['current_day'])))).'</span></div>
			<div class="ooid_issue_sp">Aktuelle Seelenpunkte <span>'.$sp.'</span></div>
			<div class="ooid_issue_clean">Aktuelle Cleanpunkte <span>'.$cp.'</span></div>
		</div>
		<div class="ti_underlay"></div>
		<div class="ti_backcontent">
			<div class="ooid_picture"><img src="'.$data['system']['avatar_url'].$data['user']['avatar'].'" /></div>
			<div class="ooid_name">'.$data['user']['name'].'</div>
			<div class="ooid_issue_location">Ausgestellt von <span>'.$data['town']['name'].'</span></div>
			<div class="ooid_issue_time">am <span>'.utf8_encode(strftime("%e. %B %Y", (int) (time() + 86400 - (86400 * $data['current_day'])))).'</span></div>
			<div class="ooid_issue_sp">Aktuelle Seelenpunkte <span>'.$sp.'</span></div>
			<div class="ooid_issue_clean">Aktuelle Cleanpunkte <span>'.$cp.'</span></div>
		</div>
		<div class="ti_content">
			<h3>Stadtinformationsübersicht</h3>
			<div class="col col1">'.$door.$defense.$attack.'</div><div class="col">'.$citizen.'</div><div class="col">'.$water.'</div>
		</div>
	</div>
	<div class="clearfix" style="border: 1px solid #999;
border-left: none;
border-right: none;
padding: 6px;
font-size: 12px;
margin-top: 3em;
background: #EEE;
color: #336;"><img src="http://chart.apis.google.com/chart?chs='.$qr.'x'.$qr.'&cht=qr&chld=L|0&chl='.urlencode('http://dieverdammten.net/ovaloffice/?key='.$k).'" alt="QR code" widht="'.$qr.'" height="'.$qr.'" title="Deine persönlich OO-Url für Dein Smartphone" style="float:right;margin:3px;" />Dein persönlicher Zugangscode lautet <em>'.$k.'</em>. <strong style="color:#633;">HALTE DIESEN CODE STRENG GEHEIM!</strong>.<br/>Ziehe <a style="color:#633;" href="http://dieverdammten.net/ovaloffice/?key='.$k.'">diesen Link</a> in Deine Lesezeichenleiste, um jederzeit auf das Oval Office zuzugreifen ohne Dich vorher bei DV einzuloggen.<br/>Falls Du ein Smartphone besitzt, kannst Du auch nebenstehenden QR Code scannen und bekommst damit Deine persönliche URL für den Zugang zum Oval Office direkt auf Dein Handy. <div class="error" style="clear:none!important;margin-right:160px;"><img src="http://www.dieverdammten.de/gfx/forum/smiley/h_warning.gif" style="vertical-align:text-bottom;" />ACHTUNG: Wenn Du auf Deiner Seelen-Einstellungsseit das Häkchen bei <em>Externe Programme erlauben</em> entfernst und reaktivierst, wird ein <strong>neuer Code</strong> generiert und das Lesezeichen verliert seine Gültigkeit (das gilt natürlich auch für den QR Code). Du musst das Oval Office dann erneut über das Verzeichnis in DV aufrufen (dabei wird auch ein neuer QR Code generiert). Im Übrigen kann man so auch seinen Zugangscode schnell ändern, falls den doch mal jemand mitbekommen hat.</div></div>';
	
} 

// soul data & xml log
if ( $p == 2 && $dbu == 1 ) {
	$rewards = $soul->data->rewards->r;
	//temp iutf8
	$db->iquery('DELETE FROM dvoo_citizen_rewards WHERE uid = '.$data['user']['id'].' AND reward NOT IN ("Osterei gefunden","Spende","Weihnachtsgeist entdeckt");');
		
	foreach ( $rewards AS $r ) {
		$r_name = (string) $r['name'];
		$r_rare = (int) $r['rare'];
		$r_count = (int) $r['n'];
		$r_img = (string) $r['img'];
		
		$db->iquery('INSERT IGNORE INTO dvoo_rewards VALUES ("'.$r_name.'", "'.$r_img.'", '.$r_rare.')');
		
		$db->iquery('INSERT INTO dvoo_citizen_rewards VALUES ('.$data['user']['id'].', "'.$r_name.'", '.$r_count.') ON DUPLICATE KEY UPDATE count = '.$r_count.' ');
		
		if ( isset($r->title) ) {
			$t = $r->title;
			$exttit = $db->query(' SELECT * FROM dvoo_titles WHERE name = "'.mysql_real_escape_string((string) $t['name']).'" ');
			if ( isset($exttit[0]['name']) ) {
				// title exists -> update
				$trmin = (int) (($r_count > 0 && $exttit[0]['min'] == 0) || ($r_count < $exttit[0]['min'] && $r_count > 0) ? $r_count : $exttit[0]['min']);
				$trmax = (int) ($r_count > $exttit[0]['max'] ? $r_count : $exttit[0]['max']);
				
				$db->iquery(' UPDATE dvoo_titles SET reward = "'.mysql_real_escape_string($r_name).'", `min` = '.$trmin.', `max` = '.$trmax.' WHERE name = "'.mysql_real_escape_string((string) $t['name']).'" ');
			}
			else {
				// insert new title
				$db->iquery(' INSERT INTO dvoo_titles VALUES ("'.mysql_real_escape_string((string) $t['name']).'", "'.mysql_real_escape_string($r_name).'", '.$r_count.', '.((int) $r_count).') ');
			}
		}
	}

	$score = 0;
	$maps = $soul->data->maps->m;
	foreach ( $maps AS $m ) {
		$score += (int) $m['score'];
	}
	$db->iquery('INSERT INTO dvoo_stat_soul VALUES ('.$data['user']['id'].', '.$score.') ON DUPLICATE KEY UPDATE score = '.$score.' ');
	
	// save xml
	$q = ' INSERT INTO dvoo_xml VALUES ('.$data['user']['id'].', '.$data['town']['id'].', '.$data['current_day'].', "", "'.$k.'", '.time().') ON DUPLICATE KEY UPDATE xml = "", stamp = '.time().' ';
	$db->iquery($q);
	$fileC = 'xml/'.$data['town']['id'].'.xml';
	file_put_contents($fileC, $xml_string, LOCK_EX);
	$fileH = 'xml/history/'.$data['town']['id'].'-'.$data['current_day'].'.xml';
	file_put_contents($fileH, $xml_string, LOCK_EX);

	
	$q = ' INSERT INTO dvoo_soul VALUES ('.$data['user']['id'].', "'.mysql_escape_string($soul_string).'", '.time().') ON DUPLICATE KEY UPDATE xml = "'.mysql_escape_string($soul_string).'", stamp = '.time().' ';
	$db->iquery($q);
}