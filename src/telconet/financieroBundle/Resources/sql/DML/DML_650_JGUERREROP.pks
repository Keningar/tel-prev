DECLARE

XSD_FORMAT CLOB:=TO_CLOB('<?xml version="1.0" encoding="UTF-8"?>
<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:ds="http://www.w3.org/2000/09/xmldsig#" elementFormDefault="qualified">
<xsd:import namespace="http://www.w3.org/2000/09/xmldsig-core-schema.xsd"/>
<xsd:simpleType name="ambiente">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[1-2]{1}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="razonSocial">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="dirMatriz">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="nombreComercial">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:complexType name="infoTributaria">
<xsd:sequence>
<xsd:element name="ambiente" type="ambiente"/>
<xsd:element name="tipoEmision" type="tipoEmision"/>
<xsd:element name="razonSocial" type="razonSocial"/>
<xsd:element name="nombreComercial" minOccurs="0" type="nombreComercial"/>
<xsd:element name="ruc" type="numeroRuc"/>
<xsd:element name="claveAcceso" type="claveAcceso"/>
<xsd:element name="codDoc" type="codDoc"/>
<xsd:element name="estab" type="establecimiento"/>
<xsd:element name="ptoEmi" type="puntoEmision"/>
<xsd:element name="secuencial" type="secuencial"/>
<xsd:element name="dirMatriz" type="dirMatriz"/>
</xsd:sequence>
</xsd:complexType>
<xsd:simpleType name="numeroRuc">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{10}001"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="idCliente">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="13"/>
<xsd:pattern value="[0-9a-zA-Z]{0,13}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="numeroRucCedula">
<xsd:restriction base="xsd:string">
<xsd:minLength value="10"/>
<xsd:maxLength value="13"/>
<xsd:pattern value="[0-9]{10}"/>
<xsd:pattern value="[0-9]{10}001"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="fechaType">
<xsd:restriction base="xsd:string">
<xsd:pattern value="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/20[0-9][0-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="fechaAutorizacion">
<xsd:restriction base="xsd:string">
<xsd:pattern value="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/20[0-9][0-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="establecimiento">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="puntoEmision">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="secuencial">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{9}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="documento">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}-[0-9]{3}-[0-9]{1,9}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codTipoDoc">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]+"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codTipoDocModificado">
<xsd:restriction base="xsd:integer">
<xsd:minInclusive value="1"/>
<xsd:maxInclusive value="8"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="claveAcceso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{49}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="autorizacion">
<xsd:restriction base="xsd:string">
<xsd:maxLength value="10"/>
<xsd:minLength value="3"/>
<xsd:pattern value="[0-9]{3,10}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="fechaEmision">
')||TO_CLOB('<xsd:restriction base="xsd:string">
<xsd:pattern
value="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/20[0-9][0-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="dirEstablecimiento">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="tipoEmision">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[12]{1}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="contribuyenteEspecial">
<xsd:restriction base="xsd:string">
<xsd:minLength value="3"/>
<xsd:maxLength value="13"/>
<xsd:pattern value="([A-Za-z0-9])*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="obligadoContabilidad">
<xsd:restriction base="xsd:string">
<xsd:enumeration value="SI"/>
<xsd:enumeration value="NO"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="tipoIdentificacionComprador">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0][4-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="guiaRemision">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}-[0-9]{3}-[0-9]{9}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="razonSocialComprador">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="identificacionComprador">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="20"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="totalSinImpuestos">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="totalSubsidio">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigo">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[235]"/>
<xsd:minLength value="1"/>
<xsd:maxLength value="1"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codDoc">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{2}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="totalDescuentos">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="descuentoAdicional">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
</xsd:restriction>
</xsd:simpleType> 
<xsd:simpleType name="codigoPorcentaje">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]+"/>
<xsd:minLength value="1"/>
<xsd:maxLength value="4"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="baseImponible">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="tarifa">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="4"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="valor">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="valorDevolucionIva">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="propina">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
')||TO_CLOB('<xsd:simpleType name="importeTotal">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="moneda">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="15"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigoPrincipal">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="25"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigoAuxiliar">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="25"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="descripcion">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="cantidad">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="precioUnitario">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="precioSinSubsidio">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="descuento">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="precioTotalSinImpuesto">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="nombre">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="campoAdicional">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="comercioExterior">
<xsd:restriction base="xsd:string">
<xsd:pattern value="EXPORTADOR"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="incoTermFactura">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="10"/>
<xsd:pattern value="([A-Z])*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="lugarIncoTerm">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="paisOrigen">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="paisDestino">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="puertoEmbarque">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="puertoDestino">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="paisAdquisicion">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="direccionComprador">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
')||TO_CLOB('<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="incoTermTotalSinImpuestos">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="10"/>
<xsd:pattern value="([A-Z])*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="fleteInternacional">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="seguroInternacional">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="gastosAduaneros">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="gastosTransporteOtros">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="formaPago">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0][1-9]"/>
<xsd:pattern value="[1][0-9]"/>
<xsd:pattern value="[2][0-1]"/>
</xsd:restriction>
</xsd:simpleType> 
<xsd:simpleType name="total">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="plazo">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType> 
<xsd:simpleType name="unidadTiempo">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="10"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="unidadMedida">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="50"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:complexType name="pagos"> 
<xsd:sequence>
<xsd:element name="pago" minOccurs="1" maxOccurs="unbounded">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="formaPago" minOccurs="1" type="formaPago"/>
<xsd:element name="total" minOccurs="1" type="total"/>
<xsd:element name="plazo" minOccurs="0" type="plazo"/>
<xsd:element name="unidadTiempo" minOccurs="0" type="unidadTiempo"/>
</xsd:sequence>
</xsd:complexType>
</xsd:element> 
</xsd:sequence>
</xsd:complexType>
<xsd:complexType name="impuesto">
<xsd:sequence>
<xsd:element name="codigo" type="codigo"/>
<xsd:element name="codigoPorcentaje" type="codigoPorcentaje"/>
<xsd:element name="tarifa" type="tarifa" minOccurs="1"/>
<xsd:element name="baseImponible" type="baseImponible" minOccurs="1"/>
<xsd:element name="valor" type="valor"/>
</xsd:sequence>
</xsd:complexType>
<xsd:simpleType name="codigoDocumentoReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{2,3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="totalComprobantesReembolso">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="totalBaseImponibleReembolso">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="totalImpuestoReembolso"> 
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="valorRetIva">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
')||TO_CLOB('</xsd:restriction>
</xsd:simpleType> 
<xsd:simpleType name="valorRetRenta">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="tipoIdentificacionProveedorReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0][4-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="identificacionProveedorReembolso">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="20"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codPaisPagoProveedorReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="tipoProveedorReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0][12]"/>
</xsd:restriction>
</xsd:simpleType> 
<xsd:simpleType name="codDocReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{2,3}"/>
</xsd:restriction>
</xsd:simpleType> 
<xsd:simpleType name="estabDocReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="ptoEmiDocReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="secuencialDocReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{9}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="fechaEmisionDocReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern
value="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/20[0-9][0-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="numeroautorizacionDocReemb">
<xsd:restriction base="xsd:string">
<xsd:maxLength value="49"/>
<xsd:minLength value="10"/>
<xsd:pattern value="[0-9]{10,49}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigoReembolso"> 
<xsd:restriction base="xsd:string">
<xsd:pattern value="[235]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigoPorcentajeReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{1,4}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="tarifaReembolso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{1,4}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="baseImponibleReembolso">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="impuestoReembolso">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:complexType name="detalleImpuestos"> 
<xsd:sequence>
<xsd:element name="detalleImpuesto" minOccurs="1" maxOccurs="unbounded">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="codigo" minOccurs="1" type="codigoReembolso"/>
<xsd:element name="codigoPorcentaje" minOccurs="1" type="codigoPorcentajeReembolso"/>
<xsd:element name="tarifa" minOccurs="1" type="tarifaReembolso"/>
<xsd:element name="baseImponibleReembolso" minOccurs="1" type="baseImponibleReembolso"/>
<xsd:element name="impuestoReembolso" minOccurs="1" type="impuestoReembolso"/> 
</xsd:sequence>
</xsd:complexType>
</xsd:element> 
</xsd:sequence>
</xsd:complexType>
<xsd:simpleType name="codigoPorcentajeCompensacion">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[1]"/>
<xsd:minLength value="1"/>
<xsd:maxLength value="1"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:complexType name="compensacion">
<xsd:sequence>
<xsd:element name="codigo" minOccurs="1" type="codigoPorcentajeCompensacion"/>
<xsd:element name="tarifa" minOccurs="1" type="tarifa"/>
<xsd:element name="valor" minOccurs="1" type="valor"/>
')||TO_CLOB('</xsd:sequence>
</xsd:complexType>

