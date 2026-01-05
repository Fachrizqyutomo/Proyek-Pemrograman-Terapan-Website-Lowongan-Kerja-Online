<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Loading...</title>

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #111;
            color: white;
            font-family: Arial, sans-serif;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 6px solid #444;
            border-top-color: #09f;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .text {
            margin-top: 20px;
            font-size: 18px;
            text-align: center;
            opacity: 0.8;
        }
    </style>

    <script>
        // otomatis pindah ke dashboard asli
        setTimeout(function() {
            window.location.href = "index.php";
        }, 2000);
    </script>
</head>
<body>

<div>
    <div class="loader"></div>
    <div class="text">Loading...</div>
</div>

</body>
</html>
