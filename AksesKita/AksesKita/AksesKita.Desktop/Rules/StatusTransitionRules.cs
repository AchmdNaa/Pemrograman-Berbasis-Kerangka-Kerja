using System.Collections.Generic;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Rules;

/// <summary>
/// Alur status yang diizinkan:
/// Baru → Terverifikasi | Ditolak, Terverifikasi → Dalam Perbaikan | Ditolak, Dalam Perbaikan → Selesai.
/// Selesai dan Ditolak adalah status akhir (hanya boleh menambah catatan).
/// Kelas ini sengaja bebas dari WPF supaya mudah diuji.
/// </summary>
public static class StatusTransitionRules
{
    private static readonly Dictionary<ReportStatus, ReportStatus[]> Allowed = new()
    {
        [ReportStatus.Baru] = new[] { ReportStatus.Terverifikasi, ReportStatus.Ditolak },
        [ReportStatus.Terverifikasi] = new[] { ReportStatus.DalamPerbaikan, ReportStatus.Ditolak },
        [ReportStatus.DalamPerbaikan] = new[] { ReportStatus.Selesai },
        [ReportStatus.Selesai] = new ReportStatus[0],
        [ReportStatus.Ditolak] = new ReportStatus[0]
    };

    /// <summary>Status yang boleh dipilih berikutnya (tanpa status saat ini).</summary>
    public static IReadOnlyList<ReportStatus> AllowedNext(ReportStatus from) => Allowed[from];

    public static bool CanTransition(ReportStatus from, ReportStatus to) =>
        System.Array.IndexOf(Allowed[from], to) >= 0;

    public static bool IsFinal(ReportStatus status) => Allowed[status].Length == 0;

    /// <summary>Isi pilihan status di layar: status saat ini diikuti status yang boleh dituju.</summary>
    public static IReadOnlyList<ReportStatus> SelectableFrom(ReportStatus current)
    {
        var list = new List<ReportStatus> { current };
        list.AddRange(Allowed[current]);
        return list;
    }

    public static string Label(ReportStatus status) => status switch
    {
        ReportStatus.DalamPerbaikan => "Dalam Perbaikan",
        _ => status.ToString()
    };

    /// <summary>
    /// Mengembalikan pesan kesalahan, atau null bila perubahan boleh dilakukan.
    /// Status yang sama hanya diterima bila ada catatan (menambah catatan tanpa mengubah status).
    /// </summary>
    public static string? Validate(ReportStatus from, ReportStatus to, string? note)
    {
        var hasNote = !string.IsNullOrWhiteSpace(note);

        if (from == to)
            return hasNote ? null : "Ubah status atau isi catatan terlebih dahulu.";

        if (!CanTransition(from, to))
            return $"Status tidak dapat diubah dari {Label(from)} ke {Label(to)}.";

        if (to == ReportStatus.Ditolak && !hasNote)
            return "Catatan wajib diisi bila laporan ditolak.";

        return null;
    }
}
