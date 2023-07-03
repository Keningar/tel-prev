Ext.require([
    '*'
]);
var mapa;
var ciudad            = "";
var tipoUbicacion     = "";
var login             = "";
var flagLoginCorrecto = 0;
var markerPto ;


function gms2dec(grados, minutos, segundos,decimas_segundos, direccion)
{
    segundos = parseFloat(segundos+'.'+decimas_segundos);

    if(direccion)
    {
        signo     = (direccion.toLowerCase() == 'oeste' ||
                     direccion.toLowerCase() == 'sur') ?
                    -1 : 1;
        direccion = (direccion.toLowerCase() == 'oeste' ||
                     direccion.toLowerCase() == 'sur' ||
                     direccion.toLowerCase() == 'norte' ||
                     direccion.toLowerCase() == 'este') ?
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

function dd2dms(lat,lng) {            
    
    lat = lat.toString();
    lng = lng.toString();

    if (lat.substr(0, 1) == "-") {
        ddLatVal = lat.substr(1, lat.length - 1);
        objCmbSeleccionLatitud.setValue('SUR');
    } else {
        ddLatVal = lat;
        objCmbSeleccionLatitud.setValue('NORTE');
    }

    if (lng.substr(0, 1) == "-") {
        ddLongVal = lng.substr(1, lng.length - 1);
        objCmbSeleccionLongitud.setValue('OESTE');
    } else {
       ddLongVal = lng;
       objCmbSeleccionLongitud.setValue('ESTE');
    }
    
    // Grados
    ddLatVals = ddLatVal.split(".");
    objTxtLatitud.setValue(ddLatVals[0]);    
    
    ddLongVals = ddLongVal.split(".");
    objTxtLongitud.setValue(ddLongVals[0]);

    // * 60 = mins
    ddLatRemainder  = ("0." + ddLatVals[1]) * 60;
    dmsLatMinVals   = ddLatRemainder.toString().split(".");
    objTxtLatitudGrados.setValue(dmsLatMinVals[0]);

    ddLongRemainder  = ("0." + ddLongVals[1]) * 60;
    dmsLongMinVals   = ddLongRemainder.toString().split(".");
    objTxtLongitudGrados.setValue(dmsLongMinVals[0]);

    // * 60 again = secs
    ddLatMinRemainder = ("0." + dmsLatMinVals[1]) * 60;
    objTxtLatitudMinutos.setValue(Math.round(ddLatMinRemainder));

    ddLongMinRemainder = ("0." + dmsLongMinVals[1]) * 60;
    objTxtLongitudMinutos.setValue(Math.round(ddLongMinRemainder));

    //Se extraen los decimales de los segundos para definir 3 posiciones de los milisegundos
    ddLatValsMl        = ddLatMinRemainder.toString().split(".");
    objTxtLatitudDecimales.setValue(ddLatValsMl[1].substring(0, 3));

    ddLongValsMl        = ddLongMinRemainder.toString().split(".");
    objTxtLongitudDecimales.setValue(ddLongValsMl[1].substring(0, 3));
    
}

function validarCoordenadasEcuador()
{
    grados_la_nuevos  = parseFloat(objTxtLatitud.getValue());
    minutos_la_nuevos = parseFloat(objTxtLatitudGrados.getValue());
    segundo_la_nuevos = parseFloat(objTxtLatitudMinutos.getValue());
    decimas_la_nuevos = parseFloat(objTxtLatitudDecimales.getValue());
    latitud_la_nuevo  = objCmbSeleccionLatitud.getValue();

    valor_latitud = gms2dec(grados_la_nuevos,minutos_la_nuevos,segundo_la_nuevos,decimas_la_nuevos,latitud_la_nuevo);

    grados_lo_nuevos  = parseFloat(objTxtLongitud.getValue());
    minutos_lo_nuevos = parseFloat(objTxtLongitudGrados.getValue());
    segundo_lo_nuevos = parseFloat(objTxtLongitudMinutos.getValue());
    decimas_lo_nuevos = parseFloat(objTxtLongitudDecimales.getValue());
    longitud_lo_nuevo = objCmbSeleccionLongitud.getValue();
    
    valor_longitud = gms2dec(grados_lo_nuevos,minutos_lo_nuevos,segundo_lo_nuevos,decimas_lo_nuevos,longitud_lo_nuevo);
    
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
    
    objTxtLatitudUbicacion.setValue(valor_latitud);
    objTxtLongitudUbicacion.setValue(valor_longitud);
          
    return true;
  }
	
function muestraMapa(){
    var latlng = new google.maps.LatLng(-2.160703,-79.897525);

    var myOptions = {
        zoom:      14,
        center:    latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    if(mapa){
        mapa.setCenter(latlng);
    }
    else{
        mapa = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    }

    if(ciudad=="gye")
        layerCiudad = 'http://157.100.3.122/Coberturas.kml';
    else
        layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';

    google.maps.event.addListener(mapa, 'dblclick', function(event) {
        if(markerPto)
            markerPto.setMap(null);
            markerPto =  new google.maps.Marker({
                position: event.latLng, 
                map: mapa
            });
            mapa.setZoom(17);
            dd2dms(event.latLng.lat(),event.latLng.lng());
            validarCoordenadasPorPaisMapa();
    });
    winLista = Ext.widget('window', {
                title:       'Mapa',
                closeAction: 'hide',
                width:       510,
                height:      380,
                minHeight:   380,
                layout:      'fit',
                resizable:   false,
                modal:       true,
		        closabled:   false,
                contentEl:   'map_canvas'
    }); 
    winLista.show();
    }