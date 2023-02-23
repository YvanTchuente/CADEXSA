<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Home - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script type="module" src="/js/counters.js"></script>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <section id="call-to-actions">
        <div id="call-to-action-carousel" class="carousel">
            <div class="carousel-item">
                <div class="call-to-action">
                    <div>
                        <h1>Contribute to the growth of the network and its expansion</h1>
                        <p>On top of providing your affection to our alma-mater (Lacadenelle Anglo-saxon high school), which is, for many of us, the main reason, your membership consolidates our unity and friendship and affords many advantages like career opportunities</p>
                        <a href="/exstudents/signup" class="button">Join our network</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="call-to-action">
                    <div>
                        <h1>We are committed to ensure the unity of our members</h1>
                        <p>CADEXSA is an initiative of ex-student from la Cadenelle Anglo Saxon college who agreed and took a decision of creating an association which shall regroup all graduate of the school so as to maintain our friendship</p>
                        <a href="/aboutus" class="button">Read About Us</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-navigation">
            <button class="previous-item-button" data-ride="prev"><i class="fas fa-arrow-left"></i></button>
            <button class="next-item-button" data-ride="next"><i class="fas fa-arrow-right"></i></button>
        </div>
    </section>
    <section id="association-description" class="ws-container">
        <div>
            <h1>Our Purpose of Ex-Students Association</h1>
            <p id="sh_p">The purpose of our ex-students association is to re-unite all the ex-students from all the various batches that had graduated from our Alma-Mater, so we support each others through the upcoming challenges of our professional careers and life.</p>
        </div>
        <div>
            <img src="/images/graphics/mission.png" alt="img" />
            <h3>Mission</h3>
            <p>At multiple occasions, we organise and pay visits to our alma-mater during which we share our experience with the current students and advice them on how to succeed their academic year.</p>
        </div>
        <div>
            <img src="/modules/fontawesome/svgs/solid/eye.svg" alt="img" />
            <h3>Vision</h3>
            <p>CADEXSA aims primarily to bring together and unite all the graduates of our alma-mater so to initiate and preserve friendship and unity for as long as possible.</p>
        </div>
        <div>
            <img src="/images/graphics/dartboard.png" alt="img" />
            <h3>Objectives</h3>
            <p>The objectives of CADEXSA in a long-run is to create job opportunities for its member and provide support to our alma-mater in all the aspects that it entails.</p>
        </div>
    </section>
    <!-- Features section -->
    <section id="features" class="ws-section">
        <div class="ws-container">
            <h1>Our Occupations</h1>
            <div class="grid-container">
                <div>
                    <img src="/images/graphics/scholars.png" alt="image" />
                    <h3>Promote Excellence</h3>
                    <p>We encourage current students and members to achieve excellence, being their best in their studies, professional careers and personal lives to make our network reach greater heights in the foreseeable future.</p>
                </div>
                <div>
                    <img src="/images/graphics/student.png" alt="image" />
                    <h3>Help Current Students</h3>
                    <p>Our aim is to provide assistance to students of our alma-mater. Every year, we organize mentorship sessions where volunteered ex-students are assigned a group of students to which they provide with academic pieces of advice, academic support and guidance.</p>
                </div>
                <div>
                    <img src="/images/graphics/school.png" alt="image" />
                    <h3>Support our High School</h3>
                    <p>As being one of our missions, we are committed to provide support to our alma-mater by various means. We yearly provision our alma-mater with school essentials among other things we provide to support.</p>
                </div>
                <div>
                    <img src="/images/graphics/community.png" alt="image" />
                    <h3>Building our network</h3>
                    <p>Networking being an important factor of our unity, we aim at staying in touch with each other to share our experiences in life and knowledge. We do organize several meetings and meetups each years for this purpose.</p>
                </div>
            </div>
        </div>
    </section>
    <?php if (isset($events)) { ?>
        <!-- Upcomming events slideshow -->
        <section id="events">
            <div class="ws-container">
                <div id="events-heading">
                    <h2>Upcoming Events</h2>
                </div>
                <div id="event_carousel" class="carousel">
                    <?php
                    foreach ($events as $event) :
                        $link = "/events/" . urlencode(strtolower($event->getName()));
                    ?>
                        <div class="carousel-item">
                            <article class="event">
                                <div class="event-thumbnail">
                                    <img src="<?= $event->getImage(); ?>" />
                                </div>
                                <div>
                                    <div class="countdown" data-target-date="<?= $event->getOccurrenceDate()->format("Y-m-d h:i:s"); ?>">
                                        <div>
                                            <label>Days</label>
                                            <div class="flip-card" data-days-tens>
                                                <div class="topHalf"></div>
                                                <div class="bottomHalf"></div>
                                            </div>
                                            <div class="flip-card" data-days-unit>
                                                <div class="topHalf"></div>
                                                <div class="bottomHalf"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <label>Hours</label>
                                            <div class="flip-card" data-hours-tens>
                                                <div class="topHalf"></div>
                                                <div class="bottomHalf"></div>
                                            </div>
                                            <div class="flip-card" data-hours-unit>
                                                <div class="topHalf"></div>
                                                <div class="bottomHalf"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <label>Minutes</label>
                                            <div class="flip-card" data-minutes-tens>
                                                <div class="topHalf"></div>
                                                <div class="bottomHalf"></div>
                                            </div>
                                            <div class="flip-card" data-minutes-unit>
                                                <div class="topHalf"></div>
                                                <div class="bottomHalf"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <label>Seconds</label>
                                            <div class="flip-card" data-seconds-tens>
                                                <div class="topHalf"></div>
                                                <div class="bottomHalf"></div>
                                            </div>
                                            <div class="flip-card" data-seconds-unit>
                                                <div class="topHalf"></div>
                                                <div class="bottomHalf"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="event-description">
                                        <a href="<?= $link; ?>">
                                            <h2><?= $event->getName(); ?></h2>
                                        </a>
                                        <div>
                                            <span><i class="fas fa-calendar-day"></i><?= $event->getOccurrenceDate()->format("l j F"); ?></span>
                                            <span><i class="fas fa-clock"></i> <?= $event->getOccurrenceDate()->format("g a"); ?></span>
                                        </div>
                                        <p><?= $event->getDescription(true); ?></p>
                                        <a href="<?= $link; ?>?action=participate" class="event-link">Participate</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($events) > 1) : ?>
                        <!-- Carousel controls -->
                        <div class="event_nav carousel-navigation">
                            <button class="event_btn" data-ride="prev"><i class="fas fa-arrow-left fa-lg"></i></button>
                            <button class="event_btn" data-ride="next"><i class="fas fa-arrow-right fa-lg"></i></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php } ?>
    <!-- General informations -->
    <section id="informative_numbers">
        <div class="ws-container">
            <div class="informative_number">
                <i class="fas fa-users"></i>
                <div>
                    <span class="counter" data-target="<?= $member_count; ?>">0</span>
                    <span>Members</span>
                </div>
            </div>
            <div class="informative_number">
                <i class="fas fa-images"></i>
                <div>
                    <span class="counter" data-target="<?= $picture_count; ?>">0</span>
                    <span>Photos</span>
                </div>
            </div>
            <div class="informative_number">
                <i class="fas fa-calendar-check"></i>
                <div>
                    <span class="counter" data-target="<?= $event_count; ?>">0</span>
                    <span>Events</span>
                </div>
            </div>
            <div class="informative_number">
                <i class="fas fa-award"></i>
                <div>
                    <span class="counter" data-target="20">0</span>
                    <span>Achievements</span>
                </div>
            </div>
        </div>
    </section>
    <!-- Gallery -->
    <section id="gallery">
        <img src="/images/gallery/img10.jpg" />
        <img src="/images/gallery/img7.jpg" />
        <img src="/images/gallery/img5.jpg" />
        <img src="/images/gallery/img15.jpg" />
        <img src="/images/gallery/img6.jpg" />
        <img src="/images/gallery/img4.jpg" />
        <img src="/images/gallery/img11.jpg" />
        <img src="/images/gallery/img12.jpg" />
    </section>
    <?php if (isset($news)) : ?>
        <!-- Latest News Sections -->
        <section id="news" class="ws-section">
            <div class="ws-container">
                <h1>News</h1>
                <div class="grid-container">
                    <?php
                    foreach ($news as $newsArticle) :
                        $link = "/news/" . urlencode(strtolower($newsArticle->getTitle()));
                    ?>
                        <div>
                            <article class="news_article">
                                <div class="image"><img src="<?= $newsArticle->getImage(); ?>"></div>
                                <div class="body">
                                    <h3><a href="<?= $link; ?>"><?= $newsArticle->getTitle(); ?></a></h3>
                                    <p><?= $newsArticle->getBody(true); ?></p>
                                    <div class="footer"><a href="<?= $link; ?>" class="link">More</a><span><i class="fas fa-clock"></i> <?= $newsArticle->getTimeSincePublication(); ?></span></div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <!-- Testimonials section -->
    <section id="testimonials" class="ws-section">
        <div class="ws-container">
            <h1>Some Speech About Us</h1>
            <div class="carousel">
                <div class="carousel-item">
                    <div class="testimony">
                        <p><i class="fas fa-quote-left"></i>Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam. De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
                        <div>
                            <img src="/images/graphics/profile-placeholder.png">
                            <div>
                                <span>Hessack Ryan</span>
                                <span>Young graduate</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="testimony">
                        <p><i class="fas fa-quote-left"></i>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand. Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
                        <div>
                            <img src="/images/graphics/profile-placeholder.png" alt="author">
                            <div>
                                <span>Mbake Collins</span>
                                <span>Vice-president</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="testimony">
                        <p><i class="fas fa-quote-left"></i>quis csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam, quis nostrud exercitation ullamco. Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
                        <div>
                            <img src="/images/graphics/profile-placeholder.png" alt="author">
                            <div>
                                <span>Kafack Steve</span>
                                <span>Member</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-navigation">
                <button class="previous-item-button" data-ride="prev"><i class="fas fa-arrow-left"></i></button>
                <button class="next-item-button" data-ride="next"><i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
    </section>
    <?php require views_path("/commons/page_footer.php"); ?>
    <script>
        const header_height = getComputedStyle(
            document.querySelector("header")
        ).getPropertyValue("height");
        document.querySelectorAll(".call-to-action").forEach((call_to_action) => {
            call_to_action.style.setProperty("--header_height", header_height);
        });
    </script>
</body>

</html>