clear-cache:
	php artisan clear-compiled
	php artisan cache:clear
	php artisan view:clear
	php artisan config:clear
	php artisan event:clear
	php artisan optimize:clear
	php artisan route:clear



