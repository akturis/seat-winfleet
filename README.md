# seat-winfleet
Plugin to gamble prizes for fleet member


## Quick Installation:

In your seat directory (By default:  /var/www/seat), type the following:

```
php artisan down

composer require akturis/seat-winfleet
php artisan vendor:publish --force
php artisan migrate

php artisan up
```

And now, when you log into 'Seat', you should see a 'Win Fleet' link on the left.


