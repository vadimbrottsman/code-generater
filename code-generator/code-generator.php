<?php
/*
Plugin Name: Code Generator
Plugin URI: https://t.me/megeroi
Description: Генерация кодов
Author: Vadim Brottsman
Version: 1.0
Author URI: https://t.me/megeroi
*/

function code_generator_admin_style() {
	wp_enqueue_style( 'code-generator-admin-style', plugin_dir_url(__FILE__).'css/admin-style.css' );
	wp_enqueue_script('code-generator-admin-script', plugin_dir_url(__FILE__).'js/admin-script.js', array('jquery') );
}

add_action('admin_enqueue_scripts', 'code_generator_admin_style');
add_action('wp_enqueue_scripts', 'code_generator_admin_style');

// Добавление нового типа постов Коды
function code_generator_posttype_code() {
    $labels = array(
        'name' => _x( 'Коды', 'Тип записей Коды' ),
        'singular_name' => _x( 'Коды', 'Тип записей Коды' ),
        'menu_name' => 'Коды',
        'all_items' => 'Все коды',
        'view_item' => 'Смотреть код',
        'add_new_item' => 'Добавить обзор кодов',
        'add_new' => 'Добавить новый код',
        'edit_item' => 'Редактировать код',
        'update_item' => 'Обновить код',
        'search_items' => 'Искать код',
        'not_found' => 'Не найдено',
        'not_found_in_trash' => 'Не найдено в корзине',
    );

    $args = array(
        'label' => 'codes',
        'description' => 'Каталог кодов',
        'labels' => $labels,
        'supports' => array( 'title', 'author'),
        'has_archive' => true,
        'taxonomies' => [],
        'hierarchical' => false,
        'public' => true,
        'menu_icon' => 'dashicons-editor-spellcheck',
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => false,
        'capability_type' => 'post',
    );

    register_post_type( 'codes', $args );

}

add_action( 'init', 'code_generator_posttype_code', 0 );


// Добавление страницы в админку
add_action( 'admin_menu', 'code_generator_admin_page', 2);

function code_generator_admin_page() {
	add_menu_page(
		'Сгенерировать коды',
		'Генератор кодов',
		'read',
		'page_code_generator',
		'code_generator_admin_page_callback',
		'dashicons-editor-spellcheck',
		4
	);

	add_menu_page(
		'Активировать коды',
		'Активировать',
		'install_plugins',
		'page_code_activator',
		'code_generator_activator_admin_page_callback',
		'dashicons-editor-spellcheck',
		3
	);
}

function code_generator_activator_admin_page_callback () {
	require_once 'templates/admin-activator.php';
}

// Подключение шаблона страницы генерации
function code_generator_admin_page_callback() {
	require_once 'templates/admin-page.php';
}


// Обработка формы
add_action( 'wp_ajax_code_generator', 'code_generator_ajax' );
add_action( 'wp_ajax_nopriv_code_generator', 'code_generator_ajax' );

function code_generator_ajax() {
	$type    = trim($_POST['type']);
	$count = trim($_POST['count']);
	$count = (int) $count;
	$error = false;

	//Валидация
	if ( empty($type) || empty($count) ) {
		$error = true;
		$message = 'Не все параметры указаны';
	}

	if ( !is_int($count) ) $count = 1;

	if ($count > 100) {
		$error = true;
		$message = 'Нельзя сгенерировать больше 100 кодов за раз';
	}

	//Генерация кодов
	$code_symbol = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	$codes_generate_json = array();

	for ($i = 1; $i <= $count; $i++) {

	    $code = generate_string($code_symbol, 10);
	    array_push($codes_generate_json, $code);

	    if ($error == false) {

	    	$query = new WP_Query( array(
	    		'title'		=> $code,
				'post_type' => array('codes')
			) );

			if ($query->have_posts())  $code = generate_string($code_symbol, 10);

			$codes_data = array(
				'post_author'   => get_current_user_id(),
			    'post_status'   => 'publish',
			    'post_type'     => 'codes',
			    'post_title'    => $code,
			);

			$code_id = wp_insert_post( $codes_data );
			update_post_meta( $code_id, 'status', 'active' );
			update_post_meta( $code_id, 'type', $type );
			update_post_meta( $code_id, 'count_activated', 0 );
		}
	}

	if ($error == false) {

		$return = array(
			'type'   => get_current_user_id(),
			'count'  => $_POST['count'],
			'codes' => $codes_generate_json
		);
		wp_send_json_success( $return );
	} else {
		$return = array(
			'message'   => $message,
		);
		wp_send_json_error( $return );
	}
}

function generate_string($input, $strength = 16) {
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }

    return $random_string;
}


// Обработка формы активации
add_action( 'wp_ajax_code_activator', 'code_generator_activate_ajax' );
add_action( 'wp_ajax_nopriv_code_activator', 'code_generator_activate_ajax' );

