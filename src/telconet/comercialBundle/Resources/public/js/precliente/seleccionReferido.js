
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
var fechaHoy = year + "-" + month + "-" + (day+2);
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
   TFApellido = new Ext.form.TextField({
                    name: 'apellidoCliente',
                    fieldLabel: 'Apellido',
                    xtype: 'textfield'
            });            
   TFRazonSocial = new Ext.form.TextField({
                    name: 'razonSocial',
                    fieldLabel: 'Razon Social',
                    xtype: 'textfield'
            }); 			
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'idPersona', type: 'int'},
                 {name:'idPersonaEmpresaRol', type: 'int'},
                 {name:'RazonSocial', type: 'string'},
                 {name:'Nombre', type: 'string'},
                 {name:'Apellidos', type: 'string'},
                 {name:'Direccion', type: 'string'}
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
                root: 'clientes',
                totalProperty: 'total'
            },
            extraParams:{nombre:'',apellido:'',razonSocial:'',fechaDesde:'T',fechaHasta:'T'},
            simpleSortMode: true            
        },
        listeners: {
            beforeload: function(store){  
                    //storeClientes.getProxy().extraParams.nombre= Ext.getCmp('nombre').getValue();   
                    storeClientes.getProxy().extraParams.nombre= Ext.ComponentQuery.query('textfield[name=nombreCliente]')[0].value;   
					storeClientes.getProxy().extraParams.apellido= Ext.ComponentQuery.query('textfield[name=apellidoCliente]')[0].value;   
					storeClientes.getProxy().extraParams.razonSocial= Ext.ComponentQuery.query('textfield[name=razonSocial]')[0].value;   
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
                        text: 'Nombres',
                        width: 220,
                        dataIndex: 'Nombre'
                    },{
                        text: 'Direccion',
                        dataIndex: 'Direccion',
                        align: 'right',
                        width: 220			
                    }
		],
                listeners: {
                    itemdblclick:{
                        fn: function( view, rec, node, index, e, options ){
                            console.log(rec.data);
                            $('#preclientetype_idreferido').val(rec.data.idPersona);
                            $('#preclientetype_idperreferido').val(rec.data.idPersonaEmpresaRol);
                            $('#preclientetype_referido').val(rec.data.Nombre);
                            
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
                                TFNombre,
								TFApellido,
								TFRazonSocial
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
                title: 'Clientes',
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

