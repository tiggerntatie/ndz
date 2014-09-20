<?php
// image.ndz.php

class cImage
{
  var $m_type;
  var $m_node;
  var $m_name;
  var $m_file;
  var $m_alt;
  var $m_url;
  var $m_x;
  var $m_y;

  function cImage($node, $type="default")
  {
    $this->m_node = $node;
    $this->m_type = $type;
    if ($this->m_type == "")
    {
      $this->m_type = "default";
    }
  }

  function getattributes()
  {
    global $ndz_defaultdpi;
    
    $this->m_file = $this->m_node->get_attribute("file"); // source file
    $this->m_url = $this->m_node->get_attribute("url"); // external url
    $this->m_alt = htmlspecialchars($this->m_node->get_attribute("alt"),ENT_QUOTES);
    $this->m_x = $this->m_node->get_attribute("x");
    $this->m_y = $this->m_node->get_attribute("y");
    $this->m_name = $this->m_node->get_attribute("name");
    $this->m_dpi = $this->m_node->get_attribute("dpi");
    if ($this->m_dpi == "")
    {
      $this->m_dpi = $ndz_defaultdpi;
    }
    
  }

  function imagetype_to_extension($imgtype)
  {
    switch ($imgtype)
    {
      case IMAGETYPE_PNG :
        $ret = "png";
        break;
      case IMAGETYPE_JPG :
        $ret = "jpg";
        break;
      case IMAGETYPE_GIF :
        $ret = "gif";
        break;
    }
    return $ret;
  }
  

  function adjustxy($srcx,$srcy, &$dstx, &$dsty)
  {
    if ($dstx!="" && $dsty=="")
    {
      $dsty = round($dstx*$srcy/$srcx);
    }
    else if ($dstx=="" && $dsty!="")
    {
      $dstx = round($dsty*$srcx/$srcy);
    }
    else if ($dstx=="" && $dsty=="")
    {
      $dstx = $srcx;
      $dsty = $srcy;
    }
    // else leave em alone
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
    global $ndz_docpath;
    global $ndz_scriptpath;
    global $ndz_pagedoc;
    
    $ret = realpath($filename);
    
    // check existance of absolute reference
    if (!is_file($ret))
    {
      // check for existance off current doc directory
      $ret = realpath($ndz_docpath.$ndz_scriptpath."/".$filename);
      if (!is_file($ret))
      {
        // check for reference relative to docroot!
        $ret = realpath($ndz_pagedoc->m_docrootpath."/".$filename);
        if (!is_file($ret))
        {
          // no path exists!
          $ret = "";
        }
      }
    }
    return $ret;
  }
  
  function ExecSvgTransform($imgext, $srcimgdest, $tmpname)
  {
    global $ndz_preferredimgtype;
    global $ndz_binpath;
    // Note: this commented code *could* be used to losslessly
    // scale the svg image before rendering, but this is apparently
    // not how ImageMagick operates!  
    /*
    // check for need to scale at conversion time
    // doing so at conversion time guarantees preservation
    // of the accuracy of the svg data
    if ($this->m_x != "" || $this->m_y != "")
    {
      // first, retrieve the source size
      exec("identify $tmpname", $output);
      foreach($output as $outline)
      {
        // look for size reported as 150x160
        preg_match_all("(([0-9]+)x([0-9]+))", $outline, $result);
        if ($result[0][0] != "") 
        {
          $destx = $this->m_x;
          $desty = $this->m_y;
          $this->adjustxy($result[1][0],$result[2][0], &$dstx, &$dsty);
          $resize = " -resize ".$dstx."x".$dsty." ";
        }
      }
    }
    */
    exec("convert -depth 8 $tmpname $srcimgdest");
    // check to see if we should reprocess a png file
    if ("png" == $this->imagetype_to_extension($ndz_preferredimgtype))
    {
      $tempfile = $srcimgdst.".temp";
      rename($srcimgdest, $tempfile);
      exec("$ndz_binpath/pngcrush  $tempfile $srcimgdest");
      unlink($tempfile);
    }
  }
  
  
  function ExecTexTransform($imgext, $srcimgdest, $tmpname)
  {
    global $ndz_binpath;
    // dpi requested?
    $dpiexpr = "";
    if ($this->m_dpi != "")
    {
      $dpiexpr = " -r ".$this->m_dpi."x".$this->m_dpi;
    }
    exec($ndz_binpath."/tex2im$dpiexpr -f $imgext -o $srcimgdest $tmpname", $output, $retval);
  }


  function ExecEpsTransform($imgext, $srcimgdest, $tmpname)
  {
    global $ndz_binpath;
    exec($ndz_binpath."/eps2png -$imgext -output $srcimgdest $tmpname");
  }


