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
        'label'       => __( 'Project services', 'cypress-theme' ),
        'id'          => 'project_services',
        'type'        => 'checkbox',
        'choices'     => array(
          array(
            'value'   => 'creativity',
            'label'   => __('Creativity', 'cypress-theme')
            ),
          array(
            'value'   => 'branding',
            'label'   => __('Brand Identity', 'cypress-theme')
            ),
          array(
            'value'   => 'copy',
            'label'   => __('Copy-writing', 'cypress-theme')
            ),
          array(
            'value'   => 'advertising',
            'label'   => __('Advertising', 'cypress-theme')
            ),
          array(
            'value'   => 'marketing',
            'label'   => __('Marketing', 'cypress-theme')
            ),
          array(
            'value'   => 'ux',
            'label'   => __('UX & Web Design', 'cypress-theme')
            ),
          array(
            'value'   => 'development',
            'label'   => __('Web Development', 'cypress-theme')
            ),
          )
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
        'class'       => 'ot-upload-attachment-id',
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
        'label'       => '<small>' . __( 'Side image', 'cypress-theme' ) . '</small>',
        'id'          => 'project_brand_image',
        'type'        => 'upload',
        'class'       => 'ot-upload-attachment-id',
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
        'class'       => 'ot-upload-attachment-id',
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
        'class'       => 'ot-upload-attachment-id',
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

$posts_metabox = array(
    'id'          => 'post_metas',
    'title'       => __( 'Article details', 'cypress-theme' ),
    'desc'        => '',
    'pages'       => array( 'post' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Press release', 'cypress-theme' ),
        'id'          => 'press_tab',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Press release module', 'cypress-theme' ),
        'id'          => 'press_check',
        'type'        => 'on-off',
        'std'         => 'off'
      ),
      array(
        'label'       => '<small>' . __( 'Source Author', 'cypress-theme' ) . '</small>',
        'id'          => 'press_author',
        'type'        => 'text',
        'condition'   => 'press_check:is(on)'
      ),
      array(
        'label'       => '<small>' . __( 'Source URL', 'cypress-theme' ) . '</small>',
        'id'          => 'press_url',
        'type'        => 'text',
        'condition'   => 'press_check:is(on)'
      )
    )
  );

$services_metabox = array(
    'id'          => 'services_metas',
    'title'       => __( 'Service details', 'cypress-theme' ),
    'desc'        => '',
    'pages'       => array( 'services' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Long title', 'cypress-theme' ),
        'id'          => 'service_title',
        'type'        => 'text',
      ),
      array(
        'label'       => __( 'Short description', 'cypress-theme' ),
        'id'          => 'service_description',
        'type'        => 'textarea-simple',
        'row'         => '3'
      ),
      array(
        'label'       => __( 'Icon class', 'cypress-theme' ),
        'id'          => 'service_icon',
        'type'        => 'text',
      ),
    )
  );

$home_metabox = array(
    'id'          => 'home_metas',
    'title'       => __( 'Front page extras', 'cypress-theme' ),
    'desc'        => '',
    'pages'       => array( 'page' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Openings background', 'cypress-theme' ),
        'id'          => 'home_openings',
        'type'        => 'upload',
        'class'       => 'ot-upload-attachment-id',
      ),
    )
  );

$about_metabox = array(
  'id'          => 'about_metas',
  'title'       => __( 'About Page', 'cypress-theme' ),
  'desc'        => '',
  'pages'       => array( 'page' ),
  'context'     => 'normal',
  'priority'    => 'high',
  'fields'      => array(
    array(
      'id'          => 'about_sections',
      'label'       => __( 'Sections', 'cypress-theme' ),
      'type'        => 'list-item',
      'operator'    => 'and',
      'settings'    => array(
        array(
          'label'       => __( 'Contenuto', 'cypress-theme' ),
          'id'          => 'section_body',
          'type'        => 'textarea',
          'row'         => '3',
          'operator'    => 'and'
        ),
        array(
          'label'       => __( 'Immagine', 'cypress-theme' ),
          'id'          => 'section_image',
          'type'        => 'upload',
          'class'       => 'ot-upload-attachment-id',
          'operator'    => 'and'
        ),
      )
    )
  )
);

$contacts_metabox = array(
  'id'          => 'contacts_metas',
  'title'       => __( 'Contacts Page', 'cypress-theme' ),
  'desc'        => '',
  'pages'       => array( 'page' ),
  'context'     => 'normal',
  'priority'    => 'high',
  'fields'      => array(
    array(
      'id'          => 'contacts_joinus_tab',
      'label'       => __( 'Lavora con noi', 'cypress-theme' ),
      'type'        => 'tab'
    ),
    array(
      'id'          => 'joinus_title',
      'label'       => __( 'Call to action', 'cypress-theme' ),
      'type'        => 'text'
    ),
    array(
      'id'          => 'joinus_email',
      'label'       => __( 'Email', 'cypress-theme' ),
      'type'        => 'text'
    ),
    array(
      'id'          => 'joinus_text',
      'label'       => __( 'Descrizione', 'cypress-theme' ),
      'type'        => 'textarea',
      'row'         => '3'
    )
  )
);

  $id = ( isset($_GET['post']) ) ? $_GET['post'] : ( isset($_POST['post_ID'] ) ? $_POST['post_ID'] : false);
  $home = get_page_template_slug($id) == 'layout/home.php';
  $about = get_page_template_slug($id) == 'layout/about.php';
  $contacts = get_page_template_slug($id) == 'layout/contacts.php';
  if ( function_exists( 'ot_register_meta_box' ) ) :
    ot_register_meta_box( $projects_metabox );
    ot_register_meta_box( $pages_metabox );
    ot_register_meta_box( $posts_metabox );
    ot_register_meta_box( $services_metabox );
    if( $id && $home ) :
      ot_register_meta_box( $home_metabox );
    endif;
    if( $id && $about ) :
      ot_register_meta_box( $about_metabox );
    endif;
    if( $id && $contacts ) :
      ot_register_meta_box( $contacts_metabox );
    endif;
  endif;
}
