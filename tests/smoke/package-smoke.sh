#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
VERSION="$(tr -d '[:space:]' < "$ROOT/VERSION")"
[ "$VERSION" = "0.1.1" ]
grep -q '<version>0.1.1</version>' "$ROOT/src/com_xdecarophotos/admin/xdecarophotos.xml"
grep -q '<version>0.1.1</version>' "$ROOT/package/pkg_xdecarophotos/pkg_xdecarophotos.xml"
ZIP="$($ROOT/build/build.sh)"
[ -f "$ZIP" ]
unzip -l "$ZIP" | grep -q 'com_xdecarophotos.zip'
unzip -l "$ZIP" | grep -q 'pkg_xdecarophotos.xml'
TMP="$(mktemp -d)"; trap 'rm -rf "$TMP"' EXIT
unzip -q "$ZIP" -d "$TMP/pkg"
unzip -q "$TMP/pkg/com_xdecarophotos.zip" -d "$TMP/component"
[ -f "$TMP/component/xdecarophotos.xml" ]
[ -f "$TMP/component/admin/access.xml" ]
[ -f "$TMP/component/site/services/provider.php" ]
[ -f "$TMP/component/media/joomla.asset.json" ]
grep -q '<version>0.1.1</version>' "$TMP/component/xdecarophotos.xml"
echo "PASS package-smoke"
