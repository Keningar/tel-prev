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
            {name:'intIdElemento',          mapping:'intIdElemento'},
            {name:'strNombreElemento',      mapping:'strNombreElemento'},
            {name:'strFechaCreacion',       mapping:'strFechaCreacion'},
            {name:'strTipoElemento',        mapping:'strTipoElemento'},
            {name:'strModeloElemento',      mapping:'strModeloElemento'},
            {name:'strGPS',                 mapping:'strGPS'},
            {name:'strDISCO',               mapping:'strDISCO'},
            {name:'strREGION',              mapping:'strREGION'},
            {name:'strMOTOR',               mapping:'strMOTOR'},
            {name:'strCHASIS',              mapping:'strCHASIS'},
            {name:'strANIO',                mapping:'strANIO'},
            {name:'strUrlEdit',             mapping:'strUrlEdit'},
            {name:'strUrlShow',             mapping:'strUrlShow'},
            {name:'strUrlShowDocumentosTransporte',   mapping:'strUrlShowDocumentosTransporte'}

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
            url : strUrlGridMediosDeTransporte,
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
        width: '100%',
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
                id: 'strDISCO',
                header: 'Disco',
                dataIndex: 'strDISCO',
                width: 60,
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
                id: 'strNombreElemento',
                header: 'Placa',
                dataIndex: 'strNombreElemento',
                width: 100,
                sortable: true
            },
            {
                id: 'strGPS',
                header: 'Serie GPS',
                dataIndex: 'strGPS',
                emptyValue: 'N/A',
                width: 100,
                sortable: true,
                renderer: function(value, metaData, record, row, col, store, gridView)
                {
                    if (record.get('strGPS') == "")
                    {
                        return "N/A";
                    } 
                    else
                    {
                        return record.get('strGPS');
                    }
                }
            },
            {
                id: 'strREGION',
                header: 'Región',
                dataIndex: 'strREGION',
                width: 80,
                sortable: true
            },
            {
                id: 'strMOTOR',
                header: 'Motor',
                dataIndex: 'strMOTOR',
                width: 100,
                sortable: true
            },
            {
                id: 'strCHASIS',
                header: 'Chasis',
                dataIndex: 'strCHASIS',
                width: 150,
                sortable: true
            },
            {
                id: 'strANIO',
                header: 'Año',
                dataIndex: 'strANIO',
                width: 60,
                sortable: true
            },
            {
                id: 'strFechaCreacion',
                header: 'Fecha Creación',
                dataIndex: 'strFechaCreacion',
                width: 120,
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
                            var permiso = $("#ROLE_313-6");
                            var boolPermiso = (permiso.val() == 1);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "") 
                            {
                                this.items[0].tooltip = '';
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
                            var rec = store.getAt(rowIndex);
                            var strUrlVer = rec.get('strUrlShow');
                            var strClassButton = 'button-grid-show';
									
                            var permiso = $("#ROLE_313-6");
                            var boolPermiso = (permiso.val() == 1);
                            if(!boolPermiso){ strClassButton = ""; }

                            if(strClassButton!="")
                            {
                                if(strUrlVer != "")
                                {
                                    window.location = strUrlVer;
                                }
                            }
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-edit';
                            var permiso = $("#ROLE_313-4");
                            var boolPermiso = (permiso.val() == 1);							
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "") 
                            {
                                this.items[1].tooltip = '';
                            }  
                            else 
                            {
                                if(rec.get('strUrlEdit') == "") 
                                {
                                    strClassButton        = '';
                                    this.items[1].tooltip = '';
                                }
                                else 
                                {
                                    this.items[1].tooltip = 'Editar';
                                }
                            }

                            return strClassButton;
                        },
                        tooltip: 'Editar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var strUrlEditar = rec.get('strUrlEdit');
                            var strClassButton = 'button-grid-edit';
									
                            var permiso = $("#ROLE_313-6");
                            var boolPermiso = (permiso.val() == 1);
                            if(!boolPermiso){ strClassButton = ""; }

                            if(strClassButton!="")
                            {
                                if(strUrlEditar != "")
                                {
                                    window.location = strUrlEditar;
                                }
                            }
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            
                            var strClassButton = 'button-grid-delete';
                            var permiso = $("#ROLE_313-8");
                            var boolPermiso = (permiso.val() == 1);							
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "") 
                            {
                                this.items[2].tooltip = '';
                            }  
                            else 
                            {
                                if(rec.get('strUrlEdit') == "") 
                                {
                                    strClassButton        = '';
                                    this.items[2].tooltip = '';
                                }
                                else 
                                {
                                    this.items[2].tooltip = 'Eliminar';
                                }
                            }
                            return strClassButton;
                        },
                        tooltip: 'Eliminar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            
                            var rec = store.getAt(rowIndex);
                            var strUrlEditar = rec.get('strUrlEdit');
                            var strClassButton = 'button-grid-delete';
									
                            var permiso = $("#ROLE_313-8");
                            var boolPermiso = (permiso.val() == 1);
                            if(!boolPermiso){ strClassButton = ""; }

                            if(strClassButton!="")
                            {
                                if(strUrlEditar != "")
                                {
                                    var intIdElemento   = rec.get('intIdElemento');
                                    var arrayParametros = [];

                                    arrayParametros['medioTransporte'] = intIdElemento;

                                    verificarElementosAEliminar(arrayParametros)
                                }
                            }
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-pdf';
                            var permiso = $("#ROLE_313-3858");
                            var boolPermiso = (permiso.val() == 1);							
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "") 
                            {
                                strClassButton        = '';
                                this.items[3].tooltip = '';
                            }  
                            else 
                            {
                                
                                this.items[3].tooltip = 'Ver Archivos Digitales';
                            }
                            return strClassButton;
                        },
                        tooltip: 'Ver Archivos Digitales',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var strClassButton = 'button-grid-pdf';
                            var strUrlVerDocumentosTransporte = rec.get('strUrlShowDocumentosTransporte');
									
                            var permiso = $("#ROLE_313-3858");
                            var boolPermiso = (permiso.val() == 1);
                            if(!boolPermiso){ strClassButton = ""; }

                            if(strClassButton!="")
                            {
                                if(strUrlVerDocumentosTransporte != "")
                                {
                                    verDocumentos(strUrlVerDocumentosTransporte);
                                }
                            }
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                            }
                        }
                    },
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
    var storeModelosMedioTransporte = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetModelosMediosTransporte,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name: 'strIdentificacion', mapping: 'strIdentificacion'},
                {name: 'strDescripcion',    mapping: 'strDescripcion'}
            ],
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0,[{ strIdentificacion: 'Todos', strDescripcion: 'Todos' }]);
                }      
            }
        });
        
    var filterPanel = Ext.create('Ext.panel.Panel', 
        {
            bodyPadding: 7, 
            border:false,
            buttonAlign: 'center',
            layout: 
            {
                type:'table',
                columns: 5,
                align: 'left'
            },
            bodyStyle: 
            {
                background: '#fff'
            },   
            collapsible : true,
            collapsed: true,
            width: '100%',
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
                
                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Modelo Medio Transporte',
                    id: 'cmbModeloMedioTransporte',
                    name: 'cmbModeloMedioTransporte',
                    store: storeModelosMedioTransporte,
                    displayField: 'strDescripcion',
                    valueField: 'strIdentificacion',
                    queryMode: 'remote',
                    emptyText: 'Seleccione',
                    forceSelection: true
                },
                
                {html:"&nbsp;",border:false,width:150},
                {
                    xtype: 'textfield',
                    id: 'strPlaca',
                    fieldLabel: 'Placa',
                    value: '',
                    width: '250',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keyup: function(form, e)
                        {
                            convertirTextoEnMayusculas('strPlaca-inputEl');
                        }
                    }
                },
                {html:"&nbsp;",border:false,width:100},
                
                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'strNumDisco',
                    fieldLabel: 'Disco',
                    value: '',
                    width: '250'
                },
                {html:"&nbsp;",border:false,width:150},
                {html:"&nbsp;",border:false,width:250},
                {html:"&nbsp;",border:false,width:100},
                
                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'strNumChasis',
                    fieldLabel: 'Chasis',
                    value: '',
                    width: '250',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keyup: function(form, e)
                        {
                            convertirTextoEnMayusculas('strNumChasis-inputEl');
                        }
                    }
                },
                {html:"&nbsp;",border:false,width:150},
                {
                    xtype: 'textfield',
                    id: 'strNumMotor',
                    fieldLabel: 'Motor',
                    value: '',
                    width: '250',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keyup: function(form, e)
                        {
                            convertirTextoEnMayusculas('strNumChasis-inputEl');
                        }
                    }
                },
                {html:"&nbsp;",border:false,width:100},
            ],	
            renderTo: 'filtro'
        }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
