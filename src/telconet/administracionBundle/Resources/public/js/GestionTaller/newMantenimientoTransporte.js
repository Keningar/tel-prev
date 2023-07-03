
var connEsperaAccion = new Ext.data.Connection
    ({
	listeners:
        {
            'beforerequest': 
            {
                fn: function (con, opt)
                {						
                    Ext.MessageBox.show
                    ({
                       msg: 'Grabando los datos, Por favor espere!!',
                       progressText: 'Saving...',
                       width:300,
                       wait:true,
                       waitConfig: {interval:200}
                    });
                },
                scope: this
            },
            'requestcomplete':
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': 
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
	}
    });

Ext.onReady(function()
{
    
    storeCategoriasMantenimientosTransporte = new Ext.data.JsonStore(
    {
        pageSize: 200,
        autoLoad: true,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : strUrlGetCategoriasTareasOTyMantenimientosTransporte,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name:'idParametroDet',         mapping:'idParametroDet'},
            {name:'valor1',                 mapping:'valor1'},
            {name:'valorTotalCategoria',    mapping:'valorTotalCategoria'}
            
        ],
        listeners: 
        {
            load: function(sender, node, records) 
            {
                Ext.each(records, function(record, index){});
            }
        }
    });
    
                    
    cellEditingValoresTotalesCategorias = Ext.create('Ext.grid.plugin.CellEditing', {
                            clicksToEdit: 1,
                            listeners: {
                                edit: function() {
                                    var sumaTotalValoresChange = 0;
                                    var boolAlertaNegativos    = false;
                                    for (var i = 0; i < gridValoresCategoriasMantenimientos.getStore().getCount(); i++)
                                    {
                                        if(gridValoresCategoriasMantenimientos.getStore().getAt(i).data.valorTotalCategoria>0)
                                        {}
                                        else if(gridValoresCategoriasMantenimientos.getStore().getAt(i).data.valorTotalCategoria<0)
                                        {
                                            gridValoresCategoriasMantenimientos.getStore().getAt(i).data.valorTotalCategoria=0;
                                            boolAlertaNegativos=true;
                                        }
                                        else
                                        {
                                            gridValoresCategoriasMantenimientos.getStore().getAt(i).data.valorTotalCategoria=0;
                                        }
                                        sumaTotalValoresChange+=gridValoresCategoriasMantenimientos.getStore().getAt(i).data.valorTotalCategoria;
                                    }
                                    
                                    if(boolAlertaNegativos)
                                    {
                                        Ext.Msg.alert('Error ', "Los valores en las categorías no pueden ser negativos.");
                                    }
                                    document.getElementById("sumaTotalValoresChange").value=sumaTotalValoresChange;
                                    
                                    gridValoresCategoriasMantenimientos.getView().refresh();
                                }
                            }
                        });
    
    gridValoresCategoriasMantenimientos = Ext.create('Ext.grid.Panel', {
        title:'Detalle de Valores Por Categorías del Mantenimiento', 
        width: '100%',
        height: 200,
        sortableColumns:false,
        store: storeCategoriasMantenimientosTransporte,
        viewConfig: {enableTextSelection: true, stripeRows: true},
        id:'gridValoresCategoriasMantenimientos',
        enableColumnResize :false,
        loadMask: true,
        frame:true,
        forceFit:true,
         plugins: [cellEditingValoresTotalesCategorias],
        listeners:{
                viewready: function (grid) {
                    var view = grid.view;

                    // record the current cellIndex
                    grid.mon(view, {
                        uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                            grid.cellIndex = cellIndex;
                            grid.recordIndex = recordIndex;
                        }
                    });

                    grid.tip = Ext.create('Ext.tip.ToolTip', {
                        target: view.el,
                        delegate: '.x-grid-cell',
                        trackMouse: true,
                        renderTo: Ext.getBody(),
                        listeners: {
                            beforeshow: function updateTipBody(tip) {
                                if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                    header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                    tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                }
                            }
                        }
                    });
                }  

        },
        columns: [
            {
              id: 'idParametroDet',
              header: 'idParametroDet',
              dataIndex: 'idParametroDet',
              hidden: true,
              hideable: false
            },
             {
              id: 'valor1',
              header: 'Categoría',
              dataIndex: 'valor1',
              width:180
            },
            {
              id: 'valorTotalCategoria',
              header: 'Valor Total',
              dataIndex: 'valorTotalCategoria',
              width:180,
              renderer:function(value, metaData, record, rowIndex,colIndex, store, view){
                    Ext.util.Format.numberRenderer(value,'0.000');
                    return value; 
              },
              editor:   {
                            xtype: 'numberfield'
                        }
            }

        ],    
        renderTo: 'detalle_categorias_mantenimiento_transporte'
    });
    
    var DTFechaDesde = new Ext.form.DateField({
            xtype: 'datefield',
            id: 'fechaInicio',
            name:'fechaInicio',
            fieldLabel: '<b>Fecha Inicio</b>',
            editable: false,
            format: 'd/m/Y',
            value:new Date(),
            emptyText: "Seleccione",
            labelWidth: 200,
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasMantenimiento(cmp);
                }
            }
     });

    var DTFechaHasta = new Ext.form.DateField({
        xtype: 'datefield',
        id: 'fechaFin',
        name:'fechaFin',
        editable: false,
        fieldLabel: '<b>Fecha Fin</b>',
        format: 'd/m/Y',
        value:new Date(),
        anchor:'100%',
        emptyText: "Seleccione",
        labelWidth: 150,
        listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasMantenimiento(cmp);
            }
        }
    });
    
    
    var formMantenimientoTransporte = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        height: '100%',
        renderTo: 'bloqueFormMantenimiento',
        border: false,
        margin: 0,
        fieldDefaults: {
           labelAlign: 'left',
           msgTarget: 'side'
        },
        defaults: {
           margins: '0 0 10 0'
        },
        items: [
           {
               xtype: 'fieldset',
               title: '',
               defaultType: 'textfield',
               width: '100%',
               height: '100%',
               margin: 0,
               padding: '10 10 0 10',
               border: false,
               items:
               [
                   {
                       xtype: 'fieldset',
                       title: 'Información del Mantenimiento',                       
                       width: '100%',
                       height: '100%',
                       margin: 0,
                       
                       items: 
                       [
                            {
                                layout: 'table',
                                border: false,
                                padding: '5 0',
                                items: 
                                [
                                    {
                                        width: 340,
                                        layout: 'form',
                                        border: false,
                                        labelWidth:200,
                                        items: 
                                        [
                                            DTFechaDesde
                                        ]
                                    },
                                    {
                                        width: 200,
                                        layout: 'form',
                                        border: false,
                                        items: 
                                        [
                                            {
                                                xtype: 'displayfield'
                                            },
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    },
                                    {
                                        width: 290,
                                        layout: 'form',
                                        border: false,
                                        labelWidth:200,
                                        items: 
                                        [
                                            DTFechaHasta
                                        ]
                                    }
                                ]
                            },
                            { 
                                fieldLabel: '<b>Valor Total</b>',
                                id: 'valorTotal',
                                name: 'valorTotal',
                                allowBlank: false,
                                labelWidth: 200,
                                xtype: 'numberfield',
                                renderer:function(value, metaData, record, rowIndex,colIndex, store, view){
                                    Ext.util.Format.numberRenderer(value,'0.000');
                                    return value; 
                                }
                            }
                            
                       ]
                   }

               ]
           }

        ]
    });
    
    var panelMultiupload = Ext.create('widget.multiupload',{ fileslist: [] });
    formPanelArchivos = Ext.create('Ext.form.Panel',
    {
       width: 500,
       frame: true,
       bodyPadding: '10 10 0',
       renderTo: "div_archivos_subir",
       defaults: {
           anchor: '100%',
           allowBlank: false,
           msgTarget: 'side',
           labelWidth: 50
       },
       items: [panelMultiupload]
   });
    
});


