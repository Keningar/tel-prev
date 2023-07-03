Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox',
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
var objStore        = '';
Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {
                name   : 'intIdCargo', 
                type   : 'string', 
                mapping: 'intIdCargo'
            },
            {
                name   : 'strDescripcion', 
                type   : 'string', 
                mapping: 'strDescripcion'
            },
            {
                name   : 'intIdRango', 
                type   : 'string', 
                mapping: 'intIdRango'
            },
            {
                name   : 'strRangoTotal', 
                type   : 'string', 
                mapping: 'strRangoTotal'
            },
            {
                name   : 'intRangoIni', 
                type   : 'string', 
                mapping: 'intRangoIni'
            },
            {
                name   : 'intRangoFin', 
                type   : 'string', 
                mapping: 'intRangoFin'
            },
            {
                name   : 'strEstado', 
                type   : 'string', 
                mapping: 'strEstado'
            },
            {
                name   : 'strLoginAux', 
                type   : 'string', 
                mapping: 'strLoginAux'
            }
        ]
    });

    var objStore = Ext.create('Ext.data.JsonStore',
    {
        model   : 'ListaDetalleModel',
        pageSize: 15,
        proxy   :
        {
            type   : 'ajax',
            url    : 'grid',
            timeout: 900000,
            reader :
            {
                type         : 'json',
                root         : 'cargos',
                totalProperty: 'total'
            },
            simpleSortMode: true
        },
        autoLoad: true
    });

    Ext.create('Ext.grid.Panel', 
    {
        id          : 'grid',
        width       : '100%',
        autoHeight  : true,
        title       : 'Listado de cargos en TelcoS+',
        renderTo    : 'grid',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store      : objStore,
            displayInfo: true,
            displayMsg : 'Mostrando cargos {0} - {1} of {2}',
            emptyMsg   : "No hay datos para mostrar"
        }),
        store      : objStore,
        viewConfig : 
        {
            emptyText          : 'No hay datos para mostrar',
            enableTextSelection: true,
            id                 : 'gv',
            trackOver          : true,
            stripeRows         : true,
            loadMask           : true
        }, 
        listeners:
        {
            viewready: function (grid)
            {
                var view = grid.view;
                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target    : view.el,
                    delegate  : '.x-grid-cell',
                    trackMouse: true,
                    renderTo  : Ext.getBody(),
                    listeners :
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex,
                                        columnText      = view.getRecord(parent).get(columnDataIndex).toString();
                                    if (columnText)
                                    {
                                        tip.update(columnText);
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }
                        }
                    }
                });
            },
            itemdblclick: function(view, record, item, index, eventobj, obj) 
            {
                var position = view.getPositionByEvent(eventobj),
                    data     = record.data,
                    value    = data[this.columns[position.column].dataIndex];
                if(value != undefined)
                {
                    Ext.Msg.show(
                        {
                            title: '¿Desea copiar el contenido?',
                            msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> "+
                            "<i class='fa fa-arrow-right' aria-hidden='true'></i> <b>" + value + "</b>",
                            buttons: Ext.Msg.OK,
                            icon: Ext.Msg.INFORMATION
                        });
                }
            }
        },

        columns: 
        [
            new Ext.grid.RowNumberer(),
            {
                id       : 'intIdCargo',
                header   : 'intIdCargo',
                dataIndex: 'intIdCargo',
                hidden   : true,
                hideable : false
            }, 
            {
                text     : 'Descripción de cargos en TelcoS+',
                width    :'30%',
                dataIndex: 'strDescripcion'
            },
            {
                text     : 'Rango de aprobación',
                width    : '20%',
                dataIndex: 'strRangoTotal'
            },
            {
                text     : 'Estado',
                width    : '15%',
                dataIndex: 'strEstado'
            },
            {
                text     : 'Auxiliar',
                width    : '15%',
                dataIndex: 'strLoginAux'
            },
            {
                header  : 'Acciones',
                xtype   : 'actioncolumn',
                width   : '20%',
                sortable: false,
                items   :
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassA         = "button-grid-edit";
                            this.items[0].tooltip = 'Asignar nuevo rango';
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = objStore.getAt(rowIndex);
                            var objParametros = {
                                intIdRango:rec.data.intIdRango,
                                strDescripcion:rec.data.strDescripcion,
                                intRangoIni:rec.data.intRangoIni,
                                intRangoFin:rec.data.intRangoFin,
                            };
                            getPanelRango(objParametros);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassA         = "button-grid-puntoCliente";
                            this.items[1].tooltip = 'Gestionar Persona Auxiliar';
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = objStore.getAt(rowIndex);
                            var objParametros = {
                                intIdRango:rec.data.intIdRango,
                                strDescripcion:rec.data.strDescripcion,
                                strLoginAux: rec.data.strLoginAux
                            };
                            getPanelAsignarNuevaPersona(objParametros);
                        }
                    }
                ]
            }
        ]
    });

    /**
     * Documentación para la función 'getPanelAsignarNuevaPersona'.
     *
     * Función que muestra un listado de personas las cuales pueden ser asignadas para
     * aprobar las solicitudes.
     *
     * @param object $objParametros {
     *                                  "intIdRango"   => identificador del rango del cargo.
     *                              }
     *
     * @return Ventana a mostrar en pantalla para seleccionar la nueva persona.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    function getPanelAsignarNuevaPersona(objParametros)
    {
        var nombresCompletosAux = '';
        storeAsignaEmpleado = new Ext.data.Store
            ({ 
                total: 'total',
                autoLoad:true,
                proxy: 
                {
                    type: 'ajax',
                    url : strUrlEmpleadosDepartamentCiudad,
                    reader: 
                    {
                        type: 'json',
                        totalProperty: 'result.total',
                        root: 'result.encontrados',
                        metaProperty: 'myMetaData'
                    }
                },
                fields:
                [
                    {name:'id_empleado',     mapping:'id_empleado'},
                    {name:'nombre_empleado', mapping:'nombre_empleado'}
                ]
            });

            Ext.Ajax.request({
                async: false,
                url: strUrlNombresCompletos,
                method: 'post',
                params:{
                        login: objParametros.strLoginAux
                       },
                success: function (response) {
                    if(response.responseText != null && response.responseText != '')
                    {
                        nombresCompletosAux = response.responseText;
                    }
                },
                failure: function (response) {
                    Ext.Msg.alert('Status', 'Request Failed.');
    
                }
            });

        combo_empleados = new Ext.form.ComboBox
            ({
                id: 'comboAsignadoEmpleado',
                name: 'comboAsignadoEmpleado',
                fieldLabel: "Empleado",
                store: storeAsignaEmpleado,
                displayField: 'nombre_empleado',
                valueField: 'id_empleado',
                value: nombresCompletosAux,
                queryMode: "remote",
                emptyText: '',
                listeners:
                        {
                            change:
                                function()
                                {
                                    Ext.getCmp('btnguardar2').setDisabled(!true);
                                }
                        }
            });

        var intIdRango = objParametros.intIdRango;

        var formPanel = Ext.create('Ext.form.Panel',
            {
                id           : 'objFormSetPersona',
                bodyPadding  : 5,
                waitMsgTarget: true,
                fieldDefaults: 
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget : 'side'
                },
                items: 
                [
                    {
                        xtype      : 'fieldset',
                        title      : '',
                        defaultType: 'textfield',
                        defaults   :
                        {
                            width: 300
                        },
                        items:
                        [
                            {
                                xtype     : 'displayfield',
                                fieldLabel: 'Cargo seleccionado',
                                name      : 'strCargo',
                                value     : objParametros.strDescripcion
                            },
                            combo_empleados
                        ]
                    }
                ],
                buttons:
                [
                    {
                        id      : 'btnguardar2',
                        text    : 'Guardar cambios',
                        disabled: true,
                        handler : function()
                        {
                            var objParametrosGuardar = {
                                intIdRango:intIdRango,
                                comboAsignadoEmpleado:Ext.getCmp('comboAsignadoEmpleado').value,
                            };
                            getGuardarPersona(objParametrosGuardar);
                        }
                    },
                    {
                        id      : 'btnEliminarAuxiliar',
                        text    : 'Eliminar Auxiliar',
                        handler : function()
                        {
                            var objParametrosEliminar = {
                                intIdRango:intIdRango
                            };
                            setEliminarAuxiliar(objParametrosEliminar);
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
            });

        win = Ext.create('Ext.window.Window',
              {
                   title      : 'Gestionar Persona Auxiliar',
                   modal      : true,
                   width      : 400,
                   closable   : true,
                   layout     : 'fit',
                   buttonAlign: 'center',
                   items: [formPanel]
              }).show();
    }


    /**
     * Documentación para la función 'getPanelRango'.
     *
     * Función que muestra los rangos para aprobar las solicitudes.
     *
     * @param object $objParametros {
     *                                  "intIdRango"   => identificador del rango del cargo.
     *                                  "intRangoIni"  => rango inicial.
     *                                  "intRangoFin"  => rango final.
     *                              }
     *
     * @return Ventana a mostrar en pantalla para ingresar los nuevos rangos.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    function getPanelRango(objParametros)
    {
        var intIdRango = objParametros.intIdRango;
        var strMensaje = "¡Por favor! ingresar solo números enteros no mayores a 100.";

        var formPanel = Ext.create('Ext.form.Panel',
            {
                id           : 'objFormSetRango',
                bodyPadding  : 5,
                waitMsgTarget: true,
                fieldDefaults: 
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget : 'side'
                },
                items: 
                [
                    {
                        xtype      : 'fieldset',
                        title      : '',
                        defaultType: 'textfield',
                        defaults   :
                        {
                            width: 300
                        },
                        items:
                        [
                            {
                                xtype     : 'displayfield',
                                fieldLabel: 'Cargo seleccionado',
                                name      : 'strCargo',
                                value     : objParametros.strDescripcion
                            },
                            {
                                xtype        : 'textfield',
                                id           : 'intRangoIni',
                                name         : 'intRangoIni',
                                fieldLabel   : 'Rango inicial',
                                displayField :'intRangoIni',
                                value        : objParametros.intRangoIni,
                                anchor       : '55%',
                                listeners:
                                {
                                    change:
                                        function(cmp, intRangoIniNuevo)
                                        {
                                            var arrayParametrosVal = new Array();
                                            var objExpRegular      = /^[0-9]{1,3}?$/;
                                            if(!objExpRegular.test(intRangoIniNuevo) || intRangoIniNuevo>100)
                                            {
                                                Ext.getCmp('intRangoIni').setValue('0');
                                                arrayParametrosVal.push(Ext.get('intRangoIni'));
                                                mostrarAlertaFormulario(strMensaje,arrayParametrosVal);
                                            }
                                            else
                                            {
                                                var enabled = objParametros.intRangoIni !== intRangoIniNuevo;
                                                Ext.getCmp('btnguardar').setDisabled(!enabled);
                                            }
                                        }
                                }
                            },
                            {
                                xtype        : 'textfield',
                                name         : 'intRangoFin',
                                id           : 'intRangoFin',
                                displayField :'intRangoFin',
                                fieldLabel   : 'Rango final',
                                value        : objParametros.intRangoFin,
                                anchor       : '55%',
                                listeners:
                                {
                                    change:
                                        function(cmp, intRangoFinNuevo)
                                        {
                                            var arrayParametrosVal = new Array();
                                            var objExpRegular      = /^[0-9]{1,3}?$/;
                                            if(!objExpRegular.test(intRangoFinNuevo) || intRangoFinNuevo>100)
                                            {
                                                Ext.getCmp('intRangoFin').setValue('0');
                                                arrayParametrosVal.push(Ext.get('intRangoFin'));
                                                mostrarAlertaFormulario(strMensaje,arrayParametrosVal);
                                            }
                                            else
                                            {
                                                var enabled = objParametros.intRangoFin !== intRangoFinNuevo;
                                                Ext.getCmp('btnguardar').setDisabled(!enabled);
                                            }
                                        }
                                }
                            }
                        ]
                    }
                ],
                buttons:
                [
                    {
                        id      : 'btnguardar',
                        text    : 'Guardar cambios',
                        disabled: true,
                        handler : function()
                        {
                            var objParametrosGuardar = {
                                intIdRango:intIdRango,
                                intRangoIni:Ext.getCmp('intRangoIni').getValue(),
                                intRangoFin:Ext.getCmp('intRangoFin').getValue(),
                            };
                            getGuardar(objParametrosGuardar);
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
            });

        win = Ext.create('Ext.window.Window',
              {
                   title      : 'Actualizar nuevo rango',
                   modal      : true,
                   width      : 270,
                   closable   : true,
                   layout     : 'fit',
                   buttonAlign: 'center',
                   items: [formPanel]
              }).show();
    }

    /**
     * Documentación para la función 'getGuardar'.
     *
     * Función que actualiza los rangos para aprobar las solicitudes.
     *
     * @param object $objParametros {
     *                                  "intIdRango"   => identificador del rango del cargo.
     *                                  "intRangoIni"  => rango inicial.
     *                                  "intRangoFin"  => rango final.
     *                               }
     *
     * @return string Texto a mostrar en pantalla si la transacción fue exitosa o no.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    function getGuardar(objParametros)
    {
        var intIdRango         = objParametros.intIdRango;
        var intRangoIni        = objParametros.intRangoIni;
        var intRangoFin        = objParametros.intRangoFin;
        var arrayParametrosVal = new Array();
        var strMensaje         = "¡Por favor! el rango inicial debe ser menor que el rango final.";

        if(parseInt(intRangoIni) >= parseInt(intRangoFin))
        {
            arrayParametrosVal.push(Ext.get('intRangoIni'));
            mostrarAlertaFormulario(strMensaje,arrayParametrosVal);
        }
        else
        {
            Ext.MessageBox.wait("Procesando...");
            Ext.Ajax.request({
                url    : strUrlSetRangos,
                method : 'post',
                params : {intIdRango: intIdRango, intRangoIni: intRangoIni, intRangoFin:intRangoFin},
                success: function (objResponse) 
                {
                    var strMensaje = objResponse.responseText;
                    Ext.MessageBox.hide();
                    objStore.load();
                    Ext.Msg.alert('Alerta',strMensaje);
                    win.destroy();
                },
                failure: function (objResponse)
                {
                    var strMensaje = objResponse.responseText;
                    objStore.load();
                    Ext.Msg.alert('Error ',strMensaje);
                    win.destroy();
                }
            });
        }
    }

    /**
     * Documentación para la función 'getGuardarPersona'.
     *
     * Función que actualiza la persona que podrá aprobar las solicitudes.
     *
     * @param object $objParametros {
     *                                  "intIdRango"             => identificador del rango del cargo.
     *                                  "comboAsignadoEmpleado"  => empleado auxiliar a guardar.
     *                               }
     *
     * @return string Texto a mostrar en pantalla si la transacción fue exitosa o no.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    function getGuardarPersona(objParametros)
    {
        var intIdRango              = objParametros.intIdRango;
        var comboAsignadoEmpleado   = objParametros.comboAsignadoEmpleado;
        var intIdPersona            = comboAsignadoEmpleado.slice(0, comboAsignadoEmpleado.indexOf("@") );

        Ext.MessageBox.wait("Procesando...");
        Ext.Ajax.request({
            url    : strUrlSetPersonaAuxiliar,
            method : 'post',
            params : {
                        intIdParametro: intIdRango,
                        intIdPersona  : intIdPersona
                     },
            success: function (objResponse) 
            {
                var strMensaje = objResponse.responseText;
                Ext.MessageBox.hide();
                objStore.load();
                Ext.Msg.alert('Alerta',strMensaje);
                win.destroy();
            },
            failure: function (objResponse)
            {
                var strMensaje = objResponse.responseText;
                objStore.load();
                Ext.Msg.alert('Error ',strMensaje);
                win.destroy();
            }
        });
    }

    /**
     * Documentación para la función 'setEliminarAuxiliar'.
     *
     * Función que elimina la persona que podrá aprobar las solicitudes.
     *
     * @param object $objParametros {
     *                                  "intIdRango"   => identificador del rango del cargo.
     *                               }
     *
     * @return string Texto a mostrar en pantalla si la transacción fue exitosa o no.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    function setEliminarAuxiliar(objParametros)
    {
        var intIdRango = objParametros.intIdRango;
        Ext.MessageBox.wait("Procesando...");
        Ext.Ajax.request({
            url    : strUrlSetEliminarAuxiliar,
            method : 'post',
            params : {
                        intIdParametro: intIdRango
                     },
            success: function (objResponse) 
            {
                var strMensaje = objResponse.responseText;
                Ext.MessageBox.hide();
                objStore.load();
                Ext.Msg.alert('Alerta',strMensaje);
                win.destroy();
            },
            failure: function (objResponse)
            {
                var strMensaje = objResponse.responseText;
                objStore.load();
                Ext.Msg.alert('Error ',strMensaje);
                win.destroy();
            }
        });
    }
});

    /**
     * Documentación para la función 'mostrarAlertaFormulario'.
     *
     * Función que muestra una alerta en el campo donde el usuario debe corregir.
     *
     * @param string $strMensaje      => String que contenga el mensaje general a mostrar.
     * @param array  $arrayParametros => Array que contenga los identificadores del elemento HTML.
     *
     * @return string Texto a mostrar en pantalla.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     */
    function mostrarAlertaFormulario(strMensaje, arrayParametros)
    {
        Ext.Msg.alert("Advertencia", `${strMensaje}`, function (btn) 
        {
            if (btn == 'ok')
            {
                for(i=0;i<arrayParametros.length;i++)
                {
                    let objCampo = Ext.get(arrayParametros[i]);
                    objCampo.addCls('animated tada');
                    objCampo.frame('red', 1, {
                        duration: 1000
                    });
                    objCampo.focus();
                    objCampo.setStyle({borderColor: 'red'});
                    setTimeout(
                        function () {
                            objCampo.removeCls('animated tada');
                        },
                        2000
                    );
                }
            }
        });
    }