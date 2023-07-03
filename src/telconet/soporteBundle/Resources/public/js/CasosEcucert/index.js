/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
*
* Opción que se ejecuta al inicio y permite dibujar la tabla y el panel de filtros.
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 10-03-2019
*
* @author Néstor Naula <nnaulal@telconet.ec>
* @version 1.1 - Se agrega el Login del cliente
* @since 1.0
*
* @author Néstor Naula <nnaulal@telconet.ec>
* @version 1.2 04-05-2020 - Se agrega en el grid la fecha de incidencia 
*                           y el login adicional
* @since 1.1
*
*/
Ext.onReady(function() {  
    Ext.tip.QuickTipManager.init();
	// **************** INCIDENCIA ******************
    var storeEstadoIncidencia = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarEstadoIncidencia',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
			{name:'comboEstadoIncidencia', mapping:'estadoIncidencia'}
		],
       /* listeners: {
            beforeload: function(store, operation, options){
                autoLoad : true;
            }
        },*/
		autoLoad: false
    });
    
    var storeSubEstadoIncidencia = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarSubEstadoIncidencia',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
			{name:'comboSubEstadoIncidencia', mapping:'subEstadoIncidencia'}
		],
		autoLoad: false
    });

    var storePrioridad = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarPrioridadIncidencia',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
			{name:'combPrioridadIncidencia', mapping:'prioridadIncidencia'}
		],
		autoLoad: false
    });
    
    var storeEstadoGestion = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarEstadoGestion',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
			{name:'comboEstadoGestionIncidencia', mapping:'estadoGestionIncidencia'}
		],
		autoLoad: false
    });
    
    
    var storeCanton = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarCanton',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }		
        },
        fields:
		[
			{name:'comboCanton', mapping:'nombre'}
		],
		autoLoad: false
    });
    
    var storeCategoriaIncidencia = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarCategoriaIncidencia',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                mostrarTodas: 'S'
            }
        },
        fields:
		[
			{name:'comboCategoria', mapping:'categoria'}
		],
		autoLoad: false
    });
    
    var storeTipoEvento = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarTipoEvento',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
        },
        fields:
		[
			{name:'comboTipoEvento', mapping:'tipoEvento'}
		],
		autoLoad: false,
    });
    
    var storeLogin = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarLogin',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
		[
			{name:'id_cliente', mapping:'id_cliente'},
			{name:'cliente', mapping:'cliente'}
		],
		autoLoad: false
    });
    
    var storeEstadoNotificacion = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarEstadoNotificacion',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
			{name:'comboNotificarIncidencia', mapping:'estadoNotificacionIncidencia'}
		],
		autoLoad: false
    });

    var storeTipoCliente = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'buscarTipoCliente',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
			{name:'comboTipoCliente', mapping:'tipoCliente'}
		],
		autoLoad: false
    });

    var comboEstadoIncidencia = new Ext.form.ComboBox({
        id: 'cmb_estadoIncidencia',
        name: 'cmb_estadoIncidencia',
        fieldLabel: "Estado Incidencia",
        emptyText: 'Seleccione Estado de Incidencia',
        store: storeEstadoIncidencia,
        displayField: 'comboEstadoIncidencia',
        valueField: 'comboEstadoIncidencia',
        height:30,
		width: 375,
        border:0,
        marginTop:0,
		queryMode: "remote",
        editable: false
    });
    
    var comboSubEstadoIncidencia = new Ext.form.ComboBox({
        id: 'cmb_subEstadoInci',
        name: 'cmb_subEstadoInci',
        fieldLabel: "Sub Estado Incidencia",
        emptyText: 'Seleccione Sub Estado',
        store: storeSubEstadoIncidencia,
        displayField: 'comboSubEstadoIncidencia',
        valueField: 'comboSubEstadoIncidencia',
        height:30,
		width: 375,
        border:0,
        marginTop:0,
		queryMode: "remote",
        editable: false
    });
    
    var comboPrioridadIncidencia = new Ext.form.ComboBox({
        id: 'cmb_prioridadIncidencia',
        name: 'cmb_prioridadIncidencia',
        fieldLabel: "Estado Prioridad",
        emptyText: 'Seleccione Prioridad',
        store: storePrioridad,
        displayField: 'combPrioridadIncidencia',
        valueField: 'combPrioridadIncidencia',
        height:30,
		width: 375,
        border:0,
        marginTop:0,
		queryMode: "remote",
        editable: false
    });

    var comboEstadoGestionIncidencia = new Ext.form.ComboBox({
        id: 'cmb_estadoGestionIncidencia',
        name: 'cmb_estadoGestionIncidencia',
        fieldLabel: "Estado Gestión",
        emptyText: 'Seleccione Estado Gestión',
        store: storeEstadoGestion,
        displayField: 'comboEstadoGestionIncidencia',
        valueField: 'comboEstadoGestionIncidencia',
        height:30,
		width: 375,
        border:0,
        marginTop:0,
		queryMode: "remote",
        editable: false
    });
    
    var comboCanton = new Ext.form.ComboBox({
        id: 'cmb_canton',
        name: 'cmb_canton',
        fieldLabel: "Jurisdicción",
        emptyText: 'Seleccione la Jurisdicción',
        store: storeCanton,
        displayField: 'comboCanton',
        valueField: 'comboCanton',
        height:30,
		width: 375,
        border:0,
        marginTop:0,
		queryMode: "remote",
        editable: false
    });
    
    var comboCategoria = new Ext.form.ComboBox({
        id: 'cmb_categoria',
        name: 'cmb_categoria',
        fieldLabel: "Categoría",
        emptyText: 'Seleccione la Categoría',
        store: storeCategoriaIncidencia,
        displayField: 'comboCategoria',
        valueField: 'comboCategoria',
        height:30,
		width: 375,
        border:0,
        marginTop:0,
		queryMode: "remote",
        editable: false
    });
    
    var comboTipoEvento = new Ext.form.ComboBox({
        id: 'cmb_tipoEvento',
        name: 'cmb_tipoEvento',
        fieldLabel: "Tipo de Evento",
        emptyText: 'Seleccione el Tipo de Evento',
        store: storeTipoEvento,
        displayField: 'comboTipoEvento',
        valueField: 'comboTipoEvento',
        height:30,
		width: 375,
        border:0,
        marginTop:0,
		queryMode: "remote",
        editable: false
    });
    
    var comboLogin = new Ext.form.ComboBox({
        id: 'cmb_Login',
        name: 'cmb_Login',
        fieldLabel: "Login",
        emptyText: 'Seleccione el Login',
        store: storeLogin,
        displayField: 'cliente',
        valueField: 'id_cliente',
        height:30,
		width: 375,
        border:0,
        margin:0,
		queryMode: "remote"
    });
    
    var comboNotificarIncidencia = new Ext.form.ComboBox({
        id: 'cmb_notificarIncidencia',
        name: 'cmb_notificarIncidencia',
        fieldLabel: "Estado Notificación",
        emptyText: 'Seleccione Estado Notificación',
        store: storeEstadoNotificacion,
        displayField: 'comboNotificarIncidencia',
        valueField: 'comboNotificarIncidencia',
        height:30,
		width: 375,
        border:0,
        marginTop:0,
		queryMode: "remote",
        editable: false
    });

    var comboTipoCliente = new Ext.form.ComboBox({
        id: 'cmb_tipoCliente',
        name: 'cmb_tipoCliente',
        fieldLabel: "Tipo IP",
        emptyText: 'Seleccione Tipo de Cliente',
        store: storeTipoCliente,
        displayField: 'comboTipoCliente',
        valueField: 'comboTipoCliente',
        height:30,
		width: 375,
        border:0,
        marginTop:0,
		queryMode: "remote",
        editable: false
    });
    
    var store = new Ext.data.Store
    ({ 
        name: 'store',
        id: 'store',
        pageSize: 10,
        total: 'total',
        proxy: 
        {
            timeout: 9600000,
            type: 'ajax',
            url : 'buscarCasosEcucert',
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                feEmisionDesde: '',
                feEmisionHasta: '',
                numeroCaso:     '',
                estadoInci:     ''
            }
        },
        fields:
		[
            {name: 'id_incidencia',            	mapping: 'idIncidencia'},
            {name: 'ticket_No',                 mapping: 'noTicket'},
            {name: 'id_Empresa',                mapping: 'empresaId'},
            {name: 'idDetalle',     		    mapping: 'idDetalle'},
            {name: 'idComunicacion',     		mapping: 'idComunicacion'},
            {name: 'personaEmpresaRolId',       mapping: 'personaEmpresaRolId'},
            {name: 'loginCliente',              mapping: 'loginCliente'},
            {name: 'numero_caso',               mapping: 'numeroCaso'},
            {name: 'estado_caso',               mapping: 'estadoCaso'},
            {name: 'estado_incidente',          mapping: 'estadoIncidencia'},
            {name: 'prioridad',                 mapping: 'prioridad'},
            {name: 'categoria',                 mapping: 'categoria'},
            {name: 'subCategoria',              mapping: 'subCategoria'},
            {name: 'observacion',               mapping: 'observacion'},
            {name: 'feIncidente',               mapping: 'fechaIpReportada'},
            {name: 'duracionDias',              mapping: 'duracionDias'},
            {name: 'idDetalleIncidencia',       mapping: 'idDetalleIncidencia'},
            {name: 'seguimientoInterno',        mapping: 'seguimientoInterno'},
            {name: 'nombretarea',               mapping: 'nombretarea'},
            {name: 'casoId',                    mapping: 'casoId'},
            {name: 'fechaSol',                  mapping: 'fechaSol'},
            {name: 'horaSol',                   mapping: 'horaSol'},
            {name: 'idTarea',                   mapping: 'idTarea'},
            {name: 'estadoTarea',               mapping: 'estadoTarea'},
            {name: 'estadoNotificacion',        mapping: 'estadoNotificacion'},
            {name: 'idPunto',                   mapping: 'idPunto'},
            {name: 'ipAddress',                 mapping: 'ipAddress'},
            {name: 'estadoIncEcucert',          mapping: 'estadoIncEcucert'},
            {name: 'tipoEvento',                mapping: 'tipoEvento'},
            {name: 'subEstado',                 mapping: 'subEstado'},
            {name: 'ipwan',                     mapping: 'ipwan'},
            {name: 'esconder',                  mapping: 'esconder'},
            {name: 'nombreEmpresa',             mapping: 'nombreEmpresa'},
            {name: 'jurisdiccion',              mapping: 'jurisdiccion'},
            {name: 'ipcontroller',              mapping: 'ipDestino'},
            {name: 'puertocontroller',          mapping: 'puerto'},
            {name: 'tipoUsuario',               mapping: 'tipoUsuario'},
            {name: 'siguienteEstadoGestion',    mapping: 'siguienteEstadoGestion'},
            {name: 'loginAdicional',            mapping: 'loginAdicional'},
            {name: 'feTicket',                  mapping: 'feIncidencia'}
        ],
        autoLoad: true
    });   
    
    var pagingToolbar = Ext.create('Ext.PagingToolbar',
    {
        id:'pagingToolbar',
        name: 'pagingToolbar',
        store: store,
        displayInfo: true,
        displayMsg: 'Mostrando {0} - {1} de {2}',
        emptyMsg: "No hay datos que mostrar."
    });
       
    Ext.create('Ext.grid.Panel', 
    {
        id: 'grid',
        name: 'grid',
        width: 1050,
        height: 400,
        store: store,
        viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
        loadMask: true,
        frame: false,
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items:
                [
                    {xtype: 'tbfill'}
                ]
            }
        ],
        columns:
        [
            {
                id: 'id_incidencia',
                header: 'IdIncidencia',
                dataIndex: 'id_incidencia',
                hidden: true,
                hideable: false
            }, 
            {
                id: 'ticket_No',
                header: 'NoTicket',
                dataIndex: 'ticket_No',
                width: 90,
                sortable: true
            },
            {
                id       : 'nombreEmpresa',
                header   : 'Empresa',
                dataIndex: 'nombreEmpresa',
                width    : 100,
                sortable : true
            },
            {
                id: 'jurisdiccion',
                header: 'Jurisdicción',
                dataIndex: 'jurisdiccion',
                width: 100,
                sortable: true
            },
            {
                id: 'idDetalleIncidencia',
                header: 'IdDetalleIncidencia',
                dataIndex: 'idDetalleIncidencia',
                hidden: true,
                hideable: false
            },
            {
                id: 'idDetalle',
                header: 'idDetalle',
                dataIndex: 'id_tarea',
                hidden: true,
                hideable: false
            },
            {
                id: 'personaEmpresaRolId',
                header: 'IdPersonaEmpresaRol',
                dataIndex: 'personaEmpresaRolId',
                hidden: true,
                hideable: false
            },
            {
                id: 'tipoEvento',
                header: 'Tipo Evento',
                dataIndex: 'tipoEvento',
                width: 100,
                sortable: true
            }
            ,
            {
                id: 'ipAddress',
                header: 'Ip Address',
                dataIndex: 'ipAddress',
                width: 100,
                sortable: true
            },
            {
                id: 'ipwan',
                header: 'Ip WAN',
                dataIndex: 'ipwan',
                width: 100,
                sortable: true
            },
            {
                id: 'ipcontroller',
                header: 'Ip Destino',
                dataIndex: 'ipcontroller',
                width: 100,
                sortable: true
            },
            {
                id: 'puertocontroller',
                header: 'Puerto Destino',
                dataIndex: 'puertocontroller',
                width: 100,
                sortable: true
            },
            {
                id: 'tipoUsuario',
                header: 'Tipo IP',
                dataIndex: 'tipoUsuario',
                width: 100,
                sortable: true
            },
            {
                id: 'loginCliente',
                header: 'Punto',
                dataIndex: 'loginCliente',                      
                width: 130,
                sortable: true,
                renderer : function(value, p,record){
                    return '<a href="#" onclick="setPuntoSesionByLogin(\''+value+'\');">'+value+'</a>';
                }                
            },
            {
                id: 'loginAdicional',
                header: 'Punto Adicional',
                dataIndex: 'loginAdicional',                      
                width: 130,
                sortable: true               
            },
            {
                id: 'categoria',
                header: 'Categoría',
                dataIndex: 'categoria',
                width: 100
            },
            {
                id: 'subCategoria',
                header: 'SubCategoría',
                dataIndex: 'subCategoria',
                width: 100
            },
            {
                id: 'subEstado',
                header: 'Sub Estado',
                dataIndex: 'subEstado',
                width: 250,
                sortable: true
            },
            {
                id: 'numero_caso',
                header: 'Número Caso',
                dataIndex: 'numero_caso',
                width: 100,
                sortable: true
            },
            {
                id: 'idComunicacion',
                header: 'Número Tarea',
                dataIndex: 'idComunicacion',
                width: 100,
                sortable: true
            },
            {
                id: 'estado_caso',
                header: 'Estado <br/> del Caso',
                dataIndex: 'estado_caso',
                width: 90,
                sortable: true
            },
            {
                id: 'estadoTarea',
                header: 'Estado de <br/> la tarea',
                dataIndex: 'estadoTarea',
                width: 90,
                sortable: true
            },
            {
                id: 'estado_incidente',
                header: 'Estado Incidencia',
                dataIndex: 'estado_incidente',
                width: 90,
                sortable: true
            },
            {
                id: 'estadoIncEcucert',
                header: 'Estado Gestión',
                dataIndex: 'estadoIncEcucert',
                width: 90,
                sortable: true
            },
            {
                id: 'prioridad',
                header: 'Prioridad',
                dataIndex: 'prioridad',
                width: 90,
                sortable: true
            },
            {
                id: 'feTicket',
                header: 'Fecha Ticket',
                dataIndex: 'feTicket',
                width: 120,
                sortable: true
            },
            {
                id: 'feIncidente',
                header: 'Fecha Incidencia',
                dataIndex: 'feIncidente',
                width: 120,
                sortable: true
            },
            {
                id        : 'duracionDias',
                header    : 'Tiempo<br/>Transcurrido (días)',
                dataIndex : 'duracionDias',
                width     :  100,
                sortable  :  true,
                align     : 'center'
            },
            {
                id        : 'estadoNotificacion',
                header    : 'Estado<br/>Notificación',
                dataIndex : 'estadoNotificacion',
                width     :  100,
                sortable  :  true,
                align     : 'center'
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 300,
                sortable: false,
                items:
               [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-vulnerabilidad"
                            this.items[0].tooltip = 'Actualizar evento';
                            
                            if (rec.data.esconder == 1)
                            {
                                this.items[0].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            validarVulnerabilidad(rowIndex);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "icon-invisible"
                            this.items[1].tooltip = '';

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {                      
                            contenerIp(rowIndex);                   
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-verAsignado"
                            this.items[2].tooltip = 'Cambio de estado de gestión';

                            if (rec.data.estadoIncEcucert == "" || rec.data.estadoIncEcucert == null || rec.data.estadoIncEcucert=='Atendido')
                            {
                                this.items[2].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {                      
                            CambiarEstadoGestion(rowIndex);                   
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-agregarSeguimiento"
                            this.items[3].tooltip = 'Agregar Seguimiento';
                            
                            if (rec.data.idComunicacion == "" || rec.data.idComunicacion == null || rec.data.estadoTarea=='Finalizada')
                            {
                                this.items[3].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            agregarSeguimiento(rec.data.casoId, rec.data.nombretarea, rec.data.idDetalle,rec.data.seguimientoInterno,false,null);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-verSeguimientoTareasCaso"
                            this.items[4].tooltip = 'Ver Seguimiento';
                            
                            if (rec.data.idComunicacion == "" || rec.data.idComunicacion == null )
                            {
                                this.items[4].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            verSeguimientoTarea(rec.data.idDetalle);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-mail"
                            this.items[5].tooltip = 'Detalle de notificaciones de correo';
                            
                            if (rec.data.loginCliente == "" || rec.data.loginCliente == null )
                            {
                                this.items[5].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec                 = store.getAt(rowIndex);
                            if(rec.data.estadoNotificacion === "No Notificado")
                            {
                                notificarCliente(rowIndex);
                            }
                            else
                            {
                                revisarNotificaciones(rec.data.idDetalleIncidencia);
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-afectados"
                            this.items[6].tooltip = 'Reprocesar registro';

                            if (rec.data.estadoIncEcucert == "" || rec.data.estadoIncEcucert == null 
                                || rec.data.estadoIncEcucert=='Atendido')
                            {
                                this.items[6].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {                      
                            reprocesarClientePorIp(rowIndex);                   
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-agregarArchivoCaso"
                            this.items[7].tooltip = 'Cargar Archivo';
                            if (rec.data.idComunicacion == "" || rec.data.idComunicacion == null )
                            {
                                this.items[7].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {                      
                            subirMultipleAdjuntosTarea(rowIndex);                   
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-pdf"
                            this.items[8].tooltip = 'Ver Archivos';
                            if (rec.data.idComunicacion == "" || rec.data.idComunicacion == null )
                            {
                                this.items[8].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {                      
                            presentarDocumentosTareas(rowIndex);                   
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-cambioEstadoNotif"
                            this.items[9].tooltip = 'Registro de Correos';

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {                      
                            CambiarEstadoNotificado(rowIndex);                   
                        }
                    }
                ]
            }
        ],
        bbar: pagingToolbar,
        renderTo: 'grid',
        listeners: 
        {
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
            },
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                var header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;
                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });

                grid.tip.on('show', function()
                {
                    var timeout;
                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function()
                        {
                            grid.tip.hide();
                        }, 500);
                    });
                    grid.tip.getEl().on('mouseover', function()
                    {
                        window.clearTimeout(timeout);
                    });
                    Ext.get(view.el).on('mouseover', function()
                    {
                        window.clearTimeout(timeout);
                    });
                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function()
                        {
                            grid.tip.hide();
                        }, 500);
                    });
                });
            }
        }
    });
     
/* ******************************************* */
            /* FILTROS DE BUSQUEDA */
/* ******************************************* */
    Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders        
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 1050,
        title: 'Críterios de busqueda',
        buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function() {
                        buscarRegistroIncidenciasPorParametros();
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function() {
                        limpiarRegistroDeFiltro();
                    }
                }
            ],
        items:
            [
                {html: "&nbsp;", border: false, width: 50},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'noTicket',
                    name:'noTicket',
                    fieldLabel: 'No. Ticket',
                    value: '',
                    width: 375
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'numeroCaso',
                    name:'numeroCaso',
                    fieldLabel: 'Número Caso',
                    value: '',
                    width: 375
                }, 
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 50},
                comboSubEstadoIncidencia,
                {html: "&nbsp;", border: false, width: 150},
                comboPrioridadIncidencia,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 50},
                comboEstadoGestionIncidencia,
                {html: "&nbsp;", border: false, width: 150},
                comboNotificarIncidencia,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 50},
                comboCanton,
                {html: "&nbsp;", border: false, width: 150},
                comboCategoria,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 50},
                comboLogin,
                {html: "&nbsp;", border: false, width: 125},
                comboTipoEvento,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 50},
                
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Fecha Ticket',
                    items: [
                        {
                            xtype: 'datefield',
                            width: 270,
                            id: 'feEmisionDesde',
                            name: 'feEmisionDesde',
                            fieldLabel: 'Desde:',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 270,
                            id: 'feEmisionHasta',
                            name: 'feEmisionHasta',
                            fieldLabel: 'Hasta:',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {html: "&nbsp;", border: false, width: 150},
                 {
                    xtype: 'fieldcontainer',
                    defaults: {
                            style: "padding-top:1px",
                            labelStyle: "margin-top:10px"
                    },
                    items: [
                        {
                            xtype: 'textfield',
                            hideTrigger: true,
                            id: 'ipAddressFil',
                            name:'ipAddressFil',
                            fieldLabel: 'Ip Address',
                            value: '',
                            width: 375
                        },
                        comboEstadoIncidencia          
                    ]
                },
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 50},
                comboTipoCliente,
                {html: "&nbsp;", border: false, width: 125},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'ipControllerFil',
                    name:'ipControllerFil',
                    fieldLabel: 'Ip Destino',
                    value: '',
                    width: 375
                },
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 50},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'numeroTareaFil',
                    name:'numeroTareaFil',
                    fieldLabel: 'Número de Tarea',
                    value: '',
                    width: 375
                },,
                {html: "&nbsp;", border: false, width: 125},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'puertoControllerFil',
                    name:'puertoControllerFil',
                    fieldLabel: 'Puerto Destino',
                    value: '',
                    width: 375
                },
                
            ],
        renderTo: 'filtro'
    });
});
