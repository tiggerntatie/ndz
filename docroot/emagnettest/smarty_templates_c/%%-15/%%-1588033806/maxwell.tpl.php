<?php /* Smarty version 2.6.1, created on 2004-06-04 10:31:52
         compiled from maxwell.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "defaultpreamble.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "defaultcss.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<style type='text/css'>
<!--
  table.eqnlist {vertical-align : top; width : 100%; }
  td.eqnno {vertical-align : top; width : 15%; padding : 5px; }
  td.eqn {vertical-align : top; width : 35%; padding : 5px; }
  td.eqndesc {vertical-align : top; width : 50%; padding : 5px; }
-->
</style>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "defaulthead.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


<table class="eqnlist">

<?php if (isset($this->_sections['sect'])) unset($this->_sections['sect']);
$this->_sections['sect']['name'] = 'sect';
$this->_sections['sect']['loop'] = is_array($_loop=$this->_tpl_vars['content']->doc_section) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['sect']['show'] = true;
$this->_sections['sect']['max'] = $this->_sections['sect']['loop'];
$this->_sections['sect']['step'] = 1;
$this->_sections['sect']['start'] = $this->_sections['sect']['step'] > 0 ? 0 : $this->_sections['sect']['loop']-1;
if ($this->_sections['sect']['show']) {
    $this->_sections['sect']['total'] = $this->_sections['sect']['loop'];
    if ($this->_sections['sect']['total'] == 0)
        $this->_sections['sect']['show'] = false;
} else
    $this->_sections['sect']['total'] = 0;
if ($this->_sections['sect']['show']):

            for ($this->_sections['sect']['index'] = $this->_sections['sect']['start'], $this->_sections['sect']['iteration'] = 1;
                 $this->_sections['sect']['iteration'] <= $this->_sections['sect']['total'];
                 $this->_sections['sect']['index'] += $this->_sections['sect']['step'], $this->_sections['sect']['iteration']++):
$this->_sections['sect']['rownum'] = $this->_sections['sect']['iteration'];
$this->_sections['sect']['index_prev'] = $this->_sections['sect']['index'] - $this->_sections['sect']['step'];
$this->_sections['sect']['index_next'] = $this->_sections['sect']['index'] + $this->_sections['sect']['step'];
$this->_sections['sect']['first']      = ($this->_sections['sect']['iteration'] == 1);
$this->_sections['sect']['last']       = ($this->_sections['sect']['iteration'] == $this->_sections['sect']['total']);
?>


  <?php if ($this->_tpl_vars['content']->doc_section[$this->_sections['sect']['index']]->type == 'eqnno'): ?>
    <tr>
    <td class="eqnno">
    <h3><?php echo $this->_tpl_vars['content']->doc_section[$this->_sections['sect']['index']]->content; ?>
</h3>
    </td>


  <?php elseif ($this->_tpl_vars['content']->doc_section[$this->_sections['sect']['index']]->type == 'eqn'): ?>
    <td class="eqn">
    <?php echo $this->_tpl_vars['content']->doc_section[$this->_sections['sect']['index']]->content; ?>

    </td>


  <?php elseif ($this->_tpl_vars['content']->doc_section[$this->_sections['sect']['index']]->type == 'eqndesc'): ?>
    <td class="eqndesc">
    <?php echo $this->_tpl_vars['content']->doc_section[$this->_sections['sect']['index']]->content; ?>

    </td>
    </tr>
    <?php if ($this->_tpl_vars['content']->doc_section[$this->_sections['sect']['index']]->name == 'lastdesc'): ?>
      </table>
    <?php endif; ?>

  <?php else: ?>  
    <h3>
    <?php echo $this->_tpl_vars['content']->doc_section[$this->_sections['sect']['index']]->title; ?>

    </h3>
    <?php echo $this->_tpl_vars['content']->doc_section[$this->_sections['sect']['index']]->content; ?>

  <?php endif;  endfor; endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "defaultfoot.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>