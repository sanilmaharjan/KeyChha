<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About - KeyChha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="styles/index.css" />
    <style>
        .about-section {
            background: linear-gradient(to right, #d3cce3, #e9e4f0);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .faq-title {
            font-weight: bold;
            color: #6e48aa;
        }

        .accordion-button {
            background-color: #f8f9fa;
            font-weight: 500;
        }

        .accordion-button:not(.collapsed) {
            color: #fff;
            background-color: #6e48aa;
        }

        .accordion-body {
            background-color: #fdfdff;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <div class="container my-5">
        <!-- About Section -->
        <div class="about-section mb-5">
            <h1 class="text-center text-dark fw-bold mb-4">
                <i class="bi bi-info-circle me-2 text-primary"></i>About <span class="text-primary">KeyChha</span>
            </h1>
            <p class="fs-5 text-justify">
                <strong>KeyChha</strong> is a modern, user-friendly web platform built to help you improve your typing
                speed,
                accuracy, and consistency. Whether you’re just starting to learn touch typing or looking to sharpen your
                keyboard skills
                for coding, writing, or gaming — KeyChha provides the right tools, interface, and feedback to help you
                grow.
            </p>
            <p class="fs-5 text-justify">
                Our vision is to empower students, professionals, and anyone with a keyboard to become faster, more
                confident typists.
                With real-time key tracking, performance statistics, error tracking, and customizable layouts, KeyChha
                transforms typing
                practice into an engaging learning experience. The platform is free to use, with optional account
                features for tracking progress.
            </p>
            <p class="fs-5 text-justify">
                We believe typing is a foundational skill for the digital world — and we’ve made it our mission to make
                it accessible,
                effective, and fun for everyone. Practice, track your progress, and grow — all on KeyChha.
            </p>
        </div>

        <!-- FAQ Section -->
        <h2 class="text-center mb-4 faq-title">Frequently Asked Questions</h2>
        <div class="accordion" id="accordionExample">

            <!-- Q1 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        What is KeyChha and how does it work?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <strong>KeyChha</strong> is an interactive typing platform designed to simulate a virtual
                        keyboard with real-time feedback.
                        It lets users practice typing in a guided area while highlighting keys pressed and tracking
                        correct/incorrect entries.
                        The site measures speed, accuracy, and progress over time.
                    </div>
                </div>
            </div>

            <!-- Q2 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Do I need to sign up to use KeyChha?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        No registration is needed to start practicing. You can jump right in and begin typing. However,
                        creating a free account
                        gives you access to features like tracking your daily performance, saving progress, and
                        customizing preferences.
                    </div>
                </div>
            </div>

            <!-- Q3 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Is KeyChha suitable for beginners?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        Absolutely! KeyChha was designed with all levels in mind. Whether you're completely new to
                        typing or just need
                        more confidence on the keyboard, our guided practice area and visual keyboard feedback help you
                        learn key positions and build muscle memory.
                    </div>
                </div>
            </div>

            <!-- Q4 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        What stats does KeyChha track?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        KeyChha tracks multiple performance metrics including words typed, characters typed, number of
                        correct inputs,
                        and errors made during each session. Logged-in users also get historical data, improvement
                        graphs, and accuracy trends over time.
                    </div>
                </div>
            </div>

            <!-- Q5 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Can I use KeyChha on mobile or tablet devices?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        While KeyChha works on mobile and tablet devices, it is optimized for desktop keyboards where
                        full typing practice
                        and feedback mechanisms are available. For the best experience, we recommend using a physical
                        keyboard.
                    </div>
                </div>
            </div>

            <!-- Q6 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        Is there any fee to use advanced features?
                    </button>
                </h2>
                <div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        No — KeyChha is completely free. All current features including performance stats, login system,
                        and visual feedback
                        are accessible at no cost. We may offer optional enhancements in the future but the core
                        platform will always remain free.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>