<?php

foreach ($notes AS $note) {
	print '<div class="fb_post'.($note['id'] == 3137 ? ' admin' : '').($note['id'] == 3 ? ' dv_admin' : '').'">';
	if ( !is_null($note['name']) && trim($note['name']) != '' ) {
		print '<p class="fb_name">' . $note['name'] . ($note['oldnames'] != '' ? ' <span style="font-size:.825em;">(ehemals '.substr($note['oldnames'],2).')</span>' : '') . '</p>';
	}
	print '<p class="fb_time">' . date('d.m.Y H:i',$note['time']) . '</p>';
	print '<p class="fb_text">' . (strpos($note['feedback'],'cockstail') ? '<img style="float:right;margin:6px;" src="img/cockstail.gif" />' : '') . stripslashes($note['feedback']) . '</p>';
	print '</div>';
}