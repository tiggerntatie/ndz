{* smarty file : maxwell.tpl *}
{include file="defaultpreamble.tpl"}
{include file="defaultcss.tpl"}

<style type='text/css'>
<!--
  table.sect {ldelim}vertical-align : top; width : 100%; {rdelim}
  td.secttitle {ldelim}vertical-align : top; width : 30%; {rdelim}
  rd.sectcont {ldelim}vertical-align : top; width : 70%; {rdelim}
-->
</style>

{include file="defaulthead.tpl"}

<table class="sect">

{section name=sect loop=$content->doc_section}
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
