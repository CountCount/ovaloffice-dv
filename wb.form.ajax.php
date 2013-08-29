<?php
include_once 'system.php';
$db = new Database();

// get key (ajax)
$u = (int) $_POST['u'];
$p = (int) $_POST['p'];
if ( $p < 1 || !is_numeric($p) || is_null($p) ) {
  $p = 25;
}

if ( $u > 0 ) {
	if ( trim($_POST['fb']) != ''  ) {
		$user = $db->query('SELECT name FROM dvoo_citizens WHERE id = '.$u.' LIMIT 1');
 	  $res = $db->query('SELECT uid, feedback FROM dvoo_feedback WHERE time <= '.time().' ORDER BY time DESC LIMIT 1');
		$fb = $_POST['fb'];
 	  if ( $u != 3137 ) {
		  $fb = nl2br(strip_tags($_POST['fb']));
		} else {
			$fb = nl2br($_POST['fb']);
		}
		
		$fb = preg_replace_callback('|\[w([0-9]+)\]|', "dice",$fb);
		$fb = preg_replace_callback('|\[zk\]|', "ht",$fb);
		$fb = preg_replace_callback('|\[karte\]|', "cards",$fb);
		$fb = preg_replace_callback('|\[cocktail\]|', "cocks",$fb);
		$fb = str_replace(':)', '<img alt=":)" src="'.t('GAMESERVER_SMILEY').'h_smile.gif" />',$fb);
		$fb = str_replace(';)', '<img alt=";)" src="'.t('GAMESERVER_SMILEY').'h_blink.gif" />',$fb);
		$fb = str_replace(':O', '<img alt=":O" src="'.t('GAMESERVER_SMILEY').'h_surprise.gif" />',$fb);
		$fb = str_replace(':(', '<img alt=":(" src="'.t('GAMESERVER_SMILEY').'h_sad.gif" />',$fb);
		$fb = str_replace(':D', '<img alt=":D" src="'.t('GAMESERVER_SMILEY').'h_lol.gif" />',$fb);
		$fb = str_replace(':|', '<img alt=":|" src="'.t('GAMESERVER_SMILEY').'h_neutral.gif" />',$fb);
		$db->iquery('INSERT INTO dvoo_feedback VALUES ('.$u.', '.time().',"'.mysql_escape_string($fb).'")');
		mail("countcount.cc@googlemail.com", "[DVOO] Feedback by ".$user[0]['name']."", $fb);
	}	
}
		
$notes = $db->query('SELECT c.id,c.name,c.oldnames,f.time,f.feedback FROM dvoo_feedback f INNER JOIN dvoo_citizens c ON c.id = f.uid AND f.uid > 0 WHERE f.time <= '.time().' ORDER BY f.time DESC LIMIT '.$p);
	
include 'wb.out.php';

function dice($d) {
	if ($d[1] > 1000) $d[1] = 1000;
	return '<span class="wb-dice">'.rand(1,$d[1]).'<span class="base">[w'.$d[1].']</span></span>';
}
function ht() {
	$ht = array('Zahl','Kopf');
	return '<span class="wb-dice headtail">'.$ht[rand(0,1)].'</span>';
}
function cards() {
	$cards = array(
			'Herz Ass',
			'Herz 2',
			'Herz 3',
			'Herz 4',
			'Herz 5',
			'Herz 6',
			'Herz 7',
			'Herz 8',
			'Herz 9',
			'Herz 10',
			'Herz Bube',
			'Herz Dame',
			'Herz König',
			'Karo Ass',
			'Karo 2',
			'Karo 3',
			'Karo 4',
			'Karo 5',
			'Karo 6',
			'Karo 7',
			'Karo 8',
			'Karo 9',
			'Karo 10',
			'Karo Bube',
			'Karo Dame',
			'Karo König',
			'Pik Ass',
			'Pik 2',
			'Pik 3',
			'Pik 4',
			'Pik 5',
			'Pik 6',
			'Pik 7',
			'Pik 8',
			'Pik 9',
			'Pik 10',
			'Pik Bube',
			'Pik Dame',
			'Pik König',
			'Kreuz Ass',
			'Kreuz 2',
			'Kreuz 3',
			'Kreuz 4',
			'Kreuz 5',
			'Kreuz 6',
			'Kreuz 7',
			'Kreuz 8',
			'Kreuz 9',
			'Kreuz 10',
			'Kreuz Bube',
			'Kreuz Dame',
			'Kreuz König',
			'Spielregeln',
		);
	return '<span class="wb-dice card">'.$cards[rand(0,count($cards)-1)].'</span>';
}
function cocks() {
	$cocks = array(
			'Bloody Mary',
			'Red Beer',
			'Cuba Libre',
			'Dark & Stormy',
			'Cherry Hooker',
			'Highball',
			'Salty Dog',
			'Tequila Sunrise',
			'Vodka Marinostov',
			'Wake the Dead',
			'Smith & Wesson',
			'Freddie Fuddpucker',
			'Banshee',
			'White Russian',
			'Godfather',
			'Peppermint Patty',
			'Rusty Nail',
			'Stinger',
			'Bone Dry Martini',
			'Gibson',
			'Dry Rob Roy',
			'Kamikaze',
			'Sex on the Beach',
			'Zombie',
			'Hurricane',
			'French 75',
			'Jack Rose',
			'Long Island Iced Tea',
			'Ward Eight',
			'Singapore Sling',
			'Between the Sheets',
			'Plutonium-Bier',
			'Metal Slug',
			'Super Metal Slug',
			'Apokalypse Bier',
			'Schokobier',
			'Schokosekt',
			'Viper Spezial',
			'Vanille-Schocker',
			'Ranzige Milch',
			'Vergammelter Orangensaft',
			'Glühwein',
			'Grog',
		);
	return '<span class="wb-dice cocktail">'.$cocks[rand(0,count($cocks)-1)].'</span>';
}