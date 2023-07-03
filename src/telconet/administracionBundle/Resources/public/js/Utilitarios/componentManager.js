

/**
 * Metodo utilitarios para generar formulario y Grid
 * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 07-10-2022
 * @since 1.0
 */
 
 /**
 * Metodo Utilitario Renderiza componetes en tipo Grid
 * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 07-10-2022
 * @since 1.0
 */
 const generateGridManager = (data) => {
    const {
        view,
        height,
        autoScroll,
        title,
        name,
        ajax,
        autoLoad,
        fields,
        columns,
        tbar,
        tbar_add,
        tbar_refresh,
        cellEditing,
        collapsible,
        collapsed,
        pageSize,

    } = data;

    let modelGridManager = Ext.define('model' + name, {
        extend: 'Ext.data.Model',
        fields: fields,
    });


    let storeGridManager = Ext.create('Ext.data.Store', {
        model: modelGridManager,
        autoLoad: autoLoad,
        id: 'idStore' + name,
        name: 'idStore' + name,
        pageSize: pageSize || 1,//numero de paginas
        proxy: ajax ?
            {
                type: 'ajax',
                url: ajax.url,
                timeout: 60000,
                reader: ajax.reader,
                extraParams: ajax.extraParams,
                simpleSortMode: true,
            } : {
                type: 'memory',
            },
        listeners: {
            load: function (sender, node, records, ddd) {
                view.unmask();
                if (ajax) {
                    let jsonData = sender.proxy.reader.jsonData||{};
                    if (jsonData.status != 'OK') {
                        Ext.Msg.alert('Alerta ', jsonData.message);
                    }
                }
            },
        },
    });



    let tbar_items = [];

    if (tbar_refresh) {

      /*  tbar_items.push({
            xtype: 'textfield',
            emptyText: 'Buscar',
            onChange: function (newVal, oldVal) {
            }
        });*/
    }

    tbar_items.push({ xtype: 'tbfill' })


    tbar_items = tbar_items.concat(tbar || []);

    if (tbar_add) {
        tbar_items.push(new Ext.Button({
            text: 'Agregar Nuevo Registro',
            iconCls: 'icon_add',
            border: true,
            handler: function () {
                let items = storeGridManager.data.items;
                let permiteCrear = true;
                if (items.length != 0) {
                    permiteCrear = (Object.keys(items[0].raw).length !== 0);
                }

                if (permiteCrear) {
                    let model = Ext.create(modelGridManager);
                    storeGridManager.insert(0, model);
                } else {
                    Ext.Msg.alert('Alerta ', 'Ya existe un item para agregar un nuevo registro, es necesario guardarlo o eliminarlo.');
                }

            }
        }))
    }
    if (tbar_refresh) {
        tbar_items.push(new Ext.Button({
            text: 'Refrescar',
            iconCls: 'icon_refresh',
            border: true,
            handler: function () {
                storeGridManager.load();
            }
        }));
    }





    let objConfGrid = {
        xtype: 'grid',
        store: storeGridManager,
        title,
        id: 'idGrid' + name,
        height: height ? height : 'auto',
        //  defaults: { flex: 1 },
        style: { width: '-webkit-fill-available' },
        //layout: 'hbox',
        autoHeight: height ? false : true,
        autoScroll: autoScroll ? autoScroll : true,
        columns,
        collapsible,
        collapsed,
        plugins: [],
        features: [],
        dockedItems: [],
        tbar: {
            align: 'center',
            border: true,
            items: tbar_items
        }
    }

    if (pageSize) {
        objConfGrid.dockedItems.push(
            Ext.create('Ext.toolbar.Paging', {
                displayInfo: true,
                dock: 'bottom',
                store: storeGridManager
            })
        )
    }


    if (cellEditing) {
        objConfGrid.plugins.push(
            Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 2,
            })
        )
    }


    return objConfGrid;

}

