#!/usr/bin/env bash
set -euo pipefail

ROOT="/srv/stacks/thormetalart-dev"
WP_CONTAINER="tma_dev_wordpress"
CONTACT="${ROOT}/data/wordpress/wp-content/mu-plugins/tma-contact-form.php"
SMTP="${ROOT}/data/wordpress/wp-content/mu-plugins/tma-smtp.php"
PASS=0
FAIL=0

pass() { PASS=$((PASS + 1)); echo "  PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "  FAIL: $1"; }

echo "TICKET-LEAD-006 - SMTP observability"

if grep -q 'notification_status' "${CONTACT}" && grep -q 'notification_error' "${CONTACT}"; then
  pass "lead schema records notification outcome"
else
  fail "lead schema has no notification outcome fields"
fi

curl -kfsS 'https://dev.thormetalart.com/contact/' >/dev/null
columns=$(docker compose --project-directory "${ROOT}" exec -T mysql sh -c \
  'MYSQL_PWD="$MYSQL_PASSWORD" mysql -u "$MYSQL_USER" "$MYSQL_DATABASE" -Nse "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='\''tma_tma_leads'\'' AND COLUMN_NAME IN ('\''notification_error'\'', '\''notification_status'\'', '\''notified_at'\'');"')
if [[ "${columns}" -eq 3 ]]; then
  pass "notification outcome columns exist in DEV"
else
  fail "DEV lead table has ${columns} of 3 notification columns"
fi

if grep -q 'function tma_notify_lead' "${CONTACT}" && grep -q 'tma_record_lead_notification_status' "${CONTACT}"; then
  pass "submission has a single observable notification path"
else
  fail "submission does not record one notification result"
fi

if grep -q 'wp_mail_failed' "${SMTP}" && grep -q 'tma_dev_mail_capture' "${SMTP}"; then
  pass "SMTP layer reports failures and captures DEV mail"
else
  fail "SMTP layer lacks failure reporting or DEV capture"
fi

capture=$(docker exec "${WP_CONTAINER}" php -r 'require "/var/www/html/wp-load.php"; delete_option("tma_dev_mail_capture"); $sent=wp_mail("dev-null@example.invalid", "[TMA Test] controlled", "private body"); $items=get_option("tma_dev_mail_capture", []); $last=end($items); echo wp_json_encode([$sent, $last["recipient_count"] ?? 0, isset($last["to"]), isset($last["subject"]), isset($last["subject_hash"]), isset($last["message"]), isset($last["message_hash"])]);')
if [[ "${capture}" == '[true,1,false,false,true,false,true]' ]]; then
  pass "DEV captures hashed metadata without sending or storing PII/message body"
else
  fail "DEV transport is not safely observable: ${capture}"
fi

lead_order=$(grep -nE '\$lead_id = \(int\) \$wpdb->insert_id;|do_action\(' "${CONTACT}" | tail -2 | cut -d: -f1 | paste -sd ' ' -)
if [[ "${lead_order}" =~ ^([0-9]+)\ ([0-9]+)$ && "${BASH_REMATCH[1]}" -lt "${BASH_REMATCH[2]}" ]]; then
  pass "lead id is captured before extension hooks can overwrite insert_id"
else
  fail "lead id is not captured before extension hooks"
fi

if grep -q 'tma_retry_notification' "${CONTACT}" && grep -q 'check_admin_referer' "${CONTACT}"; then
  pass "failed notifications have a nonce-protected retry path"
else
  fail "failed notifications cannot be retried safely"
fi

echo "RESULTS: ${PASS} pass / ${FAIL} fail"
[[ "${FAIL}" -eq 0 ]]
