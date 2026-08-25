#!/bin/bash
set -e

cd "$(dirname "$0")/.."

ZIP="thenewmarket.zip"

rm -f "$ZIP"

zip -r "$ZIP" . \
  -x ".git/*" \
  -x ".env" \
  -x ".env.*" \
  -x "install.lock" \
  -x "*.zip" \
  -x "storage/logs/*" \
  -x "storage/cache/*" \
  -x "storage/uploads/*" \
  -x ".DS_Store" \
  -x "*_cookies.txt"

echo "Created $ZIP"
