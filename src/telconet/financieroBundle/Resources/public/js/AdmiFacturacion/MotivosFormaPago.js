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
        height: 270,
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
            },
            {
                id: 'strValor1',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: '¿Genera Factura?',
                name: 'strValor1',
                labelStyle: 'font-weight:bold;',
                value: 'EL MOTIVO GENERA FACTURA SI (S) O  NO (N)',
                textAlign: 'left'
            },
            {
                id: 'strValor2',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Nombre Motivo:',
                name: 'strValor2',
                labelStyle: 'font-weight:bold;',
                value: 'MOTIVO PARA CAMBIO DE FORMA DE PAGO',
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
    
        //Define un modelo para el store storeParametrosHist.
    Ext.define('ListaParametrosHistModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdParametroDet', type: 'int'},
            {name: 'intIdParametroCab', type: 'int'},
            {name: 'strDescripcionDet', type: 'string'},
            {name: 'strValor1', type: 'string'},
            {name: 'strValor2', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'},
            {name: 'strUsrUltMod', type: 'string'},
            {name: 'strFeUltMod', type: 'string'},            
        ]
    });
    
    // Crea un store para obtener el detalle de los parámetros con la referencia de la cabecera de parámetros.
    storeParametrosHist = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'ListaParametrosHistModel',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetStoreParametrosHist,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParametroHistResult',
                totalProperty: 'intTotalParametros'
            }
        }
    });
        
    storeParametrosHist.load();    
    
    //Crea el plugin para la edición de filas del grid gridParametrosDet.
    rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText  : 'Guardar',
        cancelBtnText: 'Cancelar',      
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {
            beforeedit: function(editor, context) {
                return true;
            },
            afteredit: function(roweditor, changes, record, rowIndex) {                
                //Valida que los nuevos campos a ingresar no sean nulos.
                if ( '' !== changes.newValues.strDescripcionDet.trim() &&
                    ('' !== changes.newValues.strValor1.trim() ))
                {   
                    //Valida que los campos nuevos sean distintos a los de la base para hacer la petición ajax al controlador
                    if (changes.originalValues.strDescripcionDet.trim() !== changes.newValues.strDescripcionDet.trim() ||
                        changes.originalValues.strValor1.trim()         !== changes.newValues.strValor1.trim())
                    {
                        /**La strActualizaSoloDescripcion variable define si se actualiza solo el campo 
                         * descripción de la fila o no. Cuando la variable strActualizaSoloDescripcion es enviada 
                         * en request con 'NO' se validará del lado del controlador AdmiParametroCabController con el método 
                         * validaParametroCab que no exista parámetro en la base con los mismos datos nuevos a ingresar.
                         */
                        var strActualizaSoloDescripcion = 'NO';
                        if (changes.originalValues.strDescripcionDet.trim() !== changes.newValues.strDescripcionDet.trim() &&
                            (changes.originalValues.strValor1.trim() === changes.newValues.strValor1.trim()))
                        {
                            /**
                             * Cuando la variable strActualizaSoloDescripcion es enviada 
                             * en request con 'SI' no se validará del lado del controlador que ya exista el parámetro.
                             */
                            strActualizaSoloDescripcion = 'SI';
                        }
                        //Valida que el valor1 ingresado sea N o S.
                        if ('S' !== changes.newValues.strValor1.trim() && ('N' !== changes.newValues.strValor1.trim()))
                        {
                            Ext.Msg.alert('Error ', 'Valor ingresado no v\u00e1lido, debe ser N o S');
                            var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                            recStore.set('strValor1', changes.originalValues.strValor1);                 
                            return false;
                        }                        
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
                            url: urlActualizaParametroDet,
                            method: 'POST',
                            timeout: 60000,
                            params: {
                                intIdParametroDet: changes.originalValues.intIdParametroDet,
                                strDescripcion: changes.newValues.strDescripcionDet.trim(),                                
                                strValor1: changes.newValues.strValor1.trim(),
                                strValor2: changes.newValues.strValor2.trim(),
                                strActualizaSoloDescripcion: strActualizaSoloDescripcion
                            },
                            success: function(response) {
                                var text     = Ext.decode(response.responseText);
                                var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                                var strStatus   = '';
                                /*Cuando el strStatus enviado en el Response es != 100 el registro vuelve a sus 
                                 * valores originales.
                                 */
                                if ("100" !== text.strStatus) {
                                    recStore.set('strDescripcionDet', changes.originalValues.strDescripcionDet);
                                    recStore.set('strValor1', changes.originalValues.strValor1);                                    
                                }
                            
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
                                    storeParametrosHist.load();
                            },
                            failure: function(result) {
                                //Por alguna excepción no controlada el registro vuelve a sus valores originales.
                                var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                                recStore.set('strDescripcionDet', changes.originalValues.strDescripcionDet);
                                recStore.set('strValor1', changes.originalValues.strValor1);                                
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    } else
                    {
                        Ext.Msg.alert('Información', 'No hay cambios en el registro.');
                    }
                }//Validación de Campos.
                else {
                    Ext.Msg.alert('Alerta!',
                        'No puede insertar el regitro vacío, Debe ingresar una descripción y al menos un valor.');
                }
            }
        }
    });

    //Define el boton para crear la cabecera de parámetros usado en el toolbar toolbarCab
    var btnCrearParametroCab = Ext.create('Ext.button.Button', {
        text: 'Nuevo Motivo',
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
                        var selectionModel = Ext.getCmp('gridCreaParametrosDet').getSelectionModel();
                        //Selecciona la fila 0 en el grid gridCreaParametrosDet
                        selectionModel.select(0);
                        if (false === validateString(changes.newValues.strDescripcion.trim()))
                        {
                            Ext.Msg.alert('Error', 'No puede ingresar un caracter especial al inicio de la cadena.');
                            //Empieza la edición en fila 0 en el grid gridCreaParametrosDet
                            rowEditingCreaParamDet.startEdit(0, 0);
                            return false;
                        }
                        //Valida que el valor1 ingresado sea N o S.
                        if ('S' !== changes.newValues.strValor1.trim() && ('N' !== changes.newValues.strValor1.trim()))
                        {
                            Ext.Msg.alert('Error ', 'Valor ingresado no v\u00e1lido, debe ser N o S');
                            var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                            recStore.set('strValor1', changes.originalValues.strValor1);                            
                            return false;
                        }                        
                        if (intCountGridDetalle > 0)
                        {

                            //Valida que no se agregue a otra fila si la que se está editando actualmente tiene los campos vacios
                            if  ('' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(0).data.strDescripcion.trim() ||
                                ('' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(0).data.strValor1.trim() &&
                                 '' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(0).data.strValor2.trim()))
                            {
                                Ext.Msg.alert('Error', 
                                              'Debes ingresar la descripci\u00f3n y al menos un valor para agregar un nuevo par\u00e1metro.');
                                //Cancela la edición de la fila en el grid gridCreaParametrosDet
                                rowEditingCreaParamDet.cancelEdit();
                                //Selecciona la fila 0 en el grid gridCreaParametrosDet
                                selectionModel.select(0);
                                //Empieza la edición en fila 0 en el grid gridCreaParametrosDet
                                rowEditingCreaParamDet.startEdit(0, 0);
                            }
                        }
                        //Itera el grid gridCreaParametrosDet para verificar que el nuevo valor no esté ingresado.
                        for (var i = 1; i < intCountGridDetalle; i++)
                        {
                            //Si los nuevos valores son iguales a alguno anteriormente ingresado muestra un mensaje y sale del loop.
                            if (Ext.getCmp('gridCreaParametrosDet').getStore().getAt(i).get('strValor1') === changes.newValues.strValor1 &&
                                Ext.getCmp('gridCreaParametrosDet').getStore().getAt(i).get('strValor2') === changes.newValues.strValor2 )
                            {
                                Ext.Msg.alert('Error', 'Parámetro ya se encuetra ingresado.');
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
                text: 'Agregar Motivo',
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
                text: 'Eliminar Motivo',
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

            //Crea el formulario para crear la cabecera de parámetros usado en el panel panelCreaParametrosCab
            formCreaParametrosCab = Ext.create('Ext.form.Panel', {
                height: 345,
                width: 810,
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
                        width: 770,
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
                                width: 270,                                
                                editor: {
                                    allowBlank: false,
                                    regex: /^([A-Za-z]+[A-Za-z0-9\_\-\s]*)$/
                                }
                            },
                            {
                                header: "¿Genera Factura?",
                                dataIndex: 'strValor1',
                                width: 145,
                                editor: {
                                    allowBlank: false,
                                    regex: /^([SN])$/
                                }
                            },
                            {
                                header: "Nombre Motivo",
                                dataIndex: 'strValor2',
                                width: 300,
                                editor: {
                                    allowBlank: false,
                                    regex: /^([A-Za-z]+[A-Za-z0-9\_\-\s]*)$/
                                }
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
                                            storeParametrosHist.load();                                            
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
                width: 812,
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
            [{xtype: 'tbfill'},
                btnCrearParametroCab
            ]
    });
    // Crea el grid para mostrar el detalle de la cabecera de parámetros.
    gridParametrosDet = Ext.create('Ext.grid.Panel', {
        title: 'Listado de Motivos',
        store: storeParametrosDet,
        id: 'gridParametrosDet',
        cls: 'custom-grid',
        autoScroll: false,
        plugins: [rowEditing],
        dockedItems: [toolbarCab],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroDet', hidden: true},
            {
                header: 'Descripción ',
                dataIndex: 'strDescripcionDet',
                width: 450,
                editor: {
                    allowBlank: false,
                    maskRe: /([A-Za-z0-9\s\_\-]+)/i, 
                    regex: /^([A-Za-z0-9\s\_\-]+)$/
                }
            },            
            {
                header: '¿Genera Factura?',
                dataIndex: 'strValor1',
                width: 100,                
                editor: {
                    allowBlank: false,
                    maskRe: /^([A-Z])/ ,	
                    regex : /^([A-Z]){1,1}$/                   
                }
            }, 
            {header: 'Motivo', dataIndex: 'strValor2', width: 300},
            {header: 'Estado', dataIndex: 'strEstado', width: 80},
            {header: 'Usr. Creación', dataIndex: 'strUsrCreacion', width: 100},
            {header: 'Fe. Creación', dataIndex: 'strFeCreacion', width: 100}          
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
    
    // Crea el grid para mostrar el historial de edición de parámetros.
    gridParametrosHist = Ext.create('Ext.grid.Panel', {
        title: 'Historial de Par\u00e1metros',
        store: storeParametrosHist,
        id: 'gridParametrosHist',
        cls: 'custom-grid',
        autoScroll: false,
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroHist', hidden: true},
            {
                header: 'Detalle',
                dataIndex: 'strDescripcionDet',
                width: 650
            },
            {header: 'Usr. Modifica.', dataIndex: 'strUsrUltMod', width: 150},
            {header: 'Fe. Modifica.', dataIndex: 'strFeUltMod', width: 150}          
            
        ],
        height: 200,
        width: 1000,
        renderTo: 'ListadoHistorialParametros',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeParametrosHist,
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