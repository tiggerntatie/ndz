<?php

// eps.image.ndz.php
// encapsulated postscript processor

class cEps extends cImage
{
  
  
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
        exec($ndz_binpath."/eps2png -$imgext -output $dest $tmpname");
        fclose($tmp);
        unlink($tmpname);
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
 
  
}

?>