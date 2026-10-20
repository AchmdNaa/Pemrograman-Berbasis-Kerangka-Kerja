using AksesKita.Desktop.Security;

namespace AksesKita.Tests;

public class PasswordHasherTests
{
    [Fact]
    public void Hash_IsNotThePlainPassword()
    {
        var hash = PasswordHasher.Hash("Rahasia123");
        Assert.NotEqual("Rahasia123", hash);
        Assert.StartsWith("$2", hash); // format BCrypt
    }

    [Fact]
    public void Hash_UsesRandomSalt_SoTwoHashesDiffer() =>
        Assert.NotEqual(PasswordHasher.Hash("Rahasia123"), PasswordHasher.Hash("Rahasia123"));

    [Fact]
    public void Verify_CorrectPassword_True() =>
        Assert.True(PasswordHasher.Verify("Rahasia123", PasswordHasher.Hash("Rahasia123")));

    [Fact]
    public void Verify_WrongPassword_False() =>
        Assert.False(PasswordHasher.Verify("rahasia123", PasswordHasher.Hash("Rahasia123")));

    [Theory]
    [InlineData(null)]
    [InlineData("")]
    [InlineData("bukan-hash-bcrypt")]
    public void Verify_BrokenHash_FalseWithoutThrowing(string? hash) =>
        Assert.False(PasswordHasher.Verify("Rahasia123", hash));
}
