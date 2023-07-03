Ext.require('Ext.chart.*');
Ext.require('Ext.layout.container.Fit');

Ext.onReady(function () {

    Ext.define('tiposNegocioModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'name', type: 'string'},
		{name:'data1', type: 'string'}
		]
    });
    var storePtosClientePorTipoNegocio = Ext.create('Ext.data.JsonStore', {
        model: 'tiposNegocioModel',
        proxy: {
            type: 'ajax',
            url: dsh_pie_ptos_cliente_por_tipos_negocio,
            reader: {
                type: 'json',
                root: 'tiposNegocio'
            }
        }
    });	

    storePtosClientePorTipoNegocio.load();

    var donut = false;
    var panel2 = Ext.create('widget.panel', {
        width: 600,
        height: 300,
        title: 'Porcentaje puntos cliente por Tipos de negocio',
        renderTo:  Ext.get('clientes_por_tipos_negocio'),
        layout: 'fit',
        tbar: [{
            text: 'Actualizar',
            handler: function() {
                storePtosClientePorTipoNegocio.load();
            }
        }, {
            enableToggle: true,
            pressed: false,
            text: 'Efecto Dona',
            toggleHandler: function(btn, pressed) {
                var chart = Ext.getCmp('chartCmp');
                chart.series.first().donut = pressed ? 35 : false;
                chart.refresh();
            }
        }],
        items: {
            xtype: 'chart',
            animate: true,
            store: storePtosClientePorTipoNegocio,
            shadow: true,
            legend: {
                position: 'right'
            },
            insetPadding: 20,
            theme: 'Base:gradients',
            series: [{
                type: 'pie',
                field: 'data1',
                showInLegend: true,
                donut: donut,
                tips: {
                  trackMouse: true,
                  width: 140,
                  height: 28,
                  renderer: function(storeItem, item) {
                    //calculate percentage.
                    var total = 0;
                    storePtosClientePorTipoNegocio.each(function(rec) {
                        total += (rec.get('data1') * 1);	
                    });
					var porcentaje=Math.round((storeItem.get('data1') * 100) / total);
                    this.setTitle( storeItem.get('name') + ': ' + porcentaje + '% ('+storeItem.get('data1')+' puntos cliente)');
					//this.setTitle(storeItem.get('name') + ': ' + storeItem.get('data1'));
					
					//console.log(storeItem.get('data1') + ' * 100 / ' + total);
                  }
                },
                highlight: {
                  segment: {
                    margin: 20
                  }
                },
                label: {
                    field: 'name',
                    display: 'rotate',
                    contrast: true,
                    font: '12px Arial'
                }
            }]
        }
    });
});