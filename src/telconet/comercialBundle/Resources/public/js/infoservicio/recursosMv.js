var boolEdicionMV = false;
var arrayParametrosLicencias = [];
var nombreMaquinaVirtual     = '';
var secuenciaMaquinaVirtual  = 1;
var numberRowRecursoMaquina  = 1;
var rowMaquinasVirtuales;
var storeMaquinasVirtualesCaracteristicas; 
var arrayRecursoEliminados   = [];
var boolEditing              = false;
var arrayTempResumenRecur    = [];

function showAgregarRecursosMV(idRowGrid, esEdicion,esEdicionMvGeneral, e)
{        
    boolEsEditarSolucion = typeof boolEsEditarSolucion !== 'undefined' && boolEsEditarSolucion;
    intIdServicio        = typeof intIdServicio        !== 'undefined' && intIdServicio !== null ? intIdServicio : null;

    var array                    = [];
    var boolEsDisco              = true;
    var boolEsEdicion            = false;
    var boolEditarMv             = false;
    var idRecursoAnt             = 0;
    var recursoAnt               = 0;
    var componente               = '';
    var gridRecursosMVDisco      = null;
    var gridRecursosMVProcesador = null;
    var gridRecursosMVMemoria    = null;
    var formPanelEditar          = null;
    var winAgregarRecursosMv     = null;
    arrayRecursosGuardados       = [];
    arrayTempResumenRecur        = [];
    arrayRecursoTmp              = [];
    arrayRecursosConf            = [];

    (esEdicion) ? boolEdicionMV  =  true : boolEdicionMV =  false;

    jsonTempResumenRecur     = JSON.stringify(arrayResumenGeneralRecursos);
    arrayTempResumenRecur    = JSON.parse(jsonTempResumenRecur);
    gridRecursosMVDisco      = agreeMv(arrayRecursos.arrayDetalleDisco, 1, 'txtStorage', 'DISCO', idRowGrid);    
    gridRecursosMVProcesador = agreeMv(arrayRecursos.arrayDetalleProcesador, 0, 'txtProcesador', 'PROCESADOR', idRowGrid);
    gridRecursosMVMemoria    = agreeMv(arrayRecursos.arrayDetalleMemoria, 0, 'txtMemoria', 'MEMORIA', idRowGrid);    
    
    formPanelEditar = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        width:'auto',
        height:'auto',
        layout: {
            type: 'table',
            columns: 1
        },
        frame: true,
        items: 
        [
            {
                xtype  : 'fieldset',
                height : 170,
                items  :
                [
                    gridRecursosMVDisco
                ]
            },
            {
                xtype  : 'fieldset',
                height : 170,
                items  :
                [
                    gridRecursosMVProcesador
                ]
            },
            {
                xtype  : 'fieldset',
                height : 170,
                items  :
                [
                    gridRecursosMVMemoria
                ]
            }
        ],
        buttons: [
            {
                text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;<b>Agregar Recursos</b>',
                handler: function() 
                {
                    if(boolEditing)
                    {
                        Ext.Msg.alert('Error', 'Debe terminar de ingresar Recurso');
                        return false;
                    }

                    jsonDisco      = handlerShowRecursos(gridRecursosMVDisco      , idRowGrid, 'txtStorage'   , 'DISCO');
                    jsonMemoria    = handlerShowRecursos(gridRecursosMVMemoria    , idRowGrid, 'txtMemoria'   , 'MEMORIA');
                    jsonProcesador = handlerShowRecursos(gridRecursosMVProcesador , idRowGrid, 'txtProcesador', 'PROCESADOR');

                    if(jsonDisco['boolExiste'] && jsonMemoria['boolExiste'] && jsonProcesador['boolExiste'])
                    {
                        var rawGridMv = gridMaquinasVirtuales.getStore().getRange();

                        //Recorro el grid de Máquinas virtuales para insertar  los valores
                        $.each(rawGridMv, function(index, value) 
                        {
                            if (value.get('idRawMVs') === idRowGrid)
                            {
                                value.set('discoMV'      , jsonDisco['totalRecurso']);
                                value.set('procesadorMV' , jsonProcesador['totalRecurso']);
                                value.set('memoriaMV'    , jsonMemoria['totalRecurso']);
                                value.set('idStorage'    , jsonDisco['arrayIdRecurso']);
                                value.set('idProcesador' , jsonProcesador['arrayIdRecurso']);
                                value.set('idMemoria'    , jsonMemoria['arrayIdRecurso']);
                                value.set('discoMV'      , jsonDisco['totalRecurso']);
                                value.set('ProcesadorMV' , jsonProcesador['totalRecurso']);
                                value.set('MemoriaMV'    , jsonMemoria['totalRecurso']);
                            }
                        });

                        recursos = Ext.JSON.encode(arrayRecursoTmp);
                        var json = {};
                        json['idMaquina']     = idRowGrid;
                        json['idServicio']    = intIdServicio;
                        json['nombre']        = nombreMaquinaVirtual;
                        json['arrayRecursos'] = recursos;  
                        json['storage']       = jsonDisco['totalRecurso'];
                        json['memoria']       = jsonMemoria['totalRecurso'];
                        json['procesador']    = jsonProcesador['totalRecurso'];
                        json['so']            = 'N/D';
                        json['carpeta']       = "N/D";
                        json['tarjeta']       = "N/D";

                        $.each(arrayRecursosHosting, function(index, recurso){
                            if ($.inArray(recurso.idRecurso,jsonProcesador['arrayIdRecurso']) )  
                            {
                                recurso.idMaquinas.push(idRowGrid);
                            }
                            else if ($.inArray(recurso.idRecurso,jsonMemoria['arrayIdRecurso']))
                            {
                                recurso.idMaquinas.push(idRowGrid);
                            }
                            else if ($.inArray(recurso.idRecurso,jsonDisco['arrayIdRecurso']))
                            {
                                recurso.idMaquinas.push(idRowGrid);
                            }
                        });

                        maquinaId = arrayMaquinasEdit.find(maquina => maquina == idRowGrid)

                        if (maquinaId) {
                            json['esEdicion'] = true;
                        } else {
                            json['esEdicion'] = false;
                        }

                        if (!boolEdicionMV)
                        {
                            json['esNuevo'] = true;
                        }
                        else
                        {
                            if (accion == 'editar')
                            {
                                var jsonActual = [];
                                jsonActual = convertToOldRecurso(arrayInformacionOld.find(function(elem)
                                {
                                    return elem.idMaquina  == idRowGrid;
                                }));

                                ajaxEditarEliminarMaquinaVirtualComercial('editar', json, jsonActual); 
                            }

                            arrayInformacion = arrayInformacion.filter(function(elem)
                            {
                                return elem.idMaquina  !== idRowGrid;
                            });
                        }

                        arrayResumenGeneralRecursos  = [];
                        jsonTempResumenRecur         = JSON.stringify(arrayTempResumenRecur);
                        arrayResumenGeneralRecursos  = JSON.parse(jsonTempResumenRecur);

                        calcularTotalesMvs();
                        arrayInformacion.push(json);
                        winAgregarRecursosMv.close();
                        winAgregarRecursosMv.destroy();
                    }
                    else
                    {
                        Ext.Msg.alert('Error', 'Debe ingresar todos los recursos');
                        return false;
                    }
                }
            },
            {
                text: '<b>Cerrar</b>',
                handler: function() { 
                    if (esEdicionMvGeneral) {
                        e.store.remove(e.record);
                    }

                    winAgregarRecursosMv.destroy();
                    winAgregarRecursosMv.close();
                }
            }
        ]});

    winAgregarRecursosMv = Ext.widget('window', {
        id        : 'winAgregarRecursosMv',
        title     : 'Administrar Recursos',
        layout    : 'fit',
        resizable : true,
        modal     : true,
        closable  : false,
        width     : 'auto',
        items     : [formPanelEditar]
    });

    if (boolEditarMv)
    {
        switch(tipo)
        {
            case 'DISCO':
                if(boolCambioDisco)cargarInformacionRecursosActualizada(tipo);
                else cargarInformacionRecursosGuardada();                
                break;

            case 'MEMORIA RAM':
                if(boolCambioMemoria)cargarInformacionRecursosActualizada(tipo);
                else cargarInformacionRecursosGuardada(); 
                break;

            default ://PROCESADOR
                if(boolCambioProcesador)cargarInformacionRecursosActualizada(tipo);
                else cargarInformacionRecursosGuardada(); 
                break;
        }
    }

    winAgregarRecursosMv.show();
}

