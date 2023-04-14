<?php


#error_reporting(E_PARSE | E_ERROR);
error_reporting(E_ALL);

/* This clears all cookies
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}
*/



require_once 'includes/local.php';

if(isset($_GET['fbclid']))
	{
	$PAR=[];
	list($url,$toss)=explode('?',$url=$_SERVER['REQUEST_URI']);
	unset($_GET['fbclid']);
	foreach($_GET as $key=>$value)
		{
		$PAR[]="$key=$value";
		}
	if(count($PAR))
		{
		$parameters=implode('&',$PAR);
		header('Location: '.$url.'?'.$parameters);
		}
	else
		{
		header('Location: '.$url);
		}
	exit();
	}

$phpver=phpversion();
$PV=explode('.',$phpver);
$phpver=$PV[0].'.'.$PV[1].$PV[2];
$phpver=$phpver*1;
$header_values='';

if($phpver<7)
	{
	error_reporting(E_ERROR | E_PARSE | E_WARNING);
	}
else
	{
	#error_reporting(E_PARSE | E_ERROR);
	error_reporting(E_ALL);
	}

$UserData=[];$userid=0;$version=0;$data='';$verses='';$style='';$lang='';$trans='';$chapter_list='';$scope_change='';
$onload_js='';$user_functions='';$revolution='';$server='';$translation='';$includes='';$debug='';$url_trace='';

#$_path = '/home/jsonbibl';
#$_smarty='/myopenbible_smarty';
$_debug=[];

$_debug['php version'.'<div style="float:right">'.__FILE__.' ['.__LINE__.']</div>']=$phpver;

$_host=$_SERVER['HTTP_HOST'];

#echo "$_path -- $_smarty";
#exit();


require_once $_path.$_smarty.'/libs/Smarty.class.php';
$smarty = new Smarty;
$smarty->setTemplateDir($_path.$_smarty.'/templates/');
$smarty->compile_dir = $_path.$_smarty.'/templates_c/';
$smarty->cache_dir = $_path.$_smarty.'/cache/';
$smarty->force_compile = true;  //turn off for live site
#	$smarty->debugging = true;
$smarty->caching = true;
$smarty->cache_lifetime = 120;

$smarty->assign('keyword', '');
$smarty->assign('onload_js', '');
$smarty->assign('debugx', '');
$smarty->assign('includesx', '');
$smarty->assign('outline_text', '');



require_once 'includes/_misc_functions.php';
require_once 'includes/_db_functions.php';
$user_ip=getUserIpAddr();
if($user_ip=='98.178.164.31' or $user_ip=='127.0.0.1')
	{
	$admin_cog='<div style="float:left;margin-left:7px;margin-top:7px;font-size:21px;cursor:pointer">
					<i class="fa fa-cogs" style="text-shadow: 2px 2px 2px #000" data-toggle="modal" data-target="#myModal"></i>
				</div>';
	}
else
	{
	$admin_cog='';
	}







$smarty->assign('admin_cog', $admin_cog);
$_mysql=connect2db();
$_debug['mysql ['.__FILE__.' : '.__LINE__.']']=$_mysql;
require_once 'includes/_user_functions.php';

$userLogCount=getUserLog($user_ip);
if($userLogCount>5){sleep(10);}

$_debug['userLog']=$userLogCount;

if(isset($UserData['id']))
	{
	$header_values="var _user={$UserData['id']};";
	$userid=$UserData['id'];
	}
else
	{
	$header_values="var _user=0;";
	}
$smarty->assign('header_values',$header_values);



$uri=str_replace('.php','',$_SERVER['REQUEST_URI']);
$Pages=explode('/',$uri);
$page=$Pages[1];
if(strstr($page,'.'))
	{
	list($p,$t)=explode('.',$page);
	$page=$p;
	}

if(!$page){$page='index';}
if($page=='splash'){$page='index';}
$includes='index.php';
$smarty->assign('function', 'bible'); //move to includes/bible-study
$smarty->assign('show_layout', TRUE);
$smarty->assign('show_outline_option', TRUE);
$footer=implode(file("https://cdn.phpbible.org/links/ministry_footer.html"));

#$_debug['footer']=$footer;
$smarty->assign("footer", $footer);
$smarty->assign('scope_change', $scope_change);
$smarty->assign('revolution', $revolution);
$smarty->assign('server', $server);
$smarty->assign('subdomain', $_subdomain);
$smarty->assign('host', $_subdomain);
$smarty->assign('translation', $translation);
$smarty->assign('includes', $includes);
$smarty->assign('debug', $debug);
$smarty->assign('debugx', '');
$smarty->assign('style_url', '');
$smarty->assign('study_active', '');
$smarty->assign('read_active', '');
$smarty->assign('reading_active', '');
$smarty->assign('outline_style', '');
$smarty->assign('onload_js', '');
$smarty->assign('style', 'default');
$smarty->assign('user_functions', $user_functions);
$url_trace='';
if(isset($_COOKIE['url_trace'])){$url_trace=$_COOKIE['url_trace'];}
$url_trace=$url_trace."\n".$_SERVER['REQUEST_URI'];
setcookie('url_trace',$url_trace,0,'/');
$_debug['_COOKIE']=$_COOKIE;

if(isset($userid)){$uid=$userid;}else{$uid=0;}
$now=date("Y-m-d h:i:s");
if(isset($_SERVER['HTTP_REFERER']))
	{
	$_referer=$_SERVER['HTTP_REFERER'];
	}
else
	{
	$_referer='none';
	}
$_debug['user_log']=saveUserLog($uid,$user_ip,$_SERVER['REQUEST_URI'],$_referer,$now);

if(file_exists("includes/$page.php"))
	{
	require_once("includes/$page.php");
	}
if(isset($Styles)){$_debug['Styles']=$Styles;}
if(isset($userid) and $userid>0)
	{
	if(isset($UserData['layout'])){$layout=$UserData['layout'];}
	else{$layout='Traditional';}
	$style=$Styles[$layout];
	$book_list=str_replace('/study/',"/$style/",$book_list);
	if(isset($UserData['version'])){$version_title=$UserData['version'];}
	else{$version_title='King James Version';}
	$version=array_search($version_title,$Version_titles);
	$book_list=str_replace('/kjv/',"/$version/",$book_list);
	}

$page=$page.'.tpl';

$_server=$_SERVER['HTTP_HOST'];
$S=explode('.',$_server);
if($S[0]=='myopenbible'){array_unshift($S, 'www');}
$_subdomain=$S[0];
$_debug['subdomain']=$_subdomain;
$_debug['page']=$page;

if($user_ip=='98.178.164.31' or $user_ip=='127.0.0.1')
	{
	$debug=generateAccordion($_debug);
	}
else
	{
	$debug='';
	}
if($_subdomain=='www')
	{
	$smarty->assign('debug', '');
	}
else
	{
	$smarty->assign('debug', $debug);
	}
$smarty->assign('host', $_subdomain);

$smarty->assign('_mark', 'Mark');
$smarty->assign('book_list', $book_list);
$smarty->assign('columns', 3);
$smarty->assign('server', $_server);
$smarty->assign('Includesx', '');

if(file_exists("../$_smarty/templates/$page"))
	{
	$smarty->assign('page', $uri);
	$smarty->assign('includes', $includes);
	$smarty->display($page);
	}
else
	{
	$uri=$_SERVER['REQUEST_URI'];
	$smarty->assign('page', $uri);
	$smarty->display("404.tpl");
	}

?>