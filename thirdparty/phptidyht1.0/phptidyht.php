<?php


/******************************************************************************
* phptidyht.php Release version 0.5b (beta)
*
* COPYRIGHT 2001 (C) David C. Druffner
* ddruff@gemini1consulting.com
* phptidyht.sourceforge.net
*******************************************************************************
* LICENSE:
* Redistribution and use in source and binary forms, with or without
*  modification, are permitted provided that the following conditions are met:
*
* 1.Redistributions of source code must retain the above copyright notice,
* this list of conditions and the following disclaimer.
*
* 2.Redistributions in binary form must reproduce the above copyright notice,
* this list of conditions and the following disclaimer in the documentation
* and/or other materials provided with the distribution.
*
* 3.The name of the author may not be used to endorse or promote products
* derived from this software without specific prior written permission.
*
*  THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR IMPLIED
*  WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
*  MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
*  EVENT SHALL THE AUTHOR, ANY DISTRIBUTOR, OR ANY DOWNLOAD HOSTING COMPANY BE
*  LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
*  CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
*  SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
*  INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
*  CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
*  ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
*  THE POSSIBILITY OF SUCH DAMAGE.
*******************************************************************************
*******************************************************************************
* NOTE:
* A slightly modified version of ROBODOC is used to generate documentation for
* this code. I have modified the headers.c file in the Robodoc source code to
* set the variable header_markers to equal only /*ROBODOC* as the start of a
* header marker - this avoids confusion with other strings and comments in PHP
* code.
*
* Robodoc is available at http://www.xs4all.nl/~rfsber/Robo/robodoc.html
******************************************************************************/


/**********Set Constants and Variables************************************/


/*ROBODOC*v phpTidyHt/$html_tidy_path
* NAME
*   $html_tidy_path
* FUNCTION
*    Sets the path to the tidy executable (both filename and directory). If
*    you leave the directory out it will search the environment variable PATH
*
* NOTES
*    This is especially necessary in some virtual server environments where
*    the administrator does not allow execution of programs that are not in the
*    standard bin directories without explicit path designation.
*    This must be made global in the phpTidyHt function.
*
***/

   $html_tidy_path="tidy";


/*ROBODOC*d phpTidyHt/MS_WINDOWS
* NAME
*   MS_WINDOWS
* FUNCTION
*   Set to true if server os is Microsoft Windows,
*   False otherwise
*
***/

/* Do os check and set MS_WINDOWS boolean constant */

if(preg_match("/WIN/", PHP_OS)) {
  define("MS_WINDOWS",true);
} else {
  define("MS_WINDOWS",false);
}

/*ROBODOC*d phpTidyHt/HTML_TIDY_ON
* NAME
*   HTML_TIDY_ON
* FUNCTION
*   Turns on the fly HTML Tidy on/off
*   If off, output is just echoed to screen
***/


define ("HTML_TIDY_ON",on);

/*ROBODOC*d phpTidyHt/SAVE_TIDY_ERRORS
* NAME
*   SAVE_TIDY_ERRORS
* FUNCTION
*   Saves all errors to file specified by constant TIDY_LOG
*
***/

define ("SAVE_TIDY_ERRORS",true);


/*ROBODOC*d phpTidyHt/TIDY_LOG
* NAME
*   TIDY_LOG
* FUNCTION
*   Specifies the name of the Html Tidy error log file
*
***/

define ("TIDY_LOG", $DOCUMENT_ROOT."/"."tidy_log.txt");

/*ROBODOC*d phpTidyHt/XML_ON
* NAME
*   XML_ON
* FUNCTION
*   Turns "-asxml" option on or off (conversion to XHTML),
*   can be overridden by $tidy_options
*
***/

define("XML_ON",false);



/*ROBODOC*d phpTidyHt/ALL_TIDY_ERRORS_TO_BROWSER
* NAME
*   ALL_TIDY_ERRORS_TO_BROWSER
* FUNCTION
*   If this is true, all Html Tidy errors and warnings
*   are sent to the browser.
* NOTES
*  Really should only be set to true
*  when debugging HTML.
*
*
***/

define("ALL_TIDY_ERRORS_TO_BROWSER", false);


/*ROBODOC*d phpTidyHt/FATAL_TIDY_ERRORS_TO_BROWSER
* NAME
*   FATAL_TIDY_ERRORS_TO_BROWSER
* FUNCTION
*    If this is true, the Fatal Html Tidy errors are sent to browser
* NOTES
*  Really Should only be set to true when debugging web site.
***/

