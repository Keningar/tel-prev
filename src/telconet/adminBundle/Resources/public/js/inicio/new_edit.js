

Ext.Loader.setConfig({
    //enabled: true
});
//Ext.Loader.setPath('Ext.ux', '../ux');

Ext.require([
    '*'
]);

Ext.onReady(function(){
    var tabs = new Ext.TabPanel({
        height: 650,
        renderTo: 'my-tabs',
        activeTab: 0,
        plain:true,
        autoRender:true,
        autoShow:true,
        items:[
             {contentEl:'tab_comercial', title:'Comercial'},
             {contentEl:'tab_planificacion', title:'Planificación'},
             {contentEl:'tab_soporte', title:'Soporte'},
             {contentEl:'tab_financiero', title:'Financiera'}
        ]            
    }); 
    /*,listeners:{
            activate: function(tab){
                    grid.view.refresh()

					
             {contentEl:'tab_tecnica', title:'Técnica'},
             {contentEl:'tab_financiero', title:'Financiera'}
            }*/
});