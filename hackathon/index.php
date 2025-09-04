<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hackathon Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="text-white">
    <?php include 'navbar.php' ?>
    
    <header class="container mx-auto text-center py-32 px-4 relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,#0EA5E9_0%,rgba(0,0,0,0)_70%)] opacity-20"></div>
        <h1 class="text-6xl font-extrabold leading-tight bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">Redefining Hackathon Experiences</h1>
        <p class="mt-6 text-xl text-gray-300">Participate, organize, and connect with the global tech community.</p>
     </header>

    <section class="glass py-24 px-4">
        <div class="container mx-auto grid grid-cols-1 sm:grid-cols-2 gap-8">
            <div class="p-8 rounded-2xl bg-gradient-to-br from-slate-800/50 to-slate-900/50 border border-slate-700/50">
                <h2 class="text-2xl font-bold mb-4 text-cyan-400">For Participants</h2>
                <p class="mb-4 text-gray-300">Discover hackathons tailored to your skills and interests.</p>
                <a href="student_register.php" class="text-cyan-400 hover:text-cyan-300">Learn More →</a>
            </div>
            <div class="p-8 rounded-2xl bg-gradient-to-br from-slate-800/50 to-slate-900/50 border border-slate-700/50">
                <h2 class="text-2xl font-bold mb-4 text-blue-400">For Organizers</h2>
                <p class="mb-4 text-gray-300">Create, manage, and host your events seamlessly.</p>
                <a href="organizer_register.php" class="text-blue-400 hover:text-blue-300">Learn More →</a>
            </div>
        </div>
    </section>

    <section class="container mx-auto py-24 px-4">
        <h2 class="text-center text-4xl font-bold mb-16 bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">Why Hackathon Hub?</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="glass p-8 rounded-2xl">
                <h3 class="text-xl font-bold mb-4 text-cyan-400">Diverse Opportunities</h3>
                <p class="text-gray-300">Explore hackathons from beginner to expert levels across domains.</p>
            </div>
            <div class="glass p-8 rounded-2xl">
                <h3 class="text-xl font-bold mb-4 text-blue-400">Global Connections</h3>
                <p class="text-gray-300">Network with developers, designers, and tech enthusiasts worldwide.</p>
            </div>
            <div class="glass p-8 rounded-2xl">
                <h3 class="text-xl font-bold mb-4 text-cyan-400">Innovative Platform</h3>
                <p class="text-gray-300">Utilize advanced tools for event management and performance tracking.</p>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900/50 py-8">
        <div class="container mx-auto text-center">
            <p class="text-gray-400">© 2025 Narayana Hackathon. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>