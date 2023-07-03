/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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

Ext.onReady(function(){
    
//CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        //anchor : '65%',
        //layout: 'anchor'
});
DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        //anchor : '65%',
        //layout: 'anchor'
});
var txtDescripcion = Ext.create('Ext.form.TextField', {
xtype: 'textfield',
fieldLabel: 'Descripcion',
store: states,
id:'txtDescripcion',
name: 'txtDescripcion',
value:'',
width: 325

});
//CREAMOS DATA STORE PARA LOS NOMBRES TECNICOS
Ext.define('modelNombresTecnicos', {
extend: 'Ext.data.Model',
fields: [
    {name: 'nombre', type: 'string'}              
]
});	
var states = Ext.create('Ext.data.Store', {
autoLoad: false,
model: "modelNombresTecnicos",
proxy: {
    type: 'ajax',
    url : url_nombres_tecnicos,
    reader: {
        type: 'json',
        root: 'nombres'
            }
        }
});	        
var cmbNombreTecnico = Ext.create('Ext.form.ComboBox', {
xtype: 'combobox',
fieldLabel: 'Nombre Tecnico',
store: states,
queryMode: 'local',
id:'idNombreTecnico',
name: 'idNombreTecnico',
valueField:'nombre',
displayField:'nombre',		  
width: 325,
triggerAction: 'all',
selectOnFocus:true,
lastQuery: '',
mode: 'local',
allowBlank: false,
listeners: {
        click: {
            element: 'el', //bind to the underlying el property on the panel
            fn: function(){ 
                if(states.getCount()==0)
                {
                    states.removeAll();
                    states.load();
                }
            }
        }			
    }
});

//CREAMOS DATA STORE PARA LOS GRUPOS
Ext.define('modelGrupo', {
extend: 'Ext.data.Model',
fields: [
    {name: 'idGrupo', type: 'string'},
    {name: 'descripcion',  type: 'string'}
]
});
var grupo = Ext.create('Ext.data.Store', {
autoLoad: false,
model: "modelGrupo",
proxy: {
    type: 'ajax',
    url : url_grupo,
    reader: {
        type: 'json',
        root: 'strGrupo'
            }
        }
});	        
var cmbGrupo = Ext.create('Ext.form.ComboBox', {
xtype: 'combobox',
fieldLabel: 'Grupo',
store: grupo,
queryMode: 'local',
id:'idGrupo',
name: 'idGrupo',
valueField:'descripcion',
displayField:'descripcion',		  
width: 325,
triggerAction: 'all',
selectOnFocus:true,
lastQuery: '',
mode: 'local',
allowBlank: false,
listeners: {
        click: {
            element: 'el', //bind to the underlying el property on the panel
            fn: function(){ 
                if(grupo.getCount()==0)
                {
                    grupo.removeAll();
                    grupo.load();
                }
            }
        }			
    }
});

