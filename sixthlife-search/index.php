<?php



/*

Plugin Name:  Sixthlife Search

Plugin URI: http://sixthlife.net

Description: Sixthlife Search was originally created to aggregate information and write long Articles about Website Templates & WordPress Themes. It currrently works with Envato group Products.
Author: Sixthlife

Version: 1.0

Author URI: http://sixthlife.net

*/
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php');

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'simple_html_dom.php');
global $wpdb;

 
function thse_scripts_general() {

    wp_enqueue_script('sixthlife-search',plugins_url('/js/general.js', __FILE__), array('jquery'));
    
    wp_enqueue_style('sixthlife-search',plugins_url('/css/style.css', __FILE__));
}

add_action('admin_init', 'thse_scripts_general'); 


add_action('admin_menu', 'thse_plstarter_menu');


function thse_plstarter_menu() {

	//create new top-level menu

	add_menu_page('Sixthlife Search', 'Sixthlife Search', 'administrator', 'thse_plstarter_page', 'thse_plstarter_page',plugins_url('sixthlife-search/images/icon.png'));
        

}




function thse_plstarter_page(){

    
global $wp, $wpdb;


if((isset($_POST['submit_editenvatopdt']) && is_numeric($_POST['editenvatopdt_id']))|| isset($_POST['submit_envatopdtsave'])){

	$edit_pdtid = $_POST['editenvatopdt_id'];
	
	$query_feditpdt = "select * from ".$wpdb->prefix."envato where id = '{$_POST['editenvatopdt_id']}'";
	
	$result_feditpdt = $wpdb->get_row($query_feditpdt);
	
if(isset($_POST['submit_envatopdtsave'])){
//	print_r($_POST);
	$editerror = '';
	
	$editedtitle = esc_sql($_POST['editpdt_title']);
	$editeddesc = esc_sql($_POST['editpdt_desc']);
	$editedtags = esc_sql($_POST['editpdt_tags']);
	$editedprice = esc_sql($_POST['editpdt_price']);
	$editeddemo = esc_sql($_POST['editpdt_itemdemo']);

	$editedpdtid = esc_sql($_POST['editenvatopdt_id']);
	$editedattachid = esc_sql($_POST['editenvatoattach_id']);
	$editedattachrem = isset($_POST['editpdt_removeattach'])?$_POST['editpdt_removeattach']:0;
	
	if($editedtitle==''){
		$editerror .= "Product Title Cannot be Blank<br />";
	}
	else if($editedprice==''){
		$editerror .= "Product Price Cannot be Blank<br />";		
	}
	else{
 			
		$affdemo = (get_option('envatouseraff')!='')?$editeddemo.'?ref='.get_option('envatouseraff'):$editeddemo;
		$afflink = (get_option('envatouseraff')!='')?$result_feditpdt->item_url.'?ref='.get_option('envatouseraff'):$result_feditpdt->item_url;
		
		$query_edupdatepdt = "update ".$wpdb->prefix."envato set item_title = '{$editedtitle}',itemc_content = '{$editeddesc}', item_demo = '{$editeddemo}', item_price='{$editedprice}', item_tags = '{$editedtags}',item_afflink = '{$afflink}',  item_affdemo = '{$affdemo}'  where id = '{$_POST['editenvatopdt_id']}'";
		
		$result_edupdatepdt = $wpdb->query($query_edupdatepdt);
		
		if($result_edupdatepdt){
					$editerror .= "Product Updated Successfully.<br />";
		}
	}
	
	if(isset($_FILES['editpdt_uploadimg']['name']) && $_FILES['editpdt_uploadimg']['name']!=''){
		
			if($editedattachrem==1){
				wp_delete_attachment($editedattachid);
				
				$editerror .= 'Old Attchment delete successfuly.<br />';
			}

			$editedfile = $_FILES['editpdt_uploadimg'];
			$editedtitle = ($_POST['editpdt_title']!='')?$_POST['editpdt_title']:$_FILES['editpdt_uploadimg']['name'];
			$uploadedfile = updateAttachById($editedfile, $_POST['editenvatopdt_id'], $editedtitle);
			$editerror .= $uploadedfile['error'];
			
			$attach_id = $uploadedfile['id'];
	

}
}
	$query_feditpdt = "select * from ".$wpdb->prefix."envato where id = '{$_POST['editenvatopdt_id']}'"; 
	
	$result_feditpdt = $wpdb->get_row($query_feditpdt);

//	print_r($result_feditpdt); exit;

	$pdteditcontent = ($result_feditpdt->itemc_content!='')? stripslashes($result_feditpdt->itemc_content):getErssdesc(stripslashes($result_feditpdt->item_content));

	 ?>
<p class="description">	


	<a href="<?php echo currentpageurl();
	
	if(isset($_POST['pdtsearch']) && $_POST['pdtsearch']!=""){
		if(strpos(currentpageurl(), '?')=== FALSE){
		echo '?searchterm='.urlencode($_POST['pdtsearch']);	
		}
		else{
			echo '&searchterm='.urlencode($_POST['pdtsearch']);
		}

	
	}
	
	 ?>" style="font-weight:bold;font-size: 18px;">Back to Results</a></p>
<h3>Edit Product </h3>
<?php 
if( !ini_get('allow_url_fopen') ) {
   	echo '<div id="message" class="error" >This Plugin will not work Properly unless allow_url_fopen is On in php.in. allow_url_fopen is Off.</div>';
} 


?>
<?php 
if(!function_exists('curl_version')){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless Curl is Enabled. Curl is Disabled.</div>';
} ?>

<?php 
if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON==TRUE){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless WP-Cron is enabled. WP-Cron is Disabled.</div>';
} ?>

<?php if(isset($editerror)&& $editerror!='') {echo '<div id="message" class="updated" >'.$editerror.'</div>';} ?>
	






<form method="post" action="" enctype="multipart/form-data" >

<table class="form-table">

<tbody> 
<tr valign="top">

        <th  scope="row">

  <label for=""> Product Id</label> </th><td>
  <?php  echo stripslashes($result_feditpdt->item_id).' ';	 ?>
  </td>
  
  </tr>
  
<tr valign="top">

        <th  scope="row">

  <label for=""> Marketplace</label> </th><td>
<?php  echo stripslashes($result_feditpdt->item_type).' ';	 ?>
  </td>
  
  </tr>
  
  <tr valign="top">

        <th  scope="row">

  <label for=""> Attachment Id</label> </th><td>
<?php  echo stripslashes($result_feditpdt->attachment_id);	 ?> 
  </td>
  
  </tr>

  	 <tr valign="top">

        <th  scope="row">

  <label for=""> Product Title</label> </th><td>

        	<input type="hidden"  name="editenvatopdt_id"  id="editenvatopdt_id" value="<?php echo $edit_pdtid; ?>" />
        	
        
        
        	
        	
     	<input type="hidden"  name="editenvatoattach_id"  id="editenvatoattach_id" value="<?php echo $result_feditpdt->attachment_id;?>" />
     	
     	
		<input type="text"  name="editpdt_title" class="regular-text" id="editpdt_title" value="<?php if(isset($_POST['editpdt_title']) && !empty($_POST['editpdt_title'])){ 	echo $_POST['editpdt_title'];
				} else{
	echo stripslashes($result_feditpdt->item_title);				
				} ?>" />
		 

	

 		<p class="description" id=""></p> 	   

        <p class="description">Title of product.</p>

               </td></tr> 
               
       <tr>    <th  scope="row">

  <label for=""> Product Image</label> </th><td>
<?php

if($result_feditpdt->attachment_id!=0){
 $attachment_url = wp_get_attachment_url( $result_feditpdt->attachment_id); 
 
 
          echo "<p align=\"center\" style=\"float: none;\"><a title=\"".stripslashes($result_feditpdt->item_title)."\" href=\"".stripslashes($result_feditpdt->item_url)."\"><img class=\"alignleft\" title=\"".stripslashes($result_feditpdt->item_title)."\" alt=\"".stripslashes($result_feditpdt->item_title)."\"  src=\"{$attachment_url}\" width=\"620\" /></a><div style=\"clear:both;\"></p>";
          }
          
          else{
          	echo "<strong>Image Attachment Not Uploaded.</strong>";
          }
          ?>

        <p class="description">Image of product.</p>

               </td></tr> 
			   
   	 <tr valign="top">

        <th  scope="row">

  <label for=""> Upload Image</label> </th><td>

        

		<input type="file"  name="editpdt_uploadimg"  id="upload" value="" />


 
        <p class="description"></p>
        
        	<input type="checkbox"  name="editpdt_removeattach"  id="editpdt_removeattach" value="1" /> <label>Remove Old Attachment from media files.</label>


               </td></tr>			   
			               
   	 <tr valign="top">

        <th  scope="row">

  <label for=""> Product Description</label> </th><td>

        

		<textarea  name="editpdt_desc"  cols="100" rows="5" id="editpdt_desc"><?php 
		 
                if(isset($_POST['editpdt_desc']) && !empty($_POST['editpdt_desc'])){ 
				echo stripslashes($_POST['editpdt_desc']);
				}
				else{
				echo stripslashes($pdteditcontent);	
				}
		?></textarea>


        <p class="description">Description of product.</p>

               </td></tr>
			   
  	 <tr valign="top">

        <th  scope="row">

  <label for=""> Product Demo</label> </th><td>

  

		<input type="text"  name="editpdt_itemdemo" class="regular-text" id="editpdt_itemdemo" value="<?php if(isset($_POST['editpdt_itemdemo']) && !empty($_POST['editpdt_itemdemo'])){ 	echo $_POST['editpdt_itemdemo'];
				} else{
	echo stripslashes($result_feditpdt->item_demo);				
				} ?>" />
		 

	

 		<p class="description" id=""></p> 	   

        <p class="description">Title of product.</p>

               </td></tr> 
               
               
  	 <tr valign="top">

        <th  scope="row">

  <label for=""> Product Price</label> </th><td>



		<input type="text" class="regular-text" name="editpdt_price"  id="editpdt_price" value="<?php if(isset($_POST['editpdt_price']) && !empty($_POST['editpdt_price'])){ 	echo $_POST['editpdt_price'];
				} else{
	echo stripslashes($result_feditpdt->item_price);				
				} ?>" />
		 

	

 		<p class="description" id=""></p> 	   

        <p class="description">Price of product.</p>

               </td></tr> 
    	 <tr valign="top">

        <th  scope="row">

  <label for=""> Product Tags</label> </th><td>



		<input type="text"  name="editpdt_tags"  class="regular-text" id="editpdt_tags" value="<?php if(isset($_POST['editpdt_tags']) && !empty($_POST['editpdt_tags'])){ 	echo $_POST['editpdt_tags'];
				} else{
	echo stripslashes($result_feditpdt->item_tags);				
				} ?>" />
		 

	

 		<p class="description" id=""></p> 	   

        <p class="description">Product tags.</p>

               </td></tr>             
               </tbody>
               </table>
               <?php submit_button('Save Product', 'primary', 'submit_envatopdtsave', '', '' ); ?>
