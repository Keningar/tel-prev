/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.define('Ext.data.modelGenerico', {
    extend: 'Ext.data.Model',
    constructor: function(options) {
        Ext.apply(this, options || {});
    },
    fields: [
        {name: 'id', mapping: 'idJurisdiccion'},
        {name: 'descripcion', mapping: 'nombreJurisdiccion'}
    ]
});



Ext.define('Ext.data.modelCanton', {
    mName: '',
    constructor: function(options) {
        Ext.apply(this, options || {});
        return Ext.define(this.mName,
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_canton',          mapping: 'id_canton'},
                    {name: 'nombre_canton', mapping: 'nombre_canton'}
                ]
            });
    }
});



