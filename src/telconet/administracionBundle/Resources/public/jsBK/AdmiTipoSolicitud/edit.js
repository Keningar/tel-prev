/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validarFormulario()
{    
    var proceso = Ext.getCmp('cmb_proceso').getValue();
    var tarea = Ext.getCmp('cmb_tarea').getValue();
    var itemmenu = Ext.getCmp('cmb_itemmenu').getValue();
    
    if(proceso=="" || !proceso) {  proceso = 0; }
    if(tarea=="" || !tarea) {  tarea = 0; }
    if(itemmenu=="" || !itemmenu) {  itemmenu = 0; }
    Ext.get('escogido_proceso_id').dom.value = proceso;
    Ext.get('escogido_tarea_id').dom.value = tarea;
    Ext.get('escogido_itemmenu_id').dom.value = itemmenu;
  
    if(proceso==0)
    {
        //alert("No se ha escogido el Proceso");
        //return false;
    }
    else if(tarea==0)
    {
        //alert("No se ha escogido la Tarea");
        //return false;
    }
    else if(itemmenu==0)
    {
       // alert("No se ha escogido el Item Menu");
       // return false;
    }
  
    return true;
}

Ext.onReady(function() {
    
    /* ****************** MODULOS ************************ */
    Ext.define('ProcesosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_proceso', type:'int'},
            {name:'nombre_proceso', type:'string'}
        ]
    });
    storeProcesos = Ext.create('Ext.data.Store', {
            model: 'ProcesosList',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url : '../getListadoProcesos',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
    
    combo_procesos = new Ext.form.ComboBox({
            id: 'cmb_proceso',
            name: 'cmb_proceso',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'remote',
            width: 400,
            emptyText: 'Seleccione Proceso',
            store:storeProcesos,
            displayField: 'nombre_proceso',
            valueField: 'id_proceso',
            renderTo: 'combo_proceso',
            listeners:{
                select:{fn:function(combo, value) {
                    Ext.getCmp('cmb_tarea').reset();   
                    
                    storeTareas.proxy.extraParams = {id_proceso: combo.getValue()};
                    storeTareas.load({params: {}});
                }}
            }
    });
    
    
    /* ************* TAREAS ************************ */
    Ext.define('TareasList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_tarea', type:'int'},
            {name:'nombre_tarea', type:'string'}
        ]
    });
    storeTareas = Ext.create('Ext.data.Store', {
            model: 'TareasList',
            proxy: {
                type: 'ajax',
                url : '../getListadoTareas',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
    combo_tareas = new Ext.form.ComboBox({
            id: 'cmb_tarea',
            name: 'cmb_tarea',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'remote',
            width: 400,
            emptyText: 'Seleccione Tarea',
            store: storeTareas,
            displayField: 'nombre_tarea',
            valueField: 'id_tarea',
            renderTo: 'combo_tarea'
    });    
   
    /* ************* ITEM MENU ************************ */
    Ext.define('ItemsMenuList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_item', type:'int'},
            {name:'nombre_item', type:'string'}
        ]
    });
    storeItemsMenu = Ext.create('Ext.data.Store', {
            model: 'ItemsMenuList',
            proxy: {
                type: 'ajax',
                url : '../getListadoItemsMenu',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
    combo_itemmenu = new Ext.form.ComboBox({
            id: 'cmb_itemmenu',
            name: 'cmb_itemmenu',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'remote',
            width: 400,
            emptyText: 'Seleccione Item Menu',
            store: storeItemsMenu,
            displayField: 'nombre_item',
            valueField: 'id_item',
            renderTo: 'combo_item'
    });    
    
});