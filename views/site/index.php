<?php require ROOT . "/views/layouts/header.php"; ?>


<div class="comments-container">
    <h2 style="border-bottom: 1px solid #e4e9e2; ">Комментарии</h2><br>
    <ul id="comments"><?= (mb_strlen($commentsHtml, 'utf-8') > 0) ? $commentsHtml : '<h4>Здесь пока нет комментариев</h4>'; ?></ul>
</div>
<hr class="hr-dashed">
<div class="comment">
    <button id="add-comment" class="btn btn-defaul">Добавить комментарий</button>
    <br><br>
    <div class="well comment-form">
        <?php if (isset($errors)) : ?>
            <div class="alert alert-danger error_place">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/site/index" enctype="multipart/form-data" <!--autocomplete="off"-->

        <div class="form-group"><label for="text">Комментарий: </label>
            <div id="panel">
                <div>
                    <span class="button" data-action="strong"><b>B</b></span>
                    <span class="button" data-action="cursive"><i>i</i></span>
                    <span class="button" data-action="link">a</span>
                    <span class="button" data-action="code">code</span>
                </div>
                <textarea class="form-control" id="text" name="text"><?= $text ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <input class="form-control input-sm" id="email" type="email" name="email" placeholder="Email"
                   value="<?= $email ?>">
        </div>
        <div class="form-group">
            <input class="form-control input-sm" id="name" type="text" name="username" placeholder="username"
                   value="<?= $username ?>">
        </div>
        <div class="form-group">
            <input class="form-control input-sm" id="homepage" type="text" name="homepage" placeholder="homepage"
                   value="<?= $homepage ?>">
            <small class="form-text text-muted">Адрес с протоколом, пример: http://example.com</small>
        </div>
        <div class="form-group">
            <label for="exampleInputFile">Прикрепить файл</label>
            <input type="file" name="file" id="addFile">
            <p class="help-block">
                <small class="form-text text-muted">Допустимые форматы: JPG, GIF, PNG, TXT. Текстовый файл не более
                    100кб.
                </small>
            </p>
        </div>
        <div class="form-group">
            <img src="/components/Captcha.php" id="captcha">
            <input type="text" name="captcha">
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-sm btn-default" name="addComment" value="Добавить комментарий">
        </div>
        </form>
    </div>
</div>
<?php require ROOT . "/views/layouts/footer.php"; ?>
