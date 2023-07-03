/**
 * Gestor de representante legal
 * permite vincular representante legal a un cliente juridico
 * 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */

function gestorRepresentanteLegal(
  objView,
  strTipoIdentificacionCliente,
  strIdentificacionCliente,
  bolCoordinador
) {
  strTipoIdentificacionCliente = strTipoIdentificacionCliente
    .toUpperCase()
    .trim();
  strIdentificacionCliente = strIdentificacionCliente.toUpperCase().trim();
  let bolFalloConsultarRepresentante = false;
  let bolExistenCambiosListado = false;
  let strOrigen = 'S';
  let boolPermisoCoordinador =(bolCoordinador || false);
  let labelRucJuridico = "RUC JURIDICO";

  function limpiarFormulario() {
    Ext.Msg.confirm(
      'Alerta',
      '¿Desea borrar la información del representante legal?',
      function (btn) {
        if (btn == 'yes') {
          //restaurar formularios y controles
          cargarDatosFormulario({});
          bloquearControlFormulario(true);
        }
      }
    );
  }

  function agregarItemRepresentante() {
    let bolValido = validarDataFormulario();
    if (bolValido) {
      validarFormaContacto(function () {
        //agregar item de lista
        let strIndentificacionRepresentante = objTextIdentificacion.getValue();
        let arrayRepresentantes = getDataJsonStore(storeRepresentantes);
        storeRepresentantes.removeAll();
        let objItemRep = {
          idPersona: null,
          idRepresentanteLegal: null,
          razonSocial: null,
          nombres: null,
          apellidos: null,
          tipoIdentificacion: null,
          identificacion: null,
          cargo: null,
          direccion: null,
          tipoTributario: null,
          formaContacto: [],
          fechaExpiracionNombramiento: null,
        };
        arrayRepresentantes.forEach(item => {
          if (item.identificacion == strIndentificacionRepresentante) {
            objItemRep = item;
          } else {
            storeRepresentantes.insert(
              storeRepresentantes.getCount(),
              Ext.create(modelRepresentanteLegalModel, item)
            );
          }
        });

        objItemRep.tipoTributario = objTextTipoTributario.getValue();
        objItemRep.razonSocial = objTextRazonSocial.getValue();
        objItemRep.nombres = objTextNombres.getValue();
        objItemRep.apellidos = objTextApellidos.getValue();
        objItemRep.tipoIdentificacion = objComboTipoIdent.getValue();
        objItemRep.identificacion = objTextIdentificacion.getValue();
        objItemRep.cargo = objTextCargo.getValue();

        objItemRep.direccion = objTextDireccion.getValue();

        objItemRep.fechaExpiracionNombramiento = objDateExpiracion
          .getRawValue()
          .trim()
          .substring(0, 10);
        let objFormaContacto = getDataJsonStore(storeCreaPersonaFormaContacto);
        objItemRep.formaContacto = objFormaContacto;

        storeRepresentantes.insert(
          storeRepresentantes.getCount(),
          Ext.create(modelRepresentanteLegalModel, objItemRep)
        );
        storeRepresentantes.commitChanges();

        //restaurar formularios y controles
        cargarDatosFormulario({});
        bloquearControlFormulario(true);

        Ext.getCmp('idGridRepresentanteLegal')
          .getView()
          .scrollBy(0, 999999, true);
        existenCambiosListado(true);
      });
    }
  }

  function validarDataFormulario() {
    let boolCorreo = false;
    let boolTelefono = false;
    panel.getEl().unmask();


    if (
      Ext.isEmpty(objComboTipoIdent.getValue()) ||
      Ext.isEmpty(objTextIdentificacion.getValue())
    ) {
      Ext.Msg.alert(
        'Alerta',
        'El representante debe tener una identificación.'
      );
      return false;
    }


    if (objTextTipoTributario.getValue() == "JUR") {
      if (Ext.isEmpty(objTextRazonSocial.getValue().trim())) {
        Ext.Msg.alert(
          'Alerta',
          'El representante debe tener razon social'
        );
        return false;
      }
    }
    else {
      if (
        Ext.isEmpty(objTextNombres.getValue().trim()) ||
        Ext.isEmpty(objTextApellidos.getValue().trim())
      ) {
        Ext.Msg.alert(
          'Alerta',
          'El representante debe tener nombres y apellidos'
        );
        return false;
      }
    }



    if (Ext.isEmpty(objTextCargo.getValue().trim())) {
      Ext.Msg.alert('Alerta', 'El representante debe tener un cargo');
      return false;
    }

    if (Ext.isEmpty(objTextDireccion.getValue().trim())) {
      Ext.Msg.alert('Alerta', 'El representante debe tener una dirección');
      return false;
    }

    if (Ext.isEmpty(objDateExpiracion.getRawValue().trim())) {
      Ext.Msg.alert(
        'Alerta',
        'El representante debe tener una fecha de expiración del nombramiento'
      );
      return false;
    }

    let arrayCreaPersonaFormaContacto = new Object();
    let arrayGridCreaPersonaFormaContacto = Ext.getCmp(
      'gridCreaPersonaFormaContacto'
    );
    arrayCreaPersonaFormaContacto[
      'inTotal'
    ] = arrayGridCreaPersonaFormaContacto.getStore().getCount();
    arrayCreaPersonaFormaContacto['arrayData'] = new Array();
    let arrayCreaPersonaFormaContactoData = Array();

    if (arrayGridCreaPersonaFormaContacto.getStore().getCount() !== 0) {
      for (
        let intCounterStore = 0;
        intCounterStore <
        arrayGridCreaPersonaFormaContacto.getStore().getCount();
        intCounterStore++
      ) {
        if (
          !Ext.isEmpty(
            arrayGridCreaPersonaFormaContacto
              .getStore()
              .getAt(intCounterStore)
              .data.formaContacto.trim()
              .toUpperCase()
          )
        ) {
          if (
            Utils.existStringIn(
              'CORREO',
              arrayGridCreaPersonaFormaContacto
                .getStore()
                .getAt(intCounterStore)
                .data.formaContacto.trim()
                .toUpperCase()
            )
          ) {
            boolCorreo = true;
          }

          if (
            Utils.existStringIn(
              'TELEFONO MOVIL',
              arrayGridCreaPersonaFormaContacto
                .getStore()
                .getAt(intCounterStore)
                .data.formaContacto.trim()
                .toUpperCase()
            )
          ) {
            boolTelefono = true;
          }
          arrayCreaPersonaFormaContactoData.push(
            arrayGridCreaPersonaFormaContacto
              .getStore()
              .getAt(intCounterStore).data
          );
        }
      }

      if (!boolCorreo) {
        Ext.Msg.alert(
          'Alerta',
          'Debe ingresar al menos una forma de contacto de tipo <b>Correo</b>.'
        );
        return false;
      }

      if (!boolTelefono) {
        Ext.Msg.alert(
          'Alerta',
          'Debe ingresar al menos una forma de contacto de tipo <b>Teléfono móvil</b>.'
        );
        return false;
      }

      if (boolCorreo && boolTelefono) {
        arrayCreaPersonaFormaContacto[
          'arrayData'
        ] = arrayCreaPersonaFormaContactoData;
        jsonCreaPersonaFormaContacto = Ext.JSON.encode(
          arrayCreaPersonaFormaContacto
        );
      }
    } else {
      Ext.Msg.alert('Alerta', 'Debe ingresar al menos una forma de contacto.');
      return false;
    }
    return true;
  }

  function cargarDatosFormulario(data) {
    objComboTipoIdent.setValue(!Ext.isEmpty(data.tipoIdentificacion) ? data.tipoIdentificacion : '');
    objTextIdentificacion.setValue(!Ext.isEmpty(data.identificacion) ? data.identificacion : '');
    objTextTipoTributario.setValue(!Ext.isEmpty(data.tipoTributario) ? data.tipoTributario : '');
    objTextRazonSocial.setValue(!Ext.isEmpty(data.razonSocial) ? data.razonSocial : '');
    objTextNombres.setValue(!Ext.isEmpty(data.nombres) ? data.nombres : '');
    objTextApellidos.setValue(!Ext.isEmpty(data.apellidos) ? data.apellidos : '');
    objTextCargo.setValue(!Ext.isEmpty(data.cargo) ? data.cargo : '');
    objTextDireccion.setValue(!Ext.isEmpty(data.direccion) ? data.direccion : '');

    if (!Ext.isEmpty(data.fechaExpiracionNombramiento)) {
      objDateExpiracion.setValue(
        Ext.Date.parse(data.fechaExpiracionNombramiento, 'd/m/Y')
      );
    } else {
      objDateExpiracion.setRawValue('');
    }

    storeCreaPersonaFormaContacto.removeAll();
    if (!Ext.isEmpty(data.formaContacto)) {
      Ext.Array.forEach(data.formaContacto, function (item) {
        storeCreaPersonaFormaContacto.insert(
          storeCreaPersonaFormaContacto.getCount(),
          Ext.create('personaFormaContactoModel', {
            idPersonaFormaContacto: item.idPersonaFormaContacto,
            formaContactoId: item.formaContactoId,
            formaContacto: item.formaContacto,
            valor: item.valor,
          })
        );
      });
    }
    storeCreaPersonaFormaContacto.commitChanges();
  }

  function cancelarCambiosRepresentatesLegal() {
    if (bolExistenCambiosListado && boolPermisoCoordinador) {
      panel.getEl().unmask();
      Ext.Msg.confirm(
        'Alerta',
        '¿Seguro que desea salir sin guardar los cambios realizados?',
        function (btn) {
          if (btn == 'yes') {
            modal.destroy();
          }
        }
      );
    } else {
      modal.destroy();
    }
  }

  function validarFormaContacto(callBack) {
    panel.getEl().mask('Validando formas de contacto, Por favor espere!!');
    let objFormaContacto = Ext.getCmp(
      'gridCreaPersonaFormaContacto'
    ).getStore();
    let arrayFormaContacto = getDataJsonStore(objFormaContacto);
    let strFormaContacto = JSON.stringify(arrayFormaContacto);

    Ext.Ajax.request({
      url: urlValidarFormaContacto,
      method: 'POST',
      timeout: 6000,
      async: true,
      params: {
        strOrigen,
        strFormaContacto,
      },
      success: function (data) {
        let result = Ext.decode(data.responseText);
        panel.getEl().unmask();
        if (result.status === 'OK') {
          callBack();
        } else {
          Ext.Msg.alert('Error', result.message);
        }
      },
      failure: function (result) {
        panel.getEl().unmask();
        Ext.Msg.alert('Error ', result.statusText);
      },
    });
  }

  function actualizarListaRepresentatesLegal() {
    if (boolPermisoCoordinador) {
      modal
        .getEl()
        .mask(
          'Actualizando representante legal vinculados, Por favor espere!!'
        );
      let arrayRepresentantes = getDataJsonStore(storeRepresentantes);
      let strValidaData = validarDataActualizar(arrayRepresentantes);

      if (strValidaData == '') {
        let strRepresentantes = JSON.stringify(arrayRepresentantes);
        Ext.Ajax.request({
          url: urlRepresentanteLegalActualizar,
          method: 'POST',
          async: true,
          timeout: 6000,
          params: {
            strOrigen,
            strTipoIdentificacionCliente,
            strIdentificacionCliente,
            strRepresentantes,
          },
          success: function (data) {
            let result = Ext.decode(data.responseText);
            modal.getEl().unmask();
            if (result.status === 'OK') {
              cargarDatosFormulario({});
              bloquearControlFormulario(true);
              storeRepresentantes.load();
              existenCambiosListado(false);
              Ext.Msg.alert('Alerta', result.message);
            } else {
              Ext.Msg.alert('Error', result.message);
            }
          },
          failure: function (result) {
            modal.getEl().unmask();
            Ext.Msg.alert('Error ', result.statusText);
          },
        });
      } else {
        modal.getEl().unmask();
        Ext.Msg.alert('Error ', strValidaData);
      }
    } else {
      modal.getEl().unmask();
      Ext.Msg.alert('Error ', 'Solo el coordinador puede guardar cambios.');
    }
  }

  function eliminarItemLocalRepresentanteLegal(data) {
    let strIndentificacionRepresentante = data.identificacion;
    panel.getEl().unmask();
    Ext.Msg.confirm(
      'Alerta',
      '¿Desea eliminar el representante legal con identificación ' +
      strIndentificacionRepresentante +
      '?',
      function (btn) {
        if (btn == 'yes') {
          //eliminar item de lista
          let arrayRepresentantes = getDataJsonStore(storeRepresentantes);
          storeRepresentantes.removeAll();
          arrayRepresentantes.forEach(item => {
            if (item.identificacion != strIndentificacionRepresentante) {
              storeRepresentantes.insert(
                storeRepresentantes.getCount(),
                Ext.create(modelRepresentanteLegalModel, item)
              );
            }
          });
          storeRepresentantes.commitChanges();
          existenCambiosListado(true);
        }
      }
    );
  }

  function bloquearControlFormulario(status) {
    if (status) {
      //bloqueados
      objComboTipoIdent.enable();
      objTextIdentificacion.enable();
      objBtnBuscarRepresentante.enable();

      objTextRazonSocial.disable();
      objTextNombres.disable();
      objTextApellidos.disable();
      objTextCargo.disable();
      objTextDireccion.disable();
      objDateExpiracion.disable();

      Ext.getCmp('gridCreaPersonaFormaContacto').disable();
      objBtnAgregarRepresentante.disable();
      objBtnLimpiarRepresentante.disable();
    } else {
      //desbloqueados
      objComboTipoIdent.disable();
      objTextIdentificacion.disable();
      objBtnBuscarRepresentante.disable();

      objTextRazonSocial.enable();
      objTextNombres.enable();
      objTextApellidos.enable();
      objTextCargo.enable();
      objTextDireccion.enable();
      objDateExpiracion.enable();

      Ext.getCmp('gridCreaPersonaFormaContacto').enable();
      objBtnAgregarRepresentante.enable();
      objBtnLimpiarRepresentante.enable();
    }


    objTextRazonSocial.setVisible(false);
    objTextNombres.setVisible(false);
    objTextApellidos.setVisible(false);

    if (objTextTipoTributario.getValue() == 'JUR') {
      objTextRazonSocial.setVisible(true);
    }

    if (objTextTipoTributario.getValue() == 'NAT') {
      objTextNombres.setVisible(true);
      objTextApellidos.setVisible(true);
    }

  }



  function existenCambiosListado(status) {
    bolExistenCambiosListado = status;

    let objBotonGuardarCambios = Ext.getCmp('idGuardarCambios');
    if (objBotonGuardarCambios) {
      if (status) {
        objBotonGuardarCambios.enable();
      } else {
        objBotonGuardarCambios.disable();
      }
    }
  }

  function cargarAjustePermisoCordinador() {
    if (!boolPermisoCoordinador) {
      let objBotonGuardarCambios = Ext.getCmp('idGuardarCambios');
      if (objBotonGuardarCambios) {
        objBotonGuardarCambios.setVisible(false);
      }

      Ext.getCmp('idPanelRepresentanteLegal').setVisible(false);
      Ext.getCmp('idGridColumnAcciones').setVisible(false);
      Ext.getCmp('idGridColumnFormaContacto').setWidth(200).setWidth('auto');
      Ext.getCmp('formRepresentanteLegal').setHeight(490).setHeight('auto');
      Ext.getCmp('idGridRepresentanteLegal').setHeight(485).setHeight('auto');

    }
  }

  let objComboTipoIdent = new Ext.create('Ext.form.ComboBox', {
    id: 'cbxTipoIdent',
    fieldLabel: 'Tipo Identificación',
    store: ['CED', 'RUC', 'PAS', labelRucJuridico],
    labelAlign: 'left',
    queryMode: 'local',
    editable: false,
    displayField: '',
    valueField: '',
    width: 325,
  });
  let objTextIdentificacion = Ext.create('Ext.form.Text', {
    id: 'txtidIdentificacion',
    name: 'intIdentificacion',
    fieldLabel: 'Identificación',
    labelAlign: 'left',
    width: 325,
    value: '',
  });
  let objTextTipoTributario = Ext.create('Ext.form.Text', {
    id: 'txtidTipoTributario',
    name: 'strTipoTributario',
    fieldLabel: 'TipoTributario',
    labelAlign: 'left',
    width: 325,
    value: '',
    disabled: true
  });
  let objTextRazonSocial = Ext.create('Ext.form.Text', {
    id: 'txtidRazonSocial',
    name: 'strRazonSocial',
    fieldLabel: 'RazonSocial',
    labelAlign: 'left',
    width: 325,
    value: '',
    hidden: true
  });

  let objTextNombres = Ext.create('Ext.form.Text', {
    id: 'txtidNombres',
    name: 'strNombres',
    fieldLabel: 'Nombres',
    labelAlign: 'left',
    width: 325,
    value: '',
    hidden: true
  });
  let objTextApellidos = Ext.create('Ext.form.Text', {
    id: 'txtidApellidos',
    name: 'strApellidos',
    fieldLabel: 'Apellidos',
    labelAlign: 'left',
    width: 325,
    value: '',
    hidden: true
  });
  let objTextCargo = Ext.create('Ext.form.Text', {
    id: 'txtidCargo',
    name: 'strCargo',
    fieldLabel: 'Cargo',
    labelAlign: 'left',
    width: 325,
    value: '',
  });

  let objTextDireccion = Ext.create('Ext.form.Text', {
    id: 'txtidDireccion',
    name: 'strDireccion',
    fieldLabel: 'Dirección',
    labelAlign: 'left',
    width: 325,
    value: '',
  });
  let objDateExpiracion = new Ext.form.DateField({
    id: 'dateidExpiracion',
    name: 'dateExpiracion',
    fieldLabel: 'Expiración nombramiento',
    labelAlign: 'left',
    width: 325,
    editable: false,
    xtype: 'datefield',
    format: 'd/m/Y',
    minValue: new Date(),
    value: '',
  });

  let objBtnBuscarRepresentante = new Ext.create('Ext.button.Button', {
    text: 'Consultar identificación',
    iconCls: 'icon_search',
    handler: function () {

      if (objComboTipoIdent.getValue() == labelRucJuridico) {
        objComboTipoIdent.setValue('RUC');
        objTextTipoTributario.setValue('JUR');
      } else {
        objTextTipoTributario.setValue('NAT');
      }

      if (
        Ext.isEmpty(objComboTipoIdent.getValue()) ||
        Ext.isEmpty(objTextIdentificacion.getValue())
      ) {
        Ext.Msg.alert('Alerta', 'Es necesario una identificación.');
        return false;
      }
      panel.getEl().mask('Consultando identificación...');

      if (bolFalloConsultarRepresentante) {
        storeRepresentantes.load();
      }

      bloquearControlFormulario(true);
      let strTipoIdentificacion = objComboTipoIdent
        .getValue()
        .toUpperCase()
        .trim();

      let strIdentificacion = objTextIdentificacion
        .getValue()
        .toUpperCase()
        .trim();
      let strTipoTributario = objTextTipoTributario
        .getValue()
        .toUpperCase()
        .trim();

      Ext.Ajax.request({
        url: urlRepresentanteLegalVerificar,
        method: 'POST',
        async: true,
        timeout: 60000,
        params: {
          strTipoIdentificacionCliente,
          strIdentificacionCliente,
          strOrigen,
          strTipoIdentificacion,
          strIdentificacion,
          strTipoTributario
        },
        success: function (data) {
          let result = Ext.decode(data.responseText);
          panel.getEl().unmask();
          if (result.status === 'OK' && !Ext.isEmpty(result.response)) {
            let data = result.response;
            cargarDatosFormulario(data);
            bloquearControlFormulario(false);
          } else {
            Ext.Msg.alert('Error', result.message);
          }
        },
        failure: function (result) {
          panel.getEl().unmask();
          Ext.Msg.alert('Error ', result.statusText);
        },
      });
    },
  });
  let objBtnAgregarRepresentante = new Ext.create('Ext.button.Button', {
    text: 'Agregar',
    name: 'btnAgregar',
    id: 'idBtnAgregar',
    disabled: false,
    handler: function () {
      agregarItemRepresentante();
    },
  });
  let objBtnLimpiarRepresentante = new Ext.create('Ext.button.Button', {
    text: 'Limpiar',
    name: 'btnLimpiar',
    id: 'idBtnLimpiar',
    disabled: false,
    handler: function () {
      limpiarFormulario();
    },
  });

  let modelAdmiFormaContacto = Ext.define('modelAdmiFormaContacto', {
    extend: 'Ext.data.Model',
    fields: [
      { name: 'intIdFormaContacto', type: 'int' },
      { name: 'strDescripcionFormaContacto', type: 'string' },
      { name: 'strEstado', type: 'string' },
    ],
    idProperty: 'intIdFormaContacto',
  });
  let modelPersonaFormaContactoModel = Ext.define(
    'personaFormaContactoModel',
    {
      extend: 'Ext.data.Model',
      fields: [
        {
          name: 'idPersonaFormaContacto',
          type: 'integer',
          mapping: 'idPersonaFormaContacto',
        },
        { name: 'formaContactoId', type: 'integer', mapping: 'formaContactoId' },
        { name: 'formaContacto', type: 'string', mapping: 'formaContacto' },
        { name: 'valor', type: 'string', mapping: 'valor' },
      ],
      idProperty: 'formaContactoId',
    }
  );
  let modelRepresentanteLegalModel = Ext.define('representanteLegalModel', {
    extend: 'Ext.data.Model',
    fields: [
      { name: 'idPersona', type: 'integer' },
      { name: 'idRepresentanteLegal', type: 'integer' },
      { name: 'tipoIdentificacion', type: 'string' },
      { name: 'tipoTributario', type: 'string' },
      { name: 'identificacion', type: 'string' },
      { name: 'razonSocial', type: 'string' },
      { name: 'nombres', type: 'string' },
      { name: 'apellidos', type: 'string' },
      { name: 'cargo', type: 'string' },
      { name: 'tipoTributario', type: 'string' },
      { name: 'direccion', type: 'string' },
      { name: 'fechaExpiracionNombramiento', type: 'string' },
      { name: 'formaContacto', type: 'json' },
    ],
  });

  let storeAdmiFormaContacto = new Ext.create('Ext.data.Store', {
    model: modelAdmiFormaContacto,
    autoLoad: true,
    proxy: {
      type: 'ajax',
      url: urlGetAdmiFormaContacto,
      timeout: 90000,
      reader: {
        type: 'json',
        root: 'registros',
      },
      extraParams: {
        strEstado: 'Activo',
      },
      simpleSortMode: true,
    },
  });
  let storeCreaPersonaFormaContacto = Ext.create('Ext.data.Store', {
    id: 'idStoreCreaPersonaFormaContacto',
    pageSize: 5,
    autoDestroy: true,
    model: modelPersonaFormaContactoModel,
    proxy: {
      type: 'memory',
    },
    hasChanged: false,
    listeners: {
      update: function (store, record, operation, modifiedFieldNames, eOpts) {
        recordFormaContacto = storeAdmiFormaContacto.findRecord(
          'strDescripcionFormaContacto',
          record.data.formaContacto
        );
        if (!Ext.isEmpty(recordFormaContacto) && store.indexOf(record) >= 0) {
          store.getAt(
            store.indexOf(record)
          ).data.formaContactoId = recordFormaContacto.getId();
          store.commitChanges();
        }
      },
    },
  });
  let storeRepresentantes = Ext.create('Ext.data.Store', {
    model: modelRepresentanteLegalModel,
    autoLoad: true,
    proxy: {
      type: 'ajax',
      url: urlRepresentanteLegalConsultar,
      timeout: 60000,
      reader: {
        type: 'json',
        root: 'response',
        message: 'message',
        statusProperty: 'status',
      },
      extraParams: {
        strOrigen,
        strTipoIdentificacionCliente,
        strIdentificacionCliente,
      },
      simpleSortMode: true,
    },

    listeners: {
      beforeload: function (sender, options) {
        panel
          .getEl()
          .mask(
            'Consultando representante legal vinculados, Por favor espere!!'
          );
        bolFalloConsultarRepresentante = false;
      },
      load: function (sender, node, records, ddd) {
        panel.getEl().unmask();
        let jsonData = sender.proxy.reader.jsonData||{};
        if (jsonData.status != 'OK') {
          Ext.Msg.alert('Error ', jsonData.message);
          bolFalloConsultarRepresentante = true;
        }

        if (objView != 'modal') {
          cargarAjusteVistaTab();
        }
      },
    },
  });

  let rowEditingPersFormaContacto = Ext.create('Ext.grid.plugin.RowEditing', {
    saveBtnText: 'Guardar',
    cancelBtnText: 'Cancelar',
    clicksToMoveEditor: 1,
    autoCancel: false,
    listeners: {
      canceledit: function (editor, e, eOpts) {
        e.store.remove(e.record);
      },
      afteredit: function (roweditor, changes, record, rowIndex) {
        let intCountGridDetalle = Ext.getCmp('gridCreaPersonaFormaContacto')
          .getStore()
          .getCount();
        let selectionModel = Ext.getCmp(
          'gridCreaPersonaFormaContacto'
        ).getSelectionModel();
        selectionModel.select(0);
        if (
          Utils.existStringIn(
            'CORREO',
            changes.newValues.formaContacto.trim().toUpperCase()
          )
        ) {
          if (!Utils.validateMail(changes.newValues.valor.trim())) {
            Ext.Msg.alert(
              'Error',
              'El formato de correo no es correcto, favor revisar.'
            );
            rowEditingPersFormaContacto.startEdit(0, 0);
            return false;
          }
        }
        if (
          Utils.existStringIn(
            'TELEFONO',
            changes.newValues.formaContacto.trim().toUpperCase()
          )
        ) {
          if (
            Utils.existStringIn(
              'TELEFONO INTERNACIONAL',
              changes.newValues.formaContacto.trim().toUpperCase()
            )
          ) {
            if (
              !Utils.validateFoneMin7Max15(changes.newValues.valor.trim())
            ) {
              Ext.Msg.alert(
                'Error',
                'El formato de teléfono internacional no es correcto.! <br>' +
                'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                'Se permite un <b>mínimo de 7 dígitos y un máximo de 15 dígitos</b>. <br>' +
                'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.'
              );
              rowEditingPersFormaContacto.startEdit(0, 0);
              return false;
            }
          } else {
            if (strNombrePais === 'PANAMA') {
              if (
                Utils.existStringIn(
                  'TELEFONO FIJO',
                  changes.newValues.formaContacto.trim().toUpperCase()
                ) &&
                !/^(\+?\d{1,3}?[- .]?\d{1,3}[- .]?\d{1,4})$/.test(
                  changes.newValues.valor.trim()
                )
              ) {
                Ext.Msg.alert(
                  'Error',
                  'El formato de teléfono fijo no es correcto.! <br>' +
                  'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                  'Se permite mínimo <b>7 dígitos</b>. <br>' +
                  'No se permiten <b>caracteres especiales excepto + - .</b>, favor revisar.'
                );
                rowEditingPersFormaContacto.startEdit(0, 0);
                return false;
              }
              if (
                Utils.existStringIn(
                  'TELEFONO MOVIL',
                  changes.newValues.formaContacto.trim().toUpperCase()
                ) &&
                !/^[0-9]{8}$/.test(changes.newValues.valor.trim())
              ) {
                Ext.Msg.alert(
                  'Error',
                  'El formato de teléfono móvil no es correcto.! <br>' +
                  'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                  'Solo se permiten <b>8 dígitos</b>. <br>' +
                  'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.'
                );
                rowEditingPersFormaContacto.startEdit(0, 0);
                return false;
              }
            } else {
              if (
                !Utils.validateFoneMin8Max10(changes.newValues.valor.trim())
              ) {
                Ext.Msg.alert(
                  'Error',
                  'El formato de teléfono no es correcto.! <br>' +
                  'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                  'Se permite un <b>mínimo de 8 dígitos y un máximo de 10 dígitos</b>. <br>' +
                  'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.'
                );
                rowEditingPersFormaContacto.startEdit(0, 0);
                return false;
              }
            }
          }
        }
        if (intCountGridDetalle > 0) {
          if (
            Ext.isEmpty(
              Ext.getCmp('gridCreaPersonaFormaContacto')
                .getStore()
                .getAt(0)
                .data.formaContacto.trim()
            ) ||
            Ext.isEmpty(
              Ext.getCmp('gridCreaPersonaFormaContacto')
                .getStore()
                .getAt(0)
                .data.valor.trim()
            )
          ) {
            Ext.Msg.alert('Error', 'No puede ingresar registros en blanco.');
            rowEditingPersFormaContacto.cancelEdit();
            selectionModel.select(0);
            rowEditingPersFormaContacto.startEdit(0, 0);
            return false;
          }
        }
        for (let i = 1; i < intCountGridDetalle; i++) {
          if (
            Ext.getCmp('gridCreaPersonaFormaContacto')
              .getStore()
              .getAt(i)
              .get('formaContacto') ===
            changes.newValues.formaContacto.trim() &&
            Ext.getCmp('gridCreaPersonaFormaContacto')
              .getStore()
              .getAt(i)
              .get('valor') === changes.newValues.valor.trim()
          ) {
            Ext.Msg.alert(
              'Error',
              'Esta forma de contacto ya se encuentra previamente ingresada.'
            );
            rowEditingPersFormaContacto.startEdit(0, 0);
            break;
          }
        }
      },
    },
  });
  let btnCrearPersonaFormaContacto = Ext.create('Ext.button.Button', {
    text: 'Agregar forma contacto',
    width: 160,
    iconCls: 'button-grid-crearSolicitud-without-border',
    handler: function () {
      rowEditingPersFormaContacto.cancelEdit();

      let recordParamDet = Ext.create('personaFormaContactoModel', {
        idPersonaFormaContacto: null,
        formaContactoId: null,
        formaContacto: '',
        valor: '',
      });
      storeCreaPersonaFormaContacto.insert(0, recordParamDet);
      rowEditingPersFormaContacto.startEdit(0, 0);
      if (
        Ext.getCmp('gridCreaPersonaFormaContacto').getStore().getCount() > 1
      ) {
        if (
          Ext.isEmpty(
            Ext.getCmp('gridCreaPersonaFormaContacto')
              .getStore()
              .getAt(1)
              .data.formaContacto.trim()
          ) ||
          Ext.isEmpty(
            Ext.getCmp('gridCreaPersonaFormaContacto')
              .getStore()
              .getAt(1)
              .data.valor.trim()
          )
        ) {
          Ext.Msg.alert('Error', 'No puede ingresar registros en blanco.');
          let selectionModel = Ext.getCmp(
            'gridCreaPersonaFormaContacto'
          ).getSelectionModel();
          rowEditingPersFormaContacto.cancelEdit();
          storeCreaPersonaFormaContacto.remove(selectionModel.getSelection());
          selectionModel.select(0);
          rowEditingPersFormaContacto.startEdit(0, 0);
        }
      }

      if (!Ext.isEmpty(objBtnAgregarRepresentante)) {
        objBtnAgregarRepresentante.enable();
      }
    },
  });
  let btnDeletePersonaFormaContacto = Ext.create('Ext.button.Button', {
    text: 'Limpiar forma contacto',
    width: 160,
    iconCls: 'button-grid-quitarFacturacionElectronica-without-border',
    handler: function () {
      let gridCreaPersonaFormaContacto = Ext.getCmp(
        'gridCreaPersonaFormaContacto'
      );
      let selectionModel = gridCreaPersonaFormaContacto.getSelectionModel();
      rowEditingPersFormaContacto.cancelEdit();
      storeCreaPersonaFormaContacto.remove(selectionModel.getSelection());
      if (storeCreaPersonaFormaContacto.getCount() > 0) {
        selectionModel.select(0);
      }
      if (!Ext.isEmpty(objBtnAgregarRepresentante)) {
        objBtnAgregarRepresentante.enable();
      }
    },
  });
  let toolbarPersFormaContacto = Ext.create('Ext.toolbar.Toolbar', {
    dock: 'top',
    align: '->',
    items: [
      { xtype: 'tbfill' },
      btnCrearPersonaFormaContacto,
      btnDeletePersonaFormaContacto,
    ],
  });

  let panel = Ext.getCmp('idPanelGestorRepresentante');
  if (panel) {
    panel.destroy();
  }

  panel = Ext.create('Ext.form.Panel', {
    renderTo: objView == 'modal' ? null : objView,
    id: 'idPanelGestorRepresentante',
    fullscreen: true,
    layout: {
      layout: 'vbox',
      pack: 'center',
      align: 'middle',
      autoScroll: true,
      tableAttrs: {
        style: {
          width: '100%',
        },
      },
      tdAttrs: {
        align: 'center',
        valign: 'top',
        autoScroll: true,
      },
    },

    items: [
      {
        id: 'idPanelRepresentanteLegal',
        title: 'Representante Legal',
        header: false,
        height: 320,
        bodyPadding: 5,
        border: false,
        padding: 0,   
        autoScroll: true,

        layout: {
          type: 'table',
          columns: 2,
          pack: 'center',
          align: 'middle',
          autoScroll: true,
          tableAttrs: {
            style: {
              width: '100%',
            },
          },
          tdAttrs: {
            align: 'center',
            valign: 'top',
            autoScroll: true,
          },
        },
        items: [
          {
            title: 'Información  general',
            xtype: 'fieldset',
            columnWidth: 0.5,
            collapsible: false,
            defaultType: 'textfield',
            defaults: { anchor: '100%' },
            layout: 'anchor',
            autoHeight: true,
            autoScroll: true,
            width: 400,
            height: 270,
            margin: 5,
            padding: 5,
            items: [
              objComboTipoIdent,
              objTextIdentificacion,
              { xtype: 'tbspacer', height: 5 },
              objBtnBuscarRepresentante,
              { xtype: 'tbspacer', height: 10 },
              objTextTipoTributario,
              objTextRazonSocial,
              objTextNombres,
              objTextApellidos,
              objTextCargo,
              objTextDireccion,
              objDateExpiracion,
            ],
          },
          {
            id: 'formFormaContacto',
            title: 'Formas de Contacto',
            xtype: 'fieldset',
            columnWidth: 0.5,
            collapsible: false,
            defaultType: 'textfield',
            defaults: { anchor: '100%' },
            layout: 'anchor',
            autoHeight: true,
            autoScroll: true,
            margin: 5,
            padding: 5,
            style: {
              'margin-right': '-10px',
            },
            items: [
              {
                xtype: 'grid',
                store: storeCreaPersonaFormaContacto,
                plugins: [rowEditingPersFormaContacto],
                dockedItems: [toolbarPersFormaContacto],
                id: 'gridCreaPersonaFormaContacto',
                height: 242,
                columns: [
                  {
                    dataIndex: 'formaContactoId',
                    hidden: true,
                  },
                  {
                    header: 'Forma contacto',
                    dataIndex: 'formaContacto',
                    width: 300,
                    editor: new Ext.form.field.ComboBox({
                      typeAhead: true,
                      id: 'cbxFormaContacto',
                      name: 'cbxFormaContacto',
                      valueField: 'strDescripcionFormaContacto',
                      displayField: 'strDescripcionFormaContacto',
                      store: storeAdmiFormaContacto,
                      editable: false,
                    }),
                  },
                  {
                    header: 'Valor',
                    dataIndex: 'valor',
                    width: 300,
                    editor: 'textfield',
                  },
                ],
              },
            ],
          },
        ],

        buttonAlign: 'center',
        buttons: [objBtnLimpiarRepresentante, objBtnAgregarRepresentante],
      },
      {
        id: 'formRepresentanteLegal',
        name: 'formRepresentanteLegal',
        title: 'Representante Legal',
        xtype: 'fieldset',
        columnWidth: 0.5,
        collapsible: false,
        defaultType: 'textfield',
        defaults: { anchor: '100%' },
        layout: 'anchor',
        autoHeight: true,
        autoScroll: true,
        margin: 10,
        padding: 5,
        style: {
          'margin-right': '-10px',
        },
        items: [
          {
            xtype: 'grid',
            store: storeRepresentantes,
            id: 'idGridRepresentanteLegal',
            width: '100%',
            height: 170,
            columns: [
              {
                dataIndex: 'idRepresentanteLegal',
                hidden: true,
                hideable: false,
              },
              {
                header: 'Tipo Identificacion',
                dataIndex: 'tipoIdentificacion',
                editor: 'textfield',
                editable: false,
                width: '5%',
              },

              {
                header: 'Tipo Persona',
                dataIndex: 'tipoTributario',
                editor: 'textfield',
                editable: false,
                width: '5%',
              },
              {
                header: 'Identificacion',
                dataIndex: 'identificacion',
                editor: 'textfield',
                editable: false,
                width: '10%',
              },
              {
                header: 'Razon Social / Nombres',
                dataIndex: 'nombreGeneral',
                editor: 'textfield',
                editable: false,
                width: '20%',
                renderer: function (
                  values,
                  metaData,
                  record,
                  rowIndex,
                  colIndex,
                  store
                ) {
                  let raw = record.raw;
                  let razonSocial = raw.razonSocial || '';
                  let nombres = raw.nombres || '';
                  let apellidos = raw.apellidos || '';
                  let html = '';
                  if (raw.tipoTributario == 'JUR') {
                    html = razonSocial;
                  } else {
                    html = nombres + ' ' + apellidos;
                  }
                  return html;
                },

              },

              {
                header: 'Razon Social',
                dataIndex: 'nombres',
                editor: 'textfield',
                editable: false,
                hidden: true,

              },
              {
                header: 'Nombres',
                dataIndex: 'nombres',
                editor: 'textfield',
                editable: false,
                hidden: true,
              },
              {
                header: 'Apellidos',
                dataIndex: 'apellidos',
                editor: 'textfield',
                editable: false,
                hidden: true,
                width: '10%',
              },
              {
                header: 'Cargo',
                dataIndex: 'cargo',
                editor: 'textfield',
                editable: false,
                width: '10%',
              },
              {
                header: 'Dirección',
                dataIndex: 'direccion',
                editor: 'textfield',
                editable: false,
                width: '10%',
              },
              {
                id: 'idGridColumnFormaContacto',
                header: 'Exp.Nombramiento',
                dataIndex: 'fechaExpiracionNombramiento',
                editor: 'textfield',
                editable: false,
                width: '10%',
              },
              {
                header: 'Contacto',
                dataIndex: 'formaContacto',
                editor: 'textfield',
                editable: false,
                width: '20%',
                renderer: function (
                  values,
                  metaData,
                  record,
                  rowIndex,
                  colIndex,
                  store
                ) {
                  let html = '<ul>';
                  for (let index = 0; index < values.length; index++) {
                    const el = values[index];

                    html =
                      html +
                      '<li><b>' +
                      el.formaContacto +
                      '</b>:' +
                      el.valor +
                      ' </li>';
                  }
                  html = html + '</ul>';
                  return html;
                },
              },
              {
                id: 'idGridColumnAcciones',
                text: 'Acciones',
                align: 'center',
                width: '8%',
                renderer: function (
                  values,
                  metaData,
                  record,
                  rowIndex,
                  colIndex,
                  store
                ) {
                  let id = Ext.id();
                  Ext.defer(function () {
                    new Ext.Button({
                      text: '',
                      iconCls: 'icon_edit',
                      margin: 2,
                      padding: 5,
                      handler: function (btn, e) {
                        cargarDatosFormulario(record.raw);
                        bloquearControlFormulario(false);
                      },
                    }).render(document.body, id);

                    new Ext.Button({
                      text: '',
                      iconCls: 'icon_delete',
                      margin: 2,
                      padding: 5,
                      handler: function (btn, e) {
                        eliminarItemLocalRepresentanteLegal(record.raw);
                      },
                    }).render(document.body, id);
                  }, 50);

                  return Ext.String.format('<div id="{0}"></div>', id);
                },
              },
            ],
            listeners: {
              itemdblclick: function (grid, record) { },
            },
          },
        ],
      },
    ],
  });

  bloquearControlFormulario(true);
  if (objView == 'modal') {
    let msj = (!boolPermisoCoordinador ? ' (Solo el coordinador puede editar)' : '');
    var modal = Ext.create('Ext.window.Window', {
      title: 'Ver Representante Legal' + msj,
      height: 600,
      width: 1100,
      modal: true,
      layout: {
        type: 'fit',
        align: 'stretch',
        pack: 'start',
      },
      floating: true,
      shadow: true,
      shadowOffset: 20,
      items: [panel],
      buttonAlign: 'center',
      buttons: [
        {
          text: 'Cerrar',
          handler: function () {
            cancelarCambiosRepresentatesLegal();
          },
        },

        {
          text: 'Guardar',
          id: 'idGuardarCambios',
          handler: function () {
            actualizarListaRepresentatesLegal();
          },
        },
      ],
    });
    modal.show();
    existenCambiosListado(false);
    cargarAjustePermisoCordinador();
  } else {
   setTimeout(() => {
    cargarAjustePermisoCordinador();
   }, 100);
    return panel;
  }



}

