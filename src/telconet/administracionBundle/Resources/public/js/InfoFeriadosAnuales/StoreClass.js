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
        type: 'proxyGenerico'
    },
    model: modelGenerico,
    autoLoad: true,
    constructor: function(options) {
        Ext.apply(this, options || {});
    }
});

modelCanton = Ext.create('Ext.data.modelCanton', {mName: 'modelCanton'});
Ext.define('Ext.data.storeCanton', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlgetCantones,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            model: modelCanton,
            autoLoad: true
        });
    }
});
