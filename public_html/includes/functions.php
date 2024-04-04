<?php


function connect2db()
	{
	global $_server,$_debug,$dbuser,$dbpassword,$dbname;
	$_mysql = mysqli_connect('localhost',$dbuser,$dbpassword,$dbname);
	if(mysqli_errno($_mysql)){$_debug['mysql_error'][]= mysqli_error($_mysql);}
	mysqli_set_charset( $_mysql,'utf8mb4');
	return $_mysql;
	}


    
    
function getPrintR($array,$raw=0)
	{
	//hold on to the output
	ob_start();
	print_r($array);
	//store the output in a string
	$out =ob_get_contents();
	//delete the output, because we only wanted it in the string
	ob_clean();
	if($raw)
		{
		return $out;
		}
	else
		{
		return "<pre style=\"margin-top:0px\">$out</pre>";
		}
	}
	




function getBookByKeyword($k)
	{
	global $_mysql,$_debug;
	$Return=[];

	$_k=urldecode($k);
	$_k=preg_replace("/(\s){2,}/", ' ', $_k);   //remove extra spaces
	$_k=str_replace('.','',$_k);                  //remove periods
	$_k=str_replace('1st ', '1 ', $_k);           //make ordinals regular numbers
	$_k=str_replace('2nd ', '2 ', $_k);
	$_k=str_replace('3rd ', '3 ', $_k);
	$_k=preg_replace('/^i /i', "1 ", $_k); 
	$_k=preg_replace('/^ii /i', "2 ", $_k);
	$_k=preg_replace('/^iii /i', "3 ", $_k);

	if(preg_match('/^[1-3][a-zA-Z]/',$_k))        //catch messed up beginning numbers
		{
		$_k=preg_replace('/^1/', '1 ',$_k);
		$_k=preg_replace('/^2/', '2 ',$_k);
		$_k=preg_replace('/^3/', '3 ',$_k);
		}
	$Ref_keys=explode(' ',$_k);                  //explode by spaces
	if(!$Ref_keys[0])                            //if first element is epmty, remove
		{
		$toss=array_shift($Ref_keys);
		}
	if(preg_match('/^[1-9]/',$Ref_keys[0]))      //if first element is a number, comine with second to make the book key
		{
		$book_key=$Ref_keys[0].' '.$Ref_keys[1];
		}
	else
		{
		$book_key=$Ref_keys[0];
		}

	if($book_key=='1 JN'){$book_key='1JN';}
	if($book_key=='2 JN'){$book_key='2JN';}
	if($book_key=='3 JN'){$book_key='3JN';}
			
			
	if(strtolower($book_key)=='jud'){$book_key='Judges';}
	if(strtolower($book_key)=='eph'){$book_key='Ephesians';}
      
    $_c=0;
    $queryText = sprintf("SELECT * FROM `kjv_books` WHERE `abbr` LIKE '%%%s%%' || `book` LIKE '%s' || `kjav_abr` LIKE '%s' || `book` SOUNDS LIKE '%s'
                         ORDER BY
                         case when `abbr` LIKE '%s' then 4 else 0 end
                       + case when `book` LIKE '%s' then 3 else 0 end
                       + case when `kjav_abr` LIKE '%s' then 2 else 0 end
                       + case when `book` SOUNDS LIKE '%s' then 1 else 0 end
                         DESC LIMIT 1",
                 mysqli_real_escape_string($_mysql,$book_key),
                 mysqli_real_escape_string($_mysql,$book_key),
                 mysqli_real_escape_string($_mysql,$book_key),
                 mysqli_real_escape_string($_mysql,$book_key),
                 mysqli_real_escape_string($_mysql,$book_key),
                 mysqli_real_escape_string($_mysql,$book_key),
                 mysqli_real_escape_string($_mysql,$book_key),
                 mysqli_real_escape_string($_mysql,$book_key));
    $query=mysqli_query($_mysql,$queryText);
    if(mysqli_errno($_mysql)){echo ": " . mysqli_error($_mysql) . "<br>$queryText\n<hr>";}
    if(mysqli_num_rows($query))
      {
	  $c=0;
      while ($dbRow = mysqli_fetch_assoc($query)) 
        {
        $c++;
        if($dbRow['book']=='Psalms'){$dbRow['book']='Psalm';}
        $Return['id']=$dbRow['id'];
        $Return['book']=$dbRow['book'];
        $Return['kjv_abbr']=$dbRow['kjav_abr'];
        $Return['longname']=$dbRow['longname'];
        $Return['chapters']=$dbRow['chapters'];
        }
      }      
#    $Return['queryText']=$queryText;
    return $Return;
    }


	
