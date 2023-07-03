Ext.require([
    '*'
]);
var mapa;
var ciudad = "";
var tipoUbicacion = "";
var login = "";
var flagLoginCorrecto = 0;
var markerPto ;


function soloNumeros(evt) {
    evt = (evt) ? evt : window.event
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        alert("Solo debe ingresar numeros.");
        return false
    }
}

function uneValoresCoordenadas(){
    var latitud = 0;
    var longitud = 0;
    latitud = $("#grados_la").val();
    latitud = latitud + "-";
    latitud = $("minutos_la").val();
    latitud = latitud + "-";
    latitud = $("segundos_la").val();
    latitud = latitud + "-";
    latitud = $("decimas_segundos_la").val();
    latitud = latitud +"-";
    latitud = $("latitud").val();

    longitud = $("grados_lo").val();
    longitud = longitud + "-";
    longitud = $("minutos_lo").val();
    longitud = longitud + "-";
    longitud = $("segundos_lo").val();
    longitud = longitud + "-";
    longitud = $("decimas_segundos_lo").val();
    longitud = longitud + "-";
    longitud = $("longitud").val();
    $("decimas_segundos_lo")	
    
    coordenadas = latitud + ";" + longitud;
    
}


function validacionesEditForm()
{
    //validar nombre NodoCliente
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_nombreElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
       if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_cantonId").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_parroquiaId").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_descripcionElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
        
    if(document.getElementById("direccionUbicacion").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_modeloElementoId").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("alturaSnm").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("longitudUbicacion").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("latitudUbicacion").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    return true;
}


function validacionesForm(){

    //validar nombre NodoCliente
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_nombreElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
   
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_cantonId").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_parroquiaId").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_descripcionElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
        
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_direccionUbicacion").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_modeloElementoId").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementonodoclientetype_alturaSnm").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
        
    //funciiones para validar las coordenadas
    if(!validarGradosNuevo(document.forms[0].grados_la.value,1))
        return false;

    if(!validarMinutosNuevo(document.forms[0].minutos_la.value,1))
        return false;

    if(!validarSegundosNuevo(document.forms[0].segundos_la.value,1))
        return false;

    if(!validarDecimasSegundosNuevo(document.forms[0].decimas_segundos_la.value,1))
        return false;

    if(!validarGradosNuevo(document.forms[0].grados_lo.value,2))
        return false;

    if(!validarMinutosNuevo(document.forms[0].minutos_lo.value,2))
        return false;

    if(!validarSegundosNuevo(document.forms[0].segundos_lo.value,2))
        return false;

    if(!validarDecimasSegundosNuevo(document.forms[0].decimas_segundos_lo.value,2))
        return false;

    if(document.forms[0].latitud[document.forms[0].latitud.selectedIndex].value=='T')
    {
        alert('Ingrese la latitud (Norte/Sur)');
        return false;
    }

     if(document.forms[0].latitud[document.forms[0].longitud.selectedIndex].value=='T')
    {
        alert('Ingrese la longitud (Este/Oeste)');
        return false;
    }

    if(!validarCoordenadasPorPaisForm(document.forms[0], "telconet_schemabundle_infoelementonodoclientetype"))
        return false;
    
    return true;
}

//funciones para los grados del punto
function validaInspeccion(bandera){ 
	if(bandera==1){ 
		validarGrados(document.forms[0].grados_la.value,bandera);
		validarMinutos(document.forms[0].minutos_la.value,bandera);
		validarSegundos(document.forms[0].segundos_la.value,bandera);
		validarDecimasSegundos(document.forms[0].decimas_segundos_la.value,bandera);
	}else{
		validarGrados(document.forms[0].grados_lo.value,bandera);
		validarMinutos(document.forms[0].minutos_lo.value,bandera);
		validarSegundos(document.forms[0].segundos_lo.value,bandera);
		validarDecimasSegundos(document.forms[0].decimas_segundos_lo.value,bandera);
	}
}

function validarGrados(caracter,bandera){ 
    if(bandera==1){   
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Grados- Ingrese solo numeros');
        }else{
               //valido que sea numero entre 0-360  
            if(parseInt(caracter)<0 || parseInt(caracter)>360){
                alert('Grados- Ingrese solo numeros entre 0-360');
            }
        }
    }else{
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Grados- Ingrese solo numeros');
        }else{
               //valido que sea numero entre 0-360  
            if(parseInt(caracter)<0 || parseInt(caracter)>360){
                alert('Grados- Ingrese solo numeros entre 0-360');
            }
        }
    }
}

