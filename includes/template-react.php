<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> - React App</title>
    <?php wp_head(); ?>
</head>
<body>
    <?php get_header(); ?>
    <div id="root"></div>
    <?php get_footer(); ?>
    <?php wp_footer(); ?>
</body>
</html>