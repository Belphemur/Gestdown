var xmlhttp
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
  try {
  xmlhttp=new ActiveXObject("Msxml2.XMLHTTP")
 } catch (e) {
  try {
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
  } catch (E) {
   xmlhttp=false
  }
 }
@else
 xmlhttp=false
@end @*/
if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
 try {
  xmlhttp = new XMLHttpRequest();
 } catch (e) {
  xmlhttp=false
 }
}
function myXMLHttpRequest() {
  var xmlhttplocal;
  try {
    xmlhttplocal= new ActiveXObject("Msxml2.XMLHTTP")
 } catch (e) {
  try {
    xmlhttplocal= new ActiveXObject("Microsoft.XMLHTTP")
  } catch (E) {
    xmlhttplocal=false;
  }
 }

if (!xmlhttplocal && typeof XMLHttpRequest!='undefined') {
 try {
  var xmlhttplocal = new XMLHttpRequest();
 } catch (e) {
  var xmlhttplocal=false;
  alert('couldn\'t create xmlhttp object');
 }
}
return(xmlhttplocal);
}


function sndRequest(vote,id_num,ip_num) {
	if(vote>5 || vote <1)
	{
		var element = document.getElementById('unit_long'+id_num);
		//new Effect.Fade(element);
		element.innerHTML = '<div style="height: 20px;"><em>Désolé cette valeur est impossible.</em></div>';
	}
	else
	{
		var element = document.getElementById('unit_long'+id_num);
		//new Effect.Fade(element);
		element.innerHTML = '<div style="height: 20px;"><em>Loading ...</em></div>';
		
		xmlhttp.open('get', 'includes/rpc.php?j='+vote+'&q='+id_num+'&t='+ip_num);
		xmlhttp.onreadystatechange = handleResponse;
		xmlhttp.send(null);
	}
	
}

function handleResponse() {
    if(xmlhttp.readyState == 4){
		if (xmlhttp.status == 200){
       	
        var response = xmlhttp.responseText;
        var update = new Array();

        if(response.indexOf('|') != -1) {
            update = response.split('|');
            changeText(update[0], update[1]);
        }
		}
    }
}

function changeText( div2show, text ) {
    // Detect Browser
    var IE = (document.all) ? 1 : 0;
    var DOM = 0; 
    if (parseInt(navigator.appVersion) >=5) {DOM=1};

    // Grab the content from the requested "div" and show it in the "container"

    if (DOM) {
        var viewer = document.getElementById(div2show)
        viewer.innerHTML=text
    }
    else if(IE) {
        document.all[div2show].innerHTML=text
    }
}
