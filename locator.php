<?php 
$pageTitle = "AU // LOCATOR";
include 'includes/header.php'; 
?>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="grid-2" style="min-height: 80vh;">
    <!-- List -->
    <div style="padding: 4rem; border-right: var(--border-thick);">
        <h1 class="display-text" style="font-size: 3rem; margin-bottom: 3rem;">OUTPOSTS</h1>
        
        <div style="margin-bottom: 2rem; padding: 1rem; border: 1px solid #000; background: var(--off-white);">
            <h3 style="margin-bottom: 5px;">01. SENATE HOUSE STORE</h3>
            <p style="font-family: var(--font-tech); font-size: 0.9rem;">Main Campus, Waltair</p>
            <p style="color: green; font-size: 0.8rem; margin-top: 5px;">● OPEN NOW (10:00 - 18:00)</p>
        </div>

        <div style="margin-bottom: 2rem; padding: 1rem; border: 1px solid #ccc;">
            <h3 style="margin-bottom: 5px;">02. ENGINEERING GROUNDS</h3>
            <p style="font-family: var(--font-tech); font-size: 0.9rem;">North Campus, Maddilapalem</p>
            <p style="color: red; font-size: 0.8rem; margin-top: 5px;">● CLOSED</p>
        </div>

        <div style="margin-bottom: 2rem; padding: 1rem; border: 1px solid #ccc;">
            <h3 style="margin-bottom: 5px;">03. BEACH ROAD POP-UP</h3>
            <p style="font-family: var(--font-tech); font-size: 0.9rem;">Near Kali Mata Temple</p>
            <p style="color: orange; font-size: 0.8rem; margin-top: 5px;">● OPENS WEEKENDS ONLY</p>
        </div>
    </div>

    <!-- Fake Map -->
    <div style="background: #e0e0e0; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #666;">
            <h2 style="font-size: 3rem; opacity: 0.5;">[MAP_DATA_LOADING]</h2>
            <p>Connecting to Satellite...</p>
        </div>
        <!-- Grid lines for map effect -->
        <div style="width: 100%; height: 100%; background-image: linear-gradient(#ccc 1px, transparent 1px), linear-gradient(90deg, #ccc 1px, transparent 1px); background-size: 50px 50px; opacity: 0.5;"></div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>