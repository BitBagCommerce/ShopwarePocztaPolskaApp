## Installation in Shopware

You can install the app in a variety of ways.

1. Install the app from [the Shopware store](https://store.shopware.com/en/extensions/?p=1&o=12&n=21&c=2069&shopwareVersion=6).
2. Install the app from [the back office](https://docs.shopware.com/en/shopware-6-en/extensions/myextensions).

3. Install the app from the command line.
    1. Create a new directory inside custom/apps called BitBagShopwarePocztaPolskaApp.
    2. Copy all files from app/ into the newly created directory
    3. Install the app.
       ```bash
       $ bin/console app:install BitBagShopwarePocztaPolskaApp
       ```
    4. Activate the app.
       ```bash
         $ bin/console app:activate BitBagShopwarePocztaPolskaApp
       ```
## Run symfony sever
1. Install dependencies
```bash
$ composer install
```
2. Run basic setup server:
```bash
$ symfony server:start
```

Other deployment options largely depend on your architecture.