</form>	
	
	<?php
} //$_POST['submit_editenvatopdt'] end
else{
	
 if(isset($_POST['submit_delenvatopdt']) && is_numeric($_POST['delenvatopdt_id'])){
	$query_seldel = "delete from ".$wpdb->prefix."envato where id = '{$_POST['delenvatopdt_id']}' limit 1";
	$result_seldel = $wpdb->query($query_seldel);
	
	if($result_seldel){
		$error = "Item Deleted Successfully.";
	}
} 

 if(isset($_POST['submit_delenvatopdtattach']) && is_numeric($_POST['delenvatopdtattach_id'])){
 	
 	wp_delete_attachment($_POST['delenvatopdtattach_id']);
	$query_seldel1 = "update ".$wpdb->prefix."envato set attachment_id = '0' where id = '{$_POST['delenvatopdt_id']}' limit 1";
	$result_seldel1 = $wpdb->query($query_seldel1);
	
	if($result_seldel1){
		$error = "Item Attachment Deleted Successfully.";
	}
} 
	
	if(isset($_POST['submit_pdtsearch']) && $_POST['pdtsearch']!=''){
		if(is_numeric($_POST['pdtsearch'])){
				$rows = $wpdb->get_results("SELECT id,item_url, item_id, item_title, attachment_id, item_type, item_content, itemc_content FROM ".$wpdb->prefix."envato where item_id = ".$_POST['pdtsearch']." or attachment_id = ".$_POST['pdtsearch']." order by id DESC");	
		}
		else{
		$rows = $wpdb->get_results("SELECT id,item_url, item_id, item_title, attachment_id, item_type, item_content, itemc_content FROM ".$wpdb->prefix."envato where item_title like '%".esc_sql($_POST['pdtsearch'])."%' order by id DESC");		
		}
	}
	else if	(isset($_GET['searchterm']) && $_GET['searchterm']!=''){
		if(is_numeric($_GET['searchterm'])){
				$rows = $wpdb->get_results("SELECT id,item_url, item_id, item_title, attachment_id, item_type, item_content, itemc_content FROM ".$wpdb->prefix."envato where item_id = ".$_GET['searchterm']." or attachment_id = ".$_GET['searchterm']." order by id DESC");	
		}
		else{
		$rows = $wpdb->get_results("SELECT id,item_url, item_id, item_title, attachment_id, item_type, item_content, itemc_content FROM ".$wpdb->prefix."envato where item_title like '%".esc_sql($_GET['searchterm'])."%' order by id DESC");		
		}
	}
else{
		$rows = $wpdb->get_results("SELECT id,item_url, item_id, item_title, attachment_id, item_type, item_content, itemc_content FROM ".$wpdb->prefix."envato order by id DESC");
}
	
	
$rows_per_page = 5;
$current = (isset($_GET['paged']) && intval($_GET['paged'])) ? intval($_GET['paged']) : 1;
 
	$searchparam = isset($_POST['pdtsearch'])?array('searchterm'=>$_POST['pdtsearch']):((isset($_GET['searchterm']))?array('searchterm'=>$_GET['searchterm']):array()); 
	

 
    global $wp_rewrite;

    $pagination_args = array(
     'base' => @add_query_arg('paged','%#%'),
     'format' => '',
     'total' => ceil(sizeof($rows)/$rows_per_page),
     'current' => $current,
     'show_all' => false,
     'add_args' => $searchparam,
     'type' => 'plain',
    );

 /*   if( $wp_rewrite->using_permalinks() )
     $pagination_args['base'] = user_trailingslashit( trailingslashit( remove_query_arg('s',get_pagenum_link(1) ) ) . 'page/%#%/', 'paged'); */

    if( !empty($wp_query->query_vars['s']) )
     $pagination_args['add_args'] = array('s'=>get_query_var('s'));

  

    $start = ($current - 1) * $rows_per_page;
    $end = $start + $rows_per_page;
    $end = (sizeof($rows) < $end) ? sizeof($rows) : $end;
    
    ?>
	<div class="wrap">

<div id="icon-link-manager" class="icon32" ></div>

<h2>Sixthlife Search</h2>
<p align="right">

<strong> Envato : </strong><?php echo get_dbenvatothemecount(); ?> 

<strong>Pending : </strong><?php echo get_dbenvatolessprevcount(); ?> 

</p>
<?php
 $formsurl = currentpageurl();
 
 if(strpos(currentpageurl(), '&')!==FALSE){
 	$formsurl = explode('&', $formsurl);
 	$formsurl = $formsurl[0];
 }
 
?>
<form action="<?php echo $formsurl ?>" method="POST">
<label>Enter Item ID, Attachment ID, Keyword in Title</label>
<input type="text" name="pdtsearch" class="regular-text"/>
   <?php submit_button('Search', 'primary', 'submit_pdtsearch', '', '' ); ?>
</form>


<h3>Envato Products</h3>

<?php 
if( !ini_get('allow_url_fopen') ) {
   	echo '<div id="message" class="error" >This Plugin will not work Properly unless allow_url_fopen is On in php.in. allow_url_fopen is Off.</div>';
} 

?>
<?php 
if(!function_exists('curl_version')){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless Curl is Enabled. Curl is Disabled.</div>';
} ?>

<?php 
if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON==TRUE){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless WP-Cron is enabled. WP-Cron is Disabled.</div>';
} ?>

<?php
if(isset($error)&& $error!=''){
	echo '<div id="message" class="updated">'.$error.'</div>';
}
?>

<p class="description">When the Attachment Id (Att. Id) is 0, It means preview image was not uploaded. Edit and Upload in that case.
<br />

Before Deleting a Product delete the Image Attachment or Preview Image.

If Image Attachment was used when you created a WordPress Post from "Themes Product Post" Do not delete the Preview Image. Directly delete the Product.

</p>

<table class="wp-list-table widefat" cellspacing="0" >
    
    
    <thead>
        
        <tr>
            <th>SR No</th> 
              <th>Item URL</th>
                       <th>Item Details</th>
              <th>Item Title</th>
            <th>Item Content</th>
            <th>Edit</th>
            <th>Delete Preview</th>
             <th>Delete</th>
        </tr>   
    </thead>

    <tbody>
        <?php 
        $i =1;
       // while($row = mysql_fetch_assoc($result_p)){
       for ($i=$start;$i < $end ;++$i ) {
            $row = $rows[$i];   
            $itecont = ($row->itemc_content =='')?getErssdesc(stripslashes($row->item_content), get_option('endesccontains')):$row->itemc_content;
            echo "<tr><td>".($i+1)."</td>";
            echo "<td>". $row->item_url."</td>";
            echo "<td><strong>Item Id:</strong> ". $row->item_id."<br />";
            echo "<strong>Attachment Id:</strong> ". $row->attachment_id."<br />";
            echo "<strong>Item Type: </strong>". $row->item_type."</td>"; 
            echo "<td>". stripslashes($row->item_title)."</td>"; 
            echo "<td>". $itecont."</td>";
            echo '<td><form action="" method="POST">
                   <input type="hidden" value="'.$row->id.'" name="editenvatopdt_id" /> 
                ';   
				
			if(isset($_POST['pdtsearch'])&& $_POST['pdtsearch']!=''){
				echo ' <input type="hidden" value="'.$_POST['pdtsearch'].'" name="pdtsearch" />';
			}
			  
			echo '<input type="submit" name="submit_editenvatopdt" value="Edit" class="button button-primary"/>
			</form></td>'; 
			
			 echo '<td><form action="" method="POST">
			 	<input type="hidden" value="'.$row->id.'" name="delenvatopdt_id" />
                   <input type="hidden" value="'.$row->attachment_id.'" name="delenvatopdtattach_id" />
                    <input type="submit" name="submit_delenvatopdtattach" value="NoPreview" class="button button-primary"/>
                </form></td>';     
           echo '<td><form action="" method="POST">
                   <input type="hidden" value="'.$row->id.'" name="delenvatopdt_id" />
                    <input type="submit" name="submit_delenvatopdt" value="Delete" class="button button-primary"/>
                </form></td></tr>';
       
           // $i++;
        }
         ?>
    </tbody>
    
