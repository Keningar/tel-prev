/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();               
            
	// **************** PAIS ******************
    Ext.define('PaisList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_pais', type:'int'},
            {name:'nombre_pais', type:'string'}
        ]
    });           
    eval("var storePais = Ext.create('Ext.data.Store', { "+
        "  pageSize: 200, "+
        "  id: 'storePais', "+
        "  model: 'PaisList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : 'getPaises',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_pais = new Ext.form.ComboBox({
        id: 'cmb_pais',
        name: 'cmb_pais',
        fieldLabel: "Pais",
        queryMode:'remote',
        width: 425,
        emptyText: 'Seleccione Pais',
        store: eval("storePais"),
        displayField: 'nombre_pais',
        valueField: 'id_pais',
        labelAlign: 'top',
        disabled: false,
		listeners:{
			select:{fn:function(combo, value) {
				Ext.getCmp('cmb_region').reset();  
				
				storeRegiones.proxy.extraParams = {idPais: combo.getValue()};
				storeRegiones.load();	
			}}
		}
    });
	
	// **************** REGION ******************
    Ext.define('RegionesList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_region', type:'int'},
            {name:'nombre_region', type:'string'}
        ]
    });           
    eval("var storeRegiones = Ext.create('Ext.data.Store', { "+
        "  pageSize: 200, "+
        "  id: 'storeRegiones', "+
        "  model: 'RegionesList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : 'getRegiones',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_region = new Ext.form.ComboBox({
        id: 'cmb_region',
        name: 'cmb_region',
        fieldLabel: "Regiones",
        queryMode:'remote',
        width: 425,
        emptyText: 'Seleccione Regiones',
        store: eval("storeRegiones"),
        displayField: 'nombre_region',
        valueField: 'id_region',
        labelAlign: 'top',
        disabled: false
    });
	
	
    store = new Ext.data.Store({ 
        pageSize: 10,
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
                idPais: '',
                idRegion: '',
                nombre: '',
                estado: 'Todos'
            }
        },
        fields:
                    [
                    {name:'id_provincia', mapping:'id_provincia'},
                    {name:'nombre_provincia', mapping:'nombre_provincia'},
                    {name:'nombre_region', mapping:'nombre_region'},
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
        width: 850,
        height: 400,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
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
                  id: 'id_provincia',
                  header: 'IdProvincia',
                  dataIndex: 'id_provincia',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_provincia',
                  header: 'Nombre Provincia',
                  dataIndex: 'nombre_provincia',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'nombre_region',
                  header: 'Nombre Region',
                  dataIndex: 'nombre_region',
                  width: 200,
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
                    items: [{
                        getClass: function(v, meta, rec) {return rec.get('action1')},
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = rec.get('id_provincia')+"/show";
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
                                window.location = rec.get('id_provincia')+"/edit";
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
                                            params: { param : rec.get('id_provincia')},
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
            columns: 3,
            align: 'left'
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
		items: 
		[
			{html:"&nbsp;",border:false,width:200},
			combo_pais,
			{html:"&nbsp;",border:false,width:200},
			
			{html:"&nbsp;",border:false,width:200},
			combo_region,
			{html:"&nbsp;",border:false,width:200},
		
			{html:"&nbsp;",border:false,width:200},
			{
				xtype: 'textfield',
				id: 'txtNombre',
				fieldLabel: 'Nombre',
				value: '',
				width: '425'
			},
			{html:"&nbsp;",border:false,width:200},
		
			{html:"&nbsp;",border:false,width:200},
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
				width: '425'
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
	store.getProxy().extraParams.idPais = Ext.getCmp('cmb_pais').value;
	store.getProxy().extraParams.idRegion = Ext.getCmp('cmb_region').value;
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}
function limpiar(){
    Ext.getCmp('cmb_pais').value="";
    Ext.getCmp('cmb_pais').setRawValue("");
    Ext.getCmp('cmb_region').value="";
    Ext.getCmp('cmb_region').setRawValue("");
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
	store.getProxy().extraParams.idPais = Ext.getCmp('cmb_pais').value;
	store.getProxy().extraParams.idRegion = Ext.getCmp('cmb_region').value;
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}

function eliminarAlgunos(){
    var param = '';
    if(sm.getSelection().length > 0)
    {
    var estado = 0;
    for(var i=0 ;  i < sm.getSelection().length ; ++i)
    {
        param = param + sm.getSelection()[i].data.id_provincia;

        if(sm.getSelection()[i].data.estado == 'Eliminado')
        {
        estado = estado + 1;
        }
        if(i < (sm.getSelection().length -1))
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