
Ext.onReady(function() 
{        
    indicadorIntervalo = parseInt(indicadorIntervalo);
    
    Ext.tip.QuickTipManager.init();
    
    new Ext.TabPanel({
        height: 900,
        resizeTabs: true,
        width:1000,
        id:'tabs',
        renderTo: 'planificacion-hal-tabs',
        activeTab: 0,
        plain: true,
        autoRender: true,
        autoShow: true,
        items: [
            {contentEl: 'tab1', title: 'Planificar Cuadrillas', listeners:
                {
                    activate: function (tab) 
                    {
                        Ext.getCmp("tabs").setHeight(800);
                    }
                }
            },
            {contentEl: 'tab2', title: 'Agenda de Planificación'
            , listeners:
                {
                    activate: function (tab) 
                    {
                        Ext.getCmp("tabs").setHeight(950);
                        //Mostrar Calendario de planififcacion
                        showCalendario('','','','','');
                    }
                }
            },
            {contentEl: 'tab3', title: 'Resumen Planificación'
            , listeners:
                {
                    activate: function (tab) 
                    {
                        Ext.getCmp("tabs").setHeight(700);                        
                        //Mostrar Calendario de planififcacion
                        getGridConsultas();
                    }
                }
            }
        ]
    }); 
    
    var contentHtmlMV = Ext.create('Ext.Component', {
        html:'<div id="agregar-intervalo" align="center" style="cursor:pointer;" title="Ver listado de intervalos">\n\
               <i class="fa fa-plus-square-o fa-2x" aria-hidden="true" onclick="showIntervalosExistentes()"></i>\n\
              </div>'
    });
    
    //Store de resumen de la planificacion
    storePlanificacion = new Ext.data.Store({        
        total: 'total',
        async: false,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: urlConsultarPlanificacion,            
            timeout: 600000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idCuadrilla', mapping: 'idCuadrilla'},
                {name: 'idIntervalo', mapping: 'idIntervalo'},
                {name: 'idZona',      mapping: 'idZona'},
                {name: 'cuadrilla',   mapping: 'cuadrilla'},
                {name: 'zona',        mapping: 'zona'},
                {name: 'intervalo',   mapping: 'intervalo'},
                {name: 'fechaInicio', mapping: 'feInicio'},
                {name: 'fechaFin',    mapping: 'feFin'},
                {name: 'tareasAbiertas',mapping: 'tareasAbiertas'},
                {name: 'actividad',   mapping: 'actividad'},
                {name: 'feCreacion',mapping: 'feCreacion'}
            ]
    });
    
    //Cargar grid de consulta del resumen de planificacion
    getGridConsultas();

    var contentHtmlblankMin = Ext.create('Ext.Component', {
        html:'<div style="width:24px;"></div>'
    });

    var contentHtmlblankMd = Ext.create('Ext.Component', {
        html:'<div style="width:53px;"></div>'
    });


    var storeActividades = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        autoLoad:false,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: urlConsultarPreferencias,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                consulta: 'list_cuadrillas'
                }
        },
        fields:
            [
                {name: 'valor', mapping: 'valor'},
                {name: 'descripcion', mapping: 'descripcion'}
            ]
    });

    //Panel para creacion de planificacion HAL
    Ext.create('Ext.form.Panel', {
        id:'panelPlanificacion',
        renderTo: 'content-filtro',
        height: true,
        width   : 750,
        bodyPadding: 10,
        defaults: {
            anchor: '100%',
            labelWidth: 100            
        },
        items   : 
        [
            //Rango de fechas e Intervalos
            {
                xtype: 'fieldset',
                title: '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Información Fechas e Intervalos</b>',
                collapsible: true,
                defaults: {
                    labelWidth: 89,
                    anchor: '100%',
                    layout: {
                        type: 'hbox',
                        defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
                    }
                },
                items: [
                {
                    xtype: 'fieldcontainer',
                    items: [
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'txtFechaInicio',
                            name: 'txtFechaInicio',
                            fieldLabel: 'Inicio:',
                            format: 'Y-m-d',
                            editable: false,
                            minValue:new Date()
                        },
                        contentHtmlblankMd,
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'txtFechaFin',
                            name: 'txtFechaFin',
                            fieldLabel: 'Fin:',
                            format: 'Y-m-d',
                            editable: false,
                            minValue:new Date()
                        }
                    ]
                },
                {
                        xtype : 'fieldcontainer',
                        items : [
                            {
                                width:      290,
                                xtype:      'textfield',                                                                
                                fieldLabel: 'Escoja Intervalo',
                                id: 'txtIntervalo',
                                readOnly: true
                            },
                            {
                                xtype: 'hiddenfield',
                                id: 'hiddenIntervalo'
                            },
                            contentHtmlMV,
                            contentHtmlblankMin,
                            {
                                displayField:'descripcion',
                                valueField: 'valor',
                                xtype      : 'combobox',
                                editable   : false,
                                fieldLabel : 'Actividad',
                                id         : 'cmbActividad',
                                width      :  290,
                                name       : 'cmbActividad',
                                store      : storeActividades
                            }
                        ]
                    }
                ]
            },
            
            //Zonas y Cuadrillas
            {
                xtype: 'fieldset',
                title: '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Zonas y Cuadrillas</b>',
                collapsible: true,
                layout: 
                    {
                        type: 'table',
                        columns: 5,
                        pack: 'center'
                    },
                items: 
                [
                    //-----------------------------------
                    {width: '10%', border: false},
                    getGrid('cuadrilla'),
                    {html:"&nbsp;",border:false,width:50},
                    getGrid('zona'),
                    {width: '10%', border: false},
                    
                    //-----------------------------------
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    {html:"&nbsp;",border:false,width:50},
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    
                    //-----------------------------------      
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    {
                        xtype          : 'button',
                        text           : '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;Agregar',
                        listeners: 
                        {
                            click: function ()
                            {
                                agregarPlanificacion();
                            }
                        }
                    },
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                ]
            },
            
            //Grid de Resumen previo a generar las relaciones
            {
                xtype: 'fieldset',
                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Detalle de Planificación</b>',
                layout: 
                    {
                        type: 'table',
                        columns: 1,
                        pack: 'center'
                    },
                items: 
                [
                    getGridResumen()
                ]
            }
        ]
    });
    
    Ext.getCmp("btnCrearPlanificacion").setDisabled(true);
    
    //Cargar las cuadrillas en el combo de busqueda dentro de la planificacion
    storeCuadrillas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: urlConsultarCuadrillas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'nombre', mapping: 'nombre'}
            ]
    });

     //Cargar las zonas en el combo de busqueda dentro de la planificacion
    storeZonasFilter = new Ext.data.Store({
        total    : 'total',
        pageSize : 200,
        autoLoad : true,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    : url_zonas,
            reader: {
                type          : 'json',
                totalProperty : 'total',
                root          : 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
        [
            { name:'idZona'     , mapping:'idZona'  },
            { name:'nombreZona' , mapping:'nombreZona' }
        ]
    });

    //Cargar filtro de cuadrillas para la agenda
    getFilter();
    getFilterResumenPlanificacion();
    
});

