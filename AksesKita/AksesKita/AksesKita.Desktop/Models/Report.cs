using System;
using System.Collections.Generic;

namespace AksesKita.Desktop.Models;

public class Report
{
    public int Id { get; set; }
    public string TicketCode { get; set; } = string.Empty;
    public string Title { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;

    public int CategoryId { get; set; }
    public Category? Category { get; set; }

    public SeverityLevel Severity { get; set; } = SeverityLevel.Rendah;
    public ReportStatus Status { get; set; } = ReportStatus.Baru;

    public string Address { get; set; } = string.Empty;
    public double Latitude { get; set; }
    public double Longitude { get; set; }

    public string? ReporterName { get; set; }
    public bool IsAnonymous { get; set; }
    public int SupportCount { get; set; } = 0;

    /// <summary>Path of the evidence photo copied into the app's Uploads folder (optional).</summary>
    public string? ImagePath { get; set; }

    public List<string> ImageUrls { get; set; } = new();
    public DateTime CreatedAt { get; set; } = DateTime.Now;

    public List<ReportStatusLog> StatusLogs { get; set; } = new();
}
