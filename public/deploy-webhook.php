<?php
/**
 * GitHub Webhook handler for auto-deployment.
 * 
 * Place this file at /var/www/infrahub.click/public/deploy-webhook.php
 * Configure GitHub webhook to POST to: https://infrahub.click/deploy-webhook.php
 * 
 * Set your webhook secret in .env as DEPLOY_WEBHOOK_SECRET
 */

// Load the .env manually to get the secret (avoid bootstrapping entire Laravel)
$envFile = __DIR__ . '/../.env';
$secret = null;

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0)
            continue;
        if (strpos($line, 'DEPLOY_WEBHOOK_SECRET=') === 0) {
            $secret = trim(substr($line, strlen('DEPLOY_WEBHOOK_SECRET=')));
            break;
        }
    }
}

// Verify the secret is configured
if (empty($secret)) {
    http_response_code(500);
    echo json_encode(['error' => 'Webhook secret not configured']);
    exit;
}

// Verify the GitHub signature
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (empty($signature)) {
    http_response_code(403);
    echo json_encode(['error' => 'No signature provided']);
    exit;
}

$expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($expected, $signature)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

// Parse the payload
$data = json_decode($payload, true);

// Only deploy on pushes to main branch
$ref = $data['ref'] ?? '';
if ($ref !== 'refs/heads/main') {
    echo json_encode(['message' => "Ignored push to {$ref}, only main triggers deploy"]);
    exit;
}

// Trigger deploy script in the background
$deployScript = __DIR__ . '/../deploy.sh';

if (!file_exists($deployScript)) {
    http_response_code(500);
    echo json_encode(['error' => 'Deploy script not found']);
    exit;
}

// Run in background so GitHub doesn't wait/timeout
exec("bash {$deployScript} > /dev/null 2>&1 &");

http_response_code(200);
echo json_encode([
    'message' => 'Deployment triggered',
    'branch' => 'main',
    'commit' => $data['after'] ?? 'unknown',
    'pusher' => $data['pusher']['name'] ?? 'unknown',
    'timestamp' => date('Y-m-d H:i:s'),
]);
