<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt Preview</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        #receipt-container {
            width: 80mm;
            background: white;
            padding: 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 0 auto;
        }
        @media print {
            body {
                padding: 0;
                background: none;
            }
            #receipt-container {
                box-shadow: none;
            }
            #print-button {
                display: none;
            }
        }
        #print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        #print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div id="receipt-container">
        @include('components.pos.receipt-template')
    </div>
    <button id="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Print Receipt
    </button>

    <script>
        window.resizeTo(400, 600);
        
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