<xsd:complexType name="compensaciones"> 
<xsd:sequence>
<xsd:element name="compensacion" minOccurs="1" maxOccurs="unbounded" type="compensacion"/>
</xsd:sequence>
</xsd:complexType>

<xsd:complexType name="compensacionesReembolso">
<xsd:sequence>
<xsd:element name="compensacionReembolso" minOccurs="1" maxOccurs="unbounded" type="compensacion"/>
</xsd:sequence>
</xsd:complexType>

<xsd:complexType name="reembolsos">
<xsd:sequence>
<xsd:element name="reembolsoDetalle" minOccurs="1" maxOccurs="unbounded">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="tipoIdentificacionProveedorReembolso" minOccurs="1" type="tipoIdentificacionProveedorReembolso"/>
<xsd:element name="identificacionProveedorReembolso" minOccurs="1" type="identificacionProveedorReembolso"/>
<xsd:element name="codPaisPagoProveedorReembolso" minOccurs="0" type="codPaisPagoProveedorReembolso"/>
<xsd:element name="tipoProveedorReembolso" minOccurs="1" type="tipoProveedorReembolso"/> 
<xsd:element name="codDocReembolso" minOccurs="1" type="codDocReembolso"/>
<xsd:element name="estabDocReembolso" minOccurs="1" type="estabDocReembolso"/>
<xsd:element name="ptoEmiDocReembolso" minOccurs="1" type="ptoEmiDocReembolso"/>
<xsd:element name="secuencialDocReembolso" minOccurs="1" type="secuencialDocReembolso"/>
<xsd:element name="fechaEmisionDocReembolso" minOccurs="1" type="fechaEmisionDocReembolso"/> 
<xsd:element name="numeroautorizacionDocReemb" minOccurs="1" type="numeroautorizacionDocReemb"/>
<xsd:element name="detalleImpuestos" minOccurs="1" type="detalleImpuestos"/>
<xsd:element name="compensacionesReembolso" minOccurs="0" maxOccurs="1" type="compensacionesReembolso"/>
</xsd:sequence>
</xsd:complexType>
</xsd:element> 
</xsd:sequence>
</xsd:complexType>
<xsd:element name="factura">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="infoTributaria" type="infoTributaria"/>
<xsd:element name="infoFactura">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="fechaEmision" type="fechaEmision"/>
<xsd:element name="dirEstablecimiento" type="dirEstablecimiento" minOccurs="0"/>
<xsd:element name="contribuyenteEspecial" type="contribuyenteEspecial" minOccurs="0"/>
<xsd:element name="obligadoContabilidad" minOccurs="0" type="obligadoContabilidad"/>
<xsd:element name="comercioExterior" minOccurs="0" type="comercioExterior"/>
<xsd:element name="incoTermFactura" minOccurs="0" type="incoTermFactura"/>
<xsd:element name="lugarIncoTerm" minOccurs="0" type="lugarIncoTerm"/>
<xsd:element name="paisOrigen" minOccurs="0" type="paisOrigen"/>
<xsd:element name="puertoEmbarque" minOccurs="0" type="puertoEmbarque"/>
<xsd:element name="puertoDestino" minOccurs="0" type="puertoDestino"/>
<xsd:element name="paisDestino" minOccurs="0" type="paisDestino"/>
<xsd:element name="paisAdquisicion" minOccurs="0" type="paisAdquisicion"/>
<xsd:element name="tipoIdentificacionComprador" type="tipoIdentificacionComprador"/>
<xsd:element name="guiaRemision" minOccurs="0" type="guiaRemision"/>
<xsd:element name="razonSocialComprador" type="razonSocialComprador"/>
<xsd:element name="identificacionComprador" type="identificacionComprador"/>
<xsd:element name="direccionComprador" minOccurs="0" type="direccionComprador"/>
<xsd:element name="totalSinImpuestos" type="totalSinImpuestos"/>
<xsd:element name="totalSubsidio" minOccurs="0" type="totalSubsidio"/>
<xsd:element name="incoTermTotalSinImpuestos" minOccurs="0" type="incoTermTotalSinImpuestos"/>
<xsd:element name="totalDescuento" type="totalDescuentos"/>
<xsd:element name="codDocReembolso" minOccurs="0" type="codigoDocumentoReembolso"/> 
<xsd:element name="totalComprobantesReembolso" minOccurs="0" type="totalComprobantesReembolso"/> 
<xsd:element name="totalBaseImponibleReembolso" minOccurs="0" type="totalBaseImponibleReembolso"/> 
<xsd:element name="totalImpuestoReembolso" minOccurs="0" type="totalImpuestoReembolso"/> 
<xsd:element name="totalConImpuestos">
')||TO_CLOB('<xsd:complexType>
<xsd:sequence>
<xsd:element name="totalImpuesto" maxOccurs="unbounded">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="codigo" type="codigo"/>
<xsd:element name="codigoPorcentaje" type="codigoPorcentaje"/>
<xsd:element name="descuentoAdicional" minOccurs="0" type="descuentoAdicional"/>
<xsd:element name="baseImponible" type="baseImponible"/> 
<xsd:element name="tarifa" type="tarifa" minOccurs="0"/>
<xsd:element name="valor" minOccurs="1" type="valor"/>
<xsd:element name="valorDevolucionIva" type="valorDevolucionIva" minOccurs="0"/>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
<xsd:element name="compensaciones" minOccurs="0" maxOccurs="1" type="compensaciones"/>
<xsd:element name="propina" type="propina" minOccurs="0"/>
<xsd:element name="fleteInternacional" minOccurs="0" type="fleteInternacional"/>
<xsd:element name="seguroInternacional" minOccurs="0" type="seguroInternacional"/>
<xsd:element name="gastosAduaneros" minOccurs="0" type="gastosAduaneros"/>
<xsd:element name="gastosTransporteOtros" minOccurs="0" type="gastosTransporteOtros"/>
<xsd:element name="importeTotal" type="importeTotal"/>
<xsd:element name="moneda" minOccurs="0" type="moneda"/>
<xsd:element name="pagos" minOccurs="0" type="pagos"/>
<xsd:element name="valorRetIva" minOccurs="0" type="valorRetIva"/>
<xsd:element name="valorRetRenta" minOccurs="0" type="valorRetRenta"/>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
<xsd:element name="detalles">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="detalle" minOccurs="1" maxOccurs="unbounded">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="codigoPrincipal" minOccurs="0" type="codigoPrincipal"/>
<xsd:element name="codigoAuxiliar" minOccurs="0" type="codigoAuxiliar"/>
<xsd:element name="descripcion" minOccurs="0" type="descripcion"/>
<xsd:element name="unidadMedida" minOccurs="0" type="unidadMedida"/>
<xsd:element name="cantidad" type="cantidad"/>
<xsd:element name="precioUnitario" type="precioUnitario"/>
<xsd:element name="precioSinSubsidio" minOccurs="0" type="precioSinSubsidio"/>
<xsd:element name="descuento" minOccurs="1" type="descuento"/>
<xsd:element name="precioTotalSinImpuesto" type="precioTotalSinImpuesto"/>
<xsd:element name="detallesAdicionales" minOccurs="0">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="detAdicional" maxOccurs="3">
<xsd:complexType>
<xsd:attribute name="nombre" use="required">
<xsd:simpleType>
<xsd:restriction base="xsd:string">
<xsd:pattern value="[^n]*"/>
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
</xsd:restriction>
</xsd:simpleType>
</xsd:attribute>
<xsd:attribute name="valor" use="required">
<xsd:simpleType>
<xsd:restriction base="xsd:string">
<xsd:pattern value="[^n]*"/>
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
</xsd:restriction>
</xsd:simpleType>
</xsd:attribute>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
<xsd:element name="impuestos">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="impuesto" type="impuesto" maxOccurs="unbounded"/>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
<xsd:element name="reembolsos" minOccurs="0" type="reembolsos"/>
<xsd:element name="infoAdicional" minOccurs="0">
<xsd:complexType>
<xsd:sequence>
<xsd:element maxOccurs="15" name="campoAdicional">
<xsd:complexType>
<xsd:simpleContent>
<xsd:extension base="campoAdicional">
<xsd:attribute name="nombre" type="nombre" use="required"/>
</xsd:extension>
</xsd:simpleContent>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
<xsd:attribute name="id">
<xsd:simpleType>
<xsd:restriction base="xsd:string">
<xsd:enumeration value="comprobante"/>
</xsd:restriction>
</xsd:simpleType>
</xsd:attribute>
')||TO_CLOB('<xsd:attribute name="version"/>
</xsd:complexType>
</xsd:element>
</xsd:schema>');

