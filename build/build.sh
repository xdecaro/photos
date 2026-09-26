#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
VERSION="$(tr -d '[:space:]' < "$ROOT/VERSION")"
DIST="$ROOT/dist"; WORK="$ROOT/build/.tmp"
rm -rf "$WORK" "$DIST"; mkdir -p "$WORK/component/admin" "$WORK/component/site" "$WORK/component/media" "$WORK/package" "$DIST"
cp "$ROOT/src/com_xdecarophotos/admin/xdecarophotos.xml" "$WORK/component/xdecarophotos.xml"
for item in access.xml config.xml forms language services sql src tmpl; do [ -e "$ROOT/src/com_xdecarophotos/admin/$item" ] && cp -R "$ROOT/src/com_xdecarophotos/admin/$item" "$WORK/component/admin/"; done
cp -R "$ROOT/src/com_xdecarophotos/site/"* "$WORK/component/site/"
cp -R "$ROOT/src/com_xdecarophotos/media/"* "$WORK/component/media/"
(cd "$WORK/component" && zip -X -qr "$WORK/package/com_xdecarophotos.zip" .)
cp "$ROOT/package/pkg_xdecarophotos/pkg_xdecarophotos.xml" "$WORK/package/"
cp "$ROOT/package/pkg_xdecarophotos/script.php" "$WORK/package/"
(cd "$WORK/package" && zip -X -qr "$DIST/pkg_xdecarophotos_${VERSION}.zip" .)
echo "$DIST/pkg_xdecarophotos_${VERSION}.zip"
