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
    
    formFeriados = Ext.create('Ext.form.Panel', {
        bodyStyle: 'padding: 0px 0px 0; background:#FFFFFF;',
        //bodyPadding: 15,
        width: 760,
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
                text: 'Regresar',
                handler: function() {
                    window.location.href = strUrlIndex;
                }
            }]
    });

    var objTxtDescripcion = Utils.objText();
    objTxtDescripcion.id = 'objTxtDescripcion';
    objTxtDescripcion.name = 'objTxtDescripcion';
    objTxtDescripcion.fieldLabel = "*Feriado:";
    objTxtDescripcion.width = 250;
    objTxtDescripcion.allowBlank = false;
    objTxtDescripcion.blankText = 'Ingrese el feriado';
    objTxtDescripcion.labelWidth = 50;
    objTxtDescripcion.hasfocus = true;
    objTxtDescripcion.value    = strDescripcion;
    objTxtDescripcion.readOnly = true;
    
    var objTipo = Utils.objText();
    objTipo.id = 'objTipo';
    objTipo.name = 'objTipo';
    objTipo.fieldLabel = "Tipo:";
    objTipo.width = 250;
    objTipo.labelWidth = 50;
    objTipo.value    = strTipo;
    objTipo.readOnly = true;
    
    var objCanton = Utils.objText();
    objCanton.id = 'objCanton';
    objCanton.name = 'objCanton';
    objCanton.fieldLabel = "Cantón:";
    objCanton.width = 250;
    objCanton.labelWidth = 50;
    objCanton.value    = strTipo;
    objCanton.readOnly = true;
    
    
    var objMes = Utils.objText();
    objMes.id = 'objMes';
    objMes.name = 'objMes';
    objMes.fieldLabel = "Mes:";
    objMes.width = 250;
    objMes.labelWidth = 50;
    objMes.value    = strMes;
    objMes.readOnly = true;

    var objDia = Utils.objText();
    objDia.id = 'objDia';
    objDia.name = 'objDia';
    objDia.fieldLabel = "Dia:";
    objDia.width = 250;
    objDia.labelWidth = 50;
    objDia.value    = strDia;
    objDia.readOnly = true;
    
    txtComentario = Ext.create('Ext.form.field.TextArea', {
        id: 'txtComentario',
        name: 'txtComentario',
        fieldLabel: 'Observación',
        labelWidth: 50,
        width: 320,
        readOnly: true
    });     

        var container = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'hbox',
                style: "border:none"
            },
            width: 1200,
            items: [
                    objTxtDescripcion, objTipo, objCanton
            ]
        });
    var container2 = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'hbox',
                style: "border:none"
            },
            width: 1200,
            items: [                
                    objMes, objDia, txtComentario                    
            ]
        });



    formFeriados.add(container);
    formFeriados.add(container2);
    formFeriados.getForm().findField('objTxtDescripcion').setValue(strDescripcion);    
    formFeriados.getForm().findField('objTipo').setValue(strTipo);
    formFeriados.getForm().findField('objMes').setValue(strMes);
    formFeriados.getForm().findField('objDia').setValue(strDia);
    formFeriados.getForm().findField('objCanton').setValue(strNombreCanton);
    formFeriados.getForm().findField('txtComentario').setValue(strComentario);
    
});