function getDataJsonStore(store) {
  let arrayJson = [];
  let intTamanio = store.getCount();
  for (let index = 0; index < intTamanio; index++) {
    const item = store.getAt(index).data;
    arrayJson.push(item);
  }
  return arrayJson;
}

function validarDataActualizar(arrayRepresentantes) {
  let intReprPersonaTipoNaturalMin = 1;
  let intReprPersonaTipoNaturalMax = 1;
  let intCountReprPersonaTipoNatural = 0;
  let strMensaje = '';

  for (let index = 0; index < arrayRepresentantes.length; index++) {
    const el = arrayRepresentantes[index];
    if (el.tipoTributario == 'NAT') {
      intCountReprPersonaTipoNatural = intCountReprPersonaTipoNatural + 1;
    }
  }

  if (intCountReprPersonaTipoNatural < intReprPersonaTipoNaturalMax) {
    strMensaje = 'Es requerido un representante legal de tipo natural.';
  }

  if (intCountReprPersonaTipoNatural > intReprPersonaTipoNaturalMin) {
    strMensaje =
      'Solo esta permitido ingresar un representante legal de tipo natural.';
  }

  return strMensaje;
}

function getDataRepresentanteLegal() {
  let gridRepresentanteLegal = Ext.getCmp('idGridRepresentanteLegal');
  let arrayRepresentantes = [];
  if (gridRepresentanteLegal) {
    let storeRepresentantes = gridRepresentanteLegal.getStore();
    arrayRepresentantes = getDataJsonStore(storeRepresentantes);
    let strValidaData = validarDataActualizar(arrayRepresentantes);

    if (strValidaData != '') {
      Ext.Msg.alert('Alerta', strValidaData);
      arrayRepresentantes = [];
    }
  } else {
    Ext.Msg.alert('Alerta', 'Es requerido un representante legal.');
  }

  return arrayRepresentantes;
}

function cargarAjusteVistaTab() {
  setTimeout(() => {
    Ext.getCmp('formRepresentanteLegal').setHeight(150).setHeight('auto');
    Ext.getCmp('idGridRepresentanteLegal').setHeight(120).setHeight('auto');
  }, 10);
}
