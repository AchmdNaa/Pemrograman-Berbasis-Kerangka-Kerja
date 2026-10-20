using System;
using System.Text.RegularExpressions;
using AksesKita.Desktop.Models;

namespace AksesKita.Desktop;

public static class Formatters
{
    /// <summary>"DalamPerbaikan" → "Dalam Perbaikan".</summary>
    public static string Label(object? value)
        => value == null ? string.Empty : Regex.Replace(value.ToString() ?? string.Empty, "(?<=[a-z])(?=[A-Z])", " ");

    public static string Ago(DateTime at)
    {
        var span = DateTime.Now - at;
        if (span.TotalMinutes < 1) return "baru saja";
        if (span.TotalMinutes < 60) return $"{(int)span.TotalMinutes} mnt lalu";
        if (span.TotalHours < 24) return $"{(int)span.TotalHours} jam lalu";
        if (span.TotalDays < 30) return $"{(int)span.TotalDays} hari lalu";
        return at.ToString("dd MMM yyyy");
    }

    /// <summary>Service-level target by severity, shown on the detail page.</summary>
    public static string SlaTarget(SeverityLevel level) => level switch
    {
        SeverityLevel.Kritis => "24 Jam",
        SeverityLevel.Sedang => "3 Hari",
        _ => "7 Hari"
    };

    /// <summary>Warna titik pada garis waktu; nada tengah supaya terlihat di tema terang maupun gelap.</summary>
    public static string StatusColor(ReportStatus s) => s switch
    {
        ReportStatus.Baru => "#64748B",
        ReportStatus.Terverifikasi => "#0284C7",
        ReportStatus.DalamPerbaikan => "#0891B2",
        ReportStatus.Selesai => "#16A34A",
        _ => "#E11D48"
    };
}