define("FATAL_TIDY_ERRORS_TO_BROWSER",true);



/*ROBODOC*d phpTidyHt/SHOW_POST_VARS
* NAME
*   SHOW_POST_VARS
* FUNCTION
*    Set to True If this is true, show post variables as part of the
*    identification banner in the error log. Be carefule, as this may result
*    in a very large banner.
*
*
***/

define("SHOW_POST_VARS", true);


/*ROBODOC*v phpTidyHt/$tidy_options
* NAME
*   $tidy_options
* FUNCTION
*    This contains the command line options you are sending to Html Tidy
*
*
*
***/

$tidy_options.="-i --tidy-mark false";  //adds indent and removes tidy meta tag




/*ROBODOC*f phptidyht/phpTidyHt
*
* NAME
*   phpTidyHt - formats string to conform to HTML/XML standards
*
* SYNOPSIS
*
*  boolean phpTidyHt(string $web_page, constant __FILE__,constant __LINE__)
*
* FUNCTION
*
*  Allows you to have all output filtered through a system call to Html Tidy
* (available for both windows and unix, www.w3c.org/People/Raggett/tidy/)
*
*  INPUTS
*
*   $web_page   -contains all the html for a page to be output to the browser.
*
*    __FILE__    -PHP predefined constant giving name of calling file,
*    __LINE__    -PHP predefined constant giving line number of calling fil
*                See the PHP manual for more details.
* RESULT
*      Returns true if able to output to the browser (even if it detected warnings),
*      false if not ()it cannot analyze the file because of fatal errors)
*
* EXAMPLE
*
*          $web_page="<html>Hello";
*          phpTidyHt($web_page, __FILE__,__LINE__);
*
*
*          RESULTS IN THE FOLLOWING BEING SENT TO THE BROWSER:
*
*            <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 3.2//EN">
*            <html>
*              <head>
*                <title></title>
*              </head>
*
*              <body>
*                Hello
*              </body>
*            </html>
*
* NOTES
*
* This funciton requires the following constants and variables to be
* set in the calling script:
*
*  if(preg_match("/WIN/", PHP_OS)) {
*  define("MS_WINDOWS",true);
*  } else {
*  define("MS_WINDOWS",false);
*  }
*
*
* # These are set according to what you want
* define ("HTML_TIDY_ON",true); #turns phpTidyHt on/off
* define ("SAVE_TIDY_ERRORS",true); #saves all errors to TIDY_LOG
* define ("TIDY_LOG", DOCUMENT_ROOT."/"."tidy_log.txt");
* define("XML_ON",false); *have output conform to xml standards
* define("ALL_TIDY_ERRORS_TO_BROWSER",false);
* define("FATAL_TIDY_ERRORS_TO_BROWSER",true);
*
* # Show Post Variables as part of the banner - Be Careful
* #  if turned on as this may result in a large banner
* define("SHOW_POST_VARS", false);
*
* $tidy_options.="-i --tidy-mark false"; #adds indent and removes tidy meta tag
* $html_tidy_path=""; //set this to the path of your tidy executable
***/

