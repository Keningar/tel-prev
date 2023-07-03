//Función que obtiene todos los parametros de las licencias
function getLicencias()
{    
    $.ajax({
        type   : "GET",
        url    : urlajaxGetLicencias,
        timeout: 600000,
        datatype: 'json',               
        success: function(data)
        {
            if(data.status == 'OK')
            {
                arrayParametrosLicencias = data.licenciasParametros; 
            }
        }, 
        error : function(data){
        },
        complete: function(data){
        }        
    });    
}

//Función que valida las licencias 
function validarLicencia(num_core, nombre)
{
    var numeroLicencia         = 0; 
    var esRHLargeInstance      = false;
    var jsonRespuestaLicencias = {};
    jsonRespuestaLicencias['bloqueo'] = null;

    if (Array.isArray(arrayValidaLicencias))
    {
        for (let i = 0; i < arrayValidaLicencias.length ; i++)
        {
            if(arrayValidaLicencias[i]['strDescripcionDet'] == nombre)
            {
                if (arrayValidaLicencias[i]['strValor1'] <= num_core &
                arrayValidaLicencias[i]['strValor2'] <= num_core &
                arrayValidaLicencias[i]['strValor1'] <  arrayValidaLicencias[i]['strValor2'] &
                arrayValidaLicencias[i]['strValor3'] != null     & 
                arrayValidaLicencias[i]['strValor4'] != null)
                {                   
                    //SQL Server
                    if(num_core % arrayValidaLicencias[i]['strValor4'] == 0)
                    {                        
                        numeroLicencia = num_core / arrayValidaLicencias[i]['strValor4'] ;
                        jsonRespuestaLicencias['numeroLicencia'] = numeroLicencia;
                        jsonRespuestaLicencias['bloqueo'] = arrayValidaLicencias[i]['strValor5'];
                        return jsonRespuestaLicencias;
                    }
                    else
                    {
                        numeroLicencia = (parseInt(num_core) -(num_core % arrayValidaLicencias[i]['strValor4'])
                                         + parseInt(arrayValidaLicencias[i]['strValor4'])) / arrayValidaLicencias[i]['strValor4'];
                        jsonRespuestaLicencias['bloqueo'] = arrayValidaLicencias[i]['strValor5'];
                        jsonRespuestaLicencias['numeroLicencia'] = numeroLicencia;
                        return jsonRespuestaLicencias;
                    }                            
                }
                //Condición licencias Tipo RED HAT
                else if(arrayValidaLicencias[i]['strValor1']  > arrayValidaLicencias[i]['strValor2']      & 
                        !arrayValidaLicencias[i]['strValor3']      & 
                        !arrayValidaLicencias[i]['strValor4'] )
                {
                        //Cuando es Large  es <= 4                        
                        nombre = arrayValidaLicencias[i]['strDescripcionDet'];
                        numeroLicencia = numeroLicencia + 1;
                        jsonRespuestaLicencias['esRHLargeInstance'] = true; 
                        jsonRespuestaLicencias['nombre'] = nombre; 
                        jsonRespuestaLicencias['numeroLicencia'] = numeroLicencia;
                        jsonRespuestaLicencias['bloqueo'] = arrayValidaLicencias[i]['strValor5'];
                        jsonRespuestaLicencias['numeroCoreReq']  = arrayValidaLicencias[i]['strValor1'] ;
                        return jsonRespuestaLicencias;

                }
                else if(arrayValidaLicencias[i]['strValor1'] <   arrayValidaLicencias[i]['strValor2'] & 
                        !arrayValidaLicencias[i]['strValor3']      & 
                        !arrayValidaLicencias[i]['strValor4'] )
                {
                        //Cuando es RHT Small Instance                       
                        nombre = arrayValidaLicencias[i]['strDescripcionDet'];
                        numeroLicencia = numeroLicencia + 1;
                        jsonRespuestaLicencias['esRHSmallInstance'] = true; 
                        jsonRespuestaLicencias['nombre'] = nombre;
                        jsonRespuestaLicencias['numeroLicencia'] = numeroLicencia;
                        jsonRespuestaLicencias['numeroCoreReq']  = arrayValidaLicencias[i]['strValor2'] ;
                        jsonRespuestaLicencias['bloqueo'] = arrayValidaLicencias[i]['strValor5'];
                        return jsonRespuestaLicencias;

                }
                //Segundo bloque licencias SQL SERVER
                else if (arrayValidaLicencias[i]['strValor1'] <= num_core &
                arrayValidaLicencias[i]['strValor2'] >= num_core &
                arrayValidaLicencias[i]['strValor1'] <  arrayValidaLicencias[i]['strValor2'])
                {
                    numeroLicencia = arrayValidaLicencias[i]['strValor3'];
                    jsonRespuestaLicencias['numeroLicencia'] = numeroLicencia;
                    jsonRespuestaLicencias['bloqueo'] = arrayValidaLicencias[i]['strValor5'];
                    return jsonRespuestaLicencias;
                }
                //Licencias Por Número de Core - //Windows Server
                else if (!arrayValidaLicencias[i]['strValor1']      &  
                        !arrayValidaLicencias[i]['strValor2']      & 
                        !arrayValidaLicencias[i]['strValor3'])                
                {                    
                    numeroLicencia = num_core;
                    jsonRespuestaLicencias['numeroLicencia'] = numeroLicencia;
                    jsonRespuestaLicencias['bloqueo'] = arrayValidaLicencias[i]['strValor5'];
                    return jsonRespuestaLicencias;
                }            
            }
        }
    }

    jsonRespuestaLicencias['numeroLicencia'] = numeroLicencia;
    return jsonRespuestaLicencias;        
}

