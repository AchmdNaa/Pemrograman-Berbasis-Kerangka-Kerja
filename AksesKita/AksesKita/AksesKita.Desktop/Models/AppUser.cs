using System;

namespace AksesKita.Desktop.Models;

public enum UserRole
{
    Pelapor,
    Admin
}

public class AppUser
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public string PasswordHash { get; set; } = string.Empty;
    public UserRole Role { get; set; } = UserRole.Pelapor;
    public DateTime CreatedAt { get; set; } = DateTime.Now;
}