function agreeMv(array, parametro, componente, tipo, idMaquina)
{     
    var rowEditingRecursosMv             = null;
    var storeTipoRecursoMv               = null;
    var modeloMv                         = null;
    var storeRecursoscaracteristicaMvsMv = null;
    var toolbarMv                        = null;
    var gridRecursoMv                    = null;   
    var recordParamDet                   = null;
    var boolEsDisco                      = true;
    //Variable que indica cuando se cambia el recurso en el grid
    var boolEsEdicion                    = false;    
    var boolEditarMv                     = false;
    var idRecursoAnt                     = 0;
    var recursoAnt                       = 0;
    var esEdicionMaquinaVirtual          = false;
    if(!boolEdicionMV)
    {
        arrayRecursoTmp                      = [];
        arrayRecursosConf                    = []; 
    }
    storeTipoRecursoMv = getStoreCaracteristicasMV(array);
    
   
    rowEditingRecursosMv = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText: '<i class="fa fa-check-square"></i>',
        cancelBtnText: '<i class="fa fa-eraser"></i>',
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {
            canceledit: function(editor, e, eOpts) 
            {
                var idDetalle = e.record.data['idDetalleMv' + tipo];
                var idRecurso = e.record.data['idRecursoMv' + tipo];
                arrayRecursosConf = arrayRecursosConf.filter(recursoConf => recursoConf.idDetalle != idDetalle );
                arrayRecursoTmp = arrayRecursoTmp.filter(recursoTmp => recursoTmp.idDetalle != idDetalle );
                $.each(arrayTempResumenRecur, function(i, item1)
                {
                    if(item1.idRecurso  === idRecurso)
                    {
                         item1.disponible  =  item1.disponible +  e.record.data['asignarMv' + tipo];
                    }
                });
                if(accion == 'editar' && idRecurso > 300)
                {
                    arrayRecursoEliminados.push(idRecurso);
                }

                var totalRecurso = 0;
                
                for (var i = 0; i < gridRecursoMv.getStore().getCount(); i++)
                {
                    var asignarMv   = parseInt(gridRecursoMv.getStore().getAt(i).data.asignarMv + tipo);
                    totalRecurso += asignarMv;
                }
                //Se muestra el valorMv total de recursos escogidos ( Gb o Cores )
                boolEditing = false;
                e.store.remove(e.record);
                  
            },
            beforeedit:function(editor, e, eOpts) 
            {  
                
                recursoAnt = 0;
                boolEditing = true;
                esEdicionMaquinaVirtual = false;
                boolEsEdicion = false;
                if(e.record.data['caracteristicaMv' + tipo])
                {
                    esEdicionMaquinaVirtual = true;
                    idRecursoMvAnt = e.record.data['idRecursoMv' + tipo];                
                    recursoAnt   = e.record.data['asignarMv' + tipo]; 
                    if(!Ext.isEmpty(idRecursoMvAnt) && idRecursoMvAnt >= -1)
                    {
                        boolEsEdicion = true;
                        Ext.getCmp('txtAsignacionMv' + tipo).setEditable('true');
                    } 
                }
                
                               
            },
            afteredit: function(editor, e, eOpts) 
            {
                boolEditing = false;
                var intCountGridDetalleMv = Ext.getCmp('gridRecursoMv' + tipo).getStore().getCount();
                var selectionModel        = Ext.getCmp('gridRecursoMv' + tipo).getSelectionModel();
                var boolContinuar         = true;
                selectionModel.select(0);
                var rawData = e.record.data;
                var RecursoMv = e.record.data['caracteristicaMv' + tipo] ;
                
                if (intCountGridDetalleMv > 0)
                {
                    if ( Ext.isEmpty(RecursoMv))
                    {
                        Ext.Msg.alert('Error', 'Debe escoger los valores para asignar el recurso');
                        var idRecursoAnt = e.record.data['idRecursoMv' + tipo];
                        $.each(arrayTempResumenRecur, function(i, item1)
                        {
                            if(item1.idRecurso  === idRecursoAnt)
                            {
                                 item1.disponible  =  item1.disponible +  e.record.data['asignarMv' + tipo];
                            }
                        });
                        rowEditingRecursosMv.cancelEdit();
                        selectionModel.select(0);
                        rowEditingRecursosMv.startEdit(0, 0);
                        return false;
                    }
                    else
                    {      
                        var idrecursoIndividual = 0;
                        var arrayTempo = [];
                        //Generar nueva maquina virtual
                       
                        if(!boolEsEdicion)
                        {
                            idrecursoIndividual = e.record.data['caracteristicaMv' + tipo];
                        }
                        else
                        {
                            idrecursoIndividual = e.record.data['idRecursoMv' + tipo];
                        }
                        if(esEdicionMaquinaVirtual)
                        {
                            id =  e.record.data['idDetalleMv' + tipo];
                        }
                        else
                        {
                            id = numberRowRecursoMaquina;
                        }
                        arrayTempo = arrayRecursosConf.filter(solucion => solucion.idRecurso == idrecursoIndividual);
                        if (arrayTempo.length > 0 )
                        {
                            if((arrayTempo[0].idDetalle   !=  e.record.data['idDetalleMv' + tipo] && esEdicionMaquinaVirtual) || !esEdicionMaquinaVirtual)
                            {
                                Ext.Msg.alert('Error', 'El recurso ya se encuentra seleccionado');
                                rowEditingRecursosMv.cancelEdit();
                                selectionModel.select(0);
                                e.store.remove(e.record);
                                return false;
                            } 
                            
                       
                        }
                            
                        var item = arrayRecursosHosting.find(recurso => recurso.idRaw == idrecursoIndividual);
                        var idRecursoMaquina =  idrecursoIndividual;
                            //asignarMv el valorMv disponibleMv calculado con cada redistribucion de recursos asignados por tipo
                        var disponibleMvRecurso = 0;
                        var asignarMv           = 0;

                        $.each(arrayTempResumenRecur, function(i, item1)
                        {
                            if(item1.idRecurso  == idrecursoIndividual )
                            {
                                disponibleMvRecurso = parseInt(item1.disponible)  + recursoAnt;
                            }
                        });
                        //Si no ha sido asignado
                        if(!Ext.isEmpty(e.record.data['asignarMv' + tipo] ))
                        {

                            asignarMv = e.record.data['asignarMv' + tipo] ;                                        


                            if(disponibleMvRecurso < asignarMv)
                            {
                                Ext.Msg.alert('Alerta', 'No puede añadir un recurso mayor al ya utilizado, revise su pool de recursos.');
                                boolContinuar = false;

                                //Si los recursos son nuevos se borra el registro para volver a seleccionarlos
                                if(!boolEsEdicion)
                                {
                                    e.store.remove(e.record);
                                }
                                else//si ya existeb los permanece como estaban para continuar el proceso
                                {
                                    //Poner el recurso asignado anterior en caso de excepcion
                                    e.record.set("asignarMv" + tipo , parseInt(recursoAnt));
                                }

                                return false;

                            }
                            else
                            {
                                if(!esEdicionMaquinaVirtual)
                                {
                                    disponibleMvRecurso = parseInt(disponibleMvRecurso) - parseInt(asignarMv) ;
                                    arrayTempResumenRecur.forEach(function(item1)
                                    {
                                        if(item1.idRecurso  == idrecursoIndividual)
                                        {
                                            var recurso = item1.disponible ;
                                            item1.disponible  =  recurso  - parseInt(asignarMv);
                                        }
                                    });
                                }
                                else
                                {                            
                                    arrayTempResumenRecur.forEach(function( item1)
                                    {
                                        if(item1.idRecurso  === idrecursoIndividual )
                                        {
                                            var recurso = item1.disponible ;
                                            item1.disponible  = disponibleMvRecurso  - parseInt(asignarMv);
                                        }
                                    });
                                }
                            
                            }
                        } 
                        if(boolContinuar)
                        {
                            if(esEdicionMaquinaVirtual)
                            {
                                idDetalleMv =  e.record.data['idDetalleMv' + tipo];
                            }
                            else
                            {
                                idDetalleMv = numberRowRecursoMaquina;
                            }
                            if(boolEsEdicion)
                            {
                                disponibleMvRecurso  = disponibleMvRecurso - asignarMv;
                            }
                
                            e.record.set("idDetalleMv"     + tipo,     idDetalleMv );
                            e.record.set("idRecursoMv"     + tipo,     idRecursoMaquina);
                            e.record.set("caracteristicaMv"+ tipo,     item.caracteristica);
                            e.record.set("valorMv"         + tipo,     item.cantidad);
                            e.record.set("disponibleMv"    + tipo,     disponibleMvRecurso );
                            e.record.set("asignarMv"       + tipo,     parseInt(asignarMv));
                            e.record.set("datastoreMv"     + tipo,     item.valorMvcaracteristica );

                            (tipo === 'MEMORIA') ? tipoReal = 'MEMORIA RAM' : tipoReal =  tipo;

                            var json               = {};
                            json['tipo']           = tipoReal;
                            json['idRecurso'     ] = idRecursoMaquina;
                            json['caracteristica'] = item.caracteristica;
                            json['valor'         ] = item.cantidad;
                            json['disponible'    ] = parseInt(disponibleMvRecurso);
                            json['asignar'       ] = parseInt(asignarMv);
                            json['datastore'     ] = item.valorMvcaracteristica;   
                            json['idDetalle'     ] = idDetalleMv;
                            arrayRecursosConf = arrayRecursosConf.filter(recurso  =>  recurso.idDetalle != idDetalleMv);
                            arrayRecursoTmp = arrayRecursoTmp.filter(recurso  =>  recurso.idDetalle != idDetalleMv);
                            numberRowRecursoMaquina++;
                            arrayRecursosConf.push(json);
                            arrayRecursoTmp.push(json);
                           

                            return false;
                        }

                        if(boolEditarMv)
                        {
                            if(tipo === 'DISCO')
                            {
                                boolCambioDisco = true;
                            }
                            else if(tipo === 'PROCESADOR')
                            {
                                boolCambioProcesador = true;
                            }
                            else
                            {
                                boolCambioMemoria = true;
                            }
                        }
                      
                    }
                }
            }
        }
    });
    
    modeloMv  = Ext.define('recursosModelMV' + tipo, {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idRecursoMv'      + tipo, type: 'integer'},
            {name: 'caracteristicaMv' + tipo, type: 'string'},
            {name: 'valorMv'          + tipo, type: 'string'},
            {name: 'disponibleMv'     + tipo, type: 'integer'},
            {name: 'asignarMv'        + tipo, type: 'integer'},
            {name: 'datastoreMv'      + tipo, type: 'string'},
            {name: 'idDetalleMv'      + tipo, type: 'integer'}
        ]
    });    
      
    storeRecursoscaracteristicaMvsMv = Ext.create('Ext.data.Store', {
        pageSize: 5,
        autoDestroy: true,
        model: 'recursosModelMV' + tipo,
        proxy: {
            type: 'memory'
        }
    }); 
   
    toolbarMv = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        id:'tlbAgregarmv' + tipo,
        items   : 
        [ 
            {
                iconCls: 'icon_add',
                text: 'Agregar ' + tipo,
                id: 'btnAgregarRecursoMv' + tipo,
                scope: this,
                handler: function()
                {
                    rowEditingRecursosMv.cancelEdit();

                    if(tipo === "DISCO")
                    {
                        var recordParamDetDisco = Ext.create('recursosModelMV' + tipo, {
                              idRecursoMvDISCO       : '',
                              caracteristicaMvDISCO  : '',
                              valorMvDISCO           : '',
                              disponibleMvDISCO      : '',
                              asignarMvDISCO         : '',
                              datastoreMvDISCO       : '',
                              idDetalleMvDISCO       : ''
                          });
                          storeRecursoscaracteristicaMvsMv.insert(0, recordParamDetDisco);
                    }
                    else if(tipo === "PROCESADOR"){
                        var recordParamDetProcesador = Ext.create('recursosModelMV' + tipo, {
                              idRecursoMvPROCESADOR       : '',
                              caracteristicaMvPROCESADOR  : '',
                              valorMvPROCESADOR           : '',
                              disponiblePROCESADOR        : '',
                              asignarMvPROCESADOR         : '',
                              datastoreMvPROCESADOR       : '',
                              idDetalleMvPROCESADOR       : ''
                          });
                          storeRecursoscaracteristicaMvsMv.insert(0, recordParamDetProcesador);
                    }
                    else
                    {
                        var recordParamDetMemoria = Ext.create('recursosModelMV' + tipo, {
                              idRecursoMvMEMORIA       : '',
                              caracteristicaMvMEMORIA  : '',
                              valorMvMEMORIA           : '',
                              disponibleMEMORIA        : '',
                              asignarMvMEMORIA         : '',
                              datastoreMvMEMORIA       : '',
                              idDetalleMvMEMORIA       : ''
                          });
                          storeRecursoscaracteristicaMvsMv.insert(0, recordParamDetMemoria);
                    }
                    rowEditingRecursosMv.startEdit(0, 0);
                }
            }
        ]
    });     
   
   
    var infoRecursos = Ext.getCmp("txtInfoRecursos").getValue();
    
    //Si ya existe
    if(!Ext.isEmpty(infoRecursos))
    {        
        var arrayRecursosConfigurados = Ext.JSON.decode(infoRecursos);
        storeRecursoscaracteristicaMvsMv.clearData();
        $.each(arrayRecursosConfigurados, function(i, item)
        {
            if(item.tipo === tipo)
            {
                rowEditingRecursosMv.cancelEdit();
                
                var disponibleMvRecurso = 0;
                var asignarMv           = 0;
                
                //Obtener la cantidadad de recurso disponibleMv por tipo y caracteristicaMv
                $.each(arrayTempResumenRecur, function(i, item1)
                {
                    if(item1.idRecursoMv + tipo === item.idRecursoMv + tipo )
                    {
                        disponibleMvRecurso = item1.disponibleMv + tipo;
                        asignarMv           = item1.usado;
                    }
                });                
               
                
                var recordParamDet = Ext.create('recursosModelMV' + tipo, {
                        idRecursoMv       : item.idRecursoMv + tipo,
                        caracteristicaMv  : item.caracteristicaMv + tipo,
                        valorMv           : item.valorMv + tipo,
                        disponibleMv      : disponibleMvRecurso,
                        asignarMv         : asignarMv,
                        datastoreMv       : !Ext.isEmpty(item.datastoreMv + tipo) ? item.datastoreMv + tipo:'',
                        idDetalleMv       : item.idDetalleMv + tipo
                    });
                    
                storeRecursoscaracteristicaMvsMv.insert(0, recordParamDet);
            }
        });
    }
    
    
    gridRecursoMv = Ext.create('Ext.grid.Panel',{
        width: 600,        
        collapsible: false,        
        layout:'fit',
        dockedItems: [ toolbarMv ],
        store: storeRecursoscaracteristicaMvsMv,
        plugins: [rowEditingRecursosMv],
        id: 'gridRecursoMv' + tipo,
        height: 150,        
        columns: 
        [
            {
                id: 'idDetalleMv' + tipo,
                dataIndex: 'idDetalleMv' + tipo,
                hidden: true,
                hideable: false
            },
            {
                id: 'idRecursoMv' + tipo,
                dataIndex: 'idRecursoMv' + tipo,
                hidden: true,
                hideable: false
            },
            {
                header: "<b>Recurso</b>",
                dataIndex:'caracteristicaMv' + tipo,
                width: 250,
                align: 'left',                
                editor: new Ext.form.field.ComboBox({
                    typeAhead: true,
                    id: 'cmbcaracteristicaMv' + tipo,
                    name: 'cmbcaracteristicaMv' + tipo,
                    valueField: 'id',
                    displayField: 'value',                    
                    store: storeTipoRecursoMv,
                    editable: false,
                    listeners:
                        {
                            select: function(combo, record, index) 
                            {
                                var idRecurso = record[0].data.id;
                                var recurso = 0;
                                var tipo = '';
                                arrayTempResumenRecur.forEach(function(item1)
                                {
                                    if(item1.idRecurso  == idRecurso)
                                    {
                                        recurso = parseInt(item1.disponible) ;   
                                        tipo    = item1.tipo;
                                    }
                                });
                                   
                                if(tipo === "DISCO")
                                {
                                    Ext.getCmp('txtDisponibleMvDISCO').value = recurso;
                                    Ext.getCmp('txtDisponibleMvDISCO').setRawValue(recurso);
                                    Ext.getCmp('txtAsignacionMvDISCO').setEditable(true);  
                                    
                                }
                                else if(tipo === "PROCESADOR"){
                                    
                                    Ext.getCmp('txtDisponibleMvPROCESADOR').value = recurso;
                                    Ext.getCmp('txtDisponibleMvPROCESADOR').setRawValue(recurso);
                                    Ext.getCmp('txtAsignacionMvPROCESADOR').setEditable(true); 

                                }
                                else
                                {                                    
                                    Ext.getCmp('txtDisponibleMvMEMORIA').value = recurso;
                                    Ext.getCmp('txtDisponibleMvMEMORIA').setRawValue(recurso); 
                                    Ext.getCmp('txtAsignacionMvMEMORIA').setEditable(true); 
                                }
                                
                            }
                        }
                }),
                
                renderer: function(value)
                {                    
                    if(!Ext.isEmpty(value))
                    {
                        if(storeTipoRecursoMv.findRecord("id", value))
                        return storeTipoRecursoMv.findRecord("id", value).get('value');

                        if(storeTipoRecursoMv.findRecord("value", value))
                        return value;
                    }
                    else
                    {
                        return value;
                    }
                }
            },
            {
                header: "<b>Pool Total</b>",
                width: 80,
                dataIndex:'valorMv' + tipo,
                id:'valorMv' + tipo,
                align:'center',                             
                renderer: function(value)
                {                    
                    return '<b style="color:green;">'+value+'</b>';
                }
            },
            {
                header: "<b>disponibleMv</b>",
                width: 80,
                dataIndex:'disponibleMv' + tipo,
                id:'disponibleMv' + tipo,
                align:'center',
                editor: new Ext.form.field.Number({
                    id: 'txtDisponibleMv' + tipo,
                    name: 'txtDisponibleMv' + tipo,
                    hidden: true,
                    hideTrigger:true,
                    allowNegative: false,
                    minValue: 0,
                    editable: false,
                }),
                
                renderer: function(value)
                {                    
                    return '<b style="color:blue;">'+value+'</b>';
                }
            },
            {
                header: "<b>A asignar</b>",
                width: 80,
                dataIndex:'asignarMv' + tipo,
                id:'asignarMv' + tipo,
                align:'center',
                editor: new Ext.form.field.Number({
                    id: 'txtAsignacionMv' + tipo,
                    name: 'txtAsignacionMv' + tipo,
                    hidden: true,
                    hideTrigger:true,
                    allowNegative: false,
                    minValue: 0,
                    editable: false
                }),
                renderer: function(value)
                {                    
                    return '<b>'+value+'</b>';
                }
            }            
           
        ]
    });
    
    if(boolEdicionMV)
    {   
        //Se procede a llenar los grid de Disco, Memoria y procesador.

        var arrayMaquina = [];
        arrayMaquina = arrayInformacion.find(maquina => maquina.idMaquina === idMaquina);
        nombreMaquinaVirtual = arrayMaquina.nombre;
        if(arrayMaquina.length >  0 || typeof arrayMaquina != undefined)
        {
            var arrayRecursosMv  = [];
            var arrayTemporalRecursos = [];
            (tipo === 'MEMORIA') ? tipoReal = 'MEMORIA RAM' : tipoReal =  tipo;
            arrayRecursosMv = JSON.parse(arrayMaquina.arrayRecursos).filter(recursosmv => recursosmv.tipo == tipoReal);
            $.each(arrayRecursosMv, function (index, element){
                rowEditingRecursos.cancelEdit();
                if(tipo === "DISCO")
                {
                    var recordParamDetDisco = Ext.create('recursosModelMV' + tipo, {
                          idRecursoMvDISCO       : element.idRecurso,
                          caracteristicaMvDISCO  : element.caracteristica,
                          valorMvDISCO           : element.valor,
                          disponibleMvDISCO      : element.disponible,
                          asignarMvDISCO         : element.asignar,
                          datastoreMvDISCO       : element.datastore,
                          idDetalleMvDISCO       : element.idDetalle
                      });
                      storeRecursoscaracteristicaMvsMv.insert(0, recordParamDetDisco);
                }
                else if(tipo === "PROCESADOR"){
                    var recordParamDetProcesador = Ext.create('recursosModelMV' + tipo, {
                          idRecursoMvPROCESADOR       : element.idRecurso,
                          caracteristicaMvPROCESADOR  : element.caracteristica,
                          valorMvPROCESADOR           : element.valor,
                          disponibleMvPROCESADOR        : element.disponible,
                          asignarMvPROCESADOR         : element.asignar,
                          datastoreMvPROCESADOR       : element.datastore,
                          idDetalleMvPROCESADOR       : element.idDetalle
                        });
                        storeRecursoscaracteristicaMvsMv.insert(0, recordParamDetProcesador);

                }
                else
                {
                    var recordParamDetMemoria = Ext.create('recursosModelMV' + tipo, {
                          idRecursoMvMEMORIA       : element.idRecurso,
                          caracteristicaMvMEMORIA  : element.caracteristica,
                          valorMvMEMORIA           : element.valor,
                          disponibleMvMEMORIA      : element.disponible,
                          asignarMvMEMORIA         : element.asignar,
                          datastoreMvMEMORIA       : element.datastore,
                          idDetalleMvMEMORIA       : element.idDetalle
                        });
                    storeRecursoscaracteristicaMvsMv.insert(0, recordParamDetMemoria);
                }

                var json               = {};
                json['tipo']           = tipoReal;
                json['idRecurso'     ] = element.idRecurso;
                json['caracteristica'] = element.caracteristica;
                json['valor'         ] = element.valor;
                json['disponible'    ] = element.disponible;
                json['asignar'       ] = element.asignar;
                json['datastore'     ] = "";   
                json['idDetalle'     ] = element.idDetalle;       

                arrayRecursosConf.push(json);
                arrayRecursoTmp.push(json);              

            });            
        }        
    }
    
    return gridRecursoMv; 
  
}

function eliminarRegistroDeArray(array, registro)
{
    array = array.filter(function(elem)
    {
        return parseInt(elem.idRecurso) !== parseInt(registro);
    });
    
    return array;
}
