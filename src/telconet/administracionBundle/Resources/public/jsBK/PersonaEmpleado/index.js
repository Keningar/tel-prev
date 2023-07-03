/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
         
    store = new Ext.data.Store({ 
        pageSize: 20,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombres: '',
                apellidos: '',
                identificacion: '',
                estado: 'Todos'
            }
        },
        fields:
				[
                    {name:'id_persona', mapping:'id_persona'},
                    {name:'id_persona_empresa_rol', mapping:'id_persona_empresa_rol'},
                    {name:'id_empresa', mapping:'id_empresa'},
                    {name:'nombre_empresa', mapping:'nombre_empresa'},
                    {name:'tipo_identificacion', mapping:'tipo_identificacion'},
                    {name:'identificacion', mapping:'identificacion'},
                    {name:'nombres', mapping:'nombres'},
                    {name:'apellidos', mapping:'apellidos'},
                    {name:'nacionalidad', mapping:'nacionalidad'},
                    {name:'direccion', mapping:'direccion'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'}               
				],
        autoLoad: true
    });

    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1300,
        height: 700,
        store: store,
        loadMask: true,
        frame: false,
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    { xtype: 'tbfill' }
                ]}
        ], 
        columns:[
                {
                  id: 'id_persona_empresa_rol',
                  header: 'IdPersonaEmpresaRol',
                  dataIndex: 'id_persona_empresa_rol',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_persona',
                  header: 'IdPersona',
                  dataIndex: 'id_persona',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_empresa',
                  header: 'IdEmpresa',
                  dataIndex: 'id_empresa',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_empresa',
                  header: 'Nombre Empresa',
                  dataIndex: 'nombre_empresa',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'tipo_identificacion',
                  header: 'Tipo Ident.',
                  dataIndex: 'tipo_identificacion',
                  width: 80,
                  sortable: true
                },
                {
                  id: 'identificacion',
                  header: 'Identificacion',
                  dataIndex: 'identificacion',
                  width: 80,
                  sortable: true
                },
                {
                  id: 'nombres',
                  header: 'Nombres',
                  dataIndex: 'nombres',
                  width: 180,
                  sortable: true
                },
                {
                  id: 'apellidos',
                  header: 'Apellidos',
                  dataIndex: 'apellidos',
                  width: 180,
                  sortable: true
                },
                {
                  id: 'nacionalidad',
                  header: 'Nacionalidad',
                  dataIndex: 'nacionalidad',
                  width: 100,
                  sortable: true
                },
                {
                  id: 'direccion',
                  header: 'Direccion',
                  dataIndex: 'direccion',
                  width: 250,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 80,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 60,
                    items: [
						{
	                        getClass: function(v, meta, rec) {return rec.get('action1')},
	                        tooltip: 'Ver',
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
	                            window.location = rec.get('id_persona_empresa_rol')+"/show";
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
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout:{
            type:'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 1300,
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
                items: 
					[					
	                    {html:"&nbsp;",border:false,width:200},
	                    {
                            xtype: 'textfield',
                            id: 'txtNombres',
                            fieldLabel: 'Nombres',
                            value: '',
	                        width: '325'
	                    },
	                    {html:"&nbsp;",border:false,width:150},
	                    {
                            xtype: 'textfield',
                            id: 'txtApellidos',
                            fieldLabel: 'Apellidos',
                            value: '',
	                        width: '325'
	                    },
	                    {html:"&nbsp;",border:false,width:200},
						
						
	                    {html:"&nbsp;",border:false,width:200},
	                    {
                            xtype: 'textfield',
                            id: 'txtIdentificacion',
                            fieldLabel: 'Identificacion',
                            value: '',
	                        width: '325'
	                    },
	                    {html:"&nbsp;",border:false,width:150},
	                    {
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'sltEstado',
                            value:'Todos',
                            store: [
                                ['Todos','Todos'],
                                ['ACTIVO','Activo'],
                                ['MODIFICADO','Modificado'],
                                ['ELIMINADO','Eliminado']
                            ],
	                        width: '325'
	                    },
	                    {html:"&nbsp;",border:false,width:200}
					],	
        renderTo: 'filtro'
    }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    store.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
    store.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
    store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}
function limpiar(){
    Ext.getCmp('txtNombres').value="";
    Ext.getCmp('txtNombres').setRawValue("");
    Ext.getCmp('txtApellidos').value="";
    Ext.getCmp('txtApellidos').setRawValue("");
    Ext.getCmp('txtIdentificacion').value="";
    Ext.getCmp('txtIdentificacion').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
    store.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
    store.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
    store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}