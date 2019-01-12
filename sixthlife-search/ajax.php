<?php include('../../../wp-load.php');

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'simple_html_dom.php');

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php');

$html = new simple_html_dom(); 

//print_r($_GET);


global $wpdb;

if(isset($_GET['load_selector'])&& $_GET['load_selector']==true && !empty($_GET['rawdata_id'])){

	

	$query = "select url,baseurl, affstring, defaultcat,ethemeelem,  	ethemeurl, ethemedemo, ethemetitle, ethemeprice, ethemepreview, ethemedesc, ethemecat, ithemedemo, ithemetitle, ithemeprice, ithemepreview, ithemedesc, ithemecat from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

			$data = $result;

        

        	 $data = json_encode($data);

        	 echo $data; 

}


if(isset($_GET['load_emailselector'])&& $_GET['load_emailselector']==true && !empty($_GET['rawdata_id'])){

	

	$query = "select * from ".$wpdb->prefix."emailpages where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

			$data = $result;

        

        	 $data = json_encode($data);

        	 echo $data; 

}


if(isset($_GET['chkinner_demo']) &&  $_GET['chkinner_demo']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!='' && isset($_GET['chk_themeurl'])&& $_GET['chk_themeurl']!=''){

	$thidentifier = $_GET['chk_theme'];

	$thurlidentifier = 	$_GET['chk_themeurl'];

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chkinner_demo'];

	$query = "select url, baseurl, rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$start_url = $result->url;

	$baseurl =  $result->baseurl;

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

	

    $themeurl = $article->find($thurlidentifier, 0)->href;

if($baseurl!='' && strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false) {

  	$themeurl = $baseurl.$themeurl;	

}      

else if(strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false){

		$parse = parse_url($start_url);

    	$themeurl = $parse['scheme'].'://'.$parse['host'].'/'.$themeurl;

    }

    	$innhtml = file_get_html($themeurl);

    	$itemtext .= $innhtml->find($identifier, 0)->href;

    

}

echo $itemtext;

}

if(isset($_GET['chkinner_title']) &&  $_GET['chkinner_title']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!='' && isset($_GET['chk_themeurl'])&& $_GET['chk_themeurl']!=''){

	$thidentifier = $_GET['chk_theme'];

	$thurlidentifier = 	$_GET['chk_themeurl'];

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chkinner_title'];



	$query = "select rawdata,baseurl, url from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$start_url = $result->url;

	$baseurl =  $result->baseurl;

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $themeurl = $article->find($thurlidentifier, 0)->href;

   if($baseurl!='' && strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false) {

  	$themeurl = $baseurl.$themeurl;	

}      

else if(strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false){

		$parse = parse_url($start_url);

    	$themeurl = $parse['scheme'].'://'.$parse['host'].'/'.$themeurl;

    }  

    	$innhtml = file_get_html($themeurl);

    	$itemtext .= $innhtml->find($identifier, 0)->plaintext.'<br />';

    

}

echo $itemtext;

}



if(isset($_GET['chkinner_price']) &&  $_GET['chkinner_price']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!='' && isset($_GET['chk_themeurl'])&& $_GET['chk_themeurl']!=''){

	$thidentifier = $_GET['chk_theme'];

	$thurlidentifier = 	$_GET['chk_themeurl'];

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chkinner_price'];



	$query = "select url,baseurl, rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$start_url = $result->url;

	$baseurl =  $result->baseurl;

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $themeurl = $article->find($thurlidentifier, 0)->href;

if($baseurl!='' && strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false) {

  	$themeurl = $baseurl.$themeurl;	

}      

else    

  if(strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false){

		$parse = parse_url($start_url);

    	$themeurl = $parse['scheme'].'://'.$parse['host'].'/'.$themeurl;

    }

    	$innhtml = file_get_html($themeurl);

    	$itemtext .= $innhtml->find($identifier, 0)->plaintext;

    

}

echo $itemtext;

}



