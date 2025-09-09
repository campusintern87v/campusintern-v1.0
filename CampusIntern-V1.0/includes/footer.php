<?php
/**
 * Site Footer Template
 * Includes copyright, scripts, and closing tags
 */
?>
<footer style="background-color: #000; padding: 40px 0; font-family: 'Arial', sans-serif; color: #fff;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
            <div style="margin-bottom: 20px;">
                <h3 style="color: #ff6b00; font-size: 18px; margin-bottom: 15px;">Quick Links</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 8px;"><a href="https://jaihindcollege.com/jaihindcollege-new/" style="color: #ccc; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ff6b00'" onmouseout="this.style.color='#ccc'">College New Website</a></li>
                    <li style="margin-bottom: 8px;"><a href="https://www.jaihindcollege.com/index.html" style="color: #ccc; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ff6b00'" onmouseout="this.style.color='#ccc'">College Old Website</a></li>
                    <li style="margin-bottom: 8px;"><a href="https://www.jhcdotcomclub.com/" style="color: #ccc; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ff6b00'" onmouseout="this.style.color='#ccc'">Our Club Website</a></li>
                    <li style="margin-bottom: 8px;"><a href="https://www.linkedin.com/school/jai-hind-college-mumbai/?originalSubdomain=in" style="color: #ccc; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ff6b00'" onmouseout="this.style.color='#ccc'">LinkedIn</a></li>
                </ul>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #ff6b00; font-size: 18px; margin-bottom: 15px;">Follow Us</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 8px;">
                        <a href="https://www.instagram.com/jhc_it_sd_bda_dept?igsh=Y3o1eXBiNzVtam4x" style="color: #ccc; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ff6b00'" onmouseout="this.style.color='#ccc'">
                            <i class="fab fa-instagram" style="margin-right: 8px; color: #ff6b00;"></i>Our Department
                        </a>
                    </li>
                    <li style="margin-bottom: 8px;">
                        <a href="https://www.instagram.com/dotcomclubjhc?igsh=MXU2ejdlcGkwbHMzag==" style="color: #ccc; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ff6b00'" onmouseout="this.style.color='#ccc'">
                            <i class="fas fa-users" style="margin-right: 8px; color: #ff6b00;"></i> Our Clubs
                        </a>
                    </li>
                </ul>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #ff6b00; font-size: 18px; margin-bottom: 15px;">Contact</h3>
                <p style="color: #ccc; margin-bottom: 8px;"><i class="fas fa-envelope" style="margin-right: 8px; color: #ff6b00;"></i> campusintern87@gmail.com</p>
                <p style="color: #ccc; margin-bottom: 8px;"><i class="fas fa-clock" style="margin-right: 8px; color: #ff6b00;"></i> Help: 24x7</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #333;">
            <p style="color: #aaa; font-size: 14px;">© 2025 CampusIntern by Jai Hind College. All rights reserved.</p>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        @media (max-width: 768px) {
            footer div {
                flex-direction: column;
                text-align: center;
            }
            
            footer div div {
                margin-bottom: 25px;
            }
        }
    </style>
</footer>

<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>

</body>
</html>