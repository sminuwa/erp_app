@isset($papersize)
    @if ($papersize == 'A4')
        <style>
            /* Print-specific styles */
            @media print {
                @page {
                    size: A4;
                    margin: 10mm;
                }

                body {
                    background: #fff;
                    font-size: 12px;
                    margin: 0;
                    padding: 0;
                }

                .container-fluid {
                    width: 100%;
                    max-width: 210mm;
                    margin: auto;
                }

                .invoice {
                    padding: 15px;
                    border: 1px solid #ddd;
                    max-width: 210mm;
                    margin: auto;
                    background: #fff;
                }

                .table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .table th,
                .table td {
                    border: 1px solid #ddd;
                    padding: 5px;
                    text-align: left;
                }

                .table th {
                    background-color: #f8f9fa;
                    font-weight: bold;
                }

                .text-center {
                    text-align: center;
                }

                .signature-line {
                    margin-top: 30px;
                    text-align: left;
                    font-weight: bold;
                }
            }
        </style>
    @endif
    @if ($papersize == 'A5')
        <style>
            /* Print-specific styles */
            @media print {
                @page {
                    size: A5;
                    margin: 10mm;
                }

                body {
                    background: #fff;
                    font-size: 12px;
                    margin: 0;
                    padding: 0;
                }

                .container-fluid {
                    width: 100%;
                    max-width: 210mm;
                    margin: auto;
                }

                .invoice {
                    padding: 15px;
                    border: 1px solid #ddd;
                    max-width: 210mm;
                    margin: auto;
                    background: #fff;
                }

                .table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .table th,
                .table td {
                    border: 1px solid #ddd;
                    padding: 5px;
                    text-align: left;
                }

                .table th {
                    background-color: #f8f9fa;
                    font-weight: bold;
                }

                .text-center {
                    text-align: center;
                }

                .signature-line {
                    margin-top: 30px;
                    text-align: left;
                    font-weight: bold;
                }
            }
        </style>
    @endif
@endisset
