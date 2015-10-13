// --------------------------------------------------------------------------------------------------------------------
// <copyright file="MyItem.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   The mmo item.
// </summary>
// --------------------------------------------------------------------------------------------------------------------

namespace PB.Client
{
    using System;
    using System.Collections;
    using System.Collections.Generic;
    
    using PB.Common;
    
    using ExitGames.Client.Photon;

    /// <summary>
    /// The mmo item.
    /// </summary>
    public class MyItem : Item
    {
        /// <summary>
        /// Initializes a new instance of the <see cref="MyItem"/> class. 
        /// </summary>
        /// <param name="id">
        /// The item id.
        /// </param>
        /// <param name="type">
        /// The item type.
        /// </param>
        /// <param name="game">
        /// The mmo game.
        /// </param>
        /// <param name="text">
        /// The text property.
        /// </param>
        [CLSCompliant(false)]
        public MyItem(string id, byte type, Game game, string text)
            : base(id, type, game)
        {
            base.SetColor((int)((uint)new System.Random(Guid.NewGuid().GetHashCode()).Next(0, int.MaxValue) | 0xFF000000));
            base.SetText(text);
        }

        /// <summary>
        /// Gets a value indicating whether IsMine.
        /// </summary>
        public override bool IsMine
        {
            get
            {
                return true;
            }
        }

        /// <summary>
        /// Gets or sets a value indicating whether IsMoving.
        /// </summary>
        public bool IsMoving { get; set; }

        /// <summary>
        /// Gets an initial position when player first time enter world.
        /// </summary>
        public float[] InitialPosition { get; internal set; }

        /// <summary>
        /// Gets an inital rotation when player first time enter world
        /// </summary>
        public float[] InitialRotation { get; internal set; }

        /// <summary>
        /// Gets or sets our player token.
        /// </summary>
        public string Token { get; set; }

        /// <summary>
        /// Avatar's Inventories
        /// </summary>
        public Inventories Inventories { get; set; }

        /// <summary>
        /// Avatar's Equipments
        /// </summary>
        public Equipments Equipments { get; set; }

        /// <summary>
        /// Avatar's Quest Journal History
        /// </summary>
        public int[] QuestJournals { get; set; }

        /// <summary>
        /// Avatar's Quest Active Journal History
        /// </summary>
        public int[] QuestActiveJournals { get; set; }

        /// <summary>
        /// Get our player name.
        /// </summary>
        public string Username 
        {
            get
            {
                return base.Name;
            }
            internal set
            {
                base.Name = value;
            }
        }

        /// <summary>
        /// Get our player password
        /// </summary>
        public string Password
        {
            get;
            set;
        }

        /// <summary>
        /// The destroy.
        /// </summary>
        public void Destroy()
        {
            this.IsDestroyed = true;
            Operations.DestroyItem(this.Game, this.Id, this.Type);
        }

        /// <summary>
        /// The enter world.
        /// </summary>
        public void EnterWorld()
        {
            var r = new System.Random();

            List<Entity> spawns = new List<Entity>();

            this.Game.WorldData.Level.Entities.ForEach((e) =>
            {
                if (e.Tag == LevelConstants.TagSpawnPoints)
                {
                    spawns.Add(e);
                }
            });

            float[] position;
            float[] rotation;

            if (spawns.Count > 0)
            {
                Entity spawnPoint = null;

                if (spawns.Count > 1)
                {
                    int index = r.Next(0, spawns.Count - 1);
                    spawnPoint = spawns[index];

                    if (PopBloopSettings.useLogs)
                    {
                        this.Game.Listener.LogDebug(Game, "MyItem.EnterWorld: Randomizing Spawn Point, got index: " + index.ToString());
                    }
                }
                else if(spawns.Count == 1)
                {
                    spawnPoint = spawns[0];
                    if (PopBloopSettings.useLogs)
                    {
                        this.Game.Listener.LogDebug(Game, "MyItem.EnterWorld: Only found 1 spawn point, using it");
                    }
                }

                position = new float[]
                {
                    spawnPoint.Position.x,
                    spawnPoint.Position.z,
                    spawnPoint.Position.y
                };

                rotation = new float[] 
                {
                    spawnPoint.Rotation.x,
                    spawnPoint.Rotation.y,
                    spawnPoint.Rotation.z
                };
                
            }
            else
            {
                position = new float[]
                {
                    0.5f, 0.5f, 0.5f
                };

                rotation = new float[] 
                {
                    0.0f, 0.0f, 0.0f
                };

                this.Game.Listener.LogDebug(Game, string.Format("No Spawn Points found on World '{0}', please fix it by adding Spawn Point(s)!", this.Game.WorldData.Name));
            }

            InitialPosition = position;
            InitialRotation = rotation;
            
            this.SetPositions(position, position, rotation, rotation);           

            float[] levelViewDistance = this.Game.WorldData.Level.InterestArea;
            if (this.ViewDistanceEnter[0] != levelViewDistance[0] || this.ViewDistanceEnter[1] != levelViewDistance[1])
            {
                ViewDistanceEnter = levelViewDistance;

                this.ViewDistanceExit = new[]
                {
                    Math.Max(this.ViewDistanceEnter[0] + levelViewDistance[0], 1.5f * this.ViewDistanceEnter[0]), 
                    Math.Max(this.ViewDistanceEnter[1] + levelViewDistance[1], 1.5f * this.ViewDistanceEnter[1])
                };
            }

            var properties = new Hashtable
                {
                    { PropertyKeyInterestAreaAttached, this.InterestAreaAttached }, 
                    { PropertyKeyViewDistanceEnter, this.ViewDistanceEnter }, 
                    { PropertyKeyViewDistanceExit, this.ViewDistanceExit }, 
                    { PropertyKeyColor, this.Color }, 
                    { PropertyKeyText, this.Text },
                    { PropertyKeyServerAddress, this.Game.Settings.ServerAddress }
                };

            // If we are in private room, set the world name into user's id,
            // If not set the world name into worlddata.name
            string worldName = this.Game.Settings.IsPrivateRoom ? this.Id : this.Game.WorldData.Name;

            this.Game.Listener.LogDebug(Game, "MyItem.EnterWorld: " + worldName);

            this.Game.WorldData.Name = worldName;

            Operations.EnterWorld(this.Game, worldName, this.Id, this.AvatarName, properties, this.Position, this.Rotation, this.ViewDistanceEnter, this.ViewDistanceExit);
        }

