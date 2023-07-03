function llenarTecnico()
{
    Ext.tip.QuickTipManager.init();
         
        store = new Ext.data.Store({ 
            pageSize: 10,
            model: 'sims',
            total: 'total',
            proxy: {
                type: 'ajax',
                url : 'busqueda_por_datos_generales',
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
                        {name:'id', mapping:'id'},
                        {name:'login1', mapping:'login1'},
                        {name:'descripcionClienteSucursal', mapping:'descripcionClienteSucursal'},
                        {name:'idEstadoPtoCliente', mapping:'idEstadoPtoCliente'},
                        {name:'cliente', mapping:'cliente'},
                        {name:'vendedor', mapping:'vendedor'},
                        {name:'numeroOrdenServicio', mapping:'numeroOrdenServicio'},
                        {name:'responsable', mapping:'responsable'},
                        {name:'fechaSolicitud', mapping:'fechaSolicitud'},
                        {name:'fechaFactibilidad', mapping:'fechaFactibilidad'},
                        {name:'fechaCoordinacion', mapping:'fechaCoordinacion'},
                        {name:'action1', mapping:'action1'},
                        {name:'action2', mapping:'action2'},
                        {name:'action3', mapping:'action3'}
                      ]
        });    
    var pluginExpanded = true;
   
    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1200,
        height: 450,
        store: store,
        loadMask: true,
		viewConfig: { enableTextSelection: true },         
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    { xtype: 'tbfill' },
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        itemId: 'delete',
                        scope: this,
                        handler: function(){ eliminarAlgunos();}
                    }
                ]}
        ],
        columns:[
                {
                  id: 'id',
                  header: 'Id',
                  dataIndex: 'id',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'login1',
                  header: 'login1',
                  dataIndex: 'login1',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'descripcionClienteSucursal',
                  header: 'Descripcion Pto Cliente',
                  dataIndex: 'descripcionClienteSucursal',
                  width: 350,
                  sortable: true
                },
                {
                  id: 'idEstadoPtoCliente',
                  header: 'Estado Pto Cliente',
                  dataIndex: 'idEstadoPtoCliente',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'cliente',
                  header: 'Cliente',
                  dataIndex: 'cliente',
                  width: 250,
                  sortable: true
                },
                {
                  id: 'vendedor',
                  header: 'Vendedor',
                  dataIndex: 'vendedor',
                  width: 250,
                  sortable: true
                },
                {
                  id: 'numeroOrdenServicio',
                  header: 'N° Orden Servicio',
                  dataIndex: 'numeroOrdenServicio',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'responsable',
                  header: 'Responsable',
                  dataIndex: 'responsable',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'fechaSolicitud',
                  header: 'Fecha de Solicitud',
                  dataIndex: 'fechaSolicitud',
                  width: 180,
                  sortable: true
                },  
                {
                  id: 'fechaFactibilidad',
                  header: 'Fecha de Factibilidad',
                  dataIndex: 'fechaFactibilidad',
                  width: 180,
                  sortable: true
                },  
                {
                  id: 'fechaCoordinacion',
                  header: 'Fecha de Coordinacion',
                  dataIndex: 'fechaCoordinacion',
                  width: 180,
                  sortable: true
                },     
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [{
                        getClass: function(v, meta, rec) {return 'icon-view'},
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = rec.get('id')+"/show";                        
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            if (rec.get('action2') == "icon-invisible") 
                                this.items[1].tooltip = '';
                            else 
                                this.items[1].tooltip = 'Editar';
                            
                            return rec.get('action2')
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if(rec.get('action2')!="icon-invisible")
                                window.location = rec.get('id')+"/edit";
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            if (rec.get('action3') == "icon-invisible") 
                                this.items[2].tooltip = '';
                            else 
                                this.items[2].tooltip = 'Eliminar';
                            
                            return rec.get('action3')
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if(rec.get('action3')!="icon-invisible")
                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                    if(btn=='yes'){
                                        Ext.Ajax.request({
                                            url: "deleteAjax",
                                            method: 'post',
                                            params: { param : rec.get('id')},
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
    
}