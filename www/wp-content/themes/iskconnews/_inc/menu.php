<?php

//Navigation menus
register_nav_menus([
                     'header_menu' => 'Primary Header Navigation Menu',
                     'footer_menu' => 'Footer Industry Navigation Menu',
                   ]);


class DDM_Walker_Nav_Menu extends Walker_Nav_Menu
{
  function start_lvl(&$output, $depth = 0, $args = array(), $i = 0) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul class='submenu'>\n";
  }

  function end_lvl(&$output, $depth = 0, $args = array()) {
    $indent = str_repeat("\t", $depth);
    $output .= "$indent</ul>\n";
  }

  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    global $wp_query;
    $indent = ($depth) ? str_repeat("\t", $depth) : '';
    $class_names = $value = '';
    $classes = empty($item->classes) ? array() : (array)$item->classes;

    /* Add active class */
    if (in_array('current-menu-item', $classes)) {
      $classes[] = 'actv';
      unset($classes['current-menu-item']);
    }

    /* Check for children */
    $children = get_posts(array('post_type' => 'nav_menu_item', 'nopaging' => true, 'numberposts' => 1, 'meta_key' => '_menu_item_menu_item_parent', 'meta_value' => $item->ID));

    if (!empty($children)) {
      $classes[] = 'has-menu';
    }


    $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
    $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

    $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
    $id = $id ? ' id="' . esc_attr($id) . '"' : '';

    $output .= $indent . '<li' . $id . $value . $class_names . '>';

    /*if(empty($children)){
    $url = ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
    }else{
    $url = ' href="javascript:void(0);" ';
    }*/

    $url = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : 'javascript:void(0);';

    $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
    $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
    $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
    $attributes .= $url;

    $icon = get_field('icon', $item->ID);
    $sub_title = get_field('sub_title', $item->ID);
    $icon_tag = '';
    if (isset($icon['url']) && !empty($icon['url'])) {
      $icon_tag = '<img src="' . $icon['url'] . '" alt="' . $item->title . '" title="' . $item->title . '">';
    }
    $item_output = $args->before;
    $item_output .= '<a' . $attributes . '>';
    if (isset($icon['url']) && !empty($icon['url'])) {
      $item_output .= '<figure>' . $icon_tag . '</figure>';
    }
    $item_output .= '' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '';
    if (!empty($sub_title)) {
      $item_output .= '<span class="mnu_title__sub">' . $sub_title . $args->link_after . '</span>';
    }
    $item_output .= '';
    $item_output .= '</a>';
    $item_output .= $args->after;
    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
  }

  function end_el(&$output, $item, $depth = 0, $args = array()) {
    $output .= "</li>\n";
  }
}
