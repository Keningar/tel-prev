var grados_la_nuevos;
var minutos_la_nuevos;
var segundo_la_nuevos;
var decimas_la_nuevos;
var latitud_la_nuevo;
var valor_latitud;
var grados_lo_nuevos;
var minutos_lo_nuevos;
var segundo_lo_nuevos;
var decimas_lo_nuevos;
var longitud_lo_nuevo;
var valor_longitud;
/**
 *  Función que sirve para convertir la longitud y latitud de grados a decimales y se valida que las coordenadas se encuentren dentro de 
 *  los límites definidos a nivel de sesión de acuerdo al país
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 20-09-2018
 * 
 */
function validarCoordenadasPorPaisForm(formulario, prefijoForm)
{
    if(Ext.isEmpty(strLimiteLatitudSur) || Ext.isEmpty(strLimiteLatitudNorte) 
        || Ext.isEmpty(strLimiteLongitudOeste) || Ext.isEmpty(strLimiteLongitudEste))
    {
        Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Los límites del país en sesión no están ingresados. Favor comunicarse con el Dpto. de Sistemas! ',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
        return false;
    }
    
    grados_la_nuevos = parseFloat(formulario.grados_la.value);
    minutos_la_nuevos = parseFloat(formulario.minutos_la.value);
    segundo_la_nuevos = parseFloat(formulario.segundos_la.value);
    decimas_la_nuevos = parseFloat(formulario.decimas_segundos_la.value);
    latitud_la_nuevo = formulario.latitud[formulario.latitud.selectedIndex].value;

    valor_latitud = gms2dec(grados_la_nuevos, minutos_la_nuevos, segundo_la_nuevos, decimas_la_nuevos, latitud_la_nuevo);

    grados_lo_nuevos = parseFloat(formulario.grados_lo.value);
    minutos_lo_nuevos = parseFloat(formulario.minutos_lo.value);
    segundo_lo_nuevos = parseFloat(formulario.segundos_lo.value);
    decimas_lo_nuevos = parseFloat(formulario.decimas_segundos_lo.value);
    longitud_lo_nuevo = formulario.longitud[formulario.longitud.selectedIndex].value;
    
    valor_longitud = gms2dec(grados_lo_nuevos, minutos_lo_nuevos, segundo_lo_nuevos, decimas_lo_nuevos, longitud_lo_nuevo);
    if (!(valor_latitud >= parseFloat(strLimiteLatitudSur) && valor_latitud <= parseFloat(strLimiteLatitudNorte)))
    {
        Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'La latitud no se encuentra dentro del territorio '+strRangoPais,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
        return false;
    }
    if (!(valor_longitud >= parseFloat(strLimiteLongitudOeste) && valor_longitud <= parseFloat(strLimiteLongitudEste)))
    {
        Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'La longitud no se encuentra dentro del territorio '+strRangoPais,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
        return false;
    }
    $("#"+prefijoForm+"_latitudUbicacion").val(valor_latitud);
    $("#"+prefijoForm+"_longitudUbicacion").val(valor_longitud);
    return true;
}


/**
 *  Función que sirve para convertir la longitud y latitud de grados a decimales y se valida que las coordenadas se encuentren dentro de 
 *  los límites definidos a nivel de sesión de acuerdo al país al seleccionar la coordenada desde el mapa
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 20-09-2018
 * 
 */
function validarCoordenadasPorPaisMapa()
{
    if(Ext.isEmpty(strLimiteLatitudSur) || Ext.isEmpty(strLimiteLatitudNorte) 
        || Ext.isEmpty(strLimiteLongitudOeste) || Ext.isEmpty(strLimiteLongitudEste))
    {
        Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Los límites del país en sesión no están ingresados. Favor comunicarse con el Dpto. de Sistemas! ',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
        return false;
    }
    
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
    
    if (!(valor_latitud >= parseFloat(strLimiteLatitudSur) && valor_latitud <= parseFloat(strLimiteLatitudNorte)))
    {
        Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'La latitud no se encuentra dentro del territorio '+strRangoPais,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
        return false;
    }
    if (!(valor_longitud >= parseFloat(strLimiteLongitudOeste) && valor_longitud <= parseFloat(strLimiteLongitudEste)))
    {
        Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'La longitud no se encuentra dentro del territorio '+strRangoPais,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
        return false;
    }
    
    objTxtLatitudUbicacion.setValue(valor_latitud);
    objTxtLongitudUbicacion.setValue(valor_longitud);
          
    return true;
}
