<?php

/**
 * Thor Metal Art — Blog Post Spanish Translations Provisioner
 *
 * Provisions TranslatePress (ES) translations for 12 seed blog posts.
 * @package ThorMetalArt
 */

defined('ABSPATH') || exit;

function tma_provision_blog_translations_once()
{
    if (get_option('tma_blog_translations_v1')) {
        return;
    }

    global $wpdb;
    $orig_table = $wpdb->prefix . 'trp_original_strings';
    $dict_table = $wpdb->prefix . 'trp_dictionary_en_us_es_es';

    // ─── Part 1: Update known-ID strings (post 80 + sidebar) ──────────────
    $id_translations = array(
        169 => 'Guías de Fabricación',
        170 => '5 de enero de 2026',
        171 => 'por',
        173 => '¿Cuánto Cuesta una Puerta de Metal Personalizada en Miami?',
        175 => 'Factores Clave que Afectan el Costo',
        176 => 'Material:',
        177 => 'El hierro forjado, el acero y el aluminio tienen diferentes rangos de precio. El acero ofrece la mejor relación resistencia-costo, mientras que el aluminio es más ligero y naturalmente resistente al óxido.',
        178 => 'Tamaño y dimensiones:',
        179 => 'Una puerta peatonal sencilla es significativamente menos costosa que una puerta de entrada doble de 14 pies.',
        180 => 'Complejidad del diseño:',
        181 => 'Los trabajos ornamentales en espiral, los patrones personalizados y los acabados con pintura en polvo agregan al costo final.',
        182 => 'Herrajes y automatización:',
        183 => 'Agregar un abridor eléctrico, teclado o sistema de intercomunicación puede añadir entre $800 y $2,500 al proyecto.',
        184 => 'Instalación:',
        185 => 'El trabajo de cimentación, el anclaje de postes y la instalación eléctrica varían según las condiciones del sitio.',
        186 => 'Rangos de Precio Típicos en Miami-Dade',
        191 => 'Portón ornamental personalizado con trabajos en espiral:',
        193 => 'Portón automatizado con herrajes completos:',
        195 => 'Por Qué los Precios en Miami Son Diferentes',
        198 => 'Obtenga un Presupuesto Preciso',
        200 => '¿Listo para empezar?',
        201 => 'Contacta a Thor Metal Art hoy',
        202 => 'para tu estimado gratis.',
        203 => '¿Listo para Su Proyecto?',
        204 => 'Fabricación de metal personalizada en Miami-Dade. Estimados gratis. Sin compromiso.',
        205 => 'Publicaciones Recientes',
        206 => 'Del Boceto al Acero: Nuestro Proceso de Portones de Metal Personalizados',
        207 => '30 de marzo de 2026',
        208 => 'Soldadura TIG: El Arte detrás de la Herrería Estructural y Decorativa',
        209 => '23 de marzo de 2026',
        210 => 'Cómo Elegir la Cerca Metálica Correcta para su Propiedad en Miami',
        211 => '16 de marzo de 2026',
        212 => 'Corte por Chorro de Agua vs. Corte por Plasma: Guía del Fabricante',
        213 => '9 de marzo de 2026',
        214 => 'Por Qué los Arquitectos de Miami Eligen Herrería Personalizada',
        215 => '2 de marzo de 2026',
        216 => 'Categorías',
        217 => 'Cuidado y Mantenimiento',
        218 => 'Ideas de Diseño',
        219 => 'Arte en Metal',
        220 => 'Proyectos en Miami',
        221 => 'Fabricación de metal personalizada en Miami-Dade. Estimados gratis.',
    );

    foreach ($id_translations as $orig_id => $translated) {
        $existing_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM `{$dict_table}` WHERE original_id = %d LIMIT 1",
            $orig_id
        ));
        if ($existing_id) {
            $wpdb->update(
                $dict_table,
                array('translated' => $translated, 'status' => 2),
                array('id' => $existing_id),
                array('%s', '%d'),
                array('%d')
            );
        } else {
            $orig_text = $wpdb->get_var($wpdb->prepare(
                "SELECT original FROM `{$orig_table}` WHERE id = %d LIMIT 1",
                $orig_id
            ));
            if ($orig_text) {
                $wpdb->insert($dict_table, array(
                    'original'    => $orig_text,
                    'translated'  => $translated,
                    'status'      => 2,
                    'block_type'  => 0,
                    'original_id' => $orig_id,
                ), array('%s', '%s', '%d', '%d', '%d'));
            }
        }
    }

    // ─── Part 2: Mojibake strings — update by ID directly ──────────────────
    $mojibake_translations = array(
        174 => 'Los portones de metal personalizados son una de las inversiones más impactantes que puede hacer para su propiedad en Miami. Añaden seguridad, atractivo visual y valor duradero — pero ¿cuánto debería presupuestar? Entender los factores clave de precio le ayuda a planificar su proyecto con confianza.',
        187 => 'Portón sencillo simple (4–5 pies):',
        188 => '$1,200 – $2,500',
        189 => 'Portón de entrada doble (10–14 pies):',
        190 => '$3,000 – $7,000',
        192 => '$5,000 – $15,000+',
        194 => '$7,500 – $20,000+',
        196 => 'El clima del sur de Florida exige protección adicional contra la corrosión. Cada portón que fabricamos en Thor Metal Art incluye un imprimador de fosfato de zinc y un acabado de pintura en polvo de grado marino — esencial para sobrevivir el aire salino y la exposición UV de Miami.',
        197 => 'Los costos de mano de obra en Miami-Dade reflejan el mercado local. Nuestros soldadores y fabricadores están certificados, y llevamos seguro de responsabilidad completo — factores que protegen su propiedad y garantizan el cumplimiento del código.',
        199 => 'La mejor manera de obtener un precio preciso es una consulta gratuita en el sitio. Medimos su apertura, discutimos sus preferencias de diseño y proporcionamos un estimado por escrito en 48 horas. Sin sorpresas, sin cargos ocultos.',
    );

    foreach ($mojibake_translations as $orig_id => $translated) {
        $orig_text = $wpdb->get_var($wpdb->prepare(
            "SELECT original FROM `{$orig_table}` WHERE id = %d LIMIT 1",
            $orig_id
        ));
        if (! $orig_text) {
            continue;
        }
        $existing_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM `{$dict_table}` WHERE original_id = %d LIMIT 1",
            $orig_id
        ));
        if ($existing_id) {
            $wpdb->update(
                $dict_table,
                array('translated' => $translated, 'status' => 2),
                array('id' => $existing_id),
                array('%s', '%d'),
                array('%d')
            );
        } else {
            $wpdb->insert($dict_table, array(
                'original'    => $orig_text,
                'translated'  => $translated,
                'status'      => 2,
                'block_type'  => 0,
                'original_id' => $orig_id,
            ), array('%s', '%s', '%d', '%d', '%d'));
        }
    }

    // ─── Part 3: New strings for posts 81-91 ───────────────────────────────
    $new_translations = array(
        '7 Metal Gate Styles That Work in Miami\'s Climate' => '7 Estilos de Portones de Metal que Funcionan en el Clima de Miami',
        'Miami humidity and salt air demand gate designs that are both beautiful and durable. Here are 7 metal gate styles that thrive in South Florida.' => 'La humedad y el aire salino de Miami exigen diseños de portones que sean hermosos y duraderos. Aquí hay 7 estilos de portones de metal que prosperan en el Sur de Florida.',
        'Steel vs. Aluminum Gates: Which Is Right for Miami?' => 'Acero vs. Aluminio: ¿Cuál es el Material Correcto para Portones en Miami?',
        'Follow a custom metalwork project from the first design consultation to final installation at a Coral Gables estate.' => 'Sigue un proyecto de herrería personalizada desde la primera consulta de diseño hasta la instalación final en una residencia de Coral Gables.',
        'Inside a Miami Metalwork Project: From Design to Install' => 'Dentro de un Proyecto de Herrería en Miami: Del Diseño a la Instalación',
        'How to Maintain Metal Railings in South Florida\'s Salt Air' => 'Cómo Mantener Barandas Metálicas en el Aire Salino del Sur de Florida',
        'Salt air and humidity are tough on metal railings. Our guide shows you how to protect, clean, and extend the life of metal railings in South Florida.' => 'El aire salino y la humedad son difíciles para las barandas metálicas. Nuestra guía le muestra cómo proteger, limpiar y extender la vida de las barandas metálicas en el Sur de Florida.',
        '5 Ideas for Custom Metal Furniture That Transform Any Space' => '5 Ideas de Muebles de Metal Personalizados que Transforman Cualquier Espacio',
        'Metal Sculpture Commissioning: What to Expect' => 'Comisión de Esculturas de Metal: Qué Esperar',
        'Commissioning a custom metal sculpture is a collaborative journey. Learn the process from concept to completion at Thor Metal Art.' => 'Encargar una escultura de metal personalizada es un viaje colaborativo. Aprenda el proceso desde el concepto hasta la finalización en Thor Metal Art.',
        'Why Miami Architects Choose Custom Metalwork' => 'Por Qué los Arquitectos de Miami Eligen Herrería Personalizada',
        'Miami top architects incorporate custom metalwork into their designs for durability, aesthetics, and curb appeal. Here is why they call us.' => 'Los mejores arquitectos de Miami incorporan herrería personalizada en sus diseños por durabilidad, estética y atractivo visual. Aquí está por qué nos llaman.',
        'Water Jet Cutting vs. Plasma Cutting: A Fabricator\'s Guide' => 'Corte por Chorro de Agua vs. Corte por Plasma: Guía del Fabricante',
        'What is the difference between water jet and plasma cutting? Our guide helps you understand which method is best for your metal fabrication project.' => '¿Cuál es la diferencia entre el corte por chorro de agua y el corte por plasma? Nuestra guía le ayuda a entender qué método es mejor para su proyecto de fabricación de metal.',
        'How to Choose the Right Metal Fence for Your Miami Property' => 'Cómo Elegir la Cerca Metálica Correcta para su Propiedad en Miami',
        'Choosing the right metal fence for a Miami property means balancing security, style, and durability. Here is what to consider before you buy.' => 'Elegir la cerca metálica correcta para una propiedad en Miami significa equilibrar seguridad, estilo y durabilidad. Esto es lo que debe considerar antes de comprar.',
        'TIG Welding: The Art Behind Structural & Decorative Metalwork' => 'Soldadura TIG: El Arte detrás de la Herrería Estructural y Decorativa',
        'TIG welding is the gold standard for precision metal fabrication. Discover how this technique creates both structural strength and decorative beauty.' => 'La soldadura TIG es el estándar de oro para la fabricación de metal de precisión. Descubra cómo esta técnica crea tanto resistencia estructural como belleza decorativa.',
        'From Sketch to Steel: Our Custom Metal Gate Process' => 'Del Boceto al Acero: Nuestro Proceso de Portones de Metal Personalizados',
        'Every custom gate at Thor Metal Art starts with a conversation. Follow our process from initial sketch to installed steel masterpiece.' => 'Cada portón personalizado en Thor Metal Art comienza con una conversación. Siga nuestro proceso desde el boceto inicial hasta la obra maestra de acero instalada.',
        '1. Mediterranean Scroll Gates' => '1. Portones de Pergamino Mediterráneo',
        '2. Modern Horizontal Slat Gates' => '2. Portones de Lamas Horizontales Modernas',
        '3. Laser-Cut Privacy Gates' => '3. Portones de Privacidad Cortados con Láser',
        '4. Rustic Corten Steel Gates' => '4. Portones de Acero Corten Rústico',
        '5. Colonial Picket Gates' => '5. Portones de Estacas Coloniales',
        '6. Aluminum Pool Gates' => '6. Portones de Piscina de Aluminio',
        '7. Custom Artistic Gates' => '7. Portones Artísticos Personalizados',
        'Steel Gates: Strength and Versatility' => 'Portones de Acero: Resistencia y Versatilidad',
        'Aluminum Gates: Lightweight and Rust-Free' => 'Portones de Aluminio: Ligeros y Sin Óxido',
        'Our Recommendation' => 'Nuestra Recomendación',
        'Phase 1: Design Consultation' => 'Fase 1: Consulta de Diseño',
        'Phase 2: Shop Fabrication' => 'Fase 2: Fabricación en Taller',
        'Phase 3: Site Installation' => 'Fase 3: Instalación en el Sitio',
        'The Result' => 'El Resultado',
        'Understanding the Enemy: Salt Air Corrosion' => 'Entendiendo al Enemigo: Corrosión por Aire Salino',
        'Annual Maintenance Checklist' => 'Lista de Mantenimiento Anual',
        'Products That Work in South Florida' => 'Productos que Funcionan en el Sur de Florida',
        'When to Call a Professional' => 'Cuándo Llamar a un Profesional',
        '1. Floating Steel Shelving' => '1. Estantes de Acero Flotantes',
        '2. Custom Dining Tables with Metal Bases' => '2. Mesas de Comedor Personalizadas con Bases de Metal',
        '3. Outdoor Metal Furniture for South Florida Living' => '3. Muebles de Metal para Exteriores del Sur de Florida',
        '4. Metal Room Dividers and Privacy Screens' => '4. Divisores de Habitaciones y Pantallas de Privacidad de Metal',
        '5. Steel Bed Frames and Headboards' => '5. Marcos de Cama y Cabeceros de Acero',
        'Step 1: Concept and Vision' => 'Paso 1: Concepto y Visión',
        'Step 2: Proposal and Pricing' => 'Paso 2: Propuesta y Precios',
        'Step 3: Design Approval' => 'Paso 3: Aprobación del Diseño',
        'Step 4: Fabrication' => 'Paso 4: Fabricación',
        'Step 5: Delivery and Installation' => 'Paso 5: Entrega e Instalación',
        'Durability in a Demanding Climate' => 'Durabilidad en un Clima Exigente',
        'Design Flexibility Without Compromise' => 'Flexibilidad de Diseño Sin Compromisos',
        'Project Coordination from Shop to Site' => 'Coordinación de Proyectos del Taller al Sitio',
        'Featured Collaboration: Brickell Commercial Tower' => 'Colaboración Destacada: Torre Comercial de Brickell',
        'How Water Jet Cutting Works' => 'Cómo Funciona el Corte por Chorro de Agua',
        'How Plasma Cutting Works' => 'Cómo Funciona el Corte por Plasma',
        'Side-by-Side Comparison' => 'Comparación Lado a Lado',
        'What We Use at Thor Metal Art' => 'Qué Usamos en Thor Metal Art',
        '1. Purpose: Security, Privacy, or Aesthetics?' => '1. Propósito: ¿Seguridad, Privacidad o Estética?',
        '2. Material: Steel, Aluminum, or Wrought Iron?' => '2. Material: ¿Acero, Aluminio o Hierro Forjado?',
        '3. HOA Regulations and Local Codes' => '3. Regulaciones de HOA y Códigos Locales',
        '4. Wind Load and Hurricane Rating' => '4. Carga de Viento y Clasificación Huracanes',
        '5. Finish and Maintenance Requirements' => '5. Requisitos de Acabado y Mantenimiento',
        'What Makes TIG Welding Different?' => '¿Qué Hace Diferente a la Soldadura TIG?',
        'Why TIG for Decorative Metalwork?' => '¿Por Qué TIG para Herrería Decorativa?',
        'TIG for Structural Applications' => 'TIG para Aplicaciones Estructurales',
        'Materials We TIG Weld' => 'Materiales que Soldamos con TIG',
        'Phase 1: The Initial Consultation' => 'Fase 1: La Consulta Inicial',
        'Phase 2: Design and Proposal' => 'Fase 2: Diseño y Propuesta',
        'Phase 3: From Sketch to Steel' => 'Fase 3: Del Boceto al Acero',
        'Phase 4: Finishing' => 'Fase 4: Acabado',
        'Phase 5: Installation' => 'Fase 5: Instalación',
    );

    foreach ($new_translations as $original => $translated) {
        $orig_id = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM `{$orig_table}` WHERE original = %s LIMIT 1",
            $original
        ));
        if (! $orig_id) {
            $wpdb->insert($orig_table, array('original' => $original), array('%s'));
            $orig_id = (int) $wpdb->insert_id;
        }
        if (! $orig_id) {
            continue;
        }
        $existing_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM `{$dict_table}` WHERE original_id = %d LIMIT 1",
            $orig_id
        ));
        if ($existing_id) {
            $wpdb->update(
                $dict_table,
                array('translated' => $translated, 'status' => 2),
                array('id' => $existing_id),
                array('%s', '%d'),
                array('%d')
            );
        } else {
            $wpdb->insert($dict_table, array(
                'original'    => $original,
                'translated'  => $translated,
                'status'      => 2,
                'block_type'  => 0,
                'original_id' => $orig_id,
            ), array('%s', '%s', '%d', '%d', '%d'));
        }
    }

    update_option('tma_blog_translations_v1', true);
}
add_action('init', 'tma_provision_blog_translations_once');

