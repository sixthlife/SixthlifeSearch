<?php
/**
 * Convert XML to an Array
 *
 * @param string  $XML
 * @return array
 */

function XMLtoArray($XML)
{
    $xml_parser = xml_parser_create();
    xml_parse_into_struct($xml_parser, $XML, $vals);
    xml_parser_free($xml_parser);
    
    $_tmp='';
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_level!=1 && $x_type == 'close') {
            if (isset($multi_key[$x_tag][$x_level]))
                $multi_key[$x_tag][$x_level]=1;
            else
                $multi_key[$x_tag][$x_level]=0;
        }
        if ($x_level!=1 && $x_type == 'complete') {
            if ($_tmp==$x_tag)
                $multi_key[$x_tag][$x_level]=1;
            $_tmp=$x_tag;
        }
    }
   
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_type == 'open')
            $level[$x_level] = $x_tag;
        $start_level = 1;
        $php_stmt = '$xml_array';
        if ($x_type=='close' && $x_level!=1)
            $multi_key[$x_tag][$x_level]++;
        while ($start_level < $x_level) {
            $php_stmt .= '[$level['.$start_level.']]';
            if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
            $start_level++;
        }
        $add='';
        if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
            if (!isset($multi_key2[$x_tag][$x_level]))
                $multi_key2[$x_tag][$x_level]=0;
            else
                $multi_key2[$x_tag][$x_level]++;
            $add='['.$multi_key2[$x_tag][$x_level].']';
        }
        if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
            if ($x_type == 'open')
                $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
            else
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
            eval($php_stmt_main);
        }
        if (array_key_exists('attributes', $xml_elem)) {
            if (isset($xml_elem['value'])) {
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            foreach ($xml_elem['attributes'] as $key=>$value) {
                $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                eval($php_stmt_att);
            }
        }
    }
    return $xml_array;
}


function spost_exists($title){
	global $wpdb;
	$query_pdt = "select count(*) from ".$wpdb->prefix."posts where post_title = '{$title}'";
	$result_pdt = $wpdb->get_var($query_pdt);
	if($result_pdt<=0){
		return false;
	}
	else{
		return true;
	}
}

function unique_filename($filestr){
	$i=1;
	while(spost_exists($filestr)==true){
		$filestr = $filestr.'_'.$i;
		$i++;
	}
	return $filestr;
}

function clean_fname($new_filename){
  $new_filename = trim($new_filename);
  $remove = array('!','\'', ',',':',';','\\','&', '.' ,'/','\"','|', '+','#', '(',')', '�','"','%','`' );
  $new_filename = str_replace($remove, '',$new_filename );

  $new_filename = str_replace(' ', '-',$new_filename );
  $new_filename = str_replace('--', '-',$new_filename );
  $new_filename = str_replace('--', '-',$new_filename );
    
                           
                            
  $bad = array(
        '../','./','/', '<!--', '-->', '<', '>',
        "'", '"', '&', '$', '#',
        '{', '}', '[', ']', '=',
        ';', '?', '%20', '%22',
        '%3c',      // <
        '%253c',    // <
        '%3e',      // >
        '%0e',      // >
        '%28',      // (
        '%29',      // )
        '%2528',    // (
        '%26',      // &
        '%24',      // $
        '%3f',      // ?
        '%3b',      // ;
        '%3d'       // =
    );
    $new_filename = str_replace($bad, '',$new_filename );
    $new_filename = preg_replace("([^\w\s\d\-_~#,!;:\[\]\(\].]|[\.]{2,})", '', $new_filename);
    return $new_filename;
}

//function to rename a wp attachment

function rename_attacment($post_ID,$newfilename){

    $post = get_post($post_ID);
    $file = get_attached_file($post_ID);
    $path = pathinfo($file);
        //dirname   = File Path
        //basename  = Filename.Extension
        //extension = Extension
        //filename  = Filename

    //$newfilename = "NEW FILE NAME HERE";
    $newfile = $path['dirname']."/".$newfilename.".".$path['extension'];

    rename($file, $newfile);    
    update_attached_file( $post_ID, $newfile );

}

        
function getNewRSS($url){
	global $wpdb;
 	 $query_check = "select count(*) from ".$wpdb->prefix."envatorssurls where rss_url = '{$url}'";
 	 $result_check = $wpdb->get_var($query_check);
 	 
 	 if($result_check==0){

	 $output =	get_web_page( $url );

	 
 if(!empty($output)){
	$output = esc_sql($output);
 	$query = "insert into ".$wpdb->prefix."envatorssurls (rss_url, content) values('{$url}', '{$output}')";
 	$result = $wpdb->query($query);
 	return $result;
 	}
 	else {return 'empty';}
 }
 	else {return 'exists';}
}