</table>
<div class="sixthlife_pagi">
<span class="pagination-links">
<?php   echo paginate_links($pagination_args); ?>
</span>
</div>
        </div>	

	<?php
}	
}



  add_action('admin_menu', 'register_thse_addenvatorssurls_submenu_page');



function register_thse_addenvatorssurls_submenu_page() {

	add_submenu_page( 'thse_plstarter_page', 'Envato RSS Urls', 'Envato RSS Urls', 'manage_options', 'thse_addenvatorssurls_submenu_page','thse_addenvatorssurls_submenu_page' ); 

}
      
function thse_addenvatorssurls_submenu_page() {
global $wpdb;
	$error = '';
		 if(isset($_POST['rssurls']) && !empty($_POST['rssurls'])){ 
                     
                     if(strpos($_POST['rssurls'],'|' )!==FALSE){
                        $postedrssurls = explode("|",$_POST['rssurls']); 
                     } 
                     if(!isset($postedrssurls)||count($postedrssurls)==1){
                        $postedrssurls = str_replace("\n\r", "\n", $_POST['rssurls']);
                        $postedrssurls = explode("\n",$postedrssurls); 
                     }
                     
                     if(count($postedrssurls)==1){
                        $postedrssurls = explode(PHP_EOL,$_POST['rssurls']); 
                     }                     
			//print_r($postedrssurls);
			$ik = 1;
			$newrssforc = array();
			foreach($postedrssurls as $rssurl){
			if($ik>10){
				$newrssforc[] = $rssurl;
				
				$error .=	$rssurl. ' scheduled for addition.<br />';
			}
			else{
			$insertrss = getNewRSS(trim($rssurl));
					//echo $insertrss;
			if($insertrss === true){
				$error .=	$rssurl. ' added successfully.<br />';
				}
			else if($insertrss === 'exists'){
				$error .= 	$rssurl . ' already exists<br />';
				}
				else if($insertrss === 'empty'){
				$error .=	$rssurl. ' rss content could not be fetched.<br />';	
				}				
			}

		$ik++;
		}
			if(isset($newrssforc)&& count($newrssforc)>0){
					if(!(FALSE=== get_transient('thse_frechrss'))){
						$oldnewrss = get_transient('thse_frechrss');
						$newrssforc =	array_merge($oldnewrss,$newrssforc );
						delete_transient('thse_frechrss');
					}
				
			set_transient('thse_frechrss', $newrssforc);
			}
		}
                
                $error1 = '';
		 if(isset($_POST['submit_delrssurl']) && is_numeric($_POST['delrssurl_id'])){ 
 		
                $rssdelid = $_POST['delrssurl_id'];
                
                $query_rssdel = "delete from ".$wpdb->prefix."envatorssurls where id = '{$rssdelid}' limit 1";
		$result_rssdel = $wpdb->query($query_rssdel);                     
                    if($result_rssdel){
                        $error1 = "RSS URL deleted successfully.";
                    }
                    else{
                        $error1 = "RSS URL cound not be deleted.";
                    }
                 }
                 
                $error1 = '';
		 if(isset($_POST['submit_disabrssurl']) && is_numeric($_POST['disabrssurl_id'])){ 
 		
                $rssdisabid = $_POST['disabrssurl_id'];
                
                if($_POST['submit_disabrssurl']=='Disable'){
                $query_rssdisab = "update ".$wpdb->prefix."envatorssurls set disabled = '1' where id = '{$rssdisabid}' limit 1";
                $result_rssdisab = $wpdb->query($query_rssdisab); 
                     
                    if($result_rssdisab){
                        $error1 = "RSS URL disabled.";
                    }
                    else{
                        $error1 = "RSS URL cound not be disabled.";
                    }               
                }
                else if($_POST['submit_disabrssurl']=='Enable'){
                $query_rssdisab = "update ".$wpdb->prefix."envatorssurls set disabled = '0' where id = '{$rssdisabid}' limit 1";
                $result_rssdisab = $wpdb->query($query_rssdisab); 
                    
                    if($result_rssdisab){
                        $error1 = "RSS URL enabled.";
                    }
                    else{
                        $error1 = "RSS URL cound not be enabled.";
                    }                
                
                }

                 }
                 
                 
		
		 ?>


	<div class="wrap">

<div id="icon-link-manager" class="icon32" ></div>

<h2>Envato RSS URLS</h2>
<p align="right">

<strong> Envato : </strong><?php echo get_dbenvatothemecount(); ?> 

<strong>Pending : </strong><?php echo get_dbenvatolessprevcount(); ?> 


 
</p>
<h3>Add RSS URLs</h3>
<p class="description">
Feed URLS of Envato Products can be found here:  <a href="http://codecanyon.net/page/file_updates/#rss">Codecanyon Feed</a>, <a href="http://themeforest.net/page/file_updates/#rss">Themeforest Feed</a>, <a href="http://graphicriver.net/page/file_updates/#rss">Graphicriver Feed</a>, <a href="http://videohive.net/page/file_updates/#rss">Videohive Feed</a> and so on.
</p>

<?php 
if( !ini_get('allow_url_fopen') ) {
   	echo '<div id="message" class="error" >This Plugin will not work Properly unless allow_url_fopen is On in php.in. allow_url_fopen is Off.</div>';
} 

?>
<?php 
if(!function_exists('curl_version')){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless Curl is Enabled. Curl is Disabled.</div>';
} ?>

<?php 
if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON==TRUE){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless WP-Cron is enabled. WP-Cron is Disabled.</div>';
} ?>
<?php if($error!=''){echo'<div id="message" class="updated" >' .$error.'</div>';} ?>

<form method="post" action="" >
<table class="form-table">

<tbody>     

   	 <tr valign="top">

        <th  scope="row">

  <label for=""> RSS URLs</label> </th><td>

        

		<textarea  name="rssurls"  cols="100" rows="5" id="rssurls"><?php 
		 
                if(isset($_POST['rssurls']) && !empty($_POST['rssurls'])){ 
			

				echo $_POST['rssurls'];

		}
		?></textarea>

	

 		<p class="description" id="chkrssurls"></p> 	   

        <p class="description">List Envato RSS URLS one on each line OR separate
        each URL with a | character.</p>

               </td></tr> 
               
               </tbody>
               </table>
               <?php submit_button('Add RSS Urls', 'primary', 'submit_addrssurl', '', '' ); ?>
</form>

<h3>RSS URLS List</h3>
                    <?php
			$query_rssu = "select * from ".$wpdb->prefix."envatorssurls order by id desc";
			$result_rssu = $wpdb->get_results($query_rssu);                    
            // print_r($result_rssu)  ;   
                    ?>

 <?php 
if( !ini_get('allow_url_fopen') ) {
   	echo '<div id="message" class="error" >This Plugin will not work Properly unless allow_url_fopen is On in php.in. allow_url_fopen is Off.</div>';
} 

?>
<?php 
if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON==TRUE){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless WP-Cron is enabled. WP-Cron is Disabled.</div>';
} ?>


<?php 
if( !ini_get('allow_url_fopen') ) {
   	echo '<div id="message" class="error" >This Plugin will not work Properly unless allow_url_fopen is On in php.in. allow_url_fopen is Off.</div>';
} 

?>
<?php 
if(!function_exists('curl_version')){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless Curl is Enabled. Curl is Disabled.</div>';
} ?>



<?php if($error1!=''){echo'<div id="message" class="updated" >' .$error1.'</div>';} ?>


<table class="wp-list-table widefat" cellspacing="0" >
    
    
    <thead>
        
        <tr>
            <th>SR No</th>  
            <th>RSS URL</th>
            <th>Disable</th>
            <th>Delete</th>
        </tr>   
    </thead>

    <tbody>
        
        
            <?php $i = 1;
            
            foreach($result_rssu as $row){
             
                echo "<tr><td>".$i.".</td><td> ".$row->rss_url."</td>";
                
                $subval = ($row->disabled=='1')?'Enable':'Disable';
                
                echo '<td><form action="" method="POST">
                   <input type="hidden" value="'.$row->id.'" name="disabrssurl_id" />
                    <input type="submit" name="submit_disabrssurl" value="'.$subval.'" class="button button-primary"/>
                </form></td> ';
                
                echo '<td><form action="" method="POST">
                   <input type="hidden" value="'.$row->id.'" name="delrssurl_id" />
                    <input type="submit" name="submit_delrssurl" value="Delete" class="button button-primary"/>
                </form></td>        </tr>';
                $i++;
             } ?>
                       
    </tbody>
    </table>  <p class="description">The Disable button excludes the RSS URL from regular product fetch schedules(crons) and
      from Envato RSS themes tab.
 </p>
<?php }   

  add_action('admin_menu', 'register_thse_addenvatorssproducts_submenu_page');



function register_thse_addenvatorssproducts_submenu_page() {

	add_submenu_page( 'thse_plstarter_page', 'Envato RSS Themes', 'Envato RSS Themes', 'manage_options', 'thse_addenvatorssproducts_submenu_page','thse_addenvatorssproducts_submenu_page' ); 

}
      
