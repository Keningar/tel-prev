Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();

var itemsPerPage = 10;
var store = '';
var estado_id = '';
var strUsuario = '';
var area_id = '';
var login_id = '';
var tipo_asignacion = '';
var pto_sucursal = '';
var idClienteSucursalSesion;
var direccion = "";
var accionBuscar = '';

function eliminarCliente(id) 
{
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

    var strAncho             = 950;
    var boolOcultarVendedor  = true;
    var boolOcultarFeEmision = true;
    var boolOcultarSaldoPend = true;
    var boolOcultarEstado    = true;
    var strAnchoNombreClt    = 300;
    var strDireccion         = 360;
    if( !Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "TN" )
    {
        strAncho             = '100%';
        strAnchoNombreClt    = 255;
        strDireccion         = 220;
        boolOcultarFeEmision = false;
        boolOcultarSaldoPend = false;
        boolOcultarVendedor  = false;
        boolOcultarEstado    = false;
    }
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField(
    {
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
    DTFechaHasta = new Ext.form.DateField(
    {
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
            //anchor : '65%',
            //layout: 'anchor'
    });


    //CREAMOS DATA STORE PARA EMPLEADOS
    Ext.define('modelEstado', 
    {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idestado', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });
    var estado_store = Ext.create('Ext.data.Store', 
    {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url: url_cliente_lista_estados,
            reader: {
                type: 'json',
                root: 'estados'
            }
        }
    });
    var estado_cmb = new Ext.form.ComboBox(
    {
        xtype: 'combobox',
        store: estado_store,
        labelAlign: 'left',
        id: 'idestado',
        name: 'idestado',
        valueField: 'descripcion',
        displayField: 'descripcion',
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
                    //alert(Ext.getCmp('idestado').getValue());
                    estado_id = Ext.getCmp('idestado').getValue();
                },
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function() {
                    estado_id = '';
                    estado_store.removeAll();
                    estado_store.load();
                }
            }
        }
    });

    TFNombre = new Ext.form.TextField(
    {
        id: 'nombre',
        fieldLabel: 'Nombre',
        xtype: 'textfield'
    });
    TFApellido = new Ext.form.TextField(
    {
        id: 'apellido',
        fieldLabel: 'Apellido',
        xtype: 'textfield'
    });
    TFRazonSocial = new Ext.form.TextField(
    {
        id: 'razonSocial',
        fieldLabel: 'Razon Social',
        xtype: 'textfield'
    });
    
    TFIdentificacion = new Ext.form.TextField({
        id: 'identificacion',
        fieldLabel: 'Identificaci\u00F3n',
        xtype: 'textfield'
    });
    
    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'idPersona', type: 'int'},
            {name: 'idPersonaEmpresaRol', type: 'int'},
            {name: 'idOficina', type: 'int'},
            {name: 'nombreOficina', type: 'string'},            
            {name: 'Nombre', type: 'string'},
            {name: 'Direccion', type: 'string'},
            {name: 'fechaCreacion', type: 'string'},
            {name:'usrVendedor', type: 'string'},
            {name:'strTipoPersonal', type: 'string'},
            {name:'feEmision', type: 'string'},
            {name:'strSaldoPendiente', type: 'string'},
            {name:'strVendAsignado', type: 'string'},
            {name: 'usuarioCreacion', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'tipoEmpresa', type: 'string'},
            {name: 'tipoTributario', type: 'string'},
            {name: 'representanteLegal', type: 'string'},
            {name: 'linkVer', type: 'string'},
            {name: 'linkEditar', type: 'string'},
            {name: 'linkEliminar', type: 'string'}
        ]
    });


    store = Ext.create('Ext.data.JsonStore', 
    {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: url_grid,
            reader: {
                type: 'json',
                root: 'clientes',
                totalProperty: 'total'
            },
            extraParams: {fechaDesde: '', fechaHasta: '', estado: '', nombre: '', apellido: '', razonSocial: '',identificacion: '', accionBuscar: ''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) 
            {
                store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.estado = Ext.getCmp('idestado').getValue();
                store.getProxy().extraParams.nombre = Ext.getCmp('nombre').getValue();
                store.getProxy().extraParams.apellido = Ext.getCmp('apellido').getValue();
                store.getProxy().extraParams.razonSocial = Ext.getCmp('razonSocial').getValue();
                store.getProxy().extraParams.identificacion = Ext.getCmp('identificacion').getValue();
                store.getProxy().extraParams.accionBuscar = accionBuscar;
            },
            load: function(store) 
            {
                store.each(function(record) {});
            }
        }
    });

    store.load({params: {start: 0, limit: 10}});



    var sm = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function(selectionModel, selected, options) {
                arregloSeleccionados = new Array();
                Ext.each(selected, function(record) {});
            }
        }
    });


    function eliminarAlgunos() 
    {
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





    listView = Ext.create('Ext.grid.Panel', 
    {
        width: strAncho,
        autoHeight: true,
        collapsible: false,
        title: '',
        dockedItems: 
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: 
            [
                //tbfill -> alinea los items siguientes a la derecha
                {xtype: 'tbfill'}
            ]
        }],
        renderTo: Ext.get('lista_clientes'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando clientes {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
        viewConfig: 
        {
            emptyText: 'No hay datos para mostrar'
        },
        listeners: 
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) 
            {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show(
                {
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        },
        columns: 
        [
            new Ext.grid.RowNumberer(),
            {
                text: 'Nombre',
                width: strAnchoNombreClt,
                dataIndex: 'Nombre'
            },
            {
                text: 'Vendedor',
                width: 260,
                dataIndex: 'usrVendedor',
                hidden:boolOcultarVendedor
            },
            {
                text: 'Direccion',
                dataIndex: 'Direccion',
                width: strDireccion,
                renderer: function(value, metaData, record, colIndex, store, view) 
                {
                    metaData.tdAttr = 'data-qtip="' + value + '"';
                    return value;
                }
            },
            {
                text: 'Fecha Creacion',
                dataIndex: 'fechaCreacion',
                width: 100,
                renderer: function(value, metaData, record, colIndex, store, view) 
                {
                    metaData.tdAttr = 'data-qtip="' + value + '"';
                    return value;
                }
            },
            {
                text: 'Fecha ult. Emision',
                width: 100,
                dataIndex: 'feEmision',
                hidden:boolOcultarFeEmision
            },
            {
                text: 'Saldo Pendiente',
                width: 100,
                dataIndex: 'strSaldoPendiente',
                renderer: this.getColorSaldoPendiente,
                hidden:boolOcultarSaldoPend
            },
            {
                text: 'Estado',
                width: 50,
                dataIndex: 'estado',
                hidden:boolOcultarEstado
            },
            {
                /* CAMBIO RONALD SAENZ 22MAYO... BOTONES ACTION COLUM */
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 190,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classA = "button-grid-show";
                            var strContinuar = 'S';
                            this.items[0].tooltip = 'Ver';
                            if( (rec.data.strTipoPersonal != 'Otros' && rec.data.strTipoPersonal !='GERENTE_VENTAS') && (!Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "TN") )
                            {
                                strContinuar = 'N';
                            }
                            if( strContinuar != 'S' && rec.data.strVendAsignado !='S' )
                            {
                                classA = "icon-invisible"
                                this.items[0].tooltip = '';
                            }
                            return classA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var classA = "button-grid-show";
                            if (classA != "icon-invisible")
                                window.location = rec.data.linkVer;
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    }, 
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classA = "button-grid-edit";
                            var strContinuar = 'S';
                            this.items[1].tooltip = 'Editar';
                            if( (rec.data.strTipoPersonal != 'Otros' && rec.data.strTipoPersonal !='GERENTE_VENTAS') && (!Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "TN") )
                            {
                                strContinuar = 'N';
                            }
                            if( strContinuar == 'S' )
                            {
                                if ( rec.data.estado == "Inactivo" ) 
                                {
                                    classA = "icon-invisible";
                                    this.items[1].tooltip = '';
                                }
                            }
                            else
                            {
                                if ( rec.data.estado == "Inactivo" || rec.data.strVendAsignado !='S' ) 
                                {
                                    classA = "icon-invisible";
                                    this.items[1].tooltip = '';
                                }
                            }
                            return classA;
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var classA = "button-grid-edit";
                            if (rec.data.estado == "Inactivo") {
                                classA = "icon-invisible";
                            }

                            if (classA != "icon-invisible")
                                window.location = rec.data.linkEditar;
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classA = "button-grid-editarDireccion";
                            var permiso = puedeEditarDireccion;
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);						
                            var strContinuar = 'S';
                            this.items[2].tooltip = 'Actualizar Direccion Tributaria';
                            if( (rec.data.strTipoPersonal != 'Otros' && rec.data.strTipoPersonal !='GERENTE_VENTAS') && (!Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "TN") )
                            {
                                strContinuar = 'N';
                            }
                            if( strContinuar == 'S' )
                            {
                                if( !boolPermiso ) 
                                {
                                    classA = "icon-invisible";
                                    this.items[2].tooltip = '';
                                }
                            }
                            else
                            {
                                if ( !boolPermiso || rec.data.strVendAsignado !='S' )
                                {
                                    classA = "icon-invisible";
                                    this.items[2].tooltip = '';
                                }
                            }
                            return classA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var classA = "button-grid-editarDireccion";

                            var permiso = puedeEditarDireccion;
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
                            if (!boolPermiso) {
                                classA = "icon-invisible";
                            }

                            if (classA != "icon-invisible")
                                showEditarDireccionTributaria(grid.getStore().getAt(rowIndex).data);
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },                   
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classA = "button-grid-Import";

                            var permiso = puedeEditarNombre;
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            var strContinuar = 'S';
                            var permisoActualizar = puedeActualizarNombre_RS;
                            var boolPermisoAct = (typeof permisoActualizar === 'undefined') ? false : (permisoActualizar ? true : false);

                            this.items[3].tooltip = 'Actualizar Nombre o Razon social';
                            if( (rec.data.strTipoPersonal != 'Otros' && rec.data.strTipoPersonal !='GERENTE_VENTAS') && (!Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "TN") )
                            {
                                strContinuar = 'N';
                            }

                            if( strContinuar == 'S' )
                            {   
                                //Valida empresa MD 
                                if(!Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "MD")
                                {   //Si perfil no tiene permiso se inhabilita el boton de actualizar Nombre o RS
                                    if (!boolPermisoAct) 
                                    { 
                                        classA = "icon-invisible";
                                        this.items[3].tooltip = '';
                                       
                                    }
                                }
                                else //caso contrario es cliente TN y valida si el perfil permite o no actualizar Nombre o RS
                                {
                                    if (!boolPermiso) 
                                    { 
                                        classA = "icon-invisible";
                                        this.items[3].tooltip = '';
                                       
                                    }
                                }
                            }
                            else
                            {
                                if( !boolPermiso || rec.data.strVendAsignado !='S' )
                                {
                                    classA = "icon-invisible";
                                    this.items[3].tooltip = '';
                                   
                                }
                            }
                            return classA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var classA = "button-grid-Import";
                        
                            var permiso = puedeEditarNombre;
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso) 
                            {
                                classA = "icon-invisible";
                            }

                            if (classA !== "icon-invisible")
                                accionActualizaNombre(grid.getStore().getAt(rowIndex).data.idPersona,
                                    grid.getStore().getAt(rowIndex).data.idPersonaEmpresaRol,
                                    grid.getStore().getAt(rowIndex).data.idOficina,
                                    grid.getStore().getAt(rowIndex).data.nombreOficina,
                                    grid.getStore().getAt(rowIndex).data.Nombre,
                                    grid.getStore().getAt(rowIndex).data.tipoEmpresa,
                                    grid.getStore().getAt(rowIndex).data.tipoTributario,
                                    grid.getStore().getAt(rowIndex).data.representanteLegal);
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classA  = "button-grid-editarTipoEmpresaTributario";
                            var permiso = boolEditarTipoEmpresaTributario;
                            if(prefijoEmpresa != 'TN')
                            {
                              permiso = false;
                            }                            
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
                            var strContinuar = 'S';
                            this.items[4].tooltip = 'Actualizar Tipo Empresa-Tributario';
                            if( (rec.data.strTipoPersonal != 'Otros' && rec.data.strTipoPersonal !='GERENTE_VENTAS') && (!Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "TN") )
                            {
                                strContinuar = 'N';
                            }
                            if( strContinuar == 'S' )
                            {
                                if( !boolPermiso )
                                {
                                    classA = "icon-invisible";
                                    this.items[4].tooltip = '';
                                }
                            }
                            else
                            {
                                if( !boolPermiso || rec.data.strVendAsignado !='S' )
                                {
                                    classA = "icon-invisible";
                                    this.items[4].tooltip = '';
                                }
                            }
                            return classA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var classA      = "button-grid-editarTipoEmpresaTributario";
                            var permiso     = boolEditarTipoEmpresaTributario;
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
                            if (!boolPermiso) 
                            {
                                classA = "icon-invisible";
                            }
                        
                            if(classA != "icon-invisible")
                            {                            
                              showEditarTipoEmpresaTributario(grid.getStore().getAt(rowIndex).data);
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClass          = "icon-invisible";
                            this.items[5].tooltip = '';
                            if(prefijoEmpresa == 'TN' && rec.data.estado == 'Cancelado' && rec.data.strSaldoPendiente > 0)
                            {
                                strClass              = "button-grid-reactivarCliente";
                                this.items[5].tooltip = "Crear Solicitud Reactivación de Cliente";
                            }
                            return strClass;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var objData = store.getAt(rowIndex) ? store.getAt(rowIndex) : null;
                            if(objData != undefined && objData != null)
                            {
                                var strEstado         = objData.get('estado') ? objData.get('estado'):'';
                                var strSaldoPendiente = objData.get('strSaldoPendiente') ? objData.get('strSaldoPendiente'):'';
                                if(prefijoEmpresa == 'TN' && strEstado == 'Cancelado' && strSaldoPendiente > 0)
                                {
                                    showReactivarCliente(grid.getStore().getAt(rowIndex).data);
                                }
                                else
                                {
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta acción');
                                }
                            }
                        }
                    }
                ]
            }
        ]
    });


    function renderAcciones(value, p, record) 
    {
        var iconos = '';
        iconos = iconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a></b>';
        if (record.data.estado != 'Inactivo') {
            iconos = iconos + '<b><a href="' + record.data.linkEditar + '" onClick="" title="Editar"  class="button-grid-edit"></a></b>';
        }
        return Ext.String.format(iconos,value);
    }


    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7, // Don't want content to crunch against the borders
        border: false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'table',
            columns: 4,
            align: 'left'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        defaults: 
        {
            // applied to each contained panel
            bodyStyle: 'padding:10px'
        },
        collapsible: true,
        collapsed: true,
        width: strAncho,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                //xtype: 'button',
                iconCls: "icon_search",
                handler: Buscar
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: Limpiar
            }

        ],
        items: 
        [
            DTFechaDesde,
            {html: "&nbsp;", border: false, width: 50},
            DTFechaHasta,
            {html: "&nbsp;", border: false, width: 50},
            TFNombre,
            {html: "&nbsp;", border: false, width: 50},
            TFApellido,
            {html: "&nbsp;", border: false, width: 50},
            TFRazonSocial,
            {html: "&nbsp;", border: false, width: 50},
            estado_cmb,
            {html: "&nbsp;", border: false, width: 50},
            TFIdentificacion,
            {html: "&nbsp;", border: false, width: 50},              
        ],
        renderTo: 'filtro_clientes'
    });


    function Buscar() 
    {
        var strIdentificacion = Ext.getCmp('identificacion').getValue();
        accionBuscar = 'S';
        if (strIdentificacion!== null && strIdentificacion!=='')
        {  
          if (!(/^\d+$/.test(strIdentificacion)))
          {
              Ext.Msg.alert('Alerta ', 'Identificaci\u00F3n no v\u00E1lida.');
              return false;
          }
        }
        if ((Ext.getCmp('fechaDesde').getValue() != null) && (Ext.getCmp('fechaHasta').getValue() != null))
        {
            if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
            {
                Ext.Msg.show({
                    title: 'Error en Busqueda',
                    msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });

            }
            else
            {
                store.load({params: {start: 0, limit: 10}});
            }
        }
        else
        {
            store.load({params: {start: 0, limit: 10}});
        }
        store.currentPage = 1;
        store.load();
    }

    function Limpiar() {

        Ext.getCmp('fechaDesde').setValue('');
        Ext.getCmp('fechaHasta').setValue('');
        Ext.getCmp('idestado').setValue('');
        Ext.getCmp('nombre').setValue('');
        Ext.getCmp('apellido').setValue('');
        Ext.getCmp('razonSocial').setValue('');
        Ext.getCmp('identificacion').setValue('');
        accionBuscar = '';
        store.currentPage = 1;
        store.load();
    }


});


