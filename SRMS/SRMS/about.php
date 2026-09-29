<?php
// about.php - About SRMS
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <div class="card" style="padding: 2.5rem;">
        <div class="card-header">
            <h2><i class="fa-solid fa-circle-info" style="color: #3182ce;"></i> About SRMS (Student Relationship Management System)</h2>
        </div>
        
        <p style="font-size: 1.05rem; color: #4a5568; line-height: 1.8; margin-bottom: 1.5rem;">
            The <strong>Student Relationship Management System (SRMS)</strong> is designed to foster seamless communication, tracking, and administration between university departments and students. Built with reliability, security, and clarity in mind, SRMS simplifies student record management, announcements, and direct messaging support.
        </p>

        <h3 style="color: #0f2c59; margin-bottom: 1rem; margin-top: 1.5rem;"><i class="fa-solid fa-bullseye"></i> Key Objectives</h3>
        <ul style="margin-left: 1.5rem; color: #4a5568; line-height: 1.8; margin-bottom: 2rem;">
            <li>Maintain centralized, up-to-date student profile records across academic departments.</li>
            <li>Enable swift communication between administrators and students via official campus announcements.</li>
            <li>Provide a structured ticket-based messaging system for student inquiries, transcript requests, and course guidance.</li>
            <li>Ensure fast, responsive access across desktop and mobile browsers with zero external framework overhead.</li>
        </ul>

        <h3 style="color: #0f2c59; margin-bottom: 1rem;"><i class="fa-solid fa-layer-group"></i> Supported Departments</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 1rem;">
            <div style="background: #edf2f7; padding: 1rem; border-radius: 6px; text-align: center; font-weight: 600; color: #0f2c59;">
                <i class="fa-solid fa-laptop-code"></i> Computer Science
            </div>
            <div style="background: #edf2f7; padding: 1rem; border-radius: 6px; text-align: center; font-weight: 600; color: #0f2c59;">
                <i class="fa-solid fa-calculator"></i> Mathematics
            </div>
            <div style="background: #edf2f7; padding: 1rem; border-radius: 6px; text-align: center; font-weight: 600; color: #0f2c59;">
                <i class="fa-solid fa-atom"></i> Physics
            </div>
            <div style="background: #edf2f7; padding: 1rem; border-radius: 6px; text-align: center; font-weight: 600; color: #0f2c59;">
                <i class="fa-solid fa-flask"></i> Chemistry
            </div>
            <div style="background: #edf2f7; padding: 1rem; border-radius: 6px; text-align: center; font-weight: 600; color: #0f2c59;">
                <i class="fa-solid fa-dna"></i> Biology
            </div>
            <div style="background: #edf2f7; padding: 1rem; border-radius: 6px; text-align: center; font-weight: 600; color: #0f2c59;">
                <i class="fa-solid fa-chart-line"></i> Statistics
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
