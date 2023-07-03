/*
 * This example features a window with a DataView from which the user can select images to add to a <div> on the page.
 * To create the example we create simple subclasses of Window, DataView and Panel. When the user selects an image
 * we just add it to the page using the insertSelectedImage function below.
 * 
 * Our subclasses all sit under the Ext.chooser namespace so the first thing we do is tell Ext's class loader that it
 * can find those classes in this directory (InfoPanel.js, IconBrowser.js and Window.js). Then we just need to require
 * those files and pass in an onReady callback that will be called as soon as everything is loaded.
 */
Ext.QuickTips.init();
Ext.onReady(function() {

    Ext.tip.QuickTipManager.init();  // enable tooltips   


    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d H:i:s',
        width: 325,
        renderTo: "fecha_inicio",
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d H:i:s',
        width: 325,
        renderTo: "fecha_fin",
        //anchor : '65%',
        //layout: 'anchor'
    });

    new Ext.panel.Panel({
        title: 'Plantilla',
        renderTo: "plantilla_mail",
        width: 1000,
        height: 850,
        frame: true,
        layout: 'fit',
        items: {
            xtype: 'htmleditor',
            id: 'plantillaPanel',
            enableColors: true,
            enableAlignments: true,
            /*plugins: [
             Ext.create('Ext.ux.form.plugin.HtmlEditor',{
             enableAll:  true
             })
             ],*/
        }
    });

    var msg = function(title, msg) {
        Ext.Msg.show({
            title: title,
            msg: msg,
            minWidth: 200,
            modal: true,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    };


    var tpl = new Ext.XTemplate(
        'File processed on the server.<br />',
        'Name: {fileName}<br />',
        'Size: {fileSize:fileSize}'
        );
    Ext.create('Ext.form.Panel', {
        renderTo: 'fi-form',
        width: 500,
        frame: true,
        title: 'Formulario Subir Imagen',
        bodyPadding: '10 10 0',
        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },
        items: [{
                xtype: 'filefield',
                id: 'form-file',
                name: 'archivo',
                emptyText: 'Seleccione una imagen',
                buttonText: 'Browse',
                buttonConfig: {
                    iconCls: 'upload-icon'
                }
            }],
        buttons: [{
                text: 'Subir',
                handler: function() {
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: url_fileUpload,
                            waitMsg: 'Subiendo la imagen...',
                            success: function(fp, o) {
                                msg('Success', 'Imagen "' + o.result.fileName + '" procesada exitosamente');
                            },
                            failure: function() {
                                Ext.Msg.alert("Error", Ext.JSON.decode(this.response.responseText).message);
                            }
                        });
                    }
                }
            }, {
                text: 'Resetear',
                handler: function() {
                    this.up('form').getForm().reset();
                }
            }]
    });

    /*
     * This button just opens the window. We render it into the 'buttons' div and set its
     * handler to simply show the window
     */
    insertButton = Ext.create('Ext.button.Button', {
        text: "Seleccione Imagen",
        renderTo: 'buttons2',
        handler: function() {
            showImages();
        }
    });


    $('.emailWidgets').show();
});

function showImages()
{
    Ext.Loader.setConfig({enabled: true});
    Ext.Loader.setPath('Ext.chooser', '../../../bundles/soporte/js/Noticias');
    Ext.Loader.setPath('Ext.ux', '../../../public/js/ext-4.1.1/src/ux');

    Ext.require([
        'Ext.button.Button',
        'Ext.data.proxy.Ajax',
        'Ext.chooser.z_InfoPanel',
        'Ext.chooser.z_IconBrowser',
        'Ext.chooser.z_Window',
        'Ext.ux.DataView.Animated',
        'Ext.toolbar.Spacer'
    ]);

    /*
     * Here is where we create the window from which the user can select images to insert into the 'images' div.
     * This window is a simple subclass of Ext.window.Window, and you can see its source code in Window.js.
     * All we do here is attach a listener for when the 'selected' event is fired - when this happens it means
     * the user has double clicked an image in the window so we call our insertSelectedImage function to add it
     * to the DOM (see below).
     */
    win = Ext.create('Ext.chooser.z_Window', {
        animateTarget: insertButton.getEl(),
        listeners: {
            selected: insertSelectedImage
        }
    });

    win.show();
}


/*
 * This function is called whenever the user double-clicks an image inside the window. It creates
 * a new <img> tag inside the 'images' div and immediately hides it. We then call the show() function
 * with a duration of 500ms to fade the image in. At the end we call .frame() to give a visual cue
 * to the user that the image has been inserted
 */
function insertSelectedImage(image) {
    //(!Ext.isIE6? '<img src="/public/uploads/imagesPlantilla/{thumb}" width="50" height="50" />' : 
    var htmlImagen = '<img src="'+ image.get('url') + '" />';

    var editorPlantilla = Ext.getCmp("plantillaPanel");
    var before = editorPlantilla.getValue();
    editorPlantilla.insertAtCursor(htmlImagen);
    var after = editorPlantilla.getValue();
    if (before == after) {
        editorPlantilla.setValue(before + htmlImagen);
    }
}


function validarFormulario() {

    /*
     * 		VALIDACION PARA NOMBRE DE LA PLANTILLA
     */
    if (document.getElementById("telconet_schemabundle_plantillaNotificacionExternatype_nombrePlantilla").value == "")
    {
        Ext.Msg.alert("Alerta", "Debe ingresar un nombre de la noticia.");
        return false;
    }

    if ((Ext.getCmp('fechaDesde').getValue() != null) && (Ext.getCmp('fechaHasta').getValue() != null))
    {
        if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
        {
            Ext.Msg.alert("Alerta", "La fecha Desde debe ser menor a la fecha Hasta.");
            return false;
        }
        else
        {
            document.getElementById("telconet_schemabundle_plantillaNotificacionExternatype_fecha_desde").value = Ext.getCmp('fechaDesde').getRawValue();
            document.getElementById("telconet_schemabundle_plantillaNotificacionExternatype_fecha_hasta").value = Ext.getCmp('fechaHasta').getRawValue();

        }
    }
    else
    {
        Ext.Msg.alert("Alerta", "Las fechas son obligatorias." + Ext.getCmp('plantillaPanel').getValue());
        return false;
    }

    if (Ext.getCmp('plantillaPanel').getValue() == "")
    {
        Ext.Msg.alert("Alerta", "Debe ingresar el contenido de la Noticia.");
        return false;
    } else
    {
        document.getElementById("telconet_schemabundle_plantillaNotificacionExternatype_plantilla_mail").value = Ext.getCmp('plantillaPanel').getValue();
    }

    return true;

}

function buscar() {

    eventStore.removeAll();
    eventStore.getProxy().extraParams.startDate = globalStartDate;
    eventStore.getProxy().extraParams.endDate = globalEndDate;
    eventStore.getProxy().extraParams.estado = Ext.getCmp('cmb_estado').getValue();
    eventStore.load();
}

function limpiar() {
    Ext.getCmp('estado').setRawValue("");

    eventStore.removeAll();
    eventStore.getProxy().extraParams.startDate = globalStartDate;
    eventStore.getProxy().extraParams.endDate = globalEndDate;
    eventStore.getProxy().extraParams.estado = Ext.getCmp('cmb_estado').getValue();
    eventStore.load();
}

/***************************************************************/
function verPlantillaTipo() {
    $('.emailWidgets').show();
}