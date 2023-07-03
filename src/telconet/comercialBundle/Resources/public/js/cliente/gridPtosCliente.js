Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);



var itemsPerPage = 10;
var storePuntos = '';
var estado_id = '';
var area_id = '';
var login_id = '';
var tipo_asignacion = '';
var pto_sucursal = '';
var idClienteSucursalSesion;
var tipoNegocioActual = null;


/**
 * Documentación para el método 'cambioTipoNegocio'.
 *
 * Envia mediante post el punto y el tipo de negocio al controlado que realiza la 
 * actualizacion.
 * 
 * @param integer    idPunto Obtiene el IdPunto del cliente
 * @param string     eX      Obtiene el typo de envento.
 *
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 05-06-2014
 */
/*Incio: Cambio de Tipo de Negocio*/
function cambioTipoNegocio(idPunto, eX) {

    //Variable que se valida conta el event.type enviado en eX
    var xValid = false;

    //validad la variable xValid vs. event.type, retorna true.
    eval(function(p, a, c, k, e, d) {
        e = function(c) {
            return c
        };
        if (!''.replace(/^/, String)) {
            while (c--) {
                d[c] = k[c] || c
            }
            k = [function(e) {
                    return d[e]
                }];
            e = function() {
                return'\\w+'
            };
            c = 1
        }
        ;
        while (c--) {
            if (k[c]) {
                p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c])
            }
        }
        return p
    }('2(1==\'3\'){0=4}5{0=6}', 7, 7, 'xValid|eX|if|click|true|else|false'.split('|'), 0, {}))


    /*valida segun lo devuelto en la validacion anterior.
     * si el evento no es un click mostrara un mensaje 
     * que indicara que no tiene permiso.
     */
    if (!xValid) {
        Ext.Msg.alert('Alert', 'No tiene permisos');
    } else {
        //caso contrario levantara la ventana que muestra el combo de los tipos negocios.
        winCambioTipoNegocio = "";
        //valida si la ventana no ha sido levantada.
        if (!winCambioTipoNegocio) {

            /*Incio: Combo Tipos de Negocios*/
            Ext.define('modelTipoNegocio', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idTipoNegocio', type: 'string'},
                    {name: 'descripcion', type: 'string'}
                ]
            });
            var estado_store = Ext.create('Ext.data.Store', {
                autoLoad: false,
                model: "modelTipoNegocio",
                proxy: {
                    type: 'ajax',
                    url: url_lista_tiposNegocios,
                    reader: {
                        type: 'json',
                        root: 'tiposNegocio'
                    }
                }
            });
            var tipoNegocio_cmb = new Ext.form.ComboBox({
                id: 'idTipoNegocio',
                name: 'idTipoNegocio',
                fieldLabel: 'Tipo de Negocio',
                emptyText: '',
                store: estado_store,
                displayField: 'descripcion',
                valueField: 'idTipoNegocio',
                height: 30,
                width: 325,
                border: 0,
                margin: 0,
                queryMode: "remote",
                emptyText: ''
            });
            /*Fin: Combo Tipos de Negocios*/
            var formTipoNegocio = Ext.widget('form', {
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                border: false,
                bodyPadding: 10,
                fieldDefaults: {
                    //labelAlign: 'top',
                    labelWidth: 130,
                    labelStyle: 'font-weight:bold'
                },
                defaults: {
                    margins: '0 0 10 0'
                },
                items: [
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Tipo Negocio Actual',
                        id: 'tipoNegocioActual',
                        name: 'tipoNegocioActual'
                    },
                    tipoNegocio_cmb
                ],
                buttons: [{
                        text: 'Grabar',
                        name: 'grabar',
                        handler: function() {
                            /* valida que la variable idTipoNegocio y idPunto no sean nulas. 
                             * Por verdadero procedera a enviar los parametros al contralador que se encarga de atualizar
                             * el tipo de negocio.
                             */
                            if (Ext.getCmp('idTipoNegocio').value != null && idPunto != null) {
                                Ext.Ajax.request({
                                    url: url_cambiaTipoNegocio,
                                    method: 'post',
                                    params: {idTipoNegocio: Ext.getCmp('idTipoNegocio').value, idPunto: idPunto},
                                    success: function(response) {
                                        var text = Ext.decode(response.responseText);
                                        /* Valida si la variable succes que es devuelta por
                                         * el controlador tiene como valor true o false
                                         * Si es true presenta el mensaje enviado desde el controlador en la variable msg.
                                         */
                                        if (text.succes == true) {
                                            Ext.Msg.alert('Success', text.msg);
                                        } else {
                                            /* Caso contrario devuelve el mensaje
                                             * de error devuelta en la variable msg
                                             */
                                            Ext.Msg.alert('Alert', 'No se Realizo el cambio de Tipo de Negocio - ' + text.msg);
                                        }
                                    },
                                    failure: function(result) {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                                this.up('window').destroy();
                                winCambioTipoNegocio.close();
                            } else {
                                /*Si el idTipoNegocio es null mostrara el siguiente mensaje*/
                                Ext.Msg.alert('Alert', 'Debe seleccionar un Tipo de Negocio.');
                            }
                        }
                    },
                    {
                        text: 'Cancel',
                        handler: function() {
                            this.up('form').getForm().reset();
                            this.up('window').destroy();
                        }
                    }]
            });

            Ext.Ajax.request({
                url: url_tipoNegocioActual,
                method: 'post',
                params: {idPunto: idPunto},
                success: function(response) {
                    var text = Ext.decode(response.responseText);
                    tipoNegocioActual = text.tipoNegocioActual;
                    formTipoNegocio.getForm().findField('tipoNegocioActual').setValue(text.tipoNegocioActual);
                },
                failure: function(result) {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });

            winCambioTipoNegocio = Ext.widget('window', {
                title: 'Cambio de Tipo de Negocio',
                closeAction: 'hide',
                closable: false,
                width: 350,
                height: 170,
                minHeight: 150,
                layout: 'fit',
                resizable: true,
                modal: true,
                items: formTipoNegocio
            });

        }
        winCambioTipoNegocio.show();
    }
}
/*Fin: Cambio de Tipo de Negocio*/

