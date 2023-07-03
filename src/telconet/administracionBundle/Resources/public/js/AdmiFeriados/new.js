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
        id: 'tipoFeriado',
        name: 'tipoFeriado',
        width: 250,
        labelWidth: 50});
    
    storeMes = getMes();
    cmbMes = Ext.create('Ext.data.comboGenericoList', {store: storeMes,
        fieldLabel: "Mes:",
        id: 'mes',
        name: 'mes',
        width: 250,
        labelWidth: 50});

    storeDia = getDia();
    cmbDia = Ext.create('Ext.data.comboGenericoList', {store: storeDia,
        fieldLabel: "Dia:",
        id: 'dia',
        name: 'dia',
        width: 150,
        labelWidth: 50});
    
    cmbCanton = Ext.create('Ext.data.comboCanton', {
        id: 'cmbCanton',
        name: 'cmbCanton',
        labelWidth: 50}); 

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
        title: 'Agregar Feriados',
        renderTo: 'Div_New_Feriados',
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
                    var objTipo        = form.findField('tipoFeriado');
                    var objMes         = form.findField('mes');
                    var objDia         = form.findField('dia');
                    var objCanton      = form.findField('cmbCanton');
                    var objComentario  = form.findField('txtComentario');

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
                                intCanton      : objCanton.getValue(),
                                strComentario  : objComentario.getValue(),
                                intIdFeriados  : 0
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
                text: 'Cancelar',
                handler: function() {
                    this.up('form').getForm().reset();
                    storeFeriado.removeAll();
                }
            }]
    });
    

     Ext.getCmp('cmbCanton').setDisabled(true);
    
    var objTxtDescripcion = Utils.objText();

    objTxtDescripcion.id = 'objTxtDescripcion';
    objTxtDescripcion.name = 'objTxtDescripcion';
    objTxtDescripcion.fieldLabel = "*Feriado:";
    objTxtDescripcion.width = 250;
    objTxtDescripcion.allowBlank = false;
    objTxtDescripcion.blankText = 'Ingrese el feriado';
    objTxtDescripcion.labelWidth = 50;
    objTxtDescripcion.hasfocus = true;


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
});


