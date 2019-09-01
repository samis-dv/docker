@ECHO OFF

IF NOT EXIST ".env" (
  ECHO Please fill your docker credentials on .env file
  ECHO DOCKER_USERNAME= >> .env
  ECHO DOCKER_PASSWORD= >> .env
  GOTO END
)

docker-compose -f ./sandbox.yml pull
docker-compose -f ./sandbox.yml run --rm sandbox

:END