function eliminarCliente(id) {
    //alert (id);
    Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function(btn) {
        if (btn == 'yes') {
            Ext.Ajax.request({
                url: url_cliente_delete_ajax,
                params: {param: id},
                method: 'get',
                success: function(response) {
                    var text = response.responseText;
                    Ext.Msg.alert(text);
                    store.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}


Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    // Campos para la ventana de busqueda
    DTFechaDesde = new Ext.form.DateField({
        id: 'txtFechaDesde',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'txtFechaHasta',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
   
    TFLogin = new Ext.form.TextField({
        id: 'txtLogin',
        fieldLabel: 'Login',
        xtype: 'textfield',
        width: 325
    });    
    TFNombrePunto = new Ext.form.TextField({
        id: 'txtNombrePunto',
        fieldLabel: 'Nombre Punto',
        xtype: 'textfield',
        width: 315
    });    
    TFDireccion = new Ext.form.TextField({
        id: 'txtDireccion',
        fieldLabel: 'Direccion Punto',
        xtype: 'textfield',
        width: 315
    });
    
    Ext.define('modelEstado', 
    {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'estado_punto', type: 'string'}            
        ]
    });
    var estado_store = Ext.create('Ext.data.Store', 
    {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url: url_puntos_lista_estados,
            reader: {
                type: 'json',
                root: 'encontrados'
            }
        }
    });
    var TFEstadoPunto = new Ext.form.ComboBox(
    {
        xtype: 'combobox',
        store: estado_store,
        labelAlign: 'left',
        id: 'estado_punto',
        name: 'estado_punto',
        valueField: 'estado_punto',
        displayField: 'estado_punto',
        fieldLabel: 'Estado',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true,
        listeners: {
            select:
                function(e) {                 
                    estado_id = Ext.getCmp('estado_punto').getValue();
                },
            click: {
                element: 'el', 
                fn: function() {
                    estado_id = '';
                    estado_store.removeAll();
                    estado_store.load();
                }
            }
        }
    });

    // Filtros de Busqueda
    var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  
                border:false,                
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 4,
                    align: 'left',
                },
                bodyStyle: {
                            background: '#fff'
                        },                     
                collapsible : true,
                collapsed: true,
                width: 950,
                title: 'Criterios de busqueda',
                
                    buttons: [
                        
                        {
                            text: 'Buscar',                            
                            iconCls: "icon_search",
                            handler: Buscar
                        },                                              
                        {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: function() {
                            Limpiar();}
                        }
                        ],                

                        items: [
                                DTFechaDesde,
                                {html:"&nbsp;",border:false,width:50},
                                DTFechaHasta,
                                {html:"&nbsp;",border:false,width:605},                                
                                TFLogin,
                                {html:"&nbsp;",border:false,width:50},                                   
                                TFNombrePunto,
                                {html:"&nbsp;",border:false,width:50},                                   
                                TFDireccion,
                                {html:"&nbsp;",border:false,width:50},  
                                TFEstadoPunto,
                                {html:"&nbsp;", border:false, width:50},
                                ],		
                renderTo: 'filtro_ptos'
    });
    
        
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'idPto', type: 'int'},
            {name: 'cliente', type: 'string'},
            {name: 'login', type: 'string'},
            {name: 'nombrePunto', type: 'string'},
            {name: 'direccion', type: 'string'},
            {name: 'descripcionPunto', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'linkVer', type: 'string'},
            {name: 'linkEditar', type: 'string'},
            {name: 'linkEliminar', type: 'string'},
            {name: 'permiteAnularPunto', type: 'string'}
        ]
    });


    storePuntos = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: url_gridPtos,
            reader: {
                type: 'json',
                root: 'ptos',
                totalProperty: 'total'
            },
            extraParams: {txtFechaDesde: '', txtFechaHasta: '', txtLogin: '', txtNombrePunto: '', txtDireccion: '', estado_punto: ''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.txtFechaDesde  = Ext.getCmp('txtFechaDesde').getValue();
                store.getProxy().extraParams.txtFechaHasta  = Ext.getCmp('txtFechaHasta').getValue();
                store.getProxy().extraParams.txtLogin       = Ext.getCmp('txtLogin').getValue();
                store.getProxy().extraParams.txtNombrePunto = Ext.getCmp('txtNombrePunto').getValue();
                store.getProxy().extraParams.txtDireccion   = Ext.getCmp('txtDireccion').getValue();
                store.getProxy().extraParams.estado_punto   = Ext.getCmp('estado_punto').getValue();
            },
            load: function(store) {
                store.each(function(record) {                    
                });
            }
        }
    });

    storePuntos.load({params: {start: 0, limit: 10}});



    var sm = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function(selectionModel, selected, options) {
                arregloSeleccionados = new Array();
                Ext.each(selected, function(record) {                   
                });                

            }
        }
    });





    function eliminarAlgunos() {
        var param = '';
        if (sm.getSelection().length > 0)
        {
            var estado = 0;
            for (var i = 0; i < sm.getSelection().length; ++i)
            {
                param = param + sm.getSelection()[i].data.idPersona;

                if (sm.getSelection()[i].data.estado == 'Eliminado')
                {
                    estado = estado + 1;
                }
                if (i < (sm.getSelection().length - 1))
                {
                    param = param + '|';
                }
            }
            if (estado == 0)
            {
                Ext.Msg.confirm('Alerta', 'Se eliminaran los registros. Desea continuar?', function(btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            url: url_cliente_delete_ajax,
                            method: 'post',
                            params: {param: param},
                            success: function(response) {
                                var text = response.responseText;
                                storePuntos.load();
                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                });

            }
            else
            {
                alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
            }
        }
        else
        {
            alert('Seleccione por lo menos un registro de la lista');
        }
    }





    var listView = Ext.create('Ext.grid.Panel', {
        width: 950,
        height: 275,
        collapsible: false,
        title: 'Listado de Puntos',
        //selModel: sm,
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [                    
                    {xtype: 'tbfill'}
                ]}],
        renderTo: Ext.get('lista_ptos'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storePuntos,
            displayInfo: true,
            displayMsg: 'Mostrando clientes {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: storePuntos,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        },
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Cliente',
                width: 150,
                dataIndex: 'cliente'
            }, {
                text: 'Login',
                width: 125,
                dataIndex: 'login'
            }, {
                text: 'Nombre Punto',
                dataIndex: 'nombrePunto',
                align: 'right',
                width: 100                            
            }, {
                text: 'Direccion',
                dataIndex: 'direccion',
                align: 'right',
                width: 200
            }, {
                text: 'Referencia',
                dataIndex: 'descripcionPunto',
                align: 'right',
                width: 140
            }, {
                text: 'Estado',
                dataIndex: 'estado',
                align: 'right',
                width: 70
            }, {
                text: 'Acciones',
                width: 150,
                renderer: renderAcciones
            }

        ]
    });

    
    /**
     * Documentación para el método 'renderAcciones'.
     * 
     * Contiene los botones de la columna acciones
     * 
     * @param object  record   contiene el store de la data obtenida de url_gridPtos
     * @param integer value    contiene la posicion de la columna
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 05-06-2014
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 29-07-2017
     * Se organizan los botones de acciones y se muestran dinámicamente.
     */
    function renderAcciones(value, p, record) 
    {
        var arrayBotones = [];
        var permiso = $("#ROLE_9-6");
        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
        if ( prefijoEmpresa == 'MD')
        {
            if (boolPermiso) 
            {
                arrayBotones.push('<a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a>');     
            }
        }else
        {
            arrayBotones.push('<a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a>');
        }              
        if (verHistorial == 1) 
        {
            arrayBotones.push('<a href="#" onClick="verHistorialPunto(' + record.data.idPto + 
                              ')" title="Ver Historial" class="button-grid-logs2"></a>');
        }

        //valida si el prefijo de la empresa es TTCO => Transtelco
        if (prefijoEmpresa == 'TTCO') 
        {
            //llama a la funcion verDebitos
            arrayBotones.push('<a href="#"  onClick="verDebitos(' + record.data.idPto + 
                              ')" title="Ver Historial de Debitos" class="button-grid-debitos"></a>');
        }
        /*valida si el usuario puede realizar el cambio de tipo de negocio
         * y verifica si el punto es diferente de los estados (Cancelado | Cancel | Trasladado | Reubicado)
         * para mostrar la opcion de cambio de tipo de negocio
         */
        if (puedeCambiarTipoNegocio && record.data.estado != 'Trasladado' && record.data.estado != 'Cancel' 
            && record.data.estado != 'Cancelado' && record.data.estado != 'Reubicado') 
        {
            //llama a la funcion cambioTipoNegocio
            arrayBotones.push('<a href="#" onClick="cambioTipoNegocio(' + record.data.idPto + 
                              ', event.type)" title="Cambio Tipo de Negocio" class="button-grid-cruzar2"></a>');
        }
        //se agrega boton que permite anular el punto segun sea el caso
        var permiso = $("#ROLE_13-1779");
        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
        if (boolPermiso) 
        {
            if (record.data.permiteAnularPunto == 'si')
            {
                //llama a la funcion anularPunto
                arrayBotones.push('<a href="#" onClick="anularPunto(' + record.data.idPto + ')" title="Anular Punto" ' +
                                  'class="button-grid-BigDelete"></a>');
            }
        }
        
        //Boton que permite ver el metraje de un cliente, accion 3257 verMetraje
        var boolPermisoMetraje = $("#ROLE_13-3257");
        var boolPermisoMetraje = (typeof boolPermisoMetraje === 'undefined') ? false : (boolPermisoMetraje.val() == 1 ? true : false);
        if (boolPermisoMetraje) 
        {
            arrayBotones.push('<a href="#" onClick="verMetraje(' + record.data.idPto + ')" title="Ruta Instalación" ' + 
                              'class="button-grid-RutaMaps"></a>');
        }
        
        var acciones = '<table><tr height="30px"><td>';
        var idx      = 1;
        
        Ext.Array.each(arrayBotones, function(rec)
        {
            acciones += rec;
            // máximo 4 botones por fila.
            if (idx % 4 === 0)
            {
                acciones += '</td></tr><tr height="30px"><td>'; // Divisor de Línea.
            }

            idx++;
        });
        
        acciones += '</td></tr></table>'; // Divisor de Línea.
        return acciones;
    }


    function Buscar() 
    {
        var boolError = false;
        if ((Ext.getCmp('txtFechaDesde').getValue() != null) && (Ext.getCmp('txtFechaHasta').getValue() != null))
        {
            if (Ext.getCmp('txtFechaDesde').getValue() > Ext.getCmp('txtFechaHasta').getValue())
            {
                boolError = true;
                Ext.Msg.show({
                    title: 'Error en Busqueda',
                    msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });

            }
        }
        else
        {
            if ((Ext.getCmp('txtFechaDesde').getValue() == null) && (Ext.getCmp('txtFechaHasta').getValue() != null)
                || (Ext.getCmp('txtFechaDesde').getValue() != null) && (Ext.getCmp('txtFechaHasta').getValue() == null))
            {
                Ext.Msg.show({
                    title: 'Error en Busqueda',
                    msg: 'Por Favor Ingrese criterios de fecha correctamente.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });
            }
        }

        if (!boolError)
        {
            storePuntos.load({params: {start: 0, limit: 10}});
        }
    }

    function Limpiar()
    {

        Ext.getCmp('txtFechaDesde').setValue('');
        Ext.getCmp('txtFechaHasta').setValue('');
        Ext.getCmp('txtLogin').setValue('');
        Ext.getCmp('txtNombrePunto').setValue('');
        Ext.getCmp('txtDireccion').setValue('');
        Ext.getCmp('estado_punto').setValue('');
        storePuntos.load({params: {start: 0, limit: 10}});
    }


});

