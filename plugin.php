<?php
/*
Plugin Name: YOURLS UI Kit Template
Plugin URI: https://github.com/uglyeoin/yourls-ui-kit-template
Description: A modern admin skin for YOURLS built on UIkit 3. Drop-in replacement for the dated default look. Re-skins the existing admin pages, adds a fresh dashboard, and tidies up forms, tables and error screens — all via hooks, no core files touched.
Version: 1.0.0
Author: Square Balloon Ltd
Author URI: https://squareballoon.co.uk
*/

// No direct access
if ( !defined( 'YOURLS_ABSPATH' ) ) die();

/* ------------------------------------------------------------------
 *  1.  Inject UIkit CSS + JS + our skin into every admin page <head>
 * ------------------------------------------------------------------ */

yourls_add_action( 'html_head', 'yourls_ui_kit_template_head', 5 );

function yourls_ui_kit_template_head() {
    // UIkit version pinned. Update here when you want to bump it.
    $uikit_ver = '3.25.16';
    $plugin_url = YOURLS_SITE . '/user/plugins/yourls-ui-kit-template';
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@<?php echo $uikit_ver; ?>/dist/css/uikit.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/uikit@<?php echo $uikit_ver; ?>/dist/js/uikit.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@<?php echo $uikit_ver; ?>/dist/js/uikit-icons.min.js" defer></script>
    <link rel="stylesheet" href="<?php echo $plugin_url; ?>/assets/skin.css?v=<?php echo yourls_ui_kit_template_version(); ?>" />
    <script src="<?php echo $plugin_url; ?>/assets/skin.js?v=<?php echo yourls_ui_kit_template_version(); ?>" defer></script>
    <?php
}

function yourls_ui_kit_template_version() {
    return '1.0.0';
}

/* ------------------------------------------------------------------
 *  2.  Replace the logo block with a UIkit navbar
 *      The 'html_logo' action fires right after <body>, before the
 *      page <main> content. We swap the default H1+P logo for a
 *      proper top-bar with the site name + nav links.
 * ------------------------------------------------------------------ */

// Kill the default logo output. We do it by hooking BEFORE yourls_html_logo()
// fires its action. Since the default function just echoes a div, we can't
// truly "remove" it — but we CAN inject our navbar AFTER it and hide the
// original via CSS. That's what skin.css does (#wrap > h1 { display:none }).

yourls_add_action( 'html_logo', 'yourls_ui_kit_template_navbar' );

