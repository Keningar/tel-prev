//virtuales para efecto de validacion
var arrayRecursosConf        = [];
//Recursos de una maquina virtual previo a ser guardados

var arrayMaquinasEdit        = [];
var arrayRecursoTmp          = [];
var arrayInformacionOld      = [];
var componenteDisco          = '';
var componenteProcesador     = '';
var componenteMemoria        = '';
var idRawMVs                 = 1;
var rawNumberMV              = 1;
var boolEsNuevo              = true;
var arrayValidaLicencias     = [];
var recursos                 = "";
var arrayResumenGeneralRecursos = [];
var arrayInformacion         = [];

function renderizarGridMaquinasVirtuales(data)
{    
    boolEsPoolRecursosCompleto = data.esPoolCompleto==='SI';
    boolEsLicencia             = data.esLicencia;
    var esEdicionMvGeneral     = false;
    rawNumberMV                = 1;
    arrayRecursosHosting       = [];
    rowMaquinasVirtuales       = '';
        
//Asignar Store de acuerdo al tipo de recurso que se requiere
    var array = [];
    $.each(data.arrayJsonCaractMultiple, function(i, item) 
    {
        var json      = {};
        json['id']    = item.tipoCaracteristica;
        json['value'] = item.tipoCaracteristica;
        array.push(json);
    });    
    
    rowMaquinasVirtuales = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToMoveEditor : 1,
        autoCancel         : false,
        hideButtons: function(){
        var me = this;
            if (me.editor && me.editor.floatingButtons){
                me.editor.floatingButtons.getEl().setStyle('visibility', 'hidden');
            } else {
                Ext.defer(me.hideButtons, 10, me);
            }
        },
        listeners: 
        {
            beforeedit:function(editor, e, eOpts) 
            {
                this.hideButtons();
                if(e.record.data.nombreMV == '')
                {
                    esEdicionMvGeneral = true;
                }
                else
                {
                    esEdicionMvGeneral = false;
                }

                //Cuando es por edición de solución, no se podran editar las maquinas registradas desde la creación.
                var rawData       = e.record.data;
                var arrayTemporal = [];
                if (accion == 'editar' && boolEsEditarSolucion && intIdServicio !== null) {
                    arrayTemporal = arrayInformacion.filter(maquina => (maquina.idMaquina  == rawData.idRawMVs  ||
                                                                        maquina.nombre     == rawData.nombreMV) &&
                                                                        maquina.idServicio == intIdServicio);

                    if (arrayTemporal.length > 0 && !arrayTemporal[0].esNuevo) {
                        Ext.Msg.alert('Alerta', 'No puede Modificar la máquina virtual, elimine y cree una nueva por favor');
                        return false;
                    }
                }
            },
            afteredit: function(editor, e, eOpts) 
            {
                var esEdicion = false;

                var rawData = e.record.data; 
                if(rawData.nombreMV == "")
                { 
                    Ext.Msg.alert('Error', 'Ingrese nombre de la máquina virtual');
                    rowMaquinasVirtuales.cancelEdit();
                    rowMaquinasVirtuales.startEdit(0, 0);
                    e.store.remove(e.record);
                    return false;
                }
                else
                {
                    let arrayTemporal = [];

                    //Verificamos si ya existe una maquina virtual ingresa con el mismo nombre pero con diferente id de maquina.
                    arrayTemporal = arrayInformacion.filter(maquina => maquina.nombre   === rawData.nombreMV &&
                                                                       rawData.idRawMVs !== maquina.idMaquina);

                    if(arrayTemporal.length > 0)
                    {
                        Ext.Msg.alert('Error', 'Ya ha ingresado el nombre de la máquina virtual');
                        rowMaquinasVirtuales.cancelEdit();
                        rowMaquinasVirtuales.startEdit(0, 0);
                        e.store.remove(e.record);
                        return false;
                    }
                }

                //Variable compartida con archivos de edicion de soluciones.
                {
                    //Realizar los calculos pertinentes relacionados al precio del producto
                    var idRawMVs = rawNumberMV;

                    if(!esEdicionMvGeneral)
                    {
                        arrayInformacion.forEach(function(element){
                            if(rawData.idRawMVs == element.idMaquina)
                            {
                                idRawMVs = rawData.idRawMVs;
                            }
                        });
                    }
                    else
                    {
                        //For para obtener todo los id de maquinas virtuales registradas
                        //para evitar la duplicidad de los id.
                        var arrayIdMaquinasVirtuales = [];
                        arrayMaquinasVirtuales.forEach(function(maquinasV) {
                            maquinasV.maquinasVirtuales.forEach(function(maquinaV) {
                                arrayIdMaquinasVirtuales.push(maquinaV.idMaquina);
                            });
                        });

                        if (arrayIdMaquinasVirtuales.length > 0) {
                            var indice = arrayIdMaquinasVirtuales.findIndex(id => id === rawNumberMV);
                            if (indice >= 0) {
                                rawNumberMV = Math.max.apply(null,arrayIdMaquinasVirtuales);
                                rawNumberMV++;
                            }
                        }

                        idRawMVs = rawNumberMV;
                        e.record.set('idRawMVs', idRawMVs);
                        rawNumberMV++;
                    }
                }

                nombreMaquinaVirtual = rawData.nombreMV;

                esEdicion = false;

                if(esEdicionMvGeneral)
                {
                    showAgregarRecursosMV(idRawMVs, false, esEdicionMvGeneral, e);
                }
                else
                {
                    arrayInformacion.forEach(function(element){
                        if(rawData.idRawMVs == element.idMaquina)
                        {
                            element.nombre = rawData.nombreMV;
                        }
                    });
                }
            }
        }
    });

    var toolbarMaquinasVirtual = Ext.create('Ext.toolbar.Toolbar', {
        dock  : 'top',
        align : '->',
        id    : 'tlbAgregarMV',
        items :
        [ 
            {
                iconCls : 'icon_add',
                text    : 'Agregar Máquina' ,
                id      : 'btnAgregarRecursoMV',
                scope   : this,
                handler: function()
                {
                    if (storageTotal  - storageUsado> 0 && procesadorTotal - procesadorUsado > 0 &&
                        memoriaTotal  - memoriaUsado > 0 )
                    {
                        agregarMaquinaVirtual();
                    }
                    else
                    {
                        Ext.Msg.alert('Error', 'No existe suficientes recursos');
                    }
                }
            },
            {
                iconCls : 'icon_delete',
                text    : 'Eliminar Recurso ' ,
                id      : 'btnEliminarRecursoMV',
                scope   : this,
                handler : function()
                {
                    eliminarRecursoMv();
                }
            }
        ]
    });

    //Data model de maquinas virtuales.
    Ext.define('mvModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idRawMVs'     , type: 'integer'},
            {name: 'nombreMV'     , type: 'string'},
            {name: 'ProcesadorMV' , type: 'string'},
            {name: 'discoMV'      , type: 'string'},
            {name: 'MemoriaMV'    , type: 'string'}
        ]
    });

    //Store de maquinas virtuales.
    storeMaquinasVirtualesCaracteristicas = Ext.create('Ext.data.Store', {
        pageSize    :  5,
        autoDestroy :  true,
        model       : 'mvModel',
        proxy       : {type: 'memory'}
    });

    var gridMV= Ext.create('Ext.grid.Panel', 
    {
        id          : 'gridMaquinasVirtuales',
        title       : 'Ingreso de Máquinas Virtuales',
        width       : 530,
        store       : storeMaquinasVirtualesCaracteristicas,
        dockedItems : [toolbarMaquinasVirtual],
        plugins     : [rowMaquinasVirtuales],
        height      : 230,
        columns: 
        [ 
            {
                id: 'idRawMVs',
                dataIndex: 'idRawMVs',
                hidden: true,
                hideable: false
            },
            {
                id: 'idStorage',
                dataIndex: 'idStorage',
                hidden: true,
                hideable: false
            },
            {
                id: 'idMemoria',
                dataIndex: 'idMemoria',
                hidden: true,
                hideable: false
            },
            {
                id: 'idProcesador',
                dataIndex: 'idProcesador',
                hidden: true,
                hideable: false
            },
            {
                header: "<b>Nombre</b>",
                dataIndex:'nombreMV',
                width: 220 ,
                align:'center',
                renderer: function(value, meta, record) 
                {
                    meta.tdCls = 'nombre';
                    return value;
                },
                editor: {
                    xtype: 'textfield',
                    id:'nombreMv',
                    readOnly:false,
                    disabled:false,
                    align:'center',
                    fieldStyle:'color:green;font-weight:bold;text-align: center;',
                    listeners : {
                        change : function(field, e, lastValue) {
                            var text = field.value;
                            if(/^\s+|\s+$/.test(text))
                            {
                                this.setValue(lastValue);
                                this.setRawValue(lastValue);

                            }
                        }
                    }
                }
            },
            {
                header: "<b>Procesador</b>",
                dataIndex:'procesadorMV',
                width: 75,
                align:'center',
                renderer: function(value, meta, record) 
                {
                    meta.tdCls = 'procesador';
                    return value;
                },
                editor: {
                    xtype: 'numberfield',
                    id:'txtProcesadorMV',
                    align:'center',
                    readOnly:true,
                    hideTrigger: true,
                    disabled:false,
                    fieldStyle:'color:green;font-weight:bold;text-align: center;'
                }
            },
            {
                header: "<b>Disco</b>",
                dataIndex:'discoMV',
                width: 75,
                align:'center',
                renderer: function(value, meta, record) 
                {
                    meta.tdCls = 'disco';
                    return value;
                },
                editor: {
                    xtype: 'numberfield',
                    id:'txtDiscoMV',
                    align:'center',
                    readOnly:true,
                    hideTrigger: true,
                    disabled:false,
                    fieldStyle:'color:green;font-weight:bold;text-align: center;'
                }
            },
            {
                header: "<b>Memoria</b>",
                dataIndex:'memoriaMV',
                width: 75,
                align:'center',
                renderer: function(value, meta, record) 
                {
                    meta.tdCls = 'memoria';
                    return value;
                },
                editor: {
                    xtype: 'numberfield',
                    id:'txtmemoriaMV',
                    align:'center',
                    readOnly:true,
                    hideTrigger: true,
                    disabled:false,
                    fieldStyle:'color:green;font-weight:bold;text-align: center;'
                }
            },
            {
                xtype: 'actioncolumn',
                width: 50,
                items: [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-Tuerca';
                        },
                        tooltip: 'Configurar Máquina',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            if(accion != 'editar') {
                                boolEsEdicion = true ;
                                var rowNumber = grid.getStore().data.items[rowIndex].data.idRawMVs;
                                showAgregarRecursosMV(rowNumber, boolEsEdicion);
                            }else {
                                Ext.Msg.alert('Error', 'No puede Modificar la máquina virtual, elimine y cree una nueva por favor');
                            }
                        }
                    }
                ]
            }
        ]
    });
    
    return gridMV;
}

