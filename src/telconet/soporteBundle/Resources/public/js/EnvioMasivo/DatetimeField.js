Ext.define('Ext.ux.form.field.DateTime', {
    extend: 'Ext.form.FieldContainer',
    alias: 'widget.datetimefield',
    requires: [
        'Ext.form.field.Date',
        'Ext.form.field.Time'
    ],
    mixins: {
        field: 'Ext.form.field.Field'
    },

    //configurables
    combineErrors: true,
    msgTarget: 'under',
    layout: 'hbox',
    readOnly: false,
    allowBlank: true,

    /**
     * @cfg {String} dateFormat
     * Configuración conveniente para especificar el formato de la porción del date
     * Este valor es sobreescrito si el formato es especificado en el dateConfig
     * Por defecto es 'Y-m-d'
     */
    dateFormat: 'Y-m-d',

    /**
     * @cfg {String} timeFormat
     * Configuración conveniente para especificar el formato de la porción del time
     * Este valor es sobreescrito si el formato es especificado en el timeConfig
     * Por defecto es 'H:i'
     */
    timeFormat: 'H:i',
    /**
     * @cfg {Object} dateConfig
     * Opciones adicionales de configuración para el campo date
     */
    dateConfig: {},
    /**
     * @cfg {Object} timeConfig
     * Opciones adicionales de configuración para el campo time
     */
    timeConfig: {},


    // properties
    dateValue: null, // Holds the actual date
    /**
     * @property dateField
     * @type Ext.form.field.Date
     */
    dateField: null,
    /**
     * @property timeField
     * @type Ext.form.field.Time
     */
    timeField: null,

    /**
     * @cfg {String} readOnlyCls
     * La clase CSS aplicada al componente del elemento principal cuando este es readOnly.
     */
    readOnlyCls: Ext.baseCSSPrefix + 'form-readonly',

    initComponent: function () {
        var me = this;

        Ext.apply(me, {
            isFormField: true, //Incluir en la consulta de campo de formulario
            items: [
                Ext.apply({
                    format: me.dateFormat,
                    flex: 2,
                    isFormField: false, //excluir de la consulta del campo
                    listeners: {
                        'blur': function () {
                            me.onFieldChange();
                        },
                        scope: me
                    },
                    submitValue: false,
                    validateOnChange: false,
                    allowBlank: me.allowBlank,
                    xtype: 'datefield'
                }, me.dateConfig),

                Ext.apply({
                    format: me.timeFormat,
                    flex: 1,
                    isFormField: false, //excluir de la consulta del campo
                    listeners: {
                        'select': function () {
                            me.onFieldChange();
                        },
                        scope: me
                    },
                    submitValue: false,
                    allowBlank: me.allowBlank,
                    xtype: 'timefield'
                }, me.timeConfig)
            ]
        });

        me.callParent();

        me.dateField = me.down('datefield');
        me.timeField = me.down('timefield');

        me.initField();
    },

    beforeDestroy: function () {
        Ext.destroy(this.fieldCt);
        this.callParent(arguments);
    },

    delegateFn: function (fn) {
        this.items.each(function (item) {
            if (item[fn]) {
                item[fn]();
            }
        });
    },

    getErrors: function () {
        return [].concat(this.dateField.getErrors()).concat(this.timeField.getErrors());
    },

    getFormat: function () {
        var df = this.dateField,
                tf = this.timeField;
        return ((df.submitFormat || df.format) + " " + (tf.submitFormat || tf.format));
    },

    getSubmitValue: function(){   
        var format = this.dateTimeFormat || this.getFormat(),
            value = this.getValue();
            
        return value ? Ext.Date.format(value, format) : null;
    },
    
    getValue: function () {
        var me = this,
                value = null,
                date = me.dateField.getSubmitValue(),
                time = me.timeField.getSubmitValue(),
                format;

        if (date) {
            if (time) {
                format = me.getFormat();
                value = Ext.Date.parse(date + ' ' + time, format);
            } else {
                value = me.dateField.getValue();
            }
        }
        return value;
    },

    isDirty: function () {
        var dirty = false;
        if (this.rendered && !this.disabled) {
            this.items.each(function (item) {
                if (item.isDirty()) {
                    dirty = true;
                    return false;
                }
            });
        }
        return dirty;
    },

    onDisable: function () {
        this.delegateFn('disable');
    },

    onEnable: function () {
        this.delegateFn('enable');
    },

    onFieldChange: function () {
        this.fireEvent('change', this, this.getValue());
    },

    reset: function () {
        this.delegateFn('reset');
    },

    resetOriginalValue: function () {
        this.dateField.resetOriginalValue();
        this.timeField.resetOriginalValue();
    },

    markInvalid: function (errors) {
        this.dateField.markInvalid(errors);
        this.timeField.markInvalid(errors);
    },

    clearInvalid: function (errors) {
        this.dateField.clearInvalid(errors);
        this.timeField.clearInvalid(errors);
    },

    setReadOnly: function (readOnly) {
        this.dateField.setReadOnly(readOnly);
        this.timeField.setReadOnly(readOnly);
        this[readOnly ? 'addCls' : 'removeCls'](this.readOnlyCls);
        this.readOnly = readOnly;
    },

    setValue: function (value) {
        var format;

        if (Ext.isString(value)) {
            format = this.dateTimeFormat || this.getFormat();
            value = Ext.Date.parse(value, format);
        }
        this.dateField.setValue(value);
        this.timeField.setValue(value);
    },

    isValid: function () {
        return this.dateField.isValid() && this.timeField.isValid();
    }
});



