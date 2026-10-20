using System.Collections.Generic;
using System.Linq;
using System.Text.RegularExpressions;

namespace AksesKita.Desktop.Rules;

/// <summary>Aturan isian akun: nama, email, dan kata sandi. Bebas dari WPF supaya mudah diuji.</summary>
public static class CredentialValidator
{
    public const int NameMin = 3;
    public const int NameMax = 60;
    public const int PasswordMin = 8;

    // BCrypt hanya memakai 72 byte pertama dari kata sandi, jadi batasnya dibuat tegas.
    public const int PasswordMax = 72;

    private static readonly Regex EmailPattern =
        new(@"^[^@\s]+@[^@\s]+\.[^@\s]+$", RegexOptions.Compiled);

    public static string NormalizeEmail(string? email) => (email ?? string.Empty).Trim().ToLowerInvariant();

    public static bool IsValidEmail(string? email)
    {
        var e = NormalizeEmail(email);
        return e.Length <= 120 && EmailPattern.IsMatch(e);
    }

    /// <summary>Kata sandi: minimal 8 karakter, mengandung huruf dan angka.</summary>
    public static string? ValidatePassword(string? password)
    {
        var p = password ?? string.Empty;
        if (p.Length < PasswordMin) return $"Kata sandi minimal {PasswordMin} karakter.";
        if (p.Length > PasswordMax) return $"Kata sandi maksimal {PasswordMax} karakter.";
        if (!p.Any(char.IsLetter) || !p.Any(char.IsDigit)) return "Kata sandi harus mengandung huruf dan angka.";
        return null;
    }

    /// <summary>Daftar pesan kesalahan pendaftaran (kosong bila semua valid), berurutan sesuai formulir.</summary>
    public static IReadOnlyList<string> ValidateRegistration(
        string? name, string? email, string? password, string? confirmPassword)
    {
        var errors = new List<string>();

        var n = name?.Trim() ?? string.Empty;
        if (n.Length < NameMin || n.Length > NameMax)
            errors.Add($"Nama wajib diisi, {NameMin}–{NameMax} karakter.");

        if (!IsValidEmail(email))
            errors.Add("Format email tidak valid, contoh: nama@email.com.");

        var passwordError = ValidatePassword(password);
        if (passwordError != null)
            errors.Add(passwordError);
        else if (password != confirmPassword)
            errors.Add("Konfirmasi kata sandi tidak sama.");

        return errors;
    }

    /// <summary>Pesan kesalahan untuk isian login, atau null bila lengkap.</summary>
    public static string? ValidateLogin(string? email, string? password)
    {
        if (string.IsNullOrWhiteSpace(email) || string.IsNullOrEmpty(password))
            return "Email dan kata sandi wajib diisi.";
        return null;
    }
}
