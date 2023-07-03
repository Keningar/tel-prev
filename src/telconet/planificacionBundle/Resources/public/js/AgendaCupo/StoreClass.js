/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


modelGenerico = Ext.create('Ext.data.modelGenerico', {mName: 'modelGenerico'});
Ext.define('Ext.data.storeGenerico', {
    extend: 'Ext.data.Store',
    mName: '',
    mUrl: '',
    total: 'total',
    proxy: {
        type: 'proxyGenerico',
    },
    model: modelGenerico,
    autoLoad: true,
    constructor: function(options) {
        Ext.apply(this, options || {});
        //this.superclass.constructor.apply(this,new Array(options));
    }
});

modelJurisdiccion = Ext.create('Ext.data.modelJurisdiccion', {mName: 'modelJurisdiccion'});
Ext.define('Ext.data.storeJurisdiccion', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: ajaxGetJurisdiccion,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    idEmpresa: '18',
                    estado: 'ACTIVE'
                }
            },
            model: modelJurisdiccion,
            autoLoad: true
        });
    }
});

modelPlantilla = Ext.create('Ext.data.modelPlantilla', {mName: 'modelPlantilla'});
Ext.define('Ext.data.storePlantilla', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: ajaxGetPlantilla,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    idEmpresa: '18',
                    estado: 'Todos'
                }
            },
            model: modelPlantilla,
            autoLoad: true
        });
    }
});


