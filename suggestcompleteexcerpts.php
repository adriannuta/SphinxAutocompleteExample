<?php

require_once 'common.php';
require_once 'functions.php';

$docs = array();
$mis = array();
$suggest = false;
if (isset($_GET['query']) && trim($_GET['query']) != '') {
	$query = trim($_GET['query']);
	$indexes = 'simplecompletefull';

	$stmt = $ln_sph->prepare("SELECT * FROM $indexes WHERE MATCH(:match)  LIMIT 0,10 OPTION ranker=sph04,field_weights=(title=100,content=1)");
	$stmt->bindValue(':match', $query,PDO::PARAM_STR);
	$stmt->execute();
	$rows = $stmt->fetchAll();

	$meta = $ln_sph->query("SHOW META")->fetchAll();

	$ids = array();
	$tmpdocs = array();
	if (count($rows) > 0) {
		foreach ($rows as $v) {
			$ids[] = $v['id'];
		}
		$q = "SELECT id, title , content FROM docs WHERE id IN  (" . implode(',', $ids) . ")";
		foreach ($ln->query($q) as $row) {
			$tmpdocs[$row['id']] = array('title' => $row['title'], 'content' => $row['content']);
		}
		foreach ($ids as $id) {
			$docs[] = $tmpdocs[$id];
		}
	} else {
		$words = array();
		foreach($meta as $m) {
			if(preg_match('/keyword\[\d+]/', $m['Variable_name'])) {
				preg_match('/\d+/', $m['Variable_name'],$key);
				$key = $key[0];
				$words[$key]['keyword'] = $m['Value'];
			}
			if(preg_match('/docs\[\d+]/', $m['Variable_name'])) {
				preg_match('/\d+/', $m['Variable_name'],$key);
				$key = $key[0];
				$words[$key]['docs'] = $m['Value'];
			}
		}
		$suggest = MakePhaseSuggestion($words, $query, $ln_sph);
	}
}
?>
<?php
$title = 'Demo simple autocomplete on title';
include 'template/header.php';
?>
<div class="container">
	<ul class="nav nav-pills">
		<li><a href="simplecomplete.php">Autocomplete on titles</a></li>
		<li><a href="suggestcomplete.php">Autocomplete on titles + suggestion</a>
		</li>
		<li class="active"><a href="suggestcompleteexcerpts.php">Autocomplete
				on titles + suggestion + excerpts</a></li>
	</ul>
	<header>
		<h1>Simple autocomplete on title</h1>
	</header>
	<div class="row">
		<div class="span9">
			<p>Autocomplete is made using star on a titles index (with infixes).</p>
			<p>Start typing in the field below</p>
			<div class="well form-search">
				<form method="GET" action="" id="search_form">
					<input type="text" class="input-large" name="query" id="suggest"
						autocomplete="off" value="<?=isset($_GET['query'])?htmlentities($_GET['query']):''?>"> <input
						type="submit" class="btn btn-primary" id="send" name="send"
						value="Submit">
				</form>
			</div>
		</div>
	</div>
	<?php if ($suggest): ?>
	<div class="row">
		<div class="span9">
			<p>
				Did you mean <i><a href="?query=<?= $suggest; ?>"><?= $suggest; ?> </a>
				</i>?
			</p>
		</div>
	</div>
	<?php endif; ?>
	<div class="row">
		<?php if (count($docs) > 0): ?>
		<p class="lead">Showing first 10 results:</p>
		<?php foreach ($docs as $doc): ?>
		<div class="span9">
			<div class="container">
				<h3>
					<?= $doc['title'] ?>
				</h3>
				<p>
					<?= substr(strip_tags($doc['content']), 0, 500) . '...' ?>
				</p>

			</div>
		</div>
		<?php endforeach; ?>
		<?php elseif (isset($_GET['query']) && $_GET['query'] != ''): ?>
		<p class="lead">Nothing found!</p>
		<?php endif; ?>
	</div>
	<?php 
	$ajax_url = 'ajax_suggest_excerpts.php';
	include 'template/footer_excerpts.php';
	?>
