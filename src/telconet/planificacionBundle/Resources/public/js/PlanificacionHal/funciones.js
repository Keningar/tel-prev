
//filtro de las cuadrillas dentro de la agenda
function getFilter()
{
    var comboCuadrillas = Ext.create('Ext.form.ComboBox', {
        id           : 'comboCuadrillas',
        store        : storeCuadrillas,
        displayField : 'nombre',
        valueField   : 'id',
        fieldLabel   : 'Cuadrillas',
        width        : 340,
        labelWidth   : 80,
        emptyText    : 'Seleccione Cuadrilla a consultar',
        disabled     : false
    });

    var comboZonasFilter = Ext.create('Ext.form.ComboBox', {
        id           : 'comboZonas',
        store        :  storeZonasFilter,
        displayField : 'nombreZona',
        valueField   : 'idZona',
        fieldLabel   : 'Zona',
        width        :  340,
        labelWidth   :  80,
        emptyText    : 'Seleccione la Zona a consultar',
        disabled     : false
    });

    var dateFechaTrabajoIni = new Ext.form.DateField(
    {
        id         : 'dateFechaTrabajoIniAgenda',
        name       : 'dateFechaTrabajoIniAgenda',
        xtype      : 'datefield',
        fieldLabel : 'Fecha Ini',
        format     : 'Y-m-d',
        emptyText  : 'Fecha de trabajo Inicio',
        editable   : false,
        labelWidth : 80,
        width      : 250
    });

    var dateFechaTrabajoFin = new Ext.form.DateField(
    {
        id         : 'dateFechaTrabajoFinAgenda',
        name       : 'dateFechaTrabajoFinAgenda',
        xtype      : 'datefield',
        fieldLabel : 'Fecha Fin',
        format     : 'Y-m-d',
        emptyText  : 'Fecha de trabajo Fin',
        editable   : false,
        labelWidth : 80,
        width      : 250
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,        
        border: false,        
        buttonAlign: 'center',
        renderTo:'content-filtro-calendar',
        layout: {
            type: 'table',
            align: 'stretch',
            columns: 5
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 750,
//        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscarPlanificacionCuadrilla();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    Ext.getCmp("comboCuadrillas").value = "";
                    Ext.getCmp("comboCuadrillas").setRawValue("");
                    Ext.getCmp("comboZonas").value = "";
                    Ext.getCmp("comboZonas").setRawValue("");
                    Ext.getCmp('dateFechaTrabajoIniAgenda').setValue(null);
                    Ext.getCmp('dateFechaTrabajoFinAgenda').setValue(null);
                    showCalendario('','','','','');
                }
            }
        ],
        items: [
            {width: '1%', border: false},
            comboCuadrillas,
            {html: "&nbsp;", border: false, width: 20},
            dateFechaTrabajoIni,
            {html: "&nbsp;", border: false, width: 20},
            {html: "&nbsp;", border: false, width: 20},
            comboZonasFilter,
            {html: "&nbsp;", border: false, width: 20},
            dateFechaTrabajoFin
        ]
    });
    
    return filterPanel;
}

//filtro de las cuadrillas dentro de la agenda
function getFilterResumenPlanificacion()
{
    var comboCuadrillas = Ext.create('Ext.form.ComboBox', {
        id           : 'comboCuadrillasResumen',
        store        : storeCuadrillas,
        displayField : 'nombre',
        valueField   : 'id',
        fieldLabel   : 'Cuadrillas',
        width        : 340,
        labelWidth   : 80,
        emptyText    : 'Seleccione Cuadrilla a consultar',
        disabled     : false
    });

    var comboZonasFilter = Ext.create('Ext.form.ComboBox', {
        id           : 'comboZonasResumen',
        store        :  storeZonasFilter,
        displayField : 'nombreZona',
        valueField   : 'idZona',
        fieldLabel   : 'Zona',
        width        :  340,
        labelWidth   :  80,
        emptyText    : 'Seleccione la Zona a consultar',
        disabled     : false
    });

    var dateFechaTrabajoIni = new Ext.form.DateField(
    {
        id         : 'dateFechaTrabajoIni',
        name       : 'dateFechaTrabajoIni',
        xtype      : 'datefield',
        fieldLabel : 'Fecha Ini',
        format     : 'Y-m-d',
        emptyText  : 'Fecha de trabajo Inicio',
        editable   : false,
        labelWidth : 80,
        width      : 250
    });

    var dateFechaTrabajoFin = new Ext.form.DateField(
    {
        id         : 'dateFechaTrabajoFin',
        name       : 'dateFechaTrabajoFin',
        xtype      : 'datefield',
        fieldLabel : 'Fecha Fin',
        format     : 'Y-m-d',
        emptyText  : 'Fecha de trabajo Fin',
        editable   : false,
        labelWidth : 80,
        width      : 250
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        renderTo:'content-filtro-resumen-planificacion',
        layout: {
            type: 'table',
            align: 'stretch',
            columns: 5
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 750,
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {buscarResumenPlanificacion();}
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {limpiarResumenPlanificacion();}
            }
        ],
        items: [
            {width: '1%', border: false},
            comboCuadrillas,
            {html: "&nbsp;", border: false, width: 20},
            dateFechaTrabajoIni,
            {html: "&nbsp;", border: false, width: 20},
            {html: "&nbsp;", border: false, width: 20},
            comboZonasFilter,
            {html: "&nbsp;", border: false, width: 20},
            dateFechaTrabajoFin
        ]
    });
    return filterPanel;
}

function getDiferenciaTiempo(fechaIni, fechaFin)
{
    var fechaIniS  = getDate(fechaIni).split("-");
    var fechaFinS  = getDate(fechaFin).split("-");
    var dateInicio = (String)(fechaIniS[0] + "-" + fechaIniS[1] + "-" + fechaIniS[2]);
    var dateFin    = (String)(fechaFinS[0] + "-" + fechaFinS[1] + "-" + fechaFinS[2]);
    var diferencia = new Date(dateFin) - new Date(dateInicio);
    return Math.ceil((((diferencia / 1000) / 60) / 60) / 24);
}

