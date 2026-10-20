using System;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.EntityFrameworkCore;
using AksesKita.Desktop.Data;
using AksesKita.Desktop.Models;
using AksesKita.Desktop.Rules;
using AksesKita.Desktop.Security;

namespace AksesKita.Desktop.Services;

public class AuthService : IAuthService
{
    // Dipakai bersama selama aplikasi terbuka, jadi mengganti layar tidak mereset hitungan gagal.
    private static readonly LoginThrottle Throttle = new();

    // Hash palsu supaya waktu proses sama baik email ada maupun tidak (mempersulit menebak email terdaftar).
    private static readonly Lazy<string> DummyHash = new(() => PasswordHasher.Hash("tidak-dipakai-1"));

    public async Task<AuthResult> RegisterAsync(string name, string email, string password, string confirmPassword)
    {
        var errors = CredentialValidator.ValidateRegistration(name, email, password, confirmPassword);
        if (errors.Count > 0) return AuthResult.Fail(errors[0]);

        var normalized = CredentialValidator.NormalizeEmail(email);

        using var db = new AksesKitaDbContext();
        if (await db.Users.AnyAsync(u => u.Email == normalized))
            return AuthResult.Fail("Email ini sudah terdaftar. Silakan masuk.");

        var user = new AppUser
        {
            Name = name.Trim(),
            Email = normalized,
            PasswordHash = PasswordHasher.Hash(password),
            Role = UserRole.Pelapor,
            CreatedAt = DateTime.Now
        };
        db.Users.Add(user);
        await db.SaveChangesAsync();
        return AuthResult.Ok(user);
    }

    public async Task<AuthResult> LoginAsync(string email, string password)
    {
        var inputError = CredentialValidator.ValidateLogin(email, password);
        if (inputError != null) return AuthResult.Fail(inputError);

        var normalized = CredentialValidator.NormalizeEmail(email);

        if (Throttle.IsLocked(normalized, out var remaining))
            return AuthResult.Fail($"Terlalu banyak percobaan gagal. Coba lagi dalam {Math.Ceiling(remaining.TotalSeconds)} detik.");

        using var db = new AksesKitaDbContext();
        var user = await db.Users.FirstOrDefaultAsync(u => u.Email == normalized);

        // Verifikasi selalu dijalankan agar waktu proses tidak membocorkan apakah email terdaftar.
        var ok = PasswordHasher.Verify(password, user?.PasswordHash ?? DummyHash.Value);

        if (user == null || !ok)
        {
            Throttle.RecordFailure(normalized);
            return AuthResult.Fail("Email atau kata sandi salah.");
        }

        Throttle.Reset(normalized);
        return AuthResult.Ok(user);
    }
}
