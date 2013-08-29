<?php
/* ##### QUICK CONFIG ##### */
$maintenance = 0; // default: 0, values: 0, 1
$version = '5.4'; // current OO version
$language = 'de'; // current options: de, en
/* ######################## */

if ( isset($_POST['key']) ) {
	$data_string = 'v=220&r=dv&p=2&k=' . secureKey($_POST['key']);
	$dat2_string = 'v=220&r=dv&p=2';
	setcookie("key",secureKey($_POST['key']),time()+(3*86400));
	$key = $_POST['key'];
}
elseif ( isset($_GET['key']) ) { 
	$data_string = 'v=220&r=dv&p=2&k=' . secureKey($_GET['key']);
	$dat2_string = 'v=220&r=dv&p=2';
	setcookie("key",secureKey($_GET['key']),time()+(3*86400));
	$key = $_GET['key'];
}

elseif ( isset($_COOKIE['key']) ) {
	$data_string = 'v=220&r=co&p=2&k=' . secureKey($_COOKIE['key']);
	$dat2_string = 'v=220&r=co&p=2';
	setcookie("key",secureKey($_COOKIE['key']),time()+(3*86400));
	$key = $_COOKIE['key'];
}
else {
	$data_string = 'v=220&p=0&r='.urlencode($_SERVER['HTTP_REFERER']);
	$key = '';
}

// start system
ini_set('display_errors', 0);
// session start
session_start();
include_once 'system.php';
$db = new Database();

// exit if maintenance
if ( $maintenance == 1 ) {
	print t('MAINTENANCE_MSG');
	exit;
}

// html header
print '<?xml version="1.0" encoding="utf-8"?>';
print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

// head
print '<head>
	<title>Oval Office</title>
	<link rel="canonical" href="http://dieverdammten.net/ovaloffice/" />
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="js/jquery.event.drag-1.5.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
	<script type="text/javascript" src="js/slimbox2.js"></script>
	<link rel="stylesheet" href="css/slimbox2.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/alert.js"></script>
	<link rel="stylesheet" href="css/alert.css" type="text/css" media="screen" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<link type="text/css" href="css/oo2.css?v='.$version.'" rel="stylesheet" />
	<script src="js/RGraph/RGraph.common.core.js"></script>
	<script src="js/RGraph/RGraph.common.context.js"></script>
	<script src="js/RGraph/RGraph.common.annotate.js"></script>
	<script src="js/RGraph/RGraph.common.effects.js"></script>
	<script src="js/RGraph/RGraph.common.tooltips.js"></script>
	<script src="js/RGraph/RGraph.common.zoom.js"></script>
	<script src="js/RGraph/RGraph.line.js"></script>
	<script src="js/RGraph/RGraph.scatter.js"></script>
</head>';