function updateRSSUrls($idarray=array()){
global $wpdb; //print_r($idarray);
	if(count($idarray)==0){
 	 $query = "select id, rss_url, update_time from ".$wpdb->prefix."envatorssurls where disabled='0' and update_time < unix_timestamp(now() - interval 1 day)";		
	}
	else if(count($idarray)>1){
 	 $query = "select * from ".$wpdb->prefix."envatorssurls where id IN(".join(",", $idarray).")";		
	}
	else{
 	 $query = "select * from ".$wpdb->prefix."envatorssurls where id = '{$idarray[0]}'";		
	}
	
 	$error = '';
 	$sugg_maxexectime = 300*count($idarray);
        $sugg_maxinputtime = 300*count($idarray);
        
        $oldmaxexectime = ini_get('max_execution_time');
        $oldmaxinputtime = ini_get('max_input_time');
   
     ($oldmaxexectime<$sugg_maxexectime)?  ini_set('max_execution_time',$sugg_maxexectime ):'';
   
     ($oldmaxinputtime<$sugg_maxinputtime)?  ini_set('max_input_time',$sugg_maxinputtime ):''; 

 	 $result = $wpdb->get_results($query);
	//echo $wpdb->num_rows($result);
	
	if(count($idarray)>2 ){
				$ct = count($idarray);
		if(FALSE=== get_transient('thse_qrsspdt')){
			$trans_data = array();
		}
		else{
		$trans_data = get_transient('thse_qrsspdt');	
		}
		$prefix = $wpdb->prefix;
	
		for($i=0;$i< $ct; ){
		$trans_data[] =$idarray[$i] ;	
		$i++;		
		}
		set_transient('thse_qrsspdt',$trans_data ); 
		
				
		$error .= 'Total '.count($trans_data).' Current '. count($idarray)." RSS URLS - new content is being fetched.  You will be Notified by Email or Automatic logs.<br />";
	//	mail('anupam.rekha@satyamtechnologies.net', 'Total '.count($trans_data).' Current '. count($idarray),$error);
		return $error;
	}
		
	
 	 $i=1;
 	 foreach($result as $row){
 

	 $output =	get_web_page( $row->rss_url);

	if(!empty($output)){
		$output =  esc_sql($output);
	 	$query1 = "update ".$wpdb->prefix."envatorssurls set content = '{$output}', update_time = now() where id = {$row->id}";
	 	$result1 = $wpdb->query($query1);
	 	$error .= $row->rss_url. ' updated successfuly.<br />';
	 	}
	 	else {$error .= $row->rss_url. ' is empty or could not be fetched.<br />';} $i++;
	 }

	 
  	if(ini_get('max_execution_time')!==$oldmaxexectime){    
            ini_set('max_execution_time',$oldmaxexectime );
   }
       
   if(ini_get('max_input_time')!==$oldmaxinputtime){    
   		ini_set('max_input_time',$oldmaxinputtime );
   }	

	 return $error;

 }	 

