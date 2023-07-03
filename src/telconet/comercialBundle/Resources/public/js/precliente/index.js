Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 10;
var store='';
var estado_id='';
var area_id='';
var login_id='';
var tipo_asignacion='';
var pto_sucursal='';
var idClienteSucursalSesion;


function eliminarProspecto(id)
{
    Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', 
    function(btn)
    {
        if(btn=='yes')
        {
           Ext.Ajax.request(
           {
                url: url_precliente_delete_ajax,
                params: { param : id},                     
                method: 'get',                     
                success: function(response){
                    var text = response.responseText;
                    Ext.Msg.alert(text);
                    store.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
           });
        }
    });
}



Ext.onReady(function()
{
    var strAncho             = 950;
    var boolOcultarVendedor  = true;
    var boolOcultarFeEmision = true;
    var strAnchoNombreClt    = 275;
    var strDireccion         = 350;
    if( !Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "TN" )
    {
        strAncho             = '100%';
        strAnchoNombreClt    = 250;
        strDireccion         = 160;
        boolOcultarFeEmision = false;
        boolOcultarVendedor  = false;
    }
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325
    });


    //CREAMOS DATA STORE PARA EMPLEADOS
    Ext.define('modelEstado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idestado', type: 'string'},
            {name: 'codigo',  type: 'string'},
            {name: 'descripcion',  type: 'string'}                    
        ]
    });			
    var estado_store = Ext.create('Ext.data.Store', 
    {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url : url_precliente_lista_estados,
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
                    estado_id = Ext.getCmp('idestado').getValue();
                },
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function() {
                    if(estado_store.getCount()==0)
                    {
                        estado_id = '';
                        estado_store.removeAll();
                        estado_store.load();
                    }
                }
            }
        }
    });

    TFNombre = new Ext.form.TextField({
        id: 'nombre',
        fieldLabel: 'Nombre',
        xtype: 'textfield'
    });
    TFApellido = new Ext.form.TextField({
        id: 'apellido',
        fieldLabel: 'Apellido',
        xtype: 'textfield'
    });			
    TFRazonSocial = new Ext.form.TextField({
        id: 'razonSocial',
        fieldLabel:'Razon Social',
        xtype: 'textfield'
    });
    TFIdentificacion = new Ext.form.TextField({
        id: 'identificacion',
        fieldLabel: 'Identificacion',
        xtype: 'textfield'
    });    
    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'idPersona', type: 'int'},
            {name:'idPersonaEmpresaRol', type: 'int'},
            {name:'idOficina', type: 'int'},
            {name:'nombreOficina', type: 'string'},            
            {name:'Nombre', type: 'string'},
            {name:'Direccion', type: 'string'},
            {name:'fechaCreacion', type: 'string'},
            {name:'usrVendedor', type: 'string'},
            {name:'strTipoPersonal', type: 'string'},
            {name:'feEmision', type: 'string'},
            {name:'strVendAsignado', type: 'string'},
            {name:'usuarioCreacion', type: 'string'},
            {name:'loginUserCreacion', type: 'string'},
            {name:'estado', type: 'string'},
            {name:'tipoEmpresa', type: 'string'},
            {name:'tipoTributario', type: 'string'},
            {name:'representanteLegal', type: 'string'},
            {name:'linkVer', type: 'string'},
            {name:'linkEditar', type: 'string'},
            {name:'linkEliminar', type: 'string'}
        ]
    }); 


    store = Ext.create('Ext.data.JsonStore', 
    {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url: url_grid,
            timeout: 9000000,
            reader: 
            {
                type: 'json',
                root: 'preclientes',
                totalProperty: 'total'
            },
            extraParams: {fechaDesde: '', fechaHasta: '', estado: '', nombre: '', apellido: '', razonSocial: '',identificacion: ''},
            simpleSortMode: true
        },
        listeners:
        {
            beforeload: function(store)
            {
                store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.estado = Ext.getCmp('idestado').getValue();
                store.getProxy().extraParams.nombre = Ext.getCmp('nombre').getValue();
                store.getProxy().extraParams.apellido = Ext.getCmp('apellido').getValue();
                store.getProxy().extraParams.razonSocial = Ext.getCmp('razonSocial').getValue();
                store.getProxy().extraParams.identificacion = Ext.getCmp('identificacion').getValue();
            }
        }
    });


    var sm = new Ext.selection.CheckboxModel( 
    {
        listeners:
        {
             selectionchange: function(selectionModel, selected, options)
             {
                 arregloSeleccionados= new Array();
                 Ext.each(selected, function(record){
                 });			
             },
            select: function( selectionModel, record, index, eOpts )
            {
                if(record.data.estado!='Activo')
                {
                    sm.deselect(index);
                }
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
                Ext.Msg.confirm('Alerta', 'Se eliminaran los registros. Desea continuar?', function(btn) 
                {
                    if (btn == 'yes') 
                    {
                        Ext.Ajax.request({
                            url: url_precliente_delete_ajax,
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
            items: [
                //tbfill -> alinea los items siguientes a la derecha
                {xtype: 'tbfill'},
            ]
        }],
        renderTo: Ext.get('lista_prospectos'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando prospectos {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
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
                text: 'Nombre',
                width: strAnchoNombreClt,
                dataIndex: 'Nombre'
            }, 
            {
                text: 'Vendedor',
                width: 250,
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
                renderer: function(value, metaData, record, colIndex, store, view) {
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
                text: 'Estado',
                dataIndex: 'estado',
                width: 50
            }, 
            {
                text: 'Acciones',
                width: 150,
                renderer: renderAcciones
            }
        ]
    });            


    function renderAcciones(value, p, record) 
    {
        var strIconos = '';
        var strContinuar = 'S';
        if( (record.data.strTipoPersonal != 'Otros' && record.data.strTipoPersonal !='GERENTE_VENTAS') && (!Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "TN") )
        {
            strContinuar = 'N';
        }
        if( strContinuar == 'S' )
        {
            strIconos = strIconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a></b>';
            if ((record.data.estado == 'Activo') || (record.data.estado == 'Pendiente')) 
            {
                strIconos = strIconos + '<b><a href="' + record.data.linkEditar + 
                    '" onClick="" title="Editar"  class="button-grid-edit"></a></b>';
                if(puedeEditarNombre)
                {    
                    strIconos = strIconos + '<b><a href="#" onClick="accionActualizaNombre('+ record.data.idPersona + ',\''+
                        record.data.idPersonaEmpresaRol+'\',\''+ 
                        record.data.idOficina+'\',\''+ 
                        record.data.nombreOficina+'\',\''+ 
                        record.data.Nombre + '\',\''+record.data.tipoEmpresa+'\',\''+
                        record.data.tipoTributario+ '\',\''+record.data.representanteLegal+
                        '\')" title="Actualizar Nombre o Razon social"  class="button-grid-Import"></a></b>';
                }
            }
        }
        else
        {
            if( record.data.strVendAsignado == 'S' )
            {
                strIconos = strIconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a></b>';
                if ((record.data.estado == 'Activo') || (record.data.estado == 'Pendiente')) 
                {
                    strIconos = strIconos + '<b><a href="' + record.data.linkEditar + 
                        '" onClick="" title="Editar"  class="button-grid-edit"></a></b>';
                    if(puedeEditarNombre)
                    {    
                        strIconos = strIconos + '<b><a href="#" onClick="accionActualizaNombre('+ record.data.idPersona + ',\''+
                            record.data.idPersonaEmpresaRol+'\',\''+ 
                            record.data.idOficina+'\',\''+ 
                            record.data.nombreOficina+'\',\''+ 
                            record.data.Nombre + '\',\''+record.data.tipoEmpresa+'\',\''+
                            record.data.tipoTributario+ '\',\''+record.data.representanteLegal+
                            '\')" title="Actualizar Nombre o Razon social"  class="button-grid-Import"></a></b>';
                    }
                }
            }
        }
        if( ( puedeEditarUser && record.data.estado == 'Pendiente' ) && (!Ext.isEmpty(prefijoEmpresa) && prefijoEmpresa == "TN") )
        {
            strIconos = strIconos + '<b><a href="#" onClick="accionActualizaUsuarioCreacion('+ record.data.idPersona + ',\''+
                record.data.idPersonaEmpresaRol+'\',\''+ 
                record.data.usuarioCreacion +'\',\''+
                record.data.loginUserCreacion+'\',\''+
                record.data.Nombre+
                '\')" title="Actualizar Usuario creación"  class="button-grid-editUser"></a></b>';
        }
        return Ext.String.format(strIconos,value);
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
            bodyStyle: 'padding:10px'
        },
        collapsible: true,
        collapsed: true,
        width: strAncho,
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
        renderTo: 'filtro_prospectos'
    }); 


    function Buscar() 
    {
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
                store.loadData([],false);
                store.currentPage = 1;
                store.load();
            }
        }
        else
        {
            store.loadData([],false);
            store.currentPage = 1;
            store.load();
        }
    }

    function Limpiar() 
    {
        Ext.getCmp('fechaDesde').setValue('');
        Ext.getCmp('fechaHasta').setValue('');
        Ext.getCmp('idestado').setValue('');
        Ext.getCmp('nombre').setValue('');
        Ext.getCmp('apellido').setValue('');
        Ext.getCmp('razonSocial').setValue('');
        Ext.getCmp('identificacion').setValue('');
        
        store.loadData([],false);
        store.currentPage = 1;
        store.load();
    }
});

