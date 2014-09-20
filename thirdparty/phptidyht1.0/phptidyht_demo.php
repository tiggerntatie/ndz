<?php

/***************************************************
phptidy_demo.php
Copyright (c) 2001 David c. Druffner

A demonstration of phpTidyHt available at phptidyht.sourceforge.net

***********************/
include("phptidyht.php");

$tidied_page="<a href=$PHP_SELF"."?tidy=yes>Tidied Page</a>";
$untidied_page="<a href=$PHP_SELF"."?tidy=no>Untidied Page</a>";
$xml_page="<a href=$PHP_SELF"."?tidy=xml>XHTML Page</a>";
$fatal_page="<a href=$PHP_SELF"."?tidy=fatal>
                  Page With Fatal HTML Errors</a>";
if (file_exists(TIDY_LOG)) {
$error_log_text="<a href=$PHP_SELF?tidy=error_log>View the Html Tidy Error Log
</a> (".TIDY_LOG.")<br><br>
<a href=$PHP_SELF?tidy=log_clear>Delete the Html Tidy Error Log</a><br><br>";
}

//Set title of page
switch($tidy) {

  case "yes":

  $page_type="Tidied Page";


  break;



  case "no":
  $page_type="Untidied Page";

  break;


  case "xml":
  $page_type="XHTML Page";

  break;

  case "fatal":
  $page_type="Bad HTML Page";

  break;

  case "error_log":
  $page_type=TIDY_LOG;

  break;

  default:

  $page_type="Untidied Page";


}





$demo_page="
<html><head><title>Demo of phpTidyHt</title></head>
<body>
<br>
<br>
<a href=\"./\">Back to Main Page</a>
<h2><center>DEMO OF phpTidyHt SCRIPT ($page_type)</center></em></h2>

<table border=1><tr><td><p>This is a demo of the phpTidyHt function which utilizes <a href=\"http://www.w3c.org/People/Raggett/tidy/\">HTML Tidy</a>. Select <em> view source</em> in your browser. If this page is untidied, you will notice that it has no ending body or html tags and you will notice the source is jumbled and runs off the screen. If it is tidied, you will notice that the above errors have been fixed and that the source
code is nicely formatted. If you chose the XHTML conversion option, you will notice that the HTML code has been converted to XHTML.</td></tr></table>

<!-- Try this if you like, but commented out for normal demo
<em><h3>Example of Post Form </h3></em>
If submitted, the following form will generate a post identification string in the error log.
<form action=$PHP_SELF method=post><input type=\"text\" name=\"demo_post_text_box\" value='Sample Data'><input type=hidden name=tidy value=yes><input type=submit></form

-->

<p>This page as filtered through HTML Tidy using phpTidyHt: $tidied_page<br>
This page as unfiltered Html: $untidied_page<br>This page as converted to XHTML: $xml_page<br>This page with a fatal error added: $fatal_page
<p>$error_log_text


<b>This Demo has the following options turned on (through setting constants and variables in script):</b><br><br>
<b>HTML_TIDY_ON: true</b>  <em><br>This turns HTML Tidy parsing on/off</em><br><b>SAVE_TIDY_ERRORS: true</b> <em><br>This turns logging of errors to a text file on</em><br>
<b>TIDY_LOG: tidy_log.txt</b> <em><br>This is the name of the log file</em><br><b>XML_ON: false</b> <br><em>This turns conversion to XHTML off, but can be overridden by the variable \$tidy_options</em><br><b>ALL_TIDY_ERRORS_TO_BROWSER: false</b> <em><br>If true, this outputs all HTML Tidy errors to the browser when you view the page. This is pretty verbose so you usually want to turn this off as most errors are automatically fixed by Html Tidy, especially if you have logging turned on. This overrides FATAL_TIDY_ERRORS_TO_BROWSER.</em><br>
<b>FATAL_TIDY_ERRORS_TO_BROWSER: true</b> <em><br>If true, this outputs only fatal errors to the browser - errors that would prevent Html Tidy from showing the page, usually indicating that some browsers may not be able to view the page either.</em><br><b>SHOW_POST_VARS: true</b> <em>Turns on and off the display of post variables in the identification banner of the error log. May want to turn this off in actual use as it could generate a very large banner</em><br>
<b>$"." tidy_options :\" -i --tidy-mark false\"</b> <em><br>This sets basic Html Tidy options. Here indent is turned on and Html Tidy meta tags are removed. For a full list of options
see BOTH tidy -h AND the <a href=\"http://www.w3c.org/People/Raggett/tidy/\">Html Tidy Home Page</a></em>(neither one of them
gives you a full list of the options available).
<br>
<br>
<a href=\"./\">Back to Main Page</a>
";




switch ($tidy) {

  case "yes":

  phpTidyHt($demo_page, __File__, __LINE__);

  break;



  case "no":
  echo ($demo_page);
  break;


  case "xml":
  $tidy_options.=" -asxml";   //overides XML_ON constant
  phpTidyHt($demo_page, __File__, __LINE__);
  break;

  case "fatal":
  $demo_page=$demo_page."<not a valid tag>";
  phpTidyHt($demo_page, __File__, __LINE__);
  break;

  case "error_log":
  if(!file_exists(TIDY_LOG)){
  $contents="No Error Log Exists at This Time";
  } else {
  $contents=htmlentities(implode("",file(TIDY_LOG)));
  }

  echo "
  <html><title>
  $page_type
  </title>
  <body>
  <a href=$PHP_SELF>Return To Demo Page</a>"
  .nl2br($contents).
  "<p><a href=$PHP_SELF>
  Return To Demo Page</a></body></html>";
  exit;

  case "log_clear":

  deleteFile(TIDY_LOG);
  echo $demo_page;
  break;



  default:

  echo ($demo_page);
}




?>

