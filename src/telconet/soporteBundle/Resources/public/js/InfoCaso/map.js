Ext.require([
    '*'
]);
var mapa;
var mapaIncidente;
var mapaManga1;
var mapaManga2;
var ciudad = "";
var tipoUbicacion = "";
var login = "";
var flagLoginCorrecto = 0;
var markerPto;
var banderaCanvas = "";

function uneValoresCoordenadas() {
    var latitud = 0;
    var longitud = 0;
    latitud = $("#grados_la").val();
    latitud = latitud + "-";
    latitud = $("minutos_la").val();
    latitud = latitud + "-";
    latitud = $("segundos_la").val();
    latitud = latitud + "-";
    latitud = $("decimas_segundos_la").val();
    latitud = latitud + "-";
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

function validacionesForm() {

    //funciones para validar las coordenadas
    if (!validarGradosNuevo($("#hid_grados_la").val(), 1))
        return false;

    if (!validarMinutosNuevo($("#hid_minutos_la").val(), 1))
        return false;

    if (!validarSegundosNuevo($("#hid_segundos_la").val(), 1))
        return false;

    if (!validarDecimasSegundosNuevo($("#hid_decimas_segundos_la").val(), 1))
        return false;

    if (!validarGradosNuevo($("#hid_grados_lo").val(), 2))
        return false;

    if (!validarMinutosNuevo($("#hid_minutos_lo").val(), 2))
        return false;

    if (!validarSegundosNuevo($("#hid_segundos_lo").val(), 2))
        return false;

    if (!validarDecimasSegundosNuevo($("#hid_decimas_segundos_lo").val(), 2))
        return false;


    if (!validarCoordenadasEcuador())
        return false;

    return true;
}

//funciones para los grados del punto
function validaInspeccion(bandera) {
    if (bandera == 1) {
        validarGrados(document.forms[0].grados_la.value, bandera);
        validarMinutos(document.forms[0].minutos_la.value, bandera);
        validarSegundos(document.forms[0].segundos_la.value, bandera);
        validarDecimasSegundos(document.forms[0].decimas_segundos_la.value, bandera);
    } else {
        validarGrados(document.forms[0].grados_lo.value, bandera);
        validarMinutos(document.forms[0].minutos_lo.value, bandera);
        validarSegundos(document.forms[0].segundos_lo.value, bandera);
        validarDecimasSegundos(document.forms[0].decimas_segundos_lo.value, bandera);
    }
}

function validarGrados(caracter, bandera) {
    if (bandera == 1) {
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Grados- Ingrese solo números');
        } else {
            //valido que sea numero entre 0-360
            if (parseInt(caracter) < 0 || parseInt(caracter) > 360) {
                alert('Grados- Ingrese solo números entre 0-360');
            }
        }
    } else {
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Grados- Ingrese solo números');
        } else {
            //valido que sea numero entre 0-360
            if (parseInt(caracter) < 0 || parseInt(caracter) > 360) {
                alert('Grados- Ingrese solo números entre 0-360');
            }
        }
    }
}

function validarMinutos(caracter, bandera) {
    if (bandera == 1) {
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Minutos- Ingrese solo números');
        } else {
            //valido que sea numero entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59) {
                alert('Minutos- Ingrese solo números entre 0-59');
            }
        }
    } else {
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Minutos- Ingrese solo números');
        } else {
            //valido que sea numero entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59) {
                alert('Minutos- Ingrese solo números entre 0-59');
            }
        }
    }
}

function validarSegundos(caracter, bandera) {
    if (bandera == 1) {
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Segundos- Ingrese solo números');
        } else {
            //valido que sea numero entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59) {
                alert('Segundos- Ingrese solo números entre 0-59');
            }
        }
    } else {
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Segundos- Ingrese solo números');
        } else {
            //valido que sea numero entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59) {
                alert('Segundos- Ingrese solo números entre 0-59');
            }
        }
    }
}

function validarDecimasSegundos(caracter, bandera) {
    if (bandera == 1) {
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Decimas Segundos- Ingrese solo números');
        } else {
            //valido que sea numero entre 0-99  
            if (parseInt(caracter) < 0 || parseInt(caracter) > 99) {
                alert('Decimas Segundos- Ingrese solo números entre 0-99');
            }
        }
    } else {
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Decimas Segundos- Ingrese solo números');
        } else {
            //valido que sea numero entre 0-99  
            if (parseInt(caracter) < 0 || parseInt(caracter) > 99) {
                alert('Decimas Segundos- Ingrese solo números entre 0-99');
            }
        }
    }
}

function validarGradosNuevo(caracter, bandera)
{
    if (bandera == 1) {
        if (caracter == '') {
            alert('Ingrese los grados por favor');
            return false;
        }
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Grados- Ingrese solo números');
            return false;
        } else {
            //valido que sea numero entre 0-360
            if (parseInt(caracter) < 0 || parseInt(caracter) > 360) {
                alert('Grados- Ingrese solo números entre 0-360');
                return false;
            }
        }
    } else {
        if (caracter == '') {
            alert('Ingrese los grados por favor');
            return false;
        }
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Grados- Ingrese solo números');
            return false;
        } else {
            //valido que sea numero entre 0-360
            if (parseInt(caracter) < 0 || parseInt(caracter) > 360) {
                alert('Grados- Ingrese solo números entre 0-360');
                return false;
            }
        }
    }
    return true;
}