function yourls_ui_kit_template_navbar() {
    $site = YOURLS_SITE;
    $admin = yourls_admin_url();
    $current = isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : '';

    // Plugin pages registered via yourls_register_plugin_page() show up
    // at plugins.php?page=xxx — add our dashboard there as a top-level item.
    $dashboard_url = yourls_admin_url( 'plugins.php?page=uikit_skin_dashboard' );
    ?>
    <nav class="sb-navbar">
      <div class="uk-navbar-container sb-navbar-inner" uk-navbar="mode: click">
      <div class="uk-navbar-left">
        <a class="uk-navbar-item uk-logo" href="<?php echo $admin; ?>">
          <span uk-icon="icon: bolt; ratio: 1.4" class="sb-logo-icon"></span>
          <span class="sb-logo-text"><?php echo htmlspecialchars( parse_url( $site, PHP_URL_HOST ) ); ?></span>
        </a>
        <ul class="uk-navbar-nav uk-visible@s">
          <li><a href="<?php echo $dashboard_url; ?>"><span uk-icon="icon: home"></span> Dashboard</a></li>
          <li class="<?php echo ($current === 'index.php' ? 'uk-active' : ''); ?>">
            <a href="<?php echo yourls_admin_url('index.php'); ?>"><span uk-icon="icon: link"></span> Links</a>
          </li>
          <li class="<?php echo ($current === 'tools.php' ? 'uk-active' : ''); ?>">
            <a href="<?php echo yourls_admin_url('tools.php'); ?>"><span uk-icon="icon: cog"></span> Tools</a>
          </li>
          <li class="<?php echo ($current === 'plugins.php' ? 'uk-active' : ''); ?>">
            <a href="<?php echo yourls_admin_url('plugins.php'); ?>"><span uk-icon="icon: database"></span> Plugins</a>
          </li>
        </ul>
      </div>
      <div class="uk-navbar-right">
        <ul class="uk-navbar-nav">
          <li>
            <a href="<?php echo $site; ?>" target="_blank" rel="noopener" uk-tooltip="View public site">
              <span uk-icon="icon: world"></span>
            </a>
          </li>
          <li>
            <a href="#" uk-tooltip="Account">
              <span uk-icon="icon: user"></span>
              <span class="uk-visible@m sb-username"><?php echo defined('YOURLS_USER') ? htmlspecialchars( YOURLS_USER ) : ''; ?></span>
            </a>
            <div class="uk-navbar-dropdown" uk-dropdown="mode: click; pos: bottom-right">
              <ul class="uk-nav uk-navbar-dropdown-nav">
                <li class="uk-nav-header">Signed in as<br><strong><?php echo defined('YOURLS_USER') ? htmlspecialchars( YOURLS_USER ) : 'guest'; ?></strong></li>
                <li class="uk-nav-divider"></li>
                <li><a href="<?php echo yourls_admin_url('?action=logout'); ?>"><span uk-icon="icon: sign-out"></span> Logout</a></li>
              </ul>
            </div>
          </li>
          <li class="uk-hidden@s">
            <a href="#" uk-toggle="target: #sb-mobile-menu"><span uk-icon="icon: menu"></span></a>
          </li>
        </ul>
      </div>
      </div>
    </nav>

    <!-- Mobile offcanvas menu -->
    <div id="sb-mobile-menu" uk-offcanvas="overlay: true">
      <div class="uk-offcanvas-bar">
        <button class="uk-offcanvas-close" type="button" uk-close></button>
        <h3>Menu</h3>
        <ul class="uk-nav uk-nav-default">
          <li><a href="<?php echo $dashboard_url; ?>"><span uk-icon="icon: home"></span> Dashboard</a></li>
          <li><a href="<?php echo yourls_admin_url('index.php'); ?>"><span uk-icon="icon: link"></span> Links</a></li>
          <li><a href="<?php echo yourls_admin_url('tools.php'); ?>"><span uk-icon="icon: cog"></span> Tools</a></li>
          <li><a href="<?php echo yourls_admin_url('plugins.php'); ?>"><span uk-icon="icon: database"></span> Plugins</a></li>
          <li class="uk-nav-divider"></li>
          <li><a href="<?php echo yourls_admin_url('?action=logout'); ?>"><span uk-icon="icon: sign-out"></span> Logout</a></li>
        </ul>
      </div>
    </div>
    <?php
}

/* ------------------------------------------------------------------
 *  4.  Beautify the "main table" wrapper.
 *      Many YOURLS admin pages call yourls_table_head() and
 *      yourls_table_tfoot() — we hook around those to wrap the
 *      table inside a UIkit card.
 * ------------------------------------------------------------------ */

yourls_add_action( 'admin_page_before_table', 'yourls_ui_kit_template_table_open' );
yourls_add_action( 'admin_page_after_table', 'yourls_ui_kit_template_table_close' );

function yourls_ui_kit_template_table_open() {
    echo '<div class="uk-card uk-card-default uk-card-body sb-table-card uk-margin-top">';
    echo '<h3 class="uk-card-title"><span uk-icon="icon: link"></span> Your short links</h3>';
}

function yourls_ui_kit_template_table_close() {
    echo '</div>';
}

/* ------------------------------------------------------------------
 *  5.  Register a new "Dashboard" plugin page with stat tiles
 * ------------------------------------------------------------------ */

yourls_add_action( 'plugins_loaded', 'yourls_ui_kit_template_register_pages' );

function yourls_ui_kit_template_register_pages() {
    yourls_register_plugin_page( 'uikit_skin_dashboard', 'Dashboard', 'yourls_ui_kit_template_dashboard_page' );
    yourls_register_plugin_page( 'uikit_skin_settings',  'UIkit Skin Settings', 'yourls_ui_kit_template_settings_page' );
}

