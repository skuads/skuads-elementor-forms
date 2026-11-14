# Skuads Elementor Forms

Official WordPress plugin that connects Elementor Pro forms to the Skuads application.

The plugin discovers Elementor forms across WordPress pages, posts, custom post
types, and Elementor templates. It sends new submissions to the Skuads
application automatically and can manually synchronize the 30 most recent
Elementor submissions.

## Features

- Connects a WordPress website to the Skuads application using a Site UUID and
  API key.
- Discovers forms in Elementor pages and templates, including forms that have no
  previous submissions.
- Lets administrators select which Elementor forms should be connected.
- Sends new form submissions to the Skuads application automatically.
- Manually synchronizes the 30 most recent submissions.
- Includes stable submission identifiers and normalized UTC timestamps to prevent
  duplicate leads during repeated manual synchronizations.
- Provides separate development, staging, and production API configurations.

## Requirements

### Plugin usage

- WordPress
- Elementor Pro with Forms and Submissions enabled
- A Skuads account, Site UUID, and API key

The Site UUID and API key are available from the
[Skuads API settings](https://app.skuads.com/api-settings).

### Development and packaging

- Bash
- PHP CLI
- `zip`
- `svn` and `rsync` only when maintaining the WordPress.org distribution

## Repository structure

```text
.
├── bin/                       Build and distribution scripts
├── settings/                  Environment-specific Skuads API settings
├── skuads-elementor-forms/    WordPress plugin source
└── tests/                     Standalone PHP tests
```

The generated `includes/settings.php` file contains the selected environment
configuration. It is intentionally excluded from Git and added during the build.
The root `LICENSE.md` file is also copied into the packaged plugin automatically.

## Install locally

Build the plugin for the required environment from the repository root:

```bash
bin/build dev
```

Valid environments are `dev`, `staging`, and `prod`.

The command creates:

```text
dist/<environment>/skuads-elementor-forms.zip
```

Upload this archive from **WordPress Admin > Plugins > Add New > Upload Plugin**,
then activate **Skuads Elementor Forms**.

In WordPress Admin, open **Skuads Forms**, enter the Site UUID and API key, select
the forms to capture, and save the connection.

## Run the tests

The tests do not require a complete WordPress installation:

```bash
php tests/form-discovery.php
php tests/submission-identity.php
```

Check the syntax of all PHP files with:

```bash
find skuads-elementor-forms tests settings -name '*.php' -print0 \
  | xargs -0 -n1 php -l
```

## Build a release archive

Create a production archive with:

```bash
bin/build prod
```

The release archive is written to:

```text
dist/prod/skuads-elementor-forms.zip
```

Before releasing a new version:

1. Update the version in `skuads-elementor-forms/skuads-elementor-forms.php`.
2. Update `Stable tag` and the changelog in
   `skuads-elementor-forms/readme.txt`.
3. Run the PHP syntax checks and both test scripts.
4. Build and install the production archive on a test WordPress website.
5. Confirm the supported WordPress and Elementor Pro versions before updating
   the compatibility metadata.

### Internal release helper

`bin/release <environment>` builds the selected environment and copies its archive
to the Skuads application download directory expected by the original monorepo
layout. It is not portable to a standalone clone unless that directory layout is
available.

`bin/download` is a legacy WordPress.org SVN synchronization script. It still
expects the former `assets/` and `trunk/` repository layout and must be updated
before it can be used from this standalone repository.

## About Skuads

[Skuads](https://skuads.com/) is a French digital marketing company that combines
an all-in-one marketing application with expert support. Its services include
website creation and management, SEO, online advertising, social media, content
creation, CRM, lead management, and marketing automation.

This plugin connects Elementor Pro form submissions to the Skuads application
so businesses can centralize and process their leads in the application.

### Company information

- Skuads
- 1480 Avenue d'Arménie, Bât C, Pôle Yvon Morandat
- 13120 Gardanne, France
- <https://skuads.com/>
- <support@skuads.com>

## Support

For help with Skuads or this integration, visit the
[Skuads help center](https://support.skuads.com/) or contact
[support@skuads.com](mailto:support@skuads.com).

## Copyright and license

Copyright (C) 2026 to present Skuads. The Skuads Elementor Forms plugin was created
by Skuads and belongs to Skuads. Skuads is the copyright holder of the plugin
and its original source code.

This project is free software licensed under the GNU General Public License,
version 2 or any later version (`GPL-2.0-or-later`). See [LICENSE.md](LICENSE.md)
for the license terms.

