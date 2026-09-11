<?php

/**
 * Thor Metal Art — Lujo Forjado Pattern Spanish Translations (BRAND-017)
 *
 * Provisions TranslatePress (ES) translations for the 8 Lujo Forjado homepage
 * patterns introduced in TICKET-BRAND-001 to TICKET-BRAND-008.
 *
 * Uses version gate (tma_brand_translations_v2) so it only runs once.
 * Idempotent: safe to redeploy without duplicating data.
 *
 * @package ThorMetalArt
 */

defined('ABSPATH') || exit;

add_action('init', 'tma_provision_brand_translations_v2', 20);

function tma_provision_brand_translations_v2()
{
    if (get_option('tma_brand_translations_v2')) {
        return;
    }

    global $wpdb;
    $orig_table = $wpdb->prefix . 'trp_original_strings';
    $dict_table = $wpdb->prefix . 'trp_dictionary_en_us_es_es';

    // Verify tables exist before proceeding
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    if (! $wpdb->get_var("SHOW TABLES LIKE '{$dict_table}'")) {
        return;
    }

    /**
     * Upsert by original text so migrations remain portable across databases.
     */
    $upsert_text = function ($original, $translated) use ($wpdb, $dict_table, $orig_table) {
        $orig_id = (int) $wpdb->get_var($wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SELECT id FROM `{$orig_table}` WHERE original = %s ORDER BY id ASC LIMIT 1",
            $original
        ));

        if (! $orig_id) {
            if (false === $wpdb->insert($orig_table, ['original' => $original], ['%s'])) {
                return false;
            }
            $orig_id = (int) $wpdb->insert_id;
        }

        $existing = (int) $wpdb->get_var($wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SELECT id FROM `{$dict_table}` WHERE original_id = %d LIMIT 1",
            $orig_id
        ));
        if ($existing) {
            return false !== $wpdb->update(
                $dict_table,
                ['translated' => $translated, 'status' => 2],
                ['id' => $existing],
                ['%s', '%d'],
                ['%d']
            );
        }

        return false !== $wpdb->insert(
            $dict_table,
            [
                'original'    => $original,
                'translated'  => $translated,
                'status'      => 2,
                'block_type'  => 0,
                'original_id' => $orig_id,
            ],
            ['%s', '%s', '%d', '%d', '%d']
        );
    };

    $portable_translations = [
        'EST. MIAMI — Fabrication &amp; forged art' => 'EST. MIAMI — Fabricación y arte forjado',
        'Steel, shaped' => 'Acero, formado',
        'with' => 'con',
        'intent.' => 'intención.',
        'A Miami atelier where water-jet precision meets the forge. We fabricate architectural metalwork for the buildings that demand it — and original work for those who collect it.' => 'Un atelier en Miami donde la precisión del chorro de agua se encuentra con la forja. Fabricamos herrería arquitectónica para los edificios que lo exigen — y obra original para quienes la coleccionan.',
        'See selected work →' => 'Ver trabajo seleccionado →',
        'Inside the atelier' => 'Dentro del atelier',
        'Two disciplines,' => 'Dos disciplinas,',
        'one hand.' => 'una sola mano.',
        'Every project is drawn, cut and welded in-house — whether it holds up a staircase or hangs in a lobby.' => 'Cada proyecto se dibuja, corta y suelda internamente — ya sea que sostenga una escalera o cuelgue en un lobby.',
        'Architectural Fabrication' => 'Fabricación Arquitectónica',
        'Gates, railings, floating stairs and structural steel — engineered to code and finished to be looked at.' => 'Rejas, barandas, escaleras flotantes y acero estructural — diseñados para cumplir código y para ser admirados.',
        'Custom gates &amp; entries —' => 'Rejas y entradas personalizadas —',
        'Residential / Commercial' => 'Residencial / Comercial',
        'Railings &amp; balustrades —' => 'Barandas y balaustradas —',
        'Code-compliant' => 'Cumplimiento de código',
        'Floating &amp; spiral stairs —' => 'Escaleras flotantes y en espiral —',
        'Structural' => 'Estructural',
        'Fences &amp; perimeter systems —' => 'Cercas y sistemas perimetrales —',
        'South Florida rated' => 'Certificado en el Sur de Florida',
        'Explore fabrication →' => 'Explorar fabricación →',
        'Art &amp;' => 'Arte &',
        'Sculpture' => 'Escultura',
        'Commissioned sculpture, Damascus-forged blades and one-off design pieces — signed work by Karel Frometa.' => 'Esculturas por encargo, cuchillas forjadas en Damascus y piezas de diseño únicas — cada proyecto es una firma.',
        'Commissioned sculpture —' => 'Escultura por encargo —',
        'By brief' => 'Por encargo',
        'Damascus-forged blades —' => 'Cuchillas forjadas en Damascus —',
        'Hand-forged' => 'Forjado a mano',
        'Design furniture —' => 'Mobiliario de diseño —',
        'One-off' => 'Pieza única',
        'Lobby &amp; interior pieces —' => 'Piezas para lobby e interiores —',
        'Site-specific' => 'Sitio específico',
        'Explore the collection →' => 'Explorar la colección →',
        'Selected work' => 'Trabajo seleccionado',
        'A working index. Each piece is documented as a case study — the drawing, the raw metal, and the installed result.' => 'Un índice de trabajo. Cada pieza está documentada como estudio de caso — el dibujo, el metal en bruto y el resultado instalado.',
        '01 / STRUCTURAL' => '01 / ESTRUCTURAL',
        'TIG-Welded Structural Steel' => 'Acero Estructural Soldado TIG',
        'Precision TIG work bridging industrial load and architectural finish.' => 'Trabajo TIG de precisión que conecta la carga industrial con el acabado arquitectónico.',
        '02 / BLADE' => '02 / CUCHILLA',
        'Hand-Forged Damascus Blade' => 'Cuchilla Damascus Forjada a Mano',
        'Pattern-welded steel, hardwood handle — functional cutlery as object.' => 'Acero soldado por patrón, mango de madera dura — cubiertos funcionales como objeto.',
        '03 / MIRROR-POLISH' => '03 / ESPEJO-PULIDO',
        'Stainless Custom Fabrication' => 'Fabricación Personalizada en Acero Inoxidable',
        'Mirror-polished stainless for a commercial kitchen, welded seamless.' => 'Inoxidable pulido espejo para una cocina comercial, soldado sin costuras.',
        'View full portfolio →' => 'Ver portafolio completo →',
        'The atelier' => 'El atelier',
        'Founded on the bench, not the catalogue.' => 'Fundado en el banco de trabajo, no en el catálogo.',
        'Thor Metal Art is a Miami studio led by Karel Frometa, where heavy engineering and hand craft share the same floor. Water-jet cutting sets the tolerance; the forge and the weld give it character.' => 'Thor Metal Art es un estudio en Miami dirigido por Karel Frometa, donde la ingeniería pesada y la artesanía manual comparten el mismo espacio. El corte por chorro de agua establece la tolerancia; la forja y la soldadura le dan carácter.',
        'Nothing is outsourced. A residential gate and a commissioned sculpture pass through the same hands, the same standard, and the same drawing table before a single cut is made.' => 'Nada se subcontrata. Una reja residencial y una escultura por encargo pasan por las mismas manos, el mismo estándar y la misma mesa de dibujo antes de que se haga un solo corte.',
        'In-house' => 'En el taller',
        'Design → Install' => 'Diseño → Instalación',
        'Client rating' => 'Calificación de clientes',
        'Dade &amp; Broward' => 'Dade y Broward',
        'The philosophy' => 'La filosofía',
        'A gate should hold a wall.' => 'Una reja debe sostener una pared.',
        'A piece should hold a room.' => 'Una pieza debe sostener una habitación.',
        'We build both to outlast the person who commissioned them.' => 'Construimos ambas para que duren más que quien las encargó.',
        ', Founder' => ', Fundador',
        'From drawing' => 'Del dibujo',
        'to installed.' => 'a instalado.',
        'A sequence, not a quote form. You see the work take shape at every stage.' => 'Una secuencia, no un formulario de cotización. Usted ve el trabajo tomar forma en cada etapa.',
        '01 — The Drawing &amp; Cut' => '01 — El Dibujo y el Corte',
        'Every commission begins as a CAD drawing. Water-jet and laser set the tolerance before a weld is struck.' => 'Cada encargo comienza como un dibujo CAD. El chorro de agua y el láser establecen la tolerancia antes de dar la primera soldadura.',
        '02 — The Raw Metal' => '02 — El Metal en Bruto',
        'Forging, bending and TIG welding in the Miami shop. You receive progress documentation, not silence.' => 'Forja, doblado y soldadura TIG en el taller de Miami. Usted recibe documentación del avance, no silencio.',
        '03 — The Finished Piece' => '03 — La Pieza Terminada',
        'Delivered and installed by the team that built it, finished to sit as though it had always been there.' => 'Entregada e instalada por el equipo que la construyó, terminada para quedarse como si siempre hubiera estado ahí.',
        'Start a project' => 'Iniciar un proyecto',
        'Let&#8217;s forge' => 'Forjemos',
        'something lasting.' => 'algo duradero.',
        'Tell us what you&#8217;re building or the piece you have in mind. We reply within 24 hours — no pressure, no template quote.' => 'Cuéntenos qué está construyendo o la pieza que tiene en mente. Respondemos en 24 horas — sin presión, sin cotización genérica.',
        'Request a commission →' => 'Solicitar una comisión →',
        'WhatsApp the studio' => 'WhatsApp al estudio',
        'Trusted by architects, designers &amp; developers' => 'De confianza para arquitectos, diseñadores y promotores',
        'Made to order' => 'Hecho a medida',
        'Every project' => 'Cada proyecto',
        'Built for spaces across South Florida' => 'Creado para espacios del sur de Florida',
        'Residential' => 'Residencial',
        'Commercial' => 'Comercial',
        'Hospitality' => 'Hotelería',
        'Architectural' => 'Arquitectura',
        'Public Art' => 'Arte público',
        'Custom Commissions' => 'Encargos personalizados',
        'Get in Touch' => 'Contáctanos',
        'Art and commissions' => 'Arte y encargos',
        'Metal as Art' => 'Metal como arte',
        'Original sculptures and commissioned pieces by Karel Frometa in Miami.' => 'Esculturas originales y piezas por encargo de Karel Frometa en Miami.',
        'Our process' => 'Nuestro proceso',
        'How We Work' => 'Cómo trabajamos',
        'From first call to finished installation, every stage is handled by our Miami team.' => 'Desde la primera llamada hasta la instalación final, nuestro equipo de Miami gestiona cada etapa.',
    ];
    $success = true;
    foreach ($portable_translations as $original => $translated) {
        $success = $upsert_text($original, $translated) && $success;
    }

    if ($success) {
        update_option('tma_brand_translations_v2', true);
    }
}
