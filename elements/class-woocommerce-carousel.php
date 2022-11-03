<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Woocommerce_Element_Carousel extends Element {
    public $category     = 'custom';
    public $name         = 'woocommerce-carousel';
    public $icon         = 'ti-layout-slider-alt';
    public $css_selector = '.swiper-slide';
    public $scripts      = [ 'bricksSwiper' ];
    public $draggable    = false;

    public function get_label() {
        return esc_html__( 'Woocommerce Carousel', 'bricks' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'bricks-swiper' );
        wp_enqueue_style( 'bricks-swiper' );
        wp_enqueue_style('woocommerce-carousel', get_stylesheet_directory_uri() . '/css/woocommerce-carousel.css', false);


        if ( isset( $this->settings['imageLightbox'] ) ) {
            wp_enqueue_script( 'bricks-photoswipe' );
            wp_enqueue_style( 'bricks-photoswipe' );
        }
    }





    public function render() {

        $settings = $this->settings;

        // https://swiperjs.com/api
        $options = [
            'slidesPerView'  => isset( $settings['slidesToShow'] ) ? intval( $settings['slidesToShow'] ) : 4,
            'slidesPerGroup' => isset( $settings['slidesToScroll'] ) ? intval( $settings['slidesToScroll'] ) : 1,
            'speed'          => isset( $settings['speed'] ) ? intval( $settings['speed'] ) : 300,
            'autoHeight'     => isset( $settings['adaptiveHeight'] ),
            'effect'         => isset( $settings['effect'] ) ? $settings['effect'] : 'slide',
            'spaceBetween'   => isset( $settings['gutter'] ) ? intval( $settings['gutter'] ) : 0,
            'initialSlide'   => isset( $settings['initialSlide'] ) ? intval( $settings['initialSlide'] ) : 0,
            'loop'           => false,//isset( $settings['infinite'] ),
            'centeredSlides' => isset( $settings['centerMode'] ),
        ];


        $this->set_attribute( 'swiper', 'class', 'bricks-swiper-container' );
        $this->set_attribute( 'swiper', 'data-script-args', wp_json_encode( $options ) );
        //print_r($this->set_attribute( 'swiper', 'data-script-args', wp_json_encode( $options ) ));

        $type = ! empty( $settings['type'] ) ? $settings['type'] : 'media';

        // TYPE: IMAGES


        if ( $type === 'media' ) {

                $query_vars['post_status'] = 'publish';
                $query_vars['post_type']   = 'product';

                $carousel_query = new \WP_Query( $query_vars );


        }

        $carousel_posts = $carousel_query ? $carousel_query->get_posts() : [];


        foreach ($carousel_posts as $carousel_item_key => $carousel_item_value ){

                $woocommerce_array[] =  $carousel_item_value->ID;

        }

        foreach ($woocommerce_array as $woocommerce_item_key => $woocommerce_item_value ){

            $product = wc_get_product( $woocommerce_item_value);
            $woocommerce_filtered_array[] =  [
                'product_id' => $product->get_id(),
                'product_img' => $product->get_image(),
                'product_name' => $product->get_name(),
                'product_permalink' => get_permalink( $product->get_id() ),
                'product_price' => $product->get_price(),

            ];

        }


        echo "<div {$this->render_attributes( '_root' )}>";

            echo "<div {$this->render_attributes( 'swiper' )}>";

            echo '<div class="swiper-wrapper">';

            $item_classes = [ 'repeater-item', 'swiper-slide' ];

            foreach ( $woocommerce_filtered_array as $item_index => $item ) {

                $this->set_attribute( "list-item-$item_index", 'class', $item_classes );

                echo "<div {$this->render_attributes( "list-item-$item_index" )}>";
                echo '<div class="product-container" >';
                echo '<div class="circle">Uus</div>';
                echo $item['product_img'];
                echo '<p class="title-tag" >'.$item['product_name'].'</p>';
                echo '<p class="price-tag" >€'.$item['product_price'].'</p>';
                echo '<button ><a class="add-to-cart-button" href="'.get_site_url() .'/cart/?add-to-cart='.$item["product_id"].'">Lisa Korvi</a></button>';
                echo '</div>';

                echo '</div>';
            }

            echo '</div>';
            echo '</div>';

            echo $this->render_swiper_nav();


        echo '</div>';
    }
}
