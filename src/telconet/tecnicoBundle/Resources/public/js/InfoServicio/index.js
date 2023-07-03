Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    storeClientes = new Ext.data.Store({
        total: 'total',
        //autoLoad:true,
        proxy: {
            type: 'ajax',
            url : getNombresClientes,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idPersona', mapping:'idPersona'},
                {name:'nombreCompleto', mapping:'nombreCompleto'}
              ]
    });

    storePlanes = new Ext.data.Store({
        total: 'total',
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : getPlanesPorEstado,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                plan: ""
            }
        },
        fields:
              [
                {name:'idPlan', mapping:'idPlan'},
                {name:'nombrePlan', mapping:'nombrePlan'}
              ]
    });

    storeProductos = new Ext.data.Store({
        total: 'total',
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : getProductosPorEstado,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idProducto', mapping:'idProducto'},
                {name:'descripcionProducto', mapping:'descripcionProducto'}
              ]
    });

    storeTipoServicios = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getTiposServicios,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
          {name:'idTipo',          mapping:'idTipo'},
          {name:'descripcionTipo', mapping:'descripcionTipo'}
        ]
    });

    storeEstados = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getEstadosServicios,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
          {name:'idEstado',          mapping:'idEstado'},
          {name:'descripcionEstado', mapping:'descripcionEstado'}
        ]
    });

    store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getServiciosClientes,
            timeout: 3000000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                login: '',
                ordenTrabajo: '',
                producto: '',
                plan: '',
                estado: null,
                idElemento: '',
                idInterface:'',
                intServicioFTTxTN:''
            }
        },
        fields:
                  [
                    {name:'idServicio',                     mapping:'idServicio'},
                    {name:'booleanEsSDWAN',                 mapping:'booleanEsSDWAN'},
                    {name:'booleanEsMigracionSDWAN',        mapping:'booleanEsMigracionSDWAN'},
                    {name:'strTipoRed',                     mapping:'strTipoRed'},
                    {name:'strNGFNubePublica',              mapping:'NGFNubePublica', defaultValue: ''},
                    {name:'strNGFPlan',                     mapping:'NGFPlan', defaultValue: ''},
                    {name:'strNGFModeloEquipo',             mapping:'NGFModeloEquipo', defaultValue: ''},
                    {name:'strNGFPropiedadEquipo',          mapping:'NGFPropiedadEquipo', defaultValue: ''},
                    {name:'strNGFSerieEquipo',              mapping:'NGFSerieEquipo', defaultValue: ''},
                    {name:'strNGFMACEquipo',                mapping:'NGFMACEquipo', defaultValue: ''},
                    {name:'strNGFIpDns',                    mapping:'NGFIpDns', defaultValue: ''},
                    {name:'strNGFPuertoAdministracionWeb',  mapping:'NGFPuertoAdministracionWeb', defaultValue: ''},
                    {name:'strNGFlicencia',                 mapping:'NGFlicencia', defaultValue: ''},
                    {name:'strNGFNombreModeloElemento',     mapping:'NGFNombreModeloElemento', defaultValue: ''},
                    {name:'booleanTipoRedGpon',             mapping:'booleanTipoRedGpon', defaultValue: false},
                    {name:'strExisteHistorial',             mapping:'strExisteHistorial'},
                    {name:'tipoEnlace',                     mapping:'tipoEnlace'},
                    {name:'idServicioRefIpFija',            mapping:'idServicioRefIpFija'},
                    {name:'macIpFija',                      mapping:'macIpFija'},
                    {name:'tieneIpFijaActiva',              mapping:'tieneIpFijaActiva'},
                    {name:'puntoCoberturaId',               mapping:'puntoCoberturaId'},
                    {name:'puntoLatitud',                   mapping:'puntoLatitud'},
                    {name:'puntoLongitud',                  mapping:'puntoLongitud'},
                    {name:'puntoCanton',                    mapping:'puntoCanton'},
                    {name:'puntoDireccion',                 mapping:'puntoDireccion'},
                    {name:'login',                          mapping:'login'},
                    {name:'loginAux',                       mapping:'loginAux'},
                    {name:'esNodoWifi',                     mapping:'esNodoWifi'},
                    {name:'permiteRutaEstaticaBuckup',      mapping:'permiteRutaEstaticaBuckup'},
                    {name:'idPersonaEmpresaRol',            mapping:'idPersonaEmpresaRol'},
                    {name:'nombreCompleto',                 mapping:'nombreCompleto'},
                    {name:'nombrePlan',                     mapping:'nombrePlan'},
                    {name:'strPermiteActivarServicio',      mapping:'strPermiteActivarServicio'},
                    {name:'anillo',                         mapping:'anillo'},
                    {name:'elementoId',                     mapping:'elementoId'},
                    {name:'elementoNombre',                 mapping:'elementoNombre'},
                    {name:'elementoPadre',                  mapping:'elementoPadre'},
                    {name:'idElementoPadre',                mapping:'idElementoPadre'},
                    {name:'modeloElemento',                 mapping:'modeloElemento'},
                    {name:'marcaElemento',                  mapping:'marcaElemento'},
                    {name:'interfaceElementoId',            mapping:'interfaceElementoId'},
                    {name:'interfaceElementoNombre',        mapping:'interfaceElementoNombre'},
                    {name:'popNombre',                      mapping:'popNombre'},
                    {name:'popId',                          mapping:'popId'},
                    {name:'ipElemento',                     mapping:'ipElemento'},
                    {name:'ultimaMilla',                    mapping:'ultimaMilla'},
                    {name:'codUltimaMilla',                 mapping:'codUltimaMilla'},
                    {name:'capacidadUno',                   mapping:'capacidadUno'},
                    {name:'capacidadDos',                   mapping:'capacidadDos'},
                    {name:'capacidadTres',                  mapping:'capacidadTres'},
                    {name:'capacidadCuatro',                mapping:'capacidadCuatro'},
                    {name:'perfilDslam',                    mapping:'perfilDslam'},
                    {name:'mac',                            mapping:'mac'},
                    {name:'vlan',                           mapping:'vlan'},
                    {name:'vrf',                            mapping:'vrf'},
                    {name:'rdId',                           mapping:'rdId'},
                    {name:'asPrivado',                      mapping:'asPrivado'},
                    {name:'protocolo',                      mapping:'protocolo'},
                    {name:'defaultGateway',                 mapping:'defaultGateway'},
                    {name:'capacidadUnoId',                 mapping:'capacidadUnoId'},
                    {name:'capacidadDosId',                 mapping:'capacidadDosId'},
                    {name:'perfilDslamId',                  mapping:'perfilDslamId'},
                    {name:'macId',                          mapping:'macId'},
                    {name:'productoId',                     mapping:'productoId'},
                    {name:'descripcionProducto',            mapping:'descripcionProducto'},
                    {name:'esConcentrador',                 mapping:'esConcentrador'},
                    {name:'esSdwan',                        mapping:'esSdwan'},
                    {name:'nombreProducto',                 mapping:'nombreProducto'},
                    {name:'productoEsEnlace',               mapping:'productoEsEnlace'},
                    {name:'numeroOrdenTrabajo',             mapping:'numeroOrdenTrabajo'},
                    {name:'fechaUltActualizacion',          mapping:'fechaUltActualizacion'},
                    {name:'tipoOrden',                      mapping:'tipoOrden'},
                    {name:'tipoOrdenCompleto',              mapping:'tipoOrdenCompleto'},
                    {name:'cantidad',                       mapping:'cantidad'},
                    {name:'cantidadReal',                   mapping:'cantidadReal'},
                    {name:'tieneSolicitudCambioCpe',        mapping:'tieneSolicitudCambioCpe'},
                    {name:'tieneSolicitudMigracion',        mapping:'tieneSolicitudMigracion'},
                    {name:'strEsAgregarEquipoMasivo',       mapping:'strEsAgregarEquipoMasivo'},
                    {name:'strEsCambioEquiSoporteMasivo',   mapping:'strEsCambioEquiSoporteMasivo'},
                    {name:'tieneSolicitudAgregarEquipo',    mapping:'tieneSolicitudAgregarEquipo'},
                    {name:'tieneSolicitudPlanificacion',    mapping:'tieneSolicitudPlanificacion'},
                    {name:'idSolicitudMigracionExtender',   mapping:'idSolicitudMigracionExtender'},
                    {name:'strEsCambioOntPorSolAgregarEquipo',  mapping:'strEsCambioOntPorSolAgregarEquipo'},
                    {name:'strTipoOntNuevoPorSolAgregarEquipo', mapping:'strTipoOntNuevoPorSolAgregarEquipo'},
                    {name:'strEsSmartWifi',                 mapping:'strEsSmartWifi'},
                    {name:'strCambioAWifiDualBand',         mapping:'strCambioAWifiDualBand'},
                    {name:'strAgregaExtenderDualBand',      mapping:'strAgregaExtenderDualBand'},
                    {name:'strPermiteReintentoMcAfee',      mapping:'strPermiteReintentoMcAfee'},
                    {name:'strMcAfeeActivo',                mapping:'strMcAfeeActivo'},
                    {name:'strNuevoAntivirus',              mapping:'strNuevoAntivirus'},
                    {name:'strNuevoAntivirusActivo',        mapping:'strNuevoAntivirusActivo'},
                    {name:'strReintentoNuevoAntivirus',     mapping:'strReintentoNuevoAntivirus'},
                    {name:'strReintentoPromoBw',            mapping:'strReintentoPromoBw'},
                    {name:'strPermiteCancelLogica',         mapping:'strPermiteCancelLogica'},
                    {name:'strActivacionOrigen',            mapping:'strActivacionOrigen'},
                    {name:'botones',                        mapping:'botones'},
                    {name:'idEmpresa',                      mapping:'idEmpresa'},
                    {name:'prefijoEmpresa',                 mapping:'prefijoEmpresa'},
                    {name:'estado',                         mapping:'estado'},
                    {name:'estadoSolicitud',                mapping:'estadoSolicitud'},
                    {name:'estadoSolMigracionTunel',        mapping:'estadoSolMigracionTunel'},
                    {name:'estadoSolMigraAnillo',           mapping:'estadoSolMigraAnillo'},
                    {name:'estadoSolMigracionVlan',         mapping:'estadoSolMigracionVlan'},
                    {name:'clienteMigracionVlan',           mapping:'clienteMigracionVlan'},
                    {name:'estadoSolCambioUm',              mapping:'estadoSolCambioUm'},
                    {name:'requiereMac',                    mapping:'requiereMac'},
                    {name:'idSolicitudLineaPom',            mapping:'idSolicitudLineaPom'},
                    {name:'tieneEncuesta',                  mapping:'tieneEncuesta'},
                    {name:'tieneActa',                      mapping:'tieneActa'},
                    {name:'flujo',                          mapping:'flujo'},
                    {name:'informacionRadio',               mapping:'informacionRadio'},
                    {name:'migrado',                        mapping:'migrado'},
                    {name:'ipReservada',                    mapping:'ipReservada'},
                    {name:'ipServicio',                     mapping:'ipServicio'},
                    {name:'subredServicio',                 mapping:'subredServicio'},
                    {name:'gwSubredServicio',               mapping:'gwSubredServicio'},
                    {name:'mascaraSubredServicio',          mapping:'mascaraSubredServicio'},
                    {name:'ldap',                           mapping:'ldap'},
                    {name:'descripcionPresentaFactura',     mapping:'descripcionPresentaFactura'},
                    {name:'cacti',                          mapping:'cacti'},
                    {name:'usaUltimaMillaExistente',        mapping:'usaUltimaMillaExistente'},
                    {name:'esPseudoPe',                     mapping:'esPseudoPe'},
                    {name:'seMigraAPseudoPe',               mapping:'seMigraAPseudoPe'},
                    {name:'poseeProtocoloBGP',              mapping:'poseeProtocoloBGP'},
                    {name:'tipoSubred',                     mapping:'tipoSubred'},
                    {name:'subredServicio',                 mapping:'subredServicio'},
                    {name:'tieneSolCambioIp',               mapping:'tieneSolCambioIp'},
                    {name:'subredVsatBackbone',             mapping:'subredVsatBackbone'},
                    {name:'subredVsatCliente',              mapping:'subredVsatCliente'},
                    {name:'nombreSolucion',                 mapping:'nombreSolucion'},
                    {name:'perteneceSolucion',              mapping:'perteneceSolucion'},
                    {name:'esPreferenteSolucion',           mapping:'esPreferenteSolucion'},
                    {name:'tieneAlquilerServidores',        mapping:'tieneAlquilerServidores'},
                    {name:'grupo',                          mapping:'grupo'},
                    {name:'subgrupo',                       mapping:'subgrupo'},
                    {name:'requiereInfoTecnica',            mapping:'requiereInfoTecnica'},
                    {name:'nombreCanton',                   mapping:'nombreCanton'},
                    {name:'categoriaTelefonia',             mapping:'categoriaTelefonia'},
                    {name:'vlanLan',                        mapping:'vlanLan'},
                    {name:'vlanWan',                        mapping:'vlanWan'},
                    {name:'firewallDC',                     mapping:'firewallDC'},
                    {name:'arrayElementosActivos',          mapping:'arrayElementosActivos'},
                    {name:'velocidadISB',                   mapping:'velocidadISB'},
                    {name:'servicioHeredadoFact',           mapping:'servicioHeredadoFact'},
                    {name:'esISB',                          mapping:'esISB'},
                    {name:'seActivaServicioSolucion',       mapping:'seActivaServicioSolucion'},
                    {name:'registroEquipo',                 mapping:'registroEquipo'},
                    {name:'virtualConnect',                 mapping:'virtualConnect'},
                    {name:'iploopback',                     mapping:'iploopback'},
                    {name:'peExtremoL2',                    mapping:'peExtremoL2'},
                    {name:'strTrasladarExtenderDB',         mapping:'strTrasladarExtenderDB'},
                    {name:'strSincronizarExtenderDB',       mapping:'strSincronizarExtenderDB'},
                    {name:'strRucTg',                       mapping:'strRucTg'},
                    {name:'strReenvioCredencialTg',         mapping:'strReenvioCredencialTg'},
                    {name:'strReintentoCreacionTg',         mapping:'strReintentoCreacionTg'},
                    {name:'strCrearMonitoreoTG',            mapping:'strCrearMonitoreoTG'},
                    {name:'configuracionPeHsrp',            mapping:'configuracionPeHsrp'},
                    {name:'strCambioPassTg',                mapping:'strCambioPassTg'},
                    {name:'strTieneEquipoNuevo',            mapping:'strTieneEquipoNuevo'},
                    {name:'strTieneSolCambEquiSoporte',     mapping:'strTieneSolCambEquiSoporte'},
                    {name:'strEstadoSolCambEquiSoporte',    mapping:'strEstadoSolCambEquiSoporte'},
                    {name:'intIdSolCambioEquipoSoporte',    mapping:'intIdSolCambioEquipoSoporte'},
                    {name:'intIdElementoHw',                mapping:'intIdElementoHw'},
                    {name:'strModeloCpeOnt',                mapping:'strModeloCpeOnt'},
                    {name:'strNombreElementoHw',            mapping:'strNombreElementoHw'},
                    {name:'strSerieEquipoHw',               mapping:'strSerieEquipoHw'},
                    {name:'strMacEquipoHw',                 mapping:'strMacEquipoHw'},
                    {name:'strModeloEquipoHw',              mapping:'strModeloEquipoHw'},
                    {name:'strPermanenciaMinima',           mapping:'strPermanenciaMinima'},
                    {name:'intIdCaractCorreoMcAfee',        mapping:'intIdCaractCorreoMcAfee'},
                    {name:'strCorreoMcAfee',                mapping:'strCorreoMcAfee'},
                    {name:'intProductoMcAfeeId',            mapping:'intProductoMcAfeeId'},
                    {name:'tipoEsquema',                    mapping:'tipoEsquema'},
                    {name:'strPermanenciaMinima',           mapping:'strPermanenciaMinima'},
                    {name:'seActivaProducto',               mapping:'seActivaProducto'},
                    {name:'tieneProgresoActa',              mapping:'tieneProgresoActa'},
                    {name:'idServicioWifi',                 mapping:'idServicioWifi'},
                    {name:'idIntWifiSim',                   mapping:'idIntWifiSim'},
                    {name:'idServicioCou',                  mapping:'idServicioCou'},
                    {name:'idIntCouSim',                    mapping:'idIntCouSim'},
                    {name:'arraySolicitudWifi',             mapping:'arraySolicitudWifi'},
                    {name:'arrayConcentradoresWifi',        mapping:'arrayConcentradoresWifi'},
                    {name:'idSolicitud',                    mapping:'idSolicitud'},
                    {name: 'arrayDatosNodoWifi',            mapping:'arrayDatosNodoWifi'},
                    {name: 'objParametrosDet',              mapping: 'objParametrosDet'},
                    {name:'tieneProgresoRuta',              mapping:'tieneProgresoRuta'},
                    {name:'tieneProgresoMateriales',        mapping:'tieneProgresoMateriales'},
                    {name:'comunicacionId',                 mapping:'comunicacionId'},
                    {name:'personaId',                      mapping:'personaId'},
                    {name:'servicioId',                     mapping:'servicioId'},
                    {name:'detalleId',                      mapping:'detalleId'},
                    {name:'tareaId',                        mapping:'tareaId'},
                    {name:'requiereFibraTarea',             mapping:'requiereFibraTarea'},
                    {name:'loginSesion',                    mapping:'loginSesion'},
                    {name:'strMostrarInfoTelcoGraph',       mapping:'strMostrarInfoTelcoGraph'},
                    {name:'numeroBobinaInstal',             mapping:'numeroBobinaInstal'},
                    {name:'productoPermitidoReversarOT',    mapping:'productoPermitidoReversarOT'},
                    {name:'estadoNumeroBobinaInstal',       mapping:'estadoNumeroBobinaInstal'},
                    {name:'cantidadFibraInstMd',            mapping:'cantidadFibraInstMd'},
                    {name:'esNetlifeCloud',                 mapping:'esNetlifeCloud'},
                    {name:'strFlagActivSim',                mapping:'strFlagActivSim'},
                    {name:'boolRequiereRegistro',           mapping:'boolRequiereRegistro', defaultValue: null},
                    {name:'productoPermitidoRegistroEle',   mapping:'productoPermitidoRegistroEle', defaultValue: "N"},
                    {name:'estadoDatosSafecity',            mapping:'estadoDatosSafecity', defaultValue: "N"},
                    {name:'nombreServicioSafecity',         mapping:'nombreServicioSafecity', defaultValue: ""},
                    {name:'esServicioCamaraSafeCity',       mapping:'esServicioCamaraSafeCity', defaultValue: "N"},
                    {name:'esServicioWifiSafeCity',         mapping:'esServicioWifiSafeCity', defaultValue: "N"},
                    {name:'strExisteCamaraPtzGpon',         mapping:'strExisteCamaraPtzGpon', defaultValue: "N"},
                    {name:'esServicioCamaraVpnSafeCity',    mapping:'esServicioCamaraVpnSafeCity', defaultValue: "N"},
                    {name:'esServicioRequeridoSafeCity',    mapping:'esServicioRequeridoSafeCity', defaultValue: "N"},
                    {name:'requiereSerActivarSafecity',     mapping:'requiereSerActivarSafecity', defaultValue: "N"},
                    {name:'nombreSerRequiereSafecity',      mapping:'nombreSerRequiereSafecity', defaultValue: ""},
                    {name:'strEsVerificarSerRequerido',     mapping:'strEsVerificarSerRequerido', defaultValue: "N"},
                    {name:'strMensajeServicioRequerido',    mapping:'strMensajeServicioRequerido', defaultValue: ""},
                    {name:'strExisteSwPoeGpon',             mapping:'strExisteSwPoeGpon', defaultValue: "N"},
                    {name:'strActivarSwPoeGpon',            mapping:'strActivarSwPoeGpon', defaultValue: "N"},
                    {name:'strMigrarSwPoe',                 mapping:'strMigrarSwPoe', defaultValue: "N"},
                    {name:'vlanAdmin',                      mapping:'vlanAdmin', defaultValue: ""},
                    {name:'vrfAdmin',                       mapping:'vrfAdmin', defaultValue: ""},
                    {name:'idOnt',                          mapping:'idOnt', defaultValue: ""},
                    {name:'nombreOnt',                      mapping:'nombreOnt', defaultValue: ""},
                    {name:'serieOnt',                       mapping:'serieOnt', defaultValue: ""},
                    {name:'macOnt',                         mapping:'macOnt', defaultValue: ""},
                    {name:'marcaOnt',                       mapping:'marcaOnt', defaultValue: ""},
                    {name:'modeloOnt',                      mapping:'modeloOnt', defaultValue: ""},
                    {name:'idServicioSwPoe',                mapping:'idServicioSwPoe', defaultValue: null},
                    {name:'idInterfaceOnt',                 mapping:'idInterfaceOnt', defaultValue: null},
                    {name:'idInterfaceOntSwPoe',            mapping:'idInterfaceOntSwPoe', defaultValue: null},
                    {name:'idSwPoeGpon',                    mapping:'idSwPoeGpon', defaultValue: null},
                    {name:'nombreSwPoeGpon',                mapping:'nombreSwPoeGpon', defaultValue: ""},
                    {name:'serieSwPoeGpon',                 mapping:'serieSwPoeGpon', defaultValue: ""},
                    {name:'macSwPoeGpon',                   mapping:'macSwPoeGpon', defaultValue: ""},
                    {name:'marcaSwPoeGpon',                 mapping:'marcaSwPoeGpon', defaultValue: ""},
                    {name:'modeloSwPoeGpon',                mapping:'modeloSwPoeGpon', defaultValue: ""},
                    {name:'idServicioEleReqGpon',           mapping:'idServicioEleReqGpon', defaultValue: null},
                    {name:'idElementoReqGpon',              mapping:'idElementoReqGpon', defaultValue: null},
                    {name:'nombreElementoReqGpon',          mapping:'nombreElementoReqGpon', defaultValue: ""},
                    {name:'idInterfaceOntEleReq',           mapping:'idInterfaceOntEleReq', defaultValue: null},
                    {name:'serieElementoReqGpon',           mapping:'serieElementoReqGpon', defaultValue: ""},
                    {name:'macElementoReqGpon',             mapping:'macElementoReqGpon', defaultValue: ""},
                    {name:'marcaElementoReqGpon',           mapping:'marcaElementoReqGpon', defaultValue: ""},
                    {name:'modeloElementoReqGpon',          mapping:'modeloElementoReqGpon', defaultValue: ""},
                    {name:'nombreElementoCliente',          mapping:'nombreElementoCliente', defaultValue: ""},
                    {name:'modeloElementoCliente',          mapping:'modeloElementoCliente', defaultValue: ""},
                    {name:'serieElementoCliente',           mapping:'serieElementoCliente', defaultValue: ""},
                    {name:'macElementoCliente',             mapping:'macElementoCliente', defaultValue: ""},
                    {name:'servicioEnSwPoe',                mapping:'servicioEnSwPoe', defaultValue: "N"},
                    {name:'cooperativa',                    mapping:'cooperativa', defaultValue: ""},
                    {name:'tipoTransporte',                 mapping:'tipoTransporte', defaultValue: ""},
                    {name:'placa',                          mapping:'placa', defaultValue: ""},
                    {name:'servicioInternetMDInCorte',      mapping:'servicioInternetMDInCorte', defaultValue: "N"},
                    {name:'tipoElemento',                   mapping:'tipoElemento'},
                    {name:'strTipoElementoABuscar',         mapping:'strTipoElementoABuscar'},
                    {name:'boolTieneFlujo',                 mapping:'boolTieneFlujo', defaultValue: null},
                    {name:'boolValidaNaf',                  mapping:'boolValidaNaf', defaultValue: null},
                    {name:'arrayCaractAdicionales',         mapping:'arrayCaractAdicionales', defaultValue: null},
                    {name:'arrayCaractAdicionalesServicios',mapping:'arrayCaractAdicionalesServicios', defaultValue: null},
                    {name:'servicioPadreSimultaneo',        mapping:'servicioPadreSimultaneo', defaultValue: null},
                    {name:'strPropietarioCpeCliente',       mapping:'strPropietarioCpeCliente'},
                    {name:'strPropietarioRadioCliente',     mapping:'strPropietarioRadioCliente'},
                    {name:'strSerieCpeCliente',             mapping: 'strSerieCpeCliente'},
                    {name:'strSerieTransceiverCliente',     mapping: 'strSerieTransceiverCliente'},
                    {name:'strSerieRadioCliente',           mapping: 'strSerieRadioCliente'},
                    {name:'strSerieOntCliente',             mapping: 'strSerieOntCliente'},
                    {name:'strSerieWifiCliente',            mapping: 'strSerieWifiCliente'},
                    {name:'strJsonDipositivosNodo',         mapping: 'strJsonDipositivosNodo'},
                    {name:'strJsonTecnico',                 mapping: 'strJsonTecnico'},
                    {name:'tipoMedioId',                    mapping: 'tipoMedioId'},
                    {name:'boolValidaDptoActivar',          mapping: 'boolValidaDptoActivar'},
                    {name:'boolPermiteVisualizarBoton',     mapping: 'boolPermiteVisualizarBoton'},
                    {name:'boolVisualizarBotonCorte',       mapping: 'boolVisualizarBotonCorte'},
                    {name:'boolVisualizarBotonCancelar',    mapping: 'boolVisualizarBotonCancelar'},
                    {name:'boolVisualizarBotonReactivacion',mapping: 'boolVisualizarBotonReactivacion'},
                    {name:'boolVisualizarPantallaFibra'    ,mapping: 'boolVisualizarPantallaFibra'},
                    {name:'strTipoPlan',                    mapping: 'strTipoPlan'},
                    {name:'strEsIpWan',                     mapping: 'strEsIpWan'},
                    {name:'boolSecureCpe',                  mapping: 'boolSecureCpe'},
                    {name:'intNumLicencia',                 mapping: 'intNumLicencia'},
                    {name:'strFechaCaducidad',              mapping: 'strFechaCaducidad'},
                    {name:'intNumDiasFin',                  mapping: 'intNumDiasFin'},
                    {name:'boolVisualizaBotonNg',           mapping: 'boolVisualizaBotonNg'},
                    {name:'boolVisualizarDatosTecnicos',    mapping: 'boolVisualizarDatosTecnicos', defaultValue: "S"},
                    {name:'boolVisualizarBotonCambioVelocidad',           mapping: 'boolVisualizarBotonCambioVelocidad'},
                    {name:'activoKonibit',                  mapping: 'activoKonibit'},
                    {name:'intIdServicioInternet',          mapping: 'intIdServicioInternet'},
                    {name:'strServicioInternetInAudit',     mapping: 'strServicioInternetInAudit'}, 
                    {name:'arrayPersonalizacionOpcionesGridTecnico', mapping: 'arrayPersonalizacionOpcionesGridTecnico'},
                    {name:'intServicioFTTxTN',              mapping: 'intServicioFTTxTN'},
                    {name:'boolServicioInternetActivo',     mapping: 'boolServicioInternetActivo'},
                    {name:'strCorreoECDF',                  mapping: 'strCorreoECDF'},
                    {name:'boolResumenCompra', mapping: 'boolResumenCompra'},
                    {name:'strTienePassword',               mapping: 'strTienePassword'},
                    {name:'strClearChannelPuntoAPuntoTransporte', mapping: 'strClearChannelPuntoAPuntoTransporte'},
                    {name:'boolIsClearChannel', mapping: 'boolIsClearChannel'},
                    {name:'strTipoModeloBackUp', mapping: 'strTipoModeloBackUp'},
                    {name:'aprovisioClearChannel', mapping: 'aprovisioClearChannel'},
                    {name:'strUuIdPaquete',                 mapping: 'strUuIdPaquete'},
                    {name:'strValorProductoPaqHoras',       mapping: 'strValorProductoPaqHoras'},
                    {name:'strValorProductoPaqHorasRec',    mapping: 'strValorProductoPaqHorasRec'},
                    {name:'boolEsReplica',                  mapping: 'boolEsReplica'},
                    {name:'boolCambioPlanCP', mapping: 'boolCambioPlanCP'}
                  ]
    });
    /*Debe mantenerse como una variable global, sino dará error en pantallas externas.*/
     storeInterfacesBusq = new Ext.data.Store({
        total: 'total',
//            autoLoad:true,
        proxy: {
            type: 'ajax',
            url: getInterfacesElemento,
            extraParams: {
                idElemento: ''
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
            ]
    });

    const storeUltimaMilla = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: getUltimaMilla,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idTipoMedio', mapping: 'idTipoMedio'},
                {name: 'nombreTipoMedio', mapping: 'nombreTipoMedio'}
            ]
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    gridServicios = Ext.create('Ext.grid.Panel', {
        width: '98%',
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
        viewConfig: {
            enableTextSelection: true
        },
        iconCls: 'icon-grid',
        columns: [
            {
                id: 'idServicio',
                header: 'idServicio',
                dataIndex: 'idServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'idServicioRefIpFija',
                header: 'idServicioRefIpFija',
                dataIndex: 'idServicioRefIpFija',
                hidden: true,
                hideable: false
            },
            {
                id: 'tieneIpFijaActiva',
                header: 'tieneIpFijaActiva',
                dataIndex: 'tieneIpFijaActiva',
                hidden: true,
                hideable: false
            },
            {
                id: 'macIpFija',
                header: 'macIpFija',
                dataIndex: 'macIpFija',
                hidden: true,
                hideable: false
            },
            {
                id: 'idIntCouSim',
                header: 'idIntCouSim',
                dataIndex: 'idIntCouSim',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Completo',
                dataIndex: 'nombreCompleto',
                width: 210,
                sortable: true
            },
            {
                header: 'UM',
                dataIndex: 'codUltimaMilla',
                width: 50,
                sortable: true
            },

            {
                header: 'Producto',
                dataIndex: 'nombreProducto',
                width: 130,
                sortable: true,
                renderer: function(val,meta,rec) {
                    var id = Ext.id();
                    Ext.defer(function() {
                        Ext.widget('button', {
                            renderTo: Ext.query("#"+id)[0],
                            text: '',
                            iconCls: 'button-grid-show',
                            iconAlign: 'left',
                            tooltip: 'Ver Características Producto',
                            scale: 'medium',
                            autoWidth : true,
                            autoHeight : true,
                            padding: 0,
                            margin: 0,
                            border: 0,
                            width: '32px',
                            handler: function() {
                                verCaracteristicasProducto(rec.get('idServicio'));
                            }
                        });
                    }, 50);
                    return Ext.String.format('<label id="{0}" style="border-style:none !important;"></label>', id) + 
                           "&nbsp<label style='font-weight: bold;color:green;'>"+val+"</label>";
                }
            },
            {
                header: 'Cant.',
                dataIndex: 'cantidadReal',
                width: 35,
                sortable: true
            },
            {
                dataIndex: 'tipoOrdenCompleto',
                header: 'T. Orden',
                width: 70,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 110,
                sortable: true,
                // Agregamos Badge In-Audit si el servicio se encuentra en estado In-Corte e InAudit
                renderer: function(value,meta,rec) {
                    var badgeText = 'In-Audit';
                    if(value == 'In-Corte' && rec.get('strServicioInternetInAudit') == 'S') {
                        return "<label>"+value+"</label>" + 
                            "&nbsp<span style='background-color:#f6a700;font-weight:bold;border: #f6a700 1px groove;border-radius: .5em;'>"+ 
                            "&nbsp"+badgeText +"&nbsp</span>";
                    }
                    return value;
                }
            },
            {
                header: 'iSolicitudLineaPom',//jv
                dataIndex: 'idSolicitudLineaPom',
                hidden: true,
                width: 90,
                hideable: false
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                minWidth: 600,
                items: botones
            },
            {
                header: 'strFlagActivSim',
                dataIndex: 'strFlagActivSim',
                hidden: true,
                width: 90,
                hideable: false
            },
            {
                header: 'tipoMedioId',
                dataIndex: 'tipoMedioId',
                hidden: true,
                width: 30,
                hideable: false
            },
        ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: store,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
        renderTo: 'grid',
        listeners:
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
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
        }
    });

    //Se ordena ascendentemente por idServicio.
    gridServicios.getStore().sort('idServicio', 'ASC');
    store.load();

    if(prefijoEmpresa == "TN")
    {
        gridServicios.headerCt.insert(
                                    6,
                                    {
                                        text: 'T. Enlace',
                                        width: 75,
                                        dataIndex: 'tipoEnlace',
                                        sortable: true,
                                        renderer: function (val)
                                        {
                                            if(val === 'BACKUP')
                                            {
                                                return `<label style="color:green">  ${val} </label>
                                                        <label style="font-weight: bold; font-size: 17.5px;">&#10548;</label>`;

                                            }
                                            else
                                            {
                                                if (val === null)
                                                {
                                                    return '<b>' + "" + '</b>';
                                                }
                                                else
                                                {
                                                    return '<b>' + val + '</b>';
                                                }
                                            }
                                        }
                                    }
                                );
        gridServicios.headerCt.insert(
                                    7,
                                    {
                                        text: 'Grupo/Solución',
                                        width: 90,
                                        dataIndex: 'nombreSolucion',
                                        sortable: true
                                    }
                                );
        gridServicios.headerCt.insert(
                                    9,
                                    {
                                        text: 'Tipo Red',
                                        width: 80,
                                        dataIndex: 'strTipoRed',
                                        sortable: true
                                    }
                                );
        gridServicios.headerCt.insert(
                                    10,
                                    {
                                        text: 'Login Aux',
                                        width: 200,
                                        dataIndex: 'loginAux',
                                        sortable: true
                                    }
                                );
        gridServicios.headerCt.insert(
                                    11,
                                    {
                                        header: 'Desc. Fact.',
                                        dataIndex: 'descripcionPresentaFactura',
                                        width: 200,
                                        sortable: true
                                    });
        gridServicios.headerCt.insert(
                                    12,
                                    {
                                        header: 'Tipo Esquema',
                                        dataIndex: 'tipoEsquema',
                                        width: 95,
                                        sortable: true,
                                        align: 'center'
                                    });
    }
    else if(prefijoEmpresa == "MD" || prefijoEmpresa == "EN")
    {
        gridServicios.headerCt.insert(
                                    5,
                                    {
                                        header: 'Login',
                                        dataIndex: 'login',
                                        width: 120,
                                        sortable: true
                                    });
        gridServicios.headerCt.insert(
                                    7,
                                    {
                                        text: 'Plan',
                                        dataIndex: 'nombrePlan',
                                        width: 150,
                                        sortable: true
                                    });
        gridServicios.headerCt.insert(
                                    8,
                                    {
                                        header: 'Modelo Elemento',
                                        dataIndex: 'modeloElemento',
                                        width: 120,
                                        sortable: true
                                    }
                                    );

    }
    else if(prefijoEmpresa == "TNP")
    {
        gridServicios.headerCt.insert(
                                    6,
                                    {
                                        text: 'T. Enlace',
                                        width: 75,
                                        dataIndex: 'tipoEnlace',
                                        sortable: true,
                                        renderer: function (val)
                                        {
                                            if(val === 'BACKUP')
                                            {
                                                return '<label style="color:green">' + val +
                                                       '</label>\n\ <label style="font-weight: bold; font-size: 17.5px;">&#10548;</label>';
                                            }
                                            else
                                            {
                                                return '<b>' + val + '</b>';
                                            }
                                        }
                                    }
                                );
        gridServicios.headerCt.insert(
                    7,
                    {
                        text: 'Plan',
                        dataIndex: 'nombrePlan',
                        width: 150,
                        sortable: true
                    });
        gridServicios.headerCt.insert(
                                    9,
                                    {
                                        text: 'Login Aux',
                                        width: 120,
                                        dataIndex: 'loginAux',
                                        sortable: true
                                    }
                                );
        gridServicios.headerCt.insert(
                                    10,
                                    {
                                        header: 'Desc. Fact.',
                                        dataIndex: 'descripcionPresentaFactura',
                                        width: 150,
                                        sortable: true
                                    });
    }

    gridServicios.getView().refresh();

    itemsLoging = Ext.create('Ext.panel.Panel', {
        border:false,
        layout: {
            type: 'table',
            columns: 2,
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },
        items:[
                {
                    xtype: 'textfield',
                    id: 'txtLogin',
                    fieldLabel: 'Login',
                    value: ''
                },
                {
                    xtype: 'combobox',
                    id: 'sltLoginForma',
                    value: 'Igual que',
                    store: [
                        ['Igual que','Igual que'],
                        ['Empieza con','Empieza con'] ,
                        ['Contiene','Contiene'],
                        ['Termina con','Termina con']
                    ]
                }
            ]
            });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 3,
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },

        collapsible : true,
        collapsed: true,
        width: '98%',
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    id: 'buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscar();}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar();}
                }

                ],
                items: [
                        itemsLoging,
                        {
                            xtype: 'combobox',
                            id: 'sltProducto',
                            fieldLabel: 'Producto',
                            store: storeProductos,
                            displayField: 'descripcionProducto',
                            valueField: 'idProducto',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'remote',
                            width: '30%'
                        },
                        { width: '10%',border:false},//siguiente linea
                        //-------------------------------------
                        {
                            xtype: 'textfield',
                            id: 'txtLoginAux',
                            fieldLabel: 'Login Auxiliar',
                            value: '',
                            listeners: {
                                focus: function(el) 
                                {                                 
                                    validaPtoEnSesion(el)
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'sltTipoServicio',
                            fieldLabel: 'Tipo Servicio',
                            store: storeTipoServicios,
                            displayField: 'descripcionTipo',
                            valueField: 'idTipo',
                            loadingText: 'Buscando...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'remote',
                            width: '20%'
                        },
                        { width: '10%',border:false}, //siguiente linea
                        //-------------------------------------
                        {
                            xtype: 'combobox',
                            id: 'sltPlanes',
                            fieldLabel: 'Plan',
                            store: storePlanes,
                            displayField: 'nombrePlan',
                            valueField: 'idPlan',
                            loadingText: 'Buscando ...',
                            queryMode: 'remote',
                            listClass: 'x-combo-list-small',
                            width: '30%'
                        },
                        {
                            xtype: 'combobox',
                            id: 'sltUltimaMilla',
                            fieldLabel: 'Ultima Milla',
                            store: storeUltimaMilla,
                            displayField: 'nombreTipoMedio',
                            valueField: 'nombreTipoMedio',
                            loadingText: 'Buscando ...',
                            queryMode: 'remote',
                            listClass: 'x-combo-list-small',
                            width: '20%'
                        },
                        { width: '10%',border:false}, //siguiente linea
                        //-------------------------------------
                        {
                            xtype: 'combobox',
                            id: 'sltEstado',
                            fieldLabel: 'Estado',
                            store: storeEstados,
                            displayField: 'descripcionEstado',
                            valueField: 'idEstado',
                            loadingText: 'Buscando...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'remote',
                            width: '30%'
                        },
                        {
//                            xtype: 'textfield',
//                            id: 'txtElemento',
//                            fieldLabel: 'Elemento',
//                            value: '',
//                            readOnly: true,
                            border:false,
                            html: `<table>
                                            <tr>
                                                <td>
                                                    <label class="x-form-item-label x-form-item-label-left" cellpadding="0" 
                                                    style="width:100px;margin-right:5px;" for="txtElemento">Elemento:</label>
                                                </td>
                                                <td>
                                                    <input type="text" id="txtElemento" class="x-form-field x-form-text" 
                                                        style=" -moz-user-select: text;" readonly>
                                                    <a  href="#" onclick="buscarElementoPanel()">
                                                         <img src="/public/images/search.png" />
                                                    </a>
                                                </td>
                                            </tr>
                                   </table>`,
                        },
                        {border: false, width: '20%'},
                        {
                            xtype: 'checkbox',
                            id: 'checkboxServTelco',
                            name: 'checkboxServTelco',
                            fieldLabel: 'Login Telconet',
                            hidden: boolNoVisualizacionServTelco,
                            itemCls: 'x-check-group-alt'
                        },
                        {
                            xtype: 'combobox',
                            id:'comboInterfacesFiltro',
                            name: 'comboInterfacesFiltro',
                            store: storeInterfacesBusq,
                            fieldLabel: 'Interface',
                            displayField: 'nombreInterfaceElemento',
                            valueField: 'idInterfaceElemento',
                            queryMode: 'local',
                            width: '30%'
                        },
                        {
                            xtype: 'hidden',
                            id: 'txtIdElemento',
                            value: '',
                            readOnly: true
                        },
                    ],
        renderTo: 'filtro'
    });

});

