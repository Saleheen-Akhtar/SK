<?php
/**
 * Sacred Kompass — SEO Optimization & Open Graph Tags
 */
defined('ABSPATH') || exit;

/* ── Shared helper: resolve the home page title ── */
function sk_home_title(): string {
    $custom = sk_option('seo_home_title', '');
    if ( $custom ) return $custom;
    return get_bloginfo('name') . ' — Where the Sacred Meets the Everyday';
}

/* ── Shared helper: resolve the home page description ── */
function sk_home_desc(): string {
    $custom = sk_option('seo_home_desc', '');
    if ( $custom ) return $custom;
    return 'Sacred Kompass is a transformative wellness and consciousness-based consultancy weaving Vedic astrology, meditation, and emotional resilience into modern life.';
}

/* ── Native WordPress <title> filter (always runs, Rank Math compatible) ── */
add_filter('document_title_parts', function( array $parts ): array {
    if ( is_home() || is_front_page() ) {
        $custom = sk_option('seo_home_title', '');
        if ( $custom ) {
            $parts['title'] = $custom;
            unset($parts['tagline'], $parts['site']); // suppress "Sacred Kompass - Home" pattern
        }
    }
    return $parts;
}, 1 );

/* ── Rank Math title filter — fires when Rank Math IS active ── */
add_filter('rank_math/frontend/title', function( string $title ): string {
    if ( is_home() || is_front_page() ) {
        $custom = sk_option('seo_home_title', '');
        if ( $custom && ( strpos($title, 'Home') !== false || strpos($title, 'Sacred Kompass') === false ) ) {
            return esc_html( $custom );
        }
    }
    if ( is_page_template('page-art.php') ) {
        if ( empty($title) || strpos($title, 'Art') === false ) {
            return 'Art for Peace Gallery — ' . get_bloginfo('name');
        }
    }
    return $title;
}, 20 );

/* ── Rank Math description filter — fires when Rank Math IS active ── */
add_filter('rank_math/frontend/description', function( string $desc ): string {
    if ( is_home() || is_front_page() ) {
        if ( empty( trim($desc) ) ) {
            return esc_attr( sk_home_desc() );
        }
    }
    if ( is_page_template('page-art.php') ) {
        if ( empty( trim($desc) ) ) {
            return 'Explore the Art for Peace Gallery at Sacred Kompass. Discover expressive paintings and artworks created for mindfulness, spiritual wellness, and inner transformation.';
        }
    }
    return $desc;
}, 20 );

/* ── Native fallback meta block when no SEO plugin is active ── */
if ( ! defined('RANK_MATH_VERSION') && ! defined('WPSEO_VERSION') ) :