function yourls_ui_kit_template_dashboard_page() {
    global $ydb;

    // Fetch basic stats
    $table = YOURLS_DB_TABLE_URL;
    $total_links = (int) yourls_get_db()->fetchValue( "SELECT COUNT(keyword) FROM `$table`" );
    $total_clicks = (int) yourls_get_db()->fetchValue( "SELECT SUM(clicks) FROM `$table`" );

    // Last 7 days links
    $week_links = (int) yourls_get_db()->fetchValue(
        "SELECT COUNT(keyword) FROM `$table` WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
    );

    // Top 5 by clicks
    $top = yourls_get_db()->fetchObjects(
        "SELECT keyword, url, title, clicks FROM `$table` ORDER BY clicks DESC LIMIT 5"
    );

    // Most recent 5
    $recent = yourls_get_db()->fetchObjects(
        "SELECT keyword, url, title, clicks, timestamp FROM `$table` ORDER BY timestamp DESC LIMIT 5"
    );

    $avg = $total_links > 0 ? round( $total_clicks / $total_links, 1 ) : 0;
    ?>
    <h2 class="sb-page-title">Dashboard</h2>
    <p class="uk-text-muted">A quick overview of your shortener.</p>

    <!-- Stat tiles -->
    <div class="uk-grid-small uk-child-width-1-2 uk-child-width-1-4@m sb-stats" uk-grid>
      <div>
        <div class="uk-card uk-card-default uk-card-body sb-stat-card sb-stat-blue">
          <div class="sb-stat-icon"><span uk-icon="icon: link; ratio: 1.6"></span></div>
          <div class="sb-stat-value"><?php echo number_format( $total_links ); ?></div>
          <div class="sb-stat-label">Total links</div>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-default uk-card-body sb-stat-card sb-stat-green">
          <div class="sb-stat-icon"><span uk-icon="icon: bolt; ratio: 1.6"></span></div>
          <div class="sb-stat-value"><?php echo number_format( $total_clicks ); ?></div>
          <div class="sb-stat-label">Total clicks</div>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-default uk-card-body sb-stat-card sb-stat-amber">
          <div class="sb-stat-icon"><span uk-icon="icon: calendar; ratio: 1.6"></span></div>
          <div class="sb-stat-value"><?php echo number_format( $week_links ); ?></div>
          <div class="sb-stat-label">Links this week</div>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-default uk-card-body sb-stat-card sb-stat-pink">
          <div class="sb-stat-icon"><span uk-icon="icon: bar-chart; ratio: 1.6"></span></div>
          <div class="sb-stat-value"><?php echo $avg; ?></div>
          <div class="sb-stat-label">Avg. clicks / link</div>
        </div>
      </div>
    </div>

    <div class="uk-grid-medium uk-child-width-1-1 uk-child-width-1-2@m uk-margin-top" uk-grid>

      <!-- Top performers -->
      <div>
        <div class="uk-card uk-card-default uk-card-body sb-list-card">
          <h3 class="uk-card-title"><span uk-icon="icon: star"></span> Top performers</h3>
          <?php if ( !$top ) : ?>
            <p class="uk-text-muted">No links yet.</p>
          <?php else: ?>
          <table class="uk-table uk-table-divider uk-table-small uk-table-middle">
            <thead><tr><th>Short URL</th><th>Destination</th><th class="uk-text-right">Clicks</th></tr></thead>
            <tbody>
            <?php foreach ( $top as $row ) : ?>
              <tr>
                <td><a href="<?php echo yourls_link( $row->keyword ); ?>" target="_blank"><?php echo htmlspecialchars( $row->keyword ); ?></a></td>
                <td class="sb-truncate">
                  <?php if ( !empty($row->title) ) : ?>
                    <strong class="sb-list-title"><?php echo htmlspecialchars( $row->title ); ?></strong><br>
                  <?php endif; ?>
                  <small class="uk-text-muted"><?php echo htmlspecialchars( $row->url ); ?></small>
                </td>
                <td class="uk-text-right"><span class="uk-label sb-clicks-badge"><?php echo number_format( $row->clicks ); ?></span></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>

      <!-- Recent links -->
      <div>
        <div class="uk-card uk-card-default uk-card-body sb-list-card">
          <h3 class="uk-card-title"><span uk-icon="icon: clock"></span> Recently created</h3>
          <?php if ( !$recent ) : ?>
            <p class="uk-text-muted">No links yet.</p>
          <?php else: ?>
          <table class="uk-table uk-table-divider uk-table-small uk-table-middle">
            <thead><tr><th>Short URL</th><th>Destination</th><th class="uk-text-right">When</th></tr></thead>
            <tbody>
            <?php foreach ( $recent as $row ) : ?>
              <tr>
                <td><a href="<?php echo yourls_link( $row->keyword ); ?>" target="_blank"><?php echo htmlspecialchars( $row->keyword ); ?></a></td>
                <td class="sb-truncate">
                  <?php if ( !empty($row->title) ) : ?>
                    <strong class="sb-list-title"><?php echo htmlspecialchars( $row->title ); ?></strong><br>
                  <?php endif; ?>
                  <small class="uk-text-muted"><?php echo htmlspecialchars( $row->url ); ?></small>
                </td>
                <td class="uk-text-right"><small class="uk-text-muted"><?php echo yourls_date_i18n( 'M j, H:i', strtotime( $row->timestamp ) ); ?></small></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="uk-margin-top uk-text-center">
      <a class="uk-button uk-button-primary sb-cta" href="<?php echo yourls_admin_url('index.php'); ?>">
        <span uk-icon="icon: plus"></span> Manage all links
      </a>
    </div>
    <?php
}

