# ToolTipWP - AI Coding Agent Instructions

## Project Overview
ToolTipWP is a WordPress plugin that adds customizable tooltip icons to content by targeting CSS classes. Users create tooltip definitions via custom post type, then add classes to elements in posts/pages to automatically inject tooltip icons with hover text.

**Key architecture**: Custom Post Type (`tooltip`) stores tooltip definitions → jQuery frontend script (`tooltipwp.js`) matches classes → dynamically injects Font Awesome icons with tooltip text.

## Critical Namespace Convention
**ALL PHP code uses namespace `TooltipWP` or sub-namespaces** (`TooltipWP\Functions`, `TooltipWP\Ajax`, `TooltipWP\GitHubUpdater`). Functions are called with `__NAMESPACE__` or fully qualified names:
```php
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts');
// or
add_action('init', 'TooltipWP\\register_tooltip_cpt');
```

## File Structure & Responsibilities
- `tooltipwp.php` - Main plugin file, registers CPT, meta boxes, core hooks
- `app/functions.php` - Meta box for creating tooltips from post/page editor, dynamic inline CSS generation
- `app/ajax-handlers.php` - AJAX endpoint for creating tooltips (`wp_ajax_create_new_tooltip`)
- `app/admin-settings.php` - Settings page under Tooltips menu (appearance customization: colors, border)
- `app/assets/js/tooltipwp.js` - Frontend jQuery that reads `tooltipData` and injects tooltip icons
- `app/assets/js/admin-scripts.js` - Admin AJAX for creating tooltips from meta box
- `app/assets/css/tooltipwp.css` - Tooltip styling (hover effects, positioning)
- `src/index.js` - Webpack entry point that imports Font Awesome CSS
- `build/` - Compiled webpack assets (Font Awesome fonts + CSS)
- `github-update.php` - Custom GitHub updater (checks welbinator/tooltipwp releases)

## Data Flow: How Tooltips Work
1. **Admin creates tooltip CPT entry** with meta fields:
   - `_tooltip_text` - hover text to display
   - `_tooltip_class` - CSS class to target (e.g., `my-tooltip`)
   - `_tooltip_position` - "before" or "after" target element
   - `_tooltip_icon` - Font Awesome class (default: `fa-solid fa-circle-info`)

2. **WordPress localizes data**: `localize_script()` in `tooltipwp.php` queries all `tooltip` posts and passes data to frontend as `tooltipData` array

3. **Frontend injection**: `tooltipwp.js` loops through `tooltipData`, finds elements with matching class, appends/prepends `<i>` tag with icon and `data-tooltip` attribute

4. **CSS reveals on hover**: `.tooltip-icon:hover::after` shows the tooltip text from `data-tooltip`

## Build & Development Commands
```bash
# Frontend assets (Font Awesome bundling)
npm run start   # Development watch mode with @wordpress/scripts
npm run build   # Production build to build/scripts.js and build/scripts.css

# PHP code quality
composer phpcs:wp   # Check WordPress coding standards
composer phpcbf:wp  # Auto-fix coding standards
```

**Note**: Tailwind is configured but not actively used in content pipeline. Build system is primarily for Font Awesome bundling.

## Custom Post Type: `tooltip`
Registered without frontend visibility (`'public' => true` but no rewrite/archive). Meta boxes:
- **Tooltip Text** (`_tooltip_text`) - The hover message
- **Tooltip Class** (`_tooltip_class`) - CSS selector to target
- **Tooltip Position** (sidebar) - Radio: before/after
- **Tooltip Icon** - Font Awesome class string

**Default values**: Position defaults to "after", icon defaults to `fa-solid fa-circle-info`.

## AJAX Pattern for Tooltip Creation
`app/ajax-handlers.php` handles `create_new_tooltip` action. Used by admin meta box in post/page editor:
- Nonce: `tooltipwp_create_nonce`
- Creates new `tooltip` CPT via `wp_insert_post()`
- Saves all meta fields via `update_post_meta()`
- Returns new post ID on success

## Settings Page Customization
Located under Tooltips → Settings. Options stored in `wp_options`:
- `tooltipwp_background_color` (default: `#ffffff`)
- `tooltipwp_text_color` (default: `#000000`)
- `tooltipwp_border_color` (default: `#000000`)
- `tooltipwp_border_thickness` (default: `1`)

Inline CSS injection in `app/functions.php` via `tooltipwp_enqueue_with_inline_styles()` dynamically applies settings to `.tooltip-icon::after` pseudo-element.

## GitHub Auto-Updater
`github-update.php` hooks into `pre_set_site_transient_update_plugins` to check `welbinator/tooltipwp` releases. Compares semantic versions and adds update data to transient if new version exists. Uses GitHub API with User-Agent header.

## WordPress Hooks & Filters Used
- `wp_enqueue_scripts` - Frontend asset loading
- `admin_enqueue_scripts` - Admin scripts and wp-color-picker
- `init` - Register CPT
- `add_meta_boxes` - Register tooltip meta boxes
- `save_post` - Save meta box data (no nonce verification currently - security note)
- `admin_menu` - Add settings submenu page
- `admin_init` - Register settings and fields
- `wp_ajax_create_new_tooltip` - AJAX handler

## Key Dependencies
- **@wordpress/scripts** - Build tooling, provides webpack config
- **@fortawesome/fontawesome-free** - Icon library (bundled to build/)
- **jQuery** - Frontend scripting, WordPress default
- **WordPress Color Picker API** - Admin color inputs

## Common Modifications
**Add new tooltip icon option**: Update default in `icon_meta_box_html()` and meta box HTML
**Change tooltip positioning**: Modify `tooltipwp.js` jQuery append/prepend logic
**Adjust hover styles**: Edit `app/assets/css/tooltipwp.css` `.tooltip-icon::after` rules
**Add tooltip animation**: Modify CSS transitions in `.tooltip-icon::after`

## Security Considerations
- **Missing nonce verification** in `save_tooltip_meta_boxes_data()` - should check `wp_verify_nonce()`
- **No capability checks** on meta box save - should verify `current_user_can('edit_post')`
- AJAX handler properly verifies nonce via `wp_verify_nonce()`
- All user inputs sanitized via `sanitize_text_field()` and `esc_attr()`

## Testing Plugin Locally
1. Ensure WordPress environment (Lando setup based on path)
2. Activate plugin in WordPress admin
3. Create tooltip: Tooltips → Add New, set class (e.g., `test-tip`), text, icon
4. Add class to content: In post editor, apply `test-tip` class to text/element
5. View frontend: Hover over icon to see tooltip
6. Test settings: Tooltips → Settings to change colors/borders

## Version Management
Version is controlled by `TOOLTIPWP_VERSION` constant defined at top of `tooltipwp.php`. To update version:
1. Update `TOOLTIPWP_VERSION` constant in `tooltipwp.php`
2. Update plugin header comment version in `tooltipwp.php`
3. Update `package.json` version field
4. Create GitHub release with matching tag for auto-updates

The constant is used throughout for all `wp_enqueue_script()` and `wp_enqueue_style()` version parameters to ensure cache busting on updates.
