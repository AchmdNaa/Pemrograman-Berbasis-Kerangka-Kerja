namespace AksesKita.Desktop.Services;

/// <summary>Ringkasan angka untuk kartu statistik dashboard (dihitung dari database).</summary>
public record DashboardSummary(int Total, int InProgress, int Resolved, int Urgent);
