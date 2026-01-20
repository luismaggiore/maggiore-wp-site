<?php
/**
 * Template Name: Contacto
 * 
 * Página dedicada de contacto con formulario completo
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<div class="page-contacto">
    <main class="container py-5">
        
        <!-- Header de la página -->
        <header class="contacto-header text-center mb-5 p-top">
            <h1 class="display-4 mb-3">
                <?php _e('Contáctanos', 'maggiore'); ?>
            </h1>
            <p class="lead text-muted mb-0">
                <?php _e('Estamos listos para ayudarte a alcanzar tus objetivos de marketing digital', 'maggiore'); ?>
            </p>
        </header>

        <div class="row">
            
            <!-- Columna de información de contacto -->
            <div class="col-lg-4 mb-5 mb-lg-0 order-2 order-lg-1">
                
                <!-- Info de contacto -->
                <div class="contacto-info card-mg mb-2">
                    <h3 class="h5 mb-4"><?php _e('Información de Contacto', 'maggiore'); ?></h3>
                    
                    <!-- Email -->
                    <div class="info-item mb-3">
                        <div class="d-flex align-items-start">
                            <svg width="20" height="20" fill="currentColor" class="me-3 mt-1" viewBox="0 0 16 16" style="color: #667eea;">
                                <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                            </svg>
                            <div>
                                <strong class="d-block mb-1"><?php _e('Email', 'maggiore'); ?></strong>
                                <a href="mailto:<?php echo maggiore_get_email(); ?>" class="text-decoration-none">
<?php echo maggiore_get_email(); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Teléfono -->
                    <div class="info-item mb-3">
                        <div class="d-flex align-items-start">
                            <svg width="20" height="20" fill="currentColor" class="me-3 mt-1" viewBox="0 0 16 16" style="color: #667eea;">
                                <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                            </svg>
                            <div>
                                <strong class="d-block mb-1"><?php _e('Teléfono', 'maggiore'); ?></strong>
                                <a href="tel:<?php echo maggiore_get_telefono(); ?>" class="text-decoration-none">
                                    <?php echo maggiore_get_telefono(); ?>
                                </a>
                                
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dirección (opcional) -->
                    <div class="info-item mb-3">
                        <div class="d-flex align-items-start">
                            <svg width="20" height="20" fill="currentColor" class="me-3 mt-1" viewBox="0 0 16 16" style="color: #667eea;">
                                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                            </svg>
                            <div>
                                <strong class="d-block mb-1"><?php _e('Ubicación', 'maggiore'); ?></strong>
                                <span class="text-muted">
                                   <?php echo maggiore_get_direccion(true); ?>
                                </span>
                            </div>
                        </div>
                    </div>


                </div>
                
               
                
                <!-- Redes sociales -->
                <div class="contacto-social card-mg  mt-4">
                    <h3 class="h5 mb-3"><?php _e('Síguenos', 'maggiore'); ?></h3>
                    <div class="social-links">
                  <?php maggiore_social_icons(); ?>
                    </div>
                </div>
                
            </div>
            
            <!-- Columna del formulario -->
            <div class="col-lg-8 order-1 order-lg-2 mb-5">
                
                <div class="card-mg p-md-2 p-0 ">
                    <div class="card-body p-4 p-md-5">
                        
                        <h2 class="h4 mb-4">
                            <?php _e('Envíanos un mensaje', 'maggiore'); ?>
                        </h2>
                        
                        <!-- FORMULARIO DE CONTACTO -->
                        <form id="contactForm" data-origen="Página Contacto">
                            
                            <!-- Campo Honeypot (anti-spam) -->
                            <div style="position: absolute; left: -5000px;" aria-hidden="true">
                                <input type="text" name="website" tabindex="-1" autocomplete="off">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">
                                        <?php _e('Nombre', 'maggiore'); ?> <span class="text-danger">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="nombre" 
                                        name="nombre" 
                                        class="form-control" 
                                        placeholder="<?php _e('Tu nombre completo', 'maggiore'); ?>" 
                                        required
                                    >
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="cargo" class="form-label">
                                        <?php _e('Cargo', 'maggiore'); ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="cargo" 
                                        name="cargo" 
                                        class="form-control" 
                                        placeholder="<?php _e('Ej: Gerente de Marketing', 'maggiore'); ?>"
                                    >
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="correo" class="form-label">
                                        <?php _e('Correo Electrónico', 'maggiore'); ?> <span class="text-danger">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        id="correo" 
                                        name="correo" 
                                        class="form-control" 
                                        placeholder="nombre@empresa.com" 
                                        required
                                    >
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="empresa" class="form-label">
                                        <?php _e('Empresa', 'maggiore'); ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="empresa" 
                                        name="empresa" 
                                        class="form-control" 
                                        placeholder="<?php _e('Nombre de la empresa', 'maggiore'); ?>"
                                    >
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="telefono" class="form-label">
                                        <?php _e('Teléfono Celular', 'maggiore'); ?>
                                    </label>
                                    <input 
                                        type="tel" 
                                        id="telefono" 
                                        name="telefono" 
                                        class="form-control" 
                                        placeholder="+56 9 1234 5678"
                                    >
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="dolorEmpresa" class="form-label">
                                        <?php _e('¿Cuál es tu principal desafío?', 'maggiore'); ?>
                                    </label>
                                    <textarea 
                                        id="dolorEmpresa" 
                                        name="dolorEmpresa" 
                                        class="form-control" 
                                        rows="3" 
                                        placeholder="<?php _e('Cuéntanos qué problema o desafío quieres resolver...', 'maggiore'); ?>"
                                    ></textarea>
                                </div>
                                
                                <div class="col-12 mb-4">
                                    <label for="objetivos" class="form-label">
                                        <?php _e('¿Qué objetivos buscas alcanzar?', 'maggiore'); ?>
                                    </label>
                                    <textarea 
                                        id="objetivos" 
                                        name="objetivos" 
                                        class="form-control" 
                                        rows="3" 
                                        placeholder="<?php _e('Describe las metas que quieres lograr...', 'maggiore'); ?>"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-filter  btn-lg">
                                    <span class="text-white"><?php _e('Enviar Mensaje', 'maggiore'); ?></span>
                                </button>
                            </div>
                            
                            <p class="text-muted small mt-3 mb-0 text-center">
                                <?php _e('Te responderemos en menos de 24 horas hábiles', 'maggiore'); ?>
                            </p>
                        </form>
                        
                    </div>
                </div>
                
            </div>
            
        </div>
        
    </main>
</div>


<?php get_footer(); ?>
