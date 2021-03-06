API with Cache
========

Requirements
----
* Git
* Docker
* Docker Compose


Run It
---

* Clone this git

```
git clone https://github.com/aosmorac/iconic-int.git
```
* Go into git folder
```
cd iconic-int
```
* Run containers
```
docker-compose up
```
Now is necessary install package with composer
* Open a new terminal without close the other one.
* Go into php container
```
docker exec -i -t symfony-first bash
```
* Go to project folder
```
cd /code/interview
```
* Run conposer install
```
composer install
```
* Just for the test we are going to asign 777 permissions for avoid cache folder problems.
```
chmod 777 -R ./
```
Now you can open http://localhost:8080/customers/ and check the test


Docker Structure
---
This docker compose is compose by nginx, php, mongodb and redis.
* Nginx works as server managing virtualhost
* PHP is where our code is
* MongoDb. There are two instances to manage a replica set and could use change streams. I did not use any change streams funtionalities.
* Redis to manage cache functionalities.

Functionalities
---
Just following the tests you can check the functionalities.
* CustomersControllerFunctionalTest.php
* CollectionTest.php

The main class is: 
* Collection.php

It is necessary do some code refactoring. 

First Step
---
You can load the first data in ```http://localhost:8080/customers/``` using ```post``` and sending a object like ```[{"name":"Jhon", "age":33}, {"name":"Steve", "age":33}]```