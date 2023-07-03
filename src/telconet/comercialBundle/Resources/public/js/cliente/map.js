/**
 * Documentación para la función 'showMapaFactibilidad'
 * Muestra mapa unicamente para el país Ecuador y ubica coordenadas
 * 
 * @author Andrea Cardenas <ascardenas@telconet.ec>
 * @version 1.0 18/06/2021
 * 
 */
 var mapa_fac;
 var markerPto;

 function showMapaFactibilidad()
 {
     var coordenadas = '';
     var zoom_in = 13;
     
     // ECUADOR     
     coordenadas = new google.maps.LatLng(-2.160703, -79.897525);
     
     var options =
         {
             zoom: zoom_in,
             center: coordenadas,
             mapTypeId: google.maps.MapTypeId.ROADMAP
         };
 
         if (mapa_fac)
          {
            mapa_fac.setCenter(coordenadas);
          }
          else
          {
            mapa_fac = new google.maps.Map(document.getElementById("canvas_mapa"), options);
          }
         layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';
     
 
     // Se define el marcador en el mapa según las coordenadas existentes
     var latitud = $("#infopuntoextratype_latitudFloat").val();
     console.log(latitud);
     var longitud = $("#infopuntoextratype_longitudFloat").val();
     console.log(longitud);
     if (latitud !== '' && longitud !== '')
     {
         coordenadas = new google.maps.LatLng(latitud, longitud);
         if (markerPto)
        {
            markerPto.setMap(null);
        }

         markerPto = new google.maps.Marker(
             {
                 position: coordenadas,
                 map: mapa_fac
             });
 
             mapa_fac.setZoom(16);
             mapa_fac.setCenter(coordenadas);
     }
 
     google.maps.event.addListener(mapa_fac, 'dblclick', function(event)
     {
        if (markerPto)
        {
            markerPto.setMap(null);
        }

        markerPto = new google.maps.Marker(
             {
                 position: event.latLng,
                 map: mapa_fac
             });
        
        latitud = event.latLng.lat();
        longitud = event.latLng.lng();
        dd2dms (latitud, longitud);
     
         
     });
 
     winMapa = Ext.widget('window',
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
             contentEl: 'canvas_mapa',
             listeners: {
                close: function () {
                   dd2dms (latitud, longitud);
                   verificarGeolocalizacion (latitud, longitud);

                },
              },

         });
 
     winMapa.show();
 }
 