function agregarMaquinaVirtual()
{
    var jsonTotales = {};
    var disco = arrayRecursosHosting.find(
            arrayRecursosHosting => arrayRecursosHosting.tipoRecurso === 'DISCO');
    var procesador = arrayRecursosHosting.find(
            arrayRecursosHosting => arrayRecursosHosting.tipoRecurso === 'PROCESADOR');
    var memoria = arrayRecursosHosting.find(
            arrayRecursosHosting => arrayRecursosHosting.tipoRecurso === 'MEMORIA RAM');

    if (disco != null && procesador !=  null && memoria != null){
        rowMaquinasVirtuales.cancelEdit();
        var recordParamDetMV = Ext.create('mvModel', {
                idRaw      : rawNumber,
                nombre     : '',
                disco      : '',
                Procesador : '',
                Memoria    : ''
            });
        storeMaquinasVirtualesCaracteristicas.insert(0, recordParamDetMV);
        rowMaquinasVirtuales.startEdit(0, 0);
        intTotalStorage = 0;
        intTotalProcesador = 0;
        intTotalMemoria = 0;
        arrayDetalleStorage = [];
        arrayDetalleProcesador = [];
        arrayDetalleMemoria = [];                    
        arrayRecursosHosting.forEach(function(recursoHosting){
            if (recursoHosting.tipoRecurso == 'DISCO'){
                arrayDetalleStorage.push(convertToArrayRecursos(recursoHosting));
                intTotalStorage = intTotalStorage + parseFloat(recursoHosting.cantidad);
            }else if(recursoHosting.tipoRecurso == 'PROCESADOR'){
                arrayDetalleProcesador.push(convertToArrayRecursos(recursoHosting));
                intTotalProcesador = intTotalProcesador + parseFloat(recursoHosting.cantidad);
            }else if(recursoHosting.tipoRecurso == 'MEMORIA RAM'){
                arrayDetalleMemoria.push(convertToArrayRecursos(recursoHosting));
                intTotalMemoria = intTotalMemoria + parseFloat(recursoHosting.cantidad);
            }                        
        });

        jsonTotales['arrayDetalleDisco'] = arrayDetalleStorage;
        jsonTotales['arrayDetalleProcesador'] = arrayDetalleProcesador;
        jsonTotales['arrayDetalleMemoria'] = arrayDetalleMemoria;
        arrayRecursos = jsonTotales;
    }
    else
    {
        Ext.Msg.alert('Error', 'Debe ingresar al menos  una memoria, un procesador y  un Disco duro para agregar una Máquina Virtual ');
    }    
}

