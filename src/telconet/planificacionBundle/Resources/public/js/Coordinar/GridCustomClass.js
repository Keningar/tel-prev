/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*function cargarColumnas(mStore)
{
    mStore.getStore().each(function(record)   
    {   
        var columnas = ''
        record.fields.each(function(field)  
        {  
            fieldValue = record.get(field.name);        
            columnas += eval("{ id: '" + field.name + "'," +
                             "header: '" + field.name +"'," +
                             "dataIndex: '" + field.name + "'," +
                             " },")
        });  
    }, this);
    
};*/
Ext.define('Ext.data.customGrid',
{           
    extend : 'Ext.grid.Panel',
        width: 1230,
        height: 503,
        loadMask: true,
        frame: false,
        columns:[
        ],
    constructor	: function(options)
    {
        this.initConfig(options);
        Ext.apply(this,options || {});
        me = this;
       // me.on('load', me.cargarColumnas, me);
       console.log("contructor");
       console.log(me.store);
       //me.cargarColumnas()
    },
    cargarColumnas : function()
    {
        
        me = this;
        console.log("dentro de la funcion");
        console.log(me.getStore());
        me.getStore().each(function(record)   
         {   
             var columnas = ''
             record.fields.each(function(field)  
             {  
                 fieldValue = record.get(field.name);        
                 columnas += eval("{ id: '" + field.name + "'," +
                                  "header: '" + field.name +"'," +
                                  "dataIndex: '" + field.name + "'," +
                                  " },")
                console.log(columnas);
             });  
         }, this);
    }
});