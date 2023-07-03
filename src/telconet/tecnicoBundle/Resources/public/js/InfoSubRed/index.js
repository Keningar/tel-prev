/**
 * Documentación para cargar filtros y grid de las subredes a asignar por PE
 * 
 * @author Jonathan Montece <jmontece@telconet.ec>
 * @version 1.0 13-09-2021
 * */
 Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
   

    var storeUso = new Ext.data.Store({ 
        total: 'total',
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_getUsosSubred,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                descripcion: ''
                
            }
        },
        fields:
            [
                {name: 'descripcion', mapping: 'descripcion'},
                {name: 'id', mapping: 'descripcion'}
            ]
    });

    var storeTipoSubred = new Ext.data.Store({ 
        total: 'total',
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_getTipoSubred,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            /*extraParams: {
                descripcion_tipo: ''
                
            }*/
        },
        fields:
            [
                {name: 'descripcion_tipo', mapping: 'descripcion_tipo'},
                {name: 'id', mapping: 'descripcion_tipo'}
            ]
    });
 
    var storeEstadoSubred = new Ext.data.Store({ 
        total: 'total',
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_getEncontradosEstados,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
        },
        fields:
            [
                {name: 'descripcion_estado', mapping: 'descripcion_estado'},
                {name: 'id', mapping: 'descripcion_estado'}
            ]
    });
   
    Ext.define('ModelStore', 
    {
        extend: 'Ext.data.Model',
        fields:
        [				
            {name:'idSubred', mapping:'idSubred'},
            {name:'subRed', mapping:'subRed'},
            {name:'nombrePe', mapping:'nombrePe'},
            {name:'uso', mapping:'uso'},
            {name:'tipo', mapping:'tipo'},
            {name:'estadoElemento', mapping:'estadoElemento'},
            {name:'feCreacion', mapping:'feCreacion'}
                 
        ],
        idProperty: 'idSubred'
    });
    store = new Ext.data.Store
    ({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url : url_getEncontradosGrid,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        autoLoad: false
    });
    
    filtro = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: false,
        width: 930,
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
                        
                        {
                            xtype: 'textfield',
                            id: 'txtNombrePe',
                            fieldLabel: 'Pe',
                            value: '',
                            emptyText: 'Escriba Pe',
                            width: '200px'
                        },
                        
                        {
                            xtype: 'combobox',
                            id: 'sltUso',
                            name: 'sltUso',
                            fieldLabel: 'Uso Subred',
                            store: storeUso,
                            displayField: 'descripcion',
                            valueField: 'id',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            editable: false,
                            emptyText: 'Seleccione',
                            width: '30%',
                            forceSelection: true
                        },
                                               
                        {
                            xtype: 'combobox',
                            id: 'sltTipoSubred',
                            name: 'sltTipoSubred',
                            fieldLabel: 'Tipo Subred',
                            store: storeTipoSubred,
                            displayField: 'descripcion_tipo',
                            valueField: 'id',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            editable: false,
                            width: '30%',
                            emptyText: 'Seleccione',
                            forceSelection: true
                        },
                        { width: '20%',border:false},
                        { width: '20%',border:false},                        
                        {
                            xtype: 'datefield',
                            id: 'dateDesde',
                            fieldLabel: 'Fecha desde:',
                            format: 'Y-m-d',
                            displayField: 'descripcion_desde',
                            emptyText: 'Seleccione',
                            editable: false,
                            width: '30%'
                        },
                        
                        {
                            xtype: 'datefield',
                            id: 'dateHasta',
                            fieldLabel: 'Fecha hasta:',
                            format: 'Y-m-d',
                            displayField: 'descripcion_hasta',
                            emptyText: 'Seleccione',
                            editable: false,
                            width: '30%'
                        },
                       
                        {
                            xtype: 'textfield',
                            id: 'txtSubred',
                            fieldLabel: 'Subred',
                            emptyText: 'Escriba Subred',
                            value: '',
                            width: '200px'
                        },
                        { width: '20%',border:false},
                        { width: '20%',border:false},  
                        {
                            xtype: 'combobox',
                            id: 'sltEstadoSubred',
                            fieldLabel: 'Estado Subred',
                            store: storeEstadoSubred,
                            displayField: 'descripcion_estado',
                            valueField: 'id',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            emptyText: 'Seleccione',
                            queryMode: 'local',
                            width: '10%'
                        }
                        
                        
                        ],	
        renderTo: 'filtro'
    
    });

    grid = Ext.create('Ext.grid.Panel', {
        width: 930,
        height: 294,
        store: store,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
            
        columns:[
                {
                  id: 'idSubred',
                  header: 'idSubred',
                  dataIndex: 'idSubred',
                  hidden: true,
                  hideable: false
                },
                {
                    id: 'elemento',
                    header: 'Pe',
                    xtype: 'templatecolumn', 
                    width: 200,
                    tpl: '<span class="box-detalle">{nombrePe}</span>'
                  
                },
                {
                    header: 'Subred',
                    dataIndex: 'subRed',
                    width: 120,
                    sortable: true
                },
                {
                    header: 'Uso Subred',
                    dataIndex: 'uso',
                    width: 100,
                    sortable: true
                },
                {
                    header: 'Tipo Subred',
                    dataIndex: 'tipo',
                    width: 100,
                    sortable: true
                },
                {
                    header: 'Fecha Creación',
                    dataIndex: 'feCreacion',
                    width: 120,
                    sortable: true
                },
                {
                    header: 'Estado Subred',
                    dataIndex: 'estadoElemento',
                    width: 100,
                    sortable: true
                },
                
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 175,
                    items: [
                        {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_468-8398");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                              
                                if (rec.get('estadoElemento')=='Activo')
                                {
                                    if(boolPermiso){ 
                                        return 'button-grid-edit';
                                    }
                                    else{
                                        return 'icon-invisible';
                                    } 
                                }
                        },
                        tooltip: 'Editar',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                                window.location = ""+rec.get('idSubred')+"/edit";
                            }
                        },
                        
                        
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


 });

 function buscar()
 {
  
   
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.nombrePe         =  Ext.getCmp('txtNombrePe').value;
    store.getProxy().extraParams.uso          =    Ext.getCmp('sltUso').value;
    store.getProxy().extraParams.tipo         =   Ext.getCmp('sltTipoSubred').value;
    store.getProxy().extraParams.fecha_desde  =   Ext.getCmp('dateDesde').value;
    store.getProxy().extraParams.fecha_hasta  =   Ext.getCmp('dateHasta').value;
    store.getProxy().extraParams.estado_subred  =   Ext.getCmp('sltEstadoSubred').value;
    store.getProxy().extraParams.subred         =  Ext.getCmp('txtSubred').value;
   

   
    validacionesFiltro(); 
    //validacionSubred();
}