function eliminarRecursoMv()
{
    var gridRecursos   = Ext.getCmp('gridRecursos');
    var selectionModel = gridMaquinasVirtuales.getSelectionModel();

    if(selectionModel.getSelection()[0])
    {
        var rawData = selectionModel.getSelection()[0].data;

        arrayInformacion = arrayInformacion.filter(function(elem)
        {
            if(elem.idMaquina == rawData.idRawMVs)
            {
                var jsonRecursos = JSON.parse(elem.arrayRecursos);
                
                //Se llenan de nuevo los recursos Disponibles
                $.each(arrayResumenGeneralRecursos, function(i, item1)
                {                        
                    $.each(jsonRecursos, function(i, recurso)
                    {
                        if(recurso.idRecurso == item1.idRecurso)
                        {
                            item1.disponible =  parseInt(item1.disponible) + parseInt(recurso.asignar);
                        }
                    });
                });
                
                $.each(arraySolucion, function(i, solucion) {
                    var arrayCaracteristicas = JSON.parse(solucion.caracteristicasPoolRecursos);
                    arrayCaracteristicas = arrayCaracteristicas.filter(caracteristica => caracteristica.idMaquinas[0] !== rawData.idRawMVs);
                    arrayCaracteristicas = Ext.JSON.encode(arrayCaracteristicas);
                    solucion.caracteristicasPoolRecursos = arrayCaracteristicas;
                });

                storageUsado    = storageUsado    - rawData.storage;
                memoriaUsado    = memoriaUsado    - rawData.memoria
                procesadorUsado = procesadorUsado - rawData.procesador;

                actualizarTotal('storage'    , storageTotal    - storageUsado);
                actualizarTotal('procesador' , procesadorTotal - procesadorUsado);
                actualizarTotal('memoria'    , memoriaTotal    - memoriaUsado);
            }
            return elem.idMaquina !== rawData.idRawMVs;
        });

        rowMaquinasVirtuales.cancelEdit();
        storeMaquinasVirtualesCaracteristicas.remove(selectionModel.getSelection());
        if (storeMaquinasVirtualesCaracteristicas.getCount() > 0)
        {
            selectionModel.select(0);
        }

        //300, cantidad total de un secuencial ?.
        if(accion === 'editar' && parseInt(rawData.idRawMVs) > 300)
        {
            jsonInfo = convertToOldRecurso(arrayInformacionOld.find(function(elem)
            {
                return elem.idMaquina  == rawData.idRawMVs;
            }));

            ajaxEditarEliminarMaquinaVirtualComercial('eliminar',jsonInfo,null);
        }

        calcularTotalesMvs();
    }
    else
    {
        Ext.Msg.alert('Atención', 'Por favor, seleccione un registro para ser eliminado');
    }
    calcularTotalesMvs();
}

