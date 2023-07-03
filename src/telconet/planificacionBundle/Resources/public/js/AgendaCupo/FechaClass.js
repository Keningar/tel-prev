/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.define('Ext.data.fecha', {
    extend: 'Ext.form.DateField',
    labelAlign: 'left',
    xtype: 'datefield',
    format: 'Y-m-d',
    width: 250,
    editable: false,
    initComponent: function() {
        var me = this;
        me.callParent();
    },
    constructor: function(options) {
        Ext.apply(this, options || {});
        this.superclass.constructor.apply(this, new Array(options));
    }
});