/**
* Metodo Utilitario Renderiza componetes en tipo input
* @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
* @version 1.0 07-10-2022
* @since 1.0
*/
const generateInput = (data) => {
    const {
        type,
        name,
        title,
        value, 
        maxValue,
        maxRows,
        displayField,
        valueField,
        dataLocal,
        hidden,
        colspan,
        rowspan,
        defaults //defaults: { anchor: '100%' }
    } = data;

    let objFieldset = {
        xtype: 'fieldset',
        id: 'field-' + name,
        name: 'field-' + name,
        title: title,
        height: 'auto',
        defaults: defaults,
        layout: 'anchor',
        style: {
            marginLeft: '15px',
            marginRight: '15px',
            border: 'none',
            padding: '0px',
        },
        items: [],
        hidden: hidden,
        colspan: colspan,
        rowspan: rowspan
    };

    let objInput = {
        xtype: 'textfield',
        id: name,
        name: name,
        height: 'auto',
        defaults: { flex: 1 },
        style: { width: '-webkit-fill-available' },
        layout: 'hbox',
        value: value ? value : '',
        //  allowBlank: required,
        listeners: {
            afterrender: function (cmp) {
                renderizarAjusteEstilo();
            }
        }
    };




    let items = (dataLocal || []).map(
        (e) => {
            let item = {
                id: e['idt'],
                boxLabel: e[displayField],
                inputValue: e[valueField],
                checked: e['checked'],
                name: 'cb-custwidth'
            }
            return item;
        }
    );

    switch (type) {
        case 'number':
            objInput.xtype = 'numberfield';
            objInput.minValue = 1;
            objInput.maxValue = maxValue;
            break;

        case 'textarea':
            objInput.xtype = 'textareafield';
            objInput.maxRows = maxRows;
            break;

        case 'url':
            //  objInput.xtype = 'urlfield';
            //  objInput.label = title;
            break;

        case 'select':
            objInput.xtype = 'combobox';
            objInput.cls = 'x-input-group-alt';
            objInput.store = Ext.create('Ext.data.Store', {
                fields: [valueField, displayField,],
                data: dataLocal
            });
            objInput.queryMode = 'local';
            objInput.displayField = displayField;
            objInput.valueField = valueField;
            objInput.multiSelect = false;
            objInput.editable = false;
            objInput.selecOnFocus = true;
            objInput.forceSelection = false;
            objInput.autocomplete = true;
            objInput.emptyText = 'Seleccionar una  opción';
            break;
        case 'multiSelect':

            objInput.xtype = 'combobox';
            objInput.cls = 'x-input-group-alt';
            objInput.store = Ext.create('Ext.data.Store', {
                fields: [valueField, displayField,],
                data: dataLocal
            });
            objInput.queryMode = 'local';
            objInput.displayField = displayField;
            objInput.valueField = valueField;
            objInput.multiSelect = true;
            objInput.editable = false;
            objInput.selecOnFocus = true;
            objInput.autocomplete = true;
            objInput.emptyText = 'Seleccionar una o varias opción';
            break;
        case 'checkbox':
            objInput.xtype = 'checkboxgroup';
            objInput.items = items;
            objInput.vertical = true;
            objInput.columns = 2;
            delete objInput['layout'];
            break;
        case 'radiobox':
            objInput.xtype = 'fieldcontainer';
            objInput.defaultType = 'radiofield';
            objInput.items = items;

            break;
    }


    objFieldset.items.push(objInput);

    return objFieldset;
}






/**
* Metodo Utilitario Renderiza  ajustes en css de combox
* @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
* @version 1.0 07-10-2022
* @since 1.0
*/
const renderizarAjusteEstilo = () => {
    setTimeout(() => {
        let objSelector = document.getElementsByClassName('x-form-trigger-wrap');
        for (let index = 0; index < objSelector.length; index++) {
            const element = objSelector[index];
            element.style = null;
        }
    }, 10);
}


/**
* Metodo Utilitario ejecutar consultas ajax con reintento en fallo
* @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
* @version 1.0 07-10-2022
* @since 1.0
*/
const runAjax = (data) => {
    const { view, title, url, metodo, parameter, callBackSuccess, callBackeError } = data;
    const ejecutarPeticion = () => {

        var myMask = new Ext.LoadMask(view,
            {
                msg: title,
                style: { marginTop: '50px' }
            });

        myMask.show();

        $.ajax({
            url: url,
            type: metodo,
            data: parameter,
            dataType: "json",
            success: function (response) {
                myMask.destroy();
                if (response.status == 'OK') {
                    callBackSuccess(response.data);
                } else {
                    Ext.Msg.alert('Error ', response.message);
                }

            },

            error: function (XMLHttpRequest, textStatus, errorThrown) {
                myMask.destroy();

                Ext.Msg.confirm('Alerta', 'Petición fallida desea reintentar', function (btn) {
                    if (btn == 'yes') {
                        ejecutarPeticion()
                    } else {
                        Ext.Msg.alert('Error ', errorThrown);
                        if (callBackeError) {
                            callBackeError();
                        }
                    }
                });


            }
        });
    }
    ejecutarPeticion();
}


/**
* Metodo Utilitario retornar data de store en json
* @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
* @version 1.0 07-10-2022
* @since 1.0
*/
function getDataJsonStore(store) {
    let arrayJson = [];
    let intTamanio = store.getCount();
    for (let index = 0; index < intTamanio; index++) {
        const item = store.getAt(index).data;
        arrayJson.push(item);
    }
    return arrayJson;
}