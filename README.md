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

> **Warning**: container support is *HIGHLY* experimental and we're still gathering feedback from the community. You can raise issues or ping us in #docker channel on [Slack](https://slack.directus.io).

# Overview

Directus provides several container images that will help you get started. Even though we maintain extra `variants`, our officially supported image is based on `php-apache`. All our container images can be found in [docker hub](https://hub.docker.com/r/directus/).

# Building requirements

- [Docker](https://docs.docker.com/install/)
- [Tusk](https://github.com/rliebz/tusk)

# Building base images

You can build base root images (`directus/base`) using the following command:

## Options

| Option | Required | Default | Description |
|---|---|---|---|
| **--kind** | No | apache | What kind of image to build (apache, nginx, caddy...) |
| **--version** | No | latest | You can set the base image version that goes to the tag. |

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
