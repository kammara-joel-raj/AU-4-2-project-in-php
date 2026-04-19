<?php require_once __DIR__ . '/bootstrap.php'; ?>
<footer style="background: var(--au-blue); color: var(--paper-white); padding: 4rem; text-align: center; border-top: var(--border-thick);">
        <div style="display: flex; justify-content: center; gap: 2rem; margin-bottom: 3rem; flex-wrap: wrap; font-family: var(--font-tech); text-transform: uppercase;">
            <a href="about.php" style="color: var(--au-gold); text-decoration: none;">About</a>
            <a href="locator.php" style="color: var(--au-gold); text-decoration: none;">Store Locator</a>
            <a href="blog.php" style="color: var(--au-gold); text-decoration: none;">Blog</a>
            <a href="faq.php" style="color: var(--au-gold); text-decoration: none;">Support / FAQ</a>
        </div>

        <h2 style="font-size: 5vw; opacity: 0.3;">ANDHRA UNIVERSITY</h2>
        <p style="font-family: var(--font-tech); margin-top: 2rem;">DESIGNED IN VIZAG. WORN WORLDWIDE.</p>
    </footer>

    <script>
        window.AU_APP = {
            csrfToken: "<?= h(csrf_token()) ?>",
            isLoggedIn: <?= is_logged_in() ? 'true' : 'false' ?>
        };

        window.auPost = function(url, data, extraOptions = {}) {
            let body = data instanceof FormData ? data : new FormData();

            if (!(data instanceof FormData)) {
                Object.entries(data || {}).forEach(([key, value]) => {
                    body.append(key, value);
                });
            }

            if (!body.has('csrf_token')) {
                body.append('csrf_token', window.AU_APP.csrfToken);
            }

            return fetch(url, Object.assign({
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body
            }, extraOptions)).then(async response => {
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw payload;
                }
                return payload;
            });
        };

        document.addEventListener("DOMContentLoaded", function() {
            const cursorDot = document.querySelector(".cursor-dot");
            const cursorOutline = document.querySelector(".cursor-outline");

            if (cursorDot && cursorOutline) {
                window.addEventListener("mousemove", function (e) {
                    const posX = e.clientX;
                    const posY = e.clientY;

                    cursorDot.style.left = `${posX}px`;
                    cursorDot.style.top = `${posY}px`;

                    cursorOutline.animate({
                        left: `${posX}px`,
                        top: `${posY}px`
                    }, { duration: 500, fill: "forwards" });
                });
            }

            window.toggleCart = function() {
                const drawer = document.getElementById('cart-drawer');
                const overlay = document.getElementById('cart-overlay');
                if (drawer && overlay) {
                    drawer.classList.toggle('active');
                    overlay.classList.toggle('active');
                }
            };

            const clickables = document.querySelectorAll('a, button, .product-card, .filter-item, .cart-item, input, select, li');
            clickables.forEach(el => {
                el.addEventListener('mouseenter', () => { document.body.classList.add('hovering'); });
                el.addEventListener('mouseleave', () => { document.body.classList.remove('hovering'); });
            });
        });
    </script>
</body>
</html>