if(isset($_GET['chkinner_preview']) &&  $_GET['chkinner_preview']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!='' && isset($_GET['chk_themeurl'])&& $_GET['chk_themeurl']!=''){

	$thidentifier = $_GET['chk_theme'];

	$thurlidentifier = 	$_GET['chk_themeurl'];

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chkinner_preview'];

	$query = "select url,baseurl, rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$start_url = $result->url;

	$baseurl =  $result->baseurl;

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $themeurl = $article->find($thurlidentifier, 0)->href;

if($baseurl!='' && strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false) {

  	$themeurl = $baseurl.$themeurl;	

}      

else if(strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false){

		$parse = parse_url($start_url);

    	$themeurl = $parse['scheme'].'://'.$parse['host'].'/'.$themeurl;

    }

	    

    	$innhtml = file_get_html($themeurl);

    	$itemtext .= $innhtml->find($identifier, 0)->src;

    

}

echo $itemtext;

}



if(isset($_GET['chkinner_description']) &&  $_GET['chkinner_description']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!='' && isset($_GET['chk_themeurl'])&& $_GET['chk_themeurl']!=''){

	$thidentifier = $_GET['chk_theme'];

	$thurlidentifier = 	$_GET['chk_themeurl'];

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chkinner_description'];

	$query = "select url,baseurl, rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$start_url = $result->url;

	$baseurl =  $result->baseurl;

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $themeurl = $article->find($thurlidentifier, 0)->href;

 if($baseurl!='' && strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false) {

  	$themeurl = $baseurl.$themeurl;	

}      

else  if(strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false){

		$parse = parse_url($start_url);

    	$themeurl = $parse['scheme'].'://'.$parse['host'].'/'.$themeurl;

    }

	 

    	$innhtml = file_get_html($themeurl);

    	$itemtext .= $innhtml->find($identifier, 0)->plaintext;

    

}

echo $itemtext;

}



if(isset($_GET['chkinner_category']) &&  $_GET['chkinner_category']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!='' && isset($_GET['chk_themeurl'])&& $_GET['chk_themeurl']!=''){

	$thidentifier = $_GET['chk_theme'];

	$thurlidentifier = 	$_GET['chk_themeurl'];

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chkinner_category'];

	$query = "select url,baseurl, rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$start_url = $result->url;

	$baseurl =  $result->baseurl;

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $themeurl = $article->find($thurlidentifier, 0)->href;

if($baseurl!='' && strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false) {

  	$themeurl = $baseurl.$themeurl;	

}      

else  if(strpos($themeurl,'http')===false && strpos($themeurl,'www.')===false){

		$parse = parse_url($start_url);

    	$themeurl = $parse['scheme'].'://'.$parse['host'].'/'.$themeurl;

    }

	  

    	$innhtml = file_get_html($themeurl);

    	$itemtext .= $innhtml->find($identifier, 0)->plaintext;

    

}

echo $itemtext;

}



else if(isset($_GET['chk_themeurl']) &&  $_GET['chk_themeurl']!=''&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!='' && isset($_GET['rawdata_id'])){

	

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chk_themeurl'];

	$thidentifier = $_GET['chk_theme'];

	$query = "select url,baseurl,rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$start_url = $result->url;

	$baseurl =  $result->baseurl;

	$rawdata = stripslashes($rawdata);

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $itemtext    .= $article->find($identifier, 0)->href;

  

} 

echo $itemtext;

}



if(isset($_GET['chk_themedemo']) &&  $_GET['chk_themedemo']!=''&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!='' && isset($_GET['rawdata_id'])){

	

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chk_themedemo'];

	$thidentifier = $_GET['chk_theme'];

	$query = "select rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $itemtext    .= $article->find($identifier, 0)->href;

  

} 

echo $itemtext;

}



if(isset($_GET['chk_themepreview']) &&  $_GET['chk_themepreview']!=''&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!='' && isset($_GET['rawdata_id'])){

	

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chk_themepreview'];

	$thidentifier = $_GET['chk_theme'];

	$query = "select rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $itemtext    .= $article->find($identifier, 0)->src;

  

} 

echo $itemtext;

}



else if(isset($_GET['chk_themetitle']) &&  $_GET['chk_themetitle']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!=''){

	$thidentifier = $_GET['chk_theme'];	

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chk_themetitle'];

	$query = "select rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $itemtext .= $article->find($identifier, 0)->plaintext;



   

}

