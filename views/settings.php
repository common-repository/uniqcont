<div class="wrap ucwp-settings-page">
    <h2><?php esc_html_e('UniqCont', 'uniqcont'); ?></h2>
    <p><?php esc_html_e(
      'Check if the content you publish is unique. Find the copies on other websites. Stay original',
      'uniqcont'
    ); ?></p>
    <p><?php esc_html_e(
      'The plugin uses',
      'uniqcont'
    ); ?> <a href="https://uniqcont.com" target="_blank">uniqcont.com</a> service</p>

    <?php self::render('form'); ?>

    <h2><?php esc_html_e('To get started', 'uniqcont'); ?></h2>
    <?php self::render('steps'); ?>
</div>