function getRefByKeyword($k)
	{
	global $_mysql;
	$vid='';$_V=[];$_V2=[];$vid2='';$bid2=0;$cid2=0;
	$_k=urldecode($k);
	$_k=str_ireplace('Song of Solomon','Song',$_k);
	$_k = preg_replace("/(\s){2,}/", ' ', $_k);   //remove extra spaces
	$_k=str_replace('.','',$_k);                  //remove periods
	$_k=str_replace('1st ', '1 ', $_k);           //make ordinals regular numbers
	$_k=str_replace('2nd ', '2 ', $_k);
	$_k=str_replace('3rd ', '3 ', $_k);		
	$_k=preg_replace('/^i /i', "1 ", $_k); 
	$_k=preg_replace('/^ii /i', "2 ", $_k);
	$_k=preg_replace('/^iii /i', "3 ", $_k);

	if(preg_match('/^[1-3][a-zA-Z]/',$_k))        //catch messed up beginning numbers
		{
		$_k=preg_replace('/^1/', '1 ',$_k);
		$_k=preg_replace('/^2/', '2 ',$_k);
		$_k=preg_replace('/^3/', '3 ',$_k);
		}

	$_k=ltrim($_k);$_k2='';

	if(strstr($_k,'-'))
		{
		$Keys=explode('-',$_k);
		if(strstr($Keys[1],':'))
			{
			$_k=$Keys[0].'-';
			$_k2=$Keys[1];
			}
		}


	if(strstr($_k,' '))
		{
		$Ref_keys=explode(' ',$_k);                  //explode by spaces
		if(!$Ref_keys[0])                            //if first element is empty, remove
			{
			$toss=array_shift($Ref_keys);
			}
		if(preg_match('/[1-3]/',$Ref_keys[0]))
			{
			$num=$Ref_keys[0];
			$toss=array_shift($Ref_keys);
			$Ref_keys[0]="$num ".$Ref_keys[0];
			}
#		$Return['debug']['Ref_keys']=$Ref_keys;
		$BookData=getBookIdFromVagueTitle($Ref_keys[0]);
#		$Return['debug']['BookData']=$BookData;
		if(isset($BookData['book']))
			{
			$Return['bookname']=$BookData['book'];
			$bid=$BookData['id'];
			$Return['bid']=$bid;
			}
		else
			{
			$bid=0;
			}
		if($bid==31 or $bid==57 or $bid==63 or $bid==64 or $bid==65)
			{
			// if there's no colon in the number reference ($Ref_key[1]) then set this as the verse for chapter 1
			if(!strstr($Ref_keys[1],':'))
				{
				$Ref_keys[1]="1:{$Ref_keys[1]}";
				}
			}
		$toss=array_shift($Ref_keys);
		$ref_key=implode($Ref_keys);
		$ref_elements=explode(':',$ref_key);
#		$Return['debug']['ref_elements']=$ref_elements;
		$cid=$ref_elements[0];
		$Return['chapter']=$cid;
		}
	else
		{
		$Ref_keys=[];$bid=0;$cid=0;
		}


	if(strstr($_k2,' '))
		{
		$Ref_keys=explode(' ',$_k2);                  //explode by spaces
		if(!$Ref_keys[0])                            //if first element is empty, remove
			{
			$toss=array_shift($Ref_keys);
			}
		if(preg_match('/[1-3]/',$Ref_keys[0]))
			{
			$num=$Ref_keys[0];
			$toss=array_shift($Ref_keys);
			$Ref_keys[0]="$num ".$Ref_keys[0];
			}
#		$Return['debug']['Ref_keys2']=$Ref_keys;
		$BookData=getBookIdFromVagueTitle($Ref_keys[0]);
		$Return['debug']['BookData2']=$BookData;
		if(isset($BookData['book']))
			{
			$Return['bookname2']=$BookData['book'];
			$bid=$BookData['id'];
			$Return['bid']=$bid;
			}
		else
			{
			$bid=0;
			}
		$toss=array_shift($Ref_keys);
		$ref_key2=implode($Ref_keys);
		$ref_elements2=explode(':',$ref_key2);
#		$Return['debug']['ref_elements2']=$ref_elements2;
		$cid=$ref_elements2[0];
		$Return['chapter2']=$cid;
		}
	else
		{
		$Ref_keys=[];$bid=0;$cid=0;
		}

	
	if(isset($ref_elements[1]))
		{
		$verses=preg_replace('/[^0-9-,]+/','',$ref_elements[1]);
#		$Return['tracking']['verses']=$verses;
		if(strstr($verses,','))
			{
			$vlist=explode(',',$verses);
			for($i=0;$i<count($vlist);$i++)
				{
				if(strstr($vlist[$i],'-'))
					{
					list($start,$finish)=explode('-', $vlist[$i]);
					for($i2=$start;$i2<=$finish;$i2++)
						{
						$_V[]=$i2;
						}
					}
				else
					{
					$_V[]=$vlist[$i];
					}      
				}
			}
		elseif(strstr($verses, '-'))
			{
			$vlist=explode('-',$verses);
#			$Return['tracking']['vlist']=$vlist;
#			$Return['tracking']['count-vlist']=count($vlist);
			if(count($vlist)==2)
				{
				list($start,$finish)=explode('-', $verses);
				if(!$finish and isset($BookData['book']))
					{
					$finish=getVersesInChapter($BookData['book'],$cid);
#					$Return['tracking']['finish']=$finish;
					}
				for($i=$start;$i<=$finish;$i++)
					{
					$_V[]=$i;
					}
#				$Return['tracking']['Verse List']=$_V;
				}
			}
		else
			{
			$_V[]=$verses;
			$vid=$verses;
			}
		}
	else
		{
		$_V=[];$verses='';
		}

	if(isset($ref_elements2[1]))
		{	
		$verses2=preg_replace('/[^0-9-,]+/','',$ref_elements[1]);
		if(strstr($verses,','))
			{
			$vlist=explode(',',$verses2);
			for($i=0;$i<count($vlist);$i++)
				{
				if(strstr($vlist[$i],'-'))
					{
					list($start,$finish)=explode('-', $vlist[$i]);
					for($i2=$start;$i2<=$finish;$i2++)
						{
						$_V2[]=$i2;
						}
					}
				else
					{
					$_V2[]=$vlist[$i];
					}      
				}
			}
		elseif(strstr($verses2, '-'))
			{
			$vlist=explode('-',$verses2);
			if(count($vlist)==2)
				{
				list($start,$finish)=explode('-', $verses);
				for($i=$start;$i<=$finish;$i++)
					{
					$_V2[]=$i;
					}
				}
			}
		else
			{
			$_V2[]=$verses2;
			$vid2=$verses2;
			}
		}
	else
		{
		$_V2=[];$verses2='';
		}

	#$Return['debug']['rid']="$bid : $cid : $vid";
	if($vid)
		{
		$Rid=getVerseIDByRef($bid,$cid,$vid);
		if(isset($Rid['text'])){$Return['rid']=$Rid['text'];}
		}
	if($vid2)
		{
		$Rid2=getVerseIDByRef($bid2,$cid2,$vid2);
		if(isset($Rid2['text'])){$Return['rid2']=$Rid2['text'];}
		}
	$Return['verses']=$_V;
#	$Return['verses2']=$_V2;
	$Return['verses']['ref']=$verses;	
#	$Return['verses2']['ref']=$verses2;		
	return $Return;
	}

