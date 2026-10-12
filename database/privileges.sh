#!/bin/bash
# Principe du moindre privilège : l'utilisateur applicatif ne garde que les droits
# dont le code PHP a besoin (lecture/écriture des données, aucune modification de structure).
# Exécuté une seule fois, au premier démarrage (volume vide), après 01-schema.sql et 02-fixtures.sql.
set -e

mariadb --protocol=socket -uroot -p"${MARIADB_ROOT_PASSWORD}" <<EOSQL
REVOKE ALL PRIVILEGES ON \`${MARIADB_DATABASE}\`.* FROM '${MARIADB_USER}'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON \`${MARIADB_DATABASE}\`.* TO '${MARIADB_USER}'@'%';
FLUSH PRIVILEGES;
EOSQL
