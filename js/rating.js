var xmlhttp
if(!xmlhttp&&typeof XMLHttpRequest!='un\u0064e\u0066i\u006eed'){try{xmlhttp=new XMLHttpRequest()}catch(e){xmlhttp=false}}function myXMLHttpRequest(){var xmlhttplocal;try{xmlhttplocal=new ActiveXObject("Ms\u0078ml2.\u0058\115\u004c\110TTP")}catch(e){try{xmlhttplocal=new ActiveXObject("Mi\u0063r\u006fs\u006fft\u002e\u0058MLH\124\124P")}catch(E){xmlhttplocal=false}}if(!xmlhttplocal&&typeof XMLHttpRequest!='u\156defin\u0065d'){try{var xmlhttplocal=new XMLHttpRequest()}catch(e){var xmlhttplocal=false;alert('co\165ld\u006e\'t cr\u0065\141te x\u006d\154h\164\164p ob\152ec\164')}}return(xmlhttplocal)}function sndRequest(vote,id_num,ip_num){if(vote>5||vote<1){var element=document.getElementById('unit_l\u006f\156g'+id_num);element.innerHTML='<d\u0069v \163t\171\154e\075\u0022heigh\u0074: 2\060px;">\074e\u006d>Dé\u0073o\154é\040cet\u0074e vale\165\u0072 \145\u0073\164\040impo\u0073\163i\142l\145\056\074\u002fem>\u003c/div>'}else{var element=document.getElementById('un\151t\u005f\u006co\156g'+id_num);element.innerHTML='\074di\u0076 s\u0074\171\u006ce="h\u0065ight:\0402\u0030\u0070x;\u0022><e\155>Lo\u0061\144\151\u006eg \056\056\u002e\u003c/e\155\u003e<\057\u0064i\166>';xmlhttp.open('\u0067e\u0074','in\u0063lu\144\145s\u002f\u0072p\u0063\u002ephp?\152\u003d'+vote+'&\161\075'+id_num+'&\u0074='+ip_num);xmlhttp.onreadystatechange=handleResponse;xmlhttp.send(null)}}function handleResponse(){if(xmlhttp.readyState==4){if(xmlhttp.status==200){var response=xmlhttp.responseText;var update=new Array();if(response.indexOf('|')!=-1){update=response.split('\174');changeText(update[0],update[1])}}}}function changeText(div2show,text){var IE=(document.all)?1:0;var DOM=0;if(parseInt(navigator.appVersion)>=5){DOM=1};if(DOM){var viewer=document.getElementById(div2show);viewer.innerHTML=text}else if(IE){document.all[div2show].innerHTML=text}}