/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {  
    Ext.tip.QuickTipManager.init();
    
    //************************ COMBO CANTON *************************************
    Ext.define('CantonList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_canton', type:'string'},
            {name:'nombre_canton', type:'string'}
        ]
    });
    StoreCanton = Ext.create('Ext.data.Store', {
            model: 'CantonList',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url : '../../../administracion/general/admi_canton/getCantones',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });    
    combo_cantones = new Ext.form.ComboBox({
        id: 'cmb_canton',
        name: 'cmb_canton',
        fieldLabel: 'Nombre Canton',
        anchor: '100%',
        queryMode:'local',
        width: 600,
        emptyText: 'Seleccione Canton',
        store:StoreCanton,
        displayField: 'nombre_canton',
        valueField: 'id_canton',
        listeners:{
            select:{fn:function(combo, value) {
                Ext.getCmp('cmb_parroquia').reset();	
                Ext.getCmp('cmb_sector').reset();	
  
                var valueEscogido = combo.getValue();
                
                StoreParroquia.getProxy().extraParams.idCanton= valueEscogido;
                StoreParroquia.load({params: {start: 0, limit: 300}});
            }}
        }
    });
    
    
    //************************ COMBO PARROQUIA *************************************
    Ext.define('ParroquiaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_parroquia', type:'string'},
            {name:'nombre_parroquia', type:'string'}
        ]
    });
    StoreParroquia = Ext.create('Ext.data.Store', {
            model: 'ParroquiaList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url : '../../../administracion/general/admi_parroquia/buscarParroquias',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });        
    combo_parroquias = new Ext.form.ComboBox({
        id: 'cmb_parroquia',
        name: 'cmb_parroquia',
        fieldLabel: 'Nombre Parroquia',
        anchor: '100%',
        queryMode:'local',
        width: 600,
        emptyText: 'Seleccione Parroquia',
        store:StoreParroquia,
        displayField: 'nombre_parroquia',
        valueField: 'id_parroquia',
        listeners:{
            select:{fn:function(combo, value) {
                Ext.getCmp('cmb_sector').reset();
                
                var valueEscogido = combo.getValue();
                
                StoreSector.getProxy().extraParams.idParroquia= valueEscogido;
                StoreSector.load({params: {start: 0, limit: 300}});
            }}
        }
    });
    
    
    //************************ COMBO SECTOR *************************************
    Ext.define('SectorList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_sector', type:'string'},
            {name:'nombre_sector', type:'string'}
        ]
    });
    StoreSector = Ext.create('Ext.data.Store', {
            model: 'SectorList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url : '../../../administracion/general/admi_sector/buscarSectores',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });        
    combo_sectores = new Ext.form.ComboBox({
        id: 'cmb_sector',
        name: 'cmb_sector',
        fieldLabel: 'Nombre Sector',
        anchor: '100%',
        queryMode:'local',
        width: 600,
        emptyText: 'Seleccione Sector',
        store:StoreSector,
        displayField: 'nombre_sector',
        valueField: 'id_sector',
        listeners:{
            select:{fn:function(combo, value) {
                var valueEscogido = combo.getValue();
                
               // StoreParroquia.getProxy().extraParams.idCanton= valueEscogido;
               // StoreParroquia.load({params: {start: 0, limit: 300}});
            }}
        }
    });
    
    
    
    //************************ COMBO VENDEDORES *************************************
    Ext.define('VendedorList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'login', type:'string'},
            {name:'nombre', type:'string'}
        ]
    });
    StoreVendedor = Ext.create('Ext.data.Store', {
            model: 'VendedorList',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url : '../../../comercial/punto/ajaxlistavendedores',
                reader: {
                    type: 'json',
                    root: 'vendedores'
                }
            }
    });        
    combo_vendedores = new Ext.form.ComboBox({
        id: 'cmb_vendedor',
        name: 'cmb_vendedor',
        fieldLabel: 'Vendedor',
        anchor: '100%',
        queryMode:'local',
        width: 600,
        emptyText: 'Seleccione Vendedor',
        store:StoreVendedor,
        displayField: 'nombre',
        valueField: 'login',
        listeners:{
            select:{fn:function(combo, value) {
                var valueEscogido = combo.getValue();
                
               // StoreParroquia.getProxy().extraParams.idCanton= valueEscogido;
               // StoreParroquia.load({params: {start: 0, limit: 300}});
            }}
        }
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
        width: 1230,
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
                    combo_cantones,
                    {html:"&nbsp;",border:false,width:500},
                    
                    {html:"&nbsp;",border:false,width:200},
                    combo_parroquias,
                    {html:"&nbsp;",border:false,width:500},
                    
                    {html:"&nbsp;",border:false,width:200},
                    combo_sectores,
                    {html:"&nbsp;",border:false,width:500},
                    
                    {html:"&nbsp;",border:false,width:200},
                    combo_vendedores,
                    {html:"&nbsp;",border:false,width:500},
                               
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'textfield',
                        id: 'txtLogin',
                        fieldLabel: 'Login',
                        value: '',
                        width: '600'
                    },
                    {html:"&nbsp;",border:false,width:500},   
                    
                    
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Estadoo',
                        id: 'sltEstado',
                        value:'Todos',
                        store: [
                            ['Todos','Todos'],
                            ['Planificada','Planificada'],
                            ['Asignada','Asignada']
                        ],
                        width: '600'
                    },
                    {html:"&nbsp;",border:false,width:500},   
                ],	
        renderTo: 'filtro'
    }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    var boolError = false;
    
    var cmb_canton =  Ext.getCmp('cmb_canton').getValue();
    var cmb_parroquia =  Ext.getCmp('cmb_parroquia').getValue();
    var cmb_sector =  Ext.getCmp('cmb_sector').getValue();
    var cmb_vendedor =  Ext.getCmp('cmb_vendedor').getValue();
    
    // if(cmb_canton!=null && cmb_canton!="")
    // {
        // if(cmb_parroquia!=null && cmb_parroquia!="")
        // {
            // if(cmb_sector==null || cmb_sector=="")
            // {
                // boolError = true;

                // Ext.Msg.show({
                    // title:'Error en Busqueda',
                    // msg: 'Por Favor seleccione el sector',
                    // buttons: Ext.Msg.OK,
                    // animEl: 'elId',
                    // icon: Ext.MessageBox.ERROR
                // });	
            // }
        // }
    // }
       
    // if(!boolError && cmb_vendedor!=null && cmb_vendedor!="")
    // {
		// boolError = true;

		// Ext.Msg.show({
			// title:'Error en Busqueda',
			// msg: 'Por Favor seleccione un vendedor',
			// buttons: Ext.Msg.OK,
			// animEl: 'elId',
			// icon: Ext.MessageBox.ERROR
		// });	
	// }
	
	if(!boolError)
    {
        eventStore.removeAll();
        eventStore.getProxy().extraParams.startDate = globalStartDate;
        eventStore.getProxy().extraParams.endDate = globalEndDate;
        eventStore.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
        eventStore.getProxy().extraParams.login2= Ext.getCmp('txtLogin').value;
        eventStore.getProxy().extraParams.sectorId= cmb_sector;
        eventStore.getProxy().extraParams.parroquiaId= cmb_parroquia;
        eventStore.getProxy().extraParams.cantonId= cmb_canton;
        eventStore.getProxy().extraParams.vendedorId= cmb_vendedor;
        eventStore.load();
    }          
}

function limpiar(){
    Ext.getCmp('cmb_canton').reset();
    Ext.getCmp('cmb_parroquia').reset();	
    Ext.getCmp('cmb_sector').reset();
    Ext.getCmp('cmb_vendedor').reset();
    
    Ext.getCmp('txtLogin').value="";
    Ext.getCmp('txtLogin').setRawValue("");
    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
                            
    eventStore.removeAll();
    eventStore.getProxy().extraParams.startDate = globalStartDate;
    eventStore.getProxy().extraParams.endDate = globalEndDate;
    eventStore.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    eventStore.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    eventStore.getProxy().extraParams.sectorId = "";
	eventStore.getProxy().extraParams.parroquiaId= "";
        eventStore.getProxy().extraParams.cantonId= "";
    eventStore.getProxy().extraParams.vendedorId = "";
    eventStore.load();
}
