<?php 
$pageTitle = "AU // CENTENARY LOCATOR";
include 'includes/header.php'; 
?>
<style>
    .location-card {
        padding: 1.5rem; 
        border: 1px solid #ccc; 
        margin-bottom: 1.5rem;
        transition: 0.3s;
        background: #fff;
        position: relative;
        overflow: hidden;
    }
    .location-card:hover {
        border-color: var(--au-blue);
        box-shadow: 5px 5px 0px var(--au-blue);
        transform: translateY(-3px);
    }
    .status-indicator {
        display: inline-block;
        width: 10px; height: 10px;
        border-radius: 50%;
        margin-right: 5px;
    }
    .status-open { background: #00f3ff; box-shadow: 0 0 8px #00f3ff; }
    .status-closed { background: #ff1744; box-shadow: 0 0 8px #ff1744; }
    .status-event { background: var(--au-gold); box-shadow: 0 0 8px var(--au-gold); }
    
    /* Map Container & Dark Blue Overlay */
    .map-container {
        position: relative; 
        /* Image is in the same directory as locator.php */
        background: url('andhra_university_midnight_blue_20260419_155234.jpg') center/cover no-repeat;
        background-color: #0a1128; /* Fallback color */
        min-height: 85vh;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .map-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        /* Deepens the blue to match the brand while keeping the gold roads visible */
        background: linear-gradient(rgba(0, 33, 71, 0.4), rgba(0, 15, 30, 0.8));
        pointer-events: none;
    }

    /* Radar Animation */
    .radar {
        position: absolute; top: 55%; left: 45%; transform: translate(-50%, -50%);
        width: 350px; height: 350px; border-radius: 50%;
        border: 1px solid rgba(0, 243, 255, 0.2);
        background: radial-gradient(circle, rgba(0, 243, 255, 0.05) 0%, transparent 70%);
        pointer-events: none;
    }
    .radar::before {
        content: ''; position: absolute; top: 50%; left: 50%;
        width: 175px; height: 175px; 
        background: linear-gradient(45deg, rgba(0, 243, 255, 0.3) 0%, transparent 60%);
        transform-origin: 0 0; animation: scan 3s linear infinite;
    }
    
    /* Location Blips on the Map */
    .radar-blip {
        position: absolute; border-radius: 50%;
        animation: blink 2s infinite;
        z-index: 5;
    }
    .blip-hq { width: 12px; height: 12px; background: #00f3ff; box-shadow: 0 0 15px #00f3ff; top: 55%; left: 45%; }
    .blip-coast { width: 14px; height: 14px; background: var(--au-gold); box-shadow: 0 0 15px var(--au-gold); top: 68%; left: 85%; }
    .blip-north { width: 10px; height: 10px; background: #ff1744; box-shadow: 0 0 15px #ff1744; top: 25%; left: 55%; }

    @keyframes scan { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes blink { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.3); } }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="grid-2" style="min-height: 85vh;">
    <!-- List -->
    <div style="padding: 3rem 4rem; border-right: var(--border-thick); background: var(--paper-white); overflow-y: auto; max-height: 85vh;">
        <p style="font-family: var(--font-tech); color: #666; margin-bottom: 10px;">// OFFICIAL MERCHANDISE OUTPOSTS</p>
        <h1 class="display-text" style="font-size: 3.5rem; margin-bottom: 3rem; line-height: 1;">CAMPUS<br>LOCATOR</h1>
        
        <div class="location-card" style="border: 2px solid var(--au-blue); background: var(--off-white);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h3 style="margin-bottom: 5px; font-size: 1.3rem;">01. THE SENATE HOUSE [HQ]</h3>
                <span class="badge" style="position: static; transform: none;">MAIN STORE</span>
            </div>
            <p style="font-family: var(--font-tech); font-size: 0.9rem; color: #555; margin: 10px 0;">South Campus, Waltair, Visakhapatnam 530003</p>
            <p style="font-family: var(--font-tech); font-size: 0.8rem; margin-top: 15px; color: var(--au-blue); font-weight: bold;">
                <span class="status-indicator status-open"></span> OPEN NOW (09:00 - 18:00)
            </p>
        </div>

        <div class="location-card">
            <h3 style="margin-bottom: 5px; font-size: 1.3rem;">02. AU CONVENTION CENTRE</h3>
            <p style="font-family: var(--font-tech); font-size: 0.9rem; color: #555; margin: 10px 0;">Beach Road, Pandurangapuram</p>
            <p style="font-size: 0.85rem; color: #444; margin-bottom: 10px;">* Exclusive Centenary Collection available here during the Shatabdi Mahotsav events.</p>
            <p style="font-family: var(--font-tech); font-size: 0.8rem; color: var(--au-blue); font-weight: bold;">
                <span class="status-indicator status-event"></span> CENTENARY POP-UP
            </p>
        </div>

        <div class="location-card">
            <h3 style="margin-bottom: 5px; font-size: 1.3rem;">03. ENGINEERING COLLEGE GROUNDS</h3>
            <p style="font-family: var(--font-tech); font-size: 0.9rem; color: #555; margin: 10px 0;">North Campus (AUCE), Maddilapalem</p>
            <p style="font-family: var(--font-tech); font-size: 0.8rem; color: #666; font-weight: bold;">
                <span class="status-indicator status-closed"></span> OPENS APRIL 26TH FOR GRAND FINALE
            </p>
        </div>
        
        <div class="location-card">
            <h3 style="margin-bottom: 5px; font-size: 1.3rem;">04. TLN SABHA HALL</h3>
            <p style="font-family: var(--font-tech); font-size: 0.9rem; color: #555; margin: 10px 0;">Arts College Building</p>
            <p style="font-size: 0.85rem; color: #444; margin-bottom: 10px;">Visit the Journalism Dept's 100-Year Photo Exhibition to purchase archival prints.</p>
            <p style="font-family: var(--font-tech); font-size: 0.8rem; color: var(--au-blue); font-weight: bold;">
                <span class="status-indicator status-open"></span> EXHIBITION STORE (10:00 - 16:00)
            </p>
        </div>
    </div>

    <!-- UI Map -->
    <div class="map-container">
        <!-- Deep Blue Overlay -->
        <div class="map-overlay"></div>
        
        <!-- Radar Sweep -->
        <div class="radar"></div>

        <!-- Coordinates perfectly aligned with the streets on the image -->
        <!-- HQ (Center-left) -->
        <div class="radar-blip blip-hq" title="Senate House"></div> 
        <!-- Beach Road (Right Edge) -->
        <div class="radar-blip blip-coast" title="Convention Centre"></div> 
        <!-- AUCE (Top) -->
        <div class="radar-blip blip-north" title="Engineering College"></div> 

        <!-- HUD UI -->
        <div style="position: absolute; bottom: 20px; right: 20px; font-family: var(--font-tech); font-size: 0.8rem; text-align: right; color: #00f3ff; background: rgba(0, 15, 30, 0.85); padding: 15px; border: 1px solid rgba(0, 243, 255, 0.3); backdrop-filter: blur(4px);">
            LAT: 17.7305° N<br>
            LONG: 83.3228° E<br>
            SYS: VISAKHAPATNAM_GRID<br>
            <span style="color: var(--au-gold); display: inline-block; margin-top: 5px;">[SYNCED TO AU SATELLITE]</span>
        </div>

        <!-- Subtle scanning lines overlay to maintain the "Tech" aesthetic -->
        <div style="position: absolute; width: 100%; height: 100%; background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.1) 2px, rgba(0,0,0,0.1) 4px); pointer-events: none;"></div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>