APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:tfKZIdo3Wr/UtMK5nVP7qV0T/M2qVAP8wGiHnBVUfO0=
APP_DEBUG=true
APP_URL=http://localhost:8001
APP_SECURE=false    



APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webrapis
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=reverb
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

API_GEOCODE = "67d1adc970fff774536291vxg4ffdf1"

BROADCAST_DRIVER=reverb

BROADCAST_CONNECTION=pusher # Pastikan ini diatur ke pusher

# Kredensial Pusher Anda
PUSHER_APP_ID=1853347
PUSHER_APP_KEY=b593a39864a73b386f5a
PUSHER_APP_SECRET=0e23757ec7f6b3937ff7
PUSHER_APP_CLUSTER=ap1

# Opsi Pusher default (biasanya tidak perlu diubah jika pakai cluster)
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https

# Variabel untuk Vite (digunakan di Javascript)
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

REVERB_APP_ID=361454
REVERB_APP_KEY=mydofmf9vou7y4ahpwml
REVERB_APP_SECRET=zpqyiokwv8hj1u2z62d8
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
