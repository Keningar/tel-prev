/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var strEmpresaPermitida = 'TN';

//Variables para uso de Flujos de DC ( Hosting )
var intTotalPrecioUnitarioDC = 0;
var descrProcesador      = '';
var descrMemoriaRam      = '';
var descrStorage         = '';
var descrProductoUnico   = '';
var valorAntStorage      = 0;
var valorAntMemoria      = 0;
var valorAntProcesador   = 0;
var cantidadProcesador   = 0;
var cantidadMemoria      = 0;
var cantidadStorage      = 0;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.1 26-07-2017 - Se agrega la validación de la lista de vendedores para TNP
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.2 14-03-2019 - Se agrega campo tipo de esquema.
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.3 19-06-2020 - Se agrega campo Requiere Trabajo.
 *
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.4 04-10-2020 - Se agrega validación al eliminar producto de grid temporal de servicios.
 * 
 * @author José Candelario <jcandelario@telconet.ec> 
 * @version 1.5 19-03-2021 - Se agrega información de promociones para la empresa MD
 * 
 * @author Emmanuel Martillo <emartillo@telconet.ec>
 * @version 1.6 19-03-2021 Se agrega variable y filtro al momento de ingresar productos Karspersky
 */

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();
    
    $('button[type=submit]').attr('disabled', 'disabled');
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
        {
            clicksToEdit: 1
        });

    if( prefijoEmpresa == "MD" || prefijoEmpresa == "EN" || prefijoEmpresa == "TNP")
    {
        Ext.define('ListModelVendedor',
        {
            extend: 'Ext.data.Model',
            fields:
            [
                {name:'login',  type:'string'},
                {name:'nombre', type:'string'}
            ]
        });

        storeVendedores = Ext.create('Ext.data.Store',
        {
            model: 'ListModelVendedor',
            pageSize: 200,
            autoLoad: false,
            proxy:
            {
                type: 'ajax',
                url: url_vendedores,
                reader:
                {
                    type: 'json',
                    root: 'registros'
                }
            }
        });
        
        var combo_vendedores = new Ext.form.ComboBox
        ({
            xtype: 'combobox',
            store: storeVendedores,
            labelAlign: 'left',
            name: 'idvendedor',
            id: 'idvendedor',
            valueField: 'login',
            displayField: 'nombre',
            fieldLabel: '',
            width: 290,
            allowBlank: false,
            emptyText: 'Seleccione Vendedor',
            disabled: false,
            renderTo: 'combo_vendedor',
            listeners:
            {
                select:
                {
                    fn: function(combo, value)
                    {
                        $('#infopuntoextratype_loginVendedor').val(combo.getValue());
                        $('#infopuntoextratype_nombreVendedor').val(combo.getRawValue());
                        ocultarDiv('div_errorvendedor');
                    }
                },
                click:
                {
                    element: 'el',
                    fn: function()
                    {
                        storeVendedores.load();
                    }
                }
            }
        });

        if (typeof loginEmpleado !== typeof undefined)
        {
            combo_vendedores.setValue(loginEmpleado);
            $('#infopuntoextratype_loginVendedor').val(loginEmpleado);
        }

        if (typeof nombreEmpleado !== typeof undefined)
        {
            combo_vendedores.setRawValue(nombreEmpleado);
            $('#infopuntoextratype_nombreVendedor').val(nombreEmpleado);
        }
    }//( prefijoEmpresa == "MD" )
        
    
    Ext.define('ListadoDetalleOrden',
        {
            extend: 'Ext.data.Model',
            fields:
            [
                {name: 'codigo',                     type: 'string'},
                {name: 'producto',                   type: 'string'},
                {name: 'cantidad',                   type: 'string'},
                {name: 'frecuencia',                 type: 'string'},
                {name: 'precio',                     type: 'string'},
                {name: 'precio_total',               type: 'string'},
                {name: 'info',                       type: 'char'},
                {name: 'hijo',                       type: 'boolean'},
                {name: 'caracteristicasProducto',    type: 'string'},
                {name: 'caractCodigoPromoIns',       type: 'string'},
                {name: 'nombrePromoIns',             type: 'string'},
                {name: 'idTipoPromoIns',             type: 'string'},
                {name: 'caractCodigoPromo',          type: 'string'},
                {name: 'nombrePromo',                type: 'string'},
                {name: 'idTipoPromo',                type: 'string'},
                {name: 'caractCodigoPromoBw',        type: 'string'},
                {name: 'nombrePromoBw',              type: 'string'},
                {name: 'idTipoPromoBw',              type: 'string'},
                {name: 'strServiciosMix',            type: 'string'},
                {name: 'servicio',                   type: 'int'},
                {name: 'tipoMedio',                  type: 'string'},
                {name: 'backupDesc',                 type: 'string'},
                {name: 'fecha',                      type: 'string'},
                {name: 'precio_venta',               type: 'string'},
                {name: 'precio_instalacion',         type: 'string'},
                {name: 'descripcion_producto',       type: 'string'},
                {name: 'precio_instalacion_pactado', type: 'string'},
                {name: 'ultimaMilla',                type: 'string'},
                {name: 'um_desc',                    type: 'string'},
                {name: 'login_vendedor',             type: 'string'},
                {name: 'nombre_vendedor',            type: 'string'},
                {name: 'strPlantillaComisionista',   type: 'string'},
                {name: 'cotizacion',                 type: 'string'},
                {name: 'cot_desc',                   type: 'string'},
                {name: 'intIdPropuesta',             type: 'string'},
                {name: 'strPropuesta',               type: 'string'},
                {name: 'intIdMotivoInstalacion',     type: 'int'}
            ]
        });

    var fieldBackupDesc  = [];
    var fieldTipoMedio   = [];
    var fieldFecha       = [];
    var fieldUltimaMilla = [];

    dataStoreServicios = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'ListadoDetalleOrden',
            proxy:
            {
                type: 'memory',
                reader:
                {
                    type: 'json',
                    root: 'personaFormasContacto',
                    totalProperty: 'total'
                }
            }
        });

    gridServicios = Ext.create('Ext.grid.Panel',
        {
            id: 'gridServicios',
            store: dataStoreServicios,
            renderTo: 'lista_informacion_pre_cargada',
            width: 800,
            height: 200,
            title: 'Listado de servicios',
            frame: true,
            viewConfig:
                {
                    getRowClass: function(record, index)
                    {
                        if (record.get('hijo'))
                        {
                            return 'hijo';
                        }
                    }
                },
            selModel:
                {
                    selType: 'cellmodel'
                },
            plugins: [cellEditing],
            columns:
                [
                    new Ext.grid.RowNumberer(),
                    {
                        text: 'Codigo',
                        width: 50,
                        dataIndex: 'codigo',
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'Producto/Plan',
                        width: 215,
                        dataIndex: 'producto',
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'Cantidad',
                        dataIndex: 'cantidad',
                        align: 'right',
                        width: 60,
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'Frecuencia',
                        dataIndex: 'frecuencia',
                        align: 'right',
                        width: 65,
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'Precio Unit.',
                        width: 70,
                        dataIndex: 'precio',
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'Precio Total',
                        width: 70,
                        dataIndex: 'precio_total',
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'Vendedor',
                        width: 130,
                        dataIndex: 'nombre_vendedor',
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'caracteristicasProducto',
                        width: 130,
                        dataIndex: 'caracteristicasProducto',
                        tdCls: 'x-change-cell',
                        hidden: true
                    },
                    {
                        text: 'PROM_INS',
                        width: 100,
                        dataIndex: 'caractCodigoPromoIns',
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'nombrePromoIns',
                        width: 130,
                        dataIndex: 'nombrePromoIns',
                        tdCls: 'x-change-cell',
                        hidden: true
                    },
                    {
                        text: 'idTipoPromoIns',
                        width: 130,
                        dataIndex: 'idTipoPromoIns',
                        tdCls: 'x-change-cell',
                        hidden: true
                    },
                    {
                        text: 'PROM_MENS',
                        width: 100,
                        dataIndex: 'caractCodigoPromo',
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'nombrePromo',
                        width: 130,
                        dataIndex: 'nombrePromo',
                        tdCls: 'x-change-cell',
                        hidden: true
                    },
                    {
                        text: 'idTipoPromo',
                        width: 130,
                        dataIndex: 'idTipoPromo',
                        tdCls: 'x-change-cell',
                        hidden: true
                    },
                    {
                        text: 'PROM_BW',
                        width: 100,
                        dataIndex: 'caractCodigoPromoBw',
                        tdCls: 'x-change-cell'
                    },
                    {
                        text: 'nombrePromoBw',
                        width: 130,
                        dataIndex: 'nombrePromoBw',
                        tdCls: 'x-change-cell',
                        hidden: true
                    },
                    {
                        text: 'idTipoPromoBw',
                        width: 130,
                        dataIndex: 'idTipoPromoBw',
                        tdCls: 'x-change-cell',
                        hidden: true
                    },
                    {
                        text: 'strServiciosMix',
                        width: 130,
                        dataIndex: 'strServiciosMix',
                        tdCls: 'x-change-cell',
                        hidden: true
                    },
                    {
                        header: 'Acciones',
                        xtype: 'actioncolumn',
                        width: 100,
                        sortable: true,
                        renderer: function(val, metadata, record)
                        {
                            if (record.get('info') == 'C')
                            {
                                this.items[1].iconCls = 'button-grid-agregarCaracteristica';
                            }
                            else
                            {
                                if (record.get('hijo'))
                                {
                                    this.items[1].iconCls = 'button-grid-agregarCaracteristica';
                                }
                                else
                                {
                                    this.items[1].iconCls = '';
                                }
                            }
                            metadata.style = 'cursor: pointer;';
                            return val;
                        },
                        items:
                            [
                                {
                                    tooltip: 'Eliminar',
                                    getClass: function()
                                    {
                                        return 'button-grid-delete';
                                    },
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        var idsProductosGrid    = [];
                                        var dataXServicio       = [];
                                        for (var i = 0; i < dataStoreServicios.getCount(); i++)
                                        {
                                            dataXServicio = dataStoreServicios.getAt(i).data;
                                            idsProductosGrid.push(dataXServicio['codigo']);
                                            console.log("****codigo***"+dataXServicio['codigo']);
                                        }

                                        Ext.Ajax.request({
                                            url: strUrlValidaEliminacionServicio,
                                            method: 'post',
                                            async: false,
                                            timeout: 400000,
                                            params: {
                                                intIdProductoAEliminar: grid.getStore().getAt(rowIndex).data.codigo,
                                                strIdsProductosGrid: idsProductosGrid.join()
                                            },
                                            success: function(response) 
                                            {
                                                var objData    = Ext.JSON.decode(response.responseText);
                                                var strStatus  = objData.status;
                                                var strMensaje = objData.mensaje;
                                                var strTipoNegocio = objData.tipoNegocio;
                                                var strExisteIpWan = objData.existeIpWan;
                                                if (strStatus == "OK")
                                                {
                                                    $('#mensaje_validaciones').addClass('campo-oculto').html("");
                                                    var rec = grid.getStore().getAt(rowIndex);
                                                    if (rec.get('frecuencia') != '-' && formulario.cantidad_total_ingresada.value > 0)
                                                    {
                                                        total = Number(formulario.cantidad_total_ingresada.value) - Number(rec.get('cantidad'));

                                                        formulario.cantidad_total_ingresada.value = total;
                                                    }
                                                    dataStoreServicios.removeAt(rowIndex);
                                                    if (dataStoreServicios.data.length <= 0)
                                                    {
                                                        document.getElementById("infoAdicionalProductos").value = "";
                                                        $('#mensaje_validaciones').removeClass('campo-oculto').html("Ingrese servicios");
                                                        $('button[type=submit]').attr('disabled', 'disabled');
                                                    }
                                                    if (strTipoNegocio === "PYME")
                                                    {
                                                        if (strExisteIpWan === "SI")
                                                        {
                                                            formulario.existe_ip_wan.value = "NO";
                                                            booleanAgregarIpWanPyme        = false;
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    $('#mensaje_validaciones').removeClass('campo-oculto').html(strMensaje);
                                                }
                                            },
                                            failure: function()
                                            {
                                                $('#mensaje_validaciones').removeClass('campo-oculto')
                                                    .html("Se presentaron errores al validar los servicio, favor notificar a Sistemas.");
                                            }
                                        });
                                    }
                                },
                                {
                                    tooltip: 'Agregar Características',
                                    getClass: function()
                                    {
                                        return 'button-grid-agregarCaracteristica';
                                    },
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        agregarCaracteristica(grid.getStore().getAt(rowIndex).data);
                                    }
                                },
                                {
                                    tooltip: 'Servicio Principal',
                                    getClass: function(a, b, c)
                                    {
                                        if(c.data.servicio > 0)
                                        {
                                            return 'button-grid-servicioPrincipal';
                                        }
                                        return '';
                                    },
                                    handler: function(grid, rowIndex)
                                    {
                                        mostrarServicioPrincipal(grid.getStore().getAt(rowIndex).data);
                                    }
                                }
                            ]
                    },
                    {
                        dataIndex: 'servicio',
                        hidden: true
                    }
                ]
        });

    if (prefijoEmpresa === strEmpresaPermitida || prefijoEmpresa === 'TNP')
    {
        fieldUltimaMilla =
        {
            header: 'Última Milla',
            dataIndex: 'um_desc',
            width: 75
        };
        
        fieldBackupDesc =
        {
            text: 'Backup De',
            width: 65,
            dataIndex: 'backupDesc',
            tdCls: 'x-change-cell',
            hidden: true
        };

        fieldTipoMedio =
        {
            text: 'UM',
            width: 45,
            dataIndex: 'tipoMedio',
            tdCls: 'x-change-cell',
            hidden: true
        };

        fieldFecha =
        {
            text: 'Fecha',
            width: 90,
            dataIndex: 'fecha',
            tdCls: 'x-change-cell',
            hidden: true
        };
            
        fieldPrecioInstalacion =
        {
            text: 'Precio<br>Instalacion',
            width: 80,
            dataIndex: 'precio_instalacion',
            tdCls: 'x-change-cell'
        }; 
            
        fieldPrecioInstalacionPactado =
        {
           text: 'Precio Instalacion<br>Negociado',
           width: 100,
           dataIndex: 'precio_instalacion_pactado',
           tdCls: 'x-change-cell'
        }; 
            
        fieldPrecioNegociacion =
        {
            text: 'Precio Venta<br>Negociado',
            width: 80,
            dataIndex: 'precio_venta',
            tdCls: 'x-change-cell'
        };             
            
        gridServicios.width = gridServicios.width + 300;
        
        gridServicios.headerCt.insert(2, fieldUltimaMilla);
        gridServicios.headerCt.insert(8, fieldBackupDesc);
        gridServicios.headerCt.insert(9, fieldTipoMedio);
        gridServicios.headerCt.insert(10, fieldFecha);
        gridServicios.headerCt.insert(11, fieldPrecioInstalacion);
        gridServicios.headerCt.insert(12, fieldPrecioInstalacionPactado);
        gridServicios.headerCt.insert(13, fieldPrecioNegociacion);
        
        gridServicios.getView().refresh();
    }
    if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
    {
        getTipoNegocio();
    }
});

var servicioPrincipal = null;
var strTipoEnlace = 'PRINCIPAL';
  
function verServiciosPrincipales(objProducto)
{
    var seleccionados = [];
    
    for (var i = 0; i < dataStoreServicios.getCount(); i++)
    {
        data = dataStoreServicios.getAt(i).data;
        seleccionados.push(data['servicio']);
    }
    
    var objRecord;
    dataStoreServiciosPrincipales = new Ext.data.Store(
        {
            autoLoad: true,
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlRequiereBackup,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'registros'
                        },
                    extraParams:
                        {
                            puntoId: idPunto,
                            codigo: objProducto,
                            excluidos: seleccionados.join(),
                            query: 'true'
                        }
                },
            fields:
                [
                    {name: 'servicio', type: 'int'},
                    {name: 'codigo', type: 'int'},
                    {name: 'tipoMedio', type: 'string'},
                    {name: 'producto', type: 'string'},
                    {name: 'fecha', type: 'string'},
                    {name: 'estado', type: 'string'}
                ]
        });

    sm = Ext.create('Ext.selection.CheckboxModel',
        {
            checkOnly: false,
            showHeaderCheckbox: false,
            mode: 'SINGLE',
            listeners:
                {
                    select: function(model, record, index)
                    {
                        objRecord = record;
                    }
                }
        });

    gridServiciosPrincipales = Ext.create('Ext.grid.Panel',
        {
            id: 'gridServiciosPrincipales',
            store: dataStoreServiciosPrincipales,
            selModel: sm,
            width: 550,
            height: 400,
            collapsible: false,
            multiSelect: true,
            viewConfig:
                {
                    emptyText: '<br><center><b>No hay datos para mostrar',
                    forceFit: true
                },
            layout: 'fit',
            region: 'center',
            buttons:
                [
                    {
                        text: 'Seleccionar',
                        handler: function()
                        {
                            if (objRecord != null && objRecord.data != null)
                            {
                                servicioPrincipal = objRecord.data;
                                var servPrincAso  = '<table><tr><td width="230"><label>Servicio Principal: </label></td><td>' +
                                                    '<table style="overflow: auto">'+
                                                    '<tr><td width="95px">Producto/Plan<td/><td>:<td/><td>' + servicioPrincipal.producto + 
                                                    '<td/><tr/>'+
                                                    '<tr><td>Última Milla<td/><td width="10px">:<td/><td>' + servicioPrincipal.tipoMedio + 
                                                    '<td/><tr/>'+
                                                    '<tr><td>Fecha Creación<td/><td>:<td/><td>' + servicioPrincipal.fecha + 
                                                    '<td/><tr/>'+
                                                    '</table><br/></td><tr></table>';
                                                
                                $("#servicioPrincipal" ).empty();
                                $("#servicioPrincipal" ).append(servPrincAso);

                                win.destroy();
                            }
                            else
                            {
                                Ext.Msg.show(
                                    {
                                        title: 'Error',
                                        msg: 'No ha seleccionado un servicio principal.',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.ERROR
                                    }
                                );
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            servicioPrincipal = null;
                            strTipoEnlace     = 'PRINCIPAL';
                            win.destroy();
                        }
                    }
                ],
            columns:
                [
                    new Ext.grid.RowNumberer(),
                    {
                        dataIndex: 'servicio',
                        hidden: true
                    },
                    {
                        header: 'C\xf3digo',
                        dataIndex: 'codigo',
                        width: 80
                    },
                    {
                        header: 'Producto/Plan',
                        dataIndex: 'producto',
                        width: 250
                    },
                    {
                        header: 'Última Milla',
                        dataIndex: 'tipoMedio',
                        width: 130
                    },
                    {
                        header: 'Fecha Creaci\xf3n',
                        dataIndex: 'fecha',
                        width: 160
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 100
                    }
                ]
        });

    Ext.create('Ext.form.Panel',
        {
            id: 'formServiciosPrincipales',
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 125,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 700
                            },
                        layout:
                            {
                                type: 'table',
                                columns: 4,
                                align: 'left'
                            },
                        items:
                            [
                                gridServiciosPrincipales
                            ]
                    }
                ]
        });

    win = Ext.create('Ext.window.Window',
        {
            title: 'Seleccionar Servicio Principal',
            modal: true,
            width: 800,
            closable: true,
            layout: 'fit',
            items: [gridServiciosPrincipales]
        }).show();
}

function validarTipoEnlace(tipoEnlace)
{
    strTipoEnlace = tipoEnlace;
    btnBackup     = document.getElementById("backup");
    
    if(tipoEnlace == 'BACKUP')
    {
        btnBackup.style.visibility = "visible";
    }
    else
    {
        btnBackup.style.visibility = "hidden";
        servicioPrincipal          = null;
        strTipoEnlace              = 'PRINCIPAL';
        $("#servicioPrincipal" ).empty();
    }
}
    /**
     * Documentación para la función 'validaInformacion'.
     *
     * Función que muestra mensaje de aprobación para crear una solicitud de aprobación en caso de que el servicio sea tipo de red GPOM.
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 10-10-2019
     */
    function validaInformacion()
    {
        var strMensaje    = 'Estimado usuario, se va a crear una Solicitud de Aprobación para servicio con tipo red MPLS.<br>'
                            +'¿Desea continuar?';
        Ext.Msg.confirm('Alerta',strMensaje, function(btn){
            if(btn=='yes')
            {
                agregar_detalle();
            }
        });
    }