function handlerShowRecursos(grid, idRawMv, tipoRecurso, tipo)
{
   var boolEsCorrecto = true;
   var totalRecursoDisco = 0;
   var ArrayIdsRecursoActual = [];
   var jsonRecurso     = {};
   if(grid.getStore().getCount() > 0  || (tipo == 'PROCESADOR' && tipo == 'MEMORIA'))
        {
            var totalRecurso    = 0;
            var boolContinuar   = true;
            
            for (var j = 0; j < grid.getStore().getCount(); j++)
            {
                if(parseInt(grid.getStore().getAt(j).data['asignarMv' + tipo]) === 0)
                {
                    boolContinuar = false;//si existe alguna asignacion de recursos en 0 no continua con el flujo
                }

                if(!boolContinuar)
                {
                    break;
                }
            }

            if(boolContinuar)
            {
                //Agrear recursos a la caracteristica
                for (var k = 0; k < grid.getStore().getCount(); k++)
                {
                    var asignar   = parseInt(grid.getStore().getAt(k).data['asignarMv' + tipo]);                     
                    var idRecurso = parseInt(grid.getStore().getAt(k).data['idRecursoMv' + tipo]);
                    totalRecurso += asignar;
                    ArrayIdsRecursoActual.push(idRecurso);                   
                }
                                           
               
                boolEsEdicion = false;
                storeTipoRecursoMV = [];//Se limpia el store para que pueda ser cargado por otro tipo de recurso 
                jsonRecurso['totalRecurso']   = totalRecurso; 
                jsonRecurso['arrayIdRecurso'] = ArrayIdsRecursoActual;
                jsonRecurso['boolExiste'] = true;
                return jsonRecurso;
            }
            else
            {
                boolEsCorrecto = false;
                jsonRecurso['boolExiste'] = false;
                return jsonRecurso;
            }           
            
        }
        else
        {                      
            jsonRecurso['boolExiste'] = false;
            return jsonRecurso;
        }
}

