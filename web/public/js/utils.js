/**
 * Utils, clase que contiene metodos para aplicar validaciones y expresiones regulares.
 * 
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 05-05-2016
 * @since 1.0
 *
 * @author Edgar Holguin <eholguin@telconet.ec>
 * @version 1.1 20-12-2016 Se agrega expresión regular para validar formato de número de pago.
 * @since 1.1 
 * 
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.2 05-07-2017 Se agrega expresión regular para validar número telefónico de Panamá
 */
var Utils = new function() {

    this.TRUE                           = true;
    this.FALSE                          = false;
    this.MAX_CED_PANAMA                 = 12;
    this.MAX_RUC_PANAMA                 = 16;
    this.MAX_PAS_PANAMA                 = 20;
    this.MAX_CED_EC                     = 10;
    this.MAX_RUC_EC                     = 13;
    this.MAX_PAS_EC                     = 20;
    this.REGEX_ALFANUM                  = /^([A-Za-z]+[A-Za-z0-9]*)$/;
    this.REGEX_NUM_CEDULA               = /^[0-9]{9,9}$/;
    this.REGEX_NUM_RUC                  = /^[0-9]{3,3}$/;
    this.REGEX_NUM                      = /^[0-9]$/;
    this.REGEX_ALFA_LET_SPACE           = /^([A-Za-z]+[A-Za-z0-9\s]*)$/;
    this.REGEX_ALFANUM_LET_SPACE        = /^([A-Za-z0-9]+[A-Za-z0-9_\/\-\s]*)$/;
    this.REGEX_FONE_MIN8MAX10           = /^[0-9]{8,10}$/;
    this.REGEX_FONE_MIN7MAX15           = /^[0-9]{7,15}$/;
    this.REGEX_FONE_MIN7MAX8            = /^[0-9]{7,8}$/;
    this.REGEX_MAC                      = /[a-fA-f0-9]{4}[\.]+[a-fA-f0-9]{4}[\.]+[a-fA-F0-9]{4}$/;
    this.REGEX_IP                       = /^([01]?\d\d?|2[0-4]\d|25[0-5])\.([01]?\d\d?|2[0-4]\d|25[0-5])\.([01]?\d\d?|2[0-4]\d|25[0-5])\.([01]?\d\d?|2[0-4]\d|25[0-5])$/;
    this.STYLE_BOLD                     = 'font-weight:bold;';
    this.GREY_BOLD_COLOR                = this.STYLE_BOLD + 'color:#777777;';
    this.ALIGN_LEFT                     = 'left';
    this.REGEX_NUM_FACTURA              = /^[0-9]{3}-[0-9]{3}-[0-9]{9}/;
    this.REGEX_NUM_FACTFIS              = /^[0-9]{1,13}-[0-9]{8}/;
    this.REGEX_NUM_PAGO                 = /^[0-9]{3}-[0-9]{3}-[0-9]/;
    this.REGEX_DATE_MMDDYYYY            = /^(0?[1-9]|[12][0-9]|3[01])[\-](0?[1-9]|1[012])[\-]\d{4}$/;
    this.REGEX_PRECIO                   = /^[0-9]+([.][0-9]{2})?$/;
    //aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi
    this.REGEX_MAIL = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/;
 
    
    this.arrayTituloMensajeBox = [];
    this.arrayTituloMensajeBox['100'] = 'Información';
    this.arrayTituloMensajeBox['001'] = 'Error';
    this.arrayTituloMensajeBox['000'] = 'Alerta';

    this.validateAlfaNum = function(strVar) {
        if (this.REGEX_ALFANUM.test(strVar))
        {
            return  this.TRUE;
        }
        return this.FALSE;
    };

    this.validateAlfaLetSpace = function(strVar) {
        if (this.REGEX_ALFA_LET_SPACE.test(strVar))
        {
            return this.TRUE;
        }
        return this.FALSE;
    };

    this.validateMail = function(strVar) {
        if (this.REGEX_MAIL.test(strVar))
        {
            return this.TRUE;
        }
        return this.FALSE;
    };

    this.validateFoneMin8Max10 = function(strVar) {
        if (this.REGEX_FONE_MIN8MAX10.test(strVar))
        {
            return this.TRUE;
        }
        return this.FALSE;
    };
    
    this.validateFoneMin7Max8 = function(strVar) {
        if (this.REGEX_FONE_MIN7MAX8.test(strVar))
        {
            return this.TRUE;
        }
        return this.FALSE;
    };
    
    this.validateIp = function(strVar) {
        if (this.REGEX_IP.test(strVar))
        {
            return this.TRUE;
        }
        return this.FALSE;
    };

    this.existStringIn = function(strExists, strVar) {
        if (-1 != strVar.search(strExists)) {
            return this.TRUE;
        }
        return this.FALSE;
    };
    
    this.button = function() {
        return new Ext.create('Ext.button.Button', {});
    };
    
    this.objDate = function() {
        return new Ext.form.DateField({});
    };

    this.objText = function() {
        return new Ext.create('Ext.form.Text', {});
    };
    
    this.objTextArea = function() {
        return new Ext.create('Ext.form.TextArea', {});
    };
    
    this.objCombo = function() {
        return Ext.create('Ext.form.ComboBox', {});
    };
    
    this.objStore = function() {
        return Ext.create('Ext.data.Store', {});
    };
    
    this.windows = function() {
        return new Ext.create('Ext.window.Window', {});
    };

    this.form = function() {
        return new Ext.create('Ext.form.Panel', {});
    };

    this.panel = function(){
        return new Ext.create('Ext.panel.Panel', {});
    };

    this.toolbar = function(){
        return new Ext.create('Ext.toolbar.Toolbar', {});
    };
    
    this.objLabel = function() {
        return new Ext.create('Ext.form.Label', {});
    };
    
    this.objContainer = function() {
        return new Ext.create('Ext.container.Container',  {});
    };
    
    this.objButton = function() {
        return new Ext.create('Ext.form.Button',  {});
    };
    
    this.validateFormatDateDDMMYYYY = function(strVar){
        if (this.REGEX_DATE_MMDDYYYY.test(strVar))
        {
            return this.TRUE;
        }
        return this.FALSE;
    };
    
    /**
     * Función para calcular los días transcurridos entre dos fechas
     * @param fechaInicio Fecha inicial en formato dd-MM-YYYY
     * @param fechaFin    Fecha fin en formato dd-MM-YYYY
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 16-09-2016 
     */ 
    this.restaFechas = function(fechaInicio,fechaFin)
    {
        if(Ext.isEmpty(fechaInicio) || Ext.isEmpty(fechaInicio))
        {
          return 0;
        }
        if(!this.validateFormatDateDDMMYYYY(fechaInicio) || !this.validateFormatDateDDMMYYYY(fechaFin))
        {
          return 0;
        }
        var aFecha1 = fechaInicio.split('-'); 
        var aFecha2 = fechaFin.split('-'); 
        var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1],aFecha1[0]); 
        var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1],aFecha2[0]);        
        var dif = fFecha2 - fFecha1;
        var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
        return dias;
    };

    this.getUltimoDigitoCedula = function(objTxtCedula) {
        var arrayResult = [];
        arrayResult['intUltimoDigitoCedula'] = 0;
        arrayResult['strStatus'] = '000';
        arrayResult['strMensaje'] = 'Debe tener 9 digitos.';
        var intLength = objTxtCedula.getValue().length;
        if (intLength >= 1) {
            var intNumber = objTxtCedula.getValue()[0] + objTxtCedula.getValue()[1];
            if (new Number(intNumber) < 1 || new Number(intNumber) > 24) {
                arrayResult['strMensaje'] = 'Los dos primeros digistos no pueden ser menor a 01 o mayor a 24.';
                arrayResult['strStatus'] = '001';
                return arrayResult;
            }
        }
        if (intLength === 9) {
            var intCountValueCedula = 0;
            var intSumDigitCedula = 0;
            [2, 1, 2, 1, 2, 1, 2, 1, 2].map(function(item) {
                var intCoefVsDigito = 0;
                intCoefVsDigito = new Number(item) * new Number(objTxtCedula.getValue()[intCountValueCedula]);
                intSumDigitCedula = intSumDigitCedula + ((intCoefVsDigito >= 10) ? intCoefVsDigito - 9 : intCoefVsDigito);
                intCountValueCedula++;
            });
            if (intCountValueCedula > 0) {
                arrayResult['intUltimoDigitoCedula'] = (10 - (intSumDigitCedula % 10));
                if(10 === arrayResult['intUltimoDigitoCedula']){
                    arrayResult['intUltimoDigitoCedula'] = 0;
                }
            }
            arrayResult['strStatus'] = '100';
            arrayResult['strMensaje'] = 'Se genero ultimo digito.';
        }
        return arrayResult;
    };
    
    
    /**
    * 
    * Funcion que permite validar las forma de contacto.
    * @param gridformasContacto Es un grid con las formas de contacto que se desean validar.
    * @return boolean retorna false si hay problemas en alguna de las formas de contacto, 
    *         caso contrario devuelve true.
    * @author Héctor Ortega <haortega@telconet.ec>
    * @version 1.00, 29/11/2016
    */    
    this.validaFormasContacto = function(gridformasContacto)
    {
        var array_telefonos = new Array();
        var array_correos = new Array();
        var i = 0;
        var variable = '';
        var formaContacto = '';
        var hayTelefono = false;
        var hayCorreo = false;
        var telefonosOk = false;
        var correosOk = false;
        var hayTelefonoInternacional = false;
        var arrayTelefonosInternacionales = new Array();
        var existenCorreosFallidos = false;
        var existenFonosFallidos   = false;
    
        for (var i = 0; i < gridformasContacto.getStore().getCount(); i++)
        {
            variable = gridformasContacto.getStore().getAt(i).data;
            if (variable.formaContacto.toUpperCase().match(/^TELEFONO.*$/))
            {
                if (variable.formaContacto.toUpperCase().match(/^TELEFONO INTERNACIONAL$/))
                {
                    hayTelefonoInternacional = true;
                    arrayTelefonosInternacionales.push(variable.valor);
                }
                else
                {
                    hayTelefono = true;
                    array_telefonos.push(variable.valor);
                }
            }
            if (variable.formaContacto.toUpperCase().match(/^CORREO.*$/))
            {
                hayCorreo = true;
                array_correos.push(variable.valor);
            }
        }
        
        var valorErroneo = '';
        if (hayCorreo)
        {
            //Verificar si existen correos con errores
            for (i = 0; i < array_correos.length; i++)
            {
                correosOk = validaCorreo(array_correos[i]);
                //Si existe al menos un correo erroneo no deja continuar
                if (!correosOk)
                {
                    valorErroneo = array_correos[i];
                    existenCorreosFallidos = true;
                    break;
                }
            }

            if (existenCorreosFallidos)
            {
                if (valorErroneo !== '')
                {
                    Ext.Msg.alert("Error", "El correo <b>" + valorErroneo + "</b> contiene errores o está mal formado, por favor corregir.");
                }
                else
                {
                    Ext.Msg.alert("Error", "Ingresar el valor del correo a agregar");
                }
                return false;
            }

        }
        else
        {
            var presentaErrorCorreo = true;
            if (Ext.isDefined(window.strPrefijoEmpresa))
            {
                if (strPrefijoEmpresa == 'MD')
                {
                    presentaErrorCorreo = false;
                }
            }
            if (presentaErrorCorreo)
            {
                Ext.Msg.show({
                    title: 'Error',
                    msg: 'Debe Ingresar al menos 1 Correo.',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
                return false;
            }
        }

        //Verificar si existen telefonos con errores
        for (i = 0; i < array_telefonos.length; i++)
        {
            telefonosOk = validaTelefono(array_telefonos[i]);

            if (!telefonosOk)
            {
                valorErroneo = array_telefonos[i];
                existenFonosFallidos = true;
                break;
            }
        }

        if (existenFonosFallidos)
        {
            if (valorErroneo !== '')
            {
                Ext.Msg.alert("Error", "El Teléfono <b>" + valorErroneo + "</b> está mal formado, por favor corregir.");
            }
            else
            {
                Ext.Msg.alert("Error", "Ingresar el valor del Teléfono a agregar");
            }
            return false;
        }
        if (!verificaTelefonosInternacionales(hayTelefonoInternacional, arrayTelefonosInternacionales))
        {
            return false;
        }
        return true;

    };
    
    /**
    * 
    * Expresion regular validar telefono entre 8 y 10 digitos.
    * @param telefono Cadena con el numero de teléfono a validar.
    * @return boolean retorna true si es un telefono valido, 
    *         caso contrario devuelve false.
    * @author Héctor Ortega <haortega@telconet.ec>
    * @version 1.00, 29/11/2016
    * 
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.01, 03-07-2017
    * Se añade la validación para números de teléfono de Panamá
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.02, 08-02-2018
    * Se cambia expresión regular vara validar formato de número de teléfono para Panamá.
    */
    function validaTelefono(telefono)
    {
        var RegExPattern = /^[0-9]{8,10}$/;
        if(strNombrePais === 'PANAMA')
        {
            RegExPattern = /^(\+?\d{1,3}?[- .]?\d{1,3}[- .]?\d{1,4})$/;
        }
        if ((telefono.match(RegExPattern)) && (telefono.value != ''))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Documentación para maxCaracteresIdentificacion
     *
     * Permite obtener el número caracteres para la identificación correspondiente.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 27-09-2017 Version inicial
     * @param {String} strTipoIdentificacion ('CED' || 'RUC' || 'PAS')
     **/
    this.maxCaracteresIdentificacion = function (strTipoIdentificacion)
    {
        if ('CED' === strTipoIdentificacion.toUpperCase())
        {
            //VALIDO POR PANAMÁ
            if (intIdPais === "121")
            {
                return this.MAX_CED_PANAMA;
            }
            else
            {
                return this.MAX_CED_EC;
            }
        }
        if ('RUC' === strTipoIdentificacion.toUpperCase())
        {
            if (intIdPais === "121")
            {
                return this.MAX_RUC_PANAMA;
            }
            else
            {
                return this.MAX_RUC_EC;
            }
        }
        if ('PAS' === strTipoIdentificacion.toUpperCase())
        {
            if (intIdPais === "121")
            {
                return this.MAX_PAS_PANAMA;
            }
            else
            {
                return this.MAX_PAS_EC;
            }
        }
    }

    /**
    * 
    * Expresion regular validar un correo electrónico.
    * @param correo Cadena con el correo a validar.
    * @return boolean retorna true si es un correo valido, 
    *         caso contrario devuelve false.
    * @author Héctor Ortega <haortega@telconet.ec>
    * @version 1.00, 29/11/2016
    */
    function validaCorreo(correo)
    {
        var RegExPattern = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
        if ((correo.match(RegExPattern)) && (correo.value != ''))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
    * 
    * Esta función permite validar un arreglo de teléfonos internacionales.
    * @param hayTelefonoInternacional bandera que indica si existen teléfonos en el arreglo.
    * @param arrayTelefonosInternacionales arreglos de teléfonos internacionales.
    * @return boolean retorna true si el arreglo tiene teléfonos internacionales validoss, 
    *         caso contrario devuelve false.
    * @author Héctor Ortega <haortega@telconet.ec>
    * @version 1.00, 29/11/2016
    */
    function verificaTelefonosInternacionales(hayTelefonoInternacional, arrayTelefonosInternacionales)
    {
        if (hayTelefonoInternacional)
        {
            var telefonosOk = false;
            var valorErroneo = '';
            var i = 0;
            for (i = 0; i < arrayTelefonosInternacionales.length; i++)
            {
                telefonosOk = validaTelefonoInternacional(arrayTelefonosInternacionales[i]);
                if (!telefonosOk)
                {
                    valorErroneo = arrayTelefonosInternacionales[i];
                    break;
                }
            }

            if (telefonosOk)
            {
                return true;
            }
            else
            {

                if (valorErroneo !== '')
                {
                    Ext.Msg.alert("Error", "El Teléfono Internacional <b>" + valorErroneo +
                        "</b> está mal formado, por favor corregir.");
                }
                else
                {
                    Ext.Msg.alert("Error", "Ingresar el valor del Teléfono Internacional a agregar");
                }
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    /**
    * 
    * Esta función permite validar un teléfonos internacional.
    * @param telefonoInternacional cadena con el número de telefono internacional.
    * @return boolean retorna true si es un número de telefono valido, 
    *         caso contrario devuelve false.
    * @author Héctor Ortega <haortega@telconet.ec>
    * @version 1.00, 29/11/2016
    */
    function validaTelefonoInternacional(telefonoInternacional)
    {
        var RegExPattern = /^[0-9]{7,15}$/;
        if ((telefonoInternacional.match(RegExPattern)) && (telefonoInternacional.value != ''))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
    * 
    * Esta función permite validar si una cadena tiene entre 7 y 15 digitos.
    * @param strVar cadena con el texto a validar.
    * @return boolean retorna true si es la cadena tiene entre 7 y 15 digitos, 
    *         caso contrario devuelve false.
    * @author Héctor Ortega <haortega@telconet.ec>
    * @version 1.00, 29/11/2016
    */
    this.validateFoneMin7Max15 = function(strVar)
    {
        if (this.REGEX_FONE_MIN7MAX15.test(strVar))
        {
            return this.TRUE;
        }
        return this.FALSE;
    };
    
    this.arrayMes               = [];
    this.arrayMes['Enero']      = '01';
    this.arrayMes['Febrero']    = '02';
    this.arrayMes['Marzo']      = '03';
    this.arrayMes['Abril']      = '04';
    this.arrayMes['Mayo']       = '05';
    this.arrayMes['Junio']      = '06';
    this.arrayMes['Julio']      = '07';
    this.arrayMes['Agosto']     = '08';
    this.arrayMes['Septiembre'] = '09';
    this.arrayMes['Octubre']    = '10';
    this.arrayMes['Noviembre']  = '11';
    this.arrayMes['Diciembre']  = '12';
    
    this.arrayNombreMesesIngles               = [];
    this.arrayNombreMesesIngles['Enero']      = 'Jan';
    this.arrayNombreMesesIngles['Febrero']    = 'Feb';
    this.arrayNombreMesesIngles['Marzo']      = 'Mar';
    this.arrayNombreMesesIngles['Abril']      = 'Apr';
    this.arrayNombreMesesIngles['Mayo']       = 'May';
    this.arrayNombreMesesIngles['Junio']      = 'Jun';
    this.arrayNombreMesesIngles['Julio']      = 'Jul';
    this.arrayNombreMesesIngles['Agosto']     = 'Aug';
    this.arrayNombreMesesIngles['Septiembre'] = 'Sep';
    this.arrayNombreMesesIngles['Octubre']    = 'Oct';
    this.arrayNombreMesesIngles['Noviembre']  = 'Nov';
    this.arrayNombreMesesIngles['Diciembre']  = 'Dec';
    
    this.arrayNumeroMesesEnIngles       = [];
    this.arrayNumeroMesesEnIngles['01'] = 'Jan';
    this.arrayNumeroMesesEnIngles['02'] = 'Feb';
    this.arrayNumeroMesesEnIngles['03'] = 'Mar';
    this.arrayNumeroMesesEnIngles['04'] = 'Apr';
    this.arrayNumeroMesesEnIngles['05'] = 'May';
    this.arrayNumeroMesesEnIngles['06'] = 'Jun';
    this.arrayNumeroMesesEnIngles['07'] = 'Jul';
    this.arrayNumeroMesesEnIngles['08'] = 'Aug';
    this.arrayNumeroMesesEnIngles['09'] = 'Sep';
    this.arrayNumeroMesesEnIngles['10'] = 'Oct';
    this.arrayNumeroMesesEnIngles['11'] = 'Nov';
    this.arrayNumeroMesesEnIngles['12'] = 'Dec';
};