//Función que agrupa licencias dentro de la pantalla de nuevo
function agruparLicenciasNuevo(json)
{
    var arrayAcumulaLicencia = [];
    var array = JSON.parse(json);
    var boolFlag = true;
    
    while(boolFlag)
    {
        var idMaquinaVirtual = array[0].idMaquinas[0];
        arrayTemp = array.filter(function(elem){
        return  array[0].idRaw == elem.idRaw
        });
        var jsonNew = {};

        jsonNew['idRaw']           = array[0].idRaw;
        jsonNew['maquinaVirtual']  = idMaquinaVirtual;
        jsonNew['tipoRecurso']     = array[0].tipoRecurso;                    
        jsonNew['caracteristica']  = array[0].caracteristica;
        jsonNew['precioNegociado'] = array[0].precioNeg;
        jsonNew['cantidad']        = arrayTemp.length;
        jsonNew['idLicencias']     = arrayTemp.map(elem =>   elem.idRaw);

        arrayAcumulaLicencia.push(jsonNew);

        array = array.filter(function(elem){
        return  array[0].idRaw != elem.idRaw
        });


        if(array.length < 1)
        {
            boolFlag = false;
        }
    }   
    return arrayAcumulaLicencia;     
}

//Valida que exista máquina dentro.
function validarMVPoolRecursos(idRecurso){
    var jsonRecursoEncontrado = {};
    jsonRecursoEncontrado['existeRecurso']= false;

    $.each(arrayInformacion, function(index, maquina){
        var arrayRecursos = JSON.parse(maquina.arrayRecursos);//Json recursos existentes
        recurso = arrayRecursos.find(recurso => recurso.idRecurso == idRecurso);
        if(recurso)
        {
            jsonRecursoEncontrado['existeRecurso'] = true;
            jsonRecursoEncontrado['nombreMaquina'] = maquina.nombre;
        }
    });
    return jsonRecursoEncontrado;

}

