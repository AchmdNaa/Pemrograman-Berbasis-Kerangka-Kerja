using System.Threading.Tasks;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Services;

public record AuthResult(bool Success, AppUser? User, string? Error)
{
    public static AuthResult Ok(AppUser user) => new(true, user, null);
    public static AuthResult Fail(string error) => new(false, null, error);
}

public interface IAuthService
{
    /// <summary>Mendaftarkan akun baru. Peran selalu Pelapor; akun Admin hanya dibuat lewat data awal.</summary>
    Task<AuthResult> RegisterAsync(string name, string email, string password, string confirmPassword);

    Task<AuthResult> LoginAsync(string email, string password);
}
