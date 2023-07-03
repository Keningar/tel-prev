Ext.require('Ext.chart.*');
Ext.require(['Ext.Window', 'Ext.fx.target.Sprite', 'Ext.layout.container.Fit']);

Ext.onReady(function () {
    Ext.chart.theme.White = Ext.extend(Ext.chart.theme.Base, {
        constructor: function() {
           Ext.chart.theme.White.superclass.constructor.call(this, {
               axis: {
                   stroke: 'rgb(8,69,148)',
                   'stroke-width': 1
               },
               axisLabel: {
                   fill: 'rgb(8,69,148)',
                   font: '12px Arial',
                   'font-family': '"Arial',
                   spacing: 2,
                   padding: 5,
                   renderer: function(v) { return v; }
               },
               axisTitle: {
                  font: 'bold 18px Arial'
               }
           });
        }
    });

    Ext.define('datosModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'name', type: 'string'},
		{name:'data1', type: 'string'}
		]
    });
    var storeDatosServiciosPorProductos = Ext.create('Ext.data.JsonStore', {
        model: 'datosModel',
        proxy: {
            type: 'ajax',
            url: dsh_bar_servicios_por_productos_mes,
            reader: {
                type: 'json',
                root: 'productos'
            }
        }
    });	
storeDatosServiciosPorProductos.load();

    var win = Ext.create('widget.panel', {
	//var win = Ext.create('Ext.Window', {
        width: 600,
        height: 300,
        /*hidden: false,
        maximizable: false,
		closable: false,
		draggable: false,*/
        title: 'Productos activados del mes',
        //renderTo: Ext.getBody(),
		renderTo:  Ext.get('servicios_por_productos'),
        layout: 'fit',
        tbar: [{
            text: 'Actualizar',
            handler: function() {
				//datos=generateData();
				//console.log(generateData());                
				//storeDatosServiciosPorProductos.loadData(datos);
				storeDatosServiciosPorProductos.load();
            }
        }],
        items: {
            id: 'chartCmp',
            xtype: 'chart',
            animate: true,
            shadow: true,
            store: storeDatosServiciosPorProductos,
            axes: [{
                type: 'Numeric',
                position: 'bottom',
                fields: ['data1'],
                label: {
                    renderer: Ext.util.Format.numberRenderer('0,0')
                },
                title: 'Cantidad activados',
                grid: true,
                minimum: 0
            }, {
                type: 'Category',
                position: 'left',
                fields: ['name'],
                title: 'Productos'
            }],
            theme: 'White',
            background: {
              gradient: {
                id: 'backgroundGradient',
                angle: 45,
                stops: {
                  0: {
                    color: '#ffffff'
                  },
                  100: {
                    color: '#eaf1f8'
                  }
                }
              }
            },
            series: [{
                type: 'bar',
                axis: 'bottom',
                highlight: true,
                tips: {
                  trackMouse: true,
                  width: 140,
                  height: 28,
                  renderer: function(storeItem, item) {
                    this.setTitle(storeItem.get('name') + ': ' + storeItem.get('data1') + ' activados');
                  }
                },
                label: {
                  display: 'insideEnd',
                    field: 'data1',
                    renderer: Ext.util.Format.numberRenderer('0'),
                    orientation: 'horizontal',
                    color: '#333',
                  'text-anchor': 'middle'
                },
                xField: 'name',
                yField: ['data1']
            }]
        }
    });
});