FRONT
node v20
cd FE && npm i && npm run build
поменять в .env на бекенд url

BACKEND
1. в docker/ngrok скопировать ngrok-example.yml и вставить ngrok.yml
2. в файле ngrok.yml изменить authtoken и domain

.env изменить:
1. CANONICAL_HOST - ваш домен
2. SHOPIFY_API_KEY - api key из shopify partners
3. SHOPIFY_SECRET_KEY - secret_key из shopify partners
4. SHOPIFY_REDIRECTION_URI - redirect url из shopify partners
5. SHOPIFY_API_VERSION - api versilt из shopify partners
6. SHOPIFY_FRONT_URL - урл фронта


Запустить приложения: make up
