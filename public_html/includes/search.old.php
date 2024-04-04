<?php

	$verses='';
	require_once 'includes/_misc_functions.php';
	require_once 'includes/_db_functions.php';
	$_mysql=connect2db();
	require_once 'includes/_bible_functions.php';
	

	$uri=$_SERVER['REQUEST_URI'];
	list($_path,$params)=explode('?',$uri);
	$Params=explode('&',$params);
	foreach($Params as $param)
		{
		list($key,$value)=explode('=',$param);
		if($key=='keyword'){$_P['keyword']=$value;}
		if($key=='version' and $value){$_P['version']=$value;}
		if($key=='scope' and $value){$_P['scope']=$value;}
		if($key=='page' and $value){$_P['page']=$value;}
		}
	$Path=explode('/',$_path);
	foreach($Path as $key=>$value)
		{
		if($key==1){$_P['function']=$value;}
		if($key==2 and $_P['version']==''){$_P['version']=$value;}
		}
	if(!isset($_P['scope'])){$_P['scope']='The-Whole-Bible';}
	foreach($_P as $key=>$value)
		{
		if($key=='keyword'){$urlRef[]="keyword=$value";$kw_display=$value;}
		if($key=='function'){$_Path[]=$value;}
		if($key=='scope'){$urlRef[]="scope=$value";}
		}
	$url_ref='/?'.implode('&',$urlRef);
	$smarty->assign('url_ref', $url_ref);
	$Levels=explode('/',$uri);
  
	if(isset($_GET['page'])){$searchpage=$_GET['page'];}else{$searchpage=1;}
	if(!$searchpage){$searchpage=1;}
  
  
	$_keyword=$_GET["keyword"];
	$_keyword=str_ireplace('Song of Solomon', 'Solomon', $_keyword);
  
  
	if(isset($_GET['scope']) and $_GET['scope']!='')
		{
		$_scope=$_GET['scope'];
		}
	else{$_scope='The-Whole-Bible';}
	if($_scope)
		{
		$scope_value=$_scope;
		$scope_name=$Scope[$scope_value];
		}
  
	if($_keyword)
		{
		$book_key=getBookByKeyword($_keyword);
		if(isset($book_key['book']))
			{
			$ref_key=getRefByKeyword($_keyword);
#			echo getPrintR($ref_key);
			$_book=$book_key['book'];
			$bid=$ref_key['bid'];
			$_chapter=$ref_key['chapter'];
			$_Verses=$ref_key['verses'];
			$_book    = $book_key['book'];
			$_chapter = $ref_key['chapter'];
			$_verses  = $ref_key['verses'];
			$_book_abbr=$book_key['kjv_abbr'];
			
			$_chapter=$_chapter*1;

			$shorturl=$book_key['kjv_abbr'].'_'.$_chapter;
			if($_verses){$shorturl.="_{$_verses['ref']}";}
			}
		else
			{
			$_book=0;
			$_chapter=0;
			}

		if(isset($_GET['scope']))
			{
			if($_GET['scope']=='lex')
				{
				$_f='&in=lex';
				}
			else
				{
				$_f='&in='.$scope_value;
				}
			}
      
         
	
		if($_book and $_chapter)
			{
			#      $_debug.='Passage Reference: $shorturl$_f'." ($shorturl) - ($_f)".getPrintR("/search/$shorturl$_f");
			$search_type='reference';
			$reference=$book_key['book'].' '.$ref_key['chapter'];
			$incontext=$book_key['kjv_abbr'].'/'.$ref_key['chapter'];

			if($ref_key['verses']['ref'])
				{
				$reference.=':'.$ref_key['verses']['ref'];
				}
			else
				{
				$redirect="/bible/$_style/$version/$incontext";
#				echo $redirect;
#				echo getPrintR($_debug);
#				exit();
				header("Location: $redirect");
				exit();
				}
			$_keyword=$reference;

			$table='text_kjvs';
	
			if($table=='text_kjv')
				{
				$Verses=getVerses($book_key['id'], $_chapter, $ref_key['verses'], $table, TRUE);
				}
			else
				{
				$Verses=getVerses($book_key['id'], $_chapter, $ref_key['verses'], $table, FALSE);
				}
				
				
			$keyword_display0="$_book $_chapter:{$ref_key['verses']['ref']}";
			
			$chapter=$_chapter;
		
			foreach($Verses['Verses'] as $r=>$Verse)
				{
#				if(isset($Verses['json'][$r]['decoded']['p'])){$_p=$Verses['json'][$r]['decoded']['p'];}
				$Verse['text']=clean_foreign_texts($Verse['text']);
				$Verse['text']=preg_replace('/\s+/',' ',$Verse['text']);
				$Verse['text']=str_replace('&nbsp;',' ',$Verse['text']);
				$Verse['text']=preg_replace('/\[(.*?)\]/', '', $Verse['text']);
				$Verse['text']=capFilter($Verse['text']);
				if(isset($_p) and $r>1){$Verse['text']='&para;'.$Verse['text'];}
				
		
					
				if($r==1 )
					{
					$Verse['text']=str_replace('&para;','',$Verse['text']);
					$first_letter=$Verse['text'][0];
					$Verse['text']=substr($Verse['text'], 1);
					if($first_letter=='�')
						{
						$first_letter=$Verse['text'][0];
						$Verse['text']=substr($Verse['text'], 1);
						}
					if($first_letter==' ')
						{
						$first_letter=$Verse['text'][0];
						$Verse['text']=substr($Verse['text'], 1);
						}
					if($first_letter==' ')
						{
						$first_letter=$Verse['text'][0];
						$Verse['text']=substr($Verse['text'], 1);
						}
#					if($first_letter=='A'){$verse_style=" style=\"text-indent:-10px;\" ";}
					
					$verses.="<p class=\"verse-ref first-verse\" id=\"verse_$chapter"."_1\" ><strong class=\"chapter-number\">$chapter</strong><strong class=\"verse-number\" >$r</strong><span class=\"first-letter\">$first_letter</span>{$Verse['text']}</p>\n";
					}
				elseif($r==1 and $table=='text_kjv')
					{
					$verses.="<p class=\"verse-ref first-verse\" id=\"verse_$chapter"."_1\" $verse_style><strong class=\"chapter-number\">$chapter</strong><strong class=\"verse-number\" $number_style>$r</strong>{$Verse['text']}</p>\n";
					}
				else
					{
					$verses.="<p class=\"verse-ref\" id=\"verse_$chapter"."_$r\" ><strong class=\"verse-number\" >$r</strong> {$Verse['text']}</p>\n";
						
					}
				}			
				
			$bka=getAbbrFromBookID($bid);
			$shortened_url="-$bka"."_$chapter"."_{$ref_key['verses']['ref']}";
			$smarty->assign('book', $_book);
			$smarty->assign('_book', $_book_abbr);
			$smarty->assign('chapter', $_chapter);
			$smarty->assign('reference', $reference);
			$smarty->assign('short_url', $shortened_url);
			}
    	else
			{
			/************************************************
			 * how many keywords?
			 * keywords or phrase?
			 *
			 * if 1 keyword search within scope
			 * if 2 keywords search && within scope
			 * if phrase search within scope
			 *
			 * Verses = first page
			 * also send pagination
			 *
			 *	one word...
				*	two words...
				*	phrase...
				*	wild card...
				*
				*/
			$scope=$_GET['scope'];
			$_scope='';
			if(strlen($scope)>1){$_scope="&scope=$scope";}
			$version=strtolower($_GET['version']);
			$__version='';
			if($version)
				{$__version="&version=$version";}
			else
				{$version='kjv';}
			$table=$Versions[$version];
			$next='';$prev='';$page_start=1;
			$versecount=getVerseCountByKeyword($_keyword,$scope,$table);
			$pages=ceil($versecount/20);
			if($versecount<20)
				{
				$page_end=$versecount;
				}
			else
				{
				$page_end=20;
				$url="/search/?keyword=$_keyword$_scope$__version";
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
				
			$Verses=getVersesByKeyword($_keyword,$scope,$page_start-1,$table);
			if($Verses)
				{
				foreach($Verses as $Verse)
					{
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
						<result
							onmouseover=\"this.style.backgroundColor='#ffffcc';this.style.border='1px solid #cccc99'\"
							onmouseout=\"this.style.backgroundColor='';this.style.border='1px solid #ffffff'\">
							<ref>
								<span data-toggle=\"tooltip\" data-placement=\"top\" title=\"view chapter\">
									<a href=\"/bible/study/kjv/$abbr/$chapter\">$book $chapter</a></span>:<span
											data-toggle=\"tooltip\" data-placement=\"top\" title=\"view verse\"><a
											href=\"/search/?keyword=$abbr+$chapter:$v\">$v</a>
								</span>
							</ref>
							<verse>
								$text
							</verse>
							<note>
								<a href=\"/bible/$_style/$user_version/$abbr/$chapter\">...in context</a>
							</note>
						</result>	";
						
					$verses.=$verse;
					$keyword_display0=$keyword_display." in ".$Version_titles[$version];
					}
				}
			
	#			$_debug.="Verses".getPrintR($Verses);

			$keyword_script='var keyword=$.urlParam(\'keyword\');			
			
			$(function(){
				$(\'#filter_by\').load(\'/tools/results_found.php?keyword=\'+keyword+\'&version='.$_version.'\');
				$(\'#other_versions\').load(\'/tools/version_results_found.php?keyword=\'+keyword);
				setTimeout("$(\'[data-toggle=\"tooltip\"]\').tooltip();",1000);
			})';
				
			$smarty->assign('keyword_script', $keyword_script);
			$smarty->assign('verses', $verses);
			$smarty->assign('pagination', $pagination);
			$smarty->assign('page_start', $page_start);
			$smarty->assign('page_end', $page_end);
			$smarty->assign('versecount', $versecount);
			
			$search_type='keyword';
			$shortened_url="-key_$_keyword";
			$smarty->assign('short_url', $shortened_url);
			}
		}
	if(!isset($incontext_url)){$incontext_url='';}
	if(!$verses){$verses='<h4>Sorry, there is no verse that matches that.</h4>';}
  
	$_keyword=str_replace('"','&quot;',$_keyword);
	$smarty->assign('style', '');
	$smarty->assign('section', '');
	$smarty->assign('language', '');
	$smarty->assign('style_url', '');
	$smarty->assign('show_layout', FALSE);
	$smarty->assign('show_outline_option', FALSE);
#	$smarty->assign('url_ref_trans', "/$_version_short");
	$smarty->assign('function','search');
	$smarty->assign('incontext_url', $incontext_url);
	$smarty->assign('verses', $verses);
	$smarty->assign('keyword', $_keyword);
	$smarty->assign('keyword_display0', $keyword_display0);
#	$smarty->assign('keyword_display', $keyword_display);
	$smarty->assign('search_type', $search_type);
#	$smarty->assign('scope_change',
#			"$('#scope').text('{$scope_name}');$('#scopeField').val('{$scope_value}');$('#version').text('{$_display_version_short}');$('#versionField').val('{$_version_short}')");
  
	$smarty->assign('URI', $uri);
	#  $smarty->assign('debug', $_debug);
	$smarty->assign('authorized', FALSE);
	$smarty->assign('page_title', "Search :$_keyword");
#	$includes="includes/search.php<br>$includes";


?>