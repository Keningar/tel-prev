/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_numeracion',         mapping:'id_numeracion'},
			{name:'descripcion',           mapping:'descripcion'},
			{name:'codigo',                mapping:'codigo'},
			{name:'tipoComprobante',       mapping:'tipoComprobante'},
			{name:'tabla',                 mapping:'tabla'},
			{name:'numeracion_uno',        mapping:'numeracion_uno'},
			{name:'numeracion_dos',        mapping:'numeracion_dos'},
			{name:'secuencia',             mapping:'secuencia'},
			{name:'estado',                mapping:'estado'},
			{name:'action1',               mapping:'action1'},
			{name:'action2',               mapping:'action2'},
			{name:'action3',               mapping:'action3'},
			{name:'strNumeroAutorizacion', mapping:'strNumeroAutorizacion'},                 
		],
        idProperty: 'id_numeracion'
    });
	
    store = new Ext.data.Store
    ({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy:
        {
            url : strUrlGrid,
            type: 'ajax',
            timeout: 600000,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                nombre: '',
                estado: 'Activo'
            }
        },
        autoLoad: true
    });
   
    var pluginExpanded = true;
	
    //****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
    var permiso = $("#ROLE_33-8");
    var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	

    var permiso = $("#ROLE_33-9");
    var boolPermiso2 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	

    var eliminarBtn = "";
    sm = "";
    if(boolPermiso1 && boolPermiso2)
    {
        sm = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true
        })

            eliminarBtn = Ext.create('Ext.button.Button', {
                    iconCls: 'icon_delete',
                    text: 'Eliminar',
                    itemId: 'deleteAjax',
                text    : 'Eliminar',
                scope   : this,
                    handler: function(){ eliminarAlgunos();}
            });
    }

    grid = Ext.create('Ext.grid.Panel', 
    {
        id : 'grid',
        width: 900,
        height: 370,
        store: store,
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: 
        {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        columns:
        [
            new Ext.grid.RowNumberer(),
            {
              id: 'id_numeracion',
              header: 'IdNumeracion',
              dataIndex: 'id_numeracion',
              hidden: true,
              hideable: false
            },
            {
              id: 'tipoComprobante',
              header: 'Tipo Comprobante',
              dataIndex: 'tipoComprobante',
              width: 100,
              sortable: true
            },
            {
              id: 'codigo',
              header: 'Código',
              dataIndex: 'codigo',
              width: 80,
              sortable: true
            },
            {
              id: 'descripcion',
              header: 'Descripcion',
              dataIndex: 'descripcion',
              width: 193,
              sortable: true
            },
            {
              id: 'strNumeroAutorizacion',
              header: 'Impresión Fiscal',
              dataIndex: 'strNumeroAutorizacion',
              width: 120,
              sortable: true,
              hidden: boolMostrarNumeroAutorizacion
            },
            {
              id: 'numeracion_uno',
              header: 'Número<br/>Establecimiento SRI',
              dataIndex: 'numeracion_uno',
              width: 120,
              sortable: true,
              hidden: boolMostrarSecuenciales
            },
            {
              id: 'numeracion_dos',
              header: 'Punto de Emisión',
              dataIndex: 'numeracion_dos',
              width: 120,
              sortable: true,
              hidden: boolMostrarSecuenciales
            },
            {
              id: 'secuencia',
              header: 'Secuencia',
              dataIndex: 'secuencia',
              width: 80,
              sortable: true
            },
            {
              header: 'Estado',
              dataIndex: 'estado',
              width: 100,
              sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 80,
                items: 
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            this.items[0].tooltip = 'Ver';
                            return rec.get('action1');
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec= store.getAt(rowIndex);

                            if(rec.get('action1')!="icon-invisible")
                            {
                                window.location = rec.get('id_numeracion')+"/show";
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
                            this.items[1].tooltip = 'Eliminar';
	                            
                            return rec.get('action3');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
								
                            if(rec.get('action3')!="icon-invisible")
                            {
                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn)
                                {
                                    if(btn=='yes')
                                    {
                                        Ext.Ajax.request
                                        ({
                                            url: strUrlDeleteAjax,
                                            method: 'post',
                                            params: { param : rec.get('id_numeracion')},
                                            success: function(response)
                                            {
                                                var text = response.responseText;
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
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion'); 
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
    Ext.define('OficinaList', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'id_oficina',     type:'int'},
            {name:'nombre_oficina', type:'string'}
        ]
    });
    
    var storeOficina = Ext.create('Ext.data.Store',
    {
        model: 'OficinaList',
        proxy: 
        {
            type: 'ajax',
            url : strUrlGetListadoOficinas,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{nombre_oficina: 'Todas', id_oficina: ''}]);
            }      
        },
        autoLoad: true
    });
    
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
        width: 900,
        title: 'Criterios de busqueda',
        buttons: 
        [
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
            items: 
            [
                { width: '5%',border:false},
                {
                    xtype: 'textfield',
                    id: 'txtNombre',
                    fieldLabel: 'Descripción',
                    value: '',
                    width: '250'
                },
                { width: '15%',border:false},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Oficina de Facturación:',
                    id: 'cmbOficina',
                    name: 'cmbOficina',
                    width: '250',
                    store: storeOficina,
                    displayField: 'nombre_oficina',
                    valueField: 'id_oficina',
                    queryMode: 'remote',
                    emptyText: 'Seleccione Oficina',
                    forceSelection: true
                }
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
    store.getProxy().extraParams.nombre  = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.oficina = Ext.getCmp('cmbOficina').value;
    store.load();
}
function limpiar()
{
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('cmbOficina').value = null;
    Ext.getCmp('cmbOficina').setRawValue(null);
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.nombre  = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.oficina = Ext.getCmp('cmbOficina').value;
    store.load();
}


function eliminarAlgunos(){
    var param = '';
	var selection = grid.getPlugin('pagingSelectionPersistence').getPersistedSelection();
	
    if(selection.length > 0)
    {
		var estado = 0;
		for(var i=0 ;  i < selection.length ; ++i)
		{
			param = param + selection[i].getId();
			
			if(i < (selection.length -1))
			{
				param = param + '|';
			}
		}
    
		if(estado == 0)
		{
			Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
				if(btn=='yes'){
					Ext.Ajax.request({
						url: "deleteAjax",
						method: 'post',
						params: { param : param},
						success: function(response){
							var text = response.responseText;
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
		else
		{
			alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
		}
    }
    else
    {
        alert('Seleccione por lo menos un registro de la lista');
    }
}