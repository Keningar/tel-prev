function calcularValoresProductoIsb(data)
{
    var velocidad       = Ext.getCmp('comboVelocidadesIsb').value;
    var funcionPrecio   = data.funcionPrecio;
    var caracteristicas = data.caracteristicas;
        
    if(!Ext.isEmpty(velocidad))
    {
        if(data.nombreTecnico=='TELCOHOME')
        {
            caracteristicas["[VELOCIDAD_TELCOHOME]"]      = velocidad;
        }
        if(data.descripcion=='INTERNET SAFE')
        {
            caracteristicas["[VELOCIDAD_INTERNET_SAFE]"]  = velocidad;
        }
        caracteristicas["[VELOCIDAD]"]      = velocidad;
        caracteristicas["[Grupo Negocio]"]  = 'PYMETN';
        jsonCaracteristicasIsb              = generarJsonCaracteristicasIsb(caracteristicas,data.refCaracteristicas);
        
        for (var caracteristica in caracteristicas)
        {
            funcionPrecio   = replaceAllIsb(funcionPrecio, caracteristica, caracteristicas[caracteristica]);
        }
        
        var precioProducto      = eval(funcionPrecio);
        var respuestaPrecios= new Array();
        respuestaPrecios["precioISB"]   = precioProducto;
        return respuestaPrecios;
    }
}


function generarJsonCaracteristicasIsb(caracteristicas , refCaracteristicas)
{
    var arrayCaracteristicas = [];
        
    for(var refCaract in refCaracteristicas)
    {
        var jsonData = {};

        jsonData['idCaracteristica'] = refCaracteristicas[refCaract].idCaracteristica;

        for (var caracteristica in caracteristicas)
        {
            if(caracteristica.indexOf(refCaracteristicas[refCaract].caracteristica)!== -1)
            {
                jsonData['valor']       = caracteristicas[caracteristica];
                jsonData['descripcion'] = caracteristica;
            }
        }
        arrayCaracteristicas.push(jsonData);            
    }

    jsonCaracteristicasInternetLite = Ext.JSON.encode(arrayCaracteristicas);
    
    return jsonCaracteristicasInternetLite;
}

function replaceAllIsb( text, busca, reemplaza )
{
    while (text.toString().indexOf(busca) !== -1)
    text = text.toString().replace(busca,reemplaza);
    return text;
}