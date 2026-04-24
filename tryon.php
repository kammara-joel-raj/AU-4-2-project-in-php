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
    :root {
        --lab-nav-height: 76px;
        --lab-gap: 14px;
    }
    html { height: 100%; }
    body.lab-page {
        background:
            linear-gradient(90deg, rgba(0, 243, 255, 0.05) 1px, transparent 1px),
            radial-gradient(circle at top, rgba(0, 243, 255, 0.08), transparent 40%),
            #050505;
        background-size: 48px 100%, auto, auto;
        color: #00f3ff;
        overflow: hidden;
        min-height: 100dvh;
        font-family: "Courier New", monospace;
        text-transform: uppercase;
    }
    .navbar {
        background: #000;
        border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        height: var(--lab-nav-height);
        padding: 0.95rem 1.5rem;
        position: relative;
    }
    .nav-links { gap: 1rem; }
    .nav-links a, .logo {
        color: #fff;
        font-family: "Courier New", monospace;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .logo { font-size: 1.35rem; white-space: nowrap; }
    .lab-container {
        display: grid;
        grid-template-columns: minmax(220px, 286px) minmax(0, 1fr) minmax(220px, 240px);
        gap: var(--lab-gap);
        height: calc(100dvh - var(--lab-nav-height));
        min-height: 0;
        padding: var(--lab-gap);
    }
    .panel {
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 0;
        padding: 1rem;
        background: rgba(8, 8, 8, 0.94);
        overflow: hidden;
        position: relative;
        min-height: 0;
        display: flex;
        flex-direction: column;
    }
    .panel-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        min-height: 0;
        padding: 0.25rem 0;
        overflow: visible;
    }
    .lab-title {
        margin-bottom: 0.95rem;
        font-size: 0.98rem;
        letter-spacing: 0.02em;
    }
    .garment-item {
        padding: 0.8rem 0.9rem;
        border: 1px solid rgba(255, 255, 255, 0.16);
        cursor: pointer;
        display: flex;
        gap: 12px;
        transition: 0.25s ease;
        border-radius: 0;
        align-items: center;
        background: rgba(10, 10, 10, 0.92);
    }
    .garment-item:hover, .garment-item.selected {
        border-color: #00f3ff;
        background: rgba(0, 243, 255, 0.08);
        box-shadow: inset 0 0 0 1px rgba(0, 243, 255, 0.15);
    }
    .garment-thumb {
        width: 34px;
        height: 34px;
        background: #222;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        border-radius: 0;
    }
    .asset-caption {
        font-family: Georgia, "Times New Roman", serif;
        font-weight: 700;
        font-size: 0.88rem;
        line-height: 1.15;
        color: #fff;
        text-transform: none;
    }
    .asset-price {
        margin-top: 0.2rem;
        color: #00f3ff;
        font-size: 0.78rem;
    }
    .canvas-stage {
        width: min(100%, 500px);
        display: flex;
        flex-direction: column;
        align-items: stretch;
        justify-content: center;
        min-height: 0;
    }
    .stage-frame {
        position: relative;
        display: flex;
        flex-direction: column;
        border: 2px solid #00f3ff;
        background: rgba(0, 0, 0, 0.92);
        box-shadow: 0 0 28px rgba(0, 243, 255, 0.18);
        min-height: 0;
    }
    .stage-toolbar {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 4;
        display: flex;
        gap: 8px;
    }
    .control-mini-btn {
        min-height: 34px;
        padding: 0.55rem 0.8rem;
        border: 1px solid #00f3ff;
        background: rgba(0, 0, 0, 0.88);
        color: #00f3ff;
        font-family: "Courier New", monospace;
        font-size: 0.66rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        cursor: pointer;
        transition: 0.2s ease;
    }
    .control-mini-btn:hover:not(:disabled) {
        background: rgba(0, 243, 255, 0.14);
        box-shadow: 0 0 14px rgba(0, 243, 255, 0.24);
    }
    .control-mini-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }
    .canvas-shell {
        position: relative;
        width: 100%;
        min-height: clamp(380px, 62vh, 640px);
        display: flex;
        justify-content: center;
        align-items: center;
        background: #060606;
        overflow: hidden;
    }
    .upload-area {
        position: absolute;
        inset: 0;
        width: 100%;
        border: 1px dashed rgba(0, 243, 255, 0.32);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(6, 6, 6, 0.9);
        overflow: hidden;
        padding: 1rem;
        text-align: center;
        transition: opacity 0.2s ease;
        z-index: 2;
    }
    .upload-area.is-loaded {
        opacity: 0;
        pointer-events: none;
    }
    .upload-area input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .upload-area img { max-width: 100%; max-height: 280px; object-fit: contain; }
    .upload-hint {
        display: none;
        color: #8edfe5;
        font-size: 0.72rem;
        letter-spacing: 0.05em;
    }
    #canvasContainer {
        display: none;
        width: 100%;
        height: 100%;
        min-height: inherit;
        max-width: 100%;
        position: relative;
        overflow: hidden;
        align-items: center;
        justify-content: center;
    }
    #canvasContainer.active { display: flex; }
    #canvasContainer .canvas-container,
    #canvasContainer canvas {
        max-width: 100%;
        max-height: 100%;
    }
    #filterControls {
        display: none;
        width: 100%;
        background: rgba(10, 10, 10, 0.95);
        border-top: 1px solid rgba(0, 243, 255, 0.85);
        padding: 14px 16px 12px;
        overflow: hidden;
    }
    .controls-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 16px;
        align-items: end;
    }
    .control-card {
        min-width: 0;
    }
    .control-card--wide { grid-column: 1 / -1; }
    .control-label {
        display: block;
        margin-bottom: 8px;
        color: #d4ffff;
        font-size: 0.72rem;
        letter-spacing: 0.03em;
    }
    .control-select {
        width: 100%;
        min-height: 40px;
        padding: 0.7rem 0.8rem;
        background: #02090a;
        color: #00f3ff;
        border: 1px solid #00f3ff;
        border-radius: 0;
        font-family: "Courier New", monospace;
        text-transform: uppercase;
    }
    .color-picker-row {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 40px;
    }
    .color-picker-input {
        width: 128px;
        height: 34px;
        padding: 0;
        border: 1px solid #00f3ff;
        border-radius: 0;
        background: #02090a;
        cursor: pointer;
        overflow: hidden;
        flex: 0 0 auto;
    }
    .color-picker-input::-webkit-color-swatch-wrapper { padding: 0; }
    .color-picker-input::-webkit-color-swatch { border: none; }
    .color-preview {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .color-chip {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 0 10px rgba(0, 243, 255, 0.25);
        flex: 0 0 auto;
    }
    .color-code {
        color: #8df8ff;
        font-size: 0.74rem;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }
    .slider-value {
        float: right;
        color: #00f3ff;
        font-size: 0.78rem;
    }
    .tolerance-slider {
        width: 100%;
        height: 7px;
        appearance: none;
        background: linear-gradient(90deg, rgba(0, 243, 255, 0.95), rgba(255, 255, 255, 0.22));
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 999px;
        cursor: pointer;
    }
    .tolerance-slider::-webkit-slider-thumb {
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #00f3ff;
        border: none;
        box-shadow: 0 0 10px rgba(0, 243, 255, 0.9);
    }
    .tolerance-slider::-moz-range-thumb {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #00f3ff;
        border: none;
        box-shadow: 0 0 10px rgba(0, 243, 255, 0.9);
    }
    .tolerance-slider::-moz-range-track {
        height: 7px;
        background: linear-gradient(90deg, rgba(0, 243, 255, 0.95), rgba(255, 255, 255, 0.22));
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 999px;
    }
    .lab-actions {
        display: flex;
        justify-content: center;
        width: min(100%, 350px);
    }
    .tryon-btn {
        width: 100%;
        min-height: 44px;
        padding: 12px 14px;
        background: rgba(0, 0, 0, 0.84);
        border: 1px solid #00f3ff;
        color: #00f3ff;
        font-family: "Courier New", monospace;
        font-weight: bold;
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        cursor: pointer;
        transition: 0.3s;
        border-radius: 0;
    }
    .tryon-btn:hover:not(:disabled) { background: #00f3ff; color: #000; box-shadow: 0 0 20px rgba(0, 243, 255, 0.5); }
    .tryon-btn:disabled { opacity: 0.45; cursor: not-allowed; }
    .status-line {
        width: min(100%, 500px);
        min-height: 20px;
        color: #00f3ff;
        font-size: 0.72rem;
        letter-spacing: 0.04em;
        text-align: center;
    }
    .subtle {
        color: #888;
        font-size: 0.76rem;
        line-height: 1.5;
        text-transform: none;
    }
    #garmentList { min-height: 0; overflow: auto; padding-right: 4px; display: grid; gap: 0.75rem; align-content: start; }
    .metrics-stack { min-height: 0; overflow: auto; padding-right: 4px; }
    .metrics-card {
        margin-bottom: 1.6rem;
    }
    .metric-label {
        color: #767676;
        font-size: 0.74rem;
        margin-bottom: 0.35rem;
    }
    .metric-value {
        color: #fff;
        font-size: 0.9rem;
        line-height: 1.35;
    }
    .metric-value--green {
        color: #39ff14;
    }
    .workflow-card {
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 1rem 0.9rem;
        font-size: 0.75rem;
        color: #9b9b9b;
        line-height: 1.6;
        background: rgba(255, 255, 255, 0.01);
        text-transform: none;
    }
    #garmentList::-webkit-scrollbar, .metrics-stack::-webkit-scrollbar { width: 6px; }
    #garmentList::-webkit-scrollbar-thumb, .metrics-stack::-webkit-scrollbar-thumb { background: rgba(0, 243, 255, 0.35); border-radius: 999px; }
    @media (max-height: 900px) {
        :root { --lab-nav-height: 70px; --lab-gap: 12px; }
        .navbar { padding: 0.75rem 1.25rem; }
        .panel { padding: 0.85rem; }
        .lab-title { margin-bottom: 0.55rem; font-size: 0.9rem; }
        .canvas-shell { min-height: clamp(320px, 58vh, 560px); }
        .tryon-btn { padding: 10px 8px; font-size: 0.68rem; }
    }
    @media (max-width: 1200px) {
        .lab-container { grid-template-columns: 210px minmax(0, 1fr) 210px; }
        .canvas-stage, .status-line { width: min(100%, 440px); }
    }
    @media (max-width: 980px) {
        body.lab-page { overflow: auto; }
        .navbar { min-height: auto; padding: 1rem; flex-wrap: wrap; gap: 0.75rem; }
        .lab-container { grid-template-columns: 1fr; height: auto; min-height: calc(100dvh - var(--lab-nav-height)); }
        .panel, .panel-center { min-height: auto; }
        #garmentList, .metrics-stack { overflow: visible; }
        .panel-center { order: -1; }
        .canvas-stage, .status-line { width: min(100%, 520px); }
    }
    @media (max-width: 760px) {
        .controls-grid { grid-template-columns: 1fr; }
        .color-picker-row { flex-wrap: wrap; }
        .color-picker-input { width: 100%; }
        .stage-toolbar {
            position: static;
            padding: 10px 10px 0;
            flex-wrap: wrap;
        }
    }
    @media (max-width: 680px) {
        .canvas-shell { min-height: 420px; }
        .lab-actions { width: 100%; }
    }
</style>
</head>

<body class="lab-page">
    <div class="cursor-dot"></div><div class="cursor-outline"></div>

    <nav class="navbar">
        <div class="logo glitch" data-text="VIRTUAL_FIT_LAB">VIRTUAL_FIT_LAB v2.5</div>
        <ul class="nav-links">
            <li><a href="shop.php">EXIT LAB</a></li>
        </ul>
    </nav>

    <div class="lab-container">
        <div class="panel">
            <h3 class="lab-title">&gt; SELECT_ASSET</h3>
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
                            <div class="asset-caption"><?= h($product['name']) ?></div>
                            <div class="asset-price">&#8377;<?= format_money($product['price']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="panel-center">
            <div class="canvas-stage">
                <div class="stage-frame">
                    <div class="stage-toolbar">
                        <button class="control-mini-btn" id="placeGarmentBtn" disabled>[+] Load</button>
                        <button class="control-mini-btn" id="removeSelectedBtn" disabled>[-] Remove</button>
                        <button class="control-mini-btn" id="resetBtn" disabled>[x] Reset</button>
                    </div>
                    <div class="canvas-shell">
                        <div class="upload-area">
                            <input type="file" id="userImageInput" accept="image/*">
                            <div id="uploadPlaceholder">
                                <div style="font-size: 3rem; color: #333;">[PHOTO]</div>
                                <div style="color: #666; font-size: 0.8rem; margin-top: 10px;">
                                    Upload full body photo<br><span style="color: #00f3ff;">Processing stays in your browser</span>
                                </div>
                            </div>
                            <img id="previewImage" alt="" style="display:none;">
                            <div class="upload-hint">Photo locked in lab preview. Click again to replace it.</div>
                        </div>
                        <div id="canvasContainer">
                            <canvas id="tCanvas"></canvas>
                        </div>
                    </div>
                    <div id="filterControls">
                        <div class="controls-grid">
                            <div class="control-card control-card--wide">
                                <label class="control-label" for="blendModeSelector">&gt; Blend_Mode</label>
                                <select id="blendModeSelector" class="control-select">
                                    <option value="source-over">NORMAL</option>
                                    <option value="multiply">MULTIPLY</option>
                                    <option value="darken">DARKEN</option>
                                </select>
                            </div>
                            <div class="control-card">
                                <label class="control-label" for="bgColorPicker">&gt; Target_Color</label>
                                <div class="color-picker-row">
                                    <input type="color" id="bgColorPicker" value="#ffffff" class="color-picker-input">
                                    <div class="color-preview">
                                        <span class="color-chip" id="colorChip" style="background:#ffffff;"></span>
                                        <span class="color-code" id="colorValue">#FFFFFF</span>
                                    </div>
                                </div>
                            </div>
                            <div class="control-card">
                                <label class="control-label" for="toleranceSlider">&gt; Tolerance: <span id="toleranceVal" class="slider-value">10%</span></label>
                                <input type="range" id="toleranceSlider" min="0" max="60" value="10" class="tolerance-slider">
                            </div>
                        </div>
                        <div class="subtle" style="margin-top: 12px;">
                            *Warning: Removes all pixels matching the target color.
                        </div>
                    </div>
                </div>
            </div>

            <div class="lab-actions">
                <button class="tryon-btn" id="downloadBtn" disabled>&gt; Download Composition</button>
            </div>

            <div id="statusMsg" class="status-line"></div>
        </div>

        <div class="panel panel-right">
            <h3 class="lab-title">&gt; METRICS</h3>
            <div class="metrics-stack">
            <div class="metrics-card" style="margin-top: 1.6rem;">
                <p class="metric-label">Selected_Product</p>
                <p id="selectedProductName" class="metric-value"><?= h($selectedProduct['name'] ?? 'None') ?></p>
                <p id="selectedProductMeta" class="subtle" style="margin-top: 8px;">
                    <?= $selectedProduct ? h(strtoupper($selectedProduct['category'])) . ' // ₹' . h(format_money($selectedProduct['price'])) : 'NO PRODUCT SELECTED' ?>
                </p>
            </div>
            <div class="metrics-card">
                <p class="metric-label">Render_Engine</p>
                <p class="metric-value">FABRIC.JS w/ COMPOSITING</p>
            </div>
            <div class="metrics-card">
                <p class="metric-label">Latency</p>
                <p class="metric-value metric-value--green">ZERO-LAG (CLIENT SIDE)</p>
            </div>
            <div class="workflow-card">
                <strong style="color: #00f3ff; text-transform: uppercase;">Pro-Tips:</strong><br><br>
                1. If a shirt has a white background, change the <strong style="color:#fff;">Blend Mode</strong> to <strong style="color:#fff;">Multiply</strong>.<br><br>
                2. Use the <strong style="color:#fff;">Target Color Picker</strong> to select exactly which background color to remove.
            </div>
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
        const canvasStage = document.querySelector('.canvas-shell');
        const uploadArea = document.querySelector('.upload-area');
        const bgColorPicker = document.getElementById('bgColorPicker');
        const toleranceSlider = document.getElementById('toleranceSlider');
        const toleranceVal = document.getElementById('toleranceVal');
        const blendModeSelector = document.getElementById('blendModeSelector');
        const filterControls = document.getElementById('filterControls');
        const colorChip = document.getElementById('colorChip');
        const colorValue = document.getElementById('colorValue');

        function syncColorPreview(color) {
            const safeColor = color || '#ffffff';
            colorChip.style.background = safeColor;
            colorValue.textContent = safeColor.toUpperCase();
        }

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
            canvasContainer.classList.toggle('active', backgroundLoaded);
        }

        function syncUploadState() {
            uploadArea.classList.toggle('is-loaded', !!userImageUrl);
        }

        function getCanvasBounds() {
            const stageWidth = canvasStage ? canvasStage.clientWidth : 0;
            const stageHeight = canvasStage ? canvasStage.clientHeight : 0;

            return {
                width: Math.max(Math.min(stageWidth - 24, 860), 260),
                height: Math.max(stageHeight - 24, 240)
            };
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
                syncColorPreview('#ffffff');
                syncButtons();
                return;
            }

            const activeObject = canvas.getActiveObject();
            blendModeSelector.value = activeObject.globalCompositeOperation || 'source-over';
            bgColorPicker.value = filter.color || '#ffffff';
            toleranceSlider.value = Math.round((filter.distance || 0.1) * 100);
            toleranceVal.textContent = toleranceSlider.value + '%';
            syncColorPreview(bgColorPicker.value);
            syncButtons();
        }

        function loadPhotoIntoCanvas() {
            if (!userImageUrl) {
                setStatus('Upload your photo before using the lab.', '#ff9c74');
                return;
            }

            fabric.Image.fromURL(userImageUrl, function(img) {
                backgroundLoaded = true;
                syncButtons();

                requestAnimationFrame(() => {
                    const bounds = getCanvasBounds();
                    const scale = Math.min(bounds.width / img.width, bounds.height / img.height);
                    const safeScale = Number.isFinite(scale) && scale > 0 ? scale : 1;

                    canvas.clear();
                    canvas.setDimensions({
                        width: Math.round(img.width * safeScale),
                        height: Math.round(img.height * safeScale)
                    });

                    img.scale(safeScale);
                    img.evented = false;
                    img.selectable = false;

                    canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                    setStatus('Photo loaded. Click a garment from the left panel to start styling.');
                });
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
            backgroundLoaded = false;
            syncButtons();
            syncUploadState();
            previewImage.src = userImageUrl;
            previewImage.style.display = 'block';
            uploadPlaceholder.style.display = 'none';
            setStatus('Photo loaded into the preview. Fitting it into the lab workspace...');
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
            syncColorPreview(event.target.value);
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
        syncUploadState();
        syncColorPreview(bgColorPicker.value);
        syncButtons();
    </script>
</body>
</html>