// body/container
print '<body>';
?>
	<div id="container-wrapper">
	<div id="spy"><div id="spy-content"></div></div>
	<div id="knife"></div>
	<div id="container-head"></div>
	<div id="container">
	<div id="newlogo"><p><?php print t('VERSION').' '.$version; ?></p></div>
	
		<ul id="tabs">
			<li id="link_cott" style="cursor:pointer;position:absolute;right:-130px;top:115px;" onclick="eventSpy(1);"><img src="img/tdm.png" /></li>
			<li id="link_cott" style="cursor:pointer;position:absolute;right:-130px;" onclick="eventSpy(2);"><img src="img/sdb.png" /></li>
			<li id="link_office1"><a href="#office1">Foyer</a></li>
			<?php /* <li id="link_auswaertigesamt" class="hideme"><a href="#auswaertigesamt">Auswärtiges Amt</a></li> */ ?>
			<li id="link_office2" class="empty"><a href="#office2">Auswärtiges Amt</a></li>
			<li id="link_office3" class="empty"><a href="#office3">Reisebüro</a></li>
			<li id="link_office4" class="empty"><a href="#office4">Bürgeramt</a></li>
			<li id="link_office5" class="empty"><a href="#office5">Finanzamt</a></li>
			<li id="link_office6" class="empty"><a href="#office6">Amt für Statistik</a></li>
			<li id="link_office7" class="empty"><a href="#office7">Bauamt</a></li>
			<li id="link_office8" class="empty"><a href="#office8">Wartebereich</a></li>
			
			
		</ul>
	<div id="intro">
		<div id="refresh"><a href="javascript:refreshOffice();"></a></div>
		<div id="mailbox-wrapper"><div id="mailbox" class="icon"><a href="javascript:void(0);" onclick="$('#mailbox').toggleClass('icon postbox');" id="mailbox-toggle"></a><a href="javascript:void(0);" onclick="$('#mailbox').removeClass('postbox').addClass('icon');" id="mailbox-close"></a><?php include 'mailbox.inc.php'; ?></div></div>

			<form class="hideme" id="hdb_form" action="http://www.dv-hdb.cwsurf.de/" method="POST" target="_blank">
				
			</form>
		<span id="headtownday"></span>
	</div>
		<div id="tabcontents">
			<div id="office1" class="tabcontent">
				<?php 
				print '<ul id="sub-foyer" class="subtabs hideme">
					<li><a href="#foyer-intro">'.t('FOYER_INTRO').'</a></li>
					<li><a id="ooidtl" href="#foyer-id">'.t('FOYER_ID').'</a></li>
					<li><a href="#foyer-gm">'.t('FOYER_GM').'</a></li>
				</ul>';

				print '<div id="foyer-intro" class="subtabcontent">';
				?>
					<div class="clearfix">
						<h2>Willkommen im <em>Oval Office</em>!</h2>
						<p>Auf Grund der Ressourcenknappheit befindet sich dieses Gebäude wohl noch eine Weile im Aufbau. Nichtsdestotrotz versuchen wir bereits jetzt, Ihnen einen Mehrwert zu bieten. Im <strong>Auswärtigen Amt</strong> können Sie einen Blick auf unsere großzügige Karte der Außenwelt werfen und einzelne Zonen untersuchen. Das <strong>Bürgeramt</strong> dient der Koordination aller Bürger zum Wohle der Stadt. Bitte geben Sie dort Ihre Anwesenheit der nächsten Tage bekannt. Das wird u.a. benötigt, um im <strong>Reisebüro</strong> Expeditionen in die Außenwelt zu planen. Den materiellen Reichtum der Stadt können Sie im <strong>Finanzamt</strong> betrachten inklusive der Veränderungen zum Vortag und dem Verlauf während der Zeit in der Stadt. Das <strong>Statistikamt</strong> bietet eine welteweite Übersicht über registrierte Zombieangriffe, andere Städte sowie die Auszeichnungen von Bürgern. Im <strong>Bauamt</strong> können Baugenehmigungen für die Stadtentwicklung eingeholt werden.</p>
						<p>Falls wir Ihnen gerade nicht dienen können, haben Sie im <strong>Wartebereich</strong> die Möglichkeit, sich mit anderen Wartenden auszutauschen oder einfach nur einen Kommentar zum Oval Office zu hinterlassen.</p>
						
						<div class="hideme" style="background:#fdc;border:1px solid #d98;margin:6px;padding:6px;">
							<h3>Änderung der Funktionsweise des Menüs</h3>
							<p>Um die Auslastung des Servers zu minimieren werden die Inhalte der einzelnen Sektionen beim Aufruf des Oval Office nicht mehr automatisch geladen. Sobald der Stadtausweis im Foyer erscheint, wurde das XML bereits verarbeitet, d.h. zu dem Zeitpunkt könnte man in der Außenwelt schon zur nächsten Zone wandern. Um dann zu einem anderen Abschnitt zu wechseln, wie gewohnt den entsprechenden Menüpunkt anklicken. Erst dann wird der entsprechende Inhalt geladen.</p>
						</div>
					</div>
				</div>
				<div id="foyer-id" class="subtabcontent hideme">
					<div class="clearfix">
						<h3 id="CitizenIdentificationHeader">Identifikation läuft...</h3>
						<div id="CitizenIdentificationContent" class="loading"></div>
					</div>
				</div>
				<div id="foyer-gm" class="subtabcontent hideme">
					<div class="clearfix">
						<h2>Addons für ein verbessertes Spielerlebnis</h2>
						<div style="background:#fed;border:1px solid #dcb;margin:6px;padding:6px;">
							<h3>Videoanleitung von Burnout</h3>
							<iframe width="853" height="480" src="http://www.youtube.com/embed/QpEPrYlOrDc?rel=0" frameborder="0" allowfullscreen></iframe>
						</div>
						<div style="background:#fed;border:1px solid #dcb;margin:6px;padding:6px;">
							<h3>DieVerdammten Helferlein (Google Chrome)</h3>
							<img style="float:right;margin:0 0 .5em 1em;border:1px solid #dcb;" src="img/ooa_vh.png" />
							<p>Simas T. hat für Die2Nite eine kleine, aber geniale Chrome-Extension geschrieben, die es dem Spieler erlaubt, die jeweils aktuelle Zone automatisch mit verschiedenen externen Applikationen zu aktualisieren. Ganz richtig: AUTOMATISCH! D.h. Ihr müsst kein Knöpfchen drücken, keinen neuen Tab aufmachen, nada. Einfach auf das grüne Licht warten (Ampelsystem) und zur nächsten Zone weiter gehen. Da das so überaus praktisch ist, habe ich mit seiner Erlaubnis das ganze für DV angepasst. Spätestens jetzt habt Ihr einen guten Grund, auf Google Chrome umzusteigen. :)<br/>Die Extension mit dem Namen <strong>DieVerdammten Helferlein</strong> könnt Ihr ab sofort im <a href="https://chrome.google.com/webstore/detail/hannbnhkmhhmfafnlldcdbhnepjpladf">chrome web store</a> herunterladen und installieren. Ich habe auch ein kurzes <a href="http://www.youtube.com/watch?v=1Wfxx3MnuPc&feature=player_embedded">Demo-Video</a> erstellt, damit Ihr wisst, was auf Euch zukommt.</p>
						</div>
						<div style="background:#fed;border:1px solid #dcb;margin:6px;padding:6px;">
							<h3>DieVerdammten Helferlein (Mozilla Firefox)</h3>
							<p>Das was Simas T. für Chrome gebastelt hat, gibt es in ähnlicher Form nun auch für Firefox. Isaac W. hat das für Die2Nite vorgemacht, mit seiner Erlaubnis habe ich auch diese Erweiterung für DV adaptiert. Das Add-on erlaubt es dem Spieler, die jeweils aktuelle Zone bei verschiedenen externen Applikationen zu aktualisieren. Das geht zwar nicht ganz so automatisch wie bei der Chrome-Version, aber dafür gibt es mehr Rückmeldungen.<br/>Das Add-on trägt den gleichen Namen <strong>DieVerdammten Helferlein</strong> und ist ab sofort auf der <a href="https://addons.mozilla.org/de/firefox/addon/dieverdammten-helferlein/">Mozilla Add-on Platform</a> verfügbar.</p>
							<p>Nach der Installation gibt es <strong>keine</strong> visuellen Hinweise auf das Vorhandensein auf der DV-Seite. Wenn Ihr aber in der Außenwelt seid (und auch nur da), steht Euch über einen Klick mit der rechten Maustaste im dann erscheinenden Kontextmenü der Befehl "Karten aktualisieren" zur Verfügung.</p>
						</div>
						<div style="background:#fed;border:1px solid #dcb;margin:6px;padding:6px;">
							<img style="float:right;margin:0 0 .5em 1em;border:1px solid #dcb;" src="img/ooa_tb.png" />
							<h3>Oval Office Toolbox (Greasemonkey)</h3>
							<p style="clear:right;">Das <a href="/ovaloffice/js/ext/oo_toolbox.user.js">GM Skript</a> zeigt verschiedene, stadtbezogene Informationen direkt im Header von DV an.</p>
						</div>
						<div style="background:#fed;border:1px solid #dcb;margin:6px;padding:6px;">
							<h3>Die Verdammte Karte Updater (Greasemonkey)</h3>
							<p>Die Karten des Auswärtigen Amtes und der Fata Morgana können nun auch über <a href="http://dvmap.nospace.de/index.php?option=updater">tobsters GM-Skript</a> aktualisiert werden.<br/>Dazu müssen nach Zeile 55 im Skript folgende Zeilen eingefügt werden:<br/><br/><span style="font-size:10px;font-family: 'Courier New', monospace;">webapps['oomap'] = { id: 18,  url: 'http://dieverdammten.net/ovaloffice/upd.php', label: 'Oval Office', key: null, xml: false };<br/>
		webapps['fmmap'] = { id: 26,  url: 'http://dieverdammten.net/fatamorgana/update', label: 'Fata Morgana', key: null, xml: false };</span><br/><br/>Alternativ steht auch die aktuelle, bereits um die obige Zeile <a href="/ovaloffice/js/dvmap.user.js">erweiterte Version</a> zur Verfügung.</p>
							<p>Wie man GM-Skripte unter Opera zum Laufen bringt, hat NobbZ in seinem <a href="http://nobbz.de/blog/2011/09/oo-und-mapupdater-scripte-in-opera/" target="_new">blog</a> erklärt.</p>
						</div>
						<div style="background:#fed;border:1px solid #dcb;margin:6px;padding:6px;">
							<h3>Stylish-Script von Grolnar</h3>
							<p>Grolnar hat Vorlagen für das Addon Stylish geschrieben, mit dem sich die Darstellung von DV insbesondere auch für kleine Displays optimieren lässt. Dazu gibt es einige weitere Komfortfunktionen, der Code ist gut dokumentiert. Stylish gibt es für Firefox und Chrome.
							</p>
							<ul style="margin-left: 2em;">
								<li><a href="http://userstyles.org/styles/58638/die-verdammten-optimierung-f-r-kleine-displays" target="_new">Optimierung für kleine Displays</a></li>
								<li><a href="http://userstyles.org/styles/58641/die-verdammten-camping-ausblenden" target="_new">Camping ausblenden</a></li>
								<li><a href="http://userstyles.org/styles/58640/die-verdammten-rucksack-permanent-offen" target="_new">Rucksack permanent offen halten</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			
			<?php /* <div id="auswaertigesamt" class="tabcontent hideme"><div class="loading">
			</div></div> */ ?>
			
			<div id="office2" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office3" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office4" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office5" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office6" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office7" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			<div id="office8" class="tabcontent hideme"><div class="loading">
			</div></div>
			
			
		</div>
		
		<div class="extapp">
			<h3>Externe Gebäude</h3>
			<a target="_new" href="http://www.dieverdammten.de/#disclaimer?id=6">Verdammte Karte</a>
			<a target="_new" href="http://www.dieverdammten.de/#disclaimer?id=10">NobbZ' Wiki</a>
			<a target="_new" href="http://www.dieverdammten.de/#disclaimer?id=14">Forschungsturm</a>
			<a target="_new" href="http://verdammt.zwischenwelt.org/">Zwischenwelt</a>
			<a target="_new" href="http://forum.der-holle.de/index.php">Holles Forum</a>
			<br/>
		</div>
		
