<?php

// Somewhere is a autoloader ...
$dir = __DIR__;
do {
    if (!file_exists($dir . '/composer.json')) continue;

    $data = array_merge_recursive(
        ['config' => ['vendor-dir' => 'vendor']],
        json_decode(file_get_contents($dir . '/composer.json'), true)
    );

    if (!file_exists($dir . '/' . $data['config']['vendor-dir'] . '/autoload.php')) continue;

    require_once $dir . '/' . $data['config']['vendor-dir'] . '/autoload.php';
    break;
} while (dirname($dir) !== $dir && $dir = dirname($dir));

// Init
$basePath = __DIR__ . '/var';
$mailDir = new \ScreamingDev\MailReader\MailDir($basePath);
$config = $_GET;

// Assert current item.
if (!isset($config['current']) || !$config['current']) {
    foreach ($mailDir->getMails() as $file => $mail) {
        $config['current'] = $file;
    }
}


$current = null;
if (isset($config['current']) && file_exists($basePath . '/' . basename($config['current']))) {
    $current = new \PhpMimeMailParser\Parser();
    $current->setPath($basePath . '/' . basename($config['current']));
}

if ($current) {
    if (isset($config['show'])) {
        if ($current->getMessageBody('html')) {
            echo $current->getMessageBody('html');
            return;
        }

        echo $current->getMessageBody('text') ?: '(empty)';
        return;
    }

    if (isset($config['download'])) {
        foreach ($current->getAttachments() as $attachment) {
            if (basename($config['download']) === basename($attachment->getFilename())) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($attachment->getFilename()) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                echo $attachment->getContent();
                return;
            }
        }

        header('Location: mails.php');
        return; // Something invalid
    }

    if (isset($config['delete'])) {
        unlink($basePath . '/' . basename($config['current']));
        header('Location: mails.php');
        return;
    }
}

?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mails</title>

    <style><?php echo file_get_contents(__DIR__ . '/style.css'); ?></style>
</head>
<body>
<div class="rmp_up">
    <div class="list">
        <table>
            <thead>
            <tr>
                <th style="width: 32px">&nbsp;</th>
                <th>Subject</th>
                <th style="width: 30%">Recipient</th>
                <th style="width: 20%">Date</th>
                <th style="width: 32px"> </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($mailDir->getMails() as $file => $mail): ?>
                <tr>
                    <td>
                        <a href="?current=<?php echo $file; ?>" style="text-align: center;width: 100%">
                            <?php if ($mail->getAttachments()): ?>
                                &#128193;
                            <?php endif; ?>
                        </a>
                    </td>
                    <td>
                        <a href="?current=<?php echo $file; ?>">
                            <?php echo htmlentities($mail->getHeader('subject') ?: 'unknown'); ?>
                        </a>
                    </td>
                    <td>
                        <a href="?current=<?php echo $file; ?>">
                            <?php echo htmlentities($mail->getHeader('to') ?: 'unknown'); ?>
                        </a>
                    </td>
                    <td>
                        <a href="?current=<?php echo $file; ?>">
                            <?php echo $mail->getHeader('Date')?: basename($file, '.txt'); ?>
                        </a>
                    </td>
                    <td class="delete">
                        <a href="?current=<?php echo $file ?>&delete" style="width: 100%; text-align: center;">
                            &#128465;
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($current instanceof \PhpMimeMailParser\Parser) : ?>
        <div class="attachments">
            <?php foreach ($current->getAttachments() as $item) : ?>
                <span class="attachment">
            <a href="?current=<?php echo $config['current']; ?>&download=<?php echo $item->getFilename(); ?>">
                <?php echo $item->getFilename(); ?>
            </a>
        </span>
            <?php endforeach; ?>
        </div>
        <div class="container">
            <?php if ($config['current']): ?>
                <iframe src="?show&current=<?php echo $config['current']; ?>" frameborder="0" class="part"></iframe>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <p style="text-align: center">No mails there yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
