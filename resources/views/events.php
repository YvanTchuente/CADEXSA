<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Events - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script type="module" src="/js/nice_selects.js"></script>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <header class="page-content-header">
        <h1>Events</h1>
    </header>
    <div class="ws-container">
        <?php if (isset($events)) : ?>
            <!-- Events search start -->
            <div class="filters-container">
                <form id="filter" action="/events/filter">
                    <div class="nice-select" id="nice-select-1">
                        <span class="current">month</span>
                        <ul class="dropdown">
                            <li class="selected">month</li>
                            <?php foreach (cal_info(CAL_GREGORIAN)['months'] as $month) : ?>
                                <li><?= $month; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <select id="select-month" name="month" required>
                            <option value="" selected>Month</option>
                            <?php foreach (cal_info(CAL_GREGORIAN)['months'] as $month) : ?>
                                <option value="<?= strtolower($month); ?>"><?= $month; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="nice-select" id="nice-select-2">
                        <span class="current">year</span>
                        <ul class="dropdown">
                            <li class="selected">year</li>
                            <?php foreach ($years as $year) : ?>
                                <li><?= $year; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <select id="select-year" name="year" required>
                            <option value="" selected>Year</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?= $year; ?>"><?= $year; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit">filter</button>
                </form>
            </div>
            <!-- Events search end -->
            <div class="list-container">
                <?php
                foreach ($events as $event) :
                    $link = "/events/" . urlencode(strtolower($event->getName()));
                ?>
                    <article class="event">
                        <div class="event-thumbnail">
                            <img src="<?= $event->getImage(); ?>" />
                        </div>
                        <div>
                            <div class="countdown" data-target-date="<?= $event->getOccurrenceDate()->format("Y-m-d H:i:s"); ?>">
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
                                        <div class="topHalf">9</div>
                                        <div class="bottomHalf">9</div>
                                    </div>
                                    <div class="flip-card" data-seconds-unit>
                                        <div class="topHalf">9</div>
                                        <div class="bottomHalf">9</div>
                                    </div>
                                </div>
                            </div>
                            <div class="event-description">
                                <a href="<?= $link; ?>">
                                    <h2><?= $event->getName(); ?></h2>
                                </a>
                                <div>
                                    <span><i class="fas fa-calendar-day"></i><?= $event->getOccurrenceDate()->format("l j F"); ?></span>
                                    <span><i class="fas fa-clock"></i><?= $event->getOccurrenceDate()->format("g a"); ?></span>
                                </div>
                                <p><?= $event->getDescription(true); ?></p>
                                <a href="<?= $link; ?>?action=participate" class="event-link">Participate</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($pageCount > 1) : ?>
                <div class="pagination-area">
                    <ul class="pagination">
                        <li class="page-item <?php if ($page == 1) echo "disabled"; ?>"><a href="?page=<?= $page - 1; ?>" class="page-link"><span class="fas fa-angle-double-left"></span></a></li>
                        <?php for ($i = 1; $i <= $pageCount; $i++) : ?>
                            <li class="page-item <?php if ($page == $i) echo "active"; ?>"><a href="?page=<?= $i; ?>" class="page-link"><?= $i; ?></a></li>
                        <?php endfor; ?>
                        <li class="page-item <?php if (($page + 1) > $pageCount) echo "disabled"; ?>"><a href="?page=<?= $page + 1; ?>" class="page-link"><span class="fas fa-angle-double-right"></span></a></li>
                    </ul>
                </div>
            <?php endif; ?>
        <?php elseif ($msg) : ?>
            <div style="text-align: center; padding: 4rem 0;"><?= $msg; ?></div>
        <?php endif; ?>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>