BEGIN   

    DBMS_XMLSCHEMA.REGISTERSCHEMA
(
   SCHEMAURL => 'factura.xsd',
   SCHEMADOC => XSD_FORMAT,
   LOCAL     => TRUE,
   GENTYPES  => FALSE,
   GENTABLES => TRUE,
   OWNER     => 'DB_FINANCIERO'
 );

END; 
/

DECLARE

XSD_FORMAT CLOB:=TO_CLOB('<?xml version="1.0" encoding="UTF-8"?>
<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:ds="http://www.w3.org/2000/09/xmldsig#" elementFormDefault="qualified">
<xsd:import namespace="http://www.w3.org/2000/09/xmldsig-core-schema.xsd"/>
<xsd:simpleType name="numeroRuc">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{10}001"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="idCliente">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="13"/>
<xsd:pattern value="[0-9a-zA-Z]{0,13}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="numeroRucCedula">
<xsd:restriction base="xsd:string">
<xsd:minLength value="10"/>
<xsd:maxLength value="13"/>
<xsd:pattern value="[0-9]{10}"/>
<xsd:pattern value="[0-9]{10}001"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="fechaType">
<xsd:restriction base="xsd:string">
<xsd:pattern value="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/20[0-9][0-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="fechaAutorizacion">
<xsd:restriction base="xsd:string">
<xsd:pattern value="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/20[0-9][0-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="establecimiento">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="puntoEmision">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="secuencial">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{9}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="documento">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}-[0-9]{3}-[0-9]{1,9}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codTipoDoc">
<xsd:restriction base="xsd:integer">
<xsd:minInclusive value="4"/>
<xsd:maxInclusive value="4"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codTipoDocModificado">
<xsd:restriction base="xsd:integer">
<xsd:minInclusive value="1"/>
<xsd:maxInclusive value="8"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="claveAcceso">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{49}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="autorizacion">
<xsd:restriction base="xsd:string">
<xsd:maxLength value="10"/>
<xsd:minLength value="3"/>
<xsd:pattern value="[0-9]{3,10}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="ambiente">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[1-2]{1}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="tipoEmision">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[12]{1}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="razonSocial">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="nombreComercial">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="rise">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="40"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codDocModificado">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{2}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigoPorcentajeCompensacion">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[1-8]{1}"/>
<xsd:minLength value="1"/>
<xsd:maxLength value="1"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="numDocModificado">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{3}-[0-9]{3}-[0-9]{9}"/>
</xsd:restriction>
</xsd:simpleType>
')||TO_CLOB('<xsd:simpleType name="fechaEmisionDocSustento">
<xsd:restriction base="xsd:string">
<xsd:pattern
value="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/20[0-9][0-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="valorModificacion">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigoInterno">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="25"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigoAdicional">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="25"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="motivo">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:complexType name="detalle">
<xsd:sequence>
<xsd:element name="motivoModificacion" type="xsd:string"/>
</xsd:sequence>
</xsd:complexType>
<xsd:complexType name="impuesto">
<xsd:sequence>
<xsd:element name="codigo" type="codigo"/>
<xsd:element name="codigoPorcentaje" type="codigoPorcentaje"/>
<xsd:element name="tarifa" minOccurs="0" type="tarifa"/>
<xsd:element name="baseImponible" minOccurs="1" type="baseImponible"/>
<xsd:element name="valor" type="valor"/>
</xsd:sequence>
</xsd:complexType>
<xsd:complexType name="compensacion">
<xsd:sequence>
<xsd:element name="codigo" minOccurs="1" type="codigoPorcentajeCompensacion"/> 
<xsd:element name="tarifa" minOccurs="1" type="tarifa"/> 
<xsd:element name="valor" minOccurs="1" type="valor"/>
</xsd:sequence>
</xsd:complexType>
<xsd:complexType name="infoTributaria">
<xsd:sequence>
<xsd:element name="ambiente" type="ambiente"/>
<xsd:element name="tipoEmision" type="tipoEmision"/>
<xsd:element name="razonSocial" type="razonSocial"/>
<xsd:element name="nombreComercial" minOccurs="0" type="nombreComercial"/>
<xsd:element name="ruc" type="numeroRuc"/>
<xsd:element name="claveAcceso" type="claveAcceso"/>
<xsd:element name="codDoc" type="codDoc"/>
<xsd:element name="estab" type="establecimiento"/>
<xsd:element name="ptoEmi" type="puntoEmision"/>
<xsd:element name="secuencial" type="secuencial"/>
<xsd:element name="dirMatriz" type="dirMatriz"/>
</xsd:sequence>
</xsd:complexType>
<xsd:element name="notaCredito">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="infoTributaria" type="infoTributaria"/>
<xsd:element name="infoNotaCredito">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="fechaEmision" type="fechaEmision"/>
<xsd:element name="dirEstablecimiento" minOccurs="0" type="dirEstablecimiento"/>
<xsd:element name="tipoIdentificacionComprador" type="tipoIdentificacionComprador"/>
<xsd:element name="razonSocialComprador" type="razonSocialComprador"/>
<xsd:element name="identificacionComprador" minOccurs="1" type="identificacionComprador"/>
<xsd:element name="contribuyenteEspecial" minOccurs="0" type="contribuyenteEspecial"/>
<xsd:element name="obligadoContabilidad" minOccurs="0" type="obligadoContabilidad"/>
<xsd:element name="rise" minOccurs="0" type="rise"/>
<xsd:element name="codDocModificado" type="codDocModificado"/>
<xsd:element name="numDocModificado" type="numDocModificado"/>
<xsd:element name="fechaEmisionDocSustento" type="fechaEmisionDocSustento" minOccurs="1"/>
<xsd:element name="totalSinImpuestos" type="totalSinImpuestos"/>
<xsd:element ref="compensaciones" minOccurs="0" maxOccurs="1"/>
<xsd:element name="valorModificacion" minOccurs="1" type="valorModificacion"/> 
<xsd:element name="moneda" minOccurs="0" type="moneda"/>
<xsd:element ref="totalConImpuestos"/> 
<xsd:element name="motivo" type="motivo"/>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
<xsd:element name="detalles">
<xsd:complexType>
<xsd:sequence>
')||TO_CLOB('<xsd:element name="detalle" maxOccurs="unbounded" minOccurs="1">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="codigoInterno" minOccurs="0" type="codigoInterno"/>
<xsd:element name="codigoAdicional" minOccurs="0" type="codigoAdicional"/>
<xsd:element name="descripcion" type="descripcion"/>
<xsd:element name="cantidad" type="cantidad"/>
<xsd:element name="precioUnitario" type="precioUnitario"/>
<xsd:element name="descuento" minOccurs="0" type="descuento"/>
<xsd:element name="precioTotalSinImpuesto" type="precioTotalSinImpuesto"/>
<xsd:element name="detallesAdicionales" minOccurs="0">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="detAdicional" maxOccurs="3">
<xsd:complexType>
<xsd:attribute name="nombre" use="required">
<xsd:simpleType>
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
</xsd:restriction>
</xsd:simpleType>
</xsd:attribute>
<xsd:attribute name="valor" use="required">
<xsd:simpleType>
<xsd:restriction base="xsd:string">
<xsd:pattern value="[^n]*"/>
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
</xsd:restriction>
</xsd:simpleType>
</xsd:attribute>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
<xsd:element name="impuestos">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="impuesto" type="impuesto" minOccurs="0" maxOccurs="unbounded"/>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
<xsd:element name="infoAdicional" minOccurs="0">
<xsd:complexType>
<xsd:sequence maxOccurs="1">
<xsd:element name="campoAdicional" maxOccurs="15">
<xsd:complexType>
<xsd:simpleContent>
<xsd:extension base="campoAdicional">
<xsd:attribute name="nombre" type="nombre" use="required"/>
</xsd:extension>
</xsd:simpleContent>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
<xsd:attribute name="id" use="required">
<xsd:simpleType>
<xsd:restriction base="xsd:string">
<xsd:enumeration value="comprobante"/>
</xsd:restriction>
</xsd:simpleType>
</xsd:attribute>
<xsd:attribute name="version" type="xsd:NMTOKEN" use="required"/>
</xsd:complexType>
</xsd:element>
<xsd:element name="totalConImpuestos">
<xsd:complexType>
<xsd:sequence>
<xsd:element maxOccurs="unbounded" name="totalImpuesto">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="codigo" type="codigo"/>
<xsd:element name="codigoPorcentaje" type="codigoPorcentaje"/>
<xsd:element name="baseImponible" minOccurs="1" type="baseImponible"/>
<xsd:element name="valor" minOccurs="1" type="valor"/>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
<xsd:element name="compensaciones">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="compensacion" type="compensacion" minOccurs="1" maxOccurs="unbounded"/>
</xsd:sequence>
</xsd:complexType>
</xsd:element>
<xsd:simpleType name="codDoc">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]{2}"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="dirMatriz">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="fechaEmision">
<xsd:restriction base="xsd:string">
<xsd:pattern
value="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/20[0-9][0-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="dirEstablecimiento">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="contribuyenteEspecial">
<xsd:restriction base="xsd:string">
<xsd:minLength value="3"/>
<xsd:maxLength value="13"/>
<xsd:pattern value="([A-Za-z0-9])*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="obligadoContabilidad">
')||TO_CLOB('<xsd:restriction base="xsd:string">
<xsd:enumeration value="SI"/>
<xsd:enumeration value="NO"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="tipoIdentificacionComprador">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="13"/>
<xsd:pattern value="[0][4-9]"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="razonSocialComprador">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="identificacionComprador">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="20"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigo">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[235]"/>
<xsd:minLength value="1"/>
<xsd:maxLength value="1"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="totalSinImpuestos">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="baseImponible">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="moneda">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="15"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="codigoPorcentaje">
<xsd:restriction base="xsd:string">
<xsd:pattern value="[0-9]+"/>
<xsd:minLength value="1"/>
<xsd:maxLength value="4"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="valor">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="descripcion">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="cantidad">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="18"/>
<xsd:fractionDigits value="6"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="precioUnitario">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="18"/>
<xsd:fractionDigits value="6"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="descuento">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="precioTotalSinImpuesto">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="14"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="tarifa">
<xsd:restriction base="xsd:decimal">
<xsd:totalDigits value="4"/>
<xsd:fractionDigits value="2"/>
<xsd:minInclusive value="0"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="nombre">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
<xsd:simpleType name="campoAdicional">
<xsd:restriction base="xsd:string">
<xsd:minLength value="1"/>
<xsd:maxLength value="300"/>
<xsd:pattern value="[^n]*"/>
</xsd:restriction>
</xsd:simpleType>
</xsd:schema>');

BEGIN   

    DBMS_XMLSCHEMA.REGISTERSCHEMA
(
   SCHEMAURL => 'notaCredito.xsd',
   SCHEMADOC => XSD_FORMAT,
   LOCAL     => TRUE,
   GENTYPES  => FALSE,
   GENTABLES => TRUE,
   OWNER     => 'DB_FINANCIERO'
 );

END; 
/
