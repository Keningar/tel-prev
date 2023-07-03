/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



modelMotivosNoPlanificacion = Ext.create('Ext.data.modelMotivosNoPlanificacion', {mName: 'modelMotivosNoPlanificacion'});
Ext.define('Ext.data.storeMotivosNoPlanificacion', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlMotivosNoPlanificacion,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            model: modelMotivosNoPlanificacion,
            autoLoad: true
        });
    }
});

modelUltimaMilla = Ext.create('Ext.data.modelUltimaMilla', {mName: 'modelUltimaMilla'});
Ext.define('Ext.data.storeUltimaMilla', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlUltimaMillaCoordinar,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            model: modelUltimaMilla,
            autoLoad: true
        });
    }
});