function productsRSS($idarray=array()){
	global $wpdb;
	$error = ''; //echo count($idarray);
	if(count($idarray)==0){
 	 $query = "select * from ".$wpdb->prefix."envatorssurls where disabled='0'";		
	}
	else if(count($idarray)>1){
	 $query = "select * from ".$wpdb->prefix."envatorssurls where id IN(".join(",", $idarray).")";		
		}
		else{
	 $query = "select * from ".$wpdb->prefix."envatorssurls where id = '{$idarray[0]}'";			
		}
		

		       $sugg_maxexectime = 300*count($idarray);
        $sugg_maxinputtime = 300*count($idarray);
        
        $oldmaxexectime = ini_get('max_execution_time');
        $oldmaxinputtime = ini_get('max_input_time');
   
     ($oldmaxexectime<$sugg_maxexectime)?  ini_set('max_execution_time',$sugg_maxexectime ):'';
   
     ($oldmaxinputtime<$sugg_maxinputtime)?  ini_set('max_input_time',$sugg_maxinputtime ):'';  
 
 	 $result = $wpdb->get_results($query);
 //	 echo $wpdb->num_rows($result);
 //	print_r($idarray); exit;

 		if(count($idarray)>2){
		$ct = count($idarray);
		if(FALSE=== get_transient('thse_reachpdt')){
			$trans_data = array();
		}
		else{
		$trans_data = get_transient('thse_reachpdt');	
		}
		$prefix = $wpdb->prefix;
	
		for($i=0;$i< $ct; ){


		$trans_data[] = $idarray[$i];	
		$i++;		
		} 		
		set_transient('thse_reachpdt',$trans_data ); 
		
			
		
		$error .= 'Total '.count($trans_data).' Current '.  count($idarray)." RSS URLS - products are being fetched.  You will be Notified by Email or Automatic logs.<br />";
		
		//	mail('anupam.rekha@satyamtechnologies.net','Total '.count($trans_data).' Current '.  count($idarray),$error);
		return $error;			
		}
	  
	foreach($result as $row){
		$error .= $row->rss_url.'<br />';
		 $category = explode('/', $row->rss_url);
		 $category = $category[count($category)-1];
		 $category = str_replace('-slash', ', ', $category);
		 $category = str_replace('-', ' ', $category);
		 $category = str_replace('.atom', '',$category );
		 $category = esc_sql(ucwords($category));

		if($category!=''){  $category = esc_sql(ucwords($category));}
		else{  $category = ucwords($category);}
					 
		 $products = stripslashes($row->content);
		$products = XMLtoArray($products);
		$pdtarray = $products['FEED'];
		array_shift($pdtarray);
		array_shift($pdtarray);
		array_shift($pdtarray);
		array_shift($pdtarray);
		array_shift($pdtarray);
		array_shift($pdtarray);
		array_shift($pdtarray);
		$pdtarray = $pdtarray['ENTRY'];
		$newary = array();
		if(isset($pdtarray['ID'])){
			
			$newary[0]=$pdtarray;
			$pdtarray = $newary;			
		} //echo isset($pdtarray['ID']); exit;
	//	print_r($newary);exit;
		$i = 0;
		if(empty($pdtarray)){ return 'No Products found';}
		foreach($pdtarray as $item){
		//print_r($item); exit;
			  $idstring = $item['ID'];
			  $id = explode('/',$idstring );
			  $id = $id[1];
			  
			  $query_chk = "select * from ".$wpdb->prefix."envato where item_id = '{$id}'";
			  $result_chk = $wpdb->get_var($query_chk);
			  
			  if($result_chk==0){
			  
			  $type = explode(':', $idstring);
			  $type = $type['1'];
			  $type = explode('.', $type);
			  $type = $type['0'];
			  
			  $published = (isset($item['PUBLISHED'])&& $item['PUBLISHED']!='')?substr($item['PUBLISHED'], 0, 10):'';
			  	  
			  $itemurl = (isset($item['LINK']['HREF'])&& $item['LINK']['HREF']!='')?$item['LINK']['HREF']:'';
				
			  $itemtitle = (isset($item['TITLE'])&& $item['TITLE']!='')?esc_sql($item['TITLE']):'';
		      
			  $content = (isset($item['CONTENT']['content'])&& $item['CONTENT']['content']!='')?esc_sql($item['CONTENT']['content']):'';			  

			  $author = (isset($item['AUTHOR'][$i]['NAME'])&& $item['AUTHOR'][$i]['NAME']!='')?esc_sql($item['AUTHOR'][$i]['NAME']):'';	
		 
			  
			$ch1 = curl_init();  
			curl_setopt($ch1, CURLOPT_URL, 'http://marketplace.envato.com/api/edge/item-prices:'.$id.'.json');  
			curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch1, CURLOPT_ENCODING, "");
			curl_setopt($ch1, CURLOPT_AUTOREFERER, true);	
			curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, 120);	
			curl_setopt($ch1, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch1, CURLOPT_MAXREDIRS, 10);								
		 	curl_setopt($ch1, CURLOPT_USERAGENT, 'Sixthlife Search for Envato Affiliates'); 
			$ch_data1 = curl_exec($ch1); 
                        $curlerror = curl_error($ch1).' http://marketplace.envato.com/api/edge/item-prices:'.$id.'.json';
			curl_close($ch1); 
			$json_data1 = json_decode($ch_data1, true);
                        if(!isset($json_data1['item-prices']['0']['price'])||$json_data1['item-prices']['0']['price']=='')
                            {
                            $error .='<br />'. $curlerror."<br /> Envato API: Price could not be fetched. <br />";
                            $price = '';
                            }
                            else{
                             $price = $json_data1['item-prices']['0']['price'];
                             $price = esc_sql($price);   
                            }
		
			
			$ch2 = curl_init();  
			curl_setopt($ch2, CURLOPT_URL, 'http://marketplace.envato.com/api/edge/item:'.$id.'.json');  
			curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch2, CURLOPT_ENCODING, "");
			curl_setopt($ch2, CURLOPT_AUTOREFERER, true);	
			curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 120);	
			curl_setopt($ch2, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch2, CURLOPT_MAXREDIRS, 10); 
		 	curl_setopt($ch2, CURLOPT_USERAGENT, 'Sixthlife Search for Envato Affiliates');
			$ch_data2 = curl_exec($ch2); 
                        $curlerror = curl_error($ch2).' http://marketplace.envato.com/api/edge/item:'.$id.'.json';
			curl_close($ch2); 
			$json_data2 = json_decode($ch_data2, true); 
                        
                        //print_r($json_data2);
                            if(!isset($json_data2 ['item']['live_preview_url'])||$json_data2 ['item']['live_preview_url']=='')
                            {
                            $error .= '<br />'. $curlerror."<br /> Envato API: Preview could not be fetched. Product Demo URL may not work.<br/>";
                            $preview = '';
                            $demourl = 'full_screen_preview/'.$id;
                            $demourl = '';
                            $affdemo = '';
                            }
                            else{
                            $preview = $json_data2 ['item']['live_preview_url'];
                            $demourl = 'full_screen_preview/'.$id;
                            $demourl = str_replace($id, $demourl,$itemurl ); 
                            $affdemo = (get_option('envatouseraff')!='')? $demourl.'?ref='.get_option('envatouseraff'):$demourl;
                            }
	
			
			$afflink =(get_option('envatouseraff')!='')? $itemurl.'?ref='.get_option('envatouseraff'):$itemurl;
	
			
			$query_addproduct = "insert into ".$wpdb->prefix."envato(item_id, item_title, item_content, item_type,  	item_category, item_author, item_url, item_preview, item_demo, item_price, item_afflink,item_affdemo) values({$id},'{$itemtitle}','{$content}','{$type}' ,'{$category}','{$author}' ,'{$itemurl}','{$preview}','{$demourl}','{$price}','{$afflink}','{$affdemo}')";
			
			$result_addproduct = $wpdb->query($query_addproduct);
			$error .= $id.'-'.$itemtitle.' inserted successfully.<br />';
 			}
 			else{
 						$error .= $id.' already exists.<br />';
 			}
		$i++;
		}
	}
	          if(ini_get('max_execution_time')!==$oldmaxexectime){    
            ini_set('max_execution_time',$oldmaxexectime );
          }
       
          if(ini_get('max_input_time')!==$oldmaxinputtime){    
            ini_set('max_input_time',$oldmaxinputtime );
          }		  	
	return $error;
}