var connFact = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});

function validaPtoEnSesion(el) 
{
    let objToolBar = Ext.get('sfToolbarMainContent-5533bb');
    if (!objToolBar) 
    {
        el.setDisabled(true);
        Ext.create('Ext.tip.ToolTip', {
            target: el.id,
            html: "Para poder realizar una busqueda por <b class='red-text'>Login Auxiliar</b> debe haber un punto en sesión."
        });
    }
}

function buscarElementoPanel(){
    Ext.getCmp('comboInterfacesFiltro').value="";
    Ext.getCmp('comboInterfacesFiltro').setRawValue("");

    storeElementos = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getElementosPorEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idElemento', mapping:'idElemento'},
                {name:'nombreElemento', mapping:'nombreElemento'},
                {name:'modeloElemento', mapping:'modeloElemento'},
                {name:'ip', mapping:'ip'},
                {name:'estado', mapping:'estado'}
              ]
    });

    var storeTipoElemento = new Ext.data.Store({
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : getTiposElementosBackbone,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreTipoElemento', mapping:'nombreTipoElemento'},
                {name:'idTipoElemento', mapping:'idTipoElemento'}
              ]
    });

    gridElementosBusq = Ext.create('Ext.grid.Panel', {
        width: 530,
        height: 294,
        store: storeElementos,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
        columns:[
                {
                  id: 'idElemento',
                  header: 'idElemento',
                  dataIndex: 'idElemento',
                  hidden: true,
                  hideable: false
                },
                {
                  header: 'Nombre Elemento',
                  dataIndex: 'nombreElemento',
                  width: 160,
                  sortable: true
                },
                {
                  header: 'Modelo',
                  dataIndex: 'modeloElemento',
                  width: 80,
                  sortable: true
                },
                {
                  header: 'Ip',
                  dataIndex: 'ip',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 90,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Accion',
                    width: 50,
                    items: [
                        {
                            getClass: function(v, meta, rec) {
//                              
                                if(rec.get('modeloElemento') != "GENERICO"){
                                    return 'button-grid-seleccionar';
                                }
                                else{
                                    return 'button-grid-invisible';
                                }


                            },
                            tooltip: 'Seleccionar',
                            handler: function(grid, rowIndex, colIndex) {
                                Ext.getCmp('txtIdElemento').setValue(grid.getStore().getAt(rowIndex).data.idElemento);
                                document.getElementById('txtElemento').value = grid.getStore().getAt(rowIndex).data.nombreElemento;
                                storeInterfacesBusq.getProxy().extraParams.idElemento = grid.getStore().getAt(rowIndex).data.idElemento;
                                storeInterfacesBusq.load();
                                win.destroy();
                            }
                        }
                    ]
                }
            ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeElementos,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });

    filterPanelElementosBusq = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },

        collapsible : true,
        collapsed: false,
        width: 530,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscarElemento();}
                }

            ],  //cierre buttons
            items: [

                { width: '10%',border:false}, //inicio
                {
                    xtype: 'textfield',
                    id: 'txtNombre',
                    fieldLabel: 'Nombre',
                    value: '',
                    emptyText: 'Minimo 3 primeros caracteres',
                    width: '30%'
                },
                { width: '20%',border:false}, //medio
                {
                    xtype: 'textfield',
                    id: 'txtIp',
                    fieldLabel: 'Ip',
                    value: '',
                    width: '30%'
                },
                { width: '10%',border:false}, //final

                //-------------------------------------

                { width: '10%',border:false}, //inicio
                {
                    xtype: 'combobox',
                    id: 'sltTipoElemento',
                    fieldLabel: 'Tipo',
                    store: storeTipoElemento,
                    displayField: 'nombreTipoElemento',
                    valueField: 'idTipoElemento',
                    loadingText: 'Buscando ...',
                    listClass: 'x-combo-list-small',
                    queryMode: 'local',
                    width: '30%'
                },
                { width: '20%',border:false}, //medio
                { width: '20%',border:false},
                { width: '10%',border:false}, //final

                //-------------------------------------


            ]//cierre items
    });


    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [

        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',
//                checkboxToggle: true,
//                collapsed: true,
            defaults: {
                width: 530
            },
            items: [
                filterPanelElementosBusq,
                gridElementosBusq
            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Elementos',
        modal: true,
        width: 580,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function buscarElemento(){

    if(Ext.getCmp('txtNombre').getRawValue() == "" ||
       Ext.getCmp('txtNombre').getRawValue() != "" && Ext.getCmp('txtNombre').getRawValue().length < 3)
    {
        Ext.Msg.alert('Error ',"Debe ingresar minimo los 3 primeros caracteres del elemento.");
    }
    else if(Ext.getCmp('sltTipoElemento').getRawValue()=="")
    {
        Ext.Msg.alert('Error ',"Debe escoger un Tipo de Elemento");
    }

    else
    {
        storeElementos.getProxy().extraParams.nombreElemento    = Ext.getCmp('txtNombre').value;
        storeElementos.getProxy().extraParams.ip                = Ext.getCmp('txtIp').value;
        storeElementos.getProxy().extraParams.tipoElemento      = Ext.getCmp('sltTipoElemento').getRawValue();
        storeElementos.getProxy().extraParams.strTipoEjecucion  = 'ModuloTecnicoClientes';
        storeElementos.load();
    }
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function existeRecordIpPublica(myRecord, grid){
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var canton=grid.getStore().getAt(i).get('ip');

    if((canton == myRecord.get('ip') ) || canton == myRecord.get('ip'))
    {
      existe=true;
      break;
    }
  }
  return existe;
}

/*
 * Funcion que sirve para enviar al store los datos que se
 * llenaron en los parametros de busqueda.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.1 10-12-2019 - Se agrega funcionalidad para buscar por login auxiliar.
 *
 * @author Jeampier Carriel <jacarriel@telconet.ec>
 * @version 1.2 15-12-2021 - Se agrega filtro para ver servicios de TN en MD
 * 
 */
function buscar(){
    //validacion para que no se envie a buscar todos los datos de la base
    if(Ext.getCmp('txtLogin').value == "" && Ext.getCmp('sltProducto').value==null && Ext.getCmp('sltPlanes').value==null &&
       Ext.getCmp('sltTipoServicio').value == "" && Ext.getCmp('sltEstado').value=="Todos" && Ext.getCmp('sltUltimaMilla').value==null &&
       Ext.getCmp('txtIdElemento').value=="" && Ext.getCmp('comboInterfacesFiltro').value==null &&
       Ext.getCmp('txtLoginAux').value == "")
    {
        Ext.Msg.alert('Error ',"Debe escoger al menos un parámetro de búsqueda, Favor revisar los campos!!!");
    }
    else if(Ext.getCmp('txtLogin').value == "" && Ext.getCmp('sltProducto').value=="" && Ext.getCmp('sltPlanes').value=="" &&
       Ext.getCmp('sltTipoServicio').value == "" && Ext.getCmp('sltEstado').value=="Todos" && Ext.getCmp('sltUltimaMilla').value=="" &&
       Ext.getCmp('txtIdElemento').value=="" && Ext.getCmp('comboInterfacesFiltro').value=="" && Ext.getCmp('txtLoginAux').value == "")
    {
        Ext.Msg.alert('Error ',"Debe escoger al menos un parámetro de búsqueda, Favor revisar los campos!!!");
    }
    else
    {
        var checkServTelco    = Ext.getCmp('checkboxServTelco').value;
        var intServicioFTTxTN = null;
        if(checkServTelco != null && checkServTelco == true)
        {
            intServicioFTTxTN = 'S';
        }

        store.getProxy().extraParams = {};
        store.getProxy().extraParams.login          = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.strLoginAux    = Ext.getCmp('txtLoginAux').value;
        store.getProxy().extraParams.loginForma     = Ext.getCmp('sltLoginForma').value;
        store.getProxy().extraParams.producto       = Ext.getCmp('sltProducto').value;
        store.getProxy().extraParams.plan           = Ext.getCmp('sltPlanes').value;
        store.getProxy().extraParams.tipoServicio   = Ext.getCmp('sltTipoServicio').value;
        store.getProxy().extraParams.estado         = Ext.getCmp('sltEstado').value;
        store.getProxy().extraParams.ultimaMilla    = Ext.getCmp('sltUltimaMilla').value;
        store.getProxy().extraParams.idElemento     = Ext.getCmp('txtIdElemento').value;
        store.getProxy().extraParams.idInterface    = Ext.getCmp('comboInterfacesFiltro').value;
        store.getProxy().extraParams.intServicioFTTxTN = intServicioFTTxTN;
        store.getProxy().timeout                    = 300000;

        if (Ext.getCmp('txtLoginAux').value)
        {
            store.pageSize = 100;
        }

        store.load();

    }
}

function limpiar(){
    Ext.getCmp('txtLogin').value="";
    Ext.getCmp('txtLogin').setRawValue("");

    Ext.getCmp('txtLoginAux').value="";
    Ext.getCmp('txtLoginAux').setRawValue("");

    Ext.getCmp('sltLoginForma').value="Igual que";
    Ext.getCmp('sltLoginForma').setRawValue("Igual que");

    Ext.getCmp('txtIdElemento').value="";
    Ext.getCmp('txtIdElemento').setRawValue("");

    document.getElementById('txtElemento').value = "";

    Ext.getCmp('sltTipoServicio').value="";
    Ext.getCmp('sltTipoServicio').setRawValue("");

    Ext.getCmp('sltProducto').value="";
    Ext.getCmp('sltProducto').setRawValue("");

    Ext.getCmp('sltPlanes').value="";
    Ext.getCmp('sltPlanes').setRawValue("");

    Ext.getCmp('comboInterfacesFiltro').value="";
    Ext.getCmp('comboInterfacesFiltro').setRawValue("");

    Ext.getCmp('sltUltimaMilla').value="";
    Ext.getCmp('sltUltimaMilla').setRawValue("");

    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");

    store.getProxy().extraParams.intServicioFTTxTN  = "";

    delete store.getProxy().extraParams;
    store.pageSize = 10;
    store.load();

//    store.load({params: {
//        login: Ext.getCmp('txtLogin').value,
//        producto: Ext.getCmp('sltProducto').value,
//        plan: Ext.getCmp('sltPlanes').value,
//        nombreCliente: Ext.getCmp('sltNombreCliente').value,
//        ordenTrabajo: Ext.getCmp('txtOrdenTrabajo').value,
//        estado: Ext.getCmp('sltEstado').value
//    }});
}

function cargarModelos(idParam){
    storeModeloElemento.proxy.extraParams = {idMarca: idParam, limite:100};
    storeModeloElemento.load({params: {}});
}

function cargarInterfaces(idParam){
    storeInterfacesElementoDslam.proxy.extraParams = {idElemento: idParam, limite:100};
    storeInterfacesElementoDslam.load({params: {}});
}

function cargarGrid(idParam){
    storeInterfacesCpe.proxy.extraParams = {idModelo: idParam, limite:100};
    storeInterfacesCpe.load({params: {}});
}

function eliminarAlgunos(){
    var param = '';
    if(sm.getSelection().length > 0)
    {
      var estado = 0;
      for(var i=0 ;  i < sm.getSelection().length ; ++i)
      {
        param = param + sm.getSelection()[i].data.idElemento;

        if(sm.getSelection()[i].data.estado == 'Eliminado')
        {
          estado = estado + 1;
        }
        if(i < (sm.getSelection().length -1))
        {
          param = param + '|';
        }
//        alert(param);
      }
      if(estado == 0)
      {
        Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: "dslam/deleteAjaxDslam",
                    method: 'post',
                    params: { param : param},
                    success: function(response){
                        store.load();
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });

      }
      else
      {
        Ext.Msg.alert('Error ','Por lo menos uno de las registro se encuentra en estado ELIMINADO');
      }
    }
    else
    {
      Ext.Msg.alert('Error ','Seleccione por lo menos un registro de la lista');
    }
}

function obtenerDatosCaracteristicas(){
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridIpPublica.getStore().getCount();
  array_relaciones['caracteristicas'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridIpPublica.getStore().getCount(); i++)
  {
        array_data.push(gridIpPublica.getStore().getAt(i).data);
  }
  array_relaciones['caracteristicas'] = array_data;
  Ext.getCmp('jsonCaracteristicas').setValue(Ext.JSON.encode(array_relaciones));
}

function eliminarSeleccion(datosSelect){
  for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
  {
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
  }
}

function verCaracteristicasProducto(intIdServicio)
{
    dataCaracteristicasProducto = new Ext.data.Store(
    {
        autoLoad: true,
        total: 'total',
        proxy:
        {
            type:   'ajax',
            url:    urlAjaxCaracteristicasProductoPorServicio,
            actionMethods: {
                read: 'POST'
            },
            paramsAsJson: true,
            extraParams:
            {
                intIdServicio: intIdServicio
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
            {name: 'caracteristica', mapping: 'caracteristica', type: 'string'},
            {name: 'valor', mapping: 'valor', type: 'string'}
        ]
    });
    gridCaracteristicasProducto = Ext.create('Ext.grid.Panel',
    {
        id: 'gridCaracteristicasProducto',
        store: dataCaracteristicasProducto,
        width: 400,
        height: 250,
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
                    win4.destroy();
                }
            }
        ],
        columns:
        [
            {
                dataIndex: 'caracteristica',
                header: 'Característica',
                width: 200
            },
            {
                dataIndex: 'valor',
                header: 'Valor',
                width: 200
            }
        ]
    });

    win4 = Ext.create('Ext.window.Window',
    {
        title: 'Características Producto',
        modal: true,
        width: 400,
        closable: true,
        layout: 'fit',
        items: [gridCaracteristicasProducto]
    }).show();
}    

