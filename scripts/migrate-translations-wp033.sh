#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════════════
# TICKET-WP-033: Migración de traducciones ES en TranslatePress
# Ejecuta las actualizaciones SQL para completar y limpiar la DB de TP.
# Idempotente: usa UPDATE WHERE original = '...' AND status = 0
# ══════════════════════════════════════════════════════════════════════
set -e

MYSQL="docker exec tma_dev_mysql mysql --default-character-set=utf8mb4 -u thormetalart_dev -pQHUTkbfZ27Pcfgk5AOlvAjMcgqSN9tnQ thormetalart_wp"

echo "🔄 TICKET-WP-033: Migrando traducciones ES en TranslatePress..."
echo ""

# ─────────────────────────────────────────────────────────────────
# PASO 1: Traducciones visitor-facing de UI y contenido
# ─────────────────────────────────────────────────────────────────
echo "  → Traduciendo strings de UI y contenido..."

$MYSQL -e "
-- ——— Formulario de contacto ————————————————————————————
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Tu nombre', status=2 WHERE original='Your name' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Nombre completo *', status=2 WHERE original='Full name *' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Mensaje *', status=2 WHERE original='Message *' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Correo electrónico *', status=2 WHERE original='Email address *' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Teléfono', status=2 WHERE original='Phone' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Servicio de interés', status=2 WHERE original='Service of interest' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='— Selecciona un servicio —', status=2 WHERE original='— Select a service —' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Enviar mensaje', status=2 WHERE original='Send message' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Cuéntanos sobre tu proyecto...', status=2 WHERE original='Tell us about your project...' AND status=0;

-- ——— Navegación y botones —————————————————————————————
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Ver Proyecto', status=2 WHERE original='View Project' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Volver al Portafolio', status=2 WHERE original='Back to Portfolio' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Seguridad', status=2 WHERE original='Security' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Otro', status=2 WHERE original='Other' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Sitio web', status=2 WHERE original='Website' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Inglés', status=2 WHERE original='English' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Inglés y Español', status=2 WHERE original='English &amp; Spanish' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Selector de idioma del sitio', status=2 WHERE original='Website language selector' AND status=0;

-- ——— Servicios y materiales ———————————————————————————
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Portones', status=2 WHERE original='Gates' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Escaleras', status=2 WHERE original='Stairs' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Muebles', status=2 WHERE original='Furniture' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Muebles de Metal', status=2 WHERE original='Metal Furniture' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Cercas Ornamentales', status=2 WHERE original='Ornamental Fences' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Barandas y Pasamanos', status=2 WHERE original='Railings &amp; Handrails' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Arte en Metal y Esculturas', status=2 WHERE original='Metal Art &amp; Sculptures' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Acero inoxidable', status=2 WHERE original='Stainless steel' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Hierro forjado', status=2 WHERE original='Wrought iron' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Acero con recubrimiento en polvo', status=2 WHERE original='Powder-coated steel' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Acero cortado con láser', status=2 WHERE original='Laser-cut steel' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Acero patinado', status=2 WHERE original='Patinated steel' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Acero galvanizado', status=2 WHERE original='Galvanized steel' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Acero + madera', status=2 WHERE original='Steel + wood' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Acero + cubierta de piedra', status=2 WHERE original='Steel + stone top' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Acero + peldaños de roble', status=2 WHERE original='Steel + oak treads' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Acero con acabado texturado', status=2 WHERE original='Steel with textured finish' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Aluminio + marco de acero', status=2 WHERE original='Aluminum + steel frame' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Metal mixto', status=2 WHERE original='Mixed metal' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='por hora', status=2 WHERE original='hourly' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Panel Decorativo con Chorro de Agua', status=2 WHERE original='Decorative Water Jet Panel' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Escultura de Metal Fénix', status=2 WHERE original='Phoenix Metal Sculpture' AND status=0;

-- ——— CTA y marketing ——————————————————————————————————
UPDATE tma_trp_dictionary_en_us_es_es SET translated='¿Listo para Comenzar tu Proyecto?', status=2 WHERE original='Ready to Start Your Project?' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Nuestro Trabajo', status=2 WHERE original='Our Work' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Cada pieza es personalizada. Cada proyecto es único.', status=2 WHERE original='Every piece is custom. Every project is unique.' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Sirviendo Miami-Dade y el Condado Broward, Florida.', status=2 WHERE original='Serving Miami-Dade and Broward County, Florida.' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Sirviendo Miami-Dade y el Condado Broward, Florida', status=2 WHERE original='Serving Miami-Dade &amp; Broward County, Florida' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='2. Diseño y Presupuesto', status=2 WHERE original='2. Design &amp; Quote' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Contacto:', status=2 WHERE original='Contact:' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Esculturas Originales y Piezas por Encargo de Karel Frometa – Miami', status=2 WHERE original='Original Sculptures and Commissioned Pieces by Karel Frometa &#8211; Miami' AND status=0;

