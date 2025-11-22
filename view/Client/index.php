<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Tunispace</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>

    <!-- EXTERNAL USER CSS -->
    <link rel="stylesheet" href="assets/css/user.css">
</head>

<body>

<!-- Galaxy Background -->
<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>
<?php include __DIR__ . "/navbar.php"; ?>

<!-- ==================== HERO ==================== -->
<section id="hero">
    <section class="univers-story">
        <h1>L’histoire de l’Univers & de l’astronomie</h1>



        <p>
            À travers ce site, nous t’invitons à <strong>voyager dans l’espace et dans le temps</strong> :
            découvrir la naissance des étoiles, explorer les planètes, comprendre les galaxies et plonger au cœur des plus grands
            mystères du cosmos. Prépare-toi à embarquer pour une exploration sans limite… celle de l’Univers.
        </p>
    </section>

</section>


<!-- ==================== DM BUTTON ==================== -->
<div id="dm-btn" class="shadow-lg">
    <i class="fas fa-comment-dots"></i>
</div>

<!-- ==================== DM POPUP ==================== -->
<div id="dm-popup" class="rounded-2xl overflow-hidden">

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-3 flex items-center justify-between drag-handle">
        <div class="flex items-center space-x-3">
            <button id="back-to-list" class="p-1.5 hover:bg-white/20 rounded hidden">
                <i class="fas fa-arrow-left"></i>
            </button>
            <img id="popup-avatar" src="" alt="" class="w-8 h-8 rounded-full"/>
            <div>
                <h3 id="popup-name" class="font-semibold text-sm">Messages</h3>
                <p id="popup-status" class="text-xs opacity-90"></p>
            </div>
        </div>

        <div class="flex space-x-1">
            <button id="minimize-btn" class="p-1.5 hover:bg-white/20 rounded"><i class="fas fa-minus"></i></button>
            <button id="maximize-btn" class="p-1.5 hover:bg-white/20 rounded"><i class="fas fa-expand"></i></button>
            <button id="close-popup" class="p-1.5 hover:bg-white/20 rounded"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <!-- Layout -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Contact List -->
        <div id="contact-sidebar" class="w-80 bg-white dark:bg-gray-800 border-r dark:border-gray-700 overflow-y-auto">
            <div class="p-3 border-b dark:border-gray-700">
                <input type="text" placeholder="Search..." class="w-full px-3 py-2 border dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"/>
            </div>

            <!-- Contacts -->
            <div id="contact-list"></div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col relative">

            <!-- Empty State -->
            <div id="empty-state" class="empty-state">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <h3>Your messages</h3>
                <p>Send a message to start a chat.</p>
                <button id="start-chat-btn">Send message</button>
            </div>

            <!-- Messages -->
            <div id="popup-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900 hidden"></div>

            <!-- Input -->
            <div class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 p-3">
                <div class="flex items-center space-x-2">

                    <button id="popup-attach" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-300">
                        <i class="fas fa-paperclip"></i>
                    </button>

                    <button id="popup-emoji" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-300">
                        <i class="fas fa-smile"></i>
                    </button>

                    <input type="file" id="popup-file" class="hidden" multiple accept="image/*"/>

                    <input type="text" id="popup-input" placeholder="Type a message..." class="flex-1 px-4 py-2 border dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"/>

                    <button id="popup-send" class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>

                <div id="popup-emoji-picker" class="emoji-picker"></div>

                <div id="popup-attachment-preview" class="mt-2 flex flex-wrap gap-2"></div>
            </div>
        </div>
    </div>

    <div class="resize-handle"></div>
</div>
<footer>
    <div class="stardust">
        <!-- Génération de poussière d'étoiles -->
        <span style="top: 20%; left: 10%;"></span>
        <span style="top: 60%; left: 25%;"></span>
        <span style="top: 40%; left: 55%;"></span>
        <span style="top: 75%; left: 70%;"></span>
        <span style="top: 30%; left: 80%;"></span>
        <span style="top: 80%; left: 35%;"></span>
        <span style="top: 45%; left: 90%;"></span>
        <span style="top: 10%; left: 50%;"></span>
    </div>

    <p>© 2025 — <span>Tunispace Galaxy</span>. Beyond Imagination.</p>
</footer>


<!-- EXTERNAL USER JS -->
<script src="assets/js/user.js"></script>

</body>
</html>
