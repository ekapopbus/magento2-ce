<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';

// MFTF HTTP wrapper for running `bin/magento` remotely (development only).
// POST params: token (OAuth token) OR secret (dev bypass), command (required), arguments, timeout

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Only POST allowed.';
    exit;
}

// Always write a minimal entry for POSTs so MFTF requests are captured even on early exit
try {
    $miniPath = defined('BP') ? BP . '/var/log/mftf-command-output.log' : __DIR__ . '/mftf-command-output.log';
    @file_put_contents($miniPath, date('c') . " [post_received] remote=" . ($_SERVER['REMOTE_ADDR'] ?? '') . " uri=" . ($_SERVER['REQUEST_URI'] ?? '') . "\n", FILE_APPEND);
} catch (\Throwable $e) {
    // ignore logging errors
}

// Minimal access log (timestamp, request, remote)
@file_put_contents(defined('BP') ? BP . '/var/log/mftf-command-access.log' : __DIR__ . '/mftf-command-access.log', date('c') . " request=" . ($_SERVER['REQUEST_URI'] ?? '') . " remote=" . ($_SERVER['REMOTE_ADDR'] ?? '') . "\n", FILE_APPEND);

$token = $_POST['token'] ?? '';
$command = trim((string)($_POST['command'] ?? ''));
$arguments = trim((string)($_POST['arguments'] ?? ''));
$timeout = isset($_POST['timeout']) ? (int)$_POST['timeout'] : 60;

// Handle MFTF behavior where the command parameter name may be the URL-encoded
// command (when MAGENTO_CLI_COMMAND_PARAMETER is empty). If `command` is not
// provided, look for a POST key that isn't token/secret/arguments/timeout and
// treat that key name (URL-decoded) as the command.
if ($command === '') {
    foreach (array_keys($_POST) as $k) {
        if (in_array($k, ['token', 'secret', 'arguments', 'timeout'], true)) {
            continue;
        }
        // Skip numeric keys and empty keys
        if ($k === '' || is_numeric($k)) {
            continue;
        }
        $decoded = urldecode($k);
        if ($decoded !== '') {
            $command = trim((string)$decoded);
            // If POST value is non-empty, treat it as arguments
            if (!empty($_POST[$k]) && $arguments === '') {
                $arguments = trim((string)$_POST[$k]);
            }
            break;
        }
    }
}
$timeout = isset($_POST['timeout']) ? (int)$_POST['timeout'] : 60;

// Developer bypass secret
$secretPassed = $_POST['secret'] ?? '';
$secretFile = BP . '/.mftf_secret';
$secretBypass = false;
if ($secretPassed !== '' && is_readable($secretFile)) {
    $secretStored = trim((string)file_get_contents($secretFile));
    if ($secretStored !== '' && hash_equals($secretStored, (string)$secretPassed)) {
        $secretBypass = true;
    }
}

if ($command === '' || ($token === '' && !$secretBypass)) {
    http_response_code(412);
    echo 'Required parameters not set: token and command (or valid secret).';
    exit;
}

try {
    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();

    if (!$secretBypass) {
        $tokenModel = $objectManager->get(\Magento\Integration\Model\Oauth\Token::class);
        $stored = $tokenModel->loadByToken($token)->getToken();
        if (empty($stored) || $stored !== $token) {
            http_response_code(401);
            echo 'Command not authorized.';
            exit;
        }
    }

    $phpBin = 'php';
    if (defined('PHP_BINARY')) {
        $phpBinary = PHP_BINARY;
        if (stripos($phpBinary, 'php-fpm') === false && stripos($phpBinary, 'fpm') === false) {
            $phpBin = $phpBinary;
        }
    }

    $cmdParts = $command === '' ? [] : preg_split('/\s+/', $command);
    if ($arguments !== '') {
        $argParts = preg_split('/\s+/', $arguments);
        $cmdParts = array_merge($cmdParts, $argParts);
    }

    // Run PHP with display_errors disabled to avoid warnings splitting CLI output
    $phpExecutable = $phpBin;
    $phpInvocation = [$phpExecutable, '-d', 'display_errors=0', BP . '/bin/magento'];
    $processArgs = array_merge($phpInvocation, $cmdParts);

    $process = new \Symfony\Component\Process\Process($processArgs);
    $process->setIdleTimeout($timeout);
    $process->setTimeout(0);
    $process->run();

    // Detailed debug logging for MFTF-invoked requests: save POST, headers, args, stdout, stderr
    try {
        $debugPath = BP . '/var/log/mftf-command-output.log';
        $requestDump = json_encode(["time" => date('c'), "remote" => $_SERVER['REMOTE_ADDR'] ?? '', "request_uri" => $_SERVER['REQUEST_URI'] ?? '', "post" => $_POST], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (empty($headers)) {
            // Fallback: collect common HTTP headers from $_SERVER
            $hdrs = [];
            foreach ($_SERVER as $k => $v) {
                if (strpos($k, 'HTTP_') === 0) {
                    $hdrs[$k] = $v;
                }
            }
            $headers = $hdrs;
        }
        $processOutput = $process->getOutput();
        $processError = $process->getErrorOutput();
        $procInfo = json_encode(["args" => $processArgs, "exitCode" => $process->getExitCode(), "isSuccessful" => $process->isSuccessful()], JSON_UNESCAPED_SLASHES);
        @file_put_contents($debugPath, "---\n" . $requestDump . "\nHEADERS:\n" . json_encode($headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\nPROCESS:\n" . $procInfo . "\nSTDOUT:\n" . ($processOutput === '' ? '<empty>' : $processOutput) . "\nSTDERR:\n" . ($processError === '' ? '<empty>' : $processError) . "\n---\n", FILE_APPEND);
    } catch (\Throwable $e) {
        // ignore logging errors
    }

    $output = $process->getOutput();
    if ($output === '') {
        $output = $process->getErrorOutput();
    }

    http_response_code($process->isSuccessful() ? 202 : 500);
    echo $output ?: 'CLI did not return output.';
} catch (\Throwable $e) {
    http_response_code(500);
    echo 'Proxy error: ' . $e->getMessage();
}