function limpiar(){
   
    Ext.getCmp('txtNombrePe').value="";
    Ext.getCmp('txtNombrePe').setRawValue("");    
    
    Ext.getCmp('sltUso').value="";
    Ext.getCmp('sltUso').setRawValue("");
    
    Ext.getCmp('sltTipoSubred').value="";
    Ext.getCmp('sltTipoSubred').setRawValue("");

    Ext.getCmp('dateDesde').value="";
    Ext.getCmp('dateDesde').setRawValue("");

    Ext.getCmp('dateHasta').value="";
    Ext.getCmp('dateHasta').setRawValue("");

    Ext.getCmp('sltEstadoSubred').value="";
    Ext.getCmp('sltEstadoSubred').setRawValue("");

    Ext.getCmp('txtSubred').value="";
    Ext.getCmp('txtSubred').setRawValue("");

    store.removeAll();
    grid.getStore().removeAll();
   
   
}

function validacionesFiltro() 
{
    if((Ext.getCmp('txtNombrePe').value == null) && (Ext.getCmp('sltUso').value == null) && (Ext.getCmp('sltTipoSubred').value == null) && 
    (Ext.getCmp('dateDesde').value == null) &&  (Ext.getCmp('dateHasta').value == null) && (Ext.getCmp('sltEstadoSubred').value == null) )
    {
        
            Ext.Msg.show({
                title: 'Error en la búsqueda',
                msg: 'Seleccione almenos un criterio de búsqueda ',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
            return false;
        
       
    }
    else{
        store.load();
    }
   
      return true;
    
       
    
}

/*function validacionSubred() 
{
    
   
    
    const element=Ext.getCmp('txtSubred');
    if (Ext.getCmp('txtSubred').value != "") 
    {
        

        // Patron para validar la ip

        const patronIp=new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])?(?:\/[1-9][0-9])$/gm);

        if (element.value.search(patronIp)!=0) {

        // Ip no es correcta
        Ext.Msg.show({
            title: 'Error en la búsqueda',
            msg: 'La Subred no es correcta ',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
       
        return false;

        } 
    }
  
    return true;
    
}*/