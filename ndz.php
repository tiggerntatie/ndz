<?php

define ('HTML_FILE', 1);
define ('XML_FILE', 2);

// some naughty globals
$ndz_query = $_SERVER["QUERY_STRING"];
// default file
$ndz_file = "ndz_index";
// ndz root path
$ndz_rootpath = dirname(__FILE__);
// ndz bin path
$ndz_binpath = $ndz_rootpath."/bin";
// ndz tmp path
$ndz_tmppath = $ndz_rootpath."/tmp";
// ndz doc root
$ndz_docpath = $ndz_rootpath."/docroot";
// ndz archive path .. anywhere you like, really
$ndz_archivepath = realpath($ndz_rootpath."/../");

// array of valid file extensions
$ndz_extensionlist = 
  array(
          "_xml"=>XML_FILE,
          "_htm"=>HTML_FILE, 
          "_html"=>HTML_FILE
        );

// reference to the pagedoc object
$ndz_pagedoc = NULL;

if ($ndz_query != "")
{
  // first argument must be a filename or filename.ext
  // get the filename
  $temp=array_keys($_REQUEST);
  $ndz_file = $temp[0];
  // then, pick off any following query
  $ndz_arguments = array_slice($_REQUEST, 1);
}

// obtain the path to this script, relative to document_root
$ndz_scriptpath = substr($_SERVER["SCRIPT_FILENAME"], strlen($_SERVER["DOCUMENT_ROOT"]));
// check if at root level
if ($slashspot = strrpos($ndz_scriptpath, "/"))
{
  // not root
  $ndz_scriptpath = substr($ndz_scriptpath,0,$slashspot);
}
else
{
  $ndz_scriptpath = "/";
}

// check for a literal file in the ndz path
$ndzfilepath = $ndz_docpath.$ndz_scriptpath."/".$ndz_file;
// first, clean up possible . -> _ conversions
if (($extension = strrchr($ndzfilepath,"_")) && isset($ndz_extensionlist[$extension]))
{
  $filetype = $ndz_extensionlist[$extension];
  //replace the last underscore
  $ndzfilepath[strrpos($ndzfilepath, "_")] = ".";
}
else
{
  // no recognizable extension .. assume xml
  $ndzfilepath = $ndzfilepath.".xml";
  $filetype = XML_FILE;
}


if (is_file($ndzfilepath))
{
  switch ($filetype)
  {
    case HTML_FILE :
      readfile($ndzfilepath);
      break;
    case XML_FILE :
      require "pagedoc.ndz.php";
      $ndz_pagedoc = new cPagedoc($ndzfilepath);
      $ndz_pagedoc->Render();
      break;      
      
      //
      break;
  }
}




?>
