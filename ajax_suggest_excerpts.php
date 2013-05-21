<?php
require_once 'sphinxapi.php';
require_once 'common.php';
require_once 'functions.php';
$sphinx->SetMatchMode( SPH_MATCH_EXTENDED2);
$sphinx->SetRankingMode(SPH_RANK_SPH04);
$sphinx->SetLimits(0, 10);
$indexes = 'simplecomplete';
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
		$docs =  array();
		foreach($result['matches'] as $r)
		{
			$docs[] = $r['attrs']['title'];
		}
		$docs = $sphinx->BuildExcerpts($docs,'simplecomplete',$query);
        foreach($docs as $r){
                $arr[] = array('id' => utf8_encode($r),'label' =>utf8_encode( $r));
        }

        }

echo json_encode($arr);
exit();
