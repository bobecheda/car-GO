<?php
// Hero Section for Car Rental Landing Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Drive Your Dream Car | Car Rental</title>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Landing Styles -->
    <link rel="stylesheet" href="./styles/landing.css">
</head>
<body>
    <!-- Hero Section -->
    <section id="hero" class="hero" aria-label="Hero">
        <!-- Background image (royalty-free Unsplash) -->
       <div class="hero-bg" style="background-image: url('../uploads/1763813929_pexels-lalesh-168938.jpg');"></div>

        <!-- Overlay for readability -->
        <div class="hero-overlay"></div>

        <nav class="site-nav" aria-label="Primary">
            <div class="nav-inner">
                <div class="nav-brand">Car-Go Rentals</div>
                <button class="nav-toggle" id="navToggle" aria-label="Toggle Menu"><i class="fa-solid fa-bars"></i></button>
                <div class="nav-links" id="navLinks">
                    <a href="#hero" class="nav-link">Home</a>
                    <a href="#about" class="nav-link">About</a>
                    <a href="#popular" class="nav-link">Popular Vehicles</a>
                    <a href="#stats" class="nav-link">Achievements</a>
                    <a href="#testimonials" class="nav-link">Testimonials</a>
                    <a href="#faq" class="nav-link">FAQs</a>
                    <a href="#contact" class="nav-link">Contact</a>
                    <a href="../login.php" class="btn btn-primary nav-login"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                </div>
            </div>
        </nav>

        <div class="hero-inner">
            <div class="hero-content fade-in">
                <h1 class="hero-title">Drive Your Dream Car Today</h1>
                <p class="hero-sub">Premium, affordable, and reliable car rentals at your fingertips.Find the perfect vehicle for your trip at Car-GO.</p>
                <div class="hero-ctas">
                    <a href="../login.php" class="btn btn-primary"><i class="fa-solid fa-calendar-check"></i> Book a Car</a>
                    <a href="../login.php" class="btn btn-primary"><i class="fa-solid fa-car-side"></i> View Vehicles</a>
                </div>
            </div>

            <!-- Search / Filter Box -->
            <form class="hero-search slide-up" action="#" method="get" aria-label="Search vehicles">
                <div class="search-grid">
                    <div class="search-field">
                        <label for="location" class="sr-only">Location</label>
                        <i class="fa-solid fa-location-dot"></i>
                        <input id="location" name="location" type="text" placeholder="Pickup Location">
                    </div>
                    <div class="search-field">
                        <label for="pickup" class="sr-only">Pickup date</label>
                        <i class="fa-regular fa-calendar"></i>
                        <input id="pickup" name="pickup" type="date" placeholder="Pickup date">
                    </div>
                    <div class="search-field">
                        <label for="return" class="sr-only">Return date</label>
                        <i class="fa-regular fa-calendar-check"></i>
                        <input id="return" name="return" type="date" placeholder="Return date">
                    </div>
                    <button type="submit" class="btn btn-search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                </div>
            </form>
        </div>
    </section>
    <div class="section-transition" aria-hidden="true"></div>

    <section id="about" class="about" aria-label="About Car-Go Rentals">
        <div class="about-inner">
            <div class="about-grid">
                <figure class="about-image reveal">
                    <img src="../uploads/1763811321_pexels-daryl-johnson-165513825-13741315.jpg" alt="Car-Go Rentals fleet" loading="lazy">
                </figure>
            <div class="about-content reveal">
                <p class="about-subtitle">Reliable. Affordable. Customer-Focused.</p>
                <h2 class="about-title">About Car-Go Rentals</h2>
                <p class="about-text">Car-Go Rentals is a trusted car rental service offering a wide range of vehicles—from compact city cars to premium SUVs—tailored for every journey. We combine transparent pricing, flexible plans, and exceptional support to deliver a seamless experience you can rely on. With years of service and thousands of satisfied customers, we make renting fast, safe, and effortless.</p>
                <ul class="feature-list">
                    <li class="feature-item">
                        <span class="feature-icon"><i class="fa-solid fa-car-side"></i></span>
                        <div class="feature-content">
                            <h3>Wide Variety of Vehicles</h3>
                            <p>From economy to luxury, choose the perfect ride.</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <span class="feature-icon"><i class="fa-solid fa-tags"></i></span>
                        <div class="feature-content">
                            <h3>Affordable Pricing</h3>
                            <p>Clear, competitive rates with no hidden fees.</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <span class="feature-icon"><i class="fa-solid fa-bolt"></i></span>
                        <div class="feature-content">
                            <h3>Fast & Easy Booking</h3>
                            <p>Reserve in minutes with a modern booking flow.</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <span class="feature-icon"><i class="fa-solid fa-shield"></i></span>
                        <div class="feature-content">
                            <h3>Safe & Secure Rentals</h3>
                            <p>Well-maintained cars and trusted support.</p>
                        </div>
                    </li>
                </ul>
                <div class="about-cta">
                    <a href="../login.php" class="btn btn-accent">View Our Fleet</a>
                </div>
            </div>
            </div>
        </div>
    </section>

    <section id="popular" class="vehicles" aria-label="Popular Vehicles">
        <div class="vehicles-inner">
            <div class="vehicles-header two-col">
                <div class="vh-left">
                    <h1 class="vehicles-title">Popular Vehicles</h1>
                    <p class="vehicles-eyebrow">Choose Your Drive</p>
                    <p class="vehicles-subtitle">Top choices loved by our customers</p>
                    <a href="../login.php" class="btn btn-accent">Explore Collection</a>
                </div>
            </div>
            <div class="vehicles-grid">
                <article class="vehicle-card reveal">
                    <div class="vehicle-media">
                        <img src="../uploads/1763813672_pexels-introspectivedsgn-10012842.jpg" alt="Toyota Corolla">
                        <span class="vehicle-brand">Toyota</span>
                        <h3 class="vehicle-name">Toyota Corolla</h3>
                        <div class="vehicle-rent"><span class="rent-label">Rent Price</span><span class="rent-value">Ksh45000/day</span></div>
                    </div>
                </article>

                <article class="vehicle-card reveal">
                    <div class="vehicle-media">
                        <img src="../uploads/pexels-javon-swaby-197616-2779447.jpg" alt="Honda CR-V">
                        <span class="vehicle-brand">Honda</span>
                        <h3 class="vehicle-name">Honda CR-V</h3>
                        <div class="vehicle-rent"><span class="rent-label">Rent Price</span><span class="rent-value">Ksh68000/day</span></div>
                    </div>
                </article>

                <article class="vehicle-card reveal">
                    <div class="vehicle-media">
                        <img src="../uploads/1763813822_pexels-bertellifotografia-12446377.jpg" alt="Nissan X-Trail">
                        <span class="vehicle-brand">Nissan</span>
                        <h3 class="vehicle-name">Nissan X-Trail</h3>
                        <div class="vehicle-rent"><span class="rent-label">Rent Price</span><span class="rent-value">Ksh64000/day</span></div>
                    </div>
                </article>

                <article class="vehicle-card reveal">
                    <div class="vehicle-media">
                        <img src="../uploads/1763813769_pexels-ganinph-7716430.jpg" alt="Toyota Hiace">
                        <span class="vehicle-brand">Toyota</span>
                        <h3 class="vehicle-name">Toyota Hiace</h3>
                        <div class="vehicle-rent"><span class="rent-label">Rent Price</span><span class="rent-value">Ksh75000/day</span></div>
                    </div>
                </article>

                <article class="vehicle-card reveal">
                    <div class="vehicle-media">
                        <img src="../uploads/1763810640_pexels-tahir-x-lf-2153788153-32943798.jpg" alt="BMW X5">
                        <span class="vehicle-brand">BMW</span>
                        <h3 class="vehicle-name">BMW X5</h3>
                        <div class="vehicle-rent"><span class="rent-label">Rent Price</span><span class="rent-value">Ksh120000/day</span></div>
                    </div>
                </article>

                <article class="vehicle-card reveal">
                    <div class="vehicle-media">
                        <img src="../uploads/pexels-mikebirdy-3729464.jpg" alt="Mercedes C-Class">
                        <span class="vehicle-brand">Mercedes</span>
                        <h3 class="vehicle-name">Mercedes C-Class</h3>
                        <div class="vehicle-rent"><span class="rent-label">Rent Price</span><span class="rent-value">Ksh99000/day</span></div>
                    </div>
                </article>

                <article class="vehicle-card reveal">
                    <div class="vehicle-media">
                        <img src="../uploads/pexels-alteredsnaps-34716467.jpg" alt="Subaru Forester">
                        <span class="vehicle-brand">Subaru</span>
                        <h3 class="vehicle-name">Subaru Forester</h3>
                        <div class="vehicle-rent"><span class="rent-label">Rent Price</span><span class="rent-value">Ksh80000/day</span></div>
                    </div>
                </article>

                <article class="vehicle-card reveal">
                    <div class="vehicle-media">
                        <img src="../uploads/pexels-introspectivedsgn-17507723.jpg" alt="Toyota RAV4">
                        <span class="vehicle-brand">Toyota</span>
                        <h3 class="vehicle-name">Toyota RAV4</h3>
                        <div class="vehicle-rent"><span class="rent-label">Rent Price</span><span class="rent-value">Ksh70000/day</span></div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="stats" class="stats" aria-label="Our Achievements">
        <div class="stats-inner">
            <div class="stats-header">
                <p class="stats-subtitle">Proud milestones that define our growth</p>
                <h2 class="stats-title">Our Achievements</h2>
            </div>
            <div class="stats-grid">
                <article class="stat-card reveal">
                    <div class="stat-icon"><i class="fa-solid fa-car"></i></div>
                    <div class="stat-number count" data-target="1200" data-suffix="+">0+</div>
                    <div class="stat-label">Rentals Completed</div>
                </article>
                <article class="stat-card reveal">
                    <div class="stat-icon"><i class="fa-solid fa-face-smile"></i></div>
                    <div class="stat-number count" data-target="900" data-suffix="+">0+</div>
                    <div class="stat-label">Happy Customers</div>
                </article>
                <article class="stat-card reveal">
                    <div class="stat-icon"><i class="fa-solid fa-car-side"></i></div>
                    <div class="stat-number count" data-target="150" data-suffix="+">0+</div>
                    <div class="stat-label">Vehicles Available</div>
                </article>
                <article class="stat-card reveal">
                    <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
                    <div class="stat-number count" data-target="5" data-suffix="+">0+</div>
                    <div class="stat-label">Years of Service</div>
                </article>
            </div>
        </div>
    </section>

    <section id="testimonials" class="testimonials" aria-label="What Our Customers Say">
        <div class="t-inner">
            <div class="t-header">
                <p class="t-subtitle">Real experiences from our loyal clients</p>
                <h2 class="t-title">What Our Customers Say</h2>
            </div>
            <div class="t-controls">
                <button class="t-btn" id="t-prev"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="t-btn" id="t-next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
            <div class="t-carousel" id="t-carousel">
                <article class="t-card reveal">
                    <figure class="t-media"><img src="../uploads/pexels-justin-shaifer-501272-1222271.jpg" alt="James M"></figure>
                    <div class="t-body">
                        <div class="t-name">James M.</div>
                        <div class="t-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="t-text">Car-Go Rentals made my trip smooth and stress-free. The car was spotless and the service was outstanding!</p>
                    </div>
                </article>
                <article class="t-card reveal">
                    <figure class="t-media"><img src="../uploads/pexels-danxavier-1239291.jpg" alt="Aisha K"></figure>
                    <div class="t-body">
                        <div class="t-name">Aisha K.</div>
                        <div class="t-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="t-text">Amazing customer support and fair pricing. I've rented twice already — highly recommended!</p>
                    </div>
                </article>
                <article class="t-card reveal">
                    <figure class="t-media"><img src="../uploads/pexels-thgusstavo-1933873.jpg" alt="Samuel O"></figure>
                    <div class="t-body">
                        <div class="t-name">Samuel O.</div>
                        <div class="t-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i></div>
                        <p class="t-text">The booking process was seamless and fast. Car quality exceeded my expectations.</p>
                    </div>
                </article>
                <article class="t-card reveal">
                    <figure class="t-media"><img src="../uploads/pexels-olly-712513.jpg" alt="Grace W"></figure>
                    <div class="t-body">
                        <div class="t-name">Grace W.</div>
                        <div class="t-rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="t-text">Affordable, professional, and reliable. Car-Go Rentals never disappoints.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="faq" class="faq" aria-label="Frequently Asked Questions">
        <div class="faq-inner">
            <div class="faq-header">
                <p class="faq-sub">Everything you need to know before renting with Car-Go Rentals</p>
                <h2 class="faq-title">Frequently Asked Questions</h2>
            </div>
            <div class="faq-list">
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">What do I need to rent a car?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>You will need a valid national ID or passport, a valid driver’s license, and a payment method. Some cars may require a minimum age or additional verification.</p>
                    </div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">Can I rent a car without a credit card?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>Yes! Car-Go Rentals accepts mobile money, debit cards, and cash payments depending on the branch. However, a refundable deposit may still be required.</p>
                    </div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">Do you offer delivery or pickup services?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>Yes. We offer delivery and pickup to your home, office, airport, or hotel at an additional fee depending on distance.</p>
                    </div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">What happens if the car breaks down?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>We provide 24/7 roadside assistance. Call our support immediately and we will either fix the issue or replace the vehicle as soon as possible.</p>
                    </div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">Do I need to return the car with a full tank?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>Yes. Cars should be returned with the same fuel level they were picked up with. Otherwise, a refueling fee will apply.</p>
                    </div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">Is there a mileage limit?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>Most of our vehicles come with unlimited mileage. Some premium or specialty cars may have daily mileage limits specified during booking.</p>
                    </div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">Can I extend my rental period?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>Absolutely! You can request an extension through your dashboard or by contacting customer support. Additional charges may apply.</p>
                    </div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">What payment methods do you accept?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>We accept mobile money (M-Pesa), Visa, Mastercard, bank transfers, and cash depending on the branch.</p>
                    </div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">What if I return the car late?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>A late return fee may apply depending on how many hours you exceed your agreed return time.</p>
                    </div>
                </article>
                <article class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-qtext">Can I cancel my booking?</span>
                        <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p>Yes. You can cancel anytime. Refunds depend on how early the cancellation is made before the pickup time.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="contact" class="contact" aria-label="Get In Touch With Us">
        <div class="contact-inner">
            <div class="contact-header">
                <p class="contact-sub">We’re here to answer your questions and help you get the perfect rental experience.</p>
                <h2 class="contact-title">Get In Touch With Us</h2>
            </div>
            <div class="contact-grid">
                <div class="contact-info reveal">
                    <div class="info-card">
                        <h3>Car-Go Rentals Headquarters</h3>
                        <ul class="info-list">
                            <li><i class="fa-solid fa-location-dot"></i><span>Nairobi, Kenya</span></li>
                            <li><i class="fa-solid fa-phone"></i><span>+254 712 345 678</span></li>
                            <li><i class="fa-solid fa-envelope"></i><span>support@cargo-rentals.com</span></li>
                            <li><i class="fa-solid fa-calendar-day"></i><span>Monday – Sunday, 7:00 AM to 10:00 PM</span></li>
                        </ul>
                        <div class="social">
                            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="contact-form reveal">
                    <form id="contactForm" class="contact-card" action="#" method="post" novalidate>
                        <div class="form-alert success" id="contactSuccess" hidden>Message sent successfully.</div>
                        <div class="form-alert error" id="contactError" hidden>Check the fields and try again.</div>
                        <div class="field">
                            <span class="fi"><i class="fa-solid fa-user"></i></span>
                            <input type="text" id="fullName" name="fullName" placeholder="Full Name" required>
                        </div>
                        <div class="field">
                            <span class="fi"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" id="email" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="field">
                            <span class="fi"><i class="fa-solid fa-phone"></i></span>
                            <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
                        </div>
                        <div class="field">
                            <span class="fi"><i class="fa-solid fa-heading"></i></span>
                            <input type="text" id="subject" name="subject" placeholder="Subject" required>
                        </div>
                        <div class="field textarea">
                            <span class="fi"><i class="fa-solid fa-message"></i></span>
                            <textarea id="message" name="message" placeholder="Message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary submit-btn">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <footer class="site-footer" aria-label="Footer">
        <div class="footer-inner">
            <div class="footer-grid">
                <div class="footer-col company">
                    <div class="footer-logo">Car-Go Rentals</div>
                    <div class="footer-tag">Reliable. Affordable. Always on the Go.</div>
                    <p class="footer-desc">Premium car rentals tailored for every journey. Transparent pricing, flexible plans, and exceptional support ensure a seamless driving experience.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-col links">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-list">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Why Choose Us</a></li>
                        <li><a href="#">Popular Vehicles</a></li>
                        <li><a href="#">Testimonials</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col support">
                    <h4 class="footer-title">Customer Support</h4>
                    <ul class="footer-list">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Refund Policy</a></li>
                        <li><a href="#">Rental Guidelines</a></li>
                    </ul>
                </div>
                <div class="footer-col contact">
                    <h4 class="footer-title">Contact Information</h4>
                    <ul class="contact-list">
                        <li><i class="fa-solid fa-phone"></i><span>+254 712 345 678</span></li>
                        <li><i class="fa-solid fa-envelope"></i><span>support@cargo-rentals.com</span></li>
                        <li><i class="fa-solid fa-location-dot"></i><span>Nairobi, Kenya</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bar">
            <div class="bar-inner">
                <span>© 2025 Car-Go Rentals. All Rights Reserved.</span>
                <span>Developed by Car-Go Dev Team</span>
            </div>
        </div>
    </footer>

    <script src="./scripts/landing.js"></script>
</body>
</html>