function code_generator_activate_ajax() {
	$activate_code = trim($_POST['activate_code']);
	$error = false;

	// Валидация
	if (empty($activate_code)) {
		$error = true;
		$message = 'Укажите код';
	}


	$codes = new WP_Query( array(
	    'post_type'      => 'codes',
	    'post_status'    => 'publish',
	    'title' 		 => $activate_code,
	) );

	if ( $codes->have_posts() ) {
	   	while ( $codes->have_posts() ) {
	   		$codes->the_post();

	   		$status_code = get_post_meta(get_the_ID(), 'status', 1);
			$type = get_post_meta(get_the_ID(), 'type', 1);

			if ($type == 'type_one' && $status_code == 'activated') {
				$error = true;
				$message = 'Код уже активирован';
			}

			if ($type == 'type_no_limit' && $status_code == 'activated') {
				$error = true;
				$message = 'Код уже активирован';
			}


        	if ($error == false) {

        		$count_activated = get_post_meta(get_the_ID(), 'count_activated', 1);
        		$count_activated++;

        		//Если код одноразовый и со статусом активен
        		if ($type == 'type_one' && $status_code == 'active') update_post_meta( get_the_ID(), 'status', 'activated' );

        		if ($status_code == 'active') update_post_meta( get_the_ID(), 'count_activated', $count_activated );

        		$status = get_post_meta(get_the_ID(), 'status', 1);
		   		if ($status == 'active') $status = 'Активный';
		   		else $status = 'Активированный';


		   		if ($type == 'type_one') $type = 'Одноразовый';
		   		else $type = 'Многоразовый';


				$return = array(
					'status'  => $status,
					'type'   => $type,
					'count_activated' => get_post_meta(get_the_ID(), 'count_activated', 1),
					'date' => get_the_date(),
					'author' => get_the_author_meta('user_login'),
					'actirovalchik' => get_the_author_meta('current_user_can('administrator'),
				);
				wp_send_json_success( $return );

			} else {
				$return = array(
					'message'   => $message,
				);
				wp_send_json_error( $return );
			}

    	}
	} else {
		$return = array(
			'message'   => 'Код не валидный',
		);

		wp_send_json_error( $return );
	}

}

// Убрать чужие коды
function code_generator_author_parse_query( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/edit.php' ) !== false ) {
        if ( !current_user_can( 'level_10' ) ) {
            global $current_user;
            $wp_query->set( 'author', $current_user->ID );
            $wp_query->set( 'contributor', $current_user->ID );
        }
    }
}

add_filter('parse_query', 'code_generator_author_parse_query' );


// Метаблок для кастомного типа данных в админке
add_action('add_meta_boxes', 'code_generator_extra_fields', 1);

function code_generator_extra_fields() {
  if( current_user_can( 'administrator' ) ) {
  	add_meta_box( 'extra_fields', 'Статус', 'code_generator_fields_box_page_func', 'codes', 'normal', 'high'  );
  	add_meta_box( 'activated_fields', 'Количество активаций', 'code_generator_fields_activate_page_func', 'codes', 'normal', 'high'  );
  }

  add_meta_box( 'downloads_fields', 'Скачать шаблон', 'code_generator_fields_download_template', 'codes', 'normal', 'high'  );
}


// HTML код для блока
function code_generator_fields_box_page_func($post){
   ?>

	<p>
   		<fieldset>
           	<label for="status_active">
        		<input type="radio" id="status_active" name="extra[status]" value="active" <?php if (get_post_meta($post->ID, 'status', 1) == 'active') echo 'checked'; ?> >
        		<span>Активный</span>
            </label>
            <br>
	        <label for="status_activated">
	            <input type="radio" id="status_activated" name="extra[status]" value="activated" <?php if (get_post_meta($post->ID, 'status', 1) == 'activated') echo 'checked'; ?>>
	            <span>Активированный</span>
	        </label>
        </fieldset>
   </p>

   <p>
   	Тип кода: <strong><?php
   		$type = get_post_meta($post->ID, 'type', 1);
   		if ($type == 'type_one') echo 'Одноразовый';
   		else echo 'Многоразовый';
   	?></strong>
   </p>

  <input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>"/>
   <?php
}

function code_generator_fields_download_template ($post) {
	?>
	<p>
		<a href="<?= plugin_dir_url(__FILE__)?>pdf-generate.php?code=<?=get_the_title($post->ID)?>" class="button button-primary download_template">Скачать шаблон</a>
	</p>
	<?php
}

function code_generator_fields_activate_page_func ($post) {
	?>
	<p>
		<label for="count_activated">
        <input type="text" id="count_activated" name="extra[count_activated]" value="<?=get_post_meta($post->ID, 'count_activated', 1)?>" >
        		<span>Количество активаций</span>
        </label>
   </p>
	<?php
}


// Включаем обновление полей при сохранении
add_action( 'save_post_codes', 'code_generator_extra_fields_update', 0 );

