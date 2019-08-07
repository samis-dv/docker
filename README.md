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

Directus provides several container images that will help we get started. Even though we maintain extra `variants`, our officially supported image is based on `php-apache`. All our container images can be found in [docker hub](https://hub.docker.com/r/directus/).

# Concepts

This repository has several images in it that follows some organization concepts.

## Image kind

We don't want to force anyone to use only `apache`, even though this is the one directus team supports, we know there are many webservers out there and users should be free to use theirs. We can also provide more slim versions of images by switching between OS distributions. A list of possible (just as an example, it's not necessarily implemented yet):

- apache
- nginx
- caddy
- alpine-apache
- alpine-nginx
- alpine-caddy

## Core images

Core images are base images that contains only base scripts and the webserver itself, this allows consistency over all distributed projects whenever we make fixes and and/or security updates are applied to webservers/OS.

These images DOES NOT contain any project-specific files besides webserver entrypoints and helper scripts related to the webserver itself.

The core images exists to be extended by base images (api, app, ...), allowing us to further add requirements that an specific project might need.

> Think about these as the "clean" images.

## Base images

Every project has its own base images that inherits the core ones (allowing us to further customize the core with project-specific requirements).

For example if we're building an `api` image using `apache`, we will inherit the core image using `FROM directus/core:base-apache-VERSION` on the first line of its Dockerfile.

These base images are mostly used to simplify the project implementation by adding some `ONBUILD` steps and are meant for more advanced users.

We'll want to use them if we are building our own custom project images as they are ready to accept code from their `ONBUILD` stages.

Dockerfiles inheriting from this base images allows us to add our own extension/hooks and/or install more extensions to PHP.

> Think about these as the "extendable" images.

## Project images

Project images are the images the core directus team will build, support and publish themselves and only contains the default configuration setup. Users will likely use these for their setups.

Think about these as the "just need to run" images.

# Building requirements

- [Docker](https://docs.docker.com/install/)
- [Tusk](https://github.com/rliebz/tusk)

# Building core images

We can build core images (`directus/core`) using the following command:

## Options

| Option | Required | Default | Description |
|---|---|---|---|
| **--kind** | No | apache | What kind of image to build (apache, nginx, caddy...) |
| **--version** | No | latest | We can set the base image version that goes to the tag. |

## Output

This command builds and tags a base image and sets its tag name with the following format:

```
${namespace}/${prefix}base:${kind}-${version}
```

## Examples

### Build default base image

```
$ tusk base
...
Successfully tagged directus/base:apache-latest
```

### Build nginx base

```
$ tusk base --kind nginx
...
Successfully tagged directus/base:nginx-latest
```

### Build nginx base and put a version in it

```
$ tusk base --kind nginx --version custom
...
Successfully tagged directus/base:nginx-custom
```

------------

# Global options

| Option | Default | Description |
|---|---|---|
| **--prefix** | | The project image prefix `namespace/{prefix}project:tag`|
| **--namespace** | directus | The image namespace `{namespace}/project:tag`) |

## Examples

### Build with namespace

```
$ tusk base --namespace wolfulus
...
Successfully tagged wolfulus/base:apache-latest
```

### Build with prefix

```
$ tusk base --prefix hello-
...
Successfully tagged directus/hello-base:apache-latest
```

### Build with namespace and prefix

```
$ tusk base --namespace wolfulus --prefix directus-
...
Successfully tagged wolfulus/directus-base:apache-latest
```

### Build to another registry

```
$ tusk base --namespace registry.gitlab.com/wolfulus/some_project
...
Successfully tagged registry.gitlab.com/wolfulus/some_project/base:apache-latest
```

------------

<p align="center">
  Directus is released under the <a href="http://www.gnu.org/copyleft/gpl.html">GPLv3</a> license. <a href="http://rangerstudio.com">RANGER Studio LLC</a> owns all Directus trademarks and logos on behalf of our project's community. Copyright © 2006-2018, <a href="http://rangerstudio.com">RANGER Studio LLC</a>.
</p>