add_action('wp_head', function(): void {
    global $post;

    $site_name  = get_bloginfo('name');
    $site_url   = home_url('/');
    
    // Resolve logo/default OG image
    $logo_id = get_theme_mod('custom_logo');
    $custom_logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
    $logo_url   = sk_option('logo_url', '') ?: sk_option('seo_og_image', '') ?: $custom_logo_url;

    /* ── Determine Template Properties ── */
    $pub_date    = '';
    $mod_date    = '';
    $robots      = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';

    if ( is_search() ) {
        $query       = get_search_query();
        $title       = sprintf( __( 'Search Results for "%s" — %s', 'sacred-kompass' ), $query, $site_name );
        $description = sprintf( __( 'Search results for "%s" on %s.', 'sacred-kompass' ), $query, $site_name );
        $canonical   = home_url( '/' ) . '?s=' . urlencode( $query );
        $og_type     = 'website';
        $og_image    = $logo_url;
        $robots      = 'noindex, nofollow';
    } elseif ( is_404() ) {
        $title       = __( 'Page Not Found — ', 'sacred-kompass' ) . $site_name;
        $description = __( 'The page you are looking for does not exist on ', 'sacred-kompass' ) . $site_name . '.';
        $canonical   = home_url( '/' );
        $og_type     = 'website';
        $og_image    = $logo_url;
        $robots      = 'noindex, follow';
    } elseif ( is_home() || is_front_page() ) {
        $title       = sk_home_title();
        $description = sk_home_desc();
        $canonical   = $site_url;
        $og_type     = 'website';
        $og_image    = sk_option( 'seo_og_image', $logo_url );
    } elseif ( is_page_template( 'page-art.php' ) ) {
        $title       = __( 'Art for Peace Gallery — ', 'sacred-kompass' ) . $site_name;
        $description = __( 'Explore the Art for Peace Gallery at Sacred Kompass. Discover expressive paintings and artworks created for mindfulness, spiritual wellness, and inner transformation.', 'sacred-kompass' );
        $canonical   = get_permalink();
        $og_type     = 'website';
        $og_image    = sk_option( 'seo_og_image', $logo_url );
    } elseif ( is_post_type_archive( 'sk_story' ) || is_page_template( 'page-stories.php' ) ) {
        $opt_title   = wp_strip_all_tags( sk_option( 'stories_hero_title', 'Stories for the soul' ) );
        $title       = ( $opt_title ?: __( 'Stories for the Soul', 'sacred-kompass' ) ) . ' — ' . $site_name;
        $description = sk_option( 'stories_hero_sub', __( 'Real stories from beautiful souls who chose themselves, followed their inner compass, and created meaningful change.', 'sacred-kompass' ) );
        $canonical   = is_page_template( 'page-stories.php' ) ? get_permalink() : get_post_type_archive_link( 'sk_story' );
        $og_type     = 'website';
        $og_image    = sk_option( 'seo_og_image', $logo_url );
    } elseif ( is_page_template( 'page-collective.php' ) ) {
        $opt_title   = wp_strip_all_tags( sk_option( 'collective_title', 'The Collective' ) );
        $title       = ( $opt_title ?: __( 'The Collective', 'sacred-kompass' ) ) . ' — ' . $site_name;
        $description = sk_option( 'collective_hero_sub', __( 'Guides, teachers, and practitioners united by one vision: to help individuals, leaders, and organisations reconnect with their inner compass.', 'sacred-kompass' ) );
        $canonical   = get_permalink();
        $og_type     = 'website';
        $og_image    = sk_option( 'seo_og_image', $logo_url );
    } elseif ( is_post_type_archive( 'sk_event' ) ) {
        $opt_title   = wp_strip_all_tags( sk_option( 'event_archive_title', 'Gatherings & Workshops' ) );
        $title       = ( $opt_title ?: __( 'Gatherings & Workshops', 'sacred-kompass' ) ) . ' — ' . $site_name;
        $description = sk_option( 'event_archive_desc', __( 'Join us in person or online for immersive experiences rooted in ancient wisdom.', 'sacred-kompass' ) );
        $canonical   = get_post_type_archive_link( 'sk_event' );
        $og_type     = 'website';
        $og_image    = sk_option( 'seo_og_image', $logo_url );
    } elseif ( is_page_template( 'page-disclaimer.php' ) || is_page_template( 'page-privacy-policy.php' ) || is_page_template( 'page-terms.php' ) ) {
        $slug = 'privacy-policy';
        if ( is_page_template( 'page-disclaimer.php' ) ) $slug = 'disclaimer';
        elseif ( is_page_template( 'page-terms.php' ) ) $slug = 'terms';
        
        $legal_data  = function_exists('sk_get_legal_page') ? sk_get_legal_page($slug) : [];
        $title       = ( !empty($legal_data['title']) ? $legal_data['title'] : get_the_title() ) . ' — ' . $site_name;
        
        $raw_content = !empty($legal_data['content']) ? $legal_data['content'] : '';
        $description = $raw_content ? wp_trim_words( wp_strip_all_tags( $raw_content ), 30, '' ) : sprintf( __( 'Legal information and policies for %s.', 'sacred-kompass' ), $site_name );
        
        $canonical   = get_permalink();
        $og_type     = 'website';
        $og_image    = $logo_url;
        $pub_date    = get_the_date( 'c' );
        $mod_date    = get_the_modified_date( 'c' );
    } elseif ( is_singular() ) {
        $canonical   = get_permalink();
        $title       = get_the_title() . ' — ' . $site_name;
        $pub_date    = get_the_date( 'c' );
        $mod_date    = get_the_modified_date( 'c' );
        
        if ( is_singular( 'sk_story' ) ) {
            $og_type     = 'article';
            $description = get_post_meta( get_the_ID(), 'story_pull_quote', true ) ?: ( has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '' ) );
            $cover       = get_post_meta( get_the_ID(), 'story_cover_image_url', true );
            $og_image    = $cover ?: ( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: $logo_url );
        } elseif ( is_singular( 'sk_art' ) ) {
            $og_type     = 'article';
            $description = get_post_meta( get_the_ID(), 'art_desc', true ) ?: ( has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '' ) );
            
            $art_img_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
            if ( ! $art_img_url ) {
                $image_id    = (int) get_post_meta( get_the_ID(), 'art_image_id', true );
                $art_img_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : get_post_meta( get_the_ID(), 'art_image', true );
            }
            $og_image    = $art_img_url ?: $logo_url;
        } elseif ( is_singular( 'sk_event' ) ) {
            $og_type     = 'website';
            $description = get_post_meta( get_the_ID(), 'event_description', true ) ?: ( has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '' ) );
            $og_image    = get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: $logo_url;
        } else {
            $og_type     = is_page() ? 'website' : 'article';
            $description = has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '' );
            $og_image    = get_the_post_thumbnail_url( $post->ID, 'large' ) ?: $logo_url;
        }
    } else {
        $title       = wp_title( '—', false, 'right' ) . $site_name;
        $description = sk_home_desc();
        $canonical   = get_pagenum_link();
        $og_type     = 'website';
        $og_image    = $logo_url;
    }

    $description = esc_attr(wp_strip_all_tags($description));
    $title       = esc_attr($title);
    $canonical   = esc_url($canonical);
    $og_image    = $og_image ? esc_url($og_image) : '';
    ?>