function enviarInformacion()
{
    $('button[type=submit]').attr('disabled', 'disabled');

    var array_data_caract ={};
    var j = 0;
    var informacion = [];

    for (var i = 0; i < gridServicios.getStore().getCount(); i++)
    {
        variable = gridServicios.getStore().getAt(i).data;
        
        for (var key in variable)
        {
            var valor = variable[key];
            
            array_data_caract[key] = valor;
        }

        informacion.push(array_data_caract);

        array_data_caract = {};
        j                 = 0;
    }

    if (informacion.length > 0)
    {

        if(prefijoEmpresa != 'MD' && prefijoEmpresa != 'EN')
        {
           

             Ext.MessageBox.wait("Guardando servicio(s)...");
            document.getElementById("valores").value = JSON.stringify(informacion);        
            document.forms[0].submit();
            $('#mensaje_validaciones').addClass('campo-oculto').html(""); 
        }
        else{

            if(!validar_estado_punto())
            {

                $('button[type=submit]').removeAttr('disabled');
                return false;
            }
                Ext.MessageBox.wait("Guardando servicio(s)...");
                document.getElementById("valores").value = JSON.stringify(informacion);        
                document.forms[0].submit();
                $('#mensaje_validaciones').addClass('campo-oculto').html("");
             
        }
       
    }
    else
    {
        $('#mensaje_validaciones').removeClass('campo-oculto').html("Ingrese servicios");
        $('button[type=submit]').attr('disabled', 'disabled');

        return false;

    }
}

function validar_ultima_milla()
{
    // Si es producto(catálogo) se valida la última milla del producto "ultimaMillaIdProd", caso contrario "ultimaMillaId".
    var tipo = $("input:radio[@name='info']:checked").val();

    if (tipo == "catalogo")
    {
        ultimaMilla = document.getElementById("ultimaMillaIdProd").value;
    }
    else
    {
        ultimaMilla = document.getElementById("ultimaMillaId").value;
    }
    
	if (ultimaMilla == '' || ultimaMilla > 0)
	{
		$('#mensaje_validaciones').addClass('campo-oculto').html("");
	}
	else
	{
		$('#mensaje_validaciones').removeClass('campo-oculto').html("Ingrese Última Milla");
		$('button[type=submit]').attr('disabled', 'disabled');
	}
}
function validarProductoAdicional()
{
    var cantidad_detalle = 0;
    var tipo             = $("input:radio[@name='info']:checked").val();
    var plan             = Number(document.getElementById("planes").value.split("-")[0]);
    var producto         = Number(document.getElementById("producto").value.split("-")[0]);
    
    if( tipo == "portafolio" )
    {
         cantidad_detalle = Number(document.getElementById("cantidad_plan").value);
    }
    else
    {
         cantidad_detalle = Number(document.getElementById("cantidad").value);
    }
    
    var cantidad_total_ingresada = Number(document.getElementById("cantidad_total_ingresada").value);    
     $.ajax({
        type: "POST",
        data: "tipo=" + tipo + "&planId=" + plan + "&productoId=" + producto + "&cantidad_detalle=" + cantidad_detalle + "&cantidad_total_ingresada=" + cantidad_total_ingresada,
        url: valida_producto_adicional,
        beforeSend: function()
        {
            Ext.get('gridServicios').mask('Cargando Datos del Servicio');
        },
        success: function(msg)
        {
            if (msg.msg != 'Ok')
            {
                $('#mensaje_validaciones').removeClass('campo-oculto').html(msg.msg);
                Ext.get('gridServicios').unmask();
            }
            else
            {   
                validarIps();
            }
        },
        failure: function()
        {
            Ext.get('gridServicios').unmask();
        }
    }); 
      
}

/**
 * @version 1.0
 * 
 * @author Alex Gómez <algomez@telconet.ec>
 * @version 1.1 19-07-2022 - Se valida el estado del punto previo a la agregación 
 *                           de servicios
 * @author Carlos Caguana <ccaguana@telconet.ec>
 * @version 1.2 25-01-2023 - Se valida que el envio del nombre del producto
 */
 function validar_estado_punto()
 {
    const nombreProducto  = document.getElementById("producto").value.split("-")[1];
    
    if(nombreProducto==null || nombreProducto== 'undefined'){
 
        return true;
    }
 
 
    let valida_estado;
        $.ajax({
            type: "POST",
            data:{
                nombreProducto:nombreProducto
            },
            url: valida_estado_punto,
            async: false,
            timeout: 400000,
            success: function(msg)
            {
                if (msg.msg != 'ok')
                {
                    $('#mensaje_validaciones').removeClass('campo-oculto').html("" + msg.mensaje_validaciones + "");
                    Ext.get('gridServicios').unmask();
                    valida_estado = false;
                }
                else
                {
                    valida_estado = true;
                }
            },
            failure: function()
            {
                valida_estado = true;
            }
        });
    
    return valida_estado;
 }

/**
 * @version 1.0
 * 
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.1 04-10-2020 - Se persiste información de nuevo campo (msg.existe_ip_wan) que
 *                           retorna el servicio valida_ips_max_permitidas
 */
function validarIps()
{
    var cantidad_detalle = 0;
    var tipo             = $("input:radio[@name='info']:checked").val();
    var plan             = Number(document.getElementById("planes").value.split("-")[0]);
    var producto         = Number(document.getElementById("producto").value.split("-")[0]);
    
    if( tipo == "portafolio" )
    {
         cantidad_detalle = Number(document.getElementById("cantidad_plan").value);
    }
    else
    {
         cantidad_detalle = Number(document.getElementById("cantidad").value);
    }
    
    var cantidad_total_ingresada = Number(document.getElementById("cantidad_total_ingresada").value);
    var existe_ip_wan = document.getElementById("existe_ip_wan").value;   
     //Numero de ips Utilizadas  ,Numero maximo de ips permitidas      
     $.ajax({
        type: "POST",
        data: "tipo=" + tipo + "&planId=" + plan + "&productoId=" + producto + "&cantidad_detalle=" + cantidad_detalle + "&cantidad_total_ingresada=" + cantidad_total_ingresada + "&existe_ip_wan=" + existe_ip_wan,
        url: valida_ips_max_permitidas,
        beforeSend: function()
        {
            Ext.get('gridServicios').mask('Cargando Datos del Servicio');
        },
        success: function(msg)
        {
            if (msg.msg == 'ok')
            {
                document.getElementById('mensaje_validaciones').style.display = 'block';
                $('#mensaje_validaciones').removeClass('campo-oculto').html(msg.mensaje_validaciones);
                formulario.num_ips_utilizadas.value = msg.num_ips_utilizadas;
                formulario.num_ips_max_permitidas.value = msg.num_ips_max_permitidas;
                formulario.prod_ip.value = msg.prod_ip;
                formulario.existe_ip_wan.value = msg.existe_ip_wan;
                Ext.get('gridServicios').unmask();
            }
            else
            {
                if (msg.msg == '')
                {
                    formulario.existe_ip_wan.value = msg.existe_ip_wan;
                    formulario.num_ips_utilizadas.value = msg.num_ips_utilizadas;
                    formulario.num_ips_max_permitidas.value = msg.num_ips_max_permitidas;
                    formulario.prod_ip.value = msg.prod_ip;
                    if (tipo == "portafolio")
                    {
                        validar_frecuencia('SiIp');
                    }
                    else
                    {
                        formulario.cantidad_total_ingresada.value = Number(formulario.cantidad_total_ingresada.value) + Number(cantidad_detalle);
                        cargar_detalle_catalogo();
                    }
                }
                else if (msg.msg == 'NoIP')
                {
                    formulario.num_ips_utilizadas.value = "";
                    formulario.num_ips_max_permitidas.value = "";
                    formulario.prod_ip.value = "";
                    if (tipo == "portafolio")
                    {
                        validar_frecuencia(msg.msg);
                    }
                    else
                    {
                        validarProductoNetlifeCam();
                    }
                }
            }
        },
        failure: function()
        {
            Ext.get('gridServicios').unmask();
        }
    }); 
      
}

function validarProductoNetlifeCam()
{
    //Validar que posea un servicio de internet valido para poder contratar NetlifeCam 
    //Se excluyen los servicios en estado Rechazado, Rechazada, Cancelado, Anulado, Cancel, Eliminado, Reubicado, Trasladado, Incorte, InTemp
    var producto = Number(document.getElementById("producto").value.split("-")[0]);
    
    $.ajax({
        type: "POST",
        data: "productoId=" + producto,
        url: valida_producto_netlifecam,
        success: function(msg)
        {
            if (msg.msg == 'ok')
            {
                if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
                {
                    document.getElementById('idFrecuenciaPro').style = "display:none;";
                }
                $('#mensaje_validaciones').removeClass('campo-oculto').html("" + msg.mensaje_validaciones + "");
                Ext.get('gridServicios').unmask();
            }
            else
            {
                if (msg.msg == '')
                {
                    cargar_detalle_catalogo();
                }
                else
                {
                    Ext.get('gridServicios').unmask();
                }
            }
        },
        failure: function()
        {
            Ext.get('gridServicios').unmask();
        }
    });
}

