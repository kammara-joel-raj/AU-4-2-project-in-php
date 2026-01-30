<?php 
$pageTitle = "AU | VIRTUAL_FIT_LAB";
include 'includes/header.php'; 
?>
<style>
    body { background-color: #050505; color: #00f3ff; overflow: hidden; }
    .navbar { background: #000; border-bottom: 1px solid #333; }
    .nav-links a { color: #fff; }
    .logo { color: #fff; }
    .crt::before {
        content: " "; display: block; position: absolute;
        top: 0; left: 0; bottom: 0; right: 0;
        background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
        z-index: 2; background-size: 100% 2px, 3px 100%; pointer-events: none;
    }
    .lab-grid { display: grid; grid-template-columns: 300px 1fr 300px; height: 90vh; }
    .panel { border-right: 1px solid #333; padding: 2rem; font-family: var(--font-tech); font-size: 0.8rem; z-index: 5; }
    .panel-right { border-left: 1px solid #333; border-right: none; }
    .scan-window { position: relative; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle, #111 0%, #000 100%); }
    .garment-selector li { padding: 15px; border: 1px solid #333; margin-bottom: 10px; transition: 0.3s; list-style: none; cursor: pointer; }
    .garment-selector li:hover { background: rgba(0, 243, 255, 0.1); border-color: #00f3ff; color: #fff;}
    .status-bar { margin-top: 20px; height: 4px; background: #333; width: 100%; position: relative; }
    .status-fill { height: 100%; background: #00f3ff; width: 45%; box-shadow: 0 0 10px #00f3ff; }
    @keyframes scan { from { top: 0%; } to { top: 100%; } }
</style>
</head>
<body class="crt">

    <!-- MANUALLY ADDED CURSOR ELEMENTS (Required for this page) -->
    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <nav class="navbar">
        <div class="logo">VIRTUAL_FIT_LAB v2.0</div>
        <ul class="nav-links">
            <li><a href="shop.php">RETURN TO SHOP</a></li>
        </ul>
    </nav>

    <div class="lab-grid">
        <div class="panel">
            <h3 style="color: #fff; margin-bottom: 20px;">> SELECT_ITEM</h3>
            <ul class="garment-selector">
                <li>[01] VARSITY_JACKET.obj</li>
                <li>[02] OVERSIZED_TEE.obj</li>
                <li>[03] BUCKET_HAT.obj</li>
            </ul>
            <div style="margin-top: 50px; color: #666;">
                <p>> SYSTEM STATUS: READY</p>
                <p>> GPU: OPTIMIZED</p>
            </div>
        </div>

        <div class="scan-window">
            <div style="border: 1px solid #00f3ff; width: 300px; height: 400px; position: relative; display: flex; align-items: center; justify-content: center;">
                <div style="position: absolute; width: 100%; height: 2px; background: #00f3ff; top: 10%; box-shadow: 0 0 15px #00f3ff; animation: scan 2s infinite alternate;"></div>
                <h1 style="font-size: 5rem; opacity: 0.2; font-family: var(--font-varsity); color: #fff;">AU</h1>
            </div>
        </div>

        <div class="panel panel-right">
            <h3 style="color: #fff; margin-bottom: 20px;">> FIT_METRICS</h3>
            <p>SHOULDER: 44cm</p>
            <div class="status-bar"><div class="status-fill" style="width: 80%"></div></div>
            <br>
            <p>CHEST: 98cm</p>
            <div class="status-bar"><div class="status-fill" style="width: 60%"></div></div>
            <br>
            <button class="btn" style="border-color: #00f3ff; color: #00f3ff; width: 100%; margin-top: 40px;">RENDER_PREVIEW</button>
        </div>
    </div>

    <!-- Manual Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cursorDot = document.querySelector(".cursor-dot");
            const cursorOutline = document.querySelector(".cursor-outline");

            if (cursorDot && cursorOutline) {
                window.addEventListener("mousemove", function (e) {
                    const posX = e.clientX;
                    const posY = e.clientY;
                    cursorDot.style.left = `${posX}px`;
                    cursorDot.style.top = `${posY}px`;
                    cursorOutline.animate({ left: `${posX}px`, top: `${posY}px` }, { duration: 500, fill: "forwards" });
                });
            }

            const clickables = document.querySelectorAll('li, button, a');
            clickables.forEach(el => {
                el.addEventListener('mouseenter', () => { document.body.classList.add('hovering'); });
                el.addEventListener('mouseleave', () => { document.body.classList.remove('hovering'); });
            });
        });
    </script>
</body>
</html>