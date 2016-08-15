<html>
<head>
    <title>Файл</title>
</head>
<body>
<div class="file-content">
    <?php if (isset($text)): ?>
        <?= $text ?>
    <?php else : ?>
        <?= $image ?>
    <?php endif; ?>
</div>
</body>
</html>