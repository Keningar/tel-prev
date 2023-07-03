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
    Ext.define('datosInstalacionesModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'name', type: 'string'},
		{name:'data1', type: 'string'}
		]
    });
    var storeDatosInstalacionesAproRechaz = Ext.create('Ext.data.JsonStore', {
        model: 'datosInstalacionesModel',
        proxy: {
            type: 'ajax',
            url: dsh_bar_instalaciones_aprob_rechazadas_mes,
            reader: {
                type: 'json',
                root: 'instalaciones'
            }
        }
    });	
storeDatosInstalacionesAproRechaz.load();

    var panelInstalaciones = Ext.create('widget.panel', {
        width: 450,
        height: 400,
        title: 'Instalaciones Aprobadas y Rechazadas del mes',
        renderTo:  Ext.get('instalacionesAprobRechaz'),
        layout: 'fit',
        tbar: [{
            text: 'Actualizar',
            handler: function() {
                storeDatosInstalacionesAproRechaz.load();
            }
        }],
        items: {
            xtype: 'chart',
            animate: true,
            shadow: true,
            store: storeDatosInstalacionesAproRechaz,
            axes: [{
                type: 'Numeric',
                position: 'left',
                fields: ['data1'],
                title: 'Cantidad',
                grid: true,
                minimum: 0,
		       label: {
		            renderer: Ext.util.Format.numberRenderer('0,0')
		        }			
             //   maximum: 100
            }, {
                type: 'Category',
                position: 'bottom',
                fields: ['name'],
                title: 'Estados',
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
                tips: {
                    trackMouse: true,
                    width: 100,
                    height: 38,
                    renderer: function(storeItem, item) {
                        this.setTitle(storeItem.get('name') + ': ' + storeItem.get('data1'));
                    }
                },
                style: {
                    fill: '#38B8BF'
                },
                listeners:{
                        'itemclick': function(item){
                                var chartPie = Ext.getCmp('chartCmp');
                                if (item.storeItem.data.name=='Aprobadas'){
                                 storeInstalacionesRechazadas.proxy.url = dsh_pie_instalaciones_aprobadas;
                                 panel2.setTitle("Instalaciones Aprobadas en el mes");
                                }else{
                                    if (item.storeItem.data.name=='Rechazadas'){
                                             storeInstalacionesRechazadas.proxy.url = dsh_pie_instalaciones_rechazadas;
                                             panel2.setTitle("Instalaciones Rechazadas en el mes");
                                    }
                                }
                                storeInstalacionesRechazadas.load();
                                storeInstalacionesRechazadas.load();
                                storeInstalacionesRechazadas.load();
                                //console.log(chartPie.series.items[0]);
                                // get the correct serie
                                //var serie = chart.series.items[0];

                                // remove the serie from the chart
                                //chart.series.remove(serie); 
                                
                                chartPie.redraw();
                                chartPie.redraw();
                                chartPie.refresh();

                        }
                }
            }]
        }
    });	



	
	
    Ext.define('instalacionesRechazadasModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'name', type: 'string'},
		{name:'data1', type: 'string'}
		]
    });
    var storeInstalacionesRechazadas = Ext.create('Ext.data.JsonStore', {
        model: 'instalacionesRechazadasModel',
        proxy: {
            type: 'ajax',
            url: dsh_pie_instalaciones_aprobadas,
            reader: {
                type: 'json',
                root: 'instalaciones'
            }
        }
    });	

    storeInstalacionesRechazadas.load();

    //var donut = false,
        panel2 = Ext.create('widget.panel', {
        width: 650,
        height: 400,
        title: 'Instalaciones Rechazadas en el mes',
        renderTo:  Ext.get('instalacionesRechazadas'),
        layout: 'fit',
        items: {
            xtype: 'chart',
            id: 'chartCmp',
            animate: true,
            store: storeInstalacionesRechazadas,
            shadow: true,
            legend: {
                position: 'right'
            },
            insetPadding: 5,
            theme: 'Base:gradients',
            series: [{
                type: 'pie',
                field: 'data1',
                id: 'serie1',
                showInLegend: true,
                donut: false,
                tips: {
                  trackMouse: true,
                  width: 150,
                  height: 150,
                  renderer: function(storeItem, item) {
                    //calculate percentage.
                    var total = 0;
                    storeInstalacionesRechazadas.each(function(rec) {
                        total += (rec.get('data1') * 1);	
                    });
                    var porcentaje=Math.round((storeItem.get('data1') * 100) / total);
                    this.setTitle( storeItem.get('name') + ': ' + porcentaje + '% ('+storeItem.get('data1')+' servicios)');
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