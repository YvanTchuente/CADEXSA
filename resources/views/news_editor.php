<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>News editor - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script src="/modules/ckeditor4/ckeditor.js"></script>
</head>

<body class="cms">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <div>
            <h1>News article editor</h1>
            <div class="cms-links">
                <span>
                    <a href="/cms/">Home</a>
                </span>
                <span>
                    <a href="/cms/news/">News</a>
                </span>
                <span>
                    <a href="publish">Publish an article</a>
                </span>
            </div>
        </div>
        <div class="cs-container">
            <form action="/cms/news/edit" method="post">
                <?php if (isset($message)) : ?><div class="form-msg success"><span><?= $message; ?></span></div><?php endif; ?>
                <?php if (isset($error)) : ?><div class="form-msg error"><span><?= $error; ?></span></div><?php endif; ?>
                <div class="form-element-container grid">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= $newsArticle->getTitle(); ?>" required />
                </div>
                <div class="form-element-container grid">
                    <label for="tags">Tags</label>
                    <?php foreach ($newsArticle->getTags() as $tag) $article_tags[] = $tag->label(); ?>
                    <input type="text" class="form-control" id="tags" name="tags" value="<?= implode("; ", $article_tags); ?>" required />
                </div>
                <input type="hidden" name="newsArticleId" value="<?= $newsArticle->getId(); ?>">
                <input type="hidden" name="authorId" value="<?= $authorId; ?>">
                <div class="form-element-container grid">
                    <label for="editor">Body</label>
                    <textarea id="editor" name="body"><?= $newsArticle->getBody(); ?></textarea>
                </div>
                <input type="hidden" name="token" value="<?= $token; ?>" />
                <div>
                    <button type="submit" name="action" value="publish" class="publish-btn">Publish</button>
                    <button type="submit" name="action" value="save" class="save-btn">Save your changes</button>
                </div>
            </form>
            <aside>
                <section id="categories">
                    <h3>Tags</h3>
                    <ul>
                        <?php foreach ($tags as $tag) : ?>
                            <li><a href="#"><?= $tag; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </aside>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
    <script>
        CKEDITOR.replace('editor');
        CKEDITOR.config.height = 500;
    </script>
</body>

</html>