<?php
$pageTitle = "AU | VIRTUAL_FIT_LAB";
include 'includes/header.php';
require_once __DIR__ . '/includes/db.php';

// Added "image" to the selection so we can map it to our drag & drop overlay
try {
    $stmt = $pdo->query("SELECT id, name, price, description, image_bg_color, image FROM products ORDER BY id ASC LIMIT 20");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
}
?>

<!-- Include Fabric.js for Drag & Drop Functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<style>
    body { background-color: #050505; color: #00f3ff; overflow-x: hidden; }
    .navbar { background: #000; border-bottom: 1px solid #333; }
    .nav-links a, .logo { color: #fff; }
    
    /* CRT Effect Overlay */
    .crt::before {
        content: " "; display: block; position: absolute; top: 0; left: 0; bottom: 0; right: 0;
        background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%),
                    linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
        z-index: 2; background-size: 100% 2px, 3px 100%; pointer-events: none;
    }

    .lab-container { display: grid; grid-template-columns: 320px 1fr 320px; min-height: calc(100vh - 100px); }
    .panel { border-right: 1px solid #333; padding: 2rem; background: #0a0a0a; overflow-y: auto; max-height: calc(100vh - 100px); position: relative; z-index: 5; }
    .panel-right { border-left: 1px solid #333; border-right: none; }
    .panel-center { display: flex; flex-direction: column; align-items: center; justify-content: center; background: radial-gradient(circle at center, #111 0%, #000 100%); padding: 2rem; }

    /* Items */
    .garment-item { padding: 15px; border: 1px solid #333; margin-bottom: 10px; cursor: pointer; display: flex; gap: 12px; transition: 0.3s; }
    .garment-item:hover, .garment-item.selected { border-color: #00f3ff; background: rgba(0, 243, 255, 0.1); }
    .garment-thumb { width: 40px; height: 40px; background: #222; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

    /* Upload */
    .upload-area { width: 100%; max-width: 400px; aspect-ratio: 3/4; border: 2px dashed #333; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; background: #0a0a0a; overflow: hidden; z-index: 10;}
    .upload-area input { position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; }

    /* Button */
    .tryon-btn { width: 100%; max-width: 400px; padding: 18px; margin-top: 2rem; background: transparent; border: 2px solid #00f3ff; color: #00f3ff; font-family: var(--font-tech); font-weight: bold; cursor: pointer; transition: 0.3s; z-index: 10;}
    .tryon-btn:hover { background: #00f3ff; color: #000; box-shadow: 0 0 20px rgba(0, 243, 255, 0.5); }
    
    /* Canvas Container */
    #canvasContainer {
        display: none; 
        border: 2px solid #00f3ff; 
        box-shadow: 0 0 20px rgba(0, 243, 255, 0.2); 
        background: #111; 
        max-width: 100%; 
        position: relative;
        z-index: 10;
    }

    /* Controls UI */
    .control-group { margin-bottom: 15px; }
    .control-group label { display: flex; justify-content: space-between; font-size: 0.8rem; color: #fff; margin-bottom: 5px; }
    .control-group select { width: 100%; padding: 8px; background: #000; color: #00f3ff; border: 1px solid #00f3ff; font-family: var(--font-tech); outline: none; cursor: pointer; }

    @media (max-width: 1024px) { .lab-container { grid-template-columns: 1fr; } .panel { border: none; border-bottom: 1px solid #333; max-height: none; } }
</style>
</head>

<body class="crt">
    <!-- Cursor -->
    <div class="cursor-dot"></div><div class="cursor-outline"></div>

    <nav class="navbar">
        <div class="logo glitch" data-text="VIRTUAL_FIT_LAB">VIRTUAL_FIT_LAB v2.5</div>
        <ul class="nav-links"><li><a href="shop.php">EXIT LAB</a></li></ul>
    </nav>

    <div class="lab-container">
        <!-- Products Panel -->
        <div class="panel">
            <h3 style="font-family: var(--font-tech); margin-bottom: 1.5rem;">&gt; SELECT_ASSET</h3>
            <?php if (empty($products)): ?>
                <p style="color: #666;">Database Error: No products found.</p>
            <?php else: ?>
                <div id="garmentList">
                    <?php foreach ($products as $p): ?>
                        <?php $imgPath = !empty($p['image']) ? htmlspecialchars(str_replace('\\', '/', $p['image'])) : ''; ?>
                        <div class="garment-item" data-id="<?php echo $p['id']; ?>" data-image="<?php echo $imgPath; ?>">
                            <div class="garment-thumb" style="background: <?php echo $p['image_bg_color']; ?>;">
                                <?php echo (stripos($p['name'], 'hoodie') !== false) ? '🧥' : '👕'; ?>
                            </div>
                            <div>
                                <div style="font-weight: bold; color: #fff;"><?php echo htmlspecialchars($p['name']); ?></div>
                                <div style="font-family: var(--font-tech); color: #00f3ff; font-size: 0.8rem;">₹<?php echo number_format($p['price']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Interactive Panel -->
        <div class="panel-center">
            
            <div id="canvasContainer">
                <canvas id="tCanvas"></canvas>
                <button id="resetCanvasBtn" style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); border: 1px solid #00f3ff; color: #00f3ff; padding: 5px 10px; cursor: pointer; z-index: 20; font-family: var(--font-tech); font-size: 0.8rem;">[X] RESET</button>
                
                <!-- Dynamic Image Controls -->
                <div id="filterControls" style="display:none; position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.9); border-top: 1px solid #00f3ff; padding: 15px; z-index: 20; font-family: var(--font-tech);">
                    
                    <div class="control-group">
                        <label>
                            <span>&gt; BLEND_MODE:</span>
                        </label>
                        <select id="blendModeSelector">
                            <option value="source-over">NORMAL (Keep White BG)</option>
                            <option value="multiply">MULTIPLY (Best for White Shirts/BGs)</option>
                            <option value="darken">DARKEN</option>
                        </select>
                    </div>

                    <div class="control-group" style="display: flex; gap: 15px; align-items: center; margin-bottom: 0;">
                        <!-- Color Picker -->
                        <div style="flex: 1;">
                            <label>
                                <span>&gt; TARGET_COLOR:</span>
                            </label>
                            <input type="color" id="bgColorPicker" value="#ffffff" style="width: 100%; height: 35px; cursor: pointer; border: 1px solid #00f3ff; background: #000; padding: 2px;">
                        </div>
                        
                        <!-- Tolerance Slider -->
                        <div style="flex: 2;">
                            <label>
                                <span>&gt; TOLERANCE:</span>
                                <span id="toleranceVal" style="color: #00f3ff; font-weight: bold;">10%</span>
                            </label>
                            <input type="range" id="toleranceSlider" min="0" max="60" value="10" style="width: 100%; accent-color: #00f3ff; cursor: pointer;">
                        </div>
                    </div>
                    <small style="color: #666; font-size: 0.7rem; display:block; margin-top: 8px;">*Warning: Removes ALL pixels matching the target color.</small>

                </div>
            </div>

            <div class="upload-area" id="uploadArea">
                <input type="file" id="userImageInput" accept="image/*">
                <div id="uploadPlaceholder" style="text-align: center;">
                    <div style="font-size: 3rem; color: #333;">📸</div>
                    <div style="color: #666; font-family: var(--font-tech); font-size: 0.8rem; margin-top: 10px;">
                        UPLOAD FULL BODY PHOTO<br><span style="color: #00f3ff;">JPG / PNG / WEBP</span>
                    </div>
                </div>
            </div>

            <button class="tryon-btn" id="downloadBtn" style="display:none;">&gt; DOWNLOAD COMPOSITION</button>
            <div id="statusMsg" style="font-family: var(--font-tech); margin-top: 1rem; color: #00f3ff; display:none;"></div>

        </div>

        <!-- Metrics Panel -->
        <div class="panel panel-right">
            <h3 style="font-family: var(--font-tech); margin-bottom: 1.5rem;">&gt; METRICS</h3>
            <div style="margin-bottom: 2rem;">
                <p style="color: #666; font-size: 0.8rem; margin-bottom: 5px;">RENDER_ENGINE</p>
                <p style="color: #fff;">FABRIC.JS w/ COMPOSITING</p>
            </div>
            <div style="margin-bottom: 2rem;">
                <p style="color: #666; font-size: 0.8rem; margin-bottom: 5px;">LATENCY</p>
                <p style="color: #00ff00;">ZERO-LAG (CLIENT SIDE)</p>
            </div>
            <div style="border: 1px solid #333; padding: 1rem; font-size: 0.8rem; color: #888; line-height: 1.5;">
                <strong style="color: #00f3ff;">PRO-TIPS:</strong><br><br>
                1. If a shirt has a white background, change the <strong>Blend Mode</strong> to <span style="color:#fff;">MULTIPLY</span>.<br><br>
                2. Use the <strong>Target Color Picker</strong> to select exactly which background color to remove.
            </div>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('userImageInput');
        const canvasContainer = document.getElementById('canvasContainer');
        const downloadBtn = document.getElementById('downloadBtn');
        const resetCanvasBtn = document.getElementById('resetCanvasBtn');
        const statusMsg = document.getElementById('statusMsg');
        
        // Controls UI elements
        const filterControls = document.getElementById('filterControls');
        const bgColorPicker = document.getElementById('bgColorPicker');
        const toleranceSlider = document.getElementById('toleranceSlider');
        const toleranceVal = document.getElementById('toleranceVal');
        const blendModeSelector = document.getElementById('blendModeSelector');
        
        // Initialize Fabric.js Canvas
        let canvas = new fabric.Canvas('tCanvas', {
            preserveObjectStacking: true,
            selection: false 
        });

        // 1. Handle User Photo Upload
        fileInput.addEventListener('change', e => {
            if(e.target.files[0]) {
                const file = e.target.files[0];
                const reader = new FileReader();
                
                reader.onload = ev => {
                    const dataUrl = ev.target.result;
                    
                    fabric.Image.fromURL(dataUrl, function(img) {
                        const maxWidth = Math.min(window.innerWidth * 0.8, 450);
                        const scale = maxWidth / img.width;
                        
                        canvas.setWidth(maxWidth);
                        canvas.setHeight(img.height * scale);
                        
                        img.scale(scale);
                        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                        
                        uploadArea.style.display = 'none';
                        canvasContainer.style.display = 'block';
                        downloadBtn.style.display = 'block';
                    });
                }
                reader.readAsDataURL(file);
            }
        });

        // 2. Add Selected Garment to Canvas
        document.querySelectorAll('.garment-item').forEach(item => {
            item.addEventListener('click', () => {
                if (uploadArea.style.display !== 'none') {
                    alert("Please upload your photo first.");
                    return;
                }
                
                document.querySelectorAll('.garment-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                
                const imgPath = item.dataset.image;
                if(!imgPath) {
                    alert("Image asset missing for this product.");
                    return;
                }
                
                statusMsg.style.display = 'block';
                statusMsg.innerText = "PROCESSING ASSET...";

                // Load and place garment image
                fabric.Image.fromURL(imgPath, function(oImg) {
                    if(!oImg) {
                        statusMsg.innerText = "ERROR: Failed to load asset.";
                        return;
                    }
                    
                    // --- DYNAMIC BACKGROUND COLOR DETECTION ---
                    let detectedBgColor = "#ffffff"; 
                    try {
                        const imgEl = oImg.getElement();
                        const tCan = document.createElement('canvas');
                        tCan.width = 1; tCan.height = 1;
                        const tCtx = tCan.getContext('2d');
                        tCtx.drawImage(imgEl, 0, 0, 1, 1);
                        const p = tCtx.getImageData(0, 0, 1, 1).data;
                        
                        if (p[3] === 255) {
                            const rgbToHex = (r, g, b) => "#" + (1 << 24 | r << 16 | g << 8 | b).toString(16).padStart(6, '0').toLowerCase();
                            detectedBgColor = rgbToHex(p[0], p[1], p[2]);
                        }
                    } catch (err) {
                        console.warn("Could not sample image background", err);
                    }

                    // Start with a small 10% tolerance to eliminate anti-aliased borders
                    const removeBackgroundFilter = new fabric.Image.filters.RemoveColor({
                        distance: 0.10, 
                        color: detectedBgColor 
                    });
                    
                    oImg.filters.push(removeBackgroundFilter);
                    oImg.applyFilters();
                    
                    // Save default blend mode and color data to object for later
                    oImg.set('globalCompositeOperation', 'source-over');
                    oImg.detectedBgColor = detectedBgColor;

                    // Scale and styling
                    oImg.scaleToWidth(canvas.width * 0.6);
                    oImg.set({
                        left: (canvas.width - oImg.getScaledWidth()) / 2,
                        top: (canvas.height - oImg.getScaledHeight()) / 2,
                        transparentCorners: false,
                        cornerColor: '#00f3ff',
                        cornerStrokeColor: '#fff',
                        borderColor: '#00f3ff',
                        cornerSize: 12,
                        padding: 10,
                        cornerStyle: 'circle'
                    });
                    
                    canvas.add(oImg);
                    canvas.setActiveObject(oImg);
                    canvas.renderAll();
                    
                    // Show filter controls and reset UI
                    filterControls.style.display = 'block';
                    bgColorPicker.value = detectedBgColor;
                    toleranceSlider.value = 10;
                    toleranceVal.innerText = '10%';
                    blendModeSelector.value = 'source-over';

                    statusMsg.style.display = 'none';
                }, { crossOrigin: 'anonymous' }); 
            });
        });

        // 3. Event Listeners for UI Controls
        
        // Handle Color Picker change
        bgColorPicker.addEventListener('input', (e) => {
            const hexColor = e.target.value;
            const activeObj = canvas.getActiveObject();
            
            if (activeObj && activeObj.filters) {
                let filter = activeObj.filters.find(f => f.type === 'RemoveColor');
                if (!filter) {
                    filter = new fabric.Image.filters.RemoveColor({ 
                        distance: toleranceSlider.value / 100, 
                        color: hexColor 
                    });
                    activeObj.filters.push(filter);
                } else {
                    filter.color = hexColor;
                }
                activeObj.detectedBgColor = hexColor; // Save custom selected color
                activeObj.applyFilters();
                canvas.renderAll();
            }
        });

        // Handle Chroma Key Slider
        toleranceSlider.addEventListener('input', (e) => {
            const val = e.target.value;
            toleranceVal.innerText = val + '%';

            const activeObj = canvas.getActiveObject();
            if (activeObj && activeObj.filters) {
                let filter = activeObj.filters.find(f => f.type === 'RemoveColor');
                if (!filter) {
                    filter = new fabric.Image.filters.RemoveColor({ 
                        color: bgColorPicker.value 
                    });
                    activeObj.filters.push(filter);
                }
                filter.distance = val / 100;
                activeObj.applyFilters();
                canvas.renderAll();
            }
        });

        // Handle Blend Mode Dropdown
        blendModeSelector.addEventListener('change', (e) => {
            const activeObj = canvas.getActiveObject();
            if (activeObj) {
                activeObj.set('globalCompositeOperation', e.target.value);
                canvas.renderAll();
            }
        });

        // Sync UI when clicking different garments
        function updateUIControls(e) {
            const activeObj = e.selected ? e.selected[0] : null;
            if (activeObj) {
                filterControls.style.display = 'block';
                
                // Sync Blend Mode
                blendModeSelector.value = activeObj.globalCompositeOperation || 'source-over';

                // Sync Filter Values
                const filter = activeObj.filters ? activeObj.filters.find(f => f.type === 'RemoveColor') : null;
                if (filter) {
                    bgColorPicker.value = filter.color || '#ffffff';
                    
                    const currentVal = Math.round(filter.distance * 100);
                    toleranceSlider.value = currentVal;
                    toleranceVal.innerText = currentVal + '%';
                } else {
                    bgColorPicker.value = '#ffffff';
                    toleranceSlider.value = 0;
                    toleranceVal.innerText = '0%';
                }
            } else {
                filterControls.style.display = 'none';
            }
        }

        canvas.on('selection:created', updateUIControls);
        canvas.on('selection:updated', updateUIControls);
        canvas.on('selection:cleared', () => { filterControls.style.display = 'none'; });

        // 4. Reset Canvas
        resetCanvasBtn.addEventListener('click', () => {
            canvas.clear();
            fileInput.value = '';
            uploadArea.style.display = 'flex';
            canvasContainer.style.display = 'none';
            downloadBtn.style.display = 'none';
            filterControls.style.display = 'none';
            document.querySelectorAll('.garment-item').forEach(i => i.classList.remove('selected'));
        });

        // 5. Export & Download final composition
        downloadBtn.addEventListener('click', () => {
            canvas.discardActiveObject(); 
            filterControls.style.display = 'none'; // hide controls in screenshot just in case
            canvas.renderAll();
            
            const dataURL = canvas.toDataURL({ format: 'jpeg', quality: 1 });
            
            const link = document.createElement('a');
            link.download = 'au_heritage_fit_' + Date.now() + '.jpg';
            link.href = dataURL;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            filterControls.style.display = 'block'; // bring back controls
        });

        // Minimal cursor logic just for this page
        const cursorDot = document.querySelector(".cursor-dot");
        const cursorOutline = document.querySelector(".cursor-outline");
        if (cursorDot && cursorOutline) {
            window.addEventListener("mousemove", function (e) {
                cursorDot.style.left = `${e.clientX}px`; cursorDot.style.top = `${e.clientY}px`;
                cursorOutline.animate({ left: `${e.clientX}px`, top: `${e.clientY}px` }, { duration: 500, fill: "forwards" });
            });
        }
    </script>
</body>
</html>