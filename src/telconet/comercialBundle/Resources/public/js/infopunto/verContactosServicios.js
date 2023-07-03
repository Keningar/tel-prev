var winListaVerContactos;

function showVerContactos(idServicio,descripcionServicio){
//alert(idServicio);
//arregloSeleccionados= new Array();
winListaVerContactos="";

if (!winListaVerContactos)
{

    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'idPersonaContacto', type: 'int'},
                 {name:'idPersona', type: 'int'},
                 {name:'nombres', type: 'string'},
                 {name:'apellidos', type: 'string'},
                 {name:'estado', type: 'string'},
                 {name:'contacto', type: 'string'},
                 {name:'idPersonaFormaContacto', type: 'string'}, 
                 {name:'formaContacto', type: 'string'},
                 {name:'valor', type: 'string'} 
		]
    });
    var storeVerContactos = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        groupField: 'contacto',
        proxy: {
            type: 'ajax',
            url: url_contactos_servicio,
            reader: {
                type: 'json',
                root: 'contactos',
                totalProperty: 'total'
            },
            simpleSortMode: true
        }
    });
    storeVerContactos.load({params: {idserv: idServicio}});    

    var showSummary = true;

    var listView = Ext.create('Ext.grid.Panel', {
        width:500,
        height:300,
        collapsible:false,
        title: '',
        renderTo: Ext.getBody(),
        store: storeVerContactos,
        multiSelect: false,
        frame: true,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        features: [{
            id: 'group',
            ftype: 'groupingsummary',
            groupHeaderTpl: '{name}',
            hideGroupedHeader: true,
            enableGroupingMenu: false
        }],
        columns: [new Ext.grid.RowNumberer(),{
            text: 'Contacto',
            width: 300,
            locked: false,
            tdCls: 'task',
            sortable: true,
            dataIndex: 'contacto',
            hideable: false,
            summaryType: 'count',
            summaryRenderer: function(value, summaryData, dataIndex) {
                return ((value === 0 || value > 1) ? '(' + value + ' Servicios)' : '(1 Servicio)');
            }
        },{
            text: 'Forma Contacto',
            width: 160,
            dataIndex: 'formaContacto',
            hideable: false,
            summaryType: 'count',
            summaryRenderer: function(value, summaryData, dataIndex) {
                            return ((value === 0 || value > 1) ? '(' + value + ' Servicios)' : '(1 Servicio)');
            }                        
        },{
            text: 'Valor',
            width: 300,
            dataIndex: 'valor',
            hideable: false,
            summaryType: 'count',
            summaryRenderer: function(value, summaryData, dataIndex) {
                            return ((value === 0 || value > 1) ? '(' + value + ' Servicios)' : '(1 Servicio)');
            }                        
        }	
        ],
        selModel: {
            selType: 'rowmodel'
        },
        listeners:{
            afterrender: function(listView, eOpts){
                    showSummary = !showSummary;
                    var view = listView.lockedGrid.getView();
                    view.getFeature('group').toggleSummaryRow(showSummary);
                    view.refresh();
                    view = listView.normalGrid.getView();
                    view.getFeature('group').toggleSummaryRow(showSummary);
                    view.refresh(); 
            }
        }  
                
                
    });


	winListaVerContactos = Ext.widget('window', {
                title: 'Contactos',
                closeAction: 'hide',
                width: 510,
                height:380,
                minHeight: 380,
                layout: 'fit',
                resizable: false,
                modal: true,
		closabled: false,
                items: [listView]
    });
	
}
winListaVerContactos.show();

function cierraVentana(){
    winListaVerContactos.close();
    
}
}

