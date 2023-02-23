<?php

use Cadexsa\Infrastructure\Facades\Router;

Router::get('/', \Cadexsa\Presentation\Http\Controllers\HomeController::class)->name('home');

Router::get('/aboutus', \Cadexsa\Presentation\Http\Controllers\AboutPageController::class)->name('about_us');

Router::get('/news/', \Cadexsa\Presentation\Http\Controllers\NewsController::class)->name('news');

Router::get('/news/filter', \Cadexsa\Presentation\Http\Controllers\NewsFiltrationController::class)->name('news_filtration');

Router::get('/news/{title}', \Cadexsa\Presentation\Http\Controllers\NewsArticleController::class)->name('news_article');

Router::get('/events/', \Cadexsa\Presentation\Http\Controllers\EventsController::class)->name('events');

Router::get('/events/filter', \Cadexsa\Presentation\Http\Controllers\EventFiltrationController::class)->name('news_filtration');

Router::get('/events/{name}', \Cadexsa\Presentation\Http\Controllers\EventController::class)->name('event');

Router::get('/gallery/', \Cadexsa\Presentation\Http\Controllers\GalleryController::class);

Router::get('/gallery/pictures/{id}', \Cadexsa\Presentation\Http\Controllers\PictureController::class)->where('id', '\d+');

Router::get('/gallery/filter', \Cadexsa\Presentation\Http\Controllers\PictureFiltrationController::class)->name('gallery_filtration');

Router::match(['GET', 'POST'], '/contactus', \Cadexsa\Presentation\Http\Controllers\ContactPageController::class)->name('contact_us');

Router::get('/exstudents/', \Cadexsa\Presentation\Http\Controllers\ExStudentsController::class)->name('exstudents');

Router::get('/exstudents/activate', \Cadexsa\Presentation\Http\Controllers\AccountActivationController::class)->name('account_activation');

Router::match(['GET', 'POST'], '/exstudents/password_reset', \Cadexsa\Presentation\Http\Controllers\PasswordResetController::class)->name('password_reset');

Router::match(['GET', 'POST'], '/exstudents/login', \Cadexsa\Presentation\Http\Controllers\LoginController::class)->name('login');

Router::match(['GET', 'POST'], '/exstudents/signup', \Cadexsa\Presentation\Http\Controllers\SignupController::class)->name('signup');

Router::get('/exstudents/{username}/{tab?}', \Cadexsa\Presentation\Http\Controllers\AccountController::class)->name('account')->where('username', '\w+')->where('tab', '\w+');

Router::get('/cms/', \Cadexsa\Presentation\Http\Controllers\CMSController::class)->name('cms_home');

Router::get('/cms/news/', \Cadexsa\Presentation\Http\Controllers\NewsManagerController::class)->name('cms_news_manager');

Router::match(['GET', 'POST'], '/cms/news/publish', \Cadexsa\Presentation\Http\Controllers\NewsPublicationController::class)->name('cms_news_publication');

Router::match(['GET', 'POST'], '/cms/news/edit', \Cadexsa\Presentation\Http\Controllers\NewsEditController::class)->name('cms_news_article_edit');

Router::get('/cms/events/', \Cadexsa\Presentation\Http\Controllers\EventManagerController::class)->name('cms_event_manager');

Router::match(['GET', 'POST'], '/cms/events/publish', \Cadexsa\Presentation\Http\Controllers\EventPublicationController::class)->name('cms_events_publication');

Router::match(['GET', 'POST'], '/cms/events/edit', \Cadexsa\Presentation\Http\Controllers\EventEditController::class)->name('cms_event_edit');

Router::match(['GET', 'POST'], '/cms/picture_upload', \Cadexsa\Presentation\Http\Controllers\GalleryPictureUploadController::class)->name('cms_picture_upload');

Router::get('/cms/delete', \Cadexsa\Presentation\Http\Controllers\ContentDeletionController::class)->name('cms_content_deletion');

Router::get('/cms/recover', \Cadexsa\Presentation\Http\Controllers\ContentRecoveryController::class)->name('cms_content_recovery');

Router::fallback(\Cadexsa\Presentation\Http\Controllers\NotFoundController::class);