//CREAMOS DATA STORE PARA LOS ESTADOS
Ext.define('modelEstado', {
extend: 'Ext.data.Model',
fields: [
    {name: 'idestado', type: 'string'},
    {name: 'codigo',  type: 'string'},
    {name: 'descripcion',  type: 'string'}                    
]
});	
var estado_store = Ext.create('Ext.data.Store', {
autoLoad: false,
model: "modelEstado",
proxy: {
type: 'ajax',
url : url_estados,
reader: {
    type: 'json',
    root: 'estados'
        }
    }
});	
var estado_cmb = new Ext.form.ComboBox({
xtype: 'combobox',
store: estado_store,
labelAlign : 'left',
id:'idestado',
name: 'idestado',
valueField:'descripcion',
displayField:'descripcion',
fieldLabel: 'Estado',
width: 325,
triggerAction: 'all',
selectOnFocus:true,
lastQuery: '',
mode: 'local',
allowBlank: false,	
        
listeners: {
        select:
        function(e) {
            estado_id = Ext.getCmp('idestado').getValue();
        },
        click: {
            element: 'el', //bind to the underlying el property on the panel
            fn: function(){
                if(estado_store.getCount()==0)
                {
                    estado_store.removeAll();
                    estado_store.load();
                }
            }
        }			
    }
});


    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'idproducto', type: 'string'},
                {name:'codigo', type: 'string'},
                {name:'nombreTecnico', type: 'string'},
                {name:'descripcion', type: 'string'},
                {name:'tipo', type: 'string'},
                {name:'frecuencia', type: 'string'},
                {name:'estado', type: 'string'},
                {name:'linkVer', type: 'string'},
                {name:'linkEditar', type: 'string'},
                {name:'linkEliminar', type: 'string'},
                {name:'strRequiereComisionar', type: 'string'}
                ]
    }); 


    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
                pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: url_store_grid,
            reader: {
                type: 'json',
                root: 'productos',
                totalProperty: 'total'
            },
            extraParams:{fechaDesde:'',fechaHasta:'', estado:'',nombreTecnico:'',descripcion:'',strGrupo:''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store){
                store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();   
                store.getProxy().extraParams.nombreTecnico= Ext.getCmp('idNombreTecnico').getValue();  
                store.getProxy().extraParams.estado= Ext.getCmp('idestado').getValue();
                store.getProxy().extraParams.descripcion= Ext.getCmp('txtDescripcion').getValue();
                store.getProxy().extraParams.strGrupo= Ext.getCmp('idGrupo').getValue();
            },
            load: function(store){
                store.each(function(record) {
                    //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                });
            }
        }
    });

    store.load({params: {start: 0, limit: 10}});    



     sm = new Ext.selection.CheckboxModel( {
        listeners:{
            selectionchange: function(selectionModel, selected, options){
                arregloSeleccionados= new Array();
                Ext.each(selected, function(record){
                        //arregloSeleccionados.push(record.data.idOsDet);
        });			
                //console.log(arregloSeleccionados);

            }
        }
    });


    var listView = Ext.create('Ext.grid.Panel', {
        width:980,
        height:275,
        collapsible:false,
        title: '',
        selModel: sm,                  
        renderTo: Ext.get('lista_prospectos'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando productos {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),	
        store: store,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        listeners:{
                itemdblclick: function( view, record, item, index, eventobj, obj ){
                    var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                    Ext.Msg.show({
                        title:'Copiar texto?',
                        msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFORMATION
                    });
                }
        },
        columns: [new Ext.grid.RowNumberer(),  
                {
            text: 'Codigo',
            width: 70,
            dataIndex: 'codigo'
        },{
            text: 'Descripcion',
            width: 160,
            dataIndex: 'descripcion'
        },{
            text: 'Nombre Tecnico',
            width: 100,
            dataIndex: 'nombreTecnico'
        },{
            text: 'Tipo',
            dataIndex: 'tipo',
            align: 'center',
            width: 60
        },{
            text: 'Frecuencia',
            dataIndex: 'frecuencia',
            align: 'center',
            width: 80
        },
        {
            text: 'Estado',
            dataIndex: 'estado',
            align: 'center',
            width: 60
        },{
            text: 'Acciones',
            flex: 150,
            align: 'center',
            renderer: renderAcciones
        }]
    });

    var objPermiso = $("#ROLE_41-9");
    var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
    if (boolPermiso) {
        listView.addDocked({xtype: 'toolbar',
                            dock: 'top',
                            align: '->',
                            items: [
                                //tbfill -> alinea los items siguientes a la derecha
                                { xtype: 'tbfill' },
                                {
                                iconCls: 'icon_delete',
                                text: 'Eliminar',
                                disabled: false,
                                itemId: 'delete',
                                scope: this,
                                handler: function(){eliminarAlgunos();}
                               }]
        
        
        });
    } 
    else 
    {
        listView.addDocked({xtype: 'toolbar',
                            dock: 'top',
                            align: '->'
        });
    }

function renderAcciones(value, p, record) 
{
    var iconos          = '';
    var objPermiso      = null;
    var boolPermiso     = false;


    objPermiso = $("#ROLE_41-5297");
    boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
    if (boolPermiso && strPrefijoEmpresa == 'TN' && record.data.strRequiereComisionar == 'SI')
    {
    iconos = iconos + '<b><a href="#" onClick="verLogsPlantillaComision(' + record.data.idproducto + ')" \n\
                                title="Ver Historial de Plantilla de Comisiones" class="button-grid-logs"/></a></b>';
    }
            
    //iconos     = iconos + '<b><a href="' + record.data.linkEditar + '" onClick="" title="Ver" class="button-grid-edit"></a></b>';       
    iconos = iconos + '<b><a href="#" onClick="mostrarCaracteristicaComportamiento(' + record.data.idproducto + ')" \n\
                                title="Caracteristica Comportamiento" class="button-grid-edit"/></a></b>';


    return Ext.String.format(
    iconos,
    value,
    '1',
    'nada'
    );
}

Ext.create('Ext.panel.Panel', {
    bodyPadding: 7,  // Don't want content to crunch against the borders
    //bodyBorder: false,
    border:false,
    //border: '1,1,0,1',
    buttonAlign: 'center',
    layout:{
        type:'table',
        columns: 2,
        align: 'left',
    },
    bodyStyle: {
                background: '#fff'
            },

    collapsible : true,
    collapsed: false,
    width: 980,
    title: 'Criterios de busqueda',

        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: Buscar,
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function(){ limpiar();}
            }
            ],

            items: [
                    txtDescripcion,
                    {html:"&nbsp;",border:false,width:50},
                    cmbNombreTecnico,
                    estado_cmb,
                    DTFechaDesde,
                    DTFechaHasta,
                    cmbGrupo,
                    ],
    renderTo: 'filtro_prospectos'
});

});

