using AksesKita.Desktop.Models;
using AksesKita.Desktop.Rules;

namespace AksesKita.Tests;

public class StatusTransitionRulesTests
{
    [Theory]
    [InlineData(ReportStatus.Baru, ReportStatus.Terverifikasi)]
    [InlineData(ReportStatus.Baru, ReportStatus.Ditolak)]
    [InlineData(ReportStatus.Terverifikasi, ReportStatus.DalamPerbaikan)]
    [InlineData(ReportStatus.Terverifikasi, ReportStatus.Ditolak)]
    [InlineData(ReportStatus.DalamPerbaikan, ReportStatus.Selesai)]
    public void Perubahan_yang_diizinkan_lolos(ReportStatus dari, ReportStatus ke)
    {
        Assert.True(StatusTransitionRules.CanTransition(dari, ke));
    }

    [Theory]
    [InlineData(ReportStatus.Baru, ReportStatus.Selesai)]
    [InlineData(ReportStatus.Baru, ReportStatus.DalamPerbaikan)]
    [InlineData(ReportStatus.Terverifikasi, ReportStatus.Selesai)]
    [InlineData(ReportStatus.DalamPerbaikan, ReportStatus.Ditolak)]
    [InlineData(ReportStatus.Selesai, ReportStatus.Baru)]
    [InlineData(ReportStatus.Ditolak, ReportStatus.Terverifikasi)]
    public void Perubahan_lain_ditolak(ReportStatus dari, ReportStatus ke)
    {
        Assert.False(StatusTransitionRules.CanTransition(dari, ke));
        Assert.NotNull(StatusTransitionRules.Validate(dari, ke, "catatan"));
    }

    [Theory]
    [InlineData(ReportStatus.Selesai)]
    [InlineData(ReportStatus.Ditolak)]
    public void Status_akhir_tidak_punya_status_berikutnya(ReportStatus status)
    {
        Assert.True(StatusTransitionRules.IsFinal(status));
        Assert.Empty(StatusTransitionRules.AllowedNext(status));
    }

    [Fact]
    public void Pilihan_status_dimulai_dari_status_saat_ini()
    {
        var pilihan = StatusTransitionRules.SelectableFrom(ReportStatus.Terverifikasi);

        Assert.Equal(
            new[] { ReportStatus.Terverifikasi, ReportStatus.DalamPerbaikan, ReportStatus.Ditolak },
            pilihan);
    }

    [Theory]
    [InlineData(null)]
    [InlineData("")]
    [InlineData("   ")]
    public void Menolak_laporan_wajib_pakai_catatan(string? catatan)
    {
        var pesan = StatusTransitionRules.Validate(ReportStatus.Baru, ReportStatus.Ditolak, catatan);

        Assert.NotNull(pesan);
        Assert.Contains("Catatan", pesan);
    }

    [Fact]
    public void Menolak_laporan_dengan_catatan_diterima()
    {
        Assert.Null(StatusTransitionRules.Validate(ReportStatus.Baru, ReportStatus.Ditolak, "Foto tidak sesuai lokasi"));
    }

    [Fact]
    public void Status_sama_tanpa_catatan_ditolak_tapi_dengan_catatan_diterima()
    {
        Assert.NotNull(StatusTransitionRules.Validate(ReportStatus.Terverifikasi, ReportStatus.Terverifikasi, ""));
        Assert.Null(StatusTransitionRules.Validate(ReportStatus.Terverifikasi, ReportStatus.Terverifikasi, "Menunggu tim teknis"));
    }

    [Fact]
    public void Perubahan_valid_tanpa_catatan_diterima()
    {
        Assert.Null(StatusTransitionRules.Validate(ReportStatus.Baru, ReportStatus.Terverifikasi, ""));
    }
}
