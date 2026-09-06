#!/bin/sh
# Regenerates /etc/nginx/yc-cdn-ips.conf from Yandex Cloud CDN's official,
# published edge IP list, and reloads nginx if the result is valid.
#
# Run on the VPS itself (not inside Docker — this is for the bare-metal edge
# nginx in docker/nginx/edge.conf). Run it once manually before the first
# `nginx -t`/start (edge.conf includes this file unconditionally, so nginx
# won't start without it), then drop it in cron to keep the list current:
#
#   sudo cp refresh-yc-cdn-ips.sh /usr/local/bin/
#   sudo chmod +x /usr/local/bin/refresh-yc-cdn-ips.sh
#   sudo /usr/local/bin/refresh-yc-cdn-ips.sh          # run once now
#   echo '17 4 * * * root /usr/local/bin/refresh-yc-cdn-ips.sh >> /var/log/yc-cdn-ips-refresh.log 2>&1' \
#     | sudo tee /etc/cron.d/refresh-yc-cdn-ips
#
# Source: https://tech.cdn.yandex.net/prefixes/yc.json (a "prefixes" array of
# IPv4 CIDR strings — this is the source referenced by Yandex's own CDN
# tooling, not a copy that can silently go stale in this repo).

set -eu

SOURCE_URL="https://tech.cdn.yandex.net/prefixes/yc.json"
OUTPUT_FILE="/etc/nginx/yc-cdn-ips.conf"
TMP_FILE="$(mktemp)"
TMP_NEW_PREFIXES="$(mktemp)"
TMP_OLD_PREFIXES="$(mktemp)"
trap 'rm -f "$TMP_FILE" "$TMP_NEW_PREFIXES" "$TMP_OLD_PREFIXES"' EXIT

JSON="$(curl -fsS --max-time 15 "$SOURCE_URL")" || {
    echo "refresh-yc-cdn-ips: failed to fetch $SOURCE_URL — leaving $OUTPUT_FILE untouched" >&2
    exit 1
}

{
    echo "# Generated $(date -u +%FT%TZ) by refresh-yc-cdn-ips.sh"
    echo "# Source: $SOURCE_URL — do not edit by hand, it will be overwritten."
    echo "$JSON" | grep -oE '"[0-9]{1,3}(\.[0-9]{1,3}){3}/[0-9]{1,2}"' \
        | tr -d '"' \
        | sed 's/^/set_real_ip_from /; s/$/;/'
} > "$TMP_FILE"

PREFIX_COUNT="$(grep -c '^set_real_ip_from' "$TMP_FILE" || true)"

if [ "$PREFIX_COUNT" -lt 10 ]; then
    echo "refresh-yc-cdn-ips: only found $PREFIX_COUNT prefixes, that looks wrong — leaving $OUTPUT_FILE untouched" >&2
    exit 1
fi

grep '^set_real_ip_from' "$TMP_FILE" > "$TMP_NEW_PREFIXES"
if [ -f "$OUTPUT_FILE" ]; then
    grep '^set_real_ip_from' "$OUTPUT_FILE" > "$TMP_OLD_PREFIXES" 2>/dev/null || true
    if cmp -s "$TMP_NEW_PREFIXES" "$TMP_OLD_PREFIXES"; then
        echo "refresh-yc-cdn-ips: no change ($PREFIX_COUNT prefixes)"
        exit 0
    fi
fi

cp "$TMP_FILE" "$OUTPUT_FILE"
echo "refresh-yc-cdn-ips: wrote $PREFIX_COUNT prefixes to $OUTPUT_FILE"

if ! command -v nginx >/dev/null 2>&1; then
    echo "refresh-yc-cdn-ips: nginx not installed yet, skipping reload (first-run bootstrap)"
    exit 0
fi

if nginx -t >/dev/null 2>&1; then
    if systemctl is-active --quiet nginx 2>/dev/null; then
        systemctl reload nginx
        echo "refresh-yc-cdn-ips: nginx reloaded"
    else
        echo "refresh-yc-cdn-ips: nginx not running yet, skipping reload (first-run bootstrap)"
    fi
else
    echo "refresh-yc-cdn-ips: nginx -t failed against the new list — check the rest of your config" >&2
    exit 1
fi