function updateAttachById($file, $product_id, $title){
		global $wpdb;
		$error = '';
		$id = '';
		$new_filename = clean_fname($title);
		
		$new_filename = unique_filename($new_filename);	
			

		//	print_r($file_array);
   		// Check for download errors
	    if ( is_wp_error( $file ) ) {
     		@unlink( $file[ 'tmp_name' ] );
       		//return $tmp;
       			$error .= "Preview Image:  error fetching image.<br />";
     		continue;
	    	}
			
   		$id = media_handle_sideload( $file, 0,$new_filename );// print_r($id);
			    // Check for handle sideload errors.
	    if ( is_wp_error( $id ) ) {
     		@unlink( $file['tmp_name'] );
       		//return $id;
       			$error .=  'Preview Image:  error uploading image.'. $id->errors['upload_error'][0].'<br />';
       		continue;
			    }
		
		rename_attacment($id, urlencode($new_filename));	
				

		$query_up = "update ".$wpdb->prefix."envato set attachment_id = '{$id}' where id = {$product_id}";
		$result_up = $wpdb->query($query_up);
		if($result_up){
		$error .=  "Preview Image: uploaded successfully.<br />";}	
		
		return array('id'=>$id, 'error'=>$error);
	}

function updateAttach($limitstart, $count){
	global $wpdb;
    require_once(ABSPATH . 'wp-load.php');
    require_once(ABSPATH . "wp-includes" .'/pluggable.php');
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    	$error = "";

	$query_pdt = "select id, item_id,item_title,item_preview from ".$wpdb->prefix."envato where  attachment_id = 0 and item_preview NOT LIKE '%swf' and item_preview != '' limit {$limitstart}, {$count}";
	$result_pdt = $wpdb->get_results($query_pdt);
	
	if($count>10 && $wpdb->num_rows>10){
		$ct = $wpdb->num_rows;
		if(FALSE=== get_transient('thse_qimg')){
			$trans_data = array();
		}
		else{
		$trans_data = get_transient('thse_qimg');	
		}
		$prefix = $wpdb->prefix;
	
		for($i=$limitstart;$i<= $ct; ){


		$trans_data[] = array($limitstart,10 );	
		$i=$i+10;		
		}
		set_transient('thse_qimg',$trans_data ); 
		
		//		mail('anupam.rekha@satyamtechnologies.net',count($trans_data). ' total',count($trans_data));
				
		$error .=  $wpdb->num_rows." images are being fetched.  You will be Notified by Email or Automatic logs.<br />";
		return $error;
	}
	
 		$oldmaxexectime = ini_get('max_execution_time');
        $oldmaxinputtime = ini_get('max_input_time');
	
	if($wpdb->num_rows==0){ $error .= "All Preview Images are uploaded.";}
		else{
       $sugg_maxexectime = 15*$wpdb->num_rows;
        $sugg_maxinputtime = 15*$wpdb->num_rows;     
   
     ($oldmaxexectime<$sugg_maxexectime)?  ini_set('max_execution_time',$sugg_maxexectime ):'';
   
     ($oldmaxinputtime<$sugg_maxinputtime)?  ini_set('max_input_time',$sugg_maxinputtime ):'';   
		}
     
     //	$error .= ' max_execution_time: '.ini_get('max_execution_time').' max_input_time: '.ini_get('max_input_time').'<br />';	    
           
	foreach($result_pdt as $row){
		$new_filename = clean_fname($row->item_title);
		
		$new_filename = unique_filename($new_filename);
		
		  $tmp = download_url( $row->item_preview );
	 
	     $file_array = array(
			        'name' => basename($row->item_preview),
			        'tmp_name' => $tmp
		    		);
	//print_r($row);
	//	print_r($file_array);
   		// Check for download errors
	    if ( is_wp_error( $tmp ) ) {
     		@unlink( $file_array[ 'tmp_name' ] );
     		$row->item_preview = str_replace('https', 'http', $row->item_preview );
     		$tmp = download_url( $row->item_preview );
 	    	$file_array = array(
			        'name' => basename($row->item_preview),
			        'tmp_name' => $tmp
		    		);
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array[ 'tmp_name' ] );
       		//return $tmp;
       			$error .= $row->id. "  Preview URL: ".$row->item_preview.' error fetching url.<br />';
       			/* START 18th Jan 2016 delete envato products if image cannot be fetched */
			$query_del = "delete from ".$wpdb->prefix."envato where  id = ".$row->id;
			$result_del = $wpdb->query($query_del);
			/* END 18th Jan 2016 delete envato products if image cannot be fetched */
     		continue;
     		}
	    	}
			
   		$id = media_handle_sideload( $file_array, 0,$new_filename ); //print_r($id);
			    // Check for handle sideload errors.
                   
	    if ( is_wp_error( $id ) ) {
     		@unlink( $file_array['tmp_name'] );
       		//return $id;
 				continue;
                }
		
		rename_attacment($id, urlencode($new_filename));	
				

		$query_up = "update ".$wpdb->prefix."envato set attachment_id = '{$id}' where id = {$row->id}";
		$result_up = $wpdb->query($query_up);
		if($result_up){
		$error .=  $row->id. "  Preview URL: ".$row->item_preview.' uploaded successfully.<br />';}
		sleep(rand(1,5));
	}

          if(ini_get('max_execution_time')!==$oldmaxexectime){    
            ini_set('max_execution_time',$oldmaxexectime );
          }
       
          if(ini_get('max_input_time')!==$oldmaxinputtime){    
            ini_set('max_input_time',$oldmaxinputtime );
          }	
	return $error;
}


