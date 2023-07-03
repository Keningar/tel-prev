var store = null;
var grid  = null;
var win   = null;

Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

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
                       msg: 'Grabando los datos, Por favor espere!!',
                       progressText: 'Saving...',
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
    Ext.tip.QuickTipManager.init();
         
    Ext.define('ModelStore', 
    {
        extend: 'Ext.data.Model',
        fields:
        [				
            {name:'intIdElemento',                      mapping:'intIdElemento'},
            {name:'strNombreElemento',                  mapping:'strNombreElemento'},
            {name:'strSerieLogica',                     mapping:'strSerieLogica'},
            {name:'strSerieFisica',                     mapping:'strSerieFisica'},
            {name:'strEstadoElemento',                  mapping:'strEstadoElemento'},
            {name:'strResponsableTablet',               mapping:'strResponsableTablet'},
            {name:'strRegionResponsableTablet',         mapping:'strRegionResponsableTablet'},
            {name:'strCantonResponsableTablet',         mapping:'strCantonResponsableTablet'},
            {name:'strDepartamentoResponsableTablet',   mapping:'strDepartamentoResponsableTablet'},
            {name:'strFechaCreacion',                   mapping:'strFechaCreacion'},
            {name:'strTipoElemento',                    mapping:'strTipoElemento'},
            {name:'strUrlShow',                         mapping:'strUrlShow'},
            {name:'strUrlEdit',                         mapping:'strUrlEdit'}
        ],
        idProperty: 'intIdElemento'
    });
	
    store = new Ext.data.Store
    ({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url : strUrlGridTablets,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        autoLoad: true
    });

	
    var selModel = Ext.create('Ext.selection.CheckboxModel', 
        {
            listeners: 
            {
                selectionchange: function(sm, selections)
                {
                    grid.down('#deleteAjax').setDisabled(selections.length == 0);
                }
            }
        });

    var eliminarBtn = Ext.create('Ext.button.Button', 
        {
            iconCls: 'icon_delete',
            text: 'Eliminar',
            itemId: 'deleteAjax',
            scope: this,
            disabled: true,
            handler: function()
            { 
                eliminarAlgunos();
            }
        });
	
    var toolbar = Ext.create('Ext.toolbar.Toolbar', 
        {
            dock: 'top',
            align: '->',
            items   : 
            [ 
                { xtype: 'tbfill' },
                eliminarBtn
            ]
        });

    grid = Ext.create('Ext.grid.Panel', 
    {
        id : 'grid',
        width: 980,
        height: 400,
        store: store,
        selModel: selModel,
        plugins: [{ ptype : 'pagingselectpersist' }],
        style: 'margin:0 auto;',
        viewConfig: 
        {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
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
        },
        dockedItems: [ toolbar ], 
        columns:
        [
            {
              id: 'intIdElemento',
              header: 'intIdElemento',
              dataIndex: 'intIdElemento',
              hidden: true,
              hideable: false
            },
            {
              id: 'strNombreElemento',
              header: 'IMEI',
              dataIndex: 'strNombreElemento',
              width: 120,
              sortable: true
            },
            {
              id: 'strSerieLogica',
              header: 'PUBLISH ID',
              dataIndex: 'strSerieLogica',
              width: 250,
              hideable: false
            },
            {
              id: 'strResponsableTablet',
              header: 'Responsable',
              dataIndex: 'strResponsableTablet',
              width: 200,
              sortable: true
            },
            {
              id: 'strRegionResponsableTablet',
              header: 'Región',
              dataIndex: 'strRegionResponsableTablet',
              width: 50,
              sortable: true
            },
            {
              id: 'strCantonResponsableTablet',
              header: 'Cantón',
              dataIndex: 'strCantonResponsableTablet',
              width: 80,
              sortable: true
            },
            {
              id: 'strDepartamentoResponsableTablet',
              header: 'Departamento',
              dataIndex: 'strDepartamentoResponsableTablet',
              width: 190,
              sortable: true
            },
            {
              id: 'strEstadoElemento',
              header: 'Estado',
              dataIndex: 'strEstadoElemento',
              width: 60,
              sortable: true
            },
            {
              id: 'strFechaCreacion',
              header: 'Fecha Creación',
              dataIndex: 'strFechaCreacion',
              width: 100,
              sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 150,
                items: 
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-show';

                            if(rec.get('strUrlShow') == "") 
                            {
                                strClassButton        = '';
                                this.items[0].tooltip = '';
                            }
                            else 
                            {
                                this.items[0].tooltip = 'Ver';
                            }

                            return strClassButton;
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec       = store.getAt(rowIndex);
                            var strUrlVer = rec.get('strUrlShow');

                            if(strUrlVer != "")
                            {
                                window.location = strUrlVer;
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-edit';

                            if(rec.get('strUrlEdit') == "") 
                            {
                                strClassButton        = '';
                                this.items[0].tooltip = '';
                            }
                            else 
                            {
                                this.items[0].tooltip = 'Editar';
                            }

                            return strClassButton;
                        },
                        tooltip: 'Editar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec          = store.getAt(rowIndex);
                            var strUrlEditar = rec.get('strUrlEdit');

                            if(strUrlEditar != "")
                            {
                                window.location = strUrlEditar;
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-delete';

                            if(rec.get('strUrlEdit') == "") 
                            {
                                strClassButton        = '';
                                this.items[0].tooltip = '';
                            }
                            else 
                            {
                                this.items[0].tooltip = 'Eliminar';
                            }

                            return strClassButton;
                        },
                        tooltip: 'Eliminar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec             = store.getAt(rowIndex);
                            var intIdElemento   = rec.get('intIdElemento');
                            var arrayParametros = [];
                            
                            arrayParametros['tablet'] = intIdElemento;

                            verificarElementosAEliminar(arrayParametros);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-inactivarTablet';
                            var permiso = $("#ROLE_314-4957");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "")
                            {
                                this.items[0].tooltip = '';
                            }   
                            else
                            {
                                if(rec.get('strEstadoElemento')=='Activo')
                                {
                                    this.items[0].tooltip = 'Inactivar';
                                }
                                else
                                {
                                    strClassButton          = "";
                                    this.items[0].tooltip   = '';
                                }

                            }
                            return strClassButton;
                        },
                        tooltip: 'Inactivar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var strClassButton = 'button-grid-inactivarTablet';
                            var permiso = $("#ROLE_314-4957");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton != "")
                            {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('strEstadoElemento')=='Activo')
                                {
                                    var arrayParametros                 = [];
                                    arrayParametros['intIdElemento']    = rec.get('intIdElemento');
                                    arrayParametros['IMEI']             = rec.get('strNombreElemento');
                                    arrayParametros['responsable']      = rec.get('strResponsableTablet');
                                    inactivarTablet(arrayParametros);
                                }
                                
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-reactivarTablet';
                            var permiso = $("#ROLE_314-4977");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "")
                            {
                                this.items[0].tooltip = '';
                            }   
                            else
                            {
                                if(rec.get('strEstadoElemento')=='Inactivo')
                                {
                                    this.items[0].tooltip = 'Reactivar';
                                }
                                else
                                {
                                    strClassButton          = "";
                                    this.items[0].tooltip   = '';
                                }

                            }
                            return strClassButton;
                        },
                        tooltip: 'Reactivar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var strClassButton = 'button-grid-reactivarTablet';
                            var permiso = $("#ROLE_314-4977");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton != "")
                            {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('strEstadoElemento')=='Inactivo')
                                {
                                    var arrayParametros                 = [];
                                    arrayParametros['intIdElemento']    = rec.get('intIdElemento');
                                    reactivarTablet(arrayParametros);
                                }
                                
                            }
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', 
        {
            bodyPadding: 7, 
            border:false,
            buttonAlign: 'center',
            layout: 
            {
                type: 'hbox',
                align: 'stretch'
            },
            bodyStyle: 
            {
                background: '#fff'
            },   
            collapsible : true,
            collapsed: true,
            width: 980,
            title: 'Criterios de busqueda',
            buttons: 
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function()
                    { 
                        buscar();
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function()
                    { 
                        limpiar();
                    }
                }
            ],                
            items: 
            [
                { width: '5%',border:false},
                {
                    xtype: 'textfield',
                    id: 'strImei',
                    fieldLabel: 'IMEI',
                    value: '',
                    width: '200',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keypress: function(form, e)
                        {
                            return validarSoloNumeros(e);
                        }
                    }
                },
                { width: '5%',border:false},
                {
                    xtype: 'textfield',
                    id: 'strPublishIndex',
                    fieldLabel: 'PUBLISH ID',
                    value: '',
                    width: '200',
                    enableKeyEvents: true,
                },
                { width: '5%',border:false},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Estado',
                    id: 'strEstado',
                    value:'',
                    store: [
                        ['','Activo e Inactivo'],
                        ['Activo','Activo'],
                        ['Inactivo','Inactivo'],
                        ['Eliminado','Eliminado']
                    ],
                    width: '200'
                },
            ],	
            renderTo: 'filtro'
        }); 
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
function buscar()
{
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.imei         = Ext.getCmp('strImei').value;
    store.getProxy().extraParams.serieLogica  = Ext.getCmp('strPublishIndex').value;
    store.getProxy().extraParams.strEstado    = Ext.getCmp('strEstado').value;
    
    store.load();
}


