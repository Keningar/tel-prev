
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 10;
var store = '';
var estado_id = '';
var area_id = '';
var login_id = '';
var tipo_asignacion = '';
var pto_sucursal = '';
var idClienteSucursalSesion;

$(document).ready(function () {

    /**
    * Obtiene las formas de pago
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 08-09-2020
    */   
    $('#tabla_lista_pago_automatico_det').DataTable({ 
        "ajax": {
            "url": urlGridDetalleRetencion,
            "type": 'POST',
            "data": function (param) {
                param.intIdPagoAutomatico = $('#intIdPagoAutomatico').val()
            }
        },
        "scrollY":"300px",
        "scrollX":true,
        "scrollCollapse": true,        
        "searching":true,
        "ordering":true,
        "order": [[ 0, "asc" ]],
        "language": {
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "Cargando datos...",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "loadingRecords": "Cargando datos..."
        },        
        "rowCallback": function( row, data, index ) 
        {
            if(data.strEstado == 'Procesado')
            {
                $('td', row).css('background-color', 'grey');
                $('select', row).css('background-color', 'grey');
                $('input', row).css('background-color', 'grey');
            }
            if(data.strEstado == 'Error')
            {
                $('td', row).css('background-color', 'yellow');
                $('select', row).css('background-color', 'yellow');
                $('input', row).css('background-color', 'yellow');
            }             
        },     
        "columns": [
            {"data": "intIdPagoAutDet","visible": false},
            {"data": "strFecha","width": "5%"},
            {"data": "strLogin","width": "7%"},
            {"data": "strFactura","width": "10%"},
            {"data": "strSaldo","width": "5%"},
            {"data": "strBaseImponibleCal","width": "5%"},            
            {"data": "strBaseImponible","width": "5%"},
            {"data": "strBaseImponibleIva","width": "5%"},
            {"data": "strFormaPago","width": "10%"},
            {"data": "strPorcentajeRetencion","width": "5%"},
            {"data": "strValor","width": "5%"}, 
            {"data": "strEstado","width": "5%"},            
            {"data": "strAcciones","width": "10%",
                "render": function (data){
                    var strDatoRetorna = '';
                    if(data.strEstado!=='Eliminado')
                    {               
                        strDatoRetorna += '<a class="btn btn-outline-dark btn-sm verFact"  title="Ver Factura" ' +                            
                            ' href="' + data.linkVer + '" onClick="">' + '<i class="fa fa-search"></i>' +
                            '</a>&nbsp;';
                    }
                    
                    strDatoRetorna += '<a class="btn btn-outline-dark btn-sm verHistorial" data-toggle="modal" title="Ver Historial" ' +                            
                        ' data-id="' + data.intIdPagoAutDet + '">' + '<i class="fa fa-sticky-note-o"></i>' +
                        '</a>&nbsp;';
                
                    return strDatoRetorna;          
                }
            }
        ],

    });
    $("#buscar_pag_aut_det").click(function () {
        $('#tabla_lista_pago_automatico_det').DataTable().ajax.reload();
    }); 
    $(document).on( 'click','.verFactura', function () {
         //$("#idPagoAutomatico").val($(this).data("id"));
        // $('#modalEliminarRetencion').modal('show');
        $.ajax({
            url: urlShowFactura,
            method: 'POST',
            data: {intIdPagoAutDet: $(this).data("id")},
            success: function (data) {

            },
            error: function () {
                $('#modalMensajes .modal-body').html("Error al consultar datos de la factura. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });        
        
    });
    
    $(document).on( 'click','.verHistorial', function () {
        var idRetencion = $(this).data("id");
        verHistorialRetención(idRetencion);
            
        
    });    
    
    
    function verHistorialRetención(idRetencion)
    {  
        var dataStoreHistorial = new Ext.data.Store
        ({
            autoLoad: true,
            total: 'total',
            proxy:
            {
                type: 'ajax',
                timeout: 600000,
                url: urlHistorialRetencion,
                extraParams: {				
                    intIdPagoAutDet: idRetencion
                },                
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'registros'
                }
            },
            fields:
            [
                {name: 'detalle', mapping: 'detalle', type: 'string'},
                {name: 'motivo', mapping: 'motivo', type: 'string'},
                {name: 'estado', mapping: 'estado', type: 'string'},
                {name: 'usuario', mapping: 'usuario', type: 'string'},
                {name: 'fecha', mapping: 'fecha', type: 'string'}
            ]
        });

        var gridHistorial = Ext.create('Ext.grid.Panel',
        {
            id: 'gridHistorial',
            store: dataStoreHistorial,
            width: 790,
            height: 300,
            collapsible: false,
            multiSelect: true,
            viewConfig: 
            {
                emptyText: '<br><center><b>No hay datos para mostrar',
                forceFit: true,
                stripeRows: true,
                enableTextSelection: true
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
                        target: view.el,
                        delegate: '.x-grid-cell',
                        trackMouse: true,
                        autoHide: false,
                        renderTo: Ext.getBody(),
                        listeners:
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
                                            columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                        if( view.getRecord(parent).get(columnDataIndex) != null )
                                        {
                                            var columnText      = view.getRecord(parent).get(columnDataIndex).toString();

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
                                    else
                                    {
                                        return false;
                                    }
                                }     
                            }
                        }
                    });

                    grid.tip.on('show', function()
                    {
                        var timeout;

                        grid.tip.getEl().on('mouseout', function()
                        {
                            timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                        });

                        grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                        Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                        Ext.get(view.el).on('mouseout', function()
                        {
                            timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                        });
                    });
                }
            },
            layout: 'fit',
            region: 'center',
            buttons:
            [
                {
                    text: 'Cerrar',
                    handler: function()
                    {
                        win.destroy();
                    }
                }
            ],
            columns:
            [
                {
                    dataIndex: 'detalle',
                    header: 'Observaci\xf3n',
                    width: 300
                }, 
                {
                    dataIndex: 'motivo',
                    header: 'Motivo',
                    width: 150
                },                  
                {
                    dataIndex: 'estado',
                    header: 'Estado',
                    width: 70
                },
                {
                    dataIndex: 'usuario',
                    header: 'Usuario',
                    width: 100
                },
                {
                    dataIndex: 'fecha',
                    header: 'Fecha',
                    width: 150
                }
            ]
        });

        Ext.create('Ext.form.Panel',
        {
            id: 'formHistorialPunto',
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
                    defaults:{ width: 700 },
                    layout:
                    {
                        type: 'table',
                        columns: 4,
                        align: 'left'
                    },
                    items:[ gridHistorial ]
                }
            ]
        });

        var win = Ext.create('Ext.window.Window',
        {
            title: 'Historial Retención',
            modal: true,
            width: 800,
            closable: true,
            layout: 'fit',
            items: [gridHistorial]
        }).show();
    }    
});


