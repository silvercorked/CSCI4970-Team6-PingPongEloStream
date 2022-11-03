# ATTIC Ping Pong Website
This website is in development and aims to provide an interface for the UNO CMIT ATTIC to log, track, and view ping pong games that happen using it's ping pong table.

## Release Notes
- Milestone 1, 10/6/2022:
    - This release contains the routes, database migrations, database seeders, and partially completed controllers which will support many future features. This release also resolves is inline with the Project Plan thus far.
- Milestone 2, 10/20/2022:
    - This release contains a redesigned login and register page,
    a home page, as well as the basics for authentication on the,
    frontend.
- Milestone 3, 11/3/2022:
    - This release contains a completed profile page, updated
    submission routes, and more ways to gather statistics to
    support pages. It also contains the partially complete
    implementation of the core page, the game-play page, where
    users will interact with our system to play a game a ping pong.

## To run locally (Windows)
### Requirements
- an ssh key
- composer 2.x
- php 8.1
- node/npm
- virtualbox 6.1.x
- vagrant

### Steps
- pull down repo
- `cp .env.example .env` (copies the example .env file into one the project will utilize)
- `composer install`
- `vendor\\bin\\homestead make` (if using git bash: `php vendor/bin/homestead make`) (this will generate the Homestead.yaml file)
- `php artisan key:generate` (sets the APP_KEY property of the .env file)
- Edit Homestead.yaml file to point to project directory
- Edit Windows/System32/drivers/etc/hosts to allow connecting to vagrant box locally
- `vagrant up` (this will boot up the virtual machine. This can take several minutes. `vagrant halt` stops the virtual machine. `vagrant destroy --force` deletes the virtual machine. `vagrant reload --provision` will re-analyze the homestead.yaml file for changes and reload the box) (pay attention to the port forwarding section, you should see 80->8000, 443->44300, 3306->33060, and 22->2222. If you do not see those or one of the numbers is different, the application will likely run into issues. Very commonly, another process is using port 3306, causing this forwarding to use a different number, which will not work. `vagrant port` will show the ports being forwarded)
- attempt to connect to the local site using the url in the Homestead.yaml file (if this fails, configuration is incorrect) (if you see any indication of your application, even an error page sent by the site, this step was successful)
- If you get the error `SQLSTATE[HY000] [2002] (trying to connect via (null))`, in `.env` set `DB_HOST` to `localhost` instead of `127.0.0.1`
- `php artisan migrate` (if this failes, the above 2 steps have likely been done in error) (if this succeedes, then we're almost ready)
- If you run into errors when running the above migrate command, check your ports with `vagrant port`. You might be missing 3306, which can be fixed by adding the following to Homestead.yaml:
```
ports:
    - send: 33060
      to: 3306
```

- `npm install` (this is also a vue project, so need these dependencies)
- `npm run build` (builds the vue files. Can use `npm run watch` and `npm run dev` to have this run when files are saved)
- `php artisan storage:link` This will create a symbolic link between the /public/storage location and /storage/app location. This allows us to save files (profile pictures) to /storage/app and access them in the app by linking to http://{our-website=name}/storage/{image-url}
- (optional) `php artisan db:seed` (seeds the database according to the seeders)

## Workflow for local development
- automatic page change watching
    - `vagrant up`
    - `npm run watch`
    - while making changes
        - make change
        - if change affects migrations/seeders
            - `php artisan migrate:fresh --seed` (deletes current db, migrates db, seeds db)
        - view on site
- manual page change
    - `vagrant up`
    - while making changes
        - make change
        - `npm run build`
        - if change affects migrations/seeders
            - `php artisan migrate:fresh --seed` (deletes current db, migrates db, seeds db)
        - view on site

## Frameworks
- Vue 3
- Boostrap CSS 5
- Laravel 9
    - Homestead https://laravel.com/docs/9.x/homestead
    - Jetstream https://jetstream.laravel.com/2.x/introduction.html