  function render()
  {
    global $ndz_pagedoc;
    global $ndz_preferredimgtype;
    global $ndz_binpath;
    global $ndz_tmppath;
    global $ndz_caching;

    $imgext = $this->imagetype_to_extension($ndz_preferredimgtype);
    
    $this->getattributes();
    if ($this->m_url != "")
    {
      $imgurl = $this->m_url;
      $srclink = "src='$this->m_url' ";
    }
    else
    {
      // if name provided, and no source file exists construct the dest. name
      if (($this->m_name != "") && ($this->m_file == ""));
      {
        // determine the "virtual" image name
        $imgname = $this->m_name.".".$imgext;
        $dest = $ndz_pagedoc->GetProcessedImagePath($imgname);
        // if svg type, also create a destination svg name
        if ($this->m_type != "default")
        {
          $srcname = $this->m_name.".".$this->m_type;
          // srcdest is the path to the server doc tree copy of 
          $dest = $ndz_pagedoc->GetProcessedImagePath($srcname);
        }
      }
      // if a source file provided, figure out where it's transformed to
      if ($this->m_file != "")
      {
        $this->m_file = $this->GetFilePath($this->m_file);
        // if the source file really exists..
        if ($this->m_file != "")
        {
          // figure out what the destination image name is
          $dest = $ndz_pagedoc->GetProcessedImagePath($this->m_file); 
        }
      }
      if ($this->m_type != "default")
      {
        // the image-ified version of the source filename
        $srcimgdest = substr($dest, 0, strrpos($dest,".")).".".$imgext;
      }
      else
      {
        // ordinary image, start with its src filename
        $xformsrc = $this->m_file;
        $srcimgdest = $dest;
      }
      
      
      // check to see if processing is required for this image
      $srcimgdeststamp = filemtime($srcimgdest);
      if (!file_exists($srcimgdest) || 
          (($this->m_file != "") && ($srcimgdeststamp < filemtime($this->m_file))) ||
          ($srcimgdeststamp < $ndz_pagedoc->m_docdate))
      {
        // if this is not a jpg, gif or png source image..
        if ($this->m_type != "default")
        {
          if ($this->m_file != "")
          {
            // image source code file
            $tmpname = $this->m_file;
          }
          else
          {
            // embedded in this document, create a temp file
            // retrieve the source
            $src = trim($this->m_node->get_content());
            // write it to a temporary file
            $tmpname = tempnam($ndz_tmppath, "ndz");
            $tmp = fopen($tmpname,"w");
            fwrite($tmp,$src);
          }
          // call the special handler
          switch ($this->m_type)
          {
            case "tex" :
              $this->ExecTexTransform($imgext, $srcimgdest, $tmpname);
              break;
            case "eps" :
              $this->ExecEpsTransform($imgext, $srcimgdest, $tmpname);
              break;
            case "svg" :
              $this->ExecSvgTransform($imgext, $srcimgdest, $tmpname);
              break;
          }
          
          // throw a copy of the source file up there too, for date checking
          if ($this->m_file != "")
          {
            copy($tmpname, $dest);
          }
          
          if ($tmp)
          {
            fclose($tmp);
            unlink($tmpname);
          }
          // we may need to further transform the image
          $xformsrc = $srcimgdest;
        }
  
        // gather info about the source image
        $size = getimagesize($xformsrc);
        // figure out new dimensions for the processed image
        $destx = $this->m_x;
        $desty = $this->m_y;
        $this->adjustxy($size[0], $size[1], $destx, $desty);
        if (($destx == $size[0]) && ($desty == $size[1]))
        {
          // image is perfect size already, or no adjustment is called for.
          // do we even need to copy it anywhere!
          if ($xformsrc != $srcimgdest)
          {
            copy($xformsrc, $srcimgdest);
          }
        }
        else
        {
          // image needs to be resized
          // what have we got
          switch($size[2])
          {
            case IMAGETYPE_PNG :
              $img = imagecreatefrompng($xformsrc);
              break;
            case IMAGETYPE_JPG :
              $img = imagecreatefromjpeg($xformsrc);
              break;
            case IMAGETYPE_GIF :
              $img = imagecreatefromgif($xformsrc);
              break;
            default :
              $img = imagecreate(0,0);
              break;
          }
          // if src and dest are the same, make way for the new version
          if ($img && ($xformsrc == $srcimgdest))
          {
            unlink($srcimgdest);
          }
          
          // create a blank target image
          $destimg = imagecreatetruecolor($destx, $desty);
          // copy it
          imagecopyresampled($destimg, $img, 0, 0, 0, 0, $destx, $desty, $size[0], $size[1]);
          // now put it where it goes
          switch($size[2])
          {
            case IMAGETYPE_PNG :
              imagepng($destimg, $srcimgdest);
              break;
            case IMAGETYPE_JPG :
              imagejpeg($destimg, $srcimgdest);
              break;
            case IMAGETYPE_GIF :
              imagegif($destimg, $srcimgdest);
              break;
            default :
              break;
          }
        }
      } // if processing is required
      // no processing required, but if we're not using cached html, we 
      // need to compute the image dimensions!
      else if (!$ndz_caching)
      {
        // gather info about the destination image
        $size = getimagesize($srcimgdest);
        // figure out new dimensions for the processed image
        $destx = $size[0];
        $desty = $size[1];
      }
      
      
      $srclink = "src='".substr($srcimgdest,strrpos($srcimgdest,"/")+1)."' ";
    } // SRC not provided in script

    // if we managed to generate a src tag!
    if ($srclink != "")
    {    
      $ret = "<img ".$srclink;
      if ($this->m_alt != "")
      {
        $ret = $ret."alt='$this->m_alt' ";
      }
      $ret = $ret."height='$desty' width='$destx' />";
    }
    return $ret;
  } // render
}




?>