function validarFormulario()
{
    if(validarFechasMantenimiento)
    {
        var valorTotal = Ext.getCmp('valorTotal').getValue();
        if(valorTotal=="" || !valorTotal)
        {
            Ext.Msg.alert('Error ', "Por favor ingrese el Valor Total");
            return false;
        }
        else if(valorTotal<0)
        {
            Ext.Msg.alert('Error ', "El valor total no puede ser menor a 0");
            return false;
        }

        var sumaTotalValores = 0;

        //Validando que se han ingresado los valores en las categorías
        for (var i = 0; i < gridValoresCategoriasMantenimientos.getStore().getCount(); i++)
        {
            if(gridValoresCategoriasMantenimientos.getStore().getAt(i).data.valorTotalCategoria)
            {}
            else
            {
                gridValoresCategoriasMantenimientos.getStore().getAt(i).data.valorTotalCategoria=0;
            }
            sumaTotalValores+=gridValoresCategoriasMantenimientos.getStore().getAt(i).data.valorTotalCategoria;
        }
        if(sumaTotalValores!=valorTotal)
        {
            Ext.Msg.alert("Alerta", "El valor Total no es igual a la suma de los valores en las categorías");
            return false;
        }

        jsonValoresCategoriasMantenimientos = obtenerValoresCategoriasMantenimientos();

        Ext.get('json_valores_categorias_mantenimientos').dom.value = jsonValoresCategoriasMantenimientos; 
        return true;
    }
    return false;
}