function thse_addenvatorssproducts_submenu_page() {
	global $wpdb;
	$error = '';
if(isset($_POST['delpendingcr'])){
	if(!(FALSE=== get_transient('thse_reachpdt'))){delete_transient('thse_reachpdt');}

	if(!(FALSE=== get_transient('thse_qrsspdt'))){delete_transient('thse_qrsspdt');}

}	
	if(isset($_POST['submit_addrssproducts']) && isset($_POST['fetchfresh'])){

		$error .= updateRSSUrls($_POST['select_envrssurls']);
	}
	if(isset($_POST['submit_addrssproducts']) && isset($_POST['fetchfreshrssall'])){

		$error .= updateRSSUrls();
	}
	
	if(isset($_POST['submit_addrssproducts']) && isset($_POST['fetchfreshpdtall'])){

		$error .= productsRSS();
	}	

	if(isset($_POST['submit_addrssproducts'])&& !empty($_POST['select_envrssurls']) && (count($_POST['select_envrssurls'])<2||!(isset($_POST['fetchfresh'])))){

		$error .= productsRSS($_POST['select_envrssurls']);
	}
		
		 ?>


	<div class="wrap">

<div id="icon-link-manager" class="icon32" ></div>

<h2>Envato RSS Products</h2>
<p align="right">
<?php 		if(!(FALSE=== get_transient('thse_reachpdt'))){
			echo 'Pending Product Fetches: '.count(get_transient('thse_reachpdt')); } ?> 

<?php 		if(!(FALSE=== get_transient('thse_qrsspdt'))){
			echo 'Pending RSS Fetches: '.count(get_transient('thse_qrsspdt')); } ?> 
			
<strong> Envato : </strong><?php echo get_dbenvatothemecount(); ?> 

<strong>Pending : </strong><?php echo get_dbenvatolessprevcount(); ?> 
<?php if(!(FALSE=== get_transient('thse_reachpdt'))|| !(FALSE=== get_transient('thse_qrsspdt'))){ ?>

<form action="" method="POST" style="float:right;">
<input type="hidden" name="delpendingcr" value="Cancel Fetches" />
<input type="submit" name="delpendingcr" value="Cancel Fetches" class="button button-primary" />
</form>

<?php } ?>
</p>
<h3>Envato RSS Products</h3>

<p class="description">
You can get Envato Products from the rss content here. <br />
The rss page is stored in database, if you select the checkbox below new content for the rss page will be fetched from Envato's websites and stored in database.  
</p>
<?php 
if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON==TRUE){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless WP-Cron is enabled. WP-Cron is Disabled.</div>';
} ?>


<?php 
if( !ini_get('allow_url_fopen') ) {
   	echo '<div id="message" class="error" >This Plugin will not work Properly unless allow_url_fopen is On in php.in. allow_url_fopen is Off.</div>';
} 

?>
<?php 
if(!function_exists('curl_version')){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless Curl is Enabled. Curl is Disabled.</div>';
} ?>


<?php if($error!=''){echo'<div id="message" class="updated" >' .$error.'</div>';} ?>

<form method="post" action="" >
<table class="form-table">

<tbody>     

         <tr valign="top">

		

		<th scope="row"><label for="">Select URLs</label></th> <td>

        <select name="select_envrssurls[]" id="select_envrssurls" multiple="true" size="6" >

        <?php 
		$query_rssurls = "select id, rss_url from ".$wpdb->prefix."envatorssurls where disabled='0'";
		$result_rssurls = $wpdb->get_results($query_rssurls);
		
		foreach($result_rssurls as $row){?> 

        <option value="<?php echo $row->id; ?>"><?php echo $row->rss_url; ?></option>

        <?php } ?>

		</select>

        <p class="description">Select RSS Pages from which to fetch Products. 
		Optimal Value:1-3 RSS URLS at a time. 
		<br />In case of issues increase max_execution_time in php.ini
		</p>

       </td>

         </tr>

         
<tr>
<th scope="row">
<td>
<label for="fetchfresh">
<input id="fetchfresh" type="checkbox" name="fetchfresh" value="1" />
Fetch Fresh Content from respective RSS Urls.
</label>
</td>
</tr>
<!--
<tr>
<th scope="row">
<td>
<label for="fetchfresh">
<input id="fetchfreshrssall" type="checkbox" name="fetchfreshrssall" value="1" />
Fetch Fresh All RSS Urls.
</label>
</td>
</tr>

         
<tr>
<th scope="row">
<td>
<label for="fetchfresh">
<input id="fetchfreshpdtall" type="checkbox" name="fetchfreshpdtall" value="1" />
Fetch Products for All RSS Urls.
</label>
</td>
</tr>
-->
           </tbody>
               </table>
			             
               <?php submit_button('Add RSS Products', 'primary', 'submit_addrssproducts', '', '' ); ?>
                        
</form>
<?php }

  add_action('admin_menu', 'register_thse_fetchpreview_submenu_page');



function register_thse_fetchpreview_submenu_page() {

	add_submenu_page( 'thse_plstarter_page', 'Envato Previews', 'Envato Previews', 'manage_options', 'thse_fetchpreview_submenu_page','thse_fetchpreview_submenu_page' ); 

}
      
function thse_fetchpreview_submenu_page() {
	$error = '';
	
if(isset($_POST['delpendingpr'])){
	if(!(FALSE=== get_transient('thse_qimg'))){delete_transient('thse_qimg');}
}	
	
		if(isset($_POST['submit_fetchpreview']) && is_numeric($_POST['limit_startfrm'])&& is_numeric($_POST['limit_nofrecords'])){

		$error .=	updateAttach($_POST['limit_startfrm'], $_POST['limit_nofrecords']);
	}
	

	
		 ?><div class="wrap">

<div id="icon-link-manager" class="icon32" ></div>

<h2>Sixthlife Search</h2>
<p align="right">

<?php 		if(!(FALSE=== get_transient('thse_qimg'))){
			echo 'Pending Preview Fetches: '.count(get_transient('thse_qimg')); } ?> 

<strong> Envato : </strong><?php echo get_dbenvatothemecount(); ?> 

<strong>Pending : </strong><?php echo get_dbenvatolessprevcount(); ?> 

<?php if(!(FALSE=== get_transient('thse_qimg'))){ ?>

<form action="" method="POST" style="float:right;">
<input type="hidden" name="delpendingpr" value="Cancel Fetches" />
<input type="submit" name="delpendingpr" value="Cancel Fetches" class="button button-primary" />
</form>

<?php } ?>
 
</p>
<h3>Preview Images (Envato)</h3>
<?php 
if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON==TRUE){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless WP-Cron is enabled. WP-Cron is Disabled.</div>';
} ?>


<?php 
if( !ini_get('allow_url_fopen') ) {
   	echo '<div id="message" class="error" >This Plugin will not work Properly unless allow_url_fopen is On in php.in. allow_url_fopen is Off.</div>';
} 

?>
<?php 
if(!function_exists('curl_version')){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless Curl is Enabled. Curl is Disabled.</div>';
} ?>

<?php if($error!=''){echo'<div id="message" class="updated" >' .$error.'</div>';} ?>


<form method="post" action="" >
<table class="form-table">

<tbody>     

       <tr valign="top">

		

		<th scope="row">


		<label for="">Limit ID Start</label>

</th><td>

		 <input type="text"  name="limit_startfrm" class="small-text" value="<?php if(isset($_POST['limit_startfrm'])){  echo $_POST['limit_startfrm'];}else{echo 0;} ?>" />

		 		<label for="">Number of Records</label>

		 		 <input type="text" class="small-text" name="limit_nofrecords" value="<?php if(isset($_POST['limit_nofrecords'])){  echo $_POST['limit_nofrecords'];}else{echo 5;} ?>" />

        <p class="description">PHP limit parameter to select a certain number of records to fetch images. </p>

       </td>

         </tr>



           </tbody>
               </table>
			             
               <?php submit_button('Download', 'primary', 'submit_fetchpreview', '', '' ); ?>
                        
</form>
<?php }  



add_action('admin_menu', 'register_thse_themepdtpost_submenu_page');



function register_thse_themepdtpost_submenu_page() {

	add_submenu_page( 'thse_plstarter_page', 'Theme Products Post', 'Theme Products Post', 'manage_options', 'thse_themepdtpost_submenu_page','thse_themepdtpost_submenu_page' ); 

}
      
function thse_themepdtpost_submenu_page() {
global $wpdb;
	$error = '';
	
		if(isset($_POST['submit_themepdtpost']) ){ 
	$items_array = array('serial'=>1,'pagecontent'=>'');
	
	$items_array = 	envatoThemepdtpart($_POST['envatoth_mktplc'], $_POST['envatokwd_title'],$_POST['notenvatokwd_title'], $_POST['envatokwd_cat'],  $_POST['envtitlecont_cond'], $_POST['envcontcat_cond'], $_POST['envatopdt_max'], $items_array);
		
	

	$pagetitle = $_POST['theme_post_title'];
	$pagecontent =  $items_array['pagecontent'];
	$pagetags = $_POST['theme_post_tags'];
	
		if(!isset($_POST['final_createpostd']) && !isset($_POST['final_createpost'])){
		//	echo '<h2>'.$pagetitle.'</h2>';
		//	echo '<h2>'.$pagetags.'</h2>';
			
		//	echo $pagecontent;
		}
		
		if(isset($_POST['theme_post_cats']) && !empty($_POST['theme_post_cats'])){
			$pagecats = $_POST['theme_post_cats'];
		}
		else{
			$pagecats = array('1');
		}
		
		if(isset($_POST['theme_post_status'])){
			$pagestatus = $_POST['theme_post_status'];
		}
		else{
			$pagestatus = 'draft';
		}

	if(isset($_POST['final_createpostd'])){$deleteifexists = true;
	$error = final_create_post($pagetitle, $pagecontent, $pagetags,$pagecats,$pagestatus, $deleteifexists);	
	}
	//if(isset($_POST['final_createpost'])){
	else{	
	$deleteifexists = false;
	$error = final_create_post($pagetitle, $pagecontent, $pagetags,$pagecats,$pagestatus, $deleteifexists);	
	}

	}

	

	
		 ?>
	<div class="wrap">

<div id="icon-link-manager" class="icon32" ></div>

<h2>Sixthlife Search</h2>
<p align="right">

<strong> Envato : </strong><?php echo get_dbenvatothemecount(); ?> 

<strong>Pending : </strong><?php echo get_dbenvatolessprevcount(); ?> 

</p>
<?php 
if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON==TRUE){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless WP-Cron is enabled. WP-Cron is Disabled.</div>';
} ?>

