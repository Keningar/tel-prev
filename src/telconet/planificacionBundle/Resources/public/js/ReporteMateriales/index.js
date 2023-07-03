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
            url    : url_grid,
		    timeout: 120000,
            reader: {
                type         : 'json',
                totalProperty: 'total',
                root         : 'encontrados'
            },
            extraParams: {
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                cantiad         : '',
                materialCod     : '',
                descripcion     : '',
                unidad          : ''
            }
        },
        fields:
                [
                    {name:'cantidad', mapping     :'cantidad'},
                    {name:'materialCod', mapping  :'materialCod'},
                    {name:'descripcion', mapping  :'descripcion'},
                    {name:'unidad', mapping       :'unidad'}                    
                ], 
    });
    	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso      = $("#ROLE_299-37");
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
        width      : 950,
        height     : 500,
        store      : store,
        loadMask   : true,
        frame      : false,
		dockedItems: [ toolbar ], 
        columns:[
                {
                  id       : 'cantidad',
                  header   : 'Cantidad',
                  dataIndex: 'cantidad',
                  width    : 100,
                  sortable : true
                },
                {
                  id       : 'materialCod',
                  header   : 'Codigo del Material',
                  dataIndex: 'materialCod',
                  width    : 150,
                  sortable : true
                },                
                {
                  id       : 'descripcion',
                  header   : 'Descripcion del Material',
                  dataIndex: 'descripcion',
                  width    : 300,
                  sortable : true
                },
                {
                  id       : 'unidad',
                  header   : 'Unidad de Medida',
                  dataIndex: 'unidad',
                  width    : 130,
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
        //border    : '1,1,0,1',
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
        
        width: 950,
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
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {
                        xtype      :'fieldset',
                        title      : 'Fecha de Solicitud',
                        collapsible: false,
                        border     : true,
                        width      : 940,
                        colspan    : 5,
                        layout: {
                            type   : 'table',
                            columns: 3,
                            align  : 'left'
                        },
                        items :
                        [
                            DTFechaDesdePlanif,
                            {html: "&nbsp;", border: false, width: 200},
                            DTFechaHastaPlanif
                        ]
                    },
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200}            
    
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
    
    //Se agrega validacion para filtrar que solo se pueda consultar maximo entre 30 dias en las fechas de planificacion   
    
    
    var desdeP = Ext.getCmp('fechaDesdePlanif').getValue();
    var hastaP = Ext.getCmp('fechaHastaPlanif').getValue();

    var fechaDesdeP = desdeP.getTime();
    var fechaHastaP = hastaP.getTime();
    

    var differenceP = Math.abs(fechaDesdeP - fechaHastaP)

    //Convierto de milisegundos a dias
    var diasP = differenceP/86400000;

    if(diasP >30){
        boolError = true;
        Ext.Msg.show({
        title  :'Error en Busqueda',
        msg    : 'Por Favor solo se puede realizar busquedas de hasta 30 dias de diferencia entre la Fecha Inicio y Fin',
        buttons: Ext.Msg.OK,
        animEl : 'elId',
        icon   : Ext.MessageBox.ERROR
        });	
    }

    if(!boolError)
    {
        store.removeAll();
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;        

        $var1 = Ext.getCmp('fechaDesdePlanif').value;
        $var2 = Ext.getCmp('fechaHastaPlanif').value;        

        store.load(); 

    }  
}

function limpiar(){
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setRawValue("");  
        
    store.removeAll();
    store.getProxy().extraParams.fechaDesdePlanif = "";
    store.getProxy().extraParams.fechaHastaPlanif = "";
    store.getProxy().extraParams.cantidad         = "";
    store.getProxy().extraParams.materialCod      = "";
    store.getProxy().extraParams.descripcion      = "";
    store.getProxy().extraParams.unidad           = "";
}

function exportarExcel(){

    var url = url_exportarConsulta;

    url = url+"?fechaDesdePlanif="+Ext.getCmp('fechaDesdePlanif').getRawValue();
    url = url+"&fechaHastaPlanif="+Ext.getCmp('fechaHastaPlanif').getRawValue();        
    url = url+"&cantidad="+Ext.getCmp('cantidad').value;
    url = url+"&materialCod="+Ext.getCmp('materialCod').value;
    url = url+"&descripcion="+Ext.getCmp('descripcion').value;
    url = url+"&unidad="+Ext.getCmp('unidad').value;        
    window.open(url);
}



