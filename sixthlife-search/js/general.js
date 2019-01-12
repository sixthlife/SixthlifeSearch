      var XMLHttpRequestObject = false; 

      var XMLHttpRequestObject = false; 

      try { 
        XMLHttpRequestObject = new ActiveXObject("MSXML2.XMLHTTP"); 
         } catch (exception1) { 
         try { 
           XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP"); 
         } catch (exception2) { 
           XMLHttpRequestObject = false; 
       } 
     } 

     if (!XMLHttpRequestObject && window.XMLHttpRequest) { 
       XMLHttpRequestObject = new XMLHttpRequest(); 
     } 
     
     
function loadSelectors(baseurl){
	 var selecturl = document.getElementById('select_urls').value;
 	 var geturl = 	baseurl+'ajax.php?load_selector=true&rawdata_id='+selecturl;  
	  
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
          	if(response!=""){
              obj = JSON.parse(response);
               //   alert(response);

              document.getElementById('base_urltoattach').value = obj.baseurl;               	
               document.getElementById('base_affstring').value = obj.affstring;

               document.getElementById('default_cat').value = obj.defaultcat;
               document.getElementById('eachtheme_element').value = obj.ethemeelem;
               document.getElementById('eachtheme_url').value = obj.ethemeurl;
               document.getElementById('eachtheme_demo').value = obj.ethemedemo;

               document.getElementById('eachtheme_title').value = obj.ethemetitle;
				document.getElementById('eachtheme_price').value = obj.ethemeprice;
             

       			document.getElementById('eachtheme_preview').value = obj.ethemepreview; 
                  

			   document.getElementById('eachtheme_desc').value = obj.ethemedesc;
            
       			document.getElementById('eachtheme_cat').value = obj.ethemecat; 
            
			   document.getElementById('innertheme_demo').value = obj.ithemedemo;
       
               document.getElementById('innertheme_title').value = obj.ithemetitle;

               document.getElementById('innertheme_price').value = obj.ithemeprice; 
        
               document.getElementById('innertheme_preview').value = obj.ithemepreview; 

               document.getElementById('innertheme_desc').value = obj.ithemedesc;  

               document.getElementById('innertheme_cat').value = obj.ithemecat; 
		   
 
				              
               }
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}

function loadEmailSelectors(baseurl){
	 var selecturl = document.getElementById('emails_select_urls').value;
	 var starturlcount = document.getElementById('emails_start_from').value;
 	 var geturl = 	baseurl+'ajax.php?load_emailselector=true&rawdata_id='+selecturl+'&starturlcount='+starturlcount;  
	  
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
          	if(response!=""){
              obj = JSON.parse(response);
                //  alert(response);

              document.getElementById('emails_start_from').value = obj.start_from;               	
               document.getElementById('emails_start_to').value = obj.start_to;
  
               document.getElementById('emails_reci').value = obj.cemailrec_elem;
               document.getElementById('emails_cnamei').value = obj.cname_elem;
               document.getElementById('emails_cwebsitei').value = obj.cwebsite_elem;
             }
            } 
          } 
          XMLHttpRequestObject.send(null); 		
}

function checkTheme(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+obj.value+'&rawdata_id='+selecturl+'&themeonly=true';  
 	 	//	alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chk_theme').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}

function checkThemeurl(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themeurl='+obj.value+'&rawdata_id='+selecturl;  
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chk_themeurl').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}


function checkThemetitle(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themetitle='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chk_themetitle').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}

function checkThemeprice(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themeprice='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chk_themeprice').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}

function checkThemedemo(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themedemo='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chk_themedemo').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}

function checkThemepreview(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themepreview='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chk_themepreview').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}


function checkThemedescription(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themedescription='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chk_themedescription').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}


function checkThemecategory(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themecategory='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chk_themecategory').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}

