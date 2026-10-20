using AksesKita.Desktop.Rules;

namespace AksesKita.Tests;

public class ThemePaletteTests
{
    // Pasangan teks di atas latar yang benar-benar dipakai di tampilan.
    public static IEnumerable<object[]> Pairs()
    {
        var pairs = new (string Fg, string Bg)[]
        {
            ("Ink", "Surface"), ("Ink", "PageBg"), ("Body", "Surface"), ("Body2", "Surface"),
            ("Muted", "Surface"), ("Muted", "PageBg"), ("Body", "Hover"), ("Body", "Surface2"),
            ("DangerText", "DangerTint"), ("DangerText", "DangerTint2"), ("RoseText", "DangerTint2"),
            ("WarnText", "WarnTint"), ("OkText", "OkTint"), ("OkText", "OkTint2"),
            ("InfoText", "InfoTint"), ("InfoText", "InfoTint2"), ("TealText", "TealTint"),
            ("TealText", "TealTint2"), ("IndigoText", "IndigoTint"), ("Body2", "PageBg"),
        };
        foreach (var (fg, bg) in pairs)
        {
            yield return new object[] { fg, bg, false };
            yield return new object[] { fg, bg, true };
        }
    }

    [Theory]
    [MemberData(nameof(Pairs))]
    public void TextContrast_MeetsWcagAA(string fg, string bg, bool dark)
    {
        var ratio = ThemePalette.ContrastRatio(ThemePalette.Hex(fg, dark), ThemePalette.Hex(bg, dark));
        Assert.True(ratio >= 4.5, $"{fg} di atas {bg} ({(dark ? "gelap" : "terang")}) hanya {ratio:F2}");
    }

    [Theory]
    [InlineData(false)]
    [InlineData(true)]
    public void WhiteText_OnNavyButtons_MeetsWcagAA(bool dark)
    {
        Assert.True(ThemePalette.ContrastRatio("FFFFFF", ThemePalette.Hex("Navy", dark)) >= 4.5);
        Assert.True(ThemePalette.ContrastRatio("FFFFFF", ThemePalette.Hex("NavyHover", dark)) >= 4.5);
    }

    [Fact]
    public void EveryColor_IsValidSixDigitHex_InBothThemes()
    {
        foreach (var (key, (light, dark)) in ThemePalette.Colors)
        {
            Assert.Matches("^[0-9A-Fa-f]{6}$", light);
            Assert.Matches("^[0-9A-Fa-f]{6}$", dark);
            Assert.False(string.IsNullOrWhiteSpace(key));
        }
    }

    [Fact]
    public void DarkTheme_SurfaceIsDarkerThanText()
    {
        Assert.True(ThemePalette.ContrastRatio(ThemePalette.Hex("Ink", true), ThemePalette.Hex("PageBg", true)) > 10);
    }

    [Fact]
    public void ContrastRatio_BlackOnWhite_Is21() =>
        Assert.Equal(21.0, ThemePalette.ContrastRatio("000000", "FFFFFF"), 1);
}
