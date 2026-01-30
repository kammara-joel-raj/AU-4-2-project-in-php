<?php 
$pageTitle = "404 // DATA CORRUPTED";
include 'includes/header.php'; 
?>
<style>
    .error-container { 
        height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; 
        background: #000; color: #fff; text-align: center;
    }
    .glitch-404 { font-size: 8rem; font-family: var(--font-varsity); position: relative; }
    .glitch-404::before, .glitch-404::after {
        content: "404"; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #000;
    }
    .glitch-404::before { left: 3px; text-shadow: -2px 0 red; clip-path: inset(0 0 0 0); animation: glitch-anim-1 2s infinite linear alternate-reverse; }
    .glitch-404::after { left: -3px; text-shadow: -2px 0 blue; clip-path: inset(0 0 0 0); animation: glitch-anim-2 2s infinite linear alternate-reverse; }
</style>
</head>
<body>
    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <div class="error-container">
        <div class="glitch-404">404</div>
        <h2 style="font-family: var(--font-tech); margin: 2rem 0; color: #666;">>> ERROR: PAGE_NOT_FOUND_IN_ARCHIVES</h2>
        <p style="max-width: 400px; margin-bottom: 2rem;">The requested file may have been moved, corrupted, or deleted from the university database.</p>
        <a href="index.php"><button class="btn" style="border-color: #fff; color: #fff;">RETURN TO HOME</button></a>
    </div>

    <script>
        // Minimal cursor logic just for this page
        const cursorDot = document.querySelector(".cursor-dot");
        const cursorOutline = document.querySelector(".cursor-outline");
        window.addEventListener("mousemove", function (e) {
            cursorDot.style.left = `${e.clientX}px`; cursorDot.style.top = `${e.clientY}px`;
            cursorOutline.animate({ left: `${e.clientX}px`, top: `${e.clientY}px` }, { duration: 500, fill: "forwards" });
        });
    </script>
</body>
</html>