//Valida que exista licencias dentro de una MV
function validaExistMVLic(idRecurso)
{
    var jsonRecursoEncontrado = {};
    $.each(arrayMaquinasVirtuales, function(index, maquinasVirtuales){
        $.each(maquinasVirtuales.maquinasVirtuales, function(index, maquina){
        var arrayRecursos = JSON.parse(maquina.arrayRecursos);
        recurso = arrayRecursos.find(recurso => recurso.idRecurso = idRecurso);
        if(recurso)
        {
            jsonRecursoEncontrado['existeRecurso'] = true;
            jsonRecursoEncontrado['nombreMaquina'] = maquina.nombre;
        }else
        {
            jsonRecursoEncontrado['existeRecurso']= false;
        }
        });        
    });    
    return jsonRecursoEncontrado;
}

//Añade licencias al array Información 
function addLicenciasToArrayInformacion(itemsLicencias)
{
    boolEsEditarSolucion = typeof boolEsEditarSolucion !== 'undefined' && boolEsEditarSolucion;
    intIdServicio        = typeof intIdServicio        !== 'undefined' && intIdServicio !== null ? intIdServicio : null;
    nuevaSolucion        = typeof nuevaSolucion        !== 'undefined' && nuevaSolucion;

    var arrayMaquinasAdd = [];
    $.each(itemsLicencias,function(i,item)
    {
        var idMaquinavirtual = item.data.maquinaVirtual;
        arrayMaquinasAdd.push(idMaquinavirtual);        
        if(idMaquinavirtual >= 0) 
        {
            $.each(arrayMaquinasVirtuales, function(i, arrayMVs)
            {
                $.each(arrayMVs.maquinasVirtuales,function(j, jsonMVs)
                {
                    if(jsonMVs.idMaquina == idMaquinavirtual)
                    {
                        var jsonRecursos = JSON.parse(jsonMVs.arrayRecursos);//Json recursos existentes
                        var json = {};

                        if (boolEsEditarSolucion) {
                            if (intIdServicio !== null) {
                                json['idServicio'] = intIdServicio; //Pool Existente
                            } else {
                                json['idServicio'] = 0;             //Pool Nuevo
                            }
                        }

                        json['secuencial']      = secuencial;
                        json['tipo']            = item.data.tipoRecurso;
                        json['idRecurso']       = String(item.data.idRaw);
                        json['caracteristica']  = item.data.caracteristica;
                        json['valor']           = 1;
                        json['disponible']      = 0;
                        json['asignar']         = 1;
                        json['idDetalle']       = item.idRaw;
                        for (var l = 0; l < item.data.cantidad; l++)
                        {
                            jsonRecursos.push(json);
                        }
                        arrayRecursosLic = Ext.JSON.encode(jsonRecursos);
                        jsonMVs.arrayRecursos = [];
                        jsonMVs.arrayRecursos = arrayRecursosLic;
                    }
                });
            });
        }
    }); 
    return arrayMaquinasAdd;
}

function searchMaquinasInvolved(itemsLicencias)
{
    var arrayMaquinasAdd = [];
    $.each(itemsLicencias,function(i,item)
    {
        if(item.data.tipoRecurso == 'SISTEMA OPERATIVO')
        {
            arrayMaquinasAdd.push(item.data.maquinaVirtual);
        }
            
    });
    return arrayMaquinasAdd;
}
function limpiarArrays()
{
    arrayCambioMaquina       = [];
    arrayInformacion         = [];
    arrayInformacionOld      = [];
    arrayInformacion         = [];
    arrayLicenciasEditadas   = [];
    arrayLicenciasEliminadas = [];
    arrayMaquinasVirtuales   = [];
    arrayCambioMaquina       = [];
    arrayCambioMaquina       = [];
    arrayRecursoEliminados   = [];
    arrayRecursos            = [];
    arrayRecursoTmp          = [];
    arrayRecursosConf        = [];
    arrayRecursosHosting     = [];
    arraySolucion            = [];
}

