<?php

/**
 * Thor Metal Art — Lujo Forjado Pattern Spanish Translations (BRAND-017)
 *
 * Provisions TranslatePress (ES) translations for the 8 Lujo Forjado homepage
 * patterns introduced in TICKET-BRAND-001 to TICKET-BRAND-008.
 *
 * Uses version gate (tma_brand_translations_v1) so it only runs once.
 * Idempotent: safe to redeploy without duplicating data.
 *
 * @package ThorMetalArt
 */

defined('ABSPATH') || exit;

add_action('init', 'tma_provision_brand_translations_v1', 20);

function tma_provision_brand_translations_v1()
{
    if (get_option('tma_brand_translations_v1')) {
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
     * Upsert a translation by original_id (known ID from DB scan).
     * status=2 = human-translated (same as TranslatePress "human" status).
     */
    $upsert = function ($orig_id, $translated) use ($wpdb, $dict_table, $orig_table) {
        $existing = (int) $wpdb->get_var($wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SELECT id FROM `{$dict_table}` WHERE original_id = %d LIMIT 1",
            $orig_id
        ));
        if ($existing) {
            $wpdb->update(
                $dict_table,
                ['translated' => $translated, 'status' => 2],
                ['id' => $existing],
                ['%s', '%d'],
                ['%d']
            );
        } else {
            $orig_text = $wpdb->get_var($wpdb->prepare(
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                "SELECT original FROM `{$orig_table}` WHERE id = %d LIMIT 1",
                $orig_id
            ));
            if ($orig_text) {
                $wpdb->insert(
                    $dict_table,
                    [
                        'original'    => $orig_text,
                        'translated'  => $translated,
                        'status'      => 2,
                        'block_type'  => 0,
                        'original_id' => $orig_id,
                    ],
                    ['%s', '%s', '%d', '%d', '%d']
                );
            }
        }
    };

    // ── hero-forjado.php ─────────────────────────────────────────────────
    $upsert(730, 'EST. MIAMI — Fabricación y arte forjado');
    $upsert(731, 'Acero, formado');
    $upsert(732, 'con');
    $upsert(733, 'intención.');
    $upsert(734, 'Un atelier en Miami donde la precisión del chorro de agua se encuentra con la forja. Fabricamos herrería arquitectónica para los edificios que lo exigen — y obra original para quienes la coleccionan.');
    $upsert(735, 'Ver trabajo seleccionado →');
    $upsert(736, 'Dentro del atelier');

    // ── disciplines.php ──────────────────────────────────────────────────
    $upsert(737, 'Dos disciplinas,');
    $upsert(738, 'una sola mano.');
    $upsert(739, 'Cada proyecto se dibuja, corta y suelda internamente — ya sea que sostenga una escalera o cuelgue en un lobby.');
    $upsert(740, 'Fabricación Arquitectónica');
    $upsert(741, 'Rejas, barandas, escaleras flotantes y acero estructural — diseñados para cumplir código y para ser admirados.');
    $upsert(742, 'Rejas y entradas personalizadas —');
    $upsert(743, 'Residencial / Comercial');
    $upsert(744, 'Barandas y balaustradas —');
    $upsert(745, 'Cumplimiento de código');
    $upsert(746, 'Escaleras flotantes y en espiral —');
    $upsert(747, 'Estructural');
    $upsert(748, 'Cercas y sistemas perimetrales —');
    $upsert(749, 'Certificado en el Sur de Florida');
    $upsert(750, 'Explorar fabricación →');
    $upsert(751, 'Arte &');
    $upsert(752, 'Escultura');
    $upsert(753, 'Esculturas por encargo, cuchillas forjadas en Damascus y piezas de diseño únicas — cada proyecto es una firma.');
    $upsert(754, 'Escultura por encargo —');
    $upsert(755, 'Por encargo');
    $upsert(756, 'Cuchillas forjadas en Damascus —');
    $upsert(757, 'Forjado a mano');
    $upsert(758, 'Mobiliario de diseño —');
    $upsert(759, 'Pieza única');
    $upsert(760, 'Piezas para lobby e interiores —');
    $upsert(761, 'Sitio específico');
    $upsert(762, 'Explorar la colección →');

    // ── selected-work.php ────────────────────────────────────────────────
    $upsert(763, 'Trabajo seleccionado');
    $upsert(764, 'Un índice de trabajo. Cada pieza está documentada como estudio de caso — el dibujo, el metal en bruto y el resultado instalado.');
    $upsert(765, '01 / ESTRUCTURAL');
    $upsert(766, 'Acero Estructural Soldado TIG');
    $upsert(767, 'Trabajo TIG de precisión que conecta la carga industrial con el acabado arquitectónico.');
    $upsert(768, '02 / CUCHILLA');
    $upsert(769, 'Cuchilla Damascus Forjada a Mano');
    $upsert(770, 'Acero soldado por patrón, mango de madera dura — cubiertos funcionales como objeto.');
    $upsert(771, '03 / ESPEJO-PULIDO');
    $upsert(772, 'Fabricación Personalizada en Acero Inoxidable');
    $upsert(773, 'Inoxidable pulido espejo para una cocina comercial, soldado sin costuras.');
    $upsert(774, 'Ver portafolio completo →');

    // ── atelier.php ──────────────────────────────────────────────────────
    $upsert(775, 'El atelier');
    $upsert(776, 'Fundado en el banco de trabajo, no en el catálogo.');
    $upsert(777, 'Thor Metal Art es un estudio en Miami dirigido por Karel Frometa, donde la ingeniería pesada y la artesanía manual comparten el mismo espacio. El corte por chorro de agua establece la tolerancia; la forja y la soldadura le dan carácter.');
    $upsert(778, 'Nada se subcontrata. Una reja residencial y una escultura por encargo pasan por las mismas manos, el mismo estándar y la misma mesa de dibujo antes de que se haga un solo corte.');
    $upsert(779, 'En el taller');
    $upsert(780, 'Diseño → Instalación');
    $upsert(782, 'Calificación de clientes');
    $upsert(784, 'Dade y Broward');

    // ── quote-band.php ───────────────────────────────────────────────────
    $upsert(785, 'La filosofía');
    $upsert(786, 'Una reja debe sostener una pared.');
    $upsert(787, 'Una pieza debe sostener una habitación.');
    $upsert(788, 'Construimos ambas para que duren más que quien las encargó.');
    $upsert(790, ', Fundador');

    // ── process-forjado.php ──────────────────────────────────────────────
    $upsert(791, 'Del dibujo');
    $upsert(792, 'a instalado.');
    $upsert(793, 'Una secuencia, no un formulario de cotización. Usted ve el trabajo tomar forma en cada etapa.');
    $upsert(794, '01 — El Dibujo y el Corte');
    $upsert(795, 'Cada encargo comienza como un dibujo CAD. El chorro de agua y el láser establecen la tolerancia antes de dar la primera soldadura.');
    $upsert(796, '02 — El Metal en Bruto');
    $upsert(797, 'Forja, doblado y soldadura TIG en el taller de Miami. Usted recibe documentación del avance, no silencio.');
    $upsert(798, '03 — La Pieza Terminada');
    $upsert(799, 'Entregada e instalada por el equipo que la construyó, terminada para quedarse como si siempre hubiera estado ahí.');

    // ── cta-forjado.php ──────────────────────────────────────────────────
    $upsert(800, 'Iniciar un proyecto');
    $upsert(801, 'Forjemos');
    $upsert(802, 'algo duradero.');
    $upsert(803, 'Cuéntenos qué está construyendo o la pieza que tiene en mente. Respondemos en 24 horas — sin presión, sin cotización genérica.');
    $upsert(804, 'Solicitar una comisión →');
    $upsert(805, 'WhatsApp al estudio');

    // ── client-logos.php ─────────────────────────────────────────────────
    $upsert(806, 'De confianza para arquitectos, diseñadores y promotores');

    update_option('tma_brand_translations_v1', true);
}
