using System;
using System.Collections.Generic;
using System.Globalization;

namespace AksesKita.Desktop.Rules;

/// <summary>
/// Daftar warna tema terang dan gelap (kode heksadesimal tanpa tanda #).
/// Bebas dari WPF supaya pasangan warna bisa diuji kontrasnya tanpa membuka aplikasi.
/// </summary>
public static class ThemePalette
{
    public static readonly IReadOnlyDictionary<string, (string Light, string Dark)> Colors =
        new Dictionary<string, (string Light, string Dark)>
    {
        ["Surface"] = ("FFFFFF", "1E293B"),
        ["PageBg"] = ("F1F5F9", "0F172A"),
        ["Surface2"] = ("F8FAFC", "162033"),
        ["Hover"] = ("EEF2F7", "2A3A52"),
        ["Line"] = ("E2E8F0", "334155"),
        ["InputBorder"] = ("CBD5E1", "475569"),
        ["BorderStrong"] = ("94A3B8", "64748B"),
        ["Muted"] = ("5B6A7F", "A3B2C7"),
        ["Body2"] = ("475569", "CBD5E1"),
        ["Body"] = ("334155", "E2E8F0"),
        ["Ink"] = ("0F172A", "F8FAFC"),
        ["Navy"] = ("0F2A43", "2563A8"),
        ["NavyHover"] = ("17395C", "3374BE"),
        ["Backdrop"] = ("0F2A43", "0B1220"),
        ["Teal"] = ("0E7490", "22B8D8"),
        ["InfoTint"] = ("E0F2FE", "0C3A57"),
        ["InfoTint2"] = ("F0F9FF", "0B2F46"),
        ["InfoBorder"] = ("BAE6FD", "075985"),
        ["InfoText"] = ("0369A1", "7DD3FC"),
        ["DangerTint"] = ("FEF2F2", "3B1618"),
        ["DangerTint2"] = ("FFE4E6", "4C1620"),
        ["DangerBorder"] = ("FCA5A5", "7F1D1D"),
        ["DangerText"] = ("B91C1C", "FCA5A5"),
        ["RoseText"] = ("9F1239", "FDA4AF"),
        ["Danger"] = ("DC2626", "F87171"),
        ["WarnTint"] = ("FFFBEB", "3A2A0B"),
        ["WarnText"] = ("B45309", "FBBF24"),
        ["WarnBorder"] = ("FCD34D", "92400E"),
        ["Warn"] = ("D97706", "FBBF24"),
        ["OkTint"] = ("F0FDF4", "0F2E1B"),
        ["OkTint2"] = ("DCFCE7", "14532D"),
        ["OkText"] = ("15803D", "86EFAC"),
        ["OkBorder"] = ("86EFAC", "166534"),
        ["Ok"] = ("16A34A", "4ADE80"),
        ["TealTint"] = ("CFFAFE", "0E3A45"),
        ["TealTint2"] = ("F0FDFA", "0B2F2C"),
        ["TealBorder"] = ("99F6E4", "115E59"),
        ["TealText"] = ("0E7490", "67E8F9"),
        ["IndigoTint"] = ("EEF2FF", "1E1B4B"),
        ["IndigoBorder"] = ("C7D2FE", "3730A3"),
        ["IndigoText"] = ("3730A3", "A5B4FC"),
    };

    public static string Hex(string key, bool dark) => dark ? Colors[key].Dark : Colors[key].Light;

    /// <summary>Rasio kontras WCAG antara dua warna heksadesimal (1 sampai 21). Teks biasa butuh minimal 4,5.</summary>
    public static double ContrastRatio(string hexA, string hexB)
    {
        var l1 = Luminance(hexA);
        var l2 = Luminance(hexB);
        var (hi, lo) = l1 >= l2 ? (l1, l2) : (l2, l1);
        return (hi + 0.05) / (lo + 0.05);
    }

    private static double Luminance(string hex)
    {
        double Channel(int start)
        {
            var c = int.Parse(hex.Substring(start, 2), NumberStyles.HexNumber, CultureInfo.InvariantCulture) / 255.0;
            return c <= 0.03928 ? c / 12.92 : Math.Pow((c + 0.055) / 1.055, 2.4);
        }
        return 0.2126 * Channel(0) + 0.7152 * Channel(2) + 0.0722 * Channel(4);
    }
}