function getBookIdFromVagueTitle($title)
    {
    global $_mysql;
    trim($title);
    $title=str_replace("\n","",$title);
    $title=str_replace("\r","",$title);
		if(strtolower($title=='eph')){$title="Ephesians";}
    $queryText = sprintf("SELECT * FROM `kjv_books` WHERE `book` LIKE '%s' OR `kjav_abr` LIKE '%s' OR `abbr` LIKE '%%%s%%' LIMIT 1;",
                 mysqli_real_escape_string($_mysql,$title),
                 mysqli_real_escape_string($_mysql,$title),
                 mysqli_real_escape_string($_mysql,$title));
    $query=mysqli_query($_mysql,$queryText);
    if(mysqli_errno($_mysql)){echo ": " . mysqli_error($_mysql) . "<br>\n$queryText<hr>";}
    $result = mysqli_fetch_assoc($query);
    $result['querytext']=$queryText;
    return $result;
    }


function getBookTitleFromId($bookid)
    {
	global $_mysql;
	if($bookid)
		{
	    $row=dbFetch1('kjv_books',array('id'=>$bookid),'book');
		return $row['book'];
		}
	else
		{
		return '';
		}      
    }

function getVersesInChapter($book,$chapter,$debug=0)
    {
    global $_mysql;
    $bid=getBookIdFromTitle($book);
    $result=dbFetch('kjv_ref',array('book'=>$bid,'chapter'=>$chapter));
    if($debug)
      {
      $result['book']=$book;
      $result['chapter']=$chapter;
      return $result;
      }
    else
      {
      return count($result)-1;
      }
    }