function validar_detalle_portafolio()
{       
	var plan        = Number(document.getElementById("planes").value.split("-")[0]);
	var cantidad    = Number(document.getElementById("cantidad_plan").value);
	var valorOrig   = Number(document.getElementById("precio_h").value);
	var valorAct    = Number(document.getElementById("precio").value);
    var requiere_um = document.getElementById("requiere_ultimaMilla_plan").value;
    var ultimaMilla = document.getElementById("ultimaMillaId").value;
   
    if (isNaN(plan) || plan <= 0)
	{
		$('#mensaje_validaciones').removeClass('campo-oculto').html("Seleccione un plan");
		return false;
	}
    else if(requiere_um == 'SI' && (ultimaMilla == '' || parseInt(ultimaMilla) <= 0))
    {
        $('#mensaje_validaciones').removeClass('campo-oculto').html("Ingrese Última Milla");
        return false;
    }           
	else if (isNaN(cantidad) || cantidad <= 0)
	{
		$('#mensaje_validaciones').removeClass('campo-oculto').html("Cantidad debe ser un numero mayor que cero");
		return false;
	}
	else if (isNaN(valorAct) || valorAct < 0)
	{
		$('#mensaje_validaciones').removeClass('campo-oculto').html("Valor debe ser mayor o igual que cero");
		return false;
	}
	else if (valorOrig>valorAct)
	{
		$('#mensaje_validaciones').removeClass('campo-oculto').html("Valor actual ["+valorAct+"] menor al valor original ["+valorOrig+"]");
		return false;
	}	
    else if( formulario.idvendedor.value=='' && (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" ) )
    {
        mostrarDiv('div_errorvendedor');
        return false;
    }
    else if(document.getElementById("validacionGuardarPlan").value)
    {
        if(document.getElementById("validacionGuardarPlan").value==="ERROR")
        {
            $('#mensaje_validaciones').removeClass('campo-oculto').html(document.getElementById("infoValidaGuardarPlan").innerHTML);
            // DOUGLAS NATHA ARIAS - 26/12/2019 - Se muestra una alerta de confirmación que permita realizar el cambio de tipo de negocio
            // en caso que el tipo de negocio del servicio no corresponda al tipo de negocio del punto para MEGADATOS.

            if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
            {
                Ext.Msg.confirm('Alerta', 'El tipo de negocio del punto no corresponde con el tipo de negocio del plan.'
                +' <br>¿Desea cambiar el tipo de negocio del punto?', function(btn)
                {
                    if (btn === 'yes')
                    {
                        getCambioNegocio();
                    }
                });
            }
            return false;
        }
        else
        {
            return true;
        }
    }
	else{
		$('#mensaje_validaciones').addClass('campo-oculto').html("");
		return true;
	}
}

function validar_frecuencia(es_ip)
{
    //Validar Frecuencia
    var tipo     = $("input:radio[@name='info']:checked").val();
    var plan     = Number(document.getElementById("planes").value.split("-")[0]);
    var producto = Number(document.getElementById("producto").value.split("-")[0]);
    
    if (tipo == "portafolio")
    {
        cantidad_detalle = Number(document.getElementById("cantidad_plan").value);
    }
    else
    {
        cantidad_detalle = Number(document.getElementById("cantidad").value);
    }

    $.ajax({
        type: "POST",
        data: "tipo=" + tipo + "&planId=" + plan + "&productoId=" + producto,
        url: valida_frecuencia,
        success: function(msg)
        {
            if (msg.msg == 'ok')
            {   
                if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
                {
                    document.getElementById('idFrecuenciaPro').style = "display:none;";
                }
                
                $('#mensaje_validaciones').removeClass('campo-oculto').html(msg.mensaje_validaciones);
                Ext.get('gridServicios').unmask();
            }
            else
            {
                if (msg.msg == '')
                {
                    formulario.frecuencia_plan.value = Number(msg.frecuencia_plan);
                    if (es_ip == "SiIp")
                    {
                        formulario.cantidad_total_ingresada.value = Number(formulario.cantidad_total_ingresada.value) + Number(cantidad_detalle);
                    }
                    cargar_detalle_portafolio();
                }
                else
                {
                    Ext.get('gridServicios').unmask();
                }
            }
        },
        failure: function()
        {
            Ext.get('gridServicios').unmask();
        }
    });
    //fin de valida Frecuencia
}

/**
 * Se realizan modificaciones para aquellos planes que tengan la característica REGISTRO_UNICO, puesto que de acuerdo
 * a la cantidad ingresada, se generarán las solicitudes y órdenes de trabajo
 * @author José Candelario <jcandelario@telconet.ec> 
 * @version 1.1 19-03-2021 - Se agrega información de promociones para la empresa MD
 */
function cargar_detalle_portafolio()
{
    //Modificaciones al agregar detalle del portafolio
    //Debe listar todos los productos del plan para asi realizar el ingreso de las caracteristicas del mismo
    //Obtener informacion del formulario		
    validar_ultima_milla();
    var info_producto   = formulario.planes.value;
    var producto        = info_producto.split("-");
    var cantidad        = formulario.cantidad_plan.value;

    var strRegistroUnitarioPlan = "NO";
    var cantidadServiciosPlan   = 1;

    if (document.getElementById("strRegistroUnitarioPlan"))
    {
        strRegistroUnitarioPlan = document.getElementById("strRegistroUnitarioPlan").value;
    }

    if (strRegistroUnitarioPlan === "SI")
    {
        cantidadServiciosPlan = cantidad;
        cantidad = 1;
    }
    var precio_unitario     = 0;
    var precio_total        = 0;
    precio_unitario         = formulario.precio.value;
    precio_total            = (precio_unitario * cantidad);
    var precio_instalacion  = 0;
    var precio_venta        = 0;
    var descripcionProducto = '';
    var login_vendedor      = $('#infopuntoextratype_loginVendedor').val();
    var nombre_vendedor     = $('#infopuntoextratype_nombreVendedor').val();
    var strCodigoPromoIns   = "";
    var strNombrePromoIns   = "";
    var strIdTipoPromoIns   = "";
    var strCodigoPromoBw    = "";
    var strNombrePromoBw    = "";
    var strIdTipoPromoBw    = "";
    var strCodigoPromo      = "";
    var strNombrePromo      = "";
    var strIdTipoPromo      = "";
    var strPromoMix         = "";
    if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
    {
        strCodigoPromoIns  = strCodigoPromocionIns;
        strNombrePromoIns  = strNombrePromocionIns;
        strIdTipoPromoIns  = strTipoPromocionIns;
        strCodigoPromoBw   = strCodigoPromocionBw;
        strNombrePromoBw   = strNombrePromocionBw;
        strIdTipoPromoBw   = strTipoPromocionBw;
        strCodigoPromo     = strCodigoPromocion;
        strNombrePromo     = strNombrePromocion;
        strIdTipoPromo     = strTipoPromocion;
        strPromoMix        = strServiciosMix;
    }

    if (prefijoEmpresa === 'TNP')
    {
        tipo = $("input:radio[@name='info']:checked").val();
    }
    
    if (prefijoEmpresa === 'TN' || (prefijoEmpresa === 'TNP' && tipo == "catalogo"))
    {
        precio_instalacion  = formulario.precio_instalacion.value;
        precio_venta        = formulario.precio_venta.value;
        descripcionProducto = formulario.descripcion_producto.value;
    }
    else // MD
    {
        descripcionProducto = producto[1];
    }

    var frecuencia = formulario.frecuencia_plan.value;
    
    var registroPlan =
        {
            'codigo': producto[0],
            'producto': descripcionProducto,
            'precio': precio_unitario,
            'precio_total': precio_total,
            'cantidad': cantidad,
            'frecuencia': frecuencia,
            'caractCodigoPromoIns': strCodigoPromoIns,
            'nombrePromoIns': strNombrePromoIns,
            'idTipoPromoIns': strIdTipoPromoIns,
            'caractCodigoPromo': strCodigoPromo,
            'nombrePromo': strNombrePromo,
            'idTipoPromo': strIdTipoPromo,
            'caractCodigoPromoBw': strCodigoPromoBw,
            'nombrePromoBw': strNombrePromoBw,
            'idTipoPromoBw': strIdTipoPromoBw,
            'strServiciosMix'  : strPromoMix,
            'info': "P",
            'hijo': false,
            'backupDe': '',
            'caracteristicasProducto': '',
            'precio_instalacion': precio_instalacion,
            'precio_venta': precio_venta,
            'ultimaMilla': document.getElementById("ultimaMillaId").value,
            'login_vendedor': login_vendedor,
            'nombre_vendedor': nombre_vendedor
        };
    //Para Panama muestro la UM del Plan agregado
    if (prefijoEmpresa === 'TNP')
    {
        var um_plan = document.getElementById("ultimaMillaId");
        // Se define el valor nombre y código de la última milla.
        if (um_plan && um_plan.value == '')
        {
            ultimaMilla = '';
            UM_Descrip = 'Ninguna';
        } else
        {
            ultimaMilla = um_plan.value;
            UM_Descrip = um_plan.options[um_plan.selectedIndex].text;
        }
        registroPlan.um_desc = UM_Descrip;
        registroPlan.ultimaMilla = ultimaMilla;
    }
    
    if (prefijoEmpresa == strEmpresaPermitida && servicioPrincipal != null)
    {
        registroPlan.servicio = servicioPrincipal.servicio;
        registroPlan.tipoMedio = servicioPrincipal.tipoMedio;
        registroPlan.backupDesc = servicioPrincipal.producto;
        registroPlan.fecha = servicioPrincipal.fecha;
        servicioPrincipal = null;
        strTipoEnlace = 'PRINCIPAL';
    }
    if (prefijoEmpresa === strEmpresaPermitida /*|| prefijoEmpresa === 'TNP'*/)
    {
        registroPlan.precio_venta = precio_venta;
        registroPlan.precio_instalacion = precio_instalacion;
        registroPlan.descripcion_producto = descripcionProducto;
    }
    var boolErrorCaractsPlanProd = false;
    var arrayRegistrosHijos = [];
    //Obtengo el detalle del plan
    /* @author José Candelario <jcandelario@telconet.ec> 
     * @version 1.0 19-03-2021 - Se agrega información de promociones para la empresa MD.
    */
    $.ajax({
        type: "POST",
        data: "planId=" + producto[0],
        url: detalle_plan,
        success: function(msg)
        {
            if (msg.msg == 'ok')
            {
                var info = JSON.stringify(msg.listado);
                var myArray = JSON.parse(info);

                for (var i = 0; i < myArray.length; i++)
                {
                    var object = myArray[i];
                    var str_valida_caracts_plan_prod = validaCaractsPlanProd(object.productoid, object.nombreProducto);
                    var arrayValidaCaractsPlanProd = JSON.parse(str_valida_caracts_plan_prod);
                    if(arrayValidaCaractsPlanProd["continuaFlujo"] === "NO")
                    {
                        boolErrorCaractsPlanProd = true;
                        break;
                    }
                    var registro =
                        {
                            'codigo': object.productoid,
                            'producto': object.nombreProducto,
                            'precio': '-',
                            'precio_total': '-',
                            'cantidad': object.cantidad,
                            'frecuencia': '-',
                            'info': 'P',
                            'hijo': true,
                            'caracteristicasProducto': arrayValidaCaractsPlanProd["json_caracts_plan_prod"],
                            'precio_instalacion': precio_instalacion,
                            'precio_venta': precio_venta
                        };
                      
                    if (prefijoEmpresa == strEmpresaPermitida && servicioPrincipal != null)
                    {
                        registro.servicio = servicioPrincipal.servicio;
                        registro.tipoMedio = servicioPrincipal.tipoMedio;
                        registro.backupDesc = servicioPrincipal.producto;
                        registro.fecha = servicioPrincipal.fecha;
                        servicioPrincipal = null;
                        strTipoEnlace = 'PRINCIPAL';
                    }
                    if (prefijoEmpresa == strEmpresaPermitida)
                    {
                        registro.precio_venta = precio_venta;
                        registro.precio_instalacion = precio_instalacion;
                        registro.descripcion_producto = descripcionProducto;
                    }
                    arrayRegistrosHijos.push(registro);
                }
                if(!boolErrorCaractsPlanProd)
                {
                    for (var intContServicioPlan = 0; intContServicioPlan < cantidadServiciosPlan; intContServicioPlan++)
                    {
                        var rec_plan = new ListadoDetalleOrden(registroPlan);
                        dataStoreServicios.add(rec_plan);

                        for (var intContProdXPlan = 0; intContProdXPlan < arrayRegistrosHijos.length; intContProdXPlan++)
                        {
                            var rec_hijos = new ListadoDetalleOrden(arrayRegistrosHijos[intContProdXPlan]);
                            dataStoreServicios.add(rec_hijos);
                        }
                    }
                }
            }
            else
            {
                Ext.Msg.alert("Alerta", msg.msg);
            }
            
            if(!boolErrorCaractsPlanProd)
            {
                limpiar_detalle_portafolio(true);
            }
            Ext.get('gridServicios').unmask();
            $('button[type="submit"]').removeAttr('disabled');
        },
        failure: function()
        {
            Ext.get('gridServicios').unmask();
        }
    });
    
    if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
    {
        strCodigoPromocionIns = "";
        strNombrePromocionIns = "";
        strTipoPromocionIns   = "";
        strCodigoPromocionBw  = "";
        strNombrePromocionBw  = "";
        strTipoPromocionBw    = "";
        strCodigoPromocion    = "";
        strNombrePromocion    = "";
        strTipoPromocion      = "";
        strServiciosMix       = "";
    }
}

function agregar_detalle_portafolio()
{      
    if((prefijoEmpresa === 'MD' || prefijoEmpresa === 'EN' )&& !validar_estado_punto())
    {
        return false;
    }


    
    if (validar_detalle_portafolio())
    {
        validarIps();
    }
}

/** 
 * Función que limpia el detalle de todas las características al agregar un producto
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.1 30-08-2019 Se elimina la programación document.getElementById('mensaje_validaciones').style.display 
 *                          para mostrar el error, ya que al hacerlo de esta manera, impide que otros errores se muestren.
 *                          Además se valida que existan los divs divCabPlantillaComisionista, divCabCaracteristicaProducto,
 *                          plantillaComisionistas y cantidad-servicios-wifi antes de usarlos
 * 
 * @author Leonardo Mero <lemero@telconet.ec>
 * @version 1.2 09-12-2022 - Se oculta el contenedor de archivos
 * 
 * @since 1.0
 */
function limpiar_detalle()
{
    // Se elimina el código html de todas las etiquedas <tr> con el nombre "caracts" que alojan todas las características y propiedades del producto
    var x = document.getElementsByName("caracts");
    var i;
    for (i = 0; i < x.length; i++)
    {
        x[i].innerHTML = '';
    }
    
    if (formulario.frecuencia_producto)
    {
        formulario.frecuencia_producto.value = "";  
    }
    
    if (formulario.producto)
    {
        formulario.producto.value = "Seleccione";  
    }
    
    if (formulario.tEnlace)
    {
        formulario.tEnlace.options[0].selected = true;
        document.getElementById("tEnlace").style.display  = "none";
        document.getElementById("tEnlaceL").style.display = "none";
        document.getElementById("backup").style.display   = "none";
        servicioPrincipal = null;
        strTipoEnlace     = 'PRINCIPAL';
        $("#servicioPrincipal").empty();
    }

    $('#mensaje_validaciones').addClass('campo-oculto').html("");
    
    if(document.getElementById('divCabPlantillaComisionista'))
    {
        $('#divCabPlantillaComisionista').addClass('campo-oculto');
    }
    
    if(document.getElementById('divCabCaracteristicaProducto'))
    {
        $('#divCabCaracteristicaProducto').addClass('campo-oculto');
    }
    
    if(document.getElementById('plantillaComisionistas'))
    {
        $('#plantillaComisionistas').html("");
    }
    
    if(document.getElementById('cantidad-servicios-wifi'))
    {
        document.getElementById('cantidad-servicios-wifi').remove();
    }
    $('#contenedor_archivos').hide()
}

function replaceAll( text, busca, reemplaza )
{
		while (text.toString().indexOf(busca) != -1)
		text = text.toString().replace(busca,reemplaza);
		return text;
}

/* 
 * Función realiza las validaciones a nivel de catálogo
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.1 30-08-2019 Se elimina la programación document.getElementById('mensaje_validaciones').style.display 
 *                          para mostrar el error, ya que al hacerlo de esta manera, impide que otros errores se muestren
 * @since 1.0
 */
function validar_detalle_catalogo()
{
	var producto   = Number(document.getElementById("producto").value.split("-")[0]);
	var cantidad   = Number(document.getElementById("cantidad").value);
    var frecuencia = document.getElementById("frecuencia_producto").value;
  
	if (isNaN(producto) || producto <= 0)
	{
        $('#mensaje_validaciones').removeClass('campo-oculto').html("Seleccione un producto");
		return false;
	}
    else if (frecuencia == '')
	{
		$('#mensaje_validaciones').removeClass('campo-oculto').html("Seleccione Frecuencia de Facturación");
        return false;
	}
	else if (isNaN(cantidad) || cantidad <= 0)
	{
        $('#mensaje_validaciones').removeClass('campo-oculto').html("Cantidad debe ser un numero mayor que cero");
		return false;
	}
    else if (strTipoEnlace == 'BACKUP' && servicioPrincipal == null)
    {
        strMensaje = "Ha definido como backup este servicio pero NO ha seleccionado el servicio principal.";
		$('#mensaje_validaciones').removeClass('campo-oculto').html(strMensaje);
        return false;
    }
    else if(prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
    {
        if( Ext.isEmpty(formulario.idvendedor.value) )
        {
            mostrarDiv('div_errorvendedor');
            return false;
        }
        else
        {
            $('#mensaje_validaciones').addClass('campo-oculto').html("");
            return true;
        }
    }
    else
    {
        $('#mensaje_validaciones').addClass('campo-oculto').html("");
		return true;
	}
}


function validaCaractsPlanProd(idProducto, descripcion_producto)
{
    var valor_caract        = new Array();
    var nombre_caract       = new Array();
    var prod_caract         = new Array();
    var text                = "";
    
    var continuaFlujo               = "SI";
    var json_caracts_plan_prod      = "";
    var cantidad_caracts_plan_prod  = 0;
    var tiene_correo_electronico    = "NO";
    var correo_electronico          = "";
    var nombre_div_prod_caracts     = "div_caract_plan_producto_"+idProducto;
    var sigueFlujoAnteriorAntivirus = "SI";
    var cant_caracts_requiere_trab  = 0;
    
    //Si esta visible la característica "Requiere Trabajo" se debe incluir en el json
    var caract_requiere_trabajo     = "[REQUIERE TRABAJO]"
    
    if(document.getElementById(nombre_div_prod_caracts))
    {
        var caracteristicas                 = "formulario.caracteristicas_pp_"+idProducto+"_";
        var caracteristica_nombre           = "formulario.caracteristica_nombre_pp_"+idProducto+"_";
        var producto_caracteristica         = "formulario.producto_caracteristica_pp_"+idProducto+"_";
        var caracteristicas_n               = "";
        var caracteristica_nombre_n         = "";
        var producto_caracteristica_n       = "";
        var nombreDivCantidadProd           = "cantidad_caracteristicas_pp_"+idProducto;
        var cantidad_caracteristicas        = document.getElementById(nombreDivCantidadProd).value;
        
        for (var x = 0; x < cantidad_caracteristicas; x++)
        {
            caracteristicas_n = caracteristicas + x;
            caracteristica_nombre_n = caracteristica_nombre + x;
            producto_caracteristica_n = producto_caracteristica + x;

            if (typeof eval(caracteristicas_n) !== 'undefined')
            {
                valor_caract[x] = eval(caracteristicas_n).value;
            } 
            else
            {
                valor_caract[x] = caracteristicas_n.value;
            }

            if (typeof eval(caracteristica_nombre_n) !== 'undefined')
            {
                nombre_caract[x] = eval(caracteristica_nombre_n).value;
            } 
            else
            {
                nombre_caract[x] = caracteristica_nombre_n.value;
            }
            if (typeof eval(producto_caracteristica_n) !== 'undefined')
            {
                prod_caract[x] = eval(producto_caracteristica_n).value;
            } 
            else
            {
                prod_caract[x] = producto_caracteristica_n.value;
            }

        }
        
        for (var x = 0; x < nombre_caract.length; x++)
        {
            text = replaceAll(text, nombre_caract[x], valor_caract[x]);

            if (nombre_caract[x] == "[CANTIDAD DISPOSITIVOS]" || nombre_caract[x] == "[CORREO ELECTRONICO]" || nombre_caract[x] == "[TIENE INTERNET]"
                || nombre_caract[x] == "[ANTIVIRUS]" || nombre_caract[x] == caract_requiere_trabajo)
            {
                if (nombre_caract[x] == "[CANTIDAD DISPOSITIVOS]")
                {
                    if (cantidad_caracts_plan_prod == 0) 
                    {
                        json_caracts_plan_prod = "[";
                    }
                    if (cantidad_caracts_plan_prod > 0) 
                    {
                        json_caracts_plan_prod = json_caracts_plan_prod + ",";
                    }
                    json_caracts_plan_prod     = json_caracts_plan_prod + "{\"idCaracteristica\":" + prod_caract[x] +
                                                ",\"caracteristica\":\"CANTIDAD DISPOSITIVOS\",\"valor\":" + valor_caract[x] + "}";
                    cantidad_caracts_plan_prod = cantidad_caracts_plan_prod + 1;
                }
                if (nombre_caract[x] == "[CORREO ELECTRONICO]")
                {
                    if (cantidad_caracts_plan_prod == 0) 
                    {
                        json_caracts_plan_prod = "[";
                    }
                    if (cantidad_caracts_plan_prod > 0) 
                    {
                        json_caracts_plan_prod = json_caracts_plan_prod + ",";
                    }
                    json_caracts_plan_prod = json_caracts_plan_prod + "{\"idCaracteristica\":" + prod_caract[x] +
                                            ",\"caracteristica\":\"CORREO ELECTRONICO\",\"valor\":\"" + valor_caract[x] + "\"}";
                    cantidad_caracts_plan_prod = cantidad_caracts_plan_prod + 1;
                    tiene_correo_electronico  = "SI";
                    correo_electronico        = valor_caract[x];
                }
                if (nombre_caract[x] == "[TIENE INTERNET]")
                {
                    if (cantidad_caracts_plan_prod == 0) 
                    {
                        json_caracts_plan_prod = "[";
                    }
                    if (cantidad_caracts_plan_prod > 0) 
                    {
                        json_caracts_plan_prod = json_caracts_plan_prod + ",";
                    }
                    json_caracts_plan_prod     = json_caracts_plan_prod + "{\"idCaracteristica\":" + prod_caract[x] +
                                                ",\"caracteristica\":\"TIENE INTERNET\",\"valor\":" + valor_caract[x] + "}";
                    cantidad_caracts_plan_prod = cantidad_caracts_plan_prod + 1;
                }
                if (nombre_caract[x] == "[ANTIVIRUS]")
                {
                    if (cantidad_caracts_plan_prod == 0) 
                    {
                        json_caracts_plan_prod = "[";
                    }
                    if (cantidad_caracts_plan_prod > 0) 
                    {
                        json_caracts_plan_prod = json_caracts_plan_prod + ",";
                    }
                    json_caracts_plan_prod      = json_caracts_plan_prod + "{\"idCaracteristica\":" + prod_caract[x] +
                                                  ",\"caracteristica\":\"ANTIVIRUS\",\"valor\":\"" + valor_caract[x] + "\"}";
                    cantidad_caracts_plan_prod  = cantidad_caracts_plan_prod + 1;
                    sigueFlujoAnteriorAntivirus = "NO";
                }
                if (nombre_caract[x] == caract_requiere_trabajo)
                {
                    if (cantidad_caracts_plan_prod == 0) 
                    {
                        json_caracts_plan_prod = "[";
                    }
                    if (cantidad_caracts_plan_prod > 0) 
                    {
                        json_caracts_plan_prod = json_caracts_plan_prod + ",";
                    }
                    json_caracts_plan_prod      = json_caracts_plan_prod + "{\"idCaracteristica\":" + prod_caract[x] +
                                                  ",\"caracteristica\":\"" + nombre_caract[x] + "\",\"valor\":\"" + valor_caract[x] + ",";
                                              
                    // Se agregarán los departamentos que forman parte de la característica Requiere Trabajo con su respectivo valor
                    var cantidad_departamento = 0;
                    var requiere_trabajo      = '';
                    var departamento          = '';
                    cantidad_departamento = formulario.cantidad_departamento.value; 
                    
                    json_caracts_plan_prod      = json_caracts_plan_prod + "\"" + valor_caract[x] + ":\"[";
                                                  
        
                    for (var j = 0; j < cantidad_departamento; j++)
                    {
                        departamento            = 'requiere_trabajo_' + j;
                        requiere_trabajo        = document.getElementById(departamento);  
                        
                        json_caracts_plan_prod  = json_caracts_plan_prod + "{\"DEPARTAMENTO\":" + document.getElementById(departamento).value +
                                                  ",\"VALOR\":\"" ;
        
                        if (requiere_trabajo.checked == true)
                        {
                            json_caracts_plan_prod  = json_caracts_plan_prod + "S" + "\"}";
                        }
                        else
                        {
                            json_caracts_plan_prod  = json_caracts_plan_prod + "N" + + "\"}";
                        }
                        cant_caracts_requiere_trab = cant_caracts_requiere_trab + 1 ;
                        if (cant_caracts_requiere_trab < cantidad_departamento)
                        {
                            json_caracts_plan_prod  = json_caracts_plan_prod + ",";
                        }
                    }
                    json_caracts_plan_prod = json_caracts_plan_prod + "]";                    
                    cantidad_caracts_plan_prod  = cantidad_caracts_plan_prod + 1;
                }
            }
            else
            {
                if (cantidad_caracts_plan_prod == 0) 
                {
                    json_caracts_plan_prod = "[";
                }
                if (cantidad_caracts_plan_prod > 0) 
                {
                    json_caracts_plan_prod = json_caracts_plan_prod + ",";
                }
                json_caracts_plan_prod     = json_caracts_plan_prod + "{\"idCaracteristica\":" + prod_caract[x] +
                                            ",\"caracteristica\":\"" + nombre_caract[x] + "\",\"valor\":\"" + valor_caract[x] + "\"}";
                cantidad_caracts_plan_prod = cantidad_caracts_plan_prod + 1;
            }
        }
        if (cantidad_caracts_plan_prod > 0) 
        {
            json_caracts_plan_prod = json_caracts_plan_prod + "]";
        }
        if (tiene_correo_electronico === "SI" )
        {
            if (Ext.isEmpty(correo_electronico) || !validarEmail(correo_electronico))
            {
                $('#mensaje_validaciones').removeClass('campo-oculto').html("Debe ingresar un correo electronico valido, favor verificar");
                Ext.get('gridServicios').unmask();
            }
            else if (!Ext.isEmpty(correo_electronico) && (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" ) && (descripcion_producto.indexOf("I. ") !== -1)
                && sigueFlujoAnteriorAntivirus === "SI")
            {
                Ext.Ajax.request({
                    url: url_ajaxValidaCorreoMcAfee,
                    method: 'post',
                    async: false,
                    timeout: 400000,
                    params: {
                        correoElectronico: correo_electronico
                    },
                    success: function(response) 
                    {
                        if (response.responseText != 'NO EXISTENTE')
                        {
                            if (response.responseText == 'EXISTENTE')
                            {
                                continuaFlujo = "NO";
                                $('#mensaje_validaciones').removeClass('campo-oculto')
                                .html("El correo electronico ingresado ya fue usado en " +
                                      "otra Suscripción McAfee, favor ingresar otro correo.");
                                Ext.get('gridServicios').unmask();
                            }
                            else if (response.responseText == 'ERROR')
                            {
                                continuaFlujo = "NO";
                                $('#mensaje_validaciones').removeClass('campo-oculto')
                                .html("Se presentaron errores al validar el correo electronico" +
                                      " ingresado, favor notificar a Sistemas.");
                                Ext.get('gridServicios').unmask();
                            }
                        }
                    },
                    failure: function()
                    {
                        continuaFlujo = "NO";
                        $('#mensaje_validaciones').removeClass('campo-oculto')
                        .html("Se presentaron errores al validar el correo electronico" +
                              " ingresado, favor notificar a Sistemas.");
                        Ext.get('gridServicios').unmask();
                    }
                });
            }
        }
    }
    var infoValidaCaractsPlanProd = new Object();
    infoValidaCaractsPlanProd["continuaFlujo"]          = continuaFlujo;
    infoValidaCaractsPlanProd["json_caracts_plan_prod"] = json_caracts_plan_prod;
    return Ext.JSON.encode(infoValidaCaractsPlanProd);
    
}


/* 
 * Función que carga el detalle de todas las características al seleccionar un producto
 * 
 * @author Kevin Baque <kbaque@telconet.ec>
 * @version 1.2 03-09-2020 - Se agrega lógica para enviar la propuesta selecionada.
 *
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.1 30-08-2019 Se elimina la programación document.getElementById('mensaje_validaciones').style.display 
 *                          para mostrar el error, ya que al hacerlo de esta manera, impide que otros errores se muestren
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.2 23-06-2020 Se elimina la programación document.getElementById('mensaje_validaciones').style.display 
 *                          para mostrar el error, ya que al hacerlo de esta manera, impide que otros errores se muestren
 *
 * @author José Candelario <afayala@telconet.ec>
 * @version 1.3 19-03-2021 Se agrega información de promociones para la empresa MD.
 * 
 * */
function cargar_detalle_catalogo()
{
    validar_ultima_milla();
    
    var strPlantillaComisionista = "";
    
    var valor_caract        = new Array();
    var nombre_caract       = new Array();
    var prod_caract         = new Array();
    var text                = "";
    var cantidad            = formulario.cantidad.value;
    var strRegistroUnitario = "N";
    var cantidadServicios   = 1;
    var cant_caracts_requiere_trab  = 0;
    
    //Si esta visible la característica "Requiere Trabajo" se debe incluir en el json
    var caract_requiere_trabajo     = "[REQUIERE TRABAJO]"
    
    if( document.getElementById("strRegistroUnitario") )
    {
        strRegistroUnitario = document.getElementById("strRegistroUnitario").value;
    }
    
    if( "S" == strRegistroUnitario )
    {
        cantidadServicios = cantidad;
        cantidad          = 1;
    }
    
    var precio_unitario = 0;
    var precio_total    = 0;
    var info_producto   = formulario.producto.value;
    var producto        = info_producto.split("-");
    var registro        = "";
    var x               = 0;
    var continuaFlujo   = "SI";
    var cantidad_dispositivos     = "";
    var cantidad_dispositivo_cont = 0;
    var tiene_correo_electronico  = "NO";
    var correo_electronico        = "";
    var cantidadDispositivosIPMP  = 0;
    informacion_controlador       = {};
    informacion_controlador["producto"] = 'producto[0]';
    informacion_controlador["cantidad"] = cantidad;
    var frecuencia                      = formulario.frecuencia_producto.value;
    var caracteristicas                 = "formulario.caracteristicas_";
    var caracteristica_nombre           = "formulario.caracteristica_nombre_";
    var producto_caracteristica         = "formulario.producto_caracteristica_";
    var cantidad_caracteristicas        = formulario.cantidad_caracteristicas.value;
    var caracteristicas_n               = "";
    var caracteristica_nombre_n         = "";
    var producto_caracteristica_n       = "";
    var descripcionProducto             = "";
    var precio_instalacion              = 0; 
    var precio_venta                    = 0;  
    var precio_instalacion_pactado      = 0;
    var ultimaMilla                     = null;  
    var login_vendedor                  = $('#infopuntoextratype_loginVendedor').val();  
    var nombre_vendedor                 = $('#infopuntoextratype_nombreVendedor').val();
    var sigueFlujoAnteriorAntivirus     = "SI";
    
    var tipo_esquema                    = producto[1] == 'INTERNET WIFI' ? document.getElementById("div-tipo-esquema").getElementsByTagName('select')[0].value : null;
    const instalacion_simultanea        = typeof(document.getElementById("div-instalacion-simultanea")) != 'undefined' && document.getElementById("div-instalacion-simultanea") != null ?
                                          document.getElementById("div-instalacion-simultanea").getElementsByTagName('input')[0].value :null;
    var intIndexPropuesta               = null;
    var intIdPropuesta                  = null;
    var strPropuesta                    = null;
    var objPropuesta                    = document.getElementById("objListadoPropuesta");
    var strCodigoPromo                  = "";
    var strNombrePromo                  = "";
    var strIdTipoPromo                  = "";
    var prodProMultiPaid                = producto[1].indexOf("I. PROTEGIDO MULTI PAID");//Acceso variable I. Protegido Multi PAID
    if(objPropuesta != null && objPropuesta != undefined)
    {
        intIndexPropuesta = document.getElementById("objListadoPropuesta").selectedIndex;
        if(intIndexPropuesta > 0 && intIndexPropuesta != undefined && intIndexPropuesta != null)
        {
            intIdPropuesta = document.getElementById("objListadoPropuesta").options[intIndexPropuesta].value;
            strPropuesta   = document.getElementById("objListadoPropuesta").options[intIndexPropuesta].text;
        }
    }
    for (var x = 0; x < cantidad_caracteristicas; x++)
    {
        caracteristicas_n         = caracteristicas + x;
        caracteristica_nombre_n   = caracteristica_nombre + x;
        producto_caracteristica_n = producto_caracteristica + x;
      
       if (typeof eval(caracteristicas_n) !== 'undefined') 
       {
           valor_caract[x]  = eval(caracteristicas_n).value;
       }
       else
       {
           valor_caract[x]  =  caracteristicas_n.value;
       }

       if (typeof eval(caracteristica_nombre_n) !== 'undefined') 
       {
           nombre_caract[x]  = eval(caracteristica_nombre_n).value;
       }
       else
       {
           nombre_caract[x] =  caracteristica_nombre_n.value;
       }        
       if (typeof eval(producto_caracteristica_n) !== 'undefined') 
       {
           prod_caract[x]  = eval(producto_caracteristica_n).value;
       }
       else
       {
           prod_caract[x]  =  producto_caracteristica_n.value;
       }         
      
    }
    
    informacion_controlador["valor_caract"]  = valor_caract;
    informacion_controlador["nombre_caract"] = nombre_caract;
    informacion_controlador["prod_caract"]   = prod_caract;
    informacion_controlador["info"]          = "C";
    if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
    {
        strCodigoPromo     = strCodigoPromocion;
        strNombrePromo     = strNombrePromocion;
        strIdTipoPromo     = strTipoPromocion;
    }
    var funcion_precio = formulario.funcion_precio.value;
    text               = funcion_precio;
    
    for (var x = 0; x < nombre_caract.length; x++)
    {
        text = replaceAll(text, nombre_caract[x], valor_caract[x]);
        
        if (nombre_caract[x] == "[CANTIDAD DISPOSITIVOS]" || nombre_caract[x] == "[CORREO ELECTRONICO]" || nombre_caract[x] == "[TIENE INTERNET]"
            || nombre_caract[x] == "[ANTIVIRUS]" || nombre_caract[x] == caract_requiere_trabajo)
        {
            if (nombre_caract[x] == "[CANTIDAD DISPOSITIVOS]")
            {
                if (cantidad_dispositivo_cont == 0) 
                {
                    cantidad_dispositivos = "[";
                }
                if (cantidad_dispositivo_cont > 0) 
                {
                    cantidad_dispositivos = cantidad_dispositivos + ",";
                }
                cantidad_dispositivos     = cantidad_dispositivos + "{\"idCaracteristica\":" + prod_caract[x] +
                                            ",\"caracteristica\":\"CANTIDAD DISPOSITIVOS\",\"valor\":" + valor_caract[x] + "}";
                cantidad_dispositivo_cont = cantidad_dispositivo_cont + 1;
                cantidadDispositivosIPMP  = valor_caract[x];
            }
            if (nombre_caract[x] == "[CORREO ELECTRONICO]")
            {
                if (cantidad_dispositivo_cont == 0) 
                {
                    cantidad_dispositivos = "[";
                }
                if (cantidad_dispositivo_cont > 0) 
                {
                    cantidad_dispositivos = cantidad_dispositivos + ",";
                }
                cantidad_dispositivos = cantidad_dispositivos + "{\"idCaracteristica\":" + prod_caract[x] +
                                        ",\"caracteristica\":\"CORREO ELECTRONICO\",\"valor\":\"" + valor_caract[x] + "\"}";
                cantidad_dispositivo_cont = cantidad_dispositivo_cont + 1;
                tiene_correo_electronico  = "SI";
                correo_electronico        = valor_caract[x];
            }
            if (nombre_caract[x] == "[TIENE INTERNET]")
            {
                if (cantidad_dispositivo_cont == 0) 
                {
                    cantidad_dispositivos = "[";
                }
                if (cantidad_dispositivo_cont > 0) 
                {
                    cantidad_dispositivos = cantidad_dispositivos + ",";
                }
                if((prefijoEmpresa == "MD" || prefijoEmpresa == "EN" ) && prodProMultiPaid !== -1)
                {
                    cantidad_dispositivos     = cantidad_dispositivos + "{\"idCaracteristica\":" + prod_caract[x] +
                                            ",\"caracteristica\":\"TIENE INTERNET\",\"valor\":\"" + valor_caract[x] + "\"}";
                }
                else
                {
                    cantidad_dispositivos     = cantidad_dispositivos + "{\"idCaracteristica\":" + prod_caract[x] +
                                            ",\"caracteristica\":\"TIENE INTERNET\",\"valor\":" + valor_caract[x] + "}";
                }

                cantidad_dispositivo_cont = cantidad_dispositivo_cont + 1;
            }
            if (nombre_caract[x] == "[ANTIVIRUS]")
            {
                if (cantidad_dispositivo_cont == 0) 
                {
                    cantidad_dispositivos = "[";
                }
                if (cantidad_dispositivo_cont > 0) 
                {
                    cantidad_dispositivos = cantidad_dispositivos + ",";
                }
                cantidad_dispositivos       = cantidad_dispositivos + "{\"idCaracteristica\":" + prod_caract[x] +
                                              ",\"caracteristica\":\"ANTIVIRUS\",\"valor\":\"" + valor_caract[x] + "\"}";
                cantidad_dispositivo_cont   = cantidad_dispositivo_cont + 1;
                sigueFlujoAnteriorAntivirus = "NO";
                
            }
            if (nombre_caract[x] == caract_requiere_trabajo)
            {
                if (cantidad_dispositivo_cont == 0) 
                {
                    cantidad_dispositivos = "[";
                }
                if (cantidad_dispositivo_cont > 0) 
                {
                    cantidad_dispositivos = cantidad_dispositivos + ",";
                }
                cantidad_dispositivos      = cantidad_dispositivos + "{\"idCaracteristica\":" + prod_caract[x] +
                                                  ",\"caracteristica\":\"" + nombre_caract[x] + "\",\"valor\":\"" + valor_caract[x] + "\",";
                                              
                // Se agregarán los departamentos que forman parte de la característica Requiere Trabajo con su respectivo valor
                var cantidad_departamento = 0;
                var requiere_trabajo      = '';
                var departamento          = '';
                cantidad_departamento = formulario.cantidad_departamento.value; 
                    
                cantidad_dispositivos      = cantidad_dispositivos + "\"" + valor_caract[x] + "\":\[";
                                                  
        
                for (var k = 0; k < cantidad_departamento; k++)
                {
                    departamento            = 'requiere_trabajo_' + k;
                    requiere_trabajo        = document.getElementById(departamento);  
                      
                    cantidad_dispositivos  = cantidad_dispositivos + "{\"DEPARTAMENTO\":\"" + document.getElementById(departamento).value +
                                                  "\",\"VALOR\":\"" ;
        
                    if (requiere_trabajo.checked == true)
                    {
                        cantidad_dispositivos  = cantidad_dispositivos + "S" + "\"}";
                    }
                    else
                    {
                        cantidad_dispositivos  = cantidad_dispositivos + "N" + "\"}";
                    }
                    cant_caracts_requiere_trab = cant_caracts_requiere_trab + 1 ;
                    if (cant_caracts_requiere_trab < cantidad_departamento)
                    {
                        cantidad_dispositivos  = cantidad_dispositivos + ",";
                    }
                }
                cantidad_dispositivos = cantidad_dispositivos + "]}";                    
                cantidad_dispositivo_cont  = cantidad_dispositivo_cont + 1;
            }
        }
        else
        {
            var boolAgregarCaracteristica = true;
            if(document.getElementById("strNombreTecnico").value)
            {
                if (prefijoEmpresa === 'TN' && document.getElementById("strNombreTecnico").value === "INTERNET SMALL BUSINESS" 
                    && nombre_caract[x] == "[VELOCIDAD]")
                {
                    if(document.getElementById("infoAdicionalProductos"))
                    {
                        document.getElementById("infoAdicionalProductos").value = prod_caract[x] + '||' + valor_caract[x];
                    }
                }
                if (prefijoEmpresa === 'TN' && document.getElementById("strNombreTecnico").value === "IPSB" && nombre_caract[x] == "[VELOCIDAD]")
                {
                    boolAgregarCaracteristica = false;
                }
            }
            if(boolAgregarCaracteristica)
            {
                if (cantidad_dispositivo_cont == 0) 
                {
                    cantidad_dispositivos = "[";
                }
                if (cantidad_dispositivo_cont > 0) 
                {
                    cantidad_dispositivos = cantidad_dispositivos + ",";
                }
                cantidad_dispositivos     = cantidad_dispositivos + "{\"idCaracteristica\":" + prod_caract[x] +
                                            ",\"caracteristica\":\"" + nombre_caract[x] + "\",\"valor\":\"" + valor_caract[x] + "\"}";
                cantidad_dispositivo_cont = cantidad_dispositivo_cont + 1;
            }
        }
    }
    
    if (cantidad_dispositivo_cont > 0) 
    {
        cantidad_dispositivos = cantidad_dispositivos + "]";
    }

    try
    {
        precio_venta_tmp = 1;
        if(formulario.precio_venta)
        {
            precio_venta_tmp = formulario.precio_venta.value;
        }
        
        try
        {
            precio_unitario = eval(text);
            if(isNaN(precio_unitario))
            {
                throw null;
            }
        }
        catch (err)
        {
            // Se inicializan en -1 para forzar el error y no permitir la agregación del servicio.
            precio_unitario  = 1;
            precio_venta_tmp = 1;
            cantidad         = -1;
            formulario.precio_venta.value = '';
            alert('Función precio mal definida, No se puede procesar este servicio');
        }
        
        if (prefijoEmpresa == 'TN' || prefijoEmpresa == 'TNP')
        {
            precio_instalacion         = formulario.precio_instalacionf.value; 
            precio_instalacion_pactado = formulario.precio_instalacion.value; 
            precio_venta               = precio_venta_tmp;  
            descripcionProducto        = formulario.descripcion_producto.value;
            precio_total               = (precio_venta * cantidad);
            
            informacion_controlador["precio_instalacion"]         = precio_instalacion;
            informacion_controlador["precio_instalacion_pactado"] = precio_instalacion_pactado;
            informacion_controlador["precio_total"]               = precio_total;
            informacion_controlador["precio_venta"]               = precio_venta;
        }
        else // MD
        {
            descripcionProducto = producto[1];
            precio_total        = (precio_unitario * cantidad);
            
            informacion_controlador["precio_total"] = precio_total;
        }
       
        if (precio_total >= 0)
        {
            // Se agrega Validacion para el ingreso de la Frecuencia en Productos con la caracteristica [FACTURACION_UNICA],
            //la frecuencia ingresada debera ser UNICA osea FRECUENCIA=0
            Ext.Ajax.request({
                url: url_ajaxValidaFrecuencia,
                method: 'post',
                async: false,
                timeout: 400000,
                params: {
                    intIdProducto: producto[0],
                    intFrecuencia: frecuencia
                },
                success: function (response)
                {
                    if (response.responseText != '')
                    {
                        continuaFlujo = "NO";                
                        $('#mensaje_validaciones').removeClass('campo-oculto')
                            .html(response.responseText);    
                        Ext.get('gridServicios').unmask();
                    }

                },
                failure: function ()
                {
                    continuaFlujo = "NO";
                    $('#mensaje_validaciones').removeClass('campo-oculto')
                        .html("Se presentaron errores en la validacion de FRECUENCIA y servicios de FACTURACION_UNICA" +
                            ", favor notificar a Sistemas.");
                    Ext.get('gridServicios').unmask();
                }
            });
            //
            if (tiene_correo_electronico == "SI" )
            {
                if (Ext.isEmpty(correo_electronico) || !validarEmail(correo_electronico))
                {
                    continuaFlujo = "NO";
                    $('#mensaje_validaciones').removeClass('campo-oculto').html("Debe ingresar un correo electronico valido, favor verificar");
                    Ext.get('gridServicios').unmask();
                }
                else if (!Ext.isEmpty(correo_electronico) && (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" ) 
                    && (descripcionProducto.indexOf("I. PROTEGIDO MULTI PAID") !== -1)
                    && sigueFlujoAnteriorAntivirus === "NO")
                {
                    Ext.Ajax.request({
                        url: strUrlValidaAgregarIPMP,
                        method: 'post',
                        async: false,
                        timeout: 400000,
                        params: {
                            intIdPunto: idPunto,
                            strCantidadDispositivosIPMP: cantidadDispositivosIPMP
                        },
                        success: function(response) 
                        {
                            var objData    = Ext.JSON.decode(response.responseText);
                            var strStatus  = objData.status;
                            var strMensaje = objData.mensaje;

                            if (strStatus == "ERROR")
                            {
                                continuaFlujo = "NO";
                                $('#mensaje_validaciones').removeClass('campo-oculto').html(strMensaje);
                                Ext.get('gridServicios').unmask();
                            }
                        },
                        failure: function()
                        {
                            continuaFlujo = "NO";
                            $('#mensaje_validaciones').removeClass('campo-oculto')
                            .html("Se presentaron errores al validar el servicio, favor notificar a Sistemas.");
                            Ext.get('gridServicios').unmask();
                        }
                    });
                }
                else if (!Ext.isEmpty(correo_electronico) && (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" ) && (descripcionProducto.indexOf("I. ") !== -1)
                    && sigueFlujoAnteriorAntivirus === "SI")
                {
                    Ext.Ajax.request({
                        url: url_ajaxValidaCorreoMcAfee,
                        method: 'post',
                        async: false,
                        timeout: 400000,
                        params: {
                            correoElectronico: correo_electronico
                        },
                        success: function(response) 
                        {
                            if (response.responseText != 'NO EXISTENTE')
                            {
                                if (response.responseText == 'EXISTENTE')
                                {
                                    continuaFlujo = "NO";
                                    $('#mensaje_validaciones').removeClass('campo-oculto')
                                    .html("El correo electronico ingresado ya fue usado en " +
                                          "otra Suscripción McAfee, favor ingresar otro correo.");
                                    Ext.get('gridServicios').unmask();
                                }
                                else if (response.responseText == 'ERROR')
                                {
                                    continuaFlujo = "NO";
                                    $('#mensaje_validaciones').removeClass('campo-oculto')
                                    .html("Se presentaron errores al validar el correo electronico" +
                                          " ingresado, favor notificar a Sistemas.");
                                    Ext.get('gridServicios').unmask();
                                }
                            }
                        },
                        failure: function()
                        {
                            continuaFlujo = "NO";
                            $('#mensaje_validaciones').removeClass('campo-oculto')
                            .html("Se presentaron errores al validar el correo electronico" +
                                  " ingresado, favor notificar a Sistemas.");
                            Ext.get('gridServicios').unmask();
                        }
                    });
        
                }
                else if (!Ext.isEmpty(correo_electronico) && (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" ) && (document.getElementById("strNombreTecnico").value == 'ECDF'))
                {
                    Ext.Ajax.request({
                        url: url_ajaxValidaCorreoECDF,
                        method: 'post',
                        async: false,
                        timeout: 400000,
                        params: {
                            correoElectronico: correo_electronico
                        },
                        success: function(response) 
                        {
                            if (response.responseText != 'NO EXISTENTE')
                            {
                                if (response.responseText == 'EXISTENTE')
                                {
                                    continuaFlujo = "NO";
                                    $('#mensaje_validaciones').removeClass('campo-oculto')
                                    .html("El correo electronico ingresado ya fue usado en " +
                                        "otra Suscripción del producto El Canal del Futbol, favor ingresar otro correo.");
                                    Ext.get('gridServicios').unmask();
                                }
                                else if (response.responseText == 'ERROR')
                                {
                                    continuaFlujo = "NO";
                                    $('#mensaje_validaciones').removeClass('campo-oculto')
                                    .html("Se presentaron errores al validar el correo electronico" +
                                        " ingresado, favor notificar a Sistemas.");
                                    Ext.get('gridServicios').unmask();
                                }
                            }
                        },
                        failure: function()
                        {
                            continuaFlujo = "NO";
                            $('#mensaje_validaciones').removeClass('campo-oculto')
                            .html("Se presentaron errores al validar el correo electronico" +
                                " ingresado, favor notificar a Sistemas.");
                            Ext.get('gridServicios').unmask();
                        }
                    });
                }
                else if (!Ext.isEmpty(correo_electronico) && (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN') 
                && (document.getElementById("strNombreTecnico").value == 'HBO-MAX'))
                {
                    let tipoProducto = document.getElementById("strNombreTecnico").value;
                    Ext.Ajax.request({
                        url: url_ajaxValidaCorreoHboElearn,
                        method: 'post',
                        async: false,
                        timeout: 400000,
                        params: {
                            correoElectronico: correo_electronico,
                            tipoProducto: tipoProducto
                        },
                        success: function(response) 
                        {
                            if (response.responseText != 'NO EXISTENTE')
                            {
                                if (response.responseText == 'EXISTENTE')
                                {
                                    continuaFlujo = "NO";
                                    $('#mensaje_validaciones').removeClass('campo-oculto')
                                    .html("El correo electronico ingresado ya fue usado en " +
                                        "otra Suscripción del producto "+tipoProducto+", favor ingresar otro correo.");
                                    Ext.get('gridServicios').unmask();
                                }
                                else if (response.responseText == 'ERROR')
                                {
                                    continuaFlujo = "NO";
                                    $('#mensaje_validaciones').removeClass('campo-oculto')
                                    .html("Se presentaron errores al validar el correo electronico" +
                                        " ingresado, favor notificar a Sistemas.");
                                    Ext.get('gridServicios').unmask();
                                }
                            }
                        },
                        failure: function()
                        {
                            continuaFlujo = "NO";
                            $('#mensaje_validaciones').removeClass('campo-oculto')
                            .html("Se presentaron errores al validar el correo electronico" +
                                " ingresado, favor notificar a Sistemas.");
                            Ext.get('gridServicios').unmask();
                        }
                    });
                }
                //aqui se debe agregar validacion de correo existente siendo usado
                
            }
            
            /**
             * Bloque que verifica la plantilla de comisionistas
             */
            if( prefijoEmpresa == "TN" && strValidarPlantilla == "S" )
            {
                if( !Ext.isEmpty(strCombosValidar) )
                {
                    var arrayCombosValidar = strCombosValidar.split("|");

                    for(var i = 0; i < arrayCombosValidar.length; i++)
                    {
                        var objInputVendedor = $("#inputVendedor"+arrayCombosValidar[i]);
                        var objComboValidar  = $("#cmb"+arrayCombosValidar[i]);
                        
                        if( !Ext.isEmpty(objComboValidar) )
                        {
                            var valorSeleccionado  = parseInt(objComboValidar.val());
                            var valorInputVendedor = "N";
                            
                            if( typeof objComboValidar.attr('required') !== "undefined" )
                            {
                                if( valorSeleccionado > 0 )
                                {
                                    if( !Ext.isEmpty(objInputVendedor) )
                                    {
                                        valorInputVendedor = objInputVendedor.val();

                                        if( valorInputVendedor == "S" )
                                        {
                                            nombre_vendedor = $("#cmb" + arrayCombosValidar[i] + " option:selected").text();
                                        }//( valorInputVendedor == "S" )
                                    }//( !Ext.isEmpty(objDivVendedor) )
                                }//( valorSeleccionado > 0 )
                                else
                                {
                                    mostrarAlertaFormulario('Vendedor', "cmb" + arrayCombosValidar[i]);
                                    continuaFlujo = 'NO';
                                    Ext.get('gridServicios').unmask();
                                    break;
                                }
                            }//( typeof $("#cmb"+arrayCombosValidar[i]).attr('required') !== "undefined" )
                            else
                            {
                                if( !Ext.isEmpty(objInputVendedor) )
                                {
                                    valorInputVendedor = objInputVendedor.val();
                                    
                                    if( valorInputVendedor == "S" )
                                    {
                                        nombre_vendedor = $("#cmb" + arrayCombosValidar[i] + " option:selected").text();
                                    }//( valorInputVendedor == "S" )
                                }//( !Ext.isEmpty(objDivVendedor) )
                            }

                            if( !Ext.isEmpty(strPlantillaComisionista) )
                            {
                                strPlantillaComisionista = strPlantillaComisionista + '|';
                            }

                            strPlantillaComisionista = strPlantillaComisionista + arrayCombosValidar[i] + '---' + valorSeleccionado;
                        }
                        else
                        {
                            continuaFlujo = 'NO';
                            Ext.Msg.alert('Atención', 'No se ha encontrado item requerido de la plantilla de comisionista.');
                            Ext.get('gridServicios').unmask();
                        }
                        //( !Ext.isEmpty(objComboValidar) )
                    }//(var i = 0; i < arrayCombosValidar.length; i++)
                }
                else
                {
                    continuaFlujo = 'NO';
                    Ext.Msg.alert('Atención', 'La plantilla de comisionistas para el producto seleccionado es requerido.');
                    Ext.get('gridServicios').unmask();
                }//( Ext.isEmpty(strCombosValidar) )
            }//( prefijoEmpresa == "TN" && strValidarPlantilla == "S" )
            

            if (continuaFlujo == "SI")
            {
                $('#mensaje_validaciones').addClass('campo-oculto').html("");
                
                var um_product = document.getElementById("ultimaMillaIdProd");
                
                // Se define el valor nombre y código de la última milla.
                if(um_product && um_product.value == '')
                {
                    ultimaMilla = '';
                    UM_Descrip  = 'Ninguna';
                }
                else
                {
                    ultimaMilla = um_product.value;
                    UM_Descrip  = um_product.options[um_product.selectedIndex].text;
                }
                var cot_product = '';
                cot_product = document.getElementById("cotizacionIdProd");
                
                // Se define el valor nombre y código de la Cotización.
                if(Ext.isEmpty(cot_product) ||cot_product.value == '' || cot_product.value == 'null')
                {
                    cotizacion = '';
                    COT_Descrip  = 'Ninguna';
                }
                else
                {
                    cotizacion = cot_product.value;
                    COT_Descrip  = cot_product.options[cot_product.selectedIndex].text;
                }

                var registro = 
                {
                    'codigo':                       producto[0], 
                    'producto':                     descripcionProducto, 
                    'precio':                       precio_unitario, 
                    'precio_total':                 precio_total, 
                    'cantidad':                     cantidad,
                    'frecuencia':                   frecuencia,
                    'info':                         "C",
                    'hijo':                         false,
                    'caracteristicasProducto':      cantidad_dispositivos,
                    'caractCodigoPromo':            strCodigoPromo,
                    'nombrePromo':                  strNombrePromo,
                    'idTipoPromo':                  strIdTipoPromo,
                    'precio_instalacion':           precio_instalacion,
                    'precio_instalacion_pactado':   precio_instalacion_pactado,
                    'precio_venta':                 precio_venta,
                    'um_desc':                      UM_Descrip,
                    'ultimaMilla':                  ultimaMilla,
                    'login_vendedor':               login_vendedor,
                    'nombre_vendedor':              nombre_vendedor,
                    'strPlantillaComisionista':     strPlantillaComisionista,
                    'tipo_esquema':                 tipo_esquema,
                    'instalacion_simultanea':       instalacion_simultanea,
                    'cotizacion':                   cotizacion,
                    'cot_desc':                     COT_Descrip,
                    'intIdPropuesta':               intIdPropuesta,
                    'strPropuesta':                 strPropuesta,
                    'intIdMotivoInstalacion':       intIdMotivoInstalacion
                };
                                               
                if (prefijoEmpresa === strEmpresaPermitida || prefijoEmpresa === 'TNP')
                {
                    registro.precio_venta               = precio_venta;
                    registro.precio_instalacion         = precio_instalacion;
                    registro.precio_instalacion_pactado = precio_instalacion_pactado;
                    registro.descripcion_producto       = descripcionProducto;
                    
                    if (servicioPrincipal != null)
                    {
                        registro.servicio   = servicioPrincipal.servicio;
                        registro.tipoMedio  = servicioPrincipal.tipoMedio;
                        registro.backupDesc = servicioPrincipal.producto;
                        registro.fecha      = servicioPrincipal.fecha;
                        servicioPrincipal   = null;
                        strTipoEnlace       = 'PRINCIPAL';
                    }
                }

                const intCantidadInternetWifi           = document.getElementById('input_cantidad-servicios-wifi') ? parseInt(document.getElementById('input_cantidad-servicios-wifi').value) : cantidadServicios;

                for(var index = 0; index < intCantidadInternetWifi; index++)
                {
                    var rec = new ListadoDetalleOrden(registro);
                    dataStoreServicios.add(rec);
                }
                
                Ext.get('gridServicios').unmask();
                $('button[type="submit"]').removeAttr('disabled');
                limpiar_detalle();
                if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
                {
                    strCodigoPromocion = "";
                    strNombrePromocion = "";
                    strTipoPromocion   = "";
                }

                if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN" )
                {
                    document.getElementById('idFrecuenciaPro').style = "display:none;";
                }
            }
            else if (prefijoEmpresa === 'TN' && document.getElementById("strNombreTecnico").value === "IPSB" )
            {
                formulario.cantidad_total_ingresada.value = Number(formulario.cantidad_total_ingresada.value) 
                                                            - Number(document.getElementById("cantidad").value);
            }
        }
        else
        {
            $('#mensaje_validaciones').removeClass('campo-oculto').html("Los valores ingresados no cumplen la función precio, favor verificar");
            Ext.get('gridServicios').unmask();
        }
    }
    catch (err)
    {
        $('#mensaje_validaciones').removeClass('campo-oculto').html("Los valores ingresados no cumplen la función precio, favor verificar");
        Ext.get('gridServicios').unmask();
    }
}


function agregarLabel(strNameComboBuscar, strNameComboReemplazar)
{
    var intIdPersonalSeleccionado = $("#cmb" + strNameComboBuscar).val();
    var objLabelReemplazar        = document.getElementById("str"+strNameComboReemplazar);
    var objComboReemplazar        = document.getElementById("cmb"+strNameComboReemplazar);
    
    if( !Ext.isEmpty(objLabelReemplazar) && !Ext.isEmpty(objComboReemplazar) )
    {
        objLabelReemplazar.value = '';
        objComboReemplazar.value = '';
        
        Ext.MessageBox.wait("Cargando información del personal relacionado...");
        
        Ext.Ajax.request
        ({
            url: strAjaxGetRelacionPersonal,
            method: 'post',
            timeout: 900000,
            params:
            {
                intIdPersonalSeleccionado: intIdPersonalSeleccionado
            },
            success: function(response) 
            {
                Ext.MessageBox.hide();
                
                var objJsonResponse = Ext.JSON.decode(response.responseText);
                
                if( objJsonResponse.strMensaje == 'OK' )
                {
                    objLabelReemplazar.value = objJsonResponse.strNombreLabel;
                    objComboReemplazar.value = objJsonResponse.strComboLabel;
                }
                else
                {
                    Ext.Msg.alert('Atención', objJsonResponse.strMensaje);
                }
            },
            failure: function()
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Atención', 'Hubo un problema al obtener la relación del comisionista seleccionado');
            }
        });
    }//( document.getElementById(strNameComboReemplazar) )
}   

function validaValoresCaracteristica()
{
    var prefijo                  = "";
    var valor_caract             = [];
    var nombre_caract            = [];  
    var cantidad_caracteristicas = formulario.cantidad_caracteristicas.value;    
   
    for (var x = 0; x < cantidad_caracteristicas; x++)
    {
        prefijo                  = x;      
        var id_caracteristica_n  = "caracteristicas_" + prefijo;        
        valor_caract[x]          = formulario["caracteristicas_" + prefijo].value;      
        nombre_caract[x]         = formulario["caracteristica_nombre_" + prefijo].value;                
        
        if ( nombre_caract[x] =='[METRAJE_NETFIBER]')
        {
            var metraje_inicial = Number(formulario.c_METRAJE_NETFIBER.value);    
            if ( isNaN(Number(document.getElementById(id_caracteristica_n).value) )
                || Number(document.getElementById(id_caracteristica_n).value) < metraje_inicial) 
            {
                $('#mensaje_validaciones').removeClass('campo-oculto').html("El Valor de " + nombre_caract[x] + 
                 " no debe ser un valor < " + metraje_inicial + " metros correspondiente al metraje inicial del kit.");
                return false;
            }
        }
    }
    return true;
}

/*
* Funcion que permite mostrar una alerta en pantalla y señala el campo faltante.
*
* @params:  strNombre => String que contenga el nombre del elemento.
*           strIdCampo => String que contenga el identificador del elemento HTML.
*
* @param strText => Texto a mostrar en pantalla.
*
* @author Pablo Pin
* @version 1.0 25-07-2019 | Version Inicial.
*
* @author Kevin Baque <kbaque@telconet.ec>
* @version 1.1 16-10-2019 - Se agrega lógica para la validación del ingreso de capacidad cuando se selecciona el tipo de red GPON.
*
*/

function mostrarAlertaFormulario(strNombre, strIdCampo) {

    var strMensaje = `Ingrese valor de: <b>${strNombre}</b>`;
    Ext.Msg.alert("Advertencia",strMensaje, function (btn) {

        if (btn == 'ok')
        {
            let objCaracteristica = Ext.get(strIdCampo);
            objCaracteristica.addCls('animated tada');
            objCaracteristica.frame('red', 1, {
                duration: 1000
            });
            objCaracteristica.focus();
            objCaracteristica.setStyle({borderColor: 'red'});
            setTimeout(
                function () {
                    objCaracteristica.removeCls('animated tada');
                },
                2000
            );
            objCaracteristica.on({
                'click': {
                    fn: function (evt, el) {
                        el.removeAttribute('style');
                    }
                },
                'change': {
                    fn: function (evt, el) {
                        el.removeAttribute('style');
                    }
                }
            });
        }
    });
}

/*
* Funcion que permite mostrar un mensaje de error en el HTML.
*
* @param strText => Texto a mostrar en pantalla.
*
* @author Pablo Pin
* @version 1.0 25-07-2019 | Version Inicial.
* 
* @author Lizbeth Cruz <mlcruz@telconet.ec>
* @version 1.1 30-08-2019 Se elimina la programación objElementoMensaje.setStyle({display: 'block'}) para mostrar el error, 
*                          ya que al hacerlo de esta manera impide que otros errores se muestren.
*                          Además se verifica si se está mostrando u ocultando el error para no provocar errores al usar ésta función
*/

function mostrarMensajeError(strTexto) {
    let objElementoMensaje = Ext.get('mensaje_validaciones');
    if (objElementoMensaje.hasCls('campo-oculto')) {
        objElementoMensaje.removeCls('campo-oculto');
        objElementoMensaje.setHTML(`
            <div id="msg_html">
            <strong>¡Ha ocurrido un error!</strong> ${strTexto}
            </div>`);
        objElementoMensaje.frame('red', 1, {
                duration: 1000
            });
    }
    
    setTimeout(() => {
        if (!objElementoMensaje.hasCls('campo-oculto')) {
            objElementoMensaje.dom.removeAttribute('style');
            objElementoMensaje.addCls('campo-oculto');
            if(document.getElementById('msg_html'))
            {
                Ext.get('msg_html').remove();
            }
        }
    }, 10000);
}

/**
* Funcion que permite agregar registros en el grid de Listado.
*
* @author Antonio Ayala <afayala@telconet.ec>
* @version 1.0 10-02-2020 Se agrega validación para activación simultánea con productos COU LINEAS TELEFONIA FIJA
*
* @author Jesús Bozada <jbozada@telconet.ec>
* @version 1.1 19-09-2020 Se agrega validación para productos de MD con nombre técnico IP que tengan asociada la caracteristica IP WAN
*
* @author Leonardo Mero <lemero@telconet.ec>
* @version 1.2 09-12-2022 Se agrega la validacion para comprobar que los archivos requeridos para el producto SAFE ENTRY se encuentran cargados
*
* 
* @author Liseth Candelario <lcandelario@telconet.ec>
* @version 1.2 03-02-2023 Se agregaron variables que vienen del php los cuales tienen el id + nombre del producto paquetes de soporte y
*                         Se realizò la concatenaciòn del id + nombre del producto, para relizar la validaciòn de facturaciòn ùnica
*
* @author Kenth Encalada <kencalada@telconet.ec>
* @version 1.3 23-06-2023 Se agrega validación para comprobar que la descripción del producto no contenga caracteres especiales.
*/
function agregar_detalle()
{      
    if((prefijoEmpresa === 'MD' || prefijoEmpresa === 'EN') && !validar_estado_punto())
    {
        return false;
    }

    // Si es producto(catálogo) se envía la última milla del producto "ultimaMillaIdProd", caso contrario "ultimaMillaId".
    var tipo = $("input:radio[@name='info']:checked").val();

    if (tipo == "catalogo")
    {
        ultimaMilla = document.getElementById("ultimaMillaIdProd").value;
    }
    else
    {
        ultimaMilla = document.getElementById("ultimaMillaId").value;
    }
    
    var boolValidarUMProd = true;
    if(document.getElementById("strRequiereUltimaMillaProducto"))
    {
        var strRequiereUMProd = document.getElementById("strRequiereUltimaMillaProducto").value;
        if(strRequiereUMProd === "NO")
        {
            boolValidarUMProd = false;
        }
    }

    if ((parseInt(ultimaMilla) <= 0 || isNaN(ultimaMilla)) && boolValidarUMProd)
    {
        mostrarAlertaFormulario('Última Milla', tipo === "catalogo" ? Ext.get('ultimaMillaIdProd') : Ext.get('ultimaMillaId'));
        return false;
    }
    
    if(!validaValoresCaracteristica())
    {
        return false;
    }
    
    var strDescripcionProd  = "";
    if (document.getElementById("strDescripcionProd") && prefijoEmpresa === 'TN')
    {
        strDescripcionProd = document.getElementById("strDescripcionProd").value;
    }

    var strEsIsB            = document.getElementById("strEsIsB").value;
    var puntoRestringidoISB = false;
    //Validacion para ISB
    if(prefijoEmpresa == 'TN' &&  strEsIsB == 'SI')
    {
        //No se puede crear Servicios para estos tipo s de negocios - Tipo de negocio Restringidos: CYBERS - ISP
        var strTipoNegociosRestringidos = document.getElementById("strTipoNegociosRestringidos").value;
        var strNombreTipoNegocioPto     = document.getElementById("strNombreTipoNegocioPto").value;
        var arrayTipoNegociosRest       = strTipoNegociosRestringidos.split('|');
        var strTipoOrden                = $('#infoserviciotype_tipoOrden').val();
        var strTextTipoOrden            = $( "#infoserviciotype_tipoOrden option:selected" ).text();
        
        for (var i = 0; i < arrayTipoNegociosRest.length; i++) {
            if(arrayTipoNegociosRest[i] === strNombreTipoNegocioPto)
            {
                puntoRestringidoISB = true;
            }
            
            if(puntoRestringidoISB)
            {
                break;
            }
        }
        
        if(puntoRestringidoISB)
        {
            mostrarMensajeError("No se puede crear producto "+strDescripcionProd+" para el Tipo de negocio "+ strNombreTipoNegocioPto);
            return false;
        }
        
        if( strTipoOrden !== 'N')
        {
            mostrarMensajeError("No se puede crear producto "+strDescripcionProd+" para tipo de orden "+ strTextTipoOrden);
            return false;   
        }
    }
    
    if (document.getElementById("strNombreTecnico"))
    {
        if (prefijoEmpresa === 'TN' && document.getElementById("strNombreTecnico").value === "IPSB")
        {
            actualizaDescripcion(document.getElementById("strNombreTecnico").value);
        }
        
        if((prefijoEmpresa == "MD" || prefijoEmpresa == "EN" ) &&
           (document.getElementById("strNombreTecnico").value === "WIFI_DUAL_BAND"
           || document.getElementById("strNombreTecnico").value === "WDB_Y_EDB" 
           || document.getElementById("strEsIpWanPyme").value === "S"))
        {
            if (
                ((document.getElementById("strNombreTecnico").value === "WIFI_DUAL_BAND"
                || document.getElementById("strNombreTecnico").value === "WDB_Y_EDB" ) && booleanAgregarWifiDualBand) ||
                (document.getElementById("strEsIpWanPyme").value === "S" && booleanAgregarIpWanPyme)
               )
            {
                strMensajeError = "No se puede agregar el producto <b>"+document.getElementById("strDescripcionProd").value+
                                  "</b>, ya se encuentra un producto de este tipo en el listado de servicios agregados.";
                mostrarMensajeError(strMensajeError);
                return false;
            }
            else
            {
                if(document.getElementById("strNombreTecnico").value === "WIFI_DUAL_BAND"
                  || document.getElementById("strNombreTecnico").value === "WDB_Y_EDB")
                {
                    booleanAgregarWifiDualBand = true;
                }
                else if (document.getElementById("strEsIpWanPyme").value === "S")
                {
                    booleanAgregarIpWanPyme = true;
                }
            }
        }
    }
    
    if (prefijoEmpresa === 'TN' || prefijoEmpresa === 'TNP')
    {
        var caracteristicas_n           = "";
        var caracteristica_nombre_n     = "";
        var valor_caract                = new Array();
        var nombre_caract               = new Array();    
        var arrayCaracteristicas        = []; 
        var precio_venta                = Number(document.getElementById("precio_venta").value);
        var precio_instalacion          = Number(document.getElementById("precio_instalacion").value); 
        var precio_formula              = Number(document.getElementById('precio_unitario').value);
        var precio_instalacion_formula  = Number(document.getElementById("precio_instalacionf").value); 
        var descripcion_producto        = document.getElementById("descripcion_producto").value; 
        var producto                    = document.getElementById("producto").value;
        var nombre_producto             = producto.split("-")[1];
        var frecuencia                  = document.getElementById("frecuencia_producto").value;
        var cantidad_caracteristicas    = formulario.cantidad_caracteristicas.value;
        var caracteristicas             = "formulario.caracteristicas_";
        var caracteristica_nombre       = "formulario.caracteristica_nombre_";     
        var estadoInicial               = document.getElementById("estadoInicial").value;
        var requiereAprobServicio       = document.getElementById('requiereAprobServicio').value;
        var msjSolAprobServicio         = document.getElementById('msjSolAprobServicio').value;
        var strContieneDeuda            = document.getElementById('strContieneDeuda').value;
        var strValorProductoPaqHoras    = document.getElementById('strValorProductoPaqHoras').value;
        var strValorProductoPaqHorasRec = document.getElementById('strValorProductoPaqHorasRec').value;
        var intIdProductoPaquetePrincipal= document.getElementById('intIdProductoPaquetePrincipal').value;
        var intIdProductoPaqueteRec      = document.getElementById('intIdProductoPaqueteRec').value;
        // Se realizò la concatenaciòn del id + nombre del producto, para relizar la validaciòn de facturaciòn ùnica
        var strProdHrsSopPincipal        = intIdProductoPaquetePrincipal + '-' + strValorProductoPaqHoras+'-';
        var strProdHrsSopRecarga         = intIdProductoPaqueteRec  + '-' + strValorProductoPaqHorasRec+'-';

        /*Variables para Internet Wifi Instalacion Simultanea*/
        const boolEsInstalacionSimultaneaWifi   = !!document.querySelector('div#div-instalacion-simultanea input');
        const intCantidadInternetWifi           = document.getElementById('input_cantidad-servicios-wifi') ? parseInt(document.getElementById('input_cantidad-servicios-wifi').value) : null;

        if (producto.search('INTERNET WIFI') !== -1)
        {
            if (boolEsInstalacionSimultaneaWifi)
            {
                const boolCheckedInput = document.querySelector('div#div-instalacion-simultanea input').checked;

                if (boolCheckedInput && (isNaN(intCantidadInternetWifi) || intCantidadInternetWifi < 1))
                {
                    mostrarAlertaFormulario('Cantidad servicios Wifi', Ext.get('input_cantidad-servicios-wifi'));
                    return false;
                }
            }
        }
        if(nombre_producto === 'SAFE ENTRY')
        {
            //Se valida que los archivos requeridos para el producto se encuentren cargados
            if(!validarArchivosSafeEntry())
            {
                return false;
            };
        }
            for (var x = 0; x < cantidad_caracteristicas; x++)
            {
                id_caracteristica_n       = "caracteristicas_" + x;
                caracteristicas_n         = caracteristicas + x;
                caracteristica_nombre_n   = caracteristica_nombre + x;
                if (document.getElementById(id_caracteristica_n).type == 'checkbox') 
                {
                    document.getElementById(id_caracteristica_n).value = document.getElementById(id_caracteristica_n).checked?'S':'N';
                }   
                valor_caract[x]           = eval(caracteristicas_n).value;
                nombre_caract[x]          = eval(caracteristica_nombre_n).value;

                var objCaracteristica     = {"nombre":nombre_caract[x] ,"valor":valor_caract[x]};
                arrayCaracteristicas[x]   = objCaracteristica;

                var strClassRelacion = "";
                var strValidaClassEmpty  = "";
                var boolValidaClassEmpty = false;
                var boolValorClassEmpty  = true;
                Ext.each(arrayListaClassRelacion, function( value_class, index_class ) {
                    //obtener las clases de relaciones
                    if(strClassRelacion === ""){
                        strClassRelacion = "." + value_class;
                    } else {
                        strClassRelacion += ",." + value_class;
                    }
                    //validar inputs por class
                    if($("#"+id_caracteristica_n).is("."+value_class)){
                        boolValidaClassEmpty = true;
                        strValidaClassEmpty  = "."+value_class;
                        $("select[class='"+value_class+"']").each(function(){
                            if($(this).val() != 0 && $(this).val() != ""){
                                boolValorClassEmpty = false;
                            }
                        });
                    }
                });
                //validar si hay caracteristicas adicionales sin ninguna agregada
                if(boolValidaClassEmpty && boolValorClassEmpty && strValidaClassEmpty.length > 0){
                    var strMensajeAlerta = "Se debe ingresar por lo menos una característica para un servicio adicional del producto.";
                    mostrarMensajeAlertaFormulario(strMensajeAlerta, strValidaClassEmpty);
                    return false;
                }

                if (document.getElementById(id_caracteristica_n).value == '' && !$("#"+id_caracteristica_n).is(strClassRelacion))
                {
                    mostrarAlertaFormulario(nombre_caract[x], id_caracteristica_n);
                    return false;
                }
            }


        if (frecuencia == '')
        {
            // mostrarAlertaFormulario('Frecuencia de Facturación', Ext.get('frecuencia_producto'));
            mostrarMensajeError("Ingrese frecuencia de facturación");
            return false;
        }
        else if (producto == 'Seleccione')
        {
            mostrarAlertaFormulario('Producto', Ext.get('producto'));
            return false;
        }
        else if (strContieneDeuda === 'S')
        {
            mostrarMensajeError("Servicio no pudo ser agregado dado que el cliente aún contiene deudas.");
            return false;
        }
        else if (isNaN(precio_venta) || precio_venta <= 0)
        {
            mostrarMensajeError("Ingrese Precio de Negociación,debe ser un valor numérico. Favor Verificar");
            return false;
        }
        else if (precio_instalacion_formula != null && precio_instalacion_formula != 0)
        {
            if (isNaN(precio_instalacion) || precio_instalacion < 0)
            {
                mostrarMensajeError("Ingrese Precio de Instalación con valor numérico. Favor Verificar");
                return false;
            }
        }
        else if (descripcion_producto == '' || descripcion_producto == null)
        {
            mostrarAlertaFormulario('Descripción Producto', Ext.get('descripcion_producto'));
            return false;
        }

        if (estadoInicial=='Activo')
        {
            if(rol=='Cliente')
            {
                Ext.Msg.alert("Atención", "Producto no requiere flujo. Se realiza Activación automática.");
            }
            else
            {
                Ext.Msg.alert("Atención", "Producto no requiere flujo. Se realizará Activación automática en la Aprobación del Contrato");    
            }
        }
        if (precio_venta < precio_formula)
        {

                Ext.Msg.alert("Atención", "Precio de Negociación es menor al precio fórmula. Se generará solicitud de descuento");
            
        }
        if (precio_instalacion < precio_instalacion_formula && strEsIsB !== 'SI')
        {
            if(intIdMotivoInstalacion == 0)
            {
                mostrarMensajeError("Seleccione un motivo para la creación automática de solicitud de instalación");
                return false;
            }
        }
        if(requiereAprobServicio === "SI" && !Ext.isEmpty(msjSolAprobServicio))
        {
            Ext.Msg.alert("Atención", msjSolAprobServicio);
        }

        // Relizar la validaciòn de facturaciòn ùnica
        if ( ( (producto === strProdHrsSopPincipal) || (producto === strProdHrsSopRecarga)  ) && (frecuencia != 0) )
        {
            mostrarMensajeError('Para el producto PAQUETE DE HORA DE SOPORTE solo se puede seleccionar frecuencia de facturación Única ' );
            
            return false;
        }
    }

    // Quitar caracteres especiales de la descripción del producto.
    if (prefijoEmpresa === 'TN')
    {
        const patronCaracteresEspeciales    = /[\'^£$%&*()}{@#~?><>,|=+¬\/"]/gi;
        const strDescripcionProducto          = document.getElementById("descripcion_producto")?.value ?? '';
        if (patronCaracteresEspeciales.test(strDescripcionProducto))
        {
            Ext.Msg.alert(
            'Alerta!', 
            'La descripción del producto contenía caracteres inválidos, por lo que se procederá con el ajuste del campo de texto.',
            function () {
                document.getElementById("descripcion_producto").value = strDescripcionProducto.replace(patronCaracteresEspeciales, '').trim();
                if (validar_detalle_catalogo())
                {
                    validaProductoInternet(arrayCaracteristicas);
                }
            });
        }
        else
        {
            if (validar_detalle_catalogo())
            {
                validaProductoInternet(arrayCaracteristicas);
            }
        }
        
    }
    else
    {
        if (validar_detalle_catalogo())
        {
            validaProductoInternet(arrayCaracteristicas);
        }
    }
}
/**
 * Documentación para la función 'validarSolicitudInstalacion'.
 *
 * Función encargada de mostrar los motivos para crear una solicitud de instalación.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 03-10-2022
 *
 */
function validarSolicitudInstalacion() {
    intIdMotivoInstalacion = 0;
    if(Number(document.getElementById("precio_instalacionf").value) > Number(document.getElementById("precio_instalacion").value))
    {
        Ext.Msg.confirm('Alerta', 'Precio de Instalación es menor a precio de instalación sugerido. Se generará solicitud de instalación</b>¿Desea continuar?', function (btn) {
            if (btn == 'yes') {
                var objStoreMotivo = Ext.create('Ext.data.Store', {
                    fields: ['intIdMotivos', 'strMotivos'],
                    data:   arrayMotivoInstalacion
                });
                var objCmbMotivoInstalacion = Ext.create('Ext.form.ComboBox', {
                    xtype: 'combobox',
                    fieldLabel: 'Motivo',
                    store: objStoreMotivo,
                    labelAlign: 'left',
                    queryMode: 'local',
                    editable: false,
                    displayField: 'strMotivos',
                    valueField: 'intIdMotivos',
                    allowBlank: false,
                    value: "",
                    width: 470,
                    labelStyle: 'text-align:left;margin: auto;'
                });
                objPanelMotivosInstalacion = Ext.create('Ext.form.Panel', {
                    title: '',
                    bodyPadding: 5,
                    renderTo: Ext.getBody(),
                    items:
                        [
                            {
                                items:
                                    [
                                        objCmbMotivoInstalacion
                                    ]
                            }
                        ],
                    buttons:
                        [
                            {
                                text: 'Guardar',
                                name: 'guardarBtn',
                                disabled: false,
                                handler: function () {
                                    if (objCmbMotivoInstalacion.getValue() != "" && objCmbMotivoInstalacion.getValue() != null && objCmbMotivoInstalacion.getValue() != undefined) {
                                        intIdMotivoInstalacion = Ext.isEmpty(objCmbMotivoInstalacion.getValue()) ? 0 : objCmbMotivoInstalacion.getValue();
                                        Ext.MessageBox.show({
                                            modal: true,
                                            title: 'Información',
                                            msg: 'Guardado correctamente.',
                                            width: 300,
                                            icon: Ext.MessageBox.INFO,
                                            buttons: Ext.Msg.OK
                                        });
                                        objVentanaMotivos.destroy();
                                    }
                                    else {
                                        intIdMotivoInstalacion = 0;
                                        Ext.MessageBox.show({
                                            modal: true,
                                            title: 'Alerta',
                                            msg: 'Seleccione un motivo de la lista.',
                                            width: 300,
                                            icon: Ext.MessageBox.ERROR,
                                            buttons: Ext.Msg.OK
                                        });
                                    }
                                }
                            },
                            {
                                text: 'Cancelar',
                                handler: function () {
                                    intIdMotivoInstalacion = 0;
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Alerta',
                                        msg: 'Seleccione un motivo de la lista.',
                                        width: 300,
                                        icon: Ext.MessageBox.ERROR,
                                        buttons: Ext.Msg.OK
                                    });
                                }
                            }
                        ]
                });
                objVentanaMotivos = Ext.widget('window', {
                    title: '',
                    closeAction: 'hide',
                    closable: false,
                    width: 500,
                    height: 120,
                    layout: 'fit',
                    modal: true,
                    resizable: false,
                    items: [objPanelMotivosInstalacion]
                }).show();
            }
        });
    }
}

/* 
 * Función que valida el servicio de Internet usado para Líneas Fijas para TN
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.1 30-08-2019 Se elimina la programación document.getElementById('mensaje_validaciones').style.display 
 *                          para mostrar el error, ya que al hacerlo de esta manera, impide que otros errores se muestren
 * @since 1.0
 */
function validaProductoInternet(arrayCaracteristicas)
{
    //armo la data con las caracteristicas
    var data;
    var nombre;
    var producto = Number(document.getElementById("producto").value.split("-")[0]);
    
    if(arrayCaracteristicas)
    {    
        for (var x = 0; x < arrayCaracteristicas.length ; x++)
        {      
            nombre = arrayCaracteristicas[x].nombre.replace("[", "").replace("]", "").toLowerCase().replace(" ","_").replace(" ","_");
            if(x == 0)
            {
                data = nombre +'='+ arrayCaracteristicas[x].valor;
            }
            else
            {
                data = data + '&'+nombre +'='+ arrayCaracteristicas[x].valor;
            }        
        }

        if(data)
        {
            data = "productoId="+producto+"&"+data;
        }
        else
        {
            data = "productoId="+producto;
        }
        //Validar que posea un servicio de internet valido para poder contratar NetlifeCam 
        //Se excluyen los servicios en estado Rechazado, Rechazada, Cancelado, Anulado, Cancel, Eliminado, Reubicado, Trasladado, Incorte, InTemp

        $.ajax({
            type: "POST",
            data: data,
            url: valida_producto_internet,
            success: function(msg)
            {
                if (msg.msg == 'ERROR')
                {
                    $('#mensaje_validaciones').removeClass('campo-oculto').html("" + msg.mensaje_validaciones + "");
                    Ext.get('gridServicios').unmask();

                }
                else
                {
                    validarIps();
                }

            },
            failure: function()
            {
                Ext.Msg.alert('Atención', 'Error');
                Ext.get('gridServicios').unmask();            
            }
        });   
    }
    else
    {
            validarProductoAdicional();
            
    }
}

function limpiar_detalle_portafolio(cleanPlan)
{
    if(formulario.cantidad_plan)
	{
        formulario.cantidad_plan.value="";
    }
            
	if(formulario.precio)
	{
        formulario.precio.value="";
    }
	
	if(formulario.planes && cleanPlan)
	{
        formulario.planes.options[0].selected = true;
    }
		
	if(formulario.ultimaMillaId)
	{
        formulario.ultimaMillaId.options[0].selected = true;
    }
    
    if(document.getElementById("div_caracts_planes_productos") && cleanPlan)
    {
        document.getElementById("div_caracts_planes_productos").innerHTML = "";
    }
		
	$('#mensaje_validaciones').addClass('campo-oculto').html("");
}

function agregarCaracteristica(data)
{
	if(data.caracteristicasProducto == '')
	{
		var storeDetalle = new Ext.data.Store({
			pageSize: 1000,
			proxy: {
				type: 'ajax',
				url : listar_caracteristicas_producto,
				reader: {
					type: 'json',
					totalProperty: 'total',
					root: 'encontrados'
				}
			},
			listeners:{
				beforeload: function (storeDetalle){
						storeDetalle.getProxy().extraParams.idProducto=data.codigo;
						storeDetalle.getProxy().extraParams.cantidad=data.cantidad;
					}
			},
			fields:
				[
					{name:'idCaracteristica', mapping:'idCaracteristica'},
					{name:'caracteristica', mapping:'caracteristica'},
					{name:'valor', type:'string'}
				]
		});
	}
	else
    {
        var storeDetalle = new Ext.data.Store(
            {
                pageSize: 1000,
                data: Ext.JSON.decode(data.caracteristicasProducto),
                proxy:
                    {
                        type: 'memory',
                        reader:
                            {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'encontrados'
                            }
                    },
                fields:
                    [
                        {name: 'idCaracteristica', mapping: 'idCaracteristica'},
                        {name: 'caracteristica', mapping: 'caracteristica'},
                        {name: 'valor', type: 'string'}
                    ]
            });
    }
    
    storeDetalle.load();

    Ext.define('Detalle',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'idCaracteristica', mapping: 'idCaracteristica'},
                    {name: 'caracteristica', mapping: 'caracteristica'}
                ]
        });

    var cellEditingCarac = Ext.create('Ext.grid.plugin.CellEditing', 
    {
        clicksToEdit: 1
    });

    gridCaracteristica = Ext.create('Ext.grid.Panel',
        {
            store: storeDetalle,
            columns:
                [
                    {
                        text: 'idCaracteristica',
                        width: 80,
                        dataIndex: 'idCaracteristica',
                        hidden: false,
                        tdCls: 'x-change-cell'
                    },
                    {
                        header: 'Caracteristica',
                        dataIndex: 'caracteristica',
                        width: 200
                    },
                    {
                        header: 'Valor',
                        dataIndex: 'valor',
                        width: 200,
                        field:
                            {
                                xtype: 'textfield',
                                allowBlank: false
                            }
                    },
                    {
                        header: 'Acciones',
                        xtype: 'actioncolumn',
                        width: 100,
                        sortable: false,
                        items:
                            [
                                {
                                    iconCls: 'button-grid-delete',
                                    tooltip: 'Eliminar',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        storeDetalle.removeAt(rowIndex);
                                    }
                                }
                            ]
                    }
                ],
            selModel:
                {
                    selType: 'cellmodel'
                },
            width: 800,
            height: 200,
            title: 'Listado de caracteristicas',
            frame: true,
            plugins: [cellEditingCarac]
        });

    var win = Ext.create('Ext.window.Window',
        {
            title: 'Agregar Caracteristica',
            modal: true,
            width: 800,
            closable: false,
            layout: 'fit',
            items: [gridCaracteristica],
            bbar:
                [
                    {
                        text: 'Guardar',
                        formBind: true,
                        handler: function()
                        {
                            if (true)
                            {
                                obtenerCaracteristicas(data);
                                win.destroy();
                            }
                            else
                            {
                                Ext.Msg.alert("Failed", "Favor Revise los campos", function(btn)
                                {
                                    if (btn == 'ok')
                                    {
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function()
                        {
                            this.up('.window').close();
                        }
                    }
                ]
        }).show();
	
    function obtenerCaracteristicas(data)
    {
        var array_relaciones                = new Object();
        array_relaciones['total']           = gridCaracteristica.getStore().getCount();
        array_relaciones['caracteristicas'] = new Array();
        var array_data                      = new Array();
        for (var i = 0; i < gridCaracteristica.getStore().getCount(); i++)
        {
            array_data.push(gridCaracteristica.getStore().getAt(i).data);
        }
        array_relaciones['caracteristicas'] = array_data;
        data.caracteristicasProducto = Ext.JSON.encode(array_data);
    }

}

function validarEmail( email ) 
{
    return /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(email);
}

function actualizaDescripcion(textInput) 
{
    var cantidad                = '';
    var funcion_precio          = '';
    var cantidad_caracteristicas = '';
    var caracteristicas         = '';
    var caracteristica_nombre   = '';
    var producto_caracteristica = '';
    var descripcion_producto    = '';    
    var boolEsGrupo             = false;
    var esIntenetLite           = false;
    var esTelcoHome             = false;
    var boolCambioPreInstalacion = false;
        
    if(typeof formulario.cantidad == 'undefined')
    {
        cantidad                 = $("#cantidad").val();
        funcion_precio           = $("#funcion_precio").val();
        cantidad_caracteristicas = $("#cantidad_caracteristicas").val();
        caracteristicas          = 'caracteristicas_';
        caracteristica_nombre    = 'caracteristica_nombre_';
        producto_caracteristica  = 'producto_caracteristica_';
        descripcion_producto     = $("#hd_nombre_producto").val();
        boolEsGrupo              = true;
    }
    else//Creacion de productos de la forma convencional
    {
        cantidad                 = formulario.cantidad.value;
        funcion_precio           = formulario.funcion_precio.value;
        cantidad_caracteristicas = formulario.cantidad_caracteristicas.value; 
        caracteristicas          = 'formulario.caracteristicas_';
        caracteristica_nombre    = 'formulario.caracteristica_nombre_';
        producto_caracteristica  = 'formulario.producto_caracteristica_';
        descripcion              = document.getElementById('producto').value;
        producto                 = descripcion.split('-');
        descripcion_producto     = producto [1];
        esIntenetLite            = false;
        
        if (document.getElementById("strNombreTecnico"))
        {
            if (document.getElementById("strNombreTecnico").value === "INTERNET SMALL BUSINESS")
            {
                esIntenetLite = true;
            }
            else if (document.getElementById("strNombreTecnico").value === "TELCOHOME")
            {
                esTelcoHome = true;
                var intNumGuiones   = descripcion.split('-').length;
                var intNumDosPuntos = descripcion.split(':').length;
                if((intNumGuiones - intNumDosPuntos) > 2)
                {
                    var nombreProducto = descripcion.substr(producto[0].length+1);
                    if(intNumDosPuntos > 1)
                    {
                        nombreProducto = nombreProducto.substr(0,nombreProducto.indexOf(":"));
                        nombreProducto = nombreProducto.substr(0,nombreProducto.lastIndexOf("-"));
                    }
                    else
                    {
                        nombreProducto = nombreProducto.substr(0, nombreProducto.length-1);
                    }
                    descripcion_producto = nombreProducto;
                }
            }
        }

        if (document.getElementById("strPermiteCambioPreInstalacion")
            && document.getElementById("strPermiteCambioPreInstalacion").value === "S")
        {
            boolCambioPreInstalacion = true;
        }
    }
    
    var precio_unitario             = 0;
    var precio_total                = 0;      
    var caracteristicas_n           = "";
    var caracteristica_nombre_n     = "";
    var producto_caracteristica_n   = ""; 
    var valor_caract                = new Array();
    var nombre_caract               = new Array();
    var prod_caract                 = new Array();  
    var boolEsPoolRecursos          = false;
    var boolNecesitaAprobacion      = false;
        
    //escenario solo para pool de recursos de cloud IAAS
    if(typeof(textInput) === 'object' && descripcion_producto.includes("POOL RECURSOS"))
    {
        boolEsPoolRecursos = true;
    }
    
    if(!boolEsPoolRecursos)
    {
        if(textInput || cantidad_caracteristicas>=1)
        {
            for (var x = 0; x < cantidad_caracteristicas; x++)
            { 
                var muestraGrupoNegocioDescProd = true;
                id_caracteristica_n       = "caracteristicas_" + x;
                caracteristicas_n         = caracteristicas + x;            
                caracteristica_nombre_n   = caracteristica_nombre + x;
                producto_caracteristica_n = producto_caracteristica + x;
                if (document.getElementById(id_caracteristica_n).type == 'checkbox') 
                {
                    document.getElementById(id_caracteristica_n).value = document.getElementById(id_caracteristica_n).checked?'S':'N';
                }             
                valor_caract[x]           = eval(caracteristicas_n).value;                       
                if(valor_caract[x]==null || valor_caract[x]=='')
                {                
                    return false;
                }            
                nombre_caract[x]          = eval(caracteristica_nombre_n).value;
                //verificar class relacion
                Ext.each(arrayListaClassRelacion, function( value_class, index_class ) {
                    if($("#"+id_caracteristica_n).is("."+value_class)){
                        muestraGrupoNegocioDescProd = false;
                    }
                });
                //verificar caracteristicas relacion
                if($("#"+id_caracteristica_n).is('[class*="relacion_caracteristicas_"]')){
                    muestraGrupoNegocioDescProd = false;
                }
                if((esIntenetLite || esTelcoHome) && (nombre_caract[x] == '[Grupo Negocio]' || nombre_caract[x] == '[TIPO_FACTIBILIDAD]'))
                {
                    muestraGrupoNegocioDescProd = false;
                }
                if(esTelcoHome && nombre_caract[x] == '[VELOCIDAD_TELCOHOME]')
                {
                    var strValidaValoresCaracts     = document.getElementById("strValidaValoresCaracts").value;
                    var arrayValidaValoresCaracts   = strValidaValoresCaracts.split('|');
                    for (var indiceValor = 0; indiceValor < arrayValidaValoresCaracts.length; indiceValor++) 
                    {
                        if(arrayValidaValoresCaracts[indiceValor] === valor_caract[x])
                        {
                            boolNecesitaAprobacion = true;
                            break;
                        }
                    }
                }
                if (muestraGrupoNegocioDescProd && (nombre_caract[x] !== '[INSTALACION_SIMULTANEA_WIFI]' &&
                    nombre_caract[x] !== '[REQUIERE_INSPECCION]' && nombre_caract[x] !== '[INSTALACION_SIMULTANEA]'))
                {
                    descripcion_producto += ' ' + valor_caract[x];
                }
                prod_caract[x] = eval(producto_caracteristica_n).value;
            } 

            for (var x = 0; x < nombre_caract.length; x++)
            {
                funcion_precio = replaceAll(funcion_precio, nombre_caract[x], valor_caract[x]);
            }

            try
            {
                precio_unitario = eval(funcion_precio);
                if(isNaN(precio_unitario))
                {
                    throw null;
                }
            }
            catch (err)
            {
                alert('Función precio mal definida, No se puede procesar este servicio');
            }
        }
    }
    if(esTelcoHome && boolNecesitaAprobacion)
    {
        document.getElementById('precio_instalacion').readOnly  = false;
        document.getElementById('requiereAprobServicio').value  = "SI";
    }
    else if(esIntenetLite)
    {
        document.getElementById('precio_instalacion').readOnly  = true;
        document.getElementById("precio_instalacion").value     = document.getElementById("precio_instalacionf").value;
        document.getElementById('requiereAprobServicio').value  = "NO";
    }
    else
    {
        document.getElementById('requiereAprobServicio').value  = "NO";
    }
    if(boolCambioPreInstalacion)
    {
        document.getElementById('precio_instalacion').readOnly  = false;
    }

    if(!isNaN(precio_unitario))
    {
        precio_total  = (precio_unitario * cantidad);
    }
    else
    {
        precio_unitario = "";
        
        if(boolEsGrupo)
        {
            Ext.Msg.alert('Atención', 'Los valores ingresados no cumplen la función precio, favor verificar');
        }
        else
        {
            $('#mensaje_validaciones').removeClass('campo-oculto').html("Los valores ingresados no cumplen la función precio, favor verificar");
        }            
    }

    if(document.getElementById('descripcion_producto'))
    {
        document.getElementById('descripcion_producto').value = descripcion_producto;
    }

    if(document.getElementById('precio_venta'))
    {
        document.getElementById('precio_venta').value = precio_unitario;
    }

    if(document.getElementById('precio_unitario'))
    {
        document.getElementById('precio_unitario').value = precio_unitario;
    }

    if(document.getElementById('precio_total'))
    {
        document.getElementById('precio_total').value = precio_total;
    }

    if(document.getElementById('precio_venta'))
    {
        document.getElementById('precio_venta').disabled = false;
    }
}

function actualizaTotal () 
{
    var precioNegociacion = '';
    var cantidad          = '';
    
    if(typeof formulario.cantidad == 'undefined')
    {
        precioNegociacion = $("#precio_venta").val();
        cantidad          = $("#cantidad").val();
    }
    else
    {
        precioNegociacion = formulario.precio_venta.value; 
        cantidad          = formulario.cantidad.value;  
    }
    
    document.getElementById('precio_total').value         =  precioNegociacion * cantidad;
}

function validaSoloNumeros(e) 
{
     tecla = (document.all) ? e.keyCode : e.which;
    //Tecla de retroceso para borrar, siempre la permite
  
    if (tecla==8)
    {
        return true;
    }       
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9\.]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final); 
}

function mostrarServicioPrincipal(data)
{
    var formServicioPrincipal = Ext.create('Ext.form.Panel',
        {
            id: 'formServicipal',
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 125,
                    msgTarget: 'side'
                },
            buttons:
                [
                    {
                        text: 'Cerrar',
                        handler: function()
                        {
                            win3.destroy();
                        }
                    }
                ],
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: 400,
                        labelWidth: 70,
                        width: 320,
                        defaultType: 'textfield',
                        items:
                            [
                                {
                                    fieldLabel: 'Servicio Princial',
                                    readOnly: true,
                                    value: data.backupDesc
                                },
                                {
                                    fieldLabel: 'Última Milla',
                                    readOnly: true,
                                    value: data.tipoMedio
                                },
                                {
                                    fieldLabel: 'Fecha Creación',
                                    readOnly: true,
                                    value: data.fecha
                                }
                            ]
                    }
                ]
        });

    win3 = Ext.create('Ext.window.Window',
        {
            title: 'Datos del Servicio Respaldado',
            modal: true,
            width: 340,
            closable: true,
            layout: 'fit',
            items: [formServicioPrincipal]
        }).show();
}

/*
* Funcion que permite controlar el funcionamiento del checkbox de "Instalación Simultanea".
*
* @author Pablo Pin <ppin@telconet.ec>
* @version 1.0
*
* @author Antonio Ayala <afayala@telconet.ec>
* @version 1.1 06-02-2020 Se agrega Instalación Simultánea para productos COU LINEAS TELEFONIAS FIJA
*/

function instalacionSimultanea(cb, idServTrad, strDescripcionProducto) {

    const objCheckbox = document.getElementById(cb.id);
    objCheckbox.value = cb.checked ? idServTrad : 'null';

    if (strDescripcionProducto == 'INTERNET WIFI')
    {
        const objOpEsq1 = document.getElementById('e1');
        const objOpEsq2 = document.getElementById('e2');
        const objSelTipoEsq = document.getElementById("div-tipo-esquema").getElementsByTagName('select')[0];
        
        objOpEsq1.toggleAttribute('disabled');
        objOpEsq2.toggleAttribute('disabled');

        objSelTipoEsq.selectedIndex = 0;


        if (objOpEsq1.disabled && objOpEsq2.disabled)
        {
            objOpEsq2.toggleAttribute('disabled');
        }

        agregarCantidadInternetWifi(cb.checked)
    }
    if (strDescripcionProducto == 'COU LINEAS TELEFONIAS FIJA')
    {
        const objOpEsq1 = document.getElementById('e1');
        objOpEsq1.toggleAttribute('disabled');
        
        objSelTipoEsq.selectedIndex = 0;
    }    
    
}

/*
* Funcion que permite agregar o quitar la caja de texto para ingresar simultáneamente
* más de 1 servicio de Internet Wifi.
*
* @author Pablo Pin <ppin@telconet.ec>
* @version 1.0 05-07-2019
*
* @param checked -> Hace referencia al checkbox, si esta marcado true sino false.
*
*/

function agregarCantidadInternetWifi(checked) {

    if (checked)
    {
        /*Obtengo el objeto del div instalacion simultánea mediante su id.*/
        const objDivInternetWifi = document.getElementById('div-instalacion-simultanea');
        /*Obtengo 2 nodos superiores al del objDivIntenetWifi.*/
        const objParentNode      = objDivInternetWifi.parentNode.parentNode;
        /*Defino una constante con el HTML que voy a agregar.*/
        const nuevoElemento      = `
                                <tr id="cantidad-servicios-wifi" class="animated fadeIn">
                                <td><label id="lb_cantidad-servicios-wifi"><span class="required-dot">*</span> Ingrese la cantidad de Servicios Wifi:</label></td>
                                <td>
                                    <div id="div_cantidad-servicios-wifi">
                                        <input
                                            style="width: 3rem;" type="text" id="input_cantidad-servicios-wifi"
                                            value="1">
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                                </tr>
                                `;
        /*Agrego el elemento HTML al final del nodo objParentNode.*/
        objParentNode.insertAdjacentHTML('afterend', nuevoElemento);

    }else {
        /*Obtengo el nodo del objeto mediente su ID.*/
        const elementoRemovible = document.querySelector('#cantidad-servicios-wifi'); //document.getElementById("cantidad-servicios-wifi");
        /*Remuevo la clase que le da la animación de entrada.*/
        elementoRemovible.classList.remove('fadeIn');
        /*Agrego una clase que le da animación de salida.*/
        elementoRemovible.classList.add('fadeOut');
        /*Agrego un listener, que hará que cuando termine la animación, se elimine el objeto del DOM.*/
        elementoRemovible.addEventListener('animationend', function() {
            elementoRemovible.remove();
        });
    }

}

/*
* Funcion que permite controlar el funcionamiento del checkbox de "Requiere Inspección.".
*
* @author Pablo Pin <ppin@telconet.ec>
* @version 1.0 14-08-2019 | Versión Inicial.
*
*/

function requiereInstalacionCheckboxHandler(cb) {

    const objCheckbox = document.getElementById(cb.id);
    objCheckbox.value = cb.checked ? 'S' : 'N';

}

function validaNumerosConDecimales(e, field) 
{
    var key = e.keyCode ? e.keyCode : e.which;

    if (key == 8) return true;

    if (key > 47 && key < 58)
    {
        if (field.value == "") return true;
        
        var existePto = (/[.]/).test(field.value);
        if (existePto === false)
        {
            regexp = /.[0-9]{10}$/;
        }
        else 
        {
            regexp = /.[0-9]{2}$/;
        }
        
        return !(regexp.test(field.value));
    }

    if (key == 46)
    {
        if (field.value == "") return false;
        var regexp = /^[0-9]+$/;
        return regexp.test(field.value);
    }

    return false;
}

function getTipoNegocio()
{
    Ext.Ajax.request({
        url: url_tipoNegocioActual,
        method: 'post',
        async: false,
        timeout: 400000,
        params: {idPunto: idPunto},
        success: function(response) {
            var text = Ext.decode(response.responseText);
            $('#nombreTipoNegocio').text(text.tipoNegocioActual);
        },
        failure: function(result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}
function getCambioNegocio() {
     console.log(idPunto);
    //caso contrario levantara la ventana que muestra el combo de los tipos negocios.
    winCambioTipoNegocio = "";
    //valida si la ventana no ha sido levantada.
    if (!winCambioTipoNegocio) {

        /*Incio: Combo Tipos de Negocios*/
        Ext.define('modelTipoNegocio', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idTipoNegocio', type: 'string'},
                {name: 'descripcion', type: 'string'}
            ]
        });
        var estado_store = Ext.create('Ext.data.Store', {
            autoLoad: false,
            model: "modelTipoNegocio",
            proxy: {
                type: 'ajax',
                url: url_lista_tiposNegocios,
                reader: {
                    type: 'json',
                    root: 'tiposNegocio'
                }
            }
        });
        var tipoNegocio_cmb = new Ext.form.ComboBox({
            id: 'idTipoNegocio',
            name: 'idTipoNegocio',
            fieldLabel: 'Tipo de Negocio',
            emptyText: '',
            store: estado_store,
            displayField: 'descripcion',
            valueField: 'idTipoNegocio',
            height: 30,
            width: 325,
            border: 0,
            margin: 0,
            queryMode: "remote"
        });
        /*Fin: Combo Tipos de Negocios*/
        var formTipoNegocio = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,
            fieldDefaults: {
                //labelAlign: 'top',
                labelWidth: 130,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0'
            },
            items: [
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Tipo Negocio Actual',
                    id: 'tipoNegocioActual',
                    name: 'tipoNegocioActual'
                },
                tipoNegocio_cmb
            ],
            buttons: [{
                    text: 'Grabar',
                    name: 'grabar',
                    handler: function() {
                        /* valida que la variable idTipoNegocio y idPunto no sean nulas. 
                            * Por verdadero procedera a enviar los parametros al contralador que se encarga de atualizar
                            * el tipo de negocio.
                            */
                        if (Ext.getCmp('idTipoNegocio').value != null && idPunto != null) {
                            Ext.Ajax.request({
                                url: url_cambiaTipoNegocio,
                                method: 'post',
                                params: {idTipoNegocio: Ext.getCmp('idTipoNegocio').value, idPunto: idPunto},
                                success: function(response) {
                                    var text = Ext.decode(response.responseText);
                                    /* Valida si la variable succes que es devuelta por
                                        * el controlador tiene como valor true o false
                                        * Si es true presenta el mensaje enviado desde el controlador en la variable msg.
                                        */
                                    if (text.succes == true) {
                                        Ext.Msg.alert('Success', text.msg);
                                        window.location.reload(true);
                                    } else {
                                        /* Caso contrario devuelve el mensaje
                                            * de error devuelta en la variable msg
                                            */
                                        Ext.Msg.alert('Alert', 'No se Realizo el cambio de Tipo de Negocio - ' + text.msg);
                                    }
                                },
                                failure: function(result) {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                            this.up('window').destroy();
                            winCambioTipoNegocio.close();
                        } else {
                            /*Si el idTipoNegocio es null mostrara el siguiente mensaje*/
                            Ext.Msg.alert('Alert', 'Debe seleccionar un Tipo de Negocio.');
                        }
                    }
                },
                {
                    text: 'Cancel',
                    handler: function() {
                        this.up('form').getForm().reset();
                        this.up('window').destroy();
                    }
                }]
        });

        Ext.Ajax.request({
            url: url_tipoNegocioActual,
            method: 'post',
            params: {idPunto: idPunto},
            success: function(response) {
                var text2 = Ext.decode(response.responseText);
                tipoNegocioActual = text2.tipoNegocioActual;
                formTipoNegocio.getForm().findField('tipoNegocioActual').setValue(text2.tipoNegocioActual);
                $('#nombreTipoNegocio').text(text2.tipoNegocioActual);
            },
            failure: function(result) {
                Ext.Msg.alert('Error ', 'Errorrrr: ' + result.statusText);
            }
        });

        winCambioTipoNegocio = Ext.widget('window', {
            title: 'Cambio de Tipo de Negocio',
            closeAction: 'hide',
            closable: false,
            width: 380,
            height: 200,
            minHeight: 150,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: formTipoNegocio
        });

    }
    winCambioTipoNegocio.show();
}

function eliminarServicioGrid(data)
{

}

function getPuntosMdAsociados(idCampoPuntoMdAsociado)
{
    storePuntosMdAsociados = new Ext.data.Store
    ({
        total: 'intTotal',
        pageSize: 5,
        proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetPuntosMdAsociados,
                timeout: 600000,
                reader:
                    {
                        type: 'json',
                        totalProperty: 'intTotal',
                        root: 'arrayResultado'
                    }
            },
        fields:
            [
                {name: 'idServicioPuntoMdAsociado', mapping: 'idServicioPuntoMdAsociado'},
                {name: 'idPuntoMdAsociado', mapping: 'idPuntoMdAsociado'},
                {name: 'loginPuntoMdAsociado', mapping: 'loginPuntoMdAsociado'},
                {name: 'nombrePlanPuntoMdAsociado', mapping: 'nombrePlanPuntoMdAsociado'},
                {name: 'nombreTipoNegocioPuntoMdAsociado', mapping: 'nombreTipoNegocioPuntoMdAsociado'},
                {name: 'estadoServicioPuntoMdAsociado', mapping: 'estadoServicioPuntoMdAsociado'},
                {name: 'saldoTotalClientePuntoMdAsociado', mapping: 'saldoTotalClientePuntoMdAsociado'},
                {name: 'omiteDeudaClientePuntoMdAsociado', mapping: 'omiteDeudaClientePuntoMdAsociado'},
                {name: 'tieneServiciosAdicionalesPuntoMdAsociado', mapping: 'tieneServiciosAdicionalesPuntoMdAsociado'},
                {name: 'tipoNegocioPermitidoPuntoMdAsociado', mapping: 'tipoNegocioPermitidoPuntoMdAsociado'}
            ]
    });
    
    gridPuntosMdAsociados = Ext.create('Ext.grid.Panel',
    {
        id: 'gridPuntosMdAsociados',
        width: '100%',
        height: 200,
        store: storePuntosMdAsociados,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'idServicioPuntoMdAsociado',
                header: 'idServicioPuntoMdAsociado',
                dataIndex: 'idServicioPuntoMdAsociado',
                hidden: true,
                hideable: false
            },
            {
                id: 'idPuntoMdAsociado',
                header: 'idPuntoMdAsociado',
                dataIndex: 'idPuntoMdAsociado',
                hidden: true,
                hideable: false
            },
            {
                id: 'tipoNegocioPermitidoPuntoMdAsociado',
                header: 'tipoNegocioPermitidoPuntoMdAsociado',
                dataIndex: 'tipoNegocioPermitidoPuntoMdAsociado',
                hidden: true,
                hideable: false
            },
            {
                header: 'Login',
                dataIndex: 'loginPuntoMdAsociado',
                width: '20%',
                sortable: true,
                name: 'loginPuntoMdAsociado',
                id: 'loginPuntoMdAsociado'
            },
            {
                header: 'Plan Internet',
                dataIndex: 'nombrePlanPuntoMdAsociado',
                width: '20%',
                sortable: true,
                name: 'nombrePlanPuntoMdAsociado',
                id: 'nombrePlanPuntoMdAsociado'
            },
            {
                header: 'Tipo de Negocio',
                dataIndex: 'nombreTipoNegocioPuntoMdAsociado',
                width: '15%',
                sortable: true,
                name: 'nombreTipoNegocioPuntoMdAsociado',
                id: 'nombreTipoNegocioPuntoMdAsociado'
            },
            {
                header: 'Saldo Cliente',
                dataIndex: 'saldoTotalClientePuntoMdAsociado',
                width: '10%',
                sortable: true,
                name: 'saldoTotalClientePuntoMdAsociado',
                id: 'saldoTotalClientePuntoMdAsociado'
            },
            {
                header: 'Omite Deuda',
                dataIndex: 'omiteDeudaClientePuntoMdAsociado',
                width: '14%',
                sortable: true,
                name: 'omiteDeudaClientePuntoMdAsociado',
                id: 'omiteDeudaClientePuntoMdAsociado'
            },
            {
                header: 'Adicionales',
                dataIndex: 'tieneServiciosAdicionalesPuntoMdAsociado',
                width: '10%',
                sortable: true,
                name: 'tieneServiciosAdicionalesPuntoMdAsociado',
                id: 'tieneServiciosAdicionalesPuntoMdAsociado'
            },
            {
                header: 'Estado del Servicio',
                dataIndex: 'estadoServicioPuntoMdAsociado',
                width: '15%',
                sortable: true,
                name: 'estadoServicioPuntoMdAsociado',
                id: 'estadoServicioPuntoMdAsociado'
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storePuntosMdAsociados,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        listeners: 
        {
            itemdblclick:
            {
                fn: function( view, rec, node, index, e, options )
                {
                    var loginSeleccionado                           = rec.data.loginPuntoMdAsociado;
                    var saldoClienteLoginSeleccionado               = rec.data.saldoTotalClientePuntoMdAsociado;
                    var omiteDeudaClienteLoginSeleccionado          = rec.data.omiteDeudaClientePuntoMdAsociado;
                    var tieneServiciosAdicionalesLoginSeleccionado  = rec.data.tieneServiciosAdicionalesPuntoMdAsociado;
                    var tipoNegocioPermitidoLoginSeleccionado       = rec.data.tipoNegocioPermitidoPuntoMdAsociado;
                    
                    if(tipoNegocioPermitidoLoginSeleccionado == "NO")
                    {
                        Ext.Msg.alert('Atenci\xf3n', 'El login no puede ser seleccionado ya que su tipo de negocio no es permitido.');
                        return false;
                    }
                    
                    if(saldoClienteLoginSeleccionado > 0 && omiteDeudaClienteLoginSeleccionado === "NO")
                    {
                        Ext.Msg.alert('Atenci\xf3n', 'El login no puede ser seleccionado ya que el cliente tiene una deuda.');
                        return false;
                    }
                        
                    if(tieneServiciosAdicionalesLoginSeleccionado == "SI")
                    {
                        Ext.Msg.alert('Atenci\xf3n', 'El login no puede ser seleccionado ya que tiene servicios adicionales.');
                        return false;
                    }
                    
                    document.getElementById(""+idCampoPuntoMdAsociado).value = loginSeleccionado;
                    if( typeof winPuntosMdAsociados != 'undefined' && winPuntosMdAsociados != null )
                    {
                        winPuntosMdAsociados.destroy();
                    }
                    actualizaDescripcion(document.getElementById("strNombreTecnico").value);
                }
            }
        }
    });


    filterPanelPuntosMdAsociados = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout:{
                type:'table',
                columns: 5,
                align: 'left'
        },
        bodyStyle: {
                background: '#fff'
        },                     
        defaults: {
                bodyStyle: 'padding:10px'
        },
        collapsible : true,
        collapsed: true,
        width: '100%',
        title: 'Criterios de búsqueda',
        buttons: [                   
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function()
                { 
                    buscarPuntosMdAsociados();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                { 
                    limpiarPuntosMdAsociados();
                }
            }
        ],                
        items: [
            { width: '5%',border:false},
            {
                xtype: 'textfield',
                id: 'txtCedulaClientePuntoMdAsociado',
                fieldLabel: 'Cédula Cliente',
                value: '',
                width: '35%',
                labelStyle: "margin:0;"
            },
            { width: '15%',border:false},
            {
                xtype: 'textfield',
                id: 'txtLoginPuntoMdAsociado',
                fieldLabel: 'Login Punto',
                value: '',
                width: '35%',
                labelStyle: "margin:0;"
            },
            { width: '5%',border:false}
        ]
    });



    var formPanelPuntosMdAsociados = Ext.create('Ext.form.Panel',
    {
        bodyPadding: 5,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            msgTarget: 'side'
        },
        items:[
            {
                xtype: 'fieldset',
                title: 'Puntos MD',
                defaultType: 'textfield',
                width: '100%',
                items: [
                    filterPanelPuntosMdAsociados,
                    gridPuntosMdAsociados
                ]
            }
        ]
    });
    
    winPuntosMdAsociados = Ext.create('Ext.window.Window',
    {
        title: 'Selección de Punto MD',
        modal: true,
        width: 800,
        closable: true,
        layout: 'fit',
        items: [formPanelPuntosMdAsociados]
    }).show();
    
}