/*
 * Documentación para el método 'SolicitarFactibilidad'.
 *
 * Método utilizado para invocar el routing que solicita la factibilidad del servicio.
 *
 * @param  id_servicio integer PK del servicio.
 * @param  idProducto  integer PK del producto.
 *
 * @return Action      Alert   Mensaje de confirmación de éxito o fracaso del método.
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 13-03-2019 Se agrega función para generar factibilidad en los productos que tienen la marca de activación simultánea.
 *
 */
function solicitarFactibilidad(data)
{
    if(data.descripcionPresentaFactura == 'CANAL TELEFONIA')
    {
        usarMismaUM(data.idServicio);
    }
    else
    {
        generarFactibilidad(data.idServicio,data.productoId,'SI');
    }
}

/*
 * Documentación para el método 'usarMismaUM'.
 *
 * Método utilizado para invocar el routing que solicita la factibilidad del servicio.
 *
 * @param  id_servicio integer PK del servicio.
 *
 * @return Action      Alert   Mensaje de confirmación de éxito o fracaso del método.
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 13-03-2019 Se agrega función para usar misma milla.
 *
 */
function usarMismaUM(id_servicio)
{
    var ventana = Ext.getCmp('windowServiciosUM');
    if (ventana != null) {
        ventana.close();
        ventana.destroy();
    }

    //Define un modelo para el store storeServiciosUM
    Ext.define('modelListaServiciosUM', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdServicio', type: 'int'},
            {name: 'strLoginAux', type: 'string'},
            {name: 'strDescProducto', type: 'string'},
            {name: 'strDescFactura', type: 'string'},
            {name: 'strUltimaMilla', type: 'string'},
            {name: 'strEstado', type: 'string'}
        ]
    });

    //Store que realiza la petición ajax para el grid: gridListaServiciosUM
    var storeServiciosUM = "";
    storeServiciosUM = Ext.create('Ext.data.Store', {
        pageSize: this.intPageSize,
        model: 'modelListaServiciosUM',
        autoLoad: true,
        proxy: {
            timeout: 60000,
            type: 'ajax',
            url: '../../comercial/punto/getServiciosUM',
            reader: {
                type: 'json',
                root: 'jsonServiciosUM',
                totalProperty: 'total'
            },
            extraParams: {
                intIdServicio: id_servicio
            },
            simpleSortMode: true
        }
    });

    var chkBoxModelServicios = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function (selectionModel, selected, options) {
                Ext.each(selected, function (rec) {});
                gridListaServiciosUM.down('#btnFactibilidadServicios').setDisabled(selected.length == 0);
            }
        }
    });

    var toolbarServicios = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'bottom',
        align: '->',
        items:
            [{xtype: 'tbfill'},
                {
                    xtype: 'button',
                    cls: 'scm-button',
                    id: "btnFactibilidadServicios",
                    iconCls: "icon_anadir",
                    text: 'Generar Factibilidad',
                    disabled: true,
                    scope: this,
                    handler: function () {

                        var intServicioIdSeleccionado;
                        //Valida que haya seleccionado servicios por punto, caso contrario muestra un mensaje de alerta
                        if (1 === chkBoxModelServicios.getSelection().length)
                        {
                            //Itera los chkBox y concatena los ID Servicios en un solo string strIdServicios
                            for (var intForIndex = 0; intForIndex < chkBoxModelServicios.getSelection().length; intForIndex++) {
                                intServicioIdSeleccionado = chkBoxModelServicios.getSelection()[intForIndex].data['intIdServicio'];
                            }

                            connFact.request({
                                url: '../../comercial/punto/generarFactibilidadUM',
                                method: 'post',
                                timeout: 400000,
                                params: {idServicioOrigen: id_servicio, idServicioUm: intServicioIdSeleccionado},
                                success: function (response) {
                                    var text = response.responseText;
                                    Ext.Msg.alert('Alerta', text);
                                    winServiciosUM.close();
                                    winServiciosUM.destroy();
                                    store.load();
                                },
                                failure: function (response) {
                                    var text = response.responseText;
                                    Ext.Msg.alert('Alerta', text);
                                    winServiciosUM.close();
                                    winServiciosUM.destroy();
                                    store.load();
                                }
                            });

                        } else {
                            Ext.Msg.alert('Alerta', 'Debe seleccionar sólo una orden de servicio.');
                        }
                    }
                }
            ]
    });

    //Crea el grid que muestra la información obtenida desde el controlador  del listado de servicios.
    var gridListaServiciosUM = Ext.create('Ext.grid.Panel', {
        store: storeServiciosUM,
        id: 'gridListaServiciosUM',
        selModel: chkBoxModelServicios,
        dockedItems: [toolbarServicios],
        viewConfig: {enableTextSelection: true},
        columns: [
            {
                id: 'intIdServicioH',
                header: 'IdServicio',
                dataIndex: 'intIdServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'strLoginAuxH',
                header: 'Login Aux',
                dataIndex: 'strLoginAux',
                width: 210
            },
            {
                id: 'strProductoH',
                header: 'Producto',
                dataIndex: 'strDescProducto',
                width: 210
            },
            {
                id: 'strDescripcionH',
                header: 'Descripcion',
                dataIndex: 'strDescFactura',
                width: 210
            },
            {
                id: 'strDescripcionUm',
                header: 'Ultima MIlla',
                dataIndex: 'strUltimaMilla',
                width: 210
            },
            {
                id: 'strEstadoH',
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 230
            }
        ],
        height: 400,
        width: 980,
        listeners: {

        },
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeServiciosUM,
            displayInfo: true
        })
    });

    var winServiciosUM = Ext.widget('window', {
        id: 'windowServiciosUM',
        title: 'Seleccionar Ultima Milla: ',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: true,
        items: [gridListaServiciosUM]
    });
    winServiciosUM.show();
}