/**
 * 
 * mostrará la ventana que contiene el grid con los intervalos existentes ordenados de menor a mayor
 */
function showIntervalosExistentes()
{
    limpiarResumenPlanificacion();
    storeIntervalos = new Ext.data.Store({
        pageSize: 100,
        total: 'total',        
        autoLoad:true,
        async: false,
        proxy: {
            timeout: 3000000,
            method: 'post',
            type: 'ajax',
            url: urlConsultarIntervalo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'id',         mapping: 'id'},
                {name: 'horaInicio', mapping: 'horaInicio'},
                {name: 'horaFin',    mapping: 'horaFin'}
            ]
    });
    
    var gridIntervalos = Ext.create('Ext.grid.Panel', {
        id:'gridIntervalos',
        width: 400,
        height: 300,
        store: storeIntervalos,
        loadMask: true,
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '<-',
                items:
                [
                    {xtype: 'tbfill'},
                    {
                        iconCls: 'icon_add',
                        height: 30,
                        text: '<b>Crear Nuevo Intervalo</b>',
                        scope: this,
                        handler: function() 
                        {
                            showAgregarIntervalo();
                        }
                    }
                ]
            }
        ],
        columns: [
            {
                header: 'id',
                dataIndex: 'id',
                hidden: true,
                hideable: false
            },
            {
                header: '<i align="center" class="fa fa-hashtag" aria-hidden="true"></i>',
				xtype : 'rownumberer',
				width : 25
			},
            {
                header: '<b>Hora Inicio</b>',
                dataIndex: 'horaInicio',
                align:'center',
                width: 150,
                sortable: false
            },
            {
                header: '<b>Hora Fin</b>',
                dataIndex: 'horaFin',
                align:'center',
                width: 150,
                sortable: false
            },
            {
                header: '<i class="fa fa-cogs" aria-hidden="true"></i>',
                xtype: 'actioncolumn',
                align:'center',
                width: 45,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-seleccionar';
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var data = grid.getStore().getAt(rowIndex).data;
                            var intervalo = data.horaInicio+" - "+data.horaFin;
                            Ext.getCmp('txtIntervalo').setValue(intervalo);
                            Ext.getCmp('hiddenIntervalo').setValue(data.id);
                            winIntervalos.close();
                            winIntervalos.destroy();
                        }
                    }
                ]
            }
        ]
    });
        
    var winIntervalos = Ext.widget('window', {
            id: 'winIntervalos',
            title: 'Intervalos Existentes',
            layout: 'fit',
            resizable: true,
            modal: true,
            closable: true,
            width: 'auto',
            items: [gridIntervalos]
        });
        
    winIntervalos.show();
}

