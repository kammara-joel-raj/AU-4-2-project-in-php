AU Archives // Centennial E-Commerce & Virtual Fit Lab

A full-stack e-commerce and interactive virtual try-on platform built as a Final Year B.Tech Computer Science project.

The Vision: Designed to celebrate Andhra University's 100-Year Centennial (1926–2026). This platform bridges the university's historic academic legacy with modern streetwear culture, allowing students and alumni to purchase archival-inspired merchandise (like the Senate House Hoodie or Vice Chancellor Jacket).

Key Features

1. Zero-Latency Virtual Try-On (VTON 2.5)

We completely engineered the virtual try-on experience to run 100% locally in the user's browser without relying on expensive, slow, or rate-limited backend AI APIs.

Canvas Compositing: Powered by Fabric.js, users can upload full-body photos and drag, drop, scale, and rotate garments.

Dynamic Background Removal (Chroma Key): Built-in color picker and tolerance sliders allow users to perfectly key-out studio backgrounds from product images in real-time.

Advanced Blend Modes: Utilizes HTML5 Canvas globalCompositeOperation (Multiply/Darken) to seamlessly blend white or light-colored clothing onto the user without green-screen tearing.

Privacy First: Because image processing happens client-side, user photos are never uploaded to an external AI server.

2. Modern Full-Stack E-Commerce Architecture

Secure Backend: PHP with PDO (PHP Data Objects) for secure, SQL-injection-resistant database queries.

State Management: Robust session-based cart and checkout flow.

Dynamic Filtering: Modern shop interface with real-time category, price, and rating filters.

Immersive UI/UX: Custom brutalist/tech aesthetic featuring CSS glitch effects, custom cursors, and responsive CSS Grid layouts.

Technology Stack

Frontend: HTML5, Custom CSS3, Vanilla JavaScript, Fabric.js (HTML5 Canvas Library)

Backend: PHP (Session Management, Routing)

Database: MySQL / MariaDB

Architecture: Monolithic MVC-inspired structure

Local Setup Instructions

This project requires a local server environment like XAMPP, WAMP, or MAMP.

Clone the Repository:

git clone [https://github.com/kammara-joel-raj/AU-4-2-project-in-php.git](https://github.com/kammara-joel-raj/AU-4-2-project-in-php.git)


Move to Server Directory:
Move the project folder into your local server's root directory (e.g., C:\xampp\htdocs\au-archives).

Database Configuration:

Open phpMyAdmin (http://localhost/phpmyadmin).

Create a new database named lol (or update includes/db.php with your preferred database name).

Import the provided SQL schema: sql/database.sql.

Run the Application:

Start Apache and MySQL in your XAMPP Control Panel.

Open your browser and navigate to: http://localhost/au-archives/index.php

(Note: No external API keys are required! The Virtual Try-On runs entirely in the browser.)

How to use the Virtual Fit Lab

Navigate to Lab in the top menu.

Upload a photo: Provide a clear, full-body picture.

Select an Asset: Click a garment from the left panel.

Blend & Key: * If the garment has a white background, change the Blend Mode to MULTIPLY.

For colored backgrounds, use the Target Color picker to select the background color, then adjust the Tolerance Slider to make it transparent.

Adjust & Export: Drag the garment into place, resize using the corner handles, and click DOWNLOAD COMPOSITION to save your fit!

Academic Context

This repository represents a capstone computer science project demonstrating proficiency in full-stack web development, relational database design, and client-side image processing.

Designed and Developed in Visakhapatnam, India.