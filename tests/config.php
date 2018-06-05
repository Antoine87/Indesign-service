<?php

/*
* This file is part of the Indesign-API package.
*
* (c) Antoine87 <antoine87@openmail.cc>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

return [

    // Indesign server connection
    'host' => '192.168.42.128',
    'port' => 18000,

    // Path prefix for mounted folder
    'indesignPrefix' => '/Volumes/VMware Shared Folders/projects/test',
    'localPrefix' => '/mnt/e/Home/projects/test/',

    // Export XML
    'exportXmlFileInput' => '/Volumes/VMware Shared Folders/projects/test/tests/Dossier Calendrier/Calendrier.indd',
    'exportXmlFileOutput' => '/Volumes/VMware Shared Folders/projects/test/tests/Dossier Calendrier/export-xml.xml',
    'exportXmlFileOutputReadFrom' => __DIR__ . '/Output/export-xml.xml',

    // Export XML mounted folder
    'exportXmlFileInputMF' => __DIR__ . '/Dossier Calendrier/Calendrier.indd',
    'exportXmlFileOutputMF' => __DIR__ . '/Output/export-xml-mounted.xml'

];