<?php 
if( !ini_get('allow_url_fopen') ) {
   	echo '<div id="message" class="error" >This Plugin will not work Properly unless allow_url_fopen is On in php.in. allow_url_fopen is Off.</div>';
} 

?>
<?php 
if(!function_exists('curl_version')){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless Curl is Enabled. Curl is Disabled.</div>';
} ?>

<?php if($error!=''){echo'<div id="message" class="updated" >' .$error.'</div>';} ?>

<h3>Theme Post Article Details</h3>

<form method="post" action="" >
<table class="form-table">

<tbody>     

       <tr valign="top">

		

		<th scope="row">


		<label for="">Theme Post Title</label>

</th><td>

			 	
		 		 <input type="text" class="regular-text" name="theme_post_title" value="<?php if(isset($_POST['theme_post_title'])){  echo $_POST['theme_post_title'];} ?>" />

        <p class="description">Heading or Title of the Article being created.  </p>

       </td>

         </tr>
         
       <tr valign="top">

		

		<th scope="row">


		<label for="">Theme Post Tags</label>

</th><td>

			 	
		 		 <input type="text" class="regular-text" name="theme_post_tags" value="<?php if(isset($_POST['theme_post_tags'])){  echo $_POST['theme_post_tags'];} ?>" />

        <p class="description">Tags for Article being created.  </p>

       </td>

         </tr>
         
        <tr valign="top">

		

		<th scope="row">


		<label for="">Theme Post Categories</label>

</th><td>

			 	
		 <?php	$theme_post_cats =	wp_dropdown_categories('hierarchical=1&name=theme_post_cats[]&echo=0&hide_empty=0');
		 
		 $theme_post_cats = str_replace('id=\'theme_post_cats[]\'', 'id=\'theme_post_cats\' multiple=\'true\' size=\'8\'', $theme_post_cats);
		 echo $theme_post_cats;
		 ?>

        <p class="description">Select Categories for the Article being created.  </p>

       </td>

         </tr>       
  
         <tr valign="top">

		

		<th scope="row">


		<label for="">Theme Post Status</label>

</th><td>
<select name="theme_post_status">

<option value="draft">draft</option>
<option value="publish">publish</option>
</select>
			 	
        <p class="description">Select Article status being created.  </p>

       </td>

         </tr>        
         
</tbody></table>
<h3>Theme Products Post Part (Envato)</h3>



<table class="form-table">

<tbody>     

       <tr valign="top">

		

		<th scope="row">


		<label for="">Market Place</label>

</th><td>

			 	
		 		 <input type="text" class="medium-text" name="envatoth_mktplc" id="envatoth_mktplc" value="<?php if(isset($_POST['envatoth_mktplc'])){  echo $_POST['envatoth_mktplc'];}else{echo 'themeforest';} ?>" />

        <p class="description">This is the name of one of the Envato Marketplace.Ex: themeforest, graphicriver, codecanyon etc  </p>

       </td>

         </tr>

       <tr valign="top">

		

		<th scope="row">


		<label for="">Keyword In Title/ Content </label>

</th><td>

			 	
		 		 <input type="text" class="regular-text" name="envatokwd_title" id="envatokwd_title" value="<?php if(isset($_POST['envatokwd_title'])){  echo $_POST['envatokwd_title'];} ?>" />
		 		 
		 		 	<label for="">Title Content Condition </label> <input type="text" id="envtitlecont_cond" class="small-text" name="envtitlecont_cond" value="<?php if(isset($_POST['envtitlecont_cond'])){  echo $_POST['envtitlecont_cond'];}else {echo 'or';} ?>" />
		 		 	
	<label for="">Content Category Condition </label> <input type="text" class="small-text"  id="envcontcat_cond" name="envcontcat_cond" readonly="true" value="<?php if(isset($_POST['envcontcat_cond'])){  echo $_POST['envcontcat_cond'];} else {echo 'and';} ?>" />

        <p class="description">( Keywords in title with and/ or condition & means all the keyowords should be present. For Example: "book&store" or "chemistry|Physics") </p>

       </td>

         </tr>
         
		<th scope="row">


		<label for="">Keyword NOT In Title/ Content </label>

</th><td>

			 	
		 		 <input type="text" class="regular-text" name="notenvatokwd_title" id="notenvatokwd_title"  value="<?php if(isset($_POST['notenvatokwd_title'])){  echo $_POST['notenvatokwd_title'];} ?>" />
		 		 

        <p class="description">( Keywords in title and description should NOT be present. Write with & conditon</p>

       </td>

         </tr>
       <tr valign="top">

		

		<th scope="row">


		<label for="">Category Contains </label>

</th><td>

			 	
		 		 <input type="text" class="regular-text" name="envatokwd_cat" id="envatokwd_cat" value="<?php if(isset($_POST['envatokwd_cat'])){  echo $_POST['envatokwd_cat'];} ?>" />

        <p class="description">( Partial or Full Category Name. For Example: "PHP&Scripts" or "PHP|CSS") </p>

       </td>

         </tr>
         
       <tr valign="top">

		

		<th scope="row">


		<label for="">Maximum number of Products </label>

</th><td>

			 	
		 		 <input type="text" class="medium-text" name="envatopdt_max"  id="envatopdt_max" value="<?php if(isset($_POST['envatopdt_max'])){  echo $_POST['envatopdt_max'];}else {echo 10;} ?>" />
		 		 
		 		 <input type="button" name="envatopdt_show" value="Show" onclick="javascript:envatoParts('<?php echo plugin_dir_url(__FILE__ ); ?>');"/>

        <p class="description"/>( Number of Products to get. </p>

       </td>

         </tr>
           </tbody>
               </table>
         <div id="envatotemp"></div>	      
               
               

         <tr>
		<th scope="row"></th>
		<td>
		<label for="final_createpost">
		<input id="final_createpost" type="checkbox" name="final_createpost" value="1" />
		Finalize and Create A Post if the post does not exist.
		</label>
		</td>
		</tr>
       <tr>
		<th scope="row"></th>
		<td>
		<label for="final_createpostd">
		<input id="final_createpostd" type="checkbox" name="final_createpostd" value="1" />
		Finalize and Create A Post, Delete Old Post.
		</label>
		</td>
		</tr>
           </tbody>
               </table>	
			   
			   <div id="monstertemp"></div>		             
               <?php submit_button('Download', 'primary', 'submit_themepdtpost', '', '' ); ?>
                        
</form>
<?php } 

 add_action('admin_menu', 'register_thse_logsenvatothemes_submenu_page');



function register_thse_logsenvatothemes_submenu_page() {

	add_submenu_page( 'thse_plstarter_page', 'Automatic Logs', 'Automatic Logs', 'manage_options', 'thse_logsenvatothemes_submenu_page','thse_logsenvatothemes_submenu_page' ); 

}
      
function thse_logsenvatothemes_submenu_page() { 
 

global $wpdb;

$rows_per_page = 10;
$current = (isset($_GET['paged']) && intval($_GET['paged'])) ? intval($_GET['paged']) : 1;
 
$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."envatologs order by id DESC");
 
    global $wp_rewrite;

    $pagination_args = array(
     'base' => @add_query_arg('paged','%#%'),
     'format' => '',
     'total' => ceil(sizeof($rows)/$rows_per_page),
     'current' => $current,
     'show_all' => false,
     'type' => 'plain',
    );

  /*  if( $wp_rewrite->using_permalinks() )
     $pagination_args['base'] = user_trailingslashit( trailingslashit( remove_query_arg('s',get_pagenum_link(1) ) ) . 'page/%#%/', 'paged'); */

    if( !empty($wp_query->query_vars['s']) )
     $pagination_args['add_args'] = array('s'=>get_query_var('s'));

    

    $start = ($current - 1) * $rows_per_page;
    $end = $start + $rows_per_page;
    $end = (sizeof($rows) < $end) ? sizeof($rows) : $end;
    
    ?>
	<div class="wrap">

<div id="icon-link-manager" class="icon32" ></div>

<h2>Sixthlife Search</h2>
<p align="right">

<strong> Envato : </strong><?php echo get_dbenvatothemecount(); ?> 

<strong>Pending : </strong><?php echo get_dbenvatolessprevcount(); ?> 

</p>
<h3>Automatic Logs</h3>
<?php 
if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON==TRUE){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless WP-Cron is enabled. WP-Cron is Disabled.</div>';
} ?>
<?php 
if( !ini_get('allow_url_fopen') ) {
   	echo '<div id="message" class="error" >This Plugin will not work Properly unless allow_url_fopen is On in php.in. allow_url_fopen is Off.</div>';
} 