function yourls_ui_kit_template_settings_page() {
    // Save settings
    if ( isset($_POST['sb_uikit_save']) ) {
        // This dies on failure with an "Unauthorized" message — perfect.
        yourls_verify_nonce( 'yourls-ui-kit-template_settings' );

        $accent = isset($_POST['sb_accent_color']) ? trim($_POST['sb_accent_color']) : '#1e87f0';
        // Sanitise: must be a hex colour
        if ( preg_match('/^#[0-9a-fA-F]{6}$/', $accent) ) {
            yourls_update_option( 'sb_uikit_accent', $accent );
            echo '<div class="uk-alert-success" uk-alert><p>Settings saved.</p></div>';
        } else {
            echo '<div class="uk-alert-danger" uk-alert><p>Invalid colour. Use a 6-digit hex code like #1e87f0.</p></div>';
        }
    }

    $accent = yourls_get_option( 'sb_uikit_accent', '#1e87f0' );
    $nonce  = yourls_create_nonce( 'yourls-ui-kit-template_settings' );
    ?>
    <h2 class="sb-page-title">UIkit Skin Settings</h2>
    <div class="uk-card uk-card-default uk-card-body" style="max-width:520px">
      <form method="post" class="uk-form-stacked">
        <input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
        <div class="uk-margin">
          <label class="uk-form-label" for="sb_accent_color">Accent colour</label>
          <div class="uk-form-controls uk-flex uk-flex-middle">
            <input id="sb_accent_color" name="sb_accent_color" type="color" value="<?php echo htmlspecialchars($accent); ?>" style="width:60px;height:40px;border:none;background:transparent;cursor:pointer" />
            <input type="text" class="uk-input uk-margin-small-left" value="<?php echo htmlspecialchars($accent); ?>" id="sb_accent_hex" style="max-width:140px" />
          </div>
          <p class="uk-text-meta">Used for buttons, navbar accents and stat tiles.</p>
        </div>
        <button type="submit" name="sb_uikit_save" value="1" class="uk-button uk-button-primary">Save changes</button>
      </form>
    </div>
    <script>
      // Sync color picker <-> hex field
      document.addEventListener('DOMContentLoaded', function(){
        var pick = document.getElementById('sb_accent_color');
        var hex  = document.getElementById('sb_accent_hex');
        if (pick && hex) {
          pick.addEventListener('input', function(){ hex.value = pick.value; });
          hex.addEventListener('input', function(){
            if (/^#[0-9a-fA-F]{6}$/.test(hex.value)) pick.value = hex.value;
          });
        }
      });
    </script>
    <?php
}

/* ------------------------------------------------------------------
 *  6.  Inject the accent colour as a CSS variable so admins can theme
 *      the skin without editing CSS.
 * ------------------------------------------------------------------ */

yourls_add_action( 'html_head', 'yourls_ui_kit_template_inline_vars', 6 );

function yourls_ui_kit_template_inline_vars() {
    $accent = yourls_get_option( 'sb_uikit_accent', '#1e87f0' );
    if ( !preg_match('/^#[0-9a-fA-F]{6}$/', $accent) ) $accent = '#1e87f0';
    echo "<style>:root{--sb-accent:" . $accent . ";}</style>\n";
}