function Buscar()
{
/*if  (( Ext.getCmp('fechaDesde').getValue()!=null)&&(Ext.getCmp('fechaHasta').getValue()!=null) )
{
    if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
    {
       Ext.Msg.show({
       title:'Error en Busqueda',
       msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
       buttons: Ext.Msg.OK,
       animEl: 'elId',
       icon: Ext.MessageBox.ERROR
            });		 

    }
    else
    {*/
            store.load({params: {start: 0, limit: 10}});
    /*}
}
else
{

    Ext.Msg.show({
    title:'Error en Busqueda',
    msg: 'Por Favor Ingrese criterios de fecha.',
    buttons: Ext.Msg.OK,
    animEl: 'elId',
    icon: Ext.MessageBox.ERROR
         });
}*/
}

function eliminar(direccion)
{
//alert(direccion);
Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
if(btn=='yes'){
    Ext.Ajax.request({
        url: direccion,
        method: 'post',
        success: function(response){
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

function eliminarAlgunos()
{
var param = '';
if(sm.getSelection().length > 0)
{
var estado = 0;
for(var i=0 ;  i < sm.getSelection().length ; ++i)
{
param = param + sm.getSelection()[i].data.idproducto;

if(sm.getSelection()[i].data.estado == 'Eliminado')
{
  estado = estado + 1;
}
if(i < (sm.getSelection().length -1))
{
  param = param + '|';
}
}      
if(estado == 0)
{
Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
    if(btn=='yes'){
        Ext.Ajax.request({
            url: url_eliminar,
            method: 'post',
            params: { param : param},
            success: function(response){
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

function limpiar()
{
Ext.getCmp('fechaDesde').setRawValue("");
Ext.getCmp('fechaHasta').setRawValue("");
Ext.getCmp('idestado').setRawValue("");
Ext.getCmp('txtDescripcion').setRawValue("");
Ext.getCmp('idGrupo').setRawValue("");
Ext.getCmp('idNombreTecnico').setRawValue("");

store.removeAll();
store.load({params: {start: 0, limit: 10}});
}

function verLogsPlantillaComision(data)
{
    var storeHistorial = new Ext.data.Store({
    pageSize: 50,
    autoLoad: true,
    proxy: {
    type: 'ajax',
    url: url_gridLogsPlantillaComision,
    reader: {
        type: 'json',
        totalProperty: 'total',
        root: 'encontrados'
    },
    extraParams: {
        intIdProducto: data
    }
    },
    fields:
    [
        {name: 'strUsrCreacion', mapping: 'strUsrCreacion'},
        {name: 'strFeCreacion', mapping: 'strFeCreacion'},
        {name: 'strIpCreacion', mapping: 'strIpCreacion'},
        {name: 'strGrupoRol', mapping: 'strGrupoRol'},
        {name: 'strEstado', mapping: 'strEstado'},                
        {name: 'strObservacion', mapping: 'strObservacion'}                
    ]
    });

    Ext.define('HistorialPlantilla', {
    extend: 'Ext.data.Model',
    fields: [
    {name: 'strUsrCreacion', mapping: 'strUsrCreacion'},
    {name: 'strFeCreacion', mapping: 'strFeCreacion'},
    {name: 'strIpCreacion', mapping: 'strIpCreacion'},
    {name: 'strGrupoRol', mapping: 'strGrupoRol'},
    {name: 'strEstado', mapping: 'strEstado'},            
    {name: 'strObservacion', mapping: 'strObservacion'}            
    ]
    });

    //Grid Historial de Plantilla de comisionistas
    gridHistorialPlantilla = Ext.create('Ext.grid.Panel',
    {
    id: 'gridHistorialPlantilla',
    store: storeHistorial,
    columnLines: true,
    listeners:
        {
            viewready: function(grid)
            {
                var view = grid.view;

                grid.mon(view,
                    {
                        uievent: function(type, view, cell, recordIndex, cellIndex, e)
                        {
                            grid.cellIndex = cellIndex;
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
                                        header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                        if (header.dataIndex != null)
                                        {
                                            var trigger = tip.triggerElement,
                                                parent = tip.triggerElement.parentElement,
                                                columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                            if (view.getRecord(parent).get(columnDataIndex) != null)
                                            {
                                                var columnText = view.getRecord(parent).get(columnDataIndex).toString();

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
                        timeout = window.setTimeout(function() {
                            grid.tip.hide();
                        }, 500);
                    });

                    grid.tip.getEl().on('mouseover', function() {
                        window.clearTimeout(timeout);
                    });

                    Ext.get(view.el).on('mouseover', function() {
                        window.clearTimeout(timeout);
                    });

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function() {
                            grid.tip.hide();
                        }, 500);
                    });
                });
            }
        },
    columns:
        [
            {
                header: 'Usuario Creación',
                dataIndex: 'strUsrCreacion',
                width: 100,
                sortable: true
            }, {
                header: 'Fecha Creación',
                dataIndex: 'strFeCreacion',
                width: 120
            },
            {
                header: 'Ip Creación',
                dataIndex: 'strIpCreacion',
                width: 100
            },
            {
                header: 'Grupo Rol',
                dataIndex: 'strGrupoRol',
                width: 150
            },
            {
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 100
            },                    
            {
                header: 'Observación',
                dataIndex: 'strObservacion',
                width: 350
            }
        ],
    viewConfig:
        {
            stripeRows: true,
            enableTextSelection: true
        },
    frame: true,
    height: 300
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
        defaults: {
            width: 950
        },
        items: [
            gridHistorialPlantilla

        ]
    }
    ],
    buttons: [{
        text: 'Cerrar',
        handler: function() {
            win.destroy();
        }
    }]
    });

    var win = Ext.create('Ext.window.Window', {
    title: 'Historial de la Plantilla de Comisionistas',
    modal: true,
    width: 1000,
    closable: true,
    layout: 'fit',
    items: [formPanel]
    }).show();
}

function mostrarCaracteristicaComportamiento(intIdPrdocuto)
{
    var dataStoreCaracComp = new Ext.data.Store({
        id: 'verCaracteristicaComportamientoStore',
        total: 'total',
        pageSize: 50,
        autoLoad: true,
        proxy:
            {
                type: 'ajax',
                url: url_store_grid_comp,
                reader:
                    {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'caractComp'
                    },
                extraParams:{idProducto: intIdPrdocuto},
                simpleSortMode: true
            },
        fields:
            [
                {name:'idProdCaracComp', type: 'string'},
                {name:'idProductoCaracteristica', type: 'string'},
                {name:'caracteristicaId', type: 'string'},
                {name:'descripcionCaracteristica', type: 'string'},
                {name:'tipoIngreso', type: 'string'},
                {name:'esVisible', type: 'bool'},
                {name:'editable', type: 'bool'},
                {name:'valoresSeleccionable', type: 'string'},
                {name:'valoresDefault', type: 'string'},
                {name:'estado', type: 'string'}
            ]
    }); 

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
        {
            clicksToEdit: 1
        });

    var connGuardandoDatos = new Ext.data.Connection(
        {
            listeners:
                {
                    'beforerequest':
                        {
                            fn: function(con, opt)
                            {
                                winDocEntregable.hide();
                                Ext.MessageBox.show(
                                    {
                                        msg: 'Grabando los datos, Por favor espere!!',
                                        progressText: 'Grabando...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {interval: 0}
                                    });
                            },
                            scope: this
                        },
                    'requestcomplete':
                        {
                            fn: function()
                            {
                                Ext.MessageBox.hide();
    
                            },
                            scope: this
                        },
                    'requestexception':
                        {
                            fn: function()
                            {
                                Ext.MessageBox.hide();
                            },
                            scope: this
                        }
                }
        });

    var gridCaractComportamiento = Ext.create('Ext.grid.Panel',
        {
            id: 'gridCaractComportamiento',
            store: dataStoreCaracComp,
            timeout: 60000,
            dockedItems:
                [{
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items: [{xtype: 'tbfill'}]
                    }],
            viewConfig: {stripeRows: true},
            columnLines: true,
            buttons:
                [
                    {
                        text: 'Guardar',
                        iconCls: "iconSave",
                        handler: function()
                        {
                            var arrayCarComp = [];
                            dataStoreCaracComp.each(function(record)
                            {
                                arrayCarComp.push(record.data);

                            }, this);
                            connGuardandoDatos.request
                                (
                                    {
                                        url: urlGuardarCaracComp,
                                        method: 'post',
                                        dataType: 'json',
                                        params:
                                            {
                                                jsonEntregables: Ext.encode(arrayCarComp)
                                            },
                                        success: function(response)
                                        {
                                            var msg = Ext.decode(response.responseText);
                                            if (msg.ESTADO === 'OK')
                                            {
                                                Ext.Msg.alert('Informaci\xf3n', 'Se ha actualizados correctamente el comportamiento de las caracteristica.', function()
                                                {
                                                    winDocEntregable.close();
                                                    store.load();
                                                });
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error', msg.ERROR, function()
                                                {
                                                    winDocEntregable.show();
                                                });
                                            }

                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error', result.responseText, function()
                                            {
                                                winDocEntregable.show();
                                            });
                                        }
                                    }
                                );
                        }
                    },
                    {
                        text: 'Cancelar',
                        iconCls: "icon_cerrar",
                        handler: function()
                        {
                            winDocEntregable.close();
                        }
                    }
                ],
            buttonAlign: 'center',
            plugins: [cellEditing],
            columns: [
                {
                    id: 'idProductoCaracteristica',
                    header: 'Id Producto Caracteristica',
                    dataIndex: 'idProductoCaracteristica',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'idProdCaracComp',
                    header: 'Id Producto Caracteristica Comportamiento',
                    dataIndex: 'idProdCaracComp',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'caracteristicaId',
                    header: 'Caracteristica Id',
                    dataIndex: 'caracteristicaId',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'descripcionCaracteristica',
                    header: 'Descripcion Caracteristica',
                    dataIndex: 'descripcionCaracteristica',
                    sortable: false,
                    width: 160
                },
                {
                    xtype: 'checkcolumn',
                    header: 'Es Visible?',
                    dataIndex: 'esVisible',
                    width: 60,
                    editor:
                        {
                            xtype: 'checkbox',
                            cls: 'x-grid-checkheader-editor'
                        },
                    stopSelection: false
                },
                {
                    xtype: 'checkcolumn',
                    header: 'Editable',
                    dataIndex: 'editable',
                    width: 60,
                    editor:
                        {
                            xtype: 'checkbox',
                            cls: 'x-grid-checkheader-editor'
                        },
                    stopSelection: false
                },
                {
                    id: 'valoresSeleccionable',
                    header: 'Valores Seleccionable',
                    dataIndex: 'valoresSeleccionable',
                    sortable: false,
                    width: 180,
                    editor: 'textfield'
                },
                {
                    id: 'valoresDefault',
                    header: 'Valores Por Default',
                    dataIndex: 'valoresDefault',
                    sortable: false,
                    width: 160,
                    editor: 'textfield'
                },
                {
                    id: 'estado',
                    header: 'Estado',
                    dataIndex: 'estado',
                    sortable: false,
                    width: 60,
                    editor: 'textfield'
                }
            ]
        });


    var formCaracComp = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
        labelAlign: 'above',
        labelWidth: 85,
        msgTarget: 'side'
        },
        items: [
        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',
            defaults: {
                height: 420,
                width: 730,
            },
            items: [
                { 
                    xtype: 'label',
                    html: '<b> Nota: </b> El campo Valores Selecionable el ingreso de valores debe estar separado por el caracter <b>;</b> <i> <br> Ejemplo: valor1;valor2;valor3</i> &#8595;',
                    margin: '0 0 20 0',
                },
                gridCaractComportamiento
            ]
        }
        ]
    });

    winDocEntregable = Ext.create('Ext.window.Window',
        {
            title: 'Caracteristica Comportamiento - Producto ',
            height: 490,
            width: 730,
            modal: true,
            layout:
                {
                    type: 'fit',
                    align: 'stretch',
                    pack: 'start'
                },
            floating: true,
            shadow: true,
            shadowOffset: 20,
            items: [formCaracComp]
        });

    winDocEntregable.show();
}
