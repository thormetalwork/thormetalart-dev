---
description: "Perform a security audit of the Docker stack, WordPress configuration, and infrastructure"
agent: "security-auditor"
---
Perform a comprehensive security audit of the Thor Metal Art infrastructure:

## Audit Scope

### 1. Docker & Network Security
- Review `docker-compose.yml` for network exposure
- Verify MySQL is not exposed to `0.0.0.0`
- Check Traefik labels and routing security
- Review container resource limits

### 2. Credentials & Secrets
- Verify `.env` is in `.gitignore`
- Check for hardcoded credentials in any file
- Review backup files for sensitive data exposure
- Scan scripts for credential leaks

### 3. WordPress Hardening
- Check `wp-config.php` for debug mode, security keys
- Verify table prefix is non-default
- Review file permissions
- Check for unnecessary exposed endpoints (XML-RPC, REST API)

### 4. Redis Security
- Verify Redis is not exposed externally
- Check for authentication configuration
- Review memory limits

### 5. Backup Security
- Verify backups are not publicly accessible
- Check backup rotation is working
- Review backup script permissions

## Output
Produce a severity-ranked report (Critical → Low) with specific remediation steps for each finding.
