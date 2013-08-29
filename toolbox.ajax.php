<?php
include_once 'system.php';
$db = new Database();

$f = (string) $_POST['formid'];

// -- Piwik Tracking API init -- 
require_once "PiwikTracker.php";
PiwikTracker::$URL = 'http://sindevel.com/piwik/';

$piwikTracker = new PiwikTracker( $idSite = 2 );
// You can manually set the visitor details (resolution, time, plugins, etc.) 
// See all other ->set* functions available in the PiwikTracker.php file
$piwikTracker->setURL('http://dv.sindevel.com/oo/toolbox.ajax.php');
$piwikTracker->setCustomVariable(1, 'toolboxFunction', $f);

// Sends Tracker request via http
$piwikTracker->doTrackPageView('OO toolbox action');


switch ( $f ) {
	case 'et-add-item':
	{
		$n = (string) $_POST['item-name'];
		$t = (int)  $_POST['t'];
		$u = (int)  $_POST['u'];
		$d = (int)  $_POST['d'];
		$x = (int)  $_POST['x'];
		$y = (int)  $_POST['y'];
		$z = (int)  $_POST['z'];
		
		#print $n . ' ' .$t . ' ' .$u . ' ' .$d . ' ' .$x . ' ' .$y ;
		
		$q = ' SELECT name FROM dvoo_citizens WHERE id = ' . $u;
		$r = $db->query($q);
		$uname = $r[0][0];
		
		$q = ' SELECT info FROM dvoo_zones WHERE tid = '.$t.' AND day = '.$d.' AND x = '.$x.' AND y = '.$y.' ';
		$r = $db->query($q);
		$info = unserialize($r[0][0]);
		if ( isset($info['items']) ) {
			$items = $info['items'];
		}
		else {
			$items = array();
		}
		$q = ' SELECT items FROM dvoo_zones_visit WHERE tid = '.$t.' AND day = '.$d.' AND x = '.$x.' AND y = '.$y.' ORDER BY `by` DESC LIMIT 1 ';
		$r = $db->query($q);
		if ( isset($r[0][0]) ) {
			$items2 = unserialize($r[0][0]);
		}
		else {
			$items2 = array();
		}
		
		$q = ' SELECT iid FROM dvoo_items WHERE iname = "'.$n.'" ';
		$r = $db->query($q);
		if ( isset($r[0][0]) ) {
			$pro = 0;
			foreach ( $items AS $i => $item ) {
				if ( $item['id'] == (int) $r[0][0] ) {
					$items[$i]['count']++;
					$pro = 1;
				}
			}
			if ( $pro == 0 ) {
				$items[] = array(
					'id' => (int) $r[0][0],
					'count' => 1,
					'broken' => 0,
				);
			}
			
			$pro2 = 0;
			foreach ( $items2 AS $i => $item ) {
				if ( $item['id'] == (int) $r[0][0] ) {
					$items2[$i]['count']++;
					$pro = 1;
				}
			}
			if ( $pro == 0 ) {
				$items2[] = array(
					'id' => (int) $r[0][0],
					'count' => 1,
					'broken' => 0,
				);
			}
		}
		
		$info['items'] = $items;
		
		$q = 'UPDATE dvoo_zones SET z = '.$z.', info = "'.mysql_escape_string(serialize($info)).'" WHERE tid = '.$t.' AND day = '.$d.' AND x = '.$x.' AND y = '.$y.' ';
		$db->iquery($q);
		
		$q = 'INSERT INTO dvoo_zones_visit VALUES ('.$t.', '.$d.', '.$x.', '.$y.', 0, 0, '.$z.', "'.mysql_escape_string(serialize($items2)).'", '.time().', "'.mysql_real_escape_string($uname).'") ON DUPLICATE KEY UPDATE z = '.$z.', items = "'.mysql_escape_string(serialize($items2)).'", `on` = '.time().', `by` = "'.mysql_real_escape_string($uname).'" ';
		$db->iquery($q);
		
		$out = '';
		
		foreach ( $items AS $item ) {
			$bis = $db->query(' SELECT i.iid AS id, i.iimg AS img, i.iname AS name, i.icat AS cat FROM dvoo_items i WHERE i.iid = '.$item['id'].' ');
			$bh = $bis[0];
			$out .= '<div class="item '.($item['broken'] ? ' broken' : '').'"><span class="minus"><a href="javascript:changeItem(-1, '.$item['id'].');">x</a></span>&nbsp;<img src="'.t('GAME_ICON_SERVER').$bh['img'].'.gif" title="'.$bh['name'].'" />&nbsp;<span class="minus"><a href="javascript:changeItem('.($item['count'] - 1).', '.$item['id'].');">-</a></span><span class="count">'.$item['count'].'</span><span class="plus"><a href="javascript:changeItem('.($item['count'] + 1).', '.$item['id'].');">+</a></span></div>';
		}
		
		print $out.'<br style="clear:left;" />';
		break;
	}
	case 'et-change-item':
	{
		$i = (int)  $_POST['i'];
		$t = (int)  $_POST['t'];
		$u = (int)  $_POST['u'];
		$d = (int)  $_POST['d'];
		$x = (int)  $_POST['x'];
		$y = (int)  $_POST['y'];
		$a = (int)  $_POST['a'];
		
		$q = ' SELECT name FROM dvoo_citizens WHERE id = ' . $u;
		$r = $db->query($q);
		$uname = $r[0][0];
		
		$q = ' SELECT info FROM dvoo_zones WHERE tid = '.$t.' AND day = '.$d.' AND x = '.$x.' AND y = '.$y.' ';
		$r = $db->query($q);
		$info = unserialize($r[0][0]);
		if ( isset($info['items']) ) {
			$items = $info['items'];
		}
		else {
			$items = array();
		}
		
		foreach ( $items AS $n => $item ) {
			if ( $item['id'] == $i ) {
				if ( $a > 0 ) {
					$items[$n]['count'] = $a;
				}
				elseif ( $a == 0 ) {
					unset($items[$n]);
				}
				elseif ( $a == -1 ) {
					$items[$n]['broken'] = 1 - $items[$n]['broken'];
				}
			}
		}
		
		$info['items'] = $items;
		
		$q = 'UPDATE dvoo_zones SET info = "'.mysql_escape_string(serialize($info)).'" WHERE tid = '.$t.' AND day = '.$d.' AND x = '.$x.' AND y = '.$y.' ';
		$db->iquery($q);
		
		$q = ' SELECT items FROM dvoo_zones_visit WHERE tid = '.$t.' AND day = '.$d.' AND x = '.$x.' AND y = '.$y.' ';
		$r = $db->query($q);
		$items2 = unserialize($r[0][0]);
		
		foreach ( $items2 AS $n => $item ) {
			if ( $item['id'] == $i ) {
				if ( $a > 0 ) {
					$items2[$n]['count'] = $a;
				}
				elseif ( $a == 0 ) {
					unset($items2[$n]);
				}
				elseif ( $a == -1 ) {
					$items2[$n]['broken'] = 1 - $items[$n]['broken'];
				}
			}
		}
		
		$info['items'] = $items;
		
		$q = 'UPDATE dvoo_zones_visit SET items = "'.mysql_escape_string(serialize($items2)).'" WHERE tid = '.$t.' AND day = '.$d.' AND x = '.$x.' AND y = '.$y.' ';
		$db->iquery($q);
		
		$out = '';
		
		foreach ( $items AS $item ) {
			$bis = $db->query(' SELECT i.iid AS id, i.iimg AS img, i.iname AS name, i.icat AS cat FROM dvoo_items i WHERE i.iid = '.$item['id'].' ');
			$bh = $bis[0];
			$out .= '<div class="item '.($item['broken'] ? ' broken' : '').'"><span class="minus"><a href="javascript:changeItem(-1, '.$item['id'].');">x</a></span>&nbsp;<img src="'.t('GAME_ICON_SERVER').$bh['img'].'.gif" title="'.$bh['name'].'" />&nbsp;<span class="minus"><a href="javascript:changeItem('.($item['count'] - 1).', '.$item['id'].');">-</a></span><span class="count">'.$item['count'].'</span><span class="plus"><a href="javascript:changeItem('.($item['count'] + 1).', '.$item['id'].');">+</a></span></div>';
		}
		
		print $out.'<br style="clear:left;" />';
		break;
	}

}