/**
 * @class Ext.chooser.Window
 * @extends Ext.window.Window
 * @author Ed Spencer
 * 
 * This is a simple subclass of the built-in Ext.window.Window class. Although it weighs in at 100+ lines, most of this
 * is just configuration. This Window class uses a border layout and creates a DataView in the central region and an
 * information panel in the east. It also sets up a toolbar to enable sorting and filtering of the items in the 
 * DataView. We add a few simple methods to the class at the bottom, see the comments inline for details.
 */
Ext.define('Ext.chooser.z_Window', {
    extend: 'Ext.window.Window',
    uses: [
        'Ext.layout.container.Border',
        'Ext.form.field.Text',
        'Ext.form.field.ComboBox',
        'Ext.toolbar.TextItem',
        'Ext.layout.container.Fit'
    ],
    height: 575,
    width:910,
    title: 'Galeria de Imagenes',
    closeAction: 'hide',
    layout: 'border',
    modal: true,
    border: false,
    bodyBorder: false,
    /**
     * initComponent is a great place to put any code that needs to be run when a new instance of a component is
     * created. Here we just specify the items that will go into our Window, plus the Buttons that we want to appear
     * at the bottom. Finally we call the superclass initComponent.
     */
    initComponent: function() {
        formPanel = Ext.create('Ext.form.Panel', {
            region: 'center',
            layout: 'fit',            
            items: {
                xtype: 'iconbrowser',
                autoScroll: true,
                cls: 'img-chooser-view',
                listeners: {
                    scope: this,
                    selectionchange: this.onIconSelect
                }
            },
            tbar: [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [                       
                        {
                            xtype: 'displayfield',
                            fieldLabel: 'Acciones'
                        },                        
                        {
                            iconCls: 'icon_add',
                            text: 'Agregar Imagen',
                            itemId: 'agregar',
                            scope: this,
                            handler: function() {
                                subirNuevaImagen(idElemento);                                
                            }
                        }
                    ]
                }              			
            ]
        });

        this.items =
            [
                formPanel,
                {
                    xtype: 'infopanel',
                    id:'viewPanel',
                    region: 'east',
                    split: true
                }
            ];
        this.buttons = [            
            {
                text: 'Aceptar',
                scope: this,
                handler: function() {                                    
                    this.destroy();
                }
            }
        ];

        this.callParent(arguments);

        /**
         * Specifies a new event that this component will fire when the user selects an item. The event is fired by the
         * fireImageSelected function below. Other components can listen to this event and take action when it is fired
         */
        this.addEvents(
            /**
             * @event selected
             * Fired whenever the user selects an image by double clicked it or clicking the window's OK button
             * @param {Ext.data.Model} image The image that was selected
             */
            'selected'
            );
    },
    /**
     * @private
     * Called whenever the user types in the Filter textfield. Filters the DataView's store
     */
    filter: function(field, newValue) {        
        var store = this.down('iconbrowser').store,
            view = this.down('dataview'),
            selModel = view.getSelectionModel(),
            selection = selModel.getSelection()[0];

        store.suspendEvents();
        store.clearFilter();
        store.filter({
            property: 'name',
            anyMatch: true,
            value: newValue
        });
        store.resumeEvents();
        if (selection && store.indexOf(selection) === -1) {
            selModel.clearSelections();
            this.down('infopanel').clear();
        }
        view.refresh();

    },
    /**
     * @private
     * Called whenever the user changes the sort field using the top toolbar's combobox
     */
    sort: function() {
        var field = this.down('combobox').getValue();

        this.down('dataview').store.sort(field);
    },
    /**
     * Called whenever the user clicks on an item in the DataView. This tells the info panel in the east region to
     * display the details of the image that was clicked on
     */
    onIconSelect: function(dataview, selections) {
        var selected = selections[0];

        if (selected) 
        {
            this.down('infopanel').loadRecord(selected);                      
            
            storeTags = new Ext.data.Store({ 
                total: 'total',
                autoLoad: true,                
                proxy: {
                    type: 'ajax',
                    url: url_getTags,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        estado: 'Activo'
                    }
                },
                fields:
                    [
                        {name: 'nombre_tag_documento', mapping: 'nombre_tag_documento'},
                        {name: 'id_tag_documento', mapping: 'id_tag_documento'}
                    ]
            });

            comboTags = new Ext.form.ComboBox({
                id: 'cmbTagsEdit',
                fieldLabel: 'Tipo Imagen:',
                xtype: 'combobox',
                typeAhead: true,
                width:300,
                triggerAction: 'all',
                selectOnFocus: true,
                labelStyle:'font-weight:bold',                
                displayField: 'nombre_tag_documento',
                valueField: 'id_tag_documento',
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: storeTags,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: 
                {
                    select: function(combo)
                    {
                        document.getElementById('idTagNuevo').value = combo.getValue();                        

                    }
                }
            });
            
            fileUpLoad = Ext.create('Ext.form.field.File', {
                xtype: 'filefield',
                id: 'form-file-edit',
                labelStyle:'font-weight:bold',
                width:300,               
                name: 'archivo',
                fieldLabel: 'Imagen:',                    
                emptyText: 'Seleccione una Archivo',
                buttonText: 'Browse',
                buttonConfig: {
                    iconCls: 'upload-icon'
                }
            });

            form = new Ext.create('Ext.form.Panel',
                {
                    id: 'panelEdit',
                    width: 380,
                    frame: true,
                    renderTo: 'tipoImagenEdit',
                    bodyPadding: '10 10 0',
                    defaults: {
                        anchor: '100%',
                        allowBlank: false,
                        msgTarget: 'side',
                        labelWidth: 50
                    },
                    items: [
                        comboTags,
                        fileUpLoad
                    ],
                    buttons: [{
                            text: 'Editar Imagen',
                            handler: function() {
                                var form = this.up('form').getForm();
                                if (form.isValid())
                                {
                                    form.submit({
                                        url: utl_editarImagen,
                                        params: {                                            
                                            tagViejo: document.getElementById('idTag').value,
                                            tagNuevo: document.getElementById('idTagNuevo').value,
                                            idImagen : document.getElementById('idImagen').value,
                                            idNodo   : idElemento
                                        },
                                        waitMsg: 'Editando Imagen...',
                                        success: function(fp, o)
                                        {
                                            Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn) {
                                                if (btn == 'ok')
                                                {
                                                    storeImagenNodo.load();
                                                    Ext.getCmp('viewPanel').clear();
                                                }
                                            });
                                        },
                                        failure: function(fp, o)
                                        {
                                            Ext.Msg.alert("Alerta", o.result.respuesta);
                                        }
                                    });                                   
                                }
                            }
                        }]
                });                                           
                                
                Ext.getCmp('cmbTagsEdit').setValue(document.getElementById('tag').value.toUpperCase());                                
        }
    },
    /**
     * Fires the 'selected' event, informing other components that an image has been selected
     */
    fireImageSelected: function() {
        var selectedImage = this.down('iconbrowser').selModel.getSelection()[0];

        if (selectedImage) {
            this.fireEvent('selected', selectedImage);
            this.hide();
        }
    }
});

