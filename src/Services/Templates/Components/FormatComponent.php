<?php

namespace App\Services\Templates\Components;

class FormatComponent
{
    public static function getTemplate(): string
    {
        return <<<'BLADE'
{{-- Format Component --}}
<script>
    window.Format = (function() {
        'use strict';

        return {
            // Format currency
            currency: function(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(amount || 0));
            },

            // Parse currency
            parseCurrency: function(str) {
                return parseFloat(String(str).replace(/[^\d]/g, '')) || 0;
            },

            // Format tanggal
            date: function(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('id-ID');
            },

            // Format periode YYYYMM
            period: function(periode) {
                if (!periode || periode.length !== 6) return periode;
                const year = periode.substring(0, 4);
                const month = periode.substring(4, 6);
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov',
                    'Des'
                ];
                return months[parseInt(month) - 1] + ' ' + year;
            },

            // Nama bulan
            monthName: function(monthNumber) {
                const months = {
                    1: 'Januari',
                    2: 'Februari',
                    3: 'Maret',
                    4: 'April',
                    5: 'Mei',
                    6: 'Juni',
                    7: 'Juli',
                    8: 'Agustus',
                    9: 'September',
                    10: 'Oktober',
                    11: 'November',
                    12: 'Desember'
                };
                return months[parseInt(monthNumber)] || monthNumber;
            },

            // Format number
            number: function(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            },

            // Format NIK
            nik: function(nik) {
                if (!nik) return '';
                const clean = nik.replace(/\s/g, '');
                return clean.replace(/(\d{4})(\d{4})(\d{4})(\d{4})/, '$1 $2 $3 $4');
            },

            // Format phone
            phone: function(phone) {
                if (!phone) return '';
                const clean = phone.replace(/\D/g, '');
                if (clean.startsWith('62')) {
                    return '+62 ' + clean.substring(2).replace(/(\d{3})(\d{4})(\d{4})/, '$1-$2-$3');
                } else if (clean.startsWith('0')) {
                    return clean.replace(/(\d{4})(\d{4})(\d{4})/, '$1-$2-$3');
                }
                return phone;
            },

            // Capitalize
            capitalize: function(str) {
                if (!str) return '';
                return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
            },

            // Truncate
            truncate: function(text, maxLength = 50) {
                if (!text || text.length <= maxLength) return text;
                return text.substring(0, maxLength) + '...';
            }
        };
    })();
</script>
BLADE;
    }
}
