<?php
if (!defined('ABSPATH')) {
    exit;
}

// Obtener valores de filtros
$filtro_producto = isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0;
$filtro_validado = isset($_GET['validado']) ? intval($_GET['validado']) : -1;
?>

<div class="wrap">
    <h1>📝 Gestionar Reseñas</h1>
    
    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>
    
    <a href="<?php echo admin_url('admin.php?page=añadir-resena'); ?>" class="button button-primary">➕ Añadir Nueva</a>
    <br><br>
    
    <!-- Filtros -->
    <div class="tablenav top">
        <form method="get" style="float:left;margin-right:10px;">
            <input type="hidden" name="page" value="gestionar-resenas">
            
            <select name="producto_id">
                <option value="0">Todos los productos</option>
                <?php foreach ($products as $producto): ?>
                    <option value="<?php echo $producto->id; ?>" <?php selected($filtro_producto, $producto->id); ?>>
                        <?php echo esc_html($producto->nombre); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="validado">
                <option value="-1">Todas las reseñas</option>
                <option value="1" <?php selected($filtro_validado, 1); ?>>Solo validadas</option>
                <option value="0" <?php selected($filtro_validado, 0); ?>>Solo pendientes</option>
            </select>
            
            <input type="submit" class="button" value="Filtrar">
            
            <?php if ($filtro_producto > 0 || $filtro_validado >= 0): ?>
                <a href="<?php echo admin_url('admin.php?page=gestionar-resenas'); ?>" class="button">Limpiar filtros</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="clear"></div>
    
    <form method="post">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="check-column">
                        <input type="checkbox" id="select-all">
                    </th>
                    <th>Reseña</th>
                    <th style="width: 150px;">Producto</th>
                    <th style="width: 120px;">Valoración</th>
                    <th style="width: 120px;">Fecha</th>
                    <th style="width: 100px;">Estado</th>
                    <th style="width: 120px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <p>No hay reseñas que coincidan con los filtros.</p>
                            <a href="<?php echo admin_url('admin.php?page=añadir-resena'); ?>" class="button button-primary">
                                Añadir la primera reseña
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $resena): ?>
                        <?php 
                        $estado = $resena->validado ? '✅ Validada' : '⏳ Pendiente';
                        $estrellas = str_repeat('⭐', $resena->valoracion);
                        $texto_truncado = strlen($resena->texto) > 100 ? substr($resena->texto, 0, 100) . '...' : $resena->texto;
                        $row_class = !$resena->validado ? 'style="background-color: #fff8dc;"' : '';
                        ?>
                        <tr <?php echo $row_class; ?>>
                            <td>
                                <input type="checkbox" name="resenas_validar[]" value="<?php echo $resena->id; ?>">
                            </td>
                            <td>
                                <strong><?php echo esc_html($resena->nombre); ?></strong>
                                <br>
                                <span style="font-style: italic; color: #666;">
                                    "<?php echo esc_html($texto_truncado); ?>"
                                </span>
                                <?php if ($resena->avatar_url): ?>
                                    <br><small style="color: #999;">📷 Con avatar</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo esc_html($resena->producto_nombre ?: 'Sin asignar'); ?>
                            </td>
                            <td>
                                <?php echo $estrellas; ?>
                                <br><small>(<?php echo $resena->valoracion; ?>/5)</small>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($resena->fecha)); ?>
                                <br><small><?php echo date('H:i', strtotime($resena->fecha)); ?></small>
                            </td>
                            <td>
                                <span class="<?php echo $resena->validado ? 'status-validated' : 'status-pending'; ?>">
                                    <?php echo $estado; ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=añadir-resena&editar_id=' . $resena->id); ?>" 
                                   class="button button-small" title="Editar">✏️</a>
                                
                                <a href="<?php echo admin_url('admin.php?page=gestionar-resenas&eliminar_id=' . $resena->id); ?>" 
                                   class="button button-small button-secondary" 
                                   onclick="return confirm('¿Eliminar esta reseña permanentemente?')"
                                   title="Eliminar">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if (!empty($reviews)): ?>
            <p class="submit">
                <input type="submit" 
                       name="validar_resenas" 
                       class="button button-primary" 
                       value="✅ Validar Seleccionadas">
                <span style="margin-left: 15px; color: #666;">
                    Selecciona las reseñas que quieres validar y hacer públicas.
                </span>
            </p>
        <?php endif; ?>
    </form>
    
    <script>
    document.getElementById('select-all').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll("input[name='resenas_validar[]']");
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = this.checked;
        }
    });
    </script>
    
    <style>
    .status-validated { color: #46b450; font-weight: bold; }
    .status-pending { color: #ffba00; font-weight: bold; }
    </style>
</div>
