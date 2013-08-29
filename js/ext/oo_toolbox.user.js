/** OO Toolbox user script
 * version 3.15
 * 11 June 2012
 * Copyright (c) 2011 - 2012, Paul Bruhn
 * Released under the GPL license (http://www.gnu.org/copyleft/gpl.html)
 *
 * −−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−----------
 *
 * This is a Greasemonkey user script.
 *
 * To install, you need Greasemonkey: http://www.greasespot.net/
 * Then restart Firefox and revisit this script.
 * Under Tools, there will be a new menu item to "Install User Script".
 * Accept the default configuration and install.
 *
 * To uninstall, go to Tools/Manage User Scripts,
 * select "OO Toolbox", and click Uninstall.
 *
 * −−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−----------
 *
 * @author  Paul Bruhn <ovaloffice.dv@gmail.com>
 * @link    http://dieverdammten.net/ovaloffice/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @charset UTF-8
*/
// ==UserScript==
// @name 		OO Toolbox
// @namespace 	http://dieverdammten.net/ovaloffice/
// @description Skript für Komfortfunktionen innerhalb des Spiels "Die Verdammten"
// @include 	http://www.dieverdammten.de/*
// @version    	3.15
// ==/UserScript==

(function(){ 
	var v = "3.15";
	var userKey = null;

	var style = document.createElement('style');
		style.setAttribute('type', 'text/css');
		document.getElementsByTagName('head')[0].appendChild(style);
		
	var root = document.createElement('div');
		root.setAttribute('id', 'oo-toolbox-wrapper');
		root.setAttribute('title', 'Oval Office Toolbox');
		root.innerHTML = '<div id="oo-toolbox"><h3 id="oot-title">Oval Office Toolbox (v'+v+')</h3><a id="oo-link" class="oo-link" href="">&gt;&gt; zum Oval Office</a><iframe id="oo-toolbox-basicdata" class="oo-toolbox-content"></iframe></div>';
	var ooparent = document.getElementById('contentBg');

		
	var xhr = null;
	xhr = new XMLHttpRequest();
	if (xhr) {
			xhr.open('GET', '/disclaimer?id=18;rand='  + Math.random(), true);
			xhr.onreadystatechange = function () {
					if (xhr.readyState == 4 && /name=\"key\"\s+value=\"([a-zA-Z0-9]+)\"/.test(this.responseText)) {
							ooparent.insertBefore(root,ooparent.childNodes[0]);
							var uk = RegExp.$1;
							var ooif = document.getElementById('oo-toolbox-basicdata');
							ooif.setAttribute('src', 'http://dieverdammten.net/ovaloffice/toolbox.php?v='+v+'&uk='+uk);
							var oopl = document.getElementById('oo-link');
							oopl.setAttribute('href', 'http://dieverdammten.net/ovaloffice/?key='+uk);
              oopl.setAttribute('target', '_blank');
					}
			};
			xhr.send(null);
	}
	addStyle('#contentBg:hover #oo-toolbox-wrapper + div { background-image: url("http://data.dieverdammten.de/gfx/loc/de/content_header_s5.jpg") !important; }');
	addStyle('#oo-toolbox-wrapper { position: relative; z-index: 4; width: 100%; }');
  //addStyle('#contentBg #oo-toolbox { display: none; }');
  addStyle('#contentBg:hover #oo-toolbox { display: block; }');
  addStyle('#oo-toolbox { color: #000; background: transparent; position:absolute; right:175px; top:14px; width:420px; height:125px; z-index:4; margin:0; padding: 1px 0 0; }');
	addStyle('#oo-toolbox h3 { font-weight: normal; font-variant: small-caps; color: #DDAA5F; border-bottom: 1px solid #a73; border-top: 1px solid #a73; background:#704018; margin:0; padding: 0 5px; height: 12px; line-height: 12px; font-size: 12px; box-shadow: 0px -1px 0px #704018, 0px 1px 0px #704018; }');
	addStyle('.oo-toolbox-content { position: absolute; border:none; height: 100px; width: 420px; background: transparent; margin:0; padding: .5em; color: rgb(112, 64, 24); }');
	addStyle('.oo-link { z-index: 1; position: absolute; top: 80px; right: 0; font-variant: small-caps; text-decoration: none; color: #fff; }');
	addStyle('.oo-link:hover { color: #ff0; text-shadow: #0f0 0px 0px 5px; }');
	
	function addStyle(rule) {
		try {
			return style.sheet.insertRule(rule, style.sheet.cssRules.length);
		}
		catch(e) { console.error('Failed to insert CSS rule (' + rule + ')'); }
	};
	
})();