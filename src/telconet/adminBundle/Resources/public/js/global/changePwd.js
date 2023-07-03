/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 
 window.onload = function ()
{
	var sso_jquery = jQuery.noConflict();
	sso_jquery(document).ready(function() {  
		disableCopyPaste(document.getElementById('nueva_clave'));
			disableCopyPaste(document.getElementById('nueva_clave_2'));
	});	
} 


 function validarForm(){
    var pass1=document.getElementById('nueva_clave');
	var pass2=document.getElementById('nueva_clave_2');
    var codigo1=document.getElementById('codigo_verificacion');
	var codigo2=document.getElementById('codigo_confirmacion');
	var pass1_info = document.getElementById('error');
	pass1_info.style.display = "";
	
	if(pass1.value!=pass2.value){
		pass1_info.innerHTML='Las nuevas contrase&ntilde;as ingresadas no coinciden';
		return false;
	}
	
	if(pass1.value.length<8 || pass2.value.length<8 ){
		pass1_info.innerHTML='La contrase&ntilde;a debe tener al menos 8 caracteres de longitud';		
		return false;
	}
	
	if(pass1.value.length>15 || pass2.value.length>15 ){
		pass1_info.innerHTML='La contrase&ntilde;a debe tener a lo sumo 15 caracteres de longitud';		
		return false;
	}
       
    if(document.getElementById('codigo_confirmacion').value.length == 0){
		pass1_info.innerHTML='Debe ingresar el codigo de verificacion';		
		return false;
	}  
	
	if(codigo1.value!=codigo2.value){
		pass1_info.innerHTML='La verificacion del codigo ingresadas no coinciden';
		return false;
	}
	
	if(!isPasswordCorrect()){
		return false;
	}

	document.forms[0].submit();
 }
 function cancelar(){
     location.href="/administracion/welcome";
 }
 
function disableCopyPaste(elm) {
    // Disable cut/copy/paste key events
    elm.onkeydown = interceptKeys

    // Disable right click events
    elm.oncontextmenu = function() {
         alert("Favor tipear la clave");
        return false
    }
}

function interceptKeys(evt) {
    evt = evt||window.event // IE support
    var c = evt.keyCode
    var ctrlDown = evt.ctrlKey||evt.metaKey // Mac support

    // Check for Alt+Gr 
    if (ctrlDown && evt.altKey) return true
    // Check for ctrl+c, v and x
   // else if (ctrlDown && c==67) return false // c
    else if (ctrlDown && c==86){alert("Favor tipear la clave");return false;} // v
    //else if (ctrlDown && c==88) return false // x
    
    return true
}

function isPasswordCorrect(){
	var r1= new RegExp("[a-z]");  //lowercase    
	var r2=new RegExp("[A-Z]");  //Uppercase
	var r3=new RegExp("[0-9]");  //numbers
	var r4=new RegExp("[Ã¡Ã©Ã­Ã³ÃºÃ±Ã‘]");  // whatever you mean by 'special char'

	var cadena = document.getElementById('nueva_clave').value;
	var pass1_info = document.getElementById('error');
	var validez =true;
	var msg= "";

	pass1_info.style.display = "";
	
	if(!(cadena.length>=8) && (cadena.length<15)){       
       msg= 'La contrasena  debe tener minimo 8 caracteres y maximo 15 caracteres' + "</br>";
       pass1_info.innerHTML=msg;     
       validez = false;
	}
	
	if( !r1.test(cadena)){
       msg =msg + 'La contrasena  debe contener valores en minusculas' + "</br>";
       pass1_info.innerHTML= msg;       
       validez = false;
	}
	
	if(!r2.test(cadena)){
       msg =msg + 'La contrasena  debe contener valores en mayusculas' + "</br>";
       pass1_info.innerHTML=msg;
       
       validez = false;       
	}
	
	if(!r3.test(cadena)){
       msg =msg + 'La contrasena  debe contener valores numericos' + "</br>";
       pass1_info.innerHTML=msg;
       
       validez = false;       
	}
	
	if(!haveEspecialChar(cadena)){
       msg =msg + 'La contrasena  debe contener los caracteres especiales' + "</br>";
       pass1_info.innerHTML=msg;       
       validez = false;       
	}
    
	if(!dontHaveEspecialChar(cadena)){
       msg =msg + 'La contrasena no debe contener los caracteres especiales no permitidos' + "</br>";
       pass1_info.innerHTML=msg;       
       validez = false;       
	}
	
	if(r4.test(cadena)){
       msg =msg + 'La contrasena no debe incluir &ntilde; ni tildes' + "</br>";
       pass1_info.innerHTML=msg;       
       validez = false;       
	}
    
	if(validez){
		pass1_info.innerHTML="";
		pass1_info.style.display = "none";
	}
    
	return validez;    
}

function haveEspecialChar(cadena){ 
    var array = ["!","~", "#", "*", "_", "-", "+", "=", "{", "[", "}", "]", "|", "\\", ";", ":", "<", ",", ">", ".", "?", "/"];
	
    var array_char= cadena.split("");
    var validez =false;
    for(var i in array_char){
        if(jQuery.inArray(array_char[i], array)!=-1 ){
            validez=true;
            break;
        }
    }    
	
	return validez;
}

function dontHaveEspecialChar(cadena){ 
    var arrayNo = ["@", "'", "&", "^", "~", "¨", "`", "´", "°", "¬", "%", "¡", "¿"];
	
    var array_char= cadena.split("");
    var validez = true;
	
    for(var i in array_char){
        if(jQuery.inArray(array_char[i], arrayNo)!=-1 ){
            validez=false;
            break;
        }
    }
	
    return validez;
}