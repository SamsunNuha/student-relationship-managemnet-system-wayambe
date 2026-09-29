<?php
// contact.php - Contact Information Page
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <div style="max-width: 600px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h2><i class="fa-solid fa-envelope" style="color: #3182ce;"></i> Contact University Administration</h2>
            </div>

            <h3 style="color: #0f2c59; margin-bottom: 1rem;">Get in Touch</h3>
            <p style="color: #4a5568; margin-bottom: 1.5rem; font-size: 0.95rem;">
                Have questions regarding admissions, departmental courses, or technical support? Reach us using the details below.
            </p>

            <div style="display: flex; flex-direction: column; gap: 1rem; color: #2d3748; font-size: 0.95rem;">
                <div>
                    <i class="fa-solid fa-location-dot" style="color: #3182ce; width: 25px;"></i>
                    <strong>Address:</strong> University Administration Building, Tech Avenue
                </div>
                <div>
                    <i class="fa-solid fa-phone" style="color: #3182ce; width: 25px;"></i>
                    <strong>Phone:</strong> +94 753360543
                </div>
                <div>
                    <i class="fa-solid fa-envelope-open" style="color: #3182ce; width: 25px;"></i>
                    <strong>Email:</strong> admin@university.edu
                </div>
                <div>
                    <i class="fa-solid fa-clock" style="color: #3182ce; width: 25px;"></i>
                    <strong>Office Hours:</strong> Mon - Fri, 8:00 AM - 5:00 PM
                </div>
            </div>

            <div style="margin-top: 2rem; padding: 1rem; background: #ebf8ff; border-radius: 8px; border-left: 4px solid #3182ce; font-size: 0.92rem; color: #2c5282;">
                <i class="fa-solid fa-circle-info"></i>
                <strong>Want to send a message?</strong> Registered students can <a href="login.php" style="font-weight: 600;">log in to their portal</a> to send messages directly to the administration.
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
