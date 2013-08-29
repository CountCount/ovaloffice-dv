<?php
include_once 'system.php';

$db = new Database();

// get day number
$u = (int) $_REQUEST['u'];


$q = ' SELECT xml FROM dvoo_soul WHERE uid = '.$u.' ORDER BY stamp DESC LIMIT 1 ';
$r = $db->query($q);

if ( is_array($r) && count($r[0]) > 0 ) {
	$xml = simplexml_load_string($r[0]['xml']);
}
else {
	print '<div id="spy-close" class="clickable" onclick="spyclose();"></div>';
	print '<h2>'.t('NO_DATA_AVAILABLE').'</h2>';
	exit;
}

$headers = $xml->headers;
$game = $xml->headers->game;
$rewards = $xml->data->rewards->r;
$maps = $xml->data->maps->m;
$owner = $xml->headers->owner->citizen;

$titles = array();
$rare = array();
$common = array();
$games = array();

$citizen = array(
	'name' => (string) $owner['name'],
	'avatar' => (string) $owner['avatar'],
	'score' => 0,
);

foreach ( $rewards AS $r ) {
	$r_name = (string) $r['name'];
	$r_rare = (int) $r['rare'];
	$r_count = (int) $r['n'];
	$r_img = (string) $r['img'];
	if ( isset($r->title) ) {
		$t = $r->title;
		$titles[] = array(
			'name' => (string) $t['name'],
			'img' => $r_img,
			'rare' => $r_rare,
		);
	}
	if ( $r_rare == 1 ) {
		$ident = sprintf("%08d", $r_count).substr($r_name,0,3);
		$rare[$ident] = array(
			'name' => $r_name,
			'count' => $r_count,
			'img' => $r_img,
		);
	}
	else {
		$ident = sprintf("%08d", $r_count).substr($r_name,0,3);
		$common[$ident] = array(
			'name' => $r_name,
			'count' => $r_count,
			'img' => $r_img,
		);
	}
}
ksort($titles);
krsort($rare);
krsort($common);

foreach ( $maps AS $m ) {
	$citizen['score'] += (int) $m['score'];
	$games[(int) $m['id']] = array(
		'name' => (string) $m['name'],
		'season' => (int) $m['season'],
		'score' => (int) $m['score'],
		'days' => (int) $m['d'],
		'id' => (int) $m['id'],
		'v1' => (string) $m['v1'],
		'comment' => (string) $m->value,
	);
}

krsort($games);


/* ### OUTPUT ### */

print '<div id="spy-close" class="clickable" onclick="spyclose();"></div>';
print '<h2>'.$citizen['name'].'</h2>';
?> 
<div class="spy-box" id="spy-rwd">
<h3><?php print '<img src="'.t('GAMESERVER_ICON').'r_heroac.gif" /> '.t('REWARDS'); ?></h3>
<?php 
	foreach ( $rare AS $r ) {
		print '<div class="reward-wrapper rare"><div class="reward rare"><img src="'.t('GAMESERVER_ICON').$r['img'].'.gif" title="'.$r['name'].'" /></div>'.$r['count'].'</div>';
	}
	foreach ( $common AS $r ) {
		print '<div class="reward-wrapper"><div class="reward"><img src="'.t('GAMESERVER_ICON').$r['img'].'.gif" title="'.$r['name'].'" /></div>'.$r['count'].'</div>';
	}
?>
</div>

<div class="spy-box" id="spy-ttl">
<h3><?php print '<img src="'.t('GAMESERVER_ICON').'r_heroac.gif" /> '.t('TITLES'); ?></h3>
<?php 
	foreach ( $titles AS $r ) {
		print '<div class="title-wrapper '.($r['rare'] == 1 ? 'rare' : '').'"><div class="title '.($r['rare'] == 1 ? 'rare' : '').'"><img src="'.t('GAMESERVER_ICON').$r['img'].'.gif" title="'.$r['name'].'" /></div>'.$r['name'].'</div>';
	}
?>
</div>

<div class="spy-box" id="spy-csp">
<h3><span class="sp" style="float:right;"><?php print $citizen['score'].' '.t('POINTS'); ?></span><?php print '<img src="'.t('GAMESERVER_ICON').'small_score.gif" /> '.t('SOUL_POINTS'); ?></h3>
</div>

<div class="spy-box" id="spy-mps">
<h3><?php print '<img src="'.t('GAMESERVER_ICON').'r_explor.gif" /> '.t('GAMES'); ?></h3>
<table><tr><th>Saison</th><th>Stadt</th><th>Tage</th><th>Punkte</th></tr>
<?php 
	$zebra = 0;
	foreach ( $games AS $g ) {
		$zebra++;
		print '<tr class="'.($zebra % 2 == 0 ? 'even' : 'odd').'"><td>'.$g['season'].'</td><td><span class="clickable" onclick="spyontown('.$g['id'].','.$g['days'].');">'.$g['name'].'</td><td>'.$g['days'].'</td><td>'.$g['score'].'</td></tr>';
	}
?>
</table>
</div>


<?php

// -- Piwik Tracking API init -- 
require_once "PiwikTracker.php";
PiwikTracker::$URL = 'http://sindevel.com/piwik/';

$piwikTracker = new PiwikTracker( $idSite = 2 );
// You can manually set the visitor details (resolution, time, plugins, etc.) 
// See all other ->set* functions available in the PiwikTracker.php file
$piwikTracker->setURL('http://dv.sindevel.com/oo/stat.spyc.ajax.php');
$piwikTracker->setCustomVariable(1, 'spyuser', $u);
$piwikTracker->setCustomVariable(2, 'ip', $_SERVER['REMOTE_ADDR']);
$piwikTracker->setCustomVariable(3, 'query', $_SERVER['QUERY_STRING']);
// Sends Tracker request via http
$piwikTracker->doTrackPageView('OO SpyOnCitizen');