/**
 * tethys_ajax('.../ajax.php?cmd=...',"alert(response);");
 */
function tethys_ajax(query,Funktion){
	var xmlhttp=new XMLHttpRequest();
	xmlhttp.open("GET",query,true);
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState===4 && xmlhttp.status===200){
			if (Funktion){
				new Function("response",Funktion)(xmlhttp.responseText);
			}
		}
	};
	xmlhttp.send();
}

function tethys_ajax_to_id(query,id){
	tethys_ajax(query, "document.getElementById('"+id+"').innerHTML=response;");
}