/**
 * V2: Translations for blog post body paragraphs (posts 81–91),
 * blog listing excerpts/dates, short action strings, and encoding fixes.
 */
function tma_provision_blog_translations_v2()
{
    if (get_option('tma_blog_translations_v2')) {
        return;
    }

    global $wpdb;
    $orig_table = $wpdb->prefix . 'trp_original_strings';
    $dict_table = $wpdb->prefix . 'trp_dictionary_en_us_es_es';

    // Helper: upsert a translation by original_id
    $upsert_by_id = function ( $orig_id, $translated ) use ( $wpdb, $dict_table, $orig_table ) {
        $existing = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM `{$dict_table}` WHERE original_id = %d LIMIT 1", $orig_id
        ) );
        if ( $existing ) {
            $wpdb->update( $dict_table, [ 'translated' => $translated, 'status' => 2 ], [ 'id' => $existing ], [ '%s', '%d' ], [ '%d' ] );
        } else {
            $orig_text = $wpdb->get_var( $wpdb->prepare( "SELECT original FROM `{$orig_table}` WHERE id = %d LIMIT 1", $orig_id ) );
            if ( $orig_text ) {
                $wpdb->insert( $dict_table, [ 'original' => $orig_text, 'translated' => $translated, 'status' => 2, 'block_type' => 0, 'original_id' => $orig_id ], [ '%s', '%s', '%d', '%d', '%d' ] );
            }
        }
    };

    // Helper: upsert a translation by matching original string text
    $upsert_by_text = function ( $original, $translated ) use ( $wpdb, $orig_table, $dict_table ) {
        $orig_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM `{$orig_table}` WHERE original = %s LIMIT 1", $original ) );
        if ( ! $orig_id ) {
            $wpdb->insert( $orig_table, [ 'original' => $original ], [ '%s' ] );
            $orig_id = (int) $wpdb->insert_id;
        }
        if ( ! $orig_id ) {
            return;
        }
        $existing = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM `{$dict_table}` WHERE original_id = %d LIMIT 1", $orig_id ) );
        if ( $existing ) {
            $wpdb->update( $dict_table, [ 'translated' => $translated, 'status' => 2 ], [ 'id' => $existing ], [ '%s', '%d' ], [ '%d' ] );
        } else {
            $wpdb->insert( $dict_table, [ 'original' => $original, 'translated' => $translated, 'status' => 2, 'block_type' => 0, 'original_id' => $orig_id ], [ '%s', '%s', '%d', '%d', '%d' ] );
        }
    };

    // ─── Part A: Homepage utility strings (IDs 291–293) ────────────────────
    $upsert_by_id( 291, 'Somos un estudio de fabricación de metal personalizada en Miami. Cada pieza que realizamos — desde una reja residencial hasta una instalación escultural — se diseña y fabrica internamente con el mismo nivel de cuidado.' );
    $upsert_by_id( 292, 'Fundado por Karel Frometa, Thor Metal Art combina la tecnología de corte por chorro de agua con el arte de la soldadura tradicional para entregar trabajos que duran décadas.' );
    $upsert_by_id( 293, 'Obtenga un estimado gratis. Sin compromiso, sin presión. Cuéntenos qué necesita y le responderemos en 24 horas.' );

    // ─── Part B: Blog listing page — subtitle, excerpts, dates, "Read more" ─
    $upsert_by_id( 294, 'Consejos, perspectivas e historias del taller de Thor Metal Art en Miami.' );
    $upsert_by_id( 295, '30 mar. 2026' );
    $upsert_by_id( 296, 'Cada portón personalizado en Thor Metal Art comienza con una conversación. Siga nuestro proceso desde el boceto inicial hasta la obra maestra de acero instalada…' );
    $upsert_by_id( 297, 'Leer más →' );
    $upsert_by_id( 298, '23 mar. 2026' );
    $upsert_by_id( 299, 'La soldadura TIG es el estándar de oro para la fabricación de metal de precisión. Descubra cómo esta técnica crea resistencia estructural y belleza decorativa…' );
    $upsert_by_id( 300, '16 mar. 2026' );
    $upsert_by_id( 301, 'Elegir la cerca metálica correcta para una propiedad en Miami significa equilibrar seguridad, estilo y durabilidad. Esto es lo que debe considerar…' );
    $upsert_by_id( 302, '9 mar. 2026' );
    $upsert_by_id( 303, '¿Cuál es la diferencia entre el corte por chorro de agua y el corte por plasma? Nuestra guía le ayuda a entender qué método es mejor para…' );
    $upsert_by_id( 304, '2 mar. 2026' );
    $upsert_by_id( 305, 'Los mejores arquitectos de Miami incorporan herrería personalizada en sus diseños por durabilidad, estética y atractivo visual. Aquí está por qué nos llaman…' );
    $upsert_by_id( 306, '23 feb. 2026' );
    $upsert_by_id( 307, '16 feb. 2026' );
    $upsert_by_id( 308, 'Los muebles de metal personalizados no son solo funcionales — son arte. Descubra 5 ideas para mesas, estantes y piezas de acento…' );
    $upsert_by_id( 309, '9 feb. 2026' );
    $upsert_by_id( 310, 'Cómo Mantener Barandas Metálicas en el Aire Salino del Sur de Florida' );
    $upsert_by_id( 311, 'El aire salino y la humedad son difíciles para las barandas metálicas. Nuestra guía le muestra cómo proteger, limpiar y extender la vida…' );
    $upsert_by_id( 312, '2 feb. 2026' );
    $upsert_by_id( 313, '19 ene. 2026' );
    $upsert_by_id( 314, '¿Acero o aluminio? ¿Qué metal funciona mejor para portones en Miami? Comparamos durabilidad, costo, mantenimiento y estética para ayudarle…' );
    $upsert_by_id( 315, '12 de enero de 2026' );
    $upsert_by_id( 316, '7 Estilos de Portones de Metal que Funcionan en el Clima de Miami' );

    // ─── Part C: Post 81 — 7 Metal Gate Styles (IDs 317–327) ───────────────
    $upsert_by_id( 317, 'Miami es conocida por su atrevida arquitectura, exuberantes jardines y vida al aire libre durante todo el año. Cuando se trata de portones de metal, el estilo correcto debe equilibrar la estética con las exigencias de un clima tropical con aire salino. Aquí hay 7 estilos de portones de metal probados que lucen geniales y aguantan perfectamente en el Sur de Florida.' );
    $upsert_by_id( 318, 'Los pergaminos ornamentales clásicos con acero pintado en polvo son una constante en Miami-Dade. Las intrincadas curvas agregan elegancia a las casas de estilo mediterráneo en Coral Gables y Coconut Grove. La clave es un acabado de grado marino que resiste la corrosión salina.' );
    $upsert_by_id( 319, 'Las lamas horizontales limpias de acero o aluminio son favoritas en la arquitectura moderna de Miami Beach y Brickell. Este estilo permite la circulación del aire — importante en una región propensa a huracanes — mientras mantiene la privacidad y la seguridad.' );
    $upsert_by_id( 320, 'Los patrones cortados con láser personalizados transforman un portón en una pieza destacada. Los motivos populares incluyen follaje tropical, patrones geométricos y arte abstracto. El corte por chorro de agua garantiza precisión en cualquier diseño que pueda imaginar.' );
    $upsert_by_id( 321, 'El acero Corten (acero resistente a la intemperie) desarrolla una pátina de óxido natural que estabiliza y protege el metal. Este aspecto industrial contrasta hermosamente con el paisajismo tropical y requiere un mantenimiento mínimo en el clima de Miami.' );
    $upsert_by_id( 322, 'Los portones de estilo de estacas tradicionales funcionan bien para propiedades residenciales más pequeñas en todo Miami-Dade. Simples, duraderos y atemporales — se combinan bien con fachadas de estuco y jardines tropicales.' );
    $upsert_by_id( 323, 'El código de construcción de Florida exige que los portones de piscina se cierren y traben automáticamente. El aluminio es el material preferido: no se oxida, pesa menos que el acero y cumple todos los códigos de seguridad de Florida sin recubrimientos adicionales.' );
    $upsert_by_id( 324, 'Para los clientes que desean una entrada única, creamos portones artísticos completamente personalizados. Desde escultura soldada abstracta hasta fachadas funcionales que también son instalaciones de arte, estos portones se convierten en el elemento distintivo de cualquier propiedad.' );
    $upsert_by_id( 325, '¿No está seguro qué estilo se adapta a su propiedad?' );
    $upsert_by_id( 326, 'Programe una consulta gratuita' );
    $upsert_by_id( 327, 'con nuestro equipo de diseño — atendemos todo el condado de Miami-Dade.' );

    // ─── Part D: Post 82 — Steel vs Aluminum (IDs 328–345) ─────────────────
    $upsert_by_id( 328, '19 de enero de 2026' );
    $upsert_by_id( 329, 'Elegir entre acero y aluminio para su portón de Miami es una de las primeras decisiones que enfrentará. Ambos materiales tienen ventajas reales, y la elección correcta depende de su presupuesto, preferencias de diseño y cuánto mantenimiento está dispuesto a asumir. Así se comparan.' );
    $upsert_by_id( 330, 'El acero es el material más común para portones decorativos y de seguridad. Es resistente, soldable en formas complejas y rentable para instalaciones grandes o pesadas. En Miami, siempre aplicamos un imprimador de fosfato de zinc y una pintura en polvo de grado marino para proteger contra el óxido — algo innegociable en un ambiente con aire salino.' );
    $upsert_by_id( 331, 'Ventajas:' );
    $upsert_by_id( 332, 'Máxima flexibilidad de diseño, resistencia superior, menor costo inicial' );
    $upsert_by_id( 333, 'Desventajas:' );
    $upsert_by_id( 334, 'Más pesado (requiere postes y herrajes más robustos), puede oxidarse si el acabado se daña' );
    $upsert_by_id( 335, 'Ideal para:' );
    $upsert_by_id( 336, 'Portones de entrada grandes, instalaciones de seguridad, diseños ornamentales' );
    $upsert_by_id( 337, 'El aluminio no se oxida — nunca. Forma naturalmente una capa de óxido protectora que resiste la corrosión incluso en el aire costero de Miami. Es más ligero que el acero, lo que lo hace ideal para portones automatizados donde el esfuerzo del motor importa. La desventaja es que el aluminio no es tan resistente como el acero y no se puede soldar en tantas formas ornamentales intrincadas.' );
    $upsert_by_id( 338, 'Sin óxido de por vida, ligero, bajo mantenimiento, ideal para portones de piscina' );
    $upsert_by_id( 339, 'Menor resistencia estructural, mayor costo del material, opciones de diseño limitadas' );
    $upsert_by_id( 340, 'Cercados de piscina, propiedades costeras, portones corredizos automatizados' );
    $upsert_by_id( 341, 'Comparación de Costos en Miami' );
    $upsert_by_id( 342, 'Para un portón de entrada doble comparable, el aluminio típicamente cuesta un 20–30% más que el acero debido a los precios del material. Sin embargo, el aluminio ahorra dinero a largo plazo: sin repintado, sin tratamiento contra óxido y mantenimiento mínimo durante más de 30 años de vida útil.' );
    $upsert_by_id( 343, 'Para la mayoría de las propiedades residenciales en Miami, el acero correctamente acabado ofrece la mejor relación calidad-precio. Para propiedades frente al océano o canales — o en cualquier lugar a menos de dos millas del agua salada — el aluminio o el acero inoxidable vale el costo adicional.' );
    $upsert_by_id( 344, '¿Tiene preguntas? Nuestro equipo puede ayudarle a elegir el material correcto para su ubicación específica y presupuesto.' );
    $upsert_by_id( 345, 'Solicite un presupuesto gratis hoy.' );

    // ─── Part E: Post 83 — Miami Metalwork Project (IDs 346–359) ───────────
    $upsert_by_id( 346, '2 de febrero de 2026' );
    $upsert_by_id( 347, 'Cada proyecto en Thor Metal Art sigue un proceso probado — desde esa primera llamada telefónica hasta el último perno apretado en su nuevo portón o baranda. Este artículo le lleva dentro de un reciente proyecto en una residencia de Coral Gables: un portón de entrada de aluminio personalizado con intercomunicador integrado, flanqueado por paneles de privacidad a juego.' );
    $upsert_by_id( 348, 'El proyecto comenzó con una visita al sitio. Karel Frometa se reunió personalmente con el propietario para evaluar la apertura existente del camino de entrada (22 pies), discutir el estilo arquitectónico (Revival Mediterráneo) y revisar las opciones de materiales. El cliente quería un portón de aspecto ornamentado pero que requiriera un mantenimiento mínimo — el caso de uso perfecto para aluminio con recubrimiento en polvo color bronce.' );
    $upsert_by_id( 349, 'Después de la consulta, creamos 3 conceptos de diseño digital en 2D. El cliente seleccionó un diseño de pergamino modificado con un escudo familiar personalizado como pieza central. La aprobación tomó dos rondas de revisiones antes de que el proyecto pasara a fabricación.' );
    $upsert_by_id( 350, 'La fabricación tuvo lugar en nuestro taller de Hialeah durante 18 días hábiles. El proceso involucró:' );
    $upsert_by_id( 351, 'Corte por plasma CNC de los paneles principales del portón' );
    $upsert_by_id( 352, 'Corte por chorro de agua del escudo ornamental (se requiere precisión para el fino detalle)' );
    $upsert_by_id( 353, 'Soldadura TIG de todas las uniones estructurales' );
    $upsert_by_id( 354, 'Esmerilado, acabado y arenado' );
    $upsert_by_id( 355, 'Aplicación de imprimador de zinc y curado del recubrimiento en polvo de bronce' );
    $upsert_by_id( 356, 'La instalación requirió una visita al sitio de dos días. Primer día: cimientos de concreto para los postes del portón y conducto para el cableado eléctrico. Segundo día: colocación de postes, colgado del portón, instalación de herrajes y programación del motor. El intercomunicador se conectó al sistema de automatización del hogar existente.' );
    $upsert_by_id( 357, 'El portón terminado se convirtió en el punto focal de la propiedad. El propietario comentó que tres vecinos le pidieron nuestra tarjeta dentro de la primera semana. Esa es la mejor reseña que podemos recibir.' );
    $upsert_by_id( 358, '¿Quiere ver lo que podemos crear para su propiedad?' );
    $upsert_by_id( 359, 'Comience con una consulta gratuita.' );

    // ─── Part F: Post 84 — Maintain Metal Railings (IDs 360–376) ───────────
    $upsert_by_id( 360, '9 de febrero de 2026' );
    $upsert_by_id( 361, 'El Sur de Florida es uno de los ambientes más severos para el metal en los Estados Unidos. La combinación de aire salino, alta humedad, intensa radiación UV y lluvias frecuentes crea condiciones que aceleran la corrosión en la herrería sin protección. Aquí se explica cómo mantener sus barandas metálicas impecables y estructuralmente sólidas durante décadas.' );
    $upsert_by_id( 362, 'Las partículas de sal del océano viajan millas tierra adentro. En Miami-Dade, cualquier propiedad a menos de 10 millas de la costa tiene un riesgo de corrosión elevado. Los depósitos de sal en las superficies metálicas atraen la humedad, lo que acelera la oxidación. El resultado: óxido que, si no se trata, comprometerá la integridad estructural de sus barandas en pocos años.' );
    $upsert_by_id( 363, 'Inspección visual (mensual):' );
    $upsert_by_id( 364, 'Busque burbujas de pintura, manchas de óxido o soldaduras sueltas. Detecte los problemas a tiempo.' );
    $upsert_by_id( 365, 'Enjuague con agua dulce (mensual):' );
    $upsert_by_id( 366, 'Use una manguera de jardín para enjuagar los depósitos de sal de todas las superficies metálicas. Este único hábito extiende dramáticamente la vida del acabado.' );
    $upsert_by_id( 367, 'Lave con jabón suave (trimestral):' );
    $upsert_by_id( 368, 'Un cepillo suave y jabón lavavajillas diluido elimina la suciedad y los depósitos de sal. Enjuague bien y seque con un paño.' );
    $upsert_by_id( 369, 'Inspeccione la pintura y el recubrimiento (anual):' );
    $upsert_by_id( 370, 'Cualquier astilla o arañazo debe retocarse de inmediato. Use pintura de retoque del recubrimiento en polvo a juego o imprimador inhibidor del óxido.' );
    $upsert_by_id( 371, 'Acabado profesional (cada 5–10 años):' );
    $upsert_by_id( 372, 'El arenado completo y el nuevo recubrimiento restauran las barandas a condición de nuevas.' );
    $upsert_by_id( 373, 'No todos los inhibidores de óxido son iguales. Para las barandas de Miami, recomendamos productos calificados para ambientes marinos: Rust-Oleum Marine Coatings, Corroseal o Tremclad Rust Paint. Para retoques, use un convertidor de óxido antes de pintar sobre cualquier metal expuesto.' );
    $upsert_by_id( 374, 'Si ve óxido extendiéndose bajo el recubrimiento superficial, grietas estructurales en las soldaduras, o secciones donde el metal se ha adelgazado, es hora de llamar a un fabricante. El óxido superficial es manejable. El óxido estructural es un problema de seguridad.' );
    $upsert_by_id( 375, 'Thor Metal Art ofrece servicios de inspección y refinishado de barandas en todo Miami-Dade.' );
    $upsert_by_id( 376, 'Programe su inspección hoy.' );

    // ─── Part G: Post 85 — Custom Metal Furniture (IDs 377–386) ───────────
    $upsert_by_id( 377, '16 de febrero de 2026' );
    $upsert_by_id( 378, 'Los muebles de metal personalizados están viviendo un momento especial en el diseño de interiores y exteriores. Desde mesas de centro minimalistas hasta unidades de estantería escultóricas, las piezas de acero y hierro aportan una elegancia industrial que ningún otro material iguala. Aquí hay cinco ideas para inspirar su próximo proyecto.' );
    $upsert_by_id( 379, 'Los estantes de acero montados en la pared con acabado natural o pintado en polvo son una declaración audaz en cualquier cocina, sala de estar u oficina en casa. Fabricamos soportes y estantes personalizados en cualquier tamaño, incluidas tapas de madera de canto vivo sobre bases de acero para un cálido aspecto industrial.' );
    $upsert_by_id( 380, 'Una base de mesa de acero o hierro soldado puede diseñarse prácticamente en cualquier forma: patas de horquilla, pedestal, estructura en X o escultura completamente personalizada. Combinado con un tablero de vidrio, madera o mármol, una base de metal personalizada transforma cualquier comedor en una declaración de diseño.' );
    $upsert_by_id( 381, 'La vida al aire libre en Miami exige muebles que soporten el sol, la lluvia y el aire salino. Fabricamos conjuntos de comedor para exteriores, tumbonas y mesas auxiliares en aluminio pintado en polvo o acero galvanizado — materiales que superan a los muebles de patio estándar en durabilidad por décadas.' );
    $upsert_by_id( 382, 'Las pantallas de metal cortadas con láser crean privacidad e interés decorativo en espacios de planta abierta. Patrones tropicales, diseños geométricos o ilustraciones personalizadas — estas piezas funcionan tanto en interiores como en exteriores y se convierten en tema de conversación en cualquier ambiente.' );
    $upsert_by_id( 383, 'Un armazón de cama soldado personalizado es una inversión de por vida. Diseñamos armazones en acero minimalista, hierro ornamental o cualquier estilo intermedio. A diferencia de los muebles fabricados en serie, los nuestros se construyen según las dimensiones exactas de su colchón y habitación.' );
    $upsert_by_id( 384, '¿Listo para encargar una pieza de mueble de metal personalizado?' );
    $upsert_by_id( 385, 'Contáctenos' );
    $upsert_by_id( 386, '— diseñamos y fabricamos en todo el condado de Miami-Dade.' );

    // ─── Part H: Post 86 — Metal Sculpture Commissioning (IDs 387–397) ────
    $upsert_by_id( 387, '23 de febrero de 2026' );
    $upsert_by_id( 388, 'Encargar una escultura de metal personalizada es una de las decisiones creativas más emocionantes — y a veces más intimidantes — que puede tomar. Ya sea que sea un propietario, una empresa o una institución pública, entender el proceso de encargo elimina el misterio y hace que la colaboración sea más agradable.' );
    $upsert_by_id( 389, 'El proceso comienza con su visión. No necesita llegar con especificaciones técnicas — solo una idea. ¿Qué emoción quiere que evoque la pieza? ¿Dónde estará ubicada? ¿Cuál es el presupuesto aproximado? Incluso un boceto aproximado o una foto de un trabajo que admira nos da un punto de partida.' );
    $upsert_by_id( 390, 'Durante la consulta inicial, discutimos materiales (acero inoxidable, Corten, bronce, acero pintado), escala y ubicación. Las esculturas al aire libre requieren consideración para la intemperie; las piezas de interior pueden usar acabados más delicados.' );
    $upsert_by_id( 391, 'Después de la consulta, preparamos una propuesta escrita con conceptos de diseño, especificaciones de materiales, cronograma y precio. Las comisiones de escultura van desde $2,500 para piezas de acento pequeñas hasta más de $50,000 para instalaciones a gran escala. Los precios son transparentes — usted sabe exactamente qué está pagando.' );
    $upsert_by_id( 392, 'Creamos dibujos 2D detallados o renderizados 3D para su aprobación. Esta es la fase colaborativa — sus comentarios dan forma a la pieza final. No pasamos a fabricación hasta que esté completamente satisfecho con el diseño.' );
    $upsert_by_id( 393, 'La fabricación tiene lugar en nuestro estudio de Hialeah. Para comisiones más grandes, proporcionamos fotos del progreso en hitos clave. Esta transparencia es algo que nuestros clientes aprecian constantemente.' );
    $upsert_by_id( 394, 'Nos encargamos de la entrega e instalación en todo Miami-Dade. Para esculturas al aire libre de gran tamaño, la instalación incluye trabajo de cimentación y anclaje estructural según los códigos de construcción locales.' );
    $upsert_by_id( 395, '¿Interesado en encargar una pieza?' );
    $upsert_by_id( 396, 'Contáctenos para comenzar la conversación' );
    $upsert_by_id( 397, '— sin compromiso para la consulta inicial.' );

    // ─── Part I: Post 87 — Miami Architects (IDs 399–404, skip 398) ────────
    $upsert_by_id( 399, 'Miami es una de las ciudades arquitectónicamente más diversas de los Estados Unidos. Desde el Art Deco hasta las ultramodernas torres de vidrio, el entorno construido aquí exige materiales que sean tanto hermosos como resistentes. La herrería personalizada se ha convertido en un elemento distintivo en proyectos de los mejores arquitectos de Miami — y con razón.' );
    $upsert_by_id( 400, 'La tecnología de fabricación moderna — corte por plasma CNC, chorro de agua, soldadura TIG y recubrimiento en polvo — permite a los arquitectos realizar diseños que simplemente no se pueden lograr en madera, concreto o materiales prefabricados. Curvas, perforaciones personalizadas, canales de iluminación integrados y ensamblajes de múltiples materiales son todos posibles cuando se trabaja con un fabricante personalizado experto.' );
    $upsert_by_id( 401, 'Los arquitectos valoran a los fabricantes que entienden la gestión de proyectos de construcción. En Thor Metal Art, trabajamos directamente con contratistas generales, gerentes de proyecto y supervisores de sitio para cumplir con los requisitos del cronograma. Los dibujos de taller, envíos y certificaciones de materiales se entregan a tiempo — algo innegociable para grandes proyectos comerciales.' );
    $upsert_by_id( 402, 'Un proyecto reciente involucró el diseño y fabricación de 400 pies lineales de sistema de barandas de vidrio y acero para el vestíbulo de una torre comercial en Brickell. El resultado: un sistema uniforme que cumplió tanto con las especificaciones del ingeniero estructural como con la visión del arquitecto principal para el espacio.' );
    $upsert_by_id( 403, 'Arquitectos y diseñadores: damos la bienvenida a consultas de proyectos y colaboración en la fase de preconstrucción.' );
    $upsert_by_id( 404, 'Contáctenos para discutir su próximo proyecto.' );

    // ─── Part J: Post 88 — Water Jet vs Plasma (IDs 405–422) ───────────────
    $upsert_by_id( 405, 'Cuando se trata de corte de metal de precisión en fabricación, dos tecnologías dominan: el corte por chorro de agua y el corte por plasma. Cada una tiene fortalezas distintas, y elegir la correcta puede afectar significativamente la calidad, el costo y el cronograma de su proyecto. Aquí hay una guía práctica desde la perspectiva de un fabricante.' );
    $upsert_by_id( 406, 'El corte por chorro de agua utiliza un chorro de agua a alta presión — a veces mezclado con granate abrasivo — para cortar metal. La presión puede alcanzar 60,000 PSI, lo que corta acero, aluminio, acero inoxidable e incluso compuestos con extrema precisión. Fundamentalmente, el chorro de agua es un proceso de corte en frío: sin zona afectada por el calor (ZAC), lo que significa que las propiedades del metal se conservan en el borde cortado.' );
    $upsert_by_id( 407, 'El corte por plasma utiliza un chorro de gas ionizado (plasma) a alta velocidad para fundir y expulsar el metal en la línea de corte. Es más rápido que el chorro de agua para acero grueso y más rentable para cortes de gran volumen. Sin embargo, produce una zona afectada por el calor a lo largo del borde, lo que puede afectar la dureza y la calidad del acabado.' );
    $upsert_by_id( 408, 'Precisión:' );
    $upsert_by_id( 409, 'Chorro de agua gana — tolerancias de ±0.001". Plasma es típicamente ±0.02".' );
    $upsert_by_id( 410, 'Velocidad:' );
    $upsert_by_id( 411, 'El plasma corta más rápido en acero grueso (>1/2"). El chorro de agua es más lento pero más preciso.' );
    $upsert_by_id( 412, 'Materiales:' );
    $upsert_by_id( 413, 'El chorro de agua corta casi cualquier cosa. El plasma se limita a metales conductores.' );
    $upsert_by_id( 414, 'Calor:' );
    $upsert_by_id( 415, 'El chorro de agua no produce distorsión térmica. El plasma crea una ZAC.' );
    $upsert_by_id( 416, 'Costo:' );
    $upsert_by_id( 417, 'El corte por plasma es generalmente más barato por pie lineal.' );
    $upsert_by_id( 418, 'Chorro de agua = detalles ornamentales, patrones precisos. Plasma = cortes estructurales, láminas grandes.' );
    $upsert_by_id( 419, 'Usamos ambas tecnologías según el proyecto. Los portones personalizados con patrones ornamentales cortados con láser pasan por nuestro chorro de agua. El acero estructural para barandas y soportes se corta por plasma, luego se esmerila y termina según especificaciones. La herramienta correcta para el trabajo correcto produce mejores resultados a mejor precio.' );
    $upsert_by_id( 420, '¿Tiene un proyecto en mente?' );
    $upsert_by_id( 421, 'Hable con nuestro equipo' );
    $upsert_by_id( 422, 'sobre qué método de corte es mejor para su diseño y presupuesto.' );

    // ─── Part K: Post 89 — Right Metal Fence (IDs 423–431) ─────────────────
    $upsert_by_id( 423, 'Una cerca metálica es una inversión a largo plazo que puede definir el carácter de su propiedad durante décadas. En Miami, donde el clima y la estética juegan roles importantes, elegir la cerca correcta requiere más reflexión que simplemente elegir un estilo de un catálogo. Esto es lo que debe considerar antes de comprometerse.' );
    $upsert_by_id( 424, 'Defina el objetivo principal de su cerca. Una cerca de seguridad para una propiedad comercial tiene requisitos muy diferentes a una cerca decorativa para un jardín en Coral Gables. Las cercas de seguridad priorizan la altura, características anti-escalada y resistencia estructural. Las cercas decorativas priorizan el diseño, el acabado y el atractivo visual.' );
    $upsert_by_id( 425, 'Cada material se destaca en diferentes situaciones. El acero ofrece resistencia y flexibilidad de diseño. El aluminio nunca se oxida y es ideal para cercas de piscina y propiedades costeras. El hierro forjado proporciona una estética clásica pero requiere mantenimiento regular en la humedad de Miami. Para la mayoría de las aplicaciones residenciales en Miami-Dade, el aluminio pintado en polvo o el acero con acabado marino son las opciones prácticas.' );
    $upsert_by_id( 426, 'El condado de Miami-Dade y la mayoría de las HOA tienen reglas específicas sobre la altura, el estilo y los materiales de las cercas. Antes de ordenar cualquier cosa, consulte con su HOA y revise la Sección 33-11.5 del Código del Condado de Miami-Dade sobre permisos de cercas. Gestionamos las solicitudes de permisos como parte de nuestro servicio de instalación.' );
    $upsert_by_id( 427, 'Miami está en una Zona de Huracanes de Alta Velocidad (HVHZ). Cualquier cerca que supere cierta altura requiere dibujos de ingeniería y un cálculo de carga de viento. Diseñamos todas nuestras cercas para cumplir o superar los requisitos del Código de Construcción de Florida — su permiso no será rechazado por deficiencias estructurales.' );
    $upsert_by_id( 428, 'Un acabado de pintura en polvo de grado marino en acero agrega 15–20 años de protección contra la corrosión. El aluminio solo necesita lavado periódico. Tenga en cuenta los requisitos de mantenimiento en su presupuesto — una cerca ligeramente más costosa y de bajo mantenimiento a menudo cuesta menos durante 10 años que una opción más barata de alto mantenimiento.' );
    $upsert_by_id( 429, '¿No sabe por dónde empezar?' );
    $upsert_by_id( 430, 'Programe una evaluación gratuita del sitio' );
    $upsert_by_id( 431, 'y le guiaremos a través de todo el proceso.' );

    // ─── Part L: Post 90 — TIG Welding (IDs 432–441) ───────────────────────
    $upsert_by_id( 432, 'Si quiere entender qué separa la herrería excepcional de la fabricación ordinaria, mire las soldaduras. La soldadura TIG — Soldadura por Arco de Gas Tungsteno (GTAW) — es el proceso de soldadura de mayor precisión disponible, y es el método que usamos para todo el trabajo estructural y decorativo en Thor Metal Art.' );
    $upsert_by_id( 433, 'A diferencia de la soldadura MIG (que usa un electrodo de alambre consumible alimentado automáticamente) o la soldadura con electrodo, la soldadura TIG usa un electrodo de tungsteno no consumible para crear el arco. El soldador alimenta la varilla de relleno manualmente con la otra mano mientras controla con precisión la entrada de calor con un pedal de pie. Este nivel de control produce soldaduras más limpias, resistentes y refinadas estéticamente.' );
    $upsert_by_id( 434, 'Las piezas de metal decorativas — barandas, portones, esculturas, muebles — a menudo son visibles de cerca. Las soldaduras MIG, aunque rápidas y funcionales, típicamente requieren esmerilado y acabado para lucir presentables. Un soldador TIG hábil puede producir soldaduras en forma de pila de monedas que son en sí mismas parte de la estética terminada, requiriendo un post-procesamiento mínimo.' );
    $upsert_by_id( 435, 'La soldadura TIG también sobresale en aplicaciones estructurales donde la calidad de la soldadura es crítica. Las barandas de escaleras, los guardas de balcones y los marcos de carga se benefician de la penetración superior y la porosidad reducida que produce TIG en comparación con otros procesos. Cuando una baranda debe soportar el peso de una persona en una emergencia, la calidad de la soldadura importa enormemente.' );
    $upsert_by_id( 436, 'Acero dulce (portones, barandas, marcos)' );
    $upsert_by_id( 437, 'Acero inoxidable (equipamiento de cocina, aplicaciones marinas, arquitectura moderna)' );
    $upsert_by_id( 438, 'Aluminio (cercas de piscina, estructuras ligeras, señalización)' );
    $upsert_by_id( 439, 'Titanio y aleaciones exóticas (encargos especiales)' );
    $upsert_by_id( 440, 'Cuando contrata a Thor Metal Art, está contratando soldadores TIG certificados con la habilidad de ejecutar su visión con precisión y seguridad.' );
    $upsert_by_id( 441, 'Solicite un presupuesto hoy.' );

    // ─── Part M: Post 91 — From Sketch to Steel (IDs 442–450) ──────────────
    $upsert_by_id( 442, 'Cada portón de metal personalizado en Thor Metal Art comienza con una conversación y termina con una pieza de escultura funcional instalada con precisión. Si alguna vez se ha preguntado qué sucede entre su primera llamada y el día en que su nuevo portón está colgado en sus bisagras, este recorrido es para usted.' );
    $upsert_by_id( 443, 'Comenzamos con una visita al sitio. Karel Frometa se reúne personalmente con la mayoría de los clientes para evaluar la abertura, entender el contexto arquitectónico y discutir las preferencias de diseño. Sin compromiso — esta visita es gratuita. Tomamos medidas, fotografiamos el sitio y escuchamos. La mayoría de los clientes llegan con una idea general; nosotros la convertimos en algo construible.' );
    $upsert_by_id( 444, 'Dentro de una semana de la consulta, recibe una propuesta escrita que incluye bocetos de diseño, especificaciones de materiales y un precio fijo. No trabajamos con estimados abiertos — usted sabe exactamente qué está pagando antes de que comience cualquier trabajo. Si el diseño necesita ajustes, los hacemos sin costo adicional.' );
    $upsert_by_id( 445, 'Una vez que apruebe el diseño y la propuesta, pasamos a los dibujos de taller. Estos son documentos de ingeniería precisos que guían cada corte y soldadura. El material se adquiere localmente, se inspecciona a la entrega y se almacena en nuestro taller de fabricación de Hialeah.' );
    $upsert_by_id( 446, 'La fabricación implica corte, conformado, soldadura, esmerilado y acabado. La mayoría de los portones residenciales toman entre 10 y 20 días de taller según la complejidad. Proporcionamos fotos del progreso durante la fabricación para que no haya sorpresas en la entrega.' );
    $upsert_by_id( 447, 'Cada portón recibe un acabado de múltiples pasos: imprimador de fosfato de zinc para inhibición de la corrosión, luego una pintura en polvo de grado marino en el color que elija. Este acabado se hornea a 400°F para máxima adhesión y durabilidad — esencial para el clima de Miami.' );
    $upsert_by_id( 448, 'Nuestro equipo de instalación se encarga de todo: colocación de postes en concreto, colgado del portón, instalación de herrajes y ajuste final. Los portones automatizados incluyen programación del motor y una guía para el propietario. No consideramos un trabajo terminado hasta que esté completamente satisfecho.' );
    $upsert_by_id( 449, 'Contáctenos hoy' );
    $upsert_by_id( 450, 'para una consulta gratuita y visita al sitio.' );

    // ─── Part N: Full SEO title tag translations ────────────────────────────
    $title_translations = [
        'How Much Does a Custom Metal Gate Cost in Miami? | Thor Metal Art'
            => '¿Cuánto Cuesta una Puerta de Metal Personalizada en Miami? | Thor Metal Art',
        '7 Metal Gate Styles That Work in Miami\'s Climate | Thor Metal Art'
            => '7 Estilos de Portones de Metal que Funcionan en el Clima de Miami | Thor Metal Art',
        'Steel vs. Aluminum Gates: Which Is Right for Miami? | Thor Metal Art'
            => 'Acero vs. Aluminio: ¿Cuál es el Material Correcto para Portones en Miami? | Thor Metal Art',
        'Inside a Miami Metalwork Project: From Design to Install | Thor Metal Art'
            => 'Dentro de un Proyecto de Herrería en Miami: Del Diseño a la Instalación | Thor Metal Art',
        'How to Maintain Metal Railings in South Florida\'s Salt Air | Thor Metal Art'
            => 'Cómo Mantener Barandas Metálicas en el Aire Salino del Sur de Florida | Thor Metal Art',
        '5 Ideas for Custom Metal Furniture That Transform Any Space | Thor Metal Art'
            => '5 Ideas de Muebles de Metal Personalizados que Transforman Cualquier Espacio | Thor Metal Art',
        'Metal Sculpture Commissioning: What to Expect | Thor Metal Art'
            => 'Comisión de Esculturas de Metal: Qué Esperar | Thor Metal Art',
        'Why Miami Architects Choose Custom Metalwork | Thor Metal Art'
            => 'Por Qué los Arquitectos de Miami Eligen Herrería Personalizada | Thor Metal Art',
        'Water Jet Cutting vs. Plasma Cutting: A Fabricator\'s Guide | Thor Metal Art'
            => 'Corte por Chorro de Agua vs. Corte por Plasma: Guía del Fabricante | Thor Metal Art',
        'How to Choose the Right Metal Fence for Your Miami Property | Thor Metal Art'
            => 'Cómo Elegir la Cerca Metálica Correcta para su Propiedad en Miami | Thor Metal Art',
        'TIG Welding: The Art Behind Structural & Decorative Metalwork | Thor Metal Art'
            => 'Soldadura TIG: El Arte detrás de la Herrería Estructural y Decorativa | Thor Metal Art',
        'From Sketch to Steel: Our Custom Metal Gate Process | Thor Metal Art'
            => 'Del Boceto al Acero: Nuestro Proceso de Portones de Metal Personalizados | Thor Metal Art',
        'Blog | Thor Metal Art' => 'Blog | Thor Metal Art',
    ];
    foreach ( $title_translations as $orig => $trans ) {
        $upsert_by_text( $orig, $trans );
    }

    update_option( 'tma_blog_translations_v2', true );
}
add_action( 'init', 'tma_provision_blog_translations_v2' );