function validarMinutos(caracter,bandera){
    if(bandera==1){
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Minutos- Ingrese solo numeros');
        }else{
             //valido que sea numero entre 0-59  
            if(parseInt(caracter)<0 || parseInt(caracter)>59){
                alert('Minutos- Ingrese solo numeros entre 0-59');
            }
        }
    }else{
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Minutos- Ingrese solo numeros');
        }else{
             //valido que sea numero entre 0-59  
            if(parseInt(caracter)<0 || parseInt(caracter)>59){
                alert('Minutos- Ingrese solo numeros entre 0-59');
            }
        }
    }
}	
	
function validarSegundos(caracter,bandera){
    if(bandera==1){
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Segundos- Ingrese solo numeros');
        }else{
           //valido que sea numero entre 0-59  
            if(parseInt(caracter)<0 || parseInt(caracter)>59){
                alert('Segundos- Ingrese solo numeros entre 0-59');
            }
        }
    }else{
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Segundos- Ingrese solo numeros');
        }else{
                 //valido que sea numero entre 0-59  
            if(parseInt(caracter)<0 || parseInt(caracter)>59){
                alert('Segundos- Ingrese solo numeros entre 0-59');
            }
        }
    }
}	

/*
 * @version 1.0 Inicial
 * 
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>       
 * @version 1.1 29-09-2016 Se valida que las decimas de segundo ingresen caracteres de entre
 *                         0-999.
 * 
 */
function validarDecimasSegundos(caracter,bandera){
    if(bandera==1){
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Decimas Segundos- Ingrese solo numeros');
        }else{
            //valido que sea numero entre 0-999  
            if(parseInt(caracter)<0 || parseInt(caracter)>999){
                alert('Decimas Segundos- Ingrese solo numeros entre 0-999');
            }
        }
    }else{
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Decimas Segundos- Ingrese solo numeros');
        }else{
            //valido que sea numero entre 0-999  
            if(parseInt(caracter)<0 || parseInt(caracter)>999){
                alert('Decimas Segundos- Ingrese solo numeros entre 0-999');
            }
        }
    }
}	
	
function validarGradosNuevo(caracter,bandera){
    if(bandera==1){
        if(caracter==''){
            alert('Ingrese los grados por favor');
            return false;
        }
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Grados- Ingrese solo numeros');
            return false;
        }else{
            //valido que sea numero entre 0-360
            if(parseInt(caracter)<0 || parseInt(caracter)>360){
                alert('Grados- Ingrese solo numeros entre 0-360');
                return false;
            }
        }
    }else{
        if(caracter==''){
            alert('Ingrese los grados por favor');
            return false;
        }
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Grados- Ingrese solo numeros');
            return false;
        }else{
            //valido que sea numero entre 0-360
            if(parseInt(caracter)<0 || parseInt(caracter)>360){
                alert('Grados- Ingrese solo numeros entre 0-360');
                return false;
            }
        }
    }
    return true;
}

function validarMinutosNuevo(caracter,bandera){
    if(bandera==1){
        if(caracter==''){
            alert('Ingrese los minutos por favor');
            return false;
        }
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Minutos- Ingrese solo numeros');
            return false;
        }else{
            //valido que sea numero entre 0-59
            if(parseInt(caracter)<0 || parseInt(caracter)>59){
                alert('Minutos- Ingrese solo numeros entre 0-59');
                return false;
            }
        }
    }else{
        if(caracter==''){
            alert('Ingrese los minutos por favor');
            return false;
        }
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Minutos- Ingrese solo numeros');
            return false;
        }else{
            //valido que sea numero entre 0-59
            if(parseInt(caracter)<0 || parseInt(caracter)>59){
                alert('Minutos- Ingrese solo numeros entre 0-59');
                return false;
            }
        }
    }
    return true;
}

function validarSegundosNuevo(caracter,bandera){
    if(bandera==1){
        if(caracter==''){
            alert('Ingrese los segundos por favor');
            return false;
        }
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Segundos- Ingrese solo numeros');
            return false;
        }else{
            //valido que sea numero entre 0-59
            if(parseInt(caracter)<0 || parseInt(caracter)>59){
                alert('Segundos- Ingrese solo numeros entre 0-59');
                return false;
            }
        }
    }else{
        if(caracter==''){
            alert('Ingrese los segundos por favor');
            return false;
        }
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Segundos- Ingrese solo numeros');
            return false;
        }else{
            //valido que sea numero entre 0-59
            if(parseInt(caracter)<0 || parseInt(caracter)>59){
                alert('Segundos- Ingrese solo numeros entre 0-59');
                return false;
            }
        }
    }
    return true;
}

