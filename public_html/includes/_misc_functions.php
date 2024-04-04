<?php




	$_host=$_SERVER['HTTP_HOST'];
	
	if(strstr($_host,'g040'))
		{
		$_server='local';
		$_subdomain='g040';
		}
	elseif(strstr($_host,'dev'))
		{
		$_server='dev';
		$_subdomain='dev';
		}
	elseif(strstr($_host,'p500'))
		{
		$_server='local';
		$_subdomain='www';
		}
	else
		{
		$_server='remote';
		$_subdomain='www';
		}
		
		


  function days_since($start_date, $end_date='')
    {
    /* use format January 1, 2016 */
    if(!$end_date)
      {
      $end_date=date('F j, Y');
      }
    $_launch=strtotime($start_date);
    $_today=strtotime($end_date);
    $_since=$_today-$_launch;
    $_since=$_since/24;
    $_since=$_since/60;
    $_since=($_since/60)+1;
    return $_since;
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
		
    
   
   
	function ewchar_to_utf8($matches)
		{
		$ewchar = $matches[1];
		$binwchar = hexdec($ewchar);
		$wchar = chr(($binwchar >> 8) & 0xFF) . chr(($binwchar) & 0xFF);
		return iconv("unicodebig", "utf-8", $wchar);
		}

	function special_unicode_to_utf8($str)
		{
		return preg_replace_callback("/\\\u([[:xdigit:]]{4})/i", "ewchar_to_utf8", $str);
		}
		
		
	function getUserIpAddr()
		{
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
			{
			//ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
			//ip pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		else
			{
			$ip = $_SERVER['REMOTE_ADDR'];
			}
		return $ip;
		}
		


			
		
	function generateAccordion($values)
		{
		$i = 1;
		$html_accordion = '<div class="panel-group" id="accordion1">';
		foreach( $values as $id => $items)
			{
			$html_accordion .= "
			<div class=\"panel panel-info\">\n";
			if(is_array($items) or is_object($items))
				{
				$_id=str_replace(' ','-',$id);
				$_id=str_replace(':','-',$_id);
				$html_accordion .= "
			<div class=\"panel-heading\" data-toggle=\"collapse\" data-parent=\"#accordion3\" data-target=\"#$_id\" style=\"cursor:pointer\">$id &rarr;</div>
			<div id=\"$_id\" class=\"panel-collapse collapse\">";
			$html_accordion.=getPrintR($items);
/*
				foreach($items as $key=>$item)
					{
					$_item="<b>$key</b>".getPrintR($item);
					$html_accordion .= "<p>$_item</p>\n";
					}//end foreach items
					*/	
				$html_accordion .= '</div>';
				}
			else
				{
				$html_accordion .= "
				<div class=\"panel-heading\">$id: $items</div>\n";				
				}
			$html_accordion .= '</div>';
			$i++;
			}//end foreach collapses elements
		$html_accordion .= '</div>';
		return $html_accordion;	
		}	
  
?>