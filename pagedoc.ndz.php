<?php
// pagedoc.ndz.php
// ndz page definition file parsing class

if (PHP_VERSION>='5')
 require_once('domxml-php4-to-php5.php');

class cPagedoc
{

  var $m_docpath;
  var $m_dom;
  var $m_scriptpath;
  var $m_templatepath;
  var $m_contentpath;
  var $m_sitecontentpath;
  var $m_smarty;
  var $m_docrootpath;
  var $m_sitecontent;
  var $m_content;
  var $m_docdate; // file last changed timestamp
  var $m_cachedir; // dir of final cached html image
  var $m_cachepath; // path to tinal cached html image
  var $m_currentdir;  // absolute path to the script
  
  function cPagedoc($docpath)
  {
    $this->m_docpath = $docpath;
    $this->m_docdate = filemtime($docpath);
    $xmlcontents = file_get_contents($docpath);
    $xmlcontents = "<?xml version='1.0'?>".$xmlcontents;
    $this->m_currentdir = substr($_SERVER["SCRIPT_FILENAME"], 0, strrpos($_SERVER["SCRIPT_FILENAME"], "/"));
    if (!$this->m_dom = domxml_open_mem($xmlcontents))
    {
      echo("error opening xml page definition");
      exit;
    }
  }

  // remove "ndz_" prepended to node names
  function RemoveNDZ($text)
  {
    return substr($text,4);
  }
  
  // add properties to $this, dynamically
  // properties are prepended with script_
  function AddProperty($propname, $propvalue)
  {
    $evalstr = 
      'if (!is_array($this->script_'.$propname.'))$this->script_'.$propname.' = array();'.
      'array_push($this->script_'.$propname.', "'.$propvalue.'");';
    
    eval($evalstr);
  }
  

  // determine directory of cached html
  function GetNDZCacheDir()
  {
    global $ndz_scriptpath; // document root relative path to script
    global $ndz_docpath;  // document root
    // build a path that is off docrootpath/ndz_cache, relative to docrootpath
    return $this->m_docrootpath."/ndz_cache".
      substr($ndz_docpath.$ndz_scriptpath, strlen($this->m_docrootpath))."/";
  }
  
  
  // determine processed image path
  function GetProcessedImagePath($path)
  {
    global $ndz_file;  // root name of the page definition file
    
    $name = basename($path);
    $ext = substr($name, strrpos($name,"."));
    $name = basename($name, $ext);
    
    // obtain a directory listing of *.pagedocrootname.img.*
    return $this->m_currentdir."/".$name.".".$ndz_file.".img".$ext;
  }


  // look for images in the script folder that 
  function PurgeProcessedImages()
  {
    // obtain a directory listing of *.pagedocrootname.img.*
    foreach(glob($this->GetPRocessedImagePath("*.*")) as $img)
    {
      if (filemtime($img) < $this->m_docdate)
      {
        // delete files older than the pagedoc
        unlink($img);
      }
    }
  }
  

  
  // GetFilePath
  //
  // search for the specified filename
  // order is, 
  // 1. check for absolute reference
  // 2. check relative to current pagedoc directory
  // 3. check relative to the site defined doc root
  function GetFilePath($filename)
  {
    global $ndz_rootpath;
    global $ndz_docpath;
    global $ndz_scriptpath;
    
    $ret = realpath($filename);
    
    // check existance of absolute reference
    if (!is_file($ret))
    {
      // check for existance off current doc directory
      $ret = realpath($ndz_docpath.$ndz_scriptpath."/".$filename);
      if (!is_file($ret))
      {
        // check for reference relative to docroot!
        $ret = realpath($this->m_docrootpath."/".$filename);
        if (!is_file($ret))
        {
          // no path exists!
          $ret = "";
        }
      }
    }
    return $ret;
  }
  
  // adjust the "docdate" according to file in path
  function BumpDocdate($path)
  {
    $pathdate = filemtime($path);
    if ($this->m_docdate < $pathdate)
    {
      $this->m_docdate = $pathdate;
    }
  }
    
