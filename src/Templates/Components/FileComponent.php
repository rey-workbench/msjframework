<?php

namespace MSJFramework\LaravelGenerator\Templates\Components;

class FileComponent
{
    public static function getTemplate(): string
    {
        return <<<'BLADE'
{{-- File Component --}}
<script>
    window.File = (function() {
        'use strict';

        return {
            // Validasi file
            validate: function(file, allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'], maxSize = 5 *
                1024 * 1024) {
                // Cek tipe
                if (!allowedTypes.includes(file.type)) {
                    const types = allowedTypes.map(t => t.split('/')[1].toUpperCase()).join(', ');
                    window.Swal.error('Format Tidak Valid!', `Hanya file ${types} yang diperbolehkan.`);
                    return false;
                }

                // Cek ukuran
                if (file.size > maxSize) {
                    const maxMB = Math.round(maxSize / (1024 * 1024));
                    window.Swal.error('File Terlalu Besar!', `Maksimal ${maxMB}MB.`);
                    return false;
                }

                return true;
            },

            // Preview gambar atau PDF
            preview: function(file, previewId) {
                const preview = document.getElementById(previewId);
                if (file && preview) {
                    if (file.type.startsWith('image/')) {
                        // Preview gambar
                        const reader = new FileReader();
                        reader.onload = e => preview.src = e.target.result;
                        reader.readAsDataURL(file);
                    } else if (file.type === 'application/pdf') {
                        // Preview PDF - tampilkan ikon PDF dan nama file
                        preview.src = "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiByeD0iOCIgZmlsbD0iI0RDMjYyNiIvPgo8dGV4dCB4PSIzMiIgeT0iMzgiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZm9udC13ZWlnaHQ9ImJvbGQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5QREY8L3RleHQ+Cjwvc3ZnPgo=";
                        preview.style.cursor = 'pointer';
                        preview.title = 'Klik untuk membuka PDF: ' + file.name;
                        
                        // Add click handler untuk membuka PDF di tab baru
                        preview.onclick = function() {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const blob = new Blob([e.target.result], {type: 'application/pdf'});
                                const url = URL.createObjectURL(blob);
                                window.open(url, '_blank');
                            };
                            reader.readAsArrayBuffer(file);
                        };
                    }
                }
            },

            // Setup file input lengkap
            setup: function(inputId, previewId, allowedTypes, maxSize) {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('change', e => {
                        const file = e.target.files[0];
                        if (file) {
                            if (this.validate(file, allowedTypes, maxSize)) {
                                this.preview(file, previewId);
                            } else {
                                e.target.value = '';
                                const preview = document.getElementById(previewId);
                                if (preview) preview.src = "{{ asset('storage/noimage.png') }}";
                            }
                        }
                    });
                }
            },

            // Format ukuran file
            formatSize: function(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        };
    })();
</script>
BLADE;
    }
}