function verMetraje(intIdPunto) {

    var arrayTituloMensajeBox = [];
    arrayTituloMensajeBox['100'] = 'Información';
    arrayTituloMensajeBox['001'] = 'Error';
    arrayTituloMensajeBox['000'] = 'Alerta';


    Ext.Ajax.request({
        url: urlRutaGeorreferencial,
        method: 'POST',
        timeout: 60000,
        params: {
            intIdPunto: intIdPunto
        },
        success: function(response) {
            var objResponse = Ext.decode(response.responseText);
            if ("100" === objResponse.strStatus) {
                //form usado como item en la ventana panelShowDataMap
                formShowDataMap = Ext.create('Ext.form.Panel', {
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 3,
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'displayfield',
                            fieldLabel: 'Fibra usada',
                            name: 'strFibraManual',
                            labelStyle: 'font-weight:bold;',
                            value: objResponse.strFibraManual,
                            textAlign: 'left'
                        },
                        {
                            xtype: 'displayfield',
                            fieldLabel: 'Distancia recorrida',
                            name: 'strDistancia',
                            labelStyle: 'font-weight:bold;',
                            value: objResponse.strDistancia,
                            textAlign: 'left'
                        },
                        {
                            xtype: 'displayfield',
                            fieldLabel: 'Usuario Creacion',
                            name: 'strUsrCreacion',
                            labelStyle: 'font-weight:bold;',
                            value: objResponse.strUsrCreacion,
                            textAlign: 'left'
                        },
                        {
                            colspan: 3,
                            html: "<div id='divMapMetraje' style='width:575px; height:450px'></div>"
                        }
                    ],
                    buttonAlign: 'center',
                    buttons: [
                        {
                            text: 'Cerrar',
                            handler: function() {
                                this.up('form').getForm().reset();
                                this.up('window').destroy();
                            }
                        }]
                });

                //Panel usado como item en la ventana windowsShowDataMap
                panelShowDataMap = new Ext.Panel({
                    width: '100%',
                    height: '100%',
                    items: [
                        formShowDataMap
                    ]
                });

                //Ventana que contiene el panel panelShowDataMap para la creacion de cabecera de parámetros y sus detalles.
                windowsShowDataMap = Ext.widget('window', {
                    title: 'Ver metraje',
                    layout: 'fit',
                    resizable: false,
                    modal: true,
                    items: [panelShowDataMap]
                }).show();

                objPuntoGPS = Ext.decode(objResponse.jsonPuntosGPS);
                createMap(objResponse.strLat, objResponse.strLng, 5, 'divMapMetraje', objPuntoGPS, 19);
            }
            else
            {
                Ext.Msg.alert(arrayTituloMensajeBox[objResponse.strStatus], objResponse.strMensaje);
            }
        },
        failure: function(result) {
            Ext.Msg.alert('Error ', result.statusText);
        }
    });

}

