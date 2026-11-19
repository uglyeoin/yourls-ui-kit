<?php
/*
Plugin Name: YOURLS UI Kit Template
Plugin URI: https://github.com/uglyeoin/yourls-ui-kit-template
Description: A modern admin skin for YOURLS built on UIkit 3. Drop-in replacement for the dated default look. Re-skins the existing admin pages, adds a fresh dashboard, and tidies up forms, tables and error screens — all via hooks, no core files touched.
Version: 1.6.6
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
    $plugin_url = yourls_plugin_url(__DIR__);
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@<?php echo $uikit_ver; ?>/dist/css/uikit.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/uikit@<?php echo $uikit_ver; ?>/dist/js/uikit.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@<?php echo $uikit_ver; ?>/dist/js/uikit-icons.min.js" defer></script>
    <link rel="stylesheet" href="<?php echo yourls_esc_attr($plugin_url); ?>/assets/skin.css?v=<?php echo yourls_ui_kit_template_version(); ?>" />
    <script src="<?php echo yourls_esc_attr($plugin_url); ?>/assets/skin.js?v=<?php echo yourls_ui_kit_template_version(); ?>" defer></script>
    <?php
}

function yourls_ui_kit_template_version() {
    return '1.6.6';
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
    // The login uses the same logo hook, but has no admin navigation.
    if ( yourls_get_html_context() === 'login' ) {
        echo '<div class="sb-login-brand">';
        yourls_ui_kit_template_brand();
        echo '</div>';
        return;
    }

    $admin = yourls_admin_url();
    $logout_url = yourls_nonce_url('admin_logout',
        yourls_add_query_arg(['action' => 'logout'], yourls_admin_url('index.php')), 'nonce', 'logout');
    $current = isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : '';
    $current_page = isset($_GET['page']) ? $_GET['page'] : '';

    // Plugin pages registered via yourls_register_plugin_page() show up
    // at plugins.php?page=xxx — add our dashboard there as a top-level item.
    $dashboard_url = yourls_admin_url( 'plugins.php?page=uikit_skin_dashboard' );
    $settings_url = yourls_admin_url( 'plugins.php?page=uikit_skin_settings' );
    $is_settings = ($current === 'plugins.php' && $current_page === 'uikit_skin_settings');

    $is_dashboard = ($current === 'plugins.php' && $current_page === 'uikit_skin_dashboard');
    $is_plugins   = ($current === 'plugins.php' && $current_page === '');
    ?>
    <nav class="sb-navbar">
      <div class="uk-navbar uk-navbar-container sb-navbar-inner" uk-navbar="mode: click">
      <div class="uk-navbar-left">
        <a class="uk-navbar-item uk-logo" href="<?php echo $admin; ?>">
          <?php yourls_ui_kit_template_brand(); ?>
        </a>
        <ul class="uk-navbar-nav uk-visible@s">
          <li class="<?php echo ($is_dashboard ? 'uk-active' : ''); ?>">
            <a href="<?php echo $dashboard_url; ?>"><span uk-icon="icon: home"></span> Dashboard</a>
          </li>
          <li class="<?php echo ($current === 'index.php' ? 'uk-active' : ''); ?>">
            <a href="<?php echo yourls_admin_url('index.php'); ?>"><span uk-icon="icon: link"></span> Links</a>
          </li>
          <li class="<?php echo ($current === 'tools.php' ? 'uk-active' : ''); ?>">
            <a href="<?php echo yourls_admin_url('tools.php'); ?>"><span uk-icon="icon: cog"></span> Tools</a>
          </li>
          <li class="<?php echo ($is_plugins ? 'uk-active' : ''); ?>">
            <a href="<?php echo yourls_admin_url('plugins.php'); ?>"><span uk-icon="icon: database"></span> Plugins</a>
          </li>
          <li class="<?php echo ($is_settings ? 'uk-active' : ''); ?>">
            <a href="<?php echo yourls_esc_attr($settings_url); ?>"><span uk-icon="icon: settings"></span> Config</a>
          </li>
        </ul>
      </div>
      <div class="uk-navbar-right">
        <ul class="uk-navbar-nav">
          <li class="uk-visible@s">
            <a href="#" aria-label="Account menu" aria-haspopup="true">
              <span uk-icon="icon: user"></span>
              <span class="uk-visible@m sb-username"><?php echo defined('YOURLS_USER') ? htmlspecialchars( YOURLS_USER ) : ''; ?></span>
              <span uk-icon="icon: chevron-down; ratio: 0.8"></span>
            </a>
            <div class="uk-navbar-dropdown sb-account-dropdown">
              <ul class="uk-nav uk-navbar-dropdown-nav">
                <li class="uk-nav-header">Signed in as<br><strong><?php echo defined('YOURLS_USER') ? htmlspecialchars( YOURLS_USER ) : 'guest'; ?></strong></li>
                <li class="uk-nav-divider"></li>
                <li><a href="<?php echo yourls_esc_attr($site); ?>" target="_blank" rel="noopener"><span uk-icon="icon: world"></span> View public site</a></li>
                <li><a href="<?php echo yourls_esc_attr($logout_url); ?>"><span uk-icon="icon: sign-out"></span> Logout</a></li>
              </ul>
            </div>
          </li>
          <li class="uk-hidden@s">
            <a href="#" aria-label="Open menu" uk-toggle="target: #sb-mobile-menu"><span uk-icon="icon: menu"></span></a>
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
          <li class="<?php echo ($is_settings ? 'uk-active' : ''); ?>"><a href="<?php echo yourls_esc_attr($settings_url); ?>"><span uk-icon="icon: settings"></span> Config</a></li>
          <li class="uk-nav-divider"></li>
          <li class="uk-nav-header">Signed in as<br><strong><?php echo defined('YOURLS_USER') ? yourls_esc_html(YOURLS_USER) : 'guest'; ?></strong></li>
          <li><a href="<?php echo yourls_esc_attr($site); ?>" target="_blank" rel="noopener"><span uk-icon="icon: world"></span> View public site</a></li>
          <li><a href="<?php echo yourls_esc_attr($logout_url); ?>"><span uk-icon="icon: sign-out"></span> Logout</a></li>
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
    echo '<h3 class="uk-card-title sb-links-title"><span uk-icon="icon: link"></span> Your short links</h3>';
}

function yourls_ui_kit_template_table_close() {
    // YOURLS closes main with the table; layout wrappers are added inside it by skin.js.
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
    <div class="sb-dashboard-container">
      <h2 class="sb-page-title">Dashboard</h2>
      <p class="uk-text-muted">A quick overview of your shortener.</p>
    </div>

    <div class="sb-dashboard-container">
      <!-- Stat tiles -->
      <div class="sb-dashboard-stats sb-stats">
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

      <div class="sb-dashboard-lists uk-margin-top">

        <!-- Top performers -->
        <div>
          <div class="uk-card uk-card-default uk-card-body sb-list-card uk-card-small">
            <h3 class="uk-card-title"><span uk-icon="icon: star"></span> Top performers</h3>
            <?php if ( !$top ) : ?>
              <p class="uk-text-muted">No links yet.</p>
            <?php else: ?>
              <div class="uk-overflow-container">
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
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Recent links -->
        <div>
          <div class="uk-card uk-card-default uk-card-body sb-list-card uk-card-small">
            <h3 class="uk-card-title"><span uk-icon="icon: clock"></span> Recently created</h3>
            <?php if ( !$recent ) : ?>
              <p class="uk-text-muted">No links yet.</p>
            <?php else: ?>
            <div class="uk-overflow-container">
              <table class="uk-table uk-table-divider uk-table-small uk-table-middle">
                <thead><tr><th>Short URL</th><th>Destination</th><th class="uk-text-right">When</th></tr></thead>
                <tbody>
                <?php foreach ( $recent as $row ) : ?>
                  <tr>
                    <td class="sb-truncate"><a href="<?php echo yourls_link( $row->keyword ); ?>" target="_blank"><?php echo htmlspecialchars( $row->keyword ); ?></a></td>
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
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="uk-margin-top uk-text-center">
        <a class="uk-button uk-button-primary sb-cta" href="<?php echo yourls_admin_url('index.php'); ?>">
          <span uk-icon="icon: plus"></span> Manage all links
        </a>
      </div>
                  </div>
    <?php
}

function yourls_ui_kit_template_logo_url($kind = 'logo') {
    if (!in_array($kind, ['logo', 'favicon'], true)) return '';
    $name = yourls_get_option('sb_uikit_' . $kind, '');
    if (!is_string($name) || !preg_match('/^' . $kind . '-[a-f0-9]{32}\.png$/D', $name)) return '';
    if (!is_file(YOURLS_USERDIR . '/ui-kit-uploads/' . $name)) return '';
    return rtrim(YOURLS_USERURL, '/') . '/ui-kit-uploads/' . $name;
}

function yourls_ui_kit_template_brand() {
    $name = (string) parse_url(YOURLS_SITE, PHP_URL_HOST);
    $logo = yourls_ui_kit_template_logo_url();
    if ($logo !== '') {
        echo '<img class="sb-custom-logo" src="' . yourls_esc_attr($logo) . '" alt="' . yourls_esc_attr($name) . '">';
    } else {
        echo '<span class="sb-logo-text">' . yourls_esc_html($name) . '</span>';
    }
}

function yourls_ui_kit_template_delete_logo($name) {
    if (is_string($name) && preg_match('/^(?:logo|favicon)-[a-f0-9]{32}\.png$/D', $name)) {
        $path = YOURLS_USERDIR . '/ui-kit-uploads/' . $name;
        if (is_file($path) && !unlink($path)) error_log('UIkit: unable to remove old logo.');
    }
}

function yourls_ui_kit_template_save_logo($kind = 'logo') {
    if (!in_array($kind, ['logo', 'favicon'], true)) throw new RuntimeException('Invalid image type.');
    yourls_verify_nonce('yourls-ui-kit-template_' . $kind);
    $option = 'sb_uikit_' . $kind;
    $label = ucfirst($kind);
    $old = yourls_get_option($option, '');
    if (isset($_POST['sb_' . $kind . '_remove'])) {
        if ($old !== '' && !yourls_update_option($option, '')) throw new RuntimeException('Unable to remove the image.');
        yourls_ui_kit_template_delete_logo($old);
        return $label . ' removed.';
    }
    $file = $_FILES['sb_' . $kind] ?? [];
    if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
        || !isset($file['tmp_name']) || !is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Select an image to upload. Maximum size: 2 MB.');
    }
    $size = filesize($file['tmp_name']);
    if ($size === false || $size < 1 || $size > 2 * 1024 * 1024) throw new RuntimeException('The image must be no larger than 2 MB.');
    $info = @getimagesize($file['tmp_name']);
    if (!$info || !in_array($info[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_WEBP], true)
        || $info[0] > 4096 || $info[1] > 4096 || $info[0] * $info[1] > 4000000) {
        throw new RuntimeException('Use a PNG, JPEG or WebP image, up to 4096 pixels per side and 4 megapixels.');
    }
    if ($kind === 'favicon' && $info[0] !== $info[1]) throw new RuntimeException('The favicon must be a square image.');
    if (!function_exists('imagecreatefromstring')) throw new RuntimeException('Image processing is unavailable on this server.');
    $image = @imagecreatefromstring(file_get_contents($file['tmp_name']));
    if (!$image) throw new RuntimeException('The image could not be read.');
    $dir = YOURLS_USERDIR . '/ui-kit-uploads';
    $path = '';
    try {
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) throw new RuntimeException('Unable to create the image folder.');
        $name = $kind . '-' . bin2hex(random_bytes(16)) . '.png';
        $path = $dir . '/' . $name;
        imagealphablending($image, false);
        imagesavealpha($image, true);
        // Re-encode instead of storing the supplied file or its filename.
        if (!@imagepng($image, $path)) throw new RuntimeException('Unable to store the image.');
        if (!yourls_update_option($option, $name)) throw new RuntimeException('Unable to save the image.');
    } catch (Throwable $e) {
        if ($path !== '' && is_file($path)) unlink($path);
        error_log('UIkit logo upload: ' . $e->getMessage());
        throw new RuntimeException('Unable to save the image. Check folder permissions and try again.');
    } finally {
        imagedestroy($image);
    }
    yourls_ui_kit_template_delete_logo($old);
    return $label . ' uploaded. Reload the page to see the updated image.';
}

yourls_add_filter('get_favicon_url', 'yourls_ui_kit_template_favicon');

function yourls_ui_kit_template_favicon($default) {
    $url = yourls_ui_kit_template_logo_url('favicon');
    return $url !== '' ? $url : $default;
}

// One schema drives rendering, validation and CSS output.
function yourls_ui_kit_template_fields() {
    return [
        'accent' => ['Accent color', 'color', '#1e87f0', 'Colors'],
        'link' => ['Link color', 'color', '#1e87f0', 'Colors'],
        'link-hover' => ['Link hover color', 'color', '#1668b8', 'Colors'],
        'bg' => ['Panel background', 'color', '#f6f8fb', 'Colors'],
        'card-bg' => ['Card and table background', 'color', '#ffffff', 'Colors'],
        'text' => ['Primary text', 'color', '#1f2937', 'Colors'],
        'text-muted' => ['Secondary text', 'color', '#6b7280', 'Colors'],
        'border' => ['Border color', 'color', '#e5e9f0', 'Borders and cards'],
        'border-width' => ['Border width (px)', 'number', 1, 'Borders and cards', 0, 4],
        'radius' => ['Card corner radius (px)', 'number', 10, 'Borders and cards', 0, 32],
        'control-radius' => ['Input corner radius (px)', 'number', 6, 'Borders and cards', 0, 24],
        'button-radius' => ['Button corner radius (px)', 'number', 6, 'Buttons', 0, 50],
        'button-y' => ['Button vertical padding (px)', 'number', 10, 'Buttons', 4, 20],
        'button-x' => ['Button horizontal padding (px)', 'number', 20, 'Buttons', 8, 40],
        'button-text' => ['Primary button text color', 'color', '#ffffff', 'Buttons'],
        'new-url-bg' => ['Shorten bar background', 'color', '#ffffff', 'Shorten bar'],
        'new-url-text' => ['Shorten bar text', 'color', '#6b7280', 'Shorten bar'],
        'new-url-padding' => ['Shorten bar padding (px)', 'number', 16, 'Shorten bar', 4, 40],
        'input-bg' => ['Input and select background', 'color', '#ffffff', 'Inputs and search'],
        'input-text' => ['Input and select text', 'color', '#1f2937', 'Inputs and search'],
        'input-y' => ['Input vertical padding (px)', 'number', 10, 'Inputs and search', 2, 20],
        'filter-bg' => ['Search panel background', 'color', '#ffffff', 'Inputs and search'],
        'filter-text' => ['Search panel text', 'color', '#1f2937', 'Inputs and search'],
        'table-head-bg' => ['Table header background', 'color', '#f9fafb', 'Tables'],
        'table-head-text' => ['Table header text', 'color', '#6b7280', 'Tables'],
        'table-row-bg' => ['Row background', 'color', '#ffffff', 'Tables'],
        'table-alt-bg' => ['Alternate row background', 'color', '#f9fafb', 'Tables'],
        'table-hover-bg' => ['Row hover background', 'color', '#f1f5f9', 'Tables'],
        'table-foot-bg' => ['Table footer background', 'color', '#f9fafb', 'Tables'],
        'table-cell-y' => ['Cell vertical padding (px)', 'number', 14, 'Tables', 4, 28],
        'table-font-size' => ['Table font size (px)', 'number', 14, 'Tables', 11, 20],
        'navbar-bg' => ['Menu background', 'color', '#ffffff', 'Menu'],
        'navbar-text' => ['Menu text', 'color', '#1f2937', 'Menu'],
        'footer-bg' => ['Footer background', 'color', '#ffffff', 'Footer'],
        'footer-text' => ['Footer text', 'color', '#6b7280', 'Footer'],
        'footer-padding' => ['Footer padding (px)', 'number', 12, 'Footer', 0, 32],
        'login-start' => ['Gradient start', 'color', '#667eea', 'Login'],
        'login-end' => ['Gradient end', 'color', '#764ba2', 'Login'],
        'login-angle' => ['Gradient angle (degrees)', 'number', 135, 'Login', 0, 360],
        'login-width' => ['Card width (px)', 'number', 400, 'Login', 300, 600],
    ];
}

function yourls_ui_kit_template_valid_value($value, $field) {
    if (!is_scalar($value)) return false;
    if ($field[1] === 'color') return preg_match('/^#[0-9a-fA-F]{6}$/D', (string) $value) === 1;
    return filter_var($value, FILTER_VALIDATE_INT) !== false && $value >= $field[4] && $value <= $field[5];
}

function yourls_ui_kit_template_settings() {
    $stored = yourls_get_option('sb_uikit_theme', []);
    if (!is_array($stored)) $stored = [];
    $settings = [];
    foreach (yourls_ui_kit_template_fields() as $key => $field) {
        $value = $stored[$key] ?? ($key === 'accent' ? yourls_get_option('sb_uikit_accent', $field[2]) : $field[2]);
        $settings[$key] = yourls_ui_kit_template_valid_value($value, $field) ? $value : $field[2];
    }
    return $settings;
}

function yourls_ui_kit_template_settings_page() {
    $fields = yourls_ui_kit_template_fields();
    $settings = yourls_ui_kit_template_settings();
    $message = '';
    $error = false;
    if (isset($_POST['sb_logo_upload']) || isset($_POST['sb_logo_remove'])) {
        try {
            $message = yourls_ui_kit_template_save_logo();
        } catch (RuntimeException $e) {
            $error = true;
            $message = $e->getMessage();
        }
    }
    if (isset($_POST['sb_favicon_upload']) || isset($_POST['sb_favicon_remove'])) {
        try {
            $message = yourls_ui_kit_template_save_logo('favicon');
        } catch (RuntimeException $e) {
            $error = true;
            $message = $e->getMessage();
        }
    }
    if (isset($_POST['sb_uikit_save']) || isset($_POST['sb_uikit_reset'])) {
        yourls_verify_nonce('yourls-ui-kit-template_settings');
        $next = [];
        foreach ($fields as $key => $field) {
            $value = isset($_POST['sb_uikit_reset']) ? $field[2] : ($_POST['sb_theme'][$key] ?? null);
            if (!yourls_ui_kit_template_valid_value($value, $field)) {
                $error = true;
                $message = 'Check this field: ' . $field[0] . '. No changes were saved.';
                break;
            }
            $next[$key] = $field[1] === 'number' ? (int) $value : $value;
        }
        if (!$error) {
            if ($next === yourls_get_option('sb_uikit_theme', []) || yourls_update_option('sb_uikit_theme', $next)) {
                $settings = $next;
                $message = isset($_POST['sb_uikit_reset']) ? 'Default settings restored.' : 'Settings saved.';
            } else {
                $error = true;
                $message = 'Unable to save settings.';
            }
        }
    }
    ?>
    <div class="uk-container sb-theme-settings">
      <h2 class="sb-page-title">Template settings</h2>
      <p>Customize the admin panel and login. Changes are previewed here; save to apply them across the site.</p>
      <?php if ($message !== ''): ?>
        <div role="status" class="<?php echo $error ? 'uk-alert-danger' : 'uk-alert-success'; ?>" uk-alert><?php echo yourls_esc_html($message); ?></div>
      <?php endif; ?>
      <fieldset class="uk-card uk-card-default uk-card-body sb-logo-settings">
        <legend>Logo</legend>
        <p>Displayed in the admin menu and on the login screen. PNG, JPEG or WebP, up to 2 MB and 4 megapixels. Transparent PNG recommended.</p>
        <?php if (yourls_ui_kit_template_logo_url() !== ''): ?>
          <div class="sb-logo-preview uk-margin"><?php yourls_ui_kit_template_brand(); ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="uk-form-stacked">
          <input type="hidden" name="nonce" value="<?php echo yourls_esc_attr(yourls_create_nonce('yourls-ui-kit-template_logo')); ?>">
          <label class="uk-form-label" for="sb-logo-upload">Logo image</label>
          <input type="file" id="sb-logo-upload" name="sb_logo" accept="image/png,image/jpeg,image/webp" class="uk-margin-small-bottom">
          <div class="uk-margin-small-top">
            <button type="submit" name="sb_logo_upload" value="1" class="uk-button uk-button-primary">Upload logo</button>
            <?php if (yourls_ui_kit_template_logo_url() !== ''): ?>
              <button type="submit" name="sb_logo_remove" value="1" class="uk-button uk-button-default">Remove logo</button>
            <?php endif; ?>
          </div>
        </form>
        <hr>
        <h3>Favicon</h3>
        <p>Browser tab icon for the site and admin screens. Upload a square PNG, JPEG or WebP, up to 2 MB and 4 megapixels. A 32 × 32 or 64 × 64 transparent PNG is recommended.</p>
        <?php if (yourls_ui_kit_template_logo_url('favicon') !== ''): ?>
          <img src="<?php echo yourls_esc_attr(yourls_ui_kit_template_logo_url('favicon')); ?>" alt="Current favicon" width="32" height="32" class="uk-margin-small-bottom">
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="uk-form-stacked">
          <input type="hidden" name="nonce" value="<?php echo yourls_esc_attr(yourls_create_nonce('yourls-ui-kit-template_favicon')); ?>">
          <label class="uk-form-label" for="sb-favicon-upload">Favicon image</label>
          <input type="file" id="sb-favicon-upload" name="sb_favicon" accept="image/png,image/jpeg,image/webp" class="uk-margin-small-bottom">
          <div class="uk-margin-small-top">
            <button type="submit" name="sb_favicon_upload" value="1" class="uk-button uk-button-primary">Upload favicon</button>
            <?php if (yourls_ui_kit_template_logo_url('favicon') !== ''): ?>
              <button type="submit" name="sb_favicon_remove" value="1" class="uk-button uk-button-default">Remove favicon</button>
            <?php endif; ?>
          </div>
        </form>
      </fieldset>
      <form method="post" class="uk-form-stacked" id="sb-theme-form">
        <input type="hidden" name="nonce" value="<?php echo yourls_esc_attr(yourls_create_nonce('yourls-ui-kit-template_settings')); ?>">
        <div class="sb-settings-grid">
        <?php $group = ''; foreach ($fields as $key => $field): ?>
          <?php if ($group !== $field[3]): ?>
            <?php if ($group !== '') echo '</fieldset>'; $group = $field[3]; ?>
            <fieldset class="uk-card uk-card-default uk-card-body"><legend><?php echo yourls_esc_html($group); ?></legend>
          <?php endif; ?>
          <div class="uk-margin">
            <label class="uk-form-label" for="sb-<?php echo $key; ?>"><?php echo yourls_esc_html($field[0]); ?></label>
            <div class="sb-setting-control">
              <input class="uk-input" id="sb-<?php echo $key; ?>" name="sb_theme[<?php echo $key; ?>]" type="<?php echo $field[1]; ?>" value="<?php echo yourls_esc_attr((string) $settings[$key]); ?>" data-theme-key="<?php echo $key; ?>" <?php if ($field[1] === 'number') echo 'min="'.$field[4].'" max="'.$field[5].'" step="1"'; ?> required>
              <?php if ($field[1] === 'color'): ?><input class="uk-input sb-color-hex" aria-label="<?php echo yourls_esc_attr($field[0] . ' hexadecimal'); ?>" value="<?php echo yourls_esc_attr($settings[$key]); ?>" data-color-for="sb-<?php echo $key; ?>" pattern="#[0-9a-fA-F]{6}" maxlength="7" required><?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?></fieldset>
        </div>
        <div class="uk-margin">
          <button class="uk-button uk-button-primary" type="submit" name="sb_uikit_save" value="1">Save changes</button>
          <button class="uk-button uk-button-default" type="submit" name="sb_uikit_reset" value="1" formnovalidate>Restore defaults</button>
        </div>
      </form>
      <div class="uk-card uk-card-default uk-card-body uk-margin sb-theme-preview">
        <h3>Preview</h3>
        <p>Card with primary text, <span style="color:var(--sb-text-muted)">secondary text</span> and <a href="#sb-theme-form">link</a>.</p>
        <input class="text" placeholder="Example input" aria-label="Example input">
        <button type="button" class="uk-button uk-button-primary">Primary button</button>
        <div class="sb-new-url-preview uk-margin">
          <label for="sb-preview-url">URL to shorten</label>
          <input id="sb-preview-url" class="text" placeholder="https://example.com">
          <button type="button" class="uk-button uk-button-primary">Shorten URL</button>
        </div>
        <div class="uk-overflow-auto">
          <table class="sb-preview-table">
            <thead><tr><th>Short URL</th><th>Destination</th><th>Clicks</th></tr></thead>
            <tbody>
              <tr><td><a href="#sb-theme-form">example</a></td><td>example.com</td><td>25</td></tr>
              <tr><td><a href="#sb-theme-form">offer</a></td><td>example.com/offer</td><td>12</td></tr>
            </tbody>
            <tfoot><tr><td colspan="3">
              <div class="sb-filter-preview">
                <label for="sb-preview-search">Search</label>
                <input id="sb-preview-search" class="text" placeholder="Link">
                <select aria-label="Example sort order"><option>Newest first</option><option>Most clicks</option></select>
                <button type="button" class="uk-button uk-button-primary">Search</button>
              </div>
            </td></tr></tfoot>
          </table>
        </div>
        <div class="sb-login-preview"><div>Login</div></div>
        <div class="sb-footer-preview uk-margin-top"><p>Powered by <a href="https://yourls.org/" target="_blank" rel="noopener">YOURLS</a></p></div>
      </div>
    </div>
    <?php
}

yourls_add_action('html_head', 'yourls_ui_kit_template_inline_vars', 6);

function yourls_ui_kit_template_inline_vars() {
    echo '<style>:root{';
    foreach (yourls_ui_kit_template_settings() as $key => $value) {
        $unit = $key === 'login-angle' ? 'deg' : (is_numeric($value) ? 'px' : '');
        echo '--sb-' . $key . ':' . $value . $unit . ';';
    }
    echo "}</style>\n";
}

// Theme Google Charts through YOURLS filters, without changing core files.
yourls_add_filter('stats_line_options', 'yourls_ui_kit_template_line_options');
yourls_add_filter('stats_pie_options', 'yourls_ui_kit_template_pie_options');
yourls_add_filter('stats_countries_map_options', 'yourls_ui_kit_template_map_options');

function yourls_ui_kit_template_chart_options($options) {
    $theme = yourls_ui_kit_template_settings();
    $options['backgroundColor'] = $theme['card-bg'];
    $options['width'] = '100%';
    $options['fontName'] = 'Arial';
    return $options;
}

function yourls_ui_kit_template_chart_palette() {
    $theme = yourls_ui_kit_template_settings();
    $rgb = sscanf($theme['accent'], '#%02x%02x%02x');
    $palette = [];
    foreach ([0, .18, .36, .54, .72] as $mix) {
        $palette[] = sprintf('#%02x%02x%02x',
            (int) round($rgb[0] + (255 - $rgb[0]) * $mix),
            (int) round($rgb[1] + (255 - $rgb[1]) * $mix),
            (int) round($rgb[2] + (255 - $rgb[2]) * $mix));
    }
    return $palette;
}

function yourls_ui_kit_template_line_options($options) {
    $theme = yourls_ui_kit_template_settings();
    $options = yourls_ui_kit_template_chart_options($options);
    $options['colors'] = json_encode([$theme['accent']]);
    $options['hAxis'] = json_encode(['minTextSpacing' => 80, 'maxTextLines' => 1, 'maxAlternation' => 1,
        'textStyle' => ['color' => $theme['text-muted']], 'baselineColor' => $theme['border']]);
    $options['vAxis'] = json_encode(['minValue' => 0, 'format' => '#',
        'textStyle' => ['color' => $theme['text-muted']], 'gridlines' => ['color' => $theme['border']],
        'baselineColor' => $theme['border']]);
    return $options;
}

function yourls_ui_kit_template_pie_options($options) {
    $theme = yourls_ui_kit_template_settings();
    $options = yourls_ui_kit_template_chart_options($options);
    $options['colors'] = json_encode(yourls_ui_kit_template_chart_palette());
    $options['pieSliceBorderColor'] = $theme['card-bg'];
    $options['pieSliceTextStyle'] = json_encode(['color' => $theme['text']]);
    return $options;
}

function yourls_ui_kit_template_map_options($options) {
    $theme = yourls_ui_kit_template_settings();
    $options = yourls_ui_kit_template_chart_options($options);
    $options['colorAxis'] = json_encode(['colors' => array_reverse(yourls_ui_kit_template_chart_palette())]);
    $options['datalessRegionColor'] = $theme['table-alt-bg'];
    return $options;
}
