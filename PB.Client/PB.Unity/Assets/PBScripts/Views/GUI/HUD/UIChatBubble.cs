using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using System.Timers;

using PB.Client;
using PB.Game;

public class UIChatBubble : MonoBehaviour
{
    #region MemVars & Props

    public float bubbleHeight = 1;
	public float bubbleTTL = 5000;

    private bool bubbleShow;
    public bool IsShowing { get { return bubbleShow; } }

    protected UIBubbleView.BubbleElement bubbleElement;
    private Dictionary<Timer, GUIContent> timerMap = new Dictionary<Timer, GUIContent>();

    #endregion


    #region MonoBehavior Methods

	void Start()
    {
        bubbleShow = false;
	}

    #endregion


    #region Methods

    private Vector3 CalcOffset()
    {
        Vector3 offset = Vector3.zero;

        if (GetComponent<AvatarController>() != null)
        {
            Transform head = GetComponent<AvatarController>().GetHeadBone();
            if (head != null)
            {
                offset = head.position + Vector3.up * bubbleHeight - transform.position;
            }
            else
            {
                offset = Vector3.up * bubbleHeight;
            }
        }
        else
        {
            offset = Vector3.up * bubbleHeight;
        }

        return offset;
    }

    public void ShowImage(Texture image)
    {
        Show(new GUIContent(image));
    }

    public void ShowMessage(string message)
    {
        Show(new GUIContent(message));
    }

    public void Show(GUIContent content)
    {
        bubbleShow = true;

        Timer timer = new Timer(bubbleTTL);
        timer.Elapsed += new ElapsedEventHandler(BubbleTimer_Elapsed);
        timer.Start();

        Vector3 offset = CalcOffset();

        Messenger<GUIContent, Transform, Vector3>.Broadcast(Messages.BUBBLE_ADD, content, gameObject.transform, offset);

        timerMap.Add(timer, content);
    }

    private void BubbleTimer_Elapsed(object sender, ElapsedEventArgs e)
    {
        bubbleShow = false;

        if (sender is Timer)
        {
            Timer timer = sender as Timer;
            timer.Stop();

            GUIContent content = timerMap[timer];
            Messenger<GUIContent>.Broadcast(Messages.BUBBLE_REMOVE, content);
            
            timerMap.Remove(timer);
        }
    }

    #endregion
}

