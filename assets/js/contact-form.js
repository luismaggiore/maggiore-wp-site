/**
 * Contact Form Handler (Frontend)
 * 
 * Maneja el envío del formulario de contacto vía AJAX
 */

(function() {
    'use strict';
    
    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        
        const form = document.querySelector('#contactForm');
        
        if (!form) {
            return; // No hay formulario en esta página
        }
        
        const submitButton = form.querySelector('button[type="submit"]');
        const submitText = submitButton.querySelector('.btn-text');
        const originalButtonText = submitText ? submitText.textContent : 'Enviar';
        
        // =====================================================================
        // VALIDACIÓN EN TIEMPO REAL
        // =====================================================================
        
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this);
            });
            
            field.addEventListener('input', function() {
                // Limpiar error mientras escribe
                clearFieldError(this);
            });
        });
        
        /**
         * Validar un campo específico
         */
        function validateField(field) {
            const value = field.value.trim();
            let isValid = true;
            let errorMessage = '';
            
            // Validar si está vacío (y es requerido)
            if (field.hasAttribute('required') && !value) {
                isValid = false;
                errorMessage = 'Este campo es obligatorio';
            }
            
            // Validar email
            if (field.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Ingresa un correo válido';
                }
            }
            
            // Mostrar u ocultar error
            if (!isValid) {
                showFieldError(field, errorMessage);
            } else {
                clearFieldError(field);
            }
            
            return isValid;
        }
        
        /**
         * Mostrar error en un campo
         */
        function showFieldError(field, message) {
            clearFieldError(field); // Limpiar error previo
            
            field.classList.add('is-invalid');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            errorDiv.style.color = '#dc3545';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.style.marginTop = '0.25rem';
            
            field.parentNode.appendChild(errorDiv);
        }
        
        /**
         * Limpiar error de un campo
         */
        function clearFieldError(field) {
            field.classList.remove('is-invalid');
            
            const errorDiv = field.parentNode.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
        
        // =====================================================================
        // CAPTURAR UTM PARAMETERS (si están en la URL)
        // =====================================================================
        
        function getUTMParams() {
            const urlParams = new URLSearchParams(window.location.search);
            return {
                utm_source: urlParams.get('utm_source') || '',
                utm_medium: urlParams.get('utm_medium') || '',
                utm_campaign: urlParams.get('utm_campaign') || ''
            };
        }
        
        // =====================================================================
        // MANEJAR ENVÍO DEL FORMULARIO
        // =====================================================================
        
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validar todos los campos requeridos
            let formIsValid = true;
            requiredFields.forEach(field => {
                if (!validateField(field)) {
                    formIsValid = false;
                }
            });
            
            if (!formIsValid) {
                // Scroll al primer error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                return;
            }
            
            // Deshabilitar botón y mostrar loading
            submitButton.disabled = true;
            if (submitText) {
                submitText.textContent = 'Enviando...';
            }
            
            // Recopilar datos del formulario
            const formData = new FormData(form);
            formData.append('action', 'maggiore_contact_form');
            formData.append('nonce', maggioreData.nonce);
            
            // Agregar UTM params
            const utmParams = getUTMParams();
            formData.append('utm_source', utmParams.utm_source);
            formData.append('utm_medium', utmParams.utm_medium);
            formData.append('utm_campaign', utmParams.utm_campaign);
            
            try {
                // Enviar via AJAX
                const response = await fetch(maggioreData.ajaxUrl, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // ✅ ÉXITO
                    handleSuccess(data.data.message);
                    
                    // Evento para Google Analytics (si está disponible)
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'form_submission', {
                            'form_name': 'contacto_home',
                            'form_location': window.location.pathname
                        });
                    }
                    
                    // Meta Pixel (si está disponible)
                    if (typeof fbq !== 'undefined') {
                        fbq('track', 'Contact', {
                            content_name: 'Formulario Home'
                        });
                    }
                    
                } else {
                    // ❌ ERROR
                    handleError(data.data.message || 'Ocurrió un error. Por favor, intenta de nuevo.');
                }
                
            } catch (error) {
                console.error('Error:', error);
                handleError('Error de conexión. Por favor, verifica tu internet e intenta de nuevo.');
            }
        });
        
        /**
         * Manejar respuesta exitosa
         */
        function handleSuccess(message) {
            // Limpiar formulario
            form.reset();
            
            // Cerrar acordeón opcional si estaba abierto
            const accordion = form.querySelector('.accordion-collapse');
            if (accordion && accordion.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getInstance(accordion);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }
            
            // Mostrar mensaje de éxito
            showNotification(message, 'success');
            
            // Restaurar botón
            submitButton.disabled = false;
            if (submitText) {
                submitText.textContent = originalButtonText;
            }
            
            // Scroll al top del formulario
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        /**
         * Manejar error
         */
        function handleError(message) {
            showNotification(message, 'error');
            
            // Restaurar botón
            submitButton.disabled = false;
            if (submitText) {
                submitText.textContent = originalButtonText;
            }
        }
        
        /**
         * Mostrar notificación
         */
        function showNotification(message, type) {
            // Remover notificación previa si existe
            const existingNotification = form.querySelector('.form-notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Crear nueva notificación
            const notification = document.createElement('div');
            notification.className = 'form-notification alert ' + (type === 'success' ? 'alert-success' : 'alert-danger');
            notification.setAttribute('role', 'alert');
            notification.style.marginTop = '20px';
            notification.style.animation = 'slideInDown 0.3s ease';
            
            const icon = type === 'success' 
                ? '<svg width="20" height="20" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>'
                : '<svg width="20" height="20" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg>';
            
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    ${icon}
                    <div>${message}</div>
                </div>
            `;
            
            // Insertar antes del botón de envío
            const buttonContainer = form.querySelector('.d-grid');
            form.insertBefore(notification, buttonContainer);
            
            // Auto-remover después de 8 segundos
            setTimeout(() => {
                notification.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 8000);
        }
        
        // =====================================================================
        // ANIMACIÓN PARA NOTIFICACIONES
        // =====================================================================
        
        if (!document.querySelector('#contact-form-animations')) {
            const style = document.createElement('style');
            style.id = 'contact-form-animations';
            style.textContent = `
                @keyframes slideInDown {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                @keyframes fadeOut {
                    from {
                        opacity: 1;
                    }
                    to {
                        opacity: 0;
                    }
                }
                .form-control.is-invalid {
                    border-color: #dc3545;
                }
            `;
            document.head.appendChild(style);
        }
        
    });
    
})();
