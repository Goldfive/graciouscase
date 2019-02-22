# graciouscase

Summary

Unfortunately I couldn't get the docker image working. I was able to create the image, but there were still missing PHP dependencies 
which meant that composer wouldn't actually install Symfony. Not enough time to left to really make it work, so I hope this is at least a good try :)
docker-compose build should therefore work. docker-compose up -d works too. But by executing this line:
docker-compose exec php composer install 

I get: Problem 1
    - facebook/webdriver 1.6.0 requires ext-zip * -> the requested PHP extension zip is missing from your system.
    - facebook/webdriver 1.6.0 requires ext-zip * -> the requested PHP extension zip is missing from your system.
    - Installation request for facebook/webdriver 1.6.0 -> satisfiable by facebook/webdriver[1.6.0].

Couldn't find this dependency anywhere yet :( Would love to have some feedback about this :)

Instalation instructions

To see the app in action, you have to:

- composer update
- Create the database with bin/console doctrine:database:create
- Start the local webserver with bin/console server:start

You can switch from database (to show off a bit of Doctrine) or to load everything in from the API. UI is nothing fancy.
I'm proud of the backend code though. I see I've improved a lot over the course of the last months. There's still so much to learn, and
in this case I only used what I'm actually really comfortable with (I would've liked to do more testing, but now focussed mainly on the functionality).

Thanks for this amazing opportunity and I'd love to hear back from you!

