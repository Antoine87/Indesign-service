<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body style="text">
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Indesign\Entity\IDSP\RunScriptParameters;
use Indesign\Entity\IDSP\ScriptArg;
use Indesign\IndesignService;

try {

    $is = new IndesignService(
        '192.168.42.128',
        18000,
        '/Volumes/VMware Shared Folders/projects/test',
        '/mnt/e/Home/projects/test/'
    );


    $indesignFile = new ScriptArg('indesignFile', $is->prefixMountPath(__DIR__ . '/tests/Dossier Calendrier/Calendrier.indd'));
    $xmlFile = new ScriptArg('xmlFile', $is->prefixMountPath(__DIR__ . '/tests/Output/export-xml.xml'));

    $response = $is->runScriptFileExtended('export-xml.jsx', [$indesignFile, $xmlFile]);

    var_dump($response);

} catch (Exception $e) {
    var_dump($e);
}

?>
</body>
</html>
