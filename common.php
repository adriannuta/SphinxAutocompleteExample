<?php
define ( "FREQ_THRESHOLD", 40 );
define ( "SUGGEST_DEBUG", 0);
define ( "LENGTH_THRESHOLD", 2 );
define ( "LEVENSHTEIN_THRESHOLD", 2 );
define ( "TOP_COUNT", 1 );
define ("SPHINX_20",false);
//database PDO
$ln = new PDO( 'mysql:host=127.0.0.1;dbname=MYDB;charset=utf8', 'MYUSER', 'MYPASS' );

//Sphinx PDO
$ln_sph = new PDO( 'mysql:host=127.0.0.1;port=9306' );
?>