function buscarResumenPlanificacion()
{
    if (Ext.getCmp('dateFechaTrabajoIni').getValue() !== null &&
        Ext.getCmp('dateFechaTrabajoIni').getValue() !== ''   &&
        Ext.getCmp('dateFechaTrabajoFin').getValue() !== null &&
        Ext.getCmp('dateFechaTrabajoFin').getValue() !== '')
    {

        if (Ext.getCmp('dateFechaTrabajoFin').getValue().getTime() <
            Ext.getCmp('dateFechaTrabajoIni').getValue().getTime())
        {
            Ext.Msg.alert('Alerta', 'La fecha fin no puede ser menor a la fecha inicio..!!');
            return;
        }

        if(getDiferenciaTiempo(Ext.getCmp('dateFechaTrabajoIni').getValue(),Ext.getCmp('dateFechaTrabajoFin').getValue()) > 31)
        {
            Ext.Msg.alert('Alerta ', "El rango de fechas elegidas no puede superar un máximo de 30 días..!!");
            return;
        }
    }

    storePlanificacion.getProxy().extraParams.fechaIni    = Ext.getCmp('dateFechaTrabajoIni').getValue();
    storePlanificacion.getProxy().extraParams.fechaFin    = Ext.getCmp('dateFechaTrabajoFin').getValue();
    storePlanificacion.getProxy().extraParams.idCuadrilla = Ext.getCmp("comboCuadrillasResumen").getValue();
    storePlanificacion.getProxy().extraParams.idZona      = Ext.getCmp("comboZonasResumen").getValue();
    storePlanificacion.getProxy().extraParams.strEsConsulta = 'SI';
    storePlanificacion.load();
}

function limpiarResumenPlanificacion()
{
    Ext.getCmp("comboCuadrillasResumen").value = "";
    Ext.getCmp("comboCuadrillasResumen").setRawValue("");
    Ext.getCmp("comboZonasResumen").value = "";
    Ext.getCmp("comboZonasResumen").setRawValue("");
    Ext.getCmp('dateFechaTrabajoIni').setValue(null);
    Ext.getCmp('dateFechaTrabajoFin').setValue(null);
    storePlanificacion.getProxy().extraParams.idCuadrilla = null;
    storePlanificacion.getProxy().extraParams.idZona      = null;
    storePlanificacion.getProxy().extraParams.fechaIni    = null;
    storePlanificacion.getProxy().extraParams.fechaFin    = null;
    storePlanificacion.load();
}

//Grids donde se cargan las cuadrillas y zonas para realizar la planificacion
function getGrid(value)
{
    var array      = [];
    var widthPref  = 250;
    var storeDatos = null;

    if(value === 'cuadrilla')
    {
        widthPref  = 350;
        Ext.define('datosModel', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id',     type: 'integer'},
                {name: 'nombre', type: 'string'},
                {name: 'preferencia', type: 'string'}
            ]
        });

        storeDatos = new Ext.data.Store({
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
                    {name: 'nombre', mapping: 'nombre'},
                    {name: 'preferencia', mapping: 'preferencia'}
                ]
        });
    }
    else
    {
        array = JSON.parse(arrayZonas);
        Ext.define('datosModel', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id',     type: 'integer'},
                {name: 'nombre', type: 'string'}
            ]
        });
        storeDatos = Ext.create('Ext.data.Store', {
            pageSize: 5,
            autoDestroy: true,
            model: 'datosModel',
            proxy: {
                type: 'memory'
            }
        });
        
        $.each(array, function(i, item)
        {
            var recordParamDet = Ext.create('datosModel', {
                    id    : item.id,
                    nombre: item.nombre
                });
            storeDatos.insert(0, recordParamDet);
        });
    }
    
    var sm = null;
    
    if(value === 'zona')
    {
        sm = new Ext.selection.CheckboxModel({
            mode: 'SINGLE',
            checkOnly: 'true',
            allowDeselect: true        
        });
    }
    else
    {
        sm = new Ext.selection.CheckboxModel();
    }
    
    
    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid'+value,
        width: widthPref,
        height: 250,
        store: storeDatos,
        loadMask: true,
        selModel: sm,
        iconCls: 'icon-grid',        
        columns: [
            {
                header: 'id',
                dataIndex: 'id',
                hidden: true,
                hideable: false
            },
            {
                header: '<b>'+value.charAt(0).toUpperCase() + value.slice(1)+'</b>',
                dataIndex: 'nombre',
                width: 300,
                sortable: true
            }
        ]           
    });
    
    return grid;
}

