#!/usr/bin/env bash
set -euo pipefail

# Usage:
#   HOSTS="simdigetir.com,www.simdigetir.com" BASE_DIR="$HOME/domains/simdigetir.com" \
#   bash scripts/release/hostinger-opcache-reset.sh

BASE_DIR="${BASE_DIR:-$HOME/domains/simdigetir.com}"
PUBLIC_DIR="${PUBLIC_DIR:-$BASE_DIR/public_html}"
HOSTS="${HOSTS:-simdigetir.com,www.simdigetir.com}"
REQUEST_TIMEOUT_SECONDS="${REQUEST_TIMEOUT_SECONDS:-20}"

if [[ ! -d "$PUBLIC_DIR" ]]; then
  echo "ERROR: public directory not found: $PUBLIC_DIR" >&2
  exit 1
fi

TIMESTAMP="$(date +%Y%m%d%H%M%S)"
PROBE_FILE=".opcache-reset-${TIMESTAMP}-$$.php"
PROBE_PATH="$PUBLIC_DIR/$PROBE_FILE"

cleanup() {
  rm -f "$PROBE_PATH"
}

trap cleanup EXIT

cat > "$PROBE_PATH" <<'PHP'
<?php
header('Content-Type: text/plain; charset=utf-8');
$result = function_exists('opcache_reset') ? (opcache_reset() ? 'true' : 'false') : 'not_available';
echo 'host=' . ($_SERVER['HTTP_HOST'] ?? '') . PHP_EOL;
echo 'opcache_reset=' . $result . PHP_EOL;
PHP

IFS=',' read -r -a HOST_ARRAY <<< "$HOSTS"

if [[ ${#HOST_ARRAY[@]} -eq 0 ]]; then
  echo "ERROR: HOSTS is empty." >&2
  exit 1
fi

for raw_host in "${HOST_ARRAY[@]}"; do
  host="$(echo "$raw_host" | xargs)"
  if [[ -z "$host" ]]; then
    continue
  fi

  url="https://$host/$PROBE_FILE"
  echo "[opcache] calling $url"
  response="$(curl -fsS --max-time "$REQUEST_TIMEOUT_SECONDS" "$url")"
  echo "$response"

  if ! grep -q "opcache_reset=true" <<< "$response"; then
    echo "ERROR: opcache reset failed for host: $host" >&2
    exit 1
  fi
done

echo "[opcache] reset successful for all hosts."
