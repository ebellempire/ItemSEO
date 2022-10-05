# Description

An Omeka plugin that adds a new element set for storing SEO information.

# Usage

At the moment, this plugin only adds a single element, "Canonical URL," which is automatically appended to the document `<head>` as `<link rel="canonical" href=" ... "/>` using the `public_head` hook in your active theme. The "Item SEO" tab is only available to Super users.

If your theme doesn't use the `public_head` hook, you can access Item SEO data in the usual way, e.g.:
`<?php echo metadata( $item, array('Item SEO','Canonical URL') );?>`

# Contribute/Fork

Feel free to expand on this via a fork or pull request if you want to add more SEO features or options. For example, it might be cool to add some `meta` tags for social media cards/previews.
