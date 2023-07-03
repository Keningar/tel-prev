/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {  
    Ext.tip.QuickTipManager.init();
            
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false,
        value:new Date(),
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false,
        value:new Date(),
        //anchor : '65%',
        //layout: 'anchor'
    });	
	
	
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaUEDesde = new Ext.form.DateField({
        id: 'fechaUEDesde',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false,
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaUEHasta = new Ext.form.DateField({
        id: 'fechaUEHasta',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false,
        //anchor : '65%',
        //layout: 'anchor'
    });	
	
    store = new Ext.data.Store({ 
        pageSize: 6,
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
                fechaDesde: new Date(),
                fechaHasta: new Date(),
                fechaUEDesde: '',
                fechaUEHasta: '',
                estado: 'Todos'
            }
        },
        fields:
		[
			{name:'nombre_reporte', mapping:'nombre_reporte'},
			{name:'link_exportar', mapping:'link_exportar'}               
		],
        autoLoad: true
    });

    var pluginExpanded = true;
    
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso = $("#ROLE_200-37");
	var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);	
	
	grid = Ext.create('Ext.grid.Panel', {
        store: store,
		width: 500,
		height: 300,
        loadMask: true,
        frame: false,
        columns:
		[
			{
			  id: 'link_exportar',
			  header: 'link_exportar',
			  dataIndex: 'link_exportar',
			  hidden: true,
			  hideable: false
			},  
			{
			  id: 'nombre_reporte',
			  header: 'Nombre Reporte',
			  dataIndex: 'nombre_reporte',
			  sortable: true,
			  flex: 450,
			},
			{
				xtype: 'actioncolumn',
				header: 'Acciones',
				items: [
					{
						getClass: function(v, meta, rec) {
							var permiso = $("#ROLE_200-37");
							var boton = "button-grid-excel";
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);							
							if(!boolPermiso){ boton = "icon-invisible"; }
							
							return boton;
						},
						tooltip: 'Descargar Reporte',
						handler: function(grid, rowIndex, colIndex) {
							var rec = store.getAt(rowIndex);

							window.open(rec.data.link_exportar);
							
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
			{html:"Fecha Activacion:",border:false,width:325},
			{html:"&nbsp;",border:false,width:150},
			{html:"Fecha Ultimo Estado:",border:false,width:325},
			{html:"&nbsp;",border:false,width:200},			
		   
			{html:"&nbsp;",border:false,width:200},
			DTFechaDesde,
			{html:"&nbsp;",border:false,width:150},
			DTFechaUEDesde,
			{html:"&nbsp;",border:false,width:200},
			
			{html:"&nbsp;",border:false,width:200},
			DTFechaHasta,
			{html:"&nbsp;",border:false,width:150},
			DTFechaUEHasta,
			{html:"&nbsp;",border:false,width:200},
			
			
			{html:"&nbsp;",border:false,width:200},
			{
				xtype: 'combobox',
				fieldLabel: 'Estado',
				id: 'sltEstado',
				value:'Todos',
				store: [
					['Todos','Todos'],
					['Activo','Activo'],
					['Inactivo','Inactivo']
				],
				width: '325'
			},
			{html:"&nbsp;",border:false,width:150},
			{html:"&nbsp;",border:false,width:325},
			{html:"&nbsp;",border:false,width:200},
			
		],	
        //renderTo: 'filtro'
    }); 
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    var boolError = false;
    
    if(( Ext.getCmp('fechaDesde').getValue()!=null)&&(Ext.getCmp('fechaHasta').getValue()!=null) )
    {
        if(Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Activacion debe ser fecha menor a Fecha Hasta Activacion.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });	
        }
    }
	
	
    if(( Ext.getCmp('fechaUEDesde').getValue()!=null)&&(Ext.getCmp('fechaUEHasta').getValue()!=null) )
    {
        if(Ext.getCmp('fechaUEDesde').getValue() > Ext.getCmp('fechaUEHasta').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Ultimo Estado debe ser fecha menor a Fecha Hasta Ultimo Estado.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });	
        }
    }
	
    if(!boolError)
    {
        store.removeAll();
        store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').value;
        store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').value;
		store.getProxy().extraParams.fechaUEDesde = Ext.getCmp('fechaUEDesde').value;
        store.getProxy().extraParams.fechaUEHasta = Ext.getCmp('fechaUEHasta').value;
        store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
        store.load(); 
    }   
}

function limpiar(){
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");
    Ext.getCmp('fechaUEDesde').setRawValue("");
    Ext.getCmp('fechaUEHasta').setRawValue("");
	
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
        
    store.removeAll();
    store.getProxy().extraParams.fechaDesde = "";
    store.getProxy().extraParams.fechaHasta = "";
    store.getProxy().extraParams.estado = "Todos";
    store.load();
}

function exportarExcel(){
	var parametros = "";
	
	if(Ext.getCmp('fechaDesde').getValue()!=null){
		fechaDesde = Ext.getCmp('fechaDesde').getValue()
	}else{
		fechaDesde = ""
	}
	if(Ext.getCmp('fechaHasta').getValue()){
		fechaHasta = Ext.getCmp('fechaHasta').getValue()
	}else{
		fechaHasta = ""
	}
	if(Ext.getCmp('fechaUEDesde').getValue()!=null){
		fechaUEDesde = Ext.getCmp('fechaUEDesde').getValue()
	}else{
		fechaUEDesde = ""
	}
	if(Ext.getCmp('fechaUEHasta').getValue()){
		fechaUEHasta = Ext.getCmp('fechaUEHasta').getValue()
	}else{
		fechaUEHasta = ""
	}
	
	parametros = "?fechaDesde="+fechaDesde;
	parametros = parametros+"&fechaHasta="+fechaHasta;
	parametros = parametros+"&fechaUEDesde="+fechaUEDesde;
	parametros = parametros+"&fechaUEHasta="+fechaUEHasta;
	parametros = parametros+"&estado="+Ext.getCmp('sltEstado').value;
    
	window.open("exportarConsulta"+parametros);
}