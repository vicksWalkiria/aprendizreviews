<?php
if (!defined('ABSPATH')) {
    exit;
}

$is_edit = !empty($review);
$page_title = $is_edit ? __('✏️ Editar Reseña', 'aprendiz-reviews') : __('➕ Añadir Reseña', 'aprendiz-reviews');
?>

<div class="wrap">
    <h1><?php echo esc_html($page_title); ?></h1>

    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <form method="post">
        <?php if ($is_edit): ?>
            <input type="hidden" name="editar_id" value="<?php echo esc_attr($review->id); ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="nombre"><?php echo esc_html__('Nombre *', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="nombre"
                        name="nombre"
                        value="<?php echo esc_attr($review->nombre ?? ''); ?>"
                        required
                        style="width: 100%;"
                        placeholder="<?php echo esc_attr__('Nombre del cliente', 'aprendiz-reviews'); ?>">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="valoracion"><?php echo esc_html__('Valoración *', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <select id="valoracion" name="valoracion" required>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php selected($review->valoracion ?? 5, $i); ?>>
                                <?php echo str_repeat('⭐', $i) . " ($i/5)"; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="texto"><?php echo esc_html__('Texto de la Reseña *', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <textarea id="texto"
                        name="texto"
                        rows="4"
                        required
                        style="width: 100%;"
                        placeholder="<?php echo esc_attr__('Escribe aquí el comentario de la reseña...', 'aprendiz-reviews'); ?>"><?php echo esc_textarea($review->texto ?? ''); ?></textarea>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="fecha"><?php echo esc_html__('Fecha de Reseña *', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <input type="date"
                        id="fecha"
                        name="fecha"
                        value="<?php echo esc_attr(isset($review->fecha) ? date('Y-m-d', strtotime($review->fecha)) : date('Y-m-d')); ?>"
                        required>
                    <p class="description"><?php echo esc_html__('Fecha en la que se hizo la reseña (se mantendrá la hora actual).', 'aprendiz-reviews'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="producto_servicio_id"><?php echo esc_html__('Producto *', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <select id="producto_servicio_id" name="producto_servicio_id" required>
                        <?php foreach ($products as $producto): ?>
                            <option value="<?php echo $producto->id; ?>" <?php selected($review->producto_servicio_id ?? 1, $producto->id); ?>>
                                <?php echo esc_html($producto->nombre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php echo esc_html__('Producto al que pertenece esta reseña.', 'aprendiz-reviews'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="avatar_url"><?php echo esc_html__('Avatar', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="avatar_url"
                        name="avatar_url"
                        value="<?php echo esc_attr($review->avatar_url ?? ''); ?>"
                        style="width: 60%;"
                        readonly
                        placeholder="<?php echo esc_attr__('URL del avatar', 'aprendiz-reviews'); ?>">
                    <input type="button"
                        id="upload_avatar_button"
                        class="button"
                        value="<?php echo esc_attr__('Elegir imagen', 'aprendiz-reviews'); ?>">
                    <input type="button"
                        id="remove_avatar_button"
                        class="button button-secondary"
                        value="<?php echo esc_attr__('Quitar', 'aprendiz-reviews'); ?>"
                        style="display: <?php echo !empty($review->avatar_url) ? 'inline-block' : 'none'; ?>;">

                    <div id="avatar_preview" style="margin-top: 10px;">
                        <?php if (!empty($review->avatar_url)): ?>
                            <img src="<?php echo esc_url($review->avatar_url); ?>"
                                style="max-width: 80px; border-radius: 50%; border: 2px solid #ddd;">
                        <?php endif; ?>
                    </div>
                    <p class="description"><?php echo esc_html__('Imagen del perfil del cliente (opcional).', 'aprendiz-reviews'); ?></p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit"
                class="button-primary"
                value="<?php echo $is_edit ? esc_attr__('Actualizar reseña', 'aprendiz-reviews') : esc_attr__('Guardar reseña', 'aprendiz-reviews'); ?>">

            <a href="<?php echo admin_url('admin.php?page=gestionar-resenas'); ?>"
                class="button button-secondary"><?php echo esc_html__('Cancelar', 'aprendiz-reviews'); ?></a>
        </p>
    </form>

    <?php if ($is_edit): ?>
        <div class="postbox" style="margin-top: 20px;">
            <h3 style="padding: 10px 15px; margin: 0; background: #f1f1f1;">ℹ️ <?php echo esc_html__('Información de la Reseña', 'aprendiz-reviews'); ?></h3>
            <div style="padding: 15px;">
                <p><strong><?php echo esc_html__('ID:', 'aprendiz-reviews'); ?></strong> <?php echo esc_html($review->id); ?></p>
                <p><strong><?php echo esc_html__('Fecha de creación:', 'aprendiz-reviews'); ?></strong> <?php echo date('d/m/Y H:i', strtotime($review->fecha)); ?></p>
                <p><strong><?php echo esc_html__('Estado:', 'aprendiz-reviews'); ?></strong>
                    <?php if ($review->validado): ?>
                        <span style="color: #46b450;"><?php echo esc_html__('✅ Validada (visible públicamente)', 'aprendiz-reviews'); ?></span>
                    <?php else: ?>
                        <span style="color: #ffba00;"><?php echo esc_html__('⏳ Pendiente de validación', 'aprendiz-reviews'); ?></span>
                    <?php endif; ?>
                </p>
                <p><strong><?php echo esc_html__('Producto asociado:', 'aprendiz-reviews'); ?></strong>
                    <?php
                    $producto_nombre = '';
                    foreach ($products as $p) {
                        if ($p->id == $review->producto_servicio_id) {
                            $producto_nombre = $p->nombre;
                            break;
                        }
                    }
                    echo esc_html($producto_nombre);
                    ?>
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>