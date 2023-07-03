/*
 * Función para validar un objeto JSON.
 *
 * @author Madeline Haz <mhaz@telconet.ec>
 * @version 1.0 09-07-2019 | Versión Inicial.
 * @return {boolean}
 *
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.1 10-01-2020 Se agrega funcionalidad para ingreso de detalles del parámetro como motivo.
 */

Ext.onReady(function() { 
    
    //Obtener la informacion de los json
    comboHipotesis      = getCombo(getStore(Ext.JSON.decode(arrayHipotesis)));
    comboTipoCaso       = getCombo(getStore(Ext.JSON.decode(arrayTipoCaso)));
    comboTipoAfectacion = getCombo(getStore(Ext.JSON.decode(arrayTipoAfectacion)));
    comboPeriodos       = getCombo(getStore(Ext.JSON.decode(arrayPeriodos)));
    comboParametros     = getCombo(getStore(Ext.JSON.decode(arrayParametros)));
      
    //Define un modelo para el store storeParametrosCab.
    Ext.define('ListaParametrosCabModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdParametro', type: 'int'},
            {name: 'strNombreParametro', type: 'string'},
            {name: 'strDescripcion', type: 'string'},
            {name: 'strModulo', type: 'string'},
            {name: 'strProceso', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'}
        ]
    });    
    
    storeParametrosCab = Ext.create('Ext.data.Store', {
        pageSize: 20,
        model: 'ListaParametrosCabModel',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: urlGetstoreListaParametros,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParametroCabResult',
                totalProperty: 'intTotalParametros'
            },
            simpleSortMode: true
        },
        listeners: {
                load: function() {
                    var recStore = storeParametrosCab.getAt(0);
                    Ext.getCmp('strNombreParametro').setValue(recStore.get('strNombreParametro'));
                    Ext.getCmp('strModulo').setValue(recStore.get('strModulo'));
                    Ext.getCmp('strProceso').setValue(recStore.get('strProceso'));
                    Ext.getCmp('strEstado').setValue(recStore.get('strEstado'));
                    Ext.getCmp('strUsrCreacion').setValue(recStore.get('strUsrCreacion'));
                    Ext.getCmp('strFeCreacion').setValue(recStore.get('strFeCreacion'));
                    Ext.getCmp('strDescripcion').setValue(recStore.get('strDescripcion'));
                }
            }        
    });    
       
    //Formulario estático que solo muestra la información de la cabecera de parámetros.
    formGetParametrosCab = Ext.create('Ext.form.Panel', {
        height: 200,
        width: 1100,
        bodyPadding: 10,
        autoScroll: true,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 3,
            pack: 'center'
        },
        items: [
            {
                id: 'strNombreParametro',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Nombre parámetro',
                name: 'strNombreParametro',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strModulo',
                xtype: 'displayfield',
                fieldLabel: 'Módulo',
                name: 'strModulo',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strProceso',
                xtype: 'displayfield',
                fieldLabel: 'Proceso',
                name: 'strProceso',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strEstado',
                xtype: 'displayfield',
                fieldLabel: 'Estado',
                name: 'strEstado',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strUsrCreacion',
                xtype: 'displayfield',
                fieldLabel: 'Usuario Creación',
                name: 'strUsrCreacion',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strFeCreacion',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Fecha Creación',
                name: 'strFeCreacion',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strDescripcion',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Descripción:',
                name: 'strDescripcion',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            }
        ]             
    });    

    //Define un modelo para el store storeParametrosDet.
    Ext.define('ListaParametrosDetModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdParametroDet', type: 'int'},
            {name: 'intIdParametroCab', type: 'int'},
            {name: 'strDescripcionDet', type: 'string'},
            {name: 'strValor1', type: 'string'},
            {name: 'strValor2', type: 'string'},
            {name: 'strValor3', type: 'string'},
            {name: 'strValor4', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'}
        ]
    });

    // Crea un store para obtener el detalle de los parámetros con la referencia de la cabecera de parámetros.
    storeParametrosDet = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'ListaParametrosDetModel',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetStoreParametrosDet,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParametroDetResult',
                totalProperty: 'intTotalParametros'
            },
            simpleSortMode: true
        }
    });
    
    storeParametrosDet.load();             
    
    var btnActualizarParametros = Ext.create('Ext.button.Button', {
        text: ' <b><i class="fa fa-pencil-square-o" aria-hidden="true"></i></b>&nbsp; Guardar Actualizacion Parametros',
        scope: this,
        disabled: false,
        handler: function() {
            var arrayModificados       = gridParametrosDet.getStore().getUpdatedRecords();           
            var arrayValoresParametros = gridParametrosDet.getStore().data.items;
            var arrayActualizados      = [];
            
            $.each(arrayValoresParametros, function(i, item)
            {               
                if(Ext.isEmpty(item.data.strValor1) || item.data.strValor1==='<empty string>')
                {                    
                    Ext.Msg.alert('Alerta', 'Existen valores nulos o vacios en el parametro '+item.data.strDescripcionDet+ ' , por favor revisar');
                }
            });
            
            $.each(arrayValoresParametros, function(i, item)
            {
                if(!Ext.isEmpty(item.data.strValor1))
                {                    
                    if(validarCambiado(arrayModificados,item.data))
                    {                  
                        //agregar a un array para envio de actualizacion                       
                        arrayActualizados.push(item.data);
                    }
                }
            });
            
            if(!Ext.isEmpty(arrayActualizados))
            {
                Ext.MessageBox.show({
                    msg: 'Actualizando registro...',
                    title: 'Procesando',
                    progressText: 'Actualizando.',
                    progress: true,
                    closable: false,
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });                               

                Ext.Ajax.request({
                    url: urlActualizarDetalle,
                    method: 'POST',
                    timeout: 60000,
                    params: {
                        data : Ext.JSON.encode(arrayActualizados)
                    },
                    success: function(response) {
                        var text     = Ext.JSON.decode(response.responseText);

                        var strStatus   = '';

                        if(text.strStatus === '100')
                        {
                            strStatus = 'Información';
                        }
                        else if(text.strStatus === '001')
                        {
                            strStatus = 'Error';
                        }
                        else if(text.strStatus === '000')
                        {
                            strStatus = 'Alerta';
                        }                                
                        
                        Ext.Msg.alert(strStatus, text.strMessageStatus, function(btn){
                        if(btn=='ok')
                            {
                                storeParametrosDet.load();
                                storeParametrosHist.load();
                            }
                        });
                    },
                    failure: function(result) {
                        //Por alguna excepción no controlada el registro vuelve a sus valores originales.                          
                        Ext.Msg.alert('Error ', 'Ha ocurrido un error general, notificar a Sistemas');
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Alerta','No existen cambios detectados a registrar', function(btn){
                        if(btn=='ok')
                        {
                            storeParametrosDet.load();
                        }
                });                              
            }           
        }
    });
    
    
    //Define el boton para crear la cabecera de parámetros usado en el toolbar toolbarCab
    var btnCrearParametroCab = Ext.create('Ext.button.Button', {
        text: ' <i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Nuevos Motivos/Valores',
        scope: this,
        disabled: false,
        handler: function() {

            /**Crea el plugin para la edición de filas para el grid gridCreaParametrosDet que se encuentra como item en el
             * formulario formCreaParametrosCab
             */
            rowEditingCreaParamDet = Ext.create('Ext.grid.plugin.RowEditing', {
                clicksToMoveEditor: 1,
                autoCancel: false,
                listeners: {
                    canceledit: function(editor, e, eOpts) {
                        e.store.remove(e.record);
                    },
                    afteredit: function(roweditor, changes, record, rowIndex) {
                        var intCountGridDetalle = Ext.getCmp('gridCreaParametrosDet').getStore().getCount();
                        var selectionModel      = Ext.getCmp('gridCreaParametrosDet').getSelectionModel();
                        
                        //Selecciona la fila 0 en el grid gridCreaParametrosDet
                        selectionModel.select(0);

                        //Itera el grid gridCreaParametrosDet para verificar que el nuevo valor no esté ingresado.
                        for (var i = 1; i < intCountGridDetalle; i++)
                        {                           
                            //Si los nuevos valores son iguales a alguno anteriormente ingresado muestra un mensaje y sale del loop.
                            if (Ext.getCmp('gridCreaParametrosDet').getStore().getAt(i).get('strValor1') === changes.newValues.strValor1 )
                            {
                                Ext.Msg.alert('Error', 'Motivo/Valor ya se encuetra seleccionado.');
                                //Empieza la edición en fila 0 en el grid gridCreaParametrosDet
                                rowEditingCreaParamDet.startEdit(0, 0);
                                break;
                            }           
                            
                            
                        }       
                        
                      
                        for (var j = 0; j < Ext.getCmp('gridParametrosDet').getStore().getCount(); j++)
                            {                                
                                
                                //Si los nuevos valores son iguales a alguno anteriormente ingresado muestra un mensaje y sale del loop.
                                if (Ext.getCmp('gridParametrosDet').getStore().getAt(j).get('strValor1') === changes.newValues.strValor1 )
                                {
                                    Ext.Msg.alert('Error', 'Motivo/Valor ya se encuetra ingresado dentro de los parametros');
                                    //Empieza la edición en fila 0 en el grid gridCreaParametrosDet
                                    rowEditingCreaParamDet.startEdit(0, 0);
                                    break;
                                }                                                        
                            }
                    }
                }
            });

            //Define un modelo para el store storeCreaParametrosDet
            Ext.define('CrearParametrosDetModel', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'strDescripcion', type: 'string'},
                    {name: 'strValor1', type: 'string'},
                    {name: 'strValor2', type: 'string'}
                ]
            });

            //Crea un store estático para el grid gridCreaParametrosDet que se encuentra como item en el formulario formCreaParametrosCab
            storeCreaParametrosDet = Ext.create('Ext.data.Store', {
                pageSize: 5,
                autoDestroy: true,
                model: 'CrearParametrosDetModel',
                proxy: {
                    type: 'memory'
                }
            });

            /**Crea el boton para agregar una fila al grid gridCreaParametrosDet
             * boton usado en el toolbar toolbarCreaParamDet
             */
            btnCrearParametroCabDet = Ext.create('Ext.button.Button', {
                text: 'Agregar Motivo/Valor',
                width: 160,
                iconCls: 'button-grid-crearSolicitud-without-border',
                handler: function() {

                    rowEditingCreaParamDet.cancelEdit();

                    //Crea una nueva fila en el grid gridCreaParametrosDet con el model definido CrearParametrosDetModel
                    var recordParamDet = Ext.create('CrearParametrosDetModel', {
                        strDescripcion: '',
                        strValor1: '',
                        strValor2: ''
                    });
                    //Inserta la fila en el store del grid gridCreaParametrosDet
                    storeCreaParametrosDet.insert(0, recordParamDet);
                    //Habilita la edición de la fila del grid gridCreaParametrosDet
                    rowEditingCreaParamDet.startEdit(0, 0);
                    //Valida que el grid tenga filas creadas
                    if (Ext.getCmp('gridCreaParametrosDet').getStore().getCount() > 1)
                    {

                        //Valida que no se agregue a otra fila si la que se está editando actualmente tiene los campos vacios
                        if ('' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(1).data.strDescripcion.trim() ||
                            ('' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(1).data.strValor1.trim() &&
                                '' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(1).data.strValor2.trim()))
                        {
                            Ext.Msg.alert('Error', 'Debes ingresar la descripcion y al menos un valor para agregar un nuevo parametro.');
                            var selectionModel = Ext.getCmp('gridCreaParametrosDet').getSelectionModel();
                            //Cancela la edición de la fila en el grid gridCreaParametrosDet
                            rowEditingCreaParamDet.cancelEdit();
                            //Remueve la fila en el grid gridCreaParametrosDet
                            storeCreaParametrosDet.remove(selectionModel.getSelection());
                            //Selecciona la fila 0 en el grid gridCreaParametrosDet
                            selectionModel.select(0);
                            //Empieza la edición en fila 0 en el grid gridCreaParametrosDet
                            rowEditingCreaParamDet.startEdit(0, 0);
                        }
                    }
                }
            });

            /*Crea boton para eliminar una fila de store storeCreaParametrosDet del grid gridCreaParametrosDet, 
             * boton usado en el toolbar toolbarCreaParamDet*/
            btnDeleteParametroCabDet = Ext.create('Ext.button.Button', {
                text: 'Eliminar Parametro',
                width: 130,
                iconCls: 'button-grid-quitarFacturacionElectronica-without-border',
                handler: function() {
                    var gridCreaParametrosDet = Ext.getCmp('gridCreaParametrosDet');
                    var selectionModel = gridCreaParametrosDet.getSelectionModel();
                    //Cancela la edición de la fila en el grid gridCreaParametrosDet
                    rowEditingCreaParamDet.cancelEdit();
                    //Remueve la fila en el grid gridCreaParametrosDet
                    storeCreaParametrosDet.remove(selectionModel.getSelection());
                    //Selecciona la fila 0 del store storeCreaParametrosDet cuando este tenga registros.
                    if (storeCreaParametrosDet.getCount() > 0) {
                        selectionModel.select(0);
                    }
                }
            });

            //Crea el toolbar para el grid gridCreaParametrosDet
            toolbarCreaParamDet = Ext.create('Ext.toolbar.Toolbar', {
                dock: 'top',
                align: '->',
                items:
                    [{xtype: 'tbfill'},
                        btnCrearParametroCabDet,
                        btnDeleteParametroCabDet
                    ]
            });

            windowsCreaParametrosCab = '';
            storeNuevoParametro = [];

            //Crea el formulario para crear la cabecera de parámetros usado en el panel panelCreaParametrosCab
            formCreaParametrosCab = Ext.create('Ext.form.Panel', {
                height: 345,
                width: 600,
                bodyPadding: 10,
                layout: {
                    tdAttrs: {style: 'padding: 5px;'},
                    type: 'table',
                    columns: 3,
                    pack: 'center'
                },
                items: [

                    {
                        colspan: 3,
                        xtype: 'textfield',
                        fieldLabel: '',
                        id: '',
                        value: '',
                        width: 500,
                        disabled:true,
                        regex: /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/
                    },
                    {
                        colspan: 3,
                        xtype: 'grid',
                        store: storeCreaParametrosDet,
                        plugins: [rowEditingCreaParamDet],
                        dockedItems: [toolbarCreaParamDet],
                        id: 'gridCreaParametrosDet',
                        height: 220,
                        columns: [
                            {
                                header: "Descripción",
                                dataIndex: 'strDescripcion',
                                width: 250,
                                editor: new Ext.form.field.ComboBox({
                                    typeAhead: true,
                                    valueField: 'id',
                                    displayField: 'value',                    
                                    store: getStore(Ext.JSON.decode(arrayParametros)),
                                    editable: false,
                                    listeners:
                                        {
                                            select: function(combo, record, index) 
                                            {
                                                Ext.getCmp('cmbMotivos').value = "";
                                                Ext.getCmp('cmbMotivos').setRawValue("");
                                                if(record[0].data.value === 'MOTIVO DE INDISPONIBILIDAD PARA NC')
                                                {                                                    
                                                    Ext.getCmp('cmbMotivos').bindStore(getStore(Ext.JSON.decode(arrayHipotesis)));
                                                }
                                                if(record[0].data.value === 'TIPO AFECTACION')
                                                {                                                    
                                                    Ext.getCmp('cmbMotivos').bindStore(getStore(Ext.JSON.decode(arrayTipoAfectacion)));
                                                }
                                                if(record[0].data.value === 'TIPO CASO')
                                                {                                                    
                                                    Ext.getCmp('cmbMotivos').bindStore(getStore(Ext.JSON.decode(arrayTipoCaso)));
                                                }
                                                if(record[0].data.value === 'TIPO DE PERIODO')
                                                {                                                    
                                                    Ext.getCmp('cmbMotivos').bindStore(getStore(Ext.JSON.decode(arrayPeriodos)));
                                                }
                                            }
                                        }
                                }),
                            },
                            {
                                header: "Valor/Motivo",
                                dataIndex: 'strValor1',
                                width: 250,
                                editor: new Ext.form.field.ComboBox({
                                    id:'cmbMotivos',
                                    displayField: 'value',
                                    valueField: 'value',
                                    autoSelect: false,
                                    queryMode: 'local',
                                    selectOnFocus:true,         
                                    typeAhead:false,
                                    minChars:2
                                })
                            }
                        ]
                    }
                ],
                buttonAlign: 'center',
                buttons: [
                    {
                        text: 'Guardar Motivo',
                        name: 'btnGuardar',
                        id: 'idBtnGuardar',
                        disabled: false,
                        handler: function() {
                            var arrayCreaParametrosDet = new Object();
                            jsonCreaParametrosDet = '';
                            var arrayGridCreaParametrosDet = Ext.getCmp('gridCreaParametrosDet');
                            arrayCreaParametrosDet['inTotal'] = arrayGridCreaParametrosDet.getStore().getCount();
                            arrayCreaParametrosDet['arrayData'] = new Array();
                            arrayCreaParametrosDetData = Array();
                            var boolPermite = true;
                            //Valida que el grid gridCreaParametrosDet tenga datos
                            if (arrayGridCreaParametrosDet.getStore().getCount() !== 0)
                            {
                                if ('' === arrayGridCreaParametrosDet.getStore().getAt(0).data.strValor1.trim() &&
                                        '' === arrayGridCreaParametrosDet.getStore().getAt(0).data.strValor2.trim())
                                {
                                    boolPermite = false;
                                    Ext.Msg.alert('Alerta!', 'Debe ingresar un nombre y descripción para la cabecera del par\u00e1metro.');
                                }
                                //Itera el grid gridCreaParametrosDet y realiza un push en la variable arrayCreaParametrosDetData
                                for (var intCounterStore = 0;
                                    intCounterStore < arrayGridCreaParametrosDet.getStore().getCount(); intCounterStore++)
                                {

                                    arrayCreaParametrosDetData.push(arrayGridCreaParametrosDet.getStore().getAt(intCounterStore).data);
                                }

                                //Seta arrayCreaParametrosDetData en arrayCreaParametrosDet['arrayData']
                                arrayCreaParametrosDet['arrayData'] = arrayCreaParametrosDetData;

                                /*Realiza encode a arrayCreaParametrosDet para ser enviada por el request 
                                 * de creación de parámetros al controlador
                                 */
                                jsonCreaParametrosDet = Ext.JSON.encode(arrayCreaParametrosDet);

                            }
                            //Envia a crear los parametros siempre cuando no haya un registro en nulo
                            if (boolPermite)
                            {
                                Ext.MessageBox.show({
                                    msg: 'Guardando...',
                                    title: 'Procesando',
                                    progressText: 'Guardando.',
                                    progress: true,
                                    closable: false,
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });

                                Ext.Ajax.request({
                                    url: urlCreaMotivoParametroDet,
                                    method: 'POST',
                                    timeout: 60000,
                                    params: {
                                        jsonCreaParametrosDet: jsonCreaParametrosDet                                    
                                    },
                                    success: function(response) {
                                        var text = Ext.decode(response.responseText);
                                        Ext.Msg.alert('Información', text.strMessageStatus);
                                        //Valida que el estatus de respuesta sea 100 para destruir la ventana y resetear el formulario.
                                        if ("100" === text.strStatus) {
                                            formCreaParametrosCab.getForm().reset();
                                            formCreaParametrosCab.destroy();
                                            windowsCreaParametrosCab.destroy();
                                            storeParametrosDet.load();                                                                                       
                                        }
                                        Ext.Msg.alert('Información', text.strMessageStatus);
                                    },
                                    failure: function(result) {
                                        Ext.Msg.alert('Error ', result.statusText);
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function() {
                            this.up('form').getForm().reset();
                            this.up('window').destroy();
                        }
                    }]
            });

            //Panel usado como item en la ventana windowsCreaParametrosCab
            panelCreaParametrosCab = new Ext.Panel({
                width: '100%',
                height: '100%',
                items: [
                    formCreaParametrosCab
                ]
            });

            //Ventana que contiene el panel panelCreaParametrosCab para la creacion de cabecera de parámetros y sus detalles.
            windowsCreaParametrosCab = Ext.widget('window', {
                title: 'Nuevo Motivo',
                height: 380,
                width: 550,
                modal: true,
                resizable: false,
                items: [panelCreaParametrosCab]
            }).show();
        }
    });
    //Crea el toolbar usado en el grid gridParametrosDet
    toolbarCab = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [
                {xtype: 'tbfill'},
                btnActualizarParametros,
                btnCrearParametroCab
            ]
    });
    
   combo = null;
    // Crea el grid para mostrar el detalle de la cabecera de parámetros.
    gridParametrosDet = Ext.create('Ext.grid.Panel', {
        title: 'Listado de Motivos',
        store: storeParametrosDet,
        id: 'gridParametrosDet',
        cls: 'custom-grid',
        autoScroll: false,
        plugins: [{
                    ptype: 'cellediting',
                    clicksToEdit: 1
                }],
        dockedItems: [toolbarCab],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroDet', hidden: true},
            {
                header: 'Descripción ',
                dataIndex: 'strDescripcionDet',
                width: 320
            },            
            {
                header: 'Valor/Motivo', 
                dataIndex: 'strValor1',
                width: 250  ,        
                
                getEditor: function(record) {
                    
                    var descripcion = record.get('strDescripcionDet');

                    if(descripcion === 'TIPO DE PERIODO')
                    {
                        combo = comboPeriodos;
                    }
                    if(descripcion === 'MOTIVO DE INDISPONIBILIDAD PARA NC')
                    {
                        combo = comboHipotesis;
                    }
                    if(descripcion === 'TIPO AFECTACION')
                    {
                        combo = comboTipoAfectacion;
                    }
                    if(descripcion === 'TIPO CASO')
                    {
                        combo = comboTipoCaso;
                    }

                    if (record.get('strValor3') === 'T')
                    {
                        return Ext.create('Ext.grid.CellEditor', {
                            field: Ext.create('Ext.form.field.Text', {
                                maskRe: /[0-9.-]/,
                                validator: function (v) {
                                    return /^-?[0-9]*(\.[0-9]{1,2})?$/.test(v) ? true : 'Only positive/negative float (x.yy)/int formats allowed!';
                                },
                                listeners: {
                                    change: function (e, text, prev) {
                                        if (!/^-?[0-9]*(\.[0-9]{0,2})?$/.test(text))
                                        {
                                            this.setValue(prev);
                                        }
                                    }
                                }

                                        })
                        });
                    } 
                    else 
                    {
                        return Ext.create('Ext.grid.CellEditor', {
                            field: combo                                
                        });
                    }
                }
            },             
            {header: 'Estado', dataIndex: 'strEstado', width: 80},
            {header: 'Usr. Creación', dataIndex: 'strUsrCreacion', width: 100},
            {header: 'Fe. Creación', dataIndex: 'strFeCreacion', width: 100},
             {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 70,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var tipo = rec.get('strValor4');
                            
                            if(tipo === 'S')
                            {
                                return 'button-grid-delete';
                            }
                            
                            return 'icon-invisible';
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var tipo = storeParametrosDet.getAt(rowIndex).get('strValor4');
                            var desc = storeParametrosDet.getAt(rowIndex).get('strDescripcionDet');
                            
                            if(tipo === 'S')
                            {
                                var arrayValoresParametros = gridParametrosDet.getStore().data.items;
                                var cont = 0;
                                $.each(arrayValoresParametros, function(i, item)
                                {
                                    if(item.data.strDescripcionDet === desc)
                                    {
                                        cont++;
                                    }
                                });
                                
                                if(cont === 1)
                                {
                                    Ext.Msg.alert('Alerta', 'No puede eliminar el parámetro '+desc+' dado que es el último Activo');
                                    return;
                                }  
                                else
                                {
                                    Ext.MessageBox.show({
                                        title: 'Eliminar Registro',
                                        msg: 'Seguro que desea eliminar el Motivo?',
                                        buttons: Ext.MessageBox.OKCANCEL,
                                        icon: Ext.MessageBox.WARNING,
                                        fn: function(btn)
                                        {
                                            if(btn == 'ok')
                                            {
                                                 Ext.MessageBox.show({
                                                    msg: 'Eliminado registro...',
                                                    title: 'Procesando',
                                                    progressText: 'Eliminando.',
                                                    progress: true,
                                                    closable: false,
                                                    width: 300,
                                                    wait: true,
                                                    waitConfig: {interval: 200}
                                                });
                                                 Ext.Ajax.request({
                                                    url: urlDeleteDetalle,
                                                    method: 'POST',
                                                    timeout: 60000,
                                                    params: {
                                                        data : storeParametrosDet.getAt(rowIndex).get('intIdParametroDet')
                                                    },
                                                    success: function(response) {
                                                        var text     = Ext.JSON.decode(response.responseText);

                                                        var strStatus   = '';

                                                        if(text.strStatus === '100')
                                                        {
                                                            strStatus = 'Información';
                                                        }
                                                        else if(text.strStatus === '001')
                                                        {
                                                            strStatus = 'Error';
                                                        }
                                                        else if(text.strStatus === '000')
                                                        {
                                                            strStatus = 'Alerta';
                                                        }                                
                                                        Ext.Msg.alert(strStatus, text.strMessageStatus);
                                                        storeParametrosDet.load();

                                                    },
                                                    failure: function(result) {
                                                        //Por alguna excepción no controlada el registro vuelve a sus valores originales.                          
                                                        Ext.Msg.alert('Error ', 'Ha ocurrido un error genera, notificar a Sistemas');
                                                    }
                                                });
                                            } 
                                            else 
                                            {
                                                return;
                                            }
                                        }
                                    });                                   
                                }
                                //end delete detalle adicional
                            }
                        }
                    }
                ]
            }
        ],
        height: 270,
        width: 1000,
        renderTo: 'ListadosDetalleParametros',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeParametrosDet,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });     
    
    
    /**Panel que contiene el formulario de presentación de la cabecera de parámetro, el panel de búsqueda y el grid
    * usado en la ventana windowsParametrosDet.
    */
   panelParametrosDet = new Ext.Panel({
       width: 1000,
       height: '100%',
       items: [
           formGetParametrosCab
       ],
       renderTo: 'ListadosCabeceraParametros',
   });           
        
});

function validateString(strCampo) {
    var boolCorrecto = false;
    if (/^([A-Za-z]+[A-Za-z0-9\_\-\s]*)$/.test(strCampo))
    {
        boolCorrecto = true;
    }
    return boolCorrecto;
}

function getStore(arrayOriginal)
{
    var array = [];    
    
    $.each(arrayOriginal, function(i, item) 
    {
        var json      = {};
        json['id']    = item.valor;
        json['value'] = item.valor;
        array.push(json);        
    });
    
    
    var store = new Ext.data.Store({
        fields: ['id','value'],
        data: array
    });
    
    return store;
}

function getCombo(store)
{
    return Ext.create('Ext.form.field.ComboBox', {
        store: store,
        displayField: 'value',
        valueField: 'id',
        autoSelect: false,
        queryMode: 'local',
        selectOnFocus:true,         
        typeAhead:false,
        minChars:2
    });
}

function validarCambiado(array, data)
{
    var flag = false;
    $.each(array, function(i, item)
    {
        
        if(item.data.strDescripcionDet === data.strDescripcionDet)
        {            
            if(data.strValor1 !== item.raw.strValor1)
            {
                flag = true;
                return;
            }
        }
    });
    
    return flag;
}
