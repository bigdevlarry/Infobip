<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>


### How do we build and run it?
The application is an API based laravel application; it integrates Infobip API for communication (sending and verifying phone numbers and sending SMS to verified numbers when invited to a game round). Users on the application register then verify phone numbers, create tournaments, and invite other users to the tournament. To execute this, you need to have the composer installed and then clone this repository. Run composer install to pull the required dependencies. Then, proceed to create an environment variable file from the .env.example where you establish a database connection. Add Infobip integration variables to the .env file (API_KEY=6c****-0f444b16-1c97-4577, URL_BASE_PATH=https://***.api.infobip.com, API_KEY_PREFIX=App, FROM=SeriviceSMS, SIGNUP_PHONE_NUMBER=*****).Migrate all database tables with php artisan migrate command in the terminal. All API endpoints are published <a href="https://documenter.getpostman.com/view/3879258/TzkzqebX#a34d8670-1c62-4a56-94e5-8f416d8d7342">Here</a> and exported to the repository "Infobip.postman_collection.json". A test class is provided to test features like sending verification pins to provided numbers, creating tournaments, and inviting users to the tournament (send SMS and mail notification). Use the php artisan test to run the test. 

### What tools did you use?
What tools did you use?
The application uses 
i.) Laravel 8 ii.) Jwt library for authentication iii) Infobip API for communication
### Why did you use them?
Laravel creates the application scaffold whilst the JSON web token library generates access token and authorization (middleware) purposes. Infobip API allows for phone number verification and sending SMS within the application.


