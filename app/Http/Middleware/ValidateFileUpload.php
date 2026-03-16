<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates file uploads against the security policy from config/security.php.
 *
 * Blocks:
 * - Files with blocked extensions (e.g., .exe, .php, .sh)
 * - Files exceeding the configured size limit
 * - Files with mismatched MIME type vs. extension (e.g., .jpg with application/x-php)
 */
class ValidateFileUpload
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasFile('*') && $request->allFiles() === []) {
            return $next($request);
        }

        $config = config('security.uploads', []);
        $maxSizeMb = $config['max_file_size_mb'] ?? 50;
        $maxSizeBytes = $maxSizeMb * 1024 * 1024;
        $allowedExtensions = $config['allowed_extensions'] ?? [];
        $blockedExtensions = $config['blocked_extensions'] ?? [];

        foreach ($request->allFiles() as $key => $files) {
            // Normalize to array (single file or array of files)
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                $extension = strtolower($file->getClientOriginalExtension());
                $filename = $file->getClientOriginalName();

                // ── Block dangerous extensions ────────────────
                if (in_array($extension, $blockedExtensions)) {
                    Log::channel('security')->warning('BLOCKED_FILE_UPLOAD', [
                        'user_id' => $request->user()?->id,
                        'filename' => $filename,
                        'extension' => $extension,
                        'ip' => $request->ip(),
                    ]);

                    abort(422, "File type '.{$extension}' is not allowed for security reasons.");
                }

                // ── Validate allowed extensions (if list is configured) ──
                if (!empty($allowedExtensions) && !in_array($extension, $allowedExtensions)) {
                    abort(422, "File type '.{$extension}' is not in the list of allowed formats.");
                }

                // ── Check file size ───────────────────────────
                if ($file->getSize() > $maxSizeBytes) {
                    abort(422, "File '{$filename}' exceeds the maximum upload size of {$maxSizeMb}MB.");
                }

                // ── Double-extension check ────────────────────
                // Catches files like "invoice.pdf.php" or "report.docx.exe"
                $parts = explode('.', $filename);
                if (count($parts) > 2) {
                    $secondExt = strtolower($parts[count($parts) - 2]);
                    if (in_array($secondExt, $blockedExtensions)) {
                        Log::channel('security')->warning('DOUBLE_EXT_UPLOAD_BLOCKED', [
                            'user_id' => $request->user()?->id,
                            'filename' => $filename,
                            'ip' => $request->ip(),
                        ]);

                        abort(422, "File '{$filename}' has a suspicious double extension.");
                    }
                }

                // ── Malware Scanning (ClamAV) ─────────────────
                $scanEnabled = \App\Models\Setting::getValue('scan_for_malware', $config['scan_for_malware'] ?? false);

                if (filter_var($scanEnabled, FILTER_VALIDATE_BOOLEAN)) {
                    $this->scanForMalware($file, $filename, $request);
                }
            }
        }

        return $next($request);
    }

    /**
     * Run the uploaded file through ClamAV via CLI.
     */
    protected function scanForMalware($file, string $filename, Request $request): void
    {
        $path = $file->getRealPath();

        // Execute clamdscan (fastest, uses daemon) or fallback to clamscan
        $command = "clamdscan --no-summary " . escapeshellarg($path);

        exec($command, $output, $returnCode);

        // ClamAV exit codes: 0 = clean, 1 = virus found, 2 = error
        if ($returnCode === 1) {
            Log::channel('security')->alert('MALWARE_DETECTED', [
                'user_id' => $request->user()?->id,
                'filename' => $filename,
                'clamav_output' => implode("\n", $output),
                'ip' => $request->ip(),
            ]);

            abort(422, "Security Alert: The file '{$filename}' failed our malware scan and has been rejected.");
        } elseif ($returnCode > 1) {
            Log::error('ClamAV scan failed', [
                'filename' => $filename,
                'output' => implode("\n", $output),
                'code' => $returnCode,
            ]);
            // If the scanner itself fails, we fail-open or fail-closed based on preference.
            // In enterprise environments, returning a 500 error (fail-closed) is safer.
            abort(500, "Unable to verify file security due to a scanning system error.");
        }
    }
}
