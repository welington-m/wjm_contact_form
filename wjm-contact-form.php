<?php
/*
Plugin Name: WJM Contact Form
Description: Plugin de contato com arquitetura limpa
Version: 1.1
Author: Welington Miyazato
*/

add_shortcode('wjm_contact_form', function() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'form-handler.php';
    return ob_get_clean();
});

add_action('admin_menu', function() {
    add_options_page(
        'Configurações do Formulário',
        'Formulário WJM',
        'manage_options',
        'wjm-contact-form',
        'wjm_contact_form_settings_page'
    );

    add_menu_page(
        'Mensagens Recebidas',
        'Mensagens WJM',
        'manage_options',
        'wjm-contact-messages',
        'wjm_contact_messages_page',
        'dashicons-email-alt2'
    );
});

add_action('admin_init', function() {
    register_setting('wjm_contact_form_settings', 'wjm_contact_email');
    register_setting('wjm_contact_form_settings', 'wjm_contact_select_options');

    add_settings_section(
        'wjm_contact_main_section',
        'Configurações Gerais',
        null,
        'wjm-contact-form'
    );

    add_settings_field(
        'wjm_contact_email',
        'Email de destino',
        function() {
            $value = esc_attr(get_option('wjm_contact_email', get_option('admin_email')));
            echo "<input type='email' name='wjm_contact_email' value='$value' size='40'>";
        },
        'wjm-contact-form',
        'wjm_contact_main_section'
    );

    add_settings_section('wjm_contact_select_section', 'Opções do campo "Assunto"', null, 'wjm-contact-form');

    add_settings_field('wjm_contact_select_options', 'Valores do campo select (1 por linha)', function () {
        $value = get_option('wjm_contact_select_options', "Alistamento\nContato");
        echo "<textarea name='wjm_contact_select_options' rows='5' cols='50'>" . esc_textarea($value) . "</textarea>";
    }, 'wjm-contact-form', 'wjm_contact_select_section');
});

function wjm_contact_form_settings_page() {
    echo '<div class="wrap">';
    echo '<h1>Configurações do Formulário de Contato</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('wjm_contact_form_settings');
    do_settings_sections('wjm-contact-form');
    submit_button();
    echo '</form>';
    echo '</div>';
}

function wjm_contact_messages_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissão para acessar esta página.'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'wjm_contact_form';

    $search_email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
    $search_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 10;
    $offset = ($paged - 1) * $per_page;
    $search_sql = [];

    if ($search_email) {
        $search_sql[] = $wpdb->prepare("email LIKE %s", "%$search_email%");
    }
    if ($search_date) {
        $search_sql[] = $wpdb->prepare("DATE(created_at) = %s", $search_date);
    }

    $where_clause = $search_sql ? 'WHERE ' . implode(' AND ', $search_sql) : '';

    // Total de resultados
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table $where_clause");

    // Obter resultados com limite e offset
    $results = $wpdb->get_results("SELECT * FROM $table $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");

    // Exportar CSV com nonce
    if (isset($_GET['export']) && $_GET['export'] === 'csv' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'export_wjm_csv')) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="mensagens_wjm.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Data', 'Nome', 'Email', 'Mensagem']);
        foreach ($results as $row) {
            fputcsv($output, [$row->created_at, $row->name, $row->email, $row->message]);
        }
        fclose($output);
        exit;
    }

    $base_url = admin_url('admin.php?page=wjm-contact-messages');
    $query_params = [
        'email' => $search_email,
        'date' => $search_date,
    ];

    echo '<div class="wrap">';
    echo '<h1>Mensagens Recebidas</h1>';

    echo '<form method="GET">';
    echo '<input type="hidden" name="page" value="wjm-contact-messages">';
    echo 'Email: <input type="email" name="email" value="' . esc_attr($search_email) . '" /> ';
    echo 'Data: <input type="date" name="date" value="' . esc_attr($search_date) . '" /> ';
    echo '<input type="submit" class="button" value="Filtrar" /> ';
    echo '<a class="button" href="' . esc_url(add_query_arg(array_merge($query_params, [
        'export' => 'csv',
        '_wpnonce' => wp_create_nonce('export_wjm_csv')
    ]), $base_url)) . '">Exportar CSV</a>';
    echo '</form>';

    if ($results) {
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>Data</th><th>Nome</th><th>Email</th><th>Mensagem</th></tr></thead><tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->created_at) . '</td>';
            echo '<td>' . esc_html($row->name) . '</td>';
            echo '<td>' . esc_html($row->email) . '</td>';
            echo '<td>' . esc_html($row->message) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        // Paginação
        $total_pages = ceil($total_items / $per_page);
        if ($total_pages > 1) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            for ($i = 1; $i <= $total_pages; $i++) {
                $url = esc_url(add_query_arg(array_merge($query_params, ['paged' => $i]), $base_url));
                echo ($i == $paged)
                    ? "<span class='tablenav-page-nav'> $i </span>"
                    : "<a class='button' href='$url'>$i</a> ";
            }
            echo '</div></div>';
        }
    } else {
        echo '<p>Nenhuma mensagem encontrada.</p>';
    }
    echo '</div>';
}
