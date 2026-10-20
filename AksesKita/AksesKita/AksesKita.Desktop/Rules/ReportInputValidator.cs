using System.Collections.Generic;
using System.Globalization;

namespace AksesKita.Desktop.Rules;

/// <summary>Aturan validasi isian formulir laporan (sesuai bagian Aturan Validasi di README).</summary>
public static class ReportInputValidator
{
    public const int TitleMin = 5;
    public const int TitleMax = 100;
    public const int DescriptionMin = 20;

    /// <summary>Koordinat memakai titik desimal, tidak tergantung bahasa Windows.</summary>
    public static bool TryParseCoordinates(string? latText, string? lngText, out double lat, out double lng)
    {
        lat = 0;
        lng = 0;
        return double.TryParse(latText, NumberStyles.Float, CultureInfo.InvariantCulture, out lat) && lat >= -90 && lat <= 90
            && double.TryParse(lngText, NumberStyles.Float, CultureInfo.InvariantCulture, out lng) && lng >= -180 && lng <= 180;
    }

    /// <summary>Daftar pesan kesalahan (kosong bila semua isian valid), berurutan sesuai letak di formulir.</summary>
    public static IReadOnlyList<string> Validate(
        string? title, string? description, string? address, bool hasCategory, string? latText, string? lngText)
    {
        var errors = new List<string>();

        if (!hasCategory)
            errors.Add("Pilih kategori fasilitas yang terhambat.");

        var t = title?.Trim() ?? string.Empty;
        if (t.Length < TitleMin || t.Length > TitleMax)
            errors.Add($"Judul hambatan wajib diisi, {TitleMin}–{TitleMax} karakter.");

        var d = description?.Trim() ?? string.Empty;
        if (d.Length < DescriptionMin)
            errors.Add($"Deskripsi minimal {DescriptionMin} karakter agar petugas dapat memahami kondisinya.");

        if (string.IsNullOrWhiteSpace(address))
            errors.Add("Alamat atau patokan lokasi wajib diisi.");

        if (!TryParseCoordinates(latText, lngText, out _, out _))
            errors.Add("Koordinat tidak valid. Gunakan format desimal, contoh -7.2575 dan 112.7521.");

        return errors;
    }
}
