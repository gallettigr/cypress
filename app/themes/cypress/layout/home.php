<?php
/**
 * Cypress homepage layout.
 * Template Name: Home Template
 * Theme Name: Cypress
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>
<?php get_template_part('layout/home', 'services'); ?>
<?php //get_template_part('layout/home', 'latest'); ?>
<?php get_template_part('layout/home', 'quotes'); ?>
<?php get_template_part('layout/home', 'careers'); ?>
<?php get_template_part('layout/home', 'clients'); ?>
<?php get_template_part('layout/home', 'press'); ?>
<?php get_footer(); ?>
