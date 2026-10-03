<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php'; 

// تحقق من تسجيل الدخول
$user_logged_in = isset($_SESSION['user_id']);
$username = $user_logged_in ? $_SESSION['username'] : '';
$profile_pic = $user_logged_in && !empty($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default.png';

// التأكد من امتداد الصورة
if ($user_logged_in && pathinfo($profile_pic, PATHINFO_EXTENSION) == "") {
    if (file_exists(UPLOADS_PATH . '/' .$profile_pic.".jpg")) {
        $profile_pic .= ".jpg";
    } elseif (file_exists(UPLOADS_PATH . '/' .$profile_pic.".png")) {
        $profile_pic .= ".png";
    } else {
        $profile_pic = "default.png";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TailMate</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/logo.jpg">
    <style>
    /* === Reset & Body === */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        background: #FFF6EC;
        color: #5C3A21;
    }

    /* === Buttons === */
    .btn {
        display: inline-block;
        background: #5C3A21;
        color: #FFF6EC;
        padding: 10px 20px;
        border-radius: 25px;
        text-decoration: none;
        transition: 0.3s;
        box-shadow: 0 4px 6px rgba(92, 58, 33, 0.6);
    }

    .btn:hover {
        background: #E7A769;
        color: #5C3A21;
    }

    .btn.small {
        padding: 8px 15px;
        font-size: 0.9rem;
    }

    /* === Navbar === */
    .navbar {
        background: #E7A769;
        padding: 15px 30px;
        box-shadow: 0 2px 6px #5C3A21;
    }

    .navdiv {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .navdiv ul {
        list-style: none;
        display: flex;
        flex-wrap: wrap;
    }

    .navdiv ul li {
        position: relative;
    }

    .navdiv ul li a {
        color: #5C3A21;
        font-size: 18px;
        font-weight: bold;
        margin: 0 15px;
        transition: 0.3s;
        text-decoration: none;
        display: block;
        padding: 8px 0;
    }

    .navdiv ul li a:hover {
        color: #3e2414;
    }

    .dropdown-content {
        position: absolute;
        top: 100%;
        left: 0;
        background: #FFF6EC;
        border: 1px solid #E7A769;
        border-radius: 6px;
        min-width: 160px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        z-index: 99;
    }

    .dropdown-content a {
        display: block;
        padding: 10px;
        color: #5C3A21;
        font-size: 16px;
        text-decoration: none;
    }

    .dropdown-content a:hover {
        background: #E7A769;
        color: #fff;
    }

    .dropdown.show .dropdown-content {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-left: 15px;
        border: 2px solid #5C3A21;
        cursor: pointer;
        display: inline-block;
        vertical-align: middle;
    }

    .profile-pic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* === Hero Section === */
    .hero {
        background: url('<?= BASE_URL ?>/assets/images/k.jpg') no-repeat center center/cover;
        text-align: center;
        height: 600px;
        padding: 0 20px;
        border-bottom: 4px solid #5C3A21;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .hero h1 {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #2c1600;
    }

    .hero p {
        font-size: 1.2rem;
        margin-bottom: 20px;
        color: #2f1800;
    }

    .hero .btn-group {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    /* === Sections === */
    .intro {
        text-align: center;
        padding: 60px 20px;
        max-width: 800px;
        margin: auto;
    }

    .features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        padding: 50px;
    }

    .feature-card {
        background: #fff;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        border: 2px solid #E7A769;
        box-shadow: 0 4px 6px rgba(92, 58, 33, 0.3);
        color: #5C3A21;
    }

    .available-animals {
        padding: 50px 20px;
        text-align: center;
        background: #ffe5cc;
    }

    .animal-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .animal-card {
        background: #fff;
        border-radius: 15px;
        padding: 15px;
        box-shadow: 0 4px 6px rgba(92, 58, 33, 0.3);
        transition: transform 0.3s;
    }

    .animal-card img {
        width: 100%;
        border-radius: 10px;
        transition: transform 0.3s;
    }

    .animal-card img:hover {
        transform: scale(1.05);
    }

    .animal-card.cat {
        border: 2px solid #E7A769;
    }

    .animal-card.dog {
        border: 2px solid #DFA569;
    }

    /* Stories Section */
    .stories {
        padding: 50px 20px;
        text-align: center;
        background: #fff0e0;
    }

    .story-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .story-card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(92, 58, 33, 0.3);
        transition: transform 0.3s;
    }

    .story-card:hover {
        transform: translateY(-5px);
    }

    .story-card img {
        width: 100%;
        display: block;
        height: 200px;
        object-fit: cover;
    }

    .story-card h3 {
        margin: 15px;
        color: #5C3A21;
    }

    .story-card p {
        margin: 0 15px 15px 15px;
        color: #5C3A21;
    }

    /* Why Adopt & Tips */
    .why-adopt,
    .adoption-tips {
        padding: 50px 20px;
        text-align: center;
        background: #FFE5CC;
    }

    .why-adopt div,
    .adoption-tips div {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .why-adopt div div,
    .adoption-tips div div {
        background: #fff;
        padding: 20px;
        border-radius: 15px;
        width: 220px;
        box-shadow: 0 4px 6px rgba(92, 58, 33, 0.3);
        color: #5C3A21;
    }

    /* Testimonials */
    .testimonials {
        padding: 50px 20px;
        text-align: center;
        background: #fff0e0;
    }

    .testimonials div {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .testimonials div div {
        background: #fff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(92, 58, 33, 0.3);
    }

    /* Did You Know & Volunteer */
    .did-you-know,
    .volunteer-opportunities {
        padding: 50px 20px;
        text-align: center;
        background: #FFF6EC;
    }

    .did-you-know div,
    .volunteer-opportunities div {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .did-you-know div div,
    .volunteer-opportunities div div {
        background: #fff;
        padding: 20px;
        border-radius: 15px;
        width: 220px;
        box-shadow: 0 4px 6px rgba(92, 58, 33, 0.3);
        color: #5C3A21;
    }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/user_navbar.php'; ?>

    <!-- Hero -->
    <section class="hero">
        <h1>Give Them a Second Chance</h1>
        <p>Join us in rescuing and rehoming animals in need.</p>
        <div class="btn-group">
            <a href="<?= BASE_URL ?>/pages/adoptionform.php" class="btn">Adopt Now</a>
            <a href="<?= BASE_URL ?>/pages/cats.php" class="btn">View Cats</a>
            <a href="<?= BASE_URL ?>/pages/dogs.php" class="btn">View Dogs</a>
        </div>
    </section>

    <!-- Intro -->
    <section class="intro">
        <h2>Welcome to TailMate</h2>
        <p>We are dedicated to providing safe homes, love, and care for stray and abandoned animals. Explore our pets and find your new best friend.</p>
        <a href="<?= BASE_URL ?>/pages/cats.php" class="btn">Browse Pets</a>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="feature-card">
            <h3>Adopt a Cat</h3>
            <p>Find your perfect feline companion from our rescued cats.</p>
            <a href="<?= BASE_URL ?>/pages/cats.php" class="btn small">View Cats</a>
        </div>
        <div class="feature-card">
            <h3>Adopt a Dog</h3>
            <p>Find your new best friend from our rescued dogs.</p>
            <a href="<?= BASE_URL ?>/pages/dogs.php" class="btn small">View Dogs</a>
        </div>
        <div class="feature-card">
            <h3>Adoption Form</h3>
            <p>Ready to adopt? Fill out our simple adoption form.</p>
            <a href="<?= BASE_URL ?>/pages/adoptionform.php" class="btn small">Apply Now</a>
        </div>
    </section>

    <!-- Available Animals -->
    <section class="available-animals">
        <h2>Meet Our Animals</h2>
        <div class="animal-cards">
            <div class="animal-card dog">
                <img src="<?= BASE_URL ?>/assets/images/dog.jpg" alt="Dog">
                <h3>Max</h3>
                <a href="<?= BASE_URL ?>/pages/dogs.php" class="btn small">View Dogs</a>
            </div>
            <div class="animal-card cat">
                <img src="<?= BASE_URL ?>/assets/images/kitty.jpg" alt="Cat">
                <h3>Whiskers</h3>
                <a href="<?= BASE_URL ?>/pages/cats.php" class="btn small">View Cats</a>
            </div>
            <div class="animal-card dog">
                <img src="<?= BASE_URL ?>/assets/images/zoe.jpg" alt="Dog">
                <h3>Luna</h3>
                <a href="<?= BASE_URL ?>/pages/dogs.php" class="btn small">View Dogs</a>
            </div>
        </div>
    </section>

    <!-- Success Stories -->
    <section class="stories">
        <h2>Success Stories</h2>
        <div class="story-cards">
            <div class="story-card">
                <img src="<?= BASE_URL ?>/assets/images/b.jpg" alt="Max">
                <h3>Max found a home!</h3>
                <p>Max was rescued last month and now he has a loving family.</p>
            </div>
            <div class="story-card">
                <img src="<?= BASE_URL ?>/assets/images/g.jpg" alt="Bella">
                <h3>Bella's New Life</h3>
                <p>Bella was abandoned but now enjoys life with her new family.</p>
            </div>
            <div class="story-card">
                <img src="<?= BASE_URL ?>/assets/images/ll.jpg" alt="Luna">
                <h3>Luna's Journey</h3>
                <p>Luna was rescued from the streets and now has a forever home.</p>
            </div>
        </div>
    </section>

    <!-- Why Adopt -->
    <section class="why-adopt">
        <h2>Why Adopt?</h2>
        <p>Adopting a pet saves a life and brings love to your home. Every pet deserves a second chance.</p>
        <div>
            <div>
                <h3>Save a Life</h3>
                <p>Give abandoned pets a loving home.</p>
            </div>
            <div>
                <h3>Companionship</h3>
                <p>Pets bring joy and companionship.</p>
            </div>
            <div>
                <h3>Health Benefits</h3>
                <p>Reduce stress and stay active with a furry friend.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2>What Our Adopters Say</h2>
        <div>
            <div>
                <p>"Adopting Max was the best decision ever! He brings so much joy to our family."</p>
                <h4>- Sarah L.</h4>
            </div>
            <div>
                <p>"Bella has changed our lives for the better. Thank you for helping us find her!"</p>
                <h4>- Ahmed M.</h4>
            </div>
            <div>
                <p>"I love seeing Locy happy in his new home. Truly a wonderful experience."</p>
                <h4>- Nour A.</h4>
            </div>
        </div>
    </section>

    <!-- Adoption Tips -->
    <section class="adoption-tips">
        <h2>Adoption Tips</h2>
        <div>
            <div>
                <h3>Prepare Your Home</h3>
                <p>Make sure your home is safe and comfortable for your new pet.</p>
            </div>
            <div>
                <h3>Patience is Key</h3>
                <p>Give your pet time to adjust to their new environment.</p>
            </div>
            <div>
                <h3>Regular Vet Visits</h3>
                <p>Ensure your pet stays healthy with regular check-ups.</p>
            </div>
        </div>
    </section>

    <!-- Did You Know -->
    <section class="did-you-know">
        <h2>Did You Know?</h2>
        <p>Fun facts about cats and dogs that might surprise you!</p>
        <div>
            <div>
                <h3>Cats Sleep a Lot</h3>
                <p>Cats sleep for 12-16 hours a day on average.</p>
            </div>
            <div>
                <h3>Dogs’ Sense of Smell</h3>
                <p>A dog’s sense of smell is 40 times stronger than humans.</p>
            </div>
            <div>
                <h3>Cats Can Jump High</h3>
                <p>Cats can jump up to six times their body length in one leap.</p>
            </div>
            <div>
                <h3>Dogs Dream Too</h3>
                <p>Dogs dream just like humans and often move their paws while dreaming.</p>
            </div>
        </div>
    </section>

    <!-- Volunteer Opportunities -->
    <section class="volunteer-opportunities">
        <h2>Volunteer Opportunities</h2>
        <p>Join our team and help care for animals in need.</p>
        <div>
            <div>
                <h3>Animal Care</h3>
                <p>Feed, groom, and play with animals at the shelter.</p>
            </div>
            <div>
                <h3>Events & Fundraising</h3>
                <p>Help organize events and raise funds for our mission.</p>
            </div>
            <div>
                <h3>Social Media Support</h3>
                <p>Share our stories and help more animals find homes.</p>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script>
    function toggleDropdown(el) {
        let parent = el.parentElement;
        parent.classList.toggle("show");
        document.querySelectorAll(".dropdown").forEach(d => {
            if (d !== parent) d.classList.remove("show");
        });
    }
    document.addEventListener("click", function(e) {
        if (!e.target.closest(".dropdown")) {
            document.querySelectorAll(".dropdown").forEach(d => d.classList.remove("show"));
        }
    });
    </script>
</body>

</html>