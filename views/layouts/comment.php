<li id="comment<?= $tree['id'] ?>">
    <div class="comments-item">
        <div class="comment-title">
            <h5>
                <a href="<?= $tree['homepage'] ?>"><?= $tree['username'] ?></a>
                <span class="comment-time"><?= $tree['time'] ?></span>
            </h5>
        </div>
        <div class="comment-body"><?php echo nl2br($tree['text']) ?></div>
        <? if (strlen($tree['filename']) > 0) :?>
            <div class="uploaded">
                <a href="/site/file/<?= $tree['id']; ?>">файл</a>
            </div>
        <?php endif; ?>
        <div class="comment-footer">
            <a class="reply">ответить</a>&nbsp;<span><?= $tree['email'] ?></span>
        </div>
    </div>

    <?php if (isset($tree['child']) && !empty($tree['child'])): ?>
        <ul id="reply_comments">
            <?= $this->commentsString($tree['child']) ?>
        </ul>
    <? endif; ?>
</li>
