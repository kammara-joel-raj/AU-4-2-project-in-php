<?php 
$pageTitle = "AU HERITAGE // 100 YEARS";
include 'includes/header.php'; 
?>
<style>
    .about-hero {
        background: linear-gradient(rgba(0, 33, 71, 0.9), rgba(0, 33, 71, 0.9)), url('uploads/products/adminBlock.jpg') center/cover;
        color: var(--au-gold);
        padding: 6rem 3%;
        text-align: center;
        border-bottom: var(--border-thick);
    }
    .timeline-container {
        border-left: 2px solid var(--au-blue);
        margin-left: 20px;
        padding-left: 20px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 3rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -27px;
        top: 0;
        width: 12px;
        height: 12px;
        background: var(--au-gold);
        border: 2px solid var(--au-blue);
        border-radius: 50%;
    }
    .year-badge {
        font-family: var(--font-tech);
        background: var(--au-blue);
        color: var(--paper-white);
        padding: 5px 10px;
        display: inline-block;
        margin-bottom: 10px;
        font-weight: bold;
    }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<section class="about-hero">
    <h3 style="font-family: var(--font-tech); letter-spacing: 3px; margin-bottom: 1rem; color: #fff;">1926 — 2026</h3>
    <h1 class="display-text glitch" data-text="AU@100">AU@100</h1>
    <p style="font-family: var(--font-street); font-size: 1.2rem; max-width: 700px; margin: 2rem auto 0; color: var(--paper-white);">
        Celebrating a Century of Academic Excellence. From our founding by Sir C.R. Reddy to our modern vision for the future, Andhra University remains a beacon of knowledge on the coast of Visakhapatnam.
    </p>
</section>

<section class="grid-2" style="border-bottom: var(--border-thick);">
    <div style="padding: 4rem; border-right: var(--border-thick);">
        <h2 style="font-size: 3rem; color: var(--au-blue); margin-bottom: 2rem; line-height: 1;">THE VISION<br>DOCUMENT</h2>
        <p style="font-family: var(--font-street); font-size: 1.1rem; line-height: 1.8; color: #444; margin-bottom: 2rem;">
            As we step into our second century, AU is evolving. Our <strong>Centenary Vision Document</strong> outlines a massive transformation: blending our rich Indo-Saracenic architectural heritage with cutting-edge tech.
        </p>
        <ul style="font-family: var(--font-tech); line-height: 2; color: #222; list-style-type: square; padding-left: 20px;">
            <li>Renovation of Legacy Structures & Hostels</li>
            <li>Launch of the AU Ratan Tata Innovation Hub</li>
            <li>New Interdisciplinary AI & Sustainability Courses</li>
            <li>"100 Patents Drive" for Student Researchers</li>
        </ul>
    </div>
    
    <div style="padding: 4rem; background: var(--off-white);">
        <h2 style="font-size: 2rem; margin-bottom: 3rem;">A LEGACY TIMELINE</h2>
        <div class="timeline-container">
            <div class="timeline-item">
                <div class="year-badge">1926</div>
                <h3 style="font-family: var(--font-varsity); margin-bottom: 5px;">THE FOUNDATION</h3>
                <p style="font-family: var(--font-street); font-size: 0.95rem;">Constituted by the Madras Act of 1926. Sir C.R. Reddy serves as the visionary founder Vice-Chancellor.</p>
            </div>
            <div class="timeline-item">
                <div class="year-badge">1931</div>
                <h3 style="font-family: var(--font-varsity); margin-bottom: 5px;">RADHAKRISHNAN ERA</h3>
                <p style="font-family: var(--font-street); font-size: 0.95rem;">Former President of India, Dr. Sarvepalli Radhakrishnan, takes over as Vice-Chancellor, elevating AU's academic rigor globally.</p>
            </div>
            <div class="timeline-item">
                <div class="year-badge">1933</div>
                <h3 style="font-family: var(--font-varsity); margin-bottom: 5px;">ENGINEERING PIONEERS</h3>
                <p style="font-family: var(--font-street); font-size: 0.95rem;">AU becomes the first university in India to establish a dedicated Department of Chemical Engineering.</p>
            </div>
            <div class="timeline-item">
                <div class="year-badge">2026</div>
                <h3 style="font-family: var(--font-varsity); margin-bottom: 5px; color: var(--au-blue);">SHATABDI MAHOTSAV</h3>
                <p style="font-family: var(--font-street); font-size: 0.95rem;">The Centenary Celebrations begin. 100 years of empowering students, marked by the Vaarotsavam cultural week and global alumni meets.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>