using System;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop.Rules;

/// <summary>Siapa yang boleh mengubah isi laporan.</summary>
public static class ReportPermissions
{
    /// <summary>
    /// Laporan berstatus akhir (Selesai, Ditolak) tidak dapat diubah.
    /// Admin boleh mengubah laporan lain yang masih berjalan; warga hanya laporan miliknya yang masih berstatus Baru.
    /// </summary>
    public static bool CanEdit(bool isAdmin, string? sessionUser, string? reporterName, ReportStatus status)
    {
        if (StatusTransitionRules.IsFinal(status)) return false;
        if (isAdmin) return true;

        return status == ReportStatus.Baru
            && !string.IsNullOrWhiteSpace(sessionUser)
            && string.Equals(sessionUser.Trim(), reporterName?.Trim(), StringComparison.OrdinalIgnoreCase);
    }
}
