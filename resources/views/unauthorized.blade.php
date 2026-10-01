<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: sans-serif;
        }
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        .error-code {
            font-size: 5rem;
            font-weight: 700;
            color: #343a40;
            line-height: 1;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 1.5rem;
            color: #495057;
            margin-bottom: 1.5rem;
            font-weight: normal;
        }
        .error-message {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 1.1rem;
            line-height: 1.5;
        }
        .btn-back {
            background: #343a40;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.2s;
            margin: 0 0.5rem;
        }
        .btn-back:hover {
            background: #212529;
            color: white;
        }
        .btn-home {
            background: #f8f9fa;
            color: #343a40;
            border: 1px solid #dee2e6;
        }
        .btn-home:hover {
            background: #e9ecef;
            color: #212529;
        }
        .divider {
            display: inline-block;
            color: #dee2e6;
            margin: 0 0.5rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">401</div>
        <h1 class="error-title">Unauthorized Access</h1>
        <p class="error-message">
            You do not have sufficient permissions to access this page. 
            Please contact your administrator if you believe this is an error.
        </p>
        <div>
            <a href="{{ session('return_url', url()->previous()) }}" class="btn-back">Go Back</a>
            <span class="divider">or</span>
            <a href="{{ url('/') }}" class="btn-back btn-home">Go Home</a>
        </div>
    </div>
</body>
</html>