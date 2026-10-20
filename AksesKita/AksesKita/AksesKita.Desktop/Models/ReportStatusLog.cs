using System;

namespace AksesKita.Desktop.Models;

public class ReportStatusLog
{
    public int Id { get; set; }
    public int ReportId { get; set; }
    public ReportStatus OldStatus { get; set; }
    public ReportStatus NewStatus { get; set; }
    public string Note { get; set; } = string.Empty;
    public string UpdatedBy { get; set; } = string.Empty;
    public DateTime CreatedAt { get; set; } = DateTime.Now;
}
