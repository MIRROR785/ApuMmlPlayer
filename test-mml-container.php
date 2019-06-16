<?php
require_once('MmlContainer.php');

$fileName = '/penguin-utf8.mml';
$mmlData = file_get_contents(__DIR__ . $fileName);
$mmlContainer = new MmlContainer();
$parser = new MmlContainerParser($mmlContainer);

try {
	$parser->parse($mmlData);
	var_dump($mmlContainer);
}
catch (Exception $e) {
	echo $fileName.':'.$e->getMessage()."\n";
	echo $parser->row.','.$parser->column.': '.$parser->line."\n";
}