echo $itemtext;

}



else if(isset($_GET['chk_themedescription']) &&  $_GET['chk_themedescription']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!=''){

	$thidentifier = $_GET['chk_theme'];	

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chk_themedescription'];

	$query = "select rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $itemtext .= $article->find($identifier, 0)->plaintext;



   

}

echo $itemtext;

}



else if(isset($_GET['chk_themecategory']) &&  $_GET['chk_themecategory']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!=''){

	$thidentifier = $_GET['chk_theme'];	

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chk_themecategory'];

	$query = "select rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $itemtext .= $article->find($identifier, 0)->plaintext;



   

}

echo $itemtext;

}



else if(isset($_GET['chk_themeprice']) &&  $_GET['chk_themeprice']!='' && isset($_GET['rawdata_id'])&& isset($_GET['chk_theme'])&& $_GET['chk_theme']!=''){

	$thidentifier = $_GET['chk_theme'];	

	$rawdata_id = $_GET['rawdata_id'];

	$identifier = $_GET['chk_themeprice'];

	$query = "select rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	$rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$html->load($rawdata);

	foreach($html->find($thidentifier) as $article) {

    $itemtext .= $article->find($identifier, 0)->plaintext;

}

echo $itemtext;

}







else if(isset($_GET['chk_theme']) &&  $_GET['chk_theme']!='' && isset($_GET['rawdata_id'])&& isset($_GET['themeonly'])){



	$rawdata_id = $_GET['rawdata_id'];



	 $identifier = $_GET['chk_theme'];
	$query = "select rawdata from ".$wpdb->prefix."themepageurls where id = {$_GET['rawdata_id']} limit 1";

	$result = $wpdb->get_results($query);

	 $rawdata = $result->rawdata;

	$rawdata = stripslashes($rawdata);

	$html->load($rawdata);
	


foreach($html->find($identifier) as $article) {

  //  $item['price']     = $article->find('.from', 0)->plaintext;

 //   $item['theme_url']    = $article->find('.preview', 0)->href;

 //   $item['theme_title'] = $article->find($identifier. ' strong', 0);

  //     $item['demo'] = $article->find('a.demo', 0)->href;

    $themetext.=  $article;

}



echo $themetext;

}


if(isset($_GET['envato_part'])){
//	print_r($_GET);
		if(strpos($_GET['envatotitle'], '-and-')!==false){
		$envato_title = str_replace('-and-', '&', $_GET['envatotitle']);			
		}
		else if(strpos($_GET['envatotitle'], '-or-')!==false){
		$envato_title = str_replace('-or-', '|', $_GET['envatotitle']);			
		}
		else{
		$envato_title = $_GET['envatotitle'];	
		}
		if(strpos($_GET['notenvatotitle'], '-and-')!==false){
		$notenvato_title = str_replace('-and-', '&', $_GET['notenvatotitle']);			
		}
		else{
		$notenvato_title = $_GET['notenvatotitle'];	
		} 
		if(strpos($_GET['envatocat'], '-and-')!==false){
		$envato_cat = str_replace('-and-', '&', $_GET['envatocat']);
		}
		else if(strpos($_GET['envatocat'], '-or-')!==false){
		$envato_cat = str_replace('-or-', '|', $_GET['envatocat']);
		}
		else{
		$envato_cat = $_GET['envatocat'];			
		}
		$items_array = 	envatoThemepdtpart($_GET['envatomktplc'], $envato_title,$notenvato_title, $envato_cat  ,$_GET['envtitlecont'],  $_GET['envcontcat'], $_GET['envatopdtmax'],array('serial'=>1,'pagecontent'=>''), true);
		
	if(!empty($items_array['pagecontent'])){
		echo $items_array['pagecontent'];
	}
	else{
		echo '<h3> No Products Found</h3>';
	}
	
}



if(isset($_GET['removethis'])){
	
	$removeid = $_GET['itemid'];
	
	if($_GET['marketpl']=='envato'){
	$_SESSION['envato_remarray'][]=$removeid ;		
	}

//	print_r($_SESSION['envato_remarray']);

//echo $removeid;
}

?>