#!/usr/bin/env bash
# Adds magento2-ce.local -> 172.17.0.1 mapping to running Selenium containers' /etc/hosts
# Usage: sudo ./scripts/add-selenium-hosts.sh

set -euo pipefail
TARGET_HOSTNAME="magento2-ce.local"
TARGET_IP="172.17.0.1"

# Find running containers with 'selenium' or 'selenium' in their image/name
containers=$(docker ps --format '{{.ID}} {{.Image}} {{.Names}}' | grep -Ei 'selenium|selenium' || true)
if [ -z "$containers" ]; then
  echo "No running selenium containers found via 'docker ps'. If Selenium is remote, update its hosts manually."
  exit 1
fi

echo "Found Selenium-related containers:"
echo "$containers"

while read -r id image name; do
  echo "Patching /etc/hosts inside container $id ($name) -> $TARGET_HOSTNAME=$TARGET_IP"
  # Back up original hosts file inside container
  # Run as root inside the container to modify /etc/hosts
  # Pull current hosts from container and construct a new hosts file on host
  tmpfile=$(mktemp)
  docker exec "$id" cat /etc/hosts > "$tmpfile" || true
  # Filter out any existing mapping for the hostname, then append our mapping
  grep -v -E "(^|\s)$TARGET_HOSTNAME(\s|$)" "$tmpfile" > "${tmpfile}.filtered" || true
  echo "$TARGET_IP $TARGET_HOSTNAME" >> "${tmpfile}.filtered"
  # Copy the filtered hosts file into the container (requires root to overwrite /etc/hosts)
  docker cp "${tmpfile}.filtered" "$id":/tmp/hosts.mftf || true
  docker exec -u 0 "$id" sh -c "cp /etc/hosts /etc/hosts.mftf_backup || true && cat /tmp/hosts.mftf > /etc/hosts && rm -f /tmp/hosts.mftf"
  rm -f "$tmpfile" "${tmpfile}.filtered" || true
  echo "Patched $id"
done <<< "$containers"

echo "Done. Re-run MFTF doctor now: vendor/bin/mftf doctor --verbose"