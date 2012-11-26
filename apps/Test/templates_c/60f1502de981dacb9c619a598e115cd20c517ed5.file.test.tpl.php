<?php /* Smarty version Smarty-3.1.8, created on 2012-09-05 23:23:04
         compiled from "/home/shawn/SiB/apps/Test/templates/test.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7401971875046f81ccc55f7-70450071%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '60f1502de981dacb9c619a598e115cd20c517ed5' => 
    array (
      0 => '/home/shawn/SiB/apps/Test/templates/test.tpl',
      1 => 1346912577,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7401971875046f81ccc55f7-70450071',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5046f81cd92140_84525595',
  'variables' => 
  array (
    'data' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5046f81cd92140_84525595')) {function content_5046f81cd92140_84525595($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Test Everything</title>
<script language="javascript" type="text/javascript" src="js/jquery1.6.js"></script>
<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
<h1> Hello,world! </h1>
<P>这是真的吗？</p>
<p><?php echo $_smarty_tpl->tpl_vars['data']->value;?>
</p>
<form method ="post">
<button type="submit" name = "submit"  value ="value">test</button>
</form>
</body>
</html>
<?php }} ?>