function deleteLicenciasToArrayInformacion(Itemslicencia)
{
    if(accion != 'editar' && accion != 'agregar' )
    {
        $.each(Itemslicencia,function(i,licenciadata)
        {
            var licencia = licenciadata.data;
            var idMaquinaVirtual = (licencia.maquinaVirtual >= 0) ? licencia.maquinaVirtual : null;
            var idLicencias = licencia.idRaw;
            var tipoRecurso = licencia.tipoRecurso;
            var caracteristicaRecurso = licencia.caracteristica;
            if(idMaquinaVirtual != null)
            {
                $.each(arrayMaquinasVirtuales, function(i, arrayMVs)
                {        
                    $.each(arrayMVs.maquinasVirtuales,function(j, jsonMVs)
                    {
                        if(jsonMVs.idMaquina == idMaquinaVirtual)
                        {
                            var jsonRecursos = JSON.parse(jsonMVs.arrayRecursos);//Json recursos existentes
                            jsonRecursos = jsonRecursos.filter(function(elemento){
                                return parseInt(elemento.idRecursos) !== idLicencias 
                                && elemento.caracteristica != caracteristicaRecurso 
                                && elemento.tipo != tipoRecurso 
                            });
                            arrayRecursosLic = Ext.JSON.encode(jsonRecursos);
                            jsonMVs.arrayRecursos = [];
                            jsonMVs.arrayRecursos = arrayRecursosLic;
                        }
                    });

                });
            }

        });
    }
              
}




// Busca el número de cors que existe en una máquina
function buscarNumeroCore(idMaquina)
{
    var numCore = 0;    
    $.each(arrayMaquinasVirtuales, function(i, item)
    {
        $.each(item.maquinasVirtuales,function(j, mvs)
        {            
            if(mvs.idMaquina === idMaquina )
            {
               numCore = mvs.procesador;
               return false;               
            }
        });                       
    });
    return  numCore;
}

//Transforma array a un StoreCaracterísticasMv
function getStoreCaracteristicasMV(arrayOriginal)
{
    var array = [];    
    $.each(arrayOriginal, function(i, item) 
    {
        var json      = {};
        json['id']    = item.idRecurso;
        json['value'] = item.nombreRecurso;
        array.push(json);        
    });
    
    var store = new Ext.data.Store({
        fields: ['id','value'],
        data: array
    });
    
    return store;
}


function convertToArrayInformacion(arrayInformacionTemp)
{
    arrayTempMaquinas = [];
    $.each(arrayInformacionTemp, function (index, mvTemp)
        {
            //Se convierte y se hace push y devuelve el total  de cada uno de los recursos
            var disco = 0;
            var memoria = 0;
            var procesador = 0;

            disco      = convertToArrayTemp(mvTemp.arrayDetalleDisco);
            memoria    = convertToArrayTemp(mvTemp.arrayDetalleMemoria);
            procesador = convertToArrayTemp(mvTemp.arrayDetalleProcesador);
            convertToArrayTemp(mvTemp.arrayDetalleLicencia);

            recursos = Ext.JSON.encode(arrayRecursoTmp);
            var json = {};
            //AGREGAMOS EL ID DEL SERVICIO PARA DIFERENCIAR LAS MAQUINAS
            //VIRTUALES A QUE SERVICIO PERTENECE.
            json['idServicio']    = mvTemp.arrayInfoGeneral.idServicio;
            json['nombre']        = mvTemp.arrayInfoGeneral.nombreElemento;
            json['arrayRecursos'] = recursos;
            json['storage']       = disco;
            json['memoria']       = memoria;
            json['procesador']    = procesador;
            json['so']            = 'N/D';
            json['carpeta']       = "N/D";
            json['tarjeta']       = "N/D";        
            json['idMaquina']     = mvTemp.arrayInfoGeneral.idElemento;      

            arrayTempMaquinas.push(json);
            recursos = '';
            arrayRecursosConf = [];
            arrayRecursoTmp = [];
        }
    );
    return arrayTempMaquinas;     
}