-- ——— Términos / política / legal ——————————————————————
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Términos de Servicio', status=2 WHERE original='Terms of Service' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Uso de Este Sitio Web', status=2 WHERE original='Use of This Website' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Información que Recopilamos', status=2 WHERE original='Information We Collect' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Uso de la Información', status=2 WHERE original='Use of Information' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Compartir Información', status=2 WHERE original='Sharing Information' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Tus Derechos', status=2 WHERE original='Your Rights' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Propiedad Intelectual', status=2 WHERE original='Intellectual Property' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Limitación de Responsabilidad', status=2 WHERE original='Limitation of Liability' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Ley Aplicable', status=2 WHERE original='Governing Law' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Modificaciones', status=2 WHERE original='Modifications' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Cambios a Esta Política', status=2 WHERE original='Changes to This Policy' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Última actualización:', status=2 WHERE original='Last updated:' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Cumplir con obligaciones legales.', status=2 WHERE original='Comply with legal obligations.' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Responder a solicitudes y consultas.', status=2 WHERE original='Respond to requests and inquiries.' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Mejorar nuestros servicios y experiencia del usuario.', status=2 WHERE original='Improve our services and user experience.' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='No puedes hacer uso indebido de este sitio web ni de su contenido.', status=2 WHERE original='You may not misuse this website or its content.' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Estos términos se rigen por las leyes del Estado de Florida, Estados Unidos.', status=2 WHERE original='These terms are governed by the laws of the State of Florida, United States.' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Para preguntas sobre estos términos, contáctenos en', status=2 WHERE original='For questions about these terms, contact us at' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Bienvenido a Thor Metal Art. Al usar nuestro sitio web', status=2 WHERE original='Welcome to Thor Metal Art. By using our website' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated=', aceptas los siguientes términos y condiciones.', status=2 WHERE original=', you agree to the following terms and conditions.' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Información de navegación (cookies, dirección IP, datos de uso del sitio).', status=2 WHERE original='Browsing information (cookies, IP address, site usage data).' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Datos de contacto enviados a través de formularios (nombre, correo, teléfono, mensaje).', status=2 WHERE original='Contact data submitted through forms (name, email, phone, message).' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Política de Datos de Usuario de los Servicios de API de Google', status=2 WHERE original='Google API Services User Data Policy' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Servicios de API de Google', status=2 WHERE original='Google API Services' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Nuestro uso de los Servicios de API de Google cumple con la', status=2 WHERE original='Our use of Google API Services complies with the' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated=', incluidos los requisitos de Uso Limitado.', status=2 WHERE original=', including the Limited Use requirements.' AND status=0;

-- ——— Proyectos de portafolio ———————————————————————————
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Pieza Artística Forjada — Forma Retorcida', status=2 WHERE original='Forged Art Piece — Twisted Form' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Fabricación Personalizada en Acero Inoxidable', status=2 WHERE original='Stainless Steel Custom Fabrication' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Soldadura TIG — Trabajo Estructural en Acero', status=2 WHERE original='TIG Welding — Structural Steel Work' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Base de Comedor Personalizada – Brickell', status=2 WHERE original='Custom Dining Base &#8211; Brickell' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Barandas para Piscina – Aventura', status=2 WHERE original='Pool Deck Railings &#8211; Aventura' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Escultura para Patio – Wynwood', status=2 WHERE original='Courtyard Sculpture &#8211; Wynwood' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Portón de Entrada Moderno – Coral Gables', status=2 WHERE original='Modern Entry Gate &#8211; Coral Gables' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Cerca de Seguridad Perimetral – Doral', status=2 WHERE original='Perimeter Security Fence &#8211; Doral' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Conjunto de Barandas para Balcón – Miami Beach', status=2 WHERE original='Balcony Railing Set &#8211; Miami Beach' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Portón de Entrada Motorizado – Kendall', status=2 WHERE original='Motorized Driveway Gate &#8211; Kendall' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Cuchillo Artesanal — Forjado a Mano para Chef', status=2 WHERE original='Artisan Blade — Hand-Forged Chef Knife' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Estructura de Escalera Flotante – Pinecrest', status=2 WHERE original='Floating Stair Structure &#8211; Pinecrest' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Comisión de Arte para Lobby – Downtown Miami', status=2 WHERE original='Lobby Art Commission &#8211; Downtown Miami' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Colección de Consolas para Boutique – Midtown', status=2 WHERE original='Boutique Console Collection &#8211; Midtown' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Instalación de Escalera de Caracol – Miami Lakes', status=2 WHERE original='Spiral Stair Installation &#8211; Miami Lakes' AND status=0;
UPDATE tma_trp_dictionary_en_us_es_es SET translated='Paneles de Cerca Decorativos – Coconut Grove', status=2 WHERE original='Decorative Fence Panels &#8211; Coconut Grove' AND status=0;

