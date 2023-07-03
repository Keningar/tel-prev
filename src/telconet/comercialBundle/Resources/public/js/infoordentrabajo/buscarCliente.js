
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*'
]);

var winListaServicios;
var formServicios;
var filterPanel;
var itemsPerPage = 10;
var currentTime = new Date()
var month = currentTime.getMonth() + 1
var day = currentTime.getDate()
var year = currentTime.getFullYear()
var fechaHoy = year + "-" + month + "-" + (day+1);
function showClientes(){

winListaServicios="";
formServicios = '';
filterPanel = '';
if ((!winListaServicios)&&(!filterPanel))
{
   TFNombre = new Ext.form.TextField({
                    name: 'nombreCliente',
                    fieldLabel: 'Nombre',
                    xtype: 'textfield'
            });
            
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'id', type: 'int'},
                 {name:'login', type: 'string'},
                 {name:'descripcionPunto', type: 'string'},
                 {name:'razonSocial', type: 'string'},
                 {name:'nombres', type: 'string'},
                 {name:'apellidos', type: 'string'}
		]
    });
    var storeClientes = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: url_clientes,
            reader: {
                type: 'json',
                root: 'listado_ptos',
                totalProperty: 'total'
            },
            extraParams:{nombre:'',fechaDesde:'1930-01-01',fechaHasta:fechaHoy},
            simpleSortMode: true            
        },
                    listeners: {
                        beforeload: function(store){  
                                //storeClientes.getProxy().extraParams.nombre= Ext.getCmp('nombre').getValue();   
                                storeClientes.getProxy().extraParams.nombre= Ext.ComponentQuery.query('textfield[name=nombreCliente]')[0].value;   
                        }
                    }	
    });
    storeClientes.load({params: {start: 0, limit: 10}}); 

   
    var listView = Ext.create('Ext.grid.Panel', {
        width:500,
        height:200,
        collapsible:false,
        title: '',
        renderTo: Ext.getBody(),
        store: storeClientes,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
                        store: storeClientes,
                        displayInfo: true,
                        displayMsg: 'Mostrando clientes {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
        }),
        columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Razon Social',
                        width: 110,
                        dataIndex: 'razonSocial'
                    },{
                        text: 'Nombres',
                        width: 80,
                        dataIndex: 'nombres'
                    },{
                        text: 'Apellidos',
                        dataIndex: 'apellidos',
                        align: 'right',
                        width: 80			
                    },{
                        text: 'Login',
                        dataIndex: 'login',
                        align: 'right',
                        width: 110			
                    },{
                        text: 'Descripcion pto',
                        dataIndex: 'descripcionPunto',
                        align: 'right',
                        width: 110			
                    }
		],
                listeners: {
                    itemdblclick:{
                        fn: function( view, rec, node, index, e, options ){
                            $('#puntoid').val(rec.data.id);
							$('#punto').val(rec.data.login);
                            cierraVentana();
                        }
                        

                    } 
                    
                }
                
    });

            
   filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                //bodyBorder: false,
                border:false,
                //border: '1,1,0,1',
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 5,
                    align: 'left'
                },
                bodyStyle: {
                            background: '#fff'
                        },                     
                defaults: {
                    // applied to each contained panel
                    bodyStyle: 'padding:10px'
                },
                collapsible : true,
                collapsed: true,
                width: 800,
                title: 'Criterios de busqueda',
                
                    buttons: [                   
                        {
                            text: 'Buscar',
                            iconCls: "icon_search",
                            handler: Buscar
                        }
                        ],                

                        items: [
                                TFNombre
                                ]	
                //renderTo: 'filtro_prospectos'
            }); 
            
   formServicios = Ext.widget('form', {
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                border: false,
                bodyPadding: 10,

                fieldDefaults: {
                    labelAlign: 'top',
                    labelWidth: 100,
                    labelStyle: 'font-weight:bold'
                },
                defaults: {
                    margins: '0 0 10 0'
                },
                items: [			
			filterPanel,listView			
		]
            });

	winListaServicios = Ext.widget('window', {
                title: 'Listado de Ptos. Clientes',
                closeAction: 'hide',
                width: 510,
                height:380,
                minHeight: 380,
                layout: 'fit',
                resizable: false,
                modal: true,
		closabled: false,
                items: [formServicios]	
    });
	
}
winListaServicios.show();

function cierraVentana(){
    winListaServicios.close();
    
}
	function Buscar(){
		//if  (Ext.getCmp('nombre').getValue()!=null)
                if(Ext.ComponentQuery.query('textfield[name=nombreCliente]')[0].value!=null)
		{

			storeClientes.load({params: {start: 0, limit: 10}});
			
		}
		else
		{

			Ext.Msg.show({
			   title:'Error en Busqueda',
			   msg: 'Por Favor Ingrese un nombre para buscar',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.ERROR
                        });
		}	
	}
}

function validate(evt) {
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	if ((key < 48 || key > 57) && !(key == 8 || key == 9 || key == 13 || key == 37 || key == 39 || key == 46) )
	{
		theEvent.returnValue = false;
		if (theEvent.preventDefault) 
			theEvent.preventDefault();
	}
}

