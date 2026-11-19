=== Skuads Forms for Elementor ===
Contributors: skuads21
Tags: skuads, elementor, forms, crm, leads
Requires at least: 6.5
Tested up to: 7.1
Stable tag: 0.0.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Connect Elementor Pro forms to the Skuads application and centralize form submissions in the application.

== Description ==

Skuads Forms for Elementor is the official Skuads integration for Elementor Pro Forms. It is developed by Skuads and is not affiliated with or endorsed by Elementor.

The plugin discovers forms in Elementor pages and templates, lets administrators choose which forms to connect, sends new submissions to the Skuads application automatically, and can manually synchronize the 30 most recent submissions.

Repeated manual synchronizations include stable submission identities to prevent duplicate leads.

An active Skuads account, Site UUID, API key, and Elementor Pro are required.

For more information, visit https://skuads.com/ or the Skuads help center at https://support.skuads.com/.

== External services ==

This plugin connects to the Skuads application (https://app.skuads.com/), a service provided by Skuads, the author of this plugin. The service is needed to receive the website's Elementor form submissions as leads in the site owner's Skuads account. Nothing is sent until an administrator enables the plugin and saves a Site UUID and API key.

* When an administrator saves the settings, the plugin sends the Site UUID, the API key (as a Bearer token), and the ID and name of each selected form to https://app.skuads.com/api/connect-leadforms to check the connection.
* Each time a visitor submits an Elementor Pro form, the plugin sends the Site UUID, the form ID and name, the submitted field values (which can include personal data such as name, email address, phone number, or message), and the submission date to https://app.skuads.com/webhook/leadforms.
* When an administrator clicks "Manually sync entries", the plugin sends the same data for the 30 most recent Elementor Pro form submissions to https://app.skuads.com/webhook/leadforms.

Skuads terms of use: https://support.skuads.com/en/legal/terms/
Skuads privacy policy: https://support.skuads.com/en/legal/privacy/

== About Skuads ==

Skuads is a French digital marketing company combining an all-in-one marketing application with expert support. Skuads helps businesses manage websites, SEO, online advertising, social media, CRM, leads, and marketing automation.

Skuads - 1480 Avenue d'Arménie, Bât C, Pôle Yvon Morandat, 13120 Gardanne, France.

Website: https://skuads.com/
Support: https://support.skuads.com/
Email: support@skuads.com
Telephone: +33 4 82 29 60 75

== Copyright and License ==

Copyright (C) 2026 Skuads. The Skuads Forms for Elementor plugin was created by Skuads and belongs to Skuads. Skuads is the copyright holder of the plugin and its original source code.

This plugin is free software licensed under the GNU General Public License, version 2 or any later version (GPL-2.0-or-later). See LICENSE.md in the source repository for details.

== Changelog ==

= 0.0.5 =
* Rename the plugin to Skuads Forms for Elementor.
* Load the settings page script with wp_enqueue_script.
* Document the Skuads service used by the plugin.
* Declare the Elementor dependency.

= 0.0.4 =
* Escape all admin output, add a nonce and capability check to the manual sync, sanitize saved settings.

= 0.0.3 =
* Add stable submission identities so repeated manual syncs do not create duplicate leads.

= 0.0.2 =
* Discover forms directly from Elementor pages and templates, including forms without previous submissions.
