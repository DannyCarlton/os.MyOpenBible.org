<?php

	$verses='';$reference='';$search_type='';$debug="DEBUG...<br>\n";
	require_once 'includes/functions.php';
	$_mysql=connect2db();
	$_P=[];$prev='';
	
	if(isset($_GET['page'])){$searchpage=$_GET['page'];}else{$searchpage=1;}
	if(!$searchpage){$searchpage=1;}

	$uri=$_SERVER['REQUEST_URI'];
	if(strstr($uri,'?'))
		{
		list($_path,$params)=explode('?',$uri);
		$debug.="Path: $_path<br>\nPARAMETERS: $params<br>\n";
		$Params=explode('&',$params);
		foreach($Params as $param)
			{
			list($key,$value)=explode('=',$param);
			if($key=='keyword'){$_P['keyword']=$value;}
			if($key=='version' and $value){$_P['version']=$value;}
			if($key=='scope' and $value){$_P['scope']=$value;}
			if($key=='page' and $value){$_P['page']=$value;}
			}
		}
	else
		{
		header("Location: /");
		exit();
		}
	$debug.=getPrintR($_P);
	$_keyword=$_GET["keyword"];

	
	if($_keyword)
		{
		$bookKey=getBookByKeyword($_keyword);
		$debug.=getPrintR($bookKey);
		if(isset($bookKey['book']))
			{
			$refKey=getRefByKeyword($_keyword);
			$debug.=getPrintR($refKey);
			$_book=$bookKey['book'];
			$bid=$refKey['bid'];
			$_chapter=$refKey['chapter'];
			$_Verses=$refKey['verses'];
			$_book    = $bookKey['book'];
			$_chapter = $refKey['chapter'];
			$_verses  = $refKey['verses'];
			$_book_abbr=$bookKey['kjv_abbr'];			
			$_chapter=$_chapter*1;
			$shorturl=$bookKey['kjv_abbr'].'_'.$_chapter;
			if($_verses){$shorturl.="_{$_verses['ref']}";}
			}
		else
			{
			$_book=0;
			$_chapter=0;
			}
	
		if($_book and $_chapter)
			{
			$search_type='reference';
			$reference=$bookKey['book'].' '.$refKey['chapter'];
			if($refKey['verses']['ref'])
				{
				$reference.=':'.$refKey['verses']['ref'];
				}
			$_keyword=$reference;
			$Verses=getVerses($bookKey['id'], $_chapter, $refKey['verses'], 'text_kjvs');
			$debug.=getPrintR($Verses);
			$chapter=$_chapter;
			foreach($Verses['Verses'] as $r=>$Verse)
				{
				$Verse['text']=preg_replace('/\s+/',' ',$Verse['text']);
				$Verse['text']=str_replace('&nbsp;',' ',$Verse['text']);
				$Verse['text']=preg_replace('/\[(.*?)\]/', '', $Verse['text']);
				$Verse['text']=capFilter($Verse['text']);
				$verses.="<p id=\"verse_$chapter"."_1\" ><strong class=\"verse-number\" >$r</strong>{$Verse['text']}</p>\n";
				}
			}
		else
			{
			$versecount=getVerseCountByKeyword($_keyword);
			$debug.="$_keyword".getPrintR($versecount);
			$pages=ceil($versecount/20);
			if($versecount<20)
				{
				$page_end=$versecount;
				}
			else
				{
				$page_end=20;
				$url="/search.html?keyword=$_keyword";
				if($pages>6)
					{
					$end_page=6;
					$start_page=1;
					
					$next='<li><a href = "'.$url.'">&raquo;</a></li>';
					if($searchpage>4)
						{
						$start_page=$searchpage-2;
						$end_page=$searchpage+2;
						$prev='<li><a href = "'.$url.'">&laquo;</a></li>';
						}
					if($pages-$searchpage<3 and $pages>6)
						{
						$start_page=$pages-5;
						$end_page=$pages;
						$prev='<li><a href = "'.$url.'">&laquo;</a></li>';
						$next='';						
						}
					}
				else
					{
					$start_page=1;
					$end_page=$pages;
					$prev='';
					}
					
				$page_start=($searchpage*20)-19;
				$page_end=$page_start+19;
				if($page_end>$versecount){$page_end=$versecount;}
				
				$pagination='
						<ul class="pagination pagination-sm">'.$prev;
				for($i=$start_page;$i<=$end_page;$i++)
					{
					if($i==$searchpage){$active='active';}else{$active='';}
					$pagination.='
							<li class="'.$active.'"><a href="'.$url.'&page='.$i.'">'.$i.'</a></li>';
						
					}
				$pagination.="$next
						</ul>";
				}
			$Verses=getVersesByKeyword($_keyword,'',$page_start-1);
			$debug.=getPrintR($Verses);
			if($Verses)
				{
				foreach($Verses as $Verse)
					{
					$Verse['text']=preg_replace('/\[(.*?)\]/', '', $Verse['text']);
					$Verse['text']=capFilter($Verse['text']);
					$keyword=$_keyword;
					$keyword_display='';$KeywordDisplay=[];
					if(strstr($keyword,'"'))
						{
						$Keywords=explode('"',$keyword);
						foreach($Keywords as $kw)
							{
							$kw=str_replace(' ','_',trim($kw));
							$KW[]=$kw;
							}
						$keyword=trim(implode(' ',$KW));$KW=[];
						}
					if(strstr($keyword,' '))
						{
						$Keywords=explode(' ',$keyword);
						$text=$Verse['text'];
						foreach($Keywords as $kw)
							{
							$kw=str_replace('_',' ',$kw);
							$kw=str_replace('*','[a-zA-Z]*',$kw);
							$text=preg_replace('/\b('.$kw.')\b/i', '<strong>$1</strong>', $text);
							$kw=str_replace('[a-zA-Z]*','*',$kw);
							$KeywordDisplay[]='&ldquo;'.$kw.'&rdquo;';
							}
						$keyword_display=implode(' and ',$KeywordDisplay);
						}
					else
						{
						$keyword=str_replace('_',' ',$keyword);
						$keyword=str_replace('*','[a-zA-Z]*',$keyword);
						$text=preg_replace('/\b('.$keyword.')\b/i', '<strong>$1</strong>', $Verse['text']);
						$keyword=str_replace('[a-zA-Z]*','*',$keyword);
						$keyword_display='&ldquo;'.$keyword.'&rdquo;';
						}
					$book=$Verse['bookname'];
					$chapter=$Verse['chapter'];
					$v=$Verse['verse'];
					$abbr=$Verse['kjav_abr'];
					$verse="
						<result>
							<ref>
								$book $chapter:$v
								<note>
									<a href=\"search.html?keyword=$abbr+$chapter\">...in context</a>
								</note>
							</ref>
							<verse>
								$text
							</verse>
						</result>	";
						
					$verses.=$verse;
#					$keyword_display0=$keyword_display." in ".$Version_titles[$version];
#					$smarty->assign('keyword_script', $keyword_script);
					$smarty->assign('verses', $verses);
					$smarty->assign('pagination', $pagination);
					$smarty->assign('page_start', $page_start);
					$smarty->assign('page_end', $page_end);
					$smarty->assign('versecount', $versecount);
					$smarty->assign('keyword_display', $keyword_display);
					}
				}
			}
		}

	$smarty->assign('page_title', "Search :");
#	$smarty->assign('debug', $debug);
	$smarty->assign('debug', '');
	$smarty->assign('reference', $reference);
	$smarty->assign('search_type', $search_type);
	$smarty->assign('verses', $verses);


?>