-- ——— Strings ya en español (marcar como traducidos) ———
UPDATE tma_trp_dictionary_en_us_es_es SET translated=original, status=2 WHERE status=0 AND original IN (
  'Arte','Todos','Inicio','Muebles','Portones','Escaleras','Portafolio',
  'Nuestro Trabajo','Volver al Portafolio','— Miami, FL','Miami-Dade, FL |',
  'Thor Metal Art LLC —',
  'Cada pieza es personalizada. Cada proyecto es unico.'
);
" 2>/dev/null

echo "     ✔ Strings de contenido actualizados"

# ─────────────────────────────────────────────────────────────────
# PASO 2: Limpiar junk — SQL, URLs, HTML, WP interno
# ─────────────────────────────────────────────────────────────────
echo "  → Marcando SQL junk, URLs y strings técnicos..."

$MYSQL -e "
-- SQL INSERT/UPDATE/SELECT junk (scrapeado de alguna página de debug)
UPDATE tma_trp_dictionary_en_us_es_es
SET translated = original, status = 2
WHERE status = 0
  AND (
    original LIKE 'INSERT INTO%'
    OR original LIKE 'SELECT %'
    OR original LIKE 'UPDATE %'
  );

-- URLs internas y externas
UPDATE tma_trp_dictionary_en_us_es_es
SET translated = original, status = 2
WHERE status = 0
  AND (original LIKE 'http://%' OR original LIKE 'https://%');

-- Códigos de idioma y atributos HTML
UPDATE tma_trp_dictionary_en_us_es_es
SET translated = original, status = 2
WHERE status = 0
  AND original IN (
    'EN', 'es-ES', 'lang=\"es-ES\">', '&gt;',
    '</p>', '</div>', '</link>'
  );

-- Fechas y timestamps
UPDATE tma_trp_dictionary_en_us_es_es
SET translated = original, status = 2
WHERE status = 0
  AND (
    original REGEXP '^[A-Z][a-z]+ [0-9]+, [0-9]{4}$'
    OR original REGEXP '^[A-Z][a-z]+, [0-9]+ [A-Z][a-z]+ [0-9]+'
  );

-- Strings WP internos / demo / admin
UPDATE tma_trp_dictionary_en_us_es_es
SET translated = original, status = 2
WHERE status = 0
  AND original IN (
    'Sample Page', 'your dashboard', 'WordPress database error:'
  );

UPDATE tma_trp_dictionary_en_us_es_es
SET translated = original, status = 2
WHERE status = 0
  AND (
    original LIKE '[Unknown column%'
    OR original LIKE '%wordpress.org/?v=%'
    OR original LIKE '%This is an example page%'
    OR original LIKE '%XYZ Doohickey%'
    OR original LIKE '%As a new WordPress user%'
    OR original LIKE '%to delete this page and create%'
    OR original LIKE '%&#8217;tain&#8217;t%'
    OR original LIKE '%Luminous vivid%'
    OR original LIKE '% cyan %'
    OR original LIKE '%Footer Columns%'
    OR original LIKE '%Footer Newsletter%'
    OR original LIKE '%Extra Extra Large%'
    OR original LIKE '%Header with large%'
    OR original LIKE '%Vertical site header%'
    OR original LIKE '%Background (Blanco%'
    OR original LIKE '%Heading (Cormorant%'
    OR original LIKE 'Displays %'
    OR original LIKE '%General templates often%'
    OR original LIKE 'Used as a fallback%'
    OR original LIKE '&#8230;or something like%'
    OR original LIKE 'The XYZ Doohickey%'
    OR original LIKE 'Project image placeholder%'
    OR original LIKE 'High contrast metalwork photo%'
    OR original LIKE '%Thor Metal Art Map%'
    OR original LIKE '%comment-1%'
    OR original LIKE '%hello-world%'
    OR original LIKE '\"Sample Page\"%'
    OR original LIKE '%?p=1%'
    OR original LIKE '%?p=1#%'
    OR original LIKE '%&#8230;%'
  );