/* function Editar Direccion Tributaria del Cliente - 22Mayo Ronald Saenz */
function showEditarDireccionTributaria(data) {

    direccion = data.Direccion;

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Loading...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    
    btnguardar2 = Ext.create('Ext.Button', {
        id: 'btnguardar2',
        name: 'btnguardar2',
        text: 'Guardar',
        cls: 'x-btn-rigth',
        disabled: true,
        handler: function() {
            conn.request({
                method: 'POST',
                params: {
                    id_persona: data.idPersona,
                    id_personaEmpresaRol: data.idPersonaEmpresaRol,
                    direccionTributaria: Ext.getCmp('direccion_tributaria').value
                },
                url: url_actualizarDireccionTributaria,
                success: function(response)
                {
                    strMensaje   = 'Se actualizó satisfactoriamente la Dirección Tributaria';
                    strTitulo    = 'Mensaje';
                    objRespuesta = Ext.JSON.decode(response.responseText);
                    
                    if(!objRespuesta.success)
                    {
                        strTitulo  = 'Error';
                        strMensaje = objRespuesta.mensaje;
                    }
                    
                    Ext.Msg.alert(strTitulo, strMensaje, function(btn) 
                    {
                        if (btn === 'ok')
                        {
                            winDireccionTributaria.destroy();
                            store.load();
                        }
                    });
                },
                failure: function(rec, op) 
                {
                    var json = Ext.JSON.decode(op.response.responseText);
                    Ext.Msg.alert('Alerta ', json.mensaje);
                }
            });
        }
    });
    btncancelar2 = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            winDireccionTributaria.destroy();
        }
    });

    formPanel2 = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        height: 200,
        width: 500,
        layout: 'fit',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 140,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Información',
                defaultType: 'textfield',
                items: [
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Cliente:',
                        id: 'nombreCliente',
                        name: 'nombreCliente',
                        value: data.Nombre
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Direccion Tributaria:',
                        id: 'direccion_tributaria',
                        name: 'direccion_tributaria',
                        value: data.Direccion,
                        rows: 7,
                        cols: 70,
                        listeners:
                            {
                                change:
                                    function(cmp, nuevo)
                                    {
                                        var enabled = direccion.toLowerCase() !== nuevo.toLowerCase();
                                        Ext.getCmp('btnguardar2').setDisabled(!enabled);
                                    }
                            }
                    }
                ]
            }
        ]
    });

    winDireccionTributaria = Ext.create('Ext.window.Window', {
        title: 'Actualizar Dirección Tributaria',
        modal: true,
        width: 660,
        height: 280,
        resizable: false,
        layout: 'fit',
        items: [formPanel2],
        buttonAlign: 'center',
        buttons: [btnguardar2, btncancelar2]
    }).show();

}


