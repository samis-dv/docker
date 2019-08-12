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

## Image kinds

We don't want to force anyone to use only `apache` and even though this is the one directus team officially supports, we know there are many webservers out there and we should be free to use the ones we like. We can also provide more slim versions of images by switching between OS distributions. A list of possible (just as an example, it's not necessarily implemented):

- alpine
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

### FQIN

```
${namespace}/${prefix}core:base-${kind}-${version}
```

#### Example

| Variable | Value |
|--|--|
| **namespace** | registry.gitlab.com/user/project |
| **prefix** | d6s- |
| **kind** | apache |
| **version** | latest |

```
registry.gitlab.com/user/project/d6s-core:base-apache-latest
```

## Base images

Every project has its own base images that inherits the core ones (allowing us to further customize the core with project-specific requirements).

For example if we're building an `api` image using `apache`, we will inherit the core image using `FROM directus/core:base-apache-VERSION` on the first line of its Dockerfile.

These base images are mostly used to simplify the project implementation by adding some `ONBUILD` steps and are meant for more advanced users.

We'll want to use them if we are building our own custom project images as they are ready to accept code from their `ONBUILD` stages.

Dockerfiles inheriting from this base images allows us to add our own extension/hooks and/or install more extensions to PHP.

> Think about these as the "extendable" images.

### FQIN

```
${namespace}/${prefix}${project}:${kind}-${version}
```

#### Example

| Variable | Value |
|--|--|
| **namespace** | gcr.io/my-agency |
| **prefix** | project1- |
| **project** | directus |
| **kind** | apache |
| **version** | latest |

```
gcr.io/my-agency/project1-directus:apache-latest
```

## Official images

Official images are the images the directus team will build, support and distribute themselves and only contains the default configuration setup. Users will likely use these for their simple setups.

Think about these as the "I just want to use it" images.

### FQIN

```
directus/{project}:${kind}-${version}
```

#### Example

| Variable | Value |
|--|--|
| **project** | api |
| **kind** | apache |
| **version** | v2.4.0 |

```
directus/api:apache-v2.4.0
```

# Building

## Requirements

- [Docker](https://docs.docker.com/install/)
- [Tusk](https://github.com/rliebz/tusk)
- Bash

## Sandbox

You can start a development "machine" if you don't have `tusk`, the requirements to run the build scripts (such as bash) and/or is on a Windows machine without WSL. This allows you to invoke and debug `tusk` tasks and bash scripts.

All you need is `docker` and `docker-compose`.

### Starting a sandbox with `tusk`

```
$ tusk dev
...
root@/directus $ _
```

### Starting a sandbox with `docker-compose`

```
$ docker-compose -f ./sandbox.yml run --rm sandbox
...
root@/directus $ _
```

## Global options

These options can be passed to any command below in order to customize the way images are tagged.

| Option | Default | Description |
|--|--|--|
| **--prefix** | | The image prefix |
| **--namespace** | directus | The image namespace (registry/username) |

----------

## Building core images

We can build [core images](#core-images) using the command `tusk core`.

### Options

| Option | Required | Default | Description |
|--|--|--|--|
| **--kind** | Yes | | What kind of image to build (apache, nginx, caddy...) |
| **--version** | Yes | | We can set the base image version that goes to the tag. |

### Examples

#### Build a nginx image

```
$ tusk core --kind nginx --version v1
...
Successfully tagged directus/core:nginx-v1
```

----------

## Building base images

We can build [base images](#base-images) using the command `tusk base`.

### Dependencies

All base images depends on a core image, so we need either build a core image first or have have pull access to an already built one.

> ⚠️ Note that the core image will follow the `namespace` and `prefix` options as well, so changing them means you'll need to also build your own core images.

### Options

| Option | Required | Default | Description |
|--|--|--|--|
| **--kind** | Yes | | What kind of image to build (apache, nginx, caddy...) |
| **--version** | Yes | | Which version of core image to use |
| **--project** | Yes | | What kind of image to build (apache, nginx, caddy...) |

### Examples

#### Build an api image based on apache

```
$ tusk base --project api --kind apache --version 1.0.0
...
Successfully tagged directus/api:base-apache-1.0.0
```

----------

## FAQ

### How do I change my image username?

Use `--namespace` to set the username and/or the registry.

```
$ tusk core --namespace wolfulus \
            --kind apache \
            --version latest
...
Successfully tagged wolfulus/core:apache-latest
```

### How to set another registry?

Use `--namespace` to set the username and/or the registry.

```
$ tusk core --namespace registry.gitlab.com/wolfulus/d6s \
            --kind apache \
            --version latest
...
Successfully tagged registry.gitlab.com/wolfulus/d6s/core:apache-latest
```

### How do I set an image prefix?

Use `--prefix` to set image prefixes.

```
$ tusk core --prefix hello- \
            --kind apache \
            --version latest
...
Successfully tagged directus/hello-core:apache-latest
```

### Can I set both namespace and prefix?

Yes you can.

```
$ tusk core --namespace wolfulus \
            --prefix directus- \
            --kind apache \
            --version latest
...
Successfully tagged wolfulus/directus-base:apache-latest
```

## How do I build a custom base image?

### Select a core image or build your own

```
$ tusk core --namespace wolfulus \
            --prefix my-custom-directus-
            --kind apache \
            --version 0.0.1
...
Successfully tagged wolfulus/my-custom-directus-core:apache-0.0.1
```

### Extend the core image and add your steps

```docker
FROM wolfulus/my-custom-directus-core:apache-0.0.1
# Add custom steps
```

## How do I build a project using my base image?

> ...

## How do I install additional PHP modules?

> ...

## How do I install additional PHP extensions?

> ...

## How do I add my own extensions?

> ...

----------

<p align="center">
  Directus is released under the <a href="http://www.gnu.org/copyleft/gpl.html">GPLv3</a> license. <a href="http://rangerstudio.com">RANGER Studio LLC</a> owns all Directus trademarks and logos on behalf of our project's community. Copyright © 2006-2019, <a href="http://rangerstudio.com">RANGER Studio LLC</a>.
</p>
