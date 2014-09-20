<?php

// config.php

// caching
$ndz_caching = false;

// fast caching .. set false only if source images might change (rare) 
$ndz_fastcaching = false;

// check includes .. when caching, always check all template includes for changes
$ndz_checktemplateincludes = true;

// use html tidy to filter output
$ndz_usehtmltidy = false;

// what htmltidy character encoding to use.. one of:
//  raw              output values above 127 without conversion to entities
//  ascii            use US-ASCII for output, ISO-8859-1 for input
//  latin0           use US-ASCII for output, ISO-8859-1 for input
//  latin1           use ISO-8859-1 for both input and output
//  iso2022          use ISO-2022 for both input and output
//  utf8             use UTF-8 for both input and output
//  mac              use MacRoman for input, US-ASCII for output
//  win1252          use Windows-1252 for input, US-ASCII for output
//  ibm858           use IBM-858 (CP850+Euro) for input, US-ASCII for output
//  utf16le          use UTF-16LE for both input and output
//  utf16be          use UTF-16BE for both input and output
//  utf16            use UTF-16 for both input and output
//  big5             use Big5 for both input and output
//  shiftjis         use Shift_JIS for both input and output
$ndz_tidyencoding = "utf8";

// html tidy: convert to xhtml
$ndz_tidyxhtml = true;
 
// preferred image type
$ndz_preferredimgtype = IMAGETYPE_PNG;

// default image pixels per inch
$ndz_defaultdpi = 96;

// path to smarty templates, relative to docroot
$ndz_templatepath = "smarty_templates/";

?>