function limpiar()
{
    Ext.getCmp('strImei').value="";
    Ext.getCmp('strImei').setRawValue("");
    
    Ext.getCmp('strPublishIndex').value="";
    Ext.getCmp('strPublishIndex').setRawValue("");
    
    Ext.getCmp('strEstado').value="";
    Ext.getCmp('strEstado').setRawValue("Activo e Inactivo");
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.imei         = Ext.getCmp('strImei').value;
    store.getProxy().extraParams.serieLogica  = Ext.getCmp('strPublishIndex').value;
    store.getProxy().extraParams.strEstado    = Ext.getCmp('strEstado').value;
    store.load();
}


function eliminarAlgunos()
{
    var param            = '';
    var intSeleccionados = 0;
    var selection        = grid.getPlugin('pagingSelectionPersistence').getPersistedSelection();

    if(selection.length > 0)
    {
        for(var i=0 ;  i < selection.length ; ++i)
        {
            param = param + selection[i].getId();

            if(i < (selection.length -1))
            {
                param = param + '|';
            }

            intSeleccionados++;
        }
        
        if( intSeleccionados > 0 )
        {
            var arrayParametros                    = [];
                arrayParametros['tablet'] = param;

            eliminarAccionAjax(arrayParametros);
        }
        else
        {
            Ext.Msg.alert('Error ','Debe seleccionar tablets válidas para eliminar');
        } 
    }
    else
    {
        Ext.Msg.alert('Error ','Seleccione por lo menos una tablet de la lista');
    }
}

