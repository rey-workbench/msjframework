<?php

namespace App\Services\Templates\Components;

class InputComponent
{
    public static function getTemplate(): string
    {
        return <<<'BLADE'
{{-- Input Component --}}
<script>
    window.Input = (function() {
        'use strict';

        return {
            // Numbers only
            numbersOnly: function(inputIds) {
                inputIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', function() {
                            this.value = this.value.replace(/\D/g, '');
                        });
                    }
                });
            },

            // Currency format
            currency: function(inputIds) {
                inputIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', function() {
                            let value = this.value.replace(/\D/g, '');
                            if (value) {
                                this.value = window.Format.currency(parseInt(value));
                            }
                        });
                    }
                });
            },

            // NIK format
            nik: function(inputIds) {
                inputIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', function() {
                            let value = this.value.replace(/\D/g, '');
                            if (value.length > 16) value = value.substring(0, 16);
                            this.value = window.Format.nik(value);
                        });
                    }
                });
            },

            // Phone format
            phone: function(inputIds) {
                inputIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', function() {
                            this.value = window.Format.phone(this.value);
                        });
                    }
                });
            },

            // Uppercase
            uppercase: function(inputIds) {
                inputIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', function() {
                            this.value = this.value.toUpperCase();
                        });
                    }
                });
            },

            // Debounce
            debounce: function(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            },

            // Copy to clipboard
            copyToClipboard: function(text, successMsg = 'Teks berhasil disalin') {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(() => {
                        window.Swal.success('Berhasil!', successMsg, 1500);
                    });
                } else {
                    // Fallback
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    window.Swal.success('Berhasil!', successMsg, 1500);
                }
            }
        };
    })();
</script>
BLADE;
    }
}