function checkThemeinnerdemo(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
	 var check_themeurl = document.getElementById('eachtheme_url').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themeurl='+check_themeurl+'&chkinner_demo='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chkinner_demo').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}

function checkThemeinnertitle(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
	 var check_themeurl = document.getElementById('eachtheme_url').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themeurl='+check_themeurl+'&chkinner_title='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chkinner_title').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}


function checkThemeinnerprice(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
	 var check_themeurl = document.getElementById('eachtheme_url').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themeurl='+check_themeurl+'&chkinner_price='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chkinner_price').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}


function checkThemeinnerpreview(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
	 var check_themeurl = document.getElementById('eachtheme_url').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themeurl='+check_themeurl+'&chkinner_preview='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chkinner_preview').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}

function checkThemeinnerdescription(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
	 var check_themeurl = document.getElementById('eachtheme_url').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themeurl='+check_themeurl+'&chkinner_description='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //alert(response);
               document.getElementById('chkinner_description').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}

function checkThemeinnercategory(obj, baseurl){
	 var selecturl = document.getElementById('select_urls').value;
	 var check_theme = document.getElementById('eachtheme_element').value;
	 var check_themeurl = document.getElementById('eachtheme_url').value;
 	 var geturl = 	baseurl+'ajax.php?chk_theme='+check_theme+'&chk_themeurl='+check_themeurl+'&chkinner_category='+obj.value+'&rawdata_id='+selecturl; 
 	 		//alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //<b>alert</b>(response);
               document.getElementById('chkinner_category').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 	
}




function envatoParts(baseurl){
	//alert(baseurl);
	 var envatomktplc = document.getElementById('envatoth_mktplc').value;
	 var envatotitle = document.getElementById('envatokwd_title').value;
	 var notenvatotitle = document.getElementById('notenvatokwd_title').value;
	 	 envatotitle = envatotitle.replace("&", "-and-", 'g');
	 	 envatotitle = envatotitle.replace("|", "-or-", 'g'); 
	 	 notenvatotitle =  notenvatotitle.replace("&",  "-and-", 'g');
	 	 notenvatotitle = notenvatotitle.replace("&", "-and-");
	 var envtitlecont = document.getElementById('envtitlecont_cond').value;
	 var envcontcat = document.getElementById('envcontcat_cond').value;
	 var envatocat = document.getElementById('envatokwd_cat').value;
	 	 envatocat = envatocat.replace("&", "-and-",  'g');
	 	 envatocat = envatocat.replace("|", "-or-",  'g');
	 var envatopdtmax = document.getElementById('envatopdt_max').value;	 
	 
 	 var geturl = 	baseurl+'ajax.php?envato_part=true&envatomktplc='+envatomktplc+'&envatotitle='+envatotitle+'&notenvatotitle='+notenvatotitle+'&envtitlecont='+envtitlecont+'&envcontcat='+envcontcat+'&envatocat='+envatocat+'&envatopdtmax='+envatopdtmax; 
 	 	//	alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
              //<b>alert</b>(response);
               document.getElementById('envatotemp').innerHTML = response;
            } 
          } 
          XMLHttpRequestObject.send(null); 		
}




function removethistheme(elemid,market, baseurl){
	
	if(market=='envato'){
	str = 'itemenvato-'+elemid;		
	}

	element = document.getElementById(str);
	element.style.display = 'none';
	
 	 var geturl = 	baseurl+'?removethis=true&itemid='+elemid+'&marketpl='+market; 
 	 	//	alert(geturl);	
         	XMLHttpRequestObject.open("GET",geturl,false ); 
            XMLHttpRequestObject.onreadystatechange = function() 
          {  //alert(XMLHttpRequestObject.status);
            if (XMLHttpRequestObject.readyState == 4 && 
            XMLHttpRequestObject.status == 200) { 
              var response = XMLHttpRequestObject.responseText; 
             // alert(response);
             
            } 
          } 
          XMLHttpRequestObject.send(null); 	
	
	return false;
}