<!-- Sacred Kompass SEO -->
<meta name="description" content="<?php echo $description; ?>">
<link rel="canonical" href="<?php echo $canonical; ?>">

<!-- Open Graph -->
<meta property="og:type"        content="<?php echo esc_attr($og_type); ?>">
<meta property="og:title"       content="<?php echo $title; ?>">
<meta property="og:description" content="<?php echo $description; ?>">
<meta property="og:url"         content="<?php echo $canonical; ?>">
<?php if ($og_image) : ?>
<meta property="og:image"       content="<?php echo $og_image; ?>">
<?php endif; ?>
<meta property="og:site_name"   content="<?php echo esc_attr($site_name); ?>">
<meta property="og:locale"      content="en_US">

<!-- Twitter Card -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="<?php echo $title; ?>">
<meta name="twitter:description" content="<?php echo $description; ?>">
<?php if ($og_image) : ?>
<meta name="twitter:image"       content="<?php echo $og_image; ?>">
<?php endif; ?>

<!-- Robots -->
<meta name="robots" content="<?php echo esc_attr($robots); ?>">
    <?php
    /* ── Article dates (blog posts only) ── */
    if ($pub_date) {
        echo '<meta property="article:published_time" content="' . esc_attr($pub_date) . '">' . "\n";
        echo '<meta property="article:modified_time"  content="' . esc_attr($mod_date) . '">' . "\n";
    }

    /* ── JSON-LD Structured Data ── */
    $json_schema = null;

    if ( is_front_page() || is_home() ) {
        $json_schema = [
            '@context'     => 'https://schema.org',
            '@type'        => 'ProfessionalService',
            'name'         => $site_name,
            'url'          => $site_url,
            'logo'         => $logo_url,
            'description'  => wp_strip_all_tags( sk_home_desc() ),
            'address'      => [ '@type' => 'PostalAddress', 'addressCountry' => 'SG' ],
            'serviceType'  => [ 'Vedic Astrology', 'Meditation', 'Breathwork', 'Energy Healing', 'Emotional Resilience Coaching', 'Sacred Feminine', 'NVC' ],
            'priceRange'   => '$$',
            'contactPoint' => [
                '@type'       => 'ContactPoint',
                'contactType' => 'customer service',
                'url'         => sk_option('contact_url', home_url( '/contact/' )),
            ],
            'sameAs'       => array_filter( [
                sk_option( 'social_instagram', '' ),
                sk_option( 'social_facebook', '' ),
                sk_option( 'social_linkedin', '' ),
                sk_option( 'social_youtube', '' ),
            ] ),
        ];
    } elseif ( is_post_type_archive( 'sk_story' ) || is_page_template( 'page-stories.php' ) || is_post_type_archive( 'sk_event' ) ) {
        $json_schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'CollectionPage',
            'name'        => $title,
            'url'         => $canonical,
            'description' => $description,
            'publisher'   => [
                '@type' => 'Organization',
                'name'  => $site_name,
                'logo'  => array_filter( [ '@type' => 'ImageObject', 'url' => $logo_url ] ),
            ]
        ];
    } elseif ( is_singular( 'post' ) || is_singular( 'sk_story' ) ) {
        $author_name = $site_name;
        if ( is_singular( 'sk_story' ) ) {
            $story_author = get_post_meta( get_the_ID(), 'story_author_name', true );
            if ( $story_author ) {
                $author_name = $story_author;
            } else {
                $disp = get_the_author_meta( 'display_name' );
                $author_name = ( $disp && ! str_contains( strtolower( $disp ), 'saleheen' ) && ! str_contains( strtolower( $disp ), 'admin' ) ) ? $disp : 'Anonymous';
            }
        } else {
            $author_name = get_the_author_meta( 'display_name' ) ?: $site_name;
        }

        $json_schema = [
            '@context'         => 'https://schema.org',
            '@type'            => is_singular( 'post' ) ? 'BlogPosting' : 'Article',
            'headline'         => get_the_title(),
            'description'      => $description,
            'url'              => $canonical,
            'datePublished'    => $pub_date,
            'dateModified'     => $mod_date,
            'image'            => $og_image ?: $logo_url,
            'author'           => [ '@type' => 'Person', 'name' => $author_name ],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => $site_name,
                'logo'  => array_filter( [ '@type' => 'ImageObject', 'url' => $logo_url ] ),
            ],
            'mainEntityOfPage' => [ '@type' => 'WebPage', '@id' => $canonical ],
        ];
    } elseif ( is_singular( 'sk_art' ) ) {
        $art_id      = get_the_ID();
        $medium      = get_post_meta( $art_id, 'art_medium', true ) ?: '';
        $dimensions  = get_post_meta( $art_id, 'art_dimensions', true ) ?: '';
        $artist_name = get_post_meta( $art_id, 'art_artist', true ) ?: '';
        $artist_type = get_post_meta( $art_id, 'art_artist_type', true ) ?: '';

        if ( $artist_name === '' ) {
            $resolved_artist_name = $site_name;
            $resolved_artist_type = 'Organization';
        } else {
            $resolved_artist_name = $artist_name;
            $resolved_artist_type = $artist_type ?: 'Person';
        }

        $json_schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'VisualArtwork',
            'name'        => get_the_title(),
            'description' => $description,
            'url'         => $canonical,
            'image'       => $og_image ?: $logo_url,
            'artMedium'   => $medium,
            'artform'     => 'Painting',
            'artist'      => [
                '@type' => $resolved_artist_type,
                'name'  => $resolved_artist_name,
            ],
        ];

        if ( preg_match( '/^([\d.]+)\s*[xX*]\s*([\d.]+)(?:\s*([a-zA-Z]+))?$/', trim( $dimensions ), $matches ) ) {
            $w_val = $matches[1];
            $h_val = $matches[2];
            $unit  = isset( $matches[3] ) ? trim( $matches[3] ) : 'cm';

            $json_schema['width'] = [
                '@type' => 'QuantitativeValue',
                'value' => (float) $w_val,
                'unitText'  => $unit,
            ];
            $json_schema['height'] = [
                '@type' => 'QuantitativeValue',
                'value' => (float) $h_val,
                'unitText'  => $unit,
            ];
        }
    } elseif ( is_page_template( 'page-collective.php' ) ) {
        $json_schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'AboutPage',
            'name'        => $title,
            'url'         => $canonical,
            'description' => $description,
            'publisher'   => [
                '@type' => 'Organization',
                'name'  => $site_name,
                'logo'  => array_filter( [ '@type' => 'ImageObject', 'url' => $logo_url ] ),
            ],
        ];
    } elseif ( is_page_template( 'page-disclaimer.php' ) || is_page_template( 'page-privacy-policy.php' ) || is_page_template( 'page-terms.php' ) ) {
        $json_schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            'name'        => $title,
            'url'         => $canonical,
            'description' => $description,
            'publisher'   => [
                '@type' => 'Organization',
                'name'  => $site_name,
                'logo'  => array_filter( [ '@type' => 'ImageObject', 'url' => $logo_url ] ),
            ],
        ];
    } elseif ( is_singular( 'sk_event' ) ) {
        $event_id     = get_the_ID();
        $date         = get_post_meta( $event_id, 'event_date', true );
        $time         = get_post_meta( $event_id, 'event_time', true );
        $end_time     = get_post_meta( $event_id, 'event_end_time', true );
        $location     = get_post_meta( $event_id, 'event_location', true ) ?: 'Online';
        $location_url = get_post_meta( $event_id, 'event_location_url', true ) ?: '';
        $format       = get_post_meta( $event_id, 'event_format', true ) ?: 'inperson';
        $price        = get_post_meta( $event_id, 'event_price', true ) ?: '';

        $start_iso = '';
        $end_iso   = '';
        if ( $date ) {
            $start_iso = $time ? date( 'c', strtotime( $date . ' ' . $time ) ) : date( 'c', strtotime( $date ) );
            if ( $end_time ) {
                $end_iso = date( 'c', strtotime( $date . ' ' . $end_time ) );
            }
        }

        if ( $format === 'online' ) {
            $location_schema = [
                '@type' => 'VirtualLocation',
                'url'   => $location_url ?: $canonical,
            ];
        } else {
            $location_schema = [
                '@type'   => 'Place',
                'name'    => $location,
                'address' => [
                    '@type'           => 'PostalAddress',
                    'streetAddress'   => $location,
                    'addressLocality' => 'Singapore',
                    'addressCountry'  => 'SG',
                ],
            ];
        }

        $json_schema = [
            '@context'            => 'https://schema.org',
            '@type'               => 'Event',
            'name'                => get_the_title(),
            'description'         => $description,
            'url'                 => $canonical,
            'image'               => $og_image ?: $logo_url,
            'eventAttendanceMode' => ( $format === 'online' ) ? 'https://schema.org/OnlineEventAttendanceMode' : ( ( $format === 'hybrid' ) ? 'https://schema.org/MixedEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode' ),
            'eventStatus'         => 'https://schema.org/EventScheduled',
            'location'            => $location_schema,
        ];

        if ( $start_iso ) {
            $json_schema['startDate'] = $start_iso;
        }
        if ( $end_iso ) {
            $json_schema['endDate'] = $end_iso;
        }

        if ( $price ) {
            $clean_price = preg_replace( '/[^0-9.]/', '', $price );
            if ( $clean_price ) {
                $json_schema['offers'] = [
                    '@type'         => 'Offer',
                    'price'         => $clean_price,
                    'priceCurrency' => 'SGD',
                    'url'           => $canonical,
                    'availability'  => get_post_meta( $event_id, 'event_sold_out', true ) ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
                ];
            }
        }
    } elseif ( is_singular() ) {
        $json_schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            'name'        => $title,
            'url'         => $canonical,
            'description' => $description,
            'publisher'   => [
                '@type' => 'Organization',
                'name'  => $site_name,
                'logo'  => array_filter( [ '@type' => 'ImageObject', 'url' => $logo_url ] ),
            ],
        ];
    }

    if ( $json_schema ) {
        $json_schema = sk_clean_schema_array( $json_schema );
        echo '<script type="application/ld+json">' . wp_json_encode( $json_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }

}, 1);