function convertToArrayTemp(arrayDetalle)
{
    var total = 0;
    var tipo  = '';
    var caracteristica = '';
    var nombreRecurso  = '';

    $.each(arrayDetalle, function (index, detalle)
        {
            if(typeof (detalle) != 'undefined' &&  detalle != null)
            {
                if(detalle.tipo == 'SISTEMA_OPERATIVO')
                {
                    nombreRecurso = detalle.nombreRecurso.split('@');
                    tipo = nombreRecurso[0];
                    caracteristica = nombreRecurso[1];
                }
                else
                {
                    tipo = detalle.tipo;
                    caracteristica = detalle.nombreRecurso;
                }
                var json               = {};
                json['idServicio']     = detalle.idServicio;
                json['tipo']           = tipo ;
                json['idRecurso'     ] = detalle.idRecurso;
                json['caracteristica'] = caracteristica;
                json['valor'         ] = detalle.valor;
                json['disponible'    ] = detalle.valor;
                json['asignar'       ] = detalle.usado;
                json['datastore'     ] = detalle.valorCaracteristica;   
                json['idDetalle'     ] = detalle.idDetalle;
                arrayRecursosConf.push(json);
                arrayRecursoTmp.push(json);
                total = total + detalle.usado;
            }
        }
        ); 
    return total;
}

function convertToArrayRecursos(array){   
    var arrayRecursos = {};
    arrayRecursos['rownum']              = array.idRaw;
    arrayRecursos['idRecurso']           = array.idRaw;
    arrayRecursos['nombreRecurso']       = array.caracteristica;
    arrayRecursos['valor']               = array.cantidad;
    arrayRecursos['valorCaracteristica'] = null;
    arrayRecursos['refPadre']            = null;
    return arrayRecursos;
}

//Busca la maquina virtual a la cual pertenece la licencia
//function buscarMaquinaLicencia(descripcion, valor)
//{
//    var recurso = '';
//    $.each(arrayMaquinasVirtuales, function (index, maquinasVirtuales){
//        $.each(maquinasVirtuales.maquinasVirtuales, function (index, maquinas){
//            arrayRecursos = JSON.parse(maquinas.arrayRecursos);
//            recurso = arrayRecursos.find(recurso => recurso.descripcion == descripcion && recurso.valor  == valor);
//
//        });
//    });
//    return recurso;
//}

function buscarMaquinaLicencia(idLicencia)
{
    var nombre = '';
    $.each(arrayMaquinasVirtuales, function (index, maquinasVirtuales){
        $.each(maquinasVirtuales.maquinasVirtuales, function (index, maquinas){
            arrayRecursos = JSON.parse(maquinas.arrayRecursos);
            recurso = arrayRecursos.find(recurso => recurso.idRecurso == idLicencia && 
                                                    (recurso.tipo == 'SISTEMA OPERATIVO' &&
                                                     recurso.tipo == 'BASE DE DATOS' &&
                                                     recurso.tipo == 'APLICACIONES' &&
                                                     recurso.tipo == 'OTROS'
                                                    ));
            if(recurso)
            {
                nombre = maquinas.idMaquina; 
            }
        });
    });
    return nombre;
}

