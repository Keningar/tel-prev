SET SERVEROUTPUT ON
DECLARE
    --
    type                typeArray IS VARRAY(76) OF VARCHAR2(45);
    type                typeArrayNodos IS VARRAY(518) OF VARCHAR2(45);
    Ln_IdElemento       NUMBER;
    Ln_IdElementoAgre   NUMBER;
    Ln_IdDetElemento    NUMBER;
    Ln_Total            NUMBER;
    Ln_ContadorAgre     NUMBER := 0;
    Ln_ContadorRel      NUMBER := 0;
    Ln_ContadorPerm     NUMBER := 0;
    Ln_IdEmpresa        NUMBER := 10;
    Lb_User             VARCHAR2(20) := 'migracion_tn';
    Lr_Agregadores      typeArray;
    Lr_Nodos            typeArrayNodos;
    Lr_NodosRelacion    typeArrayNodos;
    --
    CURSOR C_getIdElemento (Cv_NombreElemento DB_INFRAESTRUCTURA.INFO_ELEMENTO.NOMBRE_ELEMENTO%TYPE,
                            Cn_ModeloElemento DB_INFRAESTRUCTURA.INFO_ELEMENTO.MODELO_ELEMENTO_ID%TYPE)
    IS
      SELECT ID_ELEMENTO
      FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO
      WHERE NOMBRE_ELEMENTO  = Cv_NombreElemento
      AND MODELO_ELEMENTO_ID = Cn_ModeloElemento
      AND ESTADO             = 'Activo';
    --
    CURSOR C_getIdDetalleElemento (Cn_IdElemento DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO.ELEMENTO_ID%TYPE)
    IS
      SELECT ID_DETALLE_ELEMENTO
      FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
      WHERE ELEMENTO_ID  = Cn_IdElemento
      AND DETALLE_NOMBRE = 'PERMITE_MULTIPLATAFORMA'
      AND USR_CREACION   = Lb_User
      AND ESTADO         = 'Activo';
    --
