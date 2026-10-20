using AksesKita.Desktop.Rules;

namespace AksesKita.Tests;

public class LoginThrottleTests
{
    private DateTime _now = new(2026, 10, 21, 9, 0, 0);

    private LoginThrottle Create() => new(3, TimeSpan.FromSeconds(60), () => _now);

    [Fact]
    public void UnderLimit_NotLocked()
    {
        var t = Create();
        t.RecordFailure("a"); t.RecordFailure("a");
        Assert.False(t.IsLocked("a", out _));
    }

    [Fact]
    public void ReachingLimit_Locks_WithRemainingTime()
    {
        var t = Create();
        for (var i = 0; i < 3; i++) t.RecordFailure("a");
        Assert.True(t.IsLocked("a", out var remaining));
        Assert.Equal(60, remaining.TotalSeconds, 1);
    }

    [Fact]
    public void LockExpires_AfterDuration()
    {
        var t = Create();
        for (var i = 0; i < 3; i++) t.RecordFailure("a");
        _now = _now.AddSeconds(61);
        Assert.False(t.IsLocked("a", out _));
    }

    [Fact]
    public void Success_ResetsCounter()
    {
        var t = Create();
        t.RecordFailure("a"); t.RecordFailure("a");
        t.Reset("a");
        t.RecordFailure("a"); t.RecordFailure("a");
        Assert.False(t.IsLocked("a", out _));
    }

    [Fact]
    public void LockIsPerAccount()
    {
        var t = Create();
        for (var i = 0; i < 3; i++) t.RecordFailure("a");
        Assert.False(t.IsLocked("b", out _));
    }
}
