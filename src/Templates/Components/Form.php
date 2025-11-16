<?php

namespace MSJFramework\LaravelGenerator\Templates\Components;

class Form
{
    public static function getTemplate(): string
    {
        return <<<'BLADE'
{{-- Form Component --}}
<script>
    window.Form = (function() {
        'use strict';

        return {
            // Validasi field required
            validateRequired: function(fieldNames) {
                let isValid = true;
                let firstInvalid = null;

                fieldNames.forEach(name => {
                    const field = document.querySelector(`[name="${name}"]`);
                    if (!field || !field.value.trim()) {
                        isValid = false;
                        if (!firstInvalid) firstInvalid = field;
                        if (field) field.classList.add('is-invalid');
                    } else if (field) {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid && firstInvalid) firstInvalid.focus();
                return isValid;
            },

            // Clear validasi
            clearValidation: function(formSelector = 'form') {
                document.querySelectorAll(`${formSelector} .is-invalid`).forEach(field => {
                    field.classList.remove('is-invalid');
                });
            },

            // Reset form
            reset: function(formSelector) {
                const form = document.querySelector(formSelector);
                if (form) {
                    form.reset();
                    this.clearValidation(formSelector);
                }
            },

            // Submit dengan konfirmasi
            submitWithConfirm: function(formSelector, title, text) {
                return window.Swal.confirm(title, text).then(result => {
                    if (result.isConfirmed) {
                        window.Swal.loading('Memproses...', 'Mohon tunggu sebentar');
                        document.querySelector(formSelector).submit();
                    }
                });
            },

            // Validasi email
            isValidEmail: function(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            },

            // Validasi phone
            isValidPhone: function(phone) {
                return /^(\+62|62|0)[0-9]{9,13}$/.test(phone.replace(/\s/g, ''));
            },

            // Validasi NIK
            isValidNIK: function(nik) {
                return /^\d{16}$/.test(nik);
            }
        };
    })();
</script>
BLADE;
    }
}
