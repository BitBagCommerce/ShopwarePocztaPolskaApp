## Requirements
- Composer 2
- PHP ^7.4
- MySQL server
- Symfony server
- Node ^16.0

## Setting up Shopware Project
1. First, we need to create a Shopware project using the command:
```bash
$ composer create-project shopware/production BaseShopwareProject_6_5
```


2. Next, we should configure the ```.env``` file to establish a connection with the database.


3. The next step is to create the database using the command:
```bash
$ bin/console system:install --create-database --basic-setup
```

4. Now, we can start our Symfony server by using the command:
```bash
$ symfony server:start
```

#### Once our Shopware project is up and running, we can proceed with installing our BitBagShopwarePocztaPolskaApp application.


## Installing BitBagShopwarePocztaPolskaApp
1. First, we need to clone the repository to the ```./custom/app``` file in our project using the command:
```bash
$ git clone https://github.com/BitBagCommerce/ShopwarePocztaPolskaApp.git
```

2. The next important step is to copy the contents of the ```custom/apps/BitBagShopwarePocztaPolskaApp/app``` 
folder to the path ```custom/apps/BitBagShopwarePocztaPolskaApp```.


3. Now, let's focus on the ```manifest.xml``` file located in the folder of our plugin, ```./BitBagShopwarePocztaPolskaApp```.
Inside the manifest file, we have sections such as ```setup``` where we find the ```registrationUrl```, ```admin``` section which contains ```action-button``` and ```module``` subsections, and the ```webhooks``` section where we define individual webhooks.
It is important to note that applications in Shopware are separate entities and are launched separately.
The links we see in the manifest.xml reflect the links that our Shopware will communicate with.


* This means that if we are using Symfony server and have launched the shopware application on ```localhost:8000``` or ```127.0.0.1:8000```,
our BitBagShopwarePocztaPolskaApp application after launch will have ```localhost:8001``` or ```127.0.0.1:8001```.


* Therefore, in the manifest.xml file, all the links contained within should be modified as follows: ```<registrationUrl>http://example-app2/app/registration</registrationUrl>``` becomes ```<registrationUrl>http://127.0.0.1:8001/app/registration</registrationUrl>```,
and ```http://localhost:7760/app/module/configuration``` becomes ```http://127.0.0.1:8001/app/module/configuration``` or ```http://localhost:8001/app/module/configuration```


* Whether it will be ```localhost:8001``` or ```127.0.0.1:8001``` or something else depends on how you start the server. It is important to have the correct domain configured in the application to enable communication between the two.

4. Next, we can navigate to the application path in the terminal using the command: ```cd custom/apps/BitBagShopwarePocztaPolskaApp```.


5. Install the application as if it were a separate Symfony application using the command:
```bash
$ composer install
```


6. Finally, we can start the application on the Symfony server using the command ```symfony server:start``` and verify if the page has loaded, showing the basic Symfony page.


## Installing the Application in Shopware
After completing the previous steps, we should be able to install our application using the following commands:

1. Install the app.
   ```bash
   $ bin/console app:install BitBagShopwarePocztaPolskaApp
   ```
2.  Activate the app.
   ```bash
     $ bin/console app:activate BitBagShopwarePocztaPolskaApp
   ```
Now, both applications should communicate properly. It is important to remember that both Shopware and the BitBagShopwarePocztaPolskaApp application need to be running on the server for communication between Shopware and the application to occur.
