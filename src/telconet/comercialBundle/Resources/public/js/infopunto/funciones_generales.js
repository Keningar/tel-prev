
function replaceAll( text, busca, reemplaza )
{
    while (text.toString().indexOf(busca) != -1)
    text = text.toString().replace(busca,reemplaza);
    return text;
}

function validaSoloNumeros(e) 
{
     tecla = (document.all) ? e.keyCode : e.which;
    //Tecla de retroceso para borrar, siempre la permite
  
    if (tecla==8)
    {
        return true;
    }       
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9\.]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final); 
}

function validaNumerosConDecimales(e, field) 
{
    var key = e.keyCode ? e.keyCode : e.which;

    if (key == 8) return true;

    if (key > 47 && key < 58)
    {
        if (field.value == "") return true;
        
        var existePto = (/[.]/).test(field.value);
        if (existePto === false)
        {
            regexp = /.[0-9]{10}$/;
        }
        else 
        {
            regexp = /.[0-9]{2}$/;
        }
        
        return !(regexp.test(field.value));
    }

    if (key == 46)
    {
        if (field.value == "") return false;
        var regexp = /^[0-9]+$/;
        return regexp.test(field.value);
    }

    return false;
}

function validarGridRegistrosNuevos()
{    
    var contadorServiciosNuevos = 0;
    
    for (var i = 0; i < gridDetalle.getStore().getCount(); i++)
    {
        var idServicio = gridDetalle.getStore().getAt(i).data.idServicio;        
        
        if(idServicio === '0')
        {
            contadorServiciosNuevos++;
        }
    }  
    
    if(contadorServiciosNuevos > 0)
    {
        Ext.getCmp('btnEditarSolucion').setDisabled(false);
    }
    else
    {
        Ext.getCmp('btnEditarSolucion').setDisabled(true);
    }
}