        /// <summary>
        /// Load the World
        /// </summary>
        public void LoadWorld()
        {
            LoadWorld(false);
        }

        /// <summary>
        /// Load the World
        /// </summary>
        /// <param name="isPrivateRoom">Is Private Room we are trying to load</param>
        public void LoadWorld(bool isPrivateRoom)
        {
            Operations.LoadWorld(this.Game, this.Game.WorldData.Name, isPrivateRoom);
        }

        /// <summary>
        /// Disconnect our avatar
        /// </summary>
        public void Disconnect()
        {
            this.Game.SetDisconnected(StatusCode.DisconnectByServerLogic);
        }

        /// <summary>
        /// Authenticate the avatar
        /// </summary>
        /// <param name="token"></param>
        public void Authenticate(string token)
        {
            Operations.Authenticate(this.Game, token);
        }

        /// <summary>
        /// Login into Game Server
        /// </summary>
        /// <param name="username">Username</param>
        /// <param name="password">Password</param>
        public void Login(string username, string password)
        {
            Username = username;
            Password = password;
            Operations.Login(this.Game, username, password);
        }

        /// <summary>
        /// The move operation.
        /// </summary>
        /// <param name="newPosition">
        /// The new position.
        /// </param>
        /// <param name="rotation">
        /// The rotation.
        /// </param>
        /// <returns>
        /// The move absolute.
        /// </returns>
        public bool MoveAbsolute(float[] newPosition, float[] rotation)
        {
            if (newPosition[0] < this.Game.WorldData.TopLeftCorner[0])
            {
                return false;
            }

            if (newPosition[0] > this.Game.WorldData.BottomRightCorner[0])
            {
                return false;
            }

            if (newPosition[1] < this.Game.WorldData.TopLeftCorner[1])
            {
                return false;
            }

            if (newPosition[1] > this.Game.WorldData.BottomRightCorner[1])
            {
                return false;
            }

            this.SetPositions(newPosition, this.Position, rotation, this.Rotation);

            Operations.Move(this.Game, this.Id, this.Type, newPosition, rotation, this.Game.Settings.SendReliable);

            return true;
        }

        /// <summary>
        /// The game item move operation.
        /// </summary>
        /// <param name="newPosition">
        /// The new position.
        /// </param>
        /// <param name="rotation">
        /// The rotation.
        /// </param>
        /// <returns>
        /// The move absolute.
        /// </returns>
        public bool GameItemMove(string gameItemId, float[] newPosition, float[] rotation)
        {
            Operations.GameItemMove(this.Game, this.Id, this.Type, gameItemId, newPosition, rotation, this.Game.Settings.SendReliable);

            return true;
        }

        /// <summary>
        /// The game item animate operation.
        /// </summary>
        /// <param name="newPosition">
        /// The new position.
        /// </param>
        /// <param name="rotation">
        /// The rotation.
        /// </param>
        /// <returns>
        /// The move absolute.
        /// </returns>
        public bool GameItemAnimate(string gameItemId, string animation, AnimationAction action, byte wrapMode, float speed)
        {
            Operations.GameItemAnimate(this.Game, this.Id, this.Type, gameItemId, animation, (byte)action, wrapMode, speed, this.Game.Settings.SendReliable);

            return true;
        }


        /// <summary>
        /// The move relative.
        /// </summary>
        /// <param name="offset">
        /// The offset.
        /// </param>
        /// <param name="rotation">
        /// The rotation.
        /// </param>
        /// <returns>
        /// true if moved.
        /// </returns>
        public bool MoveRelative(float[] offset, float[] rotation)
        {
            return this.MoveAbsolute(new[] { this.Position[0] + offset[0], this.Position[1] + offset[1] }, rotation);
        }