function buscarLicenciasEnMV()
{
    var jsonInformacionMv = {};
    var arrayLicencias    = [];
    var arrayTemp         = [];
    var idsMVs            = [];
    var arrayRecursos2    = [];
    var arrayRecursos     = [];
    boolEsEditarSolucion  = typeof boolEsEditarSolucion !== 'undefined' && boolEsEditarSolucion;
    intIdServicio         = typeof intIdServicio        !== 'undefined' && intIdServicio !== null ? intIdServicio : null;

    $.each(arrayMaquinasVirtuales, function (index, maquinasVirtuales)
    {
        $.each(maquinasVirtuales.maquinasVirtuales, function (index, maquinas)
        {
            arrayRecursos  = JSON.parse(maquinas.arrayRecursos);
            arrayRecursos2 = JSON.parse(maquinas.arrayRecursos).filter(recurso => recurso.tipo != 'MEMORIA RAM' &&
                                                                                  recurso.tipo != 'PROCESADOR'  &&
                                                                                  recurso.tipo != 'DISCO');

            $.each(arrayRecursos, function (index, recurso)
            {
                if (boolEsEditarSolucion && intIdServicio !== null ) {
                    arrayTemp =  arrayRecursos2.filter(recurso2 => recurso.tipo           == recurso2.tipo &&
                                                                   recurso.caracteristica == recurso2.caracteristica &&
                                                                   recurso.idServicio     == intIdServicio &&
                                                                   (recurso.tipo == 'SISTEMA OPERATIVO' ||
                                                                    recurso.tipo == 'BASE DE DATOS'     ||
                                                                    recurso.tipo == 'APLICACIONES'      ||
                                                                    recurso.tipo == 'OTROS'));
                } else {
                    arrayTemp =  arrayRecursos2.filter(recurso2 => recurso.tipo           == recurso2.tipo &&
                                                                   recurso.caracteristica == recurso2.caracteristica &&
                                                                   (recurso.tipo == 'SISTEMA OPERATIVO' ||
                                                                    recurso.tipo == 'BASE DE DATOS'     ||
                                                                    recurso.tipo == 'APLICACIONES'      ||
                                                                    recurso.tipo == 'OTROS'));
                }

                if(arrayTemp.length > 0)
                {
                    var json = {};                      
                    json['id']             = arrayTemp[0].idRecurso;
                    json['maquinaVirtual'] = maquinas.idMaquina;
                    json['descripcion']    = arrayTemp[0].tipo;
                    json['valor']          = arrayTemp[0].caracteristica;
                  //json['valorCaract']    = arrayTemp.length;
                    json['valorCaract']    = arrayTemp[0].asignar;
                    json['idServicio']     = arrayTemp[0].idServicio;
                    json['idLicencias']    = arrayTemp.map(elem => elem.idRecurso);

                    idsMVs = idsMVs.concat(arrayTemp.map(elem => elem.idRecurso));
                    arrayLicencias.push(json);
                    arrayRecursos2 = arrayRecursos2.filter(recurso2 => recurso.caracteristica != recurso2.caracteristica &&
                                                                       (recurso.tipo == 'SISTEMA OPERATIVO' ||
                                                                        recurso.tipo == 'BASE DE DATOS'     ||
                                                                        recurso.tipo == 'APLICACIONES'      ||
                                                                        recurso.tipo == 'OTROS'));
                    arrayTemp= [];
                }
            });

        });
    });
    jsonInformacionMv['idsMVs'] = idsMVs;
    jsonInformacionMv['arrayLicencias'] = arrayLicencias;
    return jsonInformacionMv;
}

function eliminarDependenciasMaquinaVirtual(idMaquina)
{
    $.each(arraySolucion, function(index,solucion)
    {
        caracteristicasPoolRecursos = JSON.parse(solucion.caracteristicasPoolRecursos);
        $.each(caracteristicasPoolRecursos, function(index,caracteristica){
            $.each(caracteristica.idMaquina, function(index, idMaquina)
            {
                solucion.caracteristicasPoolRecursos = JSON.stringify(array.filter(function(elem)
                {
                    return parseInt(elem.idRecurso) !== parseInt(registro);
                }));
            });           

        });
    });   
    return array;    
}

function guardarMVPoolIndividual()
{
    //Guardar Informacion de maquinas virtuales
    var arrayMaquinasVirtuales = arrayInformacion.filter(maquina => maquina.esNuevo);

    if (arrayMaquinasVirtuales.length > 0) {

        Ext.Ajax.request({
            url     :  urlGuardarMaquinasVirtuales,
            timeout :  600000,
            method  : 'post',
            params  :
            {
                'idServicio' : idServicioEditado,
                'idVCenter'  : '',
                'data'       : Ext.JSON.encode(arrayMaquinasVirtuales)
            },
            success: function(response)
            {
                ajaxConsultarSoluciones(solucionEditada);

            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ','Error: ' + result.statusText);
            }
        });

    }
}

