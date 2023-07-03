function Contacto() {
    this.intPageSize = 1000;
    this.intTimeOut = 90000;
    this.booleanAutoLoadStore = true;
    this.booleanAutoLoadStoreATR = true;
    this.intHeightGrid = 355;
    this.intWidthGrid = 1000;
    this.intWidthField = 350;
    this.intPageSizeCliente = 10;
    this.intWidthWindowInfoCont = 740;
    this.intHeightWindowInfoCont = 518;
    this.intWidthGridInfoCont = 682;
    this.intHeightGridInfoCont = 264;
    this.AJAX = 'ajax';
    this.JSON = 'json';

    /* INICIO : Declaracion de campos del filter panel*/

    this.btnBuscar = Utils.button();
    this.btnBuscar.text = 'Buscar';
    this.btnBuscar.iconCls = 'icon_search';

    this.btnLimpiar = Utils.button();
    this.btnLimpiar.text = 'Limpiar';
    this.btnLimpiar.iconCls = 'icon_limpiar';

    this.objDateDesde = Utils.objDate();
    this.objDateDesde.id = 'cbxIdFechaDesde';
    this.objDateDesde.name = 'cbxNameFechaDesde';
    this.objDateDesde.fieldLabel = 'Fecha Desde';
    this.objDateDesde.style = Utils.STYLE_BOLD;
    this.objDateDesde.editable = false;
    this.objDateDesde.align = 'left';
    this.objDateDesde.width = this.intWidthField;

    this.objDateHasta = Utils.objDate();
    this.objDateHasta.id = 'cbxIdFechaHasta';
    this.objDateHasta.name = 'cbxNameFechaHasta';
    this.objDateHasta.fieldLabel = 'Fecha Hasta';
    this.objDateHasta.style = Utils.STYLE_BOLD;
    this.objDateHasta.editable = false;
    this.objDateHasta.align = 'left';
    this.objDateHasta.width = this.intWidthField;

    this.objTxtNombres = Utils.objText();
    this.objTxtNombres.id = 'txtIdNombres';
    this.objTxtNombres.name = 'txtNameNombres';
    this.objTxtNombres.fieldLabel = 'Nombres';
    this.objTxtNombres.regex = Utils.REGEX_ALFA_LET_SPACE;
    this.objTxtNombres.msgTarget = 'under';
    this.objTxtNombres.style = Utils.STYLE_BOLD;
    this.objTxtNombres.invalidText = 'No se permiten numeros o caracteres especiales';
    this.objTxtNombres.width = this.intWidthField;

    this.objTxtApellidos = Utils.objText();
    this.objTxtApellidos.id = 'txtIdApellidos';
    this.objTxtApellidos.name = 'txtNameApellidos';
    this.objTxtApellidos.fieldLabel = 'Apellidos';
    this.objTxtApellidos.regex = Utils.REGEX_ALFA_LET_SPACE;
    this.objTxtApellidos.msgTarget = 'under';
    this.objTxtApellidos.style = Utils.STYLE_BOLD;
    this.objTxtApellidos.invalidText = 'No se permiten numeros o caracteres especiales';
    this.objTxtApellidos.width = this.intWidthField;

    this.objStoreRol = function(objScope) {
        this.modelAdmiTipoRol = Ext.define('modelAdmiRolbyTipoRol', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdRol', type: 'int'},
                {name: 'intIdEmpresaRol', type: 'int'},
                {name: 'strDescripcionRol', type: 'string'},
                {name: 'strEstado', type: 'string'}
            ]
        });

        return new Ext.create('Ext.data.Store', {
            id: objScope.id,
            model: this.modelAdmiTipoRol,
            autoLoad: this.booleanAutoLoadStore,
            proxy: {
                type: this.AJAX,
                url: urlGetAdmiRolbyTipoRol,
                timeout: this.intTimeOut,
                reader: {
                    type: this.JSON,
                    root: 'registros'
                },
                extraParams: objScope.extraParams,
                simpleSortMode: true
            }
        });
    };

    this.objComboMultiSelectRol = function(objScope) {

        Ext.define('cboSelectedCountTipoContacto', {
            alias: 'plugin.selectedAdmiRol',
            init: function(cboSelectedCountTipoContacto) {
                cboSelectedCountTipoContacto.on({
                    select: function(me, objRecords) {
                        intNumeroRegistros = objRecords.length;
                        storeCboSelectedCountTipoContacto = cboSelectedCountTipoContacto.getStore();
                        boolDiffRowCbo = objRecords.length !== storeCboSelectedCountTipoContacto.count;
                        boolNewAll = false;
                        boolSelectedAll = false;
                        objNewRecords = [];
                        Ext.each(objRecords, function(obj, i, objRecordsItself) {
                            if (objRecords[i].data.intIdEmpresaRol === 0) {
                                boolSelectedAll = true;
                                if (!cboSelectedCountTipoContacto.boolCboSelectedAll) {
                                    intNumeroRegistros = storeCboSelectedCountTipoContacto.getCount();
                                    cboSelectedCountTipoContacto.select(storeCboSelectedCountTipoContacto.getRange());
                                    cboSelectedCountTipoContacto.boolCboSelectedAll = true;
                                    boolNewAll = true;
                                }
                            } else {
                                if (boolDiffRowCbo && !boolNewAll)
                                    objNewRecords.push(objRecords[i]);
                            }
                        });
                        if (cboSelectedCountTipoContacto.boolCboSelectedAll && !boolSelectedAll) {
                            cboSelectedCountTipoContacto.clearValue();
                            cboSelectedCountTipoContacto.boolCboSelectedAll = false;
                        } else if (boolDiffRowCbo && !boolNewAll) {
                            cboSelectedCountTipoContacto.select(objNewRecords);
                            cboSelectedCountTipoContacto.boolCboSelectedAll = false;
                        }
                    }
                });
            }
        });

        return new Ext.create('Ext.form.ComboBox', {
            disabled: false,
            multiSelect: true,
            plugins: ['selectedAdmiRol'],
            id: objScope.strIdObj,
            fieldLabel: 'Tipo Contacto',
            style: Utils.STYLE_BOLD,
            store: objScope.objStore,
            queryMode: 'local',
            editable: false,
            displayField: 'strDescripcionRol',
            valueField: 'intIdEmpresaRol',
            width: objScope.intWidth,
            displayTpl: '<tpl for="."> { strDescripcionRol } <tpl if="xindex < xcount">, </tpl> </tpl>',
            listConfig: {
                itemTpl: '{ strDescripcionRol} <div class="uncheckedChkbox"></div>'
            }
        });
    };

    this.storeAdmiTitulo = function() {
        var objContacto = new Contacto();
        Ext.define('modelAdmiTitulo', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdTitulo', type: 'int'},
                {name: 'strDescripcionTitulo', type: 'string'},
                {name: 'strEstado', type: 'string'}
            ]
        });
        return new Ext.create('Ext.data.Store', {
            id: 'storeIdTitulo',
            model: 'modelAdmiTitulo',
            autoLoad: objContacto.booleanAutoLoadStore,
            proxy: {
                type: objContacto.AJAX,
                url: urlGetAdmiTitulo,
                timeout: objContacto.intTimeOut,
                reader: {
                    type: objContacto.JSON,
                    root: 'registros'
                },
                extraParams: {
                    strEstado: 'Activo'
                },
                simpleSortMode: true
            }
        });
    };

    this.objComboTitulo = function() {
        var objContacto = new Contacto();
        return new Ext.create('Ext.form.ComboBox', {
            id: 'cbxIdAdmiTitulo',
            fieldLabel: 'Titulo',
            style: Utils.STYLE_BOLD,
            store: objContacto.storeAdmiTitulo(),
            queryMode: 'local',
            editable: false,
            displayField: 'strDescripcionTitulo',
            valueField: 'intIdTitulo',
            width: objContacto.intWidthField
        });
    };

    this.objComboEscalabilidad = function () {
        var objContacto = new Contacto();
        return new Ext.create('Ext.form.ComboBox', {
            id: 'cbxEscalabilidad',
            fieldLabel: 'Escalabilidad',
            style: Utils.STYLE_BOLD,
            store: objContacto.objStoreEscalabilidad(),
            queryMode: 'local',
            editable: false,
            displayField: 'strEscalabilidadContacto',
            valueField: 'strEscalabilidadContacto',
            width: objContacto.intWidthField
        });
    };

    this.objComboHorario = function () {
        var objContacto = new Contacto();
        return new Ext.create('Ext.form.ComboBox', {
            id: 'cbxHorarios',
            fieldLabel: 'Horarios',
            style: Utils.STYLE_BOLD,
            store: objContacto.objStoreHorario(),
            queryMode: 'local',
            editable: false,
            displayField: 'strHorarioContacto',
            valueField: 'strHorarioContacto',
            width: objContacto.intWidthField
        });
    };

    this.objTxtUsrCreacion = Utils.objText();
    this.objTxtUsrCreacion.id = 'txtIdUsrCreacion';
    this.objTxtUsrCreacion.name = 'txtNameUsrCreacion';
    this.objTxtUsrCreacion.fieldLabel = 'Usuario Creacion';
    this.objTxtUsrCreacion.regex = Utils.REGEX_ALFA_LET_SPACE;
    this.objTxtUsrCreacion.msgTarget = 'under';
    this.objTxtUsrCreacion.style = Utils.STYLE_BOLD;
    this.objTxtUsrCreacion.invalidText = 'No se permiten numeros o caracteres especiales';
    this.objTxtUsrCreacion.width = this.intWidthField;

    this.objComboEstado = function() {

        var objContacto = new Contacto();

        this.modelEstado = Ext.define('modelEstado', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idestado', type: 'string'},
                {name: 'codigo', type: 'string'},
                {name: 'descripcion', type: 'string'}
            ]
        });

        var objStoreEstado = Ext.create('Ext.data.Store', {
            id: 'storeIdEstado',
            model: this.modelEstado,
            autoLoad: this.booleanAutoLoadStore,
            proxy: {
                type: this.AJAX,
                url: url_contacto_lista_estados,
                timeout: this.intTimeOut,
                reader: {
                    type: this.JSON,
                    root: 'estados'
                },
                simpleSortMode: true
            }
        });

        return new Ext.create('Ext.form.ComboBox', {
            id: 'cbxIdEstado',
            fieldLabel: 'Estado',
            style: Utils.STYLE_BOLD,
            store: objStoreEstado,
            queryMode: 'local',
            editable: false,
            displayField: 'descripcion',
            valueField: 'descripcion',
            width: objContacto.intWidthField
        });
    };

    /* FIN : Declaracion de campos del filter panel*/

    /* INICIO: Declaracion de grid de cliente y punto */

    this.modelContacto = Ext.define('ContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdPersona', type: 'int'},
            {name: 'strNombres', type: 'string'},
            {name: 'strApellidos', type: 'string'},
            {name: 'intIdTitulo', type: 'int'},
            {name: 'strTitulo', type: 'string'},
            {name: 'strTitulo', type: 'string'},
            {name: 'strTipoContacto', type: 'string'},
            {name: 'strIdentificacionCliente', type: 'string'},
            {name: 'dateFeCreacion', type: 'string'},
            {name: 'strUsuarioCreacion', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUrlShow', type: 'string'},
            {name: 'strUrlEdit', type: 'string'},
            {name: 'strUrlDelet', type: 'string'}
        ]
    });

    this.objStoreCliente = function() {
        return Ext.create('Ext.data.Store', {
            id: 'storeIdContactoCliente',
            pageSize: this.intPageSizeCliente,
            model: this.modelContacto,
            autoLoad: this.booleanAutoLoadStore,
            proxy: {
                type: this.AJAX,
                url: urlGridContactos,
                timeout: this.intTimeOut,
                reader: {
                    type: this.JSON,
                    root: 'jsonContactos',
                    totalProperty: 'intTotalContactos'
                },
                extraParams: {
                    strDescripcionTipoRol: 'Contacto',
                    intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                    intIdPunto: intIdPunto,
                    strJoinPunto: '',
                    strGroupBy: 'GROUP'
                },
                simpleSortMode: true
            }
        });
    };

    this.objStorePunto = function() {
        return Ext.create('Ext.data.Store', {
            id: 'storeIdContactoPunto',
            pageSize: this.intPageSizeCliente,
            model: this.modelContacto,
            autoLoad: this.booleanAutoLoadStore,
            proxy: {
                type: this.AJAX,
                url: urlGridContactos,
                timeout: this.intTimeOut,
                reader: {
                    type: this.JSON,
                    root: 'jsonContactos',
                    totalProperty: 'intTotalContactos'
                },
                extraParams: {
                    strDescripcionTipoRol: 'Contacto',
                    intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                    intIdPunto: intIdPunto,
                    strJoinPunto: 'BUSCA_POR_PUNTO',
                    strGroupBy: 'GROUP'
                },
                simpleSortMode: true
            }
        });
    };

    /* FIN: Declaracion de grid de cliente y punto */

    /*INICIO: Objetos GRID formas de contacto */

    this.modelAdmiFormaContacto = Ext.define('modelAdmiFormaContacto', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdFormaContacto', type: 'int'},
            {name: 'strDescripcionFormaContacto', type: 'string'},
            {name: 'strEstado', type: 'string'}
        ]
    });

    this.storeAdmiFormaContacto = function() {
        return new Ext.create('Ext.data.Store', {
            model: this.modelAdmiFormaContacto,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlGetAdmiFormaContacto,
                timeout: 90000,
                reader: {
                    type: 'json',
                    root: 'registros'
                },
                extraParams: {
                    strEstado: 'Activo'
                },
                simpleSortMode: true
            }
        });
    };

    this.objStoreEscalabilidad = function () {
        this.modelEscalabilidadContacto = Ext.define('modelEscalabilidadContacto', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdEscalabilidadContacto', type: 'int'},
                {name: 'strEscalabilidadContacto', type: 'string'}
            ]
        });
        return new Ext.create('Ext.data.Store', {
            id: 'cbxEscalabilidad',
            model: this.modelEscalabilidadContacto,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlGetEscalabilidadContacto,
                timeout: 90000,
                reader: {
                    type: 'json',
                    root: 'registros'
                },
                extraParams: {
                    strEstado: 'Activo'
                },
                simpleSortMode: true
            }
        });
    }

    this.objStoreHorario = function () {
        this.modelHorarioContacto = Ext.define('modelHorarioContacto', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdHorarioContacto', type: 'int'},
                {name: 'strHorarioContacto', type: 'string'}
            ]
        });
        return new Ext.create('Ext.data.Store', {
            id: 'cbxHorarios',
            model: this.modelHorarioContacto,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlGetHorarioContacto,
                timeout: 90000,
                reader: {
                    type: 'json',
                    root: 'registros'
                },
                extraParams: {
                    strEstado: 'Activo'
                },
                simpleSortMode: true
            }
        });
    }

    
    this.modelPersonaFormaContactoModel = Ext.define('personaFormaContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdPersonaFormaContacto', type: 'integer'},
            {name: 'strDescripcionFormaContacto', type: 'string'},
            {name: 'strValor', type: 'string'},
            {name: 'strEstadoPersonaFormaContacto', type: 'string'}
        ]
    });

    this.storeCreaPersonaFormaContacto = function() {
        return new Ext.create('Ext.data.Store', {
            model: this.modelPersonaFormaContactoModel,
            id: 'storeFormaContactoDelContacto',
            autoDestroy: true,
            autoLoad: false,
            proxy: {
                timeout: 400000,
                type: 'ajax',
                url: urlGetInfoPersonaFormaContacto,
                reader: {
                    type: 'json',
                    root: 'registros',
                    totalProperty: 'total'
                },
                simpleSortMode: true
            }
        });
    };

    this.objComboMultiSelectRol = function(objScope) {

        Ext.define('cboSelectedCountTipoContactoNew', {
            alias: 'plugin.selectedCountNew',
            init: function(cboSelectedCountTipoContactoNew) {
                cboSelectedCountTipoContactoNew.on({
                    select: function(me, objRecords) {
                        intNumeroRegistros = objRecords.length;
                        storeCboSelectedCountTipoContactoNew = cboSelectedCountTipoContactoNew.getStore();
                        boolDiffRowCbo = objRecords.length !== storeCboSelectedCountTipoContactoNew.count;
                        boolNewAll = false;
                        boolSelectedAll = false;
                        objNewRecords = [];
                        Ext.each(objRecords, function(obj, i, objRecordsItself) {
                            if (objRecords[i].data.intIdEmpresaRol === 0) {
                                boolSelectedAll = true;
                                if (!cboSelectedCountTipoContactoNew.boolCboSelectedAll) {
                                    intNumeroRegistros = storeCboSelectedCountTipoContactoNew.getCount();
                                    cboSelectedCountTipoContactoNew.select(storeCboSelectedCountTipoContactoNew.getRange());
                                    cboSelectedCountTipoContactoNew.boolCboSelectedAll = true;
                                    boolNewAll = true;
                                }
                            } else {
                                if (boolDiffRowCbo && !boolNewAll)
                                    objNewRecords.push(objRecords[i]);
                            }
                        });
                        if (cboSelectedCountTipoContactoNew.boolCboSelectedAll && !boolSelectedAll) {
                            cboSelectedCountTipoContactoNew.clearValue();
                            cboSelectedCountTipoContactoNew.boolCboSelectedAll = false;
                        } else if (boolDiffRowCbo && !boolNewAll) {
                            cboSelectedCountTipoContactoNew.select(objNewRecords);
                            cboSelectedCountTipoContactoNew.boolCboSelectedAll = false;
                        }
                    }
                });
            }
        });

        return new Ext.create('Ext.form.ComboBox', {
            disabled: false,
            multiSelect: true,
            plugins: 'selectedCountNew',
            id: objScope.id,
            fieldLabel: 'Tipo Contacto',
            store: objScope.store,
            queryMode: 'local',
            editable: false,
            displayField: 'strDescripcionRol',
            valueField: 'intIdEmpresaRol',
            labelStyle: Utils.STYLE_BOLD,
            width: objScope.intWidth,
            displayTpl: '<tpl for="."> {' + 'strDescripcionRol' + '} <tpl if="xindex < xcount">, </tpl> </tpl>',
            listConfig: {
                itemTpl: '{' + 'strDescripcionRol' + '} <div class="uncheckedChkbox"></div>'
            }, listeners: {
                select: function (combo)
                {
                    var strTipoContacto = combo.getRawValue();
                    if (!Utils.existStringIn("ESCALABLE", strTipoContacto.toUpperCase()))
                    {
                        Ext.getCmp('cbxEscalabilidad').hide();
                        Ext.getCmp('cbxHorarios').hide();
                    } else
                    {
                        Ext.getCmp('cbxEscalabilidad').show();
                        Ext.getCmp('cbxHorarios').show();
                    }
                }
            }
        });
    };


    this.groupRadioButtonIdentificacion = function() {

        var chkBoxNo = new Ext.form.Radio({
            boxLabel: 'No',
            id: 'chkBoxNo',
            name: 'grTipoIdenfiticacion',
            inputValue: 'chkBoxNo',
            checked: true
        });

        var chkBoxCedula = new Ext.form.Radio({
            boxLabel: 'Cedula',
            id: 'chkBoxCedula',
            name: 'grTipoIdenfiticacion',
            inputValue: 'chkBoxCedula'
        });

        var chkBoxPasaporte = new Ext.form.Radio({
            boxLabel: 'Pasaporte',
            id: 'chkBoxPasaporte',
            name: 'grTipoIdenfiticacion',
            inputValue: 'chkBoxPasaporte'
        });

        var chkBoxRuc = new Ext.form.Radio({
            boxLabel: 'Ruc',
            id: 'chkBoxRuc',
            name: 'grTipoIdenfiticacion',
            inputValue: 'chkBoxRuc'
        });

        return new Ext.form.RadioGroup({
            id: 'rbValueTipoIdentificacion',
            fieldLabel: 'Ingresar identificacion',
            style: 'font-weight:bold;',
            colspan: 3,
            columns: 4,
            width: 450,
            items: [chkBoxNo, chkBoxCedula, chkBoxRuc, chkBoxPasaporte]
        });
    };

    /* Checkbox para confirmar alcance masivo al editar contacto*/
    this.objChkAlcance = function(){
        return Ext.create('Ext.form.field.Checkbox', {
            fieldLabel : 'Alcance masivo',
            boxLabel : 'Editar a nivel de <b>cliente</b> y <b>puntos</b>',
            name : 'AlcanceMasivo',
            width: 350,
            checked: true,
            labelStyle: Utils.STYLE_BOLD,
            align: Utils.ALIGN_LEFT,
            hidden: true
        });
    };

    /*FIN: Objetos GRID formas de contacto */

    /* INICIO: Crea windows de ver informacion contacto */

    this.verEditarInformacionContacto = function(objScope) {

        var objContacto = new Contacto();

        var booleanPermiteEditar = false;
        var arrayPersonaFormaContactoEdit = new Array();
        var arrayPersonaFormaContactoDelete = new Array();


        var objWindowsInfoContacto = Utils.windows();
        objWindowsInfoContacto.id = 'windowsIdInfoContacto';
        objWindowsInfoContacto.title = 'Contacto';
        objWindowsInfoContacto.height = this.intHeightWindowInfoCont;
        objWindowsInfoContacto.width = this.intWidthWindowInfoCont;
        objWindowsInfoContacto.modal = true;
        objWindowsInfoContacto.resizable = false;

        var btnCancelar = Utils.button();
        btnCancelar.text = 'Cancelar edicion';
        btnCancelar.iconCls = "iconDeleteBook";
        btnCancelar.hidden = true;
        btnCancelar.on('click', function() {

            arrayPersonaFormaContactoEdit = new Array();
            arrayPersonaFormaContactoDelete = new Array();
            storeCreaPersonaFormaContacto.load();

            storeCreaPersonaFormaContacto.load();
            booleanPermiteEditar = false;
            btnDeletePersonaFormaContacto.disable();
            btnCrearPersonaFormaContacto.disable();

            btnCancelar.hide();
            Ext.getCmp('displayTxtIdTitulo').show();
            Ext.getCmp('displayTxtIdNombre').show();
            Ext.getCmp('displayTxtIdApellido').show();
            Ext.getCmp('displayTxtIdIdentificacion').show();

            Ext.getCmp('cbxIdAdmiTitulos').hide();
            objTxtNombres.hide();
            objTxtApellidos.hide();
            btnEditar.show();
            btnGuardar.hide();
        });

        var btnEditar = Utils.button();
        btnEditar.text = 'Editar';
        btnEditar.iconCls = "iconEditBook";
        btnEditar.on('click', function() {
            booleanPermiteEditar = true;
            arrayPersonaFormaContactoEdit = new Array();
            arrayPersonaFormaContactoDelete = new Array();
            btnDeletePersonaFormaContacto.enable();
            btnCrearPersonaFormaContacto.enable();
            btnEditar.hide();
            Ext.getCmp('displayTxtIdTitulo').hide();
            Ext.getCmp('displayTxtIdNombre').hide();
            Ext.getCmp('displayTxtIdApellido').hide();
            Ext.getCmp('displayTxtIdIdentificacion').hide();

            Ext.getCmp('cbxIdAdmiTitulos').select(objScope.intIdTitulo);
            objTxtNombres.setValue(Ext.getCmp('displayTxtIdNombre').getValue());
            objTxtApellidos.setValue(Ext.getCmp('displayTxtIdApellido').getValue());
            Ext.getCmp('cbxIdAdmiTitulos').show();
            objTxtNombres.show();
            objTxtApellidos.show();

            btnGuardar.show();
            btnCancelar.show();
            objFormInfoContacto.setHeight(495);
        });

        var btnGuardar = Utils.button();
        btnGuardar.text = 'Guardar';
        btnGuardar.iconCls = "iconSave";
        btnGuardar.hidden = true;
        btnGuardar.on('click', function() {
            var boolCorreo = false;
            var boolActualizar = true;
            var arrayGridCreaPersonaFormaContacto = Ext.getCmp('gridCreaPersonaFormaContacto');
            var arrayPersonaFormaContacto = new Object();
            arrayPersonaFormaContacto['intTotal'] = arrayGridCreaPersonaFormaContacto.getStore().getCount();
            arrayPersonaFormaContacto['arrayEdit'] = new Array();
            arrayPersonaFormaContacto['arrayDelete'] = new Array();
            arrayPersonaFormaContacto['arrayNew'] = new Array();
            var arrayPersFormaContactoNew = new Array();
            var arrayPersFormaContactoEdit = new Array();
            var jsonPersonaFormaContacto = '';
            if (arrayGridCreaPersonaFormaContacto.getStore().getCount() !== 0)
            {
                for (var intCounterStore = 0;
                    intCounterStore < arrayGridCreaPersonaFormaContacto.getStore().getCount(); intCounterStore++)
                {
                    if (Utils.existStringIn("CORREO", arrayGridCreaPersonaFormaContacto.getStore().getAt(intCounterStore).data.strDescripcionFormaContacto.trim().toUpperCase()))
                    {
                        boolCorreo = true;
                    }
                    if (0 === arrayGridCreaPersonaFormaContacto.getStore().getAt(intCounterStore).data.intIdPersonaFormaContacto) {
                        arrayPersFormaContactoNew.push(arrayGridCreaPersonaFormaContacto.getStore().getAt(intCounterStore).data);
                    }
                    if (-1 !== arrayPersonaFormaContactoEdit.indexOf(arrayGridCreaPersonaFormaContacto.getStore().getAt(intCounterStore).data.intIdPersonaFormaContacto)) {
                        arrayPersFormaContactoEdit.push(arrayGridCreaPersonaFormaContacto.getStore().getAt(intCounterStore).data);
                    }
                }

                if (boolCorreo) {
                    arrayPersonaFormaContacto['arrayNew'] = arrayPersFormaContactoNew;
                    arrayPersonaFormaContacto['arrayEdit'] = arrayPersFormaContactoEdit;
                    arrayPersonaFormaContacto['arrayDelete'] = arrayPersonaFormaContactoDelete;
                    jsonPersonaFormaContacto = Ext.JSON.encode(arrayPersonaFormaContacto);
                } else {
                    Ext.MessageBox.show({
                        title: 'Alerta',
                        msg: 'Debe ingresar al menos una forma de contacto de tipo <b>correo</b>.',
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.WARNING
                    });
                    boolActualizar = false;
                }

            } else {
                Ext.MessageBox.show({
                    title: 'Alerta',
                    msg: 'Debe ingresar al menos una forma de contacto.',
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.WARNING
                });
                boolActualizar = false;
            }

            if (boolActualizar) {

                if (objScope.intIdTitulo !== Ext.getCmp('cbxIdAdmiTitulos').getValue() ||
                    objScope.strNombre !== objTxtNombres.getValue() ||
                    objScope.strApellido !== objTxtApellidos.getValue() ||
                    0 !== arrayPersonaFormaContacto['arrayNew'].length ||
                    0 !== arrayPersonaFormaContacto['arrayEdit'].length ||
                    0 !== arrayPersonaFormaContacto['arrayDelete'].length
                    ) {
                    Ext.MessageBox.show({
                        title: 'Mensaje',
                        msg: 'Desea editar el contacto?',
                        buttons: Ext.MessageBox.OKCANCEL,
                        icon: Ext.MessageBox.WARNING,
                        fn: function(btn) {
                            if (btn === 'ok') {
                                Ext.MessageBox.show({
                                    msg: 'Editando contacto...',
                                    title: 'Editando',
                                    progressText: 'Editando...',
                                    progress: true,
                                    closable: false,
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });

                                Ext.Ajax.request({
                                    url: urlEditarContacto,
                                    method: 'POST',
                                    async: true,
                                    timeout: 60000,
                                    params: {
                                        intIdPersona: objScope.intIdPersona,
                                        intIdTitulo: Ext.getCmp('cbxIdAdmiTitulos').getValue(),
                                        strNombres: objTxtNombres.getValue(),
                                        strApellidos: objTxtApellidos.getValue(),
                                        jsonPersonaFormaContacto: jsonPersonaFormaContacto,
                                        strTipoConsulta: objScope.strTipoInsert,
                                        intIdPersonaEmpresaRol: objScope.intIdPersonaEmpresaRol,
                                        intIdPunto: objScope.intIdPunto,
                                        strEstado: 'Activo'
                                    },
                                    success: function(response) {
                                        var text = Ext.decode(response.responseText);
                                        Ext.Msg.alert(Utils.arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                        if ("100" === text.strStatus) {
                                            Ext.getCmp('cbxIdAdmiTitulos').hide();
                                            objTxtNombres.hide();
                                            objTxtApellidos.hide();
                                            Ext.getCmp('displayTxtIdTitulo').show();
                                            Ext.getCmp('displayTxtIdTitulo').setValue(Ext.getCmp('cbxIdAdmiTitulos').getRawValue());
                                            Ext.getCmp('displayTxtIdNombre').show();
                                            Ext.getCmp('displayTxtIdNombre').setValue(objTxtNombres.getValue());
                                            Ext.getCmp('displayTxtIdApellido').show();
                                            Ext.getCmp('displayTxtIdApellido').setValue(objTxtApellidos.getValue());
                                            Ext.getCmp('displayTxtIdIdentificacion').show();

                                            objScope.strTitulo = Ext.getCmp('cbxIdAdmiTitulos').getRawValue();
                                            objScope.strNombre = objTxtNombres.getValue();
                                            objScope.strApellido = objTxtApellidos.getValue();
                                            objScope.intIdTitulo = Ext.getCmp('cbxIdAdmiTitulos').getValue();

                                            storeCreaPersonaFormaContacto.load();
                                            booleanPermiteEditar = false;
                                            btnDeletePersonaFormaContacto.disable();
                                            btnCrearPersonaFormaContacto.disable();
                                            btnCancelar.hide();
                                            btnGuardar.hide();
                                            btnEditar.show();
                                        }
                                    },
                                    failure: function(result) {
                                        Ext.Msg.alert('Error ', result.statusText);
                                    }
                                });
                            } else {
                                return;
                            }
                        }
                    });
                } else {
                    Ext.Msg.alert('Informacion!', 'No se ha realizado ningun cambio <b>(:</b> !');
                }
            }
        });

        var btnCerrar = Utils.button();
        btnCerrar.text = 'Cerrar';
        btnCerrar.iconCls = "icon_cerrar";
        btnCerrar.on('click', function() {
            objWindowsInfoContacto.close();
            objWindowsInfoContacto.destroy();
        });

        var storeAdmiFormaContacto = objContacto.storeAdmiFormaContacto();

        var storeCreaPersonaFormaContacto = objContacto.storeCreaPersonaFormaContacto();
        storeCreaPersonaFormaContacto.proxy.extraParams = {
            intIdPersona: objScope.intIdPersona,
            strEstado: 'Activo'
        };
        storeCreaPersonaFormaContacto.load();
        var btnCrearPersonaFormaContacto = Ext.create('Ext.button.Button', {
            text: 'Agregar forma contacto',
            width: 160,
            iconCls: 'button-grid-crearSolicitud-without-border',
            handler: function() {
                Ext.getCmp('btnDeletePersonaFormaContacto').enable();
                rowEditingPersFormaContacto.cancelEdit();

                var recordParamDet = Ext.create('personaFormaContactoModel', {
                    intIdPersonaFormaContacto: 0,
                    strDescripcionFormaContacto: '',
                    strValor: '',
                    strEstadoPersonaFormaContacto: 'Activo'
                });
                storeCreaPersonaFormaContacto.insert(0, recordParamDet);
                rowEditingPersFormaContacto.startEdit(0, 0);
                if (Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getCount() > 1)
                {
                    if ('' === Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(1).data.strDescripcionFormaContacto.trim() ||
                        '' === Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(1).data.strValor.trim())
                    {
                        Ext.Msg.alert('Error', 'No puede ingresar registros en blanco.');
                        var selectionModel = Ext.getCmp('gridCreaPersonaFormaContacto').getSelectionModel();
                        rowEditingPersFormaContacto.cancelEdit();
                        storeCreaPersonaFormaContacto.remove(selectionModel.getSelection());
                        selectionModel.select(0);
                        rowEditingPersFormaContacto.startEdit(0, 0);
                    }
                }
            }
        });

        var btnDeletePersonaFormaContacto = Ext.create('Ext.button.Button', {
            text: 'Eliminar forma de contacto',
            id: 'btnDeletePersonaFormaContacto',
            width: 160,
            iconCls: 'button-grid-quitarFacturacionElectronica-without-border',
            handler: function() {
                var gridCreaPersonaFormaContacto = Ext.getCmp('gridCreaPersonaFormaContacto');
                var selectionModel = gridCreaPersonaFormaContacto.getSelectionModel();
                if (0 !== selectionModel.selected.length) {
                    for (var intForIndex = 0; intForIndex < selectionModel.getSelection().length; intForIndex++)
                    {
                        arrayPersonaFormaContactoDelete.push(selectionModel.getSelection()[intForIndex].data.intIdPersonaFormaContacto);
                        if (0 !== selectionModel.getSelection()[intForIndex].data.intIdPersonaFormaContacto) {
                            if (-1 === arrayPersonaFormaContactoDelete.indexOf(selectionModel.getSelection()[intForIndex].data.intIdPersonaFormaContacto)) {
                                arrayPersonaFormaContactoDelete.push(selectionModel.getSelection()[intForIndex].data.intIdPersonaFormaContacto);
                            }
                        }
                    }
                    rowEditingPersFormaContacto.cancelEdit();
                    storeCreaPersonaFormaContacto.remove(selectionModel.getSelection());
                    if (storeCreaPersonaFormaContacto.getCount() > 0) {
                        selectionModel.select(0);
                    }
                    if (0 === storeCreaPersonaFormaContacto.getCount()) {
                        Ext.getCmp('btnDeletePersonaFormaContacto').disable();
                    }
                } else {
                    Ext.Msg.alert('Alerta   !', 'Debe seleccionar una fila para eliminar');
                }
            }
        });

        btnDeletePersonaFormaContacto.disable();
        btnCrearPersonaFormaContacto.disable();

        var toolbarPersFormaContacto = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'top',
            align: '->',
            items:
                [{xtype: 'tbfill'},
                    btnCrearPersonaFormaContacto,
                    btnDeletePersonaFormaContacto
                ]
        });

        /**
         * 
         * Esta función permite crear un grid para ingresar formas de contato.
         * 
         * @version 1.00
         * 
         * Se agrega validación de telefono internacional.
         * 
         * @author Héctor Ortega <haortega@telconet.ec>
         * @version 1.00, 29/11/2016
         * 
         * @author Luis Cabrera <lcabrera@telconet.ec>
         * @version 1.1 06-07-2017
         * Se agregan las validaciones para los teléfonos de Panamá
         */
        var rowEditingPersFormaContacto = Ext.create('Ext.grid.plugin.RowEditing', {
            saveBtnText: 'Guardar',
            cancelBtnText: 'Cancelar',
            clicksToMoveEditor: false,
            autoCancel: false,
            errorsText: 'Error',
            dirtyText: 'Necesitas guardar o cancelar para selecionar otra fila.',
            listeners: {
                canceledit: function(roweditor, changes, record, rowIndex) {
                    var selectionModel = Ext.getCmp('gridCreaPersonaFormaContacto').getSelectionModel();
                    if (0 === changes.record.data.intIdPersonaFormaContacto &&
                        Ext.isEmpty(changes.record.data.strDescripcionFormaContacto.trim()) &&
                        Ext.isEmpty(changes.record.data.strValor.trim())) {
                        changes.store.remove(changes.record);
                    }

                    if (0 === changes.record.data.intIdPersonaFormaContacto &&
                        (Ext.isEmpty(Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(0).get('strDescripcionFormaContacto').trim()) ||
                            Ext.isEmpty(Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(0).get('strValor').trim()))) {
                        changes.store.remove(Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(0));
                    }

                    if (Utils.existStringIn("CORREO", changes.record.data.strDescripcionFormaContacto.trim().toUpperCase()))
                    {
                        if (!Utils.validateMail(changes.record.data.strValor.trim())) {
                            Ext.Msg.alert('Error', 'El formato de correo no es correcto, favor revisar.');
                            selectionModel.select(changes.rowIdx);
                            rowEditingPersFormaContacto.startEdit(changes.rowIdx, changes.colIdx);
                            return false;
                        }
                    }

                    if (Utils.existStringIn("TELEFONO", changes.record.data.strDescripcionFormaContacto.trim().toUpperCase()))
                    {
                        if (Utils.existStringIn("TELEFONO INTERNACIONAL",
                            changes.record.data.strDescripcionFormaContacto.trim().toUpperCase()))
                        {
                            if (!Utils.validateFoneMin7Max15(changes.record.data.strValor.trim())) {
                                Ext.Msg.alert('Error', 'El formato de teléfono internacional no es correcto.! <br>' +
                                    'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                                    'Se permite un <b>mínimo de 7 dígitos y un máximo de 15 dígitos</b>. <br>' +
                                    'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.');
                                selectionModel.select(changes.rowIdx);
                                rowEditingPersFormaContacto.startEdit(changes.rowIdx, changes.colIdx);
                                return false;
                            }
                        } 
                        else
                        {
                            if (!Utils.validateFoneMin8Max10(changes.record.data.strValor.trim())) {
                                Ext.Msg.alert('Error', 'El formato de teléfono no es correcto.! <br>' +
                                    'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                                    'Se permite un <b>mínimo de 8 dígitos y un maximo de 10 dígitos</b>. <br>' +
                                    'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.');
                                selectionModel.select(changes.rowIdx);
                                rowEditingPersFormaContacto.startEdit(changes.rowIdx, changes.colIdx);
                                return false;
                            }
                        }


                    }
                },
                beforeedit: function(editor, context) {

                    if ('Eliminado' === context.record.getData().strEstadoPersonaFormaContacto || !booleanPermiteEditar) {
                        return false;
                    }
                    return true;

                },
                afteredit: function(roweditor, changes, record, rowIndex) {
                    var intCountGridDetalle = Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getCount();
                    var selectionModel = Ext.getCmp('gridCreaPersonaFormaContacto').getSelectionModel();

                    if (intCountGridDetalle > 0)
                    {
                        if (Ext.isEmpty(changes.newValues.strDescripcionFormaContacto.trim()) ||
                            Ext.isEmpty(changes.newValues.strValor.trim())) {
                            Ext.Msg.alert('Error', 'No puede ingresar registros en blanco.');
                            selectionModel.getSelection()[0].set('strDescripcionFormaContacto', selectionModel.selected.items[0].raw.strDescripcionFormaContacto.trim());
                            selectionModel.getSelection()[0].set('strValor', selectionModel.selected.items[0].raw.strValor.trim());
                            selectionModel.select(changes.rowIdx);
                            rowEditingPersFormaContacto.startEdit(changes.rowIdx, changes.colIdx);
                            return false;
                        }
                    }
                    if (Ext.isEmpty(Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(0).get('strDescripcionFormaContacto').trim()) ||
                        Ext.isEmpty(Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(0).get('strValor').trim())) {
                        selectionModel.select(changes.rowIdx);
                        rowEditingPersFormaContacto.startEdit(changes.rowIdx, changes.colIdx);
                        return false;
                    }
                    if (Utils.existStringIn("CORREO", changes.newValues.strDescripcionFormaContacto.trim().toUpperCase()))
                    {
                        if (!Utils.validateMail(changes.newValues.strValor.trim())) {
                            Ext.Msg.alert('Error', 'El formato de correo no es correcto, favor revisar.');
                            selectionModel.select(changes.rowIdx);
                            rowEditingPersFormaContacto.startEdit(changes.rowIdx, changes.colIdx);
                            return false;
                        }
                    }

                    if (Utils.existStringIn("TELEFONO", changes.newValues.strDescripcionFormaContacto.trim().toUpperCase()))
                    {
                        if (Utils.existStringIn("TELEFONO INTERNACIONAL",
                            changes.newValues.strDescripcionFormaContacto.trim().toUpperCase()))
                        {
                            if (!Utils.validateFoneMin7Max15(changes.newValues.strValor.trim())) {
                                Ext.Msg.alert('Error', 'El formato de teléfono internacional no es correcto.! <br>' +
                                    'Se permiten solo numeros entre <b>[0-9]</b>. <br>' +
                                    'Se permite un <b>mínimo de 7 digitos y un maximo de 15 dígitos</b>. <br>' +
                                    'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.');
                                selectionModel.select(changes.rowIdx);
                                rowEditingPersFormaContacto.startEdit(changes.rowIdx, changes.colIdx);
                                return false;
                            }

                        } else {
                            if (strNombrePais === "PANAMA")
                            {
                                if (Utils.existStringIn("TELEFONO FIJO", changes.newValues.strDescripcionFormaContacto.trim().toUpperCase())
                                    && !(/^(\+?\d{1,3}?[- .]?\d{1,3}[- .]?\d{1,4})$/.test(changes.newValues.strValor.trim())))
                                {
                                    Ext.Msg.alert('Error', 'El formato de teléfono fijo no es correcto.! <br>' +
                                        'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                                        'Se permite mínimo <b>7 dígitos</b>. <br>' +
                                        'No se permiten <b>caracteres especiales excepto + - . </b>, favor revisar.');
                                    rowEditingPersFormaContacto.startEdit(0, 0);
                                    return false;
                                }
                                if (Utils.existStringIn("TELEFONO MOVIL", changes.newValues.strDescripcionFormaContacto.trim().toUpperCase())
                                    && !(/^[0-9]{8}$/.test(changes.newValues.strValor.trim())))
                                {
                                    Ext.Msg.alert('Error', 'El formato de teléfono móvil no es correcto.! <br>' +
                                        'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                                        'Solo se permiten <b>8 dígitos</b>. <br>' +
                                        'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.');
                                    rowEditingPersFormaContacto.startEdit(0, 0);
                                    return false;
                                }
                            } else
                            {
                                if (!Utils.validateFoneMin8Max10(changes.newValues.strValor.trim())) {
                                    Ext.Msg.alert('Error', 'El formato de teléfono no es correcto.! <br>' +
                                        'Se permiten solo numeros entre <b>[0-9]</b>. <br>' +
                                        'Se permite un <b>mínimo de 8 dígitos y un maximo de 10 dígitos</b>. <br>' +
                                        'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.');
                                    selectionModel.select(changes.rowIdx);
                                    rowEditingPersFormaContacto.startEdit(changes.rowIdx, changes.colIdx);
                                    return false;
                                }
                            }

                        }


                    }

                    for (var i = 0; i < intCountGridDetalle; i++)
                    {
                        if (i !== changes.rowIdx &&
                            Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(i).get('strDescripcionFormaContacto') === changes.newValues.strDescripcionFormaContacto.trim() &&
                            Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(i).get('strValor') === changes.newValues.strValor.trim())
                        {
                            Ext.Msg.alert('Error', 'Esta forma de contacto <b>' +
                                changes.newValues.strDescripcionFormaContacto + ': ' +
                                changes.newValues.strValor + '</b>' +
                                ' ya se encuentra previamente ingresada.');
                            rowEditingPersFormaContacto.startEdit(changes.rowIdx, changes.colIdx);
                            break;
                        }
                    }
                    if (0 !== selectionModel.getSelection()[0].get('intIdPersonaFormaContacto')) {
                        if (changes.originalValues.strDescripcionFormaContacto.trim() !== changes.newValues.strDescripcionFormaContacto.trim() ||
                            changes.originalValues.strValor.trim() !== changes.newValues.strValor.trim()) {
                            if (-1 === arrayPersonaFormaContactoEdit.indexOf(selectionModel.getSelection()[0].get('intIdPersonaFormaContacto'))) {
                                arrayPersonaFormaContactoEdit.push(selectionModel.getSelection()[0].get('intIdPersonaFormaContacto'));
                            }
                        }
                    }
                }
            }
        });


        var storeAdmiTitulo = objContacto.storeAdmiTitulo();

        var objTxtNombres = Utils.objText();
        objTxtNombres.id = 'txtIdNombres';
        objTxtNombres.name = 'txtNameNombres';
        objTxtNombres.fieldLabel = 'Nombres';
        objTxtNombres.regex = Utils.REGEX_ALFA_LET_SPACE;
        objTxtNombres.msgTarget = 'under';
        objTxtNombres.style = Utils.STYLE_BOLD;
        objTxtNombres.invalidText = 'No se permiten numeros o caracteres especiales';
        objTxtNombres.hidden = true;
        objTxtNombres.colspan = 3;
        objTxtNombres.width = 300;

        var objTxtApellidos = Utils.objText();
        objTxtApellidos.id = 'txtIdApellidos';
        objTxtApellidos.name = 'txtNameApellidos';
        objTxtApellidos.fieldLabel = 'Apellidos';
        objTxtApellidos.regex = Utils.REGEX_ALFA_LET_SPACE;
        objTxtApellidos.msgTarget = 'under';
        objTxtApellidos.style = Utils.STYLE_BOLD;
        objTxtApellidos.invalidText = 'No se permiten numeros o caracteres especiales';
        objTxtApellidos.hidden = true;
        objTxtApellidos.colspan = 3;
        objTxtApellidos.width = 300;

        var objFormInfoContacto = Utils.form();
        objFormInfoContacto.id = 'formIdInfoContacto';
        objFormInfoContacto.height = this.intHeightWindowInfoCont+15;
        objFormInfoContacto.add({
            bodyStyle: 'padding: 0px 0px 0px 0px; border: none;',
            layout: {
                type: 'table',
                columns: 1,
                tableAttrs: {
                    style: {
                        width: '100%',
                        height: '100%'
                    }
                },
                tdAttrs: {
                    align: 'center'
                }
            },
            items: [
                {
                    xtype: 'toolbar',
                    style: {
                        background: '#FFFFFF',
                        border: 'none'
                    },
                    items: [
                        {
                            xtype: 'tbfill'
                        },
                        btnEditar,
                        btnCancelar
                    ]
                },
                {
                    xtype: 'fieldset',
                    height: '100%',
                    width: '95%',
                    title: 'Información contacto',
                    layout: {
                        tdAttrs: {style: 'padding: 1px;'},
                        type: 'table',
                        columns: 3,
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'displayfield',
                            id: 'displayTxtIdTitulo',
                            colspan: 3,
                            fieldLabel: 'Titulo',
                            style: Utils.STYLE_BOLD,
                            align: Utils.ALIGN_LEFT,
                            value: objScope.strTitulo
                        },
                        {
                            xtype: 'displayfield',
                            id: 'displayTxtIdNombre',
                            colspan: 3,
                            fieldLabel: 'Nombres',
                            style: Utils.STYLE_BOLD,
                            textAlign: Utils.ALIGN_LEFT,
                            value: objScope.strNombre
                        },
                        {
                            xtype: 'displayfield',
                            id: 'displayTxtIdApellido',
                            colspan: 3,
                            fieldLabel: 'Apellido',
                            style: Utils.STYLE_BOLD,
                            align: Utils.ALIGN_LEFT,
                            value: objScope.strApellido
                        },
                        {
                            xtype: 'displayfield',
                            id: 'displayTxtIdIdentificacion',
                            colspan: 3,
                            fieldLabel: 'Identificacion',
                            style: Utils.STYLE_BOLD,
                            align: Utils.ALIGN_LEFT,
                            value: objScope.strIdentificacion
                        },
                        {
                            xtype: 'combobox',
                            colspan: 3,
                            store: storeAdmiTitulo,
                            labelAlign: 'left',
                            style: Utils.STYLE_BOLD,
                            name: 'cbxNameAdmiTitulos',
                            id: 'cbxIdAdmiTitulos',
                            valueField: 'intIdTitulo',
                            displayField: 'strDescripcionTitulo',
                            fieldLabel: 'Titulo',
                            width: 300,
                            hidden: true,
                            triggerAction: 'all',
                            queryMode: 'local',
                            allowBlank: true
                        },
                        objTxtNombres,
                        objTxtApellidos,
                    ]
                },
                {
                    xtype: 'grid',
                    id: 'gridCreaPersonaFormaContacto',
                    multiSelect: true,
                    store: storeCreaPersonaFormaContacto,
                    plugins: [rowEditingPersFormaContacto],
                    dockedItems: [toolbarPersFormaContacto],
                    width: objContacto.intWidthGridInfoCont,
                    height: objContacto.intHeightGridInfoCont,
                    columns: [
                        {
                            header: "Forma contacto",
                            dataIndex: 'strDescripcionFormaContacto',
                            width: 300,
                            editor: new Ext.form.field.ComboBox({
                                typeAhead: true,
                                id: 'cbxFormaContacto',
                                name: 'cbxFormaContacto',
                                valueField: 'strDescripcionFormaContacto',
                                displayField: 'strDescripcionFormaContacto',
                                store: storeAdmiFormaContacto,
                                editable: false
                            })
                        },
                        {
                            header: "Valor",
                            dataIndex: 'strValor',
                            width: 300,
                            editor: 'textfield'
                        },
                        {
                            header: "Estado",
                            dataIndex: 'strEstadoPersonaFormaContacto',
                            width: 80,
                            edit: false,
                            readOnly: true
                        }
                    ]
                }
            ],
            buttonAlign: 'center',
            buttons: [
                btnGuardar,
                btnCerrar
            ]
        });

        objWindowsInfoContacto.add({
            items: [
                objFormInfoContacto
            ]
        });

        objWindowsInfoContacto.show();
    };

    /* FIN: Crea windows de ver informacion contacto */

    /*INICIO: elimina tipo de contacto*/
    this.eliminarTipoContacto = function(jsonIdPersonaEmpresaRol, objStoreTipoContacto, storeTipoContacto, strTipo) {
        Ext.MessageBox.show({
            title: 'Mensaje',
            msg: 'Eliminar tipo de contacto?<br/><br/><div id="idchkalcance-group"><input type="checkbox" ' +
                'id="idchkalcance-eliminartipocontacto" style="margin-right: 10px; vertical-align: middle;" checked>' +
                '<label for="idchkalcance-eliminartipocontacto">Eliminar a nivel de <b>cliente</b> y <b>puntos</b>?' +
                '</label></div>',
            buttons: Ext.MessageBox.OKCANCEL,
            icon: Ext.MessageBox.WARNING,
            fn: function(btn) {
                var booleanChkAlcanceMasivo = Ext.getElementById('idchkalcance-eliminartipocontacto').checked;

                if (btn === 'ok') {
                    Ext.MessageBox.show({
                        msg: 'Eliminando tipo de contacto',
                        title: 'Eliminando',
                        progressText: 'Eliminando...',
                        progress: true,
                        closable: false,
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });

                    Ext.Ajax.request({
                        url: urlEliminaTipoContacto,
                        method: 'POST',
                        async: true,
                        timeout: booleanChkAlcanceMasivo ? 1200000 : 60000,
                        params: {
                            jsonIdPersonaEmpresaRol: jsonIdPersonaEmpresaRol,
                            strTipo: strTipo,
                            booleanAlcanceMasivo : booleanChkAlcanceMasivo
                        },
                        success: function(response) {
                            var text = Ext.decode(response.responseText);
                            Ext.Msg.alert(Utils.arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                            if ("100" === text.strStatus) {
                                objStoreTipoContacto.load();
                                storeTipoContacto.load();
                            }
                        },
                        failure: function(result) {
                            Ext.Msg.alert('Error ', result.statusText);
                        }
                    });
                } else {
                    return;
                }
            }
        });
    };
    /*FIN: elimina tipo de contacto*/

    /*INICIO: Ingresar tipo contactos*/
    this.ingresaTipoContacto = function(intIdPersona, objStore, strNombres, strTitulo, strTipoInsert, intIdPersonaEmpresaRol, intIdPunto) {
        var objContacto = new Contacto();
        var windowTipoContacto;
        var btnCrearTipoContacto = Ext.create('Ext.button.Button', {
            text: 'Agregar tipo contacto',
            scope: this,
            style: {
                margin: '20px'
            },
            handler: function() {
                if (!Ext.isEmpty(cbxAdmiRol.getValue().toString())) {
                    var strTipoContacto = cbxAdmiRol.getRawValue();
                    if (Utils.existStringIn("ESCALABLE", strTipoContacto.toUpperCase())) {
                        if (Ext.isEmpty(Ext.getCmp('cbxEscalabilidad').getValue())) {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Favor debe elegir un Nivel de Escalabilidad para el Contacto.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }else {
                            strTipoEscalabilidad=cbxEscalabilidadEdit.getValue().toString();
                        }
                        if (Ext.isEmpty(Ext.getCmp('cbxHorarios').getValue())) {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Favor debe elegir un Horario para el Contacto.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }else{
                            strTipoHorario=cbxHorariosEdit.getValue().toString()
                        }

                    }else{
                        strTipoEscalabilidad='';
                        strTipoHorario='';
                    } 
                    Ext.MessageBox.show({
                        title: 'Mensaje',
                        msg: 'Se asignaran nuevos tipos de contacto para ' + strTitulo + ' ' + strNombres + ', continuar?',
                        buttons: Ext.MessageBox.OKCANCEL,
                        icon: Ext.MessageBox.WARNING,
                        fn: function(btn) {
                            if (btn === 'ok') {
                                Ext.MessageBox.show({
                                    msg: 'Asignando tipo de contacto...',
                                    title: 'Asignando',
                                    progressText: 'Asignando...',
                                    progress: true,
                                    closable: false,
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                                Ext.Ajax.request({
                                    url: urlAsignaTipoContacto,
                                    method: 'POST',
                                    async: true,
                                    timeout: objChkAlcance.getValue() ? 600000 : 60000,
                                    params: {
                                        strEmpresaRol: cbxAdmiRol.getValue().toString(),
                                        intIdPersona: intIdPersona,
                                        strEscalabilidad: strTipoEscalabilidad,
                                        strHorariosContact: strTipoHorario,
                                        strTipoInsert: strTipoInsert,
                                        intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                                        intIdPunto: intIdPunto,
                                        strAlcanceMasivo: objChkAlcance.checked
                                    },
                                    success: function(response) {
                                        var text = Ext.decode(response.responseText);
                                        Ext.Msg.alert(Utils.arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                        if ("100" === text.strStatus) {
                                            if (windowTipoContacto) {
                                                cbxAdmiRol.setValue('');
                                                cbxAdmiRol.setRawValue('');
                                                objStoreTipoContacto.load();
                                                storeTipoContacto.load();
                                            }
                                        }
                                    },
                                    failure: function(result) {
                                        Ext.Msg.alert('Error ', result.statusText);
                                    }
                                });
                            } else {
                                return;
                            }
                        }
                    });
                } else {
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: 'Debe elegir al menos un tipo de contacto.',
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.WARNING
                    });
                }
            }
        });

        var btnCancelarTipoContacto = Ext.create('Ext.button.Button', {
            text: 'Cerrar',
            scope: this,
            style: {
                marginTop: '10px'
            },
            handler: function() {
                if (windowTipoContacto) {
                    windowTipoContacto.close();
                    windowTipoContacto.destroy();
                }
            }
        });

        var objScope = {
            extraParams: {
                strAppendRol: 'Todos',
                strDisponiblesPersona: 'BUSCA_DISPONIBLES',
                strEstadoTipoRol: 'Eliminado, Anulado, Inactivo',
                strDescripcionTipoRol: 'Contacto',
                strComparadorEstTipoRol: 'NOT IN',
                strEstadoRol: 'Eliminado, Anulado, Inactivo',
                strComparadorEstRol: 'NOT IN',
                strEstadoEmpresaRol: 'Eliminado, Anulado, Inactivo',
                strComparadorEmpRol: 'NOT IN',
                intIdPersona: intIdPersona,
                strComparadorPerEmpRolDis: 'NOT IN',
                strEstadoPerEmpRolDis: 'Eliminado, Anulado, Inactivo',
                strComparadorEmpRolDis: 'NOT IN',
                strEstadoEmpRolDis: 'Eliminado, Anulado, Inactivo'
            }
        };

        var objStoreTipoContacto = objContacto.objStoreRol(objScope);
        objStoreTipoContacto.store = objStoreTipoContacto;
        objStoreTipoContacto.id = 'cbxIdRol';
        objStoreTipoContacto.intWidth = 300;

        var cbxAdmiRol = objContacto.objComboMultiSelectRol(objStoreTipoContacto);
        cbxAdmiRol.colspan = 3;
        cbxAdmiRol.setWidth(350);

        cbxEscalabilidadEdit = objContacto.objComboEscalabilidad();
        cbxEscalabilidadEdit.id = 'cbxEscalabilidad';
        cbxEscalabilidadEdit.colspan = 3;
        cbxEscalabilidadEdit.setWidth(350);
        
        cbxHorariosEdit = objContacto.objComboHorario();
        cbxHorariosEdit.id = 'cbxHorarios';
        cbxHorariosEdit.colspan = 3;
        cbxHorariosEdit.setWidth(350);

        var objChkAlcance = this.objChkAlcance();
        objChkAlcance.hidden = false;
        objChkAlcance.boxLabel = 'Agregar a nivel de <b>cliente</b> y <b>puntos</b>';

        Ext.define('modelRol', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdRol', type: 'int'},
                {name: 'intIdPersonaEmpresaRol', type: 'int'},
                {name: 'strDescripcionRol', type: 'string'},
                {name: 'strEstado', type: 'string'},
                {name: 'strUsrCreacion', type: 'string'},
                {name: 'strFeCreacion', type: 'string'},
                {name: 'strUsrUltMod', type: 'string'},
                {name: 'strFeUltMod', type: 'string'}
            ]
        });

        var storeTipoContacto = Ext.create('Ext.data.Store', {
            pageSize: 5,
            autoLoad: false,
            model: 'modelRol',
            collapsible: false,
            autoScroll: true,
            proxy: {
                type: 'ajax',
                url: urlRolesPersonaPunto,
                timeout: 90000,
                reader: {
                    type: 'json',
                    root: 'registros',
                    totalProperty: 'total'
                },
                simpleSortMode: true
            },
            listeners: {
                beforeload: function(storeTipoContacto) {
                    storeTipoContacto.getProxy().extraParams.intIdPersona = intIdPersona;
                    storeTipoContacto.getProxy().extraParams.strTipoConsulta = strTipoInsert;
                    storeTipoContacto.getProxy().extraParams.strEstado = 'Activo';
                    storeTipoContacto.getProxy().extraParams.intIdPersonaEmpresaRol = intIdPersonaEmpresaRol;
                    storeTipoContacto.getProxy().extraParams.intIdPunto = intIdPunto;
                }
            }
        });
        storeTipoContacto.load();

        var btnEliminar = Ext.create('Ext.button.Button', {
            text: 'Eliminar tipo contacto',
            iconCls: 'button-grid-quitarFacturacionElectronica-without-border',
            scope: this,
            handler: function() {
                var arrayGridTipoContacto = Ext.getCmp('gridTipoContacto');
                var arrayTipoContacto = new Object();
                arrayTipoContacto['intTotal'] = arrayGridTipoContacto.getStore().getCount();
                arrayTipoContacto['arrayData'] = new Array();
                var arrayTipoContactoDelete = new Array();
                var jsonIdPersonaEmpresaRol = '';
                var selectionModel = arrayGridTipoContacto.getSelectionModel();
                if (0 !== selectionModel.selected.length) {
                    for (var intForIndex = 0; intForIndex < selectionModel.getSelection().length; intForIndex++)
                    {
                        arrayTipoContactoDelete.push(selectionModel.getSelection()[intForIndex].data.intIdPersonaEmpresaRol);
                    }
                    arrayTipoContacto['arrayData'] = arrayTipoContactoDelete;
                    jsonIdPersonaEmpresaRol = Ext.JSON.encode(arrayTipoContacto);
                    var objContacto = new Contacto();
                    objContacto.eliminarTipoContacto(jsonIdPersonaEmpresaRol, objStoreTipoContacto, storeTipoContacto, strTipoInsert);
                } else {
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: 'Debe seleccionar al menos una fila para eliminar',
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.WARNING
                    });
                }
            }
        });

        var toolbarTipoContacto = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'top',
            align: '->',
            heigth: 50,
            items:
                [{xtype: 'tbfill'},
                    btnEliminar
                ]
        });


        var gridTipoContacto = Ext.create('Ext.grid.Panel', {
            store: storeTipoContacto,
            labelStyle: 'padding: 10px 10px;',
            width: 688,
            height: 245,
            id: 'gridTipoContacto',
            dockedItems: [toolbarTipoContacto],
            autoScroll: false,
            multiSelect: true,
            columns: [
                {header: "ID", dataIndex: 'intIdRol', hidden: true},
                {header: 'Tipo Contacto', dataIndex: 'strDescripcionRol', width: 200},
                {header: 'Estado', dataIndex: 'strEstado', align: 'center', width: 100},
                {header: 'Usr. Creacion', dataIndex: 'strUsrCreacion', align: 'center', width: 140},
                {header: 'Fe. Creacion', dataIndex: 'strFeCreacion', align: 'center', width: 140},
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    align: 'center',
                    width: 106,
                    items: [
                        {
                            tooltip: 'Eliminar tipo contacto',
                            getClass: function(v, meta, rec) {
                                if ("Eliminado" !== rec.get('strEstado')) {
                                    return 'button-grid-delete button-point';
                                }
                                return 'none';
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                if (!Ext.isEmpty(grid.store.data.items[rowIndex].data.intIdPersonaEmpresaRol)) {
                                    var arrayTipoContacto = new Object();
                                    arrayTipoContacto['intTotal'] = 1;
                                    arrayTipoContacto['arrayData'] = new Array();
                                    var arrayTipoContactoDelete = new Array();
                                    var jsonIdPersonaEmpresaRol = '';
                                    arrayTipoContactoDelete.push(grid.store.data.items[rowIndex].data.intIdPersonaEmpresaRol);
                                    arrayTipoContacto['arrayData'] = arrayTipoContactoDelete;
                                    jsonIdPersonaEmpresaRol = Ext.JSON.encode(arrayTipoContacto);
                                    var objContacto = new Contacto();
                                    objContacto.eliminarTipoContacto(jsonIdPersonaEmpresaRol, objStoreTipoContacto, storeTipoContacto, strTipoInsert);
                                }
                            }
                        }
                    ]
                }

            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeTipoContacto,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            })
        });
        var formTipoContactos = Ext.create('Ext.form.Panel', {
            id: 'formTipoContactos',
            bodyStyle: 'padding: 20px 10px 0; background:#FFFFFF;',
            bodyPadding: 15,
            autoScroll: false,
            layout: {
                type: 'table',
                columns: 1,
                tableAttrs: {
                    style: {
                        width: '90%',
                        height: '90%'
                    }
                },
                tdAttrs: {
                    align: 'center',
                    valign: 'middle'
                }
            },
            items: [
                {
                    xtype: 'fieldset',
                    title: 'Agregar nuevo tipo de contacto para ' + strNombres,
                    layout: {
                        type: 'vbox',
                        align: 'center',
                        pack: 'center'
                    },
                    items: [
                        cbxAdmiRol,
                        cbxEscalabilidadEdit,
                        cbxHorariosEdit,
                        objChkAlcance,
                        btnCrearTipoContacto,
                        gridTipoContacto,
                        btnCancelarTipoContacto
                    ]
                }
            ]
        });
        windowTipoContacto = Ext.widget('window', {
            title: 'Tipo de contacto',
            id: 'windowTipoContacto',
            height: 540,
            width: 756,
            modal: true,
            resizable: false,
            items: [formTipoContactos]
        }).show();
    };
    /*FIN: Ingresar tipo contactos*/

    this.eliminarContacto = function(intIdPersona, strDelete, intIdPersonaEmpresaRol, intIdPunto, store) {
        Ext.MessageBox.show({
            title: 'Mensaje',
            msg: 'Desea eliminar el contacto?<br/><br/><div id="idchkalcance-group"><input type="checkbox" ' +
                 'id="idchkalcance-eliminarcontacto" style="margin-right: 10px; vertical-align: middle;">' +
                 '<label for="idchkalcance-eliminarcontacto">Eliminar a nivel de <b>cliente</b> y <b>puntos</b>?' +
                 '</label></div>',
            buttons: Ext.MessageBox.OKCANCEL,
            icon: Ext.MessageBox.WARNING,
            fn: function(btn) {
                var booleanChkAlcanceMasivo = Ext.getElementById('idchkalcance-eliminarcontacto').checked;

                if (btn === 'ok') {
                    Ext.MessageBox.show({
                        msg: 'Eliminando contacto' + ((booleanChkAlcanceMasivo) ? ' masivo' : '') + '...',
                        title: 'Eliminando contacto',
                        progressText: 'Eliminando...',
                        progress: true,
                        closable: false,
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                    Ext.Ajax.request({
                        url: urlEliminarContacto,
                        method: 'POST',
                        async: true,
                        timeout: booleanChkAlcanceMasivo ? 600000 : 60000,
                        params: {
                            strDelete: strDelete,
                            intIdPersona: intIdPersona,
                            intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                            intIdPunto: intIdPunto,
                            strAlcanceMasivo: booleanChkAlcanceMasivo
                        },
                        success: function(response) {
                            var text = Ext.decode(response.responseText);
                            Ext.Msg.alert(Utils.arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                            if ("100" === text.strStatus) {
                                var objExtraParams = {
                                    intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                                    intIdPunto: intIdPunto,
                                    strJoinPunto: '',
                                    strDescripcionTipoRol: 'Contacto',
                                    strGroupBy: 'GROUP'
                                };

                                Ext.data.StoreManager.get('storeIdContactoCliente').proxy.extraParams = objExtraParams;
                                Ext.data.StoreManager.get('storeIdContactoCliente').load();

                                objExtraParams.strJoinPunto = 'BUSCA_POR_PUNTO';
                                Ext.data.StoreManager.get('storeIdContactoPunto').proxy.extraParams = objExtraParams;
                                Ext.data.StoreManager.get('storeIdContactoPunto').load();
                            }
                        },
                        failure: function(result) {
                            Ext.Msg.alert('Error ', result.statusText);
                        }
                    });
                } else {
                    return;
                }
            }
        });
    };

}