	function validarTarjetaCuenta() {
		//llamada ajax para verificar que la cuenta/tarjeta sea valida
		var tipoCuentaId = document.getElementById('infocontratoformapagotype_tipoCuentaId').value;
		var bancoTipoCuentaId = document.getElementById('infocontratoformapagotype_bancoTipoCuentaId').value;
		var numeroCtaTarjeta = document.getElementById('infocontratoformapagotype_numeroCtaTarjeta').value;
		var codigoVerificacion = document.getElementById('infocontratoformapagotype_codigoVerificacion').value;
		if (!isNaN(bancoTipoCuentaId) && parseInt(bancoTipoCuentaId) == bancoTipoCuentaId)	{	
			Ext.Ajax.request({
				url: url_validarTarjetaCta,
				method: 'post',
				params: { 
					tipoCuentaId : tipoCuentaId,
					bancoTipoCuentaId: bancoTipoCuentaId,
					numeroCtaTarjeta: numeroCtaTarjeta,
					codigoVerificacion: codigoVerificacion
				},
				success: function(response){
					
					var json = Ext.JSON.decode(response.responseText);
					if (json.valida == 1)
						flagTtarjetaValida = 1;
					else {
						flagTtarjetaValida = 0;
						alert(json.msg);
					}
				},
				failure: function(result)
				{
					flagTtarjetaValida = 0;
					Ext.Msg.alert('Error ','Error: ' + result.statusText);
				}
			});
		}			
	}
	
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*'
]);

var winListaServicios;
var formServicios;
var filterPanel;
var storeServicios;
var itemsPerPage = 10;
var currentTime = new Date()
var month = currentTime.getMonth() + 1
var day = currentTime.getDate()
var year = currentTime.getFullYear()
var fechaHoy = year + "-" + month + "-" + (day+1);
function showClientes(input1,input2,titulo,rootstore){

winListaServicios="";
formServicios = '';
filterPanel = '';
storeServicios='';
if ((!winListaServicios)&&(!filterPanel))
{
    TFNombre='';
    
   TFNombre = new Ext.form.TextField({
                    name: 'nombreCliente',
                    fieldLabel: 'Nombre',
                    xtype: 'textfield'
            });    
    
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'idPersona', type: 'int'},
                 {name:'Nombre', type: 'string'},
                 {name:'Direccion', type: 'string'}
		]
    });
    storeServicios = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: url_clientes,
            reader: {
                type: 'json',
                root: rootstore,
                totalProperty: 'total'
            },
            extraParams:{nombre:'',fechaDesde:'1930-01-01',fechaHasta:fechaHoy},
            simpleSortMode: true 
        },
        listeners: {
            beforeload: function(store){  
                    //storeClientes.getProxy().extraParams.nombre= Ext.getCmp('nombre').getValue();   
                    storeServicios.getProxy().extraParams.nombre= Ext.ComponentQuery.query('textfield[name=nombreCliente]')[0].value;   
            }
        }	 	
    });
    storeServicios.load({params: {start: 0, limit: 10}});

   
    var listView = Ext.create('Ext.grid.Panel', {
        width:500,
        height:300,
        collapsible:false,
        title: '',
        renderTo: Ext.getBody(),
        store: storeServicios,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
                        store: storeServicios,
                        displayInfo: true,
                        displayMsg: 'Mostrando '+titulo+' {0} - {1} of {2}',
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
                        width: 190			
                    }
		],
                listeners: {
                    itemdblclick:{
                        fn: function( view, rec, node, index, e, options ){
                            $(input2).val(rec.data.idPersona);
                            $(input1).val(rec.data.Nombre);  
                            cierraVentana();
                        }
                        

                    } 
                    
                }
                
    });
   filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,
                border:false,
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
            title: titulo,
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
    formServicios.destroy();
    
}

function Buscar(){
        //if  (Ext.getCmp('nombre').getValue()!=null)
        if(Ext.ComponentQuery.query('textfield[name=nombreCliente]')[0].value!=null)
        {

                storeServicios.load({params: {start: 0, limit: 10}});

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

