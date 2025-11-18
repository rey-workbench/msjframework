<script>
    
    const SweetAlert2 = window.Swal;

    window.Swal = (function() {
        'use strict';

        return {
            
            init: function() {
                @if (Session::has('message'))
                    @if (Session::get('class') === 'success')
                        this.success('Berhasil!', '{{ Session::get('message') }}');
                    @elseif (Session::get('class') === 'danger' || Session::get('class') === 'error')
                        this.error('Error!', '{{ Session::get('message') }}');
                    @elseif (Session::get('class') === 'warning')
                        this.warning('Peringatan!', '{{ Session::get('message') }}');
                    @else
                        this.info('Info!', '{{ Session::get('message') }}');
                    @endif
                @endif
            },

            
            loading: function(title = 'Loading...', text = 'Mohon tunggu sebentar') {
                return SweetAlert2.fire({
                    title: title,
                    text: text,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => SweetAlert2.showLoading()
                });
            },

            
            success: function(title = 'Berhasil!', text = '', timer = 1500) {
                return SweetAlert2.fire({
                    title: title,
                    text: text,
                    icon: 'success',
                    confirmButtonColor: '#028284',
                    timer: timer,
                    timerProgressBar: timer > 0,
                    showConfirmButton: timer <= 0
                });
            },

            
            error: function(title = 'Error!', text = 'Terjadi kesalahan') {
                return SweetAlert2.fire({
                    title: title,
                    text: text,
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            },

            
            warning: function(title = 'Peringatan!', text = '', timer = 4000) {
                return SweetAlert2.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    confirmButtonColor: '#f39c12',
                    timer: timer,
                    timerProgressBar: timer > 0,
                    showConfirmButton: timer <= 0
                });
            },

            
            info: function(title = 'Info!', text = '', timer = 3000) {
                return SweetAlert2.fire({
                    title: title,
                    text: text,
                    icon: 'info',
                    confirmButtonColor: '#028284',
                    timer: timer,
                    timerProgressBar: timer > 0,
                    showConfirmButton: timer <= 0
                });
            },

            
            confirm: function(title, text, confirmText = 'Ya', cancelText = 'Batal') {
                return SweetAlert2.fire({
                    title: title,
                    html: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: cancelText,
                    confirmButtonColor: '#028284',
                    cancelButtonColor: '#6c757d'
                });
            },

            
            confirmDelete: function(itemName = 'data') {
                return this.confirm('Konfirmasi Hapus',
                    `Apakah Anda yakin ingin menghapus ${itemName} ini?`, 'Ya, Hapus!', 'Batal');
            },

            
            close: function() {
                SweetAlert2.close();
            },

            // Show image
            showImage: function(imageSrc, title = 'Preview Gambar') {
                if (!imageSrc || imageSrc.includes('noimage.png')) {
                    this.warning('Tidak Ada Gambar', 'Gambar tidak tersedia');
                    return;
                }

                return SweetAlert2.fire({
                    title: title,
                    imageUrl: imageSrc,
                    imageWidth: 600,
                    imageHeight: 400,
                    imageAlt: title,
                    showCloseButton: true,
                    showConfirmButton: false,
                    customClass: {
                        image: 'img-fluid'
                    }
                });
            },

            // Show PDF
            showPDF: function(pdfSrc, title = 'Preview PDF') {
                if (!pdfSrc) {
                    this.warning('Tidak Ada File', 'File PDF tidak tersedia');
                    return;
                }

                return SweetAlert2.fire({
                    title: title,
                    html: `<iframe src="${pdfSrc}" width="100%" height="500px" style="border: none;"></iframe>`,
                    width: '80%',
                    showCloseButton: true,
                    showConfirmButton: false
                });
            },

            // Cleanup stuck Bootstrap modals
            cleanup: function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    'overflow': '',
                    'padding-right': ''
                });
                $('.modal').modal('hide');
            },

            // Show info dengan HTML
            showInfo: function(title, htmlContent, width = '600px') {
                return SweetAlert2.fire({
                    title: title,
                    html: htmlContent,
                    width: width,
                    showCloseButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#028284'
                });
            },

            // Confirm dengan input
            confirmWithInput: function(title, inputLabel, placeholder = '') {
                return SweetAlert2.fire({
                    title: title,
                    input: 'text',
                    inputLabel: inputLabel,
                    inputPlaceholder: placeholder,
                    showCancelButton: true,
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#028284',
                    inputValidator: (value) => {
                        if (!value) return 'Input tidak boleh kosong!';
                    }
                });
            }
        };
    })();

    
    $(document).ready(() => window.Swal.init());
</script>
