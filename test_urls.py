import urllib.request
import re

urls = [
    "https://thormetalart.com/portfolio/",
    "https://thormetalart.com/es/portfolio/",
    "https://thormetalart.com/portfolio/lobby-art-commission-downtown-miami/",
    "https://thormetalart.com/es/portfolio/lobby-art-commission-downtown-miami/",
    "https://thormetalart.com/art-commissions/",
    "https://thormetalart.com/es/art-commissions/"
]

def get_meta(html, property_name):
    match = re.search(f'property="{property_name}"\s+content="([^"]+)"', html)
    if not match:
        match = re.search(f'content="([^"]+)"\s+property="{property_name}"', html)
    return match.group(1) if match else "N/A"

def get_link_rel(html, rel, lang=None):
    if lang:
        pattern = f'<link\s+rel="{rel}"\s+hreflang="{lang}"\s+href="([^"]+)"'
    else:
        pattern = f'<link\s+rel="{rel}"\s+href="([^"]+)"'
    match = re.search(pattern, html)
    return match.group(1) if match else "N/A"

def check_url(url):
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req, timeout=10) as response:
            status = response.getcode()
            html = response.read().decode('utf-8', errors='ignore')
            
            title_match = re.search('<title>(.*?)</title>', html, re.IGNORECASE | re.DOTALL)
            title = title_match.group(1).strip() if title_match else "N/A"
            
            og_locale = get_meta(html, "og:locale")
            canonical = get_link_rel(html, "canonical")
            hreflang_en = get_link_rel(html, "alternate", "en")
            hreflang_es = get_link_rel(html, "alternate", "es")
            
            fail_reasons = []
            if status != 200:
                fail_reasons.append(f"Status {status}")
            
            if "/es/" in url:
                if "/es/es/" in html:
                    fail_reasons.append("Contains /es/es/")
            
            pass_fail = "PASS" if not fail_reasons else f"FAIL ({', '.join(fail_reasons)})"
            
            return {
                "URL": url,
                "Status": status,
                "Title": title,
                "Locale": og_locale,
                "Canonical": canonical,
                "Hreflang EN": hreflang_en,
                "Hreflang ES": hreflang_es,
                "Result": pass_fail,
                "HTML": html
            }
    except Exception as e:
        return {"URL": url, "Result": f"FAIL (Error: {str(e)})", "Status": "ERR"}

results = [check_url(u) for u in urls]

print(f"{'URL':<70} | {'Stat':<4} | {'Locale':<6} | {'Result':<10}")
print("-" * 100)
for res in results:
    print(f"{res.get('URL', 'N/A')[:70]:<70} | {res.get('Status', 'N/A'):<4} | {res.get('Locale', 'N/A'):<6} | {res.get('Result', 'N/A'):<10}")

print("\nES Key Texts Check:")
for res in results:
    u = res['URL']
    if "/es/" in u and "HTML" in res:
        html = res['HTML']
        found = [t for t in ['Nuestro Trabajo', 'Todos', 'Portafolio', 'Inicio'] if t in html]
        missing = [t for t in ['Nuestro Trabajo', 'Todos', 'Portafolio', 'Inicio'] if t not in html]
        print(f"{u}: Found: {found}, Missing: {missing}")
