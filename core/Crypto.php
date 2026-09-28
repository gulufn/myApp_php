<?php
namespace Core;

class Crypto
{
    /** 密钥（生产环境从环境变量读） */
    private static string $secret = 'my-secret-key-please-change';

    private const CIPHER = 'AES-256-CBC';

    /** 加密 */
    public static function encrypt(string $plain): string
    {
        $key = hash('sha256', self::$secret, true);
        $iv  = random_bytes(16);

        $cipher = openssl_encrypt(
            $plain,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($cipher === false) {
            throw new \RuntimeException('加密失败');
        }

        return self::base64UrlEncode($iv . $cipher);
    }

    /** 解密 */
    public static function decrypt(string $token): ?string
    {
        $raw = self::base64UrlDecode($token);
        if ($raw === null || strlen($raw) < 17) {
            return null;
        }

        $iv     = substr($raw, 0, 16);
        $cipher = substr($raw, 16);

        $plain = openssl_decrypt(
            $cipher,
            self::CIPHER,
            hash('sha256', self::$secret, true),
            OPENSSL_RAW_DATA,
            $iv
        );

        return $plain === false ? null : $plain;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): ?string
    {
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        return $decoded === false ? null : $decoded;
    }
}