function envatoThemepdtpart($mktplace, $contkwds, $notcontkwds, $catkwds, $titlecontcond, $contcatcond,  $limit, $items_array = array('serial'=>1,'pagecontent'=>''), $forajax=false){
global $wpdb;
	$fortitle = '';
	$forcontent = '';
	$forcategory = '';
	$notfortitle = '';
        $notforcontent = '';
	
	
	if(strpos($contkwds, '&')!== FALSE){
	$fortitle = andQuerypart($contkwds, 'item_title',$titlecontcond );
	$forcontent = andQuerypart($contkwds, 'item_content', $contcatcond);
	}
	else if(strpos($contkwds, '|')!== FALSE){
	$fortitle = orQuerypart($contkwds, 'item_title',$titlecontcond );
	$forcontent = orQuerypart($contkwds, 'item_content', $contcatcond);		
	}
	else if($contkwds!=''){
		$fortitle = 'item_title like '. '\'%'.$contkwds.'%\' '.$titlecontcond.' ' ;
		$forcontent = 'item_content like '. '\'%'.$contkwds.'%\' '.$contcatcond.' ' ;	
	}

	if(strpos($notcontkwds, '&')!== FALSE){
	$notfortitle = andnotlikeQuerypart($notcontkwds, 'item_title',$titlecontcond );
	$notforcontent = andnotlikeQuerypart($notcontkwds, 'item_content', $contcatcond);
	}
	else if($notcontkwds!=''){
		$notfortitle = 'item_title not like '. '\'%'.$notcontkwds.'%\' and ' ;
		$notforcontent = 'item_content not like '. '\'%'.$notcontkwds.'%\'  ' ;	
	}
	if(strpos($catkwds, '&')!== FALSE){
	$forcategory = andQuerypart($catkwds, 'item_category');
	}
	else if(strpos($catkwds, '|')!== FALSE){
	$forcategory = orQuerypart($catkwds, 'item_category');
	}
	else if($catkwds!=''){
	$forcategory = 'item_category like '. '\'%'.$catkwds.'%\' ' ;
	}
	
	

		$q_part = $fortitle.$forcontent;	
	
	$q_part = rtrim($q_part, 'and ');
	$q_part = rtrim($q_part, 'or ');

	if($q_part!= ''){
		$q_part = '('.$q_part. ') ';
	}
	if($forcategory!='' && $fortitle!=''){
		$q_part .= ' and '. $forcategory;
	}
	else{
	$q_part .=  $forcategory;	
	}
	
	$q_part = rtrim($q_part, 'and ');
	$q_part = rtrim($q_part, 'or ');	
	
	if($notfortitle!=''|| $notforcontent!=''){
		$notpart = $notfortitle.$notforcontent;
		$notpart = rtrim($notpart, 'and ');
		$q_part .= ' and ('.$notpart.') and ';
	}
	else{
	$q_part .= ' and ';	
	}
	
	$q_part = ltrim($q_part, 'and ');
	$q_part = ltrim($q_part, 'or ');
				
	$query_pdts = "select * from ".$wpdb->prefix."envato where {$q_part} (attachment_id != 0 and item_type = '{$mktplace}') order by id desc limit {$limit}";
	
	//$query_pdts = str_replace('where and','where', $query_pdts);

	//echo $query_pdts;		
	$result_pdts = $wpdb->get_results($query_pdts);

	$serial = $items_array['serial'];
	$pagecontent = $items_array['pagecontent'];
	foreach($result_pdts as $row){
	
	if(isset($_SESSION['envato_remarray'])&& $forajax==false && in_array($row->id, $_SESSION['envato_remarray'])){
		continue;
	}
	 $item_title = $row->item_title;
	 $item_content = ($row->itemc_content=="")?getErssdesc($row->item_content, get_option('endesccontains')):$row->itemc_content;
	 $item_price = $row->item_price;
	 $item_category = $row->item_category;

	 $afflink = $row->item_afflink;
	 $affdemolink = $row->item_affdemo;

	 //$forbaseurl = str_replace('admin.php','', currentpageurl());
	 
	 $forbaseurl = explode('?', currentpageurl());
	 $baseurl = $forbaseurl[0]; 
	 
	 $attachment_url = wp_get_attachment_url( $row->attachment_id );	 
	 
		if($forajax==true){
			$pagecontent .=	"<div id=\"itemenvato-{$row->id}\">";
		}		 

	
		$contplaceholder = get_option('envatothemeitemcode');
		
		if($contplaceholder!=''){
			$contplaceholder = str_replace('{themecount}', $serial, $contplaceholder);
			$contplaceholder = str_replace('{themetitle}', $item_title, $contplaceholder);
			$contplaceholder = str_replace('{themeprice}', $item_price, $contplaceholder);
			$contplaceholder = str_replace('{themecategory}', $item_category, $contplaceholder);
			$contplaceholder = str_replace('{thememoreinfourl}', $afflink, $contplaceholder);
			$contplaceholder = str_replace('{previewimageurl}', $attachment_url, $contplaceholder);
			$contplaceholder = str_replace('{themedescription}', $item_content, $contplaceholder);
			$contplaceholder = str_replace('{themedemourl}', $affdemolink, $contplaceholder);
			$pagecontent .= $contplaceholder;
		}
		else{
		$pagecontent .= "<h2>{$serial}. {$item_title}  {$item_price} </h2>";
		//	$pagecontent .= "<h4>{$item_category}</h4>";

		$pagecontent .= "<a title=\"{$item_title}\" href=\"{$afflink}\"><img class=\"alignleft\" title=\"{$item_title}\" alt=\"{$item_title}\"  src=\"{$attachment_url}\" width=\"620\" /></a><div style=\"clear:both;\"></div>";
	
		$pagecontent .=	"{$item_content}<p>&nbsp;</p>";
		
		$pagecontent .=	"<a class=\"mini-butt red-buy\" title=\"{$item_title} View Demo\" href=\"{$affdemolink}\" rel=\"nofollow\">View Demo</a>";
   				
		$pagecontent .=	" <a class=\"mini-butt red-buy\" title=\"{$item_title} More Info\" href=\"{$afflink}\" rel=\"nofollow\">More Info</a>";			
		}
		if($forajax==true){
			$pagecontent .=	"&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"button\" value=\"remove\" class=\"button button-primary\" onclick = \"javascript:removethistheme('{$row->id}', 'envato', '{$baseurl}');return false;\" /></div>";
		}
			
	$serial++;
	}
	

	$items_array['serial'] = $serial;
	
	//print_r($_SESSION['envato_remarray']);

	$items_array['pagecontent'] = $pagecontent;
	if(isset($_SESSION['envato_remarray'])){ $_SESSION['envato_remarray'] = array(); }
	return  $items_array;
}


