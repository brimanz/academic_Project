// Sistema de Inventario Moderno - JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Animación de entrada para elementos
    animateOnScroll();
    
    // Cerrar modales al hacer clic fuera
    closeModalsOnOutsideClick();
    
    // Cerrar modales con tecla Escape
    closeModalsOnEscape();
    
    // Validación de formularios
    validateForms();
});

// Animación al hacer scroll
function animateOnScroll() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.stat-card, .content-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });
}

// Cerrar modales al hacer clic fuera
function closeModalsOnOutsideClick() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
}

// Cerrar modales con tecla Escape
function closeModalsOnEscape() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
}

// Validación de formularios
function validateForms() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger)';
                    
                    setTimeout(() => {
                        field.style.borderColor = '';
                    }, 3000);
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Por favor complete todos los campos obligatorios', 'error');
            }
        });
    });
}

// Notificación temporal
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `
        <span class="alert-icon">${type === 'success' ? '✓' : '⚠️'}</span>
        ${message}
    `;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100px)';
        notification.style.transition = 'all 0.3s ease';
        
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Formatear número como moneda
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Confirmación personalizada
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Copiar al portapapeles
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copiado al portapapeles', 'success');
    }).catch(() => {
        showNotification('Error al copiar', 'error');
    });
}

// Debounce para búsquedas
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Buscar en tabla
function searchTable(inputId, tableSelector) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const search = debounce(function() {
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll(`${tableSelector} tbody tr`);
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }, 300);
    
    input.addEventListener('input', search);
}

// Ordenar tabla
function sortTable(tableSelector, columnIndex) {
    const table = document.querySelector(tableSelector);
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aText = a.cells[columnIndex].textContent.trim();
        const bText = b.cells[columnIndex].textContent.trim();
        
        const aNum = parseFloat(aText.replace(/[^0-9.-]+/g, ''));
        const bNum = parseFloat(bText.replace(/[^0-9.-]+/g, ''));
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return aNum - bNum;
        }
        
        return aText.localeCompare(bText);
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// Exportar tabla a CSV
function exportTableToCSV(tableSelector, filename) {
    const table = document.querySelector(tableSelector);
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
        cols.forEach(col => {
            let data = col.textContent.trim();
            data = data.replace(/"/g, '""');
            rowData.push(`"${data}"`);
        });
        
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
    
    showNotification('Archivo CSV exportado exitosamente', 'success');
}

// Imprimir página
function printPage() {
    window.print();
}

// Actualizar hora en tiempo real
function updateClock() {
    const clock = document.getElementById('clock');
    if (clock) {
        const now = new Date();
        clock.textContent = now.toLocaleTimeString('es-ES');
    }
}

setInterval(updateClock, 1000);
updateClock();

// Prevenir doble envío de formularios
let formSubmitting = false;

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        if (formSubmitting) {
            return false;
        }
        formSubmitting = true;
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Procesando...';
        }
    });
});

// Resetear flag después de 5 segundos
setInterval(() => {
    formSubmitting = false;
    document.querySelectorAll('button[type="submit"]').forEach(btn => {
        if (btn.disabled && btn.textContent === 'Procesando...') {
            btn.disabled = false;
            btn.textContent = 'Guardar';
        }
    });
}, 5000);