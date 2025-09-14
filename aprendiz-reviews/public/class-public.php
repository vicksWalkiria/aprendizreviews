<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Public {
    
    private $plugin_name;
    private $version;
    
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    public function enqueue_styles() {
        // Solo cargar en páginas que contienen shortcodes
        global $post;
        if (!is_object($post)) return;
        
        $has_carousel = has_shortcode($post->post_content, 'reviews_general') || 
                       $this->has_dynamic_shortcode($post->post_content);
        $has_form = has_shortcode($post->post_content, 'reviews_form');
        
        if ($has_carousel || $has_form) {
            // Swiper CSS
            wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
            
            // Plugin CSS
            wp_enqueue_style(
                $this->plugin_name . '-public',
                APRENDIZ_REVIEWS_PLUGIN_URL . 'assets/css/frontend.css',
                array('swiper-css'),
                $this->version,
                'all'
            );
            
            // Añadir estilos inline
            $this->add_inline_styles($has_carousel, $has_form);
        }
    }
    
    public function enqueue_scripts() {
        global $post;
        if (!is_object($post)) return;
        
        $has_carousel = has_shortcode($post->post_content, 'reviews_general') || 
                       $this->has_dynamic_shortcode($post->post_content);
        $has_form = has_shortcode($post->post_content, 'reviews_form');
        
        if ($has_carousel || $has_form) {
            // Swiper JS
            wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true);
            
            // jQuery para formulario
            if ($has_form) {
                wp_enqueue_script('jquery');
            }
            
            // Scripts inline
            $this->add_inline_scripts($has_carousel, $has_form);
        }
    }
    
    public function register_shortcodes() {
        // Registrar shortcodes dinámicos
        $products = Aprendiz_Reviews_Product::get_all();
        
        foreach ($products as $product) {
            add_shortcode($product->shortcode, array($this, 'display_reviews_carousel'));
        }
        
        // Shortcode del formulario
        add_shortcode('reviews_form', array($this, 'display_reviews_form'));
    }
    
    public function display_reviews_carousel($atts, $content, $tag) {
        // Obtener el producto por shortcode
        $producto = Aprendiz_Reviews_Product::get_by_shortcode($tag);
        
        if (!$producto) {
            return '<p>Producto no encontrado.</p>';
        }
        
        // Obtener reseñas de este producto específico
        $resenas = Aprendiz_Reviews_Review::get_by_product($producto->id, true, 10);
        
        if (empty($resenas)) {
            return '<p>No hay reseñas para este producto.</p>';
        }
        
        ob_start();
        include APRENDIZ_REVIEWS_PLUGIN_PATH . 'templates/shortcodes/carousel.php';
        return ob_get_clean();
    }
    
    public function display_reviews_form($atts) {
        $atts = shortcode_atts(array(
            'titulo' => '📝 Comparte tu experiencia'
        ), $atts);
        
        $products = Aprendiz_Reviews_Product::get_all();
        
        ob_start();
        include APRENDIZ_REVIEWS_PLUGIN_PATH . 'templates/shortcodes/form.php';
        return ob_get_clean();
    }
    
    private function has_dynamic_shortcode($content) {
        $products = Aprendiz_Reviews_Product::get_all();
        foreach ($products as $product) {
            if (has_shortcode($content, $product->shortcode)) {
                return true;
            }
        }
        return false;
    }
    
    private function add_inline_styles($has_carousel, $has_form) {
        $css = '';
        
        if ($has_carousel) {
        wp_add_inline_style('swiper-css', '
            .reviews-swiper {
                width: 100% !important;
                padding: 20px 0 !important;
                overflow: hidden !important;
            }
            
            .reviews-swiper .swiper-wrapper {
                display: flex !important;
                align-items: stretch !important;
            }
            
            .reviews-swiper .swiper-slide {
                background: #ffffff !important;
                border: 1px solid #e1e5e9 !important;
                border-radius: 12px !important;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08) !important;
                padding: 20px !important;
                width: 300px !important;
                max-width: 300px !important;
                height: auto !important;
                display: flex !important;
                flex-direction: column !important;
                color: #333333 !important;
                margin-right: 20px !important;
                flex-shrink: 0 !important;
            }
            
            .reviews-swiper .review-header {
                display: flex !important;
                align-items: center !important;
                gap: 12px !important;
                margin-bottom: 15px !important;
            }
            
            .reviews-swiper .avatar {
                width: 50px !important;
                height: 50px !important;
                border-radius: 50% !important;
                object-fit: cover !important;
                border: 2px solid #e1e5e9 !important;
                flex-shrink: 0 !important;
            }
            
            .reviews-swiper .review-stars {
                color: #ffc107 !important;
                font-size: 18px !important;
                line-height: 1 !important;
            }
            
            .reviews-swiper .review-date {
                font-size: 13px !important;
                color: #8e9aaf !important;
                margin-bottom: 12px !important;
            }
            
            .reviews-swiper .review-text {
                font-style: italic !important;
                margin: 0 0 15px 0 !important;
                color: #555 !important;
                line-height: 1.6 !important;
                flex-grow: 1 !important;
                font-size: 15px !important;
            }
            
            .reviews-swiper .review-author {
                font-weight: 600 !important;
                color: #333 !important;
                margin: 0 !important;
                text-align: right !important;
                font-size: 14px !important;
            }
        ');
    }
        
        if ($has_form) {
            $css .= '
                .reviews-form-container {
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .reviews-form-container h3 {
                    color: #333;
                    text-align: center;
                    margin-bottom: 20px;
                }
                .reviews-form-container .form-group {
                    margin-bottom: 15px;
                }
                .reviews-form-container label {
                    display: block;
                    font-weight: bold;
                    margin-bottom: 5px;
                    color: #333;
                }
                .reviews-form-container input,
                .reviews-form-container textarea,
                .reviews-form-container select {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    font-size: 14px;
                    box-sizing: border-box;
                }
                .reviews-form-container .rating-stars {
                    display: flex;
                    gap: 5px;
                    margin: 10px 0;
                }
                .reviews-form-container .star {
                    font-size: 24px;
                    color: #ddd;
                    cursor: pointer;
                    user-select: none;
                    transition: color 0.2s;
                }
                .reviews-form-container .star:hover,
                .reviews-form-container .star.active {
                    color: #ffc107;
                }
                .reviews-form-container button {
                    background: #0073aa;
                    color: white;
                    padding: 12px 25px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                    width: 100%;
                    transition: background 0.3s;
                }
                .reviews-form-container button:hover {
                    background: #005a87;
                }
                .reviews-form-container button:disabled {
                    background: #ccc;
                    cursor: not-allowed;
                }
                #thank-you-message {
                    text-align: center;
                    padding: 30px;
                    background: #f0f8ff;
                    border: 1px solid #b3d9ff;
                    border-radius: 8px;
                    color: #0073aa;
                }
                #form-response {
                    margin-top: 15px;
                    padding: 10px;
                    border-radius: 4px;
                }
                #form-response.success {
                    background: #d4edda;
                    border: 1px solid #c3e6cb;
                    color: #155724;
                }
                #form-response.error {
                    background: #f8d7da;
                    border: 1px solid #f5c6cb;
                    color: #721c24;
                }
            ';
        }
        
        if (!empty($css)) {
            wp_add_inline_style('swiper-css', $css);
        }
    }
    
    private function add_inline_scripts($has_carousel, $has_form) {
        if ($has_carousel) {
        $delay_seconds = intval(get_option('cr_scroll_delay', 3));
        $delay_ms = $delay_seconds * 1000;
        
        wp_add_inline_script('swiper-js', "
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Iniciando Swiper...');
                
                // Verificar que Swiper esté cargado
                if (typeof Swiper === 'undefined') {
                    console.error('Swiper no está cargado');
                    return;
                }
                
                // Buscar el elemento
                const swiperElement = document.querySelector('.reviews-swiper');
                if (!swiperElement) {
                    console.error('Elemento .reviews-swiper no encontrado');
                    return;
                }
                
                // Inicializar Swiper
                const swiper = new Swiper('.reviews-swiper', {
                    slidesPerView: 'auto',
                    spaceBetween: 20,
                    loop: true,
                    autoplay: {
                        delay: $delay_ms,
                        disableOnInteraction: false,
                    },
                    grabCursor: true,
                    freeMode: {
                        enabled: true,
                        sticky: false,
                    },
                    breakpoints: {
                        320: {
                            slidesPerView: 1,
                            spaceBetween: 10
                        },
                        768: {
                            slidesPerView: 'auto',
                            spaceBetween: 20
                        }
                    }
                });
                
                console.log('Swiper inicializado:', swiper);
            });
        ");
    }
        
        if ($has_form) {
            wp_add_inline_script('jquery', "
                jQuery(document).ready(function($) {
                    // Sistema de estrellas interactivo
                    $('.star').on('click', function() {
                        var rating = $(this).data('rating');
                        $('#rf_valoracion').val(rating);
                        
                        // Actualizar visualización
                        $('.star').removeClass('active');
                        for(var i = 1; i <= rating; i++) {
                            $('.star[data-rating=' + i + ']').addClass('active');
                        }
                    });
                    
                    // Hover effect para estrellas
                    $('.star').hover(
                        function() {
                            var rating = $(this).data('rating');
                            $('.star').removeClass('active');
                            for(var i = 1; i <= rating; i++) {
                                $('.star[data-rating=' + i + ']').addClass('active');
                            }
                        },
                        function() {
                            var currentRating = $('#rf_valoracion').val();
                            $('.star').removeClass('active');
                            for(var i = 1; i <= currentRating; i++) {
                                $('.star[data-rating=' + i + ']').addClass('active');
                            }
                        }
                    );
                    
                    // Envío del formulario via AJAX
                    $('#review-form-frontend').on('submit', function(e) {
                        e.preventDefault();
                        
                        var formData = {
                            action: 'submit_review_frontend',
                            nombre: $('#rf_nombre').val(),
                            valoracion: $('#rf_valoracion').val(),
                            producto_servicio_id: $('#rf_producto').val(),
                            texto: $('#rf_texto').val(),
                            nonce: '" . wp_create_nonce('review_form_nonce') . "'
                        };
                        
                        $('#submit-review').prop('disabled', true).text('Enviando...');
                        $('#form-response').removeClass('success error').text('');
                        
                        $.post('" . admin_url('admin-ajax.php') . "', formData)
                            .done(function(response) {
                                if (response.success) {
                                    // Ocultar formulario y mostrar agradecimiento
                                    $('#review-form-frontend').fadeOut(300, function() {
                                        $('#thank-you-message').fadeIn(300);
                                    });
                                } else {
                                    $('#form-response').addClass('error').text(response.data || 'Error al enviar la reseña');
                                    $('#submit-review').prop('disabled', false).text('Enviar Reseña');
                                }
                            })
                            .fail(function() {
                                $('#form-response').addClass('error').text('Error de conexión. Inténtalo de nuevo.');
                                $('#submit-review').prop('disabled', false).text('Enviar Reseña');
                            });
                    });
                    
                    // Inicializar con 5 estrellas por defecto
                    for(var i = 1; i <= 5; i++) {
                        $('.star[data-rating=' + i + ']').addClass('active');
                    }
                });
            ");
        }
    }
}
