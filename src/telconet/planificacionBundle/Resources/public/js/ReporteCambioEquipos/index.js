/* 
 * To change this55 template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {  
    Ext.tip.QuickTipManager.init();
            
    
    /*
     * 
     * Crea campos de Fecha
     * 
     */
    DTFechaDesdePlanif = new Ext.form.DateField( {
        id         : 'fechaDesdePlanif',
        fieldLabel : 'Desde',
        labelAlign : 'right',
        xtype      : 'datefield',
        format     : 'Y-m-d',
        width      : 325,
        editable   : false
    });
    
    DTFechaHastaPlanif = new Ext.form.DateField( {
        id         : 'fechaHastaPlanif',
        fieldLabel : 'Hasta',
        labelAlign : 'right',
        xtype      : 'datefield',
        format     : 'Y-m-d',
        width      : 325,
        editable   : false       
    });	
    
  
    store = new Ext.data.Store( { 
        pageSize: 19,
        total   : 'total',
        proxy: {
            type   : 'ajax',
            url    : 'grid',
		    timeout: 120000,
            reader: {
                type         : 'json',
                totalProperty: 'total',
                root         : 'encontrados'
            },
            extraParams: {
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                nombres         : '',
                apellidos       : '',
                direccion       : '',
                motivo          : '',
                usuario_creacion: '',
                fecha_creacion  : '',                
                estado          : 'Todos'
            }
        },
        fields:
                [

                    {name:'nombres', mapping         : 'nombres'},
                    {name:'apellidos', mapping       :'apellidos'},
                    {name:'direccion', mapping       :'direccion'},
                    {name:'motivo', mapping          :'motivo'},
                    {name:'usuario_creacion', mapping:'usuario_creacion'},
                    {name:'fecha_creacion', mapping  :'fecha_creacion'},
                    {name:'estado', mapping          :'estado'}
                    
                ],   
    });
    	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso      = $("#ROLE_295-37");
	var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);		
	var exportarBtn  = "";
    
	if(boolPermiso1)
	{
		exportarBtn = Ext.create('Ext.button.Button', {
			iconCls: 'icon_exportar',
			itemId : 'exportar',
			text   : 'Exportar',
			scope  : this,
			handler: function(){ exportarExcel();}
		});
	}
			
	var toolbar = Ext.create('Ext.toolbar.Toolbar', {
		dock    : 'top',
		align   : '->',
		items   : [ '->', exportarBtn]
	});

    
    grid = Ext.create('Ext.grid.Panel', {
        width      : 1230,
        height     : 500,
        store      : store,
        loadMask   : true,
        frame      : false,
		dockedItems: [ toolbar ], 
        columns:[
                {
                  id       : 'nombres',
                  header   : 'Nombres',
                  dataIndex: 'nombres',
                  width    : 150,
                  sortable : true
                },
                {
                  id       : 'apellidos',
                  header   : 'Apellidos',
                  dataIndex: 'apellidos',
                  width    : 150,
                  sortable : true
                },
                {
                  id       : 'direccion',
                  header   : 'Direccion',
                  dataIndex: 'direccion',
                  width    : 350,
                  sortable : true
                },
                {
                  id       : 'motivo',
                  header   : 'Motivo',
                  dataIndex: 'motivo',
                  width    : 200,
                  sortable : true
                },  
                {
                  id       : 'usuario_creacion',
                  header   : 'Usuario_creacion',
                  dataIndex: 'usuario_creacion',
                  width    : 150,
                  sortable : true
                },
                {
                  id       : 'fecha_creacion',
                  header   : 'Fecha_creacion',
                  dataIndex: 'fecha_creacion',
                  width    : 120,
                  sortable : true
                },   
                {
                  id       : 'estado',
                  header   : 'Estado',
                  dataIndex: 'estado',
                  width    : 120,
                  sortable : true
                }
            ],
            bbar       : Ext.create('Ext.PagingToolbar', {
            store      : store,
            displayInfo: true,
            displayMsg : 'Mostrando {0} - {1} de {2}',
            emptyMsg   : "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding : 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border      :false,
        //border: '1,1,0,1',
        buttonAlign : 'center',
        layout:{
            type   :'table',
            columns: 5,
            align  : 'right',
            border :true
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed   : true,
        
        width: 1230,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text   : 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscar();}
                },
                {
                    text   : 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar();}
                }

                ],                
                items: 
                [  


               {html:"&nbsp;",border:false,width:120},
               {html:"&nbsp;",border:false,width:100},
               {html:"Fecha Planificacion:",border:false,width:120},                
               DTFechaDesdePlanif, 
               {
                        xtype     : 'combobox',
                        fieldLabel: 'Estado',
                        id        : 'sltEstado',
                        value     :'Todos',
                        labelAlign: 'right',
                        store: [
                                  ['Todos','Todos'],
                                  ['Anulado','Anulado'],
                                  ['PrePlanificada','PrePlanificada'],
                                  ['AsignadoTarea','AsignadoTarea'],
                                  ['Finalizada','Finalizada']
                        ],
                        width: '325'
                    }          ,
               
               
               {html:"&nbsp;",border:false,width:100},
               
               {html:"&nbsp;",border:false,width:100},
               {html:"&nbsp;",border:false,width:200}, 
               DTFechaHastaPlanif,
                                  
                ],	
        renderTo: 'filtro'
    }); 

});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
function buscar(){

    var boolError = false;
    
    if(( Ext.getCmp('fechaDesdePlanif').getValue()!=null)&&(Ext.getCmp('fechaHastaPlanif').getValue()!=null) )
    {
        if(Ext.getCmp('fechaDesdePlanif').getValue() > Ext.getCmp('fechaHastaPlanif').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title  :'Error en Busqueda',
                msg    : 'Por Favor para realizar la busqueda Fecha Desde Planificacion debe ser fecha menor a Fecha Hasta Planificacion.',
                buttons: Ext.Msg.OK,
                animEl : 'elId',
                icon   : Ext.MessageBox.ERROR
            });	
        }
    }
    
    if(!boolError)
    {
        store.removeAll();
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.estado           = Ext.getCmp('sltEstado').value;

        $var1 = Ext.getCmp('fechaDesdePlanif').value;
        $var2 = Ext.getCmp('fechaHastaPlanif').value;
        $var3 = Ext.getCmp('sltEstado').value;
        
        store.load(); 
        
    }          
}

