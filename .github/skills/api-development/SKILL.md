---
name: api-development
description: "Develop REST API endpoints for the tma-panel WordPress plugin. Covers route registration, validation, permissions, error handling, and TDD patterns for API development."
argument-hint: "Endpoint description (e.g., 'GET /leads/{id}/notes — list notes for a lead')"
---

# API Development — Thor Metal Art

## Cuándo Usar

- Agregar nuevos endpoints al plugin tma-panel
- Modificar endpoints existentes en `class-tma-panel-api.php`
- Crear validación de input para endpoints
- Implementar permisos granulares por ruta
- Escribir tests para endpoints REST

## Contexto

- **Namespace:** `tma-panel/v1`
- **Plugin:** `data/wordpress/wp-content/plugins/tma-panel/`
- **API class:** `includes/class-tma-panel-api.php`
- **Roles:** `tma_admin` (8 capabilities), `tma_client` (5 capabilities)
- **Tests:** `tests/test-panel-004-api.sh`

## Procedimiento: Nuevo Endpoint

### Fase 1: RED — Test Primero

Crear test bash en `tests/` que valide el endpoint (debe FALLAR):

```bash
#!/bin/bash
set -e

PASS=0
FAIL=0

assert_eq() {
    if [[ "$1" == "$2" ]]; then
        echo "  ✅ PASS: $3"
        ((PASS++))
    else
        echo "  ❌ FAIL: $3 (expected '$2', got '$1')"
        ((FAIL++))
    fi
}

assert_contains() {
    if echo "$1" | grep -q "$2"; then
        echo "  ✅ PASS: $3"
        ((PASS++))
    else
        echo "  ❌ FAIL: $3 (expected to contain '$2')"
        ((FAIL++))
    fi
}

# Test: endpoint returns 200 with valid auth
RESPONSE=$(docker exec tma_dev_wordpress wp eval '
    $request = new WP_REST_Request("GET", "/tma-panel/v1/{route}");
    $request->set_param("param", "value");
    wp_set_current_user(1);
    $response = rest_do_request($request);
    echo json_encode([
        "status" => $response->get_status(),
        "data"   => $response->get_data(),
    ]);
' --allow-root 2>/dev/null)

STATUS=$(echo "$RESPONSE" | php -r 'echo json_decode(file_get_contents("php://stdin"))->status;')
assert_eq "$STATUS" "200" "GET /{route} returns 200"

# Test: endpoint rejects unauthenticated request
RESPONSE_NOAUTH=$(docker exec tma_dev_wordpress wp eval '
    $request = new WP_REST_Request("GET", "/tma-panel/v1/{route}");
    wp_set_current_user(0);
    $response = rest_do_request($request);
    echo $response->get_status();
' --allow-root 2>/dev/null)

assert_eq "$RESPONSE_NOAUTH" "401" "GET /{route} rejects unauthenticated"

echo ""
echo "Results: $PASS passed, $FAIL failed"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
```

Ejecutar: `bash tests/test-{scope}-{num}-{description}.sh` → debe FALLAR.

### Fase 2: GREEN — Implementar Endpoint

Agregar route en `class-tma-panel-api.php` dentro de `register_routes()`:

```php
register_rest_route(
    'tma-panel/v1',
    '/{route}',
    array(
        'methods'             => 'GET',
        'callback'            => array( $this, 'get_{resource}' ),
        'permission_callback' => function () {
            return is_user_logged_in() && current_user_can( 'tma_view_panel' );
        },
        'args'                => array(
            'param' => array(
                'required'          => false,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function ( $value ) {
                    return is_string( $value ) && strlen( $value ) <= 200;
                },
            ),
        ),
    )
);
```

Implementar callback:

```php
public function get_{resource}( WP_REST_Request $request ) {
    global $wpdb;

    $param = $request->get_param( 'param' );

    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}panel_{table} WHERE column = %s ORDER BY created_at DESC",
            $param
        )
    );

    if ( null === $results ) {
        return new WP_REST_Response(
            array( 'message' => 'Database error' ),
            500
        );
    }

    return new WP_REST_Response( $results, 200 );
}
```

Ejecutar test → debe PASAR.

### Fase 3: REFACTOR

- Extraer validación compleja a método privado
- Agregar filtros WordPress si el endpoint necesita extensibilidad
- Optimizar query (índices, paginación)
- Ejecutar tests → deben SEGUIR pasando

## Checklist Obligatorio

Cada endpoint DEBE cumplir:

- [ ] `permission_callback` con capability check — NUNCA `__return_true`
- [ ] Todos los parámetros con `sanitize_callback`
- [ ] Queries con `$wpdb->prepare()` — NUNCA interpolación directa
- [ ] Return `WP_REST_Response` con HTTP status code correcto
- [ ] Test bash con assert para auth y no-auth
- [ ] Validación de parámetros requeridos

## Patrones de Referencia

- [Patrones de endpoint existentes](./references/api-patterns.md)
- [Leads instructions](../../instructions/leads.instructions.md) para ejemplo de CRUD completo
- [TMA Panel instructions](../../instructions/tma-panel.instructions.md) para schema y capabilities
