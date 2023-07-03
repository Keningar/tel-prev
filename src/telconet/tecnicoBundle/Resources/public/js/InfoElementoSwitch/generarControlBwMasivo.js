Ext.require([
    '*',
    'Ext.form.field.File',
    'Ext.form.Panel',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);
Ext.onReady(function()
{
    dataStoreDatosSwInterfaces = new Ext.data.ArrayStore({
        storeId: 'dataStoreDatosSwInterfaces',
        idIndex: 0,
        autoDestroy: false,
        autoLoad: false,
        data: arrayDatosSwInterfaces,
        fields:[
            {name:'is_file',     mapping:'is_file',     type: 'string'},
            {name:'idElemento',  mapping:'idElemento',  type: 'integer'},
            {name:'elemento',    mapping:'elemento',    type: 'string'},
            {name:'idInterface', mapping:'idInterface', type: 'integer'},
            {name:'interface',   mapping:'interface',   type: 'string'}
        ]
    });
    gridDatosSwInterfaces = Ext.create('Ext.grid.Panel',{
        title: 'Lista',
        id: 'gridSwitchInterfaces',
        width: '100%',
        height: 450,
        store: dataStoreDatosSwInterfaces,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
        renderTo: Ext.get('grid_generar_control_bw_masivo'),
        columns:[
            {
                id: 'is_file',
                header: 'is_file',
                dataIndex: 'is_file',
                hidden: true,
                hideable: false
            },
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'idInterface',
                header: 'idInterface',
                dataIndex: 'idInterface',
                hidden: true,
                hideable: false
            },
            {
                id: 'elemento',
                header: 'Switch',
                dataIndex: 'elemento',
                width: '45%',
                flex : 1,
                hidden: true,
                sortable: true
            },
            {
                id: 'interface',
                header: 'Interface',
                dataIndex: 'interface',
                width: '45%',
                hidden: true,
                sortable: true
            },
            {
                xtype:  'actioncolumn',
                header: 'Acciones',
                width: '10%',
                hidden: true,
                items:[{
                    tooltip: 'Quitar',
                    getClass: function(v, meta, rec) {
                        if(rec.get('is_file') == 'SI'){
                            return 'button-grid-invisible';
                        }else{
                            return 'button-grid-delete';
                        }
                    },
                    handler: function(grid, rowIndex){
                        var rec = dataStoreDatosSwInterfaces.getAt(rowIndex);
                        var tipoMasg = '';
                        if(Ext.getCmp('cbxTipoMasivo').getValue() === 'SW'){
                            tipoMasg = 'Está seguro de eliminar de la lista el switch: ' + rec.get('elemento');
                        }else{
                            tipoMasg = 'Está seguro de eliminar de la lista la interface: ' + rec.get('elemento') + '-' + rec.get('interface');
                        }
                        Ext.Msg.confirm('Alerta', tipoMasg,
                        function(btn){
                            if (btn === 'yes'){
                                dataStoreDatosSwInterfaces.removeAt(rowIndex);
                                var indexDatos = 0;
                                arrayDatosSwInterfaces.forEach(function(object, index){
                                    if(Ext.getCmp('cbxTipoMasivo').getValue() === 'SW'){
                                        if( object.idElemento === rec.get('idElemento') ){
                                            indexDatos = index;
                                        }
                                    }else{
                                        if( object.idInterface === rec.get('idInterface') ){
                                            indexDatos = index;
                                        }
                                    }
                                });
                                if(indexDatos >= 0){
                                    arrayDatosSwInterfaces.splice(indexDatos,1);
                                }
                                document.getElementById('div_count_sw_int').innerHTML = arrayDatosSwInterfaces.length;
                            }
                        });
                    }
                }]
            }
        ]
    });
    new Ext.create('Ext.form.field.Date', {
        name:     'fieldDate',
        id:       'fieldDate',
        width:    '100%',
        label:    '',
        format:   'Y-m-d',
        editable: false,
        allowBlank: false,
        renderTo: 'picker_fecha',
        minValue: new Date(),
        value:    new Date()
    });
    storeDiasNoPermitidos = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_GetDiasNoPermitidosBwMasivo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
            [
                {name: 'codigo', mapping: 'codigo'}
            ]
    }).load();
    storeTipoMasivo = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data : [
            {"id":"SW", "name":"Por Switch"},
            {"id":"INT", "name":"Por Switch/Interfaces"}
        ]
    });
    new Ext.create('Ext.form.ComboBox', {
        xtype:        'combobox',
        name:         'cbxTipoMasivo',
        id:           'cbxTipoMasivo',
        store:        storeTipoMasivo,
        queryMode:    'local',
        displayField: 'name',
        valueField:   'id',
        renderTo:     'combo_tipo_masivo',
        allowBlank:   false,
        editable:     false,
        width:        200,
        listeners:
        {
            select:
            {
                fn: function(combo,  records, index)
                {
                    clearData(false);
                    if(combo.getValue() == 'SW'){
                        Ext.getCmp('btnAddSwInt').setText('Agregar Switch');
                        gridDatosSwInterfaces.setTitle('Lista de Switch');
                        document.getElementById('label_count_sw_int').innerHTML = 'Total switch agregados: ';
                        document.getElementById('label_add_sw_int').innerHTML   = 'Agregar switch de forma individual';
                        gridDatosSwInterfaces.columns[4].setVisible(false);
                    } else {
                        Ext.getCmp('btnAddSwInt').setText('Agregar Interfaces');
                        gridDatosSwInterfaces.setTitle('Lista de Switch/Interfaces');
                        document.getElementById('label_count_sw_int').innerHTML = 'Total interfaces agregadas: ';
                        document.getElementById('label_add_sw_int').innerHTML   = 'Agregar interfaces de forma individual';
                        gridDatosSwInterfaces.columns[4].setVisible(true);
                    }
                    gridDatosSwInterfaces.columns[3].setVisible(true);
                    gridDatosSwInterfaces.columns[5].setVisible(true);
                    Ext.getCmp('formUploadFile').setDisabled(false);
                    Ext.getCmp('btnAddSwInt').setDisabled(false);
                    Ext.getCmp('btnGenerarMasivo').setDisabled(false);
                }
            },
            click:
            {
                element: 'el'
            }
        }
    });
    formUploadFile = new Ext.create('Ext.form.Panel', {
        title: '',
        id:    'formUploadFile',
        name:  'formUploadFile',
        width: '100%',
        bodyPadding: 10,
        frame: true,
        disabled: true,
        renderTo: 'upload_file',
        items: [{
            xtype: 'filefield',
            id:    'archivo',
            name:  'archivo',
            fieldLabel: '',
            labelWidth: 50,
            msgTarget: 'side',
            allowBlank: false,
            anchor: '100%',
            accept: '.csv',
            listeners:{
                afterrender:function(cmp){
                    cmp.fileInputEl.set({
                        accept: '.csv'
                    });
                },
                change: function(field, fileName) {
                    var ext = fileName.split(".").pop().toLowerCase();
                    if($.inArray(ext, ["csv"]) === 0) {
                        var error = false;
                        var mensajeError = '';
                        var arrayDatos = [];
                        var file = field.fileInputEl.dom.files[0];
                        readFile(file, function(e) {
                            var strSplit = '';
                            // use result in callback...
                            var lines = [];
                            var arrayIdSwitchInt = [];
                            var saltoLinea = e.target.result.indexOf("\r\n");
                            if (saltoLinea !== -1){
                                lines = e.target.result.split('\r\n');
                            }
                            else{
                                lines = e.target.result.split('\n');
                            }
                            for (i = 0; i < lines.length; ++i){
                                if( i > 0 && lines[i].length > 1 ){
                                    if(strSplit == ''){
                                        var posicion = lines[i].indexOf(";");
                                        if (posicion !== -1){
                                            strSplit = ';';
                                        }else{
                                            strSplit = ',';
                                        }
                                    }
                                    if(Ext.getCmp('cbxTipoMasivo').getValue() == 'SW'){
                                        var arraySplit = lines[i].split(strSplit);
                                        if( arraySplit.length != 1 ){
                                            error = true;
                                            mensajeError = 'Error al procesar el csv, el archivo solo debe tener switch(s).';
                                        }
                                        if( arraySplit.length == 1 && arraySplit[0].length > 1 && !arrayIdSwitchInt.includes(arraySplit[0]) ){
                                            var objectSwInt = {
                                                elemento: arraySplit[0]
                                            };
                                            arrayDatos.push(objectSwInt);
                                            arrayIdSwitchInt.push(arraySplit[0]);
                                        }
                                    }
                                    else{
                                        var arraySplit = lines[i].split(strSplit);
                                        if( arraySplit.length != 2 ){
                                            error = true;
                                            mensajeError = 'Error al procesar el csv deben estar separados los switch y las interfaces por ' +
                                                           'punto y coma ";" o por coma ","';
                                        }
                                        if( arraySplit.length == 2 && arraySplit[0].length > 1 && arraySplit[1].length > 1 
                                            && !arrayIdSwitchInt.includes(arraySplit[0] + ';' + arraySplit[1]) ){
                                            var objectSwInt = {
                                                elemento:    arraySplit[0],
                                                interface:   arraySplit[1]
                                            };
                                            arrayDatos.push(objectSwInt);
                                            arrayIdSwitchInt.push(arraySplit[0] + ';' + arraySplit[1]);
                                        }
                                    }
                                }
                                if( error ){
                                    break;
                                }
                            }
                            if(!error && arrayDatos.length == 0){
                                error = true;
                                mensajeError = 'Error el archivo esta vació';
                            }
                            if(error){
                                Ext.getCmp('archivo').setValue('');
                                Ext.getCmp('archivo').setRawValue('');
                                Ext.Msg.show({
                                    title:    'Error',
                                    msg:      mensajeError,
                                    buttons:  Ext.Msg.OK,
                                    icon:     Ext.MessageBox.ERROR
                                });
                            }
                            else {
                                Ext.getCmp('formUploadFile').setDisabled(true);
                                var arrayDatosIdSwitchInt = [];
                                if(Ext.getCmp('cbxTipoMasivo').getValue()==='SW'){
                                    arrayDatosSwInterfaces.forEach(function( object, index ) {
                                        arrayDatosIdSwitchInt.push(object.idElemento);
                                    });
                                }
                                else{
                                    arrayDatosSwInterfaces.forEach(function( object, index ) {
                                        arrayDatosIdSwitchInt.push(object.idInterface);
                                    });
                                }
                                connProcessData.request({
                                    url:    url_validateControlBwMasivo,
                                    method: 'POST',
                                    timeout: 300000,
                                    params:{
                                        tipo: Ext.getCmp('cbxTipoMasivo').getValue(),
                                        arrayIdSwitchInt: Ext.encode(arrayDatosIdSwitchInt),
                                        arrayData: Ext.encode(arrayDatos)
                                    },
                                    success: function(response){
                                        var result = Ext.decode(response.responseText);
                                        var arrayIdSwitchIntValid = [];
                                        if (result.status == 'OK'){
                                            for(i = 0; i < result.data.length; ++i){
                                                var intIdValid = null;
                                                if(Ext.getCmp('cbxTipoMasivo').getValue()==='SW'){
                                                    intIdValid = result.data[i].idElemento;
                                                }
                                                else{
                                                    intIdValid = result.data[i].idInterface;
                                                }
                                                if( !arrayIdSwitchIntValid.includes(intIdValid) ){
                                                    var objectSwInt = {
                                                        is_file:     'SI',
                                                        idElemento:  result.data[i].idElemento,
                                                        elemento:    result.data[i].elemento,
                                                        idInterface: result.data[i].idInterface,
                                                        interface:   result.data[i].interface
                                                    };
                                                    arrayDatosSwInterfaces.push(objectSwInt);
                                                    if(Ext.getCmp('cbxTipoMasivo').getValue()==='SW'){
                                                        arrayIdSwitchIntValid.push(result.data[i].idElemento);
                                                    }
                                                    else{
                                                        arrayIdSwitchIntValid.push(result.data[i].idInterface);
                                                    }
                                                }
                                            }
                                            document.getElementById('div_count_sw_int').innerHTML = arrayDatosSwInterfaces.length;
                                            dataStoreDatosSwInterfaces.loadData(arrayDatosSwInterfaces);
                                            Ext.Msg.show({
                                                title:   'Informaci\xf3n',
                                                msg:     result.mensaje,
                                                buttons: Ext.Msg.OK,
                                                icon:    Ext.MessageBox.INFO
                                            });
                                        }
                                        else{
                                            Ext.Msg.show({
                                                title:   'Error',
                                                msg:      'Error: ' + result.mensaje,
                                                buttons:  Ext.Msg.OK,
                                                icon:     Ext.MessageBox.ERROR
                                            });
                                        }
                                    },
                                    failure: function(response){
                                        Ext.Msg.show({
                                            title:   'Error',
                                            msg:     'Error: ' + response.statusText,
                                            buttons:  Ext.Msg.OK,
                                            icon:     Ext.MessageBox.ERROR
                                        });
                                    }
                                });
                            }
                        });
                    }
                    else{
                        Ext.Msg.show({
                            title:    'Error',
                            msg:      'Error: Debe adjuntar un archivo con extensión CSV.',
                            buttons:  Ext.Msg.OK,
                            icon:     Ext.MessageBox.ERROR
                        });
                    }
                }
            },
            regex: /(.)+((\.csv)(\w)?)$/i,
            regexText: 'Permitido solo archivos con formato CSV',
            buttonText: 'Seleccionar...'
        }],
        buttons: [{
            id:   'btnFormUploadFile',
            name: 'btnFormUploadFile',
            text: 'Cargar',
            hidden: true,
            handler: function() {
                var form = this.up('form').getForm();
                if(form.isValid())
                {
                    form.submit({
                        url: url_GenerarControlBwMasivo,
                        params :{
                            fecha: $("#fieldDate-inputEl").val(),
                            tipo: Ext.getCmp('cbxTipoMasivo').getValue(),
                            arrayDatosSwInterfaces: Ext.encode(arrayDatosSwInterfaces)
                        },
                        timeout: 300000,
                        waitMsg: 'Generando Control Bw Automático...',
                        success: function(fp, o){
                            Ext.Msg.show({
                                title:   'Informaci\xf3n',
                                msg:     o.result.mensaje,
                                buttons: Ext.Msg.OK,
                                icon:    Ext.MessageBox.INFO
                            });
                            clearData();
                        },
                        failure: function(fp, o) {
                            Ext.Msg.show({
                                title:   'Error',
                                msg:     'Error: ' + o.result.mensaje,
                                buttons:  Ext.Msg.OK,
                                icon:     Ext.MessageBox.ERROR
                            });
                            clearData();
                        }
                    });
                }
            }
        }]
    });
    Ext.create('Ext.Button', {
        text:      'Generar Control Bw Automático',
        renderTo:  Ext.get('btn_generar_masivo'),
        iconCls:   'iconSave',
        width:     '100%',
        cls:       'x-btn-item-medium',
        id:        'btnGenerarMasivo',
        itemId:    'btnGenerarMasivo',
        textAlign: 'center',
        disabled:  true,
        listeners: {
            click: function() {
                generarControlBwMasivo();
            }
        }
    });
    Ext.create('Ext.Button', {
        text:      'Agregar',
        width:     '80%',
        renderTo:  Ext.get('btn_add_sw_int'),
        iconCls:   'icon_add',
        cls:       'x-btn-item-medium',
        id:        'btnAddSwInt',
        disabled:  true,
        textAlign: 'center',
        listeners: {
            click: function() {
                showPanelAgregarSwInt();
            }
        }
    });
    Ext.create('Ext.Button', {
        text:      'Limpiar',
        width:     '100%',
        renderTo:  Ext.get('btn_limpiar'),
        iconCls:   'icon_limpiar',
        cls:       'x-btn-item-medium',
        id:        'btnLimpiar',
        itemId:    'btnLimpiar',
        textAlign: 'center',
        listeners: {
            click: function() {
                clearData();
            }
        }
    });
    function clearData(tipoClear = true){
        arrayDatosSwInterfaces = [];
        dataStoreDatosSwInterfaces.load();
        document.getElementById('div_count_sw_int').innerHTML = arrayDatosSwInterfaces.length;
        document.getElementById('label_add_sw_int').innerHTML = 'Agregar item de forma individual';
        document.getElementById('label_count_sw_int').innerHTML = 'Total agregados: ';
        gridDatosSwInterfaces.setTitle('Lista');
        gridDatosSwInterfaces.columns[3].setVisible(false);
        gridDatosSwInterfaces.columns[4].setVisible(false);
        gridDatosSwInterfaces.columns[5].setVisible(false);
        Ext.getCmp('archivo').setValue('');
        Ext.getCmp('archivo').setRawValue('');
        Ext.getCmp('formUploadFile').setDisabled(true);
        Ext.getCmp('btnAddSwInt').setText('Agregar');
        Ext.getCmp('btnAddSwInt').setDisabled(true);
        Ext.getCmp('btnGenerarMasivo').setDisabled(true);
        if(tipoClear){
            $("#fieldDate-inputEl").val(Ext.Date.format(new Date(), "Y-m-d"));
            Ext.getCmp('cbxTipoMasivo').setValue('');
            Ext.getCmp('cbxTipoMasivo').setRawValue("");
        }
    }
    function convertDateToUTC(date)
    { 
        return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds()); 
    }
    function validarDiaPermitido(strDiaSeleccionado)
    {
        var diaPermitido = true;
        for(i = 0; i < storeDiasNoPermitidos.data.items.length; ++i)
        {
            if(storeDiasNoPermitidos.data.items[i].data.codigo == strDiaSeleccionado)
            {
                diaPermitido = false;
            }
        }
        return diaPermitido;
    }
    function generarControlBwMasivo(){
        var field_date = $("#fieldDate-inputEl").val();
        var objFechaSeleccionada = convertDateToUTC(new Date(field_date));
        var strDiaSeleccionado = objFechaSeleccionada.getDate();
        if(!validarDiaPermitido(strDiaSeleccionado))
        {
            Ext.Msg.show({
                title:   'Informaci\xf3n',
                msg:     'No esta permitido la ejecución del Control Bw Automático en el día seleccionado',
                buttons:  Ext.Msg.OK,
                icon:     Ext.MessageBox.INFO
            });
        }
        else if( arrayDatosSwInterfaces.length > 0 && field_date.length > 1 ){
            Ext.Msg.confirm('Alerta','Está seguro de generar la solicitud de Control BW Automático.',
            function(btn){
                if (btn == 'yes'){
                    Ext.getCmp('formUploadFile').setDisabled(false);
                    Ext.getCmp('btnFormUploadFile').btnEl.dom.click();
                }
            });
        }
        else if( arrayDatosSwInterfaces.length < 1 ) {
            var strMensaje = 'No se ha agregado ningún item.';
            if(Ext.getCmp('cbxTipoMasivo').getValue()==='SW'){
                strMensaje = 'Debe adjuntar un archivo CSV o puede agregar switch mediante la opción Agregar Switch.';
            }
            else if(Ext.getCmp('cbxTipoMasivo').getValue()==='INT'){
                strMensaje = 'Debe adjuntar un archivo CSV o puede agregar switch e interfaces mediante la opción Agregar Interfaces.';
            }
            Ext.Msg.show({
                title:   'Informaci\xf3n',
                msg:     strMensaje,
                buttons:  Ext.Msg.OK,
                icon:     Ext.MessageBox.INFO
            });
        }
        else if( field_date.length < 2 ) {
            Ext.Msg.show({
                title:   'Informaci\xf3n',
                msg:     'No se ha seleccionado la fecha de ejecución.',
                buttons:  Ext.Msg.OK,
                icon:     Ext.MessageBox.INFO
            });
        }
    }
    function showPanelAgregarSwInt(){
        var arrayIdSwitchInt = [];
        if(Ext.getCmp('cbxTipoMasivo').getValue()==='SW'){
            arrayDatosSwInterfaces.forEach(function( object, index ) {
                arrayIdSwitchInt.push(object.idElemento);
            });
        }
        storeSwitch = Ext.create('Ext.data.Store',{
            model: 'ListModelSwitch',
            autoLoad: false,
            timeout : 300000,
            proxy:
            {
                type: 'ajax',
                url: url_GetSwitchControlBwMasivo,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'registros'
                },
                extraParams: {
                    arrayIdSwitch: Ext.encode(arrayIdSwitchInt)
                }
            },
            fields:
            [
                {name: 'idElemento', mapping: 'idElemento', type: 'integer'},
                {name: 'elemento',   mapping: 'elemento',   type: 'string'}
            ]
        });
        storeInterface = Ext.create('Ext.data.Store',{
            model: 'ListModelInterface',
            autoLoad: false,
            timeout : 300000,
            proxy:
            {
                type: 'ajax',
                url: url_GetInterfaceControlBwMasivo,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'registros'
                },
                extraParams: {
                    idElemento: null,
                    arrayIdInterface: null
                }
            },
            fields:
            [
                {name:'idInterface', mapping:'idInterface', type: 'integer'},
                {name:'interface',   mapping:'interface',   type: 'string'}
            ]
        });
        if(Ext.getCmp('cbxTipoMasivo').getValue()==='SW'){
            formAgregarSwInt = Ext.create('Ext.form.Panel', {
                title: '',
                width: '100%',
                bodyPadding: 10,
                renderTo: Ext.getBody(),
                items: [
                    {
                        xtype: 'combobox',
                        store: storeSwitch,
                        labelAlign: 'left',
                        name: 'cbxAddSwitch',
                        id: 'cbxAddSwitch',
                        valueField: 'idElemento',
                        displayField: 'elemento',
                        fieldLabel: 'Switch',
                        loadingText: 'Buscando...',
                        width: 400,
                        emptyText: 'Digite el nombre del switch para la busqueda',
                        allowBlank: true,
                        matchFieldWidth: true,
                        queryMode: 'remote',
                        listeners:
                        {
                            select:
                            {
                                fn: function(combo,  records, index)
                                {
                                    Ext.getCmp('btnPanelAgregar').setDisabled(false);
                                }
                            },
                            change: function(combo, value) {
                                if (value) {
                                    Ext.getCmp('btnPanelAgregar').setDisabled(true);
                                }
                            },
                            click:
                            {
                                element: 'el'
                            }
                        }
                    },
                    {
                        xtype:     'button',
                        text:      'Agregar',
                        width:     150,
                        iconCls:   'icon_add',
                        cls:       'x-btn-item-medium',
                        id:        'btnPanelAgregar',
                        itemId:    'btnPanelAgregar',
                        textAlign: 'center',
                        disabled:  true,
                        listeners: {
                            click: function() {
                                var objectSwInt = {
                                    is_file:     'NO',
                                    idElemento:  Ext.getCmp('cbxAddSwitch').getValue(),
                                    elemento:    Ext.getCmp('cbxAddSwitch').getRawValue(),
                                    idInterface: null,
                                    interface:   null
                                };
                                arrayDatosSwInterfaces.push(objectSwInt);
                                document.getElementById('div_count_sw_int').innerHTML = arrayDatosSwInterfaces.length;
                                dataStoreDatosSwInterfaces.loadData(arrayDatosSwInterfaces);
                                win2.destroy();
                            }
                        }
                    }
                ]
            });
        }
        else{
            formAgregarSwInt = Ext.create('Ext.form.Panel', {
                title: '',
                width: '100%',
                bodyPadding: 10,
                renderTo: Ext.getBody(),
                items: [
                    {
                        xtype: 'combobox',
                        store: storeSwitch,
                        labelAlign: 'left',
                        name: 'cbxAddSwitch',
                        id: 'cbxAddSwitch',
                        valueField: 'idElemento',
                        displayField: 'elemento',
                        fieldLabel: 'Switch',
                        loadingText: 'Buscando...',
                        width: 400,
                        emptyText: 'Digite el nombre del switch para la busqueda',
                        allowBlank: true,
                        matchFieldWidth: true,
                        queryMode: 'remote',
                        listeners: {
                            select: {
                                fn: function(combo,  records, index) {
                                    var arrayIdSwitchInt = [];
                                    arrayDatosSwInterfaces.forEach(function( object, index ) {
                                        arrayIdSwitchInt.push(object.idInterface);
                                    });
                                    storeInterface.getProxy().extraParams = {};
                                    storeInterface.getProxy().extraParams.idElemento       = combo.getValue();
                                    storeInterface.getProxy().extraParams.arrayIdInterface = Ext.encode(arrayIdSwitchInt);
                                    storeInterface.getProxy().timeout                      = 300000;
                                    storeInterface.load();
                                    Ext.getCmp('cbxAddInterface').setDisabled(false);
                                    Ext.getCmp('btnPanelAgregar').setDisabled(false);
                                }
                            },
                            change: function(combo, value) {
                                if (value) {
                                    storeInterface.clearData();
                                    Ext.getCmp('cbxAddInterface').setValue('');
                                    Ext.getCmp('cbxAddInterface').setRawValue('');
                                    Ext.getCmp('btnPanelAgregar').setDisabled(true);
                                    Ext.getCmp('cbxAddInterface').setDisabled(true);
                                }
                            },
                            click:
                            {
                                element: 'el'
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        store: storeInterface,
                        labelAlign: 'left',
                        name: 'cbxAddInterface',
                        id: 'cbxAddInterface',
                        valueField: 'idInterface',
                        displayField: 'interface',
                        fieldLabel: 'Interface',
                        loadingText: 'Buscando...',
                        width: 400,
                        emptyText: 'Seleccione las interfaces',
                        allowBlank: false,
                        matchFieldWidth: true,
                        disabled: true,
                        editable: false,
                        queryMode: 'remote',
                        multiSelect: true,
                        forceSelection: true,
                        mode: 'local',
                        triggerAction: 'all',
                        listConfig : {
                            getInnerTpl : function() {
                                return '<div class="x-combo-list-item"><img src="' + Ext.BLANK_IMAGE_URL + '" class="chkCombo-default-icon chkCombo" /> {interface} </div>';
                            }
                        }
                    },
                    {
                        xtype:     'button',
                        text:      'Agregar',
                        width:     150,
                        iconCls:   'icon_add',
                        cls:       'x-btn-item-medium',
                        id:        'btnPanelAgregar',
                        itemId:    'btnPanelAgregar',
                        textAlign: 'center',
                        disabled:  true,
                        listeners: {
                            click: function() {
                                var idValueCbx = Ext.getCmp('cbxAddInterface').getValue();
                                if(idValueCbx != null && idValueCbx.length > 0){
                                    var strIdInterface      = Ext.getCmp('cbxAddInterface').getValue().toString();
                                    var strValueInterface   = Ext.getCmp('cbxAddInterface').getRawValue().toString();
                                    var arrayIdInterface    = strIdInterface.split(',');
                                    var arrayValueInterface = strValueInterface.split(',');
                                    if(arrayIdInterface.length > 0 && arrayValueInterface.length > 0 && arrayIdInterface.length == arrayValueInterface.length){
                                        arrayIdInterface.forEach( function(value, index, array) {
                                            var objectSwInt = {
                                                is_file:     'NO',
                                                idElemento:  Ext.getCmp('cbxAddSwitch').getValue(),
                                                elemento:    Ext.getCmp('cbxAddSwitch').getRawValue(),
                                                idInterface: value,
                                                interface:   arrayValueInterface[index]
                                            };
                                            arrayDatosSwInterfaces.push(objectSwInt);
                                        });
                                        document.getElementById('div_count_sw_int').innerHTML = arrayDatosSwInterfaces.length;
                                        dataStoreDatosSwInterfaces.loadData(arrayDatosSwInterfaces);
                                        win2.destroy();
                                    }
                                }
                            }
                        }
                    }
                ]
            });
        }
        var strTitle = 'Agregar Interfaces';
        if(Ext.getCmp('cbxTipoMasivo').getValue()==='SW'){
            var strTitle = 'Agregar Switch';
        }
        win2 = Ext.create('Ext.window.Window',{
            title: strTitle,
            modal: true,
            width: 450,
            closable: true,
            layout: 'fit',
            items: [formAgregarSwInt]
        }).show();
    }
    var connProcessData = new Ext.data.Connection({
        listeners:
        {
            'beforerequest':
            {
                fn: function()
                {
                    Ext.MessageBox.show(
                    {
                        msg:          'Cargando los datos del archivo, por favor espere!!',
                        progressText: 'Upload...',
                        width:         300,
                        wait:          true,
                        waitConfig:
                        {
                            interval: 200
                        }
                    });
                },
                scope: this
            },
            'requestcomplete':
            {
                fn: function()
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception':
            {
                fn: function()
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });
    function readFile(file, onLoadCallback){ 
        var reader = new FileReader(); 
        reader.onload = onLoadCallback; 
        reader.readAsText(file); 
    }
});