function showAgregarIntervalo()
{
    var formPanelAgregarIntervalo = Ext.create('Ext.form.Panel', 
    {
        buttonAlign: 'center',
        BodyPadding: 10,
        id:'formPanelAgregarIntervalo',
        width: 300,
        height: 160,
        bodyStyle: "background: white; padding: 5px; border: 0px none;",
        frame: true,
        items:
            [
                {
                    xtype: 'fieldset',
                    id   : 'resumenEdicion',
                    title: '<b>Intervalos ( hrs. )</b>',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 1,
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'timefield',
                            fieldLabel: '<b>Hora Inicio</b>',
                            format: 'H:i',
                            id: 'txtHoraInicio',
                            name: 'txtHoraInicio',
                            minValue: '00:00',
                            maxValue: '22:00',
                            increment: 30,
                            editable: false,
                            listeners: {
                                select: {fn: function(valorTime, value) {
                                        var valueEscogido = valorTime.getValue();
                                        var valueEscogido2 = new Date(valueEscogido);
                                        var valueEscogidoAumentMili = valueEscogido2.setMinutes(valueEscogido2.getMinutes() + 60);
                                        var horaTotal = new Date(valueEscogidoAumentMili);
                                        Ext.getCmp('txtHoraFin').setMinValue(horaTotal);
                                    }}
                            }
                        },
                        {
                            xtype: 'timefield',
                            fieldLabel: '<b>Hora Fin</b>',
                            format: 'H:i',
                            id: 'txtHoraFin',
                            name: 'txtHoraFin',
                            minValue: '01:00',
                            maxValue: '23:00',
                            increment: 30,
                            editable: false
                        }
                    ]
            }                              
        ],
        buttons: [
            {
                text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;Crear',
                handler: function() 
                {                        
                    var horaInicio = Ext.Date.format(Ext.getCmp("txtHoraInicio").getValue(), 'H:i');
                    var horaFin    = Ext.Date.format(Ext.getCmp("txtHoraFin").getValue(), 'H:i');
                    
                    if(Ext.isEmpty(horaInicio) || Ext.isEmpty(horaFin))
                    {
                        Ext.Msg.alert('Alerta ', 'Por favor escoja la información de horas completas');
                        return false;
                    }
                                        
                    Ext.get('winAgregarIntervalo').mask('Creando nuevo Intervalo...');
                    
                    Ext.Ajax.request({
                        url: urlCrearIntervalo,
                        method: 'post',
                        timeout: 60000,
                        params:
                            {
                                horaInicio: horaInicio,
                                horaFin   : horaFin
                            },
                        success: function (response)
                        {
                            Ext.get('winAgregarIntervalo').unmask();

                            var objJson = Ext.JSON.decode(response.responseText);
                                                        
                            if(objJson.status === 'OK')
                            {                                
                                Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                                    if (btn == 'ok') 
                                    {
                                        winAgregarIntervalo.close();
                                        winAgregarIntervalo.destroy(); 
                                
                                        //Cargar el grid nuevamente
                                        storeIntervalos.load();
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Alerta', objJson.mensaje);
                            }
                        },
                        failure: function (result)
                        {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            },
            {
                text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function() 
                {
                    winAgregarIntervalo.close();
                    winAgregarIntervalo.destroy();                        
                }
            }
        ]});
        
    var winAgregarIntervalo = Ext.widget('window', {
            id: 'winAgregarIntervalo',
            title: 'Agregar Nuevo Intervalo',
            layout: 'fit',
            resizable: true,
            modal: true,
            closable: true,
            width: 'auto',
            items: [formPanelAgregarIntervalo]
        });
        
    winAgregarIntervalo.show();
}

function agregarPlanificacion()
{
    //obtener datos para validacion
    var fechaInicio       = Ext.getCmp("txtFechaInicio").getValue();
    var fechaFin          = Ext.getCmp("txtFechaFin").getValue();
    var intervaloId       = Ext.getCmp("hiddenIntervalo").getValue();
    var intervalo         = Ext.getCmp("txtIntervalo").getValue();
    var selectedCuadrilla = Ext.getCmp('gridcuadrilla').getView().getSelectionModel().getSelection();
    var selectedZona      = Ext.getCmp('gridzona').getView().getSelectionModel().getSelection();
    var selectedCodAct    = Ext.getCmp("cmbActividad").getValue();
    var selectedActividad = Ext.getCmp("cmbActividad").getRawValue();

    if(selectedCuadrilla.length === 0)
    {
        Ext.Msg.alert('Alerta ', 'Por favor Seleccione al menos una Cuadrilla');
        return false;
    }
    
    if(selectedZona.length === 0)
    {
        Ext.Msg.alert('Alerta ', 'Por favor seleccione al menos una Zona');
        return false;
    }
    
    if(Ext.isEmpty(fechaInicio) || Ext.isEmpty(fechaFin))
    {
        Ext.Msg.alert('Alerta ', 'Por favor escoja el rango de fecha de la Planificación');
        return false;
    }
    
    if(Ext.isEmpty(intervalo))
    {
        Ext.Msg.alert('Alerta ', 'Por favor escoja el Intervalo a asignar');
        return false;
    }
    
    if(parseInt(new Date(fechaInicio).getTime()) > parseInt(new Date(fechaFin).getTime()))
    {
        Ext.Msg.alert('Alerta ', 'La fecha Final debe ser mayor a la fecha Inicial de trabajo');
        return false;
    }

    if(selectedActividad.length === 0)
    {
        Ext.Msg.alert('Alerta ', 'Por favor Seleccione al menos una Actividad');
        return false;
    }

    var mensaje = validarCombinaciones();
    
    if(Ext.isEmpty(mensaje))
    {
        //Obtener Zona
        var idZona = selectedZona[0].data.id;
        var zona   = selectedZona[0].data.nombre;

        Ext.each(selectedCuadrilla, function (item) 
        {
            var recordParamDet = Ext.create('detalleModel', 
            {
                    idCuadrilla    : item.data.id,
                    cuadrilla      : item.data.nombre,
                    idZona         : idZona,
                    zona           : zona,
                    idIntervalo    : intervaloId,
                    intervalo      : intervalo,
                    fechaInicio    : getDate(fechaInicio),
                    fechaFin       : getDate(fechaFin),
                    actividad      : selectedActividad,
                    codActividad   : selectedCodAct
            });

            storeDetalles.insert(0, recordParamDet);
        });

        //limpiar
        limpiar();
    }
    else
    {
        Ext.getCmp("hiddenIntervalo").setValue("");
        Ext.getCmp("txtIntervalo").setValue("");
        Ext.Msg.alert('Alerta ', "No se pudo agregar la Cuadrilla con la Planificación escogida.\n\
                                  <br/>Se generaron las siguientes observaciones:<br/>"+mensaje);
        return false;
    }
}

function ajaxGuardarPlanificacion()
{
    var grid  = Ext.getCmp('gridResumen');
    
    var array = [];
    
    for (var i = 0; i < grid.getStore().getCount(); i++)
    {
        var json = {};
        json['idCuadrilla'] = grid.getStore().getAt(i).data.idCuadrilla;
        json['cuadrilla']   = grid.getStore().getAt(i).data.cuadrilla;
        json['idZona']      = grid.getStore().getAt(i).data.idZona;
        json['zona']        = grid.getStore().getAt(i).data.zona;
        json['idIntervalo'] = grid.getStore().getAt(i).data.idIntervalo;
        json['intervalo']   = grid.getStore().getAt(i).data.intervalo;
        json['fechaInicio'] = grid.getStore().getAt(i).data.fechaInicio;
        json['fechaFin']    = grid.getStore().getAt(i).data.fechaFin;
        json['codActividad']= grid.getStore().getAt(i).data.codActividad;
        
        array.push(json);
    }
    
    if(array.length !== 0)
    {
        Ext.get("panelPlanificacion").mask('Creando Planificación de las Cuadrillas...');
        
        Ext.Ajax.request({
            url: urlGuardarPlanificacion,
            method: 'post',
            params: 
            { 
                data      : Ext.JSON.encode(array)
            },
            success: function(response)
            {
                Ext.get("panelPlanificacion").unmask();
                
                var objJson = Ext.JSON.decode(response.responseText);
                
                if(objJson.status === 'OK')
                {
                    Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                        if (btn == 'ok') 
                        {                            
                            limpiar();
                            grid.getStore().removeAll();
                            grid.getStore().sync();
                            
                            Ext.getCmp('tabs').setActiveTab(1);
                            getGridConsultas();
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Alerta ', objJson.mensaje );
                }
            }
        });
    }
    else
    {
        Ext.Msg.alert('Alerta ', "No se encuentra planificada ninguna Cuadrilla");
        return false;
    }
}

