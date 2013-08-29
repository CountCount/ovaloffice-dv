<?php
include_once 'system.php';
$db = new Database();

// get version number
$v = (int) $_POST['v'];
// get process number
$p = (int) $_POST['p'];
if ( $p == 2 ) {
	$k = htmlspecialchars(strip_tags($_POST['k']));
	$u = (int) $_POST['u'];
}
elseif ( $p == 1 ) {
	$k = htmlspecialchars(strip_tags($_POST['k']));
	$n = htmlspecialchars(strip_tags($_POST['n']));
}
else {
	// no data send -> start
	print '<script type="text/javascript">
			$("#link_auswaertigesamt").remove();
			$("#auswaertigesamt").remove();
		</script>';
	exit;
}
?>
<p>Die dicke Luft wegen der Bauarbeiten verhindert derzeit gezielten Sicht- und Sprachkontakt zu anderen Wartenden. Wenn sich der Staub legt, kann hier hoffentlich rege Kommunikation stattfinden. Bis dahin können Sie aber Nachrichten in den Raum rufen, unser sensibles Sprachsteuersystem wird die Nachricht transkribieren.</p>
<div id="fb_form_wrapper">
<form id="fb_form" method="POST" onsubmit="submitFBform();return false;">
<strong>Feedback:</strong><input id="fb-button" type="submit" value="<?php print 'Absenden'; ?>" /><br/><textarea id="fb" name="fb" /></textarea>
<input type="hidden" value="<?php print $u; ?>" name="u" />
</form>
</div><p style="font-size:0.875em;color:#aac;padding-left:1em;text-align:justify;"><strong>Live-Kontakt (kein 24/7-Dienst):</strong><br/>ICQ: 619289322<br/>Skype: sin.star<br/><br/><strong>RP <span style="color:#caa;">neu!</span></strong><br/><span style="color:#aca;">[karte]</span> - Ziehe eine Karte!<br/><span style="color:#aca;">[zk]</span> - Wirf eine Münze!<br/><span style="color:#aca;">[wn]</span> - Wirf einen n-seitigen Würfel!<br/><span style="color:#aca;">[cocktail]</span> - Cocktail gefällig?<br/> </p>

<h3 id="fb_pinheader"><a href="javascript:void(0);" onclick="loadFBlist();this.blur();" style="display:block;float:right;margin-left:12px;font-size:.875em;font-weight:bold;text-decoration:none;color:#962;">Erneut hinhorchen</a>Die letzten <input type="text" size="4" value="25" id="postcount" maxlength="3"> Gesprächsfetzen</h3>
<div id="fb_pinboard"><div class="loading"></div></div>

<script type="text/javascript">				
				function submitFBform() {  
					$('#fb-button').hide();
					var fb = $.post(  
						"wb.form.ajax.php",  
						$("#fb_form").serialize(),  
						function(data){  
							$('#fb').val('');
							$('#fb_pinboard').html(data);
							$('#fb-button').fadeIn(500);
						}  
					);
				}
				
				var oo_fb = $.ajax({
						type: 'POST',
						url: 'wb.list.ajax.php',
						data: 'u=<?php print $u; ?>&p='+$('#postcount').val(),
						success: function(msg) {
							$('#fb_pinboard').html(msg);
						}
					});
					
				function loadFBlist() {
          $('#fb_pinboard').html('<div class="loading"></div>');  
					var oo_fb = $.ajax({
						type: 'POST',
						url: 'wb.list.ajax.php',
						data: 'u=<?php print $u; ?>&p='+$('#postcount').val(),
						success: function(msg) {
							$('#fb_pinboard').html(msg);
						}
					});
				}
				
				// loadFBlist();
					
		jQuery(function($) {
			$("a[rel^='lightbox']").slimbox({/* Put custom options here */}, null, function(el) {
					return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
			});
    });
	</script>
<?php

// -- Piwik Tracking API init -- 
require_once "PiwikTracker.php";
PiwikTracker::$URL = 'http://sindevel.com/piwik/';

$piwikTracker = new PiwikTracker( $idSite = 2 );
// You can manually set the visitor details (resolution, time, plugins, etc.) 
// See all other ->set* functions available in the PiwikTracker.php file
$piwikTracker->setURL('http://dv.sindevel.com/oo/wb.main.ajax.php');
$piwikTracker->setCustomVariable(1, 'user', $data['user']['id']);
$piwikTracker->setCustomVariable(2, 'scode', $k);
$piwikTracker->setCustomVariable(3, 'username', $data['user']['name']);
$piwikTracker->setCustomVariable(4, 'ip', $_SERVER['REMOTE_ADDR']);
$piwikTracker->setCustomVariable(5, 'query', $_SERVER['QUERY_STRING']);
// Sends Tracker request via http
$piwikTracker->doTrackPageView('OO Lounge');