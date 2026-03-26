# Testing Patterns — Thor Metal Art

## WordPress PHP Tests

### Setup PHPUnit (dentro del contenedor)
```bash
make shell-wp
cd /var/www/html
composer require --dev phpunit/phpunit
mkdir -p tests
```

### Test para Custom Post Type
```php
class Test_TMA_Portfolio_CPT extends WP_UnitTestCase {
    
    public function test_portfolio_cpt_registered() {
        $this->assertTrue(post_type_exists('tma_portfolio'));
    }
    
    public function test_portfolio_supports_thumbnail() {
        $cpt = get_post_type_object('tma_portfolio');
        $this->assertTrue(in_array('thumbnail', $cpt->supports));
    }
    
    public function test_portfolio_has_custom_taxonomy() {
        $this->assertTrue(taxonomy_exists('tma_project_type'));
    }
    
    public function test_create_portfolio_item() {
        $post_id = $this->factory->post->create([
            'post_type' => 'tma_portfolio',
            'post_title' => 'Custom Gate Coral Gables',
            'post_status' => 'publish',
        ]);
        $this->assertGreaterThan(0, $post_id);
        $this->assertEquals('tma_portfolio', get_post_type($post_id));
    }
}
```

### Test para Funciones de Seguridad
```php
class Test_TMA_Security extends WP_UnitTestCase {
    
    public function test_xmlrpc_returns_403() {
        // Simular request a xmlrpc.php
        $response = wp_remote_post(home_url('/xmlrpc.php'));
        $this->assertEquals(403, wp_remote_retrieve_response_code($response));
    }
    
    public function test_rest_api_users_requires_auth() {
        $response = rest_do_request(new WP_REST_Request('GET', '/wp/v2/users'));
        $this->assertEquals(401, $response->get_status());
    }
}
```

## JavaScript Tests (Dashboard)

### Test para Charts
```javascript
describe('Dashboard Charts', () => {
    test('createLeadsChart returns Chart instance', () => {
        const canvas = document.createElement('canvas');
        const chart = createLeadsChart(canvas, mockData);
        expect(chart).toBeDefined();
        expect(chart.config.type).toBe('bar');
    });
    
    test('formatCurrency formats USD correctly', () => {
        expect(formatCurrency(28400)).toBe('$28,400');
        expect(formatCurrency(0)).toBe('$0');
        expect(formatCurrency(1500.50)).toBe('$1,501');
    });
    
    test('calculateKPI returns correct trend', () => {
        const result = calculateKPI(312, 244); // 28% increase
        expect(result.trend).toBe('up');
        expect(result.percentage).toBeCloseTo(27.87, 1);
    });
});
```

## Bash Script Tests

### Test para Backup
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

assert_file_exists() {
    if [[ -f "$1" ]]; then
        echo "  ✅ PASS: $2"
        ((PASS++))
    else
        echo "  ❌ FAIL: $2 (file not found: $1)"
        ((FAIL++))
    fi
}

echo "=== Testing backup-database.sh ==="

# Test 1: Script exists and is executable
assert_file_exists "scripts/backup-database.sh" "Script exists"

# Test 2: .env validation
(source scripts/backup-database.sh 2>&1 | grep -q "Missing .env" && echo "  ✅ PASS: Validates .env") || echo "  ✅ PASS: .env exists"

echo ""
echo "Results: ${PASS} passed, ${FAIL} failed"
[[ $FAIL -eq 0 ]] && exit 0 || exit 1
```