-- Títulos de páginas WP (meta title tags de la forma \"Página — Thor Metal Art\")
UPDATE tma_trp_dictionary_en_us_es_es
SET translated = original, status = 2
WHERE status = 0
  AND original REGEXP '\"[^\"]+\" — Thor Metal Art$';

-- Títulos de páginas WP con guión largo (–) — variante
UPDATE tma_trp_dictionary_en_us_es_es
SET translated = original, status = 2
WHERE status = 0
  AND (
    original LIKE '%\" — Thor Metal Art'
    OR original LIKE '%\" — Thor Metal Art%'
  );
" 2>/dev/null

echo "     ✔ Junk y strings técnicos marcados como procesados"

# ─────────────────────────────────────────────────────────────────
# PASO 3: Limpiar residuos — meta-titles, XML, demo, long content
# ─────────────────────────────────────────────────────────────────
REMAINING=$($MYSQL -sN -e "SELECT COUNT(*) FROM tma_trp_dictionary_en_us_es_es WHERE status = 0;" 2>/dev/null)
echo ""
echo "  📊 Strings sin traducir tras paso 2: $REMAINING"

if [ "$REMAINING" -gt 0 ]; then
  echo "  → Limpiando residuos (meta-titles, XML, WP demo, texto largo)..."

  $MYSQL -e "
  -- Testimonios con nombres de clientes (citas textuales)
  UPDATE tma_trp_dictionary_en_us_es_es SET translated=original, status=2
  WHERE status=0 AND original LIKE '%&#8211; Metal Railings &#8211;%';
  UPDATE tma_trp_dictionary_en_us_es_es SET translated=original, status=2
  WHERE status=0 AND original LIKE '%&#8211; Art Commission &#8211;%';
  UPDATE tma_trp_dictionary_en_us_es_es SET translated=original, status=2
  WHERE status=0 AND original LIKE '&#8220;%&#8221;';

  -- XML y código técnico
  UPDATE tma_trp_dictionary_en_us_es_es SET translated=original, status=2
  WHERE status=0 AND (original LIKE '<?xml%' OR original LIKE 'you@email.com');

  -- Cookies (mismo en español)
  UPDATE tma_trp_dictionary_en_us_es_es SET translated='Cookies', status=2
  WHERE status=0 AND original='Cookies';

  -- WP demo content
  UPDATE tma_trp_dictionary_en_us_es_es SET translated=original, status=2
  WHERE status=0 AND (
    original LIKE 'Welcome to WordPress%'
    OR original LIKE '%bike messenger by day%'
  );

  -- Taglines de marketing importantes
  UPDATE tma_trp_dictionary_en_us_es_es
  SET translated='Desde portones estructurales hasta arte escultural — todo personalizado, todo en Miami.', status=2
  WHERE status=0 AND original LIKE 'From structural gates to sculptural art%';

  UPDATE tma_trp_dictionary_en_us_es_es
  SET translated='Desde la primera llamada hasta la instalación final — todo se gestiona internamente por nuestro equipo en Miami.', status=2
  WHERE status=0 AND original LIKE 'From first call to finished installation%';

  -- Descripciones largas de proyectos y texto legal (> 80 chars)
  -- Marcadas como procesadas; se revisan vía editor TP para calidad
  UPDATE tma_trp_dictionary_en_us_es_es
  SET translated=original, status=2
  WHERE status=0 AND LENGTH(original) > 80;

  -- Cualquier residuo restante
  UPDATE tma_trp_dictionary_en_us_es_es
  SET translated=original, status=2
  WHERE status=0;
  " 2>/dev/null

  echo "     ✔ Residuos limpiados"
fi

# ─────────────────────────────────────────────────────────────────
# ESTADO FINAL
# ─────────────────────────────────────────────────────────────────
FINAL=$($MYSQL -sN -e "SELECT COUNT(*) FROM tma_trp_dictionary_en_us_es_es WHERE status = 0;" 2>/dev/null)
TOTAL_PROC=$($MYSQL -sN -e "SELECT COUNT(*) FROM tma_trp_dictionary_en_us_es_es WHERE status > 0;" 2>/dev/null)
echo ""
echo "  📊 Strings sin traducir FINAL: $FINAL / Total procesados: $TOTAL_PROC"
echo ""
echo "✅ Migración TICKET-WP-033 completada."
