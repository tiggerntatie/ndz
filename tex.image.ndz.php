<?php

// tex.image.ndz.php
// tex processor

class cTex extends cExtendedImage
{
  function ExecTransform($imgext, $dest, $tmpname)
  {
    global $ndz_binpath;
    // dpi requested?
    $dpiexpr = "";
    if ($this->m_dpi != "")
    {
      $dpiexpr = " -r ".$this->m_dpi."x".$this->m_dpi;
    }
    exec($ndz_binpath."/tex2im$dpiexpr -f $imgext -o $dest $tmpname", $output, $retval);
  }

  function render()
  {
    $this->rendercustom();
  }
  

  /*
  
  function render()
  {
    global $ndz_pagedoc;
    global $ndz_preferredimgtype;
    global $ndz_binpath;
    global $ndz_tmppath;
    
    $imgext = $this->imagetype_to_extension($ndz_preferredimgtype);
    
    $this->getattributes();
    if ($this->m_name != "");
    {
      // determine the "virtual" image name
      $imgname = $this->m_name.".".$imgext;
      $dest = $ndz_pagedoc->GetProcessedImagePath($imgname);
      if (!file_exists($dest))
      {
        // must regenerate the image
        // retrieve the eps source
        $epssrc = trim($this->m_node->get_content());
        // write it to a temporary file
        $tmpname = tempnam($ndz_tmppath, "ndz");
        $tmp = fopen($tmpname,"w");
        fwrite($tmp,$epssrc);
        // dpi requested?
        $dpiexpr = "";
        if ($this->m_dpi != "")
        {
          $dpiexpr = " -r ".$this->m_dpi."x".$this->m_dpi;
        }
        exec($ndz_binpath."/tex2im$dpiexpr -f $imgext -o $dest $tmpname", $output, $retval);
        fclose($tmp);
        unlink($tmpname);
        if ($retval != 0)
        {
          foreach($output as $line) echo $line."<br>";
          exit;
        }
        
      }
      // retrieve the image size
      $size = getimagesize($dest);
      // start constructing the tag      
      $ret = "<img src='".$ndz_pagedoc->GetProcessedImageURL($imgname)."' ";
      $ret = $ret."width='$size[0]' height='$size[1]' ";
      if ($this->m_alt != "")
      {
        $ret = $ret."alt='$this->m_alt' ";
      }
      $ret = $ret.">";
    }
    return $ret;
  }
 */
  
}

?>