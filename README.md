Themosis framework (digitalmeat fork)
======================================

[![Tests](https://github.com/digitalmeat/themosis-framework/actions/workflows/tests.yml/badge.svg)](https://github.com/digitalmeat/themosis-framework/actions/workflows/tests.yml)

This is a maintained fork of [themosis/framework](https://github.com/themosis/framework), which has been stuck at `3.1.0` (Illuminate `^8.83`, Laravel 8, EOL since 2023) since its last commit in February 2023. This fork picks up where upstream stopped:

| Version | Illuminate | Notes |
|---|---|---|
| `3.1.0` | `^8.83` | Same as upstream's last release. |
| `3.2.0` | `^11.0` | Migrated through 9 -> 10 -> 11. See branches `illuminate-9`, `illuminate-10`, `illuminate-11` for the intermediate states, and each's commit history for what changed at every step. |

`3.2.0` is the current recommended version. `illuminate-11` is kept as an active branch for ongoing work past `3.2.0`; tag new releases from it as work lands.

The Themosis framework is a WordPress focused framework. It helps you develop enterprise level WordPress application and website using an elegant and simple code syntax.

Getting started
---------------

### Prerequisites

In order to start a new installation of the Themosis framework, you need to have all [WordPress requirements](https://wordpress.org/about/requirements/) and [Composer](https://getcomposer.org/) installed on your computer.

> We recommend to install Composer globally on your computer.

### Installing

Once you have Composer setup, run the following command in your Terminal or Console in order to install a fresh copy of the Themosis framework:

```bash
composer create-project themosis/themosis myproject
```

The above command will install a new instance of the Themosis framework under the `myproject` directory.

### Learn more

In order to learn more about how to use the framework and its features, please follow the official documentation and read the "Getting started" guide:

- [Official documentation](https://framework.themosis.com)

Contributing
------------
Any help is appreciated. The project is open-source and we encourage you to participate. You can contribute to the project in multiple ways by:

- Reporting a code issue
- Suggesting features
- Sending a pull request with code fix or feature
- Following the project on [GitHub](https://github.com/themosis/framework)
- Following us on [Twitter](https://twitter.com/Themosis)
- Sharing the project around your community

For details about contributing to the framework, please check the [contribution guide](https://github.com/themosis/framework/.github/CONTRIBUTING.md).

Versioning
----------

We use SemVer for versioning. For available versions, please check this repository tags if you need a specific version for your project.

When starting from scratch, Composer will always install the most recent stable version.

Authors
-------

- **[Julien Lambé](https://github.com/jlambe)** - *Project Lead*

See also the list of [contributors](https://github.com/themosis/framework/graphs/contributors) who participated in this project.

License
-------

This project is licensed under the GPL-2.0+ License - see the [LICENSE.md](https://github.com/themosis/framework/LICENSE.md) file for details.