/**
 * v3: Re-provisions all body-paragraph translations using text-based matching.
 * Needed because v2 used hardcoded IDs that differ between DEV and PROD.
 * Safe to run: uses UPSERT by original text — idempotent.
 */
function tma_provision_blog_translations_v3() {
    if ( get_option( 'tma_blog_translations_v3' ) ) {
        return;
    }
    global $wpdb;
    $orig_table = $wpdb->prefix . 'trp_original_strings';
    $dict_table = $wpdb->prefix . 'trp_dictionary_en_us_es_es';

    $upsert = function ( $original, $translated ) use ( $wpdb, $orig_table, $dict_table ) {
        $orig_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM `{$orig_table}` WHERE original = %s LIMIT 1", $original ) );
        if ( ! $orig_id ) {
            $wpdb->insert( $orig_table, [ 'original' => $original, 'original_status' => 0, 'block_type' => 0, 'lang' => '' ], [ '%s', '%d', '%d', '%s' ] );
            $orig_id = (int) $wpdb->insert_id;
        }
        if ( ! $orig_id ) {
            return;
        }
        $existing = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM `{$dict_table}` WHERE original_id = %d LIMIT 1", $orig_id ) );
        if ( $existing ) {
            $wpdb->update( $dict_table, [ 'translated' => $translated, 'status' => 2 ], [ 'id' => $existing ], [ '%s', '%d' ], [ '%d' ] );
        } else {
            $wpdb->insert( $dict_table, [ 'original' => $original, 'translated' => $translated, 'status' => 2, 'block_type' => 0, 'original_id' => $orig_id ], [ '%s', '%s', '%d', '%d', '%d' ] );
        }
    };

    $pairs = [
        // ── Post 81 — 7 Metal Gate Styles ──────────────────────────────────
        '7 Metal Gate Styles That Work in Miami\'s Climate' => '7 Estilos de Portones de Metal que Funcionan en el Clima de Miami',
        'Miami is known for its bold architecture, lush gardens, and year-round outdoor living. When it comes to metal gates, the right style must balance aesthetics with the demands of a tropical, salt-air climate. Here are 7 proven metal gate styles that look great and hold up beautifully in South Florida.' => 'Miami es conocida por su atrevida arquitectura, exuberantes jardines y vida al aire libre durante todo el año. Cuando se trata de portones de metal, el estilo correcto debe equilibrar la estética con las exigencias de un clima tropical con aire salino. Aquí hay 7 estilos de portones de metal probados que lucen geniales y aguantan perfectamente en el Sur de Florida.',
        'Classic ornamental scrollwork with powder-coated steel is a staple in Miami-Dade. The intricate curves add elegance to Mediterranean-style homes in Coral Gables and Coconut Grove. The key is a marine-grade finish that resists salt corrosion.' => 'Los pergaminos ornamentales clásicos con acero pintado en polvo son una constante en Miami-Dade. Las intrincadas curvas agregan elegancia a las casas de estilo mediterráneo en Coral Gables y Coconut Grove. La clave es un acabado de grado marino que resiste la corrosión salina.',
        'Clean, horizontal steel or aluminum slats are a favorite for modern architecture in Miami Beach and Brickell. This style allows air circulation while maintaining privacy and security.' => 'Las lamas horizontales limpias de acero o aluminio son favoritas en la arquitectura moderna de Miami Beach y Brickell. Este estilo permite la circulación del aire — importante en una región propensa a huracanes — mientras mantiene la privacidad y la seguridad.',
        'Custom laser-cut patterns transform a gate into a statement piece. Popular motifs include tropical foliage, geometric patterns, and abstract art. Water jet cutting ensures precision on any design.' => 'Los patrones cortados con láser personalizados transforman un portón en una pieza destacada. Los motivos populares incluyen follaje tropical, patrones geométricos y arte abstracto. El corte por chorro de agua garantiza precisión en cualquier diseño que pueda imaginar.',
        'Corten (weathering steel) develops a natural rust patina that stabilizes and protects the metal. This industrial look contrasts beautifully with tropical landscaping.' => 'El acero Corten (acero resistente a la intemperie) desarrolla una pátina de óxido natural que estabiliza y protege el metal. Este aspecto industrial contrasta hermosamente con el paisajismo tropical y requiere un mantenimiento mínimo en el clima de Miami.',
        'Traditional picket-style gates work well for smaller residential properties. Simple, durable, and timeless.' => 'Los portones de estilo de estacas tradicionales funcionan bien para propiedades residenciales más pequeñas en todo Miami-Dade. Simples, duraderos y atemporales — se combinan bien con fachadas de estuco y jardines tropicales.',
        'Florida building code requires pool gates that are self-closing and self-latching. Aluminum is the preferred material: rust-free and meets all Florida safety codes.' => 'El código de construcción de Florida exige que los portones de piscina se cierren y traben automáticamente. El aluminio es el material preferido: no se oxida, pesa menos que el acero y cumple todos los códigos de seguridad de Florida sin recubrimientos adicionales.',
        'For clients who want a one-of-a-kind entrance, we create fully custom artistic gates â€" from abstract welded sculpture to functional facades that double as art installations.' => 'Para los clientes que desean una entrada única, creamos portones artísticos completamente personalizados — desde escultura soldada abstracta hasta fachadas funcionales que sirven como instalaciones de arte.',
        'Not sure which style suits your property?' => '¿No está seguro qué estilo se adapta a su propiedad?',
        'Schedule a free consultation' => 'Programe una consulta gratuita',
        'with our design team.' => 'con nuestro equipo de diseño — atendemos todo el condado de Miami-Dade.',
        // ── Post 82 — Steel vs Aluminum ─────────────────────────────────────
        'Steel vs. Aluminum Gates: Which Is Right for Miami?' => 'Acero vs. Aluminio: ¿Cuál es el Material Correcto para Portones en Miami?',
        'Choosing between steel and aluminum for your Miami gate is one of the first decisions you\'ll face. Both materials have real advantages, and the right choice depends on your budget, design preferences, and how much maintenance you\'re willing to handle. Here\'s how they compare.' => 'Elegir entre acero y aluminio para su portón de Miami es una de las primeras decisiones que enfrentará. Ambos materiales tienen ventajas reales, y la elección correcta depende de su presupuesto, preferencias de diseño y cuánto mantenimiento está dispuesto a asumir. Así se comparan.',
        'Steel is the most common material for decorative and security gates. It\'s strong, weldable into complex shapes, and cost-effective for large or heavy installations. In Miami, we always apply a zinc phosphate primer and marine-grade powder coating to protect against rust — non-negotiable in a salt-air environment.' => 'El acero es el material más común para portones decorativos y de seguridad. Es resistente, soldable en formas complejas y rentable para instalaciones grandes o pesadas. En Miami, siempre aplicamos un imprimador de fosfato de zinc y una pintura en polvo de grado marino para proteger contra el óxido — algo innegociable en un ambiente con aire salino.',
        'Pros:' => 'Ventajas:',
        'Maximum design flexibility, superior strength, lower upfront cost' => 'Máxima flexibilidad de diseño, resistencia superior, menor costo inicial',
        'Cons:' => 'Desventajas:',
        'Heavier (requires sturdier posts and hardware), can rust if finish is damaged' => 'Más pesado (requiere postes y herrajes más robustos), puede oxidarse si el acabado se daña',
        'Best for:' => 'Ideal para:',
        'Large entry gates, security installations, ornamental designs' => 'Portones de entrada grandes, instalaciones de seguridad, diseños ornamentales',
        'Aluminum doesn\'t rust — ever. It naturally forms a protective oxide layer that resists corrosion even in Miami\'s coastal air. It\'s lighter than steel, making it ideal for automated gates where motor strain matters. The downside is that aluminum isn\'t as strong as steel and can\'t be welded into as many intricate ornamental shapes.' => 'El aluminio no se oxida — nunca. Forma naturalmente una capa de óxido protectora que resiste la corrosión incluso en el aire costero de Miami. Es más ligero que el acero, lo que lo hace ideal para portones automatizados donde el esfuerzo del motor importa. La desventaja es que el aluminio no es tan resistente como el acero y no se puede soldar en tantas formas ornamentales intrincadas.',
        'No lifetime rust, lightweight, low maintenance, ideal for pool gates' => 'Sin óxido de por vida, ligero, bajo mantenimiento, ideal para portones de piscina',
        'Lower structural strength, higher material cost, limited design options' => 'Menor resistencia estructural, mayor costo del material, opciones de diseño limitadas',
        'Pool fencing, coastal properties, automated sliding gates' => 'Cercados de piscina, propiedades costeras, portones corredizos automatizados',
        'Cost Comparison in Miami' => 'Comparación de Costos en Miami',
        'For a comparable double entry gate, aluminum typically costs 20–30% more than steel due to material pricing. However, aluminum saves money long-term: no repainting, no rust treatment, and minimal maintenance over a 30+ year lifespan.' => 'Para un portón de entrada doble comparable, el aluminio típicamente cuesta un 20–30% más que el acero debido a los precios del material. Sin embargo, el aluminio ahorra dinero a largo plazo: sin repintado, sin tratamiento contra óxido y mantenimiento mínimo durante más de 30 años de vida útil.',
        'For most residential properties in Miami, properly finished steel offers the best value. For oceanfront or canal properties — or anywhere within two miles of saltwater — aluminum or stainless steel is worth the extra cost.' => 'Para la mayoría de las propiedades residenciales en Miami, el acero correctamente acabado ofrece la mejor relación calidad-precio. Para propiedades frente al océano o canales — o en cualquier lugar a menos de dos millas del agua salada — el aluminio o el acero inoxidable vale el costo adicional.',
        'Have questions? Our team can help you choose the right material for your specific location and budget.' => '¿Tiene preguntas? Nuestro equipo puede ayudarle a elegir el material correcto para su ubicación específica y presupuesto.',
        'Request a free quote today.' => 'Solicite un presupuesto gratis hoy.',
        // ── Post 83 — Miami Metalwork Project ───────────────────────────────
        'Inside a Miami Metalwork Project: From Design to Install' => 'Dentro de un Proyecto de Herrería en Miami: Del Diseño a la Instalación',
        'Every project at Thor Metal Art follows a proven process — from that first phone call to the last bolt tightened on your new gate or railing. This article takes you inside a recent project at a Coral Gables residence: a custom aluminum entry gate with integrated intercom, flanked by matching privacy panels.' => 'Cada proyecto en Thor Metal Art sigue un proceso probado — desde esa primera llamada telefónica hasta el último perno apretado en su nuevo portón o baranda. Este artículo le lleva dentro de un reciente proyecto en una residencia de Coral Gables: un portón de entrada de aluminio personalizado con intercomunicador integrado, flanqueado por paneles de privacidad a juego.',
        'The project began with a site visit. Karel Frometa met personally with the homeowner to assess the existing driveway opening (22 feet), discuss the architectural style (Mediterranean Revival), and review material options. The client wanted an ornate-looking gate that required minimal maintenance — the perfect use case for aluminum with bronze powder coating.' => 'El proyecto comenzó con una visita al sitio. Karel Frometa se reunió personalmente con el propietario para evaluar la apertura existente del camino de entrada (22 pies), discutir el estilo arquitectónico (Revival Mediterráneo) y revisar las opciones de materiales. El cliente quería un portón de aspecto ornamentado pero que requiriera un mantenimiento mínimo — el caso de uso perfecto para aluminio con recubrimiento en polvo color bronce.',
        'After consultation, we created 3 digital 2D design concepts. The client selected a modified scroll design with a custom family crest as the centerpiece. Approval took two rounds of revisions before the project moved to fabrication.' => 'Después de la consulta, creamos 3 conceptos de diseño digital en 2D. El cliente seleccionó un diseño de pergamino modificado con un escudo familiar personalizado como pieza central. La aprobación tomó dos rondas de revisiones antes de que el proyecto pasara a fabricación.',
        'Fabrication took place in our Hialeah shop over 18 business days. The process involved:' => 'La fabricación tuvo lugar en nuestro taller de Hialeah durante 18 días hábiles. El proceso involucró:',
        'CNC plasma cutting of the main gate panels' => 'Corte por plasma CNC de los paneles principales del portón',
        'Water jet cutting of the ornamental crest (precision required for fine detail)' => 'Corte por chorro de agua del escudo ornamental (se requiere precisión para el fino detalle)',
        'TIG welding of all structural joints' => 'Soldadura TIG de todas las uniones estructurales',
        'Grinding, finishing, and sandblasting' => 'Esmerilado, acabado y arenado',
        'Zinc primer application and bronze powder coat curing' => 'Aplicación de imprimador de zinc y curado del recubrimiento en polvo de bronce',
        'Installation required a two-day site visit. Day one: concrete footings for gate posts and conduit for electrical wiring. Day two: post setting, gate hanging, hardware installation, and motor programming. The intercom was wired into the existing home automation system.' => 'La instalación requirió una visita al sitio de dos días. Primer día: cimientos de concreto para los postes del portón y conducto para el cableado eléctrico. Segundo día: colocación de postes, colgado del portón, instalación de herrajes y programación del motor. El intercomunicador se conectó al sistema de automatización del hogar existente.',
        'The finished gate became the focal point of the property. The homeowner noted that three neighbors asked for our card within the first week. That\'s the best review we can receive.' => 'El portón terminado se convirtió en el punto focal de la propiedad. El propietario comentó que tres vecinos le pidieron nuestra tarjeta dentro de la primera semana. Esa es la mejor reseña que podemos recibir.',
        'Want to see what we can create for your property?' => '¿Quiere ver lo que podemos crear para su propiedad?',
        'Start with a free consultation.' => 'Comience con una consulta gratuita.',
        // ── Post 84 — Maintain Metal Railings ───────────────────────────────
        'How to Maintain Metal Railings in South Florida\'s Salt Air' => 'Cómo Mantener Barandas Metálicas en el Aire Salino del Sur de Florida',
        'South Florida is one of the harshest environments for metal in the United States. The combination of salt air, high humidity, intense UV, and frequent rain creates conditions that accelerate corrosion on unprotected metalwork. Here\'s how to keep your metal railings looking sharp and structurally sound for decades.' => 'El Sur de Florida es uno de los ambientes más severos para el metal en los Estados Unidos. La combinación de aire salino, alta humedad, intensa radiación UV y lluvias frecuentes crea condiciones que aceleran la corrosión en la herrería sin protección. Aquí se explica cómo mantener sus barandas metálicas impecables y estructuralmente sólidas durante décadas.',
        'Salt particles from the ocean travel miles inland. In Miami-Dade, any property within 10 miles of the coast has elevated corrosion risk. Salt deposits on metal surfaces attract moisture, which accelerates oxidation. The result: rust that, left untreated, will compromise the structural integrity of your railings within a few years.' => 'Las partículas de sal del océano viajan millas tierra adentro. En Miami-Dade, cualquier propiedad a menos de 10 millas de la costa tiene un riesgo de corrosión elevado. Los depósitos de sal en las superficies metálicas atraen la humedad, lo que acelera la oxidación. El resultado: óxido que, si no se trata, comprometerá la integridad estructural de sus barandas en pocos años.',
        'Visual inspection (monthly):' => 'Inspección visual (mensual):',
        'Look for paint bubbling, rust spotting, or loose welds. Catch problems early.' => 'Busque burbujas de pintura, manchas de óxido o soldaduras sueltas. Detecte los problemas a tiempo.',
        'Fresh water rinse (monthly):' => 'Enjuague con agua dulce (mensual):',
        'Use a garden hose to rinse salt deposits from all metal surfaces. This single habit dramatically extends finish life.' => 'Use una manguera de jardín para enjuagar los depósitos de sal de todas las superficies metálicas. Este único hábito extiende dramáticamente la vida del acabado.',
        'Mild soap wash (quarterly):' => 'Lave con jabón suave (trimestral):',
        'A soft brush and diluted dish soap removes grime and salt deposits. Rinse thoroughly and dry with a cloth.' => 'Un cepillo suave y jabón lavavajillas diluido elimina la suciedad y los depósitos de sal. Enjuague bien y seque con un paño.',
        'Paint and coating inspection (annual):' => 'Inspeccione la pintura y el recubrimiento (anual):',
        'Any chips or scratches should be touched up immediately. Use matching powder coat touch-up paint or rust-inhibiting primer.' => 'Cualquier astilla o arañazo debe retocarse de inmediato. Use pintura de retoque del recubrimiento en polvo a juego o imprimador inhibidor del óxido.',
        'Professional refinishing (every 5–10 years):' => 'Acabado profesional (cada 5–10 años):',
        'Full sandblasting and recoating restores railings to like-new condition.' => 'El arenado completo y el nuevo recubrimiento restauran las barandas a condición de nuevas.',
        'Not all rust inhibitors are equal. For Miami railings, we recommend marine-rated products: Rust-Oleum Marine Coatings, Corroseal, or Tremclad Rust Paint. For touch-ups, use a rust converter before painting over any exposed metal.' => 'No todos los inhibidores de óxido son iguales. Para las barandas de Miami, recomendamos productos calificados para ambientes marinos: Rust-Oleum Marine Coatings, Corroseal o Tremclad Rust Paint. Para retoques, use un convertidor de óxido antes de pintar sobre cualquier metal expuesto.',
        'If you see rust spreading under the surface coating, structural cracks in welds, or sections where metal has thinned, it\'s time to call a fabricator. Surface rust is manageable. Structural rust is a safety issue.' => 'Si ve óxido extendiéndose bajo el recubrimiento superficial, grietas estructurales en las soldaduras, o secciones donde el metal se ha adelgazado, es hora de llamar a un fabricante. El óxido superficial es manejable. El óxido estructural es un problema de seguridad.',
        'Thor Metal Art offers railing inspection and refinishing services throughout Miami-Dade.' => 'Thor Metal Art ofrece servicios de inspección y refinishado de barandas en todo Miami-Dade.',
        'Schedule your inspection today.' => 'Programe su inspección hoy.',
        // ── Post 85 — Custom Metal Furniture ────────────────────────────────
        '5 Ideas for Custom Metal Furniture That Transform Any Space' => '5 Ideas de Muebles de Metal Personalizados que Transforman Cualquier Espacio',
        'Custom metal furniture is having a moment in interior and exterior design. From minimalist coffee tables to sculptural shelving units, steel and iron pieces bring an industrial elegance that no other material matches. Here are five ideas to inspire your next project.' => 'Los muebles de metal personalizados están viviendo un momento especial en el diseño de interiores y exteriores. Desde mesas de centro minimalistas hasta unidades de estantería escultóricas, las piezas de acero y hierro aportan una elegancia industrial que ningún otro material iguala. Aquí hay cinco ideas para inspirar su próximo proyecto.',
        'Wall-mounted steel shelves with a natural or powder-coated finish are a bold statement in any kitchen, living room, or home office. We fabricate custom brackets and shelves in any size, including live-edge wood tops over steel bases for a warm industrial look.' => 'Los estantes de acero montados en la pared con acabado natural o pintado en polvo son una declaración audaz en cualquier cocina, sala de estar u oficina en casa. Fabricamos soportes y estantes personalizados en cualquier tamaño, incluidas tapas de madera de canto vivo sobre bases de acero para un cálido aspecto industrial.',
        'A welded steel or iron table base can be designed in virtually any shape: hairpin legs, pedestal, X-frame, or fully custom sculpture. Paired with a glass, wood, or marble top, a custom metal base transforms any dining room into a design statement.' => 'Una base de mesa de acero o hierro soldado puede diseñarse prácticamente en cualquier forma: patas de horquilla, pedestal, estructura en X o escultura completamente personalizada. Combinado con un tablero de vidrio, madera o mármol, una base de metal personalizada transforma cualquier comedor en una declaración de diseño.',
        'Miami outdoor living demands furniture that withstands sun, rain, and salt air. We fabricate outdoor dining sets, loungers, and side tables in powder-coated aluminum or galvanized steel — materials that outlast standard patio furniture in durability by decades.' => 'La vida al aire libre en Miami exige muebles que soporten el sol, la lluvia y el aire salino. Fabricamos conjuntos de comedor para exteriores, tumbonas y mesas auxiliares en aluminio pintado en polvo o acero galvanizado — materiales que superan a los muebles de patio estándar en durabilidad por décadas.',
        'Laser-cut metal screens create privacy and decorative interest in open-plan spaces. Tropical patterns, geometric designs, or custom illustrations — these pieces work both indoors and outdoors and become conversation pieces in any setting.' => 'Las pantallas de metal cortadas con láser crean privacidad e interés decorativo en espacios de planta abierta. Patrones tropicales, diseños geométricos o ilustraciones personalizadas — estas piezas funcionan tanto en interiores como en exteriores y se convierten en tema de conversación en cualquier ambiente.',
        'A custom welded bed frame is a lifetime investment. We design frames in minimalist steel, ornamental iron, or anything in between. Unlike mass-produced furniture, ours are built to the exact dimensions of your mattress and room.' => 'Un armazón de cama soldado personalizado es una inversión de por vida. Diseñamos armazones en acero minimalista, hierro ornamental o cualquier estilo intermedio. A diferencia de los muebles fabricados en serie, los nuestros se construyen según las dimensiones exactas de su colchón y habitación.',
        'Ready to commission a custom metal furniture piece?' => '¿Listo para encargar una pieza de mueble de metal personalizado?',
        'Contact us' => 'Contáctenos',
        '— we design and fabricate throughout Miami-Dade County.' => '— diseñamos y fabricamos en todo el condado de Miami-Dade.',
        // ── Post 86 — Metal Sculpture Commissioning ──────────────────────────
        'Metal Sculpture Commissioning: What to Expect' => 'Comisión de Esculturas de Metal: Qué Esperar',
        'Commissioning a custom metal sculpture is one of the most exciting — and sometimes most daunting — creative decisions you can make. Whether you\'re a homeowner, a business, or a public institution, understanding the commissioning process removes the mystery and makes the collaboration more enjoyable.' => 'Encargar una escultura de metal personalizada es una de las decisiones creativas más emocionantes — y a veces más intimidantes — que puede tomar. Ya sea que sea un propietario, una empresa o una institución pública, entender el proceso de encargo elimina el misterio y hace que la colaboración sea más agradable.',
        'The process begins with your vision. You don\'t need to arrive with technical specifications — just an idea. What emotion do you want the piece to evoke? Where will it be located? What\'s the approximate budget? Even a rough sketch or a photo of work you admire gives us a starting point.' => 'El proceso comienza con su visión. No necesita llegar con especificaciones técnicas — solo una idea. ¿Qué emoción quiere que evoque la pieza? ¿Dónde estará ubicada? ¿Cuál es el presupuesto aproximado? Incluso un boceto aproximado o una foto de un trabajo que admira nos da un punto de partida.',
        'During the initial consultation, we discuss materials (stainless steel, Corten, bronze, painted steel), scale, and siting. Outdoor sculptures require weathering consideration; indoor pieces can use more delicate finishes.' => 'Durante la consulta inicial, discutimos materiales (acero inoxidable, Corten, bronce, acero pintado), escala y ubicación. Las esculturas al aire libre requieren consideración para la intemperie; las piezas de interior pueden usar acabados más delicados.',
        'Following consultation, we prepare a written proposal with design concepts, material specs, timeline, and price. Sculpture commissions range from $2,500 for small accent pieces to $50,000+ for large-scale installations. Pricing is transparent — you know exactly what you\'re paying for.' => 'Después de la consulta, preparamos una propuesta escrita con conceptos de diseño, especificaciones de materiales, cronograma y precio. Las comisiones de escultura van desde $2,500 para piezas de acento pequeñas hasta más de $50,000 para instalaciones a gran escala. Los precios son transparentes — usted sabe exactamente qué está pagando.',
        'We create detailed 2D drawings or 3D renders for your approval. This is the collaborative phase — your feedback shapes the final piece. We don\'t move to fabrication until you are completely satisfied with the design.' => 'Creamos dibujos 2D detallados o renderizados 3D para su aprobación. Esta es la fase colaborativa — sus comentarios dan forma a la pieza final. No pasamos a fabricación hasta que esté completamente satisfecho con el diseño.',
        'Fabrication takes place in our Hialeah studio. For larger commissions, we provide progress photos at key milestones. This transparency is something our clients consistently appreciate.' => 'La fabricación tiene lugar en nuestro estudio de Hialeah. Para comisiones más grandes, proporcionamos fotos del progreso en hitos clave. Esta transparencia es algo que nuestros clientes aprecian constantemente.',
        'We handle delivery and installation throughout Miami-Dade. For large outdoor sculptures, installation includes foundation work and structural anchoring per local building codes.' => 'Nos encargamos de la entrega e instalación en todo Miami-Dade. Para esculturas al aire libre de gran tamaño, la instalación incluye trabajo de cimentación y anclaje estructural según los códigos de construcción locales.',
        'Interested in commissioning a piece?' => '¿Interesado en encargar una pieza?',
        'Contact us to start the conversation' => 'Contáctenos para comenzar la conversación',
        '— no commitment for the initial consultation.' => '— sin compromiso para la consulta inicial.',
        // ── Post 87 — Miami Architects ───────────────────────────────────────
        'Why Miami Architects Choose Custom Metalwork' => 'Por Qué los Arquitectos de Miami Eligen Herrería Personalizada',
        'Miami is one of the most architecturally diverse cities in the United States. From Art Deco to ultra-modern glass towers, the built environment here demands materials that are both beautiful and durable. Custom metalwork has become a signature element in projects by Miami\'s top architects — and for good reason.' => 'Miami es una de las ciudades arquitectónicamente más diversas de los Estados Unidos. Desde el Art Deco hasta las ultramodernas torres de vidrio, el entorno construido aquí exige materiales que sean tanto hermosos como resistentes. La herrería personalizada se ha convertido en un elemento distintivo en proyectos de los mejores arquitectos de Miami — y con razón.',
        'Modern fabrication technology — CNC plasma cutting, waterjet, TIG welding, and powder coating — allows architects to realize designs that simply can\'t be achieved in wood, concrete, or prefabricated materials. Curves, custom perforations, integrated lighting channels, and multi-material assemblies are all possible when working with a skilled custom fabricator.' => 'La tecnología de fabricación moderna — corte por plasma CNC, chorro de agua, soldadura TIG y recubrimiento en polvo — permite a los arquitectos realizar diseños que simplemente no se pueden lograr en madera, concreto o materiales prefabricados. Curvas, perforaciones personalizadas, canales de iluminación integrados y ensamblajes de múltiples materiales son todos posibles cuando se trabaja con un fabricante personalizado experto.',
        'Architects value fabricators who understand construction project management. At Thor Metal Art, we work directly with general contractors, project managers, and site supervisors to meet schedule requirements. Shop drawings, submittals, and material certifications are delivered on time — non-negotiable on large commercial projects.' => 'Los arquitectos valoran a los fabricantes que entienden la gestión de proyectos de construcción. En Thor Metal Art, trabajamos directamente con contratistas generales, gerentes de proyecto y supervisores de sitio para cumplir con los requisitos del cronograma. Los dibujos de taller, envíos y certificaciones de materiales se entregan a tiempo — algo innegociable para grandes proyectos comerciales.',
        'A recent project involved the design and fabrication of 400 linear feet of glass-and-steel railing system for a commercial tower lobby in Brickell. The result: a seamless system that met both the structural engineer\'s specifications and the lead architect\'s vision for the space.' => 'Un proyecto reciente involucró el diseño y fabricación de 400 pies lineales de sistema de barandas de vidrio y acero para el vestíbulo de una torre comercial en Brickell. El resultado: un sistema uniforme que cumplió tanto con las especificaciones del ingeniero estructural como con la visión del arquitecto principal para el espacio.',
        'Architects and designers: we welcome project consultations and preconstruction-phase collaboration.' => 'Arquitectos y diseñadores: damos la bienvenida a consultas de proyectos y colaboración en la fase de preconstrucción.',
        'Contact us to discuss your next project.' => 'Contáctenos para discutir su próximo proyecto.',
        // ── Post 88 — Water Jet vs Plasma ────────────────────────────────────
        'Water Jet Cutting vs. Plasma Cutting: A Fabricator\'s Guide' => 'Corte por Chorro de Agua vs. Corte por Plasma: Guía del Fabricante',
        'When it comes to precision metal cutting in fabrication, two technologies dominate: waterjet cutting and plasma cutting. Each has distinct strengths, and choosing the right one can significantly affect the quality, cost, and timeline of your project. Here\'s a practical guide from a fabricator\'s perspective.' => 'Cuando se trata de corte de metal de precisión en fabricación, dos tecnologías dominan: el corte por chorro de agua y el corte por plasma. Cada una tiene fortalezas distintas, y elegir la correcta puede afectar significativamente la calidad, el costo y el cronograma de su proyecto. Aquí hay una guía práctica desde la perspectiva de un fabricante.',
        'Waterjet cutting uses a high-pressure jet of water — sometimes mixed with abrasive garnet — to cut metal. The pressure can reach 60,000 PSI, slicing through steel, aluminum, stainless steel, and even composites with extreme precision. Crucially, waterjet is a cold-cutting process: no heat-affected zone (HAZ), which means the metal\'s properties are preserved at the cut edge.' => 'El corte por chorro de agua utiliza un chorro de agua a alta presión — a veces mezclado con granate abrasivo — para cortar metal. La presión puede alcanzar 60,000 PSI, lo que corta acero, aluminio, acero inoxidable e incluso compuestos con extrema precisión. Fundamentalmente, el chorro de agua es un proceso de corte en frío: sin zona afectada por el calor (ZAC), lo que significa que las propiedades del metal se conservan en el borde cortado.',
        'Plasma cutting uses a high-velocity ionized gas jet (plasma) to melt and expel metal along the cut line. It\'s faster than waterjet for thick steel and more cost-effective for high-volume cuts. However, it produces a heat-affected zone along the edge, which can affect hardness and finish quality.' => 'El corte por plasma utiliza un chorro de gas ionizado (plasma) a alta velocidad para fundir y expulsar el metal en la línea de corte. Es más rápido que el chorro de agua para acero grueso y más rentable para cortes de gran volumen. Sin embargo, produce una zona afectada por el calor a lo largo del borde, lo que puede afectar la dureza y la calidad del acabado.',
        'Precision:' => 'Precisión:',
        'Waterjet wins — tolerances of ±0.001". Plasma is typically ±0.02".' => 'Chorro de agua gana — tolerancias de ±0.001". Plasma es típicamente ±0.02".',
        'Speed:' => 'Velocidad:',
        'Plasma cuts faster on thick steel (>1/2"). Waterjet is slower but more precise.' => 'El plasma corta más rápido en acero grueso (>1/2"). El chorro de agua es más lento pero más preciso.',
        'Materials:' => 'Materiales:',
        'Waterjet cuts almost anything. Plasma is limited to conductive metals.' => 'El chorro de agua corta casi cualquier cosa. El plasma se limita a metales conductores.',
        'Heat:' => 'Calor:',
        'Waterjet produces no thermal distortion. Plasma creates a HAZ.' => 'El chorro de agua no produce distorsión térmica. El plasma crea una ZAC.',
        'Cost:' => 'Costo:',
        'Plasma cutting is generally cheaper per linear foot.' => 'El corte por plasma es generalmente más barato por pie lineal.',
        'Waterjet = ornamental details, precise patterns. Plasma = structural cuts, large sheets.' => 'Chorro de agua = detalles ornamentales, patrones precisos. Plasma = cortes estructurales, láminas grandes.',
        'We use both technologies depending on the project. Custom gates with ornamental laser-cut patterns go through our waterjet. Structural steel for railings and brackets is plasma-cut, then ground and finished to spec. The right tool for the right job produces better results at a better price.' => 'Usamos ambas tecnologías según el proyecto. Los portones personalizados con patrones ornamentales cortados con láser pasan por nuestro chorro de agua. El acero estructural para barandas y soportes se corta por plasma, luego se esmerila y termina según especificaciones. La herramienta correcta para el trabajo correcto produce mejores resultados a mejor precio.',
        'Have a project in mind?' => '¿Tiene un proyecto en mente?',
        'Talk to our team' => 'Hable con nuestro equipo',
        'about which cutting method is best for your design and budget.' => 'sobre qué método de corte es mejor para su diseño y presupuesto.',
        // ── Post 89 — Right Metal Fence ──────────────────────────────────────
        'How to Choose the Right Metal Fence for Your Miami Property' => 'Cómo Elegir la Cerca Metálica Correcta para su Propiedad en Miami',
        'A metal fence is a long-term investment that can define the character of your property for decades. In Miami, where climate and aesthetics both play major roles, choosing the right fence requires more thought than simply picking a style from a catalog. Here\'s what to consider before you commit.' => 'Una cerca metálica es una inversión a largo plazo que puede definir el carácter de su propiedad durante décadas. En Miami, donde el clima y la estética juegan roles importantes, elegir la cerca correcta requiere más reflexión que simplemente elegir un estilo de un catálogo. Esto es lo que debe considerar antes de comprometerse.',
        'Define the primary purpose of your fence. A security fence for a commercial property has very different requirements than a decorative fence for a Coral Gables garden. Security fences prioritize height, anti-climb features, and structural resistance. Decorative fences prioritize design, finish, and curb appeal.' => 'Defina el objetivo principal de su cerca. Una cerca de seguridad para una propiedad comercial tiene requisitos muy diferentes a una cerca decorativa para un jardín en Coral Gables. Las cercas de seguridad priorizan la altura, características anti-escalada y resistencia estructural. Las cercas decorativas priorizan el diseño, el acabado y el atractivo visual.',
        'Each material excels in different situations. Steel offers strength and design flexibility. Aluminum never rusts and is ideal for pool fencing and coastal properties. Wrought iron provides a classic aesthetic but requires regular maintenance in Miami\'s humidity. For most residential applications in Miami-Dade, powder-coated aluminum or marine-finish steel are the practical choices.' => 'Cada material se destaca en diferentes situaciones. El acero ofrece resistencia y flexibilidad de diseño. El aluminio nunca se oxida y es ideal para cercas de piscina y propiedades costeras. El hierro forjado proporciona una estética clásica pero requiere mantenimiento regular en la humedad de Miami. Para la mayoría de las aplicaciones residenciales en Miami-Dade, el aluminio pintado en polvo o el acero con acabado marino son las opciones prácticas.',
        'Miami-Dade County and most HOAs have specific rules about fence height, style, and materials. Before ordering anything, check with your HOA and review Section 33-11.5 of the Miami-Dade County Code on fence permits. We manage permit applications as part of our installation service.' => 'El condado de Miami-Dade y la mayoría de las HOA tienen reglas específicas sobre la altura, el estilo y los materiales de las cercas. Antes de ordenar cualquier cosa, consulte con su HOA y revise la Sección 33-11.5 del Código del Condado de Miami-Dade sobre permisos de cercas. Gestionamos las solicitudes de permisos como parte de nuestro servicio de instalación.',
        'Miami is in a High Velocity Hurricane Zone (HVHZ). Any fence exceeding certain heights requires engineering drawings and a wind load calculation. We design all our fences to meet or exceed Florida Building Code requirements — your permit won\'t be rejected for structural deficiencies.' => 'Miami está en una Zona de Huracanes de Alta Velocidad (HVHZ). Cualquier cerca que supere cierta altura requiere dibujos de ingeniería y un cálculo de carga de viento. Diseñamos todas nuestras cercas para cumplir o superar los requisitos del Código de Construcción de Florida — su permiso no será rechazado por deficiencias estructurales.',
        'A marine-grade powder coat finish on steel adds 15–20 years of corrosion protection. Aluminum just needs periodic washing. Factor maintenance requirements into your budget — a slightly more expensive, low-maintenance fence often costs less over 10 years than a cheaper high-maintenance option.' => 'Un acabado de pintura en polvo de grado marino en acero agrega 15–20 años de protección contra la corrosión. El aluminio solo necesita lavado periódico. Tenga en cuenta los requisitos de mantenimiento en su presupuesto — una cerca ligeramente más costosa y de bajo mantenimiento a menudo cuesta menos durante 10 años que una opción más barata de alto mantenimiento.',
        'Not sure where to start?' => '¿No sabe por dónde empezar?',
        'Schedule a free site assessment' => 'Programe una evaluación gratuita del sitio',
        'and we\'ll walk you through the entire process.' => 'y le guiaremos a través de todo el proceso.',
        // ── Post 90 — TIG Welding ─────────────────────────────────────────────
        'TIG Welding: The Art Behind Structural & Decorative Metalwork' => 'Soldadura TIG: El Arte detrás de la Herrería Estructural y Decorativa',
        'If you want to understand what separates exceptional metalwork from ordinary fabrication, look at the welds. TIG welding — Tungsten Inert Gas welding (GTAW) — is the most precision welding process available, and it\'s the method we use for all structural and decorative work at Thor Metal Art.' => 'Si quiere entender qué separa la herrería excepcional de la fabricación ordinaria, mire las soldaduras. La soldadura TIG — Soldadura por Arco de Gas Tungsteno (GTAW) — es el proceso de soldadura de mayor precisión disponible, y es el método que usamos para todo el trabajo estructural y decorativo en Thor Metal Art.',
        'Unlike MIG welding (which uses an automatically-fed consumable wire electrode) or stick welding, TIG uses a non-consumable tungsten electrode to create the arc. The welder feeds filler rod manually with the other hand while precisely controlling heat input with a foot pedal. This level of control produces cleaner, stronger, and aesthetically superior welds.' => 'A diferencia de la soldadura MIG (que usa un electrodo de alambre consumible alimentado automáticamente) o la soldadura con electrodo, la soldadura TIG usa un electrodo de tungsteno no consumible para crear el arco. El soldador alimenta la varilla de relleno manualmente con la otra mano mientras controla con precisión la entrada de calor con un pedal de pie. Este nivel de control produce soldaduras más limpias, resistentes y refinadas estéticamente.',
        'Decorative metalwork pieces — railings, gates, sculptures, furniture — are often visible up close. MIG welds, while fast and functional, typically require grinding and finishing to look presentable. A skilled TIG welder can produce stack-of-dimes welds that are themselves part of the finished aesthetic, requiring minimal post-processing.' => 'Las piezas de metal decorativas — barandas, portones, esculturas, muebles — a menudo son visibles de cerca. Las soldaduras MIG, aunque rápidas y funcionales, típicamente requieren esmerilado y acabado para lucir presentables. Un soldador TIG hábil puede producir soldaduras en forma de pila de monedas que son en sí mismas parte de la estética terminada, requiriendo un post-procesamiento mínimo.',
        'TIG welding also excels in structural applications where weld quality is critical. Stair railings, balcony guards, and load-bearing frames benefit from the superior penetration and reduced porosity that TIG produces compared to other processes. When a railing needs to hold a person\'s weight in an emergency, weld quality matters enormously.' => 'La soldadura TIG también sobresale en aplicaciones estructurales donde la calidad de la soldadura es crítica. Las barandas de escaleras, los guardas de balcones y los marcos de carga se benefician de la penetración superior y la porosidad reducida que produce TIG en comparación con otros procesos. Cuando una baranda debe soportar el peso de una persona en una emergencia, la calidad de la soldadura importa enormemente.',
        'Mild steel (gates, railings, frames)' => 'Acero dulce (portones, barandas, marcos)',
        'Stainless steel (kitchen equipment, marine applications, modern architecture)' => 'Acero inoxidable (equipamiento de cocina, aplicaciones marinas, arquitectura moderna)',
        'Aluminum (pool fencing, lightweight structures, signage)' => 'Aluminio (cercas de piscina, estructuras ligeras, señalización)',
        'Titanium and exotic alloys (special commissions)' => 'Titanio y aleaciones exóticas (encargos especiales)',
        'When you hire Thor Metal Art, you are hiring certified TIG welders with the skill to execute your vision with precision and safety.' => 'Cuando contrata a Thor Metal Art, está contratando soldadores TIG certificados con la habilidad de ejecutar su visión con precisión y seguridad.',
        'Request a quote today.' => 'Solicite un presupuesto hoy.',
        // ── Post 91 — From Sketch to Steel ───────────────────────────────────
        'From Sketch to Steel: Our Custom Metal Gate Process' => 'Del Boceto al Acero: Nuestro Proceso de Portones de Metal Personalizados',
        'Every custom metal gate at Thor Metal Art starts with a conversation and ends with a precision-installed functional sculpture. If you\'ve ever wondered what happens between your first call and the day your new gate is hanging on its hinges, this walkthrough is for you.' => 'Cada portón de metal personalizado en Thor Metal Art comienza con una conversación y termina con una pieza de escultura funcional instalada con precisión. Si alguna vez se ha preguntado qué sucede entre su primera llamada y el día en que su nuevo portón está colgado en sus bisagras, este recorrido es para usted.',
        'We start with a site visit. Karel Frometa personally meets with most clients to assess the opening, understand the architectural context, and discuss design preferences. No commitment — this visit is free. We take measurements, photograph the site, and listen. Most clients arrive with a general idea; we turn it into something buildable.' => 'Comenzamos con una visita al sitio. Karel Frometa se reúne personalmente con la mayoría de los clientes para evaluar la abertura, entender el contexto arquitectónico y discutir las preferencias de diseño. Sin compromiso — esta visita es gratuita. Tomamos medidas, fotografiamos el sitio y escuchamos. La mayoría de los clientes llegan con una idea general; nosotros la convertimos en algo construible.',
        'Within one week of consultation, you receive a written proposal including design sketches, material specifications, and a fixed price. We don\'t work in open-ended estimates — you know exactly what you\'re paying before any work begins. If the design needs adjustments, we make them at no extra cost.' => 'Dentro de una semana de la consulta, recibe una propuesta escrita que incluye bocetos de diseño, especificaciones de materiales y un precio fijo. No trabajamos con estimados abiertos — usted sabe exactamente qué está pagando antes de que comience cualquier trabajo. Si el diseño necesita ajustes, los hacemos sin costo adicional.',
        'Once you approve the design and proposal, we move to shop drawings. These are precise engineering documents that guide every cut and weld. Material is sourced locally, inspected on delivery, and stored in our Hialeah fabrication shop.' => 'Una vez que apruebe el diseño y la propuesta, pasamos a los dibujos de taller. Estos son documentos de ingeniería precisos que guían cada corte y soldadura. El material se adquiere localmente, se inspecciona a la entrega y se almacena en nuestro taller de fabricación de Hialeah.',
        'Fabrication involves cutting, forming, welding, grinding, and finishing. Most residential gates take 10–20 shop days depending on complexity. We provide progress photos during fabrication so there are no surprises at delivery.' => 'La fabricación implica corte, conformado, soldadura, esmerilado y acabado. La mayoría de los portones residenciales toman entre 10 y 20 días de taller según la complejidad. Proporcionamos fotos del progreso durante la fabricación para que no haya sorpresas en la entrega.',
        'Every gate gets a multi-step finish: zinc phosphate primer for corrosion inhibition, then a marine-grade powder coat in your chosen color. This finish is oven-cured at 400°F for maximum adhesion and durability — essential for Miami\'s climate.' => 'Cada portón recibe un acabado de múltiples pasos: imprimador de fosfato de zinc para inhibición de la corrosión, luego una pintura en polvo de grado marino en el color que elija. Este acabado se hornea a 400°F para máxima adhesión y durabilidad — esencial para el clima de Miami.',
        'Our installation team handles everything: post setting in concrete, gate hanging, hardware installation, and final adjustment. Automated gates include motor programming and a homeowner guide. We don\'t consider a job complete until you are fully satisfied.' => 'Nuestro equipo de instalación se encarga de todo: colocación de postes en concreto, colgado del portón, instalación de herrajes y ajuste final. Los portones automatizados incluyen programación del motor y una guía para el propietario. No consideramos un trabajo terminado hasta que esté completamente satisfecho.',
        'Contact us today' => 'Contáctenos hoy',
        'for a free consultation and site visit.' => 'para una consulta gratuita y visita al sitio.',
    ];

    foreach ( $pairs as $original => $translated ) {
        $upsert( $original, $translated );
    }

    update_option( 'tma_blog_translations_v3', true );
}
add_action( 'init', 'tma_provision_blog_translations_v3' );