function validarMinutosNuevo(caracter, bandera) {
    if (bandera == 1) {
        if (caracter == '') {
            alert('Ingrese los minutos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Minutos- Ingrese solo números');
            return false;
        } else {
            //valido que sea numero entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59) {
                alert('Minutos- Ingrese solo números entre 0-59');
                return false;
            }
        }
    } else {
        if (caracter == '') {
            alert('Ingrese los minutos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Minutos- Ingrese solo números');
            return false;
        } else {
            //valido que sea numero entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59) {
                alert('Minutos- Ingrese solo números entre 0-59');
                return false;
            }
        }
    }
    return true;
}

function validarSegundosNuevo(caracter, bandera) {
    if (bandera == 1) {
        if (caracter == '') {
            alert('Ingrese los segundos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Segundos- Ingrese solo números');
            return false;
        } else {
            //valido que sea numero entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59) {
                alert('Segundos- Ingrese solo números entre 0-59');
                return false;
            }
        }
    } else {
        if (caracter == '') {
            alert('Ingrese los segundos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Segundos- Ingrese solo números');
            return false;
        } else {
            //valido que sea numero entre 0-59
            if (parseInt(caracter) < 0 || parseInt(caracter) > 59) {
                alert('Segundos- Ingrese solo números entre 0-59');
                return false;
            }
        }
    }
    return true;
}

function validarDecimasSegundosNuevo(caracter, bandera) {
    if (bandera == 1) {
        if (caracter == '') {
            alert('Ingrese las decimas de segundos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Decimas Segundos- Ingrese solo números');
            return false;
        } else {
            //valido que sea numero entre 0-99
            if (parseInt(caracter) < 0 || parseInt(caracter) > 99) {
                alert('Decimas Segundos- Ingrese solo números entre 0-99');
                return false;
            }
        }
    } else {
        if (caracter == '') {
            alert('Ingrese las decimas de segundos por favor');
            return false;
        }
        if (/[^\d]/.test(caracter) || caracter == '') {
            alert('Decimas Segundos- Ingrese solo números');
            return false;
        } else {
            //valido que sea numero entre 0-99
            if (parseInt(caracter) < 0 || parseInt(caracter) > 99) {
                alert('Decimas Segundos- Ingrese solo números entre 0-99');
                return false;
            }
        }
    }
    return true;
}

function exist(a) {
    try {
        var b = a;
    }
    catch (e) {
        return false;
    }

    return true;
}

