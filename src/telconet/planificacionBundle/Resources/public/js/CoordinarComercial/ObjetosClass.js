/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


storeMotivosNoPlanificacion = Ext.create('Ext.data.storeMotivosNoPlanificacion', {mName: ''});
Ext.define('Ext.data.comboMotivosNoPlanificacion', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosNoPlanificacion',
    name: 'cmbMotivosNoPlanificacion',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosNoPlanificacion,
    queryMode: 'local',
    lazyRender: true,
    listClass: 'x-combo-list-small',
    initComponent: function() {
        var me = this;
        me.callParent();
    },
    constructor: function(options) {
        Ext.apply(this, options || {});
        this.superclass.constructor.apply(this, new Array(options));
    }
});

storeUltimaMilla = Ext.create('Ext.data.storeUltimaMilla', {mName: ''});
Ext.define('Ext.data.comboUltimaMilla', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbUltimaMilla',
    name: 'cmbUltimaMilla',
    fieldLabel: 'Ultima Milla',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombreTipoMedio',
    valueField: 'idTipoMedio',
    selectOnTab: true,
    store: storeUltimaMilla,
    queryMode: 'local',
    lazyRender: true,
    listClass: 'x-combo-list-small',
    initComponent: function() {
        var me = this;
        me.callParent();
    },
    constructor: function(options) {
        Ext.apply(this, options || {});
        this.superclass.constructor.apply(this, new Array(options));
    }
});