?>
<?php 
if(!function_exists('curl_version')){
	echo '<div id="message" class="error" >This Plugin will not work Properly unless Curl is Enabled. Curl is Disabled.</div>';
} ?>
<table class="wp-list-table widefat" cellspacing="0" >
    
    
    <thead>
        
        <tr>
            <th>SR No</th>  
            <th>Date</th>
            <th>Title</th>
            <th>Message</th>
        </tr>   
    </thead>

    <tbody>
        <?php 
        $i =1;
        for ($i=$start;$i < $end ;++$i ) {
        //while($row = mysql_fetch_assoc($result_l)){
            $row = $rows[$i];
            echo "<tr><td>".($i+1)."</td>";
            echo "<td>". date("F jS, Y",strtotime($row->timestamp))."</td>";
            echo "<td>". $row->title."</td>";
           echo "<td>". $row->message."</td></tr>"; 
          // $i++;
        }
         ?>
    </tbody>
    
</table>
<div class="sixthlife_pagi">
    <span class="pagination-links">
<?php echo paginate_links($pagination_args); ?>
    </span>
</div>
        </div>
<?php	




    }

if(get_option('enpscronintsschedule')!='none'){
	add_action( 'wp', 'thse_setup_envatoimg_schedule' );
	
	//register_activation_hook( __FILE__, 'thse_setup_envatoimgimport_schedule' );

}


/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function thse_setup_envatoimg_schedule() {

	if ( ! wp_next_scheduled( 'thse_envatoimg_event' )&& get_option('enpscronintsschedule')!='none' ) {
		wp_schedule_event( time(), get_option('enpscronintsschedule'), 'thse_envatoimg_event');
		
	}
	 if(wp_next_scheduled( 'thse_envatoimg_event' )&& cron_schedule_change('thse_envatoimg_event', get_option('enpscronintsschedule'))==true){
		$timestamp = wp_next_scheduled( 'thse_envatoimg_event' );
		wp_unschedule_event( $timestamp, 'thse_envatoimg_event' );
		wp_schedule_event( time(), get_option('enpscronintsschedule'), 'thse_envatoimg_event');
	}

}


/**
 * On the scheduled action hook, run a function.
 */
function thse_do_envatoimg() {
global $wpdb;
		$error = updateAttach(0, get_option('envatopreviewfetchct'));
                
                                $logtitle = 'Sixthlife Envato Previews Cron '.get_option('enpscronintsschedule');
                                $query_log1 = "insert into ".$wpdb->prefix."envatologs (`title`, `message`) values('{$logtitle}', '{$error}')";
                                $result_log1 = $wpdb->query($query_log1);	
                                
                if(get_option('sendcronresults')=='1')
		{
			add_filter( 'wp_mail_content_type', 'set_html_content_type' );
			wp_mail(get_option('emailresults'), 'Sixthlife Envato Previews Cron '.get_option('enpscronintsschedule'), $error);		
			remove_filter( 'wp_mail_content_type', 'set_html_content_type' );	
		}		
	
		}
		
if(get_option('enpscronintsschedule')!='none'){

add_action( 'thse_envatoimg_event', 'thse_do_envatoimg' );

}
  
//thse_do_envatoimg();

if(get_option('envcronintsschedule')!='none'){
	add_action( 'wp', 'thse_setup_envatorss_schedule' );
}


/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function thse_setup_envatorss_schedule() {
	if ( ! wp_next_scheduled( 'thse_envatorss_event' )&& get_option('envcronintsschedule')!='none' ) {
		wp_schedule_event( time(), get_option('envcronintsschedule'), 'thse_envatorss_event');
	}
	 if(wp_next_scheduled( 'thse_envatorss_event' )&& cron_schedule_change('thse_envatorss_event', get_option('envcronintsschedule'))==true){
		$timestamp = wp_next_scheduled( 'thse_envatorss_event' );
		wp_unschedule_event( $timestamp, 'thse_envatorss_event' );
		wp_schedule_event( time(), get_option('envcronintsschedule'), 'thse_envatorss_event');
	}

}

function cron_schedule_change($currentcronevent, $dbschedule){
$allcrons = 	_get_cron_array();

foreach($allcrons as $name=>$cron){ 
	if(array_key_exists($currentcronevent, $cron)){
		
		foreach($cron[$currentcronevent] as $eventi){ //echo $eventi['schedule']; echo $dbschedule;
			if($eventi['schedule']!=$dbschedule){
			
					return true;
			}
		}
	}
}
return false;
}

//echo cron_schedule_change('thse_envatorss_event', 'daily');

if(get_option('envcronintsschedule')!='none'){
add_action( 'thse_envatorss_event', 'thse_do_envatorss' );
}

/**
 * On the scheduled action hook, run a function.
 */
function thse_do_envatorss() {
	global $wpdb;
			$error = '';
			$query_rss = "select * from ".$wpdb->prefix."envatorssurls  where disabled='0' order by update_time asc limit 1";
			$result_rss = $wpdb->get_results($query_rss);
			$idarray = array();
			foreach($result_rss as $row){
				
				$idarray[] = $row->id;
				}
				
				$error .= updateRSSUrls($idarray);
				$error .= "<br />\n";
				$error .= productsRSS($idarray);
                                $logtitle = 'Sixthlife Envato Themes Cron '.get_option('envcronintsschedule');
                                $query_log2 = "insert into ".$wpdb->prefix."envatologs (`title`, `message`) values('{$logtitle}', '{$error}')";
                                $result_log2 = $wpdb->query($query_log2);
		if(get_option('sendcronresults')=='1')
		{
			add_filter( 'wp_mail_content_type', 'set_html_content_type' );
			wp_mail(get_option('emailresults'), 'Sixthlife Envato Themes Cron '.get_option('envcronintsschedule'), $error);
			remove_filter( 'wp_mail_content_type', 'set_html_content_type' );	
		}		

		
		}
  
//thse_do_envatorss();




  add_action('admin_menu', 'register_thsesettings');


function register_thsesettings() {
	//register our settings
	register_setting( 'thse-settings-group', 'envatouseraff' );
	register_setting( 'thse-settings-group', 'emailresults' );
	register_setting( 'thse-settings-group', 'sendcronresults' );
	register_setting( 'thse-settings-group', 'envatothemeitemcode' );
	register_setting( 'thse-settings-group', 'eachthemeitemcode' );	
	register_setting( 'thse-settings-group', 'cronintsschedule' );
	register_setting( 'thse-settings-group', 'envcronintsschedule' );
	register_setting( 'thse-settings-group', 'enpscronintsschedule' );	
	register_setting( 'thse-settings-group', 'envatopreviewfetchct' );
	register_setting( 'thse-settings-group', 'endesccontains' );
	register_setting( 'thse-settings-group', 'endesincimages' );
	register_setting( 'thse-settings-group', 'endesincstrip' );	
	register_setting( 'thse-settings-group', 'endesincdemos' );		
}


// create custom plugin settings menu
add_action('admin_menu', 'thse_configuration_menu');


function thse_configuration_menu() {

add_options_page('Sixthlife Config', 'Sixthlife Config', 'administrator', __FILE__,'thse_configuration_page', plugins_url('/images/icon.png', __FILE__));  	

	add_action( 'admin_init', 'register_thsesettings' );

}
      
