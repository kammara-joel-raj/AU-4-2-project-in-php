<footer style="background: var(--au-blue); color: var(--paper-white); padding: 4rem; text-align: center; border-top: var(--border-thick);">
        <h2 style="font-size: 5vw; opacity: 0.3;">ANDHRA UNIVERSITY</h2>
        <p style="font-family: var(--font-tech); margin-top: 2rem;">DESIGNED IN VIZAG. WORN WORLDWIDE.</p>
    </footer>

    <!-- GLOBAL SCRIPTS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Cursor Movement Logic
            const cursorDot = document.querySelector(".cursor-dot");
            const cursorOutline = document.querySelector(".cursor-outline");

            if (cursorDot && cursorOutline) {
                window.addEventListener("mousemove", function (e) {
                    const posX = e.clientX;
                    const posY = e.clientY;

                    // Dot follows instantly
                    cursorDot.style.left = `${posX}px`;
                    cursorDot.style.top = `${posY}px`;

                    // Outline follows with slight delay
                    cursorOutline.animate({
                        left: `${posX}px`,
                        top: `${posY}px`
                    }, { duration: 500, fill: "forwards" });
                });
            } else {
                console.warn("Cursor elements not found in DOM.");
            }

            // 2. Cart Toggle Logic
            window.toggleCart = function() { // Made global
                const drawer = document.getElementById('cart-drawer');
                const overlay = document.getElementById('cart-overlay');
                if(drawer && overlay) {
                    drawer.classList.toggle('active');
                    overlay.classList.toggle('active');
                }
            }

            // 3. Hover Effects
            const clickables = document.querySelectorAll('a, button, .product-card, .filter-item, .cart-item, input, select, li');
            clickables.forEach(el => {
                el.addEventListener('mouseenter', () => { document.body.classList.add('hovering'); });
                el.addEventListener('mouseleave', () => { document.body.classList.remove('hovering'); });
            });
        });
    </script>
</body>
</html>