function gms2dec(grados, minutos, segundos, decimas_segundos, direccion)
{
    if (direccion)
    {
        signo = (direccion.toLowerCase() == 'o' ||
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
        signo = (grados < 0) ? -1 : 1;
        direccion = '';
    }

    dec = Math.round((Math.abs(grados) + ((minutos * 60) + segundos) / 3600) * 1000000) / 1000000;

    if (isNaN(direccion) || direccion == '')
        dec = dec * signo;
    return dec;
}

function dd2dms(lat, lng) {
    lat   = lat.toString();
    lng   = lng.toString();
    var d = document;
    var dmsLatDeg    = d.getElementById("hid_grados_la");
    var dmsLatMin    = d.getElementById("hid_minutos_la");
    var dmsLatSec    = d.getElementById("hid_segundos_la");
    var dmsLatDecSec = d.getElementById("hid_decimas_segundos_la");
    var dmsLatHem    = d.getElementById("hid_latitud");

    var dmsLongDeg    = d.getElementById("hid_grados_lo");
    var dmsLongMin    = d.getElementById("hid_minutos_lo");
    var dmsLongSec    = d.getElementById("hid_segundos_lo");
    var dmsLongDecSec = d.getElementById("hid_decimas_segundos_lo");
    var dmsLongHem    = d.getElementById("hid_longitud");

    if (lat.substr(0, 1) == "-") {
        dmsLatHem.value = 'S';
        $("#hid_latitud").val("S");
        ddLatVal = lat.substr(1, lat.length - 1);
    } else {
        dmsLatHem.value = 'N';
        $("#hid_latitud").val("N");
        ddLatVal = lat;
    }

    if (lng.substr(0, 1) == "-") {
        dmsLongHem.value = 'O';
        $("#hid_longitud").val("O");
        ddLongVal = lng.substr(1, lng.length - 1);
    } else {
        dmsLongHem.value = 'E';
        $("#hid_longitud").val("E");
        ddLongVal = lng;
    }

    // degrees = degrees
    ddLatVals = ddLatVal.split(".");
    dmsLatDeg.value = ddLatVals[0];

    ddLongVals = ddLongVal.split(".");
    dmsLongDeg.value = ddLongVals[0];

    // * 60 = mins
    ddLatRemainder = ("0." + ddLatVals[1]) * 60;
    dmsLatMinVals = ddLatRemainder.toString().split(".");
    dmsLatMin.value = dmsLatMinVals[0];

    ddLongRemainder = ("0." + ddLongVals[1]) * 60;
    dmsLongMinVals = ddLongRemainder.toString().split(".");
    dmsLongMin.value = dmsLongMinVals[0];

    // * 60 again = secs
    ddLatMinRemainder = ("0." + dmsLatMinVals[1]) * 60;
    dmsLatSec.value = Math.round(ddLatMinRemainder);

    ddLongMinRemainder = ("0." + dmsLongMinVals[1]) * 60;
    dmsLongSec.value = Math.round(ddLongMinRemainder);

    //ceros
    dmsLatDecSec.value = 0;
    dmsLongDecSec.value = 0;

}

function validarCoordenadasEcuador()
{
    grados_la_nuevos  = parseFloat($("#hid_grados_la").val());
    minutos_la_nuevos = parseFloat($("#hid_minutos_la").val());
    segundo_la_nuevos = parseFloat($("#hid_segundos_la").val());
    decimas_la_nuevos = parseFloat($("#hid_decimas_segundos_la").val());
    latitud_la_nuevo  = $("#hid_latitud").val();

    valor_latitud = gms2dec(grados_la_nuevos, minutos_la_nuevos, segundo_la_nuevos, decimas_la_nuevos, latitud_la_nuevo);

    grados_lo_nuevos  = parseFloat($("#hid_grados_lo").val());
    minutos_lo_nuevos = parseFloat($("#hid_minutos_lo").val());
    segundo_lo_nuevos = parseFloat($("#hid_segundos_lo").val());
    decimas_lo_nuevos = parseFloat($("#hid_hdd_longitud").val());
    longitud_lo_nuevo = $("#hid_longitud").val();

    valor_longitud = gms2dec(grados_lo_nuevos, minutos_lo_nuevos, segundo_lo_nuevos, decimas_lo_nuevos, longitud_lo_nuevo);

    if (!(valor_latitud >= parseFloat(-5.036) && valor_latitud <= parseFloat(1.40)))
    {
        alert('Debe ingresar la latitud en el rango de Ecuador');
        return false;
    }
    if (!(valor_longitud >= parseFloat(-95) && valor_longitud <= parseFloat(-75.25)))
    {
        alert('Debe ingresar la longitud en el rango de Ecuador');

        return false;
    }

    return true;
}

function muestraMapa(tipo) {

    var latlng = new google.maps.LatLng(-2.160703, -79.897525);

    var myOptions = {
        zoom: 14,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    if(tipo == 3)
    {
        banderaCanvas = "map_canvasIncidente";
        if (mapaIncidente) {

            mapaIncidente.setCenter(latlng);
        } else {
            mapaIncidente = new google.maps.Map(document.getElementById("map_canvasIncidente"), myOptions);
        }
        mapa = mapaIncidente;
    }
    else if(tipo == 1)
    {
        banderaCanvas = "map_canvasManga1";
        if (mapaManga1) {
            mapaManga1.setCenter(latlng);
        } else {
            mapaManga1 = new google.maps.Map(document.getElementById("map_canvasManga1"), myOptions);
        }
        mapa = mapaManga1;
    }
    else if(tipo == 2)
    {
        banderaCanvas = "map_canvasManga2";
        if (mapaManga2) {
            mapaManga2.setCenter(latlng);
        } else {
            mapaManga2 = new google.maps.Map(document.getElementById("map_canvasManga2"), myOptions);
        }
        mapa = mapaManga2;
    }
    if (ciudad == "gye")
        layerCiudad = 'http://157.100.3.122/Coberturas.kml';
    else
        layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';

    google.maps.event.addListener(mapa, 'dblclick', function(event) {

        if (markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: event.latLng,
            map: mapa
        });
        mapa.setZoom(17);

        dd2dms(event.latLng.lat(), event.latLng.lng());

        validacionesForm();

        setearCoordenadas(tipo,event.latLng.lat().toString().substr(0, 10),event.latLng.lng().toString().substr(0, 10));
    });

        winLista = Ext.widget('window', {
            title: 'Mapa',
            closeAction: 'hide',
            width: 510,
            height: 380,
            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            contentEl: banderaCanvas
        });
        winLista.show();
}

function setearCoordenadas(tipo,latitud,longitud) {

    if(tipo == 3)
    {
        Ext.getCmp('text_longitudI').setValue(longitud);
        Ext.getCmp('text_latitudI').setValue(latitud);
    }
    else if(tipo == 1)
    {
        Ext.getCmp('text_longitudManga1').setValue(longitud);
        Ext.getCmp('text_latitudManga1').setValue(latitud);
    }
    else if(tipo == 2)
    {
        Ext.getCmp('text_longitudManga2').setValue(longitud);
        Ext.getCmp('text_latitudManga2').setValue(latitud);
    }
}