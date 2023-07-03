
estaEnVentana = false;
Ext.onReady(function () {
    Ext.QuickTips.init();

    //Creación de store para las solicitudes de ajuste de tiempo.
    storeSolicitudes = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        autoLoad: true,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: getSolicitudesPuntoTipo,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'listado',
            },
            extraParams:
            {
                fecha_desde: '',
                fecha_hasta: ''
            }
        },
        fields:
            [
                { name: 'id', mapping: 'id' },
                { name: 'cliente', mapping: 'cliente' },
                { name: 'login', mapping: 'login' },
                { name: 'login_aux', mapping: 'login_aux' },
                { name: 'fe_creacion', mapping: 'fe_creacion' },
                { name: 'fe_ejecucion', mapping: 'fe_ejecucion' },
                { name: 'tarea_id', mapping: 'tarea_id' },
                { name: 'tiempo_cambio', mapping: 'minutos' },
                { name: 'motivo_cambio', mapping: 'observacion' },
                { name: 'estado', mapping: 'estado' },
                { name: 'id_detalle_solicitud', mapping: 'id_detalle_solicitud' },
                { name: 'servicio_id', mapping: 'servicio_id'},
                { name: 'tipo_solicitud_id', mapping: 'tipo_solicitud_id' },
                { name: 'motivo_id', mapping: 'motivo_id' },
                { name: 'usr_creacion', mapping: 'usr_creacion' },
                { name: 'detalle_proceso_id', mapping: 'detalle_proceso_id' },
                { name: 'nombre_producto', mapping: 'nombre_producto' }
            ]
    });

    //Creación de grid para las solicitudes de ajuste de tiempo
    gridSolicitudes = Ext.create('Ext.grid.Panel', {
        id: 'gridSolicitudes',
        width: 1000,
        height: 400,
        store: storeSolicitudes,
        viewConfig:
        {
            enableTextSelection: true,
            loadingText: '<b>Cargando solicitudes, Por favor espere...',
            emptyText: '<center><br/><b/>*** No se encontraron solicitudes ***',
            loadMask: true
        },
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'id',
                header: 'ID',
                dataIndex: 'id',
                hideable: false,
                hidden: true,
                width: 30
            },
            {
                id: 'cliente',
                header: 'Cliente',
                dataIndex: 'cliente',
                width: 120
            },
            {
                id: 'login',
                header: 'Login',
                dataIndex: 'login',
                width: 70
            },
            {
                id: 'login_aux',
                header: 'Login aux',
                dataIndex: 'login_aux',
                width: 80
            },
            {
                id: 'nombre_producto',
                header: 'Producto',
                dataIndex: 'nombre_producto',
                width: 180
            },
            {
                id: 'tarea_id',
                header: '#Tarea',
                dataIndex: 'tarea_id',
                width: 50
            },
            {
                id: 'fe_creacion',
                header: 'Fecha Solicitud',
                dataIndex: 'fe_creacion',
                width: 100
            },
            {
                id: 'tiempo_cambio',
                header: 'Tiempo del cambio',
                dataIndex: 'tiempo_cambio',
                width: 110
            },
            {
                id: 'motivo_cambio',
                header: 'Motivo del cambio',
                dataIndex: 'motivo_cambio',
                width: 110
            },
            {
                id: 'estado',
                header: 'Estado de solicitud',
                dataIndex: 'estado',
                width: 110
            },
            {
                id: 'acciones',
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 68,
                items: [
                    {
                       
                        tooltip: 'Autorizar',
                        
                        handler: function (grid, rowIndex, colIndex) {
                            aprobarSolicitud(grid, rowIndex);
                        },
                        getClass: function(v, meta, rec) {
                            if (rec.get('estado') == 'Aprobado' || rec.get('estado') == 'Rechazado')
                            {
                                return 'icon-invisible';
                            }
                            return 'button-grid-aprobar';
                        }
                    },
                    {
                        tooltip: 'Rechazar',
                        getClass: function(v, meta, rec) {
                            if (rec.get('estado') == 'Aprobado' || rec.get('estado') == 'Rechazado')
                            {
                                return 'icon-invisible';
                            }
                            return 'button-grid-quitarFacturacionElectronica';
                        },
                        handler: function (grid, rowIndex, colIndex) {
                            rechazarSolicitud(grid, rowIndex);
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeSolicitudes,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });


    //Creación de panel con parámetros de búsqueda
    Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 4
        },
        bodyStyle: {
            background: '#fff'
        },
        width: 1000,
        collapsible: true,
        collapsed: false,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function(){ buscar();}
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function(){ limpiar();}
            }],
        items:
            [
                {
                    xtype: 'datefield',
                    fieldLabel: 'Fecha desde',
                    id: 'dateFechaDesde',
                    name: 'dateFechaDesde',
                    displayField: 'nombre_fecha',
                    valueField: 'id_fecha_desde',
                    width: 250,
                    emptyText: ''
                },
                { width: 60, border: false },
                {
                    xtype: 'datefield',
                    fieldLabel: 'Fecha hasta',
                    id: 'dateFechaHasta',
                    name: 'dateFechaHasta',
                    displayField: 'nombre_fecha',
                    valueField: 'id_fecha_hasta',
                    width: 250,
                    emptyText: ''
                }
            ],
        renderTo: 'filtro'

    });


    function buscar(){
        gridSolicitudes.store.proxy.extraParams.fecha_desde = Ext.getCmp('dateFechaDesde').value;
        gridSolicitudes.store.proxy.extraParams.fecha_hasta = Ext.getCmp('dateFechaHasta').value;
        gridSolicitudes.store.reload();
    }

    function limpiar(){
        Ext.getCmp('dateFechaDesde').value = '';
        Ext.getCmp('dateFechaHasta').value = '';
        Ext.getCmp('dateFechaDesde').setRawValue("");
        Ext.getCmp('dateFechaHasta').setRawValue("");
        gridSolicitudes.store.proxy.extraParams.fecha_desde = '';
        gridSolicitudes.store.proxy.extraParams.fecha_hasta = '';
        gridSolicitudes.store.reload();
    }

    /**
     * Función que muestra ventana para aprobar la solicitud de ajuste de tiempo
     * de horas de soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    function aprobarSolicitud(grid, rowIndex) {

        estaEnVentana = true;

        bttonaceptar = Ext.create('Ext.Button', {
            text: 'Aceptar',
            cls: 'x-btn-right',
            handler: function () {
                estaEnVentana = false;
                
                let obs = Ext.getCmp('txtMotivo').value;

                Ext.Ajax.request({
                    url: putAprobarSolAjstTiempoSoporte,
                    method: 'post',
                    params: { 
                        id_detalle_solicitud: grid.store.data.items[rowIndex].raw.id_detalle_solicitud,
                        servicio_id: grid.store.data.items[rowIndex].raw.servicio_id,
                        tipo_solicitud_id: grid.store.data.items[rowIndex].raw.tipo_solicitud_id,
                        motivo_id: grid.store.data.items[rowIndex].raw.motivo_id,
                        estado: 'Aprobado',
                        observacion: (obs != '')?obs:grid.store.data.items[rowIndex].raw.observacion,
                        user_gestion: grid.store.data.items[rowIndex].raw.usr_creacion,
                        fe_rechazo: '',
                        detalle_proceso_id:grid.store.data.items[rowIndex].raw.detalle_proceso_id,
                        fe_ejecucion:new Date()
                    },
                    success: function (response) {
                        console.log( response.responseText);
                        Ext.Msg.alert('Éxito', '¡La solicitud de ajuste de tiempo de soporte se autorizó exitosamente!')
                        grid.store.load();
                    },
                    failure: function (result) {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });

                winAjstTiempo.destroy();
            }
        });

        bttoncancelar = Ext.create('Ext.Button', {
            text: 'Cancelar',
            cls: 'x-btn-right',
            handler: function () {
                estaEnVentana = false;
                winAjstTiempo.destroy();
            }
        });

        var panelModificacion = Ext.create('Ext.panel.Panel', {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 1
            },
            bodyStyle: {
                background: '#fff'
            },
            width: 400,
            items: [
                {
                    xtype: 'textareafield',
                    id: 'txtMotivo',
                    fieldLabel: 'Motivo',
                    width: 400

                }
            ]
        });

        formPanel = Ext.create('Ext.form.FormPanel', {
            width: 400,
            height: 150,
            BodyPadding: 10,
            layout: {
                type: 'table',
                columns: 1,
                align: 'center'
            },
            bodyStyle: {
                background: '#fff'
            },
            items: [
                panelModificacion
            ]

        });

        winAjstTiempo = Ext.create('Ext.window.Window', {
            title: 'Autorización de solicitud de modificación de horas',
            modal: true,
            width: 450,
            height: 150,
            resizable: false,
            layout: 'fit',
            items: [formPanel],
            buttonAlign: 'center',
            buttons: [bttonaceptar, bttoncancelar]
        }).show();
    }

    /**
     * Función que muestra ventana para rechazar la solicitud de ajuste de tiempo
     * de horas de soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    function rechazarSolicitud(grid, rowIndex) {

        estaEnVentana = true;

        bttonaceptar = Ext.create('Ext.Button', {
            text: 'Aceptar',
            cls: 'x-btn-right',
            handler: function () {
                estaEnVentana = false;

                let obs = Ext.getCmp('txtMotivo').value;
                Ext.Ajax.request({
                    url: putAprobarSolAjstTiempoSoporte,
                    method: 'post',
                    params: { 
                        id_detalle_solicitud: grid.store.data.items[rowIndex].raw.id_detalle_solicitud,
                        servicio_id: grid.store.data.items[rowIndex].raw.servicio_id,
                        tipo_solicitud_id: grid.store.data.items[rowIndex].raw.tipo_solicitud_id,
                        motivo_id: grid.store.data.items[rowIndex].raw.motivo_id,
                        estado: 'Rechazado',
                        observacion: (obs != '')?obs:grid.store.data.items[rowIndex].raw.observacion,
                        user_gestion: grid.store.data.items[rowIndex].raw.usr_creacion,
                        fe_rechazo: new Date(), 
                        detalle_proceso_id:grid.store.data.items[rowIndex].raw.detalle_proceso_id,
                        fe_ejecucion:new Date()
                    },
                    success: function (response) {
                        console.log( response.responseText);
                        Ext.Msg.alert('Éxito', '¡La solicitud de ajuste de tiempo de soporte se rechazó exitosamente!')
                        grid.store.load();
                    },
                    failure: function (result) {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });

                winAjstTiempo.destroy();
            }
        });

        bttoncancelar = Ext.create('Ext.Button', {
            text: 'Cancelar',
            cls: 'x-btn-right',
            handler: function () {
                estaEnVentana = false;
                winAjstTiempo.destroy();
            }
        });

        var panelModificacion = Ext.create('Ext.panel.Panel', {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 1
            },
            bodyStyle: {
                background: '#fff'
            },
            width: 400,
            items: [
                {
                    xtype: 'textareafield',
                    id: 'txtMotivo',
                    fieldLabel: 'Motivo',
                    width: 400

                }
            ]
        });

        formPanel = Ext.create('Ext.form.Panel', {
            width: 400,
            height: 150,
            BodyPadding: 10,
            layout: {
                type: 'table',
                columns: 1,
                align: 'center'
            },
            bodyStyle: {
                background: '#fff'
            },
            items: [
                panelModificacion
            ]

        });

        winAjstTiempo = Ext.create('Ext.window.Window', {
            title: 'Rechazo de solicitud de modificación de horas',
            modal: true,
            width: 450,
            height: 150,
            resizable: false,
            layout: 'fit',
            items: [formPanel],
            buttonAlign: 'center',
            buttons: [bttonaceptar, bttoncancelar]
        }).show();
    }

})