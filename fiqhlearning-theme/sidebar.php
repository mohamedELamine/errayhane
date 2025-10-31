<?php
/**
 * الشريط الجانبي
 *
 * @package FiqhLearning
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_active_sidebar('sidebar-main')) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar">
    <?php dynamic_sidebar('sidebar-main'); ?>
</aside>
