{* smarty file : maxwell.tpl *}
{include file="defaultpreamble.tpl"}
{include file="defaultcss.tpl"}

<style type='text/css'>
<!--
  table.eqnlist {ldelim}vertical-align : top; width : 100%; {rdelim}
  td.eqnno {ldelim}vertical-align : top; width : 15%; padding : 5px; {rdelim}
  td.eqn {ldelim}vertical-align : top; width : 35%; padding : 5px; {rdelim}
  td.eqndesc {ldelim}vertical-align : top; width : 50%; padding : 5px; {rdelim}
-->
</style>

{include file="defaulthead.tpl"}


<table class="eqnlist">

{section name=sect loop=$content->doc_section}

{* the roman numeral: equation number *}

  {if $content->doc_section[sect]->type == "eqnno"}
    <tr>
    <td class="eqnno">
    <h3>{$content->doc_section[sect]->content}</h3>
    </td>

{* the equation graphic *}

  {elseif $content->doc_section[sect]->type == "eqn"}
    <td class="eqn">
    {$content->doc_section[sect]->content}
    </td>

{* equation description *}

  {elseif $content->doc_section[sect]->type == "eqndesc"}
    <td class="eqndesc">
    {$content->doc_section[sect]->content}
    </td>
    </tr>
    {if $content->doc_section[sect]->name == "lastdesc"}
      </table>
    {/if}
{* regular body content *}

  {else}  
    <h3>
    {$content->doc_section[sect]->title}
    </h3>
    {$content->doc_section[sect]->content}
  {/if}
{/section}

{include file="defaultfoot.tpl"}