function final_create_post($pagetitle, $pagecontent, $pagetags='', $pagecats=array('1'), $pagestatus, $deleteifexists = false){
			global $wp;
			global $wpdb;

			$my_post = array();
			//print_r($pagecats) ;			
			if($pagecontent!="" && $pagetitle!=""){
			$error = '';	
			// Create post object 
		  $my_post = array(	  
		     'post_title' => $pagetitle,
		     'tags_input' => $pagetags,
		     'post_status' => $pagestatus,
		     'post_author' => get_current_user_id(),
		     'post_category' => $pagecats,
   		     'post_content' => $pagecontent
		  );
	//	  echo 'anu'.$deleteifexists.'anu';
		  
		//  print_r($my_post);
			   if(spost_exists($pagetitle) && $deleteifexists==FALSE){
			   		$error = ' Post '.$pagetitle. ' already exists';
				   return $error;
			   }
			   else if(spost_exists($pagetitle) && $deleteifexists==TRUE){
			  		$pagetitle = esc_sql($pagetitle);
				   $_chktitle = "select ID from ".$wpdb->prefix."posts where post_title = '{$pagetitle}' limit 1";
				   $_qtitle = $wpdb->query($_chktitle);
			
					if($wpdb->num_rows($_qtitle)==1){
						$current_postid = $_qtitle->ID ;
						if ( current_user_can('delete_post', $current_postid) ) {
						wp_delete_post(mysql_result($_qtitle,0,'ID'), false);	
						$error .= 'Old Post "'.$pagetitle. '" was deleted.<br />';
						}
						else{
						$error .= 'Cannot delete Post\'s created by others.';
						return $error;
						}

					}
			   }

			   	$post_id =  wp_insert_post( $my_post ); 
		
			   
			   
	if(isset($_SESSION['envato_remarray'])){ $_SESSION['envato_remarray'] = array(); }			   
			   
			   return $error.'Post "'.$pagetitle.'" was created successfully.';	
		}
	else{
		return "A Post with empty content cannot be created.";
	}
}


