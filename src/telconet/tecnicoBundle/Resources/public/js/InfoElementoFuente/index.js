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
            {name:'intIdElemento',     mapping:'intIdElemento'},
            {name:'strNombreElemento', mapping:'strNombreElemento'},
            {name:'strFechaCreacion',  mapping:'strFechaCreacion'},
            {name:'strTipoElemento',   mapping:'strTipoElemento'},
            {name:'strModeloElemento', mapping:'strModeloElemento'},
            {name:'strSerieFisica',    mapping:'strSerieFisica'},
            {name:'strUrlEdit',        mapping:'strUrlEdit'},
            {name:'strUrlShow',        mapping:'strUrlShow'}
        ],
        idProperty: 'intIdElemento'
    });
	
    store = new Ext.data.Store
    ({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            timeout: 600000,
            url : strUrlGridFuente,
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
                var permiso      = $("#ROLE_329-8");
                var boolPermiso  = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                if(!boolPermiso)
                { 
                    Ext.Msg.alert('Atención', 'USTED NO TIENE PRIVILEGIOS PARA REALIZAR ESTA FUNCION!!!');
                }
                else
                {
                    eliminarAlgunos();
                }
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
        width: 826,
        height: 400,
        store: store,
        selModel: selModel,
        plugins: [{ ptype : 'pagingselectpersist' }],
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
                header: 'Nombre',
                dataIndex: 'strNombreElemento',
                width: 350,
                sortable: true
            },
            {
                id: 'strModeloElemento',
                header: 'Modelo Elemento',
                dataIndex: 'strModeloElemento',
                width: 150,
                sortable: true
            },
            {
                id: 'strFechaCreacion',
                header: 'Fecha Creación',
                dataIndex: 'strFechaCreacion',
                width: 150,
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
                            var permiso        = $("#ROLE_329-6");
                            var boolPermiso    = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                            var strClassButton = 'button-grid-show';
                            
                            if(!boolPermiso)
                            { 
                                strClassButton = 'button-grid-invisible';
                            }
                            else
                            {
                                if(rec.get('strUrlShow') == "") 
                                {
                                    strClassButton        = '';
                                    this.items[0].tooltip = '';
                                }
                                else 
                                {
                                    this.items[0].tooltip = 'Ver';
                                }
                            }

                            return strClassButton;
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var permiso     = $("#ROLE_329-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            var rec         = store.getAt(rowIndex);
                            var strUrlVer   = rec.get('strUrlShow');          
                            
                            if(!boolPermiso)
                            { 
                                Ext.Msg.alert('Atención', 'USTED NO TIENE PRIVILEGIOS PARA REALIZAR ESTA FUNCION!!!');
                            }
                            else
                            {
                                if(strUrlVer != "")
                                {
                                    window.location = strUrlVer;
                                }
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso        = $("#ROLE_329-4");
                            var boolPermiso    = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                            var strClassButton = 'button-grid-edit';
                            
                            if(!boolPermiso)
                            { 
                                strClassButton = 'button-grid-invisible';
                            }
                            else
                            {
                                if(rec.get('strUrlEdit') == "") 
                                {
                                    strClassButton        = '';
                                    this.items[0].tooltip = '';
                                }
                                else 
                                {
                                    this.items[0].tooltip = 'Editar';
                                }
                            }

                            return strClassButton;
                        },
                        tooltip: 'Editar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var permiso      = $("#ROLE_329-4");
                            var boolPermiso  = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            var rec          = store.getAt(rowIndex);
                            var strUrlEditar = rec.get('strUrlEdit');          
                            
                            if(!boolPermiso)
                            { 
                                Ext.Msg.alert('Atención', 'USTED NO TIENE PRIVILEGIOS PARA REALIZAR ESTA FUNCION!!!');
                            }
                            else
                            {
                                if(strUrlEditar != "")
                                {
                                    window.location = strUrlEditar;
                                }
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso        = $("#ROLE_329-8");
                            var boolPermiso    = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                            var strClassButton = 'button-grid-delete';
                            
                            if(!boolPermiso)
                            { 
                                strClassButton = 'button-grid-invisible';
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
                            var permiso      = $("#ROLE_329-8");
                            var boolPermiso  = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            
                            if(!boolPermiso)
                            { 
                                Ext.Msg.alert('Atención', 'USTED NO TIENE PRIVILEGIOS PARA REALIZAR ESTA FUNCION!!!');
                            }
                            else
                            {
                                var rec             = store.getAt(rowIndex);
                                var intIdElemento   = rec.get('intIdElemento');
                                var arrayParametros = [];

                                arrayParametros['fuente'] = intIdElemento;

                                eliminarAccionAjax(arrayParametros);
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
            width: 826,
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
                    id: 'strNombre',
                    fieldLabel: 'Nombre',
                    value: '',
                    width: '250'
                },
                { width: '15%',border:false}
            ],	
            renderTo: 'filtro'
        }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
function buscar()
{
    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.nombre = Ext.getCmp('strNombre').value;
    
    store.load();
}


function limpiar()
{
    Ext.getCmp('strNombre').value="";
    Ext.getCmp('strNombre').setRawValue("");
    
    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.nombre = Ext.getCmp('strNombre').value;
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
            var arrayParametros        = [];
                arrayParametros['fuente'] = param;

            eliminarAccionAjax(arrayParametros);
        }
        else
        {
            Ext.Msg.alert('Error ','Debe seleccionar fuentes válidas para eliminar');
        } 
    }
    else
    {
        Ext.Msg.alert('Error ','Seleccione por lo menos una fuente de la lista');
    }
}


function eliminarAccionAjax(arrayParametros)
{
    Ext.Msg.confirm('Alerta','Se eliminara la fuente seleccionada. Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlEliminarFuente,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    fuente : arrayParametros['fuente']
                },
                success: function(result)
                {
                    if( "OK" == result.responseText )
                    {
                        Ext.Msg.alert('Información', 'Fuente eliminada con éxito');
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
