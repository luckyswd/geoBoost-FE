#!/bin/bash
# Обновляет базу данных GeoLite2

# Путь к базе данных GeoLite2 в вашем проекте
DB_PATH="../files/geoip/GeoLite2-City.mmdb"

# Лицензионный ключ MaxMind (необходим для автоматического скачивания)
LICENSE_KEY="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"

# URL для загрузки GeoLite2 базы данных
URL="https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=$LICENSE_KEY&suffix=tar.gz"

# Папка для временных файлов
TEMP_DIR="../var/tmp/geolite2_update"
mkdir -p $TEMP_DIR

# Загрузка базы данных
curl -L -o $TEMP_DIR/GeoLite2-City.tar.gz $URL

# Распаковка архива
tar -xzf $TEMP_DIR/GeoLite2-City.tar.gz -C $TEMP_DIR

# Копирование новой версии базы данных
cp $TEMP_DIR/GeoLite2-City_*/GeoLite2-City.mmdb $DB_PATH

# Очистка временных файлов
rm -rf $TEMP_DIR


echo "GeoLite2 база данных обновлена"

# Для крона
  # 0 0 * * 1 /path/to/update-geolite2.sh