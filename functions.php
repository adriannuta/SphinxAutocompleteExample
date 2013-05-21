<?php

function BuildTrigrams($keyword) {
    $t = "__" . $keyword . "__";
    $trigrams = "";
    for ($i = 0; $i < strlen($t) - 2; $i++)
        $trigrams .= substr($t, $i, 3) . " ";
    return $trigrams;
}

function BuildPhraseTrigrams($keyword) {
	$keyword = str_replace(' ','_',$keyword);
	    $t =  $keyword ;
    $trigrams = "";
    for ($i = 0; $i < strlen($t) - 2; $i++)
        $trigrams .= substr($t, $i, 3) . " ";
		
    return $trigrams;
}

function MakeSuggestion($keyword) {
    $trigrams = BuildTrigrams($keyword);
    $query = "\"$trigrams\"/1";
    $len = strlen($keyword);

    $delta = LENGTH_THRESHOLD;
    $cl = new SphinxClient ();
    $cl->SetServer("127.0.0.1", 9352);
    $cl->SetMatchMode(SPH_MATCH_EXTENDED2);
    $cl->SetRankingMode(SPH_RANK_WORDCOUNT);
    $cl->SetFilterRange("len", $len - $delta, $len + $delta);
    $cl->SetSelect("*, @weight+$delta-abs(len-$len) AS myrank");
    $cl->SetSortMode(SPH_SORT_EXTENDED, "myrank DESC, freq DESC");
    $cl->SetArrayResult(true);

    // pull top-N best trigram matches and run them through Levenshtein
    $res = $cl->Query($query, "suggest", 0, TOP_COUNT);

    if (!$res || !$res["matches"])
        return false;
    // further restrict trigram matches with a sane Levenshtein distance limit
    foreach ($res["matches"] as $match) {
        $suggested = $match["attrs"]["keyword"];
        if (levenshtein($keyword, $suggested) <= LEVENSHTEIN_THRESHOLD)
            return $suggested;
    }
    return $keyword;
}

function MakePhaseSuggestion($keywords) {
    $trigrams = BuildPhraseTrigrams($keywords);
    $query = "\"$trigrams\"/1";
    $cl = new SphinxClient ();
    $cl->SetServer("127.0.0.1", 9352);
    $cl->SetMatchMode(SPH_MATCH_EXTENDED2);
    $cl->SetRankingMode(SPH_RANK_WORDCOUNT);
    $cl->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC,cnt desc");
    $cl->SetArrayResult(true);
    $res = $cl->Query($query, "historical", 0, 1);

    if (!$res || $res['total_found'] == 0)
        return false;

    return $res['matches'][0]['attrs']['query_string'];
}

function QueryToHistory($query,$ln,$ln_sph) {
	$cl = new SphinxClient ();
    $cl->SetServer("127.0.0.1", 9352);
	$keywords = $cl->BuildKeywords($query,'historical',false);
	$keys = array();
	foreach($keywords as $k)
	{
		$keys[] = $k['normalized'];
	}
	$keys = implode(' ' ,$keys);
	$keyscrc = crc32($keys);
	if($keyscrc < 0) $keyscrc += 4294967296; //stupid bug on 64bit machines
    $q = "SELECT * FROM historical WHERE query='" . $keys . "' LIMIT 1";
    $r = mysqli_query($ln, $q);
    if (mysqli_num_rows($r) > 0) {
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
        $q = "UPDATE historical set count=count+1 WHERE id=" . $row['id'];
        mysqli_query($ln, $q);
       $q = "REPLACE INTO historical (id,query,query_string,cnt)  VALUES('" . $keyscrc. "','" . $keys . "','" . $keys . "'," . ($row['count'] + 1) . ")";
        mysqli_query($ln_sph, $q);
    } else {
        $q = "INSERT INTO  historical(query,count) VALUES('" . $query . "',1)";
        mysqli_query($ln, $q);
        $id = mysqli_insert_id($ln);
        $q = "INSERT INTO  historical (id,query,query_string,cnt) VALUES('$keyscrc','" . $keys . "','" . $keys . "',1)";
        mysqli_query($ln_sph, $q);
    }
}
?>