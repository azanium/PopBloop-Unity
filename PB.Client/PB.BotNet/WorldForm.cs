using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

using PB.Client;
using PB.Common;

using ExitGames.Client.Photon;
using ExitGames.Concurrency.Core;
using ExitGames.Concurrency.Fibers;
using ExitGames.Logging;

using ZedGraph;

using PhotonPeer = PB.Client.PhotonPeer;

namespace PB.BotNet
{
    public partial class WorldForm : Form, IPhotonPeerListener
    {
        #region MemVars & Props

        private static readonly ILogger log = LogManager.GetCurrentClassLogger();

        private readonly PhotonPeer diagnosticsPeer;

        private readonly FormFiber fiber;

        private int gameCounter;

        private DateTime? startTime;

        private IDisposable updateTimer;

        #endregion


        #region Ctor

        public WorldForm()
        {
            this.gameCounter = 0;
            AppDomain.CurrentDomain.UnhandledException += new UnhandledExceptionEventHandler(CurrentDomain_UnhandledException);
            Application.ThreadException += new System.Threading.ThreadExceptionEventHandler(Application_ThreadException);

            InitializeComponent();

            this.MouseWheel += this.OnMouseWheel;
        }

        private void Application_ThreadException(object sender, System.Threading.ThreadExceptionEventArgs e)
        {
            
        }

        private void CurrentDomain_UnhandledException(object sender, UnhandledExceptionEventArgs e)
        {
            
        }

        #endregion


        #region BotNet Methods

        /// <summary>
        /// The on mouse wheel.
        /// </summary>
        /// <param name="sender">
        /// The sender.
        /// </param>
        /// <param name="e">
        /// The event args.
        /// </param>
        private void OnMouseWheel(object sender, MouseEventArgs e)
        {
            var page = this.tabControlTabs.SelectedTab as GameTabPage;
            if (page == null)
            {
                return;
            }

            Game engine = page.Game;
            if (engine.State == GameState.WorldEntered)
            {
                var offset = new[] { engine.WorldData.TileDimensions[0] / 2, engine.WorldData.TileDimensions[1] / 2 };
                int factor = e.Delta > 0 ? 1 : -1;
                float[] currentViewDistance = page.MainCamera.ViewDistanceEnter;
                var newViewDistance = new[] { currentViewDistance[0] + (offset[0] * factor), currentViewDistance[1] + (offset[1] * factor) };
                page.MainCamera.SetViewDistance(newViewDistance);
            }
        }

        #endregion


        #region IPhotonPeerListener Members

        public void DebugReturn(DebugLevel level, string message)
        {
            
        }

        public void OnEvent(EventData eventData)
        {
            
        }

        public void OnOperationResponse(OperationResponse operationResponse)
        {
            
        }

        public void OnStatusChanged(StatusCode statusCode)
        {
            
        }

        #endregion
    }
}