function thse_configuration_page() {
	?>

<div class="wrap">

<div id="icon-link-manager" class="icon32" ></div>

<h2>Sixthlife Search</h2>
<p align="right">

<strong> Envato : </strong><?php echo get_dbenvatothemecount(); ?> 

<strong>Pending : </strong><?php echo get_dbenvatolessprevcount(); ?> 

 
</p>
<h3>Configuration</h3>


<form method="post" action="options.php" ><?php settings_fields( 'thse-settings-group' ); ?>



<table class="form-table">

<tbody>     

        <tr valign="top">

		

		<th scope="row"><label for="">Envato Username</label></th> <td>

        <input type="text" class="regular-text" name="envatouseraff" value="<?php if(get_option('envatouseraff')==""){ echo 'sixthlife';} else{  echo get_option('envatouseraff');} ?>" /> 

	
        <p class="description">Envato Username will be used to add affiliate string to the theme Urls.  </p>

       </td>

         </tr>

		 <tr valign="top">

        <th  scope="row"><label for="">

  Email Address</label></th><td>

        <input type="text" class="regular-text" name="emailresults" value="<?php if(get_option('emailresults')==""){ echo 'support@sixthlife.net';} else{  echo get_option('emailresults');} ?>" />

     <p class="description">The Email Address to which the Information for automated theme import results should be sent. </p>

               </td></tr>
               
               
		 <tr valign="top">

        <td  ></td><td>

        <input type="checkbox"  name="sendcronresults" value="1" <?php if(get_option('sendcronresults')=="1"){ echo "checked=\"checked\"";} ?> /><label>Send Automated Theme Fetch or Cron Results to Above Email.</label>

       

               </td></tr>
               

   			   
        <tr>
        <th scope="row"><label>Cron Schedules.</label></th>
        <td >

			
			<label>Envato Themes</label>
       		<select name="envcronintsschedule">
			<option value="none" <?php if(get_option('envcronintsschedule')=="none"){ echo "selected = \"selected\"";} ?> >None</option>
			<option value="daily" <?php if(get_option('envcronintsschedule')=="daily"){ echo "selected = \"selected\"";} ?> >Daily</option>
			<option value="twicedaily" <?php if(get_option('envcronintsschedule')=="twicedaily"){ echo "selected = \"selected\"";} ?>>Twice Daily</option>
			<option value="hourly" <?php if(get_option('envcronintsschedule')=="hourly"){ echo "selected = \"true\"";} ?>>Hourly</option>			
			</select>
			
			<label>Envato Previews</label>
       		<select name="enpscronintsschedule">
			<option value="none" <?php if(get_option('enpscronintsschedule')=="none"){ echo "selected = \"selected\"";} ?> >None</option>
			<option value="daily" <?php if(get_option('enpscronintsschedule')=="daily"){ echo "selected = \"selected\"";} ?> >Daily</option>
			<option value="twicedaily" <?php if(get_option('enpscronintsschedule')=="twicedaily"){ echo "selected = \"selected\"";} ?>>Twice Daily</option>
			<option value="hourly" <?php if(get_option('enpscronintsschedule')=="hourly"){ echo "selected = \"selected\"";} ?>>Hourly</option>			
			</select>
			  <p class="description">Please setup when products will be fetched for Envato and Non-Envato themes.   </p>
        </td>
        </tr> 
        
        <tr valign="top">

		

		<th scope="row"><label for="">Envato Previews (Count/Fetch) </label></th> <td>

        <input type="text" class="regular-text" name="envatopreviewfetchct" value="<?php if(get_option('envatopreviewfetchct')==""){ echo '10';} else{  echo get_option('envatopreviewfetchct');} ?>" /> 

	
        <p class="description">Number of Envato Previews Images to get Per Fetch or Cron. Suggested Value is 10. </p>

       </td>

         </tr>      
		
	 <tr valign="top">

        <th  scope="row">

  <label for="">Envato Theme HTML</label> </th><td>

        

		<textarea  name="envatothemeitemcode"  cols="100" rows="5" id="envatothemeitemcode"  ><?php if(get_option('envatothemeitemcode')==""){ ?>
		<h2>{themecount} {themetitle}  {themeprice} </h2>"
		<h4>{themecategory}</h4>"		
		<a title="{themetitle}" href="{thememoreinfourl}"><img class="alignleft" title="{themetitle}" alt="{themetitle}"  src="{previewimageurl}" width="" /></a><div style="clear:both;"></div>
		{themedescription}
		<p>&nbsp;</p>
		<a class="mini-butt red-buy" title="{themetitle} View Demo" href="{themedemourl}" rel="nofollow">View Demo</a><a class="mini-butt red-buy" title="{themetitle} More Info" href="{thememoreinfourl}" rel="nofollow">More Info</a>		
		<?php } else{  echo get_option('envatothemeitemcode');} ?></textarea>

	

 		<p class="description"> This the html that will be used to create the posts containing the themes. You can place and Adjust it as per your theme or preference.</p> 	   

        <p class="description">This is the HTML for Envato themes for Posts created.    
		
		<br />{themecount} is the serial number of the theme like 1, 2,3..		
		<br />{themetitle} for the Theme Name or Title 
		<br />{themedescription} for the Theme Description or content 		
		<br />{themedemourl} If the Demo Exists the code will be inserted here.
		
		<br />{thememoreinfourl} If the More Info Exists the code will be inserted here.
		
		<br />{previewimageurl} For the theme preview image url text here.	
	
		<br />{themeprice} for the Theme Price, if it was obtained. 
		
		<br />{themecategory} for the Theme Category, if it was obtained. 		
		
		
		</p>

               </td></tr> 	
			   
       <tr valign="top">

		

		<th scope="row"><label for="">Theme Content/ Description </label></th> <td>

       		<select name="endesccontains">

			<option value="DescriptionOnly" <?php if(get_option('endesccontains')==""){ echo "selected = \"selected\"";} ?>>Description Only</option>
			<option value="FeaturesListOnly" <?php if(get_option('endesccontains')=="FeaturesListOnly"){ echo "selected = \"selected\"";} ?>>Features List Only</option>
			<option value="DescriptionFeaturesList" <?php if(get_option('endesccontains')=="DescriptionFeaturesList"){ echo "selected = \"selected\"";} ?>>Description &amp; Features List</option>	
	<option value="Complete" <?php if(get_option('endesccontains')=="Complete"){ echo "selected = \"selected\"";} ?>>Complete</option>				
			</select>

	
        <p class="description">Description of each Theme or Product should contain description, features etc. </p>

       </td>

         </tr>
		 
		 		 <tr valign="top">

        <td  ></td><td>

        <input type="checkbox"  name="endesincimages" value="1" <?php if(get_option('endesincimages')=="1"){ echo "checked=\"checked\"";} ?> /><label>Description of each Theme or Product will contain images.</label>

       

               </td></tr>  
               
		 		 <tr valign="top">

        <td  ></td><td>

        <input type="checkbox"  name="endesincdemos" value="1" <?php if(get_option('endesincdemos')=="1"){ echo "checked=\"checked\"";} ?> /><label>Description of each Theme or Product will contain Demos.</label>

       

               </td></tr>   
			   
		 		 <tr valign="top">

        <td  ></td><td>

        <input type="checkbox"  name="endesincstrip" value="1" <?php if(get_option('endesincstrip')=="1"){ echo "checked=\"checked\"";} ?> /><label>Description of each Theme or Product will contain HTML.</label>

       

               </td></tr> 
			   
 

</tbody>

</table>

<?php submit_button('Submit', 'primary', 'save_optionsdata', '', '' );


 ?>

</form>

</div>

<?php 	
	}
	
function thse_register_my_session()
{
  if( !session_id() )
  {
    session_start();
  }
  thse_deleteLogs(7);
}

add_action('init', 'thse_register_my_session');

global $thse_db_version;
$thse_db_version = "1.1";

function thse_install(){
global $wpdb;
global $thse_db_version;	

thse_createdbtables();
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
add_option("thse_db_version", $thse_db_version);	
}

register_activation_hook(__FILE__, 'thse_install');


function thse_upgradedb(){
global $wpdb;
global $thse_db_version;	
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if (get_option('thse_db_version') != $thse_db_version) {
    // Execute  
    thse_updatetables_version1_1();
    // Then update the version value
    update_option('thse_db_version', $thse_db_version);
}
add_option("thse_db_version", $thse_db_version);	
}

register_activation_hook(__FILE__, 'thse_upgradedb');



	add_action( 'wp', 'thse_setup_envtraimg_schedule' );
	
	//register_activation_hook( __FILE__, 'thse_setup_envatoimgimport_schedule' );


/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function thse_setup_envtraimg_schedule() {
	global $wpdb;
		$limsarray = get_transient('thse_qimg');
		$schedule = (count($limsarray)<=10)? 'oneminute':((count($limsarray)<=30 && count($limsarray)>10)?'twominute':(count($limsarray)<=50 && count($limsarray)>30)?'fiveminute':'fifminute');
	if ( ! wp_next_scheduled( 'thse_envtraimg_event' )&& !(get_transient('thse_qimg')===FALSE) ) {
		wp_schedule_event( time(), $schedule, 'thse_envtraimg_event');
		
	}
	 if(wp_next_scheduled( 'thse_envtraimg_event' )&& get_transient('thse_qimg')===FALSE){
		$timestamp = wp_next_scheduled( 'thse_envtraimg_event' );
		wp_unschedule_event( $timestamp, 'thse_envtraimg_event' );
	}

}


/**
 * On the scheduled action hook, run a function.
 */
function thse_do_envtraimg() {
global $wpdb;
$error = '';
//mail('anupam.rekha@satyamtechnologies.net','hi',!(FALSE=== get_transient('thse_qimg')));

		if(!(FALSE=== get_transient('thse_qimg'))){
			$limsarray = get_transient('thse_qimg');
		$schedule = (count($limsarray)<=10)? '1':((count($limsarray)<=30 && count($limsarray)>10)?'2':(count($limsarray)<=50 && count($limsarray)>30)?'5':'15');
			$limit_st = $limsarray[0][0];
			$limit_ct = $limsarray[0][1];
		
			
		$error = updateAttach($limit_st, 10);
		
		
		if(!(FALSE=== get_transient('thse_qimg'))){
				//	mail('anupam.rekha@satyamtechnologies.net','hi',count(get_transient('thse_qimg')));
		$limsarray = get_transient('thse_qimg');
		array_shift($limsarray);
		
		delete_transient('thse_qimg');
		if(count($limsarray)>0){
		set_transient('thse_qimg', $limsarray);	
		}				
				
				}

	//	mail('anupam.rekha@satyamtechnologies.net',count($limsarray),count($limsarray));	

                				$error = esc_sql($error);
                                $logtitle = 'Total '.(10*count($limsarray)).' Sixthlife Envato Previews Cron '.$schedule.' Minute';
                                $query_log1 = "insert into ".$wpdb->prefix."envatologs (`title`, `message`) values('{$logtitle}', '{$error}')";
                                $result_log1 = $wpdb->query($query_log1);	
		
		
				if(get_option('sendcronresults')=='1')
			{
				add_filter( 'wp_mail_content_type', 'set_html_content_type' );
				wp_mail(get_option('emailresults'), $logtitle, $error);		
				remove_filter( 'wp_mail_content_type', 'set_html_content_type' );	
			}	
		}                       	
	
		}
//thse_do_envtraimg();
add_action( 'thse_envtraimg_event', 'thse_do_envtraimg' );


	add_action( 'wp', 'thse_setup_entursspdt_schedule' );
	
	//register_activation_hook( __FILE__, 'thse_setup_envatoimgimport_schedule' );




