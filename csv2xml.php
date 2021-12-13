<?php
 /*
  @author: Sylvain Maltais (support@gladir.com)
  @created: 2021
  @website(https://www.gladir.com/corail-darkslateblue)
  @abstract(Target: PHP)
 */

if(1 == count($argv)) {
	echo "Paramètres attendu !\n";
	exit();
} elseif(("/?" == $argv[1])||("/H" == strtoupper($argv[1]))) {
	echo "Cette commande permet de convertir un fichier source d'extension .csv en format .xml\n";
	echo "\n";
	echo "Syntaxe : csv2xml   source.csv  target.xml";
	exit();
}

$source = $argv[1];
$target = $argv[2];

$Header = true;
$Separator = '|';
$I = 0;
if($fileRead = fopen($source, 'r')) {
	$fileWrite = fopen($target, 'w');
	fwrite($fileWrite, '<?xml version="1.0" encoding="ISO-8859-15"?>'."\n");
	fwrite($fileWrite, '<root>'."\n");
	$ColumnName = array();
	while($CurrLine = fgets($fileRead)) {
		if($Header && ($I == 0)) {
			for($Cellule = '', $K = 0,$J = 0; $J < strlen($CurrLine); $J++) {
				if($CurrLine[$J] == $Separator) {
					$ColumnName[$K] = trim($Cellule);
					$Cellule = '';
					$K++;
				} else {
					if(preg_match('/^[a-zA-Z0-9]+$/', $CurrLine[$J])) $Cellule .= $CurrLine[$J];
				}
			}
			$ColumnName[$K++] = trim($Cellule);
			$I++;
		} else if($CurrLine != '') {
			fwrite($fileWrite, '<entry>'."\n");
			for($Cellule = '', $K = 0,$J = 0; $J < strlen($CurrLine); $J++) {
				if($CurrLine[$J] == $Separator) {
					if($Header) {
						fwrite($fileWrite, '<'.$ColumnName[$K].'>'.$Cellule.'</'.$ColumnName[$K].'>'."\n");
					} else {
						fwrite($fileWrite, '<cellule'.$K.'>'.$Cellule.'</cellule'.$K.'>'."\n");
					}
					$Cellule = '';
					$K++;
				} else {
					 if(ord($CurrLine[$J]) >= 32) $Cellule .= $CurrLine[$J];
				}
			}
			if($Header) {
				fwrite($fileWrite, '<'.$ColumnName[$K].'>'.$Cellule.'</'.$ColumnName[$K].'>'."\n");
			} else {
				fwrite($fileWrite, '<cellule'.$K.'>'.$Cellule.'</cellule'.$K.'>'."\n");
			}
			fwrite($fileWrite, '</entry>'."\n");
			$I++;
		}
	}
	fwrite($fileWrite, '</root>'."\n");
	fclose($fileWrite);
	fclose($fileRead);
}
?>