function buscar()
{
    var cmbModeloMedioTransporte = Ext.getCmp('cmbModeloMedioTransporte').value;
    
    if( cmbModeloMedioTransporte == "Todos" )
    {
        cmbModeloMedioTransporte = "";
    }
    
    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.placa                 = Ext.getCmp('strPlaca').value;
    store.getProxy().extraParams.chasis                = Ext.getCmp('strNumChasis').value;
    store.getProxy().extraParams.motor                 = Ext.getCmp('strNumMotor').value;
    store.getProxy().extraParams.disco                 = Ext.getCmp('strNumDisco').value;
    store.getProxy().extraParams.modeloMedioTransporte = cmbModeloMedioTransporte;
    
    store.load();
}


function limpiar()
{
    Ext.getCmp('strPlaca').value="";
    Ext.getCmp('strPlaca').setRawValue("");
    
    Ext.getCmp('strNumMotor').value="";
    Ext.getCmp('strNumMotor').setRawValue("");
    
    Ext.getCmp('strNumChasis').value="";
    Ext.getCmp('strNumChasis').setRawValue("");
    
    Ext.getCmp('strNumDisco').value="";
    Ext.getCmp('strNumDisco').setRawValue("");
    
    Ext.getCmp('cmbModeloMedioTransporte').value = null;
    Ext.getCmp('cmbModeloMedioTransporte').setRawValue(null);
    
    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.placa                 = Ext.getCmp('strPlaca').value;
    store.getProxy().extraParams.motor                 = Ext.getCmp('strNumMotor').value;
    store.getProxy().extraParams.chasis                = Ext.getCmp('strNumChasis').value;
    store.getProxy().extraParams.disco                 = Ext.getCmp('strNumDisco').value;
    store.getProxy().extraParams.modeloMedioTransporte = Ext.getCmp('cmbModeloMedioTransporte').value;
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
                arrayParametros['medioTransporte'] = param;

            verificarElementosAEliminar(arrayParametros);
        }
        else
        {
            Ext.Msg.alert('Error ','Debe seleccionar medios de transporte válidos para eliminar');
        } 
    }
    else
    {
        Ext.Msg.alert('Error ','Seleccione por lo menos un medio de transporte de la lista');
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
            medioTransporte: arrayParametros['medioTransporte']
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
    Ext.Msg.confirm('Alerta','Se eliminara el medio de transporte seleccionado. Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlEliminarMediosDeTransporte,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    medioTransporte : arrayParametros['medioTransporte']
                },
                success: function(result)
                {
                    if( "OK" == result.responseText )
                    {
                        Ext.Msg.alert('Información', 'Medio de Transporte eliminado con éxito');
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

function verDocumentos(url_showDocumentosTransporte){    
    var store = new Ext.data.Store({ 
           id:'verDocumentosDigitalesStore',
           total: 'total',
           pageSize: 10,
           autoLoad: true,
           proxy: {
               type: 'ajax',                
               url: url_showDocumentosTransporte,               
               reader: {
                   type: 'json', 
                   totalProperty: 'total', 
                   root: 'encontrados'
               }
           },
           fields:
                 [
                   {name:'id', mapping:'id'},                                      
                   {name:'ubicacionLogicaDocumento', mapping:'ubicacionLogicaDocumento'},
                   {name:'tipoDocumentoGeneral', mapping:'tipoDocumentoGeneral'},
                   {name:'feCreacion', mapping:'feCreacion'},
                   {name:'feCaducidad', mapping:'feCaducidad'},
                   {name:'usrCreacion', mapping:'usrCreacion'},
                   {name:'linkVerDocumento', mapping: 'linkVerDocumento'}
                 ]
        });
                
        var gridDocumentosDigitalesTransporte = Ext.create('Ext.grid.Panel', {
            id: 'gridDocumentosDigitalesTransporte',
            store: store,
            timeout: 60000,
            dockedItems: [ {
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items: [
                        { xtype: 'tbfill' }
                    ]}
            ],                  
            columns:[
                    {
                      id: 'id',
                      header: 'id',
                      dataIndex: 'id',
                      hidden: true,
                      hideable: false
                    },
                    {
                      header: 'Archivo Digital',
                      dataIndex: 'ubicacionLogicaDocumento',
                      width: 300
                    },
                    {
                      header: 'Tipo Documento',
                      dataIndex: 'tipoDocumentoGeneral',
                      width: 150
                    },                  
                    {
                      header: 'Fecha de Creacion',
                      dataIndex: 'feCreacion',
                      width: 160,
                      sortable: true
                    },
                    {
                       header: 'Fecha de Caducidad',
                       dataIndex: 'feCaducidad',
                       width: 160,
                       sortable: true
                    },
                    {
                      header: 'Creado por',
                      dataIndex: 'usrCreacion',
                      width: 80,
                      sortable: true
                    },
                    {
		      text: 'Acciones',
		      width: 80,
		      renderer: renderAcciones,
		    }
                ],
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
                })
        });
        function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVerDocumento+
                            '" onClick="" title="Ver Archivo Digital" class="button-grid-show"></a></b>';	                                       
                    return Ext.String.format(
                        iconos,
                        value,
                        '1',
                        'nada'
                    );
        }
        var pop = Ext.create('Ext.window.Window', {
            title: 'Archivos Digitales',
            height: 400,
            width: 800,
            modal: true,
            layout:{
                type:'fit',
                align:'stretch',
                pack:'start'
            },
            floating: true,
            shadow: true,
            shadowOffset:20,
            items: [gridDocumentosDigitalesTransporte] 
        });
        
        
        pop.show();
}

