<?php
require_once 'sphinxapi.php';
require_once 'common.php';
require_once 'functions.php';
$sphinx->SetMatchMode( SPH_MATCH_EXTENDED2);
$sphinx->SetRankingMode(SPH_RANK_SPH04);
$sphinx->SetLimits(0, 10);
$indexes = 'historical';
$q = $sphinx->EscapeString(trim($_GET['term']));
$aq = explode(' ',$q);
if(strlen($aq[count($aq)-1])<3){
$query = $q;
}else{
	$query = $q.'*';
}


$result = $sphinx->Query($query, $indexes );
$arr =array();

if($result && $result['total_found'] > 0 )
        {

        foreach($result['matches'] as $r){
                $arr[] = array('id' => utf8_encode($r['attrs']['query_string']),'label' =>utf8_encode( $r['attrs']['query_string']));
        }

        }

echo json_encode($arr);
exit();
