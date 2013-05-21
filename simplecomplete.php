<?php
require_once 'sphinxapi.php';
require_once 'common.php';
require_once 'functions.php';
$docs = array();
if (isset($_GET['query']) && trim($_GET['query']) != '') {
	$query_str = trim($_GET['query']);
    $indexes = 'simplecompletefull';
    $query = $sphinx->EscapeString($query_str);
    $sphinx->SetRankingMode(SPH_RANK_SPH04);
    $sphinx->SetMatchMode(SPH_MATCH_EXTENDED2);
    $sphinx->setFieldWeights(array('title' => 1000, 'content' => 1));
    $sphinx->SetLimits(0, 10);
    $sphinx->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC,@id desc");
    $result = $sphinx->Query($query, $indexes);
    $ids = array();
    $tmpdocs = array();
    if ($result && $result['total_found'] > 0) {
        QueryToHistory($query_str,$ln,$ln_sph);
        foreach ($result['matches'] as $k => $v) {
            $ids[] = $v['id'];
        }
        $q = "SELECT id,3text as title ,4longtext as contentdec,category FROM productsinfo WHERE id IN  (" . implode(',', $ids) . ")";
        $r = mysqli_query($ln, $q);

        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            $tmpdocs[$row['id']] = array('title' => $row['title'], 'content' => $row['contentdec'], 'category' => $row['category']);
        }
        foreach ($ids as $id) {
            $docs[] = $tmpdocs[$id];
        }
    }
}
?> 
<?php
$title = 'Demo simple autocomplete on title';
include 'template/header.php';
?>
<div class="container">
    <ul class="nav nav-pills">
        <li class="active" ><a href="simplecomplete.php">Autocomplete on titles</a></li>
        <li ><a href="suggestcomplete.php">Autocomplete on titles + suggestion</a></li>
		<li><a href="suggestcompleteexcerpts.php">Autocomplete on titles + suggestion + excerpts</a></li>
        <li><a href="historiccomplete.php">Autocomplete on history search</a></li>
    </ul>
    <header>
        <h1>Simple autocomplete on title </h1>
   </header>
    <div class="row">
        <div class="span9">
			 <p>Autocomplete is made using star on a titles index (with infixes).</p>
            <p>Start typing in the field below</p>
            <div class="well form-search">
                <form method="GET" action="" id="search_form">
                    <input type="text"  class="input-large"  name="query" id="suggest" autocomplete="off">
                    <input type="submit" class="btn btn-primary" id="send" name="send" value="Submit">
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <?php if (count($docs) > 0): ?>
            <p class="lead">Showing first 10 results:</p>
            <?php foreach ($docs as $doc): ?>
                <div class="span9">
                    <div class="container">
                        <h3><?= $doc['title'] ?></h3>
                        <p><?= substr(strip_tags($doc['content']), 0, 500) . '...' ?></p>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (isset($_GET['query']) && $_GET['query'] != ''): ?>
            <p class="lead"> Nothing found!</p>
        <?php endif; ?>
    </div>
<?php 
$ajax_url = 'ajax_suggest.php';
include 'template/footer.php'; 
?>