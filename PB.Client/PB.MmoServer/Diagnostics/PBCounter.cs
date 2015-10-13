using ExitGames.Diagnostics.Counter;
using ExitGames.Diagnostics.Monitoring;

namespace PB.MmoServer.Diagnostics
{
    public static class PBCounter
    {
        [PublishCounter("Session")]
        public static readonly NumericCounter Session = new NumericCounter("Session");
    }
}
