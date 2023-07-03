/* global Ext*/

var objDescripcion = '';
Ext.onReady(function() {

    Ext.define('FeriadosModel',
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idFeriados',  type: 'int'},
                {name: 'descripcion', type: 'string'},
                {name: 'tipo',        type: 'string'},
                {name: 'mes',         type: 'string'},
                {name: 'dia',         type: 'string'}
            ]
        });

    storeFeriado = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'FeriadosModel',
            autoload: true
        });

    storeTipoFeriado = getTiposFeriados();
    cmbTipoFeriado = Ext.create('Ext.data.comboGenericoList', {store: storeTipoFeriado,
        fieldLabel: "Tipo:",
        id: 'cmbTipoFeriado',
        name: 'cmbTipoFeriado',
        width: 250,
        labelWidth: 80});
    storeTipoFeriado.on('load', function()
    {
        cmbTipoFeriado.setValue(strTipo);
        cmbTipoFeriado.setRawValue(strTipo);
        $('#cmbTipoFeriado').val(strTipo);
    });    
    
    storeMes = getMes();
    cmbMes = Ext.create('Ext.data.comboGenericoList', {store: storeMes,
        fieldLabel: "Mes:",
        id: 'cmbMes',
        name: 'cmbMes',
        width: 250,
        labelWidth: 80});

    storeDia = getDia();
    cmbDia = Ext.create('Ext.data.comboGenericoList', {store: storeDia,
        fieldLabel: "Dia:",
        id: 'cmbDia',
        name: 'cmbDia',
        width: 150,
        labelWidth: 80});

    cmbCanton = Ext.create('Ext.data.comboCanton', {
        id: 'cmbCanton',
        name: 'cmbCanton',
        labelWidth: 50}); 

    storeCanton.on("load", function()
    {
        cmbCanton.setValue(intIdCanton);
        cmbCanton.setRawValue(strNombreCanton);
        $('#cmbCanton').val(intIdCanton);
    });    

    

    txtComentario = Ext.create('Ext.form.field.TextArea', {
        id: 'txtComentario',
        name: 'txtComentario',
        fieldLabel: 'Observación',
        labelWidth: 70,
        width: 320});
    
    

    this.cmbTipoFeriado.on('select',function(cmb){	
        if (cmb.getValue() === 'NACIONAL') 
        {
            Ext.getCmp('cmbCanton').disable();
        }
        else
        {
            Ext.getCmp('cmbCanton').enable();         
        }
    },this);  
    
    
    formFeriados = Ext.create('Ext.form.Panel', {
        bodyStyle: 'padding: 0px 0px 0; background:#FFFFFF;',
        //bodyPadding: 15,
        width: 740,
        title: 'Editar Feriados',
        renderTo: 'Div_Edit_Feriado',
        frame: true,
        //layout:'vbox',
        layoutConfig: {
            type: 'table',
            columns: 3,
            pack: 'center',
            align: 'middle',
            tableAttrs: {
                style: {
                    width: '90%',
                    height: '90%'
                }
            },
            tdAttrs: {
                align: 'left',
                valign: 'middle'
            }
        },
        buttonAlign: 'center',
        buttons: [
            {
                text: 'Guardar',
                name: 'btnGuardar',
                id: 'idBtnGuardar',
                disabled: false,
                handler: function() {
                    var form = formFeriados.getForm();
                    var objDescripcion = form.findField('objTxtDescripcion');
                    var objTipo        = form.findField('cmbTipoFeriado');
                    var objMes         = form.findField('cmbMes');
                    var objDia         = form.findField('cmbDia');

                    if (objDescripcion.getValue().length == 0)
                    {
                        Ext.Msg.alert("Error - Creación de Feriados", "Debe digitar una Descripción del Feriado");
                        objDescripcion.focus();
                        return false;
                    }
                    if (form.isValid())
                    {                    

                        var data = form.getValues();
                        Ext.get(document.body).mask('Guardando datos...');
                        Ext.Ajax.request({
                            url: strUrlSaveFeriado,
                            method: 'POST',
                            params: {
                                data,
                                strDescripcion : objDescripcion.getValue(),
                                strTipo        : objTipo.getValue(),
                                strMes         : objMes.getValue(),
                                strDia         : objDia.getValue(),
                                intIdFeriados  : intIdFeriados
                            },

                            success: function(response) {
                                Ext.get(document.body).unmask();
                                var json = Ext.JSON.decode(response.responseText);
                                if (json.strStatus == 'OK')
                                {
                                    Ext.Msg.alert('Creacion de Feriados ', json.strMessageStatus);
                                    window.location.href = strUrlIndex;
                                } else
                                {
                                    Ext.Msg.alert('Error - Creacion de Feriados ', json.strMessageStatus);
                                }
                            },
                            failure: function(result) {
                                Ext.get(document.body).unmask();
                                Ext.Msg.alert('Error - ', 'Error: ' + result.statusText);
                            }
                        });
                     }
                }
            },
            {
                text: 'Regresar',
                handler: function() {
                    window.location.href = strUrlIndex;
                }
            }]
    });
    Ext.getCmp('cmbTipoFeriado').setValue(strTipo);
    Ext.getCmp('cmbMes').setValue(strMes);        
    Ext.getCmp('cmbDia').setValue(strDia); 
    Ext.getCmp('txtComentario').setValue(strComentario);  
        
    Ext.getCmp('cmbCanton').setDisabled(true);        

    var objTxtDescripcion = Utils.objText();

    objTxtDescripcion.id = 'objTxtDescripcion';
    objTxtDescripcion.name = 'objTxtDescripcion';
    objTxtDescripcion.fieldLabel = "*Feriado:";
    objTxtDescripcion.width = 250;
    objTxtDescripcion.allowBlank = false;
    objTxtDescripcion.blankText = 'Ingrese el feriado';
    objTxtDescripcion.labelWidth = 80;
    objTxtDescripcion.hasfocus = true;
    objTxtDescripcion.value    = strDescripcion;
    var container = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'hbox',
                style: "border:none"
            },
            width: 1500,
            items: [
                    objTxtDescripcion, cmbTipoFeriado, cmbCanton
            ]
        });
    var container2 = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'hbox',
                style: "border:none"
            },
            width: 1500,
            items: [                
                    cmbMes, cmbDia, txtComentario
            ]
        });



    formFeriados.add(container);
    formFeriados.add(container2);
    formFeriados.getForm().findField('objTxtDescripcion').setValue(strDescripcion);    
});


