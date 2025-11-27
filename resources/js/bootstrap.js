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
