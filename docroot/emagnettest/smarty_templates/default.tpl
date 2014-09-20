{* smarty file : maxwell.tpl *}
{include file="defaultpreamble.tpl"}
{include file="defaultcss.tpl"}


{include file="defaulthead.tpl"}

<table class="sect">

{section name=sect loop=$content->doc_section}
  <tr>
    <td colspan="2">
    <hr/>
    </td>
  </tr>
  <tr>
    <td class="secttitle">
      <h3>{$content->doc_section[sect]->title}</h3>
    </td>
    <td class="sectcont">
      {$content->doc_section[sect]->content}
    </td>
  </tr>
{/section}

</table>

{include file="defaultfoot.tpl"}