//grid de resumen de la planificacion a ser guardada
function getGridResumen()
{
    Ext.define('detalleModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idCuadrilla', type: 'integer'},
            {name: 'idZona',      type: 'integer'},
            {name: 'idIntervalo', type: 'integer'},
            {name: 'cuadrilla',   type: 'string'},
            {name: 'zona',        type: 'string'},
            {name: 'fechaInicio', type: 'string'},
            {name: 'fechaFin',    type: 'string'},
            {name: 'intervalo',   type: 'string'},
            {name: 'actividad',   type: 'string'},
            {name: 'codActividad',type: 'string'}
        ]
    });

    storeDetalles = Ext.create('Ext.data.Store', {
        pageSize: 5,
        autoDestroy: true,
        model: 'detalleModel',
        proxy: {
            type: 'memory'
        }
    });
    
    var grid = Ext.create('Ext.grid.Panel', {
        id:'gridResumen',
        width: 700,
        height: 200,
        store: storeDetalles,
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
                        id:'btnCrearPlanificacion',
                        text: '<label style="color:#3a87ad;"><i class="fa fa-floppy-o" aria-hidden="true"></i></label>&nbsp;&nbsp;<b>Crear Planificación</b>',
                        scope: this,
                        handler: function() 
                        {
                            ajaxGuardarPlanificacion();
                        }
                    }
                ]
            }
        ],
        columns: [
            {
                header: 'idCuadrilla',
                dataIndex: 'idCuadrilla',
                hidden: true,
                hideable: false
            },
            {
                header: 'idZona',
                dataIndex: 'idZona',
                hidden: true,
                hideable: false
            },
            {
                header: 'idIntervalo',
                dataIndex: 'idIntervalo',
                hidden: true,
                hideable: false
            },
            {
                header: '<b>Cuadrilla</b>',
                dataIndex: 'cuadrilla',
                width: 150,
                sortable: true
            },
            {
                header: '<b>Zona</b>',
                dataIndex: 'zona',
                width: 150,
                sortable: true
            },
            {
                header: '<b>Fe. Inicio</b>',
                dataIndex: 'fechaInicio',
                width: 75,
                align:'center',
                sortable: true
            },
            {
                header: '<b>Fe. Fin</b>',
                dataIndex: 'fechaFin',
                width: 75,
                align:'center',
                sortable: true
            },
            {
                header: '<b>Intervalo ( hrs. )</b>',
                dataIndex: 'intervalo',
                width: 105,
                align:'center',
                sortable: true
            },
            {
                header: 'codActividad',
                dataIndex: 'codActividad',
                hidden: true,
                hideable: false
            },
            {
                header: '<b>Actividad</b>',
                dataIndex: 'actividad',
                width: 95,
                align:'center',
                sortable: true
            },
            {
                header: '<i class="fa fa-cogs" aria-hidden="true"></i>',
                xtype: 'actioncolumn',
                width: 45,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-delete';
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            storeDetalles.remove(grid.getStore().getAt(rowIndex));
                            
                            if(storeDetalles.getCount() === 0)
                            {
                                Ext.getCmp("btnCrearPlanificacion").setDisabled(true);
                            }
                        }
                    }
                ]
            }
        ]
    });        
    
    return grid;
}