function createMap(strLatitud, strLongitud, intZoomMapProp, strDivMap, objCoordenadas, intZoomMap) {

    //Crea las propiedades del mapa.
    objMapProp = {
        center: new google.maps.LatLng(strLatitud, strLongitud),
        zoom: intZoomMapProp,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    //Crea el mapa en un div con sus propiedades.
    objMap = new google.maps.Map(document.getElementById(strDivMap), objMapProp);

    //Crea un objeto de informacion para el mapa.
    objInfowindow = new google.maps.InfoWindow({
        content: ""
    });

    //Objeto que dibuja las rutas en el mapa.
    objRuta = new google.maps.Polyline({
        path: objCoordenadas,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });

    //Setea la ruta dibujada en el mapa.
    objRuta.setMap(objMap);

    //Recorre las coordenadas para agregarle informacion a los marker's.
    objCoordenadas.forEach(function(item) {

        var strLatLng = new google.maps.LatLng(item.lat, item.lng);
        //Crea un marker
        var objMarker = new google.maps.Marker({
            position: strLatLng,
            map: objMap
        });
        //Crea una ventana de informacion para el marker.
        bindInfoWindow(objMarker, objMap, objInfowindow, item.strDescripcionMarker);
    });
    //Setea el zoom al mapa.
    objMap.setZoom(intZoomMap);
}

function bindInfoWindow(objMarker, objMap, objInfowindow, strDescripcionMarker) {
    //Agrega al envento click en el contenido del mapa la informacion enviada.
    objMarker.addListener('click', function() {
        objInfowindow.setContent(strDescripcionMarker);
        objInfowindow.open(objMap, this);
    });
}

function verDebitos(data) {
    var storeHistorial = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_historial_debitos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'pagos'
            },
            extraParams: {
                idPunto: data
            }
        },
        fields:
            [
                {name: 'usuarioCreacion', mapping: 'usuarioCreacion'},
                {name: 'fechaCreacion', mapping: 'fechaCreacion'},
                {name: 'total', mapping: 'total'},
                {name: 'estado', mapping: 'estado'},
                {name: 'observacionRechazo', mapping: 'observacionRechazo'},
                {name: 'banco', mapping: 'banco'},
                {name: 'fechaProceso', mapping: 'fechaProceso'}
            ]
    });

    Ext.define('HistorialServicio', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'usuarioCreacion', mapping: 'usuarioCreacion'},
            {name: 'fechaCreacion', mapping: 'fechaCreacion'},
            {name: 'total', mapping: 'total'},
            {name: 'estado', mapping: 'estado'},
            {name: 'observacionRechazo', mapping: 'observacionRechazo'},
            {name: 'banco', mapping: 'banco'},
            {name: 'fechaProceso', mapping: 'fechaProceso'}
        ]
    });

    //grid de usuarios
    gridHistorialDebitos = Ext.create('Ext.grid.Panel', {
        id: 'gridHistorialServicio',
        store: storeHistorial,
        columnLines: true,
        columns: [
            {
                header: 'Fecha Proceso',
                dataIndex: 'fechaProceso',
                width: 100
            },
            {
                //id: 'nombreDetalle',
                header: 'Usuario Proceso',
                dataIndex: 'usuarioCreacion',
                width: 90,
                sortable: true
            },
            {
                header: 'Valor',
                dataIndex: 'total',
                width: 90
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 80
            },
            {
                header: 'Banco',
                dataIndex: 'banco',
                width: 190
            },
            {
                header: 'Motivo Rechazo',
                dataIndex: 'observacionRechazo',
                width: 235
            }],
        viewConfig: {
            stripeRows: true
        },
        frame: true,
        height: 200
            //title: 'Historial del Servicio'
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
//                checkboxToggle: true,
//                collapsed: true,
                defaults: {
                    width: 900
                },
                items: [
                    gridHistorialDebitos

                ]
            }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Historial de Debitos',
        modal: true,
        width: 950,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

//funcion que realiza la anulacion de un punto
function anularPunto(id) {
    Ext.Msg.confirm('Alerta', 'Se anulara el punto seleccionado. Desea continuar?', function(btn) {
        if (btn == 'yes') {
            Ext.Ajax.request({
                url: url_anula_punto_ajax,
                method: 'post',
                params: {idPunto: id},
                success: function(response) {
                    var text = response.responseText;
                    Ext.Msg.alert('Mensaje',text , function(btn) {
                         if (btn == 'ok') {
                             storePuntos.load({params: {start: 0, limit: 10}});                             
                         }
                    });
                    
                   
                },
                failure: function(result)
                {
                    if (result.statusText=='Forbidden')
                    {
                        Ext.Msg.alert('Error ', 'Error: No tiene credenciales para realizar esta accion.');
                    }
                    else
                    {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                    
                    storePuntos.load();
                }
            });
        }
    });
}