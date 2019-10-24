# Simple

## Prerequisites

- Make sure docker and docker-compose is available
- Make sure nothing is running on port 80 and 3306
  - If necessary, adjust the exposed ports on docker-compose.yml file

## Make sure you have the latest images

```
docker-compose pull
```

## Run the stack

```
docker-compose up -d
```

> NOTE: Making requests or trying to authenticate at this point will raise errors, because the database is not installed.

## Install the database and user

Using a specific email and password

```
docker-compose run api install --email your@email.com --password somepass
```

Using a specific email and a random password

```
docker-compose run api install --email your@email.com
```

Using default email (admin@example.com) and a random password

```
docker-compose run api install
```

## Accessing

- [api at http://api.localtest.me/](http://api.localtest.me/)
- [app at http://app.localtest.me/](http://app.localtest.me/)
