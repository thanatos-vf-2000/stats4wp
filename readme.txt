=== stats4wp ===
Contributors: vanhoucke
Tags: analytics, stats, statistics, visit, rest api
Requires at least: 5.2
Tested up to: 7.1
Requires PHP: 8.3
Stable tag: 1.6.0
License: GPLv2

This plugin gives you the complete information on your website's visitors.

== Description ==
= Statistics for Wordpress: =
With Statistics  For WordPress you can know your website statistics without any need to send your users data anywhere. You can know how many people visit your personal or business website, where they’re coming from, what browsers and search engines they use, and which of your contents and users get more visits.


= ACT BETTER  BY KNOWING WHAT YOUR USERS ARE LOOKING FOR =
* Visitor Data Records including IP, Referring Site, Browser, Search Engine, OS, Country and language
* Stunning Graphs and Visual Statistics
* Visitor’s Country Recognition
* The number of Visitors coming from each Search Engine
* The number of Referrals from each Referring Site
* Top 10 common browsers; Top 10 countries with most visitors; Top most-visited pages
* And much more information represented in graphs & charts along with data filtering
* Maps: Users location, users language, Bots location

### Features

- **Plug and play**: After installing and activating the plugin, stats will automatically be collected.
- **Data ownership**: No external services are used. Data about visits to your website is yours and yours alone.
- **Performance**. Handles sudden bursts of traffic without breaking a sweat.
- **Metrics**: All the essentials: visitors, pageviews and referrers.
- **Cookies**: not use any cookies.
- **Privacy controls**: exclude selected logged-in roles, exclude bots/crawlers, respect the browser "Do Not Track" header, and automatically purge old data after a configurable retention period.
- **REST API**: expose read-only summary, top pages, top countries, top browsers and online-visitors endpoints under `/wp-json/stats4wp/v1/` for your own dashboards or scripts. Disabled by default and protected by a per-site API key.
- **Weekly email report**: get a Monday-morning email summarizing the last 7 days (visitors, visits, top pages, top countries), with a one-click "send a test report now" option.

### Contributing

You can contribute to Stats4wp in many different ways. For example:

- Write about the plugin on your blog or share it on social media.
- [Vote on features in the GitHub issue list](https://github.com/thanatos-vf-2000/stats4wp/issues?q=is%3Aopen+is%3Aissue+label%3A%22feature+suggestion%22).
- [Translate the plugin into your language](https://translate.wordpress.org/projects/wp-plugins/stats4wp/stable/) using your WordPress.org account.


= Please Note =
Adding an additional customization option to help us personalize our sites is a help for everyone. We all seek to hide or personalize options or displays; that's why your feedback is important to me. Thank you for helping me make WordPress the best blogging platform in the world.

= Disclaimer =
This plugin doesn't require technical knowledge or to be a web developer. The activation or the modification of an option is not definitive, the deactivation of the option or the deletion of the plugin allows a return to the standard.

= Active Contributors =
<li>[Franck VANHOUCKE](https://profiles.wordpress.org/vanhoucke/) (Development)</li>


== Screenshots ==

1. Logo,
2. Dashboard,
3. Visitors,
4. Exemple Operating System
5. Options
6. Widget Number of visitors - parameters
7. Widget Number of visitors - display
8. Maps - Users Location
9. Settings - Options tab
10. Settings - API tab
11. Settings - Reports tab


== Frequently Asked Questions ==

= Installation Instructions =
1. Upload `stats4wp` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the stats4wp link from the main menu

The stats4wp requires php 8.0 or higher.

= Is this plugin compatible with WordPress multisite (MU)? =
stats4wp is multisite compatible, in case of problem contact me.

= How do I use the REST API? =
Go to Settings > Manage Settings and check "Enable the REST API", then open the "API & Reports" tab to copy your API key and see ready-to-use example requests. All routes live under `/wp-json/stats4wp/v1/` and are read-only. The API is disabled by default; only enable it if you actually plan to use it.

= Is the REST API secure? =
Yes. It is off by default, every site gets its own randomly generated key, and requests are validated with a constant-time comparison. You can regenerate the key at any time from the "API & Reports" tab, which immediately invalidates the previous one.

= How do I get a weekly email report? =
In Settings > Manage Settings, check "Send a weekly email report" and optionally set a recipient address (it defaults to your site's admin email). The report is sent every Monday for the previous 7 days. You can send yourself a test report immediately from the "API & Reports" tab.

= Can I exclude myself (or my editors) from the stats? =
Yes. Use the "Exclude logged-in roles" setting to list the WordPress role slugs (e.g. `administrator,editor`) that should never be counted.

= Does stats4wp comply with GDPR? =
stats4wp stores all data on your own server and never sends it to a third party. It also includes optional settings to anonymize IP addresses, exclude bots, respect the "Do Not Track" header, and automatically delete data older than a configurable number of days.

== Licenses ==

Composer
License: MIT License 
Source: https://github.com/composer/composer/blob/master/

Which Browser
License: Copyright (c) 2010-2017 Niels Leenheer
Source: https://github.com/WhichBrowser/Parser-PHP/

Maxmind-db
License: licensed under the Apache License, Version 2.0
Source: https://github.com/maxmind/MaxMind-DB-Reader-php

GeoLite2 Country
License: Creative Commons Attribution 4.0 License 
Source: https://www.maxmind.com/en/geolite2/eula

Chart.js
License: MIT license
Source: https://opensource.org/licenses/MIT

jVectorMap
License: The GNU AGPL is an open-source license
Source: https://jvectormap.com/

== Changelog ==

= 1.6.0 =
*Release Date - 13 July 2026*

* Test up Wordpress 7.1-alpha-62633,
* New: read-only REST API under /wp-json/stats4wp/v1/ (summary, top pages, top countries, top browsers, online visitors) to expose your stats to external tools. Disabled by default, protected by a per-site API key,
* New: optional weekly email report (every Monday) summarizing visitors, visits, top pages and top countries for the last 7 days, with a "send a test report now" button,
* New: "API & Reports" tab in Settings to manage/regenerate the API key and configure the weekly report,
* Update GeoIP Database 20260710.


See [changelog.txt](https://plugins.svn.wordpress.org/stats4wp/trunk/changelog.txt) for older changelog


Please contact me by email or through the contact form on the site [ginkgos.net] (https://ginkgos.net/). Please do not post on the forums.