  // adjust the docdate for template includes
  function BumpDocdateForTemplateIncludes($templatepath, $template)
  {
    global $ndz_checktemplateincludes;
    // first, bump based on this path
    $this->BumpDocdate($templatepath.$template);
    // check includes?
    if ($ndz_checktemplateincludes)
    {
      $filecont = file_get_contents($templatepath.$template);      
      if (preg_match_all("(\\{include +file *= *[\"\'] *([a-zA-Z0-9_\\.]+))", $filecont, $result))
      {
        foreach($result[1] as $incfile) 
        {
          $this->BumpDocdateForTemplateIncludes($templatepath, $incfile);
        }
      }
    }
  }
  
  
  // GetDirPath
  //
  // search for the specified directory
  // order is, 
  // 1. check for $ndz_scriptpath relative
  // 2. check for absolute reference
  // 3. check relative to document root
  function GetDirPath($filename)
  {
    global $ndz_docpath;
    global $ndz_scriptpath;
    
    $ret = realpath($ndz_docpath.$ndz_scriptpath."/".$filename);
    if (!file_exists($ret))
    {
      $ret = realpath($filename);
      if (!file_exists($ret))
      {
        // check for reference relative to docroot!
        $ret = realpath($ndz_docpath."/".$filename);
        if (!file_exists($ret))
        {
          // no path exists!
          $ret = "";
        }
      }
    }
    return $ret;
  }

  
  function Render()
  {
    // smarty caching default
    global $ndz_caching;
    // smarty compile check default
    global $ndz_fastcaching;
    // 
    global $ndz_file;
    //
    global $ndz_pagedoc;
    //
    global $ndz_binpath;

    // variables set in config.php.. later
    global $ndz_caching;
    global $ndz_fastcaching;
    global $ndz_checktemplateincludes;
    global $ndz_preferredimgtype;
    global $ndz_defaultdpi;
    global $ndz_templatepath;
    global $ndz_usehtmltidy;
    global $ndz_tidyencoding;
    global $ndz_tidyxhtml;
    
    
    // assign the global pagedoc reference
    $ndz_pagedoc = &$this;
    
    //  start playing with it
    $el = $this->m_dom->document_element();
//    $attr = $el->get_attribute("testattr");
    if ($el->node_name() == "pagedoc" && $el->has_child_nodes())
    {
      $child = $el->first_child();
      $first = true;
      while ($child)
      {
        if ($child->node_name() == "docroot")
        {
          $path = $this->GetDirPath($child->get_content());
          $this->m_docrootpath = $path;
          $first = false;
          // load any configuration file that might exist
          $configpath = $this->m_docrootpath."/config.php";
          $this->BumpDocdate($configpath);
          include($configpath);
        }
        else if (!$first)
        {
          $childname = $child->node_name();
          if ($childname == "template")
          {
            $this->m_templatepath = $child->get_content();
            // template docdate bumper
            $this->BumpDocdateForTemplateIncludes($this->m_docrootpath."/smarty_templates/",$this->m_templatepath);
          }
          else
          {
            $path = $this->GetFilePath($child->get_content());
            // tweak our docdate
            $this->BumpDocdate($path);
            switch ($child->node_name())
            {
              case "template" :
                
                break;
              case "script" :
                $this->m_scriptpath = $path;
                break;
              case "sitecontent" :
                $this->m_sitecontentpath = $path;
                break;
              case "content" :
                $this->m_contentpath = $path;
                break;
            }
          }
        }
        $child = $child->next_sibling();
      }
    }

    // purge outdated images
    $this->PurgeProcessedImages();
    
    // act upon the files available
    if ($this->m_templatepath != "")
    {
      require('Smarty.class.php');
      $this->m_smarty = new Smarty;
      $this->m_smarty->template_dir = $this->m_docrootpath."/smarty_templates/";
      $this->m_smarty->compile_dir = $this->m_docrootpath."/smarty_templates_c/";
      $this->m_smarty->config_dir = $this->m_docrootpath."/smarty_configs/";
      $this->m_smarty->cache_dir = $this->m_docrootpath."/smarty_cache/";
      if (!file_exists($this->m_smarty->compile_dir)) 
      {
        mkdir($this->m_smarty->compile_dir,0700);
      }
      if (!file_exists($this->m_smarty->config_dir)) 
      {
        mkdir($this->m_smarty->config_dir,0700);
      }
      if (!file_exists($this->m_smarty->cache_dir)) 
      {
        mkdir($this->m_smarty->cache_dir,0700);
      }
      // smarty caching
      $this->m_smarty->caching = false;
      // smarty compile check
      $this->m_smarty->compile_check = true;
    }
    
    // set our cache directory
    $this->m_cachedir = $this->GetNDZCacheDir();
    if (!file_exists($this->m_cachedir)) 
    {
      exec("mkdir -m 0700 -p $this->m_cachedir");
    }
    // final full path to cached html
    $cachefilepath = $this->m_cachedir.$ndz_file.".htm";
    if (file_exists($cachefilepath) &&
          (filemtime($cachefilepath) >= $this->m_docdate) &&
          $ndz_caching)
    {
      // cache exists, is newer and caching enabled, set content from cache
      $htmlcontent = file_get_contents($cachefilepath);
    }
    
    $contentincluded = false;
    // if there is a site content document, do it
    if (($this->m_sitecontentpath != "") && (!isset($htmlcontent) || !$ndz_fastcaching))
    {
      require "content.ndz.php";
      $contentincluded = true;
      $this->m_sitecontent = new cContent($this->m_sitecontentpath);
      $this->m_sitecontent->Parse();
    }
    

    // if there is a content document, do it
    if (($this->m_contentpath != "") && (!isset($htmlcontent) || !$ndz_fastcaching))
    {
      if (!$contentincluded)
      {
        require "content.ndz.php";
      }
      $this->m_content = new cContent($this->m_contentpath);
      $this->m_content->Parse();
    }

    // if a script is specified, include and execute it
    if ($this->m_scriptpath != "")
    {
      // process..  note, this could set the $htmlcontent
      // and if it does so, the template, if any, will not
      // be evaluated!
      // in order to influence the template generation using
      // the script, add properties to the pagedoc object 
      // dynamically using the AddProperty member function
      // properties added go into an array, with the member name
      // prepended with script_
      //
      include $this->m_scriptpath;
    }
    
    
    // if there was a template, 
    // and we don't have cached content, or a script was defined,
    // evaluate the template!
    if (isset($this->m_smarty) && 
        (!isset($htmlcontent) || ($this->m_scriptpath != "")))
    {
      // give smarty a reference to this pagedoc object
      $this->m_smarty->assign("pagedoc",$this);
      // give smarty a reference to the embedded content object
      $this->m_smarty->assign("content",$this->m_content);
      // give smarty a reference to the embedded site content object
      $this->m_smarty->assign("sitecontent",$this->m_sitecontent);
      // display the template
      $htmlcontent = $this->m_smarty->fetch($this->m_templatepath);
       // trim whitespace on html document 
      $htmlcontent = trim($htmlcontent);
      // html tidy, if configured
      if ($ndz_usehtmltidy)
      {
        if ($ndz_tidyxhtml)
        {
          $tidyxhtml = " -asxml";
        }
        if (isset($ndz_tidyencoding))
        {
          $tidyencoding = " -".$ndz_tidyencoding;
        }
        // write html to a temporary file
        $tidytemp = tempnam($ndz_tmppath, "ndz");
        $tmp = fopen($tidytemp,"w");
        fwrite($tmp,$htmlcontent);
        // execute tidy
        exec($ndz_binpath."/tidy".$tidyxhtml.$tidyencoding." -wrap 78 -im $tidytemp");
        fclose($tmp);
        // restore htmlcontent
        $htmlcontent = file_get_contents($tidytemp);
        // remove the temp file
        unlink($tidytemp);
      }
      

      // update the cache
      $fcache = fopen($cachefilepath,"w");
      fwrite($fcache, $htmlcontent);
      fclose($fcache);
    }


    
    // regurgitate the content
    echo $htmlcontent;
    
  }
  
 
}

?>
