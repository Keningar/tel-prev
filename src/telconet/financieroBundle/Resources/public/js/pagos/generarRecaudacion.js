Ext.require
([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage                 = 100;
var store                        = '';

Ext.onReady(function()
{
    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'id',                     type: 'int'},
            {name:'nombreCanalRecaudacion', type: 'string'},
            {name:'estadoCanalRecaudacion', type: 'string'}
        ]
    }); 


    store = Ext.create('Ext.data.JsonStore',
    {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGridCanalesRecaudacion,
            timeout: 9000000,
            reader:
            {
                type: 'json',
                root: 'canalesRecaudacion'
            },
            simpleSortMode: true
        }
    });

    store.load({params: {start: 0, limit: 100}});    



    sm = new Ext.selection.CheckboxModel( {
        listeners:{
            selectionchange: function(selectionModel, selected, options){
                arregloSeleccionados = new Array();
                Ext.each(selected, function(record){
                });			
            }
        }                            
    });
              


    var listView = Ext.create('Ext.grid.Panel', 
    {
        width:800,
        height:200,
        collapsible:false,
        title: '',
        selModel: sm,
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando clientes {0} - {1} of {2}',
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
                text: 'Descripci&oacute;n',
                width: 250,
                dataIndex: 'nombreCanalRecaudacion'
            },
            {
                text: 'Estado',
                width: 250,
                dataIndex: 'estadoCanalRecaudacion'
            }         
        ],
        listeners:
        {
            viewready: function(grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function(type, view, cell, recordIndex, cellIndex, e)
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
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if (header.dataIndex != null)
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if (view.getRecord(parent).get(columnDataIndex) != null)
                                    {
                                        var columnText = view.getRecord(parent).get(columnDataIndex).toString();

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
                                else
                                {
                                    return false;
                                }
                            }
                        }
                    }
                });

                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function()
                        {
                            grid.tip.hide();
                        }, 500);
                    });

                    grid.tip.getEl().on('mouseover', function()
                    {
                        window.clearTimeout(timeout);
                    });

                    Ext.get(view.el).on('mouseover', function()
                    {
                        window.clearTimeout(timeout);
                    });

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function()
                        {
                           grid.tip.hide();
                        }, 500);
                    });
                });
            }
        }   
    });


    var tabsFiltros = Ext.create('Ext.tab.Panel',
    {
        id: 'tab_panel',
        width: 800,
        columns: 2,
        autoScroll: true,
        activeTab: 0,
        colspan: 5,
        defaults: {autoHeight: true},
        plain: true,
        deferredRender: false,
        hideMode: 'offsets',
        frame: false,
        buttonAlign: 'center',
        items:
        [
            {
                contentEl: 'fieldsTabDebitosGenerales',
                title: 'Canales de Recaudaci&oacute;n',
                id: 'idTabCanalRecaudacion',
                layout:
                {
                    type: 'table',
                    columns: 2,
                    align: 'left'
                },
                items:[ listView ],
                closable: false,
                listeners: 
                {
                    activate: function(selModel, Cmp)
                    {
                        strTabActivo = "canalesRecaudacion";
                    }
                }
            }
        ]
    });

    var objFilterPanel = Ext.create('Ext.form.Panel',
    {
        bodyPadding: 7,
        border: false,
        bodyStyle:
        {
            background: '#fff'
        },
        collapsible: false,
        collapsed: false,
        title: '',
        width: 900,
        items:[tabsFiltros],
        renderTo: 'lista'
    });
    
});

function procesar()
{
    var strCanalesRecIds    = '';
    var boolContinuar       = true;
    var selectionModel      = null;
    var intIdGrupoDebitoCab = 0;
    var intIdFormato        = 0;
    
    
    selectionModel = sm.getSelection();
    
    if( selectionModel.length > 0 )
    {
        for(var i=0 ;  i < selectionModel.length ; ++i)
        {            
            strCanalesRecIds = strCanalesRecIds + selectionModel[i].data.id;
            
            
            if(i < (selectionModel.length -1))
            {
                strCanalesRecIds = strCanalesRecIds + '|';
            }
        }
    }//( selectionModel.length > 0 )
    else
    {
        boolContinuar = false;
        
        Ext.Msg.alert('Atención','Seleccione por lo menos un registro de la lista');
    }//( selectionModel.length < 0 )
    
    
    if( boolContinuar )
    {
        Ext.Msg.confirm('Alerta','Se crear&aacute;n los archivos de recaudaci&oacute;n para los canales de recaudaci&oacute;n \n\
                                  seleccionados. Desea continuar?', function(btn)
        {
            if(btn=='yes')
            {
                Ext.MessageBox.wait("Generando recaudaciones...");
                $('#canalesRecaudacion').val(strCanalesRecIds);
                document.forms[0].submit();
            }
        });

    }//( boolContinuar )
}

    function validarFormulario() 
    {
       var rutaArchivo      = document.getElementById("inforecaudaciontype_file").value; 
       
       var extensionArchivo = getFileExt(rutaArchivo); 
       
       if (extensionArchivo !== 'xlsx')
       {
           Ext.Msg.alert("Alerta", "Debe ingresar archivo en formato excel con extensi&oacute;n xlsx");
           
           return false;
       }       
       else if (document.getElementById("canalRecaudacion").value == '')
       {
           Ext.Msg.alert("Alerta", "Debe escoger una recaudaci&aacute;n iniciada a procesar");
           
           return false;
       }
       else
       {
           return true;
       }
    }
    
    function getFileExt(strRutaArchivo) 
    {
        return strRutaArchivo.substr(strRutaArchivo.lastIndexOf('.') + 1 );
    }  
        
