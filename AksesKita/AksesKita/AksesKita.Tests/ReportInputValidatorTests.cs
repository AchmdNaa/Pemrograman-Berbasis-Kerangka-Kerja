using AksesKita.Desktop.Rules;

namespace AksesKita.Tests;

public class ReportInputValidatorTests
{
    private const string Judul = "Trotoar rusak di depan kampus";
    private const string Deskripsi = "Trotoar berlubang dan menyulitkan pengguna kursi roda.";
    private const string Alamat = "Jl. Raya ITS, Sukolilo";

    private static IReadOnlyList<string> Cek(
        string? judul = Judul, string? deskripsi = Deskripsi, string? alamat = Alamat,
        bool kategori = true, string? lat = "-7.2819", string? lng = "112.7948")
        => ReportInputValidator.Validate(judul, deskripsi, alamat, kategori, lat, lng);

    [Fact]
    public void Isian_valid_tidak_menghasilkan_kesalahan()
    {
        Assert.Empty(Cek());
    }

    [Fact]
    public void Kategori_wajib_dipilih()
    {
        Assert.Contains(Cek(kategori: false), e => e.Contains("kategori"));
    }

    [Theory]
    [InlineData("")]
    [InlineData("abcd")]
    [InlineData("    ab    ")]
    public void Judul_terlalu_pendek_ditolak(string judul)
    {
        Assert.Contains(Cek(judul: judul), e => e.Contains("Judul"));
    }

    [Fact]
    public void Judul_lebih_dari_100_karakter_ditolak()
    {
        Assert.Contains(Cek(judul: new string('a', 101)), e => e.Contains("Judul"));
    }

    [Fact]
    public void Judul_tepat_5_dan_100_karakter_diterima()
    {
        Assert.Empty(Cek(judul: new string('a', 5)));
        Assert.Empty(Cek(judul: new string('a', 100)));
    }

    [Fact]
    public void Deskripsi_kurang_dari_20_karakter_ditolak()
    {
        Assert.Contains(Cek(deskripsi: "terlalu pendek"), e => e.Contains("Deskripsi"));
    }

    [Fact]
    public void Alamat_kosong_ditolak()
    {
        Assert.Contains(Cek(alamat: "   "), e => e.Contains("Alamat"));
    }

    [Theory]
    [InlineData("-90.1", "0")]
    [InlineData("90.1", "0")]
    [InlineData("0", "-180.1")]
    [InlineData("0", "180.1")]
    [InlineData("abc", "112")]
    [InlineData("-7.2", "")]
    public void Koordinat_tidak_valid_ditolak(string lat, string lng)
    {
        Assert.Contains(Cek(lat: lat, lng: lng), e => e.Contains("Koordinat"));
    }

    [Theory]
    [InlineData("-90", "-180")]
    [InlineData("90", "180")]
    [InlineData("-7.2575", "112.7521")]
    public void Koordinat_batas_dan_desimal_diterima(string lat, string lng)
    {
        Assert.True(ReportInputValidator.TryParseCoordinates(lat, lng, out _, out _));
    }

    [Fact]
    public void Koordinat_dibaca_dengan_titik_desimal()
    {
        ReportInputValidator.TryParseCoordinates("-7.2575", "112.7521", out var lat, out var lng);

        Assert.Equal(-7.2575, lat, 4);
        Assert.Equal(112.7521, lng, 4);
    }

    [Fact]
    public void Beberapa_kesalahan_dikembalikan_berurutan()
    {
        var errors = Cek(kategori: false, judul: "x", alamat: "");

        Assert.True(errors.Count >= 3);
        Assert.Contains("kategori", errors[0]);
    }
}