endif; // end no-SEO-plugin check

/* ── Rank Math home page meta seeder ── */
add_action('admin_init', function(): void {
    if ( ! defined('RANK_MATH_VERSION') ) return;
    $home_id = (int) get_option('page_on_front');
    if ( ! $home_id ) return;

    $existing_desc  = get_post_meta($home_id, 'rank_math_description', true);
    $existing_title = get_post_meta($home_id, 'rank_math_title', true);

    if ( empty($existing_desc) ) {
        $desc = sk_option('seo_home_desc', '');
        if ( ! $desc ) $desc = 'Sacred Kompass is a transformative wellness and consciousness-based consultancy weaving Vedic astrology, meditation, and emotional resilience into modern life.';
        update_post_meta($home_id, 'rank_math_description', sanitize_text_field($desc));
    }
    if ( empty($existing_title) ) {
        $title = sk_option('seo_home_title', '');
        if ( ! $title ) $title = get_bloginfo('name') . ' — Where the Sacred Meets the Everyday';
        update_post_meta($home_id, 'rank_math_title', sanitize_text_field($title));
    }
});

/**
 * Recursively remove empty strings and nulls from schema arrays.
 */
function sk_clean_schema_array(array $arr): array {
    $result = [];
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            $cleaned = sk_clean_schema_array($value);
            if (!empty($cleaned)) {
                $result[$key] = $cleaned;
            }
        } elseif ($value !== '' && $value !== null) {
            $result[$key] = $value;
        }
    }
    return $result;
}
