#!/usr/bin/env bash
set -euo pipefail

# Usage:
#   TARGET_RELEASE=laravel_release_v1_0_7 \
#   BASE_DIR="$HOME/domains/simdigetir.com" \
#   bash scripts/release/hostinger-atomic-cutover.sh

BASE_DIR="${BASE_DIR:-$HOME/domains/simdigetir.com}"
PUBLIC_DIR="${PUBLIC_DIR:-$BASE_DIR/public_html}"
TARGET_RELEASE="${TARGET_RELEASE:-}"
HOSTS="${HOSTS:-simdigetir.com,www.simdigetir.com}"

if [[ -z "$TARGET_RELEASE" ]]; then
  echo "ERROR: TARGET_RELEASE is required." >&2
  echo "Example: TARGET_RELEASE=laravel_release_v1_0_7 bash scripts/release/hostinger-atomic-cutover.sh" >&2
  exit 1
fi

TARGET_DIR="$BASE_DIR/$TARGET_RELEASE"
CURRENT_LINK="$BASE_DIR/current"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OPCACHE_SCRIPT="$SCRIPT_DIR/hostinger-opcache-reset.sh"

if [[ ! -d "$TARGET_DIR" ]]; then
  echo "ERROR: target release dir not found: $TARGET_DIR" >&2
  exit 1
fi

if [[ ! -d "$PUBLIC_DIR" ]]; then
  echo "ERROR: public dir not found: $PUBLIC_DIR" >&2
  exit 1
fi

if [[ ! -f "$TARGET_DIR/public/index.php" ]]; then
  echo "ERROR: release public/index.php not found: $TARGET_DIR/public/index.php" >&2
  exit 1
fi

if [[ ! -x "$OPCACHE_SCRIPT" ]]; then
  chmod +x "$OPCACHE_SCRIPT"
fi

echo "[cutover] switching current symlink -> $TARGET_RELEASE"
ln -sfn "$TARGET_RELEASE" "$CURRENT_LINK"

echo "[cutover] syncing public assets"
rsync -a --delete --exclude='index.php' "$TARGET_DIR/public/" "$PUBLIC_DIR/"
ln -sfn ../current/storage/app/public "$PUBLIC_DIR/storage"

echo "[cutover] resetting php opcache for hosts: $HOSTS"
HOSTS="$HOSTS" BASE_DIR="$BASE_DIR" bash "$OPCACHE_SCRIPT"

echo "[cutover] done."
