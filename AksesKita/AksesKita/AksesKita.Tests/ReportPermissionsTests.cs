using AksesKita.Desktop.Models;
using AksesKita.Desktop.Rules;

namespace AksesKita.Tests;

public class ReportPermissionsTests
{
    [Fact]
    public void Pemilik_boleh_mengubah_laporan_berstatus_Baru()
    {
        Assert.True(ReportPermissions.CanEdit(false, "Eko", "Eko", ReportStatus.Baru));
    }

    [Fact]
    public void Nama_dibandingkan_tanpa_peduli_huruf_besar_dan_spasi()
    {
        Assert.True(ReportPermissions.CanEdit(false, " eko ", "EKO", ReportStatus.Baru));
    }

    [Fact]
    public void Warga_tidak_boleh_mengubah_laporan_orang_lain()
    {
        Assert.False(ReportPermissions.CanEdit(false, "Eko", "Rani", ReportStatus.Baru));
    }

    [Fact]
    public void Warga_tidak_boleh_mengubah_laporan_yang_sudah_diproses()
    {
        Assert.False(ReportPermissions.CanEdit(false, "Eko", "Eko", ReportStatus.Terverifikasi));
        Assert.False(ReportPermissions.CanEdit(false, "Eko", "Eko", ReportStatus.DalamPerbaikan));
    }

    [Theory]
    [InlineData(null)]
    [InlineData("")]
    public void Tanpa_nama_pengguna_tidak_boleh_mengubah(string? nama)
    {
        Assert.False(ReportPermissions.CanEdit(false, nama, nama, ReportStatus.Baru));
    }

    [Theory]
    [InlineData(ReportStatus.Baru)]
    [InlineData(ReportStatus.Terverifikasi)]
    [InlineData(ReportStatus.DalamPerbaikan)]
    public void Admin_boleh_mengubah_laporan_yang_masih_berjalan(ReportStatus status)
    {
        Assert.True(ReportPermissions.CanEdit(true, "Admin", "Rani", status));
    }

    [Theory]
    [InlineData(ReportStatus.Selesai)]
    [InlineData(ReportStatus.Ditolak)]
    public void Status_akhir_tidak_dapat_diubah_siapa_pun(ReportStatus status)
    {
        Assert.False(ReportPermissions.CanEdit(true, "Admin", "Rani", status));
        Assert.False(ReportPermissions.CanEdit(false, "Rani", "Rani", status));
    }
}
