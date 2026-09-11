# SMTP and Release Runbook / Runbook de SMTP y Release

**Last updated / Última actualización:** 2026-09-11

## Mail Architecture / Arquitectura de correo

- Sender: `contact@thormetalart.com` through Hostinger SMTP.
- Credentials come only from environment variables: `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`, `SMTP_FROM`, `SMTP_FROM_NAME`.
- Development never opens an external SMTP connection. `pre_wp_mail` records bounded metadata in `tma_dev_mail_capture` and returns success.
- Captures contain recipient, subject, UTC timestamp, and a SHA-256 message hash. They never contain the body, credentials, or lead message.
- Each lead generates one notification. Premium services use the priority subject instead of sending a second email.
- `tma_tma_leads.notification_status` records `pending`, `sent`, or `failed`; failed attempts expose a nonce-protected retry in WordPress admin.
- `wp_mail_failed` logs only the sanitized error code.

## Controlled Production Test / Prueba controlada en PROD

1. Obtain explicit approval for the recipient and maintenance window.
2. Back up PROD with `make backup` before changing configuration or database state.
3. Verify the SMTP variables are present without printing their values.
4. Submit one uniquely named test lead from the public contact form.
5. Confirm `notification_status=sent`, Hostinger SMTP acceptance, and inbox or spam receipt.
6. Record timestamp, recipient approval, outcome, and lead ID. Never record credentials or message body.
7. Do not send a real test to a client address without explicit authorization.

## DEV to PROD Release / Release DEV a PROD

1. Run `make test-all`, `make lint`, `make lint-phpstan`, and `make test-e2e` in DEV.
2. Review the Playwright evidence under `test-results/evidence/` for EN/ES and desktop/mobile.
3. Create database and file backups in PROD.
4. Promote only versioned application files. Never copy `.env`, database volumes, uploads, caches, or DEV records.
5. Trigger idempotent WordPress migrations with one public request.
6. Run smoke tests and Playwright with `TMA_BASE_URL=https://thormetalart.com`.
7. Complete the single approved mail delivery test only if its window was authorized.

## Rollback / Reversión

Restore the pre-release application files, then restore the database backup only when a migration changed persistent data incompatibly. Verify home, contact, five services, blog, portfolio, EN/ES, and mail status after rollback.