//Grid de consultas generales de la planificacion HAL guardada
function getGridConsultas()
{            
    if(Ext.getCmp('gridConsulta'))
    {        
        storePlanificacion.load();
    }
    else
    {
        storePlanificacion.load();
        var gridConsulta = Ext.create('Ext.grid.Panel', {
            id:'gridConsulta',
            frame: false,
            width: 920,
            height: 450,
            renderTo:'content-resumen-planificacion',
            store: storePlanificacion,
            loadMask: true,
            listeners:
            {
                itemdblclick: function(view, record, item, index, eventobj, obj) {
                    var position = view.getPositionByEvent(eventobj),
                        data = record.data,
                        value = data[this.columns[position.column].dataIndex];
                    Ext.Msg.show({
                        title: 'Copiar texto?',
                        msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFORMATION
                    });
                }
            },
            columns: [
                new Ext.grid.RowNumberer(),
                {
                    id:'idCuadrilla',
                    header: 'idCuadrilla',
                    dataIndex: 'idCuadrilla',
                    hidden: true,
                    hideable: false
                },
                {
                    id:'idZona',
                    header: 'idZona',
                    dataIndex: 'idZona',
                    hidden: true,
                    hideable: false
                },
                {
                    id:'idIntervalo',
                    header: 'idIntervalo',
                    dataIndex: 'idIntervalo',
                    hidden: true,
                    hideable: false
                },
                {
                    id:'cuadrillaCreada',
                    header: '<b>Cuadrilla</b>',
                    dataIndex: 'cuadrilla',
                    width: 170,
                    align:'center'
                },
                {
                    id:'zonaCreada',
                    header: '<b>Zona</b>',
                    dataIndex: 'zona',
                    align:'center',
                    width: 150
                },
                {
                    id:'feInicioCreada',
                    header: '<b>Fe. Inicio</b>',
                    dataIndex: 'fechaInicio',
                    width: 80,
                    align:'center',
                    renderer: function(value)
                    {
                        var array = value.split(" ");
                        return array[0];
                    }
                },
                {
                    id:'feFinCreada',
                    header: '<b>Fe. Fin</b>',
                    dataIndex: 'fechaFin',
                    width: 80,
                    align:'center',
                    renderer: function(value)
                    {
                        var array = value.split(" ");
                        return array[0];
                    }
                },
                {
                    id:'intervaloCreada',
                    header: '<b>Intervalo ( hrs. )</b>',
                    dataIndex: 'intervalo',
                    width: 110,
                    align:'center'                
                },
                {
                    id        :'feCreacionPlanif',
                    header    : '<b>Fe. Creación</b>',
                    dataIndex : 'feCreacion',
                    width     : 90,
                    align     :'center',
                    renderer: function(value) {
                        var array = value.split(" ");
                        return array[0];
                    }
                },
                {
                    id:'actividadGen',
                    header: '<b>Actividad</b>',
                    dataIndex: 'actividad',
                    width: 100,
                    align:'center'
                },
                {
                    id:'tareasAbiertas',
                    header: '<b># Tareas Abiertas</b>',
                    dataIndex: 'tareasAbiertas',
                    width: 100,
                    align:'center',
                    renderer:function(val)
                    {
                        return '<b style="color:green;">'+val+'</b>';
                    }
                },
                {
                    header: '<i class="fa fa-cogs" aria-hidden="true"></i>',
                    xtype: 'actioncolumn',
                    width: 45,
                    sortable: false,
                    items:
                    [
                        {
                            getClass: function(v, meta, rec) 
                            {
                                return 'button-grid-delete';
                            },
                            handler: function(grid, rowIndex, colIndex) 
                            {
                                //Verificar si no existen tareas abiertas para poder eliminar la planificacion realizada
                                var data = grid.getStore().getAt(rowIndex).data;
                                
                                if(!Ext.isEmpty(data.tareasAbiertas) && data.tareasAbiertas !== 0)
                                {
                                    Ext.Msg.alert('<b>Alerta</b>', '<b>No se puede eliminar la planificación de trabajo dado que aún existe<br />\n\
                                                                    al menos una tarea sin finalizar para la cuadrilla..!!</b>');
                                    return false;
                                }
                                else
                                {
                                    Ext.Msg.confirm('Información', '¿Está seguro que desea eliminar la planificación de trabajo seleccionada?',
                                    function(btn)
                                    {
                                        if(btn == 'yes')
                                        {
                                            Ext.get('tabs').mask("Eliminado la Planificación Seleccionada...");

                                            //Eliminar la planificacion
                                            Ext.Ajax.request({
                                                url: urlEliminarLiberarPlanif,
                                                method: 'post',
                                                timeout: 60000,
                                                params:
                                                    {
                                                        accion        : 'eliminarPlanificacion',
                                                        idCuadrilla   : data.idCuadrilla,
                                                        idZona        : data.idZona,
                                                        idIntervalo   : data.idIntervalo
                                                    },
                                                success: function (response)
                                                {
                                                    Ext.get('tabs').unmask();

                                                    var objJson = Ext.JSON.decode(response.responseText);

                                                    if(objJson.status === 'OK')
                                                    {                                
                                                        Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                                                            if (btn == 'ok') 
                                                            {
                                                                storePlanificacion.load();
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
                                        }//btn ok
                                    });
                                }//eliminacion
                            }//handler
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storePlanificacion,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            })
        });      
    }
}

//Grid de planificacion con el detalle de horas y tareas
function getGridDetallePlanificacion(idCabecera,idCuadrilla)
{
    storeDetallePlanificacion = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: urlConsultarPlanifDiaria,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                idCabecera : idCabecera
            }
        },
        fields:
            [
                {name: 'idDet',       mapping: 'idDet'},
                {name: 'horaInicio',  mapping: 'horaInicio'},
                {name: 'horaFin',     mapping: 'horaFin'},
                {name: 'idTarea',     mapping: 'idTarea'},
                {name: 'nombreTarea', mapping: 'nombreTarea'},
                {name: 'estadoTarea', mapping: 'estadoTarea'},
                {name: 'estado',      mapping: 'estado'},
                {name: 'tipoProceso', mapping: 'tipoProceso'}
            ]
    });
    
    var sm = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function (selectionModel, selected, options) 
            {                                
                if(selected.length > 0)
                {
                    Ext.getCmp("btn_liberar_horas").setDisabled(false);
                }
                else
                {
                    Ext.getCmp("btn_liberar_horas").setDisabled(true);
                }
                
                Ext.each(selected, function (record) 
                {                    
                    if(record.data.estado === 'Liberado')
                    {
                        sm.deselect(record.index); 
                    }
                });
            }
        }
    });
    
    var gridDetalle = Ext.create('Ext.grid.Panel', {
        id:'gridDetalle',
        frame: false,
        width: 640,
        height: 360,
        store: storeDetallePlanificacion,
        loadMask: true,
        selModel: sm,
        viewConfig: 
        {
            enableTextSelection: true,
            getRowClass: function(record, index) 
            {
                var estado = record.get('estado');
                
                if (estado === 'Liberado') 
                {
                    return 'inhabilitarRawGrid';
                } 
                else
                {
                    return 'blackTextGrid';
                }
            },
            emptyText: 'No existen horas de trabajo creadas'
        },        
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items:
                [
                    {xtype: 'tbfill'},
                    {
                        text: '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>&nbsp;<b>Actualizar Horas</b>',
                        scope: this,
                        handler: function() 
                        {
                            showPanelActualizacionHorasTrabajo(idCabecera,idCuadrilla);
                        }
                    },
                    {
                        text: '<label style="color:red;"><i class="fa fa-minus-square" aria-hidden="true"></i></label>&nbsp;<b>Liberar Horas</b>',
                        scope: this,
                        id:'btn_liberar_horas',
                        handler: function() 
                        {
                            liberarHorasTrabajo(idCabecera,idCuadrilla);
                        }
                    }
                ]
            }
        ],
        columns: [
            new Ext.grid.RowNumberer(),
            {
                id:'idDet',
                header: 'idDet',
                dataIndex: 'idDet',
                hidden: true,
                hideable: false
            },
            {
                id:'estado',
                header: 'estado',
                dataIndex: 'estado',
                hidden: true,
                hideable: false
            },
            {
                id:'horaInicio',
                header: '<b>Hora Inicio</b>',
                dataIndex: 'horaInicio',
                width: 90,
                align:'center'
            },
            {
                id:'horaFin',
                header: '<b>Hora Fin</b>',
                dataIndex: 'horaFin',
                align:'center',
                width: 90
            },
            {
                id:'tipoProceso',
                header: '<b>Proceso Origen</b>',
                dataIndex: 'tipoProceso',
                align:'center',
                width: 110
            },
            {
                id:'idTarea',
                header: '<b># Tarea</b>',
                dataIndex: 'idTarea',
                width: 100,
                align:'center',
                renderer:function(val)
                {
                    if(!Ext.isEmpty(val))
                    {
                        return '<b style="color:green;">'+val+'</b>';
                    }
                    else
                    {
                        return val;
                    }
                }
            },
            {
                id:'estadoTarea',
                header: '<b>Estado Tarea</b>',
                dataIndex: 'estadoTarea',
                width: 100,
                align:'center'                
            },
            {
                id:'nombreTarea',
                header: '<b>Nombre Tarea</b>',
                dataIndex: 'nombreTarea',
                width: 330,
                align:'left'
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeDetallePlanificacion,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        listeners: 
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        }
    });    
    
    Ext.getCmp("btn_liberar_horas").setDisabled(true);
    
    return gridDetalle;
}

