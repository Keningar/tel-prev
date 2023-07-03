
Ext.QuickTips.init();

Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    
    	
    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
	    timeout: 600000,
            url : 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Todos'
            }
        },
        fields:
		[				
			{name:'idPuntoAtencion', mapping:'idPuntoAtencion'},
			{name:'nombrePuntoAtencion', mapping:'nombrePuntoAtencion'},
			{name:'estado', mapping:'estado'},
			{name:'usrCreacion', mapping:'usrCreacion'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'}
		],
        autoLoad: true
    });
    
        var pluginExpanded = true;
   
	

    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        width: 850,
        height: 400,
        store: store,
        loadMask: true,
        frame: false,
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
	    enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        dockedItems: [ toolbar ], 
        columns:[
                {
                  id: 'nombrePuntoAtencion',
                  header: 'Nombre Punto Atencion',
                  dataIndex: 'nombrePuntoAtencion',
                  width: 210,
                  sortable: true
                },
                {
                  id: 'estado',
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'usrCreacion',
                  header: 'Usuario Creación',
                  dataIndex: 'usrCreacion',
                  width: 200,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
			   {
	                        getClass: function(v, meta, rec) {
                                    if (rec.get('estado') == "Activo" || rec.get('estado') == "Modificado")
                                    {
                                        return "button-grid-edit";
                                    }
                                },
	                        tooltip: 'Editar',
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
				    window.location = rec.get('idPuntoAtencion')+"/edit";
                                    
		                }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                
                                   if (rec.get('estado') == "Activo" || rec.get('estado') == "Modificado") 
                                   {
                                       return "button-grid-delete";
                                   }
                                    
                                },
	                        tooltip: 'Eliminar',
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
				    eliminarPuntoAtencion(rec.data.idPuntoAtencion);
                                    
		                }
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
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
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 850,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscar();}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar();}
                }

                ],                
                items: [
                        { width: '5%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            name:'txtNombre',
                            fieldLabel: 'Nombre Punto Atencion',
                            value: '',
                            width: '250'
                        },
                        { width: '15%',border:false},
                        ,{
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'sltEstado',
                            name:'sltEstado',
                            value:'Todos',
                            store: [
                                ['Todos','Todos'],
                                ['Activo','Activo'],
                                ['Modificado','Modificado'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '200'
                        }
                        ],	
        renderTo: 'filtro'
    }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function eliminarPuntoAtencion(idPuntoAtencion)
{

    var strMensaje = confirm("¿Está seguro que desea eliminar el punto de atención?");

    if(strMensaje===true)
    {
        var parametro = {
           "idPuntoAtencion": idPuntoAtencion,
        }

         $.ajax({
            data: parametro,
            url: urlEliminarPuntoAtencion,
            type: 'POST',
            success: function (response)
            {
                alert(response.strMensaje);
                store.load();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert("Se presento un error al eliminar el punto de atencion");
            }
           
         });
    }
   
    
}


function buscar()
{
    strNombres   = Ext.getCmp('txtNombre').value;
   
    store.proxy.extraParams = {
        strNombres    : strNombres,
        estado        : Ext.getCmp('sltEstado').value
    };
    
    store.load();
    
}

function limpiar()
{
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos"); 
    
    store.proxy.extraParams = {
        strNombres: '',        
        estado: 'Todos'
    };
    
    grid.getStore().removeAll(); 
}

