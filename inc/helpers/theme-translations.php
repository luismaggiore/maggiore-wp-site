<?php
if (!defined('ABSPATH')) exit;

/**
 * SISTEMA DE TRADUCCIÓN DE TEXTOS DEL TEMA
 */

function mg_t($key) {
    $lang = mg_current_lang();
    $translations = mg_get_theme_translations();
    return isset($translations[$key][$lang]) ? $translations[$key][$lang] : $key;
}

function mg_te($key) {
    echo mg_t($key);
}

function mg_get_theme_translations() {
    return [
        'ver_mas' => ['es' => 'Ver más', 'en' => 'See more', 'pt' => 'Ver mais'],
        'leer_mas' => ['es' => 'Leer más', 'en' => 'Read more', 'pt' => 'Ler mais'],
        'contactar' => ['es' => 'Contactar', 'en' => 'Contact', 'pt' => 'Contatar'],
        'volver' => ['es' => 'Volver', 'en' => 'Back', 'pt' => 'Voltar'],
        
        'nuestros_servicios' => ['es' => 'Nuestros Servicios', 'en' => 'Our Services', 'pt' => 'Nossos Serviços'],
        'nuestro_equipo' => ['es' => 'Nuestro Equipo', 'en' => 'Our Team', 'pt' => 'Nossa Equipe'],
        'nuestros_clientes' => ['es' => 'Nuestros Clientes', 'en' => 'Our Clients', 'pt' => 'Nossos Clientes'],
        'casos_de_exito' => ['es' => 'Casos de Éxito', 'en' => 'Case Studies', 'pt' => 'Casos de Sucesso'],
        
        'filtrar_por' => ['es' => 'Filtrar por', 'en' => 'Filter by', 'pt' => 'Filtrar por'],
        'todos' => ['es' => 'Todos', 'en' => 'All', 'pt' => 'Todos'],
        'filtrar_por_industria' => ['es' => 'Filtrar por industria:', 'en' => 'Filter by industry:', 'pt' => 'Filtrar por indústria:'],
        'filtrar_por_categoria' => ['es' => 'Filtrar por categoría:', 'en' => 'Filter by category:', 'pt' => 'Filtrar por categoria:'],
        
        'buscar' => ['es' => 'Buscar', 'en' => 'Search', 'pt' => 'Buscar'],
        'sin_resultados' => ['es' => 'No se encontraron resultados', 'en' => 'No results found', 'pt' => 'Nenhum resultado encontrado'],
        
        'proceso' => ['es' => 'Proceso', 'en' => 'Process', 'pt' => 'Processo'],
        'entregables' => ['es' => 'Entregables', 'en' => 'Deliverables', 'pt' => 'Entregáveis'],
        'beneficios' => ['es' => 'Beneficios', 'en' => 'Benefits', 'pt' => 'Benefícios'],
        
        'nombre' => ['es' => 'Nombre', 'en' => 'Name', 'pt' => 'Nome'],
        'email' => ['es' => 'Email', 'en' => 'Email', 'pt' => 'Email'],
        'telefono' => ['es' => 'Teléfono', 'en' => 'Phone', 'pt' => 'Telefone'],
        'empresa' => ['es' => 'Empresa', 'en' => 'Company', 'pt' => 'Empresa'],
        'mensaje' => ['es' => 'Mensaje', 'en' => 'Message', 'pt' => 'Mensagem'],
        'enviar' => ['es' => 'Enviar', 'en' => 'Send', 'pt' => 'Enviar'],
        
        'siguenos' => ['es' => 'Síguenos', 'en' => 'Follow us', 'pt' => 'Siga-nos'],
        'contactanos' => ['es' => 'Contáctanos', 'en' => 'Contact us', 'pt' => 'Entre em contato'],
        
        'todos_derechos' => ['es' => 'Todos los derechos reservados', 'en' => 'All rights reserved', 'pt' => 'Todos os direitos reservados'],
    ];
}
