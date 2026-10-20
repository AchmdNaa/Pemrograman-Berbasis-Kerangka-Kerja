using AksesKita.Desktop.Rules;

namespace AksesKita.Tests;

public class CredentialValidatorTests
{
    [Theory]
    [InlineData("nama@email.com")]
    [InlineData("  Nama@Email.COM  ")]
    [InlineData("a.b@sub.domain.id")]
    public void Email_Valid(string email) => Assert.True(CredentialValidator.IsValidEmail(email));

    [Theory]
    [InlineData("")]
    [InlineData("tanpa-at.com")]
    [InlineData("a@b")]
    [InlineData("a b@c.com")]
    [InlineData(null)]
    public void Email_Invalid(string? email) => Assert.False(CredentialValidator.IsValidEmail(email));

    [Fact]
    public void NormalizeEmail_TrimsAndLowercases() =>
        Assert.Equal("nama@email.com", CredentialValidator.NormalizeEmail("  Nama@Email.COM "));

    [Theory]
    [InlineData("Abc12345")]
    [InlineData("kata-sandi-2026")]
    public void Password_Valid(string password) => Assert.Null(CredentialValidator.ValidatePassword(password));

    [Theory]
    [InlineData("Abc1234")]        // 7 karakter
    [InlineData("hanyahuruf")]     // tanpa angka
    [InlineData("1234567890")]     // tanpa huruf
    [InlineData("")]
    [InlineData(null)]
    public void Password_TooWeak(string? password) => Assert.NotNull(CredentialValidator.ValidatePassword(password));

    [Fact]
    public void Password_TooLong_Rejected() =>
        Assert.NotNull(CredentialValidator.ValidatePassword(new string('a', 70) + "123"));

    [Fact]
    public void Registration_AllValid_NoErrors() =>
        Assert.Empty(CredentialValidator.ValidateRegistration("Budi Santoso", "budi@email.com", "Rahasia123", "Rahasia123"));

    [Fact]
    public void Registration_ShortName_Error() =>
        Assert.NotEmpty(CredentialValidator.ValidateRegistration("Bu", "budi@email.com", "Rahasia123", "Rahasia123"));

    [Fact]
    public void Registration_ConfirmMismatch_Error()
    {
        var errors = CredentialValidator.ValidateRegistration("Budi Santoso", "budi@email.com", "Rahasia123", "Rahasia124");
        Assert.Contains(errors, e => e.Contains("Konfirmasi"));
    }

    [Fact]
    public void Registration_ReportsEveryProblem()
    {
        var errors = CredentialValidator.ValidateRegistration("", "salah", "pendek", "pendek");
        Assert.Equal(3, errors.Count);
    }

    [Theory]
    [InlineData("", "x")]
    [InlineData("a@b.com", "")]
    [InlineData("   ", "x")]
    public void Login_EmptyFields_Error(string email, string password) =>
        Assert.NotNull(CredentialValidator.ValidateLogin(email, password));

    [Fact]
    public void Login_Filled_NoError() => Assert.Null(CredentialValidator.ValidateLogin("a@b.com", "x"));
}
