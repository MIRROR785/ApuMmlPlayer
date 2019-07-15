<?php
require_once('PseudoApu.php');
require_once('MmlContainer.php');
require_once('MmlSequencer.php');

$fileName = '/penguin-utf8.mml';
$mmlData = file_get_contents(__DIR__ . $fileName);
$container = new MmlContainer();
$parser = new MmlContainerParser($container);

try {
	$parser->parse($mmlData);
	var_dump($container);

	$sequencer = new MmlSequencer($container, MmlMusic::TYPE_SE, 1);
	$sequencer->setUp();
}
catch (Exception $e) {
	echo $fileName.':'.$e->getMessage()."\n";
	echo $parser->row.','.$parser->column.': '.$parser->line."\n";
}
