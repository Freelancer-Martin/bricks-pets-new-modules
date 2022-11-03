<?php

namespace Bricks;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Post_Element_Carousel extends Element
{
    public $category = 'custom';
    public $name = 'post-carousel';
    public $icon = 'ti-layout-slider-alt';
    public $css_selector = '.swiper-slide';
    public $scripts = ['bricksSwiper'];
    public $draggable = false;

    public function get_label()
    {
        return esc_html__('Post Carousel', 'bricks');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('bricks-swiper');
        wp_enqueue_style('bricks-swiper');
        wp_enqueue_style('post-carousel', get_stylesheet_directory_uri() . '/css/post-carousel.css', false);


        if (isset($this->settings['imageLightbox'])) {
            wp_enqueue_script('bricks-photoswipe');
            wp_enqueue_style('bricks-photoswipe');
        }
    }


    public function render()
    {

        $settings = $this->settings;

        // https://swiperjs.com/api
        $options = [
            'slidesPerView' => isset($settings['slidesToShow']) ? intval($settings['slidesToShow']) : 3,
            'slidesPerGroup' => isset($settings['slidesToScroll']) ? intval($settings['slidesToScroll']) : 1,
            'speed' => isset($settings['speed']) ? intval($settings['speed']) : 300,
            'autoHeight' => isset($settings['adaptiveHeight']),
            'effect' => isset($settings['effect']) ? $settings['effect'] : 'slide',
            'spaceBetween' => isset($settings['gutter']) ? intval($settings['gutter']) : 0,
            'initialSlide' => isset($settings['initialSlide']) ? intval($settings['initialSlide']) : 0,
            'loop' => false,//isset( $settings['infinite'] ),
            'centeredSlides' => isset($settings['centerMode']),
        ];


        //$this->set_attribute('swiper', 'class', 'bricks-swiper-container');
        //$this->set_attribute('swiper', 'data-script-args', wp_json_encode($options));
        //print_r($this->set_attribute( 'swiper', 'data-script-args', wp_json_encode( $options ) ));

        $type = !empty($settings['type']) ? $settings['type'] : 'media';

        // TYPE: IMAGES


        if ($type) {

            $query_vars['post_status'] = 'publish';
            $query_vars['post_type'] = 'post';
            $query_vars['posts_per_page'] = 4;

            $carousel_query = new \WP_Query($query_vars);


        }

        $carousel_posts = $carousel_query->get_posts() ? $carousel_query->get_posts() : [];


        foreach ( $carousel_posts as $woocommerce_item_key => $woocommerce_item_value) {


            $woocommerce_filtered_array[] = [
                'post_title' => $woocommerce_item_value->post_title,
                'post_img' => get_the_post_thumbnail_url($woocommerce_item_value->ID),
                'post_date' => $woocommerce_item_value->post_date,
                'post_link' => $woocommerce_item_value->guid,

            ];

        }

        //print_r($carousel_posts);
        $woocommerce_array[] = [];


        //echo "<div {$this->render_attributes( '_root' )}>";

        //echo "<div {$this->render_attributes( 'swiper' )}>";

        echo '<div class="post-swiper-wrapper">';

        $item_classes = ['post-repeater-item', 'swiper-slide'];


        foreach ($woocommerce_filtered_array as $item_index => $item) {

            $this->set_attribute("list-item-$item_index", 'class', $item_classes);

            $item['post_date'] = date(' j. F');
            if( ! empty($item['post_img']) && ! empty($item['post_title']) ) {
                echo "<div {$this->render_attributes( "list-item-$item_index" )}>";
                echo '<a href="' . $item['post_link'] . '"><div class="post-container" >';
                echo '<div class="post-circle">' . $item['post_date'] . '</div>';
                echo '<img class="post-img" src="' . $item['post_img'] . '" />';
                echo '<p class="post-title" >' . $item['post_title'] . '</p>';
                //echo '<p class="price-tag" >€' . $item['product_price'] . '</p>';
                //echo '<button ><a class="add-to-cart-button" href="' . get_site_url() . '/cart/?add-to-cart=' . $item["product_id"] . '">Lisa Korvi</a></button>';
                echo '</a></div>';

                echo '</div>';
            }
        }

        echo '</div>';
        //echo '</div>';

        echo $this->render_swiper_nav();


       // echo '</div>';
    }
}
