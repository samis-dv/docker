# Simple

## Prerequisites

- Make sure docker and docker-compose is available
- Make sure nothing is running on port 80 and 3306
  - If necessary, adjust the exposed ports on docker-compose.yml file

## Run the stack

```
docker-compose up -d
```

> NOTE: Making requests or trying to authenticate at this point will raise errors, because the database is not installed.

## Install the database and user

```
docker-compose run installer install --email your@email.com --password somepass
```
