<?php
 /*
  @name: csv2sq1.php
  @author: Sylvain Maltais (support@gladir.com)
  @created: 2021
  @website(https://www.gladir.com/corail-darkslateblue)
  @abstract(Target: PHP)
 */

if(1 == count($argv)) {
	echo "Paramètres attendu !\n";
	exit();
} elseif(("/?" == $argv[1])||("/H" == strtoupper($argv[1]))) {
	echo "Cette commande permet de convertir un fichier source d'extension .csv en format .sql\n";
	echo "\n";
	echo "Syntaxe : csv2xml   source.csv  target.sql";
	exit();
}

$Source = $argv[1];
$Target = $argv[2];

$Header = true;
$Separator = '|';
$Protected = "`"; // Caractère pour délimiter les identificateurs, par défaut ` en MySQL et MariaDB
$I = 0;
if($fileRead = fopen($Source, 'r')) {
	$fileWrite = fopen($Target, 'w');
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
			fwrite($fileWrite, 'CREATE TABLE '.$Protected.$Source.$Protected.' ('."\n");
			for($J = 0; $J < count($ColumnName); $J++) {
				fwrite($fileWrite,$Protected.$ColumnName[$J].$Protected.' TEXT'.($J < count($ColumnName)-1?',':'')."\n");
			}
			fwrite($fileWrite, ');'."\n");
		} else if($CurrLine != '') {
			fwrite($fileWrite, 'INSERT INTO '.$Protected.$Source.$Protected.' (');
			for($J = 0; $J < count($ColumnName); $J++) {
				fwrite($fileWrite,$Protected.$ColumnName[$J].$Protected.($J < count($ColumnName)-1?',':''));
			}
			fwrite($fileWrite, ') VALUES (');
			for($Cellule = '', $K = 0,$J = 0; $J < strlen($CurrLine); $J++) {
				if($CurrLine[$J] == $Separator) {
					if($Header) {
						fwrite($fileWrite, "'".str_replace("'","''",$Cellule)."'".($K < count($ColumnName)-1?',':''));
					}
					$Cellule = '';
					$K++;
				} else {
					if(ord($CurrLine[$J]) >= 32) $Cellule .= $CurrLine[$J];
				}
			}
			fwrite($fileWrite, "'".str_replace("'","''",$Cellule)."'".');'."\n");
			$I++;
		}
	}
	fclose($fileWrite);
	fclose($fileRead);
}
 else
echo "Fichier source introuvable !";
?>
