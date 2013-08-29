<?php
include_once 'system.php';
$db = new Database();

// get key (ajax)
$u = (int) $_POST['u'];
$t = (int) $_POST['t'];
$d = (int) $_POST['d'];
$x = (int) $_POST['x'];
$y = (int) $_POST['y'];
$a = (string) $_POST['a'];

$r = $db->query('SELECT name FROM dvoo_citizens WHERE id = '.$u);
$uname = $r[0][0];

switch ( $a ) {
	// load current status
	case 'load':
	{
		$r = $db->query('SELECT * FROM dvoo_towns WHERE id = '.$t);
		$town = $r[0];
		
		$q = ' SELECT * FROM dvoo_zones_visit WHERE tid = '.$t.' AND x = '.$x.' AND y = '.$y.' ORDER BY `on` DESC LIMIT 1';
		$r = $db->query($q);
		if ( isset($r[0]) && count($r[0]) > 0 ) {
			$visit = $r[0];
			if ( $visit['items'] != '' ) {
				$visit['items'] = unserialize($visit['items']);
			}
			else {
				$visit['items'] = array();
			}
		}
		else {
			$visit = null;
		}
		
		$q = ' SELECT * FROM dvoo_zones_buildings WHERE tid = '.$t.' AND x = '.$x.' AND y = '.$y.' ORDER BY `stamp` DESC LIMIT 1';
		$r = $db->query($q);
		if ( isset($r[0]) && count($r[0]) > 0 ) {
			$building = $r[0];
		}
		else {
			$building = null;
		}
		
		print '<div onclick="$(\'#zone-edit-box\').removeClass(\'activated\').delay(500).addClass(\'hideme\');" id="zone-edit-close">close</div><h3>'.t('EDIT_ZONE_INFO').': '.($x - $town['x']).'|'.($town['y'] - $y).'</h3>';
		print '<div class="current-zone-detail" id="current-zone-zombies"><h4>Zombieanzahl</h4><form id="edit-zone-zombies" onsubmit="zoneEditSaveZone('.$x.','.$y.','.$t.','.$town['day'].','.$u.',\'edit-zone-zombies\',\'saveZombies\');return false;"><input type="submit" value="'.t('SAVE').'" /><input type="text" maxlength="2" size="2" name="zombies" value="'.$visit['z'].'" /> '.t('ZOMBIES').'</form></div>';
		
		print '<div class="current-zone-detail" id="current-zone-depletion"><h4>Zonenbuddelstatus</h4><form id="edit-zone-depletion" onsubmit="zoneEditSaveZone('.$x.','.$y.','.$t.','.$town['day'].','.$u.',\'edit-zone-depletion\',\'saveZoneStatus\');return false;"><input type="submit" value="'.t('SAVE').'" /><input type="checkbox" name="zdried" value="1" '.($visit['dried'] == 1 ? 'checked="checked"' : '' ).' /> '.t('ZONE_DRIED').'</form></div>';
		
		if ( !is_null($building) ) {
			print '<div class="current-zone-detail" id="current-zone-building"><h4>Zustand des Gebäudes '.$building['name'].'</h4><form id="edit-zone-building" onsubmit="zoneEditSaveZone('.$x.','.$y.','.$t.','.$town['day'].','.$u.',\'edit-zone-building\',\'saveBuildingStatus\');return false;"><input type="submit" value="'.t('SAVE').'" /><input type="checkbox" name="bdried" value="1" '.($building['depleted'] == 1 ? 'checked="checked"' : '' ).' /> '.t('BUILDING_DRIED').'<br/><br/><h4 class="logbook"><img src="http://data.dieverdammten.de/gfx/icons/item_banned_note.gif" /> '.t('LOGBOOK').'<span><strong>BEISPIEL:</strong><br/>Stürme: Tag 5 | 6 | 12 | 15<br/>Insgesamt erforscht: 10x<br/>Letzter Besuch: Tag 16<br/>Status: 5x erforscht nach 1x Sturm<br/>Status: 5x erforscht nach 4x Sturm</span></h4><textarea style="width:280px;height:120px;" name="bcontent">'.($building['content'] == '' ? "Stürme: Tag X (bisher noch kein Sturm)\nInsgesamt erforscht: 0x\nLetzter Besuch: Tag X\nStatus: 0x erforscht nach 0x Sturm" : $building['content']).'</textarea>';
			$items = unserialize($building['items']);
			if ( is_array($items) && count($items) > 0 ) {
				foreach ( $items AS $i ) {
					print '<input class="zoneitem" id="bi'.$i['id'].'" type="hidden" name="bi['.$i['id'].']" value="'.$i['count'].'" />';
				}
			}
			
		
		print '</form><div id="current-building-items">';
		if ( is_array($items) && count($items) > 0 ) {
			foreach ( $items AS $i ) {
				$q = ' SELECT iname AS name, iimg AS img FROM dvoo_items WHERE iid = '.$i['id'].' ';
				$r = $db->query($q);
				$imd = $r[0];
				print '<div id="ci'.$i['id'].'" title="'.$imd['name'].'" class="current-building-itemlist-item" style="background:transparent url('.t('GAMESERVER_ITEM').$imd['img'].'.gif) 0px 1px no-repeat;" onclick="buildingEditItem('.$i['id'].',-1);">x'.$i['count'].'</div>';
			}
		}
		print '</div><br style="clear:left;" />&nbsp;</div>';
		}
		
		print '<div class="current-zone-detail" id="current-zone-itemlist"><h4>Gegenstände am Boden</h4><form name="edit-zone-items" id="edit-zone-items" onsubmit="zoneEditSaveZone('.$x.','.$y.','.$t.','.$town['day'].','.$u.',\'edit-zone-items\',\'saveZoneItems\');return false;"><input type="submit" value="'.t('SAVE').'" />';
		if ( is_array($visit['items']) && count($visit['items']) > 0 ) {
			foreach ( $visit['items'] AS $i ) {
				print '<input class="zoneitem" id="zi'.$i['id'].'" type="hidden" name="zi['.$i['id'].']" value="'.$i['count'].'" />';
			}
		}
		
		print '</form><div id="current-zone-items">';
		if ( is_array($visit['items']) && count($visit['items']) > 0 ) {
			foreach ( $visit['items'] AS $i ) {
				$q = ' SELECT iname AS name, iimg AS img FROM dvoo_items WHERE iid = '.$i['id'].' ';
				$r = $db->query($q);
				$imd = $r[0];
				print '<div id="di'.$i['id'].'" title="'.$imd['name'].'" class="current-zone-itemlist-item" style="background:transparent url('.t('GAMESERVER_ITEM').$imd['img'].'.gif) 0px 1px no-repeat;" onclick="zoneEditItem('.$i['id'].',-1);">x'.$i['count'].'</div>';
			}
		}
		print '</div><br style="clear:left;" />&nbsp;</div>';
		
		print '<div class="current-zone-detail" id="current-zone-result"></div>';
		#print styleItems($items);	
		break;
	}
	
	case 'saveZoneItems':
	{
		#print '<p><pre>'.var_export($_POST,true).'</pre></p>';
		$zi = $_POST['zi'];
		$items = array();
		if ( is_array($zi) && count($zi) > 0 ) {
			$itemcount = count($zi);
			foreach ( $zi AS $id => $ic ) {
				$items[] = array(
					'id' => $id,
					'count' => $ic,
					'broken' => 0,
				);
			}
		}
		$q = ' INSERT INTO dvoo_zones_visit VALUES ('.$t.', '.$d.', '.$x.', '.$y.', 0, 0, 0, "'.mysql_real_escape_string(serialize($items)).'", '.time().', "'.$uname.'") ON DUPLICATE KEY UPDATE items = "'.mysql_real_escape_string(serialize($items)).'", `on` = '.time().', `by` = "'.mysql_real_escape_string($uname.' (via OO)').'" ';
		$db->iquery($q);
		print '<p>'.date('H:i:s',time()).' | '.$itemcount.' '.($itemcount == 1 ? 'Gegenstand wurde' : 'Gegenstände wurden').' gespeichert.</p>';
		break;
	}
	
	case 'saveZombies':
	{
		$z = (int) $_POST['zombies'];
		$q = ' INSERT INTO dvoo_zones_visit VALUES ('.$t.', '.$d.', '.$x.', '.$y.', 0, 0, '.$z.', "", '.time().', "'.$uname.'") ON DUPLICATE KEY UPDATE z = '.$z.', `on` = '.time().', `by` = "'.mysql_real_escape_string($uname.' (via OO)').'" ';
		$db->iquery($q);
		print '<p>'.date('H:i:s',time()).' | '.$z.' Zombies wurden eingetragen.</p>';
		break;
	}
	
	case 'saveZoneStatus':
	{
		$z = (int) $_POST['zdried'];
		$q = ' INSERT INTO dvoo_zones_visit VALUES ('.$t.', '.$d.', '.$x.', '.$y.', 0, '.$z.', 0, "", '.time().', "'.$uname.'") ON DUPLICATE KEY UPDATE dried = '.$z.', `on` = '.time().', `by` = "'.mysql_real_escape_string($uname.' (via OO)').'" ';
		$db->iquery($q);
		print '<p>'.date('H:i:s',time()).' | Zone wurde als '.($z == 0 ? 'regeneriert':'leer').' markiert.</p>';
		break;
	}
	
	case 'saveBuildingStatus':
	{
		$z = (int) $_POST['bdried'];
		$c = (string) $_POST['bcontent'];
		$q = ' UPDATE dvoo_zones_buildings SET depleted = '.$z.', content = "'.mysql_real_escape_string($c).'", stamp = '.time().' WHERE tid = '.$t.' AND x = '.$x.' AND y = '.$y.' ';
		$db->iquery($q);
		print '<p>'.date('H:i:s',time()).' | Gebäude wurde als '.($z == 0 ? 'voll':'leer').' markiert.</p>';
		
		$bi = $_POST['bi'];
		$items = array();
		if ( is_array($bi) && count($bi) > 0 ) {
			$itemcount = count($bi);
			foreach ( $bi AS $id => $ic ) {
				$items[] = array(
					'id' => $id,
					'count' => $ic,
					'broken' => 0,
				);
			}
		}
		$q = ' UPDATE dvoo_zones_buildings SET items = "'.mysql_real_escape_string(serialize($items)).'", stamp = '.time().' WHERE tid = '.$t.' AND x = '.$x.' AND y = '.$y.' ';
		$db->iquery($q);
		print '<p>'.date('H:i:s',time()).' | '.$itemcount.' '.($itemcount == 1 ? 'Fundstück wurde' : 'Fundstücke wurden').' gespeichert.</p>';
		break;
	}
}