/*
 * @version 1.0 Inicial
 * 
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>       
 * @version 1.1 29-09-2016 Se valida que las decimas de segundo ingresen caracteres de entre
 *                         0-999.
 * 
 */
function validarDecimasSegundosNuevo(caracter,bandera){
    if(bandera==1){
        if(caracter==''){
            alert('Ingrese las decimas de segundos por favor');
            return false;
        }
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Decimas Segundos- Ingrese solo numeros');
            return false;
        }else{
            //valido que sea numero entre 0-999
            if(parseInt(caracter)<0 || parseInt(caracter)>999){
                alert('Decimas Segundos- Ingrese solo numeros entre 0-999');
                return false;
            }
        }
    }else{
         if(caracter==''){
            alert('Ingrese las decimas de segundos por favor');
            return false;
        }
        if(/[^\d]/.test(caracter) || caracter==''){
            alert('Decimas Segundos- Ingrese solo numeros');
            return false;
        }else{
            //valido que sea numero entre 0-99
            if(parseInt(caracter)<0 || parseInt(caracter)>999){
                alert('Decimas Segundos- Ingrese solo numeros entre 0-999');
                return false;
            }
        }
    }
    return true;
}

function exist (a) {
    try { 
        var b=a;
    }
    catch(e){
        return false;
    }
    
    return true;
} // exist

/*
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>       
 * @version 1.1 23-09-2016
 * @since   1.0
 * Se añade los centesima de segundo a los segundos concatenando dicho valor como decimales y convirtiendo la cadena a representación Float.
 */
function gms2dec(grados, minutos, segundos,decimas_segundos, direccion)
{
    segundos = parseFloat(segundos+'.'+decimas_segundos);
    
    if(direccion)
    {
        signo     = (direccion.toLowerCase() == 'o' ||
                     direccion.toLowerCase() == 's') ?
                    -1 : 1;
        direccion = (direccion.toLowerCase() == 'o' ||
                     direccion.toLowerCase() == 's' ||
                     direccion.toLowerCase() == 'n' ||
                     direccion.toLowerCase() == 'e') ?
                    direccion.toLowerCase() : '';
    }
    else
    {
        signo     = (grados < 0) ? -1 : 1;
        direccion = '';
    }

    dec = Math.abs(grados) + (minutos / 60) + (segundos/3600);
    dec = +dec.toFixed(6);
    if(isNaN(direccion) || direccion == '')
        dec = dec * signo;
    return dec;
}

/**
 * @version 1.0 Inicial
 * 
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>
 * @version 1.1 29-09-2016 - Se considera los milisegundos para realizar el calculo
 *                           de los puntos de latitud y longitud
 * 
 * @param String lat
 * @param String lng
 */
function dd2dms(lat,lng) {            
  lat = lat.toString();
  lng = lng.toString();
    var d = document;
	var dmsLatDeg = d.getElementById("grados_la");
	var dmsLatMin = d.getElementById("minutos_la");
	var dmsLatSec = d.getElementById("segundos_la");
	var dmsLatDecSec = d.getElementById("decimas_segundos_la");
	var dmsLatHem = d.getElementById("latitud");
	
	var dmsLongDeg = d.getElementById("grados_lo");
	var dmsLongMin = d.getElementById("minutos_lo");
	var dmsLongSec = d.getElementById("segundos_lo");
	var dmsLongDecSec = d.getElementById("decimas_segundos_lo");
	var dmsLongHem = d.getElementById("longitud");
		
	if (lat.substr(0,1) == "-") {
		dmsLatHem.value = 'S';
		ddLatVal = lat.substr(1,lat.length-1);
	} else {
		dmsLatHem.value = 'N';
		ddLatVal = lat;
	}
	
	if (lng.substr(0,1) == "-") {
		dmsLongHem.value = 'O';
		ddLongVal = lng.substr(1,lng.length-1);
	} else {
		dmsLongHem.value = 'E';
		ddLongVal = lng;
	}
	
	// degrees = degrees
	ddLatVals = ddLatVal.split(".");
	dmsLatDeg.value = ddLatVals[0];
	
	ddLongVals = ddLongVal.split(".");
	dmsLongDeg.value = ddLongVals[0];
	
	// * 60 = mins
	ddLatRemainder  = ("0." + ddLatVals[1]) * 60;
	dmsLatMinVals   = ddLatRemainder.toString().split(".");
	dmsLatMin.value = dmsLatMinVals[0];
	
	ddLongRemainder  = ("0." + ddLongVals[1]) * 60;
	dmsLongMinVals   = ddLongRemainder.toString().split(".");
	dmsLongMin.value = dmsLongMinVals[0];
	
	// * 60 again = secs
	ddLatMinRemainder = ("0." + dmsLatMinVals[1]) * 60;
	dmsLatSec.value   = Math.round(ddLatMinRemainder);
	
	ddLongMinRemainder = ("0." + dmsLongMinVals[1]) * 60;
	dmsLongSec.value   = Math.round(ddLongMinRemainder);
	
	//Se extraen los decimales de los segundos para definir 3 posiciones de los milisegundos
    ddLatValsMl        = ddLatMinRemainder.toString().split(".");
    dmsLatDecSec.value = ddLatValsMl[1].substring(0, 3);

    ddLongValsMl        = ddLongMinRemainder.toString().split(".");
    dmsLongDecSec.value = ddLongValsMl[1].substring(0, 3);
    
    $("#telconet_schemabundle_infoelementonodoclientetype_latitudUbicacion").val(lat);
    $("#telconet_schemabundle_infoelementonodoclientetype_longitudUbicacion").val(lng);      
}
	