function andQuerypart($kwdcondition, $kwdfield_name,  $condition=''){
	$keywords = explode('&',$kwdcondition );
	//print_R($keywords);
	$querypart = '';
	if(count($keywords)!=0){
		$querypart .= '(';

	foreach($keywords as $keyword){
		$querypart .= $kwdfield_name.' like '. '\'%'.$keyword.'%\'  and ';
	}
	$querypart = rtrim($querypart, 'and ');

		$querypart .= ')';
		if($condition!=''){
		$querypart .= ' '.$condition.' ';}
	}
	return $querypart;
}

function andnotlikeQuerypart($kwdcondition, $kwdfield_name,  $condition=''){
	$keywords = explode('&',$kwdcondition );
	//print_R($keywords);
	$querypart = '';
	if(count($keywords)!=0){
		$querypart .= '(';

	foreach($keywords as $keyword){
		$querypart .= $kwdfield_name.' not like '. '\'%'.$keyword.'%\'  and ';
	}
	$querypart = rtrim($querypart, 'and ');

		$querypart .= ')';
		if($condition!=''){
		$querypart .= ' and ';}
	}
	return $querypart;
}

function orQuerypart($kwdcondition, $kwdfield_name,  $condition=''){
	$keywords = explode('|',$kwdcondition );
	$querypart = '';
	if(count($keywords)!=0){
		$querypart .= '(';

	foreach($keywords as $keyword){
		$querypart .= $kwdfield_name.' like '. '\'%'.$keyword.'%\'  or ';
	}
	$querypart = rtrim($querypart, 'or ');

		$querypart .= ')';
		if($condition!=''){
		$querypart .= ' '.$condition.' ';}
	}
	return $querypart;
}

