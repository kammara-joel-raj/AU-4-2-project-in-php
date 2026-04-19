<?php
require_once 'includes/bootstrap.php';

$selectedProductId = (int) ($_GET['product_id'] ?? 0);
$catalog = fetch_products_paginated($pdo, [
    'category' => 'all',
    'sort' => 'latest',
    'page' => 1,
    'per_page' => 50,
])['items'];

$selectedProduct = null;
foreach ($catalog as $product) {
    if ((int) $product['id'] === $selectedProductId) {
        $selectedProduct = $product;
        break;
    }
}

if (!$selectedProduct && !empty($catalog)) {
    $selectedProduct = $catalog[0];
    $selectedProductId = (int) $selectedProduct['id'];
}

$pageTitle = 'AU | VIRTUAL_FIT_LAB';
include 'includes/header.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<style>
    body { background-color: #050505; color: #00f3ff; overflow-x: hidden; }
    .navbar { background: #000; border-bottom: 1px solid #333; }
    .nav-links a, .logo { color: #fff; }
    .lab-container { display: grid; grid-template-columns: 320px 1fr 320px; min-height: calc(100vh - 100px); }
    .panel { border-right: 1px solid #333; padding: 2rem; background: #0a0a0a; overflow-y: auto; max-height: calc(100vh - 100px); position: relative; z-index: 5; }
    .panel-right { border-left: 1px solid #333; border-right: none; }
    .panel-center { display: flex; flex-direction: column; align-items: center; gap: 1.25rem; justify-content: flex-start; background: radial-gradient(circle at center, #111 0%, #000 100%); padding: 2rem; }
    .garment-item { padding: 15px; border: 1px solid #333; margin-bottom: 10px; cursor: pointer; display: flex; gap: 12px; transition: 0.3s; }
    .garment-item:hover, .garment-item.selected { border-color: #00f3ff; background: rgba(0, 243, 255, 0.1); }
    .garment-thumb { width: 52px; height: 52px; background: #222; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
    .upload-area { width: 100%; max-width: 860px; min-height: 150px; border: 2px dashed #333; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; background: #0a0a0a; overflow: hidden; padding: 1.5rem; text-align: center; }
    .upload-area input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .upload-area img { max-width: 100%; max-height: 220px; object-fit: contain; }
    .lab-actions { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; width: 100%; max-width: 860px; }
    .tryon-btn { width: 100%; padding: 16px; background: transparent; border: 2px solid #00f3ff; color: #00f3ff; font-family: var(--font-tech); font-weight: bold; cursor: pointer; transition: 0.3s; }
    .tryon-btn:hover:not(:disabled) { background: #00f3ff; color: #000; box-shadow: 0 0 20px rgba(0, 243, 255, 0.5); }
    .tryon-btn:disabled { opacity: 0.45; cursor: not-allowed; }
    .metrics-card { border: 1px solid #333; padding: 1rem; margin-bottom: 1rem; }
    #canvasContainer { display: none; border: 2px solid #00f3ff; box-shadow: 0 0 20px rgba(0, 243, 255, 0.2); background: #111; max-width: 100%; position: relative; }
    #filterControls { display: none; width: 100%; max-width: 860px; background: rgba(0,0,0,0.92); border: 1px solid #00f3ff; padding: 14px; font-family: var(--font-tech); }
    .status-line { width: 100%; max-width: 860px; min-height: 24px; font-family: var(--font-tech); color: #00f3ff; }
    .subtle { color: #888; font-size: 0.8rem; }
    @media (max-width: 1100px) {
        .lab-container { grid-template-columns: 1fr; }
        .panel { border: none; border-bottom: 1px solid #333; max-height: none; }
        .lab-actions { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 680px) {
        .lab-actions { grid-template-columns: 1fr; }
    }
</style>
</head>

<body>
    <div class="cursor-dot"></div><div class="cursor-outline"></div>

    <nav class="navbar">
        <div class="logo glitch" data-text="VIRTUAL_FIT_LAB">VIRTUAL_FIT_LAB v2.5</div>
        <ul class="nav-links">
            <li><a href="shop.php">EXIT LAB</a></li>
        </ul>
    </nav>

    <div class="lab-container">
        <div class="panel">
            <h3 style="font-family: var(--font-tech); margin-bottom: 1.5rem;">&gt; SELECT_ASSET</h3>
            <p class="subtle" style="margin-bottom: 1rem;">Click any garment to place it onto the uploaded photo.</p>
            <div id="garmentList">
                <?php foreach ($catalog as $product): ?>
                    <?php $isSelected = (int) $product['id'] === $selectedProductId; ?>
                    <div
                        class="garment-item <?= $isSelected ? 'selected' : '' ?>"
                        data-id="<?= (int) $product['id'] ?>"
                        data-name="<?= h($product['name']) ?>"
                        data-image="<?= h($product['image']) ?>"
                        data-category="<?= h($product['category']) ?>"
                        data-price="<?= format_money($product['price']) ?>"
                    >
                        <div class="garment-thumb" style="background: <?= h($product['image_bg_color']) ?>;">
                            <img src="<?= h($product['image']) ?>" alt="" style="max-width:100%; max-height:100%; object-fit:contain;">
                        </div>
                        <div>
                            <div style="font-weight: bold; color: #fff;"><?= h($product['name']) ?></div>
                            <div style="font-family: var(--font-tech); color: #00f3ff; font-size: 0.8rem;">&#8377;<?= format_money($product['price']) ?></div>
                            <div style="font-size: 0.75rem; color: #888;"><?= h(strtoupper($product['category'])) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="panel-center">
            <div class="upload-area">
                <input type="file" id="userImageInput" accept="image/*">
                <div id="uploadPlaceholder">
                    <div style="font-size: 3rem; color: #333;">[PHOTO]</div>
                    <div style="color: #666; font-family: var(--font-tech); font-size: 0.8rem; margin-top: 10px;">
                        UPLOAD FULL BODY PHOTO<br><span style="color: #00f3ff;">PROCESSING STAYS IN YOUR BROWSER</span>
                    </div>
                </div>
                <img id="previewImage" alt="" style="display:none;">
            </div>

            <div class="lab-actions">
                <button class="tryon-btn" id="placeGarmentBtn" disabled>&gt; LOAD SELECTED GARMENT</button>
                <button class="tryon-btn" id="removeSelectedBtn" disabled>&gt; REMOVE SELECTED</button>
                <button class="tryon-btn" id="downloadBtn" disabled>&gt; DOWNLOAD COMPOSITION</button>
                <button class="tryon-btn" id="resetBtn" disabled>&gt; RESET LAB</button>
            </div>

            <div id="canvasContainer">
                <canvas id="tCanvas"></canvas>
            </div>

            <div id="filterControls">
                <div style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
                    <div style="flex:1; min-width:180px;">
                        <label style="display:block; margin-bottom:6px;">&gt; BLEND MODE</label>
                        <select id="blendModeSelector" style="width:100%; padding:8px; background:#000; color:#00f3ff; border:1px solid #00f3ff; font-family:var(--font-tech);">
                            <option value="source-over">NORMAL</option>
                            <option value="multiply">MULTIPLY</option>
                            <option value="darken">DARKEN</option>
                        </select>
                    </div>
                    <div style="flex:1; min-width:180px;">
                        <label style="display:block; margin-bottom:6px;">&gt; TARGET COLOR</label>
                        <input type="color" id="bgColorPicker" value="#ffffff" style="width:100%; height:38px; cursor:pointer; border:1px solid #00f3ff; background:#000; padding:2px;">
                    </div>
                    <div style="flex:1; min-width:220px;">
                        <label style="display:block; margin-bottom:6px;">&gt; TOLERANCE <span id="toleranceVal">10%</span></label>
                        <input type="range" id="toleranceSlider" min="0" max="60" value="10" style="width:100%; accent-color:#00f3ff; cursor:pointer;">
                    </div>
                </div>
                <div class="subtle" style="margin-top: 12px;">
                    Use `MULTIPLY` or `DARKEN` for bright garments, and fine-tune the background color and tolerance for cleaner edges.
                </div>
            </div>

            <div id="statusMsg" class="status-line"></div>
        </div>

        <div class="panel panel-right">
            <h3 style="font-family: var(--font-tech); margin-bottom: 1.5rem;">&gt; METRICS</h3>
            <div class="metrics-card">
                <p class="subtle" style="margin-bottom: 6px;">SELECTED PRODUCT</p>
                <p id="selectedProductName" style="color: #fff;"><?= h($selectedProduct['name'] ?? 'None') ?></p>
                <p id="selectedProductMeta" class="subtle" style="margin-top: 8px;">
                    <?= $selectedProduct ? h(strtoupper($selectedProduct['category'])) . ' // ₹' . h(format_money($selectedProduct['price'])) : 'NO PRODUCT SELECTED' ?>
                </p>
            </div>
            <div class="metrics-card">
                <p class="subtle" style="margin-bottom: 6px;">LAB MODE</p>
                <p style="color: #00ff99;">LOCAL FABRIC.JS COMPOSITOR</p>
            </div>
            <div class="metrics-card">
                <p class="subtle" style="margin-bottom: 6px;">PRIVACY</p>
                <p style="color:#fff;">Your photo stays in the browser. No backend try-on request is sent.</p>
            </div>
            <div style="border: 1px solid #333; padding: 1rem; font-size: 0.8rem; color: #888; line-height: 1.6;">
                <strong style="color: #00f3ff;">WORKFLOW:</strong><br><br>
                1. Upload a clear full-body photo.<br><br>
                2. Click a garment from the left panel to place it on the canvas.<br><br>
                3. Drag, resize, or rotate the garment handles until it fits.<br><br>
                4. Adjust blend mode and chroma key controls for a cleaner look.<br><br>
                5. Download the final composition.
            </div>
        </div>
    </div>

    <script>
        const productMap = {};
        document.querySelectorAll('.garment-item').forEach(item => {
            productMap[item.dataset.id] = {
                id: item.dataset.id,
                name: item.dataset.name,
                image: item.dataset.image,
                category: item.dataset.category,
                price: item.dataset.price
            };
        });

        let selectedProductId = "<?= (int) $selectedProductId ?>";
        let userImageUrl = null;
        let backgroundLoaded = false;

        const canvas = new fabric.Canvas('tCanvas', {
            preserveObjectStacking: true,
            selection: false
        });

        const userImageInput = document.getElementById('userImageInput');
        const previewImage = document.getElementById('previewImage');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const placeGarmentBtn = document.getElementById('placeGarmentBtn');
        const removeSelectedBtn = document.getElementById('removeSelectedBtn');
        const downloadBtn = document.getElementById('downloadBtn');
        const resetBtn = document.getElementById('resetBtn');
        const statusMsg = document.getElementById('statusMsg');
        const selectedProductName = document.getElementById('selectedProductName');
        const selectedProductMeta = document.getElementById('selectedProductMeta');
        const canvasContainer = document.getElementById('canvasContainer');
        const bgColorPicker = document.getElementById('bgColorPicker');
        const toleranceSlider = document.getElementById('toleranceSlider');
        const toleranceVal = document.getElementById('toleranceVal');
        const blendModeSelector = document.getElementById('blendModeSelector');
        const filterControls = document.getElementById('filterControls');

        function setStatus(message, tone = '#00f3ff') {
            statusMsg.textContent = message;
            statusMsg.style.color = tone;
        }

        function setSelectedProduct(productId) {
            selectedProductId = String(productId);
            const selected = productMap[selectedProductId];
            document.querySelectorAll('.garment-item').forEach(item => {
                item.classList.toggle('selected', item.dataset.id === selectedProductId);
            });

            if (!selected) {
                selectedProductName.textContent = 'None';
                selectedProductMeta.textContent = 'NO PRODUCT SELECTED';
                return;
            }

            selectedProductName.textContent = selected.name;
            selectedProductMeta.textContent = `${selected.category.toUpperCase()} // ₹${selected.price}`;
        }

        function syncButtons() {
            const hasActiveObject = !!canvas.getActiveObject();
            placeGarmentBtn.disabled = !backgroundLoaded;
            removeSelectedBtn.disabled = !hasActiveObject;
            downloadBtn.disabled = !backgroundLoaded;
            resetBtn.disabled = !backgroundLoaded;
            filterControls.style.display = backgroundLoaded ? 'block' : 'none';
        }

        function activeRemoveColorFilter() {
            const activeObject = canvas.getActiveObject();
            if (!activeObject || !activeObject.filters) {
                return null;
            }

            return activeObject.filters.find(filter => filter && filter.type === 'RemoveColor') || null;
        }

        function syncActiveControls() {
            const filter = activeRemoveColorFilter();
            if (!filter) {
                blendModeSelector.value = 'source-over';
                bgColorPicker.value = '#ffffff';
                toleranceSlider.value = 10;
                toleranceVal.textContent = '10%';
                syncButtons();
                return;
            }

            const activeObject = canvas.getActiveObject();
            blendModeSelector.value = activeObject.globalCompositeOperation || 'source-over';
            bgColorPicker.value = filter.color || '#ffffff';
            toleranceSlider.value = Math.round((filter.distance || 0.1) * 100);
            toleranceVal.textContent = toleranceSlider.value + '%';
            syncButtons();
        }

        function loadPhotoIntoCanvas() {
            if (!userImageUrl) {
                setStatus('Upload your photo before using the lab.', '#ff9c74');
                return;
            }

            fabric.Image.fromURL(userImageUrl, function(img) {
                const maxWidth = Math.min(window.innerWidth * 0.78, 860);
                const scale = maxWidth / img.width;

                canvas.clear();
                canvas.setWidth(maxWidth);
                canvas.setHeight(img.height * scale);

                img.scale(scale);
                img.evented = false;
                img.selectable = false;

                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                canvasContainer.style.display = 'block';
                backgroundLoaded = true;
                syncButtons();
                setStatus('Photo loaded. Click a garment from the left panel to start styling.');
            });
        }

        function detectBackgroundColor(imageObject) {
            try {
                const imageElement = imageObject.getElement();
                const sampleCanvas = document.createElement('canvas');
                sampleCanvas.width = 1;
                sampleCanvas.height = 1;
                const sampleCtx = sampleCanvas.getContext('2d');
                sampleCtx.drawImage(imageElement, 0, 0, 1, 1);
                const pixel = sampleCtx.getImageData(0, 0, 1, 1).data;
                return "#" + ((1 << 24) | (pixel[0] << 16) | (pixel[1] << 8) | pixel[2]).toString(16).slice(1);
            } catch (error) {
                return '#ffffff';
            }
        }

        function addSelectedGarmentToCanvas() {
            const selected = productMap[selectedProductId];
            if (!selected) {
                setStatus('Select a garment first.', '#ff9c74');
                return;
            }

            if (!backgroundLoaded) {
                setStatus('Upload your photo before adding garments.', '#ff9c74');
                return;
            }

            fabric.Image.fromURL(selected.image, function(garment) {
                const detectedBgColor = detectBackgroundColor(garment);
                const removeColor = new fabric.Image.filters.RemoveColor({
                    distance: 0.10,
                    color: detectedBgColor
                });

                garment.filters = garment.filters || [];
                garment.filters.push(removeColor);
                garment.applyFilters();
                garment.set('globalCompositeOperation', 'source-over');
                garment.scaleToWidth(canvas.width * 0.55);
                garment.set({
                    left: (canvas.width - garment.getScaledWidth()) / 2,
                    top: (canvas.height - garment.getScaledHeight()) / 2,
                    transparentCorners: false,
                    cornerColor: '#00f3ff',
                    cornerStrokeColor: '#fff',
                    borderColor: '#00f3ff',
                    cornerSize: 12,
                    padding: 10,
                    cornerStyle: 'circle'
                });

                canvas.add(garment);
                canvas.setActiveObject(garment);
                canvas.renderAll();
                syncActiveControls();
                setStatus(`${selected.name} added. Drag, resize, or rotate it into place.`);
            }, { crossOrigin: 'anonymous' });
        }

        document.querySelectorAll('.garment-item').forEach(item => {
            item.addEventListener('click', function() {
                setSelectedProduct(this.dataset.id);
                if (backgroundLoaded) {
                    addSelectedGarmentToCanvas();
                } else {
                    setStatus('Garment selected. Upload your photo to start composing.');
                }
            });
        });

        userImageInput.addEventListener('change', event => {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            if (userImageUrl) {
                URL.revokeObjectURL(userImageUrl);
            }

            userImageUrl = URL.createObjectURL(file);
            previewImage.src = userImageUrl;
            previewImage.style.display = 'block';
            uploadPlaceholder.style.display = 'none';
            loadPhotoIntoCanvas();
        });

        placeGarmentBtn.addEventListener('click', addSelectedGarmentToCanvas);

        removeSelectedBtn.addEventListener('click', () => {
            const activeObject = canvas.getActiveObject();
            if (!activeObject) {
                return;
            }

            canvas.remove(activeObject);
            canvas.discardActiveObject();
            canvas.renderAll();
            syncActiveControls();
            setStatus('Selected garment removed.');
        });

        resetBtn.addEventListener('click', () => {
            if (!backgroundLoaded) {
                return;
            }

            loadPhotoIntoCanvas();
            setStatus('Lab reset. Your photo is still loaded and ready for a fresh composition.');
        });

        downloadBtn.addEventListener('click', () => {
            if (!backgroundLoaded) {
                setStatus('Upload your photo before downloading.', '#ff9c74');
                return;
            }

            const link = document.createElement('a');
            link.download = 'au-fit-composition-' + Date.now() + '.png';
            link.href = canvas.toDataURL({
                format: 'png',
                quality: 1,
                multiplier: 2
            });
            link.click();
            setStatus('Composition downloaded.');
        });

        bgColorPicker.addEventListener('input', event => {
            const filter = activeRemoveColorFilter();
            const activeObject = canvas.getActiveObject();
            if (!filter || !activeObject) {
                return;
            }

            filter.color = event.target.value;
            activeObject.applyFilters();
            canvas.renderAll();
        });

        toleranceSlider.addEventListener('input', event => {
            toleranceVal.textContent = event.target.value + '%';
            const filter = activeRemoveColorFilter();
            const activeObject = canvas.getActiveObject();
            if (!filter || !activeObject) {
                return;
            }

            filter.distance = event.target.value / 100;
            activeObject.applyFilters();
            canvas.renderAll();
        });

        blendModeSelector.addEventListener('change', event => {
            const activeObject = canvas.getActiveObject();
            if (!activeObject) {
                return;
            }

            activeObject.set('globalCompositeOperation', event.target.value);
            canvas.renderAll();
        });

        canvas.on('selection:created', syncActiveControls);
        canvas.on('selection:updated', syncActiveControls);
        canvas.on('selection:cleared', syncActiveControls);

        window.addEventListener('keydown', event => {
            if ((event.key === 'Delete' || event.key === 'Backspace') && canvas.getActiveObject()) {
                const tag = document.activeElement ? document.activeElement.tagName : '';
                if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') {
                    return;
                }

                canvas.remove(canvas.getActiveObject());
                canvas.discardActiveObject();
                canvas.renderAll();
                syncActiveControls();
                setStatus('Selected garment removed.');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cursorDot = document.querySelector(".cursor-dot");
            const cursorOutline = document.querySelector(".cursor-outline");

            if (cursorDot && cursorOutline) {
                window.addEventListener("mousemove", function (event) {
                    const posX = event.clientX;
                    const posY = event.clientY;

                    cursorDot.style.left = `${posX}px`;
                    cursorDot.style.top = `${posY}px`;

                    cursorOutline.animate({
                        left: `${posX}px`,
                        top: `${posY}px`
                    }, { duration: 500, fill: "forwards" });
                });
            }
        });

        setSelectedProduct(selectedProductId);
        syncButtons();
    </script>
</body>
</html>