function subirNuevaImagen(nodo) 
{
     storeTags = new Ext.data.Store({ 
        total: 'total',        
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : url_getTags,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'             
            }
        },
        fields:
              [
                {name:'nombre_tag_documento', mapping:'nombre_tag_documento'},
                {name:'id_tag_documento', mapping:'id_tag_documento'}
              ]
    });
    
    comboTags = new Ext.form.ComboBox({
        id: 'cmbTags',
        fieldLabel: 'Tipo Imagen:',
        xtype: 'combobox',        
        displayField: 'nombre_tag_documento',
        valueField: 'id_tag_documento',        
        loadingText: 'Buscando ...',        
        store: storeTags,
        lazyRender: true,
        listClass: 'x-combo-list-small'    
    });

    var formPanel = Ext.create('Ext.form.Panel',
        {
            width: 500,
            frame: true,
            bodyPadding: '10 10 0',
            defaults: {
                anchor: '100%',
                allowBlank: false,
                msgTarget: 'side',
                labelWidth: 50
            },
            items: [
                comboTags,
                {
                    xtype: 'filefield',
                    id: 'form-file',
                    name: 'archivo',
                    fieldLabel: 'Imagen:',                    
                    emptyText: 'Seleccione una Archivo',
                    buttonText: 'Browse',
                    buttonConfig: {
                        iconCls: 'upload-icon'
                    }
                }
            ],
            buttons: [{
                    text: 'Subir',
                    handler: function() {
                        var form = this.up('form').getForm();
                        if (form.isValid())
                        {                            
                            if(Ext.getCmp('cmbTags').value!=='' && Ext.getCmp('cmbTags').value!==null )
                            {
                                
                                form.submit({
                                    url: url_fileUpload,
                                    params: {
                                        idNodo: nodo,
                                        tag:Ext.getCmp('cmbTags').value
                                    },
                                    waitMsg: 'Procesando Imagen...',
                                    success: function(fp, o)
                                    {
                                        Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn) {
                                            if (btn == 'ok')
                                            {
                                                storeImagenNodo.load();
                                                win.destroy();                                           
                                            }
                                        });
                                    },
                                    failure: function(fp, o) 
                                    {
                                        Ext.Msg.alert("Alerta", o.result.respuesta);
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert("Advertencia", "Debe escoger el tipo de Imagen a subir");
                            }
                        }
                    }
                }, {
                    text: 'Cancelar',
                    handler: function() {
                        this.up('form').getForm().reset();                        
                        win.destroy();
                    }
                }]
        });

    var win = Ext.create('Ext.window.Window', {
        title: 'Agregar Imagen',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function borrarImagen(idImagen) {

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Borrando imagen...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    
    Ext.Msg.confirm('Informacion', 'Se eliminara la imagen. Desea continuar?', function(btn) {
        if (btn === 'yes') 
        {
            conn.request({
                url: url_eliminarImagen,
                method: 'post',
                params: {
                    idImagen: idImagen
                },
                success: function(response) 
                {                    
                    var text = Ext.JSON.decode(response.responseText);   
                    Ext.Msg.alert("Mensaje", text.respuesta, function(btn) {
                        if (btn === 'ok')
                        {                            
                            Ext.getCmp('viewPanel').clear();
                            storeImagenNodo.load();                            
                        }
                    });                    
                    
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });  
}
