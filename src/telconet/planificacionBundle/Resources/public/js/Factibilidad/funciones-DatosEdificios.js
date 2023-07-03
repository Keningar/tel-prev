/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var winDatosEdificio;

/************************************************************************ */
/******************* VER DATOS DEL EDIFICIO ***************************** */
/************************************************************************ */
function showDatosEdificio(rec) {
    formPanelDependeEdificio = Ext.create('Ext.form.Panel', {
        id: 'formPanelDependeEdificio',
        title: 'Datos del Edificio',
        bodyPadding: 7,
        waitMsgTarget: true,
        border: false,
        frame: false,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 200,
            anchor: '100%',
            msgTarget: 'side'
        },
        items:
            [
                {
                    xtype: 'fieldset',
                    title: '&nbsp;<label style="color:blue;">' +
                        '<i class="fa fa-tag" aria-hidden="true"></i></label>' +
                        '&nbsp;<b>Información del Punto</b>',
                    items:
                        [
                            {
                                xtype: 'combobox',
                                id: 'comboDependeEdificio',
                                name: 'comboDependeEdificio',
                                fieldLabel: '<b>Depende de Edificio</b>',
                                width: 270,
                                value: Ext.isEmpty(rec.get("strDependeDeEdificio")) ? 'N' : rec.get("strDependeDeEdificio"),
                                store: [['N', 'No'], ['S', 'Si']],
                                listeners: {
                                    select: function (combo) {
                                        if (combo.value === 'S') {
                                            Ext.getCmp("dependeEdificioDesc").setVisible(true);
                                            Ext.getCmp("btnBuscarEdificio").setVisible(true);
                                        } else {
                                            Ext.getCmp("dependeEdificioDesc").setVisible(false);
                                            Ext.getCmp("btnBuscarEdificio").setVisible(false);
                                        }
                                    }
                                }
                            },
                            {
                                xtype: 'panel',
                                border: false,
                                layout: { type: 'hbox', align: 'stretch' },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        id: 'dependeEdificioDesc',
                                        name: 'dependeEdificioDesc',
                                        fieldLabel: '<b>Edificio Padre*</b>',
                                        readOnly: true,
                                        hidden: rec.get("strDependeDeEdificio") !== 'S',
                                        value: rec.get("strNombreEdificio"),
                                        width: 480
                                    },
                                    {
                                        xtype: 'button',
                                        id: 'btnBuscarEdificio',
                                        name: 'btnBuscarEdificio',
                                        text: '<i class="fa fa-search" aria-hidden="true"></i>',
                                        tooltipType: 'title',
                                        tooltip: 'Buscar Edificio',
                                        hidden: rec.get("strDependeDeEdificio") !== 'S',
                                        handler: function () {
                                            showEdificios(rec.get("intCanton"));
                                        }
                                    }
                                ] 
                            },
                            {
                                xtype     : 'textfield',
                                id        : 'dependeEdificioId',
                                name      : 'dependeEdificioId',
                                readOnly  : true,
                                hidden    : true,
                                value     : ""
                            },
                            {
                                xtype     : 'textfield',
                                id        : 'tipoEdificio',
                                name      : 'tipoEdificio',
                                readOnly  : true,
                                hidden    : true
                            }
                        ]
                }
            ],
        
    });

    winDatosEdificio = Ext.widget('window', {
        title: 'Verificacion de Datos',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: true,
        items: [formPanelDependeEdificio],
        buttons:
            [
                {
                    text: '&nbsp;<label style="color:green;">' +
                    '<i class="fa fa-check-circle" aria-hidden="true"></i></label>' +
                    '&nbsp;<b>Continuar</b>',
                    name: 'guardarBtn',
                    disabled: false,
                    handler: function()
                    {
                            var status   = true;

                            if (Ext.getCmp('comboDependeEdificio').value == 'S' &&
                                (Ext.getCmp('dependeEdificioId').value == ''
                                || Ext.getCmp('dependeEdificioId').value ==null) &&
                                (Ext.getCmp('dependeEdificioDesc').value == ''
                                || Ext.getCmp('dependeEdificioDesc').value ==null))
                            {
                                Ext.Msg.alert('Error', 'Debe seleccionar el Edificio Padre' );
                                return false;
                            }

                            if (Ext.getCmp('comboDependeEdificio').value == 'S' &&
                                (Ext.getCmp('dependeEdificioId').value == ''
                                || Ext.getCmp('dependeEdificioId').value ==null) &&
                                (Ext.getCmp('dependeEdificioDesc').value != ''
                                && Ext.getCmp('dependeEdificioDesc').value !=null))
                            {
                                Ext.Msg.alert('Error', 'Debe seleccionar un Edificio Padre distinto' );
                                return false;
                            }
                            
                            Ext.MessageBox.show({
                                title      : "Mensaje",
                                msg        : '¿Está seguro de continuar con el proceso?',
                                closable   : false,
                                multiline  : false,
                                icon       : Ext.Msg.QUESTION,
                                buttons    : Ext.Msg.YESNO,
                                buttonText : {yes: 'Si', no: 'No'},
                                fn: function (buttonValue)
                                {
                                    if (buttonValue === "yes") {
    
                                        Ext.MessageBox.wait('Ejecutando proceso...');
                                        Ext.Ajax.request({
                                            url     : url_procesaNuevaFactibilidad,
                                            method  : 'post',
                                            timeout : 900000,
                                            params  : {
                                        'intIdPersona'          : rec.get("intIdPersona"),
                                        'id_punto'              : rec.get("id_punto"),
                                        'intIdServicio'         : rec.get("id_servicio"),
                                        'strEstado'             : rec.get("estadoFactibilidad"),
                                        'strTipoOrden'          : rec.get("tipo_orden"),
                                        'strLatitud'            : rec.get("latitud"),
                                        'strLongitud'           : rec.get("longitud"),
                                        'intElementoEdificioId' : Ext.getCmp('dependeEdificioId').value,
                                        'strElementoEdificio'   : Ext.getCmp('dependeEdificioDesc').value,
                                        'strDependeDeEdificio'  : Ext.getCmp('comboDependeEdificio').value,
                                        'strTipoEdificio'       : Ext.getCmp('tipoEdificio').value,
                                        'intPuntoCobertura'     : rec.get("intPuntoCobertura"),
                                        'intCanton'             : rec.get("intCanton"),
                                        'intParroquia'          : rec.get("intParroquia"),
                                        'intSector'             : rec.get("intSector"),
                                        'cliente'                : rec.get("cliente"),
                                        'intIdPersonaEmpresaRol' : rec.get("intIdPersonaEmpresaRol"),
                                        'ultimaMilla'           : rec.get("ultimaMilla"),
                                            },
                                            success: function (response) {
    
                                                objData = Ext.JSON.decode(response.responseText);
                                                message = objData.message;
                                                status  = typeof objData.status === 'undefined' ? false : objData.status;
    
                                                Ext.MessageBox.show({
                                                    title      : status ? 'Mensaje' : 'Error',
                                                    msg        : message,
                                                    buttons    : Ext.MessageBox.OK,
                                                    icon       : status ? Ext.MessageBox.INFO : Ext.MessageBox.ERROR,
                                                    closable   : false,
                                                    multiline  : false,
                                                    buttonText : {ok: 'Cerrar'},
                                                    fn: function (buttonValue) {
                                                        if (buttonValue === "ok" && status) {
                                                            winDatosEdificio.close();
                                                            winDatosEdificio.destroy();
                                                            buscar();
                                                        }
                                                    }
                                                });
                                            },
                                            failure: function (result) {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                }
                            });
                        
                    }
                },
                {
                    text: '&nbsp;<label style="color:red;">' +
                    '<i class="fa fa-times-circle" aria-hidden="true"></i></label>' +
                    '&nbsp;<b>Cerrar</b>',
                    handler: function () {
                        this.up('window').destroy();
                    }
                }
            ]
    });

    winDatosEdificio.show();
}