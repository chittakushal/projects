<nav class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="index.php" class="flex items-center">
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-500 to-purple-600 text-transparent bg-clip-text">Narayana Hackathon</span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-4">
                <a href="./index.php" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium transition-colors duration-150">Home</a>
                
                <?php if (isset($_SESSION['org_id'])): ?>
                    <a href="manage_hackathons.php" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">Manage Hackathons</a>
                    <a href="create_hackathon.php" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">Create Hackathon</a>
                    <a href="logout.php" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">Log Out</a>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <a href="hackathons.php" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">Hackathons</a>
                    <a href="my_hackathons.php" class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium">My Hackathons</a>
                    <a href="logout.php" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">Log Out</a>
                <?php else: ?>
                    <a href="org_login.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">Organization Login</a>
                    <a href="student_login.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-800 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">Student Login</a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <div class="lg:hidden">
                <button id="menu-toggle" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="lg:hidden hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 bg-gray-900">
            <a href="./index.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium">Home</a>
            
            <?php if (isset($_SESSION['org_id'])): ?>
                <a href="manage_hackathons.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium">Manage Hackathons</a>
                <a href="create_hackathon.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium">Create Hackathon</a>
                <a href="logout.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium bg-indigo-600 rounded-md mt-2">Log Out</a>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <a href="hackathons.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium">Hackathons</a>
                <a href="my_hackathons.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium">My Hackathons</a>
                <a href="logout.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium bg-indigo-600 rounded-md mt-2">Log Out</a>
            <?php else: ?>
                <a href="org_login.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium bg-indigo-600 rounded-md mt-2">Organization Login</a>
                <a href="student_login.php" class="text-gray-800 hover:bg-gray-50 block px-3 py-2 text-base font-medium bg-white rounded-md mt-2">Student Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
document.getElementById('menu-toggle').addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
});
</script>