function getBookIdFromTitle($booktitle)
    {
    global $_mysql;
    if($booktitle=='Psalm'){$booktitle='Psalms';}
	$row=dbFetch1('kjv_books',array('book'=>$booktitle),'id');
	if(isset($row['id']))
		{
		return $row['id'];
		}
	else
		{
		return '';
		}
      
    }
	

function getVerses($book, $chapter, $verses, $table='text_kjvs')
	{
	global $_mysql,$_debug;
	$_v=$verses;$class='';$mydata='';$classes=[];$vQuery=[];
	$introduction='';$verseCode=[];$_verses=[];$_Verses=[];
	if(!isset($verses['ref']))
		{
		$verses=[];
		$_book=getBookTitleFromId($book);
		$chapnum=getVersesInChapter($_book,$chapter);
		for($i=1;$i<=$chapnum;$i++)
			{
			$verses[]=$i;
			}
		}

	if(is_array($verses)){unset($verses['ref']);}
	
	foreach($verses as $key=>$verse)
		{
		$vQuery[]="`verse`=$verse ";
		}  
	$verse_query=implode(' || ',$vQuery);
	if($verse_query){$verse_query=" && ($verse_query)";}
	
	$queryText = sprintf("SELECT `verse`,`text`,`paragraph` FROM `kjv_ref` WHERE `book`='$book' && `chapter`=$chapter$verse_query");
	
#	$return['queryText1']=$queryText;
	$return['book']=$book;
	$return['chapter']=$chapter;
	$return['verses']=$verses;
#	$return['original_verse']=$_v;

	$query=mysqli_query($_mysql, $queryText);
	if(mysqli_errno($_mysql)){echo ": " . mysqli_error($_mysql) . "<br>$queryText\n<hr>";}
	if(mysqli_num_rows($query))
		{
		while ($dbRow = mysqli_fetch_assoc($query)) 
			{
			$return['result'][]=$dbRow;
			$v=$dbRow['verse'];
			$_verses[$v]=$dbRow['text'];
			$return['par'][$v]=$dbRow['paragraph'];
			}
		}
	
	if($book<40){$language='h';}
	else{$language='g';}
	
	$_c=0;  
	foreach($_verses as $v=>$_verse)
		{
		$_c++;$offset=0;
		$this_text=dbFetch1($table,array('id'=>$_verse),'text');
		$this_text['text']=capFilter($this_text['text'],1);
		$return['vid'][$v]=$_verse;
		$_Verses[$v]=$this_text;
		$verseCode='';
		}			
	$return['Verses']=$_Verses;
	return $return;
	}



