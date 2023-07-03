Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*'
]);

Ext.onReady(function () {

    Ext.define('ListaActividadesModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'usuario', type: 'string'},
		{name:'tipoActividad', type: 'string'},	
		{name:'cliente', type: 'string'},	
		{name:'puntoCliente', type: 'string'},			
		{name:'descripcionActividad', type: 'string'},
		{name:'fechaCreacion', type: 'string'}
		]
    });
    var storeListaActividades = Ext.create('Ext.data.JsonStore', {
        model: 'ListaActividadesModel',
        proxy: {
            type: 'ajax',
            url: dsh_ultimasActividades,
            reader: {
                type: 'json',
                root: 'actividades'
            }
        }
    });
    storeListaActividades.load();
	Ext.QuickTips.init();
    var listView = Ext.create('Ext.grid.Panel', {
        width:1070,
        height:280,
        collapsible:false,
		title: 'Ultimas actividades ingresadas',
        renderTo: Ext.get('ultimas_actividades'),
        store: storeListaActividades,
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
            flex: 35,
            dataIndex: 'fechaCreacion'
        },{
            text: 'Tipo',
            dataIndex: 'tipoActividad',
            align: 'right',
            flex: 70
        },{
            text: 'Actividad',
            dataIndex: 'descripcionActividad',
            align: 'right',
            flex: 60,
			renderer: function(value,metaData,record,colIndex,store,view) {
			metaData.tdAttr = 'data-qtip="' + value+'"';
			return value;
			}
        },{
            text: 'Cliente',
            dataIndex: 'cliente',
            align: 'right',
            flex: 70
        },{
            text: 'Punto Cliente',
            dataIndex: 'puntoCliente',
            align: 'right',
            flex: 70
        },{
            text: 'Usuario',
            flex: 25,
            dataIndex: 'usuario'
        }]
    });
	
	
	


});