</div>	
<div id="container-foot"></div>
</div>

<div id="disclaimer"><div style="float:right;margin:6px 6px 12px 12px;text-decoration:none;border:none;text-align:center;"><g:plusone></g:plusone><br/><br/>
<a href="http://buntmacher.net" target="_blank" style="text-decoration:none;border:none;">
<img src="/ovaloffice/css/img/bm-logo.png" alt="Logo buntmacher" title="buntmacher" border="0" width="100" /></a><br/>
</div>
Das "Oval Office" ist ein Fan-Projekt für das Survival-Browsergame <a href="http://www.dieverdammten.de?ref=SinSniper" target="_new">Die Verdammten</a>.<br/>Alle Angaben auf dieser Seite sind ohne Gewähr auf Richtigkeit und/oder Vollständigkeit.<br/>
Die verwendeten Icons entstammen dem Browserspiel <a href="http://www.dieverdammten.de?ref=SinSniper" target="_new">Die Verdammten</a> selbst<br/>bzw. der Plattform <a href="http://twinoid.com/de" target="_new">Twinoid</a> und sind somit Eigentum von <a href="http://www.motion-twin.com/german" target="_new">Motion Twin</a>.<br/>
Programmierung: Paul Bruhn a.k.a. <a href="mailto:ovaloffice.dv@gmail.com">SinSniper</a><br/>
Design: <a href="http://buntmacher.net" target="_new">buntmacher</a> a.k.a <span style="color:#333;">BetaBlocker</span><br/>
Archäologisches Archiv: Mit freundlicher Genehmigung von <span style="color:#333;">redsilence</span><br/>
Teile der Getränkekarte:  &copy;<span style="color:#333;">Bellegar</span><br/>
<em>Dieses Projekt wurde aktiv unterstützt (Geldspenden/Sachleistungen) von:</em><br/><span style="color:#333;">copafenris</span>, <span style="color:#333;">Juliezu</span>, <span style="color:#333;">wurststinker</span>, <span style="color:#333;">Nyrno</span>, <span style="color:#333;">Burnout</span>, <span style="color:#333;">IsaBellaCullen</span>, <span style="color:#333;">bayernplayer</span> &amp; 6 Unbekannten<br/><br/>Auf Grund meiner Abwesenheit im Spiel selbst wird dieses Projekt nicht aktiv weiterentwickelt und nur unregelmäßig gewartet.<br/>Wer die Weiterentwicklung selbt in die Hand nehmen möchte, kann sich gern mit mir in Verbindung setzen.
</div>

