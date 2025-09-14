jQuery(document).ready(function($) {
    'use strict';
    
    // Media Uploader para avatares
    let frame;
    
    $('#upload_avatar_button').on('click', function(e) {
        e.preventDefault();
        
        // Si el frame ya existe, abrirlo
        if (frame) {
            frame.open();
            return;
        }
        
        // Crear frame del media library
        frame = wp.media({
            title: 'Seleccionar avatar',
            button: {
                text: 'Usar esta imagen'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        // Cuando se selecciona una imagen
        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            $('#avatar_url').val(attachment.url);
            $('#avatar_preview').html(
                '<img src="' + attachment.url + '" style="max-width:80px; border-radius:50%; border: 2px solid #ddd;">'
            );
            $('#remove_avatar_button').show();
        });
        
        frame.open();
    });
    
    // Quitar avatar
    $('#remove_avatar_button').on('click', function(e) {
        e.preventDefault();
        $('#avatar_url').val('');
        $('#avatar_preview').empty();
        $(this).hide();
    });
    
    // Select all checkboxes
    $('#select-all').on('change', function() {
        const checkboxes = $("input[name='resenas_validar[]']");
        checkboxes.prop('checked', this.checked);
    });
    
    // Individual checkboxes
    $("input[name='resenas_validar[]']").on('change', function() {
        const total = $("input[name='resenas_validar[]']").length;
        const checked = $("input[name='resenas_validar[]']:checked").length;
        $('#select-all').prop('checked', total === checked);
        $('#select-all').prop('indeterminate', checked > 0 && checked < total);
    });
    
    // Validación de shortcode
    $('#shortcode').on('input', function() {
        let value = $(this).val();
        // Solo permitir letras, números y guiones bajos
        value = value.replace(/[^a-zA-Z0-9_]/g, '');
        $(this).val(value);
        
        // Mostrar preview
        if (value.length > 0) {
            const preview = '[' + value + ']';
            $('#shortcode').attr('title', 'Vista previa: ' + preview);
        }
    });
    
    // Auto-generar shortcode desde nombre
    $('#nombre').on('input', function() {
        const shortcodeField = $('#shortcode');
        if (shortcodeField.val() === '' || shortcodeField.data('auto-generated')) {
            let nombre = $(this).val();
            let shortcode = 'reviews_' + nombre.toLowerCase()
                .replace(/[^a-z0-9\s]/g, '') // quitar caracteres especiales
                .replace(/\s+/g, '_') // espacios a guiones bajos
                .substring(0, 30); // limitar longitud
            
            shortcodeField.val(shortcode).data('auto-generated', true);
        }
    });
    
    // Marcar como editado manualmente
    $('#shortcode').on('focus', function() {
        $(this).data('auto-generated', false);
    });
    
    // Confirmaciones mejoradas
    $('.button-secondary[onclick*="confirm"]').on('click', function(e) {
        const originalOnclick = this.getAttribute('onclick');
        this.removeAttribute('onclick');
        
        e.preventDefault();
        
        const isDelete = this.textContent.includes('Eliminar') || this.textContent.includes('🗑️');
        const message = isDelete 
            ? '⚠️ Esta acción no se puede deshacer. ¿Estás seguro de que quieres eliminar este elemento?'
            : '¿Estás seguro de que quieres realizar esta acción?';
        
        if (confirm(message)) {
            // Ejecutar la acción original
            eval(originalOnclick);
        }
    });
    
    // Contador de caracteres para textarea
    $('textarea[name="texto"]').after('<div class="char-counter" style="text-align: right; margin-top: 5px; font-size: 12px; color: #666;"></div>');
    
    $('textarea[name="texto"]').on('input', function() {
        const length = $(this).val().length;
        const counter = $(this).next('.char-counter');
        counter.text(length + ' caracteres');
        
        if (length > 500) {
            counter.css('color', '#d63638');
        } else if (length > 400) {
            counter.css('color', '#dba617');
        } else {
            counter.css('color', '#666');
        }
    }).trigger('input');
    
    // Mejorar UX del formulario
    $('form').on('submit', function() {
        const submitButton = $(this).find('input[type="submit"], button[type="submit"]');
        const originalText = submitButton.val() || submitButton.text();
        
        submitButton.prop('disabled', true);
        
        if (submitButton.is('input')) {
            submitButton.val('Guardando...');
        } else {
            submitButton.text('Guardando...');
        }
        
        // Restaurar después de 10 segundos por seguridad
        setTimeout(function() {
            submitButton.prop('disabled', false);
            if (submitButton.is('input')) {
                submitButton.val(originalText);
            } else {
                submitButton.text(originalText);
            }
        }, 10000);
    });
    
    // Filtros con estado visual
    $('.tablenav select').on('change', function() {
        const form = $(this).closest('form');
        if (form.length) {
            // Añadir indicador visual de filtrado
            $(this).css('border-color', '#0073aa');
        }
    });
    
    // Tooltips simples
    $('[title]').each(function() {
        $(this).on('mouseenter', function() {
            const title = $(this).attr('title');
            $(this).attr('data-original-title', title).removeAttr('title');
            
            const tooltip = $('<div class="simple-tooltip">' + title + '</div>');
            $('body').append(tooltip);
            
            const pos = $(this).offset();
            tooltip.css({
                position: 'absolute',
                top: pos.top - tooltip.outerHeight() - 5,
                left: pos.left,
                background: '#333',
                color: '#fff',
                padding: '5px 8px',
                borderRadius: '3px',
                fontSize: '12px',
                zIndex: 9999,
                whiteSpace: 'nowrap'
            });
        }).on('mouseleave', function() {
            $('.simple-tooltip').remove();
            $(this).attr('title', $(this).attr('data-original-title'));
        });
    });
});