function ajaxEditarEliminarMaquinaVirtualComercial(tipo, json, jsonActual)
{
    Ext.Msg.wait('Eliminando maquina virtual...');
    Ext.Ajax.request({
        url     :  urlActualizarMaquinasVirtuales,
        method  : 'post',
        timeout :  600000,
        params  :
        {
            'idElemento'     : json.idMaquina,
            'tipoAccion'     : tipo,
            'data'           : Ext.JSON.encode(json),
            'idServicio'     : idServicioEditado,
            'dataAnterior'   : Ext.JSON.encode(jsonActual),
            'dataEliminados' : Ext.JSON.encode(arrayRecursoEliminados)
        },
        success: function(response)
        {
            //Ext.Msg.hide();
            Ext.get('servicios').unmask();
            var objJson = Ext.JSON.decode(response.responseText);
            var status  = objJson.strStatus === 'OK';

            Ext.Msg.show({
                title      : status ? 'Mensaje' : 'Error',
                msg        : objJson.strMensaje,
                buttons    : Ext.Msg.OK,
                icon       : status ? Ext.Msg.INFO : Ext.Msg.ERROR,
                closable   : false,
                multiline  : false,
                buttonText : {ok: 'Cerrar'}
            });

            if (!status && Ext.getCmp('winCrearMV')) {
                limpiarArrays();
                limpiarPanel();
                storeDetalle.proxy.extraParams = {numeroSolucion: solucionEditada};
                storeDetalle.removeAll();
                storeDetalle.load({params: {}});
                Ext.getCmp('winCrearMV').close();
                Ext.getCmp('winCrearMV').destroy();
            }
        },
        failure: function(result)
        {
            Ext.Msg.hide();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
            if (Ext.getCmp('winCrearMV')) {
                limpiarArrays();
                limpiarPanel();
                storeDetalle.proxy.extraParams = {numeroSolucion: solucionEditada};
                storeDetalle.removeAll();
                storeDetalle.load({params: {}});
                Ext.getCmp('winCrearMV').close();
                Ext.getCmp('winCrearMV').destroy();
            }
        }
    });
}

function convertToOldRecurso(jsonNewRecurso)
{
    var jsonPrincipal           = {};
    var arrayDetalleStorage     = [];
    var arrayDetalleProcesador  = [];
    var arrayDetalleMemoria     = [];
    var jsonTotales             = {};
    arrayNewRecurso = JSON.parse(jsonNewRecurso.arrayRecursos);
        arrayNewRecurso.forEach(function(recursoHosting){
            if (recursoHosting.tipo == 'DISCO'){
                arrayDetalleStorage.push(recursoHosting);
            }else if(recursoHosting.tipo == 'PROCESADOR'){
                arrayDetalleProcesador.push(recursoHosting);
            }else if(recursoHosting.tipo == 'MEMORIA RAM'){
                arrayDetalleMemoria.push(recursoHosting);
            }                        
        });
        var json                    = {};
        json['carpeta']             ='N/D' ;
        json['idElemento']          = jsonNewRecurso.idMaquina;
        json['idSistemaOperativo']  = '';
        json['nombreElemento']      = jsonNewRecurso.nombre;
        json['sistemaOperativo']    = 'N/D';
        json['tarjetaRed']          = 'N/D';
        jsonTotales['arrayDetalleDisco'] = arrayDetalleStorage;
        jsonTotales['arrayDetalleProcesador'] = arrayDetalleProcesador;
        jsonTotales['arrayDetalleMemoria'] = arrayDetalleMemoria;
        jsonTotales['arrayInfoGeneral'] = json;

        arrayRecursos = jsonTotales;        

        jsonPrincipal['idMaquina'] = jsonNewRecurso.idMaquina;
        jsonPrincipal['licencia'] = 'N/D';
        jsonPrincipal['memoria'] = jsonNewRecurso.memoria;
        jsonPrincipal['nombre'] = jsonNewRecurso.nombre;
        jsonPrincipal['procesador'] = jsonNewRecurso.procesador;
        jsonPrincipal['so'] = 'N/D';
        jsonPrincipal['carpeta'] = 'N/D';
        jsonPrincipal['storage'] = jsonNewRecurso.storage;
        jsonPrincipal['tarjeta'] = 'N/D';
        jsonPrincipal['arrayRecursos'] = arrayRecursos;
        return jsonPrincipal;
    
}

