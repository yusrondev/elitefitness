<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Member</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .card {
            width: 54mm;
            height: 80mm;
            border: 1px solid #000;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
            background-image: url('{{ asset("assets/images/bg_barcodee.jpeg") }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
        }

        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            text-align: center;
        }

        .content h2 {
            margin: 0;
            font-size: 10px;
            font-weight: bold;
        }

        .content p {
            margin: 5px 0;
            font-size: 8px;
        }

        .barcode {
            display: inline-block;
            padding: 6px;
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(0,0,0,0.3);
            margin-top: 6px;
        }
        
        .barcode img {
            width: 100%;
            max-width: 170px;
            height: auto;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background-color: #fff;
            }

            .card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="content">
            <h2 style="color:white;font-size:20px;margin-top:0px;">{{ $member->name }}</h2>
            <div class="barcode">
                <img src="{{ asset('assets/images/barcode/' . $member->qr_filename) }}" 
                 alt="QR Code {{ $member->barcode }}" 
                 class="img-fluid" 
                 style="border: 2px solid #000; padding: 10px; background-color: #fff; width: 50%;">
            </div>
        </div>
    </div>

    <!-- Input untuk scan barcode (jika tetap dibutuhkan) -->
    <input type="text" id="barcode_input" style="opacity:0;position:absolute;" autofocus>

    <script>
        function checkBarcode() {
            var barcodeInput = document.getElementById("barcode_input");
            let barcodeValue = barcodeInput.value.trim();

            if (barcodeValue !== "") {
                var newUrl = "{{ url('/InformationMember') }}/" + encodeURIComponent(barcodeValue);
                window.location.replace(newUrl);
                barcodeInput.value = "";
            }
        }

        window.onload = function () {
            var barcodeInput = document.getElementById("barcode_input");
            barcodeInput.focus();

            // Auto fokus ulang
            setInterval(() => {
                if (document.activeElement !== barcodeInput) {
                    barcodeInput.focus();
                }
            }, 500);

            // Auto cetak saat halaman selesai dimuat
            window.print();
        };

        setInterval(checkBarcode, 500);
    </script>
</body>
</html>