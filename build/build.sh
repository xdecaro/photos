#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
VERSION="$(tr -d '\r\n' < "$ROOT/VERSION")"
DIST="$ROOT/build/dist"; WORK="$ROOT/build/.tmp"; rm -rf "$WORK" "$DIST"; mkdir -p "$WORK/component" "$WORK/package" "$DIST"
cp -R "$ROOT/src/com_xdecarophotos/admin" "$WORK/component/admin"
cp -R "$ROOT/src/com_xdecarophotos/site" "$WORK/component/site"
cp -R "$ROOT/src/com_xdecarophotos/media" "$WORK/component/media"
cp "$ROOT/src/com_xdecarophotos/admin/xdecarophotos.xml" "$WORK/component/xdecarophotos.xml"
(cd "$WORK/component" && TZ=UTC find . -exec touch -t 202609260000 {} + && zip -X -qr "$WORK/package/com_xdecarophotos.zip" .)
cp "$ROOT/package/pkg_xdecarophotos/pkg_xdecarophotos.xml" "$WORK/package/pkg_xdecarophotos.xml"
cp "$ROOT/package/pkg_xdecarophotos/script.php" "$WORK/package/script.php"
(cd "$WORK/package" && TZ=UTC find . -exec touch -t 202609260000 {} + && zip -X -qr "$DIST/pkg_xdecarophotos_${VERSION}.zip" .)
echo "$DIST/pkg_xdecarophotos_${VERSION}.zip"
