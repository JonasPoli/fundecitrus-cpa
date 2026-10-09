<?php

// Uso: php bin/db-backup.php [diretório do projeto] — gera ~/backups/<banco>-<data>.sql.gz a partir do DATABASE_URL

use Symfony\Component\Dotenv\Dotenv;

$projectDir = realpath($argv[1] ?? dirname(__DIR__));
require $projectDir . '/vendor/autoload.php';

(new Dotenv())->usePutenv(false)->bootEnv($projectDir . '/.env');

$url = parse_url($_SERVER['DATABASE_URL'] ?? '');
if (!$url || empty($url['path']) || !str_starts_with($url['scheme'] ?? '', 'mysql')) {
    fwrite(STDERR, "DATABASE_URL ausente ou não é MySQL.\n");
    exit(1);
}

$database = ltrim($url['path'], '/');
$user = urldecode($url['user'] ?? '');
$host = $url['host'] ?? 'localhost';
$quote = fn (string $value): string => '"' . addcslashes($value, '\\"') . '"';

$backupDir = (getenv('HOME') ?: $projectDir . '/var') . '/backups';
if (!is_dir($backupDir) && !mkdir($backupDir, 0700, true)) {
    fwrite(STDERR, "Não foi possível criar $backupDir.\n");
    exit(1);
}
$target = sprintf('%s/%s-%s.sql.gz', $backupDir, $database, date('Ymd-His'));

$credentials = tempnam(sys_get_temp_dir(), 'dbbackup');
$errorLog = tempnam(sys_get_temp_dir(), 'dbbackup');
chmod($credentials, 0600);
file_put_contents($credentials, sprintf(
    "[client]\nuser=%s\npassword=%s\nhost=%s\nport=%d\n",
    $quote($user),
    $quote(urldecode($url['pass'] ?? '')),
    $quote($host),
    $url['port'] ?? 3306
));

try {
    $process = proc_open(
        ['mysqldump', '--defaults-extra-file=' . $credentials, '--single-transaction', '--routines', '--triggers', '--no-tablespaces', $database],
        [1 => ['pipe', 'w'], 2 => ['file', $errorLog, 'w']],
        $pipes
    );
    $gzip = gzopen($target, 'wb6');
    chmod($target, 0600);
    while (!feof($pipes[1])) {
        gzwrite($gzip, (string) fread($pipes[1], 1 << 20));
    }
    gzclose($gzip);
    fclose($pipes[1]);
    $exitCode = proc_close($process);
} finally {
    unlink($credentials);
}

if ($exitCode !== 0) {
    @unlink($target);
    fwrite(STDERR, "mysqldump falhou:\n" . str_replace([$user, $host], '***', (string) file_get_contents($errorLog)));
    unlink($errorLog);
    exit(1);
}
unlink($errorLog);

printf("Backup salvo em %s (%s KB)\n", $target, number_format(filesize($target) / 1024, 0, ',', '.'));