function eliminarAlgunos()
{
    var param            = '';
    var intSeleccionados = 0;
    var selection        = grid.getPlugin('pagingSelectionPersistence').getPersistedSelection();

    if(selection.length > 0)
    {
        for(var i=0 ;  i < selection.length ; ++i)
        {
            param = param + selection[i].getId();

            if(i < (selection.length -1))
            {
                param = param + '|';
            }

            intSeleccionados++;
        }
        
        if( intSeleccionados > 0 )
        {
            var arrayParametros           = [];
                arrayParametros['tablet'] = param;

            verificarElementosAEliminar(arrayParametros);
        }
        else
        {
            Ext.Msg.alert('Error ','Debe seleccionar tablets válidas para eliminar');
        } 
    }
    else
    {
        Ext.Msg.alert('Error ','Seleccione por lo menos una tablet de la lista');
    }
}


function verificarElementosAEliminar(arrayParametros)
{            
    Ext.MessageBox.wait("Verificando datos...");										
                                        
    Ext.Ajax.request
    ({
        url: strUrlVerificarElementosAEliminar,
        method: 'post',
        timeout: 900000,
        params: 
        { 
            tablet: arrayParametros['tablet']
        },
        success: function(response)
        {
            var text = response.responseText;
            
            Ext.MessageBox.hide();
            
            if(text === "OK")
            {
                eliminarAccionAjax(arrayParametros);
            }
            else
            {
                Ext.Msg.alert('Error', text); 
            }
        },
        failure: function(result)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.responseText);
        }
    });
}


