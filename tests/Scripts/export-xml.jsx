/**
 * File: export-xml.jsx
 * Author: Antoine87
 */

var indesignFile = new File(app.scriptArgs.getValue('indesignFile'));
var xmlFile = new File(app.scriptArgs.getValue('xmlFile'));

var doc = app.open(indesignFile);

var nbPages = {
    'nbPages': doc.pages.length
};

doc.exportFile(ExportFormat.XML, xmlFile);

doc.close();

nbPages;