function capFilter($text,$p=0)
	{
	if($p==1)
		{
		$text=str_replace('THE LORD OUR RIGHTEOUSNESS', '!*The Lord Our Righteousness*!', $text);
		}
	else
		{
		$text=str_replace('BRANCH', '<span class="smallcaps">Branch</span>', $text);
		$text=str_replace('LORD', '<span class="smallcaps">Lord</span>', $text);
		$text=str_replace('<span class="smallcaps">Lord</span>\'S', '<span class="smallcaps">Lord\'s</span>', $text);
		$text=str_replace('GOD', '<span class="smallcaps">God</span>', $text);
		$text=str_replace('JEHOVAH', '<span class="smallcaps">Jehovah</span>', $text);
		$text=str_replace('MENE', '<span class="smallcaps">Mene</span>', $text);
		$text=str_replace('TEKEL', '<span class="smallcaps">Tekel</span>', $text);
		$text=str_replace('UPHARSIN', '<span class="smallcaps">Upharsin</span>', $text);
		$text=str_replace('PERES', '<span class="smallcaps">Peres</span>', $text);
		$text=str_replace('HOLINESS', '<span class="smallcaps">Holiness</span>', $text);
		$text=str_replace('UNTO', '<span class="smallcaps">Unto</span>', $text);
		$text=str_replace('THE', '<span class="smallcaps">The</span>', $text);
		$text=str_replace('THIS', '<span class="smallcaps">This</span>', $text);
		$text=str_replace('IS', '<span class="smallcaps">Is</span>', $text);
		$text=str_replace('KING', '<span class="smallcaps">King</span>', $text);
		$text=str_replace('OF', '<span class="smallcaps">Of</span>', $text);
		$text=str_replace('JEWS', '<span class="smallcaps">Jews</span>', $text);
		}
	return $text;
	}