function getStoreMaquinasVirtuales(array)
{    
    var arrayMaquinasVirtualesStore = [];
    
    $.each(array, function(i, item)
    {
        $.each(item.maquinasVirtuales,function(j, mvs){
            var json = {};
            json['id'] = mvs['idMaquina'];
            json['value'] = mvs['nombre'];
            arrayMaquinasVirtualesStore.push(json); 
        });
                       
    });
    var json = {};
    json['id'] = "Sin máquina";
    json['value'] = "Sin máquina";
    arrayMaquinasVirtualesStore.unshift(json);

    var store = new Ext.data.Store({
            fields: ['id','value'],
            data: arrayMaquinasVirtualesStore
            });  
    
    return store;    
}

function llenarGridMaquinasVirtuales(arrayMVs, storeMaquinasVirtualesCaracteristicas, rowMaquinasVirtuales)
{
    if (arrayMVs.length > 0)
    {
        $.each(arrayMVs, function (index, mv) {

            rowMaquinasVirtuales.cancelEdit();

            var recordParamDetMV = Ext.create('mvModel', {
                idRawMVs     : mv.idMaquina,
                nombreMV     : mv.nombre,
                ProcesadorMV : mv.procesador,
                discoMV      : mv.storage,
                MemoriaMV    : mv.memoria
            });

            rawNumberMV++;
            arrayMaquinasEdit.push(mv.idMaquina);
            storeMaquinasVirtualesCaracteristicas.insert(0, recordParamDetMV);
            storageUsado    = storageUsado + parseInt(mv.storage, 10);
            memoriaUsado    = memoriaUsado + parseInt(mv.memoria, 10);
            procesadorUsado = procesadorUsado + parseInt(mv.procesador, 10);
            
            $.each(JSON.parse(mv.arrayRecursos), function(index,recurso){
                $.each(arrayResumenGeneralRecursos, function(index, recursoResumen){
                    if(recursoResumen.idRecurso == recurso.idRecurso)
                    {
                        recursoResumen.disponible =  parseInt(recursoResumen.disponible) - parseInt(recurso.asignar);
                    }
                });
            });

            var rawGridMv = gridMaquinasVirtuales.getStore().getRange();

            //Recorro el grid de Máquinas virtuales para insertar los valores
            $.each(rawGridMv, function( index, value)
            {
                if (value.get('idRawMVs') === mv.idMaquina)
                {
                    value.set('discoMV'     , mv.storage);
                    value.set('procesadorMV', mv.procesador);
                    value.set('memoriaMV'   , mv.memoria);
                }
            });
        });

        initProgressBar(true);
        arrayInformacionOld = arrayMVs;

        boolEsEditarSolucion = typeof boolEsEditarSolucion !== 'undefined' && boolEsEditarSolucion;
        if (boolEsEditarSolucion && arrayInformacion.length > 0) {
            $.each(arrayMVs, function(i, maquina) {
                var indice = arrayInformacion.findIndex(vm => vm.idMaquina  === maquina.idMaquina &&
                                                              vm.idServicio === maquina.idServicio);
                if (indice < 0) {
                    arrayInformacion.push(maquina);
                }
             });
        } else {
            arrayInformacion = arrayMVs;
        }
   }
}

function calcularTotalesMvs()
{
    var rawGridMv = '';
    var sumaStorage = 0;
    var sumaProcesador = 0;
    var sumaMemoria = 0;
    rawGridMv = gridMaquinasVirtuales.getStore().getRange();
        //Recorro el grid de Máquinas virtuales para insertar  los valores
        $.each(rawGridMv, function( index, value ) 
        {
            sumaStorage     = sumaStorage + parseInt(value.data.discoMV);
            sumaProcesador  = sumaProcesador + parseInt(value.data.ProcesadorMV);
            sumaMemoria     = sumaMemoria + parseInt(value.data.MemoriaMV);      
        });
        storageUsado    = sumaStorage;
        memoriaUsado    = sumaMemoria;
        procesadorUsado = sumaProcesador;
        actualizarTotal('storage', storageTotal - sumaStorage);                            
        actualizarTotal('procesador', procesadorTotal - sumaProcesador);
        actualizarTotal('memoria', memoriaTotal  - sumaMemoria);
}