/**
 * Translate <title> tag for Spanish blog posts.
 * TranslatePress does not process <head>/<title> via its DOM parser.
 * We hook into 'wp' (after query is set) to add a late 'pre_get_document_title' filter.
 */
add_action( 'wp', 'tma_register_title_translation_filter' );
function tma_register_title_translation_filter() {
    global $TRP_LANGUAGE;
    if ( empty( $TRP_LANGUAGE ) || $TRP_LANGUAGE === 'en_US' ) {
        return;
    }
    add_filter( 'pre_get_document_title', 'tma_get_spanish_document_title', 10 );
}

function tma_get_spanish_document_title( $title ) {
    $slug_title_map = array(
        'custom-metal-gate-cost-miami'               => '¿Cuánto Cuesta una Puerta de Metal Personalizada en Miami? | Thor Metal Art',
        'metal-gate-styles-miami-climate'            => '7 Estilos de Portones de Metal que Funcionan en el Clima de Miami | Thor Metal Art',
        'steel-vs-aluminum-gates-miami'              => 'Acero vs Aluminio: ¿Cuál Elegir para Portones en Miami? | Thor Metal Art',
        'miami-metalwork-project-design-install'     => 'Por Dentro de un Proyecto de Herrería en Miami: Del Diseño a la Instalación | Thor Metal Art',
        'maintain-metal-railings-south-florida'      => 'Cómo Mantener Barandas de Metal en el Aire Salino del Sur de Florida | Thor Metal Art',
        'custom-metal-furniture-ideas'               => '5 Ideas de Muebles de Metal Personalizados que Transforman Cualquier Espacio | Thor Metal Art',
        'metal-sculpture-commissioning'              => 'Encargo de Esculturas de Metal: Qué Esperar | Thor Metal Art',
        'miami-architects-custom-metalwork'          => 'Por Qué los Arquitectos de Miami Eligen la Herrería Personalizada | Thor Metal Art',
        'water-jet-cutting-vs-plasma-cutting'        => 'Corte por Chorro de Agua vs Corte por Plasma: Guía de un Fabricante | Thor Metal Art',
        'right-metal-fence-miami-property'           => 'Cómo Elegir la Cerca de Metal Correcta para Tu Propiedad en Miami | Thor Metal Art',
        'tig-welding-structural-decorative'          => 'Soldadura TIG: El Arte detrás de la Herrería Estructural y Decorativa | Thor Metal Art',
        'custom-metal-gate-process'                  => 'Del Boceto al Acero: Nuestro Proceso de Portones de Metal Personalizados | Thor Metal Art',
    );

    if ( is_singular( 'post' ) ) {
        $slug = get_post_field( 'post_name', get_queried_object_id() );
        if ( isset( $slug_title_map[ $slug ] ) ) {
            return $slug_title_map[ $slug ];
        }
    } elseif ( is_home() ) {
        return 'Blog | Thor Metal Art';
    }

    return $title;
}