<div id="dynascript"></div><div id="goc"></div>
<div id="debug">
</div>
	<script type="text/javascript">
				var office = {};
				office["1"] = true;
				office["2"] = false;
				office["3"] = false;
				office["4"] = false;
				office["5"] = false;
				office["6"] = false;
				office["7"] = false;
				office["8"] = false;

				var phpreg = {};
				phpreg["2"] = 'map2';
				phpreg["3"] = 'rb.main';
				phpreg["4"] = 'ema.main';
				phpreg["5"] = 'bank';
				phpreg["6"] = 'stat';
				phpreg["7"] = 'bau';
				phpreg["8"] = 'wb.main';
				
				var loading = '<div class="loading"></div>';
				var amt = '#office1';
				$("ul#tabs li a").click(function (e) { 
					e.preventDefault();
					var newAmt = $(this).attr('href');
					//var lOf = 'loadO' + newAmt.substr(2);
					//lOf(userid);
					//window[lOf](userid);
					loadOffice(newAmt.substr(7),userid);
					$(amt).fadeOut();
					$(newAmt).fadeIn('slow');
					amt = newAmt;
					if ( newAmt == '#office2' ) {
						$("#infoblock-zone").removeClass('hideme');
					}
					else {
						$("#infoblock-zone").addClass('hideme');
					}
				});
				
				$("ul#tabs li a").hover(function () {
					$(this).addClass("hilitetab");
				}, function () {
					$(this).removeClass("hilitetab");
				});

				
				function processXML(u) {
					$('#infoblock_zone').remove();
					$('#infoblock-zone').remove();
					$('#CitizenIdentificationContent').html(' ').addClass('loading');
					// process XML
					var oo_xml = $.ajax({
						type: 'POST',
						url: 'xml.ajax.php',
						data: '<?php print $data_string; ?>&u='+u,
						success: function(msg) {
							$('#CitizenIdentificationHeader').html('Identifikation abgeschlossen.');
							$('#CitizenIdentificationContent').html(msg);
							$('#ooidtl').click();
							refreshOffice();
						}
					});
				}
				
				function loadTabContent(u) {	
				}
				
				function refreshOffice() {
					for ( i = 2; i < 9; i++ ) {
						office[i] = false;
						loadOffice(i,userid);
					}
				}
				
				function loadOffice(i,u) {
					if ( office[i] == false ) {
						// load map content
						$('#link_office'+i).addClass("empty");
						$('#office'+i).html(loading);
						var ooo = $.ajax({
							type: 'POST',
							url: phpreg[i]+'.ajax.php',
							data: '<?php print $dat2_string; ?>&u='+u,
							success: function(msg) {
								$('#infoblock-zone').remove();
								$('#office'+i).html(msg);
								$('#link_office'+i).removeClass("empty");
								if ( $('#office2').css("display") == 'block' ) {
									$("#infoblock-zone").removeClass('hideme');
								}
							}
						});
						office[i] = true;
					}
				}
				
				function loadOffice1(u) {
				}
				
				function loadOffice2(u) {
					if ( office2 == false ) {
						// load map content
						$('#link_office2').addClass("empty");
						$('#office2').html(loading);
						var oo_o2 = $.ajax({
							type: 'POST',
							url: 'map2.ajax.php',
							data: '<?php print $dat2_string; ?>&u='+u,
							success: function(msg) {
								$('#infoblock-zone').remove();
								$('#office2').html(msg);
								$('#link_office2').removeClass("empty");
								if ( $('#office2').css("display") == 'block' ) {
									$("#infoblock-zone").removeClass('hideme');
								}
							}
						});
						office2 = true;
					}
				}
					
				function loadOffice3(u) {
					if ( office3 == false ) {
						// load rb content
						$('#link_office3').addClass("empty");
						$('#office3').html(loading);
						var oo_em = $.ajax({
							type: 'POST',
							url: 'rb.main.ajax.php',
							data: '<?php print $dat2_string; ?>&u='+u,
							success: function(msg) {
								$('#office3').html(msg);
								$('#link_office3').removeClass("empty");
							}
						});
						office3 = true;
					}
				}
					
				function loadOffice4(u) {
					if ( office4 == false ) {
						// load ema content
						$('#link_office4').addClass("empty");
						$('#office4').html(loading);
						var oo_em = $.ajax({
							type: 'POST',
							url: 'ema.main.ajax.php',
							data: '<?php print $dat2_string; ?>&u='+u,
							success: function(msg) {
								$('#office4').html(msg);
								$('#link_office4').removeClass("empty");
							}
						});
						office4 = true;
					}
				}
					
				function loadOffice5(u) {
					if ( office5 == false ) {
						// load bank content
						$('#link_office5').addClass("empty");
						$('#office5').html(loading);
						var oo_fa = $.ajax({
							type: 'POST',
							url: 'bank.ajax.php',
							data: '<?php print $dat2_string; ?>&u='+u,
							success: function(msg) {
								$('#office5').html(msg);
								$('#link_office5').removeClass("empty");
							}
						});
						office5 = true;
					}
				}
					
				function loadOffice8(u) {
					if ( office8 == false ) {
						// load chat content
						$('#link_office8').addClass("empty");
						$('#office8').html(loading);
						var oo_fb = $.ajax({
							type: 'POST',
							url: 'wb.main.ajax.php',
							data: '<?php print $dat2_string; ?>&u='+u,
							success: function(msg) {
								$('#office8').html(msg);
								$('#link_office8').removeClass("empty");
							}
						});
						office8 = true;
					}
				}
					
				function loadOffice6(u) {
					if ( office6 == false ) {
						// load stat content
						$('#link_office6').addClass("empty");
						$('#office6').html(loading);
						var oo_fa = $.ajax({
							type: 'POST',
							url: 'stat.ajax.php',
							data: '<?php print $dat2_string; ?>&u='+u,
							success: function(msg) {
								$('#office6').html(msg);
								$('#link_office6').removeClass("empty");
							}
						});
						office6 = true;
					}
				}
				
				function loadOffice7(u) {
					if ( office7 == false ) {
						// load bau content
						$('#link_office7').addClass("empty");
						$('#office7').html(loading);
						var oo_fa = $.ajax({
							type: 'POST',
							url: 'bau.ajax.php',
							data: '<?php print $dat2_string; ?>&u='+u,
							success: function(msg) {
								$('#office7').html(msg);
								$('#link_office7').removeClass("empty");
							}
						});
						office7 = true;
					}
				}
			
				
				function loadDeadContent(u) {	
					var loading = '<div class="loading"></div>';
					office["6"] = true;
					office["8"] = true;
					// load chat content
					$('#link_office8').addClass("empty");
					$('#office8').html(loading);
					var oo_fb = $.ajax({
						type: 'POST',
						url: 'wb.main.ajax.php',
						data: '<?php print $dat2_string; ?>&u='+u,
						success: function(msg) {
							$('#office8').html(msg);
							$('#link_office8').removeClass("empty");
						}
					});
					
					// load stat content
					$('#link_office6').addClass("empty");
					$('#office6').html(loading);
					var oo_fa = $.ajax({
						type: 'POST',
						url: 'stat.ajax.php',
						data: '<?php print $dat2_string; ?>&u='+u,
						success: function(msg) {
							$('#office6').html(msg);
							$('#link_office6').removeClass("empty");
						}
					});
				}
				
				var curF = "#foyer-intro";
				$("ul#sub-foyer li a").click(function (e) { 
					e.preventDefault();
					var newF = $(this).attr("href");
					$(curF).fadeOut(100, function() { $(newF).fadeIn("slow", function() { $('#sub-foyer').slideDown("slow"); }); });
					curF = newF;
				});
				
				processXML(1);
				var mailcheck = window.setInterval("checkMailbox()", 600000);
				
				function checkMailbox() {
					var flcl = $.ajax({
						type: 'POST',
						url: 'mailbox.contactlist.php',
						data: 'u=<?php print $key; ?>',
						success: function(msg) {
							$('#mailbox-friendlist-list').html(msg);
						}
					});
					
					var iov = $('#io').val();
					var flml = $.ajax({
						type: 'POST',
						url: 'mailbox.message.php',
						data: 'action=list&u=<?php print $key; ?>&io='+iov,
						success: function(msg) {
							$('#mailbox-messages').html(msg);
						}
					});
				}
				function startGOC() {
					$("#santa").addClass("santa_end");
				}
				function endGOC() {
					$("#santa").remove();
				}
				function checkGOC(p,s) {
					$('#santa').remove();
					p = typeof(p) != 'undefined' ? p : 1;
					s = typeof(s) != 'undefined' ? s : 1;
					var goc = $.ajax({
						type: 'POST',
						url: 'goc.php',
						data: 'p='+p+'&s='+s+'&u=<?php print $key; ?>',
						success: function(msg) {
							//$('#goc').html(msg);
							eval(msg);
							if ( p == 3 ) {
								$("#santa").addClass("santa_end");
							}
						}
					});
				}
				
				
		function eventSpy(e) {
		// load stat content
		var st = $.ajax({
			type: "POST",
			url: "etc.ajax.php",
			data: "e="+e+"&k=<?php print $key; ?>",
			success: function(msg) {
				$("#spy-content").hide();
				$("html, body").animate({scrollTop:90}, "slow");
				$("#spy").animate({
					width: "12px",
					height: "720px",
					left: "489px",
					top: "95px"
				}, 250, function() {
					$("#spy").animate({
						width: "930px",
						left: "15px"
					}, 250, function() {
						$("#spy-content").html(msg).fadeIn(500);
					});
				});
			}
		});
	}
	function eventSignup(e,k,o,s) {
		// load stat content
		var st = $.ajax({
			type: "POST",
			url: "ets.ajax.php",
			data: "e="+e+"&k="+k+"&o="+o+"&s="+s,
			success: function(msg) {
				var r = (0 - (e - s));
				eventSpy(r);
			}
		});
	}
	</script>	
	<script type="text/javascript">
  window.___gcfg = {lang: 'de'};

  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
</body>
</html>

<?php

function secureKey($k) {
	return htmlspecialchars(strip_tags($k));
}