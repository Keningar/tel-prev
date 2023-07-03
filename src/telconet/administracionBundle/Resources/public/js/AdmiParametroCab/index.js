Ext.onReady(function() {
    //array que contiene el titulo de las ventanas de mensajes
    var arrayTituloMensajeBox = [];
    arrayTituloMensajeBox['100'] = 'Información';
    arrayTituloMensajeBox['001'] = 'Error';
    arrayTituloMensajeBox['000'] = 'Alerta';

    //Define un modelo para el store storeParametrosCab
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

    //Crea un campo para el panel de busqueda objFilterPanel
    txtNombreParametro = Ext.create('Ext.form.Text',
        {
            id: 'txtNombreParametro',
            name: 'txtNombreParametro',
            fieldLabel: 'Nombre Parametro',
            labelAlign: 'left',
            allowBlank: true,
            width: 325,
            regex: /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/
        });

    //Crea un campo para el panel de busqueda objFilterPanel
    txtDescripcion = Ext.create('Ext.form.Text',
        {
            id: 'txtDescripcion',
            name: 'txtDescripcion',
            fieldLabel: 'Descripción',
            labelAlign: 'left',
            allowBlank: true,
            width: 325,
            regex: /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/
        });

    //Crea un campo para el panel de busqueda objFilterPanel
    txtModulo = Ext.create('Ext.form.Text',
        {
            id: 'txtModulo',
            name: 'txtModulo',
            fieldLabel: 'Módulo',
            labelAlign: 'left',
            allowBlank: true,
            width: 325,
            regex: /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/
        });

    //Crea un campo para el panel de busqueda objFilterPanel
    txtProceso = Ext.create('Ext.form.Text',
        {
            id: 'txtProceso',
            name: 'txtProceso',
            fieldLabel: 'Proceso',
            labelAlign: 'left',
            allowBlank: true,
            width: 325,
            regex: /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/
        });

    //Crea un campo para el panel de busqueda objFilterPanel
    txtUsrCreacion = Ext.create('Ext.form.Text',
        {
            id: 'txtUsrCreacion',
            name: 'txtUsrCreacion',
            fieldLabel: 'Usuario Creación',
            labelAlign: 'left',
            allowBlank: true,
            width: 325,
            regex: /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/
        });

    //Define un store para estados objFilterPanel
    storeEstados = Ext.create('Ext.data.Store', {
        fields: ['strValueField', 'strDisplayField'],
        data: [
            {"strValueField": "Todos", "strDisplayField": "Todos"},
            {"strValueField": "Activo", "strDisplayField": "Activo"},
            {"strValueField": "Eliminado", "strDisplayField": "Eliminado"}
            //...
        ]
    });

    //Crea el combo box usado para el panel de busqueda objFilterPanel
    cboEstados = Ext.create('Ext.form.ComboBox', {
        fieldLabel: 'Estados',
        id: 'cboEstados',
        name: 'cboEstados',
        store: storeEstados,
        queryMode: 'local',
        displayField: 'strDisplayField',
        valueField: 'strValueField',
        editable: false
    });

    //Store que realiza la petición ajax para el grid: gridListaParametros
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
        }
    });

    //Panel con los campos que se usan como filtros para buscar en el grid: gridListaParametros del store: storeParametrosCab
    objFilterPanel = Ext.create('Ext.panel.Panel', {
        border: false,
        buttonAlign: 'center',
        layout: {
            tdAttrs: {style: 'padding: 10px;'},
            type: 'table',
            columns: 3,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 1210,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {

                    //Si el combo envía como valor todos se setea en null.
                    if ('Todos' === Ext.getCmp('cboEstados').getValue())
                    {
                        Ext.getCmp('cboEstados').value = null;
                        Ext.getCmp('cboEstados').setRawValue(null);
                    }
                    //Contrae el panel
                    objFilterPanel.toggleCollapse(true);
                    storeParametrosCab.currentPage = 1; // Establece la página en 1 antes de la consulta.
                    //Realiza la petición con los campos seteados en el panel de busqueda
                    storeParametrosCab.load({
                        params:
                            {
                                strUsrCreacion: Ext.getCmp('txtUsrCreacion').getValue(),
                                strDescripcion: Ext.getCmp('txtDescripcion').getValue(),
                                strNombreParametro: Ext.getCmp('txtNombreParametro').getValue(),
                                strModulo: Ext.getCmp('txtModulo').getValue(),
                                strProceso: Ext.getCmp('txtProceso').getValue(),
                                strEstado: Ext.getCmp('cboEstados').getValue()
                            }
                    });
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    Ext.getCmp('txtUsrCreacion').setValue('');
                    Ext.getCmp('txtDescripcion').setValue('');
                    Ext.getCmp('txtNombreParametro').setValue('');
                    Ext.getCmp('txtModulo').setValue('');
                    Ext.getCmp('txtProceso').setValue('');
                    Ext.getCmp('cboEstados').value = null;
                    Ext.getCmp('cboEstados').setRawValue(null);
                }
            }

        ],
        items: [
            txtNombreParametro,
            txtDescripcion,
            txtModulo,
            txtProceso,
            txtUsrCreacion,
            cboEstados
        ],
        renderTo: 'filtroParametros'
    });

    //Crea el plugin para la edición de las filas del grid: gridListaParametros
    rowEditingCab = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {
            beforeedit: function(editor, context) {
                var permiso = $("#ROLE_300-5");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                //Si el registro se encuentra en estado Eliminado o la persona no tiene asignado el permiso, no podrá editar el regitro.
                if ('Eliminado' === context.record.getData().strEstado || !boolPermiso) {
                    return false;
                }
                return true;
            },
            afteredit: function(roweditor, changes, record, rowIndex) {
                var recStore = Ext.getCmp('gridListaParametros').getStore().getAt(changes.rowIdx);
                //Valida que no hayan campos nulos en la fila para enviar el request con los campos a actualizar.
                if ('' !== changes.newValues.strNombreParametro.trim() && '' !== changes.newValues.strDescripcion.trim())
                {
                    //Valida que se haya realizado algun cambio en la fila para enviar el request con los campos a actualizar.
                    if (changes.originalValues.strNombreParametro.trim() !== changes.newValues.strNombreParametro.trim() ||
                        (changes.originalValues.strDescripcion.trim() !== changes.newValues.strDescripcion.trim() ||
                            changes.originalValues.strModulo.trim() !== changes.newValues.strModulo.trim() ||
                            changes.originalValues.strProceso.trim() !== changes.newValues.strProceso.trim()))
                    {
                        boolPermiteIngresar = true;
                        if (false === validateString(changes.newValues.strNombreParametro.trim()))
                        {
                            boolPermiteIngresar = false;
                        }
                        if (false === validateStringLetSpace(changes.newValues.strDescripcion.trim()))
                        {
                            Ext.Msg.alert('Alerta!', 'No puede ingresar un caracter especial al inicio de la cadena.');
                            boolPermiteIngresar = false;
                        }
                        if ('' !== changes.newValues.strModulo.trim())
                        {
                            if (false === validateStringLetSpace(changes.newValues.strModulo.trim()))
                            {
                                Ext.Msg.alert('Alerta!', 'No puede ingresar un caracter especial al inicio de la cadena.');
                                boolPermiteIngresar = false;
                            }
                        }
                        if ('' !== changes.newValues.strProceso.trim())
                        {
                            if (false === validateStringLetSpace(changes.newValues.strProceso.trim()))
                            {
                                Ext.Msg.alert('Alerta!', 'No puede ingresar un caracter especial al inicio de la cadena.');
                                boolPermiteIngresar = false;
                            }
                        }
                        if (false === boolPermiteIngresar)
                        {
                            recStore.set('strNombreParametro', changes.originalValues.strNombreParametro);
                            recStore.set('strDescripcion', changes.originalValues.strDescripcion);
                            recStore.set('strModulo', changes.originalValues.strModulo);
                            recStore.set('strProceso', changes.originalValues.strProceso);
                            Ext.Msg.alert('Alerta!', 'No puede ingresar un caracter especial ni espacios' +
                                ' al inicio de la cadena.');
                        }
                        else
                        {
                            /**La strActualizaSoloDescripcion variable define si se actualiza solo el campo descripcion de la fila o no.
                             * Cuando la variable strActualizaSoloDescripcion es enviada en request con 'NO' se validará del lado del controlador 
                             * AdmiParametroCabController con el metodo validaParametroCab que no éxsita parametro en la base con los mismos datos 
                             * nuevos a ingresar.
                             */
                            var strActualizaSoloDescripcion = 'NO';
                            if (changes.originalValues.strDescripcion.trim() !== changes.newValues.strDescripcion.trim() &&
                                changes.originalValues.strNombreParametro.trim() === changes.newValues.strNombreParametro.trim() &&
                                (changes.originalValues.strModulo.trim() === changes.newValues.strModulo.trim() ||
                                    changes.originalValues.strProceso.trim() === changes.newValues.strProceso.trim()))
                            {
                                /**
                                 * Cuando la variable strActualizaSoloDescripcion es enviada en request con 'SI' 
                                 * no se validará del lado del controlador que ya éxista el parametro.
                                 */
                                strActualizaSoloDescripcion = 'SI';
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
                                url: urlActualizaParametroCab,
                                method: 'POST',
                                timeout: 60000,
                                params: {
                                    intIdParametro: changes.originalValues.intIdParametro,
                                    strNombreParametro: changes.newValues.strNombreParametro.trim(),
                                    strDescripcion: changes.newValues.strDescripcion.trim(),
                                    strModulo: changes.newValues.strModulo.trim(),
                                    strProceso: changes.newValues.strProceso.trim(),
                                    strActualizaSoloDescripcion: strActualizaSoloDescripcion
                                },
                                success: function(response) {
                                    var text = Ext.decode(response.responseText);
                                    //Cuando el strStatus enviado en el Response es != 100 el registro vuelve a sus valores originales.
                                    if ("100" !== text.strStatus) {
                                        recStore.set('strNombreParametro', changes.originalValues.strNombreParametro.trim());
                                        recStore.set('strDescripcion', changes.originalValues.strDescripcion.trim());
                                        recStore.set('strModulo', changes.originalValues.strModulo.trim());
                                        recStore.set('strProceso', changes.originalValues.strProceso.trim());
                                    }
                                    Ext.Msg.alert(arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                },
                                failure: function(result) {
                                    //Por alguna excepcion no controlada el registro vuelve a sus valores originales
                                    recStore.set('strNombreParametro', changes.originalValues.strNombreParametro.trim());
                                    recStore.set('strDescripcion', changes.originalValues.strDescripcion.trim());
                                    recStore.set('strModulo', changes.originalValues.strModulo.trim());
                                    recStore.set('strProceso', changes.originalValues.strProceso.trim());
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText.trim());
                                }
                            });
                        }
                    }
                    else
                    {
                        Ext.Msg.alert('Información', 'No hay cambios en el registro.');
                    }
                }//Validacion de Campos
                else {
                    Ext.Msg.alert('Alerta!', 'No puede insertar el regitro vacio, Debe ingresar un nombre de parametro y almenos proceso o modulo.');
                    recStore.set('strNombreParametro', changes.originalValues.strNombreParametro);
                    recStore.set('strDescripcion', changes.originalValues.strDescripcion);
                    recStore.set('strModulo', changes.originalValues.strModulo);
                    recStore.set('strProceso', changes.originalValues.strProceso);
                }
            }
        }
    });

    var permiso = $("#ROLE_300-3");
    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    //La variable boolCreaParametroCab permite habilitar o inhabilitar el boton para la creación de la cabera de parámetros
    var boolCreaParametroCab = false;

    //Si no tiene los permisos se bloquea el boton para crear los parámetros
    if (!boolPermiso)
    {
        boolCreaParametroCab = true;
    }

    //Define el boton para crear la cabecera de parámetros usado en el toolbar toolbarCab
    var btnCrearParametroCab = Ext.create('Ext.button.Button', {
        text: 'Crea Parametro',
        scope: this,
        disabled: boolCreaParametroCab,
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
                        if (intCountGridDetalle > 0)
                        {

                            //Valida que no se agregue a otra fila si la que se está editando actualmente tiene los campos vacios
                            if ('' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(0).data.strDescripcion.trim() ||
                                ('' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(0).data.strValor1.trim() &&
                                    '' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(0).data.strValor2.trim() &&
                                    '' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(0).data.strValor3.trim() &&
                                    '' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(0).data.strValor4.trim()))
                            {
                                Ext.Msg.alert('Error', 'Debes ingresar la descripcion y al menos un valor para agregar un nuevo parametro.');
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
                                Ext.getCmp('gridCreaParametrosDet').getStore().getAt(i).get('strValor2') === changes.newValues.strValor2 &&
                                Ext.getCmp('gridCreaParametrosDet').getStore().getAt(i).get('strValor3') === changes.newValues.strValor3 &&
                                Ext.getCmp('gridCreaParametrosDet').getStore().getAt(i).get('strValor4') === changes.newValues.strValor4)
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
                    {name: 'strValor2', type: 'string'},
                    {name: 'strValor3', type: 'string'},
                    {name: 'strValor4', type: 'string'}
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
                text: 'Agregar Parametro',
                width: 160,
                iconCls: 'button-grid-crearSolicitud-without-border',
                handler: function() {

                    rowEditingCreaParamDet.cancelEdit();

                    //Crea una nueva fila en el grid gridCreaParametrosDet con el model definido CrearParametrosDetModel
                    var recordParamDet = Ext.create('CrearParametrosDetModel', {
                        strDescripcion: '',
                        strValor1: '',
                        strValor2: '',
                        strValor3: '',
                        strValor4: ''
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
                                '' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(1).data.strValor2.trim() &&
                                '' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(1).data.strValor3.trim() &&
                                '' === Ext.getCmp('gridCreaParametrosDet').getStore().getAt(1).data.strValor4.trim()))
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
                        xtype: 'textfield',
                        fieldLabel: 'Nombre',
                        id: 'strNombreParametroCab',
                        vlue: '',
                        textAlign: 'left',
                        width: 250,
                        regex: /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Módulo',
                        id: 'strModuloCab',
                        value: '',
                        textAlign: 'left',
                        width: 250,
                        regex: /^([A-Za-z]+[A-Za-z0-9\s\_\-]*)$/
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Proceso',
                        id: 'strProcesoCab',
                        value: '',
                        textAlign: 'left',
                        width: 250,
                        regex: /^([A-Za-z]+[A-Za-z0-9\s\_\-]*)$/
                    },
                    {
                        colspan: 3,
                        xtype: 'textfield',
                        fieldLabel: 'Descripción',
                        id: 'strDescripcionCab',
                        value: '',
                        textAlign: 'left',
                        width: 770,
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
                                width: 247,
                                editor: {
                                    allowBlank: false,
                                    regex: /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/
                                }
                            },
                            {
                                header: "Valor 1",
                                dataIndex: 'strValor1',
                                width: 130,
                                editor: 'textfield'
                            },
                            {
                                header: "Valor 2",
                                dataIndex: 'strValor2',
                                width: 130,
                                editor: 'textfield'
                            },
                            {
                                header: "Valor 3",
                                dataIndex: 'strValor3',
                                width: 130,
                                editor: 'textfield'
                            },
                            {
                                header: "Valor 4",
                                dataIndex: 'strValor4',
                                width: 130,
                                editor: 'textfield'
                            }
                        ]
                    }
                ],
                buttonAlign: 'center',
                buttons: [
                    {
                        text: 'Guardar Parametro',
                        name: 'btnGuardar',
                        id: 'idBtnGuardar',
                        disabled: false,
                        handler: function() {

                            //Valida que al menos el nombre y la descripción de los parámetros no sea nula
                            if ('' !== Ext.getCmp('strNombreParametroCab').getValue().trim() &&
                                '' !== Ext.getCmp('strDescripcionCab').getValue().trim())
                            {
                                boolPermiteIngresar = true;
                                //Valida que los campos no empiecen con caracteres especiales
                                if (false === validateString(Ext.getCmp('strNombreParametroCab').getValue().trim()) ||
                                    false === validateStringLetSpace(Ext.getCmp('strDescripcionCab').getValue().trim()))
                                {
                                    boolPermiteIngresar = false;
                                }
                                //Valida que el campo no empiece con caracter especial
                                if ('' !== Ext.getCmp('strModuloCab').getValue().trim())
                                {
                                    if (false === validateStringLetSpace(Ext.getCmp('strModuloCab').getValue().trim()))
                                    {
                                        boolPermiteIngresar = false;
                                    }
                                }
                                //Valida que el campo no empiece con caracter especial
                                if ('' !== Ext.getCmp('strProcesoCab').getValue().trim())
                                {
                                    if (false === validateStringLetSpace(Ext.getCmp('strProcesoCab').getValue().trim()))
                                    {
                                        boolPermiteIngresar = false;
                                    }
                                }
                                if (false === boolPermiteIngresar)
                                {
                                    Ext.Msg.alert('Alerta!', 'No puede ingresar un caracter especial al inicio de la cadena.');
                                }
                                else
                                {
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
                                                '' === arrayGridCreaParametrosDet.getStore().getAt(0).data.strValor2.trim() &&
                                                '' === arrayGridCreaParametrosDet.getStore().getAt(0).data.strValor3.trim() &&
                                                '' === arrayGridCreaParametrosDet.getStore().getAt(0).data.strValor4.trim())
                                        {
                                            boolPermite = false;
                                            Ext.Msg.alert('Alerta!', 'Debe ingresar un nombre y descripción para la cabecera del parametro.');
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
                                    if (true === boolPermite)
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
                                            url: urlCreaParametroCab,
                                            method: 'POST',
                                            timeout: 60000,
                                            params: {
                                                jsonCreaParametrosDet: jsonCreaParametrosDet,
                                                strNombreParametroCab: Ext.getCmp('strNombreParametroCab').getValue().trim(),
                                                strDescripcionCab: Ext.getCmp('strDescripcionCab').getValue().trim(),
                                                strModuloCab: Ext.getCmp('strModuloCab').getValue().trim(),
                                                strProcesoCab: Ext.getCmp('strProcesoCab').getValue().trim()
                                            },
                                            success: function(response) {
                                                var text = Ext.decode(response.responseText);
                                                Ext.Msg.alert(arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                                //Valida que el estatus de respuesta sea 100 para destruir la ventana y resetear el formulario.
                                                if ("100" === text.strStatus) {
                                                    formCreaParametrosCab.getForm().reset();
                                                    formCreaParametrosCab.destroy();
                                                    windowsCreaParametrosCab.destroy();
                                                    storeParametrosCab.loadPage(1);// Carga desde la página 1
                                                }
                                                Ext.Msg.alert(arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                            },
                                            failure: function(result) {
                                                Ext.Msg.alert('Error ', result.statusText);
                                            }
                                        });
                                    }
                                }
                            }//Validacion de Campos
                            else {
                                Ext.Msg.alert('Alerta!', 'Debe ingresar un nombre y descripción para la cabecera del parametro.');
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
                title: 'Crear parametro',
                height: 380,
                width: 812,
                modal: true,
                resizable: false,
                items: [panelCreaParametrosCab]
            }).show();
        }
    });

    //Crea el toolbar usado en el grid gridListaParametros
    toolbarCab = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [{xtype: 'tbfill'},
                btnCrearParametroCab
            ]
    });

    //Crea el grid que muestra la información obtenida desde el controlador  de la cabera de parámetros.
    gridListaParametros = Ext.create('Ext.grid.Panel', {
        title: 'Listado de Parametros',
        store: storeParametrosCab,
        plugins: [rowEditingCab],
        dockedItems: [toolbarCab],
        id: 'gridListaParametros',
        columns: [
            {header: "ID", dataIndex: 'intIdParametro', hidden: true},
            {
                header: 'Nombre Parametro',
                dataIndex: 'strNombreParametro',
                width: 250,
                editor: {
                    regex: /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/
                }
            },
            {
                header: 'Descripción',
                dataIndex: 'strDescripcion',
                width: 302,
                editor: {
                    regex: /^([A-Za-z]+[A-Za-z0-9\s\_\-]*)$/
                }
            },
            {
                header: 'Módulo',
                dataIndex: 'strModulo',
                width: 100,
                editor: {
                    regex: /^([A-Za-z]+[A-Za-z0-9\s\_\-]*)$/
                }
            },
            {
                header: 'Proceso',
                dataIndex: 'strProceso',
                width: 200,
                editor: {
                    regex: /^([A-Za-z]+[A-Za-z0-9\s\_\-]*)$/
                }
            },
            {header: 'Estado', dataIndex: 'strEstado', width: 80},
            {header: 'Usr. Creación', dataIndex: 'strUsrCreacion', width: 90},
            {header: 'Fe. Creación', dataIndex: 'strFeCreacion', width: 80},
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 105,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-show';
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var recStore = storeParametrosCab.getAt(rowIndex);
                            windowsParametrosDet = '';

                            //Formulario estático que solo muestra la información de la cabecera de parametros.
                            formGetParametrosCab = Ext.create('Ext.form.Panel', {
                                height: 170,
                                width: '100%',
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
                                        colspan: 3,
                                        xtype: 'displayfield',
                                        fieldLabel: 'Nombre Parametro',
                                        name: 'strNombreParametro',
                                        labelStyle: 'font-weight:bold;',
                                        value: recStore.get('strNombreParametro'),
                                        textAlign: 'left'
                                    },
                                    {
                                        xtype: 'displayfield',
                                        fieldLabel: 'Módulo',
                                        name: 'strModulo',
                                        labelStyle: 'font-weight:bold;',
                                        value: recStore.get('strModulo'),
                                        textAlign: 'left'
                                    },
                                    {
                                        xtype: 'displayfield',
                                        fieldLabel: 'Proceso',
                                        name: 'strProceso',
                                        labelStyle: 'font-weight:bold;',
                                        value: recStore.get('strProceso'),
                                        textAlign: 'left'
                                    },
                                    {
                                        xtype: 'displayfield',
                                        fieldLabel: 'Estado',
                                        name: 'strEstado',
                                        labelStyle: 'font-weight:bold;',
                                        value: recStore.get('strEstado'),
                                        textAlign: 'left'
                                    },
                                    {
                                        xtype: 'displayfield',
                                        fieldLabel: 'Usuario Creación',
                                        name: 'strUsrCreacion',
                                        labelStyle: 'font-weight:bold;',
                                        value: recStore.get('strUsrCreacion'),
                                        textAlign: 'left'
                                    },
                                    {
                                        colspan: 3,
                                        xtype: 'displayfield',
                                        fieldLabel: 'Fecha Creación',
                                        name: 'strFeCreacion',
                                        labelStyle: 'font-weight:bold;',
                                        value: recStore.get('strFeCreacion'),
                                        textAlign: 'left'
                                    },
                                    {
                                        colspan: 3,
                                        xtype: 'displayfield',
                                        fieldLabel: 'Descripción:',
                                        name: 'strDescripcion',
                                        labelStyle: 'font-weight:bold;',
                                        value: recStore.get('strDescripcion'),
                                        textAlign: 'left'
                                    }
                                ]
                            });

                            //Define un modelo para el store storeParametrosDet
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

                            //Crea el plugin para la edición de filas del grid gridParametrosDet
                            rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
                                clicksToMoveEditor: 1,
                                autoCancel: false,
                                listeners: {
                                    beforeedit: function(editor, context) {
                                        var permiso = $("#ROLE_300-5");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                        //Deshabilita la edición de la fila cuando estado es Eliminado o no tiene los permisos
                                        if ('Eliminado' === context.record.getData().strEstado || !boolPermiso) {
                                            return false;
                                        }
                                        return true;
                                    },
                                    afteredit: function(roweditor, changes, record, rowIndex) {

                                        //Valida que los nuevos campos a ingresar no sean nulos.
                                        if ('' !== changes.newValues.strDescripcionDet.trim() &&
                                            ('' !== changes.newValues.strValor1.trim() || '' !== changes.newValues.strValor2.trim() ||
                                                '' !== changes.newValues.strValor3.trim() || '' !== changes.newValues.strValor4.trim()))
                                        {
                                            //Valida que los campos nuevos sean distintos a los de la base para hacer la petición ajax al controlador
                                            if (changes.originalValues.strDescripcionDet.trim() !== changes.newValues.strDescripcionDet.trim() ||
                                                (changes.originalValues.strValor1.trim() !== changes.newValues.strValor1.trim() ||
                                                    changes.originalValues.strValor2.trim() !== changes.newValues.strValor2.trim() ||
                                                    changes.originalValues.strValor3.trim() !== changes.newValues.strValor3.trim() ||
                                                    changes.originalValues.strValor4.trim() !== changes.newValues.strValor4.trim()))
                                            {
                                                /**La strActualizaSoloDescripcion variable define si se actualiza solo el campo 
                                                 * descripcion de la fila o no. Cuando la variable strActualizaSoloDescripcion es enviada 
                                                 * en request con 'NO' se validará del lado del controlador AdmiParametroCabController con el metodo 
                                                 * validaParametroCab que no éxsita parametro en la base con los mismos datos nuevos a ingresar.
                                                 */
                                                var strActualizaSoloDescripcion = 'NO';
                                                if (changes.originalValues.strDescripcionDet.trim() !== changes.newValues.strDescripcionDet.trim() &&
                                                    (changes.originalValues.strValor1.trim() === changes.newValues.strValor1.trim() &&
                                                        changes.originalValues.strValor2.trim() === changes.newValues.strValor2.trim() &&
                                                        changes.originalValues.strValor3.trim() === changes.newValues.strValor3.trim() &&
                                                        changes.originalValues.strValor4.trim() === changes.newValues.strValor4.trim()))
                                                {
                                                    /**
                                                     * Cuando la variable strActualizaSoloDescripcion es enviada 
                                                     * en request con 'SI' no se validará del lado del controlador que ya éxista el parametro.
                                                     */
                                                    strActualizaSoloDescripcion = 'SI';
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
                                                        strValor3: changes.newValues.strValor3.trim(),
                                                        strValor4: changes.newValues.strValor4.trim(),
                                                        strActualizaSoloDescripcion: strActualizaSoloDescripcion
                                                    },
                                                    success: function(response) {
                                                        var text = Ext.decode(response.responseText);
                                                        var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                                                        /*Cuando el strStatus enviado en el Response es != 100 el registro vuelve a sus 
                                                         * valores originales.
                                                         */
                                                        if ("100" !== text.strStatus) {
                                                            recStore.set('strDescripcionDet', changes.originalValues.strDescripcionDet);
                                                            recStore.set('strValor1', changes.originalValues.strValor1);
                                                            recStore.set('strValor2', changes.originalValues.strValor2);
                                                            recStore.set('strValor3', changes.originalValues.strValor3);
                                                            recStore.set('strValor4', changes.originalValues.strValor4);
                                                        }
                                                        Ext.Msg.alert(arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                                    },
                                                    failure: function(result) {
                                                        //Por alguna excepcion no controlada el registro vuelve a sus valores originales
                                                        var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                                                        recStore.set('strDescripcionDet', changes.originalValues.strDescripcionDet);
                                                        recStore.set('strValor1', changes.originalValues.strValor1);
                                                        recStore.set('strValor2', changes.originalValues.strValor2);
                                                        recStore.set('strValor3', changes.originalValues.strValor3);
                                                        recStore.set('strValor4', changes.originalValues.strValor4);
                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                    }
                                                });
                                            } else
                                            {
                                                Ext.Msg.alert('Información', 'No hay cambios en el registro.');
                                            }
                                        }//Validacion de Campos
                                        else {
                                            Ext.Msg.alert('Alerta!',
                                                'No puede insertar el regitro vacio, Debe ingresar una descripcion y almenos un valor.');
                                        }
                                    }
                                }
                            });
                            var permiso = $("#ROLE_300-3");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                            //La variable boolCreaParametro permite habilitar o inhabilitar el boton para la creación de detalle de parámetros
                            var boolCreaParametro = true;

                            //Si el registro es diferente de Eliminado y tiene los permisos podrá crear un detalle de parámetro
                            if ('Eliminado' !== recStore.get('strEstado') && boolPermiso)
                            {
                                boolCreaParametro = false;
                            }

                            //Define boton para crear un detalle de parámetro usado en el toolbar: toolbar
                            var btnCrearParametro = Ext.create('Ext.button.Button', {
                                text: 'Crea Parametro',
                                disabled: boolCreaParametro,
                                scope: this,
                                handler: function() {

                                    //Si el registro es diferente de Eliminado y tiene los permisos podrá crear un detalle de parámetro
                                    if ('Eliminado' !== recStore.get('strEstado') && boolPermiso)
                                    {
                                        windowsCreaParametrosDet = '';

                                        //Crea el formulario para la creación de un detalle de parámetro. Usado en el panel panelCreaParametrosDet
                                        formCreaParametrosDet = Ext.create('Ext.form.Panel', {
                                            height: 145,
                                            width: 560,
                                            bodyPadding: 10,
                                            layout: {
                                                tdAttrs: {style: 'padding: 5px;'},
                                                type: 'table',
                                                columns: 2,
                                                pack: 'center'
                                            },
                                            items: [
                                                {
                                                    colspan: 2,
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Descripción',
                                                    id: 'strCreaDescripcion',
                                                    vlue: '',
                                                    textAlign: 'left',
                                                    width: 515,
                                                    regex: /^([A-Za-z]+[A-Za-z0-9\s\_\-]*)$/
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Valor 1',
                                                    id: 'strCreaValor1',
                                                    value: '',
                                                    textAlign: 'left',
                                                    width: 250
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Valor 2',
                                                    id: 'strCreaValor2',
                                                    value: '',
                                                    textAlign: 'left',
                                                    width: 250
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Valor 3',
                                                    id: 'strCreaValor3',
                                                    value: '',
                                                    textAlign: 'left',
                                                    width: 250
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Valor 4',
                                                    id: 'strCreaValor4',
                                                    value: '',
                                                    textAlign: 'left',
                                                    width: 250
                                                }
                                            ],
                                            buttonAlign: 'center',
                                            buttons: [
                                                {
                                                    text: 'Guardar Parametro',
                                                    name: 'btnGuardar',
                                                    id: 'idBtnGuardar',
                                                    disabled: boolCreaParametro,
                                                    handler: function() {

                                                        /**Valida que los datos nuevos a ingresar desde el formulario formCreaParametrosDet 
                                                         * no sean nulos
                                                         */
                                                        if ('' !== Ext.getCmp('strCreaDescripcion').getValue().trim() &&
                                                            ('' !== Ext.getCmp('strCreaValor1').getValue().trim() || ''
                                                                !== Ext.getCmp('strCreaValor2').getValue().trim() ||
                                                                '' !== Ext.getCmp('strCreaValor3').getValue().trim() ||
                                                                '' !== Ext.getCmp('strCreaValor4').getValue().trim()))
                                                        {
                                                            boolPermiteIngresar = true;
                                                            if (false === validateStringLetSpace(Ext.getCmp('strCreaDescripcion').getValue().trim()))
                                                            {
                                                                boolPermiteIngresar = false;
                                                            }
                                                            if (false === boolPermiteIngresar)
                                                            {
                                                                Ext.Msg.alert('Error ',
                                                                    'El campo descripción no puede contenet caracteres especiales.');
                                                            }
                                                            else
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
                                                                    url: urlCreaParametroDet,
                                                                    method: 'POST',
                                                                    timeout: 60000,
                                                                    params: {
                                                                        intParametroCab: recStore.get('intIdParametro'),
                                                                        strDescripcion: Ext.getCmp('strCreaDescripcion').getValue().trim(),
                                                                        strValor1: Ext.getCmp('strCreaValor1').getValue().trim(),
                                                                        strValor2: Ext.getCmp('strCreaValor2').getValue().trim(),
                                                                        strValor3: Ext.getCmp('strCreaValor3').getValue().trim(),
                                                                        strValor4: Ext.getCmp('strCreaValor4').getValue().trim()
                                                                    },
                                                                    success: function(response) {
                                                                        var text = Ext.decode(response.responseText);
                                                                        if ("100" === text.strStatus) {
                                                                            formCreaParametrosDet.getForm().reset();
                                                                            formCreaParametrosDet.destroy();
                                                                            windowsCreaParametrosDet.destroy();
                                                                            storeParametrosDet.load();
                                                                        }
                                                                        Ext.Msg.alert(arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                                                    },
                                                                    failure: function(result) {
                                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                                    }
                                                                });
                                                            }
                                                        }//Validacion de Campos
                                                        else {
                                                            Ext.Msg.alert('Alerta!',
                                                                'No puede insertar el regitro vacio, ' +
                                                                'Debe ingresar una descripcion y almenos un valor.');
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

                                        //Panel usuado para la ventana de creación de detalle de parámetros windowsCreaParametrosDet
                                        panelCreaParametrosDet = new Ext.Panel({
                                            width: '100%',
                                            height: '100%',
                                            items: [
                                                formCreaParametrosDet
                                            ]
                                        });

                                        //Ventana usada para la creación de detalle de parámetros windowsCreaParametrosDet
                                        windowsCreaParametrosDet = Ext.widget('window', {
                                            title: 'Crear parametro para: [' + recStore.get('strNombreParametro') + ']',
                                            height: 180,
                                            width: 562,
                                            modal: true,
                                            resizable: false,
                                            items: [panelCreaParametrosDet]
                                        }).show();
                                    }
                                }
                            });

                            //Crea el toolbar usado en el grid gridParametrosDet con el boton para la creación del parámetro.
                            toolbar = Ext.create('Ext.toolbar.Toolbar', {
                                dock: 'top',
                                align: '->',
                                items:
                                    [{xtype: 'tbfill'},
                                        btnCrearParametro
                                    ]
                            });

                            //Crea un campo para el panel de busqueda objFilterPanelDet
                            txtDescripcionDet = Ext.create('Ext.form.Text',
                                {
                                    id: 'txtDescripcionDet',
                                    name: 'txtDescripcionDet',
                                    fieldLabel: 'Descripción',
                                    labelAlign: 'left',
                                    allowBlank: true,
                                    width: 325
                                });

                            //Crea un campo para el panel de busqueda objFilterPanelDet
                            txtValor1 = Ext.create('Ext.form.Text',
                                {
                                    id: 'txtValor1',
                                    name: 'txtValor1',
                                    fieldLabel: 'Valor 1',
                                    labelAlign: 'left',
                                    allowBlank: true,
                                    width: 325
                                });

                            //Crea un campo para el panel de busqueda objFilterPanelDet
                            txtValor2 = Ext.create('Ext.form.Text',
                                {
                                    id: 'txtValor2',
                                    name: 'txtValor2',
                                    fieldLabel: 'Valor 2',
                                    labelAlign: 'left',
                                    allowBlank: true,
                                    width: 325
                                });

                            //Crea un campo para el panel de busqueda objFilterPanelDet
                            txtValor3 = Ext.create('Ext.form.Text',
                                {
                                    id: 'txtValor3',
                                    name: 'txtValor3',
                                    fieldLabel: 'Valor 3',
                                    labelAlign: 'left',
                                    allowBlank: true,
                                    width: 325
                                });

                            //Crea un campo para el panel de busqueda objFilterPanelDet
                            txtValor4 = Ext.create('Ext.form.Text',
                                {
                                    id: 'txtValor4',
                                    name: 'txtValor4',
                                    fieldLabel: 'Valor 4',
                                    labelAlign: 'left',
                                    allowBlank: true,
                                    width: 325
                                });

                            //Crea un campo para el panel de busqueda objFilterPanelDet
                            txtUsrCreacionDet = Ext.create('Ext.form.Text',
                                {
                                    id: 'txtUsrCreacionDet',
                                    name: 'txtUsrCreacionDet',
                                    fieldLabel: 'Usuario Creación',
                                    labelAlign: 'left',
                                    allowBlank: true,
                                    width: 325
                                });

                            //Crea el combo box usado para el panel de busqueda objFilterPanelDet
                            cboEstadosDetalle = Ext.create('Ext.form.ComboBox', {
                                fieldLabel: 'Estados',
                                id: 'cboEstadosDetalle',
                                name: 'cboEstadosDetalle',
                                store: storeEstados,
                                queryMode: 'local',
                                displayField: 'strDisplayField',
                                valueField: 'strValueField',
                                editable: false
                            });

                            // Se movió el DataStore para que el refresh del toolbar de paginación envíe los filtros de búsqueda.
                            // Crea un store para obtener el detalle de los parámetros con la referencia de la cabecera de parámetros.
                            storeParametrosDet = Ext.create('Ext.data.Store', {
                                pageSize: 4,
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
                                },
                                listeners: {
                                    beforeload: function(storeDocumentos) {
                                        storeDocumentos.getProxy().extraParams.intIdParametroCab = recStore.get('intIdParametro');
                                        // Se agregan filtros de búsqueda
                                        storeDocumentos.getProxy().extraParams.strDescripcionDet = Ext.getCmp('txtDescripcionDet').getValue();
                                        storeDocumentos.getProxy().extraParams.strValor1         = Ext.getCmp('txtValor1').getValue();
                                        storeDocumentos.getProxy().extraParams.strValor2         = Ext.getCmp('txtValor2').getValue();
                                        storeDocumentos.getProxy().extraParams.strValor3         = Ext.getCmp('txtValor3').getValue();
                                        storeDocumentos.getProxy().extraParams.strValor4         = Ext.getCmp('txtValor4').getValue();
                                        storeDocumentos.getProxy().extraParams.strUsrCreacion    = Ext.getCmp('txtUsrCreacionDet').getValue();
                                    },
                                    load: function(store) {
                                        if (store.getProxy().getReader().rawData.strMensajeError !== '') {
                                            Ext.Msg.alert('Error', store.getProxy().getReader().rawData.strMensajeError);
                                        }
                                    }
                                }
                            });

                            storeParametrosDet.load();
                            
                            //Crea el panel de busqueda usado en panelParametrosDet para el grid gridParametrosDet
                            objFilterPanelDet = new Ext.Panel({
                                collapsible: true,
                                collapsed: true,
                                width: '100%',
                                border: false,
                                buttonAlign: 'center',
                                layout: {
                                    tdAttrs: {style: 'padding: 10px;'},
                                    type: 'table',
                                    columns: 3,
                                    align: 'left'
                                },
                                bodyStyle: {
                                    background: '#fff'
                                },
                                title: 'Criterios de Busqueda',
                                items:
                                    [
                                        txtDescripcionDet,
                                        txtValor1,
                                        txtValor2,
                                        txtValor3,
                                        txtValor4,
                                        txtUsrCreacionDet,
                                        cboEstadosDetalle
                                    ],
                                buttons: [
                                    {
                                        text: 'Buscar',
                                        iconCls: "icon_search",
                                        handler: function() {

                                            //Si el combo envía como valor todos se setea en null.
                                            if ('Todos' === Ext.getCmp('cboEstadosDetalle').getValue())
                                            {
                                                Ext.getCmp('cboEstadosDetalle').value = null;
                                                Ext.getCmp('cboEstadosDetalle').setRawValue(null);
                                            }

                                            objFilterPanelDet.toggleCollapse(true);
                                            storeParametrosDet.currentPage = 1;
                                            storeParametrosDet.load({
                                                params:
                                                    {
                                                        strDescripcionDet: Ext.getCmp('txtDescripcionDet').getValue(),
                                                        strValor1: Ext.getCmp('txtValor1').getValue(),
                                                        strValor2: Ext.getCmp('txtValor2').getValue(),
                                                        strValor3: Ext.getCmp('txtValor3').getValue(),
                                                        strValor4: Ext.getCmp('txtValor4').getValue(),
                                                        strUsrCreacion: Ext.getCmp('txtUsrCreacionDet').getValue(),
                                                        strEstado: Ext.getCmp('cboEstadosDetalle').getValue()
                                                    }
                                            });
                                        }
                                    },
                                    {
                                        text: 'Limpiar',
                                        iconCls: "icon_limpiar",
                                        handler: function() {
                                            Ext.getCmp('txtDescripcionDet').setValue('');
                                            Ext.getCmp('txtValor1').setValue('');
                                            Ext.getCmp('txtValor2').setValue('');
                                            Ext.getCmp('txtValor3').setValue('');
                                            Ext.getCmp('txtValor4').setValue('');
                                            Ext.getCmp('txtUsrCreacionDet').setValue('');
                                            Ext.getCmp('cboEstadosDetalle').value = null;
                                            Ext.getCmp('cboEstadosDetalle').setRawValue(null);
                                        }
                                    }
                                ]
                            });
                            
                            // Se movió el GridPanel para que el refresh del toolbar de paginación envíe los filtros de búsqueda.
                            // Crea el grid para mostrar el detalle de la cabecera de parámetros
                            gridParametrosDet = Ext.create('Ext.grid.Panel', {
                                title: 'Detalle de Parametros',
                                store: storeParametrosDet,
                                id: 'gridParametrosDet',
                                cls: 'custom-grid',
                                autoScroll: false,
                                plugins: [rowEditing],
                                dockedItems: [toolbar],
                                columns: [
                                    {header: "ID", dataIndex: 'intIdParametroDet', hidden: true},
                                    {
                                        header: 'Descripción ',
                                        dataIndex: 'strDescripcionDet',
                                        width: 288,
                                        editor: {
                                            allowBlank: false,
                                            maskRe: /([A-Za-z0-9\s\_\-]+)/i,
                                            regex: /^([A-Za-z0-9\s\_\-]+)$/
                                        }
                                    },
                                    {
                                        header: 'Valor 1',
                                        dataIndex: 'strValor1',
                                        width: 130,
                                        editor: 'textfield'
                                    },
                                    {
                                        header: 'Valor 2',
                                        dataIndex: 'strValor2',
                                        width: 130,
                                        editor: 'textfield'
                                    },
                                    {
                                        header: 'Valor 3',
                                        dataIndex: 'strValor3',
                                        width: 130,
                                        editor: 'textfield'
                                    },
                                    {
                                        header: 'Valor 4',
                                        dataIndex: 'strValor4',
                                        width: 130,
                                        editor: 'textfield'
                                    },
                                    {header: 'Estado', dataIndex: 'strEstado', width: 80},
                                    {header: 'Usr. Creación', dataIndex: 'strUsrCreacion', width: 75},
                                    {header: 'Fe. Creación', dataIndex: 'strFeCreacion', width: 80},
                                    {
                                        xtype: 'actioncolumn',
                                        header: 'Acciones',
                                        width: 100,
                                        items: [
                                            {
                                                getClass: function(v, meta, rec) {
                                                    var permiso = $("#ROLE_300-9");
                                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                                    var strBtnParametro = 'button-grid-invisible';
                                                    if (!boolPermiso) {
                                                        strBtnParametro = 'button-grid-invisible';
                                                    }
                                                    else {
                                                        if ('Eliminado' !== rec.data.strEstado) {
                                                            strBtnParametro = 'button-grid-delete';
                                                        }
                                                    }
                                                    return strBtnParametro;
                                                }, tooltip: 'Eliminar',
                                                handler: function(grid, rowIndex, colIndex) {
                                                    var recStore = grid.getStore().getAt(rowIndex);

                                                    Ext.MessageBox.confirm(
                                                        'Eliminar Parametro',
                                                        '¿Está seguro de eliminar el parametro ?',
                                                        function(btn) {
                                                            if (btn === 'yes') {

                                                                Ext.MessageBox.show({
                                                                    msg: 'Eliminando Parametro ...',
                                                                    title: 'Eliminando',
                                                                    progressText: 'Eliminando datos.',
                                                                    progress: true,
                                                                    closable: false,
                                                                    width: 300,
                                                                    wait: true,
                                                                    waitConfig: {interval: 200}
                                                                });

                                                                Ext.Ajax.request({
                                                                    url: urlEliminaParametroDet,
                                                                    method: 'POST',
                                                                    timeout: 60000,
                                                                    params: {
                                                                        intIdParametroDet: recStore.get('intIdParametroDet')
                                                                    },
                                                                    success: function(response) {
                                                                        var text = Ext.decode(response.responseText);
                                                                        if ("100" === text.strStatus) {
                                                                            recStore.set('strEstado', 'Eliminado');
                                                                        }
                                                                        Ext.Msg.alert(arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                                                    },
                                                                    failure: function(result) {
                                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                                    }
                                                                });
                                                            }
                                                        });
                                                }
                                            }
                                        ]
                                    }
                                ],
                                height: 225,
                                width: 1145,
                                bbar: Ext.create('Ext.PagingToolbar', {
                                    store: storeParametrosDet,
                                    displayInfo: true,
                                    displayMsg: 'Mostrando {0} - {1} de {2}',
                                    emptyMsg: "No hay datos que mostrar."
                                })
                            });


                            /**Panel que contiene el formulario de presentación de la cabecera de parámetro, el panel de busqueda y el grid
                             * usado en la ventana windowsParametrosDet
                             */
                            panelParametrosDet = new Ext.Panel({
                                width: '100%',
                                height: '100%',
                                items: [
                                    formGetParametrosCab,
                                    objFilterPanelDet,
                                    gridParametrosDet
                                ]
                            });

                            //Venta que muestra el detalle de los parámetros.
                            windowsParametrosDet = Ext.widget('window', {
                                title: 'Información del Parametro: ' + recStore.get('strNombreParametro'),
                                height: 455,
                                width: 1160,
                                resizable: false,
                                layout: 'fit',
                                modal: true,
                                items: [panelParametrosDet]
                            }).show();
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_300-9");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            var strBtnParametro = 'button-grid-invisible';
                            if (!boolPermiso) {
                                strBtnParametro = 'button-grid-invisible';
                            }
                            else {
                                if ('Eliminado' !== rec.data.strEstado) {
                                    strBtnParametro = 'button-grid-delete';
                                }
                            }
                            return strBtnParametro;
                        },
                        tooltip: 'Eliminar',
                        handler: function(grid, rowIndex, colIndex) {
                            var recStore = grid.getStore().getAt(rowIndex);

                            Ext.MessageBox.confirm(
                                'Eliminar Parametro',
                                '¿Está seguro de eliminar el parametro ?',
                                function(btn) {
                                    if (btn === 'yes') {

                                        Ext.MessageBox.show({
                                            msg: 'Eliminando Parametro ...',
                                            title: 'Eliminando',
                                            progressText: 'Eliminando datos.',
                                            progress: true,
                                            closable: false,
                                            width: 300,
                                            wait: true,
                                            waitConfig: {interval: 200}
                                        });

                                        Ext.Ajax.request({
                                            url: urlEliminaParametroCab,
                                            method: 'POST',
                                            timeout: 60000,
                                            params: {
                                                intParametroCab: recStore.get('intIdParametro')
                                            },
                                            success: function(response) {
                                                var text = Ext.decode(response.responseText);
                                                //Si el estatus es 100 cambia el estado del registro a Eliminado
                                                if ("100" === text.strStatus) {
                                                    recStore.set('strEstado', 'Eliminado');
                                                }
                                                Ext.Msg.alert(arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                            },
                                            failure: function(result) {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                        }
                    }
                ]
            }
        ],
        height: 710,
        width: 1210,
        renderTo: 'ListadosCabeceraParametros',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeParametrosCab,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });
    
});

function validateString(strCampo) {
    var boolCorrecto = false;
    if (/^([A-Za-z]+[A-Za-z0-9\_\-]*)$/.test(strCampo))
    {
        boolCorrecto = true;
    }
    return boolCorrecto;
}
function validateStringLetSpace(strCampo) {
    var boolCorrecto = false;
    if (/^([A-Za-z]+[A-Za-z0-9\s\_\-]*)$/.test(strCampo))
    {
        boolCorrecto = true;
    }
    return boolCorrecto;
}