//Panel de actualizacion de horas de trabajo dentro de una fecha de trabajo ya planificada
function showPanelActualizacionHorasTrabajo(idCabecera,idCuadrilla)
{
    var intervalo       = Ext.getCmp("intervaloPlanificacion").getValue();
    var arrayIntervalos = intervalo.split(" - ");
    
    var formPanelAgregarIntervaloDemanda = Ext.create('Ext.form.Panel', 
    {
        buttonAlign: 'center',
        BodyPadding: 10,
        id:'formPanelAgregarIntervaloDemanda',
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
                            id: 'txtHoraInicioAct',
                            name: 'txtHoraInicioAct',
                            minValue: '00:00',
                            maxValue: getHoraLimiteRegulada(arrayIntervalos[0],"-"),
                            increment: indicadorIntervalo,
                            value:arrayIntervalos[0],
                            editable: false
                        },
                        {
                            xtype: 'timefield',
                            fieldLabel: '<b>Hora Fin</b>',
                            format: 'H:i',
                            id: 'txtHoraFinAct',
                            name: 'txtHoraFinAct',
                            minValue: getHoraLimiteRegulada(arrayIntervalos[1],"+"),
                            maxValue: '23:'+(60-indicadorIntervalo),
                            increment: indicadorIntervalo,
                            value:arrayIntervalos[1],
                            editable: false
                        }
                    ]
            }                              
        ],
        buttons: [
            {
                text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;Actualizar',
                handler: function() 
                {                        
                    var mensaje = validarCombinacionesActualizarHoras();
                    
                    if(!Ext.isEmpty(mensaje))
                    {
                        Ext.Msg.alert('Alerta ', "No se pudo agregar la Cuadrilla con la Planificación escogida.\n\
                                                  <br/>Se generaron las siguientes observaciones:<br/>" + mensaje);
                        return false;
                    }
                                            
                    Ext.get('winAgregarIntervaloDemanda').mask('Actualizando horas de trabajo...');
                    
                    var horaInicio        = Ext.Date.format(Ext.getCmp("txtHoraInicioAct").getValue(), 'H:i');
                    var horaFin           = Ext.Date.format(Ext.getCmp("txtHoraFinAct").getValue(), 'H:i');
                    
                    Ext.Ajax.request({
                        url: urlActualizarHorasTrabajo,
                        method: 'post',
                        timeout: 60000,
                        params:
                            {
                                idCabecera  : idCabecera,
                                horaInicio  : horaInicio,
                                intervalo   : intervalo,
                                horaFin     : horaFin,
                                idCuadrilla : idCuadrilla,
                                fecha       : Ext.getCmp("fechaActual").getValue()
                            },
                        success: function (response)
                        {
                            Ext.get('winAgregarIntervaloDemanda').unmask();

                            var objJson = Ext.JSON.decode(response.responseText);
                                                        
                            if(objJson.status === 'OK')
                            {                                
                                Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                                    if (btn == 'ok') 
                                    {
                                        Ext.getCmp('winAgregarIntervaloDemanda').close();
                                        storeDetallePlanificacion.load();
                                        showCalendario('','','','','');
                                        storeJornadaLaboral.removeAll();
                                        storeJornadaLaboral.getProxy().extraParams.idCab      = idCabecera;
                                        storeJornadaLaboral.getProxy().extraParams.estadoCab  = 'Activo';
                                        storeJornadaLaboral.getProxy().extraParams.estadoDet  = 'Activo';
                                        storeJornadaLaboral.load();
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
                    winAgregarIntervaloDemanda.close();
                    winAgregarIntervaloDemanda.destroy();                        
                }
            }
        ]});
        
    var winAgregarIntervaloDemanda = Ext.widget('window', {
            id: 'winAgregarIntervaloDemanda',
            title: 'Actualizar horas de trabajo',
            layout: 'fit',
            resizable: true,
            modal: true,
            closable: true,
            width: 'auto',
            items: [formPanelAgregarIntervaloDemanda]
        });
        
    winAgregarIntervaloDemanda.show();
}

function validarCombinacionesActualizarHoras()
{
    var mensaje           = "";
    
    var fechaInicioNuevo  = parseInt(new Date(Ext.getCmp("fechaActual").getValue()).getTime());
    var fechaFinNuevo     = parseInt(new Date(Ext.getCmp("fechaActual").getValue()).getTime());
    
    var idIntervaloNuevo  = parseInt(Ext.getCmp("hdIntervalo").getValue());
    var idZonaNueva       = parseInt(Ext.getCmp("hdZona").getValue());
    var cuadrillaNueva    = Ext.getCmp("cuadrillaDetalle").getValue();
    
    var horaInicio        = Ext.Date.format(Ext.getCmp("txtHoraInicioAct").getValue(), 'H:i');
    var horaFin           = Ext.Date.format(Ext.getCmp("txtHoraFinAct").getValue(), 'H:i');
        
    var horaInicioNueva   = horaInicio;
    var horaFinNueva      = horaFin;
        
    var grid              = Ext.getCmp('gridResumen');
    var gridPlanificados  = Ext.getCmp('gridConsulta');
    
    //Comparacion solo contra otra planificacion
    
    mensaje += validarCombinacionesPorGrid( grid, 
                                            idIntervaloNuevo, 
                                            cuadrillaNueva, 
                                            idZonaNueva, 
                                            fechaInicioNuevo, 
                                            fechaFinNuevo, 
                                            horaInicioNueva, 
                                            horaFinNueva,
                                            'nuevo',
                                            true,
                                            null,
                                            null,
                                            null
                                           );

    //Consulta sobre lo ingresado para un Cuadrilla
    mensaje += validarCombinacionesPorGrid( gridPlanificados, 
                                            idIntervaloNuevo, 
                                            cuadrillaNueva, 
                                            idZonaNueva, 
                                            fechaInicioNuevo, 
                                            fechaFinNuevo, 
                                            horaInicioNueva, 
                                            horaFinNueva,
                                            'planificado',
                                            true,
                                            null,
                                            null,
                                            null
                                           );
    return mensaje;
}

function validarCombinaciones()
{
    var mensaje           = "";
    var fechaInicio       = getDate(Ext.getCmp("txtFechaInicio").getValue());
    var fechaFin          = getDate(Ext.getCmp("txtFechaFin").getValue());
    var fechaInicioNuevo  = parseInt(new Date(getDate(Ext.getCmp("txtFechaInicio").getValue())).getTime());
    var fechaFinNuevo     = parseInt(new Date(getDate(Ext.getCmp("txtFechaFin").getValue())).getTime());
    var intervaloNuevo    = Ext.getCmp("txtIntervalo").getValue();
    var idIntervaloNuevo  = parseInt(Ext.getCmp("hiddenIntervalo").getValue());
    var selectedCuadrilla = Ext.getCmp('gridcuadrilla').getView().getSelectionModel().getSelection();
    var selectedZona      = Ext.getCmp('gridzona').getView().getSelectionModel().getSelection();
    
    //segmentar intervalo nuevo para validacion
    var arrayIntervalos   = intervaloNuevo.split(" - ");
    
    var horaInicioNueva   = arrayIntervalos[0];
    var horaFinNueva      = arrayIntervalos[1];
    
    var idZonaNueva       = parseInt(selectedZona[0].data.id);
    
    var grid              = Ext.getCmp('gridResumen');
    var gridPlanificados  = Ext.getCmp('gridConsulta');
    
    //Recorro las cuadrillas escogidas y comparo con las registradas para validar si puede o no ser agregado
    //a la planificacion
    Ext.each(selectedCuadrilla, function (item) 
    {
        var cuadrillaNueva   = item.data.nombre;
        var idCuadrillaNueva = item.data.id;
        
        mensaje += validarCombinacionesPorGrid( grid, 
                                                idIntervaloNuevo, 
                                                cuadrillaNueva, 
                                                idZonaNueva, 
                                                fechaInicioNuevo, 
                                                fechaFinNuevo, 
                                                horaInicioNueva, 
                                                horaFinNueva,
                                                'nuevo',
                                                false,
                                                fechaInicio,
                                                fechaFin,
                                                idCuadrillaNueva
                                               );
                                         
        //Consulta sobre lo ingresado para un Cuadrilla
        mensaje += validarCombinacionesPorGrid( gridPlanificados, 
                                                idIntervaloNuevo, 
                                                cuadrillaNueva, 
                                                idZonaNueva, 
                                                fechaInicioNuevo, 
                                                fechaFinNuevo, 
                                                horaInicioNueva, 
                                                horaFinNueva,
                                                'planificado',
                                                false,
                                                fechaInicio,
                                                fechaFin,
                                                idCuadrillaNueva
                                               );
    });
    
    return mensaje;
}

//Obtener el string de fecha en formato estandar
function getDate(date)
{
    var day = date.getDate().toString();    

    var month = (date.getMonth()+1).toString();    

    if(day.length === 1)
    {
        day = '0'+day;
    }
    
    if(month.length === 1)
    {
        month = '0'+month;
    }
    
    return date.getFullYear() +"-"+ month + '-' + day;
}

function validarCombinacionesPorGrid(grid, 
                                     idIntervaloNuevo, 
                                     cuadrillaNueva, 
                                     idZonaNueva, 
                                     fechaInicioNuevo, 
                                     fechaFinNuevo, 
                                     horaInicioEnviada, 
                                     horaFinEnviada,
                                     tipo,
                                     esActualizacionHoras,
                                     dateFechaInicio,
                                     dateFechaFin,
                                     idCuadrillaNueva
                                    )
{
    var mensaje = '';
    
    var msgAdicional = tipo==='planificado'?' (<b style="color:#3a87ad;">Sobre Planificación Guardada</b>)':
                                            ' (<b style="color:green;">Sobre Planificación Reservada</b>)';

    for (var i = 0; i < grid.getStore().getCount(); i++)
    {                        
        if(tipo === 'planificado')
        {
            var fechaIni    = parseInt(new Date(grid.getStore().getAt(i).data.fechaInicio.split(" ")[0]).getTime());
            var fechaFin    = parseInt(new Date(grid.getStore().getAt(i).data.fechaFin.split(" ")[0]).getTime());
            var fechaActual = grid.getStore().getAt(i).data.fechaInicio.split(" ")[0]+" - "+grid.getStore().getAt(i).data.fechaFin.split(" ")[0];
        }
        else
        {
            var fechaIni    = parseInt(new Date(grid.getStore().getAt(i).data.fechaInicio).getTime());
            var fechaFin    = parseInt(new Date(grid.getStore().getAt(i).data.fechaFin).getTime());
            var fechaActual = grid.getStore().getAt(i).data.fechaInicio+" - "+grid.getStore().getAt(i).data.fechaFin;
        }                

        var cuadrilla   = grid.getStore().getAt(i).data.cuadrilla;
        var idZona      = parseInt(grid.getStore().getAt(i).data.idZona);
        var idIntervalo = parseInt(grid.getStore().getAt(i).data.idIntervalo);

        //Informacion generada o reservada a mostrar al usuario para indicar el motivo de la validacion
        var intervalo   = grid.getStore().getAt(i).data.intervalo;
        var zona        = grid.getStore().getAt(i).data.zona;

        if(esActualizacionHoras)
        {
            if(idIntervalo === idIntervaloNuevo && idZonaNueva === idZona && cuadrilla === cuadrillaNueva)
            {
                continue;
            }            
        }

        var motivo      = '<br/><table>\n\
                                <tr colspan="5"><td><b>Datos de Planificación Existente o reservada con la que existen cruces:</b></td></tr>\n\
                                <tr><td><b>Zona:</b></td><td>&nbsp;</td><td>'+zona+'</td></tr>\n\
                                <tr><td><b>Fechas Trabajo:</b></td><td>&nbsp;</td><td>'+fechaActual+'</td></tr>\n\
                                <tr><td><b>Intervalo:</b></td><td>&nbsp;</td><td>'+intervalo+'</td></tr>\n\
                               </table>';

        //Cuando se trata de la misma cuadrilla
        if(cuadrilla === cuadrillaNueva)
        {
            //Horas existentes
            var arrayIntervalos   = intervalo.split(" - ");
            var horaInicio        = (getHoraAsTime(arrayIntervalos[0]));
            var horaFin           = (getHoraAsTime(arrayIntervalos[1]));

            //Horas enviadas a validar
            var horaInicioNueva   = (getHoraAsTime(horaInicioEnviada));
            var horaFinNueva      = (getHoraAsTime(horaFinEnviada));   

            if(fechaInicioNuevo > fechaFin && fechaFinNuevo > fechaFin || 
                fechaFinNuevo < fechaIni && fechaInicioNuevo < fechaIni )
            {
                continue;
            }


            //Si la zona es igual
            if(idZonaNueva === idZona)
            {
                //Si el rango de fechas de trabajo es igual 
                if(fechaInicioNuevo === fechaIni && fechaFinNuevo === fechaFin)
                {
                    if(idIntervaloNuevo === idIntervalo)
                    {
                        mensaje += "<br/><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;"+
                                   "La Cuadrilla <b>"+cuadrilla+"</b> ya fue planificada dentro de una misma Zona, \n\
                                    Rango de fechas e intervalo. "+msgAdicional+motivo;
                    }
                    else if( !esIntervaloCorrecto(horaInicioNueva, horaFinNueva, horaInicio, horaFin) )
                    {
                        mensaje += "<br/><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;" +
                                   "La Cuadrilla <b>" + cuadrilla + "</b> tiene cruces de Intervalo dentro de la misma Zona y \n\
                                    Rango de fechas de trabajo. "+msgAdicional+motivo;
                    }
                }
                else//si el rango de fechas es distinto completamente o existen cruces se validara los intervalos
                {                    
                    //si los rangos con cruzados se valida que los intervalos no se crucen
                    if(!esRangoFechaTrabajoCorrecto(fechaInicioNuevo, fechaFinNuevo, fechaIni, fechaFin))
                    {
                        if(idIntervaloNuevo === idIntervalo || !esIntervaloCorrecto(horaInicioNueva, horaFinNueva, horaInicio, horaFin))
                        {
                            mensaje += "<br/><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;"+
                                       "La Cuadrilla <b>"+cuadrilla+"</b> tiene cruce de Intervalo y fechas de trabajo dentro de la misma Zona \n\
                                        Intervalo"+msgAdicional+motivo;
                        }
                    }
                    else//si las fechas de los bordes son iguales se verificará si las horas no se cruzan
                    {                      
                        //Si los intervalos de tiempo se cruzan o son iguales
                        if(idIntervaloNuevo === idIntervalo || !esIntervaloCorrecto(horaInicioNueva, horaFinNueva, horaInicio, horaFin) )
                        {
                            mensaje += "<br/><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;"+
                                       "La Cuadrilla <b>"+cuadrilla+"</b> tiene cruce de Horario \n\
                                        dentro de un mismo día de trabajo en la misma Zona. \n\
                                        Intervalo"+msgAdicional+motivo;
                        }
                    }
                }
            }
            else//si las zonas son diferentes
            {                
                if(fechaInicioNuevo === fechaIni && fechaFinNuevo === fechaFin)
                {
                    if(idIntervaloNuevo === idIntervalo || !esIntervaloCorrecto(horaInicioNueva, horaFinNueva, horaInicio, horaFin))
                    {
                        mensaje += "<br/><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;"+
                                   "La Cuadrilla <b>"+cuadrilla+"</b> no puede ser planificada dentro dos intervalos que se cruzan en \n\
                                    dos Zonas diferentes."+msgAdicional+motivo;
                    }
                }
                else
                {
                    //si los rangos se cruzan se verifica que los intervalos no se crucen
                    if( !esRangoFechaTrabajoCorrecto(fechaInicioNuevo , fechaFinNuevo, fechaIni, fechaFin) )
                    {
                        if( !esIntervaloCorrecto(horaInicioNueva, horaFinNueva, horaInicio, horaFin) )
                        {
                            mensaje += "<br/><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;" +
                                       "La Cuadrilla <b>" + cuadrilla + "</b> tiene cruces de Intervalo dentro de dos Zonas \n\
                                        en Fechas de trabajo cruzadas."+msgAdicional+motivo;
                        }
                    }
                    else//si las fechas de los bordes son iguales se verificará si las horas no se cruzan
                    {
                        if(idIntervaloNuevo === idIntervalo || !esIntervaloCorrecto(horaInicioNueva, horaFinNueva, horaInicio, horaFin) )
                        {
                            mensaje += "<br/><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;"+
                                       "La Cuadrilla <b>"+cuadrilla+"</b> tiene cruce de Horario \n\
                                        dentro de un mismo día de trabajo en dos Zonas distintas."+msgAdicional+motivo;
                        }
                    }
                }
            }
        }
    }


    //Validamos si las fechas a crear ya se encuentran planificadas
    if (tipo === 'planificado' && !esActualizacionHoras && !Ext.isEmpty(mensaje))
    {
        Ext.get("panelPlanificacion").mask('Validando...');

        Ext.Ajax.request(
        {
            url    : urlValidarFechasPlanif,
            method : 'post',
            async  : false,
            params :
            {
                idCuadrilla : idCuadrillaNueva,
                idIntervalo : idIntervaloNuevo,
                fechaInicio : dateFechaInicio,
                fechaFin    : dateFechaFin,
                horaInicio  : horaInicioEnviada,
                horaFin     : horaFinEnviada
            },
            success: function(response)
            {
                Ext.get("panelPlanificacion").unmask();

                var responseJson = Ext.JSON.decode(response.responseText);

                if (responseJson.success)
                {
                    mensaje = '';
                }
            }
        });
    }

    return mensaje;
}

function esRangoFechaTrabajoCorrecto(fechaInicioNuevo , fechaFinNuevo, fechaIni, fechaFin)
{
    if(fechaInicioNuevo >= fechaFin && fechaFinNuevo > fechaFin || 
       fechaFinNuevo <= fechaIni && fechaInicioNuevo < fechaIni )
    {
        return true;
    }
    else if(fechaInicioNuevo < fechaIni && (fechaFinNuevo < fechaFin && fechaFinNuevo > fechaIni)     ||
        (fechaInicioNuevo > fechaIni && fechaInicioNuevo < fechaFin) && fechaFinNuevo > fechaFin ||
        fechaInicioNuevo >= fechaIni && fechaFinNuevo < fechaFin ||
        fechaInicioNuevo > fechaIni && fechaFinNuevo <= fechaFin ||
        fechaInicioNuevo < fechaIni && fechaFinNuevo > fechaFin
        )
    {
        return false;
    }
}

function esIntervaloCorrecto(horaInicioNueva, horaFinNueva, horaInicio, horaFin)
{
    if( horaInicioNueva >= horaFin   && horaFinNueva > horaFin || 
        horaInicioNueva < horaInicio && horaFinNueva <= horaInicio)
    {
        return true;
    }
    else if( horaInicioNueva < horaInicio && horaFinNueva <= horaFin ||
        horaInicioNueva > horaInicio && horaFinNueva <= horaFin ||
        horaInicioNueva >= horaInicio && horaFinNueva > horaFin ||
        horaInicioNueva >= horaInicio && horaFinNueva < horaFin ||
        horaInicioNueva < horaInicio && horaFinNueva > horaFin  ||
        horaInicioNueva < horaInicio && (horaFinNueva < horaFin && horaFinNueva > horaInicio) ||
        (horaInicioNueva > horaInicio && horaInicioNueva < horaFin) && horaFinNueva > horaFin)
    {
        return false;
    }
}

function getHoraAsTime(fecha)
{
    var date       = new Date();
    var arrayHora  = fecha.split(":");
          
    var hora = new Date(date.setHours(arrayHora[0],arrayHora[1],0,0));
    return hora;
}

//Calcula las horas limites para actualizacion de horas de trabajo de acuerdo a un factor divisible para 60 minutos
function getHoraLimiteRegulada(hora, accion)
{
    var arrayHora = hora.split(":");
    
    var minutos = '';
    var horas   = '';
    
    switch(arrayHora[1])
    {
        case '01':
            minutos = '00';
            break;            
        case '16':
            minutos = '15';
            break;
        case '31':
            minutos = '30';
            break;
        case '46':
            minutos = '45';
            break;
        default :
            minutos = arrayHora[1]+"";
            break;
    }
    
    if(accion === '+')
    {                
        minutos = parseInt(minutos) + indicadorIntervalo;
        
        if(minutos === 60)
        {
            if(parseInt(arrayHora[0]) !== 23)
            {
                horas = parseInt(arrayHora[0]) + 1;
                minutos = 0;
            }
            else
            {
                horas = arrayHora[0]+"";
                minutos = 45;
            }      
        }   
        else
        {
            horas = arrayHora[0]+"";
        }
    }
    else
    {
        if(minutos === '00')
        {
            if(parseInt(arrayHora[0]) === 0)
            {
                horas   = arrayHora[0]+"";
                minutos = 15;    
            }
            else
            {
                horas   = parseInt(arrayHora[0]) - 1;
                minutos = 60;
            }            
        }
        else
        {
            horas   = arrayHora[0]+"";
        }
        minutos = parseInt(minutos) - indicadorIntervalo
    }

    if(minutos.toString().length === 1)
    {
        minutos = "0"+minutos;
    }
    
    if(horas+"".lenght === 1)
    {
        horas = "0"+horas;
    }
    
    var horaActualizada = horas+":"+minutos;

    return horaActualizada;
}

//Limpiar datos de los formularios de creacion de la planificacion
function limpiar()
{
    Ext.getCmp('gridcuadrilla').getSelectionModel().deselectAll();
    Ext.getCmp('gridcuadrilla').getSelectionModel().clearSelections();
    Ext.getCmp('gridzona').getSelectionModel().deselectAll();
    Ext.getCmp('gridzona').getSelectionModel().clearSelections();
    //Ext.getCmp("txtFechaInicio").setValue("");
    //Ext.getCmp("txtFechaFin").setValue("");
    Ext.getCmp("hiddenIntervalo").setValue("");
    Ext.getCmp("txtIntervalo").setValue("");

    Ext.getCmp("btnCrearPlanificacion").setDisabled(false);
}

//Filtra y busca dentro de la agenda segun la cuadrilla enviada
function buscarPlanificacionCuadrilla()
{
    var cuadrilla = Ext.getCmp("comboCuadrillas").getValue();
    var zona      = Ext.getCmp("comboZonas").getValue();
    var fechaIni  = Ext.getCmp('dateFechaTrabajoIniAgenda').getValue();
    var fechaFin  = Ext.getCmp('dateFechaTrabajoFinAgenda').getValue();
    var strEsConsulta = 'SI';

    if (!Ext.isEmpty(fechaIni) && !Ext.isEmpty(fechaFin))
    {
        if (fechaFin.getTime() < fechaIni.getTime())
        {
            Ext.Msg.alert('Alerta', 'La fecha fin no puede ser menor a la fecha inicio..!!');
            return;
        }

        if(getDiferenciaTiempo(fechaIni,fechaFin) > 31)
        {
            Ext.Msg.alert('Alerta ', "El rango de fechas elegidas no puede superar un máximo de 30 días..!!");
            return;
        }
    }

    if (Ext.isEmpty(fechaIni)) {
        fechaIni = '';
    } else {
        var dateIni = getDate(fechaIni).split("-");
        fechaIni    = (String)(dateIni[0] + "-" + dateIni[1] + "-" + dateIni[2]);
    }

    if (Ext.isEmpty(fechaFin)) {
        fechaFin = '';
    } else {
        var dateFin = getDate(fechaFin).split("-");
        fechaFin    = (String)(dateFin[0] + "-" + dateFin[1] + "-" + dateFin[2]);
    }

    if (Ext.isEmpty(cuadrilla)) {
        cuadrilla = '';
    }

    if (Ext.isEmpty(zona)) {
        zona = '';
    }

    showCalendario(cuadrilla,zona,fechaIni,fechaFin, strEsConsulta);
}