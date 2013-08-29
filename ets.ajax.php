<?php
include_once 'system.php';

$db = new Database();

// get event
$e = (int) $_REQUEST['e'];
$k = (string) $_REQUEST['k'];
$o = (string) $_REQUEST['o'];
$s = (int) $_REQUEST['s'];
if ( $k != "" && $e > 0 ) {
	if ( $o == 'none' ) {
		$q = ' DELETE FROM dvoo_events_signup WHERE event = '.$e.' AND user = (SELECT id FROM dvoo_citizens WHERE scode = "'.$k.'") LIMIT 1 ';
		$db->iquery($q);
	}
	elseif ( $o != '' ) {
		$q = ' SELECT id FROM dvoo_citizens WHERE scode = "'.$k.'" ';
		$r = $db->query($q);
		if ( !isset($r[0]) || $r[0][0] != 0 ) {
			$q = ' INSERT INTO dvoo_events_signup VALUES ('.$e.', '.((int) $r[0][0]).', "'.$o.'", '.time().') ON DUPLICATE KEY UPDATE `option` = "'.$o.'", stamp = '.time().' ';
			$db->iquery($q);
		}
	}
}

print '1';
return '1';