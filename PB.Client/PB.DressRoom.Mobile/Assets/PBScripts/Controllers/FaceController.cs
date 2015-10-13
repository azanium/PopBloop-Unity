using UnityEngine;
using System.Collections;

public class FaceController : UIViewController 
{
    public GameObject headingInfo;
    public GameObject detailInfo;
    public GameObject hideFaceConnector;

    private static FaceController faceController;

    public override void Awake()
    {
        faceController = this;
    }
    
    public override void Update()
    {
        if (Input.GetKeyDown(KeyCode.Escape))
        {
            if (hideFaceConnector != null)
            {
                hideFaceConnector.SendMessage("OnClick", SendMessageOptions.DontRequireReceiver);
            }
        }
    }

    public static void ChangeFaceHeading(string headingText)
    {
        if (faceController != null)
        {
            if (faceController.detailInfo != null)
            {
                UILabel heading = faceController.headingInfo.GetComponent<UILabel>();
                if (heading != null)
                {
                    heading.text = headingText;
                }
            }
        }
    }


    public static void ChangeFaceTitle(string headingText, string detailText)
    {
        if (faceController != null)
        {   
            if (faceController.detailInfo != null)
            {
                UILabel detail = faceController.detailInfo.GetComponent<UILabel>();
                if (detail != null)
                {
                    detail.text = detailText;
                }
            } 
        }
    }
}
