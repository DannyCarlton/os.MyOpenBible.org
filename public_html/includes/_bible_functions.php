<?php











#################################################################################################

  function getAbbrFromBookID($bid)
    {
    global $_mysql;
    $Return=dbFetch1('kjv_books',Array('id'=>$bid),'`kjav_abr`');
    return $Return['kjav_abr'];
    }
    
    



function getBookByKeyword($k)
	{
	global $_mysql,$_debug;

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
                 mysql_real_escape_string($book_key),
                 mysql_real_escape_string($book_key),
                 mysql_real_escape_string($book_key),
                 mysql_real_escape_string($book_key),
                 mysql_real_escape_string($book_key),
                 mysql_real_escape_string($book_key),
                 mysql_real_escape_string($book_key),
                 mysql_real_escape_string($book_key));
    $query=mysql_query($queryText, $_mysql);
    if(mysql_errno($_mysql)){echo ": " . mysql_error($_mysql) . "<br>$queryText\n<hr>";}
    if(mysql_num_rows($query))
      {
	  $c=0;
      while ($dbRow = mysql_fetch_assoc($query)) 
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

      
    $Return['queryText']=$queryText;
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
                 mysql_real_escape_string($title),
                 mysql_real_escape_string($title),
                 mysql_real_escape_string($title));
    $query=mysql_query($queryText, $_mysql);
    if(mysql_errno($_mysql)){echo ": " . mysql_error($_mysql) . "<br>\n$queryText<hr>";}
    $result = mysql_fetch_assoc($query);
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
    
    
    
    
    
    
    
  function getFilterArray()
    {
    $_filter[1]='The Old Testament';
    $_filter[2]='The New Testament';
    $_filter[3]='The Books of Law';
    $_filter[4]='The Books of History';
    $_filter[5]='The Books of Poetry';
    $_filter[6]='The Major Prophets';
    $_filter[7]='The Minor Prophets';
    $_filter[8]='The Gospels';
    $_filter[9]='The Pauline Epistles';
    $_filter[12]='Genesis';
    $_filter[13]='Exodus';
    $_filter[14]='Leviticus';
    $_filter[15]='Numbers';
    $_filter[16]='Deuteronomy';
    $_filter[17]='Joshua';
    $_filter[18]='Judges';
    $_filter[19]='Ruth';
    $_filter[20]='1 Samuel';
    $_filter[21]='2 Samuel';
    $_filter[22]='1 Kings';
    $_filter[23]='2 Kings';
    $_filter[24]='1 Chronicles';
    $_filter[25]='2 Chronicles';
    $_filter[26]='Ezra';
    $_filter[27]='Nehemiah';
    $_filter[28]='Esther';
    $_filter[29]='Job';
    $_filter[30]='Psalms';
    $_filter[31]='Proverbs';
    $_filter[32]='Ecclesiastes';
    $_filter[33]='Song of Solomon';
    $_filter[34]='Isaiah';
    $_filter[35]='Jeremiah';
    $_filter[36]='Lamentations';
    $_filter[37]='Ezekiel';
    $_filter[38]='Daniel';
    $_filter[39]='Hosea';
    $_filter[40]='Joel';
    $_filter[41]='Amos';
    $_filter[42]='Obadiah';
    $_filter[43]='Jonah';
    $_filter[44]='Micah';
    $_filter[45]='Nahum';
    $_filter[46]='Habakkuk';
    $_filter[47]='Zephaniah';
    $_filter[48]='Haggai';
    $_filter[49]='Zechariah';
    $_filter[50]='Malachi';
    $_filter[51]='Matthew';
    $_filter[52]='Mark';
    $_filter[53]='Luke';
    $_filter[54]='John';
    $_filter[55]='Acts';
    $_filter[56]='Romans';
    $_filter[57]='1 Corinthians';
    $_filter[58]='2 Corinthians';
    $_filter[59]='Galatians';
    $_filter[60]='Ephesians';
    $_filter[61]='Philippians';
    $_filter[62]='Colossians';
    $_filter[63]='1 Thessalonians';
    $_filter[64]='2 Thessalonians';
    $_filter[65]='1 Timothy';
    $_filter[66]='2 Timothy';
    $_filter[67]='Titus';
    $_filter[68]='Philemon';
    $_filter[69]='Hebrews';
    $_filter[70]='James';
    $_filter[71]='1 Peter';
    $_filter[72]='2 Peter';
    $_filter[73]='1 John';
    $_filter[74]='2 John';
    $_filter[75]='3 John';
    $_filter[76]='Jude';
    $_filter[77]='Revelation';
    return $_filter;
      
    }
    
    
    
    
    
  function getFilterID($name)
    {
    $name=str_replace('-',' ',$name);
    $_filter=getFilterArray();
    foreach($_filter as $id=>$_name)
      {
      if($name==$_name)
        {
        $return=$id;
        break;
        }
      }
    return $return;
    }
    
    
    
    
    
  function getFilterName($id)
    {
    $_filter=getFilterArray();    
    return $_filter[$id];
    }
    
    
    
    
	function getLexDef($strongs,$testament)
		{
		global $_mysql;
		$Return=dbFetch1($testament,array('id'=>$strongs));
		return $Return;
		}
	


	function capFilter($text,$p=0)
		{
#		echo "<!-- $text -->\n\n";
/*
		$Words=explode(' ',$text);
		foreach($Words as $word)
			{
			$WORD=strtoupper($word);
			if($word==$WORD)
				{
				$Text[]="<|*>$word</*|>";
				}
			else
				{
				$Text[]=$word;
				}
			}
		$text=implode(' ',$Text);
		*/
		if($p==1)
			{
			$text=str_replace('THE LORD OUR RIGHTEOUSNESS', '!*The Lord Our Righteousness*!', $text);
#			$text=str_replace('HOLINESS UNTO THE LORD', '!*Holiness Unto The Lord*!', $text);
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
    
    
    
    
	function getOutline($book_chapter, $verse)
		{
		global $_mysql;
		$Return=dbFetch1('outline',array('chapter'=>$book_chapter, 'verse'=>$verse));
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
			$Return['debug']['Ref_keys']=$Ref_keys;
			$BookData=getBookIdFromVagueTitle($Ref_keys[0]);
			$Return['debug']['BookData']=$BookData;
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
			$Return['debug']['ref_elements']=$ref_elements;
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
			$Return['debug']['Ref_keys2']=$Ref_keys;
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
			$Return['debug']['ref_elements2']=$ref_elements2;
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
			$Return['tracking']['verses']=$verses;
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
				$Return['tracking']['vlist']=$vlist;
				$Return['tracking']['count-vlist']=count($vlist);
				if(count($vlist)==2)
					{
					list($start,$finish)=explode('-', $verses);
					if(!$finish and isset($BookData['book']))
						{
						$finish=getVersesInChapter($BookData['book'],$cid);
						$Return['tracking']['finish']=$finish;
						}
					for($i=$start;$i<=$finish;$i++)
						{
						$_V[]=$i;
						}
					$Return['tracking']['_V']=$_V;
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
		$Return['verses2']=$_V2;
		$Return['verses']['ref']=$verses;	
		$Return['verses2']['ref']=$verses2;		
		return $Return;
		}






		
		
	
	function getVerseSpanByKeyword($k)
		{
		global $_mysql;
		$Debug=[];
		$vid='';$_V=[];$_V2=[];$vid2='';$bid2=0;$cid2=0;

		/**  Set Keyword  **/

		$_k=urldecode($k);
		$_k=str_ireplace('Song of Solomon','Song',$_k);
		$_k=str_replace('~',' ',$_k);
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
		if(preg_match('/^[1-3] /',$_k))     
			{
			$_k=preg_replace('/^1 /', '1~',$_k);
			$_k=preg_replace('/^2 /', '2~',$_k);
			$_k=preg_replace('/^3 /', '3~',$_k);
			}

		/**  Keyword Set  **/

		/** Parse Keyword  **/

		$P=explode('-',$_k);
		if(!isset($P[1])){$P[1]=$P[0];}
		$P[1]=trim($P[1]);$ref2='';
		if(strstr($P[0],' ')){list($book,$ref)=explode(' ',$P[0]);}
		else{$book=$P[0];$ref='';}
		list($temp,$ref2)=explode(' ',"{$P[1]} ");
		if(!strstr($temp,' ') and $ref2)
			{
			$book2=$temp;
			}
		else
			{
			$book2=$book;
			$ref2=$P[1];
			if($ref2==$book2){$ref2='';}
			}
#		$book=str_replace('~',' ',$book);
#		$book2=str_replace('~',' ',$book2);
#		$P[0]=str_replace('~',' ',$P[0]);
		$Debug['P']=$P;
		$Return['book']=$book;
		$Debug['ref']=$ref;
		$Debug['ref2']=$ref2;
		if(strstr($ref,':'))
			{
			list($chapter,$verse)=explode(':',$ref);
			if(strstr($ref2,':'))
				{
				$end="$book2 $ref2";
				list($echapter,$everse)=explode(':',$ref2);
				}
			else
				{
				$end = "$book2 $chapter:{$P[1]}"; 
				$echapter=$chapter;
				$everse=$P[1];
				}
			}
		else
			{
			$verse=1;
			$chapter=str_replace("$book ",'',$P[0]);
			if($chapter==$P[1])
				{
				$chapter=1;
				}
			if(strstr($P[1],' '))
				{
				list($toss,$echapter)=explode(' ',$P[1]);
				}
			elseif($P[1]!=$book)
				{
				$echapter=$P[1];
				}
			else
				{
				$echapter=getChaptersInBook(str_replace('~',' ',$book2));
				}
			$end="$book2 $echapter";
			$bookData=getBookIdFromVagueTitle(str_replace('~',' ',$book2));
			$bid2=$bookData['id'];
			$book2=getBookTitleFromId($bid2);
			$everse=getVersesInChapter($book2,$echapter)-1;
			}
		$Return['book2']=$book2;
		$begin=$P[0];
		$Return['begin']=$begin;
		$Return['end']=$end;
		$Bid=getBookIdFromVagueTitle(str_replace('~',' ',$book));
		$bid=$Bid['id'];
		if(!isset($bid2)){$bid2=$bid;}
#		$Bid2=getBookIdFromVagueTitle($book2);
#		$bid2=$Bid2['id'];
		$Return['bid']=$bid;
		$Return['bid2']=$bid2;
		$Bid=getVerseIDByRef($bid,$chapter,$verse);
		$Debug["beginning verse id for $bid, $chapter, $verse"]=$Bid;
		$Return['begin-id']=$Bid['text'];
		$Debug["ending verse id for $bid2, $echapter, $everse"]=getVerseIDByRef($bid2,$echapter,$everse);
		$Eid=getVerseIDByRef($bid2,$echapter,$everse);
		$Return['end-id']=$Eid['text'];
		$Return['debug']=$Debug;
		return $Return;
		}












    
    
    
  function getVerseDataByID($vid)
    {
    global $_mysql;    
	$verseData=dbFetch1('kjv_ref', Array('id'=>$vid));
	$chapter=$verseData['chapter'];
	$verse=$verseData['verse'];
	$bid=$verseData['book'];
	$title=getBookTitleFromId($bid);
	$verseData['bookname']=$title;
	$verseData['reference']="$title $chapter:$verse";    
    return $verseData;
    }


    
    
    
	function getVerseRefByID($vid)
		{
		global $_mysql, $_debug;    
		$verseData=dbFetch1('kjv_ref', Array('id'=>$vid));
		if(isset($verseData['chapter']))
			{
			$chapter=$verseData['chapter'];
			$verse=$verseData['verse'];
			$bid=$verseData['book'];
			$title=getBookTitleFromId($bid);
			$ref="$title $chapter:$verse";    
			}
		else
			{
			$ref='No such verse';
			}
		return $ref;
		}


	function isParagraph($vid)
		{
		global $_mysql;    
		$verseData=dbFetch1('kjv_ref', Array('id'=>$vid));
		if(isset($verseData['chapter']))
			{
			if($verseData['paragraph'])
				{
				$result=TRUE;
				}
			else
				{
				$result=FALSE;
				}
			}
		else
			{
			$result=FALSE;
			}
		return $result;

		}


	function isRef($keyword)
		{
		global $_mysql;
		$Return['keyword']=$keyword;
		$Ref=getRefByKeyword($keyword);
		$bData=getBookIdFromVagueTitle($keyword);
		if(isset($Ref['verses'][1]))
			{
			$Return['type']='passage';
			}
		elseif(isset($Ref['verses'][0]))
			{
			$Return['type']='verse';
			}
		elseif(isset($Ref['chapter']))
			{
			$Return['type']='chapter';
			$Return['bookname']=$Ref['bookname'];
			$Return['chapter']=$Ref['chapter'];
			$chlength=getVersesInChapter($Ref['bookname'],$Ref['chapter']);
			$Return['chapter-length']=$chlength;
			}
		elseif(isset($bData['id']))
			{
			$Return['type']='book';
			}
		else
			{
			$Return['type']='invalid';
			}
		$Return['getRefByKeyword']=$Ref;
		$Return['bData']=$bData;
		return $Return;
		}

    
    
    
  function getVerseText($ref)
		{
		global $_debug;
		$V=[];
		$ref=str_replace('Gen1','Gen 1',$ref);
		$ref=str_replace('Mar1','Mar 1',$ref);
		$book_key=getBookByKeyword($ref);
		$_debug['book_key']=$book_key;
		$ref_key=getRefByKeyword($ref);
		$_book=$book_key['id'];
		$_chapter=$ref_key['chapter'];
		$_verses=$ref_key['verses'];
		$_chapter=$_chapter*1;
		$Verses=getVerses($_book, $_chapter, $_verses, 'text_kjv', FALSE);
	
		foreach($Verses['Verses'] as $r=>$Verse)
			{
			$V[]=$Verse['text'];
			}
		$verses=implode(' ',$V);
		return $verses;      
		}
    
    
    
	function getVerseIDByRef($bid,$cid,$vid)
		{
		$tid=dbFetch1('kjv_ref',array('book'=>$bid,'chapter'=>$cid,'verse'=>$vid),'text');
		return $tid;
		}
    
    
		
		
		
		
    
	function getVerses($book, $chapter, $verses, $table='text_kjv', $encoded=FALSE)
		{
		global $_mysql,$_debug;
		$_v=$verses;$class='';$mydata='';$jsonEncoding=[];$classes=[];$vQuery=[];
		$introduction='';$this_encoding=[];$this_encoding['coding']='';$verseCode=[];$_verses=[];$_Verses=[];
		$style='p';

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
		
		$return['queryText1']=$queryText;
		$return['book']=$book;
		$return['chapter']=$chapter;
		$return['verses']=$verses;
		$return['original_verse']=$_v;
    
		$query=mysql_query($queryText, $_mysql);
		if(mysql_errno($_mysql)){echo ": " . mysql_error($_mysql) . "<br>$queryText\n<hr>";}
		if(mysql_num_rows($query))
			{
			while ($dbRow = mysql_fetch_assoc($query)) 
				{
				$return['result'][]=$dbRow;
				$v=$dbRow['verse'];
				$_verses[$v]=$dbRow['text'];
				$return['par'][$v]=$dbRow['paragraph'];
				}
			}
      
		if($book<40){$language='h';}
		else{$language='g';}
		
		$_lex='strongs';
		$_c=0;  
		foreach($_verses as $v=>$_verse)
			{
			$_c++;$offset=0;
			$this_text=dbFetch1($table,array('id'=>$_verse),'text');
			$this_text['text']=capFilter($this_text['text'],1);
			$return['queryText2'][]=$this_text['queryText'];
			$return['vid'][$v]=$_verse;
			$_Verses[$v]=$this_text;
			if($introduction)
				{
				$return['intro2']=$introduction;$introduction='';
				$return['intro']=implode(' ', $Intro);
				$Intro='';
				}			
			$_Verses[$v]['encoding']=$this_encoding['coding'];
			$_Verses[$v]['coding']=$verseCode;
			$verseCode='';
			}			
		$return['Verses']=$_Verses;
		return $return;
		}
    
    
    
    
    
	function getReading($startRef, $endRef)
		{
		global $_mysql;
		
		$book=$startRef['bid'];
		$chapter=$startRef['chapter'];
		$verse=$startRef['verses']['ref'];
		if(!$verse){$verse=1;}
		$Start=dbFetch1('kjv_ref',Array('book'=>$book,'chapter'=>$chapter,'verse'=>$verse));
		$verses='';
		$book=$endRef['bid'];
		$chapter=$endRef['chapter'];
		$verse=$endRef['verses']['ref'];
		if(!$verse)
			{
			$verse=getLastVerse($book,$chapter);
			}
		$End=dbFetch1('kjv_ref',Array('book'=>$book,'chapter'=>$chapter,'verse'=>$verse));
		
		if(isset($Start['text']))
			{
			$_start=$Start['text'];
			}
		else
			{
			echo "<div style=\"margin-top:200px\">ERROR: Invalid reference. _bible_functions.php getReading() line ".__LINE__.".<br>\$startRef".getPrintR($startRef)."\$Start".getPrintR($Start)."</div>";
			}
		if(isset($End['text']))
			{
			$_end=$End['text'];
			}
		else
			{
			echo "ERROR: Invalid reference. _bible_functions.php getReading() line ".__LINE__." book:$book, chapter: $chapter, verse: $verse.<br>\$endRef".getPrintR($endRef)."\$End".getPrintR($End);
			}
		
		for($i=$_start;$i<=$_end;$i++)
			{
			$_verse=dbFetch1('text_kjv',Array('id'=>$i),'text');
			$Verses[$i]=$_verse['text'];
			}
		return $Verses;
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
    
    
	function getLastVerse($bid,$chapter,$debug=0)
	  {
	  global $_mysql;
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
    
  function getChaptersInBook($book)
    {
	global $_mysql;
	if($book)
		{
	    $result=dbFetch1('kjv_books',array('book'=>$book));
		return $result['chapters'];  
		}
	else
		{
		return '';
		}
    }
    
    
  function getPreviousChapter($booktitle,$chapter)
    {
    global $_mysql;
	if($booktitle=='Psalm'){$booktitle='Psalms';}
	if($booktitle)
		{
	    if($chapter>1)
    	  {
		$new_chapter=$chapter-1;
		$new_book=$booktitle;
		$newbookid=getBookIdFromTitle($booktitle);
		$newbooktitle=getBookTitleFromId($newbookid);
		}
		else   
		{
		$newbookid=getBookIdFromTitle($booktitle)-1;
		if($newbookid==0){$newbookid=66;}
		$newbooktitle=getBookTitleFromId($newbookid);
		$new_chapter=getChaptersInBook($newbooktitle);
		}
		$Result['booktitle']=$newbooktitle;
		$Result['chapter']=$new_chapter;
		$Result['bid']=$newbookid;
		return $Result;
		}
	else
		{
		return '';
		}
	}
	


	function getRefAbbr($ref)
		{
		global $_mysql;
		$Ref=isRef($ref);
		/*
		*/
		if(isset($Ref['getRefByKeyword']['debug']['BookData']['book']))
			{
			$book1=$Ref['getRefByKeyword']['debug']['BookData']['book'];
			$bk1=$Ref['getRefByKeyword']['debug']['BookData']['abbr'];
			$B=explode(', ',$bk1);
			$bk1=$B[0];
			$_ref=str_replace($book1,$bk1,$ref);
			$book2=$Ref['getRefByKeyword']['debug']['BookData2']['book'];
			$bk2=$Ref['getRefByKeyword']['debug']['BookData2']['abbr'];
			$B=explode(', ',$bk2);
			$bk2=$B[0];
			$_ref=str_replace($book2,$bk2,$_ref);
			}
		else
			{
#			print_r($Ref);
			$_ref='';
			}		
		return $_ref;
		}
    
    
  function getNextChapter($booktitle,$chapter)
    {
    global $_mysql;
	if($booktitle=='Psalm'){$booktitle='Psalms';}
	if($booktitle)
		{
		$last_chapter=getChaptersInBook($booktitle);
		if($chapter<$last_chapter)
		{
		$new_chapter=$chapter+1;
		$new_book=$booktitle;
		$newbookid=getBookIdFromTitle($booktitle);
		$newbooktitle=getBookTitleFromId($newbookid);
		}
		else   
		{
		$newbookid=getBookIdFromTitle($booktitle)+1;
		if($newbookid==67){$newbookid=1;}
		$newbooktitle=getBookTitleFromId($newbookid);
		$new_chapter=1;
		}
		$Result['booktitle']=$newbooktitle;
		$Result['chapter']=$new_chapter;
		$Result['bid']=$newbookid;
		return $Result;
		}
	else
		{
		return '';
		}
    }
    
    
    
    
  function getXref($bid,$chapter,$Verses,$source='xref_holman')
    {
	global $_mysql;
#    if($source=='xref_tsk'){$order='ORDER BY `order`';}
	$Result=dbFetch($source, array('book'=>$bid,'chapter'=>$chapter),'*');
    return $Result;    
    }
    
  function get1Xref($bid,$chapter,$verse)
    {
	global $_mysql,$_debug;
	$order='';
    $Result=dbFetch('xref_holman', array('book'=>$bid,'chapter'=>$chapter,'verse'=>$verse),'*',$order);
    $Result['debug']=Array('book'=>$bid,'chapter'=>$chapter,'verse'=>$verse);
    return $Result;    
    }
		
		
		
	function getVerseCountByKeyword($keyword,$scope,$table='text_kjv')
		{
    	global $_mysql,$_debug, $ScopeKey;
		if(!$scope){$scope='The-Whole-Bible';}
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
				$SearchKey[]="`text_kjv`.`text` REGEXP '[[:<:]]".$kw."[[:>:]]'";
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
    $queryText = sprintf("SELECT SQL_CALC_FOUND_ROWS * FROM `$table` JOIN `kjv_ref` ON `kjv_ref`.`id`=`$table`.`id`
        WHERE $search_key");
    $query=mysql_query($queryText, $_mysql);
    
    $total_records = mysql_result(mysql_query("SELECT FOUND_ROWS()",$_mysql),0,0);
    if(mysql_errno($_mysql)){echo ": " . mysql_error($_mysql) . "\n<br>$queryText<hr>";}
#    $_debug.="verse count query text<pre>$queryText</pre>";
		return $total_records;
		}
		
		
	function getVersesByKeyword($keyword,$scope,$start,$table='text_kjv')
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
    $query=mysql_query($queryText, $_mysql);
    if(mysql_errno($_mysql)){echo ": " . mysql_error($_mysql) . "\n<br>$queryText<hr>";}
		if(mysql_num_rows($query))
			{
			while ($dbRow = mysql_fetch_assoc($query)) 
				{
				$Result[]=$dbRow;
				}
			}
		if(!isset($Result)){$Result='';}
#    $_debug.="verse querytext<pre>$queryText</pre>$scope";
		return $Result;
		}
		
		
		
		
    
    
    
    
    
    

$Book['GEN']='Genesis';
$Book['EXO']='Exodus';
$Book['LEV']='Leviticus';
$Book['NUM']='Numbers';
$Book['DEU']='Deuteronomy';
$Book['JOS']='Joshua';
$Book['JDG']='Judges';
$Book['RUT']='Ruth';
$Book['1SA']='1 Samuel';
$Book['2SA']='2 Samuel';
$Book['1KI']='1 Kings';
$Book['2KI']='2 Kings';
$Book['1CH']='1 Chronicles';
$Book['2CH']='2 Chronicles';
$Book['EZR']='Ezra';
$Book['NEH']='Nehemiah';
$Book['EST']='Esther';
$Book['JOB']='Job';
$Book['PSA']='Psalms';
$Book['PRO']='Proverbs';
$Book['ECC']='Ecclesiastes';
$Book['SOS']='Song of Solomon';
$Book['ISA']='Isaiah';
$Book['JER']='Jeremiah';
$Book['LAM']='Lamentations';
$Book['EZE']='Ezekiel';
$Book['DAN']='Daniel';
$Book['HOS']='Hosea';
$Book['JOE']='Joel';
$Book['AMO']='Amos';
$Book['OBA']='Obadiah';
$Book['JON']='Jonah';
$Book['MIC']='Micah';
$Book['NAH']='Nahum';
$Book['HAB']='Habakkuk';
$Book['ZEP']='Zephaniah';
$Book['HAG']='Haggai';
$Book['ZEC']='Zechariah';
$Book['MAL']='Malachi';
$Book['MAT']='Matthew';
$Book['MAR']='Mark';
$Book['LUK']='Luke';
$Book['JOH']='John';
$Book['ACT']='Acts';
$Book['ROM']='Romans';
$Book['1CO']='1 Corinthians';
$Book['2CO']='2 Corinthians';
$Book['GAL']='Galatians';
$Book['EPH']='Ephesians';
$Book['PHI']='Philippians';
$Book['COL']='Colossians';
$Book['1TH']='1 Thessalonians';
$Book['2TH']='2 Thessalonians';
$Book['1TI']='1 Timothy';
$Book['2TI']='2 Timothy';
$Book['TIT']='Titus';
$Book['PHM']='Philemon';
$Book['HEB']='Hebrews';
$Book['JAS']='James';
$Book['1PE']='1 Peter';
$Book['2PE']='2 Peter';
$Book['1JN']='1 John';
$Book['2JN']='2 John';
$Book['3JN']='3 John';
$Book['JDE']='Jude';
$Book['REV']='Revelation';



    $book_list="<!-- start --><div class=\"row\" style=\"margin-left:-5px\"><div class=\"col-sm-4 col-xs-4\"><ul class=\"multi-column-dropdown\">";
    $_b=0;$_c=1;$chapter='1';
    foreach($Book as $bk=>$_book)
      {
      $_b++;
      $bk=strtolower($bk);
      if($_b==23 or $_b==45)
        {
				$_c++;
				if($_c==2){$width=' width-100"';}else{$width='';}
        $book_list.="</ul></div><div class=\"col-sm-4 col-xs-4 $width\"><ul class=\"multi-column-dropdown\">";
        }
      $book_list.="<li><a href=\"/bible/study/kjv/$bk/$chapter\">$_book</a></li>\n";
      }
    
    $book_list.="</ul></div></div>";


$Layouts=Array(
		'trad'=>'Traditional',
		'para'=>'Paragraph',
		'read'=>'Reading');

$Styles=Array(
		'Traditional'=>'study',
		'Paragraph'=>'read',
		'Reading'=>'reading');


$Languages=Array(
		'english'=>'text_kjv',
    'french'=>'text_french',
    'spanish'=>'text_spanish',
    'german'=>'text_german',
    'dutch'=>'text_dutch',
    'italian'=>'text_italian',
    'chinese-traditional'=>'text_chinese_union',
    'chinese-simplified'=>'text_chinese_unions',
    'korean'=>'text_korean',
    'arabic'=>'text_arabic',
    'vietnamese'=>'text_vietnamese');



  $Versions=Array(
    'kjv'=>'text_kjv',
    'asv'=>'text_asv',
    'darby'=>'text_darby',
    'web'=>'text_web',
    'twb'=>'text_nweb',
    'ylt'=>'text_ylt');
  
  $Version_titles=Array(
    'kjv'=>'King James Version',
    'asv'=>'American Standard Version',
    'darby'=>'The Darby Bible',
    'web'=>'World English Bible',
    'twb'=>'The Webster Bible',
    'ylt'=>'Young\'s Literal Translation');
  
  $Scope['The-Whole-Bible']='The Whole Bible';
  $Scope['Old-Testament']='The Old Testament';
  $Scope['New-Testament']='The New Testament';
  $Scope['Books-of-Law']='The Books of Law';
  $Scope['Books-of-History']='The Books of History';
  $Scope['Books-of-Poetry']='The Books of Poetry';
  $Scope['Major-Prophets']='Major Prophets';
  $Scope['Minor-Prophets']='Minor Prophets';
  $Scope['The-Gospels']='The Gospels';
  $Scope['Pauline-Epistles']='The Pauline Epistles';
  $Scope['General-Epistles']='The General Epistles';
	
	
	
  
  $ScopeKey['The-Whole-Bible']='';
  $ScopeKey['Old-Testament']='&& `kjv_ref`.`book` < "40" ';
  $ScopeKey['New-Testament']='&& `kjv_ref`.`book` > "39" ';
  $ScopeKey['Books-of-Law']='&& `kjv_ref`.`book` < "6"';
  $ScopeKey['Books-of-History']='&& `kjv_ref`.`book` > "5" && `kjv_ref`.`book` < "18" ';
  $ScopeKey['Books-of-Poetry']='&& `kjv_ref`.`book` > "17" && `kjv_ref`.`book` < "23" ';
  $ScopeKey['Major-Prophets']='&& `kjv_ref`.`book` > "22" && `kjv_ref`.`book` < "28" ';
  $ScopeKey['Minor-Prophets']='&& `kjv_ref`.`book` > "27" && `kjv_ref`.`book` < "40" ';
  $ScopeKey['The-Gospels']='&& `kjv_ref`.`book` > "39" && `kjv_ref`.`book` < "44" ';
  $ScopeKey['Pauline-Epistles']='&& `kjv_ref`.`book` > "44" && `kjv_ref`.`book` < "59" ';
  $ScopeKey['General-Epistles']='&& `kjv_ref`.`book` > "58" && `kjv_ref`.`book` < "66" ';
	
	
  
  
  $scope_list=' 
  				    <li id="1"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'The Whole Bible\';
													document.getElementById(\'scopeField\').value=\'\'">The Whole Bible</a></li>
						<li role="separator" class="divider"></li>
				    <li id="2"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'The Old Testament\';
													document.getElementById(\'scopeField\').value=\'Old-Testament\'">The Old Testament</a></li>
				    <li id="2"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'The New Testament\';
													document.getElementById(\'scopeField\').value=\'New-Testament\'">The New Testament</a></li>
						<li role="separator" class="divider"></li>
				    <li id="3"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'The Books of Law\';
													document.getElementById(\'scopeField\').value=\'Books-of-Law\'">The Books of Law</a></li>
				    <li id="4"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'The Books of History\';
													document.getElementById(\'scopeField\').value=\'Books-of-History\'">The Books of History</a></li>
				    <li id="5"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'The Books of Poetry\';
													document.getElementById(\'scopeField\').value=\'Books-of-Poetry\'">The Books of Poetry</a></li>
				    <li id="6"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'Major Prophets\';
													document.getElementById(\'scopeField\').value=\'Major-Prophets\'">Major Prophets</a></li>
				    <li id="7"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'Minor Prophets\';
													document.getElementById(\'scopeField\').value=\'Minor-Prophets\'">Minor Prophets</a></li>
				    <li id="8"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'The Gospels\';
													document.getElementById(\'scopeField\').value=\'The-Gospels\'">The Gospels</a></li>
				    <li id="9"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'The Pauline Epistles\';
													document.getElementById(\'scopeField\').value=\'Pauline-Epistles\'">The Pauline Epistles</a></li>
				    <li id="9"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\'The General Epistles\';
													document.getElementById(\'scopeField\').value=\'General-Epistles\'">The General Epistles</a></li>
						<li role="separator" class="divider"></li>';
						
						
  
  
  $version_list=' 
  				    <li id="v1"><a href="#"
													onclick="document.getElementById(\'version\').innerText=\'KJV\';
													document.getElementById(\'versionField\').value=\'kjv\'">KJV - King James Version</a></li>
				    <li id="v2"><a href="#"
													onclick="document.getElementById(\'version\').innerText=\'ASV\';
													document.getElementById(\'versionField\').value=\'asv\'">ASV - American Standard Version</a></li>
				    <li id="v4"><a href="#"
													onclick="document.getElementById(\'version\').innerText=\'Darby\';
													document.getElementById(\'versionField\').value=\'darby\'">Darby - The Darby Bible</a></li>
				    <li id="v5"><a href="#"
													onclick="document.getElementById(\'version\').innerText=\'WEB\';
													document.getElementById(\'versionField\').value=\'web\'">WEB - World English Bible</a></li>
				    <li id="v6"><a href="#"
													onclick="document.getElementById(\'version\').innerText=\'TWB\';
													document.getElementById(\'versionField\').value=\'twb\'">The Webster Bible</a></li>
				    <li id="v7"><a href="#"
													onclick="document.getElementById(\'version\').innerText=\'YLT\';
													document.getElementById(\'versionField\').value=\'ylt\'">Young\'s Literal Translation</a></li>
			';
			
			
			
			
	$ReadingPlans =Array(
				0=>Array('url'=>'','title'=>'No plan selected','days'=>0),
				1=>Array(
						'url'=>'the-bible-in-one-year','title'=>'The Bible in One Year','days'=>365),
				2=>Array(
						'url'=>'the-bible-in-two-years','title'=>'The Bible in Two Years','days'=>731),
				3=>Array(
						'url'=>'the-bible-in-about-3-years','title'=>'The Bible in About 3 Years','days'=>994),
				4=>Array(
						'url'=>'the-old-testament-in-one-year','title'=>'The Old Testament in One Year','days'=>365),
				5=>Array(
						'url'=>'the-new-testament-in-one-year','title'=>'The New Testament in One Year','days'=>365),
				6=>Array(
						'url'=>'the-old-and-new-testament-in-one-year','title'=>'The Old & New Testament in One Year','days'=>365),
				7=>Array(
						'url'=>'the-old-and-new-testament-in-two-years','title'=>'The Old & New Testament in Two Year','days'=>731),
				8=>Array(
						'url'=>'the-old-testament-new-testament-psalms-and-proverbs-in-one-year','title'=>'The Old Testament, New Testament, Psalms & Proverbs in One Year','days'=>365),
				9=>Array(
						'url'=>'the-old-testament-new-testament-psalms-and-proverbs-in-two-years','title'=>'The Old Testament, New Testament, Psalms & Proverbs in Two Years','days'=>731),
				10=>Array(
						'url'=>'chronological-in-one-year','title'=>'Chronological in One Year','days'=>365),
				11=>Array(
						'url'=>'the-old-testament-in-two-years','title'=>'The Old Testament in Two Years','days'=>732),
				12=>Array(
						'url'=>'the-new-testament-in-two-years','title'=>'The New Testament in Two Years','days'=>730),
				13=>Array(
						'url'=>'chronological-in-two-years','title'=>'Chronological in About Two Years','days'=>725),
				14=>Array(
						'url'=>'chronological-in-three-years','title'=>'Chronological in About Three Years','days'=>973)
				);

            
  $_c=9;
  foreach($Book as $abbr=>$name)
    {
    $_c++;
    $value=str_replace(' ','-',$name);
    $Scope[$value]=$name;
		$scope_list.='
				    <li id="'.$_c.'"><a href="#"
													onclick="document.getElementById(\'scope\').innerText=\''.$name.'\';
													document.getElementById(\'scopeField\').value=\''.$value.'\'">'.$name.'</a></li>';
    }
  
  
  if(isset($smarty))
		{
		$smarty->assign('scope_list', $scope_list);
		$smarty->assign('version_list', $version_list);
		}
	
	
	
	
		
		
		function clean_foreign_texts($text)
			{				
#      $Verse['text']=str_replace("\xc2\xa0",' ',$Verse['text']); #\u00a0
#      $Verse['text']=str_replace("\xc2\xb6",'',$Verse['text']); #\u00b6 or ¶
#      $Verse['text']=str_replace('  ',' ',$Verse['text']);
#      $Verse['text']=str_replace('  ',' ',$Verse['text']);
#      $Verse['text']=str_replace('  ',' ',$Verse['text']);
#      $Verse['text']=str_replace("\\u00b6",'',$Verse['text']);

      $text=str_replace("\xc2\xa0",' ',$text); #\u00a0
      $text=str_replace("\xc2\xb6",'',$text); #\u00b6 or ¶
      $text=str_replace("\\u00b6",'',$text);
			return $text;
			}
  
?>