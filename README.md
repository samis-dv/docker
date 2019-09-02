<p align="center">
  <a href="https://directus.io" target="_blank" rel="noopener noreferrer">
    <img src="https://user-images.githubusercontent.com/522079/43096167-3a1b1118-8e86-11e8-9fb2-7b4e3b1368bc.png" width="140" alt="Directus Logo"/>
  </a>
</p>

<p>&nbsp;</p>
<h1 align="center">
  The All-New Directus 7<br>Future-Proof Headless CMS
</h1>

<h3 align="center">
  <a href="https://directus.io">Website</a> •
  <a href="https://docs.directus.io">Docs</a> •
  <a href="https://docs.directus.io/api/reference.html">API Reference</a> •
  <a href="https://docs.directus.io/guides/user-guide.html">User Guide</a> •
  <a href="https://directus.app">Demo</a> •
  <a href="https://docs.directus.io/getting-started/supporting-directus.html">Contribute</a>
</h3>

<p>&nbsp;</p>

> **Warning**: container support is *HIGHLY* experimental and we're still gathering feedback from the community. We can raise issues or ping us in #docker channel on [Slack](https://slack.directus.io).

# Overview

Directus provides several container images that will help we get started. Even though we maintain extra `kinds`, our officially supported image is based on `php:apache`. All our container images can be found in [docker hub](https://hub.docker.com/r/directus/).

# Concepts

This repository has several images in it that follows some organization concepts.

We've organized our docker images in a way that:

- We do a better use of layer caching
- We avoid as much code duplication on dockerfiles as possible
- We can make security updates (os/webserver) without modifying application images code
- We provide a easy way for the end user to extend images

## Image kinds

We don't want to force anyone to use only `apache` and even though this is the one directus team officially supports, we know there are many webservers out there and we should be free to use the ones we like. We can also provide more slim versions of images by switching between OS distributions. For example: apache, nginx, caddy, alpine...

Our images are split into 3 image types:

1. [Dist images](#dist-images)
2. [Base images](#base-images)
3. [Core images](#core-images)

In most cases, **dist images** are the ones you'll want to use to deploy your directus instances. They are distributed by directus team through [docker hub](https://hub.docker.com/u/directus) under their respective repositories, so **always start from dist images until you find out that you need further customization**.

### Image dependencies

To make final image builds faster, we share common steps through base and core images. Here's how we do that.

<img src=".github\images\inheritance.svg" width="200" />

### Example

#### Core

> directus/core:1.2.3-apache

```dockerfile
FROM php:7.3-apache
RUN apt-get update && apt-get install dependency
...
```

#### Base (api)

> directus/base:1.2.3-api-apache

```dockerfile
FROM directus/core:1.2.3-apache
RUN docker-php-ext-install extension
...
```

#### Dist (api)

> directus/api:3.2.1-apache

```dockerfile
FROM directus/base:1.2.3-api-apache
ONBUILD COPY . .
...
```

#### Dependency diagram

<img src=".github\images\images.svg" width="300" />

We'll cover below what each image does and when to  use them.

## Dist images

> These are the images you'll likely want to use to deploy your instances.

Dist images are the images the directus team will build, support and distribute themselves and only contains the default configuration setup. Users will likely use these for their simple setups.

**WARNING: These images ARE NOT built on this repository. They live inside their own repositories with their own release cycles.**

### FQIN

```
directus/${project}:${version}-${kind}
```

#### Example

| Variable | Value |
|--|--|
| **project** | api |
| **kind** | apache |
| **version** | 3.2.1 |

```
directus/api:3.2.1-apache
```

## Base images

> These images are the base images used in project (`FROM` statements), they include everything a project need to run.

Every project has its own base images that inherits the core ones (allowing us to further customize the core with project-specific requirements).

For example if we're building an `api` image using `apache`, we will inherit the core image using `FROM directus/base:VERSION-apache` on the first line of its Dockerfile.

These base images are mostly used to simplify the project implementation by adding some `ONBUILD` steps and are meant for more advanced users.

We'll want to use them if we are building our own custom project images as they are ready to accept code from their `ONBUILD` stages.

Dockerfiles inheriting from this base images allows us to add our own extension/hooks and/or install more extensions to PHP.

### FQIN

```
directus/base:${version}-${project}-${kind}
```

#### Example

| Variable | Value |
|--|--|
| **project** | app |
| **kind** | node |
| **version** | 1.2.3 |

```
directus/base:1.2.3-app-node
```

## Core images

> These are the clean images, it doesn't include anything related to any directus project.

Core images are the images that contains only setup scripts and server related stuff, this allows us to have consistency over all projects whenever we make fixes and/or security updates are applied to webservers/OS.

These images **DOES NOT** contain any project-specific files besides webserver entrypoints and helper scripts related to the webserver itself, such as install and helper scripts.

The core images exists to be extended by base images (api, app, ...).

### FQIN

```
directus/core:${version}-${kind}
```

#### Example

| Variable | Value |
|--|--|
| **version** | 1.2.3 |
| **kind** | apache |

```
directus/core:1.2.3-apache
```

# Building

In most cases you'll not need to build anything in this repository because we already distribute built images through docker hub. But if you want to, you'll be able to easily build them with our build script.

## Requirements

- [Docker](https://docs.docker.com/install/)
- bash

----------

## Executing the build script

We can build [core images](#core-images) using the command `build --type core`.

```
# Clone the repository
git clone https://github.com/directus/docker.git

# Open the repository directory
cd docker

# Invoke build script
./bin/build --help
```

----------

# Sandbox

TODO: write about sandbox

----------

<p align="center">
  Directus is released under the <a href="http://www.gnu.org/copyleft/gpl.html">GPLv3</a> license. <a href="http://rangerstudio.com">RANGER Studio LLC</a> owns all Directus trademarks and logos on behalf of our project's community. Copyright © 2006-2019, <a href="http://rangerstudio.com">RANGER Studio LLC</a>.
</p>
