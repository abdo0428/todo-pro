<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Todo Pro' }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      min-height: 100vh;
      background: radial-gradient(circle at 10% 10%, #e9fff9 0%, #f6f8f9 40%), linear-gradient(135deg, #eef4ff 0%, #f6f8f9 100%);
      color: #142127;
      font-family: 'Manrope', sans-serif;
    }
    .brand { font-family: 'Space Grotesk', sans-serif; }
    .glass {
      border: 1px solid rgba(255, 255, 255, .5);
      background: rgba(255, 255, 255, .78);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(18, 34, 40, .09);
    }
  </style>
  @stack('styles')
</head>
<body>
  @yield('content')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