function eliminarAccionAjax(arrayParametros)
{
    Ext.Msg.confirm('Alerta','Se eliminará(n) la(s) tablet(s) seleccionada(s). Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlEliminarTablets,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    tablet : arrayParametros['tablet']
                },
                success: function(result)
                {
                    if( "OK" == result.responseText )
                    {
                        Ext.Msg.alert('Información', 'Tablet(s) eliminada(s) con éxito');
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


function inactivarTablet(arrayParametros)
{
    var storeMotivosInactivar = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetMotivos,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:
                {
                    strAccion: 'inactivar', 
                    strModulo: 'tablet'
                }
            },
            fields:
            [
                {name: 'intIdMotivoInactivar', mapping: 'intIdMotivo'},
                {name: 'strMotivoInactivar',   mapping: 'strMotivo'}
            ],
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0, 
                     [
                        {
                            strMotivo: 'Seleccione',
                            intIdMotivo: ''
                        }
                     ]);
                }      
            }
        });

    var formPanelInactivarTablet = Ext.create('Ext.form.Panel',
    {
        id: 'formPanelInactivarTablet',
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: 
        {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: 
        [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:
                {
                    width: 450
                },
                items:
                [
                    {
                        xtype: 'displayfield',
                        id: 'strIMEI',
                        name: 'strIMEI',
                        fieldLabel: '<b>IMEI </b>',
                        value: arrayParametros['IMEI'],
                        labelWidth: 150
                    },
                    {
                        xtype: 'displayfield',
                        id: 'strResponsable',
                        name: 'strResponsable',
                        fieldLabel: '<b>Responsable </b>',
                        value: arrayParametros['responsable'],
                        labelWidth: 150
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: '<b>Motivo </b>',
                        id: 'cmbMotivoInactivar',
                        name: 'cmbMotivoInactivar',
                        store: storeMotivosInactivar,
                        displayField: 'strMotivoInactivar',
                        valueField: 'intIdMotivoInactivar',
                        queryMode: 'remote',
                        emptyText: 'Seleccione',
                        forceSelection: true,
                        labelWidth: 150
                    }
                ]
            }
        ],
        buttons:
        [
            {
                text: 'Aceptar',
                type: 'submit',
                handler: function()
                {
                    
                    var intIdMotivoInactivar = Ext.getCmp('cmbMotivoInactivar').getValue();

                    if ( intIdMotivoInactivar != null && intIdMotivoInactivar != '' )
                    {
                        connEsperaAccion.request
                        ({
                            url: strUrlInactivarTablet,
                            method: 'post',
                            dataType: 'json',
                            params:
                            { 
                                intIdElemento: arrayParametros['intIdElemento'],
                                intIdMotivoInactivar: intIdMotivoInactivar
                            },
                            success: function(response)
                            {
                                if ( typeof winInactivacion != 'undefined' && winInactivacion != null )
                                {
                                    winInactivacion.destroy();
                                }

                                if( "OK" == response.responseText )
                                {
                                    Ext.Msg.alert('Información', 'Se ha Inactivado correctamente la tablet');

                                    store.load();
                                }
                                else
                                {
                                    Ext.Msg.alert('Error', response.responseText);
                                }
                            },
                            failure: function(result)
                            {
                                if ( typeof winInactivacion != 'undefined' && winInactivacion != null )
                                {
                                    winInactivacion.destroy();
                                }

                                Ext.Msg.alert('Error',result.responseText); 
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un motivo');
                    }
                }
            },
            {
                text: 'Cerrar',
                handler: function()
                {
                    win.destroy();
                }
            }
        ]
    });

    winInactivacion = Ext.create('Ext.window.Window',
    {
         title: 'Inactivar Tablet ',
         modal: true,
         width: 500,
         closable: true,
         layout: 'fit',
         items: [formPanelInactivarTablet]
    }).show();
}


function reactivarTablet(arrayParametros)
{
    Ext.Msg.confirm('Alerta','Se reactivará la tablet seleccionada. Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlReactivarTablet,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    intIdElemento : arrayParametros['intIdElemento']
                },
                success: function(result)
                {
                    if( "OK" == result.responseText )
                    {
                        Ext.Msg.alert('Información', 'Tablet reactivada con éxito');
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