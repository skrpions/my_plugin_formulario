<?php
/*
Plugin Name: KFP Form Autoevaluacion
Plugin URI: 
Description: Plugin para crear un formulario personalizado con el shortcode [my_plugin_formulario]
Version: 1.0
Author: NESTOR MARTINEZ C
Author URI: 
License: 
License URI: 
*/

// Cuando el plugin se active se crea la tabla para recoger los datos si no existe
register_activation_hook(__FILE__, 'Kfp_Aspirante_init');

/**
 * Crea la tabla para recoger los datos del formulario
 *
 * @return void
 */
function Kfp_Aspirante_init()
{
    global $wpdb; // Este objeto global permite acceder a la base de datos de WP
    // Crea la tabla sólo si no existe
    // Utiliza el mismo prefijo del resto de tablas
    $tabla_aspirantes = $wpdb->prefix . 'aspirante';
    // Utiliza el mismo tipo de orden de la base de datos
    $charset_collate = $wpdb->get_charset_collate();
    // Prepara la consulta
    $query = "CREATE TABLE IF NOT EXISTS $tabla_aspirantes (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(40) NOT NULL,
        correo varchar(100) NOT NULL,
        nivel_html smallint(4) NOT NULL,
        nivel_css smallint(4) NOT NULL,
        nivel_js smallint(4) NOT NULL,
        aceptacion smallint(4) NOT NULL,
        created_at datetime NOT NULL,
        UNIQUE (id)
        ) $charset_collate;";
    // La función dbDelta permite crear tablas de manera segura se
    // define en el archivo upgrade.php que se incluye a continuación
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($query); // Lanza la consulta para crear la tabla de manera segura
}


// Define el shortcode y lo asocia a una función
add_shortcode('kfp_aspirante_form', 'Kfp_Aspirante_form');

/** 
 * Define la función que ejecutará el shortcode
 * De momento sólo pinta un formulario que no hace nada
 * 
 * @return string
 */
function Kfp_Aspirante_form()
{
    // Carga la hoja de estilo para poner más bonito el formulario
    wp_enqueue_style('css_aspirante', plugins_url('style.css', __FILE__));

    global $wpdb; // Este objeto global permite acceder a la base de datos de WP
    // Si viene del formulario  graba en la base de datos
    // Cuidado con el último igual de la condición del if que es doble
    if ( $_POST['nombre'] != ''
        and is_email($_POST['correo'])
        and $_POST['nivel_html'] != ''
        and $_POST['nivel_css'] != ''
        and $_POST['nivel_js'] != ''
        and $_POST['aceptacion'] == '1'
    ) {
        $tabla_aspirantes = $wpdb->prefix . 'aspirante';
        $nombre = sanitize_text_field($_POST['nombre']); // sanitize_text_field evita que se registren datos incorrectos, Muy util para evadir injection sql
        $correo = $_POST['correo'];
        $nivel_html = (int)$_POST['nivel_html']; // Con (int) obligo a que todo lo que ingrese en el campo se convierta a entero 
        $nivel_css = (int)$_POST['nivel_css']; // Con (int) obligo a que todo lo que ingrese en el campo se convierta a entero 
        $nivel_js = (int)$_POST['nivel_js']; // Con (int) obligo a que todo lo que ingrese en el campo se convierta a entero 
        $aceptacion = (int)$_POST['aceptacion']; // Con (int) obligo a que todo lo que ingrese en el campo se convierta a entero 
        $created_at = date('Y-m-d H:i:s');
        $wpdb->insert(
            $tabla_aspirantes,array(
                'nombre' => $nombre,
                'correo' => $correo,
                'nivel_html' => $nivel_html,
                'nivel_css' => $nivel_css,
                'nivel_js' => $nivel_js,
                'aceptacion' => $aceptacion,
                'created_at' => $created_at,
            )
        );
        echo "<p class='exito'><b>Tus datos han sido registrados</b>. Gracias 
            por tu interés. En breve contactaré contigo.<p>";
    }



    // Esta función de PHP activa el almacenamiento en búfer de salida (output buffer)
    // Cuando termine el formulario lo imprime con la función ob_get_clean
    ob_start();
?>
    <form action="<?php get_the_permalink(); ?>" method="post" id="form_aspirante
class=" cuestionario"">
        <div class="form-input">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>
        <div class="form-input">
            <label for='correo'>Correo</label>
            <input type="email" name="correo" id="correo" required>
        </div>
        <div class="form-input">
            <label for="nivel_html">¿Cuál es tu nivel de HTML?</label>
            <input type="radio" name="nivel_html" value="1" required> Nada
            <br><input type="radio" name="nivel_html" value="2" required> Estoy
            aprendiendo
            <br><input type="radio" name="nivel_html" value="3" required> Tengo
            experiencia
            <br><input type="radio" name="nivel_html" value="4" required> Lo
            domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_css">¿Cuál es tu nivel de CSS?</label>
            <input type="radio" name="nivel_css" value="1" required> Nada
            <br><input type="radio" name="nivel_css" value="2" required> Estoy
            aprendiendo
            <br><input type="radio" name="nivel_css" value="3" required> Tengo
            experiencia
            <br><input type="radio" name="nivel_css" value="4" required> Lo
            domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_js">¿Cuál es tu nivel de JavaScript?</label>
            <input type="radio" name="nivel_js" value="1" required> Nada
            <br><input type="radio" name="nivel_js" value="2" required> Estoy
            aprendiendo
            <br><input type="radio" name="nivel_js" value="3" required> Tengo
            experiencia
            <br><input type="radio" name="nivel_js" value="4" required> Lo domino
            al dedillo
        </div>
        <div class="form-input">
            <label for="aceptacion">La información facilitada se tratará
                con respeto y admiración.</label>
            <input type="checkbox" id="aceptacion" name="aceptacion" value="1" required> Entiendo y acepto las condiciones
        </div>
        <div class="form-input">
            <input type="submit" value="Enviar">
        </div>
    </form>
<?php

    // Devuelve el contenido del buffer de salida
    return ob_get_clean();
}