function phpTidyHt($web_page, $calling_file, $calling_line) {

global $html_tidy_path;
global $tidy_options;
global $HTTP_POST_VARS;
global $HTTP_GET_VARS;

/*Dump it to screen if we are not using Html Tidy */

if(!HTML_TIDY_ON) {
  echo $web_page;
  return true;
}

/*Generate a random temporary file name */
$tidy_temp_file = "./".md5(uniqid (rand())).".tmp";

/* Verify temporary file name is useable */

if (!is_writeable(dirname($tidy_temp_file))
    or !is_readable(dirname($tidy_temp_file))) {

  echo "
  <html>
  <body>
  <h2>ERROR: Cannot Use Html Tidy:</h2>
  <br>
  Cannot write and read to ".dirname($tidy_temp_file).
  " Please verify permissions and make sure the path
  is valid.
  </body>
  </html>
  ";
  return false;
}


/* Add quotes for system echo call */


  //Get page title if it exists

  if(!$page_title=getTitle($web_page)) {
  $page_title=
  "(Page title is either blank or unable to be determined by phpTidyHt)";

  } else {
  $page_title="\"".$page_title."\""; //add quotes for better display
  }


$web_page="\"".$web_page."\"";


if (XML_ON) {
$tidy_options.=" -asxml";   //add option to convert HTML page to xml

}



/*************Begin Build of Identification Banners***************************/

/* Add Post Variables and Get Strings to Identification Banner.
Post String display can be turned on/off by SHOW_POST_VARS constant */

while(list($key, $val) = each($HTTP_POST_VARS)) {
            $key = stripslashes($key);
            $val = stripslashes($val);
            $key = urlencode($key);
            $val = urlencode($val);
            $postString .= "$key=$val&";
            }

while(list($key, $val) = each($HTTP_GET_VARS)) {
            $key = stripslashes($key);
            $val = stripslashes($val);
            $key = urlencode($key);
            $val = urlencode($val);
            $getString .= "$key=$val&";
            }



if (!$HTTP_POST_VARS) {
$Post_Text="No Post Variables Sent";
} elseif (SHOW_POST_VARS) {
$Post_Text="Post Variables: $postString";
}  else {
$Post_Text="Post Variables: Sent But Option To Display Them is Off";
}

if ($HTTP_GET_VARS) {
$Get_Text="Get Variables: $getString";
} else {
$Get_Text="Get Variables: No Get Variables Sent";
}




  $time_generated= date("m/d/Y").":".date("h:i:s:A");
  $begin_banner="
*****************************************************************************\r
Html Tidy Errors and Warnings for the Following PHP Generated Page:
Page Title: $page_title
Script Name: $calling_file
$Get_Text
$Post_Text
Script Line: $calling_line
Time: $time_generated
*******************************************************************************
";

  $end_banner="
*******************************************************************************
End of Html Tidy Errors and Warnings for page entitled $page_title
generated by:
$calling_file, $calling_line at $time_generated
*******************************************************************************
";


/*************End Build of Identification Banners******************************/

   if (SAVE_TIDY_ERRORS) {
   $tidy_error_destination=TIDY_LOG;
   appendToFile($begin_banner,$tidy_error_destination);
   } else {
   $tidy_error_destination="/dev/null";
   }

  /* Execute Html Tidy */




  exec("echo $web_page|$html_tidy_path $tidy_options 2>>"
      .$tidy_error_destination,$tidy_page, $result_code);

  $tidy_page=implode("\r\n",$tidy_page); //convert array to string

  if (SAVE_TIDY_ERRORS) {
  appendToFile($end_banner,$tidy_error_destination);
  }


  /*
  Send output and/or error messages to browser depending on result code
  0 - Success
  1 - Warnings (non fatal)
  2 - Errors  (fatal but also includes warnings)
  */


  switch ($result_code) {

  case 0:    //Successful - just echo to browser

      $return_value=true;

      break;

  case 1:      //Html Tidy Warnings, but no errors



      if (ALL_TIDY_ERRORS_TO_BROWSER) {

      $tidy_result_summary=
      "<h2>There were minor errors in your HTML which Html Tidy has fixed.
      </h2>\r\n";

      exec("echo $web_page|$html_tidy_path -e 2>$tidy_temp_file");

      $tidy_errors=
      nl2br(htmlentities(implode("",file("$tidy_temp_file"))));

      $add_string=
      nl2br($begin_banner).$tidy_result_summary.$tidy_errors.nl2br($end_banner);

      if(!$tidy_page=insertIntoHtmlBody($add_string,$tidy_page)) {
        $tidy_page=
        "<html><body>$add_string"."Unable to echo the page to the screen.
         </body></html>";
      }
      deleteFile($tidy_temp_file);
      }

      $return_value=true;

      break;

  case 2:  //Html Tidy errors

      if (ALL_TIDY_ERRORS_TO_BROWSER) {
      $warning_options="--show-warnings true";
      } else {
      $warning_options="--show-warnings false";

      }


      $tidy_result_summary="
      <h2>
      FATAL ERROR(s) IN PAGE:
      <em>Html Tidy was not able to produce a valid page.</em>
      <br>
      Errors listed below:
      </h2>";

      /* Get errors, insert them into the tidied page,
      and echo to browser */

      exec("echo $web_page|$html_tidy_path $warning_options -e 2>".
           $tidy_temp_file);
      $tidy_errors=nl2br(htmlentities(implode("",file("$tidy_temp_file"))));
      $add_string=
      nl2br($begin_banner).$tidy_result_summary.$tidy_errors.
      nl2br($end_banner);

      $tidy_page=
      "<html><title>FATAL ERRORS ON PAGE</title>
      <body>$add_string
      </body></html>";


      deleteFile($tidy_temp_file);

      $return_value=false;

    break;
  default:

      $tidy_result_summary=
      "<h2>Html Tidy gave the following error messages: $result_code</h2>";
      $add_string=
      nl2br($begin_banner).$tidy_result_summary.
      $tidy_errors.nl2br($end_banner);

      if(!$tidy_page=insertIntoHtmlBody($add_string,$tidy_page)) {
        $tidy_page="<html><body><h2>$add_string".
        "Html Tidy unable to analyze the file and echo the page to the screen.
        </h2></body></html>";
      }
      $return_value=false;

    break;


  }

  if(!$tidy_page) {
  $tidy_page="<html><body>$add_string".
  "<h2>Html Tidy unable to analyze the file and echo the page to the screen.
  </h2></body></html>";
  $return_value=false;
  } else {

  echo $tidy_page;
  return return_value;
  }



/*****************************End Body of function phpTidyHt******************/

}