BEGIN
    --
    --insert relación nodo agregador
    --
    Lr_Nodos := typeArrayNodos('Alausi SDH (O)','Atis Ambato sdh (O)','Miñarica (E)','Nuevo Ficoa (E)','Arbolito (E)','Estadio amb (E )',
                'Huachi nodo y oficina (E)','Izamba (E)','La Merced (E)','Cristobal (E)','Bodega amb (E)','Loreto amb (E)','Martinez (E)','Armenia (A)',
                'Endara (E)','Vilatuña (E)','Montalvo (E)','Isla Española (E)','San Juan de Conocoto (E)','Alondras (E)','Guayasamin (E)','Rivera (E)',
                'Chaupitena (E)','San Nicolas (E)','Morales (E)','Machachi (E)','Tambillo (E)','Aloag (E)','Los Tubos  (E)','Abel Gilbert (E)',
                'Divino Niño (E)','El Recreo Gepon (E)','Primavera 2 (E)','Entre rios (E)','Recreo (E)','Pascuales 2 (E)','Panorama (E)','La Joya (E)',
                'FIT 1 (E)','Rinconcito (E)','Primavera 1 (E)','Aurora (A)','El Buijo (E)','Duran 2 (E)','Las Lojas (E)','Amazonas (E)','Baños (O)',
                'Zaragoza (E)','Calderon (E)','Solis (E)','Zabala (E)','Cardenas (E)','Roldos (E)','Mitad del Mundo (E)','San Carlos (E)','Pomasqui (E)',
                'Bodega (E)','Carapungo (E)','Muller (E)','San Juan de Calderon (E)','Morlan (E)','Fortines (E)','Ruiz (E)','Colinas (E)','Cocotog (E)',
                'Ludeña (E)','Fatima (E)','Barrio Ecuador (E)','Real Audiencia (E)','Cotocollao (E)','Eucaliptos (E)','Nazacota (E)','Murialdo (E)',
                'Complejo Quito (E)','Ponce (E)','Córdova (E)','Pampa (E)','Pananorte (E)','Benitez (E)','Cipreses (E)','Tanda (E)','Guachan (E)',
                'Guabos (E)','Lumbisi (E)','Orquideas (E)','Tumbaco (E)','Mandarina (E)','Ferrero (E)','Vasquez (E)','Borromoni (A)',
                'Gomez Rendon  (E)','La 14 y Maldonado (E)','Alcedo y Agusto Dillon (E)','Brasil (E)','Agregador Centro (A)','Alcedo y Aciclo Garay (E)',
                'Mexico (E)','Parque Forestal (E)','Boyaca (E) (Migrando)','Bellas Artes (E)','Pedro Moncayo (E)','Luis Urdaneta (E)','Tungurahua  (E)',
                'Polideportivo Huancavilca1 (E)','Bolivariana (E)','Cordova gye (E) (Migrando)','Allcomp (E)','Garcia Aviles (E)','El Dorado Duran (E) ',
                'Brisas de procarsa (E)','Latamfiber (E) ','Calle D (E)','Kennedy (E)','Gaspar (E)','Villalengua (E)','Gosseal (A)','Acevedo (E)',
                'Whymper (E)','Florida (E)','Cochapamba (E)','El Sol (E)','Belmonte / Metropoli (E)','Inglaterra (E)','Illinworth (E)','Labrador (E)',
                'Nogales (E)','Plaza de Toros (E)','Guaranda SDH (O)','Guanujo (E)','Guaranda (E)','Alpachaca (E)','Mucho Lote (E)','Prosperina 1  Gepon (E)',
                'Orquideas gye (E)','Juan Montalvo (E)','Bastion 2 (E)','Vergeles 1 (E)','Jardines de la Esperanza (E)','Vachagnon 1 (E)','Florida 2 (E)',
                'Paraiso de la flor 1 (E)','Inmaconsa (A)','Vergeles 2 (E)','Pascuales 1 (E)','Orellana gye (E)','Mapasingue 4 (E)','Ciudad del Rio (E)',
                'Bastion 1 (E)','Brahma (E)','Nueva Prosperina 1 (E)','Samanes 6 (E) Migrando ','Florida 1 (E)','Pycca (E)','Montebello (E)','Km 27 (E)',
                'Km 17 (E)','km 18 (E)','Beata Molina 1 (E)','Paraiso de la flor 2  (E)','Mi lote (E)','El trebol (E)','Nueva Prosperina (E)','Petrillo (E)',
                'Monte Sinai (E)','Sergio Toral (E)','Jipijapa SDH (O)','Sauces 4 (E)','Guayacanes (E)','Sauces 6 (E)','Alborada 3 (E)','Sauces 2 (E)',
                'Americas 2 (E)','Kennedy 2 (E)','Atarazana (E)','Alborada 1 (E)','Americas 1 (E)','Kennedy 1 (A)','Alborada 2 (E)','Emtelsur (E)',
                'Los Nevados (E)','Niagara (E)','Latacunga SDH (O)','Calvario Nuevo (E)','Santa Monica  (E)','Barbasquillo 1 (E) Migrando ','Urbirrios (E)',
                'Maria Auxiliadora  (E)','Los Cactus  (E)','La Aurora mnt (E)','La Pradera mnt (E)','Uleam (E)','Barrio Centenario (E)','Jocay  (E)',
                'Seafman (E)','Anibal San Andres (E)','Bahia de Caraquez  (E) Migrand','Cabinanet  (NE)','Mapasingue 5 (E)','Miraflores (A) MIGRANDO ',
                'Puerto Hondo (E)','Puertas del Sol (E) ','Urdesa 1 (E)','Mapasingue 6 (E) Migrando ','Girasoles (E)','Urbanor (E)','Bellavista Gepon (E)',
                'Chongon (E)','Martha de Roldos (E)','Portete de Tarqui (E)','Urdesa 2 (E)','Ceibos (E)','Nueva Esperanza (E)','Canada (E)','Ejido (E)',
                'Muros (A)','Andagoya (E)','Swiss hotel  (E)','Colon (E)','Oriental (E)','Salgado (E)','Palacios (E)','18 de Septiembre (E)','Baquedano (E)',
                'Moran (E)','Pelileo (E)','Puembo (E)','Pifo (E)','Chiche (E)','Tababela (E)','Playas 2 (A)','Playas Bellavista (E)','El Arenal  (E)',
                'Portoviejo 1 (O) ','26 de Septiembre (E)','San Alejo (E)','Andres de Vera (E)','Portoviejo Zona Industrial  (E)','San Cristobal mnt (E)',
                'Ciudadela Municipal (E)','Portoviejo Norte  (E)','Picoaza (E)','Chone Centro (E)','Calceta (E)','Posorja 2 (E)','Progreso (O)','Velez (E)',
                'Las Cañas (E)','El Dorado (E)','Puyo SDH (O)','Parque Industrial rio (E)','Terminal rio (E)','Riobamba SDH (O)','Comil (E)','Imperial (E)',
                'San Alfonso (E)','Espoch (E)','El Libro (E)','Cisneros (E)','Guano (E)','Cajabamba (E)','Guamote (E)','Salcedo (E)','San Lorenzo 1 (E)',
                'Chipipe 1 (E)','Pueblo Nuevo 1 (E)','Paraiso 1 (E)','Punta Blanca 1(E) MIGRANDO ','Santa rosa Salinas 1 (E)','Salinas 1 (O)','Las Dunas 1 (E)',
                'Repetidora Muey (E)','Manglaralto (E)','Punta Carnero (E)','Anconcito 1 (E)','Nodo San Pablo Gepon (E)','Jambeli (E)','Las Tunas (E)',
                'Santa Ana (E)','Carolina 1 (I)','Terminal Terrestre SL 1 (E)','Enriquez Gallo (E)','Chulluype (E)','Calderon 1 (E)','Santa Elena 1 (E)',
                'Libertad  (E)','Puerto Rico (E)','Sauces sln (E)','Nodo Olon Gepon (E)','Chanduy (E)','San Jose (E)','Libertador Bolívar (E)',
                'La Entrada Gepon (E)','San Vicente mnt (E )','Shell (E)','Guasmo Sur 1 (E)','Trinitaria 2 (E)','Trinitaria 1 (E) Migrando ',
                'Estero Salado 2 (E)','Esteros 1 (E)','Union de Bananeros (E)','Batallon 3 (E)','Guasmo Este (E)','CAE (E)','Estero Salado 1 (E)',
                'Batallon 2 (E)','Esclusas (E)','Floresta 1 (E)','Puerto Liza (E)','Pradera 1 (E)','La 29 y Bolivia (E)','Sur gye (A)','Avenida 14 y Venezuela (E)',
                'Beatriz Bejar (E)','Letamendi (E)','Luz del Guayas 1 (E)','27 diagonal y Transversal (E)','Hospital Guayaquil (E)','La Saiba (E)',
                'Vacas Galindo (E)','Batallon 1 (E)','Luz del Guayas 2 (E)','Colegio Jose Llerena (E)','Calle B (E)','Sur 2 (A)','Condor (E)','Calle K (E)',
                'Tabiazo (E)','Calle 3 (E)','Lucha de los pobres (E)','Recoleta (E)','Quitumbe (E)','Calle 4 (E)','Chillogallo (E)','San Bartolo (E)',
                'Quiroz (E)','Chura (E)','S50 (E)','Chiriyacu (E)','Principe (E)','Ajavi (E)','Parque Industrial (E)','Dos Puentes (E)','Guamani (E)',
                'Tarqui 11 (E)','Calle 11 (E)','Calle H (E)','Orellana (E)','Salazar (E)','Recreo (E)','Solanda (E)','Alpahuasi (E)','Gualleturo (E)',
                'Colector (E)','Calle A (E)','Bolivar (E)','Iturralde (E)','Molina (E)','Troje (E)','Tena SDH (Dos Rios) (O)','Tena (E)','San Antonio (E)',
                'Bellavista Baja (E)','Eloy Alfaro SDH  (O)','Atuntaqui (A)','Hostal Azogues  (E)','La Playa  (E)','Babahoyo 3 (E)','Babahoyo 1 (O)',
                'Balzar 1 (O)','Buena Fe 2 (E)','Cayambe SDH (O)','Cotacachi (E)','Bellavista Cuenca  (A)','Mall del Rio (E)','Control Sur (E)',
                'Estadio cue (E)','Eucaliptos cue (E)','Alvarez (E)','Totoracocha (E)','El Arenal  (E)','Calderon cue (E)','Epoca (E)','Castellana (E)',
                'San Pedro cue (E)','Ricaurte cue (E)','Chaullabamba (NE)','Daule 1 (E)','Daule 2 (O) ','Concordia 2 (E)','Mendoza (E)','Gremio (E)',
                '3 de Julio (E)','Coca 1 (E)','Labaka (E)','Taracoa (E) ','El Empalme 1 (NE)','El Empalme 2 (E)','Tolita SDH (O)','Las Palmas (E)',
                'Esmeraldas (E)','Caliente (E)','Propicia (E)','Codesa (E)','Parada 7 (E)','Santas Vainas (E)','Voluntad (E)','Casa Bonita (E)',
                'Los Almendros (E)','Tachina (nuevo)','Gualaceo (E)','Guayllabamba (E)','Yacucalle (E)','Ejido de Caranqui (E)','Pilanqui (E)',
                'Mutualista (E)','Ibarra SDH (O)','Mariano Acosta (E)','La Victoria (E)','Alpachaca iba (E)','Azaya (E)','28 de Septiembre (E)',
                'Jujan (E)','Lago Agrio 2 (E)','Lago Agrio SDH (I) ','Venezuela (E)','Espejo (E)','Telepuerto Loja (O)','UNL (E)','Pradera (E)',
                'La Salle (E)','Materdei 1 (E)','Isidro Ayora (E)','San Vicente 2 (E)','Nueva Granada Loj (E)','Motupe (E)','Belen (E)','Amable Maria (E)',
                'El Valle (E)','Macas 1 (NE)','Villareal (E)','Sucua (E)','Arizaga (E)','Brisas del Mar (E)','Machala 3 (E)','Agregador Machala (A)',
                'Florida 4 Machala (E)','La Union mach (E)','Adavid (E)','Puerto Bolivar (E)','El Cambio 2 (E)','Milagro 2 (E)','Milagro 3 (E)',
                'Milagro (O)','Naranjito (E)','Marcelino Maridueña (E)','Naranjal 1 (O)','Cotama (A)','Otavalo (E)','Instituto (E)','Palenque 1  (E)',
                'Palestina (O)','Pasaje 2 (O)','Paute (E)','Piñas 1 (E)','Piñas 2 (E)','Decima Primera (E)','7 de Octubre 2 (E)','San Camilo 2 (E)',
                'Qmax (E)','CRE  (San Camilo) (E)','Telepuerto Quevedo (O)','El Desquite (E)','Nid (E)','20 de Febrero (E)','Obelisco (E)','Venus (E)',
                'Quininde (E)','Valle Alto SDH (O)','Las Maravillas (E)','Santa Rosa mach (E)','Shuar (E)','Bomberos (E)','Colorado (E)','Via Esmeraldas (E)',
                'Via Quito (E)','Santo Domingo SDH (O)','Via Chone (E)','Colegio Tecnico (E)','Via Quevedo (E)','Porton (E)','Santa Martha (E)',
                'Ciudad Verde (E) Migrando','Lorena (E)','Aurora (E)','Nueva Republica (E)','Asistencia Municipal (E)','Nuevo Amanecer (E)',
                '15 de Septiembre (E)','Turiscol (E)','Tabacundo SDH (O)','Quinche (E)','Tarifa (E)','Tonsupa (E)','Atacames (E)','Castelnovo (E)',
                'Prado (E)','Nueva Granada esm (E)','Atahualpa (E)','Rosario (E)','Ciudadela del Chofer (E)','El Rosal (E)','Ciudadela San Francisco (E)',
                'Tulcan SDH (O)','Vinces 2 (E)','Vinces 1 (A)','El Triunfo 2 (E)','El Triunfo 1 (E)','Yaguachi (E)');
    --
    Lr_NodosRelacion := typeArrayNodos('ALAUSI','AMBATO','AMBATO','AMBATO','AMBATO','AMBATO','AMBATO','AMBATO','AMBATO','AMBATO','AMBATO','AMBATO',
                'AMBATO','ARMENIA','ARMENIA','ARMENIA','ARMENIA','ARMENIA','ARMENIA','ARMENIA','ARMENIA','ARMENIA','ARMENIA','ARMENIA','ARMENIA','ARMENIA',
                'ARMENIA','ARMENIA','AURORA','AURORA','AURORA','AURORA','AURORA','AURORA','AURORA','AURORA','AURORA','AURORA','AURORA','AURORA','AURORA',
                'AURORA','AURORA','AURORA','AURORA','BAÑOS','BAÑOS','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA',
                'BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA',
                'BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BODEGA','BORROMONI','BORROMONI','BORROMONI','BORROMONI','BORROMONI','BORROMONI',
                'BORROMONI','BORROMONI','BORROMONI','BORROMONI','BORROMONI','CENTRO','CENTRO','CENTRO','CENTRO','CENTRO','CENTRO','CENTRO','CENTRO',
                'CENTRO','CENTRO','CENTRO','CENTRO','CENTRO','CENTRO','CENTRO','CENTRO','CENTRO','CENTRO','FABRICA','FABRICA','FABRICA','GOSSEAL','GOSSEAL',
                'GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL','GOSSEAL',
                'GUARANDA','GUARANDA','GUARANDA','GUARANDA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA',
                'INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA',
                'INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA','INMACONSA',
                'INMACONSA','INMACONSA','JIPIJAPA','KENNEDY','KENNEDY','KENNEDY','KENNEDY','KENNEDY','KENNEDY','KENNEDY','KENNEDY','KENNEDY','KENNEDY','KENNEDY',
                'KENNEDY','KENNEDY','LATACUNGA','LATACUNGA','LATACUNGA','LATACUNGA','MANTA','MANTA','MANTA','MANTA','MANTA','MANTA','MANTA','MANTA','MANTA',
                'MANTA','MANTA','MANTA','MANTA','MANTA','MIRAFLORES','MIRAFLORES','MIRAFLORES','MIRAFLORES','MIRAFLORES','MIRAFLORES','MIRAFLORES','MIRAFLORES',
                'MIRAFLORES','MIRAFLORES','MIRAFLORES','MIRAFLORES','MIRAFLORES','MIRAFLORES','MIRAFLORES','MUROS','MUROS','MUROS','MUROS','MUROS','MUROS',
                'MUROS','MUROS','MUROS','MUROS','MUROS','MUROS','PELILEO','PIFO','PIFO','PIFO','PIFO','PLAYAS','PLAYAS','PLAYAS','PORTOVIEJO','PORTOVIEJO',
                'PORTOVIEJO','PORTOVIEJO','PORTOVIEJO','PORTOVIEJO','PORTOVIEJO','PORTOVIEJO','PORTOVIEJO','PORTOVIEJO','PORTOVIEJO','POSORJA','PROGRESO',
                'PUYO','PUYO','PUYO','PUYO','RIOBAMBA','RIOBAMBA','RIOBAMBA','RIOBAMBA','RIOBAMBA','RIOBAMBA','RIOBAMBA','RIOBAMBA','RIOBAMBA','RIOBAMBA',
                'RIOBAMBA','RIOBAMBA','SALCEDO','SALINAS','SALINAS','SALINAS','SALINAS','SALINAS','SALINAS','SALINAS','SALINAS','SALINAS','SALINAS','SALINAS',
                'SALINAS','SALINAS','SALINAS','SALINAS','SANTAANA','SANTAELENA','SANTAELENA','SANTAELENA','SANTAELENA','SANTAELENA','SANTAELENA','SANTAELENA',
                'SANTAELENA','SANTAELENA','SANTAELENA','SANTAELENA','SANTAELENA','SANTAELENA','SANTAELENA','SANVICENTE','SHELL','SUR','SUR','SUR','SUR','SUR',
                'SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR','SUR',
                'SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2',
                'SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','SUR2','TENA','TENA','TENA','TENA',
                'TENA','ATUNTAQUI','AZOGUES','AZOGUES','BABAHOYO','BABAHOYO','BALZAR','BUENAFE','CAYAMBE','COTACACHI','CUENCA','CUENCA','CUENCA','CUENCA',
                'CUENCA','CUENCA','CUENCA','CUENCA','CUENCA','CUENCA','CUENCA','CUENCA','CUENCA','CUENCA','DAULE','DAULE','ELCARMEN','ELCARMEN','ELCARMEN',
                'ELCARMEN','ELCOCA','ELCOCA','ELCOCA','ELEMPALME','ELEMPALME','ESMERALDAS','ESMERALDAS','ESMERALDAS','ESMERALDAS','ESMERALDAS','ESMERALDAS',
                'ESMERALDAS','ESMERALDAS','ESMERALDAS','ESMERALDAS','ESMERALDAS','ESMERALDAS','GUALACEO','GUAYLLABAMBA','IBARRA','IBARRA','IBARRA','IBARRA',
                'IBARRA','IBARRA','IBARRA','IBARRA','IBARRA','IBARRA','JUJAN','LAGOAGRIO','LAGOAGRIO','LAGOAGRIO','LAGOAGRIO','LOJA','LOJA','LOJA','LOJA',
                'LOJA','LOJA','LOJA','LOJA','LOJA','LOJA','LOJA','LOJA','MACAS','MACAS','MACAS','MACHALA','MACHALA','MACHALA','MACHALA','MACHALA','MACHALA',
                'MACHALA','MACHALA','MACHALA','MILAGRO','MILAGRO','MILAGRO','MILAGRO','MILAGRO','NARANJAL','OTAVALO','OTAVALO','OTAVALO','PALENQUE','PALESTINA',
                'PASAJE','PAUTE','PIÑAS','PIÑAS','QUEVEDO','QUEVEDO','QUEVEDO','QUEVEDO','QUEVEDO','QUEVEDO','QUEVEDO','QUEVEDO','QUEVEDO','QUEVEDO','QUEVEDO',
                'QUININDE','QUININDE','QUININDE','SANTAROSA','SHUSHUFINDI','SHUSHUFINDI','STDO','STDO','STDO','STDO','STDO','STDO','STDO','STDO','STDO','STDO',
                'STDO','STDO','STDO','STDO','STDO','STDO','STDO','TABACUNDO','TABACUNDO','TARIFA','TONSUPA','TONSUPA','TONSUPA','TONSUPA','TONSUPA','TONSUPA',
                'TONSUPA','TULCAN','TULCAN','TULCAN','TULCAN','VINCES','VINCES','VIRGENFAT','VIRGENFAT','YAGUACHI');
    --
    Ln_Total := Lr_Nodos.count;
    --
    FOR i in 1 .. Ln_Total LOOP
        --
        Ln_IdElemento := NULL;
        Ln_IdElementoAgre := NULL;
        --obtengo el id del nodo
        OPEN C_getIdElemento(Lr_Nodos(i),350);
        FETCH C_getIdElemento INTO Ln_IdElemento;
        CLOSE C_getIdElemento;
        --obtengo el id del agregador
        OPEN C_getIdElemento(TRIM(INITCAP(Lr_NodosRelacion(i))),1985);
        FETCH C_getIdElemento INTO Ln_IdElementoAgre;
        CLOSE C_getIdElemento;
        --
        IF Ln_IdElemento IS NOT NULL and Ln_IdElementoAgre IS NOT NULL THEN
            --eliminar el detalle elemento del agregador al nodo
            DELETE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
            WHERE ELEMENTO_ID = Ln_IdElemento AND DETALLE_NOMBRE = 'AGREGADOR_ASIGNADO' AND ESTADO = 'Activo';
            Ln_ContadorRel := Ln_ContadorRel + 1;
            --verifico si existe el detalle permite multiplataforma
            Ln_IdDetElemento := NULL;
            OPEN C_getIdDetalleElemento(Ln_IdElemento);
            FETCH C_getIdDetalleElemento INTO Ln_IdDetElemento;
            CLOSE C_getIdDetalleElemento;
            IF Ln_IdDetElemento IS NOT NULL THEN
                --insert el detalle elemento permite multiplataforma al nodo
                DELETE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO WHERE ID_DETALLE_ELEMENTO = Ln_IdDetElemento;
                Ln_ContadorPerm := Ln_ContadorPerm + 1;
            END IF;
        END IF;
    END LOOP;
    --
    --eliminar agregadores
    --
    Lr_Agregadores := typeArray('ALAUSI','AMBATO','ARMENIA','AURORA','BAÑOS','BODEGA','BORROMONI','CENTRO','FABRICA','GOSSEAL','GUARANDA',
                'INMACONSA','JIPIJAPA','KENNEDY','LATACUNGA','MANTA','MIRAFLORES','MUROS','PELILEO','PIFO','PLAYAS','PORTOVIEJO','POSORJA','PROGRESO',
                'PUYO','RIOBAMBA','SALCEDO','SALINAS','SANTAANA','SANTAELENA','SANVICENTE','SHELL','SUR','SUR2','TENA','ATUNTAQUI','AZOGUES','BABAHOYO',
                'BALZAR','BUENAFE','CAYAMBE','COTACACHI','CUENCA','DAULE','ELCARMEN','ELCOCA','ELEMPALME','ESMERALDAS','GUALACEO','GUAYLLABAMBA','IBARRA',
                'JUJAN','LAGOAGRIO','LOJA','MACAS','MACHALA','MILAGRO','NARANJAL','OTAVALO','PALENQUE','PALESTINA','PASAJE','PAUTE','PIÑAS','QUEVEDO',
                'QUININDE','SANTAROSA','SHUSHUFINDI','STDO','TABACUNDO','TARIFA','TONSUPA','TULCAN','VINCES','VIRGENFAT','YAGUACHI');
    --
    Ln_Total := Lr_Agregadores.count;
    FOR i in 1 .. Ln_Total LOOP
        Ln_IdElemento := NULL;
        --obtengo el id del agregador
        OPEN C_getIdElemento(TRIM(INITCAP(Lr_Agregadores(i))),1985);
        FETCH C_getIdElemento INTO Ln_IdElemento;
        CLOSE C_getIdElemento;
        --
        IF Ln_IdElemento IS NOT NULL THEN
            --eliminar empresa elemento
            DELETE DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO WHERE ELEMENTO_ID = Ln_IdElemento;
            --eliminar historial elemento
            DELETE DB_INFRAESTRUCTURA.INFO_HISTORIAL_ELEMENTO WHERE ELEMENTO_ID = Ln_IdElemento;
            --eliminar detalle elemento
            DELETE DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO WHERE ELEMENTO_ID = Ln_IdElemento;
            --eliminar elemento
            DELETE DB_INFRAESTRUCTURA.INFO_ELEMENTO WHERE ID_ELEMENTO = Ln_IdElemento;
            --contador
            Ln_ContadorAgre := Ln_ContadorAgre + 1;
        END IF;
        --
    END LOOP;

    dbms_output.put_line('DELETE AGREGADORES: ' || Ln_ContadorAgre);
    dbms_output.put_line('DELETE RELACIONES AGREGADORES: ' || Ln_ContadorRel);
    dbms_output.put_line('DELETE PERMITIDO MULTIPLATAFORMA: ' || Ln_ContadorPerm);

    --roollback detalle agregador
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR7 = NULL WHERE PDE.ID_PARAMETRO_DET = (
        SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
          SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
        ) AND DET.DESCRIPCION = 'NOMBRES PARAMETROS DETALLES MULTIPLATAFORMA'
    );

    COMMIT;
    DBMS_OUTPUT.put_line('OK: Se guardaron los cambios.');

    EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.put_line('ERROR: '||sqlerrm);
        ROLLBACK;
END;
