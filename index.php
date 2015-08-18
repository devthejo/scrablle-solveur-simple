<?php
/*
 * Scrablle Solveur Simple
 *
 * Permet de trouver les mots français que l'on peut faire avec un jeu de lettres
 *
 * @package Scrablle Solveur Simple
 * @version 1.0
 * @link http://github.com/surikat/scrablle-solveur-simple/
 * @author Jo Surikat <jo@surikat.pro>
 * @website http://wildsurikat.com
 */

//$useDico = 'dico';
$useDico = 'dico-light'; //mots d'au moins 3 lettres et inférieurs à 7
header('Content-Type: text/html; charset=utf-8');
$letters = isset($_POST['letters'])?$_POST['letters']:'';
?>
<form method="POST">
	<label for="letters">LETTRES</label>
	<input type="text" name="letters" id="letters" value="<?php echo strtoupper($letters);?>">
	<input type="submit" value="CHERCHER">
</form>
<?php
if($letters){
	set_time_limit(0);
	
	$letters = strtolower($letters);
	$combinaisons = [];	
	function tester($word,$letters,&$combinaisons){
		$c = strlen($letters);
		for($i=0;$i<$c;$i++){
			$word2 = $word;
			$letters2 = $letters;
			$word2 .= substr($letters2,$i,1);
			$letters2 = substr($letters2,0,$i).substr($letters,$i+1);
			$combinaisons[] = $word2;
			if(!empty($letters2)){
				tester($word2,$letters2,$combinaisons);
			}
		}
	}
	tester('',$letters,$combinaisons);
	$combinaisons = array_unique($combinaisons);
	
	$dico = explode("\n",file_get_contents(__DIR__.'/'.$useDico.'.txt'));
	$words = [];
	foreach($combinaisons as $combinaison){
		if(strlen($combinaison)>2){
			if(array_search($combinaison,$dico)!==false){
				$words[] = $combinaison;
			}
		}
	}
	
	usort($words,function($a,$b){
		return strlen($a)<strlen($b);
	});
	echo '<p style="font-size:12px;">'.count($words).' MOTS TROUVÉS AVEC "'.strtoupper($letters).'" EN '.sprintf("%.2f",(microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"])).' secondes</p>';
	echo '<ul style="list-style-type:none;font-size:16px;">';
	foreach($words as $word){
		echo '<li>'.strtoupper($word).'</li>';
	}
	echo '</ul>';
}
?>