function obtenerValoresCategoriasMantenimientos()
{
    var array = new Object();
    array['total'] =  gridValoresCategoriasMantenimientos.getStore().getCount();
    array['valoresCategoriasMantenimientos'] = new Array();
    var array_data = new Array();
    for(var i=0; i < gridValoresCategoriasMantenimientos.getStore().getCount(); i++)
    {
      array_data.push(gridValoresCategoriasMantenimientos.getStore().getAt(i).data);
    }
    array['valoresCategoriasMantenimientos'] = array_data;
    return Ext.JSON.encode(array);
}


function validarFechasMantenimiento(cmp)
{
    var fieldFechaInicioMantenimiento    = Ext.getCmp('fechaInicio');
    var valFechaInicioMantenimiento      = fieldFechaInicioMantenimiento.getSubmitValue();

    var fieldFechaFinMantenimiento       = Ext.getCmp('fechaFin');
    var valFechaFinMantenimiento         = fieldFechaFinMantenimiento.getSubmitValue();

    var boolOKFechas        = true;
    var boolCamposLLenos    = false;
    var strMensaje          = '';
    var boolSinErrorFechas  = false;

    if(valFechaInicioMantenimiento && valFechaFinMantenimiento)
    {
        var valCompFechaInicioMantenimiento  = Ext.Date.parse(valFechaInicioMantenimiento, "d/m/Y");
        var valCompFechaFinMantenimiento     = Ext.Date.parse(valFechaFinMantenimiento, "d/m/Y");

        if(valCompFechaInicioMantenimiento>valCompFechaFinMantenimiento)
        {
            boolOKFechas    = false;
            strMensaje      = 'La Fecha Inicio '+ valFechaInicioMantenimiento +' no puede ser mayor a la Fecha Fin '+valFechaFinMantenimiento;
            Ext.Msg.alert('Atenci\xf3n', strMensaje); 
        }
    }

    if(valFechaInicioMantenimiento && valFechaFinMantenimiento )
    {
        boolCamposLLenos=true;
    }
    else
    {
        strMensaje      = 'La Fecha Inicio y Fecha Fin no pueden estar vacías';
        Ext.Msg.alert('Atenci\xf3n', strMensaje); 
    }

    if(boolOKFechas && boolCamposLLenos)
    {
        boolSinErrorFechas     = true;
    }
    else if(!boolOKFechas )
    {
        if(cmp!=null)
        {
            cmp.value = "";
            cmp.setRawValue("");
        }
        
        boolSinErrorFechas = false;
    }
    return boolSinErrorFechas;
}
