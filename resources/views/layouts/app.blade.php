<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Orens Pro' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .circle-blur {
            filter: blur(80px);
            animation: float 15s infinite ease-in-out;
        }
        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, 50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0, 0) scale(1); }
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-[#F8F9FA] text-[#1A1A1A] min-h-screen overflow-x-hidden font-sans">
    <div class="fixed inset-0 z-[-1] bg-gradient-to-br from-[#FFF5F0] to-white"></div>
    <div class="circle-blur absolute w-[400px] h-[400px] rounded-full bg-orens/15 -top-[100px] -right-[100px] z-[-1]"></div>
    <div class="circle-blur absolute w-[300px] h-[300px] rounded-full bg-orens/10 -bottom-[50px] -left-[50px] z-[-1]" style="animation-delay: -5s;"></div>
    
    @yield('content')
</body>
</html>
