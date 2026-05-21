# Please say thanks!
See the bottom of this document on how to say thanks for free.  Much love.

# YOURLS UIkit Skin

A modern admin re-skin for [YOURLS](https://yourls.org/) built on [UIkit 3](https://getuikit.com/). Drop it into your `user/plugins/` folder, activate it, and the dated default look is replaced with a clean, modern interface — no core files touched.

## What it does

- **Sticky top navbar** with site name, primary nav and user menu (mobile offcanvas included)
- **New "Dashboard" page** with stat tiles (total links, total clicks, this week, avg clicks/link), top performers and recently created lists
- **Re-styled main link table** — card-wrapped, hover states, badge for click counts, copy-to-clipboard button on each short URL
- **Modern form styling** for the "new link" form, search/filter and tools pages
- **Re-styled plugin list, alerts, pagination and footer**
- **Themed login screen** with gradient background and centred card
- **Configurable accent colour** via a settings page

It works by hooking into YOURLS's standard plugin API (`html_head`, `html_logo`, `html_footer`, `admin_page_before_table`, `admin_page_after_table`) — no core files are modified.

## Requirements

- YOURLS 1.9 or later
- PHP 7.4+
- A modern browser (the skin uses UIkit 3, which targets evergreen browsers)
- An internet connection on first load (UIkit is pulled from jsDelivr CDN). If you want it fully self-hosted, see "Offline / self-hosted" below.

## Installation

1. In your YOURLS install, go to `/user/plugins/`.
2. Create a folder named `uikit-skin`.
3. Copy `plugin.php` and the `assets/` folder into it. Final structure:

   ```
   user/plugins/uikit-skin/
   ├── plugin.php
   └── assets/
       ├── skin.css
       └── skin.js
   ```

4. In the YOURLS admin, go to **Manage Plugins** and activate **UIkit Skin**.
5. Reload any admin page — you should now see the new look.

To change the accent colour, go to **Manage Plugins → UIkit Skin Settings**.

## Customising

### Accent colour

Set under **Manage Plugins → UIkit Skin Settings**. The colour is stored in YOURLS's options table as `sb_uikit_accent` and injected as a `--sb-accent` CSS variable.

### Tweaking the styles

All styling lives in `assets/skin.css`. It's plain CSS using custom properties at the top:

```css
:root {
    --sb-accent: #1e87f0;
    --sb-bg: #f6f8fb;
    --sb-card-bg: #ffffff;
    --sb-border: #e5e9f0;
    --sb-radius: 10px;
}
```

Override these to shift the whole look without touching the rest of the file.

### Dashboard

The dashboard lives in the `sb_uikit_skin_dashboard_page()` function in `plugin.php`. To add more stat tiles, add another `<div>` inside the `.sb-stats` grid and query the database the same way the others do.

## Offline / self-hosted UIkit

By default UIkit is loaded from jsDelivr CDN. If you'd rather host it yourself:

1. Download UIkit's `dist/` from <https://getuikit.com/docs/installation>.
2. Drop `uikit.min.css`, `uikit.min.js` and `uikit-icons.min.js` into `assets/uikit/`.
3. In `plugin.php`, find the `sb_uikit_skin_head()` function and replace the three `cdn.jsdelivr.net` URLs with the local paths, e.g.:

   ```php
   <link rel="stylesheet" href="<?php echo $plugin_url; ?>/assets/uikit/uikit.min.css" />
   <script src="<?php echo $plugin_url; ?>/assets/uikit/uikit.min.js" defer></script>
   <script src="<?php echo $plugin_url; ?>/assets/uikit/uikit-icons.min.js" defer></script>
   ```

## Known limitations

- This is a **skin**, not a fork. It re-styles existing markup; it doesn't change YOURLS's underlying page logic. If a third-party plugin outputs raw HTML on an admin page, that page will inherit base styling but may not look as polished as the native pages.
- The original logo block is hidden via CSS rather than removed in PHP — YOURLS core doesn't currently provide a clean way to suppress the default logo output entirely. If you want it gone from the DOM as well, you can call `yourls_remove_filter()` on the default logo function (see the YOURLS hook list).
- The dashboard queries are intentionally lightweight. On installs with millions of links, you may want to cache the stats — wrap each query in `yourls_get_transient()` / `yourls_set_transient()` (or your own caching layer).

## Uninstall

Deactivate the plugin in **Manage Plugins**. To remove the stored accent colour, delete the `sb_uikit_accent` row from the YOURLS options table. To remove the plugin entirely, delete the `user/plugins/uikit-skin/` folder.

## Licence

MIT.

## Say thanks for this free template

Say thanks by following our social medias and shooting us a message.

- [Facebook](https://www.facebook.com/squareballoon.co.uk/)
- [LinkedIn](https://www.linkedin.com/company/2732306/admin/dashboard/)
- [Instagram](https://www.instagram.com/squareballoon)
- [X / Twitter](https://x.com/SquareBalloonUk)
- [YouTube](https://www.youtube.com/c/SquareballoonCoUk)

### Feeling even more grateful?

Say thanks by following my comedy business accounts too:

- [Facebook](https://www.facebook.com/londoncomedycrew/)
- [Instagram](https://www.instagram.com/londoncomedycrew)
- [TikTok](https://www.tiktok.com/@londoncomedycrew)
- [Bluesky](https://bsky.app/profile/londoncomedy.bsky.social)
- [YouTube](https://www.youtube.com/@LondonComedyUk)

#### And my personal ones:

- [Instagram](https://www.instagram.com/eoinohcomedy/)
- [Facebook](https://www.facebook.com/profile.php?id=61579112321959)

**Thanks! Love you guys.** ❤️