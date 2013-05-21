<?php
define ( "FREQ_THRESHOLD", 40 );
define ( "SUGGEST_DEBUG", 0);
define ( "LENGTH_THRESHOLD", 2 );
define ( "LEVENSHTEIN_THRESHOLD", 2 );
define ( "TOP_COUNT", 1 );
$ln = mysqli_connect('localhost','myuser','mypassword','mydb');
$ln_sph = mysqli_connect('127.0.0.1','','','',9306);

$sphinx = new SphinxClient();
$sphinx->SetServer( "127.0.0.1", 9312 );
$sphinx->SetArrayResult(true); 
?>