<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4865c75cef.js" crossorigin="anonymous"></script>
    <script src="{{ mix('/js/app.js') }}" defer></script>
  </head>
  
  <style>
    body{
      font-family: 'Poppins', sans-serif;
    }
  </style>
  <body>
    @inertia
  </body>
</html>