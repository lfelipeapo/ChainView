#!/bin/bash
set -euo pipefail

# idempotente — aplica as regras que você quer
ufw --force reset || true
ufw default allow incoming || true
ufw default allow outgoing || true
ufw allow in on lo || true

for p in 80 443 8000 5432 20000 3000 \
         8080 8081 8082 8083 8084 8085 8086 8087 8088 8089; do
  ufw allow "${p}/tcp" || true
done

ufw --force enable || {
  echo "[ufw-start] ufw enable falhou — verifique se container foi iniciado com NET_ADMIN/privileged"
  exit 0
}

echo "[ufw-start] OK"
exec "$@"
