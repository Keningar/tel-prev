Ext.require
([
    'Ext.Window', 
    'Ext.chart.*',
    'Ext.fx.target.Sprite', 
    'Ext.layout.container.Fit',
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var intMaximumCasos   = 10;
var intMaximumTareas  = 10;
var arrayExtJsCmpMask = [];
var dateFechaInicio   = '';
var dateFechaFin      = '';
var strEstado         = '';
var intEmpleado       = null;
var intDepartamento   = null;
var strFiltroUsuario  = '';
var strTipo           = '';
var intCodEmpresa     = '';
var intCuadrilla      = null;

Ext.onReady(function ()
{
    Ext.chart.theme.White = Ext.extend(Ext.chart.theme.Base,
    {
        constructor: function()
        {
            Ext.chart.theme.White.superclass.constructor.call(this,
            {
                axis:
                {
                    stroke: 'rgb(8,69,148)',
                    'stroke-width': 1
                },
                axisLabel:
                {
                    fill: 'rgb(8,148,148)',
                    font: '12px Arial',
                    'font-family': '"Arial',
                    spacing: 2,
                    padding: 5,
                    renderer: function(v)
                    {
                        return v; 
                    }
                },
                axisTitle:
                {
                  font: 'bold 18px Arial'
                }
           });
        }
    });
    
    Ext.define('dataCasosTareaModel',
    {
        extend: 'Ext.data.Model',
        fields:
        [
            {name:'name',  type: 'string',  mapping:'name'},
            {name:'value', type: 'int',     mapping:'value'}
        ]
    });
    
    /*
     * Panel Mis Casos del Mes
     */
    var storeDataCasos = Ext.create('Ext.data.JsonStore', 
        {
            model: 'dataCasosTareaModel',
            proxy:
            {
                type: 'ajax',
                url: strUrlGraficoBarrasCasosDelMes,
                reader:
                {
                    type: 'json',
                    root: 'arrayCasos'
                }
            },
            count : 0,
            listeners:
            {
                load: function(data)
                {
                    this.count      = this.getCount();
                    intMaximumCasos = 10;
                    
                    for (i=0; i < this.count; i++) 
                    {
                        var intValue =  data['data']['items'][i]['data']['value'];
                        
                        if( intValue > 10 )
                        {
                            if( intMaximumCasos < intValue )
                            {
                                intMaximumCasos = parseInt(intValue) + 2;
                            }
                        }
                    }
                    
                    Ext.getCmp('chartCasos').axes.items[0].maximum = intMaximumCasos;
                    Ext.getCmp('chartCasos').redraw();
                    
                    hideMask('casos');
                }
            },
            autoLoad: true
        });
    
    var panelCasos = Ext.create('widget.panel',
    {
        width: 360,
        height: 350,
        title: 'Mis Casos del mes',
        renderTo:  Ext.get('gridCasos'),
        layout: 'fit',
        buffered: false,
        tbar:
        [
            { 
                xtype: 'tbfill'
            },
            {
                text: 'Actualizar',
                handler: function()
                {
                    storeDataCasos.load();
                    doMask(Ext.getCmp('chartCasos'), 'casos');
                }
            }
        ],
        items:
        {
            id: 'chartCasos',
            xtype: 'chart',
            animate: true,
            store: storeDataCasos,
            axes:
            [{
                type: 'Numeric',
                position: 'left',
                fields: ['value'],
                title: 'Cantidad',
                grid: true,
                minimum: 0,
                maximum: intMaximumCasos,
                label:
                {
                    renderer: Ext.util.Format.numberRenderer('0,0')
                }
            },
            {
                type: 'Category',
                position: 'bottom',
                fields: ['name'],
                title: 'Estados',
                label:
                {
                    rotate:
                    {
                        degrees: 360
                    }
                }
            }],
            series:
            [{
                type: 'column',
                axis: 'left',
                gutter: 80,
                xField: ['name'],
                yField: ['value'],
                tips:
                {
                    trackMouse: true,
                    width: 100,
                    height: 38,
                    renderer: function(storeItem, item)
                    {
                        this.setTitle(storeItem.get('name') + ': ' + storeItem.get('value'));
                    }
                },
                style:
                {
                    fill: '#38B8BF'
                }
            }],
            listeners:
            {
                afterrender:
                {
                    fn: function()
                    {
                        doMask(Ext.getCmp('chartCasos'), 'casos');
                    }
                }
            }
        }
    });
    //Fin Panel Mis Casos del mes
    
    
    /*
     * Panel Mis Tareas del Mes
     */
    var storeDataTareas = Ext.create('Ext.data.JsonStore', 
        {
            model: 'dataCasosTareaModel',
            proxy:
            {
                type: 'ajax',
                url: strUrlGraficoBarrasTareasDelMes,
                reader:
                {
                    type: 'json',
                    root: 'arrayTareas'
                }
            },
            count : 0,
            listeners:
            {
                load: function(data)
                {
                    this.count = this.getCount();
                    intMaximumTareas = 10;
                    
                    for (i=0; i < this.count; i++) 
                    {
                        var intValue =  data['data']['items'][i]['data']['value'];
                        
                        if( intValue > 10 )
                        {
                            if( intMaximumTareas < intValue )
                            {
                                intMaximumTareas = parseInt(intValue) + 2;
                            }
                        }
                    }
                    
                    Ext.getCmp('chartTareas').axes.items[0].maximum = intMaximumTareas;
                    Ext.getCmp('chartTareas').redraw();
                    
                    hideMask('tareas');
                }
            },
            autoLoad: true
        });
    
    var panelTareas = Ext.create('widget.panel',
    {
        width: 360,
        height: 350,
        title: 'Mis Tareas del mes',
        renderTo:  Ext.get('gridTareas'),
        layout: 'fit',
        buffered: false,
        tbar:
        [
            { 
                xtype: 'tbfill'
            },
            {
                text: 'Actualizar',
                handler: function()
                {
                    storeDataTareas.load();
                    doMask(Ext.getCmp('chartTareas'), 'tareas');
                }
            }
        ],
        items:
        {
            id: 'chartTareas',
            xtype: 'chart',
            animate: true,
            store: storeDataTareas,
            axes:
            [{
                type: 'Numeric',
                position: 'left',
                fields: ['value'],
                title: 'Cantidad',
                grid: true,
                minimum: 0,
                maximum: intMaximumTareas,
                label:
                {
                    renderer: Ext.util.Format.numberRenderer('0,0')
                }
            },
            {
                type: 'Category',
                position: 'bottom',
                fields: ['name'],
                title: 'Estados',
                label:
                {
                    rotate:
                    {
                        degrees: 360
                    }
                }
            }],
            series:
            [{
                type: 'column',
                axis: 'left',
                gutter: 80,
                xField: ['name'],
                yField: ['value'],
                tips:
                {
                    trackMouse: true,
                    width: 100,
                    height: 38,
                    renderer: function(storeItem, item)
                    {
                        this.setTitle(storeItem.get('name') + ': ' + storeItem.get('value'));
                    }
                },
                style:
                {
                    fill: '#38B8BF'
                }
            }],
            listeners:
            {
                afterrender:
                {
                    fn: function()
                    {
                        doMask(Ext.getCmp('chartTareas'), 'tareas');
                    }
                }
            }
        }
    });
    //Fin Panel Mis Tareas del mes


    /*
     * Panel Mis Casos Asignados del Mes
     */
    var storeCasosAsignados = new Ext.data.Store
    ({ 
        pageSize: 10,
        total: 'total',
        proxy:
        {
            type: 'ajax',
            timeout: 600000,
            url : strUrlListadoCasosDelMes,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados',
                read: function(response)
                {
                    var data = Ext.data.reader.Json.prototype.getResponseData.call(this, response);
                    
                    if (data)
                    {
                        if (response && response.responseText) 
                        {
                            data = this.getResponseData(response);
                        }
                        
                        dateFechaInicio = data['records'][0]['raw']['feAperturaDesde'];
                        dateFechaFin    = data['records'][0]['raw']['feAperturaHasta'];
                        strEstado       = data['records'][0]['raw']['estadoCaso'];
                        intEmpleado     = data['records'][0]['raw']['empleadoId'];
                        intDepartamento = data['records'][0]['raw']['departamentoId'];
                        
                        return this.readRecords(data['records'][0]['raw']['casos']);
                    }
                    else
                    {
                        return this.nullResultSet;
                    }
                }
            }
        },
        fields:
        [
            {name:'id_caso', mapping:'id_caso'},
            {name:'numero_caso', mapping:'numero_caso'},
            {name:'titulo_ini', mapping:'titulo_ini'},
            {name:'titulo_fin', mapping:'titulo_fin'},
            {name:'version_ini', mapping:'version_ini'},
            {name:'version_fin', mapping:'version_fin'},
            {name:'departamento_asignado', mapping:'departamento_asignado'},
            {name:'empleado_asignado', mapping:'empleado_asignado'},
            {name:'oficina_asignada', mapping:'oficina_asignada'},
            {name:'empresa_asignada', mapping:'empresa_asignada'},
            {name:'asignado_por', mapping:'asignado_por'},
            {name:'fecha_asignacionCaso', mapping:'fecha_asignacionCaso'},
            {name:'fecha_apertura', mapping:'fecha_apertura'},
            {name:'hora_apertura', mapping:'hora_apertura'},
            {name:'fecha_cierre', mapping:'fecha_cierre'},
            {name:'hora_cierre', mapping:'hora_cierre'},
            {name:'estado', mapping:'estado'},                        
            {name:'usuarioApertura', mapping:'usuarioApertura'},
            {name:'usuarioCierre', mapping:'usuarioCierre'},
            {name:'tiempo_total', mapping:'tiempo_total'},
            {name:'empresa', mapping:'empresa'},
            {name:'fechaFin', mapping:'fechaFin'},
            {name:'horaFin', mapping:'horaFin'},
            {name:'siTareasAbiertas', mapping:'siTareasAbiertas'},
            {name:'siTareasSolucionadas', mapping:'siTareasSolucionadas'},
            {name:'siTareasTodas', mapping:'siTareasTodas'},
            {name:'esDepartamento', mapping:'esDepartamento'},
            {name:'elementoAfectado', mapping:'elementoAfectado'}, 
            {name:'hipotesisIniciales', mapping:'hipotesisIniciales'}, 
            {name:'date', mapping:'date'}
        ],
        autoLoad: true
    });

    
    var gridCasosAsignados = Ext.create('Ext.grid.Panel',
    {
        id: 'gridMisCasosAsignados',
        title: 'Mis Casos Asignados del Mes',
        width: 360,
        height: 350,
        store: storeCasosAsignados,
        viewConfig:
        { 
            enableTextSelection: true,
            emptyText: 'No hay datos para mostrar'
        },
        frame: false,
        selModel: {selType: 'rowmodel', mode: 'SIMPLE'},
        dockedItems: 
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: 
            [           
                {
                    text: 'Actualizar',
                    handler: function()
                    {
                        storeCasosAsignados.load();
                    }
                },
                { xtype: 'tbfill' },
                {
                    iconCls: 'icon_exportar',
                    text: 'Exportar',
                    scope: this,
                    handler: function()
                    {
                        exportarExcel('casos');
                    }
                }
            ]
        }],                  
        columns:
        [
            new Ext.grid.RowNumberer(),
            {
                id: 'id_caso',
                header: 'IdCaso',
                dataIndex: 'id_caso',
                hidden: true,
                hideable: false
            },
            {
                id: 'caso',
                header: 'Caso',
                xtype: 'templatecolumn', 
                width: 322,
                tpl: '<tpl if="version_fin==\'N/A\'">\n\\n\
                          <span class="bold">Numero Caso:</span></br>\n\
                          <span class="box-detalle">{numero_caso}</span></br>\n\\n\
                          <span class="bold">Titulo Inicial:</span></br>\n\
                          <span class="box-detalle">{titulo_ini}</span></br>\n\\n\
                          <span class="bold">Version Inicial:</span></br>\n\
                          <span>{version_ini}</span></br>\n\\n\
                          <span class="bold">Fecha de Apertura:</span></br>\n\
                          <span>{fecha_apertura} {hora_apertura}</span></br>\n\
                      </tpl>\n\\n\\n\
                      <span class="bold">Empresa Asignada:</span> {empresa_asignada}</br>\n\\n\
                      <span class="bold">Oficina Asignada:</span> {oficina_asignada}</br>\n\\n\
                      <span class="bold">Departamento Asignado:</span> {departamento_asignado}</br>\n\\n\
                      <span class="bold">Asignado Por:</span> {asignado_por}</br>\n\\n\
                      <span class="bold">Empleado Asignado:</span> {empleado_asignado}</br>\n\\n\
                      <span class="bold">Fecha Asignacion:</span> {fecha_asignacionCaso}</br></br>\n\\n\
                      <span class="bold">Usuario Apertura:</span> {usuarioApertura}</br>\n\\n\
                      <span class="bold">Usuario Cierre:</span> {usuarioCierre}</br></br>\n\\n\
                      <span class="bold">Tareas Creadas:</span> {siTareasTodas}</br>\n\\n\
                      <span class="bold">Tareas Abiertas:</span> {siTareasAbiertas}</br>\n\\n\
                      <span class="bold">Tareas Solucionadas:</span> {siTareasSolucionadas}</br></br>\n\\n\
                      '
            }
	],
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeCasosAsignados,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridCasosAsignados'
    });
    //Fin Panel Mis Casos Asignados del Mes
    
    
    /*
     * Panel Mis Tareas Asignadas del Mes
     */
    var storeTareasAsignadas = new Ext.data.Store
    ({ 
        pageSize: 10,
        total: 'total',
        proxy:
        {
            type: 'ajax',
            url : strUrlListadoTareasDelMes,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados',
                read: function(response)
                {
                    var data = Ext.data.reader.Json.prototype.getResponseData.call(this, response);
                    
                    if (data)
                    {
                        if (response && response.responseText) 
                        {
                            data = this.getResponseData(response);
                        }
                        
                        strFiltroUsuario = data['records'][0]['raw']['filtroUsuario'];
                        strTipo          = data['records'][0]['raw']['tipo'];
                        intCodEmpresa    = data['records'][0]['raw']['codEmpresa'];
                        intCuadrilla     = data['records'][0]['raw']['idCuadrilla'];
                        
                        return this.readRecords(data['records'][0]['raw']['tareas']);
                    }
                    else
                    {
                        return this.nullResultSet;
                    }
                }
            }
        },
        fields:
        [				
            {name: 'id_detalle', mapping: 'id_detalle'},
            {name: 'id_tarea', mapping: 'id_tarea'},
            {name: 'asignado_id', mapping: 'asignado_id'},
            {name: 'ref_asignado_id', mapping: 'ref_asignado_id'},
            {name: 'id_persona_empresa_rol', mapping: 'id_persona_empresa_rol'},
            {name: 'nombre_tarea', mapping: 'nombre_tarea'},
            {name: 'asignado_nombre', mapping: 'asignado_nombre'},
            {name: 'ref_asignado_nombre', mapping: 'ref_asignado_nombre'},
            {name: 'clientes', mapping: 'clientes'},
            {name: 'observacion', mapping: 'observacion'},
            {name: 'feTareaCreada', mapping: 'feTareaCreada'},
            {name: 'feSolicitada', mapping: 'feSolicitada'},
            {name: 'feTareaAsignada', mapping: 'feTareaAsignada'},
            {name: 'feTareaHistorial', mapping: 'feTareaHistorial'},
            {name: 'actualizadoPor', mapping: 'actualizadoPor'},
            {name: 'perteneceCaso', mapping: 'perteneceCaso'},
            {name: 'fechaEjecucion', mapping: 'fechaEjecucion'},
            {name: 'horaEjecucion', mapping: 'horaEjecucion'},
            {name: 'estado', mapping: 'estado'},                
            {name: 'numero_caso', mapping: 'numero_caso'},
            {name: 'numero_actividad', mapping: 'numero_actividad'},            
            {name: 'id_caso', mapping: 'id_caso'},
        ],
        autoLoad: true
    });      
    
   var gridTareasAsignadas =Ext.create('Ext.grid.Panel', 
   {
        id: 'gridMisTareasAsignadas',
        title: 'Mis Tareas Asignadas del Mes',
        width: 360,
        height: 350,
        store: storeTareasAsignadas,
        viewConfig:
        { 
            enableTextSelection: true,
            emptyText: 'No hay datos para mostrar'
        },
        frame: false,
        selModel:
        {
            selType: 'rowmodel',
            mode: 'SIMPLE'
        },
        dockedItems: 
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: 
            [           
                {
                    text: 'Actualizar',
                    handler: function()
                    {
                        storeTareasAsignadas.load();
                    }
                },
                { xtype: 'tbfill' },
                {
                    iconCls: 'icon_exportar',
                    text: 'Exportar',
                    scope: this,
                    handler: function()
                    { 
                        exportarExcel('tareas');
                    }
                }
            ]
        }],
        columns:
        [
            new Ext.grid.RowNumberer(),
            {
                id: 'id_detalle',
                header: 'IdDetalle',
                dataIndex: 'id_detalle',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_tarea',
                header: 'Tarea',
                xtype: 'templatecolumn', 
                width: 322,
                tpl: '<tpl>\n\\n\
                          <span class="bold">Pto. Cliente:</span></br>\n\
                          <span class="box-detalle">{clientes}</span></br>\n\\n\
                          <span class="bold">Numero Caso:</span></br>\n\
                          <span class="box-detalle">{numero_caso}</span></br>\n\\n\
                          <span class="bold">Nombre Tarea:</span></br>\n\
                          <span class="box-detalle">{nombre_tarea}</span></br>\n\\n\
                          <span class="bold">Fecha Ejecucion:</span></br>\n\
                          <span>{feSolicitada}</span></br>\n\
                      </tpl>\n\\n\\n\
                      <span class="bold">Empleado Asignado:</span> {empleado_asignado}</br>\n\\n\\n\\n\
                      <span class="bold">Observacion:</span></br>\n\\n\
                      <span>{observacion}</span></br>\n\\n\\n\
                      '
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeTareasAsignadas,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridTareasAsignadas'
    });
    //Fin Panel Mis Tareas Asignadas del Mes

    
    /*
     * Panel Casos del Departamento
     */
    var storeCasosDelDepartamento = Ext.create('Ext.data.JsonStore',
    {
        model: 'dataCasosTareaModel',
        proxy:
        {
            type: 'ajax',
            url: strUrlGraficoPastelCasosDelMes,
            reader:
            {
                type: 'json',
                root: 'arrayCasos'
            }
        },
        listeners:
        {
            load: function(data)
            {
                Ext.getCmp('chartCasosPastel').redraw();

                hideMask('casosPastel');
            }
        },
        autoLoad: true
    });	

    var panelCasosDepartamento = Ext.create('widget.panel',
    {
        width: 360,
        height: 350,
        title: 'Casos del Mes de Mi Departamento',
        renderTo: 'gridCasosDepartamento',
        layout: 'fit',
        items:
        {
            xtype: 'chart',
            id: 'chartCasosPastel',
            animate: true,
            store: storeCasosDelDepartamento,
            legend:
            {
                position: 'bottom'
            },
            insetPadding: 5,
            theme: 'Base:gradients',
            series:
            [{
                type: 'pie',
                field: 'value',
                id: 'seriePastel',
                showInLegend: true,
                donut: false,
                tips: 
                {
                    trackMouse: true,
                    width: 150,
                    height: 50,
                    renderer: function(storeItem, item)
                    {
                        var total = 0;

                        storeCasosDelDepartamento.each(function(rec) 
                        {
                            total += (rec.get('value') * 1);	
                        });

                        var porcentaje=Math.round((storeItem.get('value') * 100) / total);
                        this.setTitle( storeItem.get('name') + ': ' + porcentaje + '% ('+storeItem.get('value')+' casos)');
                    }
                },
                highlight: 
                {
                    segment:
                    {
                        margin: 20
                    }
                },
                label:
                {
                    field: 'name',
                    display: 'rotate',
                    contrast: true,
                    font: '12px Arial'
                }
            }]
        },
        dockedItems: 
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: 
            [      
                { xtype: 'tbfill' },
                {
                    text: 'Actualizar',
                    handler: function()
                    {
                        doMask(Ext.getCmp('chartCasosPastel'), 'casosPastel');
                        storeCasosDelDepartamento.load();
                    }
                }
            ]
        }],
        listeners:
        {
            afterrender:
            {
                fn: function()
                {
                    doMask(Ext.getCmp('chartCasosPastel'), 'casosPastel');
                }
            }
        },
    });
    //Fin Panel Casos del Departamento
    
    
    /*
     * Panel Tareas del Departamento
     */
    var storeTareasDelDepartamento = Ext.create('Ext.data.JsonStore',
    {
        model: 'dataCasosTareaModel',
        proxy:
        {
            type: 'ajax',
            url: strUrlGraficoPastelTareasDelMes,
            reader:
            {
                type: 'json',
                root: 'arrayTareas'
            }
        },
        listeners:
        {
            load: function(data)
            {
                Ext.getCmp('chartTareasPastel').redraw();

                hideMask('tareasPastel');
            }
        },
        autoLoad: true
    });	

    var panelTareasDepartamento = Ext.create('widget.panel',
    {
        width: 360,
        height: 350,
        title: 'Tareas del Mes de Mi Departamento',
        renderTo: 'gridTareasDepartamento',
        layout: 'fit',
        items:
        {
            xtype: 'chart',
            id: 'chartTareasPastel',
            animate: true,
            store: storeTareasDelDepartamento,
            legend:
            {
                position: 'right'
            },
            insetPadding: 5,
            theme: 'Base:gradients',
            series:
            [{
                type: 'pie',
                field: 'value',
                id: 'seriePastel',
                showInLegend: true,
                donut: false,
                tips: 
                {
                    trackMouse: true,
                    width: 150,
                    height: 50,
                    renderer: function(storeItem, item)
                    {
                        var total = 0;

                        storeTareasDelDepartamento.each(function(rec) 
                        {
                            total += (rec.get('value') * 1);	
                        });

                        var porcentaje=Math.round((storeItem.get('value') * 100) / total);
                        this.setTitle( storeItem.get('name') + ': ' + porcentaje + '% ('+storeItem.get('value')+' casos)');
                    }
                },
                highlight: 
                {
                    segment:
                    {
                        margin: 20
                    }
                },
                label:
                {
                    field: 'name',
                    display: 'rotate',
                    contrast: true,
                    font: '12px Arial'
                }
            }]
        },
        dockedItems: 
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: 
            [      
                { xtype: 'tbfill' },
                {
                    text: 'Actualizar',
                    handler: function()
                    {
                        doMask(Ext.getCmp('chartTareasPastel'), 'tareasPastel');
                        storeTareasDelDepartamento.load();
                    }
                }
            ]
        }],
        listeners:
        {
            afterrender:
            {
                fn: function()
                {
                    doMask(Ext.getCmp('chartTareasPastel'), 'tareasPastel');
                }
            }
        }
    });
    //Fin Panel Tareas del Departamento
    
});


function exportarExcel(name)
{      	
    if( name == 'tareas')
    {
        $('#feSolicitadaDesde').val( dateFechaInicio );
        $('#feSolicitadaHasta').val( dateFechaFin );
        $('#hid_estado').val( strEstado );

        document.forms[1].submit();
    }
    else if( name == 'casos')
    {
        $('#feAperturaDesde').val( dateFechaInicio );
        $('#feAperturaHasta').val( dateFechaFin );
        $('#hid_sltEstado').val( strEstado );
        $('#hid_comboDepartamento').val( intDepartamento  );
        $('#hid_comboEmpleado').val( intEmpleado );	

        document.forms[0].submit();
    }
}


function doMask(chart, name)
{
    arrayExtJsCmpMask[name] = new Ext.LoadMask(chart, { msg: 'Cargando...' });
    arrayExtJsCmpMask[name].show();
}


function hideMask(name)
{
    arrayExtJsCmpMask[name].hide();
}