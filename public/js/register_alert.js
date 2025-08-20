document.addEventListener('DOMContentLoaded', () => {
    if (window.registerSuccessMessage) {
        Swal.fire({
            title: 'Registrasi Berhasil 🎉',
            text: window.registerSuccessMessage,
            icon: 'success',
            background: '#f0f9ff',
            color: '#333',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Lanjut Login',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then(() => {
            window.location.href = '/login';
        });
    }
});
