@include('components.swal')
@include('components.form')
@include('components.file')
@include('components.format')
@include('components.input')

<script>
    window.deleteData = function(event, name, msg, hasUser = false) {
        event.preventDefault();

        let message = `Apakah Anda Yakin ${msg} Data ${name} ini?`;
        let warningText = '';

        if (msg === 'Non Aktifkan' && hasUser) {
            warningText = '\n\n⚠️ PERHATIAN: Akses login anggota juga akan ikut dinonaktifkan!';
        } else if (msg === 'Aktifkan' && hasUser) {
            warningText = '\n\nℹ️ INFO: Akses login anggota juga akan ikut diaktifkan.';
        }

        window.Swal.confirm('Konfirmasi', message + warningText, `Ya, ${msg}`, 'Batal').then(result => {
            if (result.isConfirmed) {
                event.target.closest('form').submit();
            }
        });
    };

    window.showSuccessMessage = function(title, message, details = null) {
        return window.Swal.success(title, message);
    };

    window.showErrorMessage = function(title, message, details = null) {
        return window.Swal.error(title, message);
    };

    window.showWarningMessage = function(title, message, details = null) {
        return window.Swal.warning(title, message);
    };

    window.showImageModal = function(imageSrc, title) {
        return window.Swal.showImage(imageSrc, title);
    };
</script>
