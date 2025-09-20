#!/usr/bin/env bash
# Quick helper to create compatibility symlinks after `bin/magento setup:static-content:deploy`
# Run from project root. It will:
# - create `pub/static/version<deployed_version>` -> `.`
# - create `pub/static/adminhtml/.../jquery/ui.js` -> `jquery-ui.js` if missing
# - create `pub/static/adminhtml/.../prototype.js` -> `prototype/prototype.js` if missing

set -euo pipefail
ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
STATIC_DIR="$ROOT_DIR/pub/static"

echo "Running post-static fix script (ROOT_DIR=$ROOT_DIR)"

if [ ! -d "$STATIC_DIR" ]; then
  echo "ERROR: static dir not found: $STATIC_DIR" >&2
  exit 1
fi

# 1) create versioned symlink if deployed_version.txt exists
DEPLOYED="$STATIC_DIR/deployed_version.txt"
if [ -f "$DEPLOYED" ]; then
  ver=$(cat "$DEPLOYED" | tr -d '\n')
  if [ -n "$ver" ]; then
    link="$STATIC_DIR/version${ver}"
    if [ ! -e "$link" ]; then
      ln -s . "$link"
      echo "Created version symlink: $link -> ."
    else
      echo "Version symlink already exists: $link"
    fi
  fi
fi

# helper to create symlink if source exists and target missing
mklink_if_needed() {
  src_rel="$1" # relative to dir
  dst_rel="$2"
  base="$3"
  src="$base/$src_rel"
  dst="$base/$dst_rel"
  if [ -e "$src" ] && [ ! -e "$dst" ]; then
    dst_dir=$(dirname "$dst")
    mkdir -p "$dst_dir"
  # create relative symlink (use python relpath for portability)
  pushd "$dst_dir" >/dev/null
    # Compute relative path using python3; guard against unexpected failures
    if command -v python3 >/dev/null 2>&1; then
      rel=$(python3 - <<PY
import os,sys
try:
    src=sys.argv[1]
    dst_dir=sys.argv[2]
    print(os.path.relpath(src, dst_dir))
except Exception:
    sys.exit(2)
PY
"$src" "$dst_dir" 2>/dev/null || true)
      if [ -z "$rel" ]; then
        echo "Could not compute relative path for $src; creating absolute symlink instead"
        ln -s "$src" "$(basename "$dst")"
      else
        ln -s "$rel" "$(basename "$dst")"
      fi
    else
      echo "python3 not found: creating absolute symlink for $dst"
      ln -s "$src" "$(basename "$dst")"
    fi
  popd >/dev/null
    echo "Created symlink: $dst -> $src"
  else
    if [ ! -e "$src" ]; then
      echo "Source missing, skip: $src"
    else
      echo "Target already exists, skip: $dst"
    fi
  fi
}

# 2) ui.js compatibility for adminhtml Magento backend
ADMIN_JQUERY_DIR="adminhtml/Magento/backend/en_US/jquery"
mklink_if_needed "$ADMIN_JQUERY_DIR/jquery-ui.js" "$ADMIN_JQUERY_DIR/ui.js" "$STATIC_DIR"

# 3) prototype.js compatibility (some pages request /prototype.js at root)
mklink_if_needed "adminhtml/Magento/backend/en_US/prototype/prototype.js" "adminhtml/Magento/backend/en_US/prototype.js" "$STATIC_DIR"

echo "Post-static fix complete. Review created symlinks under $STATIC_DIR/adminhtml/Magento/backend/en_US/"

exit 0