function validarCoordenadasEcuador(formulario)
  {
      grados_la_nuevos = parseFloat(formulario.grados_la.value);
      minutos_la_nuevos = parseFloat(formulario.minutos_la.value);
      segundo_la_nuevos = parseFloat(formulario.segundos_la.value);
      decimas_la_nuevos = parseFloat(formulario.decimas_segundos_la.value);
      latitud_la_nuevo = formulario.latitud[formulario.latitud.selectedIndex].value;

      valor_latitud = gms2dec(grados_la_nuevos,minutos_la_nuevos,segundo_la_nuevos,decimas_la_nuevos,latitud_la_nuevo);

      grados_lo_nuevos = parseFloat(formulario.grados_lo.value);
      minutos_lo_nuevos = parseFloat(formulario.minutos_lo.value);
      segundo_lo_nuevos = parseFloat(formulario.segundos_lo.value);
      decimas_lo_nuevos = parseFloat(formulario.decimas_segundos_lo.value);
      longitud_lo_nuevo = formulario.longitud[formulario.longitud.selectedIndex].value;

      valor_longitud = gms2dec(grados_lo_nuevos,minutos_lo_nuevos,segundo_lo_nuevos,decimas_lo_nuevos,longitud_lo_nuevo);
      //alert(valor_latitud);
      //alert(valor_longitud);
     if(!(valor_latitud >= parseFloat(-5.036) && valor_latitud <= parseFloat(1.40)))
         {
             alert('Debe ingresar la latitud en el rango de Ecuador');
              return false;
          }
     if(!(valor_longitud >= parseFloat(-95) && valor_longitud <= parseFloat(-75.25)))
         {
            alert('Debe ingresar la longitud en el rango de Ecuador');
             
              return false;
          }
    
    $("#telconet_schemabundle_infoelementonodoclientetype_latitudUbicacion").val(valor_latitud);
    $("#telconet_schemabundle_infoelementonodoclientetype_longitudUbicacion").val(valor_longitud);
          
      return true;
  }

function muestraMapa(){
		var latlng = new google.maps.LatLng(-2.160703,-79.897525);
		//var latlng = new google.maps.LatLng(-2.176963, -79.883673);
		 
		var myOptions = {
			zoom: 14,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}

        
		if(mapa){
			mapa.setCenter(latlng);
		}else{
			mapa = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		}
				
		if(ciudad=="gye")
		  layerCiudad = 'http://157.100.3.122/Coberturas.kml';
		else
		  layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';
		  	
		google.maps.event.addListener(mapa, 'dblclick', function(event) {
		  
		  if(markerPto)
		      markerPto.setMap(null);
		   
		     markerPto = new google.maps.Marker({
			position: event.latLng, 
			map: mapa
		      });
		   mapa.setZoom(17);
		   

		  dd2dms(event.latLng.lat(),event.latLng.lng());


		});
              
	winLista = Ext.widget('window', {
                title: 'Mapa',
                closeAction: 'hide',
                width: 510,
                height:380,
                minHeight: 380,
                layout: 'fit',
                resizable: false,
                modal: true,
		closabled: false,
                contentEl: 'map_canvas'
    }); 
    winLista.show();
    }
    
    //muestraMapa();
