import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// SweetAlert2 Integration
import Swal from 'sweetalert2';
window.Swal = Swal;

// Default SweetAlert2 configuration
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

window.Toast = Toast;

// SweetAlert2 Thai language support
Swal.updateDefaults({
     confirmButtonText: 'ตกลง',
     cancelButtonText: 'ยกเลิก',
     customClass: {
         confirmButton: 'btn btn-primary me-2',
         cancelButton: 'btn btn-secondary'
     }
});

// Theme switching functionality
class ThemeManager {
     constructor() {
         this.currentTheme = this.getStoredTheme() || 'light';
         this.init();
     }

     init() {
         this.applyTheme(this.currentTheme);
         this.createThemeToggle();
         this.bindEvents();
     }

     getStoredTheme() {
         return localStorage.getItem('theme');
     }

     storeTheme(theme) {
         localStorage.setItem('theme', theme);
     }

     applyTheme(theme) {
         document.documentElement.setAttribute('data-theme', theme);
         this.currentTheme = theme;
         this.updateToggleButton(theme);
     }

     toggleTheme() {
         const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
         this.applyTheme(newTheme);
         this.storeTheme(newTheme);

         // Add transition class for smooth switching
         document.body.classList.add('theme-transition');
         setTimeout(() => {
             document.body.classList.remove('theme-transition');
         }, 300);
     }

     createThemeToggle() {
        // Hide fallback button if it exists
        const fallbackButton = document.getElementById('theme-toggle-fallback');
        if (fallbackButton) {
            fallbackButton.style.display = 'none';
        }

        // Create theme toggle button
        const toggleButton = document.createElement('button');
        toggleButton.id = 'theme-toggle';
        toggleButton.className = 'theme-toggle-btn';
        toggleButton.setAttribute('aria-label', 'Toggle theme');
        toggleButton.innerHTML = `
            <svg class="theme-icon light-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" fill="currentColor"/>
            </svg>
            <svg class="theme-icon dark-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" fill="currentColor"/>
            </svg>
        `;

        // Add styles for the toggle button
        const style = document.createElement('style');
        style.textContent = `
            .theme-toggle-btn {
                position: fixed;
                bottom: 2rem;
                left: 2rem;
                width: 60px;
                height: 60px;
                background: #ff6b35 !important;
                border: 3px solid #fff !important;
                border-radius: 50% !important;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999 !important;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(255, 107, 53, 0.4);
                color: white;
            }
            .theme-toggle-btn:hover {
                background: #e85d2a !important;
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(255, 107, 53, 0.6);
            }
            .theme-icon {
                transition: opacity 0.3s ease;
            }
            .light-icon {
                opacity: 1;
            }
            .dark-icon {
                opacity: 0;
                position: absolute;
            }
            [data-theme="dark"] .light-icon {
                opacity: 0;
            }
            [data-theme="dark"] .dark-icon {
                opacity: 1;
            }
        `;
        document.head.appendChild(style);

        document.body.appendChild(toggleButton);
        this.toggleButton = toggleButton;
        this.updateToggleButton(this.currentTheme);

        // Debug: Log that button was created
        console.log('Advanced theme toggle button created');
    }

     updateToggleButton(theme) {
         if (this.toggleButton) {
             this.toggleButton.setAttribute('aria-label',
                 theme === 'light' ? 'Switch to dark theme' : 'Switch to light theme'
             );
         }
     }

     bindEvents() {
         if (this.toggleButton) {
             this.toggleButton.addEventListener('click', () => this.toggleTheme());
         }
     }
}

// Import additional modules
import './components/cart.js';
import './components/product.js';
import './utils/address-selector.js';
import './pages/payment.js';
import './pages/checkout.js';
import './pages/admin-dashboard.js';

// Initialize theme manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
      window.themeManager = new ThemeManager();
});
