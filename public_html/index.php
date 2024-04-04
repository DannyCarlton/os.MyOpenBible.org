<?php

#error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL);
$variable='sample variable';
$_debug='';

$_path=str_replace('/public_html','',$_SERVER['DOCUMENT_ROOT']);
require_once $_path.'/smarty/libs/Smarty.class.php';
$smarty = new Smarty;
$smarty->setTemplateDir($_path.'/smarty/templates/');
$smarty->compile_dir = $_path.'/smarty/templates_c/';
$smarty->cache_dir = $_path.'/smarty/cache/';
$smarty->force_compile = true;  //turn of for live site
#$smarty->debugging = true;
$smarty->caching = true;
$smarty->cache_lifetime = 120;


require_once 'includes/local.php';
error_reporting(E_ALL);

$uri=$_SERVER['REQUEST_URI'];
if(strstr($uri,'?'))
	{
	list($uri,$par)=explode('?',$uri);
	}

$uri=str_replace('.php','',$uri);
$uri=str_replace('.html','',$uri);
$uri=str_replace('.htm','',$uri);
$Pages=explode('/',$uri);
$page=$Pages[1];
if(!$page){$page='index';}

if(file_exists("includes/$page.php"))
	{
	require_once("includes/$page.php");
	}

$template=$page.'.tpl';

#$_debug=getPrintR($_SERVER);

$smarty->assign("variable_name", $variable);
#$smarty->assign("debug", getPrintR($_debug));
$smarty->display($template);


?>

