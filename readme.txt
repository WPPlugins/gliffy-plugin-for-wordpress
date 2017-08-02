=== Gliffy for WordPress ===
Contributors: gliffy
Tags: gliffy, diagram, drawing, draw, flowchart, uml, post, page, plugin, edit
Requires at least: 2.8
Tested up to: 3.4.2
Version: 0.4
Stable tag: 0.4

The Gliffy plugin allows you to create diagrams and insert them into your posts or pages. Draw all kinds of diagrams: flowcharts, UI mockups, UML, etc 

== Description ==

The [Gliffy](website) plugin for WordPress works with your [Gliffy Online](http://www.gliffy.com/online.shtml) account.
The plugin allows you to create diagrams and insert them into your posts or pages. Create all kinds of diagrams: basic drawings, flowcharts, floorplans, UML, ERD, UI mockups, etc.

Usage:

Look for the "Diagrams" menu when you activate the plugin, and use the Gliffy button on the media bar to insert diagrams.  Once activated, make sure to configure the plugin in the settings menu.

Notes:

The Gliffy plugin for WordPress, the publicly available Gliffy client library for php, and corresponding REST API are currently in BETA. If you experience any problems with the plugin, please log a bug in our [Issue Tracker](http://jira.gliffy.com/browse/INTWP).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `gliffy-plugin-for-wordpress` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enter your API credentials in the 'Settings'...'Gliffy API' menu in WordPress
1. Test your API credentials while in the 'Gliffy API' menu in WordPress

== Frequently Asked Questions ==

= My diagram isn't showing up on the blog! =

Make sure that you've made the diagram public. There are two ways to do this in the Gliffy Editor:

1. Go to the file menu "share" and select "Publish to Internet...", then select the radio button "Make Public", and press "Close".
1. In the upper right corner click the button "blog & share this diagram", then select the radio button "Make Public", and press "Close".

= What diagrams can I create? =

We have several examples on our [website](website). And our users have shared thousands of public diagrams Our users have made 

= How do I request new features for the plugin? =

Search for features in our [Issue Tracker](http://support.gliffy.com/forums).   If you find a feature listed, you can comment or vote on it to give it more visibility. If you don't find what you're looking for, create a new one!

= How can I learn more about Gliffy? =

Visit us at [http://www.gliffy.com](website), for a [FAQ on Gliffy Online](http://gliffy.localhost/faqs.shtml "Frequently asked questions about Gliffy").


== Screenshots ==
                   
1. Make sure to configure your plugin before use.
2. Create a new diagram by going to the object menu and selecting add new.  Provide a name for your diagram.
3. The Gliffy Editor will launch in the window.  Make sure to click on "share" to set the visibility of the document to "make public". When you're done editing it, press the link "Back to WordPress"
4. Add your new diagram into your post by clicking on the media button and then selecting your desired diagram.
5. The post will now contain a "macro" that tells WordPress that this is where your diagram should go.
6. And here is what the diagram looks like in your post.  You can click on it to see a larger version!
7. Diagrams listed in gallery view for editing

== Changelog ==
= 0.4 =
* loading jquery from google, as the wordpress version does not seem to reliably work
* js loaded through enque 
* added Gliffy Username field to hold the email address of the Gliffy account to use
* bugfix for issue regarding sites that have allow_url_fopen set to off

= 0.3.4 =
* Readme.txt changes fix for autoupdates.

= 0.3.3 =
* bug fix for links on resized diagrams in IE

= 0.3.2 =
* fix for problems with links on resized diagrams
* fix media window returning to original size when editor closes

= 0.3.1 =
* bug fixes in inserting diagrams from media pane
* launching editor from media pane
* separating out javascript to wp-script.js

= 0.3 =
* Added gallery view for diagram listing in admin
* added gallery view for diagram post insert
* modified macro to allow for mulitple sizes and iframe inserts

= 0.2 =
* Fixed problem with Gliffy API 

= 0.1.3 =
* Defaulted the Gliffy ROOT to http://www.gliffy.com

= 0.1.2 =
* Changed root filename to match the plugin folder name 'gliffy-plugin-for-wordpress.php'
* Changed the credential path and signup path to point to gliffy.com if no other Gliffy ROOT is specified

= 0.1.1 =
* Updated paths to reflect the svn repository name of 'gliffy-plugin-for-wordpress'

= 0.1 =
* INITIAL RELEASE
* A configuration panel to setup and test API credentials
* An object menu to list/edit existing diagrams and create new diagrams
* A media button to choose a diagram to insert into your post or page

== Upgrade Notice ==
= 0.3.4 =
* Bug fixes - recommended


[website]: http://www.gliffy.com/ "Diagraming Software"
