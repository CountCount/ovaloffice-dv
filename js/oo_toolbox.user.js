/** OO Toolbox user script
 * version 1.0
 * April 2011
 * Copyright (c) 2011, Paul Bruhn
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
 * @link    http://dv.sindevel.com/oo/toolbox/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @charset UTF-8
*/
// ==UserScript==
// @name 		OO Toolbox
// @namespace 	http://dv.sindevel.com/oo/toolbox/
// @description Skript für Komfortfunktionen innerhalb des Spiels "Die Verdammten"
// @include 	http://www.dieverdammten.de/*
// @version    	2.0
// ==/UserScript==

(function(){
	var userKey = null;

	var style = document.createElement('style');
		style.setAttribute('type', 'text/css');
		document.getElementsByTagName('head')[0].appendChild(style);
	
	var root = document.createElement('div');
		root.setAttribute('id', 'oo-toolbox');
		root.setAttribute('title', 'Oval Office Toolbox');
		root.innerHTML = '<h3>Oval Office Toolbox</h3><iframe id="oo-toolbox-data" class="oo-toolbox-content"></iframe>';
	var banner = document.getElementById('banner');
		banner.setAttribute('style', 'position:relative;');
		banner.appendChild(root);
		
	var xhr = null;
	xhr = new XMLHttpRequest();
	if (xhr) {
			xhr.open('GET', '/disclaimer?id=18;rand='  + Math.random(), true);
			xhr.onreadystatechange = function () {
					if (xhr.readyState == 4 && /name=\"key\"\s+value=\"([a-zA-Z0-9]+)\"/.test(this.responseText)) {
							var ooif = document.getElementById('oo-toolbox-data');
							ooif.setAttribute('src', 'http://dv.sindevel.com/oo/toolbox.php?uk='+RegExp.$1);
					}
			};
			xhr.send(null);
	}
	function getOOdata(uk) {
		var oxhr = null;
		oxhr = new XMLHttpRequest();
		if (oxhr) {
				oxhr.open('GET', 'http://dv.sindevel.com/oo/toolbox.php?uk='  + uk, true);
				oxhr.onreadystatechange = function () {
						if (oxhr.readyState == 4) {
								//alert(xhr.responseText);
								//alert(RegExp.$1);
								document.getElementById('oo-toolbox-data').innerHTML = oxhr.responseText;
						}
				};
				oxhr.send(null);
		}
	}
	addStyle('#oo-toolbox { color: #000; background:#704018; position:absolute; right:70px; top:10px; width:420px; height:125px; z-index:2; margin:0; padding: 1px 0 0; }');
	addStyle('#oo-toolbox h3 { font-weight: normal; font-variant: small-caps; color: #DDAA5F; border-bottom: 1px solid #a73; border-top: 1px solid #a73; background:rgb(112, 64, 24); margin:0; padding: 0 5px; height: 12px; line-height: 12px; font-size: 12px; }');
	addStyle('.oo-toolbox-content { border:none; width: 420px; height: 100px; background: transparent url("http://www.dieverdammten.de/gfx/loc/de/content_header.jpg") fixed 50% 0%; border-top: 1px solid rgb(112, 64, 24); margin:0; padding: .5em; color: rgb(112, 64, 24); }');

	function addStyle(rule) {
		try {
			return style.sheet.insertRule(rule, style.sheet.cssRules.length);
		}
		catch(e) { console.error('Failed to insert CSS rule (' + rule + ')'); }
	};
	
})();
