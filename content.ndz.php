<?php
// content.ndz.php
// ndz content definition file parsing class

class cSection extends cContent
{
  var $title;
  var $content;
  var $type;
  var $name;
  
  // instantiated with the section node
  function cSection($node)
  {
    global $imageincluded;
    global $otherimgincluded;
    // retrieve a title attribute, if any
    $this->title = $this->UnescapeEntities($node->get_attribute("title"));
    // retrieve a type attribute, if any
    $this->type = $node->get_attribute("type");
    // retrieve a name attribute, if any
    $this->name = $node->get_attribute("name");
    // temp
    $this->content = "";
    $child = $node->first_child();
    while ($child)
    {
      switch ($child->node_name())
      {
        case "#text" :
          $this->content .= $this->UnescapeEntities($child->get_content());
          break;
          
        case "ndz_image" :
            $imagetype = $child->get_attribute("type");
            // default image file
            if (!$imageincluded)
            {
              require "image.ndz.php";
              $imageincluded = true;
            }
            $image = new cImage($child, $imagetype);
            $this->content .= $image->render();
            break;
      }
      
      $child = $child->next_sibling();
    }
  }
}

class cContent extends cPagedoc
{

  var $m_docpath;
  var $m_dom;
  var $doc_section = array();
  var $doc_namedsection = array();
  
  function cContent($docpath)
  {
    global $ndz_pagedoc;
    global $ndz_docpath;  // path to the systemwide doc root
    global $ndz_scriptpath; // docpath relative path to site doc root
    
    
    $ndz_siterootpath = $ndz_pagedoc->m_docrootpath;  // full path to site root
    $ndz_rootpath = $ndz_docpath;
    
    $this->m_docpath = $docpath;
    $xmlcontents = file_get_contents($docpath);
    // search for and substitute ndz "environment" variables
    // $ndz_rootpath : NDZ system wide doc root
    // $ndz_scriptpath : $ndz_rootpath relative path to the current script
    // $ndz_siterootpath : ful path to site wide doc root
    $xmlcontents = str_replace(
                    array('$ndz_rootpath','$ndz_scriptpath','$ndz_siterootpath'),
                    array($ndz_rootpath,$ndz_scriptpath,$ndz_siterootpath),
                    $xmlcontents);
                    
    $xmlcontents = $this->EscapeEntities($xmlcontents);
    $xmlcontents = "<?xml version='1.0'?>".$xmlcontents;
    if (!$this->m_dom = domxml_open_mem($xmlcontents))
    {
      echo("error opening xml content definition: ".$docpath);
      exit;
    }
  }
  
  // replace & chars with something xml friendlier 
  function EscapeEntities($text)
  {
    $tmp = str_replace(array("&","<ndz","</ndz","<"), array("@~@","@@ndz","@@/ndz","[@["), $text);
    return str_replace(array("@@ndz","@@/ndz"),array("<ndz","</ndz"),$tmp);
  }
  
  // restore & chars and fixup the html tags
  function UnescapeEntities($text)
  {
    // replace phony html tags from original source
    return str_replace(array("@~@","[@["), array("&","<"),$text);
  }
  
  
  function Parse()
  {
    //  start playing with it
    $el = $this->m_dom->document_element();
    if ($el->node_name() == "ndz_document" && $el->has_child_nodes())
    {
      $child = $el->first_child();
      while ($child)
      {
        if ($child->node_name() != "#text")
        {
          // add a member variable for each node
          if ($child->node_name() == "ndz_section")
          {
            // put it and a reference to it in the doc_section member
            $tempobj = new cSection($child);
            array_push($this->doc_section, $tempobj);
            if ($tempobj->name != "")
            {
              $this->doc_section[$tempobj->name] = $tempobj;
            }
          }
          else
          {
            $tmpmember = '$this->doc_'.$this->RemoveNDZ($child->node_name());
            $tmpcontent = str_replace("'","&rsquo;",$this->UnescapeEntities($child->get_content()));
            $evalstr = 
            "if (is_array($tmpmember))
             {
               array_push($tmpmember, '$tmpcontent');
             } else if (!isset($tmpmember))
             {
               $tmpmember = '$tmpcontent';
             } else
             {
               $tmpmember = array($tmpmember, '$tmpcontent');
             };";
            eval($evalstr);
          }
        }
        $child = $child->next_sibling();
      }
    }
  }  



 
}

?>