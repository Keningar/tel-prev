Ext.require([
    '*'
]);
var mapa;
var ciudad = "";
var tipoUbicacion = "";
var login = "";
var flagLoginCorrecto = 0;
var markerPto;


function uneValoresCoordenadas()
{
    var latitud = 0;
    var longitud = 0;
    latitud = $("#grados_la").val();
    latitud = latitud + "-";
    latitud = $("#minutos_la").val();
    latitud = latitud + "-";
    latitud = $("#segundos_la").val();
    latitud = latitud + "-";
    latitud = $("#decimas_segundos_la").val();
    latitud = latitud + "-";
    latitud = $("#latitud").val();

    longitud = $("#grados_lo").val();
    longitud = longitud + "-";
    longitud = $("#minutos_lo").val();
    longitud = longitud + "-";
    longitud = $("#segundos_lo").val();
    longitud = longitud + "-";
    longitud = $("#decimas_segundos_lo").val();
    longitud = longitud + "-";
    longitud = $("#longitud").val();
    coordenadas = latitud + ";" + longitud;
}

function comprueba_extension()
{
    archivo   = document.getElementById('infopuntotype_file').value;
    extension = (archivo.substring(archivo.lastIndexOf("."))).toLowerCase();
    
    if(extension != '')
    {
        se_permite  = false;

        extensiones = new Array(".jpg", ".png");
        for (var i = 0; i < extensiones.length; i++) 
        {
            if (extensiones[i] == extension) 
            {
                se_permite = true;
                break;
            }
        }

        if (!se_permite) 
        {
            Ext.Msg.show(
                {
                    title: 'Error',
                    msg: "No se permite la extensión <b>" + extension +
                         "</b> en el archivo del Croquis. <br>Sólo se permite subir archivos con extensión <b>.jpg</b> o <b>.png",
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            return false;
        }
        else
        {
            return true;
        }
    }
    else
    {
        return true;
    }
    
}

function validaLoginCorrecto(){
    if(flagLoginCorrecto){
        return true;
    }else{
        alert("Login ya existente. Favor Corregir para poder ingresar el Nuevo Punto Cliente");
        $('#infopuntodatoadicionaltype_login').focus();
        return false;
    }
    
}

//funciones para los grados del punto
function validaInspeccion(bandera)
{
    if (bandera == 1)
    {
        validarGrados(document.forms[0].grados_la.value, bandera);
        validarMinutos(document.forms[0].minutos_la.value, bandera);
        validarSegundos(document.forms[0].segundos_la.value, bandera);
        validarDecimasSegundos(document.forms[0].decimas_segundos_la.value, bandera);
    }
    else
    {
        validarGrados(document.forms[0].grados_lo.value, bandera);
        validarMinutos(document.forms[0].minutos_lo.value, bandera);
        validarSegundos(document.forms[0].segundos_lo.value, bandera);
        validarDecimasSegundos(document.forms[0].decimas_segundos_lo.value, bandera);
    } 
}
 
function validarGrados(caracter, bandera)
{
    if (caracter !== '')
    {
        if (bandera == 1)
        {
            if (/[^\d]/.test(caracter))
            {
                alert('Grados- Ingrese solo números');
            }
            else
            {
                //valido que sea número entre 0-360  
                if (parseInt(caracter) < 0 || parseInt(caracter) > 360)
                {
                    alert('Grados- Ingrese solo números entre 0-360');
                }
            }
        }
        else
        {
            if (/[^\d]/.test(caracter))
            {
                alert('Grados- Ingrese solo números');
            }
            else
            {
                //valido que sea número entre 0-360  
                if (parseInt(caracter) < 0 || parseInt(caracter) > 360)
                {
                    alert('Grados- Ingrese solo números entre 0-360');
                }
            }
        }
        agregarPuntoMapa();
    }
}

function validarMinutos(caracter, bandera)
{
    if (caracter !== '')
    {
        if (bandera == 1)
        {
            if (/[^\d]/.test(caracter))
            {
                alert('Minutos- Ingrese solo números');
            }
            else
            {
                //valido que sea número entre 0-59  
                if (parseInt(caracter) < 0 || parseInt(caracter) > 59)
                {
                    alert('Minutos- Ingrese solo números entre 0-59');
                }
            }
        }
        else
        {
            if (/[^\d]/.test(caracter))
            {
                alert('Minutos- Ingrese solo números');
            }
            else
            {
                //valido que sea número entre 0-59  
                if (parseInt(caracter) < 0 || parseInt(caracter) > 59)
                {
                    alert('Minutos- Ingrese solo números entre 0-59');
                }
            }
        }
        agregarPuntoMapa();
    }
}

function validarSegundos(caracter, bandera)
{
    if (caracter !== '')
    {
        if (bandera == 1) 
        {
            if (/[^\d]/.test(caracter))
            {
                alert('Segundos- Ingrese solo números');
            }
            else
            {
                //valido que sea número entre 0-59  
                if (parseInt(caracter) < 0 || parseInt(caracter) > 59)
                {
                    alert('Segundos- Ingrese solo números entre 0-59');
                }
            }
        }
        else
        {
            if (/[^\d]/.test(caracter))
            {
                alert('Segundos- Ingrese solo números');
            }
            else
            {
                //valido que sea número entre 0-59  
                if (parseInt(caracter) < 0 || parseInt(caracter) > 59)
                {
                    alert('Segundos- Ingrese solo números entre 0-59');
                }
            }
        }
        agregarPuntoMapa();
    }
}
    
function validarDecimasSegundos(caracter, bandera)
{
    if (caracter !== '')
    {
        if (bandera == 1) 
        {
            if (/[^\d]/.test(caracter)) 
            {
                alert('Décimas Segundos- Ingrese solo números');
            } 
            else 
            {
                //valido que sea número entre 0-9999  
                if (parseInt(caracter) < 0 || parseInt(caracter) > 9999) 
                {
                    alert('Décimas Segundos- Ingrese solo números entre 0-9999');
                }
            }
        } 
        else 
        {
            if (/[^\d]/.test(caracter)) 
            {
                alert('Décimas Segundos- Ingrese solo números');
            } 
            else 
            {
                //valido que sea número entre 0-9999  
                if (parseInt(caracter) < 0 || parseInt(caracter) > 9999) 
                {
                    alert('Décimas Segundos- Ingrese solo números entre 0-9999');
                }
            }
        }
        agregarPuntoMapa();
    }
}   
    
function validarGradosNuevo(caracter, bandera)
{
    agregarPuntoMapa();
    if (bandera == 1) 
    {
        if (caracter == '') 
        {
            alert('Ingrese los grados por favor');
            return false;
        }
        if (/[^\d]/.test(caracter)) 
        {
            alert('Grados- Ingrese solo números');
            return false;
        } else {
            //valido que sea número entre 0-360
            if (parseInt(caracter) < 0 || parseInt(caracter) > 360) 
            {
                alert('Grados- Ingrese solo números entre 0-360');
                return false;
            }
        }
    } 
    else 
    {
        if (caracter == '') 
        {
            alert('Ingrese los grados por favor');
            return false;
        }
        if (/[^\d]/.test(caracter)) 
        {
            alert('Grados- Ingrese solo números');
            return false;
        } 
        else 
        {
            //valido que sea número entre 0-360
            if (parseInt(caracter) < 0 || parseInt(caracter) > 360) 
            {
                alert('Grados- Ingrese solo números entre 0-360');
                return false;
            }
        }
    }   
    return true;
}
  
function validarMinutosNuevo(caracter, bandera)
{   agregarPuntoMapa();
    if (bandera == 1)
    {
        if (caracter == '')
        {
            alert('Ingrese los minutos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter))
        {
            alert('Minutos- Ingrese solo números');
            return false;
        } else {
            //valido que sea número entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59)
            {
                alert('Minutos- Ingrese solo números entre 0-59');
                return false;
            }
        }
    }
    else
    {
        if (caracter == '')
        {
            alert('Ingrese los minutos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter))
        {
            alert('Minutos- Ingrese solo números');
            return false;
        }
        else
        {
            //valido que sea número entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59)
            {
                alert('Minutos- Ingrese solo números entre 0-59');
                return false;
            }
        }
    }
    return true;
}
  
function validarSegundosNuevo(caracter, bandera)
{    agregarPuntoMapa();
    if (bandera == 1) 
    {
        if (caracter == '') 
        {
            alert('Ingrese los segundos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter))
        {
            alert('Segundos- Ingrese solo números');
            return false;
        }
        else
        {
            //valido que sea número entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59)
            {
                alert('Segundos- Ingrese solo números entre 0-59');
                return false;
            }
        }
    }
    else
    {
        if (caracter == '')
        {
            alert('Ingrese los segundos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter))
        {
            alert('Segundos- Ingrese solo números');
            return false;
        }
        else
        {
            //valido que sea número entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59)
            {
                alert('Segundos- Ingrese solo números entre 0-59');
                return false;
            }
        }
    }
    return true;
}
 
function validarDecimasSegundosNuevo(caracter, bandera)
{    agregarPuntoMapa();
    if (bandera == 1)
    {
        if (caracter == '')
        {
            alert('Ingrese las decimas de segundos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter))
        {
            alert('Décimas Segundos- Ingrese solo números');
            return false;
        } 
        else 
        {
            //valido que sea número entre 0-9999
            if (parseInt(caracter) < 0 || parseInt(caracter) > 9999)
            {
                alert('Décimas Segundos- Ingrese solo números entre 0-9999');
                return false;
            }
        }
    }
    else
    {
        if (caracter == '')
        {
            alert('Ingrese las decimas de segundos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter))
        {
            alert('Décimas Segundos- Ingrese solo números');
            return false;
        }
        else
        {
            //valido que sea número entre 0-9999
            if (parseInt(caracter) < 0 || parseInt(caracter) > 9999)
            {
                alert('Décimas Segundos- Ingrese solo números entre 0-9999');
                return false;
            }
        }
    }
    return true;
}
  
function exist(a)
{
    try
    {
        var b = a;
    }
    catch (e)
    {
        return false;
    }
    return true;
} // exist

/*
 * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>       
 * @version 1.1 24-06-2016
 * @since   1.0
 * Se añade los milisegundo a los segundos concatenando dicho valor como decimales y convirtiendo la cadena a representación Float.
 * 
 * Se adopta la fórmula del módulo técnico para procesar la conversión de la coordenada de formato sexagesimal a sistema de decimal.
 * Se ajustan los decimales a 6 posiciones, para efectos prácticos la ubicación geográfica no se ve afectada por la variación en las milésimas 
 * de segundo
 * @author Hector Ortega <haortega@telconet.ec>
 * @version 1.1, 06-12-2016
 * 
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

    dec = Math.abs(grados) + (minutos / 60) + (segundos / 3600);
    dec = dec.toFixed(6);

    if (isNaN(direccion) || direccion == '')
    {
        dec = dec * signo;
    }


    return dec;
}

/*
 * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>       
 * @version 1.1 24-06-2016
 * @since   1.0
 * Se manejan los valores de milisegundos(3 cifras) para la correcta exactitud de la ubicación de las coordenadas
 * 
 * 
 * Se adopta la fórmula del libro Beginning Google Maps Mashups with Mapplets, KML, and GeoRSS pagina 315
 * para convertir de decimales a grados minutos, segundos y milisegundos.
 * De esta forma se corrige el error que hace caer la página cuando existen decimales de segundos con valor 0
 * o cuando el decimal de segundo es un cadena 099 y al momento de convertir a entero se vuelve 99.
 * 
 * @author Hector Ortega <haortega@telconet.ec>
 * @version 1.1, 06-12-2016
 */
function dd2dms(lat, lng)
{  
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

    if (lat.substr(0, 1) == "-")
    {
        dmsLatHem.value = 'S';
        ddLatVal = lat.substr(1, lat.length - 1);
    }
    else
    {
        dmsLatHem.value = 'N';
        ddLatVal = lat;
    }

    if (lng.substr(0, 1) == "-")
    {
        dmsLongHem.value = 'O';
        ddLongVal = lng.substr(1, lng.length - 1);
    }
    else
    {
        dmsLongHem.value = 'E';
        ddLongVal = lng;
    }

    var minutesLatitude = (Math.abs(ddLatVal) - Math.floor(Math.abs(ddLatVal))) * 60;
    var secondsLatitude = Number(((minutesLatitude - Math.floor(minutesLatitude)) * 60).toFixed(3));
    var minutesIntegerLatitude = Math.floor(minutesLatitude);
    var degreesLatitude = Math.floor(ddLatVal);


    var arraySecondsLatitude = secondsLatitude.toString().split(".");
    var milliSecondsLatitude;

    if (arraySecondsLatitude.length === 1)
    {
        milliSecondsLatitude = 0;
    }
    else
    {
        milliSecondsLatitude = arraySecondsLatitude[1];
    }
    var secondsIntegerLatitude = arraySecondsLatitude[0];


    dmsLatDeg.value = degreesLatitude;
    dmsLatMin.value = minutesIntegerLatitude;
    dmsLatSec.value = secondsIntegerLatitude;
    dmsLatDecSec.value = milliSecondsLatitude;

    var minutesLongitude = (Math.abs(ddLongVal) - Math.floor(Math.abs(ddLongVal))) * 60;
    var secondsLongitude = Number(((minutesLongitude - Math.floor(minutesLongitude)) * 60).toFixed(3));
    var minutesIntegerLongitude = Math.floor(minutesLongitude);
    var degreesLongitude = Math.floor(ddLongVal);

    var arraySecondsLongitude = secondsLongitude.toString().split(".");
    var milliSecondsLongitude;

    if (arraySecondsLongitude.length === 1)
    {
        milliSecondsLongitude = 0;
    }
    else
    {
        milliSecondsLongitude = arraySecondsLongitude[1];
    }
    var secondsIntegerLongitude = arraySecondsLongitude[0];

    dmsLongDeg.value = degreesLongitude;
    dmsLongMin.value = minutesIntegerLongitude;
    dmsLongSec.value = secondsIntegerLongitude;
    dmsLongDecSec.value = milliSecondsLongitude;
 
    //Se sobre escriben los valores de latitud y longitud.
    $("#infopuntoextratype_latitudFloat").val(lat);
    $("#infopuntoextratype_longitudFloat").val(lng);
}
    
/**
 * Esta función permite convertir de grados, minutos, segundos y milisegundos a decimales
 * y validar si las coordenadas son de Ecuador
 * @version 1.0
 * 
 * Se evita realizar el parseFloat de las decimas de segundos para que no se genere el problema
 * en el que la cadena 099 es convertida en 99.
 * @author Hector Ortega <haortega@telconet.ec>
 * @version 1.1, 07/12/2016
 * 
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.2 29-06-2017
 * Se agregan las coordenadas para Panamá
 * 
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.3 13-03-2019
 * Se agregan las coordenadas para Guatemala
 */
function validarCoordenadasEcuador(formulario)
  {
      grados_la_nuevos = parseFloat(formulario.grados_la.value);
      minutos_la_nuevos = parseFloat(formulario.minutos_la.value);
      segundo_la_nuevos = parseFloat(formulario.segundos_la.value);
      decimas_la_nuevos = formulario.decimas_segundos_la.value;
      latitud_la_nuevo = formulario.latitud[formulario.latitud.selectedIndex].value;
      valor_latitud = gms2dec(grados_la_nuevos,minutos_la_nuevos,segundo_la_nuevos,decimas_la_nuevos,latitud_la_nuevo);
      grados_lo_nuevos = parseFloat(formulario.grados_lo.value);
      minutos_lo_nuevos = parseFloat(formulario.minutos_lo.value);
      segundo_lo_nuevos = parseFloat(formulario.segundos_lo.value);
      decimas_lo_nuevos = formulario.decimas_segundos_lo.value;
      longitud_lo_nuevo = formulario.longitud[formulario.longitud.selectedIndex].value;
      valor_longitud = gms2dec(grados_lo_nuevos,minutos_lo_nuevos,segundo_lo_nuevos,decimas_lo_nuevos,longitud_lo_nuevo);
      
    var latitud_1   = -5.036;
    var latitud_2   = 1.40;
    var longitud_1  = -95;
    var longitud_2  = -75.25;
    var paisEmpresa = "Ecuador";
    if (strNombrePais === 'PANAMA') {
        latitud_1   = 7.15;
        latitud_2   = 9.66;
        longitud_1  = -83.06;
        longitud_2  = -77.15;
        paisEmpresa = "Panamá";
    }
    else if (strNombrePais === 'GUATEMALA') {
        latitud_1   = 13.428461;
        latitud_2   = 17.933653;
        longitud_1  = -92.512969;
        longitud_2  = -87.471971;
        paisEmpresa = "Guatemala";
    }    
     if(!(valor_latitud >= parseFloat(latitud_1) && valor_latitud <= parseFloat(latitud_2)))
         {
             alert('Debe ingresar la latitud en el rango de ' + paisEmpresa);
              return false;
          }
     if(!(valor_longitud >= parseFloat(longitud_1) && valor_longitud <= parseFloat(longitud_2)))
         {

            alert('Debe ingresar la longitud en el rango de ' + paisEmpresa);
             
              return false;
          }

    $("#infopuntoextratype_latitudFloat").val(valor_latitud);
    $("#infopuntoextratype_longitudFloat").val(valor_longitud);
          
      return true;
  }

/*
 * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>       
 * @version 1.1 24-06-2016
 * @since   1.0
 * Se muestra el marcador de las coordenadas seleccionadas.
 * 
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.2 30-06-2017
 * Se valida si el mapa adopta las configuraciones de Panamá o de Ecuador
 * 
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.3 13-03-2019
 * Se agrega  condición para que ubicación del mapa se cargue según el país
 * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec>
 * @version 1.0  07-07-2021
 * #GEO Se agrega  funcion  geoVerificarCatalogo  dentro de evento close del mapa
 */
function muestraMapa()
{
    var latlng = '';
    var zoomin = 14;
    
    if(strNombrePais === 'GUATEMALA')
    {
        latlng = new google.maps.LatLng(14.622778,-90.531389);
    }
    else if(strNombrePais === 'PANAMA')
    {
        latlng = new google.maps.LatLng(8.9945709,-79.5161408);
    }
    else // ECUADOR
    {
        if (combo_ptoscobertura.getRawValue() == 'TTCO GUAYAQUIL')
        {
            latlng = new google.maps.LatLng(-2.160703, -79.897525);
        }
        else
        {
            if (combo_ptoscobertura.getRawValue() == 'TTCO QUITO')
            {
                zoomin = 12;
                latlng = new google.maps.LatLng(-0.1950069477274085, -78.51997375488281);
            }
            else
            {
                latlng = new google.maps.LatLng(-2.160703, -79.897525);
                zoomin = 13;
            }
        }        
    }

    var myOptions =
        {
            zoom: zoomin,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

    if (mapa)
    {
        mapa.setCenter(latlng);
    }
    else
    {
        mapa = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    }

    if (ciudad == "gye")
    {
        layerCiudad = 'http://157.100.3.122/Coberturas.kml';
    }
    else
    {
        layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';
    }

    // Se define el marcador en el mapa según las coordenadas existentes
    var lat = $("#infopuntoextratype_latitudFloat").val();
    var lon = $("#infopuntoextratype_longitudFloat").val();
    if (lat !== '' && lon !== '')
    {
        latlng = new google.maps.LatLng(lat, lon);

        if (markerPto)
        {
            markerPto.setMap(null);
        }

        markerPto = new google.maps.Marker(
            {
                position: latlng,
                map: mapa
            });

        if(strNombrePais === 'PANAMA')
        {
            mapa.setZoom(17);
        }
        {
            mapa.setZoom(16);
        }
        mapa.setCenter(latlng);
    }

    google.maps.event.addListener(mapa, 'dblclick', function(event)
    {
        if (markerPto)
        {
            markerPto.setMap(null);
        }

        markerPto = new google.maps.Marker(
            {
                position: event.latLng,
                map: mapa
            });

        lat = event.latLng.lat ();
        lon = event.latLng.lng ();
        dd2dms (lat, lon);
        
    });

    winLista = Ext.widget('window',
        {
            title: 'Mapa',
            closeAction: 'hide',
            width: 800,
            height: 600,
            minHeight: 700,
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            contentEl: 'map_canvas',
            listeners: {
                close: function () {
                  dd2dms (lat, lon);
                  geoVerificarCatalogo (lat, lon);
                  
                },
              },
          
        });
    winLista.show();
}



/*
 * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec>       
 * @version 1.0  07-07-2021
 * #GEO Se consume MS para autocompletar puntos de cobertura , provincia, canton , parroquia y sector
 */
function geoVerificarCatalogo (latitud, longitud) {
    var objComboPuntoCobertura = Ext.ComponentQuery.query (
      'combobox[name=idptocobertura]'
    )[0];
    var  objComboCanton = Ext.ComponentQuery.query ('combobox[name=idcanton]')[0];
    var  objComboParroquia = Ext.ComponentQuery.query (
      'combobox[name=idparroquia]'
    )[0];
    var  objComboSector = Ext.ComponentQuery.query ('combobox[name=idsector]')[0];
  
    let cityCurrent = objComboCanton.getValue ();
  
    function geoBockSelector () {
      objComboPuntoCobertura.setDisabled (true);
      objComboCanton.setDisabled (true);
      objComboParroquia.setDisabled (true);
      objComboSector.setDisabled (true); 
    }
  
    function geoValidationYes () {
      //bloquea los selectores con data encontrada y gebera el login si la ciudad cambio
      if (cityCurrent != objComboCanton.getValue ()) {
        generaLogin ();
      }
    }
  
    function geoValidationNO () {
      //limpia los selectores
      objComboPuntoCobertura.reset ();
      objComboCanton.reset ();
      objComboParroquia.reset ();
      objComboSector.reset ();
      $ ('#infopuntoextratype_ptoCoberturaId').val ('');
      $ ('#infopuntoextratype_cantonId').val  ('');
      $ ('#infopuntoextratype_parroquiaId').val  ('');
      $ ('#infopuntoextratype_sectorId').val  ('');
      geoBockSelector ();
    }
  
    var esRequerido = (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' );
    if (latitud != '' &&  longitud != '' && esRequerido && !isNaN(latitud)&& !isNaN(longitud)) {
      geoValidationNO ();
      geoBockSelector ();
      Ext.MessageBox.wait ('Verificando localización..', 'Por favor espere');
  
      $.ajax ({
        type: 'POST',
        data: 'latitud=' + latitud + '&longitud=' + longitud,
        url: url_verificar_catalogo,
        success:  function (response) {
          Ext.MessageBox.hide ();
          let objData = response.objData;
          let strMensaje = response.strMensaje;
          let strStatus = response.strStatus;
          if (strStatus == 'OK') {
               objComboPuntoCobertura.setDisabled (false);
            if (objData.idPuntoCobertura > 0) {
              objComboPuntoCobertura.setValue (objData.idPuntoCobertura); 
              objComboPuntoCobertura.setDisabled (true);
              $ ('#infopuntoextratype_ptoCoberturaId').val (
                objData.idPuntoCobertura
              );
              //****CARGA DATA DE SELECTOR 
              objComboCanton.setDisabled (false);
              objComboCanton.getStore ().proxy.extraParams.idjurisdiccion = objData.idPuntoCobertura;           
              objComboCanton.getStore ().load ();   
            if (objData.idCanton > 0) {  
                objComboCanton.getStore ().insert (0, {
                    id: objData.idCanton,
                    nombre: objData.nombreCanton,
                });
                objComboCanton.setValue (objData.idCanton);
                objComboCanton.setDisabled (true);
                $ ('#canton').val (objData.idCanton);
                $ ('#infopuntoextratype_cantonId').val (objData.idCanton);
                //****CARGA DATA DE SELECTOR 
                objComboParroquia.setDisabled (false);
                objComboParroquia.getStore ().proxy.extraParams.idcanton = objData.idCanton;           
                objComboParroquia.getStore ().load (); 

                if (objData.idParroquia > 0) { 
                    objComboParroquia.getStore ().insert (0, {
                        id: objData.idParroquia,
                        nombre: objData.nombreParroquia,
                    });
                    objComboParroquia.setValue (objData.idParroquia);
                    objComboParroquia.setDisabled (true);
                    $ ('#infopuntoextratype_parroquiaId').val (objData.idParroquia);
                     //****CARGA DATA DE SELECTOR
                     objComboSector.setDisabled (false);
                     objComboSector.getStore ().proxy.extraParams.idparroquia = objData.idParroquia;           
                     objComboSector.getStore ().load ();
                       
                    if (objData.idSector > 0) {                
                        objComboSector.getStore ().insert (0, {
                            id: objData.idSector,
                            nombre: objData.nombreSector,
                        });
                        objComboSector.setValue (objData.idSector);
                        objComboSector.setDisabled (true);
                        $ ('#infopuntoextratype_sectorId').val (objData.idSector); 
                         geoValidationYes ();                  
                                    
                    }  
                } 
              }  
            } 


            if (!objData.idSector > 0) {  
              Ext.Msg.confirm ({
                title: 'No se obtuvieron los datos de ubicación',
                msg: '¿Las coordenadas ingresadas son correctas?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.MessageBox.QUESTION,
                buttonText: {
                  yes: 'si',
                  no: 'no',
                },
                fn: function (btn) {
                  if (btn == 'yes') {
                    geoValidationYes ();
                  } else {
                    geoValidationNO ();
                  }
                },
              });
            } 
          } else {
            Ext.Msg.alert ('Error', strMensaje);
          }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
          Ext.MessageBox.hide ();
          Ext.Msg.alert ('Error ', errorThrown);
        },
        failure: function (result) {
          Ext.MessageBox.hide ();
          Ext.Msg.alert ('Error ', 'Error: ' + result.statusText);
        },
      });
    }
  }
  

  function agregarPuntoMapa() {
    var esRequerido = (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' );
    if (esRequerido) {    
    var grados_la = $("#grados_la").val();  
    var minutos_la = $("#minutos_la").val();   
    var segundos_la = $("#segundos_la").val(); 
    var decimas_segundos_la = $("#decimas_segundos_la").val();  
    var latitud = $("#latitud").val();

    var grados_lo = $("#grados_lo").val(); 
    var minutos_lo= $("#minutos_lo").val(); 
    var segundos_lo= $("#segundos_lo").val(); 
    var decimas_segundos_lo = $("#decimas_segundos_lo").val(); 
    var longitud= $("#longitud").val(); 
    //convertir coordenadas
    var valor_latitud= gms2dec(grados_la, minutos_la, segundos_la,decimas_segundos_la, latitud); 
    valor_latitud=parseFloat(valor_latitud).toFixed(6); 
    var valor_longitud= gms2dec(grados_lo, minutos_lo, segundos_lo,decimas_segundos_lo, longitud); 
    valor_longitud=parseFloat(valor_longitud).toFixed(6); 

    
   var valor_latitud_old=  $("#infopuntoextratype_latitudFloat").val();
   valor_latitud_old=   parseFloat(valor_latitud_old).toFixed(6); 
   var valor_longitud_old = $("#infopuntoextratype_longitudFloat").val();
   valor_longitud_old=   parseFloat(valor_longitud_old).toFixed(6); 

   if (valor_latitud!= valor_latitud_old ||valor_longitud!=valor_longitud_old ) {
    $("#infopuntoextratype_latitudFloat").val(valor_latitud);
    $("#infopuntoextratype_longitudFloat").val(valor_longitud);
    //identificadores de selectores
    var  objComboPuntoCobertura = Ext.ComponentQuery.query ('combobox[name=idptocobertura]' )[0];
    var  objComboCanton = Ext.ComponentQuery.query ('combobox[name=idcanton]')[0];
    var  objComboParroquia = Ext.ComponentQuery.query ('combobox[name=idparroquia]')[0];
    var  objComboSector = Ext.ComponentQuery.query ('combobox[name=idsector]')[0];
  //deshabilitar
    objComboPuntoCobertura.setDisabled (true);
    objComboCanton.setDisabled (true);
    objComboParroquia.setDisabled (true);
    objComboSector.setDisabled (true);
    //limpiar   
    objComboPuntoCobertura.reset ();
    objComboCanton.reset ();
    objComboParroquia.reset ();
    objComboSector.reset ();
    $ ('#infopuntoextratype_ptoCoberturaId').val ('');
    $ ('#infopuntoextratype_cantonId').val  ('');
    $ ('#infopuntoextratype_parroquiaId').val  ('');
    $ ('#infopuntoextratype_sectorId').val ('');
     }
    }

  }