/**
 * Documentación para el método 'showEditarTipoEmpresaTributario'.
 * Función que envia mediante post el id de la persona y la informacion del cliente que sera enviada
 * al controlador para la respectiva actualización.
 * 
 * @param mixed  data          
 *
 * @author Edgar Holguín <eholguín@telconet.ec>
 * @version 1.0 17-11-2016 
 */
function showEditarTipoEmpresaTributario(data) 
{    
    Ext.onReady(function(){
    
    var tipoEmpresa = Ext.create('Ext.data.Store', {
          fields: ['abbr', 'textTipoEmpresa'],
          data: [	
          {
               "abbr": "",
               "textTipoEmpresa": "--Seleccione--"
          },            
          {
               "abbr": "Privada",
               "textTipoEmpresa": "Privada"
          },
          {
              "abbr": "Publica",
              "textTipoEmpresa": "Pública"
          }]
         });

     var cmbTipoEmpresa= Ext.create('Ext.form.ComboBox', {
         xtype: 'combobox',
         fieldLabel: 'Tipo Empresa',
         store: tipoEmpresa,
         queryMode: 'local',
         id:'textTipoEmpresa',
         name: 'textTipoEmpresa',
         valueField: 'abbr',
         displayField:'textTipoEmpresa',		  
         width: 325,
         triggerAction: 'all',
         selectOnFocus:true,
         lastQuery: '',
         mode: 'local',
         allowBlank: false,
         value: data.tipoEmpresa, 
        });   

      var tipoTributario = Ext.create('Ext.data.Store', {
        fields: ['abbr', 'textTipoTributario'],
        data: [
                {
                     "abbr": "",
                     "textTipoTributario": "--Seleccione--"
                },            
                {
                    "abbr": "NAT",
                    "textTipoTributario": "Natural"
                },
                {
                    "abbr": "JUR",
                    "textTipoTributario": "Jurídico"
                }
              ]
      });
      
      var cmbTipoTributario = Ext.create('Ext.form.ComboBox', {
        xtype: 'combobox',
        fieldLabel: 'Tipo Tributario',
        store: tipoTributario,
        queryMode: 'local',
        displayField: 'textTipoTributario',
        id: 'textTipoTributario',
        name: 'textTipoTributario',
        valueField: 'abbr',        
        width: 325,        
        allowBlank: false,
        value: data.tipoTributario,
    });
    
      
    panelCliente = Ext.create('Ext.form.Panel', {
        title: '',
        renderTo: Ext.getBody(),
        bodyPadding: 5,
        width: 480,
        items:[
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:
                {
                    width: 430
                },
                items: [
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Cliente:',
                    name: 'nombre_actual',
                    value: data.Nombre
                },    
                cmbTipoEmpresa,
                cmbTipoTributario          
              ]
            }
        ],
        buttons:
        [
            {
                text: 'Guardar',
                name: 'guardarBtn',
                disabled: false,
                handler: function() 
                {
                    var form1 = this.up('form').getForm(); 

                    if('' === Ext.getCmp('textTipoEmpresa').getValue() || '' === Ext.getCmp('textTipoTributario').getValue())
                    {
                        Ext.MessageBox.show({
                            modal: true,
                            title: 'Error',
                            msg: 'Ingrese todos los campos solicitados',
                            width: 300,
                            icon: Ext.MessageBox.ERROR,
                            buttons: Ext.Msg.OK  
                        });
                        return false;
                    }
                    
                    if("Publica" === Ext.getCmp('textTipoEmpresa').getValue() && "JUR" !==Ext.getCmp('textTipoTributario').getValue())
                    {
                        Ext.MessageBox.show({
                            modal: true,
                            title: 'Error',
                            msg: 'Tipo tributario ingresado no corresponde al tipo empresa seleccionado.',
                            width: 300,
                            icon: Ext.MessageBox.ERROR,
                            buttons: Ext.Msg.OK                            
                        });
                        return false;
                    }                    
                    
                    if (form1.isValid()) {

                        Ext.MessageBox.show({
                            msg: 'Guardando datos...',
                            title: 'Procesando',
                            progressText: 'Mensaje',
                            progress: true,
                            closable: false,
                            width: 300,
                            wait: true,
                            waitConfig: {interval: 200}
                        });

                        Ext.Ajax.request({
                            url: urlEditarTipoEmpresaTributario,
                            method: 'POST',
                            params: 
                            {
                                idPersona: data.idPersona,
                                idPersonaRol: data.idPersonaEmpresaRol,
                                tipoEmpresaNuevo: Ext.getCmp('textTipoEmpresa').getValue(),
                                tipoTributarioNuevo: Ext.getCmp('textTipoTributario').getValue()
                            },
                            success: function(response, request) 
                            {
                                Ext.MessageBox.hide();
                                var obj = Ext.decode(response.responseText);
                                if (obj.success) 
                                {
                                    listView.getStore().load();
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Información',
                                        msg: 'Datos han sido actualizados correctamente.',
                                        width: 300,
                                        icon: Ext.MessageBox.INFO,
                                        buttons: Ext.Msg.OK
                                    });
                                    form1.reset();
                                    ventanaEditarDatos.destroy();
                                } 
                                else 
                                {
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Error',
                                        msg: 'Error al guardar.',
                                        width: 300,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }

                            },
                            failure: function() 
                            {
                                Ext.MessageBox.show({
                                    modal: true,
                                    title: 'Error',
                                    msg: 'Error al guardar.',
                                    width: 300,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        });

                    }
                    else 
                    {
                        Ext.MessageBox.show({
                            modal: true,
                            title: 'Información',
                            msg: 'Ingrese todos los campos solicitados',
                            width: 300,
                            icon: Ext.MessageBox.ERROR
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
            }
        ]
    });  

    ventanaEditarDatos = Ext.widget('window', {
        title: 'Actualizar Tipo empresa tributario',
        closeAction: 'destroy',
        closable: true,
        width: 480,
        height: 200,
        minHeight: 200,
        autoScroll: true,
        layout: 'fit',
        resizable: true,
        modal: true,
        items: panelCliente
    });

    ventanaEditarDatos.show();
 });    
}

    /**
     * Documentación para la función 'showReactivarCliente'.
     *
     * Función que envia mediante post información para crear tarea y solicitud de reactivación del cliente.
     *
     * @param object  objData Contiene el store de la data
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     */
    function showReactivarCliente(objData)
    {
        var strTituloReingreso    = "Reingreso de Cliente";
        var strTituloSolicitud    = "Solicitud Reactivar Cliente"
        var strTitulo             = (objData.strSaldoPendiente > 0 && objData.strSaldoPendiente < 100) ? strTituloReingreso:strTituloSolicitud;
        var strMensajeCreandoClte = "Creando Pre-cliente...";
        var strMensajeCreandoSol  = "Creando Solicitud...";
        var strMensaje            = (objData.strSaldoPendiente > 0 && objData.strSaldoPendiente < 100) ? strMensajeCreandoClte:strMensajeCreandoSol;
        var objStorePago = Ext.create('Ext.data.Store',
        {
            fields: ['abbr', 'textPago'],
            data: [
                    {
                        "abbr"     : "",
                        "textPago" : ""
                    },
                    {
                        "abbr"     : "Si",
                        "textPago" : "Si"
                    },
                    {
                        "abbr"     : "No",
                        "textPago" : "No"
                    }
                    ]
        });
        var objComboPago= Ext.create('Ext.form.ComboBox',
        {
            xtype         : 'combobox',
            fieldLabel    : 'Va a pagar',
            store         : objStorePago,
            queryMode     : 'local',
            id            : 'textPago',
            name          : 'textPago',
            valueField    : 'abbr',
            displayField  : 'textPago',
            width         : 350,
            triggerAction : 'all',
            selectOnFocus : true,
            lastQuery     : '',
            mode          : 'local',
            allowBlank    : false,
            value         : '',
        });
        Ext.define('modelUsuario', 
        {
            extend: 'Ext.data.Model',
            fields:
            [
                {name: 'login',       type: 'string'},
                {name: 'descripcion', type: 'string'}
            ]
        });
        var objStoreUs = Ext.create('Ext.data.Store', 
        {
            autoLoad : false,
            model    : "modelUsuario",
            proxy    :
            {
                type   : 'ajax',
                url    : url_cliente_lista_usuario,
                reader :
                {
                    type : 'json',
                    root : 'usuario'
                },
                extraParams:
                {
                    strSaldoPendiente: objData.strSaldoPendiente
                },
                simpleSortMode: true
            }
        });
        var objComboUs = new Ext.form.ComboBox(
        {
            xtype         : 'combobox',
            store         : objStoreUs,
            labelAlign    : 'left',
            id            : 'login',
            name          : 'login',
            valueField    : 'login',
            displayField  : 'descripcion',
            fieldLabel    : 'Asignado a',
            width         : 350,
            triggerAction : 'all',
            selectOnFocus : true,
            lastQuery     : '',
            mode          : 'local',
            allowBlank    : true,
            listeners     :
            {
                select:
                    function(e)
                    {
                        strUsuario = Ext.getCmp('login').getValue();
                    },
                click:
                {
                    element: 'el',
                    fn: function()
                    {
                        strUsuario = '';
                        objStoreUs.removeAll();
                        objStoreUs.load();
                    }
                }
            }
        });
        var objAcuerdopago = new Ext.form.field.TextArea(
        {
            xtype      : 'textareafield',
            grow       : true,
            name       : 'textAcuerdoPago',
            id         : 'textAcuerdoPago',
            fieldLabel : 'Acuerdo de pago',
            allowBlank : false,
            width      : 350
        });
        objPanelReactivarCliente = Ext.create('Ext.form.Panel',
        {
            title       : '',
            renderTo    : Ext.getBody(),
            bodyPadding : 5,
            width       : 350,
            height      : 173,
            items:[
                {
                    xtype       : 'fieldset',
                    title       : '',
                    defaultType : 'textfield',
                    defaults    : { width: 350},
                    items:
                    [
                        {
                            xtype      : 'displayfield',
                            fieldLabel : 'Cliente:',
                            name       : 'nombre_actual',
                            value      : objData.Nombre
                        },
                        objComboPago,
                        objComboUs,
                        objAcuerdopago
                    ]
                }
            ],
            buttons:
            [
                {
                    text     : 'Guardar',
                    name     : 'guardarBtn',
                    disabled : false,
                    handler  : function()
                    {
                        var form1 = this.up('form').getForm();
                        if('' === Ext.getCmp('login').getValue() 
                        || '' === Ext.getCmp('textPago').getValue())
                        {
                            Ext.MessageBox.show({
                                modal   : true,
                                title   : 'Error',
                                msg     : 'Ingrese todos los campos solicitados',
                                width   : 300,
                                icon    : Ext.MessageBox.ERROR,
                                buttons : Ext.Msg.OK  
                            });
                            return false;
                        }
                        if (form1.isValid())
                        {
                            Ext.MessageBox.show({
                                msg          : strMensaje,
                                title        : 'Procesando',
                                progressText : 'Mensaje',
                                progress     : true,
                                closable     : false,
                                width        : 300,
                                wait         : true,
                                waitConfig   : {interval: 200}
                            });
                            Ext.Ajax.request({
                                url: urlReactivacion,
                                method: 'POST',
                                timeout: 900000,
                                params: 
                                {
                                    strNombreClt      : objData.Nombre,
                                    intIdPersona      : objData.idPersona,
                                    intIdPersonaRol   : objData.idPersonaEmpresaRol,
                                    strPago           : Ext.getCmp('textPago').getValue(),
                                    strAcuerdoPago    : Ext.getCmp('textAcuerdoPago').getValue(),
                                    strLogin          : Ext.getCmp('login').getValue(),
                                    strSaldoPendiente : objData.strSaldoPendiente
                                },
                                success: function(response, request) 
                                {
                                    Ext.MessageBox.hide();
                                    var obj = Ext.decode(response.responseText);
                                    if (obj.success) 
                                    {
                                        listView.getStore().load();
                                        Ext.MessageBox.show({
                                            modal   : true,
                                            title   : 'Información',
                                            msg     : obj.msg,
                                            width   : 300,
                                            icon    : Ext.MessageBox.INFO,
                                            buttons : Ext.Msg.OK
                                        });
                                        form1.reset();
                                        objVentanaReactivarCliente.destroy();
                                    }
                                    else
                                    {
                                        Ext.MessageBox.show({
                                            modal : true,
                                            title : 'Error',
                                            msg   : 'Error, por favor comunicar al departamento Sistemas.',
                                            width : 300,
                                            buttons : Ext.Msg.OK,
                                            icon  : Ext.MessageBox.ERROR
                                        });
                                    }
                                },
                                failure: function() 
                                {
                                    Ext.MessageBox.show({
                                        modal : true,
                                        title : 'Error',
                                        msg   : 'Error, por favor comunicar al departamento Sistemas.',
                                        width : 300,
                                        buttons : Ext.Msg.OK,
                                        icon  : Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        }
                        else 
                        {
                            Ext.MessageBox.show({
                                modal : true,
                                title : 'Información',
                                msg   : 'Ingrese todos los campos solicitados',
                                width : 300,
                                icon  : Ext.MessageBox.ERROR
                            });
                        }
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function()
                    {
                        this.up('form').getForm().reset();
                        this.up('window').destroy();
                    }
                }
            ]
        });
        objVentanaReactivarCliente = Ext.widget('window',
        {
            title       : strTitulo,
            closeAction : 'destroy',
            closable    : true,
            width       : 400,
            height      : 250,
            minHeight   : 200,
            autoScroll  : true,
            layout      : 'fit',
            resizable   : false,
            modal       : true,
            items       : objPanelReactivarCliente
        });
        objVentanaReactivarCliente.show();
    }

    /**
     * Documentación para la función 'getColorSaldoPendiente'.
     *
     * Función que cambia de color cuando el saldo pendiente es mayor a 0.
     *
     * @param object  objData Contiene el store de la data
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     */
    this.getColorSaldoPendiente = function (strValue)
    {
        if(strValue!== "" && strValue>0)
        {
            return '<span style="color:red; font-weight: bold;">$ ' + strValue + '</span>';
        }
    }