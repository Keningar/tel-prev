Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*'
]);

Ext.onReady(function () {

    Ext.define('ListaClientesModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'nombre', type: 'string'},
		{name:'identificacion', type: 'string'},	
		{name:'direccion', type: 'string'},	
		{name:'fechaCreacion', type: 'string'},
		{name:'usuario', type: 'string'}
		]
    });
    var storeListaClientes = Ext.create('Ext.data.JsonStore', {
        model: 'ListaClientesModel',
        proxy: {
            type: 'ajax',
            url: dsh_ultimosClientesActivos,
            reader: {
                type: 'json',
                root: 'clientes'
            }
        }
    });
    storeListaClientes.load();
	Ext.QuickTips.init();
    var listView = Ext.create('Ext.grid.Panel', {
        width:530,
        height:280,
        collapsible:false,
		title: 'Ultimos Clientes Activados',
        renderTo: Ext.get('ultimos_clientes_activos'),
        store: storeListaClientes,
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
        columns: [{
            text: 'Fecha',
            flex: 60,
            dataIndex: 'fechaCreacion'
        },{
            text: 'Cliente',
            dataIndex: 'nombre',
            align: 'right',
            flex: 70,
			renderer: function(value,metaData,record,colIndex,store,view) {
			metaData.tdAttr = 'data-qtip="' + value+'"';
			return value;
			}
        },{
            text: 'No. Identificacion',
            dataIndex: 'identificacion',
            align: 'right',
            flex: 60
        },{
            text: 'Direccion',
            dataIndex: 'direccion',
            align: 'right',
            flex: 70,
			renderer: function(value,metaData,record,colIndex,store,view) {
			metaData.tdAttr = 'data-qtip="' + value+'"';
			return value;
			}
        },{
            text: 'Usuario',
            flex: 50,
            dataIndex: 'usuario'
        }]
    });
	
	
	


});