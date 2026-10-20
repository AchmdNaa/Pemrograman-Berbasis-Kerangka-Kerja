using System.Linq;
using System.Text.Json;
using System.Threading.Tasks;
using CommunityToolkit.Mvvm.ComponentModel;
using AksesKita.Desktop.Services;

namespace AksesKita.Desktop.ViewModels;

public partial class MapViewModel : ObservableObject
{
    private readonly IReportService _service;

    [ObservableProperty]
    private string _filterCategory = "Semua";

    public MapViewModel(IReportService service)
    {
        _service = service;
    }

    // Data penanda untuk peta: dikirim ke JavaScript (fungsi setMarkers di index.html)
    public async Task<string> GetMarkersJsonAsync()
    {
        var reports = await _service.GetAllReportsAsync();
        var markers = reports.Select(r => new
        {
            id = r.Id,
            code = r.TicketCode,
            title = r.Title,
            category = r.Category?.Name ?? "",
            status = r.Status.ToString(),
            severity = r.Severity.ToString(),
            latitude = r.Latitude,
            longitude = r.Longitude
        });
        return JsonSerializer.Serialize(markers);
    }
}
