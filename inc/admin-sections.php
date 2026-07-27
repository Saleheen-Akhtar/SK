<?php
/**
 * Sacred Kompass — Homepage Section Manager
 */
defined('ABSPATH') || exit;

/* ── Register section options ── */
add_action( 'admin_init', 'sk_section_manager_settings' );
function sk_section_manager_settings(): void {
    $sections = sk_builtin_sections();
    foreach ( $sections as $key => $_ ) {
        register_setting( 'sk_section_manager_group', 'sk_show_' . $key, [
            'type'              => 'boolean',
            'default'           => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ] );
        register_setting( 'sk_section_manager_group', 'sk_admin_only_' . $key, [
            'type'              => 'boolean',
            'default'           => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ] );
    }
    register_setting( 'sk_section_manager_group', 'sk_custom_sections', [
        'type'              => 'string',
        'default'           => '[]',
        'sanitize_callback' => 'sk_sanitize_custom_sections',
    ] );
    register_setting( 'sk_section_manager_group', 'sk_section_order', [
        'type'              => 'string',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
}

add_action( 'update_option',  'sk_bust_nav_on_section_toggle', 10, 3 );
add_action( 'added_option',   'sk_bust_nav_on_section_toggle', 10, 2 );
add_action( 'deleted_option', 'sk_bust_nav_on_section_toggle', 10, 1 );
function sk_bust_nav_on_section_toggle( string $option, $old = null, $new = null ): void {
    if ( str_starts_with( $option, 'sk_show_' ) || str_starts_with( $option, 'sk_admin_only_' ) || $option === 'sk_section_order' ) {
        delete_transient( 'sk_nav_items' );
        delete_transient( 'sk_nav_items_admin' );
        delete_transient( 'sk_nav_items_public' );
        delete_transient( 'sk_has_nav_items' );
    }
}

/* ── Built-in section definitions ── */
function sk_builtin_sections(): array {
    $core = [
        'hero'           => [ 'label' => 'Hero',                    'desc' => 'Full-screen opening poem / disruption statement.',          'template' => 'template-parts/home/hero' ],
        'about'          => [ 'label' => 'About',                   'desc' => 'Who we are and why we exist.',                              'template' => 'template-parts/home/about' ],
        'art'            => [ 'label' => 'Art for Peace',           'desc' => 'Curated digital art gallery focusing on healing, reflection, and art therapy.', 'template' => 'template-parts/home/art' ],
        'philosophy'     => [ 'label' => 'Philosophy Strip',        'desc' => 'Deepens connection after the visitor is already interested.','template' => 'template-parts/home/philosophy-strip' ],
        'founders'       => [ 'label' => 'Founders',                'desc' => 'Who delivers it — peak trust moment.',                      'template' => 'template-parts/home/founders' ],
        'stories_preview' => [ 'label' => 'Stories Preview',        'desc' => 'Featured sk_story posts grid. Background image is editable via settings.',  'template' => 'template-parts/home/stories-preview' ],
        'journal'        => [ 'label' => 'Journal Preview',         'desc' => 'Latest journal entries for engaged visitors.',              'template' => 'template-parts/home/journal-preview' ],
        'faq'            => [ 'label' => 'FAQ',                     'desc' => 'Objection handling before the call-to-action.',             'template' => 'template-parts/home/faq' ],
        'cta'            => [ 'label' => 'CTA / Contact',           'desc' => 'Final conversion — booking / contact form.',               'template' => 'template-parts/home/cta' ],
    ];

    $known_templates = array_column( $core, 'template' );
    $glob_pattern    = get_template_directory() . '/template-parts/home/*.php';
    foreach ( (array) glob( $glob_pattern ) as $file ) {
        $slug     = basename( $file, '.php' );
        $tpl_path = 'template-parts/home/' . $slug;
        if ( in_array( $tpl_path, $known_templates, true ) ) continue;
        if ( in_array( $slug, [ 'testimonials', 'client-stories', 'values' ], true ) ) continue;
        $key = sanitize_key( str_replace( '-', '_', $slug ) );
        if ( isset( $core[ $key ] ) ) $key = 'extra_' . $key;
        $label = ucwords( str_replace( [ '-', '_' ], ' ', $slug ) );
        $core[ $key ] = [
            'label'    => $label,
            'desc'     => 'Auto-discovered section (' . $slug . '.php).',
            'template' => $tpl_path,
        ];
    }
    return $core;
}

function sk_sanitize_custom_sections( string $raw ): string {
    $data = json_decode( stripslashes( $raw ), true );
    if ( ! is_array( $data ) ) return '[]';
    $clean = [];
    foreach ( $data as $item ) {
        if ( empty( $item['id'] ) ) continue;
        $clean[] = [
            'id'      => sanitize_key( $item['id'] ),
            'label'   => sanitize_text_field( $item['label']   ?? '' ),
            'content' => wp_kses_post( $item['content'] ?? '' ),
            'enabled' => ! empty( $item['enabled'] ),
        ];
    }
    return wp_json_encode( $clean );
}

add_action( 'admin_menu', 'sk_section_manager_menu', 100 );
function sk_section_manager_menu(): void {
    add_submenu_page(
        'sk-settings',
        __( 'Homepage Sections', 'sacred-kompass' ),
        __( '✦ Homepage Sections', 'sacred-kompass' ),
        'edit_posts',
        'sk-homepage-sections',
        'sk_section_manager_page'
    );
}

function sk_section_manager_page(): void {
    if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Access denied.' );
    $builtin        = sk_builtin_sections();
    $custom_raw     = get_option( 'sk_custom_sections', '[]' );
    $custom         = json_decode( $custom_raw, true ) ?: [];
    $order_raw      = get_option( 'sk_section_order', '' );
    $saved_order    = $order_raw ? explode( ',', $order_raw ) : [];

    $all_keys = array_keys( $builtin );
    foreach ( $custom as $c ) { $all_keys[] = 'custom_' . $c['id']; }
    if ( $saved_order ) {
        $merged = array_unique( array_merge( $saved_order, $all_keys ) );
        $all_keys = array_values( array_filter( $merged, fn($k) => in_array( $k, $all_keys, true ) ) );
    }
    ?>
    <div class="wrap sk-sm-wrap">
        <h1 class="sk-sm-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-4px;margin-right:8px;opacity:.7"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            <?php esc_html_e( 'Homepage Section Manager', 'sacred-kompass' ); ?>
        </h1>
        <p class="sk-sm-desc"><?php esc_html_e( 'Toggle built-in sections, drag to reorder, and add custom HTML sections. Changes apply instantly on save.', 'sacred-kompass' ); ?></p>

        <?php settings_errors( 'sk_section_manager_group' ); ?>

        <form method="post" action="options.php" id="sk-sm-form">
            <?php settings_fields( 'sk_section_manager_group' ); ?>

            <input type="hidden" name="sk_section_order"   id="sk-section-order-field"   value="<?php echo esc_attr( implode( ',', $all_keys ) ); ?>">
            <input type="hidden" name="sk_custom_sections" id="sk-custom-sections-field" value="<?php echo esc_attr( $custom_raw ); ?>">

            <div class="sk-sm-cols">
                <div class="sk-sm-col-main">
                    <div class="sk-sm-panel">
                        <div class="sk-sm-panel-head">
                            <span><?php esc_html_e( 'Section Order & Visibility', 'sacred-kompass' ); ?></span>
                            <span class="sk-sm-hint"><?php esc_html_e( '↕ drag to reorder', 'sacred-kompass' ); ?></span>
                        </div>
                        <ul class="sk-sm-sortable" id="sk-sortable">
                        <?php foreach ( $all_keys as $key ) :
                            $is_custom = str_starts_with( $key, 'custom_' );
                            if ( $is_custom ) {
                                $cid   = substr( $key, 7 );
                                $found = array_filter( $custom, fn($c) => $c['id'] === $cid );
                                if ( ! $found ) continue;
                                $c     = array_values( $found )[0];
                                $label = $c['label'] ?: 'Custom Section';
                                $desc  = 'Custom HTML section';
                                $enabled = (bool) $c['enabled'];
                            } else {
                                if ( ! isset( $builtin[ $key ] ) ) continue;
                                $label      = $builtin[ $key ]['label'];
                                $desc       = $builtin[ $key ]['desc'];
                                $enabled    = (bool) get_option( 'sk_show_' . $key, true );
                                $admin_only = (bool) get_option( 'sk_admin_only_' . $key, false );
                            }
                        ?>
                        <li class="sk-sm-row<?php echo $enabled ? '' : ' sk-sm-row--off'; ?><?php echo ( ! $is_custom && ! empty( $admin_only ) ) ? ' sk-sm-row--admin-only' : ''; ?>" data-key="<?php echo esc_attr( $key ); ?>">
                            <span class="sk-sm-drag" title="Drag to reorder">⠿</span>
                            <div class="sk-sm-row-body">
                                <strong class="sk-sm-row-label"><?php echo esc_html( $label ); ?></strong>
                                <span class="sk-sm-row-desc"><?php echo esc_html( $desc ); ?></span>
                                <?php if ( ! $is_custom && ! empty( $admin_only ) ) : ?>
                                    <span class="sk-sm-admin-badge" title="<?php esc_attr_e( 'Visible to logged-in editors only', 'sacred-kompass' ); ?>">&#128274; Admin only</span>
                                <?php endif; ?>
                            </div>
                            <div class="sk-sm-row-actions">
                                <?php if ( $is_custom ) : ?>
                                    <button type="button" class="sk-sm-btn-edit button button-small" data-id="<?php echo esc_attr( $cid ); ?>"><?php esc_html_e( 'Edit', 'sacred-kompass' ); ?></button>
                                    <button type="button" class="sk-sm-btn-delete button button-small button-link-delete" data-id="<?php echo esc_attr( $cid ); ?>"><?php esc_html_e( 'Remove', 'sacred-kompass' ); ?></button>
                                <?php endif; ?>
                                <?php if ( ! $is_custom ) : ?>
                                <label class="sk-sm-toggle sk-sm-toggle--admin" title="<?php esc_attr_e( 'Admin Only — when on, section is hidden from public but visible to logged-in editors', 'sacred-kompass' ); ?>" style="flex-direction:column;align-items:center;gap:2px;">
                                    <span style="font-size:10px;font-weight:600;letter-spacing:.04em;color:#777;text-transform:uppercase;line-height:1;">Admin</span>
                                    <input type="hidden" name="sk_admin_only_<?php echo esc_attr( $key ); ?>" value="0">
                                    <input type="checkbox" name="sk_admin_only_<?php echo esc_attr( $key ); ?>" value="1" <?php checked( ! empty( $admin_only ) ); ?> hidden>
                                    <span class="sk-sm-toggle-track sk-sm-toggle-track--admin">
                                        <span class="sk-sm-toggle-thumb"></span>
                                    </span>
                                </label>
                                <?php endif; ?>
                                <label class="sk-sm-toggle" title="<?php echo $enabled ? esc_attr__( 'Visible — click to hide', 'sacred-kompass' ) : esc_attr__( 'Hidden — click to show', 'sacred-kompass' ); ?>">
                                    <?php if ( $is_custom ) : ?>
                                        <input type="checkbox" class="sk-sm-custom-toggle" data-id="<?php echo esc_attr( $cid ); ?>" <?php checked( $enabled ); ?> hidden>
                                    <?php else : ?>
                                        <input type="hidden" name="sk_show_<?php echo esc_attr( $key ); ?>" value="0">
                                        <input type="checkbox" name="sk_show_<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $enabled ); ?> hidden>
                                    <?php endif; ?>
                                    <span class="sk-sm-toggle-track">
                                        <span class="sk-sm-toggle-thumb"></span>
                                    </span>
                                </label>
                            </div>
                        </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="sk-sm-col-side">
                    <div class="sk-sm-panel" id="sk-custom-form-panel">
                        <div class="sk-sm-panel-head">
                            <span id="sk-custom-form-title"><?php esc_html_e( 'Add Custom Section', 'sacred-kompass' ); ?></span>
                        </div>
                        <div class="sk-sm-panel-body">
                            <input type="hidden" id="sk-edit-id" value="">
                            <label class="sk-sm-field-label" for="sk-new-label"><?php esc_html_e( 'Section Name', 'sacred-kompass' ); ?></label>
                            <input type="text" id="sk-new-label" class="regular-text sk-sm-input" placeholder="e.g. Newsletter Banner">

                            <label class="sk-sm-field-label" for="sk-new-content" style="margin-top:14px;"><?php esc_html_e( 'HTML Content', 'sacred-kompass' ); ?></label>
                            <textarea id="sk-new-content" class="sk-sm-textarea" rows="10" placeholder="<section class=&quot;my-section&quot;>&#10;  &lt;div class=&quot;wrap&quot;&gt;&#10;    &lt;h2&gt;Your heading&lt;/h2&gt;&#10;    &lt;p&gt;Your content here.&lt;/p&gt;&#10;  &lt;/div&gt;&#10;&lt;/section&gt;"></textarea>

                            <div class="sk-sm-field-actions">
                                <button type="button" class="button button-primary sk-sm-btn-full" id="sk-add-custom"><?php esc_html_e( '+ Add Section', 'sacred-kompass' ); ?></button>
                                <button type="button" class="button sk-sm-btn-full" id="sk-cancel-edit" style="display:none;"><?php esc_html_e( 'Cancel', 'sacred-kompass' ); ?></button>
                            </div>
                            <p class="sk-sm-field-hint"><?php esc_html_e( 'HTML is sanitised on save. The section will appear at the bottom of the list — drag it to your preferred position.', 'sacred-kompass' ); ?></p>
                        </div>
                    </div>
                </div>
            </div><!-- /cols -->

            <div class="sk-sm-submit-row">
                <?php submit_button( __( 'Save All Changes', 'sacred-kompass' ), 'primary large', 'submit', false ); ?>
            </div>
        </form>
    </div>

    <style>
    .sk-sm-wrap { max-width: 1100px; }
    .sk-sm-title { display:flex; align-items:center; font-size:22px; font-weight:600; margin-bottom:4px; }
    .sk-sm-desc  { color:#666; margin-bottom:24px; }
    .sk-sm-cols  { display:grid; grid-template-columns:1fr 380px; gap:24px; align-items:start; }
    .sk-sm-panel { background:#fff; border:1px solid #ddd; border-radius:8px; overflow:hidden; }
    .sk-sm-panel-head { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; background:#f8f8f8; border-bottom:1px solid #eee; font-weight:600; font-size:13px; color:#1e1e1e; }
    .sk-sm-hint  { font-weight:400; color:#999; font-size:12px; }
    .sk-sm-panel-body { padding:18px; }
    .sk-sm-sortable { list-style:none; margin:0; padding:0; }
    .sk-sm-row   { display:flex; align-items:center; gap:12px; padding:13px 18px; border-bottom:1px solid #f0f0f0; transition:background .15s; }
    .sk-sm-row:last-child { border-bottom:0; }
    .sk-sm-row:hover { background:#fafafa; }
    .sk-sm-row--off .sk-sm-row-label { color:#aaa; }
    .sk-sm-row--off .sk-sm-row-desc  { opacity:.4; }
    .sk-sm-row--admin-only { background:#fffbf0; }
    .sk-sm-row--admin-only:hover { background:#fff8e6; }
    .sk-sm-admin-badge { display:inline-block; margin-top:4px; font-size:10.5px; font-weight:600; letter-spacing:.04em; color:#b45309; background:#fef3c7; border:1px solid #fde68a; border-radius:4px; padding:1px 6px; }
    .sk-sm-drag  { cursor:grab; color:#bbb; font-size:18px; flex-shrink:0; line-height:1; user-select:none; }
    .sk-sm-drag:active { cursor:grabbing; }
    .sk-sm-row-body { flex:1; min-width:0; }
    .sk-sm-row-label { display:block; font-size:13.5px; color:#1e1e1e; }
    .sk-sm-row-desc  { display:block; font-size:12px; color:#888; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .sk-sm-row-actions { display:flex; align-items:center; gap:8px; flex-shrink:0; }
    .sk-sm-toggle { cursor:pointer; }
    .sk-sm-toggle-track { display:inline-flex; align-items:center; width:40px; height:22px; background:#ddd; border-radius:11px; position:relative; transition:background .2s; }
    input:checked ~ .sk-sm-toggle-track { background:#2271b1; }
    .sk-sm-toggle--admin input:checked ~ .sk-sm-toggle-track.sk-sm-toggle-track--admin { background:#d97706; }
    .sk-sm-toggle-thumb { position:absolute; left:3px; width:16px; height:16px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.25); transition:left .2s; }
    input:checked ~ .sk-sm-toggle-track .sk-sm-toggle-thumb { left:21px; }
    .sk-sm-field-label  { display:block; font-size:12px; font-weight:600; color:#444; margin-bottom:6px; }
    .sk-sm-wrap input.sk-sm-input { width:100%; box-sizing:border-box; }
    .sk-sm-textarea     { width:100%; box-sizing:border-box; font-family:monospace; font-size:12px; resize:vertical; border:1px solid #ddd; border-radius:4px; padding:8px; }
    .sk-sm-field-actions{ margin-top:14px; display:flex; flex-direction:column; gap:8px; }
    .sk-sm-btn-full     { width:100%; justify-content:center; text-align:center; }
    .sk-sm-field-hint   { font-size:11px; color:#999; margin-top:10px; line-height:1.5; }
    .sk-sm-submit-row   { margin-top:24px; }
    .sk-sm-sortable-placeholder { background:#f0f6ff; border:2px dashed #2271b1; border-radius:6px; height:52px; margin:2px 0; }
    @media (max-width:900px) { .sk-sm-cols { grid-template-columns:1fr; } }
    </style>

    <script>
    (function(){
        var customSections = <?php echo wp_json_encode( $custom ); ?>;
        function saveCustomField() {
            document.getElementById('sk-custom-sections-field').value = JSON.stringify(customSections);
        }
        function saveOrderField() {
            var items = document.querySelectorAll('#sk-sortable [data-key]');
            var order = Array.from(items).map(function(el){ return el.dataset.key; });
            document.getElementById('sk-section-order-field').value = order.join(',');
        }
        var addBtn    = document.getElementById('sk-add-custom');
        var cancelBtn = document.getElementById('sk-cancel-edit');
        var labelInp  = document.getElementById('sk-new-label');
        var contentTA = document.getElementById('sk-new-content');
        var editIdInp = document.getElementById('sk-edit-id');
        var formTitle = document.getElementById('sk-custom-form-title');

        function resetForm() {
            editIdInp.value  = '';
            labelInp.value   = '';
            contentTA.value  = '';
            addBtn.textContent = '+ Add Section';
            formTitle.textContent = '<?php esc_html_e( 'Add Custom Section', 'sacred-kompass' ); ?>';
            cancelBtn.style.display = 'none';
        }
        cancelBtn.addEventListener('click', resetForm);

        addBtn.addEventListener('click', function(){
            var label   = labelInp.value.trim();
            var content = contentTA.value.trim();
            if (!label) { labelInp.focus(); return; }

            var editId = editIdInp.value;
            if (editId) {
                customSections = customSections.map(function(c){
                    if (c.id === editId) { c.label = label; c.content = content; }
                    return c;
                });
                var row = document.querySelector('[data-key="custom_' + editId + '"] .sk-sm-row-label');
                if (row) row.textContent = label;
            } else {
                var id = 'cs_' + Date.now();
                customSections.push({ id: id, label: label, content: content, enabled: true });
                var ul = document.getElementById('sk-sortable');
                var li = document.createElement('li');
                li.className = 'sk-sm-row';
                li.dataset.key = 'custom_' + id;
                li.innerHTML =
                    '<span class="sk-sm-drag" title="Drag to reorder">⠿</span>' +
                    '<div class="sk-sm-row-body">' +
                        '<strong class="sk-sm-row-label">' + escHtml(label) + '</strong>' +
                        '<span class="sk-sm-row-desc">Custom HTML section</span>' +
                    '</div>' +
                    '<div class="sk-sm-row-actions">' +
                        '<button type="button" class="sk-sm-btn-edit button button-small" data-id="' + id + '">Edit</button>' +
                        '<button type="button" class="sk-sm-btn-delete button button-small button-link-delete" data-id="' + id + '">Remove</button>' +
                        '<label class="sk-sm-toggle">' +
                            '<input type="checkbox" class="sk-sm-custom-toggle" data-id="' + id + '" checked hidden>' +
                            '<span class="sk-sm-toggle-track"><span class="sk-sm-toggle-thumb"></span></span>' +
                        '</label>' +
                    '</div>';
                ul.appendChild(li);
                bindRowEvents(li);
            }
            saveCustomField();
            saveOrderField();
            resetForm();
        });

        function bindRowEvents(row) {
            var editBtn   = row.querySelector('.sk-sm-btn-edit');
            var delBtn    = row.querySelector('.sk-sm-btn-delete');
            var togInp    = row.querySelector('.sk-sm-custom-toggle');

            if (editBtn) editBtn.addEventListener('click', function(){
                var id   = this.dataset.id;
                var sec  = customSections.find(function(c){ return c.id === id; });
                if (!sec) return;
                editIdInp.value   = id;
                labelInp.value    = sec.label;
                contentTA.value   = sec.content;
                addBtn.textContent = 'Update Section';
                formTitle.textContent = 'Edit Custom Section';
                cancelBtn.style.display = '';
                labelInp.focus();
            });

            if (delBtn) delBtn.addEventListener('click', function(){
                var id = this.dataset.id;
                if (!confirm('Remove this custom section?')) return;
                customSections = customSections.filter(function(c){ return c.id !== id; });
                row.remove();
                saveCustomField();
                saveOrderField();
            });

            if (togInp) togInp.addEventListener('change', function(){
                var id = this.dataset.id;
                var on = this.checked;
                customSections = customSections.map(function(c){
                    if (c.id === id) c.enabled = on;
                    return c;
                });
                row.classList.toggle('sk-sm-row--off', !on);
                saveCustomField();
            });
        }

        document.querySelectorAll('#sk-sortable li[data-key^="custom_"]').forEach(bindRowEvents);

        document.querySelectorAll('#sk-sortable input[type="checkbox"]:not(.sk-sm-custom-toggle)').forEach(function(inp){
            inp.addEventListener('change', function(){
                var row = this.closest('.sk-sm-row');
                if (this.name && this.name.indexOf('sk_show_') === 0) {
                    row.classList.toggle('sk-sm-row--off', !this.checked);
                }
                if (this.name && this.name.indexOf('sk_admin_only_') === 0) {
                    row.classList.toggle('sk-sm-row--admin-only', this.checked);
                    var badge = row.querySelector('.sk-sm-admin-badge');
                    if (this.checked && !badge) {
                        var body = row.querySelector('.sk-sm-row-body');
                        var b = document.createElement('span');
                        b.className = 'sk-sm-admin-badge';
                        b.title = 'Visible to logged-in editors only';
                        b.textContent = '🔒 Admin only';
                        body.appendChild(b);
                    } else if (!this.checked && badge) {
                        badge.remove();
                    }
                }
            });
        });

        var sortable = document.getElementById('sk-sortable');
        var dragging = null;

        sortable.addEventListener('dragstart', function(e){
            dragging = e.target.closest('li');
            if (dragging) { dragging.style.opacity = '0.4'; e.dataTransfer.effectAllowed = 'move'; }
        });
        sortable.addEventListener('dragend', function(){
            if (dragging) { dragging.style.opacity = ''; dragging = null; }
            document.querySelectorAll('.sk-sm-sortable-placeholder').forEach(function(el){ el.remove(); });
            saveOrderField();
        });
        sortable.addEventListener('dragover', function(e){
            e.preventDefault();
            var target = e.target.closest('li');
            if (!target || target === dragging) return;
            var rect   = target.getBoundingClientRect();
            var after  = e.clientY > rect.top + rect.height / 2;
            if (after) target.after(dragging); else target.before(dragging);
        });

        document.querySelectorAll('.sk-sm-drag').forEach(function(handle){
            handle.closest('li').setAttribute('draggable', 'true');
        });

        function escHtml(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        saveCustomField();
        saveOrderField();
    })();
    </script>
    <?php
}

function sk_get_section_render_order(): array {
    $builtin_keys  = array_keys( sk_builtin_sections() );
    $custom_raw    = get_option( 'sk_custom_sections', '[]' );
    $custom        = json_decode( $custom_raw, true ) ?: [];
    $custom_keys   = array_map( fn($c) => 'custom_' . $c['id'], $custom );

    $all_keys      = array_merge( $builtin_keys, $custom_keys );
    $order_raw     = get_option( 'sk_section_order', '' );
    if ( str_contains( $order_raw, 'offerings' ) ) {
        $order_raw = str_replace( 'offerings', 'art', $order_raw );
        update_option( 'sk_section_order', $order_raw );
    }
    if ( ! $order_raw ) return $all_keys;

    $saved = explode( ',', $order_raw );
    $merged = array_values( array_unique( array_merge( $saved, $all_keys ) ) );
    return array_values( array_filter( $merged, fn($k) => in_array( $k, $all_keys, true ) ) );
}