function buscarPuntosMdAsociados()
{
    if(Ext.getCmp('txtCedulaClientePuntoMdAsociado').value !="" || Ext.getCmp('txtLoginPuntoMdAsociado').value !="")
    {
        storePuntosMdAsociados.loadData([],false);
        storePuntosMdAsociados.currentPage = 1;
        storePuntosMdAsociados.getProxy().extraParams.cedulaCliente   = Ext.getCmp('txtCedulaClientePuntoMdAsociado').value;
        storePuntosMdAsociados.getProxy().extraParams.loginPunto      = Ext.getCmp('txtLoginPuntoMdAsociado').value;
        storePuntosMdAsociados.load({params: {start: 0, limit: 5}});
    }
    else
    {
        Ext.Msg.show({
                title:'Error en Búsqueda',
                msg: 'Por favor ingrese la cédula del cliente o el login del punto para realizar la búsqueda',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
    }
}

function limpiarPuntosMdAsociados()
{
    Ext.getCmp('txtCedulaClientePuntoMdAsociado').value   = "";
    Ext.getCmp('txtCedulaClientePuntoMdAsociado').setRawValue("");

    Ext.getCmp('txtLoginPuntoMdAsociado').value = "";
    Ext.getCmp('txtLoginPuntoMdAsociado').setRawValue("");

    storePuntosMdAsociados.currentPage = 1;
    storePuntosMdAsociados.getProxy().extraParams.cedulaCliente = Ext.getCmp('txtCedulaClientePuntoMdAsociado').value;  
    storePuntosMdAsociados.getProxy().extraParams.loginPunto    = Ext.getCmp('txtLoginPuntoMdAsociado').value;
    storePuntosMdAsociados.load();
}

/**
 * Realiza una verificación que exista data en la caja de texto para el ingreso de códigos por mensualidad.
 *    
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 10-12-2020
 * @since 1.0
 */
function controlValidaMix(){
    var strCodigoPromocion = document.getElementById('PROM_MPLA').value;
    var intCodigoPromo     = strCodigoPromocion.length;
    if (intCodigoPromo > 0) 
    {
        validaCodigo('PROM_MPLA');
    }
}

/**
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 04-12-2020 - Se valida los códigos promocionales ingresados por el Usuario, código existente, reglas
 * de la promoción a la que pertenece el código contra los datos del servicio seleccionado y datos del punto.
 */
function validaCodigo(tipoPromo) {
    var strTipoPromo  = tipoPromo;
    var strGrupoPromo;
    var intIdServicio = 0;
    var intIdPunto    = idPunto;
    var tipo          = $("input:radio[@name='info']:checked").val();
    var intIdPlan     = Number("");
    var intIdProducto = Number("");
    if (tipo == "catalogo")
    {
        intIdProducto = Number(document.getElementById("producto").value.split("-")[0]);
    }
    else
    {
        intIdPlan     = Number(document.getElementById("planes").value.split("-")[0]);
    }
    
    if (strTipoPromo === 'PROM_BW')
    {
        strGrupoPromo = 'PROM_BW';
    }
    
    if (strTipoPromo === 'PROM_INS')
    {
        strGrupoPromo = 'PROM_INS';
    }
    
    var strTipoProceso = 'NUEVO';
    var strCodigo      = document.getElementById(""+strTipoPromo).value;
    var intIdUltimaMilla;
    if (strTipoPromo === 'PROM_BW' || strTipoPromo === 'PROM_INS')
    {
        if (tipo == "catalogo")
        {
            intIdUltimaMilla = document.getElementById("ultimaMillaIdProd").value;
        }
        else
        {
            intIdUltimaMilla = document.getElementById("ultimaMillaId").value;
        }

        if (intIdUltimaMilla === "0")
        {
            mostrarAlertaFormulario('Última Milla', tipo === "catalogo" ? Ext.get('ultimaMillaIdProd') : Ext.get('ultimaMillaId'));
            document.getElementById(""+strTipoPromo).value = "";
            return false;
        }
    }
    strCodigo          = strCodigo.trim();
    strCodigo          = strCodigo.toUpperCase();
    document.getElementById(""+strTipoPromo).value = strCodigo;
    var intCodigoPromo = strCodigo.length;
    if (strTipoPromo === 'PROM_MPLA'){
        if ($('#checkPromoMix').prop('checked')) {
            strTipoPromo='PROM_MIX';
        }
    }
    if (strTipoPromo === 'PROM_MPLA' || strTipoPromo === 'PROM_MPRO' || strTipoPromo === 'PROM_MIX')
    {
        strGrupoPromo = 'PROM_MENS';
    }
    if (intCodigoPromo > 0) 
    {
        var parametros = {
            "strGrupoPromocion"  : strGrupoPromo,
            "strTipoPromocion"   : strTipoPromo,
            "strTipoProceso"     : strTipoProceso,
            "strCodigo"          : strCodigo,
            "intIdServicio"      : intIdServicio,
            "intIdPunto"         : intIdPunto,
            "intIdPlan"          : intIdPlan,
            "intIdProducto"      : intIdProducto,
            "intIdUltimaMilla"   : intIdUltimaMilla,
            "strEsContrato"      : "S"
        };

        $.ajax({
            type: "POST",
            data: parametros,
            url: urlValidaCodigoPromocion,
            success: function(msg)
            {
                if (msg.strAplica !== 'S')
                {
                    if (strTipoPromo === 'PROM_MIX')
                    {
                        strTipoPromo = 'PROM_MPLA';
                    }
                    mostrarAlertaCodigo(msg.strMensaje,strTipoPromo);
                    if (strGrupoPromo === 'PROM_MENS'){
                        strCodigoPromocion = "";
                        strNombrePromocion = "";
                        strTipoPromocion   = "";
                        strServiciosMix    = "";
                    }
                    if (strGrupoPromo === 'PROM_INS'){
                        strCodigoPromocionIns = "";
                        strNombrePromocionIns = "";
                        strTipoPromocionIns   = "";
                    }
                    if (strGrupoPromo === 'PROM_BW'){
                        strCodigoPromocionBw = "";
                        strNombrePromocionBw = "";
                        strTipoPromocionBw   = "";
                    }
                }
                else
                {
                    Ext.Msg.alert("Advertencia", `<b>${msg.strMensaje}</b>`);
                    if (msg.strOltEdificio !== '')
                    {
                        Ext.Msg.confirm('Alerta', msg.strMensaje +' '+`<b>${msg.strOltEdificio}</b>` + ' ¿Desea continuar?', function(btn)
                        {
                            if( btn == 'yes' )
                            {
                                if (strGrupoPromo === 'PROM_MENS'){
                                    strCodigoPromocion = strCodigo;
                                    strNombrePromocion = msg.strNombrePromocion;
                                    strTipoPromocion   = msg.strIdTipoPromocion;
                                    strServiciosMix    = msg.strServiciosMix;
                                }
                                if (strGrupoPromo === 'PROM_INS'){
                                    strCodigoPromocionIns = strCodigo;
                                    strNombrePromocionIns = msg.strNombrePromocion;
                                    strTipoPromocionIns   = msg.strIdTipoPromocion;
                                }
                                if (strGrupoPromo === 'PROM_BW'){
                                    strCodigoPromocionBw = strCodigo;
                                    strNombrePromocionBw = msg.strNombrePromocion;
                                    strTipoPromocionBw   = msg.strIdTipoPromocion;
                                }
                            }
                            else
                            {
                                if (strGrupoPromo === 'PROM_MENS'){
                                    document.getElementById("PROM_MPLA").value = "";
                                    strCodigoPromocion = "";
                                    strNombrePromocion = "";
                                    strTipoPromocion   = "";
                                    strServiciosMix    = "";
                                }
                                if (strGrupoPromo === 'PROM_INS'){
                                    document.getElementById("PROM_INS").value = "";
                                    strCodigoPromocionIns = "";
                                    strNombrePromocionIns = "";
                                    strTipoPromocionIns   = "";
                                }
                                if (strGrupoPromo === 'PROM_BW'){
                                    document.getElementById("PROM_BW").value = "";
                                    strCodigoPromocionBw = "";
                                    strNombrePromocionBw = "";
                                    strTipoPromocionBw   = "";
                                }
                            }
                        });
                    }
                    else
                    {
                        if (strGrupoPromo === 'PROM_MENS'){
                            strCodigoPromocion = strCodigo;
                            strNombrePromocion = msg.strNombrePromocion;
                            strTipoPromocion   = msg.strIdTipoPromocion;
                            strServiciosMix    = msg.strServiciosMix;
                        }
                        if (strGrupoPromo === 'PROM_INS'){
                            strCodigoPromocionIns = strCodigo;
                            strNombrePromocionIns = msg.strNombrePromocion;
                            strTipoPromocionIns   = msg.strIdTipoPromocion;
                        }
                        if (strGrupoPromo === 'PROM_BW'){
                            strCodigoPromocionBw = strCodigo;
                            strNombrePromocionBw = msg.strNombrePromocion;
                            strTipoPromocionBw   = msg.strIdTipoPromocion;
                        }
                    }
                }
            },
            failure: function()
            {
                mostrarAlertaCodigo('Ocurrió un error.',strTipoPromo);
                if (strGrupoPromo === 'PROM_MENS'){
                    strCodigoPromocion = "";
                    strNombrePromocion = "";
                    strTipoPromocion   = "";
                    strServiciosMix    = "";
                }
                if (strGrupoPromo === 'PROM_INS'){
                    strCodigoPromocionIns = "";
                    strNombrePromocionIns = "";
                    strTipoPromocionIns   = "";
                }
                if (strGrupoPromo === 'PROM_BW'){
                    strCodigoPromocionBw = "";
                    strNombrePromocionBw = "";
                    strTipoPromocionBw   = "";
                }
            }
        });
    }
    else
    {
        if (strGrupoPromo === 'PROM_MENS'){
            strCodigoPromocion = "";
            strNombrePromocion = "";
            strTipoPromocion   = "";
            strServiciosMix    = "";
        }
        if (strGrupoPromo === 'PROM_INS'){
            strCodigoPromocionIns = "";
            strNombrePromocionIns = "";
            strTipoPromocionIns   = "";
        }
        if (strGrupoPromo === 'PROM_BW'){
            strCodigoPromocionBw = "";
            strNombrePromocionBw = "";
            strTipoPromocionBw   = "";
        }
    }
}

/**
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 04-12-2020 - Función para controlar los mensajes de ayuda para el Usuario presentados por pantalla.
 */
function mostrarAlertaCodigo(strMensaje, strTipoPromo) {
    Ext.Msg.alert("Advertencia", `<b>${strMensaje}</b>`, function (btn) {
        document.getElementById(""+strTipoPromo).value = "";
    });
}

/**
 * @author Felix Caicedo <facaicedo@telconet.ec>
 * @version 1.0 21-06-2021 - Función que sirve para actualizar las capacidades.
 */
function actualizaCapacidadGpon(value) 
{
    $(".update_capacidades_gpon").val(value);
    actualizaDescripcion(value);
}

/**
 * @author Felix Caicedo <facaicedo@telconet.ec>
 * @version 1.0 20-09-2021 - Función que sirve para mostrar u ocultar las características adicionales
 */
function verificarProdCaractRelacion(object)
{
    if(object.value != "" && object.value != 0){
        $(".td_relacion_"+object.id).show();
        $(".relacion_"+object.id).prop("disabled",false);
    } else {
        $(".td_relacion_"+object.id).hide();
        $(".relacion_"+object.id).prop("disabled",true);
        if($(".relacion_"+object.id).val().length < 1){
            $(".relacion_"+object.id).val("0");
        }
    }
}

/**
 * @author Felix Caicedo <facaicedo@telconet.ec>
 * @version 1.0 20-09-2021 - Función que sirve para mostrar mensaje de alerta en la pantalla y señala los campos faltantes.
 */
function mostrarMensajeAlertaFormulario(strMensaje, strNameObject) {
    Ext.Msg.alert("Advertencia",strMensaje, function (btn) {
        if (btn == 'ok'){
            let objCaracteristica = $(strNameObject);
            objCaracteristica.addClass('animated tada');
            objCaracteristica.focus();
            objCaracteristica.css({"border-color": "red"});
            setTimeout(
                function () {
                    objCaracteristica.removeClass('animated tada');
                },
                2000
            );
            objCaracteristica.on("click", function() {
                $(this).css({"border-color": ""});
            });
            objCaracteristica.on("change", function() {
                $(this).css({"border-color": ""});
            });
        }
    });
}

/*
* Funcion que permite controlar el funcionamiento del checkbox de "Es para Migración.".
*
* @author Antonio Ayala <afayala@telconet.ec>
* @version 1.0 02-08-2021 | Versión Inicial.
*
*/

function requiereMigracion(cb) {

    const objCheckbox = document.getElementById(cb.id);
    objCheckbox.value = cb.checked ? 'S' : 'N';

}

/*
* Funcion que permite validar producto por medio del microservico ms-comp-cliente.
*
* @author Alex Arreaga <atarreaga@telconet.ec>
* @version 1.0 08-12-2022 | Versión Inicial.
*
*/
function validaPorProductoAdicionalMs(intIdPersona, intIdPunto) { 
    var strStatus  = '';
    var strMensaje = ''; 
    
    $.ajax({
        url: stUrlValidaProdAdicionalMs,
        method: 'get',
        async: false,
        data: {'intIdPersona':intIdPersona,'intIdPunto':intIdPunto},
        success: function (data) {
            strStatus = data.strStatus;
            
            if(strStatus !== 'OK')
            {
                strMensaje = data.strMensaje; 
            }
        },
        error: function () {
            Ext.Msg.alert('Alert', 'Error: No se pudo realizar validación por servicio producto adicional. '
                                   +'Consulte con el Administrador del Sistema'); 
        }
    });

    return strMensaje;
}

/*
* Funcion que permite controlar el valor máximo de productos Konibit en la acción de agregar.
*
* @author José Candelario <jcandelario@telconet.ec>
* @version 1.0 02-08-2022 | Versión Inicial.
*
*/
function cantMaxKonibit() 
{
    max = $('#cantidad').attr('max');

    if(typeof formulario.cantidad == 'undefined')
    {
        cantidad  = $("#cantidad").val();
    }
    else
    {
        cantidad = formulario.cantidad.value;  
    }
    
    if((cantidad>max)){
        Ext.Msg.alert('Atenci\xf3n', 'Sobrepasa el valor máximo('+max+') de productos Konibit.');
        $("#cantidad").val(1);
        return false;
    }
}

/*
function validarProductoAdicional()
{
    var cantidad_detalle = 0;
    var tipo             = $("input:radio[@name='info']:checked").val();
    var plan             = Number(document.getElementById("planes").value.split("-")[0]);
    var producto         = Number(document.getElementById("producto").value.split("-")[0]);
    
    if( tipo == "portafolio" )
    {
         cantidad_detalle = Number(document.getElementById("cantidad_plan").value);
    }
    else
    {
         cantidad_detalle = Number(document.getElementById("cantidad").value);
    }
    
    var cantidad_total_ingresada = Number(document.getElementById("cantidad_total_ingresada").value);    
     $.ajax({
        type: "POST",
        data: "tipo=" + tipo + "&planId=" + plan + "&productoId=" + producto + "&cantidad_detalle=" + cantidad_detalle + "&cantidad_total_ingresada=" + cantidad_total_ingresada,
        url: valida_producto_adicional,
        beforeSend: function()
        {
            Ext.get('gridServicios').mask('Cargando Datos del Servicio');
        },
        success: function(msg)
        {
            if (msg.msg != 'Ok')
            {
                $('#mensaje_validaciones').removeClass('campo-oculto').html(msg.msg);
                Ext.get('gridServicios').unmask();
                validaAdicional = false;
            }
            else
            {
                validaAdicional = true;
            }
        },
        failure: function()
        {
            Ext.get('gridServicios').unmask();
        }
    }); 
      
}*/
/**
 *  Funcion que permite renderizar el input para la subida de los archivos
 * 
 * @author Leonardo Mero <lemero@telconet.ec>
 * @version 1.0 09-12-2022 - Version inicial
 */

function renderFileComponent(nombre, esRequerido)
{
    Ext.create('Ext.form.Panel', {
        width: 400,
        bodyPadding: 10,
        frame: true,
        renderTo: nombre,    
        items: [{
            xtype: 'filefield',
            name: nombre+'_file',
            id: nombre+'_file',
            labelWidth: 50,
            msgTarget: 'side',
            allowBlank: esRequerido === 'S' ? false : true,
            required: esRequerido === 'S' ? false : true,
            anchor: '100%',
            buttonText: 'Selecionar archivo'
        }]
    });
}

/**
 *  Funcion que renderiza el contenedor donde se muestra el Label del archivo requerido y el widget de subida de archivos
 * 
 * @param String Nombre de la caracteristica que requiere la subida de archivos
 * 
 * @author Leonardo Mero <lemero@telconet.ec>
 * @version 1.0 09-12-2022- Version inicial
 */
function mostarContenedorArchivos(nombre, etiqueta, esRequerido)
{
   $('#contenedor_archivos').show()

   $('#contenedor_archivos').append('<div style=" paddig-bottom:10px" >'+
        '<div id='+etiqueta+' name='+etiqueta+' class="contenedor-archivos-input"> '+
            '<label style="word-break: break-word; white-space: initial; width:155px; margin:10px 10px 0px 0px">'+
            nombre+' :</label >'+
        '</div>'+
   '</div></br>');
    renderFileComponent(etiqueta,esRequerido)
}
/**
 *  Funcion que permite validar que los archivos requeridos se encuentren cargados
 * 
 * @author Leonardo Mero <lemero@telconet.ec>
 * @version 1.0 09-12-2022- Version inicial
 */

function validarArchivosSafeEntry()
{
    var boolValidacion = true
    $.ajax({
        type: "POST",
        url:  urlGetArchivosReqSafeEntry,
        async: false,
        success: function(response)
        {
            Object.entries(response).forEach(([key, value]) => {
                if(value[1] === 'S')
                {
                    if(!$(`#${value[2]}_file-fileInputEl`).val())
                    {
                        mostrarAlertaFormulario(value[0], Ext.get(`${value[2]}_file-fileInputEl`));
                        Ext.get('gridServicios').unmask();
                        return boolValidacion = false;
                    }
                }
            });
        }
    });
    return boolValidacion
}