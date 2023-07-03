Ext.require
([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();

var itemsPerPage = 15;
var store        = '';
var estado_id    = '';

var connEsperaAccion = new Ext.data.Connection
    ({
	listeners:
        {
            'beforerequest': 
            {
                fn: function (con, opt)
                {						
                    Ext.MessageBox.show
                    ({
                       msg: 'Eliminando el reporte, Por favor espere!!',
                       progressText: 'Guardando...',
                       width:300,
                       wait:true,
                       waitConfig: {interval:200}
                    });
                },
                scope: this
            },
            'requestcomplete':
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': 
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
	}
    });
    

Ext.onReady(function()
{
    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'intIdTransaccion',     mapping: 'intIdTransaccion'},
            {name: 'strNombreTransaccion', mapping: 'strNombreTransaccion'},
            {name: 'strTipoTransaccion',   mapping: 'strTipoTransaccion'},
            {name: 'strEstado',            mapping: 'strEstado'},
            {name: 'strEmpresa',           mapping: 'strEmpresa'},
            {name: 'strFechaCreacion',     mapping: 'strFechaCreacion'},
            {name: 'strUsuarioCreacion',   mapping: 'strUsuarioCreacion'},
            {name: 'strUrlDescargar',      mapping: 'strUrlDescargar'}
        ]
    });


    store = Ext.create('Ext.data.JsonStore', 
    {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        autoLoad: true,
        timeout: 9000000,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGridReportesBuro,
            reader: 
            {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams:
            {
                tipo: 'usuario'
            },
            simpleSortMode: true
        }
    });


    var listView = Ext.create('Ext.grid.Panel', 
    {
        width: 800,
        height: 365,
        collapsible: false,
        title: '',
        dockedItems:
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: 
            [
                {xtype: 'tbfill'},
            ]
        }],
        renderTo: Ext.get('lista_reportes'),
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando registros {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
        viewConfig: 
        {
            emptyText: 'No hay datos para mostrar'
        },
        columns: 
        [
            new Ext.grid.RowNumberer(),
            {
                text: 'Archivo',
                width: 450,
                dataIndex: 'strNombreTransaccion'
            }, 
            {
                text: 'Fecha Creación',
                width: 128,
                dataIndex: 'strFechaCreacion'
            },
            {
                text: 'Usuario',
                width: 90,
                dataIndex: 'strUsuarioCreacion'
            },
            {
                text: '',
                dataIndex: 'strEstado',
                align: 'right',
                width: 22,
                renderer: renderAccionEjecutando
            },
            {
                text: 'Acciones',
                width: 85,
                renderer: renderAcciones
            }
        ],
        listeners: 
        {
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex,
                                        columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                    if (columnText)
                                    {
                                        tip.update(columnText);
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });
            }
        }
    });
    
                
    function renderAccionEjecutando(value, p, record)
    {
        var iconos = '';

        if(record.data.strEstado == 'Pendiente')
        {
            iconos = iconos + iconoEjecutando;   
        }

        return Ext.String.format(iconos,value);
    }
    
         
    function renderAcciones(value, p, record) 
    {
        var permisoDescargar     = $("#ROLE_339-3577");
        var boolPermisoDescargar = (typeof permisoDescargar === 'undefined') ? false : (permisoDescargar.val() == 1 ? true : false);          
        var permisoEliminar      = $("#ROLE_339-9");
        var boolPermisoEliminar  = (typeof permisoEliminar === 'undefined') ? false : (permisoEliminar.val() == 1 ? true : false);          
                            
        var iconos = '';
        
        if(record.data.strEstado == 'Activo')
        {
            if(boolPermisoDescargar)
            {
                iconos = iconos + '<b><a href='+record.data.strUrlDescargar
                                  + ' title="Descargar Reporte" class="button-grid-zip"></a></b>';
            }
        }
        
        if(boolPermisoEliminar)
        {    
            iconos = iconos + '<b><a href="#" onClick="eliminarReporte(\''+record.data.intIdTransaccion+'\')" title="Eliminar Reporte" '
                              + 'class="button-grid-eliminar"></a></b>';
        }
        
        return Ext.String.format(iconos,value);
    }


    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout:
        {
            type: 'table',
            columns: 4,
            align: 'left'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        defaults: 
        {
            bodyStyle: 'padding:10px'
        },
        collapsible: true,
        collapsed: false,
        width: 800,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function()
                {
                    buscar('usuario');
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                {
                    limpiar('usuario');
                }
            }
        ],
        items: 
        [
            {
                xtype: 'textfield',
                fieldLabel: 'Fecha',
                labelAlign: 'left',                
                name: 'dateBusqueda',
                id: 'dateBusqueda'
            }
        ],
        renderTo: 'filtro_reportes'
    });

    $('#dateBusqueda-inputEl').monthpicker({dateFormat: 'mm-yyyy'});

});
    
    
function descargarReporteBuro(idReporte)
{
    var boolEncontrado = false;
    
    store.each(function(record)
    {
        if(record.data.intIdTransaccion == idReporte)
        {
            document.getElementById('linkDownload').href = record.data.strUrlDescargar;
            document.getElementById('linkDownload').click();
            document.getElementById('linkDownload').href = "";
            
            boolEncontrado = true;
            
            return false;
        }
    });
    
    if( !boolEncontrado )
    {
        Ext.Msg.alert('Atención', 'No se ha encontrado el archivo seleccionado en nuestra base de datos');
    }
    
    document.getElementById('linkDownload').href = "";
}