function renderizarInformacionServicio(array,esMultipleCaracteristica,data)
{
    if(esMultipleCaracteristica === 'N')
    {
        var allInputs = $( ":input" );
                    
        $.each(array, function(i, item)
        {
            var caract = item.descripcion;
            var valor  = item.valor;
            var tipo   = item.tipoIngreso;

            $.each(allInputs,function(i,item)
            {
                var value = $(this).attr("value");

                if(value === "["+caract+"]")
                {
                    var idGenerico   = $(this).attr("id");

                    var arrayCaract  = idGenerico.split("caracteristica_nombre_");

                    var idCaract     = "#caracteristicas_"+arrayCaract[1];

                    $(idCaract).val(valor);

                    //Si es seleccionable se hace resfresh al combo
                    if(tipo === 'S')
                    {
                        //$(idCaract).selectmenu("refresh");
                    }
                }
            });
        });
    }
    else//Precargar informacion en el grid de multiple caracteristica
    {
        //Calcular los precios para cada registro de caracteristica
        $.each(array, function(i, item)
        {
            rowEditingRecursos.cancelEdit();
            if(data.esLicencia)
            {
                var recordParamDetLic = Ext.create('recursosModel', {
                    idRaw          : item.id,
                    maquinaVirtual : item.idMaquina,
                    tipoRecurso    : item.descripcion,
                    caracteristica : item.valor,
                    cantidad       : item.valorCaract,
                    idServicio     : item.idServicio,
                    precioUnitario : '',
                    precioNegociado: '',
                    descuento      : '',
                    precioTotal    : '',
                    hdvalor        : 0,
                    hdPorcentaje   : 0,
                    esAntiguo      : true
                });
                storeRecursosCaracteristicas.insert(0, recordParamDetLic);
                recordParamDet = recordParamDetLic;
            }
            else
            {
                var recordParamDetNorm = Ext.create('recursosModel', {
                    idRaw          : item.id,
                    tipoRecurso    : item.descripcion,
                    caracteristica : item.valor,
                    cantidad       : item.valorCaract,
                    idServicio     : item.idServicio,
                    precioUnitario : '',
                    precioNegociado: '',
                    descuento      : '',
                    precioTotal    : '',
                    hdvalor        : 0,
                    hdPorcentaje   : 0,
                    esAntiguo      : true
                });
                storeRecursosCaracteristicas.insert(0, recordParamDetNorm);
                recordParamDet = recordParamDetNorm;

            }

            var storeCaracteristicas = [];

            $.each(data.arrayJsonCaractMultiple, function(i, itemCaract) 
            {
                if(itemCaract.tipoCaracteristica === item.descripcion)
                {
                    storeCaracteristicas = getStoreCaracteristicas(itemCaract.arrayCaracteristica);
                    return false;
                }
            });

            Ext.getCmp('cmbCaracteristica').bindStore(storeCaracteristicas);

            var json = {};
            json['idRaw']          = item.id;
            json['tipoRecurso']    = item.descripcion;
            json['caracteristica'] = item.valor;
            json['cantidad']       = item.valorCaract;
            json['descuento']      = item.descuento;
            json['idServicio']     = item.idServicio;
            json['esAntiguo']      = true;

            if (data.esLicencia) {
                json['idMaquinas']  = [item.idMaquina];
                json['idLicencias'] = [item.id];
            } else {
                json['idMaquinas'] = [];
            }

            arrayRecursosHosting.push(json);

            //Obtenemos los valores totales del pool de recurso.
            if (item.descripcion == 'DISCO') {
                storageTotal =  storageTotal + parseInt(item.valorCaract, 10);
            } else if(item.descripcion == 'PROCESADOR') {
                procesadorTotal = procesadorTotal + parseInt(item.valorCaract, 10);
            } else {
                memoriaTotal = memoriaTotal + parseInt(item.valorCaract, 10);
            }

            var valorNegociado = '';
            //Calcular el precio segun el porcentaje de descuento aplicado
            if(!Ext.isEmpty(item.descuento))
            {
                var funcion_precio = $("#funcion_precio").val();
                funcion_precio     = replaceAll(funcion_precio, "["+item.descripcion+"]", item.valor);
                var precioUnitario = eval(funcion_precio);

                //Se obtiene el precio negociado usando el porcentaje de descuento generado
                valorNegociado = (((100 - parseFloat(item.descuento)) * parseFloat(precioUnitario))/100).toFixed(2);
            }

            jsonPreciosMulticaracteristicas = calcularPrecioMultiCaracteristica(item.descripcion,
                                                                                item.valor,
                                                                                item.valorCaract,
                                                                                valorNegociado);

            recordParamDet.set("precioUnitario",  jsonPreciosMulticaracteristicas['precioUnitario']);
            recordParamDet.set("precioNegociado", jsonPreciosMulticaracteristicas['precioNegociado']);
            recordParamDet.set("descuento",       jsonPreciosMulticaracteristicas['descuento']);
            recordParamDet.set("precioTotal",     jsonPreciosMulticaracteristicas['precioTotal']);
            recordParamDet.set("hdValor",         jsonPreciosMulticaracteristicas['hdValor']);
            recordParamDet.set("hdPorcentaje",    jsonPreciosMulticaracteristicas['hdPorcentaje']);
            recordParamDet.set("idRaw",           item.id);
            json['descuento'] =                   jsonPreciosMulticaracteristicas['hdPorcentaje'];

        });

        jsonTotales             = {};
        arrayDetalleStorage     = [];
        arrayDetalleProcesador  = [];
        arrayDetalleMemoria     = [];

        arrayRecursosHosting.forEach(function(recursoHosting){
            if (recursoHosting.tipoRecurso == 'DISCO'){
                arrayDetalleStorage.push(convertToArrayRecursos(recursoHosting));
            }else if(recursoHosting.tipoRecurso == 'PROCESADOR'){
                arrayDetalleProcesador.push(convertToArrayRecursos(recursoHosting));
            }else if(recursoHosting.tipoRecurso == 'MEMORIA RAM'){
                arrayDetalleMemoria.push(convertToArrayRecursos(recursoHosting));
            }
        });

        jsonTotales['arrayDetalleDisco']      = arrayDetalleStorage;
        jsonTotales['arrayDetalleProcesador'] = arrayDetalleProcesador;
        jsonTotales['arrayDetalleMemoria']    = arrayDetalleMemoria;
        arrayRecursos = jsonTotales;
        arrayResumenGeneralRecursos = [];

        $.each(arrayRecursos.arrayDetalleDisco, function(i, item)
        {
            var json = {};                
            json['idRecurso'] = item.idRecurso;
            json['tipo']      = 'DISCO';
            json['total']     = item.valor;
            json['disponible']= item.valor;
            json['usado']     = 0;
            arrayResumenGeneralRecursos.push(json);
        });

        $.each(arrayRecursos.arrayDetalleProcesador, function(i, item)
        {
            var json = {};
            json['idRecurso'] = item.idRecurso;
            json['tipo']      = 'PROCESADOR';
            json['total']     = item.valor;
            json['disponible']= item.valor;
            json['usado']     = 0;
            arrayResumenGeneralRecursos.push(json);
        });

        $.each(arrayRecursos.arrayDetalleMemoria, function(i, item)
        {
            var json = {};
            json['idRecurso'] = item.idRecurso;
            json['tipo']      = 'MEMORIA RAM';
            json['total']     = item.valor;
            json['disponible']= item.valor;
            json['usado']     = 0;
            arrayResumenGeneralRecursos.push(json);
        });
    }
}

function limpiarPanel()
{
    $("#content-editar-producto").find("tr").remove();
    $("#content-editar-producto").find("div").remove();
    $("#content-editar-producto").find("br").remove();
    $('#content-editar-producto').find("td").remove();
}

function agruparLicencias(array)
{
    var arrayTemp            = [];
    var boolFlag             = true
    var jsonInformacionMv    = buscarLicenciasEnMV();
    var idsMVs               = jsonInformacionMv['idsMVs'];
    var arrayAcumulaLicencia = jsonInformacionMv['arrayLicencias'];

   //array = array.filter(element => !idsMVs.includes(element.id) && Ext.isEmpty(element.idMaquina));
    if (idsMVs.length > 0) {
        array = array.filter(element => element.idMaquina === null);
    }

    while(boolFlag)
    {
        if(array.length > 0)
        {
            arrayTemp = array.filter(function(elem){
                return elem.descripcion == array[0].descripcion &&
                        elem.valor      == array[0].valor
            });

            var json = {};
            json['id']             = array[0].id;
            json['maquinaVirtual'] = '';
            json['descripcion']    = array[0].descripcion;
            json['valor']          = array[0].valor;
          //json['valorCaract']    = arrayTemp.length;
            json['valorCaract']    = array[0].valorCaract;
            json['idServicio']     = array[0].idServicio;
            json['idLicencias']    = arrayTemp.map(elem => elem.idRaw);
       
            arrayAcumulaLicencia.push(json);
            array = array.filter(function(elem) {
                return elem.descripcion != array[0].descripcion &&
                       elem.valor       != array[0].valor
            }); 
        }
        else
        {
            boolFlag = false;
        }

    }

    return arrayAcumulaLicencia;
}







