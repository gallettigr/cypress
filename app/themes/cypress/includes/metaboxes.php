<?php

add_action( 'admin_init', 'cypress_metaboxes' );
function cypress_metaboxes() {
  $projects_metabox = array(
    'id'          => 'projects_metas',
    'title'       => __( 'Project details', 'cypress-theme' ),
    'desc'        => '',
    'pages'       => array( 'projects' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Preview', 'cypress-theme' ),
        'id'          => 'project_preview_tab',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Project title', 'cypress-theme' ),
        'id'          => 'project_title',
        'type'        => 'text',
      ),
      array(
        'label'       => __( 'Project tagline', 'cypress-theme' ),
        'id'          => 'project_tagline',
        'type'        => 'text',
      ),
      array(
        'label'       => __( 'Client', 'cypress-theme' ),
        'id'          => 'project_client',
        'type'        => 'custom-post-type-select',
        'post_type'   => 'clients',
      ),
      array(
        'label'       => __( 'Section color', 'cypress-theme' ),
        'id'          => 'project_preview_color',
        'type'        => 'colorpicker',
      ),
      array(
        'label'       => __( 'Section background', 'cypress-theme' ),
        'id'          => 'project_preview_bg',
        'type'        => 'upload',
      ),
      array(
        'label'       => __( 'Section text tone', 'cypress-theme' ),
        'id'          => 'project_preview_tone',
        'type'        => 'radio',
        'choices'     => array(
          array(
            'value'       => 'light',
            'label'       => __( 'Light', 'cypress-theme' ),
          ),
          array(
            'value'       => 'gray',
            'label'       => __( 'Mid', 'cypress-theme' ),
          ),
          array(
            'value'       => 'dark',
            'label'       => __( 'Dark', 'cypress-theme' ),
          )
        )
      ),
      array(
        'label'       => __( 'Web', 'cypress-theme' ),
        'id'          => 'project_web_tab',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Development module', 'cypress-theme' ),
        'id'          => 'project_web_development_check',
        'type'        => 'on-off',
        'std'         => 'off'
      ),
      array(
        'label'       => '<small>' . __( 'Section title', 'cypress-theme' ) . '</small>',
        'id'          => 'project_web_title',
        'type'        => 'text',
        'condition'   => 'project_web_development_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Gallery', 'cypress-theme' ) . '</small>',
        'id'          => 'project_web_gallery',
        'type'        => 'gallery',
        'condition'   => 'project_web_development_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section description', 'cypress-theme' ) . '</small>',
        'id'          => 'project_web_description',
        'type'        => 'textarea-simple',
        'row'         => '3',
        'condition'   => 'project_web_development_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Project URL', 'cypress-theme' ) . '</small>',
        'id'          => 'project_web_url',
        'type'        => 'text',
        'condition'   => 'project_web_development_check:is(on)'
      ),
      array(
        'label'       => __( 'Brand', 'cypress-theme' ),
        'id'          => 'project_brand_tab',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Branding module', 'cypress-theme' ),
        'id'          => 'project_brand_check',
        'type'        => 'on-off',
        'std'         => 'off'
      ),
      array(
        'label'       => '<small>' . __( 'Section title', 'cypress-theme' ) . '</small>',
        'id'          => 'project_brand_title',
        'type'        => 'text',
        'condition'   => 'project_brand_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section description', 'cypress-theme' ) . '</small>',
        'id'          => 'project_brand_description',
        'type'        => 'textarea-simple',
        'row'         => '3',
        'condition'   => 'project_brand_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'SVG Logo', 'cypress-theme' ) . '</small>',
        'id'          => 'project_brand_logo',
        'type'        => 'upload',
        'condition'   => 'project_brand_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Side image', 'cypress-theme' ) . '</small>',
        'id'          => 'project_brand_image',
        'type'        => 'upload',
        'condition'   => 'project_brand_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section color', 'cypress-theme' ) . '</small>',
        'id'          => 'project_brand_color',
        'type'        => 'colorpicker',
        'condition'   => 'project_brand_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section text tone', 'cypress-theme' ) . '</small>',
        'id'          => 'project_brand_tone',
        'type'        => 'radio',
        'choices'     => array(
          array(
            'value'       => 'light',
            'label'       => __( 'Light', 'cypress-theme' ),
          ),
          array(
            'value'       => 'gray',
            'label'       => __( 'Mid', 'cypress-theme' ),
          ),
          array(
            'value'       => 'dark',
            'label'       => __( 'Dark', 'cypress-theme' ),
          )
        ),
        'condition'   => 'project_brand_check:is(on)'
      ),
      array(
        'label'       => __( 'Print', 'cypress-theme' ),
        'id'          => 'project_print_tab',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Print module', 'cypress-theme' ),
        'id'          => 'project_print_check',
        'type'        => 'on-off',
        'std'         => 'off'
      ),
      array(
        'label'       => '<small>' . __( 'Section title', 'cypress-theme' ) . '</small>',
        'id'          => 'project_print_title',
        'type'        => 'text',
        'condition'   => 'project_print_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section description', 'cypress-theme' ) . '</small>',
        'id'          => 'project_print_description',
        'type'        => 'textarea-simple',
        'row'         => '3',
        'condition'   => 'project_print_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section background', 'cypress-theme' ) . '</small>',
        'id'          => 'project_print_background',
        'type'        => 'upload',
        'condition'   => 'project_print_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Gallery', 'cypress-theme' ) . '</small>',
        'id'          => 'project_print_gallery',
        'type'        => 'gallery',
        'condition'   => 'project_print_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section color', 'cypress-theme' ) . '</small>',
        'id'          => 'project_print_color',
        'type'        => 'colorpicker',
        'condition'   => 'project_print_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section text tone', 'cypress-theme' ) . '</small>',
        'id'          => 'project_print_tone',
        'type'        => 'radio',
        'choices'     => array(
          array(
            'value'       => 'light',
            'label'       => __( 'Light', 'cypress-theme' ),
          ),
          array(
            'value'       => 'gray',
            'label'       => __( 'Mid', 'cypress-theme' ),
          ),
          array(
            'value'       => 'dark',
            'label'       => __( 'Dark', 'cypress-theme' ),
          )
        ),
        'condition'   => 'project_print_check:is(on)'
      ),
      array(
        'label'       => __( 'Icons', 'cypress-theme' ),
        'id'          => 'project_icons_tab',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Iconography module', 'cypress-theme' ),
        'id'          => 'project_icons_check',
        'type'        => 'on-off',
        'std'         => 'off'
      ),
      array(
        'label'       => '<small>' . __( 'Section title', 'cypress-theme' ) . '</small>',
        'id'          => 'project_icons_title',
        'type'        => 'text',
        'condition'   => 'project_icons_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section description', 'cypress-theme' ) . '</small>',
        'id'          => 'project_icons_description',
        'type'        => 'textarea-simple',
        'row'         => '3',
        'condition'   => 'project_icons_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Gallery', 'cypress-theme' ) . '</small>',
        'id'          => 'project_icons_gallery',
        'type'        => 'gallery',
        'condition'   => 'project_icons_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section color', 'cypress-theme' ) . '</small>',
        'id'          => 'project_icons_color',
        'type'        => 'colorpicker',
        'condition'   => 'project_icons_check:is(on)'
      ),
      array(
        'label'       => __( 'App', 'cypress-theme' ),
        'id'          => 'project_app_tab',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'App module', 'cypress-theme' ),
        'id'          => 'project_app_check',
        'type'        => 'on-off',
        'std'         => 'off'
      ),
      array(
        'label'       => '<small>' . __( 'Section title', 'cypress-theme' ) . '</small>',
        'id'          => 'project_app_title',
        'type'        => 'text',
        'condition'   => 'project_app_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section background', 'cypress-theme' ) . '</small>',
        'id'          => 'project_app_background',
        'type'        => 'upload',
        'condition'   => 'project_app_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Gallery', 'cypress-theme' ) . '</small>',
        'id'          => 'project_app_gallery',
        'type'        => 'gallery',
        'condition'   => 'project_app_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section color', 'cypress-theme' ) . '</small>',
        'id'          => 'project_app_color',
        'type'        => 'colorpicker',
        'condition'   => 'project_app_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Section text tone', 'cypress-theme' ) . '</small>',
        'id'          => 'project_app_tone',
        'type'        => 'radio',
        'choices'     => array(
          array(
            'value'       => 'light',
            'label'       => __( 'Light', 'cypress-theme' ),
          ),
          array(
            'value'       => 'gray',
            'label'       => __( 'Mid', 'cypress-theme' ),
          ),
          array(
            'value'       => 'dark',
            'label'       => __( 'Dark', 'cypress-theme' ),
          )
        ),
        'condition'   => 'project_app_check:is(on)'
      ),
    )
  );
$pages_metabox = array(
    'id'          => 'pages_metas',
    'title'       => __( 'Page customization', 'cypress-theme' ),
    'desc'        => '',
    'pages'       => array( 'page' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Intro tagline', 'cypress-theme' ),
        'id'          => 'page_title',
        'type'        => 'text',
      )
    )
  );

  if ( function_exists( 'ot_register_meta_box' ) )
    ot_register_meta_box( $projects_metabox );
    ot_register_meta_box( $pages_metabox );
}