function getErssdesc($content, $contains='DescriptionFeaturesList'){
	$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

	if(get_option('endesincimages')!=1){
	$content = preg_replace("/<img[^>]+\>/i", "", $content);		
	}
	
	$desc = '';	
	if($contains=='DescriptionOnly'){
		
	if(strpos($content, '<ul>')!==false){
		$desc = explode('<ul>', $content); 
		$desc =$desc[0]; 
	}
	else if(strpos($content, '<h2>')!==false){
		$desc = explode('<h2>', $content);
		$desc =$desc[0];
	}
	else if(strpos($content, '</p>')!==false){
		$desc = explode('</p>', $content);
		$desc =$desc[0];
	}
	else{
		$desc = $content;
	}

			
	}
	else if($contains=='FeaturesListOnly'){
	if(strpos($content, '<ul>')!==false){
		$desc = explode('<ul>', $content);
		$desc ='<ul>'.$desc[1];
	}
	
	if($desc!=''&& strpos($content, 'Feature')!==false){
			$desc = explode('Feature',$desc );
			$desc = $desc[0];
	}
	if($desc!=''&& strpos($content, 'FEATURE')!==false){
			$desc = explode('FEATURE',$desc );
			$desc = $desc[0];
	}
	
	}
	
	else if($contains=='DescriptionFeaturesList'){
		
	if($desc!=''&& strpos($content, 'Installation')!==false){
			$desc = explode('installation',$desc );
			$desc = $desc[0];
	}
	if($desc!=''&& strpos($content, 'INSTALLATION')!==false){
			$desc = explode('INSTALLATION',$desc );
			$desc = $desc[0];
	}
	
	if($desc!=''&& strpos($content, 'Credits')!==false){
			$desc = explode('Credits',$desc );
			$desc = $desc[0];
	}
	if($desc!=''&& strpos($content, 'CREDITS')!==false){
			$desc = explode('CREDITS',$desc );
			$desc = $desc[0];
	}
	else{
			$desc = $content;
	}		
	}
	else{
	$desc = $content;	
	}
	
	
	if(get_option('endesincdemos')!=1){	
	if($desc!=''){
			$desc = explode('Demo',$desc );
			$desc = $desc[0];
	}
	if($desc!=''){
			$desc = explode('DEMO',$desc );
			$desc = $desc[0];
	}	
	}
	if(get_option('endesincstrip')!=1){	
	$desc = strip_tags($desc);
	}
	

	return $desc;

}
	

	

function to_camel_case($str, $capitalise_first_char = false) {
    if($capitalise_first_char) {
      $str[0] = strtoupper($str[0]);
    }
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return preg_replace_callback('/_([a-z])/', $func, $str);
  }
  
  
  function get_dbenvatothemecount(){
  	global $wpdb;
  		$query_pdts = "select COUNT(*) from ".$wpdb->prefix."envato";
  		$result_pdts = $wpdb->get_var($query_pdts);
  		
  		return  $result_pdts;
  }
  
    function get_dbenvatolessprevcount(){
    	global $wpdb;
  		$query_pdts = "select COUNT(*) from ".$wpdb->prefix."envato where attachment_id='0'";
  		$result_pdts =$wpdb->get_var($query_pdts);
  		return $result_pdts;
  }
  
  
function set_html_content_type() {

	return 'text/html';
}

function currentpageurl(){                               //Function to return current page url                        
return (!empty($_SERVER['HTTPS']) ? 'https://': 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

function thse_deleteLogs($daysold){
	global $wpdb;
	$q = "DELETE FROM ".$wpdb->prefix."envatologs WHERE timestamp < NOW() - INTERVAL ".$daysold." DAY";
	$r = $wpdb->query($q);
	return $r;
}


function thse_createdbtables(){
	global $wpdb;
	$tblprefix = $wpdb->prefix;
	
	$sql1 = "CREATE TABLE IF NOT EXISTS `{$tblprefix}envato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `attachment_id` int(20) NOT NULL,
  `item_title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `item_content` text COLLATE utf8_unicode_ci NOT NULL,
  `item_type` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `item_category` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `item_tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_author` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `item_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_preview` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_demo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_price` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `item_afflink` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `item_affdemo` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `itemc_content` text COLLATE utf8_unicode_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_id` (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";

$wpdb->query($sql1);

$sql2 = "CREATE TABLE IF NOT EXISTS `{$tblprefix}envatologs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";

$wpdb->query($sql2);

$sql3 = "CREATE TABLE IF NOT EXISTS `{$tblprefix}envatorssurls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rss_url` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rss_url` (`rss_url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";

$wpdb->query($sql3);
}

function thse_updatetables_version1_1(){
	global $wpdb;
	$tblprefix = $wpdb->prefix;
	
	$sql1 = "ALTER TABLE `{$tblprefix}envatorssurls` MODIFY COLUMN content longtext";
	$wpdb->query($sql1);
}

function get_web_page( $url )
{
	if(function_exists('curl_version')){
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "Sixthlife Search for Envato Affiliates", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );
	return $content;
	}
	else if(function_exists('file_get_contents') && ini_get('allow_url_fopen')==TRUE){
	$content = @file_get_contents($url);	
	return $content;	
	}
  }


?>