function getVerseCountByKeyword($keyword,$scope='',$table='text_kjvs')
	{
	global $_mysql,$_debug;
	$keyword=strtolower($keyword);
	if(strstr($keyword,'"'))
		{
		$Keywords=explode('"',$keyword);
		foreach($Keywords as $kw)
			{
			$kw=str_replace(' ','_',trim($kw));
			$KW[]=$kw;
			}
		$keyword=trim(implode(' ',$KW));
		$KW='';
		}
	if(strstr($keyword,' '))
		{
		$Keywords=explode(' ',$keyword);
		foreach($Keywords as $kw)
			{
			$kw=str_replace('_',' ',$kw);
			$kw=str_replace('*','[a-zA-Z]*',$kw);
			$SearchKey[]="`$table`.`text` REGEXP '[[:<:]]".$kw."[[:>:]]'";
			}
		$search_key=implode(' AND ',$SearchKey);
		}
	else
		{
		$keyword=str_replace('_',' ',$keyword);
		$keyword=str_replace('*','[a-zA-Z]*',$keyword);
		$search_key="`$table`.`text` REGEXP '[[:<:]]".$keyword."[[:>:]]'";
		}
	if($scope)
		{
		if(!$ScopeKey[$scope])
			{
			$book=str_replace('-',' ',$scope);
			$bid=getBookIdFromTitle($book);
			if($bid)
				{
				$ScopeKey[$scope]="&& `kjv_ref`.`book` = '$bid'";
				}
			}
		$search_key.=' '.$ScopeKey[$scope];
		}
	$queryText = sprintf("SELECT COUNT(*) FROM `$table` JOIN `kjv_ref` ON `kjv_ref`.`id`=`$table`.`id`
		WHERE $search_key");
	$rowCount=mysqli_fetch_row(mysqli_query($_mysql,$queryText));
	$total_records = $rowCount[0];
	return $total_records;
	}

function getVersesByKeyword($keyword,$scope,$start,$table='text_kjvs')
	{
    global $_mysql,$_debug,$ScopeKey;
	$keyword=strtolower($keyword);
	if(strstr($keyword,'"'))
		{
		$Keywords=explode('"',$keyword);
#			$_debug.="Keywords: ".getPrintR($Keywords);
		foreach($Keywords as $kw)
			{
			$kw=str_replace(' ','_',trim($kw));
			$KW[]=$kw;
			}
		$keyword=trim(implode(' ',$KW));
		$KW='';
#			$_debug.="keyword: $keyword<br>";
		}
	if(strstr($keyword,' '))
		{
		$Keywords=explode(' ',$keyword);
		foreach($Keywords as $kw)
			{
			$kw=str_replace('_',' ',$kw);
			$kw=str_replace('*','[a-zA-Z]*',$kw);
			$SearchKey[]="`$table`.`text` REGEXP '[[:<:]]".$kw."[[:>:]]'";
			}
		$search_key=implode(' AND ',$SearchKey);
		}
	else
		{
		$keyword=str_replace('_',' ',$keyword);
		$keyword=str_replace('*','[a-zA-Z]*',$keyword);
		$search_key="`$table`.`text` REGEXP '[[:<:]]".$keyword."[[:>:]]'";
		}
	if($scope)
		{
		if(!$ScopeKey[$scope])
			{
			$book=str_replace('-',' ',$scope);
			$bid=getBookIdFromTitle($book);
			if($bid)
				{
				$ScopeKey[$scope]="&& `kjv_ref`.`book` = '$bid'";
				}
			}
		$search_key.=' '.$ScopeKey[$scope];
		}
	$queryText = sprintf("SELECT `$table`.`text`,`$table`.`id`,`kjv_ref`.`book`,`kjv_ref`.`chapter`,
												`kjv_ref`.`verse`,`kjv_books`.`book` as 'bookname',`kjv_books`.`kjav_abr` FROM `$table` 
			JOIN `kjv_ref` ON `kjv_ref`.`text`=`$table`.`id`
			JOIN `kjv_books` ON `kjv_books`.`id`=`kjv_ref`.`book`
			WHERE $search_key LIMIT $start, 20");
	$query=mysqli_query($_mysql,$queryText);
	if(mysqli_errno($_mysql)){echo ": " . mysqli_error($_mysql) . "\n<br>$queryText<hr>";}
	if(mysqli_num_rows($query))
		{
		while ($dbRow = mysqli_fetch_assoc($query)) 
			{
			$Result[]=$dbRow;
			}
		}
	if(!isset($Result)){$Result='';}
	return $Result;
	}




function dbFetch1($table,$Where='',$cell='*')
    {
	global $_mysql,$memcached_installed,$memCache;
	$result=[];
    if(!$cell){$cell='*';}
    if($Where)
      {
      foreach($Where as $column => $criteria)
        {
        $criteria=str_replace("'","\'",$criteria);
        $WHere[]="`$column`='$criteria'";
        }
      $where='WHERE '.implode(' AND ',$WHere);
      }
    $queryText = sprintf("SELECT $cell FROM `$table` $where LIMIT 1");

    if($memcached_installed)
      {
      $result=$memCache->get($queryText);
      }
    if(!$result)
      {
      $query=mysqli_query($_mysql,$queryText);
      if(mysqli_errno($_mysql)){echo ": " . mysqli_error($_mysql) . "\n<hr>$queryText";}
      $result = mysqli_fetch_assoc($query);
      if($memcached_installed)
        {
        $memCache->set($queryText, $result, 0, 30);
        }
      }
#    $result['queryText']=$queryText;
    return $result;
    }
    
function dbFetch($table,$Where='',$cell='*',$order='')
	{
	global $_mysql,$memcached_installed,$memCache;
	$result=[];
	if(!$cell){$cell='*';}
	if($Where)
		{
		foreach($Where as $column => $criteria)
			{
			$criteria=str_replace("'","\'",$criteria);
			$WHere[]="`$column`='$criteria'";
			}
		$where='WHERE '.implode(' AND ',$WHere);
		}
	$queryText = sprintf("SELECT $cell FROM `$table` $where $order");

	if($memcached_installed)
		{
		$result=$memCache->get($queryText);
		}
	if(!$result)
		{
		$query=mysqli_query($_mysql,$queryText);
		if(mysqli_errno($_mysql)){echo ": " . mysqli_error($_mysql) . "\n<hr>$queryText";}
		if(mysqli_num_rows($query))
			{
			while ($dbRow = mysqli_fetch_assoc($query)) 
				{
				$result[]=$dbRow;
				}
			}
		if($memcached_installed)
			{
			$memCache->set($queryText, $result, 0, 30);
			}
		}
	if(isset($result[0]) and !is_array($result[0])){$T=$result;$result=[];$result[0]=$T;}
	$result['queryText']=$queryText;
	return $result;
    }    

?>