        /// <summary>
        /// Animate this MyItem
        /// </summary>
        /// <param name="animation">Animation name</param>
        /// <param name="wrapMode">Animation wrap mode</param>
        /// <returns>false if animation is still the same, true otherwise</returns>
        [CLSCompliant(false)]
        public void Animate(string animation, AnimationAction action, byte wrapMode, float speed, int layer)
        {
            if (this.Animation != animation)
            {
                base.SetAnimation(animation, action, wrapMode, speed);

                Hashtable data = new Hashtable()
                {
                    { PropertyKeyAnimation, animation },
                    { PropertyKeyAnimationWrap, wrapMode },
                    { PropertyKeyAnimationSpeed, speed },
                    { PropertyKeyAnimationLayer, layer },
                    { PropertyKeyAnimationAction, (byte)action }
                };

                Operations.SetProperties(this.Game, this.Id, this.Type, data, null, true);
                //Operations.Animate(this.Game, this.Id, this.Type, animation, (byte)action, (byte)wrapMode, speed, layer, true);
            }

        }

        /// <summary>
        /// The set color.
        /// </summary>
        /// <param name="color">
        /// The color.
        /// </param>
        public override void SetColor(int color)
        {
            if (color != this.Color)
            {
                base.SetColor(color);
                Operations.SetProperties(this.Game, this.Id, this.Type, new Hashtable { { PropertyKeyColor, color } }, null, true);
            }
        }

        /// <summary>
        /// The set interest area attached item.
        /// </summary>
        /// <param name="attached">
        /// The attached.
        /// </param>
        public override void SetInterestAreaAttached(bool attached)
        {
            if (attached != this.InterestAreaAttached)
            {
                base.SetInterestAreaAttached(attached);
                Operations.SetProperties(this.Game, this.Id, this.Type, new Hashtable { { PropertyKeyInterestAreaAttached, attached } }, null, true);
            }
        }

        /// <summary>
        /// The set interest area view distance.
        /// </summary>
        /// <param name="viewDistanceEnter">
        /// The view distance enter.
        /// </param>
        /// <param name="viewDistanceExit">
        /// The view distance exit.
        /// </param>
        public override void SetInterestAreaViewDistance(float[] viewDistanceEnter, float[] viewDistanceExit)
        {
            base.SetInterestAreaViewDistance(viewDistanceEnter, viewDistanceExit);
            Operations.SetProperties(
                this.Game, 
                this.Id, 
                this.Type, 
                new Hashtable { { PropertyKeyViewDistanceEnter, viewDistanceEnter }, { PropertyKeyViewDistanceExit, viewDistanceExit } }, 
                null, 
                true);
        }

        /// <summary>
        /// The set view distance.
        /// </summary>
        /// <param name="camera">
        /// The camera.
        /// </param>
        public void SetInterestAreaViewDistance(InterestArea camera)
        {
            this.SetInterestAreaViewDistance(camera.ViewDistanceEnter, camera.ViewDistanceExit);
        }

        /// <summary>
        /// The set text.
        /// </summary>
        /// <param name="text">
        /// The new text.
        /// </param>
        public override void SetText(string text)
        {
            if (text != this.Text)
            {
                base.SetText(text);
                Operations.SetProperties(this.Game, this.Id, this.Type, new Hashtable { { PropertyKeyText, text } }, null, true);
            }
        }

        public void SetInventoryAdd(string code, string name, float weight, string description)
        {
            Operations.SetInventoryAdd(this.Game, code, name, weight, description);
        }

        public void SetInventoryRemove(string code, string id, string name, float weight, string description)
        {
            Operations.SetInventoryRemove(this.Game, id, code, name, weight, description);
        }

        public void UpdateEquipment(string code, int count)
        {
            Operations.UpdateEquipment(this.Game, code, count);
        }

        /// <summary>
        /// Add quest id to the quest journal on the game server
        /// </summary>
        /// <param name="questid">The quest id</param>
        public void SetQuestJournal(int questid, bool isActive)
        {
            Operations.SetQuestJournal(this.Game, questid, isActive);
        }

        /// <summary>
        /// The spawn.
        /// </summary>
        /// <param name="position">
        /// The item position.
        /// </param>
        /// <param name="rotation">
        /// The rotation.
        /// </param>
        /// <param name="color">
        /// The item color.
        /// </param>
        /// <param name="subscribe">
        /// The subscribe.
        /// </param>
        public void Spawn(float[] position, float[] rotation, int color, bool subscribe)
        {
            this.SetPositions(position, position, rotation, rotation);
            base.SetInterestAreaViewDistance(new[] { 0f, 0f }, new[] { 0f, 0f });
            base.SetColor(color);
            var properties = new Hashtable
                {
                    { PropertyKeyInterestAreaAttached, false }, 
                    { PropertyKeyViewDistanceEnter, this.ViewDistanceEnter }, 
                    { PropertyKeyViewDistanceExit, this.ViewDistanceExit }, 
                    { PropertyKeyColor, this.Color }, 
                    { PropertyKeyText, this.Text }
                };
            Operations.SpawnItem(this.Game, this.Id, this.Type, position, rotation, properties, subscribe);
        }
    }
}