/*
 * Documentación para el método 'generarFactibilidad'.
 *
 * Método utilizado para invocar el routing que solicita la factibilidad del servicio.
 *
 * @param  id_servicio integer PK del servicio.
 * @param  idProducto  integer PK del producto.
 * @param  continuaFlujo strin 'SI' o 'NO'.
 * 
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 13-03-2020 Se válida que no se duplique una solicitud de factibilidad, que se encuentre en estado: Factible o Prefactibilidad
 * 
*/

function generarFactibilidad(id_servicio,idProducto,continuaFlujo)
{
    if(continuaFlujo === 'SI')
    {
        connFact.request({
            url: '../../comercial/punto/solicitarFactibilidadGeneral',
            method: 'post',
            timeout: 400000,
            params: {id: id_servicio, idProducto: idProducto},
            success: function(response) {
                
                connFact.request({
                    url: '../../comercial/punto/solicitarFactibilidadTelefonia',
                    method: 'post',
                    timeout: 400000,
                    params: {idServicio: id_servicio},
                    success: function(response) {
                        var text = response.responseText;

                        Ext.Msg.alert('Mensaje', text, function(btn) {
                            if (btn == 'ok') {
                                store.load();
                            }
                        });
                    },
                    failure: function(result) {
                        Ext.Msg.alert('Alerta', result.responseText);
                            store.load();
                    }
                });   
            },
            failure: function(result) {
                Ext.Msg.alert('Alerta', result.responseText);
                store.load();
            }
        });
    }
    else
    {
        Ext.Msg.alert('Alerta', 'Favor ingrese el anexo Técnico y/o Comercial requerido para el servicio');
    }
}
