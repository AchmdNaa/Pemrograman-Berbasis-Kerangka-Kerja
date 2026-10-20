using System;

namespace AksesKita.Desktop.Security;

/// <summary>
/// Pembungkus BCrypt untuk kata sandi. Kata sandi tidak pernah disimpan apa adanya:
/// yang tersimpan hanya hash yang sudah diberi salt acak dan bisa diverifikasi, tidak bisa dikembalikan.
/// </summary>
public static class PasswordHasher
{
    private const int WorkFactor = 11;

    public static string Hash(string password) =>
        BCrypt.Net.BCrypt.HashPassword(password, WorkFactor);

    public static bool Verify(string password, string? hash)
    {
        if (string.IsNullOrEmpty(hash)) return false;
        try
        {
            return BCrypt.Net.BCrypt.Verify(password, hash);
        }
        catch (Exception)
        {
            // Hash rusak atau format tidak dikenal dianggap tidak cocok.
            return false;
        }
    }
}
