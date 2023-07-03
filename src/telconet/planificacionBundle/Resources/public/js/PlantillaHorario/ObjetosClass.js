/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


storeJurisdiccion = Ext.create('Ext.data.storeJurisdiccion', {mName: ''});
Ext.define('Ext.data.comboJurisdiccion', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbJurisdiccion',
    name: 'cmbJurisdiccion',
    fieldLabel: 'Jurisdicción',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'descripcion',
    valueField: 'id',
    selectOnTab: true,
    store: storeJurisdiccion,
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