function verificarGeolocalizacion (coord_latitud, coord_longitud) 
{
    var comboPuntoCobertura = Ext.ComponentQuery.query ('combobox[name=idptocobertura]')[0];
    var comboCanton = Ext.ComponentQuery.query ('combobox[name=idcanton]')[0];
    var comboParroquia = Ext.ComponentQuery.query (
      'combobox[name=idparroquia]'
    )[0];
    var comboSector = Ext.ComponentQuery.query ('combobox[name=idsector]')[0];
    
    var arrayLocation= [comboPuntoCobertura, comboCanton,comboParroquia,comboSector];
  
  
    if (coord_latitud != '' && coord_longitud != '') {
        resetCombosLocation(arrayLocation);
        disableCombosLocation(arrayLocation);
     
      Ext.MessageBox.wait ('Verificando localización..', 'Por favor espere');
  
      $.ajax ({
        type: 'POST',
        data: 'latitud=' + coord_latitud + '&longitud=' + coord_longitud,
        url: url_verificar_catalogo,
        success: function (response) {
          Ext.MessageBox.hide ();
          var objDatos = response.objData;
          var strMensaje = response.strMensaje;
          var strStatus = response.strStatus;
          if (strStatus == 'OK') {
            if (objDatos.idPuntoCobertura > 0) {
              comboPuntoCobertura.setValue (objDatos.idPuntoCobertura);
              comboPuntoCobertura.setRawValue (objDatos.nombrePuntoCobertura);
              $ ('#infopuntoextratype_ptoCoberturaId').val (
                objDatos.idPuntoCobertura
              );
            } else {
              comboPuntoCobertura.setDisabled (false);
            }
  
            if (objDatos.idCanton > 0) {
              comboCanton.getStore ().insert (0, {
                id: objDatos.idCanton,
                nombre: objDatos.nombreCanton,
              });
              comboCanton.setValue (objDatos.idCanton);
              $ ('#infopuntoextratype_cantonId').val (objDatos.idCanton);
            } else {
              comboCanton.setDisabled (false);
            }
  
            if (objDatos.idParroquia > 0) {
              comboParroquia.getStore ().insert (0, {
                id: objDatos.idParroquia,
                nombre: objDatos.nombreParroquia,
              });
              comboParroquia.setValue (objDatos.idParroquia);
              $ ('#infopuntoextratype_parroquiaId').val (objDatos.idParroquia);
            } else {
              comboParroquia.setDisabled (false);
              comboParroquia.getStore ().proxy.extraParams = {
                idcanton: objDatos.idCanton
              };
              comboParroquia.getStore ().load ();
            }
  
            if (objDatos.idSector > 0 ) {
              comboSector.getStore ().insert (0, {
                id: objDatos.idSector,
                nombre: objDatos.nombreSector,
              });
              comboSector.setValue (objDatos.idSector);
              $ ('#infopuntoextratype_sectorId').val (objDatos.idSector);
              
            } else {
              comboSector.setDisabled (false);
              comboSector.getStore ().proxy.extraParams = {
                idparroquia: objDatos.idParroquia,
              };
              comboSector.getStore ().load ();
  
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
                  if (btn == 'no') {
                    resetCombosLocation(arrayLocation);
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


  function disableCombosLocation(arrayLocation)
  {
    for (var i = 0; i < arrayLocation.length; i++)
    {
        arrayLocation[i].setDisabled (true);
    }
  }
 
  function resetCombosLocation(arrayLocation)
  {
    for (var i = 0; i < arrayLocation.length; i++)
    {
        arrayLocation[i].reset ();
    }
    disableCombosLocation(arrayLocation);
  }

 function validacionesCoordenadas() 
 {
      
     if ((document.forms[0].grados_la.value && document.forms[0].minutos_la.value && document.forms[0].segundos_la.value && document.forms[0].decimas_segundos_la.value && (document.forms[0].latitud.value != '')) &&
         (document.forms[0].grados_lo.value && document.forms[0].minutos_lo.value && document.forms[0].segundos_lo.value && document.forms[0].decimas_segundos_lo.value) && (document.forms[0].longitud.value != '')) 
    {
         //funciiones para validar las coordenadas
             if (!validarGradosNuevo(document.forms[0].grados_la.value, 1))
                 return false;
             if (!validarMinutosNuevo(document.forms[0].minutos_la.value, 1))
                 return false;
             if (!validarSegundosNuevo(document.forms[0].segundos_la.value, 1))
                 return false;
             if (!validarDecimasSegundosNuevo(document.forms[0].decimas_segundos_la.value, 1))
                 return false;
             if (!validarGradosNuevo(document.forms[0].grados_lo.value, 2))
                 return false;
             if (!validarMinutosNuevo(document.forms[0].minutos_lo.value, 2))
                 return false;
             if (!validarSegundosNuevo(document.forms[0].segundos_lo.value, 2))
                 return false;
             if (!validarDecimasSegundosNuevo(document.forms[0].decimas_segundos_lo.value, 2))
                 return false;
             if (document.forms[0].latitud[document.forms[0].latitud.selectedIndex].value == 'T') 
            {
                 alert('Ingrese la latitud (Norte/Sur)');
                 return false;
            }
             
             if (document.forms[0].longitud[document.forms[0].longitud.selectedIndex].value == 'T') 
            {
                 alert('Ingrese la longitud (Este/Oeste)');
                 return false;
            }
 
             if (!validarCoordenadasEcuador(document.forms[0])){return false;}
         return true;
    } 
    else 
    {
         if ((document.forms[0].grados_la.value || document.forms[0].minutos_la.value || document.forms[0].segundos_la.value || document.forms[0].decimas_segundos_la.value || (document.forms[0].latitud.value != '')) ||
             (document.forms[0].grados_lo.value || document.forms[0].minutos_lo.value || document.forms[0].segundos_lo.value || document.forms[0].decimas_segundos_lo.value) || (document.forms[0].longitud.value != '')) {
             alert('Debe ingresar todos los campos de las COORDENADAS para poder realizar la consulta');
             return false;
         } else {
             return true;
         }
 
         
    }
 
 }

