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
                   fill: 'rgb(8,148,148)',
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
    Ext.define('datosPtoCoberturaModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'name', type: 'string'},
		{name:'data1', type: 'string'}
		]
    });
    var storeDatosPtosClientePtoCobertura = Ext.create('Ext.data.JsonStore', {
        model: 'datosPtoCoberturaModel',
        proxy: {
            type: 'ajax',
            url: dsh_bar_ptos_cliente_por_pto_cobertura_mes,
            reader: {
                type: 'json',
                root: 'puntosCobertura'
            }
        }
    });	
storeDatosPtosClientePtoCobertura.load();
	

    var panelPtosCobertura = Ext.create('widget.panel', {
        width: 440,
        height: 300,
        title: 'Ptos. Cliente por Pto. cobertura activados del mes',
        renderTo:  Ext.get('clientes_por_pto_cobertura'),
        layout: 'fit',
        tbar: [{
            text: 'Actualizar',
            handler: function() {
                storeDatosPtosClientePtoCobertura.load();
            }
        }],
        items: {
            xtype: 'chart',
            animate: true,
            shadow: true,
            store: storeDatosPtosClientePtoCobertura,
            axes: [{
                type: 'Numeric',
                position: 'left',
                fields: ['data1'],
                title: 'Ptos. Cliente',
                grid: true,
                minimum: 0,
                maximum: 100
            }, {
                type: 'Category',
                position: 'bottom',
                fields: ['name'],
                title: 'Ptos. Cobertura',
                label: {
                    rotate: {
                        degrees: 270
                    }
                }
            }],
            series: [{
                type: 'column',
                axis: 'left',
                gutter: 80,
                xField: 'name',
                yField: ['data1'],
                //color renderer
                renderer: function(sprite, record, attr, index, store) {
                    //var fieldValue = Math.random() * 20 + 10;
                    var value = (record.get('data1') >> 0) % 5;
                    var color = ['rgb(213, 70, 121)', 
                                 'rgb(44, 153, 201)', 
                                 'rgb(146, 6, 157)', 
                                 'rgb(49, 149, 0)', 
                                 'rgb(249, 153, 0)'][value];
                    return Ext.apply(attr, {
                        fill: color
                    });
                },                
                tips: {
                    trackMouse: true,
                    width: 74,
                    height: 38,
                    renderer: function(storeItem, item) {
                        this.setTitle(storeItem.get('name') + '<br />' + storeItem.get('data1'));
                    }
                },
                style: {
                    fill: '#38B8BF'
                }
            }]
        }
    });	
	
});