/*ROBODOC*f phptidyht/deleteFile
* NAME
*   deleteFile - deletes file in unix and dos
*
* SYNOPSIS
*  boolean deleteFile(string $file_name)
*
* FUNCTION
*   Deletes file $filename in dos or unix
* INPUTS
*    $file_name - name of file to be deleted
*
* RESULT
*    Returns true on success, false on failure
*
* NOTES
*
*
*   Must define MS_WINDOWS constant like so in the calling script:
*
*
*   if(preg_match("/WIN/", PHP_OS)) {
*     define("MS_WINDOWS",true);
*    } else {
*    define("MS_WINDOWS",false);
*   }
***/

function deleteFile($file_name) {

 if (!file_exists($file_name)) {
  return true; //if file doesn't exist than don't worry about it
 }
 clearstatcache(); //clear cache since will be checking same file again

 if(is_writeable($file_name)) {

  if(MS_WINDOWS) {
    exec("del $file_name");
  } else {
    unlink($file_name);
  }

  return true;

 } else {
 return false;    //return false if errors - can't write to path
 }

}




/*ROBODOC*f phptidyht/getTitle
* NAME
*   getTitle
*  SYNOPSIS
*    string getTitle($string $html_page)
* FUNCTION
* Attempts to get the title of a web page found between the <title>
* and </title> tags.
*  INPUTS
*    $html_page - string containing all the html of a web page
*
*  RESULT
*    Returns string containing title of web page, or false if none found
***/

function getTitle($html_page) {

$html_page=preg_replace("/\s/"," ",$html_page);
//This doesn't seem to correctly evaluate without the above replace
if(!preg_match("/^.*<title>(.*)<\/.*title.*>.*/i",$html_page,$matches)) {
return false;
}  else {
$title=$matches[1]; //Title Content will be the first element
return trim($title);
}
}



/*ROBODOC*f phptidyht/insertIntoHtmlBody
* NAME
*    insertIntoHtmlBody
* SYNOPSIS
*    string insertIntoHtmlBody(string $add_string, string $html_page)
* FUNCTION
*   Attempts to insert $add_string into first lines of a web page
*   after the <body>tag.
* INPUTS
*    $html_page  -  contains all the html of a web page
*  RESULT
*    Returns string containing the $html_page with the $add_string
* added. If the body tag cannot be found, then the return
* string will be the same as $html_page
***/

function insertIntoHtmlBody($add_string, $html_page) {

return str_replace("<body>","<body>\r\n$add_string\r\n", $html_page);

}


/*ROBODOC*f phptidyht/appendToFile
* NAME
*    appendToFile
* SYNOPSIS
*     boolean appendToFile(string $new_file_contents, string $file_name);
*
* FUNCTION
*     Appends $new_file_contents to file named by $file_name.
* INPUTS
*    $new_file_contents  -  string containing text to be added to file
     $file_name          -  path and filename of file to be appended to
*  RESULT
*    Returns true on success, false on failure
***/

function appendToFile($new_file_contents, $file_name)   {

if(
    (file_exists($file_name) and !is_writeable($file_name))
    or
    !is_writeable(dirname($file_name))) {

return false;
}
if (!$fp= fopen( $file_name,"a+")) { //if can't open, return false
    return false;
} elseif(!fwrite($fp,$new_file_contents)) { //if can't write return false
  return false;
}

fclose ($fp);
return true;
}




?>

