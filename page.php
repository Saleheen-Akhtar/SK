<?php
/**
 * Sacred Kompass — Generic page template (Podium Style)
 */
get_header();
?>
<main class="sk-page-main" style="padding:var(--space-3) 0 var(--space-2);min-height:80vh;background-color:var(--color-surface-base);">
  <div class="wrap-narrow">
    <div class="sk-page-content body-ui">
      <?php
      while (have_posts()) {
          the_post();
          echo '<h1 class="display-h2" style="margin-bottom:var(--space-2);">' . get_the_title() . '</h1>';
          the_content();
      }
      ?>
    </div>
  </div>
</main>
<?php
get_footer();