/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function thse_setup_entursspdt_schedule() {
	global $wpdb;
	$rssids = get_transient('thse_qrsspdt');
	
	$schedule = (count($rssids)<=50)? 'oneminute':((count($rssids)<=100 && count($rssids)>50)?'twominute':'fiveminute');
	if ( ! wp_next_scheduled( 'thse_entursspdt_event' )&& !(get_transient('thse_qrsspdt')===FALSE) ) {
		wp_schedule_event( time(), $schedule, 'thse_entursspdt_event');
		
	}
	 if(wp_next_scheduled( 'thse_entursspdt_event' )&& get_transient('thse_qrsspdt')===FALSE){
		$timestamp = wp_next_scheduled( 'thse_entursspdt_event' );
		wp_unschedule_event( $timestamp, 'thse_entursspdt_event' );
	}

}


/**
 * On the scheduled action hook, run a function.
 */
function thse_do_entursspdt() {
global $wpdb;
$error = ''; //delete_transient('thse_qrsspdt');delete_transient('thse_reachpdt'); echo 'dfds';
//mail('anupam.rekha@satyamtechnologies.net','hi',!(FALSE=== get_transient('thse_qimg')));

		if(!(FALSE=== get_transient('thse_qrsspdt'))){
			$rssids = get_transient('thse_qrsspdt');

		$schedule = (count($rssids)<=50)? '1':((count($rssids)<=100 && count($rssids)>50)?'2':'5');	
		//echo $schedule;
		$error .= updateRSSUrls(array($rssids[0]));
		
		$error .= productsRSS(array($rssids[0]));
		
		if(!(FALSE=== get_transient('thse_qrsspdt'))){
				//	mail('anupam.rekha@satyamtechnologies.net','hi',count(get_transient('thse_qimg')));
		$rssids = get_transient('thse_qrsspdt');
		array_shift($rssids);
		
		delete_transient('thse_qrsspdt');
		if(count($rssids)>0){
		set_transient('thse_qrsspdt', $rssids);	
		}				
				
				}



                                $logtitle = 'Remaining '.count($rssids).' Sixthlife Envato RSS Update Cron '.$schedule.' Minute';
                                $query_log1 = "insert into ".$wpdb->prefix."envatologs (`title`, `message`) values('{$logtitle}', '{$error}')";
                                $result_log1 = $wpdb->query($query_log1);	
		
			if(get_option('sendcronresults')=='1')
			{
				add_filter( 'wp_mail_content_type', 'set_html_content_type' );
				wp_mail(get_option('emailresults'), $logtitle, $error);		
				remove_filter( 'wp_mail_content_type', 'set_html_content_type' );	
			}
			//	mail('anupam.rekha@satyamtechnologies.net',count($rssids),$error);	
		}                       	
	
		}
//thse_do_entursspdt();
add_action( 'thse_entursspdt_event', 'thse_do_entursspdt' );



	add_action( 'wp', 'thse_setup_entueapdt_schedule' );
	
	//register_activation_hook( __FILE__, 'thse_setup_envatoimgimport_schedule' );




/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function thse_setup_entueapdt_schedule() {
	global $wpdb;
	if ( ! wp_next_scheduled( 'thse_entueapdt_event' )&& !(get_transient('thse_reachpdt')===FALSE) ) {
		wp_schedule_event( time(), 'oneminute', 'thse_entueapdt_event');
		
	}
	 if(wp_next_scheduled( 'thse_entueapdt_event' )&& get_transient('thse_reachpdt')===FALSE){
		$timestamp = wp_next_scheduled( 'thse_entueapdt_event' );
		wp_unschedule_event( $timestamp, 'thse_entueapdt_event' );
	}

}


/**
 * On the scheduled action hook, run a function.
 */
function thse_do_entueapdt() {
global $wpdb; //delete_transient('thse_reachpdt');
$error = '';
//mail('anupam.rekha@satyamtechnologies.net','hi',!(FALSE=== get_transient('thse_qimg')));

		if(!(FALSE=== get_transient('thse_reachpdt'))){
			$rssids = get_transient('thse_reachpdt');

	
		$error .= productsRSS(array($rssids[0]));
		
		if(!(FALSE=== get_transient('thse_reachpdt'))){
				//	mail('anupam.rekha@satyamtechnologies.net','hi',count(get_transient('thse_qimg')));
		$rssids = get_transient('thse_reachpdt');
		array_shift($rssids);
		
		delete_transient('thse_reachpdt');
		if(count($rssids)>0){
		set_transient('thse_reachpdt', $rssids);	
		}						
		}


                                $logtitle = 'Remaining '.count($rssids).' Sixthlife Envato RSS Product Cron 1 Minute';
                                $query_log1 = "insert into ".$wpdb->prefix."envatologs (`title`, `message`) values('{$logtitle}', '{$error}')";
                                $result_log1 = $wpdb->query($query_log1);	
                                
			if(get_option('sendcronresults')=='1')
			{
				add_filter( 'wp_mail_content_type', 'set_html_content_type' );
				wp_mail(get_option('emailresults'), $logtitle, $error);		
				remove_filter( 'wp_mail_content_type', 'set_html_content_type' );	
			}
			//mail('anupam.rekha@satyamtechnologies.net',count($rssids),$error);	
			}                       	
		}
//thse_do_entursspdt();
add_action( 'thse_entueapdt_event', 'thse_do_entueapdt' );

	add_action( 'wp', 'thse_setup_entnewrss_schedule' );
	
	//register_activation_hook( __FILE__, 'thse_setup_envatoimgimport_schedule' );




/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function thse_setup_entnewrss_schedule() {
	global $wpdb;
	$rssurls = get_transient('thse_frechrss');
	
	$schedule = (count($rssurls)<=20)? 'oneminute':((count($rssurls)<=50 && count($rssurls)>20)?'twominute':'fiveminute');
	if ( ! wp_next_scheduled( 'thse_entnewrss_event' )&& !(get_transient('thse_frechrss')===FALSE) ) {
		wp_schedule_event( time(), $schedule, 'thse_entnewrss_event');
		
	}
	 if(wp_next_scheduled( 'thse_entnewrss_event' )&& get_transient('thse_frechrss')===FALSE){
		$timestamp = wp_next_scheduled( 'thse_entnewrss_event' );
		wp_unschedule_event( $timestamp, 'thse_entnewrss_event' );
	}

}


/**
 * On the scheduled action hook, run a function.
 */
function thse_do_entnewrss() {
global $wpdb;
$error = ''; //delete_transient('thse_qrsspdt');delete_transient('thse_reachpdt'); echo 'dfds';
//mail('anupam.rekha@satyamtechnologies.net','hi',!(FALSE=== get_transient('thse_qimg')));

		if(!(FALSE=== get_transient('thse_frechrss'))){
			$rssurls = get_transient('thse_frechrss');

	$schedule = (count($rssurls)<=20)? '1':((count($rssurls)<=50 && count($rssurls)>20)?'2':'5');
		//echo $schedule;
			$insertrss = getNewRSS(trim($rssurls[0]));
					//echo $insertrss;
			if($insertrss === true){
				$error .=	$rssurls[0]. ' added successfully.<br />';
				}
			else if($insertrss === 'exists'){
				$error .= 	$rssurls[0] . ' already exists<br />';
				}
				else if($insertrss === 'empty'){
				$error .=	$rssurls[0]. ' rss content could not be fetched.<br />';	
				}
		
		if(!(FALSE=== get_transient('thse_frechrss'))){
				//	mail('anupam.rekha@satyamtechnologies.net','hi',count(get_transient('thse_qimg')));
		$rssurls = get_transient('thse_frechrss');
		array_shift($rssurls);
		
		delete_transient('thse_frechrss');
		if(count($rssurls)>0){
		set_transient('thse_frechrss', $rssurls);	
		}				
				
				}



                                $logtitle = 'Remaining '.count($rssurls).' Sixthlife Envato Fresh RSS Cron '.$schedule.' Minute';
                                $query_log1 = "insert into ".$wpdb->prefix."envatologs (`title`, `message`) values('{$logtitle}', '{$error}')";
                                $result_log1 = $wpdb->query($query_log1);	
		
			if(get_option('sendcronresults')=='1')
			{
				add_filter( 'wp_mail_content_type', 'set_html_content_type' );
				wp_mail(get_option('emailresults'), $logtitle, $error);		
				remove_filter( 'wp_mail_content_type', 'set_html_content_type' );	
			}
			//	mail('anupam.rekha@satyamtechnologies.net',count($rssids),$error);	
		}                       	
	
		}
//thse_do_entursspdt();
add_action( 'thse_entnewrss_event', 'thse_do_entnewrss' );


add_filter( 'cron_schedules', 'thse_add_onemin_cron_schedule' );

function thse_add_onemin_cron_schedule( $schedules ) {
	$schedules['oneminute'] = array(
		'interval' => 60, // 1 minute in seconds
		'display'  => __( 'One Minute' ),
	);

	return $schedules;
}

add_filter( 'cron_schedules', 'thse_add_twomin_schedule' );

function thse_add_twomin_schedule( $schedules ) {
	$schedules['twominute'] = array(
		'interval' => 120, // 2 minute in seconds
		'display'  => __( 'Two Minute' ),
	);

	return $schedules;
}

add_filter( 'cron_schedules', 'thse_add_fiveemin_schedule' );

function thse_add_fiveemin_schedule( $schedules ) {
	$schedules['fiveminute'] = array(
		'interval' => 300, // 5 minute in seconds
		'display'  => __( 'Five Minute' ),
	);

	return $schedules;
}

add_filter( 'cron_schedules', 'thse_add_fifmin_schedule' );

function thse_add_fifmin_schedule( $schedules ) {
	$schedules['fifminute'] = array(
		'interval' => 900, // 15 minute in seconds
		'display'  => __( 'Fifteen Minute' ),
	);

	return $schedules;
}

?>