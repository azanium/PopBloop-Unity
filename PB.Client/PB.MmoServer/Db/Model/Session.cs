
namespace PB.Server.Db.Model
{
    using MongoDB.Bson;
    using System;

    [CLSCompliant(false)]
    public class Session
    {
        public string Id { get; set; }
        public DateTime TimeStart { get; set; }
        public DateTime TimeEnd { get; set; }
        public ObjectId UserId { get; set; }
        public string Username { get; set; }
        public string Avatarname { get; set; }
    }
}
