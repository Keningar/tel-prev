Ext.define('Ext.view.util.Multiupload', {
    extend: 'Ext.form.Panel',
    border: 0,
    alias: 'widget.multiupload',
    margins: '2 2 2 2',
    fileslist: [],
    frame: false,
    items: [
        {
            name:'archivos[]',
            xtype: 'filefield',
            buttonOnly: true,
            listeners: {
                change: function (view, value, eOpts) {
                    var parent = this.up('form');
                    parent.onFileChange(view, value, eOpts);
                }
            }

        }

    ],
    onFileChange: function (view, value, eOpts) {
        var fileNameIndex = value.lastIndexOf("/") + 1;
        if (fileNameIndex == 0) {
            fileNameIndex = value.lastIndexOf("\\") + 1;
        }
        var filename = value.substr(fileNameIndex);

        var IsValid = this.fileValidiation(view, filename);
        if (!IsValid) {
            return;
        }
        this.fileslist.push(filename);
        numArchivosSubidos++;
        var addedFilePanel = Ext.create('Ext.form.Panel', {
            frame: false,
            border: 0,
            padding: 2,
            margin: '0 10 0 0',
            layout: {
                type: 'hbox',
                align: 'middle'
            },
            items: [
                {
                    xtype: 'button',
                    text: null,
                    border: 0,
                    width: 30,
                    margin:0,
                    padding:0,
                    frame: false,
                    iconCls: 'button-grid-delete',
                    tooltip: 'Eliminar',
                    listeners: {
                        click: function (me, e, eOpts) {
                            var currentform = me.up('form');
                            var mainform = currentform.up('form');
                            var lbl = currentform.down('label');
                            mainform.fileslist.pop(lbl.text);
                            mainform.remove(currentform);
                            currentform.destroy();
                            mainform.doLayout();
                            numArchivosSubidos--;
                        }
                    }
                },
                {
                    xtype: 'label',
                    padding: 5,
                    listeners: {
                        render: function (me, eOpts) {
                            me.setText(filename);
                        }
                    }
                },
                {
                    xtype: 'image',
                    src: '/public/images/attach.png',
                    width: 17

                }
            ]
        });

        var newUploadControl = Ext.create('Ext.form.FileUploadField', {
            buttonOnly: true,
            name:'archivos[]',
            listeners: {
                change: function (view, value, eOpts) {
                    var parent = this.up('form');
                    parent.onFileChange(view, value, eOpts);
                }
            }
        });
        view.hide();
        addedFilePanel.add(view);
        this.insert(0, newUploadControl);
        this.add(addedFilePanel);
    },

    fileValidiation: function (me, filename) {
        var isValid = true;
        var indexofPeriod = me.getValue().lastIndexOf("."),
            uploadedExtension = me.getValue().substr(indexofPeriod + 1, me.getValue().length - indexofPeriod);
        
        if (Ext.Array.contains(this.fileslist, filename)) {
            isValid = false;
            me.setActiveError('El archivo ' + filename + ' ya está agregado!');
            Ext.MessageBox.show({
                title: 'Error',
                msg: 'El archivo ' + filename + ' ya está agregado!',
                buttons: Ext.Msg.OK,
                icon: Ext.Msg.ERROR
            });
            /* Se setea a null porque no es válido el archivo a subir, en este caso la única validación que se tiene es que el o los archivos
             * no se encuentren en el listado de los archivos a subir y por ende no se pueden subir los archivos
             */
            me.setRawValue(null);
            me.reset();
        }
        return isValid;
    }
});