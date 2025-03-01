@isset($papersize)
    @if ($papersize == 'A4')
        <style>
            body {
                font-size: 11pt;
                font-family: "Tahoma", sans-serif;
                margin: 0;
                padding: 0;
                background: #fff;
            }

            .container {
                width: 210mm;
                min-height: 297mm;
                padding: 20mm;
                margin: 10mm auto;
                background: white;
                border: 1px solid #ccc;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }

            .invoice {
                padding: 15px;
            }

            @media print {
                @page {
                    size: A4;
                    margin: 10mm;
                }

                body,
                .container {
                    width: 100%;
                    height: 100%;
                    margin: 0;
                    padding: 0;
                    box-shadow: none;
                    background: none;
                }

                .no-print {
                    display: none;
                }
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .header img {
                width: 100px;
                height: auto;
            }

            .header h2 {
                margin-top: 10px;
                font-size: 18pt;
            }

            .invoice-info {
                margin-bottom: 20px;
            }

            .invoice-info h5 {
                font-weight: bold;
                margin-bottom: 5px;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }

            .table th,
            .table td {
                border: 1px solid #000;
                padding: 6px;
                text-align: left;
            }

            .table th {
                background: #f8f8f8;
                font-weight: bold;
            }

            .signature {
                margin-top: 20px;
                display: flex;
                justify-content: space-between;
            }

            .signature div {
                width: 40%;
                text-align: center;
                padding-top: 20px;
                border-top: 1px solid #000;
            }
        </style>
    @endif
    @if ($papersize == 'A5')
    <style>
        /* General Styles */
        body {
            font-family: "Tahoma", sans-serif;
            font-size: 9.5pt;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .container {
            width: 140mm; /* Fit within A5 */
            min-height: 200mm;
            padding: 8mm;
            margin: auto;
            background: white;
            border: none;
        }

        /* Header Styling */
        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header img {
            width: 60px;
            height: auto;
        }

        .header h2 {
            margin-top: 5px;
            font-size: 12pt;
        }

        /* Invoice Info */
        .invoice-info {
            margin-bottom: 5px;
            font-size: 9pt;
        }

        .invoice-info h5 {
            font-weight: bold;
            font-size: 9.5pt;
            margin-bottom: 3px;
        }

        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .table th, .table td {
            border: 1px solid #aaa;
            padding: 3px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Signature Box */
        .signature-box {
            margin-top: 5mm;
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
        }

        .signature-box div {
            width: 45%;
            text-align: center;
            padding-top: 10px;
            border-top: 1px solid #000;
        }

        /* Print Formatting */
        @media print {
            @page {
                size: A5 portrait;
                margin: 5mm;
            }

            body, .container {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                background: none;
            }

            .no-print {
                display: none;
            }

            /* Force Scaling to Fit A5 */
            .invoice {
                transform: scale(0.90);
                transform-origin: top left;
            }
        }
    </style>
    @endif
@endisset