function buscar(strTipo) 
{
    store.loadData([],false);
    cargarFiltrosBusquedaAlStore(strTipo);
    store.currentPage = 1;
    store.load();
}


function limpiar(strTipo) 
{
    document.getElementById('dateBusqueda-inputEl').value = '';

    buscar(strTipo);
}


function cargarFiltrosBusquedaAlStore(strTipo)
{
    var fechaSeleccionada = document.getElementById('dateBusqueda-inputEl').value;
    
    var arrayNombreMesesIngles               = new Array();
        arrayNombreMesesIngles['Enero']      = '01';
        arrayNombreMesesIngles['Febrero']    = '02';
        arrayNombreMesesIngles['Marzo']      = '03';
        arrayNombreMesesIngles['Abril']      = '04';
        arrayNombreMesesIngles['Mayo']       = '05';
        arrayNombreMesesIngles['Junio']      = '06';
        arrayNombreMesesIngles['Julio']      = '07';
        arrayNombreMesesIngles['Agosto']     = '08';
        arrayNombreMesesIngles['Septiembre'] = '09';
        arrayNombreMesesIngles['Octubre']    = '10';
        arrayNombreMesesIngles['Noviembre']  = '11';
        arrayNombreMesesIngles['Diciembre']  = '12';
    
    if( fechaSeleccionada != null && fechaSeleccionada != '' )
    {
        if( typeof fechaSeleccionada === 'string')
        {
            var arrayFechaSeleccionada = fechaSeleccionada.split(', ');

            fechaSeleccionada = Ext.Date.parse("01-"+arrayNombreMesesIngles[arrayFechaSeleccionada[0]]+"-"+arrayFechaSeleccionada[1],'d-m-Y');
        }
        else
        {
            fechaSeleccionada = '';
        }
    }
    
    store.getProxy().extraParams.fecha = fechaSeleccionada;
    store.getProxy().extraParams.tipo  = strTipo;
}


function eliminarReporte(idTransaccion)
{
    Ext.Msg.confirm('Alerta','Se eliminará el reporte seleccionado. Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlEliminarReporte,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    idTransaccion : idTransaccion
                },
                success: function(result)
                {
                    if( "OK" == result.responseText )
                    {
                        Ext.Msg.alert('Información', 'Reporte eliminado con éxito');
                    }
                    else
                    {
                        Ext.Msg.alert('Error ', result.responseText);
                    }

                    store.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            });
        }
    });
}

setInterval("buscar('automatico');", 120000);