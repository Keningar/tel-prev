/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.QuickTips.init();

Ext.onReady(function(){   
    
    
    
    Ext.define('Criterio', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_criterio_afectado', mapping:'id_criterio_afectado'},
            {name:'caso_id', mapping:'caso_id'},
            {name:'criterio', mapping:'criterio'},
            {name:'opcion', mapping:'opcion'}
        ]
    });
    Ext.define('Afectado', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id', mapping:'id'},
            {name:'id_afectado', mapping:'id_afectado'},
            {name:'id_criterio', mapping:'id_criterio'},
            {name:'caso_id_afectado', mapping:'caso_id_afectado'},
            {name:'nombre_afectado', mapping:'nombre_afectado'},
            {name:'descripcion_afectado', mapping:'descripcion_afectado'}
        ]
    });
    
    storeCriterios = new Ext.data.JsonStore(
    {
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : 'getCriterios',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
          {name:'id_criterio_afectado', mapping:'id_criterio_afectado'},
          {name:'caso_id', mapping:'caso_id'},
          {name:'criterio', mapping:'criterio'},
          {name:'opcion', mapping:'opcion'}
        ]                
    });
    gridCriterios = Ext.create('Ext.grid.Panel', {
        title:'Criterios de Seleccion',
        width: 450,
        height: 200,
        autoRender:true,
        enableColumnResize :false,
        id:'gridCriterios',
        store: storeCriterios,
        loadMask: true,
        frame:true,
        forceFit:true,
        columns:[
                {
                  id: 'id_criterio_afectado',
                  header: 'id_criterio_afectado',
                  dataIndex: 'id_criterio_afectado',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'caso_id',
                  header: 'caso_id',
                  dataIndex: 'caso_id',
                  hidden: true,
                  sortable: true
                },
                {
                  id: 'criterio',
                  header: 'Criterio',
                  dataIndex: 'criterio',
                  width: 100,
                  hideable: false
                },
                {
                  id: 'opcion',
                  header: 'Opcion',
                  dataIndex: 'opcion',
                  width: 300,
                  sortable: true
                }
            ],
                  
        renderTo: 'criterios'
    });
    
    
    ////////////////Grid  Afectados////////////////  
    storeAfectados = new Ext.data.JsonStore(
    {
        autoLoad: true,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getAfectados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name:'id', mapping:'id'},
            {name:'id_afectado', mapping:'id_afectado'},
            {name:'id_criterio', mapping:'id_criterio'},
            {name:'caso_id_afectado', mapping:'caso_id_afectado'},
            {name:'nombre_afectado', mapping:'nombre_afectado'},
            {name:'descripcion_afectado', mapping:'descripcion_afectado'}
        ]                
    });
    
    gridAfectados = Ext.create('Ext.grid.Panel', {
        title:'Equipos Afectados',
        width: 450,
        height: 200,
        sortableColumns:false,
        store: storeAfectados,
        id:'gridAfectados',
        enableColumnResize :false,
        loadMask: true,
        frame:true,
        forceFit:true,
        columns: [
                 Ext.create('Ext.grid.RowNumberer'),
                 {
                  id: 'id',
                  header: 'id',
                  dataIndex: 'id',
                  hidden: true,
                  hideable: false
                },
                 {
                  id: 'id_afectado',
                  header: 'id_afectado',
                  dataIndex: 'id_afectado',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_criterio',
                  header: 'id_criterio',
                  dataIndex: 'id_criterio',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'caso_id_afectado',
                  header: 'caso_id_afectado',
                  dataIndex: 'caso_id_afectado',
                  hidden: true,
                  hideable: false 
                },
                {
                  id: 'nombre_afectado',
                  header: 'Parte Afectada',
                  dataIndex: 'nombre_afectado',
                  width:250
                },
                {
                  id: 'descripcion_afectado',
                  header: 'Descripcion',
                  dataIndex: 'descripcion_afectado',
                  width:150
                }
                
            ],    
        renderTo: 'afectados'
    });
    
    var tb = Ext.create('Ext.toolbar.Toolbar');
    tb.add("<div style='font-weight:bold;'>Acciones:</div>");
    tb.add({
            icon: '/bundles/soporte/images/iconos/16/agregar_sintoma.png',
            handler: function() {
                rechazarAsignacion(id_ticket.value);
            },
            tooltip: 'Agregar Sintoma'
        }
    );  
    tb.add({
            icon: '/bundles/soporte/images/iconos/16/agregar_hipotesis.png',
            handler: function() {
                rechazarAsignacion(id_ticket.value);
            },
            tooltip: 'Agregar Hipotesis'
        }
    );
    tb.add({
            icon: '/bundles/soporte/images/iconos/16/agregar_tarea.png',
            handler: function() {
                rechazarAsignacion(id_ticket.value);
            },
            tooltip: 'Agregar Tarea'
        }
    );
        
    tabs = Ext.create('Ext.tab.Panel', {
        id:'tab_panel',
        renderTo: 'contenedor_principal',
        width: 915,
        autoScroll: true,
        activeTab: 0,
        defaults : { autoHeight: true },
        plain:true,
        tbar: tb,
        deferredRender:false,
        hideMode: 'offsets',
        frame:false,
        items: [{
            contentEl:'datos_generales', 
            title: 'Datos Generales',
            id:'tab_datos_generales',
            autoRender:true,
            autoShow:true,
            closable: false            
        },{
            contentEl:'datos_afectados', 
            title: 'Afectados',
            id:'tab_afectados',
            listeners: {
                activate: function(tab){
                    gridCriterios.view.refresh();
                    gridAfectados.view.refresh();
                }
            },
            closable: false
        }
        ]
    });
});