function code_generator_extra_fields_update( $post_id ){
	if ( empty( $_POST['extra'] )
		|| ! wp_verify_nonce( $_POST['extra_fields_nonce'], __FILE__ )
		|| wp_is_post_autosave( $post_id )
		|| wp_is_post_revision( $post_id )
	) return false;

	$_POST['extra'] = array_map( 'sanitize_text_field', $_POST['extra'] );
	foreach( $_POST['extra'] as $key => $value ){
		if( empty($value) ){
			delete_post_meta( $post_id, $key );
			continue;
		}

		update_post_meta( $post_id, $key, $value );
	}

	return $post_id;
}

// создаем новую колонку
add_filter( 'manage_'.'codes'.'_posts_columns', 'code_generator_add_views_column', 4 );

function code_generator_add_views_column( $columns ){
	$num = 3; // после какой по счету колонки вставлять новые

	$new_columns = array(
		'status' => 'Статус',
		'type' => 'Тип кода',
		'count_activated' => 'Количество активаций',
		'template' => 'Шаблон',
	);

	return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
}

// заполняем колонку данными
add_action('manage_'.'codes'.'_posts_custom_column', 'code_generator_fill_views_column', 5, 2 );

function code_generator_fill_views_column( $colname, $post_id ){
	if( $colname === 'status' ) {
		if ( get_post_meta( $post_id, 'status', 1 ) == 'activated') echo 'Активирован';
		else  echo 'Активен';
	}
	if( $colname === 'type' ) {
		if ( get_post_meta( $post_id, 'type', 1 ) == 'type_one') echo 'Одноразовый';
		else  echo 'Многоразовый';
	}
	if( $colname === 'count_activated' ) {
		echo get_post_meta( $post_id, 'count_activated', 1 );
	}
	if( $colname === 'template' ) {
		echo '<a href="'.plugin_dir_url(__FILE__).'pdf-generate.php?code='.get_the_title($post_id).'" class="button button-primary download_template">Скачать шаблон</a>';
	}
}


/*
* Шорткоды
*/

// Вывод страницы генерации
add_shortcode('code_generator', 'code_generator_shortcode');

function code_generator_shortcode($atts){
    ob_start();

	if (checkRole()):
		?>
		<div class="code_generator_shortcode">
			<?php
				require_once 'templates/admin-page.php';
			?>
		</div>
		<?php

	else :

		?>
			<p class="error_autorization">Чтобы посмотреть эту страницу, войдите на сайт</p>
		<?php
	endif;

    return ob_get_clean();
}

// Вывод всех кодов
add_shortcode('code_generator_list', 'code_generator_list_shortcode');

function code_generator_list_shortcode($atts) {
	$current = !empty( $_GET['codes'] ) ? $_GET['codes'] : 1;
	$count = 30;

	$codes = new WP_Query( array(
	    'post_type'      => 'codes',
	    'post_status'    => 'publish',
	    'posts_per_page' => $count,
	    'paged'          => $current,
	    'author'		 => get_current_user_id(),
	) );

    $url = plugin_dir_url(__FILE__);

    ob_start();

    if (checkRole()):
    ?>
  		<div class="codes">
  			<table class="codes__table">
  				<tr>
					<th>Код</th>
					<th>Статус</th>
					<th>Тип кода</th>
					<th>Дата</th>
					<th>Действие</th>
  				</tr>
	  			<?php
					if ( $codes->have_posts() ) {
				    	while ( $codes->have_posts() ) {
				    		$codes->the_post();

				        	include('templates/loop-code.php');

				    	}
				    }
				?>
			</table>
			<div class="pagination">
		    <?php
		            $paginations = paginate_links(
		            [
		              'base'         => getUrlPage().'%_%',
		              'format'       =>   '?codes=%#%',
		              'total'   => $codes->max_num_pages,
		              'show_all'  => true,
		              'current' => $current,
		              'type' => 'array',
		              'prev_text'    => '<',
		              'next_text'    => '>',

		            ]
		          );

		        if ($paginations != null) {

		        	echo '<ul>';
		        	foreach($paginations as $pagination) {
		            	echo '<li class="number">'.$pagination.'</li>';
		            }
		        	echo '</ul>';
		        }
		    ?>
			</div>
	<?php
		wp_reset_postdata();
	?>

  		</div>
	<?php

	else:
		?>
			<p class="error_autorization">Чтобы посмотреть эту страницу, войдите на сайт</p>
		<?php
	endif;
    return ob_get_clean();
}


function getUrlPage() {
	$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$url = explode('?', $url);
	$url = $url[0];

	return $url;
}


function checkRole() {
	$user = wp_get_current_user();
	$allowed_roles = array( 'editor', 'administrator', 'author', 'contributor' );

	if ( array_intersect( $allowed_roles, $user->roles ) ) return true;
	else return false;
}

?>