function verificarSOMaquina(arrayMaquinasInvolved)
{
    boolEsEditarSolucion = typeof boolEsEditarSolucion !== 'undefined' && boolEsEditarSolucion;
    var arrayMVsSinSO    = [];
    var x                = 0;

    $.each(arrayMaquinasVirtuales, function(i, arrayMVs)
    {
        $.each(arrayMVs.maquinasVirtuales,function(j, jsonMVs)
        {
            var jsonRecursos = JSON.parse(jsonMVs.arrayRecursos);//Json recursos existentes
            var array        = [];
            var json         = {};
            array            = jsonRecursos.filter(recurso  => recurso.tipo == 'SISTEMA OPERATIVO');

            if (array.length < 1 && !arrayMaquinasInvolved.includes(jsonMVs.idMaquina))
            {
                //SI ES EDICIÓN DE SOLUCIÓN, SOLO SE TOMARA EN CUENTA LAS MAQUINAS VIRTUALES NUEVAS.
                if (boolEsEditarSolucion && !jsonMVs.esNuevo) {
                    return true;
                }

                arrayMVsSinSO.push(jsonMVs.nombre);
            }
        });
    }); 
    return arrayMVsSinSO;
}

function verificarSOMaquinaGuardada(gridRec, tipo, idMaquinaVirtual)
{   
    if( idMaquinaVirtual > 0){
        var boolSinLic = true;
        var numeroEncontrados = 0;
        $.each(gridRec, function(index, recur)
        {
            if(recur.data.maquinaVirtual == idMaquinaVirtual && recur.data.tipoRecurso == 'SISTEMA OPERATIVO')
            {
                numeroEncontrados++;
            }
        });
        if(numeroEncontrados > 1)
        {
            boolSinLic = false;
        }    
    }
    return boolSinLic;
}

//FUNCIÓN QUE PERMITE DEVOLVER LAS MAQUINAS VIRTUALES DE LA SOLUCIÓN.
function ajaxConsultarSoluciones(idSolucion)
{
    //Buscar Maquinas Virtuales
    Ext.get('servicios').mask('Obteniendo Información...');
    Ext.Ajax.request({
        url     : urlGetDetallesPorSolucion,
        timeout : 999999999,
        async   : false,
        method  : 'get',
        params  : {
            numeroSolucion : idSolucion
        },
        success: function(response)
        {
            Ext.get('servicios').unmask();
            limpiarArrays();
            var arrayMaquinas = JSON.parse(response.responseText).arrayMaquinasVirtualesPorPool;
            obtenerMaquinasVirtuales(arrayMaquinas);
        },
        failure: function(result) {
            Ext.get('servicios').unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

function obtenerMaquinasVirtuales(arrayMaquinas) {
    $.each(arrayMaquinas, function (index, maquina) {
        if (maquina.maquinasVirtuales.length > 0) {
            var arrayInformacionTemp = convertToArrayInformacion(maquina.maquinasVirtuales);
            if (arrayInformacion === null || arrayInformacion.length < 1) {
                arrayInformacion = arrayInformacionTemp;
            } else {
                arrayInformacion = arrayInformacion.concat(arrayInformacionTemp);
            }
           arrayMaquinasVirtuales.push({'maquinasVirtuales': arrayInformacionTemp,
                                        'secuencial'       : maquina.idServicio});
       }
    });
    arrayInformacion  = [];
}

function validarNúmeroPositivo(cantidad, rowEditingRecursos, selectionModel)
{
    if(cantidad < 1)
        {
            rowEditingRecursos.cancelEdit();
            selectionModel.select(0);
            rowEditingRecursos.startEdit(0, 0);
        }
}

