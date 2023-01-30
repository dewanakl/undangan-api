<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamu - PHP Framework</title>
    <link rel="stylesheet" href="<?= asset('css/welcome.css') ?>">
</head>

<body>
    <div id="main">
        <div class="fof">
            <h1>Kamu |</h1>
            <p><?= e($data) ?></p>
            <small id="information"></small>
        </div>
    </div>

    <script src="<?= asset('js/welcome.js') ?>"></script>
</body>

</html>