/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


Ext.define('Ext.data.comboGenericoList', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'descripcion',
    valueField: 'id',
    selectOnTab: true,
    queryMode: "local",
    listClass: 'x-combo-list-small',
    width: 325,
    forceSelection: true,
    initComponent: function() {
        var me = this;
        me.callParent();
    },
    constructor: function(options) {
        Ext.apply(this, options || {});
        this.superclass.constructor.apply(this, new Array(options));
    }
});

storeCanton = Ext.create('Ext.data.storeCanton', {mName: ''});
Ext.define('Ext.data.comboCanton', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbCanton',
    name: 'cmbCanton',
    fieldLabel: 'Cantón:',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_canton',
    valueField: 'id_canton',
    selectOnTab: true,
    store: storeCanton,
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
