Ext.onReady(function () {
    var objContacto = new Contacto();
    var storeAdmiFormaContacto = objContacto.storeAdmiFormaContacto();
    var cbxAdmiTitulo = objContacto.objComboTitulo();
    cbxAdmiTitulo.id = 'cbxIdTituloNew';
    cbxAdmiTitulo.colspan = 6;
    cbxAdmiTitulo.setWidth(350);
    var objTxtNombres = Utils.objText();
    objTxtNombres.id = 'txtIdNombres';
    objTxtNombres.name = 'txtNameNombres';
    objTxtNombres.fieldLabel = 'Nombres';
    objTxtNombres.regex = Utils.REGEX_ALFA_LET_SPACE;
    objTxtNombres.msgTarget = 'under';
    objTxtNombres.style = Utils.STYLE_BOLD;
    objTxtNombres.invalidText = 'No se permiten numeros o caracteres especiales';
    objTxtNombres.colspan = 6;
    objTxtNombres.width = 350;
    var objTxtApellidos = Utils.objText();
    objTxtApellidos.id = 'txtIdApellidos';
    objTxtApellidos.name = 'txtNameApellidos';
    objTxtApellidos.fieldLabel = 'Apellidos';
    objTxtApellidos.regex = Utils.REGEX_ALFA_LET_SPACE;
    objTxtApellidos.msgTarget = 'under';
    objTxtApellidos.style = Utils.STYLE_BOLD;
    objTxtApellidos.invalidText = 'No se permiten numeros o caracteres especiales';
    objTxtApellidos.colspan = 6;
    objTxtApellidos.width = 350;
    var chkBoxCliente = new Ext.form.Radio({
        boxLabel: 'Cliente',
        id: 'chkBoxCliente',
        name: 'grCrearContactoPor',
        inputValue: 'chkBoxCliente'
    });
    var chkBoxPunto = new Ext.form.Radio({
        boxLabel: 'Punto',
        id: 'chkBoxPunto',
        name: 'grCrearContactoPor',
        inputValue: 'chkBoxPunto'
    });
    var chkBoxClientePunto = new Ext.form.Radio({
        boxLabel: 'Cliente y Punto',
        id: 'chkBoxClientePunto',
        name: 'grCrearContactoPor',
        inputValue: 'chkBoxClientePunto'
    });

    var rbAlcanceValue = 'rbSesion';
    var rbSesionValue = '';
    var rbPuntosValue = 'rbTodosPuntos';

    var rbSesion = new Ext.form.Radio({
        id: 'idRbSesion',
        boxLabel: 'Sesi&oacuten',
        name: 'rgAlcance',
        inputValue: 'rbSesion',
    });

    var rbMasivo = new Ext.form.Radio({
        id: 'rbMasivo',
        boxLabel: 'Masivo',
        name: 'rgAlcance',
        inputValue: 'rbMasivo',
    });

    var rbTodosPuntos = new Ext.form.Radio({
        id: 'rbTodosPuntos',
        boxLabel: 'Todos los puntos',
        name: 'rgPuntos',
        flex: 1,
        checked: true,
        inputValue: 'rbTodosPuntos'
    });

    var rbSeleccionPuntos = new Ext.form.Radio({
        id: 'rbSeleccionPuntos',
        boxLabel: 'Elegir puntos&nbsp',
        name: 'rgPuntos',
        inputValue: 'rbSeleccionPuntos'
    });

    var chkIncluirCliente = new Ext.form.field.Checkbox({
        id: 'chkIncluirCliente',
        boxLabel: 'Incluir contacto a nivel cliente',
        name: 'chkIncluirCliente',
        inputValue: 'rgPuntos',
        checked: false,
        flex: 1,
    });

    var btnSelPuntos = Ext.create('Ext.button.Button',{
        id: 'idBtnEdicionMasivo',
        iconCls: 'button-seleccion-puntos-modal',
        tooltip: 'Elegir puntos a asignar nuevo contacto',
        colspan: 2,
        width: 22,
        style: {
          marginLeft: '6px'
        },
        disabled: true,
        handler: function () {
            objModalPanelListaPuntos.show();
    }
    });

    var rgAlcance = new Ext.form.RadioGroup({
        fieldLabel: 'Alcance',
        style: 'font-weight:bold;',
        colspan: 6,
        columns: 4,
        width: 450,
        items: [rbSesion, rbMasivo],
        listeners: {
            change: function(field, newValue, oldValue) {
                rbAlcanceValue = newValue.rgAlcance;

                if(rbAlcanceValue == 'rbSesion'){
                    rgPuntos.hide();
                    rdCrearContactoPor.show();
                } else if(rbAlcanceValue == 'rbMasivo'){
                    rdCrearContactoPor.hide();
                    rgPuntos.show();
                }
                Ext.getCmp('idBtnGuardar').setDisabled(false);
            }
        }
    });

    var rdCrearContactoPor = new Ext.form.RadioGroup({
            id: 'idRdCrearContactoPor',
            fieldLabel: 'Crear contacto a nivel',
            style: 'font-weight:bold;',
            colspan: 6,
            columns: 3,
            width: 450,
            height: 'auto',
            items: [chkBoxCliente, chkBoxPunto, chkBoxClientePunto],
            listeners: {
                change: function(field, newValue, oldValue) {
                    rbSesionValue = newValue.grCrearContactoPor;
                    Ext.getCmp('idBtnGuardar').setDisabled(false);
                }
            }
        });

    var rgPuntos = new Ext.form.RadioGroup({
        id: 'idRgPuntos',
        fieldLabel: 'Selecci&oacute;n',
        style: 'font-weight:bold;',
        width: 450,
        layout: {
            type: 'vbox',
            pack: 'start'
        },
        items: [
            {
                xtype: 'container',
                height: 'auto',

                layout: {
                    type: 'hbox',
                    pack: 'center',
                    width: 'auto'
                },
                items: [
                    rbTodosPuntos,
                    {
                        xtype: 'container',
                        height: 'auto',
                        flex: 1,
                        style: 'margin-left: 20px;',
                        layout: {
                            type: 'hbox',
                            pack: 'start'
                        },
                        items: [rbSeleccionPuntos, btnSelPuntos]
                    }

                ]
            },
            chkIncluirCliente],
        listeners: {
            change: function(field, newValue, oldValue) {

                rbPuntosValue = newValue.rgPuntos;

                if(rbPuntosValue == 'rbTodosPuntos'){
                    btnSelPuntos.disable();
                } else if(rbPuntosValue == 'rbSeleccionPuntos'){
                    btnSelPuntos.enable();
                }
                Ext.getCmp('idBtnGuardar').setDisabled(false);
            }
        }
    });
    var objScope = {
        extraParams: {
            strAppendRol: 'Todos',
            strDisponiblesPersona: '',
            strEstadoTipoRol: 'Eliminado, Anulado, Inactivo',
            strDescripcionTipoRol: 'Contacto',
            strComparadorEstTipoRol: 'NOT IN',
            strEstadoRol: 'Eliminado, Anulado, Inactivo',
            strComparadorEstRol: 'NOT IN',
            strEstadoEmpresaRol: 'Eliminado, Anulado, Inactivo',
            strComparadorEmpRol: 'NOT IN',
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
    cbxAdmiRol.colspan = 6;
    cbxAdmiRol.setWidth(350);
    cbxEscalabilidad = objContacto.objComboEscalabilidad();
    cbxEscalabilidad.id = 'cbxEscalabilidad';
    cbxEscalabilidad.colspan = 6;
    cbxEscalabilidad.setWidth(350);
    cbxHorarios = objContacto.objComboHorario();
    cbxHorarios.id = 'cbxHorarios';
    cbxHorarios.colspan = 6;
    cbxHorarios.setWidth(350);
    /**
     * Esta función permite crear un grid para ingresar formas de contato.
     * @version 1.00
     * 
     * Se agrega validación de telefono internacional.
     * 
     * @author Héctor Ortega <haortega@telconet.ec>
     * @version 1.00, 29/11/2016
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 06-07-2017
     * Se agrega la validación para teléfonos móviles y fijos de Panamá
     */
    var rowEditingPersFormaContacto = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText: 'Guardar',
        cancelBtnText: 'Cancelar',
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {
            canceledit: function(editor, e, eOpts) {
                e.store.remove(e.record);
            },
            afteredit: function(roweditor, changes, record, rowIndex) {
                var intCountGridDetalle = Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getCount();
                var selectionModel = Ext.getCmp('gridCreaPersonaFormaContacto').getSelectionModel();
                selectionModel.select(0);
                if (Utils.existStringIn("CORREO", changes.newValues.strDescripcionFormaContacto.trim().toUpperCase()))
                {
                    if (!Utils.validateMail(changes.newValues.strValorFormaContacto.trim())) {
                        Ext.Msg.alert('Error', 'El formato de correo no es correcto, favor revisar.');
                        rowEditingPersFormaContacto.startEdit(0, 0);
                        return false;
                    }
                }
                if (Utils.existStringIn("TELEFONO", changes.newValues.strDescripcionFormaContacto.trim().toUpperCase()))
                {
                    if (Utils.existStringIn("TELEFONO INTERNACIONAL",
                        changes.newValues.strDescripcionFormaContacto.trim().toUpperCase()))
                    {
                        if (!Utils.validateFoneMin7Max15(changes.newValues.strValorFormaContacto.trim())) {
                            Ext.Msg.alert('Error', 'El formato de teléfono internacional no es correcto.! <br>' +
                                'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                                'Se permite un <b>mínimo de 7 dígitos y un máximo de 15 dígitos</b>. <br>' +
                                'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.');
                            rowEditingPersFormaContacto.startEdit(0, 0);
                            return false;
                        }

                    } else
                    {
                        if (strNombrePais === "PANAMA")
                        {
                            if (Utils.existStringIn("TELEFONO FIJO", changes.newValues.strDescripcionFormaContacto.trim().toUpperCase())
                                && !(/^(\+?\d{1,3}?[- .]?\d{1,3}[- .]?\d{1,4})$/.test(changes.newValues.strValorFormaContacto.trim())))
                            {
                                Ext.Msg.alert('Error', 'El formato de teléfono fijo no es correcto.! <br>' +
                                    'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                                    'Se permite mínimo <b>7 dígitos</b>. <br>' +
                                    'No se permiten <b>caracteres especiales excepto + - .</b>, favor revisar.');
                                rowEditingPersFormaContacto.startEdit(0, 0);
                                return false;
                            }
                            if (Utils.existStringIn("TELEFONO MOVIL", changes.newValues.strDescripcionFormaContacto.trim().toUpperCase())
                                && !(/^[0-9]{8}$/.test(changes.newValues.strValorFormaContacto.trim())))
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
                            if (!Utils.validateFoneMin8Max10(changes.newValues.strValorFormaContacto.trim())) {
                                Ext.Msg.alert('Error', 'El formato de teléfono no es correcto.! <br>' +
                                    'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                                    'Se permite un <b>mínimo de 8 dígitos y un máximo de 10 dígitos</b>. <br>' +
                                    'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.');
                                rowEditingPersFormaContacto.startEdit(0, 0);
                                return false;
                            }
                        }
                    }

                }
                if (intCountGridDetalle > 0)
                {
                    if (Ext.isEmpty(Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(0).data.strDescripcionFormaContacto.trim()) ||
                        Ext.isEmpty(Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(0).data.strValorFormaContacto.trim()))
                    {
                        Ext.Msg.alert('Error', 'No puede ingresar registros en blanco.');
                        rowEditingPersFormaContacto.cancelEdit();
                        selectionModel.select(0);
                        rowEditingPersFormaContacto.startEdit(0, 0);
                        return false;
                    }
                }
                for (var i = 1; i < intCountGridDetalle; i++)
                {
                    if (Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(i).get('strDescripcionFormaContacto') === changes.newValues.strDescripcionFormaContacto.trim() &&
                        Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(i).get('strValorFormaContacto') === changes.newValues.strValorFormaContacto.trim())
                    {
                        Ext.Msg.alert('Error', 'Esta forma de contacto ya se encuentra previamente ingresada.');
                        rowEditingPersFormaContacto.startEdit(0, 0);
                        break;
                    }
                }
            }
        }
    });
    Ext.define('personaFormaContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strDescripcionFormaContacto', type: 'string'},
            {name: 'strValorFormaContacto', type: 'string'}
        ]
    });
    var storeCreaPersonaFormaContacto = Ext.create('Ext.data.Store', {
        pageSize: 5,
        autoDestroy: true,
        model: 'personaFormaContactoModel',
        proxy: {
            type: 'memory'
        }
    });
    var btnCrearPersonaFormaContacto = Ext.create('Ext.button.Button', {
        text: 'Agregar forma contacto',
        width: 160,
        iconCls: 'button-grid-crearSolicitud-without-border',
        handler: function () {

            rowEditingPersFormaContacto.cancelEdit();

            var recordParamDet = Ext.create('personaFormaContactoModel', {
                strDescripcionFormaContacto: '',
                strValorFormaContacto: ''
            });
            storeCreaPersonaFormaContacto.insert(0, recordParamDet);
            rowEditingPersFormaContacto.startEdit(0, 0);
            if (Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getCount() > 1)
            {

                if (Ext.isEmpty(Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(1).data.strDescripcionFormaContacto.trim()) ||
                    Ext.isEmpty(Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getAt(1).data.strValorFormaContacto.trim()))
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
        text: 'Eliminar forma contacto',
        width: 160,
        iconCls: 'button-grid-quitarFacturacionElectronica-without-border',
        handler: function () {
            var gridCreaPersonaFormaContacto = Ext.getCmp('gridCreaPersonaFormaContacto');
            var selectionModel = gridCreaPersonaFormaContacto.getSelectionModel();
            rowEditingPersFormaContacto.cancelEdit();
            storeCreaPersonaFormaContacto.remove(selectionModel.getSelection());
            if (storeCreaPersonaFormaContacto.getCount() > 0) {
                selectionModel.select(0);
            }
        }
    });
    var toolbarPersFormaContacto = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [{xtype: 'tbfill'},
                btnCrearPersonaFormaContacto,
                btnDeletePersonaFormaContacto
            ]
    });
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
    var chkBoxDpi = new Ext.form.Radio({
        boxLabel: 'Dpi',
        id: 'chkBoxDpi',
        name: 'grTipoIdenfiticacion',
        inputValue: 'chkBoxDpi'
    });
    var chkBoxNit = new Ext.form.Radio({
        boxLabel: 'Nit',
        id: 'chkBoxNit',
        name: 'grTipoIdenfiticacion',
        inputValue: 'chkBoxNit'
    });
    var chkBoxRuc = new Ext.form.Radio({
        boxLabel: 'Ruc',
        id: 'chkBoxRuc',
        name: 'grTipoIdenfiticacion',
        inputValue: 'chkBoxRuc'
    });  
    var rbValueTipoIdentificacion = 'chkBoxNo';
    var strTipoIdentificacion = 'CED';
    if (strNombrePais !== "GUATEMALA")
    {
            rdTipoIdenfiticacion = new Ext.form.RadioGroup({
            fieldLabel: 'Ingresar identificacion',
            style: 'font-weight:bold;',
            colspan: 6,
            columns: 4,
            width: 450,
            items: [chkBoxNo, chkBoxCedula, chkBoxRuc, chkBoxPasaporte],
            listeners: {
                change: function(field, newValue, oldValue) {
                    rbValueTipoIdentificacion = newValue.grTipoIdenfiticacion;
                    switch (rbValueTipoIdentificacion) {
                        case 'chkBoxNo':
                            Ext.getCmp('txtIdCedula').hide();
                            Ext.getCmp('txtIdCedula').setValue('');
                            Ext.getCmp('txtIdUltimoDigito').hide();
                            Ext.getCmp('txtIdUltimoDigito').setValue('');
                            Ext.getCmp('txtIdRuc').hide();
                            Ext.getCmp('txtIdRuc').setValue('');
                            Ext.getCmp('txtIdPasaporte').hide();
                            Ext.getCmp('txtIdPasaporte').setValue('');
                            break;
                        case 'chkBoxCedula':
                            Ext.getCmp('txtIdCedula').setFieldLabel('Cedula');
                            Ext.getCmp('txtIdCedula').show();
                            Ext.getCmp('txtIdCedula').setValue('');
                            Ext.getCmp('txtIdUltimoDigito').show();
                            Ext.getCmp('txtIdUltimoDigito').setValue('');
                            Ext.getCmp('txtIdRuc').hide();
                            Ext.getCmp('txtIdRuc').setValue('');
                            Ext.getCmp('txtIdPasaporte').hide();
                            Ext.getCmp('txtIdPasaporte').setValue('');
                            strTipoIdentificacion = 'CED';
                            break;
                        case 'chkBoxPasaporte':
                            Ext.getCmp('txtIdCedula').hide();
                            Ext.getCmp('txtIdCedula').setValue('');
                            Ext.getCmp('txtIdUltimoDigito').hide();
                            Ext.getCmp('txtIdUltimoDigito').setValue('');
                            Ext.getCmp('txtIdRuc').hide();
                            Ext.getCmp('txtIdRuc').setValue('');
                            Ext.getCmp('txtIdPasaporte').show();
                            Ext.getCmp('txtIdPasaporte').setValue('');
                            strTipoIdentificacion = 'PAS';
                            break;
                        case 'chkBoxRuc':
                            Ext.getCmp('txtIdCedula').setFieldLabel('Ruc');
                            Ext.getCmp('txtIdCedula').show();
                            Ext.getCmp('txtIdCedula').setValue('');
                            Ext.getCmp('txtIdUltimoDigito').show();
                            Ext.getCmp('txtIdUltimoDigito').setValue('');
                            if (strNombrePais !== 'PANAMA')
                            {
                                Ext.getCmp('txtIdRuc').show();
                                Ext.getCmp('txtIdRuc').setValue('');
                            }
                            Ext.getCmp('txtIdPasaporte').hide();
                            Ext.getCmp('txtIdPasaporte').setValue('');
                            strTipoIdentificacion = 'RUC';
                            break;
                    }
                }
            }
        });
    }
    else
    {
            rdTipoIdenfiticacion = new Ext.form.RadioGroup({
            fieldLabel: 'Ingresar identificacion',
            style: 'font-weight:bold;',
            colspan: 6,
            columns: 4,
            width: 450,
            items: [chkBoxNo, chkBoxNit, chkBoxDpi, chkBoxPasaporte],
            listeners: {
                change: function(field, newValue, oldValue) {
                    rbValueTipoIdentificacion = newValue.grTipoIdenfiticacion;
                    switch (rbValueTipoIdentificacion) {
                        case 'chkBoxNo':
                            Ext.getCmp('txtIdCedula').hide();
                            Ext.getCmp('txtIdCedula').setValue('');
                            Ext.getCmp('txtIdUltimoDigito').hide();
                            Ext.getCmp('txtIdUltimoDigito').setValue('');
                            Ext.getCmp('txtIdRuc').hide();
                            Ext.getCmp('txtIdRuc').setValue('');
                            Ext.getCmp('txtIdPasaporte').hide();
                            Ext.getCmp('txtIdPasaporte').setValue('');
                            Ext.getCmp('txtIdNit').hide();
                            Ext.getCmp('txtIdNit').setValue('');
                            Ext.getCmp('txtIdDpi').hide();
                            Ext.getCmp('txtIdDpi').setValue('');                                
                            break;
                        case 'chkBoxNit':
                            Ext.getCmp('txtIdNit').setFieldLabel('Nit');
                            Ext.getCmp('txtIdNit').show();
                            Ext.getCmp('txtIdNit').setValue('');
                            Ext.getCmp('txtIdCedula').hide();
                            Ext.getCmp('txtIdCedula').setValue('');
                            Ext.getCmp('txtIdDpi').hide();
                            Ext.getCmp('txtIdDpi').setValue('');                               
                            Ext.getCmp('txtIdUltimoDigito').hide();
                            Ext.getCmp('txtIdUltimoDigito').setValue('');
                            Ext.getCmp('txtIdRuc').hide();
                            Ext.getCmp('txtIdRuc').setValue('');
                            Ext.getCmp('txtIdPasaporte').hide();
                            Ext.getCmp('txtIdPasaporte').setValue('');
                            strTipoIdentificacion = 'NIT';
                            break;
                        case 'chkBoxDpi':
                            Ext.getCmp('txtIdDpi').setFieldLabel('Dpi');
                            Ext.getCmp('txtIdDpi').show();
                            Ext.getCmp('txtIdDpi').setValue('');
                            Ext.getCmp('txtIdCedula').hide();
                            Ext.getCmp('txtIdCedula').setValue('');
                            Ext.getCmp('txtIdNit').hide();
                            Ext.getCmp('txtIdNit').setValue('');                               
                            Ext.getCmp('txtIdUltimoDigito').hide();
                            Ext.getCmp('txtIdUltimoDigito').setValue('');
                            Ext.getCmp('txtIdRuc').hide();
                            Ext.getCmp('txtIdRuc').setValue('');
                            Ext.getCmp('txtIdPasaporte').hide();
                            Ext.getCmp('txtIdPasaporte').setValue('');
                            strTipoIdentificacion = 'DPI';
                            break;                            
                        case 'chkBoxPasaporte':
                            Ext.getCmp('txtIdCedula').hide();
                            Ext.getCmp('txtIdCedula').setValue('');
                            Ext.getCmp('txtIdUltimoDigito').hide();
                            Ext.getCmp('txtIdUltimoDigito').setValue('');
                            Ext.getCmp('txtIdRuc').hide();
                            Ext.getCmp('txtIdRuc').setValue('');
                            Ext.getCmp('txtIdPasaporte').show();
                            Ext.getCmp('txtIdPasaporte').setValue('');
                            Ext.getCmp('txtIdNit').hide();
                            Ext.getCmp('txtIdNit').setValue('');
                            Ext.getCmp('txtIdDpi').hide();
                            Ext.getCmp('txtIdDpi').setValue('');                            
                            strTipoIdentificacion = 'PAS';
                            break;
                        default:
                            strTipoIdentificacion = '';
                            break;                            
                    }
                }
            }
        });        
    }
    /** 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 07-02-2018 Se agrega llamada a función que obtiene longitud máxima de identificación parametrizada.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 06-07-2017
     * @since 1.0
     * Se agregan las validaciones para la cédula y los números de teléfonos para el país Panamá.
     **/
    var strTipoIdentCed = 'CED';
    var intMaxLengthCedEc = 0;
    Ext.Ajax.request({
        url: url_getMaxLongitudIdentificacionAjax,
        method: 'POST',
        timeout: 99999,
        async: false,
        params: {strTipoIdentificacion: strTipoIdentCed},
        success: function (response) {
            var objRespuesta = Ext.JSON.decode(response.responseText);

            if (objRespuesta.intMaxLongitudIdentificacion > 0)
            {
                intMaxLengthCedEc = objRespuesta.intMaxLongitudIdentificacion;
            }
        },
        failure: function (response)
        {
            Ext.Msg.alert('Error ', 'Error: ' + response.statusText);
        }
    });
    var intMaxLengthCedula = intMaxLengthCedEc - 1;
    var expRegCedulaEcuador = /[0-9.]/;
    var expRegularFormato = Utils.REGEX_NUM_CEDULA;
    var strMensajeInvalido = '<b>Solo numeros y debe contener 9 digitos</b>.<br>' +
        '<b>Los dos primeros digitos no deben ser menor a 01 o mayor a 24</b>.';
    if (strNombrePais === 'PANAMA')
    {
        var strTipoIdentificacion = 'RUC';
        expRegCedulaEcuador = /$/;
        expRegularFormato = /$/;
        strMensajeInvalido = "<b>La identificación debe cumplir el formato panameño.</b>";

        Ext.Ajax.request({
            url: url_getMaxLongitudIdentificacionAjax,
            method: 'POST',
            timeout: 99999,
            async: false,
            params: {strTipoIdentificacion: strTipoIdentificacion},
            success: function (response) {

                var objRespuesta = Ext.JSON.decode(response.responseText);

                if (objRespuesta.intMaxLongitudIdentificacion > 0)
                {
                    intMaxLengthCedula = objRespuesta.intMaxLongitudIdentificacion;
                }

            },
            failure: function (response)
            {
                Ext.Msg.alert('Error ', 'Error: ' + response.statusText);
            }
        });
    }
    var formCreaContacto = Ext.create('Ext.form.Panel', {
        id: 'formCreaFormaContacto',
        height: 700,
        width: 812,
        renderTo: 'divCrearContacto',
        bodyStyle: 'padding:10px 10px 0; background:#FFFFFF;',
        bodyPadding: 10,
        autoScroll: false,
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
                align: 'center',
                valign: 'middle'
            }
        },
        items: [
            {
                xtype: 'fieldset',
                height: 'auto',
                title: 'Ingrese información contacto',
                layout: {
                    tdAttrs: {style: 'padding: 1px;'},
                    type: 'table',
                    columns: 6,
                    pack: 'center'
                },
                items: [
                    cbxAdmiTitulo,
                    objTxtNombres,
                    objTxtApellidos,
                    rdTipoIdenfiticacion,
                    {
                        xtype: 'container',
                        colspan: 6,
                        bodyStyle: 'margin: 1px 20px;',
                        layout: {
                            tdAttrs: {
                                style: 'padding: 1px 2px;'
                            },
                            type: 'table',
                            columns: 3,
                            pack: 'center'
                        },
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Cedula',
                                style: 'font-weight:bold;',
                                id: 'txtIdCedula',
                                value: '',
                                textAlign: 'left',
                                width: 195,
                                regex: expRegularFormato,
                                msgTarget: 'side',
                                invalidText: strMensajeInvalido,
                                maskRe: expRegCedulaEcuador,
                                enableKeyEvents: true,
                                maxLength: intMaxLengthCedula,
                                enforceMaxLength: true,
                                listeners: {
                                    'keyup': function (f, e) {
                                        if (strNombrePais !== "PANAMA")
                                        {
                                            var arrayCedula = [];
                                            arrayCedula = Utils.getUltimoDigitoCedula(Ext.getCmp('txtIdCedula'));
                                            Ext.getCmp('txtIdUltimoDigito').setValue('');
                                            if ('001' === arrayCedula['strStatus']) {
                                                Ext.getCmp('txtIdCedula').invalidText = arrayCedula['strMensaje'];
                                                Ext.getCmp('txtIdCedula').setActiveError(arrayCedula['strMensaje']);
                                                Ext.getCmp('txtIdUltimoDigito').setValue('Error!');
                                            }
                                            if ('100' === arrayCedula['strStatus']) {
                                                Ext.getCmp('txtIdUltimoDigito').setValue(arrayCedula['intUltimoDigitoCedula']);
                                            }
                                        }
                                    },
                                    'blur': function (f, e) {
                                        if (strNombrePais === "PANAMA")
                                        {
                                            $.ajax({
                                                type: "POST",
                                                data: "identificacion=" + this.value + "&tipo=" + strTipoIdentificacion,
                                                url: url_validar_identificacion_tipo,
                                                success: function (msg)
                                                {
                                                    if (msg != '')
                                                    {
                                                        Ext.getCmp('txtIdCedula').invalidText = "";
                                                        Ext.getCmp('txtIdCedula').setActiveError("");
                                                        Ext.getCmp('txtIdUltimoDigito').setValue("Error!");
                                                    } else
                                                    {
                                                        Ext.getCmp('txtIdUltimoDigito').setValue("OK");
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }
                            },
                            {
                                xtype: 'textfield',
                                id: 'txtIdUltimoDigito',
                                value: '',
                                textAlign: 'center',
                                width: 50,
                                readOnly: true,
                                disabled: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'txtIdRuc',
                                value: '',
                                textAlign: 'left',
                                width: 50,
                                regex: Utils.REGEX_NUM_RUC,
                                msgTarget: 'side',
                                invalidText: 'Solo numeros',
                                maskRe: /[0-9.]/,
                                enableKeyEvents: true,
                                maxLength: 3
                            },
                            {
                                xtype: 'textfield',
                                id: 'txtIdNit',
                                value: '',
                                textAlign: 'left',
                                style: 'font-weight:bold;',
                                width: 300,
                                msgTarget: 'side',
                                invalidText: 'Solo numeros',
                                maskRe: /[0-9]/,
                                maxLength: 13
                            },
                            {
                                xtype: 'textfield',
                                id: 'txtIdDpi',
                                value: '',
                                textAlign: 'left',
                                style: 'font-weight:bold;',
                                width: 300,
                                msgTarget: 'side',
                                invalidText: 'Solo numeros',
                                maskRe: /[0-9]/,
                                maxLength: 13
                            },                            
                            {
                                xtype: 'textfield',
                                id: 'txtIdPasaporte',
                                fieldLabel: 'Pasaporte',
                                style: 'font-weight:bold;',
                                value: '',
                                textAlign: 'left',
                                width: 300,
                                regex: Utils.REGEX_ALFANUM_LET_SPACE,
                                msgTarget: 'side',
                                maxLength: 20,
                                enforceMaxLength: true,
                                invalidText: 'Solo numeros, letras, espacio y - _ /'
                            }
                        ]
                    },
                    cbxAdmiRol,
                    cbxEscalabilidad,
                    cbxHorarios,
                    rgAlcance,
                    {
                        xtype: 'container',
                        height: 45,
                        layout: {
                            type: 'vbox',
                            align: 'left',
                        },
                        items: [rdCrearContactoPor, rgPuntos]
                    },
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Ingrese formas de contacto',
                layout: {
                    type: 'vbox',
                    align: 'left',
                    pack: 'left'
                },
                items: [
                    {
                        xtype: 'grid',
                        store: storeCreaPersonaFormaContacto,
                        plugins: [rowEditingPersFormaContacto],
                        dockedItems: [toolbarPersFormaContacto],
                        id: 'gridCreaPersonaFormaContacto',
                        height: 250,
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
                                dataIndex: 'strValorFormaContacto',
                                width: 300,
                                editor: 'textfield'
                            }
                        ]
                    }
                ]
            }
        ],
        buttonAlign: 'center',
        buttons: [
            {
                text: 'Guardar contacto',
                name: 'btnGuardar',
                id: 'idBtnGuardar',
                disabled: false,
                handler: function () {

                    var boolCorreo = false;

                    if (Ext.isEmpty(cbxAdmiTitulo.getValue())) {
                        Ext.MessageBox.show({
                            title: 'Alerta',
                            msg: 'Debe seleccionar un titulo.',
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.WARNING
                        });
                        return false;
                    }
                    if (Ext.isEmpty(objTxtNombres.getValue().trim()) || Ext.isEmpty(objTxtApellidos.getValue().trim())) {
                        Ext.MessageBox.show({
                            title: 'Alerta',
                            msg: 'El contacto debe tener nombre y apellido.',
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.WARNING
                        });
                        return false;
                    }
                    if(rbAlcanceValue == 'rbSesion') {
                        if ('chkBoxCliente' !== rbSesionValue && 'chkBoxPunto' !== rbSesionValue && 'chkBoxClientePunto' !== rbSesionValue) {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Favor debe elegir a que nivel se creara el contacto.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                    } else if(rbAlcanceValue == 'rbMasivo'){
                        if(rbPuntosValue == 'rbTodosPuntos'){
                            rbTodosPuntos.setValue(true);
                        }
                        else if(rbPuntosValue == 'rbSeleccionPuntos'){
                            if(objStorePuntosSeleccionados && objStorePuntosSeleccionados.getCount() <= 0){
                                Ext.MessageBox.show({
                                    title: 'Alerta',
                                    msg: 'Debe seleccionar al menos <b>un punto</b>.',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.WARNING
                                });
                                return false;
                            }
                        } else {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Debe seleccionar los puntos para alcance masivo.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                    }
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
                            strTipoEscalabilidad=cbxEscalabilidad.getValue().toString();
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
                            strTipoHorario=cbxHorarios.getValue().toString()
                        }
                    }else{
                        strTipoEscalabilidad='';
                        strTipoHorario='';
                    } 
                    var strIdentificacionCliente = '';
                    var strTipoIdentificacion = '';
                    if ('chkBoxNo' !== rbValueTipoIdentificacion) {
                        var strMsgAlert = '';
                        var booleanAlert = false;
                        if ('chkBoxCedula' === rbValueTipoIdentificacion) {
                            if (Ext.isEmpty(Ext.getCmp('txtIdCedula').getValue())) {
                                booleanAlert = true;
                                strMsgAlert = 'Debe ingresar la cedula.';
                            } else if (Ext.isEmpty(Ext.getCmp('txtIdUltimoDigito').getValue()) || 'Error!' === Ext.getCmp('txtIdUltimoDigito').getValue()) {
                                booleanAlert = true;
                                strMsgAlert = 'Revise el campo "Cedula". Y recordar que: <br>' + strMensajeInvalido;
                            }
                            strTipoIdentificacion = 'CED';
                            if (strNombrePais === "PANAMA")
                            {
                                strIdentificacionCliente = Ext.getCmp('txtIdCedula').getValue().toUpperCase();
                            } else
                            {
                                strIdentificacionCliente = Ext.getCmp('txtIdCedula').getValue() + Ext.getCmp('txtIdUltimoDigito').getValue();
                            }
                        } else if ('chkBoxRuc' === rbValueTipoIdentificacion) {
                            if (strNombrePais !== 'PANAMA')
                            {
                                if (Ext.isEmpty(Ext.getCmp('txtIdRuc').getValue())) {
                                    booleanAlert = true;
                                    strMsgAlert = 'Debe ingresar los ultimos digitos del ruc.';
                                } else if (Ext.isEmpty(Ext.getCmp('txtIdUltimoDigito').getValue()) || 'Error!' === Ext.getCmp('txtIdUltimoDigito').getValue()) {
                                    booleanAlert = true;
                                    strMsgAlert = 'Debe ingresar los 9 digitos en el campo "Cedula". Y recordar que: <br>' +
                                        '<b>Solo numeros y debe contener 9 digitos</b>.<br>' +
                                        '<b>Los dos primeros digitos no deben ser menor a 01 o mayor a 24</b>.';
                                }
                                strIdentificacionCliente = Ext.getCmp('txtIdCedula').getValue() + Ext.getCmp('txtIdUltimoDigito').getValue() +
                                    Ext.getCmp('txtIdRuc').getValue();
                            } else
                            {
                                strIdentificacionCliente = Ext.getCmp('txtIdCedula').getValue().toUpperCase();
                            }
                            strTipoIdentificacion = 'RUC';

                        } else if ('chkBoxPasaporte' === rbValueTipoIdentificacion) {
                            if (Ext.isEmpty(Ext.getCmp('txtIdPasaporte').getValue())) {
                                booleanAlert = true;
                                strMsgAlert = 'Debe ingresar el pasaporte.';
                            }
                            strTipoIdentificacion = 'PAS';
                            strIdentificacionCliente = Ext.getCmp('txtIdPasaporte').getValue();
                        } else if ('chkBoxNit' === rbValueTipoIdentificacion) {
                            if (Ext.isEmpty(Ext.getCmp('txtIdNit').getValue())) {
                                booleanAlert = true;
                                strMsgAlert = 'Debe ingresar el Nit.';
                            }
                            strTipoIdentificacion = 'NIT';
                            strIdentificacionCliente = Ext.getCmp('txtIdNit').getValue();
                        } else if ('chkBoxDpi' === rbValueTipoIdentificacion) {
                            if (Ext.isEmpty(Ext.getCmp('txtIdDpi').getValue())) {
                                booleanAlert = true;
                                strMsgAlert = 'Debe ingresar el Dpi.';
                            }
                            strTipoIdentificacion = 'DPI';
                            strIdentificacionCliente = Ext.getCmp('txtIdDpi').getValue();
                        }
                        if (booleanAlert) {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: strMsgAlert,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                    }
                    if (Ext.isEmpty(cbxAdmiRol.getValue().toString())) {
                        Ext.MessageBox.show({
                            title: 'Alerta',
                            msg: 'Debe seleccionar un tipo de contacto.',
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.WARNING
                        });
                        return false;
                    }
                    var arrayCreaPersonaFormaContacto = new Object();
                    var jsonCreaPersonaFormaContacto = '';
                    var arrayGridCreaPersonaFormaContacto = Ext.getCmp('gridCreaPersonaFormaContacto');
                    arrayCreaPersonaFormaContacto['inTotal'] = arrayGridCreaPersonaFormaContacto.getStore().getCount();
                    arrayCreaPersonaFormaContacto['arrayData'] = new Array();
                    var arrayCreaPersonaFormaContactoData = Array();
                    if (arrayGridCreaPersonaFormaContacto.getStore().getCount() !== 0)
                    {
                        for (var intCounterStore = 0;
                            intCounterStore < arrayGridCreaPersonaFormaContacto.getStore().getCount(); intCounterStore++)
                        {
                            if (Utils.existStringIn("CORREO", arrayGridCreaPersonaFormaContacto.getStore().getAt(intCounterStore).data.strDescripcionFormaContacto.trim().toUpperCase()))
                            {
                                boolCorreo = true;
                            }
                            arrayCreaPersonaFormaContactoData.push(arrayGridCreaPersonaFormaContacto.getStore().getAt(intCounterStore).data);
                        }

                        if (boolCorreo) {
                            arrayCreaPersonaFormaContacto['arrayData'] = arrayCreaPersonaFormaContactoData;
                            jsonCreaPersonaFormaContacto = Ext.JSON.encode(arrayCreaPersonaFormaContacto);
                        } else {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Debe ingresar al menos una forma de contacto de tipo <b>correo</b>.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }

                        var arrayIdPuntosAsignacionMasiva = [];

                        objStorePuntosSeleccionados.each(function(record) {
                            arrayIdPuntosAsignacionMasiva.push(record.get('idPto'));
                        });
                    } else {
                        Ext.MessageBox.show({
                            title: 'Alerta',
                            msg: 'Debe ingresar al menos una forma de contacto.',
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.WARNING
                        });
                        return false;
                    }
                    Ext.MessageBox.show({
                        msg: (!Ext.isEmpty(rbAlcanceValue) && rbAlcanceValue == 'rbMasivo')
                            ? 'Este proceso podría tardar varios minutos.<br/>Espere por favor.'
                            : 'Espere por favor.',
                        title: 'Guardando contacto',
                        progressText: 'Guardando...',
                        progress: true,
                        closable: false,
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                    Ext.Ajax.request({
                        url: urlCreaPersonaFormaContacto,
                        method: 'POST',
                        async: true,
                        timeout: (rbAlcanceValue == 'rbMasivo') ? 1200000 : 60000,
                        params: {
                            jsonCreaPersonaFormaContacto: jsonCreaPersonaFormaContacto,
                            intIdTitulo: cbxAdmiTitulo.getValue(),
                            strNombreContacto: objTxtNombres.getValue().trim(),
                            strApellidoContacto: objTxtApellidos.getValue().trim(),
                            strEmpresaRol: cbxAdmiRol.getValue().toString(),
                            strEscalabilidad: strTipoEscalabilidad,
                            strHorariosContact: strTipoHorario,
                            rbValue: rbAlcanceValue == 'rbSesion'
                                ? rbSesionValue
                                : (rbAlcanceValue == 'rbMasivo')
                                    ? rbPuntosValue
                                    : null,
                            strTipoIdentificacion: strTipoIdentificacion,
                            strIdentificacionCliente: strIdentificacionCliente,
                            strAlcance: rbAlcanceValue,
                            strArrayIdPuntos: Ext.JSON.encode(arrayIdPuntosAsignacionMasiva),
                            booleanIncluirNivelCliente: (rbAlcanceValue == 'rbMasivo')
                                ? chkIncluirCliente.checked
                                : false
                        },
                        success: function(response) {
                            var text = Ext.decode(response.responseText);
                            Ext.Msg.alert(Utils.arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                            if ("100" === text.strStatus) {
                                Ext.MessageBox.show({
                                    title: 'Ver informacion de contacto',
                                    msg: 'Cargando información...',
                                    progressText: 'Redireccionando.',
                                    progress: true,
                                    closable: false,
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                                window.location.assign(text.registros);
                            }
                        },
                        failure: function(result) {
                            Ext.Msg.alert('Error ', result.statusText);
                        }
                    });
                }
            }]
    });

    /* Inicio de interfaz para creación masiva */
    Ext.tip.QuickTipManager.init();

    var registrosPorPagina = 10;

    //Modelos
    var modelEstadoPunto = Ext.define('modelEstadoPunto', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'estado_punto',
                type: 'string'
            }
        ]
    });

    var modelPunto = Ext.define('modelPunto', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idPto', type: 'int'},
            {name: 'cliente', type: 'string'},
            {name: 'login', type: 'string'},
            {name: 'nombrePunto', type: 'string'},
            {name: 'direccion', type: 'string'},
            {name: 'descripcionPunto', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'linkVer', type: 'string'},
            {name: 'linkEditar', type: 'string'},
            {name: 'linkEliminar', type: 'string'},
            {name: 'permiteAnularPunto', type: 'string'}
        ]
    });

    //Stores
    var objStoreEstadoPunto = Ext.create('Ext.data.Store',{
        autoLoad: false,
        model: modelEstadoPunto,
        proxy: {
            type: 'ajax',
            url: url_puntos_lista_estados,
            reader: {
                type: 'json',
                root: 'encontrados'
            }
        }
    });

    var objStorePuntosLista = Ext.create('Ext.data.Store', {
        id: 'idStorePuntosLista',
        autoLoad: false,
        model: modelPunto,
        pageSize: registrosPorPagina,
        proxy: {
            type: 'ajax',
            url: url_gridPtos,
            reader: {
                type: 'json',
                root: 'ptos',
                totalProperty: 'total'
            },
            extraParams: {
                txtFechaDesde: '',
                txtFechaHasta: '',
                txtLogin: '',
                txtNombrePunto: '',
                txtDireccion: '',
                estado_punto: ''
            },
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.txtFechaDesde  = Ext.getCmp('txtFechaDesde').getValue();
                store.getProxy().extraParams.txtFechaHasta  = Ext.getCmp('txtFechaHasta').getValue();
                store.getProxy().extraParams.txtLogin       = Ext.getCmp('txtLogin').getValue();
                store.getProxy().extraParams.txtNombrePunto = Ext.getCmp('txtNombrePunto').getValue();
                store.getProxy().extraParams.txtDireccion   = Ext.getCmp('txtDireccion').getValue();
                store.getProxy().extraParams.estado_punto   = Ext.getCmp('estado_punto').getValue();
            }
        }
    });

    var objStorePuntosSeleccionados = Ext.create(Ext.data.Store, {
        id: 'idStorePuntosSeleccionados',
        model: modelPunto,
        pageSize: registrosPorPagina,
        proxy: {
            type: 'memory'
        }
    });

    // Campos de búsqueda
    var objDateFechaDesde = new Ext.form.DateField({
        id: 'txtFechaDesde',
        fieldLabel: 'Creaci&oacuten Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
    var objDateFechaHasta = new Ext.form.DateField({
        id: 'txtFechaHasta',
        fieldLabel: 'Creaci&oacuten Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
    var objTextLogin = new Ext.form.TextField({
        id: 'txtLogin',
        fieldLabel: 'Login',
        xtype: 'textfield',
        width: 325
    });
    var objTextNombrePunto = new Ext.form.TextField({
        id: 'txtNombrePunto',
        fieldLabel: 'Nombre Punto',
        xtype: 'textfield',
        width: 315
    });
    var objTextDireccion = new Ext.form.TextField({
        id: 'txtDireccion',
        fieldLabel: 'Direccion Punto',
        xtype: 'textfield',
        width: 315
    });
    var objCbEstadoPunto = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: objStoreEstadoPunto,
        labelAlign: 'left',
        id: 'estado_punto',
        name: 'estado_punto',
        valueField: 'estado_punto',
        displayField: 'estado_punto',
        fieldLabel: 'Estado',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true,
        listeners: {
            select:
                function(e) {
                    estado_id = Ext.getCmp('estado_punto').getValue();
                },
            click: {
                element: 'el',
                fn: function() {
                    estado_id = '';
                    objStoreEstadoPunto.removeAll();
                    objStoreEstadoPunto.load();
                }
            }
        }
    });

    //Formulario de búsqueda
    var objFormFiltroPuntos = Ext.create('Ext.panel.Panel', {
        flex: 0.5,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 2,
            align: 'left',
            tableAttrs: {
                style: {
                    width: '100%',
                    height: '100%'
                }
            },
            tdAttrs: {
                align: 'left',
                valign: 'middle'
            }
        },
        bodyStyle: {
            padding: '10px 10px 10px 150px',
        },
        collapsible : false,
        collapsed: false,
        title: 'Criterios de b&uacutesqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function(){
                    var boolError = false;
                    if ((Ext.getCmp('txtFechaDesde').getValue() != null) && (Ext.getCmp('txtFechaHasta').getValue() != null))
                    {
                        if (Ext.getCmp('txtFechaDesde').getValue() > Ext.getCmp('txtFechaHasta').getValue())
                        {
                            boolError = true;
                            Ext.Msg.show({
                                title: 'Error en Busqueda',
                                msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                                buttons: Ext.Msg.OK,
                                animEl: 'elId',
                                icon: Ext.MessageBox.ERROR
                            });

                        }
                    }
                    else
                    {
                        if ((Ext.getCmp('txtFechaDesde').getValue() == null) && (Ext.getCmp('txtFechaHasta').getValue() != null)
                            || (Ext.getCmp('txtFechaDesde').getValue() != null) && (Ext.getCmp('txtFechaHasta').getValue() == null))
                        {
                            Ext.Msg.show({
                                title: 'Error en B&uacutesqueda',
                                msg: 'Por favor ingrese criterios de fecha correctamente.',
                                buttons: Ext.Msg.OK,
                                animEl: 'elId',
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    }

                    if (!boolError){
                        objStorePuntosLista.load({ params: {
                                    start: 0,
                                    limit: registrosPorPagina
                                }
                        });
                    }
                },
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    Ext.getCmp('txtFechaDesde').setValue('');
                    Ext.getCmp('txtFechaHasta').setValue('');
                    Ext.getCmp('txtLogin').setValue('');
                    Ext.getCmp('txtNombrePunto').setValue('');
                    Ext.getCmp('txtDireccion').setValue('');
                    Ext.getCmp('estado_punto').setValue('');
                    objStorePuntosLista.load({ params: {
                            start: 0,
                            limit: registrosPorPagina
                        }
                    });
                }
            }
        ],
        items: [
            objDateFechaDesde,
            objDateFechaHasta,
            objTextLogin,
            objTextNombrePunto,
            objTextDireccion,
            objCbEstadoPunto,
        ],
    });

    //Grid de lista de puntos
    var objGridPuntosListado = Ext.create('Ext.grid.Panel', {
        id: 'idGridPuntosListado',
        width: 'auto',
        layout: 'fit',
        flex: 1,
        store: objStorePuntosLista,
        multiSelect: false,
        style: 'vertical-align: middle;',
        title: 'Listado de Puntos',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: objStorePuntosLista,
            displayInfo: true,
            displayMsg: 'Mostrando {0} puntos - {1} de {2}',
            emptyMsg: 'No hay datos para mostrar'
        }),
        viewConfig: {
            deferEmptyText: false,
            emptyText: 'No hay datos para mostrar'
        },
        listeners: {
        },
        columns: [
            new Ext.grid.RowNumberer(
                {
                    header: '#',
                    flex: 0,
                    align: 'center',
                    dataIndex: 'index'
                }),
            {
                text: 'Login',
                flex: 1,
                dataIndex: 'login'
            },
            {
                text: 'Nombre Punto',
                dataIndex: 'nombrePunto',
                flex: 1,
            },
            {
                text: 'Direcci&oacuten',
                dataIndex: 'direccion',
                flex: 1,
            },
            {
                text: 'Estado',
                dataIndex: 'estado',
                flex: 0.4,
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                flex: 0,
                items: [
                {
                    tooltip: 'Agregar punto',
                    iconCls: 'x-btn button-seleccion-puntos-agregar',
                    handler: function(view, rowIndex, colIndex) {
                        var index = objStorePuntosSeleccionados.find('idPto',
                            objStorePuntosLista.getAt(rowIndex).get('idPto'));

                        if(index <= -1) { //Indice no encontrado
                            var objGridItem = objStorePuntosLista.getAt(rowIndex);

                            objGridItem.index = objStorePuntosSeleccionados.getCount();
                            objStorePuntosSeleccionados.add(objGridItem);
                        } else {
                            Ext.getCmp('idGridPuntosSeleccionados').getSelectionModel().select(index);
                        }
                    }
                }]
            }

        ]
    });

    //Grid de lista de puntos seleccionados
    var objGridPuntosSeleccionados = Ext.create('Ext.grid.Panel', {
        id: 'idGridPuntosSeleccionados',
        width: 'auto',
        layout: 'fit',
        flex: 0.3,
        title: 'Puntos selecionados',
        store: objStorePuntosSeleccionados,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        columns: [
            new Ext.grid.RowNumberer({
                header: '#',
                flex: 0,
                align: 'center',
            }),
            {
                text: 'Login',
                flex: 1,
                dataIndex: 'login'
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                flex: 0,
                items: [
                {
                    tooltip: 'Quitar punto',
                    iconCls: 'x-btn btn-acciones button-grid-delete',
                    handler: function(view, rowIndex, colIndex) {
                        objStorePuntosSeleccionados.removeAt(rowIndex);
                    }
                }]
            }
        ]
    });

    //Ventana modal para creación masiva
    var objModalPanelListaPuntos = Ext.create('Ext.window.Window', {
        title: 'Seleccionar puntos en donde se creará el contacto',
        id: 'idModalPanelListaPuntos',
        floating: true,
        border: false,
        frame: false,
        height: 500,
        width: 1200,
        modal: true,
        resizable: false,
        closeAction : 'hide',
        bodyStyle: 'background-color: #FFFFFF',
        layout: {
            type: 'vbox',
            align: 'stretch',
        },
        customParams: {
            firstTime: true
        },
        items: [objFormFiltroPuntos,
            {
                xtype: 'container',
                flex: 1,
                layout: {
                    type: 'hbox',
                    align: 'stretch',
                    columns: 2,
                    rows: 1,
                },
                items: [objGridPuntosListado, objGridPuntosSeleccionados]
            }],
        buttons: [
            {
                xtype: 'button',
                text: 'Cerrar y continuar',
                align: 'center',
                iconCls: 'x-btn button-seleccion-puntos-cerrar',
                handler: function () {
                    objModalPanelListaPuntos.close();
                }
            },
        ],
        listeners: {
            show: function (window, options) {
                if(window.customParams.firstTime){
                    window.customParams.firstTime = false;
                    objStorePuntosLista.load({
                            params: {
                                start: 0,
                                limit: registrosPorPagina
                            }
                    });
                }
            }
        }
    });

    Ext.getCmp('idBtnGuardar').setDisabled(true);
    Ext.getCmp('txtIdCedula').hide();
    Ext.getCmp('txtIdCedula').setValue('');
    Ext.getCmp('txtIdUltimoDigito').hide();
    Ext.getCmp('txtIdUltimoDigito').setValue('');
    Ext.getCmp('txtIdRuc').hide();
    Ext.getCmp('txtIdRuc').setValue('');
    Ext.getCmp('txtIdNit').hide();
    Ext.getCmp('txtIdNit').setValue('');
    Ext.getCmp('txtIdDpi').hide();
    Ext.getCmp('txtIdDpi').setValue('');    
    Ext.getCmp('txtIdPasaporte').hide();
    Ext.getCmp('txtIdPasaporte').setValue('');
    Ext.getCmp('idRbSesion').setValue(true);
});