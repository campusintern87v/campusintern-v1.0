<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Campus Placements</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .placement-section {
            margin: 80px auto;
            padding: 20px;
        }

        .carousel-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }

        .carousel-inner {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border: 8px solid white;
            background: white;
        }

        .carousel-inner img {
            max-height: 650px;
            width: auto;
            margin: 0 auto;
            display: block;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .carousel-item:hover img {
            transform: scale(1.02);
        }

        .carousel-control-prev, .carousel-control-next {
            width: 50px;
            height: 50px;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 1;
            transition: all 0.3s ease;
        }

        .carousel-control-prev:hover, .carousel-control-next:hover {
            background-color: var(--secondary-color);
        }

        .carousel-control-prev {
            left: 20px;
        }

        .carousel-control-next {
            right: 20px;
        }

        .carousel-indicators [data-bs-target] {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 6px;
            background-color: var(--primary-color);
            border: 2px solid white;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .carousel-indicators [data-bs-target].active {
            background-color: var(--accent-color);
            opacity: 1;
            transform: scale(1.2);
        }

        .carousel-indicators {
            bottom: -40px;
        }

        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 50px;
            color: var(--primary-color);
            font-weight: 700;
        }

        .section-title::after {
            content: '';
            position: absolute;
            width: 60%;
            height: 4px;
            background: var(--secondary-color);
            bottom: -10px;
            left: 20%;
            border-radius: 2px;
        }

        @media (max-width: 768px) {
            .carousel-inner img {
                max-height: 500px;
            }
            
            .carousel-control-prev, .carousel-control-next {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 576px) {
            .carousel-inner img {
                max-height: 400px;
            }
            
            .placement-section {
                margin: 50px auto;
            }
        }
    </style>
</head>
<body>
<?php include "../includes/header.php"; ?>
    <div class="container placement-section text-center">
        <h2 class="section-title">OUR PLACED STUDENTS</h2>

        <!-- Enhanced Carousel -->
        <div class="carousel-container">
            <div id="placementCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#placementCarousel" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#placementCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#placementCarousel" data-bs-slide-to="2"></button>
                    <button type="button" data-bs-target="#placementCarousel" data-bs-slide-to="3"></button>
                    <button type="button" data-bs-target="#placementCarousel" data-bs-slide-to="4"></button>
                    <button type="button" data-bs-target="#placementCarousel" data-bs-slide-to="5"></button>
                    <button type="button" data-bs-target="#placementCarousel" data-bs-slide-to="6"></button>
                </div>

                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="images/placement1.jpg" class="d-block" alt="Placement Certificate">
                    </div>
                    <div class="carousel-item">
                        <img src="images/placement2.jpg" class="d-block" alt="Placement Certificate">
                    </div>
                    <div class="carousel-item">
                        <img src="images/placement3.jpg" class="d-block" alt="Placement Certificate">
                    </div>
                    <div class="carousel-item">
                        <img src="images/placement4.jpg" class="d-block" alt="Placement Certificate">
                    </div>
                    <div class="carousel-item">
                        <img src="images/placement5.jpg" class="d-block" alt="Placement Certificate">
                    </div>
                    <div class="carousel-item">
                        <img src="images/placement6.jpg" class="d-block" alt="Placement Certificate">
                    </div>
                    <div class="carousel-item">
                        <img src="images/placement.jpg" class="d-block" alt="Placement Certificate">
                    </div>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#placementCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#placementCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include "../includes/footer.php"; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS for animations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scroll behavior
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
            
            // Add animation to carousel items
            const carouselItems = document.querySelectorAll('.carousel-item');
            carouselItems.forEach(item => {
                item.addEventListener('mouseenter', () => {
                    item.querySelector('img').style.transform = 'scale(1.03)';
                });
                item.addEventListener('mouseleave', () => {
                    item.querySelector('img').style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>