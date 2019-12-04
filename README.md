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

> **Warning**: container support is _HIGHLY_ experimental and we're still gathering feedback from the community. We can raise issues or ping us in #docker channel on [Slack](https://slack.directus.io).

# Overview

Directus provides several container images that will help we get started. Even though we maintain extra `kinds`, our officially supported image is based on `php:apache`. All our container images can be found in [docker hub](https://hub.docker.com/r/directus/).

# Concepts

This repository has several images in it that follows some organization concepts.

We've organized our docker images in a way that:

- We do a better use of layer caching
- We avoid as much code duplication on dockerfiles as possible
- We can make security updates (os/webserver) without modifying application images code
- We provide a easy way for the end user to extend images

# Building

In most cases you'll not need to build anything in this repository because we already distribute built images through docker hub. But if you want to, you'll be able to easily build them with our build script.

## Requirements

- [Docker](https://docs.docker.com/install/)
- bash

---

## Executing the build script

We can build images using the command `build` located in bin folder.

> Note: If you're getting "-A: invalid option" issues, try updating your bash console. OSX for example ships with older bash versions. These scripts will only work on bash 4 or newer.

```
# Clone the repository
git clone https://github.com/directus/docker.git

# Open the repository directory
cd docker

# Invoke build script
./bin/build --help
```

---

# Sandbox

TODO: write about sandbox

---

<p align="center">
  Directus is released under the <a href="http://www.gnu.org/copyleft/gpl.html">GPLv3</a> license. <a href="http://rangerstudio.com">RANGER Studio LLC</a> owns all Directus trademarks and logos on behalf of our project's community. Copyright © 2006-2019, <a href="http://rangerstudio.com">RANGER Studio LLC</a>.
</p>