function limpiar(){
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setRawValue("");

    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
        
    store.removeAll();
    store.getProxy().extraParams.fechaDesdePlanif = "";
    store.getProxy().extraParams.fechaHastaPlanif = "";
    store.getProxy().extraParams.nombres          = "";
    store.getProxy().extraParams.apellidos        = "";
    store.getProxy().extraParams.direccion        = "";
    store.getProxy().extraParams.motivo           = "";
    store.getProxy().extraParams.usuario_creacion = "";
    store.getProxy().extraParams.fecha_creacion   = "todos";
    store.getProxy().extraParams.estado           = "Todos";
}

function exportarExcel(){

    var url = "exportarConsulta";

    url = url+"?fechaDesdePlanif="+Ext.getCmp('fechaDesdePlanif').getRawValue();
    url = url+"&fechaHastaPlanif="+Ext.getCmp('fechaHastaPlanif').getRawValue();        
    url = url+"&nombres="+Ext.getCmp('nombres').value;
    url = url+"&apellidos="+Ext.getCmp('apellidos').value;
    url = url+"&direccion="+Ext.getCmp('direccion').value;
    url = url+"&motivo="+Ext.getCmp('motivo').value;
    url = url+"&usuario_creacion="+Ext.getCmp('usuario_creacion').value;
    url = url+"&fecha_creacion="+Ext.getCmp('fecha_creacion').value;
    url = url+"&estado="+Ext.getCmp('sltEstado').value;
        
    window.open(url);
}
