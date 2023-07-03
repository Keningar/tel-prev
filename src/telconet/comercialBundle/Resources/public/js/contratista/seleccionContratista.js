var itemsPerPage = 10;

function showContratistas(input1,input2,input3,titulo,rootstore)
{
    var winListaContratistas='';
    var formContratistas = '';
    var filterPanelContratistas = '';
    var storeContratistas='';
    var listViewContratistas ='';

    var TFNombre='';
    var TFIdentificacion='';

    TFNombre = new Ext.form.TextField({
                    name: 'nombreContratista',
                    fieldLabel: 'Nombre',
                    xtype: 'textfield'
             });

     TFIdentificacion = new Ext.form.TextField({
                    name: 'identificacionContratista',
                    fieldLabel: 'Identificacion',
                    xtype: 'textfield'
             });   

    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idPersonaEmpresaRol', type: 'int'},
            {name:'idPersona', type: 'int'},
            {name:'Nombre', type: 'string'},
            {name:'Direccion', type: 'string'}
        ]
    });
    storeContratistas = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: url_contratistas,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
        extraParams:{nombre:'',identificacion:''},
        simpleSortMode: true 
        },
        listeners: {
            beforeload: function(store){ 
                storeContratistas.getProxy().extraParams.nombre        = Ext.ComponentQuery.query('textfield[name=nombreContratista]')[0].value;
                storeContratistas.getProxy().extraParams.identificacion= 
                    Ext.ComponentQuery.query('textfield[name=identificacionContratista]')[0].value;  
            }
        }	 	
    });
    storeContratistas.load({params: {start: 0, limit: 10}});


    listViewContratistas = Ext.create('Ext.grid.Panel', {
        width:500,
        height:300,
        collapsible:false,
        title: '',
        renderTo: Ext.getBody(),
        store: storeContratistas,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeContratistas,
            displayInfo: true,
            displayMsg: 'Mostrando '+titulo+' {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),

        columns: [new Ext.grid.RowNumberer(),  
            {
                text: 'Nombres',
                width: 220,
                dataIndex: 'Nombre'
            },
            {
                text: 'Direccion',
                dataIndex: 'Direccion',
                align: 'right',
                width: 190			
            }
        ],
        listeners: {
            itemdblclick:{
                fn: function( view, rec, node, index, e, options ){
                    $(input3).val(rec.data.idPersonaEmpresaRol);
                    $(input2).val(rec.data.idPersona);
                    $(input1).val(rec.data.Nombre);  
                    cierraVentana();
                }
            } 

        }
    });
    
    filterPanelContratistas = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout:{
            type:'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },                     
        defaults: {
            bodyStyle: 'padding:10px'
        },
        collapsible : true,
        collapsed: true,
        width: 800,
        title: 'Criterios de busqueda',
        buttons: [                   
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: Buscar
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: Limpiar
                    }
                ],                
        items: [
                    TFNombre,
                    { width: '20%',border:false},
                    { width: '20%',border:false},
                    { width: '20%',border:false},
                    TFIdentificacion
                ]	
    });
    
    formContratistas = Ext.widget('form', {
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        border: false,
        bodyPadding: 10,
        fieldDefaults: {
            labelAlign: 'top',
            labelWidth: 100,
            labelStyle: 'font-weight:bold'
        },
        defaults: {
            margins: '0 0 10 0'
        },
        items: [			
            filterPanelContratistas,listViewContratistas			
        ]
    });


    winListaContratistas = Ext.create('Ext.window.Window',
    {
        title: titulo,
        closable: true,
        modal: true,
        width: 510,
        height:380,
        minHeight: 380,
        resizable: false,
        layout: 'fit',
        items: [formContratistas]
    }).show();
    
    function cierraVentana(){
        winListaContratistas.close();
        formContratistas.destroy();
    }

    function Buscar()
    {
        if(Ext.ComponentQuery.query('textfield[name=nombreContratista]')[0].value!=null 
                || Ext.ComponentQuery.query('textfield[name=identificacionContratista]')[0].value!=null)
        {
            storeContratistas.load({params: {start: 0, limit: 10}});

        }
        else
        {
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor Ingrese un nombre o una identificación para buscar',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }


    }

    function Limpiar()
    {
        Ext.ComponentQuery.query('textfield[name=nombreContratista]')[0].value = "";
        Ext.ComponentQuery.query('textfield[name=nombreContratista]')[0].setRawValue("");

        Ext.ComponentQuery.query('textfield[name=identificacionContratista]')[0].value  = "";
        Ext.ComponentQuery.query('textfield[name=identificacionContratista]')[0].setRawValue("");

        storeContratistas.loadData([],false);
        storeContratistas.currentPage                          = 1;

        storeContratistas.getProxy().extraParams.nombre        = Ext.ComponentQuery.query('textfield[name=nombreContratista]')[0].value;
        storeContratistas.getProxy().extraParams.identificacion= Ext.ComponentQuery.query('textfield[name=identificacionContratista]')[0].value;  

        storeContratistas.load();
    }

}

