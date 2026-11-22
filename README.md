# Skuads Forms for Elementor

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
- Provides separate API configurations for the `dev` and `main` environments.

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
- `svn` (`brew install subversion`) and `rsync` only when releasing on
  WordPress.org

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

Valid environments are `dev` and `main`, matching the repository branches:
`dev` uses the development endpoints and `main` uses the production Skuads
application (app.skuads.com).

The command creates:

```text
dist/<environment>/skuads-elementor-forms.zip
```

Upload this archive from **WordPress Admin > Plugins > Add New > Upload Plugin**,
then activate **Skuads Forms for Elementor**.

In WordPress Admin, open **Skuads Forms**, enter the Site UUID and API key, select
the forms to capture, and save the connection.

## Run the tests

The tests do not require a complete WordPress installation:

```bash
php tests/form-discovery.php
php tests/submission-identity.php
php tests/sending-rules.php
```

Check the syntax of all PHP files with:

```bash
find skuads-elementor-forms tests settings -name '*.php' -print0 \
  | xargs -0 -n1 php -l
```

## Build a release archive

Create a production archive with:

```bash
bin/build main
```

The release archive is written to:

```text
dist/main/skuads-elementor-forms.zip
```

Before releasing a new version:

1. Update the version in `skuads-elementor-forms/skuads-elementor-forms.php`.
2. Update `Stable tag` and the changelog in
   `skuads-elementor-forms/readme.txt`.
3. Run the PHP syntax checks and both test scripts.
4. Build and install the production archive on a test WordPress website.
5. Confirm the supported WordPress and Elementor Pro versions before updating
   the compatibility metadata.

### GitHub releases

The `release` GitHub Actions workflow builds the plugin on every push to `main`
and attaches the zip to the release `v<Version>`, using the `Version` header of
`skuads-elementor-forms.php` and the matching `readme.txt` changelog entry as
notes. It is marked as the latest release. Pushing `main` again without changing
the version replaces the zip of that release. Pushes to `dev` do not create a
release.

### Internal release helper

`bin/release <environment>` builds the selected environment and copies its archive
to the Skuads application download directory expected by the original monorepo
layout. It is not portable to a standalone clone unless that directory layout is
available.

### WordPress.org release

The plugin is published on WordPress.org as
[skuads-forms-for-elementor](https://wordpress.org/plugins/skuads-forms-for-elementor/).
WordPress.org distributes it from its SVN repository, not from GitHub.

After `main` has the new version, run:

```bash
bin/svn
```

It checks out the SVN repository into `svn/` (ignored by Git), copies the
production plugin into `trunk/` and `tags/<Version>`, and refuses to run if the
version is already released or differs from the readme `Stable tag`. Nothing is
sent: review `svn status`, then commit with the printed command, using the
`skuads21` SVN password set in the WordPress.org profile (Account & Security).

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

Copyright (C) 2026 Skuads. The Skuads Forms for Elementor plugin was created
by Skuads and belongs to Skuads. Skuads is the copyright holder of the plugin
and its original source code.

The copyright year is updated automatically at midnight (Europe/Paris) every
January 1 by the `update-copyright-year` GitHub Actions workflow.

This project is free software licensed under the GNU General Public License,
version 2 or any later version (`GPL-2.0-or-later`). See [LICENSE.md](LICENSE.md)
for the license terms.

