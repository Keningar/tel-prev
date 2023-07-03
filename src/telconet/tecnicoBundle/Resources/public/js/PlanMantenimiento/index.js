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
            {name:'id_proceso',             mapping:'id_proceso'},
            {name:'nombre_proceso',         mapping:'nombre_proceso'},
            {name:'descripcion_proceso',    mapping:'descripcion_proceso'},
            {name:'estado',                 mapping:'estado'},
            {name:'action1',                mapping:'action1'},
            {name:'action2',                mapping:'action2'},
            {name:'action3',                mapping:'action3'}                 
        ],
        idProperty: 'id_proceso'
    });

    store = new Ext.data.Store({ 
        pageSize: 10,
        model: 'ModelStore',
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
            }
        },
        autoLoad: true
    });

    var pluginExpanded = true;

    //****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    eliminarBtn = Ext.create('Ext.button.Button', {
        iconCls: 'icon_delete',
        text: 'Eliminar',
        itemId: 'deleteAjax',
        scope   : this,
        getClass: function(v, meta, rec) 
        {
            var strClassBtn = "";
            var permiso = $("#ROLE_343-8");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
            if(!boolPermiso){ strClassBtn = "icon-invisible"; }

            if (strClassBtn == "icon-invisible") 
            this.items[0].tooltip = '';
            else 
            this.items[0].tooltip = 'Ver';

            return strClassBtn;
        },
        handler: function()
        { 
            var strClassBtn = "";
            var permiso = $("#ROLE_343-8");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            if(!boolPermiso){ strClassBtn = "icon-invisible"; }

            if(strClassBtn!="icon-invisible")
                 eliminarPlan(2,'');
            else
                Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
        }
    });
    
    var toolbar = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items   : 
        [ 
            //tbfill -> alinea los items siguientes a la derecha
            { xtype: 'tbfill' },
            eliminarBtn
        ]
    });

    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        width: 880,
        height: 400,
        store: store,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: false
        },
        dockedItems: [ toolbar ], 
        columns:[
                {
                  id: 'id_proceso',
                  header: 'IdProceso',
                  dataIndex: 'id_proceso',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_proceso',
                  header: 'Nombre Plan de Mantenimiento',
                  dataIndex: 'nombre_proceso',
                  width: 300,
                  sortable: true
                },
                {
                  id: 'descripcion_proceso',
                  header: 'Descripción Plan de Mantenimiento',
                  dataIndex: 'descripcion_proceso',
                  width: 300,
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
                    width: 120,
                    items: [
                            {
                                getClass: function(v, meta, rec) {
                                    var permiso = $("#ROLE_343-6");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                                    if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }

                                    if (rec.get('action1') == "icon-invisible") 
                                        this.items[0].tooltip = '';
                                    else 
                                        this.items[0].tooltip = 'Ver';

                                    return rec.get('action1')
                                },
                                tooltip: 'Ver',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_343-6");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }

                                    if(rec.get('action1')!="icon-invisible")
                                        window.location = rec.get('id_proceso')+"/show";
                                    else
                                        Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    var permiso = $("#ROLE_343-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                                    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }

                                    if (rec.get('action3') == "icon-invisible") 
                                        this.items[1].tooltip = '';
                                    else 
                                        this.items[1].tooltip = 'Eliminar';

                                    return rec.get('action3');
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_343-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                                    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
                                    
                                    if(rec.get('action3')!="icon-invisible")
                                    {
                                        Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                            if(btn=='yes'){
                                                eliminarPlan(1,rec.get('id_proceso'));
                                            }
                                        });
                                    } 
                                    else
                                        Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion'); 
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
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
        },                     

        collapsible : true,
        collapsed: true,
        width: 880,
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
            {html:"&nbsp;",border:false,width:100},
            {
                 xtype: 'textfield',
                 id: 'txtNombre',
                 fieldLabel: 'Nombre',
                 value: '',
                 width: '250'
            },

            {html:"&nbsp;",border:false,width:150},			 

            {html:"&nbsp;",border:false,width:100}
        ],	
        renderTo: 'filtro'
    }); 

});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.load();
}
function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");

    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;

    store.load();
}

function eliminarPlan(tipo,idPlan)
{
    var param = '';
    var idPlanMantenimiento='';
    var strAlerta='';
    var strEliminacionPlan='';
    var boolError=false;

    //tipo=1 Eliminación desde la acción eliminar de un documento
    if(tipo==1)
    {
        idPlanMantenimiento=idPlan;
        strAlerta='Se eliminara el registro. Desea continuar?';
        strEliminacionPlan='Eliminando Plan de Mantenimiento...';
    }
    //tipo=2 Eliminación Masiva desde el botón superior eliminar
    else
    {
        strAlerta='Se eliminaran los registros. Desea continuar?';
        strEliminacionPlan='Eliminando Planes de Mantenimiento...';
        var selection = grid.getPlugin('pagingSelectionPersistence').getPersistedSelection();
        if(selection.length > 0)
        {
            for(var i=0 ;  i < selection.length ; ++i)
            {
                param = param + selection[i].data.id_proceso;
                if(i < (selection.length -1))
                {
                    param = param + '|';
                }
            }
        }
        else
        {
            alert('Seleccione por lo menos un registro de la lista');
            boolError=true;
        }
    }

    if(!boolError)
    {
        Ext.Msg.confirm('Alerta',strAlerta, function(btn){
            if(btn=='yes'){
                Ext.MessageBox.wait(strEliminacionPlan, 'Por favor espere');
                Ext.Ajax.request({
                    url: url_eliminar_plan,
                    method: 'post',
                    params: { id:idPlanMantenimiento, param : param, tipo:tipo},
                    success: function(response){
                        Ext.MessageBox.hide();
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
