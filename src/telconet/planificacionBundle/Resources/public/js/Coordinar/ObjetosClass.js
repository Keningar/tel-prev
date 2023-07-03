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

storeSectores = Ext.create('Ext.data.storeSectores', {mName: ''});
Ext.define('Ext.data.comboSectores', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbSector',
    name: 'cmbSector',
    fieldLabel: 'Sector',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_sector',
    valueField: 'id_sector',
    selectOnTab: true,
    store: storeSectores,
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

storeEstadoPunto = Ext.create('Ext.data.storeEstadoPunto', {mName: ''});
Ext.define('Ext.data.comboEstadoPunto', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbEstadoPunto',
    name: 'cmbEstadoPunto',
    fieldLabel: 'Estado del Punto',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'estado_punto_busqueda',
    valueField: 'estado_punto_busqueda',
    selectOnTab: true,
    store: storeEstadoPunto,
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

storeMotivosRechazo = Ext.create('Ext.data.storeMotivosRechazo', {mName: ''});
Ext.define('Ext.data.comboMotivosRechazo', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosRechazo',
    name: 'cmbMotivosRechazo',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosRechazo,
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

storeMotivosAnulacion = Ext.create('Ext.data.storeMotivosAnulacion', {mName: ''});
Ext.define('Ext.data.comboMotivosAnulacion', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosAnulacion',
    name: 'cmbMotivosAnulacion',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosAnulacion,
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

storeMotivosRePlanificacion = Ext.create('Ext.data.storeMotivosRePlanificacion', {mName: ''});
Ext.define('Ext.data.comboMotivosRePlanificacion', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosRePlanificacion',
    name: 'cmbMotivosRePlanificacion',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosRePlanificacion,
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

storeMotivosDetenido = Ext.create('Ext.data.storeMotivosDetenido', {mName: ''});
Ext.define('Ext.data.comboMotivosDetenido', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosDetenido',
    name: 'cmbMotivosDetenido',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosDetenido,
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

storeMotivosReplanificarInspeccion = Ext.create('Ext.data.storeMotivosReplanificarInspeccion', {mName: ''});
Ext.define('Ext.data.comboMotivosReplanificarInspeccion', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosReplanificarInspeccion',
    name: 'cmbMotivosReplanificarInspeccion',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosReplanificarInspeccion,
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
storeMotivosDetenerInspeccion = Ext.create('Ext.data.storeMotivosDetenerInspeccion', {mName: ''});
Ext.define('Ext.data.comboMotivosDetenerInspeccion', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosDetenerInspeccion',
    name: 'cmbMotivosDetenerInspeccion',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosDetenerInspeccion,
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
storeMotivosRechazarInspeccion = Ext.create('Ext.data.storeMotivosRechazarInspeccion', {mName: ''});
Ext.define('Ext.data.comboMotivosRechazarInspeccion', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosRechazarInspeccion',
    name: 'cmbMotivosRechazarInspeccion',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosRechazarInspeccion,
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
storeMotivosAnularInspeccion = Ext.create('Ext.data.storeMotivosAnularInspeccion', {mName: ''});
Ext.define('Ext.data.comboMotivosAnularInspeccion', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosAnularInspeccion',
    name: 'cmbMotivosAnularInspeccion',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosAnularInspeccion,
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
storeMotivosRechazarSolInspeccion = Ext.create('Ext.data.storeMotivosRechazarSolInspeccion', {mName: ''});
Ext.define('Ext.data.comboMotivosRechazarSolInspeccion', {
    extend: 'Ext.form.field.ComboBox',
    mName: '',
    xtype: 'combobox',
    id: 'cmbMotivosRechazarSolInspeccion',
    name: 'cmbMotivosRechazarSolInspeccion',
    fieldLabel: 'Motivo',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'nombre_motivo',
    valueField: 'id_motivo',
    selectOnTab: true,
    store: storeMotivosRechazarSolInspeccion,
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
