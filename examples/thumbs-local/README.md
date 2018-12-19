Thumbnails
==========

Locally persist thumbnails.

- One `mysql` instance on port 3306
- One `api` instance on port 7000
- One `app` instance on port 8000

> Make sure there these ports are free, otherwise you might get errors while compose starts the containers.

Running
=======

> `docker-compose up`

Endpoints
=========

- Database `localhost:3306`
- [app @ http://localhost:8000/](http://localhost:8000/)
- [api @ http://localhost:7000/](http://localhost:7000/)

Credentials
===========

### MySQL

- Root password: `rootpassword`
- Username: `someusername`
- Password: `somepassword`

### Directus

- Admin email: `admin@localhost.com`
- Admin password `directusrocks`
