<?php
    include_once 'dal2.php';
		include_once 'lang.inc.php';
		
		$db = new Database();
    
		// get day number
		$t = (int) $_POST['t']; // town
		$d = (int) $_POST['d']; // current day
		$l = (string) addslashes($_POST['l']);
		
		$itemsById = array();
		$itemsByName = array();
		$q = $db->query(' SELECT iid, iname FROM dvoo_items ORDER BY iid ASC ');
		foreach ( $q AS $i ) {
			$itemsById[$i['iid']] = $i['iname'];
			if ( !isset($itemsByName[$i['iname']]) ) {
				$itemsByName[$i['iname']] = $i['iid'];
			}
		}
		
		$lines = array_filter(explode("\n",$l));
		$pro = 0;
		$hid = 0;
		
		$out = count($lines) . " Registereinträge gefunden";
		
		$minus = array();
		$plus = array();
		
		foreach ( $lines AS $line ) {
			if ( preg_match('/\s*([a-zA-Z0-9]+)\s+hat folgenden Gegenstand aus der Bank genommen\s+([äöüÄÖÜß\(\)a-zA-Z 0-9\-]*)\s*/i', $line, $match) ) {
				#print "\n" . $match[1] . ': -' . $match[2] . '('.$itemsByName[trim($match[2])].')';
				$minus[$itemsByName[trim($match[2])]]++;
				$pro++;
			}
			elseif ( preg_match('/\s*([a-zA-Z0-9]+)\s+hat der Stadt folgendes gespendet\s+([äöüÄÖÜßß\(\)a-zA-Z 0-9\-]*)\s*/i', $line, $match) ) {
				#print "\n" . $match[1] . ': +' . $match[2] . '('.$itemsByName[trim($match[2])].')';
				if ( isset($itemsByName[trim($match[2])]) ) {
					$plus[$itemsByName[trim($match[2])]]++;
					$pro++;
				}
				else {
					$out .= "\n" . 'Unbekanntes Item: ' . $match[2];
				}
			}
			elseif ( preg_match('/\s*([a-zA-Z0-9]+)\s+hat folgenden Gegenstand hergestellt\s+([äöüÄÖÜß\(\)a-zA-Z 0-9\-]*)\s+und dafür diese Materialien vebraucht\s+([a-zA-Z 0-9\-]*)\s*/i', $line, $match) ) {
				#print "\n" . $match[1] . ': +' . $match[2] . '('.$itemsByName[trim($match[2])].')' . ', -' . $match[3] . '('.$itemsByName[trim($match[3])].')';
				$plus[$itemsByName[trim($match[2])]]++;
				$minus[$itemsByName[trim($match[3])]]++;
				$pro++;
			}
			elseif ( preg_match('/\s*Es ist etwas Gemüse in unserem Gemüsegarten gewachsen\s+([0-9]+)[x]\s+([äöüÄÖÜß\(\)a-zA-Z 0-9\-]*)\s+und\s+([0-9]+)[x]\s+([äöüÄÖÜa-zA-Z 0-9\-]*)\s*/i', $line, $match) ) {
				#print "\n" . $match[1] . ': +' . $match[2] . '('.$itemsByName[trim($match[2])].')' . ', -' . $match[3] . '('.$itemsByName[trim($match[3])].')';
				$plus[$itemsByName[trim($match[2])]] += $match[1];
				$plus[$itemsByName[trim($match[4])]] += $match[3];
				$pro++;
			}
			elseif ( preg_match('/\s*Dieser Registereintrag wurde durchgestrichen und ist nicht mehr lesbar\! Wer wollte damit etwas verbergen\?\s*/i', $line, $match) ) {
				#print "\n" . $match[1] . ': +' . $match[2] . '('.$itemsByName[trim($match[2])].')' . ', -' . $match[3] . '('.$itemsByName[trim($match[3])].')';
				$hid++;
				$pro++;
			}
			elseif ( preg_match('/\s*Für den Bau dieses Gebäudes\s+(.*)\s+wurden diese Materialien verbraucht\s+([äöüÄÖÜß\(\)a-zA-Z 0-9\-]*)\s+[x]([0-9]+)(\,\s+([äöüÄÖÜß\(\)a-zA-Z 0-9\-]*)\s+[x]([0-9]+))*\s*/i', $line, $match) ) {
				#print "\n" . $match[1] . ': +' . $match[2] . '('.$itemsByName[trim($match[2])].')' . ', -' . $match[3] . '('.$itemsByName[trim($match[3])].')';
				#$out .= var_export($match,true);
				#$minus[$itemsByName[trim($match[2])]] += $match[3];
				for ( $t = 1; $t < count($match); $t += 3 ) {
					$minus[$itemsByName[trim($match[($t+1)])]] += $match[($t+2)];
				}
				$pro++;
			}
			else {
				#Für den Bau dieses Gebäudes	 Kremato-Cue wurden diese Materialien verbraucht	  Zusammengeschusterter Holzbalken x8, Metallstruktur x1.
			}
		}
		#print var_export($plus,true);
		#print var_export($minus,true);
		
		$out .= "\n" . $pro . ' davon korrekt verarbeitet';
		$out .= "\n" . $hid . ' davon unkenntlich gemacht' . "\n";
		
		
		$q = $db->query(' SELECT iid, icount FROM dvoo_bankitems WHERE tid = '.$t.' AND cday = '.$d.' ORDER BY iid ASC ');
		foreach ( $q AS $r ) {
			$bT[$r['iid']] = $r['icount'];
		}
		
		$q = $db->query(' SELECT iid, icount FROM dvoo_bankitems WHERE tid = '.$t.' AND cday = '.($d - 1).' ORDER BY iid ASC ');
		foreach ( $q AS $r ) {
			$bY[$r['iid']] = $r['icount'];
		}
		
		foreach ( $itemsById AS $id => $name ) {
			if ( isset($bT[$id]) ) {
				$soll = 0;
				if ( isset($bY[$id]) ) {
					$soll = $bY[$id];
				}
				if ( isset($minus[$id]) ) {
					$soll -= $minus[$id];
				}
				if ( isset($plus[$id]) ) {
					$soll += $plus[$id];
				}
				if ( $soll != $bT[$id] ) {
					if ( $soll < $bT[$id] ) {
						$out .= "\n" . ($bT[$id] - $soll) . 'x ' . $name . ' zu viel in der Bank.';
					}
					else {
						$out .= "\n" . ($soll - $bT[$id]) . 'x ' . $name . ' zu wenig in der Bank.';
					}
				}				
			}
			elseif ( isset($bY[$id]) ) {
				$soll = $bY[$id];
				if ( isset($minus[$id]) ) {
					$soll -= $minus[$id];
				}
				if ( isset($plus[$id]) ) {
					$soll += $plus[$id];
				}
				if ( $soll != 0 ) {
					if ( $soll < 0 ) {
						$out .= "\n" . (0 - $soll) . 'x ' . $name . ' zu viel in der Bank.';
					}
					else {
						$out .= "\n" . $soll . 'x ' . $name . ' zu wenig in der Bank.';
					}
				}				
			}
			else {
				$soll = 0;
				if ( isset($minus[$id]) ) {
					$soll -= $minus[$id];
				}
				if ( isset($plus[$id]) ) {
					$soll += $plus[$id];
				}
				if ( $soll != 0 ) {
					if ( $soll < 0 ) {
						$out .= "\n" . (0 - $soll) . 'x ' . $name . ' zu viel in der Bank.';
					}
					else {
						$out .= "\n" . $soll . 'x ' . $name . ' zu wenig in der Bank.';
					}
				}				
			}
		}
		
		print $out;
		
		
		
		/*
		 witch23 hat folgenden Gegenstand aus der Bank genommen	  Konservendose ! 		
 tiaxblutaxt hat der Stadt folgendes gespendet	  Krummes Holzbrett 		
 Minka hat der Stadt folgendes gespendet	  Krummes Holzbrett 		
 Bang982 hat folgenden Gegenstand hergestellt	  Krummes Holzbrett und dafür diese Materialien vebraucht	  Verrotteter Baumstumpf . 	
		 Es ist etwas Gemüse in unserem Gemüsegarten gewachsen	 5x  Verdächtiges Gemüse und 4x  Darmmelone .		
 Für den Bau dieses Gebäudes	 Kremato-Cue wurden diese Materialien verbraucht	  Zusammengeschusterter Holzbalken x8, Metallstruktur x1. 	
 Dieser Registereintrag wurde durchgestrichen und ist nicht mehr lesbar! Wer wollte damit etwas verbergen? 			

		
		*/