<?php 
header('Content-Type: text/xml');
include_once 'system.php';
$db = new Database();

$m = (string) $_GET['mode'];
$x = (int) $_GET['timestamp'];
$g = (int) $_GET['gameid'];

// -- Piwik Tracking API init -- 
require_once "PiwikTracker.php";
PiwikTracker::$URL = 'http://sindevel.com/piwik/';

$piwikTracker = new PiwikTracker( $idSite = 2 );
// You can manually set the visitor details (resolution, time, plugins, etc.) 
// See all other ->set* functions available in the PiwikTracker.php file
$piwikTracker->setURL('http://dv.sindevel.com/oo/api.php');
$piwikTracker->setCustomVariable(1, 'apiMode', $m);

// Sends Tracker request via http
$piwikTracker->doTrackPageView('OO api request');

switch ( $m ) {
	case 'citylist':
		$xmlstr = <<<XML
<?xml version="1.0"?>
<api xmlns:dc="http://purl.org/dc/elements/1.1" xmlns:content="http://purl.org/rss/1.0/modules/content/">
 <cities></cities>
</api>
XML;

		$xml = new SimpleXMLElement($xmlstr);
		$q = ' SELECT * FROM dvoo_towns WHERE id > 0 '.($x > 0 ? ' AND stamp >= ' . $x : '').' '.($g > 0 ? ' AND id >= ' . $g : '').' ORDER BY id ASC ';
		$r = $db->query($q);
		foreach ( $r AS $t ) {
			$city = $xml->cities->addChild('city');
			$city->addAttribute('gameid', $t['id']);
			$city->addAttribute('cityname', $t['name']);
			$city->addAttribute('day', $t['day']);
			$city->addAttribute('time', $t['stamp']);
			
			$s = ' SELECT * FROM dvoo_stat_zombies WHERE tid = ' . $t['id'] . ' ORDER BY day ASC ';
			$u = $db->query($s);
			foreach ( $u AS $v ) {
				$save = $city->addChild('save');
				$save->addAttribute('day', $v['day']);
				$save->addAttribute('zombies', $v['z']);
				$save->addAttribute('defense', $v['v']);
			}
		}
		